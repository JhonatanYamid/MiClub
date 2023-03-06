<?php

	$ids = SIMNet::req("ids");
	
	if(empty($_POST["FechaReserva"])):			
		$fecha = date("Y-m-d");
	else:
		$fecha = $_POST["FechaReserva"];
	endif;
				
	//Si es coordinador tenemos que mirar que servicio se trae primero
	//si es un usuario normal se traen las horas
	
	$action = SIMNet::req("action");
	switch ( $action ) {	
		
		
	}//end switch



	if( empty( $view ) )
		$view = "views/eliminareservas/form.php";


	

?>