<?php
//Verificar Acciones
switch ($action) {
    /******************************************************
	/*SERVICIOS FEDEGOLF
	/******************************************************/

    case "getconfiguracionfedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $respuesta = SIMWebServiceFedegolf::get_configuracion_fedegolf($IDClub);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;


        //Clubes Fedegolf
    case "getclubfedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $respuesta = SIMWebServiceFedegolf::get_clubes();
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Canchas Fedegolf segun club
    case "getcanchafedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $IDClubFedegolf = SIMNet::req("IDClubFedegolf");
        $respuesta = SIMWebServiceFedegolf::get_canchas($IDClubFedegolf);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Marcas Fedegolf segun club - cancha
    case "getmarcafedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $IDClubFedegolf = SIMNet::req("IDClubFedegolf");
        $IDCancha = SIMNet::req("IDCancha");
        $Codigo = SIMNet::req("Codigo");
        $respuesta = SIMWebServiceFedegolf::get_marcas($IDClubFedegolf, $IDCancha, $Codigo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Marcas Fedegolf segun club - cancha
    case "gethandicapfedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
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
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $Email = SIMNet::req("Email");
        $Pwd = SIMNet::req("Pwd");
        $respuesta = SIMWebServiceFedegolf::get_autenticacion($Email, $Pwd);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //usuario por numero de codigo Fedegolf
    case "getusuariocodigofedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $Codigo = SIMNet::req("Codigo");
        $respuesta = SIMWebServiceFedegolf::get_usuario_codigo($Codigo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Buscar usuario por parametro Fedegolf
    case "getusuarionombrefedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $Tag = SIMNet::req("Tag");
        $respuesta = SIMWebServiceFedegolf::get_usuario_nombre($Tag);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('getusuarionombrefedegolf','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Carne
    case "carnefedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $respuesta = SIMWebServiceFedegolf::carne_fedegolf($IDClub, $IDSocio);
        ////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','carnefedegolf','".json_encode($_GET)."','".json_encode($respuesta)."')");
        //SIMLog::insert_app( $action,$IDClub, $_POST, $respuesta );

        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Lista de juegos
    case "getgamesjugador":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
        $IDSocio = SIMNet::req("IDSocio");
        $Codigo = SIMNet::req("Codigo");
        $respuesta = SIMWebServiceFedegolf::get_games_jugador($IDClub, $IDSocio, $Codigo);
        die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
        exit;
        break;

        //Detalle tarjeta juego
    case "getgamesfedegolf":
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
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
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
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
        require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
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

    
}///end sw
