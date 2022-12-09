<?php

if (!defined('ABSPATH'))
exit;

if (!class_exists('PSCCPG_shortcode')) {

    class PSCCPG_shortcode {

        protected static $instance;

        //add read more
        function PSCCPG_excerpt_more( $ocpc_read_more_text ) {
            return sprintf( '<a id="ocpc-postlink" class="read-more" target="_blank" href="%1$s">%2$s</a>',
                get_permalink( get_the_ID() ),
                $ocpc_read_more_text
            );
        }


        // get limited content form post
        function PSCCPG_limited_content($limit, $ocpc_read_more_text, $datacontain) {
              
            if($limit == '') {
              $limit = 15;
            }

            $datacontain= strip_tags($datacontain);
            $ocpc_content = explode(' ', $datacontain, $limit);
            if (count($ocpc_content) >= $limit) {
                array_pop($ocpc_content);
                $moreText = apply_filters('excerpt_more', $ocpc_read_more_text);
                $ocpc_content = implode(" ",$ocpc_content).'...<span class="ocpc-moretext">'.$moreText.'</span>';
            } else {
                $ocpc_content = implode(" ",$ocpc_content);
            }
            $ocpc_content = preg_replace('`\[[^\]]*\]`','',$ocpc_content);
            return '<p>'.$ocpc_content.'</p>';
        }


        function PSCCPG_post_carousel_code($atts, $content = null) {
     
            ob_start();
            extract(shortcode_atts(array(
                'id' => '',
            ), $atts));

            //get post type
            $posttype = get_post_meta( $id, 'ocpc-posttype', true );

            //get slider title
            $slidertitle = get_post_meta( $id, 'ocpc-slidertitle', true );
            $slidertitlecolor = get_post_meta( $id, 'ocpc-slidertitlecolor', true );
            $slidertitlefontsize = get_post_meta( $id, 'ocpc-slidertitlefontsize', true );
            $slidertitleposition = get_post_meta( $id, 'ocpc-slidertitleposition', true );
            $slidertitlefweight = get_post_meta( $id, 'ocpc-slidertitlefweight', true );

            //get totalpost
            $totalpostss     = get_post_meta( $id, 'ocpc-totalposts', true );
            $showpagination = get_post_meta( $id, 'ocpc-showpagination', true );
            $perpage = '10';

            if($showpagination == "on") {
              $totalposts = $perpage;
            } else {
              $totalposts = $totalpostss;
            }
  
            //get post title options
            $posttitledata = unserialize(get_post_meta( $id, 'ocpc-posttitledata', true ));
            $ocpc_posttitle = $posttitledata['ocpc_posttitle'];
            $ocpc_posttitlecolor = $posttitledata['ocpc_posttitlecolor'];
            $ocpc_posttitlefontsize = $posttitledata['ocpc_posttitlefontsize'];
            $ocpc_posttitleposition = $posttitledata['ocpc_posttitleposition'];
            $ocpc_posttitlefweight = $posttitledata['ocpc_posttitlefweight'];

            //get post date options
            $postdatedata = unserialize(get_post_meta( $id, 'ocpc-postdatedata', true ));
            $ocpc_postdate = $postdatedata['ocpc_postdate'];
            $ocpc_postdatecolor = $postdatedata['ocpc_postdatecolor'];
            $ocpc_postdatefontsize = $postdatedata['ocpc_postdatefontsize'];
            $ocpc_postdateposition = $postdatedata['ocpc_postdateposition'];
            $ocpc_postdatefweight = $postdatedata['ocpc_postdatefweight'];

            //get post category options
            $postcatsdata = unserialize(get_post_meta( $id, 'ocpc-postcatsdata', true ));
            $ocpc_postcats = $postcatsdata['ocpc_postcats'];
            $ocpc_postcatscolor = $postcatsdata['ocpc_postcatscolor'];
            $ocpc_postcatsfontsize = $postcatsdata['ocpc_postcatsfontsize'];
            $ocpc_postcatsposition = $postcatsdata['ocpc_postcatsposition'];
            $ocpc_postcatsfweight = $postcatsdata['ocpc_postcatsfweight'];

            //get post author options
            $postdatedata = unserialize(get_post_meta( $id, 'ocpc-postauthordata', true ));
            $ocpc_postauthor = $postdatedata['ocpc_postauthor'];
            $ocpc_postauthorcolor = $postdatedata['ocpc_postauthorcolor'];
            $ocpc_postauthorfontsize = $postdatedata['ocpc_postauthorfontsize'];
            $ocpc_postauthorposition = $postdatedata['ocpc_postauthorposition'];
            $ocpc_postauthorfweight = $postdatedata['ocpc_postauthorfweight'];

            //get image options
            $placeholderimage = get_post_meta($id, 'ocpc-placeholderimage', true );

            //get post description options
            $postdescriptiondata = unserialize(get_post_meta( $id, 'ocpc-postdescriptiondata', true ));
            $ocpc_postdescription = $postdescriptiondata['ocpc_postdescription'];
            $ocpc_postdescriptioncolor = $postdescriptiondata['ocpc_postdescriptioncolor'];
            $ocpc_postdescriptionfontsize = $postdescriptiondata['ocpc_postdescriptionfontsize'];
            $ocpc_postdescriptionposition = $postdescriptiondata['ocpc_postdescriptionposition'];
            $ocpc_postdescriptionfweight = $postdescriptiondata['ocpc_postdescriptionfweight'];
            $readmoretext = get_post_meta( $id, 'ocpc-readmoretext', true );
            $postdesclength = get_post_meta( $id, 'ocpc-postdesclength', true );

            $ocpc_trbgcolor = get_post_meta( $id, 'ocpc_trbgcolor', true );

            //get orderby options
            $orderby = get_post_meta( $id, 'ocpc-orderby', true );

            //get sorting options
            $sortorder = get_post_meta( $id, 'ocpc-sortorder', true );

            //get image size options
            $ocpc_image_size = get_post_meta( $id, 'ocpc_image_size', true );

            //default placeholder image
            $ocpc_default_placeholder = get_option( 'ocpc_default_placeholder' );

            //get post options
            $datasource = get_post_meta( $id, 'ocpc-datasource', true );

            $postbyids = str_replace(' ', '', get_post_meta( $id, 'ocpc-postbyids', true ));
            $postbyids = explode(",", $postbyids);

            $ocpc_postcats_sel2 = get_post_meta( $id, 'ocpc_postcats_sel2', true );
            $ocpc_posttags_sel2 = get_post_meta( $id, 'ocpc_posttags_sel2', true );
            $ocpc_prodcats_sel2 = get_post_meta( $id, 'ocpc_prodcats_sel2', true );
            $ocpc_prodtags_sel2 = get_post_meta( $id, 'ocpc_prodtags_sel2', true );

            //get readmore text color
            $readmoretextcolor = get_post_meta( $id, 'ocpc-readmoretextcolor', true );

            //set randnumber slider main class
            $ocpc_random_number_id = rand();

            //get post layout option
            $post_layout = get_post_meta( $id, 'ocpc-option', true );

            //get post template
            $post_template = get_post_meta( $id, 'ocpc-template', true );

            $ocpc_gl_space_img = get_post_meta( $id, 'ocpc_gl_space_img', true );
            $ocpc_gl_clm = get_post_meta( $id, 'ocpc_gl_clm', true );

            $ocpc_ms_clm = get_post_meta( $id, 'ocpc_ms_clm', true );

            $ocpc_pagibgclr = get_post_meta( $id, 'ocpc-pagibgclr', true );
            $ocpc_pagitxtclr = get_post_meta( $id, 'ocpc-pagitxtclr', true );
            $ocpc_pagilayout = get_post_meta( $id, 'ocpc-pagilayout', true );
            $pagi_num_radious = '';
            $pagi_nextprev_radious = '';

            if($ocpc_pagilayout == 'circle-layout') {
              $pagi_num_radious = '50%';
              $pagi_nextprev_radious = '50px';
            } else if($ocpc_pagilayout == 'boxed-layout') {
              $pagi_num_radious = '0';
              $pagi_nextprev_radious = '0';
            }

            //category svg icon
            $cat_icon = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" version="1.1" width="512" height="512" x="0" y="0" viewBox="0 0 60 60" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path xmlns="http://www.w3.org/2000/svg" d="M57.49,21.5H54v-6.268c0-1.507-1.226-2.732-2.732-2.732H26.515l-5-7H2.732C1.226,5.5,0,6.726,0,8.232v43.687l0.006,0  c-0.005,0.563,0.17,1.114,0.522,1.575C1.018,54.134,1.76,54.5,2.565,54.5h44.759c1.156,0,2.174-0.779,2.45-1.813L60,24.649v-0.177  C60,22.75,58.944,21.5,57.49,21.5z M2,8.232C2,7.828,2.329,7.5,2.732,7.5h17.753l5,7h25.782c0.404,0,0.732,0.328,0.732,0.732V21.5  H12.731c-0.144,0-0.287,0.012-0.426,0.036c-0.973,0.163-1.782,0.873-2.023,1.776L2,45.899V8.232z M47.869,52.083  c-0.066,0.245-0.291,0.417-0.545,0.417H2.565c-0.243,0-0.385-0.139-0.448-0.222c-0.063-0.082-0.16-0.256-0.123-0.408l10.191-27.953  c0.066-0.245,0.291-0.417,0.545-0.417H54h3.49c0.38,0,0.477,0.546,0.502,0.819L47.869,52.083z" fill="#cccccc" data-original="#000000" style="" class=""/><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g><g xmlns="http://www.w3.org/2000/svg"></g></g></svg>';

            include ('ocpc-style.php');
            
            echo '<div class="ocpc_post_layout '.$post_template.' '.$post_layout.' ocpc_slider_id_'.$id.'">';

              if(!empty($slidertitle)) {
                 ?>
                    <div class="ocpc-slider-title">
                      <h2><?php echo $slidertitle; ?></h2>
                    </div>
                 <?php 
              }

                //create args for post.
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                $ocpc_args = array();
                $limit = (!empty($totalposts) ?  absint($totalposts) : 5);
                $offset = ( $limit * $paged ) - $limit;
                
                $ocpc_defualt_args = [
                   'post_type' => $posttype,
                   'posts_per_page'=> (!empty($totalposts) ?  absint($totalposts) : 5),
                   'post_status'    => 'publish',
                   'orderby'   => $orderby,
                   'order'     => $sortorder,
                   'paged' => $paged,
                   'offset' => $offset,
                   'ignore_sticky_posts' => 1,
                ];

                $ocpc_args_img = array(
                   'post_type'      => 'attachment',
                   'posts_per_page'=> (!empty($totalposts) ?  absint($totalposts) : 5),
                   'post_status'    => 'any',
                   'post_mime_type' => 'image',
                   'orderby'   => $orderby,
                   'order'     => $sortorder,
                   'paged' => $paged,
                   'offset' => $offset,
                );

                if($datasource == 'ocpc-op_categories') {
                   if($posttype == 'product') {
                      $op_categories = [
                         'tax_query' => array(
                            'relation' => 'AND',
                            array(
                               'taxonomy' => 'product_cat',
                               'field' => 'id',
                               'terms' => $ocpc_prodcats_sel2,
                               'operator' => 'IN',
                            )
                         ),
                      ];
                   }else if($posttype == 'post') {
                      $op_categories = [
                         'category__in' => $ocpc_postcats_sel2,
                      ];
                   }
                   $ocpc_args = array_merge($ocpc_defualt_args, $op_categories);
                }else if( $datasource == 'ocpc-op_id') {
                   $op_id = [
                      'post__in' => $postbyids,
                   ];
                   $ocpc_args = array_merge($ocpc_defualt_args, $op_id);
                   $ocpc_args_img = array_merge($ocpc_args_img, $op_id);
                }else if( $datasource == 'ocpc-op_tags') {
                   if($posttype == 'product') {
                      $op_tags = [
                         'tax_query' => array(
                             'relation' => 'AND',
                             array(
                                 'taxonomy' => 'product_tag',
                                 'field' => 'id',
                                 'terms' => $ocpc_prodtags_sel2,
                                 'operator' => 'IN',
                             )
                         ),
                      ];
                   }else if($posttype == 'post') {
                      $op_tags = [
                         'tag__in' => $ocpc_posttags_sel2,
                      ];
                   }
                   $ocpc_args = array_merge($ocpc_defualt_args, $op_tags);
                }else {
                   $ocpc_args = $ocpc_defualt_args;
                }
                ?>
                <div class="ocpc_post_main_class">
                   	<?php
                        if($showpagination == 'off') {
                          unset($ocpc_args_img['paged']);
                          unset($ocpc_args_img['offset']);
                          unset($ocpc_args['paged']);
                          unset($ocpc_args['offset']);
                        }

                      	if($post_layout == "gallery") {

                          if($post_template == "ocpc-template-1") {
                              include('templates/gallery/ocpc-gallery-template-1.php');
                          } elseif ($post_template == "ocpc-template-2") {
                              include('templates/gallery/ocpc-gallery-template-2.php');
                          } elseif ($post_template == "ocpc-template-3") {
                              include('templates/gallery/ocpc-gallery-template-3.php');
                          } elseif ($post_template == "ocpc-template-4") {
                              include('templates/gallery/ocpc-gallery-template-4.php');
                          } elseif ($post_template == "ocpc-template-5") {
                              include('templates/gallery/ocpc-gallery-template-5.php');
                          } elseif ($post_template == "ocpc-template-6") {
                              include('templates/gallery/ocpc-gallery-template-6.php');
                          } elseif ($post_template == "ocpc-template-7") {
                              include('templates/gallery/ocpc-gallery-template-7.php');
                          } elseif ($post_template == "ocpc-template-8") {
                              include('templates/gallery/ocpc-gallery-template-8.php');
                          } elseif ($post_template == "ocpc-template-9") {
                              include('templates/gallery/ocpc-gallery-template-9.php');
                          } elseif ($post_template == "ocpc-template-10") {
                              include('templates/gallery/ocpc-gallery-template-10.php');
                          }
                      	}

                    	  if($post_layout == "carousel") {

                    	    if($post_template == "ocpc-template-1") {
                              include('templates/carousel/ocpc-carousel-template-1.php');
                          } elseif ($post_template == "ocpc-template-2") {
                              include('templates/carousel/ocpc-carousel-template-2.php');
                          } elseif ($post_template == "ocpc-template-3") {
                              include('templates/carousel/ocpc-carousel-template-3.php');
                          } elseif ($post_template == "ocpc-template-4") {
                              include('templates/carousel/ocpc-carousel-template-4.php');
                          } elseif ($post_template == "ocpc-template-5") {
                              include('templates/carousel/ocpc-carousel-template-5.php');
                          } elseif ($post_template == "ocpc-template-6") {
                              include('templates/carousel/ocpc-carousel-template-6.php');
                          } elseif ($post_template == "ocpc-template-7") {
                              include('templates/carousel/ocpc-carousel-template-7.php');
                          } elseif ($post_template == "ocpc-template-8") {
                              include('templates/carousel/ocpc-carousel-template-8.php');
                          } elseif ($post_template == "ocpc-template-9") {
                              include('templates/carousel/ocpc-carousel-template-9.php');
                          } elseif ($post_template == "ocpc-template-10") {
                              include('templates/carousel/ocpc-carousel-template-10.php');
                          }
                        }

                      	if($post_layout == "masonry") {

                       	  if($post_template == "ocpc-template-1") {
                              include('templates/masonry/ocpc-masonry-template-1.php');
                          } elseif ($post_template == "ocpc-template-2") {
                              include('templates/masonry/ocpc-masonry-template-2.php');
                          } elseif ($post_template == "ocpc-template-3") {
                              include('templates/masonry/ocpc-masonry-template-3.php');
                          } elseif ($post_template == "ocpc-template-4") {
                              include('templates/masonry/ocpc-masonry-template-4.php');
                          } elseif ($post_template == "ocpc-template-5") {
                              include('templates/masonry/ocpc-masonry-template-5.php');
                          } elseif ($post_template == "ocpc-template-6") {
                              include('templates/masonry/ocpc-masonry-template-6.php');
                          } elseif ($post_template == "ocpc-template-7") {
                              include('templates/masonry/ocpc-masonry-template-7.php');
                          } elseif ($post_template == "ocpc-template-8") {
                              include('templates/masonry/ocpc-masonry-template-8.php');
                          } elseif ($post_template == "ocpc-template-9") {
                              include('templates/masonry/ocpc-masonry-template-9.php');
                          } elseif ($post_template == "ocpc-template-10") {
                              include('templates/masonry/ocpc-masonry-template-10.php');
                          }
                        }
                   	?>
                </div>
                <?php 
            echo '</div>';

            ?>
                <!--  Slider Settings -->
                <script type="text/javascript">
                    var ocpc_left_chevron = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 407.436 407.436" style="enable-background:new 0 0 407.436 407.436;" xml:space="preserve"><polygon points="315.869,21.178 294.621,0 91.566,203.718 294.621,407.436 315.869,386.258 133.924,203.718 "/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';

                    var ocpc_right_chevron = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 407.436 407.436" style="enable-background:new 0 0 407.436 407.436;" xml:space="preserve"><polygon points="112.814,0 91.566,21.178 273.512,203.718 91.566,386.258 112.814,407.436 315.869,203.718 "/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';

                    jQuery(document).ready(function() {
                       var ocpc_post_slider = jQuery(".ocpc-main<?php echo $ocpc_random_number_id; ?>");
                       var ocpc_slider_aero = <?php echo esc_attr(get_post_meta( $id, 'ocpc-sliderarrow', true ));?>;
                       ocpc_post_slider.owlCarousel({
                           loop: true,
                           autoplay: <?php echo  esc_attr(get_post_meta( $id, 'ocpc-autoplay', true ));?>,
                           autoplayTimeout: <?php echo  esc_attr(get_post_meta( $id, 'ocpc-autoplaytimeout', true ));?>,
                           autoplayHoverPause: true,
                           margin:<?php echo esc_attr(get_post_meta( $id, 'ocpc-spacingbetwee', true ));?>,
                           nav: true,
                           navText:["<div class='ocpc_nav_slide_prev'>" + ocpc_left_chevron + "</div>","<div class='ocpc_nav_slide_next'>" + ocpc_right_chevron + "</div>"],
                           responsive:{
                             0 : {
                                  items: <?php echo  esc_attr(get_post_meta( $id, 'ocpc-mobileperrow', true ));?>,
                             },
                             500: {
                                  items: <?php echo  esc_attr(get_post_meta( $id, 'ocpc-mobileperrow', true ));?>,
                             },
                             768:{
                                   items: <?php echo  esc_attr(get_post_meta( $id, 'ocpc-tabletperrow', true ));?>,
                             },
                             991:{
                                   items: <?php echo  esc_attr(get_post_meta( $id, 'ocpc-tabletperrow', true ));?>,
                             },
                             1199:{
                                  items: <?php echo  esc_attr(get_post_meta( $id, 'ocpc-perrow', true ));?>,
                             }
                         }
                       });

                       if(ocpc_slider_aero == true) {
                          ocpc_post_slider.find('.owl-nav').removeClass('disabled');
                          ocpc_post_slider.on('changed.owl.carousel', function(event) {
                                ocpc_post_slider.find('.owl-nav').removeClass('disabled');
                          });
                       }

                       if(ocpc_slider_aero == false) {
                          ocpc_post_slider.find('.owl-nav').addClass('disabled');
                          ocpc_post_slider.on('changed.owl.carousel', function(event) {
                                      ocpc_post_slider.find('.owl-nav').addClass('disabled');
                          });
                       }
                    }); 
                </script>

            <?php
            return $var = ob_get_clean();
        }


        function init() {
            add_shortcode( 'ocpc-post-carousel', array($this,'PSCCPG_post_carousel_code'));
            add_filter( 'excerpt_more',  array($this,'PSCCPG_excerpt_more'));
        }

        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    PSCCPG_shortcode::instance();
}