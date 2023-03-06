<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
	if (!empty($_POST["IDNoticiaComentario"]))
	{				
		$sql_asociar="UPDATE NoticiaComentario SET  Publicar = '".$_POST["Valor"]."' WHERE IDNoticiaComentario = '".$_POST["IDNoticiaComentario"]."'";
		$dbo->query($sql_asociar);		
	}
?>
["ok"]
