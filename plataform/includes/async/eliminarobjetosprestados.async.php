<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
require_once LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
$frm = SIMUtil::makeSafe($_POST);
$frm_get =  SIMUtil::makeSafe($_GET);

$IDUsuario = SIMUser::get("IDUsuario");
$IDClub = SIMUser::get("club");
$Cantidad = $frm["CantidadPrestada"];
$IDCategoriaObjeto = $frm["IDCategoria"];
$IDObjetoPrestamo = $frm["ID"];

//primero hago devolucion del objeto prestado para eliminar
$respuesta = SIMWebServiceObjetosPrestados::set_devolucion_objeto_prestado($IDClub, $IDSocio, $IDUsuario, $Cantidad, $IDCategoriaObjeto, $IDObjetoPrestamo);

if (!empty($frm["Tabla"]) && !empty($frm["ID"])) {
    $sql_delete = "DELETE FROM " . $frm["Tabla"] . " WHERE ID" . $frm["Tabla"] . " = '" . $frm["ID"] . "' LIMIT 1";
    $qry_delete = $dbo->query($sql_delete);
    $nom_usu = SIMUser::get("IDUsuario") . " " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . SIMUser::get("IDUsuario") . "' ");

    SIMLog::insert($nom_usu, $frm["Tabla"], $frm["Tabla"], "delete",  $sql_delete);
}
?>
["ok"]