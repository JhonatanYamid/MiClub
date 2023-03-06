<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
require(LIBDIR . "SIMWebServiceAccesos.inc.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$fechaAhora = date("Y-m-d H:i");


if ($frm["Tipo"] != "Salida") :
    $respuesta = SIMWebServiceAccesos::set_entrada_invitado(SIMUser::get("club"), $frm["IDSocioAutorizacion"], "Contratista", $frm["Mecanismo"], SIMUser::get("IDUsuario"), $frm["OtrosCampos"], $frm['PredioIngresoSocio']);
    //$sql_ingreso = $dbo->query("Update SocioAutorizacion Set Ingreso = 'S', FechaIngreso = NOW() Where IDSocioAutorizacion = '".$frm["IDSocioAutorizacion"]."'");
    //Registro el historial de accesos
    //$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Entrada, FechaIngreso,FechaTrCr) Values ('".$frm["IDSocioAutorizacion"]."','Contratista','S',NOW(),NOW())");

    foreach ($frm["AccesoObjetos"] as $AccesoObjeto) {

        $frm_objeto = [];

        $frm_objeto["IDAccesoObjeto"] = (int) $AccesoObjeto["IDAccesoObjeto"];


        $frm_objeto["IDClub"] = SIMUser::get("club");
        //$frm_objeto["Salida"];
        //$frm_objeto["FechaSalida"] ;
        $frm_objeto["Entrada"] = "S";
        $frm_objeto["FechaIngreso"] = $fechaAhora;

        $frm_objeto = SIMUtil::varsLOG($frm_objeto);

        $id = $dbo->insert($frm_objeto, "LogAccesoObjeto", "IDLogAcceso");
    }

else :
    $respuesta = SIMWebServiceAccesos::set_salida_invitado(SIMUser::get("club"), $frm["IDSocioAutorizacion"], "Contratista", $frm["Mecanismo"], SIMUser::get("IDUsuario"), $frm["OtrosCampos"]);
    //$sql_salida = $dbo->query("Update SocioAutorizacion Set Salida = 'S', FechaSalida = NOW() Where IDSocioAutorizacion = '".$frm["IDSocioAutorizacion"]."'");
    //Registro el historial de accesos
    //$sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, Tipo, Salida, FechaSalida, FechaTrCr) Values ('".$frm["IDSocioAutorizacion"]."','Contratista','S',NOW(),NOW())");

    foreach ($frm["AccesoObjetos"] as $AccesoObjeto) {

        $frm_objeto = [];

        $frm_objeto["IDAccesoObjeto"] = (int) $AccesoObjeto["IDAccesoObjeto"];


        $frm_objeto["IDClub"] = SIMUser::get("club");
        $frm_objeto["Salida"] = "S";
        $frm_objeto["FechaSalida"] = $fechaAhora;
        //$frm_objeto["Entrada"];
        //$frm_objeto["FechaIngreso"];		

        $frm_objeto = SIMUtil::varsLOG($frm_objeto);

        $id = $dbo->insert($frm_objeto, "LogAccesoObjeto", "IDLogAcceso");
    }

endif;
?>
["ok"]