<?php

class SIMWebServiceRanchoSF
{
    // FUNCION DE AUTENTICACION PARA RECOGER EL TOKEN
    public function login()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_RANCHOSF . '/api/login/authenticate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>
            '{
                "Username": "' . USUARIO_RANCHOSF . '",
                "Password": "' . CLAVE_RANCHOSF . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Codigo: ' . CODIGO,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATOS = json_decode($response, true);

        return $DATOS;
    }

    public function consumos($Token, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $Documento = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = $IDSocio");

        if (empty($FechaInicio)) {
            $FechaInicio = date("Ymd");
        } else {
            $FechaInicio = date("Ymd", strtotime($FechaInicio));
        }

        if (empty($FechaFin)) {
            $FechaFin = date("Ymd");
        } else {
            $FechaFin = date("Ymd", strtotime($FechaFin));
        }

        $POST =
            '{
            "Identificacion": "' . $Documento . '",
            "FechaInicio": "' . $FechaInicio . '",
            "FechaFin": "' . $FechaFin . '"
        }';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_RANCHOSF . '/api/Consultar',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATOS = json_decode($response, true);

        return $DATOS;
    }

    public function consultaSinXml($Token, $IDSocio, $FechaInicio, $FechaFin)
    {
        $dbo = &SIMDB::get();

        $Documento = $dbo->getFields("Socio", "NumeroDocumento", "IDSocio = $IDSocio");

        $curl = curl_init();

        if (empty($FechaInicio)) {
            $FechaInicio = date("Ymd");
        } else {
            $FechaInicio = date("Ymd", strtotime($FechaInicio));
        }

        if (empty($FechaFin)) {
            $FechaFin = date("Ymd");
        } else {
            $FechaFin = date("Ymd", strtotime($FechaFin));
        }

        $POST =
            '{
            "Identificacion": "' . $Documento . '",
            "FechaInicio": "' . $FechaInicio . '",
            "FechaFin": "' . $FechaFin . '"
        }';

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_RANCHOSF . '/api/ConsultarSinXml',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $DATOS = json_decode($response, true);

        return $DATOS;
    }

    public function consultaXml($Token, $Cedula, $Numero, $Caja)
    {
        $dbo = &SIMDB::get();

        $curl = curl_init();

        $POST = '{
            "Cedula": "'.$Cedula.'",
            "Numero": "'.$Numero.'",
            "Caja":"'.$Caja.'"

        }';
        curl_setopt_array($curl, array(
            CURLOPT_URL =>  WS_RANCHOSF . '/api/ConsultarXml',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATOS = json_decode($response, true);       

        return $DATOS;
    }

    public function notificacion($Token)
    {
        // $FechaInicio = "20210601";
        // $FechaFin = "20210601";
        $FechaInicio = date("Ymd");
        $FechaFin = date("Ymd");

        $POST = '{	
            "FechaInicio": "'.$FechaInicio.'",
            "FechaFin": "'.$FechaFin.'"
        }';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_RANCHOSF . '/api/ConsultarPorFecha',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$POST,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATOS = json_decode($response, true);       

        return $DATOS;
    }

}
