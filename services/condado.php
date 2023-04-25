<?php
switch ($action) {
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
}///end sw
?>