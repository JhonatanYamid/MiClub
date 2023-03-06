<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Foto</title>

<style type="text/css">
	/* jQuery lightBox plugin - Gallery style */
	#cuadro_camara {
		background-color: #E7FEE9;
		padding-left: 30px;
		padding-top:20px;
	}
	#titulo_camara {
	background-color: #666;
	color:#FFF;
	padding-left: 30px;
	font-size: 14px;
	text-align:center;
	}
	.botones_cam {
		background-color:#FFF;
		color:#333;
		font-family: "Comic Sans MS", cursive;
		font-size:14px;
		margin-top:10px;
		width:100px;
		height:40px;
	}
	.formulario {
		color: #000;
	}
	
	</style>
    
	<script type="text/javascript" src="webcam.js"></script>
    <script language="JavaScript">
		webcam.set_api_url( 'test.php?IDRegistro=<?php echo $_GET["IDRegistro"]; ?>&Modulo=<?php echo $_GET["Modulo"]; ?>' );//PHP adonde va a recibir la imagen y la va a guardar en el servidor
		webcam.set_quality( 90 ); // calidad de la imagen
		webcam.set_shutter_sound( true ); // Sonido de flash
	</script>
		<script language="JavaScript">
		webcam.set_hook( 'onComplete', 'my_completion_handler' );
		
		function do_upload() {
			// subir al servidor
			document.getElementById('upload_results').innerHTML = '<h1>Cargando al servidor...</h1>';
			
			
			webcam.upload();
		}
		
		function my_completion_handler(msg) {			
			if (msg.match(/(http\:\/\/\S+)/)) {
				var image_url = RegExp.$1;//respuesta de text.php que contiene la direccion url de la imagen									
				// reset camera for another shot
				//webcam.reset();
				document.getElementById('upload_results').innerHTML = '<h1>Foto cargada correctamente</h1>';	
			}
			else alert("PHP Error: " + msg);
		}
	</script>
    
    
</head>

<body>


<div align="left" id="cuadro_camara">    

<table width="80%" height="144" align="center"><tr><td width="100" valign=top>
		<form method="post" name="frmfoto" id="frmfoto" action="">
		<input type=button value="Tomar foto" onClick="webcam.freeze()" class="botones_cam">
		<br>
		<input type=button value="Guardar" onClick="do_upload()" class="botones_cam">
		<br>
		<input type=button value="Tomar de nuevo" onClick="webcam.reset()" class="botones_cam">
		</form>
	
	</td>
    <td width="263" valign=top>
      <script language="JavaScript">
	document.write( webcam.get_html(400, 300) );//dimensiones de la camara
	</script>
    </td>
    </tr>
  <tr>
    <td colspan="2" valign=top><div id="upload_results" class="formulario" > </div></td>
    </tr>
</table>
<br /><br />
</div>
</body>
</html>
