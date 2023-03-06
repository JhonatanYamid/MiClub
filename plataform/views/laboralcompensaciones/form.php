<?php
$estadosLaboral =  SIMResources::$estado_laboral;

if (!empty($frm["IDUsuario"])) {
	$frm["TipoSolicitante"] = "Usuario";
} else if (!empty($frm["IDSocio"])) {
	$frm["TipoSolicitante"] = "Socio";
}
if (!empty($frm["FechaInicio"])) {
	$frm["FechaInicio"] = date("Y-m-d", strtotime($frm["FechaInicio"]));
}
if (!empty($frm["FechaFin"])) {
	$frm["FechaFin"] = date("Y-m-d", strtotime($frm["FechaFin"]));
}
if (empty($frm["IDEstado"])) {
	$frm["IDEstado"] = 1;
}
if ($frm["TipoSolicitante"] == "Usuario") {
	$query = "SELECT Nombre FROM Usuario WHERE IDUsuario={$frm["IDUsuario"]}";
	$consulta = $dbo->query($query);
	$consulta = $dbo->fetch($consulta);
	$frm["SolicitanteUsuario"] = $consulta["Nombre"];
	$inputShow = "Usuario";
} else if ($frm["TipoSolicitante"] == "Socio") {
	$query = "SELECT Nombre FROM Socio WHERE IDSocio={$frm["IDSocio"]}";
	$consulta = $dbo->query($query);
	$consulta = $dbo->fetch($consulta);
	$frm["SolicitanteSocio"] = $consulta["Nombre"];
	$inputShow = "Socio";
}
?>

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

						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-info green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Informaciónporpartedelsolicitante', LANGSESSION); ?>
							</h3>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'TipoSolicitante', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<label for="Usuario"><?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?></label>
									<input <?php if ($frm["TipoSolicitante"] == "Usuario") {
												echo "checked";
											} ?> type="radio" id="Usuario" name="TipoSolicitante" value="Usuario" title="Tipo Solicitante" <?php if ($_GET["action"] == "edit") {
																																				echo " disabled";
																																			} ?>>
									<label for="Socio"><?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?></label>
									<input <?php if ($frm["TipoSolicitante"] == "Socio") {
												echo "checked";
											} ?> type="radio" id="Socio" name="TipoSolicitante" value="Socio" title="Tipo Solicitante" <?php if ($_GET["action"] == "edit") {
																																			echo " disabled";
																																		} ?>>

									<!--? echo SIMHTML::formradiogroup( array_flip( ["Socio", "Usuario"] ) , $tipoSolicitante , 'TipoSolicitante' , "class='input mandatory'" ) ?-->

								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Solicitante </label>
								<div class="col-sm-8">
									<input type="text" id="solicitanteUsuario" name="solicitante" placeholder="<?= SIMUtil::get_traduccion('', '', 'Usuario', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax-funcionario-laboralUsuario <?php if ($inputShow != "Usuario") {
																																																													echo "hidden";
																																																												} ?>" title="Solicitante" value="<?php echo $frm["SolicitanteUsuario"] ?>" <?php if ($_GET["action"] == "edit") {
																																																																																echo " disabled";
																																																																															} ?>>
									<input type="text" id="solicitanteSocio" name="solicitante" placeholder="<?= SIMUtil::get_traduccion('', '', 'Socio', LANGSESSION); ?>" class="col-xs-12 autocomplete-ajax  <?php if ($inputShow != "Socio") {
																																																					echo "hidden";
																																																				} ?>" title="Solicitante" value="<?php echo $frm["SolicitanteSocio"] ?>" <?php if ($_GET["action"] == "edit") {
																																																																								echo " disabled";
																																																																							} ?>>
									<input type="hidden" name="IDUsuario" value="<?php echo $frm["IDUsuario"]; ?>" id="IDUsuario" title="Usuario">
									<input type="hidden" name="IDSocio" value="<?php echo $frm["IDSocio"]; ?>" id="IDSocio" title="Socio">
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Días', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<input type="text" id="DiasTomar" name="DiasTomar" placeholder="<?= SIMUtil::get_traduccion('', '', 'DiasATomar', LANGSESSION); ?>" class="col-xs-12 mandatory" title="DiasTomar" value="<?php echo $frm["DiasTomar"]; ?>" <?php if ($_GET["action"] == "edit") {
																																																																	echo " disabled";
																																																																} ?>>
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar mandatory" title="Fecha Inicio" value="<?php echo $frm["FechaInicio"]; ?>" <?php if ($_GET["action"] == "edit") {																																																											} ?>>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<input type="text" id="FechaFinMostrar" name="FechaFinMostrar" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="col-xs-12 mandatory calendar" title="Fecha Fin" value="<?php echo $frm["FechaFin"]; ?>" disabled>
								</div>
							</div>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Comentariosolicitante', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<textarea id="Comentario" name="Comentario" cols="10" rows="5" class="col-xs-12 mandatory" title="Comentario" <?php if ($_GET["action"] == "edit") {
																																						echo " disabled";
																																					} ?>><?php echo $frm["Comentario"]; ?></textarea>
								</div>
							</div>
					
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Archivo', LANGSESSION); ?>:</label>
								<input name="Archivo" id="Archivo" class="" title="Archivo" type="file" size="25" style="font-size: 10px">
								<div class="col-sm-8">
									<? if (!empty($frm["Archivo"])) {
										echo '<a class="blue ace-icon fa fa-file bigger-130" title="Ver archivo" href="'.LABORAL_ROOT.$frm["Archivo"].'" download="'.$row['Archivo'].'">&nbsp</a>';
									?>
										<a href="<? echo $script . ".php?action=delfoto&foto=$frm[Archivo]&campo=Archivo&id=" . $frm[$key]; ?>" class="ace-icon glyphicon glyphicon-trash">&nbsp;</a>
									<?
									} // END if
									?>
								</div>
							</div>
						</div>
						<div class="widget-header widget-header-large">
							<h3 class="widget-title grey lighter">
								<i class="ace-icon fa fa-exclamation-circle green"></i>
								<?= SIMUtil::get_traduccion('', '', 'Informaciónporpartedelclub', LANGSESSION); ?>
							</h3>
						</div>
						<div class="form-group first ">
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Estado', LANGSESSION); ?> </label>
								<div class="col-sm-8">
									<select name="IDEstado" id="IDEstado" class="form-control" <?php if ($_GET["action"] != "edit") {
																									echo " disabled";
																								} ?>>
										<?php foreach ($estadosLaboral as $key => $value) { ?>
											<option <?php if ($frm['IDEstado'] == $key) {
														echo " selected ";
													} ?>value="<?php echo $key ?>"><?php echo $value ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> <?= SIMUtil::get_traduccion('', '', 'Comentarioaprobador', LANGSESSION); ?>: </label>
								<div class="col-sm-8">
									<textarea id="ComentarioAprobacion" name="ComentarioAprobacion" cols="10" rows="5" class="col-xs-12 <?php if ($_GET["action"] == "edit") {
																																			echo 'mandatory';
																																		} ?>" title="ComentarioAprobacion" <?php if ($_GET["action"] != "edit") {
																																												echo " disabled";
																																											} ?>><?php echo $frm["ComentarioAprobacion"]; ?></textarea>
								</div>
							</div>
						</div>
						<div class="clearfix form-actions">
							<div class="col-xs-12 text-center">
								<input type="hidden" name="ID" id="ID" value="<?php echo $frm[$key] ?>" />
								<input type="hidden" name="FechaFin" id="FechaFin" value="<?php echo $frm["FechaFin"] ?>" />
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
		</div>
	</div><!-- /.widget-main -->
</div><!-- /.widget-body -->
</div><!-- /.widget-box -->

<?
include("cmp/footer_scripts.php");
?>

<script src="assets/js/grid.locale-en.js"></script>
<script type="text/javascript">

	cambiarFecha();

	$("#Usuario").click(function() {
		$("#solicitanteUsuario").removeClass("hidden");
		$("#solicitanteSocio").addClass("hidden");

	});

	$("#Socio").click(function() {
		$("#solicitanteSocio").removeClass("hidden");
		$("#solicitanteUsuario").addClass("hidden");
	});

	function cambiarFecha(){
		
		let fechaInicio = $("#FechaInicio").val();
		let dias = Number($("#DiasTomar").val());
		
		if (fechaInicio != undefined && dias != undefined) {

			$.ajax({
				url: "includes/async/<?php echo $script; ?>.async.php?oper=caculafechafin&fechainicio=" + fechaInicio + "&dias=" + dias,
				success: function(result) {
					console.log(result);
					$("#FechaFin, #FechaFinMostrar").val(result.response.Fecha);
					$("#Comentario").text(result.response.Mensaje);
				},
				error: function(error) {
					console.log(error);
				}
			});
		}
	}

	$("#FechaInicio, #DiasTomar").change(function() {
		cambiarFecha();
	});

	function cambiarFecha(){
		
		let fechaInicio = $("#FechaInicio").val();
		let dias = Number($("#DiasTomar").val());
		
		if (fechaInicio != undefined && dias != undefined) {

			$.ajax({
				url: "includes/async/<?php echo $script; ?>.async.php?oper=caculafechafin&fechainicio=" + fechaInicio + "&dias=" + dias,
				success: function(result) {
					console.log(result);
					$("#FechaFin, #FechaFinMostrar").val(result.response.Fecha);
					$("#Comentario").text(result.response.Mensaje);
				},
				error: function(error) {
					console.log(error);
				}
			});
		}
	}

</script>