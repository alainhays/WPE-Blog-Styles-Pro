<?php

/* Init custom styles */
function bsp_styles_helper() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
   	return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
	// check if WYSIWYG is enabled
	if ( get_user_option('rich_editing') == 'true') {
		add_filter( 'mce_css', 'wpe_bsp_add_editor_styles' );
	}

}
add_action('admin_head', 'bsp_styles_helper');


/**
 * Registers the stylesheet with the visual editor
 */
function wpe_bsp_add_editor_styles( $mce_css ) {

    if ( ! empty( $mce_css ) )
        $mce_css .= ',';

    $mce_css .= WPE_BSP_SHARED_DATA . '/style.css' ;

    return $mce_css;

}

/* Custom side menu that handles inserting code for BSP */
function wpe_bsp_get_all_classes(){

    $data = file_get_contents(WPE_BSP_SHARED_DATA_DIR . "admin-config.json");
    $data = json_decode($data, TRUE); 
	$class_total = count($data);
	?>
	<div class="bsp-popup-cover">
	<div class="bsp-popup-main ext">
	<div class="bsp-popup-title-bar"> 
	<div class="bsp-popup-title"> Easy Blog Styles Insert </div>
	<div class="bsp-popup-close"><b>X</b></div>
	</div>
	<div class="bsp-popup-body">
	<div class="bsp-popup-label"> Select one or more styles: </div>
	<div style="padding-top:5px;">
		<select id="wpe-bsp-select-classes" multiple="multiple">
		<?php
			for($i = 0; $i < $class_total; $i++){
			echo '<option value="'.$data[$i].'">'.str_replace(array("-", "bsp"), " ", $data[$i]).'</option>';
			}

		?>
		</select>
	</div>
	<div id="bsp-select-desc">We will add the proper code to your blog post.</div>
	</div>
	<div class="pop-btn-bar">
	<button class="bsp-popup-cancel pop-btn">Cancel</button>
	<button class="bsp-popup-confirm pop-btn"> Insert </button>
	</div>
	</div>
	</div>
	<?php

	wp_die();
}
add_action( 'wp_ajax_wpe_bsp_get_all_classes', 'wpe_bsp_get_all_classes' );


/* Add custom Interfaces in admin posts */
function wpe_bsp_add_to_post_interface(){
	$screens = array( 'post' );
	foreach ( $screens as $screen ) {
		add_meta_box( 
		'wpe-qi-box',
		'Blog Styles Pro Quick Insert',
		'wpe_bsp_quick_insert_menu',
		$screen,
		'side',
		'high'
        );
	}
}
add_action( 'add_meta_boxes', 'wpe_bsp_add_to_post_interface' );


/* Custom side menu that handles inserting code for BSP */
function wpe_bsp_quick_insert_menu( $post ) {
		
    $data = file_get_contents(WPE_BSP_SHARED_DATA_DIR . "admin-config.json");
    $data = json_decode($data, TRUE); 
	$class_total = count($data);
	?>

	<div> Select one or more styles: </div>
	<div style="padding-top:5px;">
		<select id="wpe-bsp-select-classes" multiple="multiple">
		<?php
			for($i = 0; $i < $class_total; $i++){
			echo '<option value="'.$data[$i].'">'.str_replace(array("-", "bsp"), " ", $data[$i]).'</option>';
			}

		?>
		</select>
	</div>
	<div> Select a base HTML element: </div>
	<div style="padding-top:5px;">
		<select id="wpe-bsp-select-directive">
		<option value="<div class=*xxx>***</div>" selected="selected">div - Basic block container </option>
		<option value="<span class=*xxx>***</span>">span - Basic inline container </option>
		<option value="<p class=*xxx>***</p>">p - Paragraph container </option>
		<option value="class=*yyy">class - Wrapped in class attribute </option>
		<option value="*zzz">INSERT STYLES ONLY</option>
		<option value="<h1 class=*xxx>***</h1>">h1 - Header tag largest </option>
		<option value="<h2 class=*xxx>***</h2>">h2 - Header tag larger </option>
		<option value="<h3 class=*xxx>***</h3>">h3 - Header tag medium </option>
		<option value="<h4 class=*xxx>***</h4>">h4 - Header tag small </option>
		<option value="<h5 class=*xxx>***</h5>">h5 - Header tag smallest </option>
		</select>
	</div>
	
	<div id="bsp-insert-code"> Insert </div>

<script>

/*
 * jQuery plugin: fieldSelection - v0.1.1 - last change: 2006-12-16
 * (c) 2006 Alex Brem <alex@0xab.cd> - http://blog.0xab.cd
 */
(function(){var fieldSelection={getSelection:function(){var e=(this.jquery)?this[0]:this;return(('selectionStart'in e&&function(){var l=e.selectionEnd-e.selectionStart;return{start:e.selectionStart,end:e.selectionEnd,length:l,text:e.value.substr(e.selectionStart,l)}})||(document.selection&&function(){e.focus();var r=document.selection.createRange();if(r===null){return{start:0,end:e.value.length,length:0}}var re=e.createTextRange();var rc=re.duplicate();re.moveToBookmark(r.getBookmark());rc.setEndPoint('EndToStart',re);return{start:rc.text.length,end:rc.text.length+r.text.length,length:r.text.length,text:r.text}})||function(){return null})()},replaceSelection:function(){var e=(typeof this.id=='function')?this.get(0):this;var text=arguments[0]||'';return(('selectionStart'in e&&function(){e.value=e.value.substr(0,e.selectionStart)+text+e.value.substr(e.selectionEnd,e.value.length);return this})||(document.selection&&function(){e.focus();document.selection.createRange().text=text;return this})||function(){e.value+=text;return jQuery(e)})()}};jQuery.each(fieldSelection,function(i){jQuery.fn[i]=this})})();

jQuery( document ).ready(function($) {

var addAcitons = function(){
	var cSelectBoxClasses, cSelectBoxDirective;
    $.post(ajaxurl, { action : 'wpe_bsp_get_all_classes'}, function(data) {
		/* Init Slecet 2 Box */
		cSelectBoxClasses = $("#wpe-bsp-select-classes");
		cSelectBoxClasses.select2();
	    cSelectBoxDirective = $("#wpe-bsp-select-directive");
		cSelectBoxDirective.select2();
	}); // Ajax  

	/* Create style of File */
	$("#bsp-insert-code").click(function(){

		/* Get data from quick inserts menu */
		var classes = cSelectBoxClasses.val().join(" ");
		var dirTags = cSelectBoxDirective.val().split("***");

	    if( $('#content').is(':visible') ) { /* Text editor */

			var currentSelection = $('#content').getSelection();
			var textContent = $('#content').val();
			var partOne = textContent.slice(0, currentSelection.start);
			var partTwo = textContent.slice(currentSelection.end);
			if(dirTags[0].indexOf('*xxx') !== -1){
				/* xxx = inside quotes */
				$('#content').val(partOne + dirTags[0].replace('*xxx', '"'+ classes +'"') + currentSelection.text + dirTags[1] + partTwo);
			}
			else if(dirTags[0].indexOf('*yyy') !== -1){
				/* yyy = wrapped in class tag */
				$('#content').val(partOne + dirTags[0].replace('*yyy', '"'+ classes +'"' + partTwo));
			} 
			else if(dirTags[0].indexOf('*zzz') !== -1){
				/* zzz = only classes */
				$('#content').val(partOne + dirTags[0].replace('*zzz', " " + classes + partTwo));
			}

		} else {  /* Visual editor */

		var editor = tinyMCE.activeEditor;
		/* xxx = inside quotes */
		var content = dirTags[0].replace('*xxx', '"'+ classes +'"') + editor.selection.getContent() + dirTags[1];
		editor.insertContent(content);

		}

	}); 
	   
}
addAcitons();
});	  /* Document ready */
</script>


        <?php
}
