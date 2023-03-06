<?php

require(dirname(__FILE__) . "/../../admin/config.inc.php");

function remplaza_tildes($texto)
{
    $no_permitidas = array("Ã¡", "Ã©", "Ã", "Ã³", "Ãº", "á", "é", "í­", "á", "ú");
    $permitidas = array("&aacute;", "&eacute;", "&iacute;", "o", "&uacute;", "aaaa");
    $texto_final = str_replace($no_permitidas, $permitidas, $texto);
    return $texto_final;
}


$sql_reporte = "Select *
					From EncuestaRespuesta
					Where IDEncuesta = '" . $_GET["IDEncuesta"] . "'  Order By IDEncuestaRespuesta DESC";
$result_reporte = $dbo->query($sql_reporte);

$nombre = "Registros_ResponderEncuesta:" . date("Y_m_d");

$NumSocios = $dbo->rows($result_reporte);

if ($NumSocios > 0) {
    $html  = "";
    $html .= "<table width='100%' border='1'>";
    $html .= "<tr>";
    $html .= "<th colspan='5'>Se encontraron " . $NumSocios . " registro(s) </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>ENCUESTA</th>";
    $html .= "<th>SOCIO</th>";
    $html .= "<th>ACCION</th>";


    $html .= "<th>VALOR</th>";

    $html .= "<th>FECHA REGISTRO</th>";

    //Consulto los campos dinamicos
    $r_campos = &$dbo->all("Pregunta", "IDEncuesta = '" . $_GET["IDEncuesta"]  . "'");
    while ($r = $dbo->object($r_campos)) :
        $array_campos[] = $r->IDPregunta;
        $html .= "<th>" . $r->EtiquetaCampo . "</th>";
    endwhile;

    //Especial lagartos
    // if($_GET["IDEvento"]==3043){
    // 	 $html .= "<th>MEDIO DE PAGO</th>";
    // }


    $html .= "</tr>";
    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $Datos[IDSocio] . "' ", "array");
        if (empty($datos_socio)) {
            $datos_socio = $dbo->fetchAll("Usuario", " IDUsuario = '" . $Datos[IDSocio] . "' ", "array");
        }
        $bitacora = "";
        unset($array_datos_seguimiento);

        // $corresoc = $dbo->getFields("Socio", "CorreoElectronico", "IDSocio = '" . $Datos["IDSocio"] . "'");
        // if (!empty($corresoc))
        //     $correosoc = "(" . $corresoc . ")";

        $html .= "<tr>";
        $html .= "<td>" . remplaza_tildes(utf8_decode($dbo->getFields("Encuesta", "Nombre", "IDEncuesta = '" . $Datos["IDEncuesta"] . "'"))) . "</td>";
        $html .= "<td>" . utf8_decode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) .  "</td>";
        $html .= "<td>" . utf8_decode($datos_socio["Accion"]) . "</td>";
        $html .= "<td>" . $Datos["Valor"]   . "</td>";
        $html .= "<td>" . $Datos["FechaTrCr"]   . "</td>";

        //Consulto los campos dinamicos
        $r_campos = &$dbo->all("EncuestaRespuesta", "IDEncuesta = '" . $Datos["IDEncuesta"]  . "'");
        while ($rdatos = $dbo->object($r_campos)) :
            $array_otros_datos[$rdatos->IDEncuestaRespuesta][$rdatos->IDPregunta] =  $rdatos->Valor;
        endwhile;

        if (count($array_campos) > 0) :
            foreach ($array_campos as $id_campo) :
                $html .= "<td>" . $array_otros_datos[$Datos["IDEncuestaRespuesta"]][$id_campo] . "</td>";
            endforeach;
        endif;

        //Especial lagartos
        // if ($_GET["IDEvento"] == 3043) {
        //     $html .= "<td>" . $array_otros_datos[$Datos["IDEventoRegistro"]][100000] . "</td>";
        // }

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
