/********************************************************************************************************************************************/
/********************************************************* General Settings *****************************************************************/
/********************************************************************************************************************************************/

jQuery(document).ready(function(){

	ph_ups_show_selected_tab(jQuery(".tab_general"),"general");

	jQuery(".tab_general").on("click",function(){
		return ph_ups_show_selected_tab(jQuery(this),"general");
	});
	jQuery(".tab_rates").on("click",function(){
		return ph_ups_show_selected_tab(jQuery(this),"rates");
	});
	jQuery(".tab_labels").on("click",function(){
		return ph_ups_show_selected_tab(jQuery(this),"label");
	});
	jQuery(".tab_int_forms").on("click",function(){
		return ph_ups_show_selected_tab(jQuery(this),"int_forms");
	});
	jQuery(".tab_packaging").on("click",function(){
		return ph_ups_show_selected_tab(jQuery(this),"packaging");
	});
	jQuery(".tab_freight").on("click",function(){
		return ph_ups_show_selected_tab(jQuery(this),"freight");
	});
	jQuery(".tab_pickup").on("click",function(){
		return ph_ups_show_selected_tab(jQuery(this),"pickup");
	});
	jQuery(".tab_help").on("click",function(){
		return ph_ups_show_selected_tab(jQuery(this),"help");
	});

	function ph_ups_show_selected_tab($element,$tab)
	{
		jQuery(".ph-ups-tabs").removeClass("nav-tab-active");
		$element.addClass("nav-tab-active");
			   
		jQuery(".ph_ups_rates_tab").closest("tr,h3").hide();
		jQuery(".ph_ups_rates_tab").next("p").hide();

		jQuery(".ph_ups_general_tab").closest("tr,h3").hide();
		jQuery(".ph_ups_general_tab").next("p").hide();

		jQuery(".ph_ups_label_tab").closest("tr,h3").hide();
		jQuery(".ph_ups_label_tab").next("p").hide();

		jQuery(".ph_ups_int_forms_tab").closest("tr,h3").hide();
		jQuery(".ph_ups_int_forms_tab").next("p").hide();

		jQuery(".ph_ups_packaging_tab").closest("tr,h3").hide();
		jQuery(".ph_ups_packaging_tab").next("p").hide();

		jQuery(".ph_ups_freight_tab").closest("tr,h3").hide();
		jQuery(".ph_ups_freight_tab").next("p").hide();

		jQuery(".ph_ups_pickup_tab").closest("tr,h3").hide();
		jQuery(".ph_ups_pickup_tab").next("p").hide();

		jQuery(".ph_ups_help_tab").closest("tr,h3").hide();
		jQuery(".ph_ups_help_tab").next("p").hide();

		jQuery(".ph_ups_"+$tab+"_tab").closest("tr,h3").show();
		jQuery(".ph_ups_"+$tab+"_tab").next("p").show();

		if( $tab == 'general' ){
			ph_ups_address_validation_options();
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_insuredvalue', '#woocommerce_wf_shipping_ups_min_order_amount_for_insurance' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_ship_from_address_different_from_shipper', '.ph_ups_different_ship_from_address' );
		}

		if( $tab == 'rates' ){
			ph_ups_load_availability_options();
			ph_ups_tradability_cart_title();
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_estimated_delivery', '.ph_ups_est_delivery' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_accesspoint_locator', '.ph_ups_accesspoint' );
			ph_ups_woocommerce_blocks();
			ph_ups_show_max_limit_error();
		}

		if( $tab == 'label' ){
			xa_ups_duties_payer_options();
			ph_ups_transportation_options();
			ph_toggle_ups_label_size();
			ph_ups_toggle_label_format();
			ph_ups_custom_description_for_label();
			ph_ups_toggle_label_email_settings();
			ph_ups_toggle_based_automate_pakage_generation();
			ph_toggle_ups_label_zoom_factor();
		}

		if( $tab == 'int_forms' ){
			ph_ups_eei_options();
			ph_ups_load_shipper_filed_options();
			ph_ups_toggle_nafta_certificate_options();

			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_nafta_co_form');
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_eei_data');
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_declaration_statement');
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_commercial_invoice_shipping');
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_discounted_price');
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_terms_of_shipment');
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_reason_export');
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_return_reason_export');
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_edi_on_label');
		}

		if( $tab == 'packaging' ){
			wf_load_packing_method_options();
			ph_toggle_box_packing_options_based_on_algorithms();
		}

		if( $tab == 'freight' ){

			ph_ups_load_third_party_billing_address();
			ph_ups_toggle_density_description();
			ph_ups_toggle_density_dimensions();

			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_class' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_packaging_type' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_holiday_pickup' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_inside_pickup' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_residential_pickup' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_weekend_pickup' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_liftgate_pickup' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_limitedaccess_pickup' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_holiday_delivery' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_inside_delivery' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_call_before_delivery' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_weekend_delivery' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_liftgate_delivery' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_limitedaccess_delivery' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_pickup_inst' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_delivery_inst' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_payment' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_enable_density_based_rating' );

			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_length' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_width' );
			ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_height' );
		}

		if( $tab == 'pickup' ){
			wf_ups_load_pickup_options();
		}

		if( $tab == 'help' ){
			jQuery(".woocommerce-save-button").hide();
		}else{
			jQuery(".woocommerce-save-button").show();	
		}
		
		return false;
	}

	/********************************************* General Settings *************************************************/
	
	// Toggle Address Validation
	jQuery('#woocommerce_wf_shipping_ups_residential').click(function(){
		ph_ups_address_validation_options();
	});

	// Toggle Address Suggestion
	jQuery('#woocommerce_wf_shipping_ups_address_validation').click(function(){
		ph_ups_address_validation_options();
	});

	jQuery('#woocommerce_wf_shipping_ups_suggested_address').click(function(){
		ph_ups_address_validation_options();
	});

	// Toggle Minimum Insurance amount
	jQuery('#woocommerce_wf_shipping_ups_insuredvalue').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_insuredvalue', '#woocommerce_wf_shipping_ups_min_order_amount_for_insurance' );
	});

	// Toggle Ship From Address Different from Shipper Address
	jQuery('#woocommerce_wf_shipping_ups_ship_from_address_different_from_shipper').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_ship_from_address_different_from_shipper', '.ph_ups_different_ship_from_address' );
	});

	/********************************************* Rates Settings ***************************************************/
	
	// Toggle Estimated delivery related data
	jQuery('#woocommerce_wf_shipping_ups_enable_estimated_delivery').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_estimated_delivery', '.ph_ups_est_delivery' );
		ph_ups_woocommerce_blocks();
	});

	// Toggle Access Point Locator related data
	jQuery('#woocommerce_wf_shipping_ups_accesspoint_locator').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_accesspoint_locator', '.ph_ups_accesspoint' );
	});

	// Toggle Availability
	jQuery('#woocommerce_wf_shipping_ups_availability').change(function(){
		ph_ups_load_availability_options();
	});

	jQuery('#woocommerce_wf_shipping_ups_ups_tradability').click(function(){
		ph_ups_tradability_cart_title();
	});

	jQuery('.ph_ups_accesspoint_limit').attr({
	   "max" : 50,
	   "min" : 1
	});

	jQuery('#woocommerce_wf_shipping_ups_accesspoint_max_limit').change(function(e){
		ph_ups_show_max_limit_error();
	});

	/********************************************* Label Settings ***************************************************/

	// Toggle Label Size
	jQuery('#woocommerce_wf_shipping_ups_show_label_in_browser').click(function(){
		ph_toggle_ups_label_size();
		ph_toggle_ups_label_zoom_factor();
	});

	// Automate Package Generation Check box checked
	jQuery('#woocommerce_wf_shipping_ups_automate_package_generation').click(function(){
		ph_ups_toggle_based_automate_pakage_generation();
	});

	// Toggle Label Format based on Print Label Type option and Display Label in Browser.
	jQuery('#woocommerce_wf_shipping_ups_print_label_type').change(function(){
		ph_ups_toggle_label_format();
		ph_toggle_ups_label_size();
		ph_toggle_ups_label_zoom_factor();
	});

	//Toggle Email Settings
	jQuery('#woocommerce_wf_shipping_ups_auto_email_label').change(function(){
		ph_ups_toggle_label_email_settings();
	});

	//Custom Description For Label
	jQuery('#woocommerce_wf_shipping_ups_label_description').change(function(){
		ph_ups_custom_description_for_label();
	});

	jQuery('#woocommerce_wf_shipping_ups_duties_and_taxes').change(function(){
		xa_ups_duties_payer_options()
	});

	jQuery('#woocommerce_wf_shipping_ups_transportation').change(function(){
		ph_ups_transportation_options()
	});
	
	/********************************************* Int Fomrs Settings ************************************************/

	// Toggle NAFTA Certificate for Commercial Invoice
	jQuery('#woocommerce_wf_shipping_ups_commercial_invoice').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_nafta_co_form');
	});

	jQuery('#woocommerce_wf_shipping_ups_commercial_invoice').click(function(){
		ph_ups_toggle_nafta_certificate_options();
	});

    // Toggle Producer Option & Blanket Period for NAFTA Certificate
	jQuery('#woocommerce_wf_shipping_ups_nafta_co_form').click(function(){
		ph_ups_toggle_nafta_certificate_options();
	});
	
	// Toggle EEI Data for Commercial Invoice
	jQuery('#woocommerce_wf_shipping_ups_commercial_invoice').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_eei_data');
	});

	jQuery('#woocommerce_wf_shipping_ups_commercial_invoice').click(function(){
		ph_ups_eei_options();
		ph_ups_load_shipper_filed_options();
	});

	jQuery('#woocommerce_wf_shipping_ups_eei_data').click(function(){
		ph_ups_eei_options();
		ph_ups_load_shipper_filed_options();
	});

	jQuery('#woocommerce_wf_shipping_ups_eei_shipper_filed_option').change(function(){
		ph_ups_load_shipper_filed_options();
	});

	// Toggle declaration Statement for Commercial Invoice
	jQuery('#woocommerce_wf_shipping_ups_commercial_invoice').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_declaration_statement');
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_commercial_invoice_shipping');
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_discounted_price');
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_terms_of_shipment');
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_reason_export');
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_return_reason_export');
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_commercial_invoice', '#woocommerce_wf_shipping_ups_edi_on_label');
	});

	/********************************************* Packaging Settings ************************************************/

	// Toggle Packing Methods
	jQuery('.packing_method').change(function(){
		wf_load_packing_method_options();
		ph_toggle_box_packing_options_based_on_algorithms();
	});

	// Toggle Exclude Box Weight
	jQuery('#woocommerce_wf_shipping_ups_packing_algorithm').change(function(){
		ph_toggle_box_packing_options_based_on_algorithms();
	});

	/********************************************* Freight Settings ************************************************/

	// Toggle UPS Freight Class Settings
	jQuery('#woocommerce_wf_shipping_ups_enable_freight').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_class' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_packaging_type' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_holiday_pickup' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_inside_pickup' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_residential_pickup' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_weekend_pickup' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_liftgate_pickup' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_limitedaccess_pickup' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_holiday_delivery' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_inside_delivery' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_call_before_delivery' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_weekend_delivery' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_liftgate_delivery' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_limitedaccess_delivery' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_pickup_inst' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_delivery_inst' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_freight_payment' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '.ph_ups_freight_third_party_billing' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_enable_density_based_rating' );
		ph_ups_load_third_party_billing_address();
	});

	jQuery('#woocommerce_wf_shipping_ups_freight_payment').change(function(){
		ph_ups_load_third_party_billing_address();
	});

	jQuery('#woocommerce_wf_shipping_ups_enable_density_based_rating').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_length' );
	});

	jQuery('#woocommerce_wf_shipping_ups_enable_density_based_rating').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_width' );
	});

	jQuery('#woocommerce_wf_shipping_ups_enable_density_based_rating').click(function(){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_height' );
	});
	
	jQuery('#woocommerce_wf_shipping_ups_enable_freight').click(function(){
		if( ! jQuery('#woocommerce_wf_shipping_ups_enable_density_based_rating').is(':checked') || ! jQuery(this).is(':checked') ) {
			jQuery('#woocommerce_wf_shipping_ups_density_description').next('p').hide();
		}
		else{
			jQuery('#woocommerce_wf_shipping_ups_density_description').next('p').show();
		}
	});

	jQuery('#woocommerce_wf_shipping_ups_enable_density_based_rating').click(function(){
		if( ! jQuery(this).is(':checked') ) {
			jQuery('#woocommerce_wf_shipping_ups_density_description').next('p').hide();
		}
		else{
			jQuery('#woocommerce_wf_shipping_ups_density_description').next('p').show();
		}
	});

	jQuery('#woocommerce_wf_shipping_ups_enable_freight').click(function(){
		ph_ups_toggle_density_dimensions();
	});

	jQuery('#woocommerce_wf_shipping_ups_enable_density_based_rating').click(function(){
		ph_ups_toggle_density_dimensions();
	});

	/********************************************* Pickup Settings ***************************************************/
	
	// Toggle pickup options
	jQuery('#woocommerce_wf_shipping_ups_pickup_enabled').click(function(){
		wf_ups_load_pickup_options();
	});

	// Toggle working days
	jQuery('#woocommerce_wf_shipping_ups_pickup_date').change(function(){
		wf_ups_load_working_days();
	});

	/********************************************* Product Level Settings ************************************************/

	// UPS Shipping Details Toggle
	jQuery('.ph_ups_other_details').next('.ph_ups_hide_show_product_fields').hide();
	jQuery('.ph_ups_other_details').click(function(event){
		event.stopImmediatePropagation();
		jQuery('.ups_toggle_symbol').toggleClass('ups_toggle_symbol_click');
		jQuery(this).next('.ph_ups_hide_show_product_fields').toggle();
	});

	// UPS Shipping Details Toggle - Variation Level
	jQuery(document).on('click','.ph_ups_var_other_details',function(){
		event.stopImmediatePropagation();
		jQuery(this).find('.ups_var_toggle_symbol').toggleClass('ups_var_toggle_symbol_click');
		jQuery(this).next('.ph_ups_hide_show_var_product_fields').toggle();
	});

	// Toggle Hazardous Materials

    ph_ups_toggle_restricted_materials();
	jQuery('#_ph_ups_restricted_article').change(function(){
		ph_ups_toggle_restricted_materials();
	});
	
	jQuery(document).on('click','.woocommerce_variation',function(){
		ph_ups_toggle_var_restricted_materials_on_load(this);
	});

	jQuery(document).on('change','input.ph_ups_variation_restricted_product',function(){
		ph_ups_toggle_var_restricted_materials(this);
	});

	
	ph_ups_toggle_hazardous_materials();
	jQuery('#_ph_ups_hazardous_materials').change(function(){
		ph_ups_toggle_hazardous_materials();
	});

	// Toggle Hazardous Materials - Variation Level - By Default
	jQuery(document).on('click','.woocommerce_variation',function(){
		ph_ups_toggle_var_hazardous_materials_on_load(this);
	});

	// Toggle Hazardous Materials - Variation Level - On Click
	jQuery(document).on('change','input.ph_ups_variation_hazmat_product',function(){
		ph_ups_toggle_var_hazardous_materials(this);
	});

	// End of Toggle Hazardous Materials

	jQuery("#generate_return_label").click(function(){
		service=jQuery("#return_label_service").val();
		if(service == '' ){
			alert("Select service");
			return false;
		}
		else{
			href=jQuery(this).attr('href');
			href=href+"&return_label_service="+service;
			jQuery(this).attr('href',href);
		}
		return true;
	});

	jQuery('.ph_label_custom_description').attr({'maxlength':50, 'rows':2});
    jQuery('.thirdparty_grp').attr({'maxlength':50, 'rows':2});

	// Country of Manufacturer
	jQuery('#_ph_ups_manufacture_country').attr({'maxlength':2});
	
	// EEI Product Level Fields
	jQuery('#_ph_eei_export_information').attr({'maxlength':2});
	jQuery('#_ph_eei_schedule_b_number').attr({'maxlength':10});
	jQuery('#_ph_eei_unit_of_measure').attr({'maxlength':3});

	/********************************************* Help & Support Send Report Settings ************************************************/

	jQuery('#ph_ups_ticket_number').keyup( function(){
		jQuery('#ph_ups_ticket_number').removeClass('required_field');
		jQuery('.ph_ups_ticket_number_error').hide();
	});

	jQuery("#ph_ups_consent").click( function() {
		jQuery('#ph_ups_consent').removeClass('required_field');
		jQuery('.ph_ups_consent_error').hide();
	});

	jQuery("#ph_ups_submit_ticket").click( function() {

		jQuery('.ph_error_message').remove();

		var required 	= false;
		var ticket_num 	= jQuery('#ph_ups_ticket_number').val();
		var consent 	= jQuery('#ph_ups_consent').is(':checked');

		if( !ticket_num ) {
			jQuery('#ph_ups_ticket_number').addClass('required_field');
			jQuery('.ph_ups_ticket_number_error').show();
			required 	= true;
		}

		if( !consent ) {
			jQuery('#ph_ups_consent').addClass('required_field');
			jQuery('.ph_ups_consent_error').show();
			required 	= true;
		}

		if( required ) {
			return false;
		}
		// Change Text and Disable the Button
		jQuery("#ph_ups_submit_ticket").prop("value", "Please Wait...");
		jQuery("#ph_ups_submit_ticket").attr( 'disabled', 'disabled');
		
		let key_data = {
			action 		: 'ph_ups_get_ups_log_data',
		}

		jQuery.post( ajaxurl, key_data, function( result, status ) {

			console.log(result);

			try{

				let response = JSON.parse(result);

				if( response.status == true ) {

						let key_data = {
							action 		: 'ph_ups_submit_support_ticket',
							ticket_num 	: ticket_num,
							log_file	: response.file_path
						}

						jQuery.post( ajaxurl, key_data, function( result, status ) {

							let response2 = JSON.parse(result);

							if( response2.status == true ) {
								message = "<b>Diagnostic report sent successfully.</b> PluginHive Support Team will contact you shortly via email."
								jQuery( ".ph_ups_help_table" ).after( "<p style='color:green;' class='ph_error_message'>"+message+"</p>" );

								// Add original text and enable the button
								jQuery("#ph_ups_submit_ticket").prop("value", "Send Report");
								jQuery("#ph_ups_submit_ticket").removeAttr("disabled");
							} else {

								// Add original text and enable the button
								jQuery("#ph_ups_submit_ticket").prop("value", "Send Report");
								jQuery("#ph_ups_submit_ticket").removeAttr("disabled");
							}
							
						});

				}else{
					message = response.message;
					jQuery( ".ph_ups_help_table" ).after( "<p style='color:red;' class='ph_error_message'>"+message+"</p>" );

					// Add original text and enable the button
					jQuery("#ph_ups_submit_ticket").prop("value", "Send Report");
					jQuery("#ph_ups_submit_ticket").removeAttr("disabled");
				}

			} catch(err) {
				alert(err.message);

				// Add original text and enable the button
				jQuery("#ph_ups_submit_ticket").prop("value", "Send Report");
				jQuery("#ph_ups_submit_ticket").removeAttr("disabled");
			}
			
		});
	});

});

/********************************************************************************************************************************************/
/******************************************************* Plugin Level Settings **************************************************************/
/********************************************************************************************************************************************/

function ph_ups_show_max_limit_error() {
		jQuery('.ph_ups_max_limit_error').hide();
		val = jQuery('#woocommerce_wf_shipping_ups_accesspoint_max_limit').val();
		if(val < 1 || val > 50 ){
			jQuery('#woocommerce_wf_shipping_ups_accesspoint_max_limit').after("<p style='color:red' class='ph_ups_max_limit_error'>Please enter a valid value. (Valid Values : 1-50)</p>");
		}
}

function ph_ups_woocommerce_blocks() {

	if( jQuery('#woocommerce_wf_shipping_ups_enable_estimated_delivery').is(':checked')) { 
		if( Ph_woocommerce_plugin_status.woocommerce_blocks_active ) {
			jQuery('#woocommerce_wf_shipping_ups_est_delivery_for_woocommerce_blocks').closest('tr').show();
		} 
		else{ 
			jQuery('#woocommerce_wf_shipping_ups_est_delivery_for_woocommerce_blocks').closest('tr').hide();
		}
	}
	else{
		jQuery('#woocommerce_wf_shipping_ups_est_delivery_for_woocommerce_blocks').closest('tr').hide();
	}
}


// Toggle based on checkbox status
function ph_ups_toggle_based_on_checkbox_status( tocheck, to_toggle ){
	if( ! jQuery(tocheck).is(':checked') ) {
		jQuery(to_toggle).closest('tr').hide();
	}
	else{
		jQuery(to_toggle).closest('tr').show();
	}
}

function ph_ups_address_validation_options() {

	if ( jQuery('#woocommerce_wf_shipping_ups_residential').is(':checked') ){

		jQuery('#woocommerce_wf_shipping_ups_address_validation').closest('tr').hide();
		jQuery('#woocommerce_wf_shipping_ups_suggested_address').closest('tr').hide();
		jQuery('#woocommerce_wf_shipping_ups_suggested_suggested_display').closest('tr').hide();
	} else {

		jQuery('#woocommerce_wf_shipping_ups_address_validation').closest('tr').show();

		if (jQuery('#woocommerce_wf_shipping_ups_address_validation').is(':checked') ) {

			jQuery('#woocommerce_wf_shipping_ups_suggested_address').closest('tr').show();

			if (jQuery('#woocommerce_wf_shipping_ups_suggested_address').is(':checked') ) {

				jQuery('#woocommerce_wf_shipping_ups_suggested_display').closest('tr').show();

			} else {

				jQuery('#woocommerce_wf_shipping_ups_suggested_display').closest('tr').hide();

			}
		} else {

			jQuery('#woocommerce_wf_shipping_ups_suggested_address').closest('tr').hide();
			jQuery('#woocommerce_wf_shipping_ups_suggested_display').closest('tr').hide();

		}
		
	}
}

function ph_ups_load_availability_options() {
	available = jQuery('#woocommerce_wf_shipping_ups_availability');
	if( available.val() =='all' ){
		jQuery('#woocommerce_wf_shipping_ups_countries').closest('tr').hide();
	}else{
		jQuery('#woocommerce_wf_shipping_ups_countries').closest('tr').show();
	}
}

/**
 * Toggle Tradability Cart Title
 */
function ph_ups_tradability_cart_title() {
	
	if( jQuery('#woocommerce_wf_shipping_ups_ups_tradability').is(':checked')) {
		jQuery("#woocommerce_wf_shipping_ups_tradability_cart_title").closest('tr').show();
	}
	else{
	    jQuery("#woocommerce_wf_shipping_ups_tradability_cart_title").closest('tr').hide();
	}
}

/**
 * Toggle Label Size option.
 */
function ph_toggle_ups_label_size() {

	if( jQuery("#woocommerce_wf_shipping_ups_print_label_type").val() == 'gif' || jQuery("#woocommerce_wf_shipping_ups_print_label_type").val() == 'png' ) {

		jQuery("#woocommerce_wf_shipping_ups_show_label_in_browser").closest('tr').show();
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_show_label_in_browser', '#woocommerce_wf_shipping_ups_label_in_browser_zoom' );
		
	}else{

		jQuery("#woocommerce_wf_shipping_ups_show_label_in_browser").closest('tr').hide();
		jQuery("#woocommerce_wf_shipping_ups_label_in_browser_zoom").closest('tr').hide();
	}
}

/**
 * Toggle Label Zoom Factor
 */
function ph_toggle_ups_label_zoom_factor() {

	if( jQuery('#woocommerce_wf_shipping_ups_show_label_in_browser').is(':checked') && ( jQuery("#woocommerce_wf_shipping_ups_print_label_type").val() == 'gif' || jQuery("#woocommerce_wf_shipping_ups_print_label_type").val() == 'png' ) ) {

		jQuery("#woocommerce_wf_shipping_ups_label_in_browser_zoom").closest('tr').show();
		jQuery("#woocommerce_wf_shipping_ups_rotate_label").closest('tr').show();
		jQuery(".ups_display_browser_options").closest('tr').show();
		
	} else {
		jQuery("#woocommerce_wf_shipping_ups_label_in_browser_zoom").closest('tr').hide();
	    jQuery("#woocommerce_wf_shipping_ups_rotate_label").closest('tr').hide();
	    jQuery(".ups_display_browser_options").closest('tr').hide();


	}
}

/**
 * Toggle Label Format based on Print Label Type option and Display Label in Browser.
 */
function ph_ups_toggle_label_format() {

	if( jQuery("#woocommerce_wf_shipping_ups_label_format").val() == 'laser_8_5_by_11' && ( jQuery("#woocommerce_wf_shipping_ups_print_label_type").val() == 'gif' || jQuery("#woocommerce_wf_shipping_ups_print_label_type").val() == 'png' ) ) {
		
		jQuery("#woocommerce_wf_shipping_ups_label_format").closest('tr').show();
		
	}else{
		jQuery("#woocommerce_wf_shipping_ups_label_format").closest('tr').hide();
	}
}

/**
 * Toggle UPS Label Email Settings.
 */
function ph_ups_toggle_label_email_settings() {
	if( jQuery("#woocommerce_wf_shipping_ups_auto_email_label").val() == null ) {
		jQuery(".ph_ups_email_label_settings").closest('tr').hide();
	}
	else{
		jQuery(".ph_ups_email_label_settings").closest('tr').show();
	}
}

/**
 * Custom Description For Label
**/
function ph_ups_custom_description_for_label() {
	if( jQuery("#woocommerce_wf_shipping_ups_label_description").val() == 'custom_description' ) {
		jQuery("#woocommerce_wf_shipping_ups_label_custom_description").closest('tr').show();
	}
	else{
		jQuery("#woocommerce_wf_shipping_ups_label_custom_description").closest('tr').hide();
	}
}

/**
 *  Automate Package Generation Check box checked
**/
function ph_ups_toggle_based_automate_pakage_generation() {
	
	if( jQuery('#woocommerce_wf_shipping_ups_automate_package_generation').is(':checked')) {
		jQuery("#woocommerce_wf_shipping_ups_automate_label_generation").closest('tr').show();
	}
	else{
	    jQuery("#woocommerce_wf_shipping_ups_automate_label_generation").closest('tr').hide();
	}	    
	
}

function ph_ups_toggle_nafta_certificate_options()
{
	let check1 = jQuery('#woocommerce_wf_shipping_ups_commercial_invoice').is(':checked');
	let check2 = jQuery('#woocommerce_wf_shipping_ups_nafta_co_form').is(':checked')

	if( check1 && check2 ){
		jQuery('.ph_ups_nafta_group').closest('tr').show();
	}
	else
	{
		jQuery('.ph_ups_nafta_group').closest('tr').hide();
	}
}

function ph_ups_eei_options()
{
	let check1 = jQuery('#woocommerce_wf_shipping_ups_commercial_invoice').is(':checked');
	let check2 = jQuery('#woocommerce_wf_shipping_ups_eei_data').is(':checked')

	if( check1 && check2 ){
		jQuery('.ph_ups_eei_group').closest('tr').show();
	}
	else
	{
		jQuery('.ph_ups_eei_group').closest('tr').hide();
	}
}

function ph_ups_load_shipper_filed_options(){

	var eei_filed_option 	= jQuery('#woocommerce_wf_shipping_ups_eei_shipper_filed_option').val();
	var invoice_enabled		= jQuery('#woocommerce_wf_shipping_ups_commercial_invoice').is(":checked");
	var eei_enabled			= jQuery('#woocommerce_wf_shipping_ups_eei_data').is(":checked");
	
	if( invoice_enabled && eei_enabled )
	{
		if( eei_filed_option == 'A' ){

			jQuery('.eei_pre_departure_itn_number').closest('tr').show();
			jQuery('.eei_exemption_legend').closest('tr').hide();

		}else if( eei_filed_option == 'B' ){

			jQuery('.eei_pre_departure_itn_number').closest('tr').hide();
			jQuery('.eei_exemption_legend').closest('tr').show();

		}else{

			jQuery('.eei_pre_departure_itn_number').closest('tr').hide();
			jQuery('.eei_exemption_legend').closest('tr').hide();

		}
	}
	
}

function wf_load_packing_method_options(){
	pack_method	=	jQuery('.packing_method').val();

	jQuery('#packing_options').hide();
	jQuery('.weight_based_option').closest('tr').hide();
	jQuery('.xa_ups_box_packing').closest('tr').hide();

	switch(pack_method){
		
		case 'box_packing':
		jQuery('.xa_ups_box_packing').closest('tr').show();
		jQuery('#packing_options').show();
		break;

		case 'weight_based':
		jQuery('.weight_based_option').closest('tr').show();
		break;

		case 'per_item':
		
		default:
		break;
	}
}

function ph_toggle_box_packing_options_based_on_algorithms(){

	pack_method			=	jQuery('.packing_method').val();
	packing_algorithm	=	jQuery('#woocommerce_wf_shipping_ups_packing_algorithm').val();
	
	if( packing_algorithm == 'volume_based' && pack_method == 'box_packing' ){
		
		jQuery('.exclude_box_weight').closest('tr').show();
	}else{
		
		jQuery('.exclude_box_weight').closest('tr').hide();
	}

	if( packing_algorithm == 'stack_first' && pack_method == 'box_packing' ){
		
		jQuery('.stack_to_volume').closest('tr').show();
	}else{
		
		jQuery('.stack_to_volume').closest('tr').hide();
	}
}

function ph_ups_load_third_party_billing_address() {
	var freight_payment = jQuery('#woocommerce_wf_shipping_ups_freight_payment').val();
	var freight_enabled	= jQuery('#woocommerce_wf_shipping_ups_enable_freight').is(":checked");
	
	if( !freight_enabled || freight_payment != '30' ){		
		jQuery('.ph_ups_freight_third_party_billing').closest('tr').hide();
	}else{
		jQuery('.ph_ups_freight_third_party_billing').closest('tr').show();
	}
}

function ph_ups_toggle_density_description()
{
	if( ! jQuery('#woocommerce_wf_shipping_ups_enable_density_based_rating').is(':checked') || ! jQuery('#woocommerce_wf_shipping_ups_enable_freight').is(':checked') ) {
		jQuery('#woocommerce_wf_shipping_ups_density_description').next('p').hide();
	}
	else{
		jQuery('#woocommerce_wf_shipping_ups_density_description').next('p').show();
	}
}

function ph_ups_toggle_density_dimensions()
{
	if( ! jQuery('#woocommerce_wf_shipping_ups_enable_freight').is(':checked') ) {
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_density_length' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_density_width' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_freight', '#woocommerce_wf_shipping_ups_density_height' );
	}
	else if( ! jQuery('#woocommerce_wf_shipping_ups_enable_density_based_rating').is(':checked') ){
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_length' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_width' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_height' );
	}
	else{
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_length' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_width' );
		ph_ups_toggle_based_on_checkbox_status( '#woocommerce_wf_shipping_ups_enable_density_based_rating', '#woocommerce_wf_shipping_ups_density_height' );
	}
}

function wf_ups_load_pickup_options(){
	var checked	=	jQuery('#woocommerce_wf_shipping_ups_pickup_enabled').is(":checked");
	if(checked){
		jQuery('.wf_ups_pickup_grp').closest('tr').show();
	}else{
		jQuery('.wf_ups_pickup_grp').closest('tr').hide();
	}
	wf_ups_load_working_days();
}

function wf_ups_load_working_days() {

	var pickup_date = jQuery('#woocommerce_wf_shipping_ups_pickup_date').val();
	var checked		= jQuery('#woocommerce_wf_shipping_ups_pickup_enabled').is(":checked");

	if( !checked || pickup_date != 'specific' ){
		jQuery('.pickup_working_days').closest('tr').hide();
	}else{
		jQuery('.pickup_working_days').closest('tr').show();
	}
}

/********************************************************************************************************************************************/
/***************************************************** Product Level Settings ***************************************************************/
/********************************************************************************************************************************************/
function ph_ups_toggle_restricted_materials(){
	if( jQuery("#_ph_ups_restricted_article").is(":checked") ){
		jQuery(".ph_ups_restricted_article").show();
	}
	else{
		jQuery(".ph_ups_restricted_article").hide();
	}
}
function ph_ups_toggle_var_restricted_materials_on_load(e){
 	if( jQuery(e).find(".ph_ups_variation_restricted_product").is(':checked') ){
 		jQuery(e).find(".ph_ups_var_restricted_article").show();
 	}else{
 		jQuery(e).find(".ph_ups_var_restricted_article").hide();
 	}
 }

/**
 * Toggle Hazardous Materials Settings - Variation Level - Onclick
**/
 function ph_ups_toggle_var_restricted_materials(e){
 	if( jQuery(e).is(':checked') ){
 		jQuery(e).closest( '.woocommerce_variation' ).find(".ph_ups_var_restricted_article").show();
 	}else{
 		jQuery(e).closest( '.woocommerce_variation' ).find(".ph_ups_var_restricted_article").hide();
 	}
 }

/**
 * Toggle Hazardous Materials Settings
**/


function ph_ups_toggle_hazardous_materials(){
	if( jQuery("#_ph_ups_hazardous_materials").is(":checked") ){
		jQuery(".ph_ups_hazardous_materials").show();
	}
	else{
		jQuery(".ph_ups_hazardous_materials").hide();
	}
}

/**
 * Toggle Hazardous Materials Settings - Variation Level - Onload
**/
 function ph_ups_toggle_var_hazardous_materials_on_load(e){
 	if( jQuery(e).find(".ph_ups_variation_hazmat_product").is(':checked') ){
 		jQuery(e).find(".ph_ups_var_hazardous_materials").show();
 	}else{
 		jQuery(e).find(".ph_ups_var_hazardous_materials").hide();
 	}
 }

/**
 * Toggle Hazardous Materials Settings - Variation Level - Onclick
**/
 function ph_ups_toggle_var_hazardous_materials(e){
 	if( jQuery(e).is(':checked') ){
 		jQuery(e).closest( '.woocommerce_variation' ).find(".ph_ups_var_hazardous_materials").show();
 	}else{
 		jQuery(e).closest( '.woocommerce_variation' ).find(".ph_ups_var_hazardous_materials").hide();
 	}
 }

/**
 * Duties and taxes select box
**/
function xa_ups_duties_payer_options(){

	val = jQuery("#woocommerce_wf_shipping_ups_duties_and_taxes").val();
	if( val == 'third_party' ){
		
		jQuery("#woocommerce_wf_shipping_ups_shipping_payor_acc_no").closest('tr').show();
		jQuery("#woocommerce_wf_shipping_ups_shipping_payor_post_code").closest('tr').show();
		jQuery("#woocommerce_wf_shipping_ups_shipping_payor_country_code").closest('tr').show();

	}else{
		
		jQuery("#woocommerce_wf_shipping_ups_shipping_payor_acc_no").closest('tr').hide();
		jQuery("#woocommerce_wf_shipping_ups_shipping_payor_post_code").closest('tr').hide();
		jQuery("#woocommerce_wf_shipping_ups_shipping_payor_country_code").closest('tr').hide();
	}
}

function ph_ups_transportation_options(){

	val = jQuery("#woocommerce_wf_shipping_ups_transportation").val();

	if( val == 'third_party' ){
		
		jQuery("#woocommerce_wf_shipping_ups_transport_payor_acc_no").closest('tr').show();
		jQuery("#woocommerce_wf_shipping_ups_transport_payor_post_code").closest('tr').show();
		jQuery("#woocommerce_wf_shipping_ups_transport_payor_country_code").closest('tr').show();
	
	}else{
		
		jQuery("#woocommerce_wf_shipping_ups_transport_payor_acc_no").closest('tr').hide();
		jQuery("#woocommerce_wf_shipping_ups_transport_payor_post_code").closest('tr').hide();
		jQuery("#woocommerce_wf_shipping_ups_transport_payor_country_code").closest('tr').hide();
	}
}