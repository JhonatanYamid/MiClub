<?php

class SIMWebServicePlayaAzul
{
    public function Acciones($Accion = "")
    {

        if (!empty($Accion)) :
            $Accion = "/$Accion";
        endif;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_PLAYA_AZUL . 'acciones' . $Accion,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'API-Key: ' . APIKEY_PLAYA_AZUL,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function Deuda($Accion)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_PLAYA_AZUL . 'deuda/' . $Accion,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'API-Key: ' . APIKEY_PLAYA_AZUL,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        $DATA = json_decode($response, true);
        return $DATA;
    }
}
