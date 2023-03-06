<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm_get = SIMUtil::makeSafe($_GET);
// ID encuesta
$IDEncuestaArbol = 9;
$ObjetivoIPP = 65.00;

$IDClub = SIMUser::get('club');

$encuestaPara = $dbo->getFields("EncuestaArbol", "DirigidoA", "IDEncuestaArbol = " . $IDEncuestaArbol);

$table = "EncuestaArbol";
$key = "IDEncuestaArbol";
$where = " WHERE E.IDClub = '" . $IDClub . "'";

$oper = SIMNet::req("oper");
if (isset($frm_get['oper']) && $frm_get['oper'] == 'searchurl') {
    if (!empty($frm_get["FechaHistorico"])) {
        $fechaFin = date('Y-m-t', strtotime($frm_get['FechaHistorico']));
        $fechaInicio = date('Y-m', strtotime('-3 months', strtotime($frm_get['FechaHistorico'])));
        $array_where[] = "ER.FechaTrCr >= '" . $fechaInicio . "-01 00:00:00'";
        $array_where[] = "ER.FechaTrCr <= '" . $fechaFin . " 00:00:00'";
        $fecha = date('Y-m', strtotime($frm_get['FechaHistorico'])) . "-20";
    }
    if (!empty($frm_get["Categoria"])) {
        $array_where[] = "P.IDCategoriaEncuestaArbol = " . $frm_get['Categoria'];
    }
    if (count($array_where) > 0) :
        $where_filtro = " and " . implode(" and ", $array_where);
    endif;
}
if ($frm_get['_search'] == "true") {
    $filters = stripslashes(stripslashes(htmlspecialchars_decode(SIMNet::req("filters"))));
    $array_buqueda = json_decode($filters);
    foreach ($array_buqueda->rules as $key_busqueda => $search_object) {
        switch ($search_object->field) {
            case 'Zona':

                $where .= " AND (  A.Nombre LIKE '%" . $search_object->data . "%' )  ";
                break;
            case 'Ejecutivos':
                $where .= " AND (  S.Nombre LIKE '%" . $search_object->data . "%' OR S.Apellido LIKE '%" . $search_object->data . "%' OR U.Nombre LIKE '%" . $search_object->data . "%') ";
                break;

            case 'Objetivos':
                $where .= " AND S.ObjetivoEncuesta = '$search_object->data'";
                break;

            case 'Ideal':
                $where .= " AND S.CumplimientoIdeal = '$search_object->data'";
                break;

            default:
                break;
        }
    } //end for
}

if (empty($frm_get['FechaHistorico'])) {
    $fechaFin = date('Y-m-t');
    $fechaInicio = date('Y-m', strtotime('-3 months', strtotime($fechaFin)));
    $array_where[] = "ER.FechaTrCr >= '" . $fechaInicio . "-01 00:00:00'";
    $array_where[] = "ER.FechaTrCr <= '" . $fechaFin . " 00:00:00'";
    $fecha = date('Y-m') . "-20";
}
// $FechaInicioTrimestre = strtotime('-3 months', strtotime($fecha));
// $FechaInicioTrimestre = date('Y-m', $FechaInicioTrimestre);

if (count($array_where) > 0) :
    $where_filtro = " and " . implode(" and ", $array_where);
endif;


// $array_where[] = "ER.FechaTrCr >= '" . $FechaInicioTrimestre . "-01 00:00:00'";




$sql = "SELECT C.Nombre as Ciudad,S.IDSocio, U.IDUsuario, CONCAT( S.Nombre, ' ',S.Apellido) AS NombreSocio, U.Nombre AS NombreFuncionario,
					P.IDPreguntaEncuestaArbol,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,CONCAT(DATE(ER.FechaTrCr),' ',HOUR(ER.FechaTrCr),':',MINUTE(ER.FechaTrCr)) AS FechaRespuesta ,ER.IDEncuestaArbolOpcionesRespuesta,S.IDAreaSocio
					FROM EncuestaArbol E
					JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
					JOIN EncuestaArbolRespuesta ER ON ER.IDPreguntaEncuestaArbol = P.IDPreguntaEncuestaArbol
					LEFT JOIN Socio S ON ER.IDSocio = S.IDSocio
					LEFT JOIN Usuario U ON ER.IDSocio = U.IDUsuario
					LEFT JOIN AreaSocio A ON S.IDAreaSocio = A.IDAreaSocio
					LEFT JOIN Ciudad C ON S.IDCiudad = C.IDCiudad
                    LEFT JOIN CategoriaEncuestaArbol CA ON P.IDCategoriaEncuestaArbol=CA.IDCategoriaEncuestaArbol
                    $where
					AND E.IDEncuestaArbol = " . $IDEncuestaArbol . "
					AND P.Publicar = 'S'
                    -- and ER.FechaTrCr = '2023-01-16 17:46:18'
                    $where_filtro
					ORDER BY ER.IDEncuestaArbolRespuesta DESC";

// ORDER BY Fecha,S.IDSocio,P.Orden
$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid

$result = $dbo->query($sql);

$cantidad = count($array_preguntas);
$array_preguntas = array();
while ($rowItem = $dbo->fetchArray($result)) {

    // $fecha = date('Y-m-t H:i', strtotime($rowItem['FechaTrCr']));
    $mes = date('m', strtotime($rowItem['FechaTrCr']));
    $mes = intval($mes);
    $Ciudad = (!empty($rowItem['Ciudad'])) ? $rowItem['Ciudad'] : "Otra";

    $arr_historico[$Ciudad][intval(date('m', strtotime('-0 months', strtotime($fecha))))] += 0;
    $arr_historico[$Ciudad][intval(date('m', strtotime('-1 months', strtotime($fecha))))] += 0;
    $arr_historico[$Ciudad][intval(date('m', strtotime('-2 months', strtotime($fecha))))] += 0;
    $arr_historico[$Ciudad][intval(date('m', strtotime('-3 months', strtotime($fecha))))] += 0;
    if ($rowItem['IDSocio'] > 0) {
        $id = $rowItem['IDSocio'];
    } else {
        $id = $rowItem['IDUsuario'];
    }


    $arr_Respuesta = explode(',', $rowItem['Valor']);
    if (count($arr_Respuesta) > 1) {
        foreach ($arr_Respuesta as $Respuesta) {
            $Respuesta = trim($Respuesta);
            $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "Opcion like '%" . $Respuesta . "%' AND IDEncuestaArbolPregunta = " . $rowItem['IDPreguntaEncuestaArbol']);
            $Puntos += $PuntosOpcionRespuesta;
        }
    } else {
        if ($rowItem['IDEncuestaArbolOpcionesRespuesta'] > 0) {
            $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "IDEncuestaArbolOpcionesRespuesta = " . $rowItem['IDEncuestaArbolOpcionesRespuesta']);
            $Puntos += $PuntosOpcionRespuesta;
        }
    }
    $PuntosPregunta = $dbo->getFields("PreguntaEncuestaArbol", "Puntos", "IDPreguntaEncuestaArbol=" . $rowItem['IDPreguntaEncuestaArbol']);
    $Puntos += $PuntosPregunta;
    $arr_historico[$Ciudad][intval($mes)] += $Puntos;

    unset($PuntosOpcionRespuesta);
    unset($PuntosPregunta);
    unset($Puntos);
}


$count = count($arr_historico);
if ($count > 0) {
    $total_pages = ceil($count / $limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) {
    $page = $total_pages;
}

$start = $limit * $page - $limit; // do not put $limit*($page - 1)

if (empty($limit)) {
    $limit = 1000000;
}

$responce->page = (int) $page;
$responce->total = (int) $total_pages;
$responce->records = (int) $count;
$i = 0;
foreach ($arr_historico as $Ciudad => $Meses) {
    $countMeses = count($Meses);
    $arr_mes = array();
    $data = array(
        "Ciudad" => $Ciudad,
    );
    $mes0 = 0;
    $Mes1 = 0;
    $Mes2 = 0;
    $Mes3 = 0;
    $VSObj = 0;
    $VSMesAnt = 0;
    $VSTrimestre = 0;
    $a = 0;
    foreach ($Meses as $mes => $Puntos) {
        $dataMeses['mes' . (int)$a] = $Puntos;
        if ($a == 3) {
            $Mes0 = $Puntos;
        }
        if ($a == 0) {
            $Mes1 = $Puntos;
        }
        if ($a == 1) {
            $Mes2 = $Puntos;
        }
        if ($a == 2) {
            $Mes3 = $Puntos;
        }

        $a++;
    }
    $VSObj = $Mes0 - $ObjetivoIPP;
    $VSMesAnt = $Mes0 - $Mes1;
    $VSTrimestre = $Mes0 - (($Mes1 + $Mes2 + $Mes3) / 3);


    ksort($dataMeses);
    $Calculos = array(
        "ObjIpp" => number_format($ObjetivoIPP, 2),
        "VsObj" => number_format($VSObj, 2),
        "VsMesAnt" => number_format($VSMesAnt, 2),
        "VsTrim" => number_format($VSTrimestre, 2),
    );

    $datos = array_merge($dataMeses, $Calculos);
    $responce->rows[$i]['cell'] = array_merge($data, $datos);


    unset($dataMeses);
    $i++;
}
// echo '<pre>';
// print_r($responce);
// die();
echo json_encode($responce);
