<div class="widget-box transparent" id="recent-box">

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">

                        <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-users green"></i>
                                Cambiar Clave
                                </h3>
                            </div>



							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
											<input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" readonly>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Usuario </label>

										<div class="col-sm-8">
											<input type="text" id="User" name="User" placeholder="User" class="col-xs-12 " title="User" value="<?php echo $frm["User"]; ?>" readonly>
										</div>
								</div>

							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Clave Actual </label>

										<div class="col-sm-8">
											<input type="password" id="PasswordActual" name="PasswordActual" placeholder="PasswordActual" class="col-xs-12 mandatory" title="Password Actual" value="" >
										</div>
								</div>



							</div>

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Password </label>

										<div class="col-sm-8">
											<input type="password" id="Password" name="Password" placeholder="Password" class="col-xs-12 mandatory" title="Password" value="" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Repita Password </label>

										<div class="col-sm-8">
											<input type="password" id="RePassword" name="RePassword" placeholder="Repita Password" class="col-xs-12 mandatory" title="Repita Password" >
										</div>
								</div>

							</div>

                             <div class="widget-header widget-header-large">
                                <h3 class="widget-title grey lighter">
                                    <i class="ace-icon fa fa-camera green"></i>
                                  Foto
                                </h3>
                            </div>

                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>

										<div class="col-sm-8">
											<? if (!empty($frm[Foto])) {
												echo "<img src='".USUARIO_ROOT."$frm[Foto]' width=55 >";
												?>
											<?
											}// END if
											else{
											?>
                                            <?php } ?>
										</div>
								</div>

							</div>






							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                  <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />                                                                        
									<input type="hidden" name="action" id="action" value="cambiarclave" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>" >
										<i class="ace-icon fa fa-check bigger-110"></i>
										Cambiar Clave
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
