<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select * From ActualizacionMascotasFarrallones
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
				$html .= "<th>NOMBRE MASCOTA</th>";
				$html .= "<th>TIPO MASCOTA</th>";
				$html .= "<th>RAZA</th>";
				$html .= "<th>EDAD</th>";
				$html .= "<th>FOTO VACUNAS</th>";
				$html .= "<th>FOTO MASCOTA</th>";
				$html .= "<th>DOCUMENTO DUE&Ntilde;O</th>";
				$html .= "<th>NOMBRE DUE&Ntilde;O</th>";
				$html .= "<th>FECHA REGISTRO</th>";
			$html .= "</tr>";

			$style='mso-number-format:"@";';
			while( $Datos = $dbo->fetchArray( $result_reporte ) )
			{
				$html .="<td>".$Datos["NombreMascota"]."</td>";
				$html .="<td>".$Datos["TipoAnimal"]."</td>";
				$html .="<td>".$Datos["Raza"]."</td>";
				$html .="<td>".$Datos["Edad"]."</td>";

				$imagenVacuna = SOCIO_ROOT.$Datos["FotoVacunas"];
				$html .="<td>"."<a href='".$imagenVacuna."'>Ver imagen: </a>".$imagenVacuna."</td>";

				$imagenMascotas = SOCIO_ROOT.$Datos["FotoMascota"];
				$html .="<td>"."<a href='".$imagenMascotas."'>Ver imagen: </a>".$imagenMascotas."</td>";

				$html .="<td>".$Datos["DocumentoDueño"]."</td>";
				$html .="<td>".$dbo->getFields("Socio", "Nombre" , " NumeroDocumento = '".$Datos["DocumentoDueño"]."' AND IDClub = 29")." ".$dbo->getFields("Socio", "Apellido" , " NumeroDocumento = '".$Datos["DocumentoDueño"]."' AND IDClub = 29")."</td>";
				$html .="<td>".$Datos["FechaTrCr"]."</td>";
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
