 <?

	SIMReg::setFromStructure( array(
						"title" => "Notificaciones Generales",
						"table" => "NotificacionesGenerales",
						"key" => "IDNotificacionesGenerales",
						"mod" => "NotificacionesGenerales"
	) );


	$script = "notificaciones";

	//extraemos las variables
	$table = SIMReg::get( "table" );
	$key = SIMReg::get( "key" );
	$mod = SIMReg::get( "mod" );

	//Verificar permisos
	SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

	//creando las notificaciones que llegan en el parametro m de la URL
	SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );

//print_r( $_POST  );
//exit;


//Consulto los socios que han solicitado bloqueo de notificaciones
$array_bloqueo = array();
$sql_bloqueos = $dbo->query("Select * From BloqueoNotificacion Where IDClub = '".SIMUser::get("club")."'");
while($row_bloqueo=$dbo->fetchArray($sql_bloqueos)):
	$array_bloqueo[]=$row_bloqueo["IDSocio"];
endwhile;
if(count($array_bloqueo)>0):
	$condicion_socio_bloqueado = " and IDSocio not in ( ".implode(",",$array_bloqueo)." )";
endif;


	switch ( SIMNet::req( "action" ) ) {

		case "add" :
			$view = "views/".$script."/form.php";
			$newmode = "insert";
			$titulo_accion = "Crear";
		break;

		case "insert" :

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );

					//insertamos los datos
					$id = $dbo->insert( $frm , $table , $key );

					if($frm["FechaInicio"] == '' && $frm["FechaFin"] == '' && $frm["HoraEnvio"] == ''){

						$frm["IDModulo"] =44;
						$frm["IDSeccion"]="0";
						$frm["TipoNotificacion"] ='General'; //socio
						if(!empty($frm["Link"])){
							$EnviaLink="S";
							$UrlLink=$frm["Link"];
						}
						else{
							$EnviaLink="N";
							$UrlLink="";
						}

						switch($frm["DirigidoAGeneral"]):
							case "SE": // Socio Especifico
							case "GS": // Grupo de socios
							case "S": //Todos los socios

								if($frm["DirigidoAGeneral"]=="S"):
									$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'" . $condicion_socio_bloqueado;
								elseif($frm["DirigidoAGeneral"]=="SE"):
									$invitados = explode("|||",$frm["InvitadoSeleccion"]);
									if(count($invitados)>0):
										foreach($invitados as $nom_invitado):
											$array_datos = explode("-",$nom_invitado);
											if($array_datos[0]=="socio"): // socio club
												$datos_invitado[] = trim($array_datos[1]);
											endif;
										endforeach;
									endif;

									if(count($datos_invitado)>0)	:
										$condicion_socio = " and IDSocio in (".implode(",",$datos_invitado).") ";
										$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'" . $condicion_socio . $condicion_socio_bloqueado;
									endif;

								elseif($frm["DirigidoAGeneral"]=="GS"):
									$sql_grupo="Select * From GrupoSocio Where IDGrupoSocio = '".$frm["IDGrupoSocio"]."' Limit 1";
									$result_grupo = $dbo->query($sql_grupo);
									$row_grupo = $dbo->fetchArray($result_grupo);
									$array_socio = explode("|||",$row_grupo["IDSocio"]);
									if(count($array_socio)>0):
										$condicion_socio = " and IDSocio in (".implode(",",$array_socio).") ";
									else:
										$condicion_socio = " and IDSocio in (0) ";
									endif;
										$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'" . $condicion_socio . $condicion_socio_bloqueado;
								endif;

								//traer todos los socios del club que tengan token
								//$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '8' and IDSocio = 5533 AND Token <> '' and Token <> '2byte'" . $condicion_socio_bloqueado;

								$qry_socios = $dbo->query( $sql_socios );
								$notificaciones = $dbo->rows( $qry_socios );

								$frm["TipoUsuario"] ='S'; //socio
								$frm["Titular"] = $frm["TituloMensaje"];

								while( $r_socios = $dbo->fetchArray( $qry_socios ) )
								{

									$message = $frm["Mensaje"];
									$custom["tipo"] = "General";
									$custom["idseccion"] = (string)"0";
									$custom["iddetalle"] = (string)"0";
									$custom["titulo"] = $frm["Titular"];
									$custom["idmodulo"] = 44;

									if($dbo->rows( $qry_socios )<=5){

										$users = array( array( "id" => $r_socios["IDSocio"],
											"idclub"=>$r_socios["IDClub"],
											"registration_key"=>$r_socios["Token"] ,
											"deviceType"=>$r_socios["Dispositivo"],
												'link'   => $EnviaLink,
												'urllink'   => $UrlLink
											)

																);

											$array_ios=array();
											$array_android=array();
											if($r_socios["Dispositivo"]=="iOS")
														$array_ios[]=$r_socios["Token"];
													elseif($r_socios["Dispositivo"]=="Android")
														$array_android[]=$r_socios["Token"];

													SIMUtil::sendAlerts_V2($users, $message, $custom,$TipoApp,$array_android,$array_ios,$r_socios["IDClub"]);


																///enviar notificación
																//SIMUtil::sendAlerts($users, utf8_decode($message), $custom);
										}
										else {
											SIMUtil::envia_cola_notificacion($r_socios,$frm);
										}
															//Guardo el log
															$sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle, Link) Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Socio','".$custom["tipo"]."', '".$custom["titulo"]."', '".$message."', '".$custom["idseccion"]."', '".$custom["iddetalle"]."','".$UrlLink."')");
														}//end while


								break;

								case "EE": //Empleado Especifico
								case "GE": //Empleado Grupo
								case "E": // Todos los empleados


                $frm["TipoUsuario"] ='E'; //socio

										if($frm["DirigidoAGeneral"]=="E"):
											$sql_empleado = "SELECT * FROM  Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'";
										elseif($frm["DirigidoAGeneral"]=="EE"):
											$invitados = explode("|||",$frm["InvitadoSeleccion"]);
											if(count($invitados)>0):
												foreach($invitados as $nom_invitado):
													$array_datos = explode("-",$nom_invitado);
													if($array_datos[0]=="usuario"): // socio club
														$datos_invitado[] = trim($array_datos[1]);
													endif;
												endforeach;
											endif;

											if(count($datos_invitado)>0)	:
												$condicion_usuario = " and IDUsuario in (".implode(",",$datos_invitado).") ";
												$sql_empleado = "SELECT * FROM  Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte' " . $condicion_usuario;
											endif;

										elseif($frm["DirigidoAGeneral"]=="GE"):
											$sql_grupo="Select * From GrupoUsuario Where IDGrupoUsuario = '".$frm["IDGrupoUsuario"]."' Limit 1";
											$result_grupo = $dbo->query($sql_grupo);
											$row_grupo = $dbo->fetchArray($result_grupo);
											$array_usuario = explode("|||",$row_grupo["IDUsuario"]);
											if(count($array_usuario)>0):
												$condicion_usuario = " and IDUsuario in (".implode(",",$array_usuario).") ";
											else:
												$condicion_usuario = " and IDUsuario in (0) ";
											endif;
												$sql_empleado = "SELECT * FROM  Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte' " . $condicion_usuario;
										endif;

								//traer todos los empleados del club que tengan token
								//$sql_empleado = "SELECT * FROM  Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'";

								$qry_empleado = $dbo->query( $sql_empleado );
								$notificaciones = $dbo->rows( $qry_empleado );
								while( $r_empleados = $dbo->fetchArray( $qry_empleado ) )
								{

                  SIMUtil::envia_cola_notificacion($r_empleados,$frm);
                  /*
									$users = array( array( "id" => $r_empleados["IDUsuario"],
										"idclub"=>$r_empleados["IDClub"],
										"registration_key"=>$r_empleados["Token"] ,
										"deviceType"=>$r_empleados["Dispositivo"] )

									);

									$message = $frm["Mensaje"];
									$custom["tipo"] = "General";
									$custom["idseccion"] = (string)"0";
									$custom["iddetalle"] = (string)"0";
									$custom["titulo"] = "Notificacion Club";

									///enviar notificación
									SIMUtil::sendAlerts($users, $message, $custom,"Empleado");
                  */
									//Guardo el log
									$sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, Link) Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Empleado','".$custom["tipo"]."', '".$custom["titulo"]."', '".$message."', '".$custom["idmodulo"]."','".$UrlLink."')");
								}//end while

						break;

					//echo "ejecutado";
					endswitch;
						SIMHTML::jsAlert("Notificación enviada a " . $notificaciones . " socios");
						SIMHTML::jsRedirect( $script.".php" );

			}else{

					SIMHTML::jsAlert("Notificación programada para fecha: ".$frm["FechaEnvio"]." hora: ".$frm["HoraEnvio"]);
					SIMHTML::jsRedirect( $script.".php" );

			}





					}



		break;


		case "edit":
		$frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
		$view = "views/".$script."/form.php";
		$newmode = "update";
		$titulo_accion = "Actualizar";

	break ;

		case "update" :

		if( !SIMNotify::capture( SIMUtil::valida( $_POST , $array_valida ) , "error" ) )
				{
					//los campos al final de las tablas
					$frm = SIMUtil::varsLOG( $_POST );

					foreach($frm["IDDia"] as $Dia_seleccion):
						$array_dia []= $Dia_seleccion;
					endforeach;

					if(count($array_dia)>0):
						$id_dia=implode("|",$array_dia) . "|";
					endif;
					$frm["Dias"]=$id_dia;

					$id = $dbo->update( $frm , $table , $key , SIMNet::reqInt("id") );

					if($frm["FechaEnvio"]  == '' || $frm["HoraEnvio"] ==''){

					 $frm["IDModulo"] =44;
            $frm["IDSeccion"]="0";
            $frm["TipoNotificacion"] ='General'; //socio
            if(!empty($frm["Link"])){
              $EnviaLink="S";
              $UrlLink=$frm["Link"];
            }
            else{
              $EnviaLink="N";
              $UrlLink="";
            }


						switch($frm["DirigidoAGeneral"]):
							case "SE": // Socio Especifico
							case "GS": // Grupo de socios
							case "S": //Todos los socios

                $frm["TipoUsuario"] ='S'; //socio

									if($frm["DirigidoAGeneral"]=="S"):
										$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'" . $condicion_socio_bloqueado;
									elseif($frm["DirigidoAGeneral"]=="SE"):
										$invitados = explode("|||",$frm["InvitadoSeleccion"]);
										if(count($invitados)>0):
											foreach($invitados as $nom_invitado):
												$array_datos = explode("-",$nom_invitado);
												if($array_datos[0]=="socio"): // socio club
													$datos_invitado[] = trim($array_datos[1]);
												endif;
											endforeach;
										endif;

										if(count($datos_invitado)>0)	:
											$condicion_socio = " and IDSocio in (".implode(",",$datos_invitado).") ";
											$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'" . $condicion_socio . $condicion_socio_bloqueado;
										endif;

									elseif($frm["DirigidoAGeneral"]=="GS"):
										$sql_grupo="Select * From GrupoSocio Where IDGrupoSocio = '".$frm["IDGrupoSocio"]."' Limit 1";
										$result_grupo = $dbo->query($sql_grupo);
										$row_grupo = $dbo->fetchArray($result_grupo);
										$array_socio = explode("|||",$row_grupo["IDSocio"]);
										if(count($array_socio)>0):
											$condicion_socio = " and IDSocio in (".implode(",",$array_socio).") ";
										else:
											$condicion_socio = " and IDSocio in (0) ";
										endif;
											$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'" . $condicion_socio . $condicion_socio_bloqueado;
									endif;

											//traer todos los socios del club que tengan token
											 //$sql_socios = "SELECT Socio.* FROM  Socio WHERE Socio.IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte' and IDSocio = '5533'";

											$qry_socios = $dbo->query( $sql_socios );

											$notificaciones = $dbo->rows( $qry_socios );
											$datos_club = $dbo->fetchAll( "Club", " IDClub = '" . SIMUser::get("club") . "' ", "array" );

                        //$frm["Titular"] ="Notificaciones " . $datos_club["Nombre"];
                        $frm["Titular"] = $frm["TituloMensaje"];

											while( $r_socios = $dbo->fetchArray( $qry_socios ) )
											{

                        $message = $frm["Mensaje"];
                        $custom = array( "titulo" => $frm["Titular"],
                          'idseccion'    => 0,
                          'tipo'         => 'General',
                          'idmodulo' => 44,
                          'iddetalle'   => 0,
                          'link'   => $EnviaLink,
                          'urllink'   => $UrlLink
                        );



                        if($dbo->rows( $qry_socios )<=5){


												$users = array( array( "id" => $r_socios["IDSocio"],
													"idclub"=>$r_socios["IDClub"],
													"registration_key"=>$r_socios["Token"] ,
													"deviceType"=>$r_socios["Dispositivo"] )

												);

                        $array_ios=array();
                        $array_android=array();
                        if($r_socios["Dispositivo"]=="iOS")
                					$array_ios[]=$r_socios["Token"];
                				elseif($r_socios["Dispositivo"]=="Android")
                					$array_android[]=$r_socios["Token"];

                				SIMUtil::sendAlerts_V2($users, $message, $custom,$TipoApp,$array_android,$array_ios,$r_socios["IDClub"]);

												///enviar notificación
												//SIMUtil::sendAlerts($users, utf8_decode($message), $custom);
                      }
                      else{
                        SIMUtil::envia_cola_notificacion($r_socios,$frm);
                      }
												//Guardo el log
												$sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDDetalle, Link) Values ('".$id ."', '".$r_socios["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'Socio','".$custom["tipo"]."', '".$custom["titulo"]."', '".$message."', '".$custom["idseccion"]."', '".$custom["iddetalle"]."','".$UrlLink."')");

									}//end while

							break;



							case "EE": //Empleado Especifico
							case "GE": //Empleado Grupo
							case "E": // Todos los empleados

              $frm["TipoUsuario"] ='E'; //empleados


									if($frm["DirigidoAGeneral"]=="E"):
										$sql_empleado = "SELECT * FROM  Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte'";
									elseif($frm["DirigidoAGeneral"]=="EE"):
										$invitados = explode("|||",$frm["InvitadoSeleccion"]);
										if(count($invitados)>0):
											foreach($invitados as $nom_invitado):
												$array_datos = explode("-",$nom_invitado);
												if($array_datos[0]=="usuario"): // socio club
													$datos_invitado[] = trim($array_datos[1]);
												endif;
											endforeach;
										endif;

										if(count($datos_invitado)>0)	:
											$condicion_usuario = " and IDUsuario in (".implode(",",$datos_invitado).") ";
											$sql_empleado = "SELECT * FROM  Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte' " . $condicion_usuario;
										endif;

									elseif($frm["DirigidoAGeneral"]=="GE"):
										$sql_grupo="Select * From GrupoUsuario Where IDGrupoUsuario = '".$frm["IDGrupoUsuario"]."' Limit 1";
										$result_grupo = $dbo->query($sql_grupo);
										$row_grupo = $dbo->fetchArray($result_grupo);
										$array_usuario = explode("|||",$row_grupo["IDUsuario"]);
										if(count($array_usuario)>0):
											$condicion_usuario = " and IDUsuario in (".implode(",",$array_usuario).") ";
										else:
											$condicion_usuario = " and IDUsuario in (0) ";
										endif;
											$sql_empleado = "SELECT * FROM  Usuario WHERE IDClub = '" . SIMUser::get("club") . "' AND Token <> '' and Token <> '2byte' " . $condicion_usuario;
									endif;





									//traer todos los empleados del club que tengan token


									$qry_empleado = $dbo->query( $sql_empleado );
									$notificaciones = $dbo->rows( $qry_empleado );
                  $frm["Titular"] ="Notificacion Club";
									while( $r_empleados = $dbo->fetchArray( $qry_empleado ) )
									{

                    SIMUtil::envia_cola_notificacion($r_empleados,$frm);
                    /*
										$users = array( array( "id" => $r_empleados["IDSocio"],
											"idclub"=>$r_empleados["IDClub"],
											"registration_key"=>$r_empleados["Token"] ,
											"deviceType"=>$r_empleados["Dispositivo"] )

										);

										$message = $frm["Mensaje"];
										$custom["tipo"] = "app";
										$custom["idmodulo"] = (string)"11";
										$custom["titulo"] = "Notificacion Club";

										///enviar notificación
										SIMUtil::sendAlerts($users, $message, $custom,"Empleado");
                    */
										//Guardo el log
										$sql_log = $dbo->query("Insert Into LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, Link) Values ('".$id ."', '".$r_empleados["IDSocio"]."','".$r_socios["IDClub"]."','".$r_socios["Token"]."','".$r_socios["Dispositivo"]."',NOW(),'".$UrlLink."')");
									}//end while
							break;

						endswitch;




					$frm = $dbo->fetchById( $table , $key , $id , "array" );



					SIMNotify::capture( "Los cambios han sido guardados satisfactoriamente" , "info" );

					SIMHTML::jsAlert("Notificación enviada a " . $notificaciones . " socios");
						SIMHTML::jsRedirect( $script.".php" );

			}else{

					SIMHTML::jsAlert("Notificación programada para fecha: ".$frm["FechaEnvio"]." hora: ".$frm["HoraEnvio"]);
					SIMHTML::jsRedirect( $script.".php" );

			}


				}
				else
					exit;
		break;

		case "search" :
			$view = "views/".$script."/list.php";
		break;

		default:
			$view = "views/".$script."/list.php";





	} // End switch



	if( empty( $view ) )
		$view = "views/".$script."/form.php";


?>
