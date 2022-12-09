<?php

/**
 * Base class for any view requested on the front end.
 *
 * @since 2.0.0
 */
class ewduaspView extends ewduaspBase {

	/**
	 * Post type to render
	 */
	public $post_type = null;

	/**
	 * Map types of content to the template which will render them
	 */
	public $content_map = array(
		'title'							 => 'content/title',
	);

	/**
	 * Initialize the class
	 * @since 2.0.0
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );
		
		// Filter the content map so addons can customize what and how content
		// is output. Filters are specific to each view, so for this base view
		// you would use the filter 'us_content_map_ewduaspView'
		$this->content_map = apply_filters( 'ewd_uasp_content_map_' . get_class( $this ), $this->content_map );

	}

	/**
	 * Render the view and enqueue required stylesheets
	 *
	 * @note This function should always be overridden by an extending class
	 * @since 2.0.0
	 */
	public function render() {

		$this->set_error(
			array( 
				'type'		=> 'render() called on wrong class'
			)
		);
	}

	/**
	 * Load a template file for views
	 *
	 * First, it looks in the current theme's /ewd-uasp-templates/ directory. Then it
	 * will check a parent theme's /ewd-uasp-templates/ directory. If nothing is found
	 * there, it will retrieve the template from the plugin directory.

	 * @since 2.0.0
	 * @param string template Type of template to load (eg - reviews, review)
	 */
	function find_template( $template ) {

		$this->template_dirs = array(
			get_stylesheet_directory() . '/' . EWD_UASP_TEMPLATE_DIR . '/',
			get_template_directory() . '/' . EWD_UASP_TEMPLATE_DIR . '/',
			EWD_UASP_PLUGIN_DIR . '/' . EWD_UASP_TEMPLATE_DIR . '/'
		);
		
		$this->template_dirs = apply_filters( 'ewd_uasp_template_directories', $this->template_dirs );

		foreach ( $this->template_dirs as $dir ) {
			if ( file_exists( $dir . $template . '.php' ) ) {
				return $dir . $template . '.php';
			}
		}

		return false;
	}

	/**
	 * Enqueue stylesheets
	 */
	public function enqueue_assets() {

		//enqueue assets here
	}

	public function get_option( $option_name ) {
		global $ewd_uasp_controller;

		return ! empty( $this->$option_name ) ? $this->$option_name : $ewd_uasp_controller->settings->get_setting( $option_name );
	}

	public function get_label( $label_name ) {
		global $ewd_uasp_controller;

		if ( empty( $this->label_defaults ) ) { $this->set_label_defaults(); }

		return ! empty( $ewd_uasp_controller->settings->get_setting( $label_name ) ) ? $ewd_uasp_controller->settings->get_setting( $label_name ) : $this->label_defaults[ $label_name ];
	}

	public function set_label_defaults() {

		$this->label_defaults = array(
			'label-sign-up-title'					=> __( 'Sign Up', 'ultimate-appointment-scheduling' ),
			'label-name'							=> __( 'Name', 'ultimate-appointment-scheduling' ),
			'label-phone'							=> __( 'Phone', 'ultimate-appointment-scheduling' ),
			'label-email'							=> __( 'Email', 'ultimate-appointment-scheduling' ),
			'label-service-title'					=> __( 'Service', 'ultimate-appointment-scheduling' ),
			'label-location'						=> __( 'Location', 'ultimate-appointment-scheduling' ),
			'label-service'							=> __( 'Service', 'ultimate-appointment-scheduling' ),
			'label-service-provider'				=> __( 'Service Provider', 'ultimate-appointment-scheduling' ),
			'label-any'								=> __( 'Any', 'ultimate-appointment-scheduling' ),
			'label-appointment-title'				=> __( 'Appointment', 'ultimate-appointment-scheduling' ),
			'label-appointment-date'				=> __( 'Appointment Date', 'ultimate-appointment-scheduling' ),
			'label-find-appointment'				=> __( 'Find Appointments', 'ultimate-appointment-scheduling' ),
			'label-book-appointment'				=> __( 'Book Appointment', 'ultimate-appointment-scheduling' ),
			'label-pay-in-advance'					=> __( 'Pay in Advance', 'ultimate-appointment-scheduling' ),
			'label-proceed-to-payment'				=> __( 'Proceed to Payment', 'ultimate-appointment-scheduling' ),
			'label-select-time'						=> __( 'Select Time', 'ultimate-appointment-scheduling' ),
			'label-click-select-date'				=> __( 'Click here to select a date', 'ultimate-appointment-scheduling' ),
			'label-image-number'					=> __( 'Image Required', 'ultimate-appointment-scheduling' ),
			'label-payment-required'				=> __( 'Use the button below to pay for your appointment.', 'ultimate-appointment-scheduling' ),
			'label-thank-you-submit'				=> __( 'Thank you, your appointment has been successfully created.', 'ultimate-appointment-scheduling' ),
		);
	}

	public function add_custom_styling() {
		global $ewd_uasp_controller;

		echo '<style>';
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-signup-title-font-color' ) != '' ) { echo '.ewd-uasp-registration-form-label { color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-title-font-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-signup-title-font' ) != '' ) { echo '.ewd-uasp-registration-form-label { font-family: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-title-font' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-signup-title-font-size' ) != '' ) { echo '.ewd-uasp-registration-form-label { font-size: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-title-font-size' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-signup-block-color' ) != '' ) { 
				echo '.ewd-uasp-registration-form-label { background-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-block-color' ) . ' !important; }'; 
				echo '.ewd-uasp-registration-form { border-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-block-color' ) . ' !important; }'; 
			}
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-signup-block-margin' ) != '' ) { echo '.ewd-uasp-registration-form { margin: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-block-margin' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-signup-block-padding' ) != '' ) { echo '.ewd-uasp-registration-form { padding: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-block-padding' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-signup-label-font' ) != '' ) { echo '.ewd-uasp-registration-form-content { font-family: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-label-font' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-signup-label-font-size' ) != '' ) { echo '.ewd-uasp-registration-form-content { font-size: ' . $ewd_uasp_controller->settings->get_setting( 'styling-signup-label-font-size' ) . ' !important; }'; }

			if ( $ewd_uasp_controller->settings->get_setting( 'styling-service-title-font-color' ) != '' ) { echo '.ewd-uasp-service-label { color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-title-font-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-service-title-font' ) != '' ) { echo '.ewd-uasp-service-label { font-family: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-title-font' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-service-title-font-size' ) != '' ) { echo '.ewd-uasp-service-label { font-size: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-title-font-size' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-service-block-color' ) != '' ) { 
				echo '.ewd-uasp-service-label { background-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-block-color' ) . ' !important; }'; 
				echo '.ewd-uasp-service { border-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-block-color' ) . ' !important; }'; 
			}
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-service-block-margin' ) != '' ) { echo '.ewd-uasp-service { margin: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-block-margin' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-service-block-padding' ) != '' ) { echo '.ewd-uasp-service { padding: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-block-padding' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-service-label-font' ) != '' ) { echo '.ewd-uasp-service-content { font-family: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-label-font' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-service-label-font-size' ) != '' ) { echo '.ewd-uasp-service-content { font-size: ' . $ewd_uasp_controller->settings->get_setting( 'styling-service-label-font-size' ) . ' !important; }'; }

			if ( $ewd_uasp_controller->settings->get_setting( 'styling-appointment-title-font-color' ) != '' ) { echo '.ewd-uasp-find-appointment-label { color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-title-font-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-appointment-title-font' ) != '' ) { echo '.ewd-uasp-find-appointment-label { font-family: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-title-font' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-appointment-title-font-size' ) != '' ) { echo '.ewd-uasp-find-appointment-label { font-size: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-title-font-size' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-appointment-block-color' ) != '' ) { 
				echo '.ewd-uasp-find-appointment-label { background-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-block-color' ) . ' !important; }'; 
				echo '.ewd-uasp-find-appointment { border-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-block-color' ) . ' !important; }'; 
			}
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-appointment-block-margin' ) != '' ) { echo '.ewd-uasp-find-appointment { margin: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-block-margin' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-appointment-block-padding' ) != '' ) { echo '.ewd-uasp-find-appointment { padding: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-block-padding' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-appointment-label-font' ) != '' ) { echo '.ewd-uasp-find-appointment-content { font-family: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-label-font' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-appointment-label-font-size' ) != '' ) { echo '.ewd-uasp-find-appointment-content { font-size: ' . $ewd_uasp_controller->settings->get_setting( 'styling-appointment-label-font-size' ) . ' !important; }'; }

			if ( $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-color' ) != '' ) { echo '.fc-title, .fc-time { color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-font' ) != '' ) { echo '.fc-title, .fc-time { font-family: ' . $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-font' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-font-size' ) != '' ) { echo '.fc-title, .fc-time { font-size: ' . $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-font-size' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-bg-color' ) != '' ) { echo '.fc-event { background-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-bg-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-border-color' ) != '' ) { echo '.fc-event { border-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-border-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-selected-bg-color' ) != '' ) { echo '.ewd-uasp-selected-event { background-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-selected-bg-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-selected-border-color' ) != '' ) { echo '.ewd-uasp-selected-event { border-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-calendar-appt-selected-border-color' ) . ' !important; }'; }

			if ( $ewd_uasp_controller->settings->get_setting( 'styling-button-font-color' ) != '' ) { echo '.ewd-uasp-book-button-container input[type="submit"], button#ewd-uasp-find-appointment, .ewd-uasp-edit-appointment-toggle, .ewd-uasp-edit-appointment input[type="submit"] { color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-button-font-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-button-color' ) != '' ) { echo '.ewd-uasp-book-button-container input[type="submit"], button#ewd-uasp-find-appointment, .ewd-uasp-edit-appointment-toggle, .ewd-uasp-edit-appointment input[type="submit"] { background-color: ' . $ewd_uasp_controller->settings->get_setting( 'styling-button-color' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-button-font' ) != '' ) { echo '.ewd-uasp-book-button-container input[type="submit"], button#ewd-uasp-find-appointment, .ewd-uasp-edit-appointment-toggle, .ewd-uasp-edit-appointment input[type="submit"] { font-family: ' . $ewd_uasp_controller->settings->get_setting( 'styling-button-font' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-button-font-size' ) != '' ) { echo '.ewd-uasp-book-button-container input[type="submit"], button#ewd-uasp-find-appointment, .ewd-uasp-edit-appointment-toggle, .ewd-uasp-edit-appointment input[type="submit"] { font-size: ' . $ewd_uasp_controller->settings->get_setting( 'styling-button-font-size' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-button-margin' ) != '' ) { echo '.ewd-uasp-book-button-container input[type="submit"], button#ewd-uasp-find-appointment, .ewd-uasp-edit-appointment-toggle, .ewd-uasp-edit-appointment input[type="submit"] { margin: ' . $ewd_uasp_controller->settings->get_setting( 'styling-button-margin' ) . ' !important; }'; }
			if ( $ewd_uasp_controller->settings->get_setting( 'styling-button-padding' ) != '' ) { echo '.ewd-uasp-book-button-container input[type="submit"], button#ewd-uasp-find-appointment, .ewd-uasp-edit-appointment-toggle, .ewd-uasp-edit-appointment input[type="submit"] { padding: ' . $ewd_uasp_controller->settings->get_setting( 'styling-button-padding' ) . ' !important; }'; }

			echo $ewd_uasp_controller->settings->get_setting( 'custom-css' );

		echo  '</style>';
	}

}
