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
 * Class LF_WC_Checkout_Manager_Fields_Settings_Section
 */
class LF_WC_Checkout_Manager_Fields_Settings_Section
{
    /**
     * LF_WC_Checkout_Manager_Fields_Settings_Section constructor.
     */
    function __construct()
    {
        add_filter('woocommerce_get_sections_lf_wc_checkout_manager', array($this, 'settings_section'));
        add_filter('woocommerce_get_settings_lf_wc_checkout_manager_' . $this->id, array($this, 'get_settings'), PHP_INT_MAX);
        add_action('init', array($this, 'add_settings_hook'));
    }


    /**
     * Register WC Settings Tab
     */
    function add_settings_hook()
    {
        add_filter('lf_wc_checkout_manager_settings_' . $this->id, array($this, 'add_settings'));
    }


    /**
     * @return array
     */
    function get_settings()
    {

        return array_merge(apply_filters('lf_wc_checkout_manager_settings_' . $this->id, array()), array());
    }


    /**
     * @param $sections
     * @return mixed
     */
    function settings_section($sections)
    {

        $sections[$this->id] = $this->desc;
        return $sections;
    }

}
