<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( !class_exists( 'ewduaspAppointmentsTable' ) ) {
/**
 * Appointments Table Class
 *
 * Extends WP_List_Table to display the list of appointments in a format similar to
 * the default WordPress post tables.
 *
 * @h/t Easy Digital Downloads by Pippin: https://easydigitaldownloads.com/
 * @since 2.0.0
 */
class ewduaspAppointmentsTable extends WP_List_Table {

	/**
	 * Number of results to show per page
	 *
	 * @var string
	 * @since 2.0.0
	 */
	public $per_page = 30;

	/**
	 * URL of this page
	 *
	 * @var string
	 * @since 2.0.0
	 */
	public $base_url;

	/**
	 * Array of appointment counts by total and status
	 *
	 * @var array
	 * @since 2.0.0
	 */
	public $appointment_counts;

	/**
	 * Array of appointments
	 *
	 * @var array
	 * @since 2.0.0
	 */
	public $appointments;

	/**
	 * Current date filters
	 *
	 * @var string
	 * @since 2.0.0
	 */
	public $filter_start_date = null;
	public $filter_end_date = null;

	/**
	 * Current time filters
	 *
	 * @var string
	 * @since 2.2.0
	 */
	public $filter_start_time = null;
	public $filter_end_time = null;

	/**
	 * Current location filter
	 *
	 * @var int
	 * @since 1.6
	 */
	public $filter_location = 0;

	/**
	 * Current service filter
	 *
	 * @var int
	 * @since 1.6
	 */
	public $filter_service = 0;

	/**
	 * Current provider filter
	 *
	 * @var int
	 * @since 1.6
	 */
	public $filter_provider = 0;

	/**
	 * Current query string
	 *
	 * @var string
	 * @since 2.0.0
	 */
	public $query_string;

	/**
	 * Results of a bulk or quick action
	 *
	 * @var array
	 * @since 1.4.6
	 */
	public $action_result = array();

	/**
	 * Type of bulk or quick action last performed
	 *
	 * @var string
	 * @since 1.4.6
	 */
	public $last_action = '';

	/**
	 * Stored reference to visible columns
	 *
	 * @var string
	 * @since 2.0.0
	 */
	public $visible_columns = array();

	/**
	 * Initialize the table and perform any requested actions
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			'singular'  => __( 'Appointment', 'ultimate-appointment-scheduling' ),
			'plural'    => __( 'Appointments', 'ultimate-appointment-scheduling' ),
			'ajax'      => false
		) );

		// Set the date filter
		$this->set_date_filter();

		// Strip unwanted query vars from the query string or ensure the correct
		// vars are used
		$this->query_string_maintenance();

		// Run any bulk action requests
		$this->process_bulk_action();

		// Retrieve a count of the number of appointments by status
		$this->get_appointment_counts();

		// Retrieve appointments data for the table
		$this->appointments_data();

		$this->base_url = admin_url( 'admin.php?page=ewd-uasp-appointments' );
	}

	/**
	 * Set the correct date filter
	 *
	 * $_POST values should always overwrite $_GET values
	 *
	 * @since 2.0.0
	 */
	public function set_date_filter( $start_date = null, $end_date = null, $start_time = null, $end_time = null ) {

		if ( !empty( $_GET['action'] ) && $_GET['action'] == 'clear_date_filters' ) {
			$this->filter_start_date 	= null;
			$this->filter_end_date 		= null;
			$this->filter_start_time 	= null;
			$this->filter_end_time 		= null;
		}

		$this->filter_start_date 	= $start_date;
		$this->filter_end_date 		= $end_date;
		$this->filter_start_time 	= $start_time;
		$this->filter_end_time 		= $end_time;

		if ( $start_date === null ) {
			$this->filter_start_date = !empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : null;
			$this->filter_start_date = !empty( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : $this->filter_start_date;
		}

		if ( $end_date === null ) {
			$this->filter_end_date = !empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : null;
			$this->filter_end_date = !empty( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : $this->filter_end_date;
		}

		if ( $start_time === null ) {
			$this->filter_start_time = !empty( $_GET['start_time'] ) ? sanitize_text_field( $_GET['start_time'] ) : null;
			$this->filter_start_time = !empty( $_POST['start_time'] ) ? sanitize_text_field( $_POST['start_time'] ) : $this->filter_start_time;
		}

		if ( $end_time === null ) {
			$this->filter_end_time = !empty( $_GET['end_time'] ) ? sanitize_text_field( $_GET['end_time'] ) : null;
			$this->filter_end_time = !empty( $_POST['end_time'] ) ? sanitize_text_field( $_POST['end_time'] ) : $this->filter_end_time;
		}
	}

	/**
	 * Get the current date range
	 *
	 * @since 1.3
	 */
	public function get_current_date_range() {

		$range = empty( $this->filter_start_date ) ? _x( '*', 'No date limit in a date range, eg 2014-* would mean any date from 2014 or after', 'ultimate-appointment-scheduling' ) : $this->filter_start_date;
		$range .= empty( $this->filter_start_date ) || empty( $this->filter_end_date ) ? '' : _x( '&mdash;', 'Separator between two dates in a date range', 'ultimate-appointment-scheduling' );
		$range .= empty( $this->filter_end_date ) ? _x( '*', 'No date limit in a date range, eg 2014-* would mean any date from 2014 or after', 'ultimate-appointment-scheduling' ) : $this->filter_end_date;

		return $range;
	}

	/**
	 * Strip unwanted query vars from the query string or ensure the correct
	 * vars are passed around and those we don't want to preserve are discarded.
	 *
	 * @since 2.0.0
	 */
	public function query_string_maintenance() {

		$this->query_string = remove_query_arg( array( 'action', 'start_date', 'end_date' ) );

		if ( $this->filter_start_date !== null ) {
			$this->query_string = add_query_arg( array( 'start_date' => $this->filter_start_date ), $this->query_string );
		}

		if ( $this->filter_end_date !== null ) {
			$this->query_string = add_query_arg( array( 'end_date' => $this->filter_end_date ), $this->query_string );
		}

		if ( $this->filter_start_time !== null ) {
			$this->query_string = add_query_arg( array( 'start_time' => $this->filter_start_time ), $this->query_string );
		}

		if ( $this->filter_end_time !== null ) {
			$this->query_string = add_query_arg( array( 'end_time' => $this->filter_end_time ), $this->query_string );
		}

		$this->filter_location = ! isset( $_GET['location'] ) ? 0 : intval( $_GET['location'] );
		$this->filter_location = ! isset( $_POST['location'] ) ? $this->filter_location : intval( $_POST['location'] );
		$this->query_string = remove_query_arg( 'location', $this->query_string );
		if ( !empty( $this->filter_location ) ) {
			$this->query_string = add_query_arg( array( 'location' => $this->filter_location ), $this->query_string );
		}

		$this->filter_service = ! isset( $_GET['service'] ) ? 0 : intval( $_GET['service'] );
		$this->filter_service = ! isset( $_POST['service'] ) ? $this->filter_service : intval( $_POST['service'] );
		$this->query_string = remove_query_arg( 'service', $this->query_string );
		if ( !empty( $this->filter_service ) ) {
			$this->query_string = add_query_arg( array( 'service' => $this->filter_service ), $this->query_string );
		}

		$this->filter_provider = ! isset( $_GET['provider'] ) ? 0 : intval( $_GET['provider'] );
		$this->filter_provider = ! isset( $_POST['provider'] ) ? $this->filter_provider : intval( $_POST['provider'] );
		$this->query_string = remove_query_arg( 'provider', $this->query_string );
		if ( !empty( $this->filter_provider ) ) {
			$this->query_string = add_query_arg( array( 'provider' => $this->filter_provider ), $this->query_string );
		}
	}

	/**
	 * Show the time views, date filters and the search box
	 * @since 2.0.0
	 */
	public function advanced_filters() {

		// Show the date_range views (today, upcoming, all)
		if ( !empty( $_GET['date_range'] ) ) {
			$date_range = sanitize_text_field( $_GET['date_range'] );
		} else {
			$date_range = '';
		}

		// Use a custom date_range if a date range has been entered
		if ( $this->filter_start_date !== null || $this->filter_end_date !== null ) {
			$date_range = 'custom';
		}

		// Strip out existing date filters from the date_range view urls
		$date_range_query_string = remove_query_arg( array( 'date_range', 'start_date', 'end_date' ), $this->query_string );

		$views = array(
			'upcoming'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'paged' => FALSE ), remove_query_arg( array( 'date_range' ), $date_range_query_string ) ) ), $date_range === '' ? ' class="current"' : '', __( 'Upcoming', 'ultimate-appointment-scheduling' ) ),
			'today'	    => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'date_range' => 'today', 'paged' => FALSE ), $date_range_query_string ) ), $date_range === 'today' ? ' class="current"' : '', __( 'Today', 'ultimate-appointment-scheduling' ) ),
			'past'	    => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'date_range' => 'past', 'paged' => FALSE ), $date_range_query_string ) ), $date_range === 'past' ? ' class="current"' : '', __( 'Past', 'ultimate-appointment-scheduling' ) ),
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'date_range' => 'all', 'paged' => FALSE ), $date_range_query_string ) ), $date_range == 'all' ? ' class="current"' : '', __( 'All', 'ultimate-appointment-scheduling' ) ),
		);

		if ( $date_range == 'custom' ) {
			$views['date'] = '<span class="date-filter-range current">' . $this->get_current_date_range() . '</span>';
			$views['date'] .= '<a id="ewd-uasp-date-filter-link" href="#"><span class="dashicons dashicons-calendar"></span> <span class="ewd-uasp-date-filter-label">Change date range</span></a>';
		} else {
			$views['date'] = '<a id="ewd-uasp-date-filter-link" href="#">' . esc_html__( 'Specific Date(s)/Time', 'ultimate-appointment-scheduling' ) . '</a>';
		}

		$views = apply_filters( 'ewd_uasp_appointments_table_views_date_range', $views );
		?>

		<div id="ewd-uasp-filters">
			<ul class="subsubsub ewd-uasp-views-date_range">
				<li><?php echo join( ' | </li><li>', $views ); ?></li>
			</ul>

			<div class="date-filters">
				<div class="ewd-uasp-admin-bookings-filters-start">
					<label for="start-date" class="screen-reader-text"><?php _e( 'Start Date:', 'ultimate-appointment-scheduling' ); ?></label>
					<input type="date" id="start-date" name="start_date" class="datepicker" value="<?php echo esc_attr( $this->filter_start_date ); ?>" placeholder="<?php _e( 'Start Date', 'ultimate-appointment-scheduling' ); ?>" />
					<input type="text" id="start-time" name="start_time" class="timepicker" value="<?php echo esc_attr( $this->filter_start_time ); ?>" placeholder="<?php _e( 'Start Time', 'ultimate-appointment-scheduling' ); ?>" />
				<div>
				<div class="ewd-uasp-admin-bookings-filters-end">
					<label for="end-date" class="screen-reader-text"><?php _e( 'End Date:', 'ultimate-appointment-scheduling' ); ?></label>
					<input type="date" id="end-date" name="end_date" class="datepicker" value="<?php echo esc_attr( $this->filter_end_date ); ?>" placeholder="<?php _e( 'End Date', 'ultimate-appointment-scheduling' ); ?>" />
					<input type="text" id="end-time" name="end_time" class="timepicker" value="<?php echo esc_attr( $this->filter_end_time ); ?>" placeholder="<?php _e( 'Start Time', 'ultimate-appointment-scheduling' ); ?>" />
				</div>
				<input type="submit" class="button button-secondary" value="<?php _e( 'Apply', 'ultimate-appointment-scheduling' ); ?>"/>
				<?php if( !empty( $this->filter_start_date ) || !empty( $this->filter_end_date ) || !empty( $this->filter_start_time ) || !empty( $this->filter_end_time ) ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'clear_date_filters' ) ) ); ?>" class="button button-secondary"><?php _e( 'Clear Filter', 'ultimate-appointment-scheduling' ); ?></a>
				<?php endif; ?>
			</div></div></div>

			<?php if( !empty( $_GET['status'] ) ) : ?>
				<input type="hidden" name="status" value="<?php echo esc_attr( sanitize_text_field( $_GET['status'] ) ); ?>"/>
			<?php endif; ?>
		</div>

<?php
	}

	/**
	 * Retrieve the view types
	 * @since 2.0.0
	 */
	public function get_views() {
		global $ewd_uasp_controller;

		$current = isset( $_GET['status'] ) ? $_GET['status'] : '';

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( array( 'status', 'paged' ), $this->query_string ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'ultimate-appointment-scheduling' ) . ' <span class="count">(' . $this->appointment_counts['total'] . ')</span>' ),
			'confirmed'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'status' => 'confirmed', 'paged' => FALSE ), $this->query_string ) ), $current === 'confirmed' ? ' class="current"' : '', __( 'Confirmed', 'ultimate-appointment-scheduling' ) . ' <span class="count">(' . $this->appointment_counts['confirmed'] . ')</span>' )
		);

		if ( $ewd_uasp_controller->settings->get_setting( 'paypal-prepayment' ) != 'none' or $ewd_uasp_controller->settings->get_setting( 'woocommerce-integration' ) ) {
			
			$views['paid'] = sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'status' => 'paid', 'paged' => FALSE ), $this->query_string ) ), $current === 'paid' ? ' class="current"' : '', __( 'Paid', 'ultimate-appointment-scheduling' ) . ' <span class="count">(' . $this->appointment_counts['paid'] . ')</span>' );
		}

		return apply_filters( 'ewd_uasp_appointments_table_views_status', $views );
	}

	/**
	 * Generates content for a single row of the table
	 * @since 2.0.0
	 */
	public function single_row( $item ) {
		static $row_alternate_class = '';
		$row_alternate_class = ( $row_alternate_class == '' ? 'alternate' : '' );

		$row_classes = ! empty( $item->post_status ) ? array( esc_attr( $item->post_status ) ) : array();

		if ( !empty( $row_alternate_class ) ) {
			$row_classes[] = $row_alternate_class;
		}

		$row_classes = apply_filters( 'ewd_uasp_admin_appointments_list_row_classes', $row_classes, $item );

		echo '<tr class="' . implode( ' ', $row_classes ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Retrieve the table columns
	 *
	 * @since 2.0.0
	 */
	public function get_columns() {

		// Prevent the lookup from running over and over again on a single
		// page load
		if ( !empty( $this->visible_columns ) ) {
			return $this->visible_columns;
		}

		$all_default_columns = $this->get_all_default_columns();
		$all_columns = $this->get_all_columns();

		global $ewd_uasp_controller;
		$visible_columns = $ewd_uasp_controller->settings->get_setting( 'appointments-table-columns' );
		if ( empty( $visible_columns ) ) {
			$columns = $all_default_columns;
		} else {
			$columns = array();
			$columns['cb'] = $all_default_columns['cb'];
			$columns['date'] = $all_default_columns['date'];

			foreach( $all_columns as $key => $column ) {
				if ( in_array( $key, $visible_columns ) ) {
					$columns[$key] = $all_columns[$key];
				}
			}
			$columns['details'] = $all_default_columns['details'];
		}

		$this->visible_columns = apply_filters( 'ewd_uasp_appointments_table_columns', $columns );

		return $this->visible_columns;
	}

	/**
	 * Retrieve all default columns
	 *
	 * @since 2.0.0
	 */
	public function get_all_default_columns() {
		global $ewd_uasp_controller;

		$columns = array(
			'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'date'     	=> __( 'Date', 'ultimate-appointment-scheduling' ),
			'name'  	=> __( 'Name', 'ultimate-appointment-scheduling' ),
			'email'  	=> __( 'Email', 'ultimate-appointment-scheduling' ),
			'phone'  	=> __( 'Phone', 'ultimate-appointment-scheduling' ),
			'location' 	=> __( 'Location', 'ultimate-appointment-scheduling' ),
			'service' 	=> __( 'Service', 'ultimate-appointment-scheduling' ),
			'provider' 	=> __( 'Provider', 'ultimate-appointment-scheduling' ),
		);

		if ( $ewd_uasp_controller->settings->get_setting( 'appointment-confirmation' ) ) { $columns['confirmation'] = __( 'Confirmed', 'ultimate-appointment-scheduling' ) ; }
		if ( $ewd_uasp_controller->settings->get_setting( 'paypal-prepayment' ) != 'none' or $ewd_uasp_controller->settings->get_setting( 'woocommerce-integration' ) ) { $columns['payment'] = __( 'Payment Made', 'ultimate-appointment-scheduling' ) ; }

		return $columns;
	}

	/**
	 * Retrieve all available columns
	 *
	 * This is used to get all columns including those deactivated and filtered
	 * out via get_columns().
	 *
	 * @since 2.0.0
	 */
	public function get_all_columns() {
		$columns = $this->get_all_default_columns();

		return apply_filters( 'ewd_uasp_appointments_all_table_columns', $columns );
	}

	/**
	 * Retrieve the table's sortable columns
	 * @since 2.0.0
	 */
	public function get_sortable_columns() {
		$columns = array(
			'date' 		=> array( 'date', true ),
			'name' 		=> array( 'title', true ),
			'phone' 	=> array( 'phone', true ),
			'email' 	=> array( 'email', true ),
		);
		return apply_filters( 'ewd_uasp_appointments_table_sortable_columns', $columns );
	}

	/**
	 * This function renders most of the columns in the list table.
	 * @since 2.0.0
	 */
	public function column_default( $appointment, $column_name ) {
		global $ewd_uasp_controller;

		switch ( $column_name ) {

			case 'date' :
				
				$value = $appointment->start;

				$value .= '<div class="actions">';
				$value .= '<a href="admin.php?page=ewd-uasp-add-edit-appointment&appointment_id=' . $appointment->id . '" data-id="' . esc_attr( $appointment->id ) . '">' . __( 'Edit', 'ultimate-appointment-scheduling' ) . '</a>';
				$value .= ' | <a href="#" class="delete" data-id="' . esc_attr( $appointment->id ) . '" data-action="delete">' . __( 'Delete', 'ultimate-appointment-scheduling' ) . '</a>';
				$value .= '</div>';

				break;

			case 'name' :
				$value = esc_html( $appointment->client_name );
				break;

			case 'email' :
				$value = esc_html( $appointment->client_email );
				//$value .= '<div class="actions">';
				//$value .= '<a href="#" data-id="' . esc_attr( $appointment->id ) . '" data-action="email" data-email="' . esc_attr( $appointment->client_email ) . '" data-name="' . esc_attr( $appointment->client_name ) . '">' . __( 'Send Email', 'ultimate-appointment-scheduling' ) . '</a>';
				//$value .= '</div>';
				break;

			case 'phone' :
				$value = esc_html( $appointment->client_phone );
				break;

			case 'payment' :
				$value = ( $appointment->paypal_prepaid or $appointment->wc_prepaid ) ? __( 'Yes', 'ultimate-appointment-scheduling' ) : __( 'No', 'ultimate-appointment-scheduling' );
				break;

			case 'confirmation' :
				$value = $appointment->confirmed ? __( 'Yes', 'ultimate-appointment-scheduling' ) : __( 'No', 'ultimate-appointment-scheduling' );
				break;

			case 'location' :
				$value = esc_html( $appointment->location_name );
				break;

			case 'service' :
				$value = esc_html( $appointment->service_name );
				break;

			case 'provider' :
				$value = esc_html( $appointment->provider_name );
				break;

			default:
				$value = isset( $appointment->$column_name ) ? $appointment->$column_name : '';
				break;

		}

		return apply_filters( 'ewd_uasp_appointments_table_column', $value, $appointment, $column_name );
	}

	/**
	 * Render the checkbox column
	 * @since 2.0.0
	 */
	public function column_cb( $appointment ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'appointments',
			$appointment->id
		);
	}

	/**
	 * Retrieve the bulk actions
	 * @since 2.0.0
	 */
	public function get_bulk_actions() {
		global $ewd_uasp_controller;

		$actions = array(
			'delete'                		=> __( 'Delete',		'ultimate-appointment-scheduling' ),
			//'send-email'      				=> __( 'Send Email',	'ultimate-appointment-scheduling' )
		);

		if ( $ewd_uasp_controller->settings->get_setting( 'appointment-confirmation' ) ) { 

			$actions['set-status-confirmed'] = __( 'Set To Confirmed', 'ultimate-appointment-scheduling' );
		}

		if ( $ewd_uasp_controller->settings->get_setting( 'paypal-prepayment' ) != 'none' ) { 

			$actions['set-status-paypal-received'] = __( 'Set To Payment Received', 'ultimate-appointment-scheduling' );
		}

		return apply_filters( 'ewd_uasp_appointments_table_bulk_actions', $actions );
	}

	/**
	 * Process the bulk actions
	 * @since 2.0.0
	 */
	public function process_bulk_action() {
		global $ewd_uasp_controller;

		$ids    = isset( $_POST['appointments'] ) ? $_POST['appointments'] : false;
		$action = isset( $_POST['action'] ) ? $_POST['action'] : false;

		// Check bulk actions selector below the table
		$action = $action == '-1' && isset( $_POST['action2'] ) ? $_POST['action2'] : $action;

		if( empty( $action ) || $action == '-1' ) {
			return;
		}

		if ( ! current_user_can( $ewd_uasp_controller->settings->get_setting( 'access-role' ) ) ) {
			return;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		global $ewd_uasp_controller;
		$results = array();
		foreach ( $ids as $id ) {

			if ( 'delete' === $action ) {
				$results[$id] = $ewd_uasp_controller->appointment_manager->delete_appointment( intval( $id ) );
			}

			if ( 'set-status-confirmed' === $action ) {
				$results[$id] = $ewd_uasp_controller->appointment_manager->set_appointment_to_confirmed( intval( $id ) );
			}

			if ( 'set-status-paypal-received' === $action ) {
				$results[$id] = $ewd_uasp_controller->appointment_manager->set_appointment_prepaid( intval( $id ) );
			}

			$results = apply_filters( 'ewd_uasp_appointments_table_bulk_action', $results, $id, $action );
		}

		if( count( $results ) ) {
			$this->action_result = $results;
			$this->last_action = $action;
			add_action( 'ewd_uasp_appointments_table_top', array( $this, 'admin_notice_bulk_actions' ) );
		}
	}

	/**
	 * Display an admin notice when a bulk action is completed
	 * @since 2.0.0
	 */
	public function admin_notice_bulk_actions() {

		$success = 0;
		$failure = 0;
		foreach( $this->action_result as $id => $result ) {
			if ( $result === true || $result === null ) {
				$success++;
			} else {
				$failure++;
			}
		}

		if ( $success > 0 ) :
		?>

		<div id="ewd-uasp-admin-notice-bulk-<?php esc_attr( $this->last_action ); ?>" class="updated">

			<?php if ( $this->last_action == 'delete' ) : ?>
			<p><?php echo sprintf( _n( '%d appointment deleted successfully.', '%d appointments deleted successfully.', $success, 'ultimate-appointment-scheduling' ), $success ); ?></p>

			<?php elseif ( $this->last_action == 'set-status-confirmed' ) : ?>
			<p><?php echo sprintf( _n( '%d appointment confirmed.', '%d appointments confirmed.', $success, 'ultimate-appointment-scheduling' ), $success ); ?></p>

			<?php elseif ( $this->last_action == 'set-status-paypal-received' ) : ?>
			<p><?php echo sprintf( _n( '%d appointment set payment received.', '%d appointments set to payment received.', $success, 'ultimate-appointment-scheduling' ), $success ); ?></p>

			<?php endif; ?>
		</div>

		<?php
		endif;

		if ( $failure > 0 ) :
		?>

		<div id="ewd-uasp-admin-notice-bulk-<?php esc_attr( $this->last_action ); ?>" class="error">
			<p><?php echo sprintf( _n( '%d appointment had errors and could not be processed.', '%d appointments had errors and could not be processed.', $failure, 'ultimate-appointment-scheduling' ), $failure ); ?></p>
		</div>

		<?php
		endif;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * This outputs a separate set of options above and below the table, in
	 * order to make room for the locations, services and providers.
	 *
	 * @since 1.6
	 */
	public function display_tablenav( $which ) {

		global $ewd_uasp_controller;


		// Just call the parent method for the bottom nav
		if ( 'bottom' == $which ) {
			parent::display_tablenav( $which );
			return;
		}

		$locations = $ewd_uasp_controller->cpts->get_locations( array() );
		$services = $ewd_uasp_controller->cpts->get_services( array() );
		$providers = $ewd_uasp_controller->cpts->get_service_providers( array() );
		?>

		<div class="tablenav top ewd-uasp-top-actions-wrapper">
			<?php wp_nonce_field( 'bulk-' . $this->_args['plural'] ); ?>
			<?php $this->extra_tablenav( $which ); ?>
		</div>

		<?php $this->add_notification(); ?>

		<div class="ewd-uasp-table-header-controls">
			<?php if ( $this->has_items() ) : ?>
				<div class="actions bulkactions">
					<?php $this->bulk_actions( $which ); ?>
				</div>
			<?php endif; ?>
			<select class="ewd-uasp-appointments-table-filter ewd-uasp-locations">
				<option <?php echo ( empty( $this->filter_location ) ? 'selected' : '' ); ?> data-link="<?php echo esc_url( remove_query_arg( 'location', $this->query_string ) ); ?>">
					<?php esc_html_e( 'All Locations', 'ultimate-appointment-scheduling' ); ?>
				</option>
				<?php foreach( $locations as $location ) { ?>

						<option <?php echo ( $this->filter_location == $location->ID ? 'selected' : '' ); ?> data-link="<?php echo esc_url( add_query_arg( 'location', $location->ID, $this->query_string ) ); ?>">
							<?php esc_html_e( $location->post_title ); ?>
						</option>
				<?php } ?>
			</select>
			<select class="ewd-uasp-appointments-table-filter ewd-uasp-services">
				<option <?php echo ( empty( $this->filter_service ) ? 'selected' : '' ); ?> data-link="<?php echo esc_url( remove_query_arg( 'service', $this->query_string ) ); ?>">
					<?php esc_html_e( 'All Services', 'ultimate-appointment-scheduling' ); ?>
				</option>
				<?php foreach( $services as $service ) { ?>

						<option <?php echo ( $this->filter_service == $service->ID ? 'selected' : '' ); ?> data-link="<?php echo esc_url( add_query_arg( 'service', $service->ID, $this->query_string ) ); ?>">
							<?php esc_html_e( $service->post_title ); ?>
						</option>
				<?php } ?>
			</select>
			<select class="ewd-uasp-appointments-table-filter ewd-uasp-providers">
				<option <?php echo ( empty( $this->filter_provider ) ? 'selected' : '' ); ?> data-link="<?php echo esc_url( remove_query_arg( 'provider', $this->query_string ) ); ?>">
					<?php esc_html_e( 'All Providers', 'ultimate-appointment-scheduling' ); ?>
				</option>
				<?php foreach( $providers as $provider ) { ?>

						<option <?php echo ( $this->filter_provider == $provider->ID ? 'selected' : '' ); ?> data-link="<?php echo esc_url( add_query_arg( 'provider', $provider->ID, $this->query_string ) ); ?>">
							<?php esc_html_e( $provider->post_title ); ?>
						</option>
				<?php } ?>
			</select>
		</div>

		<?php
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @param string pos Position of this tablenav: `top` or `btm`
	 * @since 1.4.1
	 */
	public function extra_tablenav( $pos ) {
		do_action( 'ewd_uasp_appointments_table_actions', $pos );
	}

	/**
	 * Add notifications above the table to indicate which appointments are
	 * being shown.
	 * @since 1.3
	 */
	public function add_notification() {

		global $ewd_uasp_controller;

		$notifications = array();

		$status = '';
		if ( !empty( $_GET['status'] ) ) {
			$status = sanitize_text_field( $_GET['status'] );
			if ( $status == 'confirmed' ) {
				$notifications['status'] = __( "You're viewing appointments that have been confirmed.", 'ultimate-appointment-scheduling' );
			}
			if ( $status == 'paid' ) {
				$notifications['paid'] = __( "You're viewing appointments that have been paid.", 'ultimate-appointment-scheduling' );
			}
		}

		if ( !empty( $this->filter_start_date ) || !empty( $this->filter_end_date ) ) {
			$notifications['date'] = sprintf( _x( 'Only appointments from %s are being shown.', 'Notification of booking date range, eg - appointments from 2014-12-02-2014-12-05', 'ultimate-appointment-scheduling' ), $this->get_current_date_range() );
		} elseif ( !empty( $_GET['date_range'] ) && $_GET['date_range'] == 'today' ) {
			$notifications['date'] = __( "Only today's appointments are being shown.", 'ultimate-appointment-scheduling' );
		} elseif ( empty( $_GET['date_range'] ) ) {
			$notifications['date'] = __( 'Only upcoming appointments are being shown.', 'ultimate-appointment-scheduling' );
		}

		$notifications = apply_filters( 'ewd_uasp_admin_appointments_table_filter_notifications', $notifications );

		if ( !empty( $notifications ) ) :
		?>

			<div class="ewd-uasp-notice <?php echo esc_attr( $status ); ?>">
				<?php echo join( ' ', $notifications ); ?>
			</div>

		<?php
		endif;
	}

	/**
	 * Retrieve the counts of appointments
	 * @since 2.0.0
	 */
	public function get_appointment_counts() {
		global $ewd_uasp_controller;

		$args = array();

		if ( $this->filter_start_date !== null || $this->filter_end_date !== null ) {

			if ( $this->filter_start_date !== null ) {

				$start_date = new DateTime( $this->filter_start_date . ' ' . $this->filter_start_time );
				$args['after'] = $start_date->format( 'Y-m-d H:i:s' );
			}

			if ( $this->filter_end_date !== null ) {

				$end_date = new DateTime( $this->filter_end_date . ' ' . $this->filter_end_time );
				$args['before'] = $end_date->format( 'Y-m-d H:i:s' );
			}

		} 
		elseif ( !empty( $_GET['date_range'] ) ) {

			$args['date_range'] = sanitize_text_field( $_GET['date_range'] );
		}
		else {
			$args['date_range']	= 'upcoming';
		}

		if ( $this->filter_location ) { $args['location'] = intval( $this->filter_location ); }
		if ( $this->filter_service ) { $args['service'] = intval( $this->filter_service ); }
		if ( $this->filter_provider ) { $args['provider'] = intval( $this->filter_provider ); }

		$this->appointment_counts = $ewd_uasp_controller->appointment_manager->get_appointment_counts( $args );
	}

	/**
	 * Retrieve all the data for all the appointments
	 * @since 2.0.0
	 */
	public function appointments_data() {
		global $ewd_uasp_controller;

		$args = array(
			'appointments_per_page'	=> $this->per_page,
		);

		if ( $this->filter_start_date !== null || $this->filter_end_date !== null ) {

			if ( !empty( $this->filter_start_date ) ) {

				$start_date = new DateTime( $this->filter_start_date . ' ' . $this->filter_start_time );
				$args['after'] = $start_date->format( 'Y-m-d H:i:s' );
			}
		
			if ( !empty( $this->filter_end_date ) ) {
			
				$end_date = new DateTime( $this->filter_end_date . ' ' . $this->filter_end_time );
				$args['before'] = $end_date->format( 'Y-m-d H:i:s' );
			}
		}
		elseif ( !empty( $_GET['date_range'] ) ) {

			$args['date_range'] = sanitize_text_field( $_GET['date_range'] );
		}

		if ( ! empty( $_GET['status'] ) ) {

			if ( $_GET['status'] == 'confirmed' ) { $args['confirmation'] = 'Yes'; } 
			if ( $_GET['status'] == 'paid' ) { $args['paid'] = 'Yes'; } 
		}

		if ( $this->filter_location ) { $args['location'] = intval( $this->filter_location ); }
		if ( $this->filter_service ) { $args['service'] = intval( $this->filter_service ); }
		if ( $this->filter_provider ) { $args['provider'] = intval( $this->filter_provider ); }

		$this->appointments = $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );
	}

	/**
	 * Setup the final data for the table
	 * @since 2.0.0
	 */
	public function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $this->appointments;

		$total_items   = empty( $_GET['status'] ) ? $this->appointment_counts['total'] : $this->appointment_counts[$_GET['status']];

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $total_items / $this->per_page )
			)
		);
	}

}
} // endif;
