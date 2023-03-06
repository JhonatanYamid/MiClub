<div class="widget-box transparent" id="recent-box">

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-users green"></i>
								<?= SIMUtil::get_traduccion('', '', 'CambiarClave', LANGSESSION); ?>
							</h3>
						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>" readonly>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="User" name="User" placeholder="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>" class="col-xs-12 " title="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>" value="<?php echo $frm["User"]; ?>" readonly>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ClaveActual', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="password" id="PasswordActual" name="PasswordActual" placeholder="<?= SIMUtil::get_traduccion('', '', 'ClaveActual', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'ClaveActual', LANGSESSION); ?>" value="">
								</div>
							</div>



						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Password', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="password" id="Password" name="Password" placeholder="<?= SIMUtil::get_traduccion('', '', 'Password', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Password', LANGSESSION); ?>" value="">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'RepitaPassword', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="password" id="RePassword" name="RePassword" placeholder="<?= SIMUtil::get_traduccion('', '', 'RepitaPassword', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'RepitaPassword', LANGSESSION); ?>">
								</div>
							</div>

						</div>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-camera green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?>
							</h3>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Foto', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? if (!empty($frm[Foto])) {
										echo "<img src='" . USUARIO_ROOT . "$frm[Foto]' width=55 >";
									?>
									<?
									} // END if
									else {
									?>
									<?php } ?>
								</div>
							</div>

						</div>






						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="cambiarclave" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', 'CambiarClave', LANGSESSION); ?>
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