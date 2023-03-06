<?php

	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	if(!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])){
		$condicion_fecha = " and DR.FechaTrCr >= '".$_POST["FechaInicio"]." 00:00:00'  and DR.FechaTrCr <= '".$_POST["FechaFin"]." 23:59:59'";
	}

	/*
	$sql_reporte = "SELECT IDUsuario,TipoUsuario,PD.IDPreguntaDiagnostico,DR.FechaTrCr
									FROM PreguntaDiagnostico PD,DiagnosticoRespuesta DR
									Where PD.IDPreguntaDiagnostico=DR.IDPreguntaDiagnostico and PD.IDDiagnostico = '".$_POST["IDDiagnostico"]."' and TipoUsuario = 'Funcionario' ".$condicion_fecha."
									Group by IDUsuario,DR.FechaTrCr";
	*/

	$sql_reporte = "SELECT IDUsuario,TipoUsuario,PD.IDPreguntaDiagnostico,DR.FechaTrCr
									FROM PreguntaDiagnostico PD,DiagnosticoRespuesta DR
									Where PD.IDPreguntaDiagnostico=DR.IDPreguntaDiagnostico and PD.IDDiagnostico = '".$_POST["IDDiagnostico"]."' and TipoUsuario = 'Funcionario' ".$condicion_fecha."
									Group by IDUsuario,DATE_FORMAT(DR.FechaTrCr, '%y-%m-%d')";
	
	$result_reporte= $dbo->query( $sql_reporte );

	// $datos_encuesta=$dbo->fetchAll( "Diagnostico", " IDDiagnostico = '" . $_POST["IDDiagnostico"] . "' ", "array" );	

	$nombre = "Registros_Corte:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>DIAGNOSTICO</th>";
		$html .= "<th>CEDULA</th>";
		$html .= "<th>USUARIO</th>";
		$html .= "<th>Tipo</th>";
		$html .= "<th>Cargo</th>";
    	//Consulto los campos dinamicos
     	// $r_campos =& $dbo->all( "PreguntaDiagnostico" , "IDDiagnostico = '" . $_POST["IDDiagnostico"]  ."' AND Publicar = 'S' Order by IDPreguntaDiagnostico");
		
		$sqlCampos = "SELECT IDPreguntaDiagnostico, EtiquetaCampo FROM PreguntaDiagnostico WHERE IDDiagnostico = '" . $_POST["IDDiagnostico"] . "' AND Publicar = 'S' ORDER BY IDPreguntaDiagnostico";
		$qryCampos = $dbo->query($sqlCampos);

		while( $r = $dbo->fetchArray($qryCampos) ):
			$array_preguntas[] = $r["IDPreguntaDiagnostico"];
		   	$html .= "<th>".utf8_decode($r["EtiquetaCampo"])."</th>";
	 	endwhile;

		// while( $r = $dbo->object( $r_campos ) ):
	   	// 	$array_preguntas[] = $r->IDPreguntaDiagnostico;
	  	// 	$html .= "<th>".$r->EtiquetaCampo."</th>";
		// endwhile;
	
		$html .= "<th>FECHA</th>";
		$html .= "</tr>";

		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			unset($array_respuesta_socio);
			$Fecha="";

			$datos_usuario=$dbo->fetchAll( "Usuario", " IDUsuario = '" . $Datos[IDUsuario] . "' ", "array" );
			$NombreResponde= utf8_encode($datos_usuario["Nombre"]);

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields( "Diagnostico" , "Nombre" , "IDDiagnostico = '".$_POST["IDDiagnostico"]."'" ))) ."</td>";
			$html .= "<td>" . $datos_usuario["NumeroDocumento"] ."</td>";
			$html .= "<td>" . $NombreResponde ."</td>";
			$html .= "<td>" . $datos_usuario["Tipo"] ."</td>";
			$html .= "<td>" . $datos_usuario["Cargo"] ."</td>";
			//$sql_repuesta_socio="Select * From DiagnosticoRespuesta DR Where IDUsuario = '".$Datos[IDUsuario]."' and FechaTrCr = '".$Datos["FechaTrCr"]."'  Group by IDPreguntaDiagnostico";
			$FechaIniCon=substr($Datos["FechaTrCr"],0,10)." " . "00:00:00";
			$FechaFinCon=substr($Datos["FechaTrCr"],0,10)." " . "23:59:59";
			$sql_repuesta_socio = "SELECT IDPreguntaDiagnostico, Valor, FechaTrCr From DiagnosticoRespuesta DR
											Where IDUsuario = '".$Datos[IDUsuario]."' and
											FechaTrCr >= '".$FechaIniCon."' and
											FechaTrCr <= '".$FechaFinCon."'
											Group by IDPreguntaDiagnostico";			

			//echo $sql_repuesta_socio="Select * From DiagnosticoRespuesta DR Where IDUsuario = '".$Datos[IDUsuario]."' ".$condicion_fecha."  Group by IDPreguntaDiagnostico";
			$r_respuesta_socio=$dbo->query($sql_repuesta_socio);
			while($row_respuesta = $dbo->fetchArray($r_respuesta_socio)):
				$array_respuesta_socio[$row_respuesta["IDPreguntaDiagnostico"]] = $row_respuesta["Valor"];
				$Fecha = $row_respuesta["FechaTrCr"];
			endwhile;
			if(count($array_preguntas)>0):
				foreach($array_preguntas as $id_pregunta):
					$html .= "<td>" .  $array_respuesta_socio[$id_pregunta]   . "</td>";
				endforeach;
			endif;

			$html .= "<td>" . $Fecha . "</td>";
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
		else{
			echo " No se encontraron registros";
		}
?>
