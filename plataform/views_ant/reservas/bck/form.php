
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR RESERVA PARA <?=strtoupper( SIMUtil::tiempo( $fecha ) )?> -
			<input type="hidden" id="fechareserva" value="" />
			<a href="javascript:void(0);" class="calendar_nueva_reservas">
				Consultar otra fecha
			</a>
		</h4>


	</div>

			<?php
			$datos_servicio_config = $dbo->fetchAll("Servicio","IDServicio='".$_GET["ids"]."'","array" );
			$pantalla_carga_elemento=$datos_servicio_config["PantallaReservaElemento"];
			$permite_repetir=$datos_servicio_config["PermiteAdminRepetirReserva"];
			$flag_elementos_tipo="N";
			?>
			<?php if($resultadook!=1): ?>
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frmReservaGeneral" name="frmReservaGeneral" action="<?php echo SIMUtil::lastURI()?>">

							<div  class="form-group first ">


                                <?php
																//if($_GET["ids"]==1375 || $_GET["ids"]==571 || $_GET["ids"]==1392 || $_GET["ids"]==77400):
																	if($pantalla_carga_elemento=="S"):
																?>
                                    <div  class="form-group first ">
                                        <div  class="col-xs-12 col-sm-6">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Elemento: </label>

                                                <div class="col-sm-8">

                                                    <select name="IDElementoSeleccion" id="IDElementoSeleccion">
                                                        <option value="">Seleccione</option>
                                                    <?php foreach( $elementos[$ids] as $key_elemento => $datos_elemento  ): ?>
                                                            <option value="<?php echo $datos_elemento["IDElemento"]; ?>" <?php if($datos_elemento["IDElemento"]==$_GET["IDElementoSelecc"]) echo "selected"; ?>><?php echo $datos_elemento["Nombre"]; ?></option>
                                                    <?php endforeach; ?>
                                                    </select>
                                                </div>
                                        </div>
                                    </div>
                            <?php endif; ?>



                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Acción </label>

										<div class="col-sm-8">
											<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" >
											<input type="hidden" name="IDSocio" value="" id="IDSocio" class="mandatory" title="Socio">
										</div>

                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones: </label>

										<div class="col-sm-8">
                                        	<textarea rows="3" cols="50" id="Observaciones" name="Observaciones" class="form-control"></textarea>

										</div>


											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Invitados </label>

										<div class="col-sm-8">

																					<input type="text" id="AccionInvitado" name="AccionInvitado" placeholder="Número de Derecho" class="col-xs-12 autocomplete-ajax-socios" title="número de derecho" >

																					<?php if(SIMUser::get("club")==8 || (SIMUser::get("club")==9 && $_GET["ids"]==67 ) ){ ?>
																						Invitados registrados del socio
																						<input type="text" id="InvitadoSocioClub" name="InvitadoSocioClub" placeholder="Documento, nombre o apellido invitado" class="col-xs-12 autocomplete-ajax-socios-invitados" title="Invitado Socio" >
																					<?php } ?>


                                            <br><a id="agregar_invitado" href="#">Agregar</a> | <a id="borrar_invitado" href="#">Borrar</a>
											<br>
                                        	<select name="SocioInvitado[]" id="SocioInvitado" class="col-xs-8"  multiple >
                                        	<?php
											$item=1;
                                        	foreach($array_invitados as $id_invitado => $datos_invitado):
													$item--;
													if($datos_invitado["IDSocio"]>0):
														$nombre_socio = utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$datos_invitado["IDSocio"]."'" ));
													?>
														<option value="<?php echo "socio-".$datos_invitado["IDSocio"]; ?>"><?php echo $nombre_socio; ?></option>
                                                    <?php
													else: ?>
                                                    	<option value="<?php echo "externo-".$datos_invitado["Nombre"]; ?>"><?php echo $datos_invitado["Nombre"]; ?></option>
                                                    <?php
													endif;
											endforeach;
											?>
                                        </select>
                                        <input type="hidden" name="InvitadoSeleccion" id="InvitadoSeleccion" value="">

										</div>

								</div>
							</div>


                            <?php
							//Verifico si acepta algun tipo especial de Tipo Turno (Ej. Dobles/Sencillos)
							$idserviciomaestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $_GET["ids"] . "'" );
							//$datos_servicio_maestro = $dbo->fetchAll("ServicioMaestro","IDServicioMaestro='".$idserviciomaestro."'","array" );
							$sql_datos_servicio_maestro="Select * From ServicioMaestro Where IDServicioMaestro='".$idserviciomaestro."'";
							$result_datos_servicio_maestro=$dbo->query($sql_datos_servicio_maestro);
							$datos_servicio_maestro = $dbo->fetchArray($result_datos_servicio_maestro);

							if($datos_servicio_maestro["PermiteTipoReserva"]=="S"):

								$sql_tipo_reserva = "Select * From ServicioTipoReserva Where IDServicio = '".$ids."'";
								$r_tipo_reserva = $dbo->query($sql_tipo_reserva);
								$totaltipo=$dbo->rows($r_tipo_reserva);

							?>
                            <div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $datos_servicio_maestro["LabelTipoReserva"]; ?> </label>

										<div class="col-sm-8">
											<select name="IDTipoReserva" id="IDtipoReserva" class="habilita_elemento">
                                            	<option value="">Seleccione</option>
                                        	<?php

											while($row_tipo_reserva = $dbo->fetchArray($r_tipo_reserva)):
													//consulto los elemntos del tipo
													$sql_eletip="SELECT IDServicioElemento From ServicioElementoTipoReserva WHERE IDServicioTipoReserva = '".$row_tipo_reserva["IDServicioTipoReserva"]."'";
													$r_eletip=$dbo->query($sql_eletip);
													while($row_eletip=$dbo->fetchArray($r_eletip)){
														$array_eletip[$row_eletip["IDServicioElemento"]][]=$row_tipo_reserva["IDServicioTipoReserva"];
													}





												?>
                                            	<option value="<?php echo $row_tipo_reserva["IDServicioTipoReserva"]; ?>"><?php echo $row_tipo_reserva["Nombre"]; ?></option>
											<?php endwhile;
											if(count($array_eletip)>0){
												$flag_elementos_tipo="S";
											}
											else{
												$flag_elementos_tipo="N";
											}

											?>
                                            </select>


										</div>
								</div>
							</div>
                            <?php
													endif; ?>


							<?php
                            //Verifico si acepta auxiliares (Ej. Boleador)
                            if($datos_servicio_maestro["PermiteAuxiliar"]=="S"):
																$MultipleAuxiliar = $dbo->getFields( "Servicio" , "MultipleAuxiliar" , "IDServicio = '" . $_GET["ids"] . "'" );

                            ?>
                            <div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $datos_servicio_maestro["LabelAuxiliar"]; ?> </label>

										<div class="col-sm-8">

											<?php if($MultipleAuxiliar=="S"){
													?>

													<table class="table table-striped table-bordered table-hover">
															<tr>
																<?php
																$sql_auxiliar = "Select * From Auxiliar Where IDServicio = '".$ids."'";
																$r_auxiliar = $dbo->query($sql_auxiliar);
																while($row_auxiliar = $dbo->fetchArray($r_auxiliar)):
																	$contador++;
																	//Verifico si el auxiliar esta disponible en el dia
																	$dia_fecha= date('w', strtotime($fecha));
																	$sql_dispo_aux_gral = "Select * From AuxiliarDisponibilidadDetalle Where IDServicio = '".$ids."' and   IDDia like '%".$dia_fecha."|%' and IDAuxiliar like '%".$row_auxiliar["IDAuxiliar"]."|%' ";
																	$qry_dispo_aux_gral = $dbo->query($sql_dispo_aux_gral);
																	$row_disponibilidad_aux = $dbo->fetchArray($qry_dispo_aux_gral);
																	if((int)$row_disponibilidad_aux["IDAuxiliarDisponibilidadDetalle"]>0):

																				?>
					                             <td> <input type="checkbox" name="Auxiliar[]" value="<?php echo $row_auxiliar["IDAuxiliar"]; ?>"><?php echo $row_auxiliar["Nombre"]; ?></td>
																			 <?php
																			 		if($contador==12){
																						echo "</tr><tr>";
																						$contador=0;
																					}
																			 ?>
					                        <?php endif; ?>
																<?php endwhile; ?>

															</tr>
													</table>

													<?php
														}
														else{


												 ?>
														<select name="IDAuxiliar" id="IDIDAuxiliar">
                                  <option value="">Seleccione</option>
			                      <?php
														$sql_auxiliar = "Select * From Auxiliar Where IDServicio = '".$ids."'";
														$r_auxiliar = $dbo->query($sql_auxiliar);
														while($row_auxiliar = $dbo->fetchArray($r_auxiliar)):
															//Verifico si el auxiliar esta disponible en el dia
															$dia_fecha= date('w', strtotime($fecha));
															$sql_dispo_aux_gral = "Select * From AuxiliarDisponibilidadDetalle Where IDServicio = '".$ids."' and   IDDia like '%".$dia_fecha."|%' and IDAuxiliar like '%".$row_auxiliar["IDAuxiliar"]."|%' ";
															$qry_dispo_aux_gral = $dbo->query($sql_dispo_aux_gral);
															$row_disponibilidad_aux = $dbo->fetchArray($qry_dispo_aux_gral);
															if((int)$row_disponibilidad_aux["IDAuxiliarDisponibilidadDetalle"]>0): ?>
			                                            		<option value="<?php echo $row_auxiliar["IDAuxiliar"]; ?>"><?php echo $row_auxiliar["Nombre"]; ?></option>
			                        <?php endif; ?>
														<?php endwhile; ?>
                            </select>
											<?php } ?>
										</div>
								</div>
							</div>
                            <?php endif; ?>


							<?php if($permite_repetir=="S"):
							?>
	                          <div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Repetir reserva </label>

										<div class="col-sm-8">
													<select name="FrecuenciaRepetir" id="FrecuenciaRepetir">
                            	<option value="">Seleccione</option>
                              <option value="Dia">Diariamente</option>
															<option value="Semana">Semanalmente</option>
															<option value="Quincenal">Quincenal</option>
															<option value="Mes">Mensualmente</option>
                          </select>
													Hasta:
													<input type="text" id="FechaFinRepetir" name="FechaFinRepetir" placeholder="Fecha Fin Repetir" class="from-control calendar"  title="Fecha Fin Repetir" readonly='readonly'>
										</div>
								</div>
							</div>
	            <?php endif; ?>

							<?php
							$r_campos =& $dbo->all( "ServicioCampo" , "IDServicio = '" . $_GET["ids"]  ."' and Publicar = 'S'");
							if(count($r_campos)>0){ ?>

							<div  class="form-group first ">
								<?php
								 while( $r = $dbo->object( $r_campos ) ): ?>
										<div  class="col-xs-12 col-sm-6">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $r->Nombre; ?> </label>
												<div class="col-sm-8">
													<?php
													$array_opciones=explode(",",$r->Valor);
													switch($r->Tipo){
															case "Texto": ?>
																<input type="text" id="Campo<?php echo $r->IDServicioCampo; ?>" name="Campo<?php echo $r->IDServicioCampo; ?>" placeholder="<?php echo $r->Nombre; ?>" class="col-xs-12" title="<?php echo $r->Nombre; ?>" value="" >
															<?php
															break;
															case "Radio":
																foreach($array_opciones as $opcion){?>
																		<input type="radio" name="Campo<?php echo $r->IDServicioCampo; ?>" value="<?php echo $opcion; ?>"><?php echo $opcion; ?>
																<?php }
															break;
															case "Check":
															foreach($array_opciones as $opcion){
																$campo++;
																?>
																	<input type="checkbox" name="Campo<?php echo $r->IDServicioCampo ?>[]" value="<?php echo $opcion; ?>"><?php echo $opcion; ?>
															<?php }
															break;
															case "Lista":?>
															<select id="Campo<?php echo $r->IDServicioCampo; ?>" name="Campo<?php echo $r->IDServicioCampo; ?>" class="form-control">
																<option value=""></option>
																<?php
																foreach($array_opciones as $opcion){ ?>
																	<option value="<?php echo $opcion; ?>"><?php echo $opcion; ?></option>
																<?php	}	?>
															</select>
															<?php
															break;
															default:?>
																<input type="text" id="Campo<?php echo $r->IDServicioCampo; ?>" name="Campo<?php echo $r->IDServicioCampo; ?>" placeholder="<?php echo $r->Nombre; ?>" class="col-xs-12" title="<?php echo $r->Nombre; ?>" value="" >
																<?php
													} ?>



												 </div>
										</div>
								<?php endwhile; ?>
							</div>
						<?php } ?>




                            <?php
                            //Verifico si acepta numero de pinvitados para salones
							$MaximoInvitadosSalon = $dbo->getFields( "Servicio" , "MaximoInvitadosSalon" , "IDServicio = '" . $_GET["ids"] . "'" );
                            if((int)$MaximoInvitadosSalon>0):


                            ?>
                            <div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero de personas </label>

										<div class="col-sm-8">
											<select name="CantidadInvitadoSalon" id="CantidadInvitadoSalon">
                                            	<option value="">Seleccione</option>
                                                <?php for($maximoper=1;$maximoper<=$MaximoInvitadosSalon;$maximoper++): ?>
                                                	<option value="<?php echo $maximoper; ?>"><?php echo $maximoper; ?></option>
                                                <?php endfor; ?>

                                            </select>
										</div>
								</div>
							</div>
                            <?php endif; ?>


						   <?php
                            //Verifico si acepta modalidad de esqui (Ej. Wake, Salalom)
                            if($datos_servicio_maestro["IDServicioInicial"]=="7"):
                            ?>
                            <div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modalidad </label>

										<div class="col-sm-8">
											<select name="IDTipoModalidadEsqui" id="IDTipoModalidadEsqui">
                                            	<option value="">Seleccione</option>
                                        	<?php
											$sql_modalidad = "Select * From TipoModalidadEsqui Where IDClub = '".SIMUser::get("club")."'";
											$r_modalidad = $dbo->query($sql_modalidad);
											while($row_modalidad = $dbo->fetchArray($r_modalidad)): ?>
                                            	<option value="<?php echo $row_modalidad["IDTipoModalidadEsqui"]; ?>"><?php echo $row_modalidad["Nombre"]; ?></option>
											<?php endwhile; ?>
                                            </select>
										</div>
								</div>
							</div>
                            <?php endif; ?>








							<div  class="form-group  ">
								<div class="tabbable">
									<ul class="nav nav-tabs" id="myTab">
										<?
										$active = " class=\"active\" ";
										$aria_expanded = "true";
										$contador=1;

										foreach( $elementos[$ids] as $key_elemento => $datos_elemento  )
										{
											$mostrar="S";
											$clase_elemento="";



											if(count($array_eletip[$datos_elemento["IDElemento"]])>0){
												foreach ($array_eletip[$datos_elemento["IDElemento"]] as $keyserv => $value_serv) {
													$clase_elemento.=" elemento_li_".$value_serv;
												}
											}

											if($contador==1){
												$active = " class='active elementos_servicio ".$clase_elemento."' ";
											}
											else{
												$active = " class='elementos_servicio ".$clase_elemento."' ";
											}

											//if($_GET["ids"]==1375 || $_GET["ids"]==571 || $_GET["ids"]==1392 || $_GET["ids"]==77400):// Gun Club Reservados
											if($pantalla_carga_elemento=="S"):
												//solo consulto un elemento
												if(!empty($_GET["IDElementoSelecc"]) && (int)$_GET["IDElementoSelecc"]>0 && $datos_elemento["IDElemento"]==$_GET["IDElementoSelecc"]):
													$mostrar="S";
												else:
													$mostrar="N";
												endif;
											endif;

											if($mostrar=="S"):
										?>
                      <li <?=$active ?>  style="<?php if($flag_elementos_tipo=="S") echo 'display:none;' ?>" >
                          <a data-toggle="tab" href="#tab<?=$key_elemento ?>" aria-expanded="<?=$aria_expanded ?>"><?=$datos_elemento["Nombre"]; ?></a>
                      </li>
										<?
										   endif;
											$active = "";
											$aria_expanded = "false";
											$contador++;
										}//end for

										?>


									</ul>

									<div class="tab-content">
										<?
										$active = " in active ";
										$aria_expanded = "true";
										$horaactual = strtotime( date("H:i:s") );

										foreach( $elementos[$ids] as $key_elemento => $datos_elemento  )
										{

												$mostrar="S";
												if($pantalla_carga_elemento=="S"):
												//if($_GET["ids"]==1375 || $_GET["ids"]==571 || $_GET["ids"]==1392 || $_GET["ids"]==77400):// Gun Club Reservados

													//solo consulto un elemento
													if(!empty($_GET["IDElementoSelecc"]) && (int)$_GET["IDElementoSelecc"]>0 && $datos_elemento["IDElemento"]==$_GET["IDElementoSelecc"]):
														$mostrar="S";
														$active = " in active ";
													else:
														$mostrar="N";
													endif;
												endif;

												if($mostrar=="S"):


										?>
											<div id="tab<?=$key_elemento ?>" class="tab-pane divhorarios fade <?php if($flag_elementos_tipo=="N") echo $active;  ?> ">

												<table id="disponibilidad<?=$key_elemento ?>" class="table table-striped table-bordered table-hover">
													<thead>
														<tr>

															<th>Hora</th>
															<th>Estado</th>
															<th class="hidden-480"></th>


														</tr>
													</thead>

													<tbody>
														<?


														foreach( $array_horas[ $datos_elemento["IDElemento"]] as $key_disp => $todashoras )
															foreach( $todashoras as $key_todahora => $datos_horas )
														{
															//print_r( $datos_horas );
															$horamostrar = strtotime( $datos_horas["Hora"] );
															//if( $horamostrar >= $horaactual || $fecha <> date("Y-m-d") )
															//{

																$texto_hora = $datos_horas["Hora"] . " " . $datos_horas["Tee"];



														?>
																<tr>
																	<td>
																		<a href="#"><?=$texto_hora; ?></a>
																	</td>
																	<td class="hidden-480">
																		<?
																			if( $datos_horas["Disponible"] == "S" )
																				echo "Disponible";
																			else
																				echo "Ocupado";
																		?>
																	</td>
																	<td >
																		<?
																			if( $datos_horas["Disponible"] == "S" )
																			{
																				$contador_fila++;
																		?>
																				<a href="#reservas" class="brnReservaGeneral" rel="<?php echo $datos_elemento["IDElemento"] ?>" rev="<?php echo $datos_horas["Hora"] ?>" lang="<?php echo $datos_horas["Tee"] ?>" fila=<?php echo $contador_fila; ?>  ><span id="txtmsjreserva<?php echo $contador_fila; ?>">Reservar</span></a>
																		<?
																			}//end if
																			else{
																				$nombre_socio = utf8_decode( $datos_horas["Socio"] );
																				if(!empty($nombre_socio)):
																					echo $nombre_socio;
																				else:
																					echo  utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$datos_horas["IDSocio"]."'" ) . "  " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$datos_horas["IDSocio"]."'" ));
																				endif;
																				//print_r($datos_horas);

																				if(!empty($datos_horas["Tee"]) && empty($nombre_socio)):
																					 $sql_reserva = "Select * From ReservaGeneral Where IDEstadoReserva = 1 and Hora = '".$datos_horas["Hora"]."' and Tee = '".$datos_horas["Tee"]."' and Fecha = '".$_GET["fecha"]."' and IDServicioElemento = '".$datos_horas["IDElemento"]."'";
																					 $qry_rserva = $dbo->query($sql_reserva);
																					 $row_reserva = $dbo->fetchArray($qry_rserva);

																					 $sql_socio = "Select * From Socio Where IDSocio = '".$row_reserva["IDSocio"]."'";
																					 $qry_socio = $dbo->query($sql_socio);
																					 $row_socio = $dbo->fetchArray($qry_socio);
																					 echo utf8_decode($row_socio["Nombre"] . " " . $row_socio["Apellido"]);
																				endif;
																			}

																		?>
																	</td>
																</tr>

														<?

															//}//end if
														}//end for

														?>


													</tbody>
												</table>

											</div>
										<?
											endif;

											$active = "";
										}//end for
										?>


									</div>
								</div>
							</div>





							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
									<input type="hidden" name="action" value="insert">
									<input type="hidden" id="ids" name="ids" value="<?=$ids ?>">
									<input type="hidden" id="fecha" name="fecha" value="<?=$fecha ?>">
									<input type="hidden" id="idelemento" name="idelemento" value=""  title="elemento">
									<input type="hidden" id="hora" name="hora" value="" class="mandatory" title="hora">
                  <input type="hidden" id="tee" name="tee" value="" class="" title="tee">
									<input type="hidden" name="elemento_con_tipo" id="elemento_con_tipo" value="<?php echo $flag_elementos_tipo; ?>" >
									<button class="btn btn-info btnEnviar" type="button" rel="frmReservaGeneral" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										Crear Reserva
									</button>


								</div>
							</div>

					</form>
				</div>
			</div>
			<?php endif; ?>



		</div><!-- /.widget-main -->



<?
	include( "cmp/footer_scripts.php" );
?>
		<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
