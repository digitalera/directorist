<?php

if ( ! defined('ABSPATH') ) { die( ATBDP_ALERT_MSG ); }

if( !class_exists( 'ATBDP_Helper' ) ) :

class ATBDP_Helper {


    private $nonce_action = 'atbdp_nonce_action';
    private $nonce_name = 'atbdp_nonce';

    public function __construct(){
        if ( ! defined('ABSPATH') ) { return; }
        add_action('init', array( $this, 'check_req_php_version' ), 100 );
    }
    public function check_req_php_version( ){
        if ( version_compare( PHP_VERSION, '5.4', '<' )) {
            add_action( 'admin_notices', array($this, 'notice'), 100 );


            // deactivate the plugin because required php version is less.
            add_action( 'admin_init', array($this, 'deactivate_self'), 100 );

            return;
        }
    }
    public function notice() { ?>
        <div class="error"> <p>
                <?php
                printf(__('%s requires minimum PHP 5.4 to function properly. Please upgrade PHP version. The Plugin has been auto-deactivated.. You have PHP version %d', ATBDP_TEXTDOMAIN), ATBDP_NAME, PHP_VERSION);
                ?>
            </p></div>
        <?php
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

    public function deactivate_self() {
        deactivate_plugins( ATBDP_BASE );
    }

    public function verify_nonce($nonce='', $action='', $method='_REQUEST' ){
        // if we do not pass any nonce and action then use default nonce and action name on this class,
        // else check provided nonce and action
        if (empty($nonce) || empty($action)){
        $nonce      = (!empty($$method[$this->nonce_name()])) ? $$method[$this->nonce_name()] : null ;
        $nonce_action  = $this->nonce_action();
        }else{
            $nonce      = (!empty($_REQUEST[$nonce])) ? $_REQUEST[$nonce] : null ;
            $nonce_action = $action;
        }
        return wp_verify_nonce( $nonce, $nonce_action );

    }

    public function nonce_action(){
        return $this->nonce_action;
    }
    public function nonce_name(){
        return $this->nonce_name;
    }

    public function social_links(){
        $s = array(
            'facebook' => __('Facebook', ATBDP_TEXTDOMAIN),
            'twitter'   => __('Twitter', ATBDP_TEXTDOMAIN),
            'google-plus' =>  __('Google+', ATBDP_TEXTDOMAIN),
            'linkedin' =>  __('LinkedIn', ATBDP_TEXTDOMAIN),
            'pinterest' =>  __('Pinterest', ATBDP_TEXTDOMAIN),
            'instagram' =>  __('Instagram', ATBDP_TEXTDOMAIN),
            'tumblr' =>  __('Tumblr', ATBDP_TEXTDOMAIN),
            'flickr' =>  __('Flickr', ATBDP_TEXTDOMAIN),
            'snapchat-ghost' =>  __('Snapchat', ATBDP_TEXTDOMAIN),
            'reddit' =>  __('Reddit', ATBDP_TEXTDOMAIN),
            'youtube' =>  __('Youtube', ATBDP_TEXTDOMAIN),
            'vimeo' =>  __('Vimeo', ATBDP_TEXTDOMAIN),
            'vine' =>  __('Vine', ATBDP_TEXTDOMAIN),
            'github' =>  __('Github', ATBDP_TEXTDOMAIN),
            'dribbble' =>  __('Dribbble', ATBDP_TEXTDOMAIN),
            'behance' =>  __('Behance', ATBDP_TEXTDOMAIN),
            'soundcloud' =>  __('SoundCloud', ATBDP_TEXTDOMAIN),
            'stack-overflow' =>  __('StackOverFLow', ATBDP_TEXTDOMAIN),
        );
        asort($s);
        return $s;
    }


    /**
     * Darken or lighten a given hex color and return it.
     * @param string $hex Hex color code to be darken or lighten
     * @param int $percent The number of percent of darkness or brightness
     * @param bool|true $darken Lighten the color if set to false, otherwise, darken it. Default is true.
     *
     * @return string
     */
    public function adjust_brightness($hex, $percent, $darken = true) {
        // determine if we want to lighten or draken the color. Negative -255 means darken, positive integer means lighten
        $brightness = $darken ? -255 : 255;
        $steps = $percent*$brightness/100;

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }





    /**
     * Lists of html tags that are allowed in a content
     * @return array List of allowed tags in a content
     */
    public function allowed_html() {
        return array(
            'i' => array(
                'class' => array(),
            ),
            'strong' => array(
                'class' => array(),
            ),
            'em' => array(
                'class' => array(),
            ),
            'a' => array(
                'class' => array(),
                'href' => array(),
                'title' => array(),
                'target' => array(),
            ),

        );
    }

    /**
     * Prints pagination for custom post
     * @param $loop
     * @param int $paged
     *
     * @return string
     */
    public  function show_pagination( $loop, $paged = 1){
        //@TODO: look into this deeply later : http://www.insertcart.com/numeric-pagination-wordpress-using-php/
        $largeNumber = 999999999; // we need a large number here
        $links = paginate_links( array(
            'base' => str_replace( $largeNumber, '%#%', esc_url( get_pagenum_link( $largeNumber ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, $paged ),
            'total' => $loop->max_num_pages,
            'prev_text' => __('&laquo; Prev', ATBDP_TEXTDOMAIN),
            'next_text' => __('Next &raquo;', ATBDP_TEXTDOMAIN),
            'type' => 'list',
        ) );


      return $links;
    }

    public function show_login_message($message=''){

        $t = !empty($message) ? $message : '';
        $t = apply_filters('atbdp_unauthorized_access_message', $t);
        ?>
            <!--        HTML STARTS -->
                <div class="notice_wrapper">
                    <p class="notice"><span class="fa fa-info" aria-hidden="true"></span><?php echo $t; ?></p>
                </div>


<!--        HTML codes ends -->

<?php


    }

    /**
     * It converts a mysql datetime string to human readable relative time
     * @param string $mysql_date Mysql Datetime string eg. 2018-5-11 17:02:26
     * @param bool $echo [optional] If $echo is true then print the value else return the value. default is true.
     * @param string $suffix [optional] Suffix to be added to the related time. Default is ' ago.' .
     * @return string|void It returns the relative time from a mysql datetime string
     */
    public function mysql_to_human_time($mysql_date, $echo=true, $suffix=' ago.')
    {
        $date = DateTime::createFromFormat ( "Y-m-d H:i:s", $mysql_date );
        $time = human_time_diff($date->getTimestamp()) .$suffix;
        if(!$echo) return $time;
        echo $time;
    }




}
endif;
