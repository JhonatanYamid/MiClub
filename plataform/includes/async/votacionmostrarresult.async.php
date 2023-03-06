<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
if (!empty($_POST["IDVotacion"]) && !empty($_POST["IDVotacionEvento"])){
	$sql_mostrar="UPDATE Votacion SET MostrarResultados= '".$_POST["Valor"]."' WHERE IDVotacion = '".$_POST["IDVotacion"]."' ";
	$dbo->query($sql_mostrar);
}
?>
["ok"]
