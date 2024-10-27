<?php

if (!defined('ABSPATH')) exit;

class License_Populator {
    private $json_file = 'data/licenses.json';
    private $version = '1.0.0';
    
    public function setup_and_populate() {
        // Add cleanup of existing data before populating
        $this->cleanup_existing_data();
        
        $result = $this->populate_licenses();
        
        if ($result) {
            update_option('osl_db_version', $this->version);
        }
        
        return $result;
    }

    /**
     * Cleans up existing license data
     */
    public function cleanup_existing_data() {
        global $wpdb;

        try {
            // 1. First remove all term relationships
            $wpdb->query("DELETE FROM {$wpdb->term_relationships} WHERE object_id IN (
                SELECT ID FROM {$wpdb->posts} WHERE post_type = 'license'
            )");

            // 2. Delete all license posts one by one
            $existing_licenses = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
                    'license'
                )
            );

            foreach ($existing_licenses as $license_id) {
                wp_delete_post($license_id, true);
            }

            // 3. Clean up taxonomies directly through database
            $wpdb->query($wpdb->prepare(
                "DELETE tt, t FROM {$wpdb->term_taxonomy} tt 
                LEFT JOIN {$wpdb->terms} t ON t.term_id = tt.term_id 
                WHERE tt.taxonomy = %s OR tt.taxonomy = %s",
                'license_category',
                'license_tag'
            ));

            // 4. Clean up any orphaned postmeta
            $wpdb->query($wpdb->prepare(
                "DELETE pm FROM {$wpdb->postmeta} pm 
                LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id 
                WHERE p.ID IS NULL OR p.post_type = %s",
                'license'
            ));

        } catch (Exception $e) {
            error_log('License cleanup error: ' . $e->getMessage());
            if (defined('WP_CLI') && WP_CLI) {
                WP_CLI::error('License cleanup failed: ' . $e->getMessage());
            }
            return false;
        }

        return true;
    }

    public function populate_licenses() {
        $json_path = plugin_dir_path(dirname(__FILE__)) . $this->json_file;
        
        if (!file_exists($json_path)) {
            $message = 'Licenses JSON file not found: ' . $json_path;
            if (defined('WP_CLI') && WP_CLI) {
                WP_CLI::error($message);
            }
            error_log($message);
            return false;
        }

        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::log('Reading JSON file from: ' . $json_path);
        }

        $json_content = file_get_contents($json_path);
        $licenses = json_decode($json_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = 'Error parsing licenses JSON: ' . json_last_error_msg();
            if (defined('WP_CLI') && WP_CLI) {
                WP_CLI::error($message);
            }
            error_log($message);
            return false;
        }

        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::log('Found ' . count($licenses) . ' licenses to process');
        }

        foreach ($licenses as $license_name => $license_data) {
            if (defined('WP_CLI') && WP_CLI) {
                WP_CLI::log('Creating license: ' . $license_name);
            }
            $result = $this->create_license($license_name, $license_data);
            if (!$result && defined('WP_CLI') && WP_CLI) {
                WP_CLI::warning('Failed to create license: ' . $license_name);
            }
        }

        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::success('All licenses processed');
        }
        return true;
    }

    private function create_license($license_name, $license_data) {
        // Get enhanced data
        $enhanced = $license_data['enhanced'] ?? [];

        $post_data = array(
            'post_title'    => $license_data['title'] ?? $license_name,
            'post_name'     => $license_data['slug'] ?? sanitize_title($license_name),
            'post_content'  => $license_data['license_body'] ?? '',
            'post_status'   => 'publish',
            'post_type'     => 'license',
            'post_excerpt'  => $enhanced['description'] ?? ''
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id && !is_wp_error($post_id)) {
            // Handle category mapping
            if (!empty($license_data['category'])) {
                $category = trim($license_data['category']);
                $mapped_category = $this->map_category($category);
                wp_set_object_terms($post_id, $mapped_category, 'license_category');
            } else {
                // Set as Uncategorized if no category provided
                wp_set_object_terms($post_id, 'Uncategorized', 'license_category');
            }

            // Handle OSI certification tag
            if (!empty($license_data['osi_certified']) && $license_data['osi_certified'] === true) {
                wp_set_object_terms($post_id, 'osi-certified', 'license_tag', true);
            }
        }

        // Basic meta fields
        $meta_fields = array(
            'link' => $license_data['link'] ?? '',
            'spdx' => $license_data['spdx'] ?? '',
            'category' => $license_data['category'] ?? '',
            'version' => $license_data['version'] ?? '',
            'osi_submitted_date' => $license_data['osi_submitted_date'] ?? '',
            'osi_submitted_link' => $license_data['osi_submitted_link'] ?? '',
            'osi_submitter' => $license_data['osi_submitter'] ?? '',
            'osi_certified_date' => $license_data['osi_certified_date'] ?? '',
            'osi_board_minutes_link' => $license_data['osi_board_minutes_link'] ?? '',
            'spdx_detail_page' => $license_data['spdx_detail_page'] ?? '',
            'steward' => $license_data['steward'] ?? '',
            'steward_url' => $license_data['steward_url'] ?? '',
            'osi_certified' => isset($license_data['osi_certified']) ? (int)$license_data['osi_certified'] : 0,
        );

        // Enhanced meta fields
        if (!empty($enhanced)) {
            $meta_fields = array_merge($meta_fields, array(
                'description' => $enhanced['description'] ?? '',
                'review' => $enhanced['review'] ?? '',
                'key_features' => json_encode($enhanced['key_features'] ?? []),
                'related_licenses' => json_encode($enhanced['related_licenses'] ?? []),
                'common_use_cases' => json_encode($enhanced['common_use_cases'] ?? []),
                'industry_adoption' => json_encode($enhanced['industry_adoption'] ?? []),
                'compliance_requirements' => json_encode($enhanced['compliance_requirements'] ?? []),
                'resources' => json_encode($enhanced['resources'] ?? []),
                'last_updated' => $enhanced['last_updated'] ?? ''
            ));
        }

        foreach ($meta_fields as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }

        return true;
    }

    /**
     * Maps original categories to our standardized categories
     */
    private function map_category($original_category) {
        $category_mapping = array(
            'International' => 'International',
            'Non-Reusable' => 'Non-Reusable',
            'Other/Miscellaneous' => 'Miscellaneous',
            'Voluntarily retired' => 'Retired',
            'Popular / Strong Community' => 'Popular',
            'Redundant with more popular' => 'Redundant',
            'Special Purpose' => 'Special',
            'Superseded' => 'Superseded'
        );

        return isset($category_mapping[$original_category]) 
            ? $category_mapping[$original_category] 
            : 'Uncategorized';
    }
}

/**
 * Cleanup function for uninstallation
 */
function osl_uninstall_cleanup() {
    // Only run cleanup if uninstall is triggered
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        return;
    }

    // Create temporary instance to use cleanup method
    $populator = new License_Populator();
    $populator->cleanup_existing_data();

    // Remove version option
    delete_option('osl_db_version');
}
