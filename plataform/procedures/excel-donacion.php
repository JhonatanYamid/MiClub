<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );


	$sql_reporte = "SELECT *
					From Donacion
					Where IDClub = '".$_GET["IDClub"]."'
					Order By IDDonacion DESC" ;
	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "Donacion:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>Accion</th>";
	  $html .= "<th>Nombre</th>";
		$html .= "<th>Valor</th>";
		$html .= "<th>Valor Letras</th>";
		$html .= "<th>Medio</th>";
		$html .= "<th>Autorizacion Mensual</th>";
		$html .= "<th>Fecha Inicio</th>";
		$html .= "<th>Fecha Fin</th>";
		$html .= "<th>Observaciones</th>";
		$html .= "<th>Fecha</th>";
		$html .= "</tr>";

		$style='mso-number-format:"@";';
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . $Datos["Accion"] ."</td>";
			$html .= "<td>" . $Datos["Nombre"] ."</td>";
			$html .= "<td>" . $Datos["Valor"] ."</td>";
			$html .= "<td>" . $Datos["valorletras"] ."</td>";
			$html .= "<td>" . $Datos["MedioDonacion"] ."</td>";
			$html .= "<td>" . $Datos["AutorizacionMensual"] ."</td>";
			$html .= "<td>" . $Datos["FechaInicio"] ."</td>";
			$html .= "<td>" . $Datos["FechaFin"] ."</td>";
			$html .= "<td>" . $Datos["Observaciones"] ."</td>";
			$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
			$html .= "</tr>";
			}
		}
		$html .= "</table>";

		//construimos el excel
		header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
		header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		?>
		<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<?php
	echo $html;
	?>
</body>
</html>
<?php
		exit();

?>
