<?php

	include( "../../procedures/general_async.php" );
	SIMUtil::cache( "text/json" );
	$dbo =& SIMDB::get();

	$frm = SIMUtil::makeSafe( $_POST );
	$frm_get =  SIMUtil::makeSafe( $_GET );
	$responce = array();
	//$id = SIMNet::reqInt( "IDContacto" );

	$idservicio = SIMNet::req("servicio");
	$tiempo = SIMNet::req("tiempo");

	$club = $datos_club["IDClub"];

	$array_colores = array("#68BC31", "#2091CF","#AF4E96","#DA5430","#DA5430","#FEE074","#D4D4D4");

	$sql_reservas = "SELECT COUNT(*) as Numero, DATE_FORMAT(Fecha,'%w') as Dia FROM ReservaGeneral ";
	$where = " WHERE IDClub = '" . $club . "'  ";
	$groupby = " GROUP BY DATE_FORMAT(Fecha,'%w') ";

	switch( $tiempo )
	{

		case "estemes":
			
			$where .= " AND DATE_FORMAT(Fecha,'%m') = '" . date("m") . "' ";
			

				
		break;

		case "mespasado":

			$fecha = date('Y-m-j');
			$nuevafecha = strtotime ( '-1 month' , strtotime ( $fecha ) ) ;
			$nuevafecha = date ( 'm' , $nuevafecha );
			$where .= " AND DATE_FORMAT(Fecha,'%m') = '" . $nuevafecha . "' ";
			
		break;
	}//end sw


	$where .= " AND IDServicio = '" . $idservicio . "'  ";

	$sql_reservas = $sql_reservas . $where . $groupby;
	$qry_reservas = $dbo->query( $sql_reservas );
	while( $r_reservas = $dbo->fetchArray( $qry_reservas ) )
	{
		$total_reservas += $r_reservas["Numero"];
		$total_dia[ $r_reservas["Dia"] ] = $r_reservas["Numero"];
	}//end while


	$count = 0;
	foreach( $total_dia as $dia => $reservas )
	{
		$pesodia = $reservas / $total_reservas;
		$nombredia = SIMResources::$dias_semana[ $dia ];
		$reservasxdia = array( "label" => $nombredia, "data" => $pesodia, "color" => $array_colores[$count] );
		$count++;
		array_push($responce, $reservasxdia);
	}//end fr


	echo json_encode($responce);

?>