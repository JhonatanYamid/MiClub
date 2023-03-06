<?

SIMReg::setFromStructure(array(
    "title" => "DisponibilidiadCaddiesEcaddie",
    "table" => "DisponibilidiadCaddiesEcaddie",
    "key" => "IDDisponibilidiadCaddiesEcaddie",
    "mod" => "Socio"
));


$script = "disponibilidadcaddie";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");
require_once LIBDIR . "SIMWebServiceEcaddie.inc.php";

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


            $DiasSeleccionados = $frm["IDDia"];
            $DiasSeleccionados = json_encode($DiasSeleccionados);
            $IDClub = $frm["IDDia"];
            $IDUsuario = $frm["IDUsuario"];
            $IDClubSeleccionado = $frm["IDClubSeleccion"];
            $TurboCaddieActivado = $frm["TurboCaddieActivado"];



            $HorasSeleccionadas = array();

            for ($i = 1; $i <= 5; $i++) {
                if ($_POST["HoraDesde$i"] > 0 && $_POST["HoraHasta$i"] > 0) {
                    $horaInicial = $_POST["HoraDesde$i"] . ":00";
                    $horaFinal = $_POST["HoraHasta$i"] . ":00";
                    $HorasSeleccionadas[] = array("HoraInicial" => $horaInicial, "HoraFinal" => $horaFinal);
                }
            }
            $HorasSeleccionadas = json_encode($HorasSeleccionadas);


            $respuesta = SIMWebServiceEcaddie::set_agenda_caddie($IDClub, $IDUsuario, $HorasSeleccionadas, $DiasSeleccionados, $IDClubSeleccionado, $TurboCaddieActivado);
            if (!$respuesta['success'])
                SIMHTML::jsAlert($respuesta['message']);
            else
                SIMHTML::jsAlert("Registro Guardado Correctamente");
            //insertamos los datos
            //$id = $dbo->insert($frm, $table, $key);

            // SIMHTML::jsAlert("Registro Guardado Correctamente");
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

            $DiasSeleccionados = $frm["IDDia"];
            $DiasSeleccionados = json_encode($DiasSeleccionados);
            $IDClub = $frm["IDDia"];
            $IDUsuario = $frm["IDUsuario"];
            $IDClubSeleccionado = $frm["IDClubSeleccion"];
            $TurboCaddieActivado = $frm["TurboCaddieActivado"];



            $HorasSeleccionadas = array();

            for ($i = 1; $i <= 5; $i++) {
                if ($_POST["HoraDesde$i"] > 0 && $_POST["HoraHasta$i"] > 0) {
                    $horaInicial = $_POST["HoraDesde$i"] . ":00";
                    $horaFinal = $_POST["HoraHasta$i"] . ":00";
                    $HorasSeleccionadas[] = array("HoraInicial" => $horaInicial, "HoraFinal" => $horaFinal);
                }
            }
            $HorasSeleccionadas = json_encode($HorasSeleccionadas);


            $respuesta = SIMWebServiceEcaddie::set_agenda_caddie($IDClub, $IDUsuario, $HorasSeleccionadas, $DiasSeleccionados, $IDClubSeleccionado, $TurboCaddieActivado);
            if (!$respuesta['success'])
                SIMHTML::jsAlert($respuesta['message']);
            else
                SIMHTML::jsAlert("Registro Guardado Correctamente");

            /* $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert("Registro Guardado Correctamente"); */
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
