<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	switch($_GET[Tipo]):
		case "Historico":
			$sql_reporte = "SELECT * FROM CaddieHistoricoAsignacion CHA, Caddie C WHERE C.IDCaddie = CHA.IDCaddie AND C.IDClub = $_GET[IDClub] AND CHA.fechaRegistro >= '$_GET[FechaInicio] 00:00:00' AND CHA.fechaRegistro <= '$_GET[FechaFin] 23:59:59' ORDER BY CHA.fechaRegistro DESC";
		break;
		case "Sorteo":
			$sql_reporte = "SELECT * FROM SorteoCaddieDetalle SCD, Caddie C WHERE C.IDClub = $_GET[IDClub] AND C.IDCaddie = SCD.IDCaddie AND SCD.fechaRegistro >= '$_GET[FechaInicio] 00:00:00' AND SCD.fechaRegistro <= '$_GET[FechaFin] 23:59:59' ORDER BY SCD.fechaRegistro DESC";
		break;
	endswitch;
	
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
				$html .= "<th>CADDIE</th>";
				$html .= "<th>CODIGO CADDIE</th>";
				$html .= "<th>CATEGORIA CADDIE</th>";								
				if($_GET[Tipo] != Sorteo):
					$html .= "<th>SOCIO</th>";
					$html .= "<th>TALEGA</th>";
					$html .= "<th>ESTADO</th>";
				endif;
				$html .= "<th>USUARIO REGISTRA</th>";				
				$html .= "<th>FECHA REGISTRO</th>";			
				
			$html .= "</tr>";

			$style='mso-number-format:"@";';
			while( $Datos = $dbo->fetchArray( $result_reporte ) )
			{
				if($Datos[estado] == 2):
					$ESTADO = "ASIGNADO";
				elseif($Datos[estado] == 1):
					$ESTADO = "DISPONIBLE";
				else:
					$ESTADO = "INACTIVO";
				endif;	
				
				$talega = $dbo->getFields("TalegaAdministracion","IDTalega","IDTalegaAdministracion = $Datos[IDTalegaAdministracion]");
				$html .= "<tr>";
					$html .="<td>".$Datos[nombre]." ".$Datos[apellido]."</td>";
					$html .="<td>".$Datos[Codigo]."</td>";
					$html .="<td>".$dbo->getFields("CategoriaCaddie", "Nombre", "IDCategoriaCaddie = $Datos[IDCategoriaCaddie]")."</td>";
					if($_GET[Tipo] != Sorteo):
						$html .="<td>".$dbo->getFields("Socio", "Nombre", "IDSocio = $Datos[IDSocio]")." ".$dbo->getFields("Socio", "Apellido", "IDSocio = $Datos[IDSocio]")."</td>";
						$html .="<td>".$dbo->getFields("Talega", "Nombre", "IDTalega = $talega")."</td>";
						$html .="<td>".$ESTADO."</td>";
					endif;					
					$html .="<td>".$dbo->getFields("Usuario", "Nombre", "IDUsuario = $Datos[idUsuarioRegistra]")."</td>";
					$html .="<td>".$Datos[fechaRegistro]."</td>";		
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
