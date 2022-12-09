<?php

if(!class_exists('wf_ups_accesspoint_locator')){
	
	class wf_ups_accesspoint_locator{

		//UPS Accesspoint Location Options Code
		private $ups_service_provider_code = array(
			"014",
			"015",
			"016"
		);
		
		public function __construct(){
			
			$this->settings 			= get_option( 'woocommerce_'.WF_UPS_ID.'_settings', null );
			$this->debug	  			= isset( $this->settings['debug'] ) && $this->settings['debug'] == 'yes' ? true : false;
			$this->accesspoint_locator 			= (isset($this->settings[ 'accesspoint_locator']) && $this->settings[ 'accesspoint_locator']=='yes') ? true : false;
			$this->api_mode      				= isset( $this->settings['api_mode'] ) ? $this->settings['api_mode'] : 'Test';
			$this->ssl_verify					= isset( $this->settings['ssl_verify'] ) ? $this->settings['ssl_verify'] : false;
			
			if( "Live" == $this->api_mode ) {
				$this->endpoint = 'https://onlinetools.ups.com/ups.app/xml/Locator';
			}
			else {
				$this->endpoint = 'https://wwwcie.ups.com/ups.app/xml/Locator';
			}
				
			$this->user_id         		= isset( $this->settings['user_id'] ) ? $this->settings['user_id'] : '';
			$this->password        		= isset( $this->settings['password'] ) ? str_replace( '&', '&amp;',$this->settings['password']) : '';
			$this->access_key      		= isset( $this->settings['access_key'] ) ? $this->settings['access_key'] : '';
			
			$this->accesspoint_req_option	= (isset( $this->settings['accesspoint_req_option']) && !empty($this->settings['accesspoint_req_option'])) ? $this->settings['accesspoint_req_option'] : '1';
			$this->accesspoint_max_limit	=	(isset($this->settings[ 'accesspoint_max_limit' ]) && !empty($this->settings[ 'accesspoint_max_limit' ])) ? $this->settings[ 'accesspoint_max_limit' ] : '10';
			$this->accesspoint_option_code  = (isset($this->settings[ 'accesspoint_option_code' ]) && !empty($this->settings[ 'accesspoint_option_code' ])) ? $this->settings[ 'accesspoint_option_code' ] : array('018');

			if($this->accesspoint_locator){
				$this->init();
			}
		}
		
		private function init(){

			add_filter( 'wp_enqueue_scripts', array( $this, 'ph_ups_checkout_scripts_for_access_point' ) );

			add_action( 'woocommerce_before_shipping_calculator', array($this, 'reset_accesspoint_before_shipping_calculator'), 10, 0 ); 

			//add accesspoint select field in checkout page
			add_filter( 'woocommerce_checkout_fields' , array( $this, 'wf_ups_add_accesspoint_to_checkout_fields') );
            
			add_filter( 'woocommerce_order_formatted_billing_address', array($this,'wf_ups_order_formatted_billing_address'),10,3 );
			add_filter( 'woocommerce_order_formatted_shipping_address', array($this,'wf_ups_order_formatted_shipping_address'),10,3 );
			
			add_filter( 'woocommerce_formatted_address_replacements',  array($this,'wf_ups_formatted_address_replacements'),10,3  );
			add_filter('woocommerce_localisation_address_formats', array($this,'wf_ups_address_formats'));
			
			//Display access point in my-account/address
			add_filter( 'woocommerce_my_account_my_address_formatted_address',array($this,'wf_ups_my_account_formated_address'),10,3 );
			
			// Giving options to access point select box while calling ajax
			add_filter( 'woocommerce_update_order_review_fragments', array($this,'update_access_point_select_options'), 90, 1);
			
			//Updating Selected accesspoint value
			add_action( 'woocommerce_checkout_update_order_review', array($this,'wf_ups_update_accesspoint'), 1, 1 );
			// Add selected accesspoint details in Woocommerce cart shipping packages.
			add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'ph_ups_update_access_point_details_in_package') );
			
			// Save Access Point Details in Meta Key for Order
			add_action('woocommerce_checkout_update_order_meta', array( $this, 'ph_add_access_point_meta_data'), 12, 2);

			// Restrict Payments Gateways if Access Point Location is selected
			// add_filter( 'woocommerce_available_payment_gateways', array( $this, 'ph_hide_payment_gateways_based_on_access_point_location'), 100, 1 );

		}

		// public function ph_hide_payment_gateways_based_on_access_point_location( $available_gateways ) {
		// 	if( isset(WC()->session) && !empty(WC()->session) && !empty(WC()->session->get('ph_ups_selected_access_point_details') ) && !empty($this->restricted_payments) ) {
		// 		foreach( $available_gateways as $gateways_id => $gateways ) {
		// 			if( in_array($gateways_id, $this->restricted_payments) ) {
		// 				unset($available_gateways[$gateways_id]);
		// 			}
		// 		}
		// 	}
		// 	return $available_gateways;
		// }

		public function ph_ups_checkout_scripts_for_access_point() {

			if ( is_checkout() ) {

				wp_enqueue_script( 'ph-ups-checkout-script', plugins_url( '../resources/js/ph_ups_checkout.js', __FILE__ ), array( 'jquery' ) );
			}
		}

		/**
		 * Update Access Point Details in Woocommerce Packages.
		 * @param array $packages Array of Woocommerce Packages.
		 * @return array
		 */
		public function ph_ups_update_access_point_details_in_package( $packages ) {
			$accesspoint_details = WC()->session->get('ph_ups_access_point_details');
			foreach( $packages as &$package ) {
				if( ! empty($package['contents']) ) {
					$selected_access_point_details = WC()->session->get('ph_ups_selected_access_point_details');
					
					if( ! empty($selected_access_point_details && !empty($accesspoint_details) ) ) {
						
						foreach( $accesspoint_details as $locator_id => $locator ){

							if( $locator_id == $selected_access_point_details ){
								$package['ph_ups_selected_access_point_details'] = $locator;
							}
						}

					}
				}
			}
			return $packages;
		}

		public function reset_accesspoint_before_shipping_calculator(){
			$this->wf_update_accesspoint_datas();
		}

		public function wf_ups_add_accesspoint_to_checkout_fields( $fields ) {
			$fields['billing']['shipping_accesspoint'] = array(
				'label'       => __('Pick up your package at a UPS Access Point® locations', 'ups-woocommerce-shipping'),
				'placeholder' => _x('', 'placeholder', 'ups-woocommerce-shipping'),
				'required'    => false,
				'clear'       => false,
				'type'        => 'select',
				'class' 	  => array ('address-field', 'update_totals_on_change' ),
				'priority'		=>	'120',
				'options'     => array(
					'' => __('Select UPS Access Point® Location', 'ups-woocommerce-shipping' )
					)
			);
			return apply_filters('xa_checkout_fields', $fields, $this->settings);
		}
		
		public function wf_ups_order_formatted_billing_address( $array,$address_fields ) { 
				$array['accesspoint'] = '';
				return $array; 
		}
		
		private function wf_get_accesspoint_datas( $order_details='' ){
			if( !empty( $order_details ) ){
				if( WC()->version < '2.7.0' ){
					return ( isset($order_details->shipping_accesspoint) ) ? json_decode( $order_details->shipping_accesspoint ) : '';
				}else{
					$address_field = $order_details->get_meta('_shipping_accesspoint');
					return json_decode($address_field);
				}
			}else{
				return WC()->session->get('ph_ups_selected_access_point_details');
			}
		}

		public function wf_ups_order_formatted_shipping_address( $array,$address_fields ) { 
			$decoded_order_formatted_accesspoint	=	$this->wf_get_accesspoint_datas($address_fields);
			
			if( is_admin() ){
				
				$accesspoint_locators = get_post_meta( $address_fields->get_id(), '_ph_accesspoint_location', true );
			}
			else{
				$accesspoint_locators = WC()->session->get('ph_ups_access_point_details');
			}


			if( is_object($decoded_order_formatted_accesspoint)){
				$accesspoint_locator_id = json_encode($decoded_order_formatted_accesspoint);
			}else{
				$accesspoint_locator_id = $decoded_order_formatted_accesspoint;
			}

			$decoded_accesspoint_location = json_decode($accesspoint_locator_id,true);
			
			if( is_array( $decoded_accesspoint_location )){
				$accesspoint_locator_id = implode('',$decoded_accesspoint_location['LocationID']);
			}
			
			$selected_accesspoint_locator = '';
			if( !empty( $accesspoint_locators ) )
				if(is_array( $accesspoint_locators ) ) {
					foreach( $accesspoint_locators as $locator_id => $locator ){
						
						if( $locator_id == $accesspoint_locator_id ){
							$selected_accesspoint_locator = $locator;
							break;
						}
					}

					$decoded_order_formatted_accesspoint = json_decode($selected_accesspoint_locator);
				}
				else{

					$decoded_order_formatted_accesspoint = json_decode($accesspoint_locators);
				}


			
			$order_shipping_accesspoint = (isset($decoded_order_formatted_accesspoint->AddressKeyFormat->ConsigneeName)) ? $decoded_order_formatted_accesspoint->AddressKeyFormat->ConsigneeName : '';
			$order_shipping_accesspoint .= (isset($decoded_order_formatted_accesspoint->AddressKeyFormat->AddressLine)) ? ', '.$decoded_order_formatted_accesspoint->AddressKeyFormat->AddressLine : '';
			$order_shipping_accesspoint .= (isset($decoded_order_formatted_accesspoint->AddressKeyFormat->PoliticalDivision1)) ? ', '.$decoded_order_formatted_accesspoint->AddressKeyFormat->PoliticalDivision1 : '';
			$order_shipping_accesspoint .= (isset($decoded_order_formatted_accesspoint->AddressKeyFormat->PoliticalDivision2)) ? ', '.$decoded_order_formatted_accesspoint->AddressKeyFormat->PoliticalDivision2 : '';
			$order_shipping_accesspoint .= (isset($decoded_order_formatted_accesspoint->AddressKeyFormat->PostcodePrimaryLow)) ? ', '.$decoded_order_formatted_accesspoint->AddressKeyFormat->PostcodePrimaryLow : '';
			$order_shipping_accesspoint .= (isset($decoded_order_formatted_accesspoint->AddressKeyFormat->CountryCode)) ? ', '.$decoded_order_formatted_accesspoint->AddressKeyFormat->CountryCode : '';
			
			
			$array['accesspoint'] = $order_shipping_accesspoint; 
			return $array; 
		}
		
		public function wf_ups_my_account_formated_address($array,$customer_id,$name  ) { 
				$getting_accesspoint = get_user_meta( $customer_id, $name . '_accesspoint', true );
				$decoded_my_account_accesspoint	=	( isset($getting_accesspoint) ) ? json_decode($getting_accesspoint) : '';
				

				$my_account_shipping_accesspoint	=	(isset($decoded_my_account_accesspoint->AddressKeyFormat->ConsigneeName)) ? $decoded_my_account_accesspoint->AddressKeyFormat->ConsigneeName : '';
				
				$array['accesspoint'] = ($name . '_accesspoint' == 'shipping_accesspoint') ? $my_account_shipping_accesspoint :'';
				return $array; 
		}
		
		public function wf_ups_formatted_address_replacements( $array, $accesspoint_locator ) {
			$accesspoint_tag = ! empty($accesspoint_locator['accesspoint']) ? __( 'Pick up your package at a UPS Access Point® locations: ', 'ups-woocommerce-shipping' ).$accesspoint_locator['accesspoint'] :'';
			$array['{accesspoint}'] = $accesspoint_tag;
			return $array; 
		}
		
		public function wf_ups_address_formats( $formats ) {
			foreach ($formats as $key => $format) {
				$formats[$key]=$format."\n{accesspoint}";
			}		
			return $formats;
		}
		
		public function update_access_point_select_options($array){
			$old_response = $response = null;
			$shipping_address = WC()->customer->get_shipping_address();
			$shipping_city = WC()->customer->get_shipping_city();
			$shipping_postalcode = WC()->customer->get_shipping_postcode();
			$shipping_state = WC()->customer->get_shipping_state();
			$shipping_country = WC()->customer->get_shipping_country();
			
			if( empty($shipping_country) ){
				return;
			}

			$option_code = '';

			foreach( $this->accesspoint_option_code as $code )
			{

				if( $code == '014' ) {
					foreach( $this->ups_service_provider_code as $service_provider_code ) {
						$option_code .= '<OptionCode>
						<Code>'.$service_provider_code.'</Code>
						</OptionCode>';
					}
				}
				else{
					$option_code .= '<OptionCode>
					<Code>'.$code.'</Code>
					</OptionCode>';
				}
			}

			$xmlRequest = '<?xml version="1.0"?>
			<AccessRequest xml:lang="en-US">
				<AccessLicenseNumber>'.$this->access_key.'</AccessLicenseNumber>
				<UserId>'.$this->user_id.'</UserId>
				<Password>'.$this->password.'</Password>
			</AccessRequest>
			<?xml version="1.0"?>
			<LocatorRequest>
				<Request>
					<RequestAction>Locator</RequestAction>
					<RequestOption>'.$this->accesspoint_req_option.'</RequestOption>
				</Request>
				<OriginAddress>
					<PhoneNumber>1234567891</PhoneNumber>
					<AddressKeyFormat>
						<ConsigneeName>yes</ConsigneeName>
						<AddressLine>'.$shipping_address.'</AddressLine>
						<PoliticalDivision2>'.$shipping_city.'</PoliticalDivision2>
						<PoliticalDivision1>'.$shipping_state.'</PoliticalDivision1>
						<PostcodePrimaryLow>'.$shipping_postalcode.'</PostcodePrimaryLow>
						<CountryCode>'.$shipping_country.'</CountryCode>
					</AddressKeyFormat>
				</OriginAddress>
				<Translate>
					<Locale>en_US</Locale>
				</Translate>
				<UnitOfMeasurement>
					<Code>MI</Code>
				</UnitOfMeasurement>
				<LocationSearchCriteria>
					<SearchOption>
						<OptionType>
							<Code>01</Code>
						</OptionType>';

			$xmlRequest .= $option_code;

			$xmlRequest .='</SearchOption>
			<MaximumListSize>'.$this->accesspoint_max_limit.'</MaximumListSize>
			<SearchRadius>50</SearchRadius>
			</LocationSearchCriteria>
			</LocatorRequest>';

			$xmlRequest 		= apply_filters( 'ph_ups_access_point_xml_request', $xmlRequest, $this->settings );
			$transient			= 'ph_ups_access_point' . md5( $xmlRequest );
			$cached_response	= get_transient( $transient );
			$response			= $cached_response;

			if( empty($cached_response) ) {
				try{
					$response = wp_remote_post( $this->endpoint,
						array(
							'timeout'   => 70,
							'sslverify' => $this->ssl_verify,
							'body'      => $xmlRequest
						)
					);
				}catch(Exception $e){
					// do nothing
				}

				// Handle WP Error
				if( is_wp_error($response) ) {
					$wp_error_message = 'Error Code : '.$response->get_error_code().'<br/>Error Message : '. $response->get_error_message();
				}

				if( ! empty($wp_error_message) ) {
					return $array;
				}
			}

			if ( $this->debug ) {
				if( ! empty($cached_response) )	$this->debug_log( "--------------------UPS Access Point Details----------------", "Using Cached Response" );
				$this->debug_log( "--------------------UPS Access Point Request----------------",htmlspecialchars( $xmlRequest ) );
				$this->debug_log( "--------------------UPS Access Point Response----------------", ! empty($wp_error_message) ? $wp_error_message : htmlspecialchars($response['body']));
			}
			
			$locators = array();
			$full_address = array();
			$drop_locations = array();
			
			libxml_use_internal_errors(TRUE);
			$xml = simplexml_load_string( '<root>' . preg_replace('/<\?xml.*\?>/','', $response['body'] ) . '</root>' );
			if(isset($xml->LocatorResponse->SearchResults->DropLocation)){
				$drop_locations = ($xml->LocatorResponse->SearchResults->DropLocation);
				if( empty($cached_response) )	set_transient( $transient, $response, 7200 );
			}
		
			if(!empty($drop_locations)){
				foreach($drop_locations as $locator_id => $drop_location){
					$locator_id				= (string)$drop_location->LocationID;
					$locator_consignee_name	=	substr( (string)$drop_location->AddressKeyFormat->ConsigneeName .', '. (string)$drop_location->AddressKeyFormat->AddressLine .', '. (string)$drop_location->AddressKeyFormat->PoliticalDivision2.', '. (string)$drop_location->AddressKeyFormat->PostcodePrimaryLow , 0, 70 );

					$drop_location_data							=	new stdClass();
					$drop_location_data->LocationID				=	$drop_location->LocationID;
					$drop_location_data->AddressKeyFormat		=	$drop_location->AddressKeyFormat;
					$drop_location_data->AccessPointInformation	=	$drop_location->AccessPointInformation;
					$locator_full_address[$locator_consignee_name] = json_encode($drop_location_data);
					$locators[$locator_id] = $locator_consignee_name;

					$all_locators[$locator_id]					= json_encode($drop_location_data);
				}
				// Saving all the accesspoint locators in session
				WC()->session->set('ph_ups_access_point_details', $all_locators );
			}
			
			$locator='<select id="shipping_accesspoint" name="shipping_accesspoint" class="select" data-select2="1">';
			$locator .=	"<option value=''>". __('Select UPS Access Point® Location', 'ups-woocommerce-shipping') ."</option>";
			
			if(!empty($locators)){
				foreach ($locators as $locator_id => $access_point_locator){
					
					$updated_accesspoint = $this->wf_get_accesspoint_datas();
					$decoded_selected_accesspoint = (isset($updated_accesspoint) && is_string($updated_accesspoint) ) ? json_decode($updated_accesspoint) : '';
					$consignee_name = ( isset($decoded_selected_accesspoint->AddressKeyFormat->ConsigneeName) ) ? $decoded_selected_accesspoint->AddressKeyFormat->ConsigneeName : '';
					$address_line 	= ( isset($decoded_selected_accesspoint->AddressKeyFormat->AddressLine) )   ? $decoded_selected_accesspoint->AddressKeyFormat->AddressLine : '';
					$political_division_2 = ( isset($decoded_selected_accesspoint->AddressKeyFormat->PoliticalDivision2) )   ? $decoded_selected_accesspoint->AddressKeyFormat->PoliticalDivision2 : '';
					$post_code = ( isset($decoded_selected_accesspoint->AddressKeyFormat->PostcodePrimaryLow) )   ? $decoded_selected_accesspoint->AddressKeyFormat->PostcodePrimaryLow : '';
					
					$selected_accesspoint_locator	=	substr( $consignee_name .', '. $address_line .', '. $political_division_2 .', '. $post_code , 0, 70 );
					
					$selected_accesspoint_locator	= isset($decoded_selected_accesspoint->LocationID) ? $decoded_selected_accesspoint->LocationID : '';
					if( !empty($selected_accesspoint_locator) && in_array($locator_id, $selected_accesspoint_locator )){
						$locator .= "<option selected='selected' value='" . $locator_id . "'>" .$access_point_locator ."</option>";
					}
					else{
						$locator .= "<option value='" . $locator_id . "'>" .$access_point_locator ."</option>";
					}
					
				}
			}
			else{
				$this->wf_update_accesspoint_datas();
			}
			$locator .=	'</select>';
			$array['#shipping_accesspoint'] = $locator;
			return $array;
		}

		/**
		 * Debug logs.
		 */
		public function debug_log( $heading, $data, $source ="PluginHive-UPS-Error-Debug-Log" ){
			if( empty($this->wc_logger))	$this->wc_logger = wc_get_logger();
			$logger_context = array( 'source' => $source );
			$this->wc_logger->debug( $heading.PHP_EOL.print_r( $data, true), $logger_context );
		}
		
		private function wf_update_accesspoint_datas( $value='' ){
			WC()->session->set('ph_ups_selected_access_point_details', $value);
		}
					

		public function wf_ups_update_accesspoint($updated_data){
			$this->wf_update_accesspoint_datas();
			$updated_fields = explode("&",$updated_data);
			if(is_array($updated_fields)){
				foreach($updated_fields as $updated_field){
					$updated_field_values = explode('=',$updated_field);
					if(is_array($updated_field_values)){
						if(in_array('shipping_accesspoint',$updated_field_values)){
							$this->wf_update_accesspoint_datas( urldecode($updated_field_values[1] ) );
						}
					}
				}
			}
			WC()->cart->calculate_shipping();
		}

		// Save Access Point Details in Metakeys Seperately
		public function ph_add_access_point_meta_data($order_id, $post){

			$updated_accesspoint = $this->wf_get_accesspoint_datas();

			$accesspoint_locators = WC()->session->get('ph_ups_access_point_details');

			$selected_accesspoint_locator = '';
			if( !empty( $accesspoint_locators ) )
			foreach( $accesspoint_locators as $locator_id => $locator ){
				if( $locator_id == $updated_accesspoint ){
					$selected_accesspoint_locator = $locator;
					break;
				}
			}

			$decoded_selected_accesspoint = json_decode($selected_accesspoint_locator);


			$decoded_selected_accesspoint = (isset($updated_accesspoint) && is_string($updated_accesspoint) ) ? json_decode($updated_accesspoint) : '';

			$accesspoint_name = ( isset($decoded_selected_accesspoint->AddressKeyFormat->ConsigneeName) ) ? $decoded_selected_accesspoint->AddressKeyFormat->ConsigneeName : '';
			$accesspoint_address 	= ( isset($decoded_selected_accesspoint->AddressKeyFormat->AddressLine) )   ? $decoded_selected_accesspoint->AddressKeyFormat->AddressLine : '';
			$accesspoint_city = ( isset($decoded_selected_accesspoint->AddressKeyFormat->PoliticalDivision2) )   ? $decoded_selected_accesspoint->AddressKeyFormat->PoliticalDivision2 : '';
			$accesspoint_state = ( isset($decoded_selected_accesspoint->AddressKeyFormat->PoliticalDivision1) )   ? $decoded_selected_accesspoint->AddressKeyFormat->PoliticalDivision1 : '';
			$accesspoint_country = ( isset($decoded_selected_accesspoint->AddressKeyFormat->CountryCode) )   ? $decoded_selected_accesspoint->AddressKeyFormat->CountryCode : '';
			$accesspoint_postcode = ( isset($decoded_selected_accesspoint->AddressKeyFormat->PostcodePrimaryLow) )   ? $decoded_selected_accesspoint->AddressKeyFormat->PostcodePrimaryLow : '';

			update_post_meta( $order_id, '_ph_accesspoint_name', $accesspoint_name );
			update_post_meta( $order_id, '_ph_accesspoint_address', $accesspoint_address );
			update_post_meta( $order_id, '_ph_accesspoint_city', $accesspoint_city );
			update_post_meta( $order_id, '_ph_accesspoint_statecode', $accesspoint_state );
			update_post_meta( $order_id, '_ph_accesspoint_countrycode', $accesspoint_country );
			update_post_meta( $order_id, '_ph_accesspoint_postcode', $accesspoint_postcode );

			// Saving accesspoint locators in meta
			update_post_meta( $order_id, '_ph_accesspoint_location', $selected_accesspoint_locator );

		}
	}
	
	new wf_ups_accesspoint_locator();
}