<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	
	
	
	$sql_reporte = "Select * From Usuario Where IDClub = '".$_GET["IDClub"]."' and Token <> ''";	
	$result_reporte= $dbo->query( $sql_reporte );
	
	$nombre = "FuncionariosActivos_" . date( "Y_m_d" );

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
		$html .= "<th>CORREO ELECTRONICO</th>";
		$html .= "<th>DISPOSITIVO</th>";
		$html .= "<th>FOTO</th>";
		
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			$html .= "<tr>";									
			$html .= "<td>" . $Datos["NumeroDocumento"] . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Nombre"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["Email"] )  . "</td>";						
			$html .= "<td>" . utf8_encode( $Datos["Dispositivo"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Foto"] )  . "</td>";			
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