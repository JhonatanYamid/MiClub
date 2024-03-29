<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	$table = "LogAcceso";
	$where = " WHERE " . $table . ".IDClub = '" .$_GET["IDClub"] . "'  ";


////Busqueda por filtro
$bsuca_filtro=0;
if(!empty($_GET["DocumentoSocio"])){
	$array_where [] = "S.NumeroDocumento = '".$_GET["DocumentoSocio"]."'";
	$bsuca_filtro=1;
}

if(!empty($_GET["DocumentoSocio"])){
	$array_where [] = "S.NumeroDocumento = '".$_GET["DocumentoSocio"]."'";
}

if(!empty($_GET["NombreSocio"])){
	$array_where [] = "S.Nombre like  '%".$_GET["NombreSocio"]."%'";
	$bsuca_filtro=1;
}
if(!empty($_GET["AccionSocio"])){
	$array_where [] = "S.Accion like  '%".$_GET["AccionSocio"]."%'";
	$bsuca_filtro=1;
}


if(!empty($_GET["ApellidoSocio"])){
	$array_where [] = "S.Apellido like '%".$_GET["ApellidoSocio"]."%'";
	$bsuca_filtro=1;
}

if(!empty($_GET["DocumentoContratista"])){
	$array_where [] = "I.NumeroDocumento = '".$_GET["DocumentoContratista"]."'";
	$bsuca_filtro=1;
}

if(!empty($_GET["NombreContratista"]) || !empty($_GET["ApellidoContratista"])){
	if(!empty($_GET["NombreContratista"]))
		$array_condicion_nombre[] = " I.Nombre like '%".$_GET["NombreContratista"]."%'";

	if(!empty($_GET["ApellidoContratista"]))
		$array_condicion_nombre[] = " I.Apellido like '%".$_GET["ApellidoContratista"]."%'";

	if(count($array_condicion_nombre)>0)	:
		$array_where [] = " ( " . implode(" and ", $array_condicion_nombre) . " ) ";
		$bsuca_filtro=1;
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
	$bsuca_filtro=1;
}

if(!empty($_GET["PredioContratista"])){
	$array_where [] = "I.Predio like '%".$_GET["PredioContratista"]."%'";
	$bsuca_filtro=1;
}

if(!empty($_GET["LicenciaConduccion"])){
	$array_where [] = "I.Licencia = '".$_GET["LicenciaConduccion"]."'";
	$bsuca_filtro=1;
}

if(!empty($_GET["IDTipoInvitado"])){
	$array_where [] = "I.IDTipoInvitado = '".$_GET["IDTipoInvitado"]."'";
	$bsuca_filtro=1;
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

echo $where_filtro;exit;

	if(!empty($where_filtro) && $bsuca_filtro==1):
		$where_repor = " WHERE SocioAutorizacion.IDClub = '" . $_GET["IDClub"] . "'  ";
		$sql_reporte = "SELECT SocioAutorizacion.*, CONCAT( I.Nombre, ' ', I.Apellido ) AS NombreInvitado,I.NumeroDocumento,I.IDTipoInvitado FROM SocioAutorizacion , Invitado I, Socio S " . $where_repor . " AND SocioAutorizacion.IDSocio = S.IDSocio AND I.IDInvitado = SocioAutorizacion.IDInvitado ". $where_filtro. "  ORDER BY FechaFin Desc";
		$result_reporte= $dbo->query( $sql_reporte );
		unset($array_where);
		while($row_reporte = $dbo->fetcharray($result_reporte)):
			$array_id_aut[]= $row_reporte["IDSocioAutorizacion"];
		endwhile;
		if(count($array_id_aut)>0):
			$array_where [] = " IDInvitacion in (".implode(",",$array_id_aut).") and Tipo = 'Contratista' ";
		endif;
	else:
		unset($array_where)	;
	endif;

////Fin Busqueda por filtro





if(!empty($_GET["Documento"]) || !empty($_GET["IDInvitado"])){
	//busco los invitados o socio con el numero de documento

	if(!empty($_GET["IDInvitado"]))
		$id_invitado = $_GET["IDInvitado"];
	elseif(!empty($_GET["Documento"]))
		$id_invitado = $dbo->getFields( "Invitado" , "IDInvitado" , "NumeroDocumento = '".$_GET["Documento"]."'" );


	if(!empty($id_invitado)):
		//Busco las autorizaciones a contratistas
		$sql_autorizacion = "Select * From SocioAutorizacion Where IDInvitado = '".$id_invitado."'";
		$result_autorizacion = $dbo->query($sql_autorizacion);
		while($row_autorizacion = $dbo->fetchArray($result_autorizacion)):
			if(!empty($row_autorizacion["IDSocioAutorizacion"])):
				$array_autorizaciones[]= $row_autorizacion["IDSocioAutorizacion"];
				$TipoBusqueda='Contratista';
			endif;
		endwhile;

		//Busco las invitaciones
		$sql_autorizacion = "Select * From SocioInvitadoEspecial Where IDInvitado = '".$id_invitado."'";
		$result_autorizacion = $dbo->query($sql_autorizacion);
		while($row_autorizacion = $dbo->fetchArray($result_autorizacion)):
			if(!empty($row_autorizacion["IDSocioInvitadoEspecial"])):
				$array_autorizaciones[]= $row_autorizacion["IDSocioInvitadoEspecial"];
				if(empty($TipoBusqueda))
					$TipoBusqueda='InvitadoAcceso';
				else
					$condicion_inv = " or Tipo = 'InvitadoAcceso' ";
			endif;
		endwhile;


	else:
		$id_socio = $dbo->getFields( "Socio" , "IDSocio" , "NumeroDocumento = '".$_GET["Documento"]."' and IDClub = '".$_GET["IDClub"]."'" );
		$array_autorizaciones[]= $id_socio;
		$TipoBusqueda='Socio';
	endif;

	if(count($array_autorizaciones)>0):
		$id_autorizaciones = implode(",",$array_autorizaciones);
	else:
		$id_autorizaciones = "1"; //para que no encuentre resultados
	endif;

	$array_where [] = " IDInvitacion in (".$id_autorizaciones.") and (Tipo = '".$TipoBusqueda."' ".$condicion_inv.") ";
}


if(!empty($_GET["Placa"])){
	$array_where [] = " Mecanismo like '%".$_GET["Placa"]."%' ";
}





if(!empty($_GET["IDTipoInvitado"])){
	switch($_GET["IDTipoInvitado"]):
		case "Socio":
			$array_where [] = " Tipo = 'Socio' ";
		break;
		case "ContratistaSocio":
			$array_where [] = " Tipo = 'Contratista' ";
		break;
		case "InvitadoSocio":
			$array_where [] = " Tipo = 'InvitadoAcceso' ";
		break;
		default:
			$sql_tipo_invitado = "Select * From Invitado Where IDTipoInvitado = '".$_GET["IDTipoInvitado"]."'";
			$result_tipo_invitado = $dbo->query($sql_tipo_invitado);
			while($row_tipo_invitado = $dbo->fetchArray($result_tipo_invitado)):
				$array_id_invitado_tipo[]= $row_tipo_invitado["IDInvitado"];
			endwhile;
			if(count($array_id_invitado_tipo)>0):
				$id_invitado_tipo = implode(",",$array_id_invitado_tipo);
				//Busco las autorizaciones
				$sql_autorizacion = "Select * From SocioAutorizacion Where IDInvitado in (".$id_invitado_tipo.")";
				$result_autorizacion = $dbo->query($sql_autorizacion);
				while($row_autorizacion = $dbo->fetchArray($result_autorizacion)):
					$array_autorizaciones_tipo[]= $row_autorizacion["IDSocioAutorizacion"];
					$TipoBusqueda='Contratista';
				endwhile;
			endif;
			if(count($array_autorizaciones_tipo)>0):
				$id_autorizaciones_tipo = implode(",",$array_autorizaciones_tipo);
			else:
				$id_autorizaciones_tipo = "1"; //para que no encuentre resultados
			endif;

			$array_where [] = " IDInvitacion in (".$id_autorizaciones_tipo.") and Tipo = '".$TipoBusqueda."' ";

		break;


	endswitch;

}

if(!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"])){
	$array_where [] = " ( FechaTrCr >= '".$_GET["FechaInicio"]." 00:00:00' and FechaTrCr <= '".$_GET["FechaFin"]." 23:59:59') ";
}


if(count($array_where)>0):
	$where_filtro =  " and " . implode(" and ",$array_where);
endif;


	$sql_reporte = "SELECT " . $table . ".* FROM " . $table . " " . $where . " ". $where_filtro. "  ORDER BY FechaTrCr Desc";

	$result_reporte= $dbo->query( $sql_reporte );


	$nombre = "EntradaSalidas_" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>TIPO</th>";
		$html .= "<th>DOCUMENTO</th>";
		$html .= "<th>NOMBRE</th>";
		$html .= "<th>PREDIO SOCIO</th>";
		$html .= "<th>TIPO MOVIMIENTO</th>";
		$html .= "<th>FECHA/HORA</th>";
		$html .= "<th>MECANISMO</th>";
		$html .= "<th>USUARIO</th>";
		$html .= "</tr>";
		while( $row = $dbo->fetchArray( $result_reporte ) )
		{


			switch($row["Tipo"]):
			case "Contratista":
				$datos_invitacion = $dbo->fetchAll( "SocioAutorizacion", " IDSocioAutorizacion = '" . $row["IDInvitacion"] . "' ", "array" );
				$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );
				$nombre_movimiento = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
				$documento_movimiento = $datos_invitado["NumeroDocumento"];
				$predio_movimiento = utf8_encode($datos_invitacion["Predio"]);
				if(empty($predio_movimiento))
					$predio_movimiento = $dbo->getFields( "Socio" , "Predio" , "IDSocio = '".$datos_invitacion["IDSocio"]."'" );


				$tipo_persona = "Contratista";
			break;
			case "Invitado":
				$datos_invitado = $dbo->fetchAll( "SocioInvitado", " IDSocioInvitado = '" . $row["IDInvitacion"] . "' ", "array" );
				$nombre_movimiento = utf8_encode($datos_invitado["Nombre"]);
				$documento_movimiento = $datos_invitado["NumeroDocumento"];
				$predio_movimiento = "";
				$tipo_persona = "Invitado Socio v1";
			break;
			case "InvitadoAcceso":
				$datos_invitacion = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $row["IDInvitacion"] . "' ", "array" );
				$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array" );
				$nombre_movimiento = trim(utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]));
				$documento_movimiento = $datos_invitado["NumeroDocumento"];
				if(empty($nombre_movimiento))
					$nombre_movimiento = "Acceso nro ".$row["IDLogAcceso"];

				$predio_movimiento = utf8_encode($datos_invitado["Predio"]);
				$tipo_persona = "Invitado Socio";
			break;
			case "InvitadoSocio":
				$nombre_movimiento = "Invitado anterior";
				$predio_movimiento = "";
				$tipo_persona = "Invitado v0";
			break;
			case "Socio":
				$datos_invitado = $dbo->fetchAll( "Socio", " IDSocio = '" . $row["IDInvitacion"] . "' ", "array" );
				$nombre_movimiento = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
				$documento_movimiento = $datos_invitado["NumeroDocumento"];
				$predio_movimiento = utf8_encode($datos_invitado["Predio"]);
				$tipo_persona = "Socio";
			break;
			case "SocioInvitado":
			break;

		endswitch;


		if($row["Salida"]=="S"):
			$TipoMovimiento="Salida";
			$FechaMovimiento=$row["FechaSalida"];
		elseif($row["Entrada"]=="S"):
			$TipoMovimiento="Entrada";
			$FechaMovimiento=$row["FechaIngreso"];
		endif;



			$html .= "<tr>";
			$html .= "<td>" . $tipo_persona ."</td>";
			$html .= "<td>" . $documento_movimiento ."</td>";
			$html .= "<td>" . utf8_encode($nombre_movimiento ) ."</td>";
			$html .= "<td>" . $predio_movimiento ."</td>";
			$html .= "<td>" . utf8_encode($TipoMovimiento) . "</td>";
			$html .= "<td>" . $FechaMovimiento ."</td>";
			$html .= "<td>" . $row["Mecanismo"] . "</td>";
			$html .= "<td>" . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$row["IDUsuario"]."'" ) . "</td>";
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
