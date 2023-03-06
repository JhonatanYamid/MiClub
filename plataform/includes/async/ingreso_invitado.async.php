<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
require(LIBDIR . "SIMWebServiceAccesos.inc.php");

SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

if ($frm["Tipo"] != "Salida") :
	//$respuesta = SIMWebServiceApp::set_entrada_invitado(SIMUser::get("club"),$frm["IDSocioAutorizacion"],"Contratista",$frm["Mecanismo"],SIMUser::get("IDUsuario"));
	$respuesta = SIMWebServiceAccesos::set_entrada_invitado(SIMUser::get("club"), $frm["IDSocioInvitado"], "SocioInvitado", $frm["Mecanismo"], SIMUser::get("IDUsuario"), $frm["OtrosCampos"], $frm['PredioIngresoSocio']);
//$sql_ingreso = $dbo->query("Update SocioAutorizacion Set Ingreso = 'S', FechaIngreso = NOW() Where IDSocioAutorizacion = '".$frm["IDSocioAutorizacion"]."'");
//Registro el historial de accesos
//$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Entrada, FechaIngreso,FechaTrCr) Values ('".$frm["IDSocioAutorizacion"]."','Contratista','S',NOW(),NOW())");

else :
	$respuesta = SIMWebServiceAccesos::set_salida_invitado(SIMUser::get("club"), $frm["IDSocioAutorizacion"], "SocioInvitado", $frm["Mecanismo"], SIMUser::get("IDUsuario"), $frm["OtrosCampos"]);
//$sql_salida = $dbo->query("Update SocioAutorizacion Set Salida = 'S', FechaSalida = NOW() Where IDSocioAutorizacion = '".$frm["IDSocioAutorizacion"]."'");
//Registro el historial de accesos
//$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Salida, FechaSalida, FechaTrCr) Values ('".$frm["IDSocioAutorizacion"]."','Contratista','S',NOW(),NOW())");

endif;



//$sql_ingreso = $dbo->query("Update SocioInvitado Set Estado = 'I', FechaIngresoClub = NOW() Where IDSocioInvitado = '".$frm["IDSocioInvitado"]."'");
//$row_ingreso = $dbo->fetchArray($sql_ingreso);

//Registro el historial de accesos
//$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Entrada, FechaIngreso, FechaTrCr) Values ('".$frm["IDSocioInvitado"]."','InvitadoSocio','S',NOW(),NOW())");

?>
["ok"]