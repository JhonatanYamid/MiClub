<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	$sql_reporte = "SELECT IDSocio,IDVotacionEvento,Tipo,Fecha,IDUsuario
													FROM LogAccesoVotacion
													Where  IDVotacionEvento = '".$_GET["IDVotacionEvento"]."'
													ORDER BY IDSocio,Fecha ASC";
	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "IngresoSalidaEventoVotacion_" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
    $html .= "<th>Evento</th>";
		$html .= "<th>Persona</th>";
		$html .= "<th>Tipo</th>";
		$html .= "<th>Fecha</th>";
		$html .= "<th>Usuario</th>";
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos["IDSocio"] . "'", "array" );
			$html .= "<tr>";
			$html .= "<td>" . utf8_encode( $dbo->getFields( "VotacionEvento", "Nombre", "IDVotacionEvento = '" . $Datos["IDVotacionEvento"] . "'" ) )  . "</td>";
			$html .= "<td>" . utf8_encode( $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Tipo"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Fecha"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $dbo->getFields( "Usuario", "Nombre", "IDUsuario = '" . $Datos["IDUsuario"] . "'" ) )  . "</td>";
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
