<?
	include( "../admin/config.inc.php" );
	include("cmp/seo.php");

?>


	</head>

	<body class="no-skin">

		Consultar fecha
		<input type="date" name="FechaPolo" id="FechaPolo">
		<input type="button" name="ConsultaFechaPoloPino" id="ConsultaFechaPoloPino" value="Consultar">

		<div class="main-container" id="main-container">


			<div class="main-content">
				<div class="main-content-inner">


					<div class="page-content">

						<div class="row">



<?php

$fecha=$_GET["Fecha"];

if($fecha==date("Y-m-d")){
	//$condicion_hora=" and Hora >=  '".date("H:i:s")."' ";
}

$sql_reserva="select RG.*, S.Handicap, S.Nombre, S.Apellido
				   From ReservaGeneral RG, Socio S
				   Where RG.IDSocio=S.IDSocio
				   and RG.IDClub='143' and Fecha='".$fecha."' ".$condicion_hora." and IDServicio = '28122'
				   Order by Hora, Cancha, FIELD (Equipo,'blanco','rojo','blancorojo'), Handicap DESC";
$r_reserva=$dbo->query($sql_reserva);
while($row_reserva=$dbo->fetchArray($r_reserva)):
	$array_canchas[$row_reserva["Hora"]][$row_reserva["Cancha"]]=$row_reserva["Cancha"];
	$array_cancha_socio[$row_reserva["Hora"]][$row_reserva["Cancha"]][$row_reserva["Equipo"]][]=$row_reserva["IDSocio"]."_".$row_reserva["Nombre"]. " " .$row_reserva["Apellido"]."_".$row_reserva["Handicap"]."_".$row_reserva["QuintoJugador"];
	$array_horas[$row_reserva["Hora"]]=$row_reserva["Hora"];
endwhile;

foreach($array_horas as $hora):?>

<table id="simple-table" class="table table-striped table-bordered table-hover">
<tr>
	<td colspan="3" style="font-size: 20px; color: brown">PR&Aacute;CTICA: <?php echo strtoupper( SIMUtil::tiempo( $fecha ) ) . " " . $hora; ?></td>
</tr>

<?php foreach($array_canchas[$hora] as $cancha):?>

<tr>
  <td colspan="3" valign="top">
	<table id="simple-table" class="table table-striped table-bordered table-hover">
					<tr>
  <td colspan="3" valign="top" align="center" style="color: crimson; font-size: 14px; font-weight: bold">CANCHA <?php echo $cancha; ?> </td>
  </tr>
<tr>

	<?php  foreach($array_cancha_socio[$hora][$cancha] as $keyequipo => $datos_equipo):
		$suma_handicap="0";
		$contador_fila=0;
	?>
	<td valign="top">
		<table id="simple-table" class="table table-striped table-bordered table-hover">
    <tbody>
      <tr>
        <td bgcolor="#F2E3E3" colspan="2" align="center" style="font-weight: bold">Equipo <?php if($keyequipo=="blancorojo") echo "Azul"; else echo $keyequipo; if($keyequipo=="blanco") echo " A"; elseif($keyequipo=="rojo") echo " B"; elseif($keyequipo=="blancorojo") echo " C (Ladies) Orden A-B, A-C,B-C";  ?></td>
        </tr>
      	<?php  foreach($datos_equipo as $datos_socio): ?>
		<tr>
        <td><?php
				$contador_fila++;
				$array_datos_socio = explode("_",$datos_socio);
			echo $array_datos_socio[1]; //nombre
			if($array_datos_socio["3"]=="S"): //Quinto Jugador
				echo "<span style='color: brown'> (5to jug.) </span>";
			else:
				$suma_handicap+=$array_datos_socio[2];
			endif;
			?></td>
        <td><?php

			echo $array_datos_socio[2]; //Handicap ?></td>
        </tr>
		<?php endforeach;
			if($contador_fila<5):
				for($i=$contador_fila;$i<5;$i++):
					echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
				endfor;
			endif;

		?>
      <tr >
        <td bgcolor="#ECBEBF"><b>Total Handicap</b></td>
        <td bgcolor="#ECBEBF"><b><?php echo $suma_handicap; ?></b></td>
        </tr>
      </tbody>
    </table></td>
	<?php endforeach; ?>

</tr>
			</table>


	</td>
</tr>
<?php endforeach; ?>

</table>
<?php endforeach; ?>

<?php if($_GET["OptImprimir"]==1){ ?>
<button id="btnequipopolo" class="btn btn-info" href="" onclick="window.print();">
	<i class="ace-icon fa fa-print align-top bigger-125"></i>
	Imprimir
</button>
<?php } ?>







								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

			<?
				include("cmp/footer.php");
			?>
		</div><!-- /.main-container -->

		<?
			//include( "cmp/footer_grid.php" );
		?>

<?
			include( "cmp/footer_grid.php" );
		?>

	</body>
</html>
