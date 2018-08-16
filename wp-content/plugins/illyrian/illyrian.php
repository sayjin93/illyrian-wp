<?php
/*
Plugin Name: Illyrian WP
Plugin URI: http://jkruja.com/illyrian
Description: Illustrates how to include an updater in your plugin for Wordpress Licensing System
Author: Jurgen Kruja
Author URI: http://jkruja.com
Version: 1.1
*/

require_once 'constants.php';

/* Register plugin menu page */
function wp_illyrian_admin_menu() {
	add_menu_page(
		"WP Illyrian Plugin",
		"Illyrian Plugin",
		'manage_options',
		'illyrian_plugin',
		'illyrian_page',
//		plugin_image . 'icon-fefaut.png',
		'',
		6
	);

	add_submenu_page( "illyrian_plugin", "Dashboard Illyrian Plugin", "Illyrian Dashboard", "manage_options", "illyrian_plugin", "illyrian_page" );
	add_submenu_page( "illyrian_plugin", "WP Illyrian License", "Activate License", "manage_options", "illyrian_plugin_license", "illyrian_plugin_license" );
}

/* Illyrian Dashboard */
function illyrian_page() {
	$license = wpls_sample_get_license_data();
	$valid   = $license->valid;
	?>
    <div class="illyrian-header">
        <img class="illyrian-header-title" src="<?php echo plugin_image ?>logo.png" alt="">
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
				<?php if ( $valid == 'true' ) {
					if ( isset( $_POST['submit'] ) && $_POST['action'] == 'form_submit' ) {
						update_option( "active", $_POST['active'] );
						update_option( "debug", $_POST['debug'] );
						update_option( "element", $_POST['element'] );
						update_option( "element_value", $_POST['element_value'] );
						update_option( "num-of-pages", $_POST['num-of-pages'] );
						update_option( "scriptCTR", $_POST['scriptCTR'] );

						update_option( "limitAd1", $_POST['limitAd1'] );
						update_option( "limitAd2", $_POST['limitAd2'] );
						update_option( "limitAd3", $_POST['limitAd3'] );

						update_option( "codeAd1", $_POST['codeAd1'] );;
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
					?>
                    <form action="" id="plugin-form" method="post">
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
                                            <td>
                                                <label>Ad1 Code</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="number" name="limitAd1" id="limitAd1"
                                                       value="<?php echo get_option( 'limitAd1' ); ?>">
                                            </td>

                                            <td><textarea rows="4" cols="100"
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
                                            <td>
                                                <label>Ad2 Code</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="number" name="limitAd2" id="limitAd2"
                                                       value="<?php echo get_option( 'limitAd2' ); ?>">
                                            </td>

                                            <td><textarea rows="4" cols="100"
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
                                            <td>
                                                <label>Ad3 Code</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="number" name="limitAd3" id="limitAd3"
                                                       value="<?php echo get_option( 'limitAd3' ); ?>">
                                            </td>

                                            <td><textarea rows="4" cols="100"
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

                            <!-- Cutom CSS -->
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
                                    <input type="submit" name="submit" class="button button-primary"
                                           value="Save Changes"> &nbsp;&nbsp;&nbsp; <input type="submit" name="reset"
                                                                                           class="button button-secondary"
                                                                                           value="Reset">
                                    <input type="hidden" name="action" value="form_submit"/>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
					<?php
				} else { ?>
                    <p>Please activate your license <a title="Activate License"
                                                       href="admin.php?page=illyrian_plugin_license">in this link.</a>
                    </p>
				<?php } ?>
            </div><!-- End illyrian-body-settings-pages -->
        </div><!-- End illyrian-body-content-->
    </div><!-- End illyrian-body -->
	<?php
}

/* Activate License */
function illyrian_plugin_license() {
	$license = get_option( 'wpls_sample_license_key' );
	$status  = get_option( 'wpls_sample_license_status' );
	?>
    <div class="illyrian-header">
        <img class="illyrian-header-title" src="<?php echo plugin_image ?>logo.png" alt="">
        <img class="illyrian-header-mascot" src="<?php echo plugin_image ?>mascot.png" alt="">
    </div>
    <div class="illyrian-body" role="main">
        <div class="illyrian-body-content">
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
                <form method="post" action="options.php">
					<?php settings_fields( 'wpls_sample_license' ); ?>
                    <table class="form-table">
                        <tbody>
                        <tr valign="top">
                            <th scope="row" valign="top">
                                <label for="wpls_sample_license_url"><?php _e( 'Where you get license?' ); ?></label>
                            </th>
                            <td>
								<?php _e( 'Please visit <a href="https://jkruja.com/product/illyrian-wp-plugin/">this link</a> to get your license.' ); ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" valign="top">
                                <label for="wpls_sample_license_key"><?php _e( 'License Key:' ); ?></label>
                            </th>
                            <td>
                                <input id="wpls_sample_license_key" name="wpls_sample_license_key" type="text"
                                       class="regular-text" value="<?php esc_attr_e( $license ); ?>"/>
                                <p class="description"><?php _e( 'Enter your license key' ); ?></p>
                            </td>
                        </tr>
						<?php if ( false !== $license ) { ?>
                            <tr valign="top">
                                <th scope="row" valign="top">
                                    <label for="act_button"><?php _e( 'Action Button:' ); ?></label>
                                </th>
                                <td>
									<?php if ( $status == 'true' ) { ?><?php wp_nonce_field( 'wpls_sample_nonce', 'wpls_sample_nonce' ); ?>
                                        <input type="submit" class="button-secondary" name="wpls_license_deactivate"
                                               value="<?php _e( 'Deactivate License' ); ?>"/><span
                                                style="color:green;"><?php _e( 'Active' ); ?></span>
									<?php } else {
										wp_nonce_field( 'wpls_sample_nonce', 'wpls_sample_nonce' ); ?>
                                        <input type="submit" class="button-secondary" name="wpls_license_activate"
                                               value="<?php _e( 'Activate License' ); ?>"/>
									<?php } ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" valign="top"><?php _e( 'License data' ); ?></th>
                                <td>
									<?php $license = wpls_sample_get_license_data();
									echo '<pre>';
									print_r( $license->info );
									echo '</pre>'; ?>
                                </td>
                            </tr>
						<?php } ?>
                        </tbody>
                    </table>
					<?php submit_button(); ?>
                </form>
            </div>
        </div>
    </div>
	<?php
}

add_action( 'admin_menu', 'wp_illyrian_admin_menu' );