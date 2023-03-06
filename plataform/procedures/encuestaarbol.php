<?

SIMReg::setFromStructure(array(
    "title" => "Encuesta",
    "table" => "EncuestaArbol",
    "key" => "IDEncuestaArbol",
    "mod" => "EncuestaArbol"
));


$script = "encuestaarbol";

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
        /*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            $files =  SIMFile::upload($_FILES["Imagen"], BANNERAPP_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["Imagen"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Imagen"] = $files[0]["innername"];



            $files =  SIMFile::upload($_FILES["Adjunto1Documento"], BANNERAPP_DIR, "DOC");
            if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Adjunto1File"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["Adjunto2Documento"], BANNERAPP_DIR, "DOC");
            if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Adjunto2File"] = $files[0]["innername"];


            //insertamos los datos del asistente
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

        $sql_preguntas = " SELECT P.IDPreguntaEncuestaArbol,P.EtiquetaCampo,P.Orden
					FROM EncuestaArbol E
					JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
					WHERE E.IDClub = " . SIMUser::get("club") . "
					AND E.IDEncuestaArbol = " . SIMNet::reqInt("id") . "
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

        $result = $dbo->query($sql_preguntas);
        $numEncuestaArbol = 1;
        $array_preguntas = array();
        $array_NoEncuestaArbol = array();
        while ($rowEncuestaArbol = $dbo->fetchArray($result)) {

            $array_preguntas[$rowEncuestaArbol["IDPreguntaEncuestaArbol"]] = str_replace(",", "/", $rowEncuestaArbol["EtiquetaCampo"]);
            $array_NoEncuestaArbol[$rowEncuestaArbol["IDPreguntaEncuestaArbol"]] = "_" . $rowEncuestaArbol["IDPreguntaEncuestaArbol"];
        }

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);
            $files =  SIMFile::upload($_FILES["Imagen"], BANNERAPP_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["Imagen"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Imagen"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["Adjunto1Documento"], BANNERAPP_DIR, "DOC");
            if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Adjunto1File"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["Adjunto2Documento"], BANNERAPP_DIR, "DOC");
            if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Adjunto2File"] = $files[0]["innername"];



            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"), "");



            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;
        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;

    case "delfoto":
        $campo = $_GET['campo'];
        $doceliminar = BANNERAPP_DIR . $dbo->getFields("Encuesta", "$campo", "IDEncuesta = '" . $_GET[id] . "'");
        unlink($doceliminar);
        $dbo->query("UPDATE Encuesta SET $campo = '' WHERE IDEncuesta = $_GET[id] LIMIT 1 ;");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));

        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
        exit;
        break;

    case "InsertarPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $id = $dbo->insert($frm, "PreguntaEncuestaArbol", "IDPreguntaEncuestaArbol");

        //Guardo opciones de respuesta
        for ($i = 1; $i <= $frm["CantidadOpciones"]; $i++) {
            if (!empty($frm["Respuesta" . $i])) {
                $frm['IDEncuestaArbolPreguntaSiguiente' . $i] = implode('|', $frm['IDEncuestaArbolPreguntaSiguiente' . $i]);
                $sql_insert = "INSERT INTO EncuestaArbolOpcionesRespuesta (IDEncuestaArbolPregunta,IDEncuestaArbolPreguntaSiguiente,Opcion,Orden,Puntos)
														 VALUES('" . $id . "','" . $frm["IDEncuestaArbolPreguntaSiguiente" . $i] . "','" . $frm["Respuesta" . $i] . "','" . $frm["Orden" . $i] . "','" . $frm["Puntos" . $i] . "')";
                $dbo->query($sql_insert);
            }
        }

        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDEncuestaArbol]);
        exit;
        break;

    case "ModificaPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $dbo->update($frm, "PreguntaEncuestaArbol", "IDPreguntaEncuestaArbol", $frm["IDPreguntaEncuestaArbol"]);

        $borro_opciones = "DELETE FROM EncuestaArbolOpcionesRespuesta WHERE IDEncuestaArbolPregunta = '" . $frm["IDPreguntaEncuestaArbol"] . "'";
        $dbo->query($borro_opciones);
        for ($i = 1; $i <= $frm["CantidadOpciones"]; $i++) {
            if (!empty($frm["Respuesta" . $i])) {
                $frm['IDEncuestaArbolPreguntaSiguiente' . $i] = implode('|', $frm['IDEncuestaArbolPreguntaSiguiente' . $i]);
                $sql_insert = "INSERT INTO EncuestaArbolOpcionesRespuesta (IDEncuestaArbolPregunta,IDEncuestaArbolPreguntaSiguiente,Opcion,Orden,Puntos)
																 VALUES('" . $frm["IDPreguntaEncuestaArbol"] . "','" . $frm["IDEncuestaArbolPreguntaSiguiente" . $i] . "','" . $frm["Respuesta" . $i] . "','" . $frm["Orden" . $i] . "','" . $frm["Puntos" . $i] . "')";
                $dbo->query($sql_insert);
            }
        }

        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm[IDEncuestaArbol]);
        exit;
        break;

    case "EliminaPregunta":
        $id = $dbo->query("DELETE FROM PreguntaEncuestaArbol WHERE IDPreguntaEncuestaArbol   = '" . $_GET["IDPreguntaEncuestaArbol"] . "' LIMIT 1");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $_GET["id"]);
        exit;
        break;


    case "InsertarNotificacionLocal":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["IDModulo"] = 104;
        $frm["IDDetalle"] = $frm["IDEncuestaArbol"];
        foreach ($frm["IDDia"] as $Dia_seleccion) :
            $array_dia[] = $Dia_seleccion;
        endforeach;
        if (count($array_dia) > 0) :
            $id_dia = implode("|", $array_dia) . "|";
        endif;
        $frm["Dias"] = $id_dia;
        $id = $dbo->insert($frm, "NotificacionLocal", "IDNotificacionLocal");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $frm["IDEncuestaArbol"]);
        exit;
        break;

    case "ModificaNotificacionLocal":
        $frm = SIMUtil::varsLOG($_POST);
        foreach ($frm["IDDia"] as $Dia_seleccion) :
            $array_dia[] = $Dia_seleccion;
        endforeach;
        if (count($array_dia) > 0) :
            $id_dia = implode("|", $array_dia) . "|";
        endif;
        $frm["Dias"] = $id_dia;
        $dbo->update($frm, "NotificacionLocal", "IDNotificacionLocal", $frm["IDNotificacionLocal"]);
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $frm["IDEncuestaArbol"]);
        exit;
        break;

    case "EliminaNotificacionLocal":
        $id = $dbo->query("DELETE FROM NotificacionLocal WHERE IDNotificacionLocal   = '" . $_GET["IDNotificacionLocal"] . "' LIMIT 1");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $_GET["id"]);
        exit;
        break;

    case "DelDocNot":
        $campo = $_GET['cam'];
        $doceliminar = BANNERAPP_DIR . $dbo->getFields("EncuestaArbol", "$campo", "IDEncuestaArbol = '" . $_GET[id] . "'");
        unlink($doceliminar);
        $dbo->query("UPDATE EncuestaArbol SET $campo = '' WHERE IDEncuestaArbol = $_GET[id] LIMIT 1 ;");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ArchivoEliminadoCorrectamente', LANGSESSION));
        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
        exit;
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
