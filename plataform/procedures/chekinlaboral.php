<?

SIMReg::setFromStructure(array(
    "title" => "CheckinLaboral",
    "table" => "CheckinLaboral",
    "key" => "IDCheckinLaboral",
    "mod" => "CheckinLaboral"
));


$script = "chekinlaboral";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");


//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);




switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            // SABER SI ES UN USUARIO (EMPLEADO) O ES UN SOCIO
            if ($frm["IDUsuario"] > 0) :
                $Campo = "IDUsuario";
                $Valor = $frm["IDUsuario"];
                $Tabla = "Usuario";
            else :
                $Campo = "IDSocio";
                $Valor = $frm["IDSocio"];
                $Tabla = "Socio";
            endif;

            //seleccionamos la hora del socio o usuario que tiene en ese momento definida y la insertamos en la tabla CheckinLaboral
            $sql_horarios = "SELECT HoraInicioLaboral,HoraFinalLaboral FROM $Tabla WHERE " . $Campo . " = '" . $Valor . "' LIMIT 1";
            $r_query_horarios = $dbo->query($sql_horarios);
            $DatosHorario = $dbo->fetchArray($r_query_horarios);
            $DatosHora = $DatosHorario;
            $frm["HoraEntradaEstablecida"] = $DatosHora["HoraInicioLaboral"];
            $frm["HoraSalidaEstablecida"]  = $DatosHora["HoraFinalLaboral"];
            $frm["Estado"]  = '2';
            $frm["UltimoMovimiento"]  = 'S';


            $frm["FechaMovimientoEntrada"] = $frm["FechaEntrada"] . " " . $frm["HoraEntrada"];
            $frm["FechaMovimientoSalida"] = $frm["FechaSalida"] . " " . $frm["HoraSalida"];


            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;


    case "edit":
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
            $frm["FechaMovimientoEntrada"] = $frm["FechaEntrada"] . " " . $frm["HoraEntrada"];
            $frm["FechaMovimientoSalida"] = $frm["FechaSalida"] . " " . $frm["HoraSalida"];
            $frm["Estado"]  = '2';
            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));



            $frm = $dbo->fetchById($table, $key, $id, "array");


            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=detalle&id=" . $_POST["IDPersona"] . "&type=" . $_POST["Type"]);
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;
    case "detalle":
        $view = "views/" . $script . "/listDetalle.php";
        break;

    case "extras":
        $view = "views/" . $script . "/listExtrasDespuesDeTurno.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
