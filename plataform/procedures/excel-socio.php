<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	
	
	
	$sql_reporte = "Select TipoSocio,Accion,NumeroDocumento,Nombre,Apellido,FechaNacimiento,IDCategoria,Email,CorreoElectronico,Dispositivo,FechaTrEd,FechaPrimerIngreso,Foto,UsuarioTrCr,UsuarioTrEd From Socio Where IDClub = '".$_GET["IDClub"]."' and Token <> ''";	
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
        $html .= "<th>TIPO SOCIO</th>";	
		$html .= "<th>NUMERO DERECHO</th>";		
		$html .= "<th>DOCUMENTO</th>";        
		$html .= "<th>NOMBRE</th>";
		$html .= "<th>APELLIDO</th>";
		$html .= "<th>FECHA DE NACIMIENTO</th>";
		$html .= "<th>CATEGORIA</th>";
		$html .= "<th>USUARIO</th>";
		$html .= "<th>CORREO ELECTRONICO</th>";
		$html .= "<th>DISPOSITIVO</th>";
		$html .= "<th>FECHA CREACION</th>";
		$html .= "<th>FECHA PRIMER INGRESO</th>";
		$html .= "<th>FOTO</th>";
		$html .= "<th>Usuario Creacion</th>";
		$html .= "<th>Usuario Modificacion</th>";
		
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			$html .= "<tr>";			
			$html .= "<td>" . $Datos["TipoSocio"] ."</td>";
			$html .= "<td>" . (string)$Datos["Accion"] . "</td>";
			$html .= "<td>" . $Datos["NumeroDocumento"] . "</td>";
			$html .= "<td>" . $Datos["Nombre"]   . "</td>";
			$html .= "<td>" .  $Datos["Apellido"]   . "</td>";
			$html .= "<td>" .  $Datos["FechaNacimiento"]   . "</td>";
			$html .= "<td>" . $dbo->getFields("Categoria", "Nombre", "IDCategoria = '" . $Datos["IDCategoria"] . "'")   . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Email"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["CorreoElectronico"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Dispositivo"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["FechaTrEd"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["FechaPrimerIngreso"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Foto"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["UsuarioTrCr"] )  . "</td>";	
			$html .= "<td>" . utf8_encode( $Datos["UsuarioTrEd"] )  . "</td>";			
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
