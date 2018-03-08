<?php
/**
 * Checkout
 *
 * @package       directorist
 * @subpackage    directorist/includes/checkout
 * @copyright     Copyright 2018. AazzTech
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GNU Public License
 * @since         3.1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * ATBDP_Checkout Class
 *
 * @since    3.1.0
 * @access   public
 */
class ATBDP_Checkout
{
    /**
     * @var string
     */
    public $nonce = 'checkout_nonce';
    /**
     * @var string
     */
    public $nonce_action = 'checkout_action';

    public function __construct()
    {

    }

    /**
     *
     */
    function ajax_atbdp_format_total_amount()
    {

        if (valid_js_nonce()){
            if( !empty( $_POST['amount'] ) ) {
                echo atbdp_format_payment_amount( $_POST['amount'] );
            }
        }
        wp_die();

    }

    /**
     * @return string
     */
    public function display_checkout_content()
    {
        // vail out, if user is not logged in. No need to run further code
        if (!is_user_logged_in()) {
            // user not logged in;
            $error_message = sprintf(__('You need to be logged in to view the content of this page. You can login %s.', ATBDP_TEXTDOMAIN), "<a href='" . wp_login_url() . "'> " . __('Here', ATBDP_TEXTDOMAIN) . "</a>");
            ?>
            <section class="directory_wrapper single_area">
                <div class="<?php echo is_directoria_active() ? 'container' : ' container-fluid'; ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <?php ATBDP()->helper->show_login_message($error_message); ?>
                        </div>
                    </div>
                </div> <!--ends container-fluid-->
            </section>
            <?php
            return;
        }
        // vail if monetization is not active.
        if (! get_directorist_option('enable_monetization')) { return __('Monetization is not active on this site. if you are an admin, you can enable it from the settings panel.', ATBDP_TEXTDOMAIN);}
        wp_enqueue_script( 'atbdp_checkout_script' );
        ob_start();
        // user logged in & monetization is active, so lets continue
        // get the listing id from the url query var
        //$listing_id = get_query_var('atbdp_listing_id'); // we will use get_query_var when we will use url rewriting
        $listing_id = !empty($_GET['atbdp_listing_id']) ? $_GET['atbdp_listing_id']: 0; // temporary solution
        // vail if the id is empty or post type is not our post type.
        if ( empty($listing_id) || (!empty($listing_id) && ATBDP_POST_TYPE != get_post_type($listing_id)) ) {
            return __('Sorry, Something went wrong. Please try again.', ATBDP_TEXTDOMAIN);
        }

        // if the checkout form is submitted, then process placing order
        if ('POST' == $_SERVER['REQUEST_METHOD'] && ATBDP()->helper->verify_nonce( $this->nonce, $this->nonce_action )){
            $this->create_order($listing_id, $_POST);
            // Process the order
        }else{
            // Checkout form is not submitted, so show the content of the checkout items here
            $form_data = apply_filters( 'atbdp_checkout_form_data', array(), $listing_id ); // this is the hook that an extension can hook to, to add new items on checkout page.eg. plan
            // let's add featured listing data
            $featured_active = get_directorist_option('enable_featured_listing');
            if ($featured_active){
                $title = get_directorist_option('featured_listing_title', __('Featured', ATBDP_TEXTDOMAIN));
                $desc = get_directorist_option('featured_listing_desc');
                $price = get_directorist_option('featured_listing_price');
                $form_data[] = array(
                        'type' => 'header',
                        'title' => $title,
                );
                $form_data[] = array(
                        'type' => 'checkbox',
                        'name' => 'feature',
                        'value' => 1,
                        'selected' => 1,
                        'title' => $title,
                        'desc' => $desc,
                        'price' => $price,
                );
            }
            // pass the data using a data var, so that we can add to it more item later.
            $data = array(
                    'form_data' => $form_data,
                    'listing_id' => $listing_id,
            );

            ATBDP()->load_template('front-end/checkout-form', $data);
        }

        return ob_get_clean();
    }

    /**
     * It creates an order for the given listing id
     * @param int $listing_id Listing ID
     * @param array $data Optional Data
     */
    private function create_order($listing_id=0, $data = array())
    {
        if (empty($listing_id)) return; // vail if not listing id is provided
        // create an order
        $order_id = wp_insert_post( array(
            'post_content' => '',
            'post_title' => sprintf('Order for the listing ID #%d', $listing_id),
            'post_status' => 'publish',
            'post_type' => 'atbdp_orders',
            'comment_status' => false,
        ) );
        // if order is created successfully then process the order
        if ($order_id){
            // Hook for developer
            do_action( 'atbdp_order_created', $order_id, $listing_id );
            /*@todo; Find a better way to search for a order with a given ID*/
            /*wp_update_post(array(
                'ID'=> (int) $order_id,
                'post_type' => 'atbdp_orders',
                'post_title' => sprintf('Order #%d for the listing ID #%d', $order_id, $listing_id)
                ));*/
            $order_details = apply_filters( 'atbdp_order_details', array(), $order_id );
            //If featured item is bought, attach it to the order.

            if (!empty($data['feature'])) {
                update_post_meta($order_id, '_featured', 1);
                //lets add the settings of featured listing to the order details
                /*
             array(
                    'active'        => get_directorist_option('enable_featured_listing'),
                    'label'         => get_directorist_option('featured_listing_title'),
                    'desc'          => get_directorist_option('featured_listing_desc'),
                    'price'         => get_directorist_option('featured_listing_price'),
                    'show_ribbon'   => get_directorist_option('show_featured_ribbon'),
        );
                */
                $order_details[] = atbdp_get_featured_settings_array();
            }
            // now lets calculate the total price of all order item's price
            $amount = 0.00;
            foreach ($order_details as $detail) {
                if (isset($detail['price'])){
                    $amount += $detail['price'];
                }
            }

            /*Lowercase alphanumeric characters, dashes and underscores are allowed.*/
            $gateway = ! empty( $amount ) && !empty($data['payment_gateway']) ? sanitize_key( $data['payment_gateway'] ) : 'free';


            // save required data as order post meta
            update_post_meta($order_id, '_listing_id', $listing_id);
            update_post_meta($order_id, '_amount', $amount);
            update_post_meta( $order_id, '_payment_gateway', $gateway );
            update_post_meta( $order_id, '_payment_status', 'created' );

            // @todo; notify admin that an order has been placed, add settings to control this notification
            //atbdp_email_admin_order_created( $listing_id, $order_id );
            $this->process_payment($amount, $gateway, $order_id, $listing_id, $data);
        }

    }

    /**
     * It process the payment of the order
     *
     * @param float $amount The order amount
     * @param string $gateway The name of the gateway
     * @param int $order_id     The order ID
     * @param int $listing_id   The Listing ID for which the order has been created.
     * @param array $data       The $_POST data basically
     */
    private function process_payment($amount, $gateway, $order_id, $listing_id, $data=array()){
        /*Process paid listing*/
        if( $amount > 0 ) {
            if( 'bank_transfer' == $gateway ) {
                update_post_meta( $order_id, '_transaction_id', wp_generate_password( 15, false ) );
                /*@todo; Notify owner based on admin settings*/
                //atbdp_email_listing_owner_order_created_offline( $listing_id, $order_id );

                //hook for developer
                do_action('atbdp_offline_order_created', $order_id, $listing_id);
                // admin will mark the order completed manually once he get the payment on his bank.
                // let's redirect the user to the payment receipt page.
                $redirect_url = ATBDP_Permalink::get_payment_receipt_page_link( $order_id );
                wp_redirect( $redirect_url );
                exit;
            } else {
                /*@todo; Notify owner based on admin settings*/
                //atbdp_email_listing_owner_order_created( $listing_id, $order_id );
                /**
                 * fires 'atbdp_process_gateway_name_payment', it helps extensions and other payment plugin to process the payment
                 * atbdp_orders post has all the required information in its meta data like listing id and featured data etc.
                 *
                 * @param string    $gateway        The name of the gateway
                 * @param int       $order_id       The Order ID
                 * @param int       $listing_id     The Listing ID
                 * @param array     $data           The $_POST data basically
                 */
                do_action( 'atbdp_process_'.$gateway.'_payment', $order_id, $listing_id, $data );
                do_action('atbdp_online_order_processed', $order_id, $listing_id);

            }
        } else {
            /*@todo; Notify owner based on admin settings that order CREATED*/
            //atbdp_email_listing_owner_order_created( $listing_id, $order_id );
            /*complete Free listing Order */
            $this->complete_free_order(
                    array(
                        'ID' => $order_id,
                        'transaction_id' => wp_generate_password( 15, false ),
                        'listing_id' => $listing_id
                    )
            );

            $redirect_url = ATBDP_Permalink::get_payment_receipt_page_link( $order_id );
            wp_redirect( $redirect_url );
            exit;

        }
    }


    /**
     * It completes order that are free of charge
     * @param array $order_data The array of order data
     */
    private function complete_free_order($order_data)
    {
        // add payment status, tnx_id etc.
        update_post_meta( $order_data['ID'], '_payment_status', 'completed' );
        update_post_meta( $order_data['ID'], '_transaction_id', $order_data['transaction_id'] );
        // If the order has featured, make the related listing featured.
        $featured = get_post_meta( $order_data['ID'], '_featured', true );
        if( ! empty( $featured ) ) {
            update_post_meta( $order_data['listing_id'], '_featured', 1 );
        }

        // Order has been completed. Let's fire a hook for a developer to extend if they wish
        do_action( 'atbdp_order_completed', $order_data['ID'] );

        // @todo; send notifications to user and admin based on the admin settings

        //atbdp_email_listing_owner_order_completed( $order_data['ID'] );
        //atbdp_email_admin_payment_received( $order_data['ID'] );
    }

} // ends class