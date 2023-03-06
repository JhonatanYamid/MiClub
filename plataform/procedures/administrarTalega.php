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

    $script = "administrarTalega";

    switch (SIMNet::req("action")) {

        case "add":

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

        case "insert":

            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

                $frm = $_POST;
                $rand = rand(0, 1000);
                $idClub = SIMUser::get("club");
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

                $aTalega["estado"] = 1;
                $aTalega["Activo"] = 'S';
                $aTalega["IDClub"] = SIMUser::get("club");
                $aTalega["fechaRegistro"] = date("Y-m-d H:i:s");
                $aTalega["idUsuarioRegistra"] = SIMUser::get("IDUsuario");
                
                $id = $dbo->insert($aTalega, $table, $key);

                $socioRegistra = $aTalega["IDSocio"];

                if(is_null($socioRegistra)){
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

                $arrPalos = json_decode($frm['Palos'],true);
                if(!empty($arrPalos)){
                    foreach($arrPalos as $palo){
                        $palo['IDTalega'] = $id;
                        $palo['IDClub'] = $idClub;
                        
                        $dbo->insert($palo, "TalegaPalos", "IDTalegaPalos");

                    }
                }

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
                SIMHTML::jsRedirect($script . ".php");
            } else
                exit;


            break;

        case "search":
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

        case "update":

            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

                $frm = $_POST;
                $rand = rand(0, 1000);
                $idClub = SIMUser::get("club");
                $idTalega = SIMNet::reqInt("id");

                $aTalega["nombre"] = $frm["nombre"];
                $aTalega["IDSocio"] = $frm["personaTalega"] == 1 ? $frm["IDSocio"] : "NULL";
                $aTalega["IDInvitado"] = $frm["personaTalega"] == 2 & $frm["tipoInvitado"] == 2 ? $frm["IDInvitado"] : "NULL";
                $aTalega["IDSocioInvitado"] = $frm["personaTalega"] == 2 & $frm["tipoInvitado"] == 1 ? $frm["IDInvitado"] : "NULL";
                $aTalega["localizacion"] = $frm["localizacion"];
                $aTalega["tipoCodigo"] = $frm["tipoCodigo"];

                $sqlCod = "SELECT IDSocio,IDInvitado,IDSocioInvitado,codigo FROM Talega "
                    . "WHERE IDTalega = $idTalega";
                $resultCod = $dbo->query($sqlCod);

                while ($rowCod = $dbo->fetchArray($resultCod)) {
                    $idSocioTal = $rowCod['IDSocio'] == 0 ? 'NULL' : $rowCod['IDSocio'];
                    $idInvitadoTal = $rowCod['IDInvitado'] == 0 ? 'NULL' : $rowCod['IDInvitado'];
                    $idSocioInvitadoTal = $rowCod['IDSocioInvitado'] == 0 ? 'NULL' : $rowCod['IDSocioInvitado'];
                    $codigo = $rowCod['codigo'];
                }

                if (($aTalega["IDSocio"] != NULL && $idSocioTal != $aTalega["IDSocio"]) || ($aTalega["IDInvitado"] != NULL && $idInvitadoTal != $aTalega["IDInvitado"]) || ($aTalega["IDSocioInvitado"] != NULL && $idSocioInvitadoTal != $aTalega["IDSocioInvitado"])) {

                    if ($frm["personaTalega"] == 1) $codigo = $idClub . "-" . $frm["IDSocio"] . "-" . $rand;
                    else if ($frm["personaTalega"] == 2) $codigo = $idClub . "-" . $frm["IDInvitado"] . "-" . $rand;
                }

                $frm['IdSocioRegistra'] = $aTalega["IDSocio"];

                if(is_null($frm['IdSocioRegistra'])){
                    $frm['IdSocioRegistra'] = !is_null($aTalega["IDInvitado"]) ? $aTalega["IDInvitado"] : $aTalega["IDSocioInvitado"];
                }

                if ($frm["tipoCodigo"] == 1)
                    $aTalega['codigoArchivo'] = SIMUtil::generar_codigo_barras_talega($codigo, $idClub);
                else
                    $aTalega['codigoArchivo'] = SIMUtil::generar_codigo_qr_talega($codigo, $frm['IdSocioRegistra'], SIMNet::reqInt("id"));
                
                $aTalega["codigo"] = $codigo;

                $id = $dbo->update($aTalega, $table, $key, SIMNet::reqInt("id"));

                $talegaAdministracion = array(
                    "IDTalega" => SIMNet::reqInt("id"),
                    "IDCaddie" => "NULL",
                    "numeroDocumentoTercero" => "",
                    "nombreTercero" => "",
                    "estado" => 5,
                    "fechaRegistro" => date("Y-m-d H:i:s"),
                    "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    "IdSocioRegistra" => $frm['IdSocioRegistra']
                );

                $idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");

                /*    if( $frm["idSocio"] > 0)
            {
                $textoPropiedades = join(", ", $aTextoPropiedades);
                $mensaje = 'cambio de estado ah..';
                SIMUtil::enviar_notificacion_push_general(SIMUser::get("club"),$frm["idSocio"],$mensaje);
            } */

                $sql_delete = "DELETE FROM TalegaDetalle "
                    . "WHERE IDTalega = '" . SIMNet::reqInt("id") . "' ";
                $qry_delete = $dbo->query($sql_delete);

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

                $sqlDel = "DELETE FROM TalegaPalos WHERE IDTalega = $id";
                $resDel = $dbo->query($sqlDel);
                
                $arrPalos = json_decode($frm['Palos'],true);
                
                if(!empty($arrPalos)){
                    foreach($arrPalos as $palo){
                        $palo['IDTalega'] = $id;
                        $palo['IDClub'] = $idClub;

                        $dbo->insert($palo, "TalegaPalos", "IDTalegaPalos");
                    }
                }

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroActualizadoCorrectamente', LANGSESSION));
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

            $sql = "select t.IDTalega, t.nombre, t.codigo, t.IDSocio, t.localizacion, t.IdSocioRegistra, t.IDInvitado, t.IDSocioInvitado,"
                . "IF(t.IDSocio > 0 ,CONCAT_WS(' ',s.Nombre, s.Apellido), IF(i.IDInvitado > 0,  CONCAT_WS(' ',i.Nombre, i.Apellido), si.Nombre )) AS socio, t.estado "
                . "from $table t "
                . "LEFT JOIN Socio s ON(t.IDSocio = s.IDSocio) "
                . "LEFT JOIN Invitado i ON(t.IDInvitado = i.IDInvitado) "
                . "LEFT JOIN SocioInvitado si ON(t.IDSocioInvitado = si.IDSocioInvitado) "
                . "where t.IDTalega = '" . SIMNet::reqInt("id") . "' ";
            $result = $dbo->query($sql);
            $row = $dbo->fetchArray($result);
            $frm = $row;

            $socioRegistra = $aTalega["IDSocio"];

            if(is_null($socioRegistra)){
                $socioRegistra = !is_null($aTalega["IDInvitado"]) ? $aTalega["IDInvitado"] : $aTalega["IDSocioInvitado"];
            }

            $view = "views/" . $script . "/form.php";
            $newmode = "updateAdmin";
            if ($frm["estado"] == 1 || $frm["estado"] == 4) $titulo_accion = "ENTREGAR";
            if ($frm["estado"] == 3) $titulo_accion = "RECIBIR";
            break;

        case "updateAdmin":

            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {

                $frm = $_POST;

                $estado = 3;
                $socioRegistra = $dbo->getFields("Talega", "IdSocioRegistra", "IDTalega = ".SIMNet::reqInt("id"));
                
                if($frm["estado"] == 1){
                    $socioRegistra = $frm['idSocio'];
                }

                if ($frm["estado"] == 3) {
                    $estado = 1;
                }

                if (array_key_exists('IDConfiguracionTalegasLugarEntrega', $frm)) {
                    $aTalega["IDConfiguracionTalegasLugarEntrega"] = $frm["IDConfiguracionTalegasLugarEntrega"];
                    $aTalega["FechaEntrega"] = date("Y-m-d H:i:s");
                }

                $aTalega["estado"] = $estado;
                $aTalega['IdSocioRegistra'] = $socioRegistra;

                //print_r($aTalega);
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
                    "IdSocioRegistra" => $socioRegistra
                );

                if (array_key_exists('IDConfiguracionTalegasLugarEntrega', $frm)) {
                    $talegaAdministracion["IDConfiguracionTalegasLugar"] = $frm["IDConfiguracionTalegasLugarEntrega"];
                }

                $idTalegaAdministracion = $dbo->insert($talegaAdministracion, "TalegaAdministracion", "IDTalegaAdministracion");


                $sql_delete = "DELETE FROM TalegaDetalle "
                    . "WHERE IDTalega = '" . SIMNet::reqInt("id") . "' ";
                $qry_delete = $dbo->query($sql_delete);

                $aPropiedad = [];
                if ($estado == 3) $proceso = "la salida";
                else $proceso = "el ingreso";

                $mensaje = "se ha realizado $proceso de su talega con la siguiente información: ";
                $aTextoPropiedades = [];

                foreach ($frm["idPropiedad"] as $index => $idPropiedad) {

                    $aPropiedadHistorico = array(
                        "IDTalegaAdministracion" => $idTalegaAdministracion,
                        "IDTalega" => $id,
                        "IDPropiedadesTalega" => $idPropiedad,
                        "valor" => $frm["propiedad"][$index],
                        "estado" => 1,
                        "fechaRegistro" => date("Y-m-d H:i:s"),
                        "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    $aTextoPropiedades[] = $frm["nombrePropiedad"][$index] . ": " . $frm["propiedad"][$index];

                    $aPropiedadDetalle = array(
                        "IDTalegaAdministracion" => $idTalegaAdministracion,
                        "IDTalega" => $id,
                        "IDPropiedadesTalega" => $idPropiedad,
                        "valor" => $frm["propiedad"][$index],
                        "fechaRegistro" => date("Y-m-d H:i:s"),
                        "idUsuarioRegistra" => SIMUser::get("IDUsuario"),
                    );

                    if($frm["observacion"][$index]){
                        $aPropiedadHistorico['Observacion'] = $frm["observacion"][$index];
                        $aPropiedadDetalle['Observacion'] = $frm["observacion"][$index];
                    }

                    $dbo->insert($aPropiedadHistorico, "TalegaHistorico", "IDTalegaHistorico");
                    $dbo->insert($aPropiedadDetalle, "TalegaDetalle", "IDTalegaDetalle");
                }

                if (count($aTextoPropiedades) > 0 && $frm["idSocio"] > 0) {
                    $textoPropiedades = join(", ", $aTextoPropiedades);
                    $mensaje = $mensaje . " " . $textoPropiedades;
                    SIMUtil::enviar_notificacion_push_general(SIMUser::get("club"), $frm["idSocio"], $mensaje);
                }

                SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroActualizadoCorrectamente', LANGSESSION));
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
