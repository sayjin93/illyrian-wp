<?php

if ( ! class_exists( 'IllyrianClass' ) ) {
	class IllyrianClass {

		/*  Construct The Plugin Object */
		public function __construct() {
			/* Set the constants needed by the plugin. */
			add_action( 'plugins_loaded', array( &$this, 'ads_constants' ), 1 );

			/* Load the backend files. */
			add_action( 'plugins_loaded', array( &$this, 'backend_assets' ), 2 );

			/* Load the backend scripts. */
			add_action( 'wp_loaded', array( &$this, 'backend_scripts' ), 3 );

			/* Load the frontend files. */
			add_action( 'wp_enqueue_scripts', array( &$this, 'frontend_assets' ) );
		}

		/*  Defines constants used by the plugin.   */
		function ads_constants() {
			/* Set constant path to the Ads plugin directory. */
			define( 'plugin_dir', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/* Set constant path to the Ads plugin URL. */
			define( 'plugin_url', trailingslashit( plugin_dir_url( __FILE__ ) ) );

			/* Set the constant path to the Ads admin directory. */
			define( 'plugin_includes', plugin_dir . trailingslashit( 'includes' ) );

			/* Set the constant path to the Ads admin directory. */
			define( 'plugin_incudes_url', plugin_url . trailingslashit( 'includes' ) );

			/* Set the constant path to the Ads stylesheet directory. */
			define( 'plugin_assets', plugin_url . trailingslashit( 'assets' ) );

			/* Set the constant path to the Image directory. */
			define( 'plugin_image', plugin_assets . trailingslashit( 'img' ) );
		}

		/* Load the backend files needed for the plugin. */
		function backend_assets() {
			/* Load the Files in Admin Section. */
			require_once( plugin_includes . 'backend_functions.php' );
			$license = wpls_sample_get_license_data();
			$valid   = $license->valid;
			if ( $valid == 'true' ) {
				require_once( plugin_includes . 'frontend_functions.php' );
				require_once( plugin_includes . 'process_post.php' );
			}
		}

		/* Load the backend files needed for the plugin. */
		function backend_scripts() {
			/* Load the Files in Admin Section. */

			wp_enqueue_style( 'custom_wp_admin_css', plugin_assets . 'css/admin.css' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'custom_wp_admin_js', plugin_assets . 'js/backend_script.js' );
		}

		/* Load the frontend files needed for the plugin. */
		function frontend_assets() {
			wp_enqueue_style( 'custom_frontend_css', plugin_assets . 'css/frontend.css' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jscookies', plugin_assets . 'js/cookies.js' );
			wp_enqueue_script( 'devtool', plugin_assets . 'js/devtools_detect.js' );
			wp_enqueue_script( 'jdetects', plugin_assets . 'js/jdetects.js' );
			wp_enqueue_script( 'jkscript', plugin_assets . 'js/frontend_scripts.js' );
		}
	}
}

/* Instantiate the Store class */
$ObStore = new IllyrianClass();