<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
if (!empty($_POST[Telefono]) && !empty($_POST[IDClub])){
	$actualiza_tel= "Update Club Set Telefono = '". $_POST["Telefono"] ."' Where IDClub = '". $_POST[IDClub] ."'";	
	$dbo->query($actualiza_tel);
}
?>
["ok"]