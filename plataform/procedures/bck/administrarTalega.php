<?php

$titulo = $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];
SIMReg::setFromStructure(array(
    "title" => "Talega",
    "table" => "Talega",
    "key" => "IDTalega",
    "mod" => "administrarTalega"
));



//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));
//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

$script = "administrarTalega";

switch (SIMNet::req("action")) {

    case "add" :

        $sql = "select IDPropiedadesTalega, nombre "
                . "from PropiedadesTalega "
                . "where IDClub = '" . SIMUser::get("club") . "' ";

        $result = $dbo->query($sql);
        $aPropiedades = [];
        while ($row = $dbo->fetchArray($result)) {
            $aPropiedades[] = $row;
        }

        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "CREAR";
        break;

    case "insert" :

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            $frm = $_POST;
            $rand = rand(0, 1000);
            $idClub = SIMUser::get("club");

           if($frm["personaTalega"] == 1) $codigo = $idClub . "-" . $frm["IDSocio"] . "-" . $rand;
           else if($frm["personaTalega"] == 1) $codigo = $idClub . "-" . $frm["IDInvitado"] . "-" . $rand;

            $aTalega["nombre"] = $frm["nombre"];
            $aTalega["tipoCodigo"] = $frm["tipoCodigo"];
            $aTalega["codigo"] = $codigo;
            $aTalega["localizacion"] = $frm["localizacion"];

            if($frm["tipoCodigo"] == 1)
            $aTalega["codigoArchivo"]= SIMUtil::generar_codigo_barras_talega($codigo,$idClub);
            else
            $aTalega["codigoArchivo"]= SIMUtil::generar_codigo_qr_talega($codigo,$idClub);

            $aTalega["IDSocio"] = $frm["personaTalega"] == 1 ? $frm["IDSocio"] : "NULL";
            $aTalega["IDInvitado"] = $frm["personaTalega"] == 2 & $frm["tipoInvitado"] == 2 ? $frm["IDInvitado"] : "NULL";
            $aTalega["IDSocioInvitado"] = $frm["personaTalega"] == 2 & $frm["tipoInvitado"] == 1 ? $frm["IDInvitado"] : "NULL";


            $aTalega["estado"] = 1;
            $aTalega["IDClub"] = SIMUser::get("club");
            $aTalega["fechaRegistro"] = date("Y-m-d H:i:s");
            $aTalega["idUsuarioRegistra"] = SIMUser::get("IDUsuario");
            $id = $dbo->insert($aTalega, $table, $key);


            $talegaAdministracion = array(
                "IDTalega" => $id,
                "IDCaddie" => "NULL",
                "numeroDocumentoTercero" => "",
                "nombreTercero" => "",
                "estado" => 1,
                "fechaRegistro" => date("Y-m-d H:i:s"),
                "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
            );

            $idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");

            $aPropiedad = [];
            foreach ($frm["idPropiedad"] AS $index => $idPropiedad) {
                $aPropiedadHistorico = array(
                    "IDTalegaAdministracion" => $idTalegaAdministracion,
                    "IDPropiedadesTalega" => $idPropiedad,
                    "valor" => $frm["propiedad"][$index],
                    "fechaRegistro" => date("Y-m-d H:i:s"),
                    "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                );

                $aPropiedadDetalle = array(
                    "IDTalegaAdministracion" => $idTalegaAdministracion,
                    "IDTalega" => $id,
                    "IDPropiedadesTalega" => $idPropiedad,
                    "valor" => $frm["propiedad"][$index],
                    "fechaRegistro" => date("Y-m-d H:i:s"),
                    "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                );

                $dbo->insert($aPropiedadHistorico, "TalegaHistorico", "IDTalegaHistorico");
                $dbo->insert($aPropiedadDetalle, "TalegaDetalle", "IDTalegaDetalle");
            }

            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;


        break;

    case "search" :
        $view = "views/administrarTalega/list.php";
        break;
    case "edit":

        $sql = "select IDPropiedadesTalega, nombre "
                . "from PropiedadesTalega "
                . "where IDClub = '" . SIMUser::get("club") . "' ";

        $result = $dbo->query($sql);
        $aPropiedades = [];
        while ($row = $dbo->fetchArray($result)) {
            $aPropiedades[] = $row;
        }

        $sql = "select IDPropiedadesTalega, valor "
                . "from TalegaDetalle "
                . "where IDTalega = '" . SIMNet::reqInt("id") . "' ";

        $result = $dbo->query($sql);
        $aPropiedadesTalega = [];
        while ($row = $dbo->fetchArray($result)) {
            $aPropiedadesTalega[$row["IDPropiedadesTalega"]] = $row["valor"];
        }


        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "ACTUALIZAR";
        break;

    case "update" :

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            $frm = $_POST;
            $aTalega["nombre"] = $frm["nombre"];
            $aTalega["IDSocio"] = $frm["personaTalega"] == 1 ? $frm["IDSocio"] : "NULL";
            $aTalega["IDInvitado"] = $frm["personaTalega"] == 2 & $frm["tipoInvitado"] == 2 ? $frm["IDInvitado"] : "NULL";
            $aTalega["IDSocioInvitado"] = $frm["personaTalega"] == 2 & $frm["tipoInvitado"] == 1 ? $frm["IDInvitado"] : "NULL";

            $aTalega["localizacion"] = $frm["localizacion"];
            $id = $dbo->update($aTalega, $table, $key, SIMNet::reqInt("id"));

             $talegaAdministracion = array(
                "IDTalega" => SIMNet::reqInt("id"),
                "IDCaddie" => "NULL",
                "numeroDocumentoTercero" => "",
                "nombreTercero" => "",
                "estado" => 4,
                "fechaRegistro" => date("Y-m-d H:i:s"),
                "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
            );

            $idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");


            $sql_delete = "DELETE FROM TalegaDetalle "
                        . "WHERE IDTalega = '" . SIMNet::reqInt("id") . "' ";
            $qry_delete = $dbo->query($sql_delete);

            $aPropiedad = [];
            foreach ($frm["idPropiedad"] AS $index => $idPropiedad) {
                $aPropiedadHistorico = array(
                    "IDTalegaAdministracion" => $idTalegaAdministracion,
                    "IDPropiedadesTalega" => $idPropiedad,
                    "valor" => $frm["propiedad"][$index],
                    "fechaRegistro" => date("Y-m-d H:i:s"),
                    "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                );


                $aPropiedadDetalle = array(
                    "IDTalegaAdministracion" => $idTalegaAdministracion,
                    "IDTalega" => $id,
                    "IDPropiedadesTalega" => $idPropiedad,
                    "valor" => $frm["propiedad"][$index],
                    "fechaRegistro" => date("Y-m-d H:i:s"),
                    "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                );

                $dbo->insert($aPropiedadHistorico, "TalegaHistorico", "IDTalegaHistorico");
                $dbo->insert($aPropiedadDetalle, "TalegaDetalle", "IDTalegaDetalle");
            }

            SIMHTML::jsAlert("Registro Actualizado Correctamente");
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;

    case "admin":

        $sql = "select IDPropiedadesTalega, nombre "
                . "from PropiedadesTalega "
                . "where IDClub = '" . SIMUser::get("club") . "' ";

        $result = $dbo->query($sql);
        $aPropiedades = [];
        while ($row = $dbo->fetchArray($result)) {
            $aPropiedades[] = $row;
        }

        $sql = "select IDPropiedadesTalega, valor "
                . "from TalegaDetalle "
                . "where IDTalega = '" . SIMNet::reqInt("id") . "' ";

        $result = $dbo->query($sql);
        $aPropiedadesTalega = [];
        while ($row = $dbo->fetchArray($result)) {
            $aPropiedadesTalega[$row["IDPropiedadesTalega"]] = $row["valor"];
        }

        $sql = "select t.IDTalega, t.nombre, t.codigo, t.IDSocio, t.localizacion, "
                . "IF(t.IDSocio > 0 ,CONCAT_WS(' ',s.Nombre, s.Apellido), IF(i.IDInvitado > 0,  CONCAT_WS(' ',i.Nombre, i.Apellido), si.Nombre )) AS socio, t.estado "
                . "from $table t "
                . "LEFT JOIN Socio s ON(t.IDSocio = s.IDSocio) "
                . "LEFT JOIN Invitado i ON(t.IDInvitado = i.IDInvitado) "
                . "LEFT JOIN SocioInvitado si ON(t.IDSocioInvitado = si.IDSocioInvitado) "
                . "where t.IDTalega = '" . SIMNet::reqInt("id") . "' ";
        $result = $dbo->query($sql);
        $row = $dbo->fetchArray($result);
        $frm = $row;

        $view = "views/" . $script . "/form.php";
        $newmode = "updateAdmin";
        if($frm["estado"] == 1)$titulo_accion = "ENTREGAR";
        if($frm["estado"] == 3)$titulo_accion = "RECIBIR";
        break;

        case "updateAdmin" :

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            $frm = $_POST;
            $estado = 3;
            if($frm["estado"] == 3)$estado = 1;
            $aTalega["estado"] = $estado;
            $id = $dbo->update($aTalega, $table, $key, SIMNet::reqInt("id"));

            $talegaAdministracion = array(
                "IDTalega" => SIMNet::reqInt("id"),
                "IDCaddie" => $frm["caddie"] > 0 ? $frm["caddie"] : "NULL",
                "numeroDocumentoTercero" => $frm["recibeTercero"] == 1 ? $frm["numeroDocumentoTercero"] : "",
                "nombreTercero" => $frm["recibeTercero"] == 1 ? $frm["nombreTercero"] : "",
                "observaciones" => $frm["observaciones"],
                "estado" => $estado,
                "fechaRegistro" => date("Y-m-d H:i:s"),
                "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
            );


            $idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");


            $sql_delete = "DELETE FROM TalegaDetalle "
                        . "WHERE IDTalega = '" . SIMNet::reqInt("id") . "' ";
            $qry_delete = $dbo->query($sql_delete);



            $aPropiedad = [];
            if($estado == 3)$proceso = "la salida";
            else $proceso = "el ingreso";

            $mensaje = "se ha realizado $proceso de su talega con la siguiente información: ";
            $aTextoPropiedades = [];

            foreach ($frm["idPropiedad"] AS $index => $idPropiedad) {

                $aPropiedadHistorico = array(
                    "IDTalegaAdministracion" => $idTalegaAdministracion,
                    "IDTalega" => $id,
                    "IDPropiedadesTalega" => $idPropiedad,
                    "valor" => $frm["propiedad"][$index],
                    "estado" => 1,
                    "fechaRegistro" => date("Y-m-d H:i:s"),
                    "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                );

                $aTextoPropiedades[] = $frm["nombrePropiedad"][$index].": ".$frm["propiedad"][$index];

                $aPropiedadDetalle = array(
                    "IDTalegaAdministracion" => $idTalegaAdministracion,
                    "IDTalega" => $id,
                    "IDPropiedadesTalega" => $idPropiedad,
                    "valor" => $frm["propiedad"][$index],
                    "fechaRegistro" => date("Y-m-d H:i:s"),
                    "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                );

                $dbo->insert($aPropiedadHistorico, "TalegaHistorico", "IDTalegaHistorico");
                $dbo->insert($aPropiedadDetalle, "TalegaDetalle", "IDTalegaDetalle");
            }


            if(count($aTextoPropiedades) > 0 && $frm["idSocio"] > 0)
            {
                $textoPropiedades = join(", ", $aTextoPropiedades);
                $mensaje = $mensaje . " " . $textoPropiedades;
                SIMUtil::enviar_notificacion_push_general(SIMUser::get("club"),$frm["idSocio"],$mensaje);
            }

            SIMHTML::jsAlert("Registro Actualizado Correctamente");
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;

        break;

    default;
        $view = "views/" . $script . "/list.php";
        //$newmode = "insert";
        //$titulo_accion = "Crear";
        break;
} // End switch

if (empty($view))
    $view = "views/" . $script . "/list.php";
?>
