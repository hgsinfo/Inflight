<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduaspUltimateWPMail' ) ) {
	/**
	 * Class to handle Ultimate WP Mail integration for Ultimate Appointment Scheduling
	 *
	 * @since 2.0.0
	 */
	class ewduaspUltimateWPMail {

		public function __construct() {

			add_filter( 'uwpm_register_custom_element_section', array( $this, 'add_element_section' ) );
			add_action( 'uwpm_register_custom_element', array( $this, 'add_elements' ) );
		}

		/**
		 * Adds in a section for UASP tags in Ultimate WP Mail
		 * @since 2.0.0
		 */
		public function add_element_section() {

			if ( ! function_exists( 'uwpm_register_custom_element_section' ) ) { return; }

			$args = array(
				'label' => 'Appointment Scheduling Tags'
			);

			uwpm_register_custom_element_section( 'ewd_uasp_uwpm_elements', $args );
		}

		/**
		 * Adds in tags for appointment data and links
		 * @since 2.0.0
		 */
		public function add_elements() { 
			global $ewd_uasp_controller;

			if ( ! function_exists( 'uwpm_register_custom_element' ) ) { return; }
			
			$args = array(
				'label' 			=> 'Appointment Time',
				'callback_function' => 'ewd_uasp_get_appointment_time',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_appointment_time', $args );

			$args = array(
				'label' 			=> 'Client Name',
				'callback_function' => 'ewd_uasp_get_client_name',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_client_name', $args );

			$args = array(
				'label' 			=> 'Client Phone',
				'callback_function' => 'ewd_uasp_get_client_phone',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_client_phone', $args );

			$args = array(
				'label' 			=> 'Client Email',
				'callback_function' => 'ewd_uasp_get_client_email',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_client_email', $args );

			$args = array(
				'label' 			=> 'Location',
				'callback_function' => 'ewd_uasp_get_location',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_location', $args );

			$args = array(
				'label' 			=> 'Service',
				'callback_function' => 'ewd_uasp_get_service',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_service', $args );

			$args = array(
				'label' 			=> 'Service Provider',
				'callback_function' => 'ewd_uasp_get_service_provider',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_service_provider', $args );

			$args = array(
				'label' 			=> 'Confirmation Link',
				'callback_function' => 'ewd_uasp_get_confirmation_link',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_confirmation_link', $args );

			$args = array(
				'label' 			=> 'Cancellation Link',
				'callback_function' => 'ewd_uasp_get_cancellation_link',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_cancellation_link', $args );

			$args = array(
				'label' 			=> 'Admin Appointment Link',
				'callback_function' => 'ewd_uasp_get_admin_appointment_link',
				'section' 			=> 'ewd_uasp_uwpm_elements'
			);

			uwpm_register_custom_element( 'ewd_uasp_admin_appointment_link', $args );

			foreach( ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) ) as $custom_field ) {

				$args = array(
					'label' 			=> $custom_field->name,
					'callback_function' => 'ewd_uasp_get_custom_field',
					'section' 			=> 'ewd_uasp_uwpm_elements'
				);

				uwpm_register_custom_element( 'ewd_uasp_' . $custom_field->slug, $args );
			}
		}
	}
}

/**
 * Returns the start time of the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_appointment_time( $params, $user ) {
	global $ewd_uasp_controller;

	return ! empty( $params['appointment_id'] ) ? $ewd_uasp_controller->appointment_manager->get_appointment_field( 'start', $params['appointment_id'] ) : '';
}

/**
 * Returns the client name of the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_client_name( $params, $user ) {
	global $ewd_uasp_controller;

	return ! empty( $params['appointment_id'] ) ? $ewd_uasp_controller->appointment_manager->get_appointment_field( 'client_name', $params['appointment_id'] ) : '';
}

/**
 * Returns the client phone number of the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_client_phone( $params, $user ) {
	global $ewd_uasp_controller;

	return ! empty( $params['appointment_id'] ) ? $ewd_uasp_controller->appointment_manager->get_appointment_field( 'client_phone', $params['appointment_id'] ) : '';
}

/**
 * Returns the client email of the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_client_email( $params, $user ) {
	global $ewd_uasp_controller;

	return ! empty( $params['appointment_id'] ) ? $ewd_uasp_controller->appointment_manager->get_appointment_field( 'client_email', $params['appointment_id'] ) : '';
}

/**
 * Returns the location name of the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_location( $params, $user ) {
	global $ewd_uasp_controller;

	return ! empty( $params['appointment_id'] ) ? $ewd_uasp_controller->appointment_manager->get_appointment_field( 'location_name', $params['appointment_id'] ) : '';
}

/**
 * Returns the service name of the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_service( $params, $user ) {
	global $ewd_uasp_controller;

	return ! empty( $params['appointment_id'] ) ? $ewd_uasp_controller->appointment_manager->get_appointment_field( 'service_name', $params['appointment_id'] ) : '';
}

/**
 * Returns the service provider name of the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_service_provider( $params, $user ) {
	global $ewd_uasp_controller;

	return ! empty( $params['appointment_id'] ) ? $ewd_uasp_controller->appointment_manager->get_appointment_field( 'provider_name', $params['appointment_id'] ) : '';
}

/**
 * Returns a confirmation link for the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_confirmation_link( $params, $user ) {
	global $ewd_uasp_controller;

	if ( empty( $params['appointment_id'] ) ) { return ''; }

	$args = array( 
		'appointment_id' 	=> intval( $params['appointment_id'] ),
		'email' 			=> $ewd_uasp_controller->appointment_manager->get_appointment_field( 'client_email', $params['appointment_id'] ),
		'action'			=> 'confirm_appointment'
	);

	$confirmation_url = add_query_arg( $args, $ewd_uasp_controller->settings->get_setting( 'appointment-booking-page' ) );	

	return '<table><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><a href="' . $confirmation_url . '" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">' . __('Confirm Appointment', 'ultimate-appointment-scheduling') . '</a></td></tr></table>';
}

/**
 * Returns a cancellation link for the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_cancellation_link( $params, $user ) {
	global $ewd_uasp_controller;

	if ( empty( $params['appointment_id'] ) ) { return ''; }

	$args = array( 
		'appointment_id' 	=> intval( $params['appointment_id'] ),
		'email' 			=> $ewd_uasp_controller->appointment_manager->get_appointment_field( 'client_email', $params['appointment_id'] ),
		'action'			=> 'cancel_appointment'
	);

	$cancellation_url = add_query_arg( $args, $ewd_uasp_controller->settings->get_setting( 'appointment-booking-page' ) );	

	return '<table><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><a href="' . $cancellation_url . '" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">' . __('Cancel Appointment', 'ultimate-appointment-scheduling') . '</a></td></tr></table>';
}

/**
 * Returns a link to view the specified appointment in the admin area
 * @since 2.0.0
 */
function ewd_uasp_get_admin_appointment_link( $params, $user ) {
	global $ewd_uasp_controller;

	if ( empty( $params['appointment_id'] ) ) { return ''; }

	$args = array( 
		'appointment_id' 	=> intval( $params['appointment_id'] ),
	);

	$admin_appointment_url = add_query_arg( $args, get_site_url() . 'wp-admin/admin.php?page=ewd-uasp-appointments' );	

	return '<table><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><a href="' . $admin_appointment_url . '" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">' . __('View Appointment', 'ultimate-appointment-scheduling') . '</a></td></tr></table>';
}

/**
 * Returns the value for the specified custom field for the specified appointment
 * @since 2.0.0
 */
function ewd_uasp_get_custom_field( $params, $user ) {
	global $ewd_uasp_controller;

	if ( empty( $params['appointment_id'] ) or empty( $params['replace_slug'] ) ) { return; }

	$custom_fields = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );

	$field_id = 0;

	foreach ( $custom_fields as $custom_field ) { 

		if ( $custom_field->slug == $params['replace_slug'] ) { $field_id = $custom_field->id; break; }
	}

	return $ewd_uasp_controller->appointment_manager->get_field_value( $field_id, $params['appointment_id'] );
}