//NEW DASHBOARD MOBILE MENU AND WIDGET TOGGLING
jQuery(document).ready(function($){
	$('#ewd-uasp-dash-mobile-menu-open').click(function(){
		$('.ewd-uasp-admin-header-menu .nav-tab:nth-of-type(1n+2)').toggle();
		$('#ewd-uasp-dash-mobile-menu-up-caret').toggle();
		$('#ewd-uasp-dash-mobile-menu-down-caret').toggle();
		return false;
	});
	$(function(){
		$(window).resize(function(){
			if($(window).width() > 785){
				$('.ewd-uasp-admin-header-menu .nav-tab:nth-of-type(1n+2)').show();
			}
			else{
				$('.ewd-uasp-admin-header-menu .nav-tab:nth-of-type(1n+2)').hide();
				$('#ewd-uasp-dash-mobile-menu-up-caret').hide();
				$('#ewd-uasp-dash-mobile-menu-down-caret').show();
			}
		}).resize();
	});	
	$('#ewd-uasp-dashboard-support-widget-box .ewd-uasp-dashboard-new-widget-box-top').click(function(){
		$('#ewd-uasp-dashboard-support-widget-box .ewd-uasp-dashboard-new-widget-box-bottom').toggle();
		$('#ewd-uasp-dash-mobile-support-up-caret').toggle();
		$('#ewd-uasp-dash-mobile-support-down-caret').toggle();
	});
	$('#ewd-uasp-dashboard-optional-table .ewd-uasp-dashboard-new-widget-box-top').click(function(){
		$('#ewd-uasp-dashboard-optional-table .ewd-uasp-dashboard-new-widget-box-bottom').toggle();
		$('#ewd-uasp-dash-optional-table-up-caret').toggle();
		$('#ewd-uasp-dash-optional-table-down-caret').toggle();
	});
});

// REQUIRE CONFIRMATION BEFORE DELETING AN APPOINTMENT
jQuery( '.appointments #the-list .delete' ).on( 'click', function() {

	var appointment_id = jQuery( this ).data( 'id' );

	var response = confirm( 'You are about to delete this appointment' );

	if ( response ) { 

		var params = {
    	    appointment_id: appointment_id,
    	    nonce: ewd_uasp_admin_php_data.nonce,
    	    action: 'ewd_uasp_delete_appointment'
    	};

    	var data = jQuery.param( params );

        jQuery.post(ajaxurl, data, function(response) {});

        setTimeout( function() { window.location.reload( true ) }, 100 );
	} 
});

// SEND TEST EMAIL
jQuery( document ).ready( function( $ ) {

	$( '.ewd-uasp-send-test-email' ).on( 'click', function() {

		$( '.ewd-uasp-test-email-response' ).remove();

		var email_address = $( 'input[name="ewd-uasp-settings[send-sample-email-address]"]' ).val();
		var email_to_send = $( '#send-sample-email-message' ).val();

		if ( email_address == '' || email_to_send == '' ) {

			$( '.ewd-uasp-send-test-email' ).after( '<div class="ewd-uasp-test-email-response">Error: Select an email and enter an email address before sending.</div>' );
		}

		var params = {
    	    email_address: email_address,
    	    email_to_send: email_to_send,
    	    nonce: ewd_uasp_admin_php_data.nonce,
    	    action: 'ewd_uasp_send_test_email'
    	};

    	var data = jQuery.param( params );
    	
        jQuery.post( ajaxurl, data, function( response ) {
        	$( '.ewd-uasp-send-test-email' ).after(response);
        });
	});

});

jQuery(document).ready(function($){

	$( '.sap-new-admin-add-button' ).on( 'click', function() {

		setTimeout( ewd_uasp_field_added_handler, 300);
		setTimeout( ewd_uasp_custom_field_name_focusout_handler, 300);
	});
});

function ewd_uasp_custom_field_name_focusout_handler() {

	jQuery( '.sap-infinite-table input[data-name="name"]' ).off( 'focusout' ).on( 'focusout', function() {

		var slug_input = jQuery( this ).parent().parent().find( 'input[data-name="slug"]' );

		if ( slug_input.val() ) { return; }

		slug_input.val( jQuery( this ).val().toLowerCase().replace( /\s+/g, '-' ) );
	});
}

function ewd_uasp_field_added_handler() {

	var highest = 0;
	jQuery( '.sap-infinite-table input[data-name="id"]' ).each( function() {
		if ( ! isNaN( this.value ) ) { highest = Math.max( highest, this.value ); }
	});

	jQuery( '.sap-infinite-table  tbody tr:last-of-type span.sap-infinite-table-hidden-value' ).html( highest + 1 );
	jQuery( '.sap-infinite-table  tbody tr:last-of-type input[data-name="id"]' ).val( highest + 1 );
}


// APPOINTMENTS TABLE SPECIFIC DATE FILTERING

jQuery(document).ready(function(){

	jQuery( '#ewd-uasp-date-filter-link' ).click( function() {
		
		jQuery( '#ewd-uasp-filters' ).toggleClass( 'date-filters-visible' );
	});
});

//SETTINGS PREVIEW SCREENS

jQuery( document ).ready( function() {

	jQuery( '.ewd-uasp-settings-preview' ).prev( 'h2' ).hide();
});

jQuery(document).ready(function() {
	jQuery('.ewd-uasp-appointments-table-filter').on('change', function(e) {
		let url = jQuery(this).find(':selected').eq(0).data('link');
		window.location = url;
	});
});