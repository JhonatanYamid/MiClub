<?php

// require( dirname( __FILE__ ) . "/../../admin/config.inc.php" );

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

if (empty(SIMUser::get("club")))
	exit;

$get = SIMUtil::makeSafe($_GET);
$post = SIMUtil::makeSafe($_POST);

require_once LIBDIR . "/APPReport.class.php";

$reportObj = new APPReport();

$headerPL["f1"] = array("Reporte :" => "REGISTRO VACUNACION");
$headerPL["f2"] = array("Fecha Generacion :" => date("d m Y H:i"));

$query = $dbo->query("SELECT * FROM VacunaMarca");
$marcaVacunas = $dbo->fetch($query);

//Condiciones de Busqueda
$condiciones = '';
$tipoReporte = $get["reporte"];

if (isset($get["estadoVacunados"]) && !is_null($get["estadoVacunados"])) {
	if ($get["estadoVacunados"] == 'N') {
		$condiciones .= " AND V.EstoyVacunado<>'S'";
	} else {
		$condiciones .= " AND V.EstoyVacunado='{$get['estadoVacunados']}'";
	}
}
if (isset($get["TipoUsuario"]) && !is_null($get["TipoUsuario"])) {
	if ($get["TipoUsuario"] == 'Socio') {
		$condiciones .= " AND V.IDSocio<>'0'";
	} elseif ($get["TipoUsuario"] == 'Usuario') {
		$condiciones .= " AND V.IDUsuario <> '0'";
	}
}
if (empty($get["tipoInvitado"]) && isset($get["TipoSocio"]) && !is_null($get["TipoSocio"])) {
	$condiciones .= " AND S.TipoSocio = '" . $get['TipoSocio'] . "'";
}

if (isset($get["fechaInicio"]) && !is_null($get["fechaInicio"])) {
	$fechaInicio = $get["fechaInicio"];
	$fechaFin = $get["fechaFin"];
	$condiciones .= " AND (V.FechaCitaVacuna BETWEEN '$fechaInicio' AND '$fechaFin')";
}

if (isset($get["citaVacuna"]) && !is_null(($get["citaVacuna"]))) {
	$citaVacuna = $get["citaVacuna"];
	$condiciones .= " AND V.FechaVacuna<>0000-00-00";
}

if (isset($get["tipoInvitado"]) && !is_null($get["tipoInvitado"])) {
	$tipoInvitado = $get["tipoInvitado"];
	$columnasInvitado = "I.IDInvitado,
    I.IDTipoInvitado,
    I.NumeroDocumento,
    I.Nombre,
    I.Apellido,
    I.Email,
     I.Telefono,
    CI.IDClasificacionInvitado,";
	$invitadoJoin = "INNER JOIN Invitado I ON V.IDInvitado=I.IDInvitado
		LEFT JOIN TipoInvitado TI ON I.IDTipoInvitado = TI.IDTipoInvitado
	   LEFT JOIN ClasificacionInvitado CI ON I.IDClasificacionInvitado = CI.IDClasificacionInvitado";
	$validaClub = "V.IDClub";
	$condiciones .= " AND I.IDTipoInvitado=$tipoInvitado ";
} else {
	$columnasInvitado = "";
	$invitadoJoin = "";
	$columnasSocio = "V.IDSocio,V.IDUsuario,S.TipoSocio,";
	$socioJoin = "LEFT JOIN Socio S ON V.IDSocio=S.IDSocio LEFT JOIN Usuario U ON V.IDUsuario=U.IDUsuario";
	$validaClub = "V.IDClub";
	$condiciones .= " AND (S.IDEstadoSocio = 1 || U.Activo = 'S')";
}


if (isset($get["tipoClasificacionInvitado"]) && !is_null($get["tipoClasificacionInvitado"])) {
	$tipoClasificacionInvitado = $get["tipoClasificacionInvitado"];
	$condiciones .= " AND CI.IDClasificacionInvitado=$tipoClasificacionInvitado ";
}

if (isset($get["idVacunaMarca"]) && !is_null($get["idVacunaMarca"])) {
	$idMarcaVacuna = $get["idVacunaMarca"];
	$vacunaMarca = $dbo->getFields('VacunaMarca', 'Nombre', 'IDVacunaMarca = ' . $idMarcaVacuna);
	$condiciones .= " AND V.Marca='" . $vacunaMarca . "' ";
}

if (isset($get["Marca"]) && !is_null($get["Marca"])) {
	$entidad = $get["entidadVacuna"];
	$condiciones .= " AND V.Marca LIKE '%$entidad%' ";
}
if (isset($get["entidadVacuna"]) && !is_null($get["entidadVacuna"])) {
	$entidad = $get["entidadVacuna"];
	$condiciones .= " AND V.EntidadDosis LIKE '%$entidad%' ";
}

if (isset($get["numeroDocumento"]) && !is_null($get["numeroDocumento"])) {
	$numeroDocumento = $get["numeroDocumento"];
	if ($get["tipoVacunados"] == "Socio") {
		$condiciones .= " AND  S.NumeroDocumento='$numeroDocumento'";
	} else if ($get["tipoVacunados"] == "Usuario") {
		$condiciones .= " AND  U.NumeroDocumento='$numeroDocumento'";
	} else {
		$condiciones .= " AND  (U.NumeroDocumento='$numeroDocumento' OR S.NumeroDocumento='$numeroDocumento')";
	}
}

$columReportSocios = implode(",", array_keys($array_columnas_socio));
$columReportUsuarios = implode(",", array_keys($array_columnas_usuario));

$sql_reporte = "
    SELECT
    IDVacuna, 
    IDDosis, 
    V.IDSocio,
    V.IDUsuario,
    Lugar,
    FechaCitaVacuna,
    V.EstoyVacunado,
    " . $columnasSocio . "
    " . $columnasInvitado . "
    V.EntidadCita AS 'EntidadCita',
    V.EntidadDosis AS 'EntidadDosis',
    V.Marca,
	V.Certificado
            FROM Vacuna2 V
            " . $socioJoin . "
            " . $invitadoJoin . "
            LEFT JOIN VacunaMarca VM ON V.IDVacunaMarca=VM.IDVacunaMarca
                  WHERE " . $validaClub . "=" . $datos->club . " " .
	$condiciones .
	" ORDER BY V.FechaTrCr";
$result_reporte = $dbo->query($sql_reporte);

$nombre = "RegistroVacunacion_" . date("Y_m_d");

$NumRows = $dbo->rows($result_reporte);
if ($NumRows > 0) {

	$result_reporte = $dbo->query($sql_reporte);
	$arrData = array();
	while ($Datos = $dbo->fetchArray($result_reporte)) {

		if (!empty($Datos['IDSocio'])) {
			$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $Datos['IDSocio'] . "' ", "array");
			$correo = $datos_usuario['CorreoElectronico'];
		} elseif (!empty($Datos['IDUsuario'])) {
			$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $Datos['IDUsuario'] . "' ", "array");
			$correo = $datos_usuario['Email'];
		}
		$Accion = ($datos_usuario["Accion"] != '') ? $datos_usuario["Accion"] : 0;
		$arrUsuario = array(
			'Estoy_Vacunado' => (!empty($Datos["EstoyVacunado"])) ? $Datos["EstoyVacunado"] : 'N',
			'Tipo_Socio' => $Datos["TipoSocio"],
			'Dosis' => $dbo->getFields('Dosis', 'NombreDosis', 'IDDosis = ' . $Datos['IDDosis']),
			'Lugar' => $Datos["Lugar"],
			'Fecha_Cita_Vacuna' => $Datos["FechaCitaVacuna"],
			'Accion' => $Accion,
			'Numero_Documento' => $datos_usuario['NumeroDocumento'],
			'Nombre' => $datos_usuario['Nombre'],
			'Apellido' => $datos_usuario['Apellido'],
			'Email' => $correo,
			'Telefono' => $datos_usuario['Telefono'],
			'Entidad_Vacuna' => $Datos["EntidadDosis"],
			'Marca_Vacuna' => $Datos["Marca"],
			'Certificado' => VACUNA_ROOT . $Datos["Certificado"],
		);
		$CampoVacunacion = $dbo->fetchAll('CampoVacunacion', 'IDClub = ' . $datos->club, 'array');

		foreach ($CampoVacunacion as $Campo) {
			$q_VacunaCampoVacunacion2 = $dbo->query("SELECT Valor FROM VacunaCampoVacunacion2 WHERE IDVacuna = " . $Datos['IDVacuna'] . " AND IDCampoVacunacion = " . $Campo['IDCampoVacunacion'] . " Limit 1 ");

			$r_VacunaCampoVacunacion2 = $dbo->assoc($q_VacunaCampoVacunacion2);
			if ($Campo['Tipo'] == 'imagen' || $Campo['Tipo'] == 'imagenarchivo') {
				if ($r_VacunaCampoVacunacion2 != '') {
					$Valor = VACUNA_ROOT . $r_VacunaCampoVacunacion2["Valor"];
				} else {
					$Valor = "Sin Certificado";
				}
			} else {
				if ($r_VacunaCampoVacunacion2 != '') {
					$Valor = $r_VacunaCampoVacunacion2["Valor"];
				} else {
					$Valor = "";
				}
			}
			// array_push($arrUsuario, $arrCampo);
			$arrUsuario += [$Campo['Nombre'] => $Valor];
		}
		array_push($arrData, $arrUsuario);
	}

	// Obtenemos los datos de la tabla Vacunado
	$sql_Vacunado = "SELECT v.* FROM Vacunado v LEFT JOIN Socio s ON v.IDSocio=s.IDSocio LEFT JOIN Usuario u ON v.IDUsuario=u.IDUsuario WHERE s.IDClub = $datos->club OR u.IDClub = $datos->club";
	$q_Vacunado = $dbo->query($sql_Vacunado);

	$Data_Vacunado = array();
	while ($Vacunado = $dbo->assoc($q_Vacunado)) {
		if (!empty($Vacunado['IDSocio'])) {
			$datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $Vacunado['IDSocio'] . "' ", "array");
			$correo = $datos_usuario['CorreoElectronico'];
			$nombre = $datos_usuario['Nombre'] . ' ' . $datos_usuario['Apellido'];
		} elseif (!empty($Vacunado['IDUsuario'])) {
			$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $Vacunado['IDUsuario'] . "' ", "array");
			$correo = $datos_usuario['Email'];
			$nombre = $datos_usuario['Nombre'];
		}
		$Accion = ($datos_usuario["Accion"] != '') ? $datos_usuario["Accion"] : 0;
		$arr_Vacunado = array(
			'Accion' => $Accion,
			'Numero_Documento' => $datos_usuario['NumeroDocumento'],
			'Nombre' => $nombre,
			'Vacunado' => $Vacunado['DeseoVacuna'],
			'Fecha_registro' => $Vacunado['FechaTrCr'],
			'Certificado_Gobierno' => (!empty($Vacunado['ArchivoVacuna'])) ? VACUNA_ROOT . $Vacunado['ArchivoVacuna'] : 'Sin certificado',
		);

		array_push($Data_Vacunado, $arr_Vacunado);
	}

	//construimos el excel
	$data_export["Registro_Dosis"]  = $arrData;
	$data_export["Registro_Certificado_Gobierno"]  = $Data_Vacunado;
	$filename = "RegistroVacunacion_" . date("Y_m_d_H_i");

	// echo '<pre>';
	// print_r($data_export);
	// die();

	$arrayFiles = $reportObj->exportPHPXLS("Registro_Vacunacion", $data_export, $filename . ".xls", "", TRUE, $headerPL);
	exit;
} else {
	echo "NO HAY RESULTADOS EN LAS FECHAS SELECCIONADAS";
}
exit;
