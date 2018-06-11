<?php
/**
 * @package LF WooCommerce Checkout Manager
 */
// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

/**
 * Class LF_WC_Checkout_Manager_Settings
 */
class LF_WC_Checkout_Manager_Settings extends WC_Settings_Page
{


    /**
     * LF_WC_Checkout_Manager_Settings constructor.
     */
    function __construct()
    {
        $this->id = 'lf_wc_checkout_manager';
        $this->label = __('LF WooCommerce Checkout Manager', 'lf-woocommerce-checkout-fields-manager');
        parent::__construct();
    }


    /**
     * @return array|mixed
     */
    function get_settings()
    {
        global $current_section;
        return apply_filters('woocommerce_get_settings_' . $this->id . '_' . $current_section, array());
    }

}

return new LF_WC_Checkout_Manager_Settings();