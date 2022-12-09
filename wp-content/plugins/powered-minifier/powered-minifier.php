<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link https://neoslab.com
 * @since 1.0.0
 * @package Powered_Minifier
 *
 * @wordpress-plugin
 * Plugin Name: Powered Minifier
 * Plugin URI: https://wordpress.org/plugins/powered-minifier/
 * Description: Powered Minifier allow you to reduce your page load and increase your performance by minifying your HTML source along with all the CSS and JS code present in your markup.
 * Version: 1.5.7
 * Author: Neoslab
 * Author URI: https://neoslab.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: powered-minifier
 * Domain Path: /languages
*/

/**
 * If this file is called directly, then abort
*/
if(!defined('WPINC'))
{
	die;
}

/**
 * Currently plugin version
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions
*/
define('POWERED_MINIFIER_VERSION', '1.5.7');

/**
 * The code that runs during plugin activation
 * This action is documented in includes/class-powered-minifier-activator.php
*/
function activate_powered_minifier()
{
	require_once plugin_dir_path(__FILE__).'includes/class-powered-minifier-activator.php';
	Powered_Minifier_Activator::activate();
}

/**
 * The code that runs during plugin deactivation
 * This action is documented in includes/class-powered-minifier-deactivator.php
*/
function deactivate_powered_minifier()
{
	require_once plugin_dir_path(__FILE__).'includes/class-powered-minifier-deactivator.php';
	Powered_Minifier_Deactivator::deactivate();
}

/**
 * Activation/deactivation hook
*/
register_activation_hook(__FILE__, 'activate_powered_minifier');
register_deactivation_hook(__FILE__, 'deactivate_powered_minifier');

/**
 * The core plugin class that is used to define internationalization and admin-specific hooks
*/
require plugin_dir_path(__FILE__).'includes/class-powered-minifier-core.php';

/**
 * Begins execution of the plugin
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle
 * @since 1.0.0
*/
function run_powered_minifier()
{
	$plugin = new Powered_Minifier();
	$plugin->run();
}

/**
 * Run plugin
*/
run_powered_minifier();

?>