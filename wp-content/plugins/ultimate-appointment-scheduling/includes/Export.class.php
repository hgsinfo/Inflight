<?php

/**
 * Class to export appointments created by the plugin
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if (!class_exists('ComposerAutoloaderInit4618f5c41cf5e27cc7908556f031e4d4')) { require_once EWD_UASP_PLUGIN_DIR . '/lib/PHPSpreadsheet/vendor/autoload.php'; }
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
class ewduaspExport {

	public function __construct() {
		add_action( 'admin_menu', array($this, 'register_install_screen' ));

		if ( isset( $_POST['ewd_uasp_export'] ) ) { add_action( 'admin_menu', array($this, 'export_appointments' )); }

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_export_scripts' ) );
	}

	public function register_install_screen() {
		global $ewd_uasp_controller;
		
		add_submenu_page( 
			'ewd-uasp-appointments', 
			'Export Menu', 
			'Export', 
			$ewd_uasp_controller->settings->get_setting( 'access-role' ), 
			'ewd-uasp-export', 
			array($this, 'display_export_screen') 
		);
	}

	public function display_export_screen() {
		global $ewd_uasp_controller;

		$export_permission = $ewd_uasp_controller->permissions->check_permission( 'export' );

		?>
		<div class='wrap'>
			<h2>Export</h2>
			<?php if ( $export_permission ) { ?> 
				<form method='post'>
					<?php wp_nonce_field( 'EWD_UASP_Export', 'EWD_UASP_Export_Nonce' );  ?>
					<input type='submit' name='ewd_uasp_export' value='Export to Spreadsheet' class='button button-primary' />
				</form>
			<?php } else { ?>
				<div class='ewd-uasp-premium-locked'>
					<a href="https://www.etoilewebdesign.com/license-payment/?Selected=UASP&Quantity=1" target="_blank">Upgrade</a> to the premium version to use this feature
				</div>
			<?php } ?>
		</div>
	<?php }

	public function export_appointments() {
		global $ewd_uasp_controller;

		if ( ! isset( $_POST['EWD_UASP_Export_Nonce'] ) ) { return; }

    	if ( ! wp_verify_nonce( $_POST['EWD_UASP_Export_Nonce'], 'EWD_UASP_Export' ) ) { return; }

		$custom_fields = ewd_uasp_decode_infinite_table_setting( $ewd_uasp_controller->settings->get_setting( 'custom-fields' ) );

		// Instantiate a new PHPExcel object
		$spreadsheet = new Spreadsheet();
		// Set the active Excel worksheet to sheet 0
		$spreadsheet->setActiveSheetIndex(0);

		// Print out the regular appointment field labels
		$spreadsheet->getActiveSheet()->setCellValue( 'A1', 'Appointment Start' );
		$spreadsheet->getActiveSheet()->setCellValue( 'B1', 'Appointment End' );
		$spreadsheet->getActiveSheet()->setCellValue( 'C1', 'Location Name' );
		$spreadsheet->getActiveSheet()->setCellValue( 'D1', 'Service Name' );
		$spreadsheet->getActiveSheet()->setCellValue( 'E1', 'Service Provider Name' );
		$spreadsheet->getActiveSheet()->setCellValue( 'F1', 'Client Name' );
		$spreadsheet->getActiveSheet()->setCellValue( 'G1', 'Client Phone' );
		$spreadsheet->getActiveSheet()->setCellValue( 'H1', 'Client Email' );
		$spreadsheet->getActiveSheet()->setCellValue( 'I1', 'Appointment Confirmed' );

		$column = 'J';
		foreach ( $custom_fields as $custom_field ) {

			$spreadsheet->getActiveSheet()->setCellValue( $column . '1', $custom_field->name );
    		$column++;
		}

		//start while loop to get data
		$row_count = 2;

		$args = array(
			'start_date'	=> date( 'Y-m-d', time() - 30*24*3600 )
		);

		$appointments = $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );

		foreach ( $appointments as $appointment ) {

    	 	$spreadsheet->getActiveSheet()->setCellValue( 'A' . $row_count, $appointment->start );
    	 	$spreadsheet->getActiveSheet()->setCellValue( 'B' . $row_count, $appointment->end );
    	 	$spreadsheet->getActiveSheet()->setCellValue( 'C' . $row_count, $appointment->location_name );
    	 	$spreadsheet->getActiveSheet()->setCellValue( 'D' . $row_count, $appointment->service_name );
    	 	$spreadsheet->getActiveSheet()->setCellValue( 'E' . $row_count, $appointment->provider_name );
    	 	$spreadsheet->getActiveSheet()->setCellValue( 'F' . $row_count, $appointment->client_name );
    	 	$spreadsheet->getActiveSheet()->setCellValue( 'G' . $row_count, $appointment->client_phone );
    	 	$spreadsheet->getActiveSheet()->setCellValue( 'H' . $row_count, $appointment->client_email );
    	 	$spreadsheet->getActiveSheet()->setCellValue( 'I' . $row_count, $appointment->confirmed );

			$column = 'J';
			foreach ( $custom_fields as $custom_field ) {

				$spreadsheet->getActiveSheet()->setCellValue( $column . $row_count, $ewd_uasp_controller->appointment_manager->get_field_value( $custom_field->id, $appointment->id ) );
   				$column++;
			}
			
    		$row_count++;
		}

		// Redirect output to a client’s web browser (Excel5)
		if (!isset($format_type) == "csv") {

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="appointments_export.csv"');
			header('Cache-Control: max-age=0');
			$objWriter = new Csv($spreadsheet);
			$objWriter->save('php://output');
			die();
		}
		else {

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="appointments_export.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = new Xls($spreadsheet);
			$objWriter->save('php://output');
			die();
		}
	}

	public function enqueue_export_scripts() {

		$screen = get_current_screen();

		if ( $screen->id == 'urp_review_page_fdm-export' ) {

			wp_enqueue_style( 'ewd-uasp-admin', EWD_UASP_PLUGIN_URL . '/assets/css/ewd-uasp-admin.css', array(), EWD_UASP_VERSION );
			wp_enqueue_script( 'ewd-uasp-admin-js', EWD_UASP_PLUGIN_URL . '/assets/js/ewd-uasp-admin.js', array( 'jquery' ), EWD_UASP_VERSION, true );
		}
	}

}


