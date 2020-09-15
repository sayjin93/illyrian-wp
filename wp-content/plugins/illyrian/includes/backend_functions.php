<?php defined( 'ABSPATH' ) or exit( 'No direct script access allowed' );

class Illyrian_Backend {
	var $shop_url;
	var $product_code;
	var $ouput_format = 'json';

	public function __construct( $shop_url, $product_code ) {

		$this->shop_url     = $shop_url;
		$this->product_code = $product_code;

		/* Action Hooks */
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_action( 'admin_footer', array( $this, 'wpls_verify_js' ), 999 );
		add_action( 'wp_ajax_' . $this->product_code . '_license_activate', array( $this, 'license_activate' ) );
		add_action( 'wp_ajax_' . $this->product_code . '_license_deactivate', array( $this, 'license_deactivate' ) );
		add_action( $this->product_code . '_cron_hook', array( $this, 'check_serial_valid' ) );

		/* Filter Hooks */
		add_filter( 'cron_schedules', array( $this, 'cron_schedule' ) );
		add_filter( 'admin_footer_text', array( $this, 'change_footer_text' ), 999, 2 );

		if ( ! wp_next_scheduled( $this->product_code . '_cron_hook' ) ) {
			wp_schedule_event( time(), $this->product_code . '_cron_events', $this->product_code . '_cron_hook' );
		}
	}

	public function set_output( $output = 'json' ) {
		$this->ouput_format = $output;
	}


	/* Register Plugin in Wordpress Menu */
	public function plugin_menu() {
		add_menu_page(
			"Illyrian Plugin",
			"Illyrian Plugin",
			'manage_options',
			'illyrian_plugin',
			array(
				$this,
				'illyrian_page'
			),
			'',
			6
		);

		add_submenu_page( "illyrian_plugin", "Plugin Dashboard", "Illyrian Dashboard", "manage_options", "illyrian_plugin", array(
			$this,
			"illyrian_page"
		), 1 );
		add_submenu_page( "illyrian_plugin", "Plugin License", "Activate License", "manage_options", "illyrian_plugin_license", array(
			$this,
			"illyrian_plugin_license"
		), 2 );
	}

	/* Generate Dashboard Page */
	public function illyrian_page() {
		$isValid = $this->check_serial_valid();
		?>

        <div class="illyrian-header">
            <img class="illyrian-header-title" src=" <?php echo plugin_image ?>logo.png" alt="">
            <img class="illyrian-header-mascot" src="<?php echo plugin_image ?>mascot.png" alt="">
        </div>
        <div class="illyrian-body" role="main">
            <div class="illyrian-body-content">
                <h1 class="illyrian-nav-container">
                    <a class="illyrian-main-nav-item illyrian-nav-item illyrian-spacing-item" href="#">&nbsp;</a>

                    <a class="illyrian-main-nav-item illyrian-nav-item illyrian-active" title="Illyrian Dashboard"
                       href="admin.php?page=illyrian_plugin">
                        Illyrian Dashboard</a>

                    <a class="illyrian-main-nav-item illyrian-nav-item illyrian-spacing-item" href="#">&nbsp;</a>

                    <a class="illyrian-main-nav-item illyrian-nav-item" title="Activate License"
                       href="admin.php?page=illyrian_plugin_license">
                        Activate License</a>
                </h1>
                <div class="illyrian-settings-pages">
					<?php if ( $isValid == true ) {
						if ( isset( $_POST['save'] ) && $_POST['action'] == 'form_submit' ) {
							update_option( "active", $_POST['active'] );
							update_option( "debug", $_POST['debug'] );
							update_option( "blocked_country", $_POST['blocked_country'] );
							update_option( "whitelisted_ip", $_POST['whitelisted_ip'] );
							update_option( "element", $_POST['element'] );
							update_option( "element_value", $_POST['element_value'] );
							update_option( "num-of-pages", $_POST['num-of-pages'] );
							update_option( "scriptCTR", $_POST['scriptCTR'] );

							update_option( "limitAd1", $_POST['limitAd1'] );
							update_option( "limitAd2", $_POST['limitAd2'] );
							update_option( "limitAd3", $_POST['limitAd3'] );

							update_option( "codeAd1", $_POST['codeAd1'] );
							update_option( "codeAd2", $_POST['codeAd2'] );
							update_option( "codeAd3", $_POST['codeAd3'] );

							update_option( "time", $_POST['time'] );
							update_option( "opacity", $_POST['opacity'] );
							update_option( "custom_css", $_POST['custom_css'] );
							?>

                            <div class="illyrian-notice illyrian-success-notice">
                                <div class="illyrian-notice-text illyrian-success-notice-text">
                                    <p class="illyrian-notice-message">Settings saved successfully.</p>
                                </div>
                            </div>
							<?php
						}
						if ( isset( $_POST['reset'] ) && $_POST['action'] == 'form_submit' ) {
							update_option( "active", 'no' );
							update_option( "debug", 'on' );
							update_option( "blocked_country", 'no' );
							update_option( "whitelisted_ip", '127.0.0.1' );
							update_option( "element", 'class' );
							update_option( "element_value", 'ngg-browser-next' );
							update_option( "num-of-pages", '1' );
							update_option( "scriptCTR", '50' );

							update_option( "limitAd1", '59' );
							update_option( "limitAd2", '26' );
							update_option( "limitAd3", '15' );

							update_option( "codeAd1", '' );
							update_option( "codeAd2", '' );
							update_option( "codeAd3", '' );

							update_option( "time", '12' );
							update_option( "opacity", '0.5' );
							update_option( "custom_css", '/*Enter css code here*/' );
							?>

                            <div class="illyrian-notice illyrian-info-notice">
                                <div class="illyrian-notice-text illyrian-info-notice-text">
                                    <p class="illyrian-notice-message">Settings were reset to default.</p>
                                </div>
                            </div>
							<?php
						}
						if ( isset( $_POST['clearCookies'] ) && $_POST['action'] == 'form_submit' ) {

							$this->ClearCookies();//call function to clear cookies

							?>
                            <div class="illyrian-notice illyrian-info-notice">
                                <div class="illyrian-notice-text illyrian-info-notice-text">
                                    <p class="illyrian-notice-message">Cookies cleared successfully.</p>
                                </div>
                            </div>
							<?php
						}
						?>
                        <form method="post" id="<?= $this->product_code; ?>-form">
                            <table class="form-table">
                                <tbody>
                                <!-- Active script -->
                                <tr>
                                    <th scope="row">Active Script</th>
                                    <td>
                                <span>
                                <input type="radio" name="active"
                                       value="yes" <?php if ( get_option( 'active' ) == 'yes' ) {
	                                echo 'checked="checked"';
                                } ?> > Yes
                                </span>
                                        <span>
                                <input type="radio" name="active"
                                       value="no" <?php if ( get_option( 'active' ) == 'no' ) {
	                                echo 'checked="checked"';
                                } ?> >No
                                </span>
                                    </td>
                                </tr>

                                <!-- Debug mode -->
                                <tr>
                                    <th scope="row">Debug Mode</th>
                                    <td>
                                <span>
                                <input type="radio" name="debug" value="on" <?php if ( get_option( 'debug' ) == 'on' ) {
	                                echo 'checked="checked"';
                                } ?> > On
                                </span>
                                        <span>
                                <input type="radio" name="debug"
                                       value="off" <?php if ( get_option( 'debug' ) == 'off' ) {
	                                echo 'checked="checked"';
                                } ?> >Off
                                </span>
                                        <span><input type="submit" name="clearCookies" class="button button-secondary"
                                                     value="Clear Cookies"/>
                                </span>
                                    </td>
                                </tr>

                                <!-- Hide from Blocked Country -->
                                <tr>
                                    <th scope="row">Hide from AL, RS, MK</th>
                                    <td>
                                <span>
                                <input type="radio" name="blocked_country"
                                       value="yes" <?php if ( get_option( 'blocked_country' ) == 'yes' ) {
	                                echo 'checked="checked"';
                                } ?> > Yes
                                </span>
                                        <span>
                                <input type="radio" name="blocked_country"
                                       value="no" <?php if ( get_option( 'blocked_country' ) == 'no' ) {
	                                echo 'checked="checked"';
                                } ?> >No
                                </span>
                                        <span>
                                <input type="text" name="whitelisted_ip" id="whitelisted_ip"
                                       value="<?php echo get_option( 'whitelisted_ip' ); ?>">
                                        <p class="description inline">Add IP to whitelist.</p>
                                </span>
                                    </td>
                                </tr>

                                <!-- Element to attach -->
                                <tr>
                                    <th scope="row">Element</th>
                                    <td>
                                <span>
                                <input type="radio" name="element"
                                       value="id" <?php if ( get_option( 'element' ) == 'id' ) {
	                                echo 'checked="checked"';
                                } ?> >ID
                                </span>
                                        <span>
                                <input type="radio" name="element"
                                       value="class" <?php if ( get_option( 'element' ) == 'class' ) {
	                                echo 'checked="checked"';
                                } ?> >Class
                               </span>
                                        <span>
                                <input type="text" name="element_value" id="element"
                                       value="<?php echo get_option( 'element_value' ); ?>">
                                </span>
                                    </td>
                                </tr>

                                <!-- No. PageViews -->
                                <tr>
                                    <th scope="row">Number of Page Views</th>
                                    <td>
                                        <input type="number" name="num-of-pages" id="num-of-pages"
                                               value="<?php echo get_option( 'num-of-pages' ); ?>">
                                    </td>
                                </tr>

                                <!--Total CTR Limit -->
                                <tr>
                                    <th scope="row">Script CTR (in %)</th>
                                    <td>
                                        <input type="number" name="scriptCTR" id="scriptCTR"
                                               value="<?php echo get_option( 'scriptCTR' ); ?>"></td>

                                </tr>

                                <!--Ad 1 Code and CTR Limit -->
                                <tr>
                                    <th scope="row">Ad 1</th>
                                    <td>
                                        <table>
                                            <tr class="header">
                                                <td>
                                                    <label>Ad1 CTR (in %)</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="p0">
                                                    <input type="number" name="limitAd1" id="limitAd1"
                                                           value="<?php echo get_option( 'limitAd1' ); ?>">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                            <tr class="header">
                                                <td>
                                                    <label>Ad1 Code</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="p0">
                                                <textarea rows="4" cols="100"
                                                          name="codeAd1"><?php echo stripslashes( get_option( 'codeAd1' ) ); ?></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!--Ad 2 Code and CTR Limit -->
                                <tr>
                                    <th scope="row">Ad 2</th>
                                    <td>
                                        <table>
                                            <tr class="header">
                                                <td>
                                                    <label>Ad2 CTR (in %)</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="p0">
                                                    <input type="number" name="limitAd2" id="limitAd2"
                                                           value="<?php echo get_option( 'limitAd2' ); ?>">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                            <tr class="header">
                                                <td>
                                                    <label>Ad2 Code</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="p0">
                                                <textarea rows="4" cols="100"
                                                          name="codeAd2"><?php echo stripslashes( get_option( 'codeAd2' ) ); ?></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!--Ad 3 Code and CTR Limit -->
                                <tr>
                                    <th scope="row">Ad 3</th>
                                    <td>
                                        <table>
                                            <tr class="header">
                                                <td>
                                                    <label>Ad3 CTR (in %)</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="p0">
                                                    <input type="number" name="limitAd3" id="limitAd3"
                                                           value="<?php echo get_option( 'limitAd3' ); ?>">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                            <tr class="header">
                                                <td>
                                                    <label>Ad3 Code</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="p0">
                                                <textarea rows="4" cols="100"
                                                          name="codeAd3"><?php echo stripslashes( get_option( 'codeAd3' ) ); ?></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Cookie Time -->
                                <tr>
                                    <th scope="row">Cookie Time (in hours)</th>
                                    <td>
                                        <input type="number" name="time" id="time"
                                               value="<?php echo get_option( 'time' ); ?>">
                                    </td>
                                </tr>

                                <!-- Opacity -->
                                <tr>
                                    <th scope="row">Opacity of Ads</th>
                                    <td>
                                <span>
                                <input type="radio" name="opacity"
                                       value="0" <?php if ( get_option( 'opacity' ) == '0' ) {
	                                echo 'checked="checked"';
                                } ?>>0
                                    </span>
                                        <span>
                                <input type="radio" name="opacity"
                                       value="0.5" <?php if ( get_option( 'opacity' ) == '0.5' ) {
	                                echo 'checked="checked"';
                                } ?>>0.5
                                    </span>
                                        <span>
                                <input type="radio" name="opacity"
                                       value="1" <?php if ( get_option( 'opacity' ) == '1' ) {
	                                echo 'checked="checked"';
                                } ?>>1
                                    </span>
                                    </td>
                                </tr>

                                <!-- Custom CSS -->
                                <tr>
                                    <th scope="row">Custom CSS</th>
                                    <td>
                                <textarea rows="6" cols="100"
                                          name="custom_css"><?php echo stripslashes( get_option( 'custom_css' ) ); ?></textarea>
                                        <p class="description">Please do not add Style tag.</p>
                                    </td>
                                </tr>

                                <!-- Buttons -->
                                <tr>
                                    <th></th>
                                    <td>
                                        <input type="submit" name="save" class="button button-primary"
                                               value="Save Changes"/> &nbsp;&nbsp;&nbsp;
                                        <input type="submit" name="reset" class="button button-secondary"
                                               value="Reset"/>
                                        <input type="hidden" name="action" value="form_submit"/>

                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
						<?php
					} else { ?>
                        <p>Please activate your license
                            <a title="Activate License"
                               href="admin.php?page=illyrian_plugin_license">in this link.</a>
                        </p>
					<?php } ?>
                </div>
            </div>
        </div>
		<?php
	}

	/* Generate License Page */
	public function illyrian_plugin_license() {
		$license = get_option( $this->product_code . '_license_key', '' );
		$email   = get_option( $this->product_code . '_license_email', '' );
		$status  = get_option( $this->product_code . '_license_status' );
		?>
        <div class="illyrian-header">
            <img class="illyrian-header-title" src="<?php echo plugin_image ?>logo.png" alt="">
            <img class="illyrian-header-mascot" src="<?php echo plugin_image ?>mascot.png" alt="">
        </div>
        <div class="illyrian-body" role="main">
            <div class="illyrian-body-content nosubsub">
                <h1 class="illyrian-nav-container">
                    <a class="illyrian-main-nav-item illyrian-nav-item illyrian-spacing-item" href="#">&nbsp;</a>

                    <a class="illyrian-main-nav-item illyrian-nav-item" title="Illyrian Dashboard"
                       href="admin.php?page=illyrian_plugin">
                        Illyrian Dashboard </a>

                    <a class="illyrian-main-nav-item illyrian-nav-item illyrian-spacing-item" href="#">&nbsp;</a>

                    <a class="illyrian-main-nav-item illyrian-nav-item illyrian-active" title="Activate License"
                       href="admin.php?page=illyrian_plugin_license">
                        Activate License</a>
                </h1>
                <div class="illyrian-settings-pages">
                    <form method="post" id="<?= $this->product_code; ?>-verify">
                        <input type="hidden"
                        <input type="hidden"
                               id="<?= $this->product_code; ?>-action"
                               name="<?= $this->product_code; ?>-action"
                               value="<?= $this->serial_valid() ? $this->product_code . '_license_deactivate' : $this->product_code . '_license_activate'; ?>"/>
                        <table class="wp-list-table widefat tags ui-sortable">
                            <tbody>
                            <tr>
                                <td>
                                    <label for="<?= $this->product_code; ?>-license">License Key:</label>
                                </td>
                                <td>
                                    <input class="textfield" name="<?= $this->product_code; ?>-license" size="50"
                                           type="text" id="<?= $this->product_code; ?>-license"
                                           value="<?php echo $license; ?>" required/>
                                    <p class="description"><?php _e( 'Valid License  to make All feature work correctly', 'wpls' ) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="<?= $this->product_code; ?>-email">Email:</label>
                                </td>
                                <td>
                                    <input class="textfield" name="<?= $this->product_code; ?>-email" size="50"
                                           type="text" id="<?= $this->product_code; ?>-email"
                                           value="<?php echo $email; ?>" required/>
                                    <p class="description"><?php _e( 'Please provide your registered email with us', 'wpls' ) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input type="submit" class="button-primary" id="<?= $this->product_code; ?>-saveS"
                                           name="saveS"
                                           value="<?= $this->serial_valid() ? __( 'Deactivate' ) : __( 'Activate' ); ?>">
                                    <p class="<?= $this->product_code; ?>-pesan"></p>
                                </td>
                            </tr>
                            <tr id="<?= $this->product_code; ?>-status"
                                style="<?= $this->serial_valid() ? '' : 'display:none;'; ?>">
                                <td colspan="2" id="td-status">
									<?php echo '<p><b>Domain:</b> ', print_r( $this->authorize_action( $license, $email )->info->domain, 1 ), '</p>'; ?>
									<?php echo '<p><b>Expire:</b> ', print_r( $this->authorize_action( $license, $email )->info->expire, 1 ), '</p>'; ?>
									<?php echo '<p><b>Status: <span style="color:green;">', print_r( $this->authorize_action( $license, $email )->info->status, 1 ), '</span></b></p>'; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
		<?php
	}


	public function check_serial_valid( $action = 'validate' ) {
		$serial    = $license = get_option( $this->product_code . '_license_key', '' );
		$email     = get_option( $this->product_code . '_license_email', '' );
		$authorize = $this->authorize_action( $serial, $email, $action );
		if ( $authorize == false ) {
			return false;
		}
		if ( $authorize->valid ) {
			return true;
		}

		return false;
	}

	private function authorize_action( $purchase_code = '', $email = '', $action = 'validate' ) {
		global $wp_version;

		$domain     = home_url();
		$api_params = array(
			'wpls-verify' => $purchase_code,
			'action'      => $action,
			'domain'      => $domain,
			'email'       => $email,
			'product'     => $this->product_code,
			'validip'     => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'],
			'type'        => $this->ouput_format, // default format is json, you may change 'json' or 'xml'
		);

		$request  = add_query_arg( $api_params, $this->shop_url );
		$response = wp_remote_get( $request, array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$authorize_data = json_decode( wp_remote_retrieve_body( $response ) );
		if ( empty( $authorize_data ) || $authorize_data === null || $authorize_data === false ) {
			return false;
		}
		update_option( $this->product_code . '_license_status', $authorize_data->valid );

		return $authorize_data;
	}

	public function serial_valid() {
		return get_option( $this->product_code . '_license_status', false );
	}

	public function wpls_verify_js() {
		?>

        <script>
            jQuery('#<?=$this->product_code;?>-verify').on('submit', function (e) {
                e.preventDefault();
                let action = jQuery('#<?=$this->product_code;?>-action').val(),
                    email = jQuery('#<?=$this->product_code;?>-email').val(),
                    code = jQuery('#<?=$this->product_code;?>-license').val();

                jQuery.ajax({
                    type: "POST",
                    url: '<?=admin_url( 'admin-ajax.php' );?>',
                    data: {'action': action, 'email': email, 'code': code},
                    beforeSend: function () {
                        jQuery('#<?=$this->product_code;?>-saveS').prop('disabled', true).val('Please wait..');
                    },
                    success: function (data) {
                        // console.log(data);
                        let btn_value = 'Activate';
                        if (data) {
                            if (data.valid) {
                                if (action == '<?=$this->product_code;?>_license_activate') {
                                    btn_value = 'Deactivate';
                                    jQuery('#<?=$this->product_code;?>-action').val('<?=$this->product_code;?>_license_deactivate');
                                    jQuery('.<?=$this->product_code;?>-pesan').html('<span style="color:green;">Congratulation, your license already activated</span>');
                                    //jQuery('tr#<?//=$this->product_code;?>//-status').load(window.location.href + ' #td-status').show();
                                } else {
                                    jQuery('#<?=$this->product_code;?>-action').val('<?=$this->product_code;?>_license_activate');
                                    jQuery('#<?=$this->product_code;?>-license').val('');
                                    jQuery('#<?=$this->product_code;?>-email').val('');
                                    jQuery('.<?=$this->product_code;?>-pesan').html('<span style="color:green;">Congratulation, your license successful deactivated</span>');
                                    //jQuery('tr#<?//=$this->product_code;?>//-status').load(window.location.href + ' #td-status').hide();
                                }

                                location.reload();
                            } else {
                                jQuery('.<?=$this->product_code;?>-pesan').html('<span style="color:red;">' + data.info.message + '</span>');
                            }
                        }

                        jQuery('#<?=$this->product_code;?>-saveS').prop('disabled', false).val(btn_value);
                    },
                    error: function (data) {
                        // console.log(data);
                        jQuery('#<?=$this->product_code;?>-saveS').prop('disabled', false).val('Update');
                    }
                });
            });
        </script>

		<?php
	}

	public function license_activate() {
		if ( isset( $_POST['email'] ) && isset( $_POST['code'] ) ) {
			$return = $this->authorize_action( $_POST['code'], $_POST['email'], 'activate' );
			if ( $return->valid ) {
				update_option( $this->product_code . '_license_key', $_POST['code'] );
				update_option( $this->product_code . '_license_email', $_POST['email'] );
			}
			header( 'Content-type: application/json' );
			echo json_encode( $return, JSON_PRETTY_PRINT );
			die();
		}
	}

	public function license_deactivate() {
		if ( isset( $_POST['email'] ) && isset( $_POST['code'] ) ) {
			$return = $this->authorize_action( $_POST['code'], $_POST['email'], 'deactivate' );
			if ( $return->valid ) {
				update_option( $this->product_code . '_license_key', '' );
				update_option( $this->product_code . '_license_email', '' );
				update_option( $this->product_code . '_license_status', false );
			}
			header( 'Content-type: application/json' );
			echo json_encode( $return, JSON_PRETTY_PRINT );
			die();
		}
	}

	public function cron_schedule( $schedules ) {
		$schedules[ $this->product_code . '_cron_events' ] = array(
			'interval' => 43200, // Every 12 hours
			'display'  => __( 'Twice a day' ),
		);

		return $schedules;
	}


	/*  Edit Wordpress default plugin footer   */
	public function change_footer_text() {
		$url  = 'https://jkruja.com';
		$text = 'Developed by %sJurgen Kruja%s.';

		return sprintf( esc_html__( $text ), '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">', '</a>' );
	}

	/*  Delete cookies when Clear Cookies button is clicked  */
	public function ClearCookies() {
		if ( isset( $_COOKIE['visit'] ) || isset( $_COOKIE['clicked_ad'] ) ) {
			unset( $_COOKIE['visit'] );
			unset( $_COOKIE['clicked_ad'] );
			setcookie( 'visit', null, - 1, '/' );
			setcookie( 'clicked_ad', null, - 1, '/' );

			return true;
		} else {
			return false;
		}
	}
}
