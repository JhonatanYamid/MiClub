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
    $respuesta = SIMWebServiceToken::get_token($IDClub, $Usuario, $Clave);
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
    } else {
        if ($_GET["IDClub"])
            $IDClubWS = SIMNet::req("IDClub");
        else
            $IDClubWS = $_POST["IDClub"];

        $IDUsuarioWS = $respuesta["response"]->data->IDUsuarioWS;
        $datos_usuario_ws = $dbo->fetchAll("UsuarioWS", " IDUsuarioWS = '" . $IDUsuarioWS . "' ", "array");
        if ($datos_usuario_ws["IDClub"] != $IDClubWS && $IDUsuarioWS != 1 && $IDUsuarioWS != 9 && $IDUsuarioWS != 24) {
            $update_usuario_ws = "UPDATE UsuarioWS  SET Activo  = 'N', UsuarioTrEd = 'Posible hackeo', FechaTrEd = NOW() WHERE IDUsuarioWS = '" . $IDUsuarioWS . "'";
            $dbo->query($update_usuario_ws);
            die(json_encode(array('success' => false, 'message' => "401 Unauthorized (" . $IDUsuarioWS . ")", 'response' => "", 'date' => $nowserver)));
            exit;
        }
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


        /*     case "getreservafecha":
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
 */


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
        $respuesta = SIMWebServiceTerceros::Invitados($IDClub, $Fecha);
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

        //INICIO AUDICAL
    case "consultarsocios":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $FechaInicio = $_POST[FechaInicio];
        $FechaFin = $_POST[FechaFin];
        $HoraInicio = $_POST[HoraInicio];
        $HoraFin = $_POST[HoraFin];
        $Nombre = $_POST[Nombre];
        $NumeroDocumento = $_POST[NumeroDocumento];
        $Telefono = $_POST[Telefono];
        $Email = $_POST[Email];
        $AccionPadre = $_POST[AccionPadre];
        $respuesta = SIMWebServiceTerceros::consultar_socios($IDClub, $FechaInicio, $FechaFin, $HoraInicio, $HoraFin, $Nombre, $NumeroDocumento, $Telefono, $Email, $AccionPadre);
        $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('consultarsociosTercero','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

        // Crear socio
    case "setsocio":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $Accion = $_POST["Accion"];
        $AccionPadre = $_POST["AccionPadre"];
        $Parentesco = $_POST["Parentesco"];
        $Genero = $_POST["Genero"];
        $Nombre = $_POST["Nombre"];
        $Apellido = $_POST["Apellido"];
        $FechaNacimiento = $_POST["FechaNacimiento"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $CorreoElectronico = $_POST["CorreoElectronico"];
        $Telefono = $_POST["Telefono"];
        $Celular = $_POST["Celular"];
        $Direccion = $_POST["Direccion"];
        $TipoSocio = $_POST["TipoSocio"];
        $EstadoSocio = $_POST["EstadoSocio"];
        $InvitacionesPermitidasMes = $_POST["InvitacionesPermitidasMes"];
        $UsuarioApp = $_POST["UsuarioApp"];
        $Predio = $_POST["Predio"];
        $Categoria = $_POST["Categoria"];
        $CodigoCarne = $_POST["CodigoCarne"];
        $datos_post = $Accion . " " . $AccionPadre . " " . $Parentesco . " " . $Genero . " " . $Nombre . " " . $Apellido . " " .  $FechaNacimiento . " " . $NumeroDocumento . " " . $CorreoElectronico . " " . $Telefono . " " . $Celular . " " . $Direccion    . " " . $TipoSocio . " " . $EstadoSocio . " " . $InvitacionesPermitidasMes;

        $respuesta = SIMWebServiceTerceros::set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio, $Categoria, "", $CodigoCarne);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','POST 1: ".$datos_post."','".json_encode($respuesta)."')");
        // SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','POST 2 ".json_encode($_POST)."','".json_encode($respuesta)."')");
        $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocioTercero','GET: " . json_encode($_GET) . "','" . json_encode($respuesta) . "')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getreservafecha":
        //require(LIBDIR . "SIMServicioReserva.inc.php");
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $FechaInicio = SIMNet::req("FechaInicio");
        $FechaFin = SIMNet::req("FechaFin");
        $IDServicio = SIMNet::req("IDServicio");
        $HoraInicio = SIMNet::req("HoraInicio");
        $HoraFin = SIMNet::req("HoraFin");
        $FechaInicioCreacion = SIMNet::req("FechaInicioCreacion");
        $FechaFinCreacion = SIMNet::req("FechaFinCreacion");
        $FechaInicioModificacion = SIMNet::req("FechaInicioModificacion");
        $FechaFinModificacion = SIMNet::req("FechaFinModificacion");
        $HoraInicioModificacion = SIMNet::req("HoraInicioModificacion");
        $HoraFinModificacion = SIMNet::req("HoraFinModificacion");
        //$respuesta = SIMServicioReserva::get_reservas_fecha($IDClub, $FechaInicio, $FechaFin, $IDServicio);
        $respuesta = SIMWebServiceTerceros::get_reservas_fecha($IDClub, $FechaInicio, $FechaFin, $IDServicio, $HoraInicio, $HoraFin, $FechaInicioCreacion, $FechaFinCreacion, $FechaInicioModificacion, $FechaFinModificacion, $HoraInicioModificacion, $HoraFinModificacion);
        //inserta _log
        $sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('getreservafechaTercero','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getservicios":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $TipoApp = SIMNet::req("TipoApp");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $IDCategoriasServicios = SIMNet::req("IDSeccion");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebServiceTerceros::get_servicios($IDClub, $TipoApp, $IDUsuario, $IDSocio, $IDCategoriasServicios, $IDClubAsociado);
        //inserta _log
        $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ($IDSocio,'getserviciosTercero','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "consultaelementosservicios":
        require LIBDIR . "SIMWebServiceTerceros.inc.php";
        $IDServicio = $_POST["IDServicio"];
        $respuesta = SIMWebServiceTerceros::consulta_elementos_servicios($IDServicio);
        //inserta _log
        $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ($IDServicio,'consultaelementosserviciosTercero','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //FIN AUDICAL

    default:
        die(json_encode(array('success' => false, 'message' => 'no action.' . $action, 'response' => '', 'date' => $nowserver)));
        break;
} ///end sw
