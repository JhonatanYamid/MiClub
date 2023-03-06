<?php

$titulo = $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];
SIMReg::setFromStructure(array(
    "title" => "PropiedadesTalega",
    "table" => "PropiedadesTalega",
    "key" => "IDPropiedadesTalega",
    "mod" => "PropiedadesTalega"
));



//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
SIMUtil::verificar_permiso("Caddie", SIMUser::get("IDPerfil"));
//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

$script = "administrarPropiedadesTalega";
$view = "views/" . $script . "/form.php";
$view1 = "crearEditar.php";
$view2 = "list.php";

switch (SIMNet::req("action")) {

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            $frm = $_POST;
            $sql = "select IDPropiedadesTalega "
                . "from PropiedadesTalega "
                . "Where nombre = '" . $frm["nombre"] . "' "
                . "AND IDClub = '" . SIMUser::get("club") . "' ";
            $result = $dbo->query($sql);
            $row = $dbo->fetchArray($result);
            if ($row == "") {
                //los campos al final de las tablas
                //insertamos los datos
                $frm["fechaRegistro"] = date("Y-m-d H:i:s");
                $frm["idUsuarioRegistra"] = SIMUser::get("IDUsuario");
                $id = $dbo->insert($frm, $table, $key);
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            } else {
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Yaexisteunapropiedaddelatalegaconestenombre', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            }
        } else
            exit;


        break;

    case "edit":
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";
        $view2 = "";
        break;

    case "update":
        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            //$frm = SIMUtil::varsLOG( $_POST );
            $frm = $_POST;

            $sql = "select IDPropiedadesTalega "
                . "from PropiedadesTalega "
                . "where nombre = '" . $frm["nombre"] . "' "
                . "AND IDPropiedadesTalega != " . SIMNet::reqInt("id") . "  "
                . "AND IDClub = '" . SIMUser::get("club") . "' ";
            $result = $dbo->query($sql);
            $row = $dbo->fetchArray($result);
            if ($row == "") {
                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));
                $frm = $dbo->fetchById($table, $key, $id, "array");
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
                //            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
            } else {
                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'Yaexisteunapropiedaddelatalegaconestenombre', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
            }
        } else
            exit;

        break;

    default;
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;
} // End switch



if (empty($view))
    $view = "views/administrarPropiedadesTalega/form.php";
