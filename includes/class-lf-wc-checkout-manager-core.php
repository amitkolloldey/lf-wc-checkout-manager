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
 * Class LF_WC_Checkout_Manager_Fields_Core
 */
class LF_WC_Checkout_Manager_Fields_Core
{


    /**
     * LF_WC_Checkout_Manager_Fields_Core constructor.
     */
    function __construct()
    {
        if ('yes' === get_option('lf_wc_checkout_manager_plugin_enabled', 'yes')) {
            add_filter('woocommerce_checkout_fields', array($this, 'customize_checkout_fields'), 9999);
        }
    }


    /**
     * @return array
     */
    function get_fields()
    {
        return array(
            'billing_country' => __('Billing country', 'lf-woocommerce-checkout-fields-manager'),
            'billing_first_name' => __('Billing first name', 'lf-woocommerce-checkout-fields-manager'),
            'billing_last_name' => __('Billing last name', 'lf-woocommerce-checkout-fields-manager'),
            'billing_company' => __('Billing company', 'lf-woocommerce-checkout-fields-manager'),
            'billing_address_1' => __('Billing address', 'lf-woocommerce-checkout-fields-manager'),
            'billing_address_2' => __('Billing address 2', 'lf-woocommerce-checkout-fields-manager'),
            'billing_city' => __('Billing city', 'lf-woocommerce-checkout-fields-manager'),
            'billing_state' => __('Billing state', 'lf-woocommerce-checkout-fields-manager'),
            'billing_postcode' => __('Billing postcode', 'lf-woocommerce-checkout-fields-manager'),
            'billing_email' => __('Billing email', 'lf-woocommerce-checkout-fields-manager'),
            'billing_phone' => __('Billing phone', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_country' => __('Shipping country', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_first_name' => __('Shipping first name', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_last_name' => __('Shipping last name', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_company' => __('Shipping company', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_address_1' => __('Shipping address', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_address_2' => __('Shipping address 2', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_city' => __('Shipping city', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_state' => __('Shipping state', 'lf-woocommerce-checkout-fields-manager'),
            'shipping_postcode' => __('Shipping postcode', 'lf-woocommerce-checkout-fields-manager'),
            'account_username' => __('Account username', 'lf-woocommerce-checkout-fields-manager'),
            'account_password' => __('Account password', 'lf-woocommerce-checkout-fields-manager'),
            'account_password-2' => __('Account password 2', 'lf-woocommerce-checkout-fields-manager'),
            'order_comments' => __('Order comments', 'lf-woocommerce-checkout-fields-manager'),
        );
    }


    /**
     * @return mixed
     */
    function get_data_options()
    {
        return apply_filters('lf_wc_checkout_manager_option', array(
            'required' => 'default',
            'label' => '',
            'placeholder' => '',
            'description' => '',
            'class' => 'default',
        ), 'data_options');
    }


    /**
     * @return mixed
     */
    function get_visibility_options()
    {
        return apply_filters('lf_wc_checkout_manager_option', array(
            'enabled' => 'default',
        ), 'visibility_options');
    }


    /**
     * @return array
     */
    function get_options()
    {
        return array_merge($this->get_visibility_options(), $this->get_data_options());
    }


    /**
     * @return array
     */
    function get_fields_data()
    {
        if (isset($this->fields_data)) {
            return $this->fields_data;
        }
        $options_data = array();
        foreach ($this->get_options() as $option => $default) {
            $options_data[$option] = get_option('lf_wc_checkout_manager_field_' . $option, array());
        }
        $this->fields_data = array();
        foreach ($this->get_fields() as $field_id => $field_title) {
            foreach ($this->get_options() as $option => $default) {
                $this->fields_data[$field_id][$option] = (isset($options_data[$option][$field_id]) ? $options_data[$option][$field_id] : $default);
            }
        }
        return $this->fields_data;
    }

    /**
     * @param $field
     * @param $field_data
     */
    function set_field_data_options(&$field, $field_data)
    {
        foreach ($this->get_data_options() as $option_id => $option_default) {
            if ($option_default != $field_data[$option_id]) {
                if ('required' === $option_id) {
                    $field[$option_id] = ('yes' === $field_data[$option_id]);
                } elseif ('class' === $option_id) {
                    $field[$option_id] = array($field_data[$option_id]);
                } else {
                    $field[$option_id] = $field_data[$option_id];
                }
            }
        }
    }

    /**
     * @param $field_id
     * @return mixed|string
     */
    function get_field_section($field_id)
    {
        $field_parts = explode('_', $field_id, 2);
        return (!empty($field_parts) && is_array($field_parts) ? $field_parts[0] : '');
    }


    /**
     * @param $field_data
     * @return bool
     */
    function is_enabled_and_visible($field_data)
    {
        return ('no' === $field_data['enabled'] || !$this->is_visible(array(
            'include_products' => '',
            'exclude_products' => '',
            'include_categories' => (isset($field_data['cats_incl']) ? $field_data['cats_incl'] : ''),
            'exclude_categories' => (isset($field_data['cats_excl']) ? $field_data['cats_excl'] : ''),
            'include_tags' => (isset($field_data['tags_incl']) ? $field_data['tags_incl'] : ''),
            'exclude_tags' => (isset($field_data['tags_excl']) ? $field_data['tags_excl'] : ''),
        )) ? false : true);
    }

    /**
     * @param $checkout_fields
     * @return mixed
     */
    function customize_checkout_fields($checkout_fields)
    {
        foreach ($this->get_fields_data() as $field_id => $field_data) {
            $section = $this->get_field_section($field_id);
            if (!isset($checkout_fields[$section][$field_id])) {
                continue;
            } elseif (!$this->is_enabled_and_visible($field_data)) {
                unset($checkout_fields[$section][$field_id]);
            } else {
                $this->set_field_data_options($checkout_fields[$section][$field_id], $field_data);
            }
        }
        if ('yes' === get_option('lf_wc_checkout_manager_force_sort_by_priority', 'no')) {
            $field_sets = array('billing', 'shipping', 'account', 'order');
            foreach ($field_sets as $field_set) {
                if (isset($checkout_fields[$field_set])) {
                    uasort($checkout_fields[$field_set], array($this, 'sort_by_priority'));
                }
            }
        }
        return $checkout_fields;
    }


    /**
     * @param $product_id
     * @param $term_ids
     * @param $taxonomy
     * @return bool
     */
    function is_product_term($product_id, $term_ids, $taxonomy)
    {
        if (empty($term_ids)) {
            return false;
        }
        $product_terms = get_the_terms($product_id, $taxonomy);
        if (empty($product_terms)) {
            return false;
        }
        foreach ($product_terms as $product_term) {
            if (in_array($product_term->term_id, $term_ids)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $product_id
     * @param $args
     * @return bool
     */
    function is_enabled_for_product($product_id, $args)
    {
        if (isset($args['include_products']) && !empty($args['include_products'])) {
            if (!is_array($args['include_products'])) {
                $args['include_products'] = array_map('trim', explode(',', $args['include_products']));
            }
            if (!in_array($product_id, $args['include_products'])) {
                return false;
            }
        }
        if (isset($args['exclude_products']) && !empty($args['exclude_products'])) {
            if (!is_array($args['exclude_products'])) {
                $args['exclude_products'] = array_map('trim', explode(',', $args['exclude_products']));
            }
            if (in_array($product_id, $args['exclude_products'])) {
                return false;
            }
        }
        if (isset($args['include_categories']) && !empty($args['include_categories'])) {
            if (!$this->is_product_term($product_id, $args['include_categories'], 'product_cat')) {
                return false;
            }
        }
        if (isset($args['exclude_categories']) && !empty($args['exclude_categories'])) {
            if ($this->is_product_term($product_id, $args['exclude_categories'], 'product_cat')) {
                return false;
            }
        }
        if (isset($args['include_tags']) && !empty($args['include_tags'])) {
            if (!$this->is_product_term($product_id, $args['include_tags'], 'product_tag')) {
                return false;
            }
        }
        if (isset($args['exclude_tags']) && !empty($args['exclude_tags'])) {
            if ($this->is_product_term($product_id, $args['exclude_tags'], 'product_tag')) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $args
     * @return bool
     */
    function is_visible($args)
    {
        foreach ($args as $arg) {
            if (!empty($arg)) {
                // At least one arg is filled - checking products in cart
                if (!isset($this->cart_product_ids)) {
                    $this->cart_product_ids = array();
                    foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                        $this->cart_product_ids[] = $values['product_id'];
                    }
                }
                foreach ($this->cart_product_ids as $product_id) {
                    if (!$this->is_enabled_for_product($product_id, $args)) {
                        return false;
                    }
                }
                break;
            }
        }
        return true;
    }


    /**
     * @param $a
     * @param $b
     * @return int
     */
    function sort_by_priority($a, $b)
    {
        $a = (isset($a['priority']) ? $a['priority'] : 0);
        $b = (isset($b['priority']) ? $b['priority'] : 0);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b ? -1 : 1);
    }

}


return new LF_WC_Checkout_Manager_Fields_Core();