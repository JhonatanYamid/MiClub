<?php
$client_id = '3MVG9W4cDaFe_AanHRlLhRgtgORz8yRE4u8RfHAqs4W9wgxhu.hnsEtTCjBQvnjNFnZxFBLSPCHI1mApS6mJt';
$redirect_uri = 'http://localhost/phpConnectToDB/csv/refreshFusionTable.php';
$client_secret = '583FD1947A7F6A3BAF7E67FABF6114ABAE3E0B5594128BD7F957055115351CAD';

define("CLIENT_ID", '3MVG9W4cDaFe_AanHRlLhRgtgORz8yRE4u8RfHAqs4W9wgxhu.hnsEtTCjBQvnjNFnZxFBLSPCHI1mApS6mJt');
define("CLIENT_SECRET", '583FD1947A7F6A3BAF7E67FABF6114ABAE3E0B5594128BD7F957055115351CAD');
define("SF_INSTANCE", 'https://test.salesforce.com.my.salesforce.com');
define("SF_USER", 'apiuser@federacioncolombianadegolf.com.fedegolfpc');
define("SF_PWD", 'GolfGolf&2022');

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://test.salesforce.com/services/oauth2/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_MAXREDIRS => 10, //default is 20
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => http_build_query(array(
        "grant_type" => 'password',
        "client_id" => CLIENT_ID,
        "client_secret" => CLIENT_SECRET,
        "username" => SF_USER,
        "password" => SF_PWD,
        "format" => "json"
    )),
    CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
));
$response_j = curl_exec($curl);
curl_close($curl);
var_dump($response_j);
