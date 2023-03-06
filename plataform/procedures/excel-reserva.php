<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );
	if(!empty($_GET["FechaInicio"]) && !empty($_GET["FechaFin"]) ){
		$condiciones = " and Fecha>='".$_GET["FechaInicio"]."' and Fecha <= '".$_GET["FechaFin"]."'";
	}
	if(!empty($_GET["NumeroDocumento"])){
		$condiciones = " and S.NumeroDocumento ='".$_GET["NumeroDocumento"]."'";
	}
	if(!empty($_GET["Accion"])){
		$condiciones = " and S.Accion ='".$_GET["Accion"]."'";
	}


	 $sql_reporte = "Select ReservaGeneral.*,  S.NumeroDocumento, S.IDSocio, S.Nombre, S.Apellido,S.Email, S.Accion, S.TipoSocio, CONCAT( S.Nombre, ' ', S.Apellido ) AS Socio
					From ReservaGeneral, Socio S
					Where ReservaGeneral.IDClub = '".$_GET["IDClub"]."' and IDEstadoReserva = 1 and ReservaGeneral.IDSocio = S.IDSocio ".$condiciones."
					UNION
					Select ReservaGeneralBck.*,  S.NumeroDocumento, S.IDSocio, S.Nombre, S.Apellido,S.Email, S.Accion, S.TipoSocio, CONCAT( S.Nombre, ' ', S.Apellido ) AS Socio
					From ReservaGeneralBck, Socio S
					Where ReservaGeneralBck.IDClub = '".$_GET["IDClub"]."' and IDEstadoReserva = 1 and ReservaGeneralBck.IDSocio = S.IDSocio ".$condiciones."
					Order By IDReservaGeneral DESC" ;


	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "Reservas_Corte:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
        $html .= "<th>SERVICIO</th>";
		if($_GET["IDClub"] == 70)
		{
			$html .= "<th>CONSECUTIVO SERVICIO</th>";
		}
		$html .= "<th>NUMERO DERECHO</th>";
		$html .= "<th>DOCUMENTO</th>";
		$html .= "<th>NOMBRE</th>";
		$html .= "<th>APELLIDO</th>";
		$html .= "<th>EMAIL</th>";
		$html .= "<th>FECHA CREACION RESERVA</th>";
		$html .= "<th>FECHA RESERVA</th>";
		$html .= "<th>HORA RESERVA</th>";
		$html .= "<th>CREADA POR</th>";
		$html .= "<th>INVITADOS</th>";

		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			$html .= "<tr>";
			$id_servicio_maestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $Datos["IDServicio"] . "'" );
			$nombre_servicio_maestro = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $id_servicio_maestro. "'" );

			$nombre_servicio_personalizado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDClub = '".$Datos["IDClub"]."' and IDServicioMaestro = '" . $id_servicio_maestro . "'" );
			if(!empty($nombre_servicio_personalizado))
				$nombre_servicio_maestro = $nombre_servicio_personalizado;



			$html .= "<td>" . $nombre_servicio_maestro ."</td>";
			if($_GET["IDClub"] == 70)
			{
				$html .= "<th>".$Datos[ConsecutivoServicio]."</th>";
			}
			$html .= "<td>" . $Datos["Accion"] . "</td>";
			$html .= "<td>" . $Datos["NumeroDocumento"] . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Nombre"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Apellido"] )  . "</td>";
			$html .= "<td>" . utf8_encode( $Datos["Email"] )  . "</td>";
			$html .= "<td>" . $Datos["FechaTrCr"]   . "</td>";
			$html .= "<td>" . $Datos["Fecha"]   . "</td>";
			$html .= "<td>" . $Datos["Hora"]   . "</td>";


			if($Datos["UsuarioTrCr"]=="Starter" || $Datos["UsuarioTrCr"]=="Empleado"):
				$creada_por =  $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" . $Datos["IDUsuarioReserva"] . "'" );
			else:
				$creada_por =  $Datos["UsuarioTrCr"];
			endif;

			$html .= "<td>" . $creada_por  . "</td>";
			//Invitados
			$datos_invitado = "";
			$sql_invitado = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$Datos["IDReservaGeneral"]."'";
			$result_invitado = $dbo->query($sql_invitado);
			while($row_invitado = $dbo->fetchArray($result_invitado)):
				if(!empty($row_invitado["IDSocio"]))
					$tipo_invitado = "Socio: ";
				else
					$tipo_invitado = "Externo: ";

				$nom_invitado = 	$row_invitado["Nombre"];

				if(!empty($row_invitado["IDSocio"]) && empty($row_invitado["Nombre"])):
					$datos_socio_invi = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos["IDSocio"] . "' ", "array" );
					$nom_invitado = $datos_socio_invi["Nombre"] . " " . $datos_socio_invi["Apellido"] . $datos_socio_invi["NumeroDocumento"];
				endif;



				$datos_invitado .= $tipo_invitado . " " .strtoupper($nom_invitado) . "<br>" . PHP_EOL;
			endwhile;
			$html .= "<td>" . $datos_invitado  . "</td>";
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
