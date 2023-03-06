<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
						
                        <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-users green"></i>
                                    Datos Personales
                                </h3>
                            </div>
                            
                            
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

										<div class="col-sm-8">
                                        <select name = "IDTipoInvitado" id="IDTipoInvitado" title="Tipo Invitado" class="form-control mandatory">
                                        	<option value=""></option>
                                        <?php 
										$sql_tipoinv_club = "Select * From TipoInvitado Where IDClub = '".SIMUser::get("club")."' and Publicar = 'S'";
										$qry_tipoinv_club = $dbo->query($sql_tipoinv_club);
										while ($r_tipoinv = $dbo->fetchArray($qry_tipoinv_club)): ?>
											<option value="<?php echo $r_tipoinv["IDTipoInvitado"]; ?>" <?php if($r_tipoinv["IDTipoInvitado"]==$frm["IDTipoInvitado"]) echo "selected";  ?>><?php echo $r_tipoinv["Nombre"]; ?></option>
                                        <?php
										 	endwhile;    ?>
                                        </select>    
										</div>
								</div>		
                                
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clasificacion </label>

										<div class="col-sm-8">
                                        <select name = "IDClasificacionInvitado" id="IDClasificacionInvitado" class="form-control">
                                        	<option value="">Clasificacion</option>                                       
                                            <?php 
											if(!empty($frm["IDTipoInvitado"])):
												$sql_clasifinv_club = "Select * From ClasificacionInvitado Where IDTipoInvitado = '".$frm["IDTipoInvitado"]."'";
												$qry_clasifinv_club = $dbo->query($sql_clasifinv_club);
												while ($r_clasifinv = $dbo->fetchArray($qry_clasifinv_club)): ?>
													<option value="<?php echo $r_clasifinv["IDClasificacionInvitado"]; ?>" <?php if($r_clasifinv["IDClasificacionInvitado"]==$frm["IDClasificacionInvitado"]) echo "selected";  ?>><?php echo $r_clasifinv["Nombre"]; ?></option>
												<?php
												endwhile;    
											endif;	
											?>
											</select>    
										</div>
								</div>								
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo Documento </label>

										<div class="col-sm-8">
												<?php echo SIMHTML::formPopUp( "TipoDocumento" , "Nombre" , "Nombre" , "IDTipoDocumento" , $frm["IDTipoDocumento"] , "[Seleccione tipo documento]" , "form-control mandatory" , "title = \"Tipo Documento\"" )?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento </label>

										<div class="col-sm-8">
                                        <input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="Numero Documento" class="col-xs-12 mandatory" title="Numero Documento" value="<?php echo $frm["NumeroDocumento"]; ?>" >
                                        </div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apellido </label>

										<div class="col-sm-8">
                                        <input type="text" id="Apellido" name="Apellido" placeholder="Apellido" class="col-xs-12 mandatory" title="Apellido" value="<?php echo $frm["Apellido"]; ?>" >
                                        </div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Direccion </label>

										<div class="col-sm-8">
											<input type="text" id="Direccion" name="Direccion" placeholder="Direccion" class="col-xs-12 mandatory" title="Direccion" value="<?php echo $frm["Direccion"]; ?>" >
										</div>
								</div>
                                
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ciudad Residencia </label>

										<div class="col-sm-8">
											<input type="text" id="CiudadResidencia" name="CiudadResidencia" placeholder="CiudadResidencia" class="col-xs-12" title="Ciudad Residencia" value="<?php echo $frm["CiudadResidencia"]; ?>" >
										</div>
								</div>

								
									
							</div>
                            
                            
                            
                            <div  class="form-group first ">
                            
                            	<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono1 </label>

										<div class="col-sm-8">
                                        <input type="text" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 mandatory" title="Telefono" value="<?php echo $frm["Telefono"]; ?>" >
                                        </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono 2 </label>

										<div class="col-sm-8">
											<input type="text" id="Telefono2" name="Telefono2" placeholder="Telefono2" class="col-xs-12" title="Telefono2" value="<?php echo $frm["Telefono2"]; ?>" >
										</div>
								</div>

								
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email 1 </label>

										<div class="col-sm-8">
											<input type="text" id="Email" name="Email" placeholder="Email" class="col-xs-12 mandatory" title="Email" value="<?php echo $frm["Email"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email 2 </label>

										<div class="col-sm-8">
                                        <input type="text" id="Email2" name="Email2" placeholder="Email2" class="col-xs-12" title="Email2" value="<?php echo $frm["Email2"]; ?>" >
                                        </div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email 3 </label>

										<div class="col-sm-8">
											<input type="text" id="Email3" name="Email3" placeholder="Email3" class="col-xs-12" title="Email3" value="<?php echo $frm["Email3"]; ?>" >
										</div>
								</div>
                                
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Predio al que se dirige </label>

										<div class="col-sm-8">
											<input type="text" id="Predio" name="Predio" placeholder="Predio" class="col-xs-12 mandatory" title="Predio" value="<?php echo $frm["Predio"]; ?>" >
										</div>
								</div>

								
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo  </label>

										<div class="col-sm-8">
											<input type="text" id="Codigo" name="Codigo" placeholder="Codigo" class="col-xs-12" title="Codigo" value="<?php echo $frm["Codigo"]; ?>" >
										</div>
								</div>
                                
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio Contrato </label>

										<div class="col-sm-8">
											<input type="text" id="FechaContrato" name="FechaContrato" placeholder="Fecha Inicio Contrato" class="col-xs-12 calendar" title="Fecha Inicio Contrato" value="<?php echo $frm["FechaContrato"] ?>" >
										</div>
								</div>

								
									
							</div>
                            
                           <!-- 
                          <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-credit-card green"></i>
                                   Licencia Conducci&oacute;n
                                </h3>
                            </div>

                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Licencia de Conduccion </label>

										<div class="col-sm-8">
                                        <input type="text" id="LicenciaConduccion" name="LicenciaConduccion" placeholder="LicenciaConduccion" class="col-xs-12" title="LicenciaConduccion" value="<?php echo $frm["LicenciaConduccion"]; ?>" >
                                        </div>
								</div>
                                
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Expedicion </label>

										<div class="col-sm-8">											
                                            <input type="text" id="FechaExpedicion" name="FechaExpedicion" placeholder="Fecha Expedicion" class="col-xs-12 calendar" title="Fecha Expedicion" value="<?php echo $frm["FechaExpedicion"] ?>" >
										</div>
								</div>

								
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> FechaVencimiento </label>

										<div class="col-sm-8">
                                        <input type="text" id="FechaVencimiento" name="FechaVencimiento" placeholder="Fecha Vencimiento" class="col-xs-12 calendar" title="Fecha Vencimiento" value="<?php echo $frm["FechaVencimiento"] ?>" >
                                        </div>
								</div>

								
									
							</div>
                            
                            -->
                            
                             <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-bell green"></i>
                                   Datos Emergencia
                                </h3>
                            </div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Emergencia </label>

										<div class="col-sm-8">
											<input type="text" id="NombreEmergencia" name="NombreEmergencia" placeholder="NombreEmergencia" class="col-xs-12" title="NombreEmergencia" value="<?php echo $frm["NombreEmergencia"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apellido Emergencia </label>

										<div class="col-sm-8">
                                        <input type="text" id="ApellidoEmergencia" name="ApellidoEmergencia" placeholder="Apellido Emergencia" class="col-xs-12" title="Apellido Emergencia" value="<?php echo $frm["ApellidoEmergencia"]; ?>" >
                                        </div>
								</div>
									
							</div>
                            
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Numero Documento Emergencia </label>

										<div class="col-sm-8">
											<input type="text" id="NumeroDocumentoEmergencia" name="NumeroDocumentoEmergencia" placeholder="Numero Documento Emergencia" class="col-xs-12" title="NumeroDocumentoEmergencia" value="<?php echo $frm["NumeroDocumentoEmergencia"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Direccion Emergencia </label>

										<div class="col-sm-8">
                                        <input type="text" id="DireccionEmergencia" name="DireccionEmergencia" placeholder="Direccion Emergencia" class="col-xs-12" title="Direccion Emergencia" value="<?php echo $frm["DireccionEmergencia"]; ?>" >
                                        </div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono Emergencia </label>

										<div class="col-sm-8">
                                        <input type="text" id="TelefonoEmergencia" name="TelefonoEmergencia" placeholder="Telefono Emergencia" class="col-xs-12" title="Telefono Emergencia" value="<?php echo $frm["TelefonoEmergencia"]; ?>" >
                                        </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Emergencia </label>

										<div class="col-sm-8">
                                        <input type="text" id="EmailEmergencia" name="EmailEmergencia" placeholder="Email Emergencia" class="col-xs-12" title="Email Emergencia" value="<?php echo $frm["EmailEmergencia"]; ?>" >
                                        </div>
								</div>
									
							</div>
                            
                            
                             <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-plus-circle green"></i>
                                   Seguridad Social
                                </h3>
                            </div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> ARL </label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formPopUp( "Arl" , "Nombre" , "Nombre" , "IDArl" , $frm["IDArl"] , "[Seleccione]" , "form-control" , "title = \"ARL\"" )?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Vencimiento ARL </label>

										<div class="col-sm-8">
	                                            <input type="text" id="FechaVencimientoArl" name="FechaVencimientoArl" placeholder="Fecha Vencimiento Arl" class="col-xs-12 calendar" title="Fecha Vencimiento Arl" value="<?php echo $frm["FechaVencimientoArl"] ?>" >
										</div>
								</div>
									
							</div>
                            
                             <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> AFP </label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formPopUp( "Afp" , "Nombre" , "Nombre" , "IDAfp" , $frm["IDAfp"] , "[Seleccione]" , "form-control" , "title = \"AFP\"" )?>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> EPS </label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formPopUp( "Eps" , "Nombre" , "Nombre" , "IDEps" , $frm["IDEps"] , "[Seleccione]" , "form-control" , "title = \"EPS\"" )?>
										</div>
								</div>
									
							</div>
                            
                           
                            
                            
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa  fa-comments green"></i>
                                  Observaciones
                                </h3>
                            </div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observacion General </label>

										<div class="col-sm-8">
                                        <textarea id="ObservacionGeneral" name="ObservacionGeneral" cols="10" rows="5" class="col-xs-12 " title="ObservacionGeneral"><?php echo $frm["ObservacionGeneral"]; ?></textarea>                                        
                                        </div>
								</div>
								
                                <!--
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observacion Especial </label>

										<div class="col-sm-8">
                                        <textarea id="ObservacionEspecial" name="ObservacionEspecial" cols="10" rows="5" class="col-xs-12 " title="ObservacionEspecial"><?php echo $frm["ObservacionEspecial"]; ?></textarea>                                        
                                        </div>
								</div>
                                -->
									
							</div>
                           
                            
                            <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-info-circle green"></i>
                                  Otros Datos
                                </h3>
                            </div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Nacimiento </label>

										<div class="col-sm-8">
	                                            <input type="text" id="FechaNacimiento" name="FechaNacimiento" placeholder="Fecha Nacimiento" class="col-xs-12 calendar" title="Fecha Nacimiento" value="<?php echo $frm["FechaNacimiento"] ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Lugar Nacimiento </label>

										<div class="col-sm-8">
                                        <input type="text" id="LugarNacimiento" name="LugarNacimiento" placeholder="LugarNacimiento" class="col-xs-12" title="Lugar Nacimiento" value="<?php echo $frm["LugarNacimiento"]; ?>" >
                                        </div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Expedicion Documento </label>

										<div class="col-sm-8">
	                                            <input type="text" id="FechaExpedicionDocumento" name="FechaExpedicionDocumento" placeholder="Fecha Expedicion Documento" class="col-xs-12 calendar" title="Fecha Expedicion Documento" value="<?php echo $frm["FechaExpedicionDocumento"] ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estatura </label>

										<div class="col-sm-8">
                                        <input type="text" id="Estatura" name="Estatura" placeholder="Estatura" class="col-xs-12" title="Estatura" value="<?php echo $frm["Estatura"]; ?>" >
                                        </div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> GrupoSanguineo </label>

										<div class="col-sm-8">
											<input type="text" id="GrupoSanguineo" name="GrupoSanguineo" placeholder="Grupo Sanguineo" class="col-xs-12" title="Grupo Sanguineo" value="<?php echo $frm["GrupoSanguineo"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>

										<div class="col-sm-8">
											<?php 
											if($frm["FotoFile"])
											{
											?>
											<img alt="<?php echo $frm["FotoFile"] ?>" src="<?php echo IMGINVITADO_ROOT.$frm["FotoFile"]?>" width="100px"> 
				                            <a href="<? echo $script.".php?action=DelImgNot&cam=FotoFile&id=" .$frm[ $key ]?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											<?php 
													}
													else
													{
													?>
											<input type="file" name="FotoImagen" id="FotoImagen" class="popup" title="Foto Imagen">
											<?php 
													}
													?>
										</div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Estado </label>

										<div class="col-sm-8">
                                        <?php echo SIMHTML::formPopUp( "EstadoInvitado" , "Nombre" , "Nombre" , "IDEstadoInvitado" , $frm["IDEstadoInvitado"] , "[Seleccione el Estado]" , "form-control mandatory" , "title = \"Estado\"" )?>
                                        </div>
								</div>
									
							</div>
                            
                           
                            <?php if($frm["IDEstadoInvitado"]!="3")
										$oculta_razon = "style='display:none'";
										
								   else
									   	$oculta_razon = "";
							 ?>
							
							<div <?php echo $oculta_razon; ?> id="divrazonbloqueo">
                               <div  class="form-group first ">
    
                                    
                                    <div  class="col-xs-12 col-sm-6">
                                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Razón Bloqueo </label>
    
                                            <div class="col-sm-8">
                                            <textarea id="RazonBloqueo" name="RazonBloqueo" cols="10" rows="5" class="col-xs-12 " title="Razon Bloqueo"><?php echo $frm["RazonBloqueo"]; ?></textarea>
                                            </div>
                                    </div>
                                        
                                </div>
                            </div>
                            

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
									</button>

									
								</div>
							</div>

					</form>