<?php

class WEXPANSE_BSP_Admin_Init {

       /**
        * If BSP theme Library out of date or doesn't exist then create it
        *
        * @since 1.0.0
        */
    	public function detect_library_version(){
            if(!file_exists(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"] . "version.txt")){
                mkdir(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"], 0755, true);
                // Copy Library Updates over to the Shared Data 
                WPEXPANSE_Blog_Styles_Pro::$helpers->copy_recursive(WPEXPANSE_Blog_Styles_Pro::$plugin_data["library-dir"], WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"]);
            } else {
                // If Library versions don't match then update Shared Data 
                if(file_get_contents(WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-root"] . "version.txt") != file_get_contents(WPEXPANSE_Blog_Styles_Pro::$plugin_data["library-root"] . "version.txt")){
                    WPEXPANSE_Blog_Styles_Pro::$helpers->copy_recursive(WPEXPANSE_Blog_Styles_Pro::$plugin_data["library-dir"], WPEXPANSE_Blog_Styles_Pro::$plugin_data["shared-bsp-dir"]);
                }
            }
        }

       /**
        * Check if the core exists otherwise download it from GitHub
        *
        * @since 1.0.0
        */
    	public function init_wpe_core(){
            // If WPE Core Dependencies not found then accuire them from GitHub
            if ( !file_exists( WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir'] . WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-core'] ) ){

                // Create Shared Folder if doesn't exists
                if(!file_exists(WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir'])){
                    mkdir(WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir'], 744);
                }
                // Download
                //TODO look into using API -> https://api.github.com/repos/wpexpanse/WPE-Core/master
                $zip_file = file_get_contents('https://github.com/wpexpanse/WPE-Core/archive/master.zip');
                file_put_contents(WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir']."/wpe-core.zip", $zip_file);
                // Unzip
                $zip = new ZipArchive;
                $wpe_core = $zip->open(WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir']."/wpe-core.zip");
                if ( $wpe_core === TRUE ) {
                    $zip->extractTo( WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir'] . '/' );
                    $zip->close();
                    // rename and remove zipfile
                    rename(WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir']."/WPE-Core-master", WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir'] . WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-core']);
                    unlink(WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir']."/wpe-core.zip");
                } else { 
                    echo '<div style="padding:20px;font-size:20px"> Error core was not installed! Please reinstall or check the permissions of the plugins folder.</div>';
                }

            }

            // Require Core Dependencies 
            if(file_exists(WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir'].WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-core']."wpe-shared-helpers.php")){

                // Core Library Includes
                $shared_dependency = array(
                    WPEXPANSE_shared_helpers => "wpe-shared-helpers.php",
                    WPEXPANSE_BSP_ui => "wpe-shared-ui.php",
                    Less_Parser => "3pl/lessphp/Less.php"		
                );
                // Be sure to check to make sure they don't already exist
                foreach ($shared_dependency as $dep_class => $dep_path) {
                    if(!class_exists($dep_class)){
                        require_once WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-dir'] . WPEXPANSE_Blog_Styles_Pro::$plugin_data['shared-core'] . $dep_path;
                    }
                }

            }
        }

}