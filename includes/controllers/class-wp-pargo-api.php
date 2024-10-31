<?php


class Wp_Pargo_Api
{
    /**
     * @param  false  $isDev
     * @return string
     */
    public function AuthToken($isDev = false)
    {
        if (!empty(WC()->session->get('access_token')) && time() < WC()->session->get('access_token_expiry')) {
            return WC()->session->get('access_token');
        }

        $apiUrl = get_option('woocommerce_wp_pargo_settings')['pargo_url'];
        $lastChar = $apiUrl[strlen($apiUrl) - 1];
        if ($lastChar !== '/') {
            $apiUrl = $apiUrl.'/';
        }

        //Set the pargo api url
        $url = $apiUrl.'auth';

        //Set the pargo username and password
        $data = array(
            'username' => get_option('woocommerce_wp_pargo_settings')['pargo_username'],
            'password' => get_option('woocommerce_wp_pargo_settings')['pargo_password']
        );
        $headers = array();

        //Set Auth data from the API
        $returnData = $this->postApi($url, $data, $headers);

        //Check if an error is returned else return the authToken
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

    /**
     * @param  false  $isDev
     * @return string
     */
    public function getAuthToken($isDev = false)
    {
        if (!empty(WC()->session->get('access_token')) && time() < WC()->session->get('access_token_expiry')) {
            return WC()->session->get('access_token');
        }

        $apiUrl = get_option('woocommerce_wp_pargo_settings')['pargo_url'];
        $lastChar = $apiUrl[strlen($apiUrl) - 1];
        if ($lastChar !== '/') {
            $apiUrl .= '/';
        }

        //Set the pargo api url
        $url = $apiUrl.'auth';

        //Set the pargo username and password
        $data = array(
            'username' => get_option('woocommerce_wp_pargo_settings')['pargo_username'],
            'password' => get_option('woocommerce_wp_pargo_settings')['pargo_password']
        );
        $headers = array();

        //Set Auth data from the API
        $returnData = $this->postApi($url, $data, $headers);

        //Check if an error is returned else return the authToken
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
     * @param $api_url
     * @param $username
     * @param $password
     *
     * @return bool
     * @package verifyUserCredentials - Used to verify user account on PargoAPI
     *
     */
    public function verifyUserCredentials($api_url, $username, $password)
    {
        $lastChar = $api_url[strlen($api_url) - 1];
        if ($lastChar !== '/') {
            $api_url .= '/';
        }

        //Set the pargo api url
        $url = $api_url.'auth';

        //Set the pargo username and password
        $data = array(
            'username' => $username,
            'password' => $password
        );
        $headers = array();

        //Get Auth data from the API
        $returnData = $this->postApi($url, $data, $headers);

        //Check if an error is returned else return the authToken
        if (is_wp_error($returnData)) {
            return false;
        } else {
            $response = wp_remote_retrieve_body($returnData);

            if (isset(json_decode($response)->access_token)) {
                return true;
            }
        }
        return false;
    }
}
