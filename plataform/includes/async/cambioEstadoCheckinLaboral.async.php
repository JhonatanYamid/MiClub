<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$nom_usu = SIMUser::get("IDUsuario") . " " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . SIMUser::get("IDUsuario") . "' ");
if ($_POST["tabla"] == "CheckinLaboral") {

	if (!empty($_POST["ID"])) {


		$Cambio = $_POST["Campo"];

		$sql_cambio = "UPDATE CheckinLaboral SET PersonaQueAprobo='" . $nom_usu . "',Estado = '" . $_POST["estado"] . "', ComentarioRevision = '" . $_POST['comentario'] . "',FechaCambioEstado=NOW() WHERE IDCheckinLaboral = '" . $_POST["ID"] . "'";
		$dbo->query($sql_cambio);
	}
	/* 	echo 'ok'; */
	//	["ok"]
}


if ($_POST["tabla"] == "CheckinLaboralHorasExtras") {

	if (!empty($_POST["ID"])) {
		$Cambio = $_POST["Campo"];

		$sql_cambio = "UPDATE CheckinLaboralHorasExtras SET  PersonaQueAprobo='" . $nom_usu . "',Estado = '" . $_POST["estado"] . "', ComentarioRevision = '" . $_POST['comentario'] . "',FechaCambioEstado=NOW() WHERE IDCheckinLaboral = '" . $_POST["ID"] . "'";

		$dbo->query($sql_cambio);
	}

	/* 	echo 'ok'; */
}
?>
["ok"]