<?php header("Content-type: application/json; charset=utf-8");
ini_set('display_errors', 1); 
include("../config.inc.php");
$json = new Services_JSON;
$arrayCaprino = array();
	$QryCategoria = $dbo->query( "SELECT * FROM CategoriaProducto WHERE Publicar='S' AND IDPadre = 0" );
	if( $dbo->rows( $QryCategoria ) > 0 )
	{
		$arrayCaprino['linea'] = array();
		while( $TipoCategoria = $dbo->fetchArray( $QryCategoria ) )
		{
			$arrayLinea['IDLinea'] = $TipoCategoria[IDCategoria];
			$arrayLinea['Descripcion'] = strip_tags($TipoCategoria[Nombre]);
			$arrayLinea['URLImagen'] = IMGCATEGORIA_ROOT.$TipoCategoria[Foto];
			$QryCategoriaHija = $dbo->query( "SELECT * FROM CategoriaProducto WHERE Publicar='S' AND IDPadre = ".$TipoCategoria[IDCategoria]."" );
			if( $dbo->rows( $QryCategoriaHija ) > 0 )
			{
				$arrayLinea["sublinea"] = array();
				while( $TipoCategoriaHija = $dbo->fetchArray( $QryCategoriaHija ) )
				{
					$arraySubLinea["IDLinea"] = $TipoCategoriaHija[IDCategoria];
					$arraySubLinea["IDPadre"] = $TipoCategoriaHija[IDPadre];
					$arraySubLinea["Descripcion"] = strip_tags($TipoCategoriaHija[Nombre]);
					$arraySubLinea["URLImagen"] = CONTENIDO_ROOT.$TipoCategoriaHija[Foto];
						$QryCategoriaProducto = $dbo->query( "SELECT * FROM Producto WHERE Publicar='S' AND IDCategoria = '".$TipoCategoriaHija[IDCategoria]."'" );
						if( $dbo->rows( $QryCategoriaProducto ) > 0 )
						{
							$arraySubLinea['producto'] = array();
							while( $TipoCategoriaProducto = $dbo->fetchArray( $QryCategoriaProducto ) )
							{
								$ArrayProducto['IDProducto'] = $TipoCategoriaProducto[IDProducto];
								$ArrayProducto['IDLinea'] = $TipoCategoriaProducto[IDCategoria];
								$ArrayProducto['Nombre'] = strip_tags($TipoCategoriaProducto[Nombre]);
								$ArrayProducto['Descripcion'] = str_replace('&nbsp;','',strip_tags($TipoCategoriaProducto[Descripcion]));
								$ArrayProducto['URLImagen'] = PRODUCTOS_ROOT.$TipoCategoriaProducto[Foto];
								$ArrayProducto['URLImagenExtra'] = PRODUCTOS_ROOT.$TipoCategoriaProducto[Foto1];
								$ArrayProducto['URLImagenIphone'] = PRODUCTOS_IPHONE_ROOT.$TipoCategoriaProducto[FotoIphone];
								$ArrayProducto['ValorUnitario'] = $TipoCategoriaProducto[ValorUnitario];
								$ArrayProducto['Referencia'] = $TipoCategoriaProducto[Referencia];
								$ArrayProducto['PrecioAnterior'] = $TipoCategoriaProducto[PrecioAnterior];
								$ArrayProducto['Descuento'] = $TipoCategoriaProducto[Descuento];
								$QryGaleriaProducto = $dbo->query( "SELECT * FROM GaleriaProducto WHERE IDProducto = '".$TipoCategoriaProducto[IDProducto]."'" );
								if( $dbo->rows( $QryGaleriaProducto ) > 0 )
								{
									$ArrayProducto['galeria'] = array();
									while( $GaleriaProducto = $dbo->fetchArray( $QryGaleriaProducto ) )
									{
										$arraygaleria['IDGaleria'] = $GaleriaProducto[IDGaleria];
										$arraygaleria['IDProducto'] = $GaleriaProducto[IDProducto];
										$arraygaleria['Nombre'] = strip_tags($GaleriaProducto[Nombre]);
										$arraygaleria['File'] = PRODUCTOS_ROOT.$GaleriaProducto[File];
										array_push($ArrayProducto['galeria'],$arraygaleria);
									}
								}
								$QryTallaProducto = $dbo->query( "SELECT * FROM TallaProducto WHERE IDProducto = '".$TipoCategoriaProducto[IDProducto]."'" );
								if( $dbo->rows( $QryTallaProducto ) > 0 )
								{
									$ArrayProducto['talla'] = array();
									while( $TallaProducto = $dbo->fetchArray( $QryTallaProducto ) )
									{
										$arrayTalla['IDTallaProducto'] = $TallaProducto[IDTallaProducto];
										$arrayTalla['IDTalla'] = $TallaProducto[IDTalla];
										$arrayTalla['IDProducto'] = $TallaProducto[IDProducto];
										$arrayTalla['Nombre'] = $dbo->getFields( "Talla","Nombre","IDTalla = " . $TallaProducto[IDTalla]);
										array_push($ArrayProducto['talla'],$arrayTalla);
									}
								}
								$QryColorProducto = $dbo->query( "SELECT * FROM ProductoColor WHERE IDProducto = '".$TipoCategoriaProducto[IDProducto]."'" );
								if( $dbo->rows( $QryColorProducto ) > 0 )
								{
									$ArrayProducto['color'] = array();
									while( $ColorProducto = $dbo->fetchArray( $QryColorProducto ) )
									{
										$arrayColor['IDColor'] = $ColorProducto[IDColor];
										$arrayColor['IDProducto'] = $ColorProducto[IDProducto];
										$arrayColor['Nombre'] = strip_tags($ColorProducto[Nombre]);
										$arrayColor['Foto2'] = PRODUCTOS_ROOT.$ColorProducto[Foto2];
										$arrayColor['Foto1'] = PRODUCTOS_ROOT.$ColorProducto[Foto1];
										$QryGaleriaColor = $dbo->query( "SELECT * FROM GaleriaColor WHERE IDColor = '".$ColorProducto[IDColor]."' " );
										if( $dbo->rows( $QryGaleriaColor ) > 0 )
										{
											$arrayColor['galeriacolor'] = array();
											while( $GaleriaColor = $dbo->fetchArray( $QryGaleriaColor ) )
											{
												$arrayGaleriaColor['IDGaleria'] = $GaleriaColor[IDGaleria];
												$arrayGaleriaColor['IDProducto'] = $GaleriaColor[IDProducto];
												$arrayGaleriaColor['IDColor'] = $GaleriaColor[IDColor];
												$arrayGaleriaColor['Nombre'] = strip_tags($GaleriaColor[Nombre]);
												$arrayGaleriaColor['File'] = PRODUCTOS_ROOT.$GaleriaColor[File];
												array_push($arrayColor['galeriacolor'],$arrayGaleriaColor);
											}
										}
										array_push($ArrayProducto['color'],$arrayColor);
									}
								}
								array_push($arraySubLinea['producto'],$ArrayProducto);
							}
						}
					array_push($arrayLinea["sublinea"],$arraySubLinea);	
				}
			}
		array_push($arrayCaprino['linea'],$arrayLinea);	
		}
	}
	$QryCategoria = $dbo->query( "SELECT * FROM Ciudad WHERE Publicar='S'" );
	if( $dbo->rows( $QryCategoria ) > 0 )
	{
		$arrayCaprino['ciudad'] = array();
		while( $TipoCategoria = $dbo->fetchArray( $QryCategoria ) ){
			$contador++;
			$arrayCiudad['IDCiudad'] = $TipoCategoria[IDCiudad];
			$arrayCiudad['Nombre'] = strip_tags($TipoCategoria[Nombre]);
			$arrayCiudad["URLImagen"] = CIUDAD_ROOT.$TipoCategoria[Foto];
			$QryPunto = $dbo->query( "SELECT * FROM PuntoVenta WHERE Publicar='S' AND IDCiudad = '".$TipoCategoria[IDCiudad]."'" );
			if( $dbo->rows( $QryPunto ) > 0 )
			{
				$arrayCiudad['puntoventa'] = array();
				while( $TipoPunto = $dbo->fetchArray( $QryPunto ) ){
					$arrayciudad['IDPuntoVenta'] = $TipoPunto[IDPuntoVenta];
					$arrayciudad['IDCiudad'] = $TipoPunto[IDCiudad];
					$arrayciudad['Nombre'] = strip_tags($TipoPunto[Nombre]);
					$arrayciudad['Email'] = $TipoPunto[Email];
					$arrayciudad['Descripcion'] = strip_tags($TipoPunto[Descripcion]);
					$arrayciudad['URLImagen'] = TIENDAS_ROOT.$TipoPunto[Foto1];
					$arrayciudad['Direccion'] = strip_tags($TipoPunto[Direccion]);
					$arrayciudad['Telefono'] = $TipoPunto[Telefono];
					$arrayciudad['Tipo'] = $TipoPunto[Tipo];
					$arrayciudad['Horario'] = $TipoPunto[Horario];
					$arrayciudad['lat'] = $TipoPunto[lat];
					$arrayciudad['lng'] = $TipoPunto[lng];
					array_push($arrayCiudad['puntoventa'],$arrayciudad);
				}
			}
		array_push($arrayCaprino['ciudad'],$arrayCiudad);	
		}
	}
	$QryCategoria = $dbo->query( "SELECT * FROM Talla WHERE Publicar='S'" );
	if( $dbo->rows( $QryCategoria ) > 0 )
	{
		$arrayCaprino['talla'] = array();
		while( $TipoCategoria = $dbo->fetchArray( $QryCategoria ) )
		{
			$arrayTallas['IDTalla'] = $TipoCategoria[IDTalla];
			$arrayTallas['Nombre'] = $TipoCategoria[Nombre];
			array_push($arrayCaprino['talla'],$arrayTallas);	
		}
	}
	$QryCategoria = $dbo->query( "SELECT * FROM Banner WHERE Publicar='S' AND find_in_set('Iphone', Ubicacion) > 0 AND FechaPublicacion <= CURDATE() AND FechaBaja >= CURDATE()" );
	if( $dbo->rows( $QryCategoria ) > 0 )
	{
		$arrayCaprino['banner'] = array();
		while( $TipoCategoria = $dbo->fetchArray( $QryCategoria ) )
		{
			$arrayBanner['IDBanner'] = $TipoCategoria[IDBanner];
			$arrayBanner['Nombre'] = strip_tags($TipoCategoria[Nombre]);
			$arrayBanner['URLImagen'] = BANNER_ROOT.$TipoCategoria[File];
			array_push($arrayCaprino['banner'],$arrayBanner);	
		}
	}
echo $json->encode( $arrayCaprino );
?>