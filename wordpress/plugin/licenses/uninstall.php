<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Enable error reporting for debugging
if (defined('WP_CLI') && WP_CLI) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require_once plugin_dir_path(__FILE__) . 'includes/populator.php';
osl_uninstall_cleanup();
