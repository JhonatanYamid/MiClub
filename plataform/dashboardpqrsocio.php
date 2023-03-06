<?php
include("procedures/general.php");
include("procedures/dashboardpqrsocio.php");
include("cmp/seo.php");
?>

<?php
header("Refresh: 1200; URL='https://miclubapp.com/plataform/dashboardpqrsocio.php'");
?>

<body>
	<div class="col-lg-12" style="padding-top: 20px;">

		<div class="card-body">
			<div class="row">
				<div class="col-lg-6">
					<label for="">Fecha Inicio</label>
					<input type="date" name="fechainiciolineal" id="fechainiciolineal">

					<label for="">Fecha Fin</label>
					<input type="date" name="fechafinlineal" id="fechafinlineal">
					<button class="btn btn-info" onclick=" CargarDatoslineal()">Buscar</button>

					<canvas id="myChart3" width="400" height="300"></canvas>
				</div>
				<div class="col-lg-6">
					<label for="">Fecha Inicio</label>
					<input type="date" name="fechainiciopqrestado" id="fechainiciopqrestado">

					<label for="">Fecha Fin</label>
					<input type="date" name="fechafinpqrestado" id="fechafinpqrestado">
					<button class="btn btn-info" onclick="CargarDatosGraficoBarEstadoPqr()">Buscar</button>

					<canvas id="myChart" width="400" height="300"></canvas>
				</div>


			</div>
			<div class="row">
				<div class="col-lg-6">
					<label for="">Fecha Inicio</label>
					<input type="date" name="fechainiciopqrmes" id="fechainiciopqrmes">

					<label for="">Fecha Fin</label>
					<input type="date" name="fechafinpqrmes" id="fechafinpqrmes">
					<button class="btn btn-info" onclick="CargarDatosGraficoBarPqrMes()">Buscar</button>
					<canvas id="myChart2" width="400" height="300"></canvas>
				</div>
				<div class="col-lg-6">
					<label for="">Fecha Inicio</label>
					<input type="date" name="fechainiciopqrarea" id="fechainiciopqrarea">

					<label for="">Fecha Fin</label>
					<input type="date" name="fechafinpqrarea" id="fechafinpqrarea">
					<button class="btn btn-info" onclick="CargarDatosGraficoBarAreasPqr()">Buscar</button>
					<canvas id="myChart1" width="400" height="300"></canvas>
				</div>
			</div>

		</div>

	</div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.1/chart.js"></script>
<script>
	CargarDatosGraficoBarEstadoPqr();
	CargarDatosGraficoBarAreasPqr();
	CargarDatosGraficoBarPqrMes();
	CargarDatoslineal();
	let myChart, myChart2, myChart3, myChart4;

	function CargarDatoslineal() {
		var fechainiciolineal = document.getElementById('fechainiciolineal').value;
		var fechafinlineal = document.getElementById('fechafinlineal').value;
		/* alert("Hola" + " " + fechainiciolineal); */
		$.ajax({
			url: 'includes/async/dashboardpqrsocio.async.php',
			type: 'POST',
			DataType: 'json',
			data: ({
				'paramlineal': 'graficolineal',
				iniciolineal: fechainiciolineal,
				finallineal: fechafinlineal,

			}),


		}).done(function(resp) {

			var cantidadLineal = [];

			var data = JSON.parse(resp);
			for (var i = 0; i < data.length; i++) {

				cantidadLineal.push(data[i][1]);
			}
			const ctx = document.getElementById('myChart3');
			//const
			if (myChart) {
				myChart.destroy();
			}
			myChart = new Chart(ctx, {
				type: 'line',
				data: {
					labels: ['1', '2', '3', '4'],
					datasets: [{
						label: 'Demoras de PQR por semanas',
						data: cantidadLineal,
						backgroundColor: [
							'green'

						],
						borderColor: [
							'green'

						],
						borderWidth: 1
					}]
				},
				options: {
					scales: {
						x: {
							beginAtZero: true
						}
					}
				}
			});

		}).fail(function(error) {

			console.log('error');

		})
	}

	function CargarDatosGraficoBarEstadoPqr() {
		var fechainiciopqrestado = document.getElementById('fechainiciopqrestado').value;
		var fechafinestadopqr = document.getElementById('fechafinpqrestado').value;
		$.ajax({
			url: 'includes/async/dashboardpqrsocio.async.php',
			type: 'POST',
			DataType: 'json',
			data: ({
				'param': 'barras',
				iniciobarraestado: fechainiciopqrestado,
				finalbarrasestado: fechafinestadopqr,
			}),


		}).done(function(resp) {
			var titulo = [];
			var cantidad = [];

			var data = JSON.parse(resp);
			for (var i = 0; i < data.length; i++) {
				titulo.push(data[i][0]);
				cantidad.push(data[i][1]);
			}
			const ctx = document.getElementById('myChart');
			if (myChart2) {
				myChart2.destroy();
			}
			myChart2 = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: titulo,
					datasets: [{
						label: 'Estado de PQR',
						data: cantidad,
						backgroundColor: [
							'green'

						],
						borderColor: [
							'green'

						],
						borderWidth: 1
					}]
				},
				options: {
					scales: {
						y: {
							beginAtZero: true
						}
					}
				}
			});

		}).fail(function(error) {

			console.log('error');

		})
	}

	function CargarDatosGraficoBarPqrMes() {
		var fechainiciopqrmes = document.getElementById('fechainiciopqrmes').value;
		var fechafinmespqr = document.getElementById('fechafinpqrmes').value;
		$.ajax({
			url: 'includes/async/dashboardpqrsocio.async.php',
			type: 'POST',
			DataType: 'json',
			data: ({
				'paramMes': 'barrasmes',
				iniciopqrmes: fechainiciopqrmes,
				finpqrmes: fechainiciopqrmes,

			}),


		}).done(function(resp) {
			var titulopqrmes = [];
			var cantidadpqrmes = [];

			var data = JSON.parse(resp);
			for (var i = 0; i < data.length; i++) {
				cantidadpqrmes.push(data[i][0]);
				titulopqrmes.push(data[i][1]);

			}
			const ctx = document.getElementById('myChart2');
			if (myChart3) {
				myChart3.destroy();
			}
			myChart3 = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: titulopqrmes,
					datasets: [{
						label: 'Cantidad PQR por mes',
						data: cantidadpqrmes,
						backgroundColor: [
							'rgba(159,51,255,1)'

						],
						borderColor: [
							'rgba(159,51,255,1)'

						],
						borderWidth: 1
					}]
				},
				options: {
					scales: {
						y: {
							beginAtZero: true
						}
					}
				}
			});

		}).fail(function(error) {

			console.log('error');

		})
	}

	function CargarDatosGraficoBarAreasPqr() {
		var fechainiciopqrareas = document.getElementById('fechainiciopqrarea').value;
		var fechafinpqrareas = document.getElementById('fechafinpqrarea').value;
		$.ajax({
			url: 'includes/async/dashboardpqrsocio.async.php',
			type: 'POST',
			DataType: 'json',
			data: ({
				'paramAreas': 'barrasareas',
				fechainicialareas: fechainiciopqrareas,
				fechafinpqrareas: fechafinpqrareas,

			}),


		}).done(function(resp) {
			var tituloarea = [];
			var cantidadareas = [];

			var data = JSON.parse(resp);
			for (var i = 0; i < data.length; i++) {
				tituloarea.push(data[i][0]);
				cantidadareas.push(data[i][1]);
			}
			const ctx = document.getElementById('myChart1');
			if (myChart4) {
				myChart4.destroy();
			}
			myChart4 = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: tituloarea,
					datasets: [{
						label: 'Areas que mas reportan PQR',
						data: cantidadareas,
						backgroundColor: [
							'rgba(159,51,255,1)'

						],
						borderColor: [
							'rgba(159,51,255,1)'

						],
						borderWidth: 1
					}]
				},
				options: {
					scales: {
						y: {
							beginAtZero: true
						}
					}
				}
			});

		}).fail(function(error) {

			console.log('error');

		})
	}
</script>