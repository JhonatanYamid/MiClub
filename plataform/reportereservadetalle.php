<?
include("procedures/general.php");
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
											<td>Club</td>
											<td>
												<select name="IDClub" id="IDClubReport" class="form-control">
													<option value="">Todos</option>
													<?php
													if (SIMUser::get("IDPerfil") == 0)
														$condicion_perfil = " IDClub > 0 ";
													else
														$condicion_perfil = " IDClub = '" . SIMUser::get("club") . "'";


													$sql_club_lista = "Select * From Club Where $condicion_club  $condicion_perfil Order By Nombre";
													$qry_club_lista = $dbo->query($sql_club_lista);
													while ($r_club_lista = $dbo->fetchArray($qry_club_lista)) : ?>
														<option value="<?php echo $r_club_lista["IDClub"]; ?>" <?php if ($r_club_lista["IDClub"] == $frm["IDClub"]) echo "selected";  ?>><?php echo $r_club_lista["Nombre"]; ?></option>
													<?php
													endwhile;    ?>
												</select>
											</td>
											<td>Fecha Inicio</td>
											<td>
												<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="fecha inicio" value="">
											</td>
											<td>Fecha Fin</td>
											<td>
												<input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value="">
											</td>
											</td>
											<td>Servicio</td>
											<td>
												<select name="IDServicioMaestro" id="IDServicioMaestro" class="form-control mandatory" title="Servicio">
													<option value=""></option>
												</select>
											</td>
										</tr>
										<tr>
											<td colspan="6" align="center">

												<input type="hidden" name="action" value="search">
												<span class="input-group-btn">

													<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscar">
														<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
														Buscar
													</button>
												</span>
												<span class="input-group-btn">


												</span>

											</td>
										</tr>
									</table>


								</div>




							</form>






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




											<?php
											$IDClub = $_GET["IDClub"];
											$FechaInicio = $_GET["FechaInicio"];
											$FechaFin = $_GET["FechaFin"];
											$IDServicio = (int)$_GET["IDServicioMaestro"];

											if (!empty($IDClub) && !empty($FechaInicio) && !empty($FechaFin)) {

												/*
										$sql_socios="SELECT IDSocio,NumeroDocumento,Accion,Nombre,Apellido FROM Socio WHERE IDClub = '".$IDClub."'";
										$r_socios=$dbo->query($sql_socios);
										while($row_socios=$dbo->fetchArray($r_socios)){
											$array_socios[$row_socios["IDSocio"]]=$row_socios;
										}
										*/

												$sql_elem = "SELECT IDServicioElemento,Nombre FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'";
												$r_elem = $dbo->query($sql_elem);
												while ($row_elem = $dbo->fetchArray($r_elem)) {
													$array_elem[$row_elem["IDServicioElemento"]] = $row_elem;
												}

												$sql_aux = "SELECT IDAuxiliar,Nombre FROM Auxiliar WHERE IDServicio = '" . $IDServicio . "'";
												$r_aux = $dbo->query($sql_aux);
												while ($row_aux = $dbo->fetchArray($r_aux)) {
													$array_aux[$row_aux["IDAuxiliar"]] = $row_aux;
												}

												$sql_elemento = "SELECT IDServicioElemento,Nombre FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'";
												$r_elemento = $dbo->query($sql_elemento);
												while ($row_elemento = $dbo->fetchArray($r_elemento)) {
													$array_elemento[$row_elemento["IDServicioElemento"]] = $row_elemento;
												}
											?>

												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="4"><b>RESUMEN </b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td>
															<?php
															$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio
																						 FROM `ReservaGeneralBck`
																						 WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																						 and IDServicio = '" . $IDServicio . "'
																						 Group By IDServicio
																						 UNION
																						 SELECT count(IDReservaGeneral) as Total, IDServicio
									 																						 FROM `ReservaGeneral`
									 																						 WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
									 																						 and IDServicio = '" . $IDServicio . "'
									 																						 Group By IDServicio
																						  ";

															$r_reserva = $dbo->query($sql_reserva);
															while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
																<table id="simple-table" class="table table-striped table-bordered table-hover">
																	<tr>
																		<td><?php

																			$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																			if (empty($NombreSer)) {
																				$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																				$NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_GET['IDClub'] . "' AND Activo = 'S'");
																			}
																			echo  $NombreSer;
																			?></td>
																		<td>
																			<?php echo $row_reserva["Total"]; ?>

																		</td>
																	</tr>
																</table>
															<?php } ?>
														</td>
													</tr>
													<tr>
														<td><b>Inscritos lista espera</b></td>
														<td>
															<?php
															$sql_lista = "SELECT count(IDListaEspera) as Total, IDServicio
																						 FROM `ListaEspera`
																						 WHERE IDClub = '" . $IDClub . "'  and FechaInicio>='" . $FechaInicio . " 00:00:00' and FechaFin<='" . $FechaFin . " 23:59:59'
																						 and IDServicio = '" . $IDServicio . "'
																						 Group By IDServicio ";



															$r_lista = $dbo->query($sql_lista);
															while ($row_lista = $dbo->fetchArray($r_lista)) {
																echo $row_lista["Total"];
															}

															?>
														</td>
													</tr>
													<tr>
														<td>Total Cancelaciones Reservas</td>
														<td>
															<?php
															$sql_reserva_eliminada = "SELECT count(IDReservaGeneral) as TotalReservasCanceladas, IDServicio
																						 FROM `ReservaGeneralEliminada`
																						 WHERE IDClub = '" . $IDClub . "'  and FechaTrCr>='" . $FechaInicio . " 00:00:00' and FechaTrCr<='" . $FechaFin . " 23:59:59'
																						 and IDServicio = '" . $IDServicio . "'
																						 Group By IDServicio ";



															$r_reserva_eliminada = $dbo->query($sql_reserva_eliminada);
															while ($row_reserva_eliminada = $dbo->fetchArray($r_reserva_eliminada)) {
																echo $row_reserva_eliminada["TotalReservasCanceladas"];
															}

															?>
														</td>
													</tr>
													<td>
														Total Socios
													</td>
													<td>

														<?php
														$sql_socio = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $IDClub . "'";
														$result_socio = $dbo->query($sql_socio);
														$row_socio = $dbo->fetchArray($result_socio);
														$total_socio = $row_socio["TotalSocio"];
														echo number_format($total_socio, 0, ",", ".");
														?>
													</td>
													</tr>

													<tr>
														<td>Socios Activos en app a la fecha</td>
														<td>

															<?php
															$sql_socio_activo = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $IDClub . "' and Token <> ''";
															$result_socio_activo = $dbo->query($sql_socio_activo);
															$row_socio_activo = $dbo->fetchArray($result_socio_activo);
															$total_socio_activo = $row_socio_activo["TotalSocio"];
															echo number_format($total_socio_activo, 0, ",", ".");
															?>

														</td>
													</tr>

													<tr>
														<td>Socios Nuevos en el periodo</td>
														<td>

															<?php
															$sql_socio_activo = "Select count(IDSocio) TotalSocio From Socio Where IDClub = '" . $IDClub . "' and Token <> '' and FechaPrimerIngreso >= '" . $_GET["FechaInicio"] . "' and FechaPrimerIngreso <= '" . $_GET["FechaFin"] . "'";
															$result_socio_activo = $dbo->query($sql_socio_activo);
															$row_socio_activo = $dbo->fetchArray($result_socio_activo);
															$total_socio_nuevo = $row_socio_activo["TotalSocio"];
															echo number_format($total_socio_nuevo, 0, ",", ".");
															?>

														</td>
													</tr>

													<tr>
														<td>Socios activos en el periodo</td>
														<td>

															<?php
															$activos_periodo = $total_socio_activo - $total_socio_nuevo;
															echo number_format($activos_periodo, 0, ",", ".");
															?>

														</td>
													</tr>

													<tr>
														<td>Total Socios Talonera</td>
														<td>

															<?php
															$sql_socio_talonera = "Select count(IDSocio) TotalSocio From SocioTalonera Where IDClub = '" . $IDClub . "' and FechaCompra >= '" . $_GET["FechaInicio"] . "' and FechaCompra <= '" . $_GET["FechaFin"] . "'";

															$result_socio_talonera = $dbo->query($sql_socio_talonera);
															$row_socio_talonera = $dbo->fetchArray($result_socio_talonera);
															$total_socio_talonera = $row_socio_talonera["TotalSocio"];
															echo number_format($total_socio_talonera, 0, ",", ".");
															?>

														</td>
													</tr>
												</table>

												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="4"><b>RESERVAS x DIA DE LA SEMANA</b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td><b>Dia de la semana</b></td>
														<td><b>Total</b></td>
													</tr>
													<?php

													$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio, DAYOFWEEK(Fecha) AS Dia_Semana
																					FROM `ReservaGeneralBck`
																					WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																					and IDServicio = '" . $IDServicio . "'
																					Group By IDServicio,WEEKDAY(Fecha)
																				
																					UNION
																					SELECT count(IDReservaGeneral) as Total, IDServicio, DAYOFWEEK(Fecha) AS Dia_Semana
																												FROM `ReservaGeneral`
																												WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																												and IDServicio = '" . $IDServicio . "'
																												Group By IDServicio,   WEEKDAY(Fecha)
																												ORDER BY Dia_Semana ASC
																					";




													$r_reserva = $dbo->query($sql_reserva);
													while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
														<tr>
															<td><?php

																$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																if (empty($NombreSer)) {
																	$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																	$NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_GET['IDClub'] . "' AND Activo = 'S'");
																}
																echo  $NombreSer;
																?></td>
															<td>

																<?php echo SIMResources::$dias_semana_reporte_x_periodo[$row_reserva["Dia_Semana"]]; ?>

															</td>
															<td>
																<?php echo $row_reserva["Total"]; ?>

															</td>
														</tr>
													<?php } ?>
												</table>

												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="4"><b>RESERVAS x DIA x HORA</b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td><b>Dia</b></td>
														<td><b>Hora</b></td>
														<td><b>Total</b></td>
													</tr>
													<?php
													$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio, Hora, DAYOFWEEK(Fecha) AS Dia_Semana
																					FROM `ReservaGeneralBck`
																					WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																					and IDServicio = '" . $IDServicio . "'
																					Group By IDServicio, Dia_Semana,Hora

																					UNION
																					SELECT count(IDReservaGeneral) as Total, IDServicio, Hora, DAYOFWEEK(Fecha) AS Dia_Semana
																												FROM `ReservaGeneral`
																												WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																												and IDServicio = '" . $IDServicio . "'
																												Group By IDServicio, Dia_Semana,Hora
																												Order by Dia_Semana,Hora
																					";


													/* echo $sql_reserva;
													exit; */

													$r_reserva = $dbo->query($sql_reserva);
													while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
														<tr>
															<td><?php

																$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																if (empty($NombreSer)) {
																	$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																	$NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_GET['IDClub'] . "' AND Activo = 'S'");
																}
																echo  $NombreSer;
																?></td>
															<td>
																<?php echo SIMResources::$dias_semana_reporte_x_periodo[$row_reserva["Dia_Semana"]]; ?>

															</td>
															<td>
																<?php echo $row_reserva["Hora"]; ?>

															</td>
															<td>
																<?php echo $row_reserva["Total"]; ?>

															</td>
														</tr>
													<?php } ?>
												</table>


												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="3"><b>RESERVAS x HORA</b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td><b>Hora</b></td>
														<td><b>Total</b></td>
													</tr>
													<?php
													$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio, Hora
																					FROM `ReservaGeneralBck`
																					WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																					and IDServicio = '" . $IDServicio . "'
																					Group By IDServicio, Hora
																					UNION
																					SELECT count(IDReservaGeneral) as Total, IDServicio, Hora
																												FROM `ReservaGeneral`
																												WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																												and IDServicio = '" . $IDServicio . "'
																												Group By IDServicio, Hora
																												Order by Total DESC
																					";

													$r_reserva = $dbo->query($sql_reserva);
													while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
														<tr>
															<td><?php

																$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																if (empty($NombreSer)) {
																	$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																	$NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_GET['IDClub'] . "' AND Activo = 'S'");
																}
																echo  $NombreSer;
																?></td>
															<td>
																<?php echo $row_reserva["Hora"]; ?>

															</td>
															<td>
																<?php echo $row_reserva["Total"]; ?>

															</td>
														</tr>
													<?php } ?>
												</table>


												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="3"><b>RESERVAS x DIA</b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td><b>Dia</b></td>
														<td><b>Total</b></td>
													</tr>
													<?php
													$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio, Fecha
																					FROM `ReservaGeneralBck`
																					WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																					and IDServicio = '" . $IDServicio . "'
																					Group By Fecha
																					UNION
																					SELECT count(IDReservaGeneral) as Total, IDServicio, Fecha
																												FROM `ReservaGeneral`
																												WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																												and IDServicio = '" . $IDServicio . "'
																												Group By Fecha
																												Order by Fecha ASC";

													$r_reserva = $dbo->query($sql_reserva);
													while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
														<tr>
															<td><?php

																$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																if (empty($NombreSer)) {
																	$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																	$NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_GET['IDClub'] . "' AND Activo = 'S'");
																}
																echo  $NombreSer;
																?></td>
															<td>
																<?php echo $row_reserva["Fecha"]; ?>

															</td>
															<td>
																<?php echo $row_reserva["Total"]; ?>

															</td>
														</tr>
													<?php } ?>
												</table>

												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="3"><b>RESERVAS x ELEMENTO x HORA</b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td><b>Hora</b></td>
														<td><b>Elemento</b></td>
														<td><b>Total</b></td>
													</tr>
													<?php
													$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio, Hora,IDServicioElemento
																					FROM `ReservaGeneralBck`
																					WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																					and IDServicio = '" . $IDServicio . "'
																					Group By IDServicio, Hora
																					UNION
																					SELECT count(IDReservaGeneral) as Total, IDServicio, Hora,IDServicioElemento
																												FROM `ReservaGeneral`
																												WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																												and IDServicio = '" . $IDServicio . "'
																												Group By IDServicio, Hora
																												Order by Total DESC
																					";

													$r_reserva = $dbo->query($sql_reserva);
													while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
														<tr>
															<td><?php

																$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																if (empty($NombreSer)) {
																	$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																	$NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_GET['IDClub'] . "' AND Activo = 'S'");
																}
																echo  $NombreSer;
																?></td>
															<td>
																<?php echo $row_reserva["Hora"]; ?>

															</td>
															<td>
																<?php echo $array_elem[$row_reserva["IDServicioElemento"]]["Nombre"];
																if (empty($array_elem[$row_reserva["IDServicioElemento"]]["Nombre"]))
																	echo $row_reserva["IDServicioElemento"];

																?>
															</td>
															<td>
																<?php echo $row_reserva["Total"]; ?>

															</td>
														</tr>
													<?php } ?>
												</table>



												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="3"><b>RESERVAS x ELEMENTO x DIA</b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td><b>Fecha</b></td>
														<td><b>Elemento</b></td>
														<td><b>Total</b></td>
													</tr>
													<?php
													$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio, IDServicioElemento,Fecha
																					FROM `ReservaGeneralBck`
																					WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																					and IDServicio = '" . $IDServicio . "'
																					Group By Fecha,IDServicioElemento
																					UNION
																					SELECT count(IDReservaGeneral) as Total, IDServicio, IDServicioElemento,Fecha
																												FROM `ReservaGeneral`
																												WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																												and IDServicio = '" . $IDServicio . "'
																												Group By Fecha,IDServicioElemento
																												Order by Fecha ASC
																					";


													$r_reserva = $dbo->query($sql_reserva);
													while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
														<tr>
															<td><?php

																$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																if (empty($NombreSer)) {
																	$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																	$NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_GET['IDClub'] . "' AND Activo = 'S'");
																}
																echo  $NombreSer;
																?>
															</td>
															<td>
																<?php echo $row_reserva["Fecha"]; ?>

															</td>
															<td>
																<?php echo $array_elem[$row_reserva["IDServicioElemento"]]["Nombre"];
																if (empty($array_elem[$row_reserva["IDServicioElemento"]]["Nombre"]))
																	echo $row_reserva["IDServicioElemento"];

																?>
															</td>
															<td>
																<?php echo $row_reserva["Total"]; ?>
															</td>
														</tr>
													<?php } ?>
												</table>


												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="3"><b>RESERVAS x ELEMENTO</b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td><b>Elemento</b></td>
														<td><b>Total</b></td>
													</tr>
													<?php
													$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio, IDServicioElemento
																					FROM `ReservaGeneralBck`
																					WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																					and IDServicio = '" . $IDServicio . "'
																					Group By IDServicioElemento
																					UNION
																					SELECT count(IDReservaGeneral) as Total, IDServicio, IDServicioElemento
																												FROM `ReservaGeneral`
																												WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																												and IDServicio = '" . $IDServicio . "'
																												Group By IDServicioElemento
																												Order by Total DESC
																					";

													$r_reserva = $dbo->query($sql_reserva);
													while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
														<tr>
															<td><?php

																$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																if (empty($NombreSer)) {
																	$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																	$NombreSer = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $IDMaestro . "' AND IDClub = '" . $_GET['IDClub'] . "' AND Activo = 'S'");
																}
																echo  $NombreSer;
																?></td>
															<td>
																<?php echo $array_elem[$row_reserva["IDServicioElemento"]]["Nombre"];
																if (empty($array_elem[$row_reserva["IDServicioElemento"]]["Nombre"]))
																	echo $row_reserva["IDServicioElemento"];

																?>
															</td>
															<td>
																<?php echo $row_reserva["Total"]; ?>
															</td>
														</tr>
													<?php } ?>
												</table>


												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td colspan="3"><b>RESERVAS x Auxiliar</b></td>
													</tr>
													<tr>
														<td><b>Servicio</b></td>
														<td><b>Elemento</b></td>
														<td><b>Total</b></td>
													</tr>
													<?php
													$sql_reserva = "SELECT count(IDReservaGeneral) as Total, IDServicio, IDAuxiliar
																					FROM `ReservaGeneralBck`
																					WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																					and IDServicio = '" . $IDServicio . "' and IDAuxiliar <> ''
																					Group By IDAuxiliar
																					UNION
																					SELECT count(IDReservaGeneral) as Total, IDServicio, IDAuxiliar
																												FROM `ReservaGeneral`
																												WHERE IDClub = '" . $IDClub . "'  and Fecha>='" . $FechaInicio . " 00:00:00' and Fecha<='" . $FechaFin . " 23:59:59'
																												and IDServicio = '" . $IDServicio . "' and IDAuxiliar <> ''
																												Group By IDAuxiliar
																												Order by Total DESC
																					";

													$r_reserva = $dbo->query($sql_reserva);
													while ($row_reserva = $dbo->fetchArray($r_reserva)) { ?>
														<tr>
															<td><?php
																$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																if (empty($NombreSer)) {
																	$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_reserva["IDServicio"] . "'");
																	$NombreSer = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDMaestro . "'");
																}
																echo $NombreSer;
																?></td>
															<td>
																<?php
																$IDAux = str_replace(",", "", $row_reserva["IDAuxiliar"]);
																$NomAux = $array_aux[$IDAux]["Nombre"];
																if (empty($NomAux))
																	echo "Eliminado";
																else {
																	echo $NomAux;
																}
																?>
															</td>
															<td>
																<?php echo $row_reserva["Total"]; ?>
															</td>
														</tr>
													<?php } ?>

												</table>
												<table>
													<tr>
														<td>
															<form name="frmexportapqr" id="frmexportapqr" method="post" enctype="multipart/form-data" action="procedures/excel-reportereservadetalle.php">
																<table>
																	<tr>
																		<div id="prueba1"></div>
																		<!--   <td><input type="text" id="FechaTrEd" name="FechaTrEd" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="FechaTrEd" value="<?php echo date("Y-m-d") ?>"></td>
                                    <td><input type="text" id="FechaTrEd" name="FechaTrEd" placeholder=" Fecha Final" class="col-xs-12 calendar" title="FechaTrEd" value="<?php echo date("Y-m-d") ?>"></td> -->
																		<td>

																			<input type="hidden" name="FechaInicial" id="FechaInicial" value="<?php echo $_GET["FechaInicio"] ?>">
																			<input type="hidden" name="FechaFinal" id="FechaFinal" value="<?php echo $_GET["FechaFin"] ?>">
																			<input type="hidden" name="Servicio" id="Servicio" value="<?php echo $_GET["IDServicioMaestro"] ?>">
																			<input type="hidden" name="IDClub" id="IDClub" value="<?php echo SIMUser::get("club"); ?>">




																			<input class="btn btn-info" type="submit" name="expbnr" id="expbnr" value="Exportar">
																			<!-- <a href="procedures/excel-pqr.php?IDClub=<?php echo SIMUser::get("club"); ?>&IDUsuario=<?php echo SIMUser::get("IDUsuario"); ?>&IDPerfil=<?php echo SIMUser::get("IDPerfil"); ?>"><img src="assets/img/xls.gif" >Exportar</a>-->
																		</td>
																	<tr>
																</table>
															</form>
														</td>
													</tr>
												</table>


											<?php } ?>






										</div><!-- /.span -->
									</div><!-- /.row -->

									<div class="hr hr-18 dotted hr-double"></div>


								</div><!-- /.col -->
							</div><!-- /.row -->







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