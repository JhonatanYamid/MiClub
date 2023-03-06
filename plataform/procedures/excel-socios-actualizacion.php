<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select *
					From ActualizacionArrayanes
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
		$html .= "<th>NumeroDerecho</th>";
    $html .= "<th>NombreApellidoTitular</th>";
	  $html .= "<th>DocumentoIdentidad</th>";
		$html .= "<th>DireccionResidencia</th>";
		$html .= "<th>Barrio</th>";
		$html .= "<th>Localidad</th>";
		$html .= "<th>TelefonoFijo</th>";
		$html .= "<th>TelefonoCelular</th>";
		$html .= "<th>CorreoElectronico</th>";
		$html .= "<th>FechaNacimiento</th>";
		$html .= "<th>Deporte</th>";
		$html .= "<th>Profesion</th>";
		$html .= "<th>Ocupacion</th>";
		$html .= "<th>Cargo</th>";
		$html .= "<th>Empresa</th>";
		$html .= "<th>Terminos</th>";
		$html .= "<th>Fecha</th>";


		$html .= "</tr>";

		$style='mso-number-format:"@";';
		while( $Datos = $dbo->fetchArray( $result_reporte ) )
		{

			$bitacora="";
			unset($array_datos_seguimiento);
			$html .= "<tr>";
			$html .= "<td>" . $Datos["NumeroDerecho"] ."</td>";
			$html .= "<td>" . $Datos["NombreApellidoTitular"] ."</td>";
			$html .= "<td>" . $Datos["DocumentoIdentidad"] ."</td>";
			$html .= "<td>" . $Datos["DireccionResidencia"] ."</td>";
			$html .= "<td>" . $Datos["Barrio"] ."</td>";
			$html .= "<td>" . $Datos["Localidad"] ."</td>";
			$html .= "<td>" . $Datos["TelefonoFijo"] ."</td>";
			$html .= "<td>" . $Datos["TelefonoCelular"] ."</td>";
			$html .= "<td>" . $Datos["CorreoElectronico"] ."</td>";
			$html .= "<td>" . $Datos["FechaNacimiento"] ."</td>";
			$html .= "<td>" . $Datos["Deporte"] ."</td>";
			$html .= "<td>" . $Datos["Profesion"] ."</td>";
			$html .= "<td>" . $Datos["Ocupacion"] ."</td>";
			$html .= "<td>" . $Datos["Cargo"] ."</td>";
			$html .= "<td>" . $Datos["Empresa"] ."</td>";
			$html .= "<td>" . $Datos["Terminos"] ."</td>";
			$html .= "<td>" . $Datos["UsuarioTrCr"] ."</td>";
			$html .= "</tr>";
			//Consulto los beneficiarios
			$sql_benef = "Select *
							From ActualizacionArrayanesBeneficiarios
							Where IDActualizacionArrayanes = '".$Datos["IDActualizacionArrayanes"]."'  Order By FechaTrCr ASC" ;
			$result_benef= $dbo->query( $sql_benef );
			while( $DatosBenef = $dbo->fetchArray( $result_benef ) ){
				$html .= "<tr>";
				$html .= "<td></td>";
				$html .= "<td>Beneficiario</td>";
				$html .= "<td>" . $DatosBenef["DocumentoBeneficiario"] ."</td>";
				$html .= "<td>" . $DatosBenef["Nombre"] ."</td>";
				$html .= "<td>" . $DatosBenef["Parentesco"] ."</td>";
				$html .= "<td>" . $DatosBenef["CorreoElectronico"] ."</td>";
				$html .= "<td>" . $DatosBenef["Telefono"] ."</td>";
				$html .= "<td>" . $DatosBenef["FechaNacimiento"] ."</td>";
				$html .= "<td>" . $DatosBenef["Ocupacion"] ."</td>";
				$html .= "<td>" . $DatosBenef["EmpresaUniversidadColegio"] ."</td>";
				$html .= "<td>" . $DatosBenef["Deporte"] ."</td>";
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
