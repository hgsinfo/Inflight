<?php
/*Masonry Template 9*/


$width = 100 / $ocpc_ms_clm;
echo '<div class="ocpc_insta_masonry_grid">';

    if($posttype == 'attachment') {
        $ocpc_postquery = new WP_Query($ocpc_args_img);
    } else {
        $ocpc_postquery = new WP_Query($ocpc_args);
    }

    if($ocpc_postquery->have_posts() ) {
        while($ocpc_postquery->have_posts() ) {
            $ocpc_postquery->the_post();
            $post_id = get_the_ID();
            $post_author_id = get_post_field( 'post_author', $post_id );
            $display_name = get_the_author_meta( 'display_name' , $post_author_id );
            $datacontain = get_the_excerpt();
            $post_link = get_permalink();
            $post_title = esc_html( get_the_title() );
            $post_desc = $this->PSCCPG_limited_content($postdesclength, $readmoretext, $datacontain);
            $post_date = esc_html( get_the_date('j F, Y', $post_id));
            $post_cats = '';
            $ocpc_post_image_url = '';

            if($posttype == 'attachment') {
                $ocpc_postimgurl = wp_get_attachment_image_src($post_id, $ocpc_image_size);
                if (!empty($ocpc_postimgurl)) {
                    $ocpc_post_image_url = esc_url($ocpc_postimgurl[0]);
                } else if(!empty($placeholderimage)) {
                    $ocpc_postimgurl_placeholder_array = wp_get_attachment_image_src($placeholderimage, $ocpc_image_size);
                    $ocpc_post_image_url = esc_url($ocpc_postimgurl_placeholder_array[0]);
                } else {
                    $ocpc_default_placeholder_array = wp_get_attachment_image_src($ocpc_default_placeholder, $ocpc_image_size);
                    $ocpc_post_image_url = esc_url($ocpc_default_placeholder_array[0]);
                }
            } else {
                $ocpc_postimgurl = get_post_thumbnail_id();
                if (!empty($ocpc_postimgurl)) {
                    $ocpc_postimgurl = wp_get_attachment_image_src($ocpc_postimgurl, $ocpc_image_size);
                    $ocpc_post_image_url = esc_url($ocpc_postimgurl[0]);
                } else if(!empty($placeholderimage)) {
                    $ocpc_postimgurl_placeholder_array = wp_get_attachment_image_src($placeholderimage, $ocpc_image_size);
                    $ocpc_post_image_url = esc_url($ocpc_postimgurl_placeholder_array[0]);
                } else {
                    $ocpc_default_placeholder_array = wp_get_attachment_image_src($ocpc_default_placeholder, $ocpc_image_size);
                    $ocpc_post_image_url = esc_url($ocpc_default_placeholder_array[0]);
                }
            }


            if($posttype == 'post') {
                $post_categories = wp_get_post_categories( $post_id );
                if(!empty($post_categories)) {
                    $post_cats_arr = array();
                    foreach ($post_categories as $cat_id) {
                        $cat_term_name = get_cat_name( $cat_id );
                        $category_link = get_category_link( $cat_id );

                        $post_cats_arr[] = '<a href="'.$category_link.'">'.$cat_term_name.'</a>';
                    }

                    $post_cats = implode(', ', $post_cats_arr);
                }
            } elseif($posttype == 'product') {
                $prod_terms_ids = wp_get_post_terms($post_id,'product_cat',array('fields'=>'ids'));

                if(!empty($prod_terms_ids)) {
                    $post_cats_arr = array();
                    foreach($prod_terms_ids as $prod_term_id) {
                        $prod_term = get_term_by( 'id', $prod_term_id, 'product_cat' );
                        $prod_term_link = get_term_link ($prod_term_id, 'product_cat');
                        $prod_term_name = $prod_term->name; 
                        $post_cats_arr[] = '<a href="'.$prod_term_link.'">'.$prod_term_name.'</a>';
                    }
                    
                    $post_cats = implode(', ', $post_cats_arr);
                }
            }
            ?>
            <div class="ocpc-topbox" style="width:<?php echo $width?>%; padding: <?php echo $ocpc_gl_space_img?>px;">
                <div class="ocpc_tmplt9_msnry_main">
                    <div class="ocpc_tmplt9_msnry_img_div">
                        <div class="ocpc_tmplt9_msnry_img">
                            <a target="_self" href="<?php echo $post_link; ?>">
                                <img alt="" src="<?php echo $ocpc_post_image_url; ?>">
                            </a>
                        </div>
                    </div>
                    <div class="ocpc_tmplt9_msnry_cont_main">
                        <?php
                        if($ocpc_posttitle == 'yes') {
                        ?>
                            <div class="ocpc_tmplt9_msnry_cont_title">
                                <a target="_blank" href="<?php echo $post_link; ?>"><?php echo $post_title; ?></a>
                            </div>
                        <?php
                        }
                        ?>

                        <?php
                        if($ocpc_postdate == 'yes') { 
                        ?>
                            <div class="ocpc_tmplt9_msnry_cont_date">
                                <span><?php echo $post_date; ?></span>
                            </div>
                        <?php
                        }
                        ?>

                        <?php
                        if($ocpc_postcats == 'yes' && $post_cats != '') { 
                        ?>
                            <div class="ocpc_tmplt9_msnry_cont_cats">
                                <span><?php echo $cat_icon.$post_cats; ?></span>
                            </div>
                        <?php
                        }
                        ?>

                        <?php
                        if($ocpc_postauthor == 'yes') { 
                        ?>
                            <div class="ocpc_tmplt9_msnry_cont_author">
                                <span><?php echo $display_name; ?></span>
                            </div>
                        <?php
                        }
                        ?>

                        <?php
                        if($ocpc_postdescription == 'yes' && $datacontain != '') {
                        ?>
                        <div class="ocpc_tmplt9_msnry_cont_dec">
                            <?php echo $post_desc; ?>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }

        if($showpagination == 'on') {
            ?>
            <div class="ocpost_page pagination">
                <?php 
                echo paginate_links( array(
                    'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                    'total'        => $ocpc_postquery->max_num_pages,
                    'current'      => max( 1, get_query_var( 'paged' ) ),
                    'format'       => '?paged=%#%',
                    'show_all'     => false,
                    'type'         => 'plain',
                    'end_size'     => 3,
                    'mid_size'     => 1,
                    'prev_next'    => true,
                    'prev_text'    => sprintf( '<i></i> %1$s', __( 'Previous', 'text-domain' ) ),
                    'next_text'    => sprintf( '%1$s <i></i>', __( 'Next', 'text-domain' ) ),
                    'add_args'     => false,
                    'add_fragment' => '',
                ) );
                ?>
            </div>
            <?php
        }
        wp_reset_postdata();
    }
    
echo '</div>';