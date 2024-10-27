<?php

if (!defined('ABSPATH')) exit;

class License_Populator {
    private $json_file = 'data/licenses_original.json';
    private $version = '1.0.0';
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'licenses';
    }

    public function setup_and_populate() {
        $this->drop_table();
        $this->create_table();
        $result = $this->populate_licenses();
        
        if ($result) {
            update_option('osl_db_version', $this->version);
        }
        
        return $result;
    }

    private function drop_table() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$this->table_name}");
    }

    private function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            link varchar(255),
            spdx varchar(100),
            category varchar(100),
            version varchar(50),
            osi_submitted_date varchar(100),
            osi_submitted_link varchar(255),
            osi_submitter varchar(255),
            osi_approved_date varchar(100),
            osi_board_minutes_link varchar(255),
            spdx_detail_page varchar(255),
            steward varchar(255),
            steward_url varchar(255),
            osi_approved tinyint(1) DEFAULT 0,
            license_body longtext,
            description text,
            review text,
            key_features text,
            related_licenses text,
            common_use_cases text,
            industry_adoption text,
            compliance_requirements text,
            resources text,
            last_updated varchar(100),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function populate_licenses() {
        $json_path = plugin_dir_path(dirname(__FILE__)) . $this->json_file;
        
        if (!file_exists($json_path)) {
            error_log('Licenses JSON file not found: ' . $json_path);
            return false;
        }

        $json_content = file_get_contents($json_path);
        $licenses = json_decode($json_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Error parsing licenses JSON: ' . json_last_error_msg());
            return false;
        }

        if (get_option('licenses_populated')) {
            return true;
        }

        foreach ($licenses as $key => $license) {
            $this->create_license($key, $license);
        }

        update_option('licenses_populated', true);
        return true;
    }

    private function create_license($key, $license_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'licenses';

        // Format arrays into JSON
        $key_features = isset($license_data['enhanced']['key_features']) 
            ? json_encode($license_data['enhanced']['key_features'])
            : '';

        $related_licenses = isset($license_data['enhanced']['related_licenses']) 
            ? json_encode($license_data['enhanced']['related_licenses'])
            : '';

        $common_use_cases = isset($license_data['enhanced']['common_use_cases']) 
            ? json_encode($license_data['enhanced']['common_use_cases'])
            : '';

        $industry_adoption = isset($license_data['enhanced']['industry_adoption']) 
            ? json_encode($license_data['enhanced']['industry_adoption'])
            : '';

        $compliance_requirements = isset($license_data['enhanced']['compliance_requirements']) 
            ? json_encode($license_data['enhanced']['compliance_requirements'])
            : '';

        $resources = isset($license_data['enhanced']['resources']) 
            ? json_encode($license_data['enhanced']['resources'])
            : '';

        $data = array(
            'title' => $license_data['title'],
            'slug' => $license_data['slug'],
            'link' => $license_data['link'],
            'spdx' => $license_data['spdx'],
            'category' => $license_data['category'],
            'version' => $license_data['version'],
            'osi_submitted_date' => $license_data['osi_submitted_date'],
            'osi_submitted_link' => $license_data['osi_submitted_link'],
            'osi_submitter' => $license_data['osi_submitter'],
            'osi_approved_date' => $license_data['osi_approved_date'],
            'osi_board_minutes_link' => $license_data['osi_board_minutes_link'],
            'spdx_detail_page' => $license_data['spdx_detail_page'],
            'steward' => $license_data['steward'],
            'steward_url' => $license_data['steward_url'],
            'osi_approved' => $license_data['osi_approved'] ? 1 : 0,
            'license_body' => $license_data['license_body'],
            'description' => $license_data['enhanced']['description'],
            'review' => $license_data['enhanced']['review'],
            'key_features' => $key_features,
            'related_licenses' => $related_licenses,
            'common_use_cases' => $common_use_cases,
            'industry_adoption' => $industry_adoption,
            'compliance_requirements' => $compliance_requirements,
            'resources' => $resources,
            'last_updated' => $license_data['enhanced']['last_updated'],
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );

        $wpdb->insert($table_name, $data);
    }
}
