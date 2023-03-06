<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'crearunnuevo', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
		</h4>


	</div>

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IconoPorEntregar', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? if (!empty($frm["IconoPorEntregar"])) {
										echo "<img src='" . CLUB_ROOT . "$frm[IconoPorEntregar]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoPorEntregar]&campo=IconoPorEntregar&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
									<input name="IconoPorEntregar" id=file class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'IconoPorEntregar', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">

								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IconoEntregado', LANGSESSION); ?> </label>

								<div class="col-sm-8">

									<? if (!empty($frm["IconoEntregado"])) {
										echo "<img src='" . CLUB_ROOT . "$frm[IconoEntregado]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoEntregado]&campo=IconoEntregado&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

									<?
									} // END if
									?>
									<input name="IconoEntregado" id=file class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'IconoEntregado', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">


								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IconoEntregar', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? if (!empty($frm["IconoEntregar"])) {
										echo "<img src='" . CLUB_ROOT . "$frm[IconoEntregar]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoEntregar]&campo=IconoEntregar&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
									<input name="IconoEntregar" id=file class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'IconoEntregar', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">

								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IconoRecibir', LANGSESSION); ?> </label>

								<div class="col-sm-8">

									<? if (!empty($frm["IconoRecibir"])) {
										echo "<img src='" . CLUB_ROOT . "$frm[IconoRecibir]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[IconoRecibir]&campo=IconoRecibir&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

									<?
									} // END if
									?>
									<input name="IconoRecibir" id=file class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'IconoRecibir', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">


								</div>
							</div>
						</div>






						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'HabilitaRegistraTodos(opcionparaentregarpjelreciboluzatodaslasviviendas)', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["HabilitaRegistraTodos"], "HabilitaRegistraTodos", "title=\"Habilita Registra Todos\"") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoBuscador', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="LabelBuscador" name="LabelBuscador" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoBuscador', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoBuscador', LANGSESSION); ?>" value="<?php echo $frm["LabelBuscador"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PermiteFirmarRecibido', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["PermiteFirmarRecibido"], "PermiteFirmarRecibido", "title=\"Permite Firmar Recibido\"") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoBotonFirmaRecepcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="LabelBotonFirmaRecepcion" name="LabelBotonFirmaRecepcion" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoBotonFirmaRecepcion', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoBotonFirmaRecepcion', LANGSESSION); ?>" value="<?php echo $frm["LabelBotonFirmaRecepcion"]; ?>">
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'ObligatorioFirmarRecibido', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), $frm["ObligatorioFirmarRecibido"], "ObligatorioFirmarRecibido", "title=\"Obligatorio Firmar Recibido\"") ?>
								</div>
							</div>
						</div>












						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
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
include("cmp/footer_scripts.php");
?>