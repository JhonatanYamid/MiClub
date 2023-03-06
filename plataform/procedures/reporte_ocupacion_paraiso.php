<?

	SIMReg::setFromStructure( array(
						"title" => "ReporteOcupación",
						"table" => "Ocupacion",
						"key" => "IDOcupacion",
						"mod" => "Ocupacion"
	) );
	
	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );
	
	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );	

	$script = "reporte_ocupacion_paraiso";


	switch ( SIMNet::req( "action" ) ) {
	
		case "search" :
			$view = "views/".$script."/list.php";
		break;
		
		default;
			// $newmode = "insert";
			// $titulo_accion = "Crear";
			$view = "views/".$script."/list.php";
		break;	
	
	}

	if( empty( $view ) )
		$view = "views/".$script."/list.php";
