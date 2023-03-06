<?php
ini_set('display_errors', 1); 
//ini_set('memory_limit','200M');
include("config_almacenes/config.inc.php");
//json general para contenido de app mobile
//header("Content-type: application/json; charset=utf-8");
$json = new Services_JSON;

$arrayCaprino = array();

//$fechaencuestion = $_GET[fecha];
//Datos Recetas

	
	$SqlCategoria = "SELECT * FROM Referencia";
	$QryCategoria = $dbo->query( $SqlCategoria );
	$NumCategoria = $dbo->rows( $QryCategoria );
	$contador=0;

	if( $NumCategoria > 0 )
	{
		$arrayCaprino['Referencia'] = array();
		while( $TipoCategoria = $dbo->fetchArray( $QryCategoria ) )
		{
			
			$arrayBanner['IDReferencia'] = $TipoCategoria[IDReferencia];
			$arrayBanner['Numero'] = $TipoCategoria[Numero];
			$arrayBanner['Nombre'] = $TipoCategoria[Nombre];

			
		array_push($arrayCaprino['Referencia'],$arrayBanner);	
		}
	}
	
//visualizamos el array/json
//print_r($arrayCaprino);
echo $json->encode( $arrayCaprino );

//echo json_encode($arrayCaprino);

//phpinfo();

?>