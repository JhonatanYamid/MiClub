<?php
class SIMWebService
{

	function valida_socio( $email,$clave,$id_club )
	{
		
		$dbo =& SIMDB::get();
		if( !empty( $email ) && !empty( $clave )  ){
				$foto="";
				$foto_cod_barras = "";
					
					$sql_verifica = "SELECT * FROM Socio WHERE Email = '".$email ."' and Clave = '".sha1($clave )."' and IDClub = '".$id_club."' and (IDEstadoSocio <> 2 and IDEstadoSocio <> 3 ) ";

					$qry_verifica = $dbo->query( $sql_verifica );
					if( $dbo->rows( $qry_verifica ) == 0 )
					{
						$respuesta["message"] = "No encontrado";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					}//end if
					else{
						
						$datos_socio = $dbo->fetchArray( $qry_verifica );
						$flag_canje_cortesia = 0;
						
					   //Si el socio es por canje o cortesia valido este activo segun las fechas
					   
					   switch ($datos_socio["TipoSocio"]):
					   	case "Canje":
							$fecha_hoy = strtotime(date("Y-m-d"));
							$FechaInicioCanje = strtotime($datos_socio["FechaInicioCanje"]);
							$FechaFinCanje = strtotime($datos_socio["FechaFinCanje"]);
							//echo $FechaInicioCanje.">=".$fecha_hoy ."&&". $fecha_hoy."<=".$FechaFinCanje;
							if ($fecha_hoy>=$FechaInicioCanje && $fecha_hoy<=$FechaFinCanje):
								$flag_canje_cortesia = 0;
							else:
								$flag_canje_cortesia = 1;
							endif;
						break;
						case "Cortesia":
							$fecha_hoy = strtotime(date("Y-m-d"));
							$FechaInicioCortesia = strtotime($datos_socio["FechaInicioCortesia"]);
							$FechaFinCortesia= strtotime($datos_socio["FechaFinCortesia"]);
							if ($fecha_hoy>=$FechaInicioCortesia && $fecha_hoy<=$FechaFinCortesia):
								$flag_canje_cortesia = 0;
							else:
								$flag_canje_cortesia = 1;	
							endif;
						break;
						case "Invitado":
							$fecha_hoy = strtotime(date("Y-m-d"));
							$FechaInicioInvitado = strtotime($datos_socio["FechaInicioInvitado"]);
							$FechaFinInvitado= strtotime($datos_socio["FechaFinInvitado"]);
							if ($fecha_hoy>=$FechaInicioInvitado && $fecha_hoy<=$FechaFinInvitado):
								$flag_canje_cortesia = 0;
							else:
								$flag_canje_cortesia = 1;	
							endif;
						break;
						default:
								$flag_canje_cortesia = 0;
					   endswitch;
					   
						
						
						if($flag_canje_cortesia==0){
						
									if (!empty($datos_socio["Foto"])){
										$foto = 	SOCIO_ROOT.$datos_socio["Foto"];
									}
									
									$tipo_codigo_carne = $dbo->getFields( "Club" , "TipoCodigoCarne" , "IDClub = '".$id_club."'" );
									
									switch($tipo_codigo_carne){
										case "Barras":
											if (!empty($datos_socio["CodigoBarras"])){												
												$foto_cod_barras = 	SOCIO_ROOT.$datos_socio["CodigoBarras"];												
											}
										break;
										case "QR":
											if (!empty($datos_socio["CodigoQR"])){												
												$foto_cod_barras = 	SOCIO_ROOT."qr/".$datos_socio["CodigoQR"];											
											}
										break;
										default:
										 $foto_cod_barras = 	"";		
									}
									
									//Consulto el nucleo familiar
									if (!empty($datos_socio["AccionPadre"])): // Es beneficiario
										$condicion_nucleo = " and (AccionPadre = '".$datos_socio["AccionPadre"]."' or Accion = '".$datos_socio["AccionPadre"]."')";
										//$tipo_socio = "Beneficiario";
										$tipo_socio = $datos_socio["TipoSocio"];
									else: // es Cabeza familia
										$condicion_nucleo = " and AccionPadre = '".$datos_socio["Accion"]."'";	
										//$tipo_socio = "Socio";
										$tipo_socio = $datos_socio["TipoSocio"];
									endif;
									
									
									$response_nucleo = array();
									$sql_nucleo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras, CodigoQR FROM Socio WHERE IDClub = '".$id_club."' and IDSocio <> '".$datos_socio["IDSocio"]."' and (IDEstadoSocio <> 2 and IDEstadoSocio <> 3 ) " . $condicion_nucleo;
									$qry_nucleo = $dbo->query( $sql_nucleo );
									while($datos_nucleo = $dbo->fetchArray( $qry_nucleo )):
										$foto_nucleo = "";
										$foto_cod_barras_nucleo = "";
										
										if (!empty($datos_nucleo["Foto"])){
											$foto_nucleo = 	SOCIO_ROOT.$datos_nucleo["Foto"];
										}
										
										switch($tipo_codigo_carne){
										case "Barras":
											if (!empty($datos_nucleo["CodigoBarras"])){												
												$foto_cod_barras_nucleo = 	SOCIO_ROOT.$datos_nucleo["CodigoBarras"];												
											}
										break;
										case "QR":
											if (!empty($datos_nucleo["CodigoQR"])){												
												$foto_cod_barras_nucleo = 	SOCIO_ROOT."qr/".$datos_nucleo["CodigoQR"];											
											}
										break;
										default:
										 $foto_cod_barras_nucleo = 	"";		
										}
									
										
										//if (!empty($datos_nucleo["CodigoBarras"])){
											//$foto_cod_barras_nucleo = 	SOCIO_ROOT.$datos_nucleo["CodigoBarras"];
										//}
										
										//Averiguo tipo: Socio o Beneficiario
										if (!empty($datos_nucleo["AccionPadre"])): // Es beneficiario
											$tipo_socio_nucleo = "Beneficiario";
											$tipo_socio_nucleo = $datos_nucleo["TipoSocio"];
										else: // es Cabeza familia
											//$tipo_socio_nucleo = "Socio";
											$tipo_socio_nucleo = $datos_nucleo["TipoSocio"];
										endif;
										
										
										$nucleo[IDSocio] =  $datos_nucleo[IDSocio];
										$nucleo[IDClub] =  $datos_nucleo[IDClub];
										$nucleo[Foto] =  $foto_nucleo;
										$nucleo[Socio] =  utf8_encode( $datos_nucleo["Nombre"] )  . " " . utf8_encode( $datos_nucleo["Apellido"] );
										$nucleo[NumeroDerecho] =  $datos_nucleo[Accion];
										$nucleo[CodigoBarras] =  $foto_cod_barras_nucleo;
										$nucleo[TipoSocio] =  $tipo_socio_nucleo;
										
										array_push($response_nucleo, $nucleo);
										/*
										$array_nucleo[$datos_nucleo[IDSocio]][IDSocio] = $datos_nucleo[IDSocio];
										$array_nucleo[$datos_nucleo[IDSocio]][IDClub] = $datos_nucleo[IDClub];
										$array_nucleo[$datos_nucleo[IDSocio]][Foto] = $foto_nucleo;
										$array_nucleo[$datos_nucleo[IDSocio]][Socio] = $datos_nucleo[Socio];
										$array_nucleo[$datos_nucleo[IDSocio]][NumeroDerecho] = $datos_nucleo[Accion];
										$array_nucleo[$datos_nucleo[IDSocio]][CodigoBarras] = $foto_cod_barras_nucleo;
										*/
									endwhile;
									
									
									//Preferencias Contenido
									$response_seccion_noticia = array();
									$sql_seccionnot_socio = $dbo->query("Select * From SocioSeccion Where IDSocio = '".$datos_socio["IDSocio"]."'");
									while ($r_seccionnot_socio = $dbo->fetchArray($sql_seccionnot_socio)):
										$seccion_noticia[IDSocio]=$datos_socio["IDSocio"];
										$seccion_noticia[IDClub]=$datos_socio["IDClub"];
										$seccion_noticia[IDSeccion]=$r_seccionnot_socio["IDSeccion"];
										array_push($response_seccion_noticia, $seccion_noticia);
										/*
										$array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSocio] = $datos_socio["IDSocio"];
										$array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDClub] = $datos_socio["IDClub"];
										$array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSeccion] = $r_seccionnot_socio["IDSeccion"];
										*/
									endwhile;
									
									//Preferencias Eventos
									$response_seccion_evento = array();
									$sql_seccioneve_socio = $dbo->query("Select * From SocioSeccionEvento Where IDSocio = '".$datos_socio["IDSocio"]."'");
									while ($r_seccioneve_socio = $dbo->fetchArray($sql_seccioneve_socio)):
										$seccion_evento[IDSocio]=$datos_socio["IDSocio"];
										$seccion_evento[IDClub]=$datos_socio["IDClub"];
										$seccion_evento[IDSeccionEvento]=$r_seccioneve_socio["IDSeccionEvento"];
										array_push($response_seccion_evento, $seccion_evento);
									endwhile;
									
									
									//Socios Favoritos
									$response_favoritos = array();
									$sql_favorito_socio = $dbo->query("Select * From SocioFavorito Where IDSocio = '".$datos_socio["IDSocio"]."'");
									while ($r_favorito_socio = $dbo->fetchArray($sql_favorito_socio)):
										$favorito_socio[IDSocio]=$r_favorito_socio["IDSocio2"];
										array_push($response_favoritos, $favorito_socio);
									endwhile;
									
									
									$response["IDClub"] = $datos_socio["IDClub"];
									$response["IDSocio"] = $datos_socio["IDSocio"];
									$response["Foto"] = $foto;
									$response["Socio"] = utf8_encode( $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] );
									$response["NumeroDerecho"] = $datos_socio["Accion"];
									$response["CodigoBarras"] = $foto_cod_barras;
									$response["NucleoFamiliar"] = $response_nucleo;
									$response["PreferenciasContenido"] = $response_seccion_noticia;
									$response["PreferenciasEvento"] = $response_seccion_evento;
									$response["SocioFavorito"] = $response_favoritos;
									$response["Dispositivo"] =  $datos_socio["Dispositivo"];
									$response["Token"] = $datos_socio["Token"];
									$response["TipoSocio"] =  $tipo_socio;
									
									
									$respuesta["message"] = "ok";
									$respuesta["success"] = true;
									$respuesta["response"] = $response;
									
						}
						else{
							$respuesta["message"] = "Lo sentimos, las fechas de la cortesia o canje ya vencieron";
							$respuesta["success"] = false;
							$respuesta["response"] = NULL;	
						}
									
									
					}
				}
			else{
				$respuesta["message"] = "1. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
			
			
			return $respuesta;

	}//end function
	
	
	
	function get_banner_app( $id_club )
	{
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM BannerApp WHERE DirigidoA <> 'E' and Publicar = 'S' and IDClub = '".$id_club."' ORDER BY RAND()";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$banner["IDBannerApp"] = $r["IDBannerApp"];
				$banner["Nombre"] = $r["Nombre"];
				if (!empty($r["Foto1"])):
					$foto = BANNERAPP_ROOT.$r["Foto1"];
				else:
					$foto="";	
				endif;
				$banner["Foto1"] = $foto;
				array_push($response, $banner);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado splash";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	function get_banner_app_empleado( $id_club )
	{
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM BannerApp WHERE DirigidoA = 'E' and Publicar = 'S' and IDClub = '".$id_club."' ORDER BY RAND()";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$banner["IDBannerApp"] = $r["IDBannerApp"];
				$banner["Nombre"] = $r["Nombre"];
				if (!empty($r["Foto1"])):
					$foto = BANNERAPP_ROOT.$r["Foto1"];
				else:
					$foto="";	
				endif;
				$banner["Foto1"] = $foto;
				array_push($response, $banner);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado splash";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	
	function verificar_contenido_modulo($IDModulo, $id_club){
		$dbo =& SIMDB::get();				
		$flag_mostrar = 0;
		//Para Noticias, Eventos y galeria se verifica si hay contenido para mostrarlo en el menu los id son 3,4,5
		switch($IDModulo):
			case "3": // Noticias
				// verifico que la seccion tenga por lo menos una noticia publicada
				$id_noticia = $dbo->getFields( "Noticia" , "IDNoticia" , "IDClub = '".$id_club."' and Publicar = 'S'" );
				if(empty($id_noticia)):
					$flag_mostrar=1;
				endif;
			break;
			case "4": // Eventos
				// verifico que la seccion tenga por lo menos una noticia publicada
				$id_evento = $dbo->getFields( "Evento" , "IDEvento" , "IDClub = '".$id_club."' and Publicar = 'S'" );
				if(empty($id_evento)):
					$flag_mostrar=1;
				endif;
			break;
			case "5": // Galerias
				// verifico que la seccion tenga por lo menos una galeria publicada
				$id_galeria = $dbo->getFields( "Galeria" , "IDGaleria" , "IDClub = '".$id_club."' and Publicar = 'S'" );
				if(empty($id_galeria)):
					$flag_mostrar=1;
				endif;
			break;
			default:
			  $flag_mostrar=0;
		endswitch;
		
		return $flag_mostrar;
	}
	
	
	
	function get_club( $id_club )
	{
		
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM Club WHERE IDClub = '".$id_club."'";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				if (!empty($r["FotoLogoApp"])){
					$foto_logo = CLUB_ROOT.$r["FotoLogoApp"];
				}
				if (!empty($r["FotoDiseno1"])){
					$foto1 = CLUB_ROOT.$r["FotoDiseno1"];
				}
				if (!empty($r["FotoDiseno2"])){
					$foto2 = CLUB_ROOT.$r["FotoDiseno2"];
				}
				
				//Banners
				$response_banner = array();
				$sql_banner = "SELECT * FROM BannerApp WHERE DirigidoA <> 'E' and Publicar = 'S' and IDClub = '".$id_club."' ORDER BY IDBannerApp";
				$qry_banner = $dbo->query( $sql_banner );
				if( $dbo->rows( $qry_banner ) > 0 )
				{	
					while( $r_banner = $dbo->fetchArray( $qry_banner ) )
					{
						$banner["IDClub"] = $id_club;
						$banner["IDBannerApp"] = $r_banner["IDBannerApp"];
						if (!empty($r_banner["Foto1"])):
							$foto = BANNERAPP_ROOT.$r_banner["Foto1"];
						else:
							$foto="";	
						endif;
						$banner["Foto"] = $foto;
						array_push($response_banner, $banner);
		
					}//ednw hile
				}
				
				//Servicios Reservas
				$response_servicio = array();
				$sql_servicio = "SELECT * FROM Servicio WHERE IDClub = '".$id_club."' ORDER BY IDServicio";
				$qry_servicio = $dbo->query( $sql_servicio );
				if( $dbo->rows( $qry_servicio ) > 0 )
				{	
					while( $r_servicio = $dbo->fetchArray( $qry_servicio ) )
					{
						$servicio["IDClub"] = $id_club;
						$servicio["IDServicio"] = $r_servicio["IDServicio"];
						$servicio["NombreServicio"] = $r_servicio["Nombre"];
						if (!empty($r_servicio["Icono"])):
							$foto = SERVICIO_ROOT.$r_servicio["Icono"];
						else:
							$foto="";	
						endif;
						$servicio["Icono"] = $foto;
						$servicio["ServicioInicial"] = $dbo->getFields( "ServicioInicial" , "Nombre" , "IDServicioInicial = '".$r_servicio["IDServicioInicial"]."'" );
						array_push($response_servicio, $servicio);
		
					}//ednw while
				}
				
				
				//Modulos Sistema Menu Central
				$response_modulo = array();
				$sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '".$id_club."' and Activo = 'S' and Ubicacion like '%Central%' ORDER BY Orden";
				$qry_modulo = $dbo->query( $sql_modulo );
				if( $dbo->rows( $qry_modulo ) > 0 )
				{	
				
					while( $r_modulo = $dbo->fetchArray( $qry_modulo ) )
					{
						// Verificar si el modulo tiene contenido para mostrar
						$flag_mostrar = SIMWebService::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);					
						//$flag_mostrar=0;
						if($flag_mostrar==0):
							$modulo["IDClub"] = $id_club;
							$modulo["IDModulo"] = $r_modulo["IDModulo"];
							if(!empty($r_modulo["Titulo"]))
								$modulo["NombreModulo"] = utf8_encode($r_modulo["Titulo"]);
							else
								$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));
							
							
							$modulo["Orden"] = $r_modulo["Orden"];
							$icono_modulo = $r_modulo["Icono"] ;						
							if (!empty($r_modulo["Icono"])):
								$foto = MODULO_ROOT.$r_modulo["Icono"];								
							else:
								$foto="";	
							endif;
							$modulo["Icono"] = $foto;
							array_push($response_modulo, $modulo);
						endif;	
		
					}//ednw while
				}
				
				
				
				
				//Modulos Sistema Menu Lateral
				unset($modulo);				
				$response_modulo_lateral = array();
				$sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '".$id_club."' and Activo = 'S' and Ubicacion like '%Lateral%' ORDER BY Orden";
				$qry_modulo = $dbo->query( $sql_modulo );
				if( $dbo->rows( $qry_modulo ) > 0 )
				{	
					
					while( $r_modulo = $dbo->fetchArray( $qry_modulo ) )
					{
						// Verificar si el modulo tiene contenido para mostrar
						$flag_mostrar = SIMWebService::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);					
						//$flag_mostrar=0;
						if($flag_mostrar==0):
							$modulo["IDClub"] = $id_club;
							$modulo["IDModulo"] = $r_modulo["IDModulo"];
							//$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));
							if(!empty($r_modulo["Titulo"]))
								$modulo["NombreModulo"] = utf8_encode($r_modulo["Titulo"]);
							else
								$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));
					
							$modulo["Orden"] = $r_modulo["Orden"];
							$icono_modulo = $r_modulo["Icono"] ;						
							if (!empty($r_modulo["Icono"])):
								$foto = MODULO_ROOT.$r_modulo["Icono"];
							else:
								$foto="";	
							endif;
							$modulo["Icono"] = $foto;
							array_push($response_modulo_lateral, $modulo);
						endif;	
		
					}//ednw while
				}
				
				
				// Georeferenciacion
				//Tomo los valores de accesos que debe apolicar para invitados
				$IDParametroAcceso = $dbo->getFields( "ParametroAcceso" , "IDParametroAcceso" , "IDClub = '".$id_club."'" );
                $datos_ParametroAcceso =$dbo->fetchAll("ParametroAcceso"," IDParametroAcceso = '".$IDParametroAcceso."' ","array");
				$response_georef = array();
				$georef["Georeferenciacion"] = $datos_ParametroAcceso["Georeferenciacion"];
				$georef["Latitud"] = $datos_ParametroAcceso["Latitud"];
				$georef["Longitud"] = $datos_ParametroAcceso["Longitud"];
				$georef["Rango"] = $datos_ParametroAcceso["Rango"];
				$georef["MensajeFueraRango"] = $datos_ParametroAcceso["MensajeFueraRango"];
				$georef["Georeferenciacion"] = $datos_ParametroAcceso["Georeferenciacion"];
				//array_push($response_georef, $georef);
				
				
				$datos_club["IDClub"] = $r["IDClub"];
				$datos_club["Nombre"] = $r["Nombre"];
				$datos_club["Direccion"] = $r["Direccion"];
				$datos_club["Telefono"] = $r["Telefono"];
				$datos_club["Email"] = $r["Email"];
				$datos_club["IDDiseno"] = $r["IDDiseno"];
				$datos_club["Foto"] = $foto_logo;
				$datos_club["Foto1"] = $foto1;
				$datos_club["Foto2"] = $foto2 ;
				$datos_club["Color1"] = $r["Color1"];
				$datos_club["Color2"] = $r["Color2"];
				$datos_club["Banner"] = $response_banner;
				$datos_club["Terminos"] = $r["Terminos"];
				
				
				//datos IOS Version
				$datos_club["iosVersion"] = $r["Version"];				
				$datos_club["iosEsencial"] = $r["Esencial"];
				$datos_club["iosversionMessage"] = $r["VersionMessage"];
				$datos_club["versionURLiOS"] = $r["VersionURLIOS"];
				
				//datos Android Version
				$datos_club["androidVersion"] = $r["VersionAndroid"];				
				$datos_club["androidEsencial"] = $r["EsencialAndroid"];
				$datos_club["androidversionMessage"] = $r["VersionMessageAndroid"];				
				$datos_club["versionURLAndroid"] = $r["VersionURLAndroid"];
				
				/*
				$datos_club["Version"] = $r["Version"];
				$datos_club["Esencial"] = $r["Esencial"];
				$datos_club["versionMessage"] = $r["VersionMessage"];
				$datos_club["versionURLiOS"] = $r["VersionURLIOS"];
				$datos_club["versionURLAndroid"] = $r["VersionURLAndroid"];
				*/				
				
				$datos_club["ServiciosReserva"] = $response_servicio;
				$datos_club["Modulos"] = $response_modulo;
				$datos_club["ModulosLateral"] = $response_modulo_lateral;
				$datos_club["Georeferenciacion"] = $georef;
				
				//Si es club es numero derecho si es Residencial o colegio Numero Doc
				if(!empty($r["LabelIdentificadorUsuario"])):
					$IdentificadorUsuario = $r["LabelIdentificadorUsuario"];
				else:
					$IdentificadorUsuario ="Numero de derecho";					
				endif;
				$datos_club["IdentificadorUsuario"] = $IdentificadorUsuario;

				array_push($response, $datos_club);

			}//ednw hile
			
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado club";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_app_empleado( $id_club )
	{
		
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM AppEmpleado WHERE IDClub = '".$id_club."'";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				if (!empty($r["FotoLogoApp"])){
					$foto_logo = CLUB_ROOT.$r["FotoLogoApp"];
				}
				if (!empty($r["FotoDiseno1"])){
					$foto1 = CLUB_ROOT.$r["FotoDiseno1"];
				}
				if (!empty($r["FotoDiseno2"])){
					$foto2 = CLUB_ROOT.$r["FotoDiseno2"];
				}
				
				//Banners
				$response_banner = array();
				$sql_banner = "SELECT * FROM BannerApp WHERE DirigidoA = 'E' and Publicar = 'S' and IDClub = '".$id_club."' ORDER BY IDBannerApp";
				$qry_banner = $dbo->query( $sql_banner );
				if( $dbo->rows( $qry_banner ) > 0 )
				{
				
					while( $r_banner = $dbo->fetchArray( $qry_banner ) )
					{
						$banner["IDClub"] = $id_club;
						$banner["IDBannerApp"] = $r_banner["IDBannerApp"];
						if (!empty($r_banner["Foto1"])):
							$foto = BANNERAPP_ROOT.$r_banner["Foto1"];
						else:
							$foto="";	
						endif;
						$banner["Foto"] = $foto;
						array_push($response_banner, $banner);
		
					}//ednw hile
				}
				
				// Georeferenciacion
				//Tomo los valores de accesos que debe apolicar para invitados
				$IDParametroAcceso = $dbo->getFields( "ParametroAcceso" , "IDParametroAcceso" , "IDClub = '".$id_club."'" );
                $datos_ParametroAcceso =$dbo->fetchAll("ParametroAcceso"," IDParametroAcceso = '".$IDParametroAcceso."' ","array");
				$response_georef = array();
				$georef["Georeferenciacion"] = $datos_ParametroAcceso["Georeferenciacion"];
				$georef["Latitud"] = $datos_ParametroAcceso["Latitud"];
				$georef["Longitud"] = $datos_ParametroAcceso["Longitud"];
				$georef["Rango"] = $datos_ParametroAcceso["Rango"];
				$georef["MensajeFueraRango"] = $datos_ParametroAcceso["MensajeFueraRango"];
				$georef["Georeferenciacion"] = $datos_ParametroAcceso["Georeferenciacion"];
				//array_push($response_georef, $georef);
				
				
				$datos_club["IDClub"] = $r["IDClub"];				
				$datos_club["Foto"] = $foto_logo;
				$datos_club["Foto1"] = $foto1;
				$datos_club["Foto2"] = $foto2 ;
				$datos_club["Color1"] = $r["Color1"];
				$datos_club["Color2"] = $r["Color2"];
				$datos_club["Banner"] = $response_banner;
				$datos_club["Terminos"] = $r["Terminos"];
				
				
				//datos IOS Version
				$datos_club["iosVersion"] = $r["Version"];				
				$datos_club["iosEsencial"] = $r["Esencial"];
				$datos_club["iosversionMessage"] = $r["VersionMessage"];
				$datos_club["versionURLiOS"] = $r["VersionURLIOS"];
				
				//datos Android Version
				$datos_club["androidVersion"] = $r["VersionAndroid"];				
				$datos_club["androidEsencial"] = $r["EsencialAndroid"];
				$datos_club["androidversionMessage"] = $r["VersionMessageAndroid"];				
				$datos_club["versionURLAndroid"] = $r["VersionURLAndroid"];
				
				$datos_club["Georeferenciacion"] = $georef;
				

				array_push($response, $datos_club);

			}//ednw hile
			
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado club";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_parametro_acceso( $id_club )
{
		
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM ParametroAcceso WHERE IDClub = '".$id_club."'";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				if (!empty($r["IconoFamiliar"])){
					$foto_familiar = CLUB_ROOT.$r["IconoFamiliar"];
				}
				if (!empty($r["IconoIndividual"])){
					$foto_individual = CLUB_ROOT.$r["IconoIndividual"];
				}
				
				//Tipo Invitado
				$response_tipo_invitado = array();
				$array_tipo_invitado = explode("|",$r["TipoInvitado"]);
				if(count($array_tipo_invitado)>0):
					foreach($array_tipo_invitado as $nombre_tipo):
						$dato_tipo_invitado[] = $nombre_tipo;
						array_push($response_tipo_invitado, $nombre_tipo);
					endforeach;
				endif;
				
				//Tipo Autorizacion
				$response_tipo_autorizacion = array();
				$array_tipo_autorizacion = explode("|",$r["TipoAutorizacion"]);
				if(count($array_tipo_autorizacion)>0):
					foreach($array_tipo_autorizacion as $nombre_tipo):
						$dato_tipo_autorizacion[] = $nombre_tipo;
						array_push($response_tipo_autorizacion, $nombre_tipo);
					endforeach;
				endif;
				
				//Tipo Documentos
				$response_tipodoc = array();
				$sql_tipodoc = "SELECT * FROM TipoDocumento WHERE Publicar = 'S' ORDER BY Nombre";
				$qry_tipodoc = $dbo->query( $sql_tipodoc );
				if( $dbo->rows( $qry_tipodoc ) > 0 )
				{	
					while( $r_tipodoc = $dbo->fetchArray( $qry_tipodoc ) )
					{
						$tipodoc["IDTipoDocumento"] = (int)$r_tipodoc["IDTipoDocumento"];
						$tipodoc["Nombre"] = $r_tipodoc["Nombre"];
						array_push($response_tipodoc, $tipodoc);
		
					}//ednw hile
				}
				
				// Georeferenciacion
					$response_georef = array();
					$georef["Georeferenciacion"] = $r["Georeferenciacion"];
					$georef["Latitud"] = $r["Latitud"];
					$georef["Longitud"] = $r["Longitud"];
					$georef["Rango"] = $r["Rango"];
					$georef["MensajeFueraRango"] = $r["MensajeFueraRango"];					
					//array_push($response_georef, $georef);
				
				// Georeferenciacion
					$response_georef_contratista = array();
					$georef_contratista["Georeferenciacion"] = $r["GeoreferenciacionContratista"];
					$georef_contratista["Latitud"] = $r["LatitudContratista"];
					$georef_contratista["Longitud"] = $r["LongitudContratista"];
					$georef_contratista["Rango"] = $r["Rango"];
					$georef_contratista["MensajeFueraRango"] = $r["MensajeFueraRangoContratista"];					
					//array_push($response_georef, $georef);
				
				
				
				$datos_acceso["IDClub"] = $r["IDClub"];
				$datos_acceso["GrupoFamiliar"] = $r["GrupoFamiliar"];
				$datos_acceso["IconoFamiliar"] = $foto_familiar;
				$datos_acceso["NombreFamiliar"] = $r["NombreFamiliar"];
				$datos_acceso["Invitado"] = $r["Invitado"];
				$datos_acceso["IconoIndividual"] = $foto_individual;
				$datos_acceso["NombreIndividual"] = $r["NombreIndividual"];				
				$datos_acceso["TipoInvitado"] = $response_tipo_invitado;
				$datos_acceso["TipoAutorizacion"] = $response_tipo_autorizacion ;
				$datos_acceso["TipoDocumento"] = $response_tipodoc ;
				$datos_acceso["TextoMenorEdad"] = $r["TextoMenorEdad"] ;
				$datos_acceso["GeoreferenciacionInvitado"] = $georef;
				$datos_acceso["GeoreferenciacionContratista"] = $georef_contratista;
				
				array_push($response, $datos_acceso);

			}//ednw hile
			
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado club";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function


	
	
	function get_seccion( $id_club , $id_socio = "")
	{
		if (!empty($id_socio)):
			$condicion = " and SS.IDSeccion = S.IDSeccion and IDSocio = '".$id_socio."' "; 
			$tabla_join = ", SocioSeccion SS ";
		endif;	
		
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT S.* FROM Seccion S ".$tabla_join." WHERE S.Publicar = 'S' and S.IDClub = '".$id_club."' ". $condicion ." ORDER BY Orden";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{	
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				// verifico que la seccion tenga por lo menos una noticia publicada
				$id_noticia = $dbo->getFields( "Noticia" , "IDNoticia" , "IDSeccion = '".$r["IDSeccion"]."' and Publicar = 'S'" );	
				if(!empty($id_noticia)):
					$seccion["IDClub"] = $r["IDClub"];
					$seccion["IDSeccion"] = $r["IDSeccion"];
					$seccion["Nombre"] = $r["Nombre"];
					$seccion["Descripcion"] = $r["Descripcion"];
					array_push($response, $seccion);
				endif;

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	function get_seccionevento( $id_club , $id_socio = "")
	{
		$dbo =& SIMDB::get();
		
		if (!empty($id_socio)):
			$condicion = " and SSE.IDSeccionEvento = S.IDSeccionEvento and IDSocio = '".$id_socio."' "; 
			$tabla_join = ", SocioSeccionEvento SSE ";
		endif;	
		
		
		$response = array();
		$sql = "SELECT S.* FROM SeccionEvento S ".$tabla_join." WHERE S.Publicar = 'S' and S.IDClub = '".$id_club."' ". $condicion ." ORDER BY S.Orden";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				// verifico que la seccion tenga por lo menos una noticia publicada
				$id_noticia = $dbo->getFields( "Evento" , "IDEvento" , "IDSeccionEvento = '".$r["IDSeccionEvento"]."' and Publicar = 'S'" );	
				if(!empty($id_noticia)):
					$seccion["IDClub"] = $r["IDClub"];
					$seccion["IDSeccion"] = $r["IDSeccionEvento"];
					$seccion["Nombre"] = $r["Nombre"];
					$seccion["Descripcion"] = $r["Descripcion"];
					array_push($response, $seccion);
				endif;	

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	function get_secciongaleria( $id_club , $id_socio = "")
	{
		$dbo =& SIMDB::get();
		
		if (!empty($id_socio)):
			$condicion = " and SSG.IDSeccionGaleria = S.IDSeccionGaleria and IDSocio = '".$id_socio."' "; 
			$tabla_join = ", SocioSeccionGaleria SSG ";
		endif;	
		
		
		$response = array();
		$sql = "SELECT S.* FROM SeccionGaleria S ".$tabla_join." WHERE S.Publicar = 'S' and S.IDClub = '".$id_club."' ". $condicion ." ORDER BY S.Nombre";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				// verifico que la seccion tenga por lo menos una galeria publicada
				$id_galeria = $dbo->getFields( "Galeria" , "IDGaleria" , "IDSeccionGaleria = '".$r["IDSeccionGaleria"]."' and Publicar = 'S'" );	
				if(!empty($id_galeria)):
					$seccion["IDClub"] = $r["IDClub"];
					$seccion["IDSeccion"] = $r["IDSeccionGaleria"];
					$seccion["Nombre"] = $r["Nombre"];
					$seccion["Descripcion"] = $r["Descripcion"];
					array_push($response, $seccion);
				endif;	

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_seccion_club( $id_club , $id_socio = "")
	{
		$dbo =& SIMDB::get();
		$response = array();
		$contador_resultado=0;
		
		//Secciones Noticia
		$sql = "SELECT * FROM Seccion S  WHERE S.Publicar = 'S' and S.IDClub = '".$id_club."' ORDER BY S.Nombre";
		$qry = $dbo->query( $sql );
		while( $r = $dbo->fetchArray( $qry ) )
		{
			$contador_resultado++;	
			$seccion["IDClub"] = $r["IDClub"];
			$seccion["Tipo"] = "Noticia";
			$seccion["IDSeccion"] = $r["IDSeccion"];
			$seccion["Nombre"] = $r["Nombre"];
			$seccion["Descripcion"] = $r["Descripcion"];
		
			// verifico si es de preferencia del socio
			if(!empty($id_socio)):
				$sql_preferencia = "Select * From SocioSeccion Where IDSocio = '".$id_socio."' and IDSeccion = '".$seccion["IDSeccion"]."'";
				$result_preferencia=$dbo->query($sql_preferencia);
				if( $dbo->rows( $result_preferencia ) > 0 ):
					$seccion["PreferenciaSocio"] = "S";
				else:
					$seccion["PreferenciaSocio"] = "N";	
				endif;
			else:
					$seccion["PreferenciaSocio"] = "N";		
			endif;	
			
			
			array_push($response, $seccion);
		}//end while
		
		
		unset($seccion);
		//Secciones Evento
		$sql = "SELECT * FROM SeccionEvento S  WHERE S.Publicar = 'S' and S.IDClub = '".$id_club."' ORDER BY S.Nombre";
		$qry = $dbo->query( $sql );
		while( $r = $dbo->fetchArray( $qry ) )
		{
			$contador_resultado++;	
			$seccion["IDClub"] = $r["IDClub"];
			$seccion["Tipo"] = "Evento";
			$seccion["IDSeccion"] = $r["IDSeccionEvento"];
			$seccion["Nombre"] = $r["Nombre"];
			$seccion["Descripcion"] = $r["Descripcion"];
		
			// verifico si es de preferencia del socio
			if(!empty($id_socio)):
				$sql_preferencia = "Select * From SocioSeccionEvento Where IDSocio = '".$id_socio."' and IDSeccionEvento = '".$seccion["IDSeccion"]."'";
				$result_preferencia=$dbo->query($sql_preferencia);
				if( $dbo->rows( $result_preferencia ) > 0 ):
					$seccion["PreferenciaSocio"] = "S";
				else:
					$seccion["PreferenciaSocio"] = "N";	
				endif;
			else:
					$seccion["PreferenciaSocio"] = "N";		
			endif;	
			
			array_push($response, $seccion);
		}//end while
		
		unset($seccion);
		//Secciones Galeria
		$sql = "SELECT * FROM SeccionGaleria S  WHERE S.Publicar = 'S' and S.IDClub = '".$id_club."' ORDER BY S.Nombre";
		$qry = $dbo->query( $sql );
		while( $r = $dbo->fetchArray( $qry ) )
		{
			$contador_resultado++;	
			$seccion["IDClub"] = $r["IDClub"];
			$seccion["Tipo"] = "Galeria";
			$seccion["IDSeccion"] = $r["IDSeccionGaleria"];
			$seccion["Nombre"] = $r["Nombre"];
			$seccion["Descripcion"] = $r["Descripcion"];
		
			// verifico si es de preferencia del socio
			if(!empty($id_socio)):
				$sql_preferencia = "Select * From SocioSeccionGaleria Where IDSocio = '".$id_socio."' and IDSeccionGaleria = '".$seccion["IDSeccion"]."'";
				$result_preferencia=$dbo->query($sql_preferencia);
				if( $dbo->rows( $result_preferencia ) > 0 ):
					$seccion["PreferenciaSocio"] = "S";
				else:
					$seccion["PreferenciaSocio"] = "N";	
				endif;
			else:
					$seccion["PreferenciaSocio"] = "N";		
			endif;
			
			array_push($response, $seccion);
		}//end while
			
			
			
		$message = $contador_resultado . " Encontrados";	
		if( $contador_resultado > 0 )
		{	
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_area_club( $IDClub)
	{	
		
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * From Area WHERE Activo = 'S' and IDClub = '".$IDClub."' ORDER BY Nombre";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{	
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				
					$area["IDClub"] = $r["IDClub"];
					$area["IDArea"] = $r["IDArea"];
					$area["Nombre"] = $r["Nombre"];
					$area["CorreoResponsable"] = $r["CorreoResponsable"];
					array_push($response, $area);				
			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_pqr_socio( $IDClub, $IDSocio, $IDPqr)
	{	
		$dbo =& SIMDB::get();
		
		$response = array();
		
		$array_id_consulta[]=$IDSocio;
		
		if(!empty($IDPqr))
			$condicion = " and IDPqr = '".$IDPqr."'";
		
		
		$sql = "SELECT * FROM Pqr WHERE IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' $condicion ORDER BY IDPqr Desc ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while ($row_pqr = $dbo->fetchArray($qry))	:
				$pqr["IDClub"] = $IDClub;
				$pqr["IDSocio"] = $IDSocio;
				$pqr["IDPqr"] = $row_pqr["IDPqr"];
				$pqr["IDArea"] = $row_pqr["IDArea"];
				$pqr["IDTipoPqr"] = $row_pqr["IDTipoPqr"];
				$pqr["NombreArea"] = utf8_encode($dbo->getFields( "Area" , "Nombre" , "IDArea = '" . $row_pqr["IDArea"] . "'" ));
				$pqr["Tipo"] = utf8_encode($dbo->getFields( "TipoPqr" , "Nombre" , "IDTipoPqr = '" . $row_pqr["IDTipoPqr"] . "'" ));
				$pqr["Asunto"] = utf8_encode($row_pqr["Asunto"]);
				$pqr["Comentario"] = utf8_encode($row_pqr["Descripcion"]);
				$pqr["Archivo"] = PQR_ROOT.$row_pqr["Archivo1"];
				$pqr["Fecha"] = $row_pqr["Fecha"];
				$pqr["Estado"] = $dbo->getFields( "PqrEstado" , "Nombre" , "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'" );
				
				//Bitacora Pqr
				$response_bitacora = array();
				$sql_bitacora = $dbo->query("SELECT * FROM Detalle_Pqr WHERE IDPQR = '".$row_pqr["IDPqr"]."' Order By 	IDDetallePqr Desc");
				while ($r_bitacora = $dbo->fetchArray($sql_bitacora)):
					$bitacora[IDDetallePqr]=$r_bitacora["IDDetallePqr"];
					 if ($r_bitacora[IDUsuario] > 0) { 
						$usuario_responde =  "CLUB: " . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" .$r_bitacora[IDUsuario] . "'" );	
						//quito caracteres especiales
						$respuesta_pqr =  strip_tags($r_bitacora["Respuesta"]);
						//$respuesta_pqr = str_replace("&nbsp;","",$respuesta_pqr);
						$respuesta_pqr  = html_entity_decode($respuesta_pqr); 
						
				     } elseif($r_bitacora[IDSocio] > 0) { 
						$nombre_cliente = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$r_bitacora[IDSocio] . "'" ));
						$apellido_cliente = utf8_encode($dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$r_bitacora[IDSocio] . "'" ));
						$usuario_responde = "Socio: " . $nombre_cliente . " " . $apellido_cliente;
						$respuesta_pqr =  utf8_encode($r_bitacora["Respuesta"]);
				    } 
					$bitacora[UsuarioResponde]=$usuario_responde;
					$bitacora[RespuestaPqr]=$respuesta_pqr;
					$bitacora[Estado]=$dbo->getFields( "PqrEstado" , "Nombre" , "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'" );
					$bitacora[FechaRespuesta]=substr($r_bitacora["Fecha"],0,10);					
					array_push($response_bitacora, $bitacora);
				endwhile;
				
				//Agrego el primer comentario como parte del seguimiento
					$bitacora[IDDetallePqr]=$row_pqr["IDPqr"];
					$nombre_cliente = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" .$IDSocio . "'" ));
					$apellido_cliente = utf8_encode($dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$IDSocio . "'" ));
					$usuario_responde = "Socio: " . $nombre_cliente . " " . $apellido_cliente;
					$respuesta_pqr =  utf8_encode($row_pqr["Descripcion"]);
					$bitacora[UsuarioResponde]=$usuario_responde;
					$bitacora[RespuestaPqr]=$respuesta_pqr;
					$bitacora[Estado]=$dbo->getFields( "PqrEstado" , "Nombre" , "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'" );
					$bitacora[FechaRespuesta]=substr($row_pqr["Fecha"],0,10);					
					array_push($response_bitacora, $bitacora);
				
				
				
				
				$pqr["Seguimiento"] = $response_bitacora;				
				array_push($response, $pqr);
			endwhile;	
				
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se han encontrado pqr registrados";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;		
			
	}// fin function
	
	
	
	function get_noticias( $id_club , $id_seccion = "", $id_socio = "", $tag="")
	{
		
		$dbo =& SIMDB::get();
			
		// Secciones Socio
		if (!empty($id_socio)):
			$sql_seccion_socio = $dbo->query("Select * From SocioSeccion Where IDSocio = '".$id_socio."'");
			while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)):
				$array_secciones_socio[] = $row_seccion["IDSeccion"];
			endwhile;
	
			if (count($array_secciones_socio)>0):
				$id_secciones = implode(",",$array_secciones_socio);
				$array_condiciones[] = " IDSeccion in(".$id_secciones.") ";
			endif;
		endif;	
		
		// Seccion Especifica
		if (!empty($id_seccion)):
			$array_condiciones[] = " IDSeccion  = '".$id_seccion."'";
		endif;	
		
		// Tag
		if (!empty($tag)):
			$array_condiciones[] = " (Titular  like '%".$tag."%' or Introduccion like '%".$tag."%' or Cuerpo like '%".$tag."%')";
		endif;	

		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);
			$condiciones_noticia = " and " .$condiciones; 
		endif;	
	
		
		
		$response = array();
		$sql = "SELECT * FROM Noticia WHERE DirigidoA <> 'E' and Publicar = 'S' and IDClub = '".$id_club."'" . $condiciones_noticia ." ORDER BY FechaInicio DESC,Orden";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$noticia["IDClub"] = $r["IDClub"];
				$noticia["IDSeccion"] = $r["IDSeccion"];
				$noticia["IDNoticia"] = $r["IDNoticia"];
				$noticia["Titular"] = $r["Titular"];
				$noticia["Introduccion"] = $r["Introduccion"];
				
				$cuerpo_noticia = str_replace("/file/noticia/editor/",IMGNOTICIA_ROOT.'editor/',$r["Cuerpo"]);	
				
				//Documentos adjuntos
				if(!empty($r["Adjunto1File"])):						
					$cuerpo_noticia = "<br><a href='". IMGNOTICIA_ROOT . $r["Adjunto1File"]."' >"  . $r["Adjunto1File"].'</a>';
				endif;
				if(!empty($r["Adjunto2File"])):
					$cuerpo_noticia = "<br><a href='". IMGNOTICIA_ROOT . $r["AdjuntoeFile"]."' >"  . $r["AdjuntoeFile"].'</a>';
				endif;
							
				$noticia["Cuerpo"] = $cuerpo_noticia;
				
				$noticia["Fecha"] = $r["FechaInicio"];
				if (!empty($r["NoticiaFile"])):
					if(strstr(strtolower($r["NoticiaFile"]),"http://"))
						$foto1 = $r["NoticiaFile"];
					else
						$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
					//$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
				else:
					$foto1="";	
				endif;
				
				if (!empty($r["FotoDestacada"])):
					if(strstr(strtolower($r["FotoDestacada"]),"http://"))
						$foto2 = $r["FotoDestacada"];
					else
						$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
				
					//$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
				else:
					$foto2="";	
				endif;
				
				$noticia["Foto"] = $foto1;
				$noticia["Foto2"] = $foto2;
				
				
				array_push($response, $noticia);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	function get_noticias_empleados( $id_club , $id_seccion = "", $id_usuario = "", $tag="")
	{
		
		$dbo =& SIMDB::get();
			
		// Secciones Empleado
		if (!empty($id_empleado)):
			$sql_seccion_empleado = $dbo->query("Select * From UsuarioSeccion Where IDUsuario = '".$id_usuario."'");
			while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)):
				$array_secciones_empleado[] = $row_seccion["IDSeccion"];
			endwhile;
	
			if (count($array_secciones_empleado)>0):
				$id_secciones = implode(",",$array_secciones_empleado);
				$array_condiciones[] = " IDSeccion in(".$id_secciones.") ";
			endif;
		endif;	
		
		// Seccion Especifica
		if (!empty($id_seccion)):
			$array_condiciones[] = " IDSeccion  = '".$id_seccion."'";
		endif;	
		
		// Tag
		if (!empty($tag)):
			$array_condiciones[] = " (Titular  like '%".$tag."%' or Introduccion like '%".$tag."%' or Cuerpo like '%".$tag."%')";
		endif;	

		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);
			$condiciones_noticia = " and " .$condiciones; 
		endif;	
	
		
		
		$response = array();
		$sql = "SELECT * FROM Noticia WHERE DirigidoA = 'E' and Publicar = 'S' and IDClub = '".$id_club."'" . $condiciones_noticia ." ORDER BY FechaInicio DESC,Orden";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$noticia["IDClub"] = $r["IDClub"];
				$noticia["IDSeccion"] = $r["IDSeccion"];
				$noticia["IDNoticia"] = $r["IDNoticia"];
				$noticia["Titular"] = $r["Titular"];
				$noticia["Introduccion"] = $r["Introduccion"];
				
				$cuerpo_noticia = str_replace("/file/noticia/editor/",IMGNOTICIA_ROOT.'editor/',$r["Cuerpo"]);				
				
				//Documentos adjuntos
				if(!empty($r["Adjunto1File"])):						
					$cuerpo_noticia = "<br><a href='". IMGNOTICIA_ROOT . $r["Adjunto1File"]."' >"  . $r["Adjunto1File"].'</a>';
				endif;
				if(!empty($r["Adjunto2File"])):
					$cuerpo_noticia = "<br><a href='". IMGNOTICIA_ROOT . $r["AdjuntoeFile"]."' >"  . $r["AdjuntoeFile"].'</a>';
				endif;
				
				$noticia["Cuerpo"] = $cuerpo_noticia;
				
				
				
				$noticia["Fecha"] = $r["FechaInicio"];
				if (!empty($r["NoticiaFile"])):
					if(strstr(strtolower($r["NoticiaFile"]),"http://"))
						$foto1 = $r["NoticiaFile"];
					else
						$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
					//$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
				else:
					$foto1="";	
				endif;
				
				if (!empty($r["FotoDestacada"])):
					if(strstr(strtolower($r["FotoDestacada"]),"http://"))
						$foto2 = $r["FotoDestacada"];
					else
						$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
				
					//$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
				else:
					$foto2="";	
				endif;
				
				$noticia["Foto"] = $foto1;
				$noticia["Foto2"] = $foto2;
				
				
				array_push($response, $noticia);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_eventos( $id_club , $id_seccion = "", $id_socio = "", $tag="")
	{
		$dbo =& SIMDB::get();
			
		// Secciones Socio
		if (!empty($id_socio)):
			$sql_seccion_socio = $dbo->query("Select * From SocioSeccionEvento Where IDSocio = '".$id_socio."'");
			while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)):
				$array_secciones_socio[] = $row_seccion["IDSeccionEvento"];
			endwhile;
			
			if (count($array_secciones_socio)>0):
				$id_secciones = implode(",",$array_secciones_socio);
				$array_condiciones[] = " IDSeccionEvento in(".$id_secciones.") ";
			endif;
		endif;	
		
		// Seccion Especifica
		if (!empty($id_seccion)):
			$array_condiciones[] = " IDSeccionEvento  = '".$id_seccion."'";
		endif;	
		
		// Tag
		if (!empty($tag)):
			$array_condiciones[] = " (Titular  like '%".$tag."%' or Introduccion like '%".$tag."%' or Cuerpo like '%".$tag."%' or Lugar like '%".$tag."%' )";
		endif;	

		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);
			$condiciones_noticia = " and " .$condiciones; 
		endif;	
	
		
		
		$response = array();
		$sql = "SELECT * FROM Evento WHERE DirigidoA <> 'E' and Publicar = 'S' and FechaInicio <= NOW() and FechaFin >= NOW() and  IDClub = '".$id_club."'" . $condiciones_noticia ." ORDER BY FechaInicio DESC";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$evento["IDClub"] = $r["IDClub"];
				$evento["IDSeccionEvento"] = $r["IDSeccionEvento"];
				$evento["IDEvento"] = $r["IDEvento"];
				$evento["Titular"] = $r["Titular"];
				$evento["Introduccion"] = $r["Introduccion"];
				
				$cuerpo_evento = str_replace("/file/noticia/editor/",IMGNOTICIA_ROOT.'editor/',$r["Cuerpo"]);								
				$evento["Cuerpo"] = $cuerpo_evento;
				
				$evento["CuerpoEmail"] = $r["CuerpoEmail"];
				$evento["Fecha"] = $r["FechaEvento"];
				$evento["FechaFinEvento"] = $r["FechaFinEvento"];
				$evento["Lugar"] = $r["Lugar"];
				$evento["Valor"] = $r["Valor"];
				$evento["EmailContacto"] = $r["EmailContacto"];
				
				if (!empty($r["EventoFile"])):
					if(strstr(strtolower($r["EventoFile"]),"http://"))
						$foto1 = $r["EventoFile"];
					else
						$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
					//$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
				else:
					$foto1="";	
				endif;
				
				if (!empty($r["FotoDestacada"])):
					if(strstr(strtolower($r["FotoDestacada"]),"http://"))
						$foto2 = $r["FotoDestacada"];
					else
						$foto2 = IMGEVENTO_ROOT.$r["FotoDestacada"];				
					//$foto2 = IMGEVENTO_ROOT.$r["FotoDestacada"];
				else:
					$foto2="";	
				endif;
				
				$evento["Foto"] = $foto1;
				$evento["Foto2"] = $foto2;
				
				array_push($response, $evento);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	function get_eventos_empleados( $id_club , $id_seccion = "", $id_usuario = "", $tag="")
	{
		$dbo =& SIMDB::get();
			
		// Secciones Empleado
		if (!empty($id_usuario)):
			$sql_seccion_empleado = $dbo->query("Select * From UsuarioSeccionEvento Where IDSocio = '".$id_socio."'");
			while ($row_seccion = $dbo->fetchArray($sql_seccion_empleado)):
				$array_secciones_empleado[] = $row_seccion["IDSeccionEvento"];
			endwhile;
			
			if (count($array_secciones_empleado)>0):
				$id_secciones = implode(",",$array_secciones_empleado);
				$array_condiciones[] = " IDSeccionEvento in(".$id_secciones.") ";
			endif;
		endif;	
		
		// Seccion Especifica
		if (!empty($id_seccion)):
			$array_condiciones[] = " IDSeccionEvento  = '".$id_seccion."'";
		endif;	
		
		// Tag
		if (!empty($tag)):
			$array_condiciones[] = " (Titular  like '%".$tag."%' or Introduccion like '%".$tag."%' or Cuerpo like '%".$tag."%' or Lugar like '%".$tag."%' )";
		endif;	

		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);
			$condiciones_noticia = " and " .$condiciones; 
		endif;	
	
		
		
		$response = array();
		$sql = "SELECT * FROM Evento WHERE DirigidoA = 'E' and Publicar = 'S' and FechaInicio <= NOW() and FechaFin >= NOW() and  IDClub = '".$id_club."'" . $condiciones_noticia ." ORDER BY FechaEvento DESC";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$evento["IDClub"] = $r["IDClub"];
				$evento["IDSeccionEvento"] = $r["IDSeccionEvento"];
				$evento["IDEvento"] = $r["IDEvento"];
				$evento["Titular"] = $r["Titular"];
				$evento["Introduccion"] = $r["Introduccion"];
				
				$cuerpo_evento = str_replace("/file/noticia/editor/",IMGNOTICIA_ROOT.'editor/',$r["Cuerpo"]);								
				$evento["Cuerpo"] = $cuerpo_evento;
				
				$evento["CuerpoEmail"] = $r["CuerpoEmail"];
				$evento["Fecha"] = $r["FechaEvento"];
				$evento["FechaFinEvento"] = $r["FechaFinEvento"];
				$evento["Lugar"] = $r["Lugar"];
				$evento["Valor"] = $r["Valor"];
				$evento["EmailContacto"] = $r["EmailContacto"];
				
				if (!empty($r["EventoFile"])):
					if(strstr(strtolower($r["EventoFile"]),"http://"))
						$foto1 = $r["EventoFile"];
					else
						$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
					//$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
				else:
					$foto1="";	
				endif;
				
				if (!empty($r["FotoDestacada"])):
					if(strstr(strtolower($r["FotoDestacada"]),"http://"))
						$foto2 = $r["FotoDestacada"];
					else
						$foto2 = IMGEVENTO_ROOT.$r["FotoDestacada"];				
					//$foto2 = IMGEVENTO_ROOT.$r["FotoDestacada"];
				else:
					$foto2="";	
				endif;
				
				$evento["Foto"] = $foto1;
				$evento["Foto2"] = $foto2;
				
				array_push($response, $evento);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	function get_directorio( $id_club )
	{
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM Directorio WHERE Publicar = 'S' and IDClub = '".$id_club."' ORDER BY Nombre";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$directorio["IDClub"] = $r["IDClub"];
				$directorio["Nombre"] =  $r["Nombre"];
				$directorio["Descripcion"] = utf8_encode($r["Descripcion"]);
				$directorio["Telefono"] = $r["Telefono"];
				$directorio["Email"] = $r["Email"];
				if (!empty($r["Foto1"])):
					$foto = DIRECTORIO_ROOT.$r["Foto1"];
				else:
					$foto="";	
				endif;
				$directorio["Foto1"] = $foto;
				array_push($response, $directorio);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registro";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	function get_restaurante( $id_club )
	{
		$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM Restaurante WHERE Publicar = 'S' and IDClub = '".$id_club."' ORDER BY Nombre";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$restaurante["IDClub"] = $r["IDClub"];
				$restaurante["Nombre"] = $r["Nombre"];
				$restaurante["Lugar"] = $r["Lugar"];
				$restaurante["Menu"] = $r["Menu"];
				$restaurante["Horario"] = $r["Horario"];
				$restaurante["Localizacion"] = $r["Localizacion"];
				
				if (!empty($r["RestauranteFile"])):
					$foto1 = IMGEVENTO_ROOT.$r["RestauranteFile"];
				else:
					$foto1="";	
				endif;
				
				$restaurante["Foto"] = $foto1;

				//Para la carta
				if (!empty($r["CartaFile"])):
					$foto1 = IMGEVENTO_ROOT.$r["CartaFile"];
				else:
					$foto1="";	
				endif;				
				$restaurante["FotoCarta"] = $foto1;
				
				$fotos_carta =array();
				$fotos_carta[]=$foto1;
				
				//Para la carta
				if (!empty($r["CartaFile2"])):
					$foto2 = IMGEVENTO_ROOT.$r["CartaFile2"];
					$fotos_carta[]=$foto2;
				else:
					$foto2="";	
				endif;				
				//$restaurante["FotoCarta2"] = $foto2;
				
				
				
				//Para la carta
				if (!empty($r["CartaFile3"])):
					$foto3 = IMGEVENTO_ROOT.$r["CartaFile3"];
					$fotos_carta[]=$foto3;
				else:
					$foto3="";	
				endif;				
				//$restaurante["FotoCarta3"] = $foto3;
				
				
				
				//Para la carta
				if (!empty($r["CartaFile4"])):
					$foto4 = IMGEVENTO_ROOT.$r["CartaFile4"];
					$fotos_carta[]=$foto4;
				else:
					$foto4="";	
				endif;				
				//$restaurante["FotoCarta4"] = $foto4;
				
				
				//Para la carta
				if (!empty($r["CartaFile5"])):
					$foto5 = IMGEVENTO_ROOT.$r["CartaFile5"];
					$fotos_carta[]=$foto5;
				else:
					$foto5="";	
				endif;				
				
				
				//Para la carta
				if (!empty($r["CartaFile6"])):
					$foto6 = IMGEVENTO_ROOT.$r["CartaFile6"];
					$fotos_carta[]=$foto6;
				else:
					$foto6="";	
				endif;				
				
				
				
				$restaurante["fotoscarta"] = $fotos_carta;


				
				array_push($response, $restaurante);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registro";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_documento( $id_club, $id_tipo_archivo = "", $id_servicio = "" )
	{
		$dbo =& SIMDB::get();
		
		
		//inserta _log		
		//$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros) Values ('get_documento','TipoArchivo: ".$id_tipo_archivo. " IDServicio:" . $id_servicio . "')");
		
		// Tipo Archivo Especifico
		if (!empty($id_tipo_archivo)):
			$condiciones = " and IDTipoArchivo  = '".$id_tipo_archivo."'";
		endif;	
		
		// Servicio Especifico
		if (!empty($id_servicio)):
			$condiciones = " and IDServicio  = '".$id_servicio."'";
		endif;	
		
		
		$response = array();
		$sql = "SELECT * FROM Documento WHERE Publicar = 'S' and IDClub = '".$id_club."' ".$condiciones." ORDER BY Nombre";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$documento["IDClub"] = $r["IDClub"];
				$documento["IDTipoArchivo"] = $r["IDTipoArchivo"];
				$documento["TipoArchivo"] = $dbo->getFields( "TipoArchivo" , "Nombre" , "IDTipoArchivo = '".$r["IDTipoArchivo"]."'" );
				
				$foto_servicio = "";
				if (!empty($r["Icono"])):
					$foto_servicio = DOCUMENTO_ROOT.$r["Icono"];
				else:
					$foto_servicio="";	
				endif;
				
				
				if(empty($r["IDServicio"])):
					$servicio = "";
					$id_servicio = "";
				else:
					$id_servicio= $r["IDServicio"];
					$id_servicio_maestro= $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '".$r["IDServicio"]."'" );	
					$servicio =  $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$id_servicio_maestro."'" );	
					$icono_servicio =  $dbo->getFields( "Servicio" , "Icono" , "IDServicio = '".$r["IDServicio"]."'" );	
					
					if(empty($foto_servicio)):
						if (!empty($icono_servicio)):
							$foto_servicio = SERVICIO_ROOT.$icono_servicio;
						else:
							$foto_servicio="";	
						endif;
					endif;	
				endif;
				
				$documento["IDServicio"] = $id_servicio;
				//$documento["Servicio"] = $servicio;
				$documento["Servicio"] = $r["Nombre"];
				$documento["IconoServicio"] = $foto_servicio;
				$documento["IDDocumento"] = $r["IDDocumento"];
				$documento["Titular"] = $r["Nombre"];
				$documento["Subtitular"] = $r["Subtitular"];
				$documento["Fecha"] = $r["Fecha"];
				$documento["Descripcion"] = $r["Descripcion"];
				//ruta temporal = 
				$ruta_temporal=str_replace("https","http",DOCUMENTO_ROOT);
				$ruta_temporal=DOCUMENTO_ROOT;
				if (!empty($r["Archivo1"])):
					$archivo = $ruta_temporal.$r["Archivo1"];
				else:
					$archivo="";	
				endif;
				$documento["Documento"] = $archivo;
				array_push($response, $documento);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registro";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_tipoarchivo( $IDClub, $id_tipo_archivo = "" )
	{
		$dbo =& SIMDB::get();
		
		// Tipo Archivo Especifico
		if (!empty($id_tipo_archivo)):
			$condiciones = " and IDTipoArchivo  = '".$id_tipo_archivo."'";
		endif;	
		
		
		$response = array();
		$sql = "SELECT * FROM TipoArchivo WHERE Publicar = 'S' ".$condiciones." ORDER BY Nombre";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				
				
				$foto = "";								
				$foto_icono = $dbo->getFields( "ClubTipoArchivo" , "Icono" , "IDTipoArchivo = '".$r["IDTipoArchivo"]."' and IDClub = '".$IDClub."'" );
				if (!empty($foto_icono)){
							$foto = 	CLIENTE_ROOT.$foto_icono;
				}
				
				if (!empty($r["Icono"]) && empty($foto)){
							$foto = 	CLIENTE_ROOT.$r["Icono"];
				}
				
				$tipo_archivo["IDTipoArchivo"] = $r["IDTipoArchivo"];
				$tipo_archivo["Nombre"] = $r["Nombre"];
				$tipo_archivo["Icono"] = $foto;
				array_push($response, $tipo_archivo);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registro";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	function get_tipo_pqr( $IDClub )
	{
		$dbo =& SIMDB::get();
		
		
		$response = array();
		$sql = "SELECT * FROM TipoPqr WHERE Publicar = 'S' and IDClub = '".$IDClub."' ORDER BY Nombre";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{	
				$tipo_pqr["IDTipoPqr"] = $r["IDTipoPqr"];
				$tipo_pqr["Nombre"] = $r["Nombre"];				
				array_push($response, $tipo_pqr);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registro";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_galeria( $id_club , $id_seccion = "", $id_socio = "", $tag="" )
	{
		$dbo =& SIMDB::get();
		
		// Secciones Socio
		if (!empty($id_socio)):
			$sql_seccion_socio = $dbo->query("Select * From SocioSeccionGaleria Where IDSocio = '".$id_socio."'");
			while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)):
				$array_secciones_socio[] = $row_seccion["IDSeccionGaleria"];
			endwhile;
			
			if (count($array_secciones_socio)>0):
				$id_secciones = implode(",",$array_secciones_socio);
				$array_condiciones[] = " IDSeccionGaleria in(".$id_secciones.") ";
			endif;
		endif;	
		
		// Seccion Especifica
		if (!empty($id_seccion)):
			$array_condiciones[] = " IDSeccionGaleria  = '".$id_seccion."'";
		endif;	
		
		// Tag
		if (!empty($tag)):
			$array_condiciones[] = " (Nombre  like '%".$tag."%' or Descripcion like '%".$tag."%')";
		endif;	

		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);
			$condiciones_galeria = " and " .$condiciones; 
		endif;	
		
		
		
		$response = array();
		$sql = "SELECT * FROM Galeria WHERE DirigidoA <> 'E' and Publicar = 'S' and IDClub = '".$id_club."' " . $condiciones_galeria;
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				if (!empty($r["Foto"])){
					if(strstr(strtolower($r["Foto"]),"http://"))
						$foto_principal = $r["Foto"];
					else
						$foto_principal = GALERIA_ROOT.$r["Foto"];
						
					//$foto_principal = GALERIA_ROOT.$r["Foto"];
				}
				
				//Fotos
				$response_foto = array();
				$sql_foto = "SELECT * FROM FotoGaleria WHERE IDGaleria = '".$r["IDGaleria"]."' ORDER BY RAND()";
				$qry_foto = $dbo->query( $sql_foto );
				if( $dbo->rows( $qry_foto ) > 0 )
				{	
					while( $r_foto = $dbo->fetchArray( $qry_foto ) )
					{
						$foto_galeria["IDClub"] = $id_club;
						$foto_galeria["IDFoto"] = $r_foto["IDFoto"];
						$foto_galeria["IDGaleria"] = $r_foto["IDGaleria"];
						$foto_galeria["Nombre"] = $r_foto["Nombre"];
						$foto_galeria["Orden"] = $r_foto["Orden"];
						$foto_galeria["Descripcion"] = $r_foto["Descripcion"];
						if (!empty($r_foto["Foto"])):
							if(strstr(strtolower($r_foto["Foto"]),"http://"))
								$foto = $r_foto["Foto"];
							else
								$foto = GALERIA_ROOT.$r_foto["Foto"];
						else:
							$foto="";	
						endif;
						$foto_galeria["Foto"] = $foto;
						array_push($response_foto, $foto_galeria);
		
					}//ednw hile
				}
				
				
				
				$datos_galeria["IDClub"] = $r["IDClub"];
				$datos_galeria["IDSeccionGaleria"] = $r["IDSeccionGaleria"];
				$datos_galeria["IDGaleria"] = $r["IDGaleria"];
				$datos_galeria["Nombre"] = $r["Nombre"];
				$datos_galeria["Descripcion"] = $r["Descripcion"];
				$datos_galeria["Fecha"] = $r["Fecha"];
				$datos_galeria["Foto"] = $foto_principal;
				$datos_galeria["FotoGaleria"] = $response_foto;
				array_push($response, $datos_galeria);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado club";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_galeria_empleados( $id_club , $id_seccion = "", $id_usuario = "", $tag="" )
	{
		$dbo =& SIMDB::get();
		
		// Secciones Socio
		if (!empty($id_usuario)):
			$sql_seccion_empleado = $dbo->query("Select * From UsuarioSeccionGaleria Where IDSocio = '".$id_usuario."'");
			while ($row_seccion = $dbo->fetchArray($sql_seccion_empleado)):
				$array_secciones_empleado[] = $row_seccion["IDSeccionGaleria"];
			endwhile;
			
			if (count($array_secciones_empleado)>0):
				$id_secciones = implode(",",$array_secciones_empleado);
				$array_condiciones[] = " IDSeccionGaleria in(".$id_secciones.") ";
			endif;
		endif;	
		
		// Seccion Especifica
		if (!empty($id_seccion)):
			$array_condiciones[] = " IDSeccionGaleria  = '".$id_seccion."'";
		endif;	
		
		// Tag
		if (!empty($tag)):
			$array_condiciones[] = " (Nombre  like '%".$tag."%' or Descripcion like '%".$tag."%')";
		endif;	

		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);
			$condiciones_galeria = " and " .$condiciones; 
		endif;	
		
		
		
		$response = array();
		$sql = "SELECT * FROM Galeria WHERE DirigidoA = 'E' and Publicar = 'S' and IDClub = '".$id_club."' " . $condiciones_galeria;
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				if (!empty($r["Foto"])){
					if(strstr(strtolower($r["Foto"]),"http://"))
						$foto_principal = $r["Foto"];
					else
						$foto_principal = GALERIA_ROOT.$r["Foto"];
						
					//$foto_principal = GALERIA_ROOT.$r["Foto"];
				}
				
				//Fotos
				$response_foto = array();
				$sql_foto = "SELECT * FROM FotoGaleria WHERE IDGaleria = '".$r["IDGaleria"]."' ORDER BY RAND()";
				$qry_foto = $dbo->query( $sql_foto );
				if( $dbo->rows( $qry_foto ) > 0 )
				{	
					while( $r_foto = $dbo->fetchArray( $qry_foto ) )
					{
						$foto_galeria["IDClub"] = $id_club;
						$foto_galeria["IDFoto"] = $r_foto["IDFoto"];
						$foto_galeria["IDGaleria"] = $r_foto["IDGaleria"];
						$foto_galeria["Nombre"] = $r_foto["Nombre"];
						$foto_galeria["Orden"] = $r_foto["Orden"];
						$foto_galeria["Descripcion"] = $r_foto["Descripcion"];
						if (!empty($r_foto["Foto"])):
							if(strstr(strtolower($r_foto["Foto"]),"http://"))
								$foto = $r_foto["Foto"];
							else
								$foto = GALERIA_ROOT.$r_foto["Foto"];
						else:
							$foto="";	
						endif;
						$foto_galeria["Foto"] = $foto;
						array_push($response_foto, $foto_galeria);
		
					}//ednw hile
				}
				
				
				
				$datos_galeria["IDClub"] = $r["IDClub"];
				$datos_galeria["IDSeccionGaleria"] = $r["IDSeccionGaleria"];
				$datos_galeria["IDGaleria"] = $r["IDGaleria"];
				$datos_galeria["Nombre"] = $r["Nombre"];
				$datos_galeria["Descripcion"] = $r["Descripcion"];
				$datos_galeria["Fecha"] = $r["Fecha"];
				$datos_galeria["Foto"] = $foto_principal;
				$datos_galeria["FotoGaleria"] = $response_foto;
				array_push($response, $datos_galeria);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado club";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
	function get_socios_club( $id_club , $numero_documento = "", $numero_derecho = "", $tag="", $IDSocio="")
	{
		$dbo =& SIMDB::get();
			
		// Secciones Socio
		if (!empty($numero_documento)):
			$array_condiciones[] = " NumeroDocumento  = '".$numero_documento."'";
		endif;	
			
		// Seccion Especifica
		if (!empty($numero_derecho)):
			$array_condiciones[] = " Accion  = '".$numero_derecho."'";
		endif;	
		
		// Tag
		if (!empty($tag)):
			$array_condiciones[] = " (	Nombre  like '%".$tag."%' or Apellido like '%".$tag."%' or Accion like '%".$tag."%' or NumeroDocumento like '%".$tag."%' or Accion like '%".$tag."%' )";
		endif;	
		
		if(!empty($IDSocio) && empty($tag)):
			$sql_fav = "SELECT * FROM SocioFavorito WHERE IDSocio = '".$IDSocio."'";
			$qry_fav = $dbo->query( $sql_fav );
			while( $r_fav = $dbo->fetchArray( $qry_fav ) ){
				$array_favoritos [] = $r_fav["IDSocio2"];
			}
			if(count($array_favoritos)>0):
				$array_condiciones[] = " IDSocio  in  (".implode(",",$array_favoritos) .")";
			else:
				$array_condiciones[] = " IDSocio  in  (0)";
			endif;
		endif;

		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);
			$condiciones_noticia = " and " .$condiciones; 
		endif;	
	
		
		
		$response = array();
		$sql = "SELECT * FROM Socio WHERE IDClub = '".$id_club."' and IDSocio <> '".$IDSocio."'" . $condiciones_noticia ." ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$evento["IDClub"] = $r["IDClub"];
				$evento["IDSocio"] = $r["IDSocio"];
				
				if (!empty($r["Foto"])){
							$foto = 	SOCIO_ROOT.$r["Foto"];
				}
				
				$favorito ="N";
				if(!empty($IDSocio)):
					$socio_favorito = $dbo->getFields( "SocioFavorito" , "IDSocioFavorito" , "IDSocio = '".$IDSocio."' and IDSocio2 = '".$r["IDSocio"]."'" );
					if(!empty($socio_favorito)):
						$favorito="S";
					else:	
						$favorito="N";
					endif;
				endif;
				
				$evento["Foto"] = $foto;
				$evento["Socio"] = utf8_encode($r["Nombre"] . " " .$r["Apellido"]);
				$evento["Favorito"] = $favorito;
				$evento["NumeroDerecho"] = $r["Accion"];
				array_push($response, $evento);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "Por favor utilice el buscador , no se encontraron registros";
				$respuesta["success"] = true;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
function get_invitado_general( $id_club , $numero_documento = "", $tag="")
	{
		$dbo =& SIMDB::get();
			
		// Doc
		if (!empty($numero_documento)):
			$array_condiciones[] = " NumeroDocumento  = '".$numero_documento."'";
		endif;	
		
		// Tag
		if (!empty($tag)):
			$array_condiciones[] = " (	Nombre  like '%".$tag."%' or Apellido like '%".$tag."%' or Email like '%".$tag."%' or NumeroDocumento like '%".$tag."%' )";
		endif;	
		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);
			$condiciones_noticia = " and " .$condiciones; 
		endif;	
	
		
		
		$response = array();
		$sql = "SELECT * FROM Invitado WHERE 1 " . $condiciones_noticia ." ORDER BY Nombre ";
		
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$datos_invitado["IDInvitado"] = $r["IDInvitado"];
				$datos_invitado["IDTipoDocumento"] = $r["IDTipoDocumento"];
				$datos_invitado["NumeroDocumento"] = $r["NumeroDocumento"];
				$datos_invitado["Nombre"] = utf8_encode($r["Nombre"]);
				$datos_invitado["Apellido"] = utf8_encode($r["Apellido"]);
				$datos_invitado["Direccion"] = $r["Direccion"];
				$datos_invitado["Telefono"] =$r["Telefono"];
				$datos_invitado["Email"] = $r["Email"];
				$datos_invitado["FechaNacimiento"] = $r["FechaNacimiento"];
				
				array_push($response, $datos_invitado);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "Por favor utilice el buscador , no se encontraron registros";
				$respuesta["success"] = true;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function
	
	
function get_invitado_dia_socio( $IDClub, $NumeroDocumento="", $Nombre="", $Fecha, $IDSocio)
	{
		$dbo =& SIMDB::get();
			
		// Secciones Socio
		if (!empty($NumeroDocumento)):
			$array_condiciones[] = " NumeroDocumento  = '".$numero_documento."'";
		endif;	
			
		// Seccion Especifica
		if (!empty($Nombre)):
			$array_condiciones[] = " Nombre  = '%".$Nombre."%'";
		endif;	
		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);			
		endif;	

		
		if (count($array_condiciones)>0):
			$condiciones = implode (" and " , $array_condiciones);			
		endif;	
	
		
		
		$response = array();
		$sql = "SELECT * FROM SocioInvitado WHERE IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and FechaIngreso = '".$Fecha."'" . $condiciones ." ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$datos_invitado["IDClub"] = $r["IDClub"];
				$datos_invitado["IDSocio"] = $r["IDSocio"];
				$datos_invitado["NumeroDocumento"] = $r["NumeroDocumento"];
				$datos_invitado["Nombre"] = utf8_encode($r["Nombre"]);
				$datos_invitado["FechaIngreso"] = $r["FechaIngreso"];
				array_push($response, $datos_invitado);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "Por favor utilice el buscador , no se encontraron registros";
				$respuesta["success"] = true;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
			
	}// fin function	
	

function get_servicios($id_club,$TipoApp="",$IDUsuario=""){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		//Consulto los servicios activos del club
		$sql_servicio_club = "Select * From  ServicioClub Where IDClub = '".$id_club."' and Activo = 'S'";
		$qry_servicio_club = $dbo->query( $sql_servicio_club );
		$message = $dbo->rows( $qry ) . " Encontrados";
		while( $r_servicio_club = $dbo->fetchArray( $qry_servicio_club ) )
		{
			$array_id_servicios []= 	$r_servicio_club["IDServicioMaestro"];	
		}
		
		if (count($array_id_servicios)>0):
			$id_servicios = implode(",",$array_id_servicios);
		endif;
		
		
		//traer servicios del usuario
		if($TipoApp=="Empleado" && !empty($IDUsuario)):
			unset($array_id_servicios);
			$id_servicios = "0";
			$response_servicio = array();
			$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
			$qry_servicios = $dbo->query( $sql_servicios );
			while( $r_servicio = $dbo->fetchArray( $qry_servicios  ) )
			{		
					$array_id_servicios []= 	$r_servicio["IDServicioMaestro"];				
				
			}//end while
			if (count($array_id_servicios)>0):
				$id_servicios = implode(",",$array_id_servicios);
			endif;
		endif;	
	
		
		
		$sql = "SELECT * FROM Servicio WHERE IDClub = '".$id_club."' and IDServicioMaestro in (".$id_servicios.") ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$servicio["IDClub"] = $r["IDClub"];
				$servicio["IDServicio"] = $r["IDServicio"];
				$servicio["Nombre"] = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" ); ;
				
				$foto = "";
				if (!empty($r["Icono"])){
							$foto = 	SERVICIO_ROOT.$r["Icono"];
				}
				else{
					$icono_maestro = $dbo->getFields( "ServicioMaestro" , "Icono" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" );
					if (!empty($icono_maestro)){
						$foto = 	SERVICIO_ROOT.$icono_maestro;
					}
				}
				
				$invitadoclub = ""; 
				$invitadoexterno = "";
				$servicio["Icono"] = $foto;
				$servicio["General"] = $dbo->getFields( "ServicioMaestro" , "General" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" ); 
				
				
				//TEMPORAL....Consulto alguna disponibilidad para ver el numero de invitados, esto se debe hacer por el servicio que consulta disponibilidades				
				$id_disponibilidad = $dbo->getFields( "ServicioDisponibilidad" , "IDDisponibilidad" , "IDServicio = '".$r["IDServicio"]."'" ); 
				
				//Consulto cual es la mayor disponibildad en tiempo apara armar el dia maximo en el servicio cuando se empieza por el elemento
				$sql_disponibilidad = "Select * From Disponibilidad Where IDServicio = '".$r["IDServicio"]."'";
				$result_disponibilidad = $dbo->query($sql_disponibilidad);
				$dia_mayor=0;
				while($row_disponibilidad = $dbo->fetchArray($result_disponibilidad)){
					$medicion_tiempo_anticipacion = $row_disponibilidad["MedicionTiempoAnticipacion"];
					$valor_anticipacion_turno = (int)$row_disponibilidad["Anticipacion"];
					switch($medicion_tiempo_anticipacion):
						case "Dias":	
							$dias_anticipacion_turno = $valor_anticipacion_turno;
						break;
						case "Horas":
							$dias_anticipacion_turno = (int)($valor_anticipacion_turno/24);
						break;
						case "Minutos":
							$dias_anticipacion_turno = (int)($valor_anticipacion_turno/1440);						
						break;
						default:
							$dias_anticipacion_turno = 0;
					endswitch;
					
					if((int)$dias_anticipacion_turno>=(int)$dia_mayor):
						$dia_mayor=$dias_anticipacion_turno;
					endif;
					
					
				}
				
				$invitadoclub = $dbo->getFields( "ServicioDisponibilidad" , "IDDisponibilidad" , "IDServicio = '".$r["IDServicio"]."'" ); 
				$invitadoexterno = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoExterno" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
				
				if (!empty($invitadoclub)):
					$servicio["NumeroInvitadoClub"] = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoClub" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
				else:
					$servicio["NumeroInvitadoClub"] = ""; 			
				endif;
				if (!empty($invitadoexterno)):
					$servicio["NumeroInvitadoExterno"] = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoExterno" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
				else:
					$servicio["NumeroInvitadoExterno"] = "";
				endif;		
				
				/*
				//Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
				$medicion_tiempo_anticipacion = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacion" , "IDDisponibilidad = '" . $id_disponibilidad  . "'" );
				$valor_anticipacion_turno = (int)$dbo->getFields( "Disponibilidad" , "Anticipacion" , "IDDisponibilidad = '" . $id_disponibilidad  . "'" );
				switch($medicion_tiempo_anticipacion):
					case "Dias":	
						$dias_anticipacion_turno = $valor_anticipacion_turno;
					break;
					case "Horas":
						$dias_anticipacion_turno = (int)($valor_anticipacion_turno/24);
					break;
					case "Minutos":
						$dias_anticipacion_turno = (int)($valor_anticipacion_turno/1440);						
					break;
					default:
						$dias_anticipacion_turno = 0;
				endswitch;
				$servicio["DiasMaximoReserva"] = "$dias_anticipacion_turno";	
				*/
				$servicio["DiasMaximoReserva"] = "$dia_mayor";	
				
				
				//temporal lagartos
				
				if($id_club==7 && $r["IDServicio"]==43){//Clases tenis especial
					$dias_maximo_especial = 9 - (int)date("w");
					$servicio["DiasMaximoReserva"] = "$dias_maximo_especial";	
				}
				
				//FIN TEMPORAL
				
				$servicio["LabelElemento"] = $dbo->getFields( "ServicioMaestro" , "LabelElemento" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" );
				$servicio["LabelAuxiliar"] = $dbo->getFields( "ServicioMaestro" , "LabelAuxiliar" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" );
				$servicio["LabelTipoReserva"] = $dbo->getFields( "ServicioMaestro" , "LabelTipoReserva" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" );
				$servicio["TextoLegal"] = $r["TextoLegal"];
				$servicio["DiasAnticipacion"] = "$r[DiasAnticipacion]";
				$servicio["HorarioAcordeon"] = $dbo->getFields( "ServicioMaestro" , "HorarioAcordeon" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" );
				
				$servicio["PermiteBeneficiario"] = $dbo->getFields( "ServicioMaestro" , "PermiteBeneficiario" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" );
				$servicio["LabelBeneficiario"] = $dbo->getFields( "ServicioMaestro" , "LabelBeneficiario" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" );
		
				
				$servicio["HoraDesde"] = $r["HoraHasta"];
				$servicio["HoraCancelacion"] = $r["HoraCancelacion"];
				$servicio["IntervaloHora"] = $r["IntervaloHora"];
				$servicio["MinutosReserva"] = "$r[MinutosReserva]";
				$servicio["TurnosMaximo"] = "$r[TurnosMaximo]";
				$id_servicio_inicial = $dbo->getFields( "ServicioMaestro" , "IDServicioInicial" , "IDServicioMaestro = '".$r["IDServicioMaestro"]."'" );
				$servicio["PrimerServicio"] = $dbo->getFields( "ServicioInicial" , "Nombre" , "IDServicioInicial = '".$id_servicio_inicial."'" );
				$servicio["Orden"] = $dbo->getFields( "ServicioClub" , "Orden" , "IDClub = '" . $id_club . "' and IDServicioMaestro = '".$r["IDServicioMaestro"]."'" ) ;
				$servicio["Georeferenciacion"] = "$r[Georeferenciacion]";
				$servicio["Latitud"] = $r["Latitud"];
				$servicio["Longitud"] = $r["Longitud"];
				$servicio["Rango"] = $r["Rango"];
				$servicio["MensajeFueraRango"] = $r["MensajeFueraRango"];
				
				$zonahoraria = date_default_timezone_get();
				$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );	
				$servicio["GMT"] = SIMWebservice::timezone_offset_string( $offset );
				
				//Campos Reservas
				$response_campos = array();
				$sql_campos = "SELECT * FROM ServicioCampo WHERE Publicar = 'S' and IDServicio= '".$r["IDServicio"]."' ORDER BY Nombre";
				$qry_campos = $dbo->query( $sql_campos );
				if( $dbo->rows( $qry_campos ) > 0 )
				{	
					while( $r_campos = $dbo->fetchArray( $qry_campos ) )
					{
						$campos["IDClub"] = $id_club;
						$campos["IDServicio"] = $r_campos["IDServicio"];
						$campos["IDServicioCampo"] = $r_campos["IDServicioCampo"];
						$campos["Nombre"] = $r_campos["Nombre"];
						$campos["Descripcion"] = $r_campos["Descripcion"];
						$campos["Tipo"] = $r_campos["Tipo"];
						$campos["Valor"] = $r_campos["Valor"];
						
						
						array_push($response_campos, $campos);
		
					}//end while
				}				
				$servicio["CamposReserva"] = $response_campos;
				
				
				//Tipos de Reservas
				$response_tiporeservas = array();
				$sql_tiporeservas = "SELECT * FROM ServicioTipoReserva WHERE Activo = 'S' and IDServicio= '".$r["IDServicio"]."' ORDER BY Nombre";
				$qry_tiporeservas = $dbo->query( $sql_tiporeservas );
				if( $dbo->rows( $qry_tiporeservas ) > 0 )
				{	
					while( $r_tiporeservas = $dbo->fetchArray( $qry_tiporeservas ) )
					{
						$tiporeserva["IDClub"] = $id_club;
						$tiporeserva["IDServicio"] = $r_tiporeservas["IDServicio"];
						$tiporeserva["IDServicioTipoReserva"] = $r_tiporeservas["IDServicioTipoReserva"];
						$tiporeserva["Nombre"] = $r_tiporeservas["Nombre"];
						array_push($response_tiporeservas, $tiporeserva);
		
					}//end while
				}
				
				$servicio["TipoReserva"] = $response_tiporeservas;
				
				
				
				array_push($response, $servicio);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
}

	
	
function get_elementos($IDClub,$IDSocio,$IDServicio,$IDUsuario=""){
	$dbo =& SIMDB::get();
	
		if(!empty($IDUsuario)):
			$sql_elemento_usuario = "Select * From UsuarioServicioElemento Where IDUsuario = '".$IDUsuario."'";
			$result_elemento_usuario = $dbo->query($sql_elemento_usuario);
			while($row_elemento_usuario = $dbo->fetchArray($result_elemento_usuario)):
				$array_id_elemento [] = $row_elemento_usuario["IDServicioElemento"];
			endwhile;
			if(count($array_id_elemento)>0):
				$condicion_elemento = " and IDServicioElemento in (" . implode(",",$array_id_elemento) . ") ";
			endif;
		endif;
			
		$response = array();
		$sql = "SELECT SE.* FROM ServicioElemento SE, Servicio S WHERE SE.IDServicio = S.IDServicio and SE.Publicar = 'S' and S.IDClub = '".$IDClub."' and SE.IDServicio = '".$IDServicio."' ".$condicion_elemento." ORDER BY SE.Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$elemento["IDElemento"] = $r["IDServicioElemento"];
				$elemento["IDClub"] = $IDClub;
				$elemento["IDServicio"] = $r["IDServicio"];
				$elemento["IDPadre"] = $r["IDPadre"];
				$elemento["Nombre"] = $r["Nombre"];
				
				array_push($response, $elemento);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
}	


function get_invitado($IDClub,$IDSocio,$NumeroDocumento, $FechaIngreso){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		if( !empty( $IDSocio ) ){
			
				// Numero Doc
				if (!empty($NumeroDocumento)):
					$array_condiciones[] = " NumeroDocumento  like '%".$NumeroDocumento."%'";
				endif;	
				
				if (!empty($FechaIngreso)):
					$array_condiciones[] = " FechaIngreso  = '".$FechaIngreso."'";
				endif;	
				
				
				
				if (count($array_condiciones)>0):
					$condiciones = "and " . implode (" and " , $array_condiciones);					
				endif;	


			
			
				$sql = "SELECT * FROM SocioInvitado WHERE IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' ".$condiciones." and Estado = 'P' and FechaIngreso >= CURDATE()  ORDER BY FechaIngreso Desc ";
				$qry = $dbo->query( $sql );
				if( $dbo->rows( $qry ) > 0 )
				{
					$message = $dbo->rows( $qry ) . " Encontrados";
					while( $r = $dbo->fetchArray( $qry ) )
					{						
						$elemento["IDClub"] = $IDClub;
						$elemento["IDInvitacion"] = $r["IDSocioInvitado"];
						$elemento["Nombre"] = $r["Nombre"];
						$elemento["NumeroDocumento"] = $r["NumeroDocumento"];
						$elemento["FechaIngreso"] = $r["FechaIngreso"];
						
						array_push($response, $elemento);
		
					}//ednw hile
						$respuesta["message"] = $message;
						$respuesta["success"] = true;
						$respuesta["response"] = $response;	
				}//End if
				else
				{
						$respuesta["message"] = "No se encontraron registros";
						$respuesta["success"] = true;
						$respuesta["response"] = NULL;	
				}//end else		
		}
		else{
			$respuesta["message"] = "2. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
			
		return $respuesta;	
	
}	


function get_mis_invitado($IDClub,$IDSocio,$NumeroDocumento){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		if( !empty( $IDSocio ) ){
			
				// Numero Doc
				if (!empty($NumeroDocumento)):
					$array_condiciones[] = " NumeroDocumento  like '%".$NumeroDocumento."%'";
				endif;	
				
				if (count($array_condiciones)>0):
					$condiciones = "and " . implode (" and " , $array_condiciones);					
				endif;	


			
			
				$sql = "SELECT * FROM SocioInvitado WHERE IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' ".$condiciones." Group by IDSocio, NumeroDocumento ORDER BY FechaIngreso Desc ";
				$qry = $dbo->query( $sql );
				if( $dbo->rows( $qry ) > 0 )
				{
					$message = $dbo->rows( $qry ) . " Encontrados";
					while( $r = $dbo->fetchArray( $qry ) )
					{						
						$elemento["IDClub"] = $IDClub;
						$elemento["Nombre"] = $r["Nombre"];
						$elemento["NumeroDocumento"] = $r["NumeroDocumento"];
						
						array_push($response, $elemento);
		
					}//ednw hile
						$respuesta["message"] = $message;
						$respuesta["success"] = true;
						$respuesta["response"] = $response;	
				}//End if
				else
				{
						$respuesta["message"] = "No se encontraron registros";
						$respuesta["success"] = true;
						$respuesta["response"] = NULL;	
				}//end else		
		}
		else{
			$respuesta["message"] = "3. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
			
		return $respuesta;	
	
}	


function get_mis_autorizaciones_invitados($IDClub,$IDSocio,$Tag, $FechaIngreso, $Tiempo=""){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		if( !empty( $IDSocio ) ){
			
				// Tag
				if (!empty($Tag)):
					// Consulto lo invitados con este dato
					$sql_busca_invitado = "Select * From Invitado Where NumeroDocumento like '%".$Tag."%' or Nombre like '%".$Tag."%' or Apellido like '%".$Tag."%'  or Telefono like '%".$Tag."%' or Email like '%".$Tag."%' ";
					$result_busca_invitado = $dbo->query($sql_busca_invitado);
					while($row_busca_invitado = $dbo->fetchArray($result_busca_invitado)):
						$array_condiciones_invitado[] = " IDInvitado  = '".$row_busca_invitado["IDInvitado"]."'";
					endwhile;
				endif;	
				
				if(count($array_condiciones_invitado)>0):
					$condiciones_invitado = "and (" . implode (" or " , $array_condiciones_invitado).")";	
				endif;
				
				if (!empty($FechaIngreso)):
					$array_condiciones[] = " FechaInicio  = '".$FechaIngreso."'";
				endif;	
				
				if (count($array_condiciones)>0):
					$condiciones = "and " . implode (" and " , $array_condiciones);					
				endif;	
				
				if($Tiempo=="Futuro" || $Tiempo ==""):
					$condicion_tiempo = " and FechaInicio >= CURDATE() ";
				elseif($Tiempo=="Pasado"):
					$condicion_tiempo = " and (FechaInicio <= CURDATE() or FechaInicio >= CURDATE() ) ";
				endif;
			
			
				$sql = "SELECT * FROM SocioInvitadoEspecial WHERE IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' ".$condiciones." " . " " .$condiciones_invitado. " " .$condicion_tiempo ."  ORDER BY IDSocioInvitadoEspecial,FechaInicio  Desc ";
				$qry = $dbo->query( $sql );
				if( $dbo->rows( $qry ) > 0 )
				{
					$message = $dbo->rows( $qry ) . " Encontrados";
					while( $r = $dbo->fetchArray( $qry ) )
					{		
						if(!in_array($r["IDInvitado"],$array_socio_listado)):
					
								$elemento["IDClub"] = $IDClub;
								$elemento["IDInvitacion"] = $r["IDSocioInvitadoEspecial"];
								//Consulto datos invitado
								$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $r["IDInvitado"] . "' ", "array" );
								$elemento["Nombre"] = $datos_invitado["Nombre"];	
								$elemento["Apellido"] = $datos_invitado["Apellido"];						
								$elemento["NumeroDocumento"] = "$datos_invitado[NumeroDocumento]";
								$elemento["Email"] = $datos_invitado["Email"];
								$elemento["TipoInvitado"] = $r["TipoInvitacion"];
								
								unset($response_tipodoc);
								$response_tipodoc = array();
								$tipodoc["IDTipoDocumento"] = (int)$datos_invitado["IDTipoDocumento"];
								$tipodoc["Nombre"] = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'" );
								array_push($response_tipodoc, $tipodoc);
								$elemento["TipoDocumento"] = $response_tipodoc ;								
								$elemento["FechaIngreso"] = $r["FechaInicio"];
								$elemento["FechaSalida"] = $r["FechaFin"];
								//Consulto datos vehiculo
								$datos_vehiculo = $dbo->fetchAll( "VehiculoInvitacion", " IDSocioInvitadoEspecial = '" . $r["IDSocioInvitadoEspecial"] . "' ", "array" );
								$elemento["Vehiculo"] = $datos_vehiculo["Placa"];	
								$elemento["CabezaInvitacion"] = $r["CabezaInvitacion"];						
								$array_socio_listado[]=$r["IDInvitado"];
								
								// Si tiene grupo familiar devuelvo el grupo y lo marco para no mostrarlo de nuevo
								if((int)$r["IDPadre"]>0):
									$condicion_padre = " or IDPadre = '".$r["IDPadre"]."'";
								endif;
								$response_invitado_familia = array();
								$sql_grupo_familiar = "Select * From SocioInvitadoEspecial Where IDPadre = '".$r["IDInvitado"]."' or IDInvitado = '".$r["IDPadre"]."' " . $condicion_padre;
								$result_grupo_familiar = $dbo->query($sql_grupo_familiar);
								while($row_grupo_familiar = $dbo->fetchArray($result_grupo_familiar)):							
									if(!in_array($row_grupo_familiar["IDInvitado"],$array_socio_listado)):
										$dato_invitado_asociado["IDClub"] = $IDClub;
										$dato_invitado_asociado["IDInvitacion"] = $row_grupo_familiar["IDSocioInvitadoEspecial"];
										//Consulto datos invitado
										$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $row_grupo_familiar["IDInvitado"] . "' ", "array" );
										$dato_invitado_asociado["Nombre"] = $datos_invitado["Nombre"];
										$dato_invitado_asociado["Apellido"] = $datos_invitado["Apellido"];						
										$dato_invitado_asociado["NumeroDocumento"] = "$datos_invitado[NumeroDocumento]";
										$dato_invitado_asociado["Email"] = $datos_invitado["Email"];
										$dato_invitado_asociado["TipoInvitado"] = $row_grupo_familiar["TipoInvitacion"];
										
										unset($response_tipodoc_asociado);
										$response_tipodoc_asociado = array();
										$tipodoc_asociado["IDTipoDocumento"] = (int)$datos_invitado["IDTipoDocumento"];
										$tipodoc_asociado["Nombre"] = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'" );
										array_push($response_tipodoc_asociado, $tipodoc_asociado);
										$dato_invitado_asociado["TipoDocumento"] = $response_tipodoc_asociado;	
										
										$dato_invitado_asociado["FechaIngreso"] = $row_grupo_familiar["FechaInicio"];
										$dato_invitado_asociado["FechaSalida"] = $row_grupo_familiar["FechaFin"];
										//Consulto datos vehiculo
										$datos_vehiculo = $dbo->fetchAll( "VehiculoInvitacion", " IDSocioInvitadoEspecial = '" . $row_grupo_familiar["IDSocioInvitadoEspecial"] . "' ", "array" );
										$dato_invitado_asociado["Vehiculo"] = $datos_vehiculo["Placa"];	
										$dato_invitado_asociado["CabezaInvitacion"] = $row_grupo_familiar["CabezaInvitacion"];	
										array_push($response_invitado_familia, $dato_invitado_asociado);
										$array_socio_listado[]=$row_grupo_familiar["IDInvitado"];
									endif;	
								endwhile;
								
								
								$elemento["GrupoFamiliar"] = $response_invitado_familia;
								array_push($response, $elemento);
						endif;		
								
		
					}//end while
						$respuesta["message"] = $message;
						$respuesta["success"] = true;
						$respuesta["response"] = $response;	
				}//End if
				else
				{
						$respuesta["message"] = "No se encontraron registros";
						$respuesta["success"] = true;
						$respuesta["response"] = NULL;	
				}//end else		
		}
		else{
			$respuesta["message"] = "2. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
			
		return $respuesta;	
	
}


function get_mis_autorizaciones_contratista($IDClub,$IDSocio,$Tag, $FechaIngreso, $Tiempo = ""){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		if( !empty( $IDSocio ) ){
			
				if (!empty($Tag)):
					// Consulto lo invitados con este dato
					$sql_busca_invitado = "Select * From Invitado Where NumeroDocumento like '%".$Tag."%' or Nombre like '%".$Tag."%' or Apellido like '%".$Tag."%'  or Telefono like '%".$Tag."%' or Email like '%".$Tag."%' ";
					$result_busca_invitado = $dbo->query($sql_busca_invitado);
					while($row_busca_invitado = $dbo->fetchArray($result_busca_invitado)):
						$array_condiciones_invitado[] = " IDInvitado  = '".$row_busca_invitado["IDInvitado"]."'";
					endwhile;
				endif;	
				
				if(count($array_condiciones_invitado)>0):
					$condiciones_invitado = "and (" . implode (" or " , $array_condiciones_invitado).")";	
				endif;
				
				
				
				if (count($array_condiciones)>0):
					$condiciones = "and " . implode (" and " , $array_condiciones);					
				endif;	
				
				
				if($Tiempo=="Futuro" || $Tiempo ==""):
					$condicion_tiempo = " and FechaInicio >= CURDATE() ";
				elseif($Tiempo=="Pasado"):
					$condicion_tiempo = " and (FechaInicio <= CURDATE() or FechaInicio >= CURDATE() ) ";
				endif;
			
			
				 $sql = "SELECT * FROM SocioAutorizacion WHERE IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' ".$condiciones. " " . $condiciones_invitado  . " " . $condicion_tiempo ."  ORDER BY FechaInicio Desc ";
				$qry = $dbo->query( $sql );
				if( $dbo->rows( $qry ) > 0 )
				{
					$message = $dbo->rows( $qry ) . " Encontrados";
					while( $r = $dbo->fetchArray( $qry ) )
					{						
						$elemento["IDClub"] = $IDClub;
						$elemento["IDAutorizacion"] = $r["IDSocioAutorizacion"];
						//Consulto datos invitado
						$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $r["IDInvitado"] . "' ", "array" );
						$elemento["Nombre"] = $datos_invitado["Nombre"];
						$elemento["Apellido"] = $datos_invitado["Apellido"];						
						$elemento["NumeroDocumento"] = "$datos_invitado[NumeroDocumento]";
						$elemento["TipoAutorizacion"] = $r["TipoAutorizacion"];
						$elemento["Email"] = $datos_invitado["Email"];
						unset($response_tipodoc_asociado);
						$response_tipodoc_asociado = array();
						$tipodoc_asociado["IDTipoDocumento"] = (int)$datos_invitado["IDTipoDocumento"];
						$tipodoc_asociado["Nombre"] = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'" );
						array_push($response_tipodoc_asociado, $tipodoc_asociado);
						$elemento["TipoDocumento"] = $response_tipodoc_asociado;
						
						$elemento["FechaIngreso"] = $r["FechaInicio"];
						$elemento["FechaSalida"] = $r["FechaFin"];
						//Consulto datos vehiculo
						$datos_vehiculo = $dbo->fetchAll( "Vehiculo", " IDVehiculo = '" . $r["IDVehiculo"] . "' ", "array" );
						$elemento["Vehiculo"] = $datos_vehiculo["Placa"];
						
						array_push($response, $elemento);
		
					}//ednw hile
						$respuesta["message"] = $message;
						$respuesta["success"] = true;
						$respuesta["response"] = $response;	
				}//End if
				else
				{
						$respuesta["message"] = "No se encontraron registros";
						$respuesta["success"] = true;
						$respuesta["response"] = NULL;	
				}//end else		
		}
		else{
			$respuesta["message"] = "2. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
			
		return $respuesta;	
	
}





function get_horas($IDClub,$IDSocio,$IDServicio){
	$dbo =& SIMDB::get();
	
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('get_horas','IDClub: ".$IDClub. " IDSocio:" . $IDSocio . " . IDServicio: ".$IDServicio." Elemento: ".$IDElemento."','".json_encode($respuesta)."')");
	
	
		$response = array();
		$sql = "SELECT * FROM Servicio WHERE IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{	
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{	
			
					// Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
					$sql_dispo_elemento_primera = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' Order by HoraDesde Limit 1";
					$qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
					$row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
				
				
				$minutoAnadir=$dbo->getFields( "Disponibilidad" , "Intervalo" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );	
			
				$horaInicial=$row_dispo_elemento_primera["HoraDesde"];
				$minutoAnadir=$dbo->getFields( "Disponibilidad" , "Intervalo" , "IDDisponibilidad = '" . $row_dispo_elemento_primera["IDDisponibilidad"] . "'" );	
				$hora_final = strtotime( $row_dispo_elemento_primera["HoraHasta"] );
				$hora_actual = $row_dispo_elemento_primera["HoraDesde"];
				
				
				
				while($hora_actual<=$hora_final):
					
					$servicio_hora["IDClub"] = $r["IDClub"];
					$servicio_hora["IDServicio"] = $r["IDServicio"];
					
					if(strlen($horaInicial)!=8):
						$horaInicial .= ":00";
					endif;
					
					$servicio_hora["Hora"] = $horaInicial;
					$zonahoraria = date_default_timezone_get();
					$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );																						
					$servicio_hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
					
					
					array_push($response, $servicio_hora);
					
					$segundos_horaInicial=strtotime($horaInicial);
					$segundos_minutoAnadir=$minutoAnadir*60;
					$array_horas=$nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
					$hora_actual = strtotime( $nuevaHora );
					$horaInicial=$nuevaHora;
					
				
				endwhile;
				
				
				
				

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
}


//Verifica si club y el servicio está abierto en la fecha indicada
function verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$IDServicioElemento="",$Hora=""){
	$dbo =& SIMDB::get();
	$respuesta="";
	$sql = "SELECT * FROM  ClubFechaCierre WHERE Fecha = '".$Fecha."' and IDClub = '".$IDClub."'";
	$qry = $dbo->query( $sql );
	if( $dbo->rows( $qry ) > 0 )
	{
		$r_cierre = $dbo->fetchArray($qry);	
		$respuesta = "Lo sentimos club cerrado el dia: " . $Fecha ." Motivo: ". $r_cierre["Motivo"];
	}
	else{
		
		
		if(!empty($IDServicioElemento)){		
			$condicion_elemento = " and IDServicioElemento like '%".$IDServicioElemento."|%'";
		}
		else{
			$condicion_elemento = " and IDServicioElemento = ''";
		}
		
		if(!empty($Hora)):
			$condicion_hora = " and HoraInicio <= '".$Hora."' and HoraFin >= '".$Hora."' ";
		else:
			$condicion_hora = " and HoraInicio = '00:00:00' and HoraFin = '00:00:00' ";
		endif;
		
		
		
			$sql_servicio = "SELECT * FROM  ServicioCierre WHERE FechaInicio <= '".$Fecha."' and FechaFin >= '".$Fecha."' and IDServicio = '".$IDServicio."' " . $condicion_hora . $condicion_elemento;
			
			if($Hora=="13:00:00"):
				//echo $sql_servicio;
			endif;
			
			
			$qry_servicio = $dbo->query( $sql_servicio );
			if( $dbo->rows( $qry_servicio ) > 0 )
			{
				$r_cierre_servicio = $dbo->fetchArray($qry_servicio);	
				$respuesta = "Lo sentimos servicio cerrado el dia: " . $Fecha ." Motivo: ". $r_cierre_servicio["Descripcion"];			
			}
		
	}
	
	return $respuesta;
}



function consultar_disponibilidad($qry,$IDElemento,$IDServicio,$Fecha){
	
	$dbo =& SIMDB::get();
	//Horas Disponibles Elemento
	$response_disponibilidad = array();
	
	if (!empty($IDElemento))
		$condicion_elemento = " and IDServicioElemento = '".$IDElemento."'";
	
	while( $r = $dbo->fetchArray( $qry ) )
	{	
	
			$sql_elementos_servicio = "Select * From ServicioElemento Where IDServicio = '".$IDServicio."' " . $condicion_elemento ." Order by Orden";
			$result_elementos_servicio = $dbo->query($sql_elementos_servicio);
			while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)):
				unset($array_hora_reservada);
				$IDElemento = $r_elementos_servicio["IDServicioElemento"];
			
			
				//Consulto lo que  tiene reservado el elemento en la fecha indicada
				$sql_reserva_elemento = "SELECT ReservaGeneral.*, CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio FROM ReservaGeneral, Socio WHERE IDServicioElemento = '".$IDElemento."' and Fecha = '".$Fecha."' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";
				$qry_reserva_elemento = $dbo->query( $sql_reserva_elemento );
				$array_socio = array();
				while($row_reserva_elemento = $dbo->fetchArray($qry_reserva_elemento)){
					$array_hora_reservada[] = $row_reserva_elemento["Hora"];
					$array_socio[ $row_reserva_elemento["Hora"] ] = $row_reserva_elemento;
					$array_socio[ $row_reserva_elemento["Hora"] ]["NombreSocio"] = utf8_encode( $row_reserva_elemento["Socio"] );
				}			
			
			
				//Horas generales del servicio
				$horaInicial=$r["HoraDesde"];
				$minutoAnadir=$r["IntervaloHora"];
				$hora_final = strtotime( $r["HoraHasta"] );
				$hora_actual = $r["HoraDesde"];
				
				$dia_fecha= date('N', strtotime($Fecha));
			
				//Verifico si tene disponibilidad especifica el elemento				
				$sql_dispo_elemento = "Select * From ElementoDisponibilidad Where IDServicioElemento = '".$IDElemento."'";
				$qry_dispo_elemento = $dbo->query($sql_dispo_elemento);
				if( $dbo->rows( $qry_dispo_elemento ) > 0 ){								
					$verifica_disponibilidad_especifica = 1;					
					$sql_dispo_elemento = "Select * From ElementoDisponibilidad Where IDServicioElemento = '".$IDElemento."' and IDDia = '".$dia_fecha."' Order by HoraDesde";
					$qry_dispo_elemento = $dbo->query($sql_dispo_elemento);
					while($row_dispo_elemento = $dbo->fetchArray($qry_dispo_elemento)){
						$horaInicial=$row_dispo_elemento["HoraDesde"];
						$minutoAnadir=$r["IntervaloHora"];
						$hora_final = strtotime( $row_dispo_elemento["HoraHasta"] );
						$hora_actual = strtotime( $row_dispo_elemento["HoraDesde"] );
						
						while($hora_actual<=$hora_final):
							if(strlen($horaInicial)!=8):
								$horaInicial .= ":00";
							endif;	
							
						
							
							$hora["Hora"] = $horaInicial;
							$zonahoraria = date_default_timezone_get();
							$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );											
							$hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
							if (in_array($horaInicial,$array_hora_reservada))
							{
								$hora["Disponible"] = "N";
								$hora["Socio"] = $array_socio["$horaInicial"]["NombreSocio"];
							}
							else
							{
								$hora["Disponible"] = "S";	
								$hora["Socio"] = "";
							}
							
							$hora["IDElemento"] = $IDElemento;		
							$hora["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $IDElemento . "'" );	
							array_push($response_disponibilidad, $hora);
							
												
							$array_horas_elemento[] = $horaInicial;								
							$segundos_horaInicial=strtotime($horaInicial);
							$segundos_minutoAnadir=$minutoAnadir*60;
							$array_horas=$nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
							$hora_actual = strtotime( $nuevaHora );
							$horaInicial=$nuevaHora;
						endwhile;				
								
					}
				}
				// Si no tiene disponibildad especifica busco la general para elementos
				else{
					//Verifico si tene disponibilidad  general el elemento				
					$sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%'";
					$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
					if( $dbo->rows( $qry_dispo_elemento_gral ) > 0 ){	
					
							$verifica_disponibilidad_general = 1;	
							while($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)){								
								$horaInicial=$row_dispo_elemento_gral["HoraDesde"];
								$minutoAnadir=$r["IntervaloHora"];
								$hora_final = strtotime( $row_dispo_elemento_gral["HoraHasta"] );
								$hora_actual = strtotime( $row_dispo_elemento_gral["HoraDesde"] );
								
								while($hora_actual<=$hora_final):
									if(strlen($horaInicial)!=8):
										$horaInicial .= ":00";
									endif;	
									
									$hora["Hora"] = $horaInicial;
									$zonahoraria = date_default_timezone_get();
									$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );											
									$hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
									if (in_array($horaInicial,$array_hora_reservada))
									{
										$hora["Disponible"] = "N";
										$hora["Socio"] = $array_socio["$horaInicial"]["NombreSocio"];
									}
									else
									{
										$hora["Disponible"] = "S";
										$hora["Socio"] = "";
									}
									
									$hora["IDElemento"] = $IDElemento;		
									$hora["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $IDElemento . "'" );
									array_push($response_disponibilidad, $hora);
									
														
									$array_horas_elemento[] = $horaInicial;								
									$segundos_horaInicial=strtotime($horaInicial);
									$segundos_minutoAnadir=$minutoAnadir*60;
									$array_horas=$nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
									$hora_actual = strtotime( $nuevaHora );
									$horaInicial=$nuevaHora;
								endwhile;				
										
							}		
						
						
					}
				}
				
			
				
				// Si no se ha especificado disponibilidad general o especifica aal elemento consulto la del servicio
				if($verifica_disponibilidad_especifica==0 && $verifica_disponibilidad_general==0): 
						$horaInicial=$r["HoraDesde"];
						$minutoAnadir=$r["IntervaloHora"];
						$hora_final = strtotime( $r["HoraHasta"] );
						$hora_actual = strtotime($r["HoraDesde"]);
						
						while($hora_actual<=$hora_final):
							
							if(strlen($horaInicial)!=8):
								$horaInicial .= ":00";
							endif;
									
							$hora["Hora"] = $horaInicial;
							$zonahoraria = date_default_timezone_get();
							$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );											
							$hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
							if (in_array($horaInicial,$array_hora_reservada))
							{
								$hora["Disponible"] = "N";
								$hora["Socio"] = $array_socio["$horaInicial"]["NombreSocio"];
							}
							else
							{
								$hora["Disponible"] = "S";
								$hora["Socio"] = "";
							}
							
							
							$hora["IDElemento"] = $IDElemento;		
							$hora["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $IDElemento . "'" );	
							array_push($response_disponibilidad, $hora);
							
							$segundos_horaInicial=strtotime($horaInicial);
							$segundos_minutoAnadir=$minutoAnadir*60;
							$array_horas=$nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
							$hora_actual = strtotime( $nuevaHora );
							$horaInicial=$nuevaHora;
						
						endwhile;
			endif;
			
			
			endwhile;
			

			}//ednw hile
	
	
	return $response_disponibilidad;
	
}




function get_disponiblidad_elemento($IDClub,$IDElemento,$IDServicio,$Fecha){
	$dbo =& SIMDB::get();
	
	
		
		$verifica_disponibilidad_especifica=0;
		$verifica_disponibilidad_general=0;
		
		// Verifico que el club y servicio este disponible en la fecha consultada
		$verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio);  
		if(!empty($verificacion)):
			$respuesta["message"] = $verificacion;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
			return $respuesta;
		endif;
	
		
		$response = array();
		$sql = "SELECT * FROM Servicio WHERE IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			$servicio_hora["IDClub"] = $IDClub;
			$servicio_hora["IDServicio"] = $IDServicio;
			$servicio_hora["IDElemento"] = $IDElemento;
			$servicio_hora["Fecha"] = $Fecha;			
			
			$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,$IDElemento,$IDServicio,$Fecha);			
			$servicio_hora["Disponibilidad"] = $response_disponibilidad;			
			array_push($response, $servicio_hora);
			
			
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
}


function timezone_offset_string( $offset ){
        return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
}


function valida_cupos_disponibles($IDClub, $IDServicio, $IDElemento, $Fecha, $horaInicial){
	$dbo =& SIMDB::get();	
	//Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
	$sql_reserva_elemento_hora_fecha = "SELECT IDReservaGeneral FROM ReservaGeneral WHERE IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and Fecha = '".$Fecha."' and (IDEstadoReserva = 1 or IDEstadoReserva=3) and Hora = '".$horaInicial."' ORDER BY Hora ";
	$result_reserva_elemento_hora_fecha = $dbo->query($sql_reserva_elemento_hora_fecha);	
	$total_cupos_reservados = $dbo->rows($result_reserva_elemento_hora_fecha);		
	return $total_cupos_reservados;
																
}


function get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento="",$Admin = "",$UnElemento="",$NumeroTurnos="", $IDTipoReserva = ""){

		$dbo =& SIMDB::get();
	
		//Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
		$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $IDServicio . "'" );
		if($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30): //Golf
			//$respuesta = SIMWebService::get_disponibilidad_campo($IDClub,$IDCampo,$Fecha, $IDServicio);
			$respuesta = SIMWebService::get_disponibilidad_campo($IDClub,"",$Fecha, $IDServicio,$Admin,$NumeroTurnos);
			return $respuesta;
		endif;
	
	
		$fecha_disponible = 0;
		
		$verifica_disponibilidad_especifica=0;
		$verifica_disponibilidad_general=0;
		
		// Verifico que el club y servicio este disponible en la fecha consultada
		$verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio);  
		if(!empty($verificacion)):
			$respuesta["message"] = $verificacion;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
			return $respuesta;
		endif;
		
		
		//Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
			$array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub,$IDServicio); 
			
			foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha):
				if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"]=="S"):
					$fecha_disponible = 1;			
				endif;
			endforeach;
			
			
			if ($fecha_disponible==0 && empty($Admin)):
				$respuesta["message"] = "Esta fecha aún no está disponible.";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				return $respuesta;
				exit;
			endif;			


		
	
		
		$response = array();
		$response_disponibilidades = array();
		$sql = "SELECT * FROM Servicio WHERE IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{		
			
			$message = $dbo->rows( $qry ) . " Encontrados";
			$servicio_hora["IDClub"] = $IDClub;
			$servicio_hora["IDServicio"] = $IDServicio;
			$servicio_hora["Fecha"] = $Fecha;
			//$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,"",$IDServicio,$Fecha);				
			
			//Horas Disponibles Elemento
			$response_disponibilidad = array();
			
			if (!empty($IDElemento))
				$condicion_elemento = " and IDServicioElemento = '".$IDElemento."'";
			
			 $r = $dbo->fetchArray( $qry );
		
	
			$sql_elementos_servicio = "Select * From ServicioElemento Where IDServicio = '".$IDServicio."' " . $condicion_elemento . " Order By Orden";
			$result_elementos_servicio = $dbo->query($sql_elementos_servicio);
			while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)):
				unset($array_hora_reservada);
				$IDElemento = $r_elementos_servicio["IDServicioElemento"];				
				
				$nombre_elemento_consulta = $r_elementos_servicio["Nombre"];
				
				//Consulto lo que  tiene reservado el elemento en la fecha indicada
				$sql_reserva_elemento = "SELECT ReservaGeneral.*, Socio.Nombre, Socio.Apellido,CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio FROM ReservaGeneral, Socio WHERE IDServicioElemento = '".$IDElemento."' and Fecha = '".$Fecha."' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";
				$qry_reserva_elemento = $dbo->query( $sql_reserva_elemento );
				$array_socio = array();
				while($row_reserva_elemento = $dbo->fetchArray($qry_reserva_elemento)){
					$tipo_reserva="";						
					if($row_reserva_elemento["Tipo"]=="Automatica"):
						//averiguo si fue automatica por una clase para mostrarlo en el nombre del socio
						$sql_reserva_padre = "SELECT * FROM ReservaGeneral WHERE Fecha = '".$Fecha."' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Hora = '".$row_reserva_elemento["Hora"]."' and Tipo <> 'Automatica' and IDSocio = '".$row_reserva_elemento["IDSocio"]."'  ORDER BY Hora Limit 1";				
						$qry_reserva_padre = $dbo->query( $sql_reserva_padre );
						$row_reserva_padre = $dbo->fetchArray($qry_reserva_padre);
						$id_servicio_maestro_reserva = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $row_reserva_padre["IDServicio"] . "'" );
						$id_servicio_cancha = $dbo->getFields( "ServicioMaestro" , "IDServicioMaestroReservar" , "IDServicioMaestro = '" . $id_servicio_maestro_reserva . "'" );																	
						//Si la reserva es una clase agrego la palabra clase
						if($id_servicio_cancha>0):
							$tipo_reserva = "Clase ";							
						else:
							$tipo_reserva = "";							
						endif;
					endif;
					
					//Verifico si el club se configuro para mostrar el nombre del socio o para mostrar un texto personalizado
					$MostrarReserva = $dbo->getFields( "Club" , "MostrarReserva" , "IDClub = '" . $IDClub . "'" );					
					if($MostrarReserva=="Pesonalizado"):
						$LabelPersonalizado = $dbo->getFields( "Club" , "LabelPersonalizado" , "IDClub = '" . $IDClub . "'" );
						$nombre_tomo_reserva = utf8_encode($LabelPersonalizado);
					else:
						$nombre_tomo_reserva = utf8_encode($row_reserva_elemento["Nombre"]) . " " .utf8_encode($row_reserva_elemento["Apellido"]);
					endif;
					
					
					$array_hora_reservada[] = $row_reserva_elemento["Hora"];
					$array_socio[ $row_reserva_elemento["Hora"] ] = $row_reserva_elemento;
					// Si la reserva fue tomada para algun beneficiario muestro el nombre del beneficiario
					if($row_reserva_elemento["IDSocioBeneficiario"]):						
						$nombre_reserva = "Benef. " . utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $row_reserva_elemento["IDSocioBeneficiario"] . "'" )	. " "  . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $row_reserva_elemento["IDSocioBeneficiario"] . "'" ));	
					else:
						$nombre_reserva = $tipo_reserva . $nombre_tomo_reserva;
					endif;
					
					$array_socio[ $row_reserva_elemento["Hora"] ]["NombreSocio"] =$nombre_reserva;
					$array_socio[ $row_reserva_elemento["Hora"] ]["IDSocio"] = $row_reserva_elemento["IDSocio"];
					$array_socio[ $row_reserva_elemento["Hora"] ]["ModalidadEsquiSocio"] = $dbo->getFields( "TipoModalidadEsqui" , "Nombre" , "IDTipoModalidadEsqui = '" . $row_reserva_elemento["IDTipoModalidadEsqui"] . "'" );
					$array_socio[ $row_reserva_elemento["Hora"] ]["IDReservaGeneral"] = $row_reserva_elemento["IDReservaGeneral"];					
				}			
			
			
				//Horas generales del servicio
				/*
				$horaInicial=$r["HoraDesde"];
				$minutoAnadir=$r["IntervaloHora"];
				$hora_final = strtotime( $r["HoraHasta"] );
				$hora_actual = $r["HoraDesde"];
				*/
				
				$dia_fecha= date('w', strtotime($Fecha));
				
				
					// Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
					$sql_dispo_elemento_primera = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%' Order by HoraDesde Limit 1";					
					$qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
					$row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
					//$horaInicial=$row_dispo_elemento_primera["HoraDesde"];
					$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_primera["HoraDesde"];					
					
					//Verifico que no tenga cierre el elemento en esta fecha
					$verifica_abierto_servicio = SIMWebservice::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$IDElemento);
					if(empty($verifica_abierto_servicio)){
					
					//Verifico si tene disponibilidad  general el elemento				
					$sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%'";
					$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
					if( $dbo->rows( $qry_dispo_elemento_gral ) > 0 ){	
					
					
							$verifica_disponibilidad_general = 1;	
							while($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)){								
								
								$horaInicial=$row_dispo_elemento_gral["HoraDesde"];
								
								//$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_gral["HoraDesde"];
								$minutoAnadir=$dbo->getFields( "Disponibilidad" , "Intervalo" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
								
								// Si la fecha es el dia actual verifico el tiempo de anticipacion para reservar
								if ($Fecha==date("Y-m-d")):
									$medicion_tiempo = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacion" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
									$valor_tiempo_anticipacion = (int)$dbo->getFields( "Disponibilidad" , "Anticipacion" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
									if($medicion_tiempo=="Horas"):
										$valor_tiempo_anticipacion = $valor_tiempo_anticipacion * 60; // Lo convierto a minutos
									elseif($medicion_tiempo=="Dias"):
									 	$valor_tiempo_anticipacion = 0;	
									endif;	
								else:
										$valor_tiempo_anticipacion = 0;	
								endif;
								
								
								
								//Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
									$medicion_tiempo_anticipacion = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacionTurno" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"]  . "'" );
									$valor_anticipacion_turno = (int)$dbo->getFields( "Disponibilidad" , "AnticipacionTurno" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"]  . "'" );
									switch($medicion_tiempo_anticipacion):
										case "Dias":	
											$minutos_anticipacion_turno = (60*24) * $valor_anticipacion_turno;
										break;
										case "Horas":
											$minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
										break;
										case "Minutos":
											$minutos_anticipacion_turno = $valor_anticipacion_turno;
										break;
										default:
											$minutos_anticipacion_turno = 0;
									endswitch;
									
								
								
								//Si es administrador no tiene limite de anticipacion
								if($Admin=="S"){
									$valor_tiempo_anticipacion = 0;	
									$minutos_anticipacion_turno = 0;
								}
								
								//Consulto hace una hora para mostrar los turnos anterior segun solicitud de lagartos
								$id_servicio_inicial = $dbo->getFields( "ServicioMaestro" , "IDServicioInicial" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
								
								//$hace_una_hora = strtotime ( '-1 hour' , strtotime ( date("Y-m-d H:i:s") ) ) ;								
								$hace_una_hora = strtotime ( '-'.$minutoAnadir.' minutes' , strtotime ( date("Y-m-d H:i:s") ) ) ;	
															
								if($Fecha==date("Y-m-d")):
									$hora_real = date('Y-m-d H:i:s',$hace_una_hora);	
								else:
									$hora_real = date('Y-m-d H:i:s');	
								endif;
								
								// Solo aplica lo de 1 hora antes cuando no es servicio de clases
								if($id_servicio_inicial=="1"): 
									$hora_real = date('Y-m-d H:i:s');									
								endif;
															
								
								$hora_empezar_reserva = strtotime ( '-'.$valor_tiempo_anticipacion.' minute' , strtotime ( $horaInicial_reserva ) ) ;
								//$hora_empezar_reserva = date ( 'H:i:s' , $hora_empezar_reserva );
								$hora_actual_sistema = strtotime( $hora_real );
								
								
								
								
								$hora_final = strtotime( $row_dispo_elemento_gral["HoraHasta"] );
								$hora_actual = strtotime( $row_dispo_elemento_gral["HoraDesde"] );
								
								
								$primer_horario = 0;
								$primer_horario_disponible="";
								$verifica_abierto_servicio_hora="";
								while($hora_actual<=$hora_final):
								 
								$hora_fecha_actual =  $Fecha . " " .date ( 'H:i:s',$hora_actual);								
								$hora_puede_reservar = strtotime ( '+'.$minutos_anticipacion_turno.' minute' , strtotime ( $hora_real ) ) ;
								/*****************************************************************************************************
								Valido que solo devuelva las fecha/hora mayor a la actual, esto por si le cambian la hora al celular
								Valido que ésta hora este disponible para reervar en el tiempo limite deacuerdo al parametro AnticipacionTurno
								******************************************************************************************************/
								if(strtotime($hora_fecha_actual) >= strtotime($hora_real) &&  $hora_puede_reservar <= strtotime($hora_fecha_actual)):
											
											if(strlen($horaInicial)!=8):
												$horaInicial .= ":00";
											endif;	
											
											
											$flag_hora_disponible = 0;
											// Si el servicio es una clase y necesita reservar cancha verifico que exista al menos un elemento (cancha) disponible para mostrar la hora											
											$id_servicio_cancha = $dbo->getFields( "ServicioMaestro" , "IDServicioMaestroReservar" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
											if($id_servicio_cancha>0):
												  // Consulto el servicio del club asociado a este servicio maestro
												  $IDServicioCanchaClub  = $dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '".$IDClub."'" );
												  // Valido si existe una cancha disponible en el horario de la clase
												  $IDElemento_cancha = SIMWebService::buscar_elemento_disponible($IDClub,$IDServicioCanchaClub,$Fecha,$horaInicial);
												  if(empty($IDElemento_cancha)):													  												  		
														$flag_hora_disponible=1;
													endif;
											endif;				
											
											
											
														$hora["Hora"] = $horaInicial;
														$zonahoraria = date_default_timezone_get();
														$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );											
														$hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
														
														
														//echo "<br>" . date ( 'Y-m-d H:i:s' , $hora_puede_reservar );
														//exit;
														
														$datos_disponibilidad = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array" );
														
														
														// Verifico si el servicio en esta disponiblidad permite a varios socios tomar el mismo turno, por ejemplo clase de gimnasia
														$multiples_cupos = "N";
														if((int)$datos_disponibilidad["Cupos"]>1):
															$multiples_cupos = "S";
															//Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
															$cupos_reservados = self::valida_cupos_disponibles($IDClub, $IDServicio, $IDElemento, $Fecha, $horaInicial);
															
															//Valido si todavia existe cupo en esta hora
															if($cupos_reservados < $datos_disponibilidad["Cupos"]):
																$cupo_total="N"; // aun hay cupos disponibles
															else:
																$cupo_total="S";// ya no hay cupos
															endif;
														endif;	
															
															
														//Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
														$hora_real_momento = strtotime(date("Y-m-d H:i:s")); //calculo de nuevo la fecha y hora real del sistema ya que pudo ser modificada con un tiempo de anticipacion anteriormente
														if ( (in_array($horaInicial,$array_hora_reservada) && $multiples_cupos=="N") || ($hora_real_momento<$hora_empezar_reserva && $valor_tiempo_anticipacion >0) )
														{	
															// Si tiene Auxiliar y es admin mustro el auxiliar seleccionado
															$NombreAuxiliarReserva="";
															if(!empty($Admin)):
																$IDAuxiliarReserva = $dbo->getFields( "ReservaGeneral" , "IDAuxiliar" , "IDReservaGeneral = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "'" );
																if(!empty($IDAuxiliarReserva)):
																	$NombreAuxiliarReserva = "<span style='color:#696'> / " . $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $IDAuxiliarReserva . "'" ) . "</span>";
																endif;
																
																//Si es una clase consulto el padre de la reserva
																$TipoReservaA = $dbo->getFields( "ReservaGeneral" , "Tipo" , "IDReservaGeneral = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "'" );
																					
																if($TipoReservaA=="Automatica"):
																	$IDBeneficiario = $dbo->getFields( "ReservaGeneral" , "IDBeneficiario" , "IDReservaGeneral = '" . $array_socio["$horaInicial"]["IDReservaGeneral"] . "'" );
																	/*
																	if(!empty($IDBeneficiario)):
																		$condicion_benef = " and IDBeneficiario = '".$IDBeneficiario."' ";
																	else:
																		$condicion_benef = " and IDBeneficiario = '0' ";	
																	endif;																	
																	*/
																	$sql_id_reserva_padre_e = "SELECT * FROM ReservaGeneralAutomatica WHERE  IDReservaGeneralAsociada = '".$array_socio["$horaInicial"]["IDReservaGeneral"]."' Limit 1";				
																	$qry_id_reserva_padre_e = $dbo->query( $sql_id_reserva_padre_e );
																	$row_id_reserva_padre_e = $dbo->fetchArray($qry_id_reserva_padre_e);
																	
																	//$sql_reserva_padre_e = "SELECT * FROM ReservaGeneral WHERE Fecha = '".$Fecha."' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Hora = '".$hora["Hora"]."' and Tipo <> 'Automatica' and IDSocio = '".$array_socio["$horaInicial"]["IDSocio"]."' ".$condicion_benef ." ORDER BY Hora Limit 1";				
																	$sql_reserva_padre_e = "SELECT * FROM ReservaGeneral WHERE IDReservaGeneral = '".$row_id_reserva_padre_e["IDReservaGeneral"]."' Limit 1";																	
																	$qry_reserva_padre_e = $dbo->query( $sql_reserva_padre_e );
																	$row_reserva_padre_e = $dbo->fetchArray($qry_reserva_padre_e);
																	$id_servicio_maestro_reserva = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $row_reserva_padre_e["IDServicio"] . "'" );
																	$id_servicio_cancha_e = $dbo->getFields( "ServicioMaestro" , "IDServicioMaestroReservar" , "IDServicioMaestro = '" . $id_servicio_maestro_reserva . "'" );																	
																	//Si la reserva es una clase agrego la palabra clase
																	if($id_servicio_cancha_e>0):
																		//Si se hizo una reserva automatica muestro el nombre del elemento reservado
																		$id_elemento_reservado = $dbo->getFields( "ReservaGeneral" , "IDServicioElemento" , "IDReservaGeneral = '" . $row_reserva_padre_e["IDReservaGeneral"] . "'" );																	
																		$elemento_reservado = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" .$id_elemento_reservado . "'" );																	
																		if(!empty($elemento_reservado)):
																			$NombreAuxiliarReserva .=  "<span style='color:#696'> / " . utf8_encode($elemento_reservado) . "</span>";
																		endif;
																	endif;																	
																endif;
																
																
																
																  //verifico si tiene una reserva automatica para mostrarla
																  $id_reserva_automatica = $dbo->getFields( "ReservaGeneralAutomatica" , "IDReservaGeneralAsociada" , " IDReservaGeneral = '".$array_socio["$horaInicial"]["IDReservaGeneral"]."'" );	
																  if (!empty( $id_reserva_automatica)): 
																	  $detalle_reserva_auto = $dbo->fetchAll( "ReservaGeneral", " IDReservaGeneral = '" . $id_reserva_automatica . "' ", "array" );
																	  $NombreAuxiliarReserva .=  "<span style='color:#696'> / " . $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '".$detalle_reserva_auto["IDServicioElemento"]."'" ) . "</span>";
																  endif;	  
																							
															endif;	
															
															
															$hora["Disponible"] = "N";															
															$hora["Socio"] = $array_socio["$horaInicial"]["NombreSocio"] . $NombreAuxiliarReserva;
															$hora["IDSocio"] = $array_socio["$horaInicial"]["IDSocio"];
															$hora["ModalidadEsquiSocio"] = $array_socio["$horaInicial"]["ModalidadEsquiSocio"];
															$hora["IDReserva"] = $array_socio["$horaInicial"]["IDReservaGeneral"];
															
															if($primer_horario==0):
																$primer_horario_disponible="reservado_socio";
															endif;
															
														}
														else
														{
															
															
															
															//Verifico que no tenga fecha de cierre en esta hora 
															$verifica_abierto_servicio_hora = SIMWebservice::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$IDElemento,$horaInicial);
															
															//Validacion Especial y temporal cuando es una clase de lagartos y la hora es mayor a 8pm no se muestra las 7 8 y 9 am del dia siguiente a menos que la hora de las 7 esté tomada															  
															  if( ($IDClub==7 || $IDClub==8) && ($IDServicio==43 || $IDServicio==41)  && empty($verifica_abierto_servicio_hora)){//Clases tenis especial
																	$fecha_hoy_sistema = date('Y-m-d');
																	$fecha_manana = strtotime ( '+1 day' , strtotime ( $fecha_hoy_sistema ) ) ;
																	$fecha_corta_manana = date ( 'Y-m-d' , $fecha_manana );
																	 
																	if( $horaInicial=="06:00:00" || $horaInicial=="07:00:00" || $horaInicial=="08:00:00" || $horaInicial=="09:00:00" ) {
																		//echo $Fecha."==".$fecha_corta_manana;	
																		if( $Fecha==date("Y-m-d") || ($Fecha==$fecha_corta_manana && date("H") >= 20 )){
																			if($primer_horario_disponible!="reservado_socio"){ // Si la primera hora está reservada, no bloqueo estos horarios
																				$verifica_abierto_servicio_hora="Hora no disponible: " . $Fecha ." Motivo:No disponible";
																			}
																		}
																	}
															  }
															
															
															
															if(!empty($verifica_abierto_servicio_hora)):
																//extraigo la razon
																$mensaje_cierre = explode(":",$verifica_abierto_servicio_hora); 
																$hora["Disponible"] = "N";															
																$hora["Socio"] = $mensaje_cierre[2];
																$hora["IDSocio"] = "";
																$hora["ModalidadEsquiSocio"] = "";
																$hora["IDReserva"] = "";
															elseif($flag_hora_disponible==1):
																$hora["Disponible"] = "N";															
																$hora["Socio"] = "No hay cancha disponible para clase";
																$hora["IDSocio"] = "";
																$hora["ModalidadEsquiSocio"] = "";
																$hora["IDReserva"] = "";															
															else:
															
															
															
															
															//Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
															if (!empty($IDTipoReserva)):																				
																$datos_tipo_reserva = $dbo->fetchAll( "ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array" );
																$cantidad_turnos = $datos_tipo_reserva["NumeroTurnos"];		
															else:
																$cantidad_turnos = 1;		
															endif;	
																
																if($cantidad_turnos>1):
																	//verifico si es posible reservar en esta hora cuando el turno sea mas de 1, valido si los siguientes turnos estan disponible
																	$array_disponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $horaInicial, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos );																	
																endif;
																if(count($array_disponible)!=(int)($cantidad_turnos-1) && $cantidad_turnos>1):
																	$hora["Disponible"] = "N";															
																	$hora["Socio"] = "No disponible";
																	$hora["IDSocio"] = "";
																	$hora["ModalidadEsquiSocio"] = "";
																	$hora["IDReserva"] = "";	
																else:
																	//Si permite multiples cupos y hay cupos disponibles pongo la hora como disponible																	
																	if($multiples_cupos=="S" && $cupo_total=="S"):
																		$hora["Disponible"] = "N";															
																		$hora["Socio"] = "Se llego al limite de cupos";
																		$hora["IDSocio"] = "";
																		$hora["ModalidadEsquiSocio"] = "";
																		$hora["IDReserva"] = "";																			
																	else:
																		$hora["Disponible"] = "S";
																		$hora["Socio"] = "";
																		$hora["IDSocio"] = "";
																		$hora["ModalidadEsquiSocio"] = "";
																		$hora["IDReserva"] = "";
																	endif;	
																
																	//if(strtotime($hora_fecha_actual) >= strtotime(date("Y-m-d H:i:s"))):
																		//$hora["Disponible"] = "S";
																		//$hora["Socio"] = "";
																		//$hora["IDSocio"] = "";
																		//$hora["ModalidadEsquiSocio"] = "";
																		//$hora["IDReserva"] = "";
																	
																	//else:
																		/*
																		$hora["Disponible"] = "N";														
																		$hora["Socio"] = "Hora no disponible";
																		$hora["IDSocio"] = "";
																		$hora["IDReserva"] = "";		
																		*/
																		//$hora["Disponible"] = "S";														
																		//$hora["Socio"] = "";
																		//$hora["IDSocio"] = "";
																		//$hora["IDReserva"] = "";																																						
																	//endif;		
																	
																endif;
																
															endif;
															
															
														}
														
														//Averiguo el numero de dias de anticipacion																						
														$hora["MaximoPersonaTurno"] = $datos_disponibilidad["MaximoPersonaTurno"];		
														$hora["NumeroInvitadoClub"] = $datos_disponibilidad["NumeroInvitadoClub"];		
														$hora["NumeroInvitadoExterno"] = $datos_disponibilidad["NumeroInvitadoExterno"];	
														
														//Repeticion reserva
														$hora["IDDisponibilidad"] = $datos_disponibilidad["IDDisponibilidad"];
														$hora["PermiteRepeticion"] = $datos_disponibilidad["PermiteRepeticion"];	
														$hora["MedicionRepeticion"] = $datos_disponibilidad["MedicionRepeticion"];	
														$hora["FechaFinRepeticion"] = $datos_disponibilidad["FechaFinRepeticion"];
														
														//Consulto los datos de georeferenciacion
														$datos_disponibilidad_geo = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $datos_disponibilidad["IDDisponibilidad"] . "' ", "array" );
														$hora["Georeferenciacion"] = $datos_disponibilidad_geo["Georeferenciacion"];
														//Consulto los demas datos de la configuracion del servicio
														$datos_geo_servicio = $dbo->fetchAll( "Servicio", " IDServicio = '" . $IDServicio . "' ", "array" );
														$hora["Latitud"] = $datos_geo_servicio["Latitud"];
														$hora["Longitud"] = $datos_geo_servicio["Longitud"];
														$hora["Rango"] = $datos_geo_servicio["Rango"];
														$hora["MensajeFueraRango"] = $datos_geo_servicio["MensajeFueraRango"];														
																								
														
														$hora["IDElemento"] = $IDElemento;
														// Consulto las modalidades que pueda tener
														$nom_modalidad="";
														$array_modalidad_elemento = SIMWebService::get_modalidades($IDClub,"",$IDElemento);
														if(count($array_modalidad_elemento)>0):
															foreach($array_modalidad_elemento["response"] as $id_modalidad => $datos_modalidad):
																$nom_modalidad[] = $datos_modalidad["Descripcion"];
															endforeach;
															$nombre_modalidad = implode("-",$nom_modalidad);
														endif;	
														// FIn Modalidades
															
														$hora["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $IDElemento . "'" );
														$hora["ModalidadElemento"] = $nombre_modalidad;
														$hora["OrdenElemento"] = $dbo->getFields( "ServicioElemento" , "Orden" , "IDServicioElemento = '" . $IDElemento . "'" );
														array_push($response_disponibilidad, $hora);														
													
								endif;		
								$primer_horario++;							
								$array_horas_elemento[] = $horaInicial;								
								$segundos_horaInicial=strtotime($horaInicial);
								$segundos_minutoAnadir=$minutoAnadir*60;
								$array_horas=$nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
								$hora_actual = strtotime( $nuevaHora );
								$horaInicial=$nuevaHora;
								
											
												
								endwhile;
										
							}		
					}
				}
		
			endwhile;
			
			
			
			foreach($response_disponibilidad as $id_array => $datos_array):				
				$orden_letra=SIMResources::$abecedario[$datos_array["OrdenElemento"]];
				$array_ordenado_hora[$orden_letra."_".$datos_array["Hora"].$datos_array["IDElemento"]] = $datos_array;
 			endforeach;
			
			ksort($array_ordenado_hora);
			
			$response_array_ordenado = array();
			foreach($array_ordenado_hora as $id_array => $datos_array):			
				array_push($response_array_ordenado, $datos_array);			
 			endforeach;
			
			array_push($response_disponibilidades, $response_array_ordenado);
			
			// Si $UnElemento no es vacio no ordeno el array ya que se consulto un solo elemnto de los contrario ordeno todos los elemnetos buscados
			if(!empty($UnElemento)):			
				$servicio_hora["Disponibilidad"] = $response_array_ordenado;
			else:
				$servicio_hora["Disponibilidad"] = $response_disponibilidades;	
			endif;
			
			//$servicio_hora["Disponibilidad"] = $response_disponibilidades;
			
			$servicio_hora["name"] = $nombre_elemento_consulta;
			
			array_push($response, $servicio_hora);
		
		
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
		
		//inserta _log				
		//$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('get_disponiblidad_elemento_servicio','IDClub: ".$IDClub. " IDServicio:" . $IDServicio . " . Fecha: ".$Fecha." Elemento: ".$IDElemento."','".json_encode($respuesta)."')");
		
			
		return $respuesta;	
	
}




function get_hora_disponible($IDClub,$IDServicio,$Fecha,$Hora,$Admin = ""){
	$dbo =& SIMDB::get();
	
		$fecha_disponible = 0;
		$verifica_disponibilidad_especifica=0;
		$verifica_disponibilidad_general=0;
		
		// Verifico que el club y servicio este disponible en la fecha consultada
		$verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio);  
		if(!empty($verificacion)):
			$respuesta["message"] = $verificacion;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
			return $respuesta;
		endif;
		
		
		//Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
			$array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub,$IDServicio); 
			
			foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha):
				if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"]=="S"):
					$fecha_disponible = 1;			
				endif;
			endforeach;
			
			
			if ($fecha_disponible==0):
				$respuesta["message"] = "Esta fecha aún no está disponible";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				return $respuesta;
			endif;			


		
	
		
		$response = array();
		$response_disponibilidades = array();
		$sql = "SELECT * FROM Servicio WHERE IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{		
			
			$message = $dbo->rows( $qry ) . " Encontrados";
			$servicio_hora["IDClub"] = $IDClub;
			$servicio_hora["IDServicio"] = $IDServicio;
			$servicio_hora["Fecha"] = $Fecha;
			//$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,"",$IDServicio,$Fecha);				
			
			//Horas Disponibles Elemento
			$response_disponibilidad = array();
			
			
			
			 $r = $dbo->fetchArray( $qry );
		
	
			$sql_elementos_servicio = "Select * From ServicioElemento Where IDServicio = '".$IDServicio."' " . $condicion_elemento;
			$result_elementos_servicio = $dbo->query($sql_elementos_servicio);
			$total_elementos = $dbo->rows($result_elementos_servicio);
			
			
			while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)):
				unset($array_hora_reservada);
				$IDElemento = $r_elementos_servicio["IDServicioElemento"];
			
				
				$dia_fecha= date('w', strtotime($Fecha));
				
				
					// Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
					$sql_dispo_elemento_primera = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%' Order by HoraDesde Limit 1";
					$qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
					$row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
					//$horaInicial=$row_dispo_elemento_primera["HoraDesde"];
					$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_primera["HoraDesde"];
						
				
					//Verifico si tene disponibilidad  general el elemento				
					$sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%'";
					$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
					if( $dbo->rows( $qry_dispo_elemento_gral ) > 0 ){	
					
					
							$verifica_disponibilidad_general = 1;	
							while($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)){								
								
								$horaInicial=$row_dispo_elemento_gral["HoraDesde"];
								//$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_gral["HoraDesde"];
								$minutoAnadir=$dbo->getFields( "Disponibilidad" , "Intervalo" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
								
								// Si la fecha es el dia actual verifico el tiempo de anticipacion para reservar
								if ($Fecha==date("Y-m-d")):
									$medicion_tiempo = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacion" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
									$valor_tiempo_anticipacion = (int)$dbo->getFields( "Disponibilidad" , "Anticipacion" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
									if($medicion_tiempo=="Horas"):
										$valor_tiempo_anticipacion = $valor_tiempo_anticipacion * 60; // Lo convierto a minutos
									endif;	
								else:
										$valor_tiempo_anticipacion = 0;	
								endif;
								
								
								
								//Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
									$medicion_tiempo_anticipacion = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacionTurno" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"]  . "'" );
									$valor_anticipacion_turno = (int)$dbo->getFields( "Disponibilidad" , "AnticipacionTurno" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"]  . "'" );
									switch($medicion_tiempo_anticipacion):
										case "Dias":	
											$minutos_anticipacion_turno = (60*24) * $valor_anticipacion_turno;
										break;
										case "Horas":
											$minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
										break;
										case "Minutos":
											$minutos_anticipacion_turno = $valor_anticipacion_turno;
										break;
										default:
											$minutos_anticipacion_turno = 0;
									endswitch;
									
								
								
								//Si es administrador no tiene limite de anticipacion
								if($Admin=="S"){
									$valor_tiempo_anticipacion = 0;	
									$minutos_anticipacion_turno = 0;
								}
								
								
								$hora_real = date('Y-m-d H:i:s');									
								$hora_empezar_reserva = strtotime ( '-'.$valor_tiempo_anticipacion.' minute' , strtotime ( $horaInicial_reserva ) ) ;
								//$hora_empezar_reserva = date ( 'H:i:s' , $hora_empezar_reserva );
								$hora_actual_sistema = strtotime( $hora_real );
								
								
								
								
								$hora_final = strtotime( $row_dispo_elemento_gral["HoraHasta"] );
								$hora_actual = strtotime( $row_dispo_elemento_gral["HoraDesde"] );
								
								
								while($hora_actual<=$hora_final):
								
								$hora_fecha_actual =  $Fecha . " " .date ( 'H:i:s',$hora_actual);								
								$hora_puede_reservar = strtotime ( '+'.$minutos_anticipacion_turno.' minute' , strtotime ( $hora_real ) ) ;
								/*****************************************************************************************************
								Valido que solo devuelva las fecha/hora mayor a la actual, esto por si le cambian la hora al celular
								Valido que ésta hora este disponible para reervar en el tiempo limite deacuerdo al parametro AnticipacionTurno
								******************************************************************************************************/
								if(strtotime($hora_fecha_actual) >= strtotime($hora_real) &&  $hora_puede_reservar <= strtotime($hora_fecha_actual)):
								
								
								
											if(strlen($horaInicial)!=8):
												$horaInicial .= ":00";
											endif;	
											
											$hora["Hora"] = $horaInicial;
											$zonahoraria = date_default_timezone_get();
											$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );											
											$hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
											
											
											//echo "<br>" . date ( 'Y-m-d H:i:s' , $hora_puede_reservar );
											//exit;
											
											$datos_disponibilidad = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array" );
											//Verifico que esta hora la tenga disponible algun elemento y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
											
											// De acuerdo al numero de elementos del servicio disponibles en esta hora verifico si en esta hora ya está reservado
											$sql_reserva_hora = "SELECT * FROM ReservaGeneral WHERE IDServicio = '".$IDServicio."' and Fecha = '".$Fecha."' and (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Hora = '".$hora["Hora"]."'  ORDER BY Hora ";
											$qry_reserva_hora= $dbo->query( $sql_reserva_hora );
											$total_horas_reservadas = $dbo->rows( $qry_reserva_hora );
											$total_horas_reservadas = $dbo->rows( $qry_reserva_hora );
											
											$contador_elementos_disponibles = 0;
											// Verifico cuantos elementos tienen esta hora disponible
											$sql_dispo_hora = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and ('".$hora["Hora"]."' >= HoraDesde and '".$hora["Hora"]."'<=HoraHasta)  Order by HoraDesde";											
											$qry_dispo_hora= $dbo->query( $sql_dispo_hora );
											$row_dispo_hora= $dbo->fetchArray($qry_dispo_hora);
											if (!empty($row_dispo_hora["IDServicioElemento"])):
												$array_elementos_hora = explode("|",$row_dispo_hora["IDServicioElemento"]);
												foreach($array_elementos_hora as $id_elemento_hora):
													if (!empty($id_elemento_hora)):
														$contador_elementos_disponibles++;
													endif;
												endforeach;												
											endif;
											$total_elementos = $contador_elementos_disponibles;
											
											if ( $total_horas_reservadas>=$total_elementos || ($hora_actual_sistema<$hora_empezar_reserva && $valor_tiempo_anticipacion >0)  )
											{
												$hora["Disponible"] = "N";
												$hora["Socio"] = "Reservado";
											}
											else
											{
												$hora["Disponible"] = "S";
												$hora["Socio"] = "";
											}
											
											$hora["MaximoPersonaTurno"] = $datos_disponibilidad["MaximoPersonaTurno"];		
											$hora["NumeroInvitadoClub"] = $datos_disponibilidad["NumeroInvitadoClub"];		
											$hora["NumeroInvitadoExterno"] = $datos_disponibilidad["NumeroInvitadoExterno"];	
											
											//Repeticion reserva
											$hora["IDDisponibilidad"] = $datos_disponibilidad["IDDisponibilidad"];
											$hora["PermiteRepeticion"] = $datos_disponibilidad["PermiteRepeticion"];	
											$hora["MaximoRepeticion"] = $datos_disponibilidad["NumeroRepeticion"] . " " . $datos_disponibilidad["MedicionRepeticion"];	
											
											
											//$hora["IDElemento"] = $IDElemento;		
											//$hora["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $IDElemento . "'" );
											array_push($response_disponibilidad, $hora);
								endif;		
																
								$array_horas_elemento[] = $horaInicial;								
								$segundos_horaInicial=strtotime($horaInicial);
								$segundos_minutoAnadir=$minutoAnadir*60;
								$array_horas=$nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
								$hora_actual = strtotime( $nuevaHora );
								$horaInicial=$nuevaHora;
											
												
								endwhile;
										
							}		
					}
		
			endwhile;
			
			
			
			//Ordeno el array y que aparezca solo una hora para todo elementos
			foreach($response_disponibilidad as $id_array => $datos_array):				
				$array_ordenado_hora[$datos_array["Hora"]] = $datos_array;
 			endforeach;
			
			ksort($array_ordenado_hora);
			
			$response_array_ordenado = array();
			foreach($array_ordenado_hora as $id_array => $datos_array):
				array_push($response_array_ordenado, $datos_array);			
 			endforeach;
			
			//array_push($response_array_ordenado, $array_ordenado_hora);
			
			
			$servicio_hora["Disponibilidad"] = $response_array_ordenado;	
			
			//$servicio_hora["Disponibilidad"] = $response_disponibilidades;
			
			
			array_push($response, $servicio_hora);
		
		
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
		
		
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('get_disponiblidad_elemento_servicio','IDClub: ".$IDClub. " IDServicio:" . $IDServicio . " . Fecha: ".$Fecha." Elemento: ".$IDElemento."','".json_encode($respuesta)."')");
		
			
		return $respuesta;	
	
}



function get_beneficiarios($IDClub,$IDSocio){
	$dbo =& SIMDB::get();
		if( !empty( $IDSocio ) ){
				$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'", "array" );
				if(!empty($datos_socio["IDSocio"])){
									//Consulto el nucleo familiar
									if (!empty($datos_socio["AccionPadre"])): // Es beneficiario
										$condicion_nucleo = " and (AccionPadre = '".$datos_socio["AccionPadre"]."' or Accion = '".$datos_socio["AccionPadre"]."')";
										$tipo_socio = "Beneficiario";
									else: // es Cabeza familia
										$condicion_nucleo = " and AccionPadre = '".$datos_socio["Accion"]."'";	
										$tipo_socio = "Socio";
									endif;
									
									$response_beneficiario = array();
									$sql_nucleo = "SELECT IDClub, IDSocio, Foto, concat(Nombre, ' ', Apellido) as Socio, Accion, AccionPadre, CodigoBarras FROM Socio WHERE IDClub = '".$IDClub."' and IDSocio <> '".$datos_socio["IDSocio"]."' " . $condicion_nucleo;
									$qry_nucleo = $dbo->query( $sql_nucleo );
									while($datos_nucleo = $dbo->fetchArray( $qry_nucleo )):
										$foto_nucleo = "";
										$foto_cod_barras_nucleo = "";
										
										$socio_beneficiario[IDBeneficiario] =  $datos_nucleo[IDSocio];										
										$socio_beneficiario[Nombre] =  utf8_encode( $datos_nucleo[Socio] );
										$socio_beneficiario[TipoBeneficiario] =  "Socio";
										
										array_push($response_beneficiario, $socio_beneficiario);
									endwhile;
									
									
									//Consulto los invitados vigentes del socio
									$sql_invitado = "SELECT IDInvitado FROM SocioInvitadoEspecial WHERE IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."' and FechaInicio >= CURDATE() Union 
													SELECT IDInvitado FROM SocioAutorizacion WHERE IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."' and FechaInicio >= CURDATE()";	
									$qry_invitado = $dbo->query( $sql_invitado );
									while($datos_invitado = $dbo->fetchArray( $qry_invitado )):
										$invitado_beneficiario["IDBeneficiario"] = $datos_invitado["IDInvitado"];
										$invitado_beneficiario["Nombre"] = utf8_encode($dbo->getFields( "Invitado" , "Nombre" , "IDInvitado = '" . $datos_invitado["IDInvitado"] . "'" ) . " " . $dbo->getFields( "Invitado" , "Apellido" , "IDInvitado = '" . $datos_invitado["IDInvitado"] . "'" ));
										$invitado_beneficiario["TipoBeneficiario"] = "Invitado";
										array_push($response_beneficiario, $invitado_beneficiario);
									endwhile;	
									
									
									$response["IDClub"] = $datos_socio["IDClub"];
									$response["IDSocio"] = $datos_socio["IDSocio"];									
									$response["Socio"] = utf8_encode( $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] );									
									$response["Beneficiarios"] = $response_beneficiario;
									$respuesta["message"] = "ok";
									$respuesta["success"] = true;
									$respuesta["response"] = $response;
									
									
						
				}
				else{
						$respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;					
				}
			}
			else{
				$respuesta["message"] = "25. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
			
			
			return $respuesta;
	
	
}



function get_campos($IDClub,$IDSocio,$IDServicio){
	$dbo =& SIMDB::get();
		
		$response = array();
		$sql = "SELECT SE.* FROM ServicioElemento SE, Servicio S WHERE SE.IDServicio = S.IDServicio and SE.Publicar = 'S' and S.IDClub = '".$IDClub."' and SE.IDServicio = '".$IDServicio."' ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				$elemento["IDClub"] = $IDClub;
				$elemento["IDServicio"] = $r["IDServicio"];
				$elemento["IDCampo"] = $r["IDServicioElemento"];
				$elemento["Nombre"] = $r["Nombre"];
				
				array_push($response, $elemento);

			}//ednw hile
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
}	


function get_fecha_disponibilidad_servicio($IDClub,$IDServicio){
	$dbo =& SIMDB::get();
	
		$response = array();
		
		
		if( !empty( $IDServicio ) ){
			
			
				$sql_servicio = "SELECT * FROM Servicio WHERE IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."'";
				$qry_servicio = $dbo->query( $sql_servicio );
				if( $dbo->rows( $qry_servicio ) > 0 )
				{
					$message = $dbo->rows( $qry_servicio ) . " Encontrados";
					while( $r = $dbo->fetchArray( $qry_servicio ) )
					{						
						$servicio["IDServicio"] = $r["IDServicioElemento"];
						$servicio["IDClub"] = $IDClub;
						$servicio["IDServicio"] = $r["IDServicio"];
						$servicio["Nombre"] = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $r["IDServicioMaestro"] . "'" );
						
						//Servicios Reservas
						$response_fechas = array();
						
						//Para mesa de yeguas solo mosttrar dos fechas
						if($IDClub==9):
							$dias_mostrar = 3;	
						else:
							$dias_mostrar = 15;	
						endif;
						
						$fecha_actual = date('Y-m-j');
						$fecha_final = strtotime ( '+15 day' , strtotime ( $fecha_actual ) ) ;
						$fecha_final = date ( 'Y-m-j' , $fecha_final );
						$fechaInicio=strtotime($fecha_actual );
						$fechaFin=strtotime($fecha_final );
						
						$contador = 1;
						$primera_fecha = 1;
						$flag_disponible_hoy=0;
						
						for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
							$fecha_validar = date("Y-m-d", $i);
							$fecha_fin_validar = date("Y-m-d", $fechaFin);
							//Consulto la disponibilidad en este dia
							$dia_semana= date('w', strtotime($fecha_validar));
							$sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_semana."|%'  Order By HoraDesde ASC, HoraHasta DESC Limit 1";
							$qry_disponibilidad = $dbo->query($sql_dispo_elemento_gral);
							$r_disponibilidad = $dbo->fetchArray($qry_disponibilidad);
							//Consulto la hora maxima del dia para reservar
							$sql_dispo_max = "Select HoraHasta From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_semana."|%'  Order By HoraHasta DESC Limit 1";
							$qry_dispo_max= $dbo->query($sql_dispo_max);
							$r_dispo_max = $dbo->fetchArray($qry_dispo_max);
							$HoraHastaFinal = $r_dispo_max["HoraHasta"];
							
							$total_disponibilidades = (int)$dbo->rows($qry_disponibilidad);
							
							// Si la fecha es hoy valido que la hora hata todavia este disponible
							if ($fecha_validar==date("Y-m-d")):			
								//echo strtotime(date("H:i:s")) ."<=". strtotime($r_disponibilidad["HoraHasta"]);			
								if (strtotime(date("H:i:s")) <= strtotime($HoraHastaFinal)):
									$flag_disponible_hoy = 0;
								else:
									$flag_disponible_hoy = 1;	
								endif;
							else:
								$flag_disponible_hoy = 0;	
							endif;
							
							if($flag_disponible_hoy==0):
							
							//verifico con cuanto tiempo de anticipacion se puede reservar
								$medicion_tiempo = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacion" , "IDDisponibilidad = '" . $r_disponibilidad["IDDisponibilidad"] . "'" );
								$valor_anticipacion = (int)$dbo->getFields( "Disponibilidad" , "Anticipacion" , "IDDisponibilidad = '" . $r_disponibilidad["IDDisponibilidad"] . "'" );
								switch($medicion_tiempo):
									case "Dias":	
										$minutos_anticipacion = (60*24) * $valor_anticipacion;
									break;
									case "Horas":
										$minutos_anticipacion = 60 * $valor_anticipacion;
									break;
									case "Minutos":
										$minutos_anticipacion = $valor_anticipacion;
									break;
								endswitch;
								
										
							
							//$hora_inicio_reserva = strtotime ($fecha_validar . " " . $r_disponibilidad["HoraDesde"]);	
							//$fechahora_actual =  strtotime ( '-'.$minutos_anticipacion.' minute' , strtotime ( date("Y-m-d H:i:s") ) ) ;
							$hora_inicio_reserva = strtotime ('-'.$minutos_anticipacion.' minute' , strtotime ( $fecha_validar . " " . $r_disponibilidad["HoraDesde"]));
							$fechahora_actual =  strtotime ( date("Y-m-d H:i:s") ) ;
							
							
							
							if ($fecha_validar=="2016-03-18")	:
								//echo  date ( 'Y-m-d H:i:s' , $hora_inicio_reserva );
								//echo  "<" .date ( 'Y-m-d H:i:s' , $fechahora_actual );
								//exit;
							endif;
								
							
							if ($total_disponibilidades<=0){
								$fecha_reservar = "nodisponible";
								$hora_reservar = "nodisponible";
								$TiempoRestanteDias = "nodisponible";
								$TiempoRestanteHoras = "nodisponible";
								$TiempoRestanteMinutos = "nodisponible";
								$TiempoRestanteSegundos = "nodisponible";
							}
							elseif($hora_inicio_reserva<$fechahora_actual){
								$activo = "S";
								$fecha_reservar = $fecha_validar;
								$hora_reservar = $r_disponibilidad["HoraDesde"];
								$TiempoRestanteDias = 0;
								$TiempoRestanteHoras = 0;
								$TiempoRestanteMinutos = 0;
								$TiempoRestanteSegundos = 0;
							}
							else{	
										
									$fecha_para_reservar = strtotime ( '-'.($minutos_anticipacion).' minute' , strtotime ( $fecha_validar ) ) ;
									$fecha_reservar = date ( 'Y-m-j' , $fecha_para_reservar );
									
									$dia_semana_reservar= date('N', strtotime($fecha_reservar));
									
									
									
									$activo = "N";								
									$hora_reservar = $r_disponibilidad["HoraDesde"];
									//Calculo tiempo restante para poder reservar
									$fecha_final = $fecha_reservar . " " . $hora_inicio_reserva;
									$fecha_actual = date("Y-m-d H:i:s");    
									//$diff = strtotime($fechahora_actual) - strtotime($hora_inicio_reserva);
									$diff =  $hora_inicio_reserva - $fechahora_actual;
									$dias = $diff/(60*60*24);
									$horas = ($dias-intval($dias))*24;
									$min = ($horas-intval($horas))*60;
									$seg = ($min-intval($min))*60;
									if ($fecha_validar=="2016-03-18")	:
										//echo $tiempo_restante = "Quedan ".intval($dias)." dias ".intval($horas)."  horas ".intval($min)." minutos ".intval($seg)." segundos";
										//exit;
									endif;
									
									$fecha_reservar = date ( 'Y-m-d' , $hora_inicio_reserva );
									$hora_reservar = date ( 'H:i:s' , $hora_inicio_reserva );
									
									// si la fecha es pasada la marco como no disponible
									if ($dias<0 || $horas<0 || $min<0):
										$activo = "N";
										$fecha_reservar = "nodisponible";
										$hora_reservar = "nodisponible";
										$TiempoRestanteDias = "nodisponible";
										$TiempoRestanteHoras = "nodisponible";
										$TiempoRestanteMinutos = "nodisponible";
										$TiempoRestanteSegundos = "nodisponible";
									else:										
										$TiempoRestanteDias = intval($dias);
										$TiempoRestanteHoras = intval($horas);
										$TiempoRestanteMinutos = intval($min);
										$TiempoRestanteSegundos = intval($seg);
										
									endif;						
								
							}
							
							
							
							
							$servicio_fecha["Fecha"] = $fecha_validar;
							$servicio_fecha["Activo"] = $activo;
							$servicio_fecha["FechaReservar"] = $fecha_reservar;
							$servicio_fecha["HoraReservar"] = $hora_reservar;
							$zonahoraria = date_default_timezone_get();
							$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );																						
							$servicio_fecha["GMT"] = SIMWebservice::timezone_offset_string( $offset );
							$servicio_fecha["TiempoRestanteDias"] = $TiempoRestanteDias;
							$servicio_fecha["TiempoRestanteHoras"] = $TiempoRestanteHoras;
							$servicio_fecha["TiempoRestanteMinutos"] = $TiempoRestanteMinutos;
							$servicio_fecha["TiempoRestanteSegundos"] = $TiempoRestanteSegundos;
							
							// si la fecha no esta isponible no la envio o si el servicio esta marcado solo para mostrar las disponibles
							if($fecha_reservar!="nodisponible"):
								if( ($r["SoloFechaDisponible"]=="S" && $activo=="S") || $r["SoloFechaDisponible"]!="S"){
									array_push($response_fechas, $servicio_fecha);
								}
							endif;
								
							$contador++;
							$primera_fecha++;
						 endif;
							
						}
						
						
						$servicio["Fechas"] = $response_fechas;
								
						array_push($response, $servicio);
		
					}//ednw hile
						$respuesta["message"] = $message;
						$respuesta["success"] = true;
						$respuesta["response"] = $response;	
				}//End if
				else
				{
						$respuesta["message"] = "No se encontraron registros";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;	
				}//end else		
		}
		else{
			$respuesta["message"] = "4. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
		
		//inserta _log				
		//$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('get_fecha_disponibilidad_servicio','IDClub: ".$IDClub. " IDServicio:" . $IDServicio . " . Fecha: ".date("Y-m-d")."','".json_encode($respuesta)."')");
		
			
		return $respuesta;	
	
	
}



function get_disponibilidad_fecha($IDClub,$IDCampo,$Fecha,$Hora){
	$dbo =& SIMDB::get();
	
		
		$response = array();
		$sql = "SELECT * FROM ReservaGeneral WHERE IDServicioElemento = '".$IDCampo."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and (IDEstadoReserva = 1 or IDEstadoReserva=3)  ORDER BY Hora ";
		$qry = $dbo->query( $sql );
			
			$servicio_hora["IDClub"] = $IDClub;
			$servicio_hora["IDCampo"] = $IDCampo;
			$servicio_hora["Fecha"] = $Fecha;
			$servicio_hora["Hora"] = $Hora;
			
			//Si existen dos reservas es que estan reservados los dos tee de lo contrario no esta reservados o solo un tee esta reservado
			if( $dbo->rows( $qry ) > 1 ):
				//Ya esta reservado
				$servicio_hora["Disponibilidad"] = "No";
				$message = " No Disponible";
			else:
				//No esta reservado devuelkque diponible en si	
				$servicio_hora["Disponibilidad"] = "Si";
				$message = "Disponible";
			endif;
			
			array_push($response, $servicio_hora);
			
			
			
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		
			
		return $respuesta;	
	
}

function get_disponiblidad_fecha_hora($IDClub,$IDServicio,$Fecha,$Hora){
	$dbo =& SIMDB::get();
	
		$Hora = SIMWebService::validar_formato_hora($Hora);
		
			$array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub,$IDServicio); 			
			foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha):
				if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"]=="S"):
					$fecha_disponible = 1;							
				endif;
			endforeach;
			
			if ($fecha_disponible==0):
				$respuesta["message"] = "Esta fecha aún no está disponible..";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				return $respuesta;
			endif;			

		
		
		$response = array();
		$sql = "SELECT * FROM ServicioElemento WHERE IDServicio = '".$IDServicio."'   ORDER BY IDServicio ";
		$qry = $dbo->query( $sql );
			
			$servicio_hora["IDClub"] = $IDClub;
			$servicio_hora["IDServicio"] = $IDServicio;
			$servicio_hora["Fecha"] = $Fecha;
			$servicio_hora["Hora"] = $Hora;
			$zonahoraria = date_default_timezone_get();
			$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );											
			$servicio_hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
			
			$sql = "SELECT * FROM ServicioElemento WHERE IDServicio = '".$IDServicio."'   ORDER BY Nombre ";
			$qry = $dbo->query( $sql );
			$response_elemento = array();
			while ($row_elemento = $dbo->fetchArray($qry)):
			
				//Verifico si tiene disponibilidad  general el elemento				
				$dia_fecha= date('w', strtotime($Fecha));
				$sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and ('".$Hora."' >= HoraDesde and '".$Hora."'<=HoraHasta) and IDServicioElemento like '%".$row_elemento[IDServicioElemento]."|%'  Order by HoraDesde";											
				$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
				if( $dbo->rows( $qry_dispo_elemento_gral ) > 0 ):
					$elemento[IDElemento] = $row_elemento[IDServicioElemento];
					$elemento[Nombre] = $row_elemento[Nombre];
					//verifico disponibilidad						
					$sql_reserva = "SELECT * FROM ReservaGeneral WHERE IDClub = '".$IDClub."' and IDServicioElemento = '".$row_elemento[IDServicioElemento]."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and (IDEstadoReserva = 1 or IDEstadoReserva=3)  ORDER BY Hora ";	
					$qry_reserva = $dbo->query($sql_reserva);
					if( $dbo->rows( $qry_reserva ) >= 1 ):
						$row_datos_reserva = $dbo->fetchArray($qry_reserva);
						$elemento[Disponible] = "N";
						$elemento["Socio"] = utf8_encode( $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $row_datos_reserva["IDSocio"] . "'" ) );
						$elemento["IDSocio"] = $row_datos_reserva["IDSocio"] ;
						$elemento["ModalidadEsquiSocio"] = $dbo->getFields( "TipoModalidadEsqui" , "Nombre" , "IDTipoModalidadEsqui = '" . $row_datos_reserva["IDTipoModalidadEsqui"] . "'" );
					else:
						$elemento[Disponible] = "S";
						$elemento["Socio"] = "";
						$elemento["IDSocio"] = "";
						$elemento["ModalidadEsquiSocio"] = "";
					endif;
					array_push($response_elemento, $elemento);
				endif;	
			endwhile;
			
			
			$servicio_hora["Disponibilidad"] = $response_elemento;
			
			
				$message = " Disponibilidad";
				
				array_push($response, $servicio_hora);
			
			
			
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		
			
		return $respuesta;	
	
}


function validar_permiso_reserva($IDSocio){	
	$dbo =& SIMDB::get();
	$permiso_reserva = "S";
	$sql_socio = "Select PermiteReservar From Socio Where IDSocio = '".$IDSocio."' limit 1";
	$result_socio = $dbo->query($sql_socio);
	$row_socio = $dbo->fetchArray($result_socio);
	if($row_socio["PermiteReservar"]=="N"):
		$permiso_reserva = "N";		
	endif;
	return $permiso_reserva;
}


function get_disponibilidad_campo($IDClub,$IDCampo,$Fecha,$IDServicio="",$Admin="",$NumeroTurnos=""){
	$dbo =& SIMDB::get();
	
		//Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
		$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $IDServicio . "'" );
		if($id_servicio_maestro==15 || $id_servicio_maestro==30 || $id_servicio_maestro==27 || $id_servicio_maestro==28): // Golf con opcion de escoger grupos para reservas turnos seguidos	
			$respuesta = SIMWebService::get_disponibilidad_campo_turno_seguido($IDClub,$IDCampo,$Fecha,$IDServicio,$Admin,$NumeroTurnos);
			return $respuesta;
		endif;
		
	
	
	
		$fecha_disponible = 0;
		
		$verifica_disponibilidad_especifica=0;
		$verifica_disponibilidad_general=0;
		
		
		
		
		//consulto los datos del servicio
		//$IDServicio = $dbo->getFields( "ServicioElemento" , "IDServicio" , "IDServicioElemento = '" . $IDCampo . "'" );
		
		
		
		// Verifico que el club y servicio este disponible en la fecha consultada
		$verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio);  
		if(!empty($verificacion)):
			$respuesta["message"] = $verificacion;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
			return $respuesta;
		endif;
		
		
		//Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
			$array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub,$IDServicio); 			
			foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha):
				if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"]=="S"):
					$fecha_disponible = 1;							
				endif;
			endforeach;
			
			if ($fecha_disponible==0 && empty($Admin)):
				$respuesta["message"] = "Esta fecha aún no está disponible.";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				return $respuesta;
			endif;			


		
	
		
		$response = array();
		$response_disponibilidades = array();
		$sql = "SELECT * FROM Servicio WHERE IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			$servicio_hora["IDClub"] = $IDClub;
			$servicio_hora["IDServicio"] = $IDServicio;
			//$servicio_hora["IDCampo"] = $IDCampo;
			$servicio_hora["Fecha"] = $Fecha;
			//$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,"",$IDServicio,$Fecha);				
			
			//Horas Disponibles Elemento
			$response_disponibilidad = array();
			
			
			
			
			
			if (!empty($IDCampo))
				$condicion_elemento = " and IDServicioElemento = '".$IDCampo."'";
			
			 $r = $dbo->fetchArray( $qry );
		
	
			$sql_elementos_servicio = "Select * From ServicioElemento Where IDServicio = '".$IDServicio."' " . $condicion_elemento;
			$result_elementos_servicio = $dbo->query($sql_elementos_servicio);
			$response_disponibilidad_tee= array();
			while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)):
			
				
			
				unset($array_hora_reservada);
				$IDElemento = $r_elementos_servicio["IDServicioElemento"];
				
				
				//Consulto lo que tiene reservado el elemento en la fecha indicada en tee1
				 $sql_reserva_elemento_tee1 = "SELECT ReservaGeneral.*, Socio.Nombre, Socio.Apellido, CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio FROM ReservaGeneral, Socio WHERE IDServicioElemento = '".$IDElemento."' and Fecha = '".$Fecha."' and Tee = 'Tee1' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";
				
				$qry_reserva_elemento_tee1 = $dbo->query( $sql_reserva_elemento_tee1 );
				while($row_reserva_elemento_tee1 = $dbo->fetchArray($qry_reserva_elemento_tee1)){
					$array_hora_reservada_tee1[$IDElemento][] = $row_reserva_elemento_tee1["Hora"];	
					$array_socio[ $row_reserva_elemento_tee1["Hora"] ] = $row_reserva_elemento_tee1;
					if($row_reserva_elemento_tee1["IDReservaGrupos"]>0):
						$array_socio[ $row_reserva_elemento_tee1["Hora"] ]["Tee1"]["NombreSocio"] = utf8_encode( $dbo->getFields( "ReservaGrupos" , "Nombre" , "IDReservaGrupos = '" . $row_reserva_elemento_tee1["IDReservaGrupos"] . "'" ) );
					else:
						$array_socio[ $row_reserva_elemento_tee1["Hora"] ]["Tee1"]["NombreSocio"] = utf8_encode( $row_reserva_elemento_tee1["Nombre"]) . " " . utf8_encode( $row_reserva_elemento_tee1["Apellido"] );
					endif;
					$array_socio[ $row_reserva_elemento_tee1["Hora"] ]["IDSocio"] = $row_reserva_elemento_tee1["IDSocio"];
					$array_socio[ $row_reserva_elemento_tee1["Hora"] ]["IDReservaGeneral"] = $row_reserva_elemento_tee1["IDReservaGeneral"];					
				}
				
				//print_r($array_socio["06:00:00"]["Tee1"]["NombreSocio"]);
				
				//Consulto lo que tiene reservado el elemento en la fecha indicada en tee10
				$sql_reserva_elemento_tee10 = "SELECT ReservaGeneral.*, Socio.Nombre, Socio.Apellido, CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio FROM ReservaGeneral, Socio WHERE IDServicioElemento = '".$IDElemento."' and Fecha = '".$Fecha."' and Tee = 'Tee10' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";
				$qry_reserva_elemento_tee10 = $dbo->query( $sql_reserva_elemento_tee10 );
				while($row_reserva_elemento_tee10 = $dbo->fetchArray($qry_reserva_elemento_tee10)){
					$array_hora_reservada_tee10[$IDElemento][] = $row_reserva_elemento_tee10["Hora"];	
					$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ] = $row_reserva_elemento_tee10;
					if($row_reserva_elemento_tee10["IDReservaGrupos"]>0):
						$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ]["Tee10"]["NombreSocio"] = utf8_encode( $dbo->getFields( "ReservaGrupos" , "Nombre" , "IDReservaGrupos = '" . $row_reserva_elemento_tee10["IDReservaGrupos"] . "'" ) );
					else:
						$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ]["Tee10"]["NombreSocio"] = utf8_encode( $row_reserva_elemento_tee10["Nombre"]) . " " . utf8_encode( $row_reserva_elemento_tee10["Apellido"]);
					endif;
					$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ]["IDSocio"] = $row_reserva_elemento_tee10["IDSocio"];
					$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ]["IDReservaGeneral"] = $row_reserva_elemento_tee10["IDReservaGeneral"];					
				}
				
				
			
				//Horas generales del servicio
				/*
				$horaInicial=$r["HoraDesde"];
				$minutoAnadir=$r["IntervaloHora"];
				$hora_final = strtotime( $r["HoraHasta"] );
				$hora_actual = $r["HoraDesde"];
				*/
				
				$dia_fecha= date('w', strtotime($Fecha));
				
				
					// Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
					$sql_dispo_elemento_primera = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%' Order by HoraDesde Limit 1";
					$qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
					$row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
					//$horaInicial=$row_dispo_elemento_primera["HoraDesde"];
					$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_primera["HoraDesde"];
					
					
					for($i=1;$i<=2;$i++):
					
					$verifica_abierto_servicio = SIMWebservice::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$IDElemento);
					if(empty($verifica_abierto_servicio)){
					
					
					
					
					//Verifico si tene disponibilidad  general el elemento				
					$sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%'";
					$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
					if( $dbo->rows( $qry_dispo_elemento_gral ) > 0 ){	
					
							
							
							
							
							$verifica_disponibilidad_general = 1;	
							while($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)){								
								
								
								
								$horaInicial=$row_dispo_elemento_gral["HoraDesde"];
								//$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_gral["HoraDesde"];
								$minutoAnadir=$dbo->getFields( "Disponibilidad" , "Intervalo" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
								
								// Si la fecha es el dia actual verifico el tiempo de anticipacion para reservar
								if ($Fecha==date("Y-m-d")):								
									$medicion_tiempo = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacion" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
									$valor_tiempo_anticipacion = (int)$dbo->getFields( "Disponibilidad" , "Anticipacion" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
									if($medicion_tiempo=="Horas"):
										$valor_tiempo_anticipacion = $valor_tiempo_anticipacion * 60; // Lo convierto a minutos
									elseif($medicion_tiempo=="Dias"):
									 	$valor_tiempo_anticipacion = 0;
									endif;	
								else:
										$valor_tiempo_anticipacion = 0;	
								endif;
								
								
								//Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
									$medicion_tiempo_anticipacion = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacionTurno" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"]  . "'" );
									$valor_anticipacion_turno = (int)$dbo->getFields( "Disponibilidad" , "AnticipacionTurno" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"]  . "'" );
									switch($medicion_tiempo_anticipacion):
										case "Dias":	
											$minutos_anticipacion_turno = (60*24) * $valor_anticipacion_turno;
										break;
										case "Horas":
											$minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
										break;
										case "Minutos":
											$minutos_anticipacion_turno = $valor_anticipacion_turno;
										break;
										default:
											$minutos_anticipacion_turno = 0;
									endswitch;
									
								
								
								//Si es administrador no tiene limite de anticipacion
								if($Admin=="S"){
									$valor_tiempo_anticipacion = 0;	
									$minutos_anticipacion_turno = 0;
								}
								
								
								
								
								$hora_real = date('Y-m-d H:i:s');									
								$hora_empezar_reserva = strtotime ( '-'.$valor_tiempo_anticipacion.' minute' , strtotime ( $horaInicial_reserva ) ) ;
								//$hora_empezar_reserva = date ( 'H:i:s' , $hora_empezar_reserva );
								$hora_actual_sistema = strtotime( $hora_real );
								
								
								
								
								$hora_final = strtotime( $row_dispo_elemento_gral["HoraHasta"] );
								$hora_actual = strtotime( $row_dispo_elemento_gral["HoraDesde"] );
								
								while($hora_actual<=$hora_final):
								
								$hora_fecha_actual =  $Fecha . " " .date ( 'H:i:s',$hora_actual);								
								$hora_puede_reservar = strtotime ( '+'.$minutos_anticipacion_turno.' minute' , strtotime ( $hora_real ) ) ;
								/*****************************************************************************************************
								Valido que solo devuelva las fecha/hora mayor a la actual, esto por si le cambian la hora al celular
								Valido que ésta hora este disponible para reervar en el tiempo limite deacuerdo al parametro AnticipacionTurno
								******************************************************************************************************/
								if(strtotime($hora_fecha_actual) >= strtotime($hora_real) &&  $hora_puede_reservar <= strtotime($hora_fecha_actual)):
									
									//Verifico si el tee esta disponible en este horario para mostrarlo
									if( ($row_dispo_elemento_gral["Tee1"]=="S" && $i==1) || ( $row_dispo_elemento_gral["Tee10"]=="S" && $i==2) ):
									
								
											if(strlen($horaInicial)!=8):
												$horaInicial .= ":00";
											endif;	
											
											$hora["Hora"] = $horaInicial;
											$zonahoraria = date_default_timezone_get();
											$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );											
											$hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
											
											
											
											//echo "<br>" . date ( 'Y-m-d H:i:s' , $hora_puede_reservar );
											//exit;
			
											
											
											$datos_disponibilidad = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array" );
											
											// Si el tee es 1
											if($i==1):
													//Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
													if ( (in_array($horaInicial,$array_hora_reservada_tee1[$IDElemento])) || ($hora_actual_sistema<$hora_empezar_reserva && $valor_tiempo_anticipacion >0)  )
													{	
														$hora["Disponible"] = "N";	
																										
														$hora["Socio"] = $array_socio["$horaInicial"]["Tee1"]["NombreSocio"];
														$hora["IDSocio"] = $array_socio["$horaInicial"]["IDSocio"];
														$hora["IDReserva"] = $array_socio["$horaInicial"]["IDReservaGeneral"];														
													}
													else
													{
														$hora["Disponible"] = "S";
														$hora["Socio"] = "";
														$hora["IDSocio"] = "";
														$hora["IDReserva"] = "";
													}
											endif;
											
											// Si el tee es 10
											if($i==2):
													//Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
													if ( (in_array($horaInicial,$array_hora_reservada_tee10[$IDElemento])) || ($hora_actual_sistema<$hora_empezar_reserva && $valor_tiempo_anticipacion >0)  )
													{
														$hora["Disponible"] = "N";
														$hora["Socio"] = $array_socio_tee10["$horaInicial"]["NombreSocio"];
														$hora["Socio"] = $array_socio_tee10["$horaInicial"]["Tee10"]["NombreSocio"];
														$hora["IDSocio"] = $array_socio_tee10["$horaInicial"]["IDSocio"];
														$hora["IDReserva"] = $array_socio_tee10["$horaInicial"]["IDReservaGeneral"];	
													}
													else
													{
														$hora["Disponible"] = "S";
														$hora["Socio"] = "";
														$hora["IDSocio"] = "";
														$hora["IDReserva"] = "";	
													}
											endif;
											
											
													
											
											$hora["IDCampo"] = $IDCampo;	
											
											if($i==1):												
												$hora["Tee"] = "Tee1";	
											else:
												$hora["Tee"] = "Tee10";	
											endif;
											
											
											$hora["MaximoPersonaTurno"] = $datos_disponibilidad["MaximoPersonaTurno"];		
											$hora["NumeroInvitadoClub"] = $datos_disponibilidad["NumeroInvitadoClub"];		
											$hora["NumeroInvitadoExterno"] = $datos_disponibilidad["NumeroInvitadoExterno"];	
											
											
											//Repeticion reserva
											$hora["IDDisponibilidad"] = $datos_disponibilidad["IDDisponibilidad"];
											$hora["PermiteRepeticion"] = $datos_disponibilidad["PermiteRepeticion"];	
											$hora["MaximoRepeticion"] = $datos_disponibilidad["NumeroRepeticion"] . " " . $datos_disponibilidad["MedicionRepeticion"];	


											$hora["IDElemento"] = $IDElemento;		
											$hora["NombreElemento"] = $hora["Tee"]."-".$dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $IDElemento . "'" );
											
											array_push($response_disponibilidad_tee, $hora);
									endif;		
								endif;
																
								$array_horas_elemento[] = $horaInicial;								
								$segundos_horaInicial=strtotime($horaInicial);
								$segundos_minutoAnadir=$minutoAnadir*60;
								$array_horas=$nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
								$hora_actual = strtotime( $nuevaHora );
								$horaInicial=$nuevaHora;
											
								
												
								endwhile;
								
										
										
							}		
					}
					
				}
					endfor;	
		
			endwhile;
			
			
			
			
			//
			
			foreach($response_disponibilidad_tee as $id_array => $datos_array):				
				$array_ordenado_hora[$datos_array["Hora"].$datos_array["NombreElemento"]] = $datos_array;
 			endforeach;
			
			ksort($array_ordenado_hora);
			
			$response_array_ordenado = array();
			foreach($array_ordenado_hora as $id_array => $datos_array):			
				array_push($response_array_ordenado, $datos_array);			
 			endforeach;
			
			array_push($response_disponibilidades, $response_array_ordenado);
			
			// Si $UnElemento no es vacio no ordeno el array ya que se consulto un solo elemnto de los contrario ordeno todos los elemnetos buscados
			if(!empty($UnElemento)):			
				$servicio_hora["Disponibilidad"] = $response_array_ordenado;
			else:
				$servicio_hora["Disponibilidad"] = $response_disponibilidades;	
			endif;
			//$servicio_hora["Disponibilidad"] = $response_disponibilidades;
			array_push($response, $servicio_hora);
			
			
			
			
			
			
		
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;	
			
		  
			
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
		
		
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('get_disponiblidad_elemento_servicio','IDClub: ".$IDClub. " IDServicio:" . $IDServicio . " . Fecha: ".$Fecha." Elemento: ".$IDElemento."','".json_encode($respuesta)."')");
			
		return $respuesta;	
	
	
}


function get_disponibilidad_campo_turno_seguido($IDClub,$IDCampo,$Fecha,$IDServicio="",$Admin="",$NumeroTurnos=""){
	$dbo =& SIMDB::get();
	
	
	if(empty($NumeroTurnos))
		$NumeroTurnos = 1;
	
	
	
		$fecha_disponible = 0;
		
		$verifica_disponibilidad_especifica=0;
		$verifica_disponibilidad_general=0;
		
		
		
		
		//consulto los datos del servicio
		//$IDServicio = $dbo->getFields( "ServicioElemento" , "IDServicio" , "IDServicioElemento = '" . $IDCampo . "'" );
		
		
		
		// Verifico que el club y servicio este disponible en la fecha consultada
		$verificacion = SIMWebService::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio);  
		if(!empty($verificacion)):
			$respuesta["message"] = $verificacion;
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
			return $respuesta;
		endif;
		
		
		//Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
			$array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub,$IDServicio); 			
			foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha):
				if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"]=="S"):
					$fecha_disponible = 1;							
				endif;
			endforeach;
			
			if ($fecha_disponible==0 && empty($Admin)):
				$respuesta["message"] = "Esta fecha aún no está disponible";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				return $respuesta;
			endif;			


		
	
		
		$response = array();
		$response_disponibilidades = array();
		$sql = "SELECT * FROM Servicio WHERE IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' ORDER BY Nombre ";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			$servicio_hora["IDClub"] = $IDClub;
			$servicio_hora["IDServicio"] = $IDServicio;
			//$servicio_hora["IDCampo"] = $IDCampo;
			$servicio_hora["Fecha"] = $Fecha;
			//$response_disponibilidad = SIMWebService::consultar_disponibilidad($qry,"",$IDServicio,$Fecha);				
			
			//Horas Disponibles Elemento
			$response_disponibilidad = array();
			
			
			
			
			
			if (!empty($IDCampo))
				$condicion_elemento = " and IDServicioElemento = '".$IDCampo."'";
			
			 $r = $dbo->fetchArray( $qry );
		
	
			$sql_elementos_servicio = "Select * From ServicioElemento Where IDServicio = '".$IDServicio."' " . $condicion_elemento;
			$result_elementos_servicio = $dbo->query($sql_elementos_servicio);
			$response_disponibilidad_tee= array();
			while ($r_elementos_servicio = $dbo->FetchArray($result_elementos_servicio)):
			
				
			
				unset($array_hora_reservada);
				$IDElemento = $r_elementos_servicio["IDServicioElemento"];
				
				
				//Consulto lo que tiene reservado el elemento en la fecha indicada en tee1
				 $sql_reserva_elemento_tee1 = "SELECT ReservaGeneral.*, Socio.Nombre, Socio.Apellido, CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio FROM ReservaGeneral, Socio WHERE IDServicioElemento = '".$IDElemento."' and Fecha = '".$Fecha."' and Tee = 'Tee1' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";
				
				$qry_reserva_elemento_tee1 = $dbo->query( $sql_reserva_elemento_tee1 );
				while($row_reserva_elemento_tee1 = $dbo->fetchArray($qry_reserva_elemento_tee1)){
					$array_hora_reservada_tee1[$IDElemento][] = $row_reserva_elemento_tee1["Hora"];	
					$array_socio[ $row_reserva_elemento_tee1["Hora"] ] = $row_reserva_elemento_tee1;
					$array_socio[ $row_reserva_elemento_tee1["Hora"] ]["Tee1"]["NombreSocio"] = utf8_encode( $row_reserva_elemento_tee1["Nombre"]) . " " . utf8_encode( $row_reserva_elemento_tee1["Apellido"] );
					$array_socio[ $row_reserva_elemento_tee1["Hora"] ]["IDSocio"] = $row_reserva_elemento_tee1["IDSocio"];
					$array_socio[ $row_reserva_elemento_tee1["Hora"] ]["IDReservaGeneral"] = $row_reserva_elemento_tee1["IDReservaGeneral"];					
				}
				
				
				//print_r($array_socio["06:00:00"]["Tee1"]["NombreSocio"]);
				
				//Consulto lo que tiene reservado el elemento en la fecha indicada en tee10
				$sql_reserva_elemento_tee10 = "SELECT ReservaGeneral.*, Socio.Nombre, Socio.Apellido, CONCAT(Socio.Nombre, ' ', Socio.Apellido ) as Socio FROM ReservaGeneral, Socio WHERE IDServicioElemento = '".$IDElemento."' and Fecha = '".$Fecha."' and Tee = 'Tee10' and  (IDEstadoReserva = 1 or IDEstadoReserva=3) AND Socio.IDSocio = ReservaGeneral.IDSocio AND Socio.IDClub = ReservaGeneral.IDClub  ORDER BY Hora ";
				$qry_reserva_elemento_tee10 = $dbo->query( $sql_reserva_elemento_tee10 );
				while($row_reserva_elemento_tee10 = $dbo->fetchArray($qry_reserva_elemento_tee10)){
					$array_hora_reservada_tee10[$IDElemento][] = $row_reserva_elemento_tee10["Hora"];	
					$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ] = $row_reserva_elemento_tee10;
					$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ]["Tee10"]["NombreSocio"] = utf8_encode( $row_reserva_elemento_tee10["Nombre"]) . " " . utf8_encode( $row_reserva_elemento_tee10["Apellido"]);
					$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ]["IDSocio"] = $row_reserva_elemento_tee10["IDSocio"];
					$array_socio_tee10[ $row_reserva_elemento_tee10["Hora"] ]["IDReservaGeneral"] = $row_reserva_elemento_tee10["IDReservaGeneral"];					
				}
				
				
			
				//Horas generales del servicio
				/*
				$horaInicial=$r["HoraDesde"];
				$minutoAnadir=$r["IntervaloHora"];
				$hora_final = strtotime( $r["HoraHasta"] );
				$hora_actual = $r["HoraDesde"];
				*/
				
				$dia_fecha= date('w', strtotime($Fecha));
				
				
					// Consulto la primer hora disponible del dia para este elemnto para calcular desde cuando se puede reservar
					$sql_dispo_elemento_primera = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%' Order by HoraDesde Limit 1";
					$qry_dispo_elemento_primera = $dbo->query($sql_dispo_elemento_primera);
					$row_dispo_elemento_primera = $dbo->fetchArray($qry_dispo_elemento_primera);
					//$horaInicial=$row_dispo_elemento_primera["HoraDesde"];
					$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_primera["HoraDesde"];
					
					
					
					for($i=1;$i<=2;$i++):
					
					$verifica_abierto_servicio = SIMWebservice::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$IDElemento);
					if(empty($verifica_abierto_servicio)){
					
					
					
					
					//Verifico si tene disponibilidad  general el elemento				
					$sql_dispo_elemento_gral = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%'";
					$qry_dispo_elemento_gral = $dbo->query($sql_dispo_elemento_gral);
					if( $dbo->rows( $qry_dispo_elemento_gral ) > 0 ){	
					
							
						
							
							
							$verifica_disponibilidad_general = 1;	
							while($row_dispo_elemento_gral = $dbo->fetchArray($qry_dispo_elemento_gral)){								
								
								
								
								$horaInicial=$row_dispo_elemento_gral["HoraDesde"];
								//$horaInicial_reserva=$Fecha . " " .$row_dispo_elemento_gral["HoraDesde"];
								$minutoAnadir=$dbo->getFields( "Disponibilidad" , "Intervalo" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
								
								// Si la fecha es el dia actual verifico el tiempo de anticipacion para reservar
								if ($Fecha==date("Y-m-d")):								
									$medicion_tiempo = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacion" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
									$valor_tiempo_anticipacion = (int)$dbo->getFields( "Disponibilidad" , "Anticipacion" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "'" );
									if($medicion_tiempo=="Horas"):
										$valor_tiempo_anticipacion = $valor_tiempo_anticipacion * 60; // Lo convierto a minutos
									elseif($medicion_tiempo=="Dias"):
									 	$valor_tiempo_anticipacion = 0;
									endif;	
								else:
										$valor_tiempo_anticipacion = 0;	
								endif;
								
								
								//Valido que la siguiente hora se pueda reservar dependiendo el parametro de AnticipacionTurno
									$medicion_tiempo_anticipacion = $dbo->getFields( "Disponibilidad" , "MedicionTiempoAnticipacionTurno" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"]  . "'" );
									$valor_anticipacion_turno = (int)$dbo->getFields( "Disponibilidad" , "AnticipacionTurno" , "IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"]  . "'" );
									switch($medicion_tiempo_anticipacion):
										case "Dias":	
											$minutos_anticipacion_turno = (60*24) * $valor_anticipacion_turno;
										break;
										case "Horas":
											$minutos_anticipacion_turno = 60 * $valor_anticipacion_turno;
										break;
										case "Minutos":
											$minutos_anticipacion_turno = $valor_anticipacion_turno;
										break;
										default:
											$minutos_anticipacion_turno = 0;
									endswitch;
									
								
								
								//Si es administrador no tiene limite de anticipacion
								if($Admin=="S"){
									$valor_tiempo_anticipacion = 0;	
									$minutos_anticipacion_turno = 0;
								}
								
								
								
								//Consulto hace una hora para mostrar los turnos anterior segun solicitud de lagartos								
								/*
								$hace_una_hora = strtotime ( '-1 hour' , strtotime ( date("Y-m-d H:i:s") ) ) ;								
								if($Fecha==date("Y-m-d")):
									$hora_real = date('Y-m-d H:i:s',$hace_una_hora);	
								else:
									$hora_real = date('Y-m-d H:i:s');	
								endif;
								*/
								
								$hora_real = date('Y-m-d H:i:s');									
								
								$hora_empezar_reserva = strtotime ( '-'.$valor_tiempo_anticipacion.' minute' , strtotime ( $horaInicial_reserva ) ) ;
								//$hora_empezar_reserva = date ( 'H:i:s' , $hora_empezar_reserva );
								$hora_actual_sistema = strtotime( $hora_real );
								
								
								
								
								$hora_final = strtotime( $row_dispo_elemento_gral["HoraHasta"] );
								$hora_actual = strtotime( $row_dispo_elemento_gral["HoraDesde"] );
								
								while($hora_actual<=$hora_final):
								
								$hora_fecha_actual =  $Fecha . " " .date ( 'H:i:s',$hora_actual);								
								$hora_puede_reservar = strtotime ( '+'.$minutos_anticipacion_turno.' minute' , strtotime ( $hora_real ) ) ;
								/*****************************************************************************************************
								Valido que solo devuelva las fecha/hora mayor a la actual, esto por si le cambian la hora al celular
								Valido que ésta hora este disponible para reervar en el tiempo limite deacuerdo al parametro AnticipacionTurno
								******************************************************************************************************/
								if(strtotime($hora_fecha_actual) >= strtotime($hora_real) &&  $hora_puede_reservar <= strtotime($hora_fecha_actual)):
									
									//Verifico si el tee esta disponible en este horario para mostrarlo
									if( ($row_dispo_elemento_gral["Tee1"]=="S" && $i==1) || ( $row_dispo_elemento_gral["Tee10"]=="S" && $i==2) ):
									
								
											if(strlen($horaInicial)!=8):
												$horaInicial .= ":00";
											endif;	
											
											$hora["Hora"] = $horaInicial;
											$zonahoraria = date_default_timezone_get();
											$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );											
											$hora["GMT"] = SIMWebservice::timezone_offset_string( $offset );
											
											
											
											//echo "<br>" . date ( 'Y-m-d H:i:s' , $hora_puede_reservar );
											//exit;
			
											
											
											$datos_disponibilidad = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array" );
											
											// Si el tee es 1
											if($i==1):
													//Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
													if ( (in_array($horaInicial,$array_hora_reservada_tee1[$IDElemento])) || ($hora_actual_sistema<$hora_empezar_reserva && $valor_tiempo_anticipacion >0)  )
													{	
														$hora["Disponible"] = "N";														
														$hora["Socio"] = $array_socio["$horaInicial"]["Tee1"]["NombreSocio"];
														$hora["IDSocio"] = $array_socio["$horaInicial"]["IDSocio"];
														$hora["IDReserva"] = $array_socio["$horaInicial"]["IDReservaGeneral"];														
													}
													else
													{	
													
														//Verifico que no tenga fecha de cierre en esta hora 
														$verifica_abierto_servicio_hora = SIMWebservice::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$IDElemento,$hora["Hora"]);
														if(!empty($verifica_abierto_servicio_hora)):
																//extraigo la razon
																$mensaje_cierre = explode(":",$verifica_abierto_servicio_hora); 
																
																$hora["Disponible"] = "N";														
																$hora["Socio"] = $mensaje_cierre[2];
																$hora["IDSocio"] = "";
																$hora["IDReserva"] = "";
														else:	
															
															//if(strtotime($hora_fecha_actual) >= strtotime(date("Y-m-d H:i:s"))):
																	if($NumeroTurnos==1):
																		$hora["Disponible"] = "S";
																		$hora["Socio"] = "";
																		$hora["IDSocio"] = "";
																		$hora["IDReserva"] = "";
																	else:
																		//verifico si es posible reservar en esta hora cuando el turno sea mas de 1, valido si los siguientes turnos estan disponible
																		$array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf($Fecha, $hora["Hora"], $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, "Tee1");
																		
																		if(count($array_disponible)==$NumeroTurnos):
																			$hora["Disponible"] = "S";
																			$hora["Socio"] = "";
																			$hora["IDSocio"] = "";
																			$hora["IDReserva"] = "";
																		else:
																			$hora["Disponible"] = "N";														
																			$hora["Socio"] = "No Disponible";
																			$hora["IDSocio"] = "";
																			$hora["IDReserva"] = "";
																		endif;	
																	endif;	
															/*
															else:
																$hora["Disponible"] = "N";														
																$hora["Socio"] = "Hora no disponible";
																$hora["IDSocio"] = "";
																$hora["IDReserva"] = "";		
															endif;	
															**/
																	
														endif;		
													}
											endif;
											
											// Si el tee es 10
											if($i==2):
													//Verifico que no este reservada y que pueda reservar en esta hora dependiendo las horas/minutos de anticipacion permitidos
													if ( (in_array($horaInicial,$array_hora_reservada_tee10[$IDElemento])) || ($hora_actual_sistema<$hora_empezar_reserva && $valor_tiempo_anticipacion >0)  )
													{
														$hora["Disponible"] = "N";
														$hora["Socio"] = $array_socio_tee10["$horaInicial"]["NombreSocio"];
														$hora["Socio"] = $array_socio_tee10["$horaInicial"]["Tee10"]["NombreSocio"];
														$hora["IDSocio"] = $array_socio_tee10["$horaInicial"]["IDSocio"];
														$hora["IDReserva"] = $array_socio_tee10["$horaInicial"]["IDReservaGeneral"];	
													}
													else
													{
														
														//Verifico que no tenga fecha de cierre en esta hora 
														$verifica_abierto_servicio_hora = SIMWebservice::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$IDElemento,$hora["Hora"]);
														if(!empty($verifica_abierto_servicio_hora)):
																//extraigo la razon
																$mensaje_cierre = explode(":",$verifica_abierto_servicio_hora); 
																
																$hora["Disponible"] = "N";														
																$hora["Socio"] = $mensaje_cierre[2];
																$hora["IDSocio"] = "";
																$hora["IDReserva"] = "";
														else:	
															
															//if(strtotime($hora_fecha_actual) >= strtotime(date("Y-m-d H:i:s"))):
																	if($NumeroTurnos==1):
																		$hora["Disponible"] = "S";
																		$hora["Socio"] = "";
																		$hora["IDSocio"] = "";
																		$hora["IDReserva"] = "";
																	else:
																	//verifico si es posible reservar en esta hora cuando el turno sea mas de 1, valido si los siguientes turnos estan disponible
																		$array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf($Fecha, $hora["Hora"], $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, "Tee10");
																		if(count($array_disponible)==$NumeroTurnos):
																			$hora["Disponible"] = "S";
																			$hora["Socio"] = "";
																			$hora["IDSocio"] = "";
																			$hora["IDReserva"] = "";
																		else:
																			$hora["Disponible"] = "N";														
																			$hora["Socio"] = "No Disponible";
																			$hora["IDSocio"] = "";
																			$hora["IDReserva"] = "";
																		endif;	
																	endif;	
															/*		
															else:
																$hora["Disponible"] = "N";														
																$hora["Socio"] = "Hora no disponible";
																$hora["IDSocio"] = "";
																$hora["IDReserva"] = "";		
															endif;			
															*/
														endif;	
													}
											endif;
											
											
													
											
											$hora["IDCampo"] = $IDCampo;	
											
											if($i==1):												
												$hora["Tee"] = "Tee1";	
											else:
												$hora["Tee"] = "Tee10";	
											endif;
											
											
											//Maximo y minimo deacuerso a los turnos
											 //$minimo_invitado = ($datos_disponibilidad["NumeroInvitadoClub"] * $NumeroTurnos)-1;
											 //$maximo_invitado = ($datos_disponibilidad["NumeroInvitadoExterno"] * $NumeroTurnos)-1;
											 $minimo_invitado = ($datos_disponibilidad["NumeroInvitadoClub"] * $NumeroTurnos);
											 $maximo_invitado = ($datos_disponibilidad["NumeroInvitadoExterno"] * $NumeroTurnos);
											
											$hora["MaximoPersonaTurno"] = $datos_disponibilidad["MaximoPersonaTurno"];		
											$hora["NumeroInvitadoClub"] = "$minimo_invitado";		
											$hora["NumeroInvitadoExterno"] =  "$maximo_invitado";	
											
											
											//Repeticion reserva
											$hora["IDDisponibilidad"] = $datos_disponibilidad["IDDisponibilidad"];
											$hora["PermiteRepeticion"] = $datos_disponibilidad["PermiteRepeticion"];	
											$hora["MaximoRepeticion"] = $datos_disponibilidad["NumeroRepeticion"] . " " . $datos_disponibilidad["MedicionRepeticion"];	


											$hora["IDElemento"] = $IDElemento;		
											$hora["NombreElemento"] = $hora["Tee"]."-".$dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $IDElemento . "'" );
											
											//Consulto los datos de georeferenciacion
											$datos_disponibilidad_geo = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $datos_disponibilidad["IDDisponibilidad"] . "' ", "array" );
											$hora["Georeferenciacion"] = $datos_disponibilidad_geo["Georeferenciacion"];
											//Consulto los demas datos de la configuracion del servicio
											$datos_geo_servicio = $dbo->fetchAll( "Servicio", " IDServicio = '" . $IDServicio . "' ", "array" );
											$hora["Latitud"] = $datos_geo_servicio["Latitud"];
											$hora["Longitud"] = $datos_geo_servicio["Longitud"];
											$hora["Rango"] = $datos_geo_servicio["Rango"];
											$hora["MensajeFueraRango"] = $datos_geo_servicio["MensajeFueraRango"];	
											
											array_push($response_disponibilidad_tee, $hora);
									endif;		
								endif;
																
								$array_horas_elemento[] = $horaInicial;								
								$segundos_horaInicial=strtotime($horaInicial);
								$segundos_minutoAnadir=$minutoAnadir*60;
								$array_horas=$nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
								$hora_actual = strtotime( $nuevaHora );
								$horaInicial=$nuevaHora;
											
								
												
								endwhile;
								
										
										
							}		
					}
					
				}
				else{						
					$respuesta["message"] = $verifica_abierto_servicio;
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
					return $respuesta;
				}
				
					endfor;	
		
			endwhile;
			
			
			
			//
			
			foreach($response_disponibilidad_tee as $id_array => $datos_array):				
				$array_ordenado_hora[$datos_array["Hora"].$datos_array["NombreElemento"]] = $datos_array;
 			endforeach;
			
			ksort($array_ordenado_hora);
			
			$response_array_ordenado = array();
			foreach($array_ordenado_hora as $id_array => $datos_array):			
				array_push($response_array_ordenado, $datos_array);			
 			endforeach;
			
			array_push($response_disponibilidades, $response_array_ordenado);
			
			// Si $UnElemento no es vacio no ordeno el array ya que se consulto un solo elemnto de los contrario ordeno todos los elemnetos buscados
			if(!empty($UnElemento)):			
				$servicio_hora["Disponibilidad"] = $response_array_ordenado;
			else:
				$servicio_hora["Disponibilidad"] = $response_disponibilidades;	
			endif;
			//$servicio_hora["Disponibilidad"] = $response_disponibilidades;
			array_push($response, $servicio_hora);
			
			
			
			
			
			
		
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;	
			
		  
			
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
		
		
		//inserta _log				
		$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('get_disponiblidad_elemento_servicio','IDClub: ".$IDClub. " IDServicio:" . $IDServicio . " . Fecha: ".$Fecha." Elemento: ".$IDElemento."','".json_encode($respuesta)."')");
			
		
		return $respuesta;	
	
	
}


function get_reserva_asociada($IDClub,$IDSocio,$IDReserva){
	$dbo =& SIMDB::get();
	$response = array();
		
		
		$sql = "SELECT * FROM ReservaGeneral WHERE IDSocio = '".$IDSocio."' and IDReservaGeneral =  '".$IDReserva."'";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while ($row_reserva = $dbo->fetchArray($qry))	:
				$reserva["IDClub"] = $IDClub;
				$reserva["IDSocio"] = $IDSocio;
				$reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
				$reserva["IDServicio"] = $row_reserva["IDServicio"];
				$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $row_reserva["IDServicio"] . "'" ); 
				$reserva["NombreServicio"] = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" ); 
				$reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
				$reserva["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'" );
				$reserva["Fecha"] = $row_reserva["Fecha"];
				$reserva["Tee"] = $row_reserva["Tee"];
				
				$estado_reserva = $row_reserva["IDEstadoReserva"];
					
				
				
				if(strlen($row_reserva["Hora"])!=8):
					$row_reserva["Hora"] .= ":00";
				endif;
				
				$reserva["Hora"] = $row_reserva["Hora"];
				
				$zonahoraria = date_default_timezone_get();
				$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );																						
				$reserva["GMT"] = SIMWebservice::timezone_offset_string( $offset );
			
				
				//Reserva automaticas
				$response_otra_reserva = array();
				$sql_otra_reserva = $dbo->query("Select * From  ReservaGeneralAutomatica Where IDReservaGeneral = '".$IDReserva."'");
				while ($r_otra_reserva = $dbo->fetchArray($sql_otra_reserva)):
					$datos_reserva = $dbo->fetchAll( "ReservaGeneral", " IDReservaGeneral = '" . $r_otra_reserva["IDReservaGeneralAsociada"] . "' ", "array" );
				
					$otra_reserva[IDReservaGeneral]=$datos_reserva["IDReservaGeneral"];					
					$otra_reserva["IDReserva"] = $datos_reserva["IDReservaGeneral"];
					$otra_reserva["IDServicio"] = $datos_reserva["IDServicio"];
					$id_servicio_maestro_otro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $datos_reserva["IDServicio"] . "'" ); 
					$otra_reserva["NombreServicio"] = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio_maestro_otro . "'" ); 
					$otra_reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
					if($estado_reserva=="3"):
						//$otra_reserva["NombreElemento"] = "Se asignará elemento automaticamente de ser necesario";
						$otra_reserva["NombreElemento"] = ".";
					else:
						$otra_reserva["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $datos_reserva["IDServicioElemento"] . "'" );
					endif;
					$otra_reserva["Fecha"] = $datos_reserva["Fecha"];
					$otra_reserva["Tee"] = $datos_reserva["Tee"];
					if(strlen($datos_reserva["Hora"])!=8):
						$datos_reserva["Hora"] .= ":00";
					endif;
				
					$otra_reserva["Hora"] = $datos_reserva["Hora"];
					$zonahoraria = date_default_timezone_get();
					$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );																						
					$otra_reserva["GMT"] = SIMWebservice::timezone_offset_string( $offset );
						
						array_push($response_otra_reserva, $otra_reserva);
					endwhile;
				
				$reserva["ReservaAsociada"] = $response_otra_reserva;
				
				
				array_push($response, $reserva);
			endwhile;	
				
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No se encontraron registros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
	
}


function get_reservas_socio($IDClub,$IDSocio,$Limite=0,$IDReserva=""){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		$array_id_consulta[]=$IDSocio;
		
		$socio_padre = $dbo->getFields( "Socio" , "AccionPadre" , "IDSocio = '".$IDSocio."'");
		// Si esta en blanco quiere decir que es socio cabeza y debe consultar las reservas de sus beneficiarios
		if ($socio_padre==""):
			$accion_padre = $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$IDSocio."'");
			$sql_beneficiarios = "SELECT * FROM Socio WHERE AccionPadre = '".$accion_padre."' and IDClub = '".$IDClub."' ORDER BY Nombre Desc ";			
			$qry_beneficiarios = $dbo->query($sql_beneficiarios);
			while ($r_beneficiario = $dbo->fetchArray($qry_beneficiarios)):
				$array_id_consulta [] = $r_beneficiario[IDSocio];
			endwhile;
		endif;
		
		if(count($array_id_consulta)>0 && empty($IDReserva)):
			$where_beneficiario = "and (IDSocio in (" . implode(",",$array_id_consulta).") or IDSocioBeneficiario in (" . implode(",",$array_id_consulta)."))";
		endif;
		
		if(!empty($IDReserva))
			$condicion_reserva = " and IDReservaGeneral = '".$IDReserva."' ";
		
		if ($Limite!=0)
			$condicion_limite = " Limit ".$Limite;
		
		$sql = "SELECT * FROM ReservaGeneral WHERE IDClub = '".$IDClub."' and IDEstadoReserva = 1 and Fecha >= CURDATE() ".$where_beneficiario." " .$condicion_reserva. "ORDER BY Fecha Desc  " . $condicion_limite;
		$qry = $dbo->query( $sql );
		
		
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			
				while ($row_reserva = $dbo->fetchArray($qry))	:
				
				
				
						// Verifico si es una reserva asociada para no mostrarla en el resultado
						$sql_auto = "SELECT * FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAsociada = '".$row_reserva["IDReservaGeneral"]."' and IDEstadoReserva = 1";
						$qry_auto = $dbo->query( $sql_auto );			
						if( $dbo->rows( $qry_auto ) <= 0 ){
	
				
				
								$reserva["IDClub"] = $IDClub;
								$reserva["IDSocio"] = $IDSocio;
								$reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
								$reserva["IDServicio"] = $row_reserva["IDServicio"];
								$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $row_reserva["IDServicio"] . "'" ); 
								$reserva["NombreServicio"] = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" ); 
								$reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
								$reserva["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'" );
								$reserva["Fecha"] = $row_reserva["Fecha"];
								$reserva["Tee"] = $row_reserva["Tee"];
								
								
								
								if (!empty($row_reserva["IDAuxiliar"])):
									$reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
									$reserva["Auxiliar"] = $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'" );
									$id_tipo_auxiliar= $dbo->getFields( "Auxiliar" , "IDAuxiliarTipo" , "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'" );
									$reserva["TipoAuxiliar"] = $dbo->getFields( "AuxiliarTipo" , "Nombre" , "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'" );					
								else:
									unset($reserva['IDAuxiliar']);
									unset($reserva['Auxiliar']);
									unset($reserva['TipoAuxiliar']);	
								endif;	
									
								
								if (!empty($row_reserva["IDTipoModalidadEsqui"])):
									$reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
									$reserva["Modalidad"] = $dbo->getFields( "TipoModalidadEsqui" , "Nombre" , "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'" );
								else:
									unset($reserva['IDTipoModalidad']);
									unset($reserva['Modalidad']);
								endif;
								
								if(strlen($row_reserva["Hora"])!=8):
									$row_reserva["Hora"] .= ":00";
								endif;
								
								$reserva["Hora"] = $row_reserva["Hora"];
								
								$zonahoraria = date_default_timezone_get();
								$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );																						
								$reserva["GMT"] = SIMWebservice::timezone_offset_string( $offset );
								
								
								if($row_reserva["IDDisponibilidad"]<=0):
									$id_disponibilidad = $dbo->getFields( "ServicioDisponibilidad" , "IDDisponibilidad" , "IDServicio = '".$r["IDServicio"]."'" ); 
								else:
									$id_disponibilidad = $row_reserva["IDDisponibilidad"];
								endif;
								
								$invitadoclub = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoClub" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
								$invitadoexterno = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoExterno" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
								
								if (!empty($invitadoclub)):
									$reserva["NumeroInvitadoClub"] = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoClub" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
								else:
									$reserva["NumeroInvitadoClub"] = ""; 			
								endif;
								if (!empty($invitadoexterno)):
									$reserva["NumeroInvitadoExterno"] = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoExterno" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
								else:
									$reserva["NumeroInvitadoExterno"] = "";
								endif;		
				
								if ($row_reserva["IDInvitadoBeneficiario"]>0):
									$reserva["Beneficiario"] = utf8_encode($dbo->getFields( "Invitado" , "Nombre" , "IDInvitado = '".$row_reserva["IDInvitadoBeneficiario"]."'" ) . " " .$dbo->getFields( "Invitado" , "Apellido" , "IDInvitado = '".$row_reserva["IDInvitadoBeneficiario"]."'" ));
								endif;
								if ($row_reserva["IDSocioBeneficiario"]>0):
									$reserva["Beneficiario"] = strtoupper(utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$row_reserva["IDSocioBeneficiario"]."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$row_reserva["IDSocioBeneficiario"]."'")));
								endif;
								
								
								
								//Invitados Reserva
								$response_invitados_reserva = array();
								$sql_invitados_reserva = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$row_reserva["IDReservaGeneral"]."'");
								$total_invitado = $dbo->rows($sql_invitados_reserva);
								while ($r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva)):
									$id_reserva_general_invitado = $r_invitados_reserva["IDReservaGeneralInvitado"];
									$invitado_reserva[IDReservaGeneralInvitado]=$r_invitados_reserva["IDReservaGeneralInvitado"];
									$invitado_reserva[IDSocio]=$r_invitados_reserva["IDSocio"];
									$invitado_reserva[NombreSocio]=utf8_decode(strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$r_invitados_reserva["IDSocio"]."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$r_invitados_reserva["IDSocio"]."'")));
									$invitado_reserva[NombreExterno]=utf8_decode(strtoupper($r_invitados_reserva["Nombre"]));
									if($r_invitados_reserva["IDSocio"]==0):
										$tipo_invitado = "Externo";	
									else:
										$tipo_invitado = "Socio";	
									endif;
									
									$invitado_reserva[TipoInvitado]=$tipo_invitado;
									
									array_push($response_invitados_reserva, $invitado_reserva);
								endwhile;
								
								/*
								//Verifico si el servicio es golf y en invitados falta 1 agrego el socio por que pertenece a la reserva
								if( ($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30) ): //Golf
									//Consulto cuantos invitados son minimo para saber si falta 1 y agregar el socio como invitado
									if ($id_disponibilidad>0):
										$minimo_invitado_reserva = (int)$dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
									endif;	
									
									if($total_invitado<$minimo_invitado_reserva):
										$invitado_reserva[IDReservaGeneralInvitado]=$id_reserva_general_invitado;
										$invitado_reserva[IDSocio]=$IDSocio;
										$invitado_reserva[NombreSocio]=strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocio."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocio."'"));										
										$tipo_invitado = "Socio";	
										$invitado_reserva[TipoInvitado]=$tipo_invitado;										
										array_push($response_invitados_reserva, $invitado_reserva);
									endif;
								endif;
								*/
								
								$reserva["Invitados"] = $response_invitados_reserva;
								
								//Reservas asociadas
								$response_reserva_asociada = array();
								$array_asociada = SIMWebService::get_reserva_asociada($IDClub,$IDSocio,$row_reserva["IDReservaGeneral"]);				
								foreach($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva):
									array_push($response_reserva_asociada, $datos_reserva);
								endforeach;
								$reserva["ReservaAsociada"] = $response_reserva_asociada;
								
								
								
								
								
								array_push($response, $reserva);
						} // fin verificar si fue un areserva automatica
				endwhile;	
			
				
			
			
			
				
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No tienes reservas programadas.";
				$respuesta["success"] = true;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
}

function get_reservas_empleado($IDClub,$IDUsuario,$Limite=0,$IDReserva=""){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		if(!empty($IDReserva))
			$condicion_reserva = " and IDReservaGeneral = '".$IDReserva."' ";
		
		if ($Limite!=0)
			$condicion_limite = " Limit ".$Limite;
		
		$sql = "SELECT * FROM ReservaGeneral WHERE IDClub = '".$IDClub."' and IDEstadoReserva = 1 and Fecha >= CURDATE() and IDUsuarioReserva = '".$IDUsuario."' ORDER BY Fecha Desc  " . $condicion_limite;
		$qry = $dbo->query( $sql );
		
		
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			
				while ($row_reserva = $dbo->fetchArray($qry))	:
				
				
				
						// Verifico si es una reserva asociada para no mostrarla en el resultado
						$sql_auto = "SELECT * FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAsociada = '".$row_reserva["IDReservaGeneral"]."' and IDEstadoReserva = 1";
						$qry_auto = $dbo->query( $sql_auto );			
						if( $dbo->rows( $qry_auto ) <= 0 ){
	
				
				
								$reserva["IDClub"] = $IDClub;
								$reserva["IDSocio"] = $IDSocio;
								$reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
								$reserva["IDServicio"] = $row_reserva["IDServicio"];
								$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $row_reserva["IDServicio"] . "'" ); 
								$reserva["NombreServicio"] = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" ); 
								$reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
								$reserva["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'" );
								$reserva["Fecha"] = $row_reserva["Fecha"];
								$reserva["Tee"] = $row_reserva["Tee"];
								
								
								
								if (!empty($row_reserva["IDAuxiliar"])):
									$reserva["IDAuxiliar"] = $row_reserva["IDAuxiliar"];
									$reserva["Auxiliar"] = $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'" );
									$id_tipo_auxiliar= $dbo->getFields( "Auxiliar" , "IDAuxiliarTipo" , "IDAuxiliar = '" . $row_reserva["IDAuxiliar"] . "'" );
									$reserva["TipoAuxiliar"] = $dbo->getFields( "AuxiliarTipo" , "Nombre" , "IDAuxiliarTipo = '" . $id_tipo_auxiliar . "'" );					
								else:
									unset($reserva['IDAuxiliar']);
									unset($reserva['Auxiliar']);
									unset($reserva['TipoAuxiliar']);	
								endif;	
									
								
								if (!empty($row_reserva["IDTipoModalidadEsqui"])):
									$reserva["IDTipoModalidad"] = $row_reserva["IDTipoModalidadEsqui"];
									$reserva["Modalidad"] = $dbo->getFields( "TipoModalidadEsqui" , "Nombre" , "IDTipoModalidadEsqui = '" . $row_reserva["IDTipoModalidadEsqui"] . "'" );
								else:
									unset($reserva['IDTipoModalidad']);
									unset($reserva['Modalidad']);
								endif;
								
								if(strlen($row_reserva["Hora"])!=8):
									$row_reserva["Hora"] .= ":00";
								endif;
								
								$reserva["Hora"] = $row_reserva["Hora"];
								
								$zonahoraria = date_default_timezone_get();
								$offset = timezone_offset_get( new DateTimeZone( $zonahoraria ), new DateTime() );																						
								$reserva["GMT"] = SIMWebservice::timezone_offset_string( $offset );
								
								
								if($row_reserva["IDDisponibilidad"]<=0):
									$id_disponibilidad = $dbo->getFields( "ServicioDisponibilidad" , "IDDisponibilidad" , "IDServicio = '".$r["IDServicio"]."'" ); 
								else:
									$id_disponibilidad = $row_reserva["IDDisponibilidad"];
								endif;
								
								$invitadoclub = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoClub" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
								$invitadoexterno = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoExterno" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
								
								if (!empty($invitadoclub)):
									$reserva["NumeroInvitadoClub"] = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoClub" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
								else:
									$reserva["NumeroInvitadoClub"] = ""; 			
								endif;
								if (!empty($invitadoexterno)):
									$reserva["NumeroInvitadoExterno"] = $dbo->getFields( "Disponibilidad" , "NumeroInvitadoExterno" , "IDDisponibilidad = '".$id_disponibilidad."'" ); 
								else:
									$reserva["NumeroInvitadoExterno"] = "";
								endif;		
				
								if ($row_reserva["IDInvitadoBeneficiario"]>0):
									$reserva["Beneficiario"] = $dbo->getFields( "Invitado" , "Nombre" , "IDInvitado = '".$row_reserva["IDInvitadoBeneficiario"]."'" ) . " " .$dbo->getFields( "Invitado" , "Apellido" , "IDInvitado = '".$row_reserva["IDInvitadoBeneficiario"]."'" );
								endif;
								if ($row_reserva["IDSocioBeneficiario"]>0):
									$reserva["Beneficiario"] = strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$row_reserva["IDSocioBeneficiario"]."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$row_reserva["IDSocioBeneficiario"]."'"));
								endif;
								
								
								
								//Invitados Reserva
								$response_invitados_reserva = array();
								$sql_invitados_reserva = $dbo->query("Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$row_reserva["IDReservaGeneral"]."'");
								$total_invitado = $dbo->rows($sql_invitados_reserva);
								while ($r_invitados_reserva = $dbo->fetchArray($sql_invitados_reserva)):
									$id_reserva_general_invitado = $r_invitados_reserva["IDReservaGeneralInvitado"];
									$invitado_reserva[IDReservaGeneralInvitado]=$r_invitados_reserva["IDReservaGeneralInvitado"];
									$invitado_reserva[IDSocio]=$r_invitados_reserva["IDSocio"];
									$invitado_reserva[NombreSocio]=strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$r_invitados_reserva["IDSocio"]."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$r_invitados_reserva["IDSocio"]."'"));
									$invitado_reserva[NombreExterno]=strtoupper($r_invitados_reserva["Nombre"]);
									if($r_invitados_reserva["IDSocio"]==0):
										$tipo_invitado = "Externo";	
									else:
										$tipo_invitado = "Socio";	
									endif;
									
									$invitado_reserva[TipoInvitado]=$tipo_invitado;
									
									array_push($response_invitados_reserva, $invitado_reserva);
								endwhile;
								
								/*
								//Verifico si el servicio es golf y en invitados falta 1 agrego el socio por que pertenece a la reserva
								if( ($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30) ): //Golf
									//Consulto cuantos invitados son minimo para saber si falta 1 y agregar el socio como invitado
									if ($id_disponibilidad>0):
										$minimo_invitado_reserva = (int)$dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
									endif;	
									
									if($total_invitado<$minimo_invitado_reserva):
										$invitado_reserva[IDReservaGeneralInvitado]=$id_reserva_general_invitado;
										$invitado_reserva[IDSocio]=$IDSocio;
										$invitado_reserva[NombreSocio]=strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$IDSocio."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$IDSocio."'"));										
										$tipo_invitado = "Socio";	
										$invitado_reserva[TipoInvitado]=$tipo_invitado;										
										array_push($response_invitados_reserva, $invitado_reserva);
									endif;
								endif;
								*/
								
								$reserva["Invitados"] = $response_invitados_reserva;
								
								//Reservas asociadas
								$response_reserva_asociada = array();
								$array_asociada = SIMWebService::get_reserva_asociada($IDClub,$IDSocio,$row_reserva["IDReservaGeneral"]);				
								foreach($array_asociada["response"]["0"]["ReservaAsociada"] as $datos_reserva):
									array_push($response_reserva_asociada, $datos_reserva);
								endforeach;
								$reserva["ReservaAsociada"] = $response_reserva_asociada;
								
								
								
								
								
								array_push($response, $reserva);
						} // fin verificar si fue un areserva automatica
				endwhile;	
			
				
			
			
			
				
				$respuesta["message"] = $message;
				$respuesta["success"] = true;
				$respuesta["response"] = $response;	
		}//End if
		else
		{
				$respuesta["message"] = "No tienes reservas programadas.";
				$respuesta["success"] = true;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
}



function verificar_disponibilidad_auxiliar($IDClub,$IDServicio,$Fecha,$Hora,$IDAuxiliar){
	$dbo =& SIMDB::get();
	// Consulto los auxiliares reservados en esta fecha y hora					
	$sql_reserva_auxiliar = $dbo->query("Select * From AuxiliarReserva Where IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDAuxiliar = '".$IDAuxiliar."'");
	if ($dbo->rows($sql_reserva_auxiliar)>0):
		return "1"; // Si esta reservado
	else:
		return "0";	// NO esta reservado
	endif;
	
}


function get_auxiliares($IDClub,$IDServicio,$Fecha,$Hora){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		if( !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora ) ){
					$Hora = SIMWebService::validar_formato_hora($Hora);
			
					
				
					$dia_fecha= date('w', strtotime($Fecha));				
					$sql_dispo_aux_gral = "Select * From AuxiliarDisponibilidadDetalle Where IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and '".$Hora. "'>=HoraDesde and '".$Hora."'<=HoraHasta";
					$qry_dispo_aux_gral = $dbo->query($sql_dispo_aux_gral);
					$response_auxiliar = array();
					if( $dbo->rows( $qry_dispo_aux_gral ) > 0 ){	
							while($row_dispo_aux_gral = $dbo->fetchArray($qry_dispo_aux_gral)){
									$array_auxiliares_disponible = explode("|",$row_dispo_aux_gral["IDAuxiliar"]);
									if (count($array_auxiliares_disponible)>0):
										foreach($array_auxiliares_disponible as $IDAuxiliar):
											$flag_disponible= SIMWebService::verificar_disponibilidad_auxiliar($IDClub,$IDServicio,$Fecha,$Hora,$IDAuxiliar);
											if($flag_disponible == "0" && !empty($IDAuxiliar)):
												//verifico que el auxiliar no este asignado en alguna reserva a esta hora
												$id_reserva = "";
												$id_reserva = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDAuxiliar = '" . $IDAuxiliar . "' and Fecha = '".$Fecha."' and Hora = '".$Hora."'" );
												if(empty($id_reserva)):
													$auxiliar["IDAuxiliar"] = $IDAuxiliar;		
													$auxiliar["Nombre"] = $dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $IDAuxiliar . "'" );
													$tipo_auxiliar = $dbo->getFields( "Auxiliar" , "IDAuxiliarTipo" , "IDAuxiliar = '" . $IDAuxiliar . "'" );
													$auxiliar["Tipo"] = $dbo->getFields( "AuxiliarTipo" , "Nombre" , "IDAuxiliarTipo = '" . $tipo_auxiliar . "'" );
													array_push($response_auxiliar, $auxiliar);												
												endif;	
											endif;
										endforeach;
									endif;
							}
							
							
							if (count($response_auxiliar)>0):
								$auxiliar_disponible["IDClub"] = $IDClub;
								$auxiliar_disponible["Auxiliares"] = $response_auxiliar;
								array_push($response, $auxiliar_disponible);									
								$respuesta["message"] = count($response_auxiliar)." Encontrados";
								$respuesta["success"] = true;
								$respuesta["response"] = $response;
							else:
								$respuesta["message"] = "No se encontraron boleadores disponibles";
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;									
							endif;
						
					}	
					else
					{
						$respuesta["message"] = "No se encontraron boleadores disponibles";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;	
					}//end else			
					
		}
		else{
			$respuesta["message"] = "5. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
			
		return $respuesta;	
	
}


function get_modalidades($IDClub,$IDTipoModalidadEsqui,$IDElemento){
	$dbo =& SIMDB::get();
		
		$response = array();
		
		if(!empty($IDTipoModalidadEsqui)):
			$condicion = " and IDTipoModalidadEsqui = '".$IDTipoModalidadEsqui."' ";
		endif;
		
		if(!empty($IDElemento)):
			// consulto las modalidades del elemento
			$sql_servicio_modalidad = "SELECT * FROM  ServicioElementoModalidad Where IDServicioElemento = '".$IDElemento."'";
			$qry_servicio_modalidad = $dbo->query( $sql_servicio_modalidad );					
			while( $r_servicio_modalidad = $dbo->fetchArray( $qry_servicio_modalidad ) ):
				$array_servicio_modalidad[] = $r_servicio_modalidad["IDTipoModalidadEsqui"];
			endwhile;
			if (count($array_servicio_modalidad)>0):
				$id_modalidades = implode(",",$array_servicio_modalidad);
			else:
				$id_modalidades = 0;	
			endif;
			$condicion = " and IDTipoModalidadEsqui in (".$id_modalidades.")";
			
		endif;
		
		if( !empty( $IDClub ) ){
					$response = array();
					$sql = "SELECT TME.* FROM TipoModalidadEsqui TME ".$tabla_join." WHERE TME.Publicar = 'S' and TME.IDClub = '".$IDClub."' ". $condicion ." ORDER BY TME.Nombre";
					$qry = $dbo->query( $sql );
					if( $dbo->rows( $qry ) > 0 )
					{	
						$message = $dbo->rows( $qry ) . " Encontrados";
						while( $r = $dbo->fetchArray( $qry ) )
						{
								$seccion["IDClub"] = $r["IDClub"];
								$seccion["IDTipoModalidad"] = $r["IDTipoModalidadEsqui"];
								$seccion["Modalidad"] = $r["Nombre"];
								$seccion["Descripcion"] = $r["Descripcion"];
								array_push($response, $seccion);
			
						}//ednw hile
							$respuesta["message"] = $message;
							$respuesta["success"] = true;
							$respuesta["response"] = $response;	
					}//End if
					else
					{
							$respuesta["message"] = "No se encontraron registros";
							$respuesta["success"] = false;
							$respuesta["response"] = NULL;	
					}//end else				
								
					
		}
		else{
			$respuesta["message"] = "6. Atencion faltan parametros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
			
		return $respuesta;	
	
}



function verificar_socio_grupo_fecha($IDClub,$IDSocioInvitado,$Fecha,$IDServicio){
		
		$dbo =& SIMDB::get();
		
		$respuesta_valida_invitado=SIMWebService::verificar_socio_grupo($IDClub,$IDSocioInvitado,$Fecha,$IDServicio);
		if ($respuesta_valida_invitado==1):
			$nombre_socio_invitado = $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $IDSocioInvitado . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $IDSocioInvitado . "'" );
			$respuesta["message"] = "El invitado: ". $nombre_socio_invitado . ", solo puede estar en un grupo por dia";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;	
		else:
			$respuesta["message"] = "valido";
			$respuesta["success"] = true;
			$respuesta["response"] = "";	
		endif;													
	
		return $respuesta;	
}



function verificar_socio_grupo($IDClub,$IDSocio,$Fecha,$IDServicio){
	
		$dbo =& SIMDB::get();		
		
		$flag_valido = 0;
		//Consulto si el socio esta en otro grupo de invitados el mismo dia de la reserva o si es dueño de una reserva de tenis el mismo dia
		$sql_socio_grupo = "SELECT RGI.* FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '".$IDSocio."') and RG.IDClub = '".$IDClub."' and RG.Fecha = '".$Fecha."' and RG.IDServicio = '".$IDServicio."' ORDER BY IDReservaGeneralInvitado Desc ";
		$qry_socio_grupo = $dbo->query($sql_socio_grupo);
		if ($dbo->rows($qry_socio_grupo)>0):
				$flag_valido = 1;				
		endif;
		return $flag_valido;	
}




//Funcion para traer las reservas en una fecha determinada de un elemento y un servicio determinado
//creado por John Escobar
//12 de Noviembre e 2015
function get_reservas_servicio($IDClub, $IDServicio,$Fecha = "", $IDServicioElemento = "", $IDSocio = "" ){
	
	$dbo =& SIMDB::get();
	$order = " ReservaGeneral.Fecha Desc, ReservaGeneral.Hora ASC ";
		
	$response = array();

	$where = "";
	if( !empty( $Fecha ) )
		$where .= " AND ReservaGeneral.Fecha = '" . $Fecha . "' ";

	if( !empty( $IDServicioElemento ) )
		$where .= " AND ReservaGeneral.IDServicioElemento = '" . $IDServicioElemento . "' ";

	if( !empty( $IDSocio ) )
	{
		$where .= " AND ( Accion = '" . $IDSocio . "' OR Nombre LIKE '%" . $IDSocio . "%' OR Apellido LIKE '%" . $IDSocio . "%'  )  ";
		if( empty( $Fecha ) )
			$where .= " AND Fecha >= CURDATE()  ";

		$order = " ReservaGeneral.Fecha ASC, ReservaGeneral.Hora ASC ";
	}


	$sql = "SELECT ReservaGeneral.*, Socio.Nombre, Socio.Apellido FROM ReservaGeneral, Socio WHERE ReservaGeneral.IDClub = '" . $IDClub . "' AND ReservaGeneral.IDEstadoReserva = 1 AND ReservaGeneral.IDServicio = '" . $IDServicio . "' AND ReservaGeneral.IDSocio = Socio.IDSocio AND Socio.IDClub = '" . $IDClub . "' " . $where . "  ORDER BY " . $order;
	$qry = $dbo->query( $sql );
	if( $dbo->rows( $qry ) > 0 )
	{
		$message = $dbo->rows( $qry ) . " Encontrados";
		while ($row_reserva = $dbo->fetchArray($qry))	:
			$reserva["IDClub"] = $IDClub;
			$reserva["IDSocio"] = $row_reserva["IDSocio"];
			$reserva["IDReserva"] = $row_reserva["IDReservaGeneral"];
			$reserva["Socio"] = array( "Nombre" => $row_reserva["Nombre"], "Apellido" => $row_reserva["Apellido"]  );
			$reserva["IDServicio"] = $row_reserva["IDServicio"];
			$reserva["IDElemento"] = $row_reserva["IDServicioElemento"];
			$reserva["NombreElemento"] = $dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $row_reserva["IDServicioElemento"] . "'" );
			$reserva["Fecha"] = $row_reserva["Fecha"];
			$reserva["Tee"] = $row_reserva["Tee"];
			
			if(strlen($row_reserva["Hora"])!=8):
				$row_reserva["Hora"] .= ":00";
			endif;
			
			$reserva["Hora"] = $row_reserva["Hora"];
			array_push($response, $reserva);
		endwhile;	
			
			$respuesta["message"] = $message;
			$respuesta["success"] = true;
			$respuesta["response"] = $response;	
	}//End if
	else
	{
			$respuesta["message"] = "No se encontraron registros";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;	
	}//end else		
	
	
	
	
		
		
		
			
		
	return $respuesta;	
	
}//end function


	
function set_preferencias($IDClub, $IDSocio, $SeccionesContenido, $SeccionesEvento,$SeccionesGaleria)
{
	
	
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio )  ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			//borro las secciones asociadas al socio
			$sql_borra_seccion = $dbo->query("Delete From SocioSeccion Where IDSocio  = '".$IDSocio."'");	
			
			//borro las secciones asociadas al socio
			$sql_borra_seccion_even = $dbo->query("Delete From SocioSeccionEvento Where IDSocio  = '".$IDSocio."'");
			
			//borro las secciones asociadas al socio
			$sql_borra_seccion_gal = $dbo->query("Delete From SocioSeccionGaleria Where IDSocio  = '".$IDSocio."'");
			
			
			if (!empty($SeccionesContenido)):
				$array_secciones_not = explode(",",$SeccionesContenido);
				if(count($array_secciones_not)>0):
					foreach ($array_secciones_not as $id_seccion ):
						// verifico que la seccion sea del club
						$id_seccion = $dbo->getFields( "Seccion" , "IDSeccion" , "IDClub = '" . $IDClub . "' and IDSeccion = '".$id_seccion."'" );
						if (!empty($id_seccion)):
							$sql_seccion_not = $dbo->query("Insert Into SocioSeccion (IDSocio, IDSeccion) Values ('".$IDSocio."', '".$id_seccion."')");
						endif;	
					endforeach;
				endif;
			endif;
			
			if (!empty($SeccionesEvento)):
				$array_secciones_even = explode(",",$SeccionesEvento);
				if(count($array_secciones_even)>0):
					foreach ($array_secciones_even as $id_seccion ):
						// verifico que la seccion sea del club
						$id_seccion = $dbo->getFields( "SeccionEvento" , "IDSeccionEvento" , "IDClub = '" . $IDClub . "' and IDSeccionEvento = '".$id_seccion."'" );
						if (!empty($id_seccion)):
							$sql_seccion_not = $dbo->query("Insert Into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('".$IDSocio."', '".$id_seccion."')");
						endif;	
					endforeach;
				endif;
			endif;
			
			if (!empty($SeccionesGaleria)):
				$array_secciones_gal = explode(",",$SeccionesGaleria);
				if(count($array_secciones_gal)>0):
					foreach ($array_secciones_gal as $id_seccion ):
						// verifico que la seccion sea del club
						$id_seccion = $dbo->getFields( "SeccionGaleria" , "IDSeccionGaleria" , "IDClub = '" . $IDClub . "' and IDSeccionGaleria = '".$id_seccion."'" );
						if (!empty($id_seccion)):
							$sql_seccion_gal = $dbo->query("Insert Into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('".$IDSocio."', '".$id_seccion."')");
						endif;	
					endforeach;
				endif;
			endif;
			
			
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "7. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}
	
	function set_preferencias_empleado($IDClub, $IDUsuario, $SeccionesContenido, $SeccionesEvento,$SeccionesGaleria)	{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDUsuario )  ){
		
		//verifico que el Usuario exista y pertenezca al club
		$id_Usuario = $dbo->getFields( "Usuario" , "IDUsuario" , "IDUsuario = '" . $IDUsuario . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_Usuario)){
			//borro las secciones asociadas al Usuario
			$sql_borra_seccion = $dbo->query("Delete From UsuarioSeccion Where IDUsuario  = '".$IDUsuario."'");	
			
			//borro las secciones asociadas al Usuario
			$sql_borra_seccion_even = $dbo->query("Delete From UsuarioSeccionEvento Where IDUsuario  = '".$IDUsuario."'");
			
			//borro las secciones asociadas al Usuario
			$sql_borra_seccion_gal = $dbo->query("Delete From UsuarioSeccionGaleria Where IDUsuario  = '".$IDUsuario."'");
			
			
			if (!empty($SeccionesContenido)):
				$array_secciones_not = explode(",",$SeccionesContenido);
				if(count($array_secciones_not)>0):
					foreach ($array_secciones_not as $id_seccion ):
						// verifico que la seccion sea del club
						$id_seccion = $dbo->getFields( "Seccion" , "IDSeccion" , "IDClub = '" . $IDClub . "' and IDSeccion = '".$id_seccion."'" );
						if (!empty($id_seccion)):
							$sql_seccion_not = $dbo->query("Insert Into UsuarioSeccion (IDUsuario, IDSeccion) Values ('".$IDUsuario."', '".$id_seccion."')");
						endif;	
					endforeach;
				endif;
			endif;
			
			if (!empty($SeccionesEvento)):
				$array_secciones_even = explode(",",$SeccionesEvento);
				if(count($array_secciones_even)>0):
					foreach ($array_secciones_even as $id_seccion ):
						// verifico que la seccion sea del club
						$id_seccion = $dbo->getFields( "SeccionEvento" , "IDSeccionEvento" , "IDClub = '" . $IDClub . "' and IDSeccionEvento = '".$id_seccion."'" );
						if (!empty($id_seccion)):
							$sql_seccion_not = $dbo->query("Insert Into UsuarioSeccionEvento (IDUsuario, IDSeccionEvento) Values ('".$IDUsuario."', '".$id_seccion."')");
						endif;	
					endforeach;
				endif;
			endif;
			
			if (!empty($SeccionesGaleria)):
				$array_secciones_gal = explode(",",$SeccionesGaleria);
				if(count($array_secciones_gal)>0):
					foreach ($array_secciones_gal as $id_seccion ):
						// verifico que la seccion sea del club
						$id_seccion = $dbo->getFields( "SeccionGaleria" , "IDSeccionGaleria" , "IDClub = '" . $IDClub . "' and IDSeccionGaleria = '".$id_seccion."'" );
						if (!empty($id_seccion)):
							$sql_seccion_gal = $dbo->query("Insert Into UsuarioSeccionGaleria (IDUsuario, IDSeccionGaleria) Values ('".$IDUsuario."', '".$id_seccion."')");
						endif;	
					endforeach;
				endif;
			endif;
			
			
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el Usuario no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "7. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	
	return $respuesta;
		
	}
	
	

function set_socio_favorito($IDClub, $IDSocio, $SocioFavorito, $EstadoFavorito = "")
{
	
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio )  ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			//borro los favoritos del socio
			//$sql_borra_favorito = $dbo->query("Delete From SocioFavorito Where IDSocio  = '".$IDSocio."'");	
			
			if (!empty($EstadoFavorito)):
				$array_socio_favorito_estado = explode(",",$EstadoFavorito);
			endif;
			
			if (!empty($SocioFavorito)):
				$array_socio_favorito = explode(",",$SocioFavorito);
			endif;
			
			$contador_socio = 0;	
			if(count($array_socio_favorito)>0):
				foreach ($array_socio_favorito as $id_socio ):					
					if($array_socio_favorito_estado[$contador_socio]=="S" && (int)$id_socio>0):
						$inserta_socio_favorito = $dbo->query("Insert Into SocioFavorito (IDSocio, IDSocio2) Values ('".$IDSocio."', '".$id_socio."')");						
					elseif($array_socio_favorito_estado[$contador_socio]=="N"):
						$delete_socio_favorito = $dbo->query("Delete From SocioFavorito Where IDSocio = '".$IDSocio."' and IDSocio2 = '".$id_socio."'");						
					endif;	
					$contador_socio++;
				endforeach;
			endif;
			
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "8. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function elimina_reserva_general($IDClub, $IDSocio, $IDReserva, $Admin = "", $Razon = "")
{
	
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio ) && !empty( $IDReserva )  ){
		
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			//Verifico que este en el tiempo limite para reservar
			$id_disponibilidad = (int)$dbo->getFields( "ReservaGeneral" , "IDDisponibilidad" , "IDReservaGeneral = '" . $IDReserva . "'");
			
			if ($id_disponibilidad>0):
				$tiempo_cancelacion = (int)$dbo->getFields( "Disponibilidad" , "TiempoCancelacion" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
				$medicion_cancelacion = $dbo->getFields( "Disponibilidad" , "MedicionTiempo" , "IDDisponibilidad = '" . $id_disponibilidad . "'");
				switch($medicion_cancelacion):
					case "Dias":	
						$minutos_anticipacion = (60*24) * $tiempo_cancelacion;
					break;
					case "Horas":
						$minutos_anticipacion = 60 * $tiempo_cancelacion;
					break;
					case "Minutos":
						$minutos_anticipacion = $tiempo_cancelacion;
					break;
					default:
						$minutos_anticipacion = 2;
						
				endswitch;
			else:
				$tiempo_cancelacion=2;
				$medicion_cancelacion = "Horas";
				$minutos_anticipacion = 120;
			endif;
			
			
			$fecha_reserva = $dbo->getFields( "ReservaGeneral" , "Fecha" , "IDReservaGeneral = '" . $IDReserva . "'");
			$hora_reserva =  $dbo->getFields( "ReservaGeneral" , "Hora" , "IDReservaGeneral = '" . $IDReserva . "'");
			$hora_inicio_reserva = strtotime ('-'.$minutos_anticipacion.' minute' , strtotime ( $fecha_reserva . " " . $hora_reserva));
			$fechahora_actual =  strtotime ( date("Y-m-d H:i:s") );		
			$id_servicio =  $dbo->getFields( "ReservaGeneral" , "IDServicio" , "IDReservaGeneral = '" . $IDReserva . "'");
			$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $id_servicio . "'" );				
			$id_servicio_cancha = $dbo->getFields( "ServicioMaestro" , "IDServicioMaestroReservar" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
				
			//$fechahora_actual =  strtotime ( "2016-03-29 07:00:00" );		
			
			if ($fechahora_actual>$hora_inicio_reserva && empty($Admin)):
				$respuesta["message"] = "No se puede eliminar la reserva debe ser minimo " . $tiempo_cancelacion . " " . $medicion_cancelacion . " antes";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
				
			else:
			
				
				//verifico en la disponibilidad si la reserva permite la eliminación cuando fue creada por el starter
				$permite_eliminar_reserva_creada_starter = $dbo->getFields( "Disponibilidad" , "PermiteEliminarCreadaStarter" , "IDDisponibilidad = '" . $id_disponibilidad . "'");				
				//verifico que la reserva haya sido creada por el socio si fue por el starter verifico en la disponibilidad si se puede eliminar por el socio
				$reservada_creada_por = $dbo->getFields( "ReservaGeneral" , "UsuarioTrCr" , "IDReservaGeneral = '" . $IDReserva . "'");
				if($reservada_creada_por == "Starter" && empty($Admin) &&  $permite_eliminar_reserva_creada_starter == "N"):					
						$respuesta["message"] = "No se puede eliminar la reserva fue creada por el Starter y solo el starter puede eliminarla";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
				else:
						//Copio Reserva						
						$sql_copia_reserva = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)  
						Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '".$Razon."', NOW(), NOW() 
						From ReservaGeneral 
						Where IDReservaGeneral  = '".$IDReserva."'");	
						//borro reserva
						$sql_borra_reserva = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral  = '".$IDReserva."'");	
						//borro invitados a esa reserva
						$sql_borra_reserva_invitados = $dbo->query("Delete From ReservaGeneralInvitado Where IDReservaGeneral  = '".$IDReserva."'");	
						
						//Verifico si tiene una reserva asociada para borrarla tambien								
						$sql_asociada = "Select * From ReservaGeneralAutomatica Where IDReservaGeneral = '".$IDReserva."' and IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and Fecha = '".$fecha_reserva."'";
						$result_asociada = $dbo->query($sql_asociada);
						while($row_asociada = $dbo->fetchArray($result_asociada)):					
							$sql_copia_reserva = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)  
							Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '".$Razon."', NOW(), NOW() 
							From ReservaGeneral 
							Where IDReservaGeneral  = '".$row_asociada["IDReservaGeneralAsociada"]."'");	
							//borro reserva					
							$sql_borra_reserva = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral  = '".$row_asociada["IDReservaGeneralAsociada"]."'");	
							//borro invitados a esa reserva
							$sql_borra_reserva_invitados = $dbo->query("Delete From ReservaGeneralInvitado Where IDReservaGeneral  = '".$row_asociada["IDReservaGeneralAsociada"]."'");									
						endwhile;
						
						//Si la reserva es una clase elimino la cancha que se reservó con la clase
						if($id_servicio_cancha>0):							
								  // Consulto el servicio del club asociado a este servicio maestro
								  $IDServicioCanchaClub  = $dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '".$IDClub."'" );
								  // Borro la cancha asociada
								  //Copio Reserva
								  $sql_reserva_auto = "Select * FRom ReservaGeneral Where IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicio = '".$IDServicioCanchaClub."' and IDEstadoReserva = 1 and Fecha = '".$fecha_reserva."' and Hora = '".$hora_reserva."' and Tipo = 'Automatica' limit 1";
								  $result_reserva_auto = $dbo->query($sql_reserva_auto);
								  $row_reserva_auto = $dbo->fetchArray($result_reserva_auto);						  
								
								$sql_copia_reserva_auto = $dbo->query("INSERT IGNORE INTO ReservaGeneralEliminada (IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, Razon, UsuarioTrCr, FechaTrCr)  
								Select IDReservaGeneral, IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDAuxiliar, IDTipoModalidadEsqui, Fecha, Hora, Tee, Observaciones, Tipo, Notificado, '".$Razon."', NOW(), NOW() 
								From ReservaGeneral 
								Where IDReservaGeneral  = '".$row_reserva_auto["IDReservaGeneral"]."'");							
								//borro reserva
								$sql_borra_reserva_auto = $dbo->query("Delete From ReservaGeneral Where IDReservaGeneral  = '".$row_reserva_auto["IDReservaGeneral"]."'");													
								
						endif;
															
						
						
						$respuesta["message"] = "Reserva eliminada correctamente";
						//$respuesta["message"] = "Delete From ReservaGeneral Where IDReservaGeneral  = '".$row_reserva_auto["IDReservaGeneral"]."'";
						$respuesta["success"] = true;
						$respuesta["response"] = NULL;	
					endif;
			endif;		
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "9. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function set_contacto($IDClub,$IDSocio,$Comentario)
{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio )  && !empty( $Comentario ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			$sql_seccion_not = $dbo->query("Insert Into Contacto (IDClub, IDSocio, Comentario, UsuarioTrCr, FechaTrCr) Values ('".$IDClub."','".$IDSocio."', '".$Comentario."','WebService',NOW())");
	
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "10. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}
	
	
	
function set_pqr($IDClub,$IDArea, $IDSocio,$TipoPqr, $Asunto, $Comentario, $Archivo, $File="",$IDTipoPqr="")
{
	
	
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDArea ) && !empty( $IDSocio )  && !empty( $Comentario ) ){
	
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			//UPLOAD de imagenes
			
			if(isset($File)){
			
				$files =  SIMFile::upload( $File["Archivo"] , PQR_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $File["Archivo"]["name"] ) ):	
					$respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
					return $respuesta;
				endif;
				$Archivo = $files[0]["innername"];				
				
				
			}//end if	
			
			//Consulto el siguiente consecutivo del pqr		
			$sql_max_numero = "Select MAX(Numero) as NumeroMaximo From Pqr Where IDClub = '".$IDClub."'";
			$result_numero = $dbo->query($sql_max_numero);
			$row_numero = $dbo->fetchArray($result_numero);
			$siguiente_consecutivo = (int)$row_numero["NumeroMaximo"]+1;
			
			$sql_pqr = $dbo->query("Insert Into Pqr (IDClub, Numero, IDTipoPqr, IDArea, IDSocio, IDPqrEstado, Tipo, Asunto, Descripcion, Archivo1, Fecha,  UsuarioTrCr, FechaTrCr) 
									Values ('".$IDClub."','".$siguiente_consecutivo."','".$IDTipoPqr."','".$IDArea."','".$IDSocio."', '1','".$TipoPqr."','".$Asunto."','".$Comentario."','".$Archivo."',NOW(),'WebService',NOW())");
			
			$id_pqr = $dbo->lastID();
			
			SIMUtil::noticar_nuevo_pqr($id_pqr);
			
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "11. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function set_pqr_respuesta($IDClub,$IDSocio, $IDPqr, $Comentario)
{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDPqr ) && !empty( $IDSocio )  && !empty( $Comentario ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
	
			$sql_pqr = $dbo->query("Insert Into Detalle_Pqr (IDPqr, IDSocio, Fecha, Respuesta, UsuarioTrCr, FechaTrCr) 
									Values ('".$IDPqr."','".$IDSocio."',NOW(), '".$Comentario."','WebService',NOW())");
	
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "12. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function set_foto_socio($IDClub,$IDSocio,$Archivo, $File="")
{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			//UPLOAD de imagenes
			
			if(isset($File)){
			
				$files =  SIMFile::upload( $File["Archivo"] , SOCIO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $File["Archivo"]["name"] ) ):	
					$respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
					return $respuesta;
				endif;
				$Archivo = $files[0]["innername"];				
				
				
			}//end if			
			
			
			$sql_actualiza_foto = $dbo->query("Update Socio Set Foto = '".$Archivo."', FotoActualizadaSocio = 'N', FechaActualizacionFoto = NOW() Where IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'");						
			$respuesta["message"] = "foto guardada";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "13. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}		
	
	function set_foto_empleado($IDClub,$IDUsuario,$Archivo, $File="")
	{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDUsuario ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_usuario = $dbo->getFields( "Usuario" , "IDUsuario" , "IDUsuario = '" . $IDUsuario . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_usuario)){
			
			if(isset($File)){
			
				$files =  SIMFile::upload( $File["Archivo"] , USUARIO_DIR , "IMAGE" );
				if( empty( $files ) && !empty( $File["Archivo"]["name"] ) ):	
					$respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
					return $respuesta;
				endif;
				$Archivo = $files[0]["innername"];				
				
				
			}//end if			
			
			$sql_actualiza_foto = $dbo->query("Update Usuario Set Foto = '".$Archivo."', FotoActualizadaEmpleado = 'N', FechaActualizacionFoto = NOW() Where IDUsuario = '" . $IDUsuario . "' and IDClub = '".$IDClub."'");						
			$respuesta["message"] = "foto guardada";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "13. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}		
	
	
	function get_actualizar_foto_socio($IDClub,$IDSocio){
	$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM Socio WHERE IDSocio = '".$IDSocio."' and IDClub = '".$IDClub."' ORDER BY RAND()";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				if ($r["FotoActualizadaSocio"]=="N"):
					$respuesta["message"] = "Lo sentimos la foto ya fue actualizada";
					$respuesta["success"] = false;
					$respuesta["response"] = $response;
				else:
					$respuesta["message"] = "ok, puede actualizar foto";
					$respuesta["success"] = true;
					$respuesta["response"] = $response;
				endif;
			}//ednw hile
					
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado socio";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
	}
	
	function get_actualizar_foto_empleado($IDClub,$IDUsuario){
	$dbo =& SIMDB::get();
		$response = array();
		$sql = "SELECT * FROM Usuario WHERE IDUsuario = '".$IDUsuario."' and IDClub = '".$IDClub."' ORDER BY RAND()";
		$qry = $dbo->query( $sql );
		if( $dbo->rows( $qry ) > 0 )
		{
			$message = $dbo->rows( $qry ) . " Encontrados";
			while( $r = $dbo->fetchArray( $qry ) )
			{
				if ($r["FotoActualizadaEmpleado"]=="N"):
					$respuesta["message"] = "Lo sentimos la foto ya fue actualizada";
					$respuesta["success"] = false;
					$respuesta["response"] = $response;
				else:
					$respuesta["message"] = "ok, puede actualizar foto";
					$respuesta["success"] = true;
					$respuesta["response"] = $response;
				endif;
			}//ednw hile
					
		}//End if
		else
		{
				$respuesta["message"] = "No se ha encontrado usuario";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		}//end else		
			
		return $respuesta;	
	
	}
	


function set_invitado($IDClub,$IDSocio,$NumeroDocumento,$Nombre,$FechaIngreso)
{	
	$dbo =& SIMDB::get();	
	
	
	
	if( !empty( $NumeroDocumento ) && !empty( $Nombre )  && !empty( $FechaIngreso ) ){
		
		$NumeroDocumento = str_replace(".","",$NumeroDocumento);
		$NumeroDocumento = trim(str_replace(",","",$NumeroDocumento));
		
		$hoy = date("Y-m-d");
		if(strtotime($FechaIngreso)>=strtotime($hoy)){
		
				//verifico que el socio exista y pertenezca al club
				$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
				
				if (!empty($id_socio)){
				
					
					// CAMBIO Sep 12 2016: Se consulta las invitaciones que puede hacer el socio Titular
					// Consulto las invitaciones que puede hacer el socio
					$numero_invitados_dia_permitido_socio = $dbo->getFields( "Socio" , "NumeroInvitados" , "IDSocio = '".$IDSocio."'" );					
					$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocio . "' ", "array" );						
					if(!empty($datos_socio["AccionPadre"])): // es un beneficiario
						$id_socio_titular = $dbo->getFields( "Socio" , "IDSocio" , "Accion = '".$datos_socio["AccionPadre"]."'" );
						$numero_invitados_dia_permitido = $dbo->getFields( "Socio" , "NumeroInvitados" , "IDSocio = '".$id_socio_titular."'" );
						
						if(empty($numero_invitados_dia_permitido) || $numero_invitados_dia_permitido==0)
							$numero_invitados_dia_permitido = $numero_invitados_dia_permitido_socio;
							
						
						//Consulto los id socio de mi vinculo
					 	$sql_socio_vinculo = "Select IDSocio From Socio Where AccionPadre = '".$datos_socio["AccionPadre"]."' or Accion = '".$datos_socio["AccionPadre"]."' and IDClub = '".$IDClub."'";
						$qry_socio_vinculo = $dbo->query($sql_socio_vinculo);
						while($row_socio_vinculo = $dbo->fetchArray($qry_socio_vinculo)):
							$array_socio_vinculo[]=$row_socio_vinculo["IDSocio"];
						endwhile;
						if(count($array_socio_vinculo)>0):
							$id_otro_socio = implode(",",$array_socio_vinculo); 	
							$condicion_otro_socio = " or IDSocio in (".$id_otro_socio.")";
						endif;
					else:
						$id_socio_titular = $IDSocio;
						$numero_invitados_dia_permitido = $dbo->getFields( "Socio" , "NumeroInvitados" , "IDSocio = '".$IDSocio."'" );							
						//Consulto los id socio de mi vinculo
					 	$sql_socio_vinculo = "Select IDSocio From Socio Where AccionPadre = '".$datos_socio["Accion"]."' and IDClub = '".$IDClub."'";
						$qry_socio_vinculo = $dbo->query($sql_socio_vinculo);
						while($row_socio_vinculo = $dbo->fetchArray($qry_socio_vinculo)):
							$array_socio_vinculo[]=$row_socio_vinculo["IDSocio"];
						endwhile;
						if(count($array_socio_vinculo)>0):
							$id_otro_socio = implode(",",$array_socio_vinculo); 	
							$condicion_otro_socio = " or IDSocio in (".$id_otro_socio.")";
						endif;
					endif;
					
					
					// Consulto si el dia de la reserva esta asignado como fecha especial para no tomar en cuenta invitaciones
					$id_fecha_Especial = $dbo->getFields( "FechaEspecialInvitado" , "IDFechaEspecialInvitado" , "Fecha = '".$FechaIngreso."' and IDClub = '".$IDClub."'" );
					if(!empty($id_fecha_Especial)):
						// Dejo los parametros ilimitados
						$numero_invitados_dia_permitido=100;
						$numero_invitados_dia_permitido_socio=100;
					endif;
					
					
					
					
					if ((int)$numero_invitados_dia_permitido>0 && $numero_invitados_dia_permitido_socio >0){
					
					
							//Consulto cuantas veces la persona ha sido invitada en el mes 
							$mes_invitacion = substr($FechaIngreso,5,2);		
							$year_invitacion = substr($FechaIngreso,0,4);			
							$dia_invitacion = substr($FechaIngreso,8,2);			
							$sql_numero_invitacion = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '".$NumeroDocumento."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and IDClub = '".$IDClub."' and Estado = 'I'");
							$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
							
							
							//Consulto cuantas personas ha invitado el socio en el mes			
							$sql_invitados_mes = $dbo->query("Select * From SocioInvitado Where IDSocio = '".$IDSocio."' and YEAR(FechaIngreso) = '".$year_invitacion."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and IDClub = '".$IDClub."' and Estado = 'I'");
							$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
							
							//Cambio Sep 12: Se suma el total de invitados por accion padre
							//Consulto cuantas personas ha invitado el socio en el dia		
							//$sql_invitados_dia = $dbo->query("Select * From SocioInvitado Where IDSocio = '".$IDSocio."' and YEAR(FechaIngreso) = '".$year_invitacion."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and DAY(FechaIngreso) = '".$dia_invitacion."' and IDClub = '".$IDClub."' and Estado = 'I'");
							//$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
							$sql_invitados_dia = $dbo->query("Select * From SocioInvitado Where (IDSocio = '".$IDSocio."' ".$condicion_otro_socio.") and YEAR(FechaIngreso) = '".$year_invitacion."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and DAY(FechaIngreso) = '".$dia_invitacion."' and IDClub = '".$IDClub."'");
							$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
							
							$numero_invitados_mes_permitido = 5000;
							$numero_mismo_invitado_mes = "3000";							
							$cumplimiento_obligatorio_limite = "S";
			
							// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
							//$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );
							
							if ((int)$numero_invitados_dia<(int)$numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite=="N"){
							
							
							
									if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite=="N"){
										if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes || $cumplimiento_obligatorio_limite=="N"){
											
											//Verifico que el invitado no este invitado mas de una vez el mismo dia
											$sql_invitacion_dia = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '".$NumeroDocumento."' and FechaIngreso = '".$FechaIngreso."'");
											$numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
											if((int)$numero_invitaciones_dia<=0){
												$sql_seccion_not = $dbo->query("Insert Into SocioInvitado (IDClub, IDSocio, NumeroDocumento, Nombre, FechaIngreso, UsuarioTrCr, FechaTrCr) Values ('".$IDClub."','".$IDSocio."', '".$NumeroDocumento."', '".$Nombre."', '".$FechaIngreso."', 'WebService',NOW())");			
												$respuesta["message"] = "guardado";
												$respuesta["success"] = true;
												$respuesta["response"] = NULL;	
											}
											else{
												$respuesta["message"] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";
												$respuesta["success"] = false;
												$respuesta["response"] = NULL;					
											}
										}
										else{
											$respuesta["message"] = "Lo sentimos supera el numero maximo de ".$numero_invitados_mes_permitido." invitaciones por mes";
											$respuesta["success"] = false;
											$respuesta["response"] = NULL;					
										}
									}
									else{
										$respuesta["message"] = "Lo sentimos, esta persona ha sido invitadas mas de ".$numero_mismo_invitado_mes." veces en este mes.";
										$respuesta["success"] = false;
										$respuesta["response"] = NULL;				
									}
							}
							else{
								$respuesta["message"] = "Lo sentimos, supera el numero maximo de ".$numero_invitados_dia_permitido." invitaciones por dia";
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;				
							}
					}
					else{
						$respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;			
					}
					
				}
				else{
					$respuesta["message"] = "Error el socio no existe o no pertenece al club";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
				
		}
		else{
			$respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "14. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}
	
	
	
function set_autorizacion_contratista($IDClub,$IDSocio,$TipoAutorizacion,$FechaIngreso,$FechaSalida,$TipoDocumento,$NumeroDocumento,$Nombre,$Apellido,$Email,$Placa,$Admin="",$HoraInicio="",$HoraSalida="",$Observaciones="",$IDUsuario="",$Telefono="",$FechaNacimiento="",$TipoSangre="",$Predio="")
{	
	$dbo =& SIMDB::get();
	
	
	if( !empty( $FechaIngreso ) && !empty( $FechaSalida ) && !empty( $TipoAutorizacion ) && !empty( $TipoDocumento ) && !empty( $NumeroDocumento ) && !empty( $Nombre ) && !empty( $Apellido )  ){
		
		$NumeroDocumento = str_replace(".","",$NumeroDocumento);
		$NumeroDocumento = str_replace(",","",$NumeroDocumento);	
		
		$hoy = date("Y-m-d");
		if(strtotime($FechaIngreso)>=strtotime($hoy)){
		
				//verifico que el socio exista y pertenezca al club
				$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
				
				if (!empty($id_socio)){
				
					// Consulto las invitaciones que puede hacer el socio
					$numero_invitados_dia_permitido = $dbo->getFields( "Socio" , "NumeroAccesos" , "IDSocio = '".$IDSocio."'" );
					
					if ((int)$numero_invitados_dia_permitido>0){
						
									//Consulto cuantas veces la persona ha sido invitada en el mes 
									$mes_invitacion = substr($FechaIngreso,5,2);		
									$year_invitacion = substr($FechaIngreso,0,4);			
									$dia_invitacion = substr($FechaIngreso,8,2);		
									
									//verifico si el invitado ya esta creado
									
									$id_invitado = $dbo->getFields( "Invitado" , "IDInvitado" , "NumeroDocumento = '".trim($NumeroDocumento)."'" );
									
									//Si el invitado no existe en la tabla maestra lo creo
									if(empty($id_invitado) || (int)$id_invitado==0 ):
										$inserta_invitado = "Insert Into Invitado (IDClub, IDTipoDocumento, NumeroDocumento, Nombre, Apellido, Email, ObservacionGeneral, Telefono, FechaNacimiento, TipoSangre, Predio, UsuarioTrCr, FechaTrCr)
															 Values('".$IDClub."', '".$TipoDocumento."','".trim($NumeroDocumento)."','".$Nombre."','".$Apellido."','".$Email."','".$Observaciones."','".$Telefono."','".$FechaNacimiento."','".$TipoSangre."','".$Predio."','App',NOW())";
										$dbo->query($inserta_invitado);					 
										$id_invitado = $dbo->lastID();
									else:
										//Actualizo las observaciones y predio unicamente
										$sql_edit_invitado ="Update Invitado Set Telefono = '".$Telefono."', FechaNacimiento = '".$FechaNacimiento."', TipoSangre = '".$TipoSangre."', Predio = '".$Predio."', ObservacionGeneral = '".$Observaciones."' Where IDInvitado = '".$id_invitado."'";
										$dbo->query($sql_edit_invitado);					 
									endif;
									
									//Si es cabeza de familia guardo el id del Socio
									if($CabezaInvitacion=="S"):
										$IDPadre = $id_invitado;
									endif;
									
									
									$sql_numero_invitacion = $dbo->query("Select * From SocioAutorizacion Where IDInvitado = '".$id_invitado."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
									//Consulto cuantas personas ha invitado el socio en el mes			
									$sql_invitados_mes = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '".$IDSocio."' and YEAR(FechaInicio) = '".$year_invitacion."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
									//Consulto cuantas personas ha invitado el socio en el dia		
									$sql_invitados_dia = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '".$IDSocio."' and YEAR(FechaInicio) = '".$year_invitacion."' and MONTH(FechaInicio) = '".$mes_invitacion."' and DAY(FechaInicio) = '".$dia_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
									
									$numero_invitados_mes_permitido = 50000;
									$numero_mismo_invitado_mes = "30000";							
									$cumplimiento_obligatorio_limite = "S";
					
									// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
									//$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );
									
									if ((int)$numero_invitados_dia<(int)$numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite=="N"){
											if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite=="N"){
												if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes || $cumplimiento_obligatorio_limite=="N"){
													//Verifico que el invitado no este invitado mas de una vez el mismo dia
													//echo "Select * From SocioAutorizacion Where IDInvitado = '".$id_invitado."' and FechaInicio = '".$FechaIngreso."'";
													//exit;
													$sql_invitacion_dia = $dbo->query("Select * From SocioAutorizacion Where IDInvitado = '".$id_invitado."' and FechaInicio = '".$FechaIngreso."'");
													$numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
													if((int)$numero_invitaciones_dia<=0){
														
														//verifico si el vehiculo ya esta creado
														if(!empty($Placa)):
															$id_vehiculo = $dbo->getFields( "Vehiculo" , "IDVehiculo" , "Placa = '".$Placa."'" );
															//Si el vehiculo no existe en la tabla maestra lo creo
															if(empty($id_vehiculo) || (int)$id_vehiculo==0 ):
																$inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa)
																					 Values('".$id_invitado."','".$Placa."')";
																$dbo->query($inserta_vehiculo);					 
																$id_vehiculo = $dbo->lastID();
															endif;
														endif;	
														
														
														//genero el codigo de autorizacion
														$CodigoAutorizacion = SIMUtil::genera_codigo_autorizacion("C");
														
														if(!empty($IDUsuario))
															$creado_por = $IDUsuario;
														else
															$creado_por = "Socio";	
														
														//Inserto invitacion														
														$sql_inserta_inv = $dbo->query("Insert Into SocioAutorizacion (IDClub, IDSocio, IDInvitado, IDVehiculo, TipoAutorizacion, FechaInicio, HoraInicio, FechaFin, HoraFin, CodigoAutorizacion, Predio, UsuarioTrCr, FechaTrCr) 
																						Values ('".$IDClub."','".$IDSocio."', '".$id_invitado."', '".$id_vehiculo."', '".$TipoAutorizacion."', '".$FechaIngreso."', '".$HoraInicio."','".$FechaSalida."', '".$HoraSalida."', '".$CodigoAutorizacion."','".$Predio."', '".$creado_por."',NOW())");
														$id_invitado_inserta = $dbo->lastID();								
														
														//Guardo el padre de la invitacion														
															if(!empty($id_invitado_inserta)):	
																//Generar Codigo QR
																//$parametros_codigo_qr = URLROOT . "plataform/invitadosespeciales.php?IDInvitacion=".$id_invitado_inserta."&Placa=".$Placa;
																$parametros_codigo_qr = $NumeroDocumento."\r\n";
																if (empty($Admin)):
																	SIMUtil::enviar_codigo_qr($id_invitado_inserta,$parametros_codigo_qr,"Contratista");
																endif;	
															endif;
														
														$respuesta["message"] = "guardado";
														$respuesta["success"] = true;
														$respuesta["response"] = NULL;
														
														
													}
													else{
														$respuesta["message"] = "Lo sentimos ".$Nombre." ".$Apellido." ya tiene una invitacion para el dia seleccionado.";
														$respuesta["success"] = false;
														$respuesta["response"] = NULL;					
													}
										}
										else{
											$respuesta["message"] = "Lo sentimos supera el numero maximo de ".$numero_invitados_mes_permitido." invitaciones por mes";
											$respuesta["success"] = false;
											$respuesta["response"] = NULL;					
										}
									}
									else{
										$respuesta["message"] = "Lo sentimos, ".$Nombre." ".$Apellido."  ha sido invitadas mas de ".$numero_mismo_invitado_mes." veces en este mes.";
										$respuesta["success"] = false;
										$respuesta["response"] = NULL;				
									}
							}
							else{
								$respuesta["message"] = "Lo sentimos, supera el numero maximo de ".$numero_invitados_dia_permitido." invitaciones por dia";
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;				
							}
							
					}
					else{
						$respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;			
					}
					
				}
				else{
					$respuesta["message"] = "Error el socio no existe o no pertenece al club";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
				
		}
		else{
			$respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "Inv. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}
	
	
function set_contratista_update_autorizacion($IDClub,$IDSocio,$IDInvitacion,$TipoAutorizacion,$FechaIngreso,$FechaSalida,$TipoDocumento,$NumeroDocumento,$Nombre,$Apellido,$Email,$Placa,$Admin="",$HoraInicio="",$HoraSalida="",$Observaciones="",$IDUsuario="",$Telefono="",$FechaNacimiento="",$TipoSangre="",$Predio="")
{	
	$dbo =& SIMDB::get();
	
	
	if( !empty( $IDInvitacion ) && !empty( $FechaIngreso ) && !empty( $FechaSalida ) && !empty( $TipoAutorizacion ) && !empty( $TipoDocumento ) && !empty( $NumeroDocumento ) && !empty( $Nombre ) && !empty( $Apellido )   ){
		
		$hoy = date("Y-m-d");
		if(strtotime($FechaIngreso)>=strtotime($hoy)){
			
			$NumeroDocumento = str_replace(".","",$NumeroDocumento);
			$NumeroDocumento = trim(str_replace(",","",$NumeroDocumento));
		
				//verifico que el socio exista y pertenezca al club
				$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
				
				if (!empty($id_socio)){
				
					// Consulto las invitaciones que puede hacer el socio
					$numero_invitados_dia_permitido = $dbo->getFields( "Socio" , "NumeroAccesos" , "IDSocio = '".$IDSocio."'" );
					
					if ((int)$numero_invitados_dia_permitido>0){
						
									//Consulto cuantas veces la persona ha sido invitada en el mes 
									$mes_invitacion = substr($FechaIngreso,5,2);		
									$year_invitacion = substr($FechaIngreso,0,4);			
									$dia_invitacion = substr($FechaIngreso,8,2);		
									
									//verifico si el invitado ya esta creado
									$id_invitado = $dbo->getFields( "Invitado" , "IDInvitado" , "NumeroDocumento = '".$NumeroDocumento."'" );
									//Si el invitado no existe en la tabla maestra lo creo
									if(empty($id_invitado) || (int)$id_invitado==0 ):
										$inserta_invitado = "Insert Into Invitado (IDClub, IDTipoDocumento, NumeroDocumento, Nombre, Apellido, Email, Telefono, FechaNacimiento, TipoSangre, Predio, UsuarioTrCr, FechaTrCr)
															 Values('".$IDClub."','".$IDTipoDocumento."','".$NumeroDocumento."','".$Nombre."','".$Apellido."','".$Email."','".$Telefono."','".$FechaNacimiento."','".$TipoSangre."','".$Predio."','App',NOW())";
										$dbo->query($inserta_invitado);					 
										$id_invitado = $dbo->lastID();
									else:
									
										$sql_invitado_update = $dbo->query("Update Invitado 
														   Set IDTipoDocumento = '".$TipoDocumento."', NumeroDocumento = '".$NumeroDocumento."', Nombre = '".$Nombre."', 
														   Apellido = '".$Apellido."',Email = '".$Email."', Telefono='".$Telefono."', FechaNacimiento = '".$FechaNacimiento."', TipoSangre = '".$TipoSangre."', Predio = '".$Predio."', ObservacionGeneral = '".$Observaciones."'
														   Where IDInvitado = '".$id_invitado."'");	
									endif;
									
									
									
									$sql_numero_invitacion = $dbo->query("Select * From SocioAutorizacion Where IDInvitado = '".$id_invitado."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
									//Consulto cuantas personas ha invitado el socio en el mes			
									$sql_invitados_mes = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '".$IDSocio."' and YEAR(FechaInicio) = '".$year_invitacion."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
									//Consulto cuantas personas ha invitado el socio en el dia		
									$sql_invitados_dia = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '".$IDSocio."' and YEAR(FechaInicio) = '".$year_invitacion."' and MONTH(FechaInicio) = '".$mes_invitacion."' and DAY(FechaInicio) = '".$dia_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
									
									$numero_invitados_mes_permitido = 50000;
									$numero_mismo_invitado_mes = "30000";							
									$cumplimiento_obligatorio_limite = "S";
					
									// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
									//$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );
									
									if ((int)$numero_invitados_dia<(int)$numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite=="N"){
											if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite=="N"){
												if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes || $cumplimiento_obligatorio_limite=="N"){
													//Verifico que el invitado no este invitado mas de una vez el mismo dia
													
													$sql_invitacion_dia = $dbo->query("Select * From SocioAutorizacion Where IDInvitado = '".$id_invitado."' and FechaInicio = '".$FechaIngreso."'");
													$numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
													if((int)$numero_invitaciones_dia<=100){
														
														//verifico si el vehiculo ya esta creado
														if(!empty($Placa)):
															$id_vehiculo = $dbo->getFields( "Vehiculo" , "IDVehiculo" , "Placa = '".$Placa."'" );
															//Si el vehiculo no existe en la tabla maestra lo creo
															if(empty($id_vehiculo) || (int)$id_vehiculo==0 ):
																$inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa)
																					 Values('".$id_invitado."','".$Placa."')";
																$dbo->query($inserta_vehiculo);					 
																$id_vehiculo = $dbo->lastID();
															endif;
														endif;	
														
														if(!empty($IDUsuario))
															$creado_por = $IDUsuario;
														else
															$creado_por = "Socio";
														
														//Actualizo invitacion
														$sql_inserta_inv = $dbo->query("Update SocioAutorizacion set  IDVehiculo = '".$id_vehiculo."', TipoAutorizacion = '".$TipoAutorizacion."', FechaInicio = '".$FechaIngreso."', FechaFin = '".$FechaSalida."', Predio = '".$Predio."', UsuarioTrEd = '".$creado_por."', FechaTrEd = NOW() Where IDSocioAutorizacion = '".$IDInvitacion."'");														
														$respuesta["message"] = "actualizado";
														$respuesta["success"] = true;
														$respuesta["response"] = NULL;
														
														
													}
													else{
														$respuesta["message"] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";
														$respuesta["success"] = false;
														$respuesta["response"] = NULL;					
													}
										}
										else{
											$respuesta["message"] = "Lo sentimos supera el numero maximo de ".$numero_invitados_mes_permitido." invitaciones por mes";
											$respuesta["success"] = false;
											$respuesta["response"] = NULL;					
										}
									}
									else{
										$respuesta["message"] = "Lo sentimos, esta persona ha sido invitadas mas de ".$numero_mismo_invitado_mes." veces en este mes.";
										$respuesta["success"] = false;
										$respuesta["response"] = NULL;				
									}
							}
							else{
								$respuesta["message"] = "Lo sentimos, supera el numero maximo de ".$numero_invitados_dia_permitido." invitaciones por dia";
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;				
							}
							
					}
					else{
						$respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;			
					}
					
				}
				else{
					$respuesta["message"] = "Error el socio no existe o no pertenece al club";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
				
		}
		else{
			$respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "Inv. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	


	
function set_invitadoV1($IDClub,$IDSocio,$NumeroDocumento,$Nombre,$FechaIngreso)
{	
	$dbo =& SIMDB::get();	
	
	
	
	if( !empty( $NumeroDocumento ) && !empty( $Nombre )  && !empty( $FechaIngreso ) ){
		
		$hoy = date("Y-m-d");
		if(strtotime($FechaIngreso)>=strtotime($hoy)){
		
				//verifico que el socio exista y pertenezca al club
				$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
				
				if (!empty($id_socio)){
				
					// Consulto las reglas que aplica al socio para invitaciones
					$array_datos_regla = SIMUtil::consulta_regla_invitacion($IDSocio,$IDClub);
					
					if ((int)$array_datos_regla["IDRegla"]>0){
					
					
					
					
							//Consulto cuantas veces la persona ha sido invitada en el mes 
							$mes_invitacion = substr($FechaIngreso,5,2);		
							$year_invitacion = substr($FechaIngreso,0,4);			
							$dia_invitacion = substr($FechaIngreso,8,2);			
							$sql_numero_invitacion = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '".$NumeroDocumento."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and IDClub = '".$IDClub."' and Estado = 'I'");
							$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
							//Consulto cuantas personas ha invitado el socio en el mes			
							$sql_invitados_mes = $dbo->query("Select * From SocioInvitado Where IDSocio = '".$IDSocio."' and YEAR(FechaIngreso) = '".$year_invitacion."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and IDClub = '".$IDClub."' and Estado = 'I'");
							$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
							//Consulto cuantas personas ha invitado el socio en el dia		
							$sql_invitados_dia = $dbo->query("Select * From SocioInvitado Where IDSocio = '".$IDSocio."' and YEAR(FechaIngreso) = '".$year_invitacion."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and DAY(FechaIngreso) = '".$dia_invitacion."' and IDClub = '".$IDClub."' and Estado = 'I'");
							$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
							
							
							
							//$numero_invitados_mes_permitido = $dbo->getFields( "Club" , "MaximoInvitadoSocio" , "IDClub = '".$IDClub."'" );
							//$numero_mismo_invitado_mes = $dbo->getFields( "Club" , "MaximoRepeticionInvitado" , "IDClub = '".$IDClub."'" );
							
							$numero_invitados_mes_permitido = $array_datos_regla["MaximoInvitadoSocio"];
							$numero_mismo_invitado_mes = $array_datos_regla["MaximoRepeticionInvitado"];
							$numero_invitados_dia_permitido = $array_datos_regla["MaximoInvitadoDia"];
							$cumplimiento_obligatorio_limite = $array_datos_regla["CumplimientoInvitados"];
			
							// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
							//$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );
							
							
		
							
							if ((int)$numero_invitados_dia<(int)$numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite=="N"){
							
									if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite=="N"){
										if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes || $cumplimiento_obligatorio_limite=="N"){
											
											//Verifico que el invitado no este invitado mas de una vez el mismo dia
											$sql_invitacion_dia = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '".$NumeroDocumento."' and FechaIngreso = '".$FechaIngreso."'");
											$numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
											if((int)$numero_invitaciones_dia<=0){
												$sql_seccion_not = $dbo->query("Insert Into SocioInvitado (IDClub, IDSocio, NumeroDocumento, Nombre, FechaIngreso, UsuarioTrCr, FechaTrCr) Values ('".$IDClub."','".$IDSocio."', '".$NumeroDocumento."', '".$Nombre."', '".$FechaIngreso."', 'WebService',NOW())");			
												$respuesta["message"] = "guardado";
												$respuesta["success"] = true;
												$respuesta["response"] = NULL;	
											}
											else{
												$respuesta["message"] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";
												$respuesta["success"] = false;
												$respuesta["response"] = NULL;					
											}
										}
										else{
											$respuesta["message"] = "Lo sentimos supera el numero maximo de ".$numero_invitados_mes_permitido." invitaciones por mes";
											$respuesta["success"] = false;
											$respuesta["response"] = NULL;					
										}
									}
									else{
										$respuesta["message"] = "Lo sentimos, esta persona ha sido invitadas mas de ".$numero_mismo_invitado_mes." veces en este mes.";
										$respuesta["success"] = false;
										$respuesta["response"] = NULL;				
									}
							}
							else{
								$respuesta["message"] = "Lo sentimos, supera el numero maximo de ".$numero_invitados_dia_permitido." invitaciones por dia";
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;				
							}
					}
					else{
						$respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;			
					}
					
				}
				else{
					$respuesta["message"] = "Error el socio no existe o no pertenece al club";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
				
		}
		else{
			$respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "15. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}
	
	
	
function set_invitado_update($IDClub,$IDSocio,$IDInvitado,$NumeroDocumento,$Nombre,$FechaIngreso)
{	
	$dbo =& SIMDB::get();	
	
	
	
	if( !empty( $NumeroDocumento ) && !empty( $Nombre )  && !empty( $FechaIngreso ) ){
		
		$NumeroDocumento = str_replace(".","",$NumeroDocumento);
		$NumeroDocumento = trim(str_replace(",","",$NumeroDocumento));
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			
			//Consulto cuantas veces la persona ha sido invitada en el mes 
			$mes_invitacion = substr($FechaIngreso,5,2);			
			$sql_numero_invitacion = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '".$NumeroDocumento."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
			$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
			//Consulto cuantas personas ha invitado el socio en el mes			
			$sql_invitados_mes = $dbo->query("Select * From SocioInvitado Where IDSocio = '".$IDSocio."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
			$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
	
			
			
			$numero_invitados_mes_permitido = $dbo->getFields( "Club" , "MaximoInvitadoSocio" , "IDClub = '".$IDClub."'" );
			$numero_mismo_invitado_mes = $dbo->getFields( "Club" , "MaximoRepeticionInvitado" , "IDClub = '".$IDClub."'" );
			// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
			$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );
			
			
			if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite=="N"){
				if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes || $cumplimiento_obligatorio_limite=="N"){
					$sql_invitado_update = $dbo->query("Update SocioInvitado 
													   Set NumeroDocumento = '".$NumeroDocumento."', Nombre = '".$Nombre."', FechaIngreso = '".$FechaIngreso."'
													   Where IDSocioInvitado = '".$IDInvitado."'");	
					$respuesta["message"] = "guardado";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;	
				}
				else{
					$respuesta["message"] = "Lo sentimos supera el numero maximo de ".$numero_invitados_mes_permitido." invitaciones por mes";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;					
				}
			}
			else{
				$respuesta["message"] = "Lo sentimos, esta persona ha sido invitadas mas de ".$numero_mismo_invitado_mes." veces en este mes.";
				$respuesta["success"] = true;
				$respuesta["response"] = NULL;				
			}
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "16. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
	
function set_contratista_update($IDClub,$IDSocio,$TipoAutorizacion,$FechaIngreso,$FechaSalida,$TipoDocumento,$NumeroDocumento,$Nombre,$Apellido,$Email,$Placa)
{	
	$dbo =& SIMDB::get();	
	
	
	
	if( !empty( $NumeroDocumento ) && !empty( $Nombre )  && !empty( $Apellido ) ){
		
		$NumeroDocumento = str_replace(".","",$NumeroDocumento);
		$NumeroDocumento = trim(str_replace(",","",$NumeroDocumento));
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
					
					
					$id_invitado = $dbo->getFields( "Invitado" , "IDInvitado" , "NumeroDocumento = '" . $NumeroDocumento);
					if(!empty($id_invitado)){
				
						$sql_invitado_update = $dbo->query("Update Invitado 
														   Set IDTipoDocumento = '".$TipoDocumento."', NumeroDocumento = '".$NumeroDocumento."', Nombre = '".$Nombre."', 
														   Apellido = '".$Apellido."',Email = '".$Email."'
														   Where IDInvitado = '".$id_invitado."'");	
						//verifico si el vehiculo ya esta creado
						if(!empty($Placa)):
							$id_vehiculo = $dbo->getFields( "Vehiculo" , "IDVehiculo" , "Placa = '".$Placa."'" );
							//Si el vehiculo no existe en la tabla maestra lo creo
							if(empty($id_vehiculo) || (int)$id_vehiculo==0 ):
								$inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa)
													 Values('".$id_invitado."','".$Placa."')";
								$dbo->query($inserta_vehiculo);					 
								$id_vehiculo = $dbo->lastID();
							endif;
						endif;									   
														   
						$respuesta["message"] = "guardado";
						$respuesta["success"] = true;
						$respuesta["response"] = NULL;	
					}
					else{
						$respuesta["message"] = "El contratista no existe";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					}
			}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "16. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}		
	
	
function cancela_invitacion($IDClub,$IDSocio,$IDInvitacion)
{	
	$dbo =& SIMDB::get();	
	
	if( !empty( $IDSocio ) && !empty( $IDInvitacion ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			$datos_tipo_reserva = $dbo->fetchAll( "SocioInvitado", " IDSocioInvitado = '" . $IDInvitacion . "' ", "array" );						
			if(!empty($datos_tipo_reserva["IDSocioInvitado"])){
				if ($datos_tipo_reserva["Estado"]!="I"){
					$sql_cancela_invitacion = $dbo->query("delete  From SocioInvitado Where IDSocioInvitado = '".$IDInvitacion."' and IDSocio = '".$IDSocio."' and IDClub = '".$IDClub."'  Limit 1");	
					$respuesta["message"] = "invitacion cancelada";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;					
				}
				else{
					$respuesta["message"] = "Lo sentimos, no se puede cancelar la invitacion, el invitado ya ingreso";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
				}
			}
			else{
				$respuesta["message"] = "Lo sentimos, la invitacion no existe";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "17. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function cancela_autorizacion_invitado($IDClub,$IDSocio,$IDInvitacion)
{	
	$dbo =& SIMDB::get();	
	
	if( !empty( $IDSocio ) && !empty( $IDInvitacion ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
					
			$datos_tipo_reserva = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array" );						
			if(!empty($datos_tipo_reserva["IDSocioInvitadoEspecial"])){
				if ($datos_tipo_reserva["Ingreso"]!="S"){					
					$sql_cancela_invitacion = $dbo->query("delete  From SocioInvitadoEspecial Where IDSocioInvitadoEspecial = '".$IDInvitacion."' and IDSocio = '".$IDSocio."' and IDClub = '".$IDClub."'  Limit 1");	
					// Si es cabeza de grupo borro las invitaciones asociadas al cabeza de grupo
					if($datos_tipo_reserva["CabezaInvitacion"]=="S"):						
						$sql_cancela_invitacion_hijos = $dbo->query("delete From SocioInvitadoEspecial Where IDPadre = '".$datos_tipo_reserva["IDInvitado"]."' and IDSocio = '".$IDSocio."' and IDClub = '".$IDClub."' and FechaInicio = '".$datos_tipo_reserva["FechaInicio"]."' and FechaFin = '".$datos_tipo_reserva["FechaFin"]."'");
					endif;
					
					$respuesta["message"] = "invitacion cancelada";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;					
				}
				else{
					$respuesta["message"] = "Lo sentimos, no se puede cancelar la invitacion, el invitado ya ingreso";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
				}
			}
			else{
				$respuesta["message"] = "Lo sentimos, la invitacion no existe";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "17. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
	
function cancela_autorizacion_contratista($IDClub,$IDSocio,$IDAutorizacion)
{	
	$dbo =& SIMDB::get();	
	
	if( !empty( $IDSocio ) && !empty( $IDAutorizacion ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
					
			$datos_tipo_reserva = $dbo->fetchAll( "SocioAutorizacion", " IDSocioAutorizacion = '" . $IDAutorizacion . "' ", "array" );						
			if(!empty($datos_tipo_reserva["IDSocioAutorizacion"])){
				if ($datos_tipo_reserva["Ingreso"]!="S"){
					$sql_cancela_invitacion = $dbo->query("delete  From SocioAutorizacion Where IDSocioAutorizacion = '".$IDAutorizacion."' and IDSocio = '".$IDSocio."' and IDClub = '".$IDClub."'  Limit 1");	
					$respuesta["message"] = "autorizacion cancelada";
					$respuesta["success"] = true;
					$respuesta["response"] = NULL;					
				}
				else{
					$respuesta["message"] = "Lo sentimos, no se puede cancelar la autorizacion, el invitado ya ingreso";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
				}
			}
			else{
				$respuesta["message"] = "Lo sentimos, la autorizacion no existe";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "17. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	


function set_autorizacion_invitado($IDClub,$IDSocio,$FechaIngreso,$FechaSalida,$DatosInvitado)
{	
	
	$dbo =& SIMDB::get();
	

	$datos_invitado= json_decode($DatosInvitado, true);
	
	
	if( !empty( $FechaIngreso ) && !empty( $FechaSalida ) && count($datos_invitado)>0 ){
		
		$hoy = date("Y-m-d");
		
		if(strtotime($FechaIngreso)>=strtotime($hoy)){
		
				//verifico que el socio exista y pertenezca al club
				$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
				
				if (!empty($id_socio)){
					
					
				
					// Consulto las invitaciones que puede hacer el socio
					$numero_invitados_dia_permitido = $dbo->getFields( "Socio" , "NumeroAccesos" , "IDSocio = '".$IDSocio."'" );
					
					if ((int)$numero_invitados_dia_permitido>0){
	
						
							//Consulto cuantas veces la persona ha sido invitada en el mes 
							$mes_invitacion = substr($FechaIngreso,5,2);		
							$year_invitacion = substr($FechaIngreso,0,4);			
							$dia_invitacion = substr($FechaIngreso,8,2);		
							
							//Recorro los datos de los invitados
							if (count($datos_invitado)>0):
							
								//genero el codigo de autorizacion
								$CodigoAutorizacion = SIMUtil::genera_codigo_autorizacion("I");
							
								foreach($datos_invitado as $detalle_datos):
									$IDTipoDocumento = $detalle_datos["IDTipoDocumento"];
									$NumeroDocumento = $detalle_datos["NumeroDocumento"];
									$Nombre = $detalle_datos["Nombre"];
									$Apellido = $detalle_datos["Apellido"];
									$Email = $detalle_datos["Email"];
									$TipoInvitado = $detalle_datos["TipoInvitado"];
									$Placa = $detalle_datos["Placa"];
									$CabezaInvitacion = $detalle_datos["CabezaInvitacion"];
									$MenorEdad = $detalle_datos["MenorEdad"];
									
									if($MenorEdad=="S" || (empty($IDTipoDocumento) && empty($NumeroDocumento) && empty($Email) ) ):
										$NumeroDocumento = "MenorEdad".rand ( 1 , 1000000 );
										$IDTipoDocumento  = 1;
									endif;	
									
									
									//verifico si el invitado ya esta creado
									$id_invitado = $dbo->getFields( "Invitado" , "IDInvitado" , "NumeroDocumento = '".$NumeroDocumento."'" );
									
									
									//Si el invitado no existe en la tabla maestra lo creo
									if(empty($id_invitado) || (int)$id_invitado==0 ):
										$inserta_invitado = "Insert Into Invitado (IDCLub, IDTipoDocumento, NumeroDocumento, Nombre, Apellido, Email, MenorEdad, UsuarioTrCr, FechaTrCr)
															 Values('".$IDClub."', '".$IDTipoDocumento."','".$NumeroDocumento."','".$Nombre."','".$Apellido."','".$Email."','".$MenorEdad."','App',NOW())";
										
										$dbo->query($inserta_invitado);					 
										$id_invitado = $dbo->lastID();
									else:
									// Si ya existe actualizo los datos basicos	
									$sql_actualizao_datos_invitado = "Update Invitado Set Email = '".$Email."' Where IDInvitado = '".$id_invitado."'";
									$dbo->query($sql_actualizao_datos_invitado);	
									endif;
									
									//Si es cabeza de familia guardo el id del Socio
									if($CabezaInvitacion=="S"):
										$IDPadre = $id_invitado;
									endif;
									
									
									$sql_numero_invitacion = $dbo->query("Select * From SocioInvitadoEspecial Where IDInvitado = '".$id_invitado."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
									//Consulto cuantas personas ha invitado el socio en el mes			
									$sql_invitados_mes = $dbo->query("Select * From SocioInvitadoEspecial Where IDSocio = '".$IDSocio."' and YEAR(FechaInicio) = '".$year_invitacion."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
									//Consulto cuantas personas ha invitado el socio en el dia		
									$sql_invitados_dia = $dbo->query("Select * From SocioInvitadoEspecial Where IDSocio = '".$IDSocio."' and YEAR(FechaInicio) = '".$year_invitacion."' and MONTH(FechaInicio) = '".$mes_invitacion."' and DAY(FechaInicio) = '".$dia_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
									
									$numero_invitados_mes_permitido = 500;
									$numero_mismo_invitado_mes = "100";							
									$cumplimiento_obligatorio_limite = "S";
					
									// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
									//$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );
									
									if ((int)$numero_invitados_dia<(int)$numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite=="N"){										
											if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite=="N"){												
												if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes || $cumplimiento_obligatorio_limite=="N"){
													//Verifico que el invitado no este invitado mas de una vez el mismo dia
													
													$sql_invitacion_dia = $dbo->query("Select * From SocioInvitadoEspecial Where IDInvitado = '".$id_invitado."' and FechaInicio = '".$FechaIngreso."'");
													$numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
													if((int)$numero_invitaciones_dia<=100){
														
														//verifico si el vehiculo ya esta creado
														if(!empty($Placa)):
															$id_vehiculo = $dbo->getFields( "Vehiculo" , "IDVehiculo" , "Placa = '".$Placa."'" );
															//Si el vehiculo no existe en la tabla maestra lo creo
															if(empty($id_vehiculo) || (int)$id_vehiculo==0 ):
																$inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa)
																					 Values('".$id_invitado."','".$Placa."')";
																$dbo->query($inserta_vehiculo);					 
																$id_vehiculo = $dbo->lastID();
															endif;
														endif;	
														
												
												
													
														//Inserto invitacion
														
														
														$sql_inserta_inv = $dbo->query("Insert Into SocioInvitadoEspecial (IDClub, IDSocio, IDInvitado, IDPadre, IDPadreInvitacion, IDVehiculo, CodigoAutorizacion, CabezaInvitacion,  TipoInvitacion, FechaInicio, FechaFin, UsuarioTrCr, FechaTrCr) 
																						Values ('".$IDClub."','".$IDSocio."', '".$id_invitado."', '".$IDPadre."','".$IDInvitacionGenerada."', '".$id_vehiculo."', '".$CodigoAutorizacion."','".$CabezaInvitacion."', '".$TipoInvitado."', '".$FechaIngreso."', '".$FechaSalida."', 'WebService',NOW())");
														$id_invitado_inserta = $dbo->lastID();								
														//Inserto el vehiculo de la invitacion
														if(!empty($Placa)):
															$inserta_vehiculo_inv = "Insert Into VehiculoInvitacion (IDSocioInvitadoEspecial, IDVehiculo, Placa)
																						 Values('".$id_invitado_inserta."','".$id_vehiculo."','".$Placa."')";
															$dbo->query($inserta_vehiculo_inv);									
														endif;
														
														
														//Guardo el padre de la invitacion														
															if( ($CabezaInvitacion=="S" || count($datos_invitado)==1)  && empty($IDInvitacionGenerada) ):																
																//Generar Codigo QR
																//$parametros_codigo_qr = URLROOT . "plataform/invitadosespeciales.php?IDInvitacion=".$id_invitado_inserta."&Placa=".$Placa;
																$parametros_codigo_qr = $NumeroDocumento."\r\n";
																SIMUtil::enviar_codigo_qr($id_invitado_inserta,$parametros_codigo_qr,"Invitado");
																if($CabezaInvitacion=="S" && empty($IDInvitacionGenerada)):
																	$IDInvitacionGenerada = $id_invitado_inserta;
																endif;	
															endif;
														
													}
													else{
														$array_errorres[] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";																	
													}
												}
												else{
													$array_errorres[] = "Lo sentimos supera el numero maximo de ".$numero_invitados_mes_permitido." invitaciones por mes";																	
												}
											}
											else{
												$array_errorres[] = "Lo sentimos, esta persona ha sido invitadas mas de ".$numero_mismo_invitado_mes." veces en este mes.";															
											}
									}
									else{
										$array_errorres[] = "Lo sentimos, supera el numero maximo de ".$numero_invitados_dia_permitido." invitaciones por dia";													
									}
								endforeach;
							endif;	
							
							if(count($array_errorres)>0):
								$otros_mensajes = implode(",",$array_errorres);
							endif;
							
							$respuesta["message"] = "guardado " . $otros_mensajes;
							$respuesta["success"] = true;
							$respuesta["response"] = NULL;	
							
					}
					else{
						$respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;			
					}
					
				}
				else{
					$respuesta["message"] = "Error el socio no existe o no pertenece al club";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
				
		}
		else{
			$respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "Inv acceso. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function set_autorizacion_invitado_update($IDClub,$IDSocio,$IDInvitacion,$FechaIngreso,$FechaSalida,$DatosInvitado)
{	

	$dbo =& SIMDB::get();
	
	$datos_invitado= json_decode($DatosInvitado, true);
	
	
	if( !empty( $FechaIngreso ) && !empty( $FechaSalida ) && !empty( $IDInvitacion ) && count($datos_invitado)>0 ){
		
		
		
		$hoy = date("Y-m-d");
		if(strtotime($FechaIngreso)>=strtotime($hoy)){
		
				//verifico que el socio exista y pertenezca al club
				$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
				
				if (!empty($id_socio)){
				
					// Consulto las invitaciones que puede hacer el socio
					$numero_invitados_dia_permitido = $dbo->getFields( "Socio" , "NumeroAccesos" , "IDSocio = '".$IDSocio."'" );
					
					if ((int)$numero_invitados_dia_permitido>0){
						
						
							//Consulto cuantas veces la persona ha sido invitada en el mes 
							$mes_invitacion = substr($FechaIngreso,5,2);		
							$year_invitacion = substr($FechaIngreso,0,4);			
							$dia_invitacion = substr($FechaIngreso,8,2);		
							
							//Recorro los datos de los invitados
							if (count($datos_invitado)>0):
								//Borro las invitaciones para volverlas a crear
								$datos_invitacion_especial = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array" );								
								$sql_borra_inv = $dbo->query("Delete From SocioInvitadoEspecial Where IDPadre = '".$datos_invitacion_especial["IDInvitado"]."'");
								$sql_borra_inv = $dbo->query("Delete From SocioInvitadoEspecial Where IDSocioInvitadoEspecial = '".$IDInvitacion."'");
							
								foreach($datos_invitado as $detalle_datos):
									//$IDInvitacion = $detalle_datos["IDInvitacion"];
									$IDTipoDocumento = $detalle_datos["IDTipoDocumento"];
									$NumeroDocumento = $detalle_datos["NumeroDocumento"];
									$Nombre = $detalle_datos["Nombre"];
									$Apellido = $detalle_datos["Apellido"];
									$Email = $detalle_datos["Email"];
									$TipoInvitado = $detalle_datos["TipoInvitado"];
									$Placa = $detalle_datos["Placa"];
									$CabezaInvitacion = $detalle_datos["CabezaInvitacion"];									
									$MenorEdad = $detalle_datos["MenorEdad"];
									
									if($MenorEdad=="S" || (empty($IDTipoDocumento) && empty($NumeroDocumento) && empty($Email) )):
										$NumeroDocumento = "MenorEdad".rand ( 1 , 1000000 );
										$IDTipoDocumento  = 1;
									endif;	
									
									
									//verifico si el invitado ya esta creado
									$id_invitado = $dbo->getFields( "Invitado" , "IDInvitado" , "NumeroDocumento = '".$NumeroDocumento."'" );
									//Si el invitado no existe en la tabla maestra lo creo
									if(empty($id_invitado) || (int)$id_invitado==0 ):
										$inserta_invitado = "Insert Into Invitado (IDCLub, IDTipoDocumento, NumeroDocumento, Nombre, Apellido, Email, MenorEdad, UsuarioTrCr, FechaTrCr)
															 Values('".$IDClub."','".$IDTipoDocumento."','".$NumeroDocumento."','".$Nombre."','".$Apellido."','".$Email."','".$MenorEdad."','App',NOW())";
										$dbo->query($inserta_invitado);					 
										$id_invitado = $dbo->lastID();
									else:
										$actualiza_invitado = "Update Invitado set IDTipoDocumento = '".$IDTipoDocumento."', NumeroDocumento = '".$NumeroDocumento."', Nombre = '".$Nombre."', Apellido = '".$Apellido."', Email = '".$Email."', UsuarioTrEd = 'App', FechaTrEd = NOW() Where IDInvitado = '".$id_invitado."'";
										$dbo->query($actualiza_invitado);					 
									endif;
									
									//Si es cabeza de familia guardo el id del Socio
									if($CabezaInvitacion=="S"):
										$IDPadre = $id_invitado;
									endif;
									
									
									$sql_numero_invitacion = $dbo->query("Select * From SocioInvitadoEspecial Where IDInvitado = '".$id_invitado."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitaciones = $dbo->rows($sql_numero_invitacion);
									//Consulto cuantas personas ha invitado el socio en el mes			
									$sql_invitados_mes = $dbo->query("Select * From SocioInvitadoEspecial Where IDSocio = '".$IDSocio."' and YEAR(FechaInicio) = '".$year_invitacion."' and MONTH(FechaInicio) = '".$mes_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitados_mes = $dbo->rows($sql_invitados_mes);
									//Consulto cuantas personas ha invitado el socio en el dia		
									$sql_invitados_dia = $dbo->query("Select * From SocioInvitadoEspecial Where IDSocio = '".$IDSocio."' and YEAR(FechaInicio) = '".$year_invitacion."' and MONTH(FechaInicio) = '".$mes_invitacion."' and DAY(FechaInicio) = '".$dia_invitacion."' and IDClub = '".$IDClub."'");
									$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
									
									$numero_invitados_mes_permitido = 500;
									$numero_mismo_invitado_mes = "300";							
									$cumplimiento_obligatorio_limite = "S";
					
									// Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
									//$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );
									
									if ((int)$numero_invitados_dia<(int)$numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite=="N"){
											if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite=="N"){
												if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes || $cumplimiento_obligatorio_limite=="N"){
													//Verifico que el invitado no este invitado mas de una vez el mismo dia
													
													$sql_invitacion_dia = $dbo->query("Select * From SocioInvitadoEspecial Where IDInvitado = '".$id_invitado."' and FechaInicio = '".$FechaIngreso."'");
													$numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
													if((int)$numero_invitaciones_dia<=1){
														
														//verifico si el vehiculo ya esta creado
														if(!empty($Placa)):
															$id_vehiculo = $dbo->getFields( "Vehiculo" , "IDVehiculo" , "Placa = '".$Placa."'" );
															//Si el vehiculo no existe en la tabla maestra lo creo
															if(empty($id_vehiculo) || (int)$id_vehiculo==0 ):
																$inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa)
																					 Values('".$id_invitado."','".$Placa."')";
																$dbo->query($inserta_vehiculo);					 
																$id_vehiculo = $dbo->lastID();
															endif;
														endif;	
														
														//Actualizo invitacion																												
														//$sql_actualiza_inv = $dbo->query("Update SocioInvitadoEspecial Set IDClub = '".$IDClub."', IDSocio = '".$IDSocio."', IDInvitado = '".$id_invitado."', IDPadre = '".$IDPadre."', IDVehiculo = '".$id_vehiculo."', CabezaInvitacion = '".$CabezaInvitacion."', TipoInvitacion = '".$TipoInvitado."', FechaInicio = '".$FechaIngreso."', FechaFin = '".$FechaSalida."', UsuarioTrEd = 'WebService', FechaTrEd = NOW() Where SocioInvitadoEspecial = '".$IDInvitacion."'");														
														//Inserto invitacion
														$sql_inserta_inv = $dbo->query("Insert Into SocioInvitadoEspecial (IDClub, IDSocio, IDInvitado, IDPadre, IDVehiculo, CabezaInvitacion, TipoInvitacion, FechaInicio, FechaFin, UsuarioTrCr, FechaTrCr) 
																						Values ('".$IDClub."','".$IDSocio."', '".$id_invitado."', '".$IDPadre."', '".$id_vehiculo."', '".$CabezaInvitacion."', '".$TipoInvitado."', '".$FechaIngreso."', '".$FechaSalida."', 'WebService',NOW())");
													
														//Inserto el vehiculo de la invitacion
														if(!empty($Placa)):
															$inserta_vehiculo_inv = "Insert Into VehiculoInvitacion (IDSocioInvitadoEspecial, IDVehiculo, Placa)
																						 Values('".$id_invitado_inserta."','".$id_vehiculo."','".$Placa."')";
															$dbo->query($inserta_vehiculo_inv);									
														endif;
														
													}
													else{
														$array_errorres[] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";																	
													}
												}
												else{
													$array_errorres[] = "Lo sentimos supera el numero maximo de ".$numero_invitados_mes_permitido." invitaciones por mes";																	
												}
											}
											else{
												$array_errorres[] = "Lo sentimos, esta persona ha sido invitadas mas de ".$numero_mismo_invitado_mes." veces en este mes.";															
											}
									}
									else{
										$array_errorres[] = "Lo sentimos, supera el numero maximo de ".$numero_invitados_dia_permitido." invitaciones por dia";													
									}
								endforeach;
							endif;	
							
							if(count($array_errorres)>0):
								$otros_mensajes = implode(",",$array_errorres);
							endif;
							
							$respuesta["message"] = "guardado " . $otros_mensajes;
							$respuesta["success"] = true;
							$respuesta["response"] = NULL;	
							
					}
					else{
						$respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;			
					}
					
				}
				else{
					$respuesta["message"] = "Error el socio no existe o no pertenece al club";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
				}
				
		}
		else{
			$respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "Invitado: Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}		
	
	
	
	
	
	
	
function buscar_elemento_disponible($IDClub,$IDServicio,$Fecha,$Hora){
	$dbo =& SIMDB::get();	
	$IDElemento="";
	$elemento_encontrado = 0;
	// Verifico cuantos elementos tienen esta hora disponible
		$dia_fecha= date('w', strtotime($Fecha));	
		$sql_dispo_hora = "Select * From ServicioDisponibilidad Where IDDisponibilidad > 0 and IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and ('".$Hora."' >= HoraDesde and '".$Hora."'<=HoraHasta)  Order by HoraDesde";
		$qry_dispo_hora= $dbo->query( $sql_dispo_hora );
		while($row_dispo_hora= $dbo->fetchArray($qry_dispo_hora)):			
			if (!empty($row_dispo_hora["IDServicioElemento"])):
				$array_elementos_hora = explode("|",$row_dispo_hora["IDServicioElemento"]);
				foreach($array_elementos_hora as $id_elemento_hora):
					//verifo que el elemento pueda ser reservado automaticamente por otro servicio (por ejemplo cancha al tomar una clase)
					$permite_reserva_automatica = $dbo->getFields( "ServicioElemento" , "PermiteReservaAutomatica" , "IDServicioElemento = '".$id_elemento_hora."'");
					if (!empty($id_elemento_hora) && $permite_reserva_automatica!="N"):
						// verifico que no este reservado
						$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$id_elemento_hora."' and IDEstadoReserva in (1) and Fecha = '".$Fecha."' and Hora = '".$Hora."'" );		
						if(empty($id_reserva_disponible)):
							//verifico que no tenga cierre a esa hora
							$verifica_abierto_servicio_hora = SIMWebservice::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$id_elemento_hora,$Hora);
							if(empty($verifica_abierto_servicio_hora)):
								$elemento_encontrado =1;
								return $id_elemento_hora;
							endif;	
						endif;
					endif;
				endforeach;												
			endif;
		endwhile;
	return $IDElemento;
}	
	
function set_separa_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,$Tee,$Fecha,$Hora,$IDTipoReserva='',$NumeroTurnos="")
 {	
	 
	$dbo =& SIMDB::get();	
	$flag_reserva_cancha_clase = 0;
	
	if(empty($NumeroTurnos)){
		$NumeroTurnos = 1;
	}
	
	
	$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $IDServicio . "'" );
	// Si el servicio es una clase y necesita reservar cancha
	$id_servicio_cancha = $dbo->getFields( "ServicioMaestro" , "IDServicioMaestroReservar" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
	if($id_servicio_cancha>0):
				// Consulto el servicio del club asociado a este servicio maestro
				$IDServicioCanchaClub  = $dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '".$IDClub."'" );
			  // Valido si existe una cancha disponible en el horario de la clase
			  $IDElemento_cancha = SIMWebService::buscar_elemento_disponible($IDClub,$IDServicioCanchaClub,$Fecha,$Hora);
			  if(empty($IDElemento_cancha)):
					$respuesta["message"] = "Lo sentimos no existe una cancha disponible para tomar la clase en este horario";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
					return $respuesta;
			 else:		
				$flag_reserva_cancha_clase = 1;																
			 endif;
	endif;
	
	
		
		//Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
		if (!empty($IDTipoReserva)):				
				$datos_tipo_reserva = $dbo->fetchAll( "ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array" );
				$cantidad_turnos = $datos_tipo_reserva["NumeroTurnos"];		
		
			if( ( (int)$cantidad_turnos>1 )):						
				//$cantidad_turnos-=1; // Quito uno para que no cuente la reserva primera 			
				
				// Separo las reservas
				$array_hora_siguiente_turno_diponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos );			
				if(count($array_hora_siguiente_turno_diponible)!=(int)($cantidad_turnos-1) || !is_array($array_hora_siguiente_turno_diponible)):
					$respuesta["message"] = "Se necesitan ".$cantidad_turnos." turnos mas seguidos y el siguiente turno no esta disponible Por favor seleccione otra opcion";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;	
					return $respuesta;			
				else:
					$flag_separa_siguiente_turno=1;						
				endif;								
			endif;
		endif;	
		
		
	//Si turnos es mayor a 1  verifico que los siguientes turnos esten disponibles y los separo
	if((int)$NumeroTurnos>1):		
		if($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30): //Golf															
			$array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, $Tee );	
		else:
			$array_disponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos );	
		endif;
	
		
		if(count($array_disponible)!=$NumeroTurnos):
			$respuesta["message"] = "Se necesitan ".$NumeroTurnos." turnos mas seguidos y el siguiente turno no esta disponible, por favor seleccione otra opcion";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;	
			return $respuesta;					
		else:
			// separo todos los turnos necesarios
			foreach($array_disponible as $key_hora => $dato_hora):					
					$sql_inserta_reserva = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, Fecha, Hora, Tee, UsuarioTrCr, FechaTrCr) 
														Values ('".$IDClub."','".$IDSocio."', '".$IDServicio."','".$IDElemento."', '3','".$Fecha."', '".$dato_hora."','".$Tee."', 'WebService',NOW())");
					$id_reserva_general = $dbo->lastID();					
			
			endforeach;		
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = $id_reserva_general;						
			return $respuesta;
		endif;		
	endif;
	
		
	
	// Si el servicio esta definido con servicio inicial = 5 que es get_reserva_aleatoria busco el primer elemento disponible	
	if (empty($IDElemento)):		
		$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $IDServicio . "'" );
		$id_servicio_inicial = $dbo->getFields( "ServicioMaestro" , "IDServicioInicial" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
		if($id_servicio_inicial==5): // 5 = get_reserva_aleatoria
			$IDElemento = SIMWebService::buscar_elemento_disponible($IDClub,$IDServicio,$Fecha,$Hora);			
		endif;
	endif;
	
	$Hora = SIMWebService::validar_formato_hora($Hora);
	
	if( !empty( $IDClub ) && !empty( $IDSocio )  && !empty( $IDElemento ) && !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			// verifico que todavia este disponible la reserva
			if(!empty($Tee)):
				$condicion_tee = " and Tee = '".$Tee."'";
			endif;
			
			$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1,3) and Fecha = '".$Fecha."' and Hora = '".$Hora."' $condicion_tee" );
			
			
			if( ($id_servicio_maestro == "15" || $id_servicio_maestro=="27" || $id_servicio_maestro=="28" ) && empty($Tee) && !empty($id_reserva_disponible)):
				$Tee = "Tee10";	
				$condicion_tee = " and Tee = '".$Tee."'";
				$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1,3) and Fecha = '".$Fecha."' and Hora = '".$Hora."' $condicion_tee" );
				if (!empty($id_disponible_tee)):
					$Tee = "Tee1";	
					$condicion_tee = " and Tee = '".$Tee."'";
					$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1,3) and Fecha = '".$Fecha."' and Hora = '".$Hora."' $condicion_tee" );					
				endif;
			endif;
			
			
			// Obtener la disponibilidad utilizada al consultar la reserva
			$id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio,$Fecha,$IDElemento,$Hora);
			$datos_disponibilidad = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $id_disponibilidad . "' ", "array" );
			// Verifico si el servicio en esta disponiblidad permite a varios socios tomar el mismo turno, por ejemplo clase de gimnasia												
			$cupo_total="S";// ya no hay cupos
			$cupos_disponibilidad = $dbo->getFields( "Disponibilidad" , "Cupos" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
			if((int)$cupos_disponibilidad>1):													
				//Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
				$cupos_reservados = self::valida_cupos_disponibles($IDClub, $IDServicio, $IDElemento, $Fecha, $Hora);
				//Valido si todavia existe cupo en esta hora
				if($cupos_reservados <= $datos_disponibilidad["Cupos"]):
					$cupo_total="N"; // aun hay cupos disponibles
				endif;
			endif;	

			
			
			if (empty($id_reserva_disponible) || $cupo_total=="N"):
				//Guardo la reserva
				$sql_inserta_reserva = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, Fecha, Hora, Tee, UsuarioTrCr, FechaTrCr) 
													Values ('".$IDClub."','".$IDSocio."', '".$IDServicio."','".$IDElemento."', '3','".$Fecha."', '".$Hora."','".$Tee."', 'WebService',NOW())");
				
				$id_reserva_general = $dbo->lastID();
				
				
				
				// SI el servicio es una clase y se solicta reservar una cancha realizo la reserva temporal
				if($flag_reserva_cancha_clase==1):
					// Obtener la disponibilidad utilizada al consultar la reserva
					$id_disponibilidad_cancha = SIMWebService::obtener_disponibilidad_utilizada($IDServicioCanchaClub,$Fecha,$IDElemento_cancha);
					$sql_inserta_reserva_cancha = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, Tipo, UsuarioTrCr, FechaTrCr) 
					Values ('".$IDClub."','".$IDSocio."', '".$IDServicioCanchaClub."','".$IDElemento_cancha."', '3','".$id_disponibilidad."','".$Fecha."', '".$Hora."','".$Observaciones."','".$Tee."','".$IDAuxiliar."','".$IDTipoModalidadEsqui."','Automatica','WebService',NOW())");											
					$id_reserva_cancha = $dbo->lastID();												
					//Agrego la relacion de servicio padre (Clase)y servicios hijos (canchas) reservados
					$sql_reserva_automatica = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva ) 
														   Values ('".$id_reserva_general."','".$id_reserva_cancha."','".$IDClub.".','".$IDSocio."','".$IDElemento_cancha."','".$Fecha."','".$Hora."','3')");
				endif;
				
				// Si se va a reservas mas turnos seguidos y la validacion fue exitosa borro las separacion hechas
				if ($flag_separa_siguiente_turno==1 && count($array_hora_siguiente_turno_diponible)>0):												
						foreach($array_hora_siguiente_turno_diponible as $Hora_siguiente):							
							// Borro la reserva separada																					
							$sql_inserta_reserva = $dbo->query("Select * From ReservaGeneral Where IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicioElemento	 = '".$IDElemento."' and Fecha = '".$Fecha."' and Hora = '".$Hora_siguiente."' and IDEstadoReserva  = 3");
							while($row_turno_siguiente = $dbo->fetchArray($sql_inserta_reserva)):								
								$sql_reserva_automatica = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva) 
																	  Values ('".$id_reserva_general."','".$row_turno_siguiente["IDReservaGeneral"]."','".$IDClub."','".$IDSocio."','".$row_turno_siguiente["IDServicioElemento"]."','".$Fecha."','".$Hora_siguiente."','3')");
							endwhile;
						endforeach;																								
				endif;
				
				
		
				$respuesta["message"] = "guardado";
				$respuesta["success"] = true;
				$respuesta["response"] = $id_reserva_general;		
	
			else:
				$respuesta["message"] = "Lo sentimos la reserva ya fue tomada".$Tee." " . $cupos_reservados;
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;		
			
			endif;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "18. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	

function set_libera_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,$Tee,$Fecha,$Hora)
 {
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio )  && !empty( $IDElemento ) && !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			// Consulto la reserva
			$sql_reserva_general = $dbo->query("Select * From ReservaGeneral Where  IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva = 3 and Fecha = '".$Fecha."' and Hora = '".$Hora."'");
			$row_reserva_general = $dbo->fetchArray($sql_reserva_general);
			//Elimino la reserva
			//$sql_libera_reserva = $dbo->query("DELETE FROM ReservaGeneral WHERE IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva = 3 and Fecha = '".$Fecha."' and Hora = '".$Hora."'");
			$sql_libera_reserva = $dbo->query("DELETE FROM ReservaGeneral WHERE IDReservaGeneral = '".$row_reserva_general["IDReservaGeneral"]."' ");
			//Elimino las relacionadas
			$sql_reserva_auto = $dbo->query("Select * From ReservaGeneralAutomatica Where  IDReservaGeneral = '".$row_reserva_general["IDReservaGeneral"]."'");
			while($row_reserva_auto = $dbo->fetchArray($sql_reserva_auto)):
				$sql_libera_reserva = $dbo->query("DELETE FROM ReservaGeneralAutomatica WHERE IDReservaGeneralAutomatica = '".$row_reserva_auto["IDReservaGeneralAutomatica"]."'");
				$sql_libera_reserva = $dbo->query("DELETE FROM ReservaGeneral WHERE IDReservaGeneral = '".$row_reserva_auto["IDReservaGeneralAsociada"]."'");
			endwhile;	
			
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = "reserva eliminada";	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "19. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	


function validar_turnos_seguidos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario = "", $TipoBeneficiario = "", $PermiteReservaSeguidaNucleo ){
			$dbo =& SIMDB::get();	
			$flag_turno_seguido = 0;
			$array_confirmado = array();
			// Consulto los turnos reservados y confirmados del socio para no tomar los separados
			if(!empty($IDBeneficiario)):
				$condicion_beneficiario = " and  (IDSocioBeneficiario = '".$IDBeneficiario."' or IDInvitadoBeneficiario = '".$IDBeneficiario."')";
			else:		
				$condicion_beneficiario = " and  IDSocioBeneficiario = '0' and IDInvitadoBeneficiario = '0'";
			endif;
			
			
			// Valido tambien para que los de la misma acción no puedan tomar turnos seguidos
			//Si en la configuracion esta marcada como "No" de lo contrario se permite turnos seguios asi sean de la misma accion
			if($PermiteReservaSeguidaNucleo=="N"):
				$accion_padre = $dbo->getFields( "Socio" , "AccionPadre" , "IDSocio = '".$IDSocio."'" );
				$accion_socio = $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$IDSocio."'" );
				if(empty($accion_padre)): // Es titular
					$array_socio[] = $IDSocio;
					$sql_nucleo="Select IDSocio From Socio Where AccionPadre = '".$accion_socio."' and IDClub = '".$IDClub."' ";
					$result_nucleo = $dbo->query($sql_nucleo);
					while($row_nucleo = $dbo->fetchArray($result_nucleo)):
						$array_socio[] = $row_nucleo["IDSocio"];
					endwhile;
				else:
					$sql_nucleo="Select IDSocio From Socio Where AccionPadre = '".$accion_padre."' or Accion = '".$accion_padre."' and IDClub = '".$IDClub."' ";
					$result_nucleo = $dbo->query($sql_nucleo);
					while($row_nucleo = $dbo->fetchArray($result_nucleo)):
						$array_socio[] = $row_nucleo["IDSocio"];
					endwhile;				
				endif;
				if(count($array_socio)>0):
					$id_socio_nucleo = implode(",",$array_socio);
				endif;
			else:
				$id_socio_nucleo = $IDSocio;
			endif;	
			
			
			//$sql_confirmado="Select * From  ReservaGeneral Where IDSocio = '".$IDSocio."' and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva	= 1 " . $condicion_beneficiario;
			$sql_confirmado="Select * From  ReservaGeneral Where IDSocio in (".$id_socio_nucleo.")  and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva	= 1 " . $condicion_beneficiario;
			$qry_confirmado = $dbo->query($sql_confirmado);
			while($r_confirmado = $dbo->fetchArray($qry_confirmado)):
				$array_confirmado [] = $r_confirmado["Hora"];
			endwhile;
			
			$array_horarios = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,"","");
			foreach ($array_horarios["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario ):				
				if(in_array($IDSocio,$array_socio) && in_array($datos_horario["Hora"],$array_confirmado)):					
					$id_socio_turno = $IDSocio;
				elseif(empty($array_turnos_dia[$datos_horario["Hora"]])):
					$id_socio_turno = "";	
				endif;
				if(empty($array_turnos_dia[$datos_horario["Hora"]])):				
					$array_turnos_dia[$datos_horario["Hora"]] = $id_socio_turno;	
				endif;	
			endforeach;	
			
			
			for($i=1;$i<=count($array_turnos_dia);$i++):
				current($array_turnos_dia);
				//Primer Posicion
				if($i==1 && key($array_turnos_dia)==$Hora && current($array_turnos_dia)==$IDSocio): // Es el primer horario y lo valido
					$flag_turno_seguido = 1;	
				endif;
				if(key($array_turnos_dia)==$Hora):			
					// me devuelvo al turno anterior
					prev($array_turnos_dia);
					if (current($array_turnos_dia)==$IDSocio):
						$flag_turno_seguido = 2;	
					endif;
					//Adelanto dos turnos, si es el final solo uno
					next($array_turnos_dia);
					if (current($array_turnos_dia)==$IDSocio):
						$flag_turno_seguido = 3;	
					endif;
					if ($i!=count($array_turnos_dia)):				
						next($array_turnos_dia);
					endif;
					if (current($array_turnos_dia)==$IDSocio):
						$flag_turno_seguido = 4;	
					endif;
				endif;
				next($array_turnos_dia);
			endfor;
			
		return $flag_turno_seguido;	
			
	}
	
	
	function validar_regla_turnos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDTipoReserva ){
			$dbo =& SIMDB::get();	
			$regla_no_cumplida = 0;			
			$turno_automatico = 0;
			$turno_tomado = 0;
			$datos_tipo_reserva = $dbo->fetchAll( "ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array" );
			
			
			// Valido tambien para que los de la misma acción no puedan tomar turnos seguidos			
			$accion_padre = $dbo->getFields( "Socio" , "AccionPadre" , "IDSocio = '".$IDSocio."'" );
			$accion_socio = $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$IDSocio."'" );
			if(empty($accion_padre)): // Es titular
				$array_socio[] = $IDSocio;
				$sql_nucleo="Select IDSocio From Socio Where AccionPadre = '".$accion_socio."' and IDClub = '".$IDClub."' ";
				$result_nucleo = $dbo->query($sql_nucleo);
				while($row_nucleo = $dbo->fetchArray($result_nucleo)):
					$array_socio[] = $row_nucleo["IDSocio"];
				endwhile;
			else:
				$sql_nucleo="Select IDSocio From Socio Where AccionPadre = '".$accion_padre."' or Accion = '".$accion_padre."' and IDClub = '".$IDClub."' ";
				$result_nucleo = $dbo->query($sql_nucleo);
				while($row_nucleo = $dbo->fetchArray($result_nucleo)):
					$array_socio[] = $row_nucleo["IDSocio"];
				endwhile;				
			endif;
			if(count($array_socio)>0):
				$id_socio_nucleo = implode(",",$array_socio);
			endif;
			
			// Consulto los turnos reservados automaticos y confirmados del socio para no tomar los separados
			//$sql_confirmado="Select * From  ReservaGeneral Where IDSocio = '".$IDSocio."' and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva	= 1";
			$sql_confirmado="Select * From  ReservaGeneral Where IDSocio in (".$id_socio_nucleo.") and IDServicio  = '".$IDServicio."' and Fecha = '".$Fecha."' and IDEstadoReserva	= 1";
			$qry_confirmado = $dbo->query($sql_confirmado);
			$turno_tomado = $dbo->rows($qry_confirmado); 
			while($row_confirmado = $dbo->fetchArray($qry_confirmado)):
				if ($row_confirmado["Tipo"] == "Automatica"):					
					$turno_automatico++;
				endif;				
			endwhile;
			
			if($turno_automatico>0):
				$regla_no_cumplida=1; // 1 = Ya tomo un turno con la opcion de 2, 3, etc  tuernos seguidos
			endif;
			
			//Valido si ya tiene una reserva en el dia no pueda reservar ninguna de turnos seguidos
			if((int)$turno_tomado>0 && (int)$datos_tipo_reserva["NumeroTurnos"]>1 && $regla_no_cumplida == 0):
				$regla_no_cumplida=2; // 2 = Ya tomo un turno ya no puede tomar uno en bloque
			endif;
			
			return $regla_no_cumplida;	
	}
	
	
	
function valida_siguiente_turno_disponible($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos ){
			$dbo =& SIMDB::get();	
			$hora_turno_siguiente = "";
			$flag_turno_disponible = 0;
			$contador_turnos=1;			
			$array_horarios = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"");
			
			foreach ($array_horarios["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario ):				
				if($flag_turno_siguiente==1):					
					$respuesta = SIMWebService::set_separa_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,"",$Fecha,$datos_horario["Hora"],"",$cantidad_turnos);	
					if ($respuesta==true):												
						$hora_turno_siguiente[] = $datos_horario["Hora"]; // Si se pudo separar
						$contador_turnos++;
						if($contador_turnos <= $cantidad_turnos):
							$Hora = $datos_horario["Hora"];
						endif;
					else:
						unset($hora_turno_siguiente); // No se pudo separar
					endif;					
				endif;
			
				if ($datos_horario["Hora"]==$Hora):
					$flag_turno_siguiente = 1;
				else:
					$flag_turno_siguiente = 0;				
				endif;
			endforeach;	
			
			//Valido que se hayan podido separado los mismos turnos que se solicitaron
			if (count($hora_turno_siguiente)!=$cantidad_turnos):
				unset($hora_turno_siguiente);
				//echo "no se pudieron tomar todos";
			endif;
			
			
		return $hora_turno_siguiente;
			
	}
	
	function valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos ){
			global $array_horarios;
			$dbo =& SIMDB::get();	
			$hora_turno_siguiente = "";
			$flag_turno_disponible = 0;
			$contador_turnos=1;	
			// Quito 1 turno por que necesito validar los siguientes
			$cantidad_turnos--;		
			if(count($array_horarios[$IDElemento])<=0):
				$array_horarios[$IDElemento] = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"");
			endif;	
			
			
			//$array_horarios = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"");
			
			foreach ($array_horarios[$IDElemento]["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario ):				
				if($flag_turno_siguiente==1):														
						//$respuesta = SIMWebService::set_separa_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,"",$Fecha,$datos_horario["Hora"],"",$cantidad_turnos);			
						// verifico si esta disponible la reserva
						$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1) and Fecha = '".$Fecha."' and Hora = '".$datos_horario["Hora"]."'" );						
						//Verifico que no haya fecha/hora de cierre en el turno siguiente
						$hora_cerrada = self::verifica_club_servicio_abierto($IDClub,$Fecha,$IDServicio,$IDElemento,$datos_horario["Hora"]);
						
						if (empty($id_reserva_disponible) && $hora_cerrada==""):
							$respuesta = SIMWebService::set_separa_reserva($IDClub,$IDSocio,$IDElemento,$IDServicio,"",$Fecha,$datos_horario["Hora"],"","");									
						else:							
							$respuesta = false;	
						endif;
						
					if ($respuesta==true):												
						$hora_turno_siguiente[] = $datos_horario["Hora"]; // Si se pudo separar
						$contador_turnos++;
						if($contador_turnos <= $cantidad_turnos):
							$Hora = $datos_horario["Hora"];
						endif;
					else:
						unset($hora_turno_siguiente); // No se pudo separar
					endif;					
				endif;
				
				if ($datos_horario["Hora"]==$Hora):
					$flag_turno_siguiente = 1;
				else:
					$flag_turno_siguiente = 0;				
				endif;
			endforeach;	
			
			//Valido que se hayan podido separado los mismos turnos que se solicitaron
			if (count($hora_turno_siguiente)!=$cantidad_turnos):
				unset($hora_turno_siguiente);
				//echo "no se pudieron tomar todos";
			endif;
			
			
		return $hora_turno_siguiente;
			
	}	
	
	function valida_siguiente_turno_disponible_golf($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos, $Tee, $TipoReserva="" ){
			global $array_horarios_servicio;
			$dbo =& SIMDB::get();	
			$hora_verify= $Hora;
			$hora_turno_siguiente = "";
			$flag_turno_disponible = 0;
			$contador_turnos=1;
			if(count($array_horarios_servicio)<=0):
				$array_horarios_servicio = SIMWebService::get_disponiblidad_elemento_servicio($IDClub,$IDServicio,$Fecha,$IDElemento,"");				
			endif;	
			//print_r($array_horarios);
			
			foreach ($array_horarios_servicio["response"][0]["Disponibilidad"][0] as $id_horario => $datos_horario ):
				// valido solo las horas mayores a la que solicita para mejorar rendimiento
				$hora_disponible = strtotime($datos_horario["Hora"]);
				$hora_consultada = strtotime($Hora);
				
				if( $datos_horario["Tee"] == $Tee && $hora_disponible>=$hora_consultada): // Solo verifico el tee que recibe						
					if($contador_turnos <= $cantidad_turnos):							
						
						//Si el tipo de reserva viene vacio es que se está separando o verificando, si es reserva no tengo en cuenta las separadas
						if($TipoReserva=="Reserva"):
							$id_tipo_reserva = "1";
						else:
							$id_tipo_reserva = "1,3";
						endif;
						// verifico si esta disponible la reserva
						$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (".$id_tipo_reserva.") and Fecha = '".$Fecha."' and Hora = '".$datos_horario["Hora"]."' and Tee = '".$Tee."'" );
						if (empty($id_reserva_disponible)):						
								$hora_turno_siguiente[] = $datos_horario["Hora"]; // Si se pudo separar						
								$Hora = $datos_horario["Hora"];
						else:
							unset($hora_turno_siguiente); // No se pudo separar		
						endif;
							$contador_turnos++;
					endif;																				
			    endif;	
			endforeach;	
		
			//Valido que se hayan podido separado los mismos turnos que se solicitaron
			if (count($hora_turno_siguiente)!=$cantidad_turnos):
				unset($hora_turno_siguiente);
				//echo "no se pudieron tomar todos";
			endif;
			
		return $hora_turno_siguiente;
			
	}	
	
	
 function set_reserva_general($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,$Observaciones="",$Admin = "", $Tee="",$IDDisponibilidad="", $PermiteRepeticion="")
 {
	$dbo =& SIMDB::get();	
	
	// Si el servicio esta definido con servicio inicial = 5 que es get_reserva_aleatoria busco el primer elemento disponible	
	if (empty($IDElemento)):		
		$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $IDServicio . "'" );
		$id_servicio_inicial = $dbo->getFields( "ServicioMaestro" , "IDServicioInicial" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
		if($id_servicio_inicial==5): // 5 = get_reserva_aleatoria
			$IDElemento = SIMWebService::buscar_elemento_disponible($IDClub,$IDServicio,$Fecha,$Hora);			
		endif;
	endif;
	
	
	
	
	if( !empty( $IDClub ) && !empty( $IDSocio )  && !empty( $IDElemento ) && !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			
			//verifico la disponibilidad que se utilizo
			$dia_fecha= date('w', strtotime($Fecha));
			$sql_disponibilidad = $dbo->query("Select IDDisponibilidad From ServicioDisponibilidad Where IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%' Limit 1");
			$row_disponibilidad = $dbo->fetchArray($sql_disponibilidad);
			$id_disponibilidad = $row_disponibilidad["IDDisponibilidad"];
			
			//Valido que no se pueda tomar varios turnos seguidos
			$flag_turno_seguido = SIMWebService::validar_turnos_seguidos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub );
			
			
			// Si es Admin si puede reservas turnos seguidos
			if(!empty($Admin)):
				$flag_turno_seguido=0;	
			endif;	
			
			if ($flag_turno_seguido==0):		
			
							$fecha_disponible = 0;
							
							//Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
							$array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub,$IDServicio); 
							foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha):
								if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"]=="S"):
									$fecha_disponible = 1;			
								endif;
							endforeach;
							
							if ($fecha_disponible==1):
							
							//Verifico que el socio no tenga mas de tres reservas en el mismo dia
							$sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where IDSocio = '".$IDSocio."' and Fecha = '".$Fecha."' and IDServicio = '".$IDServicio."'");
							$total_reserva_socio = (int)$dbo->rows($sql_reservas_dia);
							
							// Si es Admin si puede reservar mas de un turno por dia
							if(!empty($Admin)):
								$total_reserva_socio = 1;	
								$UsuarioCreacion = "Starter";
							else:
								$UsuarioCreacion = "SocioV2";
							endif;	
											
							
							if($total_reserva_socio<=1):
							
							
							
							
									// verifico que todavia este disponible la reserva
									$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1) and Fecha = '".$Fecha."' and Hora = '".$Hora."'" );		
									if (empty($id_reserva_disponible)):
									
										// Borro la reserva separada
										$sql_inserta_reserva = $dbo->query("Delete From ReservaGeneral Where IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicioElemento	 = '".$IDElemento."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDEstadoReserva  = 3");
									
										//Guardo la reserva
										$sql_inserta_reserva = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, Fecha, Hora, Observaciones, Tee, UsuarioTrCr, FechaTrCr) 
																			Values ('".$IDClub."','".$IDSocio."', '".$IDServicio."','".$IDElemento."', '1','".$id_disponibilidad."','".$Fecha."', '".$Hora."','".$Observaciones."','".$Tee."','".$UsuarioCreacion."',NOW())");
										
										$id_reserva_general = $dbo->lastID();
									
										$array_Invitados = $Invitados;
										if (count($array_Invitados)>0):
											foreach($array_Invitados as $id_valor => $valor):	
												// Guardo los invitados socios o externos									
												$sql_inserta_invitado = $dbo->query("Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre) 
																					Values ('".$id_reserva_general."','".$valor["IDSocioInvitado"]."', '".$valor["NombreInvitado"]."')");
											endforeach;									
										endif;										
								
										$array_Campos = $Campos;
										if (count($array_Campos)>0):
											foreach($array_Campos as $id_valor_campo => $valor_campo):	
												// Guardo los campos personalizados									
												$sql_inserta_campo = $dbo->query("Insert Into ReservaGeneralCampo (IDReservaGeneral, IDServicioCampo, Valor) 
																					Values ('".$id_reserva_general."','".$valor_campo["IDServicioCampo"]."', '".$valor_campo["Valor"]."')");
										endforeach;									
										endif;	
										
										
										
										
																			
											
							
										$respuesta["message"] = "guardado";
										$respuesta["success"] = true;
										$respuesta["response"] = "guardado";	
									else:
										$respuesta["message"] = "Lo sentimos la reserva ya fue tomada";
										$respuesta["success"] = false;
										$respuesta["response"] = NULL;		
									
									endif;
									
									
							else:
								$respuesta["message"] = "Lo sentimos solo se permiten 1 reserva por dia";
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;	
							endif;	
							
						else:
								$respuesta["message"] = "Lo sentimos fecha no disponible";
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;	
						endif;
			else:
				$respuesta["message"] = "Lo sentimos no se puede reservar turnos seguidos ";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
			
			
			endif;			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "20. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}
	
function validar_formato_hora($Hora){
	$hora_militar ="";	
	if (strlen($Hora)>8):
		$cadena = strtotime($Hora);
		$cadena = date("H:i:s", $cadena);
		$hora_militar = $cadena;	
	else:
		$hora_militar =$Hora;	
	endif;
	
	return $hora_militar;
}	

function obtener_disponibilidad_utilizada($IDServicio,$Fecha,$IDElemento,$Hora=""){
		$dbo =& SIMDB::get();	
		//verifico la disponibilidad que se utilizo
		if(!empty($Hora)):
			$condicion_hora = " and ('".$Hora."' >= HoraDesde and '".$Hora."'<=HoraHasta) ";
		endif;		
		$dia_fecha= date('w', strtotime($Fecha));
		$sql_disponibilidad = $dbo->query("Select IDDisponibilidad From ServicioDisponibilidad Where IDServicio = '".$IDServicio."' and   IDDia like '%".$dia_fecha."|%' and IDServicioElemento like '%".$IDElemento."|%' ".$condicion_hora." Limit 1");
		$row_disponibilidad = $dbo->fetchArray($sql_disponibilidad);	
		$id_disponibilidad = $row_disponibilidad["IDDisponibilidad"];
		return $id_disponibilidad;
}

	
function busca_cabeza_grupo($Invitados, $NumeroTurnos, $IDSocio){
		//Resto un turno ya que el primero debe ser el socio que realiza la reserva
		$NumeroTurnos -= 1;
		$array_cabeza_grupo = array();
		$total_cabeza = 1;
		$datos_invitado_turno= json_decode($Invitados, true);
		foreach($datos_invitado_turno as $detalle_datos_turno):																			
			$IDSocioInvitadoTurno = $detalle_datos_turno["IDSocio"];			 
			$NombreSocioInvitadoTurno = $detalle_datos_turno["Nombre"];
			if(!empty($IDSocioInvitadoTurno) && $total_cabeza<=$NumeroTurnos):
				$array_cabeza_grupo[]=$IDSocioInvitadoTurno."-".$NombreSocioInvitadoTurno;
				$total_cabeza++;
			endif;
		endforeach;
		
		//Verifico que los invitados sean socio para ponerlos como cabeza ya que si son externos el cabeza debe ser el socio que realiza la reserva
		if(count($array_cabeza_grupo)<$NumeroTurnos):
			for($i_cabeza=0; $i_cabeza<=$NumeroTurnos;$i_cabeza++):
				if(empty($array_cabeza_grupo[$i_cabeza])):
					$array_cabeza_grupo[$i_cabeza] = $IDSocio;
				endif;
			endfor;
		endif;
	
	return $array_cabeza_grupo;
}	
	
	
function set_reserva_generalV2($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,$Observaciones="",$Admin = "", $Tee="",$IDDisponibilidad="", $Repetir="",$Periodo="",$RepetirFechaFinal="",$IDTipoModalidadEsqui="",$IDAuxiliar="",$IDTipoReserva="",$NumeroTurnos="",$IDReservaGrupos,$IDBeneficiario="",$TipoBeneficiario="",$IDUsuarioReserva="")
 {	 
 
	$dbo =& SIMDB::get();	
	
	
	$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $IDServicio . "'" );
	// Si el servicio esta definido con servicio inicial = 5 que es get_reserva_aleatoria busco el primer elemento disponible		
	if (empty($IDElemento)):				
		$id_servicio_inicial = $dbo->getFields( "ServicioMaestro" , "IDServicioInicial" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
		if($id_servicio_inicial==5): // 5 = get_reserva_aleatoria
			$IDElemento = SIMWebService::buscar_elemento_disponible($IDClub,$IDServicio,$Fecha,$Hora);			
		endif;
	endif;
	
	
	if( ($id_servicio_maestro == "15" || $id_servicio_maestro=="27" || $id_servicio_maestro=="28") && empty($Tee)):
		$respuesta["message"] = "Para poder reservar debe actualizar la app a la ultima version";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
		return $respuesta;
	endif;
	
	
	//Valido si el socio puede reservar	
	$permiso_reserva = SIMWebService::validar_permiso_reserva($IDSocio);
	if($permiso_reserva=="N"):
		$respuesta["message"] = "Lo sentimos no tiene permiso para realizar reservas";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
		return $respuesta;
	endif;
	
	
	// Verifico si tiene sanciones
	$sancion=SIMWebServiceApp::verifica_sancion_socio($IDClub,$IDSocio, $IDServicio);
	if($sancion && $IDClub == "8"):
		$respuesta["message"] = "Lo sentimos  tiene una sancion vigente.";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
		return $respuesta;
	endif;
	
	// Si tiene invitados verifico que los invitados no tengan sanciones
	$datos_invitado= json_decode($Invitados, true);
	if (count($datos_invitado)>0):
		foreach($datos_invitado as $detalle_datos):
			$IDSocioInvitado = $detalle_datos["IDSocio"];
			if(!empty($IDSocioInvitado)):
				$sancion=SIMWebServiceApp::verifica_sancion_socio($IDClub,$IDSocioInvitado, $IDServicio);
				if($sancion && $IDClub == "8"):
					$nombre_socio_sancion=utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $IDSocioInvitado . "' and IDClub = '".$IDClub."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $IDSocioInvitado . "' and IDClub = '".$IDClub."'" ));
					$respuesta["message"] = "Lo sentimos  el invitado ".$nombre_socio_sancion." tiene una sancion vigente, la reserva no puede ser tomada";
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
					return $respuesta;
				endif;

			endif;	
		endforeach;
	endif;	
	
	
	
	//Validacion del formato de hora, el app puede enviar con a.m o p.m
	$Hora = SIMWebService::validar_formato_hora($Hora);
	
	if( !empty( $IDClub ) && !empty( $IDSocio )  && !empty( $IDElemento ) && !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora ) &&  $Hora != "00:00:00"  ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			
			// Obtener la disponibilidad utilizada al consultar la reserva
			$id_disponibilidad = SIMWebService::obtener_disponibilidad_utilizada($IDServicio,$Fecha,$IDElemento,$Hora);
			
			//Valido que no se pueda tomar varios turnos seguidos
			$PermiteReservaSeguida = $dbo->getFields( "Disponibilidad" , "PermiteReservaSeguida" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
			$PermiteReservaSeguidaNucleo = $dbo->getFields( "Disponibilidad" , "PermiteReservaSeguidaNucleo" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
			if($PermiteReservaSeguida!="S")
				$flag_turno_seguido = SIMWebService::validar_turnos_seguidos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDBeneficiario, $TipoBeneficiario, $PermiteReservaSeguidaNucleo );
			else
				$flag_turno_seguido	= 0;
			
			
			if(($IDClub=="9" || $IDClub=="8") && empty($Admin) && $IDServicio == "89"):
				//Valido regla especial en Esqui si tiene dos turnos seguidos no permite reservar mas si no solo deja las configuradas (Caso especial Mesa de Yeguas)
				$regla_no_cumplida = SIMWebService::validar_regla_turnos($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDTipoReserva );
				if($regla_no_cumplida>0):
					switch($regla_no_cumplida):
						case "1":
							$mensaje_regla_no_cumplida = "Lo sentimos, ya tomo dos turnos seguidos, no puede reservas mas turnos en este dia ";
						break;
						case "2":
							$mensaje_regla_no_cumplida = "Lo sentimos, ya reservo un turno, no puede tomar turnos seguidos en este dia";
						break;
					endswitch;
					$respuesta["message"] = $mensaje_regla_no_cumplida;
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
					return $respuesta;
				endif;
			endif;	
			
			
			//Valido que se tengan los invitados minimos y maximos para reservar
			// elimino la pabra optional segun bug detectado en una actualizacion en ios
			$nuevacadena = str_replace('Optional("',"",$Invitados);
			$nuevacadena = str_replace('")',"",$nuevacadena);
			$Invitados = $nuevacadena;
			$datos_invitado= json_decode($Invitados, true);
			
			// Si el numero de turnos es mayor a 1 multiplico el minimo de la disponibilidad * el numero de turnos para saber el minimo y validar
			if((int)$NumeroTurnos>1):
				$MinimoInvitadosDisponibilidad = $dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
				$MinimoInvitados = (int)($MinimoInvitadosDisponibilidad * $NumeroTurnos)-1; // Le resto 1 para que cuente al socio que hace la reserva
			else:
				$MinimoInvitadosDisponibilidad = $dbo->getFields( "Disponibilidad" , "MinimoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
				$MinimoInvitados = (int)$MinimoInvitadosDisponibilidad-1; 
			endif;
			
			if((int)$NumeroTurnos>1):
				$MaximoInvitadosDisponibilidad = $dbo->getFields( "Disponibilidad" , "MaximoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
				$MaximoInvitados = $MaximoInvitadosDisponibilidad * $NumeroTurnos;
			else:
				$MaximoInvitados = $dbo->getFields( "Disponibilidad" , "MaximoInvitados" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
			endif;
			
			
			$cantidad_invitado= json_decode($Invitados, true);
			
			// Si agrega un aux por ejemplo boleador lo cuento como invitado
			if(!empty($IDAuxiliar)):
				$cantidad_auxiliar=1;
				//Verifico si el auxiliar esta disponible en esta fecha hora
				$id_reserva_aux = "";
				if(($IDClub=="8" || $IDClub=="10") && ($IDServicio=="316" || $IDServicio=="317")):				
					$id_reserva_aux = SIMWebServiceApp::validar_disponibilidad_auxiliar($IDAuxiliar,$Fecha, $Hora, $IDSocio, $IDServicio, $IDClub );
					$mensaje_auxiliar_no_dispo = "La masajista seleccionada no esta disponible en esta fecha/hora, por favor seleccione otra";
				else:
					$id_reserva_aux = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDAuxiliar = '" . $IDAuxiliar . "' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and (IDEstadoReserva  = 1 or IDEstadoReserva  = 3)" );					
					$mensaje_auxiliar_no_dispo ="Lo sentimos el auxiliar/boleador/ seleccionado no esta disponible en esta fecha/hora";
				endif;
				if(!empty($id_reserva_aux)):
					$respuesta["message"] = $mensaje_auxiliar_no_dispo;
					$respuesta["success"] = false;
					$respuesta["response"] = NULL;
					return $respuesta;
				endif;
			else:
				$cantidad_auxiliar=0;
			endif;
			
			
			
			if ( (count($datos_invitado) + $cantidad_auxiliar) >=(int)$MinimoInvitados):
			 if( (count($datos_invitado) + $cantidad_auxiliar) <=(int)$MaximoInvitados):
			//if (1==1):
			
			
			
			
					// Si es Admin si puede reservas turnos seguidos
					if(!empty($Admin)):
						$flag_turno_seguido=0;	
					endif;	
					
					
					if ($flag_turno_seguido==0):		
									$fecha_disponible = 0;
									//Verifico que la fecha seleccionada verdaderamente este disponible, ésto por que se puede cambiar la fecha del cel y lo deja pasar
									$array_disponibilidad = SIMWebService::get_fecha_disponibilidad_servicio($IDClub,$IDServicio); 
									foreach ($array_disponibilidad["response"][0]["Fechas"] as $id_fecha => $datos_fecha):
										if ($datos_fecha["Fecha"] == $Fecha && $datos_fecha["Activo"]=="S"):
											$fecha_disponible = 1;			
										endif;
									endforeach;
									
									// Si es Admin si puede reservas cualquier fecha
									//Ene 27 El rancho solicitan que ellos no puedan tomar turnos si no esta activo el dia
									if(!empty($Admin) && $IDClub!="12"):
										$fecha_disponible=1;	
									endif;	
									
									
									if ($fecha_disponible==1):
									
									
									//Verifico que el socio no tenga mas de x reservas en el mismo dia dependiendo la conf de disponibilidad									
									$sql_reservas_dia = $dbo->query("Select * From ReservaGeneral Where IDSocio = '".$IDSocio."' and Fecha = '".$Fecha."' and IDServicio = '".$IDServicio."' and IDSocioBeneficiario = '".(int)$IDBeneficiario."' and IDEstadoReserva = '1'");
									$total_reserva_socio = (int)$dbo->rows($sql_reservas_dia);										
									
									//Valido si en la configuracion permite a un socio tomar otro turno dspues que cumpla el que tiene en el dia solo aplica si esta en el dia actual
									$PermiteReservaDespuesdeprimerturno = $dbo->getFields( "Disponibilidad" , "PermiteReservaCumplirTurno" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
									if($PermiteReservaDespuesdeprimerturno=="S" && $Fecha == date("Y-m-d") && $total_reserva_socio>=$MaximoReservaSocioServicio):
										$TiempoDespues = (int)$dbo->getFields( "Disponibilidad" , "TiempoDespues" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
										$MedicionTiempoDespues = $dbo->getFields( "Disponibilidad" , "MedicionTiempoDespues" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );										
										$IntervaloTurno = $dbo->getFields( "Disponibilidad" , "Intervalo" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
										
										switch($MedicionTiempoDespues):
											case "Dias":	
												$minutos_posterior_turno = (60*24) * $TiempoDespues;												
											break;
											case "Horas":
												$minutos_posterior_turno = 60 * $TiempoDespues;												
											break;
											case "Minutos":
												$minutos_posterior_turno = $TiempoDespues;												
											break;
											default:
												$minutos_posterior_turno = 0;
										endswitch;
										
										//Le sumo el intervalo del turno para calcular la siguiente hora que puede reservar despues de finalizar el turno
										$minutos_posterior_turno += (int)$IntervaloTurno;
										
										//Consulto cual es la utima que reserva que tiene en el dia para calcula con esa hora
										$sql_reserva_dia_hora = "Select * From ReservaGeneral Where IDSocio = '".$IDSocio."' and Fecha = '".$Fecha."' and IDServicio = '".$IDServicio."' and IDEstadoReserva = '1' Order by Hora Desc Limit 1";
										$result_reserva_dia_hora = $dbo->query($sql_reserva_dia_hora);
										$row_reserva_dia_hora = $dbo->fetchArray($result_reserva_dia_hora);
										$ultimo_turno_dia = $Fecha . " " .$row_reserva_dia_hora["Hora"];										
										$hora_actual_peticion = date('Y-m-d H:i:s');	
										$hora_volver_reservar = strtotime ( '+'.$minutos_posterior_turno.' minute' , strtotime ( $ultimo_turno_dia ) ) ;
										//echo "Puede reservar a las " . date("Y-m-d H:i:s",$hora_volver_reservar);																				
										if(strtotime($hora_actual_peticion)>=$hora_volver_reservar):											
											$total_reserva_socio = 0;
										else:
											$mensaje_opcion_reserva = "Puede volver a reservar despues de: ".$TiempoDespues. " " .$MedicionTiempoDespues." de cumplir la reserva del dia";	
										endif;
									endif;
									
									// Si es Admin si puede reservar mas de un turno por dia
									if(!empty($Admin)):
										//verifico que si pueda reservar mientras no sea la misma hora en el mismo servicio
										$sql_reservas_dia_hora = $dbo->query("Select * From ReservaGeneral Where IDSocio = '".$IDSocio."' and Fecha = '".$Fecha."' and IDServicio = '".$IDServicio."' and Hora = '".$Hora."' and IDServicioElemento <> '".$IDElemento."' and IDEstadoReserva = '1'");
										$total_reserva_socio_hora = (int)$dbo->rows($sql_reservas_dia_hora);	
										if($total_reserva_socio_hora>0):
											$total_reserva_socio = 100;
										else:
											$total_reserva_socio = 0;	
										endif;	
												
										//$total_reserva_socio = 0;	
										$UsuarioCreacion = "Starter";										
									else:
										if(!empty($IDUsuarioReserva))
											$UsuarioCreacion = "Empleado";
										else
											$UsuarioCreacion = "Socio";											
									endif;	
									
									//Consulto el parametro en disponibilidad de cuantas veces puede reervar el socio el mismo servicio en el mismo dia													
									$MaximoReservaSocioServicio = $dbo->getFields( "Disponibilidad" , "MaximoReservaDia" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
									//if($total_reserva_socio<1):
									if($total_reserva_socio<$MaximoReservaSocioServicio):
									
									
									
									if ($Repetir=="S"):
										
										//Consulto el limite de reservas que se pueda hacer para calcular la fecha final
										$MedicionRepeticion = $dbo->getFields( "Disponibilidad" , "MedicionRepeticion" , "IDDisponibilidad = '" . $IDDisponibilidad . "'" );
										switch($MedicionRepeticion):
												case "Dia":	
													$periodo_sumar = "day";
												break;
												case "Semana":											
													$periodo_sumar = "week";
												break;
												case "Mes":
													$periodo_sumar = "month";
												break;
												default:											
													$periodo_sumar = "day";
										endswitch;
										
										//periodo a sumar dependiendo de lo que el socio escoja en el app								
										switch($Periodo):
												case "Dia":	
													$periodo_sumar_app = "day";
												break;
												case "Semana":											
													$periodo_sumar_app = "week";
												break;
												case "Mes":
													$periodo_sumar_app = "month";
												break;
												default:											
													$periodo_sumar_app = "day";
										endswitch;
										
										
										$numero_repeticion = $dbo->getFields( "Disponibilidad" , "NumeroRepeticion" , "IDDisponibilidad = '" . $IDDisponibilidad . "'" );
										// Este sirve para establecer el limite deacuerdo al admin en el parametro limite de repeticion
										//$fechaFin = strtotime ( '+'.$numero_repeticion.' '.$periodo_sumar ,  strtotime($Fecha)  ) ;	
										
										//Toma la fecha final de lo que seleccione el usuario en el app
										if(empty($RepetirFechaFinal)):
											$fechaFin = strtotime ( $RepetirFechaFinal  ) ;	
										else:
											$fechaFin = strtotime($Fecha);	
										endif;	
										
									else:
										$numero_repeticion=1;
										$fechaFin = strtotime($Fecha);
										$periodo_sumar = "day";	
									endif;
								
								
								$fechaInicio=strtotime($Fecha);
								//$fechaFin=strtotime($fecha_fin_reserva );
								//echo date("Y-m-d",$fechaFin);
								//exit;
								
								
								if(!empty($IDBeneficiario) && !empty($TipoBeneficiario)):
									if($TipoBeneficiario=="Invitado")
										$IDInvitadoBeneficiario = $IDBeneficiario;
									elseif($TipoBeneficiario=="Socio")	 
										$IDSocioBeneficiario = $IDBeneficiario;
								endif;
								
								
								
								for($contador_fecha=$fechaInicio; $contador_fecha<=$fechaFin; $contador_fecha+=86400):
								
								
												$flag_reserva_cancha_clase = 0;
												// verifico que todavia este disponible la reserva
												
												if(!empty($Tee)):
													$condicion_tee = " and Tee = '".$Tee."'";
												endif;
												
												// Verifico si el servicio en esta disponiblidad permite a varios socios tomar el mismo turno, por ejemplo clase de gimnasia												
												$cupo_total="S";// ya no hay cupos
												$cupos_disponibilidad = $dbo->getFields( "Disponibilidad" , "Cupos" , "IDDisponibilidad = '" . $id_disponibilidad . "'" );
												$datos_disponibilidad = $dbo->fetchAll( "Disponibilidad", " IDDisponibilidad = '" . $row_dispo_elemento_gral["IDDisponibilidad"] . "' ", "array" );
												if((int)$cupos_disponibilidad>1):													
													//Consulto cuantos reservas se han tomado en esta hora para saber si ya llegó al limite de cupos
													$cupos_reservados = self::valida_cupos_disponibles($IDClub, $IDServicio, $IDElemento, $Fecha, $horaInicial);
													//Valido si todavia existe cupo en esta hora
													if($cupos_reservados <= $datos_disponibilidad["Cupos"]):
														$cupo_total="N"; // aun hay cupos disponibles
													endif;
												endif;	
												
												
												
												$id_reserva_disponible = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDClub = '".$IDClub."' and IDServicio = '".$IDServicio."' and IDServicioElemento = '".$IDElemento."' and IDEstadoReserva in (1) and Fecha = '".$Fecha."' and Hora = '".$Hora."' $condicion_tee " );		
												if (empty($id_reserva_disponible) || $cupo_total=="N"):
													$datos_invitado= json_decode($Invitados, true);
													
													//Verifico que el socio no este como invitado en el mismo servicio en otra hora
													$sql_socio_grupo = "SELECT RGI.* FROM ReservaGeneralInvitado RGI, ReservaGeneral RG WHERE RG.IDReservaGeneral = RGI.IDReservaGeneral and (RGI.IDSocio = '".$IDSocio."') and RG.IDClub = '".$IDClub."' and RG.Fecha = '".$Fecha."' and RG.IDServicio = '".$IDServicio."' ORDER BY IDReservaGeneralInvitado Desc ";
													$qry_socio_grupo = $dbo->query($sql_socio_grupo);
													if ($dbo->rows($qry_socio_grupo)>0):
														$nombre_socio_invitado = $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $IDSocio . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" .$IDSocio . "'" );
														$respuesta["message"] = $nombre_socio_invitado . ", ya esta agregado(a) en esta fecha como invitado en un grupo, no es posible realizar la reserva, por favor verifique";
														$respuesta["success"] = false;
														$respuesta["response"] = NULL;	
														return $respuesta;
														exit;			
													endif;
													
													
													// Si es golf verifico que los invitado no este en mas de un grupo el mismo dia											
													if (count($datos_invitado)>0):
														foreach($datos_invitado as $detalle_datos):
															$IDSocioInvitado = $detalle_datos["IDSocio"];
															$NombreSocioInvitado = $detalle_datos["Nombre"];													
															if(!empty($IDSocioInvitado)):
																$respuesta_valida_invitado=SIMWebService::verificar_socio_grupo($IDClub,$IDSocioInvitado,$Fecha,$IDServicio);
																if ($respuesta_valida_invitado==1):																
																	$nombre_socio_invitado = $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $IDSocioInvitado . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $IDSocioInvitado . "'" );
																	$respuesta["message"] = "El invitado: ". utf8_encode($nombre_socio_invitado) . ", solo puede estar en un grupo por dia";
																	$respuesta["success"] = false;
																	$respuesta["response"] = NULL;	
																	return $respuesta;
																	exit;
																endif;													
															endif;	
														endforeach;
													endif;	
													
													
													// Si el servicio es una clase y necesita reservar cancha
													$id_servicio_cancha = $dbo->getFields( "ServicioMaestro" , "IDServicioMaestroReservar" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
													if($id_servicio_cancha>0):
																// Consulto el servicio del club asociado a este servicio maestro
																$IDServicioCanchaClub  = $dbo->getFields( "Servicio" , "IDServicio" , "IDServicioMaestro = '" . $id_servicio_cancha . "' and IDClub = '".$IDClub."'" );
															  // Valido si existe una cancha disponible en el horario de la clase
															  $IDElemento_cancha = SIMWebService::buscar_elemento_disponible($IDClub,$IDServicioCanchaClub,$Fecha,$Hora);
															  if(empty($IDElemento_cancha)):
																	$respuesta["message"] = "Lo sentimos no existe una cancha disponible para tomar la clase en este horario";
																	$respuesta["success"] = false;
																	$respuesta["response"] = NULL;	
																	return $respuesta;
															 else:		
																$flag_reserva_cancha_clase = 1;																
															 endif;
													endif;
													
													
													
													/*
													//Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
													//Si el servicio maestro tiene definido permitir turnos seguidos cuando los invitados sean mas de X personas
													$numero_para_reservar_turnos = $dbo->getFields( "ServicioMaestro" , "InvitadoTurnos" , "IDServicioMaestro = '" . $id_servicio_maestro . "'" );
													if( ( (int)$numero_para_reservar_turnos>0 ) && count($datos_invitado)>=$numero_para_reservar_turnos): 																					
													//if($id_servicio_maestro==14): //Tennis
														$cantidad_turnos = 1; // Para validar los siguientes X turnos esten disponibles 
														$array_hora_siguiente_turno_diponible = SIMWebService::valida_siguiente_turno_disponible($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos );
														if(count($array_hora_siguiente_turno_diponible)!=$cantidad_turnos):
															$respuesta["message"] = "Se necesitan ".$cantidad_turnos." turnos mas seguidos y el siguiente turno no esta disponible, por favor seleccione otro opcion.";
															$respuesta["success"] = false;
															$respuesta["response"] = NULL;	
															return $respuesta;
														else:
															$flag_separa_siguiente_turno=1;	
														endif;	
													endif;
													*/
													
													
													//Si Invitados son X y el servicio es de mas 2 turnos se valida que el siguiente turno este disponible
													if (!empty($IDTipoReserva)):				
															$datos_tipo_reserva = $dbo->fetchAll( "ServicioTipoReserva", " IDServicioTipoReserva = '" . $IDTipoReserva . "' ", "array" );
															$cantidad_turnos = $datos_tipo_reserva["NumeroTurnos"];		
															$cantidad_minima_participantes = $datos_tipo_reserva["MinimoParticipantes"];		
														
														if ((count($datos_invitado) + $cantidad_auxiliar)>=(int)$cantidad_minima_participantes):
															// valido que no vengas mas de los participantes que es necesario
															if ( (count($datos_invitado) + $cantidad_auxiliar)>=(int)$cantidad_minima_participantes):															
																	if( ( (int)$cantidad_turnos>1 )):						
																		//$cantidad_turnos-=1; // Quito uno para que no cuente la reserva primera 			
																		
																		// Separo las reservas
																		$array_hora_siguiente_turno_diponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $cantidad_turnos );			
																		if(count($array_hora_siguiente_turno_diponible)!=(int)($cantidad_turnos-1) || !is_array($array_hora_siguiente_turno_diponible)):
																			$respuesta["message"] = "Se necesitan ".$cantidad_turnos." turnos mas seguidos y el siguiente turno no esta disponible. Por favor seleccione otra opcion";
																			$respuesta["success"] = false;
																			$respuesta["response"] = NULL;	
																			return $respuesta;			
																		else:
																			$flag_separa_siguiente_turno=1;						
																		endif;								
																	endif;
															else:
																$respuesta["message"] = "Lo sentimos, el maximo numero de invitados debe ser de " . $cantidad_minima_participantes;
																$respuesta["success"] = false;
																$respuesta["response"] = NULL;	
																return $respuesta;	
															endif;
																	
														else:
															$respuesta["message"] = "Lo sentimos, el minimo numero de invitados debe ser de: " . $cantidad_minima_participantes;
															$respuesta["success"] = false;
															$respuesta["response"] = NULL;	
															return $respuesta;		
														endif;	
													endif;	
													
													
													//Si turnos es mayor a 1 verifico que los siguientes turnos esten disponibles y los reservo
													if((int)$NumeroTurnos>1):
													
														if($id_servicio_maestro==15 || $id_servicio_maestro==27 || $id_servicio_maestro==28 || $id_servicio_maestro==30): //Golf															
															$array_disponible = SIMWebService::valida_siguiente_turno_disponible_golf($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos, $Tee,"Reserva" );
														else:
															$array_disponible = SIMWebService::valida_siguiente_turno_sin_reserva($Fecha, $Hora, $IDSocio, $IDServicio, $IDClub, $IDElemento, $NumeroTurnos );
														endif;																
														
														//Cuando es por grupos busco aleatoriamente de los invitados los socios que quedaran como cabeza de grupo
														$array_cabeza_grupo = SIMWebService::busca_cabeza_grupo($Invitados, $NumeroTurnos, $IDSocio);
														
														if(count($array_disponible)!=$NumeroTurnos):
															$respuesta["message"] = "Se necesitan: ".$NumeroTurnos." turnos mas seguidos y el siguiente turno no esta disponible, por favor seleccione otra opcion.";
															$respuesta["success"] = false;
															$respuesta["response"] = NULL;	
															return $respuesta;					
														else:
															$contador_turno = 0;
															// separo los siguientes turnos disponibles menos el primero que se realiza en el siguiente proceso
															foreach($array_disponible as $key_hora => $dato_hora):
																	if($contador_turno>0):
																		$socios_cabeza=0;
																		$contador_socio_cabeza_real=0;
																		$IDSocioCabeza = $array_cabeza_grupo[($contador_turno-1)];
																		if(empty($IDSocioCabeza))
																			$IDSocioCabeza = $IDSocio;
																			
																		// Registro los socios cabeza como ingresados para que no queden como invitados
																		foreach($array_cabeza_grupo as $id_socio_cabeza => $datos_socio_cabeza):
																			$array_invitado_agregado[]=$datos_socio_cabeza;	
																			if($IDSocio<>$datos_socio_cabeza):
																				$contador_socio_cabeza_real++; 
																			endif;
																		endforeach;
																		
																		
																		$sql_inserta_reserva_turno = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, Fecha, Hora, Tee, UsuarioTrCr, FechaTrCr) 
																										Values ('".$IDClub."','".$IDSocioCabeza."', '".$IDSocio."', '".$IDUsuarioReserva."', '".$IDServicio."','".$IDElemento."', '1','".$id_disponibilidad."','".$IDReservaGrupos."','".$IDInvitadoBeneficiario."','".$IDSocioBeneficiario."','".$Fecha."', '".$dato_hora."','".$Tee."', 'WebService Automatica',NOW())");
																		$id_reserva_general_turno = $dbo->lastID();		
																		
																																						
																		//Inserto los invitados
																		$datos_invitado_turno= json_decode($Invitados, true);
																		//Reparto los jugadores por turnos
																		$total_invitados_turno = count($datos_invitado_turno);
																		
																		if($contador_socio_cabeza_real>=1)
																			$socios_cabeza = $contador_socio_cabeza_real + 1;
																		else
																			$socios_cabeza=0;	
																		
																		$invitados_por_turno = ((int)( ($total_invitados_turno+1) - $socios_cabeza)/$NumeroTurnos);
																		
																		
																		
																		$contador_invitado_agregado = 1;
																		if (count($datos_invitado_turno)>0):
																			foreach($datos_invitado_turno as $detalle_datos_turno):																			
																				$IDSocioInvitadoTurno = $detalle_datos_turno["IDSocio"];
																				$NombreSocioInvitadoTurno = $detalle_datos_turno["Nombre"];
																				// Guardo los invitados socios o externos									
																				$datos_invitado_actual=$IDSocioInvitadoTurno."-".$NombreSocioInvitadoTurno;
																				if(!in_array($datos_invitado_actual,$array_invitado_agregado)):																				
																					if($contador_invitado_agregado<=(int)$invitados_por_turno):
																						$sql_inserta_invitado_turno = $dbo->query("Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre) 
																														Values ('".$id_reserva_general_turno."','".$IDSocioInvitadoTurno."', '".$NombreSocioInvitadoTurno."')");																								
																						$array_invitado_agregado[]=$IDSocioInvitadoTurno."-".$NombreSocioInvitadoTurno;	
																						//Envio push al invitado para notificarle si es un invitado socio
																						if(!empty($IDSocioInvitadoTurno)){
																							//SIMUtil::push_socio_invitado($IDClub,$id_reserva_general_turno,$IDSocioInvitadoTurno );
																						}
																					endif;									
																				else:
																					$contador_invitado_agregado=0;		
																				endif;	
																				$contador_invitado_agregado++;
																			endforeach;
																		endif;	
																		// Borro la reserva separada
																		$sql_inserta_reserva = $dbo->query("Delete From ReservaGeneral Where IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicioElemento	 = '".$IDElemento."' and Fecha = '".$Fecha."' and Hora = '".$dato_hora."' and IDEstadoReserva  = 3");												
																	endif;
																	
																	
																	$contador_turno++;
															endforeach;		
															//$respuesta["message"] = "guardado";
															//$respuesta["success"] = true;
															//$respuesta["response"] = $id_reserva_general;						
															//return $respuesta;
														endif;		
													endif;
													
													
													
													
													
													// Borro la reserva separada
													$sql_inserta_reserva = $dbo->query("Delete From ReservaGeneral Where IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicioElemento	 = '".$IDElemento."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDEstadoReserva  = 3");											
													// Borro la reserva separada automaticas
													$sql_inserta_reserva_aut = $dbo->query("Delete From ReservaGeneralAutomatica Where IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicioElemento	 = '".$IDElemento."' and Fecha = '".$Fecha."' and Hora = '".$Hora."' and IDEstadoReserva  = 3");											
												
												
													// Si se va a reservas mas turnos seguidos y la validacion fue exitosa borro las separacion hechas
													if ($flag_separa_siguiente_turno==1 && count($array_hora_siguiente_turno_diponible)>0):												
															foreach($array_hora_siguiente_turno_diponible as $Hora_siguiente):
																// Borro la reserva separada														
																$sql_inserta_reserva = $dbo->query("Delete From ReservaGeneral Where IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicioElemento	 = '".$IDElemento."' and Fecha = '".$Fecha."' and Hora = '".$Hora_siguiente."' and IDEstadoReserva  = 3");
																// Borro la reserva separada automaticas
																$sql_inserta_reserva_aut = $dbo->query("Delete From ReservaGeneralAutomatica Where IDClub = '".$IDClub."' and IDSocio = '".$IDSocio."' and IDServicioElemento	 = '".$IDElemento."' and Fecha = '".$Fecha."' and Hora = '".$Hora_siguiente."' and IDEstadoReserva  = 3");
												
															endforeach;																								
													endif;
													
												
													//Guardo la reserva maestra
													$sql_inserta_reserva = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, UsuarioTrCr, FechaTrCr) 
																					Values ('".$IDClub."','".$IDSocio."','".$IDSocio."', '".$IDUsuarioReserva."', '".$IDServicio."','".$IDElemento."', '1','".$id_disponibilidad."','".$IDReservaGrupos."','".$IDInvitadoBeneficiario."','".$IDSocioBeneficiario."','".$Fecha."', '".$Hora."','".$Observaciones."','".$Tee."','".$IDAuxiliar."','".$IDTipoModalidadEsqui."','".$UsuarioCreacion."',NOW())");											
													
													$id_reserva_general = $dbo->lastID();
												
													$datos_invitado= json_decode($Invitados, true);
													if (count($datos_invitado)>0):
														foreach($datos_invitado as $detalle_datos):
															$IDSocioInvitado = $detalle_datos["IDSocio"];
															$NombreSocioInvitado = $detalle_datos["Nombre"];
															$datos_invitado_actual=$IDSocioInvitado."-".$NombreSocioInvitado;
															if(!in_array($datos_invitado_actual,$array_invitado_agregado)):
																// Guardo los invitados socios o externos									
																$sql_inserta_invitado = $dbo->query("Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre) 
																								Values ('".$id_reserva_general."','".$IDSocioInvitado."', '".$NombreSocioInvitado."')");			
																
																
																
																
																//Envio push al invitado para notificarle si es un invitado socio
																if(!empty($IDSocioInvitado)){
																	SIMUtil::push_socio_invitado($IDClub,$id_reserva_general,$IDSocioInvitado );								
																}
															endif;									
														endforeach;
													endif;	
												
											
													$array_Campos = $Campos;
													if (count($array_Campos)>0):
														foreach($array_Campos as $id_valor_campo => $valor_campo):	
															// Guardo los campos personalizados									
															$sql_inserta_campo = $dbo->query("Insert Into ReservaGeneralCampo (IDReservaGeneral, IDServicioCampo, Valor) 
																								Values ('".$id_reserva_general."','".$valor_campo["IDServicioCampo"]."', '".$valor_campo["Valor"]."')");
														endforeach;									
													endif;	
													
													
													// Si se va a reservas mas turnos seguidos y la validacion fue exitosa ingreso las demas reservas
													if ($flag_separa_siguiente_turno==1 && count($array_hora_siguiente_turno_diponible)>0):												
															foreach($array_hora_siguiente_turno_diponible as $Hora_siguiente):
																	//Guardo la reserva
																	$sql_inserta_reserva_duplicar = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio,IDUsuarioReserva,  IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, Tipo, UsuarioTrCr, FechaTrCr) 
																									Values ('".$IDClub."','".$IDSocio."', '".$IDUsuarioReserva."', '".$IDServicio."','".$IDElemento."', '1','".$id_disponibilidad."','".$IDReservaGrupos."','".$IDInvitadoBeneficiario."','".$IDSocioBeneficiario."','".$Fecha."', '".$Hora_siguiente."','".$Observaciones."','".$Tee."','".$IDAuxiliar."','".$IDTipoModalidadEsqui."','Automatica','".$UsuarioCreacion."',NOW())");											
																	$id_reserva_general_duplicar = $dbo->lastID();															
																	// Duplico los invitados de la reserva padre
																	$sql_invitado_duplicar = $dbo->query("Insert into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre) Select '".$id_reserva_general_duplicar."', IDSocio, Nombre From ReservaGeneralInvitado Where IDReservaGeneral = '".$id_reserva_general."'");														
																	// Guardar relacion de reservas automaticas
																	$sql_reserva_automatica = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva) 
																			  Values ('".$id_reserva_general."','".$id_reserva_general_duplicar."','".$IDClub."','".$IDSocio."','".$IDElemento."','".$Fecha."','".$Hora_siguiente."','1')");
																	
															endforeach;																								
													endif;
													
													
													// SI el servicio es una clase y se solicta reservar una cancha realizo la reserva
													if($flag_reserva_cancha_clase==1):
														// Obtener la disponibilidad utilizada al consultar la reserva
														$id_disponibilidad_cancha = SIMWebService::obtener_disponibilidad_utilizada($IDServicioCanchaClub,$Fecha,$IDElemento_cancha);
														$sql_inserta_reserva_cancha = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, Tipo, UsuarioTrCr, FechaTrCr) 
														Values ('".$IDClub."','".$IDSocio."', '".$IDUsuarioReserva."', '".$IDServicioCanchaClub."','".$IDElemento_cancha."', '1','".$id_disponibilidad."','".$IDReservaGrupos."','".$IDInvitadoBeneficiario."','".$IDSocioBeneficiario."','".$Fecha."', '".$Hora."','".$Observaciones."','".$Tee."','".$IDAuxiliar."','".$IDTipoModalidadEsqui."','Automatica','".$UsuarioCreacion."',NOW())");											
														$id_reserva_cancha = $dbo->lastID();												
														
														// Guardar relacion de reservas automaticas
														$sql_reserva_automatica = $dbo->query("Insert Into ReservaGeneralAutomatica (IDReservaGeneral, IDReservaGeneralAsociada, IDClub, IDSocio,IDServicioElemento, Fecha, Hora, IDEstadoReserva) 
																  Values ('".$id_reserva_general."','".$id_reserva_cancha."','".$IDClub."','".$IDSocio."','".$IDElemento_cancha."','".$Fecha."','".$Hora."','1')");
														
													endif;
													
													
													SIMUtil::notificar_nueva_reserva($id_reserva_general);
														
													$respuesta["message"] = "guardado";
													$respuesta["success"] = true;
													$respuesta["response"] = "guardado";	
												else:
													$respuesta["message"] = "Lo sentimos la reserva ya fue tomada.";
													$respuesta["success"] = false;
													$respuesta["response"] = NULL;		
												
												endif;
												
										
										
										//$contador_fecha = strtotime ( '+1 '.$periodo_sumar ,  $contador_fecha  ) ;								
										//$contador_fecha = strtotime ( '+'.$numero_repeticion.' '.$periodo_sumar ,  strtotime($Fecha)  ) ;			
										$contador_fecha = strtotime ( '+1 '.$periodo_sumar_app ,  strtotime($Fecha)  ) ;
										$Fecha = date("Y-m-d",$contador_fecha);
										
										
												
												
									endfor;		
									
									else:
										$respuesta["message"] = "Lo sentimos solo se permiten ".$MaximoReservaSocioServicio." reserva por dia. " . $mensaje_opcion_reserva;
										$respuesta["success"] = false;
										$respuesta["response"] = NULL;	
									endif;	
						
							
						else:
								$respuesta["message"] = "Lo sentimos fecha no disponible";
								$respuesta["success"] = false;
								$respuesta["response"] = NULL;	
						endif;
			else:
				$respuesta["message"] = "Lo sentimos no se puede reservar turnos seguidos ";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
			endif;			
		
		 else:
				$respuesta["message"] = "Lo sentimos, el maximo numero de invitados para poder reservar es: " .$MaximoInvitados;
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		 endif;	
			
		else:
				$respuesta["message"] = "Lo sentimos el minimo de invitados para poder reservar es: " .$MinimoInvitados;
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;	
		endif;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "21. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function set_invitado_servicio($IDClub,$IDReserva,$Invitados)
 {
	$dbo =& SIMDB::get();	
	
	if( !empty( $IDClub ) && !empty( $IDReserva ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_reserva = $dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , "IDReservaGeneral = '" . $IDReserva . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_reserva)){
			//Borro los invitado anteriores
			$del_invitado = "Delete From ReservaGeneralInvitado Where IDReservaGeneral = '".$IDReserva."'";
			$dbo->query($del_invitado);
			
			
			$nuevacadena = str_replace('Optional("',"",$Invitados);
			$nuevacadena = str_replace('")',"",$nuevacadena);
			$Invitados = $nuevacadena;
			
			$datos_invitado= json_decode($Invitados, true);
			if (count($datos_invitado)>0):
				foreach($datos_invitado as $detalle_datos):
					$IDSocioInvitado = $detalle_datos["IDSocio"];
					$NombreSocioInvitado = $detalle_datos["Nombre"];
					// Guardo los invitados socios o externos									
					$sql_inserta_invitado = $dbo->query("Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre) 
														Values ('".$id_reserva."','".$IDSocioInvitado."', '".$NombreSocioInvitado."')");	
				endforeach;
			endif;	
			
			/*
			$array_Invitados = $Invitados;
			if (count($array_Invitados)>0):
				foreach($array_Invitados as $id_valor => $valor):	
					// Guardo los invitados socios o externos									
					$sql_inserta_invitado = $dbo->query("Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre) 
														Values ('".$id_reserva."','".$valor["IDSocioInvitado"]."', '".$valor["NombreInvitado"]."')");
				endforeach;									
			endif;										
			*/
					
	
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = "guardado";	
			
		}
		else{
			$respuesta["message"] = "Error la reserva no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "22. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function del_invitado_servicio($IDClub,$IDReserva,$IDReservaGeneralInvitado)
 {
	$dbo =& SIMDB::get();	
	
	if( !empty( $IDClub ) && !empty( $IDReserva ) && !empty( $IDReservaGeneralInvitado ) ){
		
		//verifico que el invitado exista y pertenezca al club		
		$id_invitado_reserva = $dbo->getFields( "ReservaGeneralInvitado" , "IDReservaGeneralInvitado" , "IDReservaGeneralInvitado = '" . $IDReservaGeneralInvitado . "'" );
		
		if (!empty($id_invitado_reserva)){
			
			
			// Borrar los invitados socios o externos									
			$sql_elimina_invitado = $dbo->query("Delete From ReservaGeneralInvitado Where IDReservaGeneralInvitado = '".$IDReservaGeneralInvitado."'");
					
	
			$respuesta["message"] = "eliminado";
			$respuesta["success"] = true;
			$respuesta["response"] = "eliminado";	
			
		}
		else{
			$respuesta["message"] = "Error la reserva no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "23. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	
function set_reserva_golf($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,$Tee)
 {
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio )  && !empty( $IDElemento ) && !empty( $IDServicio ) && !empty( $Fecha ) && !empty( $Hora ) && !empty( $Tee ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			//Guardo la reserva
			$sql_inserta_reserva = $dbo->query("Insert Into ReservaGeneral (IDClub, IDSocio, IDServicio, IDServicioElemento, IDEstadoReserva, Fecha, Hora, Tee, UsuarioTrCr, FechaTrCr) 
											    Values ('".$IDClub."','".$IDSocio."', '".$IDServicio."','".$IDElemento."', '1','".$Fecha."', '".$Hora."', '".$Tee."', 'WebService',NOW())");
			
			$id_reserva_general = $dbo->lastID();
			
			$array_Invitados = $Invitados;
			if (count($array_Invitados)>0):
				foreach($array_Invitados as $id_valor => $valor):	
					// Guardo los invitados socios o externos									
					$sql_inserta_invitado = $dbo->query("Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre) 
														Values ('".$id_reserva_general."','".$valor["IDSocioInvitado"]."', '".$valor["NombreInvitado"]."')");
				endforeach;									
			endif;										
	
			$array_Campos = $Campos;
			if (count($array_Campos)>0):
				foreach($array_Campos as $id_valor_campo => $valor_campo):	
					// Guardo los campos personalizados									
					$sql_inserta_campo = $dbo->query("Insert Into ReservaGeneralCampo (IDReservaGeneral, IDServicioCampo, Valor) 
														Values ('".$id_reserva_general."','".$valor_campo["IDServicioCampo"]."', '".$valor_campo["Valor"]."')");
			endforeach;									
			endif;										
					
	
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "24. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}


function set_cambiar_clave($IDClub,$IDSocio,$Clave)
{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio )  && !empty( $Clave ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			
			$sql_cambiar = "Update Socio Set Clave =  sha1('".$Clave."') Where IDSocio = '".$IDSocio."' and IDClub = '".$IDClub."'";
			$dbo->query($sql_cambiar);
	
			$respuesta["message"] = "clave modificada con exito";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "25. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	function set_cambiar_clave_empleado($IDClub,$IDUsuario,$Clave)
	{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDUsuario )  && !empty( $Clave ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Usuario" , "IDUsuario" , "IDUsuario = '" . $IDUsuario . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			
			$sql_cambiar = "Update Usuario Set Password =  sha1('".$Clave."') Where IDUsuario = '".$IDUsuario."' and IDClub = '".$IDClub."'";
			$dbo->query($sql_cambiar);
	
			$respuesta["message"] = "clave modificada con exito";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el usuario no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "25. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}	
	
	function verifica_version_app($IDClub,$AppVersion, $Dispositivo,$TipoApp=""){
		$dbo =& SIMDB::get();	
		if($Dispositivo=="Android"):
			$CampoVersion = "VersionAndroid";
			$CampoEsencial = "EsencialAndroid";	
			$CampoMensaje = "VersionMessageAndroid";					
			$CampoUrl = "VersionURLAndroid";
		else:
			$CampoVersion = "Version";
			$CampoEsencial = "Esencial";	
			$CampoMensaje = "VersionMessage";		
			$CampoUrl = "VersionURLIOS";
		endif;
		
		if($TipoApp=="Empleado"):
			//Consulto cual debe ser la ultima la version de empleados segun Dispositivo
			$datos_appempleado = $dbo->fetchAll( "AppEmpleado", " IDClub = '" . $IDClub . "' ", "array" );		
			$numero_version = $datos_appempleado[$CampoVersion];
			$esencial_version = $datos_appempleado[$CampoEsencial];
			if($datos_club[$CampoVersion]!=$AppVersion && $datos_club[$CampoEsencial]=="S"):				
				$respuesta["message"] = $datos_club[$CampoMensaje] . " " . $datos_club[$CampoUrl];
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				//inserta _log				
				$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('verifica_version_app','".json_encode($_GET)."','".json_encode($respuesta)."')");					
				die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );	
				exit;
			endif;
		else:
			//Consulto cual debe ser la ultima la version funcionando segun Dispositivo
			$datos_club = $dbo->fetchAll( "Club", " IDClub = '" . $IDClub . "' ", "array" );		
			$numero_version = $dbo->getFields( "Club" , $CampoVersion , "IDClub = '".$IDClub."'" );
			$esencial_version = $dbo->getFields( "Club" , $CampoEsencial , "IDClub = '".$IDClub."'" );
			
			if($datos_club[$CampoVersion]!=$AppVersion && $datos_club[$CampoEsencial]=="S"):				
				$respuesta["message"] = $datos_club[$CampoMensaje] . " " . $datos_club[$CampoUrl];
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
				//inserta _log				
				$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('verifica_version_app','".json_encode($_GET)."','".json_encode($respuesta)."')");					
				die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );	
				exit;
			endif;
		endif;
	}
	
	
function set_token($IDClub,$IDSocio,$Dispositivo,$Token)
{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDSocio )  && !empty( $Token ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "IDSocio = '" . $IDSocio . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			$sql_seccion_not = $dbo->query("Update Socio Set Dispositivo = '".$Dispositivo."', Token = '".$Token."' Where IDSocio = '".$IDSocio."'");
	
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el socio no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "26. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}
	
	function set_token_empleado($IDClub,$IDUsuario,$Dispositivo,$Token)
{
	$dbo =& SIMDB::get();	
	if( !empty( $IDClub ) && !empty( $IDUsuario )  && !empty( $Token ) ){
		
		//verifico que el socio exista y pertenezca al club
		$id_usuario = $dbo->getFields( "Usuario" , "IDUsuario" , "IDUsuario = '" . $IDUsuario . "' and IDClub = '".$IDClub."'" );
		
		if (!empty($id_socio)){
			
			$sql_seccion_not = $dbo->query("Update Usuario Set Dispositivo = '".$Dispositivo."', Token = '".$Token."' Where IDsuario = '".$IDUsuario."'");
	
			$respuesta["message"] = "guardado";
			$respuesta["success"] = true;
			$respuesta["response"] = NULL;	
			
		}
		else{
			$respuesta["message"] = "Error el usuario no existe o no pertenece al club";
			$respuesta["success"] = false;
			$respuesta["response"] = NULL;
		}
		
	}
	else{
		$respuesta["message"] = "26. Atencion faltan parametros";
		$respuesta["success"] = false;
		$respuesta["response"] = NULL;
	}
	
	return $respuesta;
		
	}
	
	
	
}//end class
?>
