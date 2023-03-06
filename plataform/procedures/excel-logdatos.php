<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	
	$sql_reporte = "Select * From LogCambioDatos Where IDClub = '".$_GET["IDClub"]."' " . $where;
	$result_reporte= $dbo->query( $sql_reporte );
	
	$nombre = "SociosActivos_" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );
	
	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
        $html .= "<th>NRO DERECHO</th>";	
		$html .= "<th>FECHA MODIFICACION</th>";		
		$html .= "<th>CAMPOS ACTUALIZADO</th>";        
		$html .= "<th>NUEVO DATO</th>";
		$html .= "<th>USUARIO ACTUALIZO</th>";		
		
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			
			if($Datos["Tabla"]=="Socio"):
				$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos["ValorID"] . "' ", "array" );
			endif;
			
			$html .= "<tr>";						
			$html .= "<td>" . $datos_socio["Accion"] . "</td>";
			$html .= "<td>" . $Datos["Fecha"] . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Campo"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["NuevoDato"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["NombreUsuario"] )  . "</td>";			
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