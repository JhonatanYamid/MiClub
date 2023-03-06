	<?php

	if (!empty($_GET[IDSocio])) :
		$condiciones .= " and IDSocio = '" . $_GET["IDSocio"] . "'";
	endif;

	if (!empty($_GET[FechaDesde] && !empty($_GET[FechaDesde]))) :
		$condiciones .= " and FechaTrCr >= '" . $_GET["FechaDesde"] . " 00:00:00' and FechaTrCr <= '" . $_GET["FechaHasta"] . " 23:59:59' ";
	endif;



	if (!empty($_GET[IDServicioReporteRecargaTalonera])) :
		$condiciones .= " and  IDServicio='" . $_GET[IDServicioReporteRecargaTalonera] . "'";
	endif;

	if (!empty($_GET[IDElementoReporteRecargaTalonera])) :
		$condicionesElementos .= " and  IDServicioElemento='" . $_GET[IDElementoReporteRecargaTalonera] . "'";
	endif;

	/* 	$prueba = "Select COUNT(FechaTrCr) AS Conteo,CONCAT(MONTHNAME(FechaTrCr), ' ',YEAR(FechaTrCr) ) AS FECHA,IDServicio,IDServicioElemento From ReservaGeneral Where IDClub='" .  SIMUser::get("club") . "'" . $condicionesElementos . " GROUP BY MONTH(FechaTrCr), IDServicioElemento ";
	echo $prueba; */

	?>

	<div class="widget-box transparent" id="recent-box">
		<div class="widget-header">
			<h4 class="widget-title lighter smaller">
				<i class="ace-icon fa fa-users orange"></i>Reporte Taloneras Compradas
			</h4>
			<!-- 
			<table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr>
					<td><?= SIMUtil::get_traduccion('', '', 'TotalPqr', LANGSESSION); ?></td>
					<td>
						<?php
						$sql_entregados = "Select * From Pqr Where IDClub =  '" . SIMUser::get("club") . "' " . $condiciones . " " . $condicion_area;
						$result_entregados = $dbo->query($sql_entregados);
						echo $dbo->rows($result_entregados);
						?>

					</td>
				</tr>
			</table> -->


		</div>

		<div class="widget-body">
			<div class="widget-main padding-4">
				<div class="row">
					<div class="col-xs-12">
						<div id="container" style="width: 90%;">
							<canvas id="canvas"></canvas>
						</div>

						<div id="container_genero" style="width: 90%;">
							<canvas id="canvas_genero"></canvas>
						</div>

						<div id="canvas-holder" style="width:90%">
							<canvas id="chart-area" />
						</div>

						<div id="container" style="width: 90%;">
							<canvas id="canvas2"></canvas>
						</div>

						<div id="container" style="width: 90%;">
							<canvas id="canvas3"></canvas>
						</div>

						<!-- PAGE CONTENT ENDS -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<?
	include("cmp/footer_scripts.php");
	?>


	<script>
		var randomScalingFactor = function() {
			return Math.round(Math.random() * 100);
		};

		var randomColorFactor = function() {
			return Math.round(Math.random() * 255);
		};
		var randomColor = function() {
			return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',.7)';
		};

		var barChartData = {
			<?php

			$sql_talonera_comprada = "Select ValorPagado,IDServicio From SocioTalonera Where IDClub = '" . SIMUser::get("club") . "' and Activo = '1' " . $condiciones;
			$qry_talonera_comprada = $dbo->query($sql_talonera_comprada);

			while ($row_talonera_comprada = $dbo->fetchArray($qry_talonera_comprada)) :

				//nombre del servicio
				$IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_talonera_comprada["IDServicio"] . "'");
				$sql_servicio = "SELECT * FROM ServicioClub WHERE IDClub='" . SIMUser::get("club") . "'" . " AND IDServicioMaestro='" . $IDServicioMaestro . "'" . " AND Activo='S'";
				$servicio = $dbo->query($sql_servicio);
				$frm = $dbo->fetchArray($servicio);
				$TituloServicio = $frm["TituloServicio"];

				if (!empty($TituloServicio)) {
					$nombreServicio = $TituloServicio;
				} else {
					$nombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDServicioMaestro . "'");
				}
				$array_servicio[] = "'" .  $nombreServicio . "'";

				$array_total_valor[] = $row_talonera_comprada["ValorPagado"];
			// Consulto total de pqr
			/* $sql_total = "Select Count(IDPqr) as Total From Pqr Where IDClub = '" . SIMUser::get("club") . "' and IDArea = '" . $row_area["IDArea"] . "' " . $condiciones;
				$qry_total = $dbo->query($sql_total);
				$row_total = $dbo->fetchArray($qry_total); */
			//$array_total_areas[] = "'" . $row_total["Total"] . "'";
			endwhile;

			$valores = implode(",", $array_total_valor);

			?>

			labels: [<?php echo implode(",", $array_servicio); ?>],

			datasets: [
				<?php
				$array_datos[] = "{
						label: 'Valor Recargado',
						backgroundColor: randomColor(),
						data: [" . $valores . "]
					}";
				?>

				<?php echo implode(",", $array_datos); ?>

			]

		};




		var config = {
			type: 'pie',
			data: {
				datasets: [{
					<?php
					$sql_estado = "Select IDServicio,ValorPagado From ReservaGeneral Where IDClub='" .  SIMUser::get("club") . "' and IDTipoPago='16' " . $condiciones;
					$qry_estado = $dbo->query($sql_estado);
					while ($row_estado = $dbo->fetchArray($qry_estado)) :

						//nombre del servicio
						$IDServicioMaestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_estado["IDServicio"] . "'");
						$sql_servicio = "SELECT * FROM ServicioClub WHERE IDClub='" . SIMUser::get("club") . "'" . " AND IDServicioMaestro='" . $IDServicioMaestro . "'" . " AND Activo='S'";
						$servicio = $dbo->query($sql_servicio);
						$frm = $dbo->fetchArray($servicio);
						$TituloServicio = $frm["TituloServicio"];

						if (!empty($TituloServicio)) {
							$nombreServicio = $TituloServicio;
						} else {
							$nombreServicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDServicioMaestro . "'");
						}


						$array_estados[] = "'" . $nombreServicio . "'";
						// Consulto total de pqr X estado
						/* $sql_total = "Select Count(IDPqr) as Total From Pqr Where IDClub = '" . SIMUser::get("club") . "' and IDPqrEstado = '" . $row_estado["IDPqrEstado"] . "' " . $condiciones . " " . $condicion_area;
						$qry_total = $dbo->query($sql_total);
						$row_total = $dbo->fetchArray($qry_total); */
						$array_total_estados[] = "'" . $row_estado["ValorPagado"] . "'";
					endwhile;
					?>

					data: [<?php echo implode(",", $array_total_estados); ?>],
					backgroundColor: [
						randomColor(),
						randomColor(),
						randomColor()
					],
					hoverBackgroundColor: [
						randomColor(),
						randomColor(),
						randomColor()
					]
				}],
				labels: [
					<?php echo implode(",", $array_estados); ?>

				]
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: 'Consumo x Servicio',
					fontSize: 20
				}




			}
		};

		var barChartData2 = {
			<?php

			$sql_talonera_comprada2 = "Select SaldoMonedero,IDServicio From SocioTalonera Where IDClub = '" . SIMUser::get("club") . "' and Activo = '1' " . $condiciones;
			$qry_talonera_comprada2 = $dbo->query($sql_talonera_comprada2);

			while ($row_talonera_comprada2 = $dbo->fetchArray($qry_talonera_comprada2)) :

				//nombre del servicio
				$IDServicioMaestro2 = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $row_talonera_comprada2["IDServicio"] . "'");
				$sql_servicio2 = "SELECT * FROM ServicioClub WHERE IDClub='" . SIMUser::get("club") . "'" . " AND IDServicioMaestro='" . $IDServicioMaestro2 . "'" . " AND Activo='S'";
				$servicio2 = $dbo->query($sql_servicio2);
				$frm2 = $dbo->fetchArray($servicio2);
				$TituloServicio2 = $frm2["TituloServicio"];

				if (!empty($TituloServicio2)) {
					$nombreServicio2 = $TituloServicio2;
				} else {
					$nombreServicio2 = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDServicioMaestro2 . "'");
				}
				$array_servicio2[] = "'" .  $nombreServicio2 . "'";

				$array_total_valor2[] = $row_talonera_comprada2["SaldoMonedero"];
			// Consulto total de pqr
			/* $sql_total = "Select Count(IDPqr) as Total From Pqr Where IDClub = '" . SIMUser::get("club") . "' and IDArea = '" . $row_area["IDArea"] . "' " . $condiciones;
				$qry_total = $dbo->query($sql_total);
				$row_total = $dbo->fetchArray($qry_total); */
			//$array_total_areas[] = "'" . $row_total["Total"] . "'";
			endwhile;

			$valores = implode(",", $array_total_valor2);

			?>

			labels: [<?php echo implode(",", $array_servicio2); ?>],

			datasets: [
				<?php
				$array_datos2[] = "{
						label: 'Saldo Monedero',
						backgroundColor: randomColor(),
						data: [" . $valores . "]
					}";
				?>

				<?php echo implode(",", $array_datos2); ?>

			]

		};

		var barChartData3 = {
			<?php

			$sql_reserva_elemento = "Select COUNT(FechaTrCr) AS Conteo,CONCAT(MONTHNAME(FechaTrCr), ' ',YEAR(FechaTrCr) ) AS FECHA,IDServicio,IDServicioElemento From ReservaGeneral Where IDClub='" .  SIMUser::get("club") . "'" . $condicionesElementos . " GROUP BY MONTH(FechaTrCr), IDServicioElemento ";
			$qry_reserva_elemento = $dbo->query($sql_reserva_elemento);

			while ($row_reserva_elemento = $dbo->fetchArray($qry_reserva_elemento)) :



				$array_servicio3[] = "'" .  $row_reserva_elemento["FECHA"] . "'";
				$array_total_valor3[] = $row_reserva_elemento["Conteo"];

			endwhile;

			$valores = implode(",", $array_total_valor3);

			?>

			labels: [<?php echo implode(",", $array_servicio3); ?>],

			datasets: [
				<?php
				$array_datos3[] = "{
						label: 'Cantidad de reservas',
						backgroundColor: randomColor(),
						data: [" . $valores . "]
					}";
				?>

				<?php echo implode(",", $array_datos3); ?>

			]

		};







		window.onload = function() {


			var ctx = document.getElementById("canvas").getContext("2d");
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					// Elements options apply to all of the options unless overridden in a dataset
					// In this case, we are setting the border of each bar to be 2px wide and green
					elements: {
						rectangle: {
							borderWidth: 2,
							borderColor: 'rgb(0, 255, 0)',
							borderSkipped: 'bottom'
						}
					},
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Taloneras compradas x Servicio',
						fontSize: 20
					},


					animation: {
						duration: 500,
						easing: "easeOutQuart",
						onComplete: function() {
							var ctx = this.chart.ctx;
							ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
							ctx.textAlign = 'center';
							ctx.textBaseline = 'bottom';
							this.data.datasets.forEach(function(dataset) {
								for (var i = 0; i < dataset.data.length; i++) {
									var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
										scale_max = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._yScale.maxHeight;
									ctx.fillStyle = '#444';
									var y_pos = model.y - 5;
									// Make sure data value does not get overflown and hidden
									// when the bar's value is too close to max value of scale
									// Note: The y value is reverse, it counts from top down
									if ((scale_max - model.y) / scale_max >= 0.93) y_pos = model.y + 20;
									ctx.fillText(dataset.data[i], model.x, y_pos);
								}
							});
						}
					}




				}
			});

			var ctx2 = document.getElementById("canvas2").getContext("2d");
			window.myBar = new Chart(ctx2, {
				type: 'bar',
				data: barChartData2,
				options: {
					// Elements options apply to all of the options unless overridden in a dataset
					// In this case, we are setting the border of each bar to be 2px wide and green
					elements: {
						rectangle: {
							borderWidth: 2,
							borderColor: 'rgb(0, 255, 0)',
							borderSkipped: 'bottom'
						}
					},
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Saldo Monedero X Servicio',
						fontSize: 20
					},


					animation: {
						duration: 500,
						easing: "easeOutQuart",
						onComplete: function() {
							var ctx = this.chart.ctx;
							ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
							ctx.textAlign = 'center';
							ctx.textBaseline = 'bottom';
							this.data.datasets.forEach(function(dataset) {
								for (var i = 0; i < dataset.data.length; i++) {
									var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
										scale_max = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._yScale.maxHeight;
									ctx.fillStyle = '#444';
									var y_pos = model.y - 5;
									// Make sure data value does not get overflown and hidden
									// when the bar's value is too close to max value of scale
									// Note: The y value is reverse, it counts from top down
									if ((scale_max - model.y) / scale_max >= 0.93) y_pos = model.y + 20;
									ctx.fillText(dataset.data[i], model.x, y_pos);
								}
							});
						}
					}




				}
			});

			var ctx3 = document.getElementById("canvas3").getContext("2d");
			window.myBar = new Chart(ctx3, {
				type: 'bar',
				data: barChartData3,
				options: {
					// Elements options apply to all of the options unless overridden in a dataset
					// In this case, we are setting the border of each bar to be 2px wide and green
					elements: {
						rectangle: {
							borderWidth: 2,
							borderColor: 'rgb(0, 255, 0)',
							borderSkipped: 'bottom'
						}
					},
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Cantidad De Reservas X Elementos X Mes',
						fontSize: 20
					},


					animation: {
						duration: 500,
						easing: "easeOutQuart",
						onComplete: function() {
							var ctx = this.chart.ctx;
							ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
							ctx.textAlign = 'center';
							ctx.textBaseline = 'bottom';
							this.data.datasets.forEach(function(dataset) {
								for (var i = 0; i < dataset.data.length; i++) {
									var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
										scale_max = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._yScale.maxHeight;
									ctx.fillStyle = '#444';
									var y_pos = model.y - 5;
									// Make sure data value does not get overflown and hidden
									// when the bar's value is too close to max value of scale
									// Note: The y value is reverse, it counts from top down
									if ((scale_max - model.y) / scale_max >= 0.93) y_pos = model.y + 20;
									ctx.fillText(dataset.data[i], model.x, y_pos);
								}
							});
						}
					}




				}
			});

			var ctx_categoria = document.getElementById("chart-area").getContext("2d");

			window.myPie = new Chart(ctx_categoria, config);







		};
	</script>

	<!-- inline scripts related to this page -->