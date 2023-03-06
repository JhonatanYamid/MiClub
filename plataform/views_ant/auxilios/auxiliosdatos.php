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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>
								<div class="col-sm-8"><input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="Nombre" value="<?php echo $frm["Nombre"]; ?>"></div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="Descripcion"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>
						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Periocidad', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<select class="form-control" id="Tipo" name="Periocidad">
										<optgroup label="Periodicidad">
											<?php
											$html = "";
											foreach (SIMResources::$PeriodicidadAuxilios as $indice => $valor) {
												$selected = ($frm["Periocidad"] == $valor) ? "selected" : "";
												$html .= '<option value="' . $indice . '" ' . $selected . '> ' . $valor . '</option>';
											}
											echo $html;
											?>
										</optgroup>
									</select>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoAuxilio', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<select class="form-control" id="IDTipoAuxilio" name="IDTipoAuxilio">
										<optgroup label="Tipo Auxilio">
											<?php
											$html = "";
											$sql_TipoAuxilio = "SELECT * FROM TipoAuxilio WHERE IDClub = '" . SIMUser::get('club') . "' AND Publicar = 'S'";
											$q_TipoAuxilio = $dbo->query($sql_TipoAuxilio);

											while ($TipoAuxilio = $dbo->assoc($q_TipoAuxilio)) {
												$selected = ($frm["IDTipoAuxilio"] == $TipoAuxilio['IDTipoAuxilio']) ? "selected" : "";
												$html .= '<option value="' . $TipoAuxilio['IDTipoAuxilio'] . '" ' . $selected . '> ' . $TipoAuxilio['Nombre'] . '</option>';
											}
											echo $html;
											?>
										</optgroup>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?> </label>
								<input name="Icono" id="Icono" class="" title="Icono" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["Icono"])) {
										echo "<img src='" . BANNERAPP_ROOT . $frm["Icono"] . "' >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Icono]&campo=Icono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>

						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Aplicapara', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<?php

									if (!empty($frm["AplicaPara"])) :
										$array_aplica = explode("|", $frm["AplicaPara"]);
									endif;
									array_pop($array_aplica);

									?>

									<input type="checkbox" name="AplicaPara[]" id="AplicaPara" value="CentralCervecera" <?php if (in_array("CentralCervecera", $array_aplica)) echo "checked"; ?>>Central Cervecera
									<input type="checkbox" name="AplicaPara[]" id="AplicaPara" value="ZonaFranca" <?php if (in_array("ZonaFranca", $array_aplica)) echo "checked"; ?>>Zona Franca
									<input type="checkbox" name="AplicaPara[]" id="AplicaPara" value="Artesanos" <?php if (in_array("Artesanos", $array_aplica)) echo "checked"; ?>>Artesanos
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
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