<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select * From ParticipantesEvento Order by date ASC" ;
	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "Registro_participantes:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );


	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>Numero Documento</th>";
		$html .= "<th>Tipo Documento</th>";
    	        $html .= "<th>Primer Nombre</th>";
    	        $html .= "<th>Segundo Nombre</th>";
	  	$html .= "<th>Primer Apellido</th>";
	 	$html .= "<th>Segundo Apellido</th>";
		$html .= "<th>Invitado por</th>";
		$html .= "<th>Tipo</th>";
		$html .= "<th>Fecha Creaci&oacute;n</th>";
		$html .= "</tr>";

		$style='mso-number-format:"@";';
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . $Datos["NumeroDocumento"] ."</td>";
			$html .= "<td>" . $Datos["IDTipoDocumento"] ."</td>";
			$html .= "<td>" . utf8_decode( $Datos["PrimerNombre"] ) ."</td>";
			$html .= "<td>" . utf8_decode($Datos["SegundoNombre"]) ."</td>";
                        $html .= "<td>" . utf8_decode( $Datos["PrimerApellido"] ) ."</td>";
			$html .= "<td>" . utf8_decode($Datos["SegundoApellido"]) ."</td>";
			$html .= "<td>" . $Datos["IDInvita"] ."</td>";
			$html .= "<td>" . $Datos["Tipo"] ."</td>";
			$html .= "<td>" . $Datos["date"] ."</td>";
			$html .= "<td></td>"; 
			$html .= "</tr>";
/*
			//Consulto los beneficiarios
			$sql_benef = "	Select * From ParticipantesEvento where Tipo='Participante'" ;
			$result_benef= $dbo->query( $sql_benef );
			$filas = $dbo->rows($result_benef);	

			while( $DatosBenef = $dbo->fetchArray( $result_benef ) )
			{
				$html .= "<tr>";
				$html .= "<td>" . $DatosBenef["NumeroDocumento"] ."</td>";
				$html .= "<td>" . utf8_decode($DatosBenef["NombreBeneficiario"]) ."</td>";
				$html .= "<td>" . utf8_decode($DatosBenef["ApellidoBeneficiario"]) ."</td>";
				$html .= "<td>" . $DatosBenef["ConjuntoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["CasaBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["TelefonoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["FechaNacimientoBeneficiario"] ."</td>";				
				$html .= "<td></td>";				
				$html .= "<td>" . $DatosBenef["CorreoBeneficiario"] ."</td>";				
				$html .= "<td></td>";				
				$html .= "<td>" . $Datos["NumeroDocumento"] ."</td>";
				$html .= "<td>" . $DatosBenef["Empleado"] ."</td>";
				$html .= "<td>" . $DatosBenef["FechaTrCr"] ."</td>";				
				$html .= "</tr>";
			} */
		}
		$html .= "</table>";

		//echo $html;
		//exit;


		//construimos el excel
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $html;
		exit();

        }
?>
