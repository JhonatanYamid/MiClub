<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$texto = str_replace("ñ", "&ntilde;", $texto);
	$texto = str_replace("á", "&aacute;", $texto);
	$texto = str_replace("é", "&eacute;", $texto);
	$texto = str_replace("í", "&iacute;", $texto);
	$texto = str_replace("ó", "&oacute;", $texto);
	$texto = str_replace("ú", "&uacute;", $texto);
	return $texto;
}






$sql_reporte = "Select IDSocio,CorreoSocio,FechaTrCr
					From SolicitudSeguros
					   Order By FechaTrCr DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "Seguros_Corte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>NOMBRE SOCIO</th>";
	$html .= "<th>CORREO SOCIO</th>";
	$html .= "<th>CEDULA </th>";
	$html .= "<th>CELUAR </th>";
	$html .= "<th>TELEFONO </th>";
	$html .= "<th>FECHA REGISTRO</th>";


	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		$bitacora = "";
		unset($array_datos_seguimiento);
		$html .= "<tr>";
		$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
		$html .= "<td>" . $Datos["CorreoSocio"] . "</td>";
		$html .= "<td>" . remplaza_tildes($dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $Datos["IDSocio"] . "'")) . "</td>";
		$html .= "<td>" .  remplaza_tildes($dbo->getFields("Socio", "Celular", "IDSocio = '" . $Datos["IDSocio"] . "'")) . "</td>";
		$html .= "<td>" . remplaza_tildes($dbo->getFields("Socio", "Telefono", "IDSocio = '" . $Datos["Telefono"] . "'")) . "</td>";
		$html .= "<td>" . $Datos["FechaTrCr"] . "</td>";


		$html .= "</tr>";
	}
	$html .= "</table>";


	//construimos el excel
	header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
	<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>

	<body>
		<?php
		echo $html;
		?>
	</body>

	</html>
<?php
	exit();
} else {
	echo "NO HAY RESULTADOS EN LAS FECHAS SELECCIONADAS";
}
?>