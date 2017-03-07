<?php
/**
 * Plugin Name: WPE Blog Styles Pro
 * Plugin URI: https://wpexpanse.com/wpe-blog-styles-pro/
 * Description: WPE Blog Styles Pro is a very simple, yet powerful stylesheet manager. Give your blog a consistent, elegant, and proffesional design.
 * Author: WP Expanse
 * Version: 1.1.1
 * Copyright 2017 WP Expanse - Contact us at https://wpexpanse.com
 *
 * @package WPE Blog Styles Pro
 */


/**  Main Blog Style's Class that encompasses all it's functionality */
class WPEXPANSE_Blog_Styles_Pro {

	/**
	 * All the plugin specific Data
	 * Static data - Define all important magic strings
	 *
	 * @since 1.1.0
	 */
	static $plugin_data = null;

	/**
	 * Database Options
	 * Private data - Settings stored in the database
	 *
	 * @since 1.1.1
	 */
	private $db_options = null;

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
	 * @return WPEXPANSE_Blog_Styles_Pro
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


		// Load settings form the database
		$db_get_options = get_option("wpe-bsp", "remove-auto-p-tags||1||default-template||default");
		$db_get_options = explode("||", $wpe_bsp_data_local);
		for ($i=0; $i < count($db_get_options); $i+=2) { 

			$this->db_options[$db_get_options[$i]] = $db_get_options[$i+1];

		}


		// If WPE Core Dependencies not found then accuire them from GitHub
		if ( !file_exists( self::$plugin_data['shared-dir'] . self::$plugin_data['shared-core'] ) ){

			// Create Shared Folder if doesn't exists
			if(!file_exists(self::$plugin_data['shared-dir'])){
				mkdir(self::$plugin_data['shared-dir'], 744);
			}
			// Download
			$zip_file = file_get_contents('https://github.com/wpexpanse/WPE-Core/archive/master.zip');
			file_put_contents(self::$plugin_data['shared-dir']."/wpe-core.zip", $zip_file);
			// Unzip
			$zip = new ZipArchive;
			$wpe_core = $zip->open(self::$plugin_data['shared-dir']."/wpe-core.zip");
			if ( $wpe_core === TRUE ) {
				$zip->extractTo( self::$plugin_data['shared-dir'] . '/' );
				$zip->close();
				// rename and remove zipfile
				rename(self::$plugin_data['shared-dir']."/WPE-Core-master", self::$plugin_data['shared-dir'] . self::$plugin_data['shared-core']);
				unlink(self::$plugin_data['shared-dir']."/wpe-core.zip");
			} else { 
				echo '<div style="padding:20px;font-size:20px"> Error core was not installed! Please reinstall or check the permissions of the plugins folder.</div>';
			}

		}


		// Require Core Dependencies 
		if(file_exists(self::$plugin_data['shared-dir'].self::$plugin_data['shared-core']."wpe-shared-helpers.php")){

			// Core Library Includes
			$shared_dependency = array(
				WPEXPANSE_shared_helpers => "wpe-shared-helpers.php",
				WPEXPANSE_BSP_ui => "wpe-shared-ui.php",
				Less_Parser => "3pl/lessphp/Less.php"		
			);
			// Be sure to check to make sure they don't already exist
			foreach ($shared_dependency as $dep_class => $dep_path) {
				if(!class_exists($dep_class)){
					require_once self::$plugin_data['shared-dir'] . self::$plugin_data['shared-core'] . $dep_path;
				}
			}

		}

		// BSP Classes & Class Extensions
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
		if(!file_exists(self::$plugin_data['shared-bsp-dir'] . "version.txt")){
			mkdir(self::$plugin_data['shared-bsp-dir'], 0744, true);
			// Copy Library Updates over to the Shared Data 
			self::$helpers->copy_recursive(self::$plugin_data['library-dir'], self::$plugin_data['shared-bsp-dir']);
		} else {
			// If Library versions don't match then update Shared Data 
			if(file_get_contents(self::$plugin_data['shared-bsp-dir'] . "version.txt") != file_get_contents(self::$plugin_data['library-root'] . "version.txt")){
				self::$helpers->copy_recursive(self::$plugin_data['library-dir'], self::$plugin_data['shared-dir']);
			}
		}

		// Register the activation hook
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// Register the deactivation hook
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

	}

	/**
	* Return options by name from database
	*
	*
	* @since  1.1.1
	* @return WPEXPANSE_Blog_Styles_Pro
	*/
	static public function get_data( $data_name ){
		return esc_html($this->db_options[$data_name]);
	}

	/**
	* Set options by name from database
	*
	*
	* @since  1.1.1
	* @return WPEXPANSE_Blog_Styles_Pro
	*/
	static public function set_data( $data_name, $value ){
		$this->db_options[sanitize_text_field($data_name)] = sanitize_text_field($value);
		$this->save_data();
	}

	/**
	* Update the data in the database
	*
	*
	* @since  1.1.1
	* @return WPEXPANSE_Blog_Styles_Pro
	*/
	static private function save_data(){
		
		$db_set_options = null;
		$count = 0;
		foreach($this->db_options as $key => $value) { 

			$db_set_options[$count] = $key;
			$db_set_options[$count+1] = $value;
			$count++;

		}
		implode("||", $db_set_options);
		set_option("wpe-bsp", $db_set_options);

	}

	/**
	* The plugin activation function 
	*
	*
	* @since  1.1.0
	* @return WPEXPANSE_Blog_Styles_Pro
	*/
	public function activate(){

	}

	/**
	* The plugin activation function 
	*
	*
	* @since  1.1.0
	* @return WPEXPANSE_Blog_Styles_Pro
	*/
	public function deactivate(){

	}

}

// Initialize Blog Styles Pro
WPEXPANSE_Blog_Styles_Pro::init();

?>