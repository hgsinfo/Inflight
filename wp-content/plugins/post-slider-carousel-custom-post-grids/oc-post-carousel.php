<?php
/**
 * Plugin Name: Post Slider Carousel & Custom Post Grids
 * Description: This plugin allows you to display your posts with a slider ( All slider customize options ).
 * Version: 1.0
 * Copyright: 2019
 */

if (!defined('ABSPATH')) {
    die('-1');
}
if (!defined('PSCCPG_PLUGIN_NAME')) {
    define('PSCCPG_PLUGIN_NAME', 'Post Carousel');
}
if (!defined('PSCCPG_PLUGIN_VERSION')) {
    define('PSCCPG_PLUGIN_VERSION', '1.0.0');
}
if (!defined('PSCCPG_PLUGIN_FILE')) {
    define('PSCCPG_PLUGIN_FILE', __FILE__);
}
if (!defined('PSCCPG_PLUGIN_DIR')) {
    define('PSCCPG_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('PSCCPG_BASE_NAME')) {
    define('PSCCPG_BASE_NAME', plugin_basename(PSCCPG_PLUGIN_DIR));
}
if (!defined('PSCCPG_DOMAIN')) {
    define('PSCCPG_DOMAIN', 'psccpg');
}

//Main class
//Load required js,css and other files

if (!class_exists('PSCCPG')) {

    class PSCCPG {

        protected static $instance;

        //Load all includes files
        function includes() {

            //Admn site Layout
            include_once('includes/psccpg-backend.php');

            //Update all Option Data
            include_once('includes/psccpg-backend-updatemeta.php');

            //create shortcode for display post slider
            include_once('includes/psccpg-shortcode.php');
        }


        function init() {
            add_action( 'admin_enqueue_scripts', array($this, 'PSCCPG_load_admin_script_style'));
            add_action( 'wp_enqueue_scripts',  array($this, 'PSCCPG_load_script_style'));
            add_filter( 'plugin_row_meta', array( $this, 'PSCCPG_plugin_row_meta' ), 10, 2 );
            add_image_size( 'psccpg_post_slider_img', 350, 270, false ); // (cropped)
        }

        function PSCCPG_plugin_row_meta( $links, $file ) {
            if ( PSCCPG_BASE_NAME === $file ) {
                $row_meta = array(
                  'rating'    =>  '<a href="https://www.xeeshop.com/support-us/?utm_source=aj_plugin&utm_medium=plugin_support&utm_campaign=aj_support&utm_content=aj_wordpress" target="_blank">Support</a> |<a href="https://wordpress.org/support/plugin/post-slider-by-oc/reviews/#new-post" target="_blank"><img src="'.PSCCPG_PLUGIN_DIR.'/assets/images/star.png" class="ocpc_rating_div"></a>',
                );

                return array_merge( $links, $row_meta );
            }
            return (array) $links;
        }

        //Add JS and CSS on Frontend
        function PSCCPG_load_script_style() {
            wp_enqueue_style( 'owlcarousel-min', PSCCPG_PLUGIN_DIR . '/assets/owlcarousel/assets/owl.carousel.min.css', false, '1.0.0' );
            wp_enqueue_style( 'owlcarousel-theme', PSCCPG_PLUGIN_DIR . '/assets/owlcarousel/assets/owl.theme.default.min.css', false, '1.0.0' );
            wp_enqueue_script( 'owlcarousel', PSCCPG_PLUGIN_DIR . '/assets/owlcarousel/owl.carousel.js', false, '1.0.0' );
            wp_enqueue_script( 'masonrypost', PSCCPG_PLUGIN_DIR . '/assets/js/masonry.pkgd.min.js', false, '1.0.0' );
            wp_enqueue_script( 'ocpcfront_js', PSCCPG_PLUGIN_DIR . '/assets/js/ocpc-front-js.js', false, '1.0.0' );
            wp_enqueue_style( 'ocpcfront_css', PSCCPG_PLUGIN_DIR . '/assets/css/ocpc-front-style.css', false, '1.0.0' );
            wp_enqueue_script( 'masonrypostimage',PSCCPG_PLUGIN_DIR . '/assets/js/imagesloaded.pkgd.min.js', false,'1.0.0');
        }


        //Add JS and CSS on Backend
        function PSCCPG_load_admin_script_style() {
            wp_enqueue_style( 'ocpcadmin_css', PSCCPG_PLUGIN_DIR . '/assets/css/ocpc-admin-style.css', false, '1.0.0' );
            wp_enqueue_script( 'ocpcadmin_js', PSCCPG_PLUGIN_DIR . '/assets/js/ocpc-admin-js.js', array( 'jquery', 'select2') );
            wp_enqueue_script( 'media_uploader', PSCCPG_PLUGIN_DIR . '/assets/js/media-uploader.js', false, '1.0.0' );
            wp_enqueue_style( 'wp-color-picker');
            wp_enqueue_script( 'wp-color-picker');
            wp_enqueue_style('ocpcadmin_select2', PSCCPG_PLUGIN_DIR . '/assets/css/select2.min.css' );
            wp_enqueue_script('ocpcadmin_select2', PSCCPG_PLUGIN_DIR . '/assets/js/select2.min.js', array('jquery') );
            wp_enqueue_script( 'wp-color-picker-alpha', PSCCPG_PLUGIN_DIR . '/assets/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), '1.0.0', true );
        }

        function PSCCPG_plugin_activation_func() {
            $filename = 'psccpg_placeholder.jpg';
            global $wpdb;
            $query = "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE '%/$filename'";

            if($wpdb->get_var($query)  > 0) {
                $plchldrExists = 'true';
            } else {
                $plchldrExists = 'false';
            }

            if($plchldrExists == 'false') {
                $desc = 'post carousel placeholder';
                $file = PSCCPG_PLUGIN_DIR .'/assets/images/psccpg_placeholder.jpg';
                $file_array  = [ 'name' => wp_basename( $file ), 'tmp_name' => download_url( $file ) ];

                // If error storing temporarily, return the error.
                if ( is_wp_error( $file_array['tmp_name'] ) ) {
                    return $file_array['tmp_name'];
                }

                // Do the validation and storage stuff.
                $id = media_handle_sideload( $file_array, 0, $desc );
                update_option( 'ocpc_default_placeholder', $id );

                // If error storing permanently, unlink.
                if ( is_wp_error( $id ) ) {
                    @unlink( $file_array['tmp_name'] );
                    return $id;
                }
            }
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
                self::$instance->includes();
            }
            return self::$instance;
        }
    }
    add_action('plugins_loaded', array('PSCCPG', 'instance'));
    register_activation_hook( PSCCPG_PLUGIN_FILE, array('PSCCPG', 'PSCCPG_plugin_activation_func' ));
}