<?php
	header('Content-Type: text/txt; charset=UTF-8');
	include( "../../procedures/general_async.php" );
	SIMUtil::cache( "text/json" );
	$dbo =& SIMDB::get();
	$frm = SIMUtil::makeSafe( $_POST );
	$frm_get =  SIMUtil::makeSafe( $_GET );

	$datos_puerta = $dbo->fetchAll( "Puerta", " IDPuerta = '" . $frm["IDPuerta"] . "' ", "array" );



$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "18083",
  CURLOPT_URL => "http://broker.miclubapp.com:18083/api/v4/mqtt/publish",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"qos\":1,\"retain\": false, \"topic\":\"".$datos_puerta["IDClub"]."/".$datos_puerta["CodigoDispositivo"]."/commando\", \"payload\":\"{\\\"cmd\\\":\\\"activar\\\",\\\"pin\\\":\\\"".$datos_puerta["PinEquipo"]."\\\",\\\"tactivo\\\":\\\"".$datos_puerta["TiempoApertura"]."\\\",\\\"tespera\\\":\\\"".$datos_puerta["TiempoEspera"]."\\\"}\" , \"client_id\": \"".$datos_puerta["IdentificadorCliente"]."\"}",
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Authorization: Basic bWFwcHM6MTIzNDU2Nw==",
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  //echo $response;
}

$array_respuesta = json_decode($response);
if($array_respuesta->code==0){
	echo '["ok"]';
}
else{
		echo '["'.$err.'"]';
}


?>
