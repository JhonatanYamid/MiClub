<?php
//Verificar Acciones
switch ($action) {

    case "getconfiguracionreservahorario":
        require LIBDIR . "SIMWebService.inc.php";
        require LIBDIR . "SIMWebServiceNuba.inc.php";
        $IDClub = SIMNet::req("IDClub");
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::get_configuracion_reservas($IDClub, $IDModulo);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    case "gethijosreservahorario":
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
        require LIBDIR . "SIMWebServiceNuba.inc.php";
        $IDClub = SIMNet::req("IDClub");
        $IDSocio = SIMNet::req("IDSocio");
        $IDModulo = SIMNet::req("IDModulo");

        $respuesta = SIMWebServiceNuba::get_mis_reservas_horario($IDClub, $IDSocio, $IDModulo);
        SIMLog::insert_app($action, $IDClub, $_GET, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

    

    case "setactualizaredades":
        require LIBDIR . "SIMWebService.inc.php";
        require LIBDIR . "SIMWebServiceNuba.inc.php";

        $IDClub = $_POST["IDClub"];

        $respuesta = SIMWebServiceNuba::actualizar_edades($IDClub);
        //SIMLog::insert_app($action, $IDClub, $_POST, $respuesta);

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


    case "setverificarhorario":
        require LIBDIR . "SIMWebService.inc.php";
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
}///end sw


