<?php
setlocale(LC_ALL, 'es_ES');

$data_chartFormaPago = '';
foreach ($array_dataFormaPago as  $FormaPago) {
	list($tipo, $total) = explode(",", $FormaPago);
	$data_chartFormaPago .= "['" . $tipo . "'," . $total . "],";
}
$data_chartMetodoPago = '';
foreach ($array_dataMetodoPago as  $MetodoPago) {
	list($tipo, $total) = explode(",", $MetodoPago);
	$data_chartMetodoPago .= "['" . $tipo . "'," . $total . "],";
}
$data_chartMesPago = '';
$data_MesPago = [];
foreach ($array_dataMesPago as  $MesPago) {
	list($tipo, $total) = explode(",", $MesPago);
	$dateObj   = DateTime::createFromFormat('!m', $tipo);
	$tipo = strftime('%B', $dateObj->getTimestamp());
	$tipo = ucfirst(substr($tipo, 0, 3));
	// $data_chartMesPago .= "['" . $tipo . "'," . $total . "],";
	$data_MesPago[$tipo] = $total;
}
for ($i = 1; $i <= 12; $i++) {
	$dateObj = DateTime::createFromFormat('!m', $i);
	$mes = strftime('%B', $dateObj->getTimestamp());
	$mes = ucfirst(substr($mes, 0, 3));
	if (array_key_exists($mes, $data_MesPago)) {
		$data_chartMesPago .= "['" . $mes . "'," . $data_MesPago[$mes] . "],";
	} else {
		$data_chartMesPago .= "['" . $mes . "',0],";
	}
}
?>
<style>
	.flex {
		-webkit-box-flex: 1;
		-ms-flex: 1 1 auto;
		flex: 1 1 auto
	}

	#container {
		width: 100%;
		height: 100%;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	@media (max-width:991.98px) {
		.padding {
			padding: 1.5rem
		}
	}

	@media (max-width:767.98px) {
		.padding {
			padding: 1rem
		}
	}

	.padding {
		padding: 5rem
	}

	.card {
		background: #fff;
		border-width: 0;
		border-radius: .25rem;
		box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
		margin-bottom: 1.5rem
	}

	.card {
		position: relative;
		display: flex;
		flex-direction: column;
		min-width: 0;
		word-wrap: break-word;
		background-color: #fff;
		background-clip: border-box;
		border: 1px solid rgba(19, 24, 44, .125);
		border-radius: .25rem
	}

	.card-header {
		padding: .75rem 1.25rem;
		margin-bottom: 0;
		background-color: rgba(19, 24, 44, .03);
		border-bottom: 1px solid rgba(19, 24, 44, .125)
	}

	.card-body {
		width: 100%;
		height: 100%;
	}

	.card-header:first-child {
		border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0
	}

	.card-footer,
	.card-header {
		background-color: transparent;
		border-color: rgba(160, 175, 185, .15);
		background-clip: padding-box
	}

	.card-info,
	.card-charts {
		background-color: #97b1c8;
		border-radius: 1rem;
		text-align: center;
		color: #000;
		margin: 5px;
		padding-top: 1rem;
		padding-bottom: 3rem;
	}

	.card-charts {
		background-color: #b5ddb7;
		padding: 1rem;
	}

	#top_x_div {
		width: 100%;
		height: 100%;
	}
</style>
<div class="widget-box transparent" id="recent-box">
	<div class="widget-body">
		<div class="widget-main padding-4">
			<div class="tabbable" id="myTABS" role="tablist">
				<ul class="nav nav-tabs" id="myTab">
					<li class="active">
						<a data-toggle="tab" href="#home">
							<i class="green ace-icon fa fa-bar-chart-o bigger-120"></i>
							Estad&iacute;sticas
						</a>
					</li>
					<li class="">
						<a data-toggle="tab" href="#parametros" role="tab">
							<i class="green ace-icon fa fa-bar-chart-o bigger-120"></i>
							Estad&iacute;sticas por Parametros
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div id="home" class="tab-pane fade in active">
						<!-- INICIO TAB DASHBOARD -->
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-8">
									<table id="simple-table" class="table table-striped table-bordered table-hover" style="width:840px;">
										<tr>
											<td>
												<select name="AnioInicio" id="AnioInicio" class="col-xs-12 calendar" title="A&ntilde;o">
													<option value="">[ Seleccione ]</option>
													<?php
													$sql_HistoriasCuotasSociales = "SELECT date_format(FechaInicioPeriodo,'%Y') AS anio FROM HistorialCuotasSociales ORDER BY FechaInicioPeriodo ASC LIMIT 1";
													$q_anio = $dbo->query($sql_HistoriasCuotasSociales);
													$r_anio = $dbo->assoc($q_anio);
													$anioPasado = $r_anio['anio'];
													$AnioActual = date('Y', strtotime(date('Y-m-d')));


													for ($i = $anioPasado; $i <= $AnioActual; $i++) {
														$selected = ($i == $AnioActual) ? 'selected' : '';
													?>
														<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
													<?php } ?>
												</select>
											</td>
											<td>
												<select name="MesInicio" id="MesInicio" class="col-xs-12 calendar" title="Mes">
													<option value="">[ Seleccione ]</option>
													<?php
													$mesActual = date('m', strtotime(date('Y-m-d')));
													$mesActual = intval($mesActual);
													$mesActual = SIMResources::$meses[$mesActual - 1];
													$meses = SIMResources::$meses;
													foreach ($meses as $indice => $valor) {
														$selected = ($valor == $mesActual) ? 'selected' : '';
													?>
														<option value="<?php echo $indice + 1; ?>" <?php echo $selected; ?>><?php echo $valor; ?></option>
													<?php } ?>
												</select>
											</td>
											<td style="width:100px;">
												<button type="button" class="btn btn-primary btn-sm" id="">
													<span class="ace-icon fa fa-search icon-on-right bigger-110 "></span>
													Buscar
												</button>
											</td>
											<td>
												<input type="radio" name="tipoencabezado" value="Indice" checked>Encabezado con indices<br>
											</td>
											<td style="width:220px;">
												<button type="button" class="btn btn-info btn-sm" id="btExporta">
													<span class="ace-icon fa  fa-cloud-download icon-on-right bigger-110"></span>
													Exportar
												</button>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="col-md-12">
								<div class="col-md-6">
									<div class="col-md-5 card-info">
										<h4>Total pagos</h4>
										<h2><?php echo $cant_Pagos['Total']; ?></h2>
										<label for="">Monto UF</label>
									</div>
									<div class="col-md-5 card-info">
										<h4>Lockers</h4>
										<h2>0</h2>
										<label for="">Monto UF</label>
									</div>
									<div class="col-md-5 card-info">
										<h4>Cuotas Sociales</h4>
										<h2><?php echo $cant_CuotasSociales['Total']; ?></h2>
										<label for="">Monto UF</label>
									</div>
									<div class="col-md-5 card-info">
										<h4>Cuota de Incorporaci&oacute;n</h4>
										<h2>0</h2>
										<label for="">Monto UF</label>
									</div>
									<div class="col-md-5 card-info">
										<h4>Estacionamiento de carros</h4>
										<h2>0</h2>
										<label for="">Monto UF</label>
									</div>
									<div class="col-md-5 card-info">
										<h4>Diferencia de cambio o intereses</h4>
										<h2>0</h2>
										<label for="">Monto UF</label>
									</div>
								</div>
								<div class="col-md-6">
									<div class="col-md-12 card-charts">
										<h4>Ingresos en UF por forma de pago</h4>
										<div id="top_x_div"></div>
									</div>
									<div class="col-md-12 card-charts">
										<h4>Nº de operaciones por forma de pago</h4>
										<div id="top_x_div2"></div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="col-md-12 card-charts">
									<h4>Mes que paga</h4>
									<div id="top_x_div3"></div>
								</div>
							</div>
						</div>
						<!-- FIN TAB DASHBOARD-->
					</div>
					<div id="parametros" class="tab-pane fade">
						<!-- INICIO TAB DASHBOARD -->
						<div class="row">
							<div class="">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-pie-chart green"></i>
									Reporte Tipo Socio
								</h3>
							</div>
							<div class="col-md-12">
								<div class="col-md-4">
									<table class="table">
										<thead>
											<tr>
												<th scope="col">Tipo Socio</th>
												<th scope="col">#</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$data_chartTipoSocio = '';
											foreach ($array_dataTipo as  $tipoEmpleado) {
												list($tipo, $total) = explode(",", $tipoEmpleado);
												echo '<tr> <td scope="row">' . $tipo . '</td>
													<td>' . $total . '</td></tr>';
												$data_chartTipoSocio .= "['" . $tipo . "'," . $total . "],";
											}
											?>

										</tbody>
									</table>
								</div>
								<div class="col-md-8">
									<div id="container">
										<div id="donutchartTipoSocio" style="width: 900px; height: 500px;"></div>
									</div>
								</div>
							</div>
							<div class="">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-pie-chart green"></i>
									Reporte Categoria
								</h3>
							</div>
							<div class="col-md-12">
								<div class="col-md-4">
									<table class="table">
										<thead>
											<tr>
												<th scope="col">Categoria</th>
												<th scope="col">#</th>
											</tr>
										</thead>
										<tbody>

											<?php
											foreach ($array_dataCategoria as  $CategoriaEmpleado) {

												list($tipo, $total) = explode(",", $CategoriaEmpleado);

												echo '<tr> <td scope="row">' . $tipo . '</td>
												<td>' . $total . '</td></tr>';
												$data_chartCategoria .= "['" . $tipo . "'," . $total . "],";
											}
											?>

										</tbody>
									</table>
								</div>
								<div class="col-md-8">
									<div id="container">
										<div id="donutchartCategoria" style="width: 900px; height: 500px;"></div>
									</div>
								</div>
							</div>
							<div class="">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-pie-chart green"></i>
									Reporte Parentesco
								</h3>
							</div>
							<div class="col-md-12">
								<div class="col-md-4">
									<table class="table">
										<thead>
											<tr>
												<th scope="col">Parentesco</th>
												<th scope="col">#</th>
											</tr>
										</thead>
										<tbody>

											<?php
											foreach ($array_dataParentesco as  $ParentescoEmpleado) {

												list($tipo, $total) = explode(",", $ParentescoEmpleado);

												echo '<tr> <td scope="row">' . $tipo . '</td>
												<td>' . $total . '</td></tr>';
												$data_chartParentesco .= "['" . $tipo . "'," . $total . "],";
											}
											?>
										</tbody>
									</table>
								</div>
								<div class="col-md-8">
									<div id="container">
										<div id="donutchartParentesco" style="width: 900px; height: 500px;"></div>
									</div>
								</div>
							</div>
							<div class="">
								<h3 class="widget-title grey lighter">
									<i class="ace-icon fa fa-pie-chart green"></i>
									Reporte Estado Civil
								</h3>
							</div>
							<div class="col-md-12">
								<div class="col-md-4">
									<table class="table">
										<thead>
											<tr>
												<th scope="col">Estado Civil</th>
												<th scope="col">#</th>
											</tr>
										</thead>
										<tbody>

											<?php
											foreach ($array_dataEstadoCivil as  $EstadoCivilEmpleado) {

												list($tipo, $total) = explode(",", $EstadoCivilEmpleado);

												echo '<tr> <td scope="row">' . $tipo . '</td>
												<td>' . $total . '</td></tr>';
												$data_chartEstadoCivil .= "['" . $tipo . "'," . $total . "],";
											}
											?>
										</tbody>
									</table>
								</div>
								<div class="col-md-8">
									<div id="container">
										<div id="donutchartEstadoCivil" style="width: 900px; height: 500px;"></div>
									</div>
								</div>
							</div>
						</div>
						<!-- FIN TAB DASHBOARD-->
					</div>
				</div>
			</div>
		</div>


	</div>
</div>
<?
include("cmp/footer_grid_chart.php");
// include("cmp/footer_scripts.php");
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load("current", {
		packages: ["corechart"]
	});
	google.charts.load("current", {
		packages: ['bar']
	});

	google.charts.setOnLoadCallback(drawChartTipoSocio);
	google.charts.setOnLoadCallback(drawChartCategoria);
	google.charts.setOnLoadCallback(drawChartParentesco);
	google.charts.setOnLoadCallback(drawChartEstadoCivil);
	google.charts.setOnLoadCallback(drawStuff);
	google.charts.setOnLoadCallback(drawStuff2);
	google.charts.setOnLoadCallback(drawStuff3);

	function drawChartTipoSocio() {
		var data = google.visualization.arrayToDataTable([
			['Task', 'Tipo socio'],
			<?php echo $data_chartTipoSocio; ?>
		]);
		var options = {
			width: 700,
			height: 500,
			title: 'Tipos de socio por socio',
			pieHole: 0.4,
		};
		var chart = new google.visualization.PieChart(document.getElementById('donutchartTipoSocio'));
		chart.draw(data, options);
	}

	function drawChartCategoria() {
		var data = google.visualization.arrayToDataTable([
			['Task', 'Categoria por socio'],
			<?php echo $data_chartCategoria; ?>
		]);
		var options = {
			width: 700,
			height: 500,
			title: 'Categorias por socio',
			pieHole: 0.4,
		};
		var chart = new google.visualization.PieChart(document.getElementById('donutchartCategoria'));
		chart.draw(data, options);
	}

	function drawChartParentesco() {
		var data = google.visualization.arrayToDataTable([
			['Task', 'Parentesco por socio'],
			<?php echo $data_chartParentesco; ?>
		]);
		var options = {
			width: 700,
			height: 500,
			title: 'Parentesco por socio',
			pieHole: 0.4,
		};
		var chart = new google.visualization.PieChart(document.getElementById('donutchartParentesco'));
		chart.draw(data, options);
	}

	function drawChartEstadoCivil() {
		var data = google.visualization.arrayToDataTable([
			['Task', 'Estado Civil por socio'],
			<?php echo $data_chartParentesco; ?>
		]);
		var options = {
			width: 700,
			height: 500,
			title: 'Estado Civil por socio',
			pieHole: 0.4,
		};
		var chart = new google.visualization.PieChart(document.getElementById('donutchartEstadoCivil'));
		chart.draw(data, options);
	}

	function drawStuff() {
		var data = new google.visualization.arrayToDataTable([
			['Método Pago', 'Ingresos UF'],
			<?php echo $data_chartFormaPago; ?>
		]);
		var options = {
			chartArea: {
				backgroundColor: 'transparent'
			},
			backgroundColor: {
				fill: 'transparent'
			},
			bar: {
				groupWidth: "55%"
			},


		};
		var chart = new google.charts.Bar(document.getElementById('top_x_div'));
		// Convert the Classic options to Material options.
		chart.draw(data, google.charts.Bar.convertOptions(options));
	};

	function drawStuff2() {
		var data = new google.visualization.arrayToDataTable([
			['Método Pago', 'Ingresos UF'],
			<?php echo $data_chartMetodoPago; ?>
		]);
		var options = {
			chartArea: {
				backgroundColor: 'transparent'
			},
			backgroundColor: {
				fill: 'transparent'
			},
			bar: {
				groupWidth: "55%"
			},


		};
		var chart = new google.charts.Bar(document.getElementById('top_x_div2'));
		// Convert the Classic options to Material options.
		chart.draw(data, google.charts.Bar.convertOptions(options));
	};

	function drawStuff3() {
		var data = new google.visualization.arrayToDataTable([
			['Mes Pago', 'Ingresos UF'],
			<?php echo $data_chartMesPago; ?>
		]);
		var options = {
			chartArea: {
				backgroundColor: 'transparent'
			},
			backgroundColor: {
				fill: 'transparent'
			},
			bar: {
				groupWidth: "55%"
			},


		};
		var chart = new google.charts.Bar(document.getElementById('top_x_div3'));
		// Convert the Classic options to Material options.
		chart.draw(data, google.charts.Bar.convertOptions(options));
	};
	$(document).ready(function() {
		$("#btExporta").click(function() {
			var AnioInicio = $("#AnioInicio").val();
			var MesInicio = $("#MesInicio").val();
			var TipoEncabezado = $('input:radio[name=tipoencabezado]:checked').val();
			window.location.href = "./procedures/excel-reporte-historial-socios.php?a=" + AnioInicio + "&m=" + MesInicio;
		});
	});
</script>