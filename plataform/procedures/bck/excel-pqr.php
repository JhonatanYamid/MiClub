<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	if($_POST["IDPerfil"]>1):
		//Consulto las areas
		$sql_area_usuario="Select * From UsuarioArea Where IDUsuario = '".$_POST["IDUsuario"]."'";
		$result_area_usuario=$dbo->query($sql_area_usuario);
		while($row_area=$dbo->fetchArray($result_area_usuario)):
			$array_areas [] = $row_area["IDArea"];
		endwhile;
		if(count($array_areas)>0):
			$id_areas = implode(",",$array_areas);
		endif;
		$condicion_area = " and Pqr.IDArea in (".$id_areas.")";
	endif;

	if(!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])){
		$condicion_fecha=" and Fecha >= '".$_POST["FechaInicio"]."'  and Fecha <= '".$_POST["FechaFin"]."'";
	}



	$sql_reporte = "Select *
					From Pqr
					Where IDClub = '".$_POST["IDClub"]."'  ".$condicion_area." ".$condicion_fecha." Order By IDPqr DESC" ;

	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "Pqr_Corte:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
        $html .= "<th>NUMERO PQR</th>";
	    $html .= "<th>TIPO</th>";
		$html .= "<th>AREA</th>";
		$html .= "<th>MEDIO</th>";
		$html .= "<th>TIPO SOCIO</th>";
		$html .= "<th>ACCION</th>";
		$html .= "<th>SOCIO</th>";
		$html .= "<th>ESTADO</th>";
		$html .= "<th>ASUNTO</th>";
		$html .= "<th>DESCRIPCION</th>";
		$html .= "<th>FECHA QUEJA</th>";
		$html .= "<th>CALIFICACION</th>";
		$html .= "<th>COMENTARIO CALIFICACION</th>";
		//Consulto columnas del seguimiento
		for($contador_columna =1;$contador_columna<=5;$contador_columna++):
			$html .= "<th>RESPONSABLE SEGUIMIENTO ".$contador_columna."</th>";
			$html .= "<th>FECHA SEGUIMIENTO ".$contador_columna."</th>";
			$html .= "<th>RESPUESTA SEGUIMIENTO ".$contador_columna."</th>";
		endfor;

		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . $Datos["Numero"] ."</td>";
			$html .= "<td>" . $dbo->getFields( "TipoPqr" , "Nombre" , "IDTipoPqr = '" . $Datos["IDTipoPqr"] . "'" ) ."</td>";
			$html .= "<td>" . remplaza_tildes($dbo->getFields( "Area" , "Nombre" , "IDArea = '" . $Datos["IDArea"] . "'" )) . "</td>";
			$html .= "<td>" . remplaza_tildes($dbo->getFields( "PqrMedio" , "Nombre" , "IDPqrMedio = '" . $Datos["IDPqrMedio"] . "'" )) . "</td>";
			$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields( "Socio" , "TipoSocio" , "IDSocio = '".$Datos["IDSocio"]."'")))) . "</td>";
			$html .= "<td>" . $dbo->getFields( "Socio" , "Accion" , "IDSocio = '".$Datos["IDSocio"]."'") . "</td>";
			$html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$Datos["IDSocio"]."'") . " " . $dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$Datos["IDSocio"]."'")) )) . "</td>";
			$html .= "<td>" . utf8_encode($dbo->getFields( "PqrEstado" , "Nombre" , "IDPqrEstado = '" . $Datos["IDPqrEstado"] . "'" ))  . "</td>";
			$html .= "<td>" . remplaza_tildes(utf8_encode( $Datos["Asunto"] ))  . "</td>";
			$html .= "<td>" . remplaza_tildes(utf8_encode($Datos["Descripcion"])) . "</td>";
			$html .= "<td>" . $Datos["Fecha"]   . "</td>";
			if((int)$Datos["Calificacion"]>0)
				$calificacion = $Datos["Calificacion"];
			else
				$calificacion = "";

			$html .= "<td>" . $calificacion   . "</td>";
			$html .= "<td>" . $Datos["ComentarioCalificacion"]   . "</td>";
			//Consulto el seguimiento
			$sql_seguimiento = "Select * From Detalle_Pqr Where IDPqr = '". $Datos["IDPqr"]."'";
			$result_seguimiento = $dbo->query($sql_seguimiento);
			while($row_seguimiento = $dbo->fetchArray($result_seguimiento)):
				$html .= "<td>" . $dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '" . $row_seguimiento["IDUsuario"] . "'" )   . "</td>";
				$html .= "<td>" . $row_seguimiento["Fecha"]   . "</td>";
				$html .= "<td>" . utf8_encode($row_seguimiento["Respuesta"])   . "</td>";
			endwhile;
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
