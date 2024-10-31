<?php


class Wp_Pargo_Map
{
    /**
     * This is the default map url for the Pargo Points
     *
     * @var string
     */
    private $pargo_map_url;

    private $auth_token;

    public function __construct()
    {
        // The Pargo Map API to get all the Pargo Pups
        $this->pargo_map_url = "https://api.pargo.co.za/v8/pargo_points";

        $this->auth_token = "YQw7kd9fQAdkxKefS3GW8PNCRXBuqg";
    }

    /**
     * Method used to make the request to the Maps API
     * @param $args
     *
     * @return array
     */
    public function makeMapRequest($params, $args)
    {
        $success = true;
        $responseMessage = 'Data successfully retrieved';

        // Making the Api Request
        $response = wp_remote_get(
            $this->pargo_map_url.$params,
            $args
        );

        // Check if Wordpress has an issue with the response
        //		if( is_wp_error( $response ) || '200' != wp_remote_retrieve_response_code( $response ) ) {
        //			$responseMessage = $response->get_error_message();
        //		}

        // Check if the response is empty
        if (empty($response)) {
            $responseMessage = "The response data is empty";
            $success = false;
        }

        // Get the response body
        $responseBody = json_decode(wp_remote_retrieve_body($response));
        // Check if request was successful
        $request_success = $responseBody->success;

        // Check if the API request was successful
        if (!$request_success) {
            $responseMessage = "The API request encountered a problem";
            $success = false;
        }

        // Finally return the response
        return [
            "success" => $success,
            "message" => $responseMessage,
            "data" => $responseBody->data
        ];
    }

    /**
     * Method used to make request to Maps API for all the available Pups
     *
     * @return array of all available pups
     */
    public function getAllPups()
    {
        $success = true;
        $responseMessage = 'Data successfully retrieved';

        // Making the Api Request
        $response = wp_remote_get(
            $this->pargo_map_url
        );

        // Check if Wordpress has an issue with the response
        if (is_wp_error($response) || '200' != wp_remote_retrieve_response_code($response)) {
            $responseMessage = $response->get_error_message();
        }

        // Check if the response is empty
        if (empty($response)) {
            $responseMessage = "The response data is empty";
            $success = false;
        }

        // Get the response body
        $responseBody = json_decode(wp_remote_retrieve_body($response));
        // Check if request was successful
        $request_success = $responseBody->success;

        // Check if the API request was successful
        if (!$request_success) {
            $responseMessage = "The API request encountered a problem";
            $success = false;
        }

        // Finally return the response
        return [
            "success" => $success,
            "message" => $responseMessage,
            "data" => $responseBody->data
        ];
    }

    /**
     * Method to request the nearest available Pups based on the address provided
     *
     * @param $limit    : used to limit the amount of nearest pups
     * @param $address  : used to find nearest pups
     *
     * @return array of nearest pups based on address given
     */
    public function getClosestPups($limit, $address)
    {
        $params = '?';
        $params .= 'sort=distance ASC&';
        $params .= "limit=$limit&";
        $params .= "address=$address&";
        // Adding the Pargo Map Authentication
        $args = array(
            "sort" => "distance ASC",
            "limit" => $limit,
            "address" => $address,
            "headers" => array(
                "Content-Type: application/json",
                "Authorization: $this->auth_token"
            )
        );

        return $this->makeMapRequest($params, $args);
    }
}
