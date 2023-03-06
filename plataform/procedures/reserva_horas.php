<?php

//Si es coordinador tenemos que mirar que servicio se trae primero
//si es un usuario normal se traen las horas
$fecha = date("Y-m-d");
if( !empty( $_POST["fecha"] ) )
	$fecha = $_POST["fecha"];


if( !empty( $id ) )
{
	$url = URLROOT . "services/club.php?key=CEr0CLUB&action=getdisponiblidadelemento&IDClub=" . SIMUser::get("IDClub") . "&IDServicio=" . $datos_servicio["IDServicio"] . "&IDElemento=" . $id . "&Fecha=" . $fecha ;
	$str_disponibilidad = file_get_contents( $url );
	$array_disponibilidad = json_decode($str_disponibilidad, true);
}//end if
else
{
	header("Location:/index.php");
}//end else


print_r( $array_disponibilidad );
print_r( $datos_servicio );

?>