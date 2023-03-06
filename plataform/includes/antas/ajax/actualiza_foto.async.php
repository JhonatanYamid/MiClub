<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );

if (!empty($_POST[ID])){
	$actualiza_foto= "Update FotoGaleria Set Orden = '". $_POST["Orden"] ."', Descripcion = '".$_POST["Descripcion"]."' Where IDFoto = '". $_POST[ID] ."'";
	$dbo->query($actualiza_foto);
}
?>
