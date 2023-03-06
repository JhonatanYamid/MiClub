<?php

function obtener_token(){
		require_once('nusoap/lib/nusoap.php');
		$client = new nusoap_client("http://181.48.188.77/Zeus_WS/ServiceWS.asmx?WSDL", 'wsdl');
		$err = $client->getError();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}
		
		$params="
			<ns1:SessionIDToken xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
			  <ns1:Request>
				<ns1:TypeSQL></ns1:TypeSQL>
				<ns1:DynamicProperty></ns1:DynamicProperty>
				<ns1:SessionID></ns1:SessionID>
				<ns1:Action></ns1:Action>
				<ns1:Body><![CDATA[<generatortoken><security user='ClubesWS' password='Zeus1234%' />    </generatortoken>]]>
			</ns1:Body>
			  </ns1:Request>
			</ns1:SessionIDToken>";
										 
		$result = $client->call('SessionIDToken', $params, '', '');					
		//echo $client->response;
		
		
		if ($client->fault) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $client->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} 
		}					
		
		//print_r( $result);
		//echo "<br>Sesion:" . $result["SessionIDTokenResult"]["SessionID"];
		//echo "<br>Status:" . $result["SessionIDTokenResult"]["Status"];
		
		return $result;
}


function envia_invitacion($Token){
		require_once('nusoap/lib/nusoap.php');
		$client = new nusoap_client("http://181.48.188.77/Zeus_WS/ServiceWS.asmx?WSDL", 'wsdl');
		$err = $client->getError();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}
		
		$params="
			<ns1:StandardCommunicationSOAP xmlns:ns1='http://www.w3.org/XML/1998/namespace:lang'>
			  <ns1:Request>
				<ns1:TypeSQL></ns1:TypeSQL>
				<ns1:DynamicProperty></ns1:DynamicProperty>
				<ns1:SessionID>".$Token."</ns1:SessionID>
				<ns1:Action>AutorizarInvitado</ns1:Action>
				<ns1:Body>				
					<![CDATA[
           			 <zeusclubes>
						<autorizaciondeinvitados>
							<autorizacion>NUEVO</autorizacion>
							<accion>0140</accion>
									  <secuencia>00</secuencia>
									 <identificacion>NUEVO</identificacion>
									 <nombre>Tomas Rueda</nombre>
									 <genero>M</genero>
									 <area></area>
									 <observacion></observacion>
									 <fechainicial>2017/03/07</fechainicial>
									 <fechafinal>2017/03/08</fechafinal>
									 <usuario>sa</usuario>
						</autorizaciondeinvitados>
					</zeusclubes>					
					]]>
				</ns1:Body>
			  </ns1:Request>
			</ns1:StandardCommunicationSOAP>";
										 
		$result = $client->call('StandardCommunicationSOAP', $params, '', '');					
		//echo $client->response;
		
		
		if ($client->fault) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $client->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} 
		}					
		
		print_r( $result);
		//echo "<br>Sesion:" . $result["SessionIDTokenResult"]["SessionID"];
		//echo "<br>Status:" . $result["SessionIDTokenResult"]["Status"];		
		return $result;
}


// Si consulta bien el token envio invitacion
$array_datos_token = obtener_token();
if(!empty($array_datos_token["SessionIDTokenResult"]["SessionID"]) && $array_datos_token["SessionIDTokenResult"]["Status"]=="SUCCESS" ):

	$result_envia_invitacion=envia_invitacion($array_datos_token["SessionIDTokenResult"]["SessionID"]);

endif;








?>
