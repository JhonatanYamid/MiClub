
<?php


/*
 *	$Id: wsdlclient1.php,v 1.3 2007/11/06 14:48:48 snichol Exp $
 *
 *	WSDL client sample.
 *
 *	Service: WSDL
 *	Payload: document/literal
 *	Transport: http
 *	Authentication: none
 */
require_once('../lib/nusoap.php');
$proxyhost = isset($_POST['proxyhost']) ? $_POST['proxyhost'] : '';
$proxyport = isset($_POST['proxyport']) ? $_POST['proxyport'] : '';
$proxyusername = isset($_POST['proxyusername']) ? $_POST['proxyusername'] : '';
$proxypassword = isset($_POST['proxypassword']) ? $_POST['proxypassword'] : '';
$client = new nusoap_client('http://181.48.188.77/Zeus_WS/ServiceWS.asmx?WSDL', 'wsdl',
						$proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}
// Doc/lit parameters get wrapped
$param = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:nam="http://www.w3.org/XML/1998/namespace:lang"><soap:Header/><soap:Body><nam:SessionIDToken><nam:Request><nam:TypeSQL>?</nam:TypeSQL><nam:DynamicProperty>?</nam:DynamicProperty><nam:SessionID>?</nam:SessionID><nam:Action>?</nam:Action><nam:Body><![CDATA[<generatortoken><security user="ClubesWS" password="Zeus1234%" /></generatortoken>]]></nam:Body></nam:Request></nam:SessionIDToken></soap:Body></soap:Envelope>';

$param = '<soapenv:Envelope xmlns:soapenv="http://tempuri.org/SessionIDToken" xmlns:nam="http://www.w3.org/XML/1998/namespace:lang">
   <soapenv:Header/>
   <soapenv:Body>
      <nam:SessionIDToken>
         <!--Optional:-->
         <nam:Request>
            <!--Optional:-->
            <nam:TypeSQL>?</nam:TypeSQL>
            <!--Optional:-->
            <nam:DynamicProperty>?</nam:DynamicProperty>
            <!--Optional:-->
            <nam:SessionID>?</nam:SessionID>
            <!--Optional:-->
            <nam:Action>?</nam:Action>
            <!--Optional:-->
            <nam:Body><![CDATA[<generatortoken><security user="ClubesWS" password="Zeus1234%" />    </generatortoken>]]>
</nam:Body>
         </nam:Request>
      </nam:SessionIDToken>
   </soapenv:Body>
</soapenv:Envelope>';



$result = $client->call('SessionIDToken', $param);
// Check for a fault
if ($client->fault) {
	echo '<h2>Fault</h2><pre>';
	print_r($result);
	echo '</pre>';
} else {
	// Check for errors
	$err = $client->getError();
	if ($err) {
		// Display the error
		echo '<h2>Error</h2><pre>' . $err . '</pre>';
	} else {
		// Display the result
		echo '<h2>Result</h2><pre>';
		print_r($result);
		echo '</pre>';
	}
}
echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';
?>
