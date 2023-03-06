<?

SIMReg::setFromStructure(array(
    "title" => "Tiempos",
    "table" => "Tiempos",
    "key" => "IDTiempos",
    "mod" => "Tiempos"
));


$script = "tiempos";

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

            //UPLOAD de imagenes
            if (isset($_FILES)) {

                $files =  SIMFile::upload($_FILES["Icono"], BANNERAPP_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Icono"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                $frm["Icono"] = $files[0]["innername"];
            } //end if

            foreach ($frm["AplicaPara"] as $Aplica_seleccion) :
                $array_aplica[] = $Aplica_seleccion;
            endforeach;

            if (count($array_aplica) > 0) :
                $id_aplica = implode("|", $array_aplica) . "|";
            endif;
            $frm["AplicaPara"] = $id_aplica;

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

        $sql_preguntas = " SELECT P.IDPreguntaTiempos,P.EtiquetaCampo,P.Orden
					FROM Tiempos T
					JOIN PreguntaTiempos P ON P.IDTiempos = T.IDTiempos
					WHERE T.IDClub = " . SIMUser::get("club") . "
					AND T.IDTiempos = " . SIMNet::reqInt("id") . "
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

        $result = $dbo->query($sql_preguntas);
        $numPregunta = 1;
        $array_preguntas = array();
        $array_NoPregunta = array();
        while ($rowPregunta = $dbo->fetchArray($result)) {

            $array_preguntas[$rowPregunta["IDPreguntaTiempos"]] = str_replace(",", "/", $rowPregunta["EtiquetaCampo"]);
            $array_NoPregunta[$rowPregunta["IDPreguntaTiempos"]] = "_" . $rowPregunta["IDPreguntaTiempos"];
        }

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            //UPLOAD de imagenes
            if (isset($_FILES)) {


                $files =  SIMFile::upload($_FILES["Icono"], BANNERAPP_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Icono"]["name"]))
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

                $frm["Icono"] = $files[0]["innername"];
            } //end if


            foreach ($frm["AplicaPara"] as $Aplica_seleccion) :
                $array_aplica[] = $Aplica_seleccion;
            endforeach;

            if (count($array_aplica) > 0) :
                $id_aplica = implode("|", $array_aplica) . "|";
            endif;
            $frm["AplicaPara"] = $id_aplica;


            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;

    case "delfoto":
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $id = $_GET['id'];
        $filedelete = SERVICIO_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        break;
    case "InsertarPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $id = $dbo->insert($frm, "PreguntaTiempos", "IDPreguntaTiempos");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDTiempos]);
        exit;
        break;
    case "ModificaPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $dbo->update($frm, "PreguntaTiempos", "IDPreguntaTiempos", $frm["IDPreguntaTiempos"]);
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDTiempos]);
        exit;
        break;
    case "EliminaPregunta":

        $id = $dbo->query("DELETE FROM PreguntaTiempos WHERE IDPreguntaTiempos   = '" . $_GET["IDPreguntaTiempos"] . "' LIMIT 1");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitosa', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" .  $_GET["IDTiempos"]);
        exit;
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
