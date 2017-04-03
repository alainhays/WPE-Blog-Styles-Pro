<?php
/**
 * Plugin Name: WPE Blog Styles Pro
 * Plugin URI: https://wpexpanse.com/wpe-blog-styles-pro/
 * Description: WPE Blog Styles Pro is a very simple, yet powerful stylesheet manager. Give your blog a consistent, elegant, and proffesional design.
 * Author: WP Expanse
 * Version: 1.1.4
 * Copyright 2017 WP Expanse - Contact us at https://wpexpanse.com
 *
 * @package WPE Blog Styles Pro
 */


/**  Main Blog Style's Class that encompasses all it's functionality */
class WPEXPANSE_Blog_Styles_Pro {

	/**
	 * All the plugin specific Data
	 *
	 * @var Static data - Define all important magic strings
	 * @since 1.1.0
	 */
	static $plugin_data = null;

	/**
	 * Backend admin initial functions
	 *
	 * @var  WEXPANSE_BSP_Admin_Init()
	 * @since 1.1.3
	 */
	 static $admin_init = null;

	/**
	 * Backend admin functions
	 *
	 * @var  WEXPANSE_BSP_Admin_Functions()
	 * @since 1.1.0
	 */
	 static $admin_functions = null;

	/**
	 * Frontend accessible functions
	 *
	 * @var  WEXPANSE_BSP_Functions()
	 * @since 1.1.0
	 */
	 static $functions = null;

	/**
	 * Thridpary LESS Compiler Library
	 *
	 * @var  Less_Parser()
	 * @since 1.1.0
	 */
	 static $less = null;

	/**
	 * PHP Helpers
	 * Extended from shared helpers
	 *
	 * @var  WPEXPANSE_Shared_Helpers()
	 * @since 1.1.0
	 */
	 static $helpers = null;

	/**
	 * User interface
	 * Extended from Shared UI
	 *
	 * @var  WPEXPANSE_BSP_UI()
	 * @since 1.1.0
	 */
	 static $ui = null;

	/**
	 * Creates and returns the main object for this plugin
	 *
	 *
	 * @since  1.1.0
	 * @return WPEXPANSE_Blog_Styles_Pro
	 */
	static public function init() {

		static $instance = null;
		if ( null === $instance ) {
			$instance = new WPEXPANSE_Blog_Styles_Pro();
		}

		return $instance;
	}

	/**
	 * Main Constructor that sets up all static data associated with this plugin.
	 *
	 *
	 * @since  1.1.0
	 *
	 */
	private function __construct() {

		// Setup static plugin_data
		self::$plugin_data = array(
		"name"            => "WPE Blog Styles Pro",
		"slug"            => "blog-styles-pro-menu",
		"version"         => "1.0.1",
		"author"          => "WP Expanse",
		"description"     => "WPE Blog Styles Pro is a very simple, yet powerful stylesheet manager. Give your blog a consistent, elegant, and proffesional design.",
		"logo"            => plugins_url( 'images/logo.png', __FILE__ ),
		"url-author"      => "http://wpexpanse.com/",
		"url-blog"        => "http://wpexpanse.com/blog/",
		"url-main"        => "http://wpexpanse.com/wpe-blog-styles-pro/",
		"url-docs"        => "http://wpexpanse.com/wpe-blog-styles-pro-documentation/",
		"this-root"       => plugins_url( '', __FILE__ )."/",
		"this-dir"        => plugin_dir_path( __FILE__ ),
		"shared-root"     => plugins_url()."/wpe-shared-data/",
		"shared-dir"      => plugin_dir_path( __DIR__ )."wpe-shared-data/",
		"shared-bsp-root" => plugins_url().'/wpe-shared-data/wpe-blog-styles/',
		"shared-bsp-dir"  => plugin_dir_path( __DIR__ )."wpe-shared-data/wpe-blog-styles/",
		"shared-core"     => "wpe-core/",
		"library-root"    => plugins_url( '/library/', __FILE__ ),
		"library-dir"     => plugin_dir_path( __FILE__ )."library/"
		);


		/* Initiate the BSP main Init class First */
		require_once 'includes/wpe-bsp-admin-init.php';
		self::$admin_init = new WEXPANSE_BSP_Admin_Init();

		/* Initiate WPE CORE */
        self::$admin_init::init_wpe_core();

		// BSP Core Classes & Class Extensions
		require_once 'includes/wpe-bsp-admin-functions.php';
		require_once 'includes/wpe-bsp-functions.php';
		require_once 'includes/wpe-bsp-ui.php';
		
		// Initiate Classes
		self::$admin_functions = new WEXPANSE_BSP_Admin_Functions();
		self::$functions = new WEXPANSE_BSP_Functions();
		self::$less = new Less_Parser(array( 'compress'=>true ));
		self::$helpers = new WPEXPANSE_Shared_Helpers();
		self::$ui = new WPEXPANSE_BSP_UI();

		// If BSP theme Library out of date or doesn't exist then create it
		self::$admin_init->detect_library_version();

		// Register the activation hook
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// Register the deactivation hook
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

	}

	/**
	* The plugin activation function 
	*
	*
	* @since  1.1.0
	*/
	public function activate(){

	}

	/**
	* The plugin deactivation function 
	*
	*
	* @since  1.1.0
	*/
	public function deactivate(){

	}

}

// Include database Management
require_once 'includes/wpe-bsp-db.php';

// Initialize Blog Styles Pro
WPEXPANSE_Blog_Styles_Pro::init();

?>