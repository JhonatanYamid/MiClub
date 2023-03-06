<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
if (!empty($_POST[IDReserva])){
	$actualiza_cancha= "Update ReservaGeneral Set Cancha = '". $_POST["Cancha"] ."', Equipo = '".$_POST["Equipo"]."', QuintoJugador = '".$_POST["QuintoJugador"]."' Where IDReservaGeneral = '". $_POST[IDReserva] ."'";
	$dbo->query($actualiza_cancha);
}
?>
["ok"]