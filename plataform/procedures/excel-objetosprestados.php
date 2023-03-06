<?php

require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$no_permitidas = array("Ã¡", "Ã©", "Ã", "Ã³", "Ãº", "á", "é", "í­", "á", "ú");
	$permitidas = array("&aacute;", "&eacute;", "&iacute;", "o", "&uacute;", "aaaa");
	$texto_final = str_replace($no_permitidas, $permitidas, $texto);
	return $texto_final;
}

//condicion fechas
if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFinal"])) {
	$condicion_fecha = " and FechaTrCr  >= '" . $_POST["FechaInicio"] . " 00:00:00'  and FechaTrCr <= '" . $_POST["FechaFinal"] . " 23:59:59'";
}

//Estado
if (!empty($_POST["Estado"])) {
	$condicion_estado = " AND Estado='" . $_POST["Estado"] . "'";
}

$sql_reporte = "SELECT * From ObjetosPrestados
				Where IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . $condicion_estado . "  Order By IDObjetosPrestados DESC";

/* echo $sql_reporte;

print_r($_POST);
exit; */
$result_reporte = $dbo->query($sql_reporte);



$nombre = "Registros_Objetos_Prestados:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>CATEGORIA</th>";
	$html .= "<th>SOCIO</th>";
	$html .= "<th>ACCION</th>";
	$html .= "<th>LUGAR</th>";
	$html .= "<th>NOMBRE OBJETO PRESTADO</th>";
	$html .= "<th>CANTIDAD PRESTADA</th>";
	$html .= "<th>CANTIDAD PENDIENTE</th>";
	$html .= "<th>CANTIDAD ENTREGADA</th>";
	$html .= "<th>ESTADO</th>";
	$html .= "<th>FECHA PRESTAMO</th>";



	$html .= "</tr>";


	while ($Datos = $dbo->fetchArray($result_reporte)) {

		if ($Datos["Estado"] == "1") {
			$Estado = "Pendiente";
		} else {
			$Estado = "Entregado";
		}

		$html .= "<tr>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields("CategoriaObjetosPrestados", "NombreCategoriaObjeto", "IDCategoriaObjetosPrestados = '" . $Datos["IDCategoriaObjetosPrestados"] . "'"))) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_decode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocioPrestamo"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocioPrestamo"] . "'")))) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields("Socio", "Accion", "IDSocio = '" . $Datos["IDSocioPrestamo"] . "'"))) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields("LugarObjetosPrestados", "Nombre", "IDLugarObjetosPrestados = '" . $Datos["IDLugarObjetosPrestados"] . "'"))) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Nombre"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CantidadPrestada"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CantidadPendiente"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["CantidadEntregada"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Estado) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["FechaTrCr"]) . "</td>";

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
