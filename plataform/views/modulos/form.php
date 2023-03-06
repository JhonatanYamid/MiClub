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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Directorio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Directorio" name="Directorio" placeholder="<?= SIMUtil::get_traduccion('', '', 'Directorio', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Directorio', LANGSESSION); ?>" value="<?php echo $frm["Directorio"]; ?>">
								</div>
							</div>

						</div>




						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Link', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Link" name="Link" placeholder="<?= SIMUtil::get_traduccion('', '', 'Link', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Link', LANGSESSION); ?>" value="<?php echo $frm["Link"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IdentificadorModulo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="IdentificadorModulo" name="IdentificadorModulo" placeholder="<?= SIMUtil::get_traduccion('', '', 'IdentificadorModulo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'IdentificadorModulo', LANGSESSION); ?>" value="<?php echo $frm["IdentificadorModulo"]; ?>">
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Tipo', LANGSESSION); ?> </label>

								<div class="col-sm-8"><?
														$tipomodulo = array("App" => "App", "WebView" => "WebView", "Encuesta" => "Encuesta", "Auxilios" => "Auxilios", "Noticias" => "Noticias", "Documentos" => "Documentos", "Restaurantes" => "Restaurantes", "Facturacion" => "Facturacion");
														echo SIMHTML::formradiogroup(array_flip($tipomodulo), $frm["Tipo"], 'Tipo', "class='input mandatory'") ?></div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarControlNavegación', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarControlNavegacion"], 'MostrarControlNavegacion', "class='input mandatory'") ?></div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarHeader', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarHeader"], 'MostrarHeader', "class='input mandatory'") ?></div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MensajeHeader', LANGSESSION); ?></label>

								<div class="col-sm-8"><input type="text" id="MensajeWebView" name="MensajeWebView" placeholder="<?= SIMUtil::get_traduccion('', '', 'MensajeWebView', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'MensajeHeader', LANGSESSION); ?>" value="<?php echo $frm["MensajeWebView"]; ?>"></div>

							</div>



						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Url', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Url" name="Url" placeholder="<?= SIMUtil::get_traduccion('', '', 'Url', LANGSESSION); ?>" class="col-xs-12" title="Url" value="<?php echo $frm["Url"]; ?>"></div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'LinkExterno', LANGSESSION); ?> </label>
								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["LinkExterno"], 'LinkExterno', "class='input mandatory'") ?></div>
							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'MostrarCirculorojodenotificaciones(cuandoseadesubmodulosymodulodenotificacionesseaunodelosmóduloshijos)', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarBadgeNotificaciones"], 'MostrarBadgeNotificaciones', "class='input mandatory'") ?></div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Sielmóduloesdenoticiasmostrarsecciones', LANGSESSION); ?>: </label>

								<div class="col-sm-8">
									<?php
									$tiposeccion = array("SeccionGrid" => "Grilla de seccion (iconos)", "SeccionHeader" => "Secciones en encbezado");
									echo SIMHTML::formradiogroup(array_flip($tiposeccion), $frm["TipoSeccion"], 'TipoSeccion', "class='input mandatory'");
									?>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'MostrarEnPerfiles', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["MostrarEnPerfiles"], 'MostrarEnPerfiles', "class='input '") ?></div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
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