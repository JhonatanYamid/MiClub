<div class="widget-box transparent" id="recent-box">

	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->


					<form class="form-horizontal formvalida" role="form" method="post" id="frmRegistroV" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-8 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Numerodedocumentooaccion', LANGSESSION); ?> </label>

								<div class="col-sm-4">
									<input type="text" id="NumeroDocumento" name="NumeroDocumento" placeholder="<?= SIMUtil::get_traduccion('', '', 'Numerodedocumentooaccion', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Numerodedocumentooaccion', LANGSESSION); ?>" value="">
									<button class="btn btn-info btnEnviar" type="submit" rel="frm<?php echo $script; ?>">
										<i class="ace-icon fa fa-check bigger-110"></i>
										<?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?>
									</button>
									<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
									<input type="hidden" name="action" id="action" value="BuscarVotante" />
									<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																							else echo $frm["IDClub"];  ?>" />
								</div>
							</div>

						</div>

					</form>

					<?php
					//echo " Cedula = '".base64_decode($_GET["Parametro"])."' and IDVotacionEvento = '".$_GET["id"]."' ";
					$datos_votante = $dbo->fetchAll("VotacionVotante", " Cedula = '" . base64_decode($_GET["Parametro"]) . "' and IDVotacionEvento = '" . $_GET["id"] . "' ", "array");
					if ((int)$datos_votante["IDVotacionVotante"] > 0) {
						if ($datos_votante["Presente"] == "S") {
							$checksi = "checked";
							$checkno = "";
							$label_estado =  SIMUtil::get_traduccion('', '', 'Registrado', LANGSESSION);
							$class_estado = "green";
						} else {
							$checkno = "checked";
							$checksi = "";
							$label_estado = SIMUtil::get_traduccion('', '', 'NORegistrado', LANGSESSION);
							$class_estado = "red";
						}
					?>
						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<tr>
								<td>
									<h3>Estado</h3>
								</td>
								<td colspan="3" class="<?php echo $class_estado; ?>">
									<h3>
										<div id="msgestadoregistro"><?php echo $label_estado;  ?></div>
									</h3>
								</td>
								<td>
									<h3><?= SIMUtil::get_traduccion('', '', 'Totalcoeficienterepresentado', LANGSESSION); ?></h3>
								</td>
								<td colspan="3" class="green">
									<h3>
										<?php
										$coeficiente = SIMUtil::verifica_coeficiente($datos_votante["IDSocio"], $_GET["id"]);
										/*
										$sql_sumapoderes="SELECT Coeficiente FROM VotacionVotante WHERE IDVotacionVotante in (SELECT IDVotacionVotanteDelegaPoder FROM VotacionPoder WHERE IDVotacionVotante = '".$datos_votante["IDVotacionVotante"]."') ";
										$r_sumapoderes=$dbo->query($sql_sumapoderes);
										while($row_poderes=$dbo->fetchArray($r_sumapoderes)){
											$suma_otorgados+=$row_poderes["Coeficiente"];
										}
										$coeficiente_total=$suma_otorgados+$datos_votante["Coeficiente"];
										*/
										echo $coeficiente;
										?>
									</h3>
								</td>
							</tr>
							<tr>
								<td>
									<h3>ID</h3>
								</td>
								<td class="green">
									<h3><?php echo $datos_votante["Cedula"];  ?></h3>
								</td>
								<td>
									<h3><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></h3>
								</td>
								<td class="green">
									<h3><?php echo $datos_votante["Nombre"];  ?></h3>
								</td>
								<td>
									<h3><?= SIMUtil::get_traduccion('', '', 'Coeficiente', LANGSESSION); ?></h3>
								</td>
								<td class="green">
									<h3><?php echo $datos_votante["Coeficiente"];  ?></h3>
								</td>
								<td>
									<h3><?= SIMUtil::get_traduccion('', '', 'Lote', LANGSESSION); ?></h3>
								</td>
								<td class="green">
									<h3><?php echo $datos_votante["NumeroCasa"];  ?></h3>
								</td>
							</tr>
						</table>

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-download green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Votacion', LANGSESSION); ?>
							</h3>
						</div>

						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<tbody>
								<tr>
									<td style="width:250px !important;"><b><?= SIMUtil::get_traduccion('', '', 'Presente', LANGSESSION); ?></b></td>
									<td>
										<input type="radio" value="S" class="btnvotantepresente" name="VotantePresente<?php echo $datos_votante["IDVotacionVotante"]; ?>" idvotante="<?php echo $datos_votante["IDVotacionVotante"]; ?>" usuarioregistra="<?php echo SIMUser::get("IDUsuario"); ?>" <?php echo $checksi ?>><?= SIMUtil::get_traduccion('', '', 'Si', LANGSESSION); ?>
										<input type="radio" value="N" class="btnvotantepresente" name="VotantePresente<?php echo $datos_votante["IDVotacionVotante"]; ?>" idvotante="<?php echo $datos_votante["IDVotacionVotante"]; ?>" usuarioregistra="<?php echo SIMUser::get("IDUsuario"); ?>" <?php echo $checkno ?>><?= SIMUtil::get_traduccion('', '', 'No', LANGSESSION); ?>
										<div name='msgupdate<?php echo $datos_votante["IDVotacionVotante"]; ?>' id='msgupdate<?php echo $datos_votante["IDVotacionVotante"]; ?>'></div>
									</td>
								</tr>

								<tr>
									<td><b><?= SIMUtil::get_traduccion('', '', 'OtorgarPodera', LANGSESSION); ?>: </b></td>
									<td>
										<button class="btn btn-info fancyboxpoder" href="registrapoder.php?Tipo=Propietario&IDVotantePadre=<?php echo $datos_votante["IDVotacionVotante"]; ?>&IDClub=<?php echo SIMUser::get("club"); ?>&IDVotacionEvento=<?php echo $_GET["id"]; ?>&IDUsuarioRegistra=<?php echo SIMUser::get("IDUsuario"); ?>" data-fancybox-type="iframe">
											<i class="ace-icon fa  fa-exchange align-top bigger-125"></i>
											<?= SIMUtil::get_traduccion('', '', 'Propietario', LANGSESSION); ?>
										</button>

										<button class="btn btn-info fancyboxpoder" href="registrapoder.php?Tipo=Externo&IDVotantePadre=<?php echo $datos_votante["IDVotacionVotante"]; ?>&IDClub=<?php echo SIMUser::get("club"); ?>&IDVotacionEvento=<?php echo $_GET["id"]; ?>&IDUsuarioRegistra=<?php echo SIMUser::get("IDUsuario"); ?>" data-fancybox-type="iframe">
											<i class="ace-icon fa  fa-exchange align-top bigger-125"></i>
											<?= SIMUtil::get_traduccion('', '', 'Externo', LANGSESSION); ?>
										</button>

									</td>
								</tr>
							<tbody>
						</table>

						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<tr>
								<th colspan="6"><?= SIMUtil::get_traduccion('', '', 'PoderOtorgadoa', LANGSESSION); ?>:</th>

							</tr>
							<tr>
								<th><?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?></th>
								<th><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></th>
								<th><?= SIMUtil::get_traduccion('', '', 'Predio', LANGSESSION); ?></th>
								<th><?= SIMUtil::get_traduccion('', '', 'Coeficiente', LANGSESSION); ?></th>
								<th><?= SIMUtil::get_traduccion('', '', 'Moroso', LANGSESSION); ?> ?</th>
								<th><?= SIMUtil::get_traduccion('', '', 'EliminarPoder', LANGSESSION); ?></th>
							</tr>
							<tbody id="listacontactosanunciante">
								<?php
								$sql_poder = "SELECT IDVotacionVotanteDelegaPoder,IDVotacionPoder,IDVotacionVotante FROM VotacionPoder WHERE IDVotacionVotanteDelegaPoder = '" . $datos_votante["IDVotacionVotante"] . "'";
								$r_poder = $dbo->query($sql_poder);
								while ($row_poder = $dbo->fetchArray($r_poder)) {
									$datos_otorga = $dbo->fetchAll("VotacionVotante", " IDVotacionVotante = '" . $row_poder["IDVotacionVotante"] . "' ", "array");
								?>
									<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
										<td><?php echo $datos_otorga["Cedula"]; ?></td>
										<td><?php echo $datos_otorga["Nombre"]; ?></td>
										<td><?php echo $datos_otorga["NumeroCasa"]; ?></td>
										<td><?php echo $datos_otorga["Coeficiente"]; ?></td>
										<td><?php echo $datos_otorga["Moroso"]; ?></td>
										<td>
											<?php echo '<a class="green" href="votacionesevento.php?action=EliminaPoder&IDVotacionPoder=' . $row_poder["IDVotacionPoder"] . '&tabclub=votantes&IDVotacionEvento=' . $datos_otorga["IDVotacionEvento"] . '">Eliminar</a>'; ?>
										</td>
									</tr>
								<?php } ?>
							</tbody>

						</table>

						<table id="simple-table" class="table table-striped table-bordered table-hover">
							<tr>
								<th colspan="6"><?= SIMUtil::get_traduccion('', '', 'PODERES', LANGSESSION); ?>:</th>

							</tr>
							<tr>
								<th><?= SIMUtil::get_traduccion('', '', 'Cedula', LANGSESSION); ?></th>
								<th><?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?></th>
								<th><?= SIMUtil::get_traduccion('', '', 'Predio', LANGSESSION); ?></th>
								<th><?= SIMUtil::get_traduccion('', '', 'Coeficiente', LANGSESSION); ?></th>
								<th><?= SIMUtil::get_traduccion('', '', 'Moroso', LANGSESSION); ?> ?</th>
								<th><?= SIMUtil::get_traduccion('', '', 'EliminarPoder', LANGSESSION); ?></th>
							</tr>
							<tbody id="listacontactosanunciante">
								<?php
								$sql_poder = "SELECT IDVotacionVotanteDelegaPoder,IDVotacionPoder,IDVotacionVotante FROM VotacionPoder WHERE IDVotacionVotante = '" . $datos_votante["IDVotacionVotante"] . "'";
								$r_poder = $dbo->query($sql_poder);
								while ($row_poder = $dbo->fetchArray($r_poder)) {
									$datos_otorga = $dbo->fetchAll("VotacionVotante", " IDVotacionVotante = '" . $row_poder["IDVotacionVotanteDelegaPoder"] . "' ", "array");
								?>
									<tr class="<?php echo SIMUtil::repetition() ? 'row0' : 'row1' ?>">
										<td><?php echo $datos_otorga["Cedula"]; ?></td>
										<td><?php echo $datos_otorga["Nombre"]; ?></td>
										<td><?php echo $datos_otorga["NumeroCasa"]; ?></td>
										<td><?php echo $datos_otorga["Coeficiente"]; ?></td>
										<td><?php echo $datos_otorga["Moroso"]; ?></td>
										<td>
											<?php echo '<a class="green" href="votacionesevento.php?action=EliminaPoder&IDVotacionPoder=' . $row_poder["IDVotacionPoder"] . '&tabclub=votantes&IDVotacionEvento=' . $datos_otorga["IDVotacionEvento"] . '">Eliminar</a>'; ?>
										</td>
									</tr>
								<?php } ?>
							</tbody>

						</table>




					<?php } elseif (!empty($_GET["Parametro"])) {
						echo SIMUtil::get_traduccion('', '', 'Noencontradoporfavorverifique', LANGSESSION);
					}
					?>


				</div>
			</div>




		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div><!-- /.widget-box -->