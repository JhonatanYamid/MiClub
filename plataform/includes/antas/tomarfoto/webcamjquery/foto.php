<!DOCTYPE html>
<html lang="es">

<head>
	<!--
		Tomar una fotografía y guardarla en un archivo v3
	    @date 2018-10-22
	    @author parzibyte
	    @web parzibyte.me/blog
	-->
	<meta charset="UTF-8">

	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Tomar foto </title>
	<style>
        	#video,#canvas {
		background-color: #E7FEE9;
	

     
	}
	#titulo_camara {
	background-color: #666;
    height: 30px;
	color:#FFF;
	padding-left: 30px;
	font-size: 24px;
	text-align:center;
	}

    #titulo_select {
    height: 30px;
    font-family: "Comic Sans MS", cursive;
	padding-left: 30px;
	font-size: 24px;
	
	}
	#boton,#guardar,#nuevo,#listaDeDispositivos{
		background-color:#FFF;
		color:#333;
		font-family: "Comic Sans MS", cursive;
		font-size:14px;
		margin-top:10px;
		width:100px;
		height:40px;
	}

		@media only screen and (max-width: 700px) {
			video {
				max-width: 100%;
			}
		}
	</style>
</head>

<body>

	<h1 id="titulo_camara">Tomar foto de acceso</h1>
	
	<h1 id="titulo_select">Selecciona un dispositivo</h1>
	
    <div align="left" id="cuadro_camara">    
		<select name="listaDeDispositivos" id="listaDeDispositivos"></select>
		<button id="boton">Tomar foto</button>
        <button id="guardar">Guardar foto</button>
        <button id="nuevo">Tomar nuevo</button>
		<p id="estado"></p>
	</div>
   
	<br>

	<video muted="muted" id="video" width="263"></video>
	<canvas id="canvas" width="20%" height="10" style="display: none;"></canvas>

	<div id="carga"   style="display: none;"> Cargando  espere...</div>
	<!-- <progress id="img-upload-bar"  min="0"  value="0"  max="100" step="1" style="width: 100%"></progress> -->

    <!-- camara -->



  
</body>

<script src="script_foto.js" IDRegistro="<?php echo $_GET["IDRegistro"]; ?>" Modulo= "<?php echo $_GET["Modulo"]; ?>"></script>

</html>