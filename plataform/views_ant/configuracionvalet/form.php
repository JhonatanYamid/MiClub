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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MensajeCancelacion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="MensajeCancelacion" name="MensajeCancelacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'MensajeCancelacion', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'MensajeCancelacion', LANGSESSION); ?>" value="<?php echo $frm["MensajeCancelacion"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MensajeSolicitar', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="MensajeSolicitar" name="MensajeSolicitar" placeholder=" <?= SIMUtil::get_traduccion('', '', 'MensajeSolicitar', LANGSESSION); ?>" class="col-xs-12 mandatory" title=" <?= SIMUtil::get_traduccion('', '', 'MensajeSolicitar', LANGSESSION); ?>" value="<?php echo $frm["MensajeSolicitar"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'MensajeSolicitarTercero', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="MensajeSolicitarTercero" name="MensajeSolicitarTercero" placeholder="<?= SIMUtil::get_traduccion('', '', 'MensajeSolicitarTercero', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'MensajeSolicitarTercero', LANGSESSION); ?>" value="<?php echo $frm["MensajeSolicitarTercero"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'BotonSolicitar', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="BotonSolicitar" name="BotonSolicitar" placeholder="<?= SIMUtil::get_traduccion('', '', 'BotonSolicitar', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'BotonSolicitar', LANGSESSION); ?>" value="<?php echo $frm["BotonSolicitar"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">



							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'BotonsolicitarTercero', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="BotonSolicitarTercero" name="BotonSolicitarTercero" placeholder="<?= SIMUtil::get_traduccion('', '', 'BotonsolicitarTercero', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'BotonsolicitarTercero', LANGSESSION); ?>" value="<?php echo $frm["BotonSolicitarTercero"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'BotonCancelacion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="BotonCancelacion" name="BotonCancelacion" placeholder="<?= SIMUtil::get_traduccion('', '', 'BotonCancelacion', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'BotonCancelacion', LANGSESSION); ?>" value="<?php echo $frm["BotonCancelacion"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">



							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoRecibirSocio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="LabelRecibirSocio" name="LabelRecibirSocio" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoRecibirSocio', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoRecibirSocio', LANGSESSION); ?>" value="<?php echo $frm["LabelRecibirSocio"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'BotonBuscarSocio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="BotonBuscarSocio" name="BotonBuscarSocio" placeholder="<?= SIMUtil::get_traduccion('', '', 'BotonBuscarSocio', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'BotonBuscarSocio', LANGSESSION); ?>" value="<?php echo $frm["BotonBuscarSocio"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">



							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'BotonRecibirSocio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="BotonRecibirSocio" name="BotonRecibirSocio" placeholder="<?= SIMUtil::get_traduccion('', '', 'BotonRecibirSocio', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'BotonRecibirSocio', LANGSESSION); ?>" value="<?php echo $frm["BotonRecibirSocio"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoRecibirTercero', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="LabelRecibirTercero" name="LabelRecibirTercero" placeholder="<?= SIMUtil::get_traduccion('', '', 'TextoRecibirTercero', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TextoRecibirTercero', LANGSESSION); ?>" value="<?php echo $frm["LabelRecibirTercero"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'BotonRecibirTercero', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="BotonRecibirTercero" name="BotonRecibirTercero" placeholder="<?= SIMUtil::get_traduccion('', '', 'BotonRecibirTercero', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'BotonRecibirTercero', LANGSESSION); ?>" value="<?php echo $frm["BotonRecibirTercero"]; ?>">
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