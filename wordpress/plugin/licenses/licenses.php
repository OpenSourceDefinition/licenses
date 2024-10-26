<?php
/**
 * Plugin Name: Open Source Licenses (OSL)
 * Plugin URI: https://github.com/OpenSourceDefinition/licenses
 * Description: Manages Open Source Licenses (OSL) that comply with the Open Source Definition (OSD)
 * Version: 1.0.0
 * Author: Sam Johnston
 * Author URI: https://samjohnston.org
 * Text Domain: open-source-licenses
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * License: MIT
 */

if (!defined('ABSPATH')) exit;

// Register Custom Post Type
function register_license_post_type() {
    $args = array(
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'licenses'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 50,
        'menu_icon' => 'dashicons-media-document',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies' => array('category', 'post_tag'),
        'labels' => array(
            'name' => 'Licenses',
            'singular_name' => 'License',
            'menu_name' => 'My Licenses',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New License',
            'edit_item' => 'Edit License',
            'view_item' => 'View License',
            'all_items' => 'All Licenses',
            'search_items' => 'Search Licenses',
            'not_found' => 'No licenses found',
        ),
    );
    
    register_post_type('license', $args);
}
add_action('init', 'register_license_post_type');

// Add template loading logic
function license_template_loader($template) {
    if (is_post_type_archive('license')) {
        $theme_file = locate_template(array('archive-license.php'));
        if ($theme_file) {
            return $theme_file;
        } else {
            return plugin_dir_path(__FILE__) . 'templates/archive-license.php';
        }
    }
    
    if (is_singular('license')) {
        $theme_file = locate_template(array('single-license.php'));
        if ($theme_file) {
            return $theme_file;
        } else {
            return plugin_dir_path(__FILE__) . 'templates/single-license.php';
        }
    }
    
    return $template;
}
add_filter('template_include', 'license_template_loader');

function license_manager_styles() {
    wp_enqueue_style(
        'license-manager-styles',
        plugins_url('css/style.css', __FILE__),
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'license_manager_styles');
