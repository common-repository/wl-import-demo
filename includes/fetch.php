<?php
/**
 * Get Demo data from Website Learnings
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
if ( ! class_exists( 'WLID_Theme_Demo_Content' ) ) {

	class WLID_Theme_Demo_Content {

        public $url = 'https://demo.websitelearnings.com/';

        public $theme;

        /**
         * Start things up
         *
         * @since 1.0.0
         */
        public function __construct() {

            $this->theme = wp_get_theme(); 
        }

        public function get_json_data( $is_settings = false, $as_array = false ) {

            $theme_name = $this->theme->get('TextDomain');

            if($is_settings) {
                $json_file = $theme_name . "-settings.json";
            }
            else {
                $json_file = $theme_name . ".json";
            }
            $remote_url = $this->url . 'themes_json/' . $theme_name . '/' . $json_file;

            // Make the request
            $request = wp_remote_get( $remote_url );

            // If the remote request fails, wp_remote_get() will return a WP_Error, so let’s check if the $request variable is an error:
            if( is_wp_error( $request ) ) {
                return false; // Bail early
            }
            else {
                // Retrieve the data
                $body = wp_remote_retrieve_body( $request );
                $data = json_decode( $body, $as_array );

                return $data;  
            }
        }

        public function get_import_files() {

            $data = $this->get_json_data();

            if($data) {

                $data_array[] = array(
                    'import_file_name'              => $data->import_file_name,
                    'import_file_url'               => $data->import_file_url,
                    'import_widget_file_url'        => $data->import_widget_file_url,
                    'import_customizer_file_url'    => $data->import_customizer_file_url,
                    'import_notice'                 => $data->import_notice,
                    'has_footer_menu'               => $data->has_footer_menu
                );
                
                return $data_array;
            }
            else {
                return false;
            }

        }

        public function get_preloaded_settings() {
            $data = $this->get_json_data(true, true);
            
            if($data) {

                if(sizeof($data) > 0) {
                    foreach($data as $key => $value){
                        $data_array['data_contents'][$key] = $value;
                    }

                    return $data_array;
                }
                else {
                    return false;
                }
                
            }
            else {
                return false;
            }
        }
    }
}


function wlid_get_import_files() {
    $wl_theme = new WLID_Theme_Demo_Content();

    return $wl_theme->get_import_files();
}

function wlid_get_import_settings() {
    $wl_theme = new WLID_Theme_Demo_Content();

    return $wl_theme->get_preloaded_settings();
}