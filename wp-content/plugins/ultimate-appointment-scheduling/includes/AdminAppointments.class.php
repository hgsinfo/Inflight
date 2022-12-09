<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduaspAdminAppointments' ) ) {
/**
 * Class to handle the admin appointments page for Ultimate Appointment Scheduling
 *
 * @since 2.0.0
 */
class ewduaspAdminAppointments {

	/**
	 * The appointments table
	 *
	 * This is only instantiated on the appointments admin page at the moment when
	 * it is generated.
	 *
	 * @see self::show_admin_appointments_page()
	 * @see WP_List_table.BookingsTable.class.php
	 * @since 2.0.0
	 */
	public $appointments_table;

	public function __construct() {

		// Add the admin menu
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );

		// Hide the 'Add New' item from the side menu
		add_action( 'admin_head', array( $this, 'hide_add_new_menu_item' ) );
	}

	/**
	 * Add the top-level admin menu page
	 * @since 2.0.0
	 */
	public function add_menu_page() {
		global $ewd_uasp_controller;

		add_menu_page(
			_x( 'Appointments', 'Title of admin page that lists appointments', 'ultimate-appointment-scheduling' ),
			_x( 'Appointments', 'Title of appointments admin menu item', 'ultimate-appointment-scheduling' ),
			$ewd_uasp_controller->settings->get_setting( 'access-role' ),
			'ewd-uasp-appointments',
			array( $this, 'show_admin_appointments_page' ),
			'dashicons-calendar-alt',
			'50.9'
		);

		add_submenu_page( 
			'ewd-uasp-appointments', 
			_x( 'Appointments', 'Title of admin page that lets you view all appointments', 'ultimate-appointment-scheduling' ),
			_x( 'Appointments', 'Title of the appointments admin menu item', 'ultimate-appointment-scheduling' ), 
			$ewd_uasp_controller->settings->get_setting( 'access-role' ), 
			'ewd-uasp-appointment-submenu', 
			array( $this, 'show_admin_appointments_page' )
		);

		add_submenu_page( 
			'ewd-uasp-appointments', 
			_x( 'Add/Edit Appointment', 'Title of admin page that lets you add or edit an appointment', 'ultimate-appointment-scheduling' ),
			_x( 'Add New', 'Title of the add/edit appointment admin menu item', 'ultimate-appointment-scheduling' ), 
			$ewd_uasp_controller->settings->get_setting( 'access-role' ), 
			'ewd-uasp-add-edit-appointment', 
			array( $this, 'add_edit_appointment' )
		);
	}

	/**
	 * Hide the 'Add New' admin page from the WordPress sidebar menu
	 * @since 2.0.0
	 */
	public function hide_add_new_menu_item() {

		remove_submenu_page( 'ewd-uasp-appointments', 'ewd-uasp-add-edit-appointment' );
	}

	/**
	 * Display the admin appointments page
	 * @since 2.0.0
	 */
	public function show_admin_appointments_page() {

		require_once( EWD_UASP_PLUGIN_DIR . '/includes/WP_List_Table.AppointmentsTable.class.php' );
		$this->appointments_table = new ewduaspAppointmentsTable();
		$this->appointments_table->prepare_items();
		?>

		<div class="wrap">
			<h1>
				<?php _e( 'Appointments', 'ultimate-appointment-scheduling' ); ?>
				<a href="admin.php?page=ewd-uasp-add-edit-appointment" class="add-new-h2 page-title-action add-appointment"><?php _e( 'Add New', 'ultimate-appointment-scheduling' ); ?></a>
			</h1>

			<?php do_action( 'ewd_uasp_appointments_table_top' ); ?>
			<form id="ewd-uasp-appointments-table" method="POST" action="">
				<input type="hidden" name="page" value="ewd-uasp-appointments">

				<div class="ewd-uasp-primary-controls clearfix">
					<div class="ewd-uasp-views">
						<?php $this->appointments_table->views(); ?>
					</div>
					<?php $this->appointments_table->advanced_filters(); ?>
				</div>

				<?php $this->appointments_table->display(); ?>
			</form>
			<?php do_action( 'ewd_uasp_appointments_table_bottom' ); ?>
		</div>

		<?php
	}

	/**
	 * Display the admin appointments page
	 * @since 2.0.0
	 */
	public function add_edit_appointment() {
		global $ewd_uasp_controller;

		// Define shortcode attributes
		$args = array(
			'display_type' 	=> $ewd_uasp_controller->settings->get_setting( 'admin-appointment-selection' ),
		);

		$appoinment_id = ! empty( $_GET['appointment_id'] ) ? intval( $_GET['appointment_id'] ) : 0;

		if ( isset( $_POST['ewd_uasp_submit_booking'] ) ) {
	
			$appointment = new ewduaspAppointment();

			if ( $appoinment_id ) { $appointment->load_appointment_from_id( $appoinment_id ); }
			
			$status = $appointment->process_admin_appointment_submission();

			if ( ! $status ) {
	
				$args['update_message'] = '';
	
				foreach ( $appointment->validation_errors as $validation_error ) {
	
					$args['update_message'] .= '<br />' . $validation_error['message'];
				}
			}
			else { $args['update_message'] = $ewd_uasp_controller->settings->get_setting( 'label-thank-you-submit' ); }
		}

		if ( $appoinment_id ) { 

			$appointment = new ewduaspAppointment();
			$appointment->load_appointment_from_id( $appoinment_id );

			$args['appointment'] = $appointment; 
		}
		
	
		// Render booking form
		ewd_uasp_load_view_files();
	
		$booking = new ewduaspViewAdminAppointmentBooking( $args );
	
		$output = $booking->render();
	
		echo $output;
	}
}
} // endif;
