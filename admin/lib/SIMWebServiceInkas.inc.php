<?php

class SIMWebServiceInkas
{
    // FUNCION QUE OBTIENE EL TOKEN DE ACCESO A LOS DEMAS SERVICIOS
    public function obtener_token()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => OBTENER_TOKEN_INKAS,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'GT: ' . GT_INKAS,
                'Content-Length: 0',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        return $DATA;
    }
    // FUNCION PARA ACTUALIZAR LOS SOCIOS, EL FILTRO PUEDE ESTAR VACIO O TRAER "MODIIFICACIONES"
    public function socios($Token, $Filtro = "")
    {
        if (!empty($Filtro)) {
            $CondicionFiltro = "?filtro=" . $Filtro;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => CONSULTA_SOCIOS_INKAS . $CondicionFiltro,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Length: 0',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $DATA = json_decode($response, true);

        return $DATA;

    }
    // OBTIENE LAS CUOTAS DE LOS SOCIOS PARA LOS PAFOS, PUEDE TAMBIEN TRAER ES DE UN SOLO SOCIO, CON EL CODIGO.
    public function cuenta_cuotas($Token, $Filtro = "")
    {

        if (!empty($Filtro)) {
            $CondicionFiltro = "?filtro=" . $Filtro;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => CUOTAS_INKAS . $CondicionFiltro,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Length: 0',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        return $DATA;
    }
    // VALORES DE LOS CONSUMOS DE LOS SOCIOS O DE UN SOCIO EN PARTICULAR CON LA VARIABLE FILTRO
    public function cuenta_consumos($Token, $Filtro = "")
    {
        if (!empty($Filtro)) {
            $CondicionFiltro = "?filtro=" . $Filtro;
        } else {
            $CondicionFiltro = "";
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => CONSUMOS_INKAS . $CondicionFiltro,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $Token,
                'Content-Length: 0',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATA = json_decode($response, true);

        return $DATA;
    }
}
