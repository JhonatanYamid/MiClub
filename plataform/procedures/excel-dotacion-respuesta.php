<?php

	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}

	$sql_reporte = "Select IDSocio,P.IDPreguntaDotacion From PreguntaDotacion P,DotacionRespuesta ER Where ER.IDPreguntaDotacion=P.IDPreguntaDotacion and P.IDDotacion = '".$_GET["IDDotacion"]."' Group by IDSocio";
	$result_reporte= $dbo->query( $sql_reporte );

	$datos_Dotacion=$dbo->fetchAll( "Dotacion", " IDDotacion = '" . $_GET["IDDotacion"] . "' ", "array" );

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
    $html .= "<th>Dotacion</th>";
		$html .= "<th>DOCUMENTO</th>";
	    $html .= "<th>SOCIO</th>";
	    //Consulto los campos dinamicos
	     $r_campos =& $dbo->all( "PreguntaDotacion" , "IDDotacion = '" . $_GET["IDDotacion"]  ."' Order by IDPreguntaDotacion");
	    while( $r = $dbo->object( $r_campos ) ):
		   $array_PreguntaDotacions[] = $r->IDPreguntaDotacion;
		  $html .= "<th>".$r->EtiquetaCampo."</th>";
	    endwhile;
		$html .= "<th>FECHA</th>";


		$html .= "</tr>";


		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			unset($array_respuesta_socio);
			$Fecha="";



			if($datos_Dotacion["DirigidoA"]=="E"){
				$NombreResponde= utf8_encode($dbo->getFields( "Usuario" , "Nombre" , "IDUsuario = '".$Datos[IDSocio]."'" ));
				$DocumentoResponde= utf8_encode($dbo->getFields( "Usuario" , "NumeroDocumento" , "IDUsuario = '".$Datos[IDSocio]."'" ));
			}
			else{
				$NombreResponde= utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$Datos[IDSocio]."'" )." ".$dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$Datos[IDSocio]."'" ));
				$DocumentoResponde= utf8_encode($dbo->getFields( "Socio" , "NumeroDocumento" , "IDSocio = '".$Datos[IDSocio]."'" ));
			}

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . $dbo->getFields( "Dotacion" , "Nombre" , "IDDotacion = '".$_GET["IDDotacion"]."'" ) ."</td>";
			$html .= "<td>" . $DocumentoResponde ."</td>";
			$html .= "<td>" . $NombreResponde ."</td>";
			$sql_repuesta_socio="Select * From DotacionRespuesta Where IDSocio = '".$Datos[IDSocio]."' Group by IDPreguntaDotacion";
			$r_respuesta_socio=$dbo->query($sql_repuesta_socio);
			while($row_respuesta = $dbo->fetchArray($r_respuesta_socio)):
				$array_respuesta_socio[$row_respuesta["IDPreguntaDotacion"]]=$row_respuesta["Valor"];
				$Fecha=$row_respuesta["FechaTrCr"];
			endwhile;
			  if(count($array_PreguntaDotacions)>0):
				foreach($array_PreguntaDotacions as $id_PreguntaDotacion):
					$html .= "<td>" .  $array_respuesta_socio[$id_PreguntaDotacion]   . "</td>";
				endforeach;
			endif;

			$html .= "<td>" . $Fecha . "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";



		//construimos el excel
		header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
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
	<?php
				}
				else{
					echo " No se encontraron registros";
				}
	?>
