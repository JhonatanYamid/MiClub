<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");



$Condiciones = "";

if(!empty($_GET[IDCaddiesEcaddie])):	
	$Condiciones .= " AND IDCaddiesEcaddie = '$_GET[IDCaddiesEcaddie]'";
endif;

if(!empty($_GET[EstadoSolicitud])):
	$Condiciones .= " AND EstadoSolicitude = '$_GET[EstadoSolicitud]'";
endif;

if(!empty($_GET[IDServiciosCaddie])):	
	$Condiciones .= " AND IDServiciosCaddie = '$_GET[IDServiciosCaddie]'";
endif;

if(!empty($_GET[IDClubSeleccion])):
	$Condiciones .= " AND IDClubSeleccion = '$_GET[IDClubSeleccion]'";
endif;

if(!empty($_GET[TipoCaddie])):
	$Condiciones .= " AND TipoCaddie = '$_GET[TipoCaddie]'";
endif;

if(!empty($_GET[IDSocio])):
	$Condiciones .= " AND IDSocio = '$_GET[IDSocio]'";
endif;

$sql_reporte = "SELECT * FROM SolicitudCaddieRappi WHERE IDClub = '$_GET[IDClub]' AND Fecha >= '$_GET[FechaInicio]' AND Fecha <= '$_GET[FechaFin]' $Condiciones";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "Solicitudes"."_"."e-Caddy:". date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
			$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
			$html .= "<th>Socio</th>";
			$html .= "<th>Club Seleccionado</th>";
			$html .= "<th>Servicio</th>";
			$html .= "<th>Elemento</th>";
			$html .= "<th>Caddie</th>";
			$html .= "<th>Pagado</th>";
			$html .= "<th>Estado Solicitud</th>";
			$html .= "<th>Fecha</th>";
			$html .= "<th>Hora</th>";
			$html .= "<th>Tipo de Caddie</th>";
			$html .= "<th>Valor</th>";	

		$html .= "</tr>";
		while ($Datos = $dbo->fetchArray($result_reporte)) {
			$html .= "<tr>";
				$html .= "<td>".$dbo->getFields("Socio","Nombre","IDSocio = $Datos[IDSocio]") . " " . $dbo->getFields("Socio","Apellido","IDSocio = $Datos[IDSocio]") ."</td>";
				$html .= "<td>".$dbo->getFields("ListaClubes","Nombre","IDListaClubes = $Datos[IDClubSeleccion]")."</td>";
				$html .= "<td>".$dbo->getFields("ServiciosCaddie","Nombre","IDServiciosCaddie = $Datos[IDServiciosCaddie]")."</td>";
				$html .= "<td>".$dbo->getFields("ElementoServiciosCaddies","Nombre","IDElementoServiciosCaddies = $Datos[IDElementoServiciosCaddies]")."</td>";
				$html .= "<td>".$dbo->getFields("CaddiesEcaddie","Nombre","IDCaddiesEcaddie = $Datos[IDCaddiesEcaddie]")."</td>";
				$html .= "<td>$Datos[Pagado]</td>";
				$html .= "<td>$Datos[EstadoSolicitud]</td>";
				$html .= "<td>$Datos[Fecha]</td>";
				$html .= "<td>$Datos[Hora]</td>";
				$html .= "<td>$Datos[TipoCaddie]</td>";
				$html .= "<td>$Datos[Valor]</td>";
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
