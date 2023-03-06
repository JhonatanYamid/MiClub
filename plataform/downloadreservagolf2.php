<?php

//Script para exportar reporte de contactos por rango de fechas
require dirname(__FILE__) . "/../admin/config.inc.php";

$condicion_servicio = " and IDServicio = '" . $_GET["IDServicio"] . "' ";

$sql_tee1 = "Select * From ReservaGeneral Where Fecha between  '" . $_GET["FechaInicio"] . "' and '" . $_GET["FechaFin"] . "' and IDClub = '" . $_GET["IDClub"] . "' and (Tee= 'Tee1' OR Tee = 'Tee 1') " . $condicion_servicio . "
				UNION Select * From ReservaGeneralBck Where Fecha between  '" . $_GET["FechaInicio"] . "' and '" . $_GET["FechaFin"] . "' and IDClub = '" . $_GET["IDClub"] . "' and (Tee= 'Tee1' OR Tee = 'Tee 1') and IDEstadoReserva = 1 " . $condicion_servicio . " Order By Fecha,Hora";

$sql_tee10 = "Select * From ReservaGeneral Where Fecha between  '" . $_GET["FechaInicio"] . "' and '" . $_GET["FechaFin"] . "' and IDClub = '" . $_GET["IDClub"] . "' and (Tee= 'Tee10' OR Tee = 'Tee 10') " . $condicion_servicio . "				
UNION Select * From ReservaGeneralBck Where Fecha between  '" . $_GET["FechaInicio"] . "' and '" . $_GET["FechaFin"] . "' and IDClub = '" . $_GET["IDClub"] . "' and (Tee= 'Tee10' OR Tee = 'Tee 10') and IDEstadoReserva = 1 " . $condicion_servicio . " Order By Fecha,Hora";

$nombre = "Reservas" . date("Y_m_d H:i:s");

$qry_tee1 = $dbo->query($sql_tee1);
$qry_tee10 = $dbo->query($sql_tee10);

$html = "<table width='100%' border='1'>
					  <tbody>
						<tr>
						  <td colspan='3' align='center'>TEE TIME " . $_GET["FechaInicio"] . "<br></td>
						</tr>
						<tr>
						  <td align='center' bgcolor='#F2F2F2'><strong>HOYO 1</strong></td>
						  <td>---</td>
						  <td align='center' bgcolor='#F2F2F2'><strong>HOYO 10</strong></td>
						</tr>";

$html .= "
		<tr>
			<td align='top' ><table width='100%' border='1'>
        		<tbody>
		  			<tr>
						<td><strong>HORA</strong></td>
						<td><strong>GOLFISTA - ACCION</strong></td>
                        <td> </td>
						";
//Consulto los campos dinamicos
$r_campos = &$dbo->all("ServicioCampo", "IDServicio = '" . $_GET["IDServicio"] . "' Order by IDServicioCampo");
while ($r = $dbo->object($r_campos)) :
    $array_preguntas[] = $r->IDServicioCampo;
    $html .= "<td>" . $r->Nombre . "</td>";
endwhile;

$html .= "</tr>";
while ($row_tee1 = $dbo->fetchArray($qry_tee1)) :
    $html .= '<tr>
								<td align="middle">' . $row_tee1["Hora"] . '</td>
								<td></td>
		            			<td align="top">';
    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_tee1["IDSocio"] . "'", "array");
    $html .= $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " -- " . $datos_socio["Accion"];

    $sql_inv = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_tee1["IDReservaGeneral"] . "'";
    $resul_inv = $dbo->query($sql_inv);
    $conta_fila = 1;

    while ($row_inv = $dbo->fetchArray($resul_inv)) :

        if (!empty($row_inv["IDSocio"])) {
            $datos_invitado = $dbo->fetchAll("Socio", "IDSocio = '" . $row_inv["IDSocio"] . "'");

            $html .= "<br>" . $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " -- " . $datos_invitado["Accion"] . " Confirmado: " . $row_inv["Confirmado"];
        } else {
            $html .= "<br>" . $row_inv["Nombre"] . " -- Confirmado: " . $row_inv["Confirmado"];
        }

        $conta_fila++;
    endwhile;
    for ($i = $conta_fila; $i <= 4; $i++) :
        $html .= '<br>';
    endfor;
    $html .= '</td>';

    $sql_repuesta_socio = "SELECT IDServicioCampo,Valor From ReservaGeneralCampo Where IDReservaGeneral = '" . $row_tee1["IDReservaGeneral"] . "'";
    $r_respuesta_socio = $dbo->query($sql_repuesta_socio);
    while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)) :
        $array_respuesta_socio[$row_respuesta["IDServicioCampo"]] = $row_respuesta["Valor"];
    endwhile;
    if (count($array_preguntas) > 0) :
        foreach ($array_preguntas as $id_pregunta) :
            $html .= "<td>" . $array_respuesta_socio[$id_pregunta] . "</td>";
        endforeach;
    endif;

endwhile;


$html .= '</tr>

        </tbody>
	  </table></td>
	  <td></td>
      <td align="top"><table width="100%" border="1">
        <tbody>
          <tr>
			<td><strong>HORA</strong></td>
			<td><strong>GOLFISTA - ACCION</strong></td>
            <td> </td>
          ';

//Consulto los campos dinamicos
$r_campos2 = &$dbo->all("ServicioCampo", "IDServicio = '" . $_GET["IDServicio"] . "' Order by IDServicioCampo");
while ($r2 = $dbo->object($r_campos2)) :
    $array_preguntas2[] = $r2->IDServicioCampo;
    $html .= "<td>" . $r2->Nombre . "</td>";
endwhile;

$html .= "</tr>";
while ($row_tee10 = $dbo->fetchArray($qry_tee10)) :
    $html .= ' <tr>
					<td align="middle">' . $row_tee10["Hora"] . '</td>
					<td></td>
		            <td align="top">';

    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_tee10["IDSocio"] . "'", "array");
    $html .= $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " -- " . $datos_socio["Accion"];

    $sql_inv = "Select * From ReservaGeneralInvitado Where IDReservaGeneral = '" . $row_tee10["IDReservaGeneral"] . "'";
    $resul_inv = $dbo->query($sql_inv);
    $conta_fila = 1;
    while ($row_inv = $dbo->fetchArray($resul_inv)) :

        if (!empty($row_inv["IDSocio"])) {
            $datos_invitado = $dbo->fetchAll("Socio", "IDSocio = '" . $row_inv["IDSocio"] . "'");

            $html .= "<br>" . $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . " -- " . $datos_invitado["Accion"] . " Confirmado: " . $row_inv["Confirmado"];
        } else {
            $html .= "<br>" . $row_inv["Nombre"] . " -- Confirmado: " . $row_inv["Confirmado"];
        }

        $conta_fila++;
    endwhile;
    for ($i = $conta_fila; $i <= 4; $i++) :
        $html .= '<br>';
    endfor;
    $html .= '</td>
		          ';

    $sql_repuesta_socio2 = "SELECT IDServicioCampo,Valor From ReservaGeneralCampo Where IDReservaGeneral = '" . $row_tee10["IDReservaGeneral"] . "'";
    $r_respuesta_socio2 = $dbo->query($sql_repuesta_socio2);
    while ($row_respuesta2 = $dbo->fetchArray($r_respuesta_socio2)) :
        $array_respuesta_socio2[$row_respuesta2["IDServicioCampo"]] = $row_respuesta2["Valor"];
    endwhile;
    if (count($array_preguntas2) > 0) :
        foreach ($array_preguntas2 as $id_pregunta2) :
            $html .= "<td>" . $array_respuesta_socio2[$id_pregunta2] . "</td>";
        endforeach;
    endif;
    $html .= '</tr>';

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
