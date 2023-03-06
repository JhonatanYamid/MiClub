<?php
header('Content-Type: text/txt; charset=UTF-8');
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
if (!empty($_POST["IDTalonera"])) {

    $Version = $_POST["Version"];

    $sql_cambio = "UPDATE SocioTalonera SET  Activo = '" . $_POST["Valor"] . "' WHERE IDTalonera = '" . $_POST["IDTalonera"] . "' and IDSocioTalonera= '" . $_POST["IDSocioTalonera"] . "'";

    $dbo->query($sql_cambio);
}
?>
["ok"]