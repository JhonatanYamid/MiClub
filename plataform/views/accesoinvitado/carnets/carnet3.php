<?php
//var_dump($datos_invitado);
//var_dump($datos_socio);
?>
<div class="carnet">
	<style>
		.carnet {		
										
		}		
		.carnet .container {
			border-style: solid;		
			border-radius: 15px;
			width: 500px;
			height: 312px;
			font-size: 16px;
			margin: 0;
		}
		.carnet .container .header{
			display: flex;
			flex-direction: row;
			justify-content: space-around;	
			border-radius: 15px 15px 0 0;
			height: 80px;
			width: 100%;
			background-color: green;		
		}

		.carnet .container .title{
			display: flex;
			flex-direction: column;
			justify-content: center;			
		}

		.carnet .container .title h2 {
			color: white;
		}

		.carnet .container .logo{
			display: flex;
			flex-direction: column;
			justify-content: center;			
		}
		.carnet .container .body {
			display: flex;
			flex-direction: column;
			justify-content: space-around;
			border-radius: 0 0 15px 15px;
			width: 100%;
			height: 232px;
			background-color: bisque;
		}		
		.carnet .container .body .row{
			display: flex;
			flex-direction: row;
			justify-content: space-around;
		}		
		.carnet .container .body .row .input{
			
		}
		.carnet .container .body .row .input .label{
			font-size: 10px;
			line-height: 1.0;
			padding: 0px;
		}		
		.carnet .container .body .row .input .value{
			font-size: 20px;
			font-weight: bold;
			line-height: 1.0;
			padding: 0px;			
		}
	</style>
	<div class="container">
		<div class="header">
			<div class="title">
				<h2>CARNÉ INVITADO</h2>
			</div>
			<div class="logo">
				<img src="assets/img/logo-interno.png">	
			</div>
		</div>
		<div class="body">
			<div class="row">
				<div class="input">
					<div class="label">ACCIÓN</div>
					<div class="value">
						<?php echo $datos_socio["Accion"]?>
					</div>
				</div>
				<div class="input">
					<div class="label">SOCIO</div>
					<div class="value">
						<?php echo $datos_socio["Nombre"] . " " .  $datos_socio["Apellido"]?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="input">
					<div class="label">NOMBRE INVITADO</div>
					<div class="value">
						<?php echo $datos_invitado["Nombre"] . " " . $datos_invitado['Apellido']?>
					</div>
				</div>
				<div class="input">
					<div class="label">DOCUMENTO INVITADO</div>
					<div class="value">
						<?php echo $datos_invitado["NumeroDocumento"]?>
					</div>
				</div>
				<div class="input">
					<div class="label">TIPO</div>
					<div class="value">
						<?php echo $datos_invitacion["TipoInvitacion"]?>
					</div>
				</div>
			</div>
			<div class="row">				
				<div class="input">
					<div class="label">AUTORIZADO DESDE</div>
					<div class="value">
						<?php echo $datos_invitacion["FechaInicio"]?>
					</div>
				</div>
				<div class="input">
					<div class="label">AUTORIZADO HASTA</div>
					<div class="value">
						<?php echo $datos_invitacion["FechaFin"]?>
					</div>
				</div>
			</div>
			<div class="row">				
				<div class="input">
					<div class="label">FECHA GENERACIÓN</div>
					<div class="value">
						<?php echo date("Y-m-d H:i")?>
					</div>
				</div>
				<div class="input">
					<div class="label">INGRESADO POR</div>
					<div class="value">
						<?php echo $usuario["Nombre"]?>
					</div>
				</div>
			</div>		
		</div>
	</div>
	
</div>