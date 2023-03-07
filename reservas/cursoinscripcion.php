<?php

include("procedures/general.php");
include("procedures/login.php");
if (SIMUser::get("club") && SIMUser::get("club") == 249) {
	include("cmp/seocotopaxi.php");
} else {
	include("cmp/seoliga.php");
}
// include("cmp/seoliga.php");

$frm = SIMUtil::varsLOG($_POST);
if ($frm["action"] == "BuscarCurso") {
	$resultado = SIMWebServiceApp::curso_buscar($frm["IDClub"], $frm["IDSocio"], $frm["IDCursoSede"], $frm["IDCursoTipo"], $frm["IDCursoEntrenador"]);
}



?>



<script type="text/javascript">
	$(document).ready(function() {

		$(".btnInscribirCurso").click(function() {
			var IDCursoHorario = $(this).attr("rel");
			var IDCursoCalendario = $(this).attr("calendario");
			var HoraDesde = $(this).attr("horadesde");
			var Consecutivo = $(this).attr("consecutivo");
			var Cupos = $(this).attr("cupos");
			var Valor = $(this).attr("valor");
			var detalle;
			$("#IDCursoHorario").val(IDCursoHorario);
			$("#IDCursoCalendario").val(IDCursoCalendario);
			$("#HoraDesde").val(HoraDesde);
			$("#Cupos").val(Cupos);
			$("#Valor").val(Valor);
			$("#txtmsjreserva" + Consecutivo).html("Procesando, por favor espere...");
			$("#frmInscribirCurso").submit();
			return false;
		});

	});
</script>


</head>

<body>

	<div id="cont_general">
		<?php
		if (SIMUser::get("club") && SIMUser::get("club") == 249) {
			include("cmp/menucotopaxi.php");
		} else {
			include("cmp/menuliga.php");
		}

		?>


		<div id="cuerpo">
			<div class="cont_central">

				<div id="titulos_internas">INSCRIPCION CURSOS</div>


				<div id="txt_internas">

					<form name="frmGeneral" id="frmGeneral" method="post" action="cursoinscripcion.php" class="formvalida">

						<input type="hidden" name="form" value="formContacto">
						<input type="text" style="display:none;" name="xvar">
						<div class="cont_1_form_pie">
							<label class="etiqueta_form_vive_interna">Sede</label>
							<?php echo SIMHTML::formPopUp("CursoSede", "Nombre", "Nombre", "IDCursoSede", $frm["IDCursoSede"], "[Seleccione]", "campo_form_pie", "title = \"Sede\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
						</div>
						<div class="cont_1_form_pie">
							<label class="etiqueta_form_vive_interna">Tipo</label>
							<?php echo SIMHTML::formPopUp("CursoTipo", "Nombre", "Nombre", "IDCursoTipo", $frm["IDCursoTipo"], "[Seleccione]", "campo_form_pie", "title = \"Horario\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
						</div>

						<!--
											<div class="cont_1_form_pie">
													<label class="etiqueta_form_vive_interna">Entrenador</label>
													<?php echo SIMHTML::formPopUp("CursoEntrenador", "Nombre", "Nombre", "IDCursoEntrenador", $frm["IDCursoEntrenador"], "[Seleccione]", "campo_form_pie", "title = \"Entrenador\"", " and IDClub = '" . SIMUser::get("club") . "'"); ?>
											</div>
										-->


						<input type="hidden" id="IDSocio" name="IDSocio" value="<?php echo $datos->IDSocio;  ?>" />
						<input type="submit" class="enviar_contacto" id="enviar_contacto" />
						<input type="hidden" name="action" value="BuscarCurso">
						<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">
					</form>
				</div>


				<div id="txt_internas">

					<?php if ($resultado["success"]) { ?>
						<form class="" role="form" method="post" id="frmInscribirCurso" name="frmInscribirCurso" action="<?php echo SIMUtil::lastURI() ?>" enctype="multipart/form-data">


							<table class="blueTable">
								<thead>
									<tr>
										<th>Curso</th>
										<th>Nivel</th>
										<th>Edad</th>
										<th>Sede</th>
										<th>Dia</th>
										<th>Fecha Inicio - Fin</th>
										<th>Hora</th>
										<th>Entrenador</th>
										<th>Valor Mes</th>
										<!--<th>Valor Trimestre</th>-->
										<th>Cupos</th>
										<th>Inscribir</th>
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
											<!--
												<td>
													<?php echo "$" . number_format($datos["ValorTrimestre"], 0, '', '.');  ?>
												</td>
											-->
											<td align="center">
												<?php
												$inscritos = SIMWebServiceApp::get_curso_inscritos($frm["IDClub"], $datos["IDCursoHorario"], $datos["IDCursoCalendario"], $datos["HoraDesde"]);
												$total_cupos = (int)$datos["Cupo"] - (int)$inscritos;
												echo $total_cupos;
												?>
											</td>
											<td align="center">
												<?php if ($total_cupos > 0) {
													$datos_encode = json_encode($datos);
													$datosurl = base64_encode($datos_encode);
												?>
													<a class="boton_personalizado" href="detalle_curso_inscripcion.php?IDSocio=<?php echo $_POST["IDSocio"] ?>&IDClub=<?php echo SIMUser::get("club"); ?>&vm=<?php echo base64_encode($datos["ValorMes"]) ?>&vt=<?php echo base64_encode($datos["ValorTrimestre"]); ?>&calendario=<?php echo $datos["IDCursoCalendario"] ?>&horadesde=<?php echo $datos["HoraDesde"] ?>&cupos=<?php echo $datos["Cupo"]; ?>&IDCursoHorario=<?php echo $datos["IDCursoHorario"] ?>&datosseleccion=<?php echo $datosurl; ?>">Inscribir</a>
													<!--<a href="#inscribircurso" class="btnInscribirCurso boton_personalizado" valor="<?php echo $datos["ValorMes"] ?>" calendario="<?php echo $datos["IDCursoCalendario"] ?>" horadesde="<?php echo $datos["HoraDesde"] ?>" cupos="<?php echo $datos["Cupo"]; ?>" rel="<?php echo $datos["IDCursoHorario"] ?>" consecutivo="<?php echo $contador; ?>" ><span id="txtmsjreserva<?php echo $contador ?>">Inscribir</span></a>-->
												<?php } else { ?>
													<a href="#inscribircurso" class="boton_personalizado_rojo">AGOTADO</a>
												<?php } ?>
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
					<?php } else { ?>
						<p style="text-align: justify;">
							<?php echo $resultado["message"]; ?>
						</p>
					<?php	} ?>



				</div>


			</div>




			<?php

			if (SIMUser::get("club") && SIMUser::get("club") == 249) {
				include("cmp/footercotopaxi.php");
			} else {
				include("cmp/footerliga.php");
			}


			?>
		</div>