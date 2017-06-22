
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
			cSelectBoxDirective = $("#wpe-bsp-select-directive");
			cSelectBoxClasses.select22();
			cSelectBoxDirective.select22();
			
		}); // Ajax  

		/* Create style of File */
		$("#bsp-insert-code").click(function(){

			/* Get data from quick inserts menu */
			var classes = "BSP "+cSelectBoxClasses.val().join(" ");
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
		
		$('.BSP').hover(function(){
			if(!$(this + " .bsp-edit-btn").length){
			$(this).append("<div class='bsp-edit-btn'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> </div>");
			}
		});

	}

	addAcitons();


	});	  /* Document ready */