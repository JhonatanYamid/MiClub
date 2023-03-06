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


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo utf8_encode($frm["Nombre"]); ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Responsable </label>

										<div class="col-sm-8">
										  <input type="text" id="Responsable" name="Responsable" placeholder="Responsable" class="col-xs-12 mandatory" title="Responsable" value="<?php echo utf8_encode($frm["Responsable"]); ?>" >
										</div>
								</div>

							</div>




							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email Responsable</label>

										<div class="col-sm-8">
											<input type="text" id="CorreoResponsable" name="CorreoResponsable" placeholder="Correo Responsable" class="col-xs-12 mandatory" title="Correo Responsable" value="<?php echo $frm["CorreoResponsable"]; ?>" >
										</div>
								</div>

                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Mostrar area en:  </label>

										<div class="col-sm-8">
                                        <select name="MostrarApp" id="MostrarApp" class="form-control">
                                        	<option value=""></option>
                                        	<option value="S" <?php if($frm["MostrarApp"]=="S") echo "selected";?> >Mostrar en App y Administrador</option>
                                            <option value="N" <?php if($frm["MostrarApp"]=="N") echo "selected";?>>Mostrar solo en Administrador</option>
                                        </select>
                                        </div>
								</div>


							</div>

                            <div  class="form-group first ">


								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Activo </label>

										<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Activo"] , 'Activo' , "class='input mandatory'" ) ?></div>
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
				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
	include( "cmp/footer_scripts.php" );
?>
