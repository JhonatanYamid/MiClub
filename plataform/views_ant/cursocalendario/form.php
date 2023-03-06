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
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Tipo </label>

									<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp( "CursoTipo" , "Nombre" , "Nombre" , "IDCursoTipo" , $frm["IDCursoTipo"] , "[Seleccione]" , "form-control" , "title = \"Tipo\"", " and IDClub = '".SIMUser::get("club")."'" ); ?>
									</div>
							</div>

							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Ciclo </label>

									<div class="col-sm-8">
										<select class="form-control" name="Ciclo" id="Ciclo">
												<option value=""></option>
												<?php for($contador_c=1;$contador_c<=12;$contador_c++){ ?>
													<option value="<?php echo $contador_c; ?>" <?php if($contador_c==$frm["Ciclo"]) echo "selected"; ?>><?php echo $contador_c; ?></option>
												<?php } ?>
										</select>
									</div>
							</div>

						</div>

						<div  class="form-group first ">

							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Incio </label>

									<div class="col-sm-8">
										<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"] ?>" >
									</div>
							</div>

							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Fin </label>

									<div class="col-sm-8">
										<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="<?php echo $frm["FechaFin"] ?>" >
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
