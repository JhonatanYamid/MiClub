<?php

	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "SELECT IDSocio,P.IDPregunta,ER.FechaTrCr
									From Pregunta P,EncuestaRespuesta ER
									Where ER.IDPregunta=P.IDPregunta and P.IDEncuesta = '".$_GET["IDEncuesta"]."' Group by IDSocio,ER.FechaTrCr";
	$result_reporte= $dbo->query( $sql_reporte );

	$datos_encuesta=$dbo->fetchAll( "Encuesta", " IDEncuesta = '" . $_GET["IDEncuesta"] . "' ", "array" );

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
        $html .= "<th>ENCUESTA</th>";
				$html .= "<th>DOCUMENTO</th>";
				$html .= "<th>ACCION</th>";
	    $html .= "<th>SOCIO</th>";
	    //Consulto los campos dinamicos
	     $r_campos =& $dbo->all( "Pregunta" , "IDEncuesta = '" . $_GET["IDEncuesta"]  ."' Order by IDPregunta");
	    while( $r = $dbo->object( $r_campos ) ):
		   $array_preguntas[] = $r->IDPregunta;
		  $html .= "<th>".$r->EtiquetaCampo."</th>";
	    endwhile;
		$html .= "<th>FECHA</th>";


		$html .= "</tr>";


		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			unset($array_respuesta_socio);
			$Fecha="";

			if($datos_encuesta["DirigidoA"]=="E"){
				$NombreResponde= utf8_encode($dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$Datos[IDSocio]."'" ));
				$DocumentoResponde= utf8_encode($dbo->getFields( "Usuario" , "NumeroDocumento" , "IDUsuario = '".$Datos[IDSocio]."'" ));
				$AccionResponde="";
			}
			else{
				$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos[IDSocio] . "' ", "array" );
				$NombreResponde= $datos_socio["Nombre"]." ".$datos_socio["Apellido"];
				$DocumentoResponde= $datos_socio["NumeroDocumento"];
				$AccionResponde= $datos_socio["Accion"];

			}

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields( "Encuesta" , "Nombre" , "IDEncuesta = '".$_GET["IDEncuesta"]."'" ))) ."</td>";
			$html .= "<td>" . $AccionResponde ."</td>";
			$html .= "<td>" . $DocumentoResponde ."</td>";
			$html .= "<td>" . $NombreResponde ."</td>";
			$sql_repuesta_socio="Select * From EncuestaRespuesta Where IDSocio = '".$Datos[IDSocio]."' and FechaTrCr = '".$Datos[FechaTrCr]."' Group by IDPregunta";
			$r_respuesta_socio=$dbo->query($sql_repuesta_socio);
			while($row_respuesta = $dbo->fetchArray($r_respuesta_socio)):
				$array_respuesta_socio[$row_respuesta["IDPregunta"]]=$row_respuesta["Valor"];
				$Fecha=$row_respuesta["FechaTrCr"];
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
?>
