<?php

	include( "../../procedures/general_async.php" );
	SIMUtil::cache( "text/json" );
	$dbo =& SIMDB::get();

	$frm = SIMUtil::makeSafe( $_POST );
	$frm_get =  SIMUtil::makeSafe( $_GET );
	//$id = SIMNet::reqInt( "IDContacto" );

	$tiporeserva = SIMNet::req("origen");
	$tiempo = SIMNet::req("origen");

	$club = $datos_club["IDClub"];

	$sql_reservas = "SELECT COUNT(*) as Numero, DATE_FORMAT(Fecha,"%w") as Dia FROM ReservaGeneral ";
	$where = " WHERE IDClub = '" . $club . "'  ";
	$groupby = " GROUP BY DATE_FORMAT(Fecha,"%w") ";

	switch( $tiempo )
	{

		case "estemes":
			
			
			

				
		break;

		case "mespasado":
			
		break;
	}//end sw


	switch( $servicio )
	{

		case "estemes":
			
			
			

				
		break;

		case "mespasado":
			
		break;
	}//end sw

	echo $sql_reservas = $sql_reservas . $where . $groupby;


	$responce = array();
	




 



echo json_encode($responce);

?>