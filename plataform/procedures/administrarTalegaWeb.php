
<?php

$titulo = $ArrayMensajesWeb[$tipo_club]["NombreModuloSocio"];
SIMReg::setFromStructure(array(
    "title" => "Talega",
    "table" => "Talega",
    "key" => "IDTalega",
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

$script = "administrarTalegaWeb";

switch (SIMNet::req("action")) {

    case "add":

        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "CREAR";
        break;

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

            $frm = $_POST;
            $rand = rand(0, 1000);
            $idClub = $frm["IDClub"];
            $idTalega = SIMNet::reqInt("id");

            if ($frm["personaTalega"] == 1) $codigo = $idClub . "-" . $frm["IDSocio"] . "-" . $rand;
            else if ($frm["personaTalega"] == 2) $codigo = $idClub . "-" . $frm["IDInvitado"] . "-" . $rand;

            $aTalega["nombre"] = $frm["nombre"];
            $aTalega["tipoCodigo"] = $frm["tipoCodigo"];
            $aTalega["codigo"] = $codigo;
            $aTalega["localizacion"] = $frm["localizacion"];

            $aTalega["IDSocio"] = $frm["personaTalega"] == 1 ? $frm["IDSocio"] : "NULL";
            $aTalega["IDInvitado"] = $frm["personaTalega"] == 2 & $frm["tipoInvitado"] == 2 ? $frm["IDInvitado"] : "NULL";
            $aTalega["IDSocioInvitado"] = $frm["personaTalega"] == 2 & $frm["tipoInvitado"] == 1 ? $frm["IDInvitado"] : "NULL";

            $aTalega["estado"] = 5;
            $aTalega["Activo"] = 'S';
            $aTalega["IDClub"] = $frm["IDClub"];
            $aTalega["fechaRegistro"] = date("Y-m-d H:i:s");
            $aTalega["UsuarioTrCr"] = "Registro Web";
            $aTalega["idUsuarioRegistra"] = SIMUser::get("IDUsuario");

            $id = $dbo->insert($aTalega, $table, $key);

            $socioRegistra = $aTalega["IDSocio"];

            if (is_null($socioRegistra)) {
                $socioRegistra = !is_null($aTalega["IDInvitado"]) ? $aTalega["IDInvitado"] : $aTalega["IDSocioInvitado"];
            }

            if ($frm["tipoCodigo"] == 1)
                $codArchivo = SIMUtil::generar_codigo_barras_talega($codigo, $idClub);
            else
                $codArchivo = SIMUtil::generar_codigo_qr_talega($codigo, $socioRegistra, $id);

            $id = $dbo->update(array("codigoArchivo" => $codArchivo), $table, $key, $id);

            $talegaAdministracion = array(
                "IDTalega" => $id,
                "IDCaddie" => "NULL",
                "numeroDocumentoTercero" => "",
                "nombreTercero" => "",
                "estado" => 1,
                "fechaRegistro" => date("Y-m-d H:i:s"),
                "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                "IdSocioRegistra" => $socioRegistra
            );


            $idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");

            $aPropiedad = [];
            foreach ($frm["idPropiedad"] as $index => $idPropiedad) {
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

            $arrPalos = json_decode($frm['Palos'], true);
            if (!empty($arrPalos)) {
                foreach ($arrPalos as $palo) {
                    $palo['IDTalega'] = $id;
                    $palo['IDClub'] = $idClub;

                    $dbo->insert($palo, "TalegaPalos", "IDTalegaPalos");
                }
            }

            $IDClub = $frm["IDClub"];
            $IDSocio = $frm["IDSocio"];
            $mensaje = "Registro Guardado";
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect("administrarTalegaWeb.php?IDClub=' $IDClub '&IDSocio=' $IDSocio '&mensaje='$mensaje'");
        } else
            exit;


        break;
} // End switch
