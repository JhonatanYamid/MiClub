<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select * From ActualizacionCaminoArrayanes
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
		$html .= "<th>Manzana</th>";
		$html .= "<th>Casa</th>";
		$html .= "<th>Telefono</th>";
		$html .= "<th>Celular</th>";
		$html .= "<th>Email</th>";
		$html .= "<th>NumeroPersonas</th>";
		$html .= "<th>Fecha</th>";
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
			$html .= "<td>" . $Datos["Manzana"] ."</td>";
			$html .= "<td>" . $Datos["Casa"] ."</td>";
			$html .= "<td>" . $Datos["Telefono"] ."</td>";
			$html .= "<td>" . $Datos["Celular"] ."</td>";
			$html .= "<td>" . $Datos["Email"] ."</td>";
			$html .= "<td>" . $Datos["NumeroPersonas"] ."</td>";
			$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
			$html .= "</tr>";

			//Consulto los beneficiarios
			$sql_benef = "	Select *
							From ActualizacionCaminoArrayanesBeneficiarios
							Where IDActualizacionCaminoArrayanes = '".$Datos["IDActualizacionCaminoArrayanes"]."'  Order By FechaTrCr ASC" ;
			$result_benef= $dbo->query( $sql_benef );
			$filas = $dbo->rows($result_benef);	

			if($filas > 0)
			{
				$html .= "<tr>";
				$html .= "<th>Beneficiarios de : ".utf8_decode($Datos["Nombre"])."</th>";
				$html .= "<th>Nombre Beneficiario</th>";
				$html .= "<th>Apellido Beneficiario</th>";
				$html .= "<th>Manzana Beneficiario</th>";
				$html .= "<th>Casa Beneficiario</th>";
				$html .= "<th>Fecha Nacimiento Beneficiario</th>";
				$html .= "<th>Numero Documento Beneficiario</th>";
				$html .= "<th>Correo Beneficiario</th>";
				$html .= "<th>Telefono Beneficiario</th>";
				$html .= "<th>Empleado</th>";
				$html .= "<th>Fecha</th>";
				$html .= "</tr>";

			}

			while( $DatosBenef = $dbo->fetchArray( $result_benef ) )
			{
				$html .= "<tr>";
				$html .= "<th>Beneficiario</th>";
				$html .= "<td>" . utf8_decode($DatosBenef["NombreBeneficiario"]) ."</td>";
				$html .= "<td>" . utf8_decode($DatosBenef["ApellidoBeneficiario"]) ."</td>";
				$html .= "<td>" . $DatosBenef["ManzanaBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["CasaBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["FechaNacimientoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["NumeroDocumentoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["CorreoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["TelefonoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["Empleado"] ."</td>";
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
