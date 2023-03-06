<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?php echo strtoupper(SIMReg::get( "title" ))?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->
				<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
						
                        		
							
                            <div  class="form-group first ">
								
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Padre</label>

										<div class="col-sm-8">
											<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocompletepadre-ajax" title="número de derecho" <?php if($newmode=="updateingreso") echo "readonly"; ?> value="<?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$frm["IDSocio"]."'" ) . " ".$dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$frm["IDSocio"]."'" ) ?>" >
											<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
										</div>
								</div>
                                
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Ingreso</label>

										<div class="col-sm-8">
										  <input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Ingreso" class="col-xs-12 <?php if($newmode!="updateingreso") echo "calendariohoy"; ?> " title="Fecha Ingreso" value="<?php  if($frm["FechaInicio"]=="0000-00-00" || $frm["FechaInicio"]=="" ) echo date("Y-m-d"); else echo $frm["FechaInicio"]; ?>" <?php if($newmode=="updateingreso") echo "readonly"; ?>>
										</div>
								</div>
									
							</div>
                            <div  class="form-group first ">
                               
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Ingreso</label>

										<div class="col-sm-8">
										  <input type="time" name="HoraInicio" id="HoraInicio" class="input" title="Hora Inicio" value="<?php echo $frm["HoraInicio"]; ?>">
										</div>
								</div> 
                               
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin</label>

										<div class="col-sm-8">
										  <input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar " title="Fecha Fin" value="<?php  if($frm["FechaFin"]=="0000-00-00" || $frm["FechaFin"]=="" ) echo date("Y-m-d"); else echo $frm["FechaFin"]; ?>" <?php if($newmode=="updateingreso") echo "readonly"; ?>>
										</div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">
                               
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Salida</label>

										<div class="col-sm-8">
										  <input type="time" name="HoraSalida" id="HoraSalida" class="input" title="Hora Salida" value="<?php echo $frm["HoraFin"]; ?>">
										</div>
								</div> 
                                
                                 <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estudiante:</label>

										<div class="col-sm-8">
										  <select name="IDSocioSalida" id="IDSocioSalida" class="form-control mandatory" title="Estudiante">
                                          	<option value="">Seleccione Estudiante</option>
                                            <?php if(!empty($frm["IDSocioSalida"])):?>
                                            	<option value="<?php echo $frm["IDSocioSalida"]; ?>" selected><?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$frm["IDSocioSalida"]."'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$frm["IDSocioSalida"]."'" ); ?></option>
                                            <?php endif; ?>
                                            
                                          </select>
										</div>
								</div> 
                               
                               
									
							</div>
                            
                             <div  class="form-group first ">
                               
                                <div  class="col-xs-12 col-sm-12">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Dias</label>

										<div class="col-sm-8">
										  <?php												 
										  if(!empty($frm["Dias"])):
                                            	$array_dias=explode("|",$frm["Dias"]);
                                          endif; 					
											array_pop($array_dias);											
											foreach($Dia_array as $id_dia => $dia):  ?>
												<input type="checkbox" name="IDDia[]" id="IDDia" value="<?php echo $id_dia; ?>" <?php if(in_array($id_dia,$array_dias) && $dia!="") echo "checked"; ?>><?php echo $dia; ?>
											<?php endforeach; ?>
										</div>
								</div> 
                                
                               
                               
                               
									
							</div>
                            
                            
                            
                            
                            <?php 
							$sql_tipodoc = $dbo->query("Select * From TipoDocumento Where Publicar = 'S'");
							while($row_tipo_doc = $dbo->fetchArray($sql_tipodoc)):
								$array_tipo_doc[$row_tipo_doc["IDTipoDocumento"]]=$row_tipo_doc["Nombre"];
							endwhile;
							
							
							if(SIMNet::req( "action" )=="editinfo")
								$total_caja_invitado = 1;
							else	
								$total_caja_invitado = 3;
							
							for($cont_invitado=1;$cont_invitado<=$total_caja_invitado;$cont_invitado++): 
								unset($datos_invitado_edit);
								unset($datos_placa_edit);
								if($cont_invitado==1):
									$IDInvitadoEdit = $frm["IDInvitado"];
									if(!empty($IDInvitadoEdit)):
										$datos_invitado_edit = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitadoEdit . "' ", "array" );										
										$datos_invitado_edit["TipoAutorizacion"] = $frm["TipoAutorizacion"];
										$datos_placa_edit = $dbo->getFields( "Vehiculo" , "Placa" , "IDVehiculo = '".$frm["IDVehiculo"]."'" );
									endif;
								endif; ?>
                                
                            <div class="col-sm-12">
										<div class="widget-box">
											<div class="widget-header">
												<h4 class="smaller">
													Autorizado <?php  echo $cont_invitado; ?>													
												</h4>
											</div>

											<div class="widget-body">
												<div class="widget-main">
													<p class="muted">
                                                    
                                                    <div  class="form-group first ">
                                                    
                                			                     <div  class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Documento </label>
                                
                                                                        <div class="col-sm-8">                                        
                                                                        <input id="NumeroDocumento<?php echo $cont_invitado; ?>" type="text" size="25" title="Numero Documento" name="NumeroDocumento<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input autocomplete-ajax_tblinvitado txtPistola" value="<?php if(!empty($datos_invitado_edit["NumeroDocumento"])){ echo $datos_invitado_edit["NumeroDocumento"]; } ?>" />                                                                               
                                                                        <input type="hidden" name="IDInvitado<?php echo $cont_invitado; ?>" value="<?php echo $frm["IDInvitado"]; ?>" id="IDInvitado<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" title="Numero Documento">
                                    
                                                                        </div>
                                                                </div>
                                                    
                                                               <div  class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
                                
                                                                        <div class="col-sm-8">
                                                                          <input id="Nombre<?php echo $cont_invitado; ?>" type="text" size="25" title="Nombre" name="Nombre<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input " value="<?php if(!empty($datos_invitado_edit["Nombre"])){ echo $datos_invitado_edit["Nombre"]; } ?>"  />
                                                                        </div>
                                                                </div>
                                                       </div>
                                                            
                                                             <div  class="form-group first ">
                                                                
                                                                 <div  class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Lugar al que se dirige </label>
                                
                                                                        <div class="col-sm-8">
                                                                          <input id="Predio<?php echo $cont_invitado; ?>" type="text" size="25" title="Predio" name="Predio<?php echo $cont_invitado; ?>" alt="<?php echo $cont_invitado; ?>" class="input" value="<?php if(!empty($datos_invitado_edit["Predio"])){ echo $datos_invitado_edit["Predio"]; } ?>" />
                                                                        </div>
                                                                </div>
                                                                
                                                                 <div  class="col-xs-12 col-sm-6">
                                                                        <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observacion</label>
                                
                                                                        <div class="col-sm-8">
                                                                          <textarea id="Observaciones<?php echo $cont_invitado; ?>" rows="4"  title="Observaciones" name="Observaciones<?php echo $cont_invitado; ?>" class="form-control" /><?php echo $datos_invitado_edit["ObservacionGeneral"]; ?></textarea>
                                                                        </div>
                                                                </div>
                                                                    
                                                            </div>
													</p>
												</div>
											</div>
										</div>
									</div><!-- /.col -->
                            
                             <div  class="form-group first ">
									
							</div>
                            
							<?php 
							endfor; 
							?>
                            <?php 
							if($newmode=="updateobservacion"): ?>
                            <div  class="form-group first">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones</label>

										<div class="col-sm-8">
										  <textarea id="Observaciones" rows="4"  title="Observaciones" name="Observaciones" class="form-control" /><?php echo $frm["Observaciones"] ?></textarea>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha/Hora Ingreso</label>

										<div class="col-sm-8">
										  <input type="text" id="FechaInicioClub" name="FechaInicioClub" placeholder="Fecha Ingreso Club" class="col-xs-12" title="Fecha Ingreso Club" value="<?php if($newmode=="updateingreso"): echo date("Y-m-d H:i:s"); else: echo ""; endif; ?>" readonly >
										</div>
								</div>
									
							</div>
                            <?php endif; ?>
                            



							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
									 <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
                                    <input type="hidden" name="NumeroInvitados" id="NumeroInvitados" value="<?php echo $cont_invitado;  ?>" />
                                    
									<button class="btn btn-info btnEnviar" type="button" rel="frm" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>
									<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[ $key ] ?>" />
                                    <input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[ $key ] ?>" />
									
								</div>
							</div>
					</form>
				</div>
			</div>

			


		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>

<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>