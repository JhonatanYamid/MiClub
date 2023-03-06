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
/* $Permiso = SIMUtil::verificar_permisos_CRUD(SIMUser::get("IDPerfil"), "PermisoBorrar");
if ($Permiso == 0) :
?>
	["NO TIENE PERMISOS PARA ELIMINAR REGISTROS"]
<?php
	exit;
endif; */

//echo $IDObjetoPrestamo;
$respuesta = SIMWebServiceObjetosPrestados::set_devolucion_objeto_prestado($IDClub, $IDSocio, $IDUsuario, $Cantidad, $IDCategoriaObjeto, $IDObjetoPrestamo);
//echo "hola";
if ($respuesta["success"] == "1") {
?>
	["ok"]
<?php
} else {


?>
	[FALLO]
<?php
}
?>