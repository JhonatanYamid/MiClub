<?php

include ("config.inc.php");
SIMUtil::cache();

//$datos_sesion = Verifica_Sesion();


$file = $_GET[reportdir].$_GET[report].".xls";

$filename = $_GET[report] . date("Y-m-d-H:m:s") . ".xls";

SIMReport::header_download($file,$filename);
?>