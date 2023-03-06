<?php
date_default_timezone_set('Europe/Madrid');
$dbhost="localhost";
$dbname="calendario";
$dbuser="root";
$dbpass="";
$con=mysql_connect($dbhost,$dbuser,$dbpass) or die("<h1>Imposible conectar a la base de datos.");
?>
