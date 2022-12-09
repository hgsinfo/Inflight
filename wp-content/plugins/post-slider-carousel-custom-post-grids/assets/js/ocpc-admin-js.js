//Copy shortcode
function ocpc_select_data(id) {
    var copyText = id;
    jQuery("#"+copyText).select();
    document.execCommand("copy");
}


function ocpc_show_pagination_settings() {
    if(jQuery("input[name='ocpc-showpagination']").is(':checked')) {
        jQuery("#ocpc-perpage").show();
        jQuery("#ocpc-totalposts").hide();
        jQuery("#ocpc-showpagination").show();
        jQuery("#ocpc-pagibgclr").show();
        jQuery("#ocpc-pagitxtclr").show();
        jQuery("#ocpc-pagilayout").show();
    } else {
        jQuery("#ocpc-showpagination").show();
        jQuery("#ocpc-totalposts").show();  
        jQuery("#ocpc-perpage").hide();
        jQuery("#ocpc-pagibgclr").hide();
        jQuery("#ocpc-pagitxtclr").hide();
        jQuery("#ocpc-pagilayout").hide();
    }
}


function ocpc_hide_all_sources() {
    jQuery('.ocpc_postcats_tr').hide();
    jQuery('.ocpc_posttags_tr').hide();
    jQuery('.ocpc_prodcats_tr').hide();
    jQuery('.ocpc_prodtags_tr').hide();
    jQuery('.ocpc_options_ids').hide();
}


function ocpc_show_sources(selectType, dataSource) {
    if(selectType == "post") {
        if(dataSource == "ocpc-op_categories") {
            jQuery('.ocpc_postcats_tr').css('display', 'table-row');
        } else if(dataSource == "ocpc-op_id") {
            jQuery('.ocpc_options_ids').css('display', 'table-row');
        } else if(dataSource == "ocpc-op_tags") {
            jQuery('.ocpc_posttags_tr').css('display', 'table-row');
        }
    } else if(selectType == "page") {
        if(dataSource == "ocpc-op_id") {
            jQuery('.ocpc_options_ids').css('display', 'table-row');
        }
    } else if(selectType == "attachment") {
        if(dataSource == "ocpc-op_id") {
            jQuery('.ocpc_options_ids').css('display', 'table-row');
        }
    } else if(selectType == "product") {
        if(dataSource == "ocpc-op_categories") {
            jQuery('.ocpc_prodcats_tr').css('display', 'table-row');
        } else if(dataSource == "ocpc-op_id") {
            jQuery('.ocpc_options_ids').css('display', 'table-row');
        } else if(dataSource == "ocpc-op_tags") {
            jQuery('.ocpc_prodtags_tr').css('display', 'table-row');
        }
    }
}


jQuery(document).ready(function() {

    jQuery('.ocpc_colorpicker').wpColorPicker();

    onLoadPtype = jQuery("select.posttype_change").val();

    if(onLoadPtype == 'attachment') {
        jQuery("#ocpc_pids_tips_post").hide();
        jQuery("#ocpc_pids_tips_prod").hide();
        jQuery("#ocpc_pids_tips_page").hide();
        jQuery("#ocpc_pids_tips_attchmnt").show();
    } else if (onLoadPtype == 'page') {
        jQuery("#ocpc_pids_tips_post").hide();
        jQuery("#ocpc_pids_tips_prod").hide();
        jQuery("#ocpc_pids_tips_attchmnt").hide();
        jQuery("#ocpc_pids_tips_page").show();
    } else if (onLoadPtype == 'product') {
        jQuery("#ocpc_pids_tips_post").hide();
        jQuery("#ocpc_pids_tips_page").hide();
        jQuery("#ocpc_pids_tips_attchmnt").hide();
        jQuery("#ocpc_pids_tips_prod").show();
    }  else if (onLoadPtype == 'post') {
        jQuery("#ocpc_pids_tips_prod").hide();
        jQuery("#ocpc_pids_tips_page").hide();
        jQuery("#ocpc_pids_tips_attchmnt").hide();
        jQuery("#ocpc_pids_tips_post").show();
    }


    //Create Ajax for post Options
	jQuery('body').on('change', 'select.posttype_change', function() {
        ocpc_hide_all_sources();
		var posttype = this.value;
		jQuery("select.ocpc-datasource").val("");
        jQuery("#ocpc_postbyids").val("");

        if(posttype == 'attachment') {
            jQuery("#ocpc_pids_tips_post").hide();
            jQuery("#ocpc_pids_tips_prod").hide();
            jQuery("#ocpc_pids_tips_page").hide();
            jQuery("#ocpc_pids_tips_attchmnt").show();
            jQuery(".ocpc-datasource").html('<option value=""> --- Select Options ---- </option><option value="ocpc-op_id">ID</option>');
        } else if (posttype == 'page') {
            jQuery("#ocpc_pids_tips_post").hide();
            jQuery("#ocpc_pids_tips_prod").hide();
            jQuery("#ocpc_pids_tips_attchmnt").hide();
            jQuery("#ocpc_pids_tips_page").show();
            jQuery(".ocpc-datasource").html('<option value=""> --- Select Options ---- </option><option value="ocpc-op_id">ID</option>');
        } else if (posttype == 'product') {
            jQuery("#ocpc_pids_tips_post").hide();
            jQuery("#ocpc_pids_tips_page").hide();
            jQuery("#ocpc_pids_tips_attchmnt").hide();
            jQuery("#ocpc_pids_tips_prod").show();
            jQuery(".ocpc-datasource").html('<option value=""> --- Select Options ---- </option><option value="ocpc-op_categories">Categories</option><option value="ocpc-op_id">ID</option><option value="ocpc-op_tags">Tags</option>');
        }  else if (posttype == 'post') {
            jQuery("#ocpc_pids_tips_prod").hide();
            jQuery("#ocpc_pids_tips_page").hide();
            jQuery("#ocpc_pids_tips_attchmnt").hide();
            jQuery("#ocpc_pids_tips_post").show();
            jQuery(".ocpc-datasource").html('<option value=""> --- Select Options ---- </option><option value="ocpc-op_categories">Categories</option><option value="ocpc-op_id">ID</option><option value="ocpc-op_tags">Tags</option>');
        }
	});


    //once page load selected datasource show
	var datasourceval = jQuery("select.ocpc-datasource").val();

    var dataSource = jQuery(this).val();
    var selecttype = jQuery(".posttype_change").val();
    ocpc_show_sources(selecttype, datasourceval);

    if(jQuery("input[name='ocpc-posttitle']").is(':checked') == true) 
    {
	   jQuery("#post_title_option").show();
	}
	if(jQuery("input[name='ocpc-postdate']").is(':checked') == true) 
    {
	   jQuery("#post_date_option").show();
	}
    if(jQuery("input[name='ocpc-postcats']").is(':checked') == true) 
    {
       jQuery("#post_cats_option").show();
    }
    if(jQuery("input[name='ocpc-postauthor']").is(':checked') == true) 
    {
       jQuery("#post_author_option").show();
    }
	if(jQuery("input[name='ocpc-postfeaturedimg']").is(':checked') == true) 
    {
	   jQuery("#post_featuredimg_option").show();
	}
	if(jQuery("input[name='ocpc-postdescription']").is(':checked') == true) 
    {
	   jQuery("#post_description_option").show();
	}


    //slider setting options by tabbing
    jQuery('ul.tabs li').click(function() {
        var tab_id = jQuery(this).attr('data-tab');
        jQuery('ul.tabs li').removeClass('current');
        jQuery('.tab-content').removeClass('current');
        jQuery(this).addClass('current');
        jQuery("#"+tab_id).addClass('current');
    })


    //Select post data source options
    jQuery('body').on('change', 'select.ocpc-datasource', function() {
        ocpc_hide_all_sources();

        var dataSource = jQuery(this).val();
        var selectType = jQuery(".posttype_change").val(); 

        ocpc_show_sources(selectType, dataSource);
	});

	jQuery("input[name='ocpc-posttitle']").change(function() {
	    if(this.checked) {
	        jQuery("#post_title_option").show();
	    }
	    else
	    {
	    	jQuery("#post_title_option").hide();
	    }
	});

	jQuery("input[name='ocpc-postdate']").change(function() {
	    if(this.checked) {
	        jQuery("#post_date_option").show();
	    }
	    else
	    {
	    	jQuery("#post_date_option").hide();
	    }
	});

    jQuery("input[name='ocpc-postcats']").change(function() {
        if(this.checked) {
            jQuery("#post_cats_option").show();
        }
        else
        {
            jQuery("#post_cats_option").hide();
        }
    });

    jQuery("input[name='ocpc-postauthor']").change(function() {
        if(this.checked) {
            jQuery("#post_author_option").show();
        }
        else
        {
            jQuery("#post_author_option").hide();
        }
    });

	jQuery("input[name='ocpc-postfeaturedimg']").change(function() {
	    if(this.checked) {
	        jQuery("#post_featuredimg_option").show();
	    }
	    else
	    {
	    	jQuery("#post_featuredimg_option").hide();	
	    }
	});

	jQuery("input[name='ocpc-postdescription']").change(function() {
	    if(this.checked) {
	        jQuery("#post_description_option").show();
	    }
	    else
	    {
	    	jQuery("#post_description_option").hide();	
	    }
	});

    jQuery("input[name='ocpc-showpagination']").change(function() {
        if(this.checked) {
            jQuery("#ocpc-perpage").show();
            jQuery("#ocpc-totalposts").hide();
            jQuery("#ocpc-pagibgclr").show();
            jQuery("#ocpc-pagitxtclr").show();
            jQuery("#ocpc-pagilayout").show();
        } else {
            jQuery("#ocpc-totalposts").show();  
            jQuery("#ocpc-perpage").hide();
            jQuery("#ocpc-pagibgclr").hide();
            jQuery("#ocpc-pagitxtclr").hide();
            jQuery("#ocpc-pagilayout").hide();
        }
    });

    if(jQuery("input[name='ocpc-showpagination']").is(':checked')) {
        jQuery("#ocpc-perpage").show();
        jQuery("#ocpc-totalposts").hide();
        jQuery("#ocpc-pagibgclr").show();
        jQuery("#ocpc-pagitxtclr").show();
        jQuery("#ocpc-pagilayout").show();
    } else {
        jQuery("#ocpc-totalposts").show();  
        jQuery("#ocpc-perpage").hide();
        jQuery("#ocpc-pagibgclr").hide();
        jQuery("#ocpc-pagitxtclr").hide();
        jQuery("#ocpc-pagilayout").hide();
    }

    jQuery('#ocpc_postcats_sel2').select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    action: 'PSCCPG_ocpc_postcats_get_cats'
                };
            },
            processResults: function( data ) {
            var options = [];
            if ( data ) {
                jQuery.each( data, function( index, text ) {
                    options.push( { id: text[0], text: text[1]  } );
                });
            }
            return {
                results: options
            };
        },
        cache: true
        },
        minimumInputLength: 3
    });

    jQuery('#ocpc_posttags_sel2').select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    action: 'PSCCPG_ocpc_posttags_get_tags'
                };
            },
            processResults: function( data ) {
            var options = [];
            if ( data ) {
                jQuery.each( data, function( index, text ) {
                    options.push( { id: text[0], text: text[1]  } );
                });
            }
            return {
                results: options
            };
        },
        cache: true
        },
        minimumInputLength: 3
    });

    jQuery('#ocpc_prodcats_sel2').select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    action: 'PSCCPG_ocpc_prodcats_get_cats'
                };
            },
            processResults: function( data ) {
            var options = [];
            if ( data ) {
                jQuery.each( data, function( index, text ) {
                    options.push( { id: text[0], text: text[1]  } );
                });
            }
            return {
                results: options
            };
        },
        cache: true
        },
        minimumInputLength: 3
    });

    jQuery('#ocpc_prodtags_sel2').select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    action: 'PSCCPG_ocpc_prodtags_get_tags'
                };
            },
            processResults: function( data ) {
            var options = [];
            if ( data ) {
                jQuery.each( data, function( index, text ) {
                    options.push( { id: text[0], text: text[1]  } );
                });
            }
            return {
                results: options
            };
        },
        cache: true
        },
        minimumInputLength: 3
    });


})


jQuery(document).ready(function() {
    var val = jQuery('input[type=radio][name=ocpc_option]:checked').val();
    
    if (val == 'carousel') {
        jQuery(".carousel").show();
        jQuery(".gallery").hide();
        jQuery(".masonry").hide();
        jQuery("#ocpc-showpagination input").prop("checked", false);
        jQuery("#ocpc-showpagination").hide();
        jQuery("#ocpc-perpage").hide();
        jQuery("#ocpc-pagibgclr").hide();
        jQuery("#ocpc-pagitxtclr").hide();
        jQuery("#ocpc-pagilayout").hide();
        jQuery("#ocpc-totalposts").show();
    }
    
    if (val == 'masonry') {
        jQuery(".carousel").hide();
        jQuery(".gallery").hide();
        jQuery(".masonry").show();
        ocpc_show_pagination_settings();
    }
    
    if (val == 'gallery') {
        jQuery(".gallery").show();
        jQuery(".carousel").hide();
        jQuery(".masonry").hide();
        ocpc_show_pagination_settings();
    }

    jQuery('body').on('change', '.ocpc_option', function () {
        if (this.value == 'gallery') {
            jQuery(".gallery").show();
            jQuery(".carousel").hide();
            jQuery(".masonry").hide();
            ocpc_show_pagination_settings();
        }else if (this.value == 'carousel') {
            jQuery(".carousel").show();
            jQuery(".gallery").hide();
            jQuery(".masonry").hide();
            jQuery("#ocpc-showpagination input").prop("checked", false);
            jQuery("#ocpc-showpagination").hide();
            jQuery("#ocpc-perpage").hide();
            jQuery("#ocpc-pagibgclr").hide();
            jQuery("#ocpc-pagitxtclr").hide();
            jQuery("#ocpc-pagilayout").hide();
            jQuery("#ocpc-totalposts").show();
        }else if(this.value == 'masonry') {
            jQuery(".carousel").hide();
            jQuery(".gallery").hide();
            jQuery(".masonry").show();
            ocpc_show_pagination_settings();
        }
    });
})