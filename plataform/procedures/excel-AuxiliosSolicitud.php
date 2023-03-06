<?php
require(dirname(__FILE__) . "/../../admin/config.inc.php");
function remplaza_tildes($texto)
{
    $texto = str_replace("ñ", "&ntilde;", $texto);
    $texto = str_replace("á", "&aacute;", $texto);
    $texto = str_replace("é", "&eacute;", $texto);
    $texto = str_replace("í", "&iacute;", $texto);
    $texto = str_replace("ó", "&oacute;", $texto);
    $texto = str_replace("ú", "&uacute;", $texto);
    return $texto;
}



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

/* $headerPL["f1"] = array("Reporte :" => "REGISTRO AUXILIO");
$headerPL["f2"] = array("Fecha Generacion :" => date("d m Y H:i")); */


//Condiciones de Busqueda
$condiciones = '';


$IDUsuario = $_POST['IDUsuario'];
$tableJoin = "";
$where = "";
$datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
if ($datos_usuario['IDPerfil'] != 1 && $datos_usuario['IDPerfil'] != 0) {

    $Socio_numeroDocumento = $datos_usuario['NumeroDocumento'];
    $tableJoin .= ",Socio ";
    $where .= " AND AuxiliosSolicitud.IDSocio = Socio.IDSocio AND Socio.DocumentoEspecialista = '" . $Socio_numeroDocumento . "' ";
}

if (!empty($_POST["FechaInicio"]) && !empty($_POST["FechaFin"])) {
    $condicion_fecha = " and AuxiliosSolicitud.FechaTrCr >= '" . $_POST["FechaInicio"] . " 00:00:00'  and AuxiliosSolicitud.FechaTrCr <= '" . $_POST["FechaFin"] . " 23:59:59'";
}



$sql_reporte = "SELECT * FROM  Auxilios WHERE IDClub=" . $_POST["IDClub"];
//$sql_reporte = "SELECT DISTINCT A.* FROM  Auxilios A,AuxiliosSolicitud AUS WHERE A.IDClub=" . $_POST["IDClub"] . " AND A.IDAuxilios=AUS.IDAuxilios AND AUS.IDAuxilios<>''";
$columReportSocios = implode(",", array_keys($array_columnas_socio));
$columReportUsuarios = implode(",", array_keys($array_columnas_usuario));


$result_reporte = $dbo->query($sql_reporte);

$nombre = "AuxiliosSolicitud_" . date("Y_m_d");

$NumRows = $dbo->rows($result_reporte);
if ($NumRows > 0) {



    $result_reporte = $dbo->query($sql_reporte);

    while ($Datos = $dbo->fetchArray($result_reporte)) {

        $sql_reporte1 = "SELECT 
        Auxilios.IDAuxilios,AuxiliosSolicitud.IDAuxiliosSolicitud,Auxilios.Nombre as AuxilioNombre,AuxiliosSolicitud.Comentarios,Usuario.Nombre as NombreUsuario, CONCAT(Socio.Nombre,' ',Socio.Apellido) as NombreSocio, AuxiliosRechazo.Nombre as NombreAuxiliosRechazo, AuxiliosSolicitud.FechaTrCr, AuxiliosSolicitud.IDEstado
        FROM AuxiliosSolicitud" . $tableJoin . " 
        LEFT JOIN Usuario ON AuxiliosSolicitud.IDUsuario = Usuario.IDUsuario
        LEFT JOIN Socio ON AuxiliosSolicitud.IDSocio = Socio.IDSocio
        LEFT JOIN Auxilios ON AuxiliosSolicitud.IDAuxilios = Auxilios.IDAuxilios
        LEFT JOIN AuxiliosRechazo ON AuxiliosSolicitud.IDAuxiliosRechazo = AuxiliosRechazo.IDAuxiliosRechazo
        " .
            "WHERE AuxiliosSolicitud.IDClub = " . $_POST["IDClub"] . " AND AuxiliosSolicitud.IDAuxilios=" . $Datos[IDAuxilios]  . $condicion_fecha . " " . $where . " Order by IDAuxiliosSolicitud DESC";

        //   echo $sql_reporte1 . "<br>";


        $result_reporte1 = $dbo->query($sql_reporte1);
        $arrData = array();
        while ($Datos1 = $dbo->fetchArray($result_reporte1)) {

            $arrUsuario = array(
                'NUMERO_Solicitud' => $Datos1["IDAuxiliosSolicitud"],
                'Usuario' => $Datos1["NombreUsuario"],
                'Socio' => $Datos1["NombreSocio"],
                'Fecha_Solicitud' => $Datos1["FechaTrCr"],
                'Auxilio' => $Datos1["AuxilioNombre"],
                'Estado' => SIMResources::$EstadoAuxilio[$Datos1["IDEstado"]],
                'Comentarios' => $Datos1["Comentarios"],
                'Tipo_Rechazo' => $Datos1["NombreAuxiliosRechazo"],

            );



            //$sqlPreguntas = $dbo->fetchAll("PreguntaAuxilios", "IDAuxilios='" . $Datos1[IDAuxilios] . "'", "array");
            $sqlPreguntas = "SELECT PreguntaAuxilios.IDPreguntaAuxilios, PreguntaAuxilios.TipoCampo, PreguntaAuxilios.EtiquetaCampo FROM PreguntaAuxilios, Auxilios WHERE PreguntaAuxilios.IDAuxilios=Auxilios.IDAuxilios AND Auxilios.IDClub = " . $_POST['IDClub'] . " AND PreguntaAuxilios.IDAuxilios='" . $Datos1[IDAuxilios] . "' ORDER BY PreguntaAuxilios.Orden ASC";
            // $queryPreguntas = $dbo->query($sqlPreguntas);
            // $CampoVacunacion = $dbo->fetchArray($queryPreguntas);
            //   echo $sqlPreguntas;


            $queryPreguntas = $dbo->query($sqlPreguntas);
            while ($rowPreguntas = $dbo->assoc($queryPreguntas)) {

                $sqlRespuestas = "SELECT AuxiliosRespuesta.Valor FROM AuxiliosRespuesta WHERE AuxiliosRespuesta.IDPreguntaAuxilios = " . $rowPreguntas['IDPreguntaAuxilios'] . " AND AuxiliosRespuesta.IDAuxiliosSolicitud = " . $Datos1['IDAuxiliosSolicitud'];
                //  echo $sqlRespuestas;
                $queryRespuestas = $dbo->query($sqlRespuestas);
                $rowRespuestas = $dbo->assoc($queryRespuestas);
                if ($rowRespuestas['Valor'] != NULL) {
                    if ($rowPreguntas['TipoCampo'] == 'imagen' || $rowPreguntas['TipoCampo'] == 'imagenarchivo') {
                        $Valor = PQR_ROOT . $rowRespuestas['Valor'];
                    } else {
                        $Valor = $rowRespuestas['Valor'];
                    }
                } else {
                    $Valor = '';
                }


                $arrUsuario += [remplaza_tildes(utf8_decode($rowPreguntas['EtiquetaCampo'])) => $Valor];
            }


            //print_r($arrUsuario);
            //exit;
            array_push($arrData, $arrUsuario);
            $rowPreguntas = "";
        }
        /*   print_r($arrData);
        exit; */
        if (!empty($arrData)) {

            $data_export[$Datos[Nombre]]  = $arrData;
        }
    }



    //construimos el excel

    $filename = "Auxilios_" . date("Y_m_d_H_i");

    // echo '<pre>';
    // print_r($data_export);
    // die();

    $arrayFiles = $reportObj->exportPHPXLS("Registro_Auxilios", $data_export, $filename . ".xls", "", TRUE, $headerPL);
    exit;
} else {
    echo "NO HAY RESULTADOS EN LAS FECHAS SELECCIONADAS";
}
exit;
