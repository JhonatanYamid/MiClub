<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

$sql_reporte = "Select * From CanjeSolicitud Where IDClub = '" . $_GET["IDClub"] . "'";
$result_reporte = $dbo->query($sql_reporte);

$nombre = "Canjes_" . date("Y_m_d");

$NumCanjes = $dbo->rows($result_reporte);

if ($NumCanjes > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumCanjes . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>Numero Canje</th>";
	$html .= "<th>Club Canje</th>";
	$html .= "<th>Socios Canje</th>";
	$html .= "<th>Accion Socio</th>";
	$html .= "<th>Cedula Socio</th>";
	$html .= "<th>Pais</th>";
	$html .= "<th>Ciudad</th>";
	$html .= "<th>Fecha Inicio</th>";
	$html .= "<th>Cantidad Dias</th>";
	$html .= "<th>Comentarios Club</th>";
	$html .= "<th>Comentarios Socio</th>";
	$html .= "</tr>";

	while ($Datos = $dbo->fetchArray($result_reporte)) {
		$html .= "<tr>";
		$html .= "<td>" . $Datos[Numero]  . "</td>";
		$html .= "<td>" . utf8_decode($dbo->getFields("ListaClubes", "Nombre", "IDListaClubes = $Datos[IDListaClubes]")) . "</td>";

		$socios = explode("|", $Datos[IDSocioBeneficiario]);
		$html .= "<td>";

		foreach ($socios as $id => $socio) :
			$html .= utf8_decode($dbo->getFields("Socio", "Nombre", "IDSocio = $socio") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = $socio"));
		endforeach;

		$html .= "</td>";
		$html .= "<td>" . utf8_decode($dbo->getFields("Socio", "Accion", "IDSocio = $Datos[IDSocio]")) . "</td>";
		$html .= "<td>" . utf8_decode($dbo->getFields("Socio", "NumeroDocumento", "IDSocio = $Datos[IDSocio]")) . "</td>";
		$html .= "<td>" . utf8_decode($dbo->getFields("Pais", "Nombre", "IDPais = $Datos[IDPais]")) . "</td>";
		$html .= "<td>" . utf8_decode($dbo->getFields("Ciudad", "Nombre", "IDCiudad = $Datos[IDCiudad]")) . "</td>";
		$html .= "<td>" . $Datos[FechaInicio] . "</td>";
		$html .= "<td>" . $Datos[CantidadDias] . "</td>";
		$html .= "<td>" . $Datos[ComentariosClub] . "</td>";
		$html .= "<td>" . $Datos[ComentariosSocio] . "</td>";
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
