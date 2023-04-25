<?php

class SIMWebServiceCampestrePereira
{
    public function Token()
    {
        $POST = '{
            "Usuario": "' . USUARIO_API_PEREIRA . '",
            "password": "' . PASS_API_PEREIRA . '"
        }';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_PEREIRA . '/api/login',
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

        return $DATA[token];
    }

    public function EstadoCuenta($Cedula)
    {
        $curl = curl_init();

        $Token = SIMWebServiceCampestrePereira::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_PEREIRA . '/api/EstadoCuenta/' . $Cedula,
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
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function Abono($Id, $Factura, $Cuota, $Valor, $FormaPago, $NumeroSoporte)
    {
        $POST = '[
            {
                "id":"' . $Id . '",
                "factura":"' . $Factura . '",
                "cuota":' . $Cuota . ',
                "valor":' . $Valor . ',
                "formapago":' . $FormaPago . ',
                "numerosoporte":"' . $NumeroSoporte . '"
            }
        ]';


        $curl = curl_init();

        $Token = SIMWebServiceCampestrePereira::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_PEREIRA . '/api/Abono',
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
        // echo $response;
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }

    public function Abono2($DATOS)
    {
        $dbo = SIMDB::get();
        $Cuota = 0;
        $Id = $DATOS['NumeroDocumento'];
        // $FormaPago = 9;
        $xmlResponse = $DATOS['xmlResponse'];
        $jsonResponse = $xmlResponse;
        $jsonResponse = json_decode($jsonResponse, true);
        $FormaPago = $jsonResponse['paymentWay'];
        $NumeroSoporte = $DATOS['NumeroTransaccion'];
        $pos = strpos($DATOS['Factura'], "/");

        if ($pos === false) :
            $POST = "[";
            $POST .= '{
                    "id":"' . $Id . '",
                    "factura":"' . $DATOS['Factura'] . '",
                    "cuota": 0,
                    "valor":"' . $DATOS['ValorPago'] . '",
                "formaPago":"' . $FormaPago . '",
                    "numerosoporte":"' . $NumeroSoporte . '"
                }';
            $POST .= "]";
        else :
            $ArregloNumeroFactura = explode("/", $DATOS['Factura']);
            $cont = count($ArregloNumeroFactura);
            $POST = "[";
            for ($i = 0; $i < count($ArregloNumeroFactura); $i++) :
                $DatosFactura = explode("|", $ArregloNumeroFactura[$i]);
                $Factura = $DatosFactura[0];
                $Valor = $DatosFactura[1];
                $Cuota = ($DatosFactura[2] > 0) ? $DatosFactura[2] : 0;

                if (!empty($Factura)) :
                    $POST .= '{
                            "id":"' . $Id . '",
                            "factura":"' . $Factura . '",
                            "cuota":' . $Cuota . ',
                            "valor":' . $Valor . ',
                            "formapago":"' . $FormaPago . '",
                            "numerosoporte":"' . $NumeroSoporte . '"
                        }';

                    if ($i >= 0 && $i < ($cont - 2)) :
                        $POST .= ",";
                    endif;
                endif;
            endfor;
            $POST .= "]";

        endif;
        $curl = curl_init();

        $Token = SIMWebServiceCampestrePereira::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_PEREIRA . '/api/Abono',
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
        // echo '<pre>';
        // var_dump($response);
        // die();
        // echo $response;
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }
    public function Factura($NumeroFactura)
    {
        $dbo = SIMDB::get();

        $FacturaConsumo = $dbo->fetchAll("FacturaConsumo", "NumeroDocumentoFactura = '" . $NumeroFactura . "' LIMIT 1", "array");
        $datos_pago = $dbo->fetchAll("PagoCredibanco", "NumeroFactura = '" . $NumeroFactura . "'", "array");
        $datos_socio = $dbo->fetchAll('Socio', "IDSocio = " . $FacturaConsumo['IDSocio'], "array");
        $Id = $datos_socio['NumeroDocumento'];
        // $FormaPago = 9;
        $Prefijo = "APP";
        $NumeroSoporte = $FacturaConsumo['NumeroTransaccion'];
        $fechaFactura = explode(' ', $FacturaConsumo['FechaTrCr']);
        $fechaFactura = $fechaFactura[0];
        $xmlResponse = $datos_pago['xmlResponse'];
        $json_response = json_decode($xmlResponse);

        $FacturasPagadas = explode('/', $FacturaConsumo['Detalle']);
        $detalle_consumo = explode('|', $FacturasPagadas[0]);
        $consecutivoControl = $detalle_consumo[0];

        $jsonResponse = $xmlResponse;
        $jsonResponse = json_decode($jsonResponse, true);
        $FormaPago = $jsonResponse['paymentWay'];

        $Valor = (!empty($datos_pago['ValorPago'])) ? (int)$datos_pago['ValorPago'] : 0;
        $Propina = (!empty($datos_pago['reserved14'])) ? (int)$datos_pago['reserved14'] : 0;

        $JsonFactura = '{
                "factura":"' . $Prefijo . $FacturaConsumo['IDFactura'] . '",
                "prefijo":"' . $Prefijo . '",
                "fechaFactura":"' . $fechaFactura . '",
                "formaPago":"' . $FormaPago . '",
                "formaPagoId":0,
                "valor":' . $Valor . ',
                "propina":' . $Propina . ',
                "consecutivoControl":' . $consecutivoControl . ',
                "detalle":[';

        foreach ($FacturasPagadas as  $FacturaPagada) {
            // 0=>consecutivoControl
            // 1=>id
            // 2=>consecutivo
            // 3=>productoId
            // 4=>nombreProducto

            $detalle_consumo = explode('|', $FacturaPagada);
            $JsonFactura .= '{
                    "id":' . $detalle_consumo[1] . ',
                    "consecutivo":' . $detalle_consumo[2] . ',
                    "productoId":"' . $detalle_consumo[3] . '"
                    },';
        }
        $JsonFactura .= '
                ]
                }';

        $POST = $JsonFactura;

        $curl = curl_init();

        $Token = SIMWebServiceCampestrePereira::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_PEREIRA . '/api/Factura',
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
        // echo $response;
        curl_close($curl);
        $DATA = json_decode($response, true);
        return $DATA;
    }


    public function Consumos($Cedula)
    {
        $curl = curl_init();

        $Token = SIMWebServiceCampestrePereira::Token();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_PEREIRA . '/api/Consumos/' . $Cedula,
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
        $DATA = json_decode($response, true);
        return $DATA;
    }
}
