<?php
//Script para exportar reporte de contactos por rango de fechas
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

		  $sql = "Select * From ListaEspera Where FechaInicio >= '".$_POST["FechaInicio"]."' and FechaFin <= '".$_POST["FechaFin"]."' and Tipo  = 'Hotel' and IDClub = '".$_POST["IDClub"]."'";
		 
		$nombre = "Lista_Espera Hotel" . date( "Y_m_d H:i:s" );

		$qry = $dbo->query( $sql );
		$Num=$dbo->rows( $qry );

		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>NUMERO ACCION</th>";
		$html .= "<th>TIPO SOCIO</th>";
		$html .= "<th>SOCIO</th>";
		$html .= "<th>USUARIO</th>";
		$html .= "<th>SERVICIO</th>";
		$html .= "<th>FECHA INICIO</th>";
		$html .= "<th>FECHA FIN</th>";
		$html .= "<th>HORA INICIO</th>";
		$html .= "<th>HORA FIN</th>";
		$html .= "<th>CELULAR</th>";
		$html .= "<th>FECHA CREACION</th>";
		$html .= "</tr>";
		$item=0;
		while( $row = $dbo->fetchArray( $qry,$a ) )
		{
			$html .= "<tr>";


			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row["IDSocio"] . "' ", "array" );
			$html .= "<td>".$datos_socio["Accion"]."</td>";
			$html .= "<td>".$datos_socio["TipoSocio"]."</td>";
			$html .= "<td>".$datos_socio["Nombre"] . " ". $datos_socio["Apellido"]."</td>";
			$html .= "<td>".$datos_socio["Email"]."</td>";
			$html .= "<td>Hotel</td>";
			$html .= "<td>".$row["FechaInicio"]."</td>";
			$html .= "<td>".$row["FechaFin"]."</td>";
			$html .= "<td>".$row["Horainicio"]."</td>";
			$html .= "<td>".$row["HoraFin"]."</td>";
			$html .= "<td>".$row["Celular"]."</td>";
			$html .= "<td>".$row["FechaTrCr"]."</td>";
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

?>
