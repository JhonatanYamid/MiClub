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

                        <b>Tipo:</b> <?php echo $datos_invitacion["TipoInvitacion"]; ?>
                        <b>Fecha Inicio Aut:</b> <?php echo $datos_invitacion["FechaInicio"]; ?>
                        <b>Fecha Fin Aut:</b> <?php echo $datos_invitacion["FechaFin"]; ?>
                        <b>Predio:</b> <?php echo $datos_socio["Predio"]; ?>
                        <b>Accion: </b><?php echo $datos_socio["Accion"]; ?>
                        <b>Socio:</b> <?php echo $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?>


		</div>

		<div class="widget-body">
			<div class="widget-main padding-4">
				<div class="row">
					<div class="col-xs-12">
						<div id="jqGrid_container">
						<table id="simple-table" class="table table-striped table-bordered table-hover">
                        	<tr>
                            	<td>
                                <table class="table table-striped table-bordered table-hover" style="font-size:40px">
                                	<tr>
                                   	  <td valign="top" width="100">
											<?
                                            if($modulo=="Socio"):
												$SOCIO_ROOTREDIMENSION=URLROOT . "file/sociofotoredimension/";

                                                //$ruta_foto = SOCIO_ROOT;
												$ruta_foto = $SOCIO_ROOTREDIMENSION;
												if(!file_exists(URLDIR."/file/sociofotoredimension/".$datos_invitado[$nombre_foto])){
													$SOCIO_ROOTREDIMENSION=URLROOT . "file/socio/";
													$ruta_foto = $SOCIO_ROOTREDIMENSION;
												}




                                                $nombre_foto = "Foto";
												$identificador=$datos_invitado["IDSocio"];
	                                            else:
                                                $ruta_foto = IMGINVITADO_ROOT;
                                                $nombre_foto = "FotoFile";
												$identificador=$datos_invitado["IDInvitado"];
                                            endif;

                                            if (!empty($datos_invitado[$nombre_foto])) {
                                                 echo "<img src='".$ruta_foto."$datos_invitado[$nombre_foto]' width='340px' height='350px'   >";
                                               }else{
                                                 echo "<img src='assets/images/sinfoto.png' width='100' height='120'> ";
                                               }
                                            ?>
                                            <a  class="fancybox" href="../admin/tomarfoto/webcamjquery/index.php?action=foto&IDRegistro=<?php echo $identificador; ?>&Modulo=<?php echo $modulo; ?>" data-fancybox-type="iframe">


                                            	 </a>
											<?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){ ?>

                                             <a  class="fancybox" href="invitadosgeneral.php?action=edit&id=<?=$datos_invitado["IDInvitado"]?>" data-fancybox-type="iframe">
                                                                    <i class="ace-icon fa fa-plus-circle bigger-120"></i>
                                                                    <span class="bigger-110">Editar info</span>
                                            </a>
                                            <?php } ?>
                                      </td>
                                        <td valign="top">

                                        <table class="table table-striped table-bordered table-hover">
                                        	<tr>
                                            	<td align="center">&nbsp;
                                                	BIENVENIDO(A),
                                                </td>
                                            </tr>
                                            <!--
                                            <tr>
                                            	<td align="center">&nbsp;
													<?php echo substr($datos_invitado["Accion"],0,5) . " - " .$datos_invitado["Predio"] . " - " . $datos_invitado["TipoSocio"];  ?>

                                                </td>
                                            </tr>
                                            -->
                                            <tr>
                                            	<td align="center">
                                                <?php echo utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"])  ?>
																								<br>
																								<b><?php echo $datos_socio["Predio"]; ?>
												                        <br><b>Accion: </b><?php echo $datos_socio["AccionPadre"] . " " . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"]; ?>


                                                &nbsp;
                                                	<?php
					                                    //Valido si tiene licencia vigente, soat y tecnomecanica si es un invitado
														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"):
															$condicion_vehiculo = " AND IDInvitado = '".$datos_invitado["IDInvitado"]."'";
														elseif($modulo == "Socio"):
															$condicion_vehiculo = " AND IDSocio = '".$datos_invitado["IDSocio"]."'";
														else:
															$condicion_vehiculo = " AND IDSocio = '-1'";
														endif;

														$registra_acceso="S";

														$text_comida="";
														if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){

																if(!empty($datos_invitacion["CodigoAutorizacion"])){
																		$array_comidas=explode(",",trim($datos_invitacion["CodigoAutorizacion"]));
																}
																if(count($array_comidas)>0){
																		$hora_actual=date("H:i:s");
																		echo "<span style='color:#000000'><br>".$datos_invitado["Telefono"]."</span>";
																		if($hora_actual<="11:30:00" && in_array("D",$array_comidas)){
																				//Verifico si ya tomo esta comida
																				$fecha_hora_comida=date("Y-m-d"). " " . "11:30:00";
																				$AccesoComida=$dbo->getFields( "LogAccesoDiario" , "IDLogAcceso" , "IDClub = '".SIMUser::get("club")."' and IDInvitacion = '".$id_registro."' and Tipo = 'Contratista' and ( (FechaIngreso <= '".$fecha_hora_comida."' and Entrada='S') or (FechaSalida <= '".$fecha_hora_comida."' and Salida='S')  )" );
																				if(empty($AccesoComida)){
																					$text_comida="<span style='color:#f37c20'><br>DESAYUNO</span>";
																				}
																				else{
																					$text_comida="<span style='color:#fe0000'><br>YA SE REGISTRO EL DESAYUNO</span>";
																					$registra_acceso="N";
																				}
																		}
																		else{
																			$text_comida="<span style='color:#fe0000'><br>NO DESAYUNO</span>";
																		}

																		if($hora_actual>"11:31:00" && $hora_actual<="17:30:00"){
																			if(in_array("A",$array_comidas)){
																				//Verifico si ya tomo esta comida
																				$fecha_hora_comida=date("Y-m-d"). " " . "17:30:00";
																				$fecha_hora_inicio_comida=date("Y-m-d"). " " . "11:31:00";

																				$AccesoComida=$dbo->getFields( "LogAccesoDiario" , "IDLogAcceso" , "IDClub = '".SIMUser::get("club")."' and IDInvitacion = '".$id_registro."' and Tipo = 'Contratista' and ( (FechaIngreso >= '".$fecha_hora_inicio_comida."' and  FechaIngreso <= '".$fecha_hora_comida."' and Entrada='S') or (FechaSalida >= '".$fecha_hora_inicio_comida."' and  FechaSalida <= '".$fecha_hora_comida."' and Salida='S') ) " );
																				if(empty($AccesoComida)){
																					$text_comida="<span style='color:#f37c20'><br>ALMUERZO</span>";
																				}
																				else{
																					$text_comida="<span style='color:#fe0000'><br>YA SE REGISTRO EL ALMUERZO</span>";
																					$registra_acceso="N";
																				}
																			}
																			else{
																				$text_comida="<span style='color:#fe0000'><br>NO ALMUERZO</span>";
																			}
																		}


																		if($hora_actual>"17:31:00"){

																			if(in_array("C",$array_comidas)){
																				//Verifico si ya tomo esta comida
																				$fecha_hora_comida=date("Y-m-d"). " " . "21:00:00";
																				$fecha_hora_inicio_comida=date("Y-m-d"). " " . "17:31:00";
																				$AccesoComida=$dbo->getFields( "LogAccesoDiario" , "IDLogAcceso" , "IDClub = '".SIMUser::get("club")."' and IDInvitacion = '".$id_registro."' and Tipo = 'Contratista' and ( (FechaIngreso >= '".$fecha_hora_inicio_comida."' and  FechaIngreso <= '".$fecha_hora_comida."' and Entrada='S') or (FechaSalida >= '".$fecha_hora_inicio_comida."' and  FechaSalida <= '".$fecha_hora_comida."' and Salida='S') ) " );
																				if(empty($AccesoComida)){
																					$text_comida="<span style='color:#fe0000'><br>CENA</span>";
																				}
																				else{
																					$text_comida="<span style='color:#fe0000'><br>YA SE REGISTRO LA CENA $hora_actual </span>";
																					$registra_acceso="N";
																				}
																		}
																		else{
																			$text_comida="<span style='color:#fe0000'><br>NO CENA $hora_actual </span>";
																		}
																	}

																		$condicion_vehiculo=" and IDVehiculo=0";

																}
																echo $text_comida;

														}
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
														 ?>
                                                        <label>
                                                        	<?php if($bloqueado<>"S"): ?>

                                                            <?php
                                                            //Verifico Cual fue el ultimo movimeinto registrado en el dia para saber si se activa la entrada o la salida
															//$sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc Limit 1";
															$sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' Order by IDLogAcceso Desc Limit 1";
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
															?>


                                                             <input type="hidden" name="ModuloAcceso" id="ModuloAcceso" value="<?php echo $modulo; ?>" >
                                                             <input type="hidden" name="IdentificadorAcceso" id="IdentificadorAcceso" value="<?php echo $id_registro; ?>" >



                                                            <?php  else: ?>
																<span style="color: #F10004; font-size:24px;">BLOQUEADO</span> <?php echo $mensaje_bloqueo; ?>
															<?php endif; ?>
                                                         </label>
                                      </td>
                              	  </tr>
                                  	<?php if ($modulo == "SocioAutorizacion" || $modulo == "SocioInvitadoEspecial"){  ?>
                                	<tr>
                                	  <td colspan="2">Alertas:
                                      <?php
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
                                	  <td colspan="2" style="font-size:18px; font-weight:bold; text-align:center">Entradas/Salidas registradas: <?php echo date("Y-m-d"); ?>:
                               	      <?php
														//Consulto el historial de entradas y salidas del dia
														$sql_log_acceso = "Select * From LogAcceso Where IDInvitacion = '".$id_registro."' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '".date("Y-m-d")."' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '".date("Y-m-d")."') Order by IDLogAcceso Desc Limit 1";
														$result_log_acceso = $dbo->query($sql_log_acceso);
														while($row_log_acceso = $dbo->fetchArray($result_log_acceso)):
															if($row_log_acceso["Entrada"]=="S"):
																echo "Entrada: " . substr($row_log_acceso["FechaIngreso"],11);
															elseif($row_log_acceso["Salida"]=="S"):
																echo "Salida: " . substr($row_log_acceso["FechaSalida"],11) ;
															endif;
														endwhile;
														?></td>
                               	  </tr>
                               	  </table>
                              </td>
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
    <?
	else: ?>

   						 <table id="simple-table" class="table table-striped table-bordered table-hover">
                        	<tr>
                            	<td align="center">
                                 <img class="boxlogo" src="<?php echo $logo_club; ?>" />
                                </td>
                            </tr>
                          </table>



	<?php endif; ?>

		<?
			include( "cmp/footer_grid.php" );
		?>

         <script>
		 	<?php if($bloqueado<>"S" && !empty($_GET["qryString"])): ?>
				<?php if($row_log_acceso_ultimo["Entrada"]=="S" && $registra_acceso=="S"): ?>
					salida_automatico(<?php $text_comida?>);
					setTimeout("location.href='accesoinvitadoaut.php'", 12000);
				<?php elseif($registra_acceso=="S"): ?>
					ingreso_automatico();
					setTimeout("location.href='accesoinvitadoaut.php'", 12000);
					<?php else: ?>
						setTimeout("location.href='accesoinvitadoaut.php'", 8000);
				<?php endif; ?>
			<?php endif; ?>

		</script>
