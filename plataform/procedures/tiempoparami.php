<?

SIMReg::setFromStructure(array(
    "title" => "TiempoParami",
    "table" => "AuxiliosInfinito",
    "key" => "IDAuxiliosInfinito",
    "IDModulo" => "145",
    "Version" => "1",
    "mod" => "Auxilios"
));


$script = "tiempoparami";

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
            $frm["IDModulo"] = $modulo;
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

        $sql_preguntas = " SELECT P.IDPreguntaAuxiliosInfinito,P.EtiquetaCampo,P.Orden
					FROM AuxiliosInfinito A
					JOIN PreguntaAuxiliosInfinito P ON P.IAuxiliosInfinito = A.IDAuxiliosInfinito
					WHERE A.IDClub = " . SIMUser::get("club") . "
					AND A.IDAuxiliosInfinito = " . SIMNet::reqInt("id") . "
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

        $result = $dbo->query($sql_preguntas);
        $numPregunta = 1;
        $array_preguntas = array();
        $array_NoPregunta = array();
        while ($rowPregunta = $dbo->fetchArray($result)) {

            $array_preguntas[$rowPregunta["IDPreguntaAuxiliosInfinitos"]] = str_replace(",", "/", $rowPregunta["EtiquetaCampo"]);
            $array_NoPregunta[$rowPregunta["IDPreguntaAuxiliosInfinito"]] = "_" . $rowPregunta["IDPreguntaAuxiliosInfinito"];
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
        $frm["IDModulo"] = $modulo;
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $id = $dbo->insert($frm, "PreguntaAuxiliosInfinito", "IDPreguntaAuxiliosInfinito");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDAuxiliosInfinito]);
        exit;
        break;
    case "ModificaPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $dbo->update($frm, "PreguntaAuxiliosInfinito", "IDPreguntaAuxiliosInfinito", $frm["IDPreguntaAuxiliosInfinito"]);
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDAuxiliosInfinito]);
        exit;
        break;
    case "EliminaPregunta":
        $id = $dbo->query("DELETE FROM PreguntaAuxiliosInfinito WHERE IDPreguntaAuxiliosInfinito  = '" . $_GET["IDPreguntaAuxilios"] . "' LIMIT 1");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $_GET["IDAuxilios"]);
        exit;
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
