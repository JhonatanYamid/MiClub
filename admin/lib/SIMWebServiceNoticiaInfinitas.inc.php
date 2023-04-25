<?php

class SIMWebServiceNoticiaInfinitas
{
    public function get_configuracion_noticias_infinitas($IDClub, $IDSocio, $IDModulo)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
        $datos_config_not = $dbo->fetchAll("ConfiguracionNoticiasInfinita", " IDClub = '" . $IDClub . "' and IDModulo= '" . $IDModulo . "' ", "array");
        $sql_modulo = "Select IDModulo,Titulo,TituloLateral From ClubModulo Where IDModulo = '" . $IDModulo . "' ";
        $r_modulo = $dbo->query($sql_modulo);
        while ($row_modulo = $dbo->fetchArray($r_modulo)) {
            $array_modulo[$row_modulo["IDModulo"]] = $row_modulo["TituloLateral"];
        }



        $configuracion["IDClub"] = $IDClub;
        if (empty($array_modulo["3"])) {
            $configuracion["MisNoticias"] = SIMUtil::get_traduccion('', '', 'MisNoticias', LANG);
        } else {
            $configuracion["MisNoticias"] = $array_modulo["3"];
        }

        $configuracion["PermiteLikesNoticias"] = $datos_config_not["PermiteLikeNoticia1"];
        $configuracion["PermiteComentariosNoticias"] = $datos_config_not["PermiteComentarioNoticia1"];
        $configuracion["PermiteIconoComentariosNoticias"] = $datos_config_not["PermiteIconoComentariosNoticias"];

        if (!empty($datos_config_not["IconoLikeNoticias"])) {
            $configuracion["ImagenLike"] = CLUB_ROOT . $datos_config_not["IconoLikeNoticias"];
        } else {
            $configuracion["ImagenLike"] = "";
        }

        if (!empty($datos_config_not["IconoUnLikeNoticias"])) {
            $configuracion["ImagenUnlike"] = CLUB_ROOT . $datos_config_not["IconoUnLikeNoticias"];
        } else {
            $configuracion["ImagenUnlike"] = "";
        }

        if (!empty($datos_config_not["ImagenComentarios"])) {
            $configuracion["ImagenComentarios"] = CLUB_ROOT . $datos_config_not["ImagenComentarios"];
        } else {
            $configuracion["ImagenComentarios"] = "";
        }

        $TipoImagenNoticias = $datos_club["TipoImagenNoticias"];
        $configuracion["TipoImagenNoticias"] = $TipoImagenNoticias;
        $configuracion["TipoInicio"] = $datos_modulo["TipoSeccion"];

        array_push($response, $configuracion);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } // fin function


    public function get_seccion_noticias_infinitas($id_club, $id_socio = "", $id_usuario = "", $TipoApp = "", $Version = "", $IDModulo)
    {
        if (!empty($id_socio)) :
        //$condicion = " and SS.IDSeccion = S.IDSeccion and IDSocio = '" . $id_socio . "' ";
        //$tabla_join = ", SocioSeccion".$Version." SS ";
        endif;

        $dbo = &SIMDB::get();
        $response = array();

        $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $id_socio . "'");
        if ($id_club == 36 && $TipoSocio == "Estudiante") {
            $condicion_noticia .= " and TipoSocio = 'Estudiante'";
        } else {
            $condicion_noticia .= " and TipoSocio <> 'Estudiante'";
        }

        if ($TipoApp == "Socio") {
            $condicion_seccion .= " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } else {
            $condicion_seccion .= " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT S.* FROM SeccionNoticiaInfinita" . $Version . " S " . $tabla_join . " WHERE S.Publicar = 'S' and S.IDClub = '" . $id_club . "' and IDModulo = '" . $IDModulo . "'  " . $condicion . " " . $condicion_seccion . " ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                $id_noticia = $dbo->getFields("NoticiaInfinita" . $Version, "IDNoticiaInfinita", "IDSeccionNoticiaInfinita = '" . $r["IDSeccionNoticiaInfinita"] . "' and Publicar = 'S' " . $condicion_noticia);
                if (!empty($id_noticia)) :
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDSeccion"] = $r["IDSeccionNoticiaInfinita"];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];
                    $seccion["Icono"] = IMGNOTICIA_ROOT . $r["IconoFile"];
                    $seccion["SoloIcono"] = $r["SoloIcono"];
                    array_push($response, $seccion);
                endif;
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

    public function get_noticias_infinitas($id_club, $id_seccion = "", $id_socio = "", $tag = "", $Version = "", $IDUsuario = "", $IDModulo)
    {

        $dbo = &SIMDB::get();

        $datos_config_not = $dbo->fetchAll("ConfiguracionNoticiasInfinita", " IDClub = '$id_club' and IDModulo= '$IDModulo' ", "array");


        if (!empty($IDUsuario)) {

            // Secciones Empleado
            if (!empty($id_empleado)) :
                $sql_seccion_empleado = $dbo->query("Select * From UsuarioSeccion Where IDUsuario = '" . $id_usuario . "'");
                while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)) :
                    $array_secciones_empleado[] = $row_seccion["IDSeccion"];
                endwhile;

                if (count($array_secciones_empleado) > 0) :
                    $id_secciones = implode(",", $array_secciones_empleado);
                    $array_condiciones[] = " NI.IDSeccionNoticiaInfinita in(" . $id_secciones . ") ";
                endif;
            endif;

            // Seccion Especifica
            if (!empty($id_seccion)) :
                $array_condiciones[] = " NI.IDSeccionNoticiaInfinita  = '" . $id_seccion . "'";
            endif;

            // Tag
            if (!empty($tag)) :
                $array_condiciones[] = " (Titular  like '%" . $tag . "%' or Introduccion like '%" . $tag . "%' or Cuerpo like '%" . $tag . "%')";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_noticia = " and " . $condiciones;
            endif;

            if ($id_club == 25) {
                $orden = " ORDER BY Orden ASC, FechaInicio";
            } else {
                $orden = " ORDER BY FechaInicio DESC, Orden";
            }

            $response = array();
            $sql = "SELECT NI.*
                  FROM NoticiaInfinita NI, SeccionNoticiaInfinita SNI
                  WHERE  NI.IDSeccionNoticiaInfinita = SNI.IDSeccionNoticiaInfinita and SNI.IDModulo = '" . $IDModulo . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() AND (NI.DirigidoA = 'E' or NI.DirigidoA = 'T') and NI.Publicar = 'S' and IDClub = '" . $id_club . "'" . $condiciones_noticia . $orden;
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                       $idgrupo=$r["IDGrupoSocio"];
                   //evaluo si el socio esta en el grupo especifico
              $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$idgrupo'"); //evaluamos los grupos pero hay que recorrer los resultados
                $array_socios = explode("|||", $SociosGrupo);
                if (count($array_socios) >= 0) {
                    foreach ($array_socios as $id_socios => $datos_socio) {
                        $array_socios_noticias[] = $datos_socio;
                    }
                }
                 
                 if($r["IDGrupoSocio"]==0){
                   $array_socios_noticias[] = $IDUsuario;
                 }
                if (in_array($IDUsuario, $array_socios_noticias)) {  
                 unset($array_socios_noticias);
                 $idgrupo="";
 
                 

                    $noticia["IDClub"] = $r["IDClub"];
                    $noticia["IDSeccion"] = $r["IDSeccionNoticiaInfinita"];
                    $noticia["IDNoticia"] = $r["IDNoticiaInfinita"];
                    $noticia["Titular"] = $r["Titular"];
                    $noticia["Introduccion"] = $r["Introduccion"];
                    $noticia["ParaPublicidad"] = $r["ParaPublicidad"];


                    $cuerpo_noticia = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Cuerpo"]);

                    //Documentos adjuntos
                    if (!empty($r["Adjunto1File"])) :
                        $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                    endif;
                    if (!empty($r["Adjunto2File"])) :
                        $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto2File"] . "' >" . $r["Adjunto2File"] . '</a>';
                    endif;

                    $noticia["Cuerpo"] = $cuerpo_noticia;

                    $noticia["Fecha"] = $r["FechaInicio"];

                    if ($datos_config_not[MostrarFecha] == 0) :
                        $noticia["Fecha"] = "";
                    endif;

                    if (!empty($r["NoticiaFile"])) :
                        if (strstr(strtolower($r["NoticiaFile"]), "http://")) {
                            $foto1 = $r["NoticiaFile"];
                        } else {
                            $foto1 = IMGNOTICIA_ROOT . $r["NoticiaFile"];
                        }

                    //$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
                    else :
                        $foto1 = "";
                    endif;

                    if (!empty($r["FotoDestacada"])) :
                        if (strstr(strtolower($r["FotoDestacada"]), "http://")) {
                            $foto2 = $r["FotoDestacada"];
                        } else {
                            $foto2 = IMGNOTICIA_ROOT . $r["FotoDestacada"];
                        }

                    //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                    else :
                        $foto2 = "";
                    endif;

                    if (!empty($r["FotoPortada"])) :
                        if (strstr(strtolower($r["FotoPortada"]), "http://")) {
                            $FotoPortada = $r["FotoPortada"];
                        } else {
                            $FotoPortada = IMGNOTICIA_ROOT . $r["FotoPortada"];
                        }

                    //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                    else :
                        $FotoPortada = "";
                    endif;

                    $noticia["Foto"] = $foto1;
                    $noticia["Foto2"] = $foto2;
                    $noticia["FotoPortada"] = $FotoPortada;

                    array_push($response, $noticia);
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

        } else {
            // Secciones Socio
            if (!empty($id_socio) && $id_seccion == "") :



                $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $id_socio . "'");
                if ($id_club == 36 && $TipoSocio == "Estudiante") {
                    $array_condiciones[] = " TipoSocio = 'Estudiante'";
                } else {
                    $array_condiciones[] = " (TipoSocio = '' or TipoSocio = 'Socio' )";
                }
            endif;

            // Seccion Especifica
            if (!empty($id_seccion)) :
                $array_condiciones[] = " NI.IDSeccionNoticiaInfinita  = '" . $id_seccion . "'";
            endif;

            // Tag
            if (!empty($tag)) :
                $array_condiciones[] = " (Titular  like '%" . $tag . "%' or Introduccion like '%" . $tag . "%' or Cuerpo like '%" . $tag . "%')";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_noticia = " and " . $condiciones;
            endif;

            if ($id_club == 25) {
                $orden = " ORDER BY Orden ASC, FechaInicio";
            } else {
                $orden = " ORDER BY FechaInicio DESC, Orden";
            }

            $response = array();

            $sql = "SELECT NI.*
                  FROM NoticiaInfinita NI, SeccionNoticiaInfinita SNI
                  WHERE  NI.IDSeccionNoticiaInfinita = SNI.IDSeccionNoticiaInfinita and SNI.IDModulo = '" . $IDModulo . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() AND (NI.DirigidoA = 'S' or NI.DirigidoA = 'T') and NI.Publicar = 'S' and NI.IDClub = '" . $id_club . "'" . $condiciones_noticia . $orden;


            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                             $idgrupo=$r["IDGrupoSocio"];
                   //evaluo si el socio esta en el grupo especifico
              $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$idgrupo'"); //evaluamos los grupos pero hay que recorrer los resultados
                $array_socios = explode("|||", $SociosGrupo);
                if (count($array_socios) >= 0) {
                    foreach ($array_socios as $id_socios => $datos_socio) {
                        $array_socios_noticias[] = $datos_socio;
                    }
                }
                 
                 if($r["IDGrupoSocio"]==0){
                   $array_socios_noticias[] = $IDSocio;
                 }
                if (in_array($IDSocio, $array_socios_noticias)) {  
                 unset($array_socios_noticias);
                 $idgrupo="";
                 $ver_registro = 1;
                 
                    if ($ver_registro = 1) {
                        $noticia["IDClub"] = $r["IDClub"];
                        $noticia["IDSeccion"] = $r["IDSeccionNoticiaInfinita"];
                        $noticia["IDNoticia"] = $r["IDNoticiaInfinita"];
                        $noticia["Titular"] = $r["Titular"];
                        $noticia["Introduccion"] = $r["Introduccion"];
                        $noticia["ParaPublicidad"] = $r["ParaPublicidad"];
                        $noticia["IDModulo"] = $r["IDModulo"];

                        $cuerpo_noticia = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Cuerpo"]);

                        //Documentos adjuntos
                        if (!empty($r["Adjunto1File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                        //$cuerpo_noticia .= '<iframe src="http://docs.google.com/gview?url='.IMGNOTICIA_ROOT . $r[ "Adjunto1File" ].'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';

                        endif;
                        if (!empty($r["Adjunto2File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto2File"] . "' >" . $r["Adjunto2File"] . '</a>';
                        //$cuerpo_noticia .= '<iframe src="http://docs.google.com/gview?url='.IMGNOTICIA_ROOT . $r[ "Adjunto2File" ].'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
                        endif;
                        if (!empty($r["Adjunto3File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto3File"] . "' >" . $r["Adjunto3File"] . '</a>';
                        //$cuerpo_noticia .= '<iframe src="http://docs.google.com/gview?url='.IMGNOTICIA_ROOT . $r[ "Adjunto3File" ].'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
                        endif;
                        if (!empty($r["Adjunto4File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto4File"] . "' >" . $r["Adjunto4File"] . '</a>';
                        //$cuerpo_noticia .= '<iframe src="http://docs.google.com/gview?url='.IMGNOTICIA_ROOT . $r[ "Adjunto4File" ].'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
                        endif;
                        if (!empty($r["Adjunto5File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto5File"] . "' >" . $r["Adjunto5File"] . '</a>';
                        //$cuerpo_noticia .= '<iframe src="http://docs.google.com/gview?url='.IMGNOTICIA_ROOT . $r[ "Adjunto5File" ].'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
                        endif;
                        if (!empty($r["Adjunto6File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto6File"] . "' >" . $r["Adjunto6File"] . '</a>';
                        //$cuerpo_noticia .= '<iframe src="http://docs.google.com/gview?url='.IMGNOTICIA_ROOT . $r[ "Adjunto6File" ].'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
                        endif;
                        if (!empty($r["Adjunto7File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto7File"] . "' >" . $r["Adjunto7File"] . '</a>';
                        //$cuerpo_noticia .= '<iframe src="http://docs.google.com/gview?url='.IMGNOTICIA_ROOT . $r[ "Adjunto7File" ].'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
                        endif;
                        if (!empty($r["Adjunto8File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto8File"] . "' >" . $r["Adjunto8File"] . '</a>';
                        //$cuerpo_noticia .= '<iframe src="http://docs.google.com/gview?url='.IMGNOTICIA_ROOT . $r[ "Adjunto8File" ].'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
                        endif;

                        $noticia["Cuerpo"] = $cuerpo_noticia;


                        $noticia["Fecha"] = $r["FechaInicio"];


                        if ($datos_config_not[MostrarFecha] == 0 || $id_club == 51) :
                            $noticia["Fecha"] = "";
                        endif;

                        if (!empty($r["NoticiaFile"])) :
                            if (strstr(strtolower($r["NoticiaFile"]), "http://")) {
                                $foto1 = $r["NoticiaFile"];
                            }
                            if (strstr(strtolower($r["NoticiaFile"]), "https://")) {
                                $foto1 = $r["NoticiaFile"];
                            } else {
                                $foto1 = IMGNOTICIA_ROOT . $r["NoticiaFile"];
                            }

                        //$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
                        else :
                            $foto1 = "";
                        endif;

                        if (!empty($r["FotoDestacada"])) :
                            if (strstr(strtolower($r["FotoDestacada"]), "http://")) {
                                $foto2 = $r["FotoDestacada"];
                            } elseif (strstr(strtolower($r["FotoDestacada"]), "https://")) {
                                $foto2 = $r["FotoDestacada"];
                            } else {
                                $foto2 = IMGNOTICIA_ROOT . $r["FotoDestacada"];
                            }

                        //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                        else :
                            $foto2 = "";
                        endif;

                        if (!empty($r["FotoPortada"])) :
                            if (strstr(strtolower($r["FotoPortada"]), "http://")) {
                                $FotoPortada = $r["FotoPortada"];
                            }
                            if (strstr(strtolower($r["FotoPortada"]), "https://")) {
                                $FotoPortada = $r["FotoPortada"];
                            } else {
                                $FotoPortada = IMGNOTICIA_ROOT . $r["FotoPortada"];
                            }

                        //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                        else :
                            $FotoPortada = "";
                        endif;

                        $noticia["Foto"] = $foto1;
                        $noticia["Foto2"] = $foto2;
                        $noticia["FotoPortada"] = $FotoPortada;

                        if (empty($Version)) {
                            $Version = 1;
                        }

                        $sql_like_total = "SELECT count(IDNoticiaLike) as Total FROM NoticiaLikeInfinita WHERE IDNoticiaInfinita = '" . $r["IDNoticia"] . "' and Version = '" . $Version . "'";
                        $r_like_total = $dbo->query($sql_like_total);
                        $row_like_total = $dbo->fetchArray($r_like_total);
                        $noticia["Likes"] = (int) $row_like_total["Total"];

                        $sql_comen_total = "SELECT count(IDNoticiaComentario) as Total FROM NoticiaComentarioInfinita WHERE IDNoticiaInfinita = '" . $r["IDNoticia"] . "' and Version = '" . $Version . "'";
                        $r_comen_total = $dbo->query($sql_comen_total);
                        $row_comen_total = $dbo->fetchArray($r_comen_total);
                        $noticia["CantidadComentarios"] = (int) $row_comen_total["Total"];

                        //Consulto si usuario ya hizo like
                        $Like = "N";
                        if (!empty($id_socio)) {
                            $sql_like = "SELECT IDNoticiaLike FROM NoticiaLikeInfinita WHERE IDSocio= '" . $id_socio . "' and IDNoticiaInfinita = '" . $r["IDNoticia"] . "' and Version = '" . $Version . "' Limit 1";
                            $r_like = $dbo->query($sql_like);
                            $row_like = $dbo->fetchArray($r_like);
                            if ((int) $row_like["IDNoticiaLike"] > 0) {
                                $Like = "S";
                            } else {
                                $Like = "N";
                            }
                        }

                        if (!empty($IDUsuario)) {
                            $sql_like = "SELECT IDNoticiaLike FROM NoticiaLikeInfinita WHERE IDUsuario= '" . $IDUsuario . "' and IDNoticiaInfinita = '" . $r["IDNoticia"] . "' and Version = '" . $Version . "' Limit 1";
                            $r_like = $dbo->query($sql_like);
                            $row_like = $dbo->fetchArray($r_like);
                            if ((int) $row_like["IDNoticiaLike"] > 0) {
                                $Like = "S";
                            } else {
                                $Like = "N";
                            }
                        }

                        $noticia["HeHechoLike"] = $Like;

                        array_push($response, $noticia);
                    }
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

        }

        return $respuesta;
    } // fin function

    public function set_like_noticias_infinitas($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $HacerLike, $Version, $IDModulo)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDNoticia)) {

            if (empty($Version)) {
                $Version = 1;
            }

            if ($HacerLike == "S") {
                if (!empty($IDSocio)) {
                    $sql_like = "INSERT INTO NoticiaLikeInfinita (IDSocio,IDNoticiaInfinita,Version,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDSocio . "','" . $IDNoticia . "','" . $Version . "','WS',NOW())";
                } else {
                    $sql_like = "INSERT INTO NoticiaLikeInfinita (IDUsuario,IDNoticiaInfinita,Version,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDUsuario . "','" . $IDNoticia . "','" . $Version . "','WS',NOW())";
                }

                $dbo->query($sql_like);
            } else {
                if (!empty($IDSocio)) {
                    $sql_like = "DELETE FROM NoticiaLikeInfinita WHERE  IDSocio = '" . $IDSocio . "' and Version='" . $Version . "' and IDNoticiaInfinita = '" . $IDNoticia . "' LIMIT 1";
                } else {
                    $sql_like = "DELETE FROM NoticiaLikeInfinita WHERE  IDUsuario = '" . $IDSocio . "' and Version='" . $Version . "' and IDNoticiaInfinita = '" . $IDNoticia . "' LIMIT 1";
                }

                $dbo->query($sql_like);
            }

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = $datos_reserva;
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function set_comentar_noticias_infinitas($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Comentario, $Version, $IDModulo)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDNoticia)) {

            if (empty($Version)) {
                $Version = 1;
            }

            $PublicarComentariosAutomaticamente = $dbo->getFields("ConfiguracionNoticiasInfinita ", "PermitePublicarComentariosAutomaticamente", "IDClub = '" . $IDClub . "' AND Activo='S' AND IDModulo='" . $IDModulo . "'");
            if ($PublicarComentariosAutomaticamente == "S") {
                $Publicar = "S";
            } else {
                $Publicar = "";
            }

            if (!empty($IDSocio)) {
                $sql_comenta = "INSERT INTO NoticiaComentarioInfinita (IDSocio,IDNoticiaInfinita,Version,Publicar,Comentario,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDSocio . "','" . $IDNoticia . "','" . $Version . "','" . $Publicar . "','" . $Comentario . "','WS',NOW())";
            } else {
                $sql_comenta = "INSERT INTO NoticiaComentarioInfinita (IDUsuario,IDNoticiaInfinita,Version,Publicar,Comentario,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDUsuario . "','" . $IDNoticia . "','" . $Version . "','" . $Publicar . "','" . $Comentario . "','WS',NOW())";
            }

            $dbo->query($sql_comenta);

            $correo = $dbo->getFields("Club", "CorreoNotificacionComentarioNoticia", "IDClub = " . $IDClub);

            if (!empty($correo)) {
                SIMUtil::notifica_nuevo_cometario_noticia($IDNoticia, $Version, $Comentario, $IDSocio, $IDUsuario);
            }

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = $datos_reserva;
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_comentarios_noticias_infinitas($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Pagina, $Version, $IDModulo)
    {

        $dbo = &SIMDB::get();
        $RegistrosPagina = 10;

        $Pagina = (int) $Pagina;
        if ($Pagina == 0) {
            $limite = "0," . $RegistrosPagina;
        } else {
            $Pagina = $Pagina * 10;
            $limite = $Pagina . "," . $RegistrosPagina;
        }

        if (empty($Version)) {
            $Version = 1;
        }

        $response = array();
        $sql = "SELECT * FROM NoticiaComentarioInfinita WHERE  Publicar = 'S' AND IDNoticiaInfinita = '" . $IDNoticia . "' and Version = '" . $Version . "' ORDER BY FechaTrCr DESC LIMIT " . $limite;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $noticia["IDNoticia"] = $r["IDNoticiaInfinita"];
                $noticia["IDComentario"] = $r["IDNoticiaComentario"];
                $noticia["IDSocio"] = $r["IDSocio"];
                $noticia["IDUsuario"] = $r["IDUsuario"];
                $foto = "";
                if ((int) $r["IDSocio"] > 0) {
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $r["IDSocio"] . "' ", "array");
                    $Nombre = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                    if (!empty($datos_socio["Foto"])) {
                        $foto = SOCIO_ROOT . $datos_socio["Foto"];
                    }
                } else {
                    $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $r["IDUsuario"] . "' ", "array");
                    $Nombre = $datos_usuario["Nombre"];
                    if (!empty($datos_usuario["Foto"])) {
                        $foto = USUARIO_ROOT . $datos_socio["Foto"];
                    }
                }
                $noticia["Nombre"] = $Nombre;
                $noticia["Foto"] = $foto;
                $noticia["Comentario"] = $r["Comentario"];
                $noticia["Fecha"] = substr($r["FechaTrCr"], 0, 10);
                array_push($response, $noticia);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'CuentanosqueteparecioestanoticiadejandounComentarioounMeGusta', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

}
