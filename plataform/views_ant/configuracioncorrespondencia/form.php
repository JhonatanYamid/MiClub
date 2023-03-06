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
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Por Entregar  </label>

									<div class="col-sm-8">
										 <? if (!empty($frm["IconoPorEntregar"])) {
												echo "<img src='".CLUB_ROOT."$frm[IconoPorEntregar]' width=55 >";
												?>
																							<a href="<? echo $script.".php?action=delfoto&foto=$frm[IconoPorEntregar]&campo=IconoPorEntregar&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											<?
											}// END if
											?>
											<input name="IconoPorEntregar" id=file class="col-xs-12"	title="IconoPorEntregar" type="file" size="25" style="font-size: 10px">

									</div>
							</div>
							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Entregado  </label>

									<div class="col-sm-8">

										<? if (!empty($frm["IconoEntregado"])) {
											echo "<img src='".CLUB_ROOT."$frm[IconoEntregado]' width=55 >";
											?>
										<a href="<? echo $script.".php?action=delfoto&foto=$frm[IconoEntregado]&campo=IconoEntregado&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

										<?
										}// END if
										?>
										<input name="IconoEntregado" id=file class="col-xs-12" title="IconoEntregado" type="file" size="25" style="font-size: 10px">


									</div>
							</div>
						</div>

						<div  class="form-group first ">
							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Entregar  </label>

									<div class="col-sm-8">
										 <? if (!empty($frm["IconoEntregar"])) {
												echo "<img src='".CLUB_ROOT."$frm[IconoEntregar]' width=55 >";
												?>
																							<a href="<? echo $script.".php?action=delfoto&foto=$frm[IconoEntregar]&campo=IconoEntregar&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
											<?
											}// END if
											?>
											<input name="IconoEntregar" id=file class="col-xs-12"	title="IconoEntregar" type="file" size="25" style="font-size: 10px">

									</div>
							</div>
							<div  class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Icono Recibir  </label>

									<div class="col-sm-8">

										<? if (!empty($frm["IconoRecibir"])) {
											echo "<img src='".CLUB_ROOT."$frm[IconoRecibir]' width=55 >";
											?>
										<a href="<? echo $script.".php?action=delfoto&foto=$frm[IconoRecibir]&campo=IconoRecibir&id=".$frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

										<?
										}// END if
										?>
										<input name="IconoRecibir" id=file class="col-xs-12" title="IconoRecibir" type="file" size="25" style="font-size: 10px">


									</div>
							</div>
						</div>






              <div  class="form-group first ">

								<div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Habilita Registra Todos (opcion para entregar pj el recibo luz a todas las viviendas) </label>

										<div class="col-sm-8">
											<?php echo SIMHTML::formRadioGroup( array_flip( SIMResources::$sino ) , $frm["HabilitaRegistraTodos"] , "HabilitaRegistraTodos" , "title=\"Habilita Registra Todos\"" )?>
										</div>
								</div>

                                <div  class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Label Buscador </label>

										<div class="col-sm-8">
										<input type="text" id="LabelBuscador" name="LabelBuscador" placeholder="Label Buscador" class="col-xs-12 mandatory" title="Label Buscador" value="<?php echo $frm["LabelBuscador"]; ?>" >
                                        </div>
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
