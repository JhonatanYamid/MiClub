<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	
	
	$sql_reporte = "SELECT SocioAutorizacion.*, I.NumeroDocumento, I.Email, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado
					FROM SocioAutorizacion , Invitado I
					WHERE I.IDInvitado = SocioAutorizacion.IDInvitado 					
					AND SocioAutorizacion.IDClub = '".$_GET["IDClub"]."'" . 
					$condicion . " ". $where . " " . $where_filtro;
					
	$result_reporte= $dbo->query( $sql_reporte );
		
	
	$nombre = "Contratista_" . date( "Y_m_d" );

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
		$html .= "<th>DOCUMENTO SOCIO</th>";
		$html .= "<th>NOMBRE SOCIO</th>";    
		$html .= "<th>INVITADO</th>";
		$html .= "<th>DOCUMENTO INVITADO</th>";   
		$html .= "<th>TIPO</th>";
		$html .= "<th>CLASIFICACION</th>";
		$html .= "<th>DIRECCION</th>";
		$html .= "<th>TELEFONO</th>";
		$html .= "<th>PREDIO AL QUE SE DIRIGE</th>";
		$html .= "<th>OBSERVACION GENERAL</th>";
		$html .= "<th>OBSERVACION ESPECIAL</th>";		
		$html .= "<th>CODIGO AUTORIZACION</th>";
		$html .= "<th>LICENCIA CONDUCCION</th>";
		$html .= "<th>PLACA</th>";
		
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{	
			
			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos["IDSocio"] . "' ", "array" );	
			$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $Datos["IDInvitado"] . "' ", "array" );	
	
		
		
			$html .= "<tr>";			
			$html .= "<td>" . $datos_socio["Accion"] ."</td>";
			$html .= "<td>" . $datos_socio["Predio"] ."</td>";
			$html .= "<td>" . $datos_socio["NumeroDocumento"] ."</td>";			
			$html .= "<td>" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Nombre"] ) ."</td>";			
			$html .= "<td>" . utf8_encode($Datos["NombreInvitado"]) . "</td>";
			$html .= "<td>" . $datos_invitado["NumeroDocumento"] ."</td>";
			$html .= "<td>" . $dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$datos_invitado["IDTipoInvitado"]."'" ) . "</td>";
			$html .= "<td>" . $dbo->getFields( "ClasificacionInvitado" , "Nombre" , "IDClasificacionInvitado = '".$datos_invitado["IDClasificacionInvitado"]."'" ) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["Direccion"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["Telefono"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["Predio"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["ObservacionGeneral"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["ObservacionEspecial"]) . "</td>";
			$html .= "<td>" . $dbo->getFields( "EstadoInvitado" , "Nombre" , "IDEstadoInvitado = '".$datos_invitado["IDEstadoInvitado"]."'" ) . "</td>";
			//Licencias asociados
			unset($array_licencias_invitado);
			$licencias_invitado="";
			$sql_licencia= "Select * From LicenciaInvitado Where IDInvitado = '".$Datos["IDInvitado"]."'";
			$result_invitado = $dbo->query($sql_licencia); 
			while($row_licencia = $dbo->fetchArray($result_invitado)):
				$array_licencias_invitado[]= "Categoria: " . $row_licencia["Categoria"] . " Fecha Vencimiento: " . $row_licencia["FechaVencimiento"];				
			endwhile;
			if(count($array_licencias_invitado)>0):
				$licencias_invitado = implode("<br>",$array_licencias_invitado);
			endif;
			//Licencias asociados
			unset($array_vehiculo_invitado);
			$vehiculo_invitado="";
			$sql_vehiculo= "Select * From Vehiculo Where IDInvitado = '".$Datos["IDInvitado"]."'";
			$result_vehiculo = $dbo->query($sql_vehiculo); 
			while($row_vehiculo = $dbo->fetchArray($result_vehiculo)):
				$array_vehiculo_invitado []= "Placa: " . $row_vehiculo["Placa"] . " Tecnomecanica: " . $row_licencia["FechaTecnomecanica"] . " Seguro: ".$row_licencia["FechaSeguro"];
			endwhile;
			if(count($array_vehiculo_invitado)>0):
				$vehiculo_invitado = implode("<br>",$array_vehiculo_invitado);
			endif;
			
			$html .= "<td>" . $licencias_invitado . "</td>";
			$html .= "<td>" . $vehiculo_invitado . "</td>";
				
						
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