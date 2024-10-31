<?php


class Wp_Pargo_Monitor
{
    private $monitor_url;
    private $auth_url;
    private $client_url;
    private $environemt_url;
    private $client_id;
    private $client_secret;
    private $access_token;


    /**
     *
     */
    public function __construct()
    {
        $this->monitor_url = "https://monitor.pargosandbox.co.za/";
        $this->auth_url = $this->monitor_url."oauth/token";
        $this->client_url = $this->monitor_url.'api/plugin/client';
        $this->environemt_url = $this->monitor_url.'api/plugin/submit';
        $this->client_id = '3';
        $this->client_secret = 'L8CBIeDsHNvjJO99mUa4Ooori0cQHeKXrgtu0wYr';
    }

    /**
     * @param  string  $status
     * @return array
     */
    public function getAccess($status = "")
    {
        $success = true;
        $responseMessage = '';

        $bodyData = array(
            'client_id' => 3,
            'client_secret' => 'L8CBIeDsHNvjJO99mUa4Ooori0cQHeKXrgtu0wYr',
            'grant_type' => 'client_credentials'
        );

        $response = wp_remote_post(
            "https://monitor.pargosandbox.co.za/oauth/token",
            array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => $bodyData,
                'cookies' => array()
            )
        );

        // Check if Wordpress has an issue with the response
        if (is_wp_error($response) || '200' != wp_remote_retrieve_response_code($response)) {
            $success = false;
            $responseMessage = $response->get_error_message();
        }

        // Check if the response is empty
        if (empty($response)) {
            $responseMessage = "The response data is empty";
            $success = false;
        }

        // Get the response body
        $responseBody = json_decode(wp_remote_retrieve_body($response));

        if (isset($responseBody->error)) {
            $responseMessage = $responseBody->error;
            $success = false;
        }

        $cli = $this->pargoMonitorClient($responseBody->access_token, $status);
        $env = $this->pargoMonitorEnvironment($responseBody->access_token, $status);

        // Finally return the response
        return [
            "success" => $success,
            "message" => $responseMessage,
            "data" => $responseBody->access_token,
            "client" => $cli,
            "environment" => $env
        ];
    }

    /**
     * @param          $accessToken
     * @param  string  $status
     * @return array
     */
    public function pargoMonitorClient($accessToken, $status = 'ACTIVE')
    {
        $success = true;
        $responseMessage = 'Successfully posted to Monitor API';

        $bodyData = array(
            'platform' => 'WordPress',
            'plugin_version' => WPPARGOVERSION,
            'client' => get_option('blogname'),
            'client_domain' => get_option('siteurl'),
            'status' => $status,
            'plugin_date' => date("Y-m-d H:i:s")
        );

        $headerData = array(
            "Accept" => "application/json",
            "Authorization" => "Bearer $accessToken",
            "Cache-Control" => "no-cache",
            "Content-Type" => "application/x-www-form-urlencoded",
            "content-type" => "multipart/form-data;"
        );

        $response = wp_remote_post(
            "https://monitor.pargosandbox.co.za/api/plugin/submit",
            array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headerData,
                'body' => $bodyData,
                'cookies' => array()
            )
        );

        // Check if Wordpress has an issue with the response
        if (is_wp_error($response) || '200' != wp_remote_retrieve_response_code($response)) {
            $success = false;
            //			$responseMessage = $response->get_error_message();
            $responseMessage = "There was an error trying to make the request";
        }

        // Check if the response is empty
        if (empty($response)) {
            $responseMessage = "The response data is empty";
            $success = false;
        }

        // Get the response body
        $responseBody = json_decode(wp_remote_retrieve_body($response));

        if (isset($responseBody->error)) {
            $responseMessage = $responseBody->error;
            $success = false;
        }

        // Finally return the response
        return [
            "success" => $success,
            "message" => $responseMessage,
            "access_token" => $accessToken
        ];
    }


    /**
     * @param          $accessToken
     * @param  string  $status
     * @return array
     */
    public function pargoMonitorEnvironment($accessToken, $status = 'ACTIVATED')
    {
        $success = true;
        $responseMessage = 'Successfully posted environment details';

        $bodyData = array(
            'client' => get_option('siteurl'),
            'status' => $status
        );

        $headerData = array(
            "Accept" => "application/json",
            "Authorization" => "Bearer $accessToken",
            "Cache-Control" => "no-cache",
            "Content-Type" => "application/x-www-form-urlencoded",
            "content-type" => "multipart/form-data;"
        );

        $response = wp_remote_post(
            "https://monitor.pargosandbox.co.za/api/plugin/client",
            array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headerData,
                'body' => $bodyData,
                'cookies' => array()
            )
        );


        // Check if Wordpress has an issue with the response
        if (is_wp_error($response) || '200' != wp_remote_retrieve_response_code($response)) {
            $success = false;
            $responseMessage = "There was an error trying to process your request";
        }

        // Check if the response is empty
        if (empty($response)) {
            $responseMessage = "The response data is empty";
            $success = false;
        }

        // Get the response body
        $responseBody = json_decode(wp_remote_retrieve_body($response));


        if (isset($responseBody->error)) {
            $responseMessage = $responseBody->error;
            $success = false;
        }

        // Finally return the response
        return [
            "success" => $success,
            "message" => $responseMessage,
            "access_token" => $accessToken
        ];
    }
}
