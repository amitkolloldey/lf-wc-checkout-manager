<?php
/**
 * @package LF WooCommerce Checkout Manager
 */
/*
Plugin Name: LF WooCommerce Checkout Fields Manager
Plugin URI: http://devthemes.com
Author: Amit Kollol Dey
Author URI: http://amitkolloldey.com
Description: Edit WooCommerce Checkout Fields Manager plugin. Enable/Disable Any Checkout Fields Within Product
Categories or Tags
Version: 1.0.0
License: GPLv2 or later
Text Domain: lf-woocommerce-checkout-fields-manager
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
/**
 * Check If WooCommerce Is Active.
 */
$plugin = 'woocommerce/woocommerce.php';
if (
    !in_array($plugin, apply_filters('active_plugins', get_option('active_plugins', array()))) &&
    !(is_multisite() && array_key_exists($plugin, get_site_option('active_sitewide_plugins', array())))
) {
    return;
}


/**
 * Class LF_WC_Checkout_Manager
 */
class LF_WC_Checkout_Manager
{


    /**
     * @var string
     */
    public $version = '1.0.0';


    /**
     * @var null
     */
    protected static $_instance = null;


    /**
     * @return LF_WC_Checkout_Manager|null
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * LF_WC_Checkout_Manager constructor.
     */
    function __construct()
    {
        // Set up localisation
        load_plugin_textdomain('lf-woocommerce-checkout-fields-manager', false, dirname(plugin_basename(__FILE__)) . '/langs/');

        // Include required files
        $this->includes();

        // Settings & Scripts
        if (is_admin()) {
            add_filter('woocommerce_get_settings_pages', array($this, 'add_woocommerce_settings_tab'));
        }
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        if ($query['tab'] == 'lf_wc_checkout_manager'):
            function lf_wc_checkout_manager_enqueue()
            {
                wp_enqueue_style('lf_wccm_styles', plugin_dir_url(__FILE__) . 'assets/css/styles.css',
                    array(), false, 'all');
            }

            add_action('admin_enqueue_scripts', 'lf_wc_checkout_manager_enqueue');
        endif;

    }

    /**
     * Requiring Plugins Core Files
     */
    function includes()
    {
        // Core
        $this->core = require_once('includes/class-lf-wc-checkout-manager-core.php');
        // Settings
        require_once('includes/admin/class-lf-wc-checkout-manager-settings-section.php');
        require_once('includes/admin/class-lf-wc-checkout-manager-settings-field.php');
        $this->settings = array();
        $this->settings['general'] = require_once('includes/admin/class-lf-wc-checkout-manager-settings-general.php');
        foreach ($this->core->get_fields() as $field_id => $field_title) {
            $this->settings[$field_id] = new LF_WC_Checkout_Manager_Settings_Field($field_id, $field_title);
        }
        if (is_admin() && get_option('lf_wc_checkout_manager_version', '') !== $this->version) {
            foreach ($this->settings as $section) {
                foreach ($section->get_settings() as $value) {
                    if (isset($value['default']) && isset($value['id'])) {
                        $autoload = isset($value['autoload']) ? ( bool )$value['autoload'] : true;
                        add_option($value['id'], $value['default'], '', ($autoload ? 'yes' : 'no'));
                    }
                }
            }
            update_option('lf_wc_checkout_manager_version', $this->version);
        }
    }


    /**
     * @param $settings
     * @return array
     */
    function add_woocommerce_settings_tab($settings)
    {
        $settings[] = include('includes/admin/class-lf-wc-checkout-manager-settings.php');
        return $settings;
    }

    /**
     * @return string
     */
    function plugin_url()
    {
        return untrailingslashit(plugin_dir_url(__FILE__));
    }

    /**
     * @return string
     */
    function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(__FILE__));
    }

}

return new LF_WC_Checkout_Manager();