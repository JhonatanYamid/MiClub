<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "SELECT * FROM `Correspondencia` WHERE `IDClub` = $_GET[IDClub] AND FechaTrCr >= '$_GET[FechaInicio]' AND FechaTrCr <= '$_GET[FechaFin]'";
	
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
				$html .= "<th>SOCIO</th>";
				$html .= "<th>TIPO CORRESPONDENCIA</th>";
				$html .= "<th>USUARIO CREA</th>";
				$html .= "<th>USUARIO ENTREGA</th>";
				$html .= "<th>ESTADO CORRESPONDENCIA</th>";
				$html .= "<th>VIVIENDA</th>";
				$html .= "<th>DESTINATARIO</th>";
				$html .= "<th>FECHA RECEPCIÓN</th>";
				$html .= "<th>FECHA ENTREGA</th>";
				$html .= "<th>ENTREGADO A</th>";
				$html .= "<th>ARCHIVO</th>";
				
			$html .= "</tr>";

			$style='mso-number-format:"@";';
			while( $Datos = $dbo->fetchArray( $result_reporte ) )
			{
				$html .= "<tr>";
					$html .="<td>".$dbo->getFields("Socio", "Nombre", "IDSocio = $Datos[IDSocio]")." ".$dbo->getFields("Socio", "Apellido", "IDSocio = $Datos[IDSocio]")."</td>";
					$html .="<td>".$dbo->getFields("TipoCorrespondencia", "Nombre", "IDTipoCorrespondencia = $Datos[IDTipoCorrespondencia]")."</td>";
					$html .="<td>".$dbo->getFields("Usuario", "Nombre", "IDUsuario = $Datos[IDUsuarioCrea]")."</td>";
					$html .="<td>".$dbo->getFields("Usuario", "Nombre", "IDUsuario = $Datos[IDUsuarioEntrega]")."</td>";
					$html .="<td>".$dbo->getFields("CorrespondenciaEstado", "Nombre", "IDCorrespondenciaEstado = $Datos[IDCorrespondenciaEstado]")."</td>";
					$html .="<td>".$Datos[Vivienda]."</td>";
					$html .="<td>".$Datos[Destinatario]."</td>";
					$html .="<td>".$Datos[FechaRecepcion]."</td>";
					$html .="<td>".$Datos[FechaEntrega]."</td>";
					$html .="<td>".$Datos[EntregadoA]."</td>";

					$Archivo = CORRESPONDENCIA_ROOT.$Datos["Archivo"];
					$html .="<td>"."<a href='".$Archivo."'>Ver imagen: </a>".$Archivo."</td>";
				$html .= "</tr>";
			}
		$html .= "</table>";

		// echo $html;
		// exit;


		//construimos el excel
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $html;
		exit();

        }else{
			echo "NO HAY DATOS PARA ESAS FECHAS";
			exit;
		}
?>
