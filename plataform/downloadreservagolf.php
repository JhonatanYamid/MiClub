<?php

//Script para exportar reporte de contactos por rango de fechas
require( dirname( __FILE__ ) . "/../admin/config.inc.php" );


		$condicion_servicio=" and IDServicio = '".$_GET["IDServicio"]."' ";

			$sql_tee1 = "Select * From ReservaGeneral Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' and Tee= 'Tee1' ".$condicion_servicio."
				UNION Select * From ReservaGeneralBck Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' and Tee= 'Tee1' and IDEstadoReserva = 1 ".$condicion_servicio." Order By Fecha,Hora";

			$sql_tee10 = "Select * From ReservaGeneral Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' and Tee= 'Tee10' ".$condicion_servicio."
				UNION Select * From ReservaGeneralBck Where Fecha between  '".$_GET["FechaInicio"]."' and '".$_GET["FechaFin"]."' and IDClub = '".$_GET["IDClub"]."' and Tee= 'Tee10' and IDEstadoReserva = 1 ".$condicion_servicio." Order By Fecha,Hora";


		$nombre = "Reservas" . date( "Y_m_d H:i:s" );

		$qry_tee1 = $dbo->query( $sql_tee1 );
		$qry_tee10 = $dbo->query( $sql_tee10 );

		$html  = "<table width='100%' border='1'>
					  <tbody>
						<tr>
						  <td colspan='3' align='center'>TEE TIME ".$_GET["FechaInicio"]."<br></td>
						</tr>
						<tr>
						  <td align='center' bgcolor='#F2F2F2'><strong>HOYO 1</strong></td>
						  <td align='center' bgcolor='#F2F2F2'><strong>HOYO 10</strong></td>
						</tr>";

		$html .= "
		 <tr>
      <td valign='top' ><table width='100%' border='1'>
        <tbody>
          <tr>
            <td><strong>HORA</strong></td>
            <td><strong>GOLFISTA</strong></td>
          </tr>
		  ";
		  while($row_tee1 = $dbo->fetchArray($qry_tee1)):
            $html .= ' <tr>
            <td valign="middle">'.$row_tee1["Hora"].'</td>
            <td valign="top">';
			$datos_socio=$dbo->fetchAll("Socio"," IDSocio = '".$row_tee1["IDSocio"]."'","array");
			$html .= $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
			$sql_inv="Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$row_tee1["IDReservaGeneral"]."'";
			$resul_inv = $dbo->query($sql_inv);
			$conta_fila=1;
			while($row_inv = $dbo->fetchArray($resul_inv)):
				$html .= "<br>".$row_inv["Nombre"];
				$conta_fila++;
			endwhile;
			for($i=$conta_fila;$i<=4;$i++):
				$html .= '<br>';
			endfor;

			$html .= '</td>
          </tr>';
		   endwhile;
		  $html .= '

        </tbody>
      </table></td>
      <td valign="top"><table width="100%" border="1">
        <tbody>
          <tr>
            <td><strong>HORA</strong></td>
            <td><strong>GOLFISTA</strong></td>
          </tr>';
		  while($row_tee10 = $dbo->fetchArray($qry_tee10)):
             $html .= ' <tr>
            <td valign="middle">'.$row_tee10["Hora"].'</td>
            <td valign="top">';
			$datos_socio=$dbo->fetchAll("Socio"," IDSocio = '".$row_tee10["IDSocio"]."'","array");
			$html .= $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
			$sql_inv="Select * From ReservaGeneralInvitado Where IDReservaGeneral = '".$row_tee10["IDReservaGeneral"]."'";
			$resul_inv = $dbo->query($sql_inv);
			$conta_fila=1;
			while($row_inv = $dbo->fetchArray($resul_inv)):
				$html .= "<br>".$row_inv["Nombre"];
				$conta_fila++;
			endwhile;
			for($i=$conta_fila;$i<=4;$i++):
				$html .= '<br>';
			endfor;
			$html .= '</td>
          </tr>';
		   endwhile;
		  $html .= '
        </tbody>
      </table></td>
    </tr>';

		$html .= "
			</tbody>
		</table>
		";


		//construimos el excel
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $html;

		exit();

?>
