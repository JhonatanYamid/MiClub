<?php

require dirname(__FILE__) . "/../../admin/config.inc.php";

function remplaza_tildes($texto)
{
    $no_permitidas = array("Ã¡", "Ã©", "Ã", "Ã³", "Ãº", "á", "é", "í­", "á", "ú");
    $permitidas = array("&aacute;", "&eacute;", "&iacute;", "o", "&uacute;", "aaaa");
    $texto_final = str_replace($no_permitidas, $permitidas, $texto);
    return $texto_final;
}

if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
    $condicion_fecha = " and DR.FechaTrCr >= '" . $_POST["FechaInicio"] . " 00:00:00'  and DR.FechaTrCr <= '" . $_POST["FechaFin"] . " 23:59:59'";
}

$sql_reporte = "SELECT IDSocio,IDUsuario,TipoUsuario,PD.IDPreguntaEncuestaArbol,NumeroDocumento,TipoUsuario,Nombre,DR.FechaTrCr
					FROM PreguntaEncuestaArbol PD,EncuestaArbolRespuesta DR
					Where PD.IDPreguntaEncuestaArbol=DR.IDPreguntaEncuestaArbol and PD.IDEncuestaArbol = '" . $_POST["IDEncuestaArbol"] . "' and TipoUsuario = 'Socio' " . $condicion_fecha . "
					Group by IDSocio,NumeroDocumento,DR.FechaTrCr";


$result_reporte = $dbo->query($sql_reporte);

$datos_encuesta = $dbo->fetchAll("EncuestaArbol", " IDEncuestaArbol = '" . $_POST["IDEncuestaArbol"] . "' ", "array");

$nombre = "Registros_Corte:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>ENCUESTA</th>";
    $html .= "<th>CEDULA</th>";
    $html .= "<th>TIPO</th>";
    $html .= "<th>ACCION</th>";
    $html .= "<th>USUARIO</th>";
    //Consulto los campos dinamicos
    $r_campos = &$dbo->all("PreguntaEncuestaArbol", "IDEncuestaArbol = '" . $_POST["IDEncuestaArbol"] . "' Order by IDPreguntaEncuestaArbol");
    while ($r = $dbo->object($r_campos)):
        $array_preguntas[] = $r->IDPreguntaEncuestaArbol;
        $html .= "<th>" . utf8_decode($r->EtiquetaCampo) . "</th>";
    endwhile;
    $html .= "<th>FECHA</th>";
    $html .= "</tr>";

    while ($Datos = $dbo->fetchArray($result_reporte)) {
        unset($array_respuesta_socio);
        $Fecha = "";

        if ($datos_encuesta["DirigidoA"] == "E") {
            $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $Datos[IDSocio] . "' ", "array");
            $NombreResponde = $datos_usuario["Nombre"];
        } else {
            $datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $Datos[IDSocio] . "' ", "array");
            $NombreResponde = $datos_usuario["Nombre"] . " " . $datos_usuario["Apellido"];
        }

        if ($Datos["TipoUsuario"] == "Externo") {
            $datos_usuario["NumeroDocumento"] = $Datos["NumeroDocumento"];
            $NombreResponde = $Datos["Nombre"];
        }

        $bitacora = "";
        unset($array_datos_seguimiento);
        $html .= "<tr>";
        $html .= "<td>" . remplaza_tildes(utf8_encode($dbo->getFields("EncuestaArbol", "Nombre", "IDEncuestaArbol = '" . $_POST["IDEncuestaArbol"] . "'"))) . "</td>";
        $html .= "<td>" . $datos_usuario["NumeroDocumento"] . "</td>";
        $html .= "<td>" . $datos_usuario["TipoSocio"] . "</td>";
        $html .= "<td>" . $datos_usuario["Accion"] . "</td>";
        $html .= "<td>" . $NombreResponde . "</td>";

        $sql_repuesta_socio = "Select * From EncuestaArbolRespuesta DR Where IDSocio = '" . $Datos[IDSocio] . "' and FechaTrCr = '" . $Datos["FechaTrCr"] . "' " . $condicion_fecha . " Group by IDPreguntaEncuestaArbol";
        $r_respuesta_socio = $dbo->query($sql_repuesta_socio);
        while ($row_respuesta = $dbo->fetchArray($r_respuesta_socio)):
            $array_respuesta_socio[$row_respuesta["IDPreguntaEncuestaArbol"]] = $row_respuesta["Valor"];
            $Fecha = $row_respuesta["FechaTrCr"];
        endwhile;

        if (count($array_preguntas) > 0):
            foreach ($array_preguntas as $id_pregunta):
                $html .= "<td>" . utf8_decode($array_respuesta_socio[$id_pregunta]) . "</td>";
            endforeach;
        endif;

        $html .= "<td>" . $Fecha . "</td>";
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

} else {
    echo " No se encontraron registros";
}
