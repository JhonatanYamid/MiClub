<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	
if(!empty($_GET["DocumentoSocio"])){
	$array_where [] = "S.NumeroDocumento = '".$_GET["DocumentoSocio"]."'";
}

if(!empty($_GET["DocumentoSocio"])){
	$array_where [] = "S.NumeroDocumento = '".$_GET["DocumentoSocio"]."'";
}

if(!empty($_GET["NombreSocio"])){
	$array_where [] = "S.Nombre like  '%".$_GET["NombreSocio"]."%'";
}
if(!empty($_GET["AccionSocio"])){
	$array_where [] = "S.Accion like  '%".$_GET["AccionSocio"]."%'";
}


if(!empty($_GET["ApellidoSocio"])){
	$array_where [] = "S.Apellido like '%".$_GET["ApellidoSocio"]."%'";
}

if(!empty($_GET["DocumentoContratista"])){
	$array_where [] = "I.NumeroDocumento = '".$_GET["DocumentoContratista"]."'";
}

if(!empty($_GET["NombreContratista"]) || !empty($_GET["ApellidoContratista"])){	
	if(!empty($_GET["NombreContratista"]))
		$array_condicion_nombre[] = " I.Nombre like '%".$_GET["NombreContratista"]."%'";
		
	if(!empty($_GET["ApellidoContratista"]))
		$array_condicion_nombre[] = " I.Apellido like '%".$_GET["ApellidoContratista"]."%'";	
		
	if(count($array_condicion_nombre)>0)	:
		$array_where [] = " ( " . implode(" and ", $array_condicion_nombre) . " ) ";
	endif;
		
	
	//$array_where [] = " (I.Nombre like '%".$_GET["NombreContratista"]."%' or I.Apellido like '%".$_GET["ApellidoContratista"]."%') ";
}

if(!empty($_GET["PlacaContratista"])){
	$sql_placa = "Select IDVehiculo From Vehiculo Where Placa like '%".$_GET["PlacaContratista"]."%' ";
	$r_placa = $dbo->query($sql_placa);
	while($row_placa = $dbo->fetchArray($r_placa)):
		$array_id_vehiculo [] = $row_placa["IDVehiculo"];
	endwhile;
	if(count($array_id_vehiculo)>0):
		$id_vehiculo = implode(",",$array_id_vehiculo);
	endif;	
	$array_where [] = " (  SocioAutorizacion.IDVehiculo in (".$id_vehiculo.")  )  ";	
}

if(!empty($_GET["PredioContratista"])){
	$array_where [] = "I.Predio like '%".$_GET["PredioContratista"]."%'";
}

if(!empty($_GET["LicenciaConduccion"])){
	$array_where [] = "I.Licencia = '".$_GET["LicenciaConduccion"]."'";
}

if(!empty($_GET["IDTipoInvitado"])){
	$array_where [] = "I.IDTipoInvitado = '".$_GET["IDTipoInvitado"]."'";
}

if(!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"])){
	$array_where [] = " ( SocioAutorizacion.FechaInicio >= '".$_GET["FechaInicio"]."' and SocioAutorizacion.FechaFin <= '".$_GET["FechaFin"]."') ";
}

if(!empty($_GET["IDInvitado"])){	
	$array_where [] = " SocioAutorizacion.IDInvitado = '".$_GET[IDInvitado]."' ";
}


if(count($array_where)>0):
	$where_filtro =  " and " . implode(" and ",$array_where);
endif;
	

	 /*
	 $sql_reporte = "SELECT SocioAutorizacion.*, I.NumeroDocumento, I.Email, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado, LA.*
					FROM SocioAutorizacion , Invitado I, LogAcceso LA   
					WHERE I.IDInvitado = SocioAutorizacion.IDInvitado 
					AND SocioAutorizacion.IDSocioAutorizacion = LA.IDInvitacion
					AND SocioAutorizacion.IDClub = '".$_GET["IDClub"]."'" . 
					$condicion . " ". $where . " " . $where_filtro . "Order by FechaFin Desc";
	*/				
	
	$table = "SocioAutorizacion";
	$where = " WHERE " . $table . ".IDClub = '" . $_GET["IDClub"] . "'  ";
	$sql_reporte = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado,I.NumeroDocumento,I.IDTipoInvitado FROM " . $table . " , Invitado I, Socio S " . $where . " AND SocioAutorizacion.IDSocio = S.IDSocio AND I.IDInvitado = SocioAutorizacion.IDInvitado ". $where_filtro. "  ORDER BY FechaFin Desc";				
					
	$result_reporte= $dbo->query( $sql_reporte );
		
	
	$nombre = "AccesoContratista_" . date( "Y_m_d" );
	$nombre = "AutorizacionesContratista_" . date( "Y_m_d" );

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
		$html .= "<th>TORRE SOCIO</th>";   
		$html .= "<th>INVITADO</th>";
		$html .= "<th>DOCUMENTO INVITADO</th>";   
		$html .= "<th>TIPO</th>";
		$html .= "<th>CLASIFICACION</th>";
		$html .= "<th>DIRECCION</th>";
		$html .= "<th>TELEFONO</th>";
		$html .= "<th>PREDIO AL QUE SE DIRIGE</th>";
		$html .= "<th>OBSERVACION GENERAL</th>";
		$html .= "<th>OBSERVACION ESPECIAL</th>";
		$html .= "<th>ESTADO INVITADO</th>";
		$html .= "<th>CODIGO AUTORIZACION</th>";
		$html .= "<th>PLACA</th>";
		$html .= "<th>FECHA INICIO AUTORIZACION</th>";
		$html .= "<th>HORA INICIO AUTORIZACION</th>";
		$html .= "<th>FECHA FIN AUTORIZACION</th>";
		$html .= "<th>HORA FIN AUTORIZACION</th>";
		$html .= "<th>CREADA POR</th>";
		$html .= "<th>USUARIO INGRESO</th>";
		$html .= "<th>USUARIO SALIDA</th>";
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
			$html .= "<td>" . $datos_socio["Torre"] . "</td>";
			$html .= "<td>" . utf8_encode($Datos["NombreInvitado"]) . "</td>";
			$html .= "<td>" . $datos_invitado["NumeroDocumento"] ."</td>";
			$html .= "<td>" . $dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$datos_invitado["IDTipoInvitado"]."'" ) . "</td>";
			$html .= "<td>" . $dbo->getFields( "ClasificacionInvitado" , "Nombre" , "IDClasificacionInvitado = '".$datos_invitado["IDClasificacionInvitado"]."'" ) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["Direccion"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["Telefono"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_socio["Predio"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["ObservacionGeneral"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["ObservacionEspecial"]) . "</td>";
			$html .= "<td>" . $dbo->getFields( "EstadoInvitado" , "Nombre" , "IDEstadoInvitado = '".$datos_invitado["IDEstadoInvitado"]."'" ) . "</td>";
			$html .= "<td>" . $Datos["CodigoAutorizacion"] . "</td>";
			$html .= "<td>" . $dbo->getFields( "Vehiculo" , "Placa" , "IDVehiculo = '".$Datos["IDVehiculo"]."'" ) . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["FechaInicio"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["HoraInicio"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["FechaFin"] )  . "</td>";	
			$html .= "<td>" . utf8_encode( $Datos["HoraFin"] )  . "</td>";			
			if($Datos["UsuarioTrCr"]=="Socio"):
				$creada_por = "Socio";
			else:
				$creada_por = $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$Datos["UsuarioTrCr"]."'" );
			endif;
			$html .= "<td>" . $creada_por. "</td>";			
			$html .= "<td>" . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$Datos["IDUsuarioIngreso"]."'" ). "</td>";			
			$html .= "<td>" . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$Datos["IDUsuarioSalida"]."'" ). "</td>";			
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