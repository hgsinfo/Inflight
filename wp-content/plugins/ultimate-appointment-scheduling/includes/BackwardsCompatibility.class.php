<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduaspBackwardsCompatibility' ) ) {
/**
 * Class to handle transforming the plugin settings from the 
 * previous style (individual options) to the new one (options array)
 *
 * @since 2.0.0
 */
class ewduaspBackwardsCompatibility {

	public function __construct() {
		
		if ( empty( get_option( 'ewd-uasp-settings' ) ) and get_option( 'EWD_UASP_Full_Version' ) ) { $this->run_backwards_compat(); }
		elseif ( ! get_option( 'ewd-uasp-permission-level' ) ) { update_option( 'ewd-uasp-permission-level', 1 ); }
	}

	public function run_backwards_compat() {

		$this->convert_exceptions();

		$settings = array(
			'custom-css' 								=> get_option( 'EWD_UASP_Custom_CSS' ),
			'multi-step-booking'						=> get_option( 'EWD_UASP_Multi_Step_Booking' ) == 'Yes' ? true : false,
			'time-between-appointments'					=> intval( get_option( 'EWD_UASP_Time_Between_Appointments' ) ),
			'required-information'						=> is_array( get_option( 'EWD_UASP_Required_Information' ) ) ? array_map( 'strtolower', get_option( 'EWD_UASP_Required_Information' ) ) : array(),
			'timezone'									=> get_option( 'EWD_UASP_Timezone' ),
			'date-formatting-style'						=> strtolower( get_option( 'EWD_UASP_Localize_Date_Time' ) ),
			'hours-format'								=> get_option( 'EWD_UASP_Hours_Format' ),
			'date-format'								=> get_option( 'EWD_UASP_PHP_Date_Format' ),
			'client-email-details'						=> get_option( 'EWD_UASP_Client_Email_Details' ) == -1 ? false : get_option( 'EWD_UASP_Client_Email_Details' ),
			'minimum-days-advance'						=> min( intval( get_option( 'EWD_UASP_Minimum_Days_Advance' ) ), 100 ),
			'minimum-hours-advance'						=> get_option( 'EWD_UASP_Minimum_Hours_Advance' ),
			'maximum-days-advance'						=> min( intval( get_option( 'EWD_UASP_Maximum_Days_Advance' ) ), 365 ),
			'calendar-starting-layout'					=> get_option( 'EWD_UASP_Calendar_Starting_Layout' ),
			'calendar-starting-time'					=> get_option( 'EWD_UASP_Calendar_Starting_Time' ),
			'calendar-offset'							=> intval( get_option( 'EWD_UASP_Calendar_Offset' ) ) . '_' . get_option( 'EWD_UASP_Calendar_Offset_Unit' ),

			'booking-form-style'						=> strtolower( get_option( 'EWD_UASP_Booking_Form_Style' ) ),
			'admin-appointment-notification'			=> get_option( 'EWD_UASP_Admin_Email_Notification' ) == -1 ? false : get_option( 'EWD_UASP_Admin_Email_Notification' ),
			'admin-email-address'						=> get_option( 'EWD_UASP_Admin_Email_Address' ),
			'service-provider-notification'				=> get_option( 'EWD_UASP_Provider_Email_Notification' ) == -1 ? false : get_option( 'EWD_UASP_Provider_Email_Notification' ),
			'use-captcha'								=> get_option( 'EWD_UASP_Add_Captcha' ) == 'Yes' ? true : false,
			'calendar-language'							=> get_option( 'EWD_Calendar_Language' ),
			'access-role'								=> get_option( 'EWD_UASP_Access_Role' ),
			'require-login'								=> get_option( 'EWD_UASP_Require_Login' ) == 'Yes' ? true : false,
			'wordpress-login-url'						=> get_option( 'EWD_UASP_WordPress_Login_URL' ),

			'custom-fields'								=> $this->convert_custom_fields(),

			'email-reminders'							=> $this->convert_reminders(),	
			'appointment-confirmation'					=> get_option( 'EWD_UASP_Appointment_Confirmation' ) == 'Yes' ? true : false,	
			'appointment-booking-page'					=> get_option( 'EWD_UASP_Appointment_Confirmation_Page' ),	
			'reminders-cache-time'						=> intval( get_option( 'EWD_UASP_Reminders_Cache_Time' ) ) / 60,	
			
			'email-messages-array'						=> $this->convert_email_messages(),

			'woocommerce-integration'					=> get_option( 'EWD_UASP_WooCommerce_Integration' ) == 'Yes' ? true : false,	
			'paypal-prepayment'							=> get_option( 'EWD_UASP_Allow_Paypal_Prepayment' ) == 'No' ? 'none' : strtolower( get_option( 'EWD_UASP_Allow_Paypal_Prepayment' ) ),	
			'currency-code'								=> get_option( 'EWD_UASP_Pricing_Currency_Code' ),	
			'paypal-email-address'						=> get_option( 'EWD_UASP_PayPal_Email_Address' ),		
			'thank-you-url'								=> get_option( 'EWD_UASP_Thank_You_URL' ),	

			'label-sign-up-title'						=> get_option( 'EWD_Sign_Up_Title_Label' ),
			'label-name'								=> get_option( 'EWD_Name_Label' ),
			'label-phone'								=> get_option( 'EWD_Phone_Label' ),
			'label-email'								=> get_option( 'EWD_Email_Label' ),
			'label-service-title'						=> get_option( 'EWD_Service_Title_Label' ),
			'label-location'							=> get_option( 'EWD_Location_Label' ),
			'label-service'								=> get_option( 'EWD_Service_Label' ),
			'label-service-provider'					=> get_option( 'EWD_Service_Provider_Label' ),
			'label-any'									=> get_option( 'EWD_UASP_Service_Provider_Any_Label' ),
			'label-appointment-title'					=> get_option( 'EWD_Appointment_Title_Label' ),
			'label-appointment-date'					=> get_option( 'EWD_Appointment_Date_Label' ),
			'label-find-appointment'					=> get_option( 'EWD_Find_Appointments_Label' ),
			'label-book-appointment'					=> get_option( 'EWD_Book_Appointment_Label' ),
			'label-pay-in-advance'						=> get_option( 'EWD_Pay_In_Advance_Label' ),
			'label-proceed-to-payment'					=> get_option( 'EWD_Proceed_To_Payment_Label' ),
			'label-select-time'							=> get_option( 'EWD_Select_Time_Label' ),
			'label-click-select-date'					=> get_option( 'EWD_UASP_Click_Select_Date_Label' ),
			'label-image-number'						=> get_option( 'EWD_UASP_Image_Number_Label' ),
			
			'styling-signup-title-font'					=> get_option( 'EWD_UASP_Signup_Title_Font' ),
			'styling-signup-title-font-size'			=> get_option( 'EWD_UASP_Signup_Title_Font_Size' ),
			'styling-signup-title-font-color'			=> get_option( 'EWD_UASP_Signup_Title_Font_Color' ),
			'styling-signup-label-font'					=> get_option( 'EWD_UASP_Signup_Label_Font' ),
			'styling-signup-label-font-size'			=> get_option( 'EWD_UASP_Signup_Label_Font_Size' ),
			'styling-signup-block-color'				=> get_option( 'EWD_UASP_Signup_Block_Color' ),
			'styling-signup-block-margin'				=> get_option( 'EWD_UASP_Signup_Block_Margin' ),
			'styling-signup-block-padding'				=> get_option( 'EWD_UASP_Signup_Block_Padding' ),

			'styling-service-title-font'				=> get_option( 'EWD_UASP_Service_Title_Font' ),
			'styling-service-title-font-size'			=> get_option( 'EWD_UASP_Service_Title_Font_Size' ),
			'styling-service-title-font-color'			=> get_option( 'EWD_UASP_Service_Title_Font_Color' ),
			'styling-service-label-font'				=> get_option( 'EWD_UASP_Service_Label_Font' ),
			'styling-service-label-font-size'			=> get_option( 'EWD_UASP_Service_Label_Font_Size' ),
			'styling-service-block-color'				=> get_option( 'EWD_UASP_Service_Block_Color' ),
			'styling-service-block-margin'				=> get_option( 'EWD_UASP_Service_Block_Margin' ),
			'styling-service-block-padding'				=> get_option( 'EWD_UASP_Service_Block_Padding' ),

			'styling-appointment-title-font'			=> get_option( 'EWD_UASP_Appointment_Title_Font' ),
			'styling-appointment-title-font-size'		=> get_option( 'EWD_UASP_Appointment_Title_Font_Size' ),
			'styling-appointment-title-font-color'		=> get_option( 'EWD_UASP_Appointment_Title_Font_Color' ),
			'styling-appointment-label-font'			=> get_option( 'EWD_UASP_Appointment_Label_Font' ),
			'styling-appointment-label-font-size'		=> get_option( 'EWD_UASP_Appointment_Label_Font_Size' ),
			'styling-appointment-block-color'			=> get_option( 'EWD_UASP_Appointment_Block_Color' ),
			'styling-appointment-block-margin'			=> get_option( 'EWD_UASP_Appointment_Block_Margin' ),
			'styling-appointment-block-padding'			=> get_option( 'EWD_UASP_Appointment_Block_Padding' ),

			'styling-button-font'						=> get_option( 'EWD_UASP_Button_Font' ),
			'styling-button-font-size'					=> get_option( 'EWD_UASP_Button_Font_Size' ),
			'styling-button-font-color'					=> get_option( 'EWD_UASP_Button_Font_Color' ),
			'styling-button-color'						=> get_option( 'EWD_UASP_Button_Color' ),
			'styling-button-margin'						=> get_option( 'EWD_UASP_Button_Margin' ),
			'styling-button-padding'					=> get_option( 'EWD_UASP_Button_Padding' ),

			'styling-email-background-color'			=> get_option( 'EWD_UASP_Email_Reminder_Background_Color' ),
			'styling-email-inner-color'					=> get_option( 'EWD_UASP_Email_Reminder_Inner_Color' ),
			'styling-email-text-color'					=> get_option( 'EWD_UASP_Email_Reminder_Text_Color' ),
			'styling-email-button-bg-color'				=> get_option( 'EWD_UASP_Email_Reminder_Button_Background_Color' ),
			'styling-email-button-text-color'			=> get_option( 'EWD_UASP_Email_Reminder_Button_Text_Color' ),
			'styling-email-button-bg-hover-color'		=> get_option( 'EWD_UASP_Email_Reminder_Button_Background_Hover_Color' ),
			'styling-email-button-text-hover-color'		=> get_option( 'EWD_UASP_Email_Reminder_Button_Text_Hover_Color' ),

			'styling-calendar-appt-font'					=> get_option( 'EWD_UASP_Calendar_Appointment_Font_Family' ),
			'styling-calendar-appt-font-size'				=> get_option( 'EWD_UASP_Calendar_Appointment_Font_Size' ),
			'styling-calendar-appt-color'					=> get_option( 'EWD_UASP_Calendar_Appointment_Color' ),
			'styling-calendar-appt-bg-color'				=> get_option( 'EWD_UASP_Calendar_Appointment_Background_Color' ),
			'styling-calendar-appt-border-color'			=> get_option( 'EWD_UASP_Calendar_Appointment_Border_Color' ),
			'styling-calendar-appt-selected-bg-color'		=> get_option( 'EWD_UASP_Calendar_Selected_Appointment_Background_Color' ),
			'styling-calendar-appt-selected-border-color'	=> get_option( 'EWD_UASP_Calendar_Selected_Appointment_Border_Color' ),

		);

		add_option( 'ewd-uasp-review-ask-time', get_option( 'EWD_UASP_Ask_Review_Date' ) );
		add_option( 'ewd-uasp-installation-time', get_option( 'EWD_UASP_Install_Time' ) );

		update_option( 'ewd-uasp-permission-level', get_option( 'EWD_UASP_Full_Version' ) == 'Yes' ? 2 : 1 );
		
		update_option( 'ewd-uasp-settings', $settings );
	}

	public function convert_reminders() {

		$old_reminders = get_option( 'EWD_UASP_Email_Reminders' );
		$new_reminders = array();

		foreach ( $old_reminders as $old_reminder ) {

			$seconds = $old_reminder['SecondsBeforeAppointment'];

			$unit = ( $seconds % 86400 ) == 0 ? 'days' : ( ( $seconds % 3600 ) == 0 ? 'hours' : 'minutes' );

			$interval = $seconds / ( $unit == 'days' ? 86400 : ( $unit == 'hours' ? 3600 : 60 ) );

			$new_reminder = array(
				'id'			=> $old_reminder['ID'],
				'interval'		=> $interval,
				'unit'			=> $unit,
				'email_id'		=> $old_reminder['Email_ID'],
				'conditional'	=> strtolower( $old_reminder['Conditional'] ),
			);

			$new_reminders[] = $new_reminder;
		}

		return json_encode( $new_reminders );
	}

	public function convert_email_messages() {

		$old_email_messages = get_option( 'EWD_UASP_Email_Messages_Array' );
		$new_email_messages = array();

		foreach ( $old_email_messages as $old_email_message ) {

			$new_email = array(
				'id'			=> $old_email_message['ID'],
				'name'			=> $old_email_message['Name'],
				'subject'		=> stripslashes( $old_email_message['Subject'] ),
				'message'		=> stripslashes( $old_email_message['Message'] ),
			);

			$new_email_messages[] = $new_email;
		}

		return json_encode( $new_email_messages );
	}

	public function convert_exceptions() {
		global $wpdb;

		$exceptions_table_name = $wpdb->prefix . 'EWD_UASP_Exceptions';

		$current_date_time = date( 'Y-m-d H:i:s' );

		$old_exceptions = $wpdb->get_results( "SELECT * FROM $exceptions_table_name WHERE Exception_End >= $current_date_time ORDER BY Exception_Start ASC" );

		foreach ( $old_exceptions as $old_exception ) {

			$args = array(
				'post_title'	=> 'Imported Exception',
				'post_content'	=> $old_exception['Exception_Reason'],
				'post_type'		=> EWD_UASP_EXCEPTION_POST_TYPE
			);

			$post_id = wp_insert_post( $args );

			if ( $post_id ) {

				update_post_meta( $post_id, 'start', strtotime( $old_exception->Exception_Start ) );
				update_post_meta( $post_id, 'end', strtotime( $old_exception->Exception_End ) );
				update_post_meta( $post_id, 'status', strtolower( $old_exception->Exception_Status ) );
				update_post_meta( $post_id, 'location_id', $old_exception->Location_Post_ID );
				update_post_meta( $post_id, 'provider_id', $old_exceptionService_Provider_Post_ID );
			}
		}
	}

	public function convert_custom_fields() {
		global $wpdb;

		$custom_fields_table_name = $wpdb->prefix . 'EWD_UASP_Custom_Fields';

		$old_fields = $wpdb->get_results( "SELECT * FROM $custom_fields_table_name ORDER BY Field_Order ASC" );

		$new_fields = array();

		foreach ( $old_fields as $old_field ) {

			$new_field = array(
				'id'		=> $old_field->Field_ID,
				'name'		=> $old_field->Field_Name,
				'slug'		=> $old_field->Field_Slug,
				'type'		=> $old_field->Field_Type == 'mediumint' ? 'number' : $old_field->Field_Type,
				'options'	=> $old_field->Field_Values,
			);

			$new_fields[] = $new_field;
		}

		return json_encode( $new_fields );
	}
}

}