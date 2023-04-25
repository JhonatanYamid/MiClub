<?php

class SIMWebServiceIpler
{
    public function get_token(){
        $url = "http://portal.ipler.edu.co:8080/rest-service-siip2/AutApp/authenticate";
        $headers = array(
            "Content-Type: application/json",
            "User-Agent: PostmanRuntime/7.29.0",
            "Accept: */*",
            "Accept-Encoding: gzip, deflate, br",
        );
        $data = array(
            "tokenRest" => "dGVjbm9sb2dpYTt3aXRob3V0cGFzc3dvcmQ=",
            "encriptar" => "N"
        );

        $cURL = curl_init($url);
        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HEADER, 0);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cURL, CURLOPT_POST, true); 
        curl_setopt($cURL, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true); 

        $apiResponse = curl_exec($cURL);

        curl_close($cURL);

        // $apiResponse - available data from the API request
        $result = json_decode($apiResponse,1);

        // $respuesta["message"] = "";
        // $respuesta["success"] = true;
        // $respuesta["response"] = $result['accessToken'];

        return $result['accessToken'];
        exit;
    }

    public function get_clave($IDSocio){

        $dbo = &SIMDB::get();

        if(!empty($IDSocio)){
            $success = false;
            $cont = 0;
            $socio = $dbo->getFields('Socio', array('NumeroDocumento', 'TipoSocio', 'AccionPadre'), 'IDSocio = ' . $IDSocio);

            //$nmDocumento = $socio['NumeroDocumento'];
            $nmDocumento = '1003953160';
            //$tipo = $socio['TipoSocio'];
            $tipo = 'Estudiante';
            //$idSede = substr($socio['AccionPadre'], 0, 1);
            $idSede = '5';

            if($tipo == 'Estudiante'){
                $url = "http://portal.ipler.edu.co:8080/rest-service-siip2/alumnosByIds?documento=$nmDocumento";
            }else{
                $url = "http://portal.ipler.edu.co:8080/rest-service-siip2/acudientesFilter?NumberID=$nmDocumento";
            }

            $sede = $idSede == '5' ? 'chico' : 'soledad';
            $token = SIMWebServiceIpler::get_token();

            $headers = array(
                "Authorization: Bearer $token+4",
                "Content-Type: application/json",
                "User-Agent: PostmanRuntime/7.29.0",
                "Accept: */*",
                "Accept-Encoding: gzip, deflate, br",
                "Connection: keep-alive",
                "sede: $sede"
            );

            while($success === false || $cont <= 2):

                $cURL = curl_init($url);
                curl_setopt($cURL, CURLOPT_URL, $url);
                curl_setopt($cURL, CURLOPT_HEADER, 0);
                curl_setopt($cURL, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true); 

                $apiResponse = curl_exec($cURL);

                curl_close($cURL);
        
                $result = json_decode($apiResponse,1);
                
                if(isset($result['contrasena_portal'])):
                    $clave = base64_decode($result['contrasena_portal']);
                    $success = true;
                else:
                    print_r($result);
                    echo $cont."--";
                endif;

                $cont++;
            endwhile;

            echo $clave;

            exit;
            

            header("Location: http://portal.ipler.edu.co:8080/portalusuarios-2022/#/login/$nmDocumento/$clave/$sede");

            // $respuesta["message"] = "";
            // $respuesta["success"] = true;
            // $respuesta["response"] = base64_decode($result['contrasena_portal']);

            // return $respuesta;
        }else{
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametrosparalaconfiguración', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        }
    }
}