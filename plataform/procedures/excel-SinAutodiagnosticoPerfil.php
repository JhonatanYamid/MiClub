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

$condicionSubQuery = array();
$condicion = array();
$strReporte = "";
$strFechaReporte = "";

if (!empty($get["qryString"])) {
	//$condicion[] = " ( S.Nombre LIKE '%" . $get["qryString"] . "%' OR S.Apellido LIKE '%" . $get["qryString"] . "%' )";

	if ($tipoReporte == "Socio")
		$condicion[] = " ( " . SIMUtil::makeboolean("S.Nombre", $get["qryString"]) . " OR " . SIMUtil::makeboolean("S.Apellido", $get["qryString"]) . " ) ";
	if ($tipoReporte == "Funcionario")
		$condicion[] = " ( " . SIMUtil::makeboolean("S.Nombre", $get["qryString"]) . " ) ";
} //end if	

require_once LIBDIR . "/APPReport.class.php";

$reportObj = new APPReport();


/*		$sql_preguntas = " SELECT C.Nombre AS NombreEmpresa,P.Orden,P.IDCampoEditarSocio,P.EtiquetaCampo,D.Nombre as TituloDiagnostico
					FROM Diagnostico D
					JOIN PreguntaDiagnostico P ON D.IDDiagnostico = P.IDDiagnostico
					JOIN DiagnosticoOpcionesRespuesta DOR ON DOR.IDDiagnosticoPregunta = IDCampoEditarSocio
					JOIN Club C ON C.IDClub = D.IDClub
					WHERE D.IDClub = ".SIMUser::get("club")."
					AND D.IDDiagnostico = 17 
					GROUP BY P.IDCampoEditarSocio
					ORDER BY P.Orden";
*/

$headerPL["f1"] = array("Reporte :" => "Reporte Autodiagnostico");
$headerPL["f2"] = array("Empresa :" => "");
$headerPL["f3"] = array("Fecha Generacion :" => date("d m Y H:i"));
$headerPL["f4"] = array("Fecha Datos :" => $strReporte);
$headerPL["f5"] = array("" => "");
$headerPL["f6"] = array("No " => "Pregunta");

$sql_preguntas = "SELECT C.Nombre AS NombreEmpresa,CED.IDCampoEditarSocio,CED.Nombre AS Pregunta
							FROM CampoEditarSocio CED
								JOIN Club C ON C.IDClub = CED.IDClub
							WHERE CED.IDClub = " . SIMUser::get("club") . "
							ORDER BY CED.Orden
                        ";

$result = $dbo->query($sql_preguntas);
$numPregunta = 1;
$array_preguntas = array();

while ($rowPregunta = $dbo->fetchArray($result)) {

	$nombreEmpresa = $rowPregunta["NombreEmpresa"];
	//	$tituloDiagnostico = utf8_decode($rowPregunta["TituloDiagnostico"]);
	//	$headerPL["f".(5+$numPregunta)] = array("$numPregunta"=> utf8_decode($rowPregunta["Pregunta"]));

	$headerPL["_" . $rowPregunta["IDCampoEditarSocio"]] = array($rowPregunta["IDCampoEditarSocio"] => utf8_decode($rowPregunta["Pregunta"]));

	$array_preguntas["_" . $rowPregunta["IDCampoEditarSocio"]] = "";
	$array_NoPregunta[$numPregunta] = $rowPregunta["IDCampoEditarSocio"];
	$numPregunta++;
}

$array_preguntasDefault = $array_preguntas;

//	print_r($headerPL);
//exit;		

// SUBQUERY PARA NOT IN -- SIN DIAGNOSTICO

if ($get["SINAUTO"] == "true") {

	$condicion[] = "S.IDClub = " . SIMUser::get("club");

	$strReporte = "SIN ";
	$condicionSubQuery[] = "D.IDClub = " . SIMUser::get("club");

	if (!empty($get["DIA"])) {
		$condicionSubQuery[] = "DATE(DR.FechaTrCr) = '" . $get["DIA"] . "'";
		$strFechaReporte = "Sin Diagnostico " . $get["DIA"];
	} else {
		$condicionSubQuery[] = "DATE(DR.FechaTrCr) = CURDATE() ";
		$strFechaReporte = "Sin Diagnostico " . date('Y m d');
	}

	if ($tipoReporte == "Socio") {

		$sql = "SELECT S.IDSocio,S.Accion,S.IDClub,TRIM(CONCAT(S.Nombre,' ',S.Apellido)) AS Nombre,ES.Nombre AS Estado,S.Celular,S.NumeroDocumento,S.Email
								FROM Socio S
									INNER JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
								
							"; //WHERE S.IDSocio NOT IN 

		$sqlSubQuery = "SELECT DR.IDSocio
									FROM Diagnostico D
										INNER JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
								";

		!empty($condicionSubQuery) ? $sqlSubQuery .= " WHERE " . implode(" AND ", $condicionSubQuery) : "";

		$condicion[] = "  S.IDSocio NOT IN (" . $sqlSubQuery . " GROUP BY IDSocio ) ";
	} elseif ($tipoReporte == "Funcionario") {

		$sql = "SELECT S.IDUSuario AS IDSocio,S.IDClub,TRIM(S.Nombre) AS Nombre,S.Telefono,S.NumeroDocumento,S.Email		
							FROM Usuario S
						
						";	//WHERE S.IDUsuario NOT IN 

		$sqlSubQuery = "SELECT DR.IDUsuario AS IDSocio
									FROM Diagnostico D
										INNER JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico
								";

		$condicion[] = " S.Autorizado = 'S'";
		$condicionSubQuery[] = "DR.TipoUsuario = 'Funcionario' ";

		!empty($condicionSubQuery) ? $sqlSubQuery .= " WHERE " . implode(" AND ", $condicionSubQuery) : "";

		$condicion[] = "  S.IDUsuario NOT IN (" . $sqlSubQuery . " GROUP BY IDSocio ) ";
	}
	//$sqlSubQuery = "SELECT DR.IDSocio
	//				FROM Diagnostico D
	//				INNER JOIN DiagnosticoRespuesta DR ON D.IDDiagnostico = DR.IDDiagnostico";



}

!empty($condicion) ? $condicionStr = " WHERE " . implode(" AND ", $condicion) : "";

/*   	$sql_usuarioPeril = "SELECT  S.IDSocio,S.NumeroDocumento,S.Nombre, S.Apellido,ES.Nombre AS Estado_Salud,
					S.Telefono,S.Direccion,S.Celular,S.FechaNacimiento,
					S.CodigoEmpleado,S.Cargo,S.Area,S.Division,S.Departamento,S.Agencia,S.Email,
					CED.IDCampoEditarSocio,SCES.Valor,CED.Nombre AS Pregunta
				FROM CampoEditarSocio CED
					LEFT JOIN SocioCampoEditarSocio SCES ON CED.IDCampoEditarSocio = SCES.IDCampoEditarSocio
					JOIN Socio S ON SCES.IDSocio = S.IDSocio
					INNER JOIN EstadoSalud ES ON ES.IDEstadoSalud = S.IDEstadoSalud
				WHERE CED.IDClub = " . SIMUser::get("club")."
					$condicionStr
				ORDER BY S.Apellido
				 ";*/
$sql_usuarioPeril = $sql . $condicionStr . " ORDER BY S.Nombre ";

$result = $dbo->query($sql_usuarioPeril);
//	$numPregunta = 1;
$array_preguntas = array();
$arrayItems = array();
$cont = 0;

while ($rowItem = $dbo->fetchArray($result)) {
	$keyRow = $rowItem["IDSocio"];

	if (!array_key_exists($keyRow, $arrayItems)) {
		$Accion = ($rowItem["Accion" != '']) ? $rowItem["Accion"] : '';
		$arrayUsuario =  array(
			'IDSOCIO' => $rowItem["IDSocio"],
			'ACCION' => $Accion,
			'NO_DOCUMENTO' => $rowItem["NumeroDocumento"],
			'NOMBRE' => $rowItem['Nombre'],
			//   'APELLIDO'=>$rowItem['Apellido'],
			//    'ESTADO SALUD'=>$rowItem["Estado_Salud"],
			'TELEFONO' => $rowItem['Telefono'],
			'DIRECCION' => $rowItem['Direccion'],
			//    'CELULAR'=>$rowItem['Celular'],
			'FECHA_NAC' => $rowItem['FechaNacimiento'],
			//    'CODIGO_EMPLEADO'=>$rowItem['CodigoEmpleado'],
			//    'CARGO'=>$rowItem['Cargo'],
			//  'AREA'=>$rowItem['Area'],
			//    'DIVISION'=>$rowItem['Division'],
			//    'DEPARTAMENTO'=>$rowItem['Departamento'],
			//    'AGENCIA'=>$rowItem['Agencia'],
			'EMAIL' => $rowItem['Email']
			//'FECHA_DIAGNOSTICO'=>$rowItem['Fecha']
		);

		$arrayItems[$keyRow] = array_merge($arrayUsuario, $array_preguntasDefault);
	}
	$rowItem["Valor"] = str_replace("false", "", $rowItem["Valor"]);

	$arrayItems[$keyRow]["_" . $rowItem["IDCampoEditarSocio"]] = $rowItem["Valor"];
}

$headerPL["f2"] = array("Empresa :" => $nombreEmpresa);
$headerPL["f4"] = array("Fecha Datos :" => $strReporte);


//		print_r($arrayItems);

//$arrayItems[$numRow] = array_merge( $arrayItems[$keyRow],$array_preguntas);
//$array_preguntas = $array_preguntasDefault;

$data_export["Sin Diagnostico"]  = $arrayItems;
// echo '<pre>';
// print_r($data_export);
// exit;
$filename = "Reporte" . $strReporte . "_Autodiagnostico_" . date("Y_m_d_H_i");


$arrayFiles = $reportObj->exportPHPXLS("Reporte" . $strReporte . "_Autodiagnostico", $data_export, $filename . ".xls", "", TRUE, $headerPL);

exit;
