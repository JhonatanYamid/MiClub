<?php
require("general.php");
switch ($action) {
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
    break;    

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
        require LIBDIR . "SIMWebServiceReservas.inc.php";
        $IDUsuario = SIMNet::req("IDUsuario");
        $IDSocio = SIMNet::req("IDSocio");
        $IDClub = SIMNet::req("IDClub");
        $respuesta = SIMWebService::get_configuracion_juegos_de_golf($IDClub, $IDUsuario, $IDSocio);
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

    //Reservas del socio ultimas 15
    case "getreservasocio":
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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

    //Guardar invitados a una reserva
    case "setinvitadoservicio":
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
    case "getbeneficiarios":
        require LIBDIR . "SIMWebService.inc.php";
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

    //Enviar la disponibilidad de un elemento en una fecha, marca las horas con disponible si o no
    case "getdisponiblidadelemento":
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
        $IDServicio = SIMNet::req("IDServicio");
        $Fecha = SIMNet::req("Fecha");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_hora_disponible($IDClub, $IDServicio, $Fecha, $Hora, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
    break;


    //Enviar la disponibilidad de los elementos en una fecha hora especifica
    case "setseparareserva":
        require LIBDIR . "SIMWebService.inc.php";
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

    case "setliberareserva":
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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

    //Enviar Campos de golf disponibles
    case "getdisponibilidadfecha":
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
        $IDCampo = SIMNet::req("IDCampo");
        $Fecha = SIMNet::req("Fecha");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_disponibilidad_campo($IDClub, $IDCampo, $Fecha, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
    break;



        //Eliminar Reserva
    case "eliminareservageneral":
        require LIBDIR . "SIMWebService.inc.php";
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


    //Reservas asociadas
    case "getreservaasociada":
        require LIBDIR . "SIMWebService.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $IDReserva = SIMNet::req("IDReserva");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_reserva_asociada($IDClub, $IDSocio, $IDReserva, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
    break;

        //Verificar Grupos golf
    case "verificarsociogrupo":
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
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
        require LIBDIR . "SIMWebService.inc.php";
        $IDTipoModalidadEsqui = SIMNet::req("IDTipoModalidadEsqui");
        $IDElemento = SIMNet::req("IDElemento");
        $IDClubAsociado = SIMNet::req("IDClubAsociado");
        $respuesta = SIMWebService::get_modalidades($IDClub, $IDTipoModalidadEsqui, $IDElemento, $IDClubAsociado);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
    break;

    //Guardar Reserva Golf
    case "setreservagolf":
        require LIBDIR . "SIMWebService.inc.php";
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
    
}///end sw
