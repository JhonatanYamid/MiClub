<?php

class SIMWebServiceLagunita
{
    // FUNCION PARA ACTUALIZAR LOS SOCIOS, EL FILTRO PUEDE ESTAR VACIO O TRAER "MODIIFICACIONES"
    public function socios($Accion = "")
    {
        if (!empty($Accion)) {
            $CondicionFiltro = "/GetAccion" . "/" . $Accion;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_LAGUNITA . "/api/GetSocios" . $CondicionFiltro,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'ApiKey: ' . API_KEY,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        return $DATA;

    }

    public function extractos($Accion = "")
    {
        if (!empty($Accion)) {
            $CondicionFiltro = "/GetAccion" . "/" . $Accion;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_LAGUNITA . "/api/GetExtractos" . $CondicionFiltro,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'ApiKey: ' . API_KEY,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        return $DATA;

    }

    public function estadoCuenta($Accion = "")
    {
        if (!empty($Accion)) {
            $CondicionFiltro = "/GetAccion" . "/" . $Accion;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => WS_LAGUNITA . "/api/EstadoCuenta" . $CondicionFiltro,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'ApiKey: ' . API_KEY,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $DATA = json_decode($response, true);

        return $DATA;

    }

    public function reportePagos($IDSocio,$IDPaypal="",$Encuesta = "",$PayPal = "")
    {
        $dbo = &SIMDB::get();

        date_default_timezone_set('America/Caracas');

        $Fecha = date("Y-m-d H:i:s");
        $datos_socio =  $dbo->fetchAll("Socio", " IDSocio = $IDSocio", "array");

        if(!empty($Encuesta)):
            $Tipo = 2;
            $sql = "SELECT * FROM EncuestaRespuesta WHERE IDEncuesta = 335 AND IDSocio = $IDSocio ORDER BY FechaTrCr DESC LIMIT 9";
            $qry = $dbo->query($sql);
            while($pregunta = $dbo->fetchArray($qry)):
                if($pregunta[IDPregunta] == 3809):
                    $BancoOrigen = $pregunta[Valor];
                elseif($pregunta[IDPregunta] == 3811):
                    $Referencia = $pregunta[Valor];
                elseif($pregunta[IDPregunta] == 3812):
                    $FechaPago = $pregunta[Valor];
                elseif($pregunta[IDPregunta] == 3813):
                    $BancoDestino = $pregunta[Valor];
                elseif($pregunta[IDPregunta] == 3814):
                    $Moneda = $pregunta[Valor];
                elseif($pregunta[IDPregunta] == 3815):
                    $Monto = $pregunta[Valor];
                elseif($pregunta[IDPregunta] == 3816):
                    $Descripcion = $pregunta[Valor];
                elseif($pregunta[IDPregunta] == 3817):
                    $Comprobante = $pregunta[Valor];
                elseif($pregunta[IDPregunta] == 3867):
                    $Accion = $pregunta[Valor];
                endif;
            endwhile;
    
            $url = PQR_ROOT . $Comprobante;

            $insert = " INSERT INTO ReportePagoLagunita (IDSocio, Fecha, Referencia, Monto, Tipo, `Url`, BancoOrigen, BancoDestino, FechaPago, Moneda, Descripcion) 
                        VALUES ($IDSocio, NOW(), '$Referencia', '$Monto', '$Tipo','$url','$BancoOrigen','$BancoDestino','$FechaPago','$Moneda','$Descripcion')";
            $qry = $dbo->query($insert);
            $ID = $dbo->lastID();

        elseif(!empty($PayPal)):
            $datos_paypal =  $dbo->fetchAll("PagosPaypal", " IDPagosPaypal = $IDPaypal", "array");

            $ID = $IDPaypal;
            $Monto = $datos_paypal[amount];
            $Referencia = $datos_paypal[id];
            $Descripcion = $datos_paypal[Descripcion];
            $url = "https://www.miclubapp.com/respuesta_transaccion_paypal.php?Consulta=1&IDPagosPaypal=$ID";
            $BancoOrigen = "PayPal";
            $BancoDestino = "PayPal";
            $FechaPago = $datos_paypal[create_time];

            $Tipo = 1;

        endif;

        $POST = 
        '{
            "idPago":'.$ID.',
            "cAccion":"'.$datos_socio["Accion"].'",
            "dMonto":'.$Monto.',
            "cReferencia":"'.$Referencia.'",
            "iTipo":" '.$Tipo.'",
            "cConcepto":"'.$Descripcion.'",
            "cUrl":"'.$url.'",
            "istatus":'.$Tipo.'",
            "dFechaReporte":"'.$Fecha.'",
            "cBancoOrigen":"'.$BancoOrigen.'",
            "cCuentaDestino":"'.$BancoDestino.'",
            "dFechaRegistro":"'.$FechaPago.'"
        }';       

        $curl = curl_init();
        
        curl_setopt_array($curl, array(            
            CURLOPT_URL => 'http://186.24.14.203:1530/api/ReportePago',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'ApiKey: ' . API_KEY,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl); 
        // echo $response;       
    }
}
