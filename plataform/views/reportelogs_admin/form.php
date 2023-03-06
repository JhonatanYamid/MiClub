<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>DETALLES DEL LOG 
		</h4>

		
	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->
					
<!-- actions <?php echo SIMUtil::lastURI()?> -->
					<form class="form-horizontal formvalida" role="form" method="post" id="frmSocio" action="" enctype="multipart/form-data">
						
							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre de Usuario </label>

										<div class="col-sm-8">
											
												<input type="text" id="IDUsuario" name="IDUsuario" placeholder="IDUsuario" class="col-xs-12 mandatory" title="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" >
											
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Modulo </label>

										<div class="col-sm-8">
											<input type="text" id="Modulo" name="Modulo" placeholder="Modulo" class="col-xs-12 mandatory" title="Modulo" value="<?php echo $frm["Modulo"]; ?>" >
										</div>
								</div>
									
							</div>
						
							 

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha </label>

										<div class="col-sm-8">
											<input type="text" id="Fecha" name="Fecha" placeholder="Fecha" class="col-xs-12 mandatory" title="Fecha" value="<?php echo $frm["Fecha"]; ?>" >
										</div>
								</div>
  
								<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Consulta realizada </label>

										<div class="col-sm-8">
											<textarea type="text" id="Operacion" name="Operacion" placeholder="Operacion" class="col-xs-12 mandatory" title="Operacion"  rows="8" cols="110" ><?php echo $frm["Operacion"]; ?> </textarea>
										</div>
										 
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Direccion IP </label>

										<div class="col-sm-8">
												<input type="text" id="DireccionIP" name="DireccionIP" placeholder="DireccionIP" class="col-xs-12 mandatory" title="DireccionIP" value="<?php echo $frm["DireccionIP"]; ?>" >
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
																		<a   href="<? echo $script?>.php">
																		
																		
																		<button class="btn btn-info" type="button" rel="frmSocio" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										volver a lista
									</button></a>

									
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
