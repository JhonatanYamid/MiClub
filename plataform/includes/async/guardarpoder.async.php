<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
if (!empty($_POST["IDVotadorPadre"]) && !empty($_POST["IDVotadorOtorga"]) && !empty($_POST["IDVotacionEvento"])){

	$Consejero=$dbo->getFields( "VotacionVotante", "Consejero", "IDVotacionVotante = '".$_POST["IDVotadorOtorga"]."'  and IDVotacionEvento = '" . $_POST["IDVotacionEvento"] . "'" );
	if($Consejero=="S"){?>
	["consejero"]
	<?php
	exit;
	}
	//Verificar si el que otorga el poder ya no se lo otorgo a otro
	$IDVotacionPoder=$dbo->getFields( "VotacionPoder", "IDVotacionPoder", " (IDVotacionVotanteDelegaPoder = '" . $_POST["IDVotadorPadre"]. "' or IDVotacionVotante = '".$_POST["IDVotadorPadre"]."')  and IDVotacionEvento = '" . $_POST["IDVotacionEvento"] . "'" );
	if((int)$IDVotacionPoder>0){?>
["repetido"]
	<?php }
	else{
		$sql_insert="INSERT INTO VotacionPoder (IDClub,IDVotacionEvento,IDVotacionVotante,IDVotacionVotanteDelegaPoder,IDUsuarioRegistra,FechaTrCr,UsuarioTrCr)
								VALUES ('".$_POST["IDClub"]."','".$_POST["IDVotacionEvento"]."','".$_POST["IDVotadorOtorga"]."','".$_POST["IDVotadorPadre"]."','".$_POST["IDUsuarioRegistra"]."',NOW(),'".$_POST["IDUsuarioRegistra"]."') ";
		$dbo->query($sql_insert);?>
		["ok"]
		<?php
	 }
}
?>
