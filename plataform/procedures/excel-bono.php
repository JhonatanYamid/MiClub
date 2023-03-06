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
		$condicion_fecha=" and FechaDesde >= '".$_POST["FechaInicio"]."'  and FechaHasta <= '".$_POST["FechaFin"]."'";
	}



	$sql_reporte = "Select *
					From ClubCodigoPago
					Where IDClub = '".$_POST["IDClub"]."'"." ".$condicion_fecha." Order By IDClubCodigoPago DESC" ;

	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "Bonos_Corte:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>DOCUMENTO SOCIO</th>";
    $html .= "<th>SOCIO</th>";
		$html .= "<th>CODIGO</th>";
		$html .= "<th>DISPONIBLE</th>";
		$html .= "<th>FECHA DESDE</th>";
		$html .= "<th>FECHA HASTA</th>";
		$html .= "<th>DESCRIPCION</th>";
		$html .= "<th>CREADO POR</th>";



		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			$html .= "<tr>";
			$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields( "Socio" , "NumeroDocumento" , "IDSocio = '".$Datos["IDSocio"]."'")))) . "</td>";
			$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$Datos["IDSocio"]."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$Datos["IDSocio"]."'")) )) . "</td>";
			$html .= "<td>" . $Datos["Codigo"]  . "</td>";
			$html .= "<td>" . $Datos["Disponible"]  . "</td>";
			$html .= "<td>" . $Datos["FechaDesde"]   . "</td>";
			$html .= "<td>" . $Datos["FechaHasta"]   . "</td>";
			$html .= "<td>" . $Datos["Descripcion"]   . "</td>";
			$html .= "<td>" . $Datos["UsuarioTrCr"]   . "</td>";		
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
