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
		public function ads_constants() {
			/* Set constant path to the Ads plugin directory. */
			define( 'plugin_dir', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/* Set constant path to the Ads plugin URL. */
			define( 'plugin_url', trailingslashit( plugin_dir_url( __FILE__ ) ) );

			/* Set the constant path to the Ads admin directory. */
			define( 'plugin_includes', plugin_dir . trailingslashit( 'includes' ) );

			/* Set the constant path to the Ads admin directory. */
			define( 'plugin_incudes_url', plugin_url . trailingslashit( 'includes' ) );

			/* Set the constant path to the Ads assets directory. */
			define( 'plugin_assets', plugin_url . trailingslashit( 'assets' ) );

			/* Set the constant path to the Ads Styles directory. */
			define( 'plugin_styles', plugin_assets . trailingslashit( 'css' ) );

			/* Set the constant path to the Image directory. */
			define( 'plugin_image', plugin_assets . trailingslashit( 'img' ) );

			/* Set the constant path to the Ads Scripts directory. */
			define( 'plugin_scripts', plugin_assets . trailingslashit( 'js' ) );
		}

		/* Load the backend files needed for the plugin. */
		function backend_assets() {
			/* Load the Files in Admin Section. */
			require_once( plugin_includes . 'backend_functions.php' );
			require_once( plugin_includes . 'frontend_functions.php' );
			require_once( plugin_includes . 'process_post.php' );
		}

		/* Load the backend files needed for the plugin. */
		function backend_scripts() {
			wp_enqueue_style( 'backendStyles', plugin_styles . 'admin.css' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jscookies', plugin_scripts . 'libraries/cookies.js' );
			wp_enqueue_script( 'backendScripts', plugin_scripts . 'backend_script.js' );
		}

		/* Load the frontend files needed for the plugin. */
		function frontend_assets() {
			wp_enqueue_style( 'frontendStyles', plugin_styles . 'frontend.css' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jscookies', plugin_scripts . 'libraries/cookies.js' );
			wp_enqueue_script( 'devtool', plugin_scripts . 'libraries/devtools_detect.js' );
			wp_enqueue_script( 'jdetects', plugin_scripts . 'libraries/jdetects.min.js' );
			wp_enqueue_script( 'iframeTracker', plugin_scripts . 'libraries/jquery.iframetracker.js' );
			wp_enqueue_script( 'frontendScripts', plugin_scripts . 'frontend_scripts.js' );
		}
	}
}

/* Instantiate the Store class */
$ObStore = new IllyrianClass();