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

	//tinymce.forced_root_block = "";

    
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
		onclick: function(event){
			var node = editor.selection.getNode();
			var elementList = editor.dom.getParents(node);
	        for(var i = 0; i < elementList.length; i++){
				if(editor.dom.hasClass(elementList[i], 'BSP' ) ){	
					editor.selection.select(elementList[i]);
					node = editor.selection.getNode();
					// Grab Content and split off top most container
					var thisContent = editor.selection.getContent({format: 'html'}).split(/>(.+)/)[1];
					editor.selection.setContent(thisContent); 
				}
			}
			/* Remove Button When finished */
			this._parent._parent._parent.remove();
			this._parent._parent.remove();
			this.remove();
			
		}
	});
	
	/* Creates a toolbar */
	if (toolbar) {
		editor.on('wptoolbar', function (event) {
			var elementList = event.parents;
			//console.log(event.parents);
	        for(var i = 0; i < elementList.length; i++){
				//if (editor.dom.hasClass(elementList[i], 'BSP')) {
				//	var thisElementsClasses = elementList[i];
					
				//if(thisElementsClasses.indexOf("BSP") > -1){
				   if (editor.dom.hasClass(elementList[i], 'BSP')) {
					//editor.selection.setNode(elementList[i]);
					// TODO: Add the edit funcitonality 'bsp-edit',
                    //console.log(elementList[i]);
					event.element = elementList[i]; 
					event.toolbar = editor.wp._createToolbar(['bsp-remove']);
				 }
			}

		});
	}
    /* Refresh toolbar onChange  
    editor.on('change', function(event) {
        if (editor.dom.hasClass(event.element, 'BSP')) {
             // TODO: Add the edit funcitonality 'bsp-edit',
            event.toolbar = editor.wp._createToolbar(['bsp-remove']);
        }
    });  */

}


/*
jQuery( document ).ready(function($) { 

		$('.BSP').hover(function(){
			if(!$(this + " .bsp-edit-btn").length){
			$(this).append("<div class='bsp-edit-btn'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> </div>");
			}
		});

});*/