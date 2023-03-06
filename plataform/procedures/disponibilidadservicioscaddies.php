<?

SIMReg::setFromStructure(array(
    "title" => "Disponibilidad Servicios Caddies",
    "table" => "DisponibilidadServiciosCaddies",
    "key" => "IDDisponibilidadServiciosCaddies",
    "mod" => "Socio"
));


$script = "disponibilidadservicioscaddies";

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

            //dias de la semana
            foreach ($frm["IDDia"] as $Dia_seleccion) :
                $array_dia[] = $Dia_seleccion;
            endforeach;

            if (count($array_dia) > 0) :
                $id_dia = implode("|", $array_dia) . "|";
            endif;
            $frm["Dias"] = $id_dia;


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

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            //dias de la semana
            foreach ($frm["IDDia"] as $Dia_seleccion) :
                $array_dia[] = $Dia_seleccion;
            endforeach;

            if (count($array_dia) > 0) :
                $id_dia = implode("|", $array_dia) . "|";
            endif;
            $frm["Dias"] = $id_dia;

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert("Registro Guardado Correctamente");
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
