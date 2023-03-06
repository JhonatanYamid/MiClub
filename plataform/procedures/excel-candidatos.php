<?php

require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
	$no_permitidas = array("Ã¡", "Ã©", "Ã", "Ã³", "Ãº", "á", "é", "í­", "á", "ú");
	$permitidas = array("&aacute;", "&eacute;", "&iacute;", "o", "&uacute;", "aaaa");
	$texto_final = str_replace($no_permitidas, $permitidas, $texto);
	return $texto_final;
}


$sql_reporte = "SELECT S.Nombre, S.Apellido, OC.*, O.Cargo, O.NombreEncargado,S.NombreJefe
								  FROM Oferta O,OfertaCandidato OC, Socio S
									Where S.IDSocio = O.IDSocio and O.IDOferta = OC.IDOferta and O.IDOferta = '" . $_GET["IDOferta"] . "'";
 								
$result_reporte = $dbo->query($sql_reporte);

$nombre = "Registros_Candidato:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>CARGO</th>";
	$html .= "<th>NOMBRE ENCARGADO</th>";
	$html .= "<th>FUNCIONARIO</th>";
	$html .= "<th>CEDULA</th>";
	$html .= "<th>JEFE INMEDIATO</th>";
	$html .= "<th>NOMBRE RECOMENDADO</th>";
	$html .= "<th>CARGO ACTUAL</th>";
	$html .= "<th>RAZON POSTULACION </th>";
	$html .= "<th>TELEFONO</th>";
	$html .= "<th>CORREO</th>";
	$html .= "<th>ARCHIVO</th>";
	$html .= "<th>ARCHIVO ADJUNTO</th>";
	$html .= "<th>FECHA</th>";
	$html .= "</tr>";

	while ($Datos = $dbo->fetchArray($result_reporte)) {
		$html .= "<tr>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($Datos["Cargo"])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($Datos["NombreEncargado"])) . "</td>";
		//$html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$Datos["IDSocio"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$Datos["IDSocio"]."'" ))) ."</td>";
			if($_GET["tipo"]=="socios"){
                $html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'")))  . "</td>";
                $html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields("Socio", "NumeroDocumento", "IDSocio = '" . $Datos["IDSocio"] . "'")))  . "</td>";
                
}
	if($_GET["tipo"]=="funcionarios"){
                $html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $Datos["IDUsuario"] . "'")))  . "</td>";
                $html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields("Usuario", "NumeroDocumento", "IDUsuario = '" . $Datos["IDUsuario"] . "'")))  . "</td>";
}               $html .= "<td>" . remplaza_tildes(utf8_encode($Datos["NombreJefe"])) . "</td>";
  
		$html .= "<td>" . remplaza_tildes(utf8_encode($Datos["NombreRecomendado"])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($Datos["CargoActual"])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($Datos["RazonPostulacion "])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($Datos["Telefono"])) . "</td>";
		$html .= "<td>" . remplaza_tildes(utf8_encode($Datos["CorreoElectronico"])) . "</td>";
		$html .= "<td>" . '<a href="' . OFERTA_ROOT . $Datos["Archivo"] . '">Ver archivo</a>' . "</td>";
		$html .= "<td>" . '<a href="' . OFERTA_ROOT . $Datos["Archivo2"] . '">Ver archivo adjunto</a>' . "</td>";
		$html .= "<td>" . $Datos["FechaTrCr"] . "</td>";
		$html .= "<td>" . $Fecha . "</td>";
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
