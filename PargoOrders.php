<?php

include_once PARGOPATH.'PargoApi.php';

class PargoOrders
{
    private const SOURCE_WOOCOMMERCE = "woocommerce";
    public const PROCESS_TYPE_W2D = "W2D";
    public const PROCESS_TYPE_W2P = "W2P";

    /**
     * @param          $order WC_Order
     * @param  string  $processType
     * @return false|string|void
     */
    final public function placeOrder(WC_Order $order, string $processType)
    {
        if (!in_array($processType, [self::PROCESS_TYPE_W2D, self::PROCESS_TYPE_W2P])) {
            error_log('Only process types of W2D and W2P are supported');
            return '';
        }

        // check if pargo is the chosen shipping method
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_shipping = $chosen_methods[0];
        if ($chosen_shipping !== 'wp_pargo') {
            return false;
        }

        if ($processType === self::PROCESS_TYPE_W2P) {
            // if no pickup point is set then do not post order
            if (!isset(WC()->session->get('pargo_shipping_address')['pargoPointCode']) || WC()->session->get('pargo_shipping_address')['pargoPointCode'] === null) {
                $note = __("Pargo pickup point is invalid - session value is not set or null.");
                $order->add_order_note($note);
                $order->save();
                return;
            }
        }

        return $this->doOrderRequest($order, $processType);
    }

    /**
     * @param  string  $responseData
     * @param          $order
     * @return string
     */
    private function handlePargoOrderApiError(string $responseData, $order): string
    {
        $error_message = $responseData->get_error_message();
        $note = __("Pargo Order failed to submit: check config ".$error_message);
        $order->add_order_note($note);
        $order->save();

        return "Pargo Order failed to submit: $error_message";
    }

    /**
     * @param  array   $responseData
     * @param  string  $url
     * @param          $order
     * @return string
     */
    private function handlePargoOrderApiResponse(array $responseData, string $url, $order): string
    {
        $response = wp_remote_retrieve_body($responseData);

        if (json_decode($response)->success == false) {
            $note = __("Pargo Order failed to submit to url: ".$url);
            $order->add_order_note($note);
            $order->save();

            if (json_decode($response)->errors[0]->detail != null && json_decode($response)->errors[0]->detail != '') {
                $note = __("API error response ".json_decode($response)->errors[0]->detail);
                $order->add_order_note($note);
                $order->save();
            }
        } else {
            $waybill = json_decode($response)->data->attributes->orderData->trackingCode;
            $labelUrl = json_decode($response)->data->attributes->orderData->orderLabel;

            update_post_meta($order->get_id(), 'pargo_waybill', ['waybill' => $waybill, 'label' => $labelUrl]);
            $order->update_meta_data('pargo_waybill', $waybill);

            update_post_meta($order->get_id(), 'pargo_order_sent', 'yes');
            $order->update_meta_data('pargo_label', $labelUrl);

            // set session to check for duplicate order creates
            WC()->session->set("pargo_".$order->get_id(), 'sent');

            $note = __("Pargo Order created - waybill: ".$waybill);
            $order->add_order_note($note);
            $order->save();
        }

        unset($_GET['ship-now']);

        return $response;
    }

    /**
     * @param $order WC_Order
     * @return false|string
     * @throws JsonException
     */
    private function getW2pData(WC_Order $order): string
    {
        $data = [
            "source" => self::SOURCE_WOOCOMMERCE,
            "source_channel" => self::SOURCE_WOOCOMMERCE,
            "data" => [
                "type" => self::PROCESS_TYPE_W2P,
                "version" => 1,
                "attributes" => [
                    "warehouseAddressCode" => null,
                    "returnAddressCode" => null,
                    "trackingCode" => null,
                    "externalReference" => (string) $order->get_id(),
                    "pickupPointCode" => WC()->session->get('pargo_shipping_address')['pargoPointCode'],
                    "consignee" => [
                        "firstName" => $order->get_data()['billing']['first_name'],
                        "lastName" => $order->get_data()['billing']['last_name'],
                        "email" => $order->get_data()['billing']['email'],
                        "phoneNumbers" => [
                            $order->get_data()['billing']['phone'],
                        ],
                        "address1" => $order->get_data()['billing']['address_1'],
                        "address2" => $order->get_data()['billing']['address_2'],
                        "suburb" => $order->get_meta('_billing_suburb'),
                        "postalCode" => $order->get_data()['billing']['postcode'],
                        "city" => $order->get_data()['billing']['city'],
                        "country" => "ZA"
                    ],
                ],
            ],
        ];
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * @param $order WC_Order
     * @return false|string
     * @throws JsonException
     */
    private function getW2dData(WC_Order $order): string
    {
        $data = [
            "source" => self::SOURCE_WOOCOMMERCE,
            "source_channel" => self::SOURCE_WOOCOMMERCE,
            "data" => [
                "type" => self::PROCESS_TYPE_W2D,
                "version" => 1,
                "attributes" => [
                    "warehouseAddressCode" => null,
                    "returnAddressCode" => null,
                    "trackingCode" => null,
                    "externalReference" => (string) $order->get_id(),
//                    "deadWeight" => WC()->cart->cart_contents_weight,
                    "consignee" => [
                        "firstName" => $order->get_data()['billing']['first_name'],
                        "lastName" => $order->get_data()['billing']['last_name'],
                        "email" => $order->get_data()['billing']['email'],
                        "phoneNumbers" => [
                            $order->get_data()['billing']['phone'],
                        ],
                        "address1" => $order->get_data()['billing']['address_1'],
                        "address2" => $order->get_data()['billing']['address_2'],
                        "suburb" => $order->get_meta('_billing_suburb'),
                        "postalCode" => $order->get_data()['billing']['postcode'],
                        "city" => $order->get_data()['billing']['city'],
                        "country" => "ZA"
                    ],
                ],
            ],
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * @param          $order WC_Order
     * @param  string  $processType
     * @return string
     */
    private function doOrderRequest(WC_Order $order, string $processType): string
    {
        $pargoApi = new PargoApi();
        try {
            if ($processType === self::PROCESS_TYPE_W2P) {
                $order_data = $this->getW2pData($order);
            } else {
                $order_data = $this->getW2dData($order);
            }
        } catch (JsonException $e) {
            error_log($e);
            return '';
        }

        // Check to see if trailing slash is added else add it
        $apiUrl = get_option('woocommerce_wp_pargo_settings')['pargo_url'];
        $lastChar = $apiUrl[strlen($apiUrl) - 1];
        if ($lastChar !== '/') {
            $apiUrl = $apiUrl.'/';
        }
        $url = $apiUrl.'orders';

        // Get an Authentication token
        $accessToken = $pargoApi->getAuthToken();

        $headers = array(
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json',
            'cache-control' => 'no-cache'
        );

        // check if order has already been created to avoid duplicates
        // 1. check session as using meta was not working fast enough
        if (!empty(WC()->session->get("pargo_".$order->get_id())) && WC()->session->get("pargo_".$order->get_id()) === 'sent') {
            return '';
        }
        // 2. check meta because that is supposed to work
        $pargo_order_sent = get_post_meta($order->get_id(), 'pargo_order_sent', true);
        if (!empty($pargo_order_sent)) {
            return '';
        }

        $responseData = $pargoApi->postApi($url, $order_data, $headers);
        if (is_wp_error($responseData)) {
            return $this->handlePargoOrderApiError($responseData, $order);
        } else {
            return $this->handlePargoOrderApiResponse($responseData, $url, $order);
        }
    }
}
