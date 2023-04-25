<?php
class SIMWebServiceTpaga
{	

	function get_autorizacion(){
		$user_tpaga="tiendaX";	
		$pass_tpaga="123456";
		$codigo_autorizacion = base64_encode($user_tpaga.$pass_tpaga);
		return $codigo_autorizacion;
	}
	
	
	function peticion_pago()	{		
		$dbo =& SIMDB::get();
		$response = array();
		
		$curl = curl_init();		
		curl_setopt_array($curl, array(
					CURLOPT_URL => "https://stag.wallet.tpaga.co/merchants/api/v1/",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HTTPHEADER => array(
						"cache-control: no-cache",
						"Authorization: Basic dGllbmRhWDoxMjM0NTY=",
					),
				));
		
		$response = curl_exec($curl);		
		print_r($response);
		
		$err = curl_error($curl);
		
		echo "Error: "; print_r($err);
		
		curl_close($curl);
		if (!$err):
			  echo "ok";
		else:
			echo "no";
		endif;	  
		
			
	}// fin function
	
	
	
}//end class
?>
