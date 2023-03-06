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

//encapsulamos los parammetros
SIMUser::setFromStructure($datos);
$get = SIMUtil::makeSafe($_GET);

$encuestaPara = $dbo->getFields("EncuestaVial", "DirigidoA", "IDEncuestaVial = " . $get["id"]);


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
    $condicion[] = " E.IDEncuestaVial = " . $get["id"];

$headerPL["f1"] = array("Reporte :" => "Reporte Encuesta Vial");
$headerPL["f2"] = array("Empresa :" => "");
$headerPL["f3"] = array("Fecha Generacion :" => date("d m Y H:i"));
$headerPL["f4"] = array("Formulario :" => "");
$headerPL["f5"] = array("" => "");
$headerPL["f6"] = array("No " => "Pregunta");

!empty($condicion) ? $strCondicion .= " WHERE " . implode(" AND ", $condicion) : "";

$sql_preguntas = " SELECT C.Nombre AS NombreEmpresa,E.Nombre as Encuesta,P.IDPreguntaVial,P.EtiquetaCampo,P.Orden
					FROM EncuestaVial E
						JOIN PreguntaVial P ON P.IDEncuestaVial = E.IDEncuestaVial
						JOIN Club C ON C.IDClub = E.IDClub
					$strCondicion
					ORDER BY P.Orden";


$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
while ($rowPregunta = $dbo->fetchArray($result)) {
    $nombreEmpresa = $rowPregunta["NombreEmpresa"];
    $tituloEncuesta = utf8_decode($rowPregunta["Encuesta"]);
    $headerPL["f" . (5 + $numPregunta)] = array($rowPregunta["IDPreguntaVial"] => utf8_decode($rowPregunta["EtiquetaCampo"]));

    $array_preguntas["_" . $rowPregunta["IDPreguntaVial"]] = "";
    $array_NoPregunta[$numPregunta] = $rowPregunta["IDPreguntaVial"];
    $numPregunta++;
}

$array_preguntasDefault = $array_preguntas;

$headerPL["f2"] = array("Empresa :" => $nombreEmpresa);
$headerPL["f4"] = array("Formulario :" => $tituloEncuesta);

$filename = "Reporte_Encuesta_Vial_" . date("Y_m_d");

if ($encuestaPara == "S") {
    $sql_encuestaVial = "SELECT S.IDSocio,S.NumeroDocumento,CONCAT( S.Nombre, ' ', S.Apellido ) AS Nombre,
			P.IDPreguntaVial,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
			FROM EncuestaVial E
			JOIN PreguntaVial P ON P.IDEncuestaVial = E.IDEncuestaVial
			JOIN EncuestaRespuestaVial ER ON ER.IDPreguntaVial = P.IDPreguntaVial
			JOIN Socio S ON ER.IDSocio = S.IDSocio
			$strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaVialRespuestaVial DESC";
} elseif ($encuestaPara == "E") {
    $sql_encuestaVial = "SELECT U.IDUsuario, U.NumeroDocumento, U.Nombre AS Nombre,
			P.IDPreguntaVial,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
			FROM EncuestaVial E
			JOIN PreguntaVial P ON P.IDEncuestaVial = E.IDEncuestaVial
			JOIN EncuestaRespuestaVial ER ON ER.IDPreguntaVial = P.IDPreguntaVial
			JOIN Usuario U ON ER.IDSocio = U.IDUsuario
			$strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaVialRespuestaVial DESC";
} else {
    $sql_encuestaVial = "SELECT S.IDSocio,S.NumeroDocumento,U.IDUsuario,U.NumeroDocumento,CONCAT( S.Nombre, ' ', S.Apellido ) AS NombreSocio, U.Nombre AS NombreUsuario,
			P.IDPreguntaVial,P.TipoCampo, ER.Valor, DATE(ER.FechaTrCr) AS Fecha, ER.FechaTrCr
			FROM EncuestaVial E
			JOIN PreguntaVial P ON P.IDEncuestaVial = E.IDEncuestaVial
			JOIN EncuestaRespuestaVial ER ON ER.IDPreguntaVial = P.IDPreguntaVial
			JOIN Socio S ON ER.IDSocio = S.IDSocio
			JOIN Usuario U ON ER.IDSocio = U.IDUsuario
			$strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaVialRespuestaVial DESC";
}

$result = $dbo->query($sql_encuestaVial);
$array_preguntas = array();
$arrayItems = array();
$cont = 0;
while ($rowItem = $dbo->fetchArray($result)) {
    if ($encuestaPara == "S") {
        $keyRow = $rowItem["IDSocio"] . $rowItem["FechaTrCr"];

        if (!array_key_exists($keyRow, $arrayItems)) {
            $arrayUsuario =  array(
                'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
                'Nombre' => $rowItem['Nombre'],
                'Area' => $rowItem['Area'],
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
                'Area' => $rowItem['Area'],
                'Fecha' => $rowItem['FechaTrCr'],
            );

            $arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
        }
    } else {
        if (!empty($rowItem["NombreSocio"])) {
            $keyRow = $rowItem["IDSocio"] . $rowItem["FechaTrCr"];

            if (!array_key_exists($keyRow, $arrayItems)) {
                $arrayUsuario =  array(
                    'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
                    'Nombre' => $rowItem['Nombre'],
                    'Area' => $rowItem['Area'],
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
                    'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
                    'Nombre' => $rowItem['Nombre'],
                    'Area' => $rowItem['Area'],
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


    $arrayItems[$keyRow]["_" . $rowItem["IDPreguntaVial"]] = $contenido_resp;
}

//echo count($arrayItems);

$data_export["Respuestas"]  = $arrayItems;

$arrayFiles = $reportObj->exportPHPXLS("Reporte Encuesta Vial ", $data_export, $filename . ".xls", "", TRUE, $headerPL);


exit;
