<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$texto = str_replace("ñ", "&ntilde;" ,$texto);
		$texto = str_replace("á", "&aacute;" ,$texto);
		$texto = str_replace("é", "&eacute;" ,$texto);
		$texto = str_replace("í", "&iacute;" ,$texto);
		$texto = str_replace("ó", "&oacute;" ,$texto);
		$texto = str_replace("ú", "&uacute;" ,$texto);
		return $texto;
	}


	if(!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])){
		$condicion_fecha=" and FechaInicio >= '".$_POST["FechaInicio"]."'  and FechaFin <= '".$_POST["FechaFin"]."'";
	}



	$sql_reporte = "Select *
					From ReservaHotelEliminada
					Where IDClub = '".$_POST["IDClub"]."'  ".$condicion_area." ".$condicion_fecha." Order By IDReserva DESC" ;

	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "Hotel_Corte:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
    $html .= "<th>NUMERO RESERVA</th>";
		$html .= "<th>SOCIO</th>";
		$html .= "<th>ACCION</th>";
	  $html .= "<th>HABITACION</th>";
		$html .= "<th>TEMPORADA</th>";
		$html .= "<th>CABEZA RESERVA</th>";
		$html .= "<th>DOCUMENTO DUENO RESERVA</th>";
		$html .= "<th>NOMBRE DUEÑO RESERVA</th>";
		$html .= "<th>EMAIL DUEÑO RESERVA</th>";
		$html .= "<th>ESTADO</th>";
		$html .= "<th>FECHA INICIO</th>";
		$html .= "<th>FECHA FIN</th>";
		$html .= "<th>VALOR</th>";
		$html .= "<th>IVA</th>";
		$html .= "<th>NUMERO PERSONAS</th>";
		$html .= "<th>PAGADO</th>";
		$html .= "<th>ESTADO TRANSACCION</th>";
		$html .= "<th>FECHA TRANSACCION</th>";
		$html .= "<th>CODIGO RESPUESTA</th>";
		$html .= "<th>MEDIO PAGO</th>";
		$html .= "<th>OBSERVACIONES</th>";
		$html .= "<th>FECHA CREACION RESERVA</th>";
		$html .= "<th>USUARIO CREACION RESERVA</th>";
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$datos_hab = $dbo->fetchAll( "Habitacion", " IDHabitacion = '" . $Datos["IDHabitacion"] . "' ", "array" );
			$habitacion=$datos_hab["NumeroHabitacion"] . " - " . $dbo->getFields( "Torre" , "Nombre" , "IDTorre = '".$datos_hab["IDTorre"]."'" ) . " - " .
			$dbo->getFields( "TipoHabitacion" , "Nombre" , "IDTipoHabitacion = '".$datos_hab["IDTipoHabitacion"]."'" );

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . $Datos["IDReserva"] ."</td>";
			$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$Datos["IDSocio"]."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$Datos["IDSocio"]."'")) )) . "</td>";
			$html .= "<td>" . $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$Datos["IDSocio"]."'") . "</td>";
			$html .= "<td>" . $habitacion . "</td>";
			$html .= "<td>" . $Datos["Temporada"] ."</td>";
			$html .= "<td>" . $Datos["CabezaReserva"] ."</td>";
			$html .= "<td>" . $Datos["DocumentoDuenoReserva"] ."</td>";
			$html .= "<td>" . $Datos["NombreDuenoReserva"] ."</td>";
			$html .= "<td>" . $Datos["EmailDuenoReserva"] ."</td>";
			$html .= "<td>" . $Datos["Estado"] ."</td>";
			$html .= "<td>" . $Datos["FechaInicio"] ."</td>";
			$html .= "<td>" . $Datos["FechaFin"] ."</td>";
			$html .= "<td>" . $Datos["Valor"] ."</td>";
			$html .= "<td>" . $Datos["IVA"] ."</td>";
			$html .= "<td>" . $Datos["NumeroPersonas"] ."</td>";
			$html .= "<td>" . $Datos["Pagado"] ."</td>";
			$html .= "<td>" . $Datos["EstadoTransaccion"] ."</td>";
			$html .= "<td>" . $Datos["FechaTransaccion"] ."</td>";
			$html .= "<td>" . $Datos["CodigoRespuesta"] ."</td>";
			$html .= "<td>" . $Datos["MedioPago"] ."</td>";
			$html .= "<td>" . $Datos["Observaciones"] ."</td>";
			$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
			$html .= "<td>" . $Datos["UsuarioTrCr"] ."</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";

		//construimos el excel
		header('Content-type: application/vnd.ms-excel');
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
	exit();
	?>
</body>
</html>
<?
}
?>