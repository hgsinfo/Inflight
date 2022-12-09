jQuery( document ).ready( function($) {

    jQuery( '#ewd-uasp-find-appointment' ).on( 'click', function() {

        var location_id = jQuery( '#ewd-uasp-location-id' ).val();
        var service_id = jQuery( '#ewd-uasp-service-id' ).val();
        var service_provider_id = jQuery( '#ewd-uasp-provider-id' ).val();
        var selected_date = jQuery( '#ewd-uasp-date' ).val();

        var data = jQuery.param({
            nonce: ewd_uasp.nonce,
            location_id: location_id,
            service_id: service_id,
            service_provider_id: service_provider_id,
            date: selected_date,
            action: 'ewd_uasp_get_appointments'
        });
        jQuery.post(ajaxurl, data, function(response) {

            jQuery( '#ewd-uasp-appointment-times' ).html( response.data.output );

            ewd_uasp_set_appointment_time_click_handlers();
        });
    });

    jQuery( '#ewd-uasp-location-id, #ewd-uasp-service-id' ).on( 'change', function() {

        var location_id = jQuery( '#ewd-uasp-location-id' ).val();
        var service_id = jQuery( '#ewd-uasp-service-id' ).val();

        var data = jQuery.param({
            nonce: ewd_uasp.nonce,
            location_id: location_id,
            service_id: service_id,
            action: 'ewd_uasp_get_service_providers'
        });
        jQuery.post( ajaxurl, data, function( response ) {

            jQuery( '#ewd-uasp-provider-id' ).html( response.data.output ).trigger( 'change' );
        });
    });

    jQuery( '.ewd-uasp-edit-appointment-toggle' ).on( 'click', function() {

        jQuery( this ).addClass( 'ewd-uasp-hidden' );

        jQuery( '.ewd-uasp-edit-appointment' ).removeClass( 'ewd-uasp-hidden' );
    });

    jQuery('.ewd-uasp-multistep-indicator').on('click', function() {
        if (jQuery(this).hasClass('ewd-uasp-indicator-selected')) {return;}

        if (jQuery(this).data('indicator') == 'registrationform') {
            
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="service"], .ewd-uasp-multistep-indicator[data-indicator="findappointment"]').removeClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="registrationform"]').addClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-service, .ewd-uasp-find-appointment, .ewd-uasp-calendar-container, .ewd-uasp-book-button-container').addClass('ewd-uasp-hidden');
            jQuery('.ewd-uasp-registration-form, .ewd-uasp-multistep-advance-container').removeClass('ewd-uasp-hidden');
        }

        if (jQuery(this).data('indicator') == 'service') {
            
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="registrationform"], .ewd-uasp-multistep-indicator[data-indicator="findappointment"]').removeClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="service"]').addClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-registration-form, .ewd-uasp-find-appointment, .ewd-uasp-calendar-container, .ewd-uasp-book-button-container').addClass('ewd-uasp-hidden');
            jQuery('.ewd-uasp-service, .ewd-uasp-multistep-advance-container').removeClass('ewd-uasp-hidden');
        }

        if (jQuery(this).data('indicator') == 'findappointment') {
            
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="registrationform"], .ewd-uasp-multistep-indicator[data-indicator="service"]').removeClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="findappointment"]').addClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-registration-form, .ewd-uasp-service, .ewd-uasp-multistep-advance-container').addClass('ewd-uasp-hidden');
            jQuery('.ewd-uasp-find-appointment, .ewd-uasp-calendar-container, .ewd-uasp-book-button-container').removeClass('ewd-uasp-hidden');
        }
    });

    jQuery('.ewd-uasp-multistep-advance-button').on('click', function() {

        if (jQuery('.ewd-uasp-indicator-selected').data('indicator') == 'registrationform') {
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="registrationform"]').removeClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="findappointment"]').removeClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="service"]').addClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-registration-form').addClass('ewd-uasp-hidden');
            jQuery('.ewd-uasp-service').removeClass('ewd-uasp-hidden');
        }
        else {
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="registrationform"]').removeClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="service"]').removeClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-multistep-indicator[data-indicator="findappointment"]').addClass('ewd-uasp-indicator-selected');
            jQuery('.ewd-uasp-service, .ewd-uasp-multistep-advance-container').addClass('ewd-uasp-hidden');
            jQuery('.ewd-uasp-calendar-container, .ewd-uasp-book-button-container').removeClass('ewd-uasp-hidden');
            jQuery('.ewd-uasp-find-appointment').removeClass('ewd-uasp-hidden');
        }
    });

    jQuery('.ewd-uasp-appointment-form').submit(function(event) {

        if ( ! jQuery( 'input[name="ewd_uasp_appointment_start"]' ).val() && ! ewd_uasp_php_data.appointment_id ) {

            jQuery( '.ewd-uasp-book-button-container' ).append( '<span class="ewd-uasp-appointment-submit-failure">Please select a valid appointment time before submitting the form.</span>' );

            setTimeout( function() { jQuery( '.ewd-uasp-appointment-submit-failure' ).remove(); }, 5000 );

            event.preventDefault();
        }
    });

    var regFormHeight = ( $('.ewd-uasp-registration-form').height() ) + 6;
    $('.ewd-uasp-service').css('height', regFormHeight+'px');

    var minDate = jQuery('.ewd-uasp-datepicker').attr('min');
    var maxDate = jQuery('.ewd-uasp-datepicker').attr('max');
    jQuery('.ewd-uasp-datepicker').datepicker( {
        dateFormat : "yy-mm-dd",
        minDate: minDate,
        maxDate: maxDate,
        defaultDate: ewd_uasp_php_data.default_date
    } );

    ewd_uasp_set_appointment_time_click_handlers();
});

function ewd_uasp_set_appointment_time_click_handlers() { 
    
    jQuery( '.ewd-uasp-appointment-link' ).off( 'click' ).on( 'click', function() {

        jQuery( '.ewd-uasp-appointment-listing' ).removeClass('ewd-uasp-selected-appointment-time');

        jQuery( this ).parent().toggleClass( 'ewd-uasp-selected-appointment-time' );
        
        jQuery( 'input[name="ewd_uasp_appointment_start"]' ).val( jQuery( '#ewd-uasp-date' ).val() + ' ' + jQuery( this ).data( 'appointment_start_time' ) );
    
        jQuery( '#ewd-uasp-provider-id' ).val( jQuery( this ).data( 'service_provider_id' ) );
    });
}

function ClearAppointments() {
    jQuery('#ewd-uasp-appointment-times').html("");
    jQuery('#ewd-uasp-selected-appointment-time').val("");
}