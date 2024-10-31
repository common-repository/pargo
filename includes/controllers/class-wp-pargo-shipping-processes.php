<?php


class Wp_Pargo_Shipping_Processes
{
    public function setShippingZip()
    {
        global $woocommerce;
        $state = null;
        if (isset(WC()->session->get('pargo_shipping_address')['province'])) {
            switch (WC()->session->get('pargo_shipping_address')['province']) {
                case 'Western Cape':
                    $state = 'WC';
                    break;
                case 'Northern Cape':
                    $state = 'NC';
                    break;
                case 'Eastern Cape':
                    $state = 'EC';
                    break;
                case 'Gauteng':
                    $state = 'GP';
                    break;
                case 'North West':
                    $state = 'NW';
                    break;
                case 'Mpumalanga':
                    $state = 'MP';
                    break;
                case 'Free State':
                    $state = 'FS';
                    break;
                case 'Limpopo':
                    $state = 'LP';
                    break;
                case 'KwaZulu-Natal':
                    $state = 'KZN';
                    break;

                default:
                    $state = null;
                    break;
            }
        }

        //set it
        if (isset(WC()->session->get('pargo_shipping_address')['address1'])) {
            $woocommerce->customer->set_shipping_address(WC()->session->get('pargo_shipping_address')['address1']);
        }
        if (isset(WC()->session->get('pargo_shipping_address')['address2'])) {
            $woocommerce->customer->set_shipping_address_2(WC()->session->get('pargo_shipping_address')['address2']);
        }
        if (isset(WC()->session->get('pargo_shipping_address')['city'])) {
            $woocommerce->customer->set_shipping_city(WC()->session->get('pargo_shipping_address')['city']);
        }
        if (isset(WC()->session->get('pargo_shipping_address')['province'])) {
            $woocommerce->customer->set_shipping_state(WC()->session->get('pargo_shipping_address')['province']);
        }
        if (!is_null($state)) {
            $woocommerce->customer->set_shipping_state($state);
        }

        if (isset(WC()->session->get('pargo_shipping_address')['storeName'])) {
            $woocommerce->customer->set_shipping_company(WC()->session->get('pargo_shipping_address')['storeName'] . ' (' . WC()->session->get('pargo_shipping_address')['pargoPointCode'] . ')');
        }
        if (isset(WC()->session->get('pargo_shipping_address')['postalcode'])) {
            $woocommerce->customer->set_shipping_postcode(WC()->session->get('pargo_shipping_address')['postalcode']);
        }
    }

    /**
     * @param $fields
     * @return array
     */
    public function overrideShippingLogic($fields)
    {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');

        $chosen_shipping = $chosen_methods[0];

        if ($chosen_shipping == 'wp_pargo') {
            $fields['shipping_state']['required'] = false;
            $fields['shipping_first_name']['required'] = false;
            $fields['shipping_last_name']['required'] = false;
            $fields['shipping_country']['required'] = false;
            $fields['shipping_company']['required'] = false;
            $fields['shipping_city']['required'] = false;
            $fields['shipping_address_1']['required'] = false;
            $fields['shipping_address_2']['required'] = false;
            $fields['shipping_postcode']['required'] = false;
        }

        return $fields;
    }

    /**
     * @return bool
     */
    public function customerAddressAvailable()
    {
        if (WC()->customer->get_billing_address()) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCustomerFullAddress()
    {
        $customerAddress = WC()->customer->get_billing_address();
        $customerAddress1 = WC()->customer->get_billing_address_1();
        $customerAddress2 = WC()->customer->get_billing_address_2();
        $customerCity = WC()->customer->get_billing_city();
        $customerCountry = WC()->customer->get_billing_country();

        $custFullAddress = $customerAddress;
        $custFullAddress .= " " . $customerAddress1;
        $custFullAddress .= " " . $customerAddress2;
        $custFullAddress .= ", " . $customerCity;
        $custFullAddress .= ", " . $customerCountry;

        return $custFullAddress;
    }

    /**
     * @return array|string
     */
    public function getRenderPickupPoints()
    {
        if ($this->customerAddressAvailable()) {
            $custAddr = $this->getCustomerFullAddress();
            $pargoMaps = new Wp_Pargo_Map();

            $response = $pargoMaps->getClosestPups(3, $pargoMaps);

            return $response;
        } else {
            return "Customer address not found";
        }
    }

    /**
     * @param $label
     * @param $method
     * @return mixed|string
     */
    public function wcPargoLabelChange($label, $method)
    {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_shipping = $chosen_methods[0];

        if ($chosen_shipping != 'wp_pargo') {
            $label = $label;
        } else {
            if ($method->method_id == 'wp_pargo') {
                //get the backend settings
                $readyPargoSettings = new Wp_Pargo_Shipping_Method();

                $pargoSettings = $readyPargoSettings->getPargoSettings();
                $pargoMerchantUserToken = $pargoSettings['pargo_map_token'];
                $pargoButtonCaptionAfter = $pargoSettings['pargo_buttoncaption_after'];
                $pargo_style_button = $pargoSettings['pargo_style_button'];
                $pargo_style_title = $pargoSettings['pargo_style_title'];
                $pargo_style_desc = $pargoSettings['pargo_style_desc'];
                $pargo_style_image = $pargoSettings['pargo_style_image'];
                $pargo_map_is_production = strtolower($pargoSettings['pargo_map_is_production']) === 'yes';
                WC()->shipping->calculate_shipping(WC()->shipping->packages);
                $image = null;
                $storeName = null;
                $storeAddress = null;
                $businessHours = null;

                if (isset(WC()->session->get('pargo_shipping_address')['photo'])) {
                    $image = WC()->session->get('pargo_shipping_address')['photo'];
                }

                if (isset(WC()->session->get('pargo_shipping_address')['storeName'])) {
                    $storeName = WC()->session->get('pargo_shipping_address')['storeName'];
                }

                if (isset(WC()->session->get('pargo_shipping_address')['address1'])) {
                    $storeAddress = WC()->session->get('pargo_shipping_address')['address1'];
                }

                if (isset(WC()->session->get('pargo_shipping_address')['businessHours'])) {
                    $businessHours = WC()->session->get('pargo_shipping_address')['businessHours'];
                }

                //button
                $label .= '<label class="pargo-small" for="modal-trigger-center" > [ what is Pargo? ]</label>';

                if (get_option('woocommerce_wp_pargo_settings')['pargo_enable_home_delivery'] === 'yes') {
                    $label .= '<select class="pargo-dropdown" id="pargo-select-delivery-type" name="pargo-select-delivery-type" size="2">
                               <option selected="selected" value="W2P">Pargo Pickup Point</option>
                               <option value="W2D">Pargo Home Delivery</option>
                           </select>';
                }

                $label .= '<div class="pargo-cart">';
                $label .= '</div>';

                if (get_option('woocommerce_wp_pargo_settings')['pargo_map_display'] === 'no') {
                    $label .= '<div id ="pargo-selected-pickup-location">';
                    $label .= '<img id="pick-up-point-img" src="' . $image . '" style="' . $pargo_style_image . '"></img>';
                    $label .= '<p id="pargoStoreName" style="' . $pargo_style_title . '">' . $storeName . '</p>';
                    $label .= '<p id="pargoStoreAddress" style="' . $pargo_style_desc . '">' . $storeAddress . '</p>';
                    $label .= '<p id="pargoBusinessHours" style="' . $pargo_style_desc . '">' . $businessHours . '</p>';
                    $label .= '<button type="button" id="select_pargo_location_button" class="pargo-button" style="' . $pargo_style_button . '">';
                    $label .= $pargoSettings['pargo_buttoncaption'];
                    $label .= '</button>';
                }

                $label .= '</div>';
                $label .= '<div class="pargo-modal"><input id="modal-trigger-center" class="checkbox" type="checkbox"><div class="modal-overlay">';
                $label .= '<label for="modal-trigger-center" class="o-close"></label><div class="modal-wrap what-is-this a-center">';
                $label .= '<label for="modal-trigger-center" class="pargo-point-modal-close">X</label>';
                $label .= '<img src="' . PARGOPLUGINURL . 'assets/images/info.png" style="height:auto;margin: 0 auto;" />';
                $label .= '</div></div></div></div></div>';

                //horrible code.
                // need to be

                /** HIDDEN FIELDS **/

                $label .= "<input type='hidden' id='pargoismapproduction' value='$pargo_map_is_production'>";
                $label .= "<input type='hidden' id='pargomerchantusermaptoken' value='$pargoMerchantUserToken'/>";
                $label .= "<input type='hidden' id='pargobuttoncaptionafter' value='$pargoButtonCaptionAfter'/>";
                $label .= "<script type='application/javascript'>
                    // this has to be here to make sure events are created when dom elements are overwritten by woocom update_order_review
                    jQuery('#pargo-select-delivery-type').waitUntilExists(setupProcessTypeSelect(jQuery),false);
                </script>";
            }
        }

        return $label;
    }

    /**
     * @param $posted
     */
    public function pargoValidateOrders($posted)
    {
        $packages = WC()->shipping->get_packages();
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        if (is_array($chosen_methods) && in_array('wp_pargo', $chosen_methods)) {
            foreach ($packages as $i => $package) {
                if ($chosen_methods[$i] != "wp_pargo") {
                    continue;
                }

                $Pargo_Shipping_Method = new Wp_Pargo_Shipping_Method();
                $weightLimit = (int) $Pargo_Shipping_Method->settings['weight'];
                $weight = 0;

                foreach ($package['contents'] as $item_id => $values) {
                    $_product = $values['data'];
                    $weight = $_product->get_weight();
                }

                $weight = wc_get_weight($weight, 'kg');

                if ($weight > $weightLimit) {
                    $message = sprintf(__(
                        'Sorry, something in your cart of %d kg exceeds the maximum weight of %d kg allowed for %s',
                        'woocommerce'
                    ), $weight, $weightLimit, $Pargo_Shipping_Method->title);
                    $messageType = "error";

                    if (!wc_has_notice($message, $messageType)) {
                        wc_add_notice($message, $messageType);
                    }
                }
            }
        }
    }
}
