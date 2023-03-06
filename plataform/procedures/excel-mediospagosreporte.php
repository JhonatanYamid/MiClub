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


$result_reporte = $dbo->query("SELECT fp.IDFacturacion, c.IDClub as idclub, m.IDMediosPago as idmediopago, m.Nombre as mediopago, (fp.ValorPagado) as totalpagado, s.IDSocio as idsocio, s.Nombre as nombre_socio, s.Apellido as apellido_socio, s.NumeroDocumento as doc, REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH')  as club , CONCAT('SETT',f.Consecutivo) as Consecutivo, f.FechaCreacion as fecha FROM FacturacionMediosPago fp, MediosPago m, Facturacion f, Club c, Socio s WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE IDClub in ($club)  and FechaCreacion>='$inicio' and FechaCreacion<='$fin') and fp.IDMediosPago=m.IDMediosPago and s.IDSocio=f.IDSocio and c.IDClub=f.IDClub and m.IDMediosPago=fp.IDMediosPago and fp.IDFacturacion=f.IDFacturacion ORDER BY f.FechaCreacion desc ");

 								
$result_reporte_general = $dbo->query("SELECT COUNT(*) as cantidad, m.Nombre as mediopago, fp.IDFacturacion, sum(fp.ValorPagado) as totalpagado FROM FacturacionMediosPago fp, MediosPago m, Facturacion f, Club c, Socio s WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE IDClub in ($club)  and FechaCreacion>='$inicio' and FechaCreacion<='$fin') and fp.IDMediosPago=m.IDMediosPago and s.IDSocio=f.IDSocio and c.IDClub=f.IDClub and m.IDMediosPago=fp.IDMediosPago and fp.IDFacturacion=f.IDFacturacion  GROUP by fp.IDMediosPago ORDER BY $orden desc");

$nombre = "Reporte". date("Y_m_d");

$Num = $dbo->rows($result_reporte_general);

if ($Num > 0) {
   
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='20'>  $reporte_general  </th>";
        $html .= "</tr>";
	$html .= "<th colspan='20'>Sede:  " . $sede . " </th>";
	$html .= "<tr>";
	$html .= "<th colspan='20'>  " . $inicio . " - " . $fin . " </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='20'>Se encontraron " . $Num . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='2' >SEDE_ID</th>";
	$html .= "<th colspan='3' >SEDE_NOMBRE</th>";
	$html .= "<th colspan='3' >CLIENTE</th>";
	$html .= "<th colspan='1' >ID_PERSONA</th>";
	$html .= "<th colspan='2' >CLIENTE_DOC</th>";
	$html .= "<th colspan='2' >ID_FACTURA</th>"; 
	$html .= "<th colspan='1' >MEDIOPAGO_ID</th>";
	$html .= "<th colspan='2' >MEDIOPAGO_NOMBRE</th>";
	$html .= "<th colspan='2' >VALOR_PAGO</th>";
	$html .= "<th colspan='2' >FECHA</th>";
	 
	$html .= "</tr>"; 
	while ($Datos = $dbo->fetchArray($result_reporte)) {
		$html .= "<tr>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["idclub"])) . "</td>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["club"])) . "</td>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["nombre_socio"]." ".$Datos["apellido_socio"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["idsocio"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["doc"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["Consecutivo"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["idmediopago"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["mediopago"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["totalpagado"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["fecha"])) . "</td>";
 
  
	}
	
	$html .= "<tr colspan='20'>";
	$html .= "</tr>";
	$html .= "<th  rowspan='3' colspan='20'>";
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
	$html .= "<th colspan='8'>  $reporte_general  </th>";
        $html .= "</tr>";
	$html .= "<th colspan='8'>Sede:  " . $sede . " </th>";
	$html .= "<tr>";
	$html .= "<th colspan='8'>  " . $inicio . " - " . $fin . " </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='8'>Se encontraron " . $Num . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='2' >VECES USADAS</th>";
	$html .= "<th colspan='3' >MEDIO DE PAGO</th>";
	$html .= "<th colspan='3' >VALOR TOTAL PAGADO</th>";
	 
	$html .= "</tr>";

	while ($Datos1 = $dbo->fetchArray($result_reporte_general)) {
		$html .= "<tr>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos1["cantidad"])) . "</td>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos1["mediopago"])) . "</td>"; 
		$html .= "<td colspan='3' >" . remplaza_tildes(utf8_encode($Datos1["totalpagado"])) . "</td>";
 
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
