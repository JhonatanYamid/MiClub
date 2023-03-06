<?
	require( "admin/config.inc.php" );
	include( "cmp/seo.php" );
	$datos_club = $dbo->fetchAll( "Club", " IDClub = '" . $_GET["IDClub"] . "' ", "array" );
	$datos_puntos = $dbo->fetchAll( "SocioPuntosArrayanes", " IDSocio = '" . $_GET["IDSocio"] . "' ", "array" );
?>
</head>
<body>

				<table border='0' cellpadding='0' cellspacing='0' width='90%' align='center'>
					<tr>
						<td>
							<img src='<?php echo CLUB_ROOT.$datos_club[FotoLogoApp] ?>'>
						</td>
					</tr>
					<tr>
						<td>
							<br>
							<span style="color: #05621C">ESTADO DE CUENTA</span> <br><br>
Estimado (a) <?php echo $datos_puntos["Apellido"] .  " " . $datos_puntos["Nombre"]; ?> ,<br><br>

Membresía <?php echo $datos_puntos["Accion"]; ?>,<br><br>
<div style="text-align:justify">
Le agradecemos por su participación en nuestra Campaña de Fidelización y Programa de Recompensas, a continuación detallamos el resumen de puntos generados por su membresía en el presente período:<br><br>
</div>
Fecha Desde: <?php echo $datos_puntos["FechaDesde"]; ?><br>
Fecha hasta: <?php echo $datos_puntos["FechaHasta"]; ?><br><br>

<table style="border:1px solid">
		<tr>
			<td style="background-color: #05621C; color:#FFFFFF;"><strong>Descripción	Puntos</strong></td>
			<td style="background-color: #05621C; color:#FFFFFF;"><strong>Puntos</strong></td>
		</tr>
		<tr>
			<td>Inscripción Campaña</td>
			<td align="right"><?php echo $datos_puntos["InscripcionCampana"]; ?></td>
		</tr>
		<tr>
			<td>Visitas</td>
			<td align="right"><?php echo $datos_puntos["Visita"]; ?></td>
		</tr>
		<tr>
			<td>Consumos restaurantes y delicatessen</td>
			<td align="right"><?php echo $datos_puntos["RestauranteDelicatessen"]; ?></td>
		</tr>
		<tr>
			<td>Escuelas y talleres deportivos</td>
			<td align="right"><?php echo $datos_puntos["EscuelaTaller"]; ?></td>
		</tr>
		<tr>
			<td>Inscripción en torneos deportivos</td>
			<td align="right"><?php echo $datos_puntos["InscripcionTorneo"]; ?></td>
		</tr>
		<tr>
			<td>Eventos sociales y corporativos</td>
			<td align="right"><?php echo $datos_puntos["EventoSocial"]; ?></td>
		</tr>
		<tr>
			<td><strong>TOTAL PUNTOS</strong></td>
			<td align="right"><?php echo $datos_puntos["TotalPuntos"]; ?></td>
		</tr>
</table>


	<br><br>
Su ubicación actual en los grupos es:<br><br>
<img src="<?php echo URLROOT.'file/imgtmparrayanes/'.$datos_puntos["Imagen"];  ?>" width="300px" height="113px" ><br><br>
Lo animamos a seguir participando de manera activa!<br><br>

<div style="text-align:justify">
Los socios que obtengan los más altos puntajes, accederán a nuestros premios y recompensas, en reconocimiento a su fidelidad.<br><br>
</div>
Atentamente,<br><br>

LA ADMINISTRACIÓN

						</td>
					</tr>
				</table>
</body>
</html>
