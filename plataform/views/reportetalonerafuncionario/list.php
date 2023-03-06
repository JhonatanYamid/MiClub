	<?php

	if (isset($_GET['IDSocio']) && $_GET['IDSocio'] && !empty($_GET['IDSocio'])) :
		$condiciones .= " and NumeroDocumento = '" . $_GET["IDSocio"] . "'";
	endif;

	if (!empty($_GET['FechaDesde'] && !empty($_GET['FechaDesde']))) :
		$condiciones .= " and FechaConsumo >= '" . $_GET["FechaDesde"] . "' and FechaConsumo <= '" . $_GET["FechaHasta"] . "' ";
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
						$sql_entregados = "Select * From LogTiqueteraFuncionarios Where IDClub =  '" . SIMUser::get("club") . "' " . $condiciones . " " . $condicion_area;
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
							<canvas id="canvas2"></canvas>
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



		var barChartData2 = {
			<?php

			$sql_talonera_comprada2 = "Select COUNT(TipoConsumo) as Cantidades, TipoConsumo From LogTiqueteraFuncionarios  Where IDClub = '" . SIMUser::get("club") . "' " . $condiciones . " GROUP BY TipoConsumo ORDER BY TipoConsumo ASC";
			$qry_talonera_comprada2 = $dbo->query($sql_talonera_comprada2);
			$qry_talonera_comprada2 = $dbo->fetch($qry_talonera_comprada2);
			$cantidades = array(0);
			$tipoConsumo = array("'Otro'");
			foreach ($qry_talonera_comprada2 as $elm) {
				if (isset($elm['Cantidades']) && $elm['Cantidades'] && isset($elm['TipoConsumo']) && $elm['TipoConsumo']) {
					$cantidades[] = $elm['Cantidades'];
					$tipoConsumo[] = "'".$elm['TipoConsumo']."'";
				}
			}

			$valores = implode(",", $cantidades);

			?>

			labels: [<?php echo implode(",", $tipoConsumo); ?>],

			datasets: [
				<?php
				$array_datos2[] = "{
						label: 'Cantidad Consumos por Tipo Alimento',
						backgroundColor: randomColor(),
						data: [" . $valores . "]
					}";
				?>

				<?php echo implode(",", $array_datos2); ?>

			]

		};

		window.onload = function() {




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
						text: 'Cantidad Consumos por Tipo Alimento',
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