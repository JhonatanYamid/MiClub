<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO SOCIO
		</h4>

		
	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->
					

					<form class="form-horizontal formvalida" role="form" method="post" id="frmSocio" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
						
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de Socio </label>

										<div class="col-sm-8">
											
											<?
												echo SIMHTML::formPopupArray( SIMResources::$tipo_socio ,  $frm["TipoSocio"] , "TipoSocio" ,  "Seleccione tipo de socio" , "form-control"  );
											?>
											
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Número de Derecho </label>

										<div class="col-sm-8">
											<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory" title="número de derecho" value="<?php echo $frm["Accion"]; ?>" >
										</div>
								</div>
									
							</div>
						
							
							<div  class="form-group first hide contentAuxiliar contentBeneficiario">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Acción Padre </label>

										<div class="col-sm-8">
											<input type="text" id="AccionPadre" name="AccionPadre" placeholder="Accion Padre" class="col-xs-12 " title="acción padre" value="<?php echo $frm["AccionPadre"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Parentesco </label>

										<div class="col-sm-8">
											<input type="text" id="Parentesco" name="Parentesco" placeholder="Parentesco" class="col-xs-12 " title="parentesco" value="<?php echo $frm["Parentesco"]; ?>" >
										</div>
								</div>
									
							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="nombre" value="<?php echo $frm["Nombre"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1" > Apellido </label>

										<div class="col-sm-8">
											<input type="text" id="Apellido" name="Apellido" placeholder="Apellido" class="col-xs-12 mandatory" title="apellido" value="<?php echo $frm["Apellido"]; ?>" >
										</div>
								</div>
									
							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Número de Documento </label>

										<div class="col-sm-8">
											<input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="Número de Documento" class="col-xs-12 " title="número de documento" value="<?php echo $frm["NumeroDocumento"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Nacimiento </label>

										<div class="col-sm-8">
											<input type="text" id="FechaNacimiento" name="FechaNacimiento" placeholder="Fecha de Nacimiento" class="col-xs-12 calendar" title="fecha de nacimiento" value="<?php echo $frm["FechaNacimiento"]; ?>"  >
										</div>
								</div>
									
							</div>


							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>

										<div class="col-sm-8">
											<input type="text" id="Email" name="Email" placeholder="Email" class="col-xs-12 mandatory" title="email" value="<?php echo $frm["Email"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clave </label>

										<div class="col-sm-8">
											<input type="password" id="Clave" name="Clave" placeholder="Clave" class="col-xs-12" title="clave" value="<?=$frm[Clave] ?>" >
										</div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo Barras </label>

										<div class="col-sm-8">
											
                                            <? if (!empty($frm[CodigoBarras])) {
                          				      echo "<img src='".SOCIO_ROOT."$frm[CodigoBarras]'>";
                                			?>
                        <?
                            }// END if
                            ?>
                                            
                                            
										</div>
								</div>
								
									
							</div>
                            
                                 <div  class="form-group first ">
								
                                
                                
                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>

										<div class="col-sm-8">
											<? if (!empty($frm[Foto])) {
												echo "<img src='".SOCIO_ROOT."$frm[Foto]' width=55 >";
												?>	
                                            <a href="<? echo $script.".php?action=delfoto&foto=$frm[Foto]&campo=Foto&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											<?
											}// END if
											?>
											<input name="Foto" id=file class=""	title="Foto" type="file" size="25" style="font-size: 10px">
										</div>
								</div>

								
									
							</div>





							<div class="contentCortesia hide contentAuxiliar">
								<h4 class="blue ">
									<i class="ace-icon fa fa-check bigger-110"></i>
									Si es Cortesía
								</h4>

								<div  class="form-group first ">

									<div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Inicio</label>

											<div class="col-sm-8">
												<input type="text" id="FechaInicioCortesia" name="FechaInicioCortesia" placeholder="Fecha de Inicio Cortesía" class="col-xs-12 calendar" title="fecha de inicio cortesía" value="<?php echo $frm["FechaInicioCortesia"]; ?>" >
											</div>
									</div>

									<div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Fin </label>

											<div class="col-sm-8">
												<input type="text" id="FechaFinCortesia" name="FechaFinCortesia" placeholder="Fecha de Fin Cortesía" class="col-xs-12 calendar" title="fecha de fin cortesía" value="<?php echo $frm["FechaFinCortesia"]; ?>" >
											</div>
									</div>
										
								</div>
							</div>

							<div class="contentCanje hide contentAuxiliar">
								<h4 class="blue ">
									<i class="ace-icon fa fa-check bigger-110"></i>
									Si es Canje
								</h4>

								<div  class="form-group first ">

									<div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Inicio</label>

											<div class="col-sm-8">
												<input type="text" id="FechaInicioCanje" name="FechaInicioCanje" placeholder="Fecha de Inicio Canje" class="col-xs-12 calendar" title="fecha de inicio canje" value="<?php echo $frm["FechaInicioCanje"]; ?>" >
											</div>
									</div>

									<div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Fin </label>

											<div class="col-sm-8">
												<input type="text" id="FechaFinCanje" name="FechaFinCanje" placeholder="Fecha de Fin Canje" class="col-xs-12 calendar" title="fecha de fin canje" value="<?php echo $frm["FechaFinCanje"]; ?>" >
											</div>
									</div>
										
								</div>
							</div>




							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                	<input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />									
                                    <input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
                                    <input type="hidden" name="ClaveAnt" id="ClaveAnt" value="<?=$frm[Clave] ?>" />
                					<input type="hidden" name="EmailAnt" id="EmailAnt" value="<?=$frm[Email] ?>" />
                					<input type="hidden" name="NumeroDocumentoAnt" id="NumeroDocumentoAnt" value="<?=$frm[NumeroDocumento] ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frmSocio" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> Socio
									</button>

									
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