<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	
	
	
	$sql_reporte = "SELECT SocioAutorizacion.*, I.NumeroDocumento, I.Email, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado FROM SocioAutorizacion , Invitado I  WHERE I.IDInvitado = SocioAutorizacion.IDInvitado and SocioAutorizacion.IDClub = '".$_GET["IDClub"]."' " . $where;
	$result_reporte= $dbo->query( $sql_reporte );
		
	
	$nombre = "AccesoContratista_" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );
	
	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";       
		$html .= "<th>ACCION</th>";        
		$html .= "<th>PREDIO</th>";		
		$html .= "<th>NOMBRE SOCIO</th>";    
		$html .= "<th>INVITADO</th>";
		$html .= "<th>CODIGO AUTORIZACION</th>";
		$html .= "<th>FECHA INICIO</th>";
		$html .= "<th>FECHA FIN</th>";
		$html .= "<th>FECHA INGRESO</th>";
		$html .= "<th>FECHA SALIDA</th>";
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{	
			
			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos["IDSocio"] . "' ", "array" );	
	
		
		
			$html .= "<tr>";			
			$html .= "<td>" . $datos_socio["Accion"] ."</td>";
			$html .= "<td>" . "-" ."</td>";
			$html .= "<td>" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Nombre"] ) ."</td>";
			$html .= "<td>" . utf8_encode($Datos["NombreInvitado"]) . "</td>";
			$html .= "<td>" . $Datos["CodigoAutorizacion"] . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["FechaInicio"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["FechaFin"] )  . "</td>";	
			$html .= "<td>" . $Datos["FechaIngreso"]   . "</td>";
			$html .= "<td>" . $Datos["FechaSalida"]   . "</td>";						
			$html .= "</tr>";
		}
		$html .= "</table>";
		
		//construimos el excel
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");		
		echo $html;
		exit();
        }
?>