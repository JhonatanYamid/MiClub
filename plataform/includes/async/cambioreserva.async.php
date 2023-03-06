<?php
header('Content-Type: text/txt; charset=UTF-8');
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
if (!empty($_POST["IDReservaGeneral"])) {

    if(SIMUser::get( "club" ) == 125):
        date_default_timezone_set('America/Montevideo');
    endif;

    $fecha = date("Y-m-d H:i:s");

    if($_POST[Valor] == 'S' || $_POST[Valor] == 'N')
        $ActulizaCabeza = ", CumplidaCabeza = '".$_POST[Valor]."'";
    else
        $ActulizaCabeza = "";

    $sql_cambio = "UPDATE ReservaGeneral SET $_POST[Campo] = '$_POST[Valor]', FechaCumplida = '$fecha', IDUsuarioCumplida = " .SIMUser::get( "IDUsuario" ) . " $ActulizaCabeza 
    WHERE IDReservaGeneral = '" . $_POST["IDReservaGeneral"] . "'";

    $dbo->query($sql_cambio);
}
?>
["ok"]
