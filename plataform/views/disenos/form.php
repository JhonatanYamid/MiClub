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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>"><?php echo $frm["Descripcion"]; ?></textarea>

								</div>
							</div>

						</div>




						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Clase', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Clase" name="Clase" placeholder="<?= SIMUtil::get_traduccion('', '', 'Clase', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Clase', LANGSESSION); ?>" value="<?php echo $frm["Clase"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Color', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input name="Color" type="color" class="col-xs-12 mandatory" value="<?php if (empty($frm["Color"])) {
																											echo "#FFFFFF";
																										} else {
																											echo $frm["Color"];
																										}    ?>" />
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6"><?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>1
								<div class="col-sm-8">
									<? if (!empty($frm[Foto1])) {
										echo "<img src='" . DISENO_ROOT . "$frm[Foto1]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto1]&campo=Foto1&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
									<input name="Foto1" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>1" type="file" size="25" style="font-size: 10px">


								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>2 </label>

								<div class="col-sm-8">
									<? if (!empty($frm[Foto2])) {
										echo "<img src='" . DISENO_ROOT . "$frm[Foto2]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto2]&campo=Foto2&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
									<input name="Foto2" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>2" type="file" size="25" style="font-size: 10px">
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6"><?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>3
								<div class="col-sm-8">
									<? if (!empty($frm[Foto3])) {
										echo "<img src='" . DISENO_ROOT . "$frm[Foto3]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Foto3]&campo=Foto3&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
									<input name="Foto3" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>3" type="file" size="25" style="font-size: 10px">


								</div>
							</div>

						</div>




						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
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