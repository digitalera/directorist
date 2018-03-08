<?php
/**
 * Offline Gateway
 *
 * @package       directorist
 * @subpackage    directorist/includes/gateways
 * @copyright     Copyright 2018. AazzTech
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GNU Public License
 * @since         3.1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * ATBDP_Offline_Gateway Class
 *
 * @since    3.1.0
 * @access   public
 */

class ATBDP_Offline_Gateway {

    public function __construct()
    {
        // add new settings
        add_filter('atbdp_monetization_settings_submenus', array($this, 'offline_gateway_settings_submenu'), 10, 1);

        // hook to process payment
    }


    /**
     * It adds a submenu of offline gateway settings
     * @param array $submenus
     * @return array
     */
    public function offline_gateway_settings_submenu($submenus)
    {
        $submenus['offline_gateway_submenu'] =  array(
            'title' => __('Offline Gateway', ATBDP_TEXTDOMAIN),
            'name' => 'offline_gateway_menu',
            'icon' => 'font-awesome:fa-university',
            'controls' => apply_filters('atbdp_offline_gateway_settings_controls', array(
                'gateways' => array(
                    'type' => 'section',
                    'title' => __('Offline Gateway Settings', ATBDP_TEXTDOMAIN),
                    'description' => __('You can customize all the settings related to your offline gateway. After switching any option, Do not forget to save the changes.', ATBDP_TEXTDOMAIN),
                    'fields' => $this->get_offline_gateway_settings_fields(),
                ),
            )),
        );
        return $submenus;
    }

    /**
     * It register the settings fields of offline gateway
     * @return array It returns an array of offline settings fields array
     */
    public function get_offline_gateway_settings_fields()
    {
        $bank_account = <<<KAMAL
Please make your payment directly to our bank account and use your ORDER ID as a Reference. Our bank account information is given below.

Account details :
------------------
Account Name : [Enter your Account Name]
Account Number : [Enter your Account Number]
Bank Name : [Enter your Bank Name]
------------------
Please remember that your order may be canceled if you do not make your payment within next 72 hours.
KAMAL;

        $bank_payment_desc = __('You can make your payment directly to our bank account using this gateway. Please use your ORDER ID as a reference when making the payment. We will complete your order as soon as your deposit is cleared in our bank.', ATBDP_TEXTDOMAIN);

        
        return apply_filters('atbdp_offline_gateway_settings_fields', array(
                array(
                    'type' => 'notebox',
                    'name' => 'offline_payment_note',
                    'label' => __('Note About Bank Transfer Gateway:', ATBDP_TEXTDOMAIN),
                    'description' => __('You should remember that this payment gateway needs some manual action to complete an order. After getting notification of order using this offline payment gateway, you should check your bank if the money is deposited to your account. Then you should change the order status manually from the "Order History" submenu.', ATBDP_TEXTDOMAIN),
                    'status' => 'info',
                ),

                array(
                    'type' => 'textbox',
                    'name' => 'bank_transfer_title',
                    'label' => __('Gateway Title', ATBDP_TEXTDOMAIN),
                    'description' => __('Enter the title of this gateway that should be displayed to the user on the front end.', ATBDP_TEXTDOMAIN),
                    'default' => 'Bank Transfer',
                ),

                array(
                    'type' => 'textarea',
                    'name' => 'bank_transfer_description',
                    'label' => __('Gateway Description', ATBDP_TEXTDOMAIN),
                    'description' => __('Enter some description for your user to transfer funds to your account.', ATBDP_TEXTDOMAIN),
                    'default' => $bank_payment_desc,
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'bank_information',
                    'label' => __('Bank Information', ATBDP_TEXTDOMAIN),
                    'description' => __('Enter your bank information below so that use can make payment directly to your bank account.', ATBDP_TEXTDOMAIN),
                    'default' => $bank_account,
                ),

            )
        );
    }
}
