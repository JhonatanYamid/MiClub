<?php

require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$no_permitidas = array("Ã¡", "Ã©", "Ã", "Ã³", "Ãº", "á", "é", "í­", "á", "ú");
	$permitidas = array("&aacute;", "&eacute;", "&iacute;", "o", "&uacute;", "aaaa");
	$texto_final = str_replace($no_permitidas, $permitidas, $texto);
	return $texto_final;
}

$reporte=$_GET["reporte"];
$sede=$_GET["nombre"];
$inicio=$_GET["inicio"];
$fin=$_GET["fin"];
$orden=$_GET["orden"];
$club=$_GET["club"];

if($club==157):
$sqlSede = "SELECT r.IDClub as club FROM  ResolucionFactura as r, Club as c WHERE r.IDClub = c.IDClub AND r.Activo = 'S' AND c.IDClubPadre = $club";
$qrySede = $dbo->query($sqlSede);
$club="";
while($row = $dbo->fetchArray($qrySede)) {
$club.=$row["club"].",";
}

$club = substr($club, 0, -1);
$reporte_general=$_GET["reporte"]." General";

endif;


$result_reporte_general = $dbo->query("SELECT fp.IDFacturacion, c.IDClub as idclub,pf.Nombre as producto, fp.Precio as precio_producto, m.Nombre as mediopago, (fmp.ValorPagado) as debito, (fmp.ValorPagado-f.Total) as credito , s.NumeroDocumento as doc, REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo, f.FechaCreacion as fecha, pf.CuentaContable as cuenta_contable, pf.Codigo as codigo, fp.Cantidad as cantidad FROM FacturacionProducto fp, MediosPago m, Facturacion f, Club c, Socio s, ProductoFacturacion pf, FacturacionMediosPago fmp WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin')  and s.IDSocio=f.IDSocio and c.IDClub=f.IDClub and f.IDFacturacion=fp.IDFacturacion and m.IDMediosPago=fmp.IDMediosPago and pf.IDProductoFacturacion=fp.IDProductoFacturacion and fmp.IDFacturacion=f.IDFacturacion ORDER BY f.IDFacturacion desc ");

$result_reporte = $dbo->query("SELECT fp.IDFacturacion, c.IDClub as idclub,pf.Nombre as producto, fp.Precio as precio_producto, m.Nombre as mediopago, (fmp.ValorPagado) as debito, (fmp.ValorPagado-f.Total) as credito , s.NumeroDocumento as doc, REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo, f.FechaCreacion as fecha, pf.CuentaContable as cuenta_contable, pf.Codigo as codigo, fp.Cantidad as cantidad FROM FacturacionProducto fp, MediosPago m, Facturacion f, Club c, Socio s, ProductoFacturacion pf, FacturacionMediosPago fmp WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin')  and s.IDSocio=f.IDSocio and c.IDClub=f.IDClub and f.IDFacturacion=fp.IDFacturacion and m.IDMediosPago=fmp.IDMediosPago and pf.IDProductoFacturacion=fp.IDProductoFacturacion and fmp.IDFacturacion=f.IDFacturacion ORDER BY f.IDFacturacion desc ");

  

$nombre = "Reporte". date("Y_m_d");

$Num = $dbo->rows($result_reporte);

if ($Num > 0) {
   
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='21'>  $reporte_general  </th>";
        $html .= "</tr>";
	$html .= "<th colspan='21'>Sede:  " . $sede . " </th>";
	$html .= "<tr>";
	$html .= "<th colspan='21'>  " . $inicio . " - " . $fin . " </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='21'>Se encontraron " . $Num . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
		 
	 
	$html .= "<th colspan='3' >SEDE</th>";
	$html .= "<th colspan='1' >FACTURA</th>"; 
	$html .= "<th colspan='2' >ID_TERCERO</th>";
	$html .= "<th colspan='3' >PRODUCTO</th>";
	$html .= "<th colspan='1' >CUENTA_CONTABLE</th>";
	$html .= "<th colspan='1' >CODIGO</th>";
	$html .= "<th colspan='1' >CANTIDAD</th>";
	$html .= "<th colspan='2' >VALOR_PRODUCTO</th>"; 
	$html .= "<th colspan='2' >MEDIO_PAGO</th>";
	$html .= "<th colspan='1' >DEBITO</th>";
	$html .= "<th colspan='1' >CREDITO</th>";
	$html .= "<th colspan='1' >FECHA</th>";  
	
	   
		
	$html .= "</tr>"; 
	 
	while ($Datos = $dbo->fetchArray($result_reporte)) {
	 
  
          
		$html .= "<tr>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["club"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["Consecutivo"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["doc"])) . "</td>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["producto"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["cuenta_contable"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["codigo"])) . "</td>"; 
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["cantidad"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["precio_producto"])) . "</td>"; 
 
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["mediopago"])) . "</td>";
 
 
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["debito"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["credito"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["fecha"])) . "</td>"; 
	}
	$html .= "<tr>";
	 
	$html .= "</tr>";
 
	  
	$html .= "</table>";
	
	 



	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $html;
	exit();
}
