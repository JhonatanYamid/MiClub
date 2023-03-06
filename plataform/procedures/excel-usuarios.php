<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");



$sql_reporte = "Select * From Usuario Where IDClub = '" . $_GET["IDClub"] . "' ";
$result_reporte = $dbo->query($sql_reporte);

$nombre = "UsuariosSistema_" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

//Consulto los campos dinamicos
$sql_campos = "SELECT IDCampoEditarUsuario, CED.Nombre
							 FROM CampoEditarUsuario CED
							 WHERE CED.IDClub='" . $_GET["IDClub"] . "'
							 Order by CED.Orden";
$r_campos = $dbo->query($sql_campos);
while ($r = $dbo->object($r_campos)) {
	$array_preguntas[$r->IDCampoEditarUsuario] = $r->Nombre;
}



if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>Numero Documento</th>";
	$html .= "<th>Nombre</th>";
	$html .= "<th>Telefono</th>";
	$html .= "<th>User</th>";
	$html .= "<th>Email</th>";
	$html .= "<th>Autorizado</th>";
	$html .= "<th>Permite Reservar</th>";
	$html .= "<th>Dispositivo</th>";
	$html .= "<th>Cargo</th>";
	$html .= "<th>Area</th>";
	$html .= "<th>Activo</th>";
	$html .= "<th>Perfil</th>";
	$html .= "<th>Tipo Usuario</th>";
	$html .= "<th>Usuario Creacion</th>";
	$html .= "<th>Fecha Creacion</th>";
	$html .= "<th>Usuario Edicion</th>";
	$html .= "<th>Fecha Edicion</th>";
	$html .= "<th>Fecha Fin Contrato</th>";
	foreach ($array_preguntas as $key_pregunta => $value_pregunta) {
		$html .= "<th>" . $value_pregunta . "</th>";
	}

	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		unset($array_respuesta);
		//Consulto los campos dinamicos
		$sql_campos = "SELECT CED.IDCampoEditarUsuario, SCES.Valor,CED.Nombre FROM CampoEditarUsuario CED, UsuarioCampoEditarUsuario SCES
									 WHERE SCES.IDCampoEditarUsuario=CED.IDCampoEditarusuario AND SCES.IDUsuario='" . $Datos["IDUsuario"] . "'
									 Group by SCES.IDCampoEditarUsuario
									 Order by CED.Orden";
		$r_campos = $dbo->query($sql_campos);
		while ($r = $dbo->object($r_campos)) {
			$array_respuesta[$r->IDCampoEditarUsuario] = $r->Valor;
		}


		$html .= "<tr>";
		$html .= "<td>" . $Datos["NumeroDocumento"] . "</td>";
		$html .= "<td>" . $Datos["Nombre"] . "</td>";
		$html .= "<td>" . $Datos["Telefono"] . "</td>";
		$html .= "<td>" . $Datos["User"]   . "</td>";
		$html .= "<td>" . $Datos["Email"]   . "</td>";
		$html .= "<td>" . $Datos["Autorizado"]   . "</td>";
		$html .= "<td>" . $Datos["PermiteReservar"]   . "</td>";
		$html .= "<td>" . $Datos["Dispositivo"]   . "</td>";
		$html .= "<td>" . $Datos["Cargo"]   . "</td>";
		$html .= "<td>" . $dbo->getFields("AreaUsuario", "Nombre", "IDAreaUsuario = '" . $Datos["IDAreaUsuario"] . "'")   . "</td>";
		$html .= "<td>" . $Datos["Activo"]   . "</td>";
		$html .= "<td>" . $dbo->getFields("Perfil", "Nombre", "IDPerfil = '" . $Datos["IDPerfil"] . "'")  . "</td>";
		$html .= "<td>" . $Datos["TipoUsuario"]   . "</td>";
		$html .= "<td>" . $Datos["UsuarioTrCr"]   . "</td>";
		$html .= "<td>" . $Datos["FechaTrCr"]   . "</td>";
		$html .= "<td>" . $Datos["UsuarioTrEd"]   . "</td>";
		$html .= "<td>" . $Datos["FechaTrEd"]   . "</td>";
		$html .= "<td>" . $Datos["FechaFinContrato"]   . "</td>";

		foreach ($array_preguntas as $key_pregunta => $value_pregunta) {
			$html .= "<th>" . $array_respuesta[$key_pregunta] . "</th>";
		}


		$html .= "</tr>";
	}
	$html .= "</table>";


	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $html;
	exit();
}
