<?php
/**
 * Plugin Name:       Cortex
 * Plugin URI:        http://logaritm.ca/cortex
 * Description:       Page builder aimed at developpers that leverages the power of Advanced Custom Fields and Timber.
 * Version:           2.1.3
 * Author:            Jean-Philippe Dery
 * Author URI:        http://logaritm.ca
 * Text Domain:       cortex
 * Domain Path:       /languages
 */

define('CORTEX_PLUGIN_VERSION', '2.1.3');

// If this file is called directly, abort.
if (defined('WPINC') == false) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cortex-activator.php
 */
function activate_cortex() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-cortex-activator.php';
	Cortex_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cortex-deactivator.php
 */
function deactivate_cortex() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-cortex-deactivator.php';
	Cortex_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_cortex');
register_deactivation_hook(__FILE__, 'deactivate_cortex');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-cortex.php';

/**
 * Begins execution of the plugin.
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 * @since 0.1.0
 */
$cortex_plugin = new Cortex();
$cortex_plugin->run();
