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
					

					<form class="form-horizontal formvalida" role="form" method="post" id="frmSocio" action="<?php echo SIMUtil::lastURI()?>">
						
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo de Socio </label>

										<div class="col-sm-8">
											
											<?
												echo SIMHTML::formPopupArray( SIMResources::$tipo_socio , "" , "TipoSocio" ,  "Seleccione tipo de socio" , "form-control"  );
											?>
											
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Número de Derecho </label>

										<div class="col-sm-8">
											<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory" title="número de derecho" >
										</div>
								</div>
									
							</div>
						
							
							<div  class="form-group first hide contentAuxiliar contentBeneficiario">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Acción Padre </label>

										<div class="col-sm-8">
											<input type="text" id="AccionPadre" name="AccionPadre" placeholder="Accion Padre" class="col-xs-12 " title="acción padre" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Parentesco </label>

										<div class="col-sm-8">
											<input type="text" id="Parentesco" name="Parentesco" placeholder="Parentesco" class="col-xs-12 " title="parentesco" >
										</div>
								</div>
									
							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="nombre" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Apellido </label>

										<div class="col-sm-8">
											<input type="text" id="Apellido" name="Apellido" placeholder="Apellido" class="col-xs-12 mandatory" title="apellido" >
										</div>
								</div>
									
							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Número de Documento </label>

										<div class="col-sm-8">
											<input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="Número de Documento" class="col-xs-12 " title="número de documento" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Nacimiento </label>

										<div class="col-sm-8">
											<input type="text" id="FechaNacimiento" name="FechaNacimiento" placeholder="Fecha de Nacimiento" class="col-xs-12 calendar" title="fecha de nacimiento" >
										</div>
								</div>
									
							</div>


							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>

										<div class="col-sm-8">
											<input type="text" id="Email" name="Email" placeholder="Email" class="col-xs-12 mandatory" title="email" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clave </label>

										<div class="col-sm-8">
											<input type="password" id="Clave" name="Clave" placeholder="Clave" class="col-xs-12 mandatory" title="clave" >
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
												<input type="text" id="FechaInicioCortesia" name="FechaInicioCortesia" placeholder="Fecha de Inicio Cortesía" class="col-xs-12 calendar" title="fecha de inicio cortesía" >
											</div>
									</div>

									<div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Fin </label>

											<div class="col-sm-8">
												<input type="text" id="FechaFinCortesia" name="FechaFinCortesia" placeholder="Fecha de Fin Cortesía" class="col-xs-12 calendar" title="fecha de fin cortesía" >
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
												<input type="text" id="FechaInicioCanje" name="FechaInicioCanje" placeholder="Fecha de Inicio Canje" class="col-xs-12 calendar" title="fecha de inicio canje" >
											</div>
									</div>

									<div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha de Fin </label>

											<div class="col-sm-8">
												<input type="text" id="FechaFinCanje" name="FechaFinCanje" placeholder="Fecha de Fin Canje" class="col-xs-12 calendar" title="fecha de fin canje" >
											</div>
									</div>
										
								</div>
							</div>




							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                	<input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" value="insert">
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frmSocio" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										Crear Socio
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