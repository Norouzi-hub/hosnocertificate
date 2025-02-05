<?php
/**
 * Plugin Name: HosNo Certificate
 * Plugin URI: https://Hosseinnorouzi.com
 * Description: A comprehensive business certification and rating system for WordPress.
 * Version: 1.0.1
 * Author: Hossein Norouzi
 * Author URI: https://Hosseinnorouzi.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: hosnocertificate
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('HC_VERSION', '1.0.0');
define('HC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once HC_PLUGIN_DIR . 'includes/class-hc-activator.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-deactivator.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-business-manager.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-rating.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-search.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-comparison.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-booking.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-verification.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-roles.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-settings.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-security.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-user-dashboard.php';
require_once HC_PLUGIN_DIR . 'includes/class-hc-email-notifications.php';

// Activation and deactivation hooks
register_activation_hook(__FILE__, array('HC_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('HC_Deactivator', 'deactivate'));

// Initialize the plugin
function run_hosnocertificate() {
    $plugin = new HC_Business_Manager();
    $plugin->run();
}
run_hosnocertificate();