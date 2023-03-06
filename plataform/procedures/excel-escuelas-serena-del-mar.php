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






$sql_reporte = "Select *
					From EscuelaSerenaDelMar
					   Order By FechaTrCr DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "Cuestionario_Escuelas_Serena_Del_Mar:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>NOMBRE SOCIO </th>";
	$html .= "<th>CLASE</th>";
	$html .= "<th>NOMBRE PERSONA INSCRITA </th>";
	$html .= "<th>APELLIDOS PERSONA INSCRITA </th>";
	$html .= "<th>TIPO DOCUMENTO</th>";
	$html .= "<th>NUMERO DOCUMENTO</th>";
	$html .= "<th>EDAD</th>";
	$html .= "<th>CELULAR</th>";
	$html .= "<th>CORREO ELECTRONICO </th>";
	$html .= "<th>TIPO DE SALUD</th>";
	$html .= "<th>PARENTESCO</th>";
	$html .= "<th>NOMBRES PADRES</th>";
	$html .= "<th>APELLIDOS PADRES</th>";
	$html .= "<th>CEDULA PADRES</th>";
	$html .= "<th>CELULAR PADRES</th>";
	$html .= "<th>CORREO PADRES</th>";
	$html .= "<th>EDIFICIO</th>";
	$html .= "<th>NUMERO DE TORRE</th>";
	$html .= "<th>NUMERO DE APTO</th>";
	$html .= "<th>VALOR</th>";
	$html .= "<th>PAGADO</th>";
	$html .= "<th>FECHA REGISTRO</th>";



	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {




		$bitacora = "";
		unset($array_datos_seguimiento);
		$html .= "<tr>";


		$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Clase"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Nombres"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Apellidos"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["DocumentoIdentificacion"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["NumeroDocumentoIdentificacion"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Edad"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Celular"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CorreoElectronico"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["TipoDeSalud"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Parentesco"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["NombresPadres"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["ApellidosPadres"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CedulaPadres"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CelularPadres"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CorreoPadres"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Edificio"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["NumeroDeTorre"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["NumeroDeApto"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Valor"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Pagado"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["FechaTrCr"]) . "</td>";





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