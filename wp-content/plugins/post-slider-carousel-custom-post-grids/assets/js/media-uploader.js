jQuery(function($){
    /*
     * Select/Upload image(s) event
     */
    $('body').on('click', '.ocpc_upload_image_button ', function(e) {
        e.preventDefault();
 
        var button = $(this),
        custom_uploader = wp.media({
            title: 'Insert image',
            library : {
                type : 'image'
            },
            button: {
                text: 'Use this image' // button label text
            },
            multiple: false // for multiple image selection set to true
        }).on('select', function() { // it also has "open" and "close" events 
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            
            if ($(".ocpc_plchld_prvw_image").length == 0) {
                $( "<img src='' class='ocpc_plchld_prvw_image' width='50px' height='50px'>" ).insertAfter( $( ".ocpc_upload_image_main_div" ) );
            }

            $('.ocpc_plchld_prvw_image').attr('src', attachment.url);
            $(".placeholderimage_hidden_img").val(attachment.id);
        })
        .open();
    });
    $('body').on('click', '.ocpc_remove_image_button ', function(e) {
        $('.ocpc_plchld_prvw_image').attr('src', '');
        $(".placeholderimage_hidden_img").val('');
        $( ".ocpc_plchld_prvw_image" ).remove();
        return false;
    });
});
