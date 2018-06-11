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
 * Class LF_WC_Checkout_Manager_Settings_General
 */
class LF_WC_Checkout_Manager_Settings_General extends LF_WC_Checkout_Manager_Fields_Settings_Section
{


    /**
     * LF_WC_Checkout_Manager_Settings_General constructor.
     */
    function __construct()
    {
        $this->id = '';
        $this->desc = __('General', 'lf-woocommerce-checkout-fields-manager');
        parent::__construct();
    }


    /**
     * @param $settings
     * @return array
     */
    function add_settings($settings)
    {

        $plugin_settings = array(
            array(
                'title' => __('LF WooCommerce Checkout Manager Options', 'lf-woocommerce-checkout-fields-manager'),
                'type' => 'title',
                'id' => 'lf_wc_checkout_manager_plugin_options',
            ),
            array(
                'title' => __('LF WooCommerce Checkout Manager for WooCommerce', 'lf-woocommerce-checkout-fields-manager'),
                'desc' => '<strong>' . __('Enable plugin', 'lf-woocommerce-checkout-fields-manager') . '</strong>',
                'desc_tip' => __('Customize WooCommerce Checkout fields.', 'lf-woocommerce-checkout-fields-manager'),
                'id' => 'lf_wc_checkout_manager_plugin_enabled',
                'default' => 'yes',
                'type' => 'checkbox',
            ),
            array(
                'type' => 'sectionend',
                'id' => 'lf_wc_checkout_manager_plugin_options',
            ),
        );


        return array_merge($plugin_settings, $settings);
    }

}

return new LF_WC_Checkout_Manager_Settings_General();