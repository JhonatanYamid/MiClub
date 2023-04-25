<?php

class SIMWebServiceHebraica
{
    function Token()
    {
        $token_url = "http://integratum-cloud.com:8080/ords/integratum-cloud/oauth/token";
        $client_id = "2_PgScV7UZ72Ke_yYdP_BQ..";
        $client_secret = "FbOOUIRgXRYLGAow2Kt3xQ.."; 
		
		$authorization = base64_encode("$client_id:$client_secret");	 		
        $curl = curl_init();        

        curl_setopt_array($curl, array(
            CURLOPT_URL => $token_url,
            CURLOPT_HTTPHEADER => array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',        
            CURLOPT_POSTFIELDS => "grant_type=client_credentials"
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        /* echo $response;
        exit; */
        $Datos = json_decode($response,true);
        return $Datos[access_token];

    }

    function clientes()
    {
        $Token = SIMWebServiceHebraica::Token();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://www.integratum-cloud.com:8080/ords/integratum-cloud/fac/v1/clientes/?id_empresa=18',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $Token
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;

        $Datos = json_decode($response,true);
        return $Datos;
    }

    function estado_cuenta_cliente($id_persona)
    {
        $Token = SIMWebServiceHebraica::Token();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://www.integratum-cloud.com:8080/ords/integratum-cloud/fac/v1/estado_cuenta_clientes/?id_empresa=18&id_persona='.$id_persona.'&cd_divisa=&id_categoria_cliente&cd_divisa_reexpresion=USD',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $Token
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        $Datos = json_decode($response,true);
        return $Datos;
    }
}
