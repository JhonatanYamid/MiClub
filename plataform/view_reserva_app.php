<?
	include( "procedures/general.php" );
	include( "procedures/reserva.php" );
	$horas = SIMWebService::get_disponiblidad_elemento_servicio( $_POST["IDClub"], $_POST["IDServicio"], $_POST["fecha"], "","Admin","","","","N");
	echo SIMUtil::view_reserva_app($horas,$_POST["ElementosSeleccionado"]);
?>
