<?php

	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select *
					From EventoRegistro
					Where IDEvento = '".$_GET["IDEvento"]."'  Order By IDEventoRegistro DESC" ;
	$result_reporte= $dbo->query( $sql_reporte );

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
    $html .= "<th>Evento</th>";
	  $html .= "<th>SOCIO</th>";
		$html .= "<th>ACCION</th>";
		 $html .= "<th>CORREO</th>";
		$html .= "<th>BENEFICIARIO</th>";
		$html .= "<th>VALOR</th>";
		$html .= "<th>CODIGO PAGO</th>";
		$html .= "<th>ESTADO TRANSACCION</th>";
		$html .= "<th>CODIGO RESPUESTA</th>";

	    //Consulto los campos dinamicos
	    $r_campos =& $dbo->all( "CampoFormularioEvento" , "IDEvento = '" . $_GET["IDEvento"]  ."'");
	    while( $r = $dbo->object( $r_campos ) ):
		  $array_campos[] = $r->IDCampoFormularioEvento;
		  $html .= "<th>".$r->EtiquetaCampo."</th>";
	    endwhile;

			//Especial lagartos
			if($_GET["IDEvento"]==3043){
				 $html .= "<th>MEDIO DE PAGO</th>";
			}


		$html .= "</tr>";
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $Datos[IDSocio] . "' ", "array" );
			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields( "Evento" , "Titular" , "IDEvento = '".$Datos["IDEvento"]."'" ))) ."</td>";
			$html .= "<td>" . utf8_encode($datos_socio["Nombre"]." ".$datos_socio["Apellido"]) ."</td>";
			$html .= "<td>" . utf8_encode($datos_socio["Accion"])."</td>";
			$html .= "<td>" . $datos_socio["Email"]   . "</td>";
			$html .= "<td>" . utf8_encode($dbo->getFields( "Socio" , "Nombre" , "IDSocio = '".$Datos[IDSocioBeneficiario]."'" )."".$dbo->getFields( "Socio" , "Apellido" , "IDSocio = '".$Datos[IDSocioBeneficiario]."'" )) ."</td>";
			$html .= "<td>" . $Datos["Valor"]   . "</td>";
			$html .= "<td>" . $Datos["CodigoPago"]   . "</td>";
			$html .= "<td>" . $Datos["EstadoTransaccion"]   . "</td>";
			$html .= "<td>" . $Datos["CodigoRespuesta"]   . "</td>";

			//Consulto los campos dinamicos
			  $r_campos =& $dbo->all( "EventoRegistroDatos" , "IDEventoRegistro = '" . $Datos["IDEventoRegistro"]  ."'");
			   while( $rdatos = $dbo->object( $r_campos ) ):
					$array_otros_datos[$rdatos->IDEventoRegistro][$rdatos->IDCampoFormularioEvento] =  $rdatos->Valor;
				endwhile;

			  if(count($array_campos)>0):
				foreach($array_campos as $id_campo):
					$html .= "<td>" . $array_otros_datos[$Datos["IDEventoRegistro"]][$id_campo] . "</td>";
				endforeach;
			  endif;

				//Especial lagartos
				if($_GET["IDEvento"]==3043){
					 $html .= "<td>".$array_otros_datos[$Datos["IDEventoRegistro"]][100000]."</td>";
				}

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
