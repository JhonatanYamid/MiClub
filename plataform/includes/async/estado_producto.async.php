<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

if (!empty($_POST["IDProducto"]) && !empty($_POST["Version"]) && !empty($_POST["IDPedido"]) && !empty($_POST["IDEstado"])){
	$datos_estado=$dbo->fetchAll( "EstadoProducto", " IDEstadoProducto = '" . $_POST["IDEstado"] . "' ", "array" );
	$RutaFireBase=$dbo->getFields( "Club", "BaseFirebase", "IDClub = '" . $_POST["IDClub"] . "'" );
	switch($_POST["Version"]){
			case "1":
				$Version="";
				$IDModulo=33;
			break;
			case "2":
				$Version="2";
				$IDModulo=98;
			break;
			case "3":
				$Version="3";
				$IDModulo=112;
			break;
			case "4":
			 $Version="4";
			 $IDModulo=113;
			break;
	}



	$actualiza_prod= "UPDATE DomicilioDetalle".$Version." Set IDEstadoProducto = '". $_POST["IDEstado"] ."' Where IDDomicilio = '". $_POST["IDPedido"] ."' ";
	$dbo->query($actualiza_prod);
	if(!empty($RutaFireBase)){
		//Actualizo Firebase
		$headers = array('X-HTTP-Method-Override: PATCH');
		$url = $RutaFireBase.$IDModulo."/".$_POST["IDPedido"]."/products/".$_POST["IDProducto"].".json";
		//$url = "https://mi-club-40515.firebaseio.com/33/deliveries.json";
		$data='{"status":"'.$datos_estado["Nombre"].'"}';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$jsonResponse = curl_exec($ch);
		if(curl_errno($ch))
		{
		    //echo 'Curl error: ' . curl_error($ch);
		}
		curl_close($ch);
		//print_r($jsonResponse);
	}
}
?>
["ok"]
