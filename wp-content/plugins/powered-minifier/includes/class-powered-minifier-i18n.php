<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link https://neoslab.com
 * @since 1.0.0
 *
 * @package Powered_Minifier
 * @subpackage Powered_Minifier/includes
*/

/**
 * Class `Powered_Minifier_i18n`
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * @since 1.0.0
 * @package Powered_Minifier
 * @subpackage Powered_Minifier/includes
 * @author Neoslab <contact@neoslab.com>
*/
class Powered_Minifier_i18n
{
	/**
	 * Load the plugin text domain for translation
	 * @since 1.0.0
	*/
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain('powered-minifier', false, dirname(dirname(plugin_basename(__FILE__))).'/languages/');
	}
}

?>