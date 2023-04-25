<?php
class SIMWebServiceSMS
{
	function enviar_sms($mobile,$message){		
		require_once(LIBDIR.'nusoap/lib/nusoap.php');
		$client = new nusoap_client("https://apismsi.aldeamo.com/sms/sms.wsdl", 'wsdl');
		$err = $client->getError();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}
		
		 $username = "22CERO2";
		 $password = "22CERO2*";
		 $country = "57";
		 //$mobile = "3203495740";
		 //$message = "MICLUB primera prueba de mensaje de texto";
		 $operator = "";	
            
		$result = $client->call('smsSendSoap', array("username"=>$username, "password"=>$password,"mobile"=>$mobile, "country"=>$country,"message"=>$message, "operator"=>$operator), '', '');					
		
		if ($client->fault) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $client->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} 
		}					
		
		//echo "<br><br><br>RESULTADO<br><br>";
		//print_r( $result);
		//echo "<br>Sesion:" . $result["SessionIDTokenResult"]["SessionID"];
		//echo "<br>Status:" . $result["SessionIDTokenResult"]["Status"];
		
		//return $result;
		return true;
}

	
	
}//end class
?>
