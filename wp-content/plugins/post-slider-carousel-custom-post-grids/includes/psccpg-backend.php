<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('PSCCPG_menu')) {

    class PSCCPG_menu {

        protected static $instance;
        /**
         * Registers ADL Post Slider post type.
         */

        function PSCCPG_create_menu() {
            $post_type = 'ocpostcarousel';
            $singular_name = 'Post Carousel';
            $plural_name = 'Post Carousel';
            $slug = 'ocpostcarousel';
            $labels = array(
                'name'               => _x( $plural_name, 'post type general name', 'ocpc' ),
                'singular_name'      => _x( $singular_name, 'post type singular name', 'ocpc' ),
                'menu_name'          => _x( $singular_name, 'admin menu name', 'ocpc' ),
                'name_admin_bar'     => _x( $singular_name, 'add new name on admin bar', 'ocpc' ),
                'add_new'            => __( 'Add New', 'ocpc' ),
                'add_new_item'       => __( 'Add New '.$singular_name, 'ocpc' ),
                'new_item'           => __( 'New '.$singular_name, 'ocpc' ),
                'edit_item'          => __( 'Edit '.$singular_name, 'ocpc' ),
                'view_item'          => __( 'View '.$singular_name, 'ocpc' ),
                'all_items'          => __( 'All '.$plural_name, 'ocpc' ),
                'search_items'       => __( 'Search '.$plural_name, 'ocpc' ),
                'parent_item_colon'  => __( 'Parent '.$plural_name.':', 'ocpc' ),
                'not_found'          => __( 'No sliders found.', 'ocpc' ),
                'not_found_in_trash' => __( 'No books found in Trash.', 'ocpc' )
            );

            $args = array(
                'labels'             => $labels,
                'description'        => __( 'Description.', 'ocpc' ),
                'public'             => false,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => $slug ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title' ),
                'menu_icon'          => 'dashicons-images-alt'


            );
            register_post_type( $post_type, $args );
        }

        // Add post Meta box for Slider Settings
        function PSCCPG_add_meta_box() {
            add_meta_box(
                'OCPC_metabox',
                __( 'Slider Settings', 'ocpc' ),
                array($this, 'PSCCPG_metabox_cb'),
                'ocpostcarousel',
                'normal'
            );
        }

        //Add all slider Options
        function PSCCPG_metabox_cb( $post ) {
            // Add a nonce field so we can check for it later.
            wp_nonce_field( 'OCPC_meta_save', 'OCPC_meta_save_nounce' );
            ?>

             <div class="ocpc-container">
                <div class="ocpc_shortcode">
                   <span><?php echo __( 'Shortcode:', PSCCPG_DOMAIN );?></span><input type="text" id="ocpc-selectdata_<?php echo $post->ID;;?>" value="[ocpc-post-carousel id=<?php echo $post->ID;?>]" size="30" onclick="ocpc_select_data(this.id)" readonly>
                </div>
                <ul class="tabs">
                    <li class="tab-link current" data-tab="tab-general"><?php echo __( 'General Settings', PSCCPG_DOMAIN );?></li>
                    <li class="tab-link" data-tab="tab-title"><?php echo __( 'Title Settings', PSCCPG_DOMAIN );?></li>
                    <li class="tab-link" data-tab="tab-data"><?php echo __( 'Data Settings', PSCCPG_DOMAIN );?></li>
                    <li class="tab-link" data-tab="tab-template"><?php echo __( 'Template Settings', PSCCPG_DOMAIN );?></li>
                </ul>
                <div id="tab-general" class="tab-content current">
                    <fieldset>
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Post Type', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <?php

                                            $ocpc_args = array(
                                                        'public'   => true
                                                    );
                                            $ocpc_output = 'names'; // names or objects, note names is the default
                                            $ocpc_operator = 'and'; // 'and' or 'or'
                                            $ocpc_post_types = get_post_types( $ocpc_args, $ocpc_output, $ocpc_operator );
                                        ?>
                                        <select name="ocpc-posttype" class='posttype_change'>
                                            <?php
                                            if(!empty($ocpc_post_types)) {
                                                foreach ( $ocpc_post_types  as $ocpc_post_type ) {
                                                    ?>
                                                    <option value="<?php echo $ocpc_post_type; ?>" <?php if(get_post_meta( $post->ID, 'ocpc-posttype', true ) == $ocpc_post_type){echo "selected";} ?>><?php echo __( ucwords($ocpc_post_type) , PSCCPG_DOMAIN );?></option>
                                                    <?php 
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr class="ocpc_add_sourcedata" valign="top">
                                <?php
                                $defult_posttype = get_post_meta( $post->ID, 'ocpc-posttype', true ); 
                                if($defult_posttype == '') {
                                    $defult_posttype = 'post';
                                }

                                if($defult_posttype == 'post' || $defult_posttype == 'product') {
                                ?>
                                    <th scope="row">
                                        <label><?php echo __( 'Select Data Source', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select name="ocpc-datasource" class="ocpc-datasource">
                                            <option value=""> <?php echo __( '--- Select Options ----', PSCCPG_DOMAIN );?> </option>
                                            <option value="ocpc-op_categories" <?php if(get_post_meta( $post->ID, 'ocpc-datasource', true ) == 'ocpc-op_categories'){echo "selected";} ?>><?php echo __( 'Categories', PSCCPG_DOMAIN );?></option>
                                            <option value="ocpc-op_id" <?php if(get_post_meta( $post->ID, 'ocpc-datasource', true ) == 'ocpc-op_id'){echo "selected";} ?>><?php echo __( 'ID', PSCCPG_DOMAIN );?></option>
                                            <option value="ocpc-op_tags" <?php if(get_post_meta( $post->ID, 'ocpc-datasource', true ) == 'ocpc-op_tags'){echo "selected";} ?>><?php echo __( 'Tags', PSCCPG_DOMAIN );?></option>
                                        </select>
                                    </td>
                               <?php } else if($defult_posttype == 'page') {?>
                                    <th scope="row">
                                        <label><?php echo __( 'Select Data Source', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td>
                                        <select name="ocpc-datasource" class="ocpc-datasource">
                                            <option value=""> <?php echo __( '--- Select Options ----', PSCCPG_DOMAIN );?> </option>
                                            <option value="ocpc-op_id" <?php if(get_post_meta( $post->ID, 'ocpc-datasource', true ) == 'ocpc-op_id'){echo "selected";} ?>><?php echo __( 'ID', PSCCPG_DOMAIN );?></option>>
                                        </select>
                                    </td>
                               <?php } else if($defult_posttype == 'attachment') {?>
                                    <th scope="row">
                                        <label><?php echo __( 'Select Data Source', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td>
                                        <select name="ocpc-datasource" class="ocpc-datasource">
                                            <option value=""> <?php echo __( '--- Select Options ----', PSCCPG_DOMAIN );?> </option>
                                            <option value="ocpc-op_id" <?php if(get_post_meta( $post->ID, 'ocpc-datasource', true ) == 'ocpc-op_id'){echo "selected";} ?>><?php echo __( 'ID', PSCCPG_DOMAIN );?></option>>
                                        </select>
                                    </td>
                               <?php } ?>
                                </tr>

                                <tr valign="top" class="ocpc_postcats_tr" id='ocpc_postcats_tr'>
                                    <th scope="row">
                                        <label><?php echo __( 'Post Categories', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select multiple id="ocpc_postcats_sel2" name="ocpc_postcats_sel2[]" style="width:99%; max-width:25em;">
                                            <?php
                                            $ocpc_postcats_sel2 = get_post_meta( $post->ID, 'ocpc_postcats_sel2', true );

                                            if( !empty($ocpc_postcats_sel2) ) {
                                                foreach( $ocpc_postcats_sel2 as $term_id ) {
                                                    $term_name = get_term( $term_id )->name;
                                                    $term_name = ( mb_strlen( $term_name ) > 50 ) ? mb_substr( $term_name, 0, 49 ) . '...' : $term_name;
                                                    echo '<option value="' . $term_id . '" selected="selected">' . $term_name . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top" class="ocpc_posttags_tr" id='ocpc_posttags_tr'>
                                    <th scope="row">
                                        <label><?php echo __( 'Post Tags', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select multiple id="ocpc_posttags_sel2" name="ocpc_posttags_sel2[]" style="width:99%; max-width:25em;">
                                            <?php
                                            $ocpc_posttags_sel2 = get_post_meta( $post->ID, 'ocpc_posttags_sel2', true );

                                            if( !empty($ocpc_posttags_sel2) ) {
                                                foreach( $ocpc_posttags_sel2 as $term_id ) {
                                                    $term_name = get_term( $term_id )->name;
                                                    $term_name = ( mb_strlen( $term_name ) > 50 ) ? mb_substr( $term_name, 0, 49 ) . '...' : $term_name;
                                                    echo '<option value="' . $term_id . '" selected="selected">' . $term_name . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top" class="ocpc_prodcats_tr" id='ocpc_prodcats_tr'>
                                    <th scope="row">
                                        <label><?php echo __( 'Product Categories', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select multiple id="ocpc_prodcats_sel2" name="ocpc_prodcats_sel2[]" style="width:99%; max-width:25em;">
                                            <?php
                                            $ocpc_prodcats_sel2 = get_post_meta( $post->ID, 'ocpc_prodcats_sel2', true );

                                            if( !empty($ocpc_prodcats_sel2) ) {
                                                foreach( $ocpc_prodcats_sel2 as $term_id ) {
                                                    $term_name = get_term( $term_id )->name;
                                                    $term_name = ( mb_strlen( $term_name ) > 50 ) ? mb_substr( $term_name, 0, 49 ) . '...' : $term_name;
                                                    echo '<option value="' . $term_id . '" selected="selected">' . $term_name . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top" class="ocpc_prodtags_tr" id='ocpc_prodtags_tr'>
                                    <th scope="row">
                                        <label><?php echo __( 'Product Tags', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select multiple id="ocpc_prodtags_sel2" name="ocpc_prodtags_sel2[]" style="width:99%; max-width:25em;">
                                            <?php
                                            $ocpc_prodtags_sel2 = get_post_meta( $post->ID, 'ocpc_prodtags_sel2', true );

                                            if( !empty($ocpc_prodtags_sel2) ) {
                                                foreach( $ocpc_prodtags_sel2 as $term_id ) {
                                                    $term_name = get_term( $term_id )->name;
                                                    $term_name = ( mb_strlen( $term_name ) > 50 ) ? mb_substr( $term_name, 0, 49 ) . '...' : $term_name;
                                                    echo '<option value="' . $term_id . '" selected="selected">' . $term_name . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr valign="top" class="ocpc_options_ids" style="display:none;">
                                    <th scope="row">
                                        <label><?php echo __( 'Post By IDs', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <input type="text" id="ocpc_postbyids" name="ocpc-postbyids" size="100" value="<?php echo get_post_meta( $post->ID, 'ocpc-postbyids', true ); ?>">
                                        <p class="ocpc-tips" id="ocpc_pids_tips_post"><?php echo __( 'Enter post IDs seperated by commas. <strong>(ex. 10, 15, 17, 19)</strong>', PSCCPG_DOMAIN );?></p>
                                        <p class="ocpc-tips" id="ocpc_pids_tips_prod"><?php echo __( 'Enter product IDs seperated by commas. <strong>(ex. 10, 15, 17, 19)</strong>', PSCCPG_DOMAIN );?></p>
                                        <p class="ocpc-tips" id="ocpc_pids_tips_page"><?php echo __( 'Enter page IDs seperated by commas. <strong>(ex. 10, 15, 17, 19)</strong>', PSCCPG_DOMAIN );?></p>
                                        <p class="ocpc-tips" id="ocpc_pids_tips_attchmnt"><?php echo __( 'Enter attachment IDs seperated by commas. <strong>(ex. 10, 15, 17, 19)</strong>', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Order by', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select name="ocpc-orderby">
                                            <option  value="date" <?php if(get_post_meta( $post->ID, 'ocpc-orderby', true ) == 'date'){echo "selected";} ?>><?php echo __( 'Date', PSCCPG_DOMAIN );?></option>
                                            <option  value="ID" <?php if(get_post_meta( $post->ID, 'ocpc-orderby', true ) == 'ID'){echo "selected";} ?>><?php echo __( 'Order by post ID', PSCCPG_DOMAIN );?></option>
                                            <option  value="author" <?php if(get_post_meta( $post->ID, 'ocpc-orderby', true ) == 'author'){echo "selected";} ?>><?php echo __( 'Author', PSCCPG_DOMAIN );?></option>
                                            <option  value="rand" <?php if(get_post_meta( $post->ID, 'ocpc-orderby', true ) == 'rand'){echo "selected";} ?>><?php echo __( 'Random order', PSCCPG_DOMAIN );?></option>
                                        </select>
                                        <p class="ocpc-tips"><?php echo __( 'Select order type.', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Sort order', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select name="ocpc-sortorder">
                                            <option value="DESC" <?php if(get_post_meta( $post->ID, 'ocpc-sortorder', true ) == 'DESC'){echo "selected";} ?>><?php echo __( 'Descending', PSCCPG_DOMAIN );?></option>
                                            <option value="ASC" <?php if(get_post_meta( $post->ID, 'ocpc-sortorder', true ) == 'ASC'){echo "selected";} ?>><?php echo __( 'Ascending', PSCCPG_DOMAIN );?></option>
                                        </select>
                                        <p class="ocpc-tips"><?php echo __( 'Select sorting order.', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Image Size', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select name="ocpc_image_size">
                                            <?php
                                            $ocpc_image_sizes = get_intermediate_image_sizes();
                                            foreach ($ocpc_image_sizes as $size) {
                                            	$size_name = str_replace(array('-', '_'), ' ', $size);
                                                $size_name = ucwords($size_name);
                                                
                                                $ocpc_image_size = get_post_meta( $post->ID, 'ocpc_image_size', true );

                                                if($ocpc_image_size == '') {
                                                    
                                                }


                                                ?>
                                                <option value="<?php echo $size; ?>" <?php if(get_post_meta( $post->ID, 'ocpc_image_size', true ) == $size){echo "selected";} ?>><?php echo $size_name; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <p class="ocpc-tips"><?php echo __( 'Select Image Size.', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
                <div id="tab-title" class="tab-content">
                    <fieldset>
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Title', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <input type="text" name="ocpc-slidertitle" size="50" placeholder="Latest Post" value="<?php echo get_post_meta( $post->ID, 'ocpc-slidertitle', true ); ?>">
                                        <p class="ocpc-tips"><?php echo __( 'Add Slider title', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Title Color', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <?php
                                        $slidertitlecolor = get_post_meta( $post->ID, 'ocpc-slidertitlecolor', true );

                                        if($slidertitlecolor == '') {
                                            $slidertitlecolor = '#607d8b';
                                        }
                                        ?>
                                        <input type="text" name="ocpc-slidertitlecolor" class="ocpc_colorpicker" value="<?php echo $slidertitlecolor; ?>">
                                        <p class="ocpc-tips"><?php echo __( 'Add Slider title color', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Title Font Size', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <?php
                                        $slidertitlefontsize = get_post_meta( $post->ID, 'ocpc-slidertitlefontsize', true );
                                        if($slidertitlefontsize == '') {
                                            $slidertitlefontsize = '30';
                                        }
                                        ?>
                                        <input type="number" name="ocpc-slidertitlefontsize" placeholder="eg. 30" value="<?php echo $slidertitlefontsize; ?>">
                                        <p class="ocpc-tips"><?php echo __( 'Add Slider title Font Size', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Title Position', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <select name="ocpc-slidertitleposition" >
                                            <option value="Center" <?php if(get_post_meta( $post->ID, 'ocpc-slidertitleposition', true ) == 'Center'){echo "selected";} ?>><?php echo __( 'Center', PSCCPG_DOMAIN );?></option>
                                            <option value="Left" <?php if(get_post_meta( $post->ID, 'ocpc-slidertitleposition', true ) == 'Left'){echo "selected";} ?>><?php echo __( 'Left', PSCCPG_DOMAIN );?></option>
                                            <option value="Right" <?php if(get_post_meta( $post->ID, 'ocpc-slidertitleposition', true ) == 'Right'){echo "selected";} ?>><?php echo __( 'Right', PSCCPG_DOMAIN );?></option>
                                        </select>
                                        <p class="ocpc-tips"><?php echo __( 'Add Slider title Position', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Title Font Weight', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <?php 
                                        $defultvalue = get_post_meta( $post->ID, 'ocpc-slidertitlefweight', true );
                                        if(!empty($defultvalue))
                                        {
                                            $slidertitlefweight_dval = $defultvalue;
                                        }
                                        else
                                        {
                                            $slidertitlefweight_dval = 'Bold';
                                        }
                                        ?>
                                        <select name="ocpc-slidertitlefweight">
                                            <option value="Normal" <?php if($slidertitlefweight_dval == 'Normal'){echo "selected";} ?>><?php echo __( 'Normal', PSCCPG_DOMAIN );?></option>
                                            <option value="Bold" <?php if($slidertitlefweight_dval == 'Bold'){echo "selected";} ?>><?php echo __( 'Bold', PSCCPG_DOMAIN );?></option>
                                        </select>
                                        <p class="ocpc-tips"><?php echo __( 'Add Slider Title Font Weight', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
                <div id="tab-data" class="tab-content">
                	<?php 
                        $posttitledata = unserialize(get_post_meta( $post->ID, 'ocpc-posttitledata', true ));
                        $postdatedata = unserialize(get_post_meta( $post->ID, 'ocpc-postdatedata', true ));
                        $postcatsdata = unserialize(get_post_meta( $post->ID, 'ocpc-postcatsdata', true ));
                        $postauthordata = unserialize(get_post_meta( $post->ID, 'ocpc-postauthordata', true ));
                        $postdescriptiondata = unserialize(get_post_meta( $post->ID, 'ocpc-postdescriptiondata', true ));

                        if(empty($posttitledata['ocpc_posttitle'])) {
                            $ocpc_posttitle_val = 'yes';
                        } else {
                            $ocpc_posttitle_val = $posttitledata['ocpc_posttitle'];
                        }

                        if(empty($postdatedata['ocpc_postdate'])) {
                            $ocpc_postdate_val = 'yes';
                        } else {
                            $ocpc_postdate_val = $postdatedata['ocpc_postdate'];
                        }

                        if(empty($postcatsdata['ocpc_postcats'])) {
                            $ocpc_postcats_val = 'no';
                        } else {
                            $ocpc_postcats_val = $postcatsdata['ocpc_postcats'];
                        }
                        
                        if(empty($postauthordata['ocpc_postauthor'])) {
                            $ocpc_postauthor_val = 'yes';
                        } else {
                            $ocpc_postauthor_val = $postauthordata['ocpc_postauthor'];
                        }


                        if(empty($postdescriptiondata['ocpc_postdescription'])) {
                            $ocpc_postdescription_val = 'yes';
                        } else {
                            $ocpc_postdescription_val = $postdescriptiondata['ocpc_postdescription'];
                        }
                    ?>
                    <fieldset>
                        <table class="form-table">
                            <tbody>
                            	<h3><?php echo __( 'Show/Hide Post Data', PSCCPG_DOMAIN );?></h3>
                                
                                <tr valign="top">
                                    <th scope="row">
                                        <input type="checkbox" name="ocpc-posttitle" id='posttitle' value="yes" <?php if($ocpc_posttitle_val == 'yes'){echo "checked";} ?>> <?php echo __( 'Post Title', PSCCPG_DOMAIN );?>
                                        <p class="ocpc-tips"><?php echo __( 'Show the Post Title.', PSCCPG_DOMAIN );?></p>
                                    </th>
                                    <td class="forminp forminp-text">
                                       	<div class="post_option" id="post_title_option" style="display: none">
                                            <ul>
                                                <li>
                                                	<label><?php echo __( 'Color', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <?php
                                                    $posttitlecolor = $posttitledata['ocpc_posttitlecolor'];
                                                    if($posttitlecolor == '') {
                                                        $posttitlecolor = '#607d8b';
                                                    }
                                                    ?>
                                                	<input type="text" name="ocpc-posttitlecolor" class="ocpc_colorpicker" value="<?php echo $posttitlecolor; ?>">
                                                </li>
                                                <li>
                                                	<label><?php echo __( 'Font Size', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <?php
                                                    $posttitlefontsize = $posttitledata['ocpc_posttitlefontsize'];
                                                    if($posttitlefontsize == '') {
                                                        $posttitlefontsize = '16';
                                                    }
                                                    ?>
                                                	<input type="number" name="ocpc-posttitlefontsize" placeholder="eg. 16" value="<?php echo $posttitlefontsize; ?>">
                                                </li>
                                                <li>
                                                	<label><?php echo __( 'Position', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
    	                                            <?php
    		                                            $defultvalue_ocpc_posttitleposition = $posttitledata['ocpc_posttitleposition'];
    		                                            if(!empty($defultvalue_ocpc_posttitleposition)) {
    		                                                $ocpc_posttitleposition_dval = $defultvalue_ocpc_posttitleposition;
    		                                            }else{
    		                                                $ocpc_posttitleposition_dval = 'Left';
    		                                            }
    	                                            ?>
                                                	<select name="ocpc-posttitleposition" >
                                                        <option value="Center" <?php if($ocpc_posttitleposition_dval == 'Center'){echo "selected";} ?>><?php echo __( 'Center', PSCCPG_DOMAIN );?></option>
                                                        <option value="Left" <?php if($ocpc_posttitleposition_dval == 'Left'){echo "selected";} ?>><?php echo __( 'Left', PSCCPG_DOMAIN );?></option>
                                                        <option value="Right" <?php if($ocpc_posttitleposition_dval == 'Right'){echo "selected";} ?>><?php echo __( 'Right', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                                <li>
                                                	<label><?php echo __( 'Font Weight', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
    	                                            <?php
    	                                            	$defultvalue_ocpc_posttitlefweight = $posttitledata['ocpc_posttitlefweight'];
    	                                            	if(!empty($defultvalue_ocpc_posttitlefweight)) {
    	                                                	$ocpc_posttitlefweight_dval = $defultvalue_ocpc_posttitlefweight;
    	                                            	}else{
    	                                                	$ocpc_posttitlefweight_dval = 'Bold';
    	                                            	}
    	                                            ?>
                                                	<select name="ocpc-posttitlefweight">
                                                        <option value="Normal" <?php if($ocpc_posttitlefweight_dval == 'Normal'){echo "selected";} ?>><?php echo __( 'Normal', PSCCPG_DOMAIN );?></option>
                                                        <option value="Bold" <?php if($ocpc_posttitlefweight_dval == 'Bold'){echo "selected";} ?>><?php echo __( 'Bold', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr valign="top">
                                	<th scope="row">
                                        <input type="checkbox" name="ocpc-postdate" value="yes" <?php if($ocpc_postdate_val == 'yes'){echo "checked";} ?>> <?php echo __( 'Post Date', PSCCPG_DOMAIN );?>
                                        <p class="ocpc-tips"><?php echo __( 'Show the Post Date under the title.', PSCCPG_DOMAIN );?></p>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <div class="post_option" id="post_date_option" style="display: none">
                                            <ul>
                                                <?php
                                                $defultvalue_postdatecolor = $postdatedata['ocpc_postdatecolor'];
                                                if(!empty($defultvalue_postdatecolor))
                                                {
                                                    $postdatecolor_dval = $defultvalue_postdatecolor;
                                                }
                                                else
                                                {
                                                    $postdatecolor_dval = '#607d8b';
                                                }
                                                ?>
                                                <li>
                                                    <label><?php echo __( 'Color', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                	<input type="text" name="ocpc-postdatecolor" class="ocpc_colorpicker" value="<?php echo $postdatecolor_dval; ?>">
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Font Size', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <?php
                                                    $postdatefontsize = $postdatedata['ocpc_postdatefontsize'];
                                                    if($postdatefontsize == '') {
                                                        $postdatefontsize = '14';
                                                    }
                                                    ?>
                                                    <input type="number" name="ocpc-postdatefontsize" placeholder="eg. 14" value="<?php echo $postdatefontsize; ?>">
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Position', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <?php
                                                $defultvalue_postdateposition = $postdatedata['ocpc_postdateposition'];
                                                if(!empty($defultvalue_postdateposition))
                                                {
                                                    $postdateposition_dval = $defultvalue_postdateposition;
                                                }
                                                else
                                                {
                                                    $postdateposition_dval = 'Left';
                                                }
                                                ?>
                                                <li><select name="ocpc-postdateposition" >
                                                        <option value="Center"  <?php if($postdateposition_dval == 'Center'){echo "selected";} ?>><?php echo __( 'Center', PSCCPG_DOMAIN );?></option>
                                                        <option value="Left"  <?php if($postdateposition_dval == 'Left'){echo "selected";} ?>><?php echo __( 'Left', PSCCPG_DOMAIN );?></option>
                                                        <option value="Right"  <?php if($postdateposition_dval == 'Right'){echo "selected";} ?>><?php echo __( 'Right', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                                <li><label><?php echo __( 'Font Weight', PSCCPG_DOMAIN );?></label></li>
                                                <li><select name="ocpc-postdatefweight">
                                                        <option value="Normal"  <?php if($postdatedata['ocpc_postdatefweight'] == 'Normal'){echo "selected";} ?>><?php echo __( 'Normal', PSCCPG_DOMAIN );?></option>
                                                        <option value="Bold"  <?php if($postdatedata['ocpc_postdatefweight'] == 'Bold'){echo "selected";} ?>><?php echo __( 'Bold', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>



                                <tr valign="top">
                                    <th scope="row">
                                        <input type="checkbox" name="ocpc-postcats" value="yes" <?php if($ocpc_postcats_val == 'yes') { echo "checked"; } ?>> <?php echo __( 'Post Category', PSCCPG_DOMAIN );?>
                                        <p class="ocpc-tips"><?php echo __( 'Show the Post Category under the post date.', PSCCPG_DOMAIN );?></p>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <div class="post_option" id="post_cats_option" style="display: none">
                                            <ul>
                                                <?php
                                                $defultvalue_postcatscolor = $postcatsdata['ocpc_postcatscolor'];
                                                if(!empty($defultvalue_postcatscolor))
                                                {
                                                    $postcatscolor_dval = $defultvalue_postcatscolor;
                                                }
                                                else
                                                {
                                                    $postcatscolor_dval = '#607d8b';
                                                }
                                                ?>
                                                <li>
                                                    <label><?php echo __( 'Color', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <input type="text" name="ocpc-postcatscolor" class="ocpc_colorpicker" value="<?php echo $postcatscolor_dval; ?>">
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Font Size', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <?php
                                                    $postcatsfontsize = $postcatsdata['ocpc_postcatsfontsize'];
                                                    if($postcatsfontsize == '') {
                                                        $postcatsfontsize = '14';
                                                    }
                                                    ?>
                                                    <input type="number" name="ocpc-postcatsfontsize" placeholder="eg. 14" value="<?php echo $postcatsfontsize; ?>"></li>
                                                <li>
                                                    <label><?php echo __( 'Position', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <?php
                                                $defultvalue_postcatsposition = $postcatsdata['ocpc_postcatsposition'];
                                                if(!empty($defultvalue_postcatsposition))
                                                {
                                                    $postcatsposition_dval = $defultvalue_postcatsposition;
                                                }
                                                else
                                                {
                                                    $postcatsposition_dval = 'Left';
                                                }
                                                ?>
                                                <li><select name="ocpc-postcatsposition" >
                                                        <option value="Center"  <?php if($postcatsposition_dval == 'Center'){echo "selected";} ?>><?php echo __( 'Center', PSCCPG_DOMAIN );?></option>
                                                        <option value="Left"  <?php if($postcatsposition_dval == 'Left'){echo "selected";} ?>><?php echo __( 'Left', PSCCPG_DOMAIN );?></option>
                                                        <option value="Right"  <?php if($postcatsposition_dval == 'Right'){echo "selected";} ?>><?php echo __( 'Right', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Font Weight', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li><select name="ocpc-postcatsfweight">
                                                        <option value="Normal"  <?php if($postcatsdata['ocpc_postcatsfweight'] == 'Normal'){echo "selected";} ?>><?php echo __( 'Normal', PSCCPG_DOMAIN );?></option>
                                                        <option value="Bold"  <?php if($postcatsdata['ocpc_postcatsfweight'] == 'Bold'){echo "selected";} ?>><?php echo __( 'Bold', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>



                                <tr valign="top">
                                    <th scope="row">
                                        <input type="checkbox" name="ocpc-postauthor" value="yes" <?php if($ocpc_postauthor_val == 'yes'){echo "checked";} ?>> <?php echo __( 'Post Author', PSCCPG_DOMAIN );?>
                                        <p class="ocpc-tips"><?php echo __( 'Show the Post Author.', PSCCPG_DOMAIN );?></p>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <div class="post_option" id="post_author_option" style="display: none">
                                            <ul>
                                                <?php
                                                $defultvalue_postauthorcolor = $postauthordata['ocpc_postauthorcolor'];
                                                if(!empty($defultvalue_postauthorcolor))
                                                {
                                                    $postauthorcolor_dval = $defultvalue_postauthorcolor;
                                                }
                                                else
                                                {
                                                    $postauthorcolor_dval = '#607d8b';
                                                }
                                                ?>
                                                <li>
                                                    <label><?php echo __( 'Color', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                	<input type="text" name="ocpc-postauthorcolor" class="ocpc_colorpicker" value="<?php echo $postauthorcolor_dval; ?>">
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Font Size', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <?php
                                                    $postauthorfontsize = $postauthordata['ocpc_postauthorfontsize'];
                                                    if($postauthorfontsize == '') {
                                                        $postauthorfontsize = '14';
                                                    }
                                                    ?>
                                                    <input type="number" name="ocpc-postauthorfontsize" placeholder="eg. 14" value="<?php echo $postauthorfontsize; ?>">
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Position', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <?php
                                                $defultvalue_postauthorposition = $postauthordata['ocpc_postauthorposition'];
                                                if(!empty($defultvalue_postauthorposition))
                                                {
                                                    $postauthorposition_dval = $defultvalue_postauthorposition;
                                                }
                                                else
                                                {
                                                    $postauthorposition_dval = 'Left';
                                                }
                                                ?>
                                                <li><select name="ocpc-postauthorposition" >
                                                        <option value="Center"  <?php if($postauthorposition_dval == 'Center'){echo "selected";} ?>><?php echo __( 'Center', PSCCPG_DOMAIN );?></option>
                                                        <option value="Left"  <?php if($postauthorposition_dval == 'Left'){echo "selected";} ?>><?php echo __( 'Left', PSCCPG_DOMAIN );?></option>
                                                        <option value="Right"  <?php if($postauthorposition_dval == 'Right'){echo "selected";} ?>><?php echo __( 'Right', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                                <li><label><?php echo __( 'Font Weight', PSCCPG_DOMAIN );?></label></li>
                                                <li><select name="ocpc-postauthorfweight">
                                                        <option value="Normal"  <?php if($postauthordata['ocpc_postauthorfweight'] == 'Normal'){echo "selected";} ?>><?php echo __( 'Normal', PSCCPG_DOMAIN );?></option>
                                                        <option value="Bold"  <?php if($postauthordata['ocpc_postauthorfweight'] == 'Bold'){echo "selected";} ?>><?php echo __( 'Bold', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr valign="top">
                                	<th scope="row">
                                        <input type="checkbox" name="ocpc-postdescription" value="yes" <?php if($ocpc_postdescription_val == 'yes'){echo "checked";} ?>> <?php echo __( 'Post Description', PSCCPG_DOMAIN );?>
                                        <p class="ocpc-tips"><?php echo __( 'Show the Post Description.', PSCCPG_DOMAIN );?></p>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <div class="post_option" id="post_description_option" style="display: none">
                                            <ul>
                                                <li>
                                                    <label><?php echo __( 'Color', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <?php
                                                    $postdescriptioncolor = $postdescriptiondata['ocpc_postdescriptioncolor'];
                                                    if($postdescriptioncolor == '') {
                                                        $postdescriptioncolor = '#607d8b';
                                                    }
                                                    ?>
                                                	<input type="text" name="ocpc-postdescriptioncolor" class="ocpc_colorpicker" value="<?php echo $postdescriptioncolor; ?>">
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Font Size', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <?php
                                                    $postdescriptionfontsize = $postdescriptiondata['ocpc_postdescriptionfontsize'];
                                                    if($postdescriptionfontsize == '') {
                                                        $postdescriptionfontsize = '15';
                                                    }
                                                    ?>
                                                    <input type="number" name="ocpc-postdescriptionfontsize" placeholder="eg. 15" value="<?php echo $postdescriptionfontsize; ?>"></li>
                                                <li>
                                                    <label><?php echo __( 'Position', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <?php
                                                $defultvalue_postdescriptionposition = $postdescriptiondata['ocpc_postdescriptionposition'];
                                                if(!empty($defultvalue_postdescriptionposition))
                                                {
                                                    $postdescriptionposition_dval = $defultvalue_postdescriptionposition;
                                                }
                                                else
                                                {
                                                    $postdescriptionposition_dval = 'Left';
                                                }
                                                ?>
                                                <li><select name="ocpc-postdescriptionposition" >
                                                        <option value="Center"  <?php if($postdescriptionposition_dval == 'Center'){echo "selected";} ?>><?php echo __( 'Center', PSCCPG_DOMAIN );?></option>
                                                        <option value="Left"  <?php if($postdescriptionposition_dval == 'Left'){echo "selected";} ?>><?php echo __( 'Left', PSCCPG_DOMAIN );?></option>
                                                        <option value="Right"  <?php if($postdescriptionposition_dval == 'Right'){echo "selected";} ?>><?php echo __( 'Right', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Font Weight', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <select name="ocpc-postdescriptionfweight">
                                                        <option value="Normal"  <?php if($postdescriptiondata['ocpc_postdescriptionfweight'] == 'Center'){echo "selected";} ?>><?php echo __( 'Normal', PSCCPG_DOMAIN );?></option>
                                                        <option value="Bold"  <?php if($postdescriptiondata['ocpc_postdescriptionfweight'] == 'Center'){echo "selected";} ?>><?php echo __( 'Bold', PSCCPG_DOMAIN );?></option>
                                                    </select>
                                                </li>
                                                <li>
                                                    <label><?php echo __( 'Post Description Length', PSCCPG_DOMAIN );?></label>
                                                </li>
                                                <li>
                                                    <?php
                                                    $postdesclength = get_post_meta( $post->ID, 'ocpc-postdesclength', true );
                                                    if($postdesclength == '') {
                                                        $postdesclength = '15';
                                                    }
                                                    ?>
                                                    <input type="number" name="ocpc-postdesclength" placeholder="eg. 20" value="<?php echo $postdesclength; ?>">
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Read More Text', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <?php
                                        $readmoretext = get_post_meta( $post->ID, 'ocpc-readmoretext', true );
                                        if($readmoretext == '') {
                                            $readmoretext = 'Read More';
                                        }
                                        ?>
                                        <input type="text" name="ocpc-readmoretext" placeholder="Read More" value="<?php echo $readmoretext; ?>">
                                        <p class="ocpc-tips"><?php echo __( 'Add Read More Text', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Read More Text Color', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <?php
                                        if(empty(get_post_meta( $post->ID, 'ocpc-readmoretextcolor', true ))){
                                             $readmorecolor = '#607d8b';
                                        } else {
                                            $readmorecolor = get_post_meta( $post->ID, 'ocpc-readmoretextcolor', true );
                                        }
                                        ?>
                                        <input type="text" name="ocpc-readmoretextcolor" class="ocpc_colorpicker" value="<?php echo $readmorecolor; ?>">
                                        <p class="ocpc-tips"><?php echo __( 'Add Read More Text Color', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">
                                        <label><?php echo __( 'Post Data Transparent Background Color', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <?php
                                        if(empty(get_post_meta( $post->ID, 'ocpc_trbgcolor', true ))){
                                            $ocpc_trbgcolor = 'rgba(0, 0, 0, .3)';
                                        } else {
                                            $ocpc_trbgcolor = get_post_meta( $post->ID, 'ocpc_trbgcolor', true );
                                        }
                                        ?>
                                        <input type="text" class="color-picker" data-alpha="true" data-default-color="<?php echo $ocpc_trbgcolor; ?>" name="ocpc_trbgcolor" value="<?php echo $ocpc_trbgcolor; ?>"/>
                                        <p class="ocpc-tips"><?php echo __( 'Post content transparent background color, only applies when template needs background color', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                                <tr valign="top" class="ocpc_plchldr_tr">
                                    <th scope="row">
                                        <label><?php echo __( 'Upload Placeholder image', PSCCPG_DOMAIN );?></label>
                                    </th>
                                    <td class="forminp forminp-text">
                                        <?php
	                                    	$ocpc_placeholderimage_id = get_post_meta($post->ID, 'ocpc-placeholderimage', true );
	                                    	$ocpc_placeholderimage_array = wp_get_attachment_image_src( $ocpc_placeholderimage_id, 'full' );
	                                    	$ocpc_placeholderimage = $ocpc_placeholderimage_array[0];
	                                    ?>
                                        <?php  
                                            echo $this->PSCCPG_image_uploader_field( 'ocpc-image',get_post_meta($post->ID, 'ocpc_image', true ));
                                        ?>
                                        <?php if(!empty(get_post_meta($post->ID, 'ocpc-placeholderimage', true ))){ ?>
                                        <img src="<?php echo $ocpc_placeholderimage; ?>" class="ocpc_plchld_prvw_image" width="50px" height="50px">
                                        <a href="#" class="ocpc_remove_image_button">x</a>
                                    	<?php } ?>
                                        <input type="hidden" name="ocpc-placeholderimage" class="placeholderimage_hidden_img" value="<?php echo $ocpc_placeholderimage_id; ?>">
                                        <p class="ocpc-tips"><?php echo __( "Upload a featured image placeholder. Otherwise, plugin's default image will be used", PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </fieldset>
                </div>
               
                <div id="tab-template" class="tab-content">
                    <fieldset>
                        <table class="form-table">
                            <tr valign="top" class="ocpc-distype">
                                <th scope="row">
                                    <label><?php echo __( 'Display Type', PSCCPG_DOMAIN );?></label>
                                </th>
                                <td class="forminp forminp-text">
                                    <input type="radio" name="ocpc_option" id="radiogallery" value="gallery" class="ocpc_option with-gap" <?php if(get_post_meta( $post->ID, 'ocpc-option', true ) == "gallery" || empty(get_post_meta( $post->ID, 'ocpc-option', true ))) { echo 'checked'; } ?>>
                                    <label>Gallery</label>
                                    <input type="radio" name="ocpc_option" id="radioslider" value="carousel" class="ocpc_option" <?php if(get_post_meta( $post->ID, 'ocpc-option', true ) == "carousel" ) { echo 'checked'; } ?>>
                                    <label>Carousel</label>
                                    <input type="radio" name="ocpc_option" id="radiomasonry" value="masonry" class="ocpc_option" <?php if(get_post_meta( $post->ID, 'ocpc-option', true ) == "masonry" ) { echo 'checked'; } ?>>
                                    <label>Masonry</label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label><?php echo __( 'Template', PSCCPG_DOMAIN );?></label>
                                </th>
                                <td class="forminp forminp-text">
                                    <select name="sel_template">
                                        <?php
                                        $ocpc_template = get_post_meta( $post->ID, 'ocpc-template', true);

                                        for ($i = 1; $i <= 33; $i++) {
                                        ?>
                                            <option value="ocpc-template-<?php echo $i; ?>" <?php if($ocpc_template == "ocpc-template-".$i) { echo "selected"; } ?> <?php if($i > 10 && $i <= 33) { echo 'disabled'; } ?>>Template <?php echo $i; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <label class="ocpc_pro_link ocpc_pro_link_fw">Disabled templates are available only in pro version <a href="https://www.xeeshop.com/product/post-slider-carousel-custom-post-grids-pro/" target="_blank">link</a></label>
                                </td>
                            </tr>
                        </table>
                        <table class="form-table">
                            <tr valign="top" id="ocpc-showpagination">
                                <th scope="row">
                                    <label><?php echo __( 'Show Pagination', PSCCPG_DOMAIN );?></label>
                                </th>
                                <td class="forminp forminp-text">
                                    <?php $pagenat = get_post_meta( $post->ID, 'ocpc-showpagination', true ); ?>
                                    <input type="checkbox" name="ocpc-showpagination" <?php if($pagenat == "on") { echo "checked"; } ?>>
                                </td>
                            </tr>
                            <tr valign="top" id="ocpc-totalposts" style="display: none;">
                                <th scope="row">
                                    <label><?php echo __( 'Total Posts', PSCCPG_DOMAIN );?></label>
                                </th>
                                <td class="forminp forminp-text">
                                    <?php
                                    $totalposts = get_post_meta( $post->ID, 'ocpc-totalposts', true );
                                    if($totalposts == '') {
                                        $totalposts = '10';
                                    }
                                    ?>
                                    <input type="number" name="ocpc-totalposts" placeholder="eg.10" value="<?php echo $totalposts; ?>">
                                </td>
                            </tr>
                            <tr valign="top" id="ocpc-perpage">
                                <th scope="row">
                                    <label><?php echo __( 'Posts Per Page', PSCCPG_DOMAIN );?></label>
                                </th>
                                <td class="forminp forminp-text">
                                    <input type="number" name="ocpc-perpage" placeholder="eg.6" value="10" disabled>
                                    <label class="ocpc_pro_link">Only available in pro version <a href="https://www.xeeshop.com/product/post-slider-carousel-custom-post-grids-pro/" target="_blank">link</a></label>
                                    
                                </td>
                            </tr>
                            <tr valign="top" id="ocpc-pagilayout">
                                <th scope="row">
                                    <label><?php echo __( 'Pagination Number Layout', PSCCPG_DOMAIN );?></label>
                                </th>
                                <td class="forminp forminp-text">
                                    <?php
                                    $pagilayout = get_post_meta( $post->ID, 'ocpc-pagilayout', true );
                                    if($pagilayout == '') {
                                        $pagilayout = 'circle-layout';
                                    }
                                    ?>
                                    <select name="ocpc-pagilayout">
                                        <option value="circle-layout" <?php if($pagilayout == 'circle-layout') { echo 'selected'; } ?>>Circle Layout</option>
                                        <option value="boxed-layout" <?php if($pagilayout == 'boxed-layout') { echo 'selected'; } ?>>Boxed Layout</option>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top" id="ocpc-pagitxtclr">
                                <th scope="row">
                                    <label><?php echo __( 'Pagination Text Color', PSCCPG_DOMAIN );?></label>
                                </th>
                                <td class="forminp forminp-text">
                                    <?php
                                    $pagitxtclr = get_post_meta( $post->ID, 'ocpc-pagitxtclr', true );
                                    if($pagitxtclr == '') {
                                        $pagitxtclr = '#FFF';
                                    }
                                    ?>
                                    <input type="text" name="ocpc-pagitxtclr" class="ocpc_colorpicker" value="<?php echo $pagitxtclr; ?>">
                                </td>
                            </tr>
                            <tr valign="top" id="ocpc-pagibgclr">
                                <th scope="row">
                                    <label><?php echo __( 'Pagination Background Color', PSCCPG_DOMAIN );?></label>
                                </th>
                                <td class="forminp forminp-text">
                                    <?php
                                    $pagibgclr = get_post_meta( $post->ID, 'ocpc-pagibgclr', true );
                                    if($pagibgclr == '') {
                                        $pagibgclr = '#03A9F4';
                                    }
                                    ?>
                                    <input type="text" name="ocpc-pagibgclr" class="ocpc_colorpicker" value="<?php echo $pagibgclr; ?>">
                                </td>
                            </tr>
                            <tr valign="top" class="space_betwwen">
                                <th scope="row">Padding in Block (in px)</th>
                                <td class="forminp forminp-text">
                                    <input type="number" name="ocpc_gl_space_img" class="insta_space_img" value="<?php if(!empty(get_post_meta( $post->ID, 'ocpc_gl_space_img', true ))){ echo get_post_meta( $post->ID, 'ocpc_gl_space_img', true ); }else{ echo "5"; }?>">
                                </td>
                            </tr>
                        </table>
                        <div class="gallery">
                            <table class="form-table">
                                <tr valign="top">
                                    <td class="forminp forminp-text"><h3>Gallery Setting</h3></td>
                                </tr>
                                <tr valign="top" class="columns"> 
                                    <td class="forminp forminp-text">Columns</td>
                                    <td class="forminp forminp-text">
                                        <input type="number" name="ocpc_gl_clm" value="<?php if(!empty(get_post_meta( $post->ID, 'ocpc_gl_clm', true ))){ echo get_post_meta( $post->ID, 'ocpc_gl_clm', true ); }else{ echo "3"; }?>">
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="carousel" style="display: none;">
                            <table class="form-table">
                                <tr valign="top">
                                    <td class="forminp forminp-text"><h3>Carousel Setting</h3></td>
                                </tr>
                                <tr valign="top">
                                    <td class="forminp forminp-text">
                                        <label><?php echo __( 'Slides per row', PSCCPG_DOMAIN );?></label>
                                    </td>
                                    <td class="forminp forminp-text">
                                        <?php
                                        $ocpc_perrow = get_post_meta( $post->ID, 'ocpc-perrow', true );
                                        if($ocpc_perrow == '') {
                                            $ocpc_perrow = '3';
                                        }
                                        ?>
                                        <input type="number" name="ocpc-perrow" placeholder="eg.3" min='3' value="<?php echo $ocpc_perrow; ?>">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td class="forminp forminp-text">
                                        <label><?php echo __( 'Auto Play', PSCCPG_DOMAIN );?></label>
                                    </td>
                                    <td class="forminp forminp-text">
                                        <select name="ocpc-autoplay">
                                            <option value="true"  <?php if(get_post_meta( $post->ID, 'ocpc-autoplay', true ) == 'true'){echo "selected";} ?>><?php echo __( 'Yes', PSCCPG_DOMAIN );?></option>
                                            <option value="false"  <?php if(get_post_meta( $post->ID, 'ocpc-autoplay', true ) == 'false'){echo "selected";} ?>><?php echo __( 'No', PSCCPG_DOMAIN );?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td class="forminp forminp-text">
                                        <label><?php echo __( 'Auto Play Timeout', PSCCPG_DOMAIN );?></label>
                                    </td>
                                    <td class="forminp forminp-text">
                                        <?php
                                        $autoplaytimeout = get_post_meta( $post->ID, 'ocpc-autoplaytimeout', true );
                                        if($autoplaytimeout == '') {
                                            $autoplaytimeout = '1000';
                                        }
                                        ?>
                                        <input type="number" name="ocpc-autoplaytimeout" placeholder="eg.1000" value="<?php echo $autoplaytimeout; ?>">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td class="forminp forminp-text">
                                        <label><?php echo __( 'Auto Play Hover Pause', PSCCPG_DOMAIN );?></label>
                                    </td>
                                    <td class="forminp forminp-text">
                                        <select name="ocpc-autoplayhoverpause" disabled="">
                                            <option value="true" selected=""><?php echo __( 'Yes', PSCCPG_DOMAIN );?></option>
                                            <option value="false"><?php echo __( 'No', PSCCPG_DOMAIN );?></option>
                                        </select>
                                        <label class="ocpc_pro_link ocpc_pro_link_fw">Only available in pro version <a href="https://www.xeeshop.com/product/post-slider-carousel-custom-post-grids-pro/" target="_blank">link</a></label>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td class="forminp forminp-text">
                                        <label><?php echo __( 'Navigation arrows', PSCCPG_DOMAIN );?></label>
                                    </td>
                                    <td class="forminp forminp-text">
                                        <select name="ocpc-sliderarrow">
                                            <option value="true"  <?php if(get_post_meta( $post->ID, 'ocpc-sliderarrow', true ) == 'true'){echo "selected";} ?>><?php echo __( 'Yes', PSCCPG_DOMAIN );?></option>
                                            <option value="false"  <?php if(get_post_meta( $post->ID, 'ocpc-sliderarrow', true ) == 'false'){echo "selected";} ?>><?php echo __( 'No', PSCCPG_DOMAIN );?></option>
                                        </select>
                                    </td>
                                </tr>
                               	<tr valign="top">
                                    <td class="forminp forminp-text">
                                        <label><?php echo __( 'Mobile Per Row', PSCCPG_DOMAIN );?></label>
                                    </td>
                                    <td class="forminp forminp-text">
                                        <?php
                                        $mobileperrow = get_post_meta( $post->ID, 'ocpc-mobileperrow', true );
                                        if($mobileperrow == '') {
                                            $mobileperrow = '1';
                                        }
                                        ?>
                                        <input type="number" name="ocpc-mobileperrow" placeholder="eg.1" min='1' value="<?php echo $mobileperrow; ?>">
                                        <p class="ocpc-tips"><?php echo __( 'Per Row must be greater than or equal to 1', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td class="forminp forminp-text">
                                        <label><?php echo __( 'Tablet Per Row', PSCCPG_DOMAIN );?></label>
                                    </td>
                                    <td class="forminp forminp-text">
                                        <?php
                                        $tabletperrow = get_post_meta( $post->ID, 'ocpc-tabletperrow', true );
                                        if($tabletperrow == '') {
                                            $tabletperrow = '2';
                                        }
                                        ?>
                                        <input type="number" name="ocpc-tabletperrow" placeholder="eg.2" min='1' value="<?php echo $tabletperrow; ?>">
                                        <p class="ocpc-tips"><?php echo __( 'Per Row must be greater than or equal to 1', PSCCPG_DOMAIN );?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="masonry">
                            <table  class="form-table">
                                <tr valign="top">
                                    <td class="forminp forminp-text"><h3>Masonry Setting</h3></td>
                                </tr>
                                
                                <tr valign="top"> 
                                    <td class="forminp forminp-text">Columns</td>
                                    <td class="forminp forminp-text">
                                        <input type="number" name="ocpc_ms_clm" value="<?php if(!empty(get_post_meta( $post->ID, 'ocpc_ms_clm', true ))){ echo get_post_meta( $post->ID, 'ocpc_ms_clm', true ); }else{ echo "3"; }?>">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </fieldset>
                </div>
            </div>
            <?php
        }


        //Upload Background Image function
        function PSCCPG_image_uploader_field( $name, $value = '') {
            $image = ' button">Upload image';
            $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
            $display = 'none'; // display state ot the "Remove image" button
         
            if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
         
                $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
                $display = 'inline-block';
         
            } 
         
            return '
            <div class="ocpc_upload_image_main_div">
                <a href="#" class="ocpc_upload_image_button' . $image . '</a>
                <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
            </div>';
        }


        //Modify Post columns
        function PSCCPG_add_new_columns($new_columns){
            $new_columns = array();
            $new_columns['cb']   = '<input type="checkbox" />';
            $new_columns['title']   = esc_html__('Name', PSCCPG_DOMAIN);
            $new_columns['shortcode']   = esc_html__('Shortcode', PSCCPG_DOMAIN);
            $new_columns['date']   = esc_html__('Created at', PSCCPG_DOMAIN);
            return $new_columns;
        }


        //Add shortcode column
        function PSCCPG_manage_custom_columns( $column_name, $post_id ) {

            switch($column_name){
                case 'shortcode': ?>
                    <input type="text" id="ocpc-selectdata_<?php echo $post_id;?>" value="[ocpc-post-carousel id=<?php echo $post_id;?>]" size="30" onclick="ocpc_select_data(this.id)" readonly>
                <?php
                break;
                default:
                break;

            }
        }


        //Load media function
        function PSCCPG_load_media_files() {
            wp_enqueue_media();
        }


        function PSCCPG_ocpc_postcats_get_cats_ajax_callback() {
            $return = array();

            $ocpc_categories = get_categories( array( 'orderby' => 'name', 'order'   => 'ASC' ) );

            if( !empty($ocpc_categories) ) {
                foreach ($ocpc_categories as $key => $category) {
                    $category->term_id;
                    $title = ( mb_strlen( $category->name ) > 50 ) ? mb_substr( $category->name, 0, 49 ) . '...' : $category->name;
                    $return[] = array( $category->term_id, $title );
                }
            }

            echo json_encode( $return );
            die;
        }


        function PSCCPG_ocpc_posttags_get_tags_ajax_callback() {
            $return = array();

            $ocpc_posttags = get_tags(array('hide_empty' => false));

            if( !empty($ocpc_posttags) ) {
                foreach ($ocpc_posttags as $key => $tag) {
                    $tag->term_id;
                    $title = ( mb_strlen( $tag->name ) > 50 ) ? mb_substr( $tag->name, 0, 49 ) . '...' : $tag->name;
                    $return[] = array( $tag->term_id, $title );
                }
            }

            echo json_encode( $return );
            die;
        }


        function PSCCPG_ocpc_prodcats_get_cats_ajax_callback() {
            $return = array();

            $product_categories = get_terms( 'product_cat', $cat_args );

            if( !empty($product_categories) ) {
                foreach ($product_categories as $key => $category) {
                    $category->term_id;
                    $title = ( mb_strlen( $category->name ) > 50 ) ? mb_substr( $category->name, 0, 49 ) . '...' : $category->name;
                    $return[] = array( $category->term_id, $title );
                }
            }

            echo json_encode( $return );
            die;
        }


        function PSCCPG_ocpc_prodtags_get_tags_ajax_callback() {
            $return = array();

            $ocpc_prodtags = get_terms( 'product_tag' );

            if( !empty($ocpc_prodtags) ) {
                foreach ($ocpc_prodtags as $key => $tag) {
                    $tag->term_id;
                    $title = ( mb_strlen( $tag->name ) > 50 ) ? mb_substr( $tag->name, 0, 49 ) . '...' : $tag->name;
                    $return[] = array( $tag->term_id, $title );
                }
            }

            echo json_encode( $return );
            die;
        }

        function PSCCPG_support_and_rating_notice() {
            $screen = get_current_screen();
            if( 'ocpostcarousel' == $screen->post_type
                && 'edit' == $screen->base ) {
                ?>
                <div class="ocpc_ratesup_notice_main">
                    <div class="ocpc_rateus_notice">
                        <div class="ocpc_rtusnoti_left">
                            <h3>Rate Us</h3>
                            <label>If you like our plugin, </label>
                            <a target="_blank" href="https://wordpress.org/support/plugin/post-slider-by-oc/reviews/#new-post">
                                <label>Please vote us</label>
                            </a>
                            <label>, so we can contribute more features for you.</label>
                        </div>
                        <div class="ocpc_rtusnoti_right">
                            <img src="<?php echo PSCCPG_PLUGIN_DIR; ?>/assets/images/review.png" class="ocpc_review_icon">
                        </div>
                    </div>
                    <div class="ocpc_support_notice">
                        <div class="ocpc_rtusnoti_left">
                            <h3>Having Issues?</h3>
                            <label>You can contact us at</label>
                            <a target="_blank" href="https://www.xeeshop.com/support-us/?utm_source=aj_plugin&utm_medium=plugin_support&utm_campaign=aj_support&utm_content=aj_wordpress">
                                <label>Our Support Forum</label>
                            </a>
                        </div>
                        <div class="ocpc_rtusnoti_right">
                            <img src="<?php echo PSCCPG_PLUGIN_DIR; ?>/assets/images/support.png" class="ocpc_review_icon">
                        </div>
                    </div>
                </div>
                <div class="ocpc_donate_main">
                   <img src="<?php echo PSCCPG_PLUGIN_DIR; ?>/assets/images/coffee.svg">
                   <h3>Buy me a Coffee !</h3>
                   <p>If you like this plugin, buy me a coffee and help support this plugin !</p>
                   <div class="ocpc_donate_form">
                      <a class="button button-primary ocpc_donate_btn" href="https://www.paypal.com/paypalme/shayona163/" data-link="https://www.paypal.com/paypalme/shayona163/" target="_blank">Buy me a coffee !</a>
                   </div>
                </div>
                <?php
            }
        }


        function init() {
           add_action('init', array($this, 'PSCCPG_create_menu'));
           add_action('add_meta_boxes', array($this, 'PSCCPG_add_meta_box'));
           add_filter('manage_ocpostcarousel_posts_columns', array($this,'PSCCPG_add_new_columns'));
           add_action('manage_ocpostcarousel_posts_custom_column', array($this, 'PSCCPG_manage_custom_columns'), 10, 2);
           add_action('admin_enqueue_scripts', array($this,'PSCCPG_load_media_files'));

           add_action( 'wp_ajax_nopriv_PSCCPG_ocpc_postcats_get_cats',array($this, 'PSCCPG_ocpc_postcats_get_cats_ajax_callback') );
           add_action( 'wp_ajax_PSCCPG_ocpc_postcats_get_cats', array($this, 'PSCCPG_ocpc_postcats_get_cats_ajax_callback') );

           add_action( 'wp_ajax_nopriv_PSCCPG_ocpc_posttags_get_tags',array($this, 'PSCCPG_ocpc_posttags_get_tags_ajax_callback') );
           add_action( 'wp_ajax_PSCCPG_ocpc_posttags_get_tags', array($this, 'PSCCPG_ocpc_posttags_get_tags_ajax_callback') );

           add_action( 'wp_ajax_nopriv_PSCCPG_ocpc_prodcats_get_cats',array($this, 'PSCCPG_ocpc_prodcats_get_cats_ajax_callback') );
           add_action( 'wp_ajax_PSCCPG_ocpc_prodcats_get_cats', array($this, 'PSCCPG_ocpc_prodcats_get_cats_ajax_callback') );

           add_action( 'wp_ajax_nopriv_PSCCPG_ocpc_prodtags_get_tags',array($this, 'PSCCPG_ocpc_prodtags_get_tags_ajax_callback') );
           add_action( 'wp_ajax_PSCCPG_ocpc_prodtags_get_tags', array($this, 'PSCCPG_ocpc_prodtags_get_tags_ajax_callback') );

           add_action( 'admin_notices', array($this, 'PSCCPG_support_and_rating_notice' ));
        }

        public static function instance() {
          if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->init();
          }
          return self::$instance;
        }

    }
    PSCCPG_menu::instance();
}