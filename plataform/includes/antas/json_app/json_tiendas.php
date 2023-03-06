<?php
header("Content-type: application/json; charset=utf-8");
$variables = json_decode($_REQUEST[json]);
$latitude = $variables->latitude;
$longitude = $variables->longitude;
ini_set('display_errors', 1); 
include("../config.inc.php");
$json = new Services_JSON;
$arrayCaprino = array();
$arrayCaprino["latitude_original"] = $latitude;
$arrayCaprino["longitude_original"] = $longitude;
$longitude = (float)$longitude;
$latitude = (float)$latitude;
$QryGEO = $dbo->query( "SELECT IDPuntoVenta,IDCiudad,Nombre,Email,Foto1,Direccion,Telefono,Tipo,Horario,lat,lng,(((acos(sin((".$latitude."*pi()/180)) * sin((`lat`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`lat`*pi()/180)) * cos(((".$longitude."- `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as Distancia FROM `PuntoVenta`" );
if( $dbo->rows( $QryGEO ) > 0 )
{
	$arrayCaprino['treskm'] = array();
	$arrayCaprino['seiskm'] = array();
	while( $ArrayQryGEO = $dbo->fetchArray( $QryGEO ) )
	{
		$distancia = (float)$ArrayQryGEO["Distancia"];
		if( 3.0 >= $distancia)
		{	
			$arrayTienda['IDPuntoVenta'] = $ArrayQryGEO[IDPuntoVenta];
			$arrayTienda['IDCiudad'] = $ArrayQryGEO[IDCiudad];
			$arrayTienda['Nombre'] = strip_tags($ArrayQryGEO[Nombre]);
			$arrayTienda['Distancia'] = $distancia;
			$arrayTienda['URLImagen'] = TIENDAS_ROOT.$ArrayQryGEO[Foto1];
			$arrayTienda['Direccion'] = strip_tags($ArrayQryGEO[Direccion]);
			$arrayTienda['Email'] = $ArrayQryGEO[Email];
			$arrayTienda['Telefono'] = $ArrayQryGEO[Telefono];
			$arrayTienda['Tipo'] = $ArrayQryGEO[Tipo];
			$arrayTienda['Horario'] = $ArrayQryGEO[Horario];
			$arrayTienda['lat'] = $ArrayQryGEO[lat];
			$arrayTienda['lng'] = $ArrayQryGEO[lng];
			array_push($arrayCaprino['treskm'],$arrayTienda);
		}
		elseif(6.0 >= $distancia)
		{
			$arrayTienda['IDPuntoVenta'] = $ArrayQryGEO[IDPuntoVenta];
			$arrayTienda['IDCiudad'] = $ArrayQryGEO[IDCiudad];
			$arrayTienda['Nombre'] = strip_tags($ArrayQryGEO[Nombre]);
			$arrayTienda['Distancia'] = $distancia;
			$arrayTienda['URLImagen'] = TIENDAS_ROOT.$ArrayQryGEO[Foto1];
			$arrayTienda['Direccion'] = strip_tags($ArrayQryGEO[Direccion]);
			$arrayTienda['Email'] = $ArrayQryGEO[Email];
			$arrayTienda['Telefono'] = $ArrayQryGEO[Telefono];
			$arrayTienda['Tipo'] = $ArrayQryGEO[Tipo];
			$arrayTienda['Horario'] = $ArrayQryGEO[Horario];
			$arrayTienda['lat'] = $ArrayQryGEO[lat];
			$arrayTienda['lng'] = $ArrayQryGEO[lng];
			array_push($arrayCaprino['seiskm'],$arrayTienda);
			
		}
	}
}
echo $json->encode( $arrayCaprino );
?>