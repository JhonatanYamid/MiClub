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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<div class="col-sm-8">
										<!--
                                          <select name = "IDSocio" id="IDSocio" <?php if ($_GET["action"] != "add") echo "disabled"; ?>>
										    <option value=""></option>
										    <?php
											$sql_socio_club = "Select * From Socio Where IDClub = '" . SIMUser::get("club") . "' Order by Apellido Asc";
											$qry_socio_club = $dbo->query($sql_socio_club);
											while ($r_socio = $dbo->fetchArray($qry_socio_club)) : ?>
										    <option value="<?php echo $r_socio["IDSocio"]; ?>" <?php if ($r_socio["IDSocio"] == $frm["IDSocio"]) echo "selected";  ?>><?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]); ?></option>
										    <?php
											endwhile;    ?>
									      </select>
                                          -->
										<?php
										$sql_socio_club = "Select * From Socio Where IDSocio = '" . $frm["IDSocio"] . "'";
										$qry_socio_club = $dbo->query($sql_socio_club);
										$r_socio = $dbo->fetchArray($qry_socio_club); ?>

										<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_decode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
										<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
									</div>
								</div>
							</div>

						</div>


						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Pregunta', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Pregunta" name="Pregunta" cols="10" rows="5" class="col-xs-12" title="Pregunta" <?php if ($_GET["action"] != "add") echo "readonly"; ?>><?php echo $frm["Pregunta"]; ?></textarea>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Respuesta', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Respuesta" name="Respuesta" cols="3" rows="5" class="col-xs-12" title="Respuesta"><?php echo $frm["Respuesta"]; ?></textarea>
								</div>
							</div>
						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Correo', LANGSESSION); ?> </label>

								<div class="col-sm-8">

									<textarea id="CorreoElectronico" name="CorreoElectronico" cols="3" rows="2" class="col-xs-12" title="Correo Electronico" <?php if ($_GET["action"] != "add") echo "readonly"; ?>><?php echo $frm["CorreoElectronico"]; ?></textarea>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Notificarpush', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formRadioGroup(array_flip(SIMResources::$sino), "", "NotificarPush", "title=\"NotificarPush\"") ?>
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