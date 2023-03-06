<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor ?>

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
					

					<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?php echo SIMUtil::lastURI()?>" enctype="multipart/form-data">
						
							<div  class="form-group first ">

								
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Titulo</label>

										<div class="col-sm-8">
											<input id="Titulo" type="text" size="25" title="Titulo" name="Titulo" class="input mandatory" value="<?php echo $frm["Titulo"] ?>" />
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Introduccion</label>

										<div class="col-sm-8">
											<textarea rows="5" cols="40" id="Introduccion" name="Introduccion" class="input"><?php echo $frm["Introduccion"] ?></textarea>
										</div>
								</div>


									
							</div>
						
							
							
                            
                            
                            

							

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

										<div class="col-sm-8"><?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , "Publicar" , "title=\"Publicar\"" )?></div>
								</div>
								

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Imagen Servicio</label>

										<div class="col-sm-8">
											<?php 
												if($frm["Foto"])
												{
													?>
													<img alt="<?php echo $frm["Foto"] ?>" src="<?php echo IMGNOTICIA_ROOT.$frm["Foto"]?>">								
					                                <a href="<? echo $script.".php?action=DelImgNot&cam=Foto&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
												<?php 
												}
												else
												{
												?>
												<input type="file" name="Foto" id="Foto" class="popup" title="Noticia Imagen">
												<?php 
												}
												?>
										</div>
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