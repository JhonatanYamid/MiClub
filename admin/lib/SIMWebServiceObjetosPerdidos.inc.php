<?php

class SIMWebServiceObjetosPerdidos
{
    public function get_configuracion_objetos_perdidos($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM Club  WHERE IDClub = '" . $IDClub . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $r["IDClub"];

                if ($IDClub == 151) {
                    $configuracion["LabelBotonSolicitarEntrega"] = "Request delivery";
                    $configuracion["LabelConfirmacionEntrega"] = "Are you sure this lost item is yours?";
                    $configuracion["LabelBotonMisSolicitudes"] = "My ads";
                } else {
                    $configuracion["LabelBotonSolicitarEntrega"] = "Solicitar entrega";
                    $configuracion["LabelConfirmacionEntrega"] = "Esta seguro que este objeto perdido es suyo?";
                    $configuracion["LabelBotonMisSolicitudes"] = "Mis Solicitudes";
                }

                $tipodoc = array();
                $response_tipodoc = array();
                $sql_tipodoc = "SELECT * FROM TipoDocumento WHERE 1 ";
                $r_tipodoc = $dbo->query($sql_tipodoc);
                while ($row_tipodoc = $dbo->fetchArray($r_tipodoc)) {
                    $tipodoc["IDTipoDocumento"] = $row_tipodoc["IDTipoDocumento"];
                    $tipodoc["Nombre"] = $row_tipodoc["Nombre"];
                    array_push($response_tipodoc, $tipodoc);
                }

                $configuracion["TipoDocumento"] = $response_tipodoc;

                $estados_obj = array();
                $response_estado_objeto = array();
                $sql_estados_obj = "SELECT * FROM EstadoObjetosPerdidos WHERE Publicar = 'S' ORDER BY Nombre";
                $r_estados_obj = $dbo->query(
                    $sql_estados_obj
                );
                while ($row_estados_obj = $dbo->fetchArray($r_estados_obj)) {
                    $estados_obj["IDEstadoObjetosPerdidos"] = $row_estados_obj["IDEstadoObjetosPerdidos"];
                    $estados_obj["Nombre"] = $row_estados_obj["Nombre"];
                    array_push($response_estado_objeto, $estados_obj);
                }

                $configuracion["EstadoObjetosPerdidos"] = $response_estado_objeto;

                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Correspondencianoestáactivo', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_objetos_perdidos($id_club, $id_categoria = "", $id_objeto_perdido = "", $tag = "", $IDSocio, $IDEstadoObjetosPerdidos)
    {

        $dbo = &SIMDB::get();

        //Socio
        if (!empty($IDSocio)) :
            $sql_solicitudes = "SELECT IDObjetoPerdido FROM ObjetoPerdidoSolicitud WHERE IDSocio = '" . $IDSocio . "' ";
            $r_solicitudes = $dbo->query($sql_solicitudes);
            while ($row_solicitudes = $dbo->fetchArray($r_solicitudes)) {
                $array_id_objeto[] = $row_solicitudes["IDObjetoPerdido"];
            }
            if (count($array_id_objeto)) {
                $id_objetos = implode(",", $array_id_objeto);
            } else {
                $id_objetos = 0;
            }
        //$array_condiciones[] = " IDObjetoPerdido  in (".$id_objetos.") ";
        endif;

        // Seccion Especifica
        if (!empty($id_categoria)) :
            $array_condiciones[] = " IDSeccionObjetosPerdidos  = '" . $id_categoria . "' and IDEstadoObjetosPerdidos  in (1,2) ";
        endif;

        // Seccion Especifica
        if (!empty($id_objeto_perdido)) :
            $array_condiciones[] = " IDObjetoPerdido  = '" . $id_objeto_perdido . "' and IDEstadoObjetosPerdidos  in (1,2) ";
        endif;

        // Tag
        if (!empty($tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $tag . "%' or Descripcion like '%" . $tag . "%') and IDEstadoObjetosPerdidos in (1,2)";
        endif;

        // Tag
        if (!empty($IDEstadoObjetosPerdidos)) :
            $array_condiciones[] = " IDEstadoObjetosPerdidos  = '" . $IDEstadoObjetosPerdidos . "' ";
        else :
            $array_condiciones[] = " IDEstadoObjetosPerdidos  > 0 ";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_clasificado = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM ObjetoPerdido WHERE  IDClub = '" . $id_club . "'" . $condiciones_clasificado . " ORDER BY FechaTrCr DESC";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $objeto["IDObjetoPerdido"] = $r["IDObjetoPerdido"];
                $objeto["IDSocio"] = $r["IDSocio"];
                $objeto["IDCategoria"] = $r["IDSeccionObjetosPerdidos"];
                $objeto["IDClub"] = $r["IDClub"];
                $objeto["IDEstadoObjetosPerdidos"] = $r["IDEstadoObjetosPerdidos"];
                $objeto["EstadoObjetosPerdidos"] = $dbo->getFields("EstadoObjetosPerdidos", "Nombre", "IDEstadoObjetosPerdidos = '" . $r["IDEstadoObjetosPerdidos"] . "'");
                $objeto["Nombre"] = $r["Nombre"];

                $cuerpo = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Descripcion"]);

                $objeto["DescripcionHtml"] = $cuerpo;
                $objeto["FechaInicio"] = $r["FechaInicio"];
                $objeto["FechaFin"] = $r["FechaFin"];

                //verifico si las fotos se estan publicando
                //$response_fotos = array();
                //unset($response_fotos);
                if (empty($r["PublicarFotos"]) || $r["PublicarFotos"] == "S") {
                    $response_fotos = array();
                    //unset($response_fotos);
                    for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])) :
                            $array_dato_foto["Foto"] = OBJETOSPERDIDOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $objeto["Fotos"] = $response_fotos;
                } else {
                    $objeto["Fotos"] = [];
                }


                //Socios que han enviado la solicitud de entrega
                $response_socios = array();
                unset($array_socio);
                $sql_solicitud = "SELECT * FROM  ObjetoPerdidoSolicitud WHERE IDObjetoPerdido = '" . $r["IDObjetoPerdido"] . "'";
                $r_solicitud = $dbo->query($sql_solicitud);
                while ($row_solicitud = $dbo->fetchArray($r_solicitud)) {
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_solicitud["IDSocio"] . "' ", "array");
                    $array_socio["IDSocio"] = $row_solicitud["IDSocio"];
                    $array_socio["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                    $array_socio["Accion"] = $datos_socio["Accion"];
                    array_push($response_socios, $array_socio);
                }

                $objeto["RequeridoPor"] = $response_socios;

                array_push($response, $objeto);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {

            if ($id_club == 151) {
                $respuesta_serv = "Not found";
            } else {
                $respuesta_serv = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            }


            $respuesta["message"] = $respuesta_serv;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_mis_solictudes_objetos_perdidos($id_club, $IDSocio)
    {

        $dbo = &SIMDB::get();

        //Socio
        if (!empty($IDSocio)) :
            $sql_solicitudes = "SELECT IDObjetoPerdido FROM ObjetoPerdidoSolicitud WHERE IDSocio = '" . $IDSocio . "' ";
            $r_solicitudes = $dbo->query($sql_solicitudes);
            while ($row_solicitudes = $dbo->fetchArray($r_solicitudes)) {
                $array_id_objeto[] = $row_solicitudes["IDObjetoPerdido"];
            }
            if (count($array_id_objeto)) {
                $id_objetos = implode(",", $array_id_objeto);
            } else {
                $id_objetos = 0;
            }
            $array_condiciones[] = " IDObjetoPerdido  in (" . $id_objetos . ") ";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_clasificado = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM ObjetoPerdido WHERE IDClub = '" . $id_club . "'" . $condiciones_clasificado . " ORDER BY FechaTrCr DESC";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $objeto["IDObjetoPerdido"] = $r["IDObjetoPerdido"];
                $objeto["IDSocio"] = $r["IDSocio"];
                $objeto["IDCategoria"] = $r["IDSeccionObjetosPerdidos"];
                $objeto["IDClub"] = $r["IDClub"];
                $objeto["IDEstadoObjetosPerdidos"] = $r["IDEstadoObjetosPerdidos"];
                $objeto["Nombre"] = $r["Nombre"];
                $objeto["FechaInicio"] = $r["FechaInicio"];
                $objeto["FechaFin"] = $r["FechaFin"];

                $response_fotos = array();
                for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
                    $campo_foto = "Foto" . $i_foto;
                    if (!empty($r[$campo_foto])) :
                        $array_dato_foto["Foto"] = OBJETOSPERDIDOS_ROOT . $r[$campo_foto];
                        array_push($response_fotos, $array_dato_foto);
                    endif;
                endfor;
                $objeto["Fotos"] = $response_fotos;

                array_push($response, $objeto);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function set_pertenencia($IDClub, $IDObjetoPerdido, $IDSocio)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDObjetoPerdido) && !empty($IDSocio)) {

            $datos_objeto = $dbo->fetchAll("ObjetoPerdido", " IDObjetoPerdido = '" . $IDObjetoPerdido . "' and IDClub = '" . $IDClub . "' ", "array");

            if (!empty($datos_objeto["IDObjetoPerdido"])) {

                $sql_solicitud = $dbo->query("INSERT IGNORE INTO ObjetoPerdidoSolicitud (IDObjetoPerdido, IDSocio, UsuarioTrCr, FechaTrCr) Values ('" . $IDObjetoPerdido . "','" . $IDSocio . "','App',NOW())");
                $sql_estado_solicitud = $dbo->query("UPDATE  ObjetoPerdido SET IDEstadoObjetosPerdidos =  2  WHERE IDObjetoPerdido = '" . $IDObjetoPerdido . "' ");
                //Envio correo
                SIMUtil::notificar_solicitud_objeto_perdido($IDObjetoPerdido, $IDSocio);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Solicitudenviadaconéxito', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionelobjetonoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "O1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_objeto_perdido($IDClub, $IDSocio, $IDCategoria, $Nombre, $Descripcion, $FechaInicio, $IDUsuario, $File = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDUsuario) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion) && !empty($FechaInicio)) {

            //verifico que el socio exista y pertenezca al club
            $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_usuario)) {

                if (isset($File)) {

                    for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
                        $campo_foto = "Foto" . $i_foto;
                        $files = SIMFile::upload($File[$campo_foto], OBJETOSPERDIDOS_DIR, "IMAGE");
                        if (empty($files) && !empty($File[$campo_foto]["name"])) :
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                        $$campo_foto = $files[0]["innername"];
                    endfor;
                } //end if

                $sql_objeto = $dbo->query("INSERT INTO ObjetoPerdido (IDUsuario	, IDSeccionObjetosPerdidos, IDClub, IDEstadoObjetosPerdidos, Nombre, Descripcion, FechaInicio, Foto1, Foto2, Foto3, Foto4, Foto5, Foto6, UsuarioTrCr, FechaTrCr	)
                                                    Values ('" . $IDUsuario . "','" . $IDCategoria . "','" . $IDClub . "','1','" . $Nombre . "','" . $Descripcion . "','" . $FechaInicio . "'
                                                            ,'" . $Foto1 . "','" . $Foto2 . "','" . $Foto3 . "','" . $Foto4 . "','" . $Foto5 . "','" . $Foto6 . "','" . $IDUsuario . "',NOW())");

                $id_objeto = $dbo->lastID();

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelusuarionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "22." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_entrega_objeto_perdido($IDClub, $IDSocio, $IDObjetoPerdido, $TipoReclamante, $NombreParticular, $DocumentoParticular, $IDTipoDocumentoParticular, $Observaciones, $IDUsuario, $File = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDObjetoPerdido) && !empty($IDUsuario)) {

            if (isset($File)) {

                for ($i_foto = 1; $i_foto <= 2; $i_foto++) :
                    $campo_foto = "FotoEntrega" . $i_foto;
                    $files = SIMFile::upload($File[$campo_foto], OBJETOSPERDIDOS_DIR, "IMAGE");
                    if (empty($files) && !empty($File[$campo_foto]["name"])) :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                    $$campo_foto = $files[0]["innername"];
                endfor;
            } //end if

            $sql_objeto = $dbo->query("UPDATE ObjetoPerdido SET TipoReclamante='" . $TipoReclamante . "', NombreParticular='" . $NombreParticular . "',
                                                                                DocumentoParticular='" . $DocumentoParticular . "', IDTipoDocumento='" . $IDTipoDocumentoParticular . "',
                                                                                Observaciones = '" . $Observaciones . "', IDUsuarioEntrega = '" . $IDUsuario . "', IDEstadoObjetosPerdidos = '2', FechaEntrega =  NOW()  WHERE IDObjetoPerdido = '" . $IDObjetoPerdido . "'");

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "22." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_categoria_objetos_perdidos($id_club)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM SeccionObjetosPerdidos  WHERE Publicar = 'S' and IDClub = '" . $id_club . "' " . $condicion . " ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                $seccion["IDClub"] = $r["IDClub"];
                $seccion["IDCategoria"] = $r["IDSeccionObjetosPerdidos"];
                $seccion["Nombre"] = $r["Nombre"];
                $seccion["Descripcion"] = $r["Descripcion"];
                $seccion["SoloIcono"] = $r["SoloIcono"];

                if (!empty($r["Foto"])) :
                    $foto = OBJETOSPERDIDOS_ROOT . $r["Foto"];
                else :
                    $foto = "";
                endif;

                $seccion["Icono"] = $foto;

                array_push($response, $seccion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {

            if ($id_club == 151) {
                $respuesta_serv = "Not found";
            } else {
                $respuesta_serv = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            }
            $respuesta["message"] = $respuesta_serv;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function  

    public function set_edita_objeto_perdido($IDClub, $IDObjetoPerdido, $IDEstadoObjetosPerdidos, $IDCategoria, $Nombre, $Descripcion, $FechaInicio, $IDUsuario, $File = "", $UrlFoto1, $UrlFoto2, $UrlFoto3, $UrlFoto4, $UrlFoto5)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDObjetoPerdido) && !empty($IDUsuario) && !empty($IDCategoria) && !empty($Nombre) && !empty($Descripcion)) {

            //verifico que el socio exista y pertenezca al club
            $id_objeto = $dbo->getFields("ObjetoPerdido", "IDObjetoPerdido", "IDObjetoPerdido = '" . $IDObjetoPerdido . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_objeto)) {

                //actualizao la fotos en blanco para que queden solo las enviadas
                $sql_objeto = "Update ObjetoPerdido set Foto1='',Foto2='',Foto3='',Foto4='',Foto5=''
                                        Where IDObjetoPerdido = '" . $IDObjetoPerdido . "'";
                $dbo->query($sql_clasificado);

                if (isset($File)) {
                    for ($i_foto = 1; $i_foto <= 6; $i_foto++) :
                        $campo_foto = "Foto" . $i_foto;
                        $files = SIMFile::upload($File[$campo_foto], CLASIFICADOS_DIR, "IMAGE");
                        if (empty($files) && !empty($File[$campo_foto]["name"])) :
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        else :
                            if (!empty($files[0]["innername"])) :
                                $actualiza_foto .= " , " . $campo_foto . " = '" . $files[0]["innername"] . "'";
                            endif;
                        endif;

                    endfor;
                } //end if

                if (!empty($UrlFoto1)) {
                    $actualiza_foto .= " , Foto1 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto1) . "'";
                }

                if (!empty($UrlFoto2)) {
                    $actualiza_foto .= " , Foto2 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto2) . "'";
                }

                if (!empty($UrlFoto3)) {
                    $actualiza_foto .= " , Foto3 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto3) . "'";
                }

                if (!empty($UrlFoto4)) {
                    $actualiza_foto .= " , Foto4 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto4) . "'";
                }

                if (!empty($UrlFoto5)) {
                    $actualiza_foto .= " , Foto5 = '" . str_replace(OBJETOSPERDIDOS_ROOT, "", $UrlFoto5) . "'";
                }

                $sql_objeto = "UPDATE ObjetoPerdido
                                                    set IDSeccionObjetosPerdidos = '" . $IDCategoria . "', IDEstadoObjetosPerdidos = '" . $IDEstadoClasificado . "', Nombre = '" . $Nombre . "', Descripcion = '" . $Descripcion . "',
                                                    FechaInicio = '" . $FechaInicio . "',
                                                    UsuarioTrEd = '" . $IDUsuario . "', FechaTrEd = NOW()  " . $actualiza_foto . "
                                                    Where IDObjetoPerdido = '" . $IDObjetoPerdido . "'";

                $dbo->query($sql_objeto);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionelobjetonoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "O2." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_estado_objetos_perdidos($IDClub)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM EstadoObjetosPerdidos WHERE Publicar = 'S' ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $estado_pqr["IDEstadoObjetosPerdidos"] = $r["IDEstadoObjetosPerdidos"];
                $estado_pqr["Nombre"] = $r["Nombre"];
                array_push($response, $estado_pqr);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function
}
