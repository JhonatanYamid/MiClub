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


	if(!empty($_GET["Socio"])){
		$sql_busq = "SELECT * From Socio Where (Nombre LIKE '%" . $_GET["Socio"] . "%' or apellido like '%".$_GET["Socio"]."%') and IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_socio[]=$row_busq["IDSocio"];
		endwhile;
		if(count($array_id_socio)>0):
			$id_socios_buscar = implode(",",$array_id_socio);
		else:
			$id_socios_buscar = 0;
		endif;
		$where .= " AND   IDSocio in (".$id_socios_buscar.")";

	}

	if(!empty($_GET["Documento"])){
		$sql_busq = "SELECT * From Socio Where NumeroDocumento = '" . $_GET["Documento"] . "' and IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_socio[]=$row_busq["IDSocio"];
		endwhile;
		if(count($array_id_socio)>0):
			$id_socios_buscar = implode(",",$array_id_socio);
		else:
			$id_socios_buscar = 0;
		endif;
		$where .= " AND   IDSocio in (".$id_socios_buscar.")";

	}

	if(!empty($_GET["IDCursoNivel"])){
		$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoNivel CN, CursoHorario CH
								 Where CN.IDCursoNivel=CH.IDCursoNivel
								 AND CN.IDCursoNivel = '".$_GET["IDCursoNivel"]."' and CH.IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_horario[]=$row_busq["IDCursoHorario"];
		endwhile;
		if(count($array_id_horario)>0):
			$id_horario_buscar = implode(",",$array_id_horario);
		else:
			$id_horario_buscar = 0;
		endif;
		$where .= " AND   IDCursoHorario in (".$id_horario_buscar.")";

	}

	if(!empty($_GET["Curso"])){
		$sql_busq = "SELECT * From CursoHorario Where Nombre LIKE '%" . $_GET["Curso"] . "%' and IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_horario[]=$row_busq["IDCursoHorario"];
		endwhile;
		if(count($array_id_horario)>0):
			$id_horario_buscar = implode(",",$array_id_horario);
		else:
			$id_horario_buscar = 0;
		endif;
		$where .= " AND   IDCursoHorario in (".$id_horario_buscar.")";
	}

	if(!empty($_GET["IDCursoSede"])){
		$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoNivel CN, CursoHorario CH
								 Where CN.IDCursoNivel=CH.IDCursoNivel
								 AND CN.IDCursoSede = '".$_GET["IDCursoSede"]."' and CH.IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_horario[]=$row_busq["IDCursoHorario"];
		endwhile;
		if(count($array_id_horario)>0):
			$id_horario_buscar = implode(",",$array_id_horario);
		else:
			$id_horario_buscar = 0;
		endif;
		$where .= " AND   IDCursoHorario in (".$id_horario_buscar.")";

	}

	if(!empty($_GET["IDCursoTipo"])){
		$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoTipo CT, CursoHorario CH
								 Where CT.IDCursoTipo=CH.IDCursoTipo
								 AND CT.IDCursoTipo = '".$_GET["IDCursoTipo"]."' and CH.IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_horario[]=$row_busq["IDCursoHorario"];
		endwhile;
		if(count($array_id_horario)>0):
			$id_horario_buscar = implode(",",$array_id_horario);
		else:
			$id_horario_buscar = 0;
		endif;
		$where .= " AND   IDCursoHorario in (".$id_horario_buscar.")";

	}

	if(!empty($_GET["FechaInicio"])){
		$sql_busq = "SELECT DISTINCT(IDCursoCalendario) From CursoCalendario CC
								 Where CC.FechaInicio = '".$_GET["FechaInicio"]."' and CC.IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_calendario[]=$row_busq["IDCursoCalendario"];
		endwhile;
		if(count($array_id_calendario)>0):
			$id_calendario_buscar = implode(",",$array_id_calendario);
		else:
			$id_calendario_buscar = 0;
		endif;
		$where .= " AND   IDCursoCalendario in (".$id_calendario_buscar.")";
	}

	if(!empty($_GET["Hora"])){
		$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoHorario CH
								 Where CH.HoraDesde = '".$_GET["Hora"]."' and CH.IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_horario[]=$row_busq["IDCursoHorario"];
		endwhile;
		if(count($array_id_horario)>0):
			$id_horario_buscar = implode(",",$array_id_horario);
		else:
			$id_horario_buscar = 0;
		endif;
		$where .= " AND   IDCursoHorario in (".$id_horario_buscar.")";
	}

	if(!empty($_GET["IDCursoEntrenador"])){
		$sql_busq = "SELECT DISTINCT(IDCursoHorario) From CursoEntrenador CE, CursoHorario CH
								 Where CE.IDCursoEntrenador=CH.IDCursoEntrenador
								AND CE.IDCursoEntrenador = '".$_GET["IDCursoEntrenador"]."' and CH.IDClub = '".$_GET["IDClub"]."'";
		$result_busq = $dbo->query($sql_busq);
		while($row_busq = $dbo->fetchArray($result_busq)):
			$array_id_horario[]=$row_busq["IDCursoHorario"];
		endwhile;
		if(count($array_id_horario)>0):
			$id_horario_buscar = implode(",",$array_id_horario);
		else:
			$id_horario_buscar = 0;
		endif;
		$where .= " AND   IDCursoHorario in (".$id_horario_buscar.")";
	}


	$sql_entrenador="SELECT * FROM CursoEntrenador WHERE IDClub = '".$_GET["IDClub"]."'";
	$r_entrenador=$dbo->query($sql_entrenador);
	while($row_entrenador=$dbo->fetchArray($r_entrenador)){
		$array_entrenador[$row_entrenador["IDCursoEntrenador"]]=$row_entrenador["Nombre"];
	}

	$sql_nivel="SELECT * FROM CursoNivel WHERE IDClub = '".$_GET["IDClub"]."'";
	$r_nivel=$dbo->query($sql_nivel);
	while($row_nivel=$dbo->fetchArray($r_nivel)){
		$array_nivel[$row_nivel["IDCursoNivel"]]=$row_nivel["Nombre"];
	}

	$sql_edad="SELECT * FROM CursoEdad WHERE IDClub = '".$_GET["IDClub"]."'";
	$r_edad=$dbo->query($sql_edad);
	while($row_edad=$dbo->fetchArray($r_edad)){
		$array_edad[$row_edad["IDCursoEdad"]]=$row_edad["Nombre"];
	}

	$sql_sede="SELECT * FROM CursoSede WHERE IDClub = '".$_GET["IDClub"]."'";
	$r_sede=$dbo->query($sql_sede);
	while($row_sede=$dbo->fetchArray($r_sede)){
		$array_sede[$row_sede["IDCursoSede"]]=$row_sede["Nombre"];
	}

	$sql_tipo="SELECT * FROM CursoTipo WHERE IDClub = '".$_GET["IDClub"]."'";
	$r_tipo=$dbo->query($sql_tipo);
	while($row_tipo=$dbo->fetchArray($r_tipo)){
		$array_tipo[$row_tipo["IDCursoTipo"]]=$row_tipo["Nombre"];
	}

	$sql_horario="SELECT * FROM CursoHorario WHERE IDClub = '".$_GET["IDClub"]."'";
	$r_horario=$dbo->query($sql_horario);
	while($row_horario=$dbo->fetchArray($r_horario)){
		$array_horario[$row_horario["IDCursoHorario"]]=$row_horario;
	}

	$sql_calendario="SELECT * FROM CursoCalendario WHERE IDClub = '".$_GET["IDClub"]."'";
	$r_calendario=$dbo->query($sql_calendario);
	while($row_calendario=$dbo->fetchArray($r_calendario)){
		$array_calendario[$row_calendario["IDCursoCalendario"]]=$row_calendario;
	}



	$sql_reporte = "Select *
					From CursoInscripcion
					Where IDClub = '".$_GET["IDClub"]."'  ".$where." "." Order By IDCursoInscripcion DESC" ;

	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "CursoInscripcion_Corte:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>NUMERO REFERENCIA</th>";
		$html .= "<th>Documento Socio</th>";
		$html .= "<th>Socio</th>";
    $html .= "<th>Curso</th>";
	  $html .= "<th>Nivel</th>";
		$html .= "<th>Sede</th>";
		$html .= "<th>Dia</th>";
		$html .= "<th>Fecha Inicio</th>";
		$html .= "<th>Fecha Fin</th>";
		$html .= "<th>Hora</th>";
		$html .= "<th>Entrenador</th>";
		$html .= "<th>Creado por</th>";
		$html .= "<th>Estado Inscripcion</th>";
		$html .= "<th>Estado Transaccion</th>";
		$html .= "<th>Fecha Transaccion</th>";
		$html .= "<th>Codigo Respuesta</th>";
		$html .= "<th>Medio Pago</th>";
		$html .= "<th>Tipo Medio Pago</th>";
		$html .= "<th>Pagado</th>";
		$html .= "<th>Pago Payu</th>";
		$html .= "<th>Valor</th>";
		$html .= "<th>Observaciones</th>";
		$html .= "<th>Usuario Creacion</th>";
		$html .= "<th>Fecha Creacion</th>";
		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos["IDSocio"] . "' ", "array" );

			$html .= "<tr>";
			$html .= "<td>" . $Datos["IDCursoInscripcion"] ."</td>";
			$html .= "<td>" . $datos_socio["NumeroDocumento"] ."</td>";
			$html .= "<td>" . $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] ."</td>";
			$html .= "<td>" . $array_horario[$Datos["IDCursoHorario"]]["Nombre"] ."</td>";
			$html .= "<td>" . $array_nivel[$array_horario[$Datos["IDCursoHorario"]]["IDCursoNivel"]] . "</td>";
			$html .= "<td>" . $array_sede[$array_horario[$Datos["IDCursoHorario"]]["IDCursoSede"]]  . "</td>";
			$html .= "<td>" . $array_tipo[$array_horario[$Datos["IDCursoHorario"]]["IDCursoTipo"]]   . "</td>";
			$html .= "<td>" . $array_calendario[$Datos["IDCursoCalendario"]]["FechaInicio"]   . "</td>";
			$html .= "<td>" . $array_calendario[$Datos["IDCursoCalendario"]]["FechaFin"]   . "</td>";
			$html .= "<td>" . $array_horario[$Datos["IDCursoHorario"]]["HoraDesde"]   . "</td>";
			$html .= "<td>" . $array_entrenador[$array_horario[$Datos["IDCursoHorario"]]["IDCursoEntrenador"]]   . "</td>";
			$html .= "<td>" . utf8_decode($dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$Datos["IDUsuarioInscribe"]."'" ))   . "</td>";
			$html .= "<td>" . $Datos["EstadoInscripcion"]   . "</td>";
			$html .= "<td>" . $Datos["EstadoTransaccion"]   . "</td>";
			$html .= "<td>" . $Datos["FechaTransaccion"]   . "</td>";
			$html .= "<td>" . $Datos["CodigoRespuesta"]   . "</td>";
			$html .= "<td>" . $Datos["MedioPago"]   . "</td>";
			$html .= "<td>" . $Datos["TipoMedioPago"]   . "</td>";
			$html .= "<td>" . $Datos["Pagado"]   . "</td>";
			$html .= "<td>" . $Datos["PagoPayu"]   . "</td>";
			$html .= "<td>" . $Datos["Valor"]   . "</td>";
			$html .= "<td>" . $Datos["Observaciones"]   . "</td>";
			$html .= "<td>" . $Datos["UsuarioTrCr"]   . "</td>";
			$html .= "<td>" . $Datos["FechaTrCr"]   . "</td>";
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
