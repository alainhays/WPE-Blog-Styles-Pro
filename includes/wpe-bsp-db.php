<?php
/**
 * Get and Set data from database
 *
 * @since 1.1.3
 */
class WEXPANSE_BSP_DB {

        private $db_options = array();
        private static $db_name = "wpe-bsp";
		private static $db_defaults = "remove-auto-p-tags||1||current-template||default";
        private static $delimiter = "||";

        /* Initiate initial DB data */
        public function __construct() {
            $this->init();
        }

       /**
        * Load BSP form database else assign defaults
        *
        * @since 1.1.3
        */
        private function init(){
                
            $get_new_options = array();
            // Load settings form the database
            $db_get_options = get_option(self::$db_name, self::$db_defaults);
            $db_get_options = explode(self::$delimiter, $db_get_options);
            for ($i=0; $i < count($db_get_options); $i+=2) { 
                $get_new_options[$db_get_options[$i]] = $db_get_options[$i+1];
            }
            $this->db_options = $get_new_options;
        }

       /**
        * Load BSP form database else assign defaults
        *
        * @since 1.1.3
        */
        public function get_data($name){
            if(!isset($this->db_options[$name])){
                $this->init();
            }
            return esc_html($this->db_options[$name]);
        }

       /**
        * Change value then save to database
        *
        * @since 1.1.3
        */
        public function set_data($name, $data){
            $this->db_options[$name] = sanitize_text_field($data);
        }

       /**
        * Update data in the database
        *
        * @since 1.1.3
        */
        public function save_data(){
            if(!is_admin()){
                return 0;
            }
            $new_db_string = "";
            foreach ($this->db_options as $key => $value){
                $new_db_string .= $key.self::$delimiter.$value.self::$delimiter; 
            }
           $new_db_string = substr($new_db_string, 0, -2);
           // update data
           update_option(self::$db_name, $new_db_string);

        }

}

global $WPE_BSP_DB;
$WPE_BSP_DB = new WEXPANSE_BSP_DB;
