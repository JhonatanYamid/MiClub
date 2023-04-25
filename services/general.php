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


?>