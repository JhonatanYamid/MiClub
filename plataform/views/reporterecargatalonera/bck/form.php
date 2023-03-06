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
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nit </label>

										<div class="col-sm-8">
										  <input type="text" id="Nit" name="Nit" placeholder="Nit" class="col-xs-12 mandatory" title="Nit" value="<?php echo $frm["Nit"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>
								  <div class="col-sm-8">
								    <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" >
                                          
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

								<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>
								  <div class="col-sm-8">
								    <input type="email" id="Email" name="Email" placeholder="Email" class="col-xs-12 mandatory" title="Email" value="<?php echo $frm["Email"]; ?>" >
                                          
                                        </div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Telefono </label>

										<div class="col-sm-8">
										  <input type="text" id="Telefono" name="Telefono" placeholder="Telefono" class="col-xs-12 mandatory" title="Telefono" value="<?php echo $frm["Telefono"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Sitio Web </label>
								  <div class="col-sm-8">
								    <input type="text" id="SitioWeb" name="SitioWeb" placeholder="Sitio Web" class="col-xs-12" title="Sitio Web" value="<?php echo $frm["SitioWeb"]; ?>" >
                                          
                                        </div>
								</div>
									
							</div>
                            
                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Contacto </label>

										<div class="col-sm-8">
										  <input type="text" id="NombreContacto" name="NombreContacto" placeholder="Nombre Contacto" class="col-xs-12 mandatory" title="Nombre Contacto" value="<?php echo $frm["NombreContacto"]; ?>" >
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Logo </label>
								  <div class="col-sm-8">
								    			<? if (!empty($frm[Logo])) {
													echo "<img src='".CLIENTE_ROOT."$frm[Logo]' width=55 >";
													?>
                                                <a href="<? echo $script.".php?action=dellogo&logo=$frm[Logo]&campo=Logo&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>     
												<?
												}// END if
												?>
												<input name="Logo" id=file class="col-xs-12"	title="Logo" type="file" size="25" style="font-size: 10px">
                                          
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