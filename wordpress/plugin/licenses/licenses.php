<?php
/**
 * Open Source Licenses (OSL)
 *
 * @package     OpenSourceLicenses
 * @author      Sam Johnston
 * @copyright   2024 Sam Johnston
 * @license     MIT
 *
 * @wordpress-plugin
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
 * License URI: https://opensource.org/licenses/MIT
 */

if (!defined('ABSPATH')) exit;

// Register activation hook
register_activation_hook(__FILE__, 'osl_activate');
function osl_activate() {
    // Create any necessary database tables
    // Set up plugin options/defaults
    // Set up any necessary roles and capabilities
    
    // Flush rewrite rules after registering custom post type
    register_license_post_type();
    flush_rewrite_rules();
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'osl_deactivate');
function osl_deactivate() {
    // Clean up temporary data
    // Remove plugin-specific capabilities if needed
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Register uninstall hook (must be in main plugin file)
register_uninstall_hook(__FILE__, 'osl_uninstall');
function osl_uninstall() {
    // Remove all plugin-related data from database
    // Remove custom post types and their posts
    // Remove plugin options
    
    // Example: Delete all license posts
    $licenses = get_posts(array('post_type' => 'license', 'numberposts' => -1));
    foreach ($licenses as $license) {
        wp_delete_post($license->ID, true);
    }
    
    // Clean up any plugin options
    delete_option('osl_plugin_options');
}

// Register Custom Post Type
function register_license_post_type() {
    $args = array(
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
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

function register_license_type_taxonomy() {
    $args = array(
        'hierarchical'      => true,
        'show_ui'          => true,
        'show_admin_column' => true,
        'show_in_rest'     => true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'license-type'),
        'labels'           => array(
            'name'              => 'License Types',
            'singular_name'     => 'License Type',
            'search_items'      => 'Search License Types',
            'all_items'         => 'All License Types',
            'edit_item'         => 'Edit License Type',
            'update_item'       => 'Update License Type',
            'add_new_item'      => 'Add New License Type',
            'new_item_name'     => 'New License Type Name',
            'menu_name'         => 'License Types'
        )
    );
    
    register_taxonomy('license_type', 'license', $args);
}
add_action('init', 'register_license_type_taxonomy');

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
#add_filter('template_include', 'license_template_loader');

function license_manager_styles() {
    wp_enqueue_style(
        'license-manager-styles',
        plugins_url('css/style.css', __FILE__),
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'license_manager_styles');

require_once plugin_dir_path(__FILE__) . 'includes/patterns.php';
