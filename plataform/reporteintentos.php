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
											<td>Fecha Apertura reserva</td>
											<td>
												<input type="text" id="FechaInicio" name="FechaInicio" placeholder="Fecha Inicio" class="col-xs-12 calendar" title="fecha inicio" value="">
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
											$Fecha = $_GET["FechaInicio"];
											$IDServicio = (int)$_GET["IDServicioMaestro"];

											$dt1 = date($Fecha . "00:00:00");
											$dt2 = date($Fecha . "23:59:59");

											if (!empty($IDClub) && !empty($Fecha) && !empty($IDServicio)) {


												$sql_socios = "SELECT IDSocio,NumeroDocumento,Accion,Nombre,Apellido FROM Socio WHERE IDClub = '" . $IDClub . "'";
												$r_socios = $dbo->query($sql_socios);
												while ($row_socios = $dbo->fetchArray($r_socios)) {
													$array_socios[$row_socios["IDSocio"]] = $row_socios;
												}

												$sql_elemento = "SELECT IDServicioElemento,Nombre FROM ServicioElemento WHERE IDServicio = '" . $IDServicio . "'";
												$r_elemento = $dbo->query($sql_elemento);
												while ($row_elemento = $dbo->fetchArray($r_elemento)) {
													$array_elemento[$row_elemento["IDServicioElemento"]] = $row_elemento;
												}


												//$busqueda="/.*2021-05-27.*/";
												//$filter = ['IDServicio' => 21696, 'FechaPeticionTexto'=>'2021-05-27 18:39:04.933100'];
												//$filter = ['IDServicio' => $IDServicio,'FechaPeticionTexto'=>new MongoDB\BSON\Regex('^'.$Fecha, 'i')];


												$filter = ['IDServicio' => $IDServicio, 'FechaPeticion' => array('$gte' => new MongoDB\BSON\UTCDateTime(strtotime("$dt1") * 1000), '$lte' => new MongoDB\BSON\UTCDateTime(strtotime("$dt2") * 1000))];

												//$filter = ['IDServicio' => 21696];
												$Coleccion = "Operacion";
												$options = [
													'projection' => ['_id' => 0],
													'sort' => ['IDServicio' => -1],
												];


												$query = new MongoDB\Driver\Query($filter, $options);
												$cursor = $dblinkMongo->executeQuery(DBNAMEMongo . '.Operacion', $query);

											?>
												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td>Movimiento</td>
														<td>Respuesta APP</td>
														<td>Servicio</td>
														<td>Elemento</td>
														<td>Tee</td>
														<td>Socio</td>
														<td>Fecha del turno</td>
														<td>Hora del turno</td>
														<td>Dispositivo</td>
														<td>Fecha Peticion</td>
													</tr>
													<?php
													$contador_intento = 0;
													$contador_exitosa = 0;
													foreach ($cursor as $row_separa_servicio) {


														$mostrar = "N";
														if ($row_separa_servicio->Servicio == "setreservageneral") {
															$mostrar = "S";
														} elseif ($row_separa_servicio->Servicio == "setseparareserva") {

															$mostrar = "S";
														} elseif ($row_separa_servicio->Servicio == "getfechasdisponiblesservicio") {

															$mostrar = "S";
														} else {

															$mostrar = "N";
														}


														if ($mostrar == "S") {
													?>
															<tr>
																<td>
																	<?php




																	$mostrar = "N";

																	if ($row_separa_servicio->Servicio == "setreservageneral") {
																		$servicio = "Pulsar en confirmar reserva";
																		$mostrar = "S";
																		if ($row_separa_servicio->RespuestaServicio == "exitoso")
																			$contador_exitosa++;
																	} elseif ($row_separa_servicio->Servicio == "setseparareserva") {
																		$servicio = "Intento reserva";
																		$mostrar = "S";
																		$contador_intento++;
																	} elseif ($row_separa_servicio->Servicio == "getfechasdisponiblesservicio") {
																		$servicio = "Ver fechas disponibles";
																		$mostrar = "S";
																		$contador_intento++;
																	} else {
																		$servicio = $row_separa_servicio->Servicio;
																		$mostrar = "N";
																	}

																	echo $servicio; ?>
																</td>
																<td>
																	<?php

																	//if($array_respuesta["message"]=="Esta fecha au00fan no estu00e1 disponible")
																	$hora_pet = $row_separa_servicio->FechaPeticion;

																	if ($row_separa_servicio->RespuestaServicio == "Guardado") {
																		$respuestaapp = "exitoso";
																	} elseif ($row_separa_servicio->RespuestaServicio == "") {
																		$respuestaapp = "exitoso.";
																	} else {
																		$respuestaapp = $row_separa_servicio->RespuestaServicio;
																	}

																	if ($hora_pet >= "10:00:00" and $IDClub == "7" && $array_respuesta["message"] == "Esta fecha au00fan no estu00e1 disponible") {
																		$respuestaapp = "Lo sentimos la reserva ya fue o esta siendo tomada";
																	}
																	echo $respuestaapp;


																	?>

																</td>
																<td><?php

																	$NombreSer = $dbo->getFields("Servicio", "Nombre", "IDServicio = '" . $row_separa_servicio->IDServicio . "'");
																	if (empty($NombreSer)) {
																		$IDMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_separa_servicio->IDServicio . "'");
																		$NombreSer = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDMaestro . "'");
																	}
																	echo $NombreSer;
																	?>

																</td>
																<td>
																	<?php
																	if ((int)$row_separa_servicio->IDElemento > 0) {
																		echo $array_elemento[$row_separa_servicio->IDElemento]["Nombre"];
																	}
																	?>
																</td>
																<td><?php echo $array_parametros["Tee"]; ?></td>
																<td><?php

																	if (!empty($row_separa_servicio->IDSocio)) {
																		$IDSoc = $row_separa_servicio->IDSocio;
																		echo $array_socios[$IDSoc]["Nombre"] . " " . $array_socios[$IDSoc]["Apellido"];
																	} else {
																		if (!empty($array_usuario[$array_parametros["IDUsuario"]]["Nombre"])) {
																			echo "Empleado: " . $row_separa_servicio->LogServicioDiario . " : " . $array_usuario[$array_parametros["IDUsuario"]]["Nombre"];
																		} else {
																			echo "starter";
																		}
																	}


																	?>
																</td>
																<td><?php
																	if (!empty($row_separa_servicio->Fecha)) {
																		$utcdatetime = new MongoDB\BSON\UTCDateTime((string)$row_separa_servicio->Fecha);
																		$datetime = $utcdatetime->toDateTime();
																		//var_dump($datetime->format('r'));
																		//var_dump($datetime->format('U.u'));
																		//var_dump($datetime->getTimezone());
																		echo $datetime->format('Y-m-d');
																	}
																	?>
																</td>
																<td><?php echo $row_separa_servicio->DatosApp->Hora; ?></td>
																<td><?php echo $row_separa_servicio->DatosApp->Dispositivo; ?></td>

																<td><?php echo $row_separa_servicio->FechaPeticionTexto; ?></td>
															</tr>
													<?php
														}
													}	?>

												</table>


												<table id="simple-table" class="table table-striped table-bordered table-hover">
													<tr>
														<td>
															Resumen
														</td>
														<td></td>
													</tr>
													<tr>
														<td>
															Intentos
														</td>
														<td><?php echo $contador_intento; ?></td>
													</tr>
													<tr>
														<td>
															Reservas Exitosas
														</td>
														<td><?php echo $contador_exitosa; ?></td>
													</tr>

													<?
													//para lagartos consulto la de toda la semana en golf
													if ($IDClub == 7 && ($IDServicio == 98 || $IDServicio == 99)) {
														$FechaFin = date("Y-m-d", strtotime($FechaInicio . " 5 days"));
														$sql_reserv = "SELECT count(IDReservaGeneral) as TotalReservas FROM ReservaGeneral WHERE IDServicio in ($IDServicio) and Fecha>='" . $Fecha . "' and Fecha <= '" . $FechaFin . "'";
														$r_reserv = $dbo->query($sql_reserv);
														$row_reserva = $dbo->fetchArray($r_reserv);
														$TotalReservasSemana = $row_reserva["TotalReservas"];
													}
													?>
													<tr>
														<td>
															Reservas Exitosas Semana
														</td>
														<td><?php echo $TotalReservasSemana; ?></td>
													</tr>


												</table>

												<table>
													<tr>
														<td>
															<form name="frmexportareporteintentos" id="frmexportareporteintentos" method="post" enctype="multipart/form-data" action="procedures/excel-reportereservaintentos.php">
																<table>
																	<tr>
																		<div id="prueba1"></div>

																		<td>

																			<input type="hidden" name="FechaInicio" id="FechaInicio" value="<?php echo $_GET["FechaInicio"] ?>">
																			<input type="hidden" name="IDServicioMaestro" id="IDServicioMaestro" value="<?php echo $_GET["IDServicioMaestro"] ?>">
																			<input type="hidden" name="IDClub" id="IDClub" value="<?php echo $_GET["IDClub"] ?>">




																			<input class="btn btn-info" type="submit" name="expbnr" id="expbnr" value="Exportar">

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