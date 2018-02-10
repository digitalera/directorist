<?php
/**
 * Orders
 *
 * @package       directorist
 * @subpackage    directorist/includes/orders
 * @copyright     Copyright 2018. AazzTech
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GNU Public License
 * @since         3.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * ATBDP_Order Class
 *
 * @since    1.0.0
 * @access   public
 */
class ATBDP_Order {

    /**
     * Register a custom post type "atbdp_orders".
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_custom_post_type() {

        $labels = array(
            'name'                => _x( 'Order History', 'Post Type General Name', ATBDP_TEXTDOMAIN ),
            'singular_name'       => _x( 'Order', 'Post Type Singular Name', ATBDP_TEXTDOMAIN ),
            'menu_name'           => __( 'Order History', ATBDP_TEXTDOMAIN ),
            'name_admin_bar'      => __( 'Order', ATBDP_TEXTDOMAIN ),
            'all_items'           => __( 'Order History', ATBDP_TEXTDOMAIN ),
            'add_new_item'        => __( 'Add New Order', ATBDP_TEXTDOMAIN ),
            'add_new'             => __( 'Add New', ATBDP_TEXTDOMAIN ),
            'new_item'            => __( 'New Order', ATBDP_TEXTDOMAIN ),
            'edit_item'           => __( 'Edit Order', ATBDP_TEXTDOMAIN ),
            'update_item'         => __( 'Update Order', ATBDP_TEXTDOMAIN ),
            'view_item'           => __( 'View Order', ATBDP_TEXTDOMAIN ),
            'search_items'        => __( 'Search Order', ATBDP_TEXTDOMAIN ),
            'not_found'           => __( 'No orders found', ATBDP_TEXTDOMAIN ),
            'not_found_in_trash'  => __( 'No orders found in Trash', ATBDP_TEXTDOMAIN ),
        );

        $args = array(
            'label'               => __( 'Orders', ATBDP_TEXTDOMAIN ),
            'description'         => __( 'Post Type Description', ATBDP_TEXTDOMAIN ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'author', ),
            'taxonomies'          => array( '' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type='.ATBDP_POST_TYPE,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'atbdp_order',
            'map_meta_cap'        => true,
        );

        register_post_type( 'atbdp_orders', $args );

    }

    /**
     * Add/Remove custom bulk actions to the select menus.
     *
     * @since    1.0.0
     * @access   public
     */
    public function admin_footer_edit() {

        global $post_type;
        if( 'atbdp_orders' == $post_type ) {
            ?>
            <script type="text/javascript">
                var acadp_bulk_actions = <?php echo json_encode( acadp_get_payment_bulk_actions() ); ?>;
                //console.log(acadp_bulk_actions);

                jQuery(document).ready(function() {
                    for( var key in acadp_bulk_actions ) {
                        if( acadp_bulk_actions.hasOwnProperty( key ) ) {
                            jQuery('<option>').val( key ).text( acadp_bulk_actions[ key ] ).appendTo('select[name="action"]');
                            jQuery('<option>').val( key ).text( acadp_bulk_actions[ key ] ).appendTo('select[name="action2"]');
                        }
                    }

                    jQuery('select[name="action"]').find('option[value="edit"]').remove();
                    jQuery('select[name="action2"]').find('option[value="edit"]').remove();
                });
            </script>
            <?php
        }
    }


    /**
     * Add custom filter options.
     *
     * @since    1.0.0
     * @access   public
     */
    public function restrict_manage_posts() {

        global $typenow, $wp_query;

        if( 'atbdp_orders' == $typenow ) {

            // Restrict by payment status
            $statuses = acadp_get_payment_statuses();
            $current_status = isset( $_GET['payment_status'] ) ? $_GET['payment_status'] : '';

            echo '<select name="payment_status">';
            echo '<option value="all">'.__( "All orders", ATBDP_TEXTDOMAIN ).'</option>';
            foreach( $statuses as $value => $title ) {
                printf( '<option value="%s"%s>%s</option>', $value, ( $value == $current_status ? ' selected="selected"' : '' ), $title );
            }
            echo '</select>';

        }

    }

    /**
     * Parse a query string and enable filter by post meta "payment_status"
     *
     * @since    1.0.0
     * @access   public
     *
     * @param	 WP_Query    $query    WordPress Query object
     */
    public function parse_query( $query ) {

        global $pagenow, $post_type;

        if( 'edit.php' == $pagenow && 'atbdp_orders' == $post_type ) {

            // Filter by post meta "payment_status"
            if( isset( $_GET['payment_status'] ) && $_GET['payment_status'] != '' ) {
                $query->query_vars['meta_key'] = 'payment_status';
                $query->query_vars['meta_value'] = sanitize_key( $_GET['payment_status'] );
            }

        }

    }

    /**
     * Retrieve the table columns.
     *
     * @since    1.0.0
     * @access   public
     *
     * @return   array    $columns    Array of all the list table columns.
     */
    public function get_columns() {

        $columns = array(
            'cb'             => '<input type="checkbox" />', // Render a checkbox instead of text
            'ID'             => __( 'Order ID', ATBDP_TEXTDOMAIN ),
            'details'        => __( 'Details', ATBDP_TEXTDOMAIN ),
            'amount'         => __( 'Amount', ATBDP_TEXTDOMAIN ),
            'type'           => __( 'Payment Type', ATBDP_TEXTDOMAIN ),
            'transaction_id' => __( 'Transaction ID', ATBDP_TEXTDOMAIN ),
            'customer'       => __( 'Customer', ATBDP_TEXTDOMAIN ),
            'date'           => __( 'Date', ATBDP_TEXTDOMAIN ),
            'status'         => __( 'Status', ATBDP_TEXTDOMAIN )
        );

        return $columns;

    }

    /**
     * This function renders the custom columns in the list table.
     *
     * @since    1.0.0
     * @access   public
     *
     * @param    string    $column    The name of the column.
     * @param    string    $post_id   Post ID.
     */
    public function custom_column_content( $column, $post_id ) {

        global $post;

        switch ( $column ) {
            case 'ID' :
                printf( '<a href="%s" target="_blank">%d</a>', acadp_get_payment_receipt_page_link( $post_id ), $post_id );
                break;
            case 'details' :
                $listing_id = get_post_meta( $post_id, 'listing_id', true );
                printf( '<p><a href="%s">%s:%d</a></p>', get_edit_post_link( $listing_id ), get_the_title( $listing_id ),  $listing_id );

                $order_details = apply_filters( 'acadp_order_details', array(), $post_id );
                foreach( $order_details as $order_detail ) {
                    echo '<div># '.$order_detail['label'].'</div>';
                }

                $featured = get_post_meta( $post_id, 'featured', true );
                if( $featured ) {
                    $featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
                    echo '<div># '.$featured_listing_settings['label'].'</div>';
                }
                break;
            case 'amount' :
                $amount = get_post_meta( $post_id, 'amount', true );
                $amount = acadp_format_payment_amount( $amount );

                $value = atbdp_order_currency_filter( $amount );
                echo $value;
                break;
            case 'type' :
                $gateway = get_post_meta( $post_id, 'payment_gateway', true );
                if( 'free' == $gateway ) {
                    _e( 'Free Submission', ATBDP_TEXTDOMAIN );
                } else {
                    $gateway_settings = get_option( 'acadp_gateway_'.$gateway.'_settings' );
                    echo ! empty( $gateway_settings['label'] ) ? $gateway_settings['label'] : $gateway;
                }
                break;
            case 'transaction_id' :
                echo get_post_meta( $post_id, 'transaction_id', true );
                break;
            case 'customer' :
                $user_info = get_userdata( $post->post_author );

                printf( '<p><a href="%s">%s</a></p>', get_edit_user_link( $user_info->ID ), $user_info->display_name );
                echo $user_info->user_email;
                break;
            case 'date' :
                $date = strtotime( $post->post_date );
                $value = date_i18n( get_option( 'date_format' ), $date );

                echo $value;
                break;
            case 'status' :
                $value = get_post_meta( $post_id, 'payment_status', true );
                echo acadp_get_payment_status_i18n( $value );
                break;
        }

    }

    /**
     * Retrieve the table's sortable columns.
     *
     * @since    1.0.0
     * @access   public
     *
     * @return   array    Array of all the sortable columns
     */
    public function get_sortable_columns() {

        $columns = array(
            'ID' 	 => 'ID',
            'amount' => 'amount',
            'date' 	 => 'date'
        );

        return $columns;

    }

    /**
     * Called only in /wp-admin/edit.php* pages.
     *
     * @since    1.0.0
     * @access   public
     */
    public function load_edit() {

        // Handle the custom bulk action
        global $typenow;
        $post_type = $typenow;

        if( 'atbdp_orders' == $typenow ) {

            // Get the action
            $wp_list_table = _get_list_table('WP_Posts_List_Table');
            $action = $wp_list_table->current_action();

            $allowed_actions = array_keys( acadp_get_payment_bulk_actions() );
            if( ! in_array( $action, $allowed_actions ) ) return;

            // Security check
            check_admin_referer('bulk-posts');

            // Make sure ids are submitted
            if( isset( $_REQUEST['post'] ) ) {
                $post_ids = array_map( 'intval', $_REQUEST['post'] );
            }

            if( empty( $post_ids ) ) return;

            // This is based on wp-admin/edit.php
            $sendback = remove_query_arg( array_merge( $allowed_actions, array( 'untrashed', 'deleted', 'ids' ) ), wp_get_referer() );
            if( ! $sendback ) $sendback = admin_url( "edit.php?post_type=$post_type" );

            $pagenum = $wp_list_table->get_pagenum();
            $sendback = add_query_arg( 'paged', $pagenum, $sendback );

            $modified = 0;
            foreach( $post_ids as $post_id ) {
                if( ! $this->update_payment_status( $action, $post_id ) )	wp_die( __( 'Error updating post.', ATBDP_TEXTDOMAIN ) );
                $modified++;
            }

            $sendback = add_query_arg( array( $action => $modified, 'ids' => join( ',', $post_ids ) ), $sendback );
            $sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view' ), $sendback );

        }

        // Add filter to sort columns
        add_filter( 'request', array( $this, 'sort_columns' ) );

    }

    /**
     * Update payment status.
     *
     * @since    1.0.0
     * @access   public
     *
     * @param    string    $action    Action to be performed.
     * @param    int       $post_id   Post ID.
     * @return 	 boolean              If the save was successful or not.
     */
    public function update_payment_status( $action, $post_id ) {

        $old_status = get_post_meta( $post_id, 'payment_status', true );

        $new_status = str_replace( 'set_to_', '', $action );
        $new_status = sanitize_key( $new_status );

        if( $new_status == $old_status ) return true;

        do_action( 'acadp_order_status_changed', $new_status, $old_status, $post_id );

        $non_complete_statuses = array( 'created', 'pending', 'failed', 'cancelled', 'refunded' );

        // If the order has featured
        $featured = get_post_meta( $post_id, 'featured', true );

        if( ! empty( $featured ) ) {
            $listing_id = get_post_meta( $post_id, 'listing_id', true );

            if( 'completed' == $old_status && in_array( $new_status, $non_complete_statuses ) ) {
                update_post_meta( $listing_id, 'featured', 0 );
            } else if( in_array( $old_status, $non_complete_statuses ) && 'completed' == $new_status ) {
                update_post_meta( $listing_id, 'featured', 1 );
            }
        }

        // Update new status
        update_post_meta( $post_id, 'payment_status', $new_status );

        // Email listing owner when his/her set to completed
        if( in_array( $old_status, $non_complete_statuses ) && 'completed' == $new_status ) {
            acadp_email_listing_owner_order_completed( $post_id );
        }

        return true;

    }

    /**
     * Display an admin notice on the payment history page after performing
     * a bulk action.
     *
     * @since    1.0.0
     * @access   public
     */
    public function admin_notices() {

        global $pagenow, $post_type;

        if( 'edit.php' == $pagenow && 'atbdp_orders' == $post_type ) {

            $message = '';
            $allowed_actions = array_keys( acadp_get_payment_bulk_actions() );

            foreach( $allowed_actions as $action ) {
                $_action = str_replace( 'set_to_', '', $action );
                if( isset( $_REQUEST[ $action ] ) && (int) $_REQUEST[ $action ] ) {
                    $message = sprintf( _n( "Order set to $_action.", "%s orders set to $_action.", $_REQUEST[ $action ], ATBDP_TEXTDOMAIN ), number_format_i18n( $_REQUEST[ $action ] ) );
                    break;
                }
            }

            $class = "updated";
            if( ! empty( $message ) ) echo "<div class=\"$class\"> <p>$message</p></div>";

        }

    }

    /**
     * Sort custom columns.
     *
     * @since    1.0.0
     * @access   public
     *
     * @param    array    $vars    Array of query variables.
     * @return array
     */
    public function sort_columns( $vars ) {

        // Check if we're viewing the 'atbdp_orders' post type
        if( isset( $vars['post_type'] ) && 'atbdp_orders' == $vars['post_type'] ) {

            // Check if 'orderby' is set to 'amount'
            if ( isset( $vars['orderby'] ) && 'amount' == $vars['orderby'] ) {

                // Merge the query vars with our custom variables.
                $vars = array_merge(
                    $vars,
                    array(
                        'meta_key' => 'amount',
                        'orderby'  => 'meta_value_num'
                    )
                );

            }

        }

        return $vars;

    }

}