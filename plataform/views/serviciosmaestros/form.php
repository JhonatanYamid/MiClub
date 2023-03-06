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
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input type="text" id="Nombre" name="Nombre" placeholder="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'nombre', LANGSESSION); ?>" value="<?php echo $frm["Nombre"]; ?>">
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? if (!empty($frm[Icono])) {
										echo "<img src='" . SERVICIO_ROOT . "$frm[Icono]' width=55 >";
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Icono]&campo=Icono&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
									<input name="Icono" id=file class="" title="<?= SIMUtil::get_traduccion('', '', 'Icono', LANGSESSION); ?>" type="file" size="25" style="font-size: 10px">


								</div>
							</div>

						</div>




						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'General', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["General"], 'General', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'ElementoInicial', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<?php echo SIMHTML::formPopUp("ServicioInicial", "Nombre", "Nombre", "IDServicioInicial", $frm["IDServicioInicial"], "[Seleccione el Servicio Inicial]", "popup form-control", "title = \"Servicio Inicial\"") ?>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoElemento', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input id=LabelElemento type=text size=25 name=LabelElemento class="input" title="<?= SIMUtil::get_traduccion('', '', 'TextoElemento', LANGSESSION); ?>" value="<?= $frm[LabelElemento] ?>">
									<br><?= SIMUtil::get_traduccion('', '', '(Textoutilizadoenelbotonapp)', LANGSESSION); ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'PemiteAuxiliares(Ej:Boleadores)', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteAuxiliar"], 'PermiteAuxiliar', "class='input mandatory'") ?>
								</div>
							</div>

						</div>



						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'ReservarCanchaautomaticamente(soloparaclases)', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<select name="IDServicioMaestroReservar" id="IDServicioMaestroReservar" class="form-control" title="<?= SIMUtil::get_traduccion('', '', 'ReservarCanchaautomaticamente(soloparaclases)', LANGSESSION); ?>">
										<option value=""></option>
										<?php $sql_maestros = $dbo->query("Select * From ServicioMaestro Where Publicar = 'S'");
										while ($row_serviciomaestro = $dbo->fetchArray($sql_maestros)) : ?>
											<option value="<?php echo $row_serviciomaestro["IDServicioMaestro"]; ?>" <?php if ($row_serviciomaestro["IDServicioMaestro"] == $frm["IDServicioMaestroReservar"]) echo "selected"; ?>><?php echo $row_serviciomaestro["Nombre"]; ?></option>
										<?php endwhile; ?>
									</select>
									<?php //echo SIMHTML::formPopUp( "ServicioMaestro" , "Nombre" , "Nombre" , "IDServicioMaestroReservar" , $frm["IDServicioMaestroReservar"] , "[Seleccione Servicio]" , "popup form-control" , "title = \"Servicio\"" )
									?>
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Reservas2turnoscuandoelnumerodeinvitadossea', LANGSESSION); ?>: </label>

								<div class="col-sm-8">
									<input id="InvitadoTurnos" type="number" size=25 name="InvitadoTurnos" class="col-xs-12" title="<?= SIMUtil::get_traduccion('', '', 'Reservas2turnoscuandoelnumerodeinvitadossea', LANGSESSION); ?>" value="<?= $frm[InvitadoTurnos] ?>">
								</div>
							</div>

						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoAuxiliar(Boleador)', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input id=LabelAuxiliar type=text size=25 name=LabelAuxiliar class="input" title="<?= SIMUtil::get_traduccion('', '', 'TextoAuxiliar(Boleador)', LANGSESSION); ?>" value="<?= $frm[LabelAuxiliar] ?>">
									<br><?= SIMUtil::get_traduccion('', '', '(Textoutilizadoenelbotonapp)', LANGSESSION); ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TextoTipoTurno(Sencillos,dobles,etc)', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input id=LabelTipoReserva type=text size=25 name=LabelTipoReserva class="input" title="<?= SIMUtil::get_traduccion('', '', 'TextoTipoTurno(Sencillos,dobles,etc)', LANGSESSION); ?>" value="<?= $frm[LabelTipoReserva] ?>">
									<br><?= SIMUtil::get_traduccion('', '', '(Textoutilizadoenelbotonapp)', LANGSESSION); ?>
								</div>
							</div>



						</div>


						<div class="form-group first ">


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteTipoTurnos(Ej:Sencillos,dobles,etc)', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteTipoReserva"], 'PermiteTipoReserva', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'VerHorariosenacordeon', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["HorarioAcordeon"], 'HorarioAcordeon', "class='input mandatory'") ?>
								</div>
							</div>

						</div>

						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteAsignarreservaaotroBeneficiario', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["PermiteBeneficiario"], 'PermiteBeneficiario', "class='input '") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textoagregarbeneficiario', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input id=LabelBeneficiario type=text size=25 name=LabelBeneficiario class="input" title="<?= SIMUtil::get_traduccion('', '', 'Textoagregarbeneficiario', LANGSESSION); ?>" value="<?= $frm[LabelBeneficiario] ?>">
								</div>
							</div>


						</div>



						<div class="form-group first ">


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Siesunareservamultiplesolomostrarfechaenlaqueempiezareserva', LANGSESSION); ?>? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["SoloFechaSeleccionada"], 'SoloFechaSeleccionada', "class='input mandatory'") ?>
								</div>
							</div>

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Textoreservamultiple', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<input id=LabelReservaMultiple type=text size=25 name=LabelReservaMultiple class="input" title="<?= SIMUtil::get_traduccion('', '', 'Textoreservamultiple', LANGSESSION); ?>" value="<?= $frm["LabelReservaMultiple"] ?>">
								</div>
							</div>





						</div>


						<div class="form-group first ">

							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'PermiteReservadeGrupos', LANGSESSION); ?> ? </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["ReservaGrupos"], 'ReservaGrupos', "class='input '") ?>
								</div>
							</div>


							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'publicar', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<? echo SIMHTML::formradiogroup(array_flip(SIMResources::$sino), $frm["Publicar"], 'Publicar', "class='input mandatory'") ?>
								</div>
							</div>


						</div>

						<div class="form-group first ">




							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?> </label>

								<div class="col-sm-8">
									<textarea id="Descripcion" name="Descripcion" cols="10" rows="5" class="col-xs-12 mandatory" title="<?= SIMUtil::get_traduccion('', '', 'Descripcion', LANGSESSION); ?>"><?php echo $frm["Descripcion"]; ?></textarea>
								</div>
							</div>



						</div>





						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="action" id="action" value="<?php echo $newmode ?>" />
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