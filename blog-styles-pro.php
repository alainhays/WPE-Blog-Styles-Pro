<?php
/*
Plugin Name: WPE Blog Styles Pro
Plugin URI: https://wpexpanse.com/wpe-blog-styles-pro/
Description: WPE Blog Styles Pro is a very simple, yet powerful stylesheet manager. Give your blog a consistent, elegant, and proffesional design.
Author: WP Expanse
Version: 1.0.0
Copyright 2017 WP Expanse - Contact us at https://wpexpanse.com

*/

 /* Quit if path is undefined */
if (!defined('ABSPATH')) {
    die();
}

/* Abort if WordPress is upgrading */
if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;
	
/* Define plugin path */
define ( 'WPE_BLOG_STYLES_PRO_DIR', plugin_dir_path( __FILE__ ));
define ( 'WPE_BLOG_STYLES_PRO_ROOT', plugins_url( '', __FILE__ ));
define ( 'WPE_BSP_LOGO', plugins_url( 'images/logo.png', __FILE__ ));
define ( 'WPE_BSP_LIBRARY', plugins_url( '/library/', __FILE__ ));
define ( 'WPE_BSP_LIBRARY_DIR', WPE_BLOG_STYLES_PRO_DIR.'library')."/";
define ( 'WPE_BSP_SHARED_DATA', plugins_url()."/wpe-shared-data/wpe-blog-styles/" );
define ( 'WPE_BSP_SHARED_DATA_DIR', plugin_dir_path( __DIR__ )."wpe-shared-data/wpe-blog-styles/" );


/* Define later in the lifecycle */
function BSP_get_extra_definitions() {
    $plugin_version_data = get_plugin_data( __FILE__ );
    define( 'WPE_BSP_VERSION', $plugin_version_data['Version']);
    define( 'WPE_BSP_AUTHOR', $plugin_version_data['Author']);
	define( 'WPE_BSP_DESC', $plugin_version_data['Description']);
	define( 'WPE_BSP_AUTHOR_URL', "http://wpexpanse.com/");
    define( 'WPE_BSP_MAIN_URL', "http://wpexpanse.com/wpe-blog-styles-pro");
    define( 'WPE_BSP_DOCS_URL', "http://wpexpanse.com/wpe-blog-styles-pro-documentation");
    define( 'WPE_BSP_BLOG_URL', "http://wpexpanse.com/blog/");
	define( 'WPE_BSP_ALL_DATA', 
	"{".
	 '"name"         : "' . 'WPE Blog Styles Pro' . '", ' .
	 '"slug"         : "' . 'blog-styles-pro-menu'. '", ' .
	 '"version"      : "' . WPE_BSP_VERSION    	  . '", ' .
	 '"description"  : "' . WPE_BSP_DESC          . '", ' .
	 '"author"       : "' . WPE_BSP_AUTHOR        . '", ' .
	 '"logo"         : "' . WPE_BSP_LOGO          . '", ' .
	 '"url-author"   : "' . WPE_BSP_AUTHOR_URL    . '", ' .
	 '"url-blog"     : "' . WPE_BSP_BLOG_URL      . '", ' .
	 '"url-main"     : "' . WPE_BSP_MAIN_URL      . '", ' .
	 '"url-docs"     : "' . WPE_BSP_DOCS_URL      . '"  ' .
	" }"
	);
}
add_action('admin_init', 'BSP_get_extra_definitions');


/* Load settings form the database */
global $wpe_bsp_data_options;
$wpe_bsp_data_local = get_option("wpe-bsp", "remove-auto-p-tags:-@-:1");
$wpe_bsp_data_local = explode(":-@-:", $wpe_bsp_data_local);
for ($i=0; $i < count($wpe_bsp_data_local); $i+=2) { 
	$wpe_bsp_data_options[$wpe_bsp_data_local[$i]] = $wpe_bsp_data_local[$i+1];
}

/* REQUIRE PLUGIN DEPENDANCIES */
/* Main Admin Page */
require_once( WPE_BLOG_STYLES_PRO_DIR . 'admin/functions/wpe-portable-helpers.php' );
require_once( WPE_BLOG_STYLES_PRO_DIR . 'admin/main.php' );
require_once( WPE_BLOG_STYLES_PRO_DIR . 'extends-wp-editor/posting-tag-helper.php' );
require_once( WPE_BLOG_STYLES_PRO_DIR . 'admin/functions/wpe-bsp-frontend.php' );


/* Setup LESS Compiler */
if(!class_exists(Less_Parser)){
	require_once(WPE_BLOG_STYLES_PRO_DIR .'admin/functions/lessphp/Less.php');
}
global $WPE_BSP_less;
$WPE_BSP_less_options = array( 'compress'=>true );
$WPE_BSP_less = new Less_Parser($WPE_BSP_less_options);


/* Library version doesn't exists */
if(!file_exists(WPE_BSP_SHARED_DATA_DIR . "version.txt")){
		global $WPE_helpers;
		mkdir(WPE_BSP_SHARED_DATA_DIR, 0744, true);
		/* Copy Library Updates over to the Shared Data */
		$WPE_helpers->copy_recursive(WPE_BSP_LIBRARY_DIR, WPE_BSP_SHARED_DATA_DIR);
} else {
	/* If Library versions don't match then update Shared Data */
	if(file_get_contents(WPE_BSP_SHARED_DATA_DIR . "version.txt") != file_get_contents(WPE_BSP_LIBRARY . "version.txt")){
		$WPE_helpers->copy_recursive(WPE_BSP_LIBRARY_DIR, WPE_BSP_SHARED_DATA_DIR);
	}
}


/* Activate BSP */
function BLOG_STYLES_PRO_activate(){
}

/* Deactivate BSP */
function BLOG_STYLES_PRO_deactivate() {

}

register_activation_hook( __FILE__, 'BLOG_STYLES_PRO_activate' );
register_deactivation_hook( __FILE__, 'BLOG_STYLES_PRO_deactivate' );
?>