<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	
	
	
	$sql_reporte = "SELECT SocioAutorizacion.*, I.NumeroDocumento, I.Email, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado FROM SocioAutorizacion , Invitado I  WHERE I.IDInvitado = SocioAutorizacion.IDInvitado and Salida = 'N' and SocioAutorizacion.IDClub = '".$_GET["IDClub"]."' " . $where;
	$result_reporte= $dbo->query( $sql_reporte );
	
	$nombre = "Invitaciones_" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );
	
	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";       
		$html .= "<th>DOCUMENTO</th>";        
		$html .= "<th>NOMBRE</th>";		
		$html .= "<th>TIPO</th>";    
		$html .= "<th>EMAIL</th>";
		$html .= "<th>FECHA INICIO</th>";
		$html .= "<th>FECHA FIN</th>";
		$html .= "<th>FECHA INGRESO</th>";
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{	
			$html .= "<tr>";			
			$html .= "<td>" . $Datos["NumeroDocumento"] ."</td>";
			$html .= "<td>" . utf8_encode($Datos["NombreInvitado"]) . "</td>";
			$html .= "<td>" . $Datos["TipoAutorizacion"] . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Email"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["FechaInicio"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["FechaFin"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["FechaIngreso"] )  . "</td>";			
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