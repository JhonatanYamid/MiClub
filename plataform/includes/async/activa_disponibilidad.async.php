<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
if (!empty($_POST["IDDisponibilidad"])){
	$actualiza_dispo= "Update Disponibilidad Set Activo = '". $_POST["Activo"] ."' Where IDDisponibilidad = '". $_POST["IDDisponibilidad"] ."'";
	$dbo->query($actualiza_dispo);
	$actualiza_dispo= "Update ServicioDisponibilidad Set Activo = '". $_POST["Activo"] ."' Where IDDisponibilidad = '". $_POST["IDDisponibilidad"] ."'";
	$dbo->query($actualiza_dispo);
}
?>
["ok"]
