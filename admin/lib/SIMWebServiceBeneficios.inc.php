<?php

class SIMWebServiceBeneficios
{
    public function get_beneficio($IDClub, $IDCategoria, $IDBeneficio, $Tag, $IDSocio)
    {

        if ($IDClub == 17) :
            $respuesta = SIMWebServiceFedegolf::get_beneficio_categoria($IDClub, $IDCategoria, $IDBeneficio, $Tag, $IDSocio);
            return $respuesta;
        endif;
        $dbo = &SIMDB::get();

        if ($IDClub == 227) :

            $token = "SELECT * FROM Socio WHERE IDSocio='$IDSocio' LIMIT 1";
            $datos_socio = $dbo->query($token);
            while ($row = $dbo->fetchArray($datos_socio)) {
                $token = $row["TokenCountryMedellin"];
            }
            $emprendedor = "false";
            $misEmpresas = "false";


            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
            $respuesta = SIMWebServiceCountryMedellin::App_ConsultarDirectorioInteraccion($token, $IDCategoria, $emprendedor, $misEmpresas);
            return $respuesta;
        endif;


        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }


        // Seccion Especifica
        if (!empty($IDCategoria)) :
            $array_condiciones[] = " IDSeccionBeneficio  = '" . $IDCategoria . "' ";
        endif;

        // Seccion Especifica
        if (!empty($IDBeneficio)) :
            $array_condiciones[] = " IDBeneficio  = '" . $IDBeneficio . "' ";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Nombre  like '%" . $Tag . "%' or Descripcion like '%" . $Tag . "%')";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_clasificado = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM Beneficio WHERE Publicar = 'S' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and IDClub = '" . $IDClub . "'" . $condiciones_clasificado . $condicion . " ORDER BY FechaInicio DESC";

        //echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($IDSocio)) {
                    $mostrar_beneficio = SIMWebServiceBeneficios::verifica_ver_beneficio($r, $IDSocio);
                } elseif (!empty($IDUsuario)) {
                    $mostrar_beneficio = SIMWebServiceBeneficios::verifica_ver_beneficio($r, $IDUsuario);
                }
                if ($mostrar_beneficio == 1) {

                    $beneficio["IDBeneficio"] = $r["IDBeneficio"];
                    $beneficio["IDCategoria"] = $r["IDSeccionBeneficio"];
                    $beneficio["IDClub"] = $r["IDClub"];
                    $beneficio["Nombre"] = $r["Nombre"];
                    $beneficio["Introduccion"] = $r["Introduccion"];
                    $beneficio["Descripcion"] = $r["Descripcion"];

                    $cuerpo_beneficio = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["DescripcionHtml"]);
                    //Documentos adjuntos
                    if (!empty($r["Adjunto1File"])) :
                        $cuerpo_beneficio .= "<br><a href='" . CLASIFICADOS_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                    endif;

                    if ($IDClub == 265) {
                        $Prefijo = "+598";
                    }

                    $beneficio["DescripcionHtml"] = $cuerpo_beneficio;
                    if ($r["Telefono"] == 0) {
                        $telefono = "";
                    } else {
                        $telefono =  $Prefijo . $r["Telefono"];
                    }




                    if (!empty($r["PaginaWeb"])) {
                        $PaginaWeb =  $r["PaginaWeb"];
                    } else {
                        $PaginaWeb = "";
                    }

                    $beneficio["PaginaWeb"] = $PaginaWeb;
                    $beneficio["Telefono"] = $telefono;
                    $beneficio["Latitud"] = $r["Latitud"];
                    $beneficio["Longitud"] = $r["Longitud"];
                    $beneficio["FechaInicio"] = $r["FechaInicio"];
                    $beneficio["FechaFin"] = $r["FechaFin"];
                    $beneficio["OcultarTelefono"] = $r["OcultarTelefono"];
                    $beneficio["OcultarPaginaWeb"] = $r["OcultarPaginaWeb"];
                    $beneficio["OcultarMapa"] = $r["OcultarMapa"];
                    $beneficio["OcultarBotonRuta"] = $r["OcultarBotonRuta"];
                    $beneficio["OcultarImagen"] = $r["OcultarImagen"];
                    $beneficio["OcultarUrlDetalle"] = $r["OcultarUrlDetalle"];
                    $beneficio["OcultarTelefonoDetalle"] = $r["OcultarTelefonoDetalle"];
                    $beneficio["MostrarCorreo"] = $r["MostrarCorreo"];
                    $beneficio["Correo"] = $r["Correo"];


                    $response_fotos = array();
                    for ($i_foto = 1; $i_foto <= 1; $i_foto++) :
                        $campo_foto = "Foto" . $i_foto;
                        if (!empty($r[$campo_foto])) :
                            $array_dato_foto["Foto"] = CLASIFICADOS_ROOT . $r[$campo_foto];
                            array_push($response_fotos, $array_dato_foto);
                        endif;
                    endfor;
                    $beneficio["Fotos"] = $response_fotos;


                    //foto portada
                    if (!empty($r["FotoPortada"])) :
                        if (strstr(strtolower($r["FotoPortada"]), "http://")) {
                            $FotoPortada = $r["FotoPortada"];
                        } else {
                            $FotoPortada = CLASIFICADOS_ROOT . $r["FotoPortada"];
                        }

                    else :
                        $FotoPortada = "";
                    endif;

                    $beneficio["FotoPortada"] = $FotoPortada;


                    array_push($response, $beneficio);
                }
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

    public function verifica_ver_beneficio($r, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $mostrar_beneficio = 1;
        $IDBeneficio = $r["IDBeneficio"];

        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T' || $r["DirigidoA"] == 'E') && ($r["DirigidoAGeneral"] == "S" || $r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS" || $r["DirigidoAGeneral"] == "EE")) {
            if ($r["DirigidoAGeneral"] == "SE") {
                $array_invitados = explode("|||", $r["InvitadoSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_beneficio = 1;
                } else {
                    $mostrar_beneficio = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "EE") {
                $array_invitados = explode("|||", $r["UsuarioSeleccion"]);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $array_socios_encuesta[] = $array_datos_invitados[1];
                        }
                    }
                }
                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_beneficio = 1;
                } else {
                    $mostrar_beneficio = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {
                $ //si va dirigido a varios grupos

                $arregloGrupos = explode("|||", $r["InvitadoSeleccionGrupo"]);

                if (count($arregloGrupos) > 0) {
                    foreach ($arregloGrupos as $id_grupo => $datos_grupo) {
                        if (!empty($datos_grupo)) {
                            $array_datos_grupo = explode("-", $datos_grupo);
                            $mostrar_beneficio = SIMWebserviceApp::verificar_socio_grupo($IDSocio, $array_datos_grupo[1]);
                        }
                        if ($mostrar_beneficio == 1) {
                            break;
                        }
                    }
                }
            }
        }

        if ($r["UnaporSocio"] == 'S') {
            $sql_resp = "Select IDBeneficio From Beneficio Where IDSocio = '" . $IDSocio . "' and IDBeneficio = '" . $IDBeneficio . "' Limit 1";
            $r_resp = $dbo->query($sql_resp);
            if ($dbo->rows($r_resp) > 0) {
                $mostrar_beneficio = 0;
            }
        }

        return $mostrar_beneficio;
    }

    public function get_seccion_socio_beneficio($id_club, $id_socio = "")
    {
        $dbo = &SIMDB::get();
        $response = array();
        $contador_resultado = 0;

        unset($seccion);
        $nombre_modulo = "";
        //Secciones Galeria
        $sql = "SELECT * FROM SeccionBeneficio WHERE Publicar = 'S' and IDClub = '" . $id_club . "' ORDER BY Nombre";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $contador_resultado++;
            $seccion["IDClub"] = $r["IDClub"];
            $nombre_modulo = "Beneficio";
            $seccion["IDSeccion"] = $r["IDSeccionBeneficio"];
            $seccion["Nombre"] = $r["Nombre"];
            $seccion["Descripcion"] = $r["Descripcion"];

            // verifico si es de preferencia del socio
            if (!empty($id_socio)) :
                $sql_preferencia = "Select * From SocioSeccionBeneficio Where IDSocio = '" . $id_socio . "' and IDSeccionBeneficio = '" . $seccion["IDSeccion"] . "'";
                $result_preferencia = $dbo->query($sql_preferencia);
                if ($dbo->rows($result_preferencia) > 0) :
                    $seccion["PreferenciaSocio"] = "S";
                else :
                    $seccion["PreferenciaSocio"] = "N";
                endif;
            else :
                $seccion["PreferenciaSocio"] = "N";
            endif;

            array_push($response, $seccion);
        } //end while

        $message = $contador_resultado . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
        if ($contador_resultado > 0) {
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

    public function set_preferencias_beneficio($IDClub, $IDSocio, $SeccionesBeneficio)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {
                //borro las secciones asociadas al socio
                $sql_borra_seccion_gal = $dbo->query("Delete From SocioSeccionBeneficio Where IDSocio  = '" . $IDSocio . "'");

                if (!empty($SeccionesBeneficio)) :
                    $array_secciones_benef = explode(",", $SeccionesBeneficio);
                    if (count($array_secciones_benef) > 0) :
                        foreach ($array_secciones_benef as $id_seccion) :
                            // verifico que la seccion sea del club
                            $id_seccion = $dbo->getFields("SeccionBeneficio", "IDSeccionBeneficio", "IDClub = '" . $IDClub . "' and IDSeccionBeneficio = '" . $id_seccion . "'");
                            if (!empty($id_seccion)) :
                                $sql_seccion_cla = $dbo->query("Insert Into SocioSeccionBeneficio (IDSocio, IDSeccionBeneficio) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                            endif;
                        endforeach;
                    endif;
                endif;

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "7." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }


    public function get_categoria_beneficio($id_club, $IDSocio)
    {
        $dbo = &SIMDB::get();
        if ($id_club == 17) :
            $respuesta = SIMWebServiceFedegolf::get_categoria_beneficio($id_club);
            return $respuesta;
        endif;

        if ($id_club == 227) :

            $token = "SELECT * FROM Socio WHERE IDSocio='$IDSocio' LIMIT 1";
            $datos_socio = $dbo->query($token);
            while ($row = $dbo->fetchArray($datos_socio)) {
                $token = $row["TokenCountryMedellin"];
            }
            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
            $respuesta = SIMWebServiceCountryMedellin::App_ConsultarSectoresEconomicos($token, $id_club);
            return $respuesta;
        endif;



        $response = array();
        $sql = "SELECT * FROM SeccionBeneficio  WHERE Publicar = 'S' and IDClub = '" . $id_club . "' " . $condicion . " ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                $seccion["IDClub"] = $r["IDClub"];
                $seccion["IDCategoria"] = $r["IDSeccionBeneficio"];
                $seccion["Nombre"] = $r["Nombre"];
                $seccion["Descripcion"] = $r["Descripcion"];
                $seccion["SoloIcono"] = $r["SoloIcono"];

                if (!empty($r["Foto"])) :
                    $foto = CLASIFICADOS_ROOT . $r["Foto"];
                else :
                    $foto = "";
                endif;
                //el id de categoria se guardo en el campo

                $seccion["Icono"] = $foto;

                array_push($response, $seccion);
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

    public function get_configuracion_beneficios($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();


        $sql = "SELECT IDClub,LabelBotonFavoritos,LabelHeaderFavoritos,TipoImagenPortada,LabelBotonEmailDetalle,TextoBuscadorBeneficios FROM ConfiguracionBeneficios  WHERE IDClub = '" . $IDClub . "'  And Activo='S'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $r["IDClub"];
                $configuracion["LabelBotonFavoritos"] = $r["LabelBotonFavoritos"];
                $configuracion["LabelHeaderFavoritos"] = $r["LabelHeaderFavoritos"];
                $configuracion["TipoImagenPortada"] = $r["TipoImagenPortada"];
                $configuracion["LabelBotonEmailDetalle"] = $r["LabelBotonEmailDetalle"];
                $configuracion["TextoBuscadorBeneficios"] = $r["TextoBuscadorBeneficios"];
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracion;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Configuracionnoestáactivo', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function 


}
