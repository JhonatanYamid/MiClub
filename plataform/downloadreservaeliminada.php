<?php
//Script para exportar reporte de contactos por rango de fechas
require( dirname( __FILE__ ) . "/../admin/config.inc.php" );


		if($_GET["IDServicio"]=="T"): //Se quiere consultar todos los servicios
			$condicion_servicio="";
		else:
			$condicion_servicio=" and IDServicio = '".$_GET["IDServicio"]."' ";
		endif;


		$sql = "Select * From ReservaGeneralEliminada Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' ".$condicion_servicio."	
				UNION
				Select * From ReservaGeneralEliminadaBck Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' ".$condicion_servicio." ";
		
		$nombre = "ReservasEliminadas" . date( "Y_m_d H:i:s" );

		$qry = $dbo->query( $sql );
		$Num=$dbo->rows( $qry );

		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='6'>Se encontraron " . $Num . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>SERVICIO</th>";
		if($_GET["IDClub"] == 70)
		{
			$html .= "<th>CONSECUTIVO SERVICIO</th>";
		}
		$html .= "<th>NUMERO ACCION</th>";
		$html .= "<th>TIPO SOCIO</th>";
		$html .= "<th>SOCIO</th>";
		$html .= "<th>USUARIO</th>";
		$html .= "<th>BENEFICIARIO</th>";
		$html .= "<th>BOLEADOR</th>";
		$html .= "<th>TEE</th>";
		$html .= "<th>MODALIDAD</th>";
		$html .= "<th>TIPO RESERVA</th>";
		$html .= "<th>PAX</th>";
		$html .= "<th>ELEMENTO</th>";
		$html .= "<th>FECHA CREACION RESERVA</th>";
		$html .= "<th>FECHA RESERVA</th>";
		$html .= "<th>HORA</th>";
		$html .= "<th>INVITADOS</th>";
		$html .= "<th>OBSERVACIONES</th>";
		$html .= "<th>CREADA POR</th>";
		$html .= "<th>COMENTARIO STARTER</th>";
		$html .= "<th>CUMPLIDA SOCIO</th>";
		$html .= "<th>TIPO PAGO</th>";
		$html .= "<th>CODIGO</th>";
		$html .= "<th>ESTADO TRANSACCION</th>";
		$html .= "<th>MEDIO PAGO</th>";
		$html .= "<th>PAGO CONFIRMADO</th>";
		$html .= "<th>VALOR PAGADO</th>";
		$html .= "<th>CANCHA</th>";
		$html .= "<th>EQUIPO</th>";
		$html .= "<th>RAZON ELIMINACION</th>";
		$html .= "<th>FECHA ELIMINACION</th>";
		$html .= "<th>ELIMINADA SUPERANDO EL MAXIMO DE HORA PERMITIDO?</th>";
		$html .= "<th>SE OCUPO EL TURNO?</th>";
		$html .= "</tr>";
		$item=0;
		while( $row = $dbo->fetchArray( $qry,$a ) )
		{
			$html .= "<tr>";
			$IDServicioMaestro = $dbo->getFields( "Servicio" , "IDServicioMaestro" , "IDServicio = '" . $row["IDServicio"] . "'");
			$NombreServicio = $dbo->getFields( "ServicioMaestro" , "Nombre" , "IDServicioMaestro = '" . $IDServicioMaestro. "'");
			$NombreServicioPersonalidado = $dbo->getFields( "ServicioClub" , "TituloServicio" , "IDServicioMaestro = '" . $IDServicioMaestro. "' and IDClub = '".$row["IDClub"]."'");

			if(!empty($NombreServicioPersonalidado))
				$Servicio = $NombreServicioPersonalidado;
			else
				$Servicio = $NombreServicio;


			$html .= "<td>".$Servicio."</td>";
			if($_GET["IDClub"] == 70)
			{
				$html .= "<th>".$row[ConsecutivoServicio]."</th>";
			}
			$html .= "<td>".$dbo->getFields( "Socio" , "Accion" , "IDSocio = '" . $row["IDSocio"] . "'")."</td>";
			$html .= "<td>".$dbo->getFields( "Socio" , "TipoSocio" , "IDSocio = '" . $row["IDSocio"] . "'")."</td>";
			$html .= "<td>".$dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $row["IDSocio"] . "'"). " ". $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $row["IDSocio"] . "'")."</td>";
			$html .= "<td>".$dbo->getFields( "Socio" , "Email" , "IDSocio = '" . $row["IDSocio"] . "'")."</td>";
			$html .= "<td>".$dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $row["IDSocioBeneficiario"] . "'"). " ". $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $row["IDSocioBeneficiario"] . "'")."</td>";
			$array_auxiliar = explode(",",$row["IDAuxiliar"]);

			unset($array_nom_auxiliar);
			if(count($array_auxiliar)>0):
				foreach ($array_auxiliar as $id_auxiliar):
					$array_nom_auxiliar[]=$dbo->getFields( "Auxiliar" , "Nombre" , "IDAuxiliar = '" . $id_auxiliar . "'");
				endforeach;
			endif;
			if(count($array_nom_auxiliar)>0):
				$auxiliares = implode(",",$array_nom_auxiliar);
			endif;

			$html .= "<td>".$auxiliares."</td>";
			$html .= "<td>".strtoupper($row["Tee"])."</td>";
			$html .= "<td>".$dbo->getFields( "TipoModalidadEsqui" , "Nombre" , "IDTipoModalidadEsqui = '" . $row["IDTipoModalidadEsqui"] . "'")."</td>";
			$html .= "<td>".$dbo->getFields( "ServicioTipoReserva" , "Nombre" , "IDServicioTipoReserva = '" . $row["IDServicioTipoReserva"] . "'")."</td>";
			$html .= "<td>".$row["CantidadInvitadoSalon"]."</td>";
			$html .= "<td>".strtoupper($dbo->getFields( "ServicioElemento" , "Nombre" , "IDServicioElemento = '" . $row["IDServicioElemento"] . "'"))."</td>";
			$html .= "<td>".strtoupper($row["FechaTrCr"])."</td>";
			$html .= "<td>".strtoupper($row["Fecha"])."</td>";
			$html .= "<td>".strtoupper($row["Hora"])."</td>";
			//Invitados
			$datos_invitado = "";
			$sql_invitado = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$row["IDReservaGeneral"]."'";
			$result_invitado = $dbo->query($sql_invitado);
			while($row_invitado = $dbo->fetchArray($result_invitado)):
				if(empty($row_invitado["IDSocio"]))
					$tipo_invitado = "Socio: ";
				else
					$tipo_invitado = "Externo: ";

				$nom_invitado = 	$row_invitado["Nombre"];

				if(!empty($row_invitado["IDSocio"]) && empty($row_invitado["Nombre"])):
					$nom_invitado = $dbo->getFields( "Socio" , "Nombre" , "IDSocio = '" . $row_invitado["IDSocio"] . "'" ) . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '" . $row_invitado["IDSocio"] . "'" );
				endif;
				$datos_invitado .= $tipo_invitado . " " .strtoupper($nom_invitado) . "<br>" . PHP_EOL;
			endwhile;
			$html .= "<td>".$datos_invitado."</td>";
			$html .= "<td>".strtoupper($row["Observaciones"])."</td>";

			if($row["UsuarioTrCr"]=="Starter" || $row["UsuarioTrCr"]=="Empleado"):
				$creada_por =  $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" . $row["IDUsuarioReserva"] . "'" );
			else:
				$creada_por =  $row["UsuarioTrCr"];
			endif;

			$html .= "<td>" . $creada_por  . "</td>";
			$html .= "<td>" . $row["ObservacionCumplida"]  . "</td>";
			$html .= "<td>" . $row["Cumplida"]  . "</td>";
			//Pago
			$html .= "<td>" . $dbo->getFields( "TipoPago" , "Nombre" , "IDTipoPago = '" . $row["IDTipoPago"] . "'" )  . "</td>";
			
			if($row["IDClub"] == 106)
				$html .= "<td>" . $dbo->getFields( "ZonaPagosPagos" , "IDZonaPagosPagos" , "IDReserva = '" . $row["IDReservaGeneral"] . "'" )  . "</td>";
			else
				$html .= "<td>" . $row["CodigoPago"]  . "</td>";

			$html .= "<td>" . $dbo->getFields( "PAYUEstadoTransaccion" , "Descripcion" , "Estado_Pol = '" . $row["EstadoTransaccion"] . "'" )  . "</td>";
			$html .= "<td>" . $dbo->getFields( "PAYUMediosPago" , "Descripcion" , "Medio_Pago = '" . $row["MedioPago"] . "'" )  . "</td>";
			if($row["IDClub"] == 106)
				$html .= "<td>" . $dbo->getFields( "ZonaPagosPagos" , "ValorPagado" , "IDReserva = '" . $row["IDReservaGeneral"] . "'" )  . "</td>";
			else
				$html .= "<td>" . $row["Pagado"]  . "</td>";

			$html .= "<td>" . $dbo->getFields( "PagosWeb" , "value" , "extra1 = '" . $row["IDReservaGeneral"] . "'" )  . "</td>";
			$html .= "<td>" . $row["Cancha"]  . "</td>";
			$html .= "<td>" . $row["Equipo"]  . "</td>";

			if($row["Razon"]=="")
				$Razon="Eliminada por Socio";
			else
				$Razon=$row["Razon"];

			$html .= "<td>" . $Razon  . "</td>";
			$html .= "<td>" . $row["FechaTrEd"]  . "</td>";
			$html .= "<td>" . $row["EliminadaFueraTiempo"]  . "</td>";

			$mensaje_reserva="";
			if($row["EliminadaFueraTiempo"]=="S"){
			//Verifico si la reserva fue tomada por alguien mas para genarar si se debe o no cobrar por turno no ocupado
			$IDReservaTomada=$dbo->getFields( "ReservaGeneral" , "IDReservaGeneral" , " IDClub = '".$row["IDClub"]."' and IDServicio='".$row["IDServicio"]."' and IDServicioElemento = '".$row["IDServicioElemento"]."' and IDEstadoReserva = 1 ");
			if((int)$IDReservaTomada>0)
				$mensaje_reserva="<font style='color: #f60606'>TURNO NO OCUPADO POR NINGUNA OTRA PERSONA</font>";
			}



			$html .= "<td>" . $mensaje_reserva  . "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";

		//construimos el excel
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		?>
		<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
	<?php
	echo $html;
	exit();
	?>
	</body>
	</html>	

?>
