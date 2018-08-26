<?php

/*  Licensing Functions  */
set_site_transient( 'update_plugins', null );

// This is the URL our updater / license checker pings. This should be the URL of the site with WPLS installed
define( 'WPLS_SAMPLE_STORE_URL', 'https://jkruja.com' ); // you should use your own CONSTANT server url, and be sure to replace it throughout this file

// The product code of your product. This should match the download name in WPLS exactly
define( 'WPLS_SAMPLE_ITEM_CODE', 'illyrianWP' ); // you should use your own CONSTANT CODE, and be sure to replace it throughout this file

function sample_authorize_action( $purchase_code = '', $action = 'validate' ) {
	$domain     = home_url();
	$api_params = array(
		'wpls-verify' => $purchase_code,
		'action'      => $action,
		'domain'      => $domain,
		'product'     => WPLS_SAMPLE_ITEM_CODE,
		'validip'     => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'],
		//'envato_item' => 'yes', // uncomment this if you use envato receipt as license key
		//'type' => 'json', // default format is json, you may change 'json' or 'xml'
	);

	$request  = add_query_arg( $api_params, WPLS_SAMPLE_STORE_URL );
	$response = wp_remote_get( $request, array( 'timeout' => 15, 'sslverify' => false ) );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$authorize_data = json_decode( wp_remote_retrieve_body( $response ) );
	if ( empty( $authorize_data ) || $authorize_data === null || $authorize_data === false ) {
		return false;
	}
	update_option( 'wpls_sample_license_status', $authorize_data->valid );

	return $authorize_data;
}

function sample_serial_valid() {
	return get_option( 'wpls_sample_license_status', false );
}

function wpls_sample_verify_js() {
	?>
    <script>
        jQuery('#sample-verify').on('submit', function (e) {
            e.preventDefault();
            var action = jQuery('#sample-action').val(),
                code = jQuery('#sample-license').val();

            jQuery.ajax({
                type: "POST",
                url: '<?=admin_url( 'admin-ajax.php' );?>',
                data: {'action': action, 'code': code},
                beforeSend: function () {
                    jQuery('#saveS').prop('disabled', true).val('Please wait..');
                },
                success: function (data) {
                    var btn_value = 'Activate';
                    if (data.valid) {
                        if (action === 'sample_license_activate') {
                            // var btn_value = 'Deactivate';
                            jQuery('#sample-action').val('sample_license_deactivate');
                            jQuery('.pesan').html('<span style="color:green;">Congratulation, your license already activated</span>');
                            jQuery('tr#sample-status').load(window.location.href + ' #td-status').show();
                        }
                        else {
                            jQuery('#sample-action').val('sample_license_activate');
                            jQuery('#sample-license').val('');
                            jQuery('.pesan').html('<span style="color:green;">Congratulation, your license successful deactivated</span>');
                            jQuery('tr#sample-status').load(window.location.href + ' #td-status').hide();
                        }
                    }
                    else {
                        jQuery('.pesan').html('<span style="color:red;">' + data.info.message + '</span>');
                    }

                    jQuery('#saveS').prop('disabled', false).val(btn_value);
                }
            });
        });
    </script>

	<?php

}

function wpls_sample_license_activate() {
	if ( isset( $_POST['code'] ) ) {
		$return = sample_authorize_action( $_POST['code'], 'activate' );
		if ( $return->valid ) {
			update_option( 'wpls_sample_license_key', $_POST['code'] );
		}
		header( 'Content-type: application/json' );
		echo json_encode( $return, JSON_PRETTY_PRINT );
		die();
	}
}

function wpls_sample_license_deactivate() {
	if ( isset( $_POST['code'] ) ) {
		$return = sample_authorize_action( $_POST['code'], 'deactivate' );
		if ( $return->valid ) {
			update_option( 'wpls_sample_license_key', '' );
			update_option( 'wpls_sample_license_status', false );
		}
		header( 'Content-type: application/json' );
		echo json_encode( $return, JSON_PRETTY_PRINT );
		die();
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

if ( ! wp_next_scheduled( 'wpls_sample_cron_hook' ) ) {
	wp_schedule_event( time(), 'wpls_sample_cron_events', 'wpls_sample_cron_hook' );
}

function wpls_sample_cron_schedule( $schedules ) {
	$schedules['wpls_sample_cron_events'] = array(
		'interval' => 43200, // Every 12 hours
		'display'  => __( 'Twice a day' ),
	);

	return $schedules;
}

/*  Edit Wordpress default plugin footer   */
function change_footer_text() {
	$url  = 'https://jkruja.com';
	$text = sprintf( esc_html__( 'Developed by %sJurgen Kruja%s.' ), '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">', '</a>' );

	return $text;
}

function change_footer_version() {
	return 'Version 1.2';
}

/* Action Hooks */
add_action( 'admin_footer', 'wpls_sample_verify_js', 999 );
add_action( 'wp_ajax_sample_license_activate', 'wpls_sample_license_activate' );
add_action( 'wp_ajax_sample_license_deactivate', 'wpls_sample_license_deactivate' );

/* Filter Hooks */
add_filter( 'cron_schedules', 'wpls_sample_cron_schedule' );
add_filter( 'admin_footer_text', 'change_footer_text', 9998, 2 );
add_filter( 'update_footer', 'change_footer_version', 9999 );

