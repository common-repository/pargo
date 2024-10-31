<?php

class Wp_Pargo_Frontend
{
    public function pargoLabelChange($label, $method)
    {
        if ($method->method_id == 'wp_pargo_shipping_method') {

            //get the backend settings
            $readyPargoSettings = new Wp_Pargo_Shipping_Method();

            $pargoSettings = $readyPargoSettings->getPargoSettings();
            $pargoMerchantUserToken = $pargoSettings['pargo_map_token'];
            $pargoButtonCaptionAfter = $pargoSettings['pargo_buttoncaption_after'];
            $pargo_style_button = $pargoSettings['pargo_style_button'];
            $pargo_style_title = $pargoSettings['pargo_style_title'];
            $pargo_style_desc = $pargoSettings['pargo_style_desc'];
            $pargo_style_image = $pargoSettings['pargo_style_image'];

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
            $label .= '<div class="pargo-cart">';
            $label .= '</div>';
            if (get_option('woocommerce_wp_pargo_settings')['pargo_map_display'] === 'no') {
                $label .= '<div id ="pargo_selected_pickup_location">';
                $label .= '<img id="pick-up-point-img" src="'.$image.'" style="'.$pargo_style_image.'"></img>';
                $label .= '<p id="pargoStoreName" style="'.$pargo_style_title.'">'.$storeName.'</p>';
                $label .= '<p id="pargoStoreAddress" style="'.$pargo_style_desc.'">'.$storeAddress.'</p>';
                $label .= '<p id="pargoBusinessHours" style="'.$pargo_style_desc.'">'.$businessHours.'</p>';
                $label .= '<button type="button" id="select_pargo_location_button" class="pargo-button" style="'.$pargo_style_button.'">';
                $label .= $pargoSettings['pargo_buttoncaption'];
                $label .= '</button>';
            }
            $label .= '</div>';
            $label .= '<div class="pargo-modal"><input id="modal-trigger-center" class="checkbox" type="checkbox"><div class="modal-overlay">';
            $label .= '<label for="modal-trigger-center" class="o-close"></label><div class="modal-wrap what-is-this a-center">';
            $label .= '<label for="modal-trigger-center" class="pargo-point-modal-close">X</label>';
            $label .= '<img src="'.PARGOPLUGINURL.'assets/images/info.png" style="height:auto;margin: 0 auto;" />';
            $label .= '</div></div></div></div></div>';
            $label .= '<p>Hello</p>';

            /** HIDDEN FIELDS **/

            $label .= '<input type="hidden" id="" value="'.$pargoMerchantUserToken.'"/>';
            $label .= '<input type="hidden" id="pargobuttoncaptionafter" value="'.$pargoButtonCaptionAfter.'"/>';
        }

        return $label;
    }
}
