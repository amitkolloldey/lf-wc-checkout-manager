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
 * Class LF_WC_Checkout_Manager_Settings_Field
 */
class LF_WC_Checkout_Manager_Settings_Field extends LF_WC_Checkout_Manager_Fields_Settings_Section
{


    /**
     * LF_WC_Checkout_Manager_Settings_Field constructor.
     * @param $id
     * @param $title
     */
    function __construct($id, $title)
    {
        $this->id = $id;
        $this->desc = $title;
        parent::__construct();
    }

    /**
     * @param $args
     * @return array
     */
    function get_terms($args)
    {
        if (!is_array($args)) {
            $_taxonomy = $args;
            $args = array(
                'taxonomy' => $_taxonomy,
                'orderby' => 'name',
                'hide_empty' => false,
            );
        }
        global $wp_version;
        if (version_compare($wp_version, '4.5.0', '>=')) {
            $_terms = get_terms($args);
        } else {
            $_taxonomy = $args['taxonomy'];
            unset($args['taxonomy']);
            $_terms = get_terms($_taxonomy, $args);
        }
        $_terms_options = array();
        if (!empty($_terms) && !is_wp_error($_terms)) {
            foreach ($_terms as $_term) {
                $_terms_options[$_term->term_id] = $_term->name;
            }
        }
        return $_terms_options;
    }

    /**
     * @param $settings
     * @return array
     */
    function add_settings($settings)
    {

        $product_cats = $this->get_terms('product_cat');
        $product_tags = $this->get_terms('product_tag');
        $field = $this->id;
        $fields_settings = array(
            array(
                'title' => $this->desc,
                'type' => 'title',
                'id' => 'lf_wc_checkout_manager_field_general_options',
            ),
            array(
                'title' => __('Enabled', 'lf-woocommerce-checkout-fields-manager'),
                'id' => "lf_wc_checkout_manager_field_enabled[{$field}]",
                'default' => 'default',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'options' => array(
                    'default' => __('Default', 'lf-woocommerce-checkout-fields-manager'),
                    'yes' => __('Enabled', 'lf-woocommerce-checkout-fields-manager'),
                    'no' => __('Disabled', 'lf-woocommerce-checkout-fields-manager'),
                ),
            ),
            array(
                'title' => __('Required', 'lf-woocommerce-checkout-fields-manager'),
                'id' => "lf_wc_checkout_manager_field_required[{$field}]",
                'default' => 'default',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'options' => array(
                    'default' => __('Default', 'lf-woocommerce-checkout-fields-manager'),
                    'yes' => __('Required', 'lf-woocommerce-checkout-fields-manager'),
                    'no' => __('Not required', 'lf-woocommerce-checkout-fields-manager'),
                ),
            ),
            array(
                'title' => __('Label', 'lf-woocommerce-checkout-fields-manager'),
                'desc_tip' => __('Leave blank for WooCommerce defaults.', 'lf-woocommerce-checkout-fields-manager'),
                'id' => "lf_wc_checkout_manager_field_label[{$field}]",
                'default' => '',
                'type' => 'text',
            ),
            array(
                'title' => __('Placeholder', 'lf-woocommerce-checkout-fields-manager'),
                'desc_tip' => __('Leave blank for WooCommerce defaults.', 'lf-woocommerce-checkout-fields-manager'),
                'id' => "lf_wc_checkout_manager_field_placeholder[{$field}]",
                'default' => '',
                'type' => 'text',
            ),
            array(
                'title' => __('Description', 'lf-woocommerce-checkout-fields-manager'),
                'desc_tip' => __('Leave blank for WooCommerce defaults.', 'lf-woocommerce-checkout-fields-manager'),
                'id' => "lf_wc_checkout_manager_field_description[{$field}]",
                'default' => '',
                'type' => 'text',
            ),
            array(
                'title' => __('Class', 'lf-woocommerce-checkout-fields-manager'),
                'id' => "lf_wc_checkout_manager_field_class[{$field}]",
                'default' => 'default',
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'options' => array(
                    'default' => __('Default', 'lf-woocommerce-checkout-fields-manager'),
                    'form-row-first' => __('Align left', 'lf-woocommerce-checkout-fields-manager'),
                    'form-row-last' => __('Align right', 'lf-woocommerce-checkout-fields-manager'),
                    'form-row-full' => __('Full row', 'lf-woocommerce-checkout-fields-manager'),
                ),
            ),
            array(
                'title' => __('Priority', 'lf-woocommerce-checkout-fields-manager'),
                'desc_tip' => __('Leave zero for WooCommerce defaults.', 'lf-woocommerce-checkout-fields-manager'),
                'desc' => apply_filters('lf_wc_checkout_manager_option', __('Set The Order Of This Field.', 'lf-woocommerce-checkout-fields-manager'), 'settings'),
                'id' => "lf_wc_checkout_manager_field_priority[{$field}]",
                'default' => 0,
                'type' => 'number',
                'custom_attributes' => apply_filters('lf_wc_checkout_manager_option', array('enabled' => 'enabled'), 'settings'),
            ),
            array(
                'type' => 'sectionend',
                'id' => 'lf_wc_checkout_manager_field_general_options',
            ),
            array(
                'title' => __('Product Visibility', 'lf-woocommerce-checkout-fields-manager'),
                'type' => 'title',
                'id' => 'lf_wc_checkout_manager_field_product_visibility_options',
            ),
            array(
                'title' => __('Include product categories', 'lf-woocommerce-checkout-fields-manager'),
                'desc_tip' => __('If not empty - selected categories products must be in the cart for current field to appear.', 'lf-woocommerce-checkout-fields-manager'),
                'desc' => apply_filters('lf_wc_checkout_manager_option', __('Set Product Categories Where The Settings Will Be Applied.',
                    'lf-woocommerce-checkout-fields-manager'), 'settings'),
                'id' => "lf_wc_checkout_manager_field_cats_incl[{$field}]",
                'default' => '',
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'options' => $product_cats,
                'custom_attributes' => apply_filters('lf_wc_checkout_manager_option', array('enabled' => 'enabled'
                ), 'settings'),
            ),
            array(
                'title' => __('Exclude product categories', 'lf-woocommerce-checkout-fields-manager'),
                'desc_tip' => __('If not empty - current field is hidden, if selected categories products are in the cart.', 'lf-woocommerce-checkout-fields-manager'),
                'desc' => apply_filters('lf_wc_checkout_manager_option', __('Exclude Product Categories Where The Settings Will Not Be Applied.',
                    'lf-woocommerce-checkout-fields-manager'), 'settings'),
                'id' => "lf_wc_checkout_manager_field_cats_excl[{$field}]",
                'default' => '',
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'options' => $product_cats,
                'custom_attributes' => apply_filters('lf_wc_checkout_manager_option', array('enabled' => 'enabled'), 'settings'),
            ),
            array(
                'title' => __('Include product tags', 'lf-woocommerce-checkout-fields-manager'),
                'desc_tip' => __('If not empty - selected tags products must be in the cart for current field to appear.', 'lf-woocommerce-checkout-fields-manager'),
                'desc' => apply_filters('lf_wc_checkout_manager_option', __('Set Product Tags Where The Settings Will Be Applied.',
                    'lf-woocommerce-checkout-fields-manager'), 'settings'),
                'id' => "lf_wc_checkout_manager_field_tags_incl[{$field}]",
                'default' => '',
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'options' => $product_tags,
                'custom_attributes' => apply_filters('lf_wc_checkout_manager_option', array('enabled' => 'enabled'), 'settings'),
            ),
            array(
                'title' => __('Exclude product tags', 'lf-woocommerce-checkout-fields-manager'),
                'desc_tip' => __('If not empty - current field is hidden, if selected tags products are in the cart.', 'lf-woocommerce-checkout-fields-manager'),
                'desc' => apply_filters('lf_wc_checkout_manager_option', __('Exclude Product Categories Where The Settings Will Not Be Applied.',
                    'lf-woocommerce-checkout-fields-manager'), 'settings'),
                'id' => "lf_wc_checkout_manager_field_tags_excl[{$field}]",
                'default' => '',
                'type' => 'multiselect',
                'class' => 'chosen_select',
                'options' => $product_tags,
                'custom_attributes' => apply_filters('lf_wc_checkout_manager_option', array('enabled' => 'enabled'), 'settings'),
            ),
            array(
                'type' => 'sectionend',
                'id' => 'lf_wc_checkout_manager_field_product_visibility_options',
            ),
        );

        return array_merge($fields_settings, $settings);
    }

}