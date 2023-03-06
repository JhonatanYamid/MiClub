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

								<!--
								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Seccion Padre</label>

                                        <div class="col-sm-8">
                                           <input type="hidden" id="IDSeccion" name="IDSeccion" value="<?php echo $frm["IDPadre"];?>">
                                           <input type="text" id="NombreSeccion" name="NombreSeccion" class="input" value="<?php echo $dbo->getFields( "Seccion" , "Nombre" , "IDSeccion = '" . $frm["IDPadre"] . "'" )?>" readonly>
											<a href="PopupSeccion.php" target="_blank" onClick="window.open(this.href, this.target, 'width=300,height=1000'); return false;" class="ace-icon glyphicon glyphicon-search"></a>
                                        </div>
								</div>
							-->

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre </label>

										<div class="col-sm-8">
                                        <input type="text" id="Nombre" name="Nombre" placeholder="Nombre" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>" >
                                        </div>
								</div>

								<div  class="col-xs-12 col-sm-6">
		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Noticia para:  </label>

		<div class="col-sm-8">
			<?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$dirigidoa ) , $frm["DirigidoA"] , "DirigidoA" , "title=\"DirigidoA\"" )?>

			<?php
			if(SIMUser::get("club")=="36"){
				echo "<br>Tipo:";
				echo SIMHTML::formPopupArray( SIMResources::$tipo_socio  ,  $frm["TipoSocio"] , "TipoSocio" ,  "Seleccione tipo" , "form-control"  );
			 } ?>

		</div>
</div>

							</div>

                            <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Descripcion </label>

										<div class="col-sm-8">
										  <textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
										</div>
								</div>

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Publicar </label>

										<div class="col-sm-8"><? echo SIMHTML::formradiogroup( array_flip( SIMResources::$sino ) , $frm["Publicar"] , 'Publicar' , "class='input mandatory'" ) ?></div>
								</div>

							</div>




							<div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Orden </label>
										<div class="col-sm-8">
											<input type="text" id="Orden" name="Orden" placeholder="Orden" class="col-xs-12 mandatory" title="Orden" value="<?php echo $frm["Orden"]; ?>" >
										</div>
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
