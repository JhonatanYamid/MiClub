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


					<form class="form-horizontal formvalida" role="form" method="post" id="frm" name="frm" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">


						<div  class="form-group first ">
							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Sede </label>

									<div class="col-sm-8">
											<?php echo SIMHTML::formPopUp( "CursoSede" , "Nombre" , "Nombre" , "IDCursoSede" , $frm["IDCursoSede"] , "[Seleccione]" , "form-control" , "title = \"Sede\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
									</div>
							</div>

							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Curso tipo </label>

									<div class="col-sm-8">
											<?php echo SIMHTML::formPopUp( "CursoTipo" , "Nombre" , "Nombre" , "IDCursoTipo" , $frm["IDCursoTipo"] , "[Seleccione]" , "form-control" , "title = \"Tipo\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
									</div>
							</div>


						</div>

							<div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Entrenador </label>

										<div class="col-sm-8">
                        <?php echo SIMHTML::formPopUp( "CursoEntrenador" , "Nombre" , "Nombre" , "IDCursoEntrenador" , $frm["IDCursoEntrenador"] , "[Seleccione]" , "form-control" , "title = \"Entrenador\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
                    </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cupo </label>

										<div class="col-sm-8">
                          <input type="text" id="Cupo" name="Cupo" placeholder="Cupo" class="col-xs-12 mandatory" title="Cupo" value="<?php echo $frm["Cupo"]; ?>" >
                  	</div>
								</div>
							</div>

							<div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Edad </label>

										<div class="col-sm-8">
                        <?php echo SIMHTML::formPopUp( "CursoEdad" , "Nombre" , "Nombre" , "IDCursoEdad" , $frm["IDCursoEdad"] , "[Seleccione]" , "form-control" , "title = \"Edad\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
                    </div>
								</div>

                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nivel </label>

										<div class="col-sm-8">
										  <?php echo SIMHTML::formPopUp( "CursoNivel" , "Nombre" , "Nombre" , "IDCursoNivel" , $frm["IDCursoNivel"] , "[Seleccione]" , "form-control" , "title = \"Nivel\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
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
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>

										<div class="col-sm-8">
										  <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
										</div>
								</div>

							</div>

							<div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Mes Web </label>

										<div class="col-sm-8">
												<input type="text" id="ValorMes" name="ValorMes" placeholder="Valor Mes" class="col-xs-12 mandatory" title="Valor Mes" value="<?php echo $frm["ValorMes"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Mes Presencial </label>

										<div class="col-sm-8">
												<input type="text" id="ValorMesPresencial" name="ValorMesPresencial" placeholder="Valor Mes Presencial" class="col-xs-12 mandatory" title="Valor Mes Presencial" value="<?php echo $frm["ValorMesPresencial"]; ?>" >
										</div>
								</div>
							</div>

							<div  class="form-group first ">


								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Valor Trimestre </label>

										<div class="col-sm-8">
												<input type="text" id="ValorTrimestre" name="ValorTrimestre" placeholder="Valor Trimestre" class="col-xs-12 mandatory" title="Valor Trimestre" value="<?php echo $frm["ValorTrimestre"]; ?>" >
										</div>
								</div>
							</div>

							<div  class="form-group first ">
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Desde </label>

										<div class="col-sm-8">
												<input type="time" id="HoraDesde" name="HoraDesde" placeholder="Hora Desde" class="col-xs-12 mandatory" title="Hora Desde" value="<?php echo $frm["HoraDesde"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Hora Hasta </label>

										<div class="col-sm-8">
												<input type="time" id="HoraHasta" name="HoraHasta" placeholder="Hora Hasta" class="col-xs-12 mandatory" title="Hora Hasta" value="<?php echo $frm["HoraHasta"]; ?>" >
										</div>
								</div>
							</div>


                  <div  class="form-group first ">
									<div  class="col-xs-12 col-sm-6">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

											<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?></div>
									</div>
							</div>





							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
                                    <input type="hidden" name="IDClub" id="IDClub" value="<?php if(empty($frm["IDClub"])) echo SIMUser::get("club"); else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> <?php echo SIMReg::get( "title" )?>
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
