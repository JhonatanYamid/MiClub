<?php

$titulo = $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];
SIMReg::setFromStructure(array(
    "title" => "Caddie",
    "table" => "Caddie",
    "key" => "IDCaddie",
    "mod" => "Caddie"
));



//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));
//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

$script = "administrarCaddie";
switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            $frm = $_POST;
            $sql = "select IDCaddie "
                . "from Caddie "
                . "where numeroDocumento = '" . $frm["numeroDocumento"] . "' "
                . "AND IDClub = '" . SIMUser::get("club") . "' ";
            $result = $dbo->query($sql);
            $row = $dbo->fetchArray($result);
            if ($row == "") {
                //los campos al final de las tablas

                //UPLOAD de imagenes
                if (isset($_FILES)) {
                    $files = SIMFile::upload($_FILES["foto"], CADDIE_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES["foto"]["name"]))
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    $frm["foto"] = $files[0]["innername"];
                } //end if
                //insertamos los datos
                $frm["fechaRegistro"] = date("Y-m-d H:i:s");
                $frm["idUsuarioRegistra"] = SIMUser::get("IDUsuario");
                $id = $dbo->insert($frm, $table, $key);
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            } else {
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Yaexisteuncaddieconestenúmerodedocumento', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            }
        } else
            exit;


        break;

    case "search":
        $view = "views/administrarCaddie/list.php";
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
            //$frm = SIMUtil::varsLOG( $_POST );
            $frm = $_POST;

            $sql = "select IDCaddie "
                . "from Caddie "
                . "where numeroDocumento = '" . $frm["numeroDocumento"] . "' "
                . "AND IDCaddie != " . SIMNet::reqInt("id") . "  "
                . "AND IDClub = '" . SIMUser::get("club") . "' ";
            $result = $dbo->query($sql);
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            if ($row == "") {
                //UPLOAD de imagenes
                if (isset($_FILES)) {



                    $files = SIMFile::upload($_FILES["foto"], CADDIE_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES["foto"]["name"])) {
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    } else {
                        if ($frm['fotoAnterior'] != "" && !empty($files)) {
                            $foto = $frm['fotoAnterior'];
                            $foto = str_replace("jsalcm", "_", $foto);
                            $filedelete = CADDIE_DIR . $foto;
                            unlink($filedelete);
                        }
                        $frm["foto"] = $files[0]["innername"];
                    }
                } //end if
                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
                $frm = $dbo->fetchById($table, $key, $id, "array");
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
            } else {
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Yaexisteuncaddieconestenúmerodedocumento', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
            }
        } else
            exit;

        break;

    case "delfoto":
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $id = $_GET['id'];
        $foto = str_replace("jsalcm", "_", $foto);
        $filedelete = CADDIE_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . $id . "");
        break;

    default;
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;
} // End switch



if (empty($view))
    $view = "views/administrarCaddie/form.php";
