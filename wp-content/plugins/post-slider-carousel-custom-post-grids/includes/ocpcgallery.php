<?php
$width = 100 / $ocpc_gl_clm;
if($posttype == 'attachment'){ 
    $ocpc_attachments = get_posts($ocpc_args_img);
    foreach ($ocpc_attachments as $ocpc_attachment) {
        $post_author_id = $ocpc_attachment->post_author;
        $display_name = get_the_author_meta( 'display_name' , $post_author_id ); 
        ?>
        <div class="ocpc-topbox" style="width: <?php echo $width?>%;padding: <?php echo $ocpc_gl_space_img?>px;">
            <?php 
                $datacontain = $ocpc_attachment->post_content;
                echo '<div class="ocpcmain_class">';
                    if($post_template == "tem3") {
                        echo '<div class="ocpc_ttl_dt_div">';
                            if($ocpc_posttitle == 'yes') { 
                                ?>
                                    <a href="<?php echo get_permalink($ocpc_attachment->ID); ?>" id="ocpc-postlink">
                                        <h1 class="ocpc-title"><?php echo esc_html( get_the_title($ocpc_attachment->ID) ); ?></h1>
                                    </a>
                                <?php 
                            }

                            //Dispaly Post Date is Enable or not
                            if($ocpc_postdate == 'yes') { 
                                ?>
                                    <a href="<?php echo get_permalink($ocpc_attachment->ID); ?>" id="ocpc-postlink">
                                        <span class="ocpc-date"><?php echo esc_html( get_the_date('j F, Y', $ocpc_attachment->ID)); ?></span>
                                    </a>
                                <?php 
                            }

                            if($ocpc_postauthor == 'yes') { 
                                ?>
                                <span class="ocpc-author"><?php echo $display_name; ?></span>
                                <?php 
                            }
                        echo '</div>';
                    }



                    //Post Image Settings
                    echo '<div class="ocpcmain_image_class">';
                        echo '<div class="ocpc_image_class">';
                            $ocpc_postimgurl = wp_get_attachment_image_src($ocpc_attachment->ID);
                            if (!empty($ocpc_postimgurl)) { 
                                ?>
                                <a href="<?php echo get_permalink($ocpc_attachment->ID); ?>" id="ocpc-postlink">
                                    <img src="<?php echo esc_url($ocpc_postimgurl[0]);?>" class="ocpc-image" />
                                </a>
                                <?php 
                            } else if(!empty($placeholderimage)) { 
                                ?>
                                <a href="<?php echo get_permalink($ocpc_attachment->ID); ?>" id="ocpc-postlink">
                                    <img src="<?php echo esc_url($placeholderimage);?>" class="ocpc-image" />
                                </a>
                                <?php 
                            } else { 
                                ?>
                                <a href="<?php echo get_permalink($ocpc_attachment->ID); ?>" id="ocpc-postlink">
                                    <img src="<?php echo esc_url(PSCCPG_PLUGIN_DIR .'/images/placeholder.jpg');?>" class="ocpc-image" />
                                </a>
                                <?php 
                            } 
                        echo '</div>';
                    echo '</div>';
        

                    echo '<div class="ocpc_discripton_class">';
                        echo '<div class="ocpc_inner_discripton_class">';
                            if($post_template != "tem3") {
                                //Dispaly Post Title is Enable or not
                                if($ocpc_posttitle == 'yes') { 
                                    ?>
                                    <a href="<?php echo get_permalink($ocpc_attachment->ID); ?>" id="ocpc-postlink">
                                        <h1 class="ocpc-title"><?php echo esc_html( get_the_title($ocpc_attachment->ID) ); ?></h1>
                                    </a>
                                    <?php 
                                }


                                //Dispaly Post Date is Enable or not
                                if($ocpc_postdate == 'yes') { 
                                    ?>
                                    <a href="<?php echo get_permalink($ocpc_attachment->ID); ?>" id="ocpc-postlink">
                                        <span class="ocpc-date"><?php echo esc_html( get_the_date('j F, Y', $ocpc_attachment->ID)); ?></span>
                                    </a>
                                    <?php
                                }

                                if($ocpc_postauthor == 'yes') { 
                                    ?>
                                    <span class="ocpc-author"><?php echo $display_name; ?></span>
                                    <?php 
                                }
                            }

                            //Dispaly Post Description is Enable or not
                            if($ocpc_postdescription == 'yes') { 
                                ?>
                                <div class="ocpc-content">
                                    <?php echo $this->PSCCPG_limited_content($postdesclength,$readmoretext,$datacontain);?>
                                </div>
                                <?php 
                            } 
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            ?>
        </div>
        <?php 
    }
}else {
    $ocpc_postquery = new WP_Query($ocpc_args);
    // echo "<pre>";
    // print_r($ocpc_postquery);
    // echo "</pre>";

    if($ocpc_postquery->have_posts() ) {
        while($ocpc_postquery->have_posts() ) {
            $ocpc_postquery->the_post(); 
            $post_id = get_the_ID();
            $post_author_id = get_post_field( 'post_author', $post_id );
            $display_name = get_the_author_meta( 'display_name' , $post_author_id ); 
            //echo $display_name;
            ?>
            <div class="ocpc-topbox" style="width: <?php echo $width?>%;padding: <?php echo $ocpc_gl_space_img?>px;">

                <?php 
                    $datacontain = get_the_excerpt();
                    //Post Image Settings
                    echo '<div class="ocpcmain_class">';
                        if($post_template == "tem3") {
                            echo '<div class="ocpc_ttl_dt_div">';
                                if($ocpc_posttitle == 'yes') { 
                                    ?>
                                    <a href="<?php echo get_permalink(); ?>" id="ocpc-postlink">
                                        <h1 class="ocpc-title"><?php echo esc_html( get_the_title() ); ?></h1>
                                    </a>
                                    <?php 
                                }

                                //Dispaly Post Date is Enable or not
                                if($ocpc_postdate == 'yes') { 
                                    ?>
                                    <a href="<?php echo get_permalink(); ?>" id="ocpc-postlink">
                                        <span class="ocpc-date"><?php echo esc_html( get_the_date('j F, Y', get_the_ID())); ?></span>
                                    </a>
                                    <?php 
                                }

                                if($ocpc_postauthor == 'yes') { 
                                    ?>
                                    <span class="ocpc-author"><?php echo $display_name; ?></span>
                                    <?php 
                                }
                            echo '</div>';
                        }
                         
                        echo '<div class="ocpcmain_image_class">';
                            echo '<div class="ocpc_image_class">';
                                $ocpc_postimgurl = wp_get_attachment_url(get_post_thumbnail_id());
                                if (!empty($ocpc_postimgurl)) { 
                                    ?>
                                    <a href="<?php echo get_permalink(); ?>" id="ocpc-postlink">
                                        <?php echo get_the_post_thumbnail( get_the_ID(), 'full', array( 'class' => 'ocpc-image' ) );  ?>
                                    </a>
                                    <?php 
                                } else if(!empty($placeholderimage)) { 
                                    ?>
                                    <a href="<?php echo get_permalink(); ?>" id="ocpc-postlink">
                                        <img src="<?php echo esc_url($placeholderimage);?>" class="ocpc-image" />
                                    </a>
                                    <?php 
                                } else { 
                                    ?>
                                    <a href="<?php echo get_permalink(); ?>" id="ocpc-postlink">
                                        <img src="<?php echo esc_url(PSCCPG_PLUGIN_DIR .'/images/placeholder.jpg');?>" class="ocpc-image" />
                                    </a>
                                    <?php 
                                } 
                            echo '</div>';
                        echo '</div>';



                        echo '<div class="ocpc_discripton_class">';
                            echo '<div class="ocpc_inner_discripton_class">';
                                if($post_template != "tem3") {
                                    //Dispaly Post Title is Enable or not
                                    if($ocpc_posttitle == 'yes') { 
                                        ?>
                                        <a href="<?php echo get_permalink(); ?>" id="ocpc-postlink">
                                            <h1 class="ocpc-title"><?php echo esc_html( get_the_title() ); ?></h1>
                                        </a>
                                        <?php 
                                    }

                                    //Dispaly Post Date is Enable or not
                                    if($ocpc_postdate == 'yes') { 
                                        ?>
                                        <a href="<?php echo get_permalink(); ?>" id="ocpc-postlink">
                                            <span class="ocpc-date"><?php echo esc_html( get_the_date('j F, Y', get_the_ID())); ?></span>
                                        </a>
                                        <?php 
                                    }

                                    if($ocpc_postauthor == 'yes') { 
                                        ?>
                                        <span class="ocpc-author"><?php echo $display_name; ?></span>
                                        <?php 
                                    }
                                }

                                //Dispaly Post Description is Enable or not
                                if($ocpc_postdescription == 'yes') { 
                                    ?>
                                    <div class="ocpc-content">
                                        <?php echo $this->PSCCPG_limited_content($postdesclength,$readmoretext,$datacontain);?>
                                    </div>
                                    <?php 
                                } 
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                ?>
            </div>
            <?php
        }
        
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
                    'end_size'     => 2,
                    'mid_size'     => 1,
                    'prev_next'    => true,
                    'prev_text'    => sprintf( '<i></i> %1$s', __( '<<', 'text-domain' ) ),
                    'next_text'    => sprintf( '%1$s <i></i>', __( '>>', 'text-domain' ) ),
                    'add_args'     => false,
                    'add_fragment' => '',
                ) );
            ?>
        </div>
        <?php
        wp_reset_postdata();
    }
}