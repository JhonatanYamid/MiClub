<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select * From ActualizacionSanJacinto
					Where 1  Order By FechaTrCr ASC" ;
	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "Actualizacion:" . date( "Y_m_d" );

	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
		$html .= "<th>NumeroDocumento</th>";
    	$html .= "<th>Nombre</th>";
	  	$html .= "<th>Apellido</th>";
		$html .= "<th>Etapa</th>";
		$html .= "<th>Casa</th>";
		$html .= "<th>Telefono</th>";
		$html .= "<th>Fecha Nacimiento</th>";
		$html .= "<th>Celular</th>";
		$html .= "<th>Email</th>";
		$html .= "<th>NumeroPersonas</th>";
		$html .= "<th>Documento TITULAR</th>";
		$html .= "<th>Deportes</th>";
		$html .= "<th>Tipo Beneficiario</th>";
		$html .= "<th>Fecha Creaci&oacute;n</th>";
		$html .= "</tr>";

		$style='mso-number-format:"@";';
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . $Datos["NumeroDocumento"] ."</td>";
			$html .= "<td>" . utf8_decode($Datos["Nombre"]) ."</td>";
			$html .= "<td>" . utf8_decode($Datos["Apellido"]) ."</td>";
			$html .= "<td>" . $Datos["Etapa"] ."</td>";
			$html .= "<td>" . $Datos["Casa"] ."</td>";
			$html .= "<td></td>";
			$html .= "<td></td>";
			$html .= "<td>" . $Datos["Celular"] ."</td>";
			$html .= "<td></td>";
			$html .= "<td>" . $Datos["NumeroPersonas"] ."</td>";
			$html .= "<td></td>";
			$html .= "<td></td>";
			$html .= "<td></td>";
			$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
			$html .= "</tr>";

			//Consulto los beneficiarios
			$sql_benef = "	Select *
							From ActualizacionSanJacintoBeneficiarios
							Where IDActualizacionSanJacinto = '".$Datos["IDActualizacionSanJacinto"]."'  Order By FechaTrCr ASC" ;
			$result_benef= $dbo->query( $sql_benef );
			$filas = $dbo->rows($result_benef);	

			while( $DatosBenef = $dbo->fetchArray( $result_benef ) )
			{
				$html .= "<tr>";
				$html .= "<td>" . $DatosBenef["NumeroDocumentoBeneficiario"] ."</td>";
				$html .= "<td>" . utf8_decode($DatosBenef["NombreBeneficiario"]) ."</td>";
				$html .= "<td>" . utf8_decode($DatosBenef["ApellidoBeneficiario"]) ."</td>";
				$html .= "<td>" . $DatosBenef["EtapaBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["CasaBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["TelefonoBeneficiario"] . " / " . $DatosBenef["Telefono2Beneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["FechaNacimientoBeneficiario"] ."</td>";				
				$html .= "<td></td>";				
				$html .= "<td>" . $DatosBenef["CorreoBeneficiario"] . " / " . $DatosBenef["Correo2Beneficiario"] ."</td>";				
				$html .= "<td></td>";				
				$html .= "<td>" . $Datos["NumeroDocumento"] ."</td>";
				$html .= "<td>" . $DatosBenef["DeportesFeneficiario"] . "</td>";
				$html .= "<td>" . $DatosBenef["TipoBeneficiario"] . "</td>";
				$html .= "<td>" . $DatosBenef["FechaTrCr"] ."</td>";				
				$html .= "</tr>";
			}
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
