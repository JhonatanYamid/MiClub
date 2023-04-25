<?php

class SIMWebServiceISolution
{
    public function TipoAccion()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => ENDPOINT_ISOLUTION . '/api/TipoAccion/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . AUTHORIZATION,
                'apiKey: ' . APIKEY,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function CrearPQR($IDPqr)
    {
        $dbo = SIMDB::get();

        $PQR = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");

        if($PQR[IDUsuario] == 0):
            $Socio = $dbo->fetchAll("Socio", " IDSocio = '" . $PQR[IDSocio] . "' ", "array");
        else:
            $Socio = $dbo->fetchAll("Usuario", " IDUsuario = '" . $PQR[IDSocio] . "' ", "array");
        endif;

        if($PQR[IDTipoPqr] == 22){
            $accion = 612;
        }elseif($PQR[IDTipoPqr] == 431){
            $accion = 8;
        }else{
            $accion = 611;
        }

        if($PQR[IDArea] == 142){
            $proceso = "9";
            $usuario = "9";
        }elseif($PQR[IDArea] == 143){
            $proceso = "12";
            $usuario = "219";
        }elseif($PQR[IDArea] == 144){
            $proceso = "12";
            $usuario = "24";
        }elseif($PQR[IDArea] == 145){
            $proceso = "14";
            $usuario = "3";
        }elseif($PQR[IDArea] == 158){
            $proceso = "8";
            $usuario = "11";
        }elseif($PQR[IDArea] == 167){
            $proceso = "6";
            $usuario = "19";
        }elseif($PQR[IDArea] == 539){
            $proceso = "16";
            $usuario = "6";
        }elseif($PQR[IDArea] == 540){
            $proceso = "13";
            $usuario = "17";
        }elseif($PQR[IDArea] == 541){
            $proceso = "18";
            $usuario = "5";
        }else{
            $proceso = "7";
            $usuario = "18";
        }

        $Datos = 
        '{
            "NombreCliente":"'.$Socio[Nombre].'",
            "TipoDocIdentidad": "CC",
            "Documento": "'.$Socio[NumeroDocumento].'",
            "Tipocliente": "Funcionario",
            "EmailCliente":"'.$Socio[CorreoElectronico].'",
            "TelefonoCliente": "'.$Socio[Celular].'",
            "TipoCaso": "'.$PQR[Asunto].'",
            "Descripcion":"'.$PQR[Descripcion].'",
            "CodTipoAccion": '.$accion.',
            "Codciudad":2,
            "CodResponsable":'.$usuario.',
            "CodProceso":'.$proceso.'
        }';          

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => ENDPOINT_ISOLUTION . '/api/pqr/',
            // CURLOPT_URL => 'http://186.31.134.27/IsolucionAPI/api/pqr/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $Datos,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . AUTHORIZATION,
                'apiKey: ' . APIKEY,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);  
        echo $response;
        $datos = json_decode($response,true);
        $campos = json_decode($datos[Entity], true);
        $numero = $campos[Consecutivo];
        
        $respuesta = '<p>
            <span style="font-family: Arial, sans-serif; text-align: justify;">
                <span style="font-size: 11pt; font-family: Arial, sans-serif;">
                    Buenos d&iacute;as
                </span>
            </span>
        </p>
        <p>
            <span style="font-family: Arial, sans-serif; text-align: justify;">
                <span style="font-size: 11pt; font-family: Arial, sans-serif;">
                    <o:p></o:p>
                </span>
            </span>
        </p>
        <p style="margin-top:0in;margin-right:0in;margin-bottom:7.5pt;margin-left:0in;&#10;text-align:justify">
            <span style="font-size: 11pt; font-family: Arial, sans-serif;">
                Reciba un cordial saludo, su PQR quedo registrada en el sistema de gesti&oacute;n de calidad como QUEJA Y RECLAMO&nbsp; No '.$numero.', en el transcurso de la semana le enviaremos respuesta de las acciones tomadas.
                <o:p></o:p>
            </span>
        </p>
        <p style="margin-top:0in;margin-right:0in;margin-bottom:7.5pt;margin-left:0in;&#10;text-align:justify">
            <span style="font-size: 11pt; font-family: Arial, sans-serif;">
                Cordialmente<o:p></o:p>
            </span>
        </p>
        <p style="margin-top:0in;margin-right:0in;margin-bottom:7.5pt;margin-left:0in;&#10;text-align:justify">
            <span style="font-size: 11pt; font-family: Arial, sans-serif;">
                Club Campestre Los Arrayanes&nbsp;<o:p></o:p>
            </span>
        </p>';
    }
}
