<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>

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
						</div>
						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Informaciónporpartedelsolicitante', LANGSESSION); ?>
							</h3>
						</div>
						<div class="form-group first ">
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">
									<?= SIMUtil::get_traduccion('', '', 'Usuariosolicitante', LANGSESSION); ?>
								</label>

								<div class="col-sm-8">
									<?php
									$sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuario"] . "'";
									$qry_socio_club = $dbo->query($sql_socio_club);
									$r_socio = $dbo->fetchArray($qry_socio_club);
									?>
									<input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-funcionario-laboralUsuario" title="número de documento" <?php if ($_GET["action"] == "edit") {
																																																																										echo "disabled";
																																																																									}  ?> value="<?php echo $r_socio["Nombre"] ?>">
									<input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" title="Usuario">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">
									<?= SIMUtil::get_traduccion('', '', 'Certificadoanombrede', LANGSESSION); ?>
								</label>

								<div class="col-sm-8">
									<input id=AnombreDe type=text size=25 name=AnombreDe class="input" placeholder="<?= SIMUtil::get_traduccion('', '', 'Certificadoanombrede', LANGSESSION); ?> " title="A nombre de" <?php if ($_GET["action"] == "edit") echo "disabled"; ?> value="<?= $frm["AnombreDe"] ?>">
								</div>
							</div>

						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">
									<?= SIMUtil::get_traduccion('', '', 'Tipodecertificado', LANGSESSION); ?>
								</label>

								<div class="col-sm-8">
									<select name="IDTipoCertificado" id="IDTipoCertificado" <?php if ($_GET["action"] == "edit") echo "disabled"; ?>>
										<option value="">[Seleccione el tipo]</option>
										<?php
										foreach (SIMResources::$tipo_certificado_laboral as $id_tipo => $tipo) :
										?>
											<option value="<?php echo $id_tipo; ?>" <?php if ($id_tipo == $frm["IDTipoCertificado"]) echo "selected";  ?>><?php echo $tipo; ?></option>
										<?php
										endforeach;
										?>
									</select>
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">
									<?= SIMUtil::get_traduccion('', '', 'Fechasdelcertificado', LANGSESSION); ?>
								</label>

								<div class="col-sm-8">
									<textarea id="Fechas" name="Fechas" cols="8" rows="4" class="col-xs-12" title="Fechas" <?php if ($_GET["action"] == "edit") echo "disabled"; ?>><?php echo $frm["Fechas"]; ?></textarea>
								</div>
							</div>
						</div>

						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">
									<?= SIMUtil::get_traduccion('', '', 'Comentariousuario', LANGSESSION); ?>
									<br>
									<br>
								</label>
								<div class="col-sm-8">
									<textarea id="Comentario" name="Comentario" cols="8" rows="4" class="col-xs-12" title="Fechas" <?php if ($_GET["action"] == "edit") echo "disabled"; ?>><?php echo $frm["Comentario"]; ?></textarea>
								</div>
							</div>
						</div>

						<div class="form-group first ">
						</div>
						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-exclamation-circle green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Informaciónporpartedelclub', LANGSESSION); ?>
							</h3>
						</div>
						<div class="form-group first ">
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">
									<?= SIMUtil::get_traduccion('', '', 'Usuarioqueautoriza', LANGSESSION); ?>
								</label>
								<div class="col-sm-8">
									<?php
									if ($_GET["action"] == "edit") :
										$frm["IDUsuarioAutoriza"] = SIMUser::get("IDUsuario");
									endif;

									$sql_socio_club = "Select * From Usuario Where IDUsuario = '" . $frm["IDUsuarioAutoriza"] . "'";
									$qry_socio_club = $dbo->query($sql_socio_club);
									$r_socio = $dbo->fetchArray($qry_socio_club);
									?>
									<input type="text" id="NumeroDocumentoUsuario" name="NumeroDocumentoUsuario" placeholder="<?= SIMUtil::get_traduccion('', '', 'Númerodedocumento', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-funcionario-laboralUsuarioAutoriza" title="número de documento" disabled value="<?php echo $r_socio["Nombre"] ?>">
									<input type="hidden" name="IDUsuarioAutoriza" value="<?php echo $frm["IDUsuarioAutoriza"]; ?>" id="IDUsuarioAutoriza" title="Usuario">
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">
									<?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?>
								</label>

								<div class="col-sm-8">
									<select name="IDEstado" id="IDEstado" <?php if ($_GET["action"] == "add") echo "disabled"; ?>>
										<option value="">[Seleccione el estado]</option>
										<?php
										if ($_GET["action"] == "add") {
										?>
											<option value="1" selected>Pendiente</option>
											<?php
										} else {
											foreach (SIMResources::$estado_laboral as $id_estado => $estado) :
											?>
												<option value="<?php echo $id_estado; ?>" <?php if ($id_estado == $frm["IDEstado"]) echo "selected";  ?>><?php echo $estado; ?></option>
										<?php
											endforeach;
										}
										?>
									</select>
								</div>
							</div>
						</div>

						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Adjuntarcertificado', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<?
									if (!empty($frm["Archivo"])) {
										echo $frm["Archivo"];
									?>
										<a href="<? echo $script . ".php?action=delDoc&doc=$frm[Archivo]&campo=Archivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
									<input name="Archivo" id=file class="" title="Archivo" type="file" size="25" style="font-size: 10px" <?php if ($_GET["action"] == "add") echo "disabled"; ?>>
								</div>
							</div>
						</div>

						<div class="form-group first">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">
									<?= SIMUtil::get_traduccion('', '', 'Comentariodeaprobación', LANGSESSION); ?>
									<br>
									<br>
								</label>
							</div>
							<div class="col-sm-12">
								<?php
								$oCuerpo = new FCKeditor("ComentarioAprobacion");
								$oCuerpo->BasePath = "js/fckeditor/";
								$oCuerpo->Height = 400;
								$oCuerpo->Value =  $frm["ComentarioAprobacion"];
								$oCuerpo->Create();
								?>
							</div>
						</div>

						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
								<input type="hidden" name="estadoAntiguo" id="estadoAntiguo" value="<?php echo $estadoAntiguo ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script; ?>">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', $titulo_accion, LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
								</button>
							</div>
						</div>
					</form>
					<!-- PAGE CONTENT END -->
				</div><!-- /.col-xs-12 -->
			</div><!-- /.row -->
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>