<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduaspSettings' ) ) {
/**
 * Class to handle configurable settings for Ultimate Appointment Scheduling
 * @since 2.0.0
 */
class ewduaspSettings {

	public $currency_options = array();

	public $language_options = array();

	public $hour_options = array();

	public $email_options = array();

	/**
	 * Default values for settings
	 * @since 2.0.0
	 */
	public $defaults = array();

	/**
	 * Stored values for settings
	 * @since 2.0.0
	 */
	public $settings = array();

	public function __construct() {

		add_action( 'init', array( $this, 'set_defaults' ) );

		add_action( 'init', array( $this, 'set_field_options' ) );

		add_action( 'init', array( $this, 'load_settings_panel' ) );
	}

	/**
	 * Load the plugin's default settings
	 * @since 2.0.0
	 */
	public function set_defaults() {

		$this->defaults = array(

			'date-format'					=> 'Y-m-d H:i:s',
			'required-information'			=> array( 'name', 'email' ),
			'calendar-language' => 'en',

			'time-between-appointments' => '30',

			'maximum-days-advance'			=> 3650,

			'booking-form-style'			=> 'standard',
			'access-role'					=> 'manage_options',

			'custom-fields'					=> array(),

			'paypal-prepayment'				=> 'none',
			'currency-code'					=> 'USD',

			'reminders-cache-time'			=> 10,

			'email-messages'				=> array(),

			'label-any'						=> __( 'Any', 'ultimate-appointment-scheduling' ),
			'label-thank-you-submit'		=> __( 'Thank you, your appointment has been successfully created.', 'ultimate-appointment-scheduling' ),
		);

		$this->defaults = apply_filters( 'ewd_uasp_defaults', $this->defaults, $this );
	}

	/**
	 * Put all of the available possible select options into key => value arrays
	 * @since 2.0.0
	 */
	public function set_field_options() {

		$this->currency_options = array(
			'AUD' => __( 'Australian Dollar', 'ultimate-appointment-scheduling'),
			'BRL' => __( 'Brazilian Real', 'ultimate-appointment-scheduling'),
			'CAD' => __( 'Canadian Dollar', 'ultimate-appointment-scheduling'),
			'CZK' => __( 'Czech Koruna', 'ultimate-appointment-scheduling'),
			'DKK' => __( 'Danish Krone', 'ultimate-appointment-scheduling'),
			'EUR' => __( 'Euro', 'ultimate-appointment-scheduling'),
			'HKD' => __( 'Hong Kong Dollar', 'ultimate-appointment-scheduling'),
			'HUF' => __( 'Hungarian Forint', 'ultimate-appointment-scheduling'),
			'ILS' => __( 'Israeli New Sheqel', 'ultimate-appointment-scheduling'),
			'JPY' => __( 'Japanese Yen', 'ultimate-appointment-scheduling'),
			'MYR' => __( 'Malaysian Ringgit', 'ultimate-appointment-scheduling'),
			'MXN' => __( 'Mexican Peso', 'ultimate-appointment-scheduling'),
			'NOK' => __( 'Norwegian Krone', 'ultimate-appointment-scheduling'),
			'NZD' => __( 'New Zealand Dollar', 'ultimate-appointment-scheduling'),
			'PHP' => __( 'Philippine Peso', 'ultimate-appointment-scheduling'),
			'PLN' => __( 'Polish Zloty', 'ultimate-appointment-scheduling'),
			'GBP' => __( 'Pound Sterling', 'ultimate-appointment-scheduling'),
			'RUB' => __( 'Russian Ruble', 'ultimate-appointment-scheduling'),
			'SGD' => __( 'Singapore Dollar', 'ultimate-appointment-scheduling'),
			'SEK' => __( 'Swedish Krona', 'ultimate-appointment-scheduling'),
			'CHF' => __( 'Swiss Franc', 'ultimate-appointment-scheduling'),
			'TWD' => __( 'Taiwan New Dollar', 'ultimate-appointment-scheduling'),
			'THB' => __( 'Thai Baht', 'ultimate-appointment-scheduling'),
			'TRY' => __( 'Turkish Lira', 'ultimate-appointment-scheduling'),
			'USD' => __( 'U.S. Dollar', 'ultimate-appointment-scheduling'),
		);

		$this->language_options = array(
			'en' 		=> __( 'English', 'ultimate-appointment-scheduling' ),
			'af' 		=> __( 'Afrikaans', 'ultimate-appointment-scheduling' ),
			'ar' 		=> __( 'Arabic', 'ultimate-appointment-scheduling' ),
			'ar-dz' 	=> __( 'Arabic (Algeria)', 'ultimate-appointment-scheduling' ),
			'ar-kw' 	=> __( 'Arabic (Kuwait)', 'ultimate-appointment-scheduling' ),
			'ar-ly' 	=> __( 'Arabic (Libya)', 'ultimate-appointment-scheduling' ),
			'ar-ma' 	=> __( 'Arabic (Morocco)', 'ultimate-appointment-scheduling' ),
			'ar-sa' 	=> __( 'Arabic (Saudi Arabia)', 'ultimate-appointment-scheduling' ),
			'ar-tn' 	=> __( 'Arabic (Tunisia)', 'ultimate-appointment-scheduling' ),
			'bg' 		=> __( 'Bulgarian', 'ultimate-appointment-scheduling' ),
			'ca' 		=> __( 'Catalan', 'ultimate-appointment-scheduling' ),
			'cs' 		=> __( 'Czech', 'ultimate-appointment-scheduling' ),
			'da' 		=> __( 'Danish', 'ultimate-appointment-scheduling' ),
			'de' 		=> __( 'German', 'ultimate-appointment-scheduling' ),
			'de-at' 	=> __( 'German (Austria)', 'ultimate-appointment-scheduling' ),
			'de-ch' 	=> __( 'German (Switzerland)', 'ultimate-appointment-scheduling' ),
			'el' 		=> __( 'Greek', 'ultimate-appointment-scheduling' ),
			'en-au' 	=> __( 'English (Australia)', 'ultimate-appointment-scheduling' ),
			'en-ca' 	=> __( 'English (Canada)', 'ultimate-appointment-scheduling' ),
			'en-gb' 	=> __( 'English (Great Britain)', 'ultimate-appointment-scheduling' ),
			'en-ie' 	=> __( 'English (Ireland)', 'ultimate-appointment-scheduling' ),
			'en-nz' 	=> __( 'English (New Zealand)', 'ultimate-appointment-scheduling' ),
			'es' 		=> __( 'Spanish', 'ultimate-appointment-scheduling' ),
			'es-do' 	=> __( 'Spanish (Dominican Republic)', 'ultimate-appointment-scheduling' ),
			'es-us' 	=> __( 'Spanish (United States)', 'ultimate-appointment-scheduling' ),
			'et' 		=> __( 'Estonian', 'ultimate-appointment-scheduling' ),
			'eu' 		=> __( 'Basque', 'ultimate-appointment-scheduling' ),
			'fa' 		=> __( 'Persian (Farsi)', 'ultimate-appointment-scheduling' ),
			'fi' 		=> __( 'Finnish', 'ultimate-appointment-scheduling' ),
			'fr' 		=> __( 'French', 'ultimate-appointment-scheduling' ),
			'fr-ca' 	=> __( 'French (Canada)', 'ultimate-appointment-scheduling' ),
			'fr-ch' 	=> __( 'French (Switzerland)', 'ultimate-appointment-scheduling' ),
			'gl' 		=> __( 'Galician', 'ultimate-appointment-scheduling' ),
			'he' 		=> __( 'Hebrew', 'ultimate-appointment-scheduling' ),
			'hi' 		=> __( 'Hindi', 'ultimate-appointment-scheduling' ),
			'hr' 		=> __( 'Croatian', 'ultimate-appointment-scheduling' ),
			'hu' 		=> __( 'Hungarian', 'ultimate-appointment-scheduling' ),
			'id' 		=> __( 'Indonesian', 'ultimate-appointment-scheduling' ),
			'is' 		=> __( 'Icelandic', 'ultimate-appointment-scheduling' ),
			'it' 		=> __( 'Italian', 'ultimate-appointment-scheduling' ),
			'ja' 		=> __( 'Japanese', 'ultimate-appointment-scheduling' ),
			'kk' 		=> __( 'Kazakh', 'ultimate-appointment-scheduling' ),
			'ko' 		=> __( 'Korean', 'ultimate-appointment-scheduling' ),
			'lb' 		=> __( 'Luxembourgish', 'ultimate-appointment-scheduling' ),
			'lt' 		=> __( 'Lithuanian', 'ultimate-appointment-scheduling' ),
			'lv' 		=> __( 'Latvian (Lettish)', 'ultimate-appointment-scheduling' ),
			'mk' 		=> __( 'Macedonian', 'ultimate-appointment-scheduling' ),
			'ms' 		=> __( 'Malay', 'ultimate-appointment-scheduling' ),
			'ms-my' 	=> __( 'Malay (Malaysia)', 'ultimate-appointment-scheduling' ),
			'nb' 		=> __( 'Norwegian bokmål', 'ultimate-appointment-scheduling' ),
			'nl' 		=> __( 'Dutch', 'ultimate-appointment-scheduling' ),
			'nl-be' 	=> __( 'Dutch (Belgium)', 'ultimate-appointment-scheduling' ),
			'nn' 		=> __( 'Norwegian nynorsk', 'ultimate-appointment-scheduling' ),
			'pl' 		=> __( 'Polish', 'ultimate-appointment-scheduling' ),
			'pt' 		=> __( 'Portuguese', 'ultimate-appointment-scheduling' ),
			'pt-br' 	=> __( 'Portuguese (Brazil)', 'ultimate-appointment-scheduling' ),
			'ro' 		=> __( 'Romanian', 'ultimate-appointment-scheduling' ),
			'ru' 		=> __( 'Russian', 'ultimate-appointment-scheduling' ),
			'sk' 		=> __( 'Slovak', 'ultimate-appointment-scheduling' ),
			'sl' 		=> __( 'Slovenian', 'ultimate-appointment-scheduling' ),
			'sq' 		=> __( 'Albanian', 'ultimate-appointment-scheduling' ),
			'sr' 		=> __( 'Serbian', 'ultimate-appointment-scheduling' ),
			'sr-cyrl' 	=> __( 'Serbian (Cyrillic)', 'ultimate-appointment-scheduling' ),
			'sv' 		=> __( 'Swedish', 'ultimate-appointment-scheduling' ),
			'th' 		=> __( 'Thai', 'ultimate-appointment-scheduling' ),
			'tr' 		=> __( 'Turkish', 'ultimate-appointment-scheduling' ),
			'uk' 		=> __( 'Ukrainian', 'ultimate-appointment-scheduling' ),
			'vi' 		=> __( 'Vietnamese', 'ultimate-appointment-scheduling' ),
			'zh-cn' 	=> __( 'Chinese', 'ultimate-appointment-scheduling' ),
			'zh-tw' 	=> __( 'Chinese (Taiwan)', 'ultimate-appointment-scheduling' ),
		);

		for ( $i = 0; $i < 24; $i++ ) {

			$this->hour_options[ $i ] = $i . ':00';
		}

		$emails = ewd_uasp_decode_infinite_table_setting( $this->get_setting( 'email-messages-array' ) );

		foreach ( $emails as $email ) { 

			$this->email_options[ $email->id ] = $email->name;
		}

		if ( in_array( 'ultimate-wp-mail/Main.php', (array) get_option( 'active_plugins' ) ) ) {

			$this->email_options[-1] = '';
			
			$args = array(
				'post_type'		=> 'uwpm_mail_template',
				'numberposts'	=> -1
			);

			$uwpm_emails = get_posts( $args );

			foreach ( $uwpm_emails as $uwpm_email ) { 

				$email_id = $uwpm_email->ID * -1;

				$this->email_options[ $email_id ] = $uwpm_email->post_title;
			}
		}
	}

	/**
	 * Get a setting's value or fallback to a default if one exists
	 * @since 2.0.0
	 */
	public function get_setting( $setting ) { 

		if ( empty( $this->settings ) ) {
			$this->settings = get_option( 'ewd-uasp-settings' );
		}
		
		if ( ! empty( $this->settings[ $setting ] ) ) {
			return apply_filters( 'ewd-uasp-settings-' . $setting, $this->settings[ $setting ] );
		}

		if ( ! empty( $this->defaults[ $setting ] ) ) { 
			return apply_filters( 'ewd-uasp-settings-' . $setting, $this->defaults[ $setting ] );
		}

		return apply_filters( 'ewd-uasp-settings-' . $setting, null );
	}

	/**
	 * Set a setting to a particular value
	 * @since 2.0.0
	 */
	public function set_setting( $setting, $value ) {

		$this->settings[ $setting ] = $value;
	}

	/**
	 * Save all settings, to be used with set_setting
	 * @since 2.0.0
	 */
	public function save_settings() {
		
		update_option( 'ewd-uasp-settings', $this->settings );
	}

	/**
	 * Load the admin settings page
	 * @since 2.0.0
	 * @sa https://github.com/NateWr/simple-admin-pages
	 */
	public function load_settings_panel() {

		global $ewd_uasp_controller;

		require_once( EWD_UASP_PLUGIN_DIR . '/lib/simple-admin-pages/simple-admin-pages.php' );
		$sap = sap_initialize_library(
			$args = array(
				'version'       => '2.6.3',
				'lib_url'       => EWD_UASP_PLUGIN_URL . '/lib/simple-admin-pages/',
				'theme'			=> 'purple',
			)
		);
		
		$sap->add_page(
			'submenu',
			array(
				'id'            => 'ewd-uasp-settings',
				'title'         => __( 'Settings', 'ultimate-appointment-scheduling' ),
				'menu_title'    => __( 'Settings', 'ultimate-appointment-scheduling' ),
				'parent_menu'	=> 'ewd-uasp-appointments',
				'description'   => '',
				'capability'    => $this->get_setting( 'access-role' ),
				'default_tab'   => 'ewd-uasp-basic-tab',
			)
		);

		$sap->add_section(
			'ewd-uasp-settings',
			array(
				'id'            => 'ewd-uasp-basic-tab',
				'title'         => __( 'Basic', 'ultimate-appointment-scheduling' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-uasp-settings',
			array(
				'id'            => 'ewd-uasp-general',
				'title'         => __( 'General', 'ultimate-appointment-scheduling' ),
				'tab'	        => 'ewd-uasp-basic-tab',
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'warningtip',
			array(
				'id'			=> 'shortcodes-reminder',
				'title'			=> __( 'REMINDER:', 'ultimate-appointment-scheduling' ),
				'placeholder'	=> __( 'REMINDERS NEED TO GO HERE' )
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'textarea',
			array(
				'id'			=> 'custom-css',
				'title'			=> __( 'Custom CSS', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'You can add custom CSS styles to your appointment booking page in the box above.', 'ultimate-appointment-scheduling' ),			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'toggle',
			array(
				'id'			=> 'disable-appointment-editing',
				'title'			=> __( 'Disable Appointment Editing', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'Should guests be blocked from editing appointments that have been previously made?', 'ultimate-appointment-scheduling' )
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'toggle',
			array(
				'id'			=> 'multi-step-booking',
				'title'			=> __( 'Multi-Step Booking', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'Should booking an appointment be split into multiple steps or happen all in one place?', 'ultimate-appointment-scheduling' )
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'text',
			array(
				'id'            => 'time-between-appointments',
				'title'         => __( 'Time Between Appointments', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'How much time should there be between scheduled appointments? (in minutes)', 'ultimate-appointment-scheduling' ),
				'small'			=> true
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'checkbox',
			array(
				'id'			=> 'required-information',
				'title'			=> __( 'Required Information', 'ultimate-appointment-scheduling' ),
				'description'	=> '',
				'options'		=> array(
					'name' 			=> 'Name',
					'phone'			=> 'Phone',
					'email'			=> 'Email',
				)
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'select',
			array(
				'id'            => 'hours-format',
				'title'         => __( 'Hours Format', 'ultimate-appointment-scheduling' ),
				'description'   => '', 
				'blank_option'	=> false,
				'options'       => array(
					'24'			=> __( '24 Hour', 'ultimate-appointment-scheduling' ),
					'12'			=> __( '12 Hour', 'ultimate-appointment-scheduling' ),
				)
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'text',
			array(
				'id'            => 'date-format',
				'title'         => __( 'PHP Date Format', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'Use this field to specify a PHP date format to be used when the date is included (e.g. in emails). More info about PHP date formats can be found <a href="https://secure.php.net/manual/en/function.date.php" target="_blank">here</a>.', 'ultimate-appointment-scheduling' ),
				'small'			=> true
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'select',
			array(
				'id'            => 'client-email-details',
				'title'         => __( 'Client Email Details', 'ultimate-appointment-scheduling' ),
				'description'   => __( 'What email, if any, should be sent to clients when they book an appointment?', 'ultimate-appointment-scheduling' ), 
				'blank_option'	=> true,
				'options'       => $this->email_options
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'count',
			array(
				'id'			=> 'minimum-days-advance',
				'title'			=> __( 'Minimum Days in Advance', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'What is the minimum number of days in advance an appointment can be booked? <strong>(Leave blank if using the Minimum Hours setting below.)</strong>', 'ultimate-appointment-scheduling' ),
				'blank_option'	=> true,
				'min_value'		=> 1,
				'max_value'		=> 100,
				'increment'		=> 1,
				'units'			=> array( 'days' => 'Days' )
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'count',
			array(
				'id'			=> 'minimum-hours-advance',
				'title'			=> __( 'Minimum Hours in Advance', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'What is the minimum number of hours in advance an appointment can be booked? <strong>(Leave blank if using the Minimum Days setting above.)</strong>', 'ultimate-appointment-scheduling' ),
				'blank_option'	=> true,
				'min_value'		=> 1,
				'max_value'		=> 48,
				'increment'		=> 1,
				'units'			=> array( 'hours' => 'Hours' )
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'count',
			array(
				'id'			=> 'maximum-days-advance',
				'title'			=> __( 'Maximum Days in Advance', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'What is the maximum number of days in advance an appointment can be booked?', 'ultimate-appointment-scheduling' ),
				'blank_option'	=> false,
				'min_value'		=> 1,
				'max_value'		=> 365,
				'increment'		=> 1,
				'units'			=> array( 'days' => 'Days' )
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'select',
			array(
				'id'            => 'calendar-starting-layout',
				'title'         => __( 'Calendar Starting Layout', 'ultimate-appointment-scheduling' ),
				'description'   => __( 'What layout should the calendar start in?', 'ultimate-appointment-scheduling' ), 
				'blank_option'	=> false,
				'options'       => array(
					'agendaDay'		=> __( 'Day', 'ultimate-appointment-scheduling' ),
					'agendaWeek'	=> __( 'Week', 'ultimate-appointment-scheduling' ),
					'month'			=> __( 'Month', 'ultimate-appointment-scheduling' ),
					'listWeek'		=> __( 'List', 'ultimate-appointment-scheduling' ),
				)
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'select',
			array(
				'id'            => 'calendar-starting-time',
				'title'         => __( 'Calendar Starting Hour', 'ultimate-appointment-scheduling' ),
				'description'   => __( 'What should the default first/top time be in the calendar (e.g. if you\'re in the daily view)?', 'ultimate-appointment-scheduling' ), 
				'blank_option'	=> false,
				'options'       => $this->hour_options
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'count',
			array(
				'id'			=> 'calendar-offset',
				'title'			=> __( 'Calendar Offset', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'Use this option so specify how far ahead (in days, weeks or months) the default opening date of the calendar will be', 'ultimate-appointment-scheduling' ),
				'blank_option'	=> false,
				'min_value'		=> 1,
				'max_value'		=> 30,
				'increment'		=> 1,
				'units'			=> array( 
					'offsetDay' 	=> 'Days', 
					'offsetWeek' 	=> 'Weeks', 
					'offsetMonth' 	=> 'Months', 
				)
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-general',
			'select',
			array(
				'id'            => 'calendar-language',
				'title'         => __( 'Calendar Language', 'ultimate-appointment-scheduling' ),
				'description'   => __( 'Select the language of the calendar in the calendar style booking form.', 'ultimate-appointment-scheduling' ), 
				'blank_option'	=> false,
				'options'       => $this->language_options
			)
		);

		$sap->add_section(
			'ewd-uasp-settings',
			array(
				'id'            => 'ewd-uasp-reminders-tab',
				'title'         => __( 'Reminders', 'ultimate-appointment-scheduling' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-uasp-settings',
			array(
				'id'            => 'ewd-uasp-reminders',
				'title'         => __( 'Reminder Options', 'ultimate-appointment-scheduling' ),
				'tab'	        => 'ewd-uasp-reminders-tab',
			)
		);

		$reminders_description = __( 'Should reminders be sent about appointments?', 'ultimate-appointment-scheduling' ) . '<br />';
		$reminders_description .= __( 'Note: Times are approximate, and depend on site traffic.', 'ultimate-appointment-scheduling' ) . '<br />';

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-reminders',
			'infinite_table',
			array(
				'id'			=> 'email-reminders',
				'title'			=> __( 'Appointment Email Reminders', 'ultimate-appointment-scheduling' ),
				'add_label'		=> __( '+ ADD', 'ultimate-appointment-scheduling' ),
				'del_label'		=> __( 'Delete', 'ultimate-appointment-scheduling' ),
				'description'	=> $reminders_description,
				'fields'		=> array(
					'id' => array(
						'type' 		=> 'hidden',
						'label' 	=> 'Reminder ID',
						'required' 	=> true
					),
					'interval' => array(
						'type' 		=> 'text',
						'label' 	=> 'Interval',
						'required' 	=> true
					),
					'unit' => array(
						'type' 		=> 'select',
						'label' 	=> __( 'Unit', 'ultimate-appointment-scheduling' ),
						'options' 	=> array(
							'minutes'	=> __( 'Minute(s)', 'ultimate-appointment-scheduling' ),
							'hours'		=> __( 'Hour(s)', 'ultimate-appointment-scheduling' ),
							'days'		=> __( 'Day(s)', 'ultimate-appointment-scheduling' ),
						)
					),
					'email_id' => array(
						'type' 		=> 'select',
						'label' 	=> __( 'Email', 'ultimate-appointment-scheduling' ),
						'options' 	=> $this->email_options
					),
					'conditional' => array(
						'type' 		=> 'select',
						'label' 	=> __( 'Ignore Confirmed?', 'ultimate-appointment-scheduling' ),
						'options' 	=> array(
							'no'		=> __( 'No', 'ultimate-appointment-scheduling' ),
							'yes'		=> __( 'Yes', 'ultimate-appointment-scheduling' ),
						)
					),
				)
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-reminders',
			'toggle',
			array(
				'id'			=> 'appointment-confirmation',
				'title'			=> __( 'Appointment Confirmation', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'Should a link be included in reminder emails so that appointments can be confirmed? (Also requires filling out the "Appointment Booking Page" field below)', 'ultimate-appointment-scheduling' )
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-reminders',
			'text',
			array(
				'id'            => 'appointment-booking-page',
				'title'         => __( 'Appointment Booking Page', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'What\'s the URL of the page with the appointment booking form, if "Appointment Confirmation" is set to "Yes"?', 'ultimate-appointment-scheduling' ),
				'conditional_on'		=> 'appointment-confirmation',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-reminders',
			'count',
			array(
				'id'			=> 'reminders-cache-time',
				'title'			=> __( 'Reminders Cache Time', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'How often should the plugin check to see if there are new reminders to send out when a page is loaded?', 'ultimate-appointment-scheduling' ),
				'blank_option'	=> false,
				'min_value'		=> 1,
				'max_value'		=> 90,
				'increment'		=> 1,
				'units'			=> array( 'minutes' => 'Minutes' )
			)
		);

		$sap->add_section(
			'ewd-uasp-settings',
			array(
				'id'            => 'ewd-uasp-emails-tab',
				'title'         => __( 'Emails', 'ultimate-appointment-scheduling' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-uasp-settings',
			array(
				'id'            => 'ewd-uasp-emails',
				'title'         => __( 'Email Messages', 'ultimate-appointment-scheduling' ),
				'tab'	        => 'ewd-uasp-emails-tab',
			),
		);

		$email_messages_description = '<ul><li>';
		$email_messages_description .= __( 'Use the table above to build emails for your users.', 'ultimate-appointment-scheduling' );
		$email_messages_description .= '</li><li>';
		$email_messages_description .= __( 'You can use [section]...[/section] and [footer]...[/footer] to split up the content of your email. You can also include a link button, like so: [button link=\'LINK_URL_GOES_HERE\']BUTTON_TEXT[/button], and a link to review each individual item in an order with:[review-items link=\'LINK_URL_TO_SUBMIT_REVIEW_PAGE\']', 'ultimate-appointment-scheduling' );
		$email_messages_description .= '</li><li>';
		$email_messages_description .= __( 'You can also put [appointment-time], [client], [phone], [email], [location], [service], [service-provider], [cancellation-link] or [confirmation-link] (if "Appointment Confirmation" is enabled) into the message body or subject.', 'ultimate-appointment-scheduling' );
		$email_messages_description .= '</li><li>';
		$email_messages_description .= __( 'Use the area below to send yourself a sample email.', 'ultimate-appointment-scheduling' );
		$email_messages_description .= '</li></ul>';

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-emails',
			'infinite_table',
			array(
				'id'			=> 'email-messages-array',
				'title'			=> __( 'Email Messages', 'ultimate-appointment-scheduling' ),
				'add_label'		=> __( '+ ADD', 'ultimate-appointment-scheduling' ),
				'del_label'		=> __( 'Delete', 'ultimate-appointment-scheduling' ),
				'description'	=> $email_messages_description,
				'fields'		=> array(
					'id' => array(
						'type' 		=> 'hidden',
						'label' 	=> 'ID',
						'required' 	=> true
					),
					'name' => array(
						'type' 		=> 'text',
						'label' 	=> __( 'Email Name', 'ultimate-appointment-scheduling' ),
						'required' 	=> true
					),
					'subject' => array(
						'type' 		=> 'text',
						'label' 	=> __( 'Message Subject', 'ultimate-appointment-scheduling' ),
						'required' 	=> true
					),
					'message' => array(
						'type' 		=> 'textarea',
						'label' 	=> __( 'Message', 'ultimate-appointment-scheduling' ),
						'required' 	=> true
					)
				)
			)
		);

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-emails',
			'select',
			array(
				'id'            => 'send-sample-email-message',
				'title'         => __( 'Sample Email Message', 'ultimate-appointment-scheduling' ),
				'description'	=> __( 'Choose an email message to send as a sample.', 'ultimate-appointment-scheduling' ),
				'blank_option'	=> false,
				'options'       => $this->email_options
			)
		);

		$sample_email_address_description = __( 'Choose an email address to send the above sample email message to. Make sure that you click the "Save Changes" button below before sending the test message, to receive the most recent version of your email.', 'ultimate-appointment-scheduling' );
		$sample_email_address_description .= '<br><br>';
		$sample_email_address_description .= '<button type="button" class="ewd-uasp-send-test-email">Send Sample Email</button>';

		$sap->add_setting(
			'ewd-uasp-settings',
			'ewd-uasp-emails',
			'text',
			array(
				'id'            => 'send-sample-email-address',
				'title'         => __( 'Sample Email Address', 'ultimate-appointment-scheduling' ),
				'description'	=> $sample_email_address_description
			)
		);

		$sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'       => 'ewd-uasp-email-styling-tab-body',
	        'tab'      => 'ewd-uasp-emails-tab',
	        'callback' => $this->premium_info( 'emails' )
	      )
	    );

		/**
	     * Premium options preview only
	     */
	    // "Premium" Tab
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'     => 'ewd-uasp-premium-tab',
	        'title'  => __( 'Premium', 'ultimate-appointment-scheduling' ),
	        'is_tab' => true,
	        'show_submit_button' => $this->show_submit_button( 'premium' )
	      )
	    );
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'       => 'ewd-uasp-premium-tab-body',
	        'tab'      => 'ewd-uasp-premium-tab',
	        'callback' => $this->premium_info( 'premium' )
	      )
	    );
	
	    // "Custom Fields" Tab
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'     => 'ewd-uasp-custom-fields-tab',
	        'title'  => __( 'Custom Fields', 'ultimate-appointment-scheduling' ),
	        'is_tab' => true,
	        'show_submit_button' => $this->show_submit_button( 'custom_fields' )
	      )
	    );
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'       => 'ewd-uasp-custom-fields-tab-body',
	        'tab'      => 'ewd-uasp-custom-fields-tab',
	        'callback' => $this->premium_info( 'custom_fields' )
	      )
	    );

	    // "Payments" Tab
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'     => 'ewd-uasp-payments-tab',
	        'title'  => __( 'Payments', 'ultimate-appointment-scheduling' ),
	        'is_tab' => true,
	        'show_submit_button' => $this->show_submit_button( 'payments' )
	      )
	    );
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'       => 'ewd-uasp-payments-tab-body',
	        'tab'      => 'ewd-uasp-payments-tab',
	        'callback' => $this->premium_info( 'payments' )
	      )
	    );
	
	    // "Labelling" Tab
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'     => 'ewd-uasp-labelling-tab',
	        'title'  => __( 'Labelling', 'ultimate-appointment-scheduling' ),
	        'is_tab' => true,
	        'show_submit_button' => $this->show_submit_button( 'labelling' )
	      )
	    );
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'       => 'ewd-uasp-labelling-tab-body',
	        'tab'      => 'ewd-uasp-labelling-tab',
	        'callback' => $this->premium_info( 'labelling' )
	      )
	    );
	
	    // "Styling" Tab
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'     => 'ewd-uasp-styling-tab',
	        'title'  => __( 'Styling', 'ultimate-appointment-scheduling' ),
	        'is_tab' => true,
	        'show_submit_button' => $this->show_submit_button( 'styling' )
	      )
	    );
	    $sap->add_section(
	      'ewd-uasp-settings',
	      array(
	        'id'       => 'ewd-uasp-styling-tab-body',
	        'tab'      => 'ewd-uasp-styling-tab',
	        'callback' => $this->premium_info( 'styling' )
	      )
	    );

		$sap = apply_filters( 'ewd_uasp_settings_page', $sap, $this );

		$sap->add_admin_menus();

	}

	public function show_submit_button( $permission_type = '' ) {
		global $ewd_uasp_controller;

		if ( $ewd_uasp_controller->permissions->check_permission( $permission_type ) ) {
			return true;
		}

		return false;
	}

	public function premium_info( $section_and_perm_type ) {
		global $ewd_uasp_controller;

		$is_premium_user = $ewd_uasp_controller->permissions->check_permission( $section_and_perm_type );
		$is_helper_installed = defined( 'EWDPH_PLUGIN_FNAME' ) && is_plugin_active( EWDPH_PLUGIN_FNAME );

		if ( $is_premium_user || $is_helper_installed ) {
			return false;
		}

		$content = '';

		$premium_features = function( $upgrade_now = true ) { return '
			<p><strong>' . __( 'The premium version also gives you access to the following features:', 'ultimate-appointment-scheduling' ) . '</strong></p>
			<ul class="ewd-uasp-dashboard-new-footer-one-benefits">
				<li>' . __( 'Accept Payments for Bookings', 'ultimate-appointment-scheduling' ) . '</li>
				<li>' . __( 'Add Custom Fields to Booking Form', 'ultimate-appointment-scheduling' ) . '</li>
				<li>' . __( 'Require Login to Book', 'ultimate-appointment-scheduling' ) . '</li>
				<li>' . __( 'Admin Appointment Notifications', 'ultimate-appointment-scheduling' ) . '</li>
				<li>' . __( 'Two Booking Form Styles', 'ultimate-appointment-scheduling' ) . '</li>
				<li>' . __( 'Import/Export Appointments', 'ultimate-appointment-scheduling' ) . '</li>
				<li>' . __( 'Advanced Styling Options', 'ultimate-appointment-scheduling' ) . '</li>
				<li>' . __( 'Advanced Labelling', 'ultimate-appointment-scheduling' ) . '</li>
				<li>' . __( 'Email Support', 'ultimate-appointment-scheduling' ) . '</li>
			</ul>'.
			(
				$upgrade_now ? ''.
					'<div class="ewd-uasp-dashboard-new-footer-one-buttons">
						<a class="ewd-uasp-dashboard-new-upgrade-button" href="https://www.etoilewebdesign.com/license-payment/?Selected=UASP&Quantity=1" target="_blank">' . __( 'UPGRADE NOW', 'ultimate-appointment-scheduling' ) . '</a>
					</div>'
					: ''
			);
		};

		switch ( $section_and_perm_type ) {

			case 'premium':

				$content = '
					<div class="ewd-uasp-settings-preview">
						<h2>' . __( 'Premium', 'ultimate-appointment-scheduling' ) . '<span>' . __( 'Premium', 'ultimate-appointment-scheduling' ) . '</span></h2>
						<p>' . __( 'The premium options let you change the booking form style, configure admin and service provider notification emails, add a captcha, require login to book and more.', 'ultimate-appointment-scheduling' ) . '</p>
						<div class="ewd-uasp-settings-preview-images">
							<img src="' . EWD_UASP_PLUGIN_URL . '/assets/img/premium-screenshots/premium.png" alt="UASP premium screenshot">
						</div>
						' . $premium_features() . '
					</div>
				';

				break;

			case 'custom_fields':

				$content = '
					<div class="ewd-uasp-settings-preview">
						<h2>' . __( 'Custom Fields', 'ultimate-appointment-scheduling' ) . '<span>' . __( 'Premium', 'ultimate-appointment-scheduling' ) . '</span></h2>
						<p>' . __( 'You can add extra custom fields to your booking form, which can be used to request any necessary extra info for the appointment being booked.', 'ultimate-appointment-scheduling' ) . '</p>
						<div class="ewd-uasp-settings-preview-images">
							<img src="' . EWD_UASP_PLUGIN_URL . '/assets/img/premium-screenshots/customfields.png" alt="UASP custom fields screenshot">
						</div>
						' . $premium_features() . '
					</div>
				';

				break;

			case 'payments':

				$content = '
					<div class="ewd-uasp-settings-preview">
						<h2>' . __( 'Payments', 'ultimate-appointment-scheduling' ) . '<span>' . __( 'Premium', 'ultimate-appointment-scheduling' ) . '</span></h2>
						<p>' . __( 'The payment options let you enable either WooCommerce payment (creates a product for each service and people check out via WooCommerce) or PayPal payment for appointments.', 'ultimate-appointment-scheduling' ) . '</p>
						<div class="ewd-uasp-settings-preview-images">
							<img src="' . EWD_UASP_PLUGIN_URL . '/assets/img/premium-screenshots/payments.png" alt="UASP payments screenshot">
						</div>
						' . $premium_features() . '
					</div>
				';

				break;

			case 'emails':

				$content = '
					<div class="ewd-uasp-settings-preview">
						<h2>' . __( 'Email Styling', 'ultimate-appointment-scheduling' ) . '<span>' . __( 'Premium', 'ultimate-appointment-scheduling' ) . '</span></h2>
						<p>' . __( 'The email styling options allow you to customize the look and design of the reminder and notification emails sent out by the plugin.', 'ultimate-appointment-scheduling' ) . '</p>
						<div class="ewd-uasp-settings-preview-images">
							<img src="' . EWD_UASP_PLUGIN_URL . '/assets/img/premium-screenshots/emails.png" alt="UASP emails screenshot">
						</div>
						' . $premium_features( $upgrade_now = false) . '
					</div>
				';

				break;

			case 'labelling':

				$content = '
					<div class="ewd-uasp-settings-preview">
						<h2>' . __( 'Labelling', 'ultimate-appointment-scheduling' ) . '<span>' . __( 'Premium', 'ultimate-appointment-scheduling' ) . '</span></h2>
						<p>' . __( 'The labelling options let you change the wording of the different labels that appear on the front end of the plugin. You can use this to translate them, customize the wording for your purpose, etc.', 'ultimate-appointment-scheduling' ) . '</p>
						<div class="ewd-uasp-settings-preview-images">
							<img src="' . EWD_UASP_PLUGIN_URL . '/assets/img/premium-screenshots/labelling1.png" alt="UASP labelling screenshot one">
							<img src="' . EWD_UASP_PLUGIN_URL . '/assets/img/premium-screenshots/labelling2.png" alt="UASP labelling screenshot two">
						</div>
						' . $premium_features() . '
					</div>
				';

				break;

			case 'styling':

				$content = '
					<div class="ewd-uasp-settings-preview">
						<h2>' . __( 'Styling', 'ultimate-appointment-scheduling' ) . '<span>' . __( 'Premium', 'ultimate-appointment-scheduling' ) . '</span></h2>
						<p>' . __( 'The styling options let you modify the color, font size, font family, border, margin and padding of the various elements found in the booking form.', 'ultimate-appointment-scheduling' ) . '</p>
						<div class="ewd-uasp-settings-preview-images">
							<img src="' . EWD_UASP_PLUGIN_URL . '/assets/img/premium-screenshots/styling1.png" alt="UASP styling screenshot one">
							<img src="' . EWD_UASP_PLUGIN_URL . '/assets/img/premium-screenshots/styling2.png" alt="UASP styling screenshot two">
						</div>
						' . $premium_features() . '
					</div>
				';

				break;
		}

		return function() use ( $content ) {

			echo wp_kses_post( $content );
		};
	}
}
} // endif;
