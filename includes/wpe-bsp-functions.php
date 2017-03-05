<?php
/* Front-end actions & filters */
class WEXPANSE_BSP_Functions {

	public function __construct(){
		add_action( 'wp_enqueue_scripts', array($this, 'final_enqueue') );
		add_filter( 'the_content', array($this, 'filter_empty_auto_tags'), 50 );
	}
	
	/* Main Equeue of final stylesheet */
	public function final_enqueue() {
		wp_enqueue_style( 'BSP-blogging-styles', WPE_BSP_SHARED_DATA . '/style.css', '', false );
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