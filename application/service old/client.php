<?php

    require_once ("nusoap/nusoap.php");

    function call($url, $function, $params) {
		if( ! starts_with($url, "http")) { // funcion localizada bajo ./helpers/general_helper.php
			return false;
		}
		
        $cliente = new nusoap_client($url);

		$error = $cliente->getError();
		if ($error) {
			return false;
		}

		$result = $cliente->call($function, $params);
		
		if ($cliente->fault) {
			return $result;
		}
		else {
			$error = $cliente->getError();
			if ($error) {
				return false;
			}
			else {
				return $result;
			}
		}
    }
    
?>