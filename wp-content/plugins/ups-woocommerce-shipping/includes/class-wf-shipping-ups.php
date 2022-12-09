<?php

include('class-wf-freight-ups.php');
include('class-ph-ups-help-and-support.php');

/**
 * WF_Shipping_UPS class.
 *
 * @extends WC_Shipping_Method
 */
class WF_Shipping_UPS extends WC_Shipping_Method {
	public $mode='volume_based';
	private $endpoint = 'https://wwwcie.ups.com/ups.app/xml/Rate';
	private $freight_endpoint = 'https://wwwcie.ups.com/rest/FreightRate';

	/**
	 * For Delivery Confirmation below array of countries will be considered as domestic, Confirmed by UPS.
	 * US to US, CA to CA, PR to PR are considered as domestic, all other shipments are international.
	 * @var array 
	 */
	public $dc_domestic_countries = array( 'US', 'CA', 'PR');
	
	private $pickup_code = array(
		'01' => "Daily Pickup",
		'03' => "Customer Counter",
		'06' => "One Time Pickup",
		'07' => "On Call Air",
		'19' => "Letter Center",
		'20' => "Air Service Center",
	);
	
	private $customer_classification_code = array(
		'NA' => "Default",
		'00' => "Rates Associated with Shipper Number",
		'01' => "Daily Rates",
		'04' => "Retail Rates",
		'05' => "Regional Rates",
		'06' => "General List Rates",
		'53' => "Standard List Rates",
	);

	private $services = array(
		// Domestic
		"12" => "UPS 3 Day Select®",
		"03" => "UPS® Ground",
		"02" => "UPS 2nd Day Air®",
		"59" => "UPS 2nd Day Air A.M.®",
		"01" => "UPS Next Day Air®",
		"13" => "UPS Next Day Air Saver®",
		"14" => "UPS Next Day Air® Early",
		"74" => "UPS Express® 12:00"	,		// Germany Domestic

		// International
		"11" => "UPS® Standard",
		"07" => "UPS Worldwide Express™",
		"54" => "UPS Worldwide Express Plus™",
		"08" => "UPS Worldwide Expedited",
		"65" => "UPS Saver",
		
		// SurePost
		"92" =>	"UPS SurePost® (USPS) < 1lb",
		"93" =>	"UPS SurePost® (USPS) > 1lb",
		"94" =>	"UPS SurePost® (USPS) BPM",
		"95" =>	"UPS SurePost® (USPS) Media",
		
		//New Services
		"M2" => "UPS First Class Mail",
		"M3" => "UPS Priority Mail",
		"M4" => "UPS Expedited Mail Innovations ",
		"M5" => "UPS Priority Mail Innovations ",
		"M6" => "UPS EconomyMail Innovations ",
		"70" => "UPS Access Point® Economy ",
		"96" => "UPS Worldwide Express Freight",
		
		"US48" => "Ground with Freight",
		
	);
	private $freight_services=array(
											'308'=>'TForce Freight LTL',
		                                    '309'=>'TForce Freight LTL - Guaranteed',
		                                    '334'=>'TForce Freight LTL - Guaranteed A.M.',
		                                    '349'=>'TForce Freight LTL Mexico',
											);
	
	public $freight_package_type_code_list=array(
											"BAG"=>"Bag",
											"BAL"=>"Bale",
											"BAR"=>"Barrel",
											"BDL"=>"Bundle",
											"BIN"=>"Bin",
											"BOX"=>"Box",
											"BSK"=>"Basket",
											"BUN"=>"Bunch",
											"CAB"=>"Cabinet",
											"CAN"=>"Can",
											"CAR"=>"Carrier",
											"CAS"=>"Case",
											"CBY"=>"Carboy",
											"CON"=>"Container",
											"CRT"=>"Crate",
											"CSK"=>"Cask",
											"CTN"=>"Carton",
											"CYL"=>"Cylinder",
											"DRM"=>"Drum",
											"LOO"=>"Loose",
											"OTH"=>"Other",
											"PAL"=>"Pail",
											"PCS"=>"Pieces",
											"PKG"=>"Package",
											"PLN"=>"Pipe Line",
											"PLT"=>"Pallet",
											"RCK"=>"Rack",
											"REL"=>"Reel",
											"ROL"=>"Roll",
											"SKD"=>"Skid",
											"SPL"=>"Spool",
											"TBE"=>"Tube",
											"TNK"=>"Tank",
											"UNT"=>"Unit",
											"VPK"=>"Van Pack",
											"WRP"=>"Wrapped",
											 );
	public  $freight_package_type_code='PLT';
	public 	$freight_shippernumber='';
	public 	$freight_billing_option_code='10';
	public 	$freight_billing_option_code_list=array('10'=>'Prepaid','30'=>'Bill to Third Party','40'=>'Freight Collect');
	public 	$freight_handling_unit_one_type_code='PLT';
	
	private $ups_surepost_services = array(92, 93, 94, 95);

	private $eu_array = array('BE','BG','CZ','DK','DE','EE','IE','GR','ES','FR','HR','IT','CY','LV','LT','LU','HU','MT','NL','AT','PT','RO','SI','SK','FI','PL','SE');

	private $cod_currency_specific_contries = array(
		'BE' => 'EUR',
		'BG' => 'EUR',
		'CZ' => 'EUR',
		'DK' => 'EUR',
		'DE' => 'EUR',
		'EE' => 'EUR',
		'IE' => 'EUR',
		'GR' => 'EUR',
		'ES' => 'EUR',
		'FR' => 'EUR',
		'HR' => 'EUR',
		'IT' => 'EUR',
		'CY' => 'EUR',
		'LV' => 'EUR',
		'LT' => 'EUR',
		'LU' => 'EUR',
		'HU' => 'EUR',
		'MT' => 'EUR',
		'NL' => 'EUR',
		'AT' => 'EUR',
		'PT' => 'EUR',
		'RO' => 'EUR',
		'SI' => 'EUR',
		'SK' => 'EUR',
		'FI' => 'EUR',
		'GB' => 'EUR',
		'PL' => 'EUR',
		'SE' => 'EUR',
	);
	
	private $no_postcode_country_array = array('AE','AF','AG','AI','AL','AN','AO','AW','BB','BF','BH','BI','BJ','BM','BO','BS','BT','BW','BZ','CD','CF','CG','CI','CK','CL','CM','CR','CV','DJ','DM','DO','EC','EG','ER','ET','FJ','FK','GA','GD','GH','GI','GM','GN','GQ','GT','GW','GY','HK','HN','HT','IE','IQ','IR','JM','JO','KE','KH','KI','KM','KN','KP','KW','KY','LA','LB','LC','LR','LS','LY','ML','MM','MO','MR','MS','MT','MU','MW','MZ','NA','NE','NG','NI','NP','NR','NU','OM','PA','PE','PF','PY','QA','RW','SB','SC','SD','SL','SN','SO','SR','SS','ST','SV','SY','TC','TD','TG','TL','TO','TT','TV','TZ','UG','VC','VE','VG','VU','WS','XA','XB','XC','XE','XL','XM','XN','XS','YE','ZM','ZW');

	
	// Shipments Originating in the European Union
	private $euservices = array(
		"07" => "UPS Express",
		"08" => "UPS Expedited",
		"11" => "UPS® Standard",
		"54" => "UPS Worldwide Express Plus™",
		"65" => "UPS Worldwide Express Saver®",
		"70" => "UPS Access Point® Economy",
		"74" => "UPS Express® 12:00",
	);

	private $ukservices = array(
		"07" => "UPS Express",
		"08" => "UPS Expedited",
		"11" => "UPS® Standard",
		"54" => "UPS Worldwide Express Plus™",
		"65" => "UPS Worldwide Express Saver®",
		"70" => "UPS Access Point® Economy",
	);

	private $polandservices = array(
		"07" => "UPS Express",
		"08" => "UPS Expedited",
		"11" => "UPS® Standard",
		"54" => "UPS Express Plus®",
		"65" => "UPS Express Saver",
		"82" => "UPS Today Standard",
		"83" => "UPS Today Dedicated Courier",
		"85" => "UPS Today Express",
		"86" => "UPS Today Express Saver",
		"70" => "UPS Access Point® Economy",
	);

	// Services for Canada Origination
	private $canadaservices = array(
		"01" =>	"UPS Express",
		"02" => "UPS Expedited",
		"07" =>	"UPS Worldwide Express™",
		"08" =>	"UPS Worldwide Expedited®",
		"11" =>	"UPS® Standard",
		"12" => "UPS 3 Day Select®",				// For CA and US48
		"13" => "UPS Express Saver",
		"14" =>	"UPS Express Early",
		"54" => "UPS Worldwide Express Plus™",	//UPS Express Early for CA and US48
		"65" => "UPS Express Saver",
		"70" =>	"UPS Access Point® Economy",
	);

	// Packaging not offered at this time: 00 = UNKNOWN, 30 = Pallet, 04 = Pak
	// Code 21 = Express box is valid code, but doesn't have dimensions
	// References:
	// http://www.ups.com/content/us/en/resources/ship/packaging/supplies/envelopes.html
	// http://www.ups.com/content/us/en/resources/ship/packaging/supplies/paks.html
	// http://www.ups.com/content/us/en/resources/ship/packaging/supplies/boxes.html
	private $packaging = array(
		"01" => array(
					"name" 	 => "UPS Letter",
					"length" => "12.5",
					"width"  => "9.5",
					"height" => "0.25",
					"weight" => "0.5"
				),
		"03" => array(
					"name" 	 => "Tube",
					"length" => "38",
					"width"  => "6",
					"height" => "6",
					"weight" => "100"
				),
		"04" => array(
					"name" 	 => "PAK",
					"length" => "17",
					"width"  => "13",
					"height" => "1",
					"weight" => "100"
				),
		"24" => array(
					"name" 	 => "25KG Box",
					"length" => "19.375",
					"width"  => "17.375",
					"height" => "14",
					"weight" => "25"
				),
		"25" => array(
					"name" 	 => "10KG Box",
					"length" => "16.5",
					"width"  => "13.25",
					"height" => "10.75",
					"weight" => "10"
				),
		"2a" => array(
					"name" 	 => "Small Express Box",
					"length" => "13",
					"width"  => "11",
					"height" => "2",
					"weight" => "100"
				),
		"2b" => array(
					"name" 	 => "Medium Express Box",
					"length" => "15",
					"width"  => "11",
					"height" => "3",
					"weight" => "100"
				),
		"2c" => array(
					"name" 	 => "Large Express Box",
					"length" => "18",
					"width"  => "13",
					"height" => "3",
					"weight" => "30"
				)
	);

	private $packaging_select = array(
		"01" => "UPS Letter",
		"03" => "Tube",
		"04" => "PAK",
		"24" => "25KG Box",
		"25" => "10KG Box",
		"2a" => "Small Express Box",
		"2b" => "Medium Express Box",
		"2c" => "Large Express Box",
	);

	private $accesspoint_location_option = array(
		"000" => "All Location Types",
		"001" => "UPS Customer Center",
		"002" => "The UPS Store",
		"003" => "UPS Drop Box",
		"004" => "Authorized Shipping Outlet",
		"005" => "Mail Boxes Etc.",
		"007" => "UPS Alliance",
		"014" => "UPS Authorized Service Providers",
		"018" => "UPS Access Point",
		"019" => "UPS Access Point Locker",
	);

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $order=null ) {
		if( $order ){
			$this->order = $order;
		}

		$this->id				 = WF_UPS_ID;
		$this->method_title	   = __( 'UPS', 'ups-woocommerce-shipping' );
		$this->method_description = __( 'WooCommerce UPS Shipping Plugin with Print Label by PluginHive', 'ups-woocommerce-shipping' );
		
		// WF: Load UPS Settings.
		$ups_settings 			= get_option( 'woocommerce_'.WF_UPS_ID.'_settings', null );
		$this->wc_weight_unit 	= get_option( 'woocommerce_weight_unit' );
		$ups_settings			= apply_filters('wf_ups_shipment_settings', $ups_settings, $order);

		$api_mode	  		= isset( $ups_settings['api_mode'] ) ? $ups_settings['api_mode'] : 'Test';
		if( "Live" == $api_mode ) {
			$this->endpoint = 'https://onlinetools.ups.com/ups.app/xml/Rate';
			$this->freight_endpoint='https://onlinetools.ups.com/rest/FreightRate';
		}
		else {
			$this->endpoint = 'https://wwwcie.ups.com/ups.app/xml/Rate';
			$this->freight_endpoint='https://wwwcie.ups.com/rest/FreightRate';
		}
		
		$this->init();
		// Add Estimated delivery to cart rates
		if( $this->show_est_delivery ) {
			add_filter( 'woocommerce_cart_shipping_method_full_label', array($this, 'wf_add_delivery_time'), 10, 2 );
		}
	}

	public function wf_add_delivery_time( $label, $method ) {

		global $wp_version;

		//Older versoin of WC is not supporting get_meta_data() on method.
		if( !is_object($method) || !method_exists($method,'get_meta_data') ){
			return $label;
		}

		if( empty($this->wp_date_time_format) ) {
			$this->wp_date_time_format = Ph_UPS_Woo_Shipping_Common::get_wordpress_date_format().' '.Ph_UPS_Woo_Shipping_Common::get_wordpress_time_format();
		}

		$shipping_rate_meta_data_arr 	= $method->get_meta_data();
		$est_delivery_text = ! empty( $this->estimated_delivery_text) ? $this->estimated_delivery_text : __( 'Est delivery', 'ups-woocommerce-shipping' );
		if( !empty($shipping_rate_meta_data_arr['ups_delivery_time']) && strpos( $label, $est_delivery_text ) === false  ){
			
			$est_delivery 	= $shipping_rate_meta_data_arr['ups_delivery_time'];
			$formatted_date = date_format($est_delivery, $this->wp_date_time_format );

			if ( version_compare( $wp_version, '5.3', '>=' ) ) {

				if (date_default_timezone_get()) {

					$zone 		= new DateTimeZone(date_default_timezone_get());
				}else{
					
					$zone 		= new DateTimeZone('UTC');
				}

				if( strtotime($formatted_date) ) {
					$formatted_date = wp_date( $this->wp_date_time_format, strtotime($formatted_date), $zone );
				}
			}else{
				if( strtotime($formatted_date) ) {
					$formatted_date = date_i18n( $this->wp_date_time_format, strtotime($formatted_date) );
				}
			}

			if( ! empty($this->estimated_delivery_text) )
				$est_delivery_html 	= "<br /><small>".$this->estimated_delivery_text. $formatted_date.'</small>';
			else
				$est_delivery_html 	= "<br /><small>".__('Est delivery: ', 'ups-woocommerce-shipping'). $formatted_date.'</small>';
			$est_delivery_html = apply_filters( 'wf_ups_estimated_delivery', $est_delivery_html, $est_delivery, $method );
			// Avoid multiple
			if( strstr( $label, $formatted_date ) === false )
				$label .= $est_delivery_html;
		}
		return $label;
	}

	/**
	 * Output a message or error
	 * @param  string $message
	 * @param  string $type
	 */
	public function debug( $message, $type = 'notice' ) {
		// Hard coding to 'notice' as recently noticed 'error' is breaking with wc_add_notice.
		$type = 'notice';
		if ( $this->debug && !is_admin() ) { //WF: do not call wc_add_notice from admin.
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
				wc_add_notice( $message, $type );
			} else {
				global $woocommerce;
				$woocommerce->add_message( $message );
			}
		}
	}

	public function diagnostic_report( $data ) {
	
		if( function_exists("wc_get_logger") ) {

			$log = wc_get_logger();
			$log->debug( ($data).PHP_EOL.PHP_EOL, array('source' => 'PluginHive-UPS-Error-Debug-Log'));
		}
	}

	private function is_soap_available() {
		
		if( extension_loaded( 'soap' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	private function init() {
		global $woocommerce;
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		$this->settings	=	apply_filters('wf_ups_shipment_settings', $this->settings, '');
		$this->isc=isset( $this->settings['international_special_commodities'] ) && !empty($this->settings['international_special_commodities']) && $this->settings['international_special_commodities'] == 'yes' ? true : false;


		$this->soap_available 		= $this->is_soap_available() ? true : false;

		// Define user set variables
		$this->mode 				= isset( $this->settings['packing_algorithm'] ) ? $this->settings['packing_algorithm'] : 'volume_based';
		$this->exclude_box_weight 	= ( isset( $this->settings['exclude_box_weight'] ) && $this->settings['exclude_box_weight'] == 'yes' ) ? true : false;
		$this->stack_to_volume 		= ( isset( $this->settings['stack_to_volume'] ) && $this->settings['stack_to_volume'] == 'yes' ) ? true : false;
		$this->enabled				= isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : $this->enabled;
		$this->title				= isset( $this->settings['title'] ) ? $this->settings['title'] : $this->method_title;
		$this->cheapest_rate_title	= isset( $this->settings['title'] ) ? $this->settings['title'] : null;
		$this->availability			= isset( $this->settings['availability'] ) ? $this->settings['availability'] : 'all';
		$this->countries	   		= isset( $this->settings['countries'] ) ? $this->settings['countries'] : array();

		// API Settings
		$this->user_id		 		= isset( $this->settings['user_id'] ) ? $this->settings['user_id'] : '';

		// WF: Print Label - Start
		$this->disble_ups_print_label	= isset( $this->settings['disble_ups_print_label'] ) ? $this->settings['disble_ups_print_label'] : '';
		$this->print_label_type	  	= isset( $this->settings['print_label_type'] ) ? $this->settings['print_label_type'] : 'gif';
		$this->show_label_in_browser	= isset( $this->settings['show_label_in_browser'] ) ? $this->settings['show_label_in_browser'] : 'no';
		$this->ship_from_address	  	= isset( $this->settings['ship_from_address'] ) ? $this->settings['ship_from_address'] : 'origin_address';
		$this->disble_shipment_tracking	= isset( $this->settings['disble_shipment_tracking'] ) ? $this->settings['disble_shipment_tracking'] : 'TrueForCustomer';
		$this->api_mode	  			= isset( $this->settings['api_mode'] ) ? $this->settings['api_mode'] : 'Test';
		$this->ups_user_name			= isset( $this->settings['ups_user_name'] ) ? $this->settings['ups_user_name'] : '';
		$this->ups_display_name			= isset( $this->settings['ups_display_name'] ) ? $this->settings['ups_display_name'] : '';
		$this->phone_number 			= isset( $this->settings['phone_number'] ) ? $this->settings['phone_number'] : '';
		// WF: Print Label - End

		$this->user_id		 		= isset( $this->settings['user_id'] ) ? $this->settings['user_id'] : '';
		$this->password				= isset( $this->settings['password'] ) ? $this->settings['password'] : '';
		$this->access_key	  		= isset( $this->settings['access_key'] ) ? $this->settings['access_key'] : '';
		$this->shipper_number  		= isset( $this->settings['shipper_number'] ) ? $this->settings['shipper_number'] : '';
		$this->negotiated	  		= isset( $this->settings['negotiated'] ) && $this->settings['negotiated'] == 'yes' ? true : false;
		$this->tax_indicator	  	= isset( $this->settings['tax_indicator'] ) && $this->settings['tax_indicator'] == 'yes' ? true : false;
		$this->origin_addressline 	= isset( $this->settings['origin_addressline'] ) ? $this->settings['origin_addressline'] : '';
		$this->origin_addressline_2 = isset( $this->settings['origin_addressline_2'] ) ? $this->settings['origin_addressline_2'] : '';
		$this->origin_city 			= isset( $this->settings['origin_city'] ) ? $this->settings['origin_city'] : '';
		$this->origin_postcode 		= isset( $this->settings['origin_postcode'] ) ? $this->settings['origin_postcode'] : '';
		$this->origin_country_state = isset( $this->settings['origin_country_state'] ) ? $this->settings['origin_country_state'] : '';
		$this->debug	  			= isset( $this->settings['debug'] ) && $this->settings['debug'] == 'yes' ? true : false;

		// Estimated delivery : Start
		$this->show_est_delivery		= ( isset($this->settings['enable_estimated_delivery']) && $this->settings['enable_estimated_delivery'] == 'yes' ) ? true : false;
		$this->estimated_delivery_text	= ! empty($this->settings['estimated_delivery_text']) ? $this->settings['estimated_delivery_text'] : null;
		$this->show_est_delivery_in_woocommerce_blocks = !empty( $this->settings['est_delivery_for_woocommerce_blocks'] ) && isset( $this->settings['est_delivery_for_woocommerce_blocks']) ? $this->settings['est_delivery_for_woocommerce_blocks'] : '';
		if( $this->show_est_delivery ) {
			if( empty($this->current_wp_time) ) {
				$current_time 			= current_time('Y-m-d H:i:s');
				$this->current_wp_time 	= date_create($current_time);
			}
			if( empty($this->wp_date_time_format) ) {
				$this->wp_date_time_format = Ph_UPS_Woo_Shipping_Common::get_wordpress_date_format().' '.Ph_UPS_Woo_Shipping_Common::get_wordpress_time_format();
			}
		}
		// Estimated delivery : End

		$this->rate_caching 	= ( isset($this->settings['ups_rate_caching']) && !empty($this->settings['ups_rate_caching']) ) ? $this->settings['ups_rate_caching'] : '24';

		// Pickup and Destination
		$this->pickup			= isset( $this->settings['pickup'] ) ? $this->settings['pickup'] : '01';
		$this->customer_classification = isset( $this->settings['customer_classification'] ) ? $this->settings['customer_classification'] : '99';
		$this->residential		= isset( $this->settings['residential'] ) && $this->settings['residential'] == 'yes' ? true : false;

		// Services and Packaging
		$this->offer_rates	 	= isset( $this->settings['offer_rates'] ) ? $this->settings['offer_rates'] : 'all';
		$this->fallback		   	= ! empty( $this->settings['fallback'] ) ? $this->settings['fallback'] : '';
		$this->currency_type	= ! empty( $this->settings['currency_type'] ) ? $this->settings['currency_type'] : get_woocommerce_currency();
		$this->conversion_rate	= ! empty( $this->settings['conversion_rate'] ) ? $this->settings['conversion_rate'] : 1;
		$this->packing_method  	= isset( $this->settings['packing_method'] ) ? $this->settings['packing_method'] : 'per_item';
		$this->ups_packaging	= isset( $this->settings['ups_packaging'] ) ? $this->settings['ups_packaging'] : array();
		$this->custom_services  = isset( $this->settings['services'] ) ? $this->settings['services'] : array();
		$this->boxes		   	= isset( $this->settings['boxes'] ) ? $this->settings['boxes'] : array();
		$this->insuredvalue 	= isset( $this->settings['insuredvalue'] ) && $this->settings['insuredvalue'] == 'yes' ? true : false;
		$this->min_order_amount_for_insurance = ! empty($this->settings['min_order_amount_for_insurance']) ? $this->settings['min_order_amount_for_insurance'] : 0;
		$this->enable_freight 	= isset( $this->settings['enable_freight'] ) && $this->settings['enable_freight'] == 'yes' ? true : false;

		$this->enable_density_based_rating = ( isset( $this->settings['enable_density_based_rating'] ) && $this->settings['enable_density_based_rating'] == 'yes') ? true : false;
		$this->density_length 	= ( isset( $this->settings['density_length'] ) && !empty( $this->settings['density_length'] ) ) ? $this->settings['density_length'] : 0;
		$this->density_width 	= ( isset( $this->settings['density_width'] ) && !empty( $this->settings['density_width'] ) ) ? $this->settings['density_width'] : 0;
		$this->density_height 	= ( isset( $this->settings['density_height'] ) && !empty( $this->settings['density_height'] ) ) ? $this->settings['density_height'] : 0;

		$this->freight_class	= ! empty($this->settings['freight_class']) ? $this->settings['freight_class'] : 50;

		$this->box_max_weight			=	$this->get_option( 'box_max_weight' );
		$this->weight_packing_process	=	$this->get_option( 'weight_packing_process' );
		$this->service_code 	= '';
		$this->min_amount	   = isset( $this->settings['min_amount'] ) ? $this->settings['min_amount'] : 0;
		// $this->ground_freight 	= isset( $this->settings['ground_freight'] ) && $this->settings['ground_freight'] == 'yes' ? true : false;
		
		// Units
		$this->units			= isset( $this->settings['units'] ) ? $this->settings['units'] : 'imperial';

		if ( $this->units == 'metric' ) {
			$this->weight_unit = 'KGS';
			$this->dim_unit	= 'CM';
		} else {
			$this->weight_unit = 'LBS';
			$this->dim_unit	= 'IN';
		}
		
		$this->uom = ($this->units == 'imperial')?'LB':'KG';

		//Advanced Settings
		$this->ssl_verify			= isset( $this->settings['ssl_verify'] ) ? $this->settings['ssl_verify'] : false;
		$this->accesspoint_locator 	= (isset($this->settings[ 'accesspoint_locator']) && $this->settings[ 'accesspoint_locator']=='yes') ? true : false;
		$this->address_validation	= (isset($this->settings[ 'address_validation']) && $this->settings[ 'address_validation']=='yes') ? true : false;

		$this->xa_show_all		= isset( $this->settings['xa_show_all'] ) && $this->settings['xa_show_all'] == 'yes' ? true : false;


		if (strstr($this->origin_country_state, ':')) :
			// WF: Following strict php standards.
			$origin_country_state_array = explode(':',$this->origin_country_state);
			$this->origin_country = current($origin_country_state_array);
			$origin_country_state_array = explode(':',$this->origin_country_state);
			$this->origin_state   = end($origin_country_state_array);
		else :
			$this->origin_country = $this->origin_country_state;
			$this->origin_state   = '';
		endif;
		$this->origin_custom_state   = (isset( $this->settings['origin_custom_state'] )&& !empty($this->settings['origin_custom_state'])) ? $this->settings['origin_custom_state'] : $this->origin_state;
		
		// COD selected
		$this->cod 					= false;
		$this->cod_total 			= 0;
		$this->cod_enable 			= (isset($this->settings['cod_enable']) && !empty($this->settings['cod_enable']) && $this->settings['cod_enable'] == 'yes') ? true : false;
		$this->eu_country_cod_type 	= isset($this->settings['eu_country_cod_type']) && !empty($this->settings['eu_country_cod_type']) ? $this->settings['eu_country_cod_type'] : 9;

		// Show the services depending on origin address
		if ( $this->origin_country == 'PL' ) {
			$this->services = $this->polandservices;
		}elseif( $this->origin_country == 'CA' ) {
			$this->services = $this->canadaservices;
		}elseif( $this->origin_country == 'GB' || $this->origin_country == 'UK' ) {
			$this->services = $this->ukservices;
		}elseif ( in_array( $this->origin_country, $this->eu_array ) ) {
			$this->services = $this->euservices;
		}
		
		// Different Ship From Address
		$this->ship_from_address_different_from_shipper = ! empty($this->settings['ship_from_address_different_from_shipper']) ? $this->settings['ship_from_address_different_from_shipper'] : 'no';
		$this->ship_from_addressline	= ! empty($this->settings['ship_from_addressline']) ? $this->settings['ship_from_addressline'] : null;
		$this->ship_from_addressline_2	= isset($this->settings['ship_from_addressline_2']) ? $this->settings['ship_from_addressline_2'] : null;
		$this->ship_from_city			= ! empty($this->settings['ship_from_city']) ? $this->settings['ship_from_city'] : null;
		$this->ship_from_postcode 		= ! empty($this->settings['ship_from_postcode']) ? $this->settings['ship_from_postcode'] : null;
		$this->ship_from_country_state	= ! empty($this->settings['ship_from_country_state']) ? $this->settings['ship_from_country_state'] : null;

		if( empty($this->ship_from_country_state) ){
			$this->ship_from_country = $this->origin_country_state;		// By Default Origin Country
			$this->ship_from_state   = $this->origin_state;				// By Default Origin State
		}
		else {
			if (strstr($this->ship_from_country_state, ':')) :
				list( $this->ship_from_country, $this->ship_from_state ) = explode(':',$this->ship_from_country_state);
			else :
				$this->ship_from_country = $this->ship_from_country_state;
				$this->ship_from_state   = '';
			endif;
		}

		$this->ship_from_custom_state   = ! empty($this->settings['ship_from_custom_state']) ? $this->settings['ship_from_custom_state'] : $this->ship_from_state;

		$this->freight_weekend_pickup 			= ( isset( $this->settings['freight_weekend_pickup'] ) && $this->settings['freight_weekend_pickup'] == 'yes' ) ? true : false;

		// Third Party Freight Billing
		$this->freight_payment_information 		= isset( $this->settings['freight_payment'] ) && !empty( $this->settings['freight_payment'] ) ? $this->settings['freight_payment'] : '10';
		$this->freight_thirdparty_contact_name	= isset( $this->settings['freight_thirdparty_contact_name'] ) && !empty($this->settings['freight_thirdparty_contact_name']) ? $this->settings['freight_thirdparty_contact_name'] : ' ';
		$this->freight_thirdparty_addressline 	= isset( $this->settings['freight_thirdparty_addressline'] ) && !empty($this->settings['freight_thirdparty_addressline']) ? $this->settings['freight_thirdparty_addressline'] : ' ';
		$this->freight_thirdparty_addressline_2 = isset( $this->settings['freight_thirdparty_addressline_2'] ) && !empty($this->settings['freight_thirdparty_addressline_2']) ? $this->settings['freight_thirdparty_addressline_2'] : ' ';
		$this->freight_thirdparty_city 			= isset( $this->settings['freight_thirdparty_city'] ) && !empty($this->settings['freight_thirdparty_city']) ? $this->settings['freight_thirdparty_city'] : ' ';
		$this->freight_thirdparty_postcode 		= isset( $this->settings['freight_thirdparty_postcode'] ) && !empty($this->settings['freight_thirdparty_postcode']) ? $this->settings['freight_thirdparty_postcode'] : ' ';
		$this->freight_thirdparty_country_state	= isset( $this->settings['freight_thirdparty_country_state'] ) && !empty($this->settings['freight_thirdparty_country_state']) ? $this->settings['freight_thirdparty_country_state'] : ' ';
		$this->ups_tradability 	= ( (isset( $this->settings['ups_tradability'] ) && !empty($this->settings['ups_tradability'] )) && $this->settings['ups_tradability'] == 'yes' ) ? true : false;

		if( empty($this->freight_thirdparty_country_state) ){
			$this->freight_thirdparty_country 	= $this->origin_country_state;		// By Default Origin Country
			$this->freight_thirdparty_state 	= $this->origin_state;				// By Default Origin State
		}
		else {
			if (strstr($this->freight_thirdparty_country_state, ':')) :
				list( $this->freight_thirdparty_country, $this->freight_thirdparty_state ) = explode(':',$this->freight_thirdparty_country_state);
			else :
				$this->freight_thirdparty_country = $this->freight_thirdparty_country_state;
				$this->freight_thirdparty_state   = '';
			endif;
		}

		$this->freight_thirdparty_state 		= isset( $this->settings['freight_thirdparty_custom_state'] ) && !empty($this->settings['freight_thirdparty_custom_state']) ? $this->settings['freight_thirdparty_custom_state'] : $this->freight_thirdparty_state;

		$this->skip_products 	= ! empty($this->settings['skip_products']) ? $this->settings['skip_products'] : array();
		$this->min_weight_limit = ! empty($this->settings['min_weight_limit']) ? (float) $this->settings['min_weight_limit'] : null;
		$this->max_weight_limit	= ! empty($this->settings['max_weight_limit']) ? (float) $this->settings['max_weight_limit'] : null;
		$this->ph_delivery_confirmation = isset( $this->settings['ph_delivery_confirmation'] ) && !empty($this->settings['ph_delivery_confirmation']) ? $this->settings['ph_delivery_confirmation'] : 0;

		if( ! empty($this->conversion_rate) ) {
			$this->rate_conversion		= $this->conversion_rate; // For Returned Rate Conversion to Default Currency 
			$this->conversion_rate		= apply_filters('ph_ups_currency_conversion_rate',$this->conversion_rate,$this->currency_type);   // Multicurrency
		}
		
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );

	}

	/**
	 * environment_check function.
	 *
	 * @access public
	 * @return void
	 */
	private function environment_check() {
		global $woocommerce;

		$error_message = '';

		// WF: Print Label - Start
		// Check for UPS User Name
		if ( ! $this->ups_user_name && $this->enabled == 'yes' ) {
			$error_message .= '<p>' . __( 'UPS is enabled, but Your Name has not been set.', 'ups-woocommerce-shipping' ) . '</p>';
		}
		// WF: Print Label - End
		
		// Check for UPS User ID
		if ( ! $this->user_id && $this->enabled == 'yes' ) {
			$error_message .= '<p>' . __( 'UPS is enabled, but the UPS User ID has not been set.', 'ups-woocommerce-shipping' ) . '</p>';
		}

		// Check for UPS Password
		if ( ! $this->password && $this->enabled == 'yes' ) {
			$error_message .= '<p>' . __( 'UPS is enabled, but the UPS Password has not been set.', 'ups-woocommerce-shipping' ) . '</p>';
		}

		// Check for UPS Access Key
		if ( ! $this->access_key && $this->enabled == 'yes' ) {
			$error_message .= '<p>' . __( 'UPS is enabled, but the UPS Access Key has not been set.', 'ups-woocommerce-shipping' ) . '</p>';
		}

		// Check for UPS Shipper Number
		if ( ! $this->shipper_number && $this->enabled == 'yes' ) {
			$error_message .= '<p>' . __( 'UPS is enabled, but the UPS Shipper Number has not been set.', 'ups-woocommerce-shipping' ) . '</p>';
		}

		// Check for Origin Postcode
		if ( ! $this->origin_postcode && $this->enabled == 'yes' ) {
			$error_message .= '<p>' . __( 'UPS is enabled, but the origin postcode has not been set.', 'ups-woocommerce-shipping' ) . '</p>';
		}

		// Check for Origin country
		if ( ! $this->origin_country_state && $this->enabled == 'yes' ) {
			$error_message .= '<p>' . __( 'UPS is enabled, but the origin country/state has not been set.', 'ups-woocommerce-shipping' ) . '</p>';
		}

		// If user has selected to pack into boxes,
		// Check if at least one UPS packaging is chosen, or a custom box is defined
		if ( ( $this->packing_method == 'box_packing' ) && ( $this->enabled == 'yes' ) ) {
			if ( empty( $this->ups_packaging )  && empty( $this->boxes ) ){
				$error_message .= '<p>' . __( 'UPS is enabled, and Parcel Packing Method is set to \'Pack into boxes\', but no UPS Packaging is selected and there are no custom boxes defined. Items will be packed individually.', 'ups-woocommerce-shipping' ) . '</p>';
			}
		}

		// Check for at least one service enabled
		$ctr=0;
		if ( isset($this->custom_services ) && is_array( $this->custom_services ) ){
			foreach ( $this->custom_services as $key => $values ){
				if ( $values['enabled'] == 1)
					$ctr++;
			}
		}
		if ( ( $ctr == 0 ) && $this->enabled == 'yes' ) {
			$error_message .= '<p>' . __( 'UPS is enabled, but there are no services enabled.', 'ups-woocommerce-shipping' ) . '</p>';
		}


		if ( ! $error_message == '' ) {
			echo '<div class="error">';
			echo $error_message;
			echo '</div>';
		}
	}

	/**
	 * admin_options function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_options() {
		// Check users environment supports this method
		$this->environment_check();

		// Show settings
		parent::admin_options();
	}

	/**
	 *
	 * generate_single_select_country_html function
	 *
	 * @access public
	 * @return void
	 */
	function generate_single_select_country_html() {
		global $woocommerce;

		ob_start();
		?>
		<tr valign="top" class="ph_ups_general_tab">
			<th scope="row" class="titledesc">
				<label for="origin_country"><?php _e( 'Origin Country', 'ups-woocommerce-shipping' ); ?></label>
			</th>
			<td class="forminp"><select name="woocommerce_ups_origin_country_state" id="woocommerce_ups_origin_country_state" style="width: 250px;" data-placeholder="<?php _e('Choose a country&hellip;', 'woocommerce'); ?>" title="Country" class="chosen_select">
				<?php echo $woocommerce->countries->country_dropdown_options( $this->origin_country, $this->origin_state ? $this->origin_state : '*' ); ?>
			</select>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 *
	 * generate_ship_from_country_state_html function
	 *
	 * @access public
	 * @return void
	 */
	function generate_ship_from_country_state_html() {
		global $woocommerce;

		ob_start();
		?>
		<tr valign="top" class="ph_ups_general_tab">
			<th scope="row" class="titledesc">
				<label for="woocommerce_wf_shipping_ups_ship_from_country_state"><?php _e( 'Ship From Country', 'ups-woocommerce-shipping' ); ?></label>
			</th>
			<td class="forminp ph_ups_different_ship_from_address"><select name="woocommerce_wf_shipping_ups_ship_from_country_state" id="woocommerce_wf_shipping_ups_ship_from_country_state" style="width: 250px;" data-placeholder="<?php _e('Choose a country&hellip;', 'woocommerce'); ?>" title="Country" class="chosen_select">
				<?php echo $woocommerce->countries->country_dropdown_options( $this->ship_from_country, $this->ship_from_state ? $this->ship_from_state : '*' ); ?>
			</select>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 *
	 * generate_freight_thirdparty_country_state_html function
	 *
	 * @access public
	 * @return void
	 */
	function generate_freight_thirdparty_country_state_html() {
		global $woocommerce;

		ob_start();
		?>
		<tr valign="top" class="ph_ups_freight_tab">
			<th scope="row" class="titledesc">
				<label for="woocommerce_wf_shipping_ups_freight_thirdparty_country_state"><?php _e( 'Country', 'ups-woocommerce-shipping' ); ?></label>
			</th>
			<td class="forminp ph_ups_freight_third_party_billing"><select name="woocommerce_wf_shipping_ups_freight_thirdparty_country_state" id="woocommerce_wf_shipping_ups_freight_thirdparty_country_state" style="width: 250px;" data-placeholder="<?php _e('Choose a country&hellip;', 'woocommerce'); ?>" title="Country" class="chosen_select">
				<?php echo $woocommerce->countries->country_dropdown_options( $this->freight_thirdparty_country, $this->freight_thirdparty_state ? $this->freight_thirdparty_state : '*' ); ?>
			</select>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * generate_services_html function.
	 *
	 * @access public
	 * @return void
	 */
	function generate_services_html() {
		ob_start();
		?>
		<style>
		/*Style for tooltip*/
		.xa-tooltip { position: relative;  }
		.xa-tooltip .xa-tooltiptext { visibility: hidden; width: 150px; background-color: black; color: #fff; text-align: center; border-radius: 6px; 
			padding: 5px 0;
			/* Position the tooltip */
			position: absolute; z-index: 1;}
		.xa-tooltip:hover .xa-tooltiptext {visibility: visible;}
		/*End of tooltip styling*/
		</style>
		<tr valign="top" id="service_options" class="ph_ups_rates_tab">
			<td class="forminp" colspan="2" style="padding-left:0px">
				<table class="ups_services widefat">
					<thead>
						<th class="sort">&nbsp;</th>
						<th><?php _e( 'Service Code', 'ups-woocommerce-shipping' ); ?></th>
						<th><?php _e( 'Name', 'ups-woocommerce-shipping' ); ?></th>
						<th class="check-column"><label for="ckbCheckAll"><input type="checkbox" id="upsCheckAll" style="float: left; margin-top: 5px;"/><div style="margin-left: 30px;padding-top: 3px;"><?php _e('Enabled', 'ups-woocommerce-shipping'); ?></div></label></th>
						<th><?php echo sprintf( __( 'Price Adjustment (%s)', 'ups-woocommerce-shipping' ), get_woocommerce_currency_symbol() ); ?></th>
						<th><?php _e( 'Price Adjustment (%)', 'ups-woocommerce-shipping' ); ?></th>
					</thead>
					<tfoot>
<?php
					if( !$this->origin_country == 'PL' && !in_array( $this->origin_country, $this->eu_array ) ) {
?>
						<tr>
							<th colspan="6">
								<small class="description"><?php _e( '<strong>Domestic Rates</strong>: Next Day Air, 2nd Day Air, Ground, 3 Day Select, Next Day Air Saver, Next Day Air Early AM, 2nd Day Air AM', 'ups-woocommerce-shipping' ); ?></small><br/>
								<small class="description"><?php _e( '<strong>International Rates</strong>: Worldwide Express, Worldwide Expedited, Standard, Worldwide Express Plus, UPS Saver', 'ups-woocommerce-shipping' ); ?></small>
							</th>
						</tr>
<?php 
	}
?>
					</tfoot>
					<tbody>
						<?php
							$sort = 0;
							$this->ordered_services = array();
							$use_services = $this->services;
							if($this->enable_freight==true) {
								$use_services= (array)$use_services + (array)$this->freight_services;	    //array + NULL will throw fatal error in php version 5.6.21
							}
							// To update the frontend with recent changes on clicking Save Changes
							$this->custom_services = isset( $this->settings['services'] ) ? $this->settings['services'] : array();
							foreach ( $use_services as $code => $name ) {

								if ( !empty($this->custom_services) && isset( $this->custom_services[ $code ] ) && isset( $this->custom_services[ $code ]['order'] ) ) {
									$sort = $this->custom_services[ $code ]['order'];
								}

								while ( isset( $this->ordered_services[ $sort ] ) )
									$sort++;

								$this->ordered_services[ $sort ] = array( $code, $name );

								$sort++;
							}

							ksort( $this->ordered_services );
						  
							foreach ( $this->ordered_services as $value ) {
								$code = $value[0];
								$name = $value[1];
								?>
								<tr>
									<td class="sort"><input type="hidden" class="order" name="ups_service[<?php echo $code; ?>][order]" value="<?php echo isset( $this->custom_services[ $code ]['order'] ) ? $this->custom_services[ $code ]['order'] : ''; ?>" /></td>
									<td><strong><?php echo $code; ?></strong><?php if( $code == 96 ) echo '<span class="xa-tooltip"><img src="'.site_url("/wp-content/plugins/woocommerce/assets/images/help.png").'" height="16" width="16" /><span class="xa-tooltiptext">In case of Weight Based Packaging, Package Dimensions will be 47x47x47 inches or 119x119x119 cm.</span></span>' ?></td>
									<td><input type="text" name="ups_service[<?php echo $code; ?>][name]" placeholder="<?php echo $name;?>" value="<?php echo isset( $this->custom_services[ $code ]['name'] ) ? $this->custom_services[ $code ]['name'] : ''; ?>" size="50" /></td>


									<td><input type="checkbox" class="checkBoxClass" name="ups_service[<?php echo $code; ?>][enabled]" <?php checked( ( ! isset( $this->custom_services[ $code ]['enabled'] ) || ! empty( $this->custom_services[ $code ]['enabled'] ) ), true ); ?> /></td>
									<td><input type="text" name="ups_service[<?php echo $code; ?>][adjustment]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ]['adjustment'] ) ? $this->custom_services[ $code ]['adjustment'] : ''; ?>" size="4" /></td>
									<td><input type="text" name="ups_service[<?php echo $code; ?>][adjustment_percent]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ]['adjustment_percent'] ) ? $this->custom_services[ $code ]['adjustment_percent'] : ''; ?>" size="4" /></td>
								</tr>
								<?php
								
							}
						?>
						
				<script type="text/javascript">
							jQuery(document).ready(function () {
						
						if (jQuery('.checkBoxClass:checked').length == jQuery('.checkBoxClass').length) {		
							jQuery("#upsCheckAll").prop("checked",true);
						}
						jQuery("#upsCheckAll").click(function () {
							jQuery(".checkBoxClass").prop('checked', jQuery(this).prop('checked'));
						});
						
						jQuery(".checkBoxClass").change(function(){
							if (!jQuery(this).prop("checked")){
								jQuery("#upsCheckAll").prop("checked",false);
							}
							if (jQuery('.checkBoxClass:checked').length == jQuery('.checkBoxClass').length) {
								jQuery("#upsCheckAll").prop("checked",true);
							}
						});
					});	
				</script>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}


	/**
	 * generate_box_packing_html function.
	 *
	 * @access public
	 * @return void
	 */
	public function generate_box_packing_html() {
		ob_start();
		?>
		<tr valign="top" id="packing_options" class="ph_ups_packaging_tab">
			<td class="forminp" colspan="2" style="padding-left:0px">
				<style type="text/css">
					.ups_boxes td, .ups_services td {
						vertical-align: middle;
						padding: 4px 7px;
					}
					.ups_boxes th, .ups_services th {
						padding: 9px 7px;
					}
					.ups_boxes td input {
						margin-right: 4px;
					}
					.ups_boxes .check-column {
						vertical-align: middle;
						text-align: left;
						padding: 0 7px;
					}
					.ups_services th.sort {
						width: 16px;
						padding: 0 16px;
					}
					.ups_services td.sort {
						cursor: move;
						width: 16px;
						padding: 0 16px;
						cursor: move;
						background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;					}
				</style>
				<strong><?php _e( 'Custom Box Dimensions', 'ups-woocommerce-shipping' ); ?></strong><br/>
				<table class="ups_boxes widefat">
					<thead>
						<tr>
							<th class="check-column"><input type="checkbox" /></th>
							<th><?php _e( 'Outer Length', 'ups-woocommerce-shipping' ); ?></th>
							<th><?php _e( 'Outer Width', 'ups-woocommerce-shipping' ); ?></th>
							<th><?php _e( 'Outer Height', 'ups-woocommerce-shipping' ); ?></th>
							<th><?php _e( 'Inner Length', 'ups-woocommerce-shipping' ); ?></th>
							<th><?php _e( 'Inner Width', 'ups-woocommerce-shipping' ); ?></th>
							<th><?php _e( 'Inner Height', 'ups-woocommerce-shipping' ); ?></th>
							<th><?php _e( 'Box Weight', 'ups-woocommerce-shipping' ); ?></th>
							<th><?php _e( 'Max Weight', 'ups-woocommerce-shipping' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="3">
								<a href="#" class="button plus insert"><?php _e( 'Add Box', 'ups-woocommerce-shipping' ); ?></a>
								<a href="#" class="button minus remove"><?php _e( 'Remove selected box(es)', 'ups-woocommerce-shipping' ); ?></a>
							</th>
							<th colspan="6">
								<small class="description"><?php _e( 'Items will be packed into these boxes depending based on item dimensions and volume. Outer dimensions will be passed to UPS, whereas inner dimensions will be used for packing. Items not fitting into boxes will be packed individually.', 'ups-woocommerce-shipping' ); ?></small>
							</th>
						</tr>
					</tfoot>
					<tbody id="rates">
						<?php
						//To update the frontend with recent changes on clicking Save Changes
						$this->boxes = isset( $this->settings['boxes'] ) ? $this->settings['boxes'] : array();
							if ( $this->boxes && ! empty( $this->boxes ) ) {
								foreach ( $this->boxes as $key => $box ) {
									?>
									<tr>
										<td class="check-column"><input type="checkbox" /></td>
										<td><input type="text" size="5" name="boxes_outer_length[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['outer_length'] ); ?>" /><?php echo $this->dim_unit; ?></td>
										<td><input type="text" size="5" name="boxes_outer_width[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['outer_width'] ); ?>" /><?php echo $this->dim_unit; ?></td>
										<td><input type="text" size="5" name="boxes_outer_height[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['outer_height'] ); ?>" /><?php echo $this->dim_unit; ?></td>
										<td><input type="text" size="5" name="boxes_inner_length[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['inner_length'] ); ?>" /><?php echo $this->dim_unit; ?></td>
										<td><input type="text" size="5" name="boxes_inner_width[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['inner_width'] ); ?>" /><?php echo $this->dim_unit; ?></td>
										<td><input type="text" size="5" name="boxes_inner_height[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['inner_height'] ); ?>" /><?php echo $this->dim_unit; ?></td>
										<td><input type="text" size="5" name="boxes_box_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['box_weight'] ); ?>" /><?php echo $this->weight_unit; ?></td>
										<td><input type="text" size="5" name="boxes_max_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['max_weight'] ); ?>" /><?php echo $this->weight_unit; ?></td>
									</tr>
									<?php
								}
							}
						?>
					</tbody>
				</table>
				<script type="text/javascript">

					jQuery(window).load(function(){

						jQuery('.ups_boxes .insert').click( function() {
							var $tbody = jQuery('.ups_boxes').find('tbody');
							var size = $tbody.find('tr').size();
							var code = '<tr class="new">\
									<td class="check-column"><input type="checkbox" /></td>\
									<td><input type="text" size="5" name="boxes_outer_length[' + size + ']" /><?php echo $this->dim_unit; ?></td>\
									<td><input type="text" size="5" name="boxes_outer_width[' + size + ']" /><?php echo $this->dim_unit; ?></td>\
									<td><input type="text" size="5" name="boxes_outer_height[' + size + ']" /><?php echo $this->dim_unit; ?></td>\
									<td><input type="text" size="5" name="boxes_inner_length[' + size + ']" /><?php echo $this->dim_unit; ?></td>\
									<td><input type="text" size="5" name="boxes_inner_width[' + size + ']" /><?php echo $this->dim_unit; ?></td>\
									<td><input type="text" size="5" name="boxes_inner_height[' + size + ']" /><?php echo $this->dim_unit; ?></td>\
									<td><input type="text" size="5" name="boxes_box_weight[' + size + ']" /><?php echo $this->weight_unit; ?></td>\
									<td><input type="text" size="5" name="boxes_max_weight[' + size + ']" /><?php echo $this->weight_unit; ?></td>\
								</tr>';

							$tbody.append( code );

							return false;
						} );

						jQuery('.ups_boxes .remove').click(function() {
							var $tbody = jQuery('.ups_boxes').find('tbody');

							$tbody.find('.check-column input:checked').each(function() {
								jQuery(this).closest('tr').hide().find('input').val('');
							});

							return false;
						});

						// Ordering
						jQuery('.ups_services tbody').sortable({
							items:'tr',
							cursor:'move',
							axis:'y',
							handle: '.sort',
							scrollSensitivity:40,
							forcePlaceholderSize: true,
							helper: 'clone',
							opacity: 0.65,
							placeholder: 'wc-metabox-sortable-placeholder',
							start:function(event,ui){
								ui.item.css('baclbsround-color','#f6f6f6');
							},
							stop:function(event,ui){
								ui.item.removeAttr('style');
								ups_services_row_indexes();
							}
						});

						function ups_services_row_indexes() {
							jQuery('.ups_services tbody tr').each(function(index, el){
								jQuery('input.order', el).val( parseInt( jQuery(el).index('.ups_services tr') ) );
							});
						};

					});

				</script>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * validate_single_select_country_field function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	public function validate_single_select_country_field( $key ) {

		if ( isset( $_POST['woocommerce_ups_origin_country_state'] ) )
			return $_POST['woocommerce_ups_origin_country_state'];
		return '';
	}
	/**
	 * validate_box_packing_field function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	public function validate_box_packing_field( $key ) {

		$boxes = array();

		if ( isset( $_POST['boxes_outer_length'] ) ) {
			$boxes_outer_length = $_POST['boxes_outer_length'];
			$boxes_outer_width  = $_POST['boxes_outer_width'];
			$boxes_outer_height = $_POST['boxes_outer_height'];
			$boxes_inner_length = $_POST['boxes_inner_length'];
			$boxes_inner_width  = $_POST['boxes_inner_width'];
			$boxes_inner_height = $_POST['boxes_inner_height'];
			$boxes_box_weight   = $_POST['boxes_box_weight'];
			$boxes_max_weight   = $_POST['boxes_max_weight'];


			for ( $i = 0; $i < sizeof( $boxes_outer_length ); $i ++ ) {

				if ( $boxes_outer_length[ $i ] && $boxes_outer_width[ $i ] && $boxes_outer_height[ $i ] && $boxes_inner_length[ $i ] && $boxes_inner_width[ $i ] && $boxes_inner_height[ $i ] ) {

					$boxes[] = array(
						'outer_length' => floatval( $boxes_outer_length[ $i ] ),
						'outer_width'  => floatval( $boxes_outer_width[ $i ] ),
						'outer_height' => floatval( $boxes_outer_height[ $i ] ),
						'inner_length' => floatval( $boxes_inner_length[ $i ] ),
						'inner_width'  => floatval( $boxes_inner_width[ $i ] ),
						'inner_height' => floatval( $boxes_inner_height[ $i ] ),
						'box_weight'   => floatval( $boxes_box_weight[ $i ] ),
						'max_weight'   => floatval( $boxes_max_weight[ $i ] ),
					);

				}

			}

		}

		return $boxes;
	}

	/**
	 * validate_services_field function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	public function validate_services_field( $key ) {
		$services		 = array();
		$posted_services  = $_POST['ups_service'];

		foreach ( $posted_services as $code => $settings ) {

			$services[ $code ] = array(
				'name'			   => wc_clean( $settings['name'] ),
				'order'			  => wc_clean( $settings['order'] ),
				'enabled'			=> isset( $settings['enabled'] ) ? true : false,
				'adjustment'		 => wc_clean( $settings['adjustment'] ),
				'adjustment_percent' => str_replace( '%', '', wc_clean( $settings['adjustment_percent'] ) )
			);

		}

		return $services;
	}

	/**
	 * clear_transients function.
	 *
	 * @access public
	 * @return void
	 */
	public function clear_transients() {
		global $wpdb;

		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_ups_quote_%') OR `option_name` LIKE ('_transient_timeout_ups_quote_%')" );
	}

	public function generate_ph_ups_settings_tabs_html() {

		$current_tab = (!empty($_GET['subtab'])) ? esc_attr($_GET['subtab']) : 'general';

		echo '
			<div class="wrap">
			<style>
				.wrap {
					min-height: 800px;
					}
				a.nav-tab{
					cursor: default;
				}
				.nav-tab-active{
					height: 24px;
				}
			</style>
			<hr class="wp-header-end">';

			$tabs = array(
				'general'		=> __("General", 'ups-woocommerce-shipping'),
				'rates'			=> __("Rates & Services", 'ups-woocommerce-shipping'),
				'labels'		=> __("Shipping Labels", 'ups-woocommerce-shipping'),
				'int_forms'		=> __("International Forms", 'ups-woocommerce-shipping'),
				'packaging'		=> __("Packaging", 'ups-woocommerce-shipping'),
				'freight'		=> __("Freight", 'ups-woocommerce-shipping'),
				'pickup'		=> __("Pickup", 'ups-woocommerce-shipping'),
				'help'			=> __("Help & Support", 'ups-woocommerce-shipping'),
			);

			$html = '<h2 class="nav-tab-wrapper">';

			foreach ($tabs as $stab => $name) {
				$class = ($stab == $current_tab) ? 'nav-tab-active' : '';
				$html .= '<a style="text-decoration:none !important;" class="nav-tab ph-ups-tabs ' . $class." tab_".$stab . '" >' . $name . '</a>';
			}

			$html .= '</h2>';

			echo $html;

	}

	public function generate_help_support_section_html() {

		ob_start();
		include( 'html-ph-help-and-support.php' );
		return ob_get_clean();
	}

	/**
	 * init_form_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {

		global $woocommerce;
		$wc_countries   = new WC_Countries();
		
		if ( WF_UPS_ADV_DEBUG_MODE == "on" ) { // Test mode is only for development purpose.
			$api_mode_options = array(
				'Test'		   => __( 'Test', 'ups-woocommerce-shipping' ),
			);
		}
		else {
			$api_mode_options = array(
				'Live'		   => __( 'Live', 'ups-woocommerce-shipping' ),
				'Test'		   => __( 'Test', 'ups-woocommerce-shipping' ),
			);
		}

		
		$pickup_start_time_options	=	array();
		foreach(range(0,23,0.5) as $pickup_start_time){
			$pickup_start_time_options[(string)$pickup_start_time]	=	date("H:i",strtotime(date('Y-m-d'))+3600*$pickup_start_time);
		}

		$pickup_close_time_options	=	array();
		foreach(range(0.5,23.5,0.5) as $pickup_close_time){
			$pickup_close_time_options[(string)$pickup_close_time]	=	date("H:i",strtotime(date('Y-m-d'))+3600*$pickup_close_time);
		}

		$ship_from_address_options		=	apply_filters( 'wf_filter_label_ship_from_address_options', array(
					'origin_address'   => __( 'Origin Address', 'ups-woocommerce-shipping' ),
					'billing_address'  => __( 'Shipping Address', 'ups-woocommerce-shipping' ),
				)
		);

		$shipping_class_option_arr = array();
		$shipping_class_arr = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );
		foreach( $shipping_class_arr as $shipping_class_detail ) {
			if( is_object($shipping_class_detail) )
			{
				$shipping_class_option_arr[$shipping_class_detail->slug] = $shipping_class_detail->name;
			}
		}

		// $payment_gateways 	= $woocommerce->payment_gateways->get_available_payment_gateways();
		// $available_gateways = array();
		// if( !empty($payment_gateways) && is_array($payment_gateways) ) {
		// 	foreach ($payment_gateways as $gateway_key => $gateway) {
		// 		$available_gateways[$gateway_key] 	= $gateway->title;
		// 	}
		// }

		$woocommerce_blocks_active = false;
		$woocommerce_blocks_active = Wf_Dependencies::ph_plugin_active_check( 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php' );

		$data = array('woocommerce_blocks_active' => $woocommerce_blocks_active);
		
		if( is_admin() ){
				 wp_enqueue_script( 'ph-ups-js', plugins_url('../resources/js/wf_ups.js',__FILE__ ), array( 'jquery' ) );
				 wp_localize_script( 'ph-ups-js', 'Ph_woocommerce_plugin_status', $data );
		}

		$this->form_fields  = array(
		   
			'tabs_wrapper'	=> array(
				'type'			=>'ph_ups_settings_tabs'
			),

			// General Tab

			'general-title'			=> array(
				'title' 		=> __( 'UPS Account & Address Settings', 'ups-woocommerce-shipping' ),
				'type'			=> 'title',
				'description'	=> __( 'Obtain UPS account credentials by registering on UPS website.', 'ups-woocommerce-shipping' ),
				'class'			=> 'ph_ups_general_tab',
			),
			'api_mode' 		=> array(
				'title' 		=> __( 'API Mode', 'ups-woocommerce-shipping' ),
				'type' 			=> 'select',
				'default' 		=> 'yes',
				'class' 		=> 'wc-enhanced-select ph_ups_general_tab',
				'options' 		=> $api_mode_options,
				'description' 	=> __( 'Set as Test to switch to UPS api test servers. Transaction will be treated as sample transactions by UPS.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true
			),
			'user_id'		=> array(
				'title'			=> __( 'UPS User ID', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Obtained from UPS after getting an account.', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'password'		=> array(
				'title'			=> __( 'UPS Password', 'ups-woocommerce-shipping' ),
				'type'			=> 'password',
				'description'	=> __( 'Obtained from UPS after getting an account.', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'access_key'		=> array(
				'title'			=> __( 'UPS Access Key', 'ups-woocommerce-shipping' ),
				'type'			=> 'password',
				'description'	=> __( 'Obtained from UPS after getting an account.', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'shipper_number'=> array(
				'title'			=> __( 'UPS Account Number', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Obtained from UPS after getting an account.', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'debug' 		=> array(
				'title' 		=> __( 'Debug Mode', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable debug mode to show debugging information on your cart/checkout.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'ssl_verify'	  => array(
				'title'		   => __( 'SSL Verify', 'ups-woocommerce-shipping' ),
				'type'			   => 'select',
				'default'			=> 0,
				'class'				=>'wc-enhanced-select ph_ups_general_tab',
				'options'			=> array(
					0		=> __( 'No', 	'ups-woocommerce-shipping' ),
					1		=> __( 'Yes',	'ups-woocommerce-shipping' ),
				),
				'description'	 => __( 'SSL Verification for API call. Recommended select \'No\'.', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
			),
			'units'			=> array(
				'title'			=> __( 'Weight/Dimension Units', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'description'	=> __( 'Switch this to metric units, if you see "This measurement system is not valid for the selected country" errors.', 'ups-woocommerce-shipping' ),
				'default'		=> 'imperial',
				'class'			=> 'wc-enhanced-select',
				'options'		=> array(
					'imperial'		=> __( 'LB / IN', 'ups-woocommerce-shipping' ),
					'metric'		=> __( 'KG / CM', 'ups-woocommerce-shipping' ),
				),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'negotiated'	=> array(
				'title'			=> __( 'Negotiated Rates', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable this if this shipping account has negotiated rates available.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'residential'	=> array(
				'title'			=> __( 'Residential', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Ship to address is Residential.', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'This will indicate to UPS that the receiver is always a residential address.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'address_validation' => array(
				'title'			=> __( 'Address Classification', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'description'	=> __( 'Helps in classifying address as Commercial or Residential. Applicable for United States and Puert Rico. Debug details are available in WC_Logger.' , 'ups-woocommerce-shipping'),
				'desc_tip'		=> true,
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_general_tab'
			),
			'suggested_address' => array(
				'title'			=> __( 'Enable Address Suggestion From UPS', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'description'	=> __( 'Provides Address Suggestions Based On Addresses In UPS Database', 'ups-woocommerce-shipping'),
				'desc_tip'		=> true,
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_general_tab'
			),
			'suggested_display'   => array(   
				'title'			=> __( 'Address Suggestion on Checkout Page', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> 'suggested_notice',
				'class'			=> 'wc-enhanced-select ph_ups_general_tab',
				'options'		=> array(
					'suggested_notice'   => __( 'Display as Notice', 'ups-woocommerce-shipping' ),
					'suggested_radio'    => __( 'Display as Options', 'ups-woocommerce-shipping' ),
				),
				'description'	=> __( 'Select how the address suggestion is displayed on the WooCommerce checkout page.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true
			),
			'insuredvalue'	=> array(
				'title'			=> __( 'Insurance Option', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Request Insurance to be included.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'min_order_amount_for_insurance' 	=> array(
				'title'			=> __( 'Min Order Amount', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Insurance will apply only if Order subtotal amount is greater or equal to the Min Order Amount. Note - For Comparison it will take only the sum of product price i.e Order Subtotal amount. In Cart It will take Cart Subtotal Amount.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'ship_from_address'   => array(
				'title'			=> __( 'Ship From Address Preference', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> 'origin_address',
				'class'			=> 'wc-enhanced-select ph_ups_general_tab',
				'options'		=> $ship_from_address_options,
				'description'	=> __( 'Change the preference of Ship From Address printed on the label. You can make  use of Billing Address from Order admin page, if you ship from a different location other than shipment origin address given below.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true
			),
			'ups_user_name'	=> array(
				'title'			=> __( 'Company Name', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Enter your company name', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'ups_display_name'	=> array(
				'title'			=> __( 'Attention Name', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Your business/attention name.', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'origin_addressline'  => array(
				'title'		   => __( 'Origin Address Line 1', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Shipping Origin Address Line 1 (Ship From Address).', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'origin_addressline_2'  => array(
				'title'			=> __( 'Origin Address Line 2', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Shipping Origin Address Line 2 (Ship From Address).', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'origin_city'	  	  => array(
				'title'		   => __( 'Origin City', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Origin City (Ship From City)', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'origin_country_state'	=> array(
				'type'				=> 'single_select_country',
			),
			'origin_custom_state'		=> array(
				'title'		   => __( 'Origin State Code', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Specify shipper state province code if state not listed with Origin Country.', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'origin_postcode'	 => array(
				'title'		   => __( 'Origin Postcode', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Ship From Zip/postcode.', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'phone_number'		=> array(
				'title'		   => __( 'Your Phone Number', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Your contact phone number.', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'email'		=> array(
				'title'		   => __( 'Your email', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Your email.', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_general_tab',
			),
			'ship_from_address_different_from_shipper'	=>	array(
				'title'			=>	__( 'Ship From Address Different from Shipper Address', 'ups-woocommerce-shipping' ),
				'label'			=>	__( 'Enable', 'ups-woocommerce-shipping'),
				'description'	=>	__( 'Shipper Address - Address to be printed on the label.<br> Ship From Address - Address from where the UPS will pickup the package (like Warehouse Address).<br>By Default Shipper address and Ship From Address are same. By enabling it, Ship From Address can be defined seperately.', 'ups-woocommerce-shipping'),
				'desc_tip'		=> true,
				'type'			=>	'checkbox',
				'default'		=>	'no',
				'class'			=> 'ph_ups_general_tab',
			),

			'ship_from_addressline'  => array(
				'title'		   => __( 'Ship From Address Line 1', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Ship From Address Line 1', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_different_ship_from_address ph_ups_general_tab'
			),
			'ship_from_addressline_2'  => array(
				'title'			=> __( 'Ship From Address Line 2', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Ship From Address Line 2', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_different_ship_from_address ph_ups_general_tab'
			),
			'ship_from_city'	  	  => array(
				'title'		   => __( 'Ship From City', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Ship From City', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_different_ship_from_address ph_ups_general_tab'
			),
			'ship_from_country_state'	=> array(
				'type'				=> 'ship_from_country_state',
			),
			'ship_from_custom_state'		=> array(
				'title'		   => __( 'Ship From State Code', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Specify shipper state province code if state not listed with Ship From Country.', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_different_ship_from_address ph_ups_general_tab'
			),
			'ship_from_postcode'	 => array(
				'title'		   => __( 'Ship From Postcode', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Ship From Zip/postcode.', 'ups-woocommerce-shipping' ),
				'default'		 => '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_different_ship_from_address ph_ups_general_tab'
			),
			'billing_address_as_shipper'	=>	array(
				'title'			=> __( 'Billing Address as Shipper Address on Label', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable (Not Applicable for Freight Shipment)', 'ups-woocommerce-shipping'),
				'description'	=> __( 'Billing Address will be printed on the label.<br> UPS will pickup the package from the address set under Ship From Address Preference or Ship From Address Different from Shipper Address', 'ups-woocommerce-shipping'),
				'desc_tip'		=> true,
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_general_tab',
			),
			'skip_products'	=> array(
				'title'			=>	__( 'Skip Products', 'ups-woocommerce-shipping' ),
				'type'			=>	'multiselect',
				'options'		=>	$shipping_class_option_arr,
				'description'	=>	__( 'Skip all the products belonging to the selected Shipping Classes while fetching rates and creating Shipping Label.', 'ups-woocommerce-shipping'),
				'desc_tip'		=>	true,
				'class'			=>	'chosen_select ph_ups_general_tab',
			),
			'xa_show_all' => array(
				'title'		   => __( 'Show All Services in Order Page', 'ups-woocommerce-shipping' ),
				'label'		   => __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'description'	 => __( 'Check this option to show all services in create label drop down(UPS).', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'class'			=> 'ph_ups_general_tab'
			),
			'remove_recipients_phno' => array(
				'title'		   => __( 'Remove Recipients Phone Number', 'ups-woocommerce-shipping' ),
				'label'		   => __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'description'	 => __( 'Adding Customer Phone Number is mandatory in shipping labels only for International Shipments or certain Domestic Services. Enabling this option will make sure customer phone number is not added to any other services', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'class'		=> 'ph_ups_general_tab'
			),
			'shipper_release_indicator' => array(
				'title'			=> __( 'Display Shipper Release Indicator', 'ups-woocommerce-shipping' ),
				'label' 		=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type' 			=> 'checkbox',
				'default' 		=> 'no',
				'description' 	=> __( 'Enabling this option indicates that the package may be released by driver without a signature from the consignee. Only available for US/PR to US/PR packages without return service. This will be added only for Packages that do not require Signature & is not a COD shipment.', 'ups-woocommerce-shipping' ),
				'desc_tip' 		=> true,
				'class'		=> 'ph_ups_general_tab'
			),
			'international_special_commodities' => array(
				'title'         => __( 'International Special Commodities', 'ups-woocommerce-shipping' ),
				'label'         => __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'          => 'checkbox',
				'default'       => 'no',
				'description'   => __( 'Enabling this option indicates that the package may contain biological items or item with International Special Commodity standards.', 'ups-woocommerce-shipping' ),
				'desc_tip'      => true,
				'class'     	=> 'ph_ups_general_tab'
			),
			'ph_delivery_confirmation'   => array(
				'title'			=> __( 'Delivery Confirmation', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'class'			=> 'wc-enhanced-select ph_ups_general_tab',
				'description'	=> __( 'Appropriate signature option for your shipping service.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'default'       => 0,
				'options'	=> array(
					0	=> __( 'Confirmation Not Required', 'ups-woocommerce-shipping' ),
					1	=> __( 'Confirmation Required', 'ups-woocommerce-shipping' ),
					2	=> __( 'Confirmation With Signature', 'ups-woocommerce-shipping' ),
					3	=> __( 'Confirmation With Adult Signature', 'ups-woocommerce-shipping' )
				),
			),

			// Rate & Services

			'rate-title'			=> array(
				'title' 		=> __( 'Shipping Rate Settings', 'ups-woocommerce-shipping' ),
				'type'			=> 'title',
				'description'	=> __( 'Configure the shipping rate related settings. You can enable the desired UPS shipping services and other rate options.', 'ups-woocommerce-shipping' ),
				'class'			=> 'ph_ups_rates_tab',
			),
			'enabled' 		=> array(
				'title' 		=> __( 'Realtime Rates', 'ups-woocommerce-shipping' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'default'		=> 'no',
				'description'	=> __( 'Enable realtime rates on Cart/Checkout page.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_rates_tab',
			),
			'title' 		=> array(
				'title'			=> __( 'UPS Method Title', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'This controls the title which the user sees during checkout.', 'ups-woocommerce-shipping' ),
				'default'		=> __( 'UPS', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_rates_tab',
			),
			'availability' 	=> array(
				'title' 		=> __( 'Method Availability', 'ups-woocommerce-shipping' ),
				'type' 			=> 'select',
				'default' 		=> 'all',
				'class' 		=> 'availability wc-enhanced-select ph_ups_rates_tab',
				'options' 		=> array(
					'all' 			=> __( 'All Countries', 'ups-woocommerce-shipping' ),
					'specific' 		=> __( 'Specific Countries', 'ups-woocommerce-shipping' ),
				),
			),
			'countries'		=> array(
				'title'			=> __( 'Specific Countries', 'ups-woocommerce-shipping' ),
				'type' 			=> 'multiselect',
				'class' 		=> 'chosen_select ph_ups_rates_tab',
				'css' 			=> 'width: 450px;',
				'default'		=> '',
				'options'		=> $wc_countries->get_allowed_countries(),
			),
			'enable_estimated_delivery'		=> array(
				'title'			=> __( 'Show Estimated Delivery', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to display Estimated delivery.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_rates_tab',
			),
			'estimated_delivery_text'	=>	array(
				'title'			=>	__( 'Estimated Delivery Text', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'default'		=> 'Est delivery :',
				'placeholder'	=> 'Est delivery :',
				'desc_tip'		=> __( 'Given text will be used to show estimated delivery.', 'ups-woocommerce-shipping' ),
				'class'			=> 'ph_ups_rates_tab ph_ups_est_delivery',
			),
			'cut_off_time'	=>	array(
				'title'			=>	__( 'Cut-Off Time', 'ups-woocommerce-shipping' ),
				'type'			=>	'text',
				'default'		=>	'24:00',
				'placeholder'	=>	'24:00',
				'desc_tip'		=> __( 'Estimated delivery will be adjusted to the next day if any order is placed after cut off time. Use 24 hour format (Hour:Minute). Example - 23:00.', 'ups-woocommerce-shipping' ),
				'class'			=> 'ph_ups_rates_tab ph_ups_est_delivery'
			),
			'est_delivery_for_woocommerce_blocks'	=> array(
				'title'			=> __( 'WooCommerce Blocks Integration', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enabling this option will display the estimated delivery date for the UPS shipping methods</br>inside the blocks on WooCommerce cart & checkout page.' ),
				'type'			=> 'checkbox',
				'deefault'		=> 'no',
				'description'	=> __( 'Enable it to display Estimated delivery in Woocommerce Blocks cart and checkout pages.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_rates_tab ph_ups_est_delivery',
			),
			'ups_rate_caching' => array(
				'title' 		=> __( 'Shipping Rates Cache Limit', 'ups-woocommerce-shipping' ),
				'desc_tip' 		=> true,
				'type' 			=> 'select',
				'options'		=>	array(
					'1'		=> __( "1 Hour", 'ups-woocommerce-shipping' ),
					'2'		=> __( "2 Hours", 'ups-woocommerce-shipping' ),
					'4'		=> __( "4 Hours", 'ups-woocommerce-shipping' ),
					'6'		=> __( "6 Hours", 'ups-woocommerce-shipping' ),
					'9'		=> __( "9 Hours", 'ups-woocommerce-shipping' ),
					'12'	=> __( "12 Hours", 'ups-woocommerce-shipping' ),
					'15'	=> __( "15 Hours", 'ups-woocommerce-shipping' ),
					'18'	=> __( "18 Hours", 'ups-woocommerce-shipping' ),
					'21'	=> __( "21 Hours", 'ups-woocommerce-shipping' ),
					'24'	=> __( "24 Hours", 'ups-woocommerce-shipping' ),
				),
				'default'		=> '24',
				'class' 		=> 'wc-enhanced-select ph_ups_rates_tab',
				'description' 	=> __('Select the Time Period you want to store the Rates.', 'ups-woocommerce-shipping'),
			),
			'pickup'  => array(
				'title'		   => __( 'Rates Based On Pickup Type', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'css'			  => 'width: 250px;',
				'class'			  => 'chosen_select wc-enhanced-select ph_ups_rates_tab',
				'default'		 => '01',
				'options'		 => $this->pickup_code,
			),
			'customer_classification'  => array(
				'title'		   => __( 'Customer Classification', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'css'			  => 'width: 250px;',
				'class'			  => 'chosen_select wc-enhanced-select ph_ups_rates_tab',
				'default'		 => 'NA',
				'options'		 => $this->customer_classification_code,
				'description'	 => __( 'Valid if origin country is US.' ),
				'desc_tip'		=> true
			),
			'cod_enable'	=> array(
				'title'			=> __( 'UPS COD', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable this to calculate COD Rates on Cart/Checkout', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_rates_tab',
			),
			'eu_country_cod_type' => array(
				'title' 		=> __( 'COD Type', 'ups-woocommerce-shipping' ),
				'desc_tip' 		=> true,
				'type' 			=> 'select',
				'options'		=>	array(
					'9'	=> __( "Check / Cashier's Check / Money Order", 'ups-woocommerce-shipping' ),
					'1'	=> __( "Cash", 'ups-woocommerce-shipping' ),
				),
				'default'		=> '9',
				'class' 		=> 'wc-enhanced-select ph_ups_rates_tab',
				'description' 	=> __('Collect on Delivery Type for all European Union (EU) Countries or Territories', 'ups-woocommerce-shipping'),
			),
			
			'email_notification'  => array(
				'title'			=> __( 'Send email notification to', 'ups-woocommerce-shipping' ),
				'type'			=> 'multiselect',
				'class'			=> 'multiselect chosen_select ph_ups_rates_tab',
				'default'		=> '',
				'options'		=> array(
					'sender'		=>'Sender',
					'recipient'		=>'Recipient'
				),
				'description'	=> __( 'Choose whom to send the notification. Leave blank to not send notification.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
			),
			'tax_indicator'	  => array(
				'title'		   => __( 'Tax On Rates', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Taxes may be applicable to shipment', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'class'			=> 'ph_ups_rates_tab'
			),
			'ups_tradability'	  => array(
				'title'		   	=> __( 'Display Additional Taxes & Charges on cart page', 'ups-woocommerce-shipping' ),
				'description' 	=> __( 'Additional Taxes & Charges will be displayed along with the shipping cost at the cart & checkout page. These charges wont be added to the cart total.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_rates_tab'
			),
			'tradability_cart_title'	  => array(
				'title'		   	=> __( 'Custom Text for Additional Taxes & Charges', 'ups-woocommerce-shipping' ),
				'description' 	=> __( 'This text will be displayed at the cart & checkout page. (max - 35 characters)', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'type'			=> 'text',
				'default'		=> __( 'Additional Taxes & Charges', 'ups-woocommerce-shipping' ),
				'class'			=> 'ph_ups_rates_tab'
			),
			'accesspoint_locator' => array(
				'title'		   => __( 'UPS Access Point® Locator', 'ups-woocommerce-shipping' ),
				'label'		   => __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'class'			=> 'ph_ups_rates_tab'
			),
			'accesspoint_req_option' => array(
				'title'			=> __( 'Location Type', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> 1,
				'options'		=> array(
					'1'			=> __( 'Display UPS Stores', 'ups-woocommerce-shipping' ),
					'64'		=> __( 'Display All UPS Access Point® Locations', 'ups-woocommerce-shipping' )
				),
				'class'			=> 'wc-enhanced-select ph_ups_rates_tab ph_ups_accesspoint'
			),
			'accesspoint_max_limit'  => array(
			'title'			=> __( 'Max Location Options', 'ups-woocommerce-shipping' ),
			'type'			=> 'number',
			'default'		=> '10',
			'description'	=> __( '<small><i>Select the maximum number of UPS Access Point® Locations that will be displayed.</i></small>', 'ups-woocommerce-shipping' ),
			'class'			=> 'ph_ups_rates_tab ph_ups_accesspoint ph_ups_accesspoint_limit',
			),
			'accesspoint_option_code' => array(
				'title'			=> __( 'UPS Access Point® Location Options', 'ups-woocommerce-shipping' ),
				'type'			=> 'multiselect',
				'default'		=> '018',
				'desc_tip'		=> true,
				'description'	=> __( 'Selecte the Access Point Location Option', 'ups-woocommerce-shipping' ),
				'class'			=> 'ph_ups_rates_tab ph_ups_accesspoint chosen_select',
				'css'			=> 'width: 400px;',
				'options'		=> $this->accesspoint_location_option,
			),
			// 'restricted_payments'	=> array(
			// 	'title'			=> __( 'Restricted Payments for Access Point® Location', 'ups-woocommerce-shipping' ),
			// 	'type' 			=> 'multiselect',
			// 	'class' 		=> 'chosen_select ph_ups_rates_tab',
			// 	'css' 			=> 'width: 400px;',
			// 	'default'		=> '',
			// 	'options'		=> $available_gateways,
			// ),
			'tin_number'  => array(
				'title'		   => __( 'TIN', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'placeholder'	  => 'Tax Identification Number',
				'description'	 => __( 'Tax Identification Number', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'class'			=> 'ph_ups_rates_tab',
			),
			'recipients_tin'  => array(
				'title'			=> __( 'Recipient TIN', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'description'	=> __( "Recipient's Tax Identification Number", 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_rates_tab'
			),
			'offer_rates'	=> array(
				'title'			=> __( 'Offer Rates', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'class'			=> 'wc-enhanced-select ph_ups_rates_tab',
				'description'	=> '<strong>'.__('Default Shipping Rates - ', 'ups-woocommerce-shipping').'</strong>'.__('It will return shipping rates for all the valid shipping services.', 'ups-woocommerce-shipping').'<br/><strong>'.__( 'Cheapest Rate - ', 'ups-woocommerce-shipping' ).'</strong>'.__( 'It will display only the cheapest shipping rate with service name as Shipping Method Title (if given) or the default Shipping Service Name will be shown.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'default'		=> 'all',
				'options'		=> array(
					'all'			=> __( 'All Shipping Rates (Default)', 'ups-woocommerce-shipping' ),
					'cheapest'		=> __( 'Cheapest Rate', 'ups-woocommerce-shipping' ),
				),
			),
			'services_packaging'  => array(
				'title'		   => __( 'Services', 'ups-woocommerce-shipping' ),
				'type'			=> 'title',
				'class'			=> 'ph_ups_rates_tab',
				'description'	 => '',
			),
			'services'			=> array(
				'type'			=> 'services'
			),
			'fallback'		=> array(
				'title'			=> __( 'Fallback', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'If UPS returns no matching rates, offer this amount for shipping so that the user can still checkout. Leave blank to disable.', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_rates_tab',
			),
			'currency_type'	=> array(
				'title'	   	=> __( 'Currency', 'ups-woocommerce-shipping' ),
				'label'	  	=> __( 'Currency', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'class'			=> 'wc-enhanced-select ph_ups_rates_tab',
				'options'	 	=> get_woocommerce_currencies(),
				'default'	 	=> get_woocommerce_currency(),	
				'description' 	=> __( 'This currency will be used to communicate with UPS.', 'ups-woocommerce-shipping' ),
			),
			'conversion_rate'	 => array(
				'title' 		  => __('Conversion Rate.', 'ups-woocommerce-shipping'),
				'type' 			  => 'text',
				'default'		 => 1,
				'description' 	  => __('Enter the conversion amount in case you have a different currency set up comparing to the currency of origin location. This amount will be multiplied with the shipping rates. Leave it empty if no conversion required.', 'ups-woocommerce-shipping'),
				'desc_tip' 		  => true,
				'class'			=> 'ph_ups_rates_tab',
			),
			'min_amount'  => array(
				'title'		   => __( 'Minimum Order Amount', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'placeholder'	=> wc_format_localized_price( 0 ),
				'default'		 => '0',
				'description'	 => __( 'Users will need to spend this amount to get this shipping available.', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'class'			=> 'ph_ups_rates_tab',
			),
			'min_weight_limit' => array(
				'title'		   => __( 'Minimum Weight', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Shipping Rates will be returned and Label will be created, if the total weight(After skipping the Products) is more than the Minimum Weight.', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'class'			=> 'ph_ups_rates_tab'
			),
			'max_weight_limit' => array(
				'title'		   => __( 'Maximum Weight', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Shipping Rates will be returned and Label will be created, if the total weight(After skipping the Products) is less than the Maximum Weight.', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'class'			=> 'ph_ups_rates_tab',
			),			

			// Labels

			'label-title'=> array(
				'title'			=> __( 'UPS Shipping Label Settings', 'ups-woocommerce-shipping' ),
				'type'			=> 'title',
				'class'			=> 'ph_ups_label_tab',
				'description'	=> __( 'Configure the UPS shipping label related settings', 'ups-woocommerce-shipping' ),
			),
			'disble_ups_print_label' => array(
				'title'			=> __( 'Label Printing', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> 'no',
				'class'			=> 'wc-enhanced-select ph_ups_label_tab',
				'options'		=> array(
					'no'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
					'yes'			=> __( 'Disable', 'ups-woocommerce-shipping' ),
				),
			),
			'print_label_type'	=> array(
				'title'			=> __( 'Print Label Type', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> 'gif',
				'class'			=> 'wc-enhanced-select ph_ups_label_tab',
				'options'		=> array(
					'gif'			=> __( 'GIF', 'ups-woocommerce-shipping' ),
					'png'			=> __( 'PNG', 'ups-woocommerce-shipping' ),
					'zpl'			=> __( 'ZPL', 'ups-woocommerce-shipping' ),
					'epl'			=> __( 'EPL', 'ups-woocommerce-shipping' ),
				),
				'description'	=> __( 'Selecting PNG will enable ~4x6 dimension label. Note that an external api labelary is used. For Laser 8.5X11 please select GIF.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true
			),
			'show_label_in_browser'  => array(
				'title'			=> __( 'Display Labels in Browser for Individual Order', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enabling this will display the label in the browser instead of downloading it. Useful if your downloaded file is getting currupted because of PHP BOM (ByteOrderMark).', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_label_tab',
			),
			'rotate_label'  => array(
				'title'			=> __( 'Display label in Landscape mode', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping'),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'desc_tip'		=> false,
				'class'			=> 'ph_ups_label_tab',
			),
			'label_in_browser_zoom'  => array(
				'title'			=> __( 'Custom Scaling (%)', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable' ),
				'type'			=> 'text',
				'default'		=> '100',
				'description'	=> __( 'Provide a percentage value to scale the shipping label image based on your preference.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_label_tab',
			),
			'label_margin'  => array(
				'title'			=> __( 'Margin', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable' ),
				'type'			=> 'number',
				'default'		=> '0',
				'description'	=> __( 'Applicable for all 4 sides (px).', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_label_tab ups_display_browser_options',
				'custom_attributes' => array('min'=> 0, 'step'=>'any'),
			),
			'label_vertical_align'  => array(
				'title'			=> __( 'Vertical Alignment', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable' ),
				'type'			=> 'select',
				'default'		=> 'center',
				'class'			=> 'ph_ups_label_tab ups_display_browser_options',
				'options'		=> array(
					'flex-start'	=>	__( 'Top','ups-woocommerce-shipping' ),
					'center'		=>	__( 'Center','ups-woocommerce-shipping' ),
					'flex-end'		=>	__( 'Bottom','ups-woocommerce-shipping' ),
				),
			),
			'label_horizontal_align'  => array(
				'title'			=> __( 'Horizontal Alignment', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable' ),
				'type'			=> 'select',
				'default'		=> 'center',
				'class'			=> 'ph_ups_label_tab ups_display_browser_options',
				'options'		=> array(
					'left'				=>	__( 'Left','ups-woocommerce-shipping' ),
					'center'			=>	__( 'Center','ups-woocommerce-shipping' ),
					'right'				=>	__( 'Right','ups-woocommerce-shipping' ),
				),
			),
			'label_format'  => array(
				'title'			=> __( 'Label Format', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'description'	=> __( 'This feature will be deprecated in the upcoming version of the plugin.', 'ups-woocommerce-shipping' ),
				'options'		=> array(
					null				=>	__( 'None','ups-woocommerce-shipping' ) ,
					'laser_8_5_by_11'	=>	__( 'Laser 8.5 X 11','ups-woocommerce-shipping' ) ,
				),
				'class'			=> 'ph_ups_label_tab',
			),
			'transportation'  => array(
				'title'            => __( 'Transportation', 'ups-woocommerce-shipping' ),
				'type'            => 'select',
				'default'			=> 'shipper',
				'class'            => 'wc-enhanced-select ph_ups_label_tab',
				'options'        => array(
					'shipper' 	 => __( 'Shipper', 'ups-woocommerce-shipping'),
					'third_party'=> __( 'Third Party', 'ups-woocommerce-shipping'),
				),
				'description'    => __( 'Select who will pay the Transportation Charges', 'ups-woocommerce-shipping' ),
				'desc_tip'        => true,
			),
			'transport_payor_acc_no'	=> array(
				'title'		   => __( 'Third Party Account Number', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'default'		 => '',
				'class'			  => 'thirdparty_grp ph_ups_label_tab',
				'desc_tip'	=> true,
			),
			'transport_payor_post_code'	=> array(
				'title'		   => __( 'Third Party Postcode', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'default'		 => '',
				'class'			  => 'thirdparty_grp ph_ups_label_tab',
				'desc_tip'	=> true,
			),
			'transport_payor_country_code'	=> array(
				'title'		   => __( 'Third Party Country code', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'default'		 => '',
				'class'			  => 'thirdparty_grp ph_ups_label_tab',
				'desc_tip'	=> true,
			),
			'duties_and_taxes'  => array(
				'title'            => __( 'Duties And Taxes Payer', 'ups-woocommerce-shipping' ),
				'type'            => 'select',
				'default'			=> 'receiver',
				'class'            => 'wc-enhanced-select ph_ups_label_tab',
				'options'        => array(
					'receiver' 	 => __( 'Reciever', 'ups-woocommerce-shipping'),
					'shipper' 	 => __( 'Shipper', 'ups-woocommerce-shipping'),
					'third_party'=> __( 'Third Party', 'ups-woocommerce-shipping'),
				),
				'description'    => __( 'Select who will pay the Duties and Taxes.<br/> * Duties and Taxes Payer will be default to Shipper in case the customers select Access Point Location.', 'ups-woocommerce-shipping' ),
				'desc_tip'        => true,
			),
			'shipping_payor_acc_no'	=> array(
				'title'		   => __( 'Third Party Account Number', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'default'		 => '',
				'class'			  => 'thirdparty_grp ph_ups_label_tab',
				'desc_tip'	=> true,
			),
			'shipping_payor_post_code'	=> array(
				'title'		   => __( 'Third Party Postcode', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'default'		 => '',
				'class'			  => 'thirdparty_grp ph_ups_label_tab',
				'desc_tip'	=> true,
			),
			'shipping_payor_country_code'	=> array(
				'title'		   => __( 'Third Party Country code', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'default'		 => '',
				'class'			  => 'thirdparty_grp ph_ups_label_tab',
				'desc_tip'	=> true,
			),
			'dangerous_goods_manifest' => array(
				'title'			=> __( 'Dangerous Goods Manifest', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable to print Dangerous Goods Manifest', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_label_tab'
			),
			'dangerous_goods_signatoryinfo' => array(
				'title'			=> __( 'Dangerous Goods Signatory Information', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable this option to print the dangerous goods signatory information along with the shipping labels.', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_label_tab'
			),
			'mail_innovation_type'   => array(
				'title'			=> __( 'Mail Innovation Packaging Type', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> '66',
				'class'			=> 'wc-enhanced-select ph_ups_label_tab',
				'options'		=> array(
					'57'	=> __( 'International Parcel: Parcels', 'ups-woocommerce-shipping' ),
					'62'	=> __( 'Domestic Parcel < 1LBS: Irregulars', 'ups-woocommerce-shipping' ),
					'63'	=> __( 'Domestic Parcel > 1LBS: Parcel Post', 'ups-woocommerce-shipping' ),
					'64'	=> __( 'Domestic Parcel: BPM Parcel', 'ups-woocommerce-shipping' ),
					'65'	=> __( 'Domestic Parcel: Media Mail', 'ups-woocommerce-shipping' ),
					'66'	=> __( 'Flat: BPM Flat', 'ups-woocommerce-shipping' ),
					'67'	=> __( 'Flat: Standard FLat', 'ups-woocommerce-shipping' ),
				),
				'description'	=> __( "Select the Packaging Type for Mail Innovation Services. For International Mail Innovations Shipments by default value will be 'Parcels'", 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
			),
			'usps_endorsement'   => array(
				'title'			=> __( 'USPS Endorsement for Mail Innovation', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> '5',
				'class'			=> 'wc-enhanced-select ph_ups_label_tab',
				'options'		=> array(
					'1'	=> __( 'Return Service', 'ups-woocommerce-shipping' ),
					'2'	=> __( 'Forwarding Service', 'ups-woocommerce-shipping' ),
					'3'	=> __( 'Address Service', 'ups-woocommerce-shipping' ),
					'4'	=> __( 'Change Service', 'ups-woocommerce-shipping' ),
					'5'	=> __( 'No Service', 'ups-woocommerce-shipping' ),
				),
				'description'	=> __( "Select the USPS Endorsement Type for Mail Innovation Services. For International Mail Innovations Shipments by default value will be 'No Service'", 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
			),
			'latin_encoding' => array(
				'title'		   => __( 'Enable Latin Encoding', 'ups-woocommerce-shipping' ),
				'label'		   => __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'description'	 => __( 'Check this option to use Latin encoding over default encoding.', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'class'			=> 'ph_ups_label_tab'
			),
			'disble_shipment_tracking'   => array(
				'title'			=> __( 'Shipment Tracking', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> 'yes',
				'class'			=> 'wc-enhanced-select ph_ups_label_tab',
				'options'		=> array(
					'TrueForCustomer'	=> __( 'Disable for Customer', 'ups-woocommerce-shipping' ),
					'False'				=> __( 'Enable', 'ups-woocommerce-shipping' ),
					'True'				=> __( 'Disable', 'ups-woocommerce-shipping' ),
				),
				'description'	=> __( 'Selecting Disable for customer will hide shipment tracking info from customer side order details page.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,

			),
			'custom_message' => array(
				'title'		   => __( 'Tracking Message', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'placeholder'	=>	__( 'Your order is shipped via UPS. To track shipment, please follow the shipment ID(s) ', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Provide Your Tracking Message. Tracking Id(s) will be appended at the end of the tracking message.', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'class'			=> 'ph_ups_label_tab'
			),
			'automate_package_generation'	  => array(
				'title'		   => __( 'Generate Packages Automatically After Order Received', 'ups-woocommerce-shipping' ),
				'label'			  => __( 'Enable', 'ups-woocommerce-shipping' ),			
				'description'	 => __( 'This will generate packages automatically after order is received and payment is successful', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'class'			=> 'ph_ups_label_tab'
			),
			'automate_label_generation'	  => array(
				'title'		   => __( 'Generate Shipping Labels Automatically After Order Received', 'ups-woocommerce-shipping' ),
				'label'			  => __( 'Enable', 'ups-woocommerce-shipping' ),			
				'description'	 => __( 'This will generate shipping labels automatically after order is received and payment is successful', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'class'			=> 'ph_ups_label_tab'
			),
			'default_dom_service' => array(
				'title'		   => __( 'Default service for domestic', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Default service for domestic label. This will consider if no UPS services selected from frond end while placing the order', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'select',
				'default'		 => '',
				'class'		   => 'wc-enhanced-select ph_ups_label_tab',
				'options'		  => array(
					null => __( 'Select', 'ups-woocommerce-shipping' )
				) + $this->services + $this->freight_services,
			),
			'default_int_service'	=> array(
				'title'		   => __( 'Default service for International', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Default service for International label. This will consider if no UPS services selected from frond end while placing the order', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'select',
				'class'		   => 'wc-enhanced-select ph_ups_label_tab',
				'default'		 => '',
				'options'		  => array(
					null => __( 'Select', 'ups-woocommerce-shipping' )
				) + $this->services + $this->freight_services,
			),
			'allow_label_btn_on_myaccount'	  => array(
				'title'		   => __( 'Allow customers to print shipping label from their <br/>My Account->Orders page', 'ups-woocommerce-shipping' ),
				'label'			  => __( 'Enable', 'ups-woocommerce-shipping' ),			
				'description'	 => __( 'A button will be available for downloading the label and printing', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'class'			=> 'ph_ups_label_tab'
			),
			'import_control_settings'  => array(
				'title'			=> __( 'UPS Import Control', 'ups-woocommerce-shipping' ),
				'label'		   => __( 'Enable<br/><small><i>If you enable this option then shipment will be considered as import control shipment. For more details:<a href="https://www.ups.com/us/en/services/shipping/import-control.page" target="_blank" >  UPS Import Control℠</a></i></small>', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'UPS Import Control allows you, as the importer, to initiate UPS shipments from another country and have those shipments delivered to your business or to an alternate location.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_label_tab qqq',
			),
			'carbonneutral_indicator' => array(
				'title'			=> __( 'UPS Carbon Neutral', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_label_tab'
			),
			'remove_special_char_product' => array(
				'title' 		=> __('Remove Special Characters from Product Name','ups-woocommerce-shipping'),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_label_tab',
				'description'	=> __('While passing product details for Commercial Invoice, remove special characters from product name.','ups-woocommerce-shipping'),
				'desc_tip'		=> true,
			),
			'label_description'	=> array(
				'title'		   => __( 'Shipment Description For UPS Label or Commercial Invoice', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Select how you want the shipment description on the UPS Shipping Label or Commercial Invoice. Choose from <br>1. Product Name <br>2. Product Category <br>3. Custom Description', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'select',
				'class'		   => 'wc-enhanced-select ph_ups_label_tab',
				'options'		  => array(
					'product_name' 			=> __( 'Product Name', 'ups-woocommerce-shipping' ),
					'product_desc' 			=> __( 'Product Description (UPS Shipping Details)', 'ups-woocommerce-shipping' ),
					'product_category'		=> __( 'Product Category', 'ups-woocommerce-shipping' ),
					'custom_description' 	=> __( 'Custom Description', 'ups-woocommerce-shipping' ),
				),
			),
			'label_custom_description'	=> array(
				'title'		   	=> __( 'Custom Description', 'ups-woocommerce-shipping' ),
				'type'			=> 'textarea',
				'css' 			=>	'width: 400px;',
				'description'	=>	__( 'Enter character with length from 1 to 50 characters. If the shipment is from US to US or PR to PR maximum character limit is 35.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=>	'ph_label_custom_description ph_ups_label_tab'
			),
			'include_order_id' => array(
				'label'		   => __( 'Include Order Id in Shipment Description', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'class'		=> 'ph_ups_label_tab'
			),
			'add_product_sku' => array(
				'label'			=> __( 'Add Product SKU in Shipping Label<br/><small>For US Domestic Shipments only Product SKU will be added</small>', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_label_tab'
			),
			'include_in_commercial_invoice' => array(
				'label'		   => __( 'Include Shipment Description for Commercial Invoice as well.<br/><small><i>If disabled, Commercial Invoice will have Product Name as Default Description.</i></small>', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'class'		=> 'ph_ups_label_tab'
			),

			

			// Send Label via Email
			'auto_email_label'	=> array(
				'title'				=> __( 'Send Shipping Label via Email', 'ups-woocommerce-shipping' ),
				'type'				=> 'multiselect',
				'class'				=> 'chosen_select ph_ups_label_tab',
				'default'			=> '',
				'options'			=> apply_filters( 'ph_ups_option_for_automatic_label_recipient', array(
					'shipper' 			=> 'To Shipper',
					'recipient'			=> 'To Recipient',
				)),
			),
			'email_subject'	  => array(
				'title'		   => __( 'Email Subject', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Subject of Email sent for UPS Label. Supported Tags : [ORDER_NO] - Order Number.', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'text',
				'placeholder'	=>	__( 'Shipment Label For Your Order', 'ups-woocommerce-shipping' ).' [ORDER_NO]',
				'class'			=>	'ph_ups_email_label_settings ph_ups_label_tab'
			),
			'email_content'	=> array(
				'title'		   	=> __( 'Content of Email With Label', 'ups-woocommerce-shipping' ),
				'type'			=> 'textarea',
				'placeholder'	=> "<html><body>
				<div>Please Download the label</div>
				<a href='[DOWNLOAD LINK]' ><input type='button' value='Download the label here' /> </a>
				</body></html>",
				'default'		=> '',
				'css' 			=>		'width:70%;height: 150px;',
				'description'	=>	__( 'Define your own email html here. Use the place holder tag [DOWNLOAD LINK] to get the label dowload link.<br />Supported Tags - <br />[DOWNLOAD LINK] - Label Link. <br />[ORDER NO] - Get order number. <br />[ORDER AMOUNT] - Order total Cost. <br />[PRODUCTS ID] - Comma seperated product ids in label. <br />[PRODUCTS SKU] - Comma seperated product sku in label. <br />[PRODUCTS NAME] - Comma seperated products name in label. <br />[PRODUCTS QUANTITY] - Comma seperated product quantities in label. <br />[ALL_PRODUCT INFO] - Product info in label in table form. <br />[ORDER_PRODUCTS] - Product info of order in table form.<br />[CUSTOMER EMAIL]- Customer contact Email ID. <br />[CUSTOMER NAME] - Customer Name.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_email_label_settings ph_ups_label_tab'
			),

			// International Forms

			'int-forms-title'=> array(
				'title'			=> __( 'UPS International Forms Settings', 'ups-woocommerce-shipping' ),
				'type'			=> 'title',
				'class'			=> 'ph_ups_int_forms_tab',
				'description'	=> __( 'Configure the UPS International forms related settings like Commercial Invoice, NAFTA and EEI DATA', 'ups-woocommerce-shipping' ),
			),
			'commercial_invoice' => array(
				'title'		   => __( 'Commercial Invoice', 'ups-woocommerce-shipping' ),
				'label'		   => __( 'Enable', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'description'	 => __('On enabling this option will create commercial invoice. Applicable for International shipments only.', 'ups-woocommerce-shipping'),
				'class'			=> 'ph_ups_int_forms_tab'
			),
			// PDS-125
			'discounted_price' => array(
				'title'			=> __( 'Discounted Price in Commercial Invoice', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'class'			=> 'commercial_invoice_toggle ph_ups_int_forms_tab',
				'default'		=> 'yes',
				'desc_tip'		=> true,
				'description'	=> 'Enabling this option will display discounted product price (if any) in Commercial Invoice.'
			),
			// PDS-124
			'commercial_invoice_shipping' => array(
				'title'			=> __( 'Shipping Charges in Commercial Invoice', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'class'			=> 'commercial_invoice_toggle ph_ups_int_forms_tab',
				'default'		=> 'no',
				'desc_tip'		=> true,
				'description'	=> 'Enabling this option will display shipping charges (if any) in Commercial Invoice.'
			),
			'declaration_statement' => array(
				'title'		   => __( 'Declaration Statement', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'label'		   => __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'css'			  => 'width:1000px',
				'placeholder'	  => __('Example: I hereby certify that the goods covered by this shipment qualify as originating goods for purposes of preferential tariff treatment under the NAFTA.','ups-woocommerce-shipping'),
				'description'	 => __('This is an optional field for the legal explanation, used by Customs, for the delivering of this shipment. It must be identical to the set of declarations actually used by Customs.', 'ups-woocommerce-shipping'),
				'class'		=> 'ph_ups_int_forms_tab'
			),	
			'terms_of_shipment'	  => array(
				'title'		   => __( 'Terms of Sale (Incoterm)', 'ups-woocommerce-shipping' ),
				'type'			   => 'select',
				'default'			=> '',
				'class'				=>'wc-enhanced-select ph_ups_int_forms_tab',
				'options'			=> array(
					''	   		=> __( 'NONE', 	'ups-woocommerce-shipping' ),
					'CFR'	   	=> __( 'Cost and Freight', 	'ups-woocommerce-shipping' ),
					'CIF'	   	=> __( 'Cost Insurance and Freight', 	'ups-woocommerce-shipping' ),
					'CIP'		=> __( 'Carriage and Insurance Paid', 	'ups-woocommerce-shipping' ),
					'CPT'		=> __( 'Carriage Paid To', 	'ups-woocommerce-shipping' ),
					'DAF'		=> __( 'Delivered at Frontier', 	'ups-woocommerce-shipping' ),
					'DDP' 		=> __( 'Delivery Duty Paid', 	'ups-woocommerce-shipping' ),
					'DDU' 		=> __( 'Delivery Duty Unpaid', 	'ups-woocommerce-shipping' ),
					'DEQ' 		=> __( 'Delivered Ex Quay', 	'ups-woocommerce-shipping' ),
					'DES' 		=> __( 'Delivered Ex Ship', 	'ups-woocommerce-shipping' ),
					'EXW' 		=> __( 'Ex Works', 	'ups-woocommerce-shipping' ),
					'FAS' 		=> __( 'Free Alongside Ship', 	'ups-woocommerce-shipping' ),
					'FCA' 		=> __( 'Free Carrier', 	'ups-woocommerce-shipping' ),
					'FOB' 		=> __( 'Free On Board', 	'ups-woocommerce-shipping' ),
				),
				'description'	 => __( 'Indicates the rights to the seller from the buyer, internationally', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
			),		
			'reason_export'	  => array(
				'title'		   => __( 'Reason for Export', 'ups-woocommerce-shipping' ),
				'type'			   => 'select',
				'default'			=> 0,
				'class'				=>'wc-enhanced-select ph_ups_int_forms_tab',
				'options'			=> array(
					'none'	   	=> __( 'NONE', 	'ups-woocommerce-shipping' ),
					'SALE'	   	=> __( 'SALE', 	'ups-woocommerce-shipping' ),
					'GIFT'	   	=> __( 'GIFT', 	'ups-woocommerce-shipping' ),
					'SAMPLE'		=> __( 'SAMPLE', 	'ups-woocommerce-shipping' ),
					'RETURN'		=> __( 'RETURN', 	'ups-woocommerce-shipping' ),
					'REPAIR'		=> __( 'REPAIR', 	'ups-woocommerce-shipping' ),
					'INTERCOMPANYDATA'=> __( 'INTERCOMPANYDATA', 	'ups-woocommerce-shipping' ),
				),
				'description'	 => __( 'This may be required for customs purpose while shipping products to your customers, internationally', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
			),
			'return_reason_export'	  => array(
				'title'		=> __( 'Reason for Export Returns', 'ups-woocommerce-shipping' ),
				'type'		=> 'select',
				'default'	=> 'RETURN',
				'class'		=>'wc-enhanced-select ph_ups_int_forms_tab',
				'options'	=> array(
					'none' 				=> __( 'NONE', 	'ups-woocommerce-shipping' ),
					'SALE' 				=> __( 'SALE', 	'ups-woocommerce-shipping' ),
					'GIFT' 				=> __( 'GIFT', 	'ups-woocommerce-shipping' ),
					'SAMPLE'			=> __( 'SAMPLE', 	'ups-woocommerce-shipping' ),
					'RETURN'			=> __( 'RETURN', 	'ups-woocommerce-shipping' ),
					'REPAIR'			=> __( 'REPAIR', 	'ups-woocommerce-shipping' ),
					'INTERCOMPANYDATA'	=> __( 'INTERCOMPANYDATA', 	'ups-woocommerce-shipping' ),
				),
				'description' 	=> __( 'This may be required for customs purpose incase of return shipments.', 'ups-woocommerce-shipping' ),
				'desc_tip' 		=> true,
			),
			'edi_on_label' => array(
				'title' 		=> __('EDI on Shipping Labels','ups-woocommerce-shipping'),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'class'			=> 'ph_ups_int_forms_tab',
				'description'	=> __('Enable this option when Shipper does not intend on supplying other self-prepared International Forms (EEI, CO, NAFTACO) to accompany the shipment.','ups-woocommerce-shipping'),
				'desc_tip'		=> true,
			),
			'nafta_co_form' => array(
				'title' 		=> __( 'NAFTA Certificate', 'ups-woocommerce-shipping' ),
				'label'		   => __( 'Enable', 'ups-woocommerce-shipping' ),
				'desc_tip' 		=> true,
				'type' 			=> 'checkbox',
				'description' 	=> __('Enable this option to create NORTH AMERICAN FREE TRADE AGREEMENT CERTIFICATE OF ORIGIN. Applicable for International shimpents only.', 'ups-woocommerce-shipping'),
				'class'			=> 'ph_ups_int_forms_tab'
			),
			'nafta_producer_option' => array(
				'title' 		=> __( 'NAFTA Producer Option', 'ups-woocommerce-shipping' ),
				'desc_tip' 		=> true,
				'type' 			=> 'select',
				'options'		=>	array(
					'01'	=> __( '01', 'ups-woocommerce-shipping' ),
					'02'	=> __( '02', 'ups-woocommerce-shipping' ),
					'03'	=> __( '03', 'ups-woocommerce-shipping' ),
					'04'	=> __( '04', 'ups-woocommerce-shipping' ),
				),
				'default'		=> '02',
				'description' 	=> __('The text associated with the code will be printed in the producer section instead of producer contact information. <br/>  01 - AVAILABLE TO CUSTOMS UPON REQUEST <br/> 02 - SAME AS EXPORTER <br/> 03 - ATTACHED LIST <br/> 04 - UNKNOWN', 'ups-woocommerce-shipping'),
				'class'			=> 'ph_ups_int_forms_tab ph_ups_nafta_group'
			),
			'blanket_begin_period' => array(
				'title' 		=> __( 'Blanket Period Begin Date', 'ups-woocommerce-shipping' ),
				'label' 		=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'desc_tip' 		=> true,
				'type' 			=> 'date',
				'css'			=> 'width:400px',
				'description' 	=> __('Begin date of the blanket period. It is the date upon which the Certificate becomes applicable to the good covered by the blanket Certificate (it may be prior to the date of signing this Certificate)', 'ups-woocommerce-shipping'),
				'class'			=> 'ph_ups_int_forms_tab ph_ups_nafta_group'
			),
			'blanket_end_period' => array(
				'title' 		=> __( 'Blanket Period End Date', 'ups-woocommerce-shipping' ),
				'label' 		=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'desc_tip' 		=> true,
				'type' 			=> 'date',
				'css'			=> 'width:400px',
				'description' 	=> __('End Date of the blanket period. It is the date upon which the blanket period expires', 'ups-woocommerce-shipping'),
				'class'			=> 'ph_ups_int_forms_tab ph_ups_nafta_group'
			),
			'eei_data' => array(
				'title'			=> __( 'EEI DATA', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'type'			=> 'checkbox',
				'description'	=> __('Enable this option to create UPS EEI DATA. Applicable for International shimpents only.', 'ups-woocommerce-shipping'),
				'class'			=> 'ph_ups_int_forms_tab'
			),
			'eei_shipper_filed_option'  => array(
				'title'			=> __( 'Shipper Filed Option', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'class'			=> 'wc-enhanced-select ph_ups_eei_group ph_ups_int_forms_tab',
				'options'		=> array(
					'A'			=> __( 'A', 'ups-woocommerce-shipping' ),
					'B'			=> __( 'B', 'ups-woocommerce-shipping' ),
					'C'			=> __( 'C', 'ups-woocommerce-shipping' ),
				),
				'description'	=> __( " A - requires the ITN <br/> B - requires the Exemption Legend <br/> C - requires the post departure filing citation.", 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
			),
			'eei_pre_departure_itn_number'  => array(
				'title'			=> __( 'Pre Departure ITN Number', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'class'			=> 'ph_ups_eei_group eei_pre_departure_itn_number ph_ups_int_forms_tab',
				'description'	=> __( "Input for Shipper Filed option 'A'. The format is available from AESDirect website", 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
			),
			'eei_exemption_legend'  => array(
				'title'			=> __( 'Exemption Legend', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'class'			=> 'ph_ups_eei_group eei_exemption_legend ph_ups_int_forms_tab',
				'description'	=> __( "Input for Shipper Filed option 'B'", 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
			),
			'eei_mode_of_transport'  => array(
				'title'			=> __( 'Mode of Transport', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> 'Air',
				'class'			=> 'wc-enhanced-select ph_ups_eei_group ph_ups_int_forms_tab',
				'options'		=> array(
					'Air'							=> __( 'Air', 	'ups-woocommerce-shipping' ),
					'AirContainerized'				=> __( 'Air Containerized', 	'ups-woocommerce-shipping' ),
					'Auto'							=> __( 'Auto', 	'ups-woocommerce-shipping' ),
					'FixedTransportInstallations'	=> __( 'Fixed Transport Installations', 	'ups-woocommerce-shipping' ),
					'Mail'							=> __( 'Mail', 	'ups-woocommerce-shipping' ),
					'PassengerHandcarried'			=> __( 'Passenger Handcarried', 	'ups-woocommerce-shipping' ),
					'Pedestrian' 					=> __( 'Pedestrian', 	'ups-woocommerce-shipping' ),
					'Rail'							=> __( 'Rail', 	'ups-woocommerce-shipping' ),
					'Containerized'					=> __( 'Containerized', 	'ups-woocommerce-shipping' ),
					'Auto'							=> __( 'Auto', 	'ups-woocommerce-shipping' ),
					'FixedTransportInstallations'	=> __( 'Fixed Transport Installations', 	'ups-woocommerce-shipping' ),
					'RoadOther'						=> __( 'Road Other', 	'ups-woocommerce-shipping' ),
					'SeaBarge'						=> __( 'Sea Barge', 	'ups-woocommerce-shipping' ),
					'SeaContainerized'				=> __( 'Sea Containerized', 	'ups-woocommerce-shipping' ),
					'SeaNoncontainerized'			=> __( 'Sea Noncontainerized', 	'ups-woocommerce-shipping' ),
					'Truck'							=> __( 'Truck', 	'ups-woocommerce-shipping' ),
					'TruckContainerized'			=> __( 'Truck Containerized', 	'ups-woocommerce-shipping' ),
				),
				'description'	=> __( 'Mode of transport by which the goods are exported. Only 10 Characters can appear on the form. Anything greater than 10 characters will be truncated on the form.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
			),
			'eei_parties_to_transaction'  => array(
				'title'			=> __( 'Parties To Transaction', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		=> 'R',
				'class'			=> 'wc-enhanced-select ph_ups_eei_group ph_ups_int_forms_tab',
				'options'		=> array(
					'R'			=> __( 'Related', 	'ups-woocommerce-shipping' ),
					'N'			=> __( 'Non Related', 	'ups-woocommerce-shipping' ),
				),
				'description'	=> __( 'Use Related, if the parties to the transaction are related. A related party is an export from a U.S. businessperson or business to a foreign business or from a U.S. business to a foreign person or business where the person has at least 10 percent of the voting shares of the business during the fiscal year.', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
			),

			// Packaging

			'packaging-title'=> array(
				'title'			=> __( 'Package Settings', 'ups-woocommerce-shipping' ),
				'type'			=> 'title',
				'class'			=> 'ph_ups_packaging_tab',
				'description'	=> __( 'Choose the packing options suitable for your store', 'ups-woocommerce-shipping' ),
			),
			'packing_method'	  => array(
				'title'		   => __( 'Parcel Packing', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		 => 'weight_based',
				'class'		   => 'packing_method wc-enhanced-select ph_ups_packaging_tab',
				'options'		 => array(
					'per_item'	=> __( 'Default: Pack items individually', 'ups-woocommerce-shipping' ),
					'box_packing' => __( 'Recommended: Pack into boxes with weights and dimensions', 'ups-woocommerce-shipping' ),
					'weight_based'=> __( 'Weight based: Calculate shipping on the basis of order total weight', 'ups-woocommerce-shipping' ),
				),
			),
			'packing_algorithm'  			=> array(
				'title'		   			=> __( 'Packing Algorithm', 'ups-woocommerce-shipping' ),
				'type'					=> 'select',
				'default'		 		=> 'volume_based',
				'class'		   			=> 'xa_ups_box_packing wc-enhanced-select ph_ups_packaging_tab',
				'options'		 		=> array(
					'volume_based'	   	=> __( 'Default: Volume Based Packing', 'ups-woocommerce-shipping' ),
					'stack_first'		=> __( 'Stack First Packing', 'ups-woocommerce-shipping' ),
					'new_algorithm'		=> __( 'New Algorithm(Based on Volume Used * Item Count)', 'ups-woocommerce-shipping' ),	
				),
			),
			'exclude_box_weight'	=> array(
				'title'   			=> __( 'Exclude Box Weight', 'ups-woocommerce-shipping' ),
				'type'				=> 'checkbox',
				'class'				=> 'xa_ups_box_packing exclude_box_weight ph_ups_packaging_tab',
				'label'				=> __( 'Enabling this option will not include Box Weight', 'ups-woocommerce-shipping' ),
				'default' 			=> 'no',
			),
			'stack_to_volume'	=> array(
				'title'   			=> __( 'Convert Stack First to Volume Based', 'ups-woocommerce-shipping' ),
				'type'				=> 'checkbox',
				'class'				=> 'xa_ups_box_packing stack_to_volume ph_ups_packaging_tab',
				'label'				=> __( 'Automatically change packing method when the products are packed in a box and the filled up space is less less than 44% of the box volume', 'ups-woocommerce-shipping' ),
				'default' 			=> 'yes',
			),

			'volumetric_weight'	=> array(
				'title'   			=> __( 'Enable Volumetric weight', 'ups-woocommerce-shipping' ),
				'type'				=> 'checkbox',
				'class'				=> 'weight_based_option ph_ups_packaging_tab',
				'label'				=> __( 'This option will calculate the volumetric weight. Then a comparison is made on the total weight of cart to the volumetric weight.</br>The higher weight of the two will be sent in the request.', 'ups-woocommerce-shipping' ),
				'default' 			=> 'no',
			),

			'box_max_weight'		   => array(
				'title'		   => __( 'Max Package Weight', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'default'		 => '10',
				'class'		   => 'weight_based_option ph_ups_packaging_tab',
				'desc_tip'	=> true,
				'description'	 => __( 'Maximum weight allowed for single box.', 'ups-woocommerce-shipping' ),
			),
			'weight_packing_process'   => array(
				'title'		   => __( 'Packing Process', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'default'		 => '',
				'class'		   => 'weight_based_option wc-enhanced-select ph_ups_packaging_tab',
				'options'		 => array(
					'pack_descending'	   => __( 'Pack heavier items first', 'ups-woocommerce-shipping' ),
					'pack_ascending'		=> __( 'Pack lighter items first.', 'ups-woocommerce-shipping' ),
					'pack_simple'			=> __( 'Pack purely divided by weight.', 'ups-woocommerce-shipping' ),
				),
				'desc_tip'	=> true,
				'description'	 => __( 'Select your packing order.', 'ups-woocommerce-shipping' ),
			),
			'ups_packaging'	   => array(
				'title'		   => __( 'UPS Packaging', 'ups-woocommerce-shipping' ),
				'type'			=> 'multiselect',
				'description'	  => __( 'UPS standard packaging options', 'ups-woocommerce-shipping' ),
				'default'		 => array(),
				'css'			  => 'width: 450px;',
				'class'		   => 'xa_ups_box_packing ups_packaging chosen_select ph_ups_packaging_tab',
				'options'		 => $this->packaging_select,
				'desc_tip'		=> true
			),
			'boxes'  => array(
				'type'			=> 'box_packing'
			),

			// Freight 

			'freight-title'=> array(
				'title'			=> __( 'Freight Settings', 'ups-woocommerce-shipping' ),
				'type'			=> 'title',
				'class'			=> 'ph_ups_freight_tab',
				'description'	=> __( 'Configure the UPS Freight related settings', 'ups-woocommerce-shipping' ),
			),
			'enable_freight'=> array(
				'title'			=> __( 'Freight Services', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable Freight Services	', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_payment'		=> array(
				'title'			=> __( 'Payment Information', 'ups-woocommerce-shipping' ),
				'type'			=> 'select',
				'desc_tip'		=> true,
				'description'	=> __( 'Choose Freight Billing Option', 'ups-woocommerce-shipping' ),
				'default'		=> '10',
				'class'			=> 'ph_ups_freight_payment wc-enhanced-select ph_ups_freight_tab',
				'options'		=> array(
					'10'		=> __( 'Prepaid', 'ups-woocommerce-shipping' ),
					'30'		=> __( 'Bill to Third Party', 'ups-woocommerce-shipping' ),
				),
			),
			'freight_thirdparty_contact_name'  => array(
				'title'			=> __( 'Contact Name', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Third Party Contact Name', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_freight_tab ph_ups_freight_third_party_billing'
			),
			'freight_thirdparty_addressline'  => array(
				'title'			=> __( 'Address Line 1', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Third Party Address Line 1', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_freight_tab ph_ups_freight_third_party_billing'
			),
			'freight_thirdparty_addressline_2'  => array(
				'title'			=> __( 'Address Line 2', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Third Party Address Line 2', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_freight_tab ph_ups_freight_third_party_billing'
			),
			'freight_thirdparty_city'	  	  => array(
				'title'			=> __( 'City', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Third Party City', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_freight_tab ph_ups_freight_third_party_billing'
			),
			'freight_thirdparty_country_state'	=> array(
				'type'			=> 'freight_thirdparty_country_state',
			),
			'freight_thirdparty_custom_state'		=> array(
				'title'			=> __( 'State Code', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Specify Third Party State Province Code if state not listed with Third Party Country.', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_freight_tab ph_ups_freight_third_party_billing'
			),
			'freight_thirdparty_postcode'	 => array(
				'title'			=> __( 'Postcode', 'ups-woocommerce-shipping' ),
				'type'			=> 'text',
				'description'	=> __( 'Third Party Zip/Postcode.', 'ups-woocommerce-shipping' ),
				'default'		=> '',
				'desc_tip'		=> true,
				'class'			=>	'ph_ups_freight_tab ph_ups_freight_third_party_billing'
			),
			'freight_class'		=>	array(
				'title'				=>	__( "Freight Class", "ups-woocommerce-shipping" ),
				"type"				=>	"select",
				"default"			=>	"50",
				"options"			=>	array(
					"50"		=>	"Class 50",	// Fits on standard shrink-wrapped 4X4 pallet, very durable, Ref link - http://www.fmlfreight.com/freight-101/freight-classes/
					"55"		=>	"Class 55",			// Bricks, cement, mortar, hardwood flooring
					"60"		=>	"Class 60",			// Car accessories & car parts
					"65"		=>	"Class 65",			// Car accessories & car parts, bottled beverages, books in boxes
					"70"		=>	"Class 70",			// Car accessories & car parts, food items, automobile engines	
					"77.5"		=>	"Class 77.5",		// Tires, bathroom fixtures	
					"85"		=>	"Class 85",			// Crated machinery, cast iron stoves
					"92.5"		=>	"Class 92.5",		// Computers, monitors, refrigerators
					"100"		=>	"Class 100",		// boat covers, car covers, canvas, wine cases, caskets	
					"110"		=>	"Class 110",		// cabinets, framed artwork, table saw	
					"125"		=>	"Class 125",		// Small Household appliances	
					"150"		=>	"Class 150",		// Auto sheet metal parts, bookcases,	
					"175"		=>	"Class 175",		// Clothing, couches stuffed furniture	
					"200"		=>	"Class 200",		// Auto sheet metal parts, aircraft parts, aluminum table, packaged mattresses,	
					"250"		=>	"Class 250",		// Bamboo furniture, mattress and box spring, plasma TV	
					"300"		=>	"Class 300",		// wood cabinets, tables, chairs setup, model boats	
					"400"		=>	"Class 400",		// Deer antlers	
					"500"		=>	"Class 500"			// Bags of gold dust, ping pong balls	
				),
				"description"		=>	__( "50 - Fits on standard shrink-wrapped 4X4 pallet, very durable (Lowest Cost).<br>55 - Bricks, cement, mortar, hardwood flooring.<br>60 - Car accessories & car parts.<br>65 - Car accessories & car parts, bottled beverages, books in boxes.<br>70 - Car accessories & car parts, food items, automobile engines.<br>77.5 - Tires, bathroom fixtures.<br>85 - Crated machinery, cast iron stoves.<br>92.5 - Computers, monitors, refrigerators.<br>100 - boat covers, car covers, canvas, wine cases, caskets.<br>110 - cabinets, framed artwork, table saw.<br>125 - Small Household appliances.<br>150 - Auto sheet metal parts, bookcases.<br>175 - Clothing, couches stuffed furniture.<br>200 - Auto sheet metal parts, aircraft parts, aluminum table, packaged mattresses.<br>250 - Bamboo furniture, mattress and box spring, plasma TV.<br>300 - wood cabinets, tables, chairs setup, model boats.<br>400 - Deer antlers.<br>500 - Bags of gold dust, ping pong balls (Highest Cost).", "ups-woocommerce-shipping" ),
				"desc_tip"			=>	true,
				'class'			=> 'ph_ups_freight_tab'
			),
			'freight_packaging_type'		=>	array(
				'title'			=>	__( "Freight Packaging Type", "ups-woocommerce-shipping" ),
				"type"			=>	"select",
				"default"		=>	"PLT",
				"options"		=>	array(
					"BAG"	=> "Bag",
					"BAL"	=> "Bale",
					"BAR"	=> "Barrel",
					"BDL"	=> "Bundle",
					"BIN"	=> "Bin",
					"BOX"	=> "Box",
					"BSK"	=> "Basket",
					"BUN"	=> "Bunch",
					"CAB"	=> "Cabinet",
					"CAN"	=> "Can",
					"CAR"	=> "Carrier",
					"CAS"	=> "Case",
					"CBY"	=> "Carboy",
					"CON"	=> "Container",
					"CRT"	=> "Crate",
					"CSK"	=> "Cask",
					"CTN"	=> "Carton",
					"CYL"	=> "Cylinder",
					"DRM"	=> "Drum",
					"LOO"	=> "Loose",
					"OTH"	=> "Other",
					"PAL"	=> "Pail",
					"PCS"	=> "Pieces",
					"PKG"	=> "Package",
					"PLN"	=> "Pipe Line",
					"PLT"	=> "Pallet",
					"RCK"	=> "Rack",
					"REL"	=> "Reel",
					"ROL"	=> "Roll",
					"SKD"	=> "Skid",
					"SPL"	=> "Spool",
					"TBE"	=> "Tube",
					"TNK"	=> "Tank",
					"UNT"	=> "Unit",
					"VPK"	=> "Van Pack",
					"WRP"	=> "Wrapped",
				),
				"description"	=>	__( "The UPS packaging type associated with the shipment. ", "ups-woocommerce-shipping" ),
				"desc_tip"		=>	true,
				'class'			=> 'ph_ups_freight_tab'
			),
			'freight_holiday_pickup' 	=> array(
				'title'			=> __( 'Freight Holiday Pickup', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate if the shipment requires a holiday pickup', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_inside_pickup' 	=> array(
				'title'			=> __( 'Freight Inside Pickup', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate if the shipment requires a inside pickup', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_residential_pickup' 	=> array(
				'title'			=> __( 'Freight Residential Pickup', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate if the shipment requires a residential pickup', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_weekend_pickup' 	=> array(
				'title'			=> __( 'Freight Weekend Pickup', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate if the shipment requires a weekend pickup', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_liftgate_pickup' 	=> array(
				'title'			=> __( 'Freight Lift Gate Pickup', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate if the shipment requires a lift gate pickup', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_limitedaccess_pickup' 	=> array(
				'title'			=> __( 'Freight Limited Access Pickup', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate if the shipment has limited access for pickup', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_holiday_delivery' 	=> array(
				'title'			=> __( 'Freight Holiday Delivery', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate that the shipment is going to be delivered on a holiday.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_inside_delivery' 	=> array(
				'title'			=> __( 'Freight Inside Delivery', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate that the shipment requires an inside delivery.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_call_before_delivery' 	=> array(
				'title'			=> __( 'Freight Call Before Delivery', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate that the shipment is going to be delivered after calling the consignee.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_weekend_delivery' 	=> array(
				'title'			=> __( 'Freight Weekend Delivery', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to  indicate that the shipment is going to be delivered on a weekend.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_liftgate_delivery' 	=> array(
				'title'			=> __( 'Freight Lift Gate Delivery', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate that the shipment requires a lift gate.', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_limitedaccess_delivery' 	=> array(
				'title'			=> __( 'Freight Limited Access Delivery', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable it to indicate that there is limited access for delivery', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_pickup_inst' 	=> array(
				'title'			=> __( 'Freight Pickup Instructions', 'ups-woocommerce-shipping' ),
				'type'			=> 'textarea',
				'css' 			=> 'width: 400px;',
				'description'	=> __( 'Pickup Instructions', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'freight_delivery_inst' => array(
				'title'			=> __( 'Freight Delivery Instructions', 'ups-woocommerce-shipping' ),
				'type'			=> 'textarea',
				'css' 			=> 'width: 400px;',
				'description'	=> __( 'Delivery Instructions', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'enable_density_based_rating'		=> array(
				'title'			=> __( 'Density Based Rating (DBR)', 'ups-woocommerce-shipping' ),
				'label'			=> __( 'Enable', 'ups-woocommerce-shipping' ),
				'type'			=> 'checkbox',
				'default'		=> 'no',
				'description'	=> __( 'Enable this option if Density Based Rating is enabled for your UPS Account', 'ups-woocommerce-shipping' ),
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'density_description'	=> array(
				'type'			=> 'title',
				'class'			=>'density_description ph_ups_freight_tab',
				'description'	=> __( 'Enter Freight Package Dimensions if Weight Based Packing Method is enabled. By default the plugin will take dimensions as 47x47x47 IN or 119x119x119 CM', 'ups-woocommerce-shipping' )
			),
			'density_length'	=> array(
				'title'		   	=> __( 'Length(DBR)', 'ups-woocommerce-shipping' ),
				'type'			=> 'number',
				'description'	=> 'Length',
				'placeholder'	=> '47 IN / 119 CM',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'density_width'	=> array(
				'title'		   	=> __( 'Width(DBR)', 'ups-woocommerce-shipping' ),
				'type'			=> 'number',
				'description'	=> 'Width',
				'placeholder'	=> '47 IN / 119 CM',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),
			'density_height'	=> array(
				'title'		   	=> __( 'Height(DBR)', 'ups-woocommerce-shipping' ),
				'type'			=> 'number',
				'description'	=> 'Height',
				'placeholder'	=> '47 IN / 119 CM',
				'desc_tip'		=> true,
				'class'			=> 'ph_ups_freight_tab',
			),

			// Pickup

			'pickup-title'  => array(
				'title'			=> __( 'UPS Pickup Settings', 'ups-woocommerce-shipping' ),
				'type'			=> 'title',
				'description'	=> __( 'Configure the UPS Pickup related settings', 'ups-woocommerce-shipping' ),
				'class'			=> 'ph_ups_pickup_tab',
			),	
			'pickup_enabled'	  => array(
				'title'		   => __( 'Enable Pickup', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Enable this to setup pickup request', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'class'			=> 'ph_ups_pickup_tab'
			),
			'pickup_start_time'		   => array(
				'title'		   => __( 'Pickup Start Time', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Items will be ready for pickup by this time from shop', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'select',
				'class'			  => 'wf_ups_pickup_grp wc-enhanced-select ph_ups_pickup_tab',
				'default'		 => 8,
				'options'		  => $pickup_start_time_options,
			),
			'pickup_close_time'		   => array(
				'title'		   => __( 'Company Close Time', 'ups-woocommerce-shipping' ),
				'description'	 => __( 'Your shop closing time. It must be greater than company open time', 'ups-woocommerce-shipping' ),
				'desc_tip'		   => true,
				'type'			=> 'select',
				'class'			  => 'wf_ups_pickup_grp wc-enhanced-select ph_ups_pickup_tab',
				'default'		 => 18,
				'options'		  => $pickup_close_time_options,
			),
			'pickup_date'		   => array(
				'title'			  => __( 'Pick up date', 'ups-woocommerce-shipping' ),
				'type'			   => 'select',
				'desc_tip'		   => true,
				'description'	 => __( 'Default option will pick current date. Choose \'Select working days\' to configure working days', 'ups-woocommerce-shipping' ),
				'default'			=> 'current',
				'class'			  => 'wf_ups_pickup_grp wc-enhanced-select ph_ups_pickup_tab',
				'options'			=> array(
					'current'			=> __( 'Default', 'ups-woocommerce-shipping' ),
					'specific'	   => __( 'Select working days', 'ups-woocommerce-shipping' ),
				),
			),
			'working_days'			  => array(
				'title'			  => __( 'Select working days', 'ups-woocommerce-shipping' ),
				'type'			   => 'multiselect',
				'desc_tip'		   => true,
				'description'	 => __( 'Select working days here. Selected days will be used for pickup' ),
				'class'			  => 'wf_ups_pickup_grp pickup_working_days chosen_select ph_ups_pickup_tab',
				'css'				=> 'width: 450px;',
				'default'			=> array('Mon', 'Tue', 'Wed', 'Thu', 'Fri'),
				'options'			=> array( 'Sun'=>'Sunday', 'Mon'=>'Monday','Tue'=>'Tuesday', 'Wed'=>'Wednesday', 'Thu'=>'Thursday', 'Fri'=>'Friday', 'Sat'=>'Saturday'),
			),

			// Help & Support

			'help_and_support'  => array(
				'type'			=> 'help_support_section'
			),
		);
	}
	
	/**
	 * See if method is available based on the package and cart.
	 *
	 * @param array $package Shipping package.
	 * @return bool
	 */
	 
	public function is_available( $package ) {
		
		if ( "no" === $this->enabled ) {

			if( $this->debug ) {
				
				$this->diagnostic_report( 'UPS : Realtime Rates is not enabled' );
			}

			return false;
		}
		
		if ( 'specific' === $this->availability ) {
			if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {

				if( $this->debug ) {
					
					$this->diagnostic_report( 'UPS : Method Availability for Specific Countries - '. print_r($this->countries,1) );
					$this->diagnostic_report( 'UPS : Checking for Destination - '. $package['destination']['country'].' Rate Calculation Aborted.' );
				}

				return false;
			}
		} elseif ( 'excluding' === $this->availability ) {
			if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
				return false;
			}
		}
		
		$has_met_min_amount = false;
		
		if(!method_exists(WC()->cart, 'get_displayed_subtotal')){// WC version below 2.6
			$total = WC()->cart->subtotal;
		}else{
			$total = WC()->cart->get_displayed_subtotal();

			if( version_compare( WC()->version, '4.4', '<' ) ) {
				$tax_display 	= WC()->cart->tax_display_cart;
			} else {
				$tax_display 	= WC()->cart->get_tax_price_display_mode();
			}
			
			if ( 'incl' === $tax_display ) {
				$total = $total - ( WC()->cart->get_cart_discount_total() + WC()->cart->get_cart_discount_tax_total() );
			} else {
				$total = $total - WC()->cart->get_cart_discount_total();
			}
		}
		if( $total < 0 )
		{		
			$total = 0;
		}
		if ( $total >= $this->min_amount ) {
			$has_met_min_amount = true;
		}
		$is_available	=	$has_met_min_amount;
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
	}

	/**
	 * calculate_shipping function.
	 *
	 * @access public
	 * @param mixed $package
	 * @return void
	 */
	public function calculate_shipping( $package=array() ) {
		global $woocommerce;

		// Address Validation applicable for US and PR
		if( $this->address_validation && in_array( $package['destination']['country'], array( 'US', 'PR' ) ) && ! is_admin() && $this->residential!="yes" ) {
			
			require_once 'class-ph-ups-address-validation.php';

			$Ph_Ups_Address_Validation 	= new Ph_Ups_Address_Validation( $package['destination'], $this->settings );
			$residential_code			= $Ph_Ups_Address_Validation->residential_check;

			// To get Address Validation Result Outside
			$residential_code 	= apply_filters( 'ph_ups_address_validation_result', $residential_code, $package['destination'], $this->settings );

			if( $residential_code == 2 ) {
				$this->residential = true;
			}
		}

		$this->ph_ups_selected_access_point_details = ! empty($package['ph_ups_selected_access_point_details']) ? $package['ph_ups_selected_access_point_details'] : null;
		libxml_use_internal_errors( true );

		// Only return rates if the package has a destination including country, postcode
		//if ( ( '' ==$package['destination']['country'] ) || ( ''==$package['destination']['postcode'] ) ) {
		if ( '' == $package['destination']['country'] ) {
			//$this->debug( __('UPS: Country, or Zip not yet supplied. Rates not requested.', 'ups-woocommerce-shipping') );
			$this->debug( __('UPS: Country not yet supplied. Rates not requested.', 'ups-woocommerce-shipping') );
			return; 
		}
		
		if( in_array( $package['destination']['country'] , $this->no_postcode_country_array ) ) {
			if ( empty( $package['destination']['city'] ) ) {
				$this->debug( __('UPS: City not yet supplied. Rates not requested.', 'ups-woocommerce-shipping') );
				return;
			}
		}
		else if( ''== $package['destination']['postcode'] ) {
			$this->debug( __('UPS: Zip not yet supplied. Rates not requested.', 'ups-woocommerce-shipping') );
			return;
		}
		// Turn off Insurance value if Cart subtotal is less than the specified amount in plugin settings
		if(isset($package['cart_subtotal']) && $package['cart_subtotal'] <= $this->min_order_amount_for_insurance ) {
			$this->insuredvalue = false;
		}

		// Skip Products
		if( ! empty($this->skip_products) ) {
			$package = $this->skip_products($package);
			if( empty($package['contents']) ) {
				return;
			}
		}

		if( ! empty($this->min_weight_limit) || ! empty($this->max_weight_limit) ) {
			$need_shipping = $this->check_min_weight_and_max_weight( $package, $this->min_weight_limit, $this->max_weight_limit );
			if( ! $need_shipping )	return;
		}

		// To Support Multi Vendor plugin
		$packages = apply_filters('wf_filter_package_address', array($package) , $this->ship_from_address );

		//Woocommerce packages after dividing the products based on vendor, if vendor plugin exist
		$wc_total_packages_count 	= count($packages);
		$package_rates 				= array();
		$allPackageRateCount 		= array();

		// $packageKey is to differentiate the multiple Cart Packages
		// Usecase: Multi Vendor with Split and Sum Method

		foreach( $packages as $packageKey => $package ) {

			// Reset Internal Rates Array after each Vendor Package Rate Calculation

			$rates = array();

			if( ( $this->origin_country != $package['destination']['country'] ) && $this->ups_tradability  ){

				$calculate_lc_query_request = $this->calculate_lc_query_request( $package );
				$lcr_response				= $this->get_lc_result( $calculate_lc_query_request, 'Landed Cost Query');

				if ( !empty($lcr_response) && isset($lcr_response->QueryResponse) && !empty($lcr_response->QueryResponse) ) {

					$transaction_digest 	= isset( $lcr_response->QueryResponse->TransactionDigest ) && !empty( $lcr_response->QueryResponse->TransactionDigest ) ? $lcr_response->QueryResponse->TransactionDigest : '';

					if( isset($transaction_digest) && !empty($transaction_digest) ){

						$calculate_lc_estimate_request	= $this->calculate_lc_estimate_request( $transaction_digest );
						$lcr_response					= $this->get_lc_result( $calculate_lc_estimate_request, 'Landed Cost Estimate');
					}
				}

				if ( !empty($lcr_response) && isset( $lcr_response->EstimateResponse ) && !empty( $lcr_response->EstimateResponse ) ) {

					$total_landed_cost  = $lcr_response->EstimateResponse->ShipmentEstimate->TotalLandedCost;

					if( WC() != null && WC()->session != null ){

						WC()->session->set('ph_ups_total_landed_cost', $total_landed_cost);
					}
				}
			} else {

				if( WC() != null && WC()->session != null ){

					WC()->session->set('ph_ups_total_landed_cost', '' );
				}
			}

			$package	= apply_filters( 'wf_customize_package_on_cart_and_checkout', $package );	// Customize the packages if cart contains bundled products
			// To pass the product info with rates meta data
			foreach( $package['contents'] as $product ) {
				$product_id = ! empty($product['variation_id']) ? $product['variation_id'] : $product['product_id'];
				$this->current_package_items_and_quantity[$product_id] = $product['quantity'];
			}

			$package_params	=	array();
			//US to US and PR, CA to CA , PR to US or PR are domestic remaining all pairs are international
			if( ( ($this->origin_country == $package['destination']['country']) && in_array( $this->origin_country, $this->dc_domestic_countries ) ) || ( ($this->origin_country == 'US' || $this->origin_country == 'PR') && ( $package['destination']['country'] == 'US' || $package['destination']['country'] == 'PR') ) ){
				$package_params['delivery_confirmation_applicable']	=	true;
			} else {
				$this->international_delivery_confirmation_applicable = true;
			}
			
			$package_requests		= $this->get_package_requests( $package, $package_params );
			$indexKey 				= 0;
			$maxIndex 				= 50;
			$packageCount 			= 0;
			$new_package_requests	= array();

			foreach ($package_requests as $key => $value) {

				$packageCount++;
				
				if( $packageCount <= $maxIndex ) {

					$new_package_requests[$indexKey][] = $value;

				} else {

					$packageCount = 1;
					$indexKey++;
					$new_package_requests[$indexKey][] = $value;
				}
			}

			$internal_package_count = !empty($new_package_requests) && is_array($new_package_requests) ? count($new_package_requests) : 0;
			$single_package 		= true;
		
			if ( !empty($new_package_requests) ) {

				foreach($new_package_requests as $key => $newPackageRequest) {

					// To get rate for services like ups ground, 3 day select etc.
					$rate_requests 	= $this->get_rate_requests( $newPackageRequest, $package );
					$rate_response 	= $this->process_result( $this->get_result($rate_requests, '', $key),'',$rate_requests );

					if ( ! empty($rate_response) ) {
						$rates[$key]['general'][] =  $rate_response;
					}
					// End of get rates for services like ups ground, 3 day select etc.

					//For Worldwide Express Freight Service
					if ( isset($this->custom_services[96]['enabled']) && $this->custom_services[96]['enabled'] ) {

						$rate_requests 		= $this->get_rate_requests( $newPackageRequest, $package, 'Pallet', 96 );
						$rates[$key][96][] 	= $this->process_result( $this->get_result($rate_requests, '', $key),'',$rate_requests );
					}

					// GFP request
					if ( isset($this->settings['services']['US48']['enabled']) && $this->settings['services']['US48']['enabled'] ) {

						if( $this->soap_available ) {

							$rate_requests	= $this->get_rate_requests_gfp( $newPackageRequest, $package);
							$rates[$key]['US48'][]	= $this->process_result_gfp( $this->get_result_gfp($rate_requests, 'UPS GFP', $key));

						} else {

							$this->debug( "UPS Ground with Freight Rate Request Failed. SoapClient is not enabled for your website. Ground with Freight Service requires SoapClient to be enabled to fetch rates. Contact your Hosting Provider to enable SoapClient and try again." );

							$this->diagnostic_report( "UPS Ground with Freight Rate Request Failed. SoapClient is not enabled for your website. Ground with Freight Service requires SoapClient to be enabled to fetch rates. Contact your Hosting Provider to enable SoapClient and try again." );
						}
					}

					// For Freight services 308, 309, 334, 349
					if ( $this->enable_freight ) {

						$freight_ups=new wf_freight_ups($this);

						foreach ($this->freight_services as $service_code => $value) {

							if ( ! empty($this->settings['services'][$service_code]['enabled']) ) {

								$this->debug( "UPS FREIGHT SERVICE START: $service_code" );

								$freight_rate 			= array();
								$cost 					= 0;
								$freight_rate_requests 	= $freight_ups->get_rate_request( $package, $service_code, $newPackageRequest );

								// Freight rate request
								// foreach( $package_requests as $package_key => $package_request) {
								// Freight rate response for individual packages request
								$freight_rate_response = $this->process_result( $this->get_result($freight_rate_requests, 'freight', $key), 'json' );

								if ( ! empty($freight_rate_response[WF_UPS_ID.":$service_code"]['cost']) ) {

									// Cost of freight packages till now processed for individual freight service
									$cost += $freight_rate_response[WF_UPS_ID.":$service_code"]['cost'];
									$freight_rate_response[WF_UPS_ID.":$service_code"]['cost'] = $cost;
									$freight_rate = $freight_rate_response;

								} else {
									// If no response comes for any packages then we won't show the response for that Freight service
									$freight_rate = array();
									$this->debug( "UPS FREIGHT SERVICE RESPONSE FAILED FOR SOME PACKAGES : $service_code" );
									break;
								}
								// }

								$this->debug( "UPS FREIGHT SERVICE END : $service_code" );

								// If rate comes for freight sevices then merge it in rates array
								if( ! empty($freight_rate) ) {
									$rates[$key][$service_code][] = $freight_rate;
								}
							}
						}
					}
					// End code for Freight services 308, 309, 334, 349

					$surepostPackageCount = !empty($newPackageRequest) && is_array($newPackageRequest) ? count($newPackageRequest) : 1;

					if ( $surepostPackageCount == 1 && $single_package ) {

						// Surepost, 1 is Commercial Address
						$surepost_check = 0;

						if( ! class_exists('Ph_Ups_Address_Validation') ) {
							require_once 'class-ph-ups-address-validation.php';
						}

						if ( isset($Ph_Ups_Address_Validation) && ( $Ph_Ups_Address_Validation instanceof Ph_Ups_Address_Validation ) ) {

							$surepost_check				= $Ph_Ups_Address_Validation->residential_check;

						} elseif ( class_exists('Ph_Ups_Address_Validation') && in_array( $package['destination']['country'], array( 'US', 'PR' ) ) ) {

							// Will check the address is Residential or not, SurePost only for residential
							$Ph_Ups_Address_Validation 	= new Ph_Ups_Address_Validation( $package['destination'], $this->settings );
							$surepost_check				= $Ph_Ups_Address_Validation->residential_check;
						}

						$surepost_check 	= apply_filters( 'ph_ups_update_surepost_address_validation_result', $surepost_check, $package['destination'], $package );

						if ( $surepost_check != 1 ) {

							foreach ( $this->ups_surepost_services as $service_code ) {

								if ( empty($this->custom_services[$service_code]['enabled']) || ( $this->custom_services[$service_code]['enabled'] != 1 ) ) {
									//It will be not set for European origin address
									continue;
								}

								$rate_requests			= $this->get_rate_requests( $newPackageRequest, $package, 'surepost', $service_code );
								$rate_response			= $this->process_result( $this->get_result($rate_requests, 'surepost', $key),'',$rate_requests );

								if( ! empty($rate_response) ) {

									$rates[$key][$service_code][]	= $rate_response;
								}
							}
						}
					} else {

						$single_package = false;

						$this->debug( "UPS SurePost Rate Request Aborted. SurePost allows only single-piece shipments. Request contains $surepostPackageCount Packages." );

						$this->diagnostic_report( "UPS SurePost Rate Request Aborted. SurePost allows only single-piece shipments. Request contains $surepostPackageCount Packages." );

					}
				}

				// Handle Rates for Internal Packages, Add Rates from all the Packages and build Final Rate Array
				// If any of the Internal Package missing any Shipping Rate, unset the Shipping Rate from the Final Rate Array
				// Usecase: More than 50 Packages generated (Packing Algorithm) from Cart Packages - Pack Item Indivisually with 120 Quantity of product.

				if( ! empty($rates) ) {

					foreach ($rates as $key => $value) {

						// $rate_type will be general, freight ( 308, 309, 334, 349 ), US48, 96, SurePost

						foreach ($value as $rate_type => $all_packages_rates ) {

							// Build Final Rate Array for each Cart Packages

							if ( !isset($package_rates[$rate_type]) ) {

								$package_rates[$rate_type] 				= array();

								// Add $packageKey in Final Rate Array to check rates are returned for all Cart Packages

								$package_rates[$rate_type][$packageKey] = array();
							}

							// Build Internal Package Rate Count for all the Services

							if ( !isset($allPackageRateCount[$rate_type]) ) {

								$allPackageRateCount[$rate_type] 				= array();

								// Add $packageKey in Internal Package Rate Count Array 
								// To check rates are returned for all the Internal Packages within a Cart Package

								$allPackageRateCount[$rate_type][$packageKey] 	= array();
							}

							$calculatedRates = current($all_packages_rates);

							foreach ( $calculatedRates as $ups_sevice => $package_rate ) {

								// Keep the Count of each UPS Shipping Service returned for all the Internal Packages

								if ( !isset($allPackageRateCount[$rate_type][$packageKey][$ups_sevice]) ) {

									$allPackageRateCount[$rate_type][$packageKey][$ups_sevice] = 1;
								}

								// If: Push the Shipping Rate array for the initial Internal Package to the Final Rate Array
								// Else: Add the Shipping Rate Cost to the Final Rate Array for each additional Internal Package
								// Increacse the Internal Package Rate Count as well

								if ( !isset($package_rates[$rate_type][$packageKey][$ups_sevice]) ) {

									$package_rates[$rate_type][$packageKey][$ups_sevice] = array();
									$package_rates[$rate_type][$packageKey][$ups_sevice] = $package_rate;
									
								} else {

									$package_rates[$rate_type][$packageKey][$ups_sevice]['cost'] = (float) $package_rates[$rate_type][$packageKey][$ups_sevice]['cost'] + (float) $package_rate['cost'];
									$allPackageRateCount[$rate_type][$packageKey][$ups_sevice]++;
								}
							}
						}
					}

					// If all the Internal Package Rates were not returned then Unset that Shipping Rate
					// This unsetting is only for Internal Package Rates of respectve Cart Packages

					if ( !empty($allPackageRateCount) ) {

						foreach ($allPackageRateCount as $rateType => $rateCount) {
							
							foreach ($rateCount[$packageKey] as $rateId => $count) {
								
								if( isset($package_rates[$rateType]) && isset($package_rates[$rateType][$packageKey]) && isset($package_rates[$rateType][$packageKey][$rateId]) && $internal_package_count != $count ) {

									$serviceName 	= $package_rates[$rateType][$packageKey][$rateId]['label'];

									$this->diagnostic_report( "$serviceName is removed from Shipping Rates. Total $internal_package_count Package Set(s) were requested for Rates. Rates returned only for $count Package Set(s). One Package Set contains maximum of 50 Packages." );

									unset($package_rates[$rateType][$packageKey][$rateId]);
								}
							}

						}
					}

				}
			}
		}
		
		$rates 		= $package_rates ;
		$all_rates 	= array();

		// Handle Rates for Multi Cart Packages, Check all cart packages returned rates.
		// Filter Common Shipping Methods and conbine the Shipping Cost and Display
		// Usecase: Multi Vendor with Split and Sum Method

		if ( ! empty($rates) ) {

			foreach ($rates as $rate_type => $all_packages_rates ) {

				// For every woocommerce package there must be response, so number of woocommerce package and UPS response must be equal

				if ( count($rates[$rate_type]) == $wc_total_packages_count ) {

					// UPS services keys in rate response

					$ups_found_services_keys = array_keys(current($all_packages_rates));
					
					foreach ( $ups_found_services_keys as $ups_sevice) {

						$count = 0;

						foreach ( $all_packages_rates as $package_rates ) {

							if( ! empty($package_rates[$ups_sevice] ) ) {

								if ( empty($all_rates[$ups_sevice]) ) {

									$all_rates[$ups_sevice] = $package_rates[$ups_sevice];
								
								} else {

									$all_rates[$ups_sevice]['cost'] = (float) $all_rates[$ups_sevice]['cost'] + (float) $package_rates[$ups_sevice]['cost'];
								}

								$count++;
							}
						}

						// If number of package requests not equal to number of response for any particular service

						if( $count != $wc_total_packages_count ) {
							unset($all_rates[$ups_sevice]);
						}
					}
				}
			}
		}

		$this->xa_add_rates($all_rates);
	}
	// End of Calculate Shipping function

	public function calculate_lc_query_request( $package ) {
		
     	//create soap request
		$action['RequestAction'] = 'LandedCost';
		$request['Request'] 	 = $action;
		$product 				 = [];

		$queryrequest = array
		(
			'Shipment' => array
			(
				'OriginCountryCode' 			=> $this->origin_country,
				'OriginStateProvinceCode' 		=> $this->origin_state,
				'DestinationCountryCode' 		=> $package['destination']['country'],
				'DestinationStateProvinceCode' 	=> $package['destination']['state'],
				'TransportationMode' 			=> '1',
				'ResultCurrencyCode' 			=> get_woocommerce_currency(),
			)
		);
		if( isset($package['contents']) && !empty($package['contents']) ) {

			foreach ( $package['contents'] as $product ) {

				$total_item_count   = $product['quantity'];
				$unit_price  		= $product['data']->get_price(); 
				$unit_weight 		= round(wc_get_weight( $product['data']->get_weight(), $this->weight_unit ,4) ); 
				$product_id 		= isset($product['variation_id']) && !empty($product['variation_id']) ? $product['variation_id'] : $product['product_id']; 
				$hst 				= get_post_meta( $product_id, '_ph_ups_hst_var', true );

				if ( empty($hst) ) {

                    $hst 	= get_post_meta( $product_id, '_wf_ups_hst', true );
				}

				$this->hst_lc[] 	= $hst;

				$queryrequest['Shipment']['Product'][] = array
				(
					'TariffInfo' => array
					(
						'TariffCode' => $hst,
					),

					'Quantity' => array
					(
						'Value' => $total_item_count
					),

					'UnitPrice' => array
					(
						'MonetaryValue' => $unit_price,
						'CurrencyCode'  => get_woocommerce_currency(),
					),
					'Weight' => array
					(
						'Value' 		=> $unit_weight,
						'UnitOfMeasure' => array
						(
							'UnitCode' => $this->uom
						)
					)
				);
			}
		}
		$request['QueryRequest'] = $queryrequest;
		return $request;
	}

	public function calculate_lc_estimate_request( $transaction_digest ) {

		$action['RequestAction'] = 'LandedCost';
		$request['Request'] 	 = $action;
		$estimaterequest 		 = [];
		$estimaterequest['Shipment']['Product'] = [];

		foreach ( $this->hst_lc as $hst ) {
			
			$estimaterequest['Shipment']['Product'][] = array
			(
				'TariffCode' => $hst,
			);
		}

		$estimaterequest['TransactionDigest'] 	= $transaction_digest;
		$request['EstimateRequest'] 			= $estimaterequest;
		return $request;
	}

	/**
	 * Skip the selected products in settings.
	 * @param array $package Cart Package.
	 * @param array
	 */
	public function skip_products( $package ) {
		$skipped_products = null;
		foreach( $package['contents'] as $line_item_key => $line_item ) {
			$line_item_shipping_class = $line_item['data']->get_shipping_class();
			if( in_array( $line_item_shipping_class, $this->skip_products ) ) {
				$skipped_products[] = ! empty($line_item['variation_id']) ? $line_item['variation_id'] : $line_item['product_id'];
				unset( $package['contents'][$line_item_key] );
			}
		}
		if( $this->debug && ! empty($skipped_products) ) {
			$skipped_products = implode( ', ', $skipped_products );
			$this->debug( __('UPS : Skipped Products Id - ', 'ups-woocommerce-shipping'). $skipped_products.' .' );
		}

		if( !empty($skipped_products) ) {

			$this->diagnostic_report( 'UPS : Skipped Product Id(s)' );
			$this->diagnostic_report( print_r( $skipped_products, 1) );
		}

		return $package;
	}

	/**
	 * Check for Order Minimum weight and Maximum weight.
	 * @param array $package Cart Package.
	 * @param float $min_weight_limit Minimum Weight.
	 * @param float $max_weight_limit Maximum Weight.
	 * @return boolean
	 */
	public function check_min_weight_and_max_weight( $package, $min_weight_limit= null, $max_weight_limit= null ) {
		$package_weight = 0;
		foreach( $package['contents'] as $line_item ) {

			$quantity 		 = isset($line_item['quantity']) ? $line_item['quantity'] : 1;
			$package_weight += (float) ( $line_item['data']->get_weight() * $quantity );
		}
		if( $package_weight < $min_weight_limit || ( ! empty($max_weight_limit) && $package_weight > $max_weight_limit ) ) {
			if( $this->debug ) {
				$this->debug( __('UPS Shipping Calculation Skipped - Package Weight is not in range of Minimum and Maximum Weight Limit (Check UPS Plugin Settings).', 'ups-woocommerce-shipping') );
			}

			// Add by default
			$this->diagnostic_report( 'UPS Shipping Calculation Skipped - Package Weight is not in range of Minimum and Maximum Weight Limit (Check UPS Plugin Settings)' );
			
			return false;
		}
		return true;
	}
	
	function xa_add_rates( $rates ){

		if ( !empty($rates) ) {
			$this->rate_conversion = apply_filters( 'xa_conversion_rate', $this->rate_conversion, ( isset($xml->RatedShipment[0]->TotalCharges->CurrencyCode) ? (string)$xml->RatedShipment[0]->TotalCharges->CurrencyCode : null ) );
			if( $this->rate_conversion ) {
				foreach ( $rates as $key => $rate ) {
					$rates[ $key ][ 'cost' ] = isset($rate[ 'cost' ]) ? $rate[ 'cost' ] * $this->rate_conversion : 0;
				}
			}

			if ( $this->offer_rates == 'all' ) {

				uasort( $rates, array( $this, 'sort_rates' ) );
				foreach ( $rates as $key => $rate ) {
					$this->add_rate( $rate );
				}

			} else {

				$cheapest_rate = '';

				foreach ( $rates as $key => $rate ) {
					if ( ! $cheapest_rate || ( $cheapest_rate['cost'] > $rate['cost'] && !empty($rate['cost']) ) )
						$cheapest_rate = $rate;
				}
				// If cheapest only without actual service name i.e Service name has to be override with method title
				if( ! empty($this->cheapest_rate_title) ) {
					$cheapest_rate['label'] = $this->cheapest_rate_title;
				}
				$this->add_rate( $cheapest_rate );
			}
		// Fallback
		} elseif ( $this->fallback ) {
			$this->add_rate( array(
				'id' 	=> $this->id . '_fallback',
				'label' => $this->title,
				'cost' 	=> $this->fallback,
				'sort'  => 0
			) );
			$this->debug( __('UPS: Using Fallback setting.', 'ups-woocommerce-shipping') );
		}
	}

	public function process_result( $ups_response, $type='',$package=array() )
	{
		//for freight response
		if( $type == 'json' ){
			$xml=json_decode($ups_response);
		}else{
			$xml = simplexml_load_string( preg_replace('/<\?xml.*\?>/','', $ups_response ) );
		}
		
		if ( ! $xml ) {
			$this->debug( __( 'Failed loading XML', 'ups-woocommerce-shipping' ), 'error' );
			return;
		}
		$rates = array();
		if ( ( property_exists($xml,'Response') && $xml->Response->ResponseStatusCode == 1)  || ( $type =='json' && !property_exists($xml,'Fault') ) ) {

			$xml = apply_filters('wf_ups_rate', $xml,$package);
			$xml_response = isset($xml->RatedShipment) ? $xml->RatedShipment : $xml;	// Normal rates : freight rates
			foreach ( $xml_response as $response ) {
				$code = (string)$response->Service->Code;

				if( ! empty( $this->custom_services[$code] ) && $this->custom_services[$code]['enabled'] != 1 ){		// For Freight service code custom services won't be set
					continue;
				}
										
				if(in_array("$code",array_keys($this->freight_services)) && property_exists($xml,'FreightRateResponse')){
					$service_name = $this->freight_services[$code];
						$rate_cost = (float) $xml->FreightRateResponse->TotalShipmentCharge->MonetaryValue;	
				}
				else{	
					$service_name = $this->services[ $code ];
					if ( $this->negotiated && isset( $response->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue ) ){
						if(property_exists($response->NegotiatedRates->NetSummaryCharges,'TotalChargesWithTaxes')){
							$rate_cost = (float) $response->NegotiatedRates->NetSummaryCharges->TotalChargesWithTaxes->MonetaryValue;
						}else{
							$rate_cost = (float) $response->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;
						}							
					}else{
						if(property_exists($response,'TotalChargesWithTaxes'))
						{
							$rate_cost = (float) $response->TotalChargesWithTaxes->MonetaryValue;
						}else{
							$rate_cost = (float) $response->TotalCharges->MonetaryValue;
						}
					}							
				}


				$rate_id	 = $this->id . ':' . $code;

				$rate_name   = $service_name ;

				// Name adjustment
				if ( ! empty( $this->custom_services[ $code ]['name'] ) )
					$rate_name = $this->custom_services[ $code ]['name'];

				// Cost adjustment %, don't apply on order page rates
				if ( ! empty( $this->custom_services[ $code ]['adjustment_percent'] ) && ! isset($_GET['wf_ups_generate_packages_rates']) )
					$rate_cost = $rate_cost + ( $rate_cost * ( floatval( $this->custom_services[ $code ]['adjustment_percent'] ) / 100 ) );
				// Cost adjustment, don't apply on order page rates
				if ( ! empty( $this->custom_services[ $code ]['adjustment'] ) && ! isset($_GET['wf_ups_generate_packages_rates']) )
					$rate_cost = $rate_cost + floatval( $this->custom_services[ $code ]['adjustment'] );

				// Sort
				if ( isset( $this->custom_services[ $code ]['order'] ) ) {
					$sort = $this->custom_services[ $code ]['order'];
				} else {
					$sort = 999;
				}

				$rates[ $rate_id ] = array(
					'id' 	=> $rate_id,
					'label' => $rate_name,
					'cost' 	=> $rate_cost,
					'sort'  => $sort,
					'meta_data'	=> array(
						'_xa_ups_method'	=>	array(
							'id'			=>	$rate_id,	// Rate id will be in format WF_UPS_ID:service_id ex for ground wf_shipping_ups:03
							'method_title'	=>	$rate_name,
							'items'			=>	isset($this->current_package_items_and_quantity) ? $this->current_package_items_and_quantity : array(),
						),
					)
				);

				// Set Estimated delivery in rates meta data
				if( $this->show_est_delivery ) {
					$estimated_delivery = null;
					// Estimated delivery for freight
					if( $type == 'json' && isset($response->TimeInTransit->DaysInTransit) ) {
						$days_in_transit 	= (string) $response->TimeInTransit->DaysInTransit;
						$current_time 		= clone $this->current_wp_time;
						if( ! empty($days_in_transit) )	$estimated_delivery = $current_time->modify("+$days_in_transit days");
					}// Estimated delivery for normal services
					elseif( ! empty($response->TimeInTransit->ServiceSummary->EstimatedArrival->Arrival) ) {
						$estimated_delivery_date = $response->TimeInTransit->ServiceSummary->EstimatedArrival->Arrival->Date; // Format YYYYMMDD, i.e Ymd
						$estimated_delivery_time = $response->TimeInTransit->ServiceSummary->EstimatedArrival->Arrival->Time; // Format His
						$estimated_delivery = date_create_from_format( "Ymj His", $estimated_delivery_date.' '.$estimated_delivery_time );
					}

					if( ! empty($estimated_delivery) ) {
						if( empty($this->wp_date_time_format) ) {
							$this->wp_date_time_format = Ph_UPS_Woo_Shipping_Common::get_wordpress_date_format().' '.Ph_UPS_Woo_Shipping_Common::get_wordpress_time_format();
						}
						
						$rates[ $rate_id ]['meta_data']['ups_delivery_time'] = apply_filters('ph_ups_estimated_delivery_customization',$estimated_delivery) ;
						if( $estimated_delivery instanceof DateTime) {

							if( !empty($this->show_est_delivery_in_woocommerce_blocks) && $this->show_est_delivery_in_woocommerce_blocks == 'yes' ){

								$rates[ $rate_id ]['label']	= apply_filters('ph_ups_estimated_delivery_html', $rates[ $rate_id ]['label'], $this->estimated_delivery_text, $estimated_delivery);
							}
							$rates[ $rate_id ]['meta_data']['Estimated Delivery'] = $estimated_delivery->format($this->wp_date_time_format);
						}
					}
				}
			} 
		}
		return $rates;
	}
	public function process_result_gfp( $gfp_response, $type='',$package=array() )
	{
		
		$rates = array();
		if(!empty($gfp_response)) {

			$gfp_response = isset($gfp_response->RatedShipment) ? $gfp_response->RatedShipment : '';	// Normal rates : freight rates
				$code = 'US48';
				$service_name = $this->services[ $code ];
				if ( $this->negotiated && isset( $gfp_response->NegotiatedRateCharges->TotalCharge->MonetaryValue ) ){
						$rate_cost = (float) $gfp_response->NegotiatedRateCharges->TotalCharge->MonetaryValue;
				}else{
					$rate_cost = (float) $gfp_response->TotalCharges->MonetaryValue;
				}	


				$rate_id	 = $this->id . ':' . $code;

				$rate_name   = $service_name ;
				// Name adjustment
				if ( ! empty( $this->custom_services[ $code ]['name'] ) )
					$rate_name = $this->custom_services[ $code ]['name'];

				// Cost adjustment %, don't apply on order page rates
				if ( ! empty( $this->custom_services[ $code ]['adjustment_percent'] ) && ! isset($_GET['wf_ups_generate_packages_rates']) )
					$rate_cost = $rate_cost + ( $rate_cost * ( floatval( $this->custom_services[ $code ]['adjustment_percent'] ) / 100 ) );
				// Cost adjustment, don't apply on order page rates
				if ( ! empty( $this->custom_services[ $code ]['adjustment'] ) && ! isset($_GET['wf_ups_generate_packages_rates']) )
					$rate_cost = $rate_cost + floatval( $this->custom_services[ $code ]['adjustment'] );

				// Sort
				if ( isset( $this->custom_services[ $code ]['order'] ) ) {
					$sort = $this->custom_services[ $code ]['order'];
				} else {
					$sort = 999;
				}

				$rates[ $rate_id ] = array(
					'id' 	=> $rate_id,
					'label' => $rate_name,
					'cost' 	=> $rate_cost,
					'sort'  => $sort,
					'meta_data'	=> array(
						'_xa_ups_method'	=>	array(
							'id'			=>	$rate_id,	// Rate id will be in format WF_UPS_ID:service_id ex for ground wf_shipping_ups:03
							'method_title'	=>	$rate_name,
							'items'			=>	isset($this->current_package_items_and_quantity) ? $this->current_package_items_and_quantity : array(),
						),
					)
				);
		}
		return $rates;
	}
	//function to get result for GFP
	public function get_result_gfp( $request, $request_type = '', $key = '' )
	{
		$ups_response = null;
		$key++;
		$ups_settings 				= get_option( 'woocommerce_'.WF_UPS_ID.'_settings', null ); 
		$api_mode	  		= isset( $ups_settings['api_mode'] ) ? $ups_settings['api_mode'] : 'Test';
		$header=new stdClass();
		$header->UsernameToken=new stdClass();
		$header->UsernameToken->Username=$this->user_id;
		$header->UsernameToken->Password=$this->password;
		$header->ServiceAccessToken=new stdClass();
		$header->ServiceAccessToken->AccessLicenseNumber=$this->access_key;
		$client = $this->wf_create_soap_client( plugin_dir_path( dirname( __FILE__ ) ) . 'wsdl/'.$api_mode.'/RateWS.wsdl', array(
			'trace' =>	true,
			// 'uri'      => "https://wwwcie.ups.com/webservices/Rate",
			// 'location'      => "https://wwwcie.ups.com/webservices/Rate",
			'cache_wsdl' => 0
			) );
		// $authvalues = new SoapVar($header, SOAP_ENC_OBJECT);
		$header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$header,false);
		
			$client->__setSoapHeaders($header);

		$client->__setSoapHeaders($header);
		try {

			$ups_response = $client->ProcessRate( $request['RateRequest'] );
			$request=$client->__getLastRequest();
			$response=$client->__getLastResponse();

		} catch (\SoapFault $fault) {
			
		}

		if( $this->debug ) {

			$this->debug( "UPS GFP REQUEST [ Package Set: ".$key." | Max Packages: 50 ] <pre>" . print_r( htmlspecialchars($client->__getLastRequest()) , true ) . '</pre>' );
			$this->debug( "UPS GFP RESPONSE [ Package Set: ".$key."	| Max Packages: 50 ] <pre>" . print_r( htmlspecialchars($client->__getLastResponse()) , true ) . '</pre>' );

			$this->diagnostic_report( '------------------------ UPS GFP REQUEST [ Package Set: ".$key." | Max Packages: 50 ] ------------------------' );
			$this->diagnostic_report( htmlspecialchars($client->__getLastRequest()) );
			$this->diagnostic_report( '------------------------ UPS GFP RESPONSE [ Package Set: ".$key." | Max Packages: 50 ] ------------------------' );
			$this->diagnostic_report( htmlspecialchars($client->__getLastResponse()) );
		}

		return $ups_response;
	}

	//Landed Cost Result
	public function get_lc_result($request,$request_type)
	{
		$ups_response 		= null;
		$exceptionMessage   = '';
		$ups_settings 		= get_option( 'woocommerce_'.WF_UPS_ID.'_settings', null ); 
		$api_mode	  		= isset( $ups_settings['api_mode'] ) ? $ups_settings['api_mode'] : 'Test';
		$header 			=new stdClass();
		$header->UserId 	=$this->user_id;
		$header->Password 	=$this->password;
		$header->AccessLicenseNumber=$this->access_key;

		$client = $this->wf_create_soap_client( plugin_dir_path( dirname( __FILE__ ) ) . 'wsdl/'.$api_mode.'/tradability/LandedCost.wsdl', array(
			'trace' 	 =>	1,
		    ) 
	 	);

		$header = new SoapHeader('http://www.ups.com/schema/xpci/1.0/auth','AccessRequest',$header,false);
		$client->__setSoapHeaders($header);

		try {
			$ups_response = $client->ProcessLCRequest( $request );

		} catch (\SoapFault $fault) {

            $exceptionMessage  = 'An exception has been raised as a result of client data.';

			if ( !empty($fault) && !empty($fault->detail) && !empty($fault->detail->Errors->ErrorDetail) && !empty($fault->detail->Errors->ErrorDetail->PrimaryErrorCode)){

				$exceptionMessage  = isset($fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description) && !empty($fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description) ? $fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description : 'An exception has been raised as a result of client data.'; 
			}

			if( WC() != null && WC()->session != null ){

				WC()->session->set('ph_ups_total_landed_cost', '');
			}
		}

		if( $this->debug ) {

			$this->diagnostic_report( '------------------------ UPS '.strtoupper($request_type).' REQUEST ------------------------' );
			$this->diagnostic_report( htmlspecialchars($client->__getLastRequest()) );
			$this->diagnostic_report( '------------------------ UPS '.strtoupper($request_type).' RESPONSE ------------------------' );
			$this->diagnostic_report( htmlspecialchars($client->__getLastResponse()) );

			if ( !empty( $exceptionMessage ) ) {

				$this->diagnostic_report( '------------------------ UPS '.strtoupper($request_type).' EXCEPTION MESSAGE ------------------------' );
				$this->diagnostic_report( htmlspecialchars( $exceptionMessage ) );
			}
		}
		return $ups_response;
	}

	public function get_result( $request, $request_type = '', $key = '' )
	{
		$ups_response = null;
		$send_request		   	= str_replace( array( "\n", "\r" ), '', $request );
		$transient			  	= 'ups_quote_' . md5( $request );
		$cached_response		= get_transient( $transient );
		$transient_time 		= ( (int) $this->rate_caching ) * 60 * 60;
		$key++;
		
		if ( $cached_response === false || apply_filters( 'ph_use_cached_response', false, $cached_response ) ) {
			
			if( $request_type == 'freight' ){
				
				$response = wp_remote_post( $this->freight_endpoint,
					array(
						'timeout'   => 70,
						'sslverify' => $this->ssl_verify,
						'body'	  => $send_request
					)
				);		
			}else{
				$response = wp_remote_post( $this->endpoint,
					array(
						'timeout'   => 70,
						'sslverify' => $this->ssl_verify,
						'body'	  => $send_request
					)
				);						
			}
			
			if ( is_wp_error( $response ) ) {	
				$error_string = $response->get_error_message();
				$this->debug( 'UPS REQUEST FAILED: <pre>' . print_r( htmlspecialchars( $error_string ), true ) . '</pre>' );
			}
			elseif ( ! empty( $response['body'] ) ) {	
				$ups_response = $response['body'];
				set_transient( $transient, $response['body'], $transient_time );
			}

		} else {
			$this->debug( __( 'UPS: Using cached response.', 'ups-woocommerce-shipping' ) );
			$ups_response = $cached_response;
		}

		if( $this->debug ) {
			$debug_request_to_display = $this->create_debug_request_or_response( $request, 'rate_request', $request_type );

			$packageCount 	= isset($debug_request_to_display['Packages']) && !empty($debug_request_to_display['Packages']) ? count($debug_request_to_display['Packages']) : '';
			$displayCount 	= !empty($packageCount) ? " | Requested Packages: ".$packageCount : '';

			$this->debug( "UPS ".strtoupper($request_type)." REQUEST [ Package Set: ".$key.$displayCount." | Max Packages: 50 ] <pre>" . print_r( $debug_request_to_display , true ) . '</pre>' );
			$debug_response_to_display = $this->create_debug_request_or_response( $ups_response, 'rate_response', $request_type );
			$this->debug( "UPS ".strtoupper($request_type)." RESPONSE [ Package Set: ".$key.$displayCount." | Max Packages: 50 ] <pre>" . print_r( $debug_response_to_display , true ) . '</pre>' );
			$this->debug( 'UPS '.strtoupper($request_type).' REQUEST XML [ Package Set: '.$key.$displayCount.' | Max Packages: 50 ] <pre>' . print_r( htmlspecialchars( $send_request ), true ) . '</pre>' );
			$this->debug( 'UPS '.strtoupper($request_type).' RESPONSE XML [ Package Set: '.$key.$displayCount.' | Max Packages: 50 ] <pre>' . print_r( htmlspecialchars( $ups_response  ), true ) . '</pre>' );

			$this->diagnostic_report( '------------------------ UPS '.strtoupper($request_type).' REQUEST [ Package Set: '.$key.$displayCount.' | Max Packages: 50 ] ------------------------' );
			$this->diagnostic_report( htmlspecialchars( $send_request ) );
			$this->diagnostic_report( '------------------------ UPS '.strtoupper($request_type).' RESPONSE [ Package Set: '.$key.$displayCount.' | Max Packages: 50 ] ------------------------' );
			$this->diagnostic_report( htmlspecialchars( $ups_response ) );
		}

		if( is_admin() ) {
			$log = wc_get_logger();
			$log->debug( print_r( __('------------------------UPS Rate Request -------------------------------', 'ups-woocommerce-shipping').PHP_EOL.PHP_EOL.htmlspecialchars($send_request).PHP_EOL.PHP_EOL,true), array('source' => 'PluginHive-UPS-Plugin'));
			$log->debug( print_r( __('------------------------UPS Rate Response -------------------------------', 'ups-woocommerce-shipping').PHP_EOL.PHP_EOL.htmlspecialchars($ups_response).PHP_EOL.PHP_EOL,true), array('source' => 'PluginHive-UPS-Plugin'));
			if( $cached_response !== false ) {
				$log->debug( print_r( 'Above Response is cached Response.'.PHP_EOL.PHP_EOL,true), array('source' => 'phive-ups-plugin'));
			}
		}

		return $ups_response;
	}

	private function wf_create_soap_client( $wsdl, $options ){
		
			$soapclient = new SoapClient( $wsdl, $options);
		return $soapclient;
	}

	/**
	 * Create Debug Request or response.
	 * @param $data mixed Xml or JSON request or response.
	 * @param $type string Rate request or Response.
	 * @param $request_type mixed Request type whether freight or surepost or normal request.
	 */
	public function create_debug_request_or_response( $data, $type='', $request_type = null ) {
		$debug_data = null;
		switch($type){
			case 'rate_request' :
									// Freight Request
									if( $request_type == 'freight' ) {
										$request_data = json_decode( $data,true);
										$debug_data = array(
											'Ship From Address'	=>	$request_data['FreightRateRequest']['ShipFrom']['Address'],
											'Ship To Address'	=>	$request_data['FreightRateRequest']['ShipTo']['Address'],
										);
										$packages = $request_data['FreightRateRequest']['Commodity'];
										foreach( $packages as $package ) {
											if( ! empty($package['Dimensions']) ) {
												$debug_data['Packages'][] = array(
													'Weight'	=>	array(
														'Value'		=>	$package['Weight']['Value'],
														'Unit'		=>	$package['Weight']['UnitOfMeasurement']['Code'],
													),
													'Dimensions'	=>	array(
														'Length'	=>	$package['Dimensions']['Length'],
														'Width'		=>	$package['Dimensions']['Width'],
														'Height'	=>	$package['Dimensions']['Height'],
														'Unit'		=>	$package['Dimensions']['UnitOfMeasurement']['Code'],
													),
												);
											}
											else{
												$debug_data['Packages'][] = array(
													'Weight'	=>	array(
														'Value'		=>	$package['Weight']['UnitOfMeasurement']['Code'],
														'Unit'		=>	$package['Weight']['Value'],
													),
												);
											}
										}
									}
									// Other request type
									else{
										$data_arr = explode( "<RatingServiceSelectionRequest>", $data );
										if( ! empty($data_arr[1]) ) {
											$request_data = self::convert_xml_to_array("<RatingServiceSelectionRequest>".$data_arr[1]);
											if( ! empty($request_data) ) {
												$debug_data = array(
													'Ship From Address'	=>	$request_data['Shipment']['ShipFrom']['Address'],
													'Ship To Address'	=>	$request_data['Shipment']['ShipTo']['Address'],
												);
												$packages = isset( $request_data['Shipment']['Package'] ) ? $request_data['Shipment']['Package'] : '';
												// Handle Single Package
												if( isset($request_data['Shipment']['Package']['PackageWeight']) ) {
													$packages = array($packages);
												}

												if ( !empty($packages) && is_array($packages) ) {

													foreach( $packages as $package ) {
														if( ! empty($package['Dimensions']) ) {
															$debug_data['Packages'][] = array(
																'Weight'	=>	array(
																	'Value'		=>	$package['PackageWeight']['Weight'],
																	'Unit'		=>	$package['PackageWeight']['UnitOfMeasurement']['Code'],
																),
																'Dimensions'	=>	array(
																	'Length'	=>	$package['Dimensions']['Length'],
																	'Width'		=>	$package['Dimensions']['Width'],
																	'Height'	=>	$package['Dimensions']['Height'],
																	'Unit'		=>	$package['Dimensions']['UnitOfMeasurement']['Code'],
																),
															);
														}
														else{
															$debug_data['Packages'][] = array(
																'Weight'	=>	array(
																	'Value'		=>	$package['PackageWeight']['UnitOfMeasurement']['Code'],
																	'Unit'		=>	$package['PackageWeight']['Weight'],
																),
															);
														}
													}
												}
											}
										}
									}
									break;
			case 'rate_response' :
									if( $request_type == 'freight' ) {
										$response_arr = json_decode($data,true);
										if( ! empty($response_arr['Fault']) ) {
											$debug_data = $response_arr['Fault'];
										}
										elseif( ! empty($response_arr['FreightRateResponse']) ) {
											$debug_data = array(
												'Service'			=>	$response_arr['FreightRateResponse']['Service']['Code'],
												'Shipping Cost'		=>	$response_arr['FreightRateResponse']['TotalShipmentCharge']['MonetaryValue'],
												'Currency Code'		=>	$response_arr['FreightRateResponse']['TotalShipmentCharge']['CurrencyCode'],
											);
										}
									}
									else{
										$response_arr =self::convert_xml_to_array($data);
										if( ! empty($response_arr['Response']['Error']) ) {
											$debug_data = $response_arr['Response']['Error'];
										}
										elseif( ! empty($response_arr['RatedShipment']) ) {
											$response_rate_arr = isset($response_arr['RatedShipment']['Service']) ? array($response_arr['RatedShipment']) : $response_arr['RatedShipment'];
											foreach( $response_rate_arr as $rate_details ) {
												$debug_data[] = array(
													'Service'		=>	$rate_details['Service']['Code'],
													'Shipping Cost'	=>	$rate_details['TotalCharges']['MonetaryValue'],
													'Currency Code'	=>	$rate_details['TotalCharges']['CurrencyCode'],
												);
											}
										}
									}
									break;
			default : break;
		}
		return $debug_data;
	}

	/**
	 * Convert XML to Array.
	 * @param $data string XML data.
	 * @return array Data as Array.
	 */
	public static function convert_xml_to_array($data){
		$data = simplexml_load_string($data);
		$data = json_encode($data);
		$data = json_decode($data,TRUE);
		return $data;
	}

	/**
	 * sort_rates function.
	 *
	 * @access public
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	public function sort_rates( $a, $b ) {
		if ( isset($a['sort']) && isset($b['sort']) && ( $a['sort'] == $b['sort'] ) ) return 0;
		return (  isset($a['sort']) && isset($b['sort']) && ( $a['sort'] < $b['sort'] ) ) ? -1 : 1;
	}

	/**
	 * get_package_requests
	 *
	 *
	 *
	 * @access private
	 * @return void
	 */
	private function get_package_requests( $package,$params=array()) {
		if( empty($package['contents']) && class_exists('wf_admin_notice') ) {
			wf_admin_notice::add_notice( __("UPS - Something wrong with products associated with order, or no products associated with order.", "ups-woocommerce-shipping"), 'error');
			return false;
		}
		// Choose selected packing
		switch ( $this->packing_method ) {
			case 'box_packing' :
				$requests = $this->box_shipping( $package,$params);
			break;
				case 'weight_based' :
						$requests = $this->weight_based_shipping($package,$params);
				break;
			case 'per_item' :
			default :
				$requests = $this->per_item_shipping( $package,$params);
			break;
		}

		if( empty($requests) )	$requests = array();

		$request_before_resetting_min_weight = $requests;
		// check for Minimum weight required by UPS
		$requests = $this->ups_minimum_weight_required( $requests );
		return apply_filters( 'ph_ups_generated_packages', $requests, $package, $request_before_resetting_min_weight );
	}



	/**
	 * get_rate_requests_gfp
	 *
	 * Get rate requests for ground freight
	 * @access private
	 * @return array of strings - XML
	 *
	 */
	public function  get_rate_requests_gfp( $package_requests, $package, $request_type='', $service_code='' ) {
			global $woocommerce;

			$customer = $woocommerce->customer;		
			
			$package_requests_to_append	= $package_requests;
			$rate_request_data			=	array(
				'user_id'					=>	$this->user_id,
				'password'					=>	str_replace( '&', '&amp;', $this->password ), // Ampersand will break XML doc, so replace with encoded version.
				'access_key'				=>	$this->access_key,
				'shipper_number'			=>	$this->shipper_number,
				'origin_addressline'		=>	$this->origin_addressline,
				'origin_addressline_2'		=>	$this->origin_addressline_2,
				'origin_postcode'			=>	$this->origin_postcode,
				'origin_city'				=>	$this->origin_city,
				'origin_state'				=>	$this->origin_state,
				'origin_country'			=>	$this->origin_country,
				'ship_from_addressline'		=>	$this->ship_from_addressline,
				'ship_from_addressline_2'	=>	$this->ship_from_addressline_2,
				'ship_from_postcode'		=>	$this->ship_from_postcode,
				'ship_from_city'			=>	$this->ship_from_city,
				'ship_from_state'			=>	$this->ship_from_state,
				'ship_from_country'			=>	$this->ship_from_country,
			);
				
			$rate_request_data	=	apply_filters('wf_ups_rate_request_data', $rate_request_data, $package, $package_requests);
			
			$request['RateRequest'] = array();

			$request['RateRequest']['Request'] = array(

				'TransactionReference'=>array(
					'CustomerContext'=>'Rating and Service'
				),
				'RequestAction'=>'Rate',
				'RequestOption'=>'Rate'
			);

			$request['RateRequest']['PickupType']=array(
				'Code'=>$this->pickup,
				'Description'=>$this->pickup_code[$this->pickup]
			);

			$request['RateRequest']['Shipment']=array(
				'FRSPaymentInformation'=>array(
					'Type'=>array(
						'Code'=>'01'
					),
					'AccountNumber'=>$this->shipper_number
				),
				'Description'=>'WooCommerce GFP Rate Request',
			);

			$originAddress = empty($rate_request_data['origin_addressline_2']) ? $rate_request_data['origin_addressline'] : array($rate_request_data['origin_addressline'],$rate_request_data['origin_addressline_2']);

			$request['RateRequest']['Shipment']['Shipper']=array(
				'Address'=>array(
					'AddressLine'=>$originAddress,
					'CountryCode'=>$rate_request_data['origin_country'],
				),
				'ShipperNumber'=>$rate_request_data['shipper_number'],
			);

			$request['RateRequest']['Shipment']['Shipper']['Address']=array_merge($request['RateRequest']['Shipment']['Shipper']['Address'],$this->ph_get_postcode_city_in_array( $rate_request_data['origin_country'], $rate_request_data['origin_city'], $rate_request_data['origin_postcode'] ));

			if( ! empty($rate_request_data['origin_state']) ) {
				$request['RateRequest']['Shipment']['Shipper']['Address']['StateProvinceCode']= $rate_request_data['origin_state'];
			}

			$destination_city 		= htmlspecialchars(strtoupper( $package['destination']['city'] ));
			$destination_country 	= "";

			if ( ( "PR" == $package['destination']['state'] ) && ( "US" == $package['destination']['country'] ) ) {		
				$destination_country = "PR";
			} else {
				$destination_country = $package['destination']['country'];
			}

			$request['RateRequest']['Shipment']['ShipTo']['Address']=array();

			$address = '';

			if ( !empty($package['destination']['address_1']) ) {

				$address = htmlspecialchars($package['destination']['address_1']);

				if( isset($package['destination']['address_2']) && !empty($package['destination']['address_2']) ) {

					$address = $address.' '.htmlspecialchars($package['destination']['address_2']);
				}

			} elseif ( !empty($package['destination']['address']) ) {

				$address = htmlspecialchars($package['destination']['address']);
			}

			if ( !empty($address) ) {

				$request['RateRequest']['Shipment']['ShipTo']['Address']['AddressLine']= $address;
			}

			$request['RateRequest']['Shipment']['ShipTo']['Address']['StateProvinceCode']=htmlspecialchars($package['destination']['state']);

			$request['RateRequest']['Shipment']['ShipTo']['Address']= array_merge($request['RateRequest']['Shipment']['ShipTo']['Address'],$this->ph_get_postcode_city_in_array( $destination_country, $destination_city, $package['destination']['postcode'] ));
			$request['RateRequest']['Shipment']['ShipTo']['Address']['CountryCode']=$destination_country;
			$request['RateRequest']['Shipment']['ShipTo']['Address']['CountryCode']=$destination_country;
			if ( $this->residential ) {
				$request['RateRequest']['Shipment']['ShipTo']['Address']['ResidentialAddressIndicator']= 1;
			}

			// If ShipFrom address is different.
			if( $this->ship_from_address_different_from_shipper == 'yes' && ! empty($rate_request_data['ship_from_addressline']) ) {

				$fromAddress = empty($rate_request_data['ship_from_addressline_2']) ? $rate_request_data['ship_from_addressline'] : array($rate_request_data['ship_from_addressline'],$rate_request_data['ship_from_addressline_2']);

				$request['RateRequest']['Shipment']['ShipFrom']=array(
					'Address'=>array(
						'AddressLine'=>$fromAddress,
						'CountryCode'=>$rate_request_data['ship_from_country'],
					),
				);

				$request['RateRequest']['Shipment']['ShipFrom']['Address']=array_merge($request['RateRequest']['Shipment']['ShipFrom']['Address'],$this->ph_get_postcode_city_in_array( $rate_request_data['ship_from_country'], $rate_request_data['ship_from_city'], $rate_request_data['ship_from_postcode'] ));

				if( ! empty($rate_request_data['ship_from_state']) ) {
					$request['RateRequest']['Shipment']['ShipFrom']['Address']['StateProvinceCode']= $rate_request_data['ship_from_state'];
				}

			} else {

				$fromAddress = empty($rate_request_data['origin_addressline_2']) ? $rate_request_data['origin_addressline'] : array($rate_request_data['origin_addressline'],$rate_request_data['origin_addressline_2']);

				$request['RateRequest']['Shipment']['ShipFrom']=array(
					'Address'=>array(
						'AddressLine'=>$fromAddress,
						'CountryCode'=>$rate_request_data['origin_country'],
					),
				);

				$request['RateRequest']['Shipment']['ShipFrom']['Address']=array_merge($request['RateRequest']['Shipment']['ShipFrom']['Address'],$this->ph_get_postcode_city_in_array( $rate_request_data['origin_country'], $rate_request_data['origin_city'], $rate_request_data['origin_postcode'] ));

				if (  $rate_request_data['origin_state'] ) {
					$request['RateRequest']['Shipment']['ShipFrom']['Address']['StateProvinceCode']= $rate_request_data['origin_state'];
				}
			}

			$request['RateRequest']['Shipment']['Service']=array('Code'=>'03');
			$total_item_count = 0;

			if( isset($package['contents']) && !empty($package['contents']) ) {

				foreach ( $package['contents'] as $product ) {
					$total_item_count += $product['quantity'];
				}
			}

			$request['RateRequest']['Shipment']['NumOfPieces']=$total_item_count;

				// packages
			$total_package_weight=0;
			$total_packags=array();
			foreach ( $package_requests_to_append as $key => $package_request ) {

				$total_package_weight += $package_request['Package']['PackageWeight']['Weight'];
				$package_request['Package']['PackageWeight'] = $this->copyArray($package_request['Package']['PackageWeight']);
				$package_request['Package']['Commodity']['FreightClass']=$this->freight_class;
				$package_request['Package']['PackagingType']['Code'] = "02";
					// Setting Length, Width and Height for weight based packing.
				if( !isset($package_request['Package']['Dimensions']) || !empty($package_request['Package']['Dimensions']) ) {
					unset($package_request['Package']['Dimensions']);
				}

                //PDS-87
				if( isset($package_request['Package']['PackageServiceOptions']) ) {

					if( isset($package_request['Package']['PackageServiceOptions']['InsuredValue']) ) {

						unset($package_request['Package']['PackageServiceOptions']['InsuredValue']);
					}

					if( isset($package_request['Package']['PackageServiceOptions']['DeliveryConfirmation']) ) {

						$package_request['Package']['PackageServiceOptions']['DeliveryConfirmation'] = $this->copyArray($package_request['Package']['PackageServiceOptions']['DeliveryConfirmation']);
					}
				}

				if( isset($package_request['Package']['items']) ) {
						unset($package_request['Package']['items']);		//Not required further
					}
					
					$total_packags[]=$package_request['Package'];
				}
				
				$request['RateRequest']['Shipment']['Package']=$total_packags;

				$request['RateRequest']['Shipment']['ShipmentRatingOptions']=array();
				if ( $this->negotiated ) {
					$request['RateRequest']['Shipment']['ShipmentRatingOptions']['NegotiatedRatesIndicator']=1;
				}
				$request['RateRequest']['Shipment']['ShipmentRatingOptions']['FRSShipmentIndicator']=1;

				if($this->tax_indicator){
					$request['RateRequest']['Shipment']['TaxInformationIndicator']=1;
				}

				$request['RateRequest']['Shipment']['DeliveryTimeInformation']=array(

					'PackageBillType' => '03',
				);

				if( $this->show_est_delivery && !empty($this->settings['cut_off_time']) && $this->settings['cut_off_time'] != '24:00' ) {
					$timestamp 							= clone $this->current_wp_time;
					$this->current_wp_time_hour_minute 	= current_time('H:i');
					if( $this->current_wp_time_hour_minute >$this->settings['cut_off_time'] ) {
						$timestamp->modify('+1 days');
						$this->pickup_date = $timestamp->format('Ymd');
						$this->pickup_time = '0800';
					} else {
						$this->pickup_date = date('Ymd');
						$this->pickup_time = $timestamp->format('Hi');
					}
					$request['RateRequest']['Shipment']['DeliveryTimeInformation']['Pickup']=array(
						'Date'=>$this->pickup_date,
						'Time'=>$this->pickup_time,
					);
				}

				$request['RateRequest']['Shipment']['ShipmentTotalWeight']=array(

					'UnitOfMeasurement' => array(
						'Code'	=>$this->weight_unit
					),
					'Weight' => $total_package_weight
				);

				$this->density_unit 	= $this->dim_unit;
				$this->density_length 	= $this->density_length;
				$this->density_width 	= $this->density_width;
				$this->density_height 	= $this->density_height;

				if( $this->density_length == 0 )
				{
					$this->density_length = ( $this->density_unit == 'IN' ) ? 10 : 26;
				}
				
				if( $this->density_width == 0 )
				{
					$this->density_width = ( $this->density_unit == 'IN' ) ? 10 : 26;
				}
				
				if( $this->density_height == 0 )
				{
					$this->density_height = ( $this->density_unit == 'IN' ) ? 10 : 26;
				}	
				if($this->enable_density_based_rating)
				{		
					$request['RateRequest']['Shipment']['FreightShipmentInformation']=array(

						'FreightDensityInfo' => array(

							'HandlingUnits' => array(

								'Quantity' 	=> 1,
								'Type'		=> array(

									'Code'			=>'PLT',
									'Description'	=> 'Density'
								),
								'Dimensions' => array(

									'UnitOfMeasurement'	=> array('Code'	=>$this->density_unit),
									'Description'		=> "Dimension unit",
									'Length'			=> $this->density_length,
									'Width'				=> $this->density_width,
									'Height'			=> $this->density_height
								)
							),
							'Description'	=> "density rating",
						),
						'DensityEligibleIndicator'	=> 1,
					);
				}
				
				return apply_filters('ph_ups_rate_request_gfp', $request, $package);
	}
	/**
	 * get_rate_requests
	 *
	 * Get rate requests for all
	 * @access private
	 * @return array of strings - XML
	 *
	 */
	public function  get_rate_requests( $package_requests, $package, $request_type='', $service_code='' ) {
		global $woocommerce;

		$customer = $woocommerce->customer;		
		
			$package_requests_to_append	= $package_requests;
			
			$rate_request_data	=	array(
				'user_id'					=>	$this->user_id,
				'password'					=>	str_replace( '&', '&amp;', $this->password ), // Ampersand will break XML doc, so replace with encoded version.
				'access_key'				=>	$this->access_key,
				'shipper_number'			=>	$this->shipper_number,
				'origin_addressline'		=>	$this->origin_addressline,
				'origin_addressline_2'		=>	$this->origin_addressline_2,
				'origin_postcode'			=>	$this->origin_postcode,
				'origin_city'				=>	$this->origin_city,
				'origin_state'				=>	$this->origin_state,
				'origin_country'			=>	$this->origin_country,
				'ship_from_addressline'		=>	$this->ship_from_addressline,
				'ship_from_addressline_2'	=>	$this->ship_from_addressline_2,
				'ship_from_postcode'		=>	$this->ship_from_postcode,
				'ship_from_city'			=>	$this->ship_from_city,
				'ship_from_state'			=>	$this->ship_from_state,
				'ship_from_country'			=>	$this->ship_from_country,
			);
			
			$rate_request_data	=	apply_filters('wf_ups_rate_request_data', $rate_request_data, $package, $package_requests);

			$this->is_hazmat_product 	= false;
			
			// Security Header
			$request  = "<?xml version=\"1.0\" ?>" . "\n";
			$request .= "<AccessRequest xml:lang='en-US'>" . "\n";
			$request .= "	<AccessLicenseNumber>" . $rate_request_data['access_key'] . "</AccessLicenseNumber>" . "\n";
			$request .= "	<UserId>" . $rate_request_data['user_id'] . "</UserId>" . "\n";
			$request .= "	<Password>" . $rate_request_data['password'] . "</Password>" . "\n";
			$request .= "</AccessRequest>" . "\n";
			$request .= "<?xml version=\"1.0\" ?>" . "\n";
			$request .= "<RatingServiceSelectionRequest>" . "\n";
			$request .= "	<Request>" . "\n";
			$request .= "	<TransactionReference>" . "\n";
			$request .= "		<CustomerContext>Rating and Service</CustomerContext>" . "\n";
			$request .= "		<XpciVersion>1.0</XpciVersion>" . "\n";
			$request .= "	</TransactionReference>" . "\n";
			$request .= "	<RequestAction>Rate</RequestAction>" . "\n";

			// For Estimated delivery, Estimated delivery not available for Surepost confirmed by UPS
			if( $this->show_est_delivery && $request_type != 'surepost') {
				$requestOption = empty($service_code) ? 'Shoptimeintransit' : 'Ratetimeintransit';
			}
			else {
				$requestOption = empty($service_code) ? 'Shop' : 'Rate';
			}
			$request .= "	<RequestOption>$requestOption</RequestOption>" . "\n";
			$request .= "	</Request>" . "\n";
			$request .= "	<PickupType>" . "\n";
			$request .= "		<Code>" . $this->pickup . "</Code>" . "\n";
			$request .= "		<Description>" . $this->pickup_code[$this->pickup] . "</Description>" . "\n";
			$request .= "	</PickupType>" . "\n";
				
			//Accroding to the documentaion CustomerClassification will not work for non-us county. But UPS team confirmed this will for any country.
			// if ( 'US' == $rate_request_data['origin_country']) {
				if ( $this->negotiated ) {
					$request .= "	<CustomerClassification>" . "\n";
					$request .= "		<Code>" . "00" . "</Code>" . "\n";
					$request .= "	</CustomerClassification>" . "\n";   
				}
				elseif ( !empty( $this->customer_classification ) && $this->customer_classification != 'NA' ) {
					$request .= "	<CustomerClassification>" . "\n";
					$request .= "		<Code>" . $this->customer_classification . "</Code>" . "\n";
					$request .= "	</CustomerClassification>" . "\n";   
				}
			// }
				
				// Shipment information
				$request .= "	<Shipment>" . "\n";
				
				if($this->accesspoint_locator ){
					$access_point_node = $this->get_acccesspoint_rate_request();					
					if(!empty($access_point_node)){// Access Point Addresses Are All Commercial
						$this->residential	=	false;
						$request .= $access_point_node;
					}
					
				}
				
				$request .= "		<Description>WooCommerce Rate Request</Description>" . "\n";
				$request .= "		<Shipper>" . "\n";
				$request .= "			<ShipperNumber>" . $rate_request_data['shipper_number'] . "</ShipperNumber>" . "\n";
				$request .= "			<Address>" . "\n";
				$request .= "				<AddressLine1>" . $rate_request_data['origin_addressline'] . "</AddressLine1>" . "\n";
				
				if ( !empty($rate_request_data['origin_addressline_2']) ) {

					$request .= "				<AddressLine2>" . $rate_request_data['origin_addressline_2'] . "</AddressLine2>" . "\n";
				}

				$request .= $this->wf_get_postcode_city( $rate_request_data['origin_country'], $rate_request_data['origin_city'], $rate_request_data['origin_postcode'] );

				if( ! empty($rate_request_data['origin_state']) ) {
					$request .= "			<StateProvinceCode>".$rate_request_data['origin_state']."</StateProvinceCode>\n";
				}

				$request .= "				<CountryCode>" . $rate_request_data['origin_country'] . "</CountryCode>" . "\n";
				$request .= "			</Address>" . "\n";
				$request .= "		</Shipper>" . "\n";
				$request .= "		<ShipTo>" . "\n";
				$request .= "			<Address>" . "\n";

				// Residential address Validation done by API automatically if address_1 is available.
				$address = '';

				if ( !empty($package['destination']['address_1']) ) {

					$address = htmlspecialchars($package['destination']['address_1']);

					if( isset($package['destination']['address_2']) && !empty($package['destination']['address_2']) ) {

						$address = $address.' '.htmlspecialchars($package['destination']['address_2']);
					}

				} elseif ( !empty($package['destination']['address']) ) {

					$address = htmlspecialchars($package['destination']['address']);
				}

				if ( !empty($address) ) {

					$request .= "				<AddressLine1>" . $address . "</AddressLine1>" . "\n";
				}

				$request .= "				<StateProvinceCode>" . htmlspecialchars($package['destination']['state']) . "</StateProvinceCode>" . "\n";
				
				$destination_city = htmlspecialchars(strtoupper( $package['destination']['city'] ));
				$destination_country = "";
				if ( ( "PR" == $package['destination']['state'] ) && ( "US" == $package['destination']['country'] ) ) {		
						$destination_country = "PR";
				} else {
						$destination_country = $package['destination']['country'];
				}
				
				//$request .= "				<PostalCode>" . $package['destination']['postcode'] . "</PostalCode>" . "\n";
				$request .= $this->wf_get_postcode_city( $destination_country, $destination_city, $package['destination']['postcode'] );
				$request .= "				<CountryCode>" . $destination_country . "</CountryCode>" . "\n";
				
				if ( $this->residential ) {
				$request .= "				<ResidentialAddressIndicator></ResidentialAddressIndicator>" . "\n";
				}
				$request .= "			</Address>" . "\n";
				$request .= "		</ShipTo>" . "\n";

				// If ShipFrom address is different.
				if( $this->ship_from_address_different_from_shipper == 'yes' && ! empty($rate_request_data['ship_from_addressline']) ) {
					$request .= "		<ShipFrom>" . "\n";
					$request .= "			<Address>" . "\n";
					$request .= "				<AddressLine1>" . $rate_request_data['ship_from_addressline'] . "</AddressLine1>" . "\n";

					if ( !empty($rate_request_data['ship_from_addressline_2']) ) {

						$request .= "			<AddressLine2>" . $rate_request_data['ship_from_addressline_2'] . "</AddressLine2>" . "\n";
					}

					$request .= $this->wf_get_postcode_city( $rate_request_data['ship_from_country'], $rate_request_data['ship_from_city'], $rate_request_data['ship_from_postcode']);

					if( ! empty($rate_request_data['ship_from_state']) ) {
						$request .= "			<StateProvinceCode>".$rate_request_data['ship_from_state']."</StateProvinceCode>\n";
					}

					$request .= "				<CountryCode>" . $rate_request_data['ship_from_country'] . "</CountryCode>" . "\n";
					$request .= "			</Address>" . "\n";
					$request .= "		</ShipFrom>" . "\n";
				} else {
					$request .= "		<ShipFrom>" . "\n";
					$request .= "			<Address>" . "\n";
					$request .= "				<AddressLine1>" . $rate_request_data['origin_addressline'] . "</AddressLine1>" . "\n";
					
					if ( !empty($rate_request_data['origin_addressline_2']) ) {

						$request .= "			<AddressLine2>" . $rate_request_data['origin_addressline_2'] . "</AddressLine2>" . "\n";
					}

					$request .= $this->wf_get_postcode_city( $rate_request_data['origin_country'], $rate_request_data['origin_city'], $rate_request_data['origin_postcode']);

					if( ! empty($rate_request_data['origin_state']) ) {
						$request .= "			<StateProvinceCode>".$rate_request_data['origin_state']."</StateProvinceCode>\n";
					}
					$request .= "				<CountryCode>" . $rate_request_data['origin_country'] . "</CountryCode>" . "\n";
					$request .= "			</Address>" . "\n";
					$request .= "		</ShipFrom>" . "\n";
				}

				//For Worldwide Express Freight Service
				if( $request_type == 'Pallet' && $service_code == 96 && isset($package['contents']) && is_array($package['contents'] ) ) {
					$total_item_count = 0;
					foreach ( $package['contents'] as $product ) {
						$total_item_count += $product['quantity'];
					}
					$request .= "	<NumOfPieces>".$total_item_count."</NumOfPieces>"."\n";
				}
				//Ground Freight Pricing Rates option indicator. If the Ground Freight Pricing Shipment indicator is enabled and  hipper number is authorized then Ground Freight Pricing rates should be returned in the response
				/*if( $this->ground_freight ){
					$request .= "		<FRSPaymentInformation>" . "\n";
					$request .= "			<Type>" . "\n";
					$request .= "				<Code>01</Code>" . "\n";
					$request .= "			</Type>" . "\n";
					$request .= "			<AccountNumber>$this->shipper_number</AccountNumber>" . "\n";

					$request .= "		</FRSPaymentInformation>" . "\n";

					$request .= "		<ShipmentRatingOptions>" . "\n";
					$request .= "			<FRSShipmentIndicator>1</FRSShipmentIndicator>" . "\n";
					$request .= "		</ShipmentRatingOptions>" . "\n";
				}*/
				if( !empty($service_code) ){
					$request .= "		<Service>" . "\n";
					$request .= "			<Code>" . $this->get_service_code_for_country( $service_code,$rate_request_data['origin_country'] ) . "</Code>" . "\n";
					$request .= "		</Service>" . "\n";
				}

				// Hazmat Materials & ISC
				$id = 0;

				$alcoholicbeveragesindicator 	= 'no';
				$diagnosticspecimensindicator 	= 'no';
				$perishablesindicator 			= 'no';
				$plantsindicator 				= 'no';
				$seedsindicator 				= 'no';
				$specialexceptionsindicator 	= 'no';
				$tobaccoindicator 				= 'no';

				if ( isset($package['contents']) ) {

					foreach( $package['contents'] as $product ) {

						$hazmat_product 	= 'no';
						$product_id 		= ( isset( $product['product_id']) ) ? $product['product_id'] : '';
						$product_var_id 	= ( isset( $product['variation_id']) ) ? $product['variation_id'] : '';

						if( !empty($product_id) )
						{
							$hazmat_product 	= get_post_meta($product_id,'_ph_ups_hazardous_materials',1);
							$hazmat_settings 	= get_post_meta($product_id,'_ph_ups_hazardous_settings',1);
						}

						if( !empty($product_var_id) && $hazmat_product != 'yes' )
						{
							$hazmat_product 	= get_post_meta($product_var_id,'_ph_ups_hazardous_materials',1);
							$hazmat_settings 	= get_post_meta($product_var_id,'_ph_ups_hazardous_settings',1);
						}
						
						if($hazmat_product == 'yes'){

							$id++;
							$this->is_hazmat_product = true;

							$req_arr['PackageIdentifier'] = $id;
							$req_arr['HazMatChemicalRecord']['ChemicalRecordIdentifier'] = !empty( $hazmat_settings['_ph_ups_record_number'] ) ? $hazmat_settings['_ph_ups_record_number'] : '';
							$req_arr['HazMatChemicalRecord']['ClassDivisionNumber'] = !empty( $hazmat_settings['_ph_ups_class_division_no'] ) ? $hazmat_settings['_ph_ups_class_division_no'] : '';
							$req_arr['HazMatChemicalRecord']['IDNumber'] = !empty( $hazmat_settings['_ph_ups_commodity_id'] ) ? $hazmat_settings['_ph_ups_commodity_id'] : '';
							$req_arr['HazMatChemicalRecord']['TransportationMode'] = $hazmat_settings['_ph_ups_hm_transportaion_mode'];
							$req_arr['HazMatChemicalRecord']['RegulationSet'] = $hazmat_settings['_ph_ups_hm_regulations'];
							$req_arr['HazMatChemicalRecord']['PackagingGroupType'] = !empty( $hazmat_settings['_ph_ups_package_group_type'] ) ? $hazmat_settings['_ph_ups_package_group_type'] : '';
							$req_arr['HazMatChemicalRecord']['Quantity'] = $product['quantity'];
							$req_arr['HazMatChemicalRecord']['UOM'] = ( $this->uom == 'LB' ) ? 'pound' : 'kg' ;
							$req_arr['HazMatChemicalRecord']['ProperShippingName'] = !empty( $hazmat_settings['_ph_ups_shipping_name'] ) ? $hazmat_settings['_ph_ups_shipping_name'] : '';
							$req_arr['HazMatChemicalRecord']['TechnicalName'] = !empty( $hazmat_settings['_ph_ups_technical_name'] ) ? $hazmat_settings['_ph_ups_technical_name'] : '';
							$req_arr['HazMatChemicalRecord']['AdditionalDescription']= !empty( $hazmat_settings['_ph_ups_additional_description'] ) ? $hazmat_settings['_ph_ups_additional_description'] : '';
							$req_arr['HazMatChemicalRecord']['PackagingType'] = !empty( $hazmat_settings['_ph_ups_package_type'] ) ? $hazmat_settings['_ph_ups_package_type'] : '';
							$req_arr['HazMatChemicalRecord']['PackagingTypeQuantity'] = $product['quantity'];
							$req_arr['HazMatChemicalRecord']['CommodityRegulatedLevelCode'] = $hazmat_settings['_ph_ups_hm_commodity'];
							$req_arr['HazMatChemicalRecord']['EmergencyPhone'] = $this->phone_number;
							$req_arr['HazMatChemicalRecord']['EmergencyContact'] = $this->ups_display_name;

							$new_req_arr[] = array('HazMat'=>$req_arr);
						}

						// Restricted Articles
						if ($this->isc) {

							$restricted_product  = 'no';
							
							if ( !empty($product_id) )
							{
								$restricted_product 	= get_post_meta($product_id,'_ph_ups_restricted_article',1);
								$restrictedarticle 		= get_post_meta($product_id,'_ph_ups_restricted_settings',1);
							}

							if ( !empty($product_var_id) && $restricted_product != 'yes' )
							{
								$restricted_product = get_post_meta($product_var_id,'_ph_ups_restricted_article',1);
								$restrictedarticle 	= get_post_meta($product_var_id,'_ph_ups_restricted_settings',1);
							}

							if ( $restricted_product =='yes' && isset($restrictedarticle) && !empty($restrictedarticle) ) {

								$alcoholicbeveragesindicator 	= ($alcoholicbeveragesindicator=='yes') ? $alcoholicbeveragesindicator : $restrictedarticle['_ph_ups_alcoholic'];
								$diagnosticspecimensindicator 	= ($diagnosticspecimensindicator=='yes') ? $diagnosticspecimensindicator : $restrictedarticle['_ph_ups_diog'];
								$perishablesindicator 			= ($perishablesindicator=='yes') ? $perishablesindicator : $restrictedarticle['_ph_ups_perishable'];
								$plantsindicator 				= ($plantsindicator=='yes') ? $plantsindicator : $restrictedarticle['_ph_ups_plantsindicator'];
								$seedsindicator 				= ($seedsindicator=='yes') ? $seedsindicator : $restrictedarticle['_ph_ups_seedsindicator'];
								$specialexceptionsindicator 	= ($specialexceptionsindicator=='yes') ? $specialexceptionsindicator : $restrictedarticle['_ph_ups_specialindicator'];
								$tobaccoindicator 				= ($tobaccoindicator=='yes') ? $tobaccoindicator : $restrictedarticle['_ph_ups_tobaccoindicator'];
							}
						}
					}
				}

				// packages
				
				$total_package_weight = 0;
				foreach ( $package_requests_to_append as $key => $package_request ) {
					$total_package_weight += $package_request['Package']['PackageWeight']['Weight'];
					if( $request_type == 'surepost' ){
						unset($package_request['Package']['PackageServiceOptions']['InsuredValue']);
						if( $service_code == 92 ) {
							$package_request = $this->convert_weight( $package_request, $service_code );
						}
					}
					
					//For Worldwide Express Freight Service
					if( $request_type == "Pallet" ) {
						$package_request['Package']['PackagingType']['Code'] = 30;
						// Setting Length, Width and Height for weight based packing.
						if( empty($package_request['Package']['Dimensions']) ) {
							
							$package_request['Package']['Dimensions'] = array(
								'UnitOfMeasurement' => array(
									'Code'  =>	($package_request['Package']['PackageWeight']['UnitOfMeasurement']['Code'] == 'LBS') ? 'IN' : 'CM',
								),
								'Length'    =>	($package_request['Package']['PackageWeight']['UnitOfMeasurement']['Code'] == 'LBS') ? 47 : 119,
								'Width'	    =>	($package_request['Package']['PackageWeight']['UnitOfMeasurement']['Code'] == 'LBS') ? 47 : 119,
								'Height'    =>	($package_request['Package']['PackageWeight']['UnitOfMeasurement']['Code'] == 'LBS') ? 47 : 119
							);
						}
					}
					
					// To Set deliveryconfirmation at shipment level if shipment is international or outside of $this->dc_domestic_countries
					if ( is_admin() ) {

						$usCountry 	= array('US','PR');
						$origin 	= $this->origin_country;
						$dest 		= $package['destination']['country'];

						if( ( ($origin !== $dest) && !(in_array($origin, $usCountry) && in_array($dest, $usCountry)) ) && isset($package_request['Package']) && isset($package_request['Package']['items']) ) {

							$shipment_delivery_confirmation = $this->get_package_signature($package_request['Package']['items']);
							$shipment_delivery_confirmation = $shipment_delivery_confirmation < $this->ph_delivery_confirmation ? $this->ph_delivery_confirmation : $shipment_delivery_confirmation ;

							$delivery_confirmation = ( isset($delivery_confirmation) && $delivery_confirmation >= $shipment_delivery_confirmation ) ? $delivery_confirmation : $shipment_delivery_confirmation;

						}

					} else if( isset($this->international_delivery_confirmation_applicable) && $this->international_delivery_confirmation_applicable ) {

						$shipment_delivery_confirmation = $this->get_package_signature($package_request['Package']['items']);
						$shipment_delivery_confirmation = $shipment_delivery_confirmation < $this->ph_delivery_confirmation ? $this->ph_delivery_confirmation : $shipment_delivery_confirmation ;

						$delivery_confirmation = ( isset($delivery_confirmation) && $delivery_confirmation >= $shipment_delivery_confirmation) ? $delivery_confirmation : $shipment_delivery_confirmation;
						
					}
					
					//Not required further
					if( isset($package_request['Package']['items']) ) {

						unset($package_request['Package']['items']);
					}

					if($this->is_hazmat_product)
					{
						$hazmat_array['Package']['PackageServiceOptions']['HazMat']	=	array_merge(array('multi_node'=>1), $new_req_arr);
						$package_request = array_merge_recursive($package_request, $hazmat_array);
					}

					$request .= $this->wf_array_to_xml($package_request);
				}
				// negotiated rates flag
				if ( $this->negotiated ) {
				$request .= "		<RateInformation>" . "\n";
				$request .= "			<NegotiatedRatesIndicator />" . "\n";
				$request .= "		</RateInformation>" . "\n";
				}
				
				if($this->tax_indicator){
					$request .= "		<TaxInformationIndicator/>" . "\n";
				}	
				
				if ( ( isset($delivery_confirmation) && !empty($delivery_confirmation) ) || ($this->isc) || ($this->cod_enable)) {

					$request .= "<ShipmentServiceOptions>";
				}		
				
				// Set deliveryconfirmation at shipment level for international shipment
				if( isset($delivery_confirmation) && !empty($delivery_confirmation) ) {
					
					$delivery_confirmation = ($delivery_confirmation == 3) ? 2 : 1;
					
					$request .= "			<DeliveryConfirmation>"
							. "<DCISType>$delivery_confirmation</DCISType>"
							. "</DeliveryConfirmation>"."\n";
				}

				if ( $this->isc) {
					
					$request .="\n  <RestrictedArticles>"."\n";

					if ($alcoholicbeveragesindicator=='yes') {

						$request .="<AlcoholicBeveragesIndicator></AlcoholicBeveragesIndicator>";
					}

					if ($diagnosticspecimensindicator=='yes') {

						$request .="<DiagnosticSpecimensIndicator></DiagnosticSpecimensIndicator>";
					}

					if ($perishablesindicator=='yes') {
						
						$request .="<PerishablesIndicator></PerishablesIndicator>";
					}

					if ($plantsindicator=='yes') {

						$request .="<PlantsIndicator></PlantsIndicator>";
					}
					
					if ($seedsindicator=='yes') {

						$request .="<SeedsIndicator></SeedsIndicator>";
					}

					if ($specialexceptionsindicator=='yes') {

						$request .="<SpecialExceptionsIndicator></SpecialExceptionsIndicator>";
					}

					if ($tobaccoindicator=='yes') {

						$request .="<TobaccoIndicator></TobaccoIndicator>";
					}

					$request.="  </RestrictedArticles>"."\n";
				}

				if ($this->cod_enable) {

					$destination = isset($this->destination['country']) && !empty($this->destination['country']) ? $this->destination['country'] : $package['destination']['country'];

					if ( $this->is_shipment_level_cod_required($destination) ) {

						$cod_amount = isset($package['cart_subtotal']) && !empty($package['cart_subtotal']) ? $package['cart_subtotal'] : $package['contents_cost'];	

						// 1 for Cash, 9 for Cheque, 1 is available for all the countries
						$codfundscode = in_array( $to_address['country'], array('RU', 'AE') ) ? 1 : $this->eu_country_cod_type;

						$request .="<COD><CODCode>3</CODCode><CODFundsCode>".$codfundscode."</CODFundsCode><CODAmount><MonetaryValue>".$cod_amount."</MonetaryValue></CODAmount></COD>";
					}
				}

				if ( ( isset($delivery_confirmation) && !empty($delivery_confirmation) ) || ($this->isc) | ($this->cod_enable)) {

					$request .= "</ShipmentServiceOptions>";
				}

				// Required for estimated delivery
				if( $this->show_est_delivery ) {

					//cuttoff time- PDS-80
					if( !empty($this->settings['cut_off_time']) && $this->settings['cut_off_time'] != '24:00') {

						$timestamp = clone $this->current_wp_time;
						$this->current_wp_time_hour_minute = current_time('H:i');

						if($this->current_wp_time_hour_minute >$this->settings['cut_off_time']){

							$timestamp->modify('+1 days');
							$this->pickup_date = $timestamp->format('Ymd');
							 $this->pickup_time = '0800';
					    }
						
						else{
							$this->pickup_date= date('Ymd');
							$this->pickup_time= $timestamp->format('Hi');
						}
					}
                    
                    else{
						$this->pickup_date= date('Ymd');
						$this->pickup_time= current_time('Hi');
					}

					$request .= "\n<DeliveryTimeInformation><PackageBillType>03</PackageBillType><Pickup><Date>".$this->pickup_date."</Date><Time>".$this->pickup_time."</Time></Pickup></DeliveryTimeInformation>\n";
					$request .= "\n<ShipmentTotalWeight>
						<UnitOfMeasurement><Code>".$this->weight_unit."</Code></UnitOfMeasurement>
						<Weight>$total_package_weight</Weight>
						</ShipmentTotalWeight>\n";

					if( $this->ship_from_country != $package['destination']['country']) {

						if(empty($package['contents_cost']) && isset($package['cart_subtotal'])) {

							$package['contents_cost']=$package['cart_subtotal'];
						}

						$invoiceTotal  = round( ($package['contents_cost'] / (float)$this->conversion_rate), 2 );

						// Invoice Line Total amount for the shipment.
						// Valid values are from 1 to 99999999
						if( $invoiceTotal < 1 ) {
							$invoiceTotal 	= 1;
						}

						$request .= "\n<InvoiceLineTotal>
											<CurrencyCode>".$this->currency_type."</CurrencyCode>
											<MonetaryValue>".$invoiceTotal."</MonetaryValue>
										</InvoiceLineTotal>\n";
					}
				}

				$request .= "	</Shipment>" . "\n";
				$request .= "</RatingServiceSelectionRequest>" . "\n";

				if($this->is_hazmat_product)
				{
					$exploded_request = explode( "</TransactionReference>", $request );
					$request  = $exploded_request[0]."</TransactionReference>";
					$request .= "\n    <SubVersion>1701</SubVersion>";
					$request .= $exploded_request[1];
				}

				return apply_filters('wf_ups_rate_request', $request, $package);


	}
	private function wf_get_accesspoint_datas( $order_details='' ){
		// For getting the rates in backend
		if( is_admin() ){
			if( isset($_GET['wf_ups_generate_packages_rates']) ) {
				$order_id = base64_decode($_GET['wf_ups_generate_packages_rates']);
				$order_details = new WC_Order($order_id);
			}
			else {
				return;
			}
		}
		
		if( !empty( $order_details ) ){
			if( WC()->version < '2.7.0' ){
				return ( isset($order_details->shipping_accesspoint) ) ? stripslashes($order_details->shipping_accesspoint) : '';
			}else{
				$address_field = $order_details->get_meta('_shipping_accesspoint');
				return stripslashes($address_field);
			}
		}else{
			return $this->ph_ups_selected_access_point_details;
		}
	}

	private function get_service_code_for_country($service, $country){
		$service_for_country = array(
			'CA' => array(
				'07' => '01', // for Canada serivce code of 'UPS Express(07)' is '01'
				'65' => '13', // Saver
			),
		);
		if( array_key_exists($country, $service_for_country) ){
			return isset($service_for_country[$country][$service]) ? $service_for_country[$country][$service] : $service;
		}
		return $service;
	}


	public function get_acccesspoint_rate_request(){
		//Getting accesspoint address details
		$access_request = '';
		$shipping_accesspoint = $this->wf_get_accesspoint_datas();
		if( !empty($shipping_accesspoint) && is_string($shipping_accesspoint) ){
			$decoded_accesspoint = json_decode($shipping_accesspoint);
			if(isset($decoded_accesspoint->AddressKeyFormat)){
					
				$accesspoint_addressline	= $decoded_accesspoint->AddressKeyFormat->AddressLine;
				$accesspoint_city			= (property_exists($decoded_accesspoint->AddressKeyFormat,'PoliticalDivision2')) ? $decoded_accesspoint->AddressKeyFormat->PoliticalDivision2 : '';
				$accesspoint_state			= (property_exists($decoded_accesspoint->AddressKeyFormat,'PoliticalDivision1')) ? $decoded_accesspoint->AddressKeyFormat->PoliticalDivision1:'';
				$accesspoint_postalcode		= $decoded_accesspoint->AddressKeyFormat->PostcodePrimaryLow;
				$accesspoint_country		= $decoded_accesspoint->AddressKeyFormat->CountryCode;
			
				$access_request .= "		<ShipmentIndicationType>" . "\n";
				$access_request .=	"			<Code>01</Code>" . "\n";
				$access_request .=	"		</ShipmentIndicationType>" . "\n";
				$access_request .= "		<AlternateDeliveryAddress>" . "\n";
				$access_request .= "			<Address>" . "\n";
				$access_request .= "				<AddressLine1>" . $accesspoint_addressline. "</AddressLine1>" . "\n";
				$access_request .= "				<City>" .$accesspoint_city ."</City>" . "\n";
				$access_request .= "				<StateProvinceCode>" . $accesspoint_state. "</StateProvinceCode>" . "\n";
				$access_request .= "				<PostalCode>" .$accesspoint_postalcode . "</PostalCode>" . "\n";
				$access_request .= "				<CountryCode>" . $accesspoint_country. "</CountryCode>" . "\n";
				$access_request .= "			</Address>" . "\n";
				$access_request .= "		</AlternateDeliveryAddress>" . "\n";
			}
		}
		
		return $access_request;
		
	}

	private function wf_get_postcode_city($country, $city, $postcode){
		$request_part = "";
		if( in_array( $country, $this->no_postcode_country_array ) && !empty( $city ) ) {
			$request_part = "<City>" . $city . "</City>" . "\n";
		}
		else if ( empty( $city ) ) {
			$request_part = "<PostalCode>" . $postcode . "</PostalCode>" . "\n";
		}
		else {
			$request_part = " <City>" . $city . "</City>" . "\n";
			$request_part .= "<PostalCode>" . $postcode. "</PostalCode>" . "\n";
		}
		
		return $request_part;
	}

	private function ph_get_postcode_city_in_array($country, $city, $postcode){
		$request_part = array();
		if( in_array( $country, $this->no_postcode_country_array ) && !empty( $city ) ) {
			$request_part['City'] =$city;
		}
		else if ( empty( $city ) ) {
			$request_part['PostalCode'] = $postcode;
		}
		else {
			$request_part['City'] =$city;
			$request_part['PostalCode'] = $postcode;
		}
		
		return $request_part;
	}

	/**
	 * per_item_shipping function.
	 *
	 * @access private
	 * @param mixed $package
	 * @return mixed $requests - an array of XML strings
	 */
	private function per_item_shipping( $package, $params=array() ) {
		global $woocommerce;

		$requests = array();
		$refrigeratorindicator='no';
		$ctr=0;
		$this->destination = $package['destination'];
		foreach ( $package['contents'] as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $values['data'] );
			$ctr++;

			$additional_products = apply_filters( 'xa_ups_alter_products_list', array($values) );	// To support product addon

			foreach( $additional_products as $values ) {
				
				$skip_product = apply_filters('wf_shipping_skip_product',false, $values, $package['contents']);
				if($skip_product){
					continue;
				}

				if ( !( $values['quantity'] > 0 && $values['data']->needs_shipping() ) ) {
					$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'ups-woocommerce-shipping' ), $values['data']->id ) );

					// Add by Default
					$this->diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Rate Calculation', $values['data']->id ) );

					continue;
				}

				if ( ! $values['data']->get_weight() ) {
					$this->debug( sprintf( __( 'Product #%d is missing weight. Aborting.', 'ups-woocommerce-shipping' ), $values['data']->id ), 'error' );

					// Add by Default
					$this->diagnostic_report( sprintf( 'Product #%d is missing weight. Aborting Rate Calculation', $values['data']->id ) );

					return;
				}

				// get package weight
				$weight = wc_get_weight( $values['data']->get_weight(), $this->weight_unit );
				//$weight = apply_filters('wf_ups_filter_product_weight', $weight, $package, $item_id );

				// get package dimensions
				if ( $values['data']->length && $values['data']->height && $values['data']->width ) {

					$dimensions = array( number_format( wc_get_dimension( (float) $values['data']->length, $this->dim_unit ), 2, '.', ''),
										 number_format( wc_get_dimension( (float) $values['data']->height, $this->dim_unit ), 2, '.', ''),
										 number_format( wc_get_dimension( (float) $values['data']->width, $this->dim_unit ), 2, '.', '') );
					sort( $dimensions );

				}
				if( isset( $dimensions ) ){
					foreach( $dimensions as $key => $dimension ){	//ensure the dimensions aren't zero
						if ( $dimension <= 0 ){
							$dimensions[ $key ] = 0.01;
						}
					}
				}

				// get quantity in cart
				$cart_item_qty = $values['quantity'];
				// get weight, or 1 if less than 1 lbs.
				// $_weight = ( floor( $weight ) < 1 ) ? 1 : $weight;
				
				$request['Package']	=	array(
					'PackagingType'	=>	array(
						'Code'			=>	'02',
						'Description'	=>	'Package/customer supplied'
					),
					'Description'	=>	'Rate',
				);
				
				if ( $values['data']->length && $values['data']->height && $values['data']->width ) {
					$request['Package']['Dimensions']	=	array(
						'UnitOfMeasurement'	=>	array(
							'Code'	=>	$this->dim_unit
						),
						'Length'	=>	$dimensions[2],
						'Width'		=>	$dimensions[1],
						'Height'	=>	$dimensions[0]
					);
				}
				if((isset($params['service_code'])&&$params['service_code']==92)||($this->service_code==92))// Surepost Less Than 1LBS
				{
					if($this->weight_unit=='LBS'){ // make sure weight in pounds
						$weight_ozs=$weight*16;
					}else{
						$weight_ozs=$weight*35.274; // From KG
					}
					$request['Package']['PackageWeight']	=	array(
						'UnitOfMeasurement'	=>	array(
							'Code'	=>	'OZS'
						),
						'Weight'	=>	$weight_ozs,
					);
				}else{

					// Invalid Weight Error if Weight is less than 0.05 for Estimated Delivery Option
					if( $weight < 0.05 ) {
						$weight = 0.05;
					}

					$request['Package']['PackageWeight']	=	array(
						'UnitOfMeasurement'	=>	array(
							'Code'	=>	$this->weight_unit
						),
						'Weight'	=>	$weight,
					);
				}

				
				if( $this->insuredvalue || $this->cod || $this->cod_enable) {
					
					// InsuredValue
					if( $this->insuredvalue ) {
						
						$request['Package']['PackageServiceOptions']['InsuredValue']	=	array(
							'CurrencyCode'	=>	$this->get_ups_currency(),
							'MonetaryValue'	=>	(string) round(( $this->wf_get_insurance_amount($values['data']) / $this->conversion_rate ),2)
						);
					}
					
					//Cod
					if ( ($this->cod &&isset( $_GET['wf_ups_shipment_confirm'])) || ($this->cod_enable && !isset( $_GET['wf_ups_shipment_confirm'])) ) {

						if ( ! $this->is_shipment_level_cod_required($this->destination['country']) ) {
							
							$cod_amount 	= $values['data']->get_price();
							$codfundscode 	= in_array( $this->destination['country'], array('AR', 'BR', 'CL') ) ? 9 : 0;

							$request['Package']['PackageServiceOptions']['COD']	=	array(
								'CODCode'		=>	3,
								'CODFundsCode'	=>	$codfundscode,
								'CODAmount'		=>	array(
									'MonetaryValue'	=>	(string) round( $cod_amount, 2),
								),
							);
						}
					}
				}

				if ($this->isc) {

					$refrigeratorindicator 	= 'no';
					$clinicalid 			= '';
					$clinicalvar 			= get_post_meta($values['data']->id,'_ph_ups_clinicaltrials_var',1);
					$refrigerator_var 		= get_post_meta($values['data']->id,'_ph_ups_refrigeration_var',1);

					if (empty($refrigerator_var) || !isset($refrigerator_var)) {

						$refrigerator 	= get_post_meta($values['data']->id,'_ph_ups_refrigeration',1);
					
					} else {

						$refrigerator 	= $refrigerator_var;
					}

					if (empty($clinicalvar) || !isset($clinicalvar)) {

						$clinical 	= get_post_meta($values['data']->id,'_ph_ups_clinicaltrials',1); 
					
					} else {

						$clinical 	= $clinicalvar;
					}

					$refrigeratorindicator  = ($refrigeratorindicator=='yes') ? $refrigeratorindicator : $refrigerator;
					$clinicalid 			= (isset($clinicalid)&&!empty($clinicalid)) ? $clinicalid : $clinical;

					if ($refrigeratorindicator=='yes') {

						$request['Package']['PackageServiceOptions']['RefrigerationIndicator'] = '1';
					}

					if ( isset($clinicalid) && !empty($clinicalid) && isset($_GET['wf_ups_shipment_confirm']) ) {

						$request['Package']['PackageServiceOptions']['ClinicaltrialsID'] = $clinicalid;
					}
				}
				
				//Adding all the items to the stored packages
				$request['Package']['items'] = array($values['data']->obj);
				
				// Direct Delivery option
				$directdeliveryonlyindicator = $this->get_individual_product_meta( array($values['data']), '_wf_ups_direct_delivery' );
				if( $directdeliveryonlyindicator == 'yes' ) {
					$request['Package']['DirectDeliveryOnlyIndicator'] = $directdeliveryonlyindicator;
				}
				
				// Delivery Confirmation
				if(isset($params['delivery_confirmation_applicable']) && $params['delivery_confirmation_applicable'] == true){

					$signature_option = $this->get_package_signature(array($values['data']));
					$signature_option = $signature_option < $this->ph_delivery_confirmation ? $this->ph_delivery_confirmation : $signature_option ;

					if (isset($request['Package']['PackageServiceOptions']) && isset($request['Package']['PackageServiceOptions']['COD'])) {

						$this->diagnostic_report( 'UPS : COD Shipment. Signature will not be applicable.' );
					}

					if(!empty($signature_option)&& ($signature_option > 0) && ( !isset($request['Package']['PackageServiceOptions']) || (isset($request['Package']['PackageServiceOptions']) && !isset($request['Package']['PackageServiceOptions']['COD'])) ) ) {

						$this->diagnostic_report( 'UPS : Require Signature - '. $signature_option );
						$request['Package']['PackageServiceOptions']['DeliveryConfirmation']['DCISType']= $signature_option;
					}
				}

				for ( $i=0; $i < $cart_item_qty ; $i++)
					$requests[] = $request;
			}
		}

		return $requests;
	}

	/**
	 * box_shipping function.
	 *
	 * @access private
	 * @param mixed $package
	 * @return void
	 */
	private function box_shipping( $package, $params=array() ) {
		global $woocommerce;
		$pre_packed_contents = array();
		$requests = array();
		
		if ( ! class_exists( 'PH_UPS_Boxpack' ) ) {
			include_once 'class-wf-packing.php';
		}
		if ( ! class_exists( 'PH_UPS_Boxpack_Stack' ) ) {
			include_once 'class-wf-packing-stack.php';
		}
		
		volume_based:
		if(isset($this->mode) && $this->mode=='stack_first'){
			$boxpack = new PH_UPS_Boxpack_Stack();
		}
		else{
			$boxpack = new PH_UPS_Boxpack($this->mode, $this->exclude_box_weight);
		}

		// Add Standard UPS boxes
		if ( ! empty( $this->ups_packaging )  ) {
			foreach ( $this->ups_packaging as $key => $box_code ) {

				$box 	= $this->packaging[ $box_code ];
				
				$newbox = $boxpack->add_box( $box['length'], $box['width'], $box['height'] );
				$newbox->set_inner_dimensions( $box['length'], $box['width'], $box['height'] );
				
				if ( $box['weight'] ) {
					$newbox->set_max_weight( $box['weight'] );
				}
				
				$newbox->set_id($box_code);

				if (isset($this->mode) && $this->mode=='stack_first') {

					$newbox = $boxpack->add_box( $box['height'], $box['width'], $box['length'] );
					$newbox->set_inner_dimensions( $box['height'], $box['width'], $box['length'] );

					if ( $box['weight'] ) {
						$newbox->set_max_weight( $box['weight'] );
					}

					$newbox->set_id($box_code);

				}

			}
		}

		// Define boxes
		if ( ! empty( $this->boxes ) ) {
			foreach ( $this->boxes as $box ) {
				
				$newbox = $boxpack->add_box( $box['outer_length'], $box['outer_width'], $box['outer_height'], $box['box_weight'] );				
				$newbox->set_inner_dimensions( $box['inner_length'], $box['inner_width'], $box['inner_height'] );

				if ( $box['max_weight'] ) {
					$newbox->set_max_weight( $box['max_weight'] );
				}

				if (isset($this->mode) && $this->mode=='stack_first') {

					$newbox = $boxpack->add_box( $box['outer_height'], $box['outer_width'], $box['outer_length'], $box['box_weight'] );				
					$newbox->set_inner_dimensions( $box['inner_height'], $box['inner_width'], $box['inner_length'] );

					if ( $box['max_weight'] ) {
						$newbox->set_max_weight( $box['max_weight'] );
					}
				}

			}
		}
		
		// Add items
		$ctr 					= 0;
		$pre_packed_contents 	= [];
		$this->destination 		= $package['destination'];
		
		if( isset($package['contents']) ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$values['data'] = $this->wf_load_product( $values['data'] );

				$ctr++;

				$additional_products = apply_filters( 'xa_ups_alter_products_list', array($values) );	// To support product addon

				foreach( $additional_products as $values ) {
					$skip_product = apply_filters('wf_shipping_skip_product',false, $values, $package['contents']);
					if($skip_product){
						continue;
					}

					if ( !( $values['quantity'] > 0 && $values['data']->needs_shipping() ) ) {
						$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'ups-woocommerce-shipping' ), $values['data']->id ) );

						// Add by Default
						$this->diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Rate Calculation', $values['data']->id ) );

						continue;
					}

					$pre_packed = get_post_meta($values['data']->id , '_wf_pre_packed_product_var', 1);

					if( empty( $pre_packed ) || $pre_packed == 'no' ){
						$parent_product_id = wp_get_post_parent_id($values['data']->id);
						$pre_packed = get_post_meta( !empty($parent_product_id) ? $parent_product_id : $values['data']->id , '_wf_pre_packed_product', 1);
					}
					
					$pre_packed = apply_filters('wf_ups_is_pre_packed',$pre_packed,$values);

					if( !empty($pre_packed) && $pre_packed == 'yes' ){
						$pre_packed_contents[] = $values;
						$this->debug( sprintf( __( 'Pre Packed product. Skipping the product # %d', 'ups-woocommerce-shipping' ), $values['data']->id ) );

						// Add by Default
						$this->diagnostic_report( sprintf( 'Pre Packed product. Skipping the product %d from Box Packing Algorithm', $values['data']->id ) );

						continue;
					}

					if ( $values['data']->length && $values['data']->height && $values['data']->width && $values['data']->weight ) {

						$dimensions = array( $values['data']->length, $values['data']->width, $values['data']->height );

						for ( $i = 0; $i < $values['quantity']; $i ++ ) {
							$boxpack->add_item(
								number_format( wc_get_dimension( (float) $dimensions[0], $this->dim_unit ), 6, '.', ''),
								number_format( wc_get_dimension( (float) $dimensions[1], $this->dim_unit ), 6, '.', ''),
								number_format( wc_get_dimension( (float) $dimensions[2], $this->dim_unit ), 6, '.', ''),
								number_format( wc_get_weight( $values['data']->get_weight(), $this->weight_unit ), 6, '.', ''),
								$this->wf_get_insurance_amount($values['data']),
								$values['data'] // Adding Item as meta
							);
						}

					} else {
						$this->debug( sprintf( __( 'UPS Parcel Packing Method is set to Pack into Boxes. Product #%d is missing dimensions. Aborting.', 'ups-woocommerce-shipping' ), $ctr ), 'error' );

						// Add by Default
						$this->diagnostic_report( sprintf( 'UPS Parcel Packing Method is set to Pack into Boxes. Product #%d is missing dimensions. Aborting Rate Calulation.', $values['data']->id ) );

						return;
					}
				}
			}
		}
		else {
			wf_admin_notice::add_notice('No package found. Your product may be missing weight/length/width/height');

			// Add by Default
			$this->diagnostic_report( 'No package found. Your product may be missing weight/length/width/height' );
		}
		// Pack it
		$boxpack->pack();
		
		// Get packages
		$box_packages 	= $boxpack->get_packages();
		$stop_fallback 	= apply_filters( 'xa_ups_stop_fallback_from_stack_first_to_vol_based', false );

		if ( isset($this->mode) && $this->mode=='stack_first' && ! $stop_fallback && $this->stack_to_volume ) {

			foreach($box_packages as $key => $box_package) {

				$box_volume 					= $box_package->length * $box_package->width * $box_package->height ;
				$box_used_volume 				= isset($box_package->volume) && !empty($box_package->volume) ? $box_package->volume : 1;
				$box_used_volume_percentage 	= ($box_used_volume * 100 )/$box_volume;

				if (isset($box_used_volume_percentage) && $box_used_volume_percentage<44) {

					$this->mode = 'volume_based';

					$this->debug( '(FALLBACK) : Stack First Option changed to Volume Based' );

					// Add by Default
					$this->diagnostic_report( '(FALLBACK) : Stack First Method changed to Volume Based. Reason: Selected Box Volume % used is less than 44%' );

					goto volume_based;
					break;
				}
			}
		}

		$ctr=0;

		$standard_boxes_without_dimensions = array('01','24','25');

		foreach ( $box_packages as $key => $box_package ) {
			$ctr++;
			
			// if( $this->debug ) {

			// 	$this->debug( "Box Packing Result: PACKAGE " . $ctr . " (" . $key . ")\n<pre>" . print_r( $box_package,true ) . "</pre>", 'error' );
			// }

			$weight	 = $box_package->weight;
			$dimensions = array( $box_package->length, $box_package->width, $box_package->height );
					
			// UPS packaging type select, If not present set as custom box
			if(!isset($box_package->id) || empty($box_package->id) || !array_key_exists($box_package->id,$this->packaging_select)){
				$box_package->id = '02';
			}
			
			sort( $dimensions );
			// get weight, or 1 if less than 1 lbs.
			// $_weight = ( floor( $weight ) < 1 ) ? 1 : $weight;
			
			$request['Package']	=	array(
				'PackagingType'	=>	array(
					'Code'				=>	$box_package->id,
					'Description'	=>	'Package/customer supplied'
				),
				'Description'	=> 'Rate',
			);
			
			// Dimensions Mismatch error will come for some Default Boxes
			if( !in_array($box_package->id, $standard_boxes_without_dimensions) ) {

				$request['Package']['Dimensions'] = array(

					'UnitOfMeasurement'	=>	array(
						'Code'	=>	$this->dim_unit,
					),
					'Length'	=>	$dimensions[2],
					'Width'		=>	$dimensions[1],
					'Height'	=>	$dimensions[0]
				);
			}

			// Getting packed items
			$packed_items	=	array();
			if(!empty($box_package->packed) && is_array($box_package->packed)){
				
				foreach( $box_package->packed as $item ) {
					$item_product	=	$item->meta;
					$packed_items[] = $item_product;					
				}
			}

			if ($this->isc || $this->cod_enable || $this->cod) {	

				$refrigeratorindicator  = 'no';
				$clinicalid 			= '';
				$cod_amount=0;

				foreach($packed_items as $key => $value) {

					if ($this->isc) {

						$clinicalvar            = get_post_meta($value->id,'_ph_ups_clinicaltrials_var',1);
						$refrigerator_var       = get_post_meta($value->id,'_ph_ups_refrigeration_var',1);

						if(empty($refrigerator_var) || !isset($refrigerator_var)) {

							$refrigerator 	= get_post_meta($value->id,'_ph_ups_refrigeration',1);

						} else {

							$refrigerator 	= $refrigerator_var;
						}

						if (empty($clinicalvar) || !isset($clinicalvar)) {

							$clinical 	= get_post_meta($value->id,'_ph_ups_clinicaltrials',1); 

						} else {

							$clinical 	= $clinicalvar;
						}

						$refrigeratorindicator  = ($refrigeratorindicator=='yes') ? $refrigeratorindicator : $refrigerator;
						$clinicalid 			= (isset($clinicalid)&&!empty($clinicalid)) ? $clinicalid : $clinical;
					}

					if ($this->cod_enable || $this->cod) {
						
						$cod_amount = $cod_amount + $value->get_price();
					}
				}

				if ($this->isc) {

					if ($refrigeratorindicator=='yes') {

						$request['Package']['PackageServiceOptions']['RefrigerationIndicator'] = '1';
					}

					if ( isset($clinicalid) && !empty($clinicalid) && isset($_GET['wf_ups_shipment_confirm']) ) {

						$request['Package']['PackageServiceOptions']['ClinicaltrialsID'] = $clinicalid;
					}
				}
			}
			
			if((isset($params['service_code'])&&$params['service_code']==92)||($this->service_code==92))// Surepost Less Than 1LBS
			{
				if($this->weight_unit=='LBS'){ // make sure weight in pounds
					$weight_ozs=$weight*16;
				}else{
					$weight_ozs=$weight*35.274; // From KG
				}
				
				$request['Package']['PackageWeight']	=	array(
					'UnitOfMeasurement'	=>	array(
						'Code'	=>	'OZS'
					),
					'Weight'	=>	$weight_ozs
				);
				
			}else{

				// Invalid Weight Error if Weight is less than 0.05 for Estimated Delivery Option
				if( $weight < 0.05 ) {
					$weight = 0.05;
				}

				$request['Package']['PackageWeight']	=	array(
					'UnitOfMeasurement'	=>	array(
						'Code'	=>	$this->weight_unit
					),
					'Weight'	=>	$weight
				);
			}
			
			if( $this->insuredvalue || $this->cod || $this->cod_enable) {
				
				// InsuredValue
				if( $this->insuredvalue ) {
					$request['Package']['PackageServiceOptions']['InsuredValue']	=	array(
							'CurrencyCode'	=>	$this->get_ups_currency(),
							'MonetaryValue'	=>	(string)round(($box_package->value / $this->conversion_rate),2)
						);
				}

				//COD
				if ( ($this->cod &&isset( $_GET['wf_ups_shipment_confirm'])) || ($this->cod_enable && !isset( $_GET['wf_ups_shipment_confirm'])) ) {

					if ( ! $this->is_shipment_level_cod_required($this->destination['country']) ) {

						$codfundscode = in_array( $this->destination['country'], array('AR', 'BR', 'CL') ) ? 9 : 0;

						$request['Package']['PackageServiceOptions']['COD']	=	array(
							'CODCode'		=>	3,
							'CODFundsCode'	=>	$codfundscode,
							'CODAmount'		=>	array(
								'MonetaryValue'	=>	(string) round( $cod_amount, 2),
							),
						);
					}
				}	
			}
			
			//Adding all the items to the stored packages
			if( isset($box_package->unpacked) && $box_package->unpacked && isset($box_package->obj) ) {
				$request['Package']['items'] = array($box_package->obj);
			}
			else {
				$request['Package']['items'] = $packed_items;
			}
			// Direct Delivery option
			$directdeliveryonlyindicator = ! empty($packed_items) ? $this->get_individual_product_meta( $packed_items, '_wf_ups_direct_delivery' ) : $this->get_individual_product_meta( array($box_package), '_wf_ups_direct_delivery' ); // else part is for unpacked item
			if( $directdeliveryonlyindicator == 'yes' ) {
				$request['Package']['DirectDeliveryOnlyIndicator'] = $directdeliveryonlyindicator;
			}
			
			// Delivery Confirmation
			if(isset($params['delivery_confirmation_applicable']) && $params['delivery_confirmation_applicable'] == true){
				
				$signature_option = $this->get_package_signature($request['Package']['items']) ;	//Works on both packed and unpacked items
				$signature_option = $signature_option < $this->ph_delivery_confirmation ? $this->ph_delivery_confirmation : $signature_option ;

				if (isset($request['Package']['PackageServiceOptions']) && isset($request['Package']['PackageServiceOptions']['COD'])) {

					$this->diagnostic_report( 'UPS : COD Shipment. Signature will not be applicable.' );
				}

				if(!empty($signature_option)&& ($signature_option > 0) && ( !isset($request['Package']['PackageServiceOptions']) || (isset($request['Package']['PackageServiceOptions']) && !isset($request['Package']['PackageServiceOptions']['COD'])) ) ) {

					$this->diagnostic_report( 'UPS : Require Signature - '. $signature_option );

					$request['Package']['PackageServiceOptions']['DeliveryConfirmation']['DCISType']= $signature_option;
				}
			}
			
			$requests[] = $request;
		}
		//add pre packed item with the package
		if( !empty($pre_packed_contents) ){
			$prepacked_requests = $this->wf_ups_add_pre_packed_product( $pre_packed_contents, $params );
			if( is_array($prepacked_requests) ) {
				$requests = array_merge($requests, $prepacked_requests);
			}
		}
		return $requests;
	}

	/**
	 * weight_based_shipping function.
	 *
	 * @access private
	 * @param mixed $package
	 * @return void
	 */
	private function weight_based_shipping($package, $params = array()) {
		global $woocommerce;
		$pre_packed_contents = array();
		if ( ! class_exists( 'WeightPack' ) ) {
			include_once 'weight_pack/class-wf-weight-packing.php';
		}
		$weight_pack=new WeightPack($this->weight_packing_process);
		$weight_pack->set_max_weight($this->box_max_weight);
		
		$package_total_weight = 0;
		$insured_value = 0;

		$requests = array();
		$ctr = 0;
		$this->destination = $package['destination'];
		foreach ($package['contents'] as $item_id => $values) {
			$values['data'] = $this->wf_load_product( $values['data'] );
			$ctr++;
			
			$additional_products = apply_filters( 'xa_ups_alter_products_list', array($values) );	// To support product addon
			foreach ( $additional_products as $values ) {
				$skip_product = apply_filters('wf_shipping_skip_product',false, $values, $package['contents']);
				if($skip_product){
					continue;
				}
				
				if (!($values['quantity'] > 0 && $values['data']->needs_shipping())) {
					$this->debug(sprintf(__('Product # %d is virtual. Skipping.', 'ups-woocommerce-shipping'), $values['data']->id));

					// Add by Default
					$this->diagnostic_report( sprintf( 'Product # %d is virtual. Skipping from Rate Calculation.', $values['data']->id) );

					continue;
				}

				if (!$values['data']->get_weight()) {
					$this->debug(sprintf(__('Product # %d is missing weight. Aborting.', 'ups-woocommerce-shipping'), $values['data']->id), 'error');

					// Add by Default
					$this->diagnostic_report( sprintf( 'Product # %d is missing weight. Aborting Rate Calculation.', $values['data']->id) );

					return;
				}
				
				$pre_packed = get_post_meta($values['data']->id , '_wf_pre_packed_product_var', 1);

				if( empty( $pre_packed ) || $pre_packed == 'no' ){
					$parent_product_id = wp_get_post_parent_id($values['data']->id);
					$pre_packed = get_post_meta( !empty($parent_product_id) ? $parent_product_id : $values['data']->id , '_wf_pre_packed_product', 1);
				}

				$pre_packed = apply_filters('wf_ups_is_pre_packed',$pre_packed,$values);
				
				if( !empty($pre_packed) && $pre_packed == 'yes' ){
					$pre_packed_contents[] = $values;
					$this->debug( sprintf( __( 'Pre Packed product. Skipping the product # %d', 'ups-woocommerce-shipping' ), $values['data']->id ) );

					// Add by Default
					$this->diagnostic_report( sprintf( 'Pre Packed product. Skipping the product %d from Weight Packing Algorithm', $values['data']->id) );

					continue;
				}

				$product_weight = $this->xa_get_volumatric_products_weight( $values['data'] );
				$weight_pack->add_item(wc_get_weight( $product_weight, $this->weight_unit ), $values['data'], $values['quantity']);
			}
		}
		
		$pack	=	$weight_pack->pack_items();		
		$errors	=	$pack->get_errors();
		if( !empty($errors) ){
			//do nothing
			return;
		} else {
			$boxes		=	$pack->get_packed_boxes();
			$unpacked_items	=	$pack->get_unpacked_items();
			
			$insured_value			=	0;
			
			if(isset($this->order)){
				$order_total	=	$this->order->get_total();
			}
			
			
			$packages		=	array_merge( $boxes,	$unpacked_items ); // merge items if unpacked are allowed
			$package_count	=	sizeof($packages);
			
			// get all items to pass if item info in box is not distinguished
			$packable_items	=	$weight_pack->get_packable_items();
			$all_items		=	array();
			if(is_array($packable_items)){
				foreach($packable_items as $packable_item){
					$all_items[]	=	$packable_item['data'];
				}
			}
			
			foreach($packages as $package) {

				$packed_products 		= array();
				$insured_value  		= 0;
				$refrigeratorindicator	= 'no';
				$clinicalid 			= '';
				$cod_amount 			= 0;

				if (!empty($package['items'])) {

					foreach($package['items'] as $item) {
						
						if ($this->insuredvalue ) {
							
							$insured_value 	= $insured_value + $this->wf_get_insurance_amount($item);
						}

						if ($this->isc) {

							$clinicalvar            = get_post_meta($item->id,'_ph_ups_clinicaltrials_var',1);
							$refrigerator_var       = get_post_meta($item->id,'_ph_ups_refrigeration_var',1);

							if(empty($refrigerator_var) || !isset($refrigerator_var)) {

								$refrigerator 	= get_post_meta($item->id,'_ph_ups_refrigeration',1);
							
							} else {

								$refrigerator 	= $refrigerator_var;
							}

							if (empty($clinicalvar) || !isset($clinicalvar)) {

								$clinical 	= get_post_meta($item->id,'_ph_ups_clinicaltrials',1); 
							
							} else {

								$clinical 	= $clinicalvar;
							}

							$refrigeratorindicator  = ($refrigeratorindicator=='yes') ? $refrigeratorindicator : $refrigerator;
							$clinicalid 			= ( isset($clinicalid) && !empty($clinicalid) ) ? $clinicalid : $clinical;
							
						}

						if ($this->cod_enable ||$this->cod) {

							$cod_amount = $cod_amount + $item->get_price();
						}
					}

				} elseif ( isset($order_total) && $package_count) {

					$insured_value	=	$order_total / $package_count;

					if ($this->cod_enable || $this->cod) {

						$cod_amount = $order_total / $package_count;
					}
				}
				
				$packed_products	=	isset($package['items']) ? $package['items'] : $all_items;
				// Creating package request
				$package_total_weight	=	$package['weight'];
				
				$request['Package']	=	array(
					'PackagingType'	=>	array(
						'Code'			=>	'02',
						'Description'	=>	'Package/customer supplied',
					),
					'Description'	=>	'Rate',
				);
									
				if ((isset($params['service_code']) && $params['service_code'] == 92) || ($this->service_code == 92)) { // Surepost Less Than 1LBS
					if ($this->weight_unit == 'LBS') { // make sure weight in pounds
						$weight_ozs = $package_total_weight * 16;
					} else {
						$weight_ozs = $package_total_weight * 35.274; // From KG
					}
					
					$request['Package']['PackageWeight']	=	array(
						'UnitOfMeasurement'	=>	array(
							'Code'	=>	'OZS'
						),
						'Weight'	=>	$weight_ozs
					);
				} else {

					// Invalid Weight Error if Weight less is than 0.05 for Estimated Delivery Option
					if( $package_total_weight < 0.05 ) {
						$package_total_weight = 0.05;
					}
					
					$request['Package']['PackageWeight']	=	array(
						'UnitOfMeasurement'	=>	array(
							'Code'	=>	$this->weight_unit
						),
						'Weight'	=>	$package_total_weight
					);
				}

				// InsuredValue
				if ($this->insuredvalue ) {
					$request['Package']['PackageServiceOptions']['InsuredValue']	=	array(
						'CurrencyCode'	=>	$this->get_ups_currency(),
						'MonetaryValue'	=>	(string) round(($insured_value / $this->conversion_rate),2),
					);
				}

				if ($this->isc) {

					if( $refrigeratorindicator == 'yes' ) {

						$request['Package']['PackageServiceOptions']['RefrigerationIndicator'] = '1';
					}

					if( isset($clinicalid) && !empty($clinicalid) && isset($_GET['wf_ups_shipment_confirm']) ) {

						$request['Package']['PackageServiceOptions']['ClinicaltrialsID'] = $clinicalid;	
					}
				}

				if ( ($this->cod && isset( $_GET['wf_ups_shipment_confirm'])) || ($this->cod_enable && !isset( $_GET['wf_ups_shipment_confirm'])) ) {

					if ( ! $this->is_shipment_level_cod_required($this->destination['country']) ) {

						$codfundscode = in_array( $this->destination['country'], array('AR', 'BR', 'CL') ) ? 9 : 0;

						$request['Package']['PackageServiceOptions']['COD']	=	array(

							'CODCode'		=>	3,
							'CODFundsCode'	=>	$codfundscode,
							'CODAmount'		=>	array(
								'MonetaryValue'	=>	(string) round( $cod_amount, 2),
							),
						);
					}
				}
				
				// Direct Delivery option
				$directdeliveryonlyindicator = $this->get_individual_product_meta( $packed_products, '_wf_ups_direct_delivery' );
				if( $directdeliveryonlyindicator == 'yes' ) {
					$request['Package']['DirectDeliveryOnlyIndicator'] = $directdeliveryonlyindicator;
				}
				
				// Delivery Confirmation
				if(isset($params['delivery_confirmation_applicable']) && $params['delivery_confirmation_applicable'] == true){
					
					$signature_option = $this->get_package_signature($packed_products);
					$signature_option = $signature_option < $this->ph_delivery_confirmation ? $this->ph_delivery_confirmation : $signature_option ;

					if (isset($request['Package']['PackageServiceOptions']) && isset($request['Package']['PackageServiceOptions']['COD'])) {
						$this->diagnostic_report( 'UPS : COD Shipment. Signature will not be applicable.' );
					}

					if( !empty($signature_option) && ($signature_option > 0) && ( !isset($request['Package']['PackageServiceOptions']) || (isset($request['Package']['PackageServiceOptions']) && !isset($request['Package']['PackageServiceOptions']['COD'])) ) ) {

						$this->diagnostic_report( 'UPS : Require Signature - '. $signature_option );

						$request['Package']['PackageServiceOptions']['DeliveryConfirmation']['DCISType']= $signature_option;
					}
				}

				$request['Package']['items'] = $package['items'];	    //Required for numofpieces in case of worldwidefreight
				$requests[] = $request;
			}
		}
		//add pre packed item with the package
		if( !empty($pre_packed_contents) ){
			$prepacked_requests = $this->wf_ups_add_pre_packed_product( $pre_packed_contents, $params );
			if( is_array($prepacked_requests) ) {
				$requests = array_merge($requests, $prepacked_requests);
			}
		}		
		return $requests;
	}
	
	/**
	 * Get Volumetric weight .
	 * @param object wf_product | wc_product object .
	 * @return float Volumetric weight if it is higher than product weight else actual product weight.
	 */
	private function xa_get_volumatric_products_weight( $values ) {

		if( ! empty($this->settings['volumetric_weight']) && $this->settings['volumetric_weight'] == 'yes' ) {

			$length = wc_get_dimension( (float) $values->get_length(), 'cm' );
			$width 	= wc_get_dimension( (float) $values->get_width(), 'cm' );
			$height = wc_get_dimension( (float) $values->get_height(), 'cm' );
			if( $length != 0 && $width != 0 && $height !=0 ) {
				$volumetric_weight = $length * $width * $height /  5000; // Divide by 5000 as per fedex standard
			}
		}
		
		$weight = $values->get_weight();

		if( ! empty($volumetric_weight) ) {
			$volumetric_weight = wc_get_weight( $volumetric_weight, $this->wc_weight_unit, 'kg' );
			if( $volumetric_weight > $weight ) {
				$weight = $volumetric_weight;
			}
		}
		return $weight;		
	}

	/**
	* Get UPS package weight converted for rate request for service 92
	* @param $package_request array UPS package request array
	* @return $service_code array UPS Package request
	*/
	public function convert_weight( $package_request, $service_code = null){
		if ( $service_code = 92 ) { // Surepost Less Than 1 LBS
			if ($this->weight_unit == 'LBS') { // make sure weight in pounds
				$weight_ozs = (float) $package_request['Package']['PackageWeight']['Weight'] * 16;
			} else {
				$weight_ozs = (float) $package_request['Package']['PackageWeight']['Weight'] * 35.274; // From KG
			}
			
			$package_request['Package']['PackageWeight']	=	array(
				'UnitOfMeasurement'	=>	array(
					'Code'	=>	'OZS'
				),
				'Weight'	=>	$weight_ozs
			);
		}
		return $package_request;
	}
	
	/**
	 * @param wf_product object
	 * @return int the Insurance amount for the product.
	 */
	public function wf_get_insurance_amount( $product ) {

		if( WC()->version > 2.7 ) {
			$product 	= wc_get_product($product->get_id());
			$parent_id 	= is_object($product) ? $product->get_parent_id() : 0;
			$product_id = ! empty($parent_id) ? $parent_id : $product->get_id();
		}
		else {
			$product_id = ($product instanceof WC_Product_Variable) ? $product->parent->id : $product->id ;
		}
		$insured_price = get_post_meta( $product_id, '_wf_ups_custom_declared_value', true );
		$meta_exists=metadata_exists('post', $product_id, '_wf_ups_custom_declared_value');
		return ( ( !$meta_exists || (empty($insured_price) && !is_numeric($insured_price)) ) ? (float) $product->get_price() : (float) $insured_price );
	}

	/**
	 * wf_get_api_rate_box_data function.
	 *
	 * @access public
	 * @return requests
	 */
	public function wf_get_api_rate_box_data( $package, $packing_method, $params = array()) {
		$this->packing_method	= $packing_method;
		$requests 				= $this->get_package_requests($package, $params);

		return $requests;
	}
	
	public function wf_set_cod_details($order){
		if($order->id){
			$this->cod=get_post_meta($order->id,'_wf_ups_cod',true);
			$this->cod_total=$order->get_total();
		}
	}
	
	public function wf_set_service_code($service_code){
		$this->service_code=$service_code;
	}
	
	/**
	 * Get product meta data for single occurance in request
	 * @param array|object $products array of wf_product object
	 * @param string $option
	 * @return mixed Return option value
	 */
	public function get_individual_product_meta( $products, $option = '' ) {
		$meta_result = '';
		foreach( $products as $product ) {
			if( empty($meta_result) ) {
				$meta_result = ! empty($product->obj) ? $product->obj->get_meta($option) : '';	// $product->obj actual product
			}
		}
		
		return $meta_result;
	}
	
	public function get_package_signature($products){
		$higher_signature_option = 0;
		foreach( $products as $product ){

			$product 		= wc_get_product($product->get_id());

			if( !empty($product) && ($product instanceof WC_Product) ) {
				
				$parent_id 		= is_object($product) ? $product->get_parent_id() : 0;
				$product_id 	= ! empty($parent_id) ? $parent_id : $product->get_id();

				$wf_dcis_type = get_post_meta($product_id, '_wf_ups_deliveryconfirmation', true);
				if( empty($wf_dcis_type) || !is_numeric ( $wf_dcis_type )){
					$wf_dcis_type = 0;
				}

				if( $wf_dcis_type > $higher_signature_option ){
					$higher_signature_option = $wf_dcis_type;
				}
			}
		}
		return $higher_signature_option;
	}
	
	public function get_ups_currency(){
		return $this->currency_type;
	}
	
	public function wf_array_to_xml($tags,$full_xml=false){//$full_xml true will contain <?xml version
		$xml_str	=	'';
		foreach($tags as $tag_name	=> $tag){
			$out	=	'';
			try{
				$xml = new SimpleXMLElement('<'.$tag_name.'/>');
				
				if(is_array($tag)){
					$this->array2XML($xml,$tag);
					
					if(!$full_xml){
						
						if( function_exists( 'dom_import_simplexml' ) )
						{
							$dom	=	dom_import_simplexml($xml);
							$out.=$dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);

						}else{
							$this->debug( __( 'The DOMElement class is not enabled for your site, so UPS Packages are not created. Please contact your Hosting Provider to enable DOMElement class for your site and try again', 'ups-woocommerce-shipping' ) );
						}
						
					}
					else{
						$out.=$xml->saveXML();
					}
				}
				else{
					$out.=$tag;
				}
				
			}catch(Exception $e){
				// Do nothing
			}
			$xml_str.=$out;
		}
		// echo preg_replace('<[\/]*item[0-9]>', '', $xml_str);
		return $xml_str;
	}
	
	public function array2XML($obj, $array)
	{
		foreach ($array as $key => $value)
		{
			if(is_numeric($key))
				$key = 'item' . $key;

			if (is_array($value))
			{
				if(!array_key_exists('multi_node', $value))
				{
					$node = $obj->addChild($key);
					$this->array2XML($node, $value);
				}else{
					unset($value['multi_node']);
					foreach($value as $node_value){
						$this->array2XML($obj, $node_value);
					}
				}					
			}
			else
			{
				$obj->addChild($key, $value);
			}
		}
	}

	/**
	 * Check whether Shipment Level COD is required or not.
	 * @param string $country_code
	 * @return bool True if Shipment Level COD is required else false.
	 */
	public function is_shipment_level_cod_required($country_code){
		if( ! $country_code ) {
			return false;
		}
		// United Arab Emirates, Russia, European Countries
		$countries = array(
			'AE','RU','UA','FR','ES','SE','NO','DE','FI','PL','IT',
			'UK','RO','BY','EL','BG','IS','HU','PT','AZ','AT',
			'CZ','RS','IE','GE','LT','LV','HR','BA','SK','EE',
			'DK','CH','NL','MD','BE','AL','MK','TR','SI','ME',
			'XK','LU','MT','LI',
		);
		return in_array( $country_code, $countries );
	}
	
	/*
	 * function to create package for pre packed items
	 *
	 * @ since 3.3.1
	 * @ access private
	 * @ params pre_packed_items
	 * @ return requests
	 */
	 private function wf_ups_add_pre_packed_product($pre_packed_items, $params = array() )
	 {
		 $requests = array();
		 foreach ( $pre_packed_items as $item_id => $values ) {
			if ( !( $values['quantity'] > 0 && $values['data']->needs_shipping() ) ) {
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'ups-woocommerce-shipping' ), $values['data']->id ) );

				// Add by Default
				$this->diagnostic_report( sprintf( 'Product %d is virtual. Skipping from Rate Calculation', $values['data']->id) );

				continue;
			}
			
			 if ( ! $values['data']->get_weight() ) {
				$this->debug(sprintf(__('Product #%d is missing weight. Aborting.', 'ups-woocommerce-shipping'), $values['data']->id), 'error');

				// Add by Default
				$this->diagnostic_report( sprintf( 'Product %d is  missing weight. Aborting Rate Calculation', $values['data']->id) );

				return;
			}
			$weight = wc_get_weight( $values['data']->get_weight(), $this->weight_unit );
			
			if ( $values['data']->length && $values['data']->height && $values['data']->width && $values['data']->weight ) {
				$dimensions = array( $values['data']->length, $values['data']->height, $values['data']->width );
				sort( $dimensions );
			} else {
				$this->debug( sprintf( __( 'Product is missing dimensions. Aborting.', 'ups-woocommerce-shipping' )), 'error' );

				// Add by Default
				$this->diagnostic_report( sprintf( 'Product %d is  missing dimensions. Aborting Rate Calculation', $values['data']->id) );

				return;
			}
			
			$cart_item_qty = $values['quantity'];
		
			$request['Package']	=	array(
				'PackagingType'	=>	array(
					'Code'			=>	'02',
					'Description'	=>	'Package/customer supplied'
				),
				'Description'	=>	'Rate',
			);
			
			// Direct Delivery option
			$directdeliveryonlyindicator = $this->get_individual_product_meta( array($values['data']), '_wf_ups_direct_delivery' );
			if( $directdeliveryonlyindicator == 'yes' ) {
				$request['Package']['DirectDeliveryOnlyIndicator'] = $directdeliveryonlyindicator;
			}
			
			if ( $values['data']->length && $values['data']->height && $values['data']->width ) {
				$request['Package']['Dimensions']	=	array(
					'UnitOfMeasurement'	=>	array(
						'Code'	=>	$this->dim_unit
					),
					'Length'	=>	$dimensions[2],
					'Width'		=>	$dimensions[1],
					'Height'	=>	$dimensions[0]
				);
			}
			if((isset($params['service_code'])&&$params['service_code']==92)||($this->service_code==92))// Surepost Less Than 1LBS
			{
				if($this->weight_unit=='LBS'){ // make sure weight in pounds
					$weight_ozs=$weight*16;
				}else{
					$weight_ozs=$weight*35.274; // From KG
				}
				$request['Package']['PackageWeight']	=	array(
					'UnitOfMeasurement'	=>	array(
						'Code'	=>	'OZS'
					),
					'Weight'	=>	$weight_ozs,
				);
			}else{

				// Invalid Weight Error if Weight is less than 0.05 for Estimated Delivery Option
				if( $weight < 0.05 ) {
					$weight = 0.05;
				}
				
				$request['Package']['PackageWeight']	=	array(
					'UnitOfMeasurement'	=>	array(
						'Code'	=>	$this->weight_unit
					),
					'Weight'	=>	$weight,
				);
			}

			
			if( $this->insuredvalue || $this->cod || $this->cod_enable) {
				
				// InsuredValue
				if( $this->insuredvalue ) {
					
					$request['Package']['PackageServiceOptions']['InsuredValue']	=	array(
						'CurrencyCode'	=>	$this->get_ups_currency(),
						'MonetaryValue'	=>	(string) round(( $this->wf_get_insurance_amount($values['data']) * $this->conversion_rate ),2)
					);
				}

				//COD
				if ( ($this->cod &&isset( $_GET['wf_ups_shipment_confirm'])) || ($this->cod_enable && !isset( $_GET['wf_ups_shipment_confirm'])) ) {

					if ( ! $this->is_shipment_level_cod_required($this->destination['country']) ) {

						$cod_amount 	= $values['data']->get_price();
						$codfundscode 	= in_array( $this->destination['country'], array('AR', 'BR', 'CL') ) ? 9 : 0;

						$request['Package']['PackageServiceOptions']['COD']	= array(
							'CODCode'		=>	3,
							'CODFundsCode'	=>	$codfundscode,
							'CODAmount'		=>	array(
								'MonetaryValue'	=>	(string) round( $cod_amount, 2),
							),
						);
					}
				}
			}
			
			// Delivery Confirmation
			if(isset($params['delivery_confirmation_applicable']) && $params['delivery_confirmation_applicable'] == true){

				$signature_option = $this->get_package_signature(array($values['data']));
				$signature_option = $signature_option < $this->ph_delivery_confirmation ? $this->ph_delivery_confirmation : $signature_option ;

				if (isset($request['Package']['PackageServiceOptions']) && isset($request['Package']['PackageServiceOptions']['COD'])) {
					$this->diagnostic_report( 'UPS : COD Shipment. Signature will not be applicable.' );
				}

				if(!empty($signature_option)&& ($signature_option > 0) && ( !isset($request['Package']['PackageServiceOptions']) || (isset($request['Package']['PackageServiceOptions']) && !isset($request['Package']['PackageServiceOptions']['COD'])) ) ) {

					$this->diagnostic_report( 'UPS : Require Signature - '. $signature_option );

					$request['Package']['PackageServiceOptions']['DeliveryConfirmation']['DCISType']= $signature_option;
				}
			}
			
			if ($this->isc) {

				$refrigeratorindicator 	= 'no';
				$clinicalid 			= '';
				$clinicalvar 			= get_post_meta($values['data']->id,'_ph_ups_clinicaltrials_var',1);
				$refrigerator_var 		= get_post_meta($values['data']->id,'_ph_ups_refrigeration_var',1);
				
				if(empty($refrigerator_var) || !isset($refrigerator_var)) {

					$refrigerator 	= get_post_meta($values['data']->id,'_ph_ups_refrigeration',1);

				} else {

					$refrigerator 	= $refrigerator_var;
				}

				if (empty($clinicalvar) || !isset($clinicalvar)) {

					$clinical 	= get_post_meta($values['data']->id,'_ph_ups_clinicaltrials',1); 

				} else {

					$clinical 	= $clinicalvar;
				}
				
				$refrigeratorindicator 	= ($refrigeratorindicator=='yes') ? $refrigeratorindicator : $refrigerator;
				$clinicalid 			= (isset($clinicalid)&&!empty($clinicalid)) ? $clinicalid : $clinical;

				if ($refrigeratorindicator=='yes') {
					
					$request['Package']['PackageServiceOptions']['RefrigerationIndicator'] = '1';
				}
				
				if ( isset($clinicalid) && !empty($clinicalid) && isset($_GET['wf_ups_shipment_confirm']) ) {

					$request['Package']['PackageServiceOptions']['ClinicaltrialsID'] = $clinicalid;
				}
			}
			
			//Setting the product object in package request	
			$request['Package']['items'] = array($values['data']->obj);

			for ( $i=0; $i < $cart_item_qty ; $i++)
				$requests[] = $request;

		 }
		 return $requests;
	}

	function wf_load_product( $product ){
		if( !class_exists('wf_product') ){
			include_once('class-wf-legacy.php');
		}
		if( !$product ){
			return false;
		}
		return ( WC()->version < '2.7.0' ) ? $product : new wf_product( $product );
	}

	/**
	 * Minimum Weight Required by UPS.
	 * @param array $ups_packages UPS packages generated by packaging Algorithms
	 * @return array UPS packages
	 */
	public function ups_minimum_weight_required( $ups_packages ) {

		switch( $this->origin_country ) {
			case 'IL' 	:	$min_weight = 0.5;
							break;
			default		:	$min_weight = 0.0001;
		}

		foreach( $ups_packages as &$ups_package ) {
			if( (double) $ups_package['Package']['PackageWeight']['Weight'] < $min_weight ) {
				if( $this->debug ) {
					$this->debug( sprintf( __( "Package Weight has been reset to Minimum Weight. [ Actual Weight - %lf Minimum Weight - %lf ]", 'ups-woocommerce-shipping' ), $ups_package['Package']['PackageWeight']['Weight'], $min_weight ) );
				}

				// Add by Default
				$this->diagnostic_report( sprintf( 'Package Weight has been reset to Minimum Weight. [ Actual Weight - %lf Minimum Weight - %lf ]', $ups_package['Package']['PackageWeight']['Weight'], $min_weight ) );
				
				$ups_package['Package']['PackageWeight']['Weight'] = $min_weight;
			}
		}
		return $ups_packages;
	}
	public function copyArray($source){
		$result = array();

		foreach($source as $key => $item){
			$result[$key] = (is_array($item) ? $this->copyArray($item) : $item);
		}

		return $result;
	}
	
}