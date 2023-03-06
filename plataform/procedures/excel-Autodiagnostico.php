<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");
session_start();
//handler de sesion
$simsession = new SIMSession(SESSION_LIMIT);

$tipoReporte = empty($_SESSION["TipoRepDiagnostico"]) ? exit : $_SESSION["TipoRepDiagnostico"];

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

require_once LIBDIR . "/APPReport.class.php";

$reportObj = new APPReport();

// Crear constrain/condiciones para filtrar datos $where
$condicion = array();

!empty(SIMUser::get("club")) ? $condicion[] = "S.IDClub = " . SIMUser::get("club") : exit;

$condicion[] = "DR.TipoUsuario = '" . $tipoReporte . "'";

if ($tipoReporte == "Socio")
	$condicion[] = " S.IDEstadoSocio = 1 ";

if (!empty($get["DIA"]))
	$condicion[] = "DATE(DR.FechaTrCr) = '" . $get["DIA"] . "'";


if (!empty($get["IDES"]) && $tipoReporte == "Socio")
	$condicion[] = "S.IDEstadoSalud = '" . $get["IDES"] . "'";


if (!empty($get["qryString"])) {
	//$condicion[] = " ( S.Nombre LIKE '%" . $get["qryString"] . "%' OR S.Apellido LIKE '%" . $get["qryString"] . "%' )";

	if ($tipoReporte == "Socio")
		$condicion[] = " ( " . SIMUtil::makeboolean("S.Nombre", $get["qryString"]) . " OR " . SIMUtil::makeboolean("S.Apellido", $get["qryString"]) . " ) ";
	if ($tipoReporte == "Funcionario")
		$condicion[] = " ( " . SIMUtil::makeboolean("S.Nombre", $get["qryString"]) . " ) ";
} //end if



if (!empty($get["week"])) {
	$condicion[] = " DATE(DR.FechaTrCr) BETWEEN CURDATE()-INTERVAL " . $get["week"] . " WEEK AND CURDATE()";
} else
	$condicion[] = " DATE(DR.FechaTrCr) BETWEEN CURDATE()-INTERVAL 2 WEEK AND CURDATE()";

$headerPL["f1"] = array("Reporte :" => "Reporte Autodiagnostico");
$headerPL["f2"] = array("Empresa :" => "");
$headerPL["f3"] = array("Fecha Generacion :" => date("d m Y H:i"));
//	$headerPL["f4"] = array("Nombre :"=> "");
$headerPL["f5"] = array("" => "");
$headerPL["f6"] = array("No " => "Pregunta");

!empty($condicion) ? $strCondicion .= " WHERE " . implode(" AND ", $condicion) : "";

$sql_preguntas = " SELECT C.Nombre AS NombreEmpresa,P.Orden,P.IDPreguntaDiagnostico,P.EtiquetaCampo,D.Nombre as TituloDiagnostico
							FROM Diagnostico D
								JOIN PreguntaDiagnostico P ON D.IDDiagnostico = P.IDDiagnostico
								JOIN DiagnosticoOpcionesRespuesta DOR ON DOR.IDDiagnosticoPregunta = P.IDPreguntaDiagnostico
								JOIN Club C ON C.IDClub = D.IDClub
							WHERE D.IDClub = " . SIMUser::get("club") . "
							GROUP BY P.IDPreguntaDiagnostico
							ORDER BY P.Orden";


$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();
while ($rowPregunta = $dbo->fetchArray($result)) {
	$nombreEmpresa = $rowPregunta["NombreEmpresa"];
	$tituloDiagnostico = utf8_decode($rowPregunta["TituloDiagnostico"]);
	$headerPL["f" . (5 + $numPregunta)] = array($rowPregunta["IDPreguntaDiagnostico"] => utf8_decode($rowPregunta["EtiquetaCampo"]));

	$array_preguntas["_" . $rowPregunta["IDPreguntaDiagnostico"]] = "";
	$array_NoPregunta[$numPregunta] = $rowPregunta["IDPreguntaDiagnostico"];
	$numPregunta++;
}

$array_preguntasDefault = $array_preguntas;


$headerPL["f2"] = array("Empresa :" => $nombreEmpresa);
$headerPL["f4"] = array("Nombre :" => $tituloDiagnostico);

$filename = "Reporte_Autodiagnostico_" . date("Y_m_d");

$sql_diagnostico = "SELECT  S.IDSocio,S.Accion,S.NumeroDocumento,S.Nombre, S.Apellido,
								S.Telefono,S.Direccion,S.Celular,S.FechaNacimiento,S.Email,
								P.IDPreguntaDiagnostico,DOR.Opcion,DR.Valor,DR.Peso,DATE(DR.FechaTrCr) AS Fecha
							FROM Diagnostico D
								JOIN PreguntaDiagnostico P ON D.IDDiagnostico = P.IDDiagnostico
								JOIN DiagnosticoOpcionesRespuesta DOR ON DOR.IDDiagnosticoPregunta = IDPreguntaDiagnostico
								JOIN DiagnosticoRespuesta DR ON DR.IDDiagnosticoOpcionesRespuesta = DOR.IDDiagnosticoOpcionesRespuesta
								JOIN Socio S ON DR.IDSocio = S.IDSocio
							$strCondicion
							ORDER BY Fecha,P.Orden ";

if ($tipoReporte == "Funcionario") {

	$sql_diagnostico = "SELECT  S.IDUsuario AS IDSocio,S.NumeroDocumento,S.Nombre,
								S.Telefono,S.Email,
								P.IDPreguntaDiagnostico,DOR.Opcion,DR.Valor,DR.Peso,DATE(DR.FechaTrCr) AS Fecha
							FROM Diagnostico D
								JOIN PreguntaDiagnostico P ON D.IDDiagnostico = P.IDDiagnostico
								JOIN DiagnosticoOpcionesRespuesta DOR ON DOR.IDDiagnosticoPregunta = IDPreguntaDiagnostico
								JOIN DiagnosticoRespuesta DR ON DR.IDDiagnosticoOpcionesRespuesta = DOR.IDDiagnosticoOpcionesRespuesta
								JOIN Usuario S ON DR.IDUsuario = S.IDUsuario
							$strCondicion
							ORDER BY Fecha,P.Orden ";
}



$result = $dbo->query($sql_diagnostico);
//	$numPregunta = 1;
$array_preguntas = array();
$arrayItems = array();
$cont = 0;
while ($rowItem = $dbo->fetchArray($result)) {
	$keyRow = $rowItem["IDSocio"] . $rowItem["Fecha"];

	if (!array_key_exists($keyRow, $arrayItems)) {
		$Accion = ($rowItem["NumeroDocumento"] != '') ? $rowItem["NumeroDocumento"] : '';
		$arrayUsuario =  array(
			'ACCION' => $Accion,
			'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],

			'NOMBRE' => $rowItem['Nombre'],
			//	    'APELLIDO'=>$rowItem['Apellido'],
			'TELEFONO' => $rowItem['Telefono'],
			'DIRECCION' => $rowItem['Direccion'],
			////	    'CELULAR'=>$rowItem['Celular'],
			'FECHA_NAC' => $rowItem['FechaNacimiento'],
			'CODIGO_EMPLEADO' => $rowItem['CodigoEmpleado'],
			//	    'CARGO'=>$rowItem['Cargo'],
			//	    'AREA'=>$rowItem['Area'],
			//	    'DIVISION'=>$rowItem['Division'],
			//	    'DEPARTAMENTO'=>$rowItem['Departamento'],
			//	    'AGENCIA'=>$rowItem['Agencia'],
			'EMAIL' => $rowItem['Email'],
			'FECHA_DIAGNOSTICO' => $rowItem['Fecha']
		);

		$arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
	}

	$arrayItems[$keyRow]["_" . $rowItem["IDPreguntaDiagnostico"]] = $rowItem["Valor"];
}

//echo count($arrayItems);

$data_export["Diagnosticos"]  = $arrayItems;

$arrayFiles = $reportObj->exportPHPXLS("Reporte Autodiagnostico", $data_export, $filename . ".xls", "", TRUE, $headerPL);


exit;
