<?php

class WEXPANSE_BSP_Admin_Functions {

       /**
        * Assign all admin AJAX action to the proper functions
        *
        * @since 1.0.0
        */
    	public function __construct(){

		if( is_admin()){

			// WPEXPANSE_Blog_Styles_Pro::get_plugin_data();
            /* AJAX Calls */
            add_action( 'wp_ajax_bsp_load_less_file', array($this, 'bsp_load_less_file' ) );
            add_action( 'wp_ajax_bsp_save_less_file', array($this, 'bsp_save_less_file' ) );
            add_action( 'wp_ajax_bsp_load_less_workshop', array($this, 'bsp_load_less_workshop' ) );
            add_action( 'wp_ajax_bsp_run_file_action', array($this, 'bsp_run_file_action' ) );
            add_action( 'wp_ajax_bsp_popup', array($this, 'bsp_popup' ) );

		}

	}

    /**
    * load LESS file
    *
    * @since 1.0.0
    */
    public function bsp_load_less_file() {	

            /* Prepare Data */
            $filename = WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $_POST["filename"];

            /* Load this File */
            $data = file_get_contents($filename);

            /* Print return contents */
            echo $data;

            wp_die();

    }

    /**
	 * Save less data to file
	 *
	 * @since 1.0.0
	 */
    public function bsp_save_less_file() {	
            
            /* Prepare Data */
            if($_POST["data"]["mode"] == "advanced"){
                $filename = WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $_POST["data"]["path"];
                $folderpath = explode("/", $_POST["data"]["path"]);
                $name = $folderpath[1];
                $folderpath = WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $folderpath[0]."/";
                /* Save this File */
                file_put_contents($filename, str_replace("\'", "'", str_replace('\"', '"', $_POST["data"]["content"])));
                echo $name . " was saved & compiled!";
            } else {
                $folderpath = WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $_POST["data"]["path"]."/";
                echo "settings saved & compiled!";
                /* TODO when I add options here then save them */
            }

            /* Compile the less file and output a CSS file */
            WPEXPANSE_Blog_Styles_Pro::$less->parseFile($folderpath."style.less", $folderpath);
            $get_final_styles = WPEXPANSE_Blog_Styles_Pro::$less->getCss();
            file_put_contents(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . "style.css", $get_final_styles);


            /* Filter the css into usable data for workshop/admin-config.json which is used for post inserting */
            $get_container_total = explode("{", $get_final_styles);
            $get_container_total = explode(",", $get_container_total[0]);
            $container_total = count($get_container_total);
            $get_final_styles = WPEXPANSE_Blog_Styles_Pro::$helpers->filter_out_everything_between_these_tags("{", "}", $get_final_styles);
            $get_final_styles = WPEXPANSE_Blog_Styles_Pro::$helpers->filter_out_everything_between_these_tags("/*", "*/", $get_final_styles);
            $get_final_styles = str_replace(array("#BSP-init ", ",#tinymce.wp-editor"), "", $get_final_styles);
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
            file_put_contents(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . "admin-config.json", $admin_config);

            /* Should trigger a cache purge */
            do_action("switch_theme");

            wp_die();

    }

    /**
    * Get a List of LESS files in current theme
    *
    * @since 1.0.0
    */
    public function bsp_load_less_workshop() {	

            /* load current selected file list */
            $list = glob(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $_POST["dir"]."/*.less");
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
            $list = glob(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . "*");
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

    /**
    * Popup a menu for folder managemenet
    *
    * @since 1.0.0
    */
    public function bsp_popup(){
    
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

    /**
    * Task runner that modifies the BSP file & theme system
    *
    * @since 1.0.0
    */
    public function bsp_run_file_action() {

            /* prepare data */
            $name = str_replace(" ", "-", $_POST["data"]["name"]);
            $directory = trim(reset(explode("/", $_POST["data"]["dir"])));

            /* Create a new style directory */
            if($_POST["data"]["type"] == "create-style"){
                $source = WPEXPANSE_Blog_Styles_Pro::$plugin_data["this-dir"]."templates/library/empty";
                $dest = WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $name."/";
                WPEXPANSE_Blog_Styles_Pro::$helpers->copy_recursive($source, $dest);
                echo  '{ "dir": "'. $name.'", "message" :"BSP theme ' . $name . ' created!"}';
            } 
            /* Create a new file in a style directory */
            else if($_POST["data"]["type"] == "create-file"){
                file_put_contents(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $directory."/". $name.".less" ,"");
                echo '{ "dir": "'. $directory.'", "message" :"'. $name . ' created in ' . $directory.'!"}';
            } 
            /* Delete entire style directory */
            else if($_POST["data"]["type"] == "delete-style"){
                $dest = WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $name."/";
                WPEXPANSE_Blog_Styles_Pro::$helpers->unlink_recursive($dest, TRUE);
                echo '{ "dir": "'. $directory.'", "message" :"BSP theme '. $name . ' deleted!"}';
            } 
            /* Delete Single File */
            else if($_POST["data"]["type"] == "delete-file"){  
                unlink(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $directory."/". $name.".less");
                echo '{ "dir": "'. $directory.'", "message" :"BSP file '. $name . ' in ' . $directory . ' deleted!"}';
            }
            /* Duplicate an existing style directory */
            else if($_POST["data"]["type"] == "duplicate-style"){  
                $source = WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $name."/";
                $new_unique_name = WPEXPANSE_Blog_Styles_Pro::$helpers->unique_name_in_dir(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] , $name, "-duplicated-");
                $dest   = WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $new_unique_name."/";
                WPEXPANSE_Blog_Styles_Pro::$helpers->copy_recursive($source, $dest);
                echo  '{ "dir": "'. $name.'", "message" :"BSP theme ' . $name . ' is duplicated!"}';
            } 
            /* Rename current style */
            else if($_POST["data"]["type"] == "rename-style"){  
                rename(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $directory, WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . $name);
                echo  '{ "dir": "'. $name.'", "message" :"BSP theme ' . $directory . ' is now ' . $name . '!"}';
            }
            else {
                echo '{ "dir": "'. $directory.'", "message" :"fail"}';
            }

            wp_die();

    }


}