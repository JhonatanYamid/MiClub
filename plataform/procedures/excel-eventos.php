<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
    $texto = str_replace("ñ", "&ntilde;", $texto);
    $texto = str_replace("á", "&aacute;", $texto);
    $texto = str_replace("é", "&eacute;", $texto);
    $texto = str_replace("í", "&iacute;", $texto);
    $texto = str_replace("ó", "&oacute;", $texto);
    $texto = str_replace("ú", "&uacute;", $texto);
    return $texto;
}

if (!empty($_POST["FechaEvento"]) && !empty($_POST["FechaFinEvento"])) {
    $condicion_fecha = " and FechaEvento  >= '" . $_POST["FechaEvento"] . "'  and FechaEvento <= '" . $_POST["FechaFinEvento"] . "'";
}


$sql_reporte = "Select er.IDSocio,e.DirigidoA,e.Titular,e.Introduccion,e.Lugar,e.Hora,e.FechaEvento
,e.FechaFinEvento,e.EmailContacto,e.Cuerpo,e.FechaInicio,e.FechaFin,e.ValorInscripcion,e.MaximoParticipantes,
e.EmailNotificacionInscripcion,er.FechaTrCr
 From EventoRegistro as er INNER JOIN Evento as e on er.IDEvento = e.IDEvento
					Where er.IDClub = '" . $_POST["IDClub"] . "'  " . $condicion_fecha . " Order By IDEventoRegistro DESC";

$result_reporte = $dbo->query($sql_reporte);

$nombre = "Eventos_Reporte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>IDSOCIO</th>";
    $html .= "<th>SOCIO INSCRITO</th>";
    $html .= "<th>DIRIGIDO A</th>";
    $html .= "<th>TITULAR</th>";
    $html .= "<th>INTRODUCCION</th>";
    $html .= "<th>LUGAR</th>";
    $html .= "<th>HORA</th>";
    $html .= "<th>FECHA EVENTO</th>";
    $html .= "<th>FECHA FIN EVENTO</th>";
    $html .= "<th>EMAIL CONTACTO</th>";
    $html .= "<th>CUERPO</th>";
    $html .= "<th>FECHA INICIO PUBLICACION</th>";
    $html .= "<th>FECHA FIN PUBLICACION</th>";
    $html .= "<th>VALOR INSCRIPCION</th>";
    $html .= "<th>MAXIMO PARTICIPANTES</th>";
    $html .= "<th>EMAIL NOTIFICACION</th>";
    $html .= "<th>FECHA INSCRIPCION SOCIO</th>";



    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {
        // $r_datos = $dbo->fetchArray("EventoRegistro", "IDEvento = '" . $Datos["IDEvento"]  . "'");
        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        $html .= "<td>" . ($Datos["IDSocio"]) . "</td>";
        $html .= "<td>" . remplaza_tildes(utf8_encode(strtoupper($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $Datos["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $Datos["IDSocio"] . "'")))) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["DirigidoA"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Titular"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Introduccion"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Lugar"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Hora"]) . "</td>";
        $html .= "<td>" . $Datos["FechaEvento"]   . "</td>";
        $html .= "<td>" . $Datos["FechaFinEvento"]   . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EmailContacto"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["Cuerpo"]) . "</td>";
        $html .= "<td>" . ($Datos["FechaInicio"]) . "</td>";
        $html .= "<td>" . ($Datos["FechaFin"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["ValorInscripcion"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["MaximoParticipantes"]) . "</td>";
        $html .= "<td>" . remplaza_tildes($Datos["EmailNotificacionInscripcion"]) . "</td>";

        $html .= "<td>" . remplaza_tildes($Datos["FechaTrCr"]) . "</td>";
    }
    $html .= "</table>";

    //construimos el excel
    header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
    header("Content-Disposition: attachment; filename=" . $nombre . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>

    <body>
        <?php
        echo $html;
        ?>
    </body>

    </html>
<?php
    exit();
}
?>