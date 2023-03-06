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


if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
    $condicion_fecha = " and TiemposSolicitud.FechaTrCr >= '" . $_POST["FechaInicio"] . " 00:00:00'  and TiemposSolicitud.FechaTrCr <= '" . $_POST["FechaFin"] . " 23:59:59'";
}

$sql_reporte = "SELECT 
    TiemposSolicitud.IDTiemposSolicitud,TiemposSolicitud.Comentarios,Usuario.Nombre as NombreUsuario, Socio.Nombre as NombreSocio, TiemposRechazo.Nombre as NombreTiemposRechazo, TiemposSolicitud.FechaTrCr, TiemposSolicitud.IDEstado
FROM TiemposSolicitud 
LEFT JOIN Usuario ON TiemposSolicitud.IDUsuario = Usuario.IDUsuario
LEFT JOIN Socio ON TiemposSolicitud.IDSocio = Socio.IDSocio
LEFT JOIN TiemposRechazo ON TiemposSolicitud.IDTiemposRechazo = TiemposRechazo.IDTiemposRechazo
" .
    "WHERE TiemposSolicitud.IDClub = " . $_POST["IDClub"] . $condicion_fecha . " Order by IDTiemposSolicitud DESC";


$result_reporte = $dbo->query($sql_reporte);

$nombre = "Tiempos_Solicitud_Corte:" . date("Y_m_d");

$NumTiemposSolicitud = $dbo->rows($result_reporte);
$resTiemposSolicitud = $dbo->assoc($result_reporte);

if ($NumTiemposSolicitud > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumTiemposSolicitud . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>NUMERO Solicitud</th>";
    $html .= "<th>Usuario</th>";
    $html .= "<th>Socio</th>";
    $html .= "<th>Fecha Solicitud</th>";

    $sqlPreguntas = "SELECT PreguntaTiempos.IDPreguntaTiempos, PreguntaTiempos.TipoCampo, PreguntaTiempos.EtiquetaCampo FROM PreguntaTiempos  ORDER BY PreguntaTiempos.Orden ASC";
    $queryPreguntas = $dbo->query($sqlPreguntas);

    while ($rowPreguntas = $dbo->assoc($queryPreguntas)) {
        $html .= "<th>" . $rowPreguntas['EtiquetaCampo'] . "</th>";
    }
    $html .= "<th>Estado</th>";
    $html .= "<th>Comentarios</th>";
    $html .= "<th>Tipo Rechazo</th>";


    $html .= "</tr>";
    $result_reporte = $dbo->query($sql_reporte);
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        $html .= "<td>" . $Datos["IDTiemposSolicitud"] . "</td>";
        $html .= "<td>" . $Datos["NombreUsuario"] . "</td>";
        $html .= "<td>" . $Datos["NombreSocio"] . "</td>";
        $html .= "<td>" . $Datos["FechaTrCr"] . "</td>";

        $queryPreguntas = $dbo->query($sqlPreguntas);
        while ($rowPreguntas = $dbo->assoc($queryPreguntas)) {
            $sqlRespuestas = "SELECT TiemposRespuesta.Valor FROM TiemposRespuesta WHERE TiemposRespuesta.IDPreguntaTiempos = " . $rowPreguntas['IDPreguntaTtiempos'] . " AND TiemposRespuesta.IDTiemposSolicitud = " . $Datos['IDTiemposSolicitud'];
            $queryRespuestas = $dbo->query($sqlRespuestas);
            $rowRespuestas = $dbo->assoc($queryRespuestas);
            if ($rowRespuestas['Valor'] != NULL) {
                if ($rowPreguntas['TipoCampo'] == 'imagen' || $rowPreguntas['TipoCampo'] == 'imagenarchivo') {
                    $respuesta = PQR_ROOT . $rowRespuestas['Valor'];
                } else {
                    $respuesta = $rowRespuestas['Valor'];
                }
            } else {
                $respuesta = 'NULL';
            }
            $html .= "<td>" . $respuesta . "</td>";
        }


     //   $html .= "<td>" . SIMResources::$EstadoTiempo[$Datos["IDEstado"]] . "</td>";
        $html .= "<td>" . $Datos["Comentarios"] . "</td>";
        $html .= "<td>" . $Datos["NombreTiemposRechazo"] . "</td>";
        $html .= "</tr>";
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
    </body>;

    </html>
<?php
    exit();
} else {
    echo "NO HAY RESULTADOS EN LAS FECHAS SELECCIONADAS";
}
?>