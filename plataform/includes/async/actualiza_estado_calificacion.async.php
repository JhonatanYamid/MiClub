<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
if (!empty($_POST[ID])){
	$actualiza_foto= "Update ".$_POST["Tabla"]." Set Publicar = '". $_POST["Publicar"] ."' Where ID".$_POST["Tabla"]." = '". $_POST[ID] ."'";
	$dbo->query($actualiza_foto);
}
?>
["ok"]