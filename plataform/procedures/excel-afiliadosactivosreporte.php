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



$result_reporte_general = $dbo->query("SELECT REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo,  pf.Nombre as producto, cf.Nombre as categoria, f.FechaCreacion as fecha_inicia,f.FechaVence as fecha_vence, fp.Cantidad as cantidad,fp.Precio as valor_producto, s.IDSocio as idsocio, s.Nombre as nombre_socio, s.Apellido as apellido_socio, s.Genero as genero , s.NumeroDocumento as doc, s.FechaNacimiento as nacimiento, TIMESTAMPDIFF(YEAR,s.FechaNacimiento,CURDATE()) AS edad, s.Direccion as direccion, s.CorreoElectronico as email, s.Telefono as telefono, s.Celular as celular, TIMESTAMPDIFF(DAY, CURDATE(),f.FechaVence) AS dias_restantes FROM FacturacionProducto fp, Socio s, CategoriaFacturacion cf, ProductoFacturacion pf,  Facturacion f, Club c WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin') and c.IDClub=f.IDClub and fp.IDFacturacion=f.IDFacturacion and s.IDSocio=f.IDSocio and pf.IDCategoriaFacturacion=cf.IDCategoriaFacturacion and s.IDEstadoSocio=1 and fp.IDProductoFacturacion=pf.IDProductoFacturacion  and TIMESTAMPDIFF(DAY, CURDATE(),f.FechaVence)>= 0 ORDER BY f.FechaCreacion desc");

$result_reporte = $dbo->query("SELECT REPLACE(c.Nombre, 'SEDE', 'ACTIVE BODYTECH') as club , CONCAT('SETT',f.Consecutivo) as Consecutivo,  pf.Nombre as producto, cf.Nombre as categoria, f.FechaCreacion as fecha_inicia,f.FechaVence as fecha_vence, fp.Cantidad as cantidad,fp.Precio as valor_producto, s.IDSocio as idsocio, s.Nombre as nombre_socio, s.Apellido as apellido_socio, s.Genero as genero , s.NumeroDocumento as doc, s.FechaNacimiento as nacimiento, TIMESTAMPDIFF(YEAR,s.FechaNacimiento,CURDATE()) AS edad, s.Direccion as direccion, s.CorreoElectronico as email, s.Telefono as telefono, s.Celular as celular, TIMESTAMPDIFF(DAY, CURDATE(),f.FechaVence) AS dias_restantes FROM FacturacionProducto fp, Socio s, CategoriaFacturacion cf, ProductoFacturacion pf,  Facturacion f, Club c WHERE fp.IDFacturacion in (SELECT IDFacturacion FROM `Facturacion` WHERE f.IDClub in ($club)  and f.FechaCreacion>='$inicio' and f.FechaCreacion<='$fin') and c.IDClub=f.IDClub and fp.IDFacturacion=f.IDFacturacion and s.IDSocio=f.IDSocio and pf.IDCategoriaFacturacion=cf.IDCategoriaFacturacion and s.IDEstadoSocio=1 and fp.IDProductoFacturacion=pf.IDProductoFacturacion  and TIMESTAMPDIFF(DAY, CURDATE(),f.FechaVence)>= 0 ORDER BY f.FechaCreacion desc");

  

$nombre = "Reporte". date("Y_m_d");

$Num = $dbo->rows($result_reporte);

if ($Num > 0) {
   
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='30'>  $reporte_general  </th>";
        $html .= "</tr>";
	$html .= "<th colspan='30'>Sede:  " . $sede . " </th>";
	$html .= "<tr>";
	$html .= "<th colspan='30'>  " . $inicio . " - " . $fin . " </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='30'>Se encontraron " . $Num . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	
	 
	$html .= "<th colspan='3' >SEDE</th>";
	$html .= "<th colspan='1' >FACTURA</th>"; 
	$html .= "<th colspan='2' >CATEGORIA</th>";
	$html .= "<th colspan='3' >PRODUCTO</th>";
	$html .= "<th colspan='1' >FECHA_INICIO</th>";
	$html .= "<th colspan='1' >FECHA_VENCIMIENTO</th>";
	$html .= "<th colspan='1' >CANTIDAD</th>";
	$html .= "<th colspan='2' >VALOR_PRODUCTO</th>"; 
	$html .= "<th colspan='2' >CLIENTE_ID</th>";
	$html .= "<th colspan='3' >NOMBRE_CLIENTE</th>";
	$html .= "<th colspan='1' >GENERO</th>";
	$html .= "<th colspan='1' >DOCUMENTO_CLIENTE</th>";
	$html .= "<th colspan='1' >FECHA_NACIMIENTO</th>";
	$html .= "<th colspan='1' >EDAD</th>";
	$html .= "<th colspan='1' >DIRECCION</th>";
	$html .= "<th colspan='2' >EMAIL</th>";
	$html .= "<th colspan='1' >TELEFONO</th>";
	$html .= "<th colspan='1' >CELULAR</th>";
	$html .= "<th colspan='1' >DIAS_RESTANTES</th>";
	$html .= "<th colspan='1' >VALOR_PAGADO</th>";
	
	   
		
	$html .= "</tr>"; 
	 
	$sum_total=0;
	
	while ($Datos = $dbo->fetchArray($result_reporte)) {
	 
  $sum_total+=$Datos["valor_producto"]*$Datos["cantidad"];
   
  
		$html .= "<tr>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["club"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["Consecutivo"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["categoria"])) . "</td>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["producto"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["fecha_inicia"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["fecha_vence"])) . "</td>"; 
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["cantidad"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["valor_producto"])) . "</td>"; 
 
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["idsocio"])) . "</td>";
		$html .= "<td colspan='3'>" . remplaza_tildes(utf8_encode($Datos["nombre_socio"]." ".$Datos["apellido_socio"])) . "</td>";
 
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["genero"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["doc"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["nacimiento"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["edad"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["direccion"])) . "</td>";
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($Datos["email"])) . "</td>";
		$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["telefono"])) . "</td>";
	        $html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["celular"])) . "</td>";
	        $html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["dias_restantes"])) . "</td>";
	    	$html .= "<td colspan='1'>" . remplaza_tildes(utf8_encode($Datos["valor_producto"]*$Datos["cantidad"])) . "</td>"; 
 
 
	}
	$html .= "<tr>";
	$html .= "<th colspan='2'>   </th>";
        $html .= "</tr>";
	$html .= "<th colspan='2'> </th>";
	$html .= "<tr>";
	$html .= "<th colspan='2'>   </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th colspan='2'>TOTALES </th>";
	$html .= "</tr>";
	
	$html .= "<tr >";
  
	 
	$html .= "<th colspan='2' >VALOR TOTAL PAGADO</th>";
	$html .= "</tr>";
	   $html .= "<tr>";
	       
		$html .= "<td colspan='2'>" . remplaza_tildes(utf8_encode($sum_total)) . "</td>";
 
 
		$html .= "</tr>";
	         
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
 
	  
	$html .= "</table>";
	
	 



	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $html;
	exit();
}
