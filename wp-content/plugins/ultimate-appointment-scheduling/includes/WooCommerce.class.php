<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduaspWooCommerce' ) ) {
	/**
	 * Class to handle WooCommerce integration for Ultimate Appointment Scheduling
	 *
	 * @since 2.0.0
	 */
	class ewduaspWooCommerce {

		public function __construct() {

			add_action( 'woocommerce_thankyou', array( $this, 'handle_woocommerce_checkout' ) );

			add_action( 'init', array( $this, 'handle_appointment_deletion' ) );

			if ( ! empty( $_POST['ewd-uasp-settings']['woocommerce-integration'] ) ) { add_action( 'init', array( $this, 'initial_sync' ) ); }

			add_action( 'publish_' . EWD_UASP_SERVICE_POST_TYPE, 	array( $this, 'create_linked_product' ) );
			add_action( 'save_post', 								array( $this, 'update_linked_product' ), 11 );
		}

		/**
		 * Syncs all WC products when payment options are saved, if enabled
		 * @since 2.0.0
		 */
		public function initial_sync() {

			$args = array(
				'posts_per_page'	=> -1,
				'post_type'			=> EWD_UASP_SERVICE_POST_TYPE
			);

			$services = get_posts( $args );

			foreach ( $services as $service ) { $this->create_linked_product( $service->ID ); }
		}

		/**
		 * Creates a WC product with a name/price matching those of a particular service
		 * @since 2.0.0
		 */
		public function create_linked_product( $service_id ) {
			global $ewd_uasp_controller;

			if ( ! $ewd_uasp_controller->settings->get_setting( 'woocommerce-integration' ) ) { return; }

			$args = array(
				'post_type' => 'product',
				'meta_query' => array(
					array(
						'key' => 'EWD_UASP_Service_ID',
						'value' => $service_id,
						'compare' => '='
					)
				)
			);
			
			$service_query = new WP_Query($args);

			if ( $service_query->post_count ) { return; }

			$args = array(
				'post_title' => get_the_title( $service_id ),
				'post_content' => get_the_content( $service_id ),
				'post_status' => 'publish',
				'post_type' => 'product' 
			);

			$post_id = wp_insert_post( $args );

			update_post_meta( $post_id, 'EWD_UASP_Service_ID', $service_id );

			wp_set_object_terms( $post_id, 'simple', 'product_type' );

			update_post_meta( $post_id, '_regular_price', get_post_meta( $service_id, 'Service Price', true ) );
			update_post_meta( $post_id, '_price', get_post_meta( $service_id, 'Service Price', true ) );
		}

		/**
		 * Updates the price of a linked WC product when a particular service is updated
		 * @since 2.0.0
		 */
		public function update_linked_product( $service_id ) {
			global $ewd_uasp_controller;
			
			if ( ! $ewd_uasp_controller->settings->get_setting( 'woocommerce-integration' ) ) { return; }

			elseif ( get_post_type( $service_id ) != EWD_UASP_SERVICE_POST_TYPE ) { return; }

			$args = array(
				'post_type' => 'product',
				'meta_query' => array(
					array(
						'key' => 'EWD_UASP_Service_ID',
						'value' => $service_id,
						'compare' => '='
					)
				)
			);
			
			$service_query = new WP_Query($args);

			if ( ! $service_query->post_count ) { return; }

			update_post_meta( $service_query->posts[0]->ID, '_regular_price', get_post_meta( $service_id, 'Service Price', true ) );
			update_post_meta( $service_query->posts[0]->ID, '_price', get_post_meta( $service_id, 'Service Price', true ) );
		}

		/**
		 * Adds an appointment to be deleted if payment is not received within 15 minutes
		 * @since 2.0.0
		 */
		public function add_possible_appointment_deletion( $appointment ) {

			$delete_appointments = (array) get_option( 'EWD_UASP_WC_Delete_Appointments' );

			$delete_appointments[ $appointment->id ] = time() + 60*15;

			update_option( 'EWD_UASP_WC_Delete_Appointments', $delete_appointments );
		}

		/**
		 * Updates the information for an appointment after checkout, if enabled
		 * @since 2.0.0
		 */
		public function handle_woocommerce_checkout( $order_id ) {
			global $ewd_uasp_controller;

			if ( ! $ewd_uasp_controller->settings->get_setting( 'woocommerce-integration' ) ) { return; }

			if ( ! $order_id ) { return; }

			$order = wc_get_order( $order_id );

			$appointment_id = 0;
			foreach ( $order->get_items() as $item_id => $item ) {

				if ( ! empty( $item->cart_item_data['appointment_id'] ) ) { $appointment_id = intval( $item->cart_item_data['appointment_id'] ); }
			}

			if ( ! $appointment_id ) { return; }

			$args = array(
				'id' => $appointment_id
			);

			$appointments = $ewd_uasp_controller->appointment_manager->get_matching_appointments( $args );

			$appointment = new ewduaspAppointment();
			$appointment->load_appointment( reset( $appointments ) );

			$appointment->wc_prepaid = true;
			$appointment->wc_order_id = $order_id;

			$appointment->update_appointment();

			$this->remove_appointment_deletion( $appointment );
		}

		/**
		 * Stops an appointment from being deleted
		 * @since 2.0.0
		 */
		public function remove_appointment_deletion( $appointment ) {

			$delete_appointments = (array) get_option( 'EWD_UASP_WC_Delete_Appointments' );

			if ( ! empty( $delete_appointments[ $appointment->id ] ) ) { unset( $delete_appointments[ $appointment->id ] ); }

			update_option( 'EWD_UASP_WC_Delete_Appointments' );
		}

		/**
		 * Periodically check to see if there are unpaid for appointments that should be deleted
		 * @since 2.0.0
		 */
		public function handle_appointment_deletion() {
			global $ewd_uasp_controller;

			if ( ! $ewd_uasp_controller->settings->get_setting( 'woocommerce-integration' ) ) { return; }

			$delete_appointments = (array) get_option( 'EWD_UASP_WC_Delete_Appointments' );

			foreach ( $delete_appointments as $appointment_id => $deletion_time ) {

				if ( $deletion_time < time() ) {

					$ewd_uasp_controller->appointment_manager->delete_appointment( $appointment_id );	

					unset( $delete_appointments[ $appointment_id ] );
				}
			}

			update_option( 'EWD_UASP_WC_Delete_Appointments', $delete_appointments );
		}

		/**
		 * Adds an appointment's service item to the WC cart, along with the appointment ID in the item data
		 * @since 2.0.0
		 */
		public function add_service_to_cart( $appointment ) {
			global $woocommerce;

			if ( empty( $woocommerce ) ) { return; }

			$args = array(
				'post_type' => 'product',
				'meta_query' => array(
					array(
						'key' => 'EWD_UASP_Service_ID',
						'value' => $appointment->service,
						'compare' => '='
					)
				)
			);

			$service_query = new WP_Query( $args );

			if ( $service_query->post_count ) {

				$item_data = array(
					'appointment_id' => $appointment->id
				);

				$wc_product = $service_query->posts[0];

				@$woocommerce->cart->add_to_cart( $wc_product->ID, 1, 0, array(), $item_data );
			}
		}
	}
}