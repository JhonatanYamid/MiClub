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


					<form class="form-horizontal formvalida" role="form" method="post" id="frmBuscarCurso" name="frmBuscarCurso" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Sede', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CursoSede", "Nombre", "Nombre", "IDCursoSede", $frm["IDCursoSede"], "[Seleccione]", "form-control ", "title = \"Sede\"", " and IDClub = '" . SIMUser::get("club") . "' " . $condicion_sede); ?>
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoCurso', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CursoTipo", "Nombre", "Nombre", "IDCursoTipo", $frm["IDCursoTipo"], "[Seleccione]", "form-control", "title = \"Horario\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
								</div>
							</div>

						</div>

						<div class="form-group first ">


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Profesor', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("CursoEntrenador", "Nombre", "Nombre", "IDCursoEntrenador", $frm["IDCursoEntrenador"], "[Seleccione]", "form-control", "title = \"Entrenador\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Accion" name="Accion" placeholder="<?= SIMUtil::get_traduccion('', '', 'AccionNombreApellidoNumeroDocumento', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax" title="Accion" <?php if ($_GET["action"] != "add") echo "readonly"; ?> value="<?php echo utf8_encode($r_socio["Apellido"] . " " . $r_socio["Nombre"] . $label_accion) ?>">
									<?= SIMUtil::get_traduccion('', '', 'Busquedapor:Accion,Nombre,Apellido,NumeroDocumento', LANGSESSION); ?>
									<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" title="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>">
								</div>
							</div>

						</div>



						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="BuscarCurso" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<button class="btn btn-info btnEnviar" type="button" rel="frmBuscarCurso">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= SIMUtil::get_traduccion('', '', 'BuscarCurso', LANGSESSION); ?>
								</button>


							</div>
						</div>

					</form>

					<div id="tab" class="tab-pane">

						<?php if ($resultado["success"]) { ?>
							<form class="form-horizontal formvalida" role="form" method="post" id="frmInscribirCurso" name="frmInscribirCurso" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">
								<table class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th><?= SIMUtil::get_traduccion('', '', 'Cursos', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'Nivel', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'Edad', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'Sede', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'dia', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'FechaInicio-Fin', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'Hora', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'Entrenador', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'ValorMes', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'ValorTrimestre', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'Cupos', LANGSESSION); ?></th>
											<th><?= SIMUtil::get_traduccion('', '', 'Inscribir', LANGSESSION); ?></th>
										</tr>
									</thead>
									<tbody>
										<?


										foreach ($resultado["response"] as $key_dato => $datos) {
											$contador++;
										?>
											<tr>
												<td>
													<?php echo $datos["Nombre"];  ?>
												</td>
												<td>
													<?php echo $datos["Nivel"];  ?>
												</td>
												<td>
													<?php echo $datos["Edad"];  ?>
												</td>
												<td>
													<?php echo $datos["Sede"];  ?>
												</td>
												<td>
													<?php echo $datos["Dia"];  ?>
												</td>
												<td>
													<?php echo $datos["FechaInicio"] . " al " . $datos["FechaFin"];  ?>
												</td>
												<td>
													<?php echo $datos["HoraDesde"];  ?>
												</td>
												<td>
													<?php echo $datos["Entrenador"];  ?>
												</td>
												<td>
													<?php echo "$" . number_format($datos["ValorMes"], 0, '', '.');  ?>
												</td>
												<td>
													<?php echo "$" . number_format($datos["ValorTrimestre"], 0, '', '.');  ?>
												</td>
												<td align="center">
													<?php
													$inscritos = SIMWebServiceApp::get_curso_inscritos($frm["IDClub"], $datos["IDCursoHorario"], $datos["IDCursoCalendario"], $datos["HoraDesde"]);
													$total_cupos = (int)$datos["Cupo"] - (int)$inscritos;
													echo $total_cupos;
													?>
												</td>
												<td class="hidden-480">
													<?php if ($total_cupos > 0) {
														$datos_encode = json_encode($datos);
														$datosurl = base64_encode($datos_encode);

													?>
														<a class="fancybox" data-fancybox-type="iframe" href="detalle_curso_inscripcion.php?IDSocio=<?php echo $_POST["IDSocio"] ?>&IDClub=<?php echo SIMUser::get("club"); ?>&valor=<?php echo $datos["ValorMes"] ?>&calendario=<?php echo $datos["IDCursoCalendario"] ?>&horadesde=<?php echo $datos["HoraDesde"] ?>&cupos=<?php echo $datos["Cupo"]; ?>&IDCursoHorario=<?php echo $datos["IDCursoHorario"] ?>&datosseleccion=<?php echo $datosurl; ?>">
															<?= SIMUtil::get_traduccion('', '', 'Inscribir', LANGSESSION); ?>
														</a>
														<!--<a href="#inscribircurso" class="btnInscribirCurso" valor="<?php echo $datos["ValorMes"] ?>" calendario="<?php echo $datos["IDCursoCalendario"] ?>" horadesde="<?php echo $datos["HoraDesde"] ?>" cupos="<?php echo $datos["Cupo"]; ?>" rel="<?php echo $datos["IDCursoHorario"] ?>" consecutivo="<?php echo $contador; ?>" ><span id="txtmsjreserva<?php echo $contador ?>">Inscribir</span></a>-->
													<?php } else {
														echo "No hay cupos";
													} ?>
												</td>
											</tr>
										<?php
										}
										?>
									</tbody>
								</table>

								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="IDCursoHorario" id="IDCursoHorario" value="" />
								<input type="hidden" name="IDCursoCalendario" id="IDCursoCalendario" value="" />
								<input type="hidden" name="Cupos" id="Cupos" value="" />
								<input type="hidden" name="Valor" id="Valor" value="" />
								<input type="hidden" name="HoraDesde" id="HoraDesde" value="" />
								<input type="hidden" name="IDSocio" id="IDSocio" value="<?php echo $_POST["IDSocio"] ?>" />
								<input type="hidden" name="action" id="action" value="insert" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />

							</form>
						<?php } else {
							echo $resultado["message"];
						}
						?>

					</div>


				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
include("cmp/footer_grid.php");
?>