<?php
header('Content-Type: text/txt; charset=UTF-8');
include( "../../procedures/general_async.php" );
SIMUtil::cache( "text/json" );
$dbo =& SIMDB::get();

$frm = SIMUtil::makeSafe( $_POST );
$frm_get =  SIMUtil::makeSafe( $_GET );	
		
		$sql_reserva_general = $dbo->query("Select * From ReservaGeneral Where IDReservaGeneral = '".$frm["IDReservaGeneral"]."'");
		$row_reserva = $dbo->fetchArray($sql_reserva_general);
		
		if (!empty($row_reserva["IDSocio"]) && !empty($row_reserva["IDReservaGeneral"]) && !empty($row_reserva["IDClub"])  ):
			 $result =  SIMWebService::elimina_reserva_general($row_reserva["IDClub"],$row_reserva["IDSocio"],$row_reserva["IDReservaGeneral"],"Admin",$frm["Razon"]);	
			 //print_r($result);
		endif;	 
?>
["<?php echo $result["message"]; ?>"]