<?php
$frm_vacuna['IDSocio'] = $_GET['id'];

$dbo = &SIMDB::get();
$query = $dbo->query("SELECT V.* FROM Socio S LEFT JOIN Vacuna V ON S.IDSocio=V.IDSocio WHERE S.IDSocio=" . $frm_vacuna['IDSocio']);
$frm_vacuna = $dbo->fetch($query);

//Dato list marca vacuna
$query = $dbo->query("SELECT * FROM VacunaMarca");
$marcaVacunas = $dbo->fetch($query);

//Dato list entidad vacuna
$query = $dbo->query("SELECT IDVacunaEntidad, Nombre FROM VacunaEntidad WHERE IDClub=" . SIMUser::get("club"));
$entidadVacunas = $dbo->fetch($query);
if (isset($entidadVacunas["IDVacunaEntidad"])) {
	$entidadVacunas = [$entidadVacunas];
}

if (empty($marcaVacunas[0]["IDVacunaMarca"])) {
	$marcaVacunas = [$marcaVacunas];
}

if (empty($frm_vacuna['IDVacuna'])) {
	$frm_vacuna['Vacunado'] = 'N';
}

if ($frm_vacuna["FechaPrimeraDosis"] === "0000-00-00") {
	$frm_vacuna["FechaPrimeraDosis"] = "";
}

if ($frm_vacuna["FechaSegundaDosis"] === "0000-00-00") {
	$frm_vacuna["FechaSegundaDosis"] = "";
}

?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'Informacióndevacuna', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Elsocioestávacunado', LANGSESSION); ?>? </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm_vacuna["Vacunado"], 'Vacunado', "class='input mandatory'") ?>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Vacunadoterceradosis', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm_vacuna["VacunadoTerceraDosis"], 'VacunadoTerceraDosis', "class='input mandatory'") ?>
								</div>
							</div>

						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Marca', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<select name="IDVacunaMarca" id="IDVacunaMarca" class="form-control">
										<option value=""><?= SIMUtil::get_traduccion('', '', '[SeleccioneMarcavacuna]', LANGSESSION); ?></option>
										<?php foreach ($marcaVacunas as $value) { ?>
											<option <?php if ($frm_vacuna['IDVacunaMarca'] == $value["IDVacunaMarca"]) {
														echo " selected ";
													} ?>value="<?php echo $value["IDVacunaMarca"] ?>"><?php echo $value["Nombre"] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Entidad', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="Entidad" name="Entidad" placeholder="<?= SIMUtil::get_traduccion('', '', 'Entidad', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Entidad', LANGSESSION); ?>" value="<?php echo $frm_vacuna["Entidad"]; ?>">
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'LugarVacunación', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="Lugar" name="Lugar" placeholder="<?= SIMUtil::get_traduccion('', '', 'LugarVacunación', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'LugarVacunación', LANGSESSION); ?>" value="<?php echo $frm_vacuna["Lugar"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Fechacita', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="FechaCita" name="FechaCita" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechacita', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'Fechacita', LANGSESSION); ?>" value="<?php echo $frm_vacuna["FechaCita"]; ?>">
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Lugarprimeracita', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="LugarCitaPrimera" name="LugarCitaPrimera" placeholder="<?= SIMUtil::get_traduccion('', '', 'Lugarprimeracita', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Lugarprimeracita', LANGSESSION); ?>" value="<?php echo $frm_vacuna["LugarCitaPrimera"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Lugarsegundacita', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="LugarCitaSegunda" name="LugarCitaSegunda" placeholder="<?= SIMUtil::get_traduccion('', '', 'Lugarsegundacita', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Lugarsegundacita', LANGSESSION); ?>" value="<?php echo $frm_vacuna["LugarCitaSegunda"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Lugarterceracita', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="LugarTerceraDosis" name="LugarTerceraDosis" placeholder="<?= SIMUtil::get_traduccion('', '', 'Lugarterceracita', LANGSESSION); ?>" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Lugarterceracita', LANGSESSION); ?>" value="<?php echo $frm_vacuna["LugarTerceraDosis"]; ?>">
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Fechaprimeradosis', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="FechaPrimeraDosis" name="FechaPrimeraDosis" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechaprimeradosis', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'Fechaprimeradosis', LANGSESSION); ?>" value="<?php echo $frm_vacuna["FechaPrimeraDosis"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Fechasegundadosis', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="FechaSegundaDosis" name="FechaSegundaDosis" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechasegundadosis', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'Fechasegundadosis', LANGSESSION); ?>" value="<?php echo $frm_vacuna["FechaSegundaDosis"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Fechatercerdosis', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<input type="text" id="FechaTerceraDosis" name="FechaTerceraDosis" placeholder="<?= SIMUtil::get_traduccion('', '', 'Fechatercerdosis', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'Fechatercerdosis', LANGSESSION); ?>" value="<?php echo $frm_vacuna["FechaTerceraDosis"]; ?>">
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Certificadoprimeradosis', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<?php if ($frm_vacuna["ImagenPrimeraDosis"]) { ?>
										<h5>Imagen actual</h5>
										<img src="<?php echo VACUNA_ROOT . $frm_vacuna["ImagenPrimeraDosis"] ?>" width="200">
										<a href="<? echo VACUNA_ROOT . $frm_vacuna["ImagenPrimeraDosis"] ?>" class="ace-icon fa fa-eye">&nbsp;</a>
										<a href="<? echo $script . ".php?action=del-vacuna-image&archivo=" . $frm_vacuna["ImagenPrimeraDosis"] . "&num_img=Primera&id=" . $frm_vacuna["IDVacuna"] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

									<?php } ?>
									<br />
									<br />
									<input type="file" id="ImagenPrimeraDosis" name="ImagenPrimeraDosis" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Certificadoprimeradosis', LANGSESSION); ?>" value="<?php echo $frm_vacuna["ImagenPrimeraDosis"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Certificadosegundadosis', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<?php if ($frm_vacuna["ImagenSegundaDosis"]) { ?>
										<h5>Imagen actual</h5>
										<img src="<?php echo VACUNA_ROOT . $frm_vacuna["ImagenSegundaDosis"] ?>" width="200">
										<a href="<? echo VACUNA_ROOT . $frm_vacuna["ImagenSegundaDosis"] ?>" class="ace-icon fa fa-eye">&nbsp;</a>
										<a href="<? echo $script . ".php?action=del-vacuna-image&archivo=" . $frm_vacuna["ImagenSegundaDosis"] . "&num_img=Segunda&id=" . $frm_vacuna["IDVacuna"] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

									<?php } ?>
									<br />
									<br />
									<input type="file" id="ImagenSegundaDosis" name="ImagenSegundaDosis" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Certificadosegundadosis', LANGSESSION); ?>" value="<?php echo $frm_vacuna["ImagenSegundaDosis"]; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'Certificadoterceradosis', LANGSESSION); ?></label>
								<div class="col-sm-8">
									<?php if ($frm_vacuna["ImagenTerceraDosis"]) { ?>
										<h5>Imagen actual</h5>
										<img src="<?php echo VACUNA_ROOT . $frm_vacuna["ImagenTerceraDosis"] ?>" width="200">
										<a href="<? echo VACUNA_ROOT . $frm_vacuna["ImagenTerceraDosis"] ?>" class="ace-icon fa fa-eye">&nbsp;</a>
										<a href="<? echo $script . ".php?action=del-vacuna-image&archivo=" . $frm_vacuna["ImagenTerceraDosis"] . "&num_img=Segunda&id=" . $frm_vacuna["IDVacuna"] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

									<?php } ?>
									<br />
									<br />
									<input type="file" id="ImagenTerceraDosis" name="ImagenTerceraDosis" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Certificadoterceradosis', LANGSESSION); ?>" value="<?php echo $frm_vacuna["ImagenTerceraDosis"]; ?>">
								</div>
							</div>

						</div>
						<!--div  class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Campos primera dosis</label>					
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Campos segunda dosis</label>					
							</div>
						</div-->

						<!-- Campos dinámicos-->
						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Camposprimeradosis', LANGSESSION); ?>
							</h3>
						</div>
						<?php
						$query = $dbo->query("SELECT Nombre, Tipo, Obligatorio, IDCampoVacunacion, Valores
							FROM CampoVacunacion 							
							WHERE IDClub=" . SIMUser::get("club") . " 
							ORDER BY Orden ASC");
						$camposvacuna = $dbo->fetch($query);
						if (isset($camposvacuna["Nombre"])) {
							$camposvacuna = [$camposvacuna];
						}

						$camposvacuna = array_chunk($camposvacuna, 2);

						$key = 0;
						foreach ($camposvacuna as $value) :

						?>
							<div class="form-group first ">
								<?php

								foreach ($value as $campos_vacuna) :


									$IDCampoVacunacion = $campos_vacuna["IDCampoVacunacion"];

									$query = $dbo->query("SELECT IDVacunaCampoVacunacion, Valor 
									FROM VacunaCampoVacunacion
									WHERE IDCampoVacunacion=$IDCampoVacunacion 
									AND Dosis=1 
									AND IDSocio={$_GET["id"]}
									LIMIT 1");

									$campo = $dbo->fetch($query);

								?>
									<div class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?php echo $campos_vacuna["Nombre"] ?></label>
										<div class="col-sm-8">
											<?php $campos = explode(",", $campos_vacuna["Valores"]); ?>
											<?php if ($campos_vacuna["Valores"] != null && $campos_vacuna["Valores"] != "") : ?>
												<select name="campos_dinamicos[Valor_<?php echo $key ?>]">
													<option></option>
													<?php foreach ($campos as $option) : ?>
														<option value="<?php echo $option ?>" <?php if ($option == $campo["Valor"]) {
																									echo 'selected';
																								} ?>><?php echo $option ?></option>
													<?php endforeach; ?>
												</select>
											<?php else : ?>
												<input type="text" name="campos_dinamicos[Valor_<?php echo $key ?>]" placeholder="<?php echo $campos_vacuna["Nombre"] ?>" class="col-xs-12" value="<?php echo $campo["Valor"] ?>" />
											<?php endif; ?>
											<input type="hidden" name="campos_dinamicos[Dosis_<?php echo $key ?>]" value="1">
											<input type="hidden" name="campos_dinamicos[IDSocio_<?php echo $key ?>]" value="<?php echo $frm["IDSocio"] ?>">
											<input type="hidden" name="campos_dinamicos[IDCampoVacunacion_<?php echo $key ?>]" value="<?php echo $campos_vacuna["IDCampoVacunacion"] ?>">
											<input type="hidden" name="campos_dinamicos[IDVacunaCampoVacunacion_<?php echo $key++ ?>]" value="<?php echo $campo["IDVacunaCampoVacunacion"] ?>">
										</div>
									</div>

								<?php
								endforeach;

								?>
							</div>
						<?php

						endforeach;

						?>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Campossegundadosis', LANGSESSION); ?>
							</h3>
						</div>

						<?php

						foreach ($camposvacuna as $value) :

						?>
							<div class="form-group first ">
								<?php

								foreach ($value as $campos_vacuna) :

									$IDCampoVacunacion = $campos_vacuna["IDCampoVacunacion"];

									$query = $dbo->query("SELECT IDVacunaCampoVacunacion, Valor 
									FROM VacunaCampoVacunacion
									WHERE IDCampoVacunacion=$IDCampoVacunacion 
									AND Dosis=2
									AND IDSocio={$_GET["id"]}
									LIMIT 1");

									$campo = $dbo->fetch($query);

								?>
									<div class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?php echo $campos_vacuna["Nombre"] ?></label>
										<div class="col-sm-8">
											<?php $campos = explode(",", $campos_vacuna["Valores"]); ?>
											<?php if ($campos_vacuna["Valores"] != null && $campos_vacuna["Valores"] != "") : ?>
												<select name="campos_dinamicos[Valor_<?php echo $key ?>]">
													<option></option>
													<?php foreach ($campos as $option) : ?>
														<option value="<?php echo $option ?>" <?php if ($option == $campo["Valor"]) {
																									echo 'selected';
																								} ?>><?php echo $option ?></option>
													<?php endforeach; ?>
												</select>
											<?php else : ?>
												<input type="text" name="campos_dinamicos[Valor_<?php echo $key ?>]" placeholder="<?php echo $campos_vacuna["Nombre"] ?>" class="col-xs-12" value="<?php echo $campo["Valor"] ?>" />
											<?php endif; ?>
											<input type="hidden" name="campos_dinamicos[Dosis_<?php echo $key ?>]" value="2">
											<input type="hidden" name="campos_dinamicos[IDSocio_<?php echo $key ?>]" value="<?php echo $frm["IDSocio"] ?>">
											<input type="hidden" name="campos_dinamicos[IDCampoVacunacion_<?php echo $key ?>]" value="<?php echo $campos_vacuna["IDCampoVacunacion"] ?>">
											<input type="hidden" name="campos_dinamicos[IDVacunaCampoVacunacion_<?php echo $key++ ?>]" value="<?php echo $campo["IDVacunaCampoVacunacion"] ?>">
										</div>
									</div>

								<?php
								endforeach;

								?>
							</div>
						<?php

						endforeach;
						?>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Camposterceradosis', LANGSESSION); ?>
							</h3>
						</div>

						<?php

						foreach ($camposvacuna as $value) :

						?>
							<div class="form-group first ">
								<?php

								foreach ($value as $campos_vacuna) :

									$IDCampoVacunacion = $campos_vacuna["IDCampoVacunacion"];

									$query = $dbo->query("SELECT IDVacunaCampoVacunacion, Valor 
									FROM VacunaCampoVacunacion
									WHERE IDCampoVacunacion=$IDCampoVacunacion 
									AND Dosis=3
									AND IDSocio={$_GET["id"]}
									LIMIT 1");

									$campo = $dbo->fetch($query);

								?>
									<div class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?php echo $campos_vacuna["Nombre"] ?></label>
										<div class="col-sm-8">
											<?php $campos = explode(",", $campos_vacuna["Valores"]); ?>
											<?php if ($campos_vacuna["Valores"] != null && $campos_vacuna["Valores"] != "") : ?>
												<select name="campos_dinamicos[Valor_<?php echo $key ?>]">
													<option></option>
													<?php foreach ($campos as $option) : ?>
														<option value="<?php echo $option ?>" <?php if ($option == $campo["Valor"]) {
																									echo 'selected';
																								} ?>><?php echo $option ?></option>
													<?php endforeach; ?>
												</select>
											<?php else : ?>
												<input type="text" name="campos_dinamicos[Valor_<?php echo $key ?>]" placeholder="<?php echo $campos_vacuna["Nombre"] ?>" class="col-xs-12" value="<?php echo $campo["Valor"] ?>" />
											<?php endif; ?>
											<input type="hidden" name="campos_dinamicos[Dosis_<?php echo $key ?>]" value="2">
											<input type="hidden" name="campos_dinamicos[IDSocio_<?php echo $key ?>]" value="<?php echo $frm["IDSocio"] ?>">
											<input type="hidden" name="campos_dinamicos[IDCampoVacunacion_<?php echo $key ?>]" value="<?php echo $campos_vacuna["IDCampoVacunacion"] ?>">
											<input type="hidden" name="campos_dinamicos[IDVacunaCampoVacunacion_<?php echo $key++ ?>]" value="<?php echo $campo["IDVacunaCampoVacunacion"] ?>">
										</div>
									</div>

								<?php
								endforeach;

								?>
							</div>
						<?php

						endforeach;
						?>



						<input type="hidden" name="campos_dinamicos[keys]" value="<?php echo $key ?>">

						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $frm["IDClub"] ?>" />
								<input type="hidden" name="action" id="action" value="update-vacuna" />
								<input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $frm["IDSocio"] ?>" />
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