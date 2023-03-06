<?php
	//include( "./procedures/general.php" );
	require( "../admin/config.inc.php" );
	require("../admin/lib/SIMServicioReserva.inc.php" );	
	
	$validacion = SIMServicioReserva::validarReservaAreaDeportiva(31, "Mauricio  Sanchez");
	echo json_encode($validacion);
