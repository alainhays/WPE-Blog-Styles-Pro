
jQuery( document ).ready(function($) {
  
    ace.require("ace/ext/language_tools");
    var editor = ace.edit("BSP-main-editor");
    editor.setTheme("ace/theme/custom");
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true
    });

    editor.getSession().setMode("ace/mode/less");
    var dataTabs = {
        config : "" 
    };
/*  editor.resize(true);
    editor.scrollToLine(0, true, true, function () {});
    editor.gotoLine(0, 0, true); */


    /* load Initial LESS File */
    var init = function(){
    var startingDirectory = "default";
    var startingFile = "config";
    var path = startingDirectory+"/"+startingFile+".less";
    $("#bsp-loading-container").show();
    /* Load Advanced Options */
  /*  $.post(ajaxurl, {action:'bsp_load_less_file', filename: path}, function(data) {
        editor.getSession().setValue($.trim(data));
        dataTabs["less-config"] = $.trim(data);
        addTab(path, $.trim(data));
}); */
        editor.getSession().setValue('');
        $(".just-added-now").removeClass("just-added-now");
        LoadFolderStructure(startingDirectory, 'easy');
    }


    /* load files and fodlers */
    var LoadFolderStructure = function(directory, mode){
        $("#list-container").html('');
        $("#easy-folder-selector").html('');
        /* load less directory */
        $.post(ajaxurl, {action:'bsp_load_less_workshop', dir: directory}, function(data) {
            data = data.split("<!--**********-->");
            $("#list-container").append(data[0]);
            $("#list-container").prepend(data[1]); 
            $("#easy-folder-selector").prepend(data[1]);
            refreshButtonActions();
            $("#bsp-loading-container").hide();
            if(mode != "current"){
                setCurrentMode(mode);
            }
        });
    }

    /* Initialize all the buttons */
    var initButtonActions = function(){

        /* Save File Button */
        $("#save-active-file").click(function(){ 
            console.log("pressed");
            if(getCurrentMode() == "advanced"){
                var data = { 
                    mode: "advanced",
                    content: editor.getSession().getValue(),
                    path: $(".tab-active").attr("data-path")
                };
            } else {
                var data = { 
                    mode: "easy",
                    path: $(".list-folder-selector .select-part").val()
                };
            }
            console.log(getCurrentMode());
            console.log(data);
                $.post(ajaxurl, {action:'bsp_save_less_file', data: data}, function(message) {
                    console.log(message);
                    toastr.success(message);

                });     
        });

        /* Toggle mode */
        $("#bsp-advanced-mode, #bsp-easy-mode").click(function(){
            if(!$(this).hasClass("active")){
                toggleCurrentMode();
            }
        });

        /* Show tab Button content */
        $(".tab-item").click(function(){

            dataTabs[$(".tab-active").attr("data-content-id")] = editor.getSession().getValue();

            /* Add active selector */
            $(".tab-item").removeClass("tab-active");
            $(this).addClass("tab-active");

            /* Refresh the all attached button events */
            refreshButtonActions();
                
            /* Last but not least add to editor */
            editor.getSession().setValue(dataTabs[$(this).attr("data-content-id")]);

        });


         /* remove tabs */
        $(".tab-close").click(function(){
            var parentTab = $(this).parent();
            // Clear editor if this tab is active
            if(parentTab.hasClass("tab-active")){
                editor.getSession().setValue("");
            }
            deleteTab(parentTab.attr("data-path"));
        }); 


        /* If folder Selected then update file list */
        $(".list-folder-selector .select-part").change(function(){
            /* Get data and clear */
            var selectedDirectory = $(this).val();
            LoadFolderStructure(selectedDirectory, 'current');
        });

        
        /* If folder popu theme manager triggered */
        $(".list-folder-selector .creator-part").click(function(){
            /* Reload Folders and Files */
                $.post(ajaxurl, {action:'bsp_popup', reason: "create"}, function(data) {
                    $("body").append(data);
                    /* Close Popup */
                    $(".bsp-popup-close, .bsp-popup-cancel").click(function(){
                        $(".bsp-popup-cover").replaceWith("");
                    });
                    /* Change the description based on what option is selected */
                    $("#bsp-data-type").change(function(){
                        $('#bsp-select-desc').html($('option:selected', this).attr("data-desc"));
                    });
                    /* Create style of File */
                    $(".bsp-popup-confirm").click(function(){
                        var data = {
                            type: $('#bsp-data-type').val(),
                            name: $('#bsp-data-name').val(),
                            dir:  $(".list-folder-selector .select-part").val()
                        };
                        $.post(ajaxurl, {action:'bsp_run_file_action', data: data}, function(response) {
                            var response = JSON.parse(response);
                            if(response.message == "fail"){
                                toastr.error("Task has failed!");
                            } else {
                                toastr.success(response.message);
                            }
                            LoadFolderStructure(response.dir, 'current');
                        });
                    });
                });
        });


        /* Add a new tab Button & content */
        $(".list-item").click(function(){
            /* setup */
            var path = $(this).attr("data-path");
            var isList = $(this).hasClass("list-item");
            var thisElement = this;
            var addCheck = 1;
            /* !important save previous tabs session value */
            dataTabs[$(".tab-active").attr("data-content-id")] = editor.getSession().getValue();

            $.post(ajaxurl, {action:'bsp_load_less_file', filename: path}, function(data) {
                
                /* Is a list item then open new tab */
                if(isList){
                    addCheck = addTab(path);
                    if(addCheck == 1){
                    var thisElement = $(".just-added-now");
                        $(thisElement).removeClass("just-added-now");
                    }
                }
                if(addCheck == 1){
                    /* Add active selector */
                    $(".tab-item").removeClass("tab-active");
                    $(thisElement).addClass("tab-active");

                    /* Refresh the all attached button events */
                    refreshButtonActions();
                        
                    /* Last but not least add to editor */
                    editor.getSession().setValue($.trim(data));
                } else {
                    toastr.info("Already Open!");
                }
            });
        });

    }



    /********** Helper Functions **********/

    /* remove all buttons */
    var refreshButtonActions = function(){
        $(".tab-item, .list-item, .btn-elem, .select-part, .creator-part").unbind();
        initButtonActions();
    }

    /* Add tab to editor */
    var addTab = function(path){
        var name = path.split("/");
        var theme = path.split("/");
        name = name[name.length-1];
        theme = theme[theme.length-2];
        contentID = path.replace(".less", "").replace("/", "-");
        addCheck = 1;
        /* Check if already Open */
        $(".tab-item").each(function(){
            if($(this).attr("data-content-id") == contentID){
                addCheck = 0;
            }
        });
        /* if it checks out then open file in tabs */
        if(addCheck == 1){
            $("#tab-container").prepend(
            "<div class='tab-item tab-active just-added-now' data-path='"+path+"' "
            +" data-content-id='"+ contentID +"'><div class='folder-name'> "+theme+"</div>" 
           + name +
            " <div class='tab-close'><i class='fa fa-times'></i></div></div>");
        }
        return addCheck;
    }

    /* Remove tab from editor */
    var deleteTab = function(path){
        var name = path.split("/");
        name = name[name.length-1];
        var contentId = path.replace(".less", "").replace("/", "-");
        /* Remove Tab */
        $(".tab-item").each( function(){
            if($(this).attr("data-content-id") == contentId){
                $(this).replaceWith("");
            }
        });
        /* Remove session from the temp storage */
        for (var i = 0; i < dataTabs.length; i++) {
            if(dataTabs == contentId){
                dataTabs.splice(i,1);
            }
        }
    }

    /* Set current mode */
    var setCurrentMode = function(Mode){
        if(Mode == "easy"){ // Set to easy
            $('#bsp-advanced-mode').removeClass('active');
            $('#bsp-easy-mode').addClass('active');
            $('#bsp-advanced-container').css("left", "-3000px");
            $('#bsp-easy-container').css("left", "0px");
        } else { // Set to advanced
            $('#bsp-easy-mode').removeClass('active');
            $('#bsp-advanced-mode').addClass('active');
            $('#bsp-easy-container').css("left", "-3000px");
            $('#bsp-advanced-container').css("left", "-0px");
        }
    };

    /* Get current mode */
    var getCurrentMode = function(){
        if($("#bsp-easy-mode").hasClass('active')){
            return "easy";
        } else {
            return "advanced";
        }
    };

    /* Toggle current mode */
    var toggleCurrentMode = function(){
        if(getCurrentMode() == "easy"){
            setCurrentMode("advanced");
        } else {
           setCurrentMode("easy"); 
        }
    };


init(); /* Start the intalization */
});
