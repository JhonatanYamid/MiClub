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


$result_reporte = $dbo->query("SELECT REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo, pf.Nombre as producto, cf.Nombre as categoria, f.FechaCreacion as fecha, fp.Cantidad as cantidad,fp.Precio as valor_producto, fp.SubTotal as subtotal, fp.Descuento as valor_descuento, fp.Base as valor_neto, fp.Impuesto as valor_impuesto FROM FacturacionProducto fp, CategoriaFacturacion cf, ProductoFacturacion pf, Facturacion f, Club c WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE  f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin') and c.IDClub=f.IDClub and fp.IDFacturacion=f.IDFacturacion and pf.IDCategoriaFacturacion=cf.IDCategoriaFacturacion and fp.IDProductoFacturacion=pf.IDProductoFacturacion ORDER BY f.FechaCreacion desc");

 								
$result_reporte_general = $dbo->query("SELECT ProductoFacturacion.Nombre as producto, sum(Cantidad) as total FROM FacturacionProducto, ProductoFacturacion WHERE IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE IDClub in ($club)  and FechaCreacion>='$inicio' and FechaCreacion<='$fin') and ProductoFacturacion.IDProductoFacturacion=FacturacionProducto.IDProductoFacturacion GROUP by FacturacionProducto.IDProductoFacturacion ORDER by FacturacionProducto.Cantidad DESC ");

$nombre = "Reporte". date("Y_m_d");

$Num = $dbo->rows($result_reporte_general);

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
	$html .= "<th colspan='2' >CATEGORIA</th>";
	$html .= "<th colspan='3' >PRODUCTO</th>";
	$html .= "<th colspan='1' >CANTIDAD</th>";
	$html .= "<th colspan='2' >VALOR_PRODUCTO</th>";
	$html .= "<th colspan='2' >SUBTOTAL</th>"; 
	$html .= "<th colspan='1' >VALOR_DESCUENTO</th>";
	$html .= "<th colspan='2' >VALOR_NETO</th>";
	$html .= "<th colspan='2' >VALOR_IMPUESTO</th>";
	$html .= "<th colspan='2' >FECHA</th>";
	 
	$html .= "</tr>"; 
	while ($Datos = $dbo->fetchArray($result_reporte)) {
		$html .= "<tr>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["club"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["Consecutivo"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["producto"])) . "</td>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["categoria"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["cantidad"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["valor_producto"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["subtotal"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["valor_descuento"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["valor_neto"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["valor_impuesto"])) . "</td>";   
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["fecha"])) . "</td>";
 
  
	}
	
	$html .= "<tr colspan='21'>";
	$html .= "</tr>";
	$html .= "<th  rowspan='3' colspan='21'>";
	$html .= "</th>";
	$html .= "<th>";
	$html .= "</th>"; 
	$html .= "<th>";
	$html .= "</th>";
	
	$html .= "<tr>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "</tr>";
	 
	
	$html .= "<tr>";
	$html .= "<th colspan='4'>  $reporte_general  </th>";
        $html .= "</tr>";
	$html .= "<th colspan='4'>Sede:  " . $sede . " </th>";
	$html .= "<tr>";
	$html .= "<th colspan='4'>  " . $inicio . " - " . $fin . " </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='4'>Se encontraron " . $Num . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='3' >PRODUCTO</th>";
	$html .= "<th colspan='1' >CANTIDAD</th>";
 
	 
	$html .= "</tr>";

	while ($Datos1 = $dbo->fetchArray($result_reporte_general)) {
		$html .= "<tr>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos1["producto"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos1["total"])) . "</td>"; 
 
 
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
