<?php
/**
 * A shared UI library with common reusable elements
 *
 * @package WPEXPANSE Core
 */
if(!class_exists('WPEXPANSE_Shared_UI')){
    class WPEXPANSE_Shared_UI {

        public function __construct(){

        }
    /**
    * The global header system for wpexpanse plugins
    *
    * @since 1.0.0
    */
        public function wpexpanse_master_header( $data_obj ){
        ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $data_obj['shared-root']; ?>/wpe-core/css/style.css" />
        <div id="wpe-master-header-container">
                <div class="wpe-jumbotron-section">
                <img class="wpe-plugin-logo" src="<?php echo $data_obj['logo'] ?>" /> 
                <div class="wpe-title">
                    <h1><?php echo $data_obj['name']; ?></h1>
                    Version <b><?php echo $data_obj['version']; ?></b> 
                    Created by <a href="<?php echo $data_obj['url-author']; ?>"><?php echo $data_obj['author']; ?></a> 
                </div>
                </div>
                <div class="wpe-master-action-bar">
                        <a href="<?php menu_page_url("wpe-dashboard"); ?>" target="_WPEXPANSE_blog">
                            <button class="wpe-action-btn">
                                <i class="fa fa-home"></i>
                                <span class="wpe-hide-on-mobile"> WPE Dashboard</span>
                            </button>
                        </a>
                        <a href="<?php echo $data_obj['url-blog'] ?>" target="_WPEXPANSE_blog">
                            <button class="wpe-action-btn">
                                <i class="fa fa-thumbs-o-up"></i>
                                <span class="wpe-hide-on-mobile"> WPE Blog</span>
                            </button>
                        </a>
                        <a href="<?php echo $data_obj['url-docs'] ?>" target="_WPEXPANSE_ayn_documentation">
                            <button class="wpe-action-btn" >
                                <i class="fa fa-list-alt"></i>
                                <span class="wpe-hide-on-mobile"> Documentation</span>
                            </button>
                        </a>
                        <a href="<?php echo $data_obj['url-main'] ?>" target="_WPEXPANSE_ayn_feature">
                            <button class="wpe-action-btn">
                                <i class="fa fa-info-circle"></i>
                                <span class="wpe-hide-on-mobile"> Plugin Page</span>
                            </button>
                        </a> 
                </div>
        </div>
        <?php
        }

        /**
        * Loads a PHP Rendered Template
        *
        * The filename is the full path Directory path without the ".php"
        * Use the $data parameter to pass data into each template as needed
        *
        * @since  1.0.0
        * @param  string $name is the template name.
        * @param  array  $data extracted into variables & passed into the template. Must be key value pairs!
        */
        public function load_template($filename, $data = array()){
            if(isset($filename)){
                extract($data);
                require $filename.".php";
            }
        }

    }
}

