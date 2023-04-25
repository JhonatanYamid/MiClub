<?php

class SIMWebServiceDocumentos
{
    public function get_documento($IDClub, $IDTipoArchivo = "", $IDServicio = "")
    {
        $dbo = &SIMDB::get();

        //inserta _log
        //$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros) Values ('get_documento','TipoArchivo: ".$IDTipoArchivo. " IDServicio:" . $id_servicio . "')");

        // Tipo Archivo Especifico
        if (!empty($IDTipoArchivo)) :
            $condiciones = " and IDTipoArchivo  = '" . $IDTipoArchivo . "'";
        endif;

        // Servicio Especifico
        if (!empty($id_servicio)) :
            $condiciones = " and IDServicio  = '" . $id_servicio . "'";
        endif;

        /*       if ($IDClub == 223) {
            $orden = "ORDER BY  FechaTrCr DESC";
        } else { */
        $orden = "ORDER BY Fecha DESC";
        // }

        $response = array();
        $sql = "SELECT * FROM Documento WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' " . $condiciones . " $orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $documento["IDClub"] = $r["IDClub"];
                $documento["IDTipoArchivo"] = $r["IDTipoArchivo"];
                $documento["TipoArchivo"] = $dbo->getFields("TipoArchivo", "Nombre", "IDTipoArchivo = '" . $r["IDTipoArchivo"] . "'");

                $foto_servicio = "";
                if (!empty($r["Icono"])) :
                    $foto_servicio = DOCUMENTO_ROOT . $r["Icono"];
                else :
                    $foto_servicio = "";
                endif;

                if (empty($r["IDServicio"])) :
                    $servicio = "";
                    $id_servicio = "";
                else :
                    $id_servicio = $r["IDServicio"];
                    $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r["IDServicio"] . "'");
                    $servicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                    $icono_servicio = $dbo->getFields("Servicio", "Icono", "IDServicio = '" . $r["IDServicio"] . "'");

                    if (empty($foto_servicio)) :
                        if (!empty($icono_servicio)) :
                            $foto_servicio = SERVICIO_ROOT . $icono_servicio;
                        else :
                            $foto_servicio = "";
                        endif;
                    endif;
                endif;

                $documento["IDServicio"] = $id_servicio;
                //$documento["Servicio"] = $servicio;
                $documento["Servicio"] = $r["Nombre"];
                $documento["IconoServicio"] = $foto_servicio;
                $documento["IDDocumento"] = $r["IDDocumento"];
                $documento["Titular"] = $r["Nombre"];
                $documento["Subtitular"] = $r["Subtitular"];
                $documento["Fecha"] = $r["Fecha"];
                $documento["Descripcion"] = $r["Descripcion"];
                //ruta temporal =
                //$ruta_temporal = str_replace( "https", "http", DOCUMENTO_ROOT );
                //$ruta_temporal = DOCUMENTO_ROOT;
                if (!empty($r["Archivo1"])) :
                    $archivo = $ruta_temporal . $r["Archivo1"];
                else :
                    $archivo = "";
                endif;
                $documento["Documento"] = $archivo;
                array_push($response, $documento);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registro";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_documento_personal($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM DocumentoPersonal WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and (IDSocio = '" . $IDSocio . "' or IDSocio = 0)  ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $documento["IDClub"] = $r["IDClub"];
                $foto_servicio = "";
                if (!empty($r["Icono"])) :
                //$foto_servicio = DOCUMENTO_ROOT . $r["Icono"];
                else :
                    $foto_servicio = "";
                endif;

                $documento["IconoServicio"] = $foto_servicio;
                $documento["IDDocumentoPersonal"] = $r["IDDocumentoPersonal"];
                $documento["Nombre"] = $r["Nombre"];
                $documento["Fecha"] = $r["Fecha"];
                $documento["Descripcion"] = $r["Descripcion"];

                $documento["DebeAceptarTerminos"] = $r["DebeAceptarTerminos"];
                $documento["TerminosAceptados"] = $r["TerminosAceptados"];
                $documento["FechaAceptacionTerminos"] = $r["FechaAceptacionTerminos"];
                $documento["TextoTerminosHtml"] = $r["TextoTerminosHtml"];
                $documento["BotonAceptarTerminosLabel"] = $r["BotonAceptarTerminosLabel"];
                $documento["LabelTerminosAceptados"] = $r["LabelTerminosAceptados"];

                //ruta temporal =
                //$ruta_temporal = str_replace( "https", "http", DOCUMENTO_ROOT );
                $ruta_temporal = DOCUMENTO_ROOT;
                if (!empty($r["Archivo1"])) :
                    $archivo = $ruta_temporal . $r["Archivo1"];
                else :
                    $archivo = "";
                endif;
                $documento["Documento"] = $archivo;
                array_push($response, $documento);
            }
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registro";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function set_acepta_documento_personal($IDClub, $IDSocio, $IDUsuario, $IDDocumentoPersonal)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDDocumentoPersonal) && (!empty($IDSocio) || !empty($IDUsuario))) {

            $sql_documento = $dbo->query("UPDATE DocumentoPersonal
                                        SET TerminosAceptados = 'S', FechaAceptacionTerminos = NOW()
                                        WHERE IDDocumentoPersonal = '" . $IDDocumentoPersonal . "'");
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardadocorrectamente', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "DP1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_tipoarchivo($IDClub, $IDTipoArchivo = "", $IDSubmodulo = "")
    {
        $dbo = &SIMDB::get();

        // Tipo Archivo Especifico
        if (!empty($IDTipoArchivo)) :
            $condiciones = " and IDTipoArchivo  = '" . $IDTipoArchivo . "'";
        endif;

        $response = array();
        $sql = "SELECT * FROM TipoArchivo WHERE Publicar = 'S' " . $condiciones . " ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $activo_tipoarchivo = $dbo->getFields("ClubTipoArchivo", "Activo", "IDTipoArchivo = '" . $r["IDTipoArchivo"] . "' and IDClub = '" . $IDClub . "'");
                if ($activo_tipoarchivo == "S") :
                    $foto = "";
                    $foto_icono = $dbo->getFields("ClubTipoArchivo", "Icono", "IDTipoArchivo = '" . $r["IDTipoArchivo"] . "' and IDClub = '" . $IDClub . "'");
                    if (!empty($foto_icono)) {
                        $foto = CLIENTE_ROOT . $foto_icono;
                    }

                    if (!empty($r["Icono"]) && empty($foto)) {
                        $foto = CLIENTE_ROOT . $r["Icono"];
                    }

                    $tipo_archivo["IDTipoArchivo"] = $r["IDTipoArchivo"];

                    $nombre_tipoarchivo = $dbo->getFields("ClubTipoArchivo", "NombreTipoArchivo", "IDTipoArchivo = '" . $r["IDTipoArchivo"] . "' and IDClub = '" . $IDClub . "'");
                    $tipo_archivo["Nombre"] = $r["Nombre"];

                    if (!empty($nombre_tipoarchivo)) :
                        $tipo_archivo["Label"] = $nombre_tipoarchivo;
                    else :
                        $tipo_archivo["Label"] = $r["Nombre"];
                    endif;

                    $tipo_archivo["Icono"] = $foto;
                    array_push($response, $tipo_archivo);
                endif;
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registro";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    // NUEVAS

    public function get_codigo_pago($IDClub, $CodigoPago)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($CodigoPago)) {

            //verifico que el codigo exista y no haya sido utilizado
            $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "' and Disponible = 'S'");

            if (!empty($id_codigo)) {

                $respuesta["message"] = "Codigo correcto";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "El codigo no es valido";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "52. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_documento_dinamico($IDClub, $IDSubmodulo, $Version, $IDSocio)
    {
        $dbo = &SIMDB::get();

        $response = array();
        //$sql = "SELECT TA.*,CTA.Icono,CTA.NombreTipoArchivo FROM TipoArchivo TA,ClubTipoArchivo CTA  WHERE TA.IDTipoArchivo=CTA.IDTipoArchivo and  CTA.IDClub = '".$IDClub."' and Activo = 'S' ORDER BY Nombre";
        $sql = "SELECT * FROM TipoArchivo" . $Version . " WHERE IDClub = '" . $IDClub . "' and Publicar = 'S'  and DirigidoA <> 'E' ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . "" . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $foto = "";
                $foto_icono = $r["Icono"] . "'";

                if (!empty($r["Icono"]) && empty($foto)) {
                    $foto = CLIENTE_ROOT . $r["Icono"];
                }

                $tipo_archivo["IDTipoArchivo"] = $r["IDTipoArchivo"];
                $tipo_archivo["Nombre"] = $r["Nombre"];
                $nombre_tipoarchivo = $r["NombreTipoArchivo"];
                if (!empty($nombre_tipoarchivo)) :
                    $tipo_archivo["Label"] = $nombre_tipoarchivo;
                else :
                    $tipo_archivo["Label"] = $r["Nombre"];
                endif;
                $tipo_archivo["Tipo"] = $r["Tipo"];
                $tipo_archivo["Icono"] = $foto;
                $tipo_archivo["SoloIcono"] = $r["SoloIcono"];

                //Consulto los archivos que tiene este tipo de archivo

                if ($IDClub == 223) {
                    $orden = "ORDER BY  Fecha DESC";
                } else {

                    $orden = "ORDER BY Orden ASC";
                }

                $response_archivo = array();

                $sql_archivo = "SELECT * FROM Documento" . $Version . " WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and IDTipoArchivo = '" . $r["IDTipoArchivo"] . "' $orden";
                $qry_archivo = $dbo->query($sql_archivo);
                while ($r_archivo = $dbo->fetchArray($qry_archivo)) {
                    $documento["IDClub"] = $r_archivo["IDClub"];
                    $documento["IDTipoArchivo"] = $r_archivo["IDTipoArchivo"];


                    //aca vemos si aplica la clasificacion por grupo
                    $idgrupo = $r_archivo["IDGrupoSocio"];
                    //evaluo si el socio esta en el grupo especifico
                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$idgrupo'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_socios = explode("|||", $SociosGrupo);
                    if (count($array_socios) >= 0) {
                        foreach ($array_socios as $id_socios => $datos_socio) {
                            $array_socios_noticias[] = $datos_socio;
                        }
                    }




                    $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");
                    $tiposocio = $datos_socio["TipoSocio"];

                    $tipos_socios = str_replace("|||", ",", "$r_archivo[IDTipoSocio]");
                    $tipos = substr($tipos_socios, 0, -1);

                    $permiso = "0";
                    $r_detalle = $dbo->query("SELECT * FROM TipoSocio WHERE IDTipoSocio IN ('" . $tipos . "')  ");
                    while ($row_detalle = $dbo->fetchArray($r_detalle)) {

                        if ($row_detalle["Nombre"] == $tiposocio) :
                            $permiso += 1;
                        else :
                            $permiso += 0;
                        endif;
                    }

                    //si tiene el mismo tiposocio se agrega al array
                    if ($permiso >= 1) {
                        $array_socios_noticias[] = $IDSocio;
                    }
                    //si no hay opciones selecionadas se deja pasar para todos
                    if ($r_archivo["IDGrupoSocio"] == 0 and $r_archivo["IDTipoSocio"] == 0) {
                        $array_socios_noticias[] = $IDSocio;
                    }
                    if (in_array($IDSocio, $array_socios_noticias)) {
                        unset($array_socios_noticias);
                        $idgrupo = "";
                        $permiso = "";




                        $foto_servicio = "";
                        if (!empty($r["Icono"])) :
                            $foto_servicio = DOCUMENTO_ROOT . $r_archivo["Icono"];
                        else :
                            $foto_servicio = "";
                        endif;

                        if (empty($r_archivo["IDServicio"])) :
                            $servicio = "";
                            $id_servicio = "";
                        else :
                            $id_servicio = $r_archivo["IDServicio"];
                            $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_archivo["IDServicio"] . "'");
                            $servicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                            $icono_servicio = $dbo->getFields("Servicio", "Icono", "IDServicio = '" . $r_archivo["IDServicio"] . "'");

                            if (empty($foto_servicio)) :
                                if (!empty($icono_servicio)) :
                                    $foto_servicio = SERVICIO_ROOT . $icono_servicio;
                                else :
                                    $foto_servicio = "";
                                endif;
                            endif;
                        endif;

                        $documento["IDServicio"] = $id_servicio;
                        //$documento["Servicio"] = $servicio;
                        $documento["Servicio"] = $r_archivo["Nombre"];
                        $documento["IconoServicio"] = $foto_servicio;
                        $documento["IDDocumento"] = $r_archivo["IDDocumento"];
                        $documento["Titular"] = $r_archivo["Nombre"];
                        $documento["Subtitular"] = $r_archivo["Subtitular"];
                        $documento["Fecha"] = $r_archivo["Fecha"];
                        $documento["Descripcion"] = $r_archivo["Descripcion"];
                        $documento["MostrarPDFInternoAndroid"] = $r_archivo["MostrarPDFInternoAndroid"];

                        //ruta temporal =
                        //$ruta_temporal = str_replace("https", "http", DOCUMENTO_ROOT);
                        $ruta_temporal = DOCUMENTO_ROOT;
                        if (!empty($r_archivo["Archivo1"])) :
                            $archivo = $ruta_temporal . $r_archivo["Archivo1"];
                        else :
                            $archivo = "";
                        endif;
                        $documento["Documento"] = $archivo;

                        if (!empty($archivo))
                            array_push($response_archivo, $documento);
                    }
                }
                //Fin consulto archivos

                $tipo_archivo["Documentos"] = $response_archivo;

                array_push($response, $tipo_archivo);
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

    public function get_documento_dinamico_funcionario($IDClub, $Version)
    {
        $dbo = &SIMDB::get();

        $response = array();
        //$sql = "SELECT TA.*,CTA.Icono,CTA.NombreTipoArchivo FROM TipoArchivo TA,ClubTipoArchivo CTA  WHERE TA.IDTipoArchivo=CTA.IDTipoArchivo and  CTA.IDClub = '".$IDClub."' and Activo = 'S' ORDER BY Nombre";
        $sql = "SELECT * FROM TipoArchivo" . $Version . " WHERE IDClub = '" . $IDClub . "' and Publicar = 'S' and (DirigidoA = 'E' or DirigidoA = 'T') ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . "" . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

            while ($r = $dbo->fetchArray($qry)) {

                $foto = "";
                $foto_icono = $r["Icono"] . "'";

                if (!empty($r["Icono"]) && empty($foto)) {
                    $foto = CLIENTE_ROOT . $r["Icono"];
                }

                $tipo_archivo["IDTipoArchivo"] = $r["IDTipoArchivo"];
                $tipo_archivo["Nombre"] = $r["Nombre"];
                $nombre_tipoarchivo = $r["NombreTipoArchivo"];
                if (!empty($nombre_tipoarchivo)) :
                    $tipo_archivo["Label"] = $nombre_tipoarchivo;
                else :
                    $tipo_archivo["Label"] = $r["Nombre"];
                endif;
                $tipo_archivo["Tipo"] = $r["Tipo"];
                $tipo_archivo["Icono"] = $foto;

                //Consulto los archivos que tiene este tipo de archivo
                $response_archivo = array();
                $sql_archivo = "SELECT * FROM Documento" . $Version . " WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and IDTipoArchivo = '" . $r["IDTipoArchivo"] . "' ORDER BY Nombre";
                $qry_archivo = $dbo->query($sql_archivo);
                while ($r_archivo = $dbo->fetchArray($qry_archivo)) {
                    $documento["IDClub"] = $r_archivo["IDClub"];
                    $documento["IDTipoArchivo"] = $r_archivo["IDTipoArchivo"];
                    $foto_servicio = "";
                    if (!empty($r["Icono"])) :
                        $foto_servicio = DOCUMENTO_ROOT . $r_archivo["Icono"];
                    else :
                        $foto_servicio = "";
                    endif;

                    if (empty($r_archivo["IDServicio"])) :
                        $servicio = "";
                        $id_servicio = "";
                    else :
                        $id_servicio = $r_archivo["IDServicio"];
                        $id_servicio_maestro = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicio = '" . $r_archivo["IDServicio"] . "'");
                        $servicio = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $id_servicio_maestro . "'");
                        $icono_servicio = $dbo->getFields("Servicio", "Icono", "IDServicio = '" . $r_archivo["IDServicio"] . "'");

                        if (empty($foto_servicio)) :
                            if (!empty($icono_servicio)) :
                                $foto_servicio = SERVICIO_ROOT . $icono_servicio;
                            else :
                                $foto_servicio = "";
                            endif;
                        endif;
                    endif;

                    $documento["IDServicio"] = $id_servicio;
                    //$documento["Servicio"] = $servicio;
                    $documento["Servicio"] = $r_archivo["Nombre"];
                    $documento["IconoServicio"] = $foto_servicio;
                    $documento["IDDocumento"] = $r_archivo["IDDocumento"];
                    $documento["Titular"] = $r_archivo["Nombre"];
                    $documento["Subtitular"] = $r_archivo["Subtitular"];
                    $documento["Fecha"] = $r_archivo["Fecha"];
                    $documento["Descripcion"] = $r_archivo["Descripcion"];
                    //ruta temporal =
                    //$ruta_temporal = str_replace("https", "http", DOCUMENTO_ROOT);
                    $ruta_temporal = DOCUMENTO_ROOT;
                    if (!empty($r_archivo["Archivo1"])) :
                        $archivo = $ruta_temporal . $r_archivo["Archivo1"];
                    else :
                        $archivo = "";
                    endif;
                    $documento["Documento"] = $archivo;

                    array_push($response_archivo, $documento);
                }
                //Fin consulto archivos

                $tipo_archivo["Documentos"] = $response_archivo;

                array_push($response, $tipo_archivo);
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
