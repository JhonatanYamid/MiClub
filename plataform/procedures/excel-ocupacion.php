<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	$array_adentro = SIMUtil::consulta_ocupacion($_GET,$_GET["IDClub"],"ID" );

	if(count($array_adentro)>0):
		$id_log_acceso = implode(",",$array_adentro);
	endif;

	$sql_reporte = "Select * From LogAcceso Where IDClub = '".$_GET["IDClub"]."' and IDLogAcceso in (".$id_log_acceso.")";
	$result_reporte= $dbo->query( $sql_reporte );

	$nombre_archivo = "Ocupacion_" . date( "Y_m_d_H:i:s" );

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
		$html .= "<th>CLASE</th>";
		$html .= "<th>DOCUMENTO</th>";
		$html .= "<th>NOMBRE</th>";
		$html .= "<th>PREDIO</th>";
		$html .= "<th>FECHA INGRESO</th>";
		$html .= "<th>HORA INGRESO</th>";
		$html .= "<th>USUARIO REGISTRO PORTERIA</th>";
		$html .= "<th>USUARIO REGISTRO INVITACION / AUTORIZACION</th>";
		$html .= "<th>FECHA USUARIO REGISTRO INVITACION / AUTORIZACION</th>";

		$html .= "</tr>";
		while( $row = $dbo->fetchArray( $result_reporte ) )	{

			$nombre="";
	$predio="";
	$UsuarioRegistroInvitacion="";
	$IDUsuarioRegistroInvitacion="";
	$FechaUsuarioRegistroInvitacion  = "";
	$clase_invitado="";

	switch($row["Tipo"]):
		case "Contratista":
		case "InvitadoSocio":
			$IDInvitado=$dbo->getFields( "SocioAutorizacion" , "IDInvitado" , "IDSocioAutorizacion = '".$row["IDInvitacion"]."'" );
			$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitado . "' ", "array" );
			$IDTipoInvitado=$datos_invitado["IDTipoInvitado"];
			$tipo_invitado=$dbo->getFields( "TipoInvitado" , "Nombre" , "IDTipoInvitado = '".$IDTipoInvitado."'" );
			$clase_invitado=$dbo->getFields( "ClasificacionInvitado" , "Nombre" , "IDClasificacionInvitado = '".$datos_invitado["IDClasificacionInvitado"]."'" );

			$documento=$datos_invitado["NumeroDocumento"];
			$nombre = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
			$predio = utf8_encode($datos_invitado["Predio"]);
			//Registrado Invitacion
			$IDUsuarioRegistroInvitacion=$dbo->getFields( "SocioAutorizacion" , "UsuarioTrCr" , "IDSocioAutorizacion = '".$row["IDInvitacion"]."'" );
			$FechaUsuarioRegistroInvitacion=$dbo->getFields( "SocioAutorizacion" , "FechaTrCr" , "IDSocioAutorizacion = '".$row["IDInvitacion"]."'" );
			$UsuarioRegistroInvitacion=$dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$IDUsuarioRegistroInvitacion."'" );
			if(empty($UsuarioRegistroInvitacion))
				$UsuarioRegistroInvitacion=$IDUsuarioRegistroInvitacion;

		break;
		case "InvitadoAcceso":
			$IDInvitado=$dbo->getFields( "SocioInvitadoEspecial" , "IDInvitado" , "IDSocioInvitadoEspecial = '".$row["IDInvitacion"]."'" );
			$IDSocioAutoriza=$dbo->getFields( "SocioInvitadoEspecial" , "IDSocio" , "IDSocioInvitadoEspecial = '".$row["IDInvitacion"]."'" );
			$datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $IDInvitado . "' ", "array" );
			$tipo_invitado="Invitado Socio";
			$documento = $datos_invitado["NumeroDocumento"];
			$nombre = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
			$predio=$dbo->getFields( "Socio" , "Predio" , "IDSocio = '".$IDSocioAutoriza."'" );
			$predio = utf8_encode($predio);
			//Registrado Invitacion
			$IDUsuarioRegistroInvitacion=$dbo->getFields( "SocioInvitadoEspecial" , "UsuarioTrCr" , "IDSocioInvitadoEspecial = '".$row["IDInvitacion"]."'" );
			$FechaUsuarioRegistroInvitacion=$dbo->getFields( "SocioInvitadoEspecial" , "FechaTrCr" , "IDSocioInvitadoEspecial = '".$row["IDInvitacion"]."'" );
			$UsuarioRegistroInvitacion=$dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$IDUsuarioRegistroInvitacion."'" );
			if(empty($UsuarioRegistroInvitacion))
				$UsuarioRegistroInvitacion=$IDUsuarioRegistroInvitacion;



		break;
		case "Socio":
			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $row["IDInvitacion"] . "' ", "array" );
			$documento=$datos_socio["NumeroDocumento"];
			$nombre=utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
			$predio=$dbo->getFields( "Socio" , "Predio" , "IDSocio = '".$row["IDInvitacion"]."'" );
			$tipo_invitado = "Socio";
		break;
		case "SocioInvitado":
			$datos_socio_invitado = $dbo->fetchAll( "SocioInvitado", " IDSocioInvitado = '" . $row["IDInvitacion"] . "' ", "array" );
			$documento=$datos_socio_invitado["NumeroDocumento"]."DD";
			$nombre=utf8_encode($datos_socio_invitado["Nombre"]);
			$predio="n/a";
			$tipo_invitado = "Invitado por socio";
			//Registrado Invitacion
			$IDUsuarioRegistroInvitacion=$dbo->getFields( "SocioInvitado" , "UsuarioTrCr" , "IDSocioInvitado = '".$row["IDInvitacion"]."'" );
			$FechaUsuarioRegistroInvitacion=$dbo->getFields( "SocioInvitado" , "FechaTrCr" , "IDSocioInvitado = '".$row["IDInvitacion"]."'" );
			if(empty($IDUsuarioRegistroInvitacion))
				$UsuarioRegistroInvitacion=$IDUsuarioRegistroInvitacion;
		break;
	endswitch;

	//Registrado por
	$UsuarioRegistro=$dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$row["IDUsuario"]."'" );

			if($predio==""){
				$predio=$row["Predio"];
			}

			$html .= "<tr>";
			$html .= "<td>" . $tipo_invitado . "</td>";
			$html .= "<td>" . $clase_invitado ."</td>";
			$html .= "<td>" . $documento . "</td>";
			$html .= "<td>" . $nombre . "</td>";
			$html .= "<td>" . $predio  . "</td>";
			$html .= "<td>" . substr($row["FechaIngreso"],0,10)  . "</td>";
			$html .= "<td>" . substr($row["FechaIngreso"],10)  . "</td>";
			$html .= "<td>" . $UsuarioRegistro  . "</td>";
			$html .= "<td>" . $UsuarioRegistroInvitacion  . "</td>";
			$html .= "<td>" . $FechaUsuarioRegistroInvitacion  . "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";

		//construimos el excel
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $nombre_archivo . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $html;
		exit();
        }
