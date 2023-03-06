<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");
session_start();
//handler de sesion
$simsession = new SIMSession(SESSION_LIMIT);

//traemos lo datos de la session
$datos = $simsession->verificar();

if (!is_object($datos)) {
    SIMHTML::jsTopRedirect("/login.php?msg=NSA");
    exit;
} //ebd if

//veriificamos el club de la sesion
if (!empty($_SESSION["club"]))
    $datos->club = $_SESSION["club"];
else
    $datos->club = $datos->IDClub;

//encapsulamos los parametros
SIMUser::setFromStructure($datos);
$get = SIMUtil::makeSafe($_GET);

$encuestaPara = $dbo->getFields("EncuestaArbol", "DirigidoA", "IDEncuestaArbol = " . $get["id"]);


require_once LIBDIR . "/APPReport.class.php";

$reportObj = new APPReport();

// Crear constrain/condiciones para filtrar datos $where
$condicion = array();

$condicion[] = "P.Publicar = 'S'";

!empty(SIMUser::get("club")) ? $condicion[] = "E.IDClub = " . SIMUser::get("club") : exit;

if (!empty($get["FechaInicio"]) && !empty($get["FechaFin"])) {
    $condicionFecha = " ( ER.FechaTrCr >= '" . $get["FechaInicio"] . " 00:00:00'  AND ER.FechaTrCr <= '" . $get["FechaFin"] . " 23:59:59' )";
}
if (!empty($get["id"]))
    $condicion[] = " E.IDEncuestaArbol = " . $get["id"];

$headerPL["f1"] = array("Reporte :" => "Reporte Encuesta");
$headerPL["f2"] = array("Empresa :" => "");
$headerPL["f3"] = array("Fecha Generacion :" => date("d-m-Y H:i"));
$headerPL["f4"] = array("Formulario :" => "");
$headerPL["f5"] = array("" => "");
$headerPL["f6"] = array("No " => "Pregunta");

!empty($condicion) ? $strCondicion .= " WHERE " . implode(" AND ", $condicion) : "";

$sql_preguntas = " SELECT C.Nombre AS NombreEmpresa,E.Nombre as Encuesta,P.IDPreguntaEncuestaArbol,P.EtiquetaCampo,P.Orden,P.NumeroPregunta
					FROM EncuestaArbol E
						JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
						JOIN Club C ON C.IDClub = E.IDClub
					$strCondicion
					ORDER BY P.Orden";


$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
$array_preguntasHijas = array();
while ($rowPregunta = $dbo->fetchArray($result)) {

    // Buscamos si la pregunta tiene preguntas hijas
    $PreguntasSiguientes = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "IDEncuestaArbolPreguntaSiguiente", "IDEncuestaArbolPregunta = " . $rowPregunta['IDPreguntaEncuestaArbol']);
    if ($PreguntasSiguientes != 0) {
        $arr_IDEncuestaArbolPreguntaSiguiente = explode('|', $PreguntasSiguientes);
        foreach ($arr_IDEncuestaArbolPreguntaSiguiente as $idPreguta) {
            $array_preguntasHijas[$idPreguta] = $rowPregunta['IDPreguntaEncuestaArbol'];
        }
    }
    // Fin Buscamos si la pregunta tiene preguntas hijas

    $nombreEmpresa = $rowPregunta["NombreEmpresa"];
    $tituloEncuesta = utf8_decode($rowPregunta["Encuesta"]);
    // if (isset($array_preguntasHijas[$rowPregunta['IDPreguntaEncuestaArbol']])) {
    //     $headerPL["f" . (5 + $numPregunta)] = array($array_preguntasHijas[$rowPregunta['IDPreguntaEncuestaArbol']] . '.' . $rowPregunta["IDPreguntaEncuestaArbol"] => utf8_decode($rowPregunta["EtiquetaCampo"]));
    //     $array_preguntas["_" . $array_preguntasHijas[$rowPregunta['IDPreguntaEncuestaArbol']] . "-" . $rowPregunta["IDPreguntaEncuestaArbol"]] = "";
    // } else {
    //     $headerPL["f" . (5 + $numPregunta)] = array($rowPregunta["IDPreguntaEncuestaArbol"] . "." => utf8_decode($rowPregunta["EtiquetaCampo"]));
    //     $array_preguntas["_" . $rowPregunta["IDPreguntaEncuestaArbol"]] = "";
    // }
    $headerPL["f" . (5 + $numPregunta)] = array($rowPregunta["NumeroPregunta"] . "." => utf8_decode($rowPregunta["EtiquetaCampo"]));
    $array_preguntas["_" . $rowPregunta["NumeroPregunta"]] = "";

    $array_NoPregunta[$numPregunta] = $rowPregunta["NumeroPregunta"];
    $numPregunta++;
}
$array_preguntasDefault = $array_preguntas;
$headerPL["f1"] = array("Reporte :" => $tituloEncuesta);
$headerPL["f2"] = array("Empresa :" => $nombreEmpresa);
$headerPL["f4"] = array("Formulario :" => $tituloEncuesta);

$filename = "Reporte_Encuesta_Arbol_" . date("Y_m_d");

if ($encuestaPara == "S") {
    $sql_encuesta = "SELECT S.IDSocio,S.NumeroDocumento,CONCAT( S.Nombre, ' ', S.Apellido ) AS Nombre,
			P.IDPreguntaEncuestaArbol,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,ER.IDEncuestaArbolOpcionesRespuesta,P.NumeroPregunta
			FROM EncuestaArbol E
			JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
			JOIN EncuestaArbolRespuesta ER ON ER.IDPreguntaEncuestaArbol = P.IDPreguntaEncuestaArbol
			JOIN Socio S ON ER.IDSocio = S.IDSocio
			$strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaArbolRespuesta ASC";
} elseif ($encuestaPara == "E") {
    $sql_encuesta = "SELECT U.IDUsuario, U.NumeroDocumento, U.Nombre AS Nombre,
			P.IDPreguntaEncuestaArbol,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,ER.IDEncuestaArbolOpcionesRespuesta,P.NumeroPregunta
			FROM EncuestaArbol E
			JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
			JOIN EncuestaArbolRespuesta ER ON ER.IDPreguntaEncuestaArbol = P.IDPreguntaEncuestaArbol
			JOIN Usuario U ON ER.IDSocio = U.IDUsuario
			$strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaArbolRespuesta ASC";
} else {
    $sql_encuesta = "SELECT S.IDSocio,S.NumeroDocumento AS DocumentoSocio,U.IDUsuario,U.NumeroDocumento AS DocumentoUsuario,CONCAT( S.Nombre, ' ', S.Apellido ) AS NombreSocio, U.Nombre AS NombreUsuario,
			P.IDPreguntaEncuestaArbol,P.TipoCampo, ER.Valor, DATE(ER.FechaTrCr) AS Fecha, ER.FechaTrCr,ER.IDEncuestaArbolOpcionesRespuesta,P.NumeroPregunta
			FROM EncuestaArbol E
			JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
			JOIN EncuestaArbolRespuesta ER ON ER.IDPreguntaEncuestaArbol = P.IDPreguntaEncuestaArbol
			LEFT JOIN Socio S ON ER.IDSocio = S.IDSocio
			LEFT JOIN Usuario U ON ER.IDSocio = U.IDUsuario
			$strCondicion AND $condicionFecha
			ORDER BY P.Orden ASC";
}
// echo $sql_encuesta;
// die();
$result = $dbo->query($sql_encuesta);
$array_preguntas = array();
$arrayItems = array();
$cont = 0;
$Puntos = array();
$PuntosOpcionRespuesta = 0;
while ($rowItem = $dbo->fetchArray($result)) {
    $fecha = date('Y-m-d H:i', strtotime($rowItem['FechaTrCr']));
    if ($rowItem['IDSocio'] > 0) {
        $id = $rowItem['IDSocio'];
    } else {
        $id = $rowItem['IDUsuario'];
    }
    // echo $rowItem['IDEncuestaArbolOpcionesRespuesta'] . "--" . $Opcion = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Opcion", "IDEncuestaArbolOpcionesRespuesta = " . $rowItem['IDEncuestaArbolOpcionesRespuesta']);
    // if ($rowItem['IDEncuestaArbolOpcionesRespuesta'] > 0) {
    //     $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "IDEncuestaArbolOpcionesRespuesta = " . $rowItem['IDEncuestaArbolOpcionesRespuesta']);
    //     $Puntos[$id . $fecha] += $PuntosOpcionRespuesta;
    // }
    $arr_Respuesta = explode(',', $rowItem['Valor']);
    if (count($arr_Respuesta) > 1) {
        foreach ($arr_Respuesta as $Respuesta) {
            $Respuesta = trim($Respuesta);
            $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "Opcion like '%" . $Respuesta . "%' AND IDEncuestaArbolPregunta = " . $rowItem['IDPreguntaEncuestaArbol']);
            $Puntos[$id . $fecha] += $PuntosOpcionRespuesta;
        }
    } else {
        if ($rowItem['IDEncuestaArbolOpcionesRespuesta'] > 0) {
            $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "IDEncuestaArbolOpcionesRespuesta = " . $rowItem['IDEncuestaArbolOpcionesRespuesta']);
            $Puntos[$id . $fecha] += $PuntosOpcionRespuesta;
        }
    }
    $PuntosPregunta = $dbo->getFields("PreguntaEncuestaArbol", "Puntos", "IDPreguntaEncuestaArbol=" . $rowItem['IDPreguntaEncuestaArbol']);
    $Puntos[$id . $fecha] += $PuntosPregunta;

    if ($encuestaPara == "S") {
        $keyRow = $rowItem["IDSocio"] . $rowItem["FechaTrCr"];

        if (!array_key_exists($keyRow, $arrayItems)) {
            $arrayUsuario =  array(
                'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
                'Nombre' => $rowItem['Nombre'],
                // 'Area' => $rowItem['Area'],
                'Fecha' => $rowItem['FechaTrCr'],
            );

            $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
        }
    } elseif ($encuestaPara == "E") {
        $keyRow = $rowItem["IDUsuario"] . $rowItem["FechaTrCr"];

        if (!array_key_exists($keyRow, $arrayItems)) {
            $arrayUsuario =  array(
                'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
                'Nombre' => $rowItem['Nombre'],
                // 'Area' => $rowItem['Area'],
                'Fecha' => $rowItem['FechaTrCr'],
            );
            $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
        }
    } else {
        if (!empty($rowItem["NombreSocio"])) {
            $keyRow = $rowItem["IDSocio"] . $rowItem["FechaTrCr"];

            if (!array_key_exists($keyRow, $arrayItems)) {
                $arrayUsuario =  array(
                    'NO_DOCUMENTO' => $rowItem["DocumentoSocio"],
                    'Nombre' => $rowItem['NombreSocio'],
                    // 'Area' => $rowItem['Area'],
                    'Fecha' => $rowItem['FechaTrCr'],
                );
                $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
            }
        }
        //verifico si saco la info del empleado
        else {
            $keyRow = $rowItem["IDUsuario"] . $rowItem["FechaTrCr"];

            if (!array_key_exists($keyRow, $arrayItems)) {
                $arrayUsuario =  array(
                    'NO_DOCUMENTO' => $rowItem["DocumentoUsuario"],
                    'Nombre' => $rowItem['NombreUsuario'],
                    // 'Area' => $rowItem['Area'],
                    'Fecha' => $rowItem['FechaTrCr'],
                );
                $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
            }
        }
    }

    if ($rowItem["TipoCampo"] == "imagen" && !empty($rowItem["Valor"])) {
        $ruta_imagen = PQR_ROOT . $rowItem["Valor"];
        $contenido_resp = "<a href='" . $ruta_imagen . "'>Ver imagen: </a>" . $ruta_imagen;
    } else {
        $contenido_resp = $rowItem["Valor"];
    }

    // if (!empty($array_preguntasHijas[$rowItem['IDPreguntaEncuestaArbol']])) {
    //     $arrayItems[$keyRow]["_" . $array_preguntasHijas[$rowItem['IDPreguntaEncuestaArbol']] . "-" . $rowItem["IDPreguntaEncuestaArbol"]] = $contenido_resp;
    // } else {
    //     $arrayItems[$keyRow]["_" . $rowItem["IDPreguntaEncuestaArbol"]] = $contenido_resp;
    // }
    $arrayItems[$keyRow]["_" . $rowItem["NumeroPregunta"]] = $contenido_resp;

    $arrayItems[$keyRow]["Puntos"] = $Puntos[$id . $fecha];
    unset($PuntosOpcionRespuesta);
    unset($PuntosPregunta);
}
//echo count($arrayItems);

$data_export["Respuestas"]  = $arrayItems;

// echo '<pre>';
// // print_r($headerPL);
// print_r($data_export);
// die();

$arrayFiles = $reportObj->exportPHPXLS("Reporte Encuesta Arbol ", $data_export, $filename . ".xls", "", TRUE, $headerPL);


exit;
