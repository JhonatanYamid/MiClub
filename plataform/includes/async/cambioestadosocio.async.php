<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();
	if (!empty($_POST["IDSocio"]))
	{				
		$sql_asociar="UPDATE Socio SET  IDEstadoSocio = '".$_POST["Valor"]."' WHERE IDSocio = '".$_POST["IDSocio"]."'";
		$dbo->query($sql_asociar);		
	}
?>
["ok"]
