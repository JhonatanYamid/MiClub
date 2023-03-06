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

$encuestaPara = $dbo->getFields("Encuesta", "DirigidoA", "IDEncuesta = " . $get["id"]);


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
	$condicion[] = " E.IDEncuesta = " . $get["id"];

$headerPL["f1"] = array("Reporte :" => "Reporte Encuestas");
$headerPL["f2"] = array("Empresa :" => "");
$headerPL["f3"] = array("Fecha Generacion :" => date("d m Y H:i"));
$headerPL["f4"] = array("Formulario :" => "");
$headerPL["f5"] = array("" => "");
$headerPL["f6"] = array("No " => "Pregunta");

!empty($condicion) ? $strCondicion .= " " . implode(" AND ", $condicion) : "";

$sql_preguntas = " SELECT C.Nombre AS NombreEmpresa,E.Nombre as Encuesta,P.IDPregunta,P.EtiquetaCampo,P.Orden
					FROM Encuesta E
						JOIN Pregunta P ON P.IDEncuesta = E.IDEncuesta
						JOIN Club C ON C.IDClub = E.IDClub
						WHERE 
					$strCondicion
					ORDER BY P.Orden";


$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
while ($rowPregunta = $dbo->fetchArray($result)) {
	$nombreEmpresa = $rowPregunta["NombreEmpresa"];
	$tituloEncuesta = utf8_decode($rowPregunta["Encuesta"]);
	$headerPL["f" . (5 + $numPregunta)] = array($rowPregunta["IDPregunta"] => utf8_decode($rowPregunta["EtiquetaCampo"]));

	$array_preguntas["_" . $rowPregunta["IDPregunta"]] = "";
	$array_NoPregunta[$numPregunta] = $rowPregunta["IDPregunta"];
	$numPregunta++;
}

$array_preguntasDefault = $array_preguntas;

$headerPL["f2"] = array("Empresa :" => $nombreEmpresa);
$headerPL["f4"] = array("Formulario :" => $tituloEncuesta);

$filename = "Reporte_Encuesta_" . date("Y_m_d");

if ($encuestaPara == "S") {
	$sql_diagnostico = "SELECT S.IDSocio,S.NumeroDocumento,CONCAT( S.Nombre, ' ', S.Apellido ) AS Nombre,
			P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr, S.Predio,S.NumeroDeCasa,S.Accion,S.Area
			FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Socio S
			WHERE  P.IDEncuesta = E.IDEncuesta 
                AND ER.IDPregunta = P.IDPregunta 
                AND ER.IDSocio = S.IDSocio 
                AND $strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaRespuesta DESC";
} elseif ($encuestaPara == "E") {
	$sql_diagnostico = "SELECT U.IDUsuario, U.NumeroDocumento, U.Nombre AS Nombre,U.Area
			P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr
			FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Usuario U
			WHERE
                P.IDEncuesta = E.IDEncuesta
                AND ER.IDPregunta = P.IDPregunta
                AND ER.IDSocio = U.IDUsuario
                AND $strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaRespuesta DESC";
}

/* echo $sql_diagnostico;
exit; */

$result = $dbo->query($sql_diagnostico);
$array_preguntas = array();
$arrayItems = array();
$cont = 0;

if ($encuestaPara == "S" || $encuestaPara == "E") :

	while ($rowItem = $dbo->fetchArray($result)) {
		if ($encuestaPara == "S") {
			$keyRow = $rowItem["IDSocio"] . $rowItem["FechaTrCr"];

			if (!array_key_exists($keyRow, $arrayItems)) {
				$arrayUsuario =  array(
					'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
					'Accion' => $rowItem["Accion"],
					'Nombre' => $rowItem['Nombre'],
					'Predio' => $rowItem['Predio'],
					'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
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
					'Accion' => $rowItem["Accion"],
					'Nombre' => $rowItem['Nombre'],
					'Predio' => $rowItem['Predio'],
					'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
					'Area' => $rowItem['Area'],
					'Fecha' => $rowItem['FechaTrCr'],
				);

				$arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
			}
		}

		if ($rowItem["TipoCampo"] == "imagen" && !empty($rowItem["Valor"])) {
			$ruta_imagen = PQR_ROOT . $rowItem["Valor"];
			$contenido_resp = "<a href='" . $ruta_imagen . "'>Ver imagen: </a>" . $ruta_imagen;
		} else {
			$contenido_resp = $rowItem["Valor"];
		}


		$arrayItems[$keyRow]["_" . $rowItem["IDPregunta"]] = $contenido_resp;
	}

else :

	$sql_diagnostico = "SELECT S.IDSocio,S.NumeroDocumento,CONCAT( S.Nombre, ' ', S.Apellido ) AS Nombre,
			P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr, S.Predio,S.NumeroDeCasa,S.Accion,S.Area
			FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Socio S
			WHERE  P.IDEncuesta = E.IDEncuesta 
                AND ER.IDPregunta = P.IDPregunta 
                AND ER.IDSocio = S.IDSocio 
                AND $strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaRespuesta DESC";

	$result = $dbo->query($sql_diagnostico);

	while ($rowItem = $dbo->fetchArray($result)) {
		$keyRow = $rowItem["IDSocio"] . $rowItem["FechaTrCr"];

		if (!array_key_exists($keyRow, $arrayItems)) {
			$arrayUsuario =  array(
				'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
				'Accion' => $rowItem["Accion"],
				'Nombre' => $rowItem['Nombre'],
				'Predio' => $rowItem['Predio'],
				'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
				'Area' => $rowItem['Area'],
				'Fecha' => $rowItem['FechaTrCr'],
			);

			$arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
		}


		if ($rowItem["TipoCampo"] == "imagen" && !empty($rowItem["Valor"])) {
			$ruta_imagen = PQR_ROOT . $rowItem["Valor"];
			$contenido_resp = "<a href='" . $ruta_imagen . "'>Ver imagen: </a>" . $ruta_imagen;
		} else {
			$contenido_resp = $rowItem["Valor"];
		}

		$arrayItems[$keyRow]["_" . $rowItem["IDPregunta"]] = $contenido_resp;
	}

	$sql_diagnostico = "SELECT U.IDUsuario, U.NumeroDocumento, U.Nombre AS Nombre,
			P.IDPregunta,P.TipoCampo, ER.Valor,DATE(ER.FechaTrCr) AS Fecha,ER.FechaTrCr,U.Accion,U.Area
			FROM Encuesta E, Pregunta P, EncuestaRespuesta ER, Usuario U
			WHERE
                P.IDEncuesta = E.IDEncuesta
                AND ER.IDPregunta = P.IDPregunta
                AND ER.IDSocio = U.IDUsuario
                AND $strCondicion AND $condicionFecha
			ORDER BY ER.IDEncuestaRespuesta DESC";

	$result = $dbo->query($sql_diagnostico);

	while ($rowItem = $dbo->fetchArray($result)) {
		$keyRow = $rowItem["IDUsuario"] . $rowItem["FechaTrCr"];

		if (!array_key_exists($keyRow, $arrayItems)) {
			$arrayUsuario =  array(
				'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
				'Accion' => $rowItem["Accion"],
				'Nombre' => $rowItem['Nombre'],
				'Predio' => $rowItem['Predio'],
				'NumeroDeCasa' => $rowItem['NumeroDeCasa'],
				'Area' => $rowItem['Area'],
				'Fecha' => $rowItem['FechaTrCr'],
			);

			$arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
		}


		if ($rowItem["TipoCampo"] == "imagen" && !empty($rowItem["Valor"])) {
			$ruta_imagen = PQR_ROOT . $rowItem["Valor"];
			$contenido_resp = "<a href='" . $ruta_imagen . "'>Ver imagen: </a>" . $ruta_imagen;
		} else {
			$contenido_resp = $rowItem["Valor"];
		}


		$arrayItems[$keyRow]["_" . $rowItem["IDPregunta"]] = $contenido_resp;
	}


endif;
$data_export["Respuestas"]  = $arrayItems;

$arrayFiles = $reportObj->exportPHPXLS("Reporte Autodiagnostico", $data_export, $filename . ".xls", "", TRUE, $headerPL);

exit;
