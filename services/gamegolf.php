<?php
switch ($action) {
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
}///end sw
?>