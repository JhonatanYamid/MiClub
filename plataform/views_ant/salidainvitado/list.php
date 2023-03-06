<?
	/*
	$url_search = "";
	if( SIMNet::req("action") == "search" )
	{
		$url_search = "?oper=search_url&qryString=" . SIMNet::get("qryString");
	}//end if
	*/
?>

	<?php
	$sql_borro_ant = $dbo->query("Delete from LogAccesoDiario Where FechaTrCr < '".date("Y-m-d")."'");
	$array_tipo_contratista = array ("4","19","20","21");



	if($total_resultados>=1): ?>
	<div class="widget-box transparent" id="recent-box">
		<div class="widget-header">
			<!--
            <h4 class="widget-title lighter smaller">
	                <i class="ace-icon fa fa-users orange"></i>
                    <?php echo $modo_busqueda ?>
                    <?php echo strtoupper(SIMNet::req("qryString")); ?>
            </h4>
            -->

                        <b>Tipo:</b> <?php echo $datos_invitacion["TipoInvitacion"]; ?>
                        <b>Fecha Inicio Aut:</b> <?php echo $datos_invitacion["FechaInicio"]; ?>
                        <b>Fecha Fin Aut:</b> <?php echo $datos_invitacion["FechaFin"]; ?>
                        <b>Predio:</b> <?php echo $datos_socio["Predio"]; ?>
                        <b>Accion: </b><?php echo $datos_socio["Accion"]; ?>
                        <b>Socio:</b> <?php echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?>


												<?php
												if(!empty($mensaje_salida)){
													echo "<span style='color:#1DA912; font-size:16px'; font-weight:bold><br>" . $mensaje_salida . '</span>';
													exit;
												}
												?>



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
                                                 echo "<img src='".$ruta_foto."$datos_invitado[$nombre_foto]' width='100' height='120'  >";
                                               }else{
                                                 echo "<img src='assets/images/sinfoto.png' width='100' height='120'> ";
                                               }
                                            ?>
                                            <a  class="fancybox" href="../admin/tomarfoto/webcamjquery/index.php?action=foto&IDRegistro=<?php echo $identificador; ?>&Modulo=<?php echo $modulo; ?>" data-fancybox-type="iframe">
                                                                    <i class="ace-icon fa fa-camera bigger-120"></i>
                                                                    <span class="bigger-110">Tomar Foto</span>
                                            	 </a>
											<?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){ ?>

                                             <a  class="fancybox" href="invitadosgeneral.php?action=edit&id=<?=$datos_invitado["IDInvitado"]?>&refiere=porteria" data-fancybox-type="iframe">
                                                                    <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                                    <span class="bigger-110">Editar info</span>
                                            </a>
                                            <?php } ?>
                                      </td>
                                        <td valign="top">

                                        <table class="table table-striped table-bordered table-hover">
                                        	<tr>
                                            	<td>&nbsp;
                                                	<?php echo $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]  ?>
                                                </td>
                                            </tr>
                                            <!--
                                            <tr>
                                            	<td>&nbsp;
													<?php
                                                    $tipo_doc="";
                                                    $tipo_doc = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'" );
                                                    if(empty($tipo_doc)):
                                                        echo "Documento";
                                                    else:
                                                        echo $tipo_doc;
                                                    endif;
                                                    ?>
                                                    <?php echo $datos_invitado["NumeroDocumento"];  ?>

                                                </td>
                                            </tr>
                                            -->
                                            <tr>
                                            	<td>
                                                &nbsp;Predio <?php echo $datos_invitado["Predio"];  ?>
                                                <?php
												if(!empty($datos_invitado["IDTipoInvitado"])):
													echo  "<br>&nbsp;" . $dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '" . $datos_invitado["IDTipoInvitado"] . "'" );
												endif;
												if(!empty($datos_invitado["IDClasificacionInvitado"])):
													echo  " / " . $dbo->getFields( "ClasificacionInvitado" , "Nombre" , "IDClasificacionInvitado = '" . $datos_invitado["IDClasificacionInvitado"] . "'" );
												endif;


												?>
                                                 <br>
                                                <?php
                                                //ARL
															//if(empty($datos_invitado["FechaVencimientoArl"]) && in_array($datos_invitado["IDTipoInvitado"],$array_tipo_contratista)):
															if(empty($datos_invitado["FechaVencimientoArl"]) && ($datos_invitado["IDTipoInvitado"]!="3" )):
																echo '<span style="color: #F10004">Sin fecha ARL</span>';
															//elseif(strtotime($datos_invitado["FechaVencimientoArl"])<strtotime(date("Y-m-d"))  && in_array($datos_invitado["IDTipoInvitado"],$array_tipo_contratista)):
															elseif(strtotime($datos_invitado["FechaVencimientoArl"])<strtotime(date("Y-m-d")) && (($datos_invitado["IDTipoInvitado"]!="3" )) ):
																echo '<span style="color: #F10004">ARL Vencido</span>';
															else:
																echo "<strong>ARL al dia</strong>";
															endif;
												?>

                                                <br>&nbsp;Vehiculo:
                                                	<?php
					                                    //Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"):
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
															echo "<br>";

															echo '</div>';
														endwhile;
														?>

                                                </td>
                                            </tr>
                                            <?php
												//Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
												if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){ ?>
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
                                      <label>
                                                        	<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" value="Peatonal"/>
                                                        	<span class="lbl">Peatonal</span>
                                        </label>
                                                        <?php if(count($array_placa)>0):
															foreach($array_placa as $placa_vehiculo): ?>
                                                        <label>
                                                        	<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" value="Vehiculo <?php echo $placa_vehiculo; ?>"/>
                                                        	<span class="lbl"><?php echo $placa_vehiculo; ?></span>
                                                        </label>
                                                        <?php
															endforeach;
														endif; ?>
                                                        <!--
                                                        <label>
                                                        	<input name="MecanismoEntradaIngreso" type="radio" class="ace tipoentrada" value="OtroVehiculo"/>
                                                        	<span class="lbl">Otro Vehiculo</span>
                                                        </label>
                                                        -->
                                                        <span class="lbl">

                                                        <?php
														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"):
															$link_otro_vehiculo = "invitadosgeneral.php?action=edit&id=".$datos_invitado["IDInvitado"]."&editarinfo=n&tabinvitado=vehiculos";
														else:
															$link_otro_vehiculo = "socios.php?action=edit&id=".$datos_invitado["IDSocio"]."&editarinfo=n&tabsocio=vehiculos";
														endif;
														?>
                                                            <a  class="fancybox_vehiculo" href="<?php echo $link_otro_vehiculo?>" data-fancybox-type="iframe">
                                                                        <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                                        <span class="bigger-110">Agregar Veh&iacute;culo</span>
                                                            </a>


                                                        </span>

                                                        <?php
                                                //Consulto los predios del socio y si tiene los muestre para seleccionar el predio
												$sql_predio_soc = "Select * From Predio Where IDSocio = '".$datos_socio["IDSocio"]."'";
												$result_predio_soc = $dbo->query($sql_predio_soc);
												$total_predio_soc = $dbo->rows($result_predio_soc);
												if((int)$total_predio_soc>0):
													echo "<br>Predio al que se dirige:";
												endif;
												$contador_predio=0;
												while($row_predio_soc = $dbo->fetchArray($result_predio_soc)):
													$contador_predio++;
												?>
                                                	<label>
                                                        <input name="PredioIngreso" type="radio" class="ace" value="<?php echo $row_predio_soc["Predio"];  ?>" <?php if($contador_predio==1): ?> checked <?php endif; ?> />
                                                        <span class="lbl"><?php echo $row_predio_soc["Predio"];  ?></span>
                                                    </label>
												<?php endwhile;	?>
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
																elseif($modulo == "SocioInvitadoEspecial" && (int)$datos_invitado["IDEstadoInvitado"]<=0){ // A los invitados de socio sin estado no los bloqueo
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
														$bloqueado="N";
														 ?>
                                                        <label>
                                                        	<?php if($bloqueado<>"S"): ?>

                                                            <?php
                                                            //Verifico Cual fue el ultimo movimeinto registrado en el dia para saber si se activa la entrada o la salida
															//$sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc Limit 1";
															$sql_log_acceso_ultimo = "Select * From LogAccesoDiario Where IDInvitacion = '".$id_registro."' Order by IDLogAcceso Desc Limit 1";
															$result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
															$row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
															$total_log = $dbo->rows($result_log_acceso_ultimo);
															if($row_log_acceso_ultimo["Entrada"]=="S"):
																$campo_entrada = "disabled";
															else:
																$campo_entrada = "";
															endif;

															if($row_log_acceso_ultimo["Salida"]=="S" || $total_log ==0 ):
																$campo_salida = "disabled";
															else:
																$campo_salida = "";
															endif;

															$campo_entrada = "";
															$campo_salida = "";

															?>

                                       							 </label>
                                                                <label style="padding-left:40px">
                                                                <!--<input name="Salida" id="Salida" class="ace input-lg ace-checkbox-2 salida_acceso" type="checkbox" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>" <?php echo $campo_salida; ?> /> -->
																<?php
																	if(SIMUser::get("club") == 16 || SIMUser::get("club") == 8)
																	{			
																																	
																		$club = SIMUser::get("club");
																		$consulta = "SELECT * FROM AccesosOtrosDatos WHERE IDClub = '".$club."' AND Movimiento = 'Entrada' AND IDPreguntaAcceso = 43 AND IDInvitacion = '".$observacion."' ORDER BY FechaTrCr DESC Limit 1";
																		$ejecuta = $dbo->query($consulta);
																		$obs = $dbo->fetchArray($ejecuta);
																?>
																	<textarea>Comentario ultima entrada: <?php echo $obs['Valor']; ?></textarea>
																	<input id="prodId" name="prodId" type="hidden" value="<?php echo $observacion; ?>"/>
																	<br>
																<?php
																}
																?>
													<input type="button" value="REGISTRAR SALIDA" name="salidaanterior" id="salidaanterior" onclick="window.location.href='salidainvitado.php?qryString=<?php echo $_GET["qryString"]; ?>&modulo=<?php echo $modulo ?>&action=salidaespecial'">


                                                            <?php  else: ?>
																<span style="color: #F10004">BLOQUEADO</span> <?php echo $mensaje_bloqueo; ?>
															<?php endif; ?>
                                                         </label>
                                      </td>
                              	  </tr>
                                  	<?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){  ?>
                                	<tr>
                                	  <td colspan="2">Alertas:
                                      <?php
									  					echo $datos_invitado["ObservacionGeneral"];
														$datos_invitacion["ObservacionSocio"];
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
														$sql_log_acceso = "Select * From LogAccesoDiario Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc";
														$result_log_acceso = $dbo->query($sql_log_acceso);
														while($row_log_acceso = $dbo->fetchArray($result_log_acceso)):
															if($row_log_acceso["Entrada"]=="S"):
																echo "<br>Entrada: " . substr($row_log_acceso["FechaIngreso"],11);
															elseif($row_log_acceso["Salida"]=="S"):
																echo "<br>Salida: " . substr($row_log_acceso["FechaSalida"],11) ;
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
                                    <td valign="top">
                                    <table class="table table-striped table-bordered table-hover">
                                	<tr>
                                   	  <td valign="top" width="100">
											<?
									if($modulo=="Socio"):
										$ruta_foto = SOCIO_ROOT;
										$nombre_foto = "Foto";
									else:
										$ruta_foto = IMGINVITADO_ROOT;
										$nombre_foto = "FotoFile";
									endif;

									if (!empty($datos_invitado_familiar[$nombre_foto])) {
										 echo "<img src='".$ruta_foto."$datos_invitado_familiar[$nombre_foto]' width='100' height='120' >";
									   }else{
										 echo "<img src='assets/images/sinfoto.png' width='100' height='120' > ";
									   }
									?>
                                    &nbsp;
                                      </td>
                                        <td valign="top">

                                        <table class="table table-striped table-bordered table-hover">
                                        	<tr>
                                            	<td>&nbsp;
                                                	<?php echo $datos_invitado_familiar["Nombre"] . " " . $datos_invitado_familiar["Apellido"]  ?>
                                                </td>
                                            </tr>
                                            <!--
                                            <tr>
                                            	<td>&nbsp;
													 <?php
													$tipo_doc="";
													$tipo_doc = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'" );
													if(empty($tipo_doc)):
														echo "Documento";
													else:
														echo $tipo_doc;
													endif;
													?>
                                                    <?php echo $datos_invitado_familiar["NumeroDocumento"];  ?>
                                                </td>
                                            </tr>
                                            -->
                                            <tr>
                                            	<td>
                                                <br>
                                                <?php
                                                //ARL
															if(empty($datos_invitado["FechaVencimientoArl"])):
																echo '<span style="color: #F10004">Sin fecha ARL</span>';
															elseif(strtotime($datos_invitado["FechaVencimientoArl"])<strtotime(date("Y-m-d"))):
																echo '<span style="color: #F10004">ARL Vencido</span>';
															else:
																echo "<strong>ARL al dia</strong>";
															endif;
												?>
                                                &nbsp;Vehiculo:
                                                	<?php
					                                    //Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"):
															$condicion_vehiculo = " AND IDInvitado = '".$datos_invitado_familiar["IDInvitado"]."'";
														elseif($modulo == "Socio"):
															$condicion_vehiculo = " AND IDSocio = '".$datos_invitado_familiar["IDSocio"]."'";
														else:
															$condicion_vehiculo = " AND IDSocio = '-1'";
														endif;
														?>
                                                        <?php
														unset($array_placa);
														//datos vehiculo
														$sql_vehiculo = "Select * From Vehiculo Where 1 " . $condicion_vehiculo;
														$result_vehiculo = $dbo->query($sql_vehiculo);
                                                        $cont_vehiculo=0;
														while($row_vehiculo = $dbo->fetchArray($result_vehiculo)):
															$cont_vehiculo++;
															$array_placa[]=strtoupper($row_vehiculo["Placa"]);
															echo "Placa: " . strtoupper($row_vehiculo["Placa"]) . "<br>";
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

														endwhile;
														?>
                                                </td>
                                            </tr>
                                        </table>


                                        </td>
                                    </tr>
                                	<tr>
                                	  <td colspan="2">
                                      &nbsp;
                                                     <label>
                                                        	<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg" value="Peatonal"/>
                                                        	<span class="lbl">Peatonal</span>
                                                        </label>
                                                        <?php if(count($array_placa)>0):
															foreach($array_placa as $placa_vehiculo): ?>
                                                        <label>
                                                        	<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg" value="Vehiculo <?php echo $placa_vehiculo; ?>"/>
                                                        	<span class="lbl"><?php echo $placa_vehiculo; ?></span>
                                                        </label>
                                                        <?php
															endforeach;
														endif; ?>
                                                        <label>
                                                        	<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg" value="OtroVehiculo"/>
                                                        	 <span class="lbl">

                                                        <?php
														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"):
															$link_otro_vehiculo = "invitadosgeneral.php?action=edit&id=".$datos_invitado_familiar["IDInvitado"]."&editarinfo=n&tabinvitado=vehiculos";
														else:
															$link_otro_vehiculo = "socios.php?action=edit&id=".$datos_invitado_familiar["IDSocio"]."&editarinfo=n&tabsocio=vehiculos";
														endif;
														?>
                                                            <a  class="fancybox_vehiculo" href="<?php echo $link_otro_vehiculo?>" data-fancybox-type="iframe">
                                                                        <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                                        <span class="bigger-110">Agregar Veh&iacute;culo</span>
                                                            </a>


                                                        </span>
                                                        </label>
                                      </td>
                              	  </tr>
                                	<tr>
                                	  <td colspan="2">
                                      <?php
														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){
															if($datos_invitado["IDEstadoInvitado"]=="2" || $datos_invitado["IDEstadoInvitado"]=="3"){
																$bloqueado = "S";
																$mensaje_bloqueo = $datos_invitado["RazonBloqueo"];
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
															if($datos_invitado_familiar["IDEstadoSocio"]=="2" || $datos_invitado_familiar["IDEstadoSocio"]=="3")
																$bloqueado = "S";
																$mensaje_bloqueo = "por favor comuniquese con administracion";
														}

														//Verifico Cual fue el ultimo movimeinto registrado en el dia para saber si se activa la entrada o la salida
															//$sql_log_acceso_ultimo= "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc Limit 1";
															$sql_log_acceso_ultimo= "Select * From LogAccesDiario Where IDInvitacion = '".$id_registro."' Order by IDLogAcceso Desc Limit 1";
															$result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
															$row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
															$total_log = $dbo->rows($result_log_acceso_ultimo);
															if($row_log_acceso_ultimo["Entrada"]=="S"):
																$campo_entrada = "disabled";
															else:
																$campo_entrada = "";
															endif;

															if($row_log_acceso_ultimo["Salida"]=="S" || $total_log ==0 ):
																$campo_salida = "disabled";
															else:
																$campo_salida = "";
															endif;

															$campo_entrada = "";
															$campo_salida = "";

														 ?>
                                                         <label>
                                                          	<!--<input name="Ingreso" id="Ingreso" class="ace input-lg ace-checkbox-2 ingreso_acceso" type="checkbox" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>" <?php echo $campo_entrada; ?> />-->
												<input type="button" value="REGISTRAR SALIDA" name="salidaanterior" id="salidaanterior" onclick="window.location.href='salidainvitado.php?qryString=<?php echo $datos_invitado_familiar["NumeroDocumento"]; ?>&modulo=<?php echo $modulo ?>&action=salidaespecial'">
                                                            <!--<span class="lbl"><b>INGRESO</b></span>-->
                                                        </label>
																												<!--
																														<label>
                                                            <input name="Salida" id="Salida" class="ace input-lg ace-checkbox-2 salida_acceso" type="checkbox" alt="<?php echo $modulo; ?>" title="<?php echo $id_registro; ?>"  <?php echo $campo_salida; ?> />
                                                            <span class="lbl"><b>SALIDA</b></span>
                                                         </label>
																											 -->
                                      </td>
                              	  </tr>
                                  <?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){  ?>
                                	<tr>
                                	  <td colspan="2">
                                      Alertas:
                                              <?php
														//Consulto el historial de alertas
														$sql_observacion = "Select * From ObservacionInvitado Where IDInvitado = '".$datos_invitado_familiar["IDInvitado"]."' Order by IDObservacionInvitado Desc";
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
														$sql_log_acceso = "Select * From LogAccesoDiario Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc";
														$result_log_acceso = $dbo->query($sql_log_acceso);
														while($row_log_acceso = $dbo->fetchArray($result_log_acceso)):
															if($row_log_acceso["Entrada"]=="S"):
																echo "<br>Entrada: " . substr($row_log_acceso["FechaIngreso"],11);
															elseif($row_log_acceso["Salida"]=="S"):
																echo "<br>Salida: " . substr($row_log_acceso["FechaSalida"],11);
															endif;
														endwhile;

														?>




                                                        </td>
                               	  </tr>
                               	  </table>
                                    <?php
									if($nucleo_socio=="1"):
                                    	$modulo="Socio";
                                    	$id_registro = $datos_grupo_familiar["IDSocio"];
									else:
										$modulo="SocioInvitadoEspecial";
                                    	$id_registro = $datos_grupo_familiar["IDSocioInvitadoEspecial"];
									endif;
									?>


                           	  </td>
                                    <?php if ($contador_grupo=="3"):
                                    		echo "</tr><tr>";
											$contador_grupo="-1";
										endif;
									?>

									<?php endwhile;
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
