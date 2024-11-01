<?php
/*
Plugin Name: WL Import Demo
Plugin URI: https://websitelearnings.com/wl-import-demo/
Description: Import Demo data of specific Theme created by Website Learnings 
Version: 1.1
Author: Website Learnings
Author URI: https://websitelearnings.com/
License: GPLv2 or later
Text Domain: websitelearnings
*/

require_once plugin_dir_path( __FILE__ ) . '/includes/tgmpa/class-tgm-plugin-activation.php';

require_once plugin_dir_path( __FILE__ ) . '/includes/fetch.php';

/**
 * Plugin activation.
 */

function wlid_after_import_setup() {
    $theme_settings = wlid_get_import_settings();
    $initial_settings = wlid_get_import_files();
    $theme_options = array();

    $has_footer_menu = ($initial_settings['has_footer_menu'] == 'yes') ? true : false;
    
    if($theme_settings) {

        foreach($theme_settings['data_contents'] as $key => $val) {
           $theme_options[$key] = $val;
        }
    }
    else {
        $has_footer_menu = false;
    }

    $main_menu = get_term_by( 'name', 'Primary Navigation', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
            'primary' => $main_menu->term_id,
            'footer' => ($has_footer_menu) ? $main_menu->term_id : 0,
        )
    );
        // Assign home page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );
    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );

    // Enable Elementor Font Awesome Icon support
    update_option( 'elementor_load_fa4_shim', 'yes' );

    // Set Default Logo
    $logo_id = wlid_get_attachment_id_by_slug('logo');

    if($logo_id){
        set_theme_mod( 'custom_logo', $logo_id );
    }
    else {
        set_theme_mod( 'custom_logo', false );
    }
}
add_action( 'pt-ocdi/after_import', 'wlid_after_import_setup' );

function wlid_before_widgets_import($selected_import) {
    if (!get_option('acme_cleared_widgets')) {
        update_option('sidebars_widgets', array());
        update_option('acme_cleared_widgets', true);
    }
}
add_action('pt-ocdi/before_widgets_import', 'wlid_before_widgets_import');

function wlid_import_files() {
    return wlid_get_import_files();
}

add_filter( 'pt-ocdi/import_files', 'wlid_import_files' );

add_filter('pt-ocdi/disable_pt_branding', '__return_true');

// Find ID of default logo
function wlid_get_attachment_id_by_slug( $slug ) {
    $args = array(
        'post_type' => 'attachment',
        'name' => sanitize_title($slug),
        'posts_per_page' => 1,
        'post_status' => 'inherit',
    );

    $_header = get_posts( $args );
    $header = $_header ? array_pop($_header) : null;

    return $header ? $header->ID : null;
}