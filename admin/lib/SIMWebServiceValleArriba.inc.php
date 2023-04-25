<?php
    class SIMWebServiceValleArriba
    {
        public function Socios()
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => WS_VALLE_ARRIBA . 'socios',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $DATA = json_decode($response, true);
            return $DATA;
        }

        public function Cuentas($Accion)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => WS_VALLE_ARRIBA . 'EstadosCuenta/' . $Accion,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $DATA = json_decode($response, true);
            return $DATA;
        }
    }