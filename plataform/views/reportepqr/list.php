	<?php

	if (!empty($_GET[FechaDesde]) && !empty($_GET[FechaHasta])) :
		$condiciones .= " and Fecha >= '" . $_GET["FechaDesde"] . " 00:00:00' and Fecha <= '" . $_GET["FechaHasta"] . " 23:59:59' ";
	endif;

	if (!empty($_GET[IDPqrEstado])) :
		$condiciones .= " and IDPqrEstado = '" . $_GET["IDPqrEstado"] . "'";
	endif;

	if (!empty($_GET[IDArea])) :
		$condiciones .= " and IDArea = '" . $_GET["IDArea"] . "'";
	endif;



	?>

	<div class="widget-box transparent" id="recent-box">
		<div class="widget-header">
			<h4 class="widget-title lighter smaller">
				<i class="ace-icon fa fa-users orange"></i>REPORTE PQR
			</h4>

			<table id="simple-table" class="table table-striped table-bordered table-hover">
				<tr>
					<td>Total Pqr</td>
					<td>
						<?php
						$sql_entregados = "Select * From Pqr Where IDClub =  '" . SIMUser::get("club") . "' " . $condiciones . " " . $condicion_area;
						$result_entregados = $dbo->query($sql_entregados);
						echo $dbo->rows($result_entregados);
						?>

					</td>
				</tr>
			</table>


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

			$sql_area = "Select IDArea,Nombre From Area Where IDClub = '" . SIMUser::get("club") . "' and Activo = 'S' " . $condicion_area;
			$qry_area = $dbo->query($sql_area);
			while ($row_area = $dbo->fetchArray($qry_area)) :
				$array_areas[] = "'" . $row_area["Nombre"] . "'";
				// Consulto total de pqr
				$sql_total = "Select Count(IDPqr) as Total From Pqr Where IDClub = '" . SIMUser::get("club") . "' and IDArea = '" . $row_area["IDArea"] . "' " . $condiciones;
				$qry_total = $dbo->query($sql_total);
				$row_total = $dbo->fetchArray($qry_total);
				$array_total_areas[] = "'" . $row_total["Total"] . "'";
			endwhile;

			$valores = implode(",", $array_total_areas);

			?>

			labels: [<?php echo implode(",", $array_areas); ?>],

			datasets: [
				<?php
				$array_datos[] = "{
						label: 'Total x Area',
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
					$sql_estado = "Select IDPqrEstado,Nombre From PqrEstado Where Publicar = 'S' ";
					$qry_estado = $dbo->query($sql_estado);
					while ($row_estado = $dbo->fetchArray($qry_estado)) :
						$array_estados[] = "'" . $row_estado["Nombre"] . "'";
						// Consulto total de pqr X estado
						$sql_total = "Select Count(IDPqr) as Total From Pqr Where IDClub = '" . SIMUser::get("club") . "' and IDPqrEstado = '" . $row_estado["IDPqrEstado"] . "' " . $condiciones . " " . $condicion_area;
						$qry_total = $dbo->query($sql_total);
						$row_total = $dbo->fetchArray($qry_total);
						$array_total_estados[] = "'" . $row_total["Total"] . "'";
					endwhile;
					?>

					data: [<?php echo implode(",", $array_total_estados); ?>],
					backgroundColor: [
						randomColor(),
						randomColor(),
						randomColor(),
						randomColor(),
						randomColor(),
						randomColor(),
						randomColor()
					],
					hoverBackgroundColor: [
						randomColor(),
						randomColor(),
						randomColor(),
						randomColor(),
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
					text: 'Pqr x Estado',
					fontSize: 20
				}




			}
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
						text: 'Pqr x Area',
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