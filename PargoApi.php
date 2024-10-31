<?php

/**
 * @since      PargoApi
 * @package    pargo_plugin
 * @subpackage pargo_plugin/includes
 * @author     Pargo Plugins <plugins@pargo.co.za>
 */
class PargoApi
{
    /**
     * @param         $url
     * @param  array  $body
     * @param  array  $headers
     * @return string
     */
    public function postApi($url, $body = array(), $headers = array())
    {
        $response = wp_remote_post(
            $url,
            array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'body' => $body,
                'cookies' => array()
            )
        );

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            return "Something went wrong: $error_message";
        } else {
            return $response;
        }
    }

    public function getAuthToken($isDev = false)
    {
        if (!empty(WC()->session->get('access_token')) && time() < WC()->session->get('access_token_expiry')) {
            return WC()->session->get('access_token');
        }

        $apiUrl = get_option('woocommerce_wp_pargo_settings')['pargo_url'];
        $lastChar = $apiUrl[strlen($apiUrl) - 1];
        if ($lastChar !== '/') {
            $apiUrl = $apiUrl.'/';
        }

        /**
         * Get the pargo api url
         */
        $url = $apiUrl.'auth';

        /**
         * Get the pargo username and password
         */
        $data = array(
            'username' => get_option('woocommerce_wp_pargo_settings')['pargo_username'],
            'password' => get_option('woocommerce_wp_pargo_settings')['pargo_password']
        );
        $headers = array();

        /**
         * Get Auth data from the API
         */
        $returnData = $this->postApi($url, $data, $headers);

        /**
         * Check if an error is returned else return the authToken
         */
        if (is_wp_error($returnData)) {
            $error_message = $returnData->get_error_message();
            return "Something went wrong: $error_message";
        } else {
            $response = wp_remote_retrieve_body($returnData);

            $accessToken = json_decode($response)->access_token;
            $expiresIn = json_decode($response)->expires_in;
            WC()->session->set('access_token', $accessToken);
            WC()->session->set('access_token_expiry', $expiresIn);

            if ($isDev) {
                return $returnData;
            } else {
                return $accessToken;
            }
        }
    }

    /**
     * @return mixed
     */
    public function monitorAuthToken($url, $postData = array(), $headers = array())
    {
        /**
         * Get Auth data from the API
         */
        $returnData = $this->postApi($url, $postData, $headers);

        /**
         * Check if an error is returned else return the authToken
         */
        if (is_wp_error($returnData)) {
            $error_message = $returnData->get_error_message();
            return "Something went wrong: $error_message";
        } else {
            $response = wp_remote_retrieve_body($returnData);
            $accessToken = json_decode($response)->access_token;
            return $accessToken;
        }
    }

    public function getFormattedUrl()
    {
        $apiUrl = get_option('woocommerce_wp_pargo_settings')['pargo_url'];
        $lastChar = $apiUrl[strlen($apiUrl) - 1];
        if ($lastChar !== '/') {
            return $apiUrl.'/';
        }
        return $apiUrl;
    }

    /**
     * @param $username
     * @param $password
     *
     * @return mixed|string, [token_type, expires_in, access_token, refresh_token]
     */
    public function authUserCredentials($username, $password)
    {
        $url = $this->getFormattedUrl();
        $authUrl = $url."auth";

        $headers = [];

        $credentials = [
            "username" => $username,
            "password" => $password
        ];

        $args = [
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers,
            'body' => $credentials,
            'cookies' => array()
        ];

        $response = wp_remote_post($authUrl, $args);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();

            return "Something went wrong: $error_message";
        }
        $response = wp_remote_retrieve_body($response);

        return json_decode($response);
    }
}
