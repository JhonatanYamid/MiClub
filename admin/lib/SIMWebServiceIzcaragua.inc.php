<?php
    
    class SIMWebServiceIzcaragua
    {       

        public function ServicioDeudaSocio($IDSocio)
        {
            $dbo = SIMDB::get();

            $SQLSocio = "SELECT Accion FROM Socio WHERE IDSocio = $IDSocio";
            $QRYSocio = $dbo->query($SQLSocio);
            $DatosSocio = $dbo->fetchArray($QRYSocio);

            $Accion = $DatosSocio[Accion];

            $InfoAccion = explode("-",$Accion);

            $AccionConsulta = $InfoAccion[0]."-".$InfoAccion[1];

            $GET = URL_IZCARAGUA ."/index.php?co_cli=" . $AccionConsulta;
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL =>  $GET,
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

            $Datos = json_decode($response,true);

            return $Datos;

        }
        public function DeudaSocio($IDSocio)
        {
            $dbo = SIMDB::get();            

            $Datos = SIMWebServiceIzcaragua:: ServicioDeudaSocio($IDSocio);            
            
            if((string)trim($Datos[0][deuda]) == "TRUE"):
                $respuesta[message] = "Favor Comunicarse con el Dpto de Cobranza.";
                $respuesta[success] = false;
                $respuesta[response] = $Datos;
            else:
                $respuesta[message] = "NO HAY DEUDA";
                $respuesta[success] = true;
                $respuesta[response] = $Datos;
            endif;

            return $respuesta;
        }
    }
