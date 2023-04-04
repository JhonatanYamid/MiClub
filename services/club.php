<?php

/**** SERVICIOS JSON PARA APPS MOVILES ******/
/**** Creación: Jorge Chirivi ******/
/**** Fecha de Creación: 17 de Septiembre de 2015 ******/
/**** Ultima Modificación': 17 de Dic de 2021 11:50am Jorge Chirivi ******/
/**** Comentarios ULtima Modificación: ******/
/**** Scripts Iniciales ******/

if (
    $_GET["email"] == "apptestcontenido" || $_POST["email"] == "apptestcontenido" ||
    $_GET["IDSocio"] == "1" || $_POST["IDSocio"] == "1"
) {
    require("../admin/config.inc.php");
    require("clubquemado.php");
    exit;
}


require("../admin/config.inc.php");
header("Content-type: application/json; charset=utf-8");

if ($_GET["AppVersion"])
    $AppVersion = SIMNet::req("AppVersion");
else
    $AppVersion = $_POST["AppVersion"];

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
            require("clubquemado.php");
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
    ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_token','".json_encode($_POST)."','".json_encode($respuesta)."')");
    //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
    die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
    exit;
}

if (!empty($_GET["TokenID"]) || !empty($_POST["TokenID"]) || $AppVersion >= 26) {
    if (!empty($_GET["TokenID"]))
        $Token = $_GET["TokenID"];
    else
        $Token = $_POST["TokenID"];

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
        if ($datos_usuario_ws["IDClub"] != $IDClubWS && $IDUsuarioWS != 1 && $IDUsuarioWS != 22 && $IDUsuarioWS != 24) {
            $update_usuario_ws = "UPDATE UsuarioWS  SET Activo  = 'N', UsuarioTrEd = 'Posible hackeo', FechaTrEd = NOW() WHERE IDUsuarioWS = '" . $IDUsuarioWS . "'";
            $dbo->query($update_usuario_ws);
            die(json_encode(array('success' => false, 'message' => "401 Unauthorized (" . $IDUsuarioWS . ")", 'response' => "", 'date' => $nowserver)));
            exit;
        }
    }
    //inserta _log
    ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('valida_token','".json_encode($_GET)."','".json_encode($respuesta)."')");
    //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
}


if ($_GET["key"])
    $key = SIMNet::req("key");
else
    $key = $_POST["key"];

if ($_GET["action"])
    $action = SIMNet::req("action");
else
    $action = $_POST["action"];

if ($_GET["IDClub"])
    $IDClub = SIMNet::req("IDClub");
else
    $IDClub = $_POST["IDClub"];

if ($_GET["IDSocio"])
    $IDSocio = SIMNet::req("IDSocio");
else
    $IDSocio = $_POST["IDSocio"];

if ($_GET["IDUsuario"])
    $IDUsuario = SIMNet::req("IDUsuario");
else
    $IDUsuario = $_POST["IDUsuario"];




//$action = SIMNet::req( "action" );
//$IDClub = SIMNet::req( "IDClub" );

$nowserver = date("Y-m-d H:i:s");

//Validar KEY en vesiones inferiores a 26
if (($key <> KEY_SERVICES && (int)$AppVersion < 26) || empty($IDClub))
    exit;

//Clubes dados de baja
if (
    $IDClub == 31 || $IDClub == 149 || $IDClub == 176 || $IDClub == 142 || $IDClub == 39 || $IDClub == 175 || $IDClub == 158 ||
    $IDClub == 73 || $IDClub == 80 || $IDClub == 117 || $IDClub == 142 || $IDClub == 145 |  $IDClub == 176
)
    exit;

//Valido unica sesion por dispositivo si el club lo configura asi
if ((int)$AppVersion >= 48) {
    if (!empty($_GET["IDSocio"]))
        $IdentificadorUsuario = $_GET["IDSocio"];
    elseif (!empty($_POST["IDSocio"]))
        $IdentificadorUsuario = $_POST["IDSocio"];
    elseif (!empty($_GET["IDUsuario"]))
        $IdentificadorUsuario = $_GET["IDUsuario"];
    elseif (!empty($_POST["IDUsuario"]))
        $IdentificadorUsuario = $_POST["IDUsuario"];

    $TokenSesion = SIMNet::req("TokenSesion");
    $TipoApp = SIMNet::req("TipoApp");
    $array_servicio_no_valida_sesion = array("getsocio", "siteoption", "getconfiguracionnoticiaspublicas", "getnoticiaspublicas", "getrecuperarclave", "getlabels", "gettoken", "getinfosocio", "getnoticacionlocal", "validarqrvermenudomicilio", "validarqrvermenudomicilio2", "validarqrvermenudomicilio3", "validarqrvermenudomicilio4");
    $UnicaSesion = $dbo->getFields("ConfiguracionClub", "UnicaSesionPorDispositivo", "IDClub = '" . $IDClub . "'");
    if ($UnicaSesion == "S" && !in_array($action, $array_servicio_no_valida_sesion)) {
        require LIBDIR  . "SIMWebServiceSesion.inc.php";
        $respuesta = SIMWebServiceSesion::valida_sesion($IdentificadorUsuario, $TipoApp, $TokenSesion);
        if (!$respuesta[success]) {
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('validasesion','" . json_encode($_GET) . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
            //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
            die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
            exit;
        }
    }
}



////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values (5533,'".$action."','".json_encode($_POST)."','".json_encode($_GET)."')");

//Verificar Acciones
switch ($action) {
    case "getfechahoraserver":
        $nowserver = date("Y-m-d H:i:s");
        $miliseg = intval(round(microtime(true) * 1000));
        $nowserver .= "." . substr($miliseg, -3);
        die(json_encode(array('success' => true, 'message' => "hora fecha actual", 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Validar Socio
    case "getsocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $email = SIMNet::req("email");
        $clave = SIMNet::req("clave");
        $AppVersion = SIMNet::req("AppVersion");
        $Identificador = SIMNet::req("Identificador");
        $Modelo = SIMNet::req("Modelo");
        $Data = SIMNet::req("data");

        $respuesta = SIMWebServiceUsuarios::valida_socio($email, $clave, $IDClub, $AppVersion, $Data, $Identificador, $Modelo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getsocio','".json_encode($_POST).json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getsocioencript":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $email = SIMNet::req("email");
        $clave = SIMNet::req("clave");
        $AppVersion = SIMNet::req("AppVersion");
        //Validar Socio
        $respuesta = SIMWebServiceUsuarios::valida_socio($email, $clave, $IDClub, $AppVersion);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getsocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Validar Socio
    case "getsocioaccion":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $Accion = SIMNet::req("Accion");
        $respuesta = SIMWebServiceUsuarios::valida_socio_accion($IDClub, $Accion);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getsocio','".$_SERVER['HTTPS']."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getperfil":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceUsuarios::get_perfil($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getperfil','".$_SERVER['HTTPS']."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Validar Usuario administrador (porteria)
    case "getusuarioweb":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $email = SIMNet::req("email");
        $clave = SIMNet::req("clave");
        //Validar Usuario
        $respuesta = SIMWebServiceUsuarios::valida_usuario_web($email, $clave, $IDClub, "", "");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getusuarioweb','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar splash del club
    case "getbannerapp":
        require LIBDIR . "SIMWebServiceBanner.inc.php";
        $respuesta = SIMWebServiceBanner::get_banner_app($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar splash de empleados
    case "getbannerappempleado":
        require LIBDIR . "SIMWebServiceBanner.inc.php";
        $respuesta = SIMWebServiceBanner::get_banner_app_empleado($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar la configuracion del club, nombre, color, etc
    case "siteoption":
        require LIBDIR . "SIMWebServiceClub.inc.php";
        $TipoApp = SIMNet::req("TipoApp");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");

        if ($TipoApp == "Empleado") :
            $respuesta = SIMWebServiceClub::get_app_empleado($IDClub, $IDUsuario);
        else :
            $respuesta = SIMWebServiceClub::get_club($IDClub, $IDSocio);
        endif;
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar la configuracion del app de empleados
    case "siteoptionempleado":
        require LIBDIR . "SIMWebServiceClub.inc.php";
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceClub::get_app_empleado($IDClub);
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionnoticiaspublicas":
        require LIBDIR . "SIMWebServiceClub.inc.php";
        $respuesta = SIMWebServiceClub::get_configuracion_noticias_publicas($IDClub);
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getnoticiaspublicas":
        require LIBDIR . "SIMWebServiceClub.inc.php";
        $respuesta = SIMWebServiceClub::get_noticias_publicas($IDClub);
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar secciones de noticia del club o del socio
    case "getsubmodulo":
        require LIBDIR . "SIMWebServiceClub.inc.php";
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceClub::get_submodulo($IDClub, $IDModulo);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getsubmodulo','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar secciones de noticia del club o del socio
    case "getseccion":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceNoticias::get_seccion($IDClub, $IDSocio, $IDUsuario, $TipoApp);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccion','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getseccionnoticia2":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceNoticias::get_seccion($IDClub, $IDSocio, $IDUsuario, $TipoApp, "2");
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccionnoticia2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getseccionnoticia3":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceNoticias::get_seccion($IDClub, $IDSocio, $IDUsuario, $TipoApp, "3");
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccionnoticia2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar secciones de eventos del club o del socio
    case "getseccionevento":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceEventos::get_seccionevento($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar secciones de eventos del club o del socio
    case "getseccionevento2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceEventos::get_seccionevento($IDClub, $IDSocio, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar secciones del club o del socio completas de noticias, eventos y galerias
    case "getseccionclub":
        require LIBDIR . "SIMWebServiceClub.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceClub::get_seccion_club($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //ConfiguracionClasificado
    case "getconfiguracionclasificado":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceClasificados::get_configuracion_clasificado($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getcatagoriaclasificado":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceClasificados::get_categoria_clasificado($IDClub, $TipoApp);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values (5533,'getcatagoriaclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getclasificado":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $Tag = SIMNet::req("Tag");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDClasificado = SIMNet::req("IDClasificado");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDEstadoClasificado = SIMNet::req("IDEstadoClasificado");
        $respuesta = SIMWebServiceClasificados::get_clasificado($IDClub, $IDCategoria, $IDClasificado, $Tag, $IDSocio, $IDEstadoClasificado, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setpreguntaclasificado":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDClasificado = $_POST["IDClasificado"];
        $Pregunta = $_POST["Pregunta"];
        $respuesta = SIMWebServiceClasificados::set_pregunta_clasificado($IDClub, $IDClasificado, $Pregunta, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreguntaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestaclasificado":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDClasificado = $_POST["IDClasificado"];
        $IDPregunta = $_POST["IDPregunta"];
        $Respuesta = $_POST["Respuesta"];
        $respuesta = SIMWebServiceClasificados::set_respuesta_clasificado($IDClub, $IDClasificado, $IDPregunta, $Respuesta, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setclasificado":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        if (count($_FILES) > 0)
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $TipoApp = $_POST["TipoApp"];
        $IDCategoria = $_POST["IDCategoria"];
        $Nombre = $_POST["Nombre"];
        $Descripcion = $_POST["Descripcion"];
        $Telefono = $_POST["Telefono"];
        $Email = $_POST["Email"];
        $Valor = $_POST["Valor"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $Whatsapp = $_POST["Whatsapp"];

        $Dispositivo = SIMNet::req("Dispositivo");

        $respuesta = SIMWebServiceClasificados::set_clasificado($IDClub, $IDSocio, $IDCategoria, $Nombre, $Descripcion, $Telefono, $Email, $Valor, $FechaInicio, $FechaFin, $_FILES, $Dispositivo, $TipoApp, $IDUsuario, $Whatsapp);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','setclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteditaclasificado":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        if (count($_FILES) > 0)
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDClasificado = $_POST["IDClasificado"];
        $IDEstadoClasificado = $_POST["IDEstadoClasificado"];
        $IDCategoria = $_POST["IDCategoria"];
        $Nombre = $_POST["Nombre"];
        $Descripcion = $_POST["Descripcion"];
        $Telefono = $_POST["Telefono"];
        $Email = $_POST["Email"];
        $Valor = $_POST["Valor"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $UrlFoto1 = $_POST["UrlFoto1"];
        $UrlFoto2 = $_POST["UrlFoto2"];
        $UrlFoto3 = $_POST["UrlFoto3"];
        $UrlFoto4 = $_POST["UrlFoto4"];
        $UrlFoto5 = $_POST["UrlFoto5"];
        $UrlFoto6 = $_POST["UrlFoto6"];
        $UrlFoto7 = $_POST["UrlFoto7"];
        $UrlFoto8 = $_POST["UrlFoto8"];
        $UrlFoto9 = $_POST["UrlFoto9"];
        $UrlFoto10 = $_POST["UrlFoto10"];
        $UrlFoto11 = $_POST["UrlFoto11"];
        $Whatsapp = $_POST["Whatsapp"];
        $respuesta = SIMWebServiceClasificados::set_edita_clasificado($IDClub, $IDSocio, $IDClasificado, $IDEstadoClasificado, $IDCategoria, $Nombre, $Descripcion, $Telefono, $Email, $Valor, $FechaInicio, $FechaFin, $_FILES, $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5, $IDUsuario, $Whatsapp, $UrlFoto6, $UrlFoto7, $UrlFoto8, $UrlFoto9, $UrlFoto10, $UrlFoto11);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','seteditaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Clasificados 2
        //ConfiguracionClasificado
    case "getconfiguracionclasificado2":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceClasificados::get_configuracion_clasificado2($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getcatagoriaclasificado2":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $respuesta = SIMWebServiceClasificados::get_categoria_clasificado2($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getclasificado2":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $Tag = SIMNet::req("Tag");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDClasificado = SIMNet::req("IDClasificado");
        $IDSocio = SIMNet::req("IDSocio");
        $IDEstadoClasificado = SIMNet::req("IDEstadoClasificado");
        $respuesta = SIMWebServiceClasificados::get_clasificado2($IDClub, $IDCategoria, $IDClasificado, $Tag, $IDSocio, $IDEstadoClasificado);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getclasificado2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setpreguntaclasificado2":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDClasificado = $_POST["IDClasificado"];
        $Pregunta = $_POST["Pregunta"];
        $respuesta = SIMWebServiceClasificados::set_pregunta_clasificado2($IDClub, $IDClasificado, $Pregunta, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreguntaclasificado2','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestaclasificado2":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDClasificado = $_POST["IDClasificado"];
        $IDPregunta = $_POST["IDPregunta"];
        $Respuesta = $_POST["Respuesta"];
        $respuesta = SIMWebServiceClasificados::set_respuesta_clasificado2($IDClub, $IDClasificado, $IDPregunta, $Respuesta, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setrespuestaclasificado2','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setclasificado2":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDCategoria = $_POST["IDCategoria"];
        $Nombre = $_POST["Nombre"];
        $Descripcion = $_POST["Descripcion"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceClasificados::set_clasificado2($IDClub, $IDSocio, $IDCategoria, $Nombre, $Descripcion, $Respuestas, $_FILES);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteditaclasificado2":
        require(LIBDIR . "SIMWebServiceClasificados.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDClasificado = $_POST["IDClasificado"];
        $IDEstadoClasificado = $_POST["IDEstadoClasificado"];
        $IDCategoria = $_POST["IDCategoria"];
        $Nombre = $_POST["Nombre"];
        $Descripcion = $_POST["Descripcion"];
        $Respuestas = $_POST["Respuestas"];
        $UrlFoto1 = $_POST["UrlFoto1"];
        $UrlFoto2 = $_POST["UrlFoto2"];
        $UrlFoto3 = $_POST["UrlFoto3"];
        $UrlFoto4 = $_POST["UrlFoto4"];
        $UrlFoto5 = $_POST["UrlFoto5"];
        $respuesta = SIMWebServiceClasificados::set_edita_clasificado2($IDClub, $IDSocio, $IDClasificado, $IDEstadoClasificado, $IDCategoria, $Nombre, $Descripcion, $Respuestas, $_FILES, $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','seteditaclasificado2','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN Clasificados 2



    case "getconfiguracionofertas":
    case "getconfiguracionofertas2":
        require(LIBDIR . "SIMWebServiceOfertas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");

        if ($action == "getconfiguracionofertas")
            $Version = 1;
        else
            $Version = 2;

        $respuesta = SIMWebServiceOfertas::get_configuracion_ofertas($IDClub, $IDSocio, $Version);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionofertas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



    case "setoferta":
    case "setoferta2":
        require(LIBDIR . "SIMWebServiceOfertas.inc.php");

        if ($action == "setoferta")
            $Version = 1;
        else
            $Version = 2;

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDIndustria = $_POST["IDIndustria"];
        $TipoContrato = $_POST["TipoContrato"];
        $NombreEmpresa = $_POST["NombreEmpresa"];
        $PublicarEmpresa = $_POST["PublicarEmpresa"];
        $Cargo = $_POST["Cargo"];
        $Ciudad = $_POST["Ciudad"];
        $NombreEncargado = $_POST["NombreEncargado"];
        $CorreoContacto = $_POST["CorreoContacto"];
        $DescripcionCargo = $_POST["DescripcionCargo"];
        $ComentarioAdicional = $_POST["ComentarioAdicional"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $respuesta = SIMWebServiceOfertas::set_oferta($IDClub, $IDSocio, $IDUsuario, $IDIndustria, $TipoContrato, $NombreEmpresa, $PublicarEmpresa, $Cargo, $Ciudad, $NombreEncargado, $CorreoContacto, $DescripcionCargo, $ComentarioAdicional, $FechaInicio, $FechaFin, $Version);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setoferta','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteditaoferta":
    case "seteditaoferta2":
        require(LIBDIR . "SIMWebServiceOfertas.inc.php");

        if ($action == "setoferta")
            $Version = 1;
        else
            $Version = 2;

        $IDOferta = $_POST["IDOferta"];
        $IDEstadoOferta = $_POST["IDEstadoOferta"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDIndustria = $_POST["IDIndustria"];
        $TipoContrato = $_POST["TipoContrato"];
        $NombreEmpresa = $_POST["NombreEmpresa"];
        $PublicarEmpresa = $_POST["PublicarEmpresa"];
        $Cargo = $_POST["Cargo"];
        $Ciudad = $_POST["Ciudad"];
        $NombreEncargado = $_POST["NombreEncargado"];
        $CorreoContacto = $_POST["CorreoContacto"];
        $DescripcionCargo = $_POST["DescripcionCargo"];
        $ComentarioAdicional = $_POST["ComentarioAdicional"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $respuesta = SIMWebServiceOfertas::set_edita_oferta($IDClub, $IDOferta, $IDEstadoOferta, $IDSocio, $IDIndustria, $TipoContrato, $NombreEmpresa, $PublicarEmpresa, $Cargo, $Ciudad, $NombreEncargado, $CorreoContacto, $DescripcionCargo, $ComentarioAdicional, $FechaInicio, $FechaFin);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ('".$IDSocio."','seteditaoferta','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setaplicaroferta":
    case "setaplicaroferta2":
        require(LIBDIR . "SIMWebServiceOfertas.inc.php");

        if ($action == "setaplicaroferta")
            $Version = 1;
        else
            $Version = 2;


        if (count($_FILES) > 0)
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDOferta = $_POST["IDOferta"];
        $NombreRecomendado = $_POST["NombreRecomendado"];
        $Telefono = $_POST["Telefono"];
        $CorreoElectronico = $_POST["CorreoElectronico"];
        $CargoActual = $_POST["CargoActual"];
        $RazonPostulacion = $_POST["RazonPostulacion"];

        $respuesta = SIMWebServiceOfertas::set_aplicar_oferta($IDClub, $IDSocio, $IDOferta, $NombreRecomendado, $Telefono, $CorreoElectronico, $_FILES, $IDUsuario, $Version, $CargoActual, $RazonPostulacion);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setaplicaroferta','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getofertas":
    case "getofertas2":
        require(LIBDIR . "SIMWebServiceOfertas.inc.php");

        if ($action == "getofertas")
            $Version = 1;
        else
            $Version = 2;

        $IDSocio = SIMNet::req("IDSocio");
        $IDEstadoOferta = SIMNet::req("IDEstadoOferta");
        $IDOferta = SIMNet::req("IDOferta");
        $respuesta = SIMWebServiceOfertas::get_ofertas($IDClub, $IDSocio, $IDEstadoOferta, $Version);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccion','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Objetos Perdidos
        //Configuracion Objetos Perdidos
    case "getconfiguracionobjetosperdidos":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceObjetosPerdidos::get_configuracion_objetos_perdidos($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getcategoriaobjetosperdidos":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceObjetosPerdidos::get_categoria_objetos_perdidos($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getobjetosperdidos":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDObjetoPerdido = SIMNet::req("IDObjetoPerdido");
        $IDSocio = SIMNet::req("IDSocio");
        $IDEstadoObjetosPerdidos = SIMNet::req("IDEstadoObjetosPerdidos");
        $respuesta = SIMWebServiceObjetosPerdidos::get_objetos_perdidos($IDClub, $IDCategoria, $IDObjetoPerdido, $Tag, $IDSocio, $IDEstadoObjetosPerdidos);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setpertenencia":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDObjetoPerdido = $_POST["IDObjetoPerdido"];
        $respuesta = SIMWebServiceObjetosPerdidos::set_pertenencia($IDClub, $IDObjetoPerdido, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpertenencia','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getestadoobjetosperdidos":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";
        $respuesta = SIMWebServiceObjetosPerdidos::get_estado_objetos_perdidos($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setobjetoperdido":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDCategoriaObjetosPerdidos = $_POST["IDCategoriaObjetosPerdidos"];
        $Nombre = $_POST["Nombre"];
        $Descripcion = $_POST["Descripcion"];
        $FechaInicio = $_POST["FechaInicio"];
        $IDEstadoObjetosPerdidos = $_POST["FechaInicio"];
        $respuesta = SIMWebServiceObjetosPerdidos::set_objeto_perdido($IDClub, $IDSocio, $IDCategoriaObjetosPerdidos, $Nombre, $Descripcion, $FechaInicio, $IDUsuario, $_FILES);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteditaobjetoperdido":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";
        if (count($_FILES) > 0)
            $_POST = array_map('utf8_encode', $_POST);

        $IDUsuario = $_POST["IDUsuario"];
        $IDObjetoPerdido = $_POST["IDObjetoPerdido"];
        $IDEstadoObjetosPerdidos = $_POST["IDEstadoObjetosPerdidos"];
        $IDCategoriaObjetosPerdidos = $_POST["IDCategoriaObjetosPerdidos"];
        $Nombre = $_POST["Nombre"];
        $Descripcion = $_POST["Descripcion"];
        $FechaInicio = $_POST["FechaInicio"];
        $UrlFoto1 = $_POST["UrlFoto1"];
        $UrlFoto2 = $_POST["UrlFoto2"];
        $UrlFoto3 = $_POST["UrlFoto3"];
        $UrlFoto4 = $_POST["UrlFoto4"];
        $UrlFoto5 = $_POST["UrlFoto5"];
        $respuesta = SIMWebServiceObjetosPerdidos::set_edita_objeto_perdido($IDClub, $IDObjetoPerdido, $IDEstadoObjetosPerdidos, $IDCategoriaObjetosPerdidos, $Nombre, $Descripcion, $FechaInicio, $IDUsuario, $_FILES, $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('$IDEstadoObjetosPerdidos','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "setentregaobjetoperdido":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";

        if (count($_FILES) > 0)
            $_POST = array_map('utf8_encode', $_POST);


        $IDUsuario = $_POST["IDUsuario"];
        $IDSocio = $_POST["IDSocio"];
        $IDObjetoPerdido = $_POST["IDObjetoPerdido"];
        $TipoReclamante = $_POST["IDEstadoObjetosPerdidos"];
        $NombreParticular = $_POST["IDCategoriaObjetosPerdidos"];
        $DocumentoParticular = $_POST["Nombre"];
        $IDTipoDocumentoParticular = $_POST["IDTipoDocumentoParticular"];
        $Observaciones = $_POST["Observaciones"];
        $respuesta = SIMWebServiceObjetosPerdidos::set_entrega_objeto_perdido($IDClub, $IDSocio, $IDObjetoPerdido, $TipoReclamante, $NombreParticular, $DocumentoParticular, $IDTipoDocumentoParticular, $Observaciones, $IDUsuario, $_FILES);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('$IDEstadoObjetosPerdidos','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmissolictudesobjetosperdidos":
        require LIBDIR . "SIMWebServiceObjetosPerdidos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceObjetosPerdidos::get_mis_solictudes_objetos_perdidos($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Fin Objetos perdidos

    case "getconfiguracionbeneficios":
        require(LIBDIR . "SIMWebServiceBeneficios.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceBeneficios::get_configuracion_beneficios($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getcategoriabeneficio":
        require LIBDIR . "SIMWebServiceBeneficios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceBeneficios::get_categoria_beneficio($IDClub, $IDSocio);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getcategoriabeneficio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getbeneficio":
        require LIBDIR . "SIMWebServiceBeneficios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDBeneficio = SIMNet::req("IDBeneficio");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        if (SIMNet::req("TipoApp") == "Socio") {
            $IDSocio = SIMNet::req("IDSocio");
            $user = $IDSocio;
        } else {
            $IDSocio = "";
            $IDUsuario = SIMNet::req("IDSocio");
            $user = $IDUSuario;
        }
        $respuesta = SIMWebServiceBeneficios::get_beneficio($IDClub, $IDCategoria, $IDBeneficio, $Tag, $IDSocio, $IDUsuario);
        //inserta _log
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getbeneficio','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getseccionsociobeneficio":
        require LIBDIR . "SIMWebServiceBeneficios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceBeneficios::get_seccion_socio_beneficio($IDClub, $IDSocio);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getseccionsociobeneficio','".json_encode($_GET)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setpreferenciasbeneficio":
        require LIBDIR . "SIMWebServiceBeneficios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $SeccionesBeneficio = $_POST["SeccionesBeneficio"];
        $respuesta = SIMWebServiceBeneficios::set_preferencias_beneficio($IDClub, $IDSocio, $SeccionesBeneficio);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreferencias','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setlistaespera":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDServicio = $_POST["IDServicio"];
        $IDServicioElemento = $_POST["IDServicioElemento"];
        $IDAuxiliar = $_POST["IDAuxiliar"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $HoraInicio = $_POST["HoraInicio"];
        $HoraFin = $_POST["HoraFin"];
        $AceptoTerminos = $_POST["AceptoTerminos"];
        $Celular = $_POST["Celular"];
        $Tipo = $_POST["Tipo"];
        $IDClubAsociado = $_POST["IDClubAsociado"];
        $EligeListaEsperaTodos = $_POST["EligeListaEsperaTodos"];
        $respuesta = SIMWebServiceReservas::set_lista_espera($IDClub, $IDSocio, $IDServicio, $IDServicioElemento, $IDAuxiliar, $FechaInicio, $FechaFin, $HoraInicio, $HoraFin, $AceptoTerminos, $Celular, $Tipo, $IDClubAsociado, $EligeListaEsperaTodos);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreguntaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar areas del club
    case "getareaclub":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $respuesta = SIMWebServicePqr::get_area_club($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar noticias del club o del socio o de la seccion o busqueda
    case "getnoticias":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDSeccion = SIMNet::req("IDSeccion");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceNoticias::get_noticias($IDClub, $IDSeccion, $IDSocio, $Tag, "", $IDUsuario);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getnoticias','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getnoticias2":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDSeccion = SIMNet::req("IDSeccion");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceNoticias::get_noticias($IDClub, $IDSeccion, $IDSocio, $Tag, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getnoticias3":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDSeccion = SIMNet::req("IDSeccion");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceNoticias::get_noticias($IDClub, $IDSeccion, $IDSocio, $Tag, "3");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setlikenoticias":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDNoticia = $_POST["IDNoticia"];
        $HacerLike = $_POST["HacerLike"];
        $respuesta = SIMWebServiceNoticias::set_like_noticia($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $HacerLike, "");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setlikenoticias2":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDNoticia = $_POST["IDNoticia"];
        $HacerLike = $_POST["HacerLike"];
        $respuesta = SIMWebServiceNoticias::set_like_noticia($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $HacerLike, "2");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setlikenoticias3":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDNoticia = $_POST["IDNoticia"];
        $HacerLike = $_POST["HacerLike"];
        $respuesta = SIMWebServiceNoticias::set_like_noticia($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $HacerLike, "3");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcomentarnoticias":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDNoticia = $_POST["IDNoticia"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMWebServiceNoticias::set_comentar_noticias($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Comentario, "");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setcomentarnoticias2":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDNoticia = $_POST["IDNoticia"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMWebServiceNoticias::set_comentar_noticias($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Comentario, "2");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setcomentarnoticias3":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDNoticia = $_POST["IDNoticia"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMWebServiceNoticias::set_comentar_noticias($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Comentario, "3");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar noticias del club o del socio o de la seccion o busqueda
    case "getnoticiasempleados":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSeccion = SIMNet::req("IDSeccion");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceNoticias::get_noticias_empleados($IDClub, $IDSeccion, $IDUsuario, $Tag);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getnoticiasempleados','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcomentariosnoticias":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDNoticia = SIMNet::req("IDNoticia");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Pagina = SIMNet::req("Pagina");
        $respuesta = SIMWebServiceNoticias::get_comentarios_noticias($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Pagina, "");
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getnoticias','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcomentariosnoticias2":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDNoticia = SIMNet::req("IDNoticia");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceNoticias::get_comentarios_noticias($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Pagina, "2");
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getnoticias','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcomentariosnoticias3":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDNoticia = SIMNet::req("IDNoticia");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceNoticias::get_comentarios_noticias($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Pagina, "3");
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getnoticias','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar eventos del club o del socio o de la seccion o busqueda
    case "getconfiguracionevento":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $Version = "";
        $respuesta = SIMWebServiceEventos::get_configuracion_evento($IDClub, $IDSocio, $Version);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getconfiguracionevento','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionevento2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $Version = "2";
        $respuesta = SIMWebServiceEventos::get_configuracion_evento($IDClub, $IDSocio, $Version);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getconfiguracionevento','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "geteventos":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $Fecha = SIMNet::req("Fecha");
        $Nombre = SIMNet::req("Nombre");

        if (!empty(SIMNet::req("IDSeccion"))) :
            $IDSeccionEvento = SIMNet::req("IDSeccion");
        endif;

        if (!empty(SIMNet::req("IDSeccionEvento"))) :
            $IDSeccionEvento = SIMNet::req("IDSeccionEvento");
        endif;

        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceEventos::get_eventos($IDClub, $IDSeccionEvento, $IDSocio, $Tag, $Nombre, $Fecha);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "geteventos2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $Fecha = SIMNet::req("Fecha");
        $Nombre = SIMNet::req("Nombre");

        if (!empty(SIMNet::req("IDSeccion"))) :
            $IDSeccionEvento = SIMNet::req("IDSeccion");
        endif;

        if (!empty(SIMNet::req("IDSeccionEvento"))) :
            $IDSeccionEvento = SIMNet::req("IDSeccionEvento");
        endif;

        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceEventos::get_eventos($IDClub, $IDSeccionEvento, $IDSocio, $Tag, $Nombre, $Fecha, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        //Guardar campos formulario Evento
    case "setformularioevento":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDEvento = $_POST["IDEvento"];
        $IDSocio = $_POST["IDSocio"];
        $TipoApp = $_POST["TipoApp"];
        $IDSocioBeneficiario = $_POST["IDSocioBeneficiario"];
        $ValoresFormulario = $_POST["ValoresFormulario"];
        //$OtrosDatosFormulario = "NumeroPersonas: " . $_POST["personas"] . " NombreInvitados" . $_POST["nomInvitados"] . " Comentarios:".$_POST["comentarios"];
        $OtrosDatosFormulario = "";
        $respuesta = SIMWebServiceEventos::set_formulario_evento($IDClub, $IDEvento, $IDSocio, $IDSocioBeneficiario, $ValoresFormulario, $OtrosDatosFormulario, "", "", "", $TipoApp);

        if (!empty($_POST["paginaretorno"]))
            header("Location: " . $_POST["paginaretorno"] . "?mensaje=" . $respuesta[message]);

        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setformularioevento','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));

        exit;
        break;

        //Guardar campos formulario Evento
    case "setformularioevento2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDEvento = $_POST["IDEvento"];
        $IDSocio = $_POST["IDSocio"];
        $IDSocioBeneficiario = $_POST["IDSocioBeneficiario"];
        $ValoresFormulario = $_POST["ValoresFormulario"];
        //$OtrosDatosFormulario = "NumeroPersonas: " . $_POST["personas"] . " NombreInvitados" . $_POST["nomInvitados"] . " Comentarios:".$_POST["comentarios"];
        $respuesta = SIMWebServiceEventos::set_formulario_evento($IDClub, $IDEvento, $IDSocio, $IDSocioBeneficiario, $ValoresFormulario, $OtrosDatosFormulario, "2");

        if (!empty($_POST["paginaretorno"]))
            header("Location: " . $_POST["paginaretorno"] . "?mensaje=" . $respuesta[message]);

        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setformularioevento2','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));

        exit;
        break;




        //Enviar eventos del club o del socio o de la seccion o busqueda
    case "geteventosempleado":
    case "geteventosempleados":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $Fecha = SIMNet::req("Fecha");
        $Nombre = SIMNet::req("Nombre");

        if (!empty(SIMNet::req("IDSeccion"))) :
            $IDSeccionEvento = SIMNet::req("IDSeccion");
        endif;

        if (!empty(SIMNet::req("IDSeccionEvento"))) :
            $IDSeccionEvento = SIMNet::req("IDSeccionEvento");
        endif;

        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceEventos::get_eventos_empleados($IDClub, $IDSeccionEvento, $IDUsuario, $Tag, "", $Nombre, $Fecha);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "geteventosempleados2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $Fecha = SIMNet::req("Fecha");
        $Nombre = SIMNet::req("Nombre");

        if (!empty(SIMNet::req("IDSeccion"))) :
            $IDSeccionEvento = SIMNet::req("IDSeccion");
        endif;

        if (!empty(SIMNet::req("IDSeccionEvento"))) :
            $IDSeccionEvento = SIMNet::req("IDSeccionEvento");
        endif;

        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceEventos::get_eventos_empleados($IDClub, $IDSeccionEvento, $IDUsuario, $Tag, "2", $Nombre, $Fecha);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar Productos Domicilios
        //no se esta usando
    case "getproducto":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDProducto = SIMNet::req("IDProducto");
        $respuesta = SIMWebServiceDomicilios::get_producto($IDClub, $IDProducto, $Tag);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getproductocategoria":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $TipoApp = SIMNet::req("TipoApp");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceDomicilios::get_producto_categoria($IDClub, $IDCategoria, $Tag, "", $IDRestaurante, $TipoApp, $IDSocio, $IDUsuario);
        //inserta _log
        /* $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getproductocategoria','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')"); */
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Eliminar Pedido
    case "eliminarpedido":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = $_GET["IDSocio"];
        $IDDomicilio = $_GET["IDDomicilio"];
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::elimina_pedido($IDClub, $IDSocio, $IDDomicilio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','eliminarpedido','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getdomiciliosocio":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_domicilio_socio($IDClub, $IDSocio, "", $IDRestaurante, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //ConfiguracionDomicilio
    case "getconfiguraciondomicilio":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        if ($IDClub == "7") :
            require LIBDIR . "SIMWebServiceClub.inc.php";
            $respuesta_version = SIMWebServiceClub::verifica_version_app($IDClub, $_GET["AppVersion"], $_GET, $TipoApp);
        endif;
        $Fecha = SIMNet::req("Fecha");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_configuracion_domicilio($IDClub, $IDSocio, $Fecha, "", $IDRestaurante, $IDUsuario);
        //inserta _log
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ('5533','getconfiguraciondomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getproductobuscador":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_producto_buscador($IDClub, $IDSocio, $IDUsuario, $Tag, $IDRestaurante, "");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getrestaurantedomicilio":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceDomicilios::get_restaurante_domicilio($IDClub, $Version = "", $IDSocio, $IDUsuario);
        //inserta _log
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','getrestaurantedomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Fechas Disponibles para domicilios
    case "getfechasdomicilio":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_fechas_domicilio($IDClub, "", $IDRestaurante);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Horas Disponibles para domicilios
    case "gethorasentrega":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Fecha = SIMNet::req("Fecha");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_horas_entrega($IDClub, $Fecha, "", $IDRestaurante);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gethorasentrega','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getdomiciliodetalle":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDomicilios::get_domicilio_detalle($IDClub, $IDDomicilio, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar domicilio Domicilios
    case "setdomicilio":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $HoraEntrega = $_POST["HoraEntrega"];
        $ComentariosSocio = $_POST["ComentariosSocio"];
        $DetallePedido = $_POST["DetallePedido"];
        $Celular = $_POST["Celular"];
        $Direccion = $_POST["Direccion"];
        $ValorDomicilio = $_POST["ValorDomicilio"];
        $FormaPago = $_POST["FormaPago"];
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $NumeroMesa = SIMNet::req("NumeroMesa");
        $CamposFormulario = $_POST["CamposFormulario"];
        $Propina = $_POST["Propina"];
        $Qr = $_POST["QR"];

        //Por si viene de la pagina
        if (empty($DetallePedido)) :
            $HoraEntrega = $_POST["FechaDomicilio"] . " " . $_POST["HoraEntrega"];
            $contador = 0;
            foreach ($_POST["IDProductos"] as $id_producto) :
                $array_productos[$contador]["IDProducto"] = $id_producto;
                $array_productos[$contador]["Cantidad"] = $_POST["cantidad" . $id_producto];
                $array_productos[$contador]["ValorUnitario"] = $_POST["PrecioProducto" . $id_producto];
                $contador++;
            endforeach;
            $array_productos = json_encode($array_productos);
            $DetallePedido = $array_productos;
        endif;

        $respuesta = SIMWebServiceDomicilios::set_domicilio($IDClub, $IDSocio, $HoraEntrega, $ComentariosSocio, $DetallePedido, $Celular, $Direccion, $ValorDomicilio, $FormaPago, "", $IDRestaurante, $NumeroMesa, $CamposFormulario, $Propina, $IDUsuario, $Qr);



        //inserta _log
        if ($IDSocio > 0) :
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setdomicilio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        elseif ($IDUsuario > 0) :
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDUsuario."','setdomicilio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        endif;

        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        if (!empty($_POST["paginaretorno"]))
            header("Location: " . $_POST["paginaretorno"]);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Domicilios 2
        //Enviar Productos Domicilios
        //no se esta usando
    case "getproducto2":
        $Tag = SIMNet::req("Tag");
        $IDProducto = SIMNet::req("IDProducto");
        $respuesta = SIMWebServiceApp::c($IDClub, $IDProducto, $Tag, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getproductocategoria2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $TipoApp = SIMNet::req("TipoApp");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceDomicilios::get_producto_categoria($IDClub, $IDCategoria, $Tag, "2", $IDRestaurante, $TipoApp, $IDSocio, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Eliminar Pedido
    case "eliminarpedido2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = $_GET["IDSocio"];
        $IDDomicilio = $_GET["IDDomicilio"];
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::elimina_pedido($IDClub, $IDSocio, $IDDomicilio, "", "", "2");
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminarpedido','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getdomiciliosocio2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_domicilio_socio($IDClub, $IDSocio, "2", $IDRestaurante, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getrestaurantedomicilio2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceDomicilios::get_restaurante_domicilio($IDClub, "2", $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getrestaurantedomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //ConfiguracionDomicilio
    case "getconfiguraciondomicilio2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        if ($IDClub == "7") :
            require LIBDIR . "SIMWebServiceClub.inc.php";
            $respuesta_version = SIMWebServiceClub::verifica_version_app($IDClub, $_GET["AppVersion"], $_GET, $TipoApp);
        endif;
        $Fecha = SIMNet::req("Fecha");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_configuracion_domicilio($IDClub, $IDSocio, $Fecha, "2", $IDRestaurante, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getproductobuscador2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_producto_buscador($IDClub, $IDSocio, $IDUsuario, $Tag, $IDRestaurante, "2");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        //Fechas Disponibles para domicilios
    case "getfechasdomicilio2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_fechas_domicilio($IDClub, "2", $IDRestaurante);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Horas Disponibles para domicilios
    case "gethorasentrega2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Fecha = SIMNet::req("Fecha");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_horas_entrega($IDClub, $Fecha, "2", $IDRestaurante);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gethorasentrega','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getdomiciliodetalle2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDomicilios::get_domicilio_detalle($IDClub, $IDDomicilio, $IDSocio, "", "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar domicilio Domicilios
    case "setdomicilio2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $HoraEntrega = $_POST["HoraEntrega"];
        $ComentariosSocio = $_POST["ComentariosSocio"];
        $DetallePedido = $_POST["DetallePedido"];
        $Celular = $_POST["Celular"];
        $Direccion = $_POST["Direccion"];
        $NumeroMesa = $_POST["NumeroMesa"];
        $ValorDomicilio = $_POST["ValorDomicilio"];
        $FormaPago = $_POST["FormaPago"];
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $CamposFormulario = $_POST["CamposFormulario"];
        $Propina = $_POST["Propina"];
        $Qr = $_POST["QR"];

        //Por si viene de la pagina
        if (empty($DetallePedido)) :
            $HoraEntrega = $_POST["FechaDomicilio"] . " " . $_POST["HoraEntrega"];
            $contador = 0;
            foreach ($_POST["IDProductos"] as $id_producto) :
                $array_productos[$contador]["IDProducto"] = $id_producto;
                $array_productos[$contador]["Cantidad"] = $_POST["cantidad" . $id_producto];
                $array_productos[$contador]["ValorUnitario"] = $_POST["PrecioProducto" . $id_producto];
                $contador++;
            endforeach;
            $array_productos = json_encode($array_productos);
            $DetallePedido = $array_productos;
        endif;

        $respuesta = SIMWebServiceDomicilios::set_domicilio($IDClub, $IDSocio, $HoraEntrega, $ComentariosSocio, $DetallePedido, $Celular, $Direccion, $ValorDomicilio, $FormaPago, "2", $IDRestaurante, $NumeroMesa, $CamposFormulario, $Propina, $IDUsuario, $Qr);



        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setdomicilio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        if (!empty($_POST["paginaretorno"]))
            header("Location: " . $_POST["paginaretorno"]);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //Fin Domicilios 2



        //DOMICILIOS 3
        //Enviar Productos Domicilios
        //no se esta usando
    case "getproducto3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDProducto = SIMNet::req("IDProducto");
        $respuesta = SIMWebServiceDomicilios::get_producto($IDClub, $IDProducto, $Tag, "3");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios

    case "getproductocategoria3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $TipoApp = SIMNet::req("TipoApp");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceDomicilios::get_producto_categoria($IDClub, $IDCategoria, $Tag, "3", $IDRestaurante, $TipoApp, $IDSocio, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Eliminar Pedido
    case "eliminarpedido3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = $_GET["IDSocio"];
        $IDDomicilio = $_GET["IDDomicilio"];
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::elimina_pedido($IDClub, $IDSocio, $IDDomicilio, "", "", "3");
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminarpedido3','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getdomiciliosocio3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_domicilio_socio($IDClub, $IDSocio, "3", $IDRestaurante, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getrestaurantedomicilio3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceDomicilios::get_restaurante_domicilio($IDClub, "3", $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getrestaurantedomicilio3','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //ConfiguracionDomicilio
    case "getconfiguraciondomicilio3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        if ($IDClub == "7") :
            require LIBDIR . "SIMWebServiceClub.inc.php";
            $respuesta_version = SIMWebServiceClub::verifica_version_app($IDClub, $_GET["AppVersion"], $_GET, $TipoApp);
        endif;
        $Fecha = SIMNet::req("Fecha");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_configuracion_domicilio($IDClub, $IDSocio, $Fecha, "3", $IDRestaurante, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio3','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getproductobuscador3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_producto_buscador($IDClub, $IDSocio, $IDUsuario, $Tag, $IDRestaurante, "3");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Fechas Disponibles para domicilios
    case "getfechasdomicilio3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_fechas_domicilio($IDClub, "3", $IDRestaurante);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Horas Disponibles para domicilios
    case "gethorasentrega3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Fecha = SIMNet::req("Fecha");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_horas_entrega($IDClub, $Fecha, "3", $IDRestaurante);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gethorasentrega3','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getdomiciliodetalle3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDomicilios::get_domicilio_detalle($IDClub, $IDDomicilio, $IDSocio, "", "3");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar domicilio Domicilios
    case "setdomicilio3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $HoraEntrega = $_POST["HoraEntrega"];
        $ComentariosSocio = $_POST["ComentariosSocio"];
        $DetallePedido = $_POST["DetallePedido"];
        $Celular = $_POST["Celular"];
        $Direccion = $_POST["Direccion"];
        $NumeroMesa = $_POST["NumeroMesa"];
        $ValorDomicilio = $_POST["ValorDomicilio"];
        $FormaPago = $_POST["FormaPago"];
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $CamposFormulario = $_POST["CamposFormulario"];
        $Propina = $_POST["Propina"];
        $Qr = $_POST["QR"];

        //Por si viene de la pagina
        if (empty($DetallePedido)) :
            $HoraEntrega = $_POST["FechaDomicilio"] . " " . $_POST["HoraEntrega"];
            $contador = 0;
            foreach ($_POST["IDProductos"] as $id_producto) :
                $array_productos[$contador]["IDProducto"] = $id_producto;
                $array_productos[$contador]["Cantidad"] = $_POST["cantidad" . $id_producto];
                $array_productos[$contador]["ValorUnitario"] = $_POST["PrecioProducto" . $id_producto];
                $contador++;
            endforeach;
            $array_productos = json_encode($array_productos);
            $DetallePedido = $array_productos;
        endif;

        $respuesta = SIMWebServiceDomicilios::set_domicilio($IDClub, $IDSocio, $HoraEntrega, $ComentariosSocio, $DetallePedido, $Celular, $Direccion, $ValorDomicilio, $FormaPago, "3", $IDRestaurante, $NumeroMesa, $CamposFormulario, $Propina, $IDUsuario, $Qr);



        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setdomicilio3','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        if (!empty($_POST["paginaretorno"]))
            header("Location: " . $_POST["paginaretorno"]);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN DOMICILIOS 3


        //DOMICILIOS 4
        //Enviar Productos Domicilios
        //no se esta usando
    case "getproducto4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDProducto = SIMNet::req("IDProducto");
        $respuesta = SIMWebServiceDomicilios::get_producto($IDClub, $IDProducto, $Tag, "4");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getproductocategoria4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $TipoApp = SIMNet::req("TipoApp");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceDomicilios::get_producto_categoria($IDClub, $IDCategoria, $Tag, "4", $IDRestaurante, $TipoApp, $IDSocio, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Eliminar Pedido
    case "eliminarpedido4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = $_GET["IDSocio"];
        $IDDomicilio = $_GET["IDDomicilio"];
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::elimina_pedido($IDClub, $IDSocio, $IDDomicilio, "", "", "4");
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminarpedido4','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getdomiciliosocio4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_domicilio_socio($IDClub, $IDSocio, "4", $IDRestaurante, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getrestaurantedomicilio4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceDomicilios::get_restaurante_domicilio($IDClub, "4", $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getrestaurantedomicilio4','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //ConfiguracionDomicilio
    case "getconfiguraciondomicilio4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        if ($IDClub == "7") :
            require LIBDIR . "SIMWebServiceClub.inc.php";
            $respuesta_version = SIMWebServiceClub::verifica_version_app($IDClub, $_GET["AppVersion"], $_GET, $TipoApp);
        endif;
        $Fecha = SIMNet::req("Fecha");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_configuracion_domicilio($IDClub, $IDSocio, $Fecha, "4", $IDRestaurante, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio4','".json_encode($_GET)."','".json_encode($respuesta)."')");
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getproductobuscador4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_producto_buscador($IDClub, $IDSocio, $IDUsuario, $Tag, $IDRestaurante, "4");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciondomicilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Fechas Disponibles para domicilios
    case "getfechasdomicilio4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_fechas_domicilio($IDClub, "4", $IDRestaurante);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Horas Disponibles para domicilios
    case "gethorasentrega4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $Fecha = SIMNet::req("Fecha");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $respuesta = SIMWebServiceDomicilios::get_horas_entrega($IDClub, $Fecha, "4", $IDRestaurante);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gethorasentrega4','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Productos Domicilios
    case "getdomiciliodetalle4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDomicilios::get_domicilio_detalle($IDClub, $IDDomicilio, $IDSocio, "", "4");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar domicilio Domicilios
    case "setdomicilio4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $HoraEntrega = $_POST["HoraEntrega"];
        $ComentariosSocio = $_POST["ComentariosSocio"];
        $DetallePedido = $_POST["DetallePedido"];
        $Celular = $_POST["Celular"];
        $Direccion = $_POST["Direccion"];
        $NumeroMesa = $_POST["NumeroMesa"];
        $ValorDomicilio = $_POST["ValorDomicilio"];
        $FormaPago = $_POST["FormaPago"];
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $CamposFormulario = $_POST["CamposFormulario"];
        $Propina = $_POST["Propina"];
        $Qr = $_POST["QR"];


        //Por si viene de la pagina
        if (empty($DetallePedido)) :
            $HoraEntrega = $_POST["FechaDomicilio"] . " " . $_POST["HoraEntrega"];
            $contador = 0;
            foreach ($_POST["IDProductos"] as $id_producto) :
                $array_productos[$contador]["IDProducto"] = $id_producto;
                $array_productos[$contador]["Cantidad"] = $_POST["cantidad" . $id_producto];
                $array_productos[$contador]["ValorUnitario"] = $_POST["PrecioProducto" . $id_producto];
                $contador++;
            endforeach;
            $array_productos = json_encode($array_productos);
            $DetallePedido = $array_productos;
        endif;

        $respuesta = SIMWebServiceDomicilios::set_domicilio($IDClub, $IDSocio, $HoraEntrega, $ComentariosSocio, $DetallePedido, $Celular, $Direccion, $ValorDomicilio, $FormaPago, "4", $IDRestaurante, $NumeroMesa, $CamposFormulario, $Propina, $IDUsuario, $Qr);



        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setdomicilio4','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        if (!empty($_POST["paginaretorno"]))
            header("Location: " . $_POST["paginaretorno"]);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN DOMICILIOS 4

        //INICIO QR RESTAURANTE DOMICILIOS

    case "validarqrvermenudomicilio":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $IDSocio = SIMNet::req("IDSocio");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $QR = SIMNet::req("QR");
        $respuesta = SIMWebServiceDomicilios::validar_qr_ver_menu_domicilio($IDClub, $IDSocio, $IDUsuario, $QR, $IDRestaurante, "");
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','validarqrvermenudomicilio','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validarqrvermenudomicilio2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $IDSocio = SIMNet::req("IDSocio");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $QR = SIMNet::req("QR");
        $respuesta = SIMWebServiceDomicilios::validar_qr_ver_menu_domicilio($IDClub, $IDSocio, $IDUsuario, $QR, $IDRestaurante, "2");
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','validarqrvermenudomicilio','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validarqrvermenudomicilio3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $IDSocio = SIMNet::req("IDSocio");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $QR = SIMNet::req("QR");
        $respuesta = SIMWebServiceDomicilios::validar_qr_ver_menu_domicilio($IDClub, $IDSocio, $IDUsuario, $QR, $IDRestaurante, "3");
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','validarqrvermenudomicilio','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validarqrvermenudomicilio4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $IDSocio = SIMNet::req("IDSocio");
        $IDRestaurante = SIMNet::req("IDRestaurante");
        $QR = SIMNet::req("QR");
        $respuesta = SIMWebServiceDomicilios::validar_qr_ver_menu_domicilio($IDClub, $IDSocio, $IDUsuario, $QR, $IDRestaurante, "4");
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','validarqrvermenudomicilio','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN QR RESTAURANTE DOMICILIOS


    case "getpuertas":
        require(LIBDIR . "SIMWebServicePuertas.inc.php");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServicePuertas::get_puertas($IDClub, $Tag);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setabrirpuerta":
        require(LIBDIR . "SIMWebServicePuertas.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDPuerta = $_POST["IDPuerta"];
        $respuesta = SIMWebServicePuertas::set_abrir_puerta($IDClub, $IDSocio, $IDPuerta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setabrirpuerta','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar directorio del club
    case "getrestaurante":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $respuesta = SIMWebServiceDomicilios::get_restaurante($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getrestauranteinfinito":
        $IDModulo = SIMNet::req("IDModulo");
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $respuesta = SIMWebServiceDomicilios::get_restaurante($IDClub, $IDModulo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcamposinvitados":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $respuesta = SIMWebServiceAccesos::get_campos_invitados($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcamposcontratista":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $respuesta = SIMWebServiceAccesos::get_campos_contratista($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar publicidad
    case "getpublicidad":
        require LIBDIR . "SIMWebServicePublicidad.inc.php";
        $IDModulo = SIMNet::req("IDModulo");
        $IDCategoria = SIMNet::req("IDCategoria");
        $IDServicio = SIMNet::req("IDServicio");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServicePublicidad::get_publicidad($IDClub, $IDModulo, $IDCategoria, $TipoApp, $IDServicio, $IDSocio, $IDUsuario);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','getpublicidad','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar documentos del club
    case "getdocumento":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $IDTipoArchivo = SIMNet::req("IDTipoArchivo");
        $IDServicio = SIMNet::req("IDServicio");
        $respuesta = SIMWebServiceDocumentos::get_documento($IDClub, $IDTipoArchivo, $IDServicio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getverificadocumento":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $NumeroDocumento = SIMNet::req("NumeroDocumento");
        $respuesta = SIMWebServiceUsuarios::get_verifica_documento($IDClub, $NumeroDocumento);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar documentos del socio
    case "getdocumentopersonal":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDocumentos::get_documento_personal($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentopersonal','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "setaceptadocumentopersonal":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDDocumentoPersonal = $_POST["IDDocumentoPersonal"];
        $respuesta = SIMWebServiceDocumentos::set_acepta_documento_personal($IDClub, $IDSocio, $IDUsuario, $IDDocumentoPersonal);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdocumentodinamico":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $IDSubmodulo = SIMNet::req("IDSubmodulo");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDocumentos::get_documento_dinamico($IDClub, $IDSubmodulo, "", $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdocumentodinamico2":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $IDSubmodulo = SIMNet::req("IDSubmodulo");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDocumentos::get_documento_dinamico($IDClub, $IDSubmodulo, "2", $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdocumentodinamico3":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $IDSubmodulo = SIMNet::req("IDSubmodulo");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDocumentos::get_documento_dinamico($IDClub, $IDSubmodulo, "3", $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico3','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getdocumentodinamicofuncionario":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $respuesta = SIMWebServiceDocumentos::get_documento_dinamico_funcionario($IDClub, "");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdocumentodinamicofuncionario2":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $respuesta = SIMWebServiceDocumentos::get_documento_dinamico_funcionario($IDClub, "2");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdocumentodinamicofuncionario3":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $respuesta = SIMWebServiceDocumentos::get_documento_dinamico_funcionario($IDClub, "3");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdocumentodinamico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar tipo de archivos
    case "gettipoarchivo":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $IDTipoArchivo = SIMNet::req("IDTipoArchivo");
        $IDTipoArchivo = SIMNet::req("IDSubmodulo");
        $respuesta = SIMWebServiceDocumentos::get_tipoarchivo($IDClub, $IDTipoArchivo, $IDSubmodulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettipoarchivo','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;




        // Guardar las preferncias del usuario
    case "setpreferencias":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $SeccionesContenido = $_POST["SeccionesContenido"];
        $SeccionesContenido2 = $_POST["SeccionesContenido2"];
        $SeccionesContenido3 = $_POST["SeccionesContenido3"];
        $SeccionesEvento = $_POST["SeccionesEvento"];
        $SeccionesEvento2 = $_POST["SeccionesEvento2"];
        $SeccionesGaleria = $_POST["SeccionesGaleria"];
        $SeccionesClasificado = $_POST["SeccionesClasificado"];
        $respuesta = SIMWebServiceUsuarios::set_preferencias($IDClub, $IDSocio, $SeccionesContenido, $SeccionesEvento, $SeccionesGaleria, $SeccionesClasificado, $SeccionesContenido2, $SeccionesEvento2, $SeccionesContenido3, $IDUsuario);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreferencias','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // Guardar las preferncias del Empleado
    case "setpreferenciasempleado":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDusuario = $_POST["IDusuario"];
        $SeccionesContenido = $_POST["SeccionesContenido"];
        $SeccionesEvento = $_POST["SeccionesEvento"];
        $SeccionesGaleria = $_POST["SeccionesGaleria"];
        $respuesta = SIMWebServiceUsuarios::set_preferencias_empleado($IDClub, $IDusuario, $SeccionesContenido, $SeccionesEvento, $SeccionesGaleria);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // Guardar Socios Favoritos
    case "setsociofavorito":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $SocioFavorito = $_POST["SocioFavorito"];
        $EstadoFavorito = $_POST["EstadoFavorito"];
        $IDClubAsociado = $_POST["IDClubAsociado"];
        $respuesta = SIMWebServiceUsuarios::set_socio_favorito($IDClub, $IDSocio, $SocioFavorito, $EstadoFavorito, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        //Enviar socios del club
    case "getsociosclub":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $NumeroDocumento = SIMNet::req("NumeroDocumento");
        $NumeroDerecho = SIMNet::req("NumeroDerecho");
        $Tag = SIMNet::req("Tag");
        $Titular = SIMNet::req("Titular");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $IDFiltro = SIMNet::req("IDFiltro");
        $respuesta = SIMWebServiceUsuarios::get_socios_club($IDClub, $NumeroDocumento, $NumeroDerecho, $Tag, $IDSocio, $Titular, $IDClubAsociado, $IDFiltro);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getsociosclub','".json_encode($_GET)."','')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar informacion del socio
    case "getinfosocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $AppVersion = SIMNet::req("AppVersion");
        $TipoApp = SIMNet::req("TipoApp");
        $Data = SIMNet::req("data");
        $respuesta = SIMWebServiceUsuarios::get_info_socio($IDClub, $IDSocio, $AppVersion, $Data, $TipoApp);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getinfosocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Busca invitados Generales
    case "getinvitadogeneral":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $NumeroDocumento = SIMNet::req("NumeroDocumento");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceAccesos::get_invitado_general($IDClub, $NumeroDocumento, $Tag);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar invitados socios del dia
    case "getinvitadodiasocio":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $NumeroDocumento = SIMNet::req("NumeroDocumento");
        $Nombre = SIMNet::req("Nombre");
        $Fecha = SIMNet::req("Fecha");
        $respuesta = SIMWebServiceAccesos::get_invitado_dia_socio($IDClub, $NumeroDocumento, $Nombre, $Fecha, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getfiltroelementoportexto":
        require(LIBDIR . "SIMServicioReserva.inc.php");
        $Tag = SIMNet::req("Tag");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        //Verifico Version del app
        $respuesta = SIMServicioReserva::get_filtro_elemento_por_texto($IDClub, $Tag, $IDUsuario, $IDSocio, $IDServicio, $IDClubAsociado);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getservicios','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;

    case "getfiltroelementoporboton":
        require(LIBDIR . "SIMServicioReserva.inc.php");
        $Tag = SIMNet::req("Tag");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        //Verifico Version del app
        $respuesta = SIMServicioReserva::get_filtro_elemento_por_boton($IDClub, $Tag, $IDUsuario, $IDSocio, $IDServicio, $IDClubAsociado);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getservicios','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getserviciosadicionales":
        require(LIBDIR . "SIMServicioReserva.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $IDElemento = SIMNet::req("IDElemento");
        $Fecha = SIMNet::req("Fecha");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $IDTipoReserva = SIMNet::req("IDServicioTipoReserva");
        $respuesta = SIMServicioReserva::get_servicios_adicionales($IDClub, $IDSocio, $IDServicio, $IDElemento, $Fecha, $IDClubAsociado, $IDTipoReserva);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getserviciosadicionales','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gettiporeserva":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $TipoApp = SIMNet::req("TipoApp");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebServiceReservas::get_tipo_reserva($IDClub, $TipoApp, $IDUsuario, $IDSocio, $IDServicio, $Fecha, $IDClubAsociado);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettiporeserva','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // NUEVO SEERVICIO DE CONFIGURACIÓN PQR

    case "getconfiguracionpqr":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUSuario = SIMNet::req("IDUSuario");

        $respuesta = SIMWebServicePqr::get_configuracion_pqr($IDClub, $IDSocio, $IDUsuario);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getpqrsocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar pqr socio
    case "getpqrsocio":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDPqr = SIMNet::req("IDPqr");
        $respuesta = SIMWebServicePqr::get_pqr_socio($IDClub, $IDSocio, $IDPqr);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getpqrsocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar estado pqr
    case "getestadopqr":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $respuesta = SIMWebServicePqr::get_estado_pqr($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getserviciopqr":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $respuesta = SIMWebServicePqr::get_servicio_pqr($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getserviciopqrfuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $respuesta = SIMWebServicePqr::get_servicio_pqr_funcionario($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcategoriapqr":
        $IDTipoPqr = SIMNet::req("IDTipoPqr");
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $respuesta = SIMWebServicePqr::get_categoria_pqr($IDClub, $IDTipoPqr);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcategoriapqrfuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDTipoPqr = SIMNet::req("IDTipoPqr");
        $respuesta = SIMWebServicePqr::get_categoria_pqr_funcionario($IDClub, $IDTipoPqr);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        //Guardar contactos del club
    case "setcontacto":
        require LIBDIR . "SIMWebServiceContactos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Nombre = $_POST["Nombre"];
        $Telefono = $_POST["Telefono"];
        $Ciudad = $_POST["Ciudad"];
        $Direccion = $_POST["Direccion"];
        $Email = $_POST["Email"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMWebServiceContactos::set_contacto($IDClub, $IDSocio, $Telefono, $Ciudad, $Direccion, $Email, $Comentario, $Nombre);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar tipo de pqr
    case "gettipopqr":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $respuesta = SIMWebServicePqr::get_tipo_pqr($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Pqr
    case "setpqr":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDArea = $_POST["IDArea"];
        $IDSocio = $_POST["IDSocio"];
        $Archivo = $_POST["Archivo"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $TipoPqr = $_POST["TipoPqr"];
        $IDTipoPqr = $_POST["IDTipoPqr"];
        $Asunto = $_POST["Asunto"];
        $Comentario = $_POST["Comentario"];
        $NombreColaborador = $_POST["NombreColaborador"];
        $ApellidoColaborador = $_POST["ApellidoColaborador"];
        $IDServicio = $_POST["IDServicio"];
        $respuesta = SIMWebServicePqr::set_pqr($IDClub, $IDArea, $IDSocio, $TipoPqr, $Asunto, $Comentario, $Archivo, $_FILES, $IDTipoPqr, $NombreColaborador, $ApellidoColaborador, $IDServicio);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setpqr','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setpqrws":
        $Code = $_POST["code"];
        $Hub_Code = $_POST["hub_code"];
        $Status = $_POST["status"];

        $respuesta = SIMWebServiceApp::actualizar_pqr_ws($IDClub, $Code, $Hub_Code, $Status);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setpqrws','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Guardar Respuesta Socio Pqr
    case "setpqrrespuesta":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $Archivo = $_POST["Archivo"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDPqr = $_POST["IDPqr"];
        $Comentario = $_POST["Comentario"];
        $IDPqrEstado = $_POST["IDPqrEstado"];
        $respuesta = SIMWebServicePqr::set_pqr_respuesta($IDClub, $IDSocio, $IDPqr, $Comentario, $IDPqrEstado, $Archivo, $_FILES);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setpqrrespuesta','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Calificacion pqr
    case "setcalificacionpqr":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDPqr = $_POST["IDPqr"];
        $Comentario = $_POST["Comentario"];
        $Calificacion = $_POST["Calificacion"];
        $respuesta = SIMWebServicePqr::set_calificacion_pqr($IDClub, $IDSocio, $IDPqr, $Comentario, $Calificacion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Funcionarios PQR
        //Enviar pqr socio funcionario
    case "getpqrfuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDPqr = SIMNet::req("IDPqr");
        $respuesta = SIMWebServicePqr::get_pqr_funcionario($IDClub, $IDUsuario, $IDPqr);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getpqrsociofuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDPqr = SIMNet::req("IDPqr");
        $respuesta = SIMWebServicePqr::get_pqr_socio_funcionario($IDClub, $IDUsuario, $IDPqr);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Las pqr que envia un funcionario y en la cual otro funcionario debe responder
    case "getpqrasignadafuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDPqr = SIMNet::req("IDPqr");
        $respuesta = SIMWebServicePqr::get_pqr_asignada_funcionario($IDClub, $IDUsuario, $IDPqr);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar tipo de pqr funcionario
    case "gettipopqrfuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $respuesta = SIMWebServicePqr::get_tipo_pqr_funcionario($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Pqr funcionario
    case "setpqrfuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDArea = $_POST["IDArea"];
        $IDUsuario = $_POST["IDUsuario"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $TipoPqr = $_POST["TipoPqr"];
        $IDTipoPqr = $_POST["IDTipoPqr"];
        $Asunto = $_POST["Asunto"];
        $Comentario = $_POST["Comentario"];
        $Archivo = $_POST["Archivo"];
        $IDServicio = $_POST["IDServicio"];
        $respuesta = SIMWebServicePqr::set_pqr_funcionario($IDClub, $IDArea, $IDUsuario, $TipoPqr, $Asunto, $Comentario, $Archivo, $_FILES, $IDTipoPqr, $IDServicio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Respuesta Socio Pqr funcionario
    case "setpqrrespuestafuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDPqr = $_POST["IDPqr"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMWebServicePqr::set_pqr_respuesta_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $Estado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Respuesta Socio Pqr funcionario
    case "setpqrrespuestaparasocio":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDPqr = $_POST["IDPqr"];
        $Comentario = $_POST["Comentario"];
        $IDPqrEstado = $_POST["IDPqrEstado"];
        $respuesta = SIMWebServicePqr::set_pqr_respuesta_para_socio($IDClub, $IDUsuario, $IDPqr, $Comentario, $IDPqrEstado);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('5533','setpqrrespuestaparasocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Respuesta a un pqr creado por un Funcionario donde otro funcionario le responde
    case "setpqrrespuestaparafuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDPqr = $_POST["IDPqr"];
        $Comentario = $_POST["Comentario"];
        $IDPqrEstado = $_POST["IDPqrEstado"];
        $respuesta = SIMWebServicePqr::set_pqr_respuesta_para_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $IDPqrEstado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Calificacion pqr funcionario
    case "setcalificacionpqrfuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDPqr = $_POST["IDPqr"];
        $Comentario = $_POST["Comentario"];
        $Calificacion = $_POST["Calificacion"];
        $respuesta = SIMWebServicePqr::set_calificacion_pqr_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $Calificacion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar areas del club funcionario
    case "getareaclubfuncionario":
        require LIBDIR . "SIMWebServicePqr.inc.php";
        $respuesta = SIMWebServicePqr::get_area_club_funcionario($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        // FIN Funcionarios PQR






        //Guardar Foto Socio
    case "setfotosocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Archivo = $_POST["Archivo"];
        $respuesta = SIMWebServiceUsuarios::set_foto_socio($IDClub, $IDSocio, $Archivo, $_FILES);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "validarcambiofotoperfilusuario":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceUsuarios::validar_cambio_foto_perfil_usuario($IDClub, $IDSocio, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setfotopasaporte":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Archivo = $_POST["Archivo"];
        $respuesta = SIMWebServiceUsuarios::set_foto_pasaporte($IDClub, $IDSocio, $Archivo, $_FILES);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setfotodiploma":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Archivo = $_POST["Archivo"];
        $respuesta = SIMWebServiceUsuarios::set_foto_diploma($IDClub, $IDSocio, $Archivo, $_FILES);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Verificar si Foto Socio ya fue cargada
    case "getactualizarfotosocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceUsuarios::get_actualizar_foto_socio($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Guardar Foto Empleado
    case "setfotoempleado":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $Archivo = $_POST["Archivo"];
        $respuesta = SIMWebServiceUsuarios::set_foto_empleado($IDClub, $IDUsuario, $Archivo, $_FILES);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Verificar si Foto Empleado ya fue cargada
    case "getactualizarfotoempleado":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceUsuarios::get_actualizar_foto_empleado($IDClub, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;




        //Guardar invitados del socio club
    case "setinvitadogruporeserva":
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $respuesta = SIMWebServiceApp::set_invitado_grupo_reserva($IDClub, $IDSocio, $IDReserva);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setinvitadogruporeserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteditarserviciosreserva":
        require(LIBDIR . "SIMServicioReserva.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $IDReservaGeneralInvitado = $_POST["IDReservaGeneralInvitado"];
        $AdicionalesSocio = $_POST["AdicionalesSocio"];
        $Adicionales = $_POST["Adicionales"];
        $Invitados = $_POST["Invitados"];
        $IDCaddieSocio = $_POST["IDCaddieSocio"];
        $respuesta = SIMServicioReserva::set_editar_servicios_reserva($IDClub, $IDSocio, $IDReserva, $AdicionalesSocio, $Adicionales, $IDReservaGeneralInvitado, $Invitados, $IDCaddieSocio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','seteditarserviciosreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Actualizar invitados del socio club
    case "setinvitadoupdate":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDInvitado = $_POST["IDInvitado"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $Nombre = $_POST["Nombre"];
        $FechaIngreso = $_POST["FechaIngreso"];
        $respuesta = SIMWebServiceAccesos::set_invitado_update($IDClub, $IDSocio, $IDInvitado, $NumeroDocumento, $Nombre, $FechaIngreso);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setinvitadoupdate','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Eliminar Reserva
    case "cancelarinvitacion":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");;
        $IDInvitacion = SIMNet::req("IDInvitacion");
        $respuesta = SIMWebServiceAccesos::cancela_invitacion($IDClub, $IDSocio, $IDInvitacion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getingresosalidainvitadov1":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDSocioInvitado = SIMNet::req("IDSocioInvitado");
        $respuesta = SIMWebServiceAccesos::get_ingreso_salida_invitadov1($IDClub, $IDSocio, $IDSocioInvitado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar tipo de pqr
    case "gettipoingreso":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceAccesos::get_tipo_ingreso($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Guardar invitados a una reserva
    case "setinvitadoservicio":
        $IDReserva = $_POST["IDReserva"];
        $IDSocio = $_POST["IDSocio"];
        $Invitados = $_POST["Invitados"];
        $respuesta = SIMWebService::set_invitado_servicio($IDClub, $IDReserva, $Invitados);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$_POST["IDSocio"]."','setinvitadoservicio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Elimina invitados a una reserva
    case "delinvitadoservicio":
        $IDReserva = $_POST["IDReserva"];
        $IDReservaGeneralInvitado = $_POST["IDReservaGeneralInvitado"];
        $EliminarParaMi = $_POST["EliminarParaMi"];
        $respuesta = SIMWebService::del_invitado_servicio($IDClub, $IDReserva, $IDReservaGeneralInvitado, $EliminarParaMi);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDReservaGeneral,Servicio, Parametros, Respuesta) Values ('".$_POST["IDSocio"]."','".$IDReserva."','delinvitadoservicio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar Los invitados de un socio
    case "getinvitado":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $NumeroDocumento = SIMNet::req("NumeroDocumento");
        $FechaIngreso = SIMNet::req("FechaIngreso");
        $respuesta = SIMWebServiceAccesos::get_invitado($IDClub, $IDSocio, $NumeroDocumento, $FechaIngreso);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Los invitados de un socio
    case "getmisinvitados":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $NumeroDocumento = SIMNet::req("NumeroDocumento");
        $respuesta = SIMWebServiceAccesos::get_mis_invitado($IDClub, $IDSocio, $NumeroDocumento);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Los invitados de un socio
    case "getbeneficiarios":
        $IDSocio = SIMNet::req("IDSocio");
        $Fecha = SIMNet::req("Fecha");
        $Hora = SIMNet::req("Hora");
        $Tipo = SIMNet::req("tipo");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_beneficiarios($IDClub, $IDSocio, $Fecha, $Hora, $Tipo, $IDClubAsociado);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getbeneficiarios','".json_encode($_GET)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Eliminar Invitacion Especial
    case "cancelarautorizacioninvitado":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");;
        $IDInvitacion = SIMNet::req("IDInvitacion");
        $respuesta = SIMWebServiceAccesos::cancela_autorizacion_invitado($IDClub, $IDSocio, $IDInvitacion);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','cancelarautorizacioninvitado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "eliminamisautorizacionesinvitadosanterior":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDInvitacion = $_POST["IDInvitacion"];
        $respuesta = SIMWebServiceAccesos::elimina_misautorizaciones_invitados_anterior($IDClub, $IDSocio, $IDInvitacion);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisautorizacionesinvitadosanterior','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getingresosalidaautorizacionesinvitados":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDInvitacion = SIMNet::req("IDInvitacion");
        $respuesta = SIMWebServiceAccesos::get_ingresosalida_autorizaciones_invitados($IDClub, $IDSocio, $IDInvitacion);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisautorizacionesinvitadosanterior','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



    case "getingresosalidaautorizacionescontratista":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDAutorizacion = SIMNet::req("IDAutorizacion");
        $respuesta = SIMWebServiceAccesos::get_ingresosalida_autorizaciones_contratista($IDClub, $IDSocio, $IDAutorizacion);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminamisautorizacionescontratistaanterior','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;







        //Eliminar Invitacion Contratista
    case "cancelaautorizacioncontratista":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");;
        $IDAutorizacion = SIMNet::req("IDAutorizacion");
        $respuesta = SIMWebServiceAccesos::cancela_autorizacion_contratista($IDClub, $IDSocio, $IDAutorizacion);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','cancelaautorizacioncontratista','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Actualizar datos contratista
    case "setcontratistaoupdate":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $TipoDocumento = $_POST["TipoDocumento"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $Nombre = $_POST["Nombre"];
        $Apellido = $_POST["Apellido"];
        $Email = $_POST["Email"];
        $Placa = $_POST["Placa"];
        $ObservacionSocio = $_POST["Observaciones"];
        $respuesta = SIMWebServiceAccesos::set_contratista_update($IDClub, $IDSocio, $TipoDocumento, $NumeroDocumento, $Nombre, $Apellido, $Email, $Placa);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcontratistaoupdate','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Los elementos de un servicio
    case "getelementos":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebServiceReservas::get_elementos($IDClub, $IDSocio, $IDServicio, $IDClubAsociado);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getelementos','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setimagenmapaservicioreserva":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDServicio = SIMNet::req("IDServicio");
        $File = $_FILES["Archivo"];
        $respuesta = SIMWebServiceReservas::set_imagen_mapa_servicio_reserva($IDClub, $IDServicio, $File);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getelementos','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionjuegosdegolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $IDClub = SIMNet::req("IDClub");
        $respuesta = SIMWebServiceGameGolf::get_configuracion_juegos_de_golf($IDClub, $IDUsuario, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setmapaservicioreserva":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDServicio = SIMNet::req("IDServicio");
        $Elementos = SIMNet::req("Elementos");
        $ImagenMapa = SIMNet::req("ImagenMapa");
        $ImagenAncho = SIMNet::req("ImagenAncho");
        $ImagenAlto = SIMNet::req("ImagenAlto");
        $respuesta = SIMWebServiceReservas::set_mapa_servicio_reserva($IDClub, $IDServicio, $ImagenMapa, $ImagenAncho, $ImagenAlto, $Elementos);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getelementos','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmapaservicioreserva":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDServicio = SIMNet::req("IDServicio");
        $respuesta = SIMWebServiceReservas::get_mapa_servicio_reserva($IDClub, $IDServicio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getelementos','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar Las horas de un servicio
    case "gethoras":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebServiceReservas::get_horas($IDClub, $IDSocio, $IDServicio, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar Los campos de un servicio
    case "getcampos":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebServiceReservas::get_campos($IDClub, $IDSocio, $IDServicio, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar la disponibilidad de un elemento en una fecha, marca las horas con disponible si o no
    case "getdisponiblidadelemento":
        $IDElemento = SIMNet::req("IDElemento");
        $IDServicio = SIMNet::req("IDServicio");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $Fecha = SIMNet::req("Fecha");
        //$respuesta = SIMWebService::get_disponiblidad_elemento($IDClub,$IDElemento,$IDServicio,$Fecha);
        $respuesta = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $IDServicio, $Fecha, $IDElemento, "", $UnElemento = 1, "", $IDTipoReserva, "", "", "", $IDClubAsociado);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDServicio,IDServicioElemento,Servicio, Parametros, Respuesta) Values ('".$_GET["IDSocio"]."','".$IDServicio."','".$IDElemento."','getdisponiblidadelemento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar la disponibilidad de varios elementos de un servicio en una fecha, marca las horas con disponible si o no
    case "getdisponiblidadelementoservicio":
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $IDElemento = SIMNet::req("IDElemento");
        $IDTipoReserva = SIMNet::req("IDTipoReserva");
        $NumeroTurnos = SIMNet::req("NumeroTurnos"); // Recibo cuantos turnos seguidos desea reservar
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $IDSocio = SIMNet::req("IDSocio");
        $HoraInicial = SIMNet::req("HoraInicial");
        $HoraFinal = SIMNet::req("HoraFinal");
        $respuesta = SIMWebService::get_disponiblidad_elemento_servicio($IDClub, $IDServicio, $Fecha, $IDElemento, "", "", $NumeroTurnos, $IDTipoReserva, "", "", "", $IDClubAsociado, $IDSocio, $HoraInicial, $HoraFinal);

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDServicio,IDServicioElemento,Servicio, Parametros, Respuesta) Values ('".SIMNet::req( "IDSocio" )."','".$IDServicio."','".$IDElemento."','getdisponiblidadelementoservicio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar la disponibilidad de los elementos en una fecha hora especifica
    case "getdisponiblidadfechahora":
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $Hora = SIMNet::req("Hora");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_disponiblidad_fecha_hora($IDClub, $IDServicio, $Fecha, $Hora, $NumeroTurnos, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar las horas Dsiponibles consultando todos los elementos en una fecha especifica
    case "gethoradisponible":
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_hora_disponible($IDClub, $IDServicio, $Fecha, $Hora, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar la disponibilidad de los elementos en una fecha hora especifica
    case "setseparareserva":
        $IDSocio = $_POST["IDSocio"];
        $IDElemento = $_POST["IDElemento"];
        $IDServicio = $_POST["IDServicio"];
        $IDTipoReserva = $_POST["IDTipoReserva"];
        $Tee = $_POST["Tee"];
        $Fecha = $_POST["Fecha"];
        $Hora = $_POST["Hora"];
        $IDClubAsociado = $_POST["IDClubAsociado"];
        $NumeroTurnos = $_POST["NumeroTurnos"]; // Recibo cuantos turnos seguidos desea reservar
        $respuesta = SIMWebService::set_separa_reserva($IDClub, $IDSocio, $IDElemento, $IDServicio, $Tee, $Fecha, $Hora, $IDTipoReserva, $NumeroTurnos, $IDClubAsociado);

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDServicio,IDServicioElemento,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','".$IDServicio."','".$IDElemento."','setseparareserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setpushgeneral":
        $Accion = $_POST["Accion"];
        $Mensaje = $_POST["Mensaje"];
        $respuesta = SIMWebServiceApp::set_push_general($IDClub, $Accion, $Mensaje);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$_POST["IDSocio"]."','setpushgeneral','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setliberareserva":
        $IDSocio = $_POST["IDSocio"];
        $IDElemento = $_POST["IDElemento"];
        $IDServicio = $_POST["IDServicio"];
        $Tee = $_POST["Tee"];
        $Fecha = $_POST["Fecha"];
        $Hora = $_POST["Hora"];
        $IDClubAsociado = $_POST["IDClubAsociado"];
        $respuesta = SIMWebService::set_libera_reserva($IDClub, $IDSocio, $IDElemento, $IDServicio, $Tee, $Fecha, $Hora, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Guardar Reserva General
    case "setreservageneral":

        $IDSocio = $_POST["IDSocio"];
        $IDElemento = $_POST["IDElemento"];
        $IDServicio = $_POST["IDServicio"];
        $Fecha = $_POST["Fecha"];
        $Hora = $_POST["Hora"];
        $HoraFinal = $_POST["HoraFinal"];
        $Campos = $_POST["Campos"];
        $Invitados = $_POST["Invitados"];
        //Repeticion reserva
        $IDDisponibilidad = $_POST["IDDisponibilidad"];
        $Repetir = $_POST["Repetir"];
        $Periodo = $_POST["Periodo"];
        $RepetirFechaFinal = $_POST["RepetirFechaFinal"];
        //Auxiliar-Boleador
        $IDAuxiliar = $_POST["IDAuxiliar"];
        $ListaAuxiliar = $_POST["ListaAuxiliar"];
        //Modalidad
        $IDTipoModalidadEsqui = $_POST["IDTipoModalidad"];
        //Tee
        $Tee = $_POST["Tee"];
        //Si se asigna la reserva a un beneficiario
        $IDBeneficiario = $_POST["IDBeneficiario"];
        $TipoBeneficiario = $_POST["TipoBeneficiario"];
        // Tipo Reserva (Dobles, sencillos, etc)
        $IDTipoReserva = $_POST["IDTipoReserva"];
        $NumeroTurnos = $_POST["NumeroTurnos"]; // Recibo cuantos turnos seguidos desea reservar
        //Si la reserva la hace un Empleado
        $IDUsuarioReserva = $_POST["IDUsuario"];
        //Numero de invitados cuando es salon por ejemplo
        $CantidadInvitadoSalon = $_POST["CantidadInvitadoSalon"];
        $Altitud = $_POST["Altitud"];
        $Longitud = $_POST["Longitud"];
        $AdicionalesSocio = $_POST["AdicionalesSocio"];
        $IDCaddieSocio = $_POST["IDCaddieSocio"];
        $IDClubAsociado = $_POST["IDClubAsociado"];

        $respuesta = SIMWebService::set_reserva_generalV2($IDClub, $IDSocio, $IDElemento, $IDServicio, $Fecha, $Hora, $Campos, $Invitados, "", "", $Tee, $IDDisponibilidad, $Repetir, $Periodo, $RepetirFechaFinal, $IDTipoModalidadEsqui, $IDAuxiliar, $IDTipoReserva, $NumeroTurnos, "", $IDBeneficiario, $TipoBeneficiario, $IDUsuarioReserva, $CantidadInvitadoSalon, $ListaAuxiliar, $Altitud, $Longitud, $AdicionalesSocio, $IDCaddieSocio, $IDClubAsociado, $HoraFinal);
        //$respuesta = SIMWebServiceApp::set_reserva_generalV2_test($IDClub,$IDSocio,$IDElemento,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,"","",$Tee,$IDDisponibilidad,$Repetir,$Periodo,$RepetirFechaFinal,$IDTipoModalidadEsqui,$IDAuxiliar,$IDTipoReserva,$NumeroTurnos,"",$IDBeneficiario,$TipoBeneficiario,$IDUsuarioReserva,$CantidadInvitadoSalon,$ListaAuxiliar);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDServicio,IDServicioElemento, Servicio, Parametros, Respuesta) Values ('".$IDSocio."','".$IDServicio."','".$IDElemento."','set_reserva_generalV2','".json_encode($_POST)."','".json_encode($respuesta)."')");

        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        if (!empty($_POST["paginaretorno"]))
            header("Location: " . $_POST["paginaretorno"] . "?mensaje=" . $respuesta[message]);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Reserva General
    case "setreservageneralv2":
        $IDSocio = $_POST["IDSocio"];
        $IDElemento = $_POST["IDElemento"];
        $IDServicio = $_POST["IDServicio"];
        $Fecha = $_POST["Fecha"];
        $Hora = $_POST["Hora"];
        $Campos = $_POST["Campos"];
        $Invitados = $_POST["Invitados"];
        //Repeticion reserva
        $IDDisponibilidad = $_POST["IDDisponibilidad"];
        $Repetir = $_POST["Repetir"];
        $Periodo = $_POST["Periodo"];
        $RepetirFechaFinal = $_POST["RepetirFechaFinal"];
        //Auxiliar-Boleador
        $IDAuxiliar = $_POST["IDAuxiliar"];
        $ListaAuxiliar = $_POST["ListaAuxiliar"];
        //Modalidad
        $IDTipoModalidadEsqui = $_POST["IDTipoModalidad"];
        //Tee
        $Tee = $_POST["Tee"];
        //Si se asigna la reserva a un beneficiario
        $IDBeneficiario = $_POST["IDBeneficiario"];
        $TipoBeneficiario = $_POST["TipoBeneficiario"];
        // Tipo Reserva (Dobles, sencillos, etc)
        $IDTipoReserva = $_POST["IDTipoReserva"];
        //Numero de invitados cuando es salon por ejemplo
        $CantidadInvitadoSalon = $_POST["CantidadInvitadoSalon"];
        //Si la reserva la hace un Empleado
        $IDUsuarioReserva = $_POST["IDUsuario"];
        $Altitud = $_POST["Altitud"];
        $Longitud = $_POST["Longitud"];
        $AdicionalesSocio = $_POST["AdicionalesSocio"];

        $respuesta = SIMWebService::set_reserva_generalV2($IDClub, $IDSocio, $IDElemento, $IDServicio, $Fecha, $Hora, $Campos, $Invitados, "", "", $Tee, $IDDisponibilidad, $Repetir, $Periodo, $RepetirFechaFinal, $IDTipoModalidadEsqui, $IDAuxiliar, $IDTipoReserva, "", $IDBeneficiario, $TipoBeneficiario, $IDUsuarioReserva, $CantidadInvitadoSalon, $ListaAuxiliar, $Altitud, $Longitud, $AdicionalesSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;




        //Eliminar Reserva
    case "eliminareservageneral":
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $EliminarParaMi = $_POST["EliminarParaMi"];
        $respuesta = SIMWebService::elimina_reserva_general($IDClub, $IDSocio, $IDReserva, "", "", $EliminarParaMi);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDReservaGeneral,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','".$IDReserva."','eliminareservageneral','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        if (!empty($_POST["paginaretorno"]))
            header("Location: " . $_POST["paginaretorno"] . "?mensaje=" . $respuesta[message]);


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));

        exit;
        break;




        //Enviar si socio cumplio o no la reserva
    case "setreservacumplida":
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDReservaGeneral = $_POST["IDReservaGeneral"];
        $ReservaCumplida = $_POST["ReservaCumplida"];
        $ReservaCumplidaSocio = $_POST["ReservaCumplidaSocio"];
        $Observacion = $_POST["Observacion"];
        $Invitados = $_POST["Invitados"];
        $ReservaCumplida = "S";
        $respuesta = SIMWebServiceApp::set_reserva_cumplida($IDClub, $IDSocio, $IDUsuario, $IDReservaGeneral, $ReservaCumplida, $ReservaCumplidaSocio, $Observacion, $Invitados);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDReservaGeneral,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','".$IDReservaGeneral."','setreservacumplida','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setreconfirmareserva":
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $respuesta = SIMWebServiceApp::set_reconfirma_reserva($IDClub, $IDSocio, $IDReserva);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDReservaGeneral,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','".$IDReserva."','setreconfirmareserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setinvitadogruposervicio":
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $Invitados = $_POST["Invitados"];
        $respuesta = SIMWebServiceApp::set_invitado_grupo_servicio($IDClub, $IDSocio, $IDReserva, $Invitados);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDReservaGeneral,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','".$IDReserva."','setinvitadogruposervicio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar si socio cumplio o no la reserva
    case "seteditaauxiliarreserva":
        $IDSocio = $_POST["IDSocio"];
        $IDReservaGeneral = $_POST["IDReservaGeneral"];
        $ListaAuxiliar = $_POST["ListaAuxiliar"];
        $respuesta = SIMWebServiceApp::set_edita_auxiliar_reserva($IDClub, $IDSocio, $IDReservaGeneral, $ListaAuxiliar);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDReservaGeneral,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','".$IDReservaGeneral."','seteditaauxiliarreserva','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        //Validar reserva con pago
    case "validapagoreserva":
        $IDSocio = SIMNet::req("IDSocio");
        $IDReservaGeneral = SIMNet::req("IDReservaGeneral");
        $respuesta = SIMWebServiceApp::valida_pago_reserva($IDClub, $IDSocio, $IDReservaGeneral);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validapagodomicilio":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $respuesta = SIMWebServiceDomicilios::valida_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, "");
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','validapagodomicilio','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validapagodomicilio2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDDomicilio = SIMNet::req("IDDomicilio");
        $respuesta = SIMWebServiceDomicilios::valida_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Validar reserva con pago
    case "validapagoevento":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDEventoRegistro = SIMNet::req("IDEventoRegistro");
        $respuesta = SIMWebServiceEventos::valida_pago_evento($IDClub, $IDSocio, $IDEventoRegistro);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Validar reserva con pago
    case "validapagoevento2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDEventoRegistro = SIMNet::req("IDEventoRegistro");
        $respuesta = SIMWebServiceEventos::valida_pago_evento($IDClub, $IDSocio, $IDEventoRegistro, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Campos de golf disponibles
    case "getdisponibilidadfecha":
        $IDCampo = SIMNet::req("IDCampo");
        $Fecha = SIMNet::req("Fecha");
        $Hora = SIMNet::req("Hora");
        $respuesta = SIMWebService::get_disponibilidad_fecha($IDClub, $IDCampo, $Fecha, $Hora);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$_GET["IDSocio"]."','getdisponibilidadfecha','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar Campos de golf disponibles
    case "getdisponibilidadcampo":
        $IDCampo = SIMNet::req("IDCampo");
        $Fecha = SIMNet::req("Fecha");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_disponibilidad_campo($IDClub, $IDCampo, $Fecha, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Inscripcion a eventos
    case "geteventosocio":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDEvento = SIMNet::req("IDEvento");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceEventos::get_evento_socio($IDClub, $IDSocio, 200, $IDEvento, "", $TipoApp);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('geteventosocio','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Inscripcion a eventos
    case "geteventosocio2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDEvento = SIMNet::req("IDEvento");
        $respuesta = SIMWebServiceEventos::get_evento_socio($IDClub, $IDSocio, 200, $IDEvento, "2");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('geteventosocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Elimina reserva inscripcion
    case "eliminaeventosocio":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDEventoRegistro = $_POST["IDEventoRegistro"];
        $respuesta = SIMWebServiceEventos::elimina_evento_socio($IDClub, $IDSocio, $IDEventoRegistro, "");
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminaeventosocio','".json_encode($_POST)."','')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "eliminaeventosocio2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDEventoRegistro = $_POST["IDEventoRegistro"];

        $respuesta = SIMWebServiceEventos::elimina_evento_socio($IDClub, $IDSocio, $IDEventoRegistro, "2");
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminaeventosocio2','".json_encode($_POST)."','')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        //Reservas del socio ultimas 15
    case "getreservasocio":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDReserva = SIMNet::req("IDReserva");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceReservas::get_reservas_socio($IDClub, $IDSocio, 200, $IDReserva, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservasocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //listar los invitados externos en la reserva
    case "getinvitadosexternosreservas":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceReservas::get_invitados_externos_reservas($IDClub, $IDSocio, $Tag, $IDServicio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservasocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // Guardar externos Favoritos
    case "setinvitadosexternosreservasfavorito":
        require LIBDIR  . "SIMWebServiceReservas.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDClubAsociado = $_POST["IDClubAsociado"];
        $Favorito = $_POST["Favorito"];
        $IDInvitadoExterno = $_POST["IDInvitadoExterno"];

        $respuesta = SIMWebServiceReservas::set_invitados_externos_reservas_favorito($IDClubAsociado, $IDSocio, $Favorito, $IDInvitadoExterno);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setinvitadosexternosreservasfavorito','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Reservas del socio todas
    case "getreservasociotodas":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceReservas::get_reservas_socio($IDClub, $IDSocio, 0);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservasocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Reservas del socio donde es invitado
    case "getreservasocioinvitado":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceReservas::get_reservas_socio_invitado($IDClub, $IDSocio, 0);

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservasocioinvitado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Reservas asociadas
    case "getreservaasociada":
        $IDSocio = SIMNet::req("IDSocio");
        $IDReserva = SIMNet::req("IDReserva");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $IDReserva, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Reservas que ha realizado el empleado
    case "getreservaempleado":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDReserva = SIMNet::req("IDReserva");
        $respuesta = SIMWebServiceReservas::get_reservas_empleado($IDClub, $IDUsuario, 15, $IDReserva);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getreservaempleado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Verificar Grupos golf
    case "verificarsociogrupo":
        $IDSocio = SIMNet::req("IDSocio");
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::verificar_socio_grupo_fecha($IDClub, $IDSocio, $Fecha, $IDServicio, $IDClubAsociado);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('verificarsociogrupo','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar fechas disponibles o no para reservas servicio
    case "getfechasdisponiblesservicio":
        $IDSocio = SIMNet::req("IDSocio");
        //Verifico Version del app
        $TipoApp = SIMNet::req("TipoApp");
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $IDFiltro = SIMNet::req("IDFiltro");
        $TipoFiltro = SIMNet::req("Tipo");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $IDTipoReserva = SIMNet::req("IDTipoReserva");
        $respuesta = SIMWebService::get_fecha_disponibilidad_servicio($IDClub, $IDServicio, $Fecha, "App", "", $IDFiltro, $TipoFiltro, $IDClubAsociado, $IDTipoReserva);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,IDServicio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','".$IDServicio."','getfechasdisponiblesservicio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        $nowserver = date("Y-m-d H:i:s");
        $miliseg = intval(round(microtime(true) * 1000));
        $nowserver .= "." . substr($miliseg, -3);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar auxiliares boleadores
    case "getauxiliares":
        $IDServicio = SIMNet::req("IDServicio");
        $IDReservaGeneral = SIMNet::req("IDReservaGeneral");
        $IDSocio = SIMNet::req("IDSocio");
        $Fecha = SIMNet::req("Fecha");
        $Hora = SIMNet::req("Hora");
        $ListaEspera = SIMNet::req("ListaEspera");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");


        //Si lista de espera es S llamo el servicio para ver a Todos
        if ($ListaEspera == "S")
            $VerSoloDisponibles = "N";
        else
            $VerSoloDisponibles = "S";

        $respuesta = SIMWebService::get_auxiliares($IDClub, $IDServicio, $Fecha, $Hora, $VerSoloDisponibles, $IDReservaGeneral, $IDClubAsociado, $IDSocio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getauxiliares','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Enviar modalidades club
    case "getmodalidades":
        $IDTipoModalidadEsqui = SIMNet::req("IDTipoModalidadEsqui");
        $IDElemento = SIMNet::req("IDElemento");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_modalidades($IDClub, $IDTipoModalidadEsqui, $IDElemento, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar mensajes socio push
    case "getnotificaciones":
        require LIBDIR . "SIMWebServiceNotificaciones.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceNotificaciones::get_notificaciones($IDClub, $IDSocio, $TipoApp);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getnotificaciones','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcumpleanosleido":
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Leido = $_POST["Leido"];
        $respuesta = SIMWebServiceApp::set_cumpleanos_leido($IDClub, $IDSocio, $IDUsuario, $Leido);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setnotificacionleida":
        require LIBDIR . "SIMWebServiceNotificaciones.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDLogNotificacion = $_POST["IDLogNotificacion"];
        $respuesta = SIMWebServiceNotificaciones::set_notificacion_leida($IDClub, $IDSocio, $IDLogNotificacion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getnoticacionlocal":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceUsuarios::get_noticacion_local($IDClub, $IDSocio, $IDUsuario);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getnoticacionlocal','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;





        //Guardar Reserva Golf
    case "setreservagolf":
        $IDSocio = $_POST["IDSocio"];
        $IDCampo = $_POST["IDCampo"];
        $IDServicio = $_POST["IDServicio"];
        $Fecha = $_POST["Fecha"];
        $Hora = $_POST["Hora"];
        $Campos = $_POST["Campos"];
        $Invitados = $_POST["Jugadores"];
        $Tee = $_POST["Tee"];
        $IDClubAsociado = $_POST["IDClubAsociado"];
        //$respuesta = SIMWebService::set_reserva_golf($IDClub,$IDSocio,$IDCampo,$IDServicio,$Fecha,$Hora,$Campos,$Invitados,$Tee);
        $respuesta = SIMWebService::set_reserva_general($IDClub, $IDSocio, $IDCampo, $IDServicio, $Fecha, $Hora, $Campos, $Invitados, "", "", $Tee, "", "", $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Guardar Tokenm
    case "settoken":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Dispositivo = $_POST["Dispositivo"];
        $Token = $_POST["Token"];
        $ClubToken = $_POST["ClubToken"];

        if (empty($Token) && !empty($ClubToken)) {
            $Token = $ClubToken;
        }

        $respuesta = SIMWebServiceUsuarios::set_token($IDClub, $IDSocio, $Dispositivo, $Token);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settoken','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "settokenempleado":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $Dispositivo = $_POST["Dispositivo"];
        $Token = $_POST["Token"];
        $ClubToken = $_POST["ClubToken"];

        if (empty($Token) && !empty($ClubToken)) {
            $Token = $ClubToken;
        }
        $respuesta = SIMWebServiceUsuarios::set_token_empleado($IDClub, $IDUsuario, $Dispositivo, $Token);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settokenempleado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Guardar Token
    case "setcambiarclave":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Clave = $_POST["Clave"];
        $SegundaClave = $_POST["SegundaClave"];
        $Correo = $_POST["Correo"];
        $Documento = $_POST["Documento"];
        $ClaveAnterior = $_POST["ClaveOriginal"];
        $respuesta = SIMWebServiceUsuarios::set_cambiar_clave($IDClub, $IDSocio, $Clave, $SegundaClave, $Correo, $ClaveAnterior, $Documento);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcambiarclave','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcambiarclaveempleado":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $Clave = $_POST["Clave"];

        $respuesta = SIMWebServiceUsuarios::set_cambiar_clave_empleado($IDClub, $IDUsuario, $Clave);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Segunda clave
    case "setcambiarsegundaclave":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Clave = $_POST["Clave"];
        $Correo = $_POST["Correo"];
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcambiarsegundaclave','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        $respuesta = SIMWebServiceUsuarios::set_cambiar_segunda_clave($IDClub, $IDSocio, $Clave, $Correo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;




        //Marcar Presalida cuando se hace por el módulo de busqueda de cualquier persona
    case "setpresalidacualquiera":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDInvitacion = $_POST["IDInvitacion"];
        $TipoInvitacion = $_POST["TipoInvitacion"];
        $TipoSocio = $_POST["TipoSocio"];
        $respuesta = SIMWebServiceAccesos::set_presalida_cualquiera($IDClub, $IDSocio, $IDInvitacion, $TipoInvitacion, $TipoSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpresalidacualquiera','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        exit;
        break;


        //Presalida Invitado
    case "setpresalida":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDInvitacion = $_POST["IDInvitacion"];
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcambiarsegundaclave','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        $respuesta = SIMWebServiceAccesos::set_presalida($IDClub, $IDSocio, $IDInvitacion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Presalida Contratista
    case "setpresalidacontratista":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDInvitacion = $_POST["IDInvitacion"];
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcambiarsegundaclave','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        $respuesta = SIMWebServiceAccesos::set_presalida_contratista($IDClub, $IDSocio, $IDInvitacion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validasegundaclave":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $Clave = SIMNet::req("Clave");
        $respuesta = SIMWebServiceUsuarios::valida_segunda_clave($IDClub, $IDSocio, $Clave);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;






    case "getvalidareserva":
        require(LIBDIR . "SIMServicioReserva.inc.php");
        $AppVersion = SIMNet::req("AppVersion");
        $Documento = SIMNet::req("Documento");
        $IDServicio = $_POST["IDServicio"];
        $respuesta = SIMServicioReserva::get_valida_reserva($IDClub, $Documento, $IDServicio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getvalidareserva','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setfacturaconsumo":
        $Accion = $_POST["Accion"];
        $Cedula = $_POST["Cedula"];
        $RucAsociado = $_POST["RucAsociado"];
        $TipoSocio = $_POST["TipoSocio"];
        $NumeroDocumentoFactura = $_POST["NumeroDocumentoFactura"];
        $Total = $_POST["Total"];
        $Iva = $_POST["Iva"];
        $Servicio = $_POST["Servicio"];
        $Estado = $_POST["Estado"];
        $IDFactura = $_POST["IDFactura"];
        $SubTotal0 = $_POST["SubTotal0"];
        $SubTotal12 = $_POST["SubTotal12"];
        $Detalle = utf8_encode($_POST["Detalle"]);
        $TextoRecibo = utf8_encode($_POST["TextoRecibo"]);
        $respuesta = SIMWebServiceApp::set_factura_consumo($IDClub, $Accion, $Cedula, $TipoSocio, $NumeroDocumentoFactura, $Total, $Iva, $Servicio, $Estado, $IDFactura, $Detalle, $TextoRecibo, $SubTotal0, $SubTotal12, $RucAsociado);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$_POST["IDSocio"]."','setfacturaconsumo','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getestadofacturaconsumo":
        $Accion = $_POST["Accion"];
        $Cedula = $_POST["Cedula"];
        $NumeroDocumentoFactura = $_POST["NumeroDocumentoFactura"];
        $respuesta = SIMWebServiceApp::get_estado_factura_consumo($IDClub, $Accion, $Cedula, $NumeroDocumentoFactura);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$_POST["IDSocio"]."','getestadofacturaconsumo','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getagenda":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $Fecha = SIMNet::req("Fecha");
        $respuesta = SIMWebServiceReservas::get_agenda($IDClub, $IDUsuario, $Fecha);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getagenda','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Facturas
    case "getfactura":
        require(LIBDIR . "SIMWebServiceFacturas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $FechaInicio = SIMNet::req("FechaInicio");
        $FechaFin = SIMNet::req("FechaFin");
        $Dispositivo = SIMNet::req("Dispositivo");
        $FiltrarGastosFamiliares = SIMNet::req("FiltrarGastosFamiliares");

        if (!empty($IDUsuario))
            $IDSocio = $IDUsuario;

        if ($IDClub == 10) //Rincon
            $respuesta = SIMWebServiceFacturas::get_factura_ftp($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 25) //Gun
            //$respuesta = SIMWebServiceFacturas::get_factura_zeus($IDClub,$IDSocio,$FechaInicio,$FechaFin);
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_gun($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 11) //Arrayanes
            // $respuesta = SIMWebServiceFacturas::get_factura_ftp_arrayanes($IDClub,$IDSocio,$FechaInicio,$FechaFin,$TipoApp);
            $respuesta = SIMWebServiceFacturas::get_factura_arrayanes_Zeus($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo, $TipoApp);
        elseif ($IDClub == 184) //pispesca
            $respuesta = SIMWebServiceFacturas::get_factura_pispesca($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 225) //PLAYA AZUL
            $respuesta = SIMWebServiceFacturas::get_facturas_hebraica($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 108) //Carmel
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_carmel($IDClub, $IDSocio, $FechaInicio, $FechaFin, $TipoApp);
        elseif ($IDClub == 95) { //La Sabana
            //$respuesta = SIMWebServiceFacturas::get_factura_ftp_sabana($IDClub,$IDSocio,$FechaInicio,$FechaFin,$TipoApp);
            $respuesta = SIMWebServiceFacturas::get_factura_sabana($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 76) //Sindamanoy
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_sindamanoy($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 27) //Payande
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_payande($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 46) //Anapoima
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_anapoima($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 12) //Rancho
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_r($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 72) //BTCC
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_btcc($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 37) //Polo Club
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_polo($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 1) //Guaymaral
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_g($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 70) //San Andres
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_s($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 56 || $IDClub == 00) //bataca
            $respuesta = SIMWebServiceFacturas::get_factura_bacata($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 29) //Farallones
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_f($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        //elseif ($IDClub == 227) //country medellin
        //$respuesta = SIMWebServiceFacturas::get_factura_country_medellin($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 15  || $IDClub == 8) //Pereira
            $respuesta = SIMWebServiceFacturas::get_factura_pereira($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 127) //cabo tortuga
            $respuesta = SIMWebServiceFacturas::get_factura_cabo_tortuga($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 71 || $IDClub == 150 || $IDClub == 158) //Invermetros
            $respuesta = SIMWebServiceFacturas::get_factura_invermetros($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 75) //Entrelomas
            $respuesta = SIMWebServiceFacturas::get_factura_entrelomas($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 59 || $IDClub == 58) // Conjuntos fontanar
            $respuesta = SIMWebServiceFacturas::get_factura_fontanar($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 50) //Uraki
            //$respuesta = SIMWebServiceFacturas::get_factura_uraki($IDClub,$IDSocio,$FechaInicio,$FechaFin);
            $respuesta = SIMWebServiceFacturas::get_factura_mi_club($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 41 || $IDClub == 87) //Sigma
            $respuesta = SIMWebServiceFacturas::get_factura_sigma($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 20) //Medellin
            $respuesta = SIMWebServiceFacturas::get_factura_medellin($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 8) //Mi Club
            $respuesta = SIMWebServiceFacturas::get_factura_pradera($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 203) //Mi Comunidad
            $respuesta = SIMWebServiceFacturas::get_factura_barranquilla(110, "358668", $FechaInicio, $FechaFin);
        elseif ($IDClub == 38) //Club Colombia
            $respuesta = SIMWebServiceFacturas::get_factura_colombia($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 54) //Italiano
            $respuesta = SIMWebServiceFacturas::get_factura_italiano($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 23) //Arrayanes E
            $respuesta = SIMWebServiceFacturas::get_factura_arrayanes_ec($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 135) //INKAS
            $respuesta = SIMWebServiceFacturas::get_factura_inka($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 141) //LAGUNITA
            $respuesta = SIMWebServiceFacturas::get_factura_lagunita($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 34) //RANCHO SAN FRANCISCO
            $respuesta = SIMWebServiceFacturas::get_factura_RSF($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 44) //Country Club Bogota
            $respuesta = SIMWebServiceFacturas::get_factura_mi_club_pagos($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 112) //BELLAVISTA
            $respuesta = SIMWebServiceFacturas::get_factura_Bellavista($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        elseif ($IDClub == 110 || $IDClub == 8) //COUNTRY BARRANQUILLA
            $respuesta = SIMWebServiceFacturas::get_factura_barranquilla($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 183) //VALLE ARRIBA
            $respuesta = SIMWebServiceFacturas::get_factura_valle_arriba($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        elseif ($IDClub == 189) //IZCARAGUA
            $respuesta = SIMWebServiceFacturas::get_factura_izcaragua($IDClub, $IDSocio);
        elseif ($IDClub == 172) //COMUNIDAD MINI
            $respuesta = SIMWebServiceFacturas::get_factura_comunidad_mini($IDClub, $IDSocio);
        elseif ($IDClub == 156) //PLAYA AZUL
            $respuesta = SIMWebServiceFacturas::get_factura_Playa_Azul($IDClub, $IDSocio);

        elseif ($IDClub == 205) //PLAYA AZUL
            $respuesta = SIMWebServiceFacturas::get_facturas_WTC($IDClub, $IDSocio);
        elseif ($IDClub == 7) { //Lagartos
            $respuesta = SIMWebServiceFacturas::get_factura_lagartos($IDClub, $IDSocio, $FechaInicio, $FechaFin);            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getfacturalagartos','".json_encode($_GET)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        } else {
            $respuesta = SIMWebServiceFacturas::get_factura($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        }


        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getfactura','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getconfiguracionfactura":

        require(LIBDIR . "SIMWebServiceFacturas.inc.php");
        //datos de configuracionfacturacion 
        $IDSocio = SIMNet::req("IDSocio");
        $IDClub = SIMNet::req("IDClub");
        $IDUsuario = SIMNet::req("IDUsuario");

        if (!empty($IDUsuario)) {
            $IDSocio = $IDUsuario;
        }

        $respuesta = SIMWebServiceFacturas::Configuracion_facturacion($IDClub, $IDSocio);

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getfactura','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getfacturav2":
        require(LIBDIR . "SIMWebServiceFacturas.inc.php");

        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $FechaInicio = SIMNet::req("FechaInicio");
        $FechaFin = SIMNet::req("FechaFin");
        $Dispositivo = SIMNet::req("Dispositivo");
        $Pagina = SIMNet::req("Pagina");
        $IDModulo = SIMNet::req("IDModulo");
        if ($Pagina > 0) {
            $Pagina = $Pagina;
        } else {
            $Pagina = 0;
        }




        if (!empty($IDUsuario)) {
            $IDSocio = $IDUsuario;
        }

        if ($IDClub == 16) { //Pradera
            $respuesta = SIMWebServiceFacturas::get_factura($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 70) {
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_s($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 141) { //LAGUNITA
            $respuesta = SIMWebServiceFacturas::get_factura_lagunita($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 72) { //BTCC
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_btcc($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 12) { //Rancho
            $respuesta = SIMWebServiceFacturas::get_factura_ftp_r($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 184) { //pispesca
            $respuesta = SIMWebServiceFacturas::get_factura_pispesca($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        } elseif ($IDClub == 44) { //Country Club Bogota
            $respuesta = SIMWebServiceFacturas::get_factura_mi_club_pagos($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        } elseif ($IDClub == 183) { //VALLE ARRIBA
            $respuesta = SIMWebServiceFacturas::get_factura_valle_arriba($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 56 || $IDClub == 00) { //bataca
            $respuesta = SIMWebServiceFacturas::get_factura_bacata($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo);
        } elseif ($IDClub == 15) { //CAMPESTRE PEREIRA 
            $respuesta = SIMWebServiceFacturas::get_factura_pereira($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo, $IDModulo);
        } elseif ($IDClub == 20) { //Medellin
            $respuesta = SIMWebServiceFacturas::get_factura_medellin($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 11) { //Arrayanes
            // $respuesta = SIMWebServiceFacturas::get_factura_ftp_arrayanes($IDClub,$IDSocio,$FechaInicio,$FechaFin,$TipoApp);
            $respuesta = SIMWebServiceFacturas::get_factura_arrayanes_Zeus($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo, $TipoApp);
        } elseif ($IDClub == 8) { //PRUEBA 
            $respuesta = SIMWebServiceFacturas::get_facturas_hebraica($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } elseif ($IDClub == 71 || $IDClub == 150 || $IDClub == 158) { //Invermetros
            $respuesta = SIMWebServiceFacturas::get_factura_invermetros($IDClub, $IDSocio, $FechaInicio, $FechaFin);
        } else {
            $timestamp = strtotime($FechaInicio);
            $FechaInicio = date("d-m-Y", $timestamp);


            $timestamp = strtotime($FechaFin);
            $FechaFin = date("d-m-Y", $timestamp);
            $fecha_actual = date("d-m-Y");
            if ($fecha_actual == $FechaInicio and $FechaInicio == $FechaFin) {
                $FechaInicio = date("d-m-Y", strtotime($FechaInicio . "- 1 month"));
            }

            $filtro = SIMNet::req("FiltrarGastosFamiliares");
            $FiltrarGastosFamiliares = "false";
            if ($filtro == "S") {
                $FiltrarGastosFamiliares = "true";
            }

            $respuesta = SIMWebServiceFacturas::get_factura_country_medellin($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo, $FiltrarGastosFamiliares);
        }

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getfacturav2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getfacturahistorial":
        require(LIBDIR . "SIMWebServiceFacturas.inc.php");

        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $FechaInicio = SIMNet::req("FechaInicio");
        $FechaFin = SIMNet::req("FechaFin");
        $Dispositivo = SIMNet::req("Dispositivo");
        $Pagina = SIMNet::req("Pagina");
        if ($Pagina > 0) {
            $Pagina = $Pagina;
        } else {
            $Pagina = 0;
        }

        $timestamp = strtotime($FechaInicio);
        $FechaInicio = date("d-m-Y", $timestamp);


        $timestamp = strtotime($FechaFin);
        $FechaFin = date("d-m-Y", $timestamp);

        $filtro = SIMNet::req("FiltrarGastosFamiliares");
        $FiltrarGastosFamiliares = "true";
        if ($filtro != "S") {
            $FiltrarGastosFamiliares = "false";
        }


        if (!empty($IDUsuario)) {
            $IDSocio = $IDUsuario;
        }
        $respuesta = SIMWebServiceFacturas::get_consumos_country_medellin($IDClub, $IDSocio, $FechaInicio, $FechaFin, $Dispositivo, $Pagina, $FiltrarGastosFamiliares);


        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getfactura','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        //Detalle Facturas
    case "getdetallefactura":
        require(LIBDIR . "SIMWebServiceFacturas.inc.php");

        $IDSocio = SIMNet::req("IDSocio");
        $IDFactura = SIMNet::req("IDFactura");
        $NumeroFactura = SIMNet::req("NumeroFactura");
        $Dispositivo = SIMNet::req("Dispositivo");
        $TipoApp = SIMNet::req("TipoApp");
        $Tipo = SIMNet::req("IDFactura"); //este es el tipo de facturas a consultar, consumos o cuentas abiertas (country medellin)

        if ($IDClub == 10) //Rincon
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_app($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 25) //Gun
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_gun($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 95) //La Sabana

            $respuesta = SIMWebServiceFacturas::get_detalle_factura_sabana($IDClub, $IDFactura, $NumeroFactura, $TipoApp);
        elseif ($IDClub == 56) //bacata
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_bacata($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio);
        elseif ($IDClub == 184) //pispesca
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_pispesca($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio);
        elseif ($IDClub == 225) //HEBRAICA
            $respuesta = SIMWebServiceFacturas::get_detalle_facturas_hebraica($IDClub, $IDSocio, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 11) //Arrayanes
            // $respuesta = SIMWebServiceFacturas::get_detalle_factura_arrayanes($IDClub,$IDFactura,$NumeroFactura,$TipoApp);
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_arrayanes_zeus($IDClub, $IDFactura, $NumeroFactura, $TipoApp);
        elseif ($IDClub == 108) //Carmel
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_carmel($IDClub, $IDFactura, $NumeroFactura, $TipoApp);
        elseif ($IDClub == 95) //Carmel
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_sabana($IDClub, $IDFactura, $NumeroFactura, $TipoApp);
        elseif ($IDClub == 76) //Sindamanoy
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_sindamanoy($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 27) //Payande
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_payande($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 227) //country medellin
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_country_medellin($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio, $Tipo);
        elseif ($IDClub == 15) //Gun
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_pereira($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio);
        elseif ($IDClub == 127) //cabo tortuga
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_cabo_tortuga($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio);
        elseif ($IDClub == 12) //Rancho
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_rancho($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 72) //BTCC
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_btcc($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 37) //polo
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_polo($IDClub, $IDFactura, $NumeroFactura, $IDSocio);
        elseif ($IDClub == 46) //Anapoima
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_anapoima($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 1) //Guaymaral
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_guaymaral($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 70) //San Andres
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_san_andres($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 29) //Farallones
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_farallones($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 7) //Lagartos
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_lagartos($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 71 || $IDClub == 150 || $IDClub == 158) //Invermetros
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_invermetros($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 75) //Entrelomas
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_entrelomas($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 38) //Club Colombia
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_colombia($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 54) //Italiano
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_italiano($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 58 || $IDClub == 59) //Pto Tranquilo
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_fontanar($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 50) //Uraki
            //$respuesta = SIMWebServiceFacturas::get_detalle_factura_uraki($IDClub,$IDFactura,$NumeroFactura);
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_mi_club($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 41 || $IDClub == 87) //Sigma
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_sigma($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 20) //Medellin
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_medellin($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 23) //Arrayanes EC
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_arrayanes_ec($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 135) //INKAS
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_inka($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 141) //LAGUNITA
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_lagunita($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 34) //RANCHO SAN FRANCISCO
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_RSF($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 44) //Country Club Bogota
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_mi_club_pagos($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 112) //BELLAVISTA
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_Bellavista($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 110) //COUNTRY BARRANQUILLA
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_barranquilla($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 183) //VALLE ARRIBA
            $respuesta = SIMWebServiceFacturas::detalle_factura_valle_arriba($IDClub, $IDSocio, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 189) //IZCARAGUA
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_izcaragua($IDClub, $IDSocio, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 172) //COMUNIDAD MINI
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_comunidad_mini($IDClub, $IDSocio, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 156) //PLAYA AZUL
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_playa_azul($IDClub, $IDSocio, $IDFactura, $NumeroFactura);
        //elseif ($IDClub == 8) //Mi Club
        //	$respuesta = SIMWebServiceFacturas::get_detalle_factura_pradera($IDClub, $IDFactura, $NumeroFactura);
        elseif ($IDClub == 203) //Mi Comunidad
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_barranquilla($IDClub, $IDFactura, $NumeroFactura);

        elseif ($IDClub == 205) //HEBRAICA
            $respuesta = SIMWebServiceFacturas::get_detalle_factura_WTC($IDClub, $IDSocio, $IDFactura, $NumeroFactura);
        else
            $respuesta = SIMWebServiceFacturas::get_detalle_factura($IDClub, $IDFactura, $NumeroFactura);


        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdetallefactura','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // SERVICIO VARIAS FACTURAS
    case "getdetallevariasfacturas":
        require(LIBDIR . "SIMWebServiceFacturas.inc.php");

        $IDSocio = SIMNet::req("IDSocio");
        $IDFactura = SIMNet::req("IDFactura");
        $NumeroFactura = SIMNet::req("NumeroFactura");
        $Dispositivo = SIMNet::req("Dispositivo");
        $TipoApp = SIMNet::req("TipoApp");

        if ($IDClub == 15 || $IDClub == 8) :
            $respuesta = SIMWebServiceFacturas::get_detalle_varias_factura_pereira($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio);
        endif;
        if ($IDClub == 127) :
            $respuesta = SIMWebServiceFacturas::get_detalle_varias_factura_cabo_tortuga($IDClub, $IDFactura, $NumeroFactura, $Dispositivo, $IDSocio);
        endif;

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getdetallevariasfacturas','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Certificados
    case "getcertificado":
        require LIBDIR . "SIMWebServiceFacturas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $FechaInicio = SIMNet::req("FechaInicio");
        $FechaFin = SIMNet::req("FechaFin");
        $Dispositivo = SIMNet::req("Dispositivo");

        if ($IDClub == 12) //Rancho
            $respuesta = SIMWebServiceFacturas::get_certificado($IDClub, $IDSocio, $FechaInicio, $FechaFin);

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getfactura','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Codigo Pago
    case "setcodigopago":
        require LIBDIR . "SIMWebServiceFacturas.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDFactura = $_POST["IDFactura"];
        $Codigo = $_POST["Codigo"];
        $respuesta = SIMWebServiceFacturas::set_codigo_pago($IDClub, $IDSocio, $IDFactura, $Codigo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcodigopago','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Extractos Gun Club
    case "getextracto":
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceZeus::consulta_extracto($IDClub, $IDSocio);
        //$respuesta = SIMWebServiceZeus::consulta_extracto("25","85176"); //TEMPORAL PARA PRUEBAS
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getextracto','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Movimientos Gun Club
    case "getmovimiento":
        $IDSocio = SIMNet::req("IDSocio");
        $Mes = SIMNet::req("Mes");
        //$respuesta = SIMWebServiceZeus::consulta_extracto($IDClub,$IDSocio,$Mes);
        //$respuesta = SIMWebServiceZeus::consulta_movimiento("25","85176","08"); //TEMPORAL PARA PRUEBAS
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getextracto','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //registrar estado cuenta ws
    case "setestadocuentaws":
        $IDRegistro =  $_POST["IDRegistro"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $Accion = $_POST["Accion"];
        $Secuencia = $_POST["Secuencia"];
        $Concepto = $_POST["Concepto"];
        $Valor = $_POST["Valor"];
        $Fecha = $_POST["Fecha"];
        $Observaciones = $_POST["Observaciones"];
        $respuesta = SIMWebServiceApp::set_estado_cuenta_ws($IDClub, $IDRegistro, $NumeroDocumento, $Accion, $Secuencia, $Concepto, $Valor, $Fecha, $Observaciones);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$_POST["IDSocio"]."','setestadocuentaws','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //registrar estado cuenta ws
    case "setborrarestadocuentaws":
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $respuesta = SIMWebServiceApp::set_borrar_estado_cuenta_ws($IDClub, $NumeroDocumento);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$_POST["IDSocio"]."','set_borrar_estado_cuenta_ws','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        /******************************************************
	/*SERVICIOS FEDEGOLF
	/******************************************************/


    case "getconfiguracionfedegolf":
        $respuesta = SIMWebServiceFedegolf::get_configuracion_fedegolf($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Clubes Fedegolf
    case "getclubfedegolf":
        $respuesta = SIMWebServiceFedegolf::get_clubes();
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Canchas Fedegolf segun club
    case "getcanchafedegolf":
        $IDClubFedegolf = SIMNet::req("IDClubFedegolf");
        $respuesta = SIMWebServiceFedegolf::get_canchas($IDClubFedegolf);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Marcas Fedegolf segun club - cancha
    case "getmarcafedegolf":
        $IDClubFedegolf = SIMNet::req("IDClubFedegolf");
        $IDCancha = SIMNet::req("IDCancha");
        $Codigo = SIMNet::req("Codigo");
        $respuesta = SIMWebServiceFedegolf::get_marcas($IDClubFedegolf, $IDCancha, $Codigo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Marcas Fedegolf segun club - cancha
    case "gethandicapfedegolf":
        //$Curva = SIMNet::req( "Curva" );
        //$Indice = SIMNet::req( "Indice" );

        $IDClubFedegolf = SIMNet::req("IDClubFedegolf");
        $IDCancha = SIMNet::req("IDCancha");
        $IDMarca = SIMNet::req("IDMarca");
        $IDSocio = SIMNet::req("IDSocio");
        $CodigoJugador = SIMNet::req("CodigoJugador");

        $respuesta = SIMWebServiceFedegolf::get_handicap($IDClubFedegolf, $IDCancha, $IDMarca, $CodigoJugador, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;

        break;

        //Autenticacion Usuarios Fedegolf
    case "getautenticacionfedegolf":
        $Email = SIMNet::req("Email");
        $Pwd = SIMNet::req("Pwd");
        $respuesta = SIMWebServiceFedegolf::get_autenticacion($Email, $Pwd);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //usuario por numero de codigo Fedegolf
    case "getusuariocodigofedegolf":
        $Codigo = SIMNet::req("Codigo");
        $respuesta = SIMWebServiceFedegolf::get_usuario_codigo($Codigo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Buscar usuario por parametro Fedegolf
    case "getusuarionombrefedegolf":
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceFedegolf::get_usuario_nombre($Tag);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getusuarionombrefedegolf','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Carne
    case "carnefedegolf":
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceFedegolf::carne_fedegolf($IDClub, $IDSocio);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','carnefedegolf','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Lista de juegos
    case "getgamesjugador":
        $IDSocio = SIMNet::req("IDSocio");
        $Codigo = SIMNet::req("Codigo");
        $respuesta = SIMWebServiceFedegolf::get_games_jugador($IDClub, $IDSocio, $Codigo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Detalle tarjeta juego
    case "getgamesfedegolf":
        $IDSocio = SIMNet::req("IDSocio");
        $Codigo = SIMNet::req("Codigo");
        $Score = SIMNet::req("Score");
        $DetalleScore = SIMNet::req("DetalleScore");
        $respuesta = SIMWebServiceFedegolf::get_games($IDClub, $IDSocio, $Score, $Codigo, $DetalleScore);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // Crear socio
    case "setsocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
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

        $respuesta = SIMWebServiceUsuarios::set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio, $Categoria, "", $CodigoCarne);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','POST 1: ".$datos_post."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','POST 2 ".json_encode($_POST)."','".json_encode($respuesta)."')");
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','GET: ".json_encode($_GET)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // Actualizar datos socios
    case "setpropietario":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $Nombre = utf8_encode($_POST["Nombre"]);
        $Apellido = utf8_encode($_POST["Apellido"]);
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $CorreoElectronico = $_POST["CorreoElectronico"];
        $Telefono = $_POST["Telefono"];
        $Celular = $_POST["Celular"];
        $Portal = utf8_encode($_POST["Portal"]);
        $Casa = $_POST["Casa"];
        $Llave = $_POST["Llave"];
        $AccionRegistro = $_POST["AccionRegistro"];    // Update, delete, insert
        $datos_post = $Nombre . " " . $Apellido . " " . $NumeroDocumento . " " . $CorreoElectronico . " " . $Telefono . " " . $Celular . " " . $Portal . " " . $Casa . " " . $Llave . " " . $AccionRegistro;
        $respuesta = SIMWebServiceUsuarios::set_propietario($IDClub, $Nombre, $Apellido, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Portal, $Casa, $Llave, $AccionRegistro);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpropietario','".$datos_post."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        /******************************************************
	/*FIN SERVICIOS FEDEGOLF
	/******************************************************/



        /******************************************************
	/*SERVICIOS HOTEL
	/******************************************************/

    case "getconfiguracionhotel":
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceHotel::get_configuracion_hotel($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getfechasdisponibleshotel":
        $IDSocio = SIMNet::req("IDSocio");
        if (empty($IDSocio)) :
            $IDSocio = SIMNet::req("IDUsuario");
        endif;
        //(SELECT FechaFin FROM `ReservaHotel` WHERE IDClub=8 and FechaFin>=now() ) UNION  (SELECT FechaInicio FROM `ReservaHotel` WHERE IDClub=8 and FechaInicio>=now() )
        $respuesta = SIMWebServiceHotel::get_fechas_disponibles_hotel($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        // Reservas hotel
    case "getmisreservashotel":
        $IDSocio = SIMNet::req("IDSocio");
        $IDReserva = SIMNet::req("IDReserva");
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        if ($IDClub == 27) : //Payande
            //$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisreservashotel&IDSocio=".$IDSocio);
            $datos = file_get_contents("http://payandereservas.miclubapp.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisreservashotel&IDSocio=" . $IDSocio);
            $respuesta = json_decode($datos, true);
        else :
            $respuesta = SIMWebServiceHotel::get_mis_reservas($IDSocio, $IDReserva);
        endif;

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpropietario','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Valida fecha
    case "getvalidafecha":
        $FechaInicio = SIMNet::req("FechaInicio");
        $FechaFin = SIMNet::req("FechaFin");
        $IDSocio = SIMNet::req("IDSocio");
        $version = SIMNet::req("AppVersion");

        if ($IDClub == 27) : //Payande
            if ((int)$version < 26) {
                //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','mensajeversion','".json_encode($_GET)."','".json_encode($respuesta)."')");
                $respuesta["message"] = "Por favor descargue la ultima version del app";
                $respuesta["success"] = false;
                $respuesta["response"] = NULL;
                die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
                exit;
            } else {
                //$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getvalidafecha&FechaInicio=".$FechaInicio."&FechaFin=".$FechaFin);
                $datos = file_get_contents("http://payandereservas.miclubapp.com/reservas/services/club.php?key=P4y4nd3Reser&action=getvalidafecha&FechaInicio=" . $FechaInicio . "&FechaFin=" . $FechaFin);
                $respuesta = json_decode($datos, true);
            }
        else :
            $respuesta = SIMWebServiceHotel::get_valida_fecha($IDClub, $FechaInicio, $FechaFin, "N", $IDSocio);
        endif;
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getvalidafecha','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));

        break;

    case "settipopagoreservahotel":
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        if ($IDClub == 27) { //Payande
            $key = "P4y4nd3Reser";
            $action = "settipopagoreservahotel";
            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocio'   => $IDSocio,
                'IDReserva'   => $IDReserva,
                'IDTipoPago'   => $IDTipoPago,
                'CodigoPago'   => $CodigoPago
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            print_r($response);
            exit;
        } else {
            $respuesta = SIMWebServiceHotel::set_tipo_pago_reserva_hotel($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $CodigoPago);
        }

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Invitados Hotel
    case "getmisinvitadoshotel":
        $IDSocio = SIMNet::req("IDSocio");
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        if ($IDClub == 27) : //Payande
            //$datos = file_get_contents("http://www.clubpayande.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisinvitadoshotel&IDSocio=".$IDSocio);
            $datos = file_get_contents("http://payandereservas.miclubapp.com/reservas/services/club.php?key=P4y4nd3Reser&action=getmisinvitadoshotel&IDSocio=" . $IDSocio);
            $respuesta = json_decode($datos, true);
        else :
            $respuesta = SIMWebServiceHotel::get_mis_invitados($IDSocio);
        endif;

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpropietario','".json_encode($_POST)."','".json_encode($respuesta)."')");
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Invitados Hotel
    case "setinvitadoshotel":
        $IDSocio = SIMNet::req("IDSocio");
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        $Documento = $_POST["Documento"];
        $Nombre = $_POST["Nombre"];
        $Apellido = $_POST["Apellido"];
        $Email = $_POST["Email"];
        $MenorEdad = $_POST["MenorEdad"];

        if ($IDClub == 27) : //Payande
            $key = "P4y4nd3Reser";
            $action = "setinvitadoshotel";
            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocio'   => $IDSocio,
                'Documento'   => $Documento,
                'Nombre'   => $Nombre,
                'Apellido'   => $Apellido,
                'Email'   => $Email
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            print_r($response);
            exit;
        else :
            $respuesta = SIMWebServiceHotel::set_invitados_hotel($IDSocio, $Documento, $Nombre, $Apellido, $Email, $MenorEdad);
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setinvitadoshotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        endif;

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "seteditainvitadohotel":
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $IDReservaHotelInvitado = $_POST["IDReservaHotelInvitado"];
        $IDSocioInvitado = $_POST["IDSocioInvitado"];
        $TipoInvitado = $_POST["TipoInvitado"];
        $Documento = $_POST["Documento"];
        $Nombre = $_POST["Nombre"];
        $Apellido = $_POST["Apellido"];
        $Email = $_POST["Email"];
        $MenorEdad = $_POST["MenorEdad"];
        $respuesta = SIMWebServiceHotel::set_edita_invitado_hotel($IDSocio, $IDReserva, $IDReservaHotelInvitado, $IDSocioInvitado, $TipoInvitado, $Documento, $Nombre, $Apellido, $Email, $MenorEdad);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setinvitadoshotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setagregainvitadohotel":
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $AcompananteSocio = $_POST["AcompananteSocio"];
        $respuesta = SIMWebServiceHotel::set_agrega_invitado_hotel($IDClub, $IDSocio, $IDReserva, $AcompananteSocio);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ('".$IDSocio."','set_agrega_invitado_hotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validafechapasadia":

        $FechaIncio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $respuesta = SIMWebServiceHotel::get_valida_fecha_pasadia($IDClub, $IDSocio, $FechaIncio, $FechaFin);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ('".$IDSocio."','set_agrega_invitado_hotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
        // SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteliminainvitadohotel":
        $IDReserva = SIMNet::req("IDReserva");
        $IDSocio = SIMNet::req("IDSocio");
        $IDReservaHotelInvitado = SIMNet::req("IDReservaHotelInvitado");
        $IDSocioInvitado = SIMNet::req("IDSocioInvitado");
        $TipoInvitado = SIMNet::req("TipoInvitado");
        $respuesta = SIMWebServiceHotel::set_elimina_invitado_hotel($IDReserva, $IDSocio, $IDReservaHotelInvitado, $TipoInvitado, $IDSocioInvitado);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','seteliminainvitadoshotel','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setreservahotel":

        $IDSocioMiClub = $_POST["IDSocio"];
        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocioMiClub . "'");
        //Averiguo el id del socio en el sistema de payande
        $IDSocio = $_POST["IDSocio"];
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        $IDBeneficiario = $_POST["IDBeneficiario"];
        $IDHabitacion = $_POST["IDHabitacion"];
        $IDPromocion = $_POST["IDPromocion"];
        $IDTemporadaAlta = $_POST["IDTemporadaAlta"];
        $Temporada = $_POST["Temporada"];
        $CabezaReserva = $_POST["CabezaReserva"];
        $Estado = $_POST["Estado"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $Ninera = $_POST["Ninera"];
        $Corral = $_POST["Corral"];
        $Observaciones = $_POST["Observaciones"];
        //$Valor = $_POST["Valor"];
        //$IVA = $_POST["IVA"];
        $NumeroPersonas = $_POST["NumeroPersonas"];
        $Adicional = $_POST["Adicional"];
        $IDInvitadoHotel = $_POST["IDInvitadoHotel"];
        //$Pagado = $_POST["Pagado"];
        //$FechaReserva = $_POST["FechaReserva"];

        $NombreDuenoReserva = $_POST["Nombre"];
        $DocumentoDuenoReserva = $_POST["Cedula"];
        $EmailDuenoReserva = $_POST["Email"];

        $Campos = $_POST["CamposReserva"];

        //Ajusto los datos que se envian al servicio de payande
        $datos_invitado = json_decode($_POST["AcompananteSocio"], true);
        $contador_invitado = 0;
        if (count($datos_invitado) > 0) :
            foreach ($datos_invitado as $datos_invitado_reserva) :
                $IDSocioInvitadoTurno = $datos_invitado_reserva["IDSocio"];
                $NombreSocioInvitadoTurno = $datos_invitado_reserva["Nombre"];
                $Familiar = "N";
                $array_datos_acompanante[$contador_invitado]["IDSocio"] = $dbo->getFields("Socio", "IDSocioSistemaExterno", "IDSocio = '" . $datos_invitado_reserva["IDSocio"] . "'");
                $array_datos_acompanante[$contador_invitado]["Nombre"] = $NombreSocioInvitadoTurno;
                $array_datos_acompanante[$contador_invitado]["IDInvitado"] = $datos_invitado_reserva["IDInvitado"];
                //Verifico si es familiar
                if (!empty($IDSocioInvitadoTurno)) :
                    $AccionSocioInvitado = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocioInvitadoTurno . "'");
                    $array_datos_acompanante[$contador_invitado]["Nombre"]  = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitadoTurno . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitadoTurno . "'");
                    if ($AccionSocioInvitado == $AccionSocio)
                        $Familiar = "S";
                endif;
                $array_datos_acompanante[$contador_invitado]["Familiar"] = $Familiar;
                $contador_invitado++;
            endforeach;
        endif;

        $AcompananteSocio = json_encode($array_datos_acompanante);

        if ($IDClub == 27) : //Payande

            $key = "P4y4nd3Reser";
            $action = "setreserva";

            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";

            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocioMiClub'   => $IDSocioMiClub,
                'IDInvitadoHotel'   => $IDInvitadoHotel,
                'IDBeneficiario'   => $IDBeneficiario,
                'AccionSocio'   => $AccionSocio,
                'IDSocio'   => $IDSocio,
                'IDHabitacion'   => $IDHabitacion,
                'IDPromocion'   => $IDPromocion,
                'IDTemporadaAlta'   => $IDTemporadaAlta,
                'Temporada'   => $Temporada,
                'CabezaReserva'   => $CabezaReserva,
                'FechaInicio'   => $FechaInicio,
                'FechaFin'   => $FechaFin,
                'Ninera'   => $Ninera,
                'Corral'   => $Corral,
                'NumeroPersonas'   => $NumeroPersonas,
                'Adicional'   => $Adicional,
                'AcompananteSocio'   => $AcompananteSocio
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);

            // close the connection, release resources used
            curl_close($ch);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreservahotel','".json_encode($_POST)."','".json_encode($response)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
            print_r($response);
            exit;
        else :
            $AcompananteSocio = $_POST["AcompananteSocio"];
            $respuesta = SIMWebServiceHotel::set_reserva($IDClub, $IDSocio, $IDInvitadoHotel, $IDBeneficiario, $IDHabitacion, $IDPromocion, $IDTemporadaAlta, $Temporada, $CabezaReserva, $Estado, $FechaInicio, $FechaFin, $Ninera, $Corral, $IVA, $NumeroPersonas, $Adicional, $Pagado, $FechaReserva, $AcompananteSocio, "", "", $NombreDuenoReserva, $DocumentoDuenoReserva, $EmailDuenoReserva, $Observaciones, $Campos);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        endif;

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setreservahotelv2":
        $IDSocioMiClub = $_POST["IDSocio"];
        $AccionSocio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocioMiClub . "'");
        //Averiguo el id del socio en el sistema de payande
        $IDSocio = $_POST["IDSocio"];
        //$IDSocio =  $dbo->getFields( "Socio" , "IDSocioSistemaExterno" , "IDSocio = '".$IDSocio."'" );
        $IDBeneficiario = $_POST["IDBeneficiario"];
        $IDHabitacion = $_POST["IDHabitacion"];
        $IDPromocion = $_POST["IDPromocion"];
        $IDTemporadaAlta = $_POST["IDTemporadaAlta"];
        $Temporada = $_POST["Temporada"];
        $CabezaReserva = $_POST["CabezaReserva"];
        $Estado = $_POST["Estado"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $Ninera = $_POST["Ninera"];
        $Corral = $_POST["Corral"];
        $Observaciones = $_POST["Observaciones"];
        //$Valor = $_POST["Valor"];
        //$IVA = $_POST["IVA"];
        $NumeroPersonas = $_POST["NumeroPersonas"];
        $Adicional = $_POST["Adicional"];
        $IDInvitadoHotel = $_POST["IDInvitadoHotel"];
        //$Pagado = $_POST["Pagado"];
        //$FechaReserva = $_POST["FechaReserva"];

        $NombreDuenoReserva = $_POST["Nombre"];
        $DocumentoDuenoReserva = $_POST["Cedula"];
        $EmailDuenoReserva = $_POST["Email"];

        $Campos = $_POST["CamposReserva"];
        $TipoReserva = $_POST["TipoReserva"];

        //Ajusto los datos que se envian al servicio de payande
        $datos_invitado = json_decode($_POST["AcompananteSocio"], true);
        $contador_invitado = 0;
        if (count($datos_invitado) > 0) :
            foreach ($datos_invitado as $datos_invitado_reserva) :
                $IDSocioInvitadoTurno = $datos_invitado_reserva["IDSocio"];
                $NombreSocioInvitadoTurno = $datos_invitado_reserva["Nombre"];
                $Familiar = "N";
                $array_datos_acompanante[$contador_invitado]["IDSocio"] = $dbo->getFields("Socio", "IDSocioSistemaExterno", "IDSocio = '" . $datos_invitado_reserva["IDSocio"] . "'");
                $array_datos_acompanante[$contador_invitado]["Nombre"] = $NombreSocioInvitadoTurno;
                $array_datos_acompanante[$contador_invitado]["IDInvitado"] = $datos_invitado_reserva["IDInvitado"];
                //Verifico si es familiar
                if (!empty($IDSocioInvitadoTurno)) :
                    $AccionSocioInvitado = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocioInvitadoTurno . "'");
                    $array_datos_acompanante[$contador_invitado]["Nombre"]  = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocioInvitadoTurno . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocioInvitadoTurno . "'");
                    if ($AccionSocioInvitado == $AccionSocio)
                        $Familiar = "S";
                endif;
                $array_datos_acompanante[$contador_invitado]["Familiar"] = $Familiar;
                $contador_invitado++;
            endforeach;
        endif;

        $AcompananteSocio = json_encode($array_datos_acompanante);

        if ($IDClub == 27) : //Payande

            $key = "P4y4nd3Reser";
            $action = "setreserva";

            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";

            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocioMiClub'   => $IDSocioMiClub,
                'IDInvitadoHotel'   => $IDInvitadoHotel,
                'IDBeneficiario'   => $IDBeneficiario,
                'AccionSocio'   => $AccionSocio,
                'IDSocio'   => $IDSocio,
                'IDHabitacion'   => $IDHabitacion,
                'IDPromocion'   => $IDPromocion,
                'IDTemporadaAlta'   => $IDTemporadaAlta,
                'Temporada'   => $Temporada,
                'CabezaReserva'   => $CabezaReserva,
                'FechaInicio'   => $FechaInicio,
                'FechaFin'   => $FechaFin,
                'Ninera'   => $Ninera,
                'Corral'   => $Corral,
                'NumeroPersonas'   => $NumeroPersonas,
                'Adicional'   => $Adicional,
                'AcompananteSocio'   => $AcompananteSocio
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);


            // close the connection, release resources used
            curl_close($ch);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreservahotel','".json_encode($_POST)."','".json_encode($response)."')");
            //SIMLog::insert_app($action, $IDClub, $_POST, $response);
            print_r($response);
            exit;
        else :
            $AcompananteSocio = $_POST["AcompananteSocio"];
            $respuesta = SIMWebServiceHotel::set_reservav2($IDClub, $IDSocio, $IDInvitadoHotel, $IDBeneficiario, $IDHabitacion, $IDPromocion, $IDTemporadaAlta, $Temporada, $CabezaReserva, $Estado, $FechaInicio, $FechaFin, $Ninera, $Corral, $IVA, $NumeroPersonas, $Adicional, $Pagado, $FechaReserva, $AcompananteSocio, "", "", $NombreDuenoReserva, $DocumentoDuenoReserva, $EmailDuenoReserva, $Observaciones, $Campos, $TipoReserva);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        endif;

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Notificar lista de espera
    case "notificarlistaesperahotel":
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $respuesta = SIMUtil::notificar_lista_espera_hotel($IDClub, $FechaInicio, $FechaFin);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Eliminar Reserva Hotel
    case "eliminareservahotel":
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        if ($IDClub == 27) : //Payande
            $key = "P4y4nd3Reser";
            $action = "eliminareservahotel";
            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocioMiClub'   => $IDSocio,
                'IDReserva'   => $IDReserva
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            print_r($response);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminareservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
            exit;
        else :
            $respuesta = SIMWebServiceHotel::elimina_reserva_hotel($IDClub, $IDSocio, $IDReserva);
        endif;

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminareservahotel','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Validar reserva con pago
    case "validapagoreservahotel":
        $IDSocio = SIMNet::req("IDSocio");
        $IDReserva = SIMNet::req("IDReserva");

        if ($IDClub == 27) : //Payande
            $key = "P4y4nd3Reser";
            $action = "validapagoreservahotel";
            //$url="http://www.clubpayande.com/reservas/services/club.php";
            $url = "http://payandereservas.miclubapp.com/reservas/services/club.php";
            $post = [
                'key' => $key,
                'action' => $action,
                'IDSocio'   => $IDSocio,
                'IDReserva'   => $IDReserva
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            print_r($response);
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','validapagoreservahotel','".json_encode($_GET)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
            exit;
        else :
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','validapagoreservahotel','".json_encode($_GET)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
            $respuesta = SIMWebServiceHotel::valida_pago_reserva_hotel($IDSocio, $IDReserva);
        endif;


        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        /******************************************************
	/*FIN SERVICIOS HOTEL
	/******************************************************/



        /******************************************************
	SERVICIOS ESPECIAL CONDADO
	/******************************************************/
    case "setcrearcuenta":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $Accion = $_POST["Accion"];
        $Identificacion = $_POST["Identificacion"];
        $CorreoElectronico = $_POST["Correo"];
        $respuesta = SIMWebServiceUsuarios::set_crear_cuenta($IDClub, $Accion, $Identificacion, $CorreoElectronico);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcrearcuenta','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Header para nuevo diseño de club
    case "getheaderclub":
        $respuesta = SIMWebServiceApp::get_header_club($IDClub);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getheaderclub','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setactualizadatos":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Direccion = $_POST["Direccion"];
        $Telefono = $_POST["Telefono"];
        $DireccionOficina = $_POST["DireccionOficina"];
        $TelefonoOficina = $_POST["TelefonoOficina"];
        $Celular = $_POST["Celular"];
        $CorreoElectronico = $_POST["CorreoElectronico"];
        $respuesta = SIMWebServiceUsuarios::set_actualiza_datos($IDClub, $IDSocio, $Direccion, $Telefono, $DireccionOficina, $TelefonoOficina, $Celular, $CorreoElectronico);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setactualizadatos','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdatossociows":
        $respuesta = SIMWebServiceApp::get_datos_socio_ws($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getheaderclub','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdeudasocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceUsuarios::get_deuda_socio($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdeudasocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setdeudasocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Documento = $_POST["Documento"];
        $ValorPagar = $_POST["ValorPagar"];
        $respuesta = SIMWebServiceUsuarios::set_deuda_socio($IDClub, $IDSocio, $Documento, $ValorPagar);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setdeudasocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getplacetopaytransacciones":
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceApp::get_place_to_pay_transacciones($IDClub, $IDSocio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setdeudasocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        /******************************************************
	FIN SERVICIOS ESPECIAL CONDADO
	/******************************************************/


        //Pago Reserva
    case "settipopagoreserva":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDReserva = $_POST["IDReserva"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        $IDTaloneraDisponible = $_POST["IDTaloneraDisponible"];
        $IDClubAsociado = $_POST["IDClubAsociado"];
        $IDPorcentajeAbono = $_POST["IDPorcentajeAbono"];
        $PagarMasTarde = $_POST["PagarMasTarde"];
        $respuesta = SIMWebServiceReservas::set_tipo_pago_reserva($IDClub, $IDSocio, $IDReserva, $IDTipoPago, $CodigoPago, $IDTaloneraDisponible, $IDClubAsociado, $IDPorcentajeAbono, $PagarMasTarde);
        //inserta _log
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcodigopago":
        require LIBDIR . "SIMWebServiceDocumentos.inc.php";
        $CodigoPago = SIMNet::req("CodigoPago");
        $respuesta = SIMWebServiceDocumentos::get_codigo_pago($IDClub, $CodigoPago);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcodigopago','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Pago Evento
    case "settipopagoevento":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = $_POST["IDSocio"];

        if ($_POST["TipoApp"] == "Empleado")
            $IDSocio = $_POST["IDUsuario"];

        $IDEventoRegistro = $_POST["IDEventoRegistro"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        $respuesta = SIMWebServiceEventos::set_tipo_pago_evento($IDClub, $IDSocio, $IDEventoRegistro, $IDTipoPago, $CodigoPago);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoevento','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settipopagodomicilio":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDDomicilio = $_POST["IDDomicilio"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        $respuesta = SIMWebServiceDomicilios::set_tipo_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, $IDTipoPago, $CodigoPago);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagodomicilio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settipopagodomicilio2":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDDomicilio = $_POST["IDDomicilio"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        $respuesta = SIMWebServiceDomicilios::set_tipo_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, $IDTipoPago, $CodigoPago, "2");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settipopagodomicilio3":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDDomicilio = $_POST["IDDomicilio"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        $respuesta = SIMWebServiceDomicilios::set_tipo_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, $IDTipoPago, $CodigoPago, "3");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settipopagodomicilio4":
        require LIBDIR . "SIMWebServiceDomicilios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDDomicilio = $_POST["IDDomicilio"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        $respuesta = SIMWebServiceDomicilios::set_tipo_pago_domicilio($IDClub, $IDSocio, $IDDomicilio, $IDTipoPago, $CodigoPago, "4");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settipopagoevento2":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDEventoRegistro = $_POST["IDEventoRegistro"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];
        $respuesta = SIMWebServiceEventos::set_tipo_pago_evento($IDClub, $IDSocio, $IDEventoRegistro, $IDTipoPago, $CodigoPago, "2");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settipopagoreserva','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getcodigopagoevento":
        require LIBDIR . "SIMWebServiceEventos.inc.php";
        $CodigoPago = SIMNet::req("CodigoPago");
        $respuesta = SIMWebServiceEventos::get_codigo_pago($IDClub, $CodigoPago);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcodigopago','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "setcerrarsesion":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceUsuarios::set_cerrar_sesion($IDClub, $IDSocio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcerrarsesion','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getdatossocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $Identificacion = SIMNet::req("Identificacion");
        $Todos = SIMNet::req("Todos"); // S / N
        $respuesta = SIMWebServiceUsuarios::get_datos_socio($IDClub, $Identificacion, $Todos);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcerrarsesion','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmodulowebview":
        require LIBDIR . "SIMWebServiceClub.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDModulo = SIMNet::req("IDModulo");
        $TipoApp = SIMNet::req("TipoApp");
        $respuesta = SIMWebServiceClub::get_moduloweb_view($IDClub, $IDSocio, $IDModulo, $TipoApp);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmodulowebview','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcampoacceso":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $respuesta = SIMWebServiceAccesos::get_campo_acceso($IDClub);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcampoacceso','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;

    case "getdiagnostico":
        require LIBDIR . "SIMWebServiceDiagnostico.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceDiagnostico::get_diagnostico($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnostico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisdiagnosticos":
        require LIBDIR . "SIMWebServiceDiagnostico.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceDiagnostico::get_mis_diagnosticos($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisdiagnosticos','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestadiagnostico":
        require LIBDIR . "SIMWebServiceDiagnostico.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDDiagnostico = $_POST["IDDiagnostico"];
        $IDBeneficiario = $_POST["IDBeneficiario"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $Nombre = $_POST["Nombre"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceDiagnostico::set_respuesta_diagnostico($IDClub, $IDSocio, $IDDiagnostico, $Respuestas, $IDUsuario, $NumeroDocumento, $Nombre, $IDBeneficiario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrespuestadiagnostico','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdiagnosticopersona":
        require LIBDIR . "SIMWebServiceDiagnostico.inc.php";
        $Fecha = $_POST["Fecha"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $respuesta = SIMWebServiceDiagnostico::get_diagnostico_persona($IDClub, $NumeroDocumento, $Fecha);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnosticopersona','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionencuestacalificacion":
        require LIBDIR . "SIMWebServiceEncuestas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceEncuestas::get_configuracion_encuesta_calificacion($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionenecuestacalificacion','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionencuesta":
        require LIBDIR . "SIMWebServiceEncuestas.inc.php";
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceEncuestas::get_configuracion_encuesta($IDClub, $IDSocio, $IDUsuario, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionenecuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisencuestashistorial":
        require LIBDIR . "SIMWebServiceEncuestas.inc.php";
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceEncuestas::get_mis_encuestas_historial($IDClub, $IDSocio, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionenecuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getencuestacalificacion":
        require LIBDIR . "SIMWebServiceEncuestas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceEncuestas::get_encuesta_calificacion($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnostico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestaencuestacalificacion":
        require LIBDIR . "SIMWebServiceEncuestas.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDEncuesta = $_POST["IDEncuesta"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceEncuestas::set_respuesta_encuesta_calificacion($IDClub, $IDSocio, $IDEncuesta, $Respuestas, $IDUsuario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrespuestaencuestacalificacion','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



    case "getencuesta":
        require LIBDIR . "SIMWebServiceEncuestas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceEncuestas::get_encuesta($IDClub, $IDSocio, $IDUsuario, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_encuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestaencuesta":
        require LIBDIR . "SIMWebServiceEncuestas.inc.php";
        $Archivo = $_POST["Imagen1"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDEncuesta = $_POST["IDEncuesta"];
        $Respuestas = $_POST["Respuestas"];

        /*
		if($IDClub==8){
			require( LIBDIR . "SIMEncuesta.inc.php" );
			$respuesta = SIMEncuesta::set_respuesta_encuesta($IDClub,$IDSocio,$IDEncuesta,$Respuestas,$IDUsuario,$Archivo,$_FILES);
		}
		else{
			$respuesta = SIMWebServiceApp::set_respuesta_encuesta($IDClub,$IDSocio,$IDEncuesta,$Respuestas,$IDUsuario,$Archivo,$_FILES);
		}
		*/

        $respuesta = SIMWebServiceEncuestas::set_respuesta_encuesta($IDClub, $IDSocio, $IDEncuesta, $Respuestas, $IDUsuario, $Archivo, $_FILES);

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrespuestaencuesta','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Encuesta Vial
    case "getconfiguracionseguridadvial":
        require(LIBDIR . "SIMWebServiceVial.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVial::get_configuracion_seguridad_vial($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_encuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getseguridadvial":
        require(LIBDIR . "SIMWebServiceVial.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDCarro = SIMNet::req("IDCarro");
        $respuesta = SIMWebServiceVial::get_seguridad_vial($IDClub, $IDSocio, $IDUsuario, $IDCarro);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_encuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcarrosseguridadvial":
        require(LIBDIR . "SIMWebServiceVial.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVial::get_carros_seguridad_vial($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_encuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setseguridadvial":
        require(LIBDIR . "SIMWebServiceVial.inc.php");
        $Archivo = $_POST["Imagen1"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDFormulario = $_POST["IDFormulario"];
        $Respuestas = $_POST["Respuestas"];
        $IDCarro = $_POST["IDCarro"];

        $respuesta = SIMWebServiceVial::set_seguridad_vial($IDClub, $IDSocio, $IDFormulario, $Respuestas, $IDUsuario, $IDCarro, $Archivo, $_FILES);

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setseguridadvial','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcarroseguridadvial":
        require(LIBDIR . "SIMWebServiceVial.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Placa = $_POST["Placa"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceVial::set_carro_seguridad_vial($IDClub, $IDSocio, $Placa, $Respuestas, $IDUsuario, $Archivo, $_FILES);

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcarroseguridadvial','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gethistorialseguridadvial":
        require(LIBDIR . "SIMWebServiceVial.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVial::get_historial_seguridad_vial($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_encuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setactivarcarroseguridadvial":
        require(LIBDIR . "SIMWebServiceVial.inc.php");
        $IDCarro = $_POST["IDCarro"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Estado = $_POST["Estado"];
        $respuesta = SIMWebServiceVial::set_activar_carro_seguridad_vial($IDClub, $IDCarro, $IDSocio, $IDUsuario, $Estado);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setactivarcarroseguridadvial','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Fin Encuesta vial

    case "getdotacion":
        require LIBDIR . "SIMWebServiceDotacion.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceDotacion::get_dotacion($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdotacion','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestadotacion":
        require LIBDIR . "SIMWebServiceDotacion.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDDotacion = $_POST["IDDotacion"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceDotacion::set_respuesta_dotacion($IDClub, $IDSocio, $IDDotacion, $Respuestas, $IDUsuario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrespuestadotacion','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getrutas":
        require LIBDIR . "SIMWebServiceRuta.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceRuta::get_rutas($IDClub, $IDSocio, $IDUsuario, $Tag);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getrutas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getpersonasruta":
        require LIBDIR . "SIMWebServiceRuta.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDRuta = SIMNet::req("IDRuta");
        $respuesta = SIMWebServiceRuta::get_personas_ruta($IDClub, $IDSocio, $IDUsuario, $IDRuta);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getpersonasruta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setingresoruta":
        require LIBDIR . "SIMWebServiceRuta.inc.php";
        $IDRuta = $_POST["IDRuta"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDPersona = $_POST["IDPersona"];
        $Tipo = $_POST["Tipo"]; //Socio,Funcionario
        $respuesta = SIMWebServiceRuta::set_ingreso_ruta($IDClub, $IDRuta, $IDSocio, $IDUsuario, $IDPersona, $Tipo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setingresoruta','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionvotacion":
        require LIBDIR . "SIMWebServiceVotacion.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVotacion::get_configuracion_votacion($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionvotacion','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getvotacion":
        require LIBDIR . "SIMWebServiceVotacion.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVotacion::get_votacion($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('get_encuesta','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestavotacion":
        require LIBDIR . "SIMWebServiceVotacion.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDVotacion = $_POST["IDVotacion"];
        $Respuestas = $_POST["Respuestas"];
        $Dispositivo = $_POST["Dispositivo"];
        $respuesta = SIMWebServiceVotacion::set_respuesta_votacion($IDClub, $IDSocio, $IDVotacion, $Respuestas, $IDUsuario, $Dispositivo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrespuestavotacion','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "setactualizadatossocio":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $Archivo = $_POST["Imagen1"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Campos = $_POST["Campos"];
        $TipoApp = $_POST["TipoApp"];

        $respuesta = SIMWebServiceUsuarios::set_actualiza_datos_socio($IDClub, $IDSocio, $Campos, $IDUsuario, $TipoApp, $Archivo, $_FILES);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setactualizadatossocio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Correspondencia
    case "setcorrespondencia":
        require LIBDIR . "SIMWebServiceCorrespondencia.inc.php";
        if (count($_FILES) > 0)
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDTipoCorrrespondencia = $_POST["IDTipoCorrrespondencia"];
        $Vivienda = $_POST["Vivienda"];
        $Destinatario = $_POST["Destinatario"];
        $FechaRecepcion = $_POST["FechaRecepcion"];
        $HoraRecepcion = $_POST["HoraRecepcion"];
        $EntregarATodos = $_POST["EntregarATodos"];
        $Archivo = $_POST["Archivo"];

        $respuesta = SIMWebServiceCorrespondencia::set_correspondencia($IDClub, $IDSocio, $IDUsuario, $IDTipoCorrrespondencia, $Vivienda, $Destinatario, $FechaRecepcion, $HoraRecepcion, $EntregarATodos, $Archivo, $_FILES);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcorrespondencia','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setentregacorrespondencia":
        require LIBDIR . "SIMWebServiceCorrespondencia.inc.php";
        if (count($_FILES) > 0)
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDCorrespondencia = $_POST["IDCorrespondencia"];
        $FechaEntrega = $_POST["FechaEntrega"];
        $HoraEntrega = $_POST["HoraEntrega"];
        $EntregadoA = $_POST["EntregadoA"];
        $Archivo = $_POST["FotoFirma"];
        $Dispositivo = $_POST["Dispositivo"];

        $respuesta = SIMWebServiceCorrespondencia::set_entrega_correspondencia($IDClub, $IDSocio, $IDUsuario, $IDCorrespondencia, $FechaEntrega, $HoraEntrega, $EntregadoA, $Archivo, $_FILES, $Dispositivo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setentregacorrespondencia','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcorrespondencia":
        require LIBDIR . "SIMWebServiceCorrespondencia.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceCorrespondencia::get_correspondencia($IDClub, $IDSocio, $Tag);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gettipocorrespondencia":
        require LIBDIR . "SIMWebServiceCorrespondencia.inc.php";
        $respuesta = SIMWebServiceCorrespondencia::get_tipo_correspondencia($IDClub);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettipocorrespondencia','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Configuracion correspondencia
    case "getconfiguracioncorrespondencia":
        require LIBDIR . "SIMWebServiceCorrespondencia.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceCorrespondencia::get_configuracion_correspondencia($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracioncorrespondencia','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        // Fin Correspondencia

        //Configuracion  Noticia
    case "getconfiguracionnoticias":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceNoticias::get_configuracion_noticias($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionnoticias','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Configuracion  Noticia Infinitas
    case "getconfiguracionnoticiasinfinitas":
        require(LIBDIR . "SIMWebServiceNoticiaInfinitas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceNoticiaInfinitas::get_configuracion_noticias_infinitas($IDClub, $IDSocio, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionnoticias','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getseccionnoticiasinfinitas":
        require(LIBDIR . "SIMWebServiceNoticiaInfinitas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $TipoApp = SIMNet::req("TipoApp");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceNoticiaInfinitas::get_seccion_noticias_infinitas($IDClub, $IDSocio, $IDUsuario, $TipoApp, "", $IDModulo);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getseccion','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getnoticiasinfinitas":
        require(LIBDIR . "SIMWebServiceNoticiaInfinitas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDSeccion = SIMNet::req("IDSeccion");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Tag = SIMNet::req("Tag");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceNoticiaInfinitas::get_noticias_infinitas($IDClub, $IDSeccion, $IDSocio, $Tag, "", $IDUsuario, $IDModulo);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getnoticias','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setlikenoticiasinfinitas":
        require(LIBDIR . "SIMWebServiceNoticiaInfinitas.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDNoticia = $_POST["IDNoticia"];
        $HacerLike = $_POST["HacerLike"];
        $IDModulo = $_POST["IDModulo"];
        $respuesta = SIMWebServiceNoticiaInfinitas::set_like_noticias_infinitas($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $HacerLike, "", $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcomentariosnoticiasinfinitas":
        require(LIBDIR . "SIMWebServiceNoticiaInfinitas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDNoticia = SIMNet::req("IDNoticia");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Pagina = SIMNet::req("Pagina");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceNoticiaInfinitas::get_comentarios_noticias_infinitas($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Pagina, "", $IDModulo);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getnoticias','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcomentarnoticiasinfinitas":
        require(LIBDIR . "SIMWebServiceNoticiaInfinitas.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDNoticia = $_POST["IDNoticia"];
        $Comentario = $_POST["Comentario"];
        $IDModulo = $_POST["IDModulo"];
        $respuesta = SIMWebServiceNoticiaInfinitas::set_comentar_noticias_infinitas($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Comentario, "", $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN Configuracion  Noticia Infinitas



    case "setcodigoqr":
        $IDSocio = $_POST["IDSocio"];
        $CodigoQR = $_POST["CodigoQR"];
        $respuesta = SIMWebServiceApp::set_codigo_qr($IDClub, $IDSocio, $CodigoQR);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setentregacorrespondencia','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setsolicitarvehiculo":
        require LIBDIR . "SIMWebServiceValet.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $Placa = $_POST["Placa"];
        $Tercero = $_POST["Tercero"];
        $respuesta = SIMWebServiceValet::set_solicitar_vehiculo($IDClub, $IDSocio, $Placa, $Tercero);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setsolicitarvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrecibirvehiculo":
        require LIBDIR . "SIMWebServiceValet.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDSocio = $_POST["IDSocio"];
        $Placa = $_POST["Placa"];
        $Cedula = $_POST["Cedula"];
        $Nombre = $_POST["Nombre"];
        $NumeroParqueadero = $_POST["NumeroParqueadero"];
        $IDTipoVehiculo = $_POST["IDTipoVehiculo"];
        $respuesta = SIMWebServiceValet::set_recibir_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $Cedula, $Nombre, $NumeroParqueadero, $IDTipoVehiculo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrecibirvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        /*  case "setentregarvehiculo":
        require LIBDIR . "SIMWebServiceValet.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDSocio = $_POST["IDSocio"];
        $Placa = $_POST["Placa"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $NumeroPago = $_POST["NumeroPago"];
        $respuesta = SIMWebServiceValet::set_entregar_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa,$IDTipoPago, $NumeroPago);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setentregarvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        case "setrecibirvehiculo":
            require LIBDIR . "SIMWebServiceValet.inc.php";
            $IDUsuario = $_POST["IDUsuario"];
            $IDSocio = $_POST["IDSocio"];
            $Placa = $_POST["Placa"];
            $Cedula = $_POST["Cedula"];
            $Nombre = $_POST["Nombre"];
            $NumeroParqueadero = $_POST["NumeroParqueadero"];
            $IDTipoVehiculo = $_POST["IDTipoVehiculo"];
            $respuesta = SIMWebServiceValet::set_recibir_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $Cedula, $Nombre, $NumeroParqueadero, $IDTipoVehiculo);
            //inserta _log
            //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrecibirvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
            SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
            die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
            exit;
            break; */

    case "setentregarvehiculo":
        require LIBDIR . "SIMWebServiceValet.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDSocio = $_POST["IDSocio"];
        $Placa = $_POST["Placa"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $NumeroPago = $_POST["NumeroPago"];
        $respuesta = SIMWebServiceValet::set_entregar_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $IDTipoPago, $NumeroPago);
        //inserta _log
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setentregarvehiculo','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcancelarentregarvehiculo":
        require LIBDIR . "SIMWebServiceValet.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDSocio = $_POST["IDSocio"];
        $Placa = $_POST["Placa"];
        $Tercero = $_POST["Tercero"];
        $respuesta = SIMWebServiceValet::set_cancelar_entregar_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $Tercero);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcancelarentregarvehiculo','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionvalet":
        require LIBDIR . "SIMWebServiceValet.inc.php";
        $respuesta = SIMWebServiceValet::get_configuracion_valet($IDClub);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionofertas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getvehiculoregistradosocio":
        require LIBDIR . "SIMWebServiceValet.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceValet::get_vehiculo_registrado_socio($IDClub, $IDSocio);

        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getvehiculoregistradosocio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getsolicitudentrega":
        require LIBDIR . "SIMWebServiceValet.inc.php";
        $Placa = SIMNet::req("Placa");
        $respuesta = SIMWebServiceValet::get_solicitud_entrega($IDClub, $Placa);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionofertas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionregistrocontacto":
        require LIBDIR . "SIMWebServiceContactos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceContactos::get_configuracion_registro_contacto($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionregistrocontacto','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getverificaaccion":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDNotificacion = $_POST["IDNotificacion"];
        $respuesta = SIMWebServiceUsuarios::get_verifica_accion($IDClub, $IDSocio, $IDUsuario, $IDNotificacion);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getnotificacionlocal','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setregistrocontacto":
        require LIBDIR . "SIMWebServiceContactos.inc.php";
        $IDUsuario = $_POST["IDUsuario"];
        $IDSocio = $_POST["IDSocio"];
        $FechaHora = $_POST["FechaHora"];
        $Lugar = $_POST["Lugar"];
        $Latitud = $_POST["Latitud"];
        $Longitud = $_POST["Longitud"];
        $Contactos = $_POST["Contactos"];
        $CamposFormulario = $_POST["CamposFormulario"];
        $respuesta = SIMWebServiceContactos::set_registro_contacto($IDClub, $IDSocio, $IDUsuario, $FechaHora, $Lugar, $Latitud, $Longitud, $Contactos, $CamposFormulario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setregistrocontacto','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisregistroscontactos":
        require LIBDIR . "SIMWebServiceContactos.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceContactos::get_mis_registros_contactos($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisregistroscontactos','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //FAQS
    case "getconfiguracionfaq":
        require(LIBDIR . "SIMWebServiceFaq.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceFaq::get_configuracion_faq($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getcategoriasfaq":
        require(LIBDIR . "SIMWebServiceFaq.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceFaq::get_categorias_faq($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getpreguntasfaq":
        require(LIBDIR . "SIMWebServiceFaq.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDCategoria = SIMNet::req("IDCategoria");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceFaq::get_preguntas_faq($IDClub, $IDSocio, $IDUsuario, $IDCategoria, $Tag);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionobjetosperdidos','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setcalificarfaq":
        require(LIBDIR . "SIMWebServiceFaq.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDPregunta = SIMNet::req("IDPregunta");
        $ResultoUtil = SIMNet::req("ResultoUtil");
        $respuesta = SIMWebServiceFaq::set_calificar_faq($IDClub, $IDSocio, $IDUsuario, $IDPregunta, $ResultoUtil);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setpreguntafaq":
        require(LIBDIR . "SIMWebServiceFaq.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Correo = SIMNet::req("Correo");
        $Pregunta = SIMNet::req("Pregunta");
        $respuesta = SIMWebServiceFaq::set_pregunta_faq($IDClub, $IDSocio, $IDUsuario, $Correo, $Pregunta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN FAQS


        //AUXILIOS
    case "getconfiguracionauxilios":
        require(LIBDIR . "SIMWebServiceAuxilios.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceAuxilios::get_configuracion_auxilios($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionauxilios','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getauxilios":
        require(LIBDIR . "SIMWebServiceAuxilios.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceAuxilios::get_auxilios($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getauxilios','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gettiporechazoauxilio":
        require(LIBDIR . "SIMWebServiceAuxilios.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDAuxilio = SIMNet::req("IDAuxilio");
        $respuesta = SIMWebServiceAuxilios::get_tiporechazo_auxilio($IDClub, $IDSocio, $IDUsuario, $IDAuxilio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettiporechazoauxilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getauxiliosporaprobar":
        require(LIBDIR . "SIMWebServiceAuxilios.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDAuxilio = SIMNet::req("IDAuxilio");
        $respuesta = SIMWebServiceAuxilios::get_auxilios_por_aprobar($IDClub, $IDSocio, $IDUsuario, $IDAuxilio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettiporechazoauxilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisauxilios":
        require(LIBDIR . "SIMWebServiceAuxilios.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDAuxilio = SIMNet::req("IDAuxilio");
        $respuesta = SIMWebServiceAuxilios::get_mis_auxilios($IDClub, $IDSocio, $IDUsuario, $IDAuxilio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettiporechazoauxilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setauxilio":
        $Archivo = $_POST["Imagen1"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        require(LIBDIR . "SIMWebServiceAuxilios.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDAuxilio = $_POST["IDAuxilio"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceAuxilios::set_auxilio($IDClub, $IDSocio, $IDAuxilio, $Respuestas, $IDUsuario, $Archivo, $_FILES);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setauxilio','".json_encode($_POST).json_encode($_FILES)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestasolicitudauxilio":
        require(LIBDIR . "SIMWebServiceAuxilios.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDSolicitud = $_POST["IDSolicitud"];
        $Aprueba = $_POST["Aprueba"];
        $IDTipoRechazo = $_POST["IDTipoRechazo"];
        $Comentarios = $_POST["Comentarios"];
        $respuesta = SIMWebServiceAuxilios::set_respuesta_solicitud_auxilio($IDClub, $IDSocio, $IDUsuario, $IDSolicitud, $Aprueba, $IDTipoRechazo, $Comentarios);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrespuestasolicitudauxilio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //FIN Auxilios


        //AUXILIOS INFINITOS
    case "getconfiguracionauxiliosinfinito":
        require(LIBDIR . "SIMWebServiceAuxiliosInfinito.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceAuxiliosInfinito::get_configuracion_auxilios_infinito($IDClub, $IDSocio, $IDUsuario, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionauxilios','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getauxiliosinfinito":
        require(LIBDIR . "SIMWebServiceAuxiliosInfinito.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceAuxiliosInfinito::get_auxilios_infinito($IDClub, $IDSocio, $IDUsuario, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getauxilios','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gettiporechazoauxilioinfinito":
        require(LIBDIR . "SIMWebServiceAuxiliosInfinito.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDAuxilio = SIMNet::req("IDAuxilio");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceAuxiliosInfinito::get_tiporechazo_auxilio_infinito($IDClub, $IDSocio, $IDUsuario, $IDAuxilio, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettiporechazoauxilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getauxiliosporaprobarinfinito":
        require(LIBDIR . "SIMWebServiceAuxiliosInfinito.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDAuxilio = SIMNet::req("IDAuxilio");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceAuxiliosInfinito::get_auxilios_por_aprobar_infinito($IDClub, $IDSocio, $IDUsuario, $IDAuxilio, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettiporechazoauxilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisauxiliosinfinito":
        require(LIBDIR . "SIMWebServiceAuxiliosInfinito.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDAuxilio = SIMNet::req("IDAuxilio");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceAuxiliosInfinito::get_mis_auxilios_infinito($IDClub, $IDSocio, $IDUsuario, $IDAuxilio, $IDModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettiporechazoauxilio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setauxilioinfinito":
        $Archivo = $_POST["Imagen1"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        require(LIBDIR . "SIMWebServiceAuxiliosInfinito.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDAuxilio = $_POST["IDAuxilio"];
        $Respuestas = $_POST["Respuestas"];
        $IDModulo = $_POST["IDModulo"];
        $respuesta = SIMWebServiceAuxiliosInfinito::set_auxilio_infinito($IDClub, $IDSocio, $IDAuxilio, $Respuestas, $IDUsuario, $Archivo, $_FILES, $IDModulo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setauxilio','".json_encode($_POST).json_encode($_FILES)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestasolicitudauxilioinfinito":
        require(LIBDIR . "SIMWebServiceAuxiliosInfinito.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDSolicitud = $_POST["IDSolicitud"];
        $Aprueba = $_POST["Aprueba"];
        $IDTipoRechazo = $_POST["IDTipoRechazo"];
        $Comentarios = $_POST["Comentarios"];
        $IDModulo = $_POST["IDModulo"];
        $respuesta = SIMWebServiceAuxiliosInfinito::set_respuesta_solicitud_auxilio_infinito($IDClub, $IDSocio, $IDUsuario, $IDSolicitud, $Aprueba, $IDTipoRechazo, $Comentarios, $IDModulo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrespuestasolicitudauxilio','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //FIN Auxilios INFINITOS

    case "getconfiguracionmovilidad":
        require(LIBDIR . "SIMWebServiceEncuestaArbol.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceEncuestaArbol::get_configuracion_movilidad($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnostico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getmovilidad":
        require(LIBDIR . "SIMWebServiceEncuestaArbol.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceEncuestaArbol::get_movilidad($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnostico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getmovilidadhistorial":
        require(LIBDIR . "SIMWebServiceEncuestaArbol.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Fecha = SIMNet::req("Fecha");
        if (!empty(SIMNet::req("IDMovilidad"))) {
            $IDEncuestaArbol = SIMNet::req("IDMovilidad");
        }
        $respuesta = SIMWebServiceEncuestaArbol::get_movilidad_historial($IDClub, $IDSocio, $IDUsuario, $IDEncuestaArbol, $Fecha);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdiagnostico','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestamovilidad":
        require(LIBDIR . "SIMWebServiceEncuestaArbol.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDMovilidad = $_POST["IDMovilidad"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $Nombre = $_POST["Nombre"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceEncuestaArbol::set_respuesta_movilidad($IDClub, $IDSocio, $IDMovilidad, $Respuestas, $IDUsuario, $NumeroDocumento, $Nombre);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setrespuestamovilidad','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



    case "setqr":
        require(LIBDIR . "SIMWebServiceQR.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Codigo = $_POST["Codigo"];
        $respuesta = SIMWebServiceQR::set_qr($IDClub, $IDSocio, $IDUsuario, $Codigo);
        //inserta _log
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setqr','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Reconocimientos
    case "getconfiguracionreconocimiento":
        require(LIBDIR . "SIMWebServiceReconocimiento.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceReconocimiento::get_configuracion_reconocimiento($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionreconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcategoriareconocimiento":
        require(LIBDIR . "SIMWebServiceReconocimiento.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSubModulo = SIMNet::req("IDSubModulo");
        $respuesta = SIMWebServiceReconocimiento::get_categoria_reconocimiento($IDClub, $IDSocio, $IDUsuario, $IDSubModulo);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcategoriareconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getgruporeconocimiento":
        require(LIBDIR . "SIMWebServiceReconocimiento.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceReconocimiento::get_grupo_reconocimiento($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcategoriareconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getgruposreconocimiento":
        require(LIBDIR . "SIMWebServiceReconocimiento.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceReconocimiento::get_grupos_reconocimiento($IDClub, $IDSocio, $IDUsuario, $Tag);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcategoriareconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getfiltrocategoriareconocimiento":
        require(LIBDIR . "SIMWebServiceReconocimiento.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");

        $respuesta = SIMWebServiceReconocimiento::get_filtro_categoria_reconocimiento($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getcategoriareconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getreconocimientoenviado":
        require(LIBDIR . "SIMWebServiceReconocimiento.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceReconocimiento::get_reconocimiento_enviado($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicio (Servicio, Parametros, Respuesta) Values ('getcategoriareconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setreconocimiento":
        require(LIBDIR . "SIMWebServiceReconocimiento.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDCategoriaReconocimiento = $_POST["IDCategoriaReconocimiento"];
        $IDSocioReconocido = $_POST["IDSocioReconocido"];
        $IDGrupoReconocimiento = $_POST["IDGrupoReconocimiento"];
        $GrupoReconocimiento = $_POST["GrupoReconocimiento"];
        $Comentario = $_POST["Comentario"];
        $Opciones = $_POST["Opciones"];
        $respuesta = SIMWebServiceReconocimiento::set_reconocimiento($IDClub, $IDSocio, $IDUsuario, $IDCategoriaReconocimiento, $IDSocioReconocido, $IDGrupoReconocimiento, $Comentario, $Opciones, $GrupoReconocimiento);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setreconocimiento','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN Reconocimientos

    case "getconfiguracionpasalapagina":
        require(LIBDIR . "SIMWebServicePasaPagina.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServicePasaPagina::get_configuracion_pasalapagina($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionpago":
        require(LIBDIR . "SIMWebServicePago.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServicePago::get_configuracion_pago($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionpago','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settransaccionpago":
        require(LIBDIR . "SIMWebServicePago.inc.php");
        $IDModulo = $_POST["IDModulo"];
        $PurchaseCode = $_POST["PurchaseCode"];
        $IDObjeto = $_POST["IDObjecto"];
        $Aprobada = $_POST["Aprobada"];
        $ResultadoTranssacion = $_POST["ResultadoTranssacion"];
        $respuesta = SIMWebServicePago::set_transaccion_pago($IDClub, $IDModulo, $PurchaseCode, $IDObjeto, $Aprobada, $ResultadoTranssacion);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('settransaccionpago','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //POSTULADOS
    case "getconfiguracionpostulados":
        require(LIBDIR . "SIMPostulados.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMPostulados::get_configuracion_postulados($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionpostulados','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getpostulados":
        require(LIBDIR . "SIMPostulados.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMPostulados::get_postulados($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionpostulados','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcomentariopostulado":
        require(LIBDIR . "SIMPostulados.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDPostulado = $_POST["IDPostulado"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMPostulados::set_comentario_postulado($IDClub, $IDPostulado, $Comentario, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('set_comentario_postulado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        // FIN POSTULADOS

        //Laboral
    case "getconfiguracionlaboral":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceLaboral::get_configuracion_laboral($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionreconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getfiltrosolicitudeslaboralesporaprobar":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceLaboral::get_filtro_solicitudes_laborales_por_aprobar($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionreconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setsolicitudlaboralvacaciones":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDSolicitud = $_POST["IDSolicitud"];
        $Aprueba = $_POST["Aprueba"];
        $Comentarios = $_POST["Comentarios"];
        $Modulo = $_POST["Modulo"];
        $respuesta = SIMWebServiceLaboral::set_solicitud_laboral_vacaciones($IDClub, $IDSocio, $IDUsuario, $IDSolicitud, $Aprueba, $Comentarios, $Modulo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setsolicitudlaboralvacaciones','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getsolicitudeslaboralesporaprobar":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDFiltro = SIMNet::req("IDFiltro");
        $respuesta = SIMWebServiceLaboral::get_solicitudes_laborales_por_aprobar($IDClub, $IDSocio, $IDUsuario, $IDFiltro);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionreconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setlaboralpermiso":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDMotivo = $_POST["IDMotivo"];
        $FechaHoraInicio = $_POST["FechaHoraInicio"];
        $FechaHoraFin = $_POST["FechaHoraFin"];
        $DiasHabiles = $_POST["DiasHabiles"];
        $Remunerado = $_POST["Remunerado"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMWebServiceLaboral::set_laboral_permiso($IDClub, $IDSocio, $IDUsuario, $IDMotivo, $FechaHoraInicio, $FechaHoraFin, $DiasHabiles, $Remunerado, $Comentario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setlaboralpermiso','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setlaboralvacaciones":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $Dias = $_POST["Dias"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMWebServiceLaboral::set_laboral_vacaciones($IDClub, $IDSocio, $IDUsuario, $FechaInicio, $FechaFin, $Dias, $Comentario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setlaboralvacaciones','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setlaboralcompensatorio":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $FechaInicio = $_POST["FechaInicio"];
        $FechaFin = $_POST["FechaFin"];
        $Dias = $_POST["Dias"];
        $Comentario = $_POST["Comentario"];

        $Archivo = $_POST["Archivo"];
        if (count($_FILES) > 0)
            $_POST = array_map('utf8_encode', $_POST);

        SIMWebServiceLaboral::set_laboral_compensatorio($IDClub, $IDSocio, $IDUsuario, $FechaInicio, $FechaFin, $Dias, $Comentario,  $_FILES);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setlaboralcompensatorio','".json_encode($_FILES)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setlaboralcertificado":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDTipoCertificado = $_POST["IDTipoCertificado"];
        $Fechas = $_POST["Fechas"];
        $ANombreDe = $_POST["ANombreDe"];
        $Comentario = $_POST["Comentario"];
        $respuesta = SIMWebServiceLaboral::set_laboral_certificado($IDClub, $IDSocio, $IDUsuario, $IDTipoCertificado, $Fechas, $ANombreDe, $Comentario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setlaboralcertificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmissolicitudeslaborales":
        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceLaboral::get_mis_solicitudes_laborales($IDClub, $IDSocio, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getlaboralcalculafechafin":

        require(LIBDIR . "SIMWebServiceLaboral.inc.php");
        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $FechaInicial = SIMNet::req("FechaInicial");
        $Dias = SIMNet::req("Dias");
        $DiasDinero = SIMNet::req("DiasDinero");
        $DiasNormales = SIMNet::req("DiasNormales");
        $respuesta = SIMWebServiceLaboral::get_laboral_calcula_fechafin($IDClub, $FechaInicial, $Dias, $DiasDinero, $DiasNormales, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getlaboralcalculafechafin','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //Fin Laboral

        //Vacunacion
    case "getconfiguracionvacunacion":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVacunacion::get_configuracion_vacunacion($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionreconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionvacunacionv2":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVacunacion::get_configuracion_vacunacionv2($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionreconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getvacunas":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $respuesta = SIMWebServiceVacunacion::get_vacunas($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getvacunasv2":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $respuesta = SIMWebServiceVacunacion::get_vacunas($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getinformacionvacunacion":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVacunacion::get_informacion_vacunacion($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionreconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getinformacionvacunacionv2":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceVacunacion::get_informacion_vacunacionv2($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionreconocimiento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setvacunacion":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $Archivo = $_POST["Archivo"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDVacuna = $_POST["IDVacuna"];
        $Lugar = $_POST["Lugar"];
        $Dosis = $_POST["Dosis"];
        $Entidad = $_POST["Entidad"];
        $IDEntidad = $_POST["IDEntidad"];
        $Fecha = $_POST["Fecha"];
        $Foto = $_POST["Foto"];
        $Campos = $_POST["CamposDosis"];

        $respuesta = SIMWebServiceVacunacion::set_vacunacion($IDClub, $IDSocio, $IDUsuario, $IDVacuna, $Lugar, $Dosis, $Entidad, $Fecha, $Foto, $_FILES, $IDEntidad, $Campos);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio,Parametros, Respuesta) Values ('setvacunacion','".$IDSocio."','".json_encode($_POST).json_encode($_FILES)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setvacunacionv2":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $Archivo = $_POST["Archivo"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDSocioBeneficiario = $_POST["IDSocioBeneficiario"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDVacuna = $_POST["IDVacuna"];
        $Lugar = $_POST["Lugar"];
        $Dosis = $_POST["Dosis"];
        $Entidad = $_POST["Entidad"];
        $IDEntidad = $_POST["IDEntidad"];
        $Fecha = $_POST["Fecha"];
        $Foto = $_POST["Foto"];
        $Campos = $_POST["CamposDosis"];
        $IDDosis = $_POST[IDDosis];

        $respuesta = SIMWebServiceVacunacion::set_vacunacionv2($IDClub, $IDSocio, $IDUsuario, $IDVacuna, $Lugar, $Dosis, $Entidad, $Fecha, $Foto, $_FILES, $IDEntidad, $Campos, $IDDosis, $IDSocioBeneficiario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio,Parametros, Respuesta) Values ('setvacunacionv2','".$IDSocio."','".json_encode($_POST).json_encode($_FILES)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcarnegobierno":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $Archivo = $_POST["Archivo"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDSocioBeneficiario = $_POST["IDSocioBeneficiario"];
        $URLQR = $_POST["URLQR"];
        $Foto = $_POST["Foto"];
        $respuesta = SIMWebServiceVacunacion::set_carne_gobierno($IDClub, $IDSocio, $IDUsuario, $URLQR, $Foto, $_FILES, $IDSocioBeneficiario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio,Parametros, Respuesta) Values ('setcarnegobierno','".$IDSocio."','".json_encode($_FILES)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcitavacunacion":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Dosis = $_POST["Dosis"];
        $Entidad = $_POST["Entidad"];
        $IDEntidad = $_POST["IDEntidad"];
        $Fecha = $_POST["Fecha"];
        $respuesta = SIMWebServiceVacunacion::set_cita_vacunacion($IDClub, $IDSocio, $IDUsuario, $Dosis, $Entidad, $Fecha, $IDEntidad);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio, Parametros, Respuesta) Values ('setcitavacunacion','".$IDSocio."','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcitavacunacionv2":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Dosis = $_POST["Dosis"];
        $Entidad = $_POST["Entidad"];
        $IDEntidad = $_POST["IDEntidad"];
        $Fecha = $_POST["Fecha"];
        $IDDosis = $_POST[IDDosis];
        $respuesta = SIMWebServiceVacunacion::set_cita_vacunacionv2($IDClub, $IDSocio, $IDUsuario, $Dosis, $Entidad, $Fecha, $IDEntidad, $IDDosis);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio, Parametros, Respuesta) Values ('setcitavacunacionv2','" . $IDSocio . "','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setvacunado":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Vacunado = $_POST["Vacunado"];
        $respuesta = SIMWebServiceVacunacion::set_vacunado($IDClub, $IDSocio, $IDUsuario, $Vacunado);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio, Parametros, Respuesta) Values ('setvacunado','".$IDSocio."','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setvacunadov2":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Vacunado = $_POST["Vacunado"];
        $respuesta = SIMWebServiceVacunacion::set_vacunadov2($IDClub, $IDSocio, $IDUsuario, $Vacunado);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio, Parametros, Respuesta) Values ('setvacunadov2','".$IDSocio."','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "eliminartodasdosisvacunacionv2":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];

        $respuesta = SIMWebServiceVacunacion::eliminar_todas_dosis_vacunacionv2($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio, Parametros, Respuesta) Values ('setvacunadov2','" . $IDSocio . "','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "editavacunacionv2":
        require(LIBDIR . "SIMWebServiceVacunacion.inc.php");
        $Archivo = $_POST["Archivo"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST["IDSocio"];
        $IDSocioBeneficiario = $_POST["IDSocioBeneficiario"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDVacuna = $_POST["IDVacuna"];
        $Lugar = $_POST["Lugar"];
        $Dosis = $_POST["Dosis"];
        $Entidad = $_POST["Entidad"];
        $IDEntidad = $_POST["IDEntidad"];
        $Fecha = $_POST["Fecha"];
        $Foto = $_POST["Foto"];
        $Campos = $_POST["CamposDosis"];
        $IDDosis = $_POST[IDDosis];

        $respuesta = SIMWebServiceVacunacion::set_vacunacionv2($IDClub, $IDSocio, $IDUsuario, $IDVacuna, $Lugar, $Dosis, $Entidad, $Fecha, $Foto, $_FILES, $IDEntidad, $Campos, $IDDosis, $IDSocioBeneficiario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio,Parametros, Respuesta) Values ('setvacunacionv2','" . $IDSocio . "','" . json_encode($_POST) . json_encode($_FILES) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Fin Vacunacion


        //Talegas
    case "getconfiguraciontalegas":
        require(LIBDIR . "SIMWebServiceTalegas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceTalegas::get_configuracion_talegas($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciontalegas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getlistalugarestalega":
        require(LIBDIR . "SIMWebServiceTalegas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceTalegas::get_listalugares_talega($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciontalegas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gettalegas":
        require(LIBDIR . "SIMWebServiceTalegas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDBeneficiario = SIMNet::req("IDBeneficiario");
        $respuesta = SIMWebServiceTalegas::get_talegas($IDClub, $IDSocio, $IDUsuario, $IDBeneficiario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciontalegas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settalega":
        require(LIBDIR . "SIMWebServiceTalegas.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDTalega = $_POST["IDTalega"];
        $IDLugarTalega = $_POST["IDLugarTalega"];
        $Fecha = $_POST["Fecha"];
        $ObjetosInventario = $_POST["ObjetosInventario"];
        $CamposTalega = $_POST["CamposTalega"];

        $respuesta = SIMWebServiceTalegas::set_talega($IDClub, $IDSocio, $IDUsuario, $IDTalega, $IDLugarTalega, $Fecha, $ObjetosInventario, $CamposTalega, $_FILES);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio, Parametros, Respuesta) Values ('settalega','" . $IDSocio . "','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmistalegashistorial":
        require(LIBDIR . "SIMWebServiceTalegas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Fecha = SIMNet::req("Fecha");
        $respuesta = SIMWebServiceTalegas::get_mis_talegas_historial($IDClub, $IDSocio, $IDUsuario, $Fecha);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciontalegas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcancelartalega":
        require(LIBDIR . "SIMWebServiceTalegas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDTalega = SIMNet::req("IDTalega");
        $respuesta = SIMWebServiceTalegas::set_cancelar_talega($IDClub, $IDSocio, $IDUsuario, $IDTalega);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ($IDSocio,'setcancelartalega','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Fin Talegas


        //Domiciliarios

    case "getconfiguraciondomiciliarios":
        require(LIBDIR . "SIMWebServiceDomiciliario.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        if (empty($IDSocio)) :
            $IDSocio = SIMNet::req("IDUsuario");
        endif;
        $respuesta = SIMWebServiceDomiciliario::get_configuracion_domiciliarios($IDClub, $IDSocio);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
    case "setdomiciliario":
        require(LIBDIR . "SIMWebServiceDomiciliario.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Empresa = $_POST["Empresa"];
        $Fecha = $_POST["Fecha"];
        $Hora = $_POST["Hora"];
        $NombreDomiciliario = $_POST["NombreDomiciliario"];
        $DocumentoDomiciliario = $_POST["DocumentoDomiciliario"];
        $IDDomicilio = $_POST["IDDomicilio"];
        $respuesta = SIMWebServiceDomiciliario::set_domiciliario($IDClub, $IDSocio, $IDUsuario, $Empresa, $Fecha, $Hora, $NombreDomiciliario, $DocumentoDomiciliario, $IDDomicilio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setdomiciliario','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getHistorialDomiciliarios":
        require(LIBDIR . "SIMWebServiceDomiciliario.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDomiciliario::get_Historial_Domiciliarios($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getHistorialDomiciliarios','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getDomiciliariosPendientes":
        require(LIBDIR . "SIMWebServiceDomiciliario.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceDomiciliario::get_Domiciliarios_Pendientes($IDClub, $IDSocio);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getDomiciliariosPendientes','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getDomiciliariosBuscador":
        require(LIBDIR . "SIMWebServiceDomiciliario.inc.php");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Tag = SIMNet::req("Tag");

        $respuesta = SIMWebServiceDomiciliario::get_Domiciliarios_Buscador($IDClub, $IDUsuario, $Tag);
        //inserta _log
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('586828','getDomiciliariosBuscador','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //Fin Domiciliarios

    case "setinformacionpanico":
        require(LIBDIR . "SIMWebServicePanico.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Latitud = $_POST["Latitud"];
        $Longitud = $_POST["Longitud"];
        $respuesta = SIMWebServicePanico::set_informacion_panico($IDClub, $IDSocio, $IDUsuario, $Latitud, $Longitud);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setpreguntaclasificado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getlabels":
        $respuesta = SIMWebServiceApp::get_labels($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setemaileliminarcuenta":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $Correo = $_POST[Correo];
        $respuesta = SIMWebServiceUsuarios::set_email_eliminar_cuenta($IDClub, $IDSocio, $IDUsuario, $Correo);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "setcodigoeliminarcuenta":
        require LIBDIR  . "SIMWebServiceUsuarios.inc.php";
        $Codigo = $_POST[Codigo];
        $respuesta = SIMWebServiceUsuarios::set_codigo_eliminar_cuenta($IDClub, $IDSocio, $IDUsuario, $Codigo);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;


        //Recuperar clave de socio y enviarla al email
    case "getrecuperarclave":
        require LIBDIR . "SIMWebServiceClubColombia.inc.php";
        require LIBDIR . "SIMWebServiceCountryMedellin.inc.php";
        $email = SIMNet::req("email");
        $accion = SIMNet::req("accion");
        $Documento = SIMNet::req("Documento");


        if (!empty($Documento) and $IDClub == 227) :

            $message = SIMWebServiceCountryMedellin::App_RecuperarClave($Documento);
            $respuesta1 = json_decode($message);
            $respuesta = array();
            if ($respuesta1->estado == "true") {

                $respuesta["message"] = "Se ha enviado la contraseña al correo electrónico";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } else {

                $respuesta["message"] =  "No se encontraron registros con el código";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
            exit;
            break;


        endif;

        //verificar si viene Email
        if (!empty($email)) {
            //Condado con accion
            if ($IDClub == 51) {
                if ($accion == "") {
                    $message = "Codigo de socio o membresia es requerido";
                    die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                    exit;
                } else {
                    $condicion_accion = " and Accion = '" . SIMNet::req("accion") . "' ";
                }
            }

            if (SIMNet::req("VerificaAccion") == "1") : //En rancho se pide la accion para poder cambiarla cuando es por web
                $condicion_accion = " and Accion = '" . SIMNet::req("Accion") . "' ";
            endif;

            if ($IDClub == 18000) : //En fontanar se envia a otra persona ya que mi club no maneja las claves de los usuarios

                $message = "Su solicitud fue enviada correctamente, pronto nos pondremos en contacto";
                //Enviamos el correo al usario de conectar
                $dest = trim("asistentehfr@sicre.co");

                $head  = "From: " . "info@miclubapp.com" . "\r\n";
                $head .= "To: " . $dest . " \r\n";

                // Ahora creamos el cuerpo del mensaje
                $msg  = "Mensaje desde la Aplicación de Hacienda Fontanar \n\n";
                $msg .= "Cordial Saludo, \n\n El usuario con el siguiente correo ha solicitado sea recordada su clave: \n Email: " . $email . "\n \n Notificaciones automaticas Mi Club.";

                // Finalmente enviamos el mensaje
                mail($dest, "Recordar Clave Fontanar", $msg, $head);
                die(json_encode(array('success' => false, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                exit;
            elseif ($IDClub == 25) : // Gun club la solicitude llega al club y ellos gestionan el cambio de clave
                $message = "Su solicitud fue enviada correctamente, pronto nos pondremos en contacto para reestablecer su clave";
                //Enviamos el correo al usario de conectar
                $dest = trim("comunicaciones@gunclub.com.co,c.comunicaciones@gunclub.com.co,auxiliarayb@gunclub.com.co");

                $head  = "From: " . "info@miclubapp.com" . "\r\n";
                $head .= "To: " . $dest . " \r\n";

                // Ahora creamos el cuerpo del mensaje
                $msg  = "Mensaje desde app Gun Club \n\n";
                $msg .= "Cordial Saludo, \n\n El usuario con el siguiente correo ha solicitado sea recordada su clave: \n Email: " . $email . "\n \n Notificaciones automaticas Mi Club.";

                // Finalmente enviamos el mensaje
                mail($dest, "Recordar Clave Gun Club", $msg, $head);
                die(json_encode(array('success' => false, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                exit;
            elseif ($IDClub == 38000) : // Club Colombia la clave se maneja en la base de ellos por lo tanto se envia por WS

                $sql_verifica = "SELECT * FROM Socio WHERE CorreoElectronico = '" . $email . "' and IDClub = '" . $IDClub . "'" . $condicion_accion;
                $qry_verifica = $dbo->query($sql_verifica);
                if ($dbo->rows($qry_verifica) == 0) {
                    $message = "El Correo no esta registrado en la base de datos del club, por favor verificar";
                } //end if
                else {
                    $datos_socio = $dbo->fetchArray($qry_verifica);

                    $nueva_clave = substr($datos_socio[Nombre], 0, 3) . rand(1, 20000) . substr($datos_socio[Apellido], 2, 2);
                    $nueva_clave = strtoupper($nueva_clave);

                    //Enviamos el correo al usario de conectar
                    $dest = trim($email);

                    $head  = "From: " . "info@miclubapp.com" . "\r\n";
                    $head .= "To: " . $dest . " \r\n";

                    // Ahora creamos el cuerpo del mensaje
                    $msg  = "Mensaje desde la App de Clubes \n\n";
                    $msg .= "Cordial Saludo, \n\n Le recordamos que los datos de acceso al sistema de clubes es: \n Usuario: " . $datos_socio["Email"] . "\n Clave: " . $nueva_clave . "\n\n Notificaciones automaticas Clubes.";

                    // Finalmente enviamos el mensaje
                    mail($dest, "Recordar Clave Club Colombia", $msg, $head);
                    //mail( "sistemas@clubcolombia.org", "Recordar Clave Club Colombia", $msg, $head );


                    $token_socio = SIMWebServiceClubColombia::obtener_token_colombia("1107051301", "24281107");
                    if (empty($token_socio)) :
                        $message = "No fue posible conectarse al servidor, por favor intente mas tarde";
                        die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                        exit;
                    else :
                        $resultado = SIMWebServiceClubColombia::set_recordar_clave_colombia($token_socio, $email, $nueva_clave);
                    endif;


                    //actualizo clave
                    $sql_update_clave = $dbo->query("Update Socio Set Clave =  sha1('" . $nueva_clave . "') Where CorreoElectronico = '" . $email . "' and IDClub = '" . $IDClub . "'");
                    $message = "Clave enviada correctamente";
                    die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                    exit;
                }
            elseif ($IDClub == 227) : // Contry club medellin la clave se maneja en la base de ellos por lo tanto se envia por WS 
                $sql_verifica = "SELECT * FROM Socio WHERE Email = '" . $email . "' and IDClub = '" . $IDClub . "'";
                $qry_verifica = $dbo->query($sql_verifica);
                if ($dbo->rows($qry_verifica) == 0) {

                    $message = "El Correo no esta registrado en la base de datos del club, por favor verificar";
                } //end if
                else {
                    $datos_socio = $dbo->fetchArray($qry_verifica);
                    $email = $datos_socio[CorreoElectronico];
                    $id = $datos_socio[IDSocio];
                    $codigo = $datos_socio[NumeroDerecho];
                    $cedula = $datos_socio[NumeroDocumento];

                    require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

                    $resultado = SIMWebServiceCountryMedellin::App_RecuperarClave($email);
                    $data = json_decode($resultado);
                    if ($data->estado == "false") :

                        $message = "No fue posible conectarse al servidor, por favor intente mas tarde";
                        die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                        exit;
                    else :


                        $restante = substr("$cedula", -3);
                        $nuevaclave = $codigo . $restante;

                        $sql_verifica = "UPDATE Socio SET Clave=sha1('" . $nuevaclave . "') WHERE IDSocio = '$id' and IDClub = '" . $IDClub . "'";
                        $qry_verifica = $dbo->query($sql_verifica);

                        $message = "Clave enviada correctamente";
                        die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                        exit;
                    endif;
                }
            elseif ($IDClub == 44) :
                $message = "Para cambiar usuario y contraseña por favor diríjase a https://www.countryclubdebogota.com/members";

            elseif ($IDClub == 93) :
                $sql_verifica = "SELECT * FROM Socio WHERE CorreoElectronico = '" . $email . "' and IDClub = '" . $IDClub . "'" . $condicion_accion;
                $qry_verifica = $dbo->query($sql_verifica);
                if ($dbo->rows($qry_verifica) == 0) {
                    $message = "No encontrado";
                } //end if
                else {
                    $datos_socio = $dbo->fetchArray($qry_verifica);

                    $nueva_clave = substr($datos_socio[Nombre], 0, 3) . rand(1, 20000) . substr($datos_socio[Apellido], 2, 2);
                    $nueva_clave = strtoupper($nueva_clave);

                    //Enviamos el correo al usario de conectar
                    $dest = trim($email);

                    $head  = "From: " . "info@miclubapp.com" . "\r\n";
                    $head .= "To: " . $dest . " \r\n";
                    $head .= "Content-Type: text/plain;charset=utf-8";

                    // Ahora creamos el cuerpo del mensaje
                    $msg  = "Mensaje desde la Aplicación de Clubes \n\n";
                    $msg .= "Cordial Saludo, \n\n Las credenciales de acceso para la App del Club son: \n Usuario: " . $datos_socio["Email"] . "\n Clave: " . $nueva_clave . "\n\n
						Esta es una notificación automática por haber solicitado desde la aplicación móvil la opción recordar clave.";

                    // Finalmente enviamos el mensaje
                    mail($dest, "Contacto desde la aplicación de clubes - Salinas Yacht Club", $msg, $head);

                    //actualizo clave
                    $sql_update_clave = $dbo->query("Update Socio Set Clave =  sha1('" . $nueva_clave . "') Where CorreoElectronico = '" . $email . "' and IDClub = '" . $IDClub . "'");
                    $message = "Clave enviada correctamente.";
                    die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                    exit;
                }
            elseif ($IDClub == 185) :
                $message = "Si has olvidado tu Usuario o no sabes cual es, comunicate con el asistente de la App al 097 050 133";
            else :

                $sql_hijos = " Select IDClub From Club Where IDClubPadre = '" . $IDClub . "' ";
                $result_hijos = $dbo->query($sql_hijos);
                while ($r_hijos = $dbo->fetchArray($result_hijos)) :
                    $array_id_hijos[] = $r_hijos["IDClub"];
                endwhile;
                if (count($array_id_hijos) > 0) :
                    $id_club_consulta = implode(",", $array_id_hijos);
                else :
                    $id_club_consulta = $IDClub;
                endif;

                if (!empty($Documento)) :
                    $busqueda = "AND NumeroDocumento = '$Documento'";
                endif;


                $sql_verifica = "SELECT * FROM Socio WHERE CorreoElectronico = '$email'  AND IDClub in (" . $id_club_consulta . ")  $busqueda " . $condicion_accion;
                $qry_verifica = $dbo->query($sql_verifica);
                if ($dbo->rows($qry_verifica) == 0) {


                    // Cambiar contraseña Usuario
                    $sql_verifica_usuario = "SELECT * FROM Usuario WHERE Email = '$email'  AND IDClub in (" . $id_club_consulta . ")  $busqueda " . $condicion_accion;
                    $qry_verifica_usuario = $dbo->query($sql_verifica_usuario);
                    if ($dbo->rows($qry_verifica_usuario) == 0) {
                        $message = "El Correo no esta registrado en la base de datos del club, por favor verificar";

                        if ($IDClub == 155) :
                            $message = "Si el correo esta registrado recibirá la información de la contraseña";
                        endif;
                    } else {
                        $datos_socio = $dbo->fetchArray($qry_verifica_usuario);

                        $nueva_clave =  SIMUtil::generarPassword(10);

                        //Enviamos el correo al usario de conectar
                        $dest = trim($email);

                        $head  = "From: " . "info@miclubapp.com" . "\r\n";
                        $head .= "To: " . $dest . " \r\n";
                        $head .= "Content-Type: text/html; charset=UTF-8";

                        // Ahora creamos el cuerpo del mensaje
                        $msg  = "Mensaje desde la Aplicación de Clubes \n\n";
                        $msg .= "Cordial Saludo, \n\n Las credenciales de acceso para la App del Club son: \n Usuario: " . $datos_socio["User"] . "\n Clave: " . $nueva_clave . "\n\n
							Esta es una notificación automática por haber solicitado desde la aplicación móvil la opción recordar clave.";

                        // Finalmente enviamos el mensaje
                        mail($dest, "Contacto desde la Aplicación de Clubes", $msg, $head);



                        //actualizo clave
                        $sql_update_clave = $dbo->query("Update Usuario Set Password =  sha1('" . $nueva_clave . "') Where Email = '" . $email . "' and IDClub = '" . $IDClub . "'");
                        //$sql_update_clave = "Update Usuario Set Password =  sha1('" . $nueva_clave . "') Where Email = '" . $email . "' and IDClub = '" . $IDClub . "'";

                        $message = "Clave enviada correctamente.";
                        die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));


                        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getrecuperarclave','" . json_encode($_GET) . "','" . $sql . "')");

                        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );

                        exit;
                    }
                } //end if
                else {
                    $datos_club = $dbo->fetchAll("Club ", " IDClub  = '" . $IDClub . "' ", "array");
                    $datos_socio = $dbo->fetchArray($qry_verifica);

                    $nueva_clave =  SIMUtil::generarPassword(10);

                    //Enviamos el correo al usario de conectar
                    $dest = trim($email);

                    if (empty($datos_club["CorreoRemitente"])) {
                        $FromCorreo = "info@miclubapp.com";
                    } else {
                        $FromCorreo = $datos_club["CorreoRemitente"];
                    }


                    $head  = "From: " . $FromCorreo . "\r\n";
                    $head .= "To: " . $dest . " \r\n";
                    $head .= "Content-Type: text/html; charset=UTF-8";

                    // Ahora creamos el cuerpo del mensaje
                    $msg  = "Mensaje desde la App \n\n";
                    $msg .= "Cordial Saludo, \n\n Las credenciales de acceso para la App del Club son: \n Usuario: " . $datos_socio["Email"] . "\n Clave: " . $nueva_clave . "\n\n
						Esta es una notificación automática por haber solicitado desde la aplicación móvil la opción recordar clave.";

                    // Finalmente enviamos el mensaje
                    //mail($dest, "Contacto desde la Aplicación de Clubes", $msg, $head);
                    SIMUtil::send_mail($IDClub, $dest, "Solicitud de restablecimiento de clave", $msg);

                    //actualizo clave
                    $sql_update_clave = $dbo->query("Update Socio Set Clave =  sha1('" . $nueva_clave . "') Where CorreoElectronico = '" . $email . "' and IDClub = '" . $IDClub . "'");
                    $message = "Clave enviada correctamente.";
                    die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));

                    // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );

                    exit;
                }
            endif;
        } //end if
        else {
            $message = "Error faltan parametros";
            die(json_encode(array('success' => false, 'message' => $message, 'response' => $response, 'update' => $update, 'date' => $nowserver)));
        }

        die(json_encode(array('success' => false, 'message' => $message, 'response' => $response, 'update' => $update, 'date' => $nowserver)));
        break;

        //Recuperar clave de socio y enviarla al email
    case "getrecuperarclaveempleado":
        $email = SIMNet::req("email");
        //verificar si viene Email
        if (!empty($email)) {
            $sql_verifica = "SELECT * FROM Usuario WHERE Email = '" . $email . "' and IDClub = '" . $IDClub . "'";
            $qry_verifica = $dbo->query($sql_verifica);
            if ($dbo->rows($qry_verifica) == 0) {
                $message = "No encontrado";
            } //end if
            else {
                $datos_Usuario = $dbo->fetchArray($qry_verifica);

                $nueva_clave = substr($datos_Usuario[Nombre], 0, 3) . rand(1, 20000) . substr($datos_Usuario[Apellido], 2, 2);
                $nueva_clave = strtoupper($nueva_clave);

                //Enviamos el correo al usario de conectar
                $dest = trim($email);

                $head  = "From: " . "info@22cero2.com" . "\r\n";
                $head .= "To: " . $dest . " \r\n";

                // Ahora creamos el cuerpo del mensaje
                $msg  = "Mensaje desde la Aplicación de Clubes \n\n";
                $msg .= "Cordial Saludo, \n\n Le recordamos que los datos de acceso al sistema de clubes es: \n Usuario: " . $email . "\n Clave: " . $nueva_clave . "\n\n Notificaciones automaticas Clubes.";

                // Finalmente enviamos el mensaje
                mail($dest, "Contacto desde la Aplicación de Clubes", $msg, $head);

                //actualizo clave
                $sql_update_clave = $dbo->query("Update Usuario Set Password =  sha1('" . $nueva_clave . "') Where Email = '" . $email . "' and IDClub = '" . $IDClub . "'");

                $message = "Clave enviada correctamente";

                die(json_encode(array('success' => true, 'message' => $message, 'response' => $response, 'date' => $nowserver)));
                exit;
            }
        } //end if
        else {
            $message = "Error faltan parametros";
            die(json_encode(array('success' => false, 'message' => $message, 'response' => $response, 'update' => $update, 'date' => $nowserver)));
        }

        die(json_encode(array('success' => false, 'message' => $message, 'response' => $response, 'update' => $update, 'date' => $nowserver)));
        break;



        // SERVICIO DE TALONERAS 
        // AGREGADOS POR Juan Diego Villa Montoya
        // Fecha: 2021-11-10

    case "gettalonerasrevicios": //ESTA MAL ESCRITO, ASI DEBE SER
        require LIBDIR . "SIMWebServiceTaloneras.inc.php";

        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");

        $respuesta = SIMWebServiceTaloneras::get_taloneras_servicio($IDClub, $IDSocio, $IDUsuario);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','gettalonerasservicios','".json_encode($_GET)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguraciontaloneras":
        require LIBDIR . "SIMWebServiceTaloneras.inc.php";

        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");

        $respuesta = SIMWebServiceTaloneras::get_configuracion_taloneras($IDClub, $IDSocio, $IDUsuario);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getconfiguraciontaloneras','".json_encode($_GET)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settalonera":
        require LIBDIR . "SIMWebServiceTaloneras.inc.php";

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDServicio = $_POST["IDServicio"];
        $IDTalonera = $_POST["IDTalonera"];
        $Asignados = $_POST["Asignados"];

        $respuesta = SIMWebServiceTaloneras::set_taloneras($IDClub, $IDSocio, $IDUsuario, $IDServicio, $IDTalonera, $Asignados);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','settalonera','".json_encode($_POST)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getestadocuentataloneras":
        require LIBDIR . "SIMWebServiceTaloneras.inc.php";

        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");

        $respuesta = SIMWebServiceTaloneras::get_estado_cuenta_taloneras($IDClub, $IDSocio, $IDUsuario);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getestadocuentataloneras','".json_encode($_POST)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settipopagotalonera":
        require LIBDIR . "SIMWebServiceTaloneras.inc.php";

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDSocioTalonera = $_POST["IDCompraTalonera"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];

        $respuesta = SIMWebServiceTaloneras::set_tipo_pago_talorena($IDClub, $IDSocio, $IDUsuario, $IDSocioTalonera, $IDTipoPago, $CodigoPago);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getestadocuentataloneras','".json_encode($_POST)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validapagotalonera":
        require LIBDIR . "SIMWebServiceTaloneras.inc.php";

        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocioTalonera = SIMNet::req("IDCompraTalonera");


        $respuesta = SIMWebServiceTaloneras::valida_pago_talonera($IDClub, $IDSocio, $IDUsuario, $IDSocioTalonera);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','validapagotalonera','".json_encode($_GET)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "eliminatalonera":
        require LIBDIR . "SIMWebServiceTaloneras.inc.php";

        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDSocioTalonera = $_POST["IDCompraTalonera"];

        $respuesta = SIMWebServiceTaloneras::eliminia_talorena($IDClub, $IDSocio, $IDUsuario, $IDSocioTalonera, "", "");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','eliminatalonera','".json_encode($_POST)."','".json_encode($respuesta)."')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // SERVICIO DE CADDIES 
        // AGREGADOS POR Juan Diego Villa Montoya
        // Fecha: 2021-11-25

    case "getcategoriascaddies":
        require LIBDIR . "SIMWebServiceCaddies.inc.php";

        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDServicio = SIMNet::req("IDServicio");
        $IDElemento = SIMNet::req("IDElemento");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");


        $respuesta = SIMWebServiceCaddies::get_categorias_caddies($IDClub, $IDSocio, $IDUsuario, $IDServicio, $IDElemento, $IDClubAsociado);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getcategoriascaddies','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcaddiesdisponibles":
        require LIBDIR . "SIMWebServiceCaddies.inc.php";

        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDServicio = SIMNet::req("IDServicio");
        $IDElemento = SIMNet::req("IDElemento");
        $Fecha = SIMNet::req("Fecha");
        $Hora = SIMNet::req("Hora");
        $IDCategoria = SIMNet::req("IDCategoria");
        $Tag = SIMNet::req("Tag");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");

        $respuesta = SIMWebServiceCaddies::get_caddies_disponibles($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Fecha, $Hora, $IDElemento, $IDCategoria, $Tag, $IDClubAsociado);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getcaddiesdisponibles','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // SERVICIO DE BICICLETAS
        // AGREGADOS POR Juan Diego Villa Montoya
        // Fecha: 2021-11-30

    case "getconfiguraciontalegas2":
        require(LIBDIR . "SIMWebServiceBicicletas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceBicicletas::get_configuracion_bicicleta($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguraciontalegas2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getlistalugarestalega2":
        require(LIBDIR . "SIMWebServiceBicicletas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceBicicletas::get_listalugares_bicicleta($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getlistalugarestalega2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gettalegas2":
        require(LIBDIR . "SIMWebServiceBicicletas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDBeneficiario = SIMNet::req("IDBeneficiario");
        $respuesta = SIMWebServiceBicicletas::get_bicicleta($IDClub, $IDSocio, $IDUsuario, $IDBeneficiario);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('gettalegas2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settalega2":
        require(LIBDIR . "SIMWebServiceBicicletas.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDBicicleta = $_POST["IDTalega"];
        $IDLugarBicicleta = $_POST["IDLugarTalega"];
        $Fecha = $_POST["Fecha"];
        $ObjetosInventario = $_POST["ObjetosInventario"];
        $CamposBicicleta = $_POST["CamposTalega"];

        $respuesta = SIMWebServiceBicicletas::set_bicicleta($IDClub, $IDSocio, $IDUsuario, $IDBicicleta, $IDLugarBicicleta, $Fecha, $ObjetosInventario, $CamposBicicleta, $_FILES);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, IDSocio, Parametros, Respuesta) Values ('settalega2','" . $IDSocio . "','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getmistalegashistorial2":
        require(LIBDIR . "SIMWebServiceBicicletas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Fecha = SIMNet::req("Fecha");
        $respuesta = SIMWebServiceBicicletas::get_mis_bicicleta_historial($IDClub, $IDSocio, $IDUsuario, $Fecha);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmistalegashistorial2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcancelartalega2":
        require(LIBDIR . "SIMWebServiceBicicletas.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDTalega = SIMNet::req("IDTalega");
        $respuesta = SIMWebServiceBicicletas::set_cancelar_bicicleta($IDClub, $IDSocio, $IDUsuario, $IDTalega);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ($IDSocio,'setcancelartalega','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // SERVICIO DE DOCUMENTOS INFINITOS
        // AGREGADOS POR Juan Diego Villa Montoya
        // Fecha: 2021-12-03

    case "getdocumentodinamicoinfinito":
        require(LIBDIR . "SIMWebServiceDocumentoInfinitos.inc.php");
        $IDSubmodulo = SIMNet::req("IDSubmodulo");
        $IDModulo = SIMNet::req("IDModulo");
        $IDSocio = SIMNet::req("IDSocio");

        $respuesta = SIMWebServiceDocumentoInfinitos::get_documento_dinamico_infinito($IDClub, $IDSubmodulo, $IDModulo, $IDSocio);
        //inserta _log
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('99999999','getdocumentodinamicoinfinito','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdocumentofuncionariodinamicoinfinito":
        require(LIBDIR . "SIMWebServiceDocumentoInfinitos.inc.php");
        $IDModulo = SIMNet::req("IDModulo");
        $respuesta = SIMWebServiceDocumentoInfinitos::get_documento_funcionario_dinamico_infinito($IDClub, $IDModulo);
        //inserta _log
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('99999999','getdocumentofuncionariodinamicoinfinito','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // SERVICIO DE CHECKIN
        // AGREGADOS POR Juan Diego Villa Montoya
        // Fecha: 2021-12-07

    case "getconfiguracioncheckinglaboral":
        require(LIBDIR . "SIMWebServiceCheck.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");

        $respuesta = SIMWebServiceCheck::get_configuracion_checking_laboral($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getconfiguracioncheckinglaboral','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmishorasextras":
        require(LIBDIR . "SIMWebServiceCheck.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceCheck::get_mis_horas_extras($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getmicheckinglaboral','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmicheckinglaboral":
        require(LIBDIR . "SIMWebServiceCheck.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceCheck::get_mi_checking_laboral($IDClub, $IDSocio, $IDUsuario);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('$IDSocio','getmicheckinglaboral','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcheckinglaboral":
        require(LIBDIR . "SIMWebServiceCheck.inc.php");
        $IDSocio = $_POST[IDSocio];
        $IDUsuario = $_POST[IDUsuario];
        $Latitud = $_POST[Latitud];
        $Longitud = $_POST[Longitud];
        $EsEntrada = $_POST[EsEntrada];
        $Observaciones = $_POST[Observaciones];
        $IDSitioTrabajo = $_POST[IDSitioTrabajo];
        $respuesta = SIMWebServiceCheck::set_checking_laboral($IDClub, $IDSocio, $IDUsuario, $Latitud, $Longitud, $EsEntrada, $Observaciones, $IDSitioTrabajo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio ,Servicio, Parametros, Respuesta) Values ($IDSocio,'setcheckinglaboral','".json_encode($_POST)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // SERVICIOS ORGANIZADOS Y NUEVOS DE ACCESOS
        // AGREGADOS POR Juan Diego Villa Montoya
        // Fecha: 2022-01-04

    case "eliminamisautorizacionescontratistaanterior":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDAutorizacion = $_POST["IDAutorizacion"];
        $respuesta = SIMWebServiceAccesos::elimina_misautorizaciones_contratista_anterior($IDClub, $IDSocio, $IDAutorizacion);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','eliminamisautorizacionescontratistaanterior','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcargarexcelinvitadosv1":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDSocio = $_POST[IDSocio];
        $IDUsuario = $_POST[IDUsuario];
        $respuesta = SIMWebServiceAccesos::set_cargar_excel_invitadosv1($IDClub, $IDSocio, $IDUsuario, $_FILES);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcargarexcelinvitadosv1','".json_encode($_POST).json_encode($_FILES)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcargarexcelacceso":

        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = $_POST[IDSocio];
        $IDUsuario = $_POST[IDUsuario];
        $respuesta = SIMWebServiceAccesos::set_cargar_excel_acceso($IDClub, $IDSocio, $IDUsuario, $_FILES);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcargarexcelacceso','".json_encode($_POST).json_encode($_FILES)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcargarexcelcontratista":

        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = $_POST[IDSocio];
        $IDUsuario = $_POST[IDUsuario];
        $respuesta = SIMWebServiceAccesos::set_cargar_excel_contratista($IDClub, $IDSocio, $IDUsuario, $_FILES);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcargarexcelcontratista','".json_encode($_POST).json_encode($_FILES)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracioninvitadosv1":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $respuesta = SIMWebServiceAccesos::get_configuracion_invitadosv1($IDClub);
        // //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('1','getconfiguracioninvitadosv1','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionautorizaciones":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $respuesta = SIMWebServiceAccesos::get_configuracion_autorizaciones($IDClub);
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getconfiguracionclasificado','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //get informacion de acceso del club
    case "getparametroacceso":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $respuesta = SIMWebServiceAccesos::get_parametro_acceso($IDClub, $IDSocio);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //get informacion de acceso de app porteria
    case "getparametrosempleados":
        require LIBDIR . "SIMWebServiceAccesos.inc.php";
        $respuesta = SIMWebServiceApp::get_parametros_empleados($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getinvitadodocumento2":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $AppVersion = SIMNet::req("AppVersion");
        if (!empty(SIMNet::req("Tag")))
            $Documento = SIMNet::req("Tag");
        else
            $Documento = SIMNet::req("Documento");

        //$Documento = SIMNet::req( "Tag" );
        $respuesta = SIMWebServiceAccesos::get_invitado_documento($IDClub, $Documento);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getinvitadodocumento2','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //registrar ingreso invitado/contratista
    case "setentradainvitado2":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");

        $IDInvitacion = $_POST["IDInvitacion"];
        $TipoInvitacion = $_POST["TipoInvitacion"];
        $IDUsuario = $_POST["IDUsuario"];
        $Mecanismo = $_POST["Mecanismo"];
        $Respuestas = $_POST["Respuestas"];
        $RespuestasGeneral = $_POST["RespuestasGeneral"];
        $IDLugar = $_POST[IDLugar];
        $respuesta = SIMWebServiceAccesos::set_entrada_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo, $IDUsuario, $Respuestas, $RespuestasGeneral, $IDLugar);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setentradainvitado2','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //registrar ingreso invitado/contratista
    case "setentradainvitado":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDInvitacion = $Tee = $_POST["IDInvitacion"];
        $TipoInvitacion = $_POST["TipoInvitacion"];
        $IDUsuario = $_POST["IDUsuario"];
        $Mecanismo = $_POST["Mecanismo"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceAccesos::set_entrada_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo, $IDUsuario, $Respuestas, "");
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setentradainvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setsalidainvitado2":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDInvitacion = $_POST["IDInvitacion"];
        $TipoInvitacion = $_POST["TipoInvitacion"];
        $IDUsuario = $_POST["IDUsuario"];
        $Mecanismo = $_POST["Mecanismo"];
        $Respuestas = $_POST["Respuestas"];
        $RespuestasGeneral = $_POST["RespuestasGeneral"];
        $IDLugar = $_POST[IDLugar];
        $respuesta = SIMWebServiceAccesos::set_salida_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo, $IDUsuario, $Respuestas, $RespuestasGeneral, $IDLugar);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsalidainvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //registrar salida invitado/contratista
    case "setsalidainvitado":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDInvitacion = $_POST["IDInvitacion"];
        $TipoInvitacion = $_POST["TipoInvitacion"];
        $IDUsuario = $_POST["IDUsuario"];
        $Mecanismo = $_POST["Mecanismo"];
        $Respuestas = $_POST["Respuestas"];
        $respuesta = SIMWebServiceAccesos::set_salida_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo, $IDUsuario, $Respuestas);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsalidainvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getobjetossugeridosinvitado":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDInvitacion = $_GET["IDInvitacion"];
        $TipoInvitacion = $_GET["TipoInvitacion"];
        $IDUsuario = $_GET["IDUsuario"];
        $respuesta = SIMWebServiceAccesos::get_objetos_sugeridos_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "setentradaobjetosexistentesinvitado":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDUsuario = $_POST["IDUsuario"];
        $IDInvitacion = $_POST["IDInvitacion"];
        $TipoInvitacion = $_POST["TipoInvitacion"];
        $IDObjetos = $_POST["IDObjetos"];
        $respuesta = SIMWebServiceAccesos::set_entrada_objetos_existentes_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario, $IDObjetos);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setentradaobjetonuevoinvitado":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDUsuario = $_POST["IDUsuario"];
        $IDInvitacion = $_POST["IDInvitacion"];
        $TipoInvitacion = $_POST["TipoInvitacion"];
        $Campo1 = $_POST["Campo1"];
        $Campo2 = $_POST["Campo2"];
        $IDTipo = $_POST["IDTipo"];
        $respuesta = SIMWebServiceAccesos::set_entrada_objeto_nuevo_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario, $Campo1, $Campo2, $IDTipo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setsalidaobjeto":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDUsuario = $_POST["IDUsuario"];
        $IDInvitacion = $_POST["IDInvitacion"];
        $TipoInvitacion = $_POST["TipoInvitacion"];
        $IDObjetos = $_POST["IDObjetos"];
        $respuesta = SIMWebServiceAccesos::set_salida_objeto($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario, $IDObjetos);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gethistorialaccesosinvitado":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDUsuario = $_GET["IDUsuario"];
        $IDInvitacion = $_GET["IDInvitacion"];
        $TipoInvitacion = $_GET["TipoInvitacion"];
        $IDObjetos = $_GET["IDObjetos"];
        $respuesta = SIMWebServiceAccesos::get_historial_accesos_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar invitados del socio club
    case "setautorizacioninvitado":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $FechaIngreso = $_POST["FechaIngreso"];
        $FechaSalida = $_POST["FechaSalida"];
        $DatosInvitado = $_POST["DatosInvitado"];
        $ValoresFormulario = $_POST["ValoresFormulario"];
        $DiasCheckbox = $_POST["DiasCheckbox"];
        $respuesta = SIMWebServiceAccesos::set_autorizacion_invitado($IDClub, $IDSocio, $FechaIngreso, $FechaSalida, $DatosInvitado, "", $ValoresFormulario, "", $DiasCheckbox);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setautorizacioninvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Invitacion Contratista
    case "setautorizacioncontratista":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $TipoAutorizacion = $_POST["TipoAutorizacion"];
        $FechaIngreso = $_POST["FechaIngreso"];
        $FechaSalida = $_POST["FechaSalida"];
        $HoraIncio = $_POST["HoraIngreso"];
        $HoraFin = $_POST["HoraSalida"];
        $TipoDocumento = $_POST["TipoDocumento"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $Nombre = $_POST["Nombre"];
        $Apellido = $_POST["Apellido"];
        $Email = $_POST["Email"];
        $Placa = $_POST["Placa"];
        $ObservacionSocio = $_POST["Observaciones"];
        $ValoresFormulario = $_POST["ValoresFormulario"];
        $DiasCheckbox = $_POST["DiasCheckbox"];
        $respuesta = SIMWebServiceAccesos::set_autorizacion_contratista($IDClub, $IDSocio, $TipoAutorizacion, $FechaIngreso, $FechaSalida, $TipoDocumento, $NumeroDocumento, $Nombre, $Apellido, $Email, $Placa, "", $HoraInicio, $HoraSalida, $Observaciones, $IDUsuario, $Telefono, $FechaNacimiento, $TipoSangre, $Predio, $Arl, $Eps, $VencimientoArl, $VencimientoEps, $ObservacionSocio, $ArlFile, $DiasCheckbox, $ValoresFormulario, $AceptaTerminos);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setautorizacioncontratista','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar invitados del socio club
    case "setinvitado":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $Nombre = $_POST["Nombre"];
        $FechaIngreso = $_POST["FechaIngreso"];
        $ValoresFormulario = $_POST["ValoresFormulario"];
        $files = (!empty($_FILES)) ? $_FILES : "";
        $respuesta = SIMWebServiceAccesos::set_invitado($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario, "", $files);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setinvitado','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Los invitados Contratistas de un socio
    case "getmisautorizacionescontratista":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $Tag = SIMNet::req("Tag");
        $FechaIngreso = SIMNet::req("FechaIngreso");
        $respuesta = SIMWebServiceAccesos::get_mis_autorizaciones_contratista($IDClub, $IDSocio, $Tag, $FechaIngreso, "Futuro");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisautorizacionescontratista','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Los invitados Contratistas de un socio
    case "getmisautorizacionescontratistaanterior":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $Tag = SIMNet::req("Tag");
        $FechaIngreso = SIMNet::req("FechaIngreso");
        $respuesta = SIMWebServiceAccesos::get_mis_autorizaciones_contratista($IDClub, $IDSocio, $Tag, $FechaIngreso, "Pasado");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Actualizar autorizacion contratista
    case "setcontratistaupdateautorizacion":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDInvitacion = $_POST["IDInvitacion"];
        $IDSocio = $_POST["IDSocio"];
        $TipoAutorizacion = $_POST["TipoAutorizacion"];
        $FechaIngreso = $_POST["FechaIngreso"];
        $FechaSalida = $_POST["FechaSalida"];
        $HoraInicio = $_POST["HoraIngreso"];
        $HoraFin = $_POST["HoraSalida"];
        $TipoDocumento = $_POST["TipoDocumento"];
        $NumeroDocumento = $_POST["NumeroDocumento"];
        $Nombre = $_POST["Nombre"];
        $Apellido = $_POST["Apellido"];
        $Email = $_POST["Email"];
        $Placa = $_POST["Placa"];
        $ObservacionSocio = $_POST["Observaciones"];
        $ValoresFormulario = $_POST["ValoresFormulario"];
        $DiasCheckbox = $_POST["DiasCheckbox"];
        $respuesta = SIMWebServiceAccesos::set_contratista_update_autorizacion($IDClub, $IDSocio, $IDInvitacion, $TipoAutorizacion, $FechaIngreso, $FechaSalida, $TipoDocumento, $NumeroDocumento, $Nombre, $Apellido, $Email, $Placa, "", $HoraInicio, $HoraSalida, $Observaciones, $IDUsuario, $Telefono, $FechaNacimiento, $TipoSangre, $Predio, $Arl, $Eps, $VencimientoArl, $VencimientoEps, $ObservacionSocio, $ValoresFormulario, $CodigoAutorizacion, $ArlFile, $DiasCheckbox);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setcontratistaupdateautorizacion','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Actualizar invitados del socio club
    case "setautorizacioninvitadoupdate":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDInvitacion = $_POST["IDInvitacion"];
        $FechaIngreso = $_POST["FechaIngreso"];
        $FechaSalida = $_POST["FechaSalida"];
        $DatosInvitado = $_POST["DatosInvitado"];
        $ValoresFormulario = $_POST["ValoresFormulario"];
        $DiasCheckbox = $_POST["DiasCheckbox"];
        $respuesta = SIMWebServiceAccesos::set_autorizacion_invitado_update($IDClub, $IDSocio, $IDInvitacion, $FechaIngreso, $FechaSalida, $DatosInvitado, $ValoresFormulario, $DiasCheckbox);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','setautorizacioninvitadoupdate','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Los invitados de accesos especiales de un socio
    case "getmisautorizacionesinvitados":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $Tag = SIMNet::req("Tag");
        $FechaIngreso = SIMNet::req("FechaIngreso");
        $respuesta = SIMWebServiceAccesos::get_mis_autorizaciones_invitados($IDClub, $IDSocio, $Tag, $FechaIngreso, "Futuro");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisautorizacionesinvitadosanterior":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $Tag = SIMNet::req("Tag");
        $FechaIngreso = SIMNet::req("FechaIngreso");
        $respuesta = SIMWebServiceAccesos::get_mis_autorizaciones_invitados($IDClub, $IDSocio, $Tag, $FechaIngreso, "Pasado");
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getmisautorizacionesinvitadosanterior','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //traer invitados/contratista por cedula
    case "getinvitadodocumento":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $AppVersion = SIMNet::req("AppVersion");
        $Documento = SIMNet::req("Documento");
        $Dispositivo = SIMNet::req("Dispositivo");
        $respuesta = SIMWebServiceAccesos::get_invitado_documento_v2($IDClub, $Documento, $Dispositivo);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario ( Servicio, Parametros, Respuesta) Values ('getinvitadodocumento','".json_encode($_GET)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Buscar Presalida
    case "getpresalida":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceAccesos::get_presalida($IDClub, $IDSocio, $Tag);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        //inserta _log
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getpresalida','".json_encode($_POST)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        exit;
        break;

    case "seteliminarinvitadov1":
        require(LIBDIR . "SIMWebServiceAccesos.inc.php");
        $IDSocio = $_POST["IDSocio"];
        $IDSocioInvitado = $_POST["IDSocioInvitado"];
        $respuesta = SIMWebServiceAccesos::set_eliminar_invitado_v1($IDClub, $IDSocio, $IDSocioInvitado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // NUEVOS SERVICIOS CREADOS PARA DIRECTORIO
        // JUAN DIEGO VILLA MONTOYA
        // 2022-02-02

    case "getconfiguraciondirectorio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $respuesta = SIMWebServiceDirectorios::get_configuracion_directorio($IDClub);
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "getconfiguraciondirectoriosocio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $respuesta = SIMWebServiceDirectorios::get_configuracion_directorio_socio($IDClub);
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "getsecciondirectorio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $respuesta = SIMWebServiceDirectorios::get_seccion_directorio($IDClub);
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

    case "getsecciondirectoriosocio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $respuesta = SIMWebServiceDirectorios::get_seccion_directorio_socio($IDClub);
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        break;

        //Enviar directorio del club
    case "getdirectorio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDSeccion = SIMNet::req("IDSeccion");
        $respuesta = SIMWebServiceDirectorios::get_directorio($IDClub, $Tag, $IDSeccion);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getdirectorio','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcategoriadirectorio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $respuesta = SIMWebServiceDirectorios::get_categoria_directorio($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar directorio del club
    case "getdirectoriosocio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $Tag = SIMNet::req("Tag");
        $IDSeccion = SIMNet::req("IDSeccion");
        $respuesta = SIMWebServiceDirectorios::get_directorio_socio($IDClub, $Tag, $IDSeccion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcategoriadirectoriosocio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $respuesta = SIMWebServiceDirectorios::get_categoria_directorio_socio($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Calificacion directorio club
    case "setcalificaciondirectorio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDDirectorio = $_POST["IDDirectorio"];
        $Comentario = utf8_decode($_POST["Comentario"]);
        $Calificacion = utf8_decode($_POST["Calificacion"]);
        $respuesta = SIMWebServiceDirectorios::set_calificacion_directorio($IDClub, $IDSocio, $IDDirectorio, $Comentario, $Calificacion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar Calificacion directorio socios
    case "setcalificaciondirectoriosocios":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDDirectorio = $_POST["IDDirectorioSocio"];
        $Comentario = utf8_decode($_POST["Comentario"]);
        $Calificacion = utf8_decode($_POST["Calificacion"]);
        $respuesta = SIMWebServiceDirectorios::set_calificacion_directorio_socios($IDClub, $IDSocio, $IDDirectorio, $Comentario, $Calificacion);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Consultar comentario de Calificacion directorio socios
    case "getcalificaciondirectorio":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $IDDirectorio = SIMNet::req("IDDirectorio");
        $respuesta = SIMWebServiceDirectorios::get_calificacion_directorio($IDClub, $IDDirectorio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Consultar comentario de Calificacion directorio socios
    case "getcalificaciondirectoriosocios":
        require LIBDIR . "SIMWebServiceDirectorios.inc.php";
        $IDDirectorio = SIMNet::req("IDDirectorio");
        $respuesta = SIMWebServiceDirectorios::get_calificacion_directorio_socios($IDClub, $IDDirectorio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        // NUEVOS SERVICIO DE GALERIAS
        // AGREGADOS POR Juan Diego Villa Montoya
        // Fecha: 2022-02-03

        //likes galerias
    case "getdetallefotogaleria":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 1;
        $IDFoto = SIMNet::req("IDFoto");
        $respuesta = SIMWebServiceGalerias::get_detalle_foto_galeria($IDClub, $IDSocio, $Version, $IDFoto);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdetallefotogaleria2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 2;
        $IDFoto = SIMNet::req("IDFoto");
        $respuesta = SIMWebServiceGalerias::get_detalle_foto_galeria($IDClub, $IDSocio, $Version, $IDFoto);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getdetallefotogaleriaempleados":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 1;
        $IDFoto = SIMNet::req("IDFoto");
        $respuesta = SIMWebServiceGalerias::get_detalle_foto_galeria_empleados($IDClub, $IDUsuario, $Version, $IDFoto);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdetallefotogaleriaempleados2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 2;
        $IDFoto = SIMNet::req("IDFoto");
        $respuesta = SIMWebServiceGalerias::get_detalle_foto_galeria_empleados($IDClub, $IDUsuario, $Version, $IDFoto);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "actualizarmegusta":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 1;
        $IDFoto = $_POST["IDFoto"];
        $MeGusta = $_POST["MeGusta"];

        $respuesta = SIMWebServiceGalerias::actualizar_me_gusta($IDClub, $IDSocio, $Version, $IDFoto, $MeGusta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "actualizarmegusta2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 2;
        $IDFoto = $_POST["IDFoto"];
        $MeGusta = $_POST["MeGusta"];

        $respuesta = SIMWebServiceGalerias::actualizar_me_gusta($IDClub, $IDSocio, $Version, $IDFoto, $MeGusta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "actualizarmegustaempleados":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 1;
        $IDFoto = $_POST["IDFoto"];
        $MeGusta = $_POST["MeGusta"];
        $respuesta = SIMWebServiceGalerias::actualizar_me_gusta_empleados($IDClub, $IDUsuario, $Version, $IDFoto, $MeGusta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "actualizarmegustaempleados2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 2;
        $IDFoto = $_POST["IDFoto"];
        $MeGusta = $_POST["MeGusta"];
        $respuesta = SIMWebServiceGalerias::actualizar_me_gusta_empleados($IDClub, $IDUsuario, $Version, $IDFoto, $MeGusta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //fin likes galerias

    case "getconfiguraciongaleria":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 1;
        $respuesta = SIMWebServiceGalerias::get_configuracion_galeria($IDClub, $Version);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguraciongaleria2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 2;
        $respuesta = SIMWebServiceGalerias::get_configuracion_galeria($IDClub, $Version);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguraciongaleriaempleados":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 1;
        $respuesta = SIMWebServiceGalerias::get_configuracion_galeria_empleados($IDClub, $Version);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguraciongaleriaempleados2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $Version = 2;
        $respuesta = SIMWebServiceGalerias::get_configuracion_galeria_empleados($IDClub, $Version);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar secciones de galerias del club o del socio
    case "getsecciongaleria":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceGalerias::get_seccion_galeria($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getsecciongaleria2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceGalerias::get_seccion_galeria($IDClub, $IDSocio, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getsecciongaleriaempleados":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceGalerias::get_seccion_galeria_empleados($IDClub, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getsecciongaleriaempleados2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $IDSocio = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceGalerias::get_seccion_galeria_empleados($IDClub, $IDUsuario, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar galerias del club o del socio o de la seccion o busqueda
    case "getgaleria":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDSeccionGaleria = SIMNet::req("IDSeccion");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceGalerias::get_galeria($IDClub, $IDSeccionGaleria, $IDSocio, $Tag);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getgaleria2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDSeccionGaleria = SIMNet::req("IDSeccion");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceGalerias::get_galeria($IDClub, $IDSeccionGaleria, $IDSocio, $Tag, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar galerias del club o del empleado o de la seccion o busqueda
    case "getgaleriaempleados":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSeccionGaleria = SIMNet::req("IDSeccion");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceGalerias::get_galeria_empleados($IDClub, $IDSeccionGaleria, $IDUsuario, $Tag);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getgaleriaempleados2":
        require LIBDIR . "SIMWebServiceGalerias.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSeccionGaleria = SIMNet::req("IDSeccion");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceGalerias::get_galeria_empleados($IDClub, $IDSeccionGaleria, $IDUsuario, $Tag, "2");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // NUEVOS SERVICIOS PARA RESEVAS

    case "gethistorialmisreservas":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceReservas::get_historial_misreservas($IDClub, $IDSocio, $IDUsuario);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        break;

    case "getconfiguracionreservas":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $respuesta = SIMWebServiceReservas::get_configuracion_reservas($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        break;

    case "getlistaesperareservasocio":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $respuesta = SIMWebServiceReservas::get_lista_espera_reserva_socio($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        break;

    case "eliminarlistaesperasocio":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDClub = $_POST["IDClub"];
        $IDReserva = $_POST["IDReserva"];

        $respuesta = SIMWebServiceReservas::eliminar_lista_espera_socio($IDClub, $IDSocio, $IDReserva);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcategoriasreservas":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $respuesta = SIMWebServiceReservas::get_categorias_reservas($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        break;

    case "getclubesasociadosreserva":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $respuesta = SIMWebServiceReservas::get_clubes_asociados_reserva($IDClub, $IDSocio);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        // SIMLog::insert_app( $action , $IDClub, $_GET, $respuesta );
        break;

        //Enviar socios del club
    case "getservicios":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $TipoApp = SIMNet::req("TipoApp");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $IDCategoriasServicios = SIMNet::req("IDSeccion");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebServiceReservas::get_servicios($IDClub, $TipoApp, $IDUsuario, $IDSocio, $IDCategoriasServicios, $IDClubAsociado);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio, Servicio, Parametros, Respuesta) Values ($IDSocio,'getservicios','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "verificarcodigocortesiareservas":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $Hora = SIMNet::req("Hora");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $Codigo = SIMNet::req("Codigo");
        $respuesta = SIMWebServiceReservas::verificarcodigocortesiareservas($IDClub, $IDSocio, $IDUsuario, $IDServicio, $IDElemento, $Fecha, $Hora, $IDClubAsociado, $Codigo);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','verificarcodigocortesiareservas','".json_encode($_GET)."','".json_encode($respuesta)."')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "gethorainicioservicio":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $TipoApp = SIMNet::req("TipoApp");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $Fecha = SIMNet::req("Fecha");
        $IDServicio = SIMNet::req("IDServicio");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebServiceReservas::get_horainicio_servicio($IDClub, $TipoApp, $IDUsuario, $IDSocio, $Fecha, $IDServicio, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gethorafinservicio":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $TipoApp = SIMNet::req("TipoApp");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $Fecha = SIMNet::req("Fecha");
        $IDServicio = SIMNet::req("IDServicio");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebServiceReservas::get_hora_fin_servicio($IDClub, $TipoApp, $IDUsuario, $IDSocio, $Fecha, $IDServicio, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;



        // NUEVOS SERVICIOS PARA CANJES
        // AGREGADOS POR JUAN DIEGO VILLA MONTOYA
        // 2022-02-15

    case "seteliminarcanje":
        require LIBDIR . "SIMWebServiceCanjes.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $IDCanjeSolicitud = $_POST["IDCanjeSolicitud"];
        $respuesta = SIMWebServiceCanjes::set_eliminar_canje($IDClub, $IDSocio, $IDUsuario, $IDCanjeSolicitud);

        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','seteliminarcanje','".json_encode($_POST)."','".json_encode($respuesta)."')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getsolicitudcanje":
        require LIBDIR . "SIMWebServiceCanjes.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDCanjeSolicitud = SIMNet::req("IDCanjeSolicitud");
        $respuesta = SIMWebServiceCanjes::get_solicitud_canje($IDClub, $IDSocio, $IDCanjeSolicitud);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Guardar Solicitud Canje
    case "setsolicitudcanje":
        require LIBDIR . "SIMWebServiceCanjes.inc.php";
        $IDSocio = $_POST["IDSocio"];
        $IDListaClubes = $_POST["IDListaClubes"];
        $FechaInicio = $_POST["FechaInicio"];
        $CantidadDias = $_POST["CantidadDias"];
        $Beneficiarios = $_POST["Beneficiarios"];
        $ValoresFormulario = $_POST["ValoresFormulario"];
        $ComentariosSocio = $_POST["Comentario"];

        $IDCiudad = $_POST["IdCiudad"];
        $IDPais = $_POST["IdPais"];
        $Dispositivo = $_POST["Dispositivo"];
        $respuesta = SIMWebServiceCanjes::set_solicitud_canje($IDClub, $IDSocio, $IDListaClubes, $FechaInicio, $CantidadDias, $Beneficiarios, $ValoresFormulario, $IDCiudad, $IDPais, $Dispositivo, $ComentariosSocio);
        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setsolicitudcanje','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getconfiguracioncanjes":
        require LIBDIR . "SIMWebServiceCanjes.inc.php";
        $respuesta = SIMWebServiceCanjes::get_configuracion_canjes($IDClub);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getpaisescanjes":
        require LIBDIR . "SIMWebServiceCanjes.inc.php";
        $respuesta = SIMWebServiceCanjes::get_paises_canjes($IDClub);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getciudadescanjes":
        require LIBDIR . "SIMWebServiceCanjes.inc.php";
        $IDPais = SIMNet::req("IDPais");
        $respuesta = SIMWebServiceCanjes::get_ciudades_canjes($IDClub, $IDPais);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Enviar listado de clubes con convenio del club
    case "getlistaclubes":
        require LIBDIR . "SIMWebServiceCanjes.inc.php";
        $IDPais = SIMNet::req("IDPais");
        $IDCiudad = SIMNet::req("IDCiudad");
        $respuesta = SIMWebServiceCanjes::get_lista_clubes($IDClub, $IDPais, $IDCiudad);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        // NUEVOS SERVICIOS PARA eCADDIE
        // AGREGADOS POR JUAN DIEGO VILLA MONTOYA
        // 2022-02-21

    case "getconfiguracioncaddies":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $respuesta = SIMWebServiceEcaddie::get_configuracion_caddies($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getclubescaddies":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $respuesta = SIMWebServiceEcaddie::get_clubes_caddies($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getclubesagendacaddies":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $respuesta = SIMWebServiceEcaddie::get_clubes_caddies($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getservicioscaddies":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDClubSeleccion = SIMNet::req("IDClubSeleccion");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceEcaddie::get_servicios_caddies($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion);
        //SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getfechasdisponiblesserviciocaddies":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDClubSeleccion = SIMNet::req("IDClubSelecciono");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDServicio = SIMNet::req("IDServicio");
        $respuesta = SIMWebServiceEcaddie::get_fechas_disponibles_servicio_caddies($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, $IDServicio);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdisponibilidadelementoserviciocaddie":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDClubSeleccion = SIMNet::req("IDClubSelecciono");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $respuesta = SIMWebServiceEcaddie::get_disponibilidad_elemento_servicio_caddie($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, $IDServicio, $Fecha);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcaddiesdisponiblescaddies":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClubSeleccion = SIMNet::req("IDClubSeleccion");
        $Tag = SIMNet::req("Tag");
        $Fecha = SIMNet::req("Fecha");
        $IDServicio = SIMNet::req("IDServicio");
        $Hora = SIMNet::req("Hora");
        $IDElemento = SIMNet::req("IDElemento");
        $IDCategoria = SIMNet::req("IDCategoria");

        $respuesta = SIMWebServiceEcaddie::get_caddies_disponibles_caddies($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, $Tag, $Fecha, $IDServicio, $Hora, $IDElemento, $IDCategoria);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setsolicitarcaddierappi":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDSocio = $_POST[IDSocio];
        $IDUsuario = $_POST[IDUsuario];
        $IDClubSeleccion = $_POST[IDClubSeleccion];
        $Fecha = $_POST[Fecha];
        $IDServicio = $_POST[IDServicio];
        $Hora = $_POST[Hora];
        $IDElemento = $_POST[IDElemento];
        $TipoCaddie = $_POST[TipoCaddie];
        $OpcionesCaddies = $_POST[OpcionesCaddies];
        $IDCategoria = $_POST[IDCategoria];
        $respuesta = SIMWebServiceEcaddie::set_solicitar_caddie_rappi($IDClub, $IDSocio, $IDUsuario, $IDClubSeleccion, $Fecha, $IDServicio, $Hora, $IDElemento, $TipoCaddie, $OpcionesCaddies, $IDCategoria);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getestadosolicitudcaddierappi":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $respuesta = SIMWebServiceEcaddie::get_estado_solicitud_caddie_rappi($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisreservascaddiessocio":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDSolicitud = SIMNet::req("IDSolicitud");
        $respuesta = SIMWebServiceEcaddie::get_mis_reservas_caddies_socio($IDClub, $IDSocio, $IDUsuario, $IDSolicitud);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;

        break;
    case "getmisreservascaddiesempleado":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDSolicitud = SIMNet::req("IDSolicitud");
        $respuesta = SIMWebServiceEcaddie::get_mis_reservas_caddies_empleado($IDClub, $IDSocio, $IDUsuario, $IDSolicitud);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "settipopagocaddiereserva":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDSolicitud = $_POST["IDSolicitud"];
        $IDTipoPago = $_POST["IDTipoPago"];
        $CodigoPago = $_POST["CodigoPago"];

        $respuesta = SIMWebServiceEcaddie::set_tipo_pago_caddie_reserva($IDClub, $IDSocio, $IDUsuario, $IDSolicitud, $IDTipoPago, $CodigoPago);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteliminarcaddiereserva":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDSolicitud = $_POST["IDSolicitud"];

        $respuesta = SIMWebServiceEcaddie::set_eliminar_caddie_reserva($IDClub, $IDSocio, $IDUsuario, $IDSolicitud);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "seteliminarcaddiereservaempleado":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDSolicitud = $_POST["IDSolicitud"];

        $respuesta = SIMWebServiceEcaddie::set_eliminar_caddie_reserva_empleado($IDClub, $IDUsuario, $IDSolicitud);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setagendacaddie":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $HorasSeleccionadas = $_POST[HorasSeleccionadas];
        $DiasSeleccionados = $_POST[DiasSeleccionados];
        $IDClubSeleccionado = $_POST[IDClubSeleccionado];
        $TurboCaddieActivado = $_POST[TurboCaddieActivado];

        $respuesta = SIMWebServiceEcaddie::set_agenda_caddie($IDClub, $IDUsuario, $HorasSeleccionadas, $DiasSeleccionados, $IDClubSeleccionado, $TurboCaddieActivado);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmiagendacaddie":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";

        $respuesta = SIMWebServiceEcaddie::get_mi_agenda_caddie($IDClub, $IDUsuario);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setrespuestadecaddieareserva":
        require LIBDIR . "SIMWebServiceEcaddie.inc.php";
        $IDSolicitud = $_POST["IDSolicitud"];
        $AceptaSolicitud = $_POST["AceptaSolicitud"];

        $respuesta = SIMWebServiceEcaddie::set_respuesta_de_caddie_a_reserva($IDClub, $IDUsuario, $IDSolicitud, $AceptaSolicitud);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        // NUEVOS SERVICIOS PARA SORTEOS
        // AGREGADOS POR JUAN DIEGO VILLA MONTOYA
        // 2022-03-28

    case "getserviciossorteoturnos":
        require LIBDIR . "SIMWebServiceSorteos.inc.php";
        $respuesta = SIMWebServiceSorteos::get_servicios_sorteo_turnos($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getfechassorteoturnos":
        require LIBDIR . "SIMWebServiceSorteos.inc.php";
        $IDServicio = SIMNet::req("IDServicio");
        $respuesta = SIMWebServiceSorteos::get_fechas_sorteo_turnos($IDClub, $IDSocio, $IDUsuario, $IDServicio);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getsociossorteosclub":
        require LIBDIR . "SIMWebServiceSorteos.inc.php";
        $IDServicio = SIMNet::req("IDServicio");
        $Tag = SIMNet::req("Tag");
        $Fecha = SIMNet::req("Fecha");
        $respuesta = SIMWebServiceSorteos::get_socios_sorteos_club($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Tag, $Fecha);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validarinvitadossorteo":
        require LIBDIR . "SIMWebServiceSorteos.inc.php";
        $IDServicio = $_POST["IDServicio"];
        $Invitados = $_POST["Invitados"];
        $Fecha = $_POST["Fecha"];
        $respuesta = SIMWebServiceSorteos::validar_invitados_sorteo($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Fecha, $Invitados);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getelementofechasorteoturnos":
        require LIBDIR . "SIMWebServiceSorteos.inc.php";
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $respuesta = SIMWebServiceSorteos::get_elemento_fecha_sorteo_turnos($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Fecha);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setsorteoservicio":
        require LIBDIR . "SIMWebServiceSorteos.inc.php";
        $IDServicio = $_POST["IDServicio"];
        $Fecha = $_POST["Fecha"];
        $Invitados = $_POST["Invitados"];
        $Elementos = $_POST["Elementos"];
        $respuesta = SIMWebServiceSorteos::set_sorteo_servicio($IDClub, $IDSocio, $IDUsuario, $IDServicio, $Fecha, $Invitados, $Elementos);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisreservasorteoturnos":
        require LIBDIR . "SIMWebServiceSorteos.inc.php";
        $respuesta = SIMWebServiceSorteos::get_mis_reserva_sorteo_turnos($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "eliminarsorteoreserva":
        require LIBDIR . "SIMWebServiceSorteos.inc.php";
        $IDReserva = $_POST["IDReserva"];
        $respuesta = SIMWebServiceSorteos::eliminar_sorteo_reserva($IDClub, $IDSocio, $IDUsuario, $IDReserva);
        // SIMLog::insert_app( $action,$IDClub, $_GET, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracioncarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";
        SIMNet::req("IDServicio");
        $IDClub = SIMNet::req("IDClub");
        $respuesta = SIMWebServiceCarPool::get_configuracion($IDClub);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getviajescarpooldisponibles":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Sentido = SIMNet::req("Sentido");
        $IDTipoVehiculo = SIMNet::req("IDTipoVehiculo");
        $tipo = 1;

        $respuesta = SIMWebServiceCarPool::get_viajes($IDSocio, $IDUsuario, $IDClub, $Sentido, $tipo, $IDTipoVehiculo);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisviajespublicadoscarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Sentido = SIMNet::req("Sentido");
        $tipo = 2;

        $respuesta = SIMWebServiceCarPool::get_viajes($IDSocio, $IDUsuario, $IDClub, $Sentido, $tipo);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisviajessolicitadoscarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");

        $respuesta = SIMWebServiceCarPool::get_viajes_solicitados($IDSocio, $IDUsuario);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "seteliminarrutapublicadacarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDViaje = $_POST["IDViaje"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];

        $respuesta = SIMWebServiceCarPool::set_eliminar_ruta_publicada_carpool($IDSocio, $IDUsuario, $IDViaje);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setaplicarcarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDViaje = $_POST["IDViaje"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];

        $respuesta = SIMWebServiceCarPool::set_solicitar($IDSocio, $IDUsuario, $IDViaje);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcarpool":
    case "setreusarcarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Fecha = $_POST["Fecha"];
        $Hora = $_POST["Hora"];
        $LugarEncuentro = $_POST["LugarEncuentro"];
        $IDTipoVehiculo = $_POST["IDTipoVehiculo"];
        $Sentido = $_POST["Sentido"];
        $Latitud = $_POST["Latitud"];
        $Longitud = $_POST["Longitud"];
        $Direccion = $_POST["Direccion"];
        $Cupos = $_POST["Cupos"];
        $Modelo = $_POST["Modelo"];
        $Color = $_POST["Color"];
        $Valor = $_POST["Valor"];
        $Telefono = $_POST["Telefono"];
        $Placa = $_POST["Placa"];
        $Marca = $_POST["Marca"];
        $Descripcion = $_POST["Descripcion"];

        $respuesta = SIMWebServiceCarPool::set_viaje($IDClub, $IDSocio, $IDUsuario, $Fecha, $Hora, $LugarEncuentro, $IDTipoVehiculo, $Sentido, $Latitud, $Longitud, $Direccion, $Cupos, $Modelo, $Color, $Valor, $Telefono, $Placa, $Marca, $Descripcion);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcalificarcarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDSolicitudViaje = $_POST["IDSolicitudViaje"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Calificacion = $_POST["Calificacion"];
        $Motivos = $_POST["MotivosCalificacionSeleccionados"];

        $respuesta = SIMWebServiceCarPool::set_calificacion($IDSolicitudViaje, $IDSocio, $IDUsuario, $Calificacion, $Motivos);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setaprobarsolicitudcarpool":

        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDViaje = $_POST["IDViaje"];
        $IDSolicitudViaje = $_POST["IDSolicitudViaje"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Aprobado = $_POST["Aprobado"];

        $respuesta = SIMWebServiceCarPool::set_aprobar($IDViaje, $IDSolicitudViaje, $IDSocio, $IDUsuario, $Aprobado);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcancelarviajecarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDViaje = $_POST["IDViaje"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];

        $respuesta = SIMWebServiceCarPool::set_cancelar_viaje($IDViaje, $IDSocio, $IDUsuario);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcancelarsolicitudcarpool":
        require LIBDIR . "SIMWebServiceCarPool.inc.php";

        $IDSolicitudViaje = $_POST["IDSolicitudViaje"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];

        $respuesta = SIMWebServiceCarPool::set_cancelar_solicitud($IDSolicitudViaje, $IDSocio, $IDUsuario);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getconfiguracionreservahorario":
        require LIBDIR . "SIMWebServiceNuba.inc.php";
        $IDClub = SIMNet::req("IDClub");
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::get_configuracion_reservas($IDClub, $IDModulo);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gethijosreservahorario":
        require LIBDIR . "SIMWebServiceNuba.inc.php";
        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::get_hijos_reserva($IDClub, $IDSocio, $IDModulo);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getserviciosreservahorario":
        require LIBDIR . "SIMWebServiceNuba.inc.php";
        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDHijo = SIMNet::req("IDHijo");
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::get_servicios_reserva($IDClub, $IDSocio, $IDHijo, $IDModulo);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gethoraselementosreservahorario":
        require LIBDIR . "SIMWebServiceNuba.inc.php";
        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDHijo = SIMNet::req("IDHijo");
        $IDServicio = SIMNet::req("IDServicio");
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::get_elementos_reservas($IDClub, $IDSocio, $IDHijo, $IDServicio, $IDModulo);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setreservahorario":
        require LIBDIR . "SIMWebServiceNuba.inc.php";

        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDHijo = $_POST["IDHijo"];
        $IDServicio = $_POST["IDServicio"];
        $Fechas = $_POST["Fechas"];
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::set_reserva_horario($IDClub, $IDSocio, $IDHijo, $IDServicio, $Fechas, $IDModulo);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisreservashorario":
        require LIBDIR . "SIMWebServiceNuba.inc.php";
        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::get_mis_reservas_horario($IDClub, $IDSocio, $IDModulo);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setcancelarreservahorario":
        require LIBDIR . "SIMWebServiceNuba.inc.php";

        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDHijo = $_POST["IDHijo"];
        $IDReservaHorario = $_POST["IDReservaHorario"];

        $respuesta = SIMWebService::elimina_reserva_general($IDClub, $IDSocio, $IDReservaHorario);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setactualizaredades":
        require LIBDIR . "SIMWebServiceNuba.inc.php";

        $IDClub = $_POST["IDClub"];

        $respuesta = SIMWebServiceNuba::actualizar_edades($IDClub);
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "setverificarhorario":
        require LIBDIR . "SIMWebServiceNuba.inc.php";

        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDHijo = $_POST["IDHijo"];
        $IDServicio = $_POST["IDServicio"];
        $Fechas = $_POST["Fechas"];
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::set_verificar_horario($IDClub, $IDSocio, $IDHijo, $IDServicio, $Fechas, $IDModulo);
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getparametrospagoreserva":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDReserva = SIMNet::req("IDReserva");
        $respuesta = SIMWebServiceReservas::get_parametros_pago_reserva($IDClub, $IDSocio, $IDReserva);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getticketsdescuentoservicio":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $IDServicio = SIMNet::req("IDServicio");
        $respuesta = SIMWebServiceReservas::get_tickets_descuento_servicio($IDClub, $IDClubAsociado, $IDSocio, $IDServicio);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcamposinvitadoexterno":
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $IDServicio = SIMNet::req("IDServicio");
        $respuesta = SIMWebServiceReservas::get_campos_invitado_externo($IDClub, $IDClubAsociado, $IDSocio, $IDServicio);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //GAMEGOLF
    case "getjuegosgolfcourses":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Texto = SIMNet::req("Texto");
        $respuesta = SIMWebServiceGameGolf::get_juegos_golf_courses($IDClub, $IDSocio, $IDUsuario, $Texto);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "getjuegosgolfjugadores":
        require("../admin/configgamegolf.inc.php");
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Texto = SIMNet::req("Texto");
        $respuesta = SIMWebServiceGameGolf::get_juegos_golf_jugadores($IDClub, $IDSocio, $IDUsuario, $Texto);
        //SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
    case "setjugadorexternojuegosgolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $NombreJugador = SIMNet::req("NombreJugador");
        $NumeroDocumento = SIMNet::req("NumeroDocumento");
        $Handicap = SIMNet::req("Handicap");
        $Email = SIMNet::req("Email");
        $Celular = SIMNet::req("Celular");
        $respuesta = SIMWebServiceGameGolf::set_jugador_externo_juegos_golf($IDClub, $IDSocio, $IDUsuario, $NombreJugador, $NumeroDocumento, $Handicap, $Email, $Celular);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setfavoritojuegosgolfjugadores":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJugador = SIMNet::req("IDJugador");
        $Favorito = SIMNet::req("Favorito");
        $respuesta = SIMWebServiceGameGolf::set_favorito_juegos_golf_jugadores($IDClub, $IDSocio, $IDUsuario, $IDJugador, $Favorito);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setfavoritojuegogolfcourse":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDGolfCourse = SIMNet::req("IDGolfCourse");
        $Favorito = SIMNet::req("Favorito");
        $respuesta = SIMWebServiceGameGolf::set_favorito_juego_golf_course($IDClub, $IDSocio, $IDUsuario, $IDGolfCourse, $Favorito);
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjugadorsociojuegosgolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceGameGolf::get_jugador_socio_juegos_golf($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getformatosjuegosgolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $respuesta = SIMWebServiceGameGolf::get_formatos_juegos_golf($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "agregarjuegogolf":

        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDGolfCourse = SIMNet::req("IDGolfCourse");
        $NumeroHoyos = SIMNet::req("NumeroHoyos");
        $IDMarca = SIMNet::req("IDMarca");
        $HoyoInicial = SIMNet::req("HoyoInicial");
        $Grupos = $_POST["Grupos"];
        $FormatosJuegoCreados = $_POST["FormatosJuegoCreados"];

        $respuesta = SIMWebServiceGameGolf::agregar_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGolfCourse, $NumeroHoyos, $IDMarca, $Grupos, $FormatosJuegoCreados, $HoyoInicial);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validaedicionjugadorjuegogolf":
        require("../admin/configgamegolf.inc.php");
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJugador = SIMNet::req("IDJugador");
        $IDGolfCourse = SIMNet::req("IDGolfCourse");
        $IDMarca = SIMNet::req("IDMarca");
        $Handicap = SIMNet::req("Handicap");
        $HoyosAJugar = SIMNet::req("HoyosAJugar");
        $HoyoInicial = SIMNet::req("HoyoInicial");

        $respuesta = SIMWebServiceGameGolf::valida_edicion_jugador_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGolfCourse, $IDMarca, $Handicap, $HoyosAJugar, $HoyoInicial);
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");

        $respuesta = SIMWebServiceGameGolf::get_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getgolpesjuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");

        $respuesta = SIMWebServiceGameGolf::get_golpes_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setgolpesjuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $Golpes = $_POST["Golpes"];

        $respuesta = SIMWebServiceGameGolf::set_golpes_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $Golpes);
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjuegogolfresultadogeneral":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDJugador = SIMNet::req("IDJugador");
        $IDPartida = SIMNet::req("IDPartida");
        $Vista = SIMNet::req("Vista");

        $respuesta = SIMWebServiceGameGolf::get_juego_golf_resultado_general($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDJugador, $Vista, $IDPartida);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getresultadoformatojuegoindividualversus":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDFormatosJuegos = SIMNet::req("IDFormatosJuegos");
        $Vista = SIMNet::req("Vista");
        $SoloMatch = SIMNet::req("SoloMatch");

        $respuesta = SIMWebServiceGameGolf::get_resultado_formato_juego_individual_versus($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatosJuegos, $Vista, $SoloMatch);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getresultadoformatojuegoparejasversus":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDFormatosJuegos = SIMNet::req("IDFormatosJuegos");
        $Vista = SIMNet::req("Vista");
        $SoloMatch = SIMNet::req("SoloMatch");

        $respuesta = SIMWebServiceGameGolf::get_resultado_formato_juego_parejas_versus($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatosJuegos, $Vista, $SoloMatch);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjuegogolfresultadogrupaldefecto":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDFormatoJuego = SIMNet::req("IDFormatoJuego");

        $respuesta = SIMWebServiceGameGolf::get_juego_golf_resultado_formato_juego($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatoJuego, "Grupal");
        //SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjuegogolfresultadoindividualdefecto":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDFormatoJuego = SIMNet::req("IDFormatoJuego");

        $respuesta = SIMWebServiceGameGolf::get_juego_golf_resultado_formato_juego($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatoJuego, "individual");
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjuegogolfresultadoformatojuego":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDFormatoJuego = SIMNet::req("IDFormatoJuego");
        $IDPartida = SIMNet::req("IDPartida");
        $Vista = SIMNet::req("Vista");


        $respuesta = SIMWebServiceGameGolf::get_juego_golf_resultado_formato_juego($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatoJuego, $Vista, $IDPartida);
        //SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjuegogolfconfigurado":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");

        $respuesta = SIMWebServiceGameGolf::get_juego_golf_configurado($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "actualizarjuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDMarca = SIMNet::req("IDMarca");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $FormatosJuegoCreados = $_POST["FormatosJuegoCreados"];
        $Grupos = $_POST["Grupos"];
        $respuesta = SIMWebServiceGameGolf::actualizar_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $FormatosJuegoCreados, $Grupos, $IDMarca);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getformatosdefectojuegosgolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");

        $respuesta = SIMWebServiceGameGolf::get_formatos_defecto_juegos_golf($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjuegosgolfjuegoactivo":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");

        $respuesta = SIMWebServiceGameGolf::get_juegos_golf_juego_activo($IDClub, $IDSocio, $IDUsuario);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setfinalizarjuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");

        $respuesta = SIMWebServiceGameGolf::set_finalizar_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "validarfinalizarjuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");

        $respuesta = SIMWebServiceGameGolf::validar_finalizar_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gethandicapjuegosgolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDGolfCourse = SIMNet::req("IDGolfCourse");
        $IDMarca = SIMNet::req("IDMarca");
        $Jugadores = $_POST["Jugadores"];

        $respuesta = SIMWebServiceGameGolf::get_handicap_juegos_golf($IDClub, $IDGolfCourse, $IDMarca, $Jugadores, $IDSocio);
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getmisjuegosgolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Fecha = SIMNet::req("Fecha");

        $respuesta = SIMWebServiceGameGolf::get_mis_juegos_golf($IDClub, $IDSocio, $IDUsuario, $Fecha);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getjuegosgolfdemisgrupos":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $Fecha = SIMNet::req("Fecha");
        $Vista = "MisGrupos";
        $respuesta = SIMWebServiceGameGolf::get_juegos_golf_de_mis_grupos($IDClub, $IDSocio, $IDUsuario, $Fecha);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "eliminarjuegogolfformatoconfigurado":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDFormatoJuego = SIMNet::req("IDFormatoJuego");
        $IDPartida = SIMNet::req("IDPartida");

        $respuesta = SIMWebServiceGameGolf::eliminar_juego_golf_formato_configurado($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatoJuego, $IDPartida);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "editarjuegogolfformatoconfigurado":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $FormatoJuego = $_POST["FormatoJuego"];

        $respuesta = SIMWebServiceGameGolf::editar_juego_golf_formato_configurado($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $FormatoJuego);
        SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "crearjuegogolfformatoconfigurado":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $FormatoJuego = $_POST["FormatoJuego"];

        $respuesta = SIMWebServiceGameGolf::crear_juego_golf_formato_configurado($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $FormatoJuego);
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getmisgruposjuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDGrupo = SIMNet::req("IDGrupo");

        $respuesta = SIMWebServiceGameGolf::get_mis_grupos_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGrupo);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "crearmigrupojuegosgolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $NombreGrupo = SIMNet::req("NombreGrupo");
        $Participantes = $_POST["Participantes"];

        $respuesta = SIMWebServiceGameGolf::crear_mi_grupo_juegos_golf($IDClub, $IDSocio, $IDUsuario, $NombreGrupo, $Participantes);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "editarmigrupojuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDGrupo = SIMNet::req("IDGrupo");
        $NombreGrupo = SIMNet::req("NombreGrupo");
        $Participantes = $_POST["Participantes"];

        $respuesta = SIMWebServiceGameGolf::editar_mi_grupo_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGrupo, $NombreGrupo, $Participantes);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "salirdemigrupojuegogolf":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDGrupo = SIMNet::req("IDGrupo");
        $IDJugador = SIMNet::req("IDJugador");

        $respuesta = SIMWebServiceGameGolf::salir_de_mi_grupo_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGrupo, $IDJugador);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdetallejuegoindividualvs":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDFormatoJuego = SIMNet::req("IDFormatoJuego");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDPartida = SIMNet::req("IDPartida");
        $Vista = SIMNet::req("Vista");

        $respuesta = SIMWebServiceGameGolf::get_detalle_juego_individual_vs($IDClub, $IDSocio, $IDUsuario, $IDFormatoJuego, $IDJugador, $IDJuegoGolf, $IDPartida, $Vista);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdetallejuegoparejavs":
        require LIBDIR . "SIMWebServiceGameGolf.inc.php";
        require("../admin/configgamegolf.inc.php");
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDFormatoJuego = SIMNet::req("IDFormatoJuego");
        $IDJuegoGolf = SIMNet::req("IDJuegoGolf");
        $IDPartida = SIMNet::req("IDPartida");
        $Vista = SIMNet::req("Vista");

        $respuesta = SIMWebServiceGameGolf::get_detalle_juego_pareja_vs($IDClub, $IDSocio, $IDUsuario, $IDFormatoJuego, $IDJugador, $IDJuegoGolf, $IDPartida, $Vista);
        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN GAMEGOLF

        //inicio Objetos prestados
        //configuracion
    case "getconfiguracionobjetosprestados":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClub = SIMNet::req("IDClub");
        $respuesta = SIMWebServiceObjetosPrestados::get_configuracion_objetos_prestados($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionobjetosprestados','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //categorias
    case "getcategoriasobjetosprestados":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClub = SIMNet::req("IDClub");
        $respuesta = SIMWebServiceObjetosPrestados::get_categorias_objetos_prestados($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getcategoriasobjetosprestados','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getobjetosprestadospendientes":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDCategoriaObjeto = SIMNet::req("IDCategoriaObjeto");
        $respuesta = SIMWebServiceObjetosPrestados::get_objetos_prestados_pendientes($IDCategoriaObjeto, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getobjetosprestadospendientes','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getobjetosprestadosentregados":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDCategoriaObjeto = SIMNet::req("IDCategoriaObjeto");
        $respuesta = SIMWebServiceObjetosPrestados::get_objetos_prestados_entregados($IDCategoriaObjeto, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getobjetosprestadosentregados','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getconfiguracionobjetosprestadosadministrador":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClub = SIMNet::req("IDClub");
        $respuesta = SIMWebServiceObjetosPrestados::get_configuracion_objetos_prestados_administrador($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionobjetosprestadosadministrador','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getdetallecategoriaobjetosprestados":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDCategoriaObjeto = SIMNet::req("IDCategoriaObjeto");
        $respuesta = SIMWebServiceObjetosPrestados::get_detalle_categoria_objetos_prestados($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionobjetosprestadosadministrador','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getlugaresentregaobjetosprestados":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDCategoriaObjeto = SIMNet::req("IDCategoriaObjeto");
        $respuesta = SIMWebServiceObjetosPrestados::get_lugares_entrega_objetos_prestados($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionobjetosprestadosadministrador','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getlistasociosobjetosprestados":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDCategoriaObjeto = SIMNet::req("IDCategoriaObjeto");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceObjetosPrestados::get_lista_socios_objetos_prestados($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto, $Tag);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionobjetosprestadosadministrador','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setobjetoprestado":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Cantidad = $_POST["Cantidad"];
        $IDSocioPrestamo = $_POST["IDSocioPrestamo"];
        $IDLugarEntrega = $_POST["IDLugarEntrega"];
        $IDCategoriaObjeto = $_POST["IDCategoriaObjeto"];



        $respuesta = SIMWebServiceObjetosPrestados::set_objeto_prestado($IDClub, $IDSocio, $IDUsuario, $Cantidad, $IDSocioPrestamo, $IDLugarEntrega, $IDCategoriaObjeto);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setobjetoprestado','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getobjetosprestadospendientesadministrador":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDCategoriaObjeto = SIMNet::req("IDCategoriaObjeto");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceObjetosPrestados::get_objetos_prestados_pendientes_administrador($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto, $Tag);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionobjetosprestadosadministrador','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getobjetosprestadosentregadosadministrador":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDClub = SIMNet::req("IDClub");
        $IDCategoriaObjeto = SIMNet::req("IDCategoriaObjeto");
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceObjetosPrestados::get_objetos_prestados_entregados_administrador($IDClub, $IDSocio, $IDUsuario, $IDCategoriaObjeto, $Tag);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionobjetosprestadosadministrador','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setdevolucionobjetoprestado":
        require LIBDIR . "SIMWebServiceObjetosPrestados.inc.php";
        $IDClub = $_POST["IDClub"];
        $IDSocio = $_POST["IDSocio"];
        $IDUsuario = $_POST["IDUsuario"];
        $Cantidad = $_POST["Cantidad"];
        $IDCategoriaObjeto = $_POST["IDCategoriaObjeto"];
        $IDObjetoPrestamo = $_POST["IDObjetoPrestamo"];
        //$IDSocioPrestamo = $_POST["IDSocioPrestamo"];
        //$IDLugarEntrega = $_POST["IDLugarEntrega"];



        $respuesta = SIMWebServiceObjetosPrestados::set_devolucion_objeto_prestado($IDClub, $IDSocio, $IDUsuario, $Cantidad, $IDCategoriaObjeto, $IDObjetoPrestamo);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setobjetoprestado','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //fin Objetos prestados

        //inicio clima
    case "getconfiguracionclima":
        require LIBDIR . "SIMWebServiceClima.inc.php";

        $IDClub = SIMNet::req("IDClub");

        $respuesta = SIMWebServiceClima::get_configuracion_clima($IDClub);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionclima','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //fin clima

        //inicio entrega kits
    case "getconfiguracionkitsdeportivos":
        require LIBDIR . "SIMWebServiceEntregaKits.inc.php";



        $respuesta = SIMWebServiceEntregaKits::get_configuracion_kits_deportivos($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionclima','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "buscarkitdeportivodocumento":
        require LIBDIR . "SIMWebServiceEntregaKits.inc.php";
        $Documento = SIMNet::req("Documento");
        $IDCarrera = SIMNet::req("IDCarrera");


        $respuesta = SIMWebServiceEntregaKits::buscar_kit_deportivo_documento($IDClub, $IDSocio, $IDUsuario, $Documento, $IDCarrera);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','buscarkitdeportivodocumento','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //buscar por qr
    case "buscarkitdeportivodocumentoqr":
        require LIBDIR . "SIMWebServiceEntregaKits.inc.php";

        $QR = SIMNet::req("QR");


        $respuesta = SIMWebServiceEntregaKits::buscar_kit_deportivo_documento_qr($IDClub, $IDSocio, $IDUsuario, $QR);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','buscarkitdeportivodocumento','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getcarreraskitsdeportivos":
        require LIBDIR . "SIMWebServiceEntregaKits.inc.php";
        $Documento = SIMNet::req("Documento");


        $respuesta = SIMWebServiceEntregaKits::get_carreras_kits_deportivos($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionclima','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getkitsdeportivos":
        require LIBDIR . "SIMWebServiceEntregaKits.inc.php";
        $Documento = SIMNet::req("Documento");
        $IDCarrera  = SIMNet::req("IDCarrera");


        $respuesta = SIMWebServiceEntregaKits::get_kits_deportivos($IDClub, $IDSocio, $IDUsuario, $Documento, $IDCarrera);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionclima','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getkitdeportivodetalle":
        require LIBDIR . "SIMWebServiceEntregaKits.inc.php";

        $IDKit  = SIMNet::req("IDKit");


        $respuesta = SIMWebServiceEntregaKits::get_kit_deportivo_detalle($IDClub, $IDSocio, $IDUsuario, $IDKit);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getkitdeportivodetalle','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setkitusuario":
        require LIBDIR . "SIMWebServiceEntregaKits.inc.php";

        $Archivo = $_POST["Imagen1"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);



        $Documento  = $_POST["Documento"];
        $Nombre  = $_POST["Nombre"];
        $CamposKits  = $_POST["CamposKits"];
        $IDKit  = $_POST["IDKit"];


        $respuesta = SIMWebServiceEntregaKits::set_kit_usuario($IDClub, $IDSocio, $IDUsuario, $Documento, $Nombre, $CamposKits, $Archivo, $_FILES, $IDKit);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //  $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setkitusuario','" . json_encode($_FILES) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //fin entrega kits

        //INICIO PERFILES INFINITOS
    case "getconfiguracionregistrosdinamicos":
        require LIBDIR . "SIMWebServicePerfilesInfinito.inc.php";

        $respuesta = SIMWebServicePerfilesInfinito::get_configuracion_registros_dinamicos($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionregistrosdinamicos','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gettiposformularioregistrosdinamicos":
        require LIBDIR . "SIMWebServicePerfilesInfinito.inc.php";

        $respuesta = SIMWebServicePerfilesInfinito::get_tipos_formulario_registros_dinamicos($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','gettiposformularioregistrosdinamicos','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getformularioregistrosdinamicos":
        require LIBDIR . "SIMWebServicePerfilesInfinito.inc.php";

        $IDTipoFormulario  = SIMNet::req("IDTipoFormulario");


        $respuesta = SIMWebServicePerfilesInfinito::get_formulario_registros_dinamicos($IDClub, $IDSocio, $IDUsuario, $IDTipoFormulario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getformularioregistrosdinamicos','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getmisregistrosdinamicos":
        require LIBDIR . "SIMWebServicePerfilesInfinito.inc.php";
        $IDTipoFormulario  = SIMNet::req("IDTipoFormulario");


        $respuesta = SIMWebServicePerfilesInfinito::get_mis_registros_dinamicos($IDClub, $IDSocio, $IDUsuario, $IDTipoFormulario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getmisregistrosdinamicos','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "getdetalleregistrodinamico":
        require LIBDIR . "SIMWebServicePerfilesInfinito.inc.php";
        $IDRegistroDinamico  = SIMNet::req("IDRegistroDinamico");

        $respuesta = SIMWebServicePerfilesInfinito::get_detalle_registro_dinamico($IDClub, $IDSocio, $IDUsuario, $IDRegistroDinamico);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getdetalleregistrodinamico','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setregistrodinamico":
        require LIBDIR . "SIMWebServicePerfilesInfinito.inc.php";
        $Archivo = $_POST["Imagen1"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);


        $IDTipoFormulario = $_POST["IDTipoFormulario"];
        $CamposFormulario = $_POST["CamposFormulario"];

        $respuesta = SIMWebServicePerfilesInfinito::set_registro_dinamico($IDClub, $IDSocio, $IDUsuario, $IDTipoFormulario, $CamposFormulario, $Archivo, $_FILES);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setregistrodinamico','" . json_encode($_FILES) . "','" . json_encode($respuesta) . "')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setactualizaregistrodinamico":
        require LIBDIR . "SIMWebServicePerfilesInfinito.inc.php";
        $Archivo = $_POST["Imagen1"];
        if (count($_FILES) > 0 && $_POST["Dispositivo"] == "Android")
            $_POST = array_map('utf8_encode', $_POST);

        $IDRegistroDinamico = $_POST["IDRegistroDinamico"];
        $CamposFormulario = $_POST["CamposFormulario"];

        $respuesta = SIMWebServicePerfilesInfinito::set_actualiza_registro_dinamico($IDClub, $IDSocio, $IDUsuario, $IDRegistroDinamico, $CamposFormulario, $Archivo, $_FILES);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setactualizaregistrodinamico','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        //$sql_log_servicio2 = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setactualizaregistrodinamicoFILES','" . json_encode($_FILES) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "eliminarregistrodinamico":
        require LIBDIR . "SIMWebServicePerfilesInfinito.inc.php";

        $IDRegistroDinamico = $_POST["IDRegistroDinamico"];


        $respuesta = SIMWebServicePerfilesInfinito::eliminar_registro_dinamico($IDClub, $IDSocio, $IDUsuario, $IDRegistroDinamico);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','eliminarregistrodinamico','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN PERFILES INFINITOS

        //INICIO NOTIFICACION POR GRUPOS NOTICIAS

    case "getconfiguracionpreferenciasusuario":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";



        $respuesta = SIMWebServiceNoticias::get_configuracion_preferencias_usuario($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionpreferenciasusuario','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "getgruposdenoticiaspreferencias":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";



        $respuesta = SIMWebServiceNoticias::get_grupos_de_noticias_preferencias($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        // $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getconfiguracionpreferenciasusuario','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "setgruposnoticiaspreferencias":
        require LIBDIR . "SIMWebServiceNoticias.inc.php";


        $GruposNoticias  = $_POST["GruposNoticias"];


        $respuesta = SIMWebServiceNoticias::set_grupos_noticias_preferencias($IDClub, $IDSocio, $IDUsuario, $GruposNoticias);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','setgruposnoticiaspreferencias','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;
        //FIN NOTIFICACION POR GRUPOS NOTICIAS

        //INICIO QR DINAMICO

    case "getcodigoQRcarnetdinamico":
        require LIBDIR . "SIMWebServiceQrDinamico.inc.php";
        $IDSocioCarnet  = SIMNet::req("IDSocioCarnet");


        $respuesta = SIMWebServiceQrDinamico::get_codigo_QR_carnet_dinamico($IDClub, $IDSocio, $IDUsuario, $IDSocioCarnet);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','getcodigoQRcarnetdinamico','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "activarcarnetseguridadsocio":
        require LIBDIR . "SIMWebServiceQrDinamico.inc.php";
        $UID = $_POST["UID"];
        $Modelo = $_POST["Modelo"];
        $CorreoElectronico = $_POST["CorreoElectronico"];


        $respuesta = SIMWebServiceQrDinamico::activar_carnet_seguridad_socio($IDClub, $IDSocio, $IDUsuario, $UID, $Modelo, $CorreoElectronico);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','activarcarnetseguridadsocio','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "solicitarotpcarnetseguridadsocio":
        require LIBDIR . "SIMWebServiceQrDinamico.inc.php";



        $respuesta = SIMWebServiceQrDinamico::solicitar_otp_carnet_seguridad_socio($IDClub, $IDSocio, $IDUsuario);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','solicitarotpcarnetseguridadsocio','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "verificarotpcarnetseguridadsocio":
        require LIBDIR . "SIMWebServiceQrDinamico.inc.php";
        $Codigo = $_POST["Codigo"];


        $respuesta = SIMWebServiceQrDinamico::verificar_otp_carnet_seguridad_socio($IDClub, $IDSocio, $IDUsuario, $Codigo);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','solicitarotpcarnetseguridadsocio','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "activarcarnetseguridadmiembrofamilia":
        require LIBDIR . "SIMWebServiceQrDinamico.inc.php";
        $IDSocioFamiliar = $_POST["IDSocioFamiliar"];


        $respuesta = SIMWebServiceQrDinamico::activar_carnet_seguridad_miembro_familia($IDClub, $IDSocio, $IDUsuario, $IDSocioFamiliar);

        // SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','solicitarotpcarnetseguridadsocio','" . json_encode($_POST) . "','" . json_encode($respuesta) . "')");
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //FIN QR DINAMICO


    default:
        die(json_encode(array('success' => false, 'message' => 'no action.' . $action, 'response' => '', 'date' => $nowserver)));
        break;
}///end sw
