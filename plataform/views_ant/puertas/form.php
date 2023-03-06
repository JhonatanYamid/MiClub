<?php include_once("js/fckeditor/fckeditor.php"); // FCKEditor 
?>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-header">
		<h4 class="widget-title lighter smaller">
			<i class="ace-icon fa fa-users orange"></i><?= SIMUtil::get_traduccion('', '', 'CREARNUEVA', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?>
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
								</div>
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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CodigoDispositivo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="CodigoDispositivo" name="CodigoDispositivo" placeholder="<?= SIMUtil::get_traduccion('', '', 'CodigoDispositivo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'CodigoDispositivo', LANGSESSION); ?>" value="<?php echo $frm["CodigoDispositivo"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'CodigoPuerta', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="CodigoPuerta" name="CodigoPuerta" placeholder="<?= SIMUtil::get_traduccion('', '', 'CodigoPuerta', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'CodigoPuerta', LANGSESSION); ?>" value="<?php echo $frm["CodigoPuerta"]; ?>">
								</div>
							</div>



						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TiempoApertura', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="TiempoApertura" name="TiempoApertura" placeholder="<?= SIMUtil::get_traduccion('', '', 'TiempoApertura', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TiempoApertura', LANGSESSION); ?>" value="<?php echo $frm["TiempoApertura"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TiempoEspera', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="TiempoEspera" name="TiempoEspera" placeholder="<?= SIMUtil::get_traduccion('', '', 'TiempoEspera', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'TiempoEspera', LANGSESSION); ?>" value="<?php echo $frm["TiempoEspera"]; ?>">
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PinEquipo', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="PinEquipo" name="PinEquipo" placeholder="<?= SIMUtil::get_traduccion('', '', 'PinEquipo', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'PinEquipo', LANGSESSION); ?>" value="<?php echo $frm["PinEquipo"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'IdentificadorCliente', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="IdentificadorCliente" name="IdentificadorCliente" placeholder="<?= SIMUtil::get_traduccion('', '', 'IdentificadorCliente', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'IdentificadorCliente', LANGSESSION); ?>" value="<?php echo $frm["IdentificadorCliente"]; ?>">
								</div>
							</div>


						</div>

						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Ubicacion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("PuertaUbicacion", "Nombre", "Nombre", "IDPuertaUbicacion", $frm["IDPuertaUbicacion"], "[Seleccione]", "form-control", "title = \"Ubicacion\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
								</div>
							</div>

							<div class="form-group first ">

								<div class="col-xs-12 col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'ServicioAsociado(reservas)', LANGSESSION); ?> </label>

									<div class="col-sm-8">

										<select name="IDServicio" id="IDServicio">
											<?php
											if (count($datos_servicio) > 0) {
												foreach ($datos_servicio as $idservicio => $servicio) {

													$id_servicio_mestro_menu = $servicio["IDServicioMaestro"];
													$servicio["Nombre"] =  $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");
													$servicio["NombrePersonalizado"] =  $dbo->getFields("ServicioClub", "TituLoServicio", "IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' and IDServicioMaestro = '" . $id_servicio_mestro_menu . "'");

													if (!empty($servicio["NombrePersonalizado"]))
														$NombreServicio = $servicio["NombrePersonalizado"];
													else
														$NombreServicio = $servicio["Nombre"];


													if ($servicio["IDServicio"] == $frm["IDServicio"]) :
														$seleccionar = "selected";
													else :
														$seleccionar = "";
													endif;
											?>
													<option value="<?php echo $servicio["IDServicio"] ?>" <?php echo $seleccionar; ?>>
														<?php echo $NombreServicio;  ?>
													</option>
											<?php }
											}
											?>
										</select>

									</div>
								</div>

							</div>
						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Validarquetengareservaenelservicioasociadoparapoderabrirlapuerta', LANGSESSION); ?>? </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ValidarReserva"], 'ValidarReserva', "class='input mandatory'") ?></div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Tiempoanterioridadparapoderabrirlapuertaconreserva(ej10minsilareservaesalas3permitiraabrirdesdelas2:50pm)', LANGSESSION); ?> </label>

								<div class="col-sm-8"><input type="number" id="MinutoAnterioridad" name="MinutoAnterioridad" placeholder="<?= SIMUtil::get_traduccion('', '', 'MinutoAnterioridad', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'MinutoAnterioridad', LANGSESSION); ?>" value="<?php echo $frm["MinutoAnterioridad"]; ?>"></div>
							</div>


						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8"><? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?></div>
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