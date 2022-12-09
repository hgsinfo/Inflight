<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists('Ph_UPS_Woo_Shipping_Common') ) {
	/**
	 * Common Class.
	 */
	class Ph_UPS_Woo_Shipping_Common {
		
		/**
		 * Wordpress date format.
		 */
		public static $wp_date_format;
		/**
		 * Wordpress date format.
		 */
		public static $wp_time_format;

		/**
		 * Get Wordpress Date format.
		 * @return string Wordpress Date format.
		 */
		public static function get_wordpress_date_format(){
			if( empty(self::$wp_date_format) ) {
				self::$wp_date_format = get_option('date_format');
			}
			return self::$wp_date_format;
		}

		/**
		 * Get Wordpress Time format.
		 * @return string Wordpress Time format.
		 */
		public static function get_wordpress_time_format(){
			if( empty(self::$wp_time_format) ) {
				self::$wp_time_format = get_option('time_format');
			}
			return self::$wp_time_format;
		}

	}
}