<?php
class SIMWebServiceIsraeli
{

	function get_autorizacion(){

			$curl = curl_init();

			curl_setopt_array($curl, array(
		  CURLOPT_URL => URL_ISRAEL . 'Auth/login',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{
		   "userName": "'.USUARIO_ISRAEL.'",
		   "clave": "'.CLAVE_ISRAEL.'"
		}',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;
		$datos_resp = json_decode($response);
		$Token = $datos_resp->data->token;


		return $Token;
	}

	function get_cuotas($Token,$NumeroDocumento){

		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_ISRAEL . 'Cartera/CuotaDetalles/'.$NumeroDocumento.'/ID_N',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Authorization: Bearer '.$Token
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;
		$datos_resp=json_decode($response);
		$datos=$datos_resp->data;


		return $datos;
	}


	function pago_cuota($NumeroDocumento,$ValorPagado,$IDSocio){

		$dbo = & SIMDB::get();

		$Token = self::get_autorizacion();
		if(empty($Token))
		{
			$mensaje="No fue posible obtener respuesta, por favor intente mas tarde";
		}
		else
		{

			$curl = curl_init();

			curl_setopt_array($curl, array(
		  CURLOPT_URL => URL_ISRAEL . '/Cartera/CuotaDetalles/',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{
		  "Id_N": '.$NumeroDocumento.',
		  "ValorCancelado": '.$ValorPagado.',
		  "FormaPago": 1
		}',
		  CURLOPT_HTTPHEADER => array(
		    'Authorization: Bearer '.$Token,
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;
		$datos_resp = json_decode($response);
		$mensaje = $datos_resp->message;

		$datos_enviado = $NumeroDocumento."-".$ValorPagado;
		$sql_log_servicio = $dbo->query("INSERT INTO LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','pagocuotaisraeli','".$datos_enviado."','".json_encode($response)."')");

		}



		return $mensaje;
	}


}//end class
?>
