<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
require(LIBDIR . "SIMWebServiceAccesos.inc.php");

SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);


if ($frm["Tipo"] != "Salida") :
	$respuesta = SIMWebServiceAccesos::set_entrada_invitado(SIMUser::get("club"), $frm["IDInvitadoEvento"], "InvitadoEvento", $frm["Mecanismo"], SIMUser::get("IDUsuario"), $frm["OtrosCampos"], $frm['PredioIngresoSocio']);

else :
	$respuesta = SIMWebServiceAccesos::set_salida_invitado(SIMUser::get("club"), $frm["IDInvitadoEvento"], "InvitadoEvento", $frm["Mecanismo"], SIMUser::get("IDUsuario"), $frm["OtrosCampos"]);

endif;

?>
["ok"]