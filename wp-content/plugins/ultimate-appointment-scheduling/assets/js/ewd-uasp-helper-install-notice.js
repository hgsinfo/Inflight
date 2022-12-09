jQuery( document ).ready( function( $ ) {

  jQuery(document).on( 'click', '.ewd-uasp-helper-install-notice .notice-dismiss', function( event ) {
    var data = jQuery.param({
      action: 'ewd_uasp_hide_helper_notice',
      nonce: ewd_uasp_helper_notice.nonce
    });

    jQuery.post( ajaxurl, data, function() {} );
  });
});