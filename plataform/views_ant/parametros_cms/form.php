<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?php echo strtoupper(SIMReg::get( "title" ))?>
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
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>

										<div class="col-sm-8">
											<input type="text" id="Email" name="Email" placeholder="Email" class="col-xs-12 mandatory" title="Email" value="<?php echo $frm["Email"]; ?>" >
										</div>
								</div>
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Foto </label>
										 <input name="Foto" id=file class="" title="Foto" type="file" size="25" style="font-size: 10px">
										<div class="col-sm-8">
											<? if (!empty($frm[Foto])) {
												echo "<img src='".DISENO_ROOT."$frm[Foto]' width='200' >";
												?>	
                                              <a href="<? echo $script.".php?action=delfoto&foto=$frm[Foto]&campo=Foto&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											  <?
											}// END if
											?>
										</div>
								</div>

								
									
							</div>
						
						
							

							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Titulo Home </label>

										<div class="col-sm-8">
											<textarea id="TituloHome" name="TituloHome" cols="10" rows="5" class="col-xs-12 mandatory" title="TituloHome"><?php echo $frm["TituloHome"]; ?></textarea>
    									</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Texto Home </label>

										<div class="col-sm-8">
											<textarea id="TextoHome" name="TextoHome" cols="10" rows="5" class="col-xs-12 mandatory" title="TituloHome"><?php echo $frm["TextoHome"]; ?></textarea>
    									</div>
								</div>

								
							</div>

                            
                            

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
                                    <input type="hidden" name="ID"  id="ID" value="<?php echo $frm[ $key ] ?>" />
									<input type="hidden" name="action" id="action" value="<?php echo $newmode?>" />
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