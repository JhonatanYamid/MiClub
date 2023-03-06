<?php

require_once LIBDIR . "SIMWebServiceUsuarios.inc.php";

$hora_actual_sistema_valida = date("H:i:s");
$fecha_actual =  date("Y-m-d");

$fechats = strtotime($fecha_actual);

$socioRoot = str_replace("/", "^", SOCIO_ROOT);
$socioRoot = str_replace("_", "~", $socioRoot);

$fotoSocio = "";
if ($EsSocio == "S") {
	$foto = str_replace("_", "~", $Socio['Foto']);
	$fotoSocio = $socioRoot . $foto;

	//datos vehiculo
	$sql_vehiculo = "Select * From Vehiculo WHERE IDSocio = $ID";
	$result_vehiculo = $dbo->query($sql_vehiculo);
	$cont_vehiculo = 0;
	while ($row_vehiculo = $dbo->fetchArray($result_vehiculo)) :
		$array_placa[] = strtoupper($row_vehiculo["Placa"]);
	endwhile;
}
?>
<style>
	#jqGrid_container {
		display: flex;
		flex-wrap: wrap;
		flex-direction: row;
		gap: 1%;
	}
</style>


<?php

$sql_borro_ant = $dbo->query("Delete from LogAccesoDiario Where FechaTrCr < '" . date("Y-m-d") . "'");
if ($total_resultados >= 1) :

	$sql_log_acceso_ultimo = "Select * From LogAcceso Where IDInvitacion = '" . $ID . "' AND FechaTrCr = '" . date("Y-m-d") . "' Order by IDLogAcceso Desc Limit 1";
	$result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
	$row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
	$total_log = $dbo->rows($result_log_acceso_ultimo);

	if ($total_log > 0) {
		$mensaje_alerta = "¡Atención, La persona ya ha ingresado al evento el día de hoy!";
	}

	$mecanismoLog = $row_log_acceso_ultimo["Mecanismo"];
	$mecanismoLog = explode(" ", $mecanismoLog);
	$mecanismo = $mecanismoLog[0];
	$placa = $mecanismoLog[1];

	$SalidaLog =  $row_log_acceso_ultimo["Salida"]; ?>
	<div class="widget-box transparent" id="recent-box">

		<?php

		if (isset($AlertaIngreso)) { ?>
			<span style='color: #F00; font-size:16px'>
				<?= $AlertaIngreso ?>
			</span>
			<br><br><br>
		<?php }

		$estado_socio = $dbo->getFields('EstadoSocio', "IDEstadoSocio", " IDEstadoSocio = '" . $Socio["IDEstadoSocio"] . "'");
		switch ($estado_socio):
			case "1":
				$estado_socio_color = "color: #4fb852;";
				break;
			case "2":
				$estado_socio_color = "color: #3a4694;";
				break;
			case "3":
				$estado_socio_color = "color: #F10004;";
				break;
			case "4":
				$estado_socio_color = "color: #F10004;";
				break;
			case "6":
				$estado_socio_color = "color: #F10004;";
				break;
			default:
				/* 	$estado_socio_color= "color: #F10004;"; */
				break;

		endswitch;
		?>
		<div class="widget-header">

			<b>Tipo: <span style="color: #000;"><?php echo "Evento {$Evento['Titular']}" ?></span></b>
			<b>Fecha Inicio Aut:</b> <?= $Evento["FechaInicio"] ?>
			<b>Fecha Fin Aut:</b> <?= $Evento["FechaFinEvento"] ?>


			<?= (($Accion != "") ? "<b>Acci&oacute;n:</b>  {$Accion}" : ""); ?>
			<b><?= "Invitado:"; ?></b> <?= $Nombre; ?>
			<b>Tipo invitado:</b> <?= ($EsSocio == "S") ? "Socio" : "No Socio"; ?>
			<b>Documento de Identidad:</b> <?= $NumeroDocumento; ?>
			<?php
			if ($EsSocio == "S") { ?>
				<b style=<?php echo "'$estado_socio_color'" ?>>Estado Socio: </b>
				<b style=<?php echo "'$estado_socio_color'" ?>>
					<?php echo $dbo->getFields('EstadoSocio', "Nombre", " IDEstadoSocio = '" . $Socio["IDEstadoSocio"] . "'"); ?>
				</b>
			<?php } ?>
			</br>
			</br>

		</div>

		<div class="widget-body">
			<div class="widget-main padding-4">
				<div class="row">
					<div class="col-xs-12">
						<div id="jqGrid_container">
							<table id="simple-table" class="table table-striped table-bordered table-hover" style="width:15% !important">
								<tr>
									<td>
										<table class="table table-striped table-bordered table-hover">
											<tr>
												<td valign="top" width="100">
													<?
													if ($EsSocio == "S") :
														$ruta_foto = SOCIO_ROOT;
														$nombre_foto = "Foto";
														$identificador = $ID;
													else :
														$ruta_foto = IMGINVITADO_ROOT;
														$nombre_foto = "FotoFile";
														$identificador = $ID;
													endif;

													if (!empty($datos_invitado[$nombre_foto])) {
														echo "<img src='" . $ruta_foto . "$datos_invitado[$nombre_foto]' width='100' height='120'  >";
													} else {
														echo "<img src='assets/images/sinfoto.png' width='100' height='120'> ";
													}
													?>
													<a class="fancybox" href="../admin/tomarfoto/webcamjquery/foto.php?action=foto&IDRegistro=<?= $identificador; ?>&Modulo=<?php echo $modulo; ?>" data-fancybox-type="iframe">
														<i class="ace-icon fa fa-camera bigger-120"></i>
														<span class="bigger-110">Tomar Foto</span>
													</a>

												</td>
												<td valign="top">
													<table class="table table-striped table-bordered table-hover">
														<tr>
															<td>&nbsp;
																<?= $Nombre; ?>
															</td>

														</tr>

														<?php if ($FechaNacimiento) { ?>
															<tr>
																<td>&nbsp;
																	Fecha Nacimiento: <?php echo $datos_invitado["FechaNacimiento"] ?>
																</td>
															</tr>
															<tr>
																<td style="color: #f00000;">&nbsp;
																	<strong>
																		Edad: <?= SIMUtil::Calcular_Edad($FechaNacimiento) ?>
																	</strong>
																</td>
															</tr>
														<?php } ?>
													</table>
												</td>
											</tr>
											<tr>
												<?php
												//Verifico Cual fue el ultimo movimeinto registrado en el dia para saber si se activa la entrada o la salida
												$sql_log_acceso_ultimo = "Select * From LogAccesoDiario Where IDInvitacion = '" . $ID . "' Order by IDLogAcceso Desc Limit 1";
												$result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
												$row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
												$total_log = $dbo->rows($result_log_acceso_ultimo);

												$mecanismoLog = $row_log_acceso_ultimo["Mecanismo"];
												$mecanismoLog = explode(" ", $mecanismoLog);
												$mecanismo = $mecanismoLog[0];
												$placa = $mecanismoLog[1];

												$SalidaLog =  $row_log_acceso_ultimo["Salida"];

												?>

												<td colspan="2">
													<label>
														<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" id="PeatonalTitular" value="Peatonal" <?php echo ((SIMUser::get("club") == 44) ? " checked" : "") ?> />
														<span class="lbl">Peatonal</span>
													</label>
													<?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 52) { ?>
														<label>
															<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" value="Acompañante Vehiculo" />
															<span class="lbl">Acompañante vehiculo</span>
														</label>
													<?php } ?>
													<?php if (SIMUser::get("club") == 8 || SIMUser::get("club") == 16) { ?>
														<label>
															<input name="MecanismoEntradaIngreso" id="Occidental" type="radio" class="ace input-lg tipoentrada" value="Occidental" />
															<span class="lbl">Occidental</span>
														</label>
													<?php } ?>
													<label>
														<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" id="VehiculoTitular" value="Vehiculo" <?= (((SIMUser::get("club") == 8  || SIMUser::get("club") == 10) && $mecanismo == "Vehiculo" && $SalidaLog = "S") ? " checked" : "") ?> />
														<span class="lbl">Vehiculo</span>
													</label>
													<input type="text" placeholder="Ingrese placa" id="PlacaVehiculoTitular" name="PlacaVehiculo" class="PlacaVehiculo" <?php echo (((SIMUser::get("club") == 8  || SIMUser::get("club") == 10) && $mecanismo == "Vehiculo" && $SalidaLog = "S") ? ' value="' . "$placa"  : "") ?>>
													<?php
													if (count($array_placa) > 0) :
														foreach ($array_placa as $placa_vehiculo) : ?>
															<label>
																<input name="MecanismoEntradaIngreso" type="radio" class="ace input-lg tipoentrada" value="Vehiculo <?php echo $placa_vehiculo; ?>" />
																<span class="lbl"><?php echo $placa_vehiculo; ?></span>
															</label>
													<?php
														endforeach;
													endif;
													?>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<?php

													if ($Socio["IDEstadoSocio"] == "2" || $Socio["IDEstadoSocio"] == "3")
														$bloqueado = "S";
													echo $mensaje_alerta
													?>
													<label>
														<?php if ($bloqueado <> "S") : ?>

															<?php
															if ($row_log_acceso_ultimo["Entrada"] == "S") :
																$campo_entrada = "disabled";
															else :
																$campo_entrada = "";
															endif;

															if ($row_log_acceso_ultimo["Salida"] == "S" || $total_log == 0) :
																$campo_salida = "disabled";
															else :
																$campo_salida = "";
															endif;

															if ($PermitirMultipleAcceso == 'S' || $PermitirMultipleAcceso == '') {
																$campo_entrada = "";
																$campo_salida = "";
															}
															?>
															<input name="Ingreso_" id="Ingreso" class="ace input-lg ace-checkbox-2 ingreso_accesov2" type="checkbox" tipoacceso="unico" alt="<?php echo $modulo; ?>" title="<?php echo $ID; ?>" <?php echo $campo_entrada; ?> />
															<span class="lbl"><b>INGRESO</b></span>
													</label>
													<label style="padding-left:40px">
														<input name="Salida" id="Salida" class="ace input-lg ace-checkbox-2 salida_accesov2" type="checkbox" tipoacceso="unico" alt="<?php echo $modulo; ?>" title="<?php echo $ID; ?>" <?php echo $campo_salida; ?> />
														<span class="lbl"><b>SALIDA</b></span>
													<?php else : ?>
														<span style="color: #F10004">BLOQUEADO</span> <?php echo $mensaje_bloqueo; ?>

														<span style="color: #F10004">BLOQUEADO</span> <?php echo $alerta_edad_beneficiario ?>

													<?php endif; ?>
													</label>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<label for="">Permitir Ingreso/Salida a todos:</label>
													<br>
													<label>
														<?php if ($bloqueado <> "S") : ?>

															<?php
															if ($row_log_acceso_ultimo["Entrada"] == "S") :
																$campo_entrada = "disabled";
															else :
																$campo_entrada = "";
															endif;

															if ($row_log_acceso_ultimo["Salida"] == "S" || $total_log == 0) :
																$campo_salida = "disabled";
															else :
																$campo_salida = "";
															endif;

															if ($PermitirMultipleAcceso == 'S' || $PermitirMultipleAcceso == '') {
																$campo_entrada = "";
																$campo_salida = "";
															}
															?>
															<input name="IngresarTodos" id="IngresarTodos" class="ace input-lg ace-checkbox-2 IngresarTodos" type="checkbox" tipoacceso="unico" alt="<?php echo $modulo; ?>" title="<?php echo $ID; ?>" <?php echo $campo_entrada; ?> />
															<span class="lbl"><b>INGRESAR TODOS</b></span>
													</label>
													<label style="padding-left:40px">
														<input name="SalidaTodos" id="SalidaTodos" class="ace input-lg ace-checkbox-2 SalidaTodos" type="checkbox" tipoacceso="unico" alt="<?php echo $modulo; ?>" title="<?php echo $ID; ?>" <?php echo $campo_salida; ?> />
														<span class="lbl"><b>SALIDA TODOS</b></span>
													<?php else : ?>
														<span style="color: #F10004">BLOQUEADO</span> <?php echo $mensaje_bloqueo; ?>

														<span style="color: #F10004">BLOQUEADO</span> <?php echo $alerta_edad_beneficiario ?>

													<?php endif; ?>
													</label>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<div style="overflow:scroll;height:200px;">
														Entradas/Salidas registradas: <?php echo date("Y-m-d"); ?>:
														<?php
														//Consulto el historial de entradas y salidas del dia

														switch (trim($modulo)) {
															case "InvitadoAcceso":
																$condicion_log_acceso = " AND Tipo = 'InvitadoAcceso'";

																break;
															case "Contratista":
																$condicion_log_acceso = " AND Tipo = 'Contratista'";


																break;
															case "SocioInvitado":
																$condicion_log_acceso = " AND Tipo = 'SocioInvitado'";
																break;
															case "Invitado":
																$condicion_log_acceso = " AND (Tipo = 'InvitadoAcceso' or  Tipo = 'SocioInvitado' )";

																break;
															case "Socio":
																$condicion_log_acceso = " AND Tipo = 'Socio'";


																break;
															case "Usuario":
																$condicion_log_acceso = " AND Tipo = 'Usuario'";

																break;
															case "Usuario":
																$condicion_log_acceso = " AND Tipo = 'Usuario'";

																break;
															case "InvitadoEvento":
																$condicion_log_acceso = " AND Tipo = 'InvitadoEvento'";

																break;
														}

														$sql_log_acceso = "SELECT * FROM LogAcceso WHERE IDClub = " . SIMUser::get("club") . " AND IDInvitacion = '" . $ID . "' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '" . date("Y-m-d") . "' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '" . date("Y-m-d") . "')   $condicion_log_acceso Order by IDLogAcceso Desc";

														$result_log_acceso = $dbo->query($sql_log_acceso);
														while ($row_log_acceso = $dbo->fetchArray($result_log_acceso)) :
															if ($row_log_acceso)
																if ($row_log_acceso["Entrada"] == "S") :
																	echo "<br>Entrada: " . substr($row_log_acceso["FechaIngreso"], 11) . " Mecanismo: $row_log_acceso[Mecanismo]";
																elseif ($row_log_acceso["Salida"] == "S") :
																	echo "<br>Salida: " . substr($row_log_acceso["FechaSalida"], 11) . " Mecanismo: $row_log_acceso[Mecanismo]";
																endif;
														endwhile;
														?>
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<!-- PAGE CONTENT ENDS -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php

elseif (!empty(SIMNet::req("qryString"))) :
	if (count($array_proxima_autorizacion) > 0) :
		echo "<span style='color:#063; font-size:16px'; font-weight:bold><br>" . implode("<br>", $array_proxima_autorizacion) . '</span>';
	else : ?>
		<span style='color: #F00; font-size:16px'>
			<?= $mensaje ?>
		</span>
	<?php
	endif;
elseif (empty(SIMNet::req("qryString"))) :
	?>
	<span style='color: #F00; font-size:16px'>
		El campo de busqueda no debe estar vacio.
	</span>
<?php

endif;

include("cmp/footer_grid.php");
?>

<script>
	$('.IngresarTodos').click(function() {
		$('.ingreso_accesov2').each(function() {
			$(this).trigger("click");
		});
	});
	$('.SalidaTodos').click(function() {
		$('.salida_accesov2').each(function() {
			$(this).trigger("click");
		});
	});
</script>