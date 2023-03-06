<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );	

$HoraInicio = $frm[HoraIncio];
$HoraFin = $frm[HoraFin];
$Fecha = $frm[Fecha];
$IDServicioElemento = $frm[IDServicioElemento];
$IDServicio = $frm[IDServicio];
$Razon = $frm[Razon];

if(!empty($IDServicioElemento))
	$CondicionElemento = " AND IDServicioElemento = '$IDServicioElemento'";

$SQLReservas = "SELECT * FROM ReservaGeneral WHERE IDServicio = '$IDServicio' AND Fecha = '$Fecha' AND Hora >= '$HoraInicio' AND Hora <= '$HoraFin' $CondicionElemento";
$QRYReservas = $dbo->query($SQLReservas);

while($Datos = $dbo->fetchArray($QRYReservas)):
	if (!empty($Datos["IDSocio"]) && !empty($Datos["IDReservaGeneral"]) && !empty($Datos["IDClub"])  ):
		$result =  SIMWebService::elimina_reserva_general($Datos["IDClub"],$Datos["IDSocio"],$Datos["IDReservaGeneral"],"Admin",$Razon);	
		$Respuesta .= $result["message"]."\n";
	endif;	
endwhile;
		
		
		 
?>
["<?php echo $Respuesta; ?>"]