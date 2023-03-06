<?php
header('Content-Type: text/txt; charset=UTF-8');
include("../../procedures/general_async.php");
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();
if (!empty($_POST["IDNoticiaComentario"])) {
    $sql_asociar = "UPDATE NoticiaComentarioInfinita SET  Publicar = '" . $_POST["Valor"] . "' WHERE IDNoticiaComentarioInfinita = '" . $_POST["IDNoticiaComentario"] . "'";
    $dbo->query($sql_asociar);
}
?>
["ok"]