<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
if (!empty($_POST["IDVotacionVotante"])){
	$datos_votante = $dbo->fetchAll( "VotacionVotante", " IDVotacionVotante = '" . $_POST["IDVotacionVotante"] . "' ", "array" );
	if($_POST["Presente"]=="S"){
		$TipoMovimiento="Entrada";
		$Mensaje="Se ha registrado su ingreso con exito";
		SIMUtil::enviar_notificacion_push_general($datos_votante["IDClub"],$datos_votante["IDSocio"],$Mensaje);
		//Verifico que la persona a la que se da ingreso no haya otorgado su poder
		$IDPoder=$dbo->getFields( "VotacionPoder", "IDVotacionPoder", "IDVotacionVotanteDelegaPoder = '" . $_POST["IDVotacionVotante"] . "' " );
		if(!empty($IDPoder)){?>
			["poder"]
			<?php
			exit;
	}
	else{
		//Verifico que solo haya un votante por predio
		$IDVotanteCasa=$dbo->getFields( "VotacionVotante", "IDVotacionVotante", "Presente = 'S' and NumeroCasa = '".$datos_votante["NumeroCasa"]."' and IDVotacionEvento = '".$datos_votante["IDVotacionEvento"]."'" );
		if(!empty($IDVotanteCasa)){ ?>
			["repetidocasa"]
		<?php
		exit;
	}
	}
	}
	else{
		$TipoMovimiento="Salida";
		$Mensaje="Se ha registrado su salida con exito";
		SIMUtil::enviar_notificacion_push_general($datos_votante["IDClub"],$datos_votante["IDSocio"],$Mensaje);

	}

	$actualiza_dispo= "Update VotacionVotante Set Presente = '". $_POST["Presente"] ."', IDUsuarioRegistra='".$_POST["IDUsuario"]."' Where IDVotacionVotante = '". $_POST["IDVotacionVotante"] ."'";
	$dbo->query($actualiza_dispo);


	$inserta_ingreso="INSERT INTO LogAccesoVotacion (IDClub,IDSocio,IDVotacionEvento,Tipo,Fecha,IDUsuario,UsuarioTrCr,FechaTrCr)
										VALUES('".$datos_votante["IDClub"]."','".$datos_votante["IDSocio"]."','".$datos_votante["IDVotacionEvento"]."','".$TipoMovimiento."',NOW(),'".$_POST["IDUsuario"]."','".$_POST["IDUsuario"]."',NOW()) ";
	$dbo->query($inserta_ingreso);
}

?>
["ok"]
