<?php

require_once(TOOLKIT . '/class.datasource.php');
require_once(EXTENSIONS . '/freegeoip_service/lib/class.freegeoip_service.php');

Class datasourcefreegeoip extends Datasource {

    /**
     * Root now uses dashes instead of underscores
     * @since 0.3
     */
    public $dsParamROOTELEMENT = 'user-geo-info';

    public function about(){
        return array(
            'name' => 'FreeGeoIp Service',
            'version' => '1.1.0',
            'release-date' => '2017-02-19',
            'author' => array(
                'name' => 'Dom Sammut',
                'website' => 'https://www.domsammut.com/'
            )
        );
    }

    public function execute() {
        $data = $_SESSION['country-data'];

        // If there is no session, create the data using freegeoip lookup
		if (!isset($data)) {
            // Determine best method of getting Header info
            if (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
            }
            else {
                $headers = $_SERVER;
            }

            // And store the IP address
            if (array_key_exists( 'X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ip = $headers['X-Forwarded-For'];
            }
            else if (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ip = $headers['HTTP_X_FORWARDED_FOR'];
            }
            else {
                $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            }

            // Perform the lookup and store it
            $data = freegeoip_service_request::ip_lookup($ip);

            // Store data in a session
            $_SESSION['country-data'] = $data;
		}

        // Session data is empty? append error
        if (is_null($data)) {
            $result = new XMLElement($this->dsParamROOTELEMENT);
            $result->appendChild(new XMLElement('error', 'Location cannot be found.'));
            $result->appendChild(new XMLElement('ip_information', $_SERVER['REMOTE_ADDR']));
        }
        // Otherwise append the data
        else {
            $result = new XMLElement($this->dsParamROOTELEMENT, $data);
        }

        return $result;
    }
}
