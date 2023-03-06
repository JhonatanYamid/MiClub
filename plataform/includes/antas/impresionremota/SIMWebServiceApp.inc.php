<?php

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class SIMWebServiceApp
{

	function verifica_permiso_modulo( $IDModulo,$IDPerfil ) {
		$dbo =& SIMDB::get();
		$agregar_modulo = "S";

		//Dependiendo el perfil muestro o no los modulos
						switch($IDModulo){
							case "1": //Invitados, Solo porteria y admin puede tener acceso a este modulo
								if($IDPerfil =="4"){
									$agregar_modulo = "S";
								}
								else{
									$agregar_modulo = "N";
								}
							break;
							case "29": //Agenda, Solo porteria y admin puede tener acceso a este modulo
								if($IDPerfil =="2" || $IDPerfil =="3" || $IDPerfil =="10"){
									$agregar_modulo = "S";
								}
								else{
									$agregar_modulo = "N";
								}
							break;
							case "2": //Reservas, Solo porteria y admin puede tener acceso a este modulo
								if($IDPerfil =="2" || $IDPerfil =="3" || $IDPerfil =="10"){
									$agregar_modulo = "S";
								}
								else{
									$agregar_modulo = "N";
								}
							break;
						}

						//Perfil admin puede ver todo
						if($IDPerfil  == "1"):
							$agregar_modulo = "S";
						endif;

			return 	$agregar_modulo;


	}

	function valida_usuario_web( $email,$clave,$id_club )
	{

		$dbo =& SIMDB::get();
		$foto_cod_barras = "";

		if( !empty( $email ) && !empty( $clave )  ){

					$sql_verifica = "SELECT * FROM Usuario WHERE User = '".$email ."' and Password = '".sha1($clave )."' and IDClub = '".$id_club."' and Activo <> 'N'";

					$qry_verifica = $dbo->query( $sql_verifica );
					if( $dbo->rows( $qry_verifica ) == 0 )
					{
						$respuesta["message"] = "No encontrado";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					}//end if
					else{
						$datos_usuario = $dbo->fetchArray( $qry_verifica );


						//Modulos Sistema Menu Central
				$response_modulo = array();
				$sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '".$id_club."' and Activo = 'S' and Ubicacion like '%Central%' ORDER BY Orden";
				$qry_modulo = $dbo->query( $sql_modulo );
				if( $dbo->rows( $qry_modulo ) > 0 )
				{
					while( $r_modulo = $dbo->fetchArray( $qry_modulo ) )
					{

						$agregar_modulo  = self::verifica_permiso_modulo ($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);


						if($agregar_modulo=="S"):
							// Verificar si el modulo tiene contenido para mostrar
							$flag_mostrar = SIMWebService::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
							//$flag_mostrar=0;
							if($flag_mostrar==0):
								$modulo["IDClub"] = $id_club;
								$modulo["IDModulo"] = $r_modulo["IDModulo"];
								if(!empty($r_modulo["Titulo"]))
									$modulo["NombreModulo"] = utf8_encode($r_modulo["Titulo"]);
								else
									$modulo["NombreModulo"] = utf8_decode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));


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
						endif;

					}//ednw while
				}


							//Modulos Sistema Menu Lateral
							unset($modulo);
							$response_modulo_lateral = array();
							$sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '".$id_club."' and Activo = 'S' and Ubicacion like '%Lateral%' ORDER BY Orden";
							$qry_modulo = $dbo->query( $sql_modulo );
							if( $dbo->rows( $qry_modulo ) > 0 )
							{

								while( $r_modulo = $dbo->fetchArray( $qry_modulo ) )
								{
									$agregar_modulo  = self::verifica_permiso_modulo ($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);

									if($agregar_modulo=="S"):
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
									endif;

								}//ednw while
							}

							//traer servicios del usuario
							$response_servicio = array();
							$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $datos_usuario["IDUsuario"] . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
							$qry_servicios = $dbo->query( $sql_servicios );
							while( $r_servicio = $dbo->fetchArray( $qry_servicios  ) )
							{
									$servicio["IDClub"] = $id_club;
									$servicio["IDServicio"] = $r_servicio["IDServicio"];
									$servicio["NombreServicio"] = $dbo->getFields( "ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r_servicio["IDServicioMaestro"] . "' " );
									if (!empty($r_servicio["Icono"])):
										$foto = SERVICIO_ROOT.$r_servicio["Icono"];
									else:
										$foto="";
									endif;

									$servicio["Icono"] = $foto;
									//$servicio["ServicioInicial"] = $dbo->getFields( "ServicioInicial" , "Nombre" , "IDServicioInicial = '".$r_servicio["IDServicioInicial"]."'" );
									array_push($response_servicio, $servicio);

							}//end while

							$tipo_codigo_carne = $dbo->getFields( "AppEmpleado" , "TipoCodigoCarne" , "IDClub = '".$id_club."'" );
							switch($tipo_codigo_carne){
								case "Barras":
									if (!empty($datos_usuario["CodigoBarras"])){
										$foto_cod_barras = 	USUARIO_ROOT.$datos_usuario["CodigoBarras"];
									}
								break;
								case "QR":
									if (!empty($datos_usuario["CodigoQR"])){
										$foto_cod_barras = 	USUARIO_ROOT."qr/".$datos_usuario["CodigoQR"];
									}
								break;
								default:
								 $foto_cod_barras = 	"";
							}

							if (!empty($datos_usuario["Foto"])){
								$foto_empleado = 	USUARIO_ROOT.$datos_usuario["Foto"];
							}


							$response["IDClub"] = $datos_usuario["IDClub"];
							$response["IDUsuario"] = $datos_usuario["IDUsuario"];
							$response["IDPerfil"] = $datos_usuario["IDPerfil"];
							$response["Nombre"] = $datos_usuario["Nombre"];
							$response["Autorizado"] = $datos_usuario["Autorizado"];
							$response["Nivel"] = $datos_usuario["Nivel"];
							$response["Permiso"] = $datos_usuario["Permiso"];
							$response["ServiciosReserva"] = $response_servicio;
							$response["Modulos"] = $response_modulo;
							$response["ModulosLateral"] = $response_modulo_lateral;
							$response["CodigoBarras"] = $foto_cod_barras;
							$response["Dispositivo"] =  $datos_usuario["Dispositivo"];
							$response["Token"] = $datos_usuario["Token"];
							//$response["NumeroDerecho"] = $datos_usuario["CodigoUsuario"];
							$response["NumeroDerecho"] = "";
							//Consulto si el app esta configurado para permitir se puede cambiar p[ara que sea por usuario
							$response["PermiteInvitacionPortero"] = $dbo->getFields( "AppEmpleado" , "PermiteInvitacionPortero" , "IDClub = '".$id_club."'" );
							//Consulto las areas
							$sql_area_usuario="Select * From UsuarioArea Where IDUsuario = '".$datos_usuario["IDUsuario"]."'";
							$result_area_usuario=$dbo->query($sql_area_usuario);
							while($row_area=$dbo->fetchArray($result_area_usuario)):
								$nombre_area = utf8_encode($dbo->getFields( "Area" , "Nombre" , "IDArea = '".$row_area["IDArea"]."'" ));
								$array_areas [] = $nombre_area;
							endwhile;
							if(count($array_areas)>0):
								$nombre_areas = implode(",",$array_areas);
							endif;


							$nombre_areas="";
							$response["Area"] = $nombre_areas;
							$response["Cargo"] = utf8_encode($datos_usuario["Cargo"]);
							$response["Codigo"] = $datos_usuario["CodigoUsuario"];
							$response["PermiteReservar"] = $datos_usuario["PermiteReservar"];
							$response["Activo"] = $datos_usuario["Activo"];
							$response["Foto"] = $foto_empleado;
							$response["TipoUsuario"] =  "Empleado";


							$respuesta["message"] = "ok";
							$respuesta["success"] = true;
							$respuesta["response"] = $response;
					}
				}
			else{
				$respuesta["message"] = "1. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}


			return $respuesta;

	}//end function


	function get_agenda($IDClub,$IDUsuario,$Fecha){
		$dbo =& SIMDB::get();

		if(empty($Fecha)):
		  $Fecha=date("Y-m-d");
		endif;

		if( !empty( $IDUsuario ) ){
			//Consulto el servicio que tiene permiso y el elemnto para consultar la agenda
			$sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
			$qry_servicios = $dbo->query( $sql_servicios );
			$response_agenda = array();
			$response = array();
			$agenda_dia = false;
			while( $r_servicio = $dbo->fetchArray( $qry_servicios  ) )
			{
					//Consulto solo los elementos al los que tiene permiso de ver
					//$sql_elementos = "Select * From UsuarioServicioElemento Where IDUsuario = '".$IDUsuario."'";
					$sql_elementos = "Select *
									  From UsuarioServicioElemento USES, ServicioElemento SE
									  Where SE.IDServicioElemento = USES.IDServicioElemento
									  and IDServicio = '".$r_servicio["IDServicio"]."'
									  and IDUsuario = '".$IDUsuario."'";

					$qry_elementos = $dbo->query($sql_elementos);
					while($row_elemento = $dbo->fetchArray($qry_elementos)):
						//Si el elemnto pertenece al servicio lo consulto
						$horas = SIMWebService::get_disponiblidad_elemento_servicio( $IDClub, $r_servicio["IDServicio"], $Fecha, $row_elemento["IDServicioElemento"],"Agenda","","","","S");
						if($horas["response"][0]):
							if(count($horas["response"][0]["Disponibilidad"][0])>0):
								$agenda_dia = true;
								array_push($response, $horas["response"][0]);
							endif;

						endif;
					endwhile;

			}//end while

			if($agenda_dia):
				//$response["Agenda"] = $response_agenda;
				$respuesta["message"] = "ok";
				$respuesta["success"] = true;
				$respuesta["response"] = $response;
			else:
				//$response["Agenda"] = $response_agenda;
				$respuesta["message"] = "No tiene reservas para hoy.";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			endif;




		}
		else{
				$respuesta["message"] = "28. Atencion faltan parametros en agenda";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}
		return $respuesta;
	}




	function get_parametros_empleados( $id_club )
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


				//Consulto el icono de contratistas
				//Modulos Sistema Menu Central
				$response_modulo = array();
				$sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '".$id_club."' and Activo = 'S' and Ubicacion like '%Central%' and IDModulo = 26 ORDER BY Orden";
				$qry_modulo = $dbo->query( $sql_modulo );
				if( $dbo->rows( $qry_modulo ) > 0 )
				{
					while( $r_modulo = $dbo->fetchArray( $qry_modulo ) )
					{
						// Verificar si el modulo tiene contenido para mostrar
						$flag_mostrar = SIMWebService::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
						//$flag_mostrar=0;
						if($flag_mostrar==0):
							if(!empty($r_modulo["Titulo"]))
								$modulo["NombreModulo"] = utf8_encode($r_modulo["Titulo"]);
							else
								$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));

							$modulo["Orden"] = $r_modulo["Orden"];
							$icono_modulo = $r_modulo["Icono"] ;
							if (!empty($r_modulo["Icono"])):
								$foto_modulo = MODULO_ROOT.$r_modulo["Icono"];
							else:
								$foto_modulo="";
							endif;
							$icono_contratista = $foto_modulo;
						endif;

					}//ednw while
				}


				$datos_acceso["IDClub"] = $r["IDClub"];
				$datos_acceso["GrupoFamiliar"] = $r["GrupoFamiliar"];
				$datos_acceso["IconoFamiliar"] = $foto_familiar;
				$datos_acceso["NombreFamiliar"] = $r["NombreFamiliar"];
				$datos_acceso["Invitado"] = $r["Invitado"];
				$datos_acceso["IconoIndividual"] = $foto_individual;
				$datos_acceso["NombreIndividual"] = $r["NombreIndividual"];
				$datos_acceso["TipoInvitado"] = $response_tipo_invitado;
				$datos_acceso["IconoContratista"] = $icono_contratista;
				$datos_acceso["NombreContratista"] = $modulo["NombreModulo"];
				$datos_acceso["TipoAutorizacion"] = $response_tipo_autorizacion ;
				$datos_acceso["TipoDocumento"] = $response_tipodoc ;
				$datos_acceso["TextoMenorEdad"] = $r["TextoMenorEdad"] ;

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


	function get_invitado_documento( $IDClub,$Documento )
	{

		$dbo =& SIMDB::get();
		if( !empty( $Documento ) ){

					//BUSQUEDA INVITADOS ACCESOS
						$qryString = str_replace(".","",$Documento);
						$qryString = str_replace(",","",$qryString);
						$qryString = str_replace("-","",$qryString);


						if (ctype_digit($qryString)) {
							// si es solo numeros en un numero de documento
							$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '".(int)$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='".$IDClub."' Order By IDSocioInvitadoEspecial";
							$modo_busqueda = "DOCUMENTO";
						} else {
							//seguramente es una placa
							//Consulto en invitaciones accesos
							$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='".$IDClub."' Order By IDSocioInvitadoEspecial";
							$modo_busqueda = "PLACA";
						}

							$result_invitacion = $dbo->query($sql_invitacion);
							$total_resultados = $dbo->rows($result_invitacion);
							$datos_invitacion = $dbo->fetchArray($result_invitacion);
							$datos_invitacion["TipoInvitacion"] = "InvitadoAcceso";
							$datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitadoEspecial"];
							$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
							$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );

							if($datos_invitacion["Ingreso"]=="N"){
								$accion_acceso = "ingreso";
								$label_accion_acceso = "Ingres&oacute;";
							}
							elseif($datos_invitacion["Salida"]=="N")	{
								$accion_acceso	= "salio";
								$label_accion_acceso	= "Sali&oacute;";
							}
							//Consulto grupo Familiar
							if($datos_invitacion["CabezaInvitacion"]=="S"):
								$sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '".$datos_invitacion["IDSocioInvitadoEspecial"]."'";
								$result_grupo = $dbo->query($sql_grupo);
							endif;
					//FIN BUSQUEDA INVITADOS ACCESOS

					//BUSQUEDA CONTRATISTA
						if($total_resultados<=0):
							if (ctype_digit($qryString)) {
									// si es solo numeros en un numero de documento
									$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SA.IDClub='".$IDClub."'";
									$modo_busqueda = "DOCUMENTO";
							} else {
								//seguramente es una placa
								//Consulto en invitaciones accesos
								$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub='".$IDClub."'";
								$modo_busqueda = "PLACA";
							}

								$result_invitacion = $dbo->query($sql_invitacion);
								$total_resultados = $dbo->rows($result_invitacion);
								$datos_invitacion = $dbo->fetchArray($result_invitacion);

								$datos_invitacion["Ingreso"];
								$datos_invitacion["Salida"];
								$datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioAutorizacion"];
								$datos_invitacion["TipoInvitacion"] = "Contratista";
								$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
								$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );

						endif;
					//FIN BUSQUEDA CONTRATISTA

					//BUSQUEDA INVITADOS GENERAL
					if($total_resultados<=0):
						if (ctype_digit($qryString)) {
							// si es solo numeros en un numero de documento
							$sql_invitacion = "Select SI.* From SocioInvitado SI Where SI.NumeroDocumento = '".(int)$qryString."' and FechaIngreso = '".date("Y-m-d")."' and IDClub = '".$IDClub."'";
							$modo_busqueda = "DOCUMENTO";

								$result_invitacion = $dbo->query($sql_invitacion);
								$total_resultados = $dbo->rows($result_invitacion);
								$datos_invitacion = $dbo->fetchArray($result_invitacion);

								$datos_invitacion["Ingreso"];
								$datos_invitacion["Salida"];
								$datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitado"];
								$datos_invitacion["TipoInvitacion"] = "Invitado";
								$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
								$datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
						}
					endif;
				//FIN BUSQUEDA CONTRATISTA


					if($total_resultados<=0)
					{
						$respuesta["message"] = "No encontrado";
						$respuesta["success"] = false;
						$respuesta["response"] = NULL;
					}//end if
					else{


							$response["IDInvitacion"] = $datos_invitacion["IDInvitacion"];
							$response["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
							$response["FechaInicio"] = $datos_invitacion["FechaInicio"];
							$response["FechaFin"] = $datos_invitacion["FechaFin"];
							$response["Accion"] = $datos_socio["Accion"];
							$response["Socio"] = "Invitado por: " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " Inv ". $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . "  " . $datos_socio["Predio"];
							$response["TipoSocio"] = $datos_socio["TipoSocio"];
							$response["Observaciones"] = $datos_socio["Predio"];
							$response["Ingreso"] = $datos_invitacion["Ingreso"];
							$response["FechaIngreso"] = $datos_invitacion["FechaInicio"];
							$response["Salida"] = $datos_invitacion["Salida"];
							$response["FechaSalida"] = $datos_invitacion["FechaFin"];


							if (!empty($datos_invitado[FotoFile])) {
								$foto = 	SOCIO_ROOT.$datos_invitado["FotoFile"];
							}else{
								$foto = URLROOT."plataform/assets/images/sinfoto.png";
							}

							$response["Foto"] = $foto;
							$response["NombreInvitado"] = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"];
							$response["TipoDocumentoInvitado"] = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'" );
							$response["NumeroDocumentoInvitado"] = $datos_invitado["NumeroDocumento"];


								//SI ES CABEZA CONUSLTO EL GRUPO FAMILIAR
								$response_invitado_familia = array();
								if($datos_invitacion["CabezaInvitacion"]=="S"):
									while($datos_grupo_familiar = $dbo->fetchArray($result_grupo)):
										 	$datos_invitado_familiar = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_grupo_familiar["IDInvitado"] . "' ", "array" );
										 	if (!empty($datos_invitado_familiar[FotoFile])) {
										  	$foto = INVITADO_ROOT.$datos_invitado_familiar[FotoFile];
										   }else{
											 $foto = URLROOT."/images/sinfoto.png";
										   }

										   $dato_invitado_asociado["IDClub"] = $IDClub;
										   $dato_invitado_asociado["IDInvitacion"] = $datos_grupo_familiar["IDSocioInvitadoEspecial"];
										   $dato_invitado_asociado["Nombre"] = utf8_encode($datos_invitado_familiar["Nombre"] . " " . $datos_invitado_familiar["Apellido"]);
										   $dato_invitado_asociado["TipoDocumento"] = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'" );
										   $dato_invitado_asociado["Documento"] = $datos_invitado_familiar["NumeroDocumento"];
										   //Consulto el historial de ingresos y salidas del dia
											$response_historial_acceso_grupo = array();
											$fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
											$fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
											$sql_historial_grupo = $dbo->query("Select * From LogAcceso Where IDInvitacion = '".$datos_grupo_familiar["IDSocioInvitadoEspecial"]."' and FechaTrCr >= '".$fecha_hoy_desde."' and FechaTrCr <= '".$fecha_hoy_hasta."'");
											while($row_historial_grupo = $dbo->fetchArray($sql_historial_grupo)):
												   $dato_historial_grupo["Tipo"] = $row_historial_grupo["Tipo"];
												   $dato_historial_grupo["Salida"] = $row_historial_grupo["Salida"];
												   $dato_historial_grupo["FechaSalida"] = $row_historial_grupo["FechaSalida"];
												   $dato_historial_grupo["Entrada"] = $row_historial_grupo["Entrada"];
												   $dato_historial_grupo["FechaIngreso"] = $row_historial_grupo["FechaIngreso"];
												   array_push($response_historial_acceso_grupo, $dato_historial_grupo);
											 endwhile;
										$dato_invitado_asociado["Historial"] = $response_historial_acceso_grupo;
										//Fin Historial de acceso
										   array_push($response_invitado_familia, $dato_invitado_asociado);
									 endwhile;
								endif;

								$response["GrupoInvitado"] = $response_invitado_familia;


							//Consulto el historial de ingresos y salidas del dia
									$response_historial_acceso = array();
									$fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
									$fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
									$sql_historial = $dbo->query("Select * From LogAcceso Where IDInvitacion = '".$datos_invitacion["IDInvitacion"]."' and FechaTrCr >= '".$fecha_hoy_desde."' and FechaTrCr <= '".$fecha_hoy_hasta."'");
									while($row_historial = $dbo->fetchArray($sql_historial)):
										   $dato_historial["Tipo"] = $row_historial["Tipo"];
										   $dato_historial["Salida"] = $row_historial["Salida"];
										   $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
										   $dato_historial["Entrada"] = $row_historial["Entrada"];
										   $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];
										   array_push($response_historial_acceso, $dato_historial);
									 endwhile;
								$response["Historial"] = $response_historial_acceso;




							$respuesta["message"] = "ok";
							$respuesta["success"] = true;
							$respuesta["response"] = $response;
					}
				}
			else{
				$respuesta["message"] = "1. Atencion faltan parametros";
				$respuesta["success"] = false;
				$respuesta["response"] = NULL;
			}


			return $respuesta;

	}//end function




	function get_invitado_documento_v2( $IDClub,$Documento )
	{

		$dbo =& SIMDB::get();
		if( !empty( $Documento ) ){
					$autorizacion_recogida=0;
					$autorizacion_invitacion=0;

					//BUSQUEDA INVITADOS ACCESOS
						$qryString = str_replace(".","",$Documento);
						$qryString = str_replace(",","",$qryString);
						$qryString = str_replace("-","",$qryString);
						if (ctype_digit($qryString)) {
							// si es solo numeros en un numero de documento
							$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '".(int)$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='".$IDClub."'";
							$modo_busqueda = "DOCUMENTO";
						} else {
							//seguramente es una placa
							//Consulto en invitaciones accesos
							$sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SIE.IDClub='".$IDClub."'";
							$modo_busqueda = "PLACA";
						}



							$result_invitacion = $dbo->query($sql_invitacion);
							$total_resultados = $dbo->rows($result_invitacion);

							if($total_resultados>0)
								$autorizacion_invitacion=1;

							$datos_invitacion = $dbo->fetchArray($result_invitacion);
							$datos_invitacion["TipoInvitacion"] = "InvitadoAcceso";
							$datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitadoEspecial"];
							$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
							$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );
							$tipo_socio = $datos_socio["TipoSocio"];
							$datos_socio["TipoSocio"] = "Invitado por";
							$datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(".$tipo_socio.")") ;

							if($datos_invitacion["Ingreso"]=="N"){
								$accion_acceso = "ingreso";
								$label_accion_acceso = "Ingres&oacute;";
							}
							elseif($datos_invitacion["Salida"]=="N")	{
								$accion_acceso	= "salio";
								$label_accion_acceso	= "Sali&oacute;";
							}
							//Consulto grupo Familiar
							if($datos_invitacion["CabezaInvitacion"]=="S"):
								$sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '".$datos_invitacion["IDSocioInvitadoEspecial"]."'";
								$result_grupo = $dbo->query($sql_grupo);
							endif;
					//FIN BUSQUEDA INVITADOS ACCESOS

					//BUSQUEDA CONTRATISTA
						if($total_resultados<=0):
							if (ctype_digit($qryString)) {
									// si es solo numeros en un numero de documento
									$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '".(int)$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SA.IDClub='".$IDClub."'";
									$modo_busqueda = "DOCUMENTO";
							} else {
								//seguramente es una placa
								//Consulto en invitaciones accesos
								$sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '".$qryString."' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub='".$IDClub."'";
								$modo_busqueda = "PLACA";
							}

								$result_invitacion = $dbo->query($sql_invitacion);
								$total_resultados = $dbo->rows($result_invitacion);
								$datos_invitacion = $dbo->fetchArray($result_invitacion);
								if($total_resultados>0)
									$autorizacion_invitacion=1;

								$datos_invitacion["Ingreso"];
								$datos_invitacion["Salida"];
								$datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioAutorizacion"];
								$datos_invitacion["TipoInvitacion"] = "Contratista";
								$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
								$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );
								$datos_socio["TipoSocio"] = "Invitado por";
								$datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(".$tipo_socio.")") ;

						endif;
					//FIN BUSQUEDA CONTRATISTA

					//BUSQUEDA INVITADOS GENERAL
					if($total_resultados<=0):
						if (ctype_digit($qryString)) {
							// si es solo numeros en un numero de documento
							$sql_invitacion = "Select SI.* From SocioInvitado SI Where SI.NumeroDocumento = '".(int)$qryString."' and FechaIngreso = '".date("Y-m-d")."' and IDClub = '".$IDClub."'";
							$modo_busqueda = "DOCUMENTO";

								$result_invitacion = $dbo->query($sql_invitacion);
								$total_resultados = $dbo->rows($result_invitacion);
								$datos_invitacion = $dbo->fetchArray($result_invitacion);

								if($total_resultados>0)
									$autorizacion_invitacion=1;
								$datos_invitacion["Ingreso"];
								$datos_invitacion["Salida"];
								$datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitado"];
								$datos_invitacion["TipoInvitacion"] = "Invitado";
								$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
								$datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
						}
					endif;
				//FIN BUSQUEDA CONTRATISTA

				//BUSQUEDA SOCIO o Empleado si esta como Socio
				if($total_resultados<=0):
					if (ctype_digit($qryString)) {
							// si es solo numeros en un numero de documento
							$sql_invitacion = "Select * From Socio Where (NumeroDocumento = '".(int)$qryString."' or Accion = '".$qryString."' or NumeroDerecho = '".$qryString."') and IDClub = '".$IDClub."'";
							$modo_busqueda = "DOCUMENTO";
					} else {
						//seguramente es una placa	o una accion
						//Consulto las placas de vehiculos de socios
						$sql_invitacion = "Select * From Socio Where (Accion = '".$qryString."' or NumeroDerecho = '".$qryString."') and IDClub = '".$IDClub."'
										  UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '".$qryString."' and IDClub = '".$IDClub."'
										  UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '".$qryString."' and IDClub = '".$IDClub."'  and AccionPadre = ''";

					}



						$result_invitacion = $dbo->query($sql_invitacion);
						$total_resultados = $dbo->rows($result_invitacion);

						if($total_resultados>0)
							$autorizacion_invitacion=1;

						$datos_invitacion = $dbo->fetchArray($result_invitacion);
						$datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocio"];
						$datos_invitacion["TipoInvitacion"] = "SocioClub";
						$datos_invitacion["PersonaAutoriza"]="b";
						$datos_invitacion["FechaInicio"]='indefinida';
						$datos_invitacion["FechaFin"]='indedefinida';
						$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array" );
						$datos_invitad