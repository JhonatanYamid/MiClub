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




if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
	$condicion_fecha = " and FechaTrCr >= '" . $_POST["FechaInicio"] . " 00:00:00'  and FechaTrCr <= '" . $_POST["FechaFin"] . " 23:59:59'";
}



$sql_reporte = "Select IDSocio,IDUsuario,IDSeccionObjetosPerdidos,IDEstadoObjetosPerdidos,IDUsuarioEntrega,Nombre,Descripcion,Telefono,Email,Valor,
FechaInicio,FechaFin,FechaEntrega,Observaciones,TipoReclamante,NombreParticular,DocumentoParticular
					From ObjetoPerdido
					Where IDClub = '" . $_POST["IDClub"] . "' " . $condicion_fecha . " Order By IDObjetoPerdido DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "Objetos_Perdidos:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>SOCIO / USUARIO</th>";
	$html .= "<th>NOMBRE</th>";
	$html .= "<th>CATEGORIA</th>";
	$html .= "<th>ESTADO</th>";
	$html .= "<th>DESCRIPCION</th>";
	$html .= "<th>TELEFONO</th>";
	$html .= "<th>EMAIL</th>";
	$html .= "<th>VALOR</th>";
	$html .= "<th>FECHA INICIO</th>";
	$html .= "<th>FECHA FIN</th>";
	$html .= "<th>FECHA ENTREGA</th>";
	$html .= "<th>OBSERVACIONES</th>";
	$html .= "<th>TIPO RECLAMANTE</th>";
	$html .= "<th>NOMBRE PARTICULAR</th>";
	$html .= "<th>DOCUMENTO PARTICULAR</th>";
	$html .= "<th>USUARIO QUE ENTREGA</th>";



	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		if ($Datos["IDSocio"] > 0) {
			$nombreSocio = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'");
		} else if ($Datos["IDUsuario"] > 0) {
			$nombreSocio = $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $Datos["IDUsuario"] . "'") . " " . $dbo->getFields("Usuario", "Apellido", "IDUsuario = '" . $Datos["IDUsuario"] . "'");
		} else
			$nombreSocio = "";

		$bitacora = "";

		$html .= "<tr>";

		$html .= "<td>" . $nombreSocio . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Nombre"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($dbo->getFields("SeccionObjetosPerdidos", "Nombre", "IDSeccionObjetosPerdidos = '" . $Datos["IDSeccionObjetosPerdidos"] . "'")) . "</td>";
		$html .= "<td>" . remplaza_tildes($dbo->getFields("EstadoObjetosPerdidos", "Nombre", "IDEstadoObjetosPerdidos = '" . $Datos["IDEstadoObjetosPerdidos"] . "'")) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Descripcion"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Telefono"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Email"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Valor"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["FechaInicio"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["FechaFin"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["FechaEntrega"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["Observaciones"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["TipoReclamante"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["NombreParticular"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($Datos["DocumentoParticular"]) . "</td>";
		$html .= "<td>" . remplaza_tildes($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $Datos["IDUsuarioEntrega"] . "'") . " " . $dbo->getFields("Usuario", "Apellido", "IDUsuario = '" . $Datos["IDUsuarioEntrega"] . "'")) . "</td>";


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