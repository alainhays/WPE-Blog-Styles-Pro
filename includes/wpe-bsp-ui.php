<?php
/**
 * Blog Styles Pro UI which extends the shared UI class in the core
 *
 * @package WPE Blog Styles Pro
 */
class WPEXPANSE_BSP_UI extends WPEXPANSE_Shared_UI {

	/**
	 * Post Types available
	 *
	 * @var  An array of all the get_post_types()
	 * @since 1.1.2
	 */
	private $post_types = NUll;

	public function __construct(){
		if( is_admin()){
			// UI actions
			$UI_Actions = array(
				'admin_menu' => "init_admin_menu_item", 
				'admin_enqueue_scripts' => "bsp_admin_enqueue", 
				'admin_head' => "posts_visual_editor_options", 
				'add_meta_boxes' => "wpe_bsp_add_to_post_interface"
				);
			foreach ($UI_Actions as $hook => $function) { 
				add_action($hook , array( $this, $function ) );
			}
		}
	}

	/**
	 * Admin Enqueue -  Which encompasss all scripts and styles for the wp-admin areas
	 *
	 * @since 1.1.0
	 */
	public function bsp_admin_enqueue($hook) {

		// load in entire admin area 
		wp_enqueue_style(  'BSP-admin-styles', WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-root"].'style.css', '', false );
		// Select 2 boxes 
		wp_enqueue_style( 'select-2-styles', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css', '', false );
		wp_enqueue_script( 'select-2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js' );
		wp_enqueue_script( 'BSP-admin-post-page-edit', WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-root"].'js/wp-post-page-tools.js' );
		wp_enqueue_style(  'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', '', false );
		wp_enqueue_style(  'BSP-edit', WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-root"] . 'bsp-edit.css', array('font-awesome'), false );
		 // If its not the main post page then exit now
		if ( 'toplevel_page_blog-styles-pro-menu' != $hook && 'wpe-dashboard_page_blog-styles-pro-menu' != $hook ) {
			return;
		}
		// Only if it exists on the admin main page 
		wp_enqueue_media();
		wp_enqueue_script( 'toastr', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js' );
		wp_enqueue_style(  'toastr-style', "https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css", '', false );
		wp_enqueue_script( 'BSP-admin-config', WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-root"].'js/config.js' );
		wp_enqueue_script( 'BSP-admin-script', WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-root"].'js/functions.js', array('jquery', 'toastr', 'media-upload'));
		// Ace Code editor
		wp_enqueue_script( 'ace-code-editor', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ace.js' );
		wp_enqueue_script( 'ace-code-editor-mode-less', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-less.js' );
		wp_enqueue_script( 'ace-code-editor-bsp-theme', WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-root"].'js/theme-custom.js' );
		wp_enqueue_script( 'ace-code-editor-autocomplete', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ext-language_tools.js' );

	}

	
	/**
	 * Attach Javascript to the TinyMCE plug-in(s)
	 *
	 * @since 1.1.2
	 */
	public function add_tinymce_plugin($plugin_array) {
		$plugin_array['extra_inline_buttons'] = WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-root"]."js/wp-editor.js";
		return $plugin_array;
	}

	/* Attach BSP dashboard to the admin menu or inside the dashboard if it exists
	 *
	 * @since 1.0.0
	 */
	public function init_admin_menu_item() {
	if(!class_exists("WPEXPANSE_Dashboard")){
		$icon_url = plugins_url('../images/icon.png', __FILE__);
		add_menu_page('Blog Styles Pro', 'Blog Styles Pro', 'administrator', 'blog-styles-pro-menu', array($this, 'init_admin_page'), $icon_url);
	} else {
		add_submenu_page( "wpe-dashboard", "Blog Styles Pro", "Blog Styles Pro", 'administrator', 'blog-styles-pro-menu', array($this, 'init_admin_page') );
	}
	}
	
	/* Renders the_ID() BSP admin page
	 *
	 * @since 1.0.0
	 */  
	public function init_admin_page() {
		$this->print_header();
		$this->load_template( WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-dir"].'templates/admin-area' );
	}

	public function print_header() {
		$as_json = defined( 'DOING_AJAX' ) && DOING_AJAX;
		if ( $as_json ) { ob_start(); }
		
			if(file_exists(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-dir"].WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-core"]."wpe-shared-ui.php")){
				$this->wpexpanse_master_header(WPEXPANSE_Blog_Styles_Pro::$plugin_data);
			} else {
				$this->load_template( WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-dir"].'templates/no-header' );
			} 

		if ( $as_json ) {
			$code = ob_get_clean();
			wp_send_json_success( array( 'html' => $code ) );
		}
	}

		
	/* Add custom styles to admin visual editor
	 *
	 * @since 1.0.0
	 */  
	public function posts_visual_editor_options() {
		// check user permissions
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
			return;
		}
		
		// check if WYSIWYG is enabled
		if ( get_user_option('rich_editing') == 'true') {
			add_filter( 'mce_css', array($this, 'wpe_bsp_add_editor_styles' ) );
			add_filter( 'mce_external_plugins', array( $this,'add_tinymce_plugin' ) );
		}
	}

	/**
	* Registers the stylesheet with the visual editor
	*/
	public function wpe_bsp_add_editor_styles( $mce_css ) {

		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}
		$mce_css .= WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-root"] . 'style.css, ' ;
		$mce_css .= WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-root"] . 'bsp-edit.css' ;

		return $mce_css;
	}

	/* Add custom Interfaces in admin posts */
	public function wpe_bsp_add_to_post_interface(){
		$this->post_types = get_post_types(array('public'   => true), 'names');
		foreach ( $this->post_types as $screen ) {
			add_meta_box( 
			'wpe-qi-box',
			'Blog Styles Pro Quick Insert',
			array($this, 'wpe_bsp_quick_insert_menu'),
			$screen,
			'side',
			'high'
			);
		}
	}

	/* Custom side menu that handles inserting code for BSP */
	public function wpe_bsp_quick_insert_menu( $post ) {

		$data = file_get_contents(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . "admin-config.json");
		$data = json_decode($data, TRUE);
		$pepared_data = array("data" => $data, "class_total" => count($data));
		$this->load_template( WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-dir"].'templates/insert-menu', $pepared_data);

	}

}