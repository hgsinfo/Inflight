<?php
/**
 * Fired during plugin deactivation
 *
 * @link https://neoslab.com
 * @since 1.0.0
 *
 * @package Powered_Minifier
 * @subpackage Powered_Minifier/includes
*/

/**
 * Class `Powered_Minifier_Deactivator`
 * This class defines all code necessary to run during the plugin's deactivation
 * @since 1.0.0
 * @package Powered_Minifier
 * @subpackage Powered_Minifier/includes
 * @author Neoslab <contact@neoslab.com>
*/
class Powered_Minifier_Deactivator
{
	/**
	 * Deactivate plugin
	 * @since 1.0.0
	*/
	public static function deactivate()
	{
		$option = delete_option('_powered_minifier');
	}
}

?>