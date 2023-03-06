<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select * From ActualizacionAltoLiving
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
				$html .= "<th>Apartamento</th>";
				$html .= "<th>Telefono</th>";
				$html .= "<th>Celular</th>";
				$html .= "<th>Email</th>";
				$html .= "<th>Profesion</th>";				

				$html .= "<th>Parentesco Beneficiario</th>";
				$html .= "<th>Edad Empleado</th>";
				$html .= "<th>Empleado</th>";
				
				$html .= "<th>Nombre Mascota</th>";
				$html .= "<th>Especie Mascota</th>";
				$html .= "<th>Raza Mascota</th>";
				$html .= "<th>Edad Mascota</th>";

				$html .= "<th>Marca Vehiculo</th>";
				$html .= "<th>Color Vehiculo</th>";
				$html .= "<th>Placa Vehiculo</th>";

				$html .= "<th>Contacto Emergencia</th>";
				$html .= "<th>Telefono 1 Contacto Emergencia</th>";
				$html .= "<th>Telefono 2 Contacto Emergencia</th>";

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
				$html .= "<td>" . $Datos["Casa"] ."</td>";
				$html .= "<td>" . $Datos["Telefono"] ."</td>";
				$html .= "<td>" . $Datos["Celular"] ."</td>";
				$html .= "<td>" . $Datos["Email"] ."</td>";
				$html .= "<td>" . $Datos["Profesion"] ."</td>";				
				$html .= "<td></td>";				
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td></td>";
				$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
			$html .= "</tr>";

			//Consulto los beneficiarios
			$sql_benef = "	Select *
							From ActualizacionAltoLivingBeneficiarios
							Where IDActualizacionAltoLiving = '".$Datos["IDActualizacionAltoLiving"]."'  Order By FechaTrCr ASC" ;
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
					$html .= "<td>" . $DatosBenef["CorreoBeneficiario"] ."</td>";
					$html .= "<td>" . $DatosBenef["ProfesionBeneficiario"] ."</td>";				

					$html .= "<td>".$DatosBenef["ParentescoBeneficiario"]."</td>";				
					$html .= "<td>".$DatosBenef["EdadEmpleado"]."</td>";
					$html .= "<td>".$DatosBenef["Empleado"]."</td>";

					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td>".$Datos["NumeroDocumento"]."</td>";
					$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
				$html .= "</tr>";
			}

			//Consulto las mascotas
			$sql_mascotas = "	Select *
							From ActualizacionMascotasAltoLiving
							Where IDActualizacionAltoLiving = '".$Datos["IDActualizacionAltoLiving"]."'  Order By FechaTrCr ASC" ;
			$result_mascotas= $dbo->query( $sql_mascotas );
			$filas = $dbo->rows($result_mascotas);	

			while( $DatosMascota = $dbo->fetchArray( $result_mascotas ) )
			{
				$html .= "<tr>";
					$html .= "<td>MASCOTA</td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					

					$html .= "<td></td>";				
					$html .= "<td></td>";
					$html .= "<td></td>";

					$html .= "<td>".$DatosMascota["NombreMascota"]."</td>";
					$html .= "<td>".$DatosMascota["EspecieMascota"]."</td>";
					$html .= "<td>".$DatosMascota["Raza"]."</td>";
					$html .= "<td>".$DatosMascota["Edad"]."</td>";			

					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td>".$Datos["NumeroDocumento"]."</td>";
					$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
				$html .= "</tr>";
			}

			//Consulto los vehiculos
			$sql_vehiculos = "	Select *
							From ActualizacionVehiculosAltoLiving
							Where IDActualizacionAltoLiving = '".$Datos["IDActualizacionAltoLiving"]."'  Order By FechaTrCr ASC" ;
			$result_vehiculos= $dbo->query( $sql_vehiculos );
			$filas = $dbo->rows($result_vehiculos);	

			while( $Datosvehiculos = $dbo->fetchArray( $result_vehiculos ) )
			{
				$html .= "<tr>";
					$html .= "<td>VEHICULO</td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";					

					$html .= "<td></td>";				
					$html .= "<td></td>";
					$html .= "<td></td>";

					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";						

					$html .= "<td>".$Datosvehiculos["Marca"]."</td>";
					$html .= "<td>".$Datosvehiculos["Color"]."</td>";
					$html .= "<td>".$Datosvehiculos["Placa"]."</td>";									

					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td>".$Datos["NumeroDocumento"]."</td>";
					$html .= "<td>" . $Datos["FechaTrCr"] ."</td>";
				$html .= "</tr>";
			}

			//Consulto los contactos
			$sql_contactos = "	Select *
							From ActulizacionContactosAltoLiving
							Where IDActualizacionAltoLiving = '".$Datos["IDActualizacionAltoLiving"]."'  Order By FechaTrCr ASC" ;
			$result_contactos= $dbo->query( $sql_contactos );
			$filas = $dbo->rows($result_contactos);	

			while( $Datoscontactos = $dbo->fetchArray( $result_contactos ) )
			{
				$html .= "<tr>";
					$html .= "<td>CONTACTO DE EMERGENCIA</td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";		

					$html .= "<td></td>";				
					$html .= "<td></td>";
					$html .= "<td></td>";

					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";						

					$html .= "<td></td>";
					$html .= "<td></td>";
					$html .= "<td></td>";									

					$html .= "<td>".$Datoscontactos[Nombre]."</td>";
					$html .= "<td>".$Datoscontactos[Telefono1]."</td>";
					$html .= "<td>".$Datoscontactos[Telefono2]."</td>";
					
					$html .= "<td>".$Datos["NumeroDocumento"]."</td>";
					$html .= "<td>".$Datos["FechaTrCr"]."</td>";
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
