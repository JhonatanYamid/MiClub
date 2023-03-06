 <?php

    SIMReg::setFromStructure(array(
        "title" => 'Bicicleta',
        "table" => "Bicicleta",
        "key" => "IDBicicleta",
        "mod" => "Bicicleta",
    ));

    //extraemos las variables
    $table = SIMReg::get("table");
    $key = SIMReg::get("key");
    $mod = SIMReg::get("mod");

    //Verificar permisos
    SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));
    //creando las notificaciones que llegan en el parametro m de la URL
    SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

    $script = "administrarBicicleta";

    switch (SIMNet::req("action")) {

        case "add":

            $sql = "SELECT IDPropiedadesBicicleta, Nombre "
                . "FROM PropiedadesBicicleta "
                . "WHERE IDClub = '" . SIMUser::get("club") . "' ";

            $result = $dbo->query($sql);
            $aPropiedades = [];
            while ($row = $dbo->fetchArray($result)) {
                $aPropiedades[] = $row;
            }

            $view = "views/" . $script . "/form.php";
            $newmode = "insert";
            $titulo_accion = "CREAR";
            break;

        case "insert":

            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

                //$frm = $_POST;
                $frm = SIMUtil::varsLOG($_POST);
                $rand = rand(0, 1000);
                $idClub = SIMUser::get("club");

                if ($frm["PersonaBicicleta"] == 1) $codigo = $idClub . "-" . $frm["IDSocio"] . "-" . $rand;
                else if ($frm["PersonaBicicleta"] == 2) $codigo = $idClub . "-" . $frm["IDInvitado"] . "-" . $rand;

                $aBicicleta["Nombre"] = $frm["Nombre"];
                $aBicicleta["TipoCodigo"] = $frm["TipoCodigo"];
                $aBicicleta["Codigo"] = $codigo;
                $aBicicleta["Localizacion"] = $frm["Localizacion"];

                if ($frm["TipoCodigo"] == 1)
                    $aBicicleta["CodigoArchivo"] = SIMUtil::generar_codigo_barras_bicicleta($codigo, $idClub);
                else
                    $aBicicleta["CodigoArchivo"] = SIMUtil::generar_codigo_qr_bicicleta($codigo, $idClub);

                $aBicicleta["IDSocio"] = $frm["PersonaBicicleta"] == 1 ? $frm["IDSocio"] : "NULL";
                $aBicicleta["IDInvitado"] = $frm["PersonaBicicleta"] == 2 & $frm["TipoInvitado"] == 2 ? $frm["IDInvitado"] : "NULL";
                $aBicicleta["IDSocioInvitado"] = $frm["PersonaBicicleta"] == 2 & $frm["TipoInvitado"] == 1 ? $frm["IDInvitado"] : "NULL";

                $aBicicleta["Estado"] = 1;
                $aBicicleta["IDClub"] = SIMUser::get("club");
                $aBicicleta["FechaRegistro"] = date("Y-m-d H:i:s");
                $aBicicleta["IDUsuarioRegistra"] = SIMUser::get("IDUsuario");

                if (isset($_FILES)) {
                    $files =  SIMFile::upload($_FILES["Foto"], BICICLETA_DIR, "IMAGE");
               
                    if (empty($files) && !empty($_FILES["Foto"]["name"]))
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    $aBicicleta["Foto"] = $files[0]["innername"];
                }
     
                $id = $dbo->insert($aBicicleta, $table, $key);

                $socioRegistra = $aBicicleta["IDSocio"];

                if(is_null($socioRegistra)){
                    $socioRegistra = !is_null($aBicicleta["IDInvitado"]) ? $aBicicleta["IDInvitado"] : $aBicicleta["IDSocioInvitado"];
                }

                $bicicletaAdministracion = array(
                    "IDBicicleta" => $id,
                    "IDCaddie" => "NULL",
                    "NumeroDocumentoTercero" => "",
                    "NombreTercero" => "",
                    "Estado" => 1,
                    "FechaRegistro" => date("Y-m-d H:i:s"),
                    "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    "IDSocioRegistra" => $socioRegistra
                );

                $idBicicletaAdministracion = $dbo->insert($bicicletaAdministracion, "BicicletaAdministracion", "IDBicicletaAdministracion");

                $aPropiedad = [];
                foreach ($frm["IDPropiedad"] as $index => $idPropiedad) {
                    $aPropiedadHistorico = array(
                        "IDBicicletaAdministracion" => $idBicicletaAdministracion,
                        "IDPropiedadesBicicleta" => $idPropiedad,
                        "Valor" => $frm["Propiedad"][$index],
                        "FechaRegistro" => date("Y-m-d H:i:s"),
                        "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    $aPropiedadDetalle = array(
                        "IDBicicletaAdministracion" => $idBicicletaAdministracion,
                        "IDBicicleta" => $id,
                        "IDPropiedadesBicicleta" => $idPropiedad,
                        "Valor" => $frm["Propiedad"][$index],
                        "FechaRegistro" => date("Y-m-d H:i:s"),
                        "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    $dbo->insert($aPropiedadHistorico, "BicicletaHistorico", "IDBicicletaHistorico");
                    $dbo->insert($aPropiedadDetalle, "BicicletaDetalle", "IDBicicletaDetalle");
                }

                $arrAccesorios = json_decode($frm['Accesorios'],true);
                if(!empty($arrAccesorios)){
                    foreach($arrAccesorios as $accesorio){
                        $accesorio['IDBicicleta'] = $id;
                        $accesorio['IDClub'] = $idClub;
                        
                        $dbo->insert($accesorio, "AccesoriosBicicleta", "IDAccesoriosBicicleta");

                    }
                }

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            } else
                exit;


            break;

        case "search":
            $view = "views/administrarBicicleta/list.php";
            break;
        case "edit":

            $sql = "SELECT IDPropiedadesBicicleta, Nombre "
                . "FROM PropiedadesBicicleta "
                . "WHERE IDClub = '" . SIMUser::get("club") . "' ";

            $result = $dbo->query($sql);
            $aPropiedades = [];
            while ($row = $dbo->fetchArray($result)) {
                $aPropiedades[] = $row;
            }

            $sql = "SELECT IDPropiedadesBicicleta, Valor "
                . "FROM BicicletaDetalle "
                . "WHERE IDBicicleta = '" . SIMNet::reqInt("id") . "' ";

            $result = $dbo->query($sql);
            $aPropiedadesBicicleta = [];
            while ($row = $dbo->fetchArray($result)) {
                $aPropiedadesBicicleta[$row["IDPropiedadesBicicleta"]] = $row["Valor"];
            }

            $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
            $view = "views/" . $script . "/form.php";
            $newmode = "update";
            $titulo_accion = "ACTUALIZAR";
            break;

        case "update":

            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

                $frm = $_POST;

                $rand = rand(0, 1000);
                $idClub = SIMUser::get("club");
                $idBicicleta = SIMNet::reqInt("id");

                $aBicicleta["Nombre"] = $frm["Nombre"];
                $aBicicleta["Localizacion"] = $frm["Localizacion"];
                $aBicicleta["IDSocio"] = $frm["PersonaBicicleta"] == 1 ? $frm["IDSocio"] : "NULL";
                $aBicicleta["IDInvitado"] = $frm["PersonaBicicleta"] == 2 & $frm["TipoInvitado"] == 2 ? $frm["IDInvitado"] : "NULL";
                $aBicicleta["IDSocioInvitado"] = $frm["PersonaBicicleta"] == 2 & $frm["TipoInvitado"] == 1 ? $frm["IDInvitado"] : "NULL";
                
                $sqlCod = "SELECT IDSocio,IDInvitado,IDSocioInvitado,Codigo FROM Bicicleta "
                . "WHERE IDBicicleta = $idBicicleta";
                $resultCod = $dbo->query($sqlCod);

                while ($rowCod = $dbo->fetchArray($resultCod)) {
                    $idSocioTal = $rowCod['IDSocio'] == 0 ? 'NULL' : $rowCod['IDSocio'];
                    $idInvitadoTal = $rowCod['IDInvitado'] == 0 ? 'NULL' : $rowCod['IDInvitado'];
                    $idSocioInvitadoTal = $rowCod['IDSocioInvitado'] == 0 ? 'NULL' : $rowCod['IDSocioInvitado'];
                    $codigo = $rowCod['Codigo'];
                }

                if (($aBicicleta["IDSocio"] != NULL && $idSocioTal != $aBicicleta["IDSocio"]) || ($aBicicleta["IDInvitado"] != NULL && $idInvitadoTal != $aBicicleta["IDInvitado"]) || ($aBicicleta["IDSocioInvitado"] != NULL && $idSocioInvitadoTal != $aBicicleta["IDSocioInvitado"])) {

                    if ($frm["PersonaBicicleta"] == 1) $codigo = $idClub . "-" . $frm["IDSocio"] . "-" . $rand;
                    else if ($frm["PersonaBicicleta"] == 2) $codigo = $idClub . "-" . $frm["IDInvitado"] . "-" . $rand;
                }

                $frm['IDSocioRegistra'] = $aBicicleta["IDSocio"];

                if(is_null($frm['IDSocioRegistra'])){
                    $frm['IDSocioRegistra'] = !is_null($aBicicleta["IDInvitado"]) ? $aBicicleta["IDInvitado"] : $aBicicleta["IDSocioInvitado"];
                }

                if ($frm["TipoCodigo"] == 1)
                    $aBicicleta["CodigoArchivo"] = SIMUtil::generar_codigo_barras_bicicleta($codigo, $idClub);
                else
                    $aBicicleta["CodigoArchivo"] = SIMUtil::generar_codigo_qr_bicicleta($codigo, $idClub);

                $aBicicleta["Codigo"] = $codigo;

                $id = $dbo->update($aBicicleta, $table, $key, SIMNet::reqInt("id"));

                $bicicletaAdministracion = array(
                    "IDBicicleta" => SIMNet::reqInt("id"),
                    "IDCaddie" => "NULL",
                    "NumeroDocumentoTercero" => "",
                    "NombreTercero" => "",
                    "Estado" => 5,
                    "FechaRegistro" => date("Y-m-d H:i:s"),
                    "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    "IDSocioRegistra" => $frm['IDSocioRegistra']
                );

                $idBicicletaAdministracion = $dbo->insert($bicicletaAdministracion, "BicicletaAdministracion", "IDBicicletaAdministracion");
               
                $sql_delete = "DELETE FROM BicicletaDetalle "
                    . "WHERE IDBicicleta = '" . SIMNet::reqInt("id") . "' ";
                $qry_delete = $dbo->query($sql_delete);

                $aPropiedad = [];
                foreach ($frm["IDPropiedad"] as $index => $idPropiedad) {
                    $aPropiedadHistorico = array(
                        "IDBicicletaAdministracion" => $idBicicletaAdministracion,
                        "IDPropiedadesBicicleta" => $idPropiedad,
                        "Valor" => $frm["Propiedad"][$index],
                        "FechaRegistro" => date("Y-m-d H:i:s"),
                        "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    $aPropiedadDetalle = array(
                        "IDBicicletaAdministracion" => $idBicicletaAdministracion,
                        "IDBicicleta" => $id,
                        "IDPropiedadesBicicleta" => $idPropiedad,
                        "Valor" => $frm["Propiedad"][$index],
                        "FechaRegistro" => date("Y-m-d H:i:s"),
                        "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    $dbo->insert($aPropiedadHistorico, "BicicletaHistorico", "IDBicicletaHistorico");
                    $dbo->insert($aPropiedadDetalle, "BicicletaDetalle", "IDBicicletaDetalle");
                }

                $sqlDel = "DELETE FROM AccesoriosBicicleta WHERE IDBicicleta = $id";
                $resDel = $dbo->query($sqlDel);
                
                $arrAccesorios = json_decode($frm['Accesorios'],true);
                
                if(!empty($arrAccesorios)){
                    foreach($arrAccesorios as $accesorio){
                        $accesorio['IDBicicleta'] = $id;
                        $accesorio['IDClub'] = $idClub;

                        $dbo->insert($accesorio, "AccesoriosBicicleta", "IDAccesoriosBicicleta");
                    }
                }

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroActualizadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            } else
                exit;

            break;

        case "admin":

            $sql = "SELECT IDPropiedadesBicicleta, Nombre "
                . "FROM PropiedadesBicicleta "
                . "WHERE IDClub = '" . SIMUser::get("club") . "' ";

            $result = $dbo->query($sql);
            $aPropiedades = [];
            while ($row = $dbo->fetchArray($result)) {
                $aPropiedades[] = $row;
            }

            $sql = "SELECT  IDPropiedadesBicicleta, Valor "
                . "FROM BicicletaDetalle "
                . "WHERE IDBicicleta = '" . SIMNet::reqInt("id") . "' ";

            $result = $dbo->query($sql);
            $aPropiedadesBicicleta = [];
            while ($row = $dbo->fetchArray($result)) {
                $aPropiedadesBicicleta[$row["IDPropiedadesBicicleta"]] = $row["Valor"];
            }

            $sql = "SELECT b.IDBicicleta, b.Nombre, b.Codigo, b.IDSocio, b.Localizacion, b.IDSocioRegistra, b.IDInvitado, b.IDSocioInvitado,"
                . "IF(b.IDSocio > 0 ,CONCAT_WS(' ',s.Nombre, s.Apellido), IF(i.IDInvitado > 0,  CONCAT_WS(' ',i.Nombre, i.Apellido), si.Nombre )) AS Socio, b.Estado "
                . "FROM $table b "
                . "LEFT JOIN Socio s ON(b.IDSocio = s.IDSocio) "
                . "LEFT JOIN Invitado i ON(b.IDInvitado = i.IDInvitado) "
                . "LEFT JOIN SocioInvitado si ON(b.IDSocioInvitado = si.IDSocioInvitado) "
                . "WHERE b.IDBicicleta = '" . SIMNet::reqInt("id") . "' ";
            $result = $dbo->query($sql);
            $row = $dbo->fetchArray($result);
            $frm = $row;

            $socioRegistra = $aBicicleta["IDSocio"];

            if(is_null($socioRegistra)){
                $socioRegistra = !is_null($aBicicleta["IDInvitado"]) ? $aBicicleta["IDInvitado"] : $aBicicleta["IDSocioInvitado"];
            }

            $view = "views/" . $script . "/form.php";
            $newmode = "updateAdmin";
            if ($frm["Estado"] == 1 || $frm["Estado"] == 4) $titulo_accion = "ENTREGAR";
            if ($frm["Estado"] == 3) $titulo_accion = "RECIBIR";
            break;

        case "updateAdmin":

            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

                $frm = $_POST;

                $estado = 3;
                $socioRegistra = $dbo->getFields("Bicicleta", "IDSocioRegistra", "IDBicicleta = ".SIMNet::reqInt("id"));
                
                if($frm["Estado"] == 1)
                    $socioRegistra = $frm['IDSocio'];

                if ($frm["Estado"] == 3)
                    $estado = 1;

                if (array_key_exists('IDConfiguracionBicicletaLugarEntrega', $frm)) {
                    $aBicicleta["IDConfiguracionBicicletaLugarEntrega"] = $frm["IDConfiguracionBicicletaLugarEntrega"];
                    $aBicicleta["FechaEntrega"] = date("Y-m-d H:i:s");
                }

                $aBicicleta["Estado"] = $estado;
                $aBicicleta['IDSocioRegistra'] = $socioRegistra;

                $id = $dbo->update($aBicicleta, $table, $key, SIMNet::reqInt("id"));

                $bicicletaAdministracion = array(
                    "IDBicicleta" => SIMNet::reqInt("id"),
                    "IDCaddie" => $frm["caddie"] > 0 ? $frm["caddie"] : "NULL",
                    "NumeroDocumentoTercero" => $frm["RecibeTercero"] == 1 ? $frm["NumeroDocumentoTercero"] : "",
                    "NombreTercero" => $frm["RecibeTercero"] == 1 ? $frm["NombreTercero"] : "",
                    "Observaciones" => $frm["Observaciones"],
                    "Estado" => $estado,
                    "FechaRegistro" => date("Y-m-d H:i:s"),
                    "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    "IDSocioRegistra" => $socioRegistra
                );

                if (array_key_exists('IDConfiguracionBicicletaLugarEntrega', $frm)) {
                    $bicicletaAdministracion["IDConfiguracionBicicletaLugar"] = $frm["IDConfiguracionBicicletaLugarEntrega"];
                }

                $idBicicletaAdministracion = $dbo->insert($bicicletaAdministracion, "BicicletaAdministracion", "IDBicicletaAdministracion");

                $sql_delete = "DELETE FROM BicicletaDetalle "
                    . "WHERE IDBicicleta = '" . SIMNet::reqInt("id") . "' ";
                $qry_delete = $dbo->query($sql_delete);

                $aPropiedad = [];
                if ($estado == 3) $proceso = "la salida";
                else $proceso = "el ingreso";

                $mensaje = "se ha realizado $proceso de su bicicleta con la siguiente información: ";
                $aTextoPropiedades = [];

                foreach ($frm["IDPropiedad"] as $index => $idPropiedad) {

                    $aPropiedadHistorico = array(
                        "IDBicicletaAdministracion" => $idBicicletaAdministracion,
                        "IDBicicleta" => $id,
                        "IDPropiedadesBicicleta" => $idPropiedad,
                        "Valor" => $frm["Propiedad"][$index],
                        "Estado" => 1,
                        "FechaRegistro" => date("Y-m-d H:i:s"),
                        "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    $aTextoPropiedades[] = $frm["NombrePropiedad"][$index] . ": " . $frm["Propiedad"][$index];

                    $aPropiedadDetalle = array(
                        "IDBicicletaAdministracion" => $idBicicletaAdministracion,
                        "IDBicicleta" => $id,
                        "IDPropiedadesBicicleta" => $idPropiedad,
                        "Valor" => $frm["Propiedad"][$index],
                        "FechaRegistro" => date("Y-m-d H:i:s"),
                        "IDUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    if($frm["Observacion"][$index]){
                        $aPropiedadHistorico['Observacion'] = $frm["Observacion"][$index];
                        $aPropiedadDetalle['Observacion'] = $frm["Observacion"][$index];
                    }

                    $dbo->insert($aPropiedadHistorico, "BicicletaHistorico", "IDBicicletaHistorico");
                    $dbo->insert($aPropiedadDetalle, "BicicletaDetalle", "IDBicicletaDetalle");
                }


                if (count($aTextoPropiedades) > 0 && $frm["IDSocio"] > 0) {
                    $textoPropiedades = join(", ", $aTextoPropiedades);
                    $mensaje = $mensaje . " " . $textoPropiedades;
                    SIMUtil::enviar_notificacion_push_general(SIMUser::get("club"), $frm["IDSocio"], $mensaje);
                }

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroActualizadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            } else
                exit;

            break;

        case "delfoto":

            $foto = $_GET['foto'];
            $campo = $_GET['campo'];
            $id = $_GET['id'];

            $filedelete = BICICLETA_ROOT . $foto;
            unlink($filedelete);

            $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id");
            
            SIMHTML::jsAlert("Imagen Eliminada Correctamente");
            SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $id . "");

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
