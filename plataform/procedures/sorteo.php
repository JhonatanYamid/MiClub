<?

SIMReg::setFromStructure(array(
    "title" => " ",
    "table" => "SorteosServicios",
    // "key" => "IDServicio",
    "mod" => "Socio"
));


$script = "sorteo";

//extraemos las variables
$table = SIMReg::get("table");
//$key = SIMReg::get("key");
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

            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php?action=add");
        } else
            exit;

        break;


    case "edit":
        $key = "IDServicio";
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("ids"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            /*  print_r($_POST);
            exit;
 */
            //Consulto si existe el servicio en la tabla SorteosServicios si existe lo actualizo si no lo inserto
            $sql = "Select IDServicio FROM  SorteosServicios WHERE IDServicio='" . $frm["IDServicio"] . "'";


            $query = $dbo->query($sql);
            $datos = $dbo->fetchArray($query);
            $SorteoServicio = $datos;
            if ($SorteoServicio["IDServicio"] == "") {
                $key = "IDSorteosServicios";

                //UPLOAD de imagenes
                if (isset($_FILES)) {

                    $files =  SIMFile::upload($_FILES["Icono"], SERVICIO_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES["Icono"]["name"]))
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    $frm["Icono"] = $files[0]["innername"];
                } //end if	
                //insertamos los datos
                $id = $dbo->insert($frm, $table, $key);

                SIMHTML::jsAlert("Registro Guardado Correctamente");
                SIMHTML::jsRedirect($script . ".php?action=edit&ids=" . $frm["IDServicio"]);
            } else if ($SorteoServicio["IDServicio"] != "") {
                // $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
                $key = "IDServicio";
                //UPLOAD de imagenes
                if (isset($_FILES)) {


                    $files =  SIMFile::upload($_FILES["Icono"], SERVICIO_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES["Icono"]["name"]))
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

                    $frm["Icono"] = $files[0]["innername"];
                } //end if			


                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("ids"));

                $frm = $dbo->fetchById($table, $key, $id, "array");



                SIMHTML::jsAlert("Registro Guardado Correctamente");
                SIMHTML::jsRedirect($script . ".php?action=edit&ids=" . $frm["IDServicio"]);
            }
        } else
            exit;

        break;

    case "delfoto":
        $key = "IDServicio";
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $id = $_GET['ids'];
        $filedelete = SERVICIO_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
        SIMHTML::jsAlert("Imagen Eliminada Correctamente");
        // SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");
        SIMHTML::jsRedirect($script . ".php?action=edit&ids=" . $id);

        break;





    case "search":
        $view = "views/" . $script . "/list.php";
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
