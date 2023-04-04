<?

SIMReg::setFromStructure(array(
    "title" => "ConfiguracionJuegosdeGolf",
    "table" => "ConfiguracionJuegosdeGolf",
    "key" => "IDConfiguracionJuegosdeGolf",
    "mod" => "ConfiguracionJuegosdeGolf"
));


$script = "configuracionjuegosdegolf";

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

            // $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
            if (!empty($_FILES["IconoJugar"]["name"])) {
                $files =  SIMFile::upload($_FILES["IconoJugar"], SERVICIO_DIR, "IMAGE");
                if (empty($files)) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
                    //print_form( $frm , "insert" , "Agregar Registro" );
                    exit;
                }

                $frm["IconoJugar"] = $files[0]["innername"];
                $frm["IconoJugar"] = URLROOT . "file/servicio/" . $files[0]["innername"];




                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
                $ids = SIMNet::reqInt("id");
            }
            if (!empty($_FILES["IconoHandicap"]["name"])) {
                $files =  SIMFile::upload($_FILES["IconoHandicap"], SERVICIO_DIR, "IMAGE");
                if (empty($files)) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
                    //print_form( $frm , "insert" , "Agregar Registro" );
                    exit;
                }

                $frm["IconoHandicap"] = $files[0]["innername"];
                $frm["IconoHandicap"] = URLROOT . "file/servicio/" . $files[0]["innername"];




                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
                $ids = SIMNet::reqInt("id");
            }

            if (!empty($_FILES["IconoJuegos"]["name"])) {
                $files =  SIMFile::upload($_FILES["IconoJuegos"], SERVICIO_DIR, "IMAGE");
                if (empty($files)) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
                    //print_form( $frm , "insert" , "Agregar Registro" );
                    exit;
                }

                $frm["IconoJuegos"] = $files[0]["innername"];
                $frm["IconoJuegos"] = URLROOT . "file/servicio/" . $files[0]["innername"];




                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
                $ids = SIMNet::reqInt("id");
            }

            if (!empty($_FILES["IconoGrupos"]["name"])) {
                $files =  SIMFile::upload($_FILES["IconoGrupos"], SERVICIO_DIR, "IMAGE");
                if (empty($files)) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga. Verifique que el archivo no contenga errores y que el tipo de archivo sea permitido.", "error");
                    //print_form( $frm , "insert" , "Agregar Registro" );
                    exit;
                }

                $frm["IconoGrupos"] = $files[0]["innername"];
                $frm["IconoGrupos"] = URLROOT . "file/servicio/" . $files[0]["innername"];




                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
                $ids = SIMNet::reqInt("id");
            }

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;
    case 'delfoto':

        $foto = $_REQUEST['foto'];
        $campo = $_REQUEST['campo'];
        $id = $_REQUEST['id'];
        $filedelete = SERVICIO_DIR . $foto;
        unlink($filedelete);
        $borrar = "UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ";
        $dbo->query($borrar);

        SIMHTML::jsAlert("Imagen Eliminada Correctamente");
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        break;

    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
