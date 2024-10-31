<?php


class Wp_Pargo_Shipping_Method extends WC_Shipping_Method
{
    public const PARGO_PROCESS_TYPE_W2P = "W2P";
    public const PARGO_PROCESS_TYPE_W2D = "W2D";

    public const PARGO_API_ENDPOINTS = [
        "staging" => "https://api.staging.pargo.co.za",
        "production" => "https://api.pargo.co.za"
    ];

    /**
     * Constructor for your shipping class
     *
     * @access public
     * @return void
     */
    public function __construct($instance_id = 0)
    {
        $this->id = 'wp_pargo';
        $this->method_title = __('Pargo', 'woocommerce');
        $this->method_description = __('Shipping Method for Pargo', 'woocommerce');
        // Availability & Countries
        $this->availability = 'including';
        $this->countries = array(
            'ZA', //South Africa
        );
        //Woocommerce 3 support
        $this->instance_id = absint($instance_id);
        $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
        $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Pargo', 'woocommerce');

        $this->supports = array(
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
            'settings'
        );

        $this->init_instance_settings();
        $this->init();
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    public function init()
    {
        // Load the settings API
        $this->init_form_fields();
        $this->init_settings();

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));
    }

    /**
     * Define settings field for Pargo shipping
     * @return void
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'pargo_enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable Pargo', 'woocommerce'),
                'default' => 'yes'
            ),
            'pargo_description' => array(
                'title' => __('Method Description', 'woocommerce'),
                'type' => 'text',
                'description' => __('This controls the description next to the Pargo delivery method.', 'woocommerce'),
                'default' => __(
                    'Pargo: Parcels on the go!',
                    'woocommerce'
                ),
            ),
            'pargo_use_api' => array(
                'title' => __('Use backend shipment', 'woocommerce'),
                'type' => 'checkbox',
            ),
            'pargo_buttoncaption' => array(
                'title' => __('Pickup Button Caption Before Pickup Point Selection', 'woocommerce'),
                'type' => 'text',
                'description' => __(
                    'Sets the caption of the button that allows users to choose a Pargo pickup point.',
                    'woocommerce'
                ),
                'default' => __('Select a pick up point', 'woocommerce'),
            ),
            'pargo_buttoncaption_after' => array(
                'title' => __('Pickup Button Caption After Pickup Point Selection', 'woocommerce'),
                'type' => 'text',
                'description' => __(
                    'Sets the caption of the button after a user has selected a Pargo pickup point.',
                    'woocommerce'
                ),
                'default' => __('Re-select a pick up point', 'woocommerce'),
            ),
            'pargo_style_button' => array(
                'title' => __('Pickup Button Style', 'woocommerce'),
                'type' => 'textarea',
                'description' => __('Sets the style of the button pressed to select a pickup point.', 'woocommerce'),
                'default' => ''
            ),
            'enable_free_shipping' => array(
                'title' => __('Enable free shipping', 'woocommerce'),
                'type' => 'checkbox',
            ),
            'free_shipping_amount' => array(
                'title' => __('Set the minimum amount for free shipping', 'woocommerce'),
                'type' => 'number',
            ),
            'pargo_style_title' => array(
                'title' => __('Pargo Point Title Style', 'woocommerce'),
                'type' => 'textarea',
                'description' => __('Set the style of the selected Pargo Point title.', 'woocommerce'),
                'default' => 'font-size: 16px;font-weight:bold;margin-bottom:0px;margin-top:0px;max-width:250px;'
            ),
            'pargo_style_desc' => array(
                'title' => __('Pargo Point Description Style', 'woocommerce'),
                'type' => 'textarea',
                'description' => __('Set the style of the selected Pargo Point line items.', 'woocommerce'),
                'default' => 'font-size:12px;margin-bottom:0px;margin-top:0px;max-width:250px;'
            ),
            'pargo_style_image' => array(
                'title' => __('Pargo Point Image Style', 'woocommerce'),
                'type' => 'textarea',
                'description' => __('Set the style of the selected Pargo Point image', 'woocommerce'),
                'default' => 'max-width:250px;border:1px solid #EBEBEB;border-radius:2px;'
            ),
            'weight' => array(
                'title' => __('Weight (kg)', 'woocommerce'),
                'type' => 'number',
                'description' => __('Maximum allowed weight per item to use for Pargo delivery', 'woocommerce'),
                'id' => 'weight',
                'default' => 15
            ),
            'pargo_cost_5' => array(
                'title' => __('5kg Shipping to Pickup Point Cost', 'woocommerce'),
                'type' => 'number',
                'description' => __('This controls the cost of Pargo delivery to pickup point for 0-5kg items.', 'woocommerce'),
                'default' => __('75', 'woocommerce')
            ),
            'pargo_cost_10' => array(
                'title' => __('10kg Shipping to Pickup Point Cost', 'woocommerce'),
                'type' => 'number',
                'description' => __('This controls the cost of Pargo delivery to pickup point for 5-10kg items.', 'woocommerce'),
                'default' => __('', 'woocommerce')
            ),
            'pargo_cost_15' => array(
                'title' => __('15kg Shipping to Pickup Point Cost', 'woocommerce'),
                'type' => 'number',
                'description' => __('This controls the cost of Pargo delivery to pickup point for 10-15kg items.', 'woocommerce'),
                'default' => __('', 'woocommerce')
            ),
            'pargo_enable_home_delivery' => array(
                'title' => __('Enable home delivery', 'woocommerce'),
                'type' => 'checkbox',
                'description' => __(
                    'This will enable home delivery.',
                    'woocommerce'
                ),
                'default' => 'No'
            ),
            'pargo_door_cost_5' => array(
                'title' => __('5kg Shipping to Home Cost', 'woocommerce'),
                'type' => 'number',
                'description' => __('This controls the cost of Pargo delivery to home for 0-5kg items.', 'woocommerce'),
                'default' => __('75', 'woocommerce')
            ),
            'pargo_door_cost_10' => array(
                'title' => __('10kg Shipping to Home Cost', 'woocommerce'),
                'type' => 'number',
                'description' => __('This controls the cost of Pargo delivery to home for 5-10kg items.', 'woocommerce'),
                'default' => __('', 'woocommerce')
            ),
            'pargo_door_cost_15' => array(
                'title' => __('15kg Shipping to Home Cost', 'woocommerce'),
                'type' => 'number',
                'description' => __('This controls the cost of Pargo delivery for to home 10-15kg items.', 'woocommerce'),
                'default' => __('', 'woocommerce')
            ),
            'pargo_cost' => array(
                'title' => __('No weight Shipping Cost', 'woocommerce'),
                'type' => 'number',
                'description' => __(
                    'This controls the cost of Pargo delivery without product weight settings.',
                    'woocommerce'
                ),
                'default' => __('', 'woocommerce')
            ),
            'pargo_map_is_production' => array(
                'title' => __('Map production environment', 'woocommerce'),
                'type' => 'checkbox',
                'description' => __(
                    'Set this to true for a production environment and false when the site is still in development or is a staging environment.',
                    'woocommerce'
                ),
                'default' => 'yes'
            ),
            'pargo_map_display' => array(
                'title' => __('Pargo map display as static widget', 'woocommerce'),
                'type' => 'checkbox',
                'description' => __('Display map as a modal or static widget', 'woocommerce'),
                'default' => __('no', 'woocommerce')
            )

        );
    }

    /**
     * calculate_shipping function.
     * WC_Shipping_Method::get_option('pargo_cost_10');
     * @access public
     * @return array
     */
    public function getPargoSettings()
    {
        $pargosetting['pargo_description'] = $this->get_option('pargo_description');
        $pargosetting['pargo_map_token'] = $this->get_option('pargo_map_token');
        $pargosetting['pargo_map_is_production'] = $this->get_option('pargo_map_is_production');
        $pargosetting['pargo_buttoncaption'] = $this->get_option('pargo_buttoncaption');
        $pargosetting['pargo_buttoncaption_after'] = $this->get_option('pargo_buttoncaption_after');
        $pargosetting['pargo_style_button'] = $this->get_option('pargo_style_button');

        $pargosetting['pargo_style_title'] = $this->get_option('pargo_style_title');
        $pargosetting['pargo_style_desc'] = $this->get_option('pargo_style_desc');
        $pargosetting['pargo_style_image'] = $this->get_option('pargo_style_image');

        return $pargosetting;
    }

    /**
     * Called to calculate shipping rates for this method. Rates can be added using the add_rate() method.
     *
     * @param array $package Package array.
     */
    public function calculate_shipping($package = array())
    {
        $weight = 0;

        [$cost_5, $cost_10, $cost_15] = $this->getWeightCosts();

        foreach ($package['contents'] as $values) {
            $_product = $values['data'];
            if (!empty($_product->get_weight())) {
                $weight += $_product->get_weight() * $values['quantity'];
            }
        }

        $weight = wc_get_weight($weight, 'kg');

        if ($weight <= 5) {
            $cost = $cost_5;
        } elseif ($weight <= 10) {
            $cost = $cost_10;
        } else {
            $cost = $cost_15;
        }

        $rate = array(
            'id' => $this->id,
            'label' => $this->title.': ' . $this->get_option('pargo_description'),
            'cost' => $cost,
            'calc_tax' => 'per_item'
        );

        if ($this->get_option('enable_free_shipping') === 'yes') {
            $total_cart_amount = (int) WC()->cart->cart_contents_total;

            if ($total_cart_amount >= $this->get_option('free_shipping_amount')) {
                $rate['cost'] = 0;
                $rate['label'] = $this->title.': Free';
            }
        }

        // Register the rate
        $this->add_rate($rate);
    }

    /**
     * @return array
     */
    private function getWeightCosts(): array
    {
        if (WC()->session->get('delivery_type') === self::PARGO_PROCESS_TYPE_W2D) {
            $cost_5 = $this->get_option('pargo_door_cost_5');
            $cost_10 = $this->get_option('pargo_door_cost_10');
            $cost_15 = $this->get_option('pargo_door_cost_15');
        } else {
            $cost_5 = $this->get_option('pargo_cost_5');
            $cost_10 = $this->get_option('pargo_cost_10');
            $cost_15 = $this->get_option('pargo_cost_15');
        }

        return [$cost_5, $cost_10, $cost_15];
    }
}
