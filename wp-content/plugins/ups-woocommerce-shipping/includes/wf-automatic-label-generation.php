<?php
	$shipping_setting =get_option('woocommerce_wf_shipping_ups_settings');

	if(isset($shipping_setting['automate_package_generation']) && $shipping_setting['automate_package_generation']=='yes' )
	{
		add_action( 'woocommerce_thankyou', 'wf_automatic_package_and_label_generation_ups' );
	}
	function wf_automatic_package_and_label_generation_ups( $order_id )
	{
		$order 					= new WC_Order($order_id);
		$order_status 			= $order->get_status();
		$ups_shipping_setting 	= get_option('woocommerce_wf_shipping_ups_settings');
		$allowed_order_status 	= apply_filters( 'xa_automatic_label_generation_allowed_order_status', array('processing'), $order_status, $order_id );	// Allowed order status for automatic label generation

		// Add transient to check for duplicate label generation
		$transient			 	= 'ups_auto_generate' . md5( $order_id );
		$processed_order		= get_transient( $transient );

		// If requested order is already processed, return.
		if( $processed_order ) {
			return;
		}
		
		if( ! in_array($order_status, $allowed_order_status) ) {
			if($ups_shipping_setting['debug'] == 'yes') {
				WC_Admin_Meta_Boxes::add_error( __( "Since Order Status is ", 'ups-woocommerce-shipping' ).$order_status.__( ". Automatic Package / Label generation has been suspended.", 'ups-woocommerce-shipping' ) );
			}
			return;
		}
		
		$order_items = $order->get_items();
		if( empty($order_items) && class_exists('WC_Admin_Meta_Boxes') ) {
			WC_Admin_Meta_Boxes::add_error( __( 'UPS - No product Found. Please check the products in order.', 'ups-woocommerce-shipping' ) );
			return;
		}
		//  Automatically Generate Packages		
		$current_minute=(integer)date('i');

		// Set transient for 2 min to avoid duplicate label generation
		set_transient( $transient, $order_id, 120 );
		
		$ups_admin_class 	= new WF_Shipping_UPS_Admin();

		$ups_admin_class->ph_ups_auto_generate_packages( base64_encode($order_id), $ups_shipping_setting, md5($current_minute) );
	}

	if( isset($shipping_setting['automate_label_generation']) && $shipping_setting['automate_label_generation']=='yes' )
	{	
		add_action('wf_after_package_generation','wf_auto_genarate_label_ups',2,2);
	}

	if( !function_exists('xa_get_shipping_method') ){
		function xa_get_shipping_method($order_id){
			if(!$order_id)
				return false;
			$return = null;

			$order = new WC_Order( $order_id );
			$order_shipping_method 		= current( $order->get_items( 'shipping' ) );
			if(!$order_shipping_method)
			{
				return '';
			}
			// From UPS version 3.9.14.20
			$order_shipping_method_ups 	= $order_shipping_method->get_meta('_xa_ups_method');
			if( ! empty($order_shipping_method_ups) ) {
				$order_shipping_method = $order_shipping_method_ups['id'];
			}
			// Till UPS version 3.9.14.19
			else {
				$order_shipping_method = ( WC()->version > '2.7' ) ? ( is_object($order_shipping_method) ? $order_shipping_method->get_method_id() : '' ) : ( isset($order_shipping_method['method_id']) ? $order_shipping_method['method_id'] : '' );
			}
			if( ! empty($order_shipping_method) ) {
				$service_code=explode(':',$order_shipping_method);
				if( $service_code[0] == WF_UPS_ID ) {
					$return = $service_code[1];
				}
			}

			if( empty($return) ){
				$settings = get_option( 'woocommerce_'.WF_UPS_ID.'_settings', null );

				//Origin coutry without state
				$country = explode( ':', $settings['origin_country_state'] );	//It may be in Country : State format or only country
				$origin_country = array_shift( $country ) ;

				if( $origin_country == $order->get_shipping_country() ){
					if( !empty($settings['default_dom_service']) ){
						$return = $settings['default_dom_service'];	//Return default service for domestic
					}
				}else{
					if( !empty($settings['default_int_service']) ){
						$return = $settings['default_int_service'];	//Return default service for international
					}
				}
			}
			return apply_filters( 'ph_ups_label_shipping_method', $return, $order );
		}
	}

	function wf_auto_genarate_label_ups($order_id,$package_data)
	{	
		$service_code = xa_get_shipping_method($order_id);
		// If ShopOwner wants to generate the label for customer selected service only
		if( empty($service_code) ) {
			WC_Admin_Meta_Boxes::add_error( __( 'UPS - Automatic Label Generation has been suspended. Reason - Service Code not found.', 'ups-woocommerce-shipping' ) );
			return;
		}

		
		$order = new WC_Order( $order_id );
		$settings = get_option( 'woocommerce_'.WF_UPS_ID.'_settings', null );
		//Origin coutry without state
		$country = explode( ':', $settings['origin_country_state'] );    //It may be in Country : State format or only country
		$origin_country = array_shift( $country ) ;

		if( $origin_country == $order->get_shipping_country() ){
			$service_type="domestic";
		}else{
			$service_type="international";
		}
		
		/// Automatically Generate Labels
		$current_minute=(integer)date('i');
		
		$weight=array();
		$length=array();
		$width=array();
		$height=array();
		$services=array();
		foreach($package_data as $key=>$val)
		{	
			foreach($val as $key2=>$package)
			{	
				if(isset($package['PackageWeight'])) $weight[]=$package['PackageWeight']['Weight'];

					$length[]	= ! empty($package['Dimensions']['Length']) ? $package['Dimensions']['Length'] : 0;
					$width[]	= ! empty($package['Dimensions']['Width']) ? $package['Dimensions']['Width'] : 0;
					$height[]	= ! empty($package['Dimensions']['Height']) ? $package['Dimensions']['Height'] : 0;
					
				if(isset($package['PackageServiceOptions']) && isset($package['PackageServiceOptions']['InsuredValue']) && isset($package['PackageServiceOptions']['InsuredValue']['MonetaryValue']))
				{
					$insurance[]=$package['PackageServiceOptions']['InsuredValue']['MonetaryValue'];
				}
				else{
					$insurance[]=0;
				}
				$services[]=apply_filters('ph_ups_default_service',$service_code,isset($package['PackageWeight'])?$package['PackageWeight']['Weight']:0,$service_type);
			}
		}

		$ups_admin_class 	= new WF_Shipping_UPS_Admin();

		$ups_admin_class->ph_ups_auto_create_shipment( $order_id, $settings, $weight, $length, $width, $height, $services, $insurance, md5($current_minute) );
	}

	// To send the label in email after label generation 
	// $shipping_setting['auto_email_label']=='yes' is For backward comptibility can we removed after few version release, 3.9.14.1
	if( isset($shipping_setting['auto_email_label']) && ( $shipping_setting['auto_email_label']=='yes' || is_array($shipping_setting['auto_email_label']) ) ) {
		add_action('wf_label_generated_successfully','wf_after_label_generation_ups',3,6 );
	}

	function wf_after_label_generation_ups($shipment_id,$order_id,$label_extn_code,$index,$tracking_number, $ups_label_details)
	{
		$shipping_setting2 =get_option('woocommerce_wf_shipping_ups_settings');
		$order 		= wc_get_order($order_id);
		$order_num 	= $order->get_order_number();
		
		if(isset($shipping_setting2['email_content']) && !empty($shipping_setting2['email_content']))
		{
			$emailcontent=$shipping_setting2['email_content'];
		}
		else
		{
			$emailcontent= "<html>
								<body>
									<div>Please Download the label</div>
									<a href='[DOWNLOAD LINK]' ><input type='button' value='Download the label here' /> </a>
								</body>
							</html>";
		}

		// To display product info in email sent
		if( strstr( $emailcontent, '[PRODUCTS ID]' ) || strstr( $emailcontent, '[PRODUCTS SKU]' ) || strstr( $emailcontent, '[PRODUCTS NAME]' ) || strstr( $emailcontent, '[ALL_PRODUCT INFO]' ) || strstr( $emailcontent, '[PRODUCTS QUANTITY]' ) ) {

			$stored_packages = get_post_meta( $order_id, '_wf_ups_stored_packages',false);
			foreach( $stored_packages as $stored_package ) {
				$products_in_package = array();
				if( ! empty($stored_package[$index]['Package']['items']) ) {
					foreach( $stored_package[$index]['Package']['items'] as $product ) {
						$id = $product->get_id();
						if( empty($all_product_info[$id]) ) {
							$all_product_info[$id] = array(
								'id'		=>	$id,
								'name'		=>	$product->get_name(),
								'sku'		=>	$product->get_sku(),
								'quantity'	=>	1,
							);
						}
						else {
							$all_product_info[$id]['quantity'] += 1;
						}
					}
					break;
				}
			}

			if( isset($all_product_info) && is_array($all_product_info) ) {
				// All product id in particular label
				$product_ids 			= implode( ',', array_column( $all_product_info, 'id') );
				// All product name in particular label
				$product_names 			= implode( ',', array_column( $all_product_info, 'name') );
				// All product sku in particular label
				$product_skus 			= implode( ',', array_column( $all_product_info, 'sku') );
				// All product quantity in this label
				$product_quantities 	= implode( ',', array_column( $all_product_info, 'quantity') );

				$emailcontent 	= str_replace( "[PRODUCTS ID]" , $product_ids, $emailcontent );
				$emailcontent 	= str_replace( "[PRODUCTS SKU]" , $product_skus, $emailcontent );
				$emailcontent 	= str_replace( "[PRODUCTS NAME]" , $product_names, $emailcontent );
				$emailcontent 	= str_replace( "[PRODUCTS QUANTITY]" , $product_quantities, $emailcontent );

				// To set all product info for this label
				if( strstr( $emailcontent, '[ALL_PRODUCT INFO]' ) ) {
					$product_info_in_label = xa_ups_product_info_in_order( $order, $all_product_info, 'label' );
					$emailcontent 	= str_replace( "[ALL_PRODUCT INFO]" , $product_info_in_label, $emailcontent );
				}
			}
		}

		// Get the product details in complete order not in package
		if( strstr( $emailcontent, '[ORDER_PRODUCTS]' ) ) {
			$order_products_info_html 	= xa_ups_product_info_in_order( $order);
			$emailcontent 				= str_replace( "[ORDER_PRODUCTS]", $order_products_info_html, $emailcontent);
		}

		// To display order info in email sent
		$emailcontent = str_replace( "[ORDER NO]" , $order_num, $emailcontent );
		$emailcontent = str_replace( "[ORDER AMOUNT]" , $order->get_total(), $emailcontent );

		// To display customer details info in email sent
		$customer_email	= "";
		$first_name 	= "";
		$last_name 		= "";

		if( is_object($order) )
		{
			$customer_email = $order->get_billing_email();
			$first_name 	= $order->get_billing_first_name();
			$last_name		= $order->get_billing_last_name();
		}
			
		$customer_name 	= $first_name.' '.$last_name;
		$emailcontent = str_replace( "[CUSTOMER EMAIL]" , $customer_email , $emailcontent );
		$emailcontent = str_replace( "[CUSTOMER NAME]" , $customer_name, $emailcontent );


		$to_emails = array();
		if(!empty($shipment_id))
		{
			if( is_array($shipping_setting2['auto_email_label']) ) {
				if( in_array( 'shipper', $shipping_setting2['auto_email_label'] ) ){
					$to_emails[] = $shipping_setting2['email'];
				}
				if( in_array( 'recipient', $shipping_setting2['auto_email_label'] ) ){
					$to_emails[] = $order->get_billing_email();
				}
			}
			
			$to_emails = apply_filters( 'xa_add_email_addresses_to_send_label',$to_emails, $shipment_id, $order, 10,3);
			
			$subject = ! empty($shipping_setting2['email_subject']) ? $shipping_setting2['email_subject'] : __('Shipment Label For Your Order', 'ups-woocommerce-shipping').' [ORDER_NO]';
			$subject = str_replace( '[ORDER_NO]', $order_num, $subject );
			$img_url=admin_url('/post.php?wf_ups_print_label='.base64_encode( $shipment_id.'|'.$order_id.'|'.$label_extn_code.'|'.$index.'|'.$tracking_number ));
			$body = str_replace("[DOWNLOAD LINK]",$img_url, $emailcontent);
			$headers = array('Content-Type: text/html; charset=UTF-8');

			$label = base64_decode(chunk_split($ups_label_details['GraphicImage']));


			$show_label_in_browser	= isset( $shipping_setting2['show_label_in_browser'] ) ? $shipping_setting2['show_label_in_browser'] : 'no';
			$label_format			= ! empty($shipping_setting2['label_format']) ? $shipping_setting2['label_format'] : null;

			if( strtolower($label_extn_code) == 'gif' && $show_label_in_browser != 'yes' && $label_format == 'laser_8_5_by_11' ) {
					$file_name_without_extension = 'ups_label_'.$shipment_id;
					$html_data = str_replace( 'label'.$shipment_id, $file_name_without_extension, base64_decode($ups_label_details['HTMLImage']));
					$html_file_name = WP_CONTENT_DIR."/uploads/$file_name_without_extension.html";
					file_put_contents( $html_file_name, $html_data );
					$attachments[] = $html_file_name;
			}
			elseif( strtolower($label_extn_code) == 'zpl' && strtolower($shipping_setting2['print_label_type']) == 'png' ) {
				$label_extn_code = 'png';
				$zpl_label_inverted = str_replace( "^POI", "", $label);
				$response 		= wp_remote_post( "http://api.labelary.com/v1/printers/8dpmm/labels/4x6/0/",
					array(
						'timeout'   => 70,
						'sslverify' => isset($shipping_setting2['ssl_verify']) ? $shipping_setting2['ssl_verify'] : false,
						'body'      => $zpl_label_inverted
						)
					);


				$label 	= $response["body"];
			}

			$file_name = WP_CONTENT_DIR."/uploads/ups_label_$shipment_id.".strtolower($label_extn_code);
			
			file_put_contents( $file_name, $label);		// Save the label to wp-content/uploads
			$attachments[] = $file_name;			// Attach the label to mail
			foreach($to_emails as $to)
			{
			    wp_mail( $to, $subject, $body, $headers, $attachments );
			}

			// Delete the label
			if( ! empty($html_file_name) )	unlink($html_file_name);
			unlink($file_name);
		}
	
	}

	/**
	 * Function to get the Product details in order in html.
	 * @param $order obj wc_order object
	 * @param $all_product_info array Array of products.
	 * @param $type string To get products from order or label
	 * @return string Product info in order in html.
	 */
	function xa_ups_product_info_in_order($order, $all_product_info = array(), $type = 'order' ) {

		$all_product_info_html = null;
		// Get the product details from order
		if( $type == 'order' && ( $order instanceof WC_Order ) ) {
			$order_items = $order->get_items();
			foreach( $order_items as $order_item ) {
				$id = $order_item->get_variation_id();
				if( empty($id) ) {
					$id = $order_item->get_product_id();
				}
				$product = wc_get_product($id);
				$all_product_info[$id] = array(
					'id'		=>	$id,
					'name'		=>	$product->get_name(),
					'sku'		=>	$product->get_sku(),
					'quantity'	=>	$order_item->get_quantity(),
				);
			}
		}

		foreach( $all_product_info as $product ) {
			$all_product_info_html = "
							<table border= '1 px' style = 'border-collapse: collapse;' id = xa_ups_product_info_in_$type >
								<tr>
									<th style = 'padding: 5px; text-align: center;'> Product Name </th>
									<th style = 'padding: 5px; text-align: center;'> Product id </th>
									<th style = 'padding: 5px; text-align: center;'> Product Sku </th>
									<th style = 'padding: 5px; text-align: center;'> Product Quantity</th>
								</tr>";
					foreach( $all_product_info as $product_id => $product ) {
						$all_product_info_html = $all_product_info_html . "
								<tr>
									<td style = 'padding: 5px; text-align: center;'> $product[name] </td>
									<td style = 'padding: 5px; text-align: center;'> $product[id] </td>
									<td style = 'padding: 5px; text-align: center;'> $product[sku] </td>
									<td style = 'padding: 5px; text-align: center;'> $product[quantity] </td>
								</tr>";
					}
					$all_product_info_html .= "
							</table>";
		}
		return $all_product_info_html;
	}

	if(isset($shipping_setting['allow_label_btn_on_myaccount']) && $shipping_setting['allow_label_btn_on_myaccount']=='yes' )
	{	
		add_action('woocommerce_view_order','wf_add_view_shippinglabel_button_on_myaccount_order_page_ups');
	}
	function wf_add_view_shippinglabel_button_on_myaccount_order_page_ups($order_id)
	{	
			$created_shipments_details_array 	= get_post_meta( $order_id, 'ups_created_shipments_details_array', true );
			$ups_label_details_array = get_post_meta( $order_id, 'ups_label_details_array', true );
			$ups_commercial_invoice_details = get_post_meta( $order_id, 'ups_commercial_invoice_details', true );
			if(!empty($ups_label_details_array) && is_array($ups_label_details_array)){
				foreach ( $created_shipments_details_array as $shipmentId => $created_shipments_details ){
					/////
					echo __( 'Shipment ID: ', 'ups-woocommerce-shipping' ).'</strong>'.$shipmentId.'<hr style="border-color:#0074a2">';
					// Multiple labels for each package.
					$index = 0;
					if( !empty($ups_label_details_array[$shipmentId]) ){
						foreach ( $ups_label_details_array[$shipmentId] as $ups_label_details ) {
							$label_extn_code 	= $ups_label_details["Code"];
							$tracking_number 	= isset( $ups_label_details["TrackingNumber"] ) ? $ups_label_details["TrackingNumber"] : '';
							$download_url 		= admin_url( '/?wf_ups_print_label='.base64_encode( $shipmentId.'|'.$order_id.'|'.$label_extn_code.'|'.$index.'|'.$tracking_number ) );
							$post_fix_label		= '';
							
							if( count($ups_label_details_array) > 1 ) {
								$post_fix_label = '#'.( $index + 1 );
							}
							?>
							
							<strong><?php _e( 'Tracking No: ', 'ups-woocommerce-shipping' ); ?></strong>
							<a href="http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=<?php echo $ups_label_details["TrackingNumber"] ?>" target="_blank"><?php echo $ups_label_details["TrackingNumber"] ?></a>
							<br/><br/>
							<a class="button button-primary tips" href="<?php echo $download_url; ?>" data-tip="<?php _e( 'Print Shipping Label ', 'ups-woocommerce-shipping' );echo $post_fix_label; ?>" target="_blank"><?php _e( 'Print Shipping Label ', 'ups-woocommerce-shipping' );echo $post_fix_label ?></a>
							<br/><br/>
							<hr style="border-color:#0074a2">

							<?php						
							// Return Label Link
							if(isset($created_shipments_details['return'])&&!empty($created_shipments_details['return'])){
								$return_shipment_id = current(array_keys($created_shipments_details['return'])); // only one return label is considered now
								$ups_return_label_details_array = get_post_meta( $order_id, 'ups_return_label_details_array', true );
								if( is_array($ups_return_label_details_array) && isset($ups_return_label_details_array[$return_shipment_id]) ){// check for return label accepted data
									$ups_return_label_details = $ups_return_label_details_array[$return_shipment_id];
									if( is_array($ups_return_label_details) ){
										$ups_return_label_detail = current($ups_return_label_details);
										$label_index=0;// as we took only one label so index is zero
										$return_download_url = admin_url( '/?wf_ups_print_label='.base64_encode( $return_shipment_id.'|'.$order_id.'|'.$label_extn_code.'|'.$label_index.'|return' ) );
										?>
										
										<strong><?php _e( 'Tracking No: ', 'ups-woocommerce-shipping' ); ?></strong>
										<a href="http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=<?php echo $ups_return_label_detail["TrackingNumber"] ?>" target="_blank"><?php echo $ups_return_label_detail["TrackingNumber"] ?></a>
										<br/></br>
										<a class="button button-primary tips" href="<?php echo $return_download_url; ?>" data-tip="<?php _e( 'Print Return Shipping Label ', 'ups-woocommerce-shipping' ); ?>" target="_blank"><?php _e( 'Print Return Shipping Label', 'ups-woocommerce-shipping' ); ?></a>
										<br/><br/>
										
										<?php
									}
								}
							}
							
							
							// EOF Return Label Link						
							$index = $index + 1;
						}
					}
					if(isset($ups_commercial_invoice_details[$shipmentId])){
						echo '<a class="button button-primary tips" target="_blank" href="'.admin_url( '/?wf_ups_print_commercial_invoice='.base64_encode($order_id.'|'.$shipmentId)).'" data-tip="'.__('Print Commercial Invoice', 'ups-woocommerce-shipping').'">'.__('Commercial Invoice', 'ups-woocommerce-shipping').'</a></br>';
					}
				}

			}

	}
	unset($shipping_setting);