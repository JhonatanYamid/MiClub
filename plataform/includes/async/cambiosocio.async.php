<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
if (!empty($_POST["IDSocio"])) {
	$Cambio = $_POST["Campo"];

	if ($Cambio == "Estado") {
		if ($_POST["Valor"] == 2 || $_POST["Valor"] == 3) :
			$cerrarSesion = ", SolicitarCierreSesion = 'S'";
		endif;

		$sql_cambio = "UPDATE Socio SET  IDEstadoSocio = '" . $_POST["Valor"] . "' $cerrarSesion WHERE IDSocio = '" . $_POST["IDSocio"] . "'";
	} elseif ($Cambio == "PermiteReserva") {
		$sql_cambio = "UPDATE Socio SET  PermiteReservar = '" . $_POST["Valor"] . "', FechaTrEd = NOW() WHERE IDSocio = '" . $_POST["IDSocio"] . "'";
	}

	$dbo->query($sql_cambio);

	//inactivar los clasificados del socio
	if ($_POST["Valor"] == 2) {
		$sql_inactivar_clasificados = "UPDATE Clasificado SET IDEstadoClasificado='4' WHERE IDSocio='" . $_POST["IDSocio"] . "'";
		$dbo->query($sql_inactivar_clasificados);
	}
}
?>
["ok"]