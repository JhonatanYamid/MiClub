<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i>CREAR UN NUEVO <?= strtoupper(SIMReg::get("title")) ?>
		</h4>
	</div>
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="row">
				<div class="col-xs-12">
					<!-- PAGE CONTENT BEGINS -->

					<form class="form-horizontal formvalida" role="form" method="post" name="frm" id="frm" action="<?= SIMUtil::lastURI() ?>" enctype="multipart/form-data">


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Socio</label>

								<div class="col-sm-8">
									<input type="text" id="Accion" name="Accion" placeholder="Número de Derecho" class="col-xs-12 mandatory autocomplete-ajax" title="número de derecho" <?php if ($newmode == "updateingreso") echo "readonly"; ?> value="<?= $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $frm["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $frm["IDSocio"] . "'") ?>">
									<input type="hidden" name="IDSocio" value="1<?= $frm["IDSocio"]; ?>" id="IDSocio" class="mandatory" title="Socio">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha Ingreso</label>

								<div class="col-sm-8">
									<input type="text" id="FechaIngreso" name="FechaIngreso" placeholder="Fecha Ingreso" class="col-xs-12 <?php if ($newmode != "updateingreso") echo "calendariohoy"; ?> " title="Fecha Ingreso" value="<?php if ($frm["FechaIngreso"] == "0000-00-00" || $frm["FechaIngreso"] == "") echo date("Y-m-d");
																																																											else echo $frm["FechaIngreso"]; ?>" <?php if ($newmode == "updateingreso") echo "readonly"; ?>>
								</div>
							</div>

						</div>


						<?php
						for ($cont_invitado = 1; $cont_invitado <= 1; $cont_invitado++) : ?>
							<div class="widget-header">
								<h4 class="widget-title lighter smaller">
									<i class="ace-icon fa fa-user orange"></i>Invitado <?= $cont_invitado; ?>
								</h4>
							</div>
							<div class="form-group first ">

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Documento Invitado <?= $cont_invitado; ?></label>

									<div class="col-sm-8">
										<input id="NumeroDocumento<?= $cont_invitado; ?>" type="text" size="25" title="Numero Documento" name="NumeroDocumento<?= $cont_invitado; ?>" alt="<?= $cont_invitado; ?>" class="input autocomplete-ajax_invitado" value="<?php if (!empty($frm["NumeroDocumento"]) && $cont_invitado == 1) {
																																																																		echo $frm["NumeroDocumento"];
																																																																	} ?>" />
										<input type="hidden" name="IDSocioInvitado<?= $cont_invitado; ?>" value="<?= $frm["NumeroDocumento"]; ?>" id="IDSocioInvitado<?= $cont_invitado; ?>" alt="<?= $cont_invitado; ?>" title="Numero Documento">

									</div>
								</div>

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre Invitado <?= $cont_invitado; ?></label>

									<div class="col-sm-8">
										<input id="Nombre<?= $cont_invitado; ?>" type="text" size="25" title="Nombre" name="Nombre<?= $cont_invitado; ?>" alt="<?= $cont_invitado; ?>" class="input " value="<?php if (!empty($frm["Nombre"]) && $cont_invitado == 1) {
																																																					echo $frm["Nombre"];
																																																				} ?>" />
										<?php
										// if ($cont_invitado == 1 && (int)$frm["IDSocioInvitado"] > 0) {
										// 	//otros datos
										// 	$sql_otros = "SELECT * FROM InvitadosOtrosDatos WHERE IDInvitacion = '" . $frm["IDSocioInvitado"] . "'";
										// 	$r_otros = $dbo->query($sql_otros);
										// 	while ($row_otros = $dbo->fetchArray($r_otros)) {
										// 		echo $otros_datos = "<br>" . $dbo->getFields("CampoFormularioInvitado", "EtiquetaCampo", "IDCampoFormularioInvitado = '" . $row_otros["IDCampoFormularioInvitado"] . "'") . ":" . $row_otros["Valor"];
										// 	}
										// }
										?>

									</div>
								</div>

							</div>
							<?php
							// Insertamos los campos dinamicos al formulario si el club lo solicito

							$response_campo_formulario = array();
							$sql_campo_form = "SELECT * FROM CampoFormularioInvitado WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
							$qry_campo_form = $dbo->query($sql_campo_form);
							if ($dbo->rows($qry_campo_form) > 0) { ?>
								<div class="form-group first ">
									<?php
									while ($r_campo = $dbo->fetchArray($qry_campo_form)) :
										$mandatory = ($r_campo['Obligatorio'] == 'S') ? 'mandatory' : '';

										//otros datos
										$sql_otros = "SELECT * FROM InvitadosOtrosDatos WHERE IDInvitacion = '" . $frm["IDSocioInvitado"] . "' AND IDCampoFormularioInvitado = " . $r_campo['IDCampoFormularioInvitado'];
										$r_otros = $dbo->query($sql_otros);
										$row_otros = $dbo->assoc($r_otros);
										$r_campo["Valor"] = ($row_otros > 0) ? $row_otros['Valor'] : "";
									?>
										<div class="col-xs-12 col-sm-6 first">
											<?php
											switch ($r_campo['TipoCampo']):
												case 'textarea': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<textarea id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>"><?= $row_otros["Valor"]; ?></textarea>
													</div>
												<?php
													break;
												case 'radio': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<?php
														$options = explode(',', $r_campo['Valores']);
														$radiogroup = "";
														foreach ($options as $key => $val) {
															$val = trim($val); //Eliminar espacios

															$radiogroup .= ' <label class="radiogroup"><input type="radio" name="' . $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado . '" id="' . $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado . '" title="' . $r_campo['EtiquetaCampo'] . '" class="' . $mandatory . '" value="' . $val . '"';

															$radiogroup .= ($val == $row_otros['Valor']) ? " checked" : "";

															$radiogroup .= "> " . $val . "</label>";
														}
														echo $radiogroup;
														?>
													</div>
												<?php
													break;
												case 'checkbox': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<?php
														$options = explode(',', $r_campo['Valores']);
														$respuesta  = explode(',', $row_otros['Valor']);
														foreach ($options as $i => $option) {
															$checked = (in_array($option, $respuesta)) ? "checked" : ""; ?>
															<input type="checkbox" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado . "[]" ?>" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado ?>" class="<?= $mandatory ?>" value="<?= $option ?>" <?= $checked ?>>
															&nbsp;<?= $option ?>
														<?php
														}
														?>


													</div>
												<?php
													break;
												case 'select': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<select name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>">
															<?php
															$options = explode(',', $r_campo['Valores']);
															foreach ($options as $key => $value) :
																$selected = ($value == $row_otros['Valor']) ? "selected" : "";
															?>
																<option value="<?= $value ?>" <?= $selected ?>><?= $value ?></option>
															<?php
															endforeach;
															?>
														</select>
													</div>
												<?php
													break;
												case 'number': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<input type="number" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
													</div>
												<?php
													break;
												case 'date': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<input type="date" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
													</div>
												<?php
													break;
												case 'time': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<input type="time" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
													</div>
												<?php
													break;
												case 'email': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<input type="email" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
													</div>
												<?php
													break;
												case 'rating': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<i class="fa fa-star"> <?= $row_otros['Valor'] ?> </i>
													</div>
												<?php
													break;
												case 'imagen': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<input type="file" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" class="col-xs-12 <?= $mandatory ?>">
														<? if (!empty($r_campo['Valor'])) { ?>
															<a target="_blank" href="<?= PQR_ROOT . $row_otros['Valor'] ?>">
																<?php //echo mb_strimwidth($r_campo['Valor'], 0, 45, '...');
																?>
																Ver archivo
															</a>
														<?
														} // END if
														?>
													</div>
												<?php
													break;

												case 'imagenarchivo': ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<input type="file" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" class="col-xs-12 <?= $mandatory ?>">

														<? if (!empty($r_campo['Valor'])) { ?>
															<a target="_blank" href="<?= PQR_ROOT . $row_otros['Valor'] ?>">
																<?php //echo mb_strimwidth($r_campo['Valor'], 0, 45, '...');
																?>
																Ver archivo
															</a>
														<?
														} // END if
														?>
													</div>
												<?php
													break;

												case 'titulo': ?>
													<!-- <div class="col-sm-12">
														<h3 style="text-align: center;"><?= $r_campo['EtiquetaCampo'] ?></h3>
													</div> -->
												<?php
													break;

												default: ?>
													<label class="col-sm-4 control-label no-padding-right" for="Valor"> <?= $r_campo['EtiquetaCampo']; ?> </label>
													<div class="col-sm-8">
														<input type="text" id="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" name="<?= $r_campo['IDCampoFormularioInvitado'] . '-' . $cont_invitado; ?>" placeholder="" class="col-xs-12 <?= $mandatory; ?>" title="<?= $r_campo['EtiquetaCampo'] ?>" value="<?= $row_otros["Valor"]; ?>">
													</div>
											<?php
													break;
											endswitch;
											?>
										</div>


									<?php endwhile; //end while 
									?>
								</div>
						<?php
							}

						endfor;

						?>

						<input type="checkbox" name="NotificarZeus" id="NotificarZeus"> Notificar Zeus

						<?php
						if ($newmode == "updateobservacion") : ?>
							<div class="form-group first">

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones</label>

									<div class="col-sm-8">
										<textarea id="Observaciones" rows="4" title="Observaciones" name="Observaciones" class="form-control" /><?= $frm["Observaciones"] ?></textarea>
									</div>
								</div>

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Fecha/Hora Ingreso</label>

									<div class="col-sm-8">
										<input type="text" id="FechaIngresoClub" name="FechaIngresoClub" placeholder="Fecha Ingreso Club" class="col-xs-12" title="Fecha Ingreso Club" value="<?php if ($newmode == "updateingreso") : echo date("Y-m-d H:i:s");
																																																else : echo "";
																																																endif; ?>" readonly>
									</div>
								</div>

							</div>






						<?php endif; ?>




						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?= $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?= $newmode ?>" />
								<input type="hidden" name="IDClub" id="IDClub" value="<?php if (empty($frm["IDClub"])) echo SIMUser::get("club");
																						else echo $frm["IDClub"];  ?>" />
								<input type="hidden" name="NumeroInvitados" id="NumeroInvitados" value="<?= $cont_invitado;  ?>" />

								<button class="btn btn-info btnEnviar" type="button" rel="frm">
									<i class="ace-icon fa fa-check bigger-110"></i>
									<?= $titulo_accion; ?> <?= SIMReg::get("title") ?>
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

<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>