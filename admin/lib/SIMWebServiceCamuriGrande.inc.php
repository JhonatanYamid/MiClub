<?php

class SIMWebServiceCamuriGrande
{
    public function GetTitulares()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_CAMURIGRANDE . 'Objfamiusr',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = json_decode($response);
        return $DATOS;
    }
    public function GetFamiliares()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_CAMURIGRANDE . 'Objfamivis',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $DATOS = json_decode($response);
        return $DATOS;
    }
}
