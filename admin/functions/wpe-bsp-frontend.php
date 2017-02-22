<?php
/* Front-end actions & filters */
class blog_styles_pro {
	/* Main Equeue of final stylesheet */
	public function final_enqueue() {
		wp_enqueue_style( 'BSP-blogging-styles', WPE_BSP_SHARED_DATA . '/style.css', '', false );
	}
	public function load_data_options(){

	}

	/* Remove All (P)aragraph and Line (BR)eak tags from Post body, great defense against WordPress Auto tags  */
	public function filter_empty_auto_tags($content){
		global $wpe_bsp_data_options;
	    if($wpe_bsp_data_options["remove-auto-p-tags"] > 0){
			$content = str_replace(array("<p>", "</p>", "<br>"), "", $content);
		}
		return $content;
	} 

}

add_action( 'wp_enqueue_scripts', array('blog_styles_pro', 'final_enqueue') );
add_filter( 'the_content', array('blog_styles_pro', 'filter_empty_auto_tags'), 50 );