<?php

    Class extension_freegeoip_service extends Extension {

    	public function getSubscribedDelegates() {
    		return array(
    			array(
    				'page' => '/frontend/',
    				'delegate' => 'FrontendParamsPostResolve',
    				'callback' => 'addParameters'
    			)
    		);
    	}

    	public function addParameters($context) {
    		session_start();

            // Last request was more than 1 day ago, so reset the country and data sessions
            if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 86400)) {
    			unset($_SESSION['country-code']);
    			unset($_SESSION['country-data']);
            }

            // Update last activity time stamp
            $_SESSION['LAST_ACTIVITY'] = time();

            // Set the country code based on URL parameter
    		if ($_GET['set_country_code']) {
                $_SESSION['country-code'] = $_GET['set_country_code'];
            }

            // Clear the country code
    		if ($_GET['clear_country_code']) {
    			unset($_SESSION['country-code']);
    		}

            // Add code to parameter pool
    		if (isset($_SESSION['country-code'])) {
            	$context['params']['country'] = $_SESSION['country-code'];
            }
        }
    }

?>
