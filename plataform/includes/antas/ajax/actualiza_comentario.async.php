<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );

if ($_POST[Valor]=="S")
	$estado_valor="A";
elseif($_POST[Valor]=="N")	
	$estado_valor="NA";
	
$campo_id="IDComentario";	

if (!empty($_POST[ID])){
	$actualiza_estado= "Update " . $_POST[Tabla] . " Set Estado = '". $estado_valor ."' Where " . $campo_id ."= '". $_POST[ID] ."'";	
	$dbo->query($actualiza_estado);
}
?>
