<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
if (!empty($_POST["IDVotacion"]) && !empty($_POST["IDVotacionEvento"])){
		$IDVotacionEventoVotacion =  $dbo->getFields( "VotacionEventoVotacion", "IDVotacionEventoVotacion", "IDVotacionEvento = '" . $_POST["IDVotacionEvento"] . "' and IDVotacion = '" . $_POST["IDVotacion"] . "'" );
		if((int)$IDVotacionEventoVotacion<=0){
			$sql_asociar="INSERT INTO VotacionEventoVotacion (IDVotacionEvento,IDVotacion,Activa,FechaTrCr,UsuarioTrCr)
			 							VALUES('".$_POST["IDVotacionEvento"]."','".$_POST["IDVotacion"]."','".$_POST["Valor"]."',NOW(),'".$_POST["IDUsuario"]."')";
			$dbo->query($sql_asociar);
		}
		else{
			$sql_asociar="UPDATE VotacionEventoVotacion SET  Activa = '".$_POST["Valor"]."' WHERE IDVotacionEventoVotacion = '".$IDVotacionEventoVotacion."'";
			$dbo->query($sql_asociar);
		}
	}
?>
["ok"]
