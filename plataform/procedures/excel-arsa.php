<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select *
					From ActualizacionArsa
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
		$html .= "<th>Direccion</th>";
		$html .= "<th>Telefono</th>";
		$html .= "<th>Celular</th>";
		$html .= "<th>Email</th>";
		$html .= "<th>NumeroPersonas</th>";
		$html .= "<th>AfiliadoArsa</th>";
		$html .= "<th>Fecha</th>";


		$html .= "</tr>";

		$style='mso-number-format:"@";';
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . $Datos["NumeroDocumento"] ."</td>";
			$html .= "<td>" . $Datos["Nombre"] ."</td>";
			$html .= "<td>" . $Datos["Apellido"] ."</td>";
			$html .= "<td>" . $Datos["Direccion"] ."</td>";
			$html .= "<td>" . $Datos["Telefono"] ."</td>";
			$html .= "<td>" . $Datos["Celular"] ."</td>";
			$html .= "<td>" . $Datos["Email"] ."</td>";
			$html .= "<td>" . $Datos["NumeroPersonas"] ."</td>";
			$html .= "<td>" . $Datos["AfiliadoArsa"] ."</td>";
			$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
			$html .= "</tr>";
			//Consulto los beneficiarios
			$sql_benef = "Select *
							From ActualizacionArsaBeneficiarios
							Where IDActualizacionArsa = '".$Datos["IDActualizacionArsa"]."'  Order By FechaTrCr ASC" ;
			$result_benef= $dbo->query( $sql_benef );
			while( $DatosBenef = $dbo->fetchArray( $result_benef ) ){
				$html .= "<tr>";
				$html .= "<td></td>";
				$html .= "<td>Beneficiario</td>";
				$html .= "<td>" . $DatosBenef["NombreBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["ApellidoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["NumeroIdentificacionBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["CorreoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["TelefonoBeneficiario"] ."</td>";
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
