<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

if (!empty($_GET[IDInvitado])) :
	$condicion = " and SocioInvitadoEspecial.IDInvitado = '" . $_GET[IDInvitado] . "'";
endif;

if (!empty($_GET[gs_Accion])) :
	$condicion_soc = " and  (  S.Accion LIKE '%" . $_GET[gs_Accion] . "%' ) ";
endif;

if (!empty($_GET[gs_Predio])) :
	$condicion = " and S.Preio = '" . $_GET[gs_Predio] . "'";
endif;

if (!empty($_GET[gs_NombreSocio])) :
	$condicion_soc .= " and (S.Nombre like '%" . $_GET[gs_NombreSocio] . "%' or S.Apellido like '%" . $_GET[gs_NombreSocio] . "%') ";
endif;

if (!empty($_GET[gs_NombreInvitado])) :
	$condicion .= " and (I.Nombre like '%" . $_GET[gs_NombreInvitado] . "%' or I.Apellido like '%" . $_GET[gs_NombreInvitado] . "%' ) ";
endif;

if (!empty($_GET[gs_FechaInicio])) :
	$condicion .= " and SocioInvitadoEspecial.FechaInicio >= '" . $_GET[gs_FechaInicio] . "'";
endif;

if (!empty($_GET[gs_FechaFin])) :
	$condicion .= " and SocioInvitadoEspecial.FechaFin <= '" . $_GET[gs_FechaFin] . "'";
endif;


if (!empty($_GET["NumeroDocumento"])) {
	$sql_soc = "Select IDSocio From Socio S Where IDClub = '" . $_GET["IDClub"] . "' AND NumeroDocumento='" . $_GET["NumeroDocumento"] . "'";
	$result_soc = $dbo->query($sql_soc);
	$row_soc = $dbo->fetchArray($result_soc);
	$condicion .= " and SocioInvitadoEspecial.IDSocio in (" . $row_soc["IDSocio"]  . ") ";
}
//  else {

// 	$sql_soc = "Select * From Socio S Where IDClub = '" . $_GET["IDClub"] . "' " . $condicion_soc;
// 	$result_soc = $dbo->query($sql_soc);
// 	while ($row_soc = $dbo->fetchArray($result_soc)) :
// 		$array_id_socio[] = $row_soc["IDSocio"];
// 	endwhile;
// 	if (count($array_id_socio) > 0) :
// 		$condicion .= " and SocioInvitadoEspecial.IDSocio in (" . implode(",", $array_id_socio) . ") ";
// 	endif;
// }


//Con Log Acceso
$sql_reporte = "SELECT SocioInvitadoEspecial.*, I.NumeroDocumento, I.Email, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado, LA.*
				   FROM SocioInvitadoEspecial , Invitado I , LogAcceso LA
				   WHERE I.IDInvitado = SocioInvitadoEspecial.IDInvitado
				   AND SocioInvitadoEspecial.IDSocioInvitadoEspecial = LA.IDInvitacion
				   and SocioInvitadoEspecial.IDClub = '" . $_GET["IDClub"] . "' " . $condicion . " " . $where;

//Solo invitaciones
$sql_reporte2 = "SELECT SocioInvitadoEspecial.*, I.NumeroDocumento, I.Email, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado
				   FROM SocioInvitadoEspecial , Invitado I, Socio S
				   WHERE I.IDInvitado = SocioInvitadoEspecial.IDInvitado AND SocioInvitadoEspecial.IDSocio = S.IDSocio
				   and SocioInvitadoEspecial.IDClub = '" . $_GET["IDClub"] . "' " . $condicion . " " . $where;
$result_reporte = $dbo->query($sql_reporte2);

$nombre = "AccesoInvitado_" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
	$html  = "";
	$html .= "<table width='100%' border='1'>";
	$html .= "<tr>";
	$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
	$html .= "</tr>";
	$html .= "<tr>";
	$html .= "<th>ACCION</th>";
	$html .= "<th>NOMBRE SOCIO</th>";
	$html .= "<th>DOCUMENTO SOCIO</th>";
	$html .= "<th>TORRE SOCIO</th>";
	$html .= "<th>INVITADO</th>";
	$html .= "<th>DOCUMENTO INVITADO</th>";
	$html .= "<th>CORREO INVITADO</th>";
	$html .= "<th>TIPO</th>";
	$html .= "<th>CLASIFICACION</th>";
	$html .= "<th>DIRECCION</th>";
	$html .= "<th>TELEFONO</th>";
	$html .= "<th>PREDIO AL QUE SE DIRIGE</th>";
	$html .= "<th>OBSERVACION GENERAL</th>";
	$html .= "<th>OBSERVACION ESPECIAL</th>";
	$html .= "<th>ESTADO INVITADO</th>";
	$html .= "<th>CODIGO AUTORIZACION</th>";
	$html .= "<th>FECHA INICIO AUTORIZACION</th>";
	$html .= "<th>FECHA FIN AUTORIZACION</th>";
	$html .= "<th>TIPO MOVIMIENTO</th>";
	$html .= "<th>FECHA MOVIMIENTO</th>";
	$html .= "</tr>";
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $Datos["IDSocio"] . "' ", "array");
		$datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $Datos["IDInvitado"] . "' ", "array");


		$html .= "<tr>";
		$html .= "<td>" . $datos_socio["Accion"] . "</td>";
		$html .= "<td>" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "</td>";
		$html .= "<td>" . $datos_socio["NumeroDocumento"] . "</td>";
		$html .= "<td>" . $datos_socio["Torre"] . "</td>";
		$html .= "<td>" . utf8_encode($Datos["NombreInvitado"]) . "</td>";
		$html .= "<td>" . $Datos["NumeroDocumento"] . "</td>";
		$html .= "<td>" . $Datos["Email"] . "</td>";
		$html .= "<td>" . $dbo->getFields("TipoInvitado", "Nombre", "IDTipoInvitado = '" . $datos_invitado["IDTipoInvitado"] . "'") . "</td>";
		$html .= "<td>" . $dbo->getFields("ClasificacionInvitado", "Nombre", "IDClasificacionInvitado = '" . $datos_invitado["IDClasificacionInvitado"] . "'") . "</td>";
		$html .= "<td>" . utf8_encode($datos_invitado["Direccion"]) . "</td>";
		$html .= "<td>" . utf8_encode($datos_invitado["Telefono"]) . "</td>";
		$html .= "<td>" . utf8_encode($datos_socio["Predio"]) . "</td>";
		$html .= "<td>" . utf8_encode($datos_invitado["ObservacionGeneral"]) . "</td>";
		$html .= "<td>" . utf8_encode($datos_invitado["ObservacionEspecial"]) . "</td>";
		$html .= "<td>" . $dbo->getFields("EstadoInvitado", "Nombre", "IDEstadoInvitado = '" . $datos_invitado["IDEstadoInvitado"] . "'") . "</td>";
		$html .= "<td>" . $Datos["CodigoAutorizacion"] . "</td>";
		$html .= "<td>" . utf8_encode($Datos["FechaInicio"])  . "</td>";
		$html .= "<td>" . utf8_encode($Datos["FechaFin"])  . "</td>";
		if ($Datos["Entrada"] == "S") :
			$tipo_movimiento =  "Entrada";
			$fecha_movimiento = $Datos["FechaIngreso"];
		else :
			$tipo_movimiento = "Salida";
			$fecha_movimiento = $Datos["FechaSalida"];
		endif;
		$html .= "<td>" . $tipo_movimiento   . "</td>";
		$html .= "<td>" . $fecha_movimiento   . "</td>";
		$html .= "</tr>";
	}
	$html .= "</table>";
	// echo $html;
	// exit;
	//construimos el excel
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $html;
	exit();
}
