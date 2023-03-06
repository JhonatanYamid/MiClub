<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
	$condicion_fecha = " and FechaTrCr >= '" . $_POST["FechaInicio"] . " 00:00:00'  and FechaTrCr <= '" . $_POST["FechaFin"] . " 23:59:59'";
}

$sql_reporte = "Select * From  PagoEcollect Where IDClub = '" . $_POST["IDClub"] . "'" . $condicion_fecha . " Order by IDPagoEcollect Desc";
$result_reporte = $dbo->query($sql_reporte);

$nombre = "TransaccionesCredibanco_" . date("Y_m_d");

$Num = $dbo->rows($result_reporte);

if ($Num > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $Num . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>NombreSocio</th>";
	$html .= "<th>ApellidoSocio</th>";
	$html .= "<th>NumeroAccion</th>";
	$html .= "<th>Telefono</th>";
	$html .= "<th>Email</th>";
	$html .= "<th>NumeroFactura</th>";
	$html .= "<th>ValorPago</th>";
	$html .= "<th>FechaTransaccion</th>";
	$html .= "<th>Numero Transaccion</th>";
	$html .= "<th>RespuestaTransaccion</th>";

	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {
		$datos_socio = $dbo->fetchAll("Socio", "IDSocio = $Datos[IDSocio]");


		$Telefono = $datos_socio[Celular];
		$NombreSocio = $datos_socio[Nombre];
		$ApellidoSocio = $datos_socio[Apellido];
		$NumeroAccion = $datos_socio[Accion];
		$Email = $datos_socio[CorreoElectronico];
		$NumeroFactura = $Datos[Factura];

		if (empty($NumeroFactura))
			$NumeroFactura = $Datos[TicketId];

		$Order = $Datos[ValorID];
		$FechaTransaccion = $Datos[BankProcessDate];
		$ValorPago = $Datos[TransValue];
		$EstadoTransaccion = $Datos[TranState];

		$html .= "<tr>";

		$html .= "<td>$NombreSocio</td>";
		$html .= "<td>$ApellidoSocio</td>";
		$html .= "<td>$NumeroAccion</td>";
		$html .= "<td>$Telefono</td>";
		$html .= "<td>$Email</td>";
		$html .= "<td>$NumeroFactura</td>";
		$html .= "<td>$ValorPago</td>";
		$html .= "<td>$FechaTransaccion</td>";
		$html .= "<td>$Order</td>";
		$html .= "<td>$EstadoTransaccion</td>";
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
