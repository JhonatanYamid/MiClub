<?php
/*
Simple iOS push notification with auth key
*/
	require_once('inc_jwt_helper.php');

	$certificado_p8=$pemFile =  "/home/http/miclubapp/httpdocs/admin/certificates/miclub/AuthKey_G23TP85S7M.p8";
	$authKey = $certificado_p8;
  	$arParam['teamId'] = '2YWSP8R8F7';// Get it from Apple Developer's page
 	$arParam['authKeyId'] = 'G23TP85S7M';
  	$arParam['apns-topic'] = 'com.22Cero2.ClubApp';
	$arClaim = ['iss'=>$arParam['teamId'], 'iat'=>time()];
	$arParam['p_key'] = file_get_contents($authKey);
	$arParam['header_jwt'] = JWT::encode($arClaim, $arParam['p_key'], $arParam['authKeyId'], 'RS256');

	// Sending a request to APNS
	$stat = push_to_apns($arParam, $ar_msg);
	if($stat == FALSE){
    // err handling
		exit();
	}

	exit();

// ***********************************************************************************
function push_to_apns($arParam, &$ar_msg){

	$arSendData = array();

	$url_cnt = "https://www.google.com";
	$arSendData['aps']['alert']['title'] = sprintf("Notification Title"); // Notification title
	$arSendData['aps']['alert']['body'] = sprintf("Body text"); // body text
	$arSendData['data']['jump-url'] = $url_cnt; // other parameters to send to the app

	$sendDataJson = json_encode($arSendData);

	$endPoint = 'https://api.development.push.apple.com/3/device'; // https://api.push.apple.com/3/device

	//　Preparing request header for APNS
	$ar_request_head[] = sprintf("content-type: application/json");
	$ar_request_head[] = sprintf("authorization: bearer %s", $arParam['header_jwt']);
	$ar_request_head[] = sprintf("apns-topic: %s", $arParam['apns-topic']);

	$dev_token = '5ef89716215feb79e8d17202e11c1fab9709bdce7386b19759b15d64181e0d92';  // Device token

	$url = sprintf("%s/%s", $endPoint, $dev_token);

	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $sendDataJson);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $ar_request_head);
	$response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if(empty(curl_error($ch))){
    // echo "empty curl error \n";
	}

	// Logging
  // After we need to remove device tokens which had response code 410.
  /*
		if(intval($httpcode) == 200){ fwrite($fp_ok, $output); }
		else{ fwrite($fp_ng, $output); }

		if(intval($httpcode) == 410){ fwrite($fp_410, $output); }
  */
	curl_close($ch);

	return TRUE;
}

?>
