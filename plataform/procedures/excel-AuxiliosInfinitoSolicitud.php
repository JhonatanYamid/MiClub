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
   
    $condicion_fecha = " and AuxiliosInfinitoSolicitud.FechaTrCr >= '" . $_POST["FechaInicio"] . " 00:00:00'  and AuxiliosInfinitoSolicitud.FechaTrCr <= '" . $_POST["FechaFin"] . " 23:59:59'";
    print_r($condicion_fecha);
}

$sql_reporte = "SELECT 
    AuxiliosInfinitoSolicitud.IDAuxiliosInfinitoSolicitud,AuxiliosInfinitoSolicitud.Comentarios,Usuario.Nombre as NombreUsuario, Socio.Nombre as NombreSocio, AuxiliosInfinitoRechazo.Nombre as NombreAuxiliosInfinitoRechazo, AuxiliosInfinitoSolicitud.FechaTrCr, AuxiliosInfinitoSolicitud.IDEstado
FROM AuxiliosInfinitoSolicitud 
LEFT JOIN Usuario ON AuxiliosInfinitoSolicitud.IDUsuario = Usuario.IDUsuario
LEFT JOIN Socio ON AuxiliosInfinitoSolicitud.IDSocio = Socio.IDSocio
LEFT JOIN AuxiliosInfinitoRechazo ON AuxiliosInfinitoSolicitud.IDAuxiliosInfinitoRechazo = AuxiliosInfinitoRechazo.IDAuxiliosInfinitoRechazo
" .
    "WHERE AuxiliosInfinitoSolicitud .IDClub = " . $_POST["IDClub"] . $condicion_fecha . " Order by IDAuxiliosInfinitoSolicitud DESC";


$result_reporte = $dbo->query($sql_reporte);

$nombre = "Auxilios_Infinito_Solicitud_Corte:" . date("Y_m_d");

$NumAuxiliosSolicitud = $dbo->rows($result_reporte);
$resAuxiliosSolicitud = $dbo->assoc($result_reporte);

if ($NumAuxiliosSolicitud > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumAuxiliosSolicitud . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>NUMERO Solicitud</th>";
    $html .= "<th>Usuario</th>";
    $html .= "<th>Socio</th>";
    $html .= "<th>Fecha Solicitud</th>";

    $sqlPreguntas = "SELECT PreguntaAuxiliosInfinito.IDPreguntaAuxiliosInfinito, PreguntaAuxiliosInfinito.TipoCampo, PreguntaAuxiliosInfinito.EtiquetaCampo FROM PreguntaAuxiliosInfinito  ORDER BY PreguntaAuxiliosInfinito.Orden ASC";
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
        $html .= "<td>" . $Datos["IDAuxiliosInfinitoSolicitud"] . "</td>";
        $html .= "<td>" . $Datos["NombreUsuario"] . "</td>";
        $html .= "<td>" . $Datos["NombreSocio"] . "</td>";
        $html .= "<td>" . $Datos["FechaTrCr"] . "</td>";

        $queryPreguntas = $dbo->query($sqlPreguntas);
        while ($rowPreguntas = $dbo->assoc($queryPreguntas)) {
            $sqlRespuestas = "SELECT AuxiliosInfinitoRespuesta.Valor FROM AuxiliosInfinitoRespuesta WHERE AuxiliosInfinitoRespuesta.IDPreguntaAuxiliosInfinito = " . $rowPreguntas['IDPreguntaAuxiliosInfinito'] . " AND AuxiliosInfinitoRespuesta.IDAuxiliosInfinitoSolicitud = " . $Datos['IDAuxiliosInfinitoSolicitud'];
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


        $html .= "<td>" . SIMResources::$EstadoAuxilio[$Datos["IDEstado"]] . "</td>";
        $html .= "<td>" . $Datos["Comentarios"] . "</td>";
        $html .= "<td>" . $Datos["NombreAuxiliosInfinitoRechazo"] . "</td>";
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