<?php


class Wp_Pargo_Orders
{
    private $pargo_username;

    private $pargo_password;

    private $pargo_order_url;

    private $pargo_order_auth;

    public function postOrder($order)
    {
        include_once plugin_dir_path(__FILE__).'PargoApi.php';
        $pargoApi = new PargoApi();

        $data = array(
            "data" => array(
                "type" => "W2P",
                "version" => 1,
                "attributes" => array(
                    "warehouseAddressCode" => null,
                    "returnAddressCode" => null,
                    "trackingCode" => null,
                    "externalReference" => (string) $order->get_id(),
                    "pickupPointCode" => WC()->session->get('pargo_shipping_address')['pargoPointCode'],

                    "consignee" => array(
                        "firstName" => $order->get_data()['billing']['first_name'],
                        "lastName" => $order->get_data()['billing']['last_name'],
                        "email" => $order->get_data()['billing']['email'],
                        "phoneNumbers" => array(
                            $order->get_data()['billing']['phone'],
                        ),
                        "address1" => $order->get_data()['billing']['address_1'],
                        "address2" => $order->get_data()['billing']['address_2'],
                        "suburb" => "",
                        "postalCode" => $order->get_data()['billing']['postcode'],
                        "city" => $order->get_data()['billing']['city'],
                        "country" => "ZA"
                    ),
                ),
            ),
        );
        $data = json_encode($data);

        /**
         * Check to see if trailing slash is added else add it
         */
        $apiUrl = get_option('woocommerce_wp_pargo_settings')['pargo_url'];
        $lastChar = $apiUrl[strlen($apiUrl) - 1];
        if ($lastChar !== '/') {
            $apiUrl = $apiUrl.'/';
        }

        $url = $apiUrl.'orders';

        /**
         * Get an Authentication token
         */
        $accessToken = $pargoApi->getAuthToken();

        $headers = array(
            "Authorization" => "Bearer $accessToken",
            "Content-Type" => "application/json",
            "cache-control" => "no-cache"
        );

        $responseData = $pargoApi->postApi($url, $data, $headers);

        if (is_wp_error($responseData)) {
            $error_message = $responseData->get_error_message();
            return "Something went wrong: $error_message";
        } else {
            $response = wp_remote_retrieve_body($responseData);
            $labelUrl = json_decode($response)->data->attributes->orderData->orderLabel;
            update_post_meta(
                $order->get_id(),
                'pargo_waybill',
                ['waybill' => json_decode($response)->data->attributes->orderData->trackingCode, 'label' => $labelUrl]
            );
            $my_value = get_post_meta($order->get_id(), 'pargo_waybills', true);

            if (json_decode($response)->success == false) {
                $note = __("Pargo Shipment failed ".json_decode($response)->errors[0]->detail);
                $order->add_order_note($note);
                $order->save();
            } else {
                $note = __("Pargo Shipment processed.");
                $order->add_order_note($note);
                $order->save();
            }

            $url = $_SERVER['REQUEST_URI'];
            $parsed = parse_url($url);
            $path = $parsed['path'];
            unset($_GET['ship-now']);

            return $response;
        }
    }
}
