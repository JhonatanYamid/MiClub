<?php
ini_set('display_errors', 1); 
//ini_set('memory_limit','200M');
include("../config.inc.php");
//json general para contenido de app mobile

$json = new Services_JSON;

$arrayCaprino = array();

//$fechaencuestion = $_GET[fecha];
//Datos Recetas


	$SqlCategoria = "SELECT * FROM CategoriaProducto WHERE Publicar='S'";
	$QryCategoria = $dbo->query( $SqlCategoria );
	$NumCategoria = $dbo->rows( $QryCategoria );
	$contador=0;

	if( $NumCategoria > 0 )
	{
		$arrayCaprino['Linea'] = array();
		while( $TipoCategoria = $dbo->fetchArray( $QryCategoria ) )
		{
			
			$arrayLinea['IDLinea'] = $TipoCategoria[IDCategoria];
			$arrayLinea['Descripcion'] = htmlspecialchars_decode(htmlentities(utf8_encode($TipoCategoria[Nombre]), ENT_NOQUOTES, 'UTF-8', true));
			$arrayLinea['URLImagen'] = CONTENIDO_ROOT.$TipoCategoria[Foto];

			
				$SqlCategoriaProducto = "SELECT * FROM Producto WHERE Publicar='S' AND IDCategoria = '".$TipoCategoria[IDCategoria]."'";
				$QryCategoriaProducto = $dbo->query( $SqlCategoriaProducto );
				$NumCategoriaProducto = $dbo->rows( $QryCategoriaProducto );
				$contadorproducto=0;
			
				if( $NumCategoriaProducto > 0 )
				{
					$arrayLinea['Producto'] = array();
					while( $TipoCategoriaProducto = $dbo->fetchArray( $QryCategoriaProducto ) )
					{
						$array['IDProducto'] = $TipoCategoriaProducto[IDProducto];
						$array['IDLinea'] = $TipoCategoria[IDCategoria];
						$array['Nombre'] = htmlspecialchars_decode(htmlentities(utf8_encode($TipoCategoriaProducto[Nombre]), ENT_NOQUOTES, 'UTF-8', true));
						$array['Descripcion'] = htmlspecialchars_decode(htmlentities(utf8_encode($TipoCategoriaProducto[Descripcion]), ENT_NOQUOTES, 'UTF-8', true));
						$array['URLImagen'] = PRODUCTOS_ROOT.$TipoCategoriaProducto[Foto];
						$array['ValorUnitario'] = $TipoCategoriaProducto[ValorUnitario];
						$array['Referencia'] = $TipoCategoriaProducto[Referencia];
						$array['PrecioAnterior'] = $TipoCategoriaProducto[PrecioAnterior];
						$array['Descuento'] = $TipoCategoriaProducto[Descuento];
						
						array_push($arrayLinea['Producto'],$array);
					}
				}
				
		array_push($arrayCaprino['Linea'],$arrayLinea);	
		}
	}
	
	
	
	
	
	
	$SqlCategoria = "SELECT * FROM Ciudad WHERE Publicar='S'";
	$QryCategoria = $dbo->query( $SqlCategoria );
	$NumCategoria = $dbo->rows( $QryCategoria );

	if( $NumCategoria > 0 )
	{
		
		$arrayCaprino['Ciudad'] = array();
		while( $TipoCategoria = $dbo->fetchArray( $QryCategoria ) ){
			$contador++;
			$arrayCiudad['IDCiudad'] = $TipoCategoria[IDCiudad];
			$arrayCiudad['Nombre'] = htmlspecialchars_decode(htmlentities(utf8_encode($TipoCategoria[Nombre]), ENT_NOQUOTES, 'UTF-8', true));
			
			
			$SqlPunto = "SELECT * FROM PuntoVenta WHERE Publicar='S' AND IDCiudad = '".$TipoCategoria[IDCiudad]."'";
			$QryPunto = $dbo->query( $SqlPunto );
			$NumPunto = $dbo->rows( $QryPunto );
			
			if( $NumPunto > 0 )
			{
				$arrayCiudad['PuntoVenta'] = array();
				while( $TipoPunto = $dbo->fetchArray( $QryPunto ) ){
					$arrayciudad['IDPuntoVenta'] = $TipoPunto[IDPuntoVenta];
					$arrayciudad['IDCiudad'] = $TipoPunto[IDCiudad];
					$arrayciudad['Nombre'] = htmlspecialchars_decode(htmlentities(utf8_encode($TipoPunto[Nombre]), ENT_NOQUOTES, 'UTF-8', true));
					$arrayciudad['Email'] = $TipoPunto[Email];
					$arrayciudad['Descripcion'] = htmlspecialchars_decode(htmlentities(utf8_encode($TipoPunto[Descripcion]), ENT_NOQUOTES, 'UTF-8', true));
					$arrayciudad['URLImagen'] = TIENDAS_ROOT.$TipoPunto[Foto1];
					$arrayciudad['Direccion'] = htmlspecialchars_decode(htmlentities(utf8_encode($TipoPunto[Direccion]), ENT_NOQUOTES, 'UTF-8', true));
					$arrayciudad['Telefono'] = $TipoPunto[Telefono];
					$arrayciudad['Tipo'] = $TipoPunto[Tipo];
					$arrayciudad['Horario'] = $TipoPunto[Horario];
					$arrayciudad['RDesde'] = $TipoPunto[RDesde];
					$arrayciudad['RHasta'] = $TipoPunto[RHasta];
					$arrayciudad['lat'] = $TipoPunto[lat];
					$arrayciudad['lng'] = $TipoPunto[lng];
					
					array_push($arrayCiudad['PuntoVenta'],$arrayciudad);
				}
			}
		array_push($arrayCaprino['Ciudad'],$arrayCiudad);	
			
		}
	}
	
	
	
	$SqlCategoria = "SELECT * FROM Banner WHERE Publicar='S' AND find_in_set('Iphone', Ubicacion) > 0";
	$QryCategoria = $dbo->query( $SqlCategoria );
	$NumCategoria = $dbo->rows( $QryCategoria );
	$contador=0;

	if( $NumCategoria > 0 )
	{
		$arrayCaprino['Banner'] = array();
		while( $TipoCategoria = $dbo->fetchArray( $QryCategoria ) )
		{
			
			$arrayBanner['IDBanner'] = $TipoCategoria[IDBanner];
			$arrayBanner['Nombre'] = htmlspecialchars_decode(htmlentities(utf8_encode($TipoCategoria[Nombre]), ENT_NOQUOTES, 'UTF-8', true));
			$arrayBanner['URLImagen'] = BANNER_ROOT.$TipoCategoria[File];

			
		array_push($arrayCaprino['Banner'],$arrayBanner);	
		}
	}
	
//visualizamos el array/json
//print_r($arrayCaprino);
//echo $json->encode( $arrayCaprino );

echo json_encode($arrayCaprino);

//phpinfo();

?>