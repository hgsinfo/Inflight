jQuery( document ).ready( function( $ ) {
	jQuery( '.ewd-uasp-main-dashboard-review-ask' ).css( 'display', 'block' );

  jQuery(document).on( 'click', '.ewd-uasp-main-dashboard-review-ask .notice-dismiss', function( event ) {

  	var params = {
			ask_review_time: '7',
			nonce: ewd_uasp_review_ask.nonce,
			action: 'ewd_uasp_hide_review_ask'
		};

		var data = jQuery.param( params );
    
    jQuery.post( ajaxurl, data, function() {} );
  });

	jQuery( '.ewd-uasp-review-ask-yes' ).on( 'click', function() {

		jQuery( '.ewd-uasp-review-ask-feedback-text' ).removeClass( 'ewd-uasp-hidden' );
		jQuery( '.ewd-uasp-review-ask-starting-text' ).addClass( 'ewd-uasp-hidden' );

		jQuery( '.ewd-uasp-review-ask-no-thanks' ).removeClass( 'ewd-uasp-hidden' );
		jQuery( '.ewd-uasp-review-ask-review' ).removeClass( 'ewd-uasp-hidden' );

		jQuery( '.ewd-uasp-review-ask-not-really' ).addClass( 'ewd-uasp-hidden' );
		jQuery( '.ewd-uasp-review-ask-yes' ).addClass( 'ewd-uasp-hidden' );

		var params = {
			ask_review_time: '7',
			nonce: ewd_uasp_review_ask.nonce,
			action: 'ewd_uasp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-uasp-review-ask-not-really' ).on( 'click', function() {

		jQuery( '.ewd-uasp-review-ask-review-text' ).removeClass( 'ewd-uasp-hidden' );
		jQuery( '.ewd-uasp-review-ask-starting-text' ).addClass( 'ewd-uasp-hidden' );

		jQuery( '.ewd-uasp-review-ask-feedback-form' ).removeClass( 'ewd-uasp-hidden' );
		jQuery( '.ewd-uasp-review-ask-actions' ).addClass( 'ewd-uasp-hidden' );

		var params = {
			ask_review_time: '1000',
			nonce: ewd_uasp_review_ask.nonce,
			action: 'ewd_uasp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-uasp-review-ask-no-thanks' ).on( 'click', function() {

		var params = {
			ask_review_time: '1000',
			nonce: ewd_uasp_review_ask.nonce,
			action: 'ewd_uasp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );

    jQuery( '.ewd-uasp-main-dashboard-review-ask' ).css( 'display', 'none' );
	});

	jQuery( '.ewd-uasp-review-ask-review' ).on( 'click', function() {

		jQuery( '.ewd-uasp-review-ask-feedback-text' ).addClass( 'ewd-uasp-hidden' );
		jQuery( '.ewd-uasp-review-ask-thank-you-text' ).removeClass( 'ewd-uasp-hidden' );

		var params = {
			ask_review_time: '1000',
			nonce: ewd_uasp_review_ask.nonce,
			action: 'ewd_uasp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-uasp-review-ask-send-feedback' ).on( 'click', function() {

		var feedback = jQuery( '.ewd-uasp-review-ask-feedback-explanation textarea' ).val();
		var email_address = jQuery( '.ewd-uasp-review-ask-feedback-explanation input[name="feedback_email_address"]' ).val();
		var data = 'feedback=' + feedback + '&email_address=' + email_address + '&action=ewd_uasp_send_feedback';

		var params = {
			feedback: feedback,
			email_address: email_address,
			nonce: ewd_uasp_review_ask.nonce,
			action: 'ewd_uasp_send_feedback'
		};

		var data = jQuery.param( params );

    jQuery.post( ajaxurl, data, function() {} );

    var params = {
			ask_review_time: '1000',
			nonce: ewd_uasp_review_ask.nonce,
			action: 'ewd_uasp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );

    jQuery( '.ewd-uasp-review-ask-feedback-form' ).addClass( 'ewd-uasp-hidden' );
    jQuery( '.ewd-uasp-review-ask-review-text' ).addClass( 'ewd-uasp-hidden' );
    jQuery( '.ewd-uasp-review-ask-thank-you-text' ).removeClass( 'ewd-uasp-hidden' );
	});
});