<?php

/**** SERVICIOS JSON PARA APPS MOVILES ******/
/**** Creación: Jorge Chirivi ******/
/**** Fecha de Creación: 17 de Septiembre de 2015 ******/
/**** Ultima Modificación': 20 de Nov de 2020 11:55am Jorge Chirivi ******/
/**** Comentarios ULtima Modificación: ******/
/**** Scripts Iniciales ******/

if (
    $_GET["email"] == "apptestcontenido" || $_POST["email"] == "apptestcontenido" ||
    $_GET["IDSocio"] == "1" || $_POST["IDSocio"] == "1"
) {
    require "../admin/config.inc.php";
    require "clubquemado.php";
    exit;
}

require "../admin/config.inc.php";
header("Content-type: application/json; charset=utf-8");

if ($_GET["AppVersion"]) {
    $AppVersion = SIMNet::req("AppVersion");
} else {
    $AppVersion = $_POST["AppVersion"];
}


if ($_GET["LANG"])
    define("LANG", $_GET["LANG"]);
elseif ($_POST["LANG"])
    define("LANG", $_POST["LANG"]);
else
    define("LANG", "Es");


$AppVersion = SIMNet::req("AppVersion");
$Data = SIMNet::req("data");
if ($AppVersion >= 31 && !empty($Data)) {
    $valornonce = substr($Data, 0, 48);
    $valorencrip = substr($Data, 48);
    $param['key'] = KEY_API;
    $param['chiper'] = $valorencrip;
    $param['nonce'] = $valornonce;
    $result_decrypt = SIMUtil::decryptSodium($param);
    if ($result_decrypt["decryptedText"] == "nodecrypt") {
        //encripta no
    } else {
        $result_decrypt["decryptedText"];
        $array_datos = json_decode($result_decrypt["decryptedText"]);
        $email = $array_datos->ax;
        if ($email == "apptestcontenido") {
            require "clubquemado.php";
            exit;
        }
    }
}

//$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('".$_SERVER["REMOTE_ADDR"]."','".json_encode($_POST)."','".json_encode($_GET)."')");
if ($_POST["action"] == "gettoken") {
    $Usuario = $_POST["Usuario"];
    $Clave = $_POST["Clave"];
    $respuesta = SIMWebServiceToken::get_token($Usuario, $Clave);
    //inserta _log
    //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_token','".json_encode($_POST)."','".json_encode($respuesta)."')");
    die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
    exit;
}

if (!empty($_GET["TokenID"]) || !empty($_POST["TokenID"]) || $AppVersion >= 26) {
    if (!empty($_GET["TokenID"])) {
        $Token = $_GET["TokenID"];
    } else {
        $Token = $_POST["TokenID"];
    }

    //Valido el Token
    $respuesta = SIMWebServiceToken::valida_token($Token);
    if (!$respuesta["success"]) {
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
    }
    //inserta _log
    //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('valida_token','".json_encode($_GET)."','".json_encode($respuesta)."')");
}

if ($_GET["key"]) {
    $key = SIMNet::req("key");
} else {
    $key = $_POST["key"];
}

if ($_GET["action"]) {
    $action = SIMNet::req("action");
} else {
    $action = $_POST["action"];
}

if ($_GET["IDClub"]) {
    $IDClub = SIMNet::req("IDClub");
} else {
    $IDClub = $_POST["IDClub"];
}

//$action = SIMNet::req( "action" );
//$IDClub = SIMNet::req( "IDClub" );

$nowserver = date("Y-m-d H:i:s");

//Validar KEY en vesiones inferiores a 26
if (($key != KEY_SERVICES && (int) $AppVersion < 26) || empty($IDClub)) {
    exit;
}

if ($IDClub == 31) {
    exit;
}

//Verificar Acciones
switch ($action) {
    case "enviarnotificaciongeneral":

        $IDClub = $_POST["IDClub"];
        $AccionSocio = $_POST["AccionSocio"];
        $SecuenciaSocio = $_POST["Secuencia"];
        $Mensaje = $_POST["Mensaje"];
        $IDModulo = "11";
        $IDDetalle = "";

        if ($IDClub == 70) {
            $busqueda = $AccionSocio . "-" . $SecuenciaSocio;
        } else {
            $busqueda = $AccionSocio;
        }

        $sqlSocio = "SELECT IDSocio FROM Socio WHERE (Accion = '" . $busqueda . "') AND IDClub = " . $IDClub;
        $qrySocio = $dbo->query($sqlSocio);
        $IDSocio = $dbo->fetchArray($qrySocio);

        $respuesta = SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocio["IDSocio"], $Mensaje, $IDModulo, $IDDetalle);

        if ($respuesta) {
            die(json_encode(array('success' => $respuesta, 'message' => "Notificación enviada con exito", 'response' => "", 'date' => $nowserver)));
        }

        break;


    case "ingresosalidausuario":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDClub = $_POST["IDClub"];
        $Documento = $_POST["Documento"];
        $Movimiento = $_POST["Movimiento"];
        $respuesta = SIMWebServiceAccesos::ingreso_salida_usuario($IDClub, $Documento, $Movimiento);
        //inserta _log
        $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('ingresosalidausuario','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");

        if ($respuesta) {
            die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        }
        break;

    case "getvalidareserva":
        require(LIBDIR . "SIMServicioReserva.inc.php");
        $AppVersion = SIMNet::req("AppVersion");
        $Documento = SIMNet::req("Documento");
        $IDServicio = $_POST["IDServicio"];
        $respuesta = SIMServicioReserva::get_valida_reserva($IDClub, $Documento, $IDServicio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getvalidareserva','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getreservafecha":
        require(LIBDIR . "SIMServicioReserva.inc.php");
        $FechaInicio = SIMNet::req("FechaInicio");
        $FechaFin = SIMNet::req("FechaFin");
        $IDServicio = SIMNet::req("IDServicio");
        $respuesta = SIMServicioReserva::get_reservas_fecha($IDClub, $FechaInicio, $FechaFin, $IDServicio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('getreservasocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



    case "actulizarestadosocio":


        $IDClub = $_POST["IDClub"];
        $AccionSocio = $_POST["AccionSocio"];
        $Estado = $_POST["Estado"];

        $sqlSocio = "SELECT IDSocio FROM Socio WHERE (Accion = '" . $AccionSocio . "' ) AND IDClub = " . $IDClub;
        $qrySocio = $dbo->query($sqlSocio);
        $IDSocio = $dbo->fetchArray($qrySocio);

        $sqlUpdate = "UPDATE Socio SET IDEstadoSocio = " . $Estado . " WHERE IDSocio = " . $IDSocio["IDSocio"];
        $run = $dbo->query($sqlUpdate);

        if (empty($run)) {
            $respuesta["message"] = "NO SE PUDO ACTULIZAR EL SOCIO";
            $respuesta["success"] = false;
            $respuesta["response"] = $sqlUpdate;
        } else {
            $respuesta["message"] = "ESTADO DEL SOCIO ACTULIZADO";
            $respuesta["success"] = true;
            $respuesta["response"] = $sqlUpdate;
        }

        die(json_encode(array('success' => $respuesta["success"], 'message' => $respuesta["message"], 'response' => $respuesta["response"], 'date' => $nowserver)));

        break;

    case "consultavacunacionsocio":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $Documento = $_POST[Documento];
        $Accion = $_POST[Accion];
        $respuesta = SIMWebServiceTerceros::InfoVacunacionSocio($IDClub, $Documento, $Accion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response])));
        exit;
        break;

    case "encuestasclub":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $FechaEncuesta = $_POST[FechaEncuesta];
        $FechaIncioRespuestas = $_POST[FechaIncioRespuestas];
        $FechaFinRespuestas = $_POST[FechaFinRespuestas];
        $respuesta = SIMWebServiceTerceros::Encuestas($IDClub, $FechaEncuesta, $FechaIncioRespuestas, $FechaFinRespuestas);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "inivtadosclub":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $Fecha = $_POST[Fecha];
        $Accion = $_POST[Accion];
        $respuesta = SIMWebServiceTerceros::Invitados($IDClub, $Fecha, $Accion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "crearactulizarsocio":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $Accion = $_POST[Accion];
        $AccionPadre = $_POST[AccionPadre];
        $Genero = $_POST[Genero];
        $Genero = $_POST[Genero];
        $Nombre = $_POST[Nombre];
        $Apellido = $_POST[Apellido];
        $FechaNacimiento = $_POST[FechaNacimiento];
        $NumeroDocumento = $_POST[NumeroDocumento];
        $CorreoElectronico = $_POST[CorreoElectronico];
        $Telefono = $_POST[Telefono];
        $Celular = $_POST[Celular];
        $Direccion = $_POST[Direccion];
        $EstadoSocio = $_POST[EstadoSocio];
        $PermiteReservar = $_POST[PermiteReservar];
        $UsuarioApp = $_POST[UsuarioApp];
        $ClaveApp = $_POST[ClaveApp];

        $respuesta = SIMWebServiceTerceros::CrearActulizarSocio($IDClub, $Accion, $AccionPadre, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $EstadoSocio, $PermiteReservar, $UsuarioApp, $ClaveApp);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "consultarsocio":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $NumeroDocumento = $_POST[NumeroDocumento];
        $respuesta = SIMWebServiceTerceros::ConsultarSocio($IDClub, $NumeroDocumento);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "consultarbasesocio":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $NumeroDocumento = $_POST[NumeroDocumento];
        $respuesta = SIMWebServiceTerceros::ConsultarBaseSocio($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "consultarplaca":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $Placa = $_POST[Placa];
        $respuesta = SIMWebServiceTerceros::placa($IDClub, $Placa);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "placasclub":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $Placa = $_POST[Placa];
        $respuesta = SIMWebServiceTerceros::placas_club($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

        //INICIO CUSEZAR
    case "invitadosclub":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $IDClub = $_GET[IDClub];
        $FechaInicio = $_GET[FechaInicio];
        $FechaFin = $_GET[FechaFin];
        $Cedula = $_GET[Cedula];
        $respuesta = SIMWebServiceTerceros::invitados_club($IDClub, $FechaInicio, $FechaFin, $Cedula);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;
        //FIN CUSEZAR
    default:
        die(json_encode(array('success' => false, 'message' => 'no action.' . $action, 'response' => '', 'date' => $nowserver)));
        break;
} ///end sw
