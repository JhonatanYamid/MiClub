<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(dirname(__FILE__) . "/../../admin/config.inc.php");
require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";

session_start();
//handler de sesion
$simsession = new SIMSession(SESSION_LIMIT);

//traemos lo datos de la session
$datos = $simsession->verificar();

// Array estado Invitado
$EstadoInvitado = array(
    'P' => 'Pendiente',
    'I' => 'Ingresado'
);

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
$where = '';
if (isset($post['FechaInicio']) && isset($post['FechaFinal'])) {
    $where .= " AND FechaIngreso BETWEEN '" . $post['FechaInicio'] . "' AND '" . $post['FechaFinal'] . "'";
} else {
    $where .= " AND FechaIngreso > '2022-01-01'";
}

require_once LIBDIR . "/APPReport.class.php";
$reportObj = new APPReport();


//Condiciones de Busqueda
if (isset($get["idVacunaMarca"]) && !is_null($get["idVacunaMarca"])) {
    $idMarcaVacuna = $get["idVacunaMarca"];
    $condiciones .= " AND V.IDVacunaMarca=$idMarcaVacuna ";
}


$array_columnas = array(
    "SI.Estado" => "",
    "CONCAT( S.Nombre, ' ', S.Apellido ) as Socio" => "",
    "S.NumeroDerecho" => "",
    "S.Accion" => "",
    "S.CargoSocio as Cargo" => "",
    "SI.NumeroDocumento" => "",
    "SI.Nombre as Invitado" => "",
    "SI.FechaIngreso" => "",
    "SI.Observaciones" => "",
    "SI.TipoInvitacion" => "",
    "SI.IDSocioInvitado" => ""
);
$array_columnas = implode(",", array_keys($array_columnas));

$sql_PendientesIngreso = " SELECT $array_columnas
FROM Socio S, SocioInvitado SI
WHERE S.IDSocio=SI.IDSocio AND SI.IDClub=" . SIMUser::get("club") . " 
AND SI.Estado='P' $where
ORDER BY SI.IDSocioInvitado DESC";

$q_PendientesIngreso = $dbo->query($sql_PendientesIngreso);
$n_PendientesIngreso = $dbo->rows($q_PendientesIngreso);
$Data_PendientesIngreso = array();

if ($n_PendientesIngreso > 0) {
    while ($r_Pendientes = $dbo->assoc($q_PendientesIngreso)) {
        $arr_PendientesIngreso = array(
            'Estado' => $EstadoInvitado[$r_Pendientes['Estado']],
            'Socio' => $r_Pendientes['Socio'],
            'Numero_Derecho' => $r_Pendientes['NumeroDerecho'],
            'Accion' => $r_Pendientes['Accion'],
            'Cargo' => $r_Pendientes['Cargo'],
            'Numero_Documento' => $r_Pendientes['NumeroDocumento'],
            'Invitado' => $r_Pendientes['Invitado'],
            'Fecha_Ingreso' => $r_Pendientes['FechaIngreso'],
            'Observaciones' => $r_Pendientes['Observaciones'],
            'TipoInvitacion' => $r_Pendientes['TipoInvitacion'],
        );
        $CampoFormularioInvitado = $dbo->fetchAll('CampoFormularioInvitado', 'IDClub = ' . SIMUser::get('club') . " Order by Orden ASC", 'array');

        foreach ($CampoFormularioInvitado as $Campo) {
            $q_InvitadosOtrosDatos = $dbo->query("SELECT Valor FROM InvitadosOtrosDatos WHERE IDInvitacion = " . $r_Pendientes['IDSocioInvitado'] . " AND IDCampoFormularioInvitado = " . $Campo['IDCampoFormularioInvitado'] . " Limit 1 ");

            $r_InvitadosOtrosDatos = $dbo->assoc($q_InvitadosOtrosDatos);
            if ($Campo['TipoCampo'] == 'imagen' || $Campo['TipoCampo'] == 'imagenarchivo') {
                if ($r_InvitadosOtrosDatos != '') {
                    $Valor = PQR_ROOT . $r_InvitadosOtrosDatos["Valor"];
                } else {
                    $Valor = "Sin Certificado";
                }
            } else {
                if ($r_InvitadosOtrosDatos != '') {
                    $Valor = $r_InvitadosOtrosDatos["Valor"];
                } else {
                    $Valor = "";
                }
            }
            $CampoEtiquetaCampo = utf8_decode($Campo['EtiquetaCampo']);
            $arr_PendientesIngreso[$CampoEtiquetaCampo] = $Valor;
        }
        array_push($Data_PendientesIngreso, $arr_PendientesIngreso);
    }
}

$sql_Ingresados = "
	    SELECT $array_columnas
	     FROM Socio S, SocioInvitado SI
		WHERE S.IDSocio=SI.IDSocio AND SI.IDClub=" . SIMUser::get("club") . " 
		AND SI.Estado='I' $where
		ORDER BY SI.IDSocioInvitado DESC";


$q_Ingresados = $dbo->query($sql_Ingresados);
$n_Ingresados = $dbo->rows($q_Ingresados);
$Data_Ingresados = array();
if ($n_Ingresados > 0) {
    while ($r_Ingresados = $dbo->assoc($q_Ingresados)) {
        $arr_Ingresados = array(
            'Estado' => $EstadoInvitado[$r_Ingresados['Estado']],
            'Socio' => $r_Ingresados['Socio'],
            'Numero_Derecho' => $r_Ingresados['NumeroDerecho'],
            'Accion' => $r_Ingresados['Accion'],
            'Cargo' => $r_Ingresados['Cargo'],
            'Numero_Documento' => $r_Ingresados['NumeroDocumento'],
            'Invitado' => $r_Ingresados['Invitado'],
            'Fecha_Ingreso' => $r_Ingresados['FechaIngreso'],
            'Observaciones' => $r_Ingresados['Observaciones'],
            'TipoInvitacion' => $r_Ingresados['TipoInvitacion'],
        );
        $CampoFormularioInvitado = $dbo->fetchAll('CampoFormularioInvitado', 'IDClub = ' . SIMUser::get('club') . " Order by Orden ASC", 'array');

        foreach ($CampoFormularioInvitado as $Campo) {
            $q_InvitadosOtrosDatos = $dbo->query("SELECT Valor FROM InvitadosOtrosDatos WHERE IDInvitacion = " . $r_Ingresados['IDSocioInvitado'] . " AND IDCampoFormularioInvitado = " . $Campo['IDCampoFormularioInvitado'] . " Limit 1 ");

            $r_InvitadosOtrosDatos = $dbo->assoc($q_InvitadosOtrosDatos);
            if ($Campo['TipoCampo'] == 'imagen' || $Campo['TipoCampo'] == 'imagenarchivo') {
                if ($r_InvitadosOtrosDatos != '') {
                    $Valor = PQR_ROOT . $r_InvitadosOtrosDatos["Valor"];
                } else {
                    $Valor = "Sin Certificado";
                }
            } else {
                if ($r_InvitadosOtrosDatos != '') {
                    $Valor = $r_InvitadosOtrosDatos["Valor"];
                } else {
                    $Valor = "";
                }
            }
            $CampoEtiquetaCampo = utf8_decode($Campo['EtiquetaCampo']);
            $arr_Ingresados[$CampoEtiquetaCampo] = $Valor;
        }
        array_push($Data_Ingresados, $arr_Ingresados);
    }
}

//construimos el excel
$data_export["Ingreso Pendiente"]  = $Data_PendientesIngreso;
$data_export["Invitados Ingresados"]  = $Data_Ingresados;

// echo '<pre>';
// var_dump($data_export);
// die();
$headerPL["f1"] = array("Reporte :" => "INVITADOS_SOCIOS");
$headerPL["f2"] = array("Fecha Generacion :" => date("d m Y h:i"));

$filename = "RegistroInvitadosSocio_" . date("Y_m_d");

$arrayFiles = $reportObj->exportPHPXLS("Registro Invitados", $data_export, $filename . ".xls", "", TRUE, $headerPL);

exit;
