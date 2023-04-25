<?php
class SIMWebServiceRedeban
{

    public function get_token($IDClub)
    {



        define("API_LOGIN_DEV", "MATIMBASTG-RB-SERVER");
        define("API_KEY_DEV", "UkbNrTdyL0c0AwYkWex3Rlypqv9hx3");

        $server_application_code = API_LOGIN_DEV;
        $server_app_key = API_KEY_DEV;
        $date = new DateTime();
        $unix_timestamp = $date->getTimestamp();
        // $unix_timestamp = "1546543146";
        $uniq_token_string = $server_app_key . $unix_timestamp;
        $uniq_token_hash = hash('sha256', $uniq_token_string);
        $auth_token = base64_encode($server_application_code . ";" . $unix_timestamp . ";" . $uniq_token_hash);
        echo "TIMESTAMP: $unix_timestamp";
        echo "\nUNIQTOKENST: $uniq_token_string";
        echo "\nUNIQTOHAS: $uniq_token_hash";
        echo "\nAUTHTOKEN: $auth_token";
    }
}
