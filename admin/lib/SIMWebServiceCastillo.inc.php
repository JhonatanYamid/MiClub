<?php
class SIMWebServiceCastillo
{
    public function obtener_foto($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();
        //CONSULTO CEDULA DEL SOCIO PARA HACER LA PETICION A CASTILLO DE AMAGUAÑA  DE CONSULTAR A LOS SOCIOS
        $NumeroDocumentoSocio = $dbo->getFields('Socio', 'NumeroDocumento', "IDSocio = $IDSocio");
        $urlPeticion = "http://181.39.25.76:44180/WebApiSIAccessCastillo/ConsultaDatosSocio/" . $NumeroDocumentoSocio;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlPeticion);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $response = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);

        if ($response["estado"] == 0) { //SOCIO ACTIVO

            if (!empty($response["foto"])) {
                //CONSULTO EL SERVICIO DE ELLOS QUE GENERA LA FOTO
                $Foto = $response["foto"];
                $urlPeticion2 = "http://181.39.25.76:44180/WebApiSIAccessCastillo/GetFotoSocio/" . $Foto;
                //echo $urlPeticion2;
                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_URL, $urlPeticion2);
                curl_setopt($ch2, CURLOPT_HEADER, false);
                curl_setopt($ch2, CURLOPT_HTTPGET, true);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                $response2 = curl_exec($ch2);
                $response2 = json_decode($response2, true, 512, JSON_BIGINT_AS_STRING);




                if (!empty($response2["mensaje"])) {

                    //Guardamos la foto de ellos en nuestro directorio 
                    $imagenEnBase64 = $response2["mensaje"];
                    $img = str_replace('data:image/png;base64,', '', $imagenEnBase64);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $file = SOCIO_DIR . uniqid() . '.png';
                    $success = file_put_contents($file, $data);

                    //PROD
                    $foto = str_replace('/home/http/miclubapp/httpdocs/admin/../file/socio/', '', $file);
                    //DEV
                    //$foto = str_replace('/home/http/miclubappdev/httpdocs/admin/../file/socio/', '', $file);

                    /*  echo $foto;
                    exit; */
                    $sql_actualiza_foto = "UPDATE Socio SET Foto='" . $foto . "' WHERE IDSocio='" . $IDSocio . "' AND IDClub='" . $IDClub . "'";
                    $dbo->query($sql_actualiza_foto);
                }
            }
        }
        return true;
    }
}
