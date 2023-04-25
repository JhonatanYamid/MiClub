<?php
class SIMWebServicePasarelaLukaPay
{
    public function getToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_LUKAPAY . 'acciones' . $Accion,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'API-Key: ' . APIKEY_PLAYA_AZUL,
                'Username: usuario_comercio',
                'Password: password-comercio'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        $DATA = json_decode($response, true);
        return $DATA;
    }
    public function AutenticarAPILukaPay($IDClub)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("ConfiguracionClub", " IDClub = '$IDClub' ", "array");
        $UsuarioLukaPay = $datos_club['UsuarioLukaPay'];
        $ClaveLukaPay = $datos_club['ClaveLukaPay'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => URL_LUKAPAY_API_TEST . 'transaccion?transaccionId=' . 10488,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$UsuarioLukaPay:$ClaveLukaPay",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        $DATA = json_decode($response, true);
        return $DATA;
    }
    public function SondaAPILukaPay($IDClub, $ReferenciaTransaccion)
    {
        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("ConfiguracionClub", " IDClub = '$IDClub' ", "array");
        $UsuarioLukaPay = $datos_club['UsuarioLukaPay'];
        $ClaveLukaPay = $datos_club['ClaveLukaPay'];
        $credenciales = base64_encode("$UsuarioLukaPay:$ClaveLukaPay");

        $URLLukaPay = $datos_club['URLLukaPay'];
        $URLLukaPayPruebas = $datos_club['URLLukaPayPruebas'];
        $IsTestLukaPay = $datos_club['IsTestLukaPay'];

        if ($IsTestLukaPay == 1) {
            $url = $URLLukaPayPruebas;
        } else {
            $url = $URLLukaPay;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . 'transaccion?trazaId=' . $ReferenciaTransaccion,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic ' . $credenciales
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $Data = json_decode($response);
        $Data = json_decode(json_encode($Data), true);
        $Data = $Data[0];


        if ($Data['Exitoso'] == 1) {
            $Exito = 1;
            $Estado = "APROBADA";
        } else {
            $Exito = 2;
            $Estado = "RECHAZADA";
        }
        $ReferenciaTransaccion = $Data['TrazaId'];

        // convertir objeto a array?
        $datos_transaccion = $dbo->fetchAll('PagosLukaPay', "ReferenciaTransaccion='" . $ReferenciaTransaccion . "'", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_transaccion["IDClub"] . "' ", "array");

        $estado_transaccion = $Estado;
        $mensaje = ucfirst($Data['Descripcion']);
        $requestId = $Data['TransaccionId'];
        // Si hubo conversion de moneda
        $TasaConversion = (isset($Data['MontoOriginal']['TasaConversion'])) ? $Data['MontoOriginal']['TasaConversion'] : "";

        $sql_actualiza = "UPDATE PagosLukaPay SET Estado = '" . $estado_transaccion . "', MensajeEstado='" . $mensaje . "', ReferenciaLukaPay = '" . $requestId . "', Response = '" . $response . "', UsuarioTrEd = 'SondaLukaPay',FechaTrEd = NOW()  WHERE ReferenciaTransaccion = '" . $ReferenciaTransaccion . "'";
        $dbo->query($sql_actualiza);
        $responseSonda = "$Estado|$ReferenciaTransaccion";
        return $responseSonda;
    }
}
