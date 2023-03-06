<?php
require_once 'lib/Chat-API-master/src/whatsprot.class.php';

$username = "11111111111"; //Mobile Phone prefixed with country code so for india it will be 91xxxxxxxx
$password = "somepasswordstring";
 
$w = new WhatsProt($username, 0, "WhatsApp Messaging", true); //Name your application by replacing "WhatsApp Messaging"

$w->connect();

echo "aca3";
exit;


$w>loginWithPassword($password);


 
$target = '912222222222'; //Target Phone,reciever phone
$message = 'Your message comes here';
 
$w>SendPresenceSubscription($target); //Let us first send presence to user
$w>sendMessage($target,$message ); // Send Message


?>