<?
include("procedures/general.php");
//include("procedures/reporteestadisticas.php");
include("cmp/seo.php");
if (!empty($_GET["IDClub"])) :
	$condicion_club_busqueda .= " and IDClub = '" . $_GET["IDClub"] . "'";
endif;

$TablaReserva = "ReservaGeneral";

if (!empty($_GET["FechaInicio"])) :
	$condicion_fecha  .= " and FechaTrCr >= '" . $_GET["FechaInicio"] . "'";
	$condicion_fecha_reserva  .= " and Fecha >= '" . $_GET["FechaInicio"] . "'";
	if ((int)substr($_GET["FechaInicio"], 0, 4) <= 2017)
		$TablaReserva = "ReservaGeneralBck";
endif;

if (!empty($_GET["FechaFin"])) :
	$condicion_fecha  .= " and FechaTrCr <= '" . $_GET["FechaFin"] . "'";
	$condicion_fecha_reserva  .= " and Fecha <= '" . $_GET["FechaFin"] . "'";
endif;

//echo $condicion;

if (SIMUser::get("Nivel") == 0) :
	$condicion_club1 = $_GET["IDClub"];
//$condicion_club = " IDClub >0 ";
else :
	$condicion_club1 = " IDClub = '" . SIMUser::get("club") . "'";
endif;


//traer todos los clubes en el sistema
$sql_clubes_busqueda = "SELECT * FROM Club Where " . $condicion_club1 . " " . $condicion_club_busqueda;
$qry_clubes_busqueda = $dbo->query($sql_clubes_busqueda);
while ($r_clubes_busqueda = $dbo->fetchArray($qry_clubes_busqueda))
	$array_clubes_busqueda[$r_clubes_busqueda["IDClub"]] = $r_clubes_busqueda;


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


							<form class="form-horizontal formvalida" id="frmfrmBuscarEstadisticas" name="frmfrmBuscarEstadisticas" role="form" action="<?php echo SIMUtil::lastURI() ?>" method="get">

								<div class="col-xs-12 col-sm-12">



									<table id="simple-table" class="table table-striped table-bordered table-hover">
										<tr>
											<td>Club</td>
											<td>
												<select name="IDClub" id="IDClubEstadisticas" class="form-control">
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
											<td><input type="text" id="FechaFin" name="FechaFin" placeholder="Fecha Fin" class="col-xs-12 calendar" title="fecha fin" value=""></td>
										</tr>
										<tr>
											<td colspan="6" align="center">

												<input type="hidden" name="action" value="search">
												<span class="input-group-btn">

													<button type="button" class="btn btn-purple btn-sm btnEnviar" rel="frmfrmBuscarEstadisticas">
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


							<?php
							$IDClub = $_GET["IDClub"];
							$FechaInicio = $_GET["FechaInicio"];
							$FechaFin = $_GET["FechaFin"];


							if (!empty($IDClub) && !empty($FechaInicio) && !empty($FechaFin)) {


								foreach ($array_clubes_busqueda as $idclub => $datos_club) {
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
																<th>Resumen Socios</th>
																<th>Total</th>
															</tr>
														</thead>

														<tbody>
															<tr>
																<td>
																	Total Socios
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
																<td>Socios Activos en app a la fecha</td>
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
																<td>Socios Nuevos en el periodo</td>
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
																<td>Socios activos en el periodo</td>
																<td>

																	<?php
																	$activos_periodo = $total_socio_activo - $total_socio_nuevo;
																	echo number_format($activos_periodo, 0, ",", ".");
																	?>

																</td>
															</tr>

															<!-- 	<tr>
															<td>
																Noticias
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
																Eventos
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
																Galerias
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
															<td>Invitados V1</td>
															<td><?php
																$sql_invitado = "Select count(IDSocioInvitado) TotalInvitado From SocioInvitado Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_invitado = $dbo->query($sql_invitado);
																$row_invitado = $dbo->fetchArray($result_invitado);
																$total_invitado = $row_invitado["TotalInvitado"];
																echo number_format($total_invitado, 0, ",", ".");
																?></td>
														</tr>

														<tr>
															<td>Invitados V2</td>
															<td><?php
																$sql_invitado = "Select count(IDSocioInvitadoEspecial) TotalInvitado From SocioInvitadoEspecial Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_invitado = $dbo->query($sql_invitado);
																$row_invitado = $dbo->fetchArray($result_invitado);
																$total_invitado = $row_invitado["TotalInvitado"];
																echo number_format($total_invitado, 0, ",", ".");
																?></td>
														</tr>

														<tr>
															<td>Autorizacion Contratista</td>
															<td><?php
																$sql_invitado = "Select count(IDSocioAutorizacion) TotalInvitado From SocioAutorizacion Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																$result_invitado = $dbo->query($sql_invitado);
																$row_invitado = $dbo->fetchArray($result_invitado);
																$total_invitado = $row_invitado["TotalInvitado"];
																echo number_format($total_invitado, 0, ",", ".");
																?></td>
														</tr>
 -->
															<tr>
																<td>Total Socios que realizaron reservas</td>
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
																				<th>Servicio</th>
																				<th>Total</th>
																				<th>Capacidad Aprox.</th>
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





																							$dias_consulta = $total_dia_real;

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
																					<b>TOTAL RESERVAS</b>
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
															<tr>
																<td colspan="2">
																	<table id="simple-table" class="table table-striped table-bordered table-hover">
																		<thead>
																			<tr>
																				<th colspan="4">Modulos Activos</th>

																			</tr>
																		</thead>

																		<tbody>
																			<tr>
																				<td>
																					<?php
																					//traer servicios maestros activos
																					$sql_modulo_activos = " SELECT CM.IDModulo,M.Nombre FROM ClubModulo CM,Modulo M where  CM.IDModulo=M.IDModulo AND IDClub='"  . $idclub . "' AND CM.Activo='S'";

																					$qry_modulo_activos = $dbo->query($sql_modulo_activos);

																					$columnas = 0;
																					while ($r_modulo_activos = $dbo->fetchArray($qry_modulo_activos)) {
																						//noticias
																						if ($r_modulo_activos["IDModulo"] == 3)
																							$noticias1 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 66)
																							$noticias2 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 81)
																							$noticias3 = $r_modulo_activos["IDModulo"];

																						//eventos
																						if ($r_modulo_activos["IDModulo"] == 4)
																							$evento1 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 76)
																							$evento2 = $r_modulo_activos["IDModulo"];

																						//galerias
																						if ($r_modulo_activos["IDModulo"] == 5)
																							$galeria1 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 150)
																							$galeria2 = $r_modulo_activos["IDModulo"];

																						//clasificados
																						if ($r_modulo_activos["IDModulo"] == 46)
																							$clasificado1 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 97)
																							$clasificado2 = $r_modulo_activos["IDModulo"];

																						//domicilios socios
																						if ($r_modulo_activos["IDModulo"] == 33)
																							$domicilio1 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 98)
																							$domicilio2 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 112)
																							$domicilio3 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 113)
																							$domicilio4 = $r_modulo_activos["IDModulo"];

																						//domicilios usuarios
																						if ($r_modulo_activos["IDModulo"] == 33)
																							$domicilio1 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 98)
																							$domicilio2 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 112)
																							$domicilio3 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 113)
																							$domicilio4 = $r_modulo_activos["IDModulo"];

																						//encuestas
																						if ($r_modulo_activos["IDModulo"] == 58)
																							$encuestas1 = $r_modulo_activos["IDModulo"];

																						if ($r_modulo_activos["IDModulo"] == 101)
																							$encuestacalificacion = $r_modulo_activos["IDModulo"];


																						$columnas++;

																						echo $r_modulo_activos["Nombre"];
																					?>
																				</td>
																				<?php
																						if ($columnas == 4) {
																							$columnas = 0;
																				?>

																			</tr>
																			<tr>
																				<td>


																				<?php } else { ?>
																				<td>


																			<?php }
																					} ?>
																			<!-- end else -->
																			<!-- end while -->
																			</tr>
																		</tbody>
																	</table>


																</td>
															</tr>

															<tr>
																<td colspan="2">
																	<table id="simple-table" class="table table-striped table-bordered table-hover">
																		<thead>
																			<tr>
																				<th colspan="1">Modulos </th>
																				<th colspan="1">Total</th>

																			</tr>
																		</thead>

																		<tbody>
																			<?php if (!empty($encuestas1)) { ?>
																				<tr>
																					<td>
																						Encuestas 1
																					</td>
																					<td>

																						<?php
																						$sql_encuestas1 = "Select count(IDEncuesta) TotalEncuesta From Encuesta Where IDClub = '" . $idclub . "' AND IDModulo=0 " . $condicion_fecha;
																						$result_encuestas1 = $dbo->query($sql_encuestas1);
																						$row_encuestas1 = $dbo->fetchArray($result_encuestas1);
																						$total_encuestas1 = $row_encuestas1["TotalEncuesta"];
																						echo number_format($total_encuestas1, 0, ",", ".");
																						?>

																					</td>
																				</tr>
																			<?php } ?>

																			<?php if (!empty($encuestacalificacion)) { ?>
																				<tr>
																					<td>
																						Encuesta Calificacion
																					</td>
																					<td>

																						<?php
																						$sql_encuestas2 = "Select count(IDEncuesta2) TotalEncuesta From Encuesta2 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_encuestas2 = $dbo->query($sql_encuestas2);
																						$row_encuestas2 = $dbo->fetchArray($result_encuestas2);
																						$total_encuestas2 = $row_encuestas2["TotalEncuesta"];
																						echo number_format($total_encuestas2, 0, ",", ".");
																						?>

																					</td>
																				</tr>
																			<?php } ?>

																			<tr>
																				<td>
																					Encuestas Infinita
																				</td>
																				<td>

																					<?php
																					$sql_encuestas_infinita = "Select count(IDEncuesta) TotalEncuestaInfinita From Encuesta Where IDClub = '" . $idclub . "' AND IDModulo > 0  " . $condicion_fecha;
																					$result_encuestas_infinita = $dbo->query($sql_encuestas_infinita);
																					$row_encuestas_infinita = $dbo->fetchArray($result_encuestas_infinita);
																					$total_encuestas_infinita = $row_encuestas_infinita["TotalEncuestaInfinita"];
																					echo number_format($total_encuestas_infinita, 0, ",", ".");


																					?>

																				</td>
																			</tr>


																			<?php if (!empty($noticias1)) { ?>
																				<tr>
																					<td>
																						Noticias 1
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
																			<?php } ?>

																			<?php if (!empty($noticias2)) { ?>
																				<tr>
																					<td>
																						Noticias 2
																					</td>
																					<td>

																						<?php
																						$sql_noticia2 = "Select count(IDNoticia) TotalNoticia From Noticia2 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_noticia2 = $dbo->query($sql_noticia2);
																						$row_noticia2 = $dbo->fetchArray($result_noticia2);
																						$total_noticia2 = $row_noticia2["TotalNoticia"];
																						echo number_format($total_noticia2, 0, ",", ".");
																						?>

																					</td>
																				</tr>
																			<?php } ?>

																			<?php if (!empty($noticias3)) { ?>
																				<tr>
																					<td>
																						Noticias 3
																					</td>
																					<td>

																						<?php
																						$sql_noticia3 = "Select count(IDNoticia) TotalNoticia From Noticia3 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_noticia3 = $dbo->query($sql_noticia3);
																						$row_noticia3 = $dbo->fetchArray($result_noticia3);
																						$total_noticia3 = $row_noticia3["TotalNoticia"];
																						echo number_format($total_noticia3, 0, ",", ".");
																						?>

																					</td>
																				</tr>
																			<?php } ?>



																			<tr>
																				<td>
																					Noticias Infinitas
																				</td>
																				<td>

																					<?php
																					$sql_noticia_infinita = "Select count(IDNoticiaInfinita) TotalNoticia From NoticiaInfinita Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																					$result_noticia_infinita = $dbo->query($sql_noticia_infinita);
																					$row_noticia_infinita = $dbo->fetchArray($result_noticia_infinita);
																					$total_noticia_infinita = $row_noticia_infinita["TotalNoticia"];
																					echo number_format($total_noticia_infinita, 0, ",", ".");
																					?>

																				</td>
																			</tr>


																			<?php if (!empty($evento1)) { ?>
																				<tr>
																					<td>
																						Eventos 1
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
																			<?php } ?>

																			<?php if (!empty($evento2)) { ?>
																				<tr>
																					<td>
																						Eventos 2
																					</td>
																					<td>

																						<?php
																						$sql_evento2 = "Select count(IDEvento2) TotalEvento From Evento2 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_evento2 = $dbo->query($sql_evento2);
																						$row_evento2 = $dbo->fetchArray($result_evento2);
																						$total_evento2 = $row_evento2["TotalEvento"];
																						echo number_format($total_evento2, 0, ",", ".");
																						?>

																					</td>
																				</tr>
																			<?php } ?>

																			<?php if (!empty($galeria1)) { ?>
																				<tr>
																					<td>
																						Galerias 1
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
																			<?php } ?>

																			<?php if (!empty($galeria1)) { ?>
																				<tr>
																					<td>
																						Galerias 2
																					</td>
																					<td>

																						<?php
																						$sql_galeria2 = "Select count(IDGaleria2) TotalGaleria From Galeria2 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_galeria2 = $dbo->query($sql_galeria2);
																						$row_galeria2 = $dbo->fetchArray($result_galeria2);
																						$total_galeria2 = $row_galeria2["TotalGaleria"];
																						echo number_format($total_galeria2, 0, ",", ".");
																						?>


																					</td>
																				</tr>

																			<?php } ?>

																			<?php if (!empty($clasificado1)) { ?>
																				<tr>
																					<td>
																						Clasificados Socios
																					</td>
																					<td>

																						<?php
																						$sql_clasificado = "Select count(IDClasificado) TotalClasificado From Clasificado Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_clasificado = $dbo->query($sql_clasificado);
																						$row_clasificado = $dbo->fetchArray($result_clasificado);
																						$total_clasificado = $row_clasificado["TotalClasificado"];
																						echo number_format($total_clasificado, 0, ",", ".");
																						?>


																					</td>
																				</tr>
																			<?php } ?>

																			<?php if (!empty($clasificado2)) { ?>

																				<tr>
																					<td>
																						Clasificados Funcionarios
																					</td>
																					<td>

																						<?php
																						$sql_clasificado2 = "Select count(IDClasificado2) TotalClasificado From Clasificado2 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_clasificado2 = $dbo->query($sql_clasificado2);
																						$row_clasificado2 = $dbo->fetchArray($result_clasificado2);
																						$total_clasificado2 = $row_clasificado2["TotalClasificado"];
																						echo number_format($total_clasificado2, 0, ",", ".");
																						?>


																					</td>
																				</tr>
																			<?php } ?>

																			<?php if (!empty($domicilio1)) { ?>

																				<tr>
																					<td>
																						Domicilio 1
																					</td>
																					<td>

																						<?php
																						$sql_domicilio1 = "Select count(IDDomicilio) TotalDomicilio From Domicilio Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_domicilio1 = $dbo->query($sql_domicilio1);
																						$row_domicilio1 = $dbo->fetchArray($result_domicilio1);
																						$total_domicilio1 = $row_domicilio1["TotalDomicilio"];
																						echo number_format($total_domicilio1, 0, ",", ".");
																						?>


																					</td>
																				</tr>
																			<?php } ?>

																			<?php if (!empty($domicilio2)) { ?>

																				<tr>
																					<td>
																						Domicilio 2
																					</td>
																					<td>

																						<?php
																						$sql_domicilio2 = "Select count(IDDomicilio) TotalDomicilio From Domicilio2 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_domicilio2 = $dbo->query($sql_domicilio2);
																						$row_domicilio2 = $dbo->fetchArray($result_domicilio2);
																						$total_domicilio2 = $row_domicilio2["TotalDomicilio"];
																						echo number_format($total_domicilio2, 0, ",", ".");
																						?>


																					</td>
																				</tr>
																			<?php } ?>

																			<?php if (!empty($domicilio3)) { ?>

																				<tr>
																					<td>
																						Domicilio 3
																					</td>
																					<td>

																						<?php
																						$sql_domicilio3 = "Select count(IDDomicilio) TotalDomicilio From Domicilio3 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_domicilio3 = $dbo->query($sql_domicilio3);
																						$row_domicilio3 = $dbo->fetchArray($result_domicilio3);
																						$total_domicilio3 = $row_domicilio3["TotalDomicilio"];
																						echo number_format($total_domicilio3, 0, ",", ".");
																						?>


																					</td>
																				</tr>
																			<?php } ?>

																			<?php if (!empty($domicilio4)) { ?>

																				<tr>
																					<td>
																						Domicilio 4
																					</td>
																					<td>

																						<?php
																						$sql_domicilio4 = "Select count(IDDomicilio) TotalDomicilio From Domicilio4 Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																						$result_domicilio4 = $dbo->query($sql_domicilio4);
																						$row_domicilio4 = $dbo->fetchArray($result_domicilio4);
																						$total_domicilio4 = $row_domicilio4["TotalDomicilio"];
																						echo number_format($total_domicilio4, 0, ",", ".");
																						?>


																					</td>
																				</tr>
																			<?php } ?>
																			<tr>
																				<td>Invitados V1</td>
																				<td><?php
																					$sql_invitado = "Select count(IDSocioInvitado) TotalInvitado From SocioInvitado Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																					$result_invitado = $dbo->query($sql_invitado);
																					$row_invitado = $dbo->fetchArray($result_invitado);
																					$total_invitado = $row_invitado["TotalInvitado"];
																					echo number_format($total_invitado, 0, ",", ".");
																					?></td>
																			</tr>

																			<tr>
																				<td>Invitados V2</td>
																				<td><?php
																					$sql_invitado = "Select count(IDSocioInvitadoEspecial) TotalInvitado From SocioInvitadoEspecial Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																					$result_invitado = $dbo->query($sql_invitado);
																					$row_invitado = $dbo->fetchArray($result_invitado);
																					$total_invitado = $row_invitado["TotalInvitado"];
																					echo number_format($total_invitado, 0, ",", ".");
																					?></td>
																			</tr>

																			<tr>
																				<td>Autorizacion Contratista</td>
																				<td><?php
																					$sql_invitado = "Select count(IDSocioAutorizacion) TotalInvitado From SocioAutorizacion Where IDClub = '" . $idclub . "' " . $condicion_fecha;
																					$result_invitado = $dbo->query($sql_invitado);
																					$row_invitado = $dbo->fetchArray($result_invitado);
																					$total_invitado = $row_invitado["TotalInvitado"];
																					echo number_format($total_invitado, 0, ",", ".");
																					?></td>
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


							<?php  }
							}
							?>




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