var calendarLanguage = ewd_uasp_php_calendar_data.calendar_language;
if (ewd_uasp_php_calendar_data.hours_format == "12") {var timeFormat = 'h(:00)a';}
else {var timeFormat = 'H(:00)';}
jQuery(document).ready(function() {
	jQuery('#ewd-uasp-calendar').fullCalendar({
        locale: + calendarLanguage,
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		defaultView: ewd_uasp_php_calendar_data.starting_layout,
        defaultDate: moment(ewd_uasp_php_calendar_data.default_date),
		scrollTime : ewd_uasp_php_calendar_data.starting_time,
		editable: false,
		disableDragging: true,
		eventLimit: true, // allow "more" link when too many events
		timezone: ewd_uasp_php_calendar_data.timezone,
        timeFormat: timeFormat,
        slotLabelFormat: timeFormat,
		events: function(start, end, timezone, callback) {
            jQuery.post({
    	        url: ajaxurl,
    	        data: {
    	            // our hypothetical feed requires UNIX timestamps
                    nonce: ewd_uasp_calendar.nonce, 
    	            action: 'ewd_uasp_get_events',
    	            start: start.unix(),
    	            end: end.unix(),
    	            location: jQuery( '#ewd-uasp-location-id' ).val(),
    	            service: jQuery( '#ewd-uasp-service-id' ).val(),
    	            service_provider: jQuery( '#ewd-uasp-provider-id' ).val()
    	        },
    	        success: function( response ) {
    	        	event_objects = response.data.events;
    	            var events = [];
    	            jQuery(event_objects).each(function(index, item) {
    	                events.push({
    	                    title: item.title,
    	                    start: item.start,
    	                    end: item.end,  // will be parsed
    	                    provider: item.provider
    	                });
    	            });
    	            callback(events);
    	        }
    	    });
    	},
    	eventClick: function(calEvent, jsEvent, view) {
        	var Time_Select  = jQuery('#ewd-uasp-time-select-input-div select');
        	Time_Select.find('option').remove();
        	
        	var Time = Date.parse(calEvent.start) / 1000; 
        	var End_Time = Date.parse(calEvent.end) / 1000; 
        	var Service_Duration = jQuery('#ewd-uasp-service').find(':selected').data('serviceduration');
        	
        	jQuery('#ewd-uasp-time-select').data('date', calEvent.start);
        	jQuery('#ewd-uasp-time-select').data('provider', calEvent.provider);

        	/* var Offset = ewd_uasp_php_calendar_data.timezone_offset; 
        	var Offset_Parts = Offset.split(':'); 
        	var Offset_Seconds = (parseInt(Offset_Parts[0] * -1 * 60) + parseInt(Offset_Parts[1])) * 60; */

            var now = new Date();
            var client_offset = now.getTimezoneOffset() * 60; 

            // var offset_difference = Offset_Seconds - client_offset;
            
       		while (Time < End_Time) { 
        		var Appointment_Time = new Date((Time + client_offset)*1000);
        		jQuery('#ewd-uasp-time-select-input-div select').append('<option value="' + Time + '">' + Get_Formatted_Time(Appointment_Time, ewd_uasp_php_calendar_data.hours_format) + '</option>');
        		Time += (ewd_uasp_php_calendar_data.time_interval * 60);
        	}

            jQuery('.ewd-uasp-selected-event').removeClass('ewd-uasp-selected-event');
            jQuery(this).addClass('ewd-uasp-selected-event');

        	jQuery('#ewd-uasp-time-location').html(ewd_uasp_php_calendar_data.pop_up_label_location + ': ' + jQuery('#ewd-uasp-location-id option:selected').text());
        	jQuery('#ewd-uasp-time-service').html(ewd_uasp_php_calendar_data.pop_up_label_service + ': ' + jQuery('#ewd-uasp-service-id option:selected').text());
        	jQuery('#ewd-uasp-time-service-provider').html(ewd_uasp_php_calendar_data.pop_up_label_provider + ': ' + jQuery('#ewd-uasp-provider-id option[value="' + calEvent.provider + '"]').text());
        	
        	jQuery('#ewd-uasp-time-select, #ewd-uasp-screen-background').removeClass('ewd-uasp-hidden');
    	}
	});

	jQuery( '#ewd-uasp-location-id, #ewd-uasp-service-id, #ewd-uasp-provider-id' ).on('change', function() {

		jQuery('#ewd-uasp-calendar').fullCalendar('refetchEvents');
	});

    jQuery('#ewd-uasp-screen-background').on('click', function() {
        jQuery('#ewd-uasp-time-select, #ewd-uasp-screen-background').addClass('ewd-uasp-hidden');
    });

	jQuery('#ewd-uasp-select-time-button').on('click', function() {
		//var Selected_Time = new Date(jQuery('#ewd-uasp-time-select-input-div select').val()).toLocaleString('en-ca', {timezone: ewd_uasp_php_calendar_data.timezone});
        /* var newTimeOffset = ewd_uasp_php_calendar_data.timezone_offset;
        var newTimeOffsetBeforeColon = parseInt( newTimeOffset.split(':')[0] );
        var newTimeOffsetAfterColon = parseInt( newTimeOffset.split(':')[1] );
        if(newTimeOffsetBeforeColon < 0){
            var newTimeOffsetCombined = (newTimeOffsetBeforeColon * 60) - newTimeOffsetAfterColon;
        }
        else {
            var newTimeOffsetCombined = (newTimeOffsetBeforeColon * 60) + newTimeOffsetAfterColon;
        }
        var newTimeOffsetMilli = (newTimeOffsetCombined * 60) * 1000; */

        var provider = jQuery( '#ewd-uasp-time-select' ).data( 'provider' );

        jQuery( '#ewd-uasp-provider-id' ).val( provider );

        var now = new Date();
        var client_offset_milli = now.getTimezoneOffset() * 60 * 1000;
        
		var Selected_Time = new Date( ( jQuery('#ewd-uasp-time-select-input-div select').val()*1000 ) + client_offset_milli ); 
        jQuery('#ewd-uasp-service-provider').val(jQuery('#ewd-uasp-time-select').data('provider'));
		jQuery('#ewd-uasp-selected-appointment-time').val(Selected_Time.getFullYear() + "-" + ('0' + (Selected_Time.getMonth() + 1)).slice(-2) + "-" + ('0' + Selected_Time.getDate()).slice(-2) + " " + ('0' + Selected_Time.getHours()).slice(-2) + ":" + ('0' + Selected_Time.getMinutes()).slice(-2) + ":" + ('0' + Selected_Time.getSeconds()).slice(-2));
		jQuery('#ewd-uasp-time-select, #ewd-uasp-screen-background').addClass('ewd-uasp-hidden');
	});
    
});


function Get_Formatted_Time(Appointment_Time, Hours_Format) {
    if (Hours_Format == "12") {
        var Hours = Appointment_Time.getHours();
        var Am_Pm = Hours >= 12 ? 'pm' : 'am';
        Hours = Hours % 12;
        Hours = Hours ? Hours : 12;
        var Time = ('0' + Hours).slice(-2) + ":" + ('0' + Appointment_Time.getMinutes()).slice(-2) + ' ' + Am_Pm;
    }
    else {
        var Time = ('0' + Appointment_Time.getHours()).slice(-2) + ":" + ('0' + Appointment_Time.getMinutes()).slice(-2);
    }

    return Time;
}