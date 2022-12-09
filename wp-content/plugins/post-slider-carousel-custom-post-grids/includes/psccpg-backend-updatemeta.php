<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('PSCCPG_meta_update')) {

  class PSCCPG_meta_update {

    protected static $instance;
   
    function PSCCPG_recursive_sanitize_text_field($array) {  
        if(!empty($array)) {
            foreach ( $array as $key => $value ) {
                if ( is_array( $value ) ) {
                    $value = $this->scfw_recursive_sanitize_text_field($value);
                }else{
                    $value = sanitize_text_field( $value );
                }
            }
        }
        return $array;
    }

    function PSCCPG_meta_save( $post_id, $post ) {
        // the following line is needed because we will hook into edit_post hook, so that we can set default value of checkbox.
        if ($post->post_type != 'ocpostcarousel') {
            return;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post_id ))
            return;

        // Perform checking for before saving
        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $is_valid_nonce = (isset($_POST['OCPC_meta_save_nounce']) && wp_verify_nonce( $_POST['OCPC_meta_save_nounce'], 'OCPC_meta_save' )? 'true': 'false');

        if ( $is_autosave || $is_revision || !$is_valid_nonce ) return;

        // get all data to save

        // General Settings tab1
        $ocpc_posttype = sanitize_text_field( $_POST["ocpc-posttype"] );
        $ocpc_datasource = sanitize_text_field( $_POST["ocpc-datasource"] );
        $ocpc_postbyids = sanitize_text_field( $_POST["ocpc-postbyids"] );
        $ocpc_orderby = sanitize_text_field( $_POST["ocpc-orderby"] );
        $ocpc_sortorder = sanitize_text_field( $_POST["ocpc-sortorder"] );
        $ocpc_image_size = sanitize_text_field( $_POST["ocpc_image_size"] );
        $ocpc_slidertitle = sanitize_text_field( $_POST["ocpc-slidertitle"] );
        $ocpc_slidertitlecolor = sanitize_text_field( $_POST["ocpc-slidertitlecolor"] );
        $ocpc_slidertitlefontsize = sanitize_text_field( $_POST["ocpc-slidertitlefontsize"] );
        $ocpc_slidertitleposition = sanitize_text_field( $_POST["ocpc-slidertitleposition"] );
        $ocpc_slidertitlefweight = sanitize_text_field( $_POST["ocpc-slidertitlefweight"] );

        update_post_meta( $post_id, 'ocpc-posttype', $ocpc_posttype );
        update_post_meta( $post_id, 'ocpc-datasource', $ocpc_datasource );
        update_post_meta( $post_id, 'ocpc-postbyids', $ocpc_postbyids );
        update_post_meta( $post_id, 'ocpc-orderby', $ocpc_orderby );
        update_post_meta( $post_id, 'ocpc-sortorder', $ocpc_sortorder );
        update_post_meta( $post_id, 'ocpc_image_size', $ocpc_image_size );
        update_post_meta( $post_id, 'ocpc-slidertitle', $ocpc_slidertitle );
        update_post_meta( $post_id, 'ocpc-slidertitlecolor', $ocpc_slidertitlecolor );
        update_post_meta( $post_id, 'ocpc-slidertitlefontsize', $ocpc_slidertitlefontsize );
        update_post_meta( $post_id, 'ocpc-slidertitleposition', $ocpc_slidertitleposition );
        update_post_meta( $post_id, 'ocpc-slidertitlefweight', $ocpc_slidertitlefweight );
        // End General Settings tab1

        //Post Content Settings        
        $ocpc_postdesclength = sanitize_text_field( $_POST["ocpc-postdesclength"] );
        $ocpc_readmoretext = sanitize_text_field( $_POST["ocpc-readmoretext"] );
        $ocpc_readmoretextcolor = sanitize_text_field( $_POST["ocpc-readmoretextcolor"] );
        $ocpc_placeholderimage = sanitize_text_field($_POST['ocpc-placeholderimage']);
        $ocpc_image = sanitize_text_field($_POST['ocpc-image']);
        
        update_post_meta( $post_id, 'ocpc-postdesclength', $ocpc_postdesclength );
        update_post_meta( $post_id, 'ocpc-readmoretext', $ocpc_readmoretext );
        update_post_meta( $post_id, 'ocpc-readmoretextcolor', $ocpc_readmoretextcolor );
        update_post_meta( $post_id, 'ocpc-placeholderimage', $ocpc_placeholderimage);
        update_post_meta( $post_id, 'ocpc-image', $ocpc_image);
        //End Post Content Settings  

        //template setting data
        $ocpc_totalposts = sanitize_text_field( $_POST["ocpc-totalposts"] );
        update_post_meta( $post_id, 'ocpc-totalposts', $ocpc_totalposts );

        if(isset($_POST['ocpc-showpagination']) && $_POST['ocpc-showpagination'] == 'on') {
            $ocpc_showpagination = sanitize_text_field( $_POST['ocpc-showpagination'] );
        } else {
            $ocpc_showpagination = 'off';
        }
        update_post_meta( $post_id, 'ocpc-showpagination', $ocpc_showpagination );

        update_post_meta( $post_id, 'ocpc-template', sanitize_text_field( $_POST['sel_template'] ));
        update_post_meta( $post_id, 'ocpc-option', sanitize_text_field( $_POST['ocpc_option'] ));

        update_post_meta( $post_id, 'ocpc_gl_space_img', sanitize_text_field( $_POST['ocpc_gl_space_img'] ));
        update_post_meta( $post_id, 'ocpc_gl_clm', sanitize_text_field( $_POST['ocpc_gl_clm'] ));
        update_post_meta( $post_id, 'ocpc_ms_clm', sanitize_text_field( $_POST['ocpc_ms_clm'] ));
        update_post_meta( $post_id, 'ocpc_trbgcolor', sanitize_text_field( $_POST['ocpc_trbgcolor'] ));
        //End template setting data 

        //Post Title
        if(isset($_POST["ocpc-posttitle"]) && !empty($_POST["ocpc-posttitle"])) {
            $ocpc_posttitle = sanitize_text_field( $_POST["ocpc-posttitle"] );
        } else {
            $ocpc_posttitle = 'no';
        }
        
        $ocpc_posttitlecolor = sanitize_text_field( $_POST["ocpc-posttitlecolor"] );
        $ocpc_posttitlefontsize = sanitize_text_field( $_POST["ocpc-posttitlefontsize"] );
        $ocpc_posttitleposition = sanitize_text_field( $_POST["ocpc-posttitleposition"] );
        $ocpc_posttitlefweight = sanitize_text_field( $_POST["ocpc-posttitlefweight"] );
        $ocpc_posttitledata = array('ocpc_posttitle' => $ocpc_posttitle,
                                    'ocpc_posttitlecolor' => $ocpc_posttitlecolor,
                                    'ocpc_posttitlefontsize' => $ocpc_posttitlefontsize,
                                    'ocpc_posttitleposition' => $ocpc_posttitleposition,
                                    'ocpc_posttitlefweight' => $ocpc_posttitlefweight);
        $ocpc_posttitledata=serialize($ocpc_posttitledata);
        update_post_meta( $post_id, 'ocpc-posttitledata', $ocpc_posttitledata);

        //Post Date
        if(isset($_POST["ocpc-postdate"]) && !empty($_POST["ocpc-postdate"])) {
            $ocpc_postdate = sanitize_text_field( $_POST["ocpc-postdate"] );
        } else {
            $ocpc_postdate = 'no';
        }

        $ocpc_postdatecolor = sanitize_text_field( $_POST["ocpc-postdatecolor"] );
        $ocpc_postdatefontsize = sanitize_text_field( $_POST["ocpc-postdatefontsize"] );
        $ocpc_postdateposition = sanitize_text_field( $_POST["ocpc-postdateposition"] );
        $ocpc_postdatefweight = sanitize_text_field( $_POST["ocpc-postdatefweight"] );
        $ocpc_postdatedata = array('ocpc_postdate' => $ocpc_postdate,
                                    'ocpc_postdatecolor' => $ocpc_postdatecolor,
                                    'ocpc_postdatefontsize' => $ocpc_postdatefontsize,
                                    'ocpc_postdateposition' => $ocpc_postdateposition,
                                    'ocpc_postdatefweight' => $ocpc_postdatefweight);
        $ocpc_postdatedata=serialize($ocpc_postdatedata);
        update_post_meta( $post_id, 'ocpc-postdatedata', $ocpc_postdatedata);


        //Post categories
        if(isset($_POST["ocpc-postcats"]) && !empty($_POST["ocpc-postcats"])) {
            $ocpc_postcats = sanitize_text_field( $_POST["ocpc-postcats"] );
        } else {
            $ocpc_postcats = 'no';
        }

        $ocpc_postcatscolor = sanitize_text_field( $_POST["ocpc-postcatscolor"] );
        $ocpc_postcatsfontsize = sanitize_text_field( $_POST["ocpc-postcatsfontsize"] );
        $ocpc_postcatsposition = sanitize_text_field( $_POST["ocpc-postcatsposition"] );
        $ocpc_postcatsfweight = sanitize_text_field( $_POST["ocpc-postcatsfweight"] );
        $ocpc_postcatsdata = array('ocpc_postcats' => $ocpc_postcats,
                                    'ocpc_postcatscolor' => $ocpc_postcatscolor,
                                    'ocpc_postcatsfontsize' => $ocpc_postcatsfontsize,
                                    'ocpc_postcatsposition' => $ocpc_postcatsposition,
                                    'ocpc_postcatsfweight' => $ocpc_postcatsfweight);
        $ocpc_postcatsdata = serialize($ocpc_postcatsdata);
        update_post_meta( $post_id, 'ocpc-postcatsdata', $ocpc_postcatsdata);

        //Post author
        if(isset($_POST["ocpc-postauthor"]) && !empty($_POST["ocpc-postauthor"])) {
            $ocpc_postauthor = sanitize_text_field( $_POST["ocpc-postauthor"] );
        } else {
            $ocpc_postauthor = 'no';
        }

        $ocpc_postauthorcolor = sanitize_text_field( $_POST["ocpc-postauthorcolor"] );
        $ocpc_postauthorfontsize = sanitize_text_field( $_POST["ocpc-postauthorfontsize"] );
        $ocpc_postauthorposition = sanitize_text_field( $_POST["ocpc-postauthorposition"] );
        $ocpc_postauthorfweight = sanitize_text_field( $_POST["ocpc-postauthorfweight"] );
        $ocpc_postauthordata = array('ocpc_postauthor' => $ocpc_postauthor,
                                    'ocpc_postauthorcolor' => $ocpc_postauthorcolor,
                                    'ocpc_postauthorfontsize' => $ocpc_postauthorfontsize,
                                    'ocpc_postauthorposition' => $ocpc_postauthorposition,
                                    'ocpc_postauthorfweight' => $ocpc_postauthorfweight);
        $ocpc_postauthordata=serialize($ocpc_postauthordata);
        update_post_meta( $post_id, 'ocpc-postauthordata', $ocpc_postauthordata);

        //Post Description Data
        if(isset($_POST["ocpc-postdescription"]) && !empty($_POST["ocpc-postdescription"])) {
            $ocpc_postdescription = sanitize_text_field( $_POST["ocpc-postdescription"] );
        } else {
            $ocpc_postdescription = 'no';
        }

        $ocpc_postdescriptioncolor = sanitize_text_field( $_POST["ocpc-postdescriptioncolor"] );
        $ocpc_postdescriptionfontsize = sanitize_text_field( $_POST["ocpc-postdescriptionfontsize"] );
        $ocpc_postdescriptionposition = sanitize_text_field( $_POST["ocpc-postdescriptionposition"] );
        $ocpc_postdescriptionfweight = sanitize_text_field( $_POST["ocpc-postdescriptionfweight"] );
        $ocpc_postdescriptiondata = array('ocpc_postdescription' => $ocpc_postdescription,
                                    'ocpc_postdescriptioncolor' => $ocpc_postdescriptioncolor,
                                    'ocpc_postdescriptionfontsize' => $ocpc_postdescriptionfontsize,
                                    'ocpc_postdescriptionposition' => $ocpc_postdescriptionposition,
                                    'ocpc_postdescriptionfweight' => $ocpc_postdescriptionfweight);
        $ocpc_postdescriptiondata=serialize($ocpc_postdescriptiondata);
        update_post_meta( $post_id, 'ocpc-postdescriptiondata', $ocpc_postdescriptiondata);

        //slider setting in desktop
        $ocpc_perrow = sanitize_text_field( $_POST["ocpc-perrow"] );
        $ocpc_autoplay = sanitize_text_field( $_POST["ocpc-autoplay"] );
        $ocpc_spacingbetwee = sanitize_text_field( $_POST["ocpc_gl_space_img"] );
        $ocpc_autoplaytimeout = sanitize_text_field( $_POST["ocpc-autoplaytimeout"] );
        $ocpc_sliderarrow = sanitize_text_field( $_POST["ocpc-sliderarrow"] );
        $ocpc_mobileperrow = sanitize_text_field( $_POST["ocpc-mobileperrow"] );
        $ocpc_tabletperrow = sanitize_text_field( $_POST["ocpc-tabletperrow"] );

        if( isset( $_POST['ocpc_postcats_sel2'] ) ) {
            update_post_meta( $post_id, 'ocpc_postcats_sel2', $this->PSCCPG_recursive_sanitize_text_field($_POST['ocpc_postcats_sel2']) );
        }
        else {
            delete_post_meta( $post_id, 'ocpc_postcats_sel2' );
        }

        if( isset( $_POST['ocpc_posttags_sel2'] ) ) {
            update_post_meta( $post_id, 'ocpc_posttags_sel2', $this->PSCCPG_recursive_sanitize_text_field($_POST['ocpc_posttags_sel2']) );
        }
        else {
            delete_post_meta( $post_id, 'ocpc_posttags_sel2' );
        }

        if( isset( $_POST['ocpc_prodcats_sel2'] ) ) {
            update_post_meta( $post_id, 'ocpc_prodcats_sel2', $this->PSCCPG_recursive_sanitize_text_field($_POST['ocpc_prodcats_sel2']) );
        }
        else {
            delete_post_meta( $post_id, 'ocpc_prodcats_sel2' );
        }

        if( isset( $_POST['ocpc_prodtags_sel2'] ) ) {
            update_post_meta( $post_id, 'ocpc_prodtags_sel2', $this->PSCCPG_recursive_sanitize_text_field($_POST['ocpc_prodtags_sel2']) );
        }
        else {
            delete_post_meta( $post_id, 'ocpc_prodtags_sel2' );
        }

        update_post_meta( $post_id, 'ocpc-perrow', $ocpc_perrow );
        update_post_meta( $post_id, 'ocpc-autoplay', $ocpc_autoplay);
        update_post_meta( $post_id, 'ocpc-spacingbetwee', $ocpc_spacingbetwee );
        update_post_meta( $post_id, 'ocpc-autoplaytimeout', $ocpc_autoplaytimeout );
        update_post_meta( $post_id, 'ocpc-sliderarrow', $ocpc_sliderarrow );
        update_post_meta( $post_id, 'ocpc-mobileperrow', $ocpc_mobileperrow );
        update_post_meta( $post_id, 'ocpc-tabletperrow', $ocpc_tabletperrow );

        update_post_meta( $post_id, 'ocpc-pagilayout', sanitize_text_field( $_POST["ocpc-pagilayout"] ) );
        update_post_meta( $post_id, 'ocpc-pagitxtclr', sanitize_text_field( $_POST["ocpc-pagitxtclr"] ) );
        update_post_meta( $post_id, 'ocpc-pagibgclr', sanitize_text_field( $_POST["ocpc-pagibgclr"] ) );

    }

    function init() {
        // Update all slider options
        add_action( 'edit_post', array($this, 'PSCCPG_meta_save'), 10, 2);
    }

    public static function instance() {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->init();
      }
      return self::$instance;
    }

  }

  PSCCPG_meta_update::instance();
}