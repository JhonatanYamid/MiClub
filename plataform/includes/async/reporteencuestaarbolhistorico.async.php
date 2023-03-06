<?php
include "../../procedures/general_async.php";
SIMUtil::cache("text/json");
$dbo = &SIMDB::get();

$frm_get = SIMUtil::makeSafe($_GET);
// ID encuesta
$IDEncuestaArbol = 9;
$IDClub = SIMUser::get('club');

$encuestaPara = $dbo->getFields("EncuestaArbol", "DirigidoA", "IDEncuestaArbol = " . $IDEncuestaArbol);

$table = "EncuestaArbol";
$key = "IDEncuestaArbol";
$where = " WHERE E.IDClub = '" . $IDClub . "'";

$oper = SIMNet::req("oper");

if (isset($frm_get['oper']) && $frm_get['oper'] == 'searchurl') {
    if (!empty($frm_get["FechaInicio"])) {
        $array_where[] = "ER.FechaTrCr >= '" . $frm_get['FechaInicio'] . " 00:00:00'";
    }
    if (!empty($frm_get["FechaFin"])) {
        $array_where[] = "ER.FechaTrCr <= '" . $frm_get['FechaFin'] . " 00:00:00'";
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

if (!isset($frm_get['oper']) && $frm_get['_search'] != "true") {
    $fecha_inicio = date('Y-m-01');
    $fecha_fin = date('Y-m-t');
    $where_filtro = " and (ER.FechaTrCr >= '" . $fecha_inicio . " 00:00:00' and ER.FechaTrCr <= '" . $fecha_fin . " 23:59:59')";
}

$sql_preguntas = " SELECT P.IDPreguntaEncuestaArbol,P.EtiquetaCampo,P.Orden
							FROM PreguntaEncuestaArbol P
							WHERE 	P.IDEncuestaArbol = " . $IDEncuestaArbol . "
									AND P.Publicar = 'S'
									ORDER BY P.Orden";

$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
$array_NoPregunta = array();


while ($rowPregunta = $dbo->fetchArray($result)) {

    $array_preguntas["_" . $rowPregunta["IDPreguntaEncuestaArbol"]] = "";
    $array_NoPregunta[$rowPregunta["IDPreguntaEncuestaArbol"]] = $rowPregunta["IDPreguntaEncuestaArbol"];
}

$array_preguntasDefault = $array_preguntas;

$sql = "SELECT S.IDSocio, U.IDUsuario, CONCAT( S.Nombre, ' ',S.Apellido) AS NombreSocio, U.Nombre AS NombreFuncionario,
					P.IDPreguntaEncuestaArbol,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,CONCAT(DATE(ER.FechaTrCr),' ',HOUR(ER.FechaTrCr),':',MINUTE(ER.FechaTrCr)) AS FechaRespuesta ,ER.IDEncuestaArbolOpcionesRespuesta,S.IDAreaSocio
					FROM EncuestaArbol E
					JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
					JOIN EncuestaArbolRespuesta ER ON ER.IDPreguntaEncuestaArbol = P.IDPreguntaEncuestaArbol
					LEFT JOIN Socio S ON ER.IDSocio = S.IDSocio
					LEFT JOIN Usuario U ON ER.IDSocio = U.IDUsuario
					LEFT JOIN AreaSocio A ON S.IDAreaSocio = A.IDAreaSocio
                    $where
					AND E.IDEncuestaArbol = " . $IDEncuestaArbol . "
					AND P.Publicar = 'S'
                    $where_filtro
					ORDER BY ER.IDEncuestaArbolRespuesta DESC";

// ORDER BY Fecha,S.IDSocio,P.Orden
$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid


$result = $dbo->query($sql);

//    echo $responce->records;
//    $numPregunta = 1;
$cantidad = count($array_preguntas);
$array_preguntas = array();
$arrayItems = array();
$Arr_AreaSocio = array();
$contPuntos = 0;
$cont = 0;
while ($rowItem = $dbo->fetchArray($result)) {
    $fecha = date('Y-m-d H:i', strtotime($rowItem['FechaTrCr']));
    if ($rowItem['IDSocio'] > 0) {
        $id = $rowItem['IDSocio'];
    } else {
        $id = $rowItem['IDUsuario'];
    }

    // AreaSocio - Area
    $AreaSocio = $dbo->getFields('AreaSocio', 'Nombre', "IDAreaSocio= " . $rowItem['IDAreaSocio']);
    $FechaEncuesta = strtotime($rowItem['FechaRespuesta']);
    $Arr_AreaSocio[$AreaSocio][$rowItem['IDSocio']][$FechaEncuesta] = 1;

    // $arr_Respuesta = explode(',', $rowItem['Valor']);
    // if (count($arr_Respuesta) > 1) {
    //     foreach ($arr_Respuesta as $Respuesta) {
    //         $Respuesta = trim($Respuesta);
    //         $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "Opcion like '%" . $Respuesta . "%' AND IDEncuestaArbolPregunta = " . $rowItem['IDPreguntaEncuestaArbol']);
    //         $Puntos[$id . $fecha] += $PuntosOpcionRespuesta;
    //     }
    // } else {
    //     if ($rowItem['IDEncuestaArbolOpcionesRespuesta'] > 0) {
    //         $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "IDEncuestaArbolOpcionesRespuesta = " . $rowItem['IDEncuestaArbolOpcionesRespuesta']);
    //         $Puntos[$id . $fecha] += $PuntosOpcionRespuesta;
    //     }
    // }
    // $PuntosPregunta = $dbo->getFields("PreguntaEncuestaArbol", "Puntos", "IDPreguntaEncuestaArbol=" . $rowItem['IDPreguntaEncuestaArbol']);
    // $Puntos[$id . $fecha] += $PuntosPregunta;



    // $arrayItems[$keyRow]["_" . $rowItem["IDPreguntaEncuestaArbol"]] = $contenido_resp;
    // $arrayItems[$keyRow]["Puntos"] = $Puntos[$id . $fecha];


    unset($PuntosOpcionRespuesta);
    unset($PuntosPregunta);
}
$count = count($arrayItems);
//exit;
// echo '<pre>';
// print_r($Arr_AreaSocio);
// die();
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

foreach ($Arr_AreaSocio as $key => $area) {
    foreach ($area as $ID => $ejecutivo) {
        $CountEncuesta = 0;
        // foreach ($ejecutivo as $Encuesta) {
        //     $CountEncuesta += $Encuesta;
        // }
        if ($encuestaPara == 'E') {
            $responce->rows[$i]['id'] = $ID;
        } elseif ($encuestaPara == 'S') {
            $responce->rows[$i]['id'] = $ID;
            $s_socio = "select Nombre,Apellido,240 as Objetivo ,157 as Ideal from Socio where IDClub = " . SIMUser::get("club") . " and IDSocio = $ID Limit 1";
            $q_socio = $dbo->query($s_socio);
            $socio = $dbo->assoc($q_socio);
        } else {
            if (!empty($rowItem["NombreSocio"])) {
                $responce->rows[$i]['id'] = $ID;
                $s_socio = "select Nombre,Apellido,240 as Objetivo ,157 as Ideal from Socio where IDClub = " . SIMUser::get("club") . " and IDSocio = $ID Limit 1";
                $q_socio = $dbo->query($s_socio);
                $socio = $dbo->assoc($q_socio);
            } else {
                $responce->rows[$i]['id'] = $ID;
            }
        }

        $Objetivo = $socio['Objetivo'];
        $Ideal = $socio['Ideal'];
        $Encuestas = count($ejecutivo);
        $Cumplimiento = $Encuestas / $Objetivo * 100;
        $Faltante = $Ideal - $Encuestas;
        $responce->rows[$i]['cell'] = array(
            "Zona" => $key,
            "Ejecutivos" => $socio['Nombre'] . ' ' . $socio['Apellido'],
            "Objetivos" => $socio['Objetivo'],
            "Encuestas" =>  $Encuestas,
            "Cumplimiento" => number_format($Cumplimiento, 2, '.', ',') . '%',
            "Ideal" => $Ideal,
            "Faltante" => $Faltante,
            "Proyeccion_cierre" => 1,
            "Proyeccion" => 1,
        );
    }

    $i++;
}
// echo '<pre>';
// print_r($responce);
// die();
echo json_encode($responce);
