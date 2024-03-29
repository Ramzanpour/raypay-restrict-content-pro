<?php
/**
 * Required functions.
 *
 * @package RCP_RayPay
 * @since 1.0
 */

/**
 * Call the gateway endpoints.
 *
 * Try to get response from the gateway for 4 times.
 *
 * @param string $url
 * @param array $args
 * @return array|WP_Error
 */
function rcp_raypay_call_gateway_endpoint( $url, $args ) {
	$tries = 2;

	while ( $tries ) {
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			$tries--;
			continue;
		} else {
			break;
		}
	}

	return $response;
}

function ryapay_rcp_send_data_shaparak($access_token , $terminal_id){
    echo '<p style="color:#ff0000; font:18px Tahoma; direction:rtl;">در حال اتصال به درگاه بانکی. لطفا صبر کنید ...</p>';
    echo '<form name="frmRayPayPayment" method="post" action=" https://mabna.shaparak.ir:8080/Pay ">';
    echo '<input type="hidden" name="TerminalID" value="' . $terminal_id . '" />';
    echo '<input type="hidden" name="token" value="' . $access_token . '" />';
    echo '<input class="submit" type="submit" value="پرداخت" /></form>';
    echo '<script>document.frmRayPayPayment.submit();</script>';
}

/**
 * Check the payment ID in the system.
 *
 * @param string $id
 * @return void
 */
function rcp_raypay_check_verification( $id ) {

	global $wpdb;

	if ( ! function_exists( 'rcp_get_payment_meta_db_name' ) ) {
		return;
	}

	$table = rcp_get_payment_meta_db_name();

	$check = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE meta_key='_verification_params' AND meta_value='raypay-%s'",
			$id
		)
	);

	if ( ! empty( $check ) ) {
		wp_die( __( 'Duplicate payment record', 'raypay-for-rcp' ) );
	}
}

/**
 * Set the payment ID for later verifications.
 *
 * @param int $payment_id
 * @param string $param
 * @return void
 */
function rcp_raypay_set_verification( $payment_id, $params ) {
	global $wpdb;

	if ( ! function_exists( 'rcp_get_payment_meta_db_name' ) ) {
		return;
	}

	$table = rcp_get_payment_meta_db_name();

	$wpdb->insert(
		$table,
		array(
			'payment_id'	=> $payment_id,
			'meta_key'		=> '_verification_params',
			'meta_value'	=> $params,
		), 
		array('%d', '%s', '%s')
	);
}