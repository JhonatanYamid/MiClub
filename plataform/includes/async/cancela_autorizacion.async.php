<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );	
		
		
		if (!empty($frm["Tabla"]) && !empty($frm["ID"]))
			 $sql_actualiza = "Update  ".$frm["Tabla"]."  Set FechaFin = '".date("Y-m-d")."', IDUsuarioCancela = '".SIMUser::get("IDUsuario")."', FechaCancelacion = NOW() Where ID".$frm["Tabla"]." = '" . $frm["ID"] . "' LIMIT 1";
		
		$qry_actualiza = $dbo->query( $sql_actualiza );
?>
["ok"]