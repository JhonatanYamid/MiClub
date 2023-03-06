<?
SIMReg::setFromStructure(array(
    "title" => "Ingresos",
    "table" => "Ingresos",
    "key" => "IDIngresos",
    "IDModulo" => "176",
    "mod" => "Ingresos"
));

$script = "ingresos";

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

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            if ($id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"))) {
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
    case "InsertarPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $id = $dbo->insert($frm, "IngresosPreguntas", "IDIngresosPreguntas");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm['IDIngresos']);
        exit;
        break;
    case "ModificaPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $dbo->update($frm, "IngresosPreguntas", "IDIngresosPreguntas", $frm["IDIngresosPreguntas"]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm['IDIngresos']);
        exit;
        break;
    case "EliminaPregunta":
        $id = $dbo->query("DELETE FROM IngresosPreguntas WHERE IDIngresosPreguntas   = '" . $_GET["IDIngresosPreguntas"] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $_GET["id"]);
        exit;
        break;

    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
