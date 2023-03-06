<?

SIMReg::setFromStructure(array(
    "title" => "Compensaciones",
    "table" => "LaboralCompensatorio",
    "key" => "IDLaboralCompensatorio",
    "mod" => "Laboral"
));


$script = "laboralcompensaciones";

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

            //Se obtiene el IDUsario o IDSocio
            $tipoSolicitante = $frm["TipoSolicitante"];
            if ($tipoSolicitante == "Usuario") {
                unset($frm["IDSocio"]);
            } else if ($tipoSolicitante == "Socio") {
                unset($frm["IDUsuario"]);
            }

            $frm["IDEstado"] = 1;
            $files = SIMFile::upload($_FILES["Archivo"], LABORAL_DIR);
            if (empty($files) && !empty($_FILES["Archivo"]["name"])) {
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
            }
          
            $frm["Archivo"] = $files[0]["innername"];

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

            //Si el estado es aprobado se agrega fecha de aprobación
            $estadosLaboral =  SIMResources::$estado_laboral;
            $estado = $estadosLaboral[$frm["IDEstado"]];
            if ($estado == "Aprobado") {
                $frm["FechaAprobacion"] = date("Y-m-d");
                $frm["IDUsuarioAutoriza"] = SIMUser::get("IDUsuario");
            }

            $files =  SIMFile::upload($_FILES["Archivo"], LABORAL_DIR);
            if (empty($files) && !empty($_FILES["Archivo"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Archivo"] = $files[0]["innername"];

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
        break;
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
