<?php
/**
 * DB class of Directorist
 *
 * This class is for interacting database table
 *
 * @package     Directorist
 * @subpackage  Classes/DB Customer Meta
 * @copyright   Copyright (c) 2018, AazzTech
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) die('What the hell are you doing here accessing this file directly');
if (!class_exists('ATBDP_Template')):
    class ATBDP_Template
    {


        public function __construct()
        {
            // load custom page template for the single page for our custom post
            //add_filter('single_template', array( $this, 'load_custom_single_template') );
            //add_filter('template_include', array($this, 'custom_search_template'));


        }


        /**
         * It loads custom template for member single page
         * @param string $template The name of the current template
         *
         * @return string It returns custom template for single page of a member for the adl-listing post type
         */
        public function load_custom_single_template($template) {
            global $post;
            // Is this a ATBDP_POST_TYPE post?
            if (!empty($post->post_type) && $post->post_type == ATBDP_POST_TYPE){

                // The name of custom post type single template
                $custom_template = 'single-'.ATBDP_POST_TYPE.'.php';

                // A specific single template for my custom post type exists in theme folder? Or it also doesn't exist in my plugin?
                if($template === get_stylesheet_directory() . '/' . $custom_template
                    || !file_exists(ATBDP_TEMPLATES_DIR . $custom_template)) {
                    //Then return "single.php" or "single-ATBDP_POST_TYPE.php" from theme directory.
                    return $template;
                }
                // enqueue scripts and styles for the  single page template of the plugin.

                // If not, return my plugin custom post type template.
                return ATBDP_TEMPLATES_DIR . $custom_template;
            }

            //This is not my custom post type, do nothing with $template
            return $template;
        }

        public function custom_search_template($template)
        {

            global $wp_query;
            global $post; //culprit



            $post_type = get_query_var('post_type');
            $post_type = (!empty( $post_type)) ?  $post_type : ((is_object($post) && !empty($post->post_type)) ? $post->post_type : 'any');

            $custom_search_template = 'search-'.ATBDP_POST_TYPE.'.php';
            if( $wp_query->is_search && $post_type == ATBDP_POST_TYPE )
            {
                $search_template = locate_template($custom_search_template); // if the theme has the template return it
                if ($search_template){
                    return $search_template;
                }elseif (file_exists( ATBDP_TEMPLATES_DIR . $custom_search_template )){
                    // if the theme does not have the template file then return plugin's template file if it exists
                    return ATBDP_TEMPLATES_DIR . $custom_search_template;
                }


            }
            return $template;
        }


        public function load_template($template)
        {

        }


        /**
         * Locates template based on the template type.
         *
         * @since 1.0.0
         * @package ATBDP
         * @global string $post_type The post type.
         * @global object $wp WordPress object.
         * @global object $post WordPress post object.
         * @param string $template The template type.
         * @return bool|string The template path.
         */
        public function locate_template($template = '')
        {
            global $post_type, $wp, $post;
            $fields = array();

            switch ($template) {
                case 'signup':
                    return $template = locate_template(array("atbdp-signup.php"));
                    break;

            }

            return false;

        }


        /**
         * Display the font awesome rating icons in place of default rating images.
         *
         * @since 1.0
         * @package ATBDP
         *
         * @param float $rating Current rating value.
         * @param int $star_count Total rating stars. Default 5.
         * @return string Rating icons html content.
         */
        public function font_awesome_rating_stars_html($rating, $star_count = 5)
        {

                $rating = min($rating, $star_count);
                $full_stars = floor($rating);
                $half_stars = ceil($rating - $full_stars);
                $empty_stars = $star_count - $full_stars - $half_stars;

                $html = '<div class="gd-star-rating gd-fa-star-rating">';
                $html .= str_repeat('<i class="fa fa-star gd-full-star"></i>', $full_stars);
                $html .= str_repeat('<i class="fa fa-star-o fa-star-half-full gd-half-star"></i>', $half_stars);
                $html .= str_repeat('<i class="fa fa-star-o gd-empty-star"></i>', $empty_stars);
                $html .= '</div>';


            return $html;
        }

        /**
         * Adds the style for the font awesome rating icons.
         *
         * @since 1.0
         * @param string $full_color The color of full star rating
         * @package ATBDP
         */
        public function font_awesome_rating_css( $full_color = '#757575')
        {
            // Font awesome rating style

                if ($full_color != '#757575') {
                    echo '<style type="text/css">.br-theme-fontawesome-stars .br-widget a.br-active:after,.br-theme-fontawesome-stars .br-widget a.br-selected:after,
			.gd-star-rating i.fa {color:' . stripslashes($full_color) . '!important;}</style>';
                }

        }
    }
endif;