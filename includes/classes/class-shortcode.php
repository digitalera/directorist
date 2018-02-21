<?php
if ( !class_exists('ATBDP_Shortcode') ):

class ATBDP_Shortcode {

    public function __construct()
    {

        add_shortcode( 'search_listing', array( $this, 'search_listing' ) );

        add_shortcode( 'search_result', array( $this, 'search_result' ) );

        add_shortcode( 'all_listing', array( $this, 'all_listing' ) );

        add_shortcode( 'add_listing', array( $this, 'add_listing' ) );

        add_shortcode( 'custom_registration', array( $this, 'user_registration' ) );

        add_shortcode( 'user_dashboard', array( $this, 'user_dashboard' ) );

    }

    public function search_result()
    {
        ob_start();
        if( !isset( $_GET['q'] ) ) {
            /*@todo; make the following text configurable in the settings later. */
            return '<span class="no-result">'.__( 'Sorry, No Matched Results Found !', ATBDP_TEXTDOMAIN ).'</span>';
        }
        $paged = atbdp_get_paged_num();
        $srch_p_num = get_directorist_option('search_posts_num', 6);
        $s_string = sanitize_text_field( $_GET['q'] );// get the searched query
        $in_cat = !empty($_GET['in_cat']) ? sanitize_text_field($_GET['in_cat']) : '';
        $in_loc = !empty($_GET['in_loc']) ? sanitize_text_field($_GET['in_loc']) : '';
        $in_tag = !empty($_GET['in_tag']) ? sanitize_text_field($_GET['in_tag']) : '';

        // lets setup the query args
        $args = array(
            'post_type'      => ATBDP_POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => (int) $srch_p_num,
            'paged'          => $paged,
            's'              => $s_string,
        );

        /*@todo; make the query smaller and specific using cats and locs and in the premium version we may add many criteria to search by*/

        $tax_queries=array(); // initiate the tax query var to append to it different tax query

        if( !empty($in_cat) ) {
            /*@todo; add option to the settings panel to let user choose whether to include result from children or not*/
            $tax_queries[] = array(
                'taxonomy'         => ATBDP_CATEGORY,
                'field'            => 'slug',
                'terms'            => $in_cat,
                'include_children' => true, /*@todo; Add option to include children or exclude it*/
            );

        }

        if( !empty($in_loc) ) {
            /*@todo; add option to the settings panel to let user choose whether to include result from children or not*/
            $tax_queries[] = array(
                'taxonomy'         => ATBDP_LOCATION,
                'field'            => 'slug',
                'terms'            => $in_loc,
                'include_children' => true
            );

        }

        if( !empty($in_tag) ) {
            /*@todo; add option to the settings panel to let user choose whether to include result from children or not*/
            $tax_queries[] = array(
                'taxonomy'         => ATBDP_TAGS,
                'field'            => 'slug',
                'terms'            => $in_tag,
            );

        }

        if (!is_empty_array($tax_queries)){
            $args['tax_query'] = $tax_queries;
        }

        $listings = new WP_Query($args);


        $data_for_template = compact('listings', 'in_loc', 'in_cat', 'in_tag', 's_string', 'paged');
        ATBDP()->load_template('search-at_biz_dir', array( 'data' => $data_for_template ));
        return ob_get_clean();
    }

    public function all_listing()
    {
        ob_start();
        ATBDP()->load_template('front-end/all-listing');
        ATBDP()->enquirer->common_scripts_styles();
        return ob_get_clean();
    }

    public function user_dashboard()
    {

        ob_start();
        // show user dashboard if the user is logged in, else kick him out of this page or show a message
        if (is_user_logged_in()){
             ATBDP()->enquirer->front_end_enqueue_scripts(true); // all front end scripts forcibly here
             ATBDP()->user->user_dashboard();
        }else{
            // user not logged in;
            $error_message = sprintf(__('You need to be logged in to view the content of this page. You can login %s.', ATBDP_TEXTDOMAIN), "<a href='".wp_login_url()."'> ". __('Here', ATBDP_TEXTDOMAIN)."</a>");


             ?>
            <section class="directory_wrapper single_area">
                <div class="<?php echo is_directoria_active() ? 'container': 'container-fluid'; ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <?php  ATBDP()->helper->show_login_message($error_message); ?>
                        </div>
                    </div>
            </section>
<?php

        }

        return ob_get_clean();

    }
    public function search_listing($atts, $content = null) {

        ob_start();
         ATBDP()->load_template('listing-home');
         ATBDP()->enquirer->common_scripts_styles();
         ATBDP()->enquirer->search_listing_scripts_styles();
        return ob_get_clean();
    }

    public function add_listing($atts, $content = null, $sc_name) {
        ob_start();
        if (is_user_logged_in()) {
           ATBDP()->enquirer->add_listing_scripts_styles();

           ATBDP()->load_template('front-end/add-listing');
        }else{
            // user not logged in;
            $error_message = sprintf(__('You need to be logged in to view the content of this page. You can login %s.', ATBDP_TEXTDOMAIN), "<a href='".wp_login_url()."'> ". __('Here', ATBDP_TEXTDOMAIN)."</a>");
            ?>


            <section class="directory_wrapper single_area">
                <div class="<?php echo is_directoria_active() ? 'container': ' container-fluid'; ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <?php  ATBDP()->helper->show_login_message($error_message); ?>
                        </div>
                    </div>
                </div> <!--ends container-fluid-->
            </section>
<?php

        }
    }

    public function user_registration()
    {

        ob_start();
        // show registration form if the user is not
        if (!is_user_logged_in()){
             ATBDP()->user->registration_form();
        }else{
            $error_message = sprintf(__('Registration page is only for unregistered user. <a href="%s">Go Back To Home</a>', ATBDP_TEXTDOMAIN), esc_url(get_home_url()));
            ?>
            <div class="single_area">
                <div class="<?php echo is_directoria_active() ? 'container': ' container-fluid'; ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <?php ATBDP()->helper->show_login_message($error_message);  ?>
                        </div>
                    </div> <!--ends .row-->
                </div>
            </div>
        <?php
        }

        return ob_get_clean();
    }


}


endif;