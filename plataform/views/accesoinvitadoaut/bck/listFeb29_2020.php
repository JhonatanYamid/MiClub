<?
	/*
	$url_search = "";
	if( SIMNet::req("action") == "search" )
	{
		$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
	}//end if
	*/
?>

	<?php if($total_resultados>=1): ?>
	<div class="widget-box transparent" id="recent-box">
		<div class="widget-header" style="height:10px !important;">
			<!--
            <h4 class="widget-title lighter smaller">
	                <i class="ace-icon fa fa-users orange"></i>
                    <?php echo $modo_busqueda ?>
                    <?php echo strtoupper(SIMNet::req("qryString")); ?>
            </h4>
            -->


                        <b>Fecha Inicio Aut:</b> <?php echo $datos_invitacion["FechaInicio"]; ?>
                        <b>Fecha Fin Aut:</b> <?php echo $datos_invitacion["FechaFin"]; ?>
                        <b>Cod Familia: </b><?php echo $datos_socio["AccionPadre"]; ?>
                        <b>Autoriza:</b> <?php echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?>
												<b>AP:</b> <?php  if(!empty($datos_socio["ObservacionEspecial"])) echo $datos_socio["ObservacionEspecial"]; else echo "NO"; ?>


		</div>

		<div class="widget-body">
			<div class="widget-main padding-4">
				<div class="row">
					<div class="col-xs-12">
						<div id="jqGrid_container">
						<table id="simple-table" class="table table-striped table-bordered table-hover">
                        	<tr>
                            	<td>
                                <table class="table table-striped table-bordered table-hover">
                                	<tr>
                                   	  <td valign="top" width="100">
											<?
                                            if($modulo=="Socio"):
                                                $ruta_foto = SOCIO_ROOT;
                                                $nombre_foto = "Foto";
												$identificador=$datos_invitado["IDSocio"];
                                            else:
                                                $ruta_foto = IMGINVITADO_ROOT;
                                                $nombre_foto = "FotoFile";
												$identificador=$datos_invitado["IDInvitado"];
                                            endif;

                                            if (!empty($datos_invitado[$nombre_foto])) {
                                                 echo "<img src='".$ruta_foto."$datos_invitado[$nombre_foto]' width='200' height='220'  >";
                                               }else{
                                                 echo "<img src='assets/images/sinfoto.png' width='100' height='120'> ";
                                               }
                                            ?>

											<?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioAutorizacionSalida"){ ?>


                                            <?php } ?>
                                      </td>
                                        <td valign="top">

                                        <table class="table table-striped table-bordered table-hover">
                                        	<tr>
                                            	<td>&nbsp;
                                                	<?php echo $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]  ?>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td>&nbsp;
													<?php
                                                    $tipo_doc="";
                                                    $tipo_doc = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'" );
                                                    if(empty($tipo_doc)):
                                                        echo "Documento:";
                                                    else:
                                                        echo $tipo_doc;
                                                    endif;
                                                    ?>
                                                    <?php echo $datos_invitado["NumeroDocumento"];
													if($datos_invitado["TipoSocio"]=="Estudiante"):
														$sqlGradosSel = "SELECT DISTINCT Grado.Id, Grado.Nombre
														FROM Grado,Salon,Clase,GradosSalon,GradosSalonClase, UserGradosSalonClase
														WHERE Grado.Id = GradosSalon.idGrados and Salon.Id=GradosSalon.idSalon
														and GradosSalon.Id = GradosSalonClase.idGradosSalon
														and GradosSalonClase.idClase = Clase.Id
														and UserGradosSalonClase.idGradosSalonClase = GradosSalonClase.Id
														and UserGradosSalonClase.idSocio = " . $datos_invitado[IDSocio];

														$result_grado = $dbo->query($sqlGradosSel);
														$datos_grado = $dbo->fetchArray($result_grado);
														//echo "<br><b>&nbsp;Curso: " . $datos_grado["Nombre"]."</b>";
														echo "<br><b>&nbsp;Curso: " . $datos_invitado["Curso"]."</b>";
													endif;

													?>

                                                </td>
                                            </tr>
                                            <tr>
                                            	<td>
                                                <?php
												if(!empty($datos_invitado["IDTipoInvitado"])):
													echo  "<br>&nbsp;" . $dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '" . $datos_invitado["IDTipoInvitado"] . "'" );
												endif;
												if(!empty($datos_invitado["IDClasificacionInvitado"])):
													echo  " / " . $dbo->getFields( "ClasificacionInvitado" , "Nombre" , "IDClasificacionInvitado = '" . $datos_invitado["IDClasificacionInvitado"] . "'" );
												endif;


												?>
                                                <br>&nbsp;Vehiculo:
                                                	<?php
					                                    //Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioAutorizacionSalida"):
															$condicion_vehiculo = " AND IDInvitado = '".$datos_invitado["IDInvitado"]."'";
														elseif($modulo == "Socio"):
															$condicion_vehiculo = " AND IDSocio = '".$datos_invitado["IDSocio"]."'";
														else:
															$condicion_vehiculo = " AND IDSocio = '-1'";
														endif;
														?>
                                                        <?php
														//datos vehiculo
														$sql_vehiculo = "Select * From Vehiculo Where 1 " . $condicion_vehiculo;
														$result_vehiculo = $dbo->query($sql_vehiculo);
                                                        $cont_vehiculo=0;
														while($row_vehiculo = $dbo->fetchArray($result_vehiculo)):
															$cont_vehiculo++;
															$array_placa[]=strtoupper($row_vehiculo["Placa"]);
															echo '<div style="border:1px solid #E9E9E9; margin-top:3px"><strong>Placa: ' . strtoupper($row_vehiculo["Placa"]) . "</strong><br>";
															if(empty($row_vehiculo["FechaTecnomecanica"])):
																echo '<span style="color: #F10004">Sin fecha tecnomecanica</span>';
															elseif(strtotime($row_vehiculo["FechaTecnomecanica"])<strtotime(date("Y-m-d"))):
																echo '<span style="color: #F10004">Tecnomecanica Vencida</span>';
															else:
																echo "Tecnomecanica al dia";
															endif;
															echo "<br>";
															//SOAT
															if(empty($row_vehiculo["FechaSeguro"])):
																echo '<span style="color: #F10004">Sin fecha SOAT</span>';
															elseif(strtotime($row_vehiculo["FechaSeguro"])<strtotime(date("Y-m-d"))):
																echo '<span style="color: #F10004">SOAT Vencido</span>';
															else:
																echo "SOAT al dia";
															endif;
															echo '</div>';
														endwhile;
														?>

                                                </td>
                                            </tr>
                                            <?php
												//Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
												if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioAutorizacionSalida"){ ?>
                                            <tr>
                                            	<td>
                                                 Licencia
                                                        <?php

														$cont_licencia = 0;
														$sql_licencia = "Select * From LicenciaInvitado Where IDInvitado = '" . $datos_invitado["IDInvitado"] . "'";
														$result_licencia = $dbo->query($sql_licencia);
														while($row_licencia = $dbo->fetchArray($result_licencia)):
															$cont_licencia++;
															echo "Categoria: " . strtoupper($row_licencia["Categoria"]) . " ";
															if(empty($row_licencia["FechaVencimiento"])):
																echo '<span style="color: #F10004">Sin fecha vencimiento</span>';
															elseif(strtotime($row_licencia["FechaVencimiento"])<=strtotime(date("Y-m-d"))):
																echo '<span style="color: #F10004">Licencia Vencida</span>';
															else:
																echo "Licencia al dia";
															endif;
														endwhile;
														if($cont_licencia<=0):
															echo '<span style="color: #F10004">Sin Licencia</span>';
														endif;
														?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                          </table>


                                        </td>
                                    </tr>
                                	<tr>
                                	  <td colspan="2">
                                      <?php



														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){
															if($datos_invitado["IDEstadoInvitado"]=="2" || $datos_invitado["IDEstadoInvitado"]=="3" || $datos_invitado["IDEstadoInvitado"]=="0" ){
																$bloqueado = "S";
																if($datos_invitado["IDEstadoInvitado"]=="0" && $modulo == "SocioAutorizacion" ){
																	$mensaje_bloqueo = "SIN ESTADO";
																}
																elseif($modulo == "SocioInvitadoEspecial"){ // A los invitados de socio sin estado no los bloqueo
																	$mensaje_bloqueo = "";
																	$bloqueado = "N";
																}
																else{
																	$mensaje_bloqueo = $datos_invitado["RazonBloqueo"];
																}
															}
															else{
																$hora_actual = date ("Y-m-d H:i:s");
																//Verifico si tiene algun bloqueo temporal
																$sql_observacion_bloqueo = "Select * From ObservacionInvitado Where IDInvitado = '".$datos_invitado["IDInvitado"]."' and FechaInicioBloqueo <= CURDATE() AND FechaFinBloqueo >= CURDATE() Order by IDObservacionInvitado Desc";
																$result_observacion_bloqueo = $dbo->query($sql_observacion_bloqueo);
																while($row_log_acceso = $dbo->fetchArray($result_observacion_bloqueo)):
																	if( $row_log_acceso["HoraInicioBloqueo"]=="00:00:00" && $row_log_acceso["HoraFinBloqueo"]=="00:00:00"):
																		$bloqueado = "S";
																		$mensaje_bloqueo = " (".$row_log_acceso["Observacion"].")";
																	else:
																		//Verifico si esta en la hora del bloqueo si es el dia actual
																		$hora_inicio_bloqueo = date("Y-m-d") . " " .$row_log_acceso["HoraInicioBloqueo"];
																		$hora_fin_bloqueo = date("Y-m-d") . " " .$row_log_acceso["HoraFinBloqueo"];
																		if( strtotime($hora_actual) >= strtotime($hora_inicio_bloqueo)  && strtotime($hora_actual) <= strtotime($hora_fin_bloqueo)  ){
																			$bloqueado = "S";
																			$mensaje_bloqueo = " (".$row_log_acceso["Observacion"].")";
																		}
																	endif;
																endwhile;
															}
														}
														else{
															if($datos_invitado["IDEstadoSocio"]=="2" || $datos_invitado["IDEstadoSocio"]=="3")
																$bloqueado = "S";
																$mensaje_bloqueo = $datos_invitado["RazonBloqueo"];
														}


														if($TipoAutorizacion!="SalidaEstudiante"):


														 ?>

                                                        <label>
                                                        	<?php if($bloqueado<>"S" && $bloqueo_seguridad == 0): ?>

                                                            <?php
                                                            //Verifico Cual fue el ultimo movimeinto registrado en el dia para saber si se activa la entrada o la salida
															//$sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc Limit 1";
															$sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc Limit 1";
															$result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
															$row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
															$total_log = $dbo->rows($result_log_acceso_ultimo);
															if($row_log_acceso_ultimo["Entrada"]=="S"):
																$campo_entrada = "disabled";
															else:
																$campo_entrada = "";
															endif;
															$campo_entrada="";
															if($row_log_acceso_ultimo["Salida"]=="S" || $total_log ==0 ):
																$campo_salida = "disabled";
															else:
																$campo_salida = "";
															endif;

															$campo_entrada="";
															$campo_salida="";



															?>
                                                               <input type="hidden" name="ModuloAcceso" id="ModuloAcceso" value="<?php echo $modulo; ?>" >
                                                               <input type="hidden" name="IdentificadorAcceso" id="IdentificadorAcceso" value="<?php echo $id_registro; ?>" >

                                                            <?php  else: ?>
                                                            		<?php if($bloqueo_seguridad==1) $mensaje_bloqueo ="Atencion: Problema en el filtro de seguridad";  ?>
																<span style="color: #F10004">BLOQUEADO.</span> <?php echo $mensaje_bloqueo; ?>
															<?php endif; ?>
                                                         </label>
                                                         <?php endif; ?>



                                      </td>
                              	  </tr>
                                  	<?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial" || $modulo == "SocioAutorizacionSalida"){  ?>
                                	<tr>
                                	  <td colspan="2">Alertas:
                                      <?php
									  					echo $datos_invitacion["Observacion"];
														echo $datos_invitado["ObservacionGeneral"];
														//Consulto el historial de alertas
														$sql_observacion = "Select * From ObservacionInvitado Where IDInvitado = '".$datos_invitado["IDInvitado"]."' Order by IDObservacionInvitado Desc";
														$result_observacion = $dbo->query($sql_observacion);
														while($row_observacion = $dbo->fetchArray($result_observacion)):
															echo $row_observacion["Observacion"];
															if(!empty($row_observacion["FechaInicioBloqueo"]) && $row_observacion["FechaInicioBloqueo"] != "0000-00-00" && !empty($row_observacion["FechaFinBloqueo"]) && $row_observacion["FechaFinBloqueo"]!="0000-00-00"){
																echo "<br>Inicio Bloqueo:" . $row_observacion["FechaInicioBloqueo"] . " " . $row_observacion["HoraInicioBloqueo"];
																echo "<br>Fin Bloqueo:" . $row_observacion["FechaFinBloqueo"] . " " . $row_observacion["HoraFinBloqueo"];
															}
															echo "<br>";
														endwhile;
														?>
                                      </td>
                              	  </tr>
                                  <?php } ?>
                                	<tr>
                                	  <td colspan="2">Entradas/Salidas registradas: <?php echo date("Y-m-d"); ?>:
                               	      <?php
														//Consulto el historial de entradas y salidas del dia
														if($TipoAutorizacion=="SalidaEstudiante"):
															if(count($array_alumno_autorizado)>0):
																	foreach($array_alumno_autorizado as $id_invitacion => $id_socio)
																		$array_id_invitacion[]=$id_invitacion;

																	if(count($array_id_invitacion)>0)
																		$id_invitaciones = implode(",",$array_id_invitacion);

															$sql_log_acceso = "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc";
															endif;
														else:
															$id_invitaciones = $id_registro;
														endif;

														$sql_log_acceso = "Select * From LogAcceso Where IDInvitacion in (".$id_invitaciones.")  and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc";

														$result_log_acceso = $dbo->query($sql_log_acceso);
														while($row_log_acceso = $dbo->fetchArray($result_log_acceso)):
															if($row_log_acceso["Entrada"]=="S"):
																echo "<br>Entrada: " . substr($row_log_acceso["FechaIngreso"],11);
															elseif($row_log_acceso["Salida"]=="S"):
																echo "<br>Salida: " . substr($row_log_acceso["FechaSalida"],11) ;
																if($TipoAutorizacion=="SalidaEstudiante"):
																	$id_socio_sale = $dbo->getFields( "SocioAutorizacionSalida" , "IDSocioSalida" , "IDSocioAutorizacionSalida = '".$row_log_acceso["IDInvitacion"]."'" );
																	echo " Sale: " . $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$id_socio_sale."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$id_socio_sale."'" );
																endif;
															endif;
														endwhile;
														?></td>
                               	  </tr>
                               	  </table>
                              </td>

                                <?php
								if($datos_invitacion["CabezaInvitacion"]=="S"):
									while($datos_grupo_familiar = $dbo->fetchArray($result_grupo)):
									$contador_grupo++;
										if($nucleo_socio=="1"):
											$datos_invitado_familiar = $datos_grupo_familiar;
											$id_registro=$datos_invitado_familiar["IDSocio"];
										else:
											$id_registro=$datos_grupo_familiar["IDSocioInvitadoEspecial"];
											$datos_invitado_familiar = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_grupo_familiar["IDInvitado"] . "' ", "array" );
										endif;
									?>
                                    <?php if ($contador_grupo=="3"):
                                    	echo "</tr><tr>";
										endif;
									?>

									<?php endwhile;
								endif;
								?>





                                 <?php
								 //ALUMNOS AUTORIZADOS PARA SALIDA
								if(count($array_alumno_autorizado)>0):
									foreach($array_alumno_autorizado as $id_autorizacion => $id_socio):
										$datos_invitado_familiar = $dbo->fetchAll( "Socio", " IDSocio = '" . $id_socio. "' ", "array" );
										$contador_grupo++;
									?>
                                    <?php if ($contador_grupo=="3"):
                                    	echo "</tr><tr>";
										endif;
									?>

									<?php endforeach;
								endif;
								?>





                            </tr>
                        </table>
                        </div>
						<!-- PAGE CONTENT ENDS -->
					</div>
				</div>
			</div>
		</div>
	</div>
    <? elseif(!empty(SIMNet::req("qryString"))):
		if(count($array_proxima_autorizacion)>0):
			echo "<span style='color:#063; font-size:16px'; font-weight:bold><br>" . implode("<br>",$array_proxima_autorizacion) . '</span>';
		else: ?>
        <span style='color: #F00; font-size:16px'>
	        No se encontraron resultados
        </span>
		<?php endif; ?>
    <? endif; ?>

		<?

			include( "cmp/footer_grid.php" );
		?>


    	<script>
		 	<?php

			if($bloqueado<>"S" && $bloqueo_seguridad == 0):
				?>
				<?php if($row_log_acceso_ultimo["Entrada"]=="S"): ?>
					salida_automatico();
				<?php else: ?>
					ingreso_automatico();
				<?php endif; ?>
			<?php endif; ?>
		</script>
