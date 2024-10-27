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

require_once plugin_dir_path(__FILE__) . 'includes/populator.php';
require_once plugin_dir_path(__FILE__) . 'includes/patterns.php';
require_once plugin_dir_path(__FILE__) . 'includes/templates.php';

// Add taxonomy registration function
function register_license_taxonomies() {
    // Register License Categories
    register_taxonomy('license_category', 'license', array(
        'hierarchical'      => true,
        'labels'           => array(
            'name'              => _x('License Categories', 'taxonomy general name', 'open-source-licenses'),
            'singular_name'     => _x('License Category', 'taxonomy singular name', 'open-source-licenses'),
            'menu_name'         => __('Categories', 'open-source-licenses'),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array(
            'slug' => 'licenses/category',
            'with_front' => false
        ),
    ));

    // Register License Tags
    register_taxonomy('license_tag', 'license', array(
        'hierarchical'      => false,
        'labels'           => array(
            'name'              => _x('License Tags', 'taxonomy general name', 'open-source-licenses'),
            'singular_name'     => _x('License Tag', 'taxonomy singular name', 'open-source-licenses'),
            'menu_name'         => __('Tags', 'open-source-licenses'),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array(
            'slug' => 'licenses/tag',
            'with_front' => false
        ),
    ));
}
add_action('init', 'register_license_taxonomies');

// Register activation hook
register_activation_hook(__FILE__, 'osl_activate');
function osl_activate() {
    register_license_post_type();
    register_license_taxonomies();
    
    // Clear template cache
    wp_cache_delete('block_templates', 'block-templates');
    
    // Create default terms
    $default_categories = array(
        'International' => 'Licenses with international focus or origin',
        'Non-Reusable' => 'Licenses that cannot be reused',
        'Miscellaneous' => 'Other miscellaneous licenses',
        'Retired' => 'Voluntarily retired licenses',
        'Popular' => 'Widely used licenses with strong community support',
        'Redundant' => 'Licenses redundant with more popular alternatives',
        'Special' => 'Licenses for special purposes',
        'Superseded' => 'Licenses that have been superseded by newer versions',
        'Uncategorized' => 'Licenses without a specific category'
    );

    foreach ($default_categories as $category => $description) {
        if (!term_exists($category, 'license_category')) {
            wp_insert_term(
                $category,
                'license_category',
                array('description' => $description)
            );
        }
    }

    // Create OSI Certified tag
    if (!term_exists('osi-certified', 'license_tag')) {
        wp_insert_term(
            'osi-certified',
            'license_tag',
            array('description' => 'Licenses certified by the Open Source Initiative')
        );
    }
    
    // Initialize the license populator
    $populator = new License_Populator();
    $result = $populator->setup_and_populate();
    
    if (!$result) {
        error_log('OSL Plugin: Failed to populate licenses during activation');
    }
    
    // Flush rules after everything is set up
    flush_rewrite_rules();
}

// Add WP-CLI support
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('licenses populate', function() {
        WP_CLI::log('Starting license population...');
        
        // Verify class exists
        if (!class_exists('License_Populator')) {
            WP_CLI::error('License_Populator class not found');
            return;
        }
        
        WP_CLI::log('Initializing populator...');
        $populator = new License_Populator();
        
        WP_CLI::log('Running setup and populate...');
        $result = $populator->setup_and_populate();
        
        if ($result) {
            WP_CLI::success('Licenses table created and populated successfully');
        } else {
            WP_CLI::error('Failed to setup or populate licenses');
        }
    });
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
        'taxonomies' => array('license_category', 'license_tag'), // Update this line
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

function license_manager_styles() {
    wp_enqueue_style(
        'license-manager-styles',
        plugins_url('css/style.css', __FILE__),
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'license_manager_styles');
