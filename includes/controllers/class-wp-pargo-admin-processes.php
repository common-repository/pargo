<?php

class Wp_Pargo_Admin_Processes
{
    public function verifyPargoAccount($username, $password)
    {
        /**
         * Get the pargo api url
         */
        $url = 'https://api.staging.pargo.co.za/auth';

        /**
         * Get the pargo username and password
         */
        $data = array(
            'username' => $username,
            'password' => $password
        );
        $headers = array();

        /**
         * Get Auth data from the API
         */
        $returnData = $this->postApi($url, $data, $headers);

        return $returnData;
    }
}
