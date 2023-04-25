<?php

class SIMWebServiceNoticias
{

    public function get_seccion($IDClub, $IDSocio = "", $IDUsuario = "", $TipoApp = "", $Version = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDSocio)) :
        //$condicion = " and SS.IDSeccion = S.IDSeccion and IDSocio = '" . $IDSocio . "' ";
        //$tabla_join = ", SocioSeccion".$Version." SS ";
        endif;

        // PARA FEDEGOLF SE UTILIZA EL SERVICIO DE ELLOS PARA LA VERSION 2
        if ($IDClub == 17 && $Version == 2) :
            $respuesta = SIMWebServiceFedegolf::categoria_comunidad_al_aire($IDClub, $IDSocio, $IDUsuario, $TipoApp, $Version);
            return $respuesta;
        endif;


        if ($IDClub == 227 && $Version == 2) :

            $query = $dbo->query("SELECT * FROM Socio WHERE IDSocio='$IDSocio' AND IDClub='$IDClub'");

            while ($row = $dbo->fetchArray($query)) {
                $tokencountry = $row["TokenCountryMedellin"];
            }
            $token = urlencode($tokencountry);

            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

            $datos = SIMWebServiceCountryMedellin::App_ConsultarNoticias2($token);

            $respuesta = $datos;

            return $respuesta;

        endif;

        if ($IDClub == 227) :

            $query = $dbo->query("SELECT * FROM Socio WHERE IDSocio='$IDSocio' AND IDClub='$IDClub'");

            while ($row = $dbo->fetchArray($query)) {
                $tokencountry = $row["TokenCountryMedellin"];
            }
            $token = urlencode($tokencountry);

            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

            $datos = SIMWebServiceCountryMedellin::App_ConsultarNoticias($token);

            $respuesta = $datos;

            return $respuesta;

        endif;


        $response = array();

        $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
        if ($IDClub == 36 && $TipoSocio == "Estudiante") {
            $condicion_noticia .= " and TipoSocio = 'Estudiante'";
        } else {
            $condicion_noticia .= " and TipoSocio <> 'Estudiante'";
        }

        if ($TipoApp == "Socio") {
            $condicion_seccion .= " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } else {
            $condicion_seccion .= " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT S.* FROM Seccion" . $Version . " S " . $tabla_join . " WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' " . $condicion . " " . $condicion_seccion . " ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                $id_noticia = $dbo->getFields("Noticia" . $Version, "IDNoticia", "IDSeccion = '" . $r["IDSeccion"] . "' and Publicar = 'S' " . $condicion_noticia);
                if (!empty($id_noticia)) :
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDSeccion"] = $r["IDSeccion"];
                    $seccion["Nombre"] = $r["Nombre"];
                    $seccion["Descripcion"] = $r["Descripcion"];
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

    public function get_noticias($IDClub, $IDSeccion = "", $IDSocio = "", $Tag = "", $Version = "", $IDUsuario = "")
    {

        $dbo = &SIMDB::get();
        $datos_config_not = $dbo->fetchAll("ConfiguracionNoticias", " IDClub = '" . $IDClub . "' ", "array");

        // PARA FEDEGOLF SE UTILIZA EL SERVICIO DE ELLOS PARA LA VERSION 2
        if ($IDClub == 17 && $Version == 2) :
            require LIBDIR  . "SIMWebServiceFedegolf.inc.php";
            $respuesta = SIMWebServiceFedegolf::comunidad_al_aire($IDClub, $IDSeccion, $IDSocio, $Tag, $Version, $IDUsuario);
            return $respuesta;
        endif;

        // PARA COUNTRY MEDELLIN SE USA EL SERVICIO DE ELLOS 

        if ($IDClub == 227 && $Version == 2) :

            $query = $dbo->query("SELECT * FROM Socio WHERE IDSocio='$IDSocio' AND IDClub='$IDClub'");

            while ($row = $dbo->fetchArray($query)) {
                $tokencountry = $row["TokenCountryMedellin"];
            }
            $token = urlencode($tokencountry);

            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

            $datos = SIMWebServiceCountryMedellin::App_ConsultarNoticiasTipo2($token, $IDSeccion);

            $respuesta = $datos;

            return $respuesta;

        endif;



        if ($IDClub == 227) :

            $query = $dbo->query("SELECT * FROM Socio WHERE IDSocio='$IDSocio' AND IDClub='$IDClub'");

            while ($row = $dbo->fetchArray($query)) {
                $tokencountry = $row["TokenCountryMedellin"];
            }
            $token = urlencode($tokencountry);

            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

            $datos = SIMWebServiceCountryMedellin::App_ConsultarNoticiasTipo($token, $IDSeccion);

            $respuesta = $datos;

            return $respuesta;

        endif;


        if (!empty($IDUsuario)) {

            // Secciones Empleado
            if (!empty($id_empleado)) :
                $sql_seccion_empleado = $dbo->query("Select * From UsuarioSeccion Where IDUsuario = '" . $id_usuario . "'");
                while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)) :
                    $array_secciones_empleado[] = $row_seccion["IDSeccion"];
                endwhile;

                if (count($array_secciones_empleado) > 0) :
                    $IDSecciones = implode(",", $array_secciones_empleado);
                    $array_condiciones[] = " IDSeccion in(" . $IDSecciones . ") ";
                endif;
            endif;

            // Seccion Especifica
            if (!empty($IDSeccion)) :
                $array_condiciones[] = " IDSeccion  = '" . $IDSeccion . "'";
            endif;

            // Tag
            if (!empty($Tag)) :
                $array_condiciones[] = " (Titular  like '%" . $Tag . "%' or Introduccion like '%" . $Tag . "%' or Cuerpo like '%" . $Tag . "%')";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_noticia = " and " . $condiciones;
            endif;

            if ($IDClub == 25) {
                $orden = " ORDER BY Orden ASC, FechaInicio";
            } else {
                $orden = " ORDER BY FechaInicio DESC, Orden";
            }

            $response = array();
            $sql = "SELECT * FROM Noticia WHERE  FechaInicio <= CURDATE() AND FechaFin >= CURDATE() AND (DirigidoA = 'E' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $IDClub . "'" . $condiciones_noticia . $orden;

            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $idgrupo = $r["IDGrupoSocio"];
                    //evaluo si el socio esta en el grupo especifico
                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$idgrupo'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_socios = explode("|||", $SociosGrupo);
                    if (count($array_socios) >= 0) {
                        foreach ($array_socios as $id_socios => $datos_socio) {
                            $array_socios_noticias[] = $datos_socio;
                        }
                    }

                    if ($r["IDGrupoSocio"] == 0) {
                        $array_socios_noticias[] = $IDUsuario;
                    }
                    
                    if($IDClub==25 and $r["ParaPaginaWeb"] == 1):
                    
                    else:
                    
                    if (in_array($IDUsuario, $array_socios_noticias)) {
                        unset($array_socios_noticias);
                        $idgrupo = "";


                        $noticia["IDClub"] = $r["IDClub"];
                        $noticia["IDSeccion"] = $r["IDSeccion"];
                        $noticia["IDNoticia"] = $r["IDNoticia"];
                        $noticia["Titular"] = $r["Titular"];
                        $noticia["Introduccion"] = $r["Introduccion"];
                        $noticia["ParaPublicidad"] = $r["ParaPublicidad"];
                        $noticia["ParaSeccionPublica"] = $r["ParaSeccionPublica"];
                         //se agrega este campo para permitir o no verlo en la pagina web
                        $noticia["ParaPaginaWeb"] = $r["ParaPaginaWeb"];

                        $cuerpo_noticia = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Cuerpo"]);

                        //Documentos adjuntos
                        if (!empty($r["Adjunto1File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                        endif;
                        if (!empty($r["Adjunto2File"])) :
                            $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto2File"] . "' >" . $r["Adjunto2File"] . '</a>';
                        endif;

                        $cuerpo_noticia = str_replace("\r\n", "<br>", $cuerpo_noticia);
                        $cuerpo_noticia = str_replace("[", "<", $cuerpo_noticia);
                        $cuerpo_noticia = str_replace("]", ">", $cuerpo_noticia);

                        $noticia["Cuerpo"] = $cuerpo_noticia;

                        $noticia["Fecha"] = $r["FechaInicio"];



                        if ($datos_config_not[MostrarFecha] == 0 and $IDClub != 25) :
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
                        
                        
                        
                        if (!empty($r["NoticiaFilePagina"])) :
                            if (strstr(strtolower($r["NoticiaFilePagina"]), "http://")) {
                                $fotoweb = $r["NoticiaFilePagina"];
                            } else {
                                $fotoweb = IMGNOTICIA_ROOT . $r["NoticiaFilePagina"];
                            }

                        //$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
                        else :
                            $fotoweb = "";
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
                        $noticia["FotoWeb"] = $fotoweb;
                        $noticia["Foto2"] = $foto2;
                        $noticia["FotoPortada"] = $FotoPortada;

                        array_push($response, $noticia);
                    } 
                    endif;
                }
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
            if (!empty($IDSocio) && $IDSeccion == "") :

                $sql_seccion_socio = $dbo->query("Select * From SocioSeccion" . $Version . " Where IDSocio = '" . $IDSocio . "'");

                //$dbo->object($sql_seccion_socio);

                while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)) :
                    $array_secciones_socio[] = $row_seccion["IDSeccion"];
                endwhile;

                if (count($array_secciones_socio) > 0) :
                    $IDSecciones = implode(",", $array_secciones_socio);
                    $array_condiciones[] = " IDSeccion in(" . $IDSecciones . ") ";
                endif;

                $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
                if ($IDClub == 36 && $TipoSocio == "Estudiante") {
                    $array_condiciones[] = " TipoSocio = 'Estudiante'";
                } else {
                    $array_condiciones[] = " (TipoSocio = '' or TipoSocio = 'Socio' )";
                }
            endif;

            // Seccion Especifica
            if (!empty($IDSeccion)) :
                $array_condiciones[] = " IDSeccion  = '" . $IDSeccion . "'";
            endif;

            // Tag
            if (!empty($Tag)) :
                $array_condiciones[] = " (Titular  like '%" . $Tag . "%' or Introduccion like '%" . $Tag . "%' or Cuerpo like '%" . $Tag . "%')";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_noticia = " and " . $condiciones;
            endif;

            if ($IDClub == 25) {
                $orden = " ORDER BY Orden ASC, FechaInicio";
            } else {
                $orden = " ORDER BY FechaInicio DESC, Orden";
            }

            $response = array();
            $sql = "SELECT * FROM Noticia" . $Version . " WHERE FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and (DirigidoA = 'S' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $IDClub . "'" . $condiciones_noticia . $orden;
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $idgrupo = $r["IDGrupoSocio"];
                    //evaluo si el socio esta en el grupo especifico
                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$idgrupo'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_socios = explode("|||", $SociosGrupo);
                    if (count($array_socios) >= 0) {
                        foreach ($array_socios as $id_socios => $datos_socio) {
                            $array_socios_noticias[] = $datos_socio;
                        }
                    }

                    if ($r["IDGrupoSocio"] == 0) {
                        $array_socios_noticias[] = $IDSocio;
                    }
                    
                     if($IDClub==25 and $r["ParaPaginaWeb"] == 1):
                    
                    else:
                    
                    if (in_array($IDSocio, $array_socios_noticias)) {
                        unset($array_socios_noticias);
                        $ver_registro = 1;

                        if ($ver_registro == 1) {
                            $noticia["IDClub"] = $r["IDClub"];
                            $noticia["IDSeccion"] = $r["IDSeccion"];
                            $noticia["IDNoticia"] = $r["IDNoticia"];
                            $noticia["Titular"] = $r["Titular"];
                            $noticia["Introduccion"] = $r["Introduccion"];
                            $noticia["ParaPublicidad"] = $r["ParaPublicidad"];
                            $noticia["ParaSeccionPublica"] = $r["ParaSeccionPublica"];
                            //se agrega este campo para permitir o no verlo en la pagina web
                            $noticia["ParaPaginaWeb"] = $r["ParaPaginaWeb"];
                             

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


                            if ($datos_config_not[MostrarFecha] == 0 || $IDClub == 51 and $IDClub != 25) :
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
                            
                              if (!empty($r["NoticiaFilePagina"])) :
                            if (strstr(strtolower($r["NoticiaFilePagina"]), "http://")) {
                                $fotoweb = $r["NoticiaFilePagina"];
                            } else {
                                $fotoweb = IMGNOTICIA_ROOT . $r["NoticiaFilePagina"];
                            }

                        //$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
                        else :
                            $fotoweb = "";
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
                            $noticia["FotoWeb"] = $fotoweb;
                            $noticia["Foto2"] = $foto2;
                            $noticia["FotoPortada"] = $FotoPortada;

                            if (empty($Version)) {
                                $Version = 1;
                            }

                            $sql_like_total = "SELECT count(IDNoticiaLike) as Total FROM NoticiaLike WHERE IDNoticia = '" . $r["IDNoticia"] . "' and Version = '" . $Version . "'";
                            $r_like_total = $dbo->query($sql_like_total);
                            $row_like_total = $dbo->fetchArray($r_like_total);
                            $noticia["Likes"] = (int) $row_like_total["Total"];

                            $sql_comen_total = "SELECT count(IDNoticiaComentario) as Total FROM NoticiaComentario WHERE IDNoticia = '" . $r["IDNoticia"] . "' and Version = '" . $Version . "'";
                            $r_comen_total = $dbo->query($sql_comen_total);
                            $row_comen_total = $dbo->fetchArray($r_comen_total);
                            $noticia["CantidadComentarios"] = (int) $row_comen_total["Total"];

                            //Consulto si usuario ya hizo like
                            $Like = "N";
                            if (!empty($IDSocio)) {
                                $sql_like = "SELECT IDNoticiaLike FROM NoticiaLike WHERE IDSocio= '" . $IDSocio . "' and IDNoticia = '" . $r["IDNoticia"] . "' and Version = '" . $Version . "' Limit 1";
                                $r_like = $dbo->query($sql_like);
                                $row_like = $dbo->fetchArray($r_like);
                                if ((int) $row_like["IDNoticiaLike"] > 0) {
                                    $Like = "S";
                                } else {
                                    $Like = "N";
                                }
                            }

                            if (!empty($IDUsuario)) {
                                $sql_like = "SELECT IDNoticiaLike FROM NoticiaLike WHERE IDUsuario= '" . $IDUsuario . "' and IDNoticia = '" . $r["IDNoticia"] . "' and Version = '" . $Version . "' Limit 1";
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

        }

        return $respuesta;
    }

    public function get_comentarios_noticias($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Pagina, $Version)
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
        $sql = "SELECT * FROM NoticiaComentario WHERE  Publicar = 'S' AND IDNoticia = '" . $IDNoticia . "' and Version = '" . $Version . "' ORDER BY FechaTrCr DESC LIMIT " . $limite;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $noticia["IDNoticia"] = $r["IDNoticia"];
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
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'CuentanosqueteparecioestanoticiadejandounComentarioounMeGusta.', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function set_like_noticia($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $HacerLike, $Version)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDNoticia)) {

            if (empty($Version)) {
                $Version = 1;
            }

            if ($HacerLike == "S") {
                if (!empty($IDSocio)) {
                    $sql_like = "INSERT INTO NoticiaLike (IDSocio,IDNoticia,Version,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDSocio . "','" . $IDNoticia . "','" . $Version . "','WS',NOW())";
                } else {
                    $sql_like = "INSERT INTO NoticiaLike (IDUsuario,IDNoticia,Version,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDUsuario . "','" . $IDNoticia . "','" . $Version . "','WS',NOW())";
                }

                $dbo->query($sql_like);
            } else {
                if (!empty($IDSocio)) {
                    $sql_like = "DELETE FROM NoticiaLike WHERE  IDSocio = '" . $IDSocio . "' and Version='" . $Version . "' and IDNoticia = '" . $IDNoticia . "' LIMIT 1";
                } else {
                    $sql_like = "DELETE FROM NoticiaLike WHERE  IDUsuario = '" . $IDSocio . "' and Version='" . $Version . "' and IDNoticia = '" . $IDNoticia . "' LIMIT 1";
                }

                $dbo->query($sql_like);
            }

            $respuesta["message"] = "guardado.";
            $respuesta["success"] = true;
            $respuesta["response"] = $datos_reserva;
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function set_comentar_noticias($IDClub, $IDNoticia, $IDSocio, $IDUsuario, $Comentario, $Version)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDNoticia)) {

            $datos_config_not = $dbo->fetchAll("ConfiguracionNoticias", " IDClub = '" . $IDClub . "' ", "array");
            if ($datos_config_not["PublicarComentariosAutomaticamente" . $Version] == "S") {
                $Publicar = "S";
            } else {
                $Publicar = "N";
            }

            if (empty($Version)) {
                $Version = 1;
            }



            if (!empty($IDSocio)) {
                $sql_comenta = "INSERT INTO NoticiaComentario (IDSocio,IDNoticia,Version,Comentario,Publicar,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDSocio . "','" . $IDNoticia . "','" . $Version . "','" . $Comentario . "','" . $Publicar . "','WS',NOW())";
            } else {
                $sql_comenta = "INSERT INTO NoticiaComentario (IDUsuario,IDNoticia,Version,Comentario,Publicar,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDUsuario . "','" . $IDNoticia . "','" . $Version . "','" . $Comentario . "','" . $Publicar . "','WS',NOW())";
            }

            $dbo->query($sql_comenta);

            $correo = $dbo->getFields("Club", "CorreoNotificacionComentarioNoticia", "IDClub = " . $IDClub);

            if (!empty($correo)) {
                SIMUtil::notifica_nuevo_cometario_noticia($IDNoticia, $Version, $Comentario, $IDSocio, $IDUsuario);
            }

            $respuesta["message"] = "guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = $datos_reserva;
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_noticias_empleados($id_club, $id_seccion = "", $id_usuario = "", $tag = "")
    {

        $dbo = &SIMDB::get();

        // Secciones Empleado
        if (!empty($id_empleado)) :
            $sql_seccion_empleado = $dbo->query("Select * From UsuarioSeccion Where IDUsuario = '" . $id_usuario . "'");
            while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)) :
                $array_secciones_empleado[] = $row_seccion["IDSeccion"];
            endwhile;

            if (count($array_secciones_empleado) > 0) :
                $id_secciones = implode(",", $array_secciones_empleado);
                $array_condiciones[] = " IDSeccion in(" . $id_secciones . ") ";
            endif;
        endif;

        // Seccion Especifica
        if (!empty($id_seccion)) :
            $array_condiciones[] = " IDSeccion  = '" . $id_seccion . "'";
        endif;

        // Tag
        if (!empty($tag)) :
            $array_condiciones[] = " (Titular  like '%" . $tag . "%' or Introduccion like '%" . $tag . "%' or Cuerpo like '%" . $tag . "%')";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_noticia = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM Noticia WHERE (DirigidoA = 'E' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $id_club . "'" . $condiciones_noticia . " ORDER BY FechaInicio DESC,Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $idgrupo = $r["IDGrupoSocio"];
                //evaluo si el socio esta en el grupo especifico
                $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$idgrupo'"); //evaluamos los grupos pero hay que recorrer los resultados
                $array_socios = explode("|||", $SociosGrupo);
                if (count($array_socios) >= 0) {
                    foreach ($array_socios as $id_socios => $datos_socio) {
                        $array_socios_noticias[] = $datos_socio;
                    }
                }

                if ($r["IDGrupoSocio"] == 0) {
                    $array_socios_noticias[] = $IDUsuario;
                }
                if (in_array($IDUsuario, $array_socios_noticias)) {
                    unset($array_socios_noticias);
                    $idgrupo = "";

                    $noticia["IDClub"] = $r["IDClub"];
                    $noticia["IDSeccion"] = $r["IDSeccion"];
                    $noticia["IDNoticia"] = $r["IDNoticia"];
                    $noticia["Titular"] = $r["Titular"];
                    $noticia["Introduccion"] = $r["Introduccion"];

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

                    $noticia["Foto"] = $foto1;
                    $noticia["Foto2"] = $foto2;

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

        return $respuesta;
    } // fin function

    public function get_configuracion_noticias($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_config_not = $dbo->fetchAll("ConfiguracionNoticias", " IDClub = '" . $IDClub . "' ", "array");
        $sql_modulo = "Select IDModulo,Titulo,TituloLateral From ClubModulo Where IDClub = '" . $IDClub . "' and IDModulo in (3,4,5,66,81,76)";
        $r_modulo = $dbo->query($sql_modulo);
        while ($row_modulo = $dbo->fetchArray($r_modulo)) {
            $array_modulo[$row_modulo["IDModulo"]] = $row_modulo["TituloLateral"];
        }



        $configuracion["IDClub"] = $IDClub;
        if (empty($array_modulo["3"])) {
            $configuracion["MisNoticias"] = "Mis Noticias";
        } else {
            $configuracion["MisNoticias"] = $array_modulo["3"];
        }

        if (empty($array_modulo["66"])) {
            $configuracion["MisNoticias2"] = "Mis Noticias";
        } else {
            $configuracion["MisNoticias2"] = $array_modulo["66"];
        }

        if (empty($array_modulo["81"])) {
            $configuracion["MisNoticias3"] = "Mis Noticias";
        } else {
            $configuracion["MisNoticias3"] = $array_modulo["81"];
        }

        if (empty($array_modulo["4"])) {
            $configuracion["MisEventos"] = "Mis Eventos";
        } else {
            $configuracion["MisEventos"] = $array_modulo["4"];
        }

        if (empty($array_modulo["76"])) {
            $configuracion["MisEventos2"] = "Mis Eventos";
        } else {
            $configuracion["MisEventos2"] = $array_modulo["76"];
        }

        if (empty($array_modulo["5"])) {
            $configuracion["MisGalerias"] = "Mis Galerias";
        } else {
            $configuracion["MisGalerias"] = $array_modulo["5"];
        }

        if (empty($array_modulo["150"])) {
            $configuracion["MisGalerias2"] = "Mis Galerias";
        } else {
            $configuracion["MisGalerias2"] = $array_modulo["150"];
        }

        $configuracion["PermiteLikesNoticias"] = $datos_config_not["PermiteLikeNoticia1"];
        $configuracion["PermiteLikesNoticias2"] = $datos_config_not["PermiteLikeNoticia2"];
        $configuracion["PermiteLikesNoticias3"] = $datos_config_not["PermiteLikeNoticia3"];
        $configuracion["PermiteComentariosNoticias"] = $datos_config_not["PermiteComentarioNoticia1"];
        $configuracion["PermiteComentariosNoticias2"] = $datos_config_not["PermiteComentarioNoticia2"];
        $configuracion["PermiteComentariosNoticias3"] = $datos_config_not["PermiteComentarioNoticia3"];

        $configuracion["PermiteIconoComentariosNoticias"] = $datos_config_not["PermiteIconoComentariosNoticias"];
        $configuracion["PermiteIconoComentariosNoticias2"] = $datos_config_not["PermiteIconoComentariosNoticias2"];
        $configuracion["PermiteIconoComentariosNoticias3"] = $datos_config_not["PermiteIconoComentariosNoticias3"];





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
        $configuracion["TipoImagenNoticias2"] = $TipoImagenNoticias;
        $configuracion["TipoImagenNoticias3"] = $TipoImagenNoticias;

        array_push($response, $configuracion);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } // fin function

    //INICIO NOTIFICACIONES GRUPO NOTICIAS DEL MODULO CONFIGURACION
    public function get_configuracion_preferencias_usuario($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();


        if (!empty($IDClub)) :

            $SQLConfiguracion = "SELECT * FROM ConfiguracionPreferenciasUsuario WHERE IDClub = $IDClub AND Activo = 1";
            $QRYCOnfiguracion = $dbo->query($SQLConfiguracion);

            if ($dbo->rows($QRYCOnfiguracion) > 0) :
                $message = $dbo->rows($QRYCOnfiguracion) . "Encontrados";
                while ($Datos = $dbo->fetchArray($QRYCOnfiguracion)) :

                    $InfoConfiguracion[IDClub] = $Datos[IDClub];
                    $InfoConfiguracion[HeaderListaGrupos] = $Datos[HeaderListaGrupos];
                    $InfoConfiguracion[LabelBotonGruposNoticias] = $Datos[LabelBotonGruposNoticias];
                    $InfoConfiguracion[MostrarGruposNoticias] = $Datos[MostrarGruposNoticias];

                    $InfoConfiguracion[HeaderGruposNoticias] = $Datos[HeaderGruposNoticias];
                    $InfoConfiguracion[HeaderCheckboxAgregame] = $Datos[HeaderCheckboxAgregame];
                    $InfoConfiguracion[HeaderCheckboxNotificame] = $Datos[HeaderCheckboxNotificame];


                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $InfoConfiguracion;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    } // fin function

    public function get_grupos_de_noticias_preferencias($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub) && !empty($IDSocio)) :

            $SQLGrupoNoticias = "SELECT * FROM GrupoNoticias  WHERE IDClub = $IDClub AND Publicar = 1";
            $QRYGrupoNoticias = $dbo->query($SQLGrupoNoticias);

            if ($dbo->rows($QRYGrupoNoticias) > 0) :
                $message = $dbo->rows($QRYGrupoNoticias) . "Encontrados";
                while ($Datos = $dbo->fetchArray($QRYGrupoNoticias)) :

                    $InfoPreferencias[IDGrupoNoticia] = $Datos[IDGrupoNoticias];
                    $InfoPreferencias[Nombre] = $Datos[Nombre];

                    //SELECCIONO LAS PREFERENCIAS DEL SOCIO
                    $SQLPreferencias = "SELECT * FROM PreferenciasGrupoNoticias  WHERE IDSocio = $IDSocio AND IDGrupoNoticias = $Datos[IDGrupoNoticias]";
                    $QRYPreferencias = $dbo->query($SQLPreferencias);
                    $DatosPreferencia = $dbo->fetchArray($QRYPreferencias);



                    $InfoPreferencias[Agregado] = !empty($DatosPreferencia[Agregado]) ? $DatosPreferencia[Agregado] : "";
                    $InfoPreferencias[Notificado] = !empty($DatosPreferencia[Notificado]) ? $DatosPreferencia[Notificado] : "";


                    array_push($response, $InfoPreferencias);

                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    } // fin function

    public function set_grupos_noticias_preferencias($IDClub, $IDSocio, $IDUsuario, $GruposNoticias)
    {
        $dbo = SIMDB::get();


        if (!empty($IDClub) && !empty($IDSocio)) :

            //BORRO LAS PREFERENCIAS QUE TIENE ACTUALMENTE EL SOCIO
            $qry_delete = $dbo->query("DELETE FROM PreferenciasGrupoNoticias WHERE IDSocio= $IDSocio");

            $Respuestas = trim(preg_replace('/\s+/', ' ', $GruposNoticias));
            $datos_respuesta = json_decode($Respuestas, true);
            if (count($datos_respuesta) > 0) :
                foreach ($datos_respuesta as $detalle_respuesta) :
                    $sql_datos_form = $dbo->query("Insert Into PreferenciasGrupoNoticias (IDSocio,IDGrupoNoticias,Agregado,Notificado,FechaTrCr) Values ('" . $IDSocio . "','" . $detalle_respuesta["IDGrupoNoticia"] . "','" . $detalle_respuesta["Agregado"] . "','" . $detalle_respuesta["Notificado"] . "',NOW())");
                //$sql_datos_form = "Insert Into PreferenciasGrupoNoticias (IDSocio,IDGrupoNoticias,Agregado,Notificado,FechaTrCr) Values ('" . $IDSocio . "','" . $detalle_respuesta["IDGrupoNoticia"] . "','" . $detalle_respuesta["Agregado"] . "','" . $detalle_respuesta["Notificado"] . "',NOW())";
                //echo $sql_datos_form;


                endforeach;

            endif;


            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = "Guardado";
        else :
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    } // fin function
}
