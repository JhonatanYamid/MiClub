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






$sql_reporte = "Select Categoria,NombreAcudiente,CedulaAcudiente,CorreoElectronicoAcudiente,NumeroCelular,
NombreNino,TarjetaIdentidad,FechaNacimientoNino,NombreClub,DepartamentoCiudad,Rama,Ranking,EstadoTransaccion,Foto,Foto2,
CodigoRespuesta,FechaTrEd
					From TorneoChiquitinesDeTenis WHERE CodigoRespuesta <>''
					   Order By IDTorneoChiquitinesDeTenis DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "Torneo_Chiquitines_De_Tenis:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>NOMBRE ACUDIENTE</th>";
	$html .= "<th>CATEGORIA</th>";
	$html .= "<th>CEDULA ACUDIENTE </th>";
	$html .= "<th>CORREO </th>";
	$html .= "<th>CELULAR </th>";
	$html .= "<th>NOMBRE NIÑO</th>";
	$html .= "<th>TARJETA IDENTIDAD</th>";
	$html .= "<th>FECHA NACIMIENTO NIÑO</th>";
	$html .= "<th>NOMBRE CLUB</th>";
	$html .= "<th>DEPARTAMENTO O CIUDAD</th>";
	$html .= "<th>RAMA</th>";
	$html .= "<th>RANKING</th>";
	$html .= "<th>ESTADO TRANSACCION</th>";
	$html .= "<th>CODIGO</th>";
	$html .= "<th>FECHA INSCRIPCION</th>";
	$html .= "<th>FOTO (Frente/cara1)</th>";
	$html .= "<th>FOTO (Respaldo/cara2)</th>";


	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {


		if ($Datos["Categoria"] == 1) {
			$Categoria = "6 años masculino";
		}
		if ($Datos["Categoria"] == 2) {
			$Categoria = "6 años femenino";
		}
		if ($Datos["Categoria"] == 3) {
			$Categoria = "7 años masculino";
		}
		if ($Datos["Categoria"] == 4) {
			$Categoria = "7 años femenino";
		}
		if ($Datos["Categoria"] == 5) {
			$Categoria = "8 años masculino";
		}
		if ($Datos["Categoria"] == 6) {
			$Categoria = "8 años femenino";
		}
		if ($Datos["Categoria"] == 7) {
			$Categoria = "9 años masculino";
		}
		if ($Datos["Categoria"] == 8) {
			$Categoria = "9 años femenino";
		}
		if ($Datos["Categoria"] == 9) {
			$Categoria = "10 años masculino";
		}
		if ($Datos["Categoria"] == 10) {
			$Categoria = "10 años femenino";
		}
		if ($Datos["Categoria"] == 11) {
			$Categoria = "12 años masculino";
		}
		if ($Datos["Categoria"] == 12) {
			$Categoria = "12 años femenino";
		}
		if ($Datos["Categoria"] == 13) {
			$Categoria = "12 años masculino con ranking";
		}
		if ($Datos["Categoria"] == 14) {
			$Categoria = "12 años femenino con ranking";
		}




		$bitacora = "";
		unset($array_datos_seguimiento);
		$html .= "<tr>";


		$html .= "<td>" . remplaza_tildes($Datos["NombreAcudiente"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Categoria) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CedulaAcudiente"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CorreoElectronicoAcudiente"]) . "</td>";
		$html .= "<td>" . $Datos["NumeroCelular"] . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["NombreNino"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["TarjetaIdentidad"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["FechaNacimientoNino"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["NombreClub"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["DepartamentoCiudad"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Rama"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Ranking"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["EstadoTransaccion"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CodigoRespuesta"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["FechaTrEd"]) . "</td>";

		$foto = TORNEOCHIQUITINESDETENIS_ROOT . $Datos["Foto"];
		$html .= "<td>" . "<a href='" . $foto . "'>Ver imagen: </a>" . "</td>";

		$foto2 = TORNEOCHIQUITINESDETENIS_ROOT . $Datos["Foto2"];
		$html .= "<td>" . "<a href='" . $foto2 . "'>Ver imagen: </a>" . "</td>";



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