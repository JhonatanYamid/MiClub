<?
include("procedures/general.php");
include("procedures/reporteadmin.php");
include("cmp/seo.php");



?>
<link rel="stylesheet" href="assets/css/datepicker.min.css" />
<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
</head>

<body class="no-skin">


	<?
	include("cmp/header.php");
	?>


	<div class="main-container" id="main-container">
		<script type="text/javascript">
			try {
				ace.settings.check('main-container', 'fixed')
			} catch (e) {}
		</script>

		<?
		$menu_club = " class=\"active\" ";
		include("cmp/menu.php");
		?>

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs" id="breadcrumbs">
					<script type="text/javascript">
						try {
							ace.settings.check('breadcrumbs', 'fixed')
						} catch (e) {}
					</script>

					<?php include("cmp/breadcrumb.php"); ?>


				</div>

				<div class="page-content">




					<div class="page-header">
						<?php include("cmp/migapan.php"); ?>
					</div><!-- /.page-header -->

					<div class="row">
						<div class="col-xs-12">
							<!-- PAGE CONTENT BEGINS -->


							<form class="form-horizontal formvalida" id="frmfrmBuscar" name="frmfrmBuscar" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

								<div class="col-xs-12 col-sm-12">



									<table id="simple-table" class="table table-striped table-bordered table-hover">
										<tr>
											<td><?= SIMUtil::get_traduccion('', '', 'Club', LANGSESSION); ?></td>
											<td>
												<select name="IDClub" id="IDClub" class="form-control">
													<option value=""><?= SIMUtil::get_traduccion('', '', 'Todos', LANGSESSION); ?></option>
													<?php
													if (SIMUser::get("IDPerfil") == 0)
														$condicion_perfil = " IDClub > 0 ";
													else
														$condicion_perfil = "and IDClub = '" . SIMUser::get("club") . "'";


													echo $sql_club_lista = "Select * From Club Where $condicion_club  $condicion_perfil Order By Nombre";
													$qry_club_lista = $dbo->query($sql_club_lista);
													while ($r_club_lista = $dbo->fetchArray($qry_club_lista)) : ?>
														<option value="<?php echo $r_club_lista["IDClub"]; ?>" <?php if ($r_club_lista["IDClub"] == $frm["IDClub"]) echo "selected";  ?>><?php echo $r_club_lista["Nombre"]; ?></option>
													<?php
													endwhile;    ?>
												</select>
											</td>
											<td><?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?></td>
											<td>
												<input type="text" id="FechaInicio" name="FechaInicio" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaInicio', LANGSESSION); ?>" value="">
											</td>
											<td><?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?></td>
											<td><input type="text" id="FechaFin" name="FechaFin" placeholder="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" class="col-xs-12 calendar" title="<?= SIMUtil::get_traduccion('', '', 'FechaFin', LANGSESSION); ?>" value=""></td>
										</tr>
										<tr>
											<td colspan="6" align="center">

												<input type="hidden" name="action" value="search">
												<span class="input-group-btn">

													<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
														<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
														<?= SIMUtil::get_traduccion('', '', 'Buscar', LANGSESSION); ?>
													</button>
												</span>
												<span class="input-group-btn">


												</span>

											</td>
										</tr>
									</table>


								</div>




							</form>




							<? foreach ($array_clubes_busqueda as $idclub => $datos_club) {
								$gran_total_reserva = 0;

							?>

								<div class="page-header">
									<h1>
										<img width="150" height="150" alt="150x150" src="<?= CLUB_ROOT . $datos_club[FotoLogoApp] ?>" />
										<?php echo $datos_club["Nombre"]; ?>
										<small>
											<i class="ace-icon fa fa-angle-double-right"></i>
											<?php echo $datos_club["Direccion"]; ?>
										</small>
									</h1>
								</div><!-- /.page-header -->

								<div class="row">
									<div class="col-xs-12">
										<!-- PAGE CONTENT BEGINS -->
										<div class="row">
											<div class="col-xs-12">
												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<thead>
														<tr>
															<th><?= SIMUtil::get_traduccion('', '', 'Resumen', LANGSESSION); ?></th>
															<th><?= SIMUtil::get_traduccion('', '', 'Total', LANGSESSION); ?></th>
														</tr>
													</thead>

													<tbody>
														<tr>
															<td>
																<?= SIMUtil::get_traduccion('', '', 'TotalSocios', LANGSESSION); ?>
															</td>
															<td>

																<?php
																$sql_socio = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $idclub . "'";
																$result_socio = $dbo->query($sql_socio);
																$row_socio = $dbo->fetchArray($result_socio);
																$total_socio = $row_socio["TotalSocio"];
																echo number_format($total_socio, 0, ",", ".");
																?>
															</td>
														</tr>

														<tr>
															<td><?= SIMUtil::get_traduccion('', '', 'SociosActivosenappalafecha', LANGSESSION); ?></td>
															<td>

																<?php
																$sql_socio_activo = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $idclub . "' and Token <> ''";
																$result_socio_activo = $dbo->query($sql_socio_activo);
																$row_socio_activo = $dbo->fetchArray($result_socio_activo);
																$total_socio_activo = $row_socio_activo["TotalSocio"];
																echo number_format($total_socio_activo, 0, ",", ".");
																?>

															</td>
														</tr>

														<tr>
															<td><?= SIMUtil::get_traduccion('', '', 'SociosNuevosenelperiodo', LANGSESSION); ?></td>
															<td>

																<?php
																$sql_socio_activo = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $idclub . "' and Token <> '' and FechaPrimerIngreso >= '" . $_GET["FechaInicio"] . "' and FechaPrimerIngreso <= '" . $_GET["FechaFin"] . "'";
																$result_socio_activo = $dbo->query($sql_socio_activo);
																$row_socio_activo = $dbo->fetchArray($result_socio_activo);
																$total_socio_nuevo = $row_socio_activo["TotalSocio"];
																echo number_format($total_socio_nuevo, 0, ",", ".");
																?>

															</td>
														</tr>

														<tr>
															<td><?= SIMUtil::get_traduccion('', '', 'Sociosactivosenelperiodo', LANGSESSION); ?></td>
															<td>

																<?php
																$activos_periodo = $total_socio_activo - $total_socio_nuevo;
																echo number_format($activos_periodo, 0, ",", ".");
																?>

															</td>
														</tr>

														<tr>
															<td>
																<?= SIMUtil::get_traduccion('', '', 'Noticias', LANGSESSION); ?>
															</td>
															<td>

																<?php
																$sql_noticia = "Select count(IDNoticia) TotalNoticia From Noticia Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_noticia = $dbo->query($sql_noticia);
																$row_noticia = $dbo->fetchArray($result_noticia);
																$total_noticia = $row_noticia["TotalNoticia"];
																echo number_format($total_noticia, 0, ",", ".");
																?>

															</td>
														</tr>

														<tr>
															<td>
																<?= SIMUtil::get_traduccion('', '', 'Eventos', LANGSESSION); ?>
															</td>
															<td>

																<?php
																$sql_evento = "Select count(IDEvento) TotalEvento From Evento Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_evento = $dbo->query($sql_evento);
																$row_evento = $dbo->fetchArray($result_evento);
																$total_evento = $row_evento["TotalEvento"];
																echo number_format($total_evento, 0, ",", ".");
																?>

															</td>
														</tr>

														<tr>
															<td>
																<?= SIMUtil::get_traduccion('', '', 'Galerias', LANGSESSION); ?>
															</td>
															<td>

																<?php
																$sql_galeria = "Select count(IDGaleria) TotalGaleria From Galeria Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_galeria = $dbo->query($sql_galeria);
																$row_galeria = $dbo->fetchArray($result_galeria);
																$total_galeria = $row_galeria["TotalGaleria"];
																echo number_format($total_galeria, 0, ",", ".");
																?>


															</td>
														</tr>
														<tr>
															<td><?= SIMUtil::get_traduccion('', '', 'Invitados', LANGSESSION); ?> V1</td>
															<td><?php
																$sql_invitado = "Select count(IDSocioInvitado) TotalInvitado From SocioInvitado Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_invitado = $dbo->query($sql_invitado);
																$row_invitado = $dbo->fetchArray($result_invitado);
																$total_invitado = $row_invitado["TotalInvitado"];
																echo number_format($total_invitado, 0, ",", ".");
																?></td>
														</tr>

														<tr>
															<td><?= SIMUtil::get_traduccion('', '', 'Invitados', LANGSESSION); ?> V2</td>
															<td><?php
																$sql_invitado = "Select count(IDSocioInvitadoEspecial) TotalInvitado From SocioInvitadoEspecial Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_invitado = $dbo->query($sql_invitado);
																$row_invitado = $dbo->fetchArray($result_invitado);
																$total_invitado = $row_invitado["TotalInvitado"];
																echo number_format($total_invitado, 0, ",", ".");
																?></td>
														</tr>

														<tr>
															<td><?= SIMUtil::get_traduccion('', '', 'AutorizacionContratista', LANGSESSION); ?></td>
															<td><?php
																$sql_invitado = "Select count(IDSocioAutorizacion) TotalInvitado From SocioAutorizacion Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_invitado = $dbo->query($sql_invitado);
																$row_invitado = $dbo->fetchArray($result_invitado);
																$total_invitado = $row_invitado["TotalInvitado"];
																echo number_format($total_invitado, 0, ",", ".");
																?></td>
														</tr>

														<tr>
															<td><?= SIMUtil::get_traduccion('', '', 'TotalSociosquerealizaronreservas', LANGSESSION); ?></td>
															<td><?php
																$sql_socio_reserva = "SELECT count(IDSocio) TotalReservaSocio From ReservaGeneral Where IDClub = '" . $idclub . "' " . " and Fecha >= '" . $_GET["FechaInicio"] . "' and Fecha <= '" . $_GET["FechaFin"] . "' Group by IDSocio";
																$result_invitado = $dbo->query($sql_socio_reserva);
																$actuales = $dbo->rows($result_invitado);

																$sql_socio_reserva = "SELECT count(IDSocio) TotalReservaSocio From ReservaGeneralBck Where IDClub = '" . $idclub . "' " . " and Fecha >= '" . $_GET["FechaInicio"] . "' and Fecha <= '" . $_GET["FechaFin"] . "' Group by IDSocio";
																$result_invitado = $dbo->query($sql_socio_reserva);
																$anteriores = $dbo->rows($result_invitado);

																$TotalSocioReserva = $actuales + $anteriores;

																echo number_format($TotalSocioReserva, 0, ",", ".");
																?></td>
														</tr>


														<tr>
															<td colspan="2">



																<table id="simple-table" class="table table-striped table-bordered table-hover">
																	<thead>
																		<tr>
																			<th><?= SIMUtil::get_traduccion('', '', 'Servicio', LANGSESSION); ?></th>
																			<th><?= SIMUtil::get_traduccion('', '', 'Total', LANGSESSION); ?></th>
																			<th><?= SIMUtil::get_traduccion('', '', 'CapacidadAprox', LANGSESSION); ?>.</th>
																		</tr>
																	</thead>

																	<tbody>
																		<?php





																		//traer servicios maestros activos
																		$sql_reserva_servicio = " SELECT SM.IDServicioMaestro, SM.Nombre Nombre,  count(RG.IDReservaGeneral) Total, S.IDServicio
																	FROM " . $TablaReserva . " RG, Servicio S, ServicioMaestro SM
																	WHERE RG.IDClub = '" . $idclub . "'
																	AND RG.IDServicio = S.IDServicio
																	AND S.IDServicioMaestro = SM.IDServicioMaestro
																	" . $condicion_fecha_reserva . "
																	Group by SM.IDServicioMaestro
																	UNION
																	SELECT SM.IDServicioMaestro, SM.Nombre Nombre,  count(RG.IDReservaGeneral) Total, S.IDServicio
																							FROM ReservaGeneralBck RG, Servicio S, ServicioMaestro SM
																							WHERE RG.IDClub = '" . $idclub . "'
																							AND RG.IDServicio = S.IDServicio
																							AND S.IDServicioMaestro = SM.IDServicioMaestro
																							" . $condicion_fecha_reserva . "
																							Group by SM.IDServicioMaestro
																	";
																		$qry_reserva_servicio = $dbo->query($sql_reserva_servicio);

																		while ($r_reserva_servicio = $dbo->fetchArray($qry_reserva_servicio)) {
																			//Verifico si el servicio esta activo
																			$servicio_activo = $dbo->getFields("ServicioClub", "Activo", "IDServicioMaestro = '" . $r_reserva_servicio["IDServicioMaestro"] . "' and IDClub = '" . $idclub . "'");
																			if ($servicio_activo == "S") {
																		?>
																				<tr>
																					<td>
																						<?php
																						$nombre_servicio_personalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDClub = '" . $idclub . "' and IDServicioMaestro = '" . $r_reserva_servicio["IDServicioMaestro"] . "'");
																						if (empty($nombre_servicio_personalizado))
																							$nombre_servicio_personalizado = $r_reserva_servicio["Nombre"];

																						echo $nombre_servicio_personalizado; ?>
																					</td>
																					<td>
																						<?php
																						$total_reserva = $r_reserva_servicio["Total"];
																						$gran_total_reserva += $total_reserva;
																						echo number_format($total_reserva, 0, ",", ".");
																						?>
																					</td>
																					<td>
																						<?php

																						if (!empty($_GET["FechaInicio"])) :
																							$fecha1 = $_GET["FechaInicio"]; //fecha inicial
																						else :
																							//$fecha1 = "2018-01-01";//fecha inicial
																							$fecha1 = date("Y-m-d"); //fecha inicial
																						endif;

																						if (!empty($_GET["FechaFin"])) :
																							$fecha2 = $_GET["FechaFin"]; //fecha de cierre
																						else :
																							$fecha2 = date("Y-m-d"); //fecha de cierre
																						endif;

																						$total_dia_real = 0;
																						//Verifico cuantos cupos por cada dia estan disponibles
																						$fechaInicio = strtotime($fecha1);
																						$fechaFin = strtotime($fecha2);
																						for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400) {
																							$dia_semana = date("w", $i);
																							//Verifico si este dia tiene disponibilidad para contarlo como disponible
																							$dias_real_servicio = "Select * From ServicioDisponibilidad Where IDServicio = '" . $r_reserva_servicio["IDServicio"] . "' and  IDDia like '%" . $dia_semana . "|%' limit 1";
																							$result_real_servicio = $dbo->query($dias_real_servicio);
																							if ($dbo->rows($result_real_servicio) > 0) :
																								$total_dia_real += 1;
																							endif;
																						}

																						//Consulto Elementos
																						$sql_elemento_servicio = "Select * From ServicioElemento Where IDServicio = '" . $r_reserva_servicio["IDServicio"] . "'";
																						$result_elemento_servicio = $dbo->query($sql_elemento_servicio);
																						$total_elemento_servicio = $dbo->rows($result_elemento_servicio);
																						//Consulto cupos aprox en el dia
																						//$sql_disponibilidad = "Select * From ServicioDisponibilidad Where IDServicio = '". $r_reserva_servicio["IDServicio"]."' Order by IDServicioDisponibilidad desc Limit 1 ";
																						$sql_disponibilidad = "Select * From ServicioDisponibilidad Where IDServicio = '" . $r_reserva_servicio["IDServicio"] . "' Order by IDServicioDisponibilidad desc Limit 1 ";
																						$result_disponibilidad = $dbo->query($sql_disponibilidad);
																						$row_disponibilidad = $dbo->fetchArray($result_disponibilidad);
																						$intervalo_turno = (int)$dbo->getFields("Disponibilidad", "Intervalo", "IDDisponibilidad = '" . $row_disponibilidad["IDDisponibilidad"] . "'");
																						$cupos_turno = (int)$dbo->getFields("Disponibilidad", "Cupos", "IDDisponibilidad = '" . $row_disponibilidad["IDDisponibilidad"] . "'");

																						//Desde
																						$sql_disponibilidad = "Select HoraDesde From ServicioDisponibilidad Where IDServicio = '" . $r_reserva_servicio["IDServicio"] . "' Order by HoraDesde ASC Limit 1 ";
																						$result_disponibilidad = $dbo->query($sql_disponibilidad);
																						$row_disponibilidad = $dbo->fetchArray($result_disponibilidad);
																						$HoraDesde = $row_disponibilidad["HoraDesde"];

																						//Hasta
																						$sql_disponibilidad = "Select HoraHasta From ServicioDisponibilidad Where IDServicio = '" . $r_reserva_servicio["IDServicio"] . "' Order by HoraHasta DESC Limit 1 ";
																						$result_disponibilidad = $dbo->query($sql_disponibilidad);
																						$row_disponibilidad = $dbo->fetchArray($result_disponibilidad);
																						$HoraHasta = $row_disponibilidad["HoraHasta"];
																						// Le sumo el utimo turno si por ejemplo es 7:00pm y el intervalo es 30 presta servicio hasta las 7:30
																						$HoraHasta = strtotime('+' . $intervalo_turno . ' minutes', strtotime(date("Y-m-d " . $HoraHasta)));
																						$HoraHasta = date("H:i:s", $HoraHasta);



																						$fecha1 = new DateTime(date("Y-m-d") . $HoraDesde); //fecha inicial
																						$fecha2 = new DateTime(date("Y-m-d") . $HoraHasta); //fecha de cierre


																						$intervalo = $fecha1->diff($fecha2);
																						$horas_dia = (int)$intervalo->format('%H');
																						$cupos_dia = ($horas_dia * 60) / $intervalo_turno;

																						//Si la disponibilidad permite mas de 1 persona por hora p.j las claseslo multiplico
																						if ($cupos_turno > 1)
																							$cupos_dia *= $cupos_turno;


																						/*
														if(!empty($_GET["FechaInicio"])):
															$fecha1 = new DateTime($_GET["FechaInicio"]);//fecha inicial
														else:
															$fecha1 = new DateTime("2018-01-01");//fecha inicial
														endif;

														if(!empty($_GET["FechaFin"])):
															$fecha2 = new DateTime($_GET["FechaFin"]);//fecha de cierre
														else:
															$fecha2 = new DateTime(date("Y-m-d"));//fecha de cierre
														endif;
														*/

																						//echo "Ini " .$fecha1->format("Y-m-d")	 . " Fin " . $fecha2->format("Y-m-d");

																						//$intervalo = $fecha1->diff($fecha2);
																						//$dias_consulta = (int)$intervalo->format('%R%a');


																						$dias_consulta = $total_dia_real;
																						//echo $cupos_periodo = (int)($cupos_dia * $total_elemento_servicio) * ($dias_consulta+1);
																						echo $cupos_periodo = (int)($cupos_dia * $total_elemento_servicio) * $dias_consulta;
																						$total_cupos_aprox += $cupos_periodo;
																						?>
																						Turnos
																					</td>
																				</tr>
																		<?php
																			}
																		}


																		?>
																		<tr>
																			<td>
																				<b><?= SIMUtil::get_traduccion('', '', 'TOTALRESERVAS', LANGSESSION); ?></b>
																			</td>
																			<td><b>
																					<?php
																					echo number_format($gran_total_reserva, 0, ",", ".");
																					?>
																				</b>
																			</td>
																			<td><b>
																					<?php
																					echo number_format($total_cupos_aprox, 0, ",", ".");
																					?>
																				</b>
																			</td>
																		</tr>
																	</tbody>
																</table>




															</td>
														</tr>
													</tbody>
												</table>
											</div><!-- /.span -->
										</div><!-- /.row -->

										<div class="hr hr-18 dotted hr-double"></div>


									</div><!-- /.col -->
								</div><!-- /.row -->


							<?php  } ?>




						</div><!-- PAGE CONTENT ENDS -->

					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.page-content -->
		</div>
	</div><!-- /.main-content -->

	<?
	include("cmp/footer.php");
	?>
	</div><!-- /.main-container -->

	<?
	include("cmp/footer_scripts.php");
	?>
</body>

</html>