<?php

class SIMWebServiceValleArribaAthetic 
{
 
    public function App_AutenticarUser($email, $Clave)
    {
       
$url = "https://vaac.dyndns.org/tisolucionesapi/socios/login";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Accept: application/json",
   "Content-Type: application/json",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data = <<<DATA
{
  "username": "$email",
  "password": "$Clave" 
}
DATA;

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
 


        return $resp;
    }
    
    
     
    public function App_ConsultarPerfil($token)
    {   
     /*$datos = self::App_AutenticarUser();
     $resp = json_decode($datos,true);
     $token=$resp[access_token]; */
     
       $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://vaac.dyndns.org/tisolucionesapi/socios/datosocio", 
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token,
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
  
        return $response;
    }
       
       
    public function App_ConsultarPerfilFamiliares($token)
    {  
     $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://vaac.dyndns.org/tisolucionesapi/socios/datosfamiliares", 
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token,
                    'Cookie: BrowserId=bCE8iOZ9EeuuQwEoDgDUiA; CookieConsentPolicy=0:0',
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
  
        return $response;
    }
       

    

}
