<?php

	$ids = SIMNet::req("ids");
	//Si es coordinador tenemos que mirar que servicio se trae primero
	//si es un usuario normal se traen las horas

	$fecha = SIMNet::req("fecha");
	if(empty( $fecha ))
		$fecha = date("Y-m-d");

	$action = SIMNet::req("action");

	$datos_servicio_config = $dbo->fetchAll("Servicio","IDServicio='".$ids."'","array" );
	$pantalla_carga_elemento=$datos_servicio_config["PantallaReservaElemento"];
	$permite_repetir=$datos_servicio_config["PermiteAdminRepetirReserva"];


	function copiar_archivo(&$frm,$file) {
		$filedir=SOCIOPLANO_DIR;
		$nuevo_nombre = rand(0,1000000). "_".date("Y-m-d")."_".$file['file']['name'];
		if (copy($file['file']['tmp_name'], "$filedir/".$nuevo_nombre) ) {
			echo "File : ".$file['file']['name']."... ";
			echo "Size :".$file['file']['size']." Bytes ... ";
			echo "Status : Transfer Ok ...<br>";
			return $nuevo_nombre;

		}
		else{
			echo "error";
		}
}

function get_data($nombrearchivo,$file,$IGNORE_FIRTS_ROW,$FIELD_TEMINATED='',$field='',$IDClub, $IDServicio,$FechaInicio,$FechaFin,$Dias)
{

	$dbo =& SIMDB::get();

	$sql_socios="SELECT IDSocio,Accion,Nombre, Apellido FROM Socio WHERE IDClub = '".$IDClub."'";
  	$r_socios=$dbo->query($sql_socios);
  	while($row_socios=$dbo->fetchArray($r_socios))
  	{
    	$array_socios[$row_socios["Accion"]]=$row_socios["IDSocio"];
		$array_datos_socios[$row_socios["IDSocio"]]=$row_socios["Nombre"] . " " . $row_socios["Apellido"];
  	}

	$numregok = 0;

  require_once LIBDIR."excel/PHPExcel-1.8/Classes/PHPExcel.php";

  $archivo = $file;
  $inputFileType = PHPExcel_IOFactory::identify($archivo);
  $objReader = PHPExcel_IOFactory::createReader($inputFileType);
  $objPHPExcel = $objReader->load($archivo);
  $sheet = $objPHPExcel->getSheet(0);
  $highestRow = $sheet->getHighestRow();
  $highestColumn = $sheet->getHighestColumn();

	$nombreArchivo = $nombrearchivo;


	//verifico que todos los socios existan
	$cargar_base="S";
  for ($row = 2; $row <= $highestRow; $row++)
  {
			$AccionSocio =  trim(utf8_decode($sheet->getCell("C".$row)->getFormattedValue()));
			$Invitados = $sheet->getCell("D".$row)->getValue();
				if(!empty($AccionSocio))
				{
					if(!empty($Invitados))
					{
						$array_invitados=explode("|",$Invitados);
						if(count($array_invitados)>0)
						{
							foreach($array_invitados as $accion_invitado)
							{
								if(!empty($accion_invitado))
								{
									$IDSocioInvitado=$array_socios[$accion_invitado];
									if($IDSocioInvitado<=0)
									{
										echo "<br>El socio con accion " . $accion_invitado . " no se ha encontrado en la base, por favor verifique";
										$cargar_base="N";
									}
								}
							}
						}
					}

	       			$IDSocio=$array_socios[$AccionSocio];
					if($IDSocio<=0)
					{
						echo "<br>El socio con accion: " . $AccionSocio . " no se ha encontrado en la base, por favor verifique";
						$cargar_base="N";
					}
				}
	}

	if($cargar_base=="N")
	{
		echo "<br><b>No es posible cargar la base, por favor verifique</b>";
	}


	$inicio=strtotime($FechaInicio);
	$fin=strtotime($FechaFin);
	for ( $i = $inicio; $i <= $fin; $i += 86400 )
	{
			$FechaReserva=date("Y-m-d",$i);
			$dia_actual=date("w",$i);
			if(in_array($dia_actual,$Dias)){
				//Cargar datos
				for ($row = 2; $row <= $highestRow; $row++){

					$Tee =  trim($sheet->getCell("A".$row)->getValue());
					$Hora =  trim(utf8_decode($sheet->getCell("B".$row)->getFormattedValue()));
					$AccionSocio =  trim(utf8_decode($sheet->getCell("C".$row)->getFormattedValue()));
					$Invitados = $sheet->getCell("D".$row)->getValue();
					$Observacion =  $sheet->getCell("E".$row)->getValue();





					$IDElemento=$IDSocio = $dbo->getFields( "ServicioElemento", "IDServicioElemento", "IDServicio = '" . $IDServicio . "' " );

						if(!empty($AccionSocio) && !empty($Tee) && !empty($Hora) && !empty($IDServicio) )
						{
							$IDSocio=$array_socios[$AccionSocio];
							//Vuelvo a validar que nadie haya tomado la reserva nuevamente por los casos de milisegundos
		          			$id_reserva_disponible = $dbo->getFields( "ReservaGeneral", "IDReservaGeneral", "IDClub = '" . $IDClub . "' and IDServicio = '" . $IDServicio . "' and IDServicioElemento = '" . $IDElemento . "' and IDEstadoReserva in (1) and Fecha = '" . $FechaReserva . "' and Hora = '" . $Hora . "' and Tee = '".$Tee."'" );
							if($id_reserva_disponible<=0)
							{
								$sql="INSERT INTO ReservaGeneral (IDClub, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva,
																Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion, Altitud, Longitud, NombreSocio,AccionSocio,NombreBeneficiario,AccionBeneficiario, IP, IdentificadorServicio,ConsecutivoServicio,UsuarioTrCr, FechaTrCr)
													Values ('" . $IDClub . "','" . $IDSocio . "','" . $IDSocio . "', '" . $IDUsuarioReserva . "', '" . $IDServicio . "','" . $IDElemento . "', '1','" . $id_disponibilidad . "','" . $IDReservaGrupos . "','" . $IDInvitadoBeneficiario . "','" . $IDSocioBeneficiario . "',
															'" . $IDTipoReserva . "','" . $FechaReserva . "', '" . $Hora . "','" . $Observaciones . "','" . $Tee . "','" . $IDAuxiliar . "','" . $IDTipoModalidadEsqui . "','" . $CantidadInvitadoSalon . "','" . $numero_inscripcion . "','".$Altitud."' , '".$Longitud."','".$array_datos_socios[$IDSocio]."',
															'".$AccionSocio."','".$NombreBenefReserva."','".$AccionBenefReserva."','".$IP."','".$nombreArchivo."','".$ConsecutivoServicio."','" . SIMUser::get( "IDUsuario" ) . "',NOW())";
									//echo $sql;
									//exit;
								$numregok++;
								$array_invitados=array();
								$sql_inserta_reserva = $dbo->query( $sql );
 								$id_reserva_general = $dbo->lastID();
								if((int)$id_reserva_general>0){
										//Insertar invitados
										if(!empty($Invitados)){
											$array_invitados=explode("|",$Invitados);
											if(count($array_invitados)>0){
												foreach($array_invitados as $accion_invitado){
													if(!empty($accion_invitado)){
														$IDSocioInvitado=$array_socios[$accion_invitado];
														if($IDSocioInvitado>0)
														{
															$sql_invitados="INSERT Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre)
																							Values ('" . $id_reserva_general . "','" . $IDSocioInvitado . "', '" . $array_datos_socios[$IDSocioInvitado] . "')";
															$sql_inserta_invitado = $dbo->query( $sql_invitados );

														}
													}
												}
												$array_invitados = json_encode($datos_invitado);
											}
											else{
												$array_invitados = "";
											}
										}
								}
								else{
									echo "<br>" . $respuesta[ "message" ] = "No se pudo realizar la reserva, intente de nuevo (C1)";
								}

							}
							else{
								echo "<br>Ya existe una reserva en esta fecha y hora: " . $FechaReserva . " " . $Hora . " Tee: " .$Tee;
							}
							//FIN INSERTAR RESERVA
							$cont++;
						}
				} // END While
			}
	}
		fclose($fp);
			return array("Numregs"=>$cont,"RegsOK"=>$numregok);

	return false;
}


	switch ( $action ) {


		case 'insert':
			$frm = SIMUtil::varsLOG( $_POST );

			//$invitados = explode("\r",$frm["SocioInvitado"]);
			$invitados = explode("|||",$frm["InvitadoSeleccion"]);
			if(count($invitados)>0):
				foreach($invitados as $nom_invitado):
					$array_datos = explode("-",$nom_invitado);
					if($array_datos[0]=="socio"): // socio club
						$datos_invitado[]["IDSocio"] = trim($array_datos[1]);
					elseif($array_datos[0]=="externo"): // invitado externo
						$datos_invitado[]["Nombre"] = trim($array_datos[1]);
					endif;
				endforeach;
				$array_invitados = json_encode($datos_invitado);
			else:
				$array_invitados = "";
			endif;

			$datos_tipo_reserva = $dbo->fetchAll( "ServicioTipoReserva", " IDServicioTipoReserva = '" . $frm["IDTipoReserva"] . "' ", "array" );
			$NumeroTurnos = $datos_tipo_reserva["NumeroTurnos"];


			//Multiple auxiliares
			if(count($_POST["Auxiliar"]>0)){
				foreach($_POST["Auxiliar"] as $id_auxiliar){
						$datos_auxiliar[]["IDAuxiliar"] = $id_auxiliar;
				};
				$ListaAuxiliar = json_encode($datos_auxiliar);
			}


			if(!empty($frm["FechaFinRepetir"]) && !empty($frm["FrecuenciaRepetir"])){
				$Repetir="S";
				$Periodo=$frm["FrecuenciaRepetir"];
				$RepetirFechaFinal=$frm["FechaFinRepetir"];
			}


			$r_campos =& $dbo->all( "ServicioCampo" , "IDServicio = '" . $ids  ."' and Publicar = 'S'");
			$response_dinamicos=array();
			while( $r = $dbo->object( $r_campos ) ){
					$array_dinamicos["IDCampo"]=$r->IDServicioCampo;
					$array_dinamicos["Valor"]=$_POST["Campo".$r->IDServicioCampo];
					array_push( $response_dinamicos, $array_dinamicos );
			}
				if(count($array_dinamicos)>0){
					$Campos=json_encode($response_dinamicos);
				}
				else{
						$Campos="";
				}

			//$respuesta = SIMWebService::set_reserva_generalV2(SIMUser::get("club"),$frm["IDSocio"],$frm["idelemento"],$frm["ids"],$frm["fecha"],$frm["hora"],"",$array_invitados,$frm["Observaciones"],"Admin",$frm["tee"],"","","","",$frm["IDTipoModalidadEsqui"],$frm["IDAuxiliar"],$frm["IDServicioTipoReserva"],$NumeroTurnos,"","");
			$respuesta = SIMWebService::set_reserva_generalV2(SIMUser::get("club"),$frm["IDSocio"],$frm["idelemento"],$frm["ids"],$frm["fecha"],$frm["hora"],$Campos,$array_invitados,$frm["Observaciones"],"Admin",$frm["tee"],"",$Repetir,$Periodo,$RepetirFechaFinal,$frm["IDTipoModalidadEsqui"],$frm["IDAuxiliar"],$frm["IDTipoReserva"],"","","","",SIMUser::get( "IDUsuario" ),$frm["CantidadInvitadoSalon"],$ListaAuxiliar);
			//set_reserva_generalV2($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,$Observaciones="",$Admin = "", $Tee="",$IDDisponibilidad="", $Repetir="",$Periodo="",$RepetirFechaFinal="",$IDTipoModalidadEsqui="",$IDAuxiliar="",$IDTipoReserva="",$NumeroTurnos="",$IDReservaGrupos,$IDBeneficiario="",$TipoBeneficiario="")




			if( $respuesta["success"] == "1" )
			{
				//bien
				//SIMNotify::capture( "La reserva se ha creado correctamente" , "info alert-success" );
				SIMHtml::jsAlert("La reserva se ha creado correctamente");
				SIMHtml::jsRedirect("reservas_admin.php?ids=".$frm["ids"]."&action=new&fecha=".$fecha);
				$resultadook=1;
			}//end if
			else
			{
				//paila
				//SIMNotify::capture( $respuesta["message"]  , "error alert-danger" );
				SIMHtml::jsAlert("ATENCION LA RESERVA NO PUDO SER TOMADA: " . $respuesta["message"]);
				SIMHtml::jsRedirect("reservas.php?action=new&ids=".$frm["ids"]);
				$resultadook=1;
			}//end else


		exit;
		break;

		case 'updateinvitado':

			$frm = SIMUtil::varsLOG( $_POST );
			//$invitados = explode("\r",$frm["SocioInvitado"]);
			//$invitados=$frm["SocioInvitado"];
			//print_r($frm);
			//exit;
			 if($frm["IDSocioOrig"]!=$frm["IDSocio"] && $frm["IDSocio"]>0):
				//Actualizo el socio dueño de la reserva
				$datos_newSocio = $dbo->fetchAll( "Socio", " IDSocio = '" . $frm["IDSocio"] . "' ", "array" );
				$ObservacionCambio = "El Usuario " . SIMUser::get("Nombre") . " cambio el dueno de la reserva que era originalmente " . $frm["IDSocioOrig"];
				$update_reserva="Update ReservaGeneral Set IDSocio = '".$frm["IDSocio"]."', Observaciones= '".$ObservacionCambio."',UsuarioTrEd='".SIMUser::get("Nombre")." ". $ObservacionCambio ."', FechaTrEd = NOW(),NombreSocio='".$datos_newSocio["Nombre"] . " " .$datos_newSocio["Apellido"]."',AccionSocio='".$datos_newSocio["Accion"]."' Where IDReservaGeneral = '".$frm["IDReservaGeneral"]."'";
				$dbo->query($update_reserva);
			endif;

			if($frm["IDSocioBeneficiarioOrig"]!=$frm["IDSocioBeneficiario"] && $frm["IDSocio"]>0):
				//Actualizo el socio dueño de la reserva
				$ObservacionCambio = "El Usuario " . SIMUser::get("Nombre") . " cambio el dueno de la reserva que era originalmente " . $frm["IDSocioOrig"];
				$update_reserva="Update ReservaGeneral Set IDSocioBeneficiario = '".$frm["IDSocioBeneficiario"]."', Observaciones= '".$ObservacionCambio."',UsuarioTrEd='".SIMUser::get("Nombre")." ". $ObservacionCambio ."', FechaTrEd = NOW() Where IDReservaGeneral = '".$frm["IDReservaGeneral"]."'";
				$dbo->query($update_reserva);
			endif;

			//Actualizo cancha y equipo si aplica
				$update_reserva="Update ReservaGeneral Set Cancha = '".$frm["Cancha"]."', Equipo= '".$frm["Equipo"]."',UsuarioTrEd='".SIMUser::get("Nombre")." ". "', FechaTrEd = NOW() Where IDReservaGeneral = '".$frm["IDReservaGeneral"]."'";
				$dbo->query($update_reserva);

			$invitados = explode("|||",$frm["InvitadoSeleccion"]);
			if(count($invitados)>0):
				// Borro invitados
				$sql_invidado_reserva_del = "Delete From ReservaGeneralInvitado Where IDReservaGeneral = '".$frm[IDReservaGeneral]."'";
				$dbo->query( $sql_invidado_reserva_del);
				foreach($invitados as $nom_invitado):
					$array_datos = explode("-",$nom_invitado);
					if($array_datos[0]=="socio"): // socio club
						$inserta_socio =  "Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre)
															Values ('".$frm[IDReservaGeneral]."','".$array_datos[1]."', '')";
						$dbo->query($inserta_socio);

					elseif($array_datos[0]=="externo"): // invitado externo
							$inserta_externo = "Insert Into ReservaGeneralInvitado (IDReservaGeneral, IDSocio, Nombre)
								  Values ('".$frm[IDReservaGeneral]."','', '".$array_datos[1]."')";
							$dbo->query($inserta_externo);
					endif;
				endforeach;
				$respuesta["success"] = "1";
			endif;
			if( $respuesta["success"] == "1" )
			{
				//bien
				SIMNotify::capture( "Invitados modificados correctamente" , "info alert-success" );
			}//end if
			else
			{
				//paila
				SIMNotify::capture( "Se producjo un error al guardar"  , "error alert-danger" );
			}//end else
		break;


		case 'updatereservatomada':
			$frm = SIMUtil::varsLOG( $_POST );

			 $sql_invitados_reserva ="Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$frm["IDReservaGeneral"]."'";
			 $qry_invitados_reserva = $dbo->query($sql_invitados_reserva);
			 $total_invitados = $dbo->rows($qry_invitados_reserva);
			 $invitado_asiste = 0;
			 if($total_invitados>0):
			 	while($row_invitados_reserva = $dbo->fetchArray($qry_invitados_reserva)):
					$nombre_campo = "InvitadoCumplio".$row_invitados_reserva["IDReservaGeneralInvitado"];
					$valor_campo = $frm[$nombre_campo];
					if($valor_campo=="S")
						$invitado_asiste++;
					else
						$invitado_no_asiste++;

					$sql_actualiza_invitado = "Update ReservaGeneralInvitado Set Cumplida = '".$valor_campo."' Where IDReservaGeneralInvitado = '".$row_invitados_reserva["IDReservaGeneralInvitado"]."' and IDReservaGeneral = '".$frm["IDReservaGeneral"]."'";
					$dbo->query($sql_actualiza_invitado);
				endwhile;
			 endif;

			 //if($total_invitados>0 && $invitado_asiste!=$total_invitados || ($frm["CumplidaCabeza"] =="N" && $total_invitados>0 && ))
			 if($total_invitados>0 && $invitado_asiste!=$total_invitados && $invitado_no_asiste!=$total_invitados)
			 	$estado_reserva_cumplida = "P";
			else
				$estado_reserva_cumplida = $frm["Cumplida"];

			//Actualizo Estado de reserva
			$sql_reserva_estado = "Update ReservaGeneral Set Cumplida = '".$estado_reserva_cumplida."', CumplidaCabeza = '".$frm["CumplidaCabeza"]."', FechaCumplida = NOW(), IDUsuarioCumplida = '".$frm["IDUsuario"]."', ObservacionCumplida = '".$frm["ObservacionCumplida"]."' Where IDReservaGeneral = '".$frm["IDReservaGeneral"]."'";
			$dbo->query($sql_reserva_estado);

			if($estado_reserva_cumplida=="N" || $estado_reserva_cumplida=="P"){
				$datos_reserva = $dbo->fetchAll( "ReservaGeneral", " IDReservaGeneral = '" . $frm["IDReservaGeneral"] . "' ", "array" );
				$datos_servicio = $dbo->fetchAll( "Servicio", " IDServicio = '" . $datos_reserva["IDServicio"] . "' ", "array" );
				if($datos_servicio["NotificarSocioReservaIncumplida"]=="S"){
					SIMUtil::notifica_reserva_incumplida($frm["IDReservaGeneral"]);
				}

			}
			SIMNotify::capture( "Reserva actualizada correctamente" , "info alert-success" );
		break;

			case 'updateconfirmarreserva':
			$frm = SIMUtil::varsLOG( $_POST );
			//Actualizo Estado de reserva
			$sql_reserva_estado = "Update ReservaGeneral Set Confirmada = '".$frm["Confirmada"]."', FechaConfirmada = NOW(), IDUsuarioConfirmada = '".$frm["IDUsuario"]."', ObservacionConfirmada = '".$frm["ObservacionConfirmada"]."' Where IDReservaGeneral = '".$frm["IDReservaGeneral"]."'";
			$dbo->query($sql_reserva_estado);

			if($frm["Confirmada"]=="S"){
				$mensaje_confirmacion=" fue confirmada ";
			}
			else{
				$mensaje_confirmacion=" No fue aprobada ";
			}

			$MensajeConfirma="Le informamos que su reserva para el dia ".$frm["FechaReservaConfirma"]." Hora: ".$frm["HoraReservaConfirma"]." ".$mensaje_confirmacion." por operaciones: " . $frm["ObservacionConfirmada"];
			SIMUtil::enviar_notificacion_push_general($frm["IDClub"],$frm["IDSocio"],$MensajeConfirma);
			SIMNotify::capture( "Reserva actualizada correctamente" , "info alert-success" );
		break;

		case 'confirmacionhorario':
		$frm = SIMUtil::varsLOG( $_POST );
		//Actualizo Horas de reserva golf
		$sql_reserva_estado = "Update ReservaGeneral Set HoraSalidaPrimero9hoyos = '".$frm["HoraSalidaPrimero9hoyos"]."', HoraFinPrimero9Hoyos = '".$frm["HoraFinPrimero9Hoyos"]."', HoraSalidaSegundo9Hoyos = '".$frm["HoraSalidaSegundo9Hoyos"]."' , HoraFinSegundo9Hoyos='".$frm["HoraFinSegundo9Hoyos"]."', UsuarioTrEd = '".$frm["IDUsuario"]." Actualiza horario golf', FechaTrEd = NOW() Where IDReservaGeneral = '".$frm["IDReservaGeneral"]."'";
		$dbo->query($sql_reserva_estado);
		SIMNotify::capture( "Reserva guardada correctamente" , "info alert-success" );
	break;

		case "new":
			foreach( $elementos[$ids] as $key_elemento => $datos_elemento  )
			{
				$MostrarTodoDia = $dbo->getFields( "Servicio" , "PermiteReservaCualquierHora" , "IDServicio = '" . $ids . "'" );
				$PermiteAntes=SIMUser::get( "PermiteReservarAntes" );
				if($PermiteAntes=="N"){
					$EsAdmin="";
				}
				else{
					$EsAdmin="S";
				}


				//if($_GET["ids"]==1375 || $_GET["ids"]==571 || $_GET["ids"]==1392 || $_GET["ids"]==77400):// Gun Club Reservados
				if($pantalla_carga_elemento=="S"):
					//$horas = SIMWebServiceHotel::get_disponiblidad_elemento_servicioV2( SIMUser::get("club"), $ids, $fecha, "728","S","","","","",$MostrarTodoDia);
					//solo consulto un elemento
					if(!empty($_GET["IDElementoSelecc"]) && (int)$_GET["IDElementoSelecc"]>0 && $datos_elemento["IDElemento"]==$_GET["IDElementoSelecc"]):
						$horas = SIMWebService::get_disponiblidad_elemento_servicio( SIMUser::get("club"), $ids, $fecha, $_GET["IDElementoSelecc"],$EsAdmin,"","","","",$MostrarTodoDia,SIMUser::get( "IDUsuario" ));
					endif;
				else:
						$horas = SIMWebService::get_disponiblidad_elemento_servicio( SIMUser::get("club"), $ids, $fecha, $datos_elemento["IDElemento"],$EsAdmin,"","","","",$MostrarTodoDia,SIMUser::get( "IDUsuario" ));
				endif;


				unset($array_datos_elemento);

				//Consulto el servicio maestro si es golf lo envio al metodo de horas de campos de golf que es especial
				$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $ids . "'" );
				if($id_servicio_maestro==15): //15 = Golf
					foreach( $horas["response"]["0"]["Disponibilidad"] as $key_horas => $todashoras ){
						foreach( $todashoras as $key_todahora => $datos_horas ){
						  //print_r($datos_horas);
						  if($datos_horas["IDElemento"]==$datos_elemento["IDElemento"]):
								//echo "<br>" . $datos_horas["IDElemento"];
								$array_datos_elemento [][] =$datos_horas;
								$array_horas[ $datos_horas["IDElemento"] ]  = $array_datos_elemento;
						  endif;
						}
					}//end for

				else:
				foreach( $horas["response"] as $key_horas => $datos_horas )
				{
					$array_horas[ $datos_elemento["IDElemento"] ]  = $datos_horas["Disponibilidad"];

				}//end for

				endif;

			}//end for

		break;

		case "cargareservas" :
					if(count($_POST["IDDia"])<=0){
							SIMUtil::display_msg("Debe seleccionar los dias");
					}
					else{
								$time_start = SIMUtil::getmicrotime();
								$nombre_archivo = copiar_archivo($_POST,$_FILES);
								if($nombre_archivo=="error"):
									echo "Error Transfiriendo Archivo";
									exit;
								endif;


								$result = get_data($nombre_archivo,SOCIOPLANO_DIR.$nombre_archivo,$_POST['IGNORELINE'],$_POST['FIELD_TEMINATED'],$_POST['field'],$_POST['IDClub'],$_POST['IDServicio'],$_POST["FechaInicio"],$_POST["FechaFin"],$_POST["IDDia"]);
								if($result["Numregs"] > 0){
									echo " <br> Archivo $filename Registros $result[Numregs] <font color ='blue'><b>Insertados</b></font> $result[RegsOK]<br>";

								} // if($result["Numregs"] > 0){

							$time_end = SIMUtil::getmicrotime();
							$time = $time_end - $time_start;
							$time = number_format($time,3);
							SIMUtil::display_msg("Tiempo de Actulizaci&oacute;n $time Segundos");
					}
				exit;
		break;
		case "eliminareservas":
			$sql = "SELECT * FROM ReservaGeneral WHERE IDClub = '".$_POST[IDClub]."' AND IDServicio = '".$_POST[IDServicio]."' AND IdentificadorServicio LIKE '%".$_POST['identificador']."%' ORDER BY FechaTrCr DESC";
			$qry = $dbo->query($sql);
			$num = 0;

			while($row = $dbo->fetchArray($qry))
			{
				$num++;
				$sql2="INSERT INTO ReservaGeneralEliminada (IDClub, IDSocio, IDSocioReserva, IDUsuarioReserva, IDServicio, IDServicioElemento, IDEstadoReserva, IDDisponibilidad, IDReservaGrupos, IDInvitadoBeneficiario, IDSocioBeneficiario, IDServicioTipoReserva,
				Fecha, Hora, Observaciones, Tee, IDAuxiliar, IDTipoModalidadEsqui, CantidadInvitadoSalon, NumeroInscripcion,UsuarioTrCr, FechaTrCr, Razon)
						Values ('" . $row[IDClub] . "','" . $row[IDSocio] . "','" . $row[IDSocio] . "', '" . $row[IDUsuarioReserva] . "', '" . $row[IDServicio] . "','" . $row[IDElemento] . "', '1','" . $row[IDDisponibilidad] . "','" . $row[IDReservaGrupos] . "','" . $row[IDInvitadoBeneficiario] . "','" . $row[IDSocioBeneficiario] . "',
								'" . $row[IDServicioTipoReserva]. "','" . $row[Fecha] . "', '" . $row[Hora] . "','" . $row[Observaciones] . "','" . $row[Tee] . "','" . $row[IDAuxiliar] . "','" . $row[IDTipoModalidadEsqui] . "','" . $row[CantidadInvitadoSalon] . "','" . $row[NumeroInscripcion] . "',
								'" . SIMUser::get( "IDUsuario" ) . "',NOW(), 'Eliminada por archivo: ".$row[IdentificadorServicio]."')";

				$qry2 = $dbo->query($sql2);

				$delete = "DELETE FROM ReservaGeneral WHERE IDClub = '".$_POST[IDClub]."' AND IDServicio = '".$_POST[IDServicio]."' AND IdentificadorServicio LIKE '%".$_POST['identificador']."%' ORDER BY FechaTrCr DESC";
				$qryDelete = $dbo->query($delete);
			}
			echo "Se eliminaron: ".$num;
			exit;
		break;




	}//end switch



	//if($_GET["ids"]==1375 || $_GET["ids"]==571 || $_GET["ids"]==1392):// Gun Club Reservados
	if($pantalla_carga_elemento=="S"):
		//print_r($horas);
		//exit;
	endif;



	if (!empty($_GET["idr"])):
		$detalle_reserva = $dbo->fetchAll( "ReservaGeneral", " IDReservaGeneral = '" . $_GET["idr"] . "' ", "array" );
		$sql_invitado_reserva = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$_GET["idr"]."'";
		$qry_invitado_reserva = $dbo->query($sql_invitado_reserva);
		while($row_invitado_reserva = $dbo->fetchArray($qry_invitado_reserva)):
			$array_invitados[$row_invitado_reserva["IDReservaGeneralInvitado"]] = $row_invitado_reserva;
		endwhile;
	endif;



	if( empty( $view ) )
		$view = "views/reservas/form.php";




?>
