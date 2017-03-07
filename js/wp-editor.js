/* *** Include inside the tiny MCE for direct manipulation *** */

/*
tinyMCE.init({
   setup:function(ed) {
       ed.on('change', function(e) {
           console.log('the event object ', e);
           console.log('the editor object ', ed);
           console.log('the content ', ed.getContent());
       });
   }
}); */

// JavaScript Document
jQuery( document ).on( 'tinymce-editor-init', function( event, editor ) {

    addInlineEditorButtons($, event, editor );

    
});
	
var addInlineEditorButtons = function($, event, editor){
	editor.addButton('bsp-edit', {
		title: 'Edit BSP ELement',
		classes: 'bsp-edit',
		onclick: function(e){
			var node = editor.selection.getNode();
			//var viewType = editor.dom.getAttrib( node, 'data-wpview-type' )
			if( editor.dom.hasClass( node, 'BSP' ) ){
				console.log(node);
				//this is where you add code to do something
			}
		}
	});

	editor.addButton('bsp-remove', {
		title: 'Remove BSP ELement',
		classes: 'bsp-remove',
		onclick: function(e){
			var node = editor.selection.getNode();
			if(editor.dom.hasClass( node, 'BSP' ) ){
                var thisSelection = editor.selection.getSel();
                editor.dom.remove(node);
                editor.selection.setContent(thisSelection.focusNode.textContent);
			}
		}
	});
	
	/* Creates a toolbar */
	if (toolbar) {
		editor.on('wptoolbar', function (event) {
			if (editor.dom.hasClass(event.element, 'BSP')) {
                 // TODO: Add the edit funcitonality 'bsp-edit',
				event.toolbar = editor.wp._createToolbar(['bsp-remove']);
			}
		});
	}
    /* Refresh toolbar onChange */  
    editor.on('change', function(event) {
        if (editor.dom.hasClass(event.element, 'BSP')) {
             // TODO: Add the edit funcitonality 'bsp-edit',
            event.toolbar = editor.wp._createToolbar(['bsp-remove']);
        }
    }); 

}


/*
jQuery( document ).ready(function($) { 

		$('.BSP').hover(function(){
			if(!$(this + " .bsp-edit-btn").length){
			$(this).append("<div class='bsp-edit-btn'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> </div>");
			}
		});

});*/