<?php


$dbo = &SIMDB::get();
$frm = SIMUtil::varsLOG($_POST);
$get = SIMUtil::varsLOG($_GET);
//Dato list marca Vacuna2
$query = $dbo->query("SELECT * FROM VacunaMarca");
$marcaVacunas = $dbo->fetch($query);
$sqlCampoVacunacion = "SELECT * FROM CampoVacunacion WHERE IDClub = " . SIMUser::get('club') . " AND Publicar = 'S' ORDER BY Orden ASC";
$sqlConfiguraionVacunacion2 = "SELECT * FROM ConfiguracionVacunacion2 WHERE IDClub = " . SIMUser::get('club') . " AND Activo = 'S' LIMIT 1";

$q_CampoVacunacion = $dbo->query($sqlCampoVacunacion);
$q_ConfiguraionVacunacion2 = $dbo->query($sqlConfiguraionVacunacion2);

$r_ConfiguraionVacunacion2 = $dbo->assoc($q_ConfiguraionVacunacion2);
$radioVacunado['si'] = $r_ConfiguraionVacunacion2['LabelSiConfirmacion'];
$radioVacunado['no'] = $r_ConfiguraionVacunacion2['LabelNoConfirmacion'];
$radioVacunado['no quiero'] = $r_ConfiguraionVacunacion2['LabelNoQuieroConfirmacion'];

//Dato list entidad Vacuna2
$query = $dbo->query("SELECT IDVacunaEntidad, Nombre FROM VacunaEntidad WHERE IDClub=" . SIMUser::get("club"));
$entidadVacunas = $dbo->fetch($query);
if (isset($entidadVacunas["IDVacunaEntidad"])) {
	$entidadVacunas = [$entidadVacunas];
}
//Dato list Vacunado
$queryVacunado = $dbo->query("SELECT * FROM Vacunado WHERE IDSocio=" . $get['id']);
$Vacunado = $dbo->fetch($queryVacunado);
if (empty($marcaVacunas[0]["IDVacunaMarca"])) {
	$marcaVacunas = [$marcaVacunas];
}

?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>Información de Vacuna 2 <?php echo strtoupper(SIMReg::get("title")) ?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<br>

					<div class="widget-header widget-header-large registrarDosis">
						<h3 class="widget-title grey lighter">
							<i class="ace-icon fa fa-info green"></i>
							Registrar Certificado Digital
						</h3>
					</div>

					<form class="form-horizontal formvalida" role="form" method="post" id="frmCertificadoDigital" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
						<?php if ($r_ConfiguraionVacunacion2['MostrarEstaVacunado'] == 'S') { ?>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> El socio está vacunado? </label>
									<div class="col-sm-8">
										<?= SIMHTML::formradiogroup(array_flip($radioVacunado), $Vacunado['DeseoVacuna'], 'DeseoVacuna', "class='input ValidaVacunado mandatory'") ?>
									</div>
								</div>
							</div>
						<?php } ?>
						<?php
						$ArchivoVacuna = (isset($Vacunado["ArchivoVacuna"])) ? $Vacunado["ArchivoVacuna"] : '';
						$QrVacuna = (isset($Vacunado["CodigoQr"])) ? $Vacunado["CodigoQr"] : '';
						?>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= $r_ConfiguraionVacunacion2['LabelRegistrarCarneGobierno'] ?></label>
								<div class="col-sm-8">
									<?php if ($ArchivoVacuna != '') { ?>
										<iframe src="<?= VACUNA_ROOT . $ArchivoVacuna ?>" style="width:100%; height:300px;" frameborder="0"></iframe>
										<a href="<? echo $script . ".php?action=del-archivo-vacunado&archivo=" . $ArchivoVacuna . "&id=" . $Vacunado['IDVacunado'] . "&IDSocio=" . $get["id"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?php } ?>
									<input type="file" name="CertificadoDigital" id="CertificadoDigital" class="" title="<?= $r_ConfiguraionVacunacion2['LabelRegistrarCarneGobierno'] ?>" value="<?= $ArchivoVacuna ?>">
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Codigo QR </label>
								<div class="col-sm-8">
									<? if (!empty($QrVacuna)) {
										echo "<img src='" . VACUNA_ROOT . "qr/$QrVacuna'>";
									} // END if
									?>
								</div>
							</div>
						</div>
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="action" id="action" value="update-Archivo-vacunado" />
								<input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $get["id"] ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frmCertificadoDigital">
									<i class="ace-icon fa fa-check bigger-110"></i>
									Registrar Certificado Digital
								</button>
								<input type="hidden" name="IDA" id="IDA0" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="IDB" id="IDB0" value="<?php echo $frm[$key] ?>" />
							</div>
						</div>
					</form>

					<?php
					$sqlDosis = "SELECT * FROM Dosis WHERE IDClub = " . SIMUser::get('club');
					$q_Dosis = $dbo->query($sqlDosis);

					// $frm_vacuna = array();
					while ($arrVacuna = $dbo->assoc($query)) {
						array_push($frm_vacuna, $arrVacuna);
					}

					$cont = 0;
					while ($r_Dosis = $dbo->assoc($q_Dosis)) {
						$query = $dbo->query("SELECT V.* FROM Vacuna2 V WHERE V.IDSocio=" . $get['id'] . " AND V.IDClub = " . SIMUser::get('club') . " AND IDDosis = '" . $r_Dosis['IDDosis'] . "' ORDER BY IDVacuna ASC");
						$frm_vacuna = $dbo->assoc($query);

					?>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info green"></i>
								<?= $r_Dosis['NombreDosis'] ?>
							</h3>
						</div>

						<form class="form-horizontal formvalida" role="form" method="post" id="frm<?php echo $script . $cont; ?>" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Marca</label>
									<div class="col-sm-8">
										<select name="Marca" id="Marca<?= $cont ?>" class="form-control">
											<option value="">[Seleccione Marca vacuna]</option>
											<?php foreach ($marcaVacunas as $value) { ?>
												<option <?php if ($frm_vacuna['Marca'] == $value["Nombre"]) {
															echo " selected ";
														} ?>value="<?php echo $value["Nombre"] ?>"><?php echo $value["Nombre"] ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Entidad</label>
									<div class="col-sm-8">
										<input type="text" id="EntidadDosis<?= $cont ?>" name="EntidadDosis" placeholder="Entidad" class="col-xs-12" title="Entidad" value="<?php echo $frm_vacuna["EntidadDosis"]; ?>">
									</div>
								</div>
							</div>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Lugar Vacunación</label>
									<div class="col-sm-8">
										<input type="text" id="Lugar<?= $cont ?>" name="Lugar" placeholder="Lugar vacunación" class="col-xs-12" title="Lugar vacunación" value="<?php echo $frm_vacuna["Lugar"]; ?>">
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Fecha cita</label>
									<div class="col-sm-8">
										<input type="text" id="FechaCitaVacuna<?= $cont ?>" name="FechaCitaVacuna" placeholder="Fecha Cita" class="col-xs-12 calendar" title="Fecha Cita" value="<?php echo $frm_vacuna["FechaCitaVacuna"]; ?>">
									</div>
								</div>
							</div>
							<div class="form-group first ">
								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Certificado</label>
									<div class="col-sm-8">
										<?php if ($frm_vacuna["Certificado"]) { ?>
											<h5>Imagen actual</h5>
											<img src="<?= VACUNA_ROOT . $frm_vacuna["Certificado"] ?>" width="200">
											<a href="<?= VACUNA_ROOT . $frm_vacuna["Certificado"] ?>" class="ace-icon fa fa-eye">&nbsp;</a>
											<a href="<?= $script . ".php?action=del-vacuna2-image&archivo=" . $frm_vacuna["Certificado"] . "&num_img=Primera&id=" . $frm_vacuna["IDVacuna"] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
										<?php } ?>
										<br />
										<br />
										<input type="file" id="Certificado<?= $cont ?>" name="Foto" class="" title="Certificado" value="<?= $frm_vacuna["Certificado"]; ?>">
									</div>
								</div>
							</div>
							<?php
							//Consulto los campos dinamicos

							$q_CampoVacunacion = $dbo->query($sqlCampoVacunacion);
							while ($r = $dbo->assoc($q_CampoVacunacion)) :
								if ($frm_vacuna['IDVacuna']) {
									$q_VacunaCampoVacunacion2 = $dbo->getFields('VacunaCampoVacunacion2', 'Valor', ' IDVacuna = "' . $frm_vacuna['IDVacuna'] . '" AND IDCampoVacunacion = ' . $r['IDCampoVacunacion']);
								} else {
									$q_VacunaCampoVacunacion2 = '';
								}
								$mandatory = ($r['Obligatorio' == 'S']) ? 'mandatory' : '';
							?>
								<div class="form-group first ">
									<div class="col-xs-12 col-sm-6">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?php echo $r['Nombre']; ?> </label>
										<div class="col-sm-8">
											<!-- si la pregunta es de tipo radio-->
											<?php
											$radios = explode("|", $r['Valores']);
											for ($i = 0; $i < count($radios); $i++) {
												if ($r['Tipo'] == "radio") {
											?>
													<input type="radio" name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?php echo $radios[$i] ?>" <?= ($radios[$i] == $q_VacunaCampoVacunacion2) ? 'checked' : ''; ?>><?php echo $radios[$i] ?><br>
											<?php }
											} ?>
											<!--end if ** end for--->

											<!-- si la pregunta es de tipo text-->
											<?php if ($r['Tipo'] == "text") { ?>

												<input type="text" id="Campo|<?= $r['IDCampoVacunacion']; ?>" name="Campo|<?= $r['IDCampoVacunacion']; ?>" placeholder="<?php echo $r['EtiquetaCampo']; ?>" class="col-xs-12 <?= $mandatory ?>" title="<?php echo $r['EtiquetaCampo']; ?>" value="<?= $q_VacunaCampoVacunacion2 ?>">
											<?php } ?>
											<!--end if--->
											<!-- si la pregunta es de tipo checkbox-->
											<?php
											$checkbox = explode("|", $r['Valores']);
											for ($i = 0; $i < count($radios); $i++) {
												if ($r['Tipo'] == "checkbox") { ?>

													<input type="checkbox" name="Campo|<?= $r['IDCampoVacunacion']; ?>" placeholder="<?php echo $r['EtiquetaCampo']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?php echo $checkbox[$i] ?>" <?= ($checkbox[$i] == $q_VacunaCampoVacunacion2) ? 'checked' : ''; ?>> <?php echo $checkbox[$i] ?><br>

											<?php }
											} ?>

											<!-- si la pregunta es de tipo textarea-->
											<?php

											if ($r['Tipo'] == "textarea") { ?>

												<textarea name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" cols="30" rows="10"><?= $q_VacunaCampoVacunacion2 ?></textarea>

											<?php }
											?>

											<!-- si la pregunta es de tipo select-->
											<?php if ($r['Tipo'] == "select") { ?>
												<select name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>">
													<option value=""></option>
													<?php
													$select = explode("|", $r['Valores']);
													for ($i = 0; $i < count($select); $i++) {
													?>

														<option value="<?php echo $select[$i] ?>" <?= ($select[$i] == $q_VacunaCampoVacunacion2) ? 'selected' : ''; ?>><?php echo $select[$i] ?></option>
													<?php }
													?>
												</select>
											<?php } ?>

											<!-- si la pregunta es de tipo number-->
											<?php

											if ($r['Tipo'] == "number") { ?>

												<input type="number" name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?= $q_VacunaCampoVacunacion2 ?>">

											<?php }
											?>

											<!-- si la pregunta es de tipo date-->
											<?php

											if ($r['Tipo'] == "date") { ?>

												<input type="date" name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?= $q_VacunaCampoVacunacion2 ?>">

											<?php }
											?>

											<!-- si la pregunta es de tipo time-->
											<?php

											if ($r['Tipo'] == "time") { ?>

												<input type="time" name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?= $q_VacunaCampoVacunacion2 ?>">


											<?php }
											?>

											<!-- si la pregunta es de tipo email-->
											<?php

											if ($r['Tipo'] == "email") { ?>

												<input type="email" name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?= $q_VacunaCampoVacunacion2 ?>">


											<?php }
											?>

											<!-- si la pregunta es de tipo rating-->
											<?php

											for ($i = 1; $i <= 5; $i++) {
												if ($r['Tipo'] == "rating") { ?>
													<input type="radio" name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?php echo $i ?>" <?= ($radios[$i] == $q_VacunaCampoVacunacion2) ? 'checked' : ''; ?>><?php echo $i ?>
											<?php }
											} ?>

											<!-- si la pregunta es de tipo imagen-->
											<?php

											if ($r['Tipo'] == "imagen") { ?>

												<?php if ($q_VacunaCampoVacunacion2) { ?>
													<h5>Imagen actual</h5>
													<img src="<?= VACUNA_ROOT . $q_VacunaCampoVacunacion2 ?>" width="200" height="auto" class="">
													<a href="<?= VACUNA_ROOT . $q_VacunaCampoVacunacion2 ?>" class="ace-icon fa fa-eye">&nbsp;</a>
													<a href="<?= $script . ".php?action=del-vacunaCampoVacuncion2-image&archivo=" . $q_VacunaCampoVacunacion2 . "&num_img=Primera&id=" . $frm_vacuna["IDVacuna"] . "&campo=" . $r['IDCampoVacunacion'] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

												<?php } ?>
												<br />
												<br />
												<input type="file" name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?= $q_VacunaCampoVacunacion2 ?>">

											<?php } ?>
											<!-- si la pregunta es de tipo imagen-->

											<?php if ($r['Tipo'] == "imagenarchivo") { ?>

												<?php if ($q_VacunaCampoVacunacion2) { ?>
													<h5>Imagen actual</h5>
													<iframe src="<?= VACUNA_ROOT . $q_VacunaCampoVacunacion2 ?>" width="100%" height="100%" frameborder="0"></iframe>
													<!-- <img src="<?= VACUNA_ROOT . $q_VacunaCampoVacunacion2 ?>" width="200" height="auto" class=""> -->
													<a href="<?= VACUNA_ROOT . $q_VacunaCampoVacunacion2 ?>" class="ace-icon fa fa-eye">&nbsp;</a>
													<a href="<?= $script . ".php?action=del-vacunaCampoVacuncion2-image&archivo=" . $q_VacunaCampoVacunacion2 . "&num_img=Primera&id=" . $frm_vacuna["IDVacuna"] . "&campo=" . $r['IDCampoVacunacion'] . "&IDSocio=" . $frm_vacuna["IDSocio"] ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>

												<?php } ?>
												<br />
												<br />
												<input type="file" name="Campo|<?= $r['IDCampoVacunacion']; ?>" id="Campo|<?= $r['IDCampoVacunacion']; ?>" class="<?= $mandatory ?>" value="<?= $q_VacunaCampoVacunacion2 ?>">

											<?php }
											?>

											<!-- si la pregunta es de tipo titulo-->
											<?php

											if ($r['Tipo'] == "titulo") { ?>

												<input type="text" id="Campo|<?= $r['IDCampoVacunacion']; ?>" name="Campo|<?= $r['IDCampoVacunacion']; ?>" placeholder="<?php echo $r['EtiquetaCampo']; ?>" class="col-xs-12 <?= $mandatory ?>" title="<?php echo $r['EtiquetaCampo']; ?>" value="<?= $q_VacunaCampoVacunacion2 ?>">


											<?php }
											?>



										</div>
									</div>
								</div>
							<?php endwhile; ?>
							<input type="hidden" name="campos_dinamicos[keys]" value="<?php echo $key ?>">

							<div class="clearfix form-actions">
								<div class="col-xs-12 text-center">
									<input type="hidden" name="IDVacuna[]" id="IDVacuna" value="<?php echo $frm_vacuna["IDVacuna"] ?>" />
									<input type="hidden" name="IDDosis[]" id="IDDosis" value="<?= $r_Dosis["IDDosis"] ?>" />
									<input type="hidden" name="EstoyVacunado" id="EstoyVacunado" value="S" />
									<input type="hidden" name="action" id="action" value="update-vacuna2" />
									<input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $get["id"] ?>" />
									<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																							else echo $frm["IDClub"];  ?>" />
									<button class="btn btn-info btnEnviar" type="button" rel="frm<?php echo $script . $cont; ?>">
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?php echo $titulo_accion; ?> Dosis
									</button>
									<input type="hidden" name="IDA" id="IDA" value="<?php echo $frm[$key] ?>" />
									<input type="hidden" name="IDB" id="IDB" value="<?php echo $frm[$key] ?>" />
								</div>
							</div>

						</form>
					<?php
						$cont++;
					}
					?>
				</div>
			</div>
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->