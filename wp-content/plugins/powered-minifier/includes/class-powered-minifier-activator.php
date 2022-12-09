<?php
/**
 * Fired during plugin activation
 *
 * @link https://neoslab.com
 * @since 1.0.0
 *
 * @package Powered_Minifier
 * @subpackage Powered_Minifier/includes
*/

/**
 * Class `Powered_Minifier_Activator`
 * This class defines all code necessary to run during the plugin's activation
 * @since 1.0.0
 * @package Powered_Minifier
 * @subpackage Powered_Minifier/includes
 * @author Neoslab <contact@neoslab.com>
*/
class Powered_Minifier_Activator
{
	/**
	 * Activate plugin
	 * @since 1.0.0
	*/
	public static function activate()
	{
		$option = add_option('_powered_minifier', false);
	}
}

?>