<?php
 if( is_admin()){


/* Graphical UI assign priority 25 so that is adds itself in the dashboards menu */
add_action("admin_menu", "blog_styles_pro", 25);

function blog_styles_pro_admin_enqueue($hook) {
    
    /* load in entire admin area */
    wp_enqueue_style(  'BSP-admin-styles', WPE_BLOG_STYLES_PRO_ROOT.'/style.css', '', false );
    /* Select 2 boxes */
    wp_enqueue_style( 'select-2-styles', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css', '', false );
    wp_enqueue_script( 'select-2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js' );
    
    if ( 'toplevel_page_blog-styles-pro-menu' != $hook && 'wpe-dashboard_page_blog-styles-pro-menu' != $hook ) { // Don't load unless needed
        return;
    }
    /* Only if it exists on the admin main page */
    wp_enqueue_media();
    wp_enqueue_style(  'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', '', false );
    wp_enqueue_script( 'toastr', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js' );
    wp_enqueue_style(  'toastr-style', "https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css", '', false );
    wp_enqueue_script( 'BSP-admin-config', WPE_BLOG_STYLES_PRO_ROOT.'/admin/js/config.js' );
    wp_enqueue_script( 'BSP-admin-script', WPE_BLOG_STYLES_PRO_ROOT.'/admin/js/functions.js', array('jquery', 'toastr', 'media-upload'));
    // Ace Code editor
    wp_enqueue_script( 'ace-code-editor', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ace.js' );
    wp_enqueue_script( 'ace-code-editor-mode-less', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-less.js' );
    wp_enqueue_script( 'ace-code-editor-bsp-theme', WPE_BLOG_STYLES_PRO_ROOT.'/admin/js/theme-custom.js' );
    wp_enqueue_script( 'ace-code-editor-autocomplete', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ext-language_tools.js' );
}
add_action( 'admin_enqueue_scripts', 'blog_styles_pro_admin_enqueue');


/* Add Menu */
function blog_styles_pro() {
    if(!function_exists("wpe_dash_get_extra_definitions")){
        $icon_url = plugins_url('../images/icon.png', __FILE__);
        add_menu_page('Blog Styles Pro', 'Blog Styles Pro', 'administrator', 'blog-styles-pro-menu', 'blog_styles_pro_dashboard', $icon_url);
    } else {
        add_submenu_page( "wpe-dashboard", "Blog Styles Pro", "Blog Styles Pro", 'administrator', 'blog-styles-pro-menu', 'blog_styles_pro_dashboard' );
    }
}
/* Add Dashboard */
function blog_styles_pro_dashboard() {
    /* Add Dashboard header if it exists */
    if(defined('WPE_DASHBOARD_MASTER_HEADER')){
        require_once(WPE_DASHBOARD_MASTER_HEADER);
        wpexpanse_master_header(WPE_BSP_ALL_DATA);
    } else {
        require_once(WPE_BLOG_STYLES_PRO_DIR . "includes/header-sub.php");
    }
?>
<div id="top-actions-bar">
<div id="save-active-file" class="btn-elem"><i class="fa fa-check"></i> Save </div>
<div class="flex-padding"></div>
<div id="bsp-advanced-mode" class="btn-elem"><i class="fa fa-code"></i> Advanced </div>
<div id="bsp-easy-mode" class="btn-elem active"><i class="fa fa-magic"></i> Easy </div>
</div>

<div id="bsp-admin-content">

<!-- Easy options -->
<div id="bsp-easy-container">
<div class="col-100">
<label> Select a style: </label><div id="easy-folder-selector"></div>
</div>
</div>

<!-- Advanced code editor options -->
<div id="bsp-advanced-container">
<div id="wpe-bsp-main">
<div id="tab-container"></div>
<div id="BSP-main-editor"></div>
</div>
<div id="list-container">
</div>
</div>

</div>

<!-- initial loading screen -->
<div id="bsp-loading-container">
<div id="bsp-loader-icon">
<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
</div>
</div>
<?php 
}


/* Load a Less file at a specific path */
function bsp_load_less_file() {	

        /* Prepare Data */
        $filename = WPE_BSP_SHARED_DATA_DIR . $_POST["filename"];

        /* Load this File */
        $data = file_get_contents($filename);

        /* Print return contents */
        echo $data;

        wp_die();

}

/* Save data to a Less file at a specific path */
function bsp_save_less_file() {	
        
        /* Prepare Data */
        global $WPE_BSP_less;
        if($_POST["data"]["mode"] == "advanced"){
            $filename = WPE_BSP_SHARED_DATA_DIR . $_POST["data"]["path"];
            $folderpath = explode("/", $_POST["data"]["path"]);
            $name = $folderpath[1];
            $folderpath = WPE_BSP_SHARED_DATA_DIR . $folderpath[0]."/";
            /* Save this File */
            file_put_contents($filename, str_replace("\'", "'", str_replace('\"', '"', $_POST["data"]["content"])));
            echo $name . " was saved & compiled!";
        } else {
            $folderpath = WPE_BSP_SHARED_DATA_DIR . $_POST["data"]["path"]."/";
            echo "settings saved & compiled!";
            /* TODO when I add options here then save them */
        }

        /* Compile the less file and output a CSS file */
        $WPE_BSP_less->parseFile($folderpath."style.less", $folderpath);
        $get_final_styles = $WPE_BSP_less->getCss();
        file_put_contents(WPE_BSP_SHARED_DATA_DIR . "style.css", $get_final_styles);


        /* Filter the css into usable data for workshop/admin-config.json which is used for post inserting */
        global $WPE_helpers;
        $get_container_total = explode("{", $get_final_styles);
        $get_container_total = explode(",", $get_container_total[0]);
        $container_total = count($get_container_total);
        $get_final_styles = $WPE_helpers->filter_out_everything_between_these_tags("{", "}", $get_final_styles);
        $get_final_styles = $WPE_helpers->filter_out_everything_between_these_tags("/*", "*/", $get_final_styles);
        $get_final_styles = str_replace(array(".type-post ", ",#tinymce.wp-editor"), "", $get_final_styles);
        $get_final_styles = explode(".", $get_final_styles);
        $final_computed_classes = array();
        $entry = 0;
        $total = count($get_final_styles);
        for ($i=0; $i < $total; $i++) { 
            if (strpos($get_final_styles[$i],'bsp-') !== false) {
                    $final_computed_classes[$entry] = trim($get_final_styles[$i]);
                    $entry ++;  
            }
        }
        $final_computed_classes = array_unique($final_computed_classes);
        $total = count($final_computed_classes)*$container_total;
        $admin_config = "[";
        for ($i=0; $i < $total*2; $i++) { 
            if(strlen($final_computed_classes[$i]) > 3){
                $admin_config .='"'. $final_computed_classes[$i].'"';
                if($i != ($total-$container_total)){
                   $admin_config .= ", ";
                }
            }
        }
        $admin_config .= "]";
        file_put_contents(WPE_BSP_SHARED_DATA_DIR . "admin-config.json", $admin_config);

        /* Should trigger a cache purge */
        do_action("switch_theme");

        wp_die();

}

/* List out Less files in a specific folder */
function bsp_load_less_workshop() {	

        /* load current selected file list */
        $list = glob(WPE_BSP_SHARED_DATA_DIR . $_POST["dir"]."/*.less");
        foreach ($list as $filename) {
            $file_path_final = explode("/", $filename);
            $total = count($file_path_final);
            $file_name_final = $file_path_final[$total-1];
            $file_path_final = $file_path_final[$total-2]."/". $file_path_final[$total-1];
            ?>
            <div class="list-item" data-path="<?php echo $file_path_final; ?>"><i></i><?php echo $file_name_final; ?></div>
            <?php
        }
        ?> <!--**********--> <?php
        /* load all BSP theme Folder list */
        $list = glob(WPE_BSP_SHARED_DATA_DIR . "*");
        ?>
        <div class="list-folder-selector"><select class="select-part">
        <?php
        foreach ($list as $foldername) {
            $file_path_final = explode("/", $foldername);
            $total = count($file_path_final);
            $file_name_final = $file_path_final[$total-1];
            if(is_dir($foldername)){
            ?>
                <option value="<?php echo $file_name_final; ?>"
                <?php  
                if($_POST["dir"] == $file_name_final){ echo "selected='selected'"; }
                ?> >
                    <?php echo $file_name_final; ?>
                </option>      
           <?php
        } /* style.css isn't a folder' */
        } ?>
        </select>
        <div class="creator-part"><i class="fa fa-cog"></i></div>
        </div>
        <?php

        wp_die();

}

/* Popup Menu */
function bsp_popup(){
 
  if($_POST["reason"] == "create"){
      $title = "BSP Theme Management";
  }

?>
<div class="bsp-popup-cover">
<div class="bsp-popup-main">
<div class="bsp-popup-title-bar"> 
<div class="bsp-popup-title"><?php echo $title; ?></div>
<div class="bsp-popup-close"><i class="fa fa-times"></i></div>
</div>
<div class="bsp-popup-body">
<?php
    if($_POST["reason"] == "create"){
        ?>
            <div class="bsp-popup-label"> name: </div>
            <input id="bsp-data-name" placeholder="name" />
            <div style="padding-top:10px;">
            <select id="bsp-data-type">
            <option selected="selected" value="create-style" data-desc="Creates a new BSP theme to be used."> 
            New BSP theme </option>
            <option value="create-file" data-desc="Adds a new empty file to the currectly selected BSP theme."> 
            Add a new file </option>
            <option value="delete-style" data-desc="Deletes the BSP theme, just type in the name.(Make sure you spell in correctly.)"> 
            Delete BSP theme </option>
            <option value="delete-file" data-desc="Deletes a file, just type in the name. This file must exist in the currently selected BSP theme.(Make sure you spell in correctly.)"> 
            Delete BSP File </option>
            <option value="duplicate-style" data-desc="Duplicates the BSP theme, just type in its name. This allows you can make changes without destroying the original."> 
            Duplicate BSP theme </option>
            <option value="rename-style" data-desc="Renames the currently selected BSP theme, just type in the name.(Make sure you spell in correctly.)"> 
            Rename BSP theme </option>
            </select>
            </div>
            <div id="bsp-select-desc">Creates a new BSP theme to be used.</div>
        <?php
    } 
?>
</div>
<div class="pop-btn-bar">
<button class="bsp-popup-cancel pop-btn">Cancel</button>
<button class="bsp-popup-confirm pop-btn"> Run Task </button>
</div></div></div>
<?php

    wp_die();
}

/* Create a new BSP themefolder or file */
function bsp_run_file_action() {

        /* prepare data */	
        global $WPE_helpers;
        $name = str_replace(" ", "-", $_POST["data"]["name"]);
        $directory = trim(reset(explode("/", $_POST["data"]["dir"])));

        /* Create a new style directory */
        if($_POST["data"]["type"] == "create-style"){
            $source = WPE_BLOG_STYLES_PRO_DIR."templates/default";
            $dest = WPE_BSP_SHARED_DATA_DIR . $name."/";
            $WPE_helpers->copy_recursive($source, $dest);
            echo  '{ "dir": "'. $name.'", "message" :"BSP theme ' . $name . ' created!"}';
        } 
        /* Create a new file in a style directory */
        else if($_POST["data"]["type"] == "create-file"){
            file_put_contents(WPE_BSP_SHARED_DATA_DIR . $directory."/". $name.".less" ,"");
            echo '{ "dir": "'. $directory.'", "message" :"'. $name . ' created in ' . $directory.'!"}';
        } 
        /* Delete entire style directory */
        else if($_POST["data"]["type"] == "delete-style"){
            $dest = WPE_BSP_SHARED_DATA_DIR . $name."/";
            $WPE_helpers->unlink_recursive($dest, TRUE);
            echo '{ "dir": "'. $directory.'", "message" :"BSP theme '. $name . ' deleted!"}';
        } 
        /* Delete Single File */
        else if($_POST["data"]["type"] == "delete-file"){  
            unlink(WPE_BSP_SHARED_DATA_DIR . $directory."/". $name.".less");
            echo '{ "dir": "'. $directory.'", "message" :"BSP file '. $name . ' in ' . $directory . ' deleted!"}';
        }
        /* Duplicate an existing style directory */
        else if($_POST["data"]["type"] == "duplicate-style"){  
            $source = WPE_BSP_SHARED_DATA_DIR . $name."/";
            $new_unique_name = $WPE_helpers->unique_name_in_dir(WPE_BSP_SHARED_DATA_DIR , $name, "-duplicated-");
            $dest   = WPE_BSP_SHARED_DATA_DIR . $new_unique_name."/";
            $WPE_helpers->copy_recursive($source, $dest);
            echo  '{ "dir": "'. $name.'", "message" :"BSP theme ' . $name . ' is duplicated!"}';
        } 
        /* Rename current style */
        else if($_POST["data"]["type"] == "rename-style"){  
            rename(WPE_BSP_SHARED_DATA_DIR . $directory, WPE_BSP_SHARED_DATA_DIR . $name);
            echo  '{ "dir": "'. $name.'", "message" :"BSP theme ' . $directory . ' is now ' . $name . '!"}';
        }
        else {
            echo '{ "dir": "'. $directory.'", "message" :"fail"}';
        }

        wp_die();

}

/* AJAX Calls */
add_action( 'wp_ajax_bsp_load_less_file', 'bsp_load_less_file' );
add_action( 'wp_ajax_bsp_save_less_file', 'bsp_save_less_file' );
add_action( 'wp_ajax_bsp_load_less_workshop', 'bsp_load_less_workshop' );
add_action( 'wp_ajax_bsp_run_file_action', 'bsp_run_file_action' );
add_action( 'wp_ajax_bsp_popup', 'bsp_popup' );


} /* isAdmin */
?>