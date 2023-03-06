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
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>

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

									<input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" class="col-xs-12 mandatory autocomplete-ajax" title="<?= SIMUtil::get_traduccion('', '', 'NúmerodeDerecho', LANGSESSION); ?>" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"]) ?>">
									<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Codigo', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="Codigo" name="Codigo" placeholder="<?= SIMUtil::get_traduccion('', '', 'Codigo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Codigo', LANGSESSION); ?>" value="<?php echo $frm["Codigo"]; ?>">
									<input type="button" name="GenerarCodigo" id="GenerarCodigo" value="<?= SIMUtil::get_traduccion('', '', 'GenerarCodigoAutomatico', LANGSESSION); ?>">
								</div>

							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6"><label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaDesde', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="FechaDesde" name="FechaDesde" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaDesde', LANGSESSION); ?>" class="col-xs-12 calendar mandatory" title="<?= SIMUtil::get_traduccion('', '', 'FechaDesde', LANGSESSION); ?>" value="<?php echo $frm["FechaDesde"] ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaHasta', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="FechaHasta" name="FechaHasta" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaHasta', LANGSESSION); ?>" class="col-xs-12 calendar mandatory" title="<?= SIMUtil::get_traduccion('', '', 'FechaHasta', LANGSESSION); ?>" value="<?php echo $frm["FechaHasta"] ?>">
								</div>
							</div>

						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Motivo', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Motivo', LANGSESSION); ?>"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Disponible', LANGSESSION); ?> </label>
								<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Disponible"], 'Disponible', "class='input mandatory'") ?>
							</div>
						</div>

						<?php
						if (SIMUser::get("club") == 8 || SIMUser::get("club") == 28) {
						?>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ValorBono', LANGSESSION); ?> </label>
									<div class="col-sm-8">
										<input type="number" id="Valor" name="Valor" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'ValorBono', LANGSESSION); ?>" value="<?php echo $frm["Valor"] ?>">
									</div>
								</div>
							</div>
						<?php
						}
						?>

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
								<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
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