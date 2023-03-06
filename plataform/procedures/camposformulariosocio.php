<?php
    SIMReg::setFromStructure(array(
        "title" => "CamposFormularioSocio",
        "table" => "CamposFormularioSocio",
        "key" => "IDCamposFormularioSocio",
        "mod" => "camposformulariosocio"
    ));


    $script = "camposformulariosocio";

    //extraemos las variables
    $table = SIMReg::get("table");
    $key = SIMReg::get("key");
    $mod = SIMReg::get("mod");

    // Funcion para obteber los datos de Diccionario
    function get_CamposFormularioSocio($IDCamposFormularioSocio, $dbo)
    {
        $SqlCategoria = "SELECT * FROM CamposFormularioSocio WHERE CamposFormularioSocio.IDCamposFormularioSocio = $IDCamposFormularioSocio";
        $resultCategoria = $dbo->query($SqlCategoria);
        $RowCategoria = $dbo->assoc($resultCategoria);
        return $RowCategoria;
    }

    //Verificar permisos
    SIMUtil::verificar_permiso( $mod , SIMUser::get("IDPerfil") );

    //creando las notificaciones que llegan en el parametro m de la URL
    SIMNotify::capture( SIMResources::$mensajes[ SIMNet::req("m") ]["msg"] , SIMResources::$mensajes[ SIMNet::req("m") ]["type"] );

    switch ( SIMNet::req( "action" ) ) {

        case "add" :
            $view = "views/".$script."/form.php";
            $newmode = "insert";
            $titulo_accion = "Crear";
        break;

        case "insert" :

            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
                //los campos al final de las tablas
                $frm = SIMUtil::varsLOG($_POST);

                if($frm['TipoOpciones'] == 1){
                    if($frm['OpcionesSeleccion'] != ''){
                        $frm['OpcionesSeleccion'] = str_replace(" = ","=",$frm['OpcionesSeleccion']);
                        $frm['OpcionesSeleccion'] = str_replace(" | ","|",$frm['OpcionesSeleccion']);
                    }else{
                        SIMHTML::jsAlert("Error:Lasopcionesnopuedenservacias");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                        exit;
                    }
                }

                if($frm['TipoOpciones'] == 2){

                    $tablaCampo = $frm['NombreTabla'];
                    $campoKey = $frm['CampoName'];
                    $campoValue = $frm['CampoValue'];
                    $where = $frm['Condicion'] != "" ? "WHERE ".$frm['Condicion'] : "";
                    $where = str_replace("?IDClub",SIMUser::get("club"),$where);
                        
                    $sqlOpciones = "SELECT $campoKey,$campoValue FROM $tablaCampo $where ORDER BY $campoKey";

                    $resOpciones = $dbo->query($sqlOpciones);

                    if (!$resOpciones){
                        SIMHTML::jsAlert("Error:Laconsultanoesvalida");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                        exit;
                    }
                }
                
                if($frm['TipoOpciones'] == 3){
                    if($frm['ConsultaBD'] != ''){

                        $qryNew = str_replace("?IDClub",SIMUser::get("club"),$frm['ConsultaBD']);
                        $resConsulta = $dbo->query($qryNew);

                        if (!$resConsulta){
                            SIMHTML::jsAlert("Error:Laconsultanoesvalida");
                            SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                            exit;
                        }
                    }else{
                        SIMHTML::jsAlert("Error:Laconsultanoesvalida");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                        exit;
                    }
                }

                if($frm['TipoOpciones'] == 4){
                    if($frm['OpcionesClase'] != ''){
                        $opClase = $frm['OpcionesClase'];
                        $validaCl = strpos($opClase, ";");
                        if ($validaCl === false)
                            $opClase .=";";

                        try
                        {
                            eval('$arrOpciones = '.$opClase);
                        }
                        catch (ParseError $err)
                        {
                            SIMHTML::jsAlert("Error:Elllamadoalaclaseesincorrecto");
                            SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                            exit;
                        }
                    }else{
                        SIMHTML::jsAlert("Error:Lasopcionesnopuedenservacias");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                        exit;
                    }
                }

                //insertamos los datos
                $id = $dbo->insert($frm, $table, $key);

                SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
                SIMHTML::jsRedirect($script . ".php");
            } else
                exit;

            break;

        break;


        case "edit":
            $frm = $dbo->fetchById( $table , $key , SIMNet::reqInt("id") , "array" );
            $view = "views/".$script."/form.php";
            $newmode = "update";
            $titulo_accion = "Actualizar";

        break ;

        case "update" :

            if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
                //los campos al final de las tablas
                $frm = SIMUtil::varsLOG($_POST);

                if($frm['TipoOpciones'] == 1){
                    if($frm['OpcionesSeleccion'] != ''){
                        $frm['OpcionesSeleccion'] = str_replace(" = ","=",$frm['OpcionesSeleccion']);
                        $frm['OpcionesSeleccion'] = str_replace(" | ","|",$frm['OpcionesSeleccion']);
                    }else{
                        SIMHTML::jsAlert("Error:Lasopcionesnopuedenservacias");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                        exit;
                    }
                }

                if($frm['TipoOpciones'] == 2){

                    $tablaCampo = $frm['NombreTabla'];
                    $campoKey = $frm['CampoName'];
                    $campoValue = $frm['CampoValue'];
                    $where = $frm['Condicion'] != "" ? "WHERE ".$frm['Condicion'] : "";
                    $where = str_replace("?IDClub",SIMUser::get("club"),$where);
                        
                    $sqlOpciones = "SELECT $campoKey,$campoValue FROM $tablaCampo $where ORDER BY $campoKey";

                    $resOpciones = $dbo->query($sqlOpciones);

                    if (!$resOpciones){
                        SIMHTML::jsAlert("Error:Laconsultanoesvalida");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                        exit;
                    }
                }
                
                if($frm['TipoOpciones'] == 3){
                    if($frm['ConsultaBD'] != ''){

                        $qryNew = str_replace("?IDClub",SIMUser::get("club"),$frm['ConsultaBD']);
                        $resConsulta = $dbo->query($qryNew);

                        if (!$resConsulta){
                            SIMHTML::jsAlert("Error:Laconsultanoesvalida");
                            SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                            exit;
                        }
                    }else{
                        SIMHTML::jsAlert("Error:Laconsultanoesvalida");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                        exit;
                    }
                }

                if($frm['TipoOpciones'] == 4){
                    if($frm['OpcionesClase'] != ''){
                        $opClase = $frm['OpcionesClase'];
                        $validaCl = strpos($opClase, ";");
                        if ($validaCl === false)
                            $opClase .=";";

                        try
                        {
                            eval('$arrOpciones = '.$opClase);
                        }
                        catch (ParseError $err)
                        {
                            SIMHTML::jsAlert("Error:Elllamadoalaclaseesincorrecto");
                            SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                            exit;
                        }
                    }else{
                        SIMHTML::jsAlert("Error:Lasopcionesnopuedenservacias");
                        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . SIMNet::reqInt("id"));
                        exit;
                    }
                }

                $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

                $frm = $dbo->fetchById($table, $key, $id, "array");

                SIMHTML::jsAlert("RegistroGuardadoCorrectamente");
                SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
            } else
                exit;

            break;

        break;

        case "search" :
            $view = "views/".$script."/list.php";
        break;
        
        default:

            $view = "views/".$script."/list.php";
    } // End switch

    if( empty( $view ) )
        $view = "views/".$script."/form.php";
?>