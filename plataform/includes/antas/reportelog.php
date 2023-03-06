<?php

require( "config.inc.php" );
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once(LIBDIR.'nusoap/lib/nusoap.php');
//require_once('inc_jwt_helper.php');



//Reporte Log Separacion reserva
//$Servicio='"IDServicio":"1043"';
$Servicio='"IDServicio":"99"';
$Fecha='"Fecha":"2020-12-05"';
$IDClub=7;
$sql_socios="SELECT IDSocio,NumeroDocumento,Accion,Nombre,Apellido FROM Socio WHERE IDClub = '".$IDClub."'";
$r_socios=$dbo->query($sql_socios);
while($row_socios=$dbo->fetchArray($r_socios)){
	$array_socios[$row_socios["IDSocio"]]=$row_socios;
}

//$sql_separa_reserva="SELECT * FROM `LogServicioQuincenal` WHERE (`Servicio` = 'set_reserva_generalV2' or `Servicio` = 'setseparareserva' or `Servicio` = 'getdisponiblidadelementoservicio') and (Parametros like '%".$Fecha."%'  and Parametros like '%".$Servicio."%' ) ORDER BY LogServicioDiario";
$sql_separa_reserva="SELECT * FROM `LogServicioQuincenal` WHERE IDSocio in (2230,4039)  ORDER BY LogServicioDiario";

$r_separa_servicio=$dbo->query($sql_separa_reserva);
?>
<table border=1>
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
				while($row_separa_servicio=$dbo->fetchArray($r_separa_servicio)){
					$Parametros=$row_separa_servicio["Parametros"];
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"null"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"null"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67", "Nombre":"Necesita Caddie?", "Valor":"Si"},{"IDCampo":"218", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"2"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"Si"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"1"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67", "Nombre":"Necesita Caddie?", "Valor":"Si"},{"IDCampo":"218", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"1"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"Si"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"2"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67", "Nombre":"Necesita Caddie?", "Valor":"No"},{"IDCampo":"218", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"0"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"Optional("67")", "Nombre":"Necesita Caddie?", "Valor":"No"},{"IDCampo":"Optional("218")", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"0"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"No"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"0"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"Si"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"3"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"Si"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"0"}]",','',$Parametros);
					$Parametros=str_replace('"Campos":"[{"IDCampo":"67", "Nombre":"Necesita Caddie?", "Valor":"Si"},{"IDCampo":"218", "Nombre":"u00bfCuu00e1ntos caddies necesita?", "Valor":"3"}]",','',$Parametros);

					$Parametros=str_replace("null",'',$Parametros);
					$Parametros= trim(preg_replace('/\s+/', '', $Parametros));
					$Parametros= str_replace("\"[{","{",$Parametros);
					$Parametros= str_replace("}]\"","}",$Parametros);

						//$Parametros='{"action":"setseparareserva","IDSocio":"50771","IDServicio":"289","IDElemento":"349","Fecha":"2020-10-31","Hora":"08:10:00","Campos":"[{"IDCampo":"67","Nombre":"Necesita Caddie?","Valor":"null"}, {"IDCampo":"218","Nombre":"u00bfCuu00e1ntos caddies necesita?","Valor":"null"}]","Invitados":"[]","Tee":"Tee1","NumeroTurnos":"1","IDClub":"15","AppVersion":"31","Dispositivo":"Android","TipoApp":"Socio","TokenID":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJhdWQiOiJodHRwczpcL1wvd3d3Lm1pY2x1YmFwcC5jb20iLCJpYXQiOjE2MDQwNDE1MDUsIm5iZiI6MTYwNDA0MTUwNSwiZXhwIjoxNjA0MTAxNTA1LCJkYXRhIjp7IklEVXN1YXJpb1dTIjoiMSIsIk5vbWJyZSI6IlJ1YmksIEpob24iLCJFbXByZXNhIjoiRGluZ28ifX0.fgRAK-u5ePu9rn_hL-GG7UI72LcIDhAx3Wl-9pEqaUs"}';

					$array_parametros=json_decode($Parametros,true);
					$Respuesta= trim(preg_replace('/\s+/', ' ', $Respuesta));
					$Respuesta=$row_separa_servicio["Respuesta"];
					$array_respuesta=json_decode($Respuesta,true);
					?>
					<tr>
							<td>
								<?php
								if($row_separa_servicio["Servicio"]=="set_reserva_generalV2"){
									$servicio="Pulsar en confirmar reserva";
								}
								elseif($row_separa_servicio["Servicio"]=="setseparareserva"){
									$servicio="Intento reserva";
								}
								elseif($row_separa_servicio["Servicio"]=="getdisponiblidadelementoservicio"){
									$servicio="Ver fechas disponibles";
								}
								else{
									$servicio=$row_separa_servicio["Servicio"];
								}

								echo $servicio;?>
							</td>
							<td>
								<?php

								//if($array_respuesta["message"]=="Esta fecha au00fan no estu00e1 disponible")
								$hora_pet=substr($row_separa_servicio["FechaPeticion"],11,8);

								if($array_respuesta["message"]=="Guardado"){
									$respuestaapp="exitoso";
								}
								else{
									$respuestaapp=$array_respuesta["message"];
								}

								if($hora_pet>= "10:00:00" and $IDClub=="7" && $array_respuesta["message"] == "Esta fecha au00fan no estu00e1 disponible"){
									$respuestaapp="Lo sentimos la reserva ya fue o esta siendo tomada";
								}
								echo $respuestaapp;
								?>

							</td>
							<td><?php

							$NombreSer=$dbo->getFields( "Servicio", "Nombre", "IDServicio = '" . $row_separa_servicio["IDServicio"] . "'" );
							if(empty($NombreSer)){
								$IDMaestro = $dbo->getFields( "Servicio", "IDServicioMaestro", "IDServicio = '" . $row_separa_servicio["IDServicio"] . "'" );
								$NombreSer=$dbo->getFields( "ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $IDMaestro . "'" );
							}
							echo $NombreSer;
							?>

						</td>
							<td></td>
							<td><?php echo $array_parametros["Tee"]; ?></td>
							<td><?php
							echo $array_socios[$array_parametros["IDSocio"]]["Nombre"] . " " . $array_socios[$array_parametros["IDSocio"]]["Apellido"]; ?></td>
							<td><?php echo $array_parametros["Fecha"]; ?></td>
							<td><?php echo $array_parametros["Hora"]; ?></td>
							<td><?php echo $array_parametros["Dispositivo"]; ?></td>

							<td><?php echo $row_separa_servicio["FechaPeticion"]; ?></td>
					</tr>
				<?php }	?>

</table>
<?php
exit;
?>
