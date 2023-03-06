<?php
header('Content-Type: text/txt; charset=UTF-8');
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
if (!empty($_POST["IDProducto"])) {

    $Version = $_POST["Version"];

    $sql_cambio = "UPDATE Producto".$Version." SET  Publicar = '" . $_POST["Valor"] . "' WHERE IDProducto = '" . $_POST["IDProducto"] . "'";

    $dbo->query($sql_cambio);
}
?>
["ok"]
