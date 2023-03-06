<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
require(LIBDIR . "SIMWebServiceAccesos.inc.php");

SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

if ($frm["Tipo"] != "Salida") :
	$respuesta = SIMWebServiceAccesos::set_entrada_invitado(SIMUser::get("club"), $frm["IDSocioInvitadoEspecial"], "InvitadoAcceso", $frm["Mecanismo"], SIMUser::get("IDUsuario"), $frm["OtrosCampos"], $frm['PredioIngresoSocio']);

//$sql_ingreso = $dbo->query("Update SocioInvitadoEspecial Set Ingreso = 'S', FechaIngreso = NOW() Where IDSocioInvitadoEspecial = '".$frm["IDSocioInvitadoEspecial"]."' and IDpadre = '".$datos_invitacion_especial["IDInvitado"]."'");
/*
		$datos_invitacion_especial = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $frm["IDSocioInvitadoEspecial"] . "' ", "array" );
		if($datos_invitacion_especial["CabezaInvitacion"]=="S"):
			$sql_ingreso_grupo = $dbo->query("Update SocioInvitadoEspecial Set Ingreso = 'S', FechaIngreso = NOW() Where  IDPadre = '".$datos_invitacion_especial["IDInvitado"]."' and FechaInicio = '".$datos_invitacion_especial["FechaInicio"]."' and FechaFin = '".$datos_invitacion_especial["FechaFin"]."'");
		endif;
		*/
//Registro el historial de accesos
//$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Entrada, FechaIngreso,FechaTrCr) Values ('".$frm["IDSocioInvitadoEspecial"]."','InvitadoAcceso','S',NOW(),NOW())");

else :
	$respuesta = SIMWebServiceAccesos::set_salida_invitado(SIMUser::get("club"), $frm["IDSocioInvitadoEspecial"], "InvitadoAcceso", $frm["Mecanismo"], SIMUser::get("IDUsuario"), $frm["OtrosCampos"]);
//$sql_salida = $dbo->query("Update SocioInvitadoEspecial Set Salida = 'S', FechaSalida = NOW() Where IDSocioInvitadoEspecial = '".$frm["IDSocioInvitadoEspecial"]."'");
/*
		$datos_invitacion_especial = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $frm["IDSocioInvitadoEspecial"] . "' ", "array" );
		if($datos_invitacion_especial["CabezaInvitacion"]=="S"):
			$sql_ingreso_grupo = $dbo->query("Update SocioInvitadoEspecial Set Salida = 'S', FechaSalida = NOW() Where  IDPadre = '".$datos_invitacion_especial["IDInvitado"]."' and FechaInicio = '".$datos_invitacion_especial["FechaInicio"]."' and FechaFin = '".$datos_invitacion_especial["FechaFin"]."'");
		endif;
		*/
//Registro el historial de accesos
//$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Salida, FechaSalida, FechaTrCr) Values ('".$frm["IDSocioInvitadoEspecial"]."','InvitadoAcceso','S',NOW(),NOW())");

endif;

?>
["ok"]