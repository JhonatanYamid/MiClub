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
					

					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI()?>">
						
							<div  class="form-group first ">

								

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio </label>

										<div class="col-sm-8">
											<?php echo $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $frm["IDSocio"] . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $frm["IDSocio"] . "'" )?>
										</div>
								</div>
									
							</div>
						
						
							

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Comentario </label>

										<div class="col-sm-8">
											<?php echo $frm["Comentario"]; ?>
									</div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha </label>

										<div class="col-sm-8">
											<?php echo $frm["FechaTrCr"]; ?>
									</div>
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