jQuery(document).ready(function() {
	jQuery('.ewd-uasp-welcome-screen-box h2').on('click', function() {
		var page = jQuery(this).parent().data('screen');
		EWD_UASP_Toggle_Welcome_Page(page);
	});

	jQuery('.ewd-uasp-welcome-screen-next-button').on('click', function() {
		var page = jQuery(this).data('nextaction');
		EWD_UASP_Toggle_Welcome_Page(page);
	});

	jQuery('.ewd-uasp-welcome-screen-previous-button').on('click', function() {
		var page = jQuery(this).data('previousaction');
		EWD_UASP_Toggle_Welcome_Page(page);
	});

	jQuery('.ewd-uasp-welcome-screen-add-service-button').on('click', function() {

		jQuery('.ewd-uasp-welcome-screen-show-created-services').show();

		var service_name = jQuery('.ewd-uasp-welcome-screen-add-service-name input').val();
		var service_description = jQuery('.ewd-uasp-welcome-screen-add-service-description textarea').val();
		var service_capacity = jQuery('.ewd-uasp-welcome-screen-add-service-capacity input').val();
		var service_duration = jQuery('.ewd-uasp-welcome-screen-add-service-duration input').val();

		jQuery('.ewd-uasp-welcome-screen-add-service-name input').val('');
		jQuery('.ewd-uasp-welcome-screen-add-service-description textarea').val('');
		jQuery('.ewd-uasp-welcome-screen-add-service-capacity input').val('');
		jQuery('.ewd-uasp-welcome-screen-add-service-duration input').val('');

		var params = {
			service_name: service_name,
			service_description: service_description,
			service_capacity: service_capacity,
			service_duration: service_duration,
			nonce: ewd_uasp_getting_started.nonce,
			action: 'ewd_uasp_welcome_add_service'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function(response) {

			var HTML = '<tr class="ewd-uasp-welcome-screen-service">';
			HTML += '<td class="ewd-uasp-welcome-screen-service-name">' + service_name + '</td>';
			HTML += '<td class="ewd-uasp-welcome-screen-service-description">' + service_description + '</td>';
			HTML += '<td class="ewd-uasp-welcome-screen-service-capacity">' + service_capacity + '</td>';
			HTML += '<td class="ewd-uasp-welcome-screen-service-duration">' + service_duration + '</td>';
			HTML += '</tr>';

			jQuery( '.ewd-uasp-welcome-screen-show-created-services' ).append(HTML);

			jQuery( '.ewd-uasp-welcome-screen-provider-services' ).append( '<input type="checkbox" class="ewd-uasp-welcome-screen-provider-services" value="' + response.data.post_id + '" /> ' + service_name + '<br />' );
		});
	});

	jQuery('.ewd-uasp-welcome-screen-add-location-button').on('click', function() {

		jQuery('.ewd-uasp-welcome-screen-show-created-locations').show();

		var location_name = jQuery('.ewd-uasp-welcome-screen-add-location-name input').val();
		var location_description = jQuery('.ewd-uasp-welcome-screen-add-location-description textarea').val();

		var openings = new Object;
		var closings = new Object;

		var days = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ];

		jQuery( days ).each( function( index, day ) {

			openings[day] = jQuery( '.ewd-uasp-welcome-screen-add-location-open[data-day="' + day + '"] select' ).val();
			closings[day] = jQuery( '.ewd-uasp-welcome-screen-add-location-close[data-day="' + day + '"] select' ).val();
		});

		jQuery('.ewd-uasp-welcome-screen-add-location-name input').val('');
		jQuery('.ewd-uasp-welcome-screen-add-location-description textarea').val('');

		var params = {
			location_name: location_name,
			location_description: location_description,
			location_openings: JSON.stringify( openings ),
			location_closings: JSON.stringify( closings ),
			nonce: ewd_uasp_getting_started.nonce,
			action: 'ewd_uasp_welcome_add_location'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function(response) {

			var HTML = '<tr class="ewd-uasp-welcome-screen-location">';
			HTML += '<td class="ewd-uasp-welcome-screen-location-name">' + location_name + '</td>';
			HTML += '<td class="ewd-uasp-welcome-screen-location-description">' + location_description + '</td>';
			HTML += '</tr>';

			jQuery('.ewd-uasp-welcome-screen-show-created-locations').append(HTML);

			jQuery( '.ewd-uasp-welcome-screen-provider-location-select' ).append( '<option value="' + response.data.post_id + '">' + location_name + '</option>' );
		});
	});

	jQuery('.ewd-uasp-welcome-screen-add-provider-button').on('click', function() {

		jQuery('.ewd-uasp-welcome-screen-show-created-providers').show();

		var provider_name = jQuery('.ewd-uasp-welcome-screen-add-provider-name input').val();
		var provider_description = jQuery('.ewd-uasp-welcome-screen-add-provider-description textarea').val();
		var provider_email = jQuery('.ewd-uasp-welcome-screen-add-provider-email input').val();

		var services = [];

		jQuery( '.ewd-uasp-welcome-screen-provider-services:checked' ).each( function() {

			services.push( jQuery( this ).val() );
		});

		var openings = new Object;
		var closings = new Object;
		var locations = new Object;

		var days = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ];

		jQuery( days ).each( function( index, day ) {

			openings[day] = jQuery( '.ewd-uasp-welcome-screen-add-provider-open[data-day="' + day + '"] select' ).val();
			closings[day] = jQuery( '.ewd-uasp-welcome-screen-add-provider-close[data-day="' + day + '"] select' ).val();
			locations[day] = jQuery( '.ewd-uasp-welcome-screen-add-provider-location[data-day="' + day + '"] select' ).val();
		});

		jQuery('.ewd-uasp-welcome-screen-add-provider-name input').val('');
		jQuery('.ewd-uasp-welcome-screen-add-provider-description textarea').val('');
		jQuery('.ewd-uasp-welcome-screen-add-provider-email input').val('');

		var params = {
			provider_name: provider_name,
			provider_description: provider_description,
			provider_email: provider_email,
			provider_services: JSON.stringify( services ),
			provider_openings: JSON.stringify( openings ),
			provider_closings: JSON.stringify( closings ),
			provider_locations: JSON.stringify( locations ),
			nonce: ewd_uasp_getting_started.nonce,
			action: 'ewd_uasp_welcome_add_provider'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function(response) {
			var HTML = '<tr class="ewd-uasp-welcome-screen-provider">';
			HTML += '<td class="ewd-uasp-welcome-screen-provider-name">' + provider_name + '</td>';
			HTML += '<td class="ewd-uasp-welcome-screen-provider-description">' + provider_description + '</td>';
			HTML += '<td class="ewd-uasp-welcome-screen-provider-email">' + provider_email + '</td>';
			HTML += '</tr>';

			jQuery('.ewd-uasp-welcome-screen-show-created-providers').append(HTML);
		});
	});

	jQuery('.ewd-uasp-welcome-screen-add-booking-page-button').on('click', function() {
		
		var booking_page_title = jQuery('.ewd-uasp-welcome-screen-add-booking-page-name input').val();

		EWD_UASP_Toggle_Welcome_Page('options');

		var params = {
			booking_page_title: booking_page_title,
			nonce: ewd_uasp_getting_started.nonce,
			action: 'ewd_uasp_welcome_add_booking_page'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function(response) {});
	});

	jQuery('.ewd-uasp-welcome-screen-save-options-button').on('click', function() {

		var multi_step_booking = jQuery('input[name="multi_step_booking"]:checked').val(); 
		var time_between_appointments = jQuery('input[name="time_between_appointments"]').val(); 
		var hours_format = jQuery('select[name="hours_format"]').val(); 
		var calendar_starting_layout = jQuery('select[name="calendar_starting_layout"]').val();

		var params = {
			multi_step_booking: multi_step_booking,
			time_between_appointments: time_between_appointments,
			hours_format: hours_format,
			calendar_starting_layout: calendar_starting_layout,
			nonce: ewd_uasp_getting_started.nonce,
			action: 'ewd_uasp_welcome_set_options'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function(response) {
			jQuery('.ewd-uasp-welcome-screen-save-options-button').after('<div class="ewd-uasp-save-message"><div class="ewd-uasp-save-message-inside">Options have been saved.</div></div>');
			jQuery('.ewd-uasp-save-message').delay(2000).fadeOut(400, function() {jQuery('.ewd-uasp-save-message').remove();});
		});
	});
});

function EWD_UASP_Toggle_Welcome_Page(page) {
	jQuery('.ewd-uasp-welcome-screen-box').removeClass('ewd-uasp-welcome-screen-open');
	jQuery('.ewd-uasp-welcome-screen-' + page).addClass('ewd-uasp-welcome-screen-open');
}