<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$no_permitidas = array("Ã¡", "Ã©", "Ã", "Ã³", "Ãº", "á", "é", "í­", "á", "ú");
	$permitidas = array("&aacute;", "&eacute;", "&iacute;", "o", "&uacute;", "aaaa");
	$texto_final = str_replace($no_permitidas, $permitidas, $texto);
	return $texto_final;
}
$servicio = $_POST['ids'];


$sql_reporte = "Select * From ServicioCierre
					Where IDServicio ='" . $servicio . "'  Order By FechaTrCr ASC";
$result_reporte = $dbo->query($sql_reporte);

$nombre = "Fechas_Cierre:" . date("Y_m_d");

/* echo $sql_reporte;
	exit; */

$Num = $dbo->rows($result_reporte);

if ($Num > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $Num . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>Fecha Inicio</th>";
	$html .= "<th>Fecha Fin</th>";
	$html .= "<th>Hora Inicio</th>";
	$html .= "<th>Hora Fin</th>";
	$html .= "<th>Descripcion</th>";
	$html .= "<th>Elementos</th>";
	$html .= "<th>Creado por</th>";
	$html .= "<th>Editado por</th>";
	$html .= "</tr>";

	$style = 'mso-number-format:"@";';
	while ($Datos = $dbo->fetchArray($result_reporte)) {


		$html .= "<tr>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($Datos["FechaInicio"])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($Datos["FechaFin"])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($Datos["HoraInicio"])) . "</td>";

		$html .= "<td>" . remplaza_tildes(utf8_decode($Datos["HoraFin"])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($Datos["Descripcion"])) . "</td>";

		$array_elementos_guardados = explode("|", $Datos["IDServicioElemento"]);
		$elementos = "";
		$r_elemento = &$dbo->all("ServicioElemento", "IDServicio = '" . $Datos["IDServicio"]  . "'");
		while ($row_elemento = $dbo->object($r_elemento)) {
			if (in_array($row_elemento->IDServicioElemento, $array_elementos_guardados))
				$elementos .= $row_elemento->Nombre . "<br>";
		}
		if ($Datos["Tee1"] == "S")
			$elementos .= " - Tee 1";
		elseif ($Datos["Tee10"] == "S")
			$elementos .= " - Tee 10";

		$html .= "<td>" . remplaza_tildes(utf8_decode($elementos)) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($Datos["UsuarioTrCr"])) . " " . remplaza_tildes(utf8_decode($Datos["FechaTrCr"])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode($Datos["UsuarioTrEd"])) . " " . remplaza_tildes(utf8_decode($Datos["FechaTrEd"])) . "</td>";
		$html .= "</tr>";
	}
	$html .= "</table>";

	/* echo $html;
		exit; */

	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $html;
	exit();
}
