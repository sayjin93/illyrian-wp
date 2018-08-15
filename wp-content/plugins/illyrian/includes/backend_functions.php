<?php
/* License */
set_site_transient( 'update_plugins', null );
// this is the URL our updater / license checker pings. This should be the URL of the site with WPLS installed
define( 'WPLS_SAMPLE_STORE_URL', 'http://jkruja.com/' ); // you should use your own CONSTANT server url, and be sure to replace it throughout this file

// the name of your product. This should match the download name in WPLS exactly
define( 'WPLS_SAMPLE_ITEM_CODE', 'illyrianWP' ); // you should use your own CONSTANT CODE, and be sure to replace it throughout this file

function wpls_sample_register_option() {
	// creates our settings in the options table
	register_setting( 'wpls_sample_license', 'wpls_sample_license_key', 'wpls_sanitize_license' );
}

function wpls_sanitize_license( $new ) {
	$old = get_option( 'wpls_sample_license_key' );
	if ( $old && $old != $new ) {
		delete_option( 'wpls_sample_license_status' ); // new license has been entered, so must reactivate
	}

	return $new;
}

/************************************
 * this illustrates how to activate
 * a license key
 *************************************/
function wpls_sample_activate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['wpls_license_activate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'wpls_sample_nonce', 'wpls_sample_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'wpls_sample_license_key' );

		$license_data = wpls_sampleRequest( WPLS_SAMPLE_STORE_URL, $license, WPLS_SAMPLE_ITEM_CODE );

		update_option( 'wpls_sample_license_status', $license_data->valid );

	}
}

function wpls_sample_deactivate_license() {
	$license   = wpls_sample_get_license_data();
	$time      = strtotime( $license->info->expire );
	$newformat = date( 'Y-m-d', $time );
	$date      = date( 'Y-m-d' );

	if ( $date > $newformat ) {
		delete_option( 'wpls_sample_license_key' );
	}
}

function wpls_sample_get_license_data() {
	$license      = get_option( 'wpls_sample_license_key' );
	$license_data = wpls_sampleRequest( WPLS_SAMPLE_STORE_URL, $license, WPLS_SAMPLE_ITEM_CODE );

	return $license_data;
}

function wpls_sampleRequest( $server, $license, $product ) {

	$domain = $_SERVER['SERVER_NAME'];
	if ( substr( $domain, 0, 4 ) == "www." ) {
		$domain = substr( $domain, 4 );
	}

	$data = array(
		'wpls-verify' => $license,
		'product'     => urlencode( $product ), // the CODE of our product in WPLS
		'domain'      => $domain,
		'validip'     => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'],
		'timeout'     => 10,
		'sslverify'   => false,
		'httpversion' => '1.0',
	);

	$licenseServer = $server;
	$ch            = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $licenseServer );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 600 );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 600 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$json_data = curl_exec( $ch );
	curl_close( $ch );

	$result = json_decode( $json_data );

	return $result;
}

function wpls_checkEventScheduled() {
	$schedules = wp_get_schedules();

	if ( ! isset( $schedules['wpls_sample_deactivate_license'] ) ) {
		return false;
	}

	wp_schedule_event( time(), 'daily', 'wpls_sample_deactivate_license', '' );

	return true;
}

/*  Edit Wordress default page footer   */
function change_footer_text( $text ) {
	$url  = 'https://jkruja.com';
	$text = sprintf( esc_html__( 'Developed by %sJurgen Kruja%s.' ), '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">', '</a>' );

	return $text;
}

function change_footer_version() {
	return 'Version 1.0';
}

/* Action Hooks */
add_action( 'admin_init', 'wpls_sample_register_option' );
add_action( 'admin_init', 'wpls_sample_activate_license' );
add_action( 'admin_init', 'wpls_sample_deactivate_license' );
add_action( 'wp_loaded', 'wpls_checkEventScheduled' );

/* Filter Hooks */
add_filter( 'admin_footer_text', 'change_footer_text', 1, 2 );
add_filter( 'update_footer', 'change_footer_version', 9999 );
