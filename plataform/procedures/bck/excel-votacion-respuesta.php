<?php

	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select IDSocio,P.IDPregunta From PreguntaVotacion P,VotacionRespuesta ER Where ER.IDPregunta=P.IDPregunta and P.IDVotacion = '".$_GET["IDVotacion"]."' Group by IDSocio";
	$result_reporte= $dbo->query( $sql_reporte );

	$datos_encuesta=$dbo->fetchAll( "Votacion", " IDVotacion = '" . $_GET["IDVotacion"] . "' ", "array" );

	$nombre = "Registros_Corte:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='7'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
    $html .= "<th>VOTACION</th>";
		$html .= "<th>VOTANTE</th>";
	    //Consulto los campos dinamicos
	     $r_campos =& $dbo->all( "PreguntaVotacion" , "IDVotacion = '" . $_GET["IDVotacion"]  ."' Order by IDPregunta");
	    while( $r = $dbo->object( $r_campos ) ):
		   $array_preguntas[] = $r->IDPregunta;
		  $html .= "<th>".$r->EtiquetaCampo."</th>";
	    endwhile;
		$html .= "<th>Peso Voto</th>";
		$html .= "<th>Dispositivo</th>";
		$html .= "<th>IP</th>";
		$html .= "<th>FECHA</th>";


		$html .= "</tr>";


		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			unset($array_respuesta_socio);
			$Fecha="";



			if($datos_encuesta["DirigidoA"]=="E"){
				$NombreResponde= utf8_encode($dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$Datos[IDSocio]."'" ));
			}
			else{
				$NombreResponde= utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$Datos[IDSocio]."'" )." ".$dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$Datos[IDSocio]."'" ));
			}

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields( "Votacion" , "Nombre" , "IDVotacion = '".$_GET["IDVotacion"]."'" ))) ."</td>";
			$html .= "<td>" . $NombreResponde ."</td>";

			$sql_repuesta_socio="Select * From VotacionRespuesta Where IDSocio = '".$Datos[IDSocio]."' Group by IDPregunta";
			$r_respuesta_socio=$dbo->query($sql_repuesta_socio);
			while($row_respuesta = $dbo->fetchArray($r_respuesta_socio)):
				$array_respuesta_socio[$row_respuesta["IDPregunta"]]=$row_respuesta["Valor"];
				$Fecha=$row_respuesta["FechaTrCr"];
				$IP=$row_respuesta["IP"];
				$Dispositivo=$row_respuesta["Dispositivo"];
				$PesoVoto=$row_respuesta["PesoVoto"];
			endwhile;
			  if(count($array_preguntas)>0):
				foreach($array_preguntas as $id_pregunta):
					if($datos_encuesta["IDClub"]==23){
						$html .= "<td>privado</td>";
					}
					else {
						$html .= "<td>" .  $array_respuesta_socio[$id_pregunta]   . "</td>";
					}
					//$html .= "<td>" .  $array_respuesta_socio[$id_pregunta]   . "</td>";
					//$html .= "<td>privado</td>";
				endforeach;
			endif;

			$html .= "<td>" . $PesoVoto . "</td>";
			$html .= "<td>" . $IP . "</td>";
			$html .= "<td>" . $Dispositivo . "</td>";
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
