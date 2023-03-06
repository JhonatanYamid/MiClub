<?
	include( "procedures/general.php" );
	include( "procedures/reserva.php" );	
	$horas = SIMWebService::get_disponiblidad_elemento_servicio( $_POST["IDClub"], $_POST["IDServicio"], $_POST["fecha"], "","Admin");


	echo SIMUtil::view_reserva_app_grupos($horas);
?>	