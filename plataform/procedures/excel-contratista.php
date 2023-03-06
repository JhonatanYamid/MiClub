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
	$sql_reporte = "SELECT " . $table . ".*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado,I.NumeroDocumento,I.IDTipoInvitado FROM " . $table . " , Invitado I, Socio S " . $where . " AND SocioAutorizacion.IDSocio = S.IDSocio AND I.IDInvitado = SocioAutorizacion.IDInvitado ". $where_filtro. "  Group by I.IDInvitado ORDER BY FechaFin Desc";				
					
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
		$html .= "<th>INVITADO</th>";
		$html .= "<th>DOCUMENTO INVITADO</th>";   
		$html .= "<th>TIPO</th>";
		$html .= "<th>CLASIFICACION</th>";
		$html .= "<th>DIRECCION</th>";
		$html .= "<th>CIUDAD RESIDENCIA</th>";
		$html .= "<th>TELEFONO</th>";
		$html .= "<th>OBSERVACION GENERAL</th>";
		$html .= "<th>OBSERVACION ESPECIAL</th>";
		$html .= "<th>ESTADO INVITADO</th>";		
		$html .= "<th>PLACA</th>";
		$html .= "<th>ARL</th>";
		$html .= "<th>FECHA VENCIMIENTO ARL</th>";
		$html .= "<th>AFP</th>";
		$html .= "<th>EPS</th>";
		$html .= "<th>TIPO SANGRE</th>";		
		$html .= "<th>NOMBRE EMERGENCIA</th>";
		$html .= "<th>APELLIDO EMERGENCIA</th>";
		$html .= "<th>DOCUMENTO EMERGENCIA</th>";
		$html .= "<th>DIRECCION EMERGENCIA</th>";
		$html .= "<th>TELEFONO EMERGENCIA</th>";
		$html .= "<th>EMAIL EMERGENCIA</th>";
		$html .= "<th>LICENCIAS</th>";
		
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{	
			
			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos["IDSocio"] . "' ", "array" );	
			$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $Datos["IDInvitado"] . "' ", "array" );	
		
			$html .= "<tr>";						
			$html .= "<td>" . utf8_encode($Datos["NombreInvitado"]) . "</td>";
			$html .= "<td>" . $datos_invitado["NumeroDocumento"] ."</td>";
			$html .= "<td>" . $dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$datos_invitado["IDTipoInvitado"]."'" ) . "</td>";
			$html .= "<td>" . $dbo->getFields( "ClasificacionInvitado" , "Nombre" , "IDClasificacionInvitado = '".$datos_invitado["IDClasificacionInvitado"]."'" ) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["Direccion"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["CiudadResidencia"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["Telefono"]) . "</td>";			
			$html .= "<td>" . utf8_encode($datos_invitado["ObservacionGeneral"]) . "</td>";
			$html .= "<td>" . utf8_encode($datos_invitado["ObservacionEspecial"]) . "</td>";
			$html .= "<td>" . $dbo->getFields( "EstadoInvitado" , "Nombre" , "IDEstadoInvitado = '".$datos_invitado["IDEstadoInvitado"]."'" ) . "</td>";			
			$html .= "<td>" . $dbo->getFields( "Vehiculo" , "Placa" , "IDVehiculo = '".$Datos["IDVehiculo"]."'" ) . "</td>";			
			$html .= "<td>" . $dbo->getFields( "Arl" , "Nombre" , "IDArl = '".$datos_invitado["IDArl"]."'" ) . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["FechaVencimientoArl"] )  . "</td>";
			$html .= "<td>" . $dbo->getFields( "Afp" , "Nombre" , "IDAfp = '".$datos_invitado["IDAfp"]."'" ) . "</td>";
			$html .= "<td>" . $dbo->getFields( "Eps" , "Nombre" , "IDEps = '".$datos_invitado["IDEps"]."'" ) . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["TipoSangre"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["NombreEmergencia"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["ApellidoEmergencia"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["DocumentoEmergencia"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["DireccionEmergencia"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["TelefonoEmergencia"] )  . "</td>";			
			$html .= "<td>" . utf8_encode( $Datos["EmailEmergencia"] )  . "</td>";			
	
			$datos_licencia="";
			$sql_licencias= "Select * From LicenciaInvitado Where IDInvitado = '".$datos_invitado["IDInvitado"]."'";
			$result_licencias = $dbo->query($sql_licencias);
			while($row_licencias = $dbo->fetchArray($result_licencias)):
				$datos_licencia .= " Tipo: " . $row_licencias["Categoria"] . " Fecha Vencimiento: " . $row_licencias["FechaVencimiento"];
			endwhile;
			
			$html .= "<td>" . $datos_licencia. "</td>";
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