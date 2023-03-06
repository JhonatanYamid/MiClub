<?

SIMReg::setFromStructure(array(
    "title" => "Acceso Evento",
    "table" => "SocioInvitado",
    "key" => "IDSocioInvitado",
    "mod" => "SocioInvitado",

));

$script = "accesoevento";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
//  SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

require_once LIBDIR . "SIMWebServiceUsuarios.inc.php";
require_once LIBDIR . "SIMWebServiceReservas.inc.php";

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

$IngresoPermitidoLag = 4;


switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;
    case "search":

        $qryString = str_replace(".", "", SIMNet::req("qryString"));
        $qryString = str_replace(",", "", $qryString);
        $arr_Qr = explode("|", $qryString);
        $IDEvento = $arr_Qr[0];
        $ID = $arr_Qr[1];
        $FechaActual = date("Y-m-d");
        // Valido si la busqueda es por Qr o por numero de documento
        $EsSocio = "S";
        if (isset($ID)) {
            $TipoBusqueda = "Qr";
            $whereSocio = " AND EventoRegistro.IDEvento = $IDEvento AND EventoRegistro.IDSocio = '$ID'";
            $whereNoSocio = " AND EventoRegistro.IDNoSocios = '$ID'";
        } else {
            $TipoBusqueda = "Documento";
            $whereSocio = " AND Socio.NumeroDocumento = '$qryString'";
            $whereNoSocio = " AND NoSocios.NumeroDocumento = '$qryString'";
        }

        //Fin  Valido si la busqueda es por Qr o por numero de documento
        //realizo busquedas
        $sql_soc = "SELECT Socio.IDSocio,Evento.IDEvento FROM Socio,EventoRegistro,Evento Where Socio.IDSocio=EventoRegistro.IDSocio AND EventoRegistro.IDEvento=Evento.IDEvento $whereSocio and EventoRegistro.IDClub = " . SIMUser::get('club') . " and Socio.IDEstadoSocio = 1 AND Evento.FechaEvento <= '{$FechaActual}' AND Evento.FechaFinEvento >= '{$FechaActual}' Limit 1";
        $q_soc = $dbo->query($sql_soc);
        if ($dbo->rows($q_soc) > 0) {
            $EsSocio = "S";
            $datos_socio = $dbo->assoc($q_soc);
            $ID = $datos_socio['IDSocio'];
            if (empty($datos_socio) && $TipoBusqueda == 'Qr') {
                $IDEvento = $IDEvento;
            } else {
                $IDEvento = $datos_socio['IDEvento'];
            }
        } else {
            $EsSocio = "N";
            $sql_NoSoc = "SELECT NoSocios.IDNoSocios,Evento.IDEvento FROM NoSocios,EventoRegistro,Evento Where NoSocios.IDNoSocios=EventoRegistro.IDNoSocios AND EventoRegistro.IDEvento=Evento.IDEvento $whereNoSocio and EventoRegistro.IDClub = " . SIMUser::get('club') . " AND Evento.FechaEvento <= '{$FechaActual}' AND Evento.FechaFinEvento >= '{$FechaActual}' Limit 1";
            $q_noSocio = $dbo->query($sql_NoSoc);
            $datos_socio = $dbo->assoc($q_noSocio);
            $ID = $datos_socio['IDNoSocios'];
            if (empty($datos_socio) && $TipoBusqueda == 'Qr') {
                $IDEvento = $IDEvento;
            } else {
                $IDEvento = $datos_socio['IDEvento'];
            }
        }
        // Buscamos el evento y validamos la fecha
        $sql_Evento = "SELECT E.* From Evento E WHERE IDEvento = $IDEvento AND IDClub = " . SIMUser::get('club') . " AND  Publicar = 'S' AND FechaEvento <= '{$FechaActual}' AND FechaFinEvento >= '{$FechaActual}'";
        //Fin Buscamos el evento y validamos la fecha

        $q_Evento = $dbo->query($sql_Evento);
        if ($dbo->rows($q_Evento) == 0) {
            $view = "views/" . $script . "/list.php";
            $mensaje = "El evento no ha sido encontrado o no ha iniciado.";
            $total_resultados = 0;
            break;
        }
        $Evento = $dbo->assoc($q_Evento);


        // Validamos si el invitado ya ingresó en el día
        $sql_LogAccesoDiario = "SELECT IDLogAcceso FROM LogAccesoDiario WHERE IDClub = " . SIMUser::get('club') . " AND IDInvitacion = $ID AND (Tipo = 'InvitadoEvento' OR Tipo = 'Socio') AND Entrada = 'S' ";
        $q_LogAccesoDiario = $dbo->query($sql_LogAccesoDiario);
        if ($dbo->rows($q_LogAccesoDiario) >= 1) {
            $AlertaIngreso = "El invitado ya ha ingresado al evento.";
        }

        // Fin Validamos si el invitado ya ingresó en el día

        //BUSQUEDA INVITADOS EVENTO
        if ($EsSocio == "S") {
            $Socio = $dbo->fetchAll("Socio", "IDSocio = $ID", "array");
            $Nombre = $Socio['Nombre'] . " " . $Socio['Apellido'];
            $NumeroDocumento = $Socio['NumeroDocumento'];
            $Accion = $Socio['Accion'];
            $FechaNacimiento = $Socio['FechaNacimiento'];
            $CorreoElectronico = $Socio['CorreoElectronico'];
            $Celular = $Socio['Telefono'] . " / " . $Socio['Celular'];
            $modulo = "Socio";
        } else {
            $NoSocio = $dbo->fetchAll("NoSocios", "IDNoSocios = $ID", "array");
            $Nombre = $NoSocio['Nombre'];
            $NumeroDocumento = $NoSocio['NumeroDocumento'];
            $Accion = "";
            $FechaNacimiento = $NoSocio['FechaNacimiento'];
            $CorreoElectronico = $NoSocio['CorreoElectronico'];
            $Celular = $NoSocio['Celular'];
            $modulo = "InvitadoEvento";
        }

        if (!empty($Socio) || !empty($NoSocio)) {
            $view = "views/" . $script . "/list.php";
            $total_resultados = 1;
        } else {
            $view = "views/" . $script . "/list.php";
            $mensaje = "El C&oacute;digo Qr no pertenece a eventos programados para hoy.";
            $total_resultados = 0;

            break;
        }
        //FIN BUSQUEDA INVITADOS EVENTO


        break;

    case "imprimir-carnet":
        $view = "views/" . $script . "/carnetselector.php";
        break;

    default:
        $view = "views/" . $script . "/list.php";
} // End switch


if (empty($view))
    $view = "views/" . $script . "/form.php";
