<?php

/**
 * Class to handle importing appointments into the plugin
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if (!class_exists('ComposerAutoloaderInit4618f5c41cf5e27cc7908556f031e4d4')) {require_once EWD_UASP_PLUGIN_DIR . '/lib/PHPSpreadsheet/vendor/autoload.php';}
use PhpOffice\PhpSpreadsheet\Spreadsheet;
class ewduaspImport {

	public $status;
	public $message;

	public function __construct() {
		add_action( 'admin_menu', array($this, 'register_install_screen' ));

		if ( isset( $_POST['ewduaspImport'] ) ) { add_action( 'admin_init', array($this, 'import_appointments' )); }

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_import_scripts' ) );
	}

	public function register_install_screen() {
		global $ewd_uasp_controller;
		
		add_submenu_page( 
			'ewd-uasp-appointments', 
			'Import Menu', 
			'Import', 
			$ewd_uasp_controller->settings->get_setting( 'access-role' ), 
			'ewd-uasp-import', 
			array($this, 'display_import_screen') 
		);
	}

	public function display_import_screen() {
		global $ewd_uasp_controller;

		$import_permission = $ewd_uasp_controller->permissions->check_permission( 'import' );
		?>
		<div class='wrap'>
			<h2>Import</h2>
			<?php if ( $import_permission ) { ?> 
				<form method='post' enctype="multipart/form-data">
					
					<?php wp_nonce_field( 'EWD_UASP_Import', 'EWD_UASP_Import_Nonce' );  ?>

					<p>
						<label for="ewd_uasp_appointments_spreadsheet"><?php _e( 'Spreadsheet Containing Appointments', 'ultimate-appointment-scheduling' ) ?></label><br />
						<input name="ewd_uasp_appointments_spreadsheet" type="file" value=""/>
					</p>
					<input type='submit' name='ewduaspImport' value='Import Appointments' class='button button-primary' />
				</form>
			<?php } else { ?>
				<div class='ewd-uasp-premium-locked'>
					<a href="https://www.etoilewebdesign.com/license-payment/?Selected=UASP&Quantity=1" target="_blank">Upgrade</a> to the premium version to use this feature
				</div>
			<?php } ?>
		</div>
	<?php }

	public function import_appointments() {
		global $ewd_uasp_controller;

		if ( ! current_user_can( 'edit_posts' ) ) { return; }

		if ( ! isset( $_POST['EWD_UASP_Import_Nonce'] ) ) { return; }

    	if ( ! wp_verify_nonce( $_POST['EWD_UASP_Import_Nonce'], 'EWD_UASP_Import' ) ) { return; }

		$update = $this->handle_spreadsheet_upload();

    	$custom_fields = ewd_ufaq_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );

		if ( $update['message_type'] != 'Success' ) :
			$this->status = false;
			$this->message =  $update['message'];

			add_action( 'admin_notices', array( $this, 'display_notice' ) );

			return;
		endif;

		$excel_url = EWD_UASP_PLUGIN_DIR . '/appointment-sheets/' . $update['filename'];

	    // Build the workbook object out of the uploaded spreadsheet
	    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $excel_url );
	
	    // Create a worksheet object out of the product sheet in the workbook
	    $sheet = $spreadsheet->getActiveSheet();
	
	    $allowable_custom_fields = array();
	    foreach ( $custom_fields as $custom_field ) { $allowable_custom_fields[] = $custom_field->name; }
	    //List of fields that can be accepted via upload
	    $allowed_fields = array( 'Appointment Start', 'Appointment End', 'Location Name', 'Service Name', 'Service Provider Name', 'Client Name', 'Client Phone', 'Client Email', 'Appointment Confirmed' );
	
	    // Get column names
	    $highest_column = $sheet->getHighestColumn();
	    $highest_column_index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString( $highest_column );
	    for ( $column = 1; $column <= $highest_column_index; $column++ ) {

	    	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Appointment Start' ) { $start_column = $column; }
        	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Appointment End' ) { $end_column = $column; }
        	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Location Name' ) { $location_column = $column; }
        	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Service Name' ) { $service_column = $column; }
        	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Service Provider Name' ) { $provider_column = $column; }
        	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Client Name' ) { $client_name_column = $column; }
        	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Client Phone' ) { $client_phone_column = $column; }
        	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Client Email' ) { $client_email_column = $column; }
        	if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == 'Appointment Confirmed' ) { $confirmation_column = $column; }
	
	        foreach ( $custom_fields as $custom_field ) {

        	    if ( trim( $sheet->getCellByColumnAndRow( $column, 1 )->getValue() ) == $custom_field->name ) { $custom_field->column = $column; }
        	}
	    }

	    $start_column = ! empty( $start_column ) ? $start_column : -1;
	    $end_column = ! empty( $end_column ) ? $end_column : -1;
	    $location_column = ! empty( $location_column ) ? $location_column : -1;
	    $service_column = ! empty( $service_column ) ? $service_column : -1;
	    $provider_column = ! empty( $provider_column ) ? $provider_column : -1;
	    $client_name_column = ! empty( $client_name_column ) ? $client_name_column : -1;
	    $client_phone_column = ! empty( $client_phone_column ) ? $client_phone_column : -1;
	    $client_email_column = ! empty( $client_email_column ) ? $client_email_column : -1;
	    $confirmation_column = ! empty( $confirmation_column ) ? $confirmation_column : -1;
	
	    // Put the spreadsheet data into a multi-dimensional array to facilitate processing
	    $highest_row = $sheet->getHighestRow();
	    for ( $row = 2; $row <= $highest_row; $row++ ) {
	        for ( $column = 1; $column <= $highest_column_index; $column++ ) {
	            $data[$row][$column] = $sheet->getCellByColumnAndRow( $column, $row )->getValue();
	        }
	    }
	
	    // Create the query to insert the products one at a time into the database and then run it
	    foreach ( $data as $appointment_data ) {
	        
	        // Create a new appointment object, and assign the imported values to it
	     	$appointment = new ewduaspAppointment();
	        foreach ( $appointment_data as $col_index => $value ) {

	            if ( $col_index == $start_column ) { $appointment->start = esc_sql( $value ); }
            	elseif ( $col_index == $end_column ) { $appointment->end = esc_sql( $value ); }
            	elseif ( $col_index == $location_column ) { $appointment->location_name = esc_sql( $value ); }
            	elseif ( $col_index == $service_column ) { $appointment->service_name = esc_sql( $value ); }
            	elseif ( $col_index == $provider_column ) { $appointment->provider_name = esc_sql( $value ); }
            	elseif ( $col_index == $client_name_column ) { $appointment->client_name = esc_sql( $value ); }
            	elseif ( $col_index == $client_phone_column ) { $appointment->client_phone = esc_sql( $value ); }
            	elseif ( $col_index == $client_email_column ) { $appointment->client_email = esc_sql( $value ); }
            	elseif ( $col_index == $confirmation_column ) { $appointment->confirmed = esc_sql( $value ); }
            	else {

            		foreach ( $custom_fields as $custom_field ) {

            			if ( $col_index == $custom_field->column ) { $appointment->custom_fields[ $custom_field->id ] = esc_sql( $value ); }
            		}
            	}
	        }

	        $location = ! empty( $appointment->location_name ) ? get_page_by_title( $appointment->location_name, OBJECT, EWD_UASP_LOCATION_POST_TYPE ) : false;
	        $appointment->location = $location ? $location->ID : null;

	        $service = ! empty( $appointment->service_name ) ? get_page_by_title( $appointment->service_name, OBJECT, EWD_UASP_SERVICE_POST_TYPE ) : false;
	        $appointment->service = $service ? $service->ID : null;

	        $provider = ! empty( $appointment->provider_name ) ? get_page_by_title( $appointment->provider_name, OBJECT, EWD_UASP_PROVIDER_POST_TYPE ) : false;
	        $appointment->provider = $provider ? $provider->ID : null;
	        
	        $appointment->insert_appointment();
	    }

	    $this->status = true;
		$this->message = __( 'Appointments added successfully.', 'ultimate-appointment-scheduling' );

		add_action( 'admin_notices', array( $this, 'display_notice' ) );
	}

	function handle_spreadsheet_upload() {
		  /* Test if there is an error with the uploaded spreadsheet and return that error if there is */
        if ( ! empty( $_FILES['ewd_uasp_appointments_spreadsheet']['error'] ) ) {
                
            switch( $_FILES['ewd_uasp_appointments_spreadsheet']['error'] ) {

                case '1':
                    $error = __( 'The uploaded file exceeds the upload_max_filesize directive in php.ini', 'ultimate-appointment-scheduling' );
                    break;
                case '2':
                    $error = __( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'ultimate-appointment-scheduling' );
                    break;
                case '3':
                    $error = __( 'The uploaded file was only partially uploaded', 'ultimate-appointment-scheduling' );
                    break;
                case '4':
                    $error = __( 'No file was uploaded.', 'ultimate-appointment-scheduling' );
                    break;

                case '6':
                    $error = __( 'Missing a temporary folder', 'ultimate-appointment-scheduling' );
                    break;
                case '7':
                    $error = __( 'Failed to write file to disk', 'ultimate-appointment-scheduling' );
                    break;
                case '8':
                    $error = __( 'File upload stopped by extension', 'ultimate-appointment-scheduling' );
                    break;
                case '999':
                    default:
                    $error = __( 'No error code avaiable', 'ultimate-appointment-scheduling' );
            }
        }
        /* Make sure that the file exists */
        elseif ( empty($_FILES['ewd_uasp_appointments_spreadsheet']['tmp_name']) || $_FILES['ewd_uasp_appointments_spreadsheet']['tmp_name'] == 'none' ) {
                $error = __( 'No file was uploaded here..', 'ultimate-appointment-scheduling' );
        }
        /* Move the file and store the URL to pass it onwards*/
        /* Check that it is a .xls or .xlsx file */ 
        if ( ! isset($_FILES['ewd_uasp_appointments_spreadsheet']['name'] ) or ( ! preg_match("/\.(xls.?)$/", $_FILES['ewd_uasp_appointments_spreadsheet']['name'] ) and ! preg_match( "/\.(csv.?)$/", $_FILES['ewd_uasp_appointments_spreadsheet']['name'] ) ) ) {
            $error = __( 'File must be .csv, .xls or .xlsx', 'ultimate-appointment-scheduling' );
        }
        else {
            $filename = basename( $_FILES['ewd_uasp_appointments_spreadsheet']['name'] );
            $filename = mb_ereg_replace( "([^\w\s\d\-_~,;\[\]\(\).])", '', $filename );
            $filename = mb_ereg_replace ("([\.]{2,})", '', $filename );

            //for security reason, we force to remove all uploaded file
            $target_path = EWD_UASP_PLUGIN_DIR . "/appointment-sheets/";

            $target_path = $target_path . $filename;

            if ( ! move_uploaded_file($_FILES['ewd_uasp_appointments_spreadsheet']['tmp_name'], $target_path ) ) {
                $error .= "There was an error uploading the file, please try again!";
            }
            else {
                $excel_file_name = $filename;
            }
        }

        /* Pass the data to the appropriate function in Update_Admin_Databases.php to create the products */
        if ( ! isset( $error ) ) {
                $update = array( "message_type" => "Success", "filename" => $excel_file_name );
        }
        else {
                $update = array( "message_type" => "Error", "message" => $error );
        }

        return $update;
	}

	public function enqueue_import_scripts() {

		$screen = get_current_screen();

		if ( $screen->id == 'fdm-menu_page_fdm-import' ) {

			wp_enqueue_style( 'fdm-admin', EWD_UASP_PLUGIN_URL . '/assets/css/admin.css', array(), '2.0.0' );
			wp_enqueue_script( 'fdm-admin-js', EWD_UASP_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), '2.0.0', true );
		}
	}

	public function display_notice() {

		if ( $this->status ) {

			echo "<div class='updated'><p>" . $this->message . "</p></div>";
		}
		else {

			echo "<div class='error'><p>" . $this->message . "</p></div>";
		}
	}

}


