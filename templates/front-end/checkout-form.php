<?php
/*Template for displaying Checkout form*/
// prepare all the variables required by the checkout page.
$form_data = !empty($args['form_data']) ? $args['form_data'] : array();
$listing_id = !empty($args['listing_id']) ? $args['listing_id'] : 0;
$c_position = get_directorist_option('payment_currency_position');
$currency = atbdp_get_payment_currency();
$symbol = atbdp_currency_symbol($currency);
//displaying data for checkout

?>
<div class="directorist directorist-checkout-form">
    <?php do_action('atbdp_before_checkout_form_start'); ?>
    <form id="atbdp-checkout-form" class="form-vertical" method="post" action="" role="form">
        <?php do_action('atbdp_after_checkout_form_start'); ?>
        <p><?php esc_html_e('Your order details are given below. Please review it and click on Proceed to Payment to complete this order.', ATBDP_TEXTDOMAIN); ?></p>
        <div class="table-responsive">


            <table id="directorist-checkout-table" class="table table-bordered table-striped">
                <?php
                // $args is auto available available through the load_template().
                foreach ($form_data as $op){
                    /*Display header type item in a bold style*/
                    if ('header' == $op['type']){ ?>
                        <tr>
                            <td colspan="2">
                                <h3 class="no-margin"><?php if( !empty( $op['title'] ) ) echo esc_html($op['title']); ?></h3>
                                <?php if( !empty( $op['desc'] ) ) echo $op['desc']; ?>
                            </td>
                            <td><strong><?php printf(__('Price [%s]', ATBDP_TEXTDOMAIN), $currency); ?></strong></td>
                        </tr>
                    <?php } else { /*Display other type of item here*/ ?>
                        <tr>
                            <td>
                                <?php
                                /*display proper type of checkbox/radio etc*/
                                $checked = isset($op['selected']) ? checked(1, $op['selected'], false): '';
                                $input_field = sprintf( '<input type="checkbox" name="%s" class="atbdp_checkout_item_field" value="%s" data-price="%s" %s/>', esc_attr($op['name']), esc_attr($op['value']), esc_attr($op['price']), $checked );

                               echo str_replace('checkbox', $op['type'], $input_field);
                                ?>
                            </td>

                            <td>
                                <?php if(!empty($op['title'])) echo "<h4>".esc_html($op['title'])."</h4>"; ?>
                                <?php if(!empty($op['desc'])) echo esc_html($op['desc']) ; ?>
                            </td>

                            <td align="right" class="text-right">
                                <?php if( !empty( $op['price'] ) ){
                                    $before = ''; $after = '';
                                     ('after' == $c_position) ? $after = $symbol : $before = $symbol;
                                     echo $before.esc_html(atbdp_format_payment_amount($op['price'])).$after;
                                }?>
                            </td>
                        </tr>
                    <?php }
                } ?>
                <tr>
                    <td colspan="2" class="text-right vertical-middle">
                        <strong><?php printf( __( 'Total amount [%s]', ATBDP_TEXTDOMAIN ), $currency ); ?></strong>
                    </td>
                    <td class="text-right vertical-middle">
                        <div id="atbdp_checkout_total_amount"></div><!--total amount will be populated by JS-->
                    </td>
                </tr>



            </table>

            <div id="directorist_payment_gateways" class="panel panel-default">
                <div class="panel-heading"><?php esc_html_e( 'Choose a payment method', ATBDP_TEXTDOMAIN ); ?></div>
                <?php echo ATBDP_Gateway::gateways_markup(); ?>
            </div>

            <p id="atbdp_checkout_errors" class="text-danger"></p>

            <?php wp_nonce_field( 'checkout_action', 'checkout_nonce' ); ?>
            <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>" />
            <div class="pull-right">
                <a href="<?php echo ATBDP_Permalink::get_dashboard_page_link(); ?>" class="btn btn-default"><?php _e( 'Not Now', ATBDP_TEXTDOMAIN ); ?></a>
                <input type="submit" id="atbdp_checkout_submit_btn" class="btn btn-primary" value="<?php _e( 'Pay Now', ATBDP_TEXTDOMAIN ); ?>" />
            </div>
        <?php do_action('atbdp_before_checkout_form_end'); ?>
    </form>
    <?php do_action('atbdp_after_checkout_form_end'); ?>


</div>