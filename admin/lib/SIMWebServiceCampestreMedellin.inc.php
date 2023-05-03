<?php

class SIMWebServiceCampestreMedellin
{
    public function Token()
    {
        $POST = '{
            "Usuario": "' . USUARIO_API_MEDELLIN . '",
            "password": "' . PASS_API_MEDELLIN . '"
        }';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_MEDELLIN . '/api/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $POST,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $DATA = json_decode($response, true);

        return $DATA['token'];
    }

    public function FacturarServicio($IDClub, $IDReserva)
    {
        $dbo = &SIMDB::get();
        $detalle = array();
        $qry = $dbo->query("SELECT IDReservaGeneral,ValorPagado,IDServicio,IDSocio,MedioPago,IDServicioElemento,IDAuxiliar FROM ReservaGeneral  WHERE IDClub = '" . $IDClub . "' AND IDReservaGeneral = '$IDReserva' LIMIT 1");
        $r = $dbo->fetchArray($qry);
        $IDServicio = $r['IDServicio'];
        $IDSocio = $r['IDSocio'];
        $IDReservaGeneral = $r['IDReservaGeneral'];
        $IDServicioElemento = $r['IDServicioElemento'];
        $socio = $dbo->getFields("Socio", "NumeroDocumento", "IDClub = '$IDClub' AND IDSocio = '$IDSocio' LIMIT 1");
        $servicio = $dbo->getFields("Servicio", "AlmacenId", "IDServicio = '$IDServicio' LIMIT 1");
        $servicioElemento = $dbo->fetchAll("ServicioElemento", "IDServicioElemento = '$IDServicioElemento'", "array");
        $pagosWomppi = $dbo->fetchAll("PagosWompi", "IDReserva = '$IDReservaGeneral'", "array");
        $detalle[] = array("productoId" => $servicioElemento['ProductoId'], "cantidad" => 1, "valor" => $r['ValorPagado']);
        $POST = '{
                "almacenId":"' . $servicio . '",
                "terceroId":"' . $socio . '",
                "valorTotal":' . $r['ValorPagado'] . ',
                "formaPago":"' . $r['MedioPago'] . '",
                "comprobante":"' . $pagosWomppi['ReferenciaWompi'] . '",
                "detalle":' . json_encode($detalle) . '
            }';

        if ($r['IDAuxiliar'] != '' && $servicio == '005') {
            $curl = curl_init();

            $Token = SIMWebServiceCampestreMedellin::Token();

            curl_setopt_array($curl, array(
                CURLOPT_URL => API_MEDELLIN . '/api/FacturarServicio',
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
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $DATA = json_decode($response)->mensaje;
            $frm["almacenId"] = $servicio;
            $frm["terceroId"] = $socio;
            $frm["valorTotal"] = $r['ValorPagado'];
            $frm["formaPago"] = $r['MedioPago'];
            $frm["comprobante"] = $pagosWomppi['ReferenciaWompi'];
            $frm["detalle"] = json_encode($detalle);
            $frm["IDClub"] =$IDClub;
            $frm["IDReservaGeneral"] =$IDReserva;
            $frm["Respuesta"] =$DATA;
            $id = $dbo->insert($frm, "LogFacturarServicioMedellin", "IDLogFacturarServicioMedellin");
            
            $respuesta["message"] = ($DATA) ? $DATA : "Facturacion Exitosa";
            $respuesta["success"] = true;
            $respuesta["response"] = null;

            
            return $respuesta;
        } else {
            return false;
        }
    }
}
