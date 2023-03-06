<?php

	require( "config.inc.php" );

	//verificar si hay noticias nuevas
	//SocioSeccion
	//SocioSeccionEvento
	
	//SocioSeccionGaleria


	/*$users = array( array( "id"=>121, 
					"idclub"=>7, 
					"registration_key"=>"855290cfd09bb5b12ce0314b7ee0b784c14fb4732f2698842ba2ee80d1b95416" ,
					"deviceType"=>"iOS" )

				);
	*/

	$users = array( array( "id"=>5515, 
					"idclub"=>8, 
					"registration_key"=>"9a6526a8bf7e31702520c3670f0bdcf907fb4aa38f3d2c3a5275cc8672171c31" ,
					"deviceType"=>"iOS" )

				);

				

	$message = "Hola Rubi";

						$custom["tipo"] = "galeria";
	           		$custom["idseccion"] = (string)"8" ;
	           		$custom["iddetalle"] = (string)"10";
	           		$custom["idmodulo"] = (string)"5";
	           		$custom["titulo"] = "Hola";


	SIMUtil::sendAlerts($users, $message, $custom);

	
	$dbo->close();
?>