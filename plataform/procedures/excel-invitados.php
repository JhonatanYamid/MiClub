<?php
	require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

	function remplaza_tildes($texto){
		$no_permitidas= array ("Ã¡","Ã©","Ã","Ã³","Ãº","á","é","í­","á","ú");
		$permitidas= array ("&aacute;","&eacute;","&iacute;","o","&uacute;","aaaa");
		$texto_final = str_replace($no_permitidas, $permitidas ,$texto);
		return $texto_final;
	}


	$sql_reporte = "Select *
					From Invitado
					Where IDClub = '".$_GET["IDClub"]."'  Order By IDInvitado ASC" ;
	$result_reporte= $dbo->query( $sql_reporte );

	$nombre = "InvitadosClub:" . date( "Y_m_d" );

	$sql_arl="SELECT IDArl,Nombre FROM Arl Where 1";
	$r_arl=$dbo->query($sql_arl);
	while($row=$dbo->fetchArray($r_arl)){
		$array_arl[$row["IDArl"]]=$row["Nombre"];
	}


	$sql_eps="SELECT IDEps,Nombre FROM Eps Where 1";
	$r_eps=$dbo->query($sql_eps);
	while($row=$dbo->fetchArray($r_eps)){
		$array_eps[$row["IDEps"]]=$row["Nombre"];
	}

	$sql_afp="SELECT IDAfp,Nombre FROM Afp Where 1";
	$r_afp=$dbo->query($sql_afp);
	while($row=$dbo->fetchArray($r_afp)){
		$array_afp[$row["IDAfp"]]=$row["Nombre"];
	}

	$sql_tipoinv="SELECT IDTipoInvitado,Nombre FROM TipoInvitado Where IDClub= '".$_GET["IDClub"]."'";
	$r_tipoinv=$dbo->query($sql_tipoinv);
	while($row=$dbo->fetchArray($r_tipoinv)){
		$array_tipoinv[$row["IDTipoInvitado"]]=$row["Nombre"];
	}

	$sql_clasifinv="SELECT IDClasificacionInvitado,Nombre FROM ClasificacionInvitado Where 1 ";
	$r_clasifinv=$dbo->query($sql_clasifinv);
	while($row=$dbo->fetchArray($r_clasifinv)){
		$array_clasifinv[$row["IDClasificacionInvitado"]]=$row["Nombre"];
	}


	$NumSocios = $dbo->rows( $result_reporte );

	if( $NumSocios > 0 )
	{
		$html  = "";
		$html .= "<table width='100%' border='1'>";
		$html .= "<tr>";
		$html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
		$html .= "</tr>";
		$html .= "<tr>";
    $html .= "<th>TipoInvitado</th>";
	  $html .= "<th>ClasificacionInvitado</th>";
		$html .= "<th>Arl</th>";
		$html .= "<th>Afp</th>";
		$html .= "<th>Eps</th>";
		$html .= "<th>FechaVencimientoArl</th>";
		$html .= "<th>FechaVencimientoAfp</th>";
		$html .= "<th>FechaVencimientoEps</th>";
		$html .= "<th>Numero Documento</th>";
		$html .= "<th>Nombre</th>";
		$html .= "<th>Apellido</th>";
		$html .= "<th>Direccion</th>";
		$html .= "<th>Telefono</th>";
		$html .= "<th>Email</th>";
		$html .= "<th>Predio</th>";
		$html .= "<th>NombreEmergencia</th>";
		$html .= "<th>ApellidoEmergencia</th>";
		$html .= "<th>DireccionEmergencia</th>";
		$html .= "<th>ObservacionGeneral</th>";
		$html .= "<th>RazonBloqueo</th>";

		$html .= "</tr>";

		$style='mso-number-format:"@";';
		while( $Datos = $dbo->fetchArray( $result_reporte ) ){


			$html .= "<tr>";
			$html .= "<td style='".$style."'>" . $array_tipoinv[$Datos["IDTipoInvitado"]] ."</td>";
			$html .= "<td>" . $array_clasifinv[$Datos["IDClasificacionInvitado"]] ."</td>";
			$html .= "<td>" . $array_arl[$Datos["IDArl"]] ."</td>";
			$html .= "<td>" . $array_afp[$Datos["IDAfp"]] ."</td>";
			$html .= "<td>" . $array_eps[$Datos["IDEps"]] ."</td>";
			$html .= "<td>" . $Datos["FechaVencimientoArl"] ."</td>";
			$html .= "<td>" . $Datos["FechaVencimientoAfp"] ."</td>";
			$html .= "<td style='".$style."'>" . $Datos["FechaVencimientoEps"] ."</td>";
			$html .= "<td>" . $Datos["NumeroDocumento"] ."</td>";
			$html .= "<td>" . $Datos["Nombre"] ."</td>";
			$html .= "<td>" . $Datos["Apellido"] ."</td>";
			$html .= "<td>" . $Datos["Direccion"] ."</td>";
			$html .= "<td>" . $Datos["Telefono"] ."</td>";
			$html .= "<td>" . $Datos["Email"] ."</td>";
			$html .= "<td>" . $Datos["Predio"] ."</td>";
			$html .= "<td>" . $Datos["NombreEmergencia"] ."</td>";
			$html .= "<td>" . $Datos["ApellidoEmergencia"] ."</td>";
			$html .= "<td>" . $Datos["DireccionEmergencia"] ."</td>";
			$html .= "<td>" . $Datos["ObservacionGeneral"] ."</td>";
			$html .= "<td>" . $Datos["RazonBloqueo"] ."</td>";
			$html .= "</tr>";
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
