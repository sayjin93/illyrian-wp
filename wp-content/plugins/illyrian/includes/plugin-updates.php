<?php defined( 'ABSPATH' ) or exit( 'No direct script access allowed' );

if ( ! class_exists( 'WPLSPluginUpdater_illyrianWp' ) ) {
	class WPLSPluginUpdater_illyrianWp {

		var $api_url;
		var $plugin_id = 39;
		var $plugin_path;
		var $plugin_slug;
		var $license_key;
		var $product_code;
		var $email;

		public function __construct( $api_url, $plugin_path, $product_code = 'illyrianWp', $license_key = null, $email = null ) {
			$this->api_url      = $api_url;
			$this->plugin_path  = $plugin_path;
			$this->license_key  = $license_key;
			$this->email        = $email;
			$this->product_code = $product_code;

			if ( strstr( $plugin_path, '/' ) ) {
				list ( $t1, $t2 ) = explode( '/', $plugin_path );
			} else {
				$t2 = $plugin_path;
			}

			$this->plugin_slug = str_replace( '.php', '', $t2 );

			add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_for_update' ) );
			add_filter( 'plugins_api', array( &$this, 'plugin_api_call' ), 10, 3 );

			add_action( 'after_plugin_row_' . $this->plugin_path, array( $this, 'printPluginRowNotice' ), 10, 0 );
			add_action( 'wp_ajax_show_license_ui-' . $this->plugin_slug, array( $this, 'show_license_form' ) );

			// This is for testing only!
			//set_site_transient( 'update_plugins', null );

			// Show which variables are being requested when query plugin API
			//add_filter( 'plugins_api_result', array(&$this, 'debug_result'), 10, 3 );
		}

		public function check_for_update( $transient ) {
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$request_args = array(
				'id'      => $this->plugin_id,
				'slug'    => $this->plugin_slug,
				'version' => $transient->checked[ $this->plugin_path ],
				'product' => $this->product_code,
				'domain'  => home_url()
			);

			if ( $this->license_key ) {
				$request_args['license'] = $this->license_key;
			}
			if ( $this->email ) {
				$request_args['email'] = $this->email;
			}

			$request_string = $this->prepare_request( 'update_check', $request_args );
			$raw_response   = wp_remote_post( $this->api_url, $request_string );

			$response = null;
			if ( ! is_wp_error( $raw_response ) && ( $raw_response['response']['code'] == 200 ) ) {
				$response = unserialize( $raw_response['body'] );
			}

			if ( is_object( $response ) && ! empty( $response ) ) {
				// Feed the update data into WP updater
				$transient->response[ $this->plugin_path ] = $response;

				return $transient;
			}

			// Check to make sure there is not a similarly named plugin in the wordpress.org repository
			if ( isset( $transient->response[ $this->plugin_path ] ) ) {
				if ( strpos( $transient->response[ $this->plugin_path ]->package, 'wordpress.org' ) !== false ) {
					unset( $transient->response[ $this->plugin_path ] );
				}
			}

			return $transient;
		}

		public function plugin_api_call( $def, $action, $args ) {
			if ( ! isset( $args->slug ) || $args->slug != $this->plugin_slug ) {
				return $def;
			}

			$plugin_info  = get_site_transient( 'update_plugins' );
			$request_args = array(
				'id'      => $this->plugin_id,
				'slug'    => $this->plugin_slug,
				'version' => ( isset( $plugin_info->checked ) ) ? $plugin_info->checked[ $this->plugin_path ] : '1.0',
				// Current version
				'product' => $this->product_code,
				'domain'  => home_url()
			);

			if ( $this->license_key ) {
				$request_args['license'] = $this->license_key;
			}
			if ( $this->email ) {
				$request_args['email'] = $this->email;
			}

			$request_string = $this->prepare_request( $action, $request_args );
			$raw_response   = wp_remote_post( $this->api_url, $request_string );

			if ( is_wp_error( $raw_response ) ) {
				$res = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>' ), $raw_response->get_error_message() );
			} else {
				$res = unserialize( $raw_response['body'] );

				if ( $res === false ) {
					$res = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred' ), $raw_response['body'] );
				}
			}

			return $res;
		}

		public function prepare_request( $action, $args ) {
			global $wp_version;

			$request = array(
				'body'       => array(
					'wpls-action' => $action,
					'package'     => 'plugin',
					'request'     => serialize( $args ),
					'api-key'     => md5( home_url() )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
			);

			return $request;
		}

		public function debug_result( $res, $action, $args ) {
			echo '<pre>' . print_r( $res, true ) . '</pre>';

			return $res;
		}

		public function printPluginRowNotice() {
			//If there's anything wrong with the plugin's license, output a notice under the plugin row in "Plugins".
			$license = $this->wpls_authorize_action();
			if ( $license && $license->info->status === 'Active' ) {
				return;
			}

			$messages = array(
				'no_license_yet' => __( 'License is not set yet. Please enter your license key to enable automatic updates.', 'wpls' ),
				'Expired'        => __( 'Your access to updates has expired. You can continue using the plugin, but you\'ll need to renew your license to receive updates and bug fixes.', 'wpls' ),
				'Invalid'        => __( 'The current license key or site token is invalid. Please enter your license key to enable automatic updates.', 'wpls' ),
				'Deactive'       => __( 'The current license is inactive.', 'wpls' ),
				'Suspended'      => __( 'The current license is suspended.', 'wpls' ),
				'Pending'        => __( 'The current license is pending activation.', 'wpls' ),
				'Wrong_site'     => __( 'Please re-enter your license key. This is necessary because the site URL has changed.', 'wpls' ),
			);

			if ( $this->license_key ) {
				$status = $license ? $license->info->status : 'no_license_yet';
			} else {
				$status = 'no_license_yet';
			}

			$notice = isset( $messages[ $status ] ) ? $messages[ $status ] : __( 'The current license is invalid.', 'wpls' );

			$licenseLink     = $this->makeLicenseLink( apply_filters(
				'wpls_plugin_row_link_text-' . $this->plugin_slug,
				__( 'Enter License Key', 'wpls' )
			) );
			$showLicenseLink = ( $status !== 'expired' );

			//WP 4.6+ uses different styles for the update row. We use an inverted condition here because some buggy
			//plugins overwrite $wp_version. This way the default is to assume it's WP 4.6 or higher.
			$isWP46orHigher = ! ( isset( $GLOBALS['wp_version'] ) && version_compare( $GLOBALS['wp_version'], '4.5.9', '<=' ) );

			$messageClasses = array( 'update-message' );
			if ( $isWP46orHigher ) {
				$messageClasses = array_merge( $messageClasses, array(
					'notice',
					'inline',
					'notice-warning',
					'notice-alt'
				) );
			}

			$active = is_plugin_active( $this->plugin_path ) ? ' active' : '';

			?>
            <tr class="plugin-update-tr<?= $active; ?>">
                <td class="plugin-update colspanchange" colspan="3">
                    <div class="<?php echo esc_attr( implode( ' ', $messageClasses ) ); ?>">
						<?php
						if ( $isWP46orHigher ) {
							echo '<p>';
						}

						if ( $showLicenseLink ) {
							echo $licenseLink, ' | ';
						}
						echo $notice;

						if ( $isWP46orHigher ) {
							echo '</p>';
						}
						?>
                    </div>
                </td>
            </tr>
			<?php
		}

		public function show_license_form() {

			$license   = get_option( $this->product_code . '_license_key', '' );
			$email     = get_option( $this->product_code . '_license_email', '' );
			$status    = get_option( $this->product_code . '_license_status' );
			$vlicense  = $this->wpls_authorize_action();
			$is_active = false;
			if ( $vlicense && $vlicense->info->status === 'Active' ) {
				$is_active = true;
			}
			?>
            <head>
				<?php
				global $hook_suffix;
				wp_enqueue_style( 'colors' );
				wp_enqueue_style( 'ie' );
				wp_enqueue_script( 'utils' );
				wp_enqueue_script( 'svg-painter' );
				do_action( 'admin_enqueue_scripts', $hook_suffix );
				do_action( "admin_print_styles-{$hook_suffix}" );
				do_action( 'admin_print_styles' );
				do_action( "admin_print_scripts-{$hook_suffix}" );
				do_action( 'admin_print_scripts' );
				do_action( "admin_head-{$hook_suffix}" );
				do_action( 'admin_head' );
				$admin_body_classes = apply_filters( 'admin_body_class', '' );
				?>
            </head>
        <body class="wp-admin wp-core-ui no-js <?php echo $admin_body_classes . ' ' . $admin_body_class; ?>"
              style="max-width:500px!important;max-height:300px!important;">
			<?php do_action( 'in_admin_header' ); ?>
            <div class="wrap nosubsub" style="margin:0 auto;padding:0 5%; width:400px;">
                <form method="post" id="<?= $this->product_code; ?>-verify">
                    <h2>Sample Licensed Plugin</h2>
                    <input type="hidden" id="<?= $this->product_code; ?>-action"
                           name="<?= $this->product_code; ?>-action"
                           value="<?= $is_active ? $this->product_code . '_license_deactivate' : $this->product_code . '_license_activate'; ?>"/>
                    <table class="wp-list-table widefat tags ui-sortable">
                        <tbody>
                        <tr>
                            <td valign="top">
                                <label for="<?= $this->product_code; ?>-license">License Key:</label><br>
                                <input class="textfield" name="<?= $this->product_code; ?>-license" size="85"
                                       type="text" id="<?= $this->product_code; ?>-license"
                                       value="<?php echo $license; ?>" required/>
                                <p class="description"><?php _e( 'Valid License  to make All feature work correctly', 'wpls' ) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <label for="<?= $this->product_code; ?>-email">Email:</label><br>
                                <input class="textfield" name="<?= $this->product_code; ?>-email" size="85" type="text"
                                       id="<?= $this->product_code; ?>-email" value="<?php echo $email; ?>" required/>
                                <p class="description"><?php _e( 'Please provide your registered email with us', 'wpls' ) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="submit" class="button-primary" id="<?= $this->product_code; ?>-saveS"
                                       name="saveS" value="<?= $is_active ? __( 'Deactivate' ) : __( 'Activate' ); ?>">
                                <p class="<?= $this->product_code; ?>-pesan"></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
			<?php
			do_action( 'in_admin_footer' );
			do_action( 'admin_footer' );
			do_action( "admin_print_footer_scripts-{$hook_suffix}" );
			do_action( 'admin_print_footer_scripts' );
			do_action( "admin_footer-{$hook_suffix}" );
			echo '</body></html>';
			die();
		}

		private function makeLicenseLink( $linkText = 'License' ) {
			return sprintf(
				'<a href="%s" class="thickbox" title="%s">%s</a>',
				esc_attr( add_query_arg(
					array( 'TB_iframe' => true ),
					$this->getLicensingPageUrl()
				) ),
				esc_attr( $this->getPageTitle() ),
				apply_filters( 'wpls_action_link_text-' . $this->plugin_slug, $linkText )
			);
		}

		private function getLicensingPageUrl() {
			$url = add_query_arg(
				array(
					'action'   => $this->getAjaxActionName(),
					'_wpnonce' => wp_create_nonce( 'show_license' ),
					//Assumes the default license action = "show_license".
				),
				admin_url( 'admin-ajax.php' )
			);

			return $url;
		}

		private function getAjaxActionName() {
			return 'show_license_ui-' . $this->plugin_slug;
		}

		private function getPageTitle() {
			return apply_filters( 'wpls_license_ui_title-' . $this->plugin_slug, __( 'Manage Licenses', 'wpls' ) );
		}

		private function wpls_authorize_action( $action = 'validate' ) {
			global $wp_version;

			$domain     = home_url();
			$api_params = array(
				'wpls-verify' => $this->license_key,
				'action'      => $action,
				'domain'      => $domain,
				'email'       => $this->email,
				'product'     => $this->product_code,
				'validip'     => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']
			);

			$request  = add_query_arg( $api_params, WPLS_STORE_URL );
			$response = wp_remote_get( $request, array( 'timeout' => 15, 'sslverify' => false ) );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$authorize_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( empty( $authorize_data ) || $authorize_data === null || $authorize_data === false ) {
				return false;
			}

			return $authorize_data;
		}

		private function buat_log( $message, $success = true, $end = false ) {
			if ( ! file_exists( WP_CONTENT_DIR . '/uploads/log.txt' ) ) {
				file_put_contents( WP_CONTENT_DIR . '/uploads/log.txt', 'Shipping Logs' . "\r\n" );
			}

			$text = "[" . date( "m/d/Y g:i A" ) . "] - " . ( $success ? "SUCCESS :" : "FAILURE :" ) . $message . "\n";

			if ( $end ) {
				$text .= "\n------------------------------------------------------------------\n\n";
			}

			$debug_log_file_name = WP_CONTENT_DIR . '/uploads/log.txt';
			$fp                  = fopen( $debug_log_file_name, "a" );
			fwrite( $fp, $text );
			fclose( $fp );
		}

	}
}
