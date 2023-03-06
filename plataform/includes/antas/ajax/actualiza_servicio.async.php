<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );

if (!empty($_POST[IDServicioMaestro]) && !empty($_POST[IDClub]) ){
	$estado_servicio = $dbo->getFields( "ServicioClub" , "Activo" , "IDServicioMaestro = '".$_POST[IDServicioMaestro]."' and IDClub = '".$_POST[IDClub]."' "); 
	if(empty($estado_servicio)):
		$sql_servicio= "INSERT INTO ServicioClub (IDClub, IDServicioMaestro, Activo) VALUES ('".$_POST[IDClub]."','".$_POST[IDServicioMaestro]."','S')";
		$nuevo_estado = "S";
	else:
		if($estado_servicio=="S")
			$nuevo_estado = "N";
		else	
			$nuevo_estado = "S";
			
		$sql_servicio= "Update ServicioClub Set Activo = '".$nuevo_estado."' Where  IDClub = '".$_POST[IDClub]."' and IDServicioMaestro = '".$_POST[IDServicioMaestro]."'";	
	endif;
	
	$dbo->query($sql_servicio);
	
	echo json_encode($nuevo_estado);
}
?>
