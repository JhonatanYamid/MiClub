<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select * From ActualizacionGratamira
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
				$html .= "<th>Tipo</th>";
				$html .= "<th>Numero Documento</th>";
				$html .= "<th>Nombre</th>";
				$html .= "<th>Apellido</th>";
				$html .= "<th>Torre</th>";
				$html .= "<th>Apartamento</th>";
				$html .= "<th>Telefono</th>";
				$html .= "<th>Celular</th>";
				$html .= "<th>Email</th>";					

				$html .= "<th>Parentesco Beneficiario</th>";		
				$html .= "<th>Menor de edad</th>";			

				$html .= "<th>Documento TITULAR</th>";
				$html .= "<th>Fecha Creaci&oacute;n</th>";
			$html .= "</tr>";

		$style='mso-number-format:"@";';
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{
			
			$html .= "<tr>";
				$html .= "<td>" . $Datos["Tipo"] ."</td>";
				$html .= "<td>" . $Datos["NumeroDocumento"] ."</td>";
				$html .= "<td>" . utf8_decode($Datos["Nombre"]) ."</td>";
				$html .= "<td>" . utf8_decode($Datos["Apellido"]) ."</td>";
				$html .= "<td>" . $Datos["Torre"] ."</td>";
				$html .= "<td>" . $Datos["Casa"] ."</td>";
				$html .= "<td>" . $Datos["Telefono"] ."</td>";
				$html .= "<td>" . $Datos["Celular"] ."</td>";
				$html .= "<td>" . $Datos["Email"] ."</td>";				
				$html .= "<td></td>";				
				$html .= "<td></td>";				
				$html .= "<td></td>";			
				$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
			$html .= "</tr>";

			//Consulto los beneficiarios
			$sql_benef = "	Select *
							From ActualizacionGratamiraBeneficiarios
							Where IDActualizacionGratamira = '".$Datos["IDActualizacionGratamira"]."'  Order By FechaTrCr ASC" ;
			$result_benef= $dbo->query( $sql_benef );
			$filas = $dbo->rows($result_benef);	

			while( $DatosBenef = $dbo->fetchArray( $result_benef ) )
			{
				$html .= "<tr>";
					$html .= "<td>BENEFICIARIO</td>";
					$html .= "<td>" . $DatosBenef["NumeroDocumentoBeneficiario"] ."</td>";
					$html .= "<td>" . utf8_decode($DatosBenef["NombreBeneficiario"]) ."</td>";
					$html .= "<td>" . utf8_decode($DatosBenef["ApellidoBeneficiario"]) ."</td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";											
					$html .= "<td></td>";						
					$html .= "<td>".$DatosBenef["ParentescoBeneficiario"]."</td>";	
					$html .= "<td>".$DatosBenef["MenorDeEdad"]."</td>";						
					$html .= "<td>".$Datos["NumeroDocumento"]."</td>";
					$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
				$html .= "</tr>";
			}
						
			$html .= "<tr>";
			$html .= "</tr>";
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
