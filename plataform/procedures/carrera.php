<?

SIMReg::setFromStructure(array(
    "title" => "Carrera",
    "titleB" => "Carreras",
    "table" => "Carrera",
    "key" => "IDCarrera",
    "mod" => "Carrera"
));

$script = "carrera";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");
$action = SIMNet::req("action");

$IDClub = SIMUser::get("club");

//Verificar permisos
//SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

switch ($action) {

    case "add":
        $view = "views/" . $script . "/list.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=add");

        } else
            exit;

        break;


    case "edit":
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/list.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);						

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=add");
            
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    default:
        $view = "views/" . $script . "/list.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
    break;
} // End switch

if (empty($view)){
    $view = "views/" . $script . "/list.php";
    $newmode = "insert";
    $titulo_accion = "Crear";
}

?>