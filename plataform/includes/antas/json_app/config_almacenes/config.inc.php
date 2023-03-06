<?php
include("../../config.inc.php");

//exit;
error_reporting( E_ERROR | E_PARSE );

define( "VERSION" , "4.0" );

$dbo =& SIMDB::get();
$dblink = $dbo->connect( "localhost" , "Caprino", "almacenescaprino" , "c4prin0" );

?>
