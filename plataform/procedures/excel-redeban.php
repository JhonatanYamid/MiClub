<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");


$FechaInicio = $_POST["FechaInicio"];
$FechaFin = $_POST["FechaFin"];

$where = "and FechaTransaccion BETWEEN '$FechaInicio' and '$FechaFin'";

$sql_reporte = "Select * From PagoCredibanco Where IDClub = '" . $_GET["IDClub"] . "' $where Order by IDPagoCredibanco Desc";
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
	$html .= "<th>Tipo Pago</th>";
	$html .= "<th>NumeroDocumento</th>";
	$html .= "<th>NombreSocio</th>";
	$html .= "<th>ApellidoSocio</th>";
	$html .= "<th>NumeroAccion</th>";
	$html .= "<th>Telefono</th>";
	$html .= "<th>Email</th>";
	$html .= "<th>NumeroFactura</th>";
	$html .= "<th>ValorPago</th>";
	$html .= "<th>FechaTransaccion</th>";
	$html .= "<th>Numero Transaccion</th>";
	$html .= "<th>Codigo Autorizacion</th>";
	$html .= "<th>RespuestaTransaccion</th>";

	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {
		if ($Datos["orderStatus"] == "2") {
			$estado = "Realizada con exito";
		} else {
			$estado = "transaccion fallida";
		}
		$html .= "<tr>";
		if ($Datos['Modulo'] == "CarteraPereira") {
			$Modulo = "Cartera";
		} elseif ($Datos['Modulo'] == "ConsumosPereira") {
			$Modulo = "Consumos";
		} else {
			$Modulo = "Otro";
		}
		$html .= "<td>" . $Modulo . "</td>";
		$html .= "<td>" . $Datos["NumeroDocumento"] . "</td>";
		$html .= "<td>" . $Datos["NombreSocio"] . "</td>";
		$html .= "<td>" . $Datos["ApellidoSocio"] . "</td>";
		$html .= "<td>" . (string)$Datos["NumeroAccion"] . "</td>";
		$html .= "<td>" . $Datos["Telefono"] . "</td>";
		$html .= "<td>" . $Datos["Email"] . "</td>";
		$Facturas = array();
		$arr_Facturas  = explode('/', $Datos['Factura']);
		foreach ($arr_Facturas as $Factura) {
			$IDFactura  = explode('|', $Factura);

			if (!empty($IDFactura[0]))
				array_push($Facturas, $IDFactura[0]);
		}
		$FacturasPagadas = implode(',', $Facturas);
		$html .= "<td>" . $FacturasPagadas . "</td>";
		$html .= "<td>" . $Datos["ValorPago"] . "</td>";
		$html .= "<td>" . $Datos["FechaTransaccion"] . "</td>";
		$html .= "<td>" . $Datos["NumeroTransaccion"] . "</td>";
		$html .= "<td>" . $Datos["NumeroFactura"] . "</td>";
		$html .= "<td>" . $estado . "</td>";
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
} else {

	echo '<script language="javascript">alert("lo sentimos, no hay registros entre estas fechas"); 
        </script>';
	echo '<script language="javascript"> 
          
        setTimeout(function(){
  window.location.href = "../pagosredeban.php";
}, 300);
        </script>';
}
