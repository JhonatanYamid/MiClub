<?
SIMReg::setFromStructure(array(
    "title" => "Solicitudes",
    "table" => "AuxiliosInfinitoSolicitud",
    "key" => "IDAuxiliosInfinitoSolicitud",
    "IDModulo" => "145",
    "mod" => "AuxiliosInfinito1"
));


$script = "tiempoparamisolicitud";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");
$modulo = SIMReg::get("IDModulo");


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

            $frm["IDModulo"] = $modulo;
            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);

            SIMHTML::jsAlert("Registro Guardado Correctamente");
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
        include('../admin/lib/SIMWebServiceAuxiliosInfinito.inc.php');

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
            $frm['IDUsuarioAprueba'] = SIMUser::get('IDUsuario');
            if ($frm['IDEstado'] == 1 || $frm['IDEstado'] == 4) {
                $Aprueba = 1;
                SIMHTML::jsAlert("Es necesario actualizar el estado de la solicitud");
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
                exit;
            } elseif ($frm['IDEstado'] == 3) {
                $Aprueba = 'S';
            } elseif ($frm['IDEstado'] == 2) {
                $Aprueba = 'N';
            }
            $Comentario = (isset($frm['Comentarios'])) ? $frm['Comentarios'] : $frm['ComentarioAprobador'];
            $Response = SIMWebServiceAuxiliosInfinito::set_respuesta_solicitud_auxilio_infinito(SIMUser::get("club"), '', $frm['IDUsuarioAprueba'], $frm['ID'], $Aprueba, $frm['ID'], $Comentario, 157);

            if ($Response['success']) {
                SIMHTML::jsAlert("Registro Guardado Correctamente");
            } else {
                SIMHTML::jsAlert("Se ha producido un error");
            }
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
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
