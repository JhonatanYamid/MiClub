<?
	include( "procedures/general.php" );	
	
	if(file_exists("/../procedures/general.php")):
		echo "Si";
	else:
		echo "No";
		
	endif;
	
	//$respuesta = SIMWebService::set_reserva_generalV2($_POST["IDClub"],$_POST["IDSocio"],$_POST["IDElemento"],$_POST["IDServicio"],$_POST["Fecha"],$_POST["Hora"],"",$_POST["Invitados"],"","",$_POST["Tee"],$_POST["IDDisponibilidad"],"","","",$_POST["IDTipoModalidadEsqui"],$_POST["IDAuxiliar"],$_POST["IDTipoReserva"],"",$_POST["IDBeneficiario"],$_POST["TipoBeneficiario"],"");		
	print_r($respuesta);
	echo json_encode("Terminado y guardado");
?>