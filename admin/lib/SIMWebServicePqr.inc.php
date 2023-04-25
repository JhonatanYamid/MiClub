<?php
class SIMWebServicePqr
{
    public function get_configuracion_pqr($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :

            $SQLConfiguracion = "SELECT * FROM ConfiguracionPqr WHERE IDClub = $IDClub AND Activo = 'S'";
            $QRYConfiguracion = $dbo->query($SQLConfiguracion);

            if ($dbo->rows($QRYConfiguracion) > 0) :

                $message = SIMUtil::get_traduccion('', '', 'Datosencontrados', LANG);

                while ($Datos = $dbo->fetchArray($QRYConfiguracion)) :

                    $Configuracion[IDClub] = $Datos[IDClub];
                    $Configuracion[TextoIntroPqr] = $Datos[TextoIntroPqr];
                    $Configuracion[TituloMisPqr] = $Datos[TituloMisPqr];
                    $Configuracion[LabelTipoPqr] = $Datos[LabelTipoPqr];
                    $Configuracion[LabelTituloPqr] = $Datos[LabelTituloPqr];
                    $Configuracion[LabelComentarioPqr] = $Datos[LabelComentarioPqr];
                    $Configuracion[LabelAreaPqr] = $Datos[LabelAreaPqr];
                    $Configuracion[PermiteSeleccionarCategoria] = $Datos[PermiteSeleccionarCategoria];
                    $Configuracion[LabelCategoriaPqr] = $Datos[LabelCategoriaPqr];
                    $Configuracion[PermiteSeleccionarServicios] = $Datos[PermiteSeleccionarServicios];
                    $Configuracion[ObligatorioSeleccionarServicios] = $Datos[ObligatorioSeleccionarServicios];
                    $Configuracion[LabelServiciosPqr] = $Datos[LabelServiciosPqr];
                    $Configuracion[TextoIntroCreacionPqr] = $Datos[TextoIntroCreacionPqr];

                    array_push($response, $Configuracion);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $Configuracion;

            else :

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohaydatosdeconfiguración,porfavorverificar', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = "";

            endif;
        else :

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Faltanparametrosparalaconfiguración', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = "";

        endif;

        return $respuesta;
    }


    public function get_area_club($IDClub)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $sql = "SELECT * From Area WHERE Activo = 'S' and IDClub = '" . $IDClub . "' and MostrarApp <> 'N' ORDER BY Orden,Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $area["IDClub"] = $r["IDClub"];
                $area["IDArea"] = $r["IDArea"];
                $area["Nombre"] = $r["Nombre"];
                $area["CorreoResponsable"] = $r["CorreoResponsable"];
                $foto = "";
                $foto_icono = $r["Icono"];

                if (!empty($r["Icono"])) {
                    $foto = PQR_ROOT . $r["Icono"];
                }

                $area["Icono"] = $foto;

                array_push($response, $area);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_pqr_socio($IDClub, $IDSocio, $IDPqr)
    {
        $dbo = &SIMDB::get();

        $response = array();

        $array_id_consulta[] = $IDSocio;

        if (!empty($IDPqr)) {
            $condicion .= " and IDPqr = '" . $IDPqr . "'";
        }

        if (!empty($IDSocio)) {
            $condicion .= " and IDSocio = '" . $IDSocio . "'";
        }

        $sql = "SELECT * FROM Pqr WHERE IDClub = '" . $IDClub . "' $condicion ORDER BY IDPqr Desc ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($row_pqr = $dbo->fetchArray($qry)) :
                $pqr["IDClub"] = $IDClub;
                $pqr["IDSocio"] = $IDSocio;
                $pqr["IDPqr"] = $row_pqr["IDPqr"];
                $pqr["IDArea"] = $row_pqr["IDArea"];
                $pqr["IDTipoPqr"] = $row_pqr["IDTipoPqr"];
                $pqr["IDPqrEstado"] = $row_pqr["IDPqrEstado"];
                $pqr["NombreArea"] = $dbo->getFields("Area", "Nombre", "IDArea = '" . $row_pqr["IDArea"] . "'");
                $pqr["Tipo"] = $dbo->getFields("TipoPqr", "Nombre", "IDTipoPqr = '" . $row_pqr["IDTipoPqr"] . "'");
                $pqr["Asunto"] = $row_pqr["Asunto"];
                $pqr["Comentario"] = $row_pqr["Descripcion"];

                if (!empty($row_pqr["Archivo1"])) {
                    $pqr["Archivo"] = PQR_ROOT . $row_pqr["Archivo1"];
                } else {
                    $pqr["Archivo"] = "";
                }

                $pqr["Fecha"] = $row_pqr["Fecha"];
                $pqr["Estado"] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");

                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_pqr["IDSocio"] . "' ", "array");
                $pqr["NombreSocio"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                $pqr["CelularSocio"] = $datos_socio["Celular"];
                $pqr["AccionSocio"] = $datos_socio["Accion"];

                //Bitacora Pqr
                $response_bitacora = array();
                $sql_bitacora = $dbo->query("SELECT * FROM Detalle_Pqr WHERE IDPQR = '" . $row_pqr["IDPqr"] . "' AND MostrarSocio <> 'N' Order By 	IDDetallePqr Desc");
                while ($r_bitacora = $dbo->fetchArray($sql_bitacora)) :



                    $bitacora[IDDetallePqr] = $r_bitacora["IDDetallePqr"];
                    if ($r_bitacora[IDUsuario] > 0) {
                        $usuario_responde = SIMUtil::get_traduccion('', '', 'CLUB', LANG) . ": " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuario] . "'");
                        $pos = strpos($r_bitacora["Respuesta"], "<p>"); //para saber si la respuesta esta en html o no
                        //quito caracteres especiales
                        $respuesta_pqr = strip_tags($r_bitacora["Respuesta"]);
                        $respuesta_pqr = str_replace("&nbsp;", " ", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&aacute;", "á", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&eacute;", "é", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&iacute;", "í", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&oacute;", "ó", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&uacute;", "ú", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&ntilde;", "ñ", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&Aacute;", "Á", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&Eacute;", "É", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&Iacute;", "Í", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&Oacute;", "Ó", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&Uacute;", "Ú", $respuesta_pqr);
                        $respuesta_pqr = str_replace("&Ntilde;", "Ñ", $respuesta_pqr);
                        if ($pos !== false) {
                            $respuesta_pqr = html_entity_decode($respuesta_pqr, ENT_QUOTES, "UTF-8");
                            //  $respuesta_pqr .= str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r_bitacora["Respuesta"]);
                        }
                    } elseif ($r_bitacora[IDSocio] > 0) {
                        //$nombre_cliente = utf8_encode( $dbo->getFields( "Socio", "Nombre", "IDSocio = '" . $r_bitacora[ IDSocio ] . "'" ) );
                        //$apellido_cliente = utf8_encode( $dbo->getFields( "Socio", "Apellido", "IDSocio = '" . $r_bitacora[ IDSocio ] . "'" ) );
                        $nombre_cliente = $datos_socio["Nombre"];
                        $apellido_cliente = $datos_socio["Apellido"];
                        $usuario_responde = SIMUtil::get_traduccion('', '', 'Socio', LANG) . ": " . $nombre_cliente . " " . $apellido_cliente;
                        $respuesta_pqr = $r_bitacora["Respuesta"];
                    }
                    $bitacora[UsuarioResponde] = $usuario_responde;
                    $bitacora[RespuestaPqr] = $respuesta_pqr;
                    $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                    $bitacora[FechaRespuesta] = substr($r_bitacora["Fecha"], 0, 10);

                    if (!empty($r_bitacora["Archivo"])) {
                        $array_documento_resp[] = PQR_ROOT . $r_bitacora["Archivo"];
                    }
                    $bitacora["Archivos"] = $array_documento_resp;
                    array_push($response_bitacora, $bitacora);
                    $array_documento_resp = array();
                endwhile;

                $bitacora = array();
                //Agrego el primer comentario como parte del seguimiento
                $bitacora[IDDetallePqr] = $row_pqr["IDPqr"];
                //$nombre_cliente = utf8_encode( $dbo->getFields( "Socio", "Nombre", "IDSocio = '" . $IDSocio . "'" ) );
                //$apellido_cliente = utf8_encode( $dbo->getFields( "Socio", "Apellido", "IDSocio = '" . $IDSocio . "'" ) );
                $nombre_cliente = $datos_socio["Nombre"];
                $apellido_cliente = $datos_socio["Apellido"];
                $usuario_responde = SIMUtil::get_traduccion('', '', 'Socio', LANG) . ": " . $nombre_cliente . " " . $apellido_cliente;
                $respuesta_pqr = $row_pqr["Descripcion"];
                $bitacora[UsuarioResponde] = $usuario_responde;
                $bitacora[RespuestaPqr] = $respuesta_pqr;
                $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                $bitacora[FechaRespuesta] = substr($row_pqr["Fecha"], 0, 10);
                array_push($response_bitacora, $bitacora);

                $pqr["Seguimiento"] = $response_bitacora;
                array_push($response, $pqr);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {

            $pqr["IDSocio"] = "";
            $pqr["IDPqr"] = "";
            $pqr["IDArea"] = "";
            $pqr["IDTipoPqr"] = "";
            $pqr["IDPqrEstado"] = "";
            $pqr["NombreArea"] = "";
            $pqr["Tipo"] = "";
            $pqr["Asunto"] = "";
            $pqr["Comentario"] = "";
            $pqr["Archivo"] = "";
            $pqr["Fecha"] = "";
            $pqr["Estado"] = "";

            array_push($response, $reserva);
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehanencontraronresultados', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            //$respuesta[ "message" ] = "No se han encontraron resultados";
            //$respuesta[ "success" ] = false;
            //$respuesta[ "response" ] = NULL;
        } //end else

        return $respuesta;
    } // fin function

    public function get_tipo_pqr($IDClub)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM TipoPqr WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $tipo_pqr["IDTipoPqr"] = $r["IDTipoPqr"];
                $tipo_pqr["Nombre"] = $r["Nombre"];
                array_push($response, $tipo_pqr);
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

    public function set_pqr($IDClub, $IDArea, $IDSocio, $TipoPqr, $Asunto, $Comentario, $Archivo, $File = "", $IDTipoPqr = "", $NombreColaborador, $ApellidoColaborador, $IDServicio = "")
    {

        //Valido el pseo del archivo
        $tamano_archivo = $File["Archivo"]["size"];
        if ($tamano_archivo >= 6000000) {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDArea) && !empty($IDSocio) && !empty($Comentario)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                //UPLOAD de imagenes

                if (isset($File)) {

                    $files = SIMFile::upload($File["Archivo"], PQR_DIR, "IMAGE");
                    if (empty($files) && !empty($File["Archivo"]["name"])) :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                    $Archivo = $files[0]["innername"];
                } //end if

                //Consulto el siguiente consecutivo del pqr
                $sql_max_numero = "Select MAX(Numero) as NumeroMaximo From Pqr Where IDClub = '" . $IDClub . "'";
                $result_numero = $dbo->query($sql_max_numero);
                $row_numero = $dbo->fetchArray($result_numero);
                $siguiente_consecutivo = (int) $row_numero["NumeroMaximo"] + 1;

                if ($IDClub == 99) {
                    $IDEstadoPqr = 7;
                } else {
                    $IDEstadoPqr = 1;
                }




                //Valido que el pqr no exista por que en algunos casos quedó repetido
                $sql_pqr_existe = "Select *
                                        From Pqr
                                        Where IDTipoPqr = '" . $IDTipoPqr . "' and IDArea = '" . $IDArea . "' and IDSocio = '" . $IDSocio . "' and Tipo = '" . $TipoPqr . "' and Asunto = '" . $Asunto . "' and Descripcion='" . $Comentario . "'";
                $result_pqr_existe = $dbo->query($sql_pqr_existe);
                if ($dbo->rows($result_pqr_existe) <= 0) :
                    $sql_pqr = $dbo->query("
                                        Insert Into Pqr (IDClub, Numero, IDTipoPqr, IDArea, IDServiciosPqr,IDSocio, IDPqrEstado, Tipo, Asunto, Descripcion, Archivo1, Fecha,  NombreColaborador,ApellidoColaborador, UsuarioTrCr, FechaTrCr)
                                        Values ('" . $IDClub . "','" . $siguiente_consecutivo . "','" . $IDTipoPqr . "','" . $IDArea . "','" . $IDServicio . "','" . $IDSocio . "', '" . $IDEstadoPqr . "','" . $TipoPqr . "','" . $Asunto . "','" . $Comentario . "',
                                        '" . $Archivo . "',NOW(),'" . $NombreColaborador . "','" . $ApellidoColaborador . "','WebService',NOW())");
                    $id_pqr = $dbo->lastID();
                    SIMUtil::noticar_nuevo_pqr($id_pqr);
                    SIMUtil::noticar_respuesta_aut_pqr($id_pqr);
                endif;

                // PARA ARRAYANES COLOMBIA SE DEBE CRAR EN ISOLUCION LA PQR.
                /* if($IDClub == 11)
                {
                    require LIBDIR . "SIMWebServiceISolution.inc.php";
                    SIMWebServiceISolution::CrearPQR($id_pqr);
                }
                    */

                //Espcial pqr a ws
                if ($IDClub == 99) {
                    $datos_tipo_pqr = $dbo->fetchAll("TipoPqr", " IDTipoPqr = '" . $IDTipoPqr . "' ", "array");
                    $MensajeWS = $datos_tipo_pqr["Descripcion"];

                    $respuesta_ws = SIMWebServiceApp::crear_pqr_ws($IDClub, $id_pqr);
                    $resp = json_decode($respuesta_ws);
                    if ($resp->code != 0) {
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Hubounproblemaalguardarlasolicitud:', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        $respuesta["message"] = $MensajeWS;
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                // consulto la configuracion de la pqr si tiene un mensaje al guardar
                $mensaje_guardar = $dbo->getFields("ConfiguracionPqr", "TextoGuardarPqr", "IDClub = '" . $IDClub . "'");
                if (!empty($mensaje_guardar)) {
                    $mostrar_mensaje_guardar = $mensaje_guardar;
                } else {
                    $mostrar_mensaje_guardar = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                }

                $respuesta["message"] = $mostrar_mensaje_guardar;
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "11." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_pqr_respuesta($IDClub, $IDSocio, $IDPqr, $Comentario, $IDPqrEstado, $Archivo = "", $File = "")
    {
        $dbo = &SIMDB::get();

        //Valido el pseo del archivo
        $tamano_archivo = $File["Archivo"]["size"];
        if ($tamano_archivo >= 6000000) {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if (!empty($IDClub) && !empty($IDPqr) && !empty($IDSocio) && !empty($Comentario)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                if (isset($File)) {

                    $files = SIMFile::upload($File["Archivo"], PQR_DIR, "IMAGE");
                    if (empty($files) && !empty($File["Archivo"]["name"])) :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                    $Archivo = $files[0]["innername"];
                } //end if

                $sql_pqr = $dbo->query("INSERT INTO Detalle_Pqr (IDPqr, IDSocio, Fecha, Respuesta, Archivo, UsuarioTrCr, FechaTrCr)
                                            Values ('" . $IDPqr . "','" . $IDSocio . "',NOW(), '" . $Comentario . "','" . $Archivo . "','WebService',NOW())");

                if (!empty($IDPqrEstado)) {
                    $sql_estado = "UPDATE Pqr Set Estado = '" . $IDPqrEstado . "' WHERE IDPqr='" . $IDPqr . "'";
                    $dbo->query($sql_estado);
                }

                SIMUtil::noticar_respuesta_pqr($IDPqr, $Comentario);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "12." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }
    //NUEVA

    public function set_calificacion_pqr($IDClub, $IDSocio, $IDPqr, $Comentario, $Calificacion)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDPqr) && !empty($IDSocio) && !empty($Calificacion)) {

            $datos_pqr = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");
            if ($datos_pqr["Calificacion"] == 0) {
                $sql_pqr = $dbo->query("Update Pqr Set Calificacion = '" . $Calificacion . "', ComentarioCalificacion = '" . $Comentario . "', FechaCalificacion = NOW() Where IDPqr = '" . $IDPqr . "'");
                SIMUtil::noticar_calificacion_pqr($IDPqr, $Calificacion);
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Yasehabíaregistradounacalificación', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "120." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    //PQR Funcionarios
    public function get_pqr_funcionario($IDClub, $IDUsuario, $IDPqr)
    {
        $dbo = &SIMDB::get();

        $response = array();

        $array_id_consulta[] = $IDUsuario;

        if (!empty($IDPqr)) {
            $condicion = " and IDPqr = '" . $IDPqr . "'";
        }

        $sql = "SELECT * FROM PqrFuncionario WHERE IDClub = '" . $IDClub . "' and IDUsuarioCreacion = '" . $IDUsuario . "' $condicion ORDER BY FIELD (IDPqrEstado,'1','4','5','2','3'),IDPqrEstado ASC ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($row_pqr = $dbo->fetchArray($qry)) :
                $pqr["IDClub"] = $IDClub;
                $pqr["IDUsuario"] = $IDUsuario;
                $pqr["IDPqr"] = $row_pqr["IDPqr"];
                $pqr["IDArea"] = $row_pqr["IDArea"];
                $pqr["IDTipoPqr"] = $row_pqr["IDTipoPqr"];
                $pqr["IDPqrEstado"] = $row_pqr["IDPqrEstado"];
                $pqr["NombreArea"] = utf8_encode($dbo->getFields("AreaFuncionario", "Nombre", "IDArea = '" . $row_pqr["IDArea"] . "'"));
                $pqr["Tipo"] = utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $row_pqr["IDTipoPqr"] . "'"));
                $pqr["Asunto"] = utf8_encode($row_pqr["Asunto"]);
                $pqr["Comentario"] = utf8_encode($row_pqr["Descripcion"]);
                $pqr["Archivo"] = PQR_ROOT . $row_pqr["Archivo1"];
                $pqr["Fecha"] = $row_pqr["Fecha"];
                $pqr["Estado"] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");

                //Bitacora Pqr
                $response_bitacora = array();
                $sql_bitacora = $dbo->query("SELECT * FROM Detalle_PqrFuncionario WHERE IDPqr = '" . $row_pqr["IDPqr"] . "' Order By IDDetallePqr Desc");
                while ($r_bitacora = $dbo->fetchArray($sql_bitacora)) :
                    $bitacora[IDDetallePqr] = $r_bitacora["IDDetallePqr"];
                    if ($r_bitacora[IDUsuario] > 0) {
                        $usuario_responde = SIMUtil::get_traduccion('', '', 'CLUB', LANG) . ": " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuario] . "'");
                        $pos = strpos($r_bitacora["Respuesta"], "<p>"); //para saber si la respuesta esta en html o no
                        //quito caracteres especiales
                        $respuesta_pqr = strip_tags($r_bitacora["Respuesta"]);
                        //$respuesta_pqr = str_replace("&nbsp;","",$respuesta_pqr);
                        if ($pos !== false) {
                            //$respuesta_pqr = utf8_decode(html_entity_decode($respuesta_pqr, ENT_QUOTES, "UTF-8"));
                        }
                    } elseif ($r_bitacora[IDUsuarioCreacion] > 0) {
                        $nombre_cliente = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuarioCreacion] . "'"));
                        $usuario_responde = SIMUtil::get_traduccion('', '', 'Funcionario', LANG) . ": " . $nombre_cliente;
                        $respuesta_pqr = utf8_encode($r_bitacora["Respuesta"]);
                    }
                    $bitacora[UsuarioResponde] = $usuario_responde;
                    $bitacora[RespuestaPqr] = utf8_encode($respuesta_pqr);
                    $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                    $bitacora[FechaRespuesta] = substr($r_bitacora["Fecha"], 0, 10);
                    array_push($response_bitacora, $bitacora);
                endwhile;

                //Agrego el primer comentario como parte del seguimiento
                $bitacora[IDDetallePqr] = $row_pqr["IDPqr"];
                $nombre_cliente = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDUsuarioCreacion . "'"));
                $usuario_responde = SIMUtil::get_traduccion('', '', 'Funcionario', LANG) . ": " . $nombre_cliente;
                $respuesta_pqr = utf8_encode($row_pqr["Descripcion"]);
                $bitacora[UsuarioResponde] = $usuario_responde;
                $bitacora[RespuestaPqr] = $respuesta_pqr;
                $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                $bitacora[FechaRespuesta] = substr($row_pqr["Fecha"], 0, 10);
                array_push($response_bitacora, $bitacora);

                $pqr["Seguimiento"] = $response_bitacora;
                array_push($response, $pqr);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehanencontraronresultados', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_pqr_socio_funcionario($IDClub, $IDUsuario, $IDPqr)
    {
        $dbo = &SIMDB::get();

        $response = array();

        $array_id_consulta[] = $IDUsuario;

        if (!empty($IDPqr)) {
            $condicion = " and IDPqr = '" . $IDPqr . "'";
        }

        //Selecciona las areas del usuario para saber cuales pqr puede gestionar
        $sql_area_usuario = "Select * From UsuarioArea Where IDUsuario = '" . $IDUsuario . "'";
        $result_area = $dbo->query($sql_area_usuario);
        while ($row_area = $dbo->fetchArray($result_area)) :
            $array_id_area[] = $row_area["IDArea"];
        endwhile;

        if (count($array_id_area) > 0) {
            $id_area = implode(",", $array_id_area);
            $sql = "SELECT * FROM Pqr WHERE IDClub = '" . $IDClub . "' and IDArea in (" . $id_area . ") $condicion ORDER BY FIELD (IDPqrEstado,'1','4','5','2','3'),IDPqrEstado ASC ";
            $qry = $dbo->query($sql);
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($row_pqr = $dbo->fetchArray($qry)) :
                $pqr["IDClub"] = $IDClub;
                $pqr["IDUsuario"] = $IDUsuario;
                $pqr["IDPqr"] = $row_pqr["IDPqr"];
                $pqr["IDSocio"] = $row_pqr["IDSocio"];
                $IDSocio = $row_pqr["IDSocio"];
                $pqr["IDArea"] = $row_pqr["IDArea"];
                $pqr["IDTipoPqr"] = $row_pqr["IDTipoPqr"];
                $pqr["IDPqrEstado"] = $row_pqr["IDPqrEstado"];
                $pqr["NombreArea"] = utf8_encode($dbo->getFields("AreaFuncionario", "Nombre", "IDArea = '" . $row_pqr["IDArea"] . "'"));
                $pqr["Tipo"] = utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $row_pqr["IDTipoPqr"] . "'"));
                $pqr["Asunto"] = utf8_encode($row_pqr["Asunto"]);
                $pqr["Comentario"] = utf8_encode($row_pqr["Descripcion"]);
                $pqr["Archivo"] = PQR_ROOT . $row_pqr["Archivo1"];
                $pqr["Fecha"] = $row_pqr["Fecha"];
                $pqr["Estado"] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");

                //Bitacora Pqr
                $response_bitacora = array();
                $sql_bitacora = $dbo->query("SELECT * FROM Detalle_Pqr WHERE IDPQR = '" . $row_pqr["IDPqr"] . "' Order By 	IDDetallePqr Desc");
                while ($r_bitacora = $dbo->fetchArray($sql_bitacora)) :
                    $bitacora[IDDetallePqr] = $r_bitacora["IDDetallePqr"];
                    if ($r_bitacora[IDUsuario] > 0) {
                        $usuario_responde = SIMUtil::get_traduccion('', '', 'CLUB', LANG) . ": " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuario] . "'");
                        $pos = strpos($r_bitacora["Respuesta"], "<p>"); //para saber si la respuesta esta en html o no
                        //quito caracteres especiales
                        $respuesta_pqr = strip_tags($r_bitacora["Respuesta"]);
                        //$respuesta_pqr = str_replace("&nbsp;","",$respuesta_pqr);
                        if ($pos !== false) {
                            $respuesta_pqr = utf8_decode(html_entity_decode($respuesta_pqr, ENT_QUOTES, "UTF-8"));
                        }
                    } elseif ($r_bitacora[IDSocio] > 0) {
                        $nombre_cliente = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r_bitacora[IDSocio] . "'"));
                        $apellido_cliente = utf8_encode($dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r_bitacora[IDSocio] . "'"));
                        $usuario_responde = SIMUtil::get_traduccion('', '', 'Socio', LANG) . ": " . $nombre_cliente . " " . $apellido_cliente;
                        $respuesta_pqr = utf8_encode($r_bitacora["Respuesta"]);
                    }
                    $bitacora[UsuarioResponde] = $usuario_responde;
                    $bitacora[RespuestaPqr] = utf8_encode($respuesta_pqr);
                    $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                    $bitacora[FechaRespuesta] = substr($r_bitacora["Fecha"], 0, 10);
                    array_push($response_bitacora, $bitacora);
                endwhile;

                //Agrego el primer comentario como parte del seguimiento
                $bitacora[IDDetallePqr] = $row_pqr["IDPqr"];
                $nombre_cliente = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "'"));
                $apellido_cliente = utf8_encode($dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "'"));
                $usuario_responde = SIMUtil::get_traduccion('', '', 'Socio', LANG) . ": " . $nombre_cliente . " " . $apellido_cliente;
                $respuesta_pqr = utf8_encode($row_pqr["Descripcion"]);
                $bitacora[UsuarioResponde] = $usuario_responde;
                $bitacora[RespuestaPqr] = $respuesta_pqr;
                $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                $bitacora[FechaRespuesta] = substr($row_pqr["Fecha"], 0, 10);
                array_push($response_bitacora, $bitacora);

                $pqr["Seguimiento"] = $response_bitacora;
                array_push($response, $pqr);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehanencontraronresultados', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_pqr_asignada_funcionario($IDClub, $IDUsuario, $IDPqr)
    {
        $dbo = &SIMDB::get();

        $response = array();

        $array_id_consulta[] = $IDUsuario;

        if (!empty($IDPqr)) {
            $condicion = " and IDPqr = '" . $IDPqr . "'";
        }

        //Selecciona las areas del usuario para saber cuales pqr puede gestionar de funcionarios
        $sql_area_usuario = "Select * From UsuarioAreaFuncionario Where IDUsuario = '" . $IDUsuario . "'";
        $result_area = $dbo->query($sql_area_usuario);
        while ($row_area = $dbo->fetchArray($result_area)) :
            $array_id_area[] = $row_area["IDArea"];
        endwhile;

        if (count($array_id_area) > 0) {
            $id_area = implode(",", $array_id_area);
            $sql = "SELECT * FROM PqrFuncionario WHERE IDClub = '" . $IDClub . "' and IDArea in (" . $id_area . ") $condicion ORDER BY FIELD (IDPqrEstado,'1','4','5','2','3'),IDPqrEstado ASC ";
            $qry = $dbo->query($sql);
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($row_pqr = $dbo->fetchArray($qry)) :
                $pqr["IDClub"] = $IDClub;
                $pqr["IDUsuario"] = $IDUsuario;
                $pqr["IDPqr"] = $row_pqr["IDPqr"];
                $pqr["IDUsuarioCreacion"] = $row_pqr["IDUsuarioCreacion"];
                $IDUsuario = $row_pqr["IDUsuarioCreacion"];
                $pqr["IDArea"] = $row_pqr["IDArea"];
                $pqr["IDTipoPqr"] = $row_pqr["IDTipoPqr"];
                $pqr["IDPqrEstado"] = $row_pqr["IDPqrEstado"];
                $pqr["NombreArea"] = utf8_encode($dbo->getFields("AreaFuncionario", "Nombre", "IDArea = '" . $row_pqr["IDArea"] . "'"));
                $pqr["Tipo"] = utf8_encode($dbo->getFields("TipoPqrFuncionario", "Nombre", "IDTipoPqr = '" . $row_pqr["IDTipoPqr"] . "'"));
                $UsuarioCreacion = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $row_pqr["IDUsuarioCreacion"] . "'"));
                $pqr["Asunto"] = utf8_encode($row_pqr["Asunto"]) . " creado por: " . $UsuarioCreacion;
                $pqr["Comentario"] = utf8_encode($row_pqr["Descripcion"]);
                $pqr["Archivo"] = PQR_ROOT . $row_pqr["Archivo1"];
                $pqr["Fecha"] = $row_pqr["Fecha"];
                $pqr["Estado"] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");

                //Bitacora Pqr
                $response_bitacora = array();
                $sql_bitacora = $dbo->query("SELECT * FROM Detalle_PqrFuncionario WHERE IDPqr = '" . $row_pqr["IDPqr"] . "' Order By 	IDDetallePqr Desc");
                while ($r_bitacora = $dbo->fetchArray($sql_bitacora)) :
                    $bitacora[IDDetallePqr] = $r_bitacora["IDDetallePqr"];
                    if ($r_bitacora[IDUsuario] > 0) {
                        $usuario_responde = SIMUtil::get_traduccion('', '', 'CLUB', LANG) . ": " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuario] . "'");
                        $pos = strpos($r_bitacora["Respuesta"], "<p>"); //para saber si la respuesta esta en html o no
                        //quito caracteres especiales
                        $respuesta_pqr = strip_tags($r_bitacora["Respuesta"]);
                        //$respuesta_pqr = str_replace("&nbsp;","",$respuesta_pqr);
                        if ($pos !== false) {
                            $respuesta_pqr = utf8_decode(html_entity_decode($respuesta_pqr, ENT_QUOTES, "UTF-8"));
                        }
                    } elseif ($r_bitacora[IDUsuarioCreacion] > 0) {
                        $nombre_cliente = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_bitacora[IDUsuarioCreacion] . "'"));
                        $usuario_responde = SIMUtil::get_traduccion('', '', 'Socio', LANG) . ": " . $nombre_cliente . " " . $apellido_cliente;
                        $respuesta_pqr = utf8_decode($r_bitacora["Respuesta"]);
                    }
                    $bitacora[UsuarioResponde] = $usuario_responde;
                    $bitacora[RespuestaPqr] = utf8_encode($respuesta_pqr);
                    $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                    $bitacora[FechaRespuesta] = substr($r_bitacora["Fecha"], 0, 10);
                    array_push($response_bitacora, $bitacora);
                endwhile;

                //Agrego el primer comentario como parte del seguimiento
                $bitacora[IDDetallePqr] = $row_pqr["IDPqr"];
                $nombre_cliente = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "'"));
                $apellido_cliente = utf8_encode($dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "'"));
                $usuario_responde = SIMUtil::get_traduccion('', '', 'Usuario', LANG) . ": " . $nombre_cliente . " " . $apellido_cliente;
                $respuesta_pqr = utf8_encode($row_pqr["Descripcion"]);
                $bitacora[UsuarioResponde] = $usuario_responde;
                $bitacora[RespuestaPqr] = $respuesta_pqr;
                $bitacora[Estado] = $dbo->getFields("PqrEstado", "Nombre", "IDPqrEstado = '" . $row_pqr["IDPqrEstado"] . "'");
                $bitacora[FechaRespuesta] = substr($row_pqr["Fecha"], 0, 10);
                array_push($response_bitacora, $bitacora);

                $pqr["Seguimiento"] = $response_bitacora;
                array_push($response, $pqr);
            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehanencontraronresultados', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_tipo_pqr_funcionario($IDClub)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM TipoPqrFuncionario WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $tipo_pqr["IDTipoPqr"] = $r["IDTipoPqr"];
                $tipo_pqr["Nombre"] = $r["Nombre"];
                array_push($response, $tipo_pqr);
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

    public function set_pqr_funcionario($IDClub, $IDArea, $IDUsuario, $TipoPqr, $Asunto, $Comentario, $Archivo, $File = "", $IDTipoPqr = "")
    {
        $dbo = &SIMDB::get();

        //Valido el pseo del archivo
        $tamano_archivo = $File["Archivo"]["size"];
        if ($tamano_archivo >= 6000000) {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if (!empty($IDClub) && !empty($IDArea) && !empty($IDUsuario) && !empty($Comentario)) {

            //verifico que el usuario exista y pertenezca al club
            $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_usuario)) {

                //UPLOAD de imagenes

                if (isset($File)) {

                    $files = SIMFile::upload($File["Archivo"], PQR_DIR, "IMAGE");
                    if (empty($files) && !empty($File["Archivo"]["name"])) :
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;
                    $Archivo = $files[0]["innername"];
                } //end if

                //Consulto el siguiente consecutivo del pqr
                $sql_max_numero = "Select MAX(Numero) as NumeroMaximo From PqrFuncionario Where IDClub = '" . $IDClub . "'";
                $result_numero = $dbo->query($sql_max_numero);
                $row_numero = $dbo->fetchArray($result_numero);
                $siguiente_consecutivo = (int) $row_numero["NumeroMaximo"] + 1;

                //Valido que el pqr no exista por que en algunos casos quedó repetido
                $sql_pqr_existe = "Select *
								   From PqrFuncionario
								   Where IDTipoPqr = '" . $IDTipoPqr . "' and IDArea = '" . $IDArea . "' and IDUsuarioCreacion = '" . $IDUsuario . "' and Tipo = '" . $TipoPqr . "' and Asunto = '" . $Asunto . "' and Descripcion='" . $Comentario . "'";
                $result_pqr_existe = $dbo->query($sql_pqr_existe);
                if ($dbo->rows($result_pqr_existe) <= 0) :
                    $sql_pqr = $dbo->query("Insert Into PqrFuncionario (IDClub, Numero, IDTipoPqr, IDArea, IDUsuarioCreacion, IDPqrEstado, Tipo, Asunto, Descripcion, Archivo1, Fecha,  UsuarioTrCr, FechaTrCr)
												Values ('" . $IDClub . "','" . $siguiente_consecutivo . "','" . $IDTipoPqr . "','" . $IDArea . "','" . $IDUsuario . "', '1','" . $TipoPqr . "','" . $Asunto . "','" . $Comentario . "','" . $Archivo . "',NOW(),'WebService',NOW())");
                    $id_pqr = $dbo->lastID();
                    SIMUtil::noticar_nuevo_pqr_funcionario($id_pqr);
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
            $respuesta["message"] = "11." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_pqr_respuesta_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $IDPqrEstado)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDPqr) && !empty($IDUsuario) && !empty($Comentario)) {

            //verifico que el socio exista y pertenezca al club
            $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_usuario)) {

                $sql_pqr = $dbo->query("Insert Into Detalle_PqrFuncionario (IDPqr, IDUsuarioCreacion, Fecha, Respuesta, UsuarioTrCr, FechaTrCr)
										Values ('" . $IDPqr . "','" . $IDUsuario . "',NOW(), '" . $Comentario . "','WebService',NOW())");

                SIMUtil::noticar_respuesta_pqr_funcionario($IDPqr, $Comentario);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "12." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_calificacion_pqr_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $Calificacion)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDPqr) && !empty($IDUsuario) && !empty($Calificacion)) {

            $datos_pqr = $dbo->fetchAll("PqrFuncionario", " IDPqr = '" . $IDPqr . "' ", "array");

            if ($datos_pqr["Calificacion"] == 0) {

                $sql_pqr = $dbo->query("Update PqrFuncionario Set Calificacion = '" . $Calificacion . "', ComentarioCalificacion = '" . $Comentario . "', FechaCalificacion = NOW() Where IDPqr = '" . $IDPqr . "'");

                SIMUtil::noticar_respuesta_pqr_funcionario($IDPqr, $Comentario);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Yasehabíaregistradounacalificación', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "120." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_area_club_funcionario($IDClub)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $sql = "SELECT * From AreaFuncionario WHERE Activo = 'S' and IDClub = '" . $IDClub . "' and MostrarApp <> 'N' ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $area["IDClub"] = $r["IDClub"];
                $area["IDArea"] = $r["IDArea"];
                $area["Nombre"] = utf8_encode($r["Nombre"]);
                $area["CorreoResponsable"] = $r["CorreoResponsable"];
                array_push($response, $area);
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

    public function set_pqr_respuesta_para_socio($IDClub, $IDUsuario, $IDPqr, $Comentario, $IDPqrEstado)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDPqr) && !empty($IDUsuario) && !empty($Comentario)) {

            //verifico que el socio exista y pertenezca al club
            $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_usuario)) {

                //Actualizo el estado del pqr
                if (!empty($IDPqrEstado)) {
                    $sql_estado = $dbo->query("Update Pqr Set IDPqrEstado = '" . $IDPqrEstado . "' Where IDPqr = '" . $IDPqr . "' ");
                }

                $sql_pqr = $dbo->query("Insert Into Detalle_Pqr (IDPqr, IDUsuario, Fecha, Respuesta, UsuarioTrCr, FechaTrCr)
										Values ('" . $IDPqr . "','" . $IDUsuario . "',NOW(), '" . $Comentario . "','WebService',NOW())");

                //Averiguo el nombre del modulo del pqr
                $nombre_modulo = utf8_encode($dbo->getFields("ClubModulo", "Titulo", "IDModulo = '15' and IDClub = '" . $IDClub . "'"));
                if (empty(trim($nombre_modulo))) {
                    // PARA ALGUNOS CLUBES ESTA VACIO EL TITULO PERO NO EL LATERAL
                    $nombre_modulo = utf8_encode($dbo->getFields("ClubModulo", "TituloLateral", "IDModulo = '15' and IDClub = '" . $IDClub . "'"));
                    if (empty(trim($nombre_modulo))) {
                        $nombre_modulo = "Pqr";
                    }
                }

                $frm = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");

                $Mensaje = SIMUtil::get_traduccion('', '', 'CordialSaludo,sehadadorespuestaenelmodulo', LANG) . " \"$nombre_modulo\", " . SIMUtil::get_traduccion('', '', 'desusolicitud', LANG) . " " . trim($frm[Asunto]) . ", " . SIMUtil::get_traduccion('', '', 'porfavoringresealappparaconocermasdetalles', LANG) . ". (" . $frm["Numero"] . ")";

                SIMUtil::envia_respuesta_cliente($frm, $IDPqr, $Comentario, $IDClub);
                SIMUtil::enviar_notificacion_push_general($IDClub, $frm["IDSocio"], $Mensaje);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelusuarionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "12." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_pqr_respuesta_para_funcionario($IDClub, $IDUsuario, $IDPqr, $Comentario, $IDPqrEstado)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDPqr) && !empty($IDUsuario) && !empty($Comentario)) {

            //verifico que el socio exista y pertenezca al club
            $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_usuario)) {

                //Actualizo el estado del pqr
                if (!empty($IDPqrEstado)) {
                    $sql_estado = $dbo->query("Update PqrFuncionario Set IDPqrEstado = '" . $IDPqrEstado . "' Where IDPqr = '" . $IDPqr . "' ");
                }

                $sql_pqr = $dbo->query("Insert Into Detalle_PqrFuncionario (IDPqr, IDUsuario, Fecha, Respuesta, UsuarioTrCr, FechaTrCr)
										Values ('" . $IDPqr . "','" . $IDUsuario . "',NOW(), '" . $Comentario . "','WebService',NOW())");

                //Averiguo el nombre del modulo del pqr
                $nombre_modulo = utf8_encode($dbo->getFields("ClubModulo", "Titulo", "IDModulo = '15' and IDClub = '" . $IDClub . "'"));
                if (empty($nombre_modulo)) {
                    $nombre_modulo = "Pqr";
                }

                $frm = $dbo->fetchAll("Pqr", " IDPqr = '" . $IDPqr . "' ", "array");

                $Mensaje = SIMUtil::get_traduccion('', '', 'CordialSaludo,sehadadorespuestaenelmodulo', LANG) . " \"$nombre_modulo\", " . SIMUtil::get_traduccion('', '', 'porfavoringresealappparaconocermasdetalles', LANG) . ". (" . $frm["Numero"] . ")";

                SIMUtil::envia_respuesta_funcionario($frm, $IDPqr, $Comentario, $IDClub);
                SIMUtil::enviar_notificacion_push_general_funcionario($IDClub, $IDUsuario, $Mensaje);

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "12." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_estado_pqr($IDClub)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM PqrEstado WHERE Publicar = 'S' ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $estado_pqr["IDPqrEstado"] = $r["IDPqrEstado"];
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

    public function get_categoria_pqr_funcionario($IDClub, $IDTipoPqr)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM CategoriaPqrFuncionario WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' AND IDTipoPqr='" . $IDTipoPqr . "' ORDER BY Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $categoria_pqr["IDCategoriapqr"] = $r["IDCategoriaPqrFuncionario"];
                $categoria_pqr["Nombre"] = $r["Nombre"];
                array_push($response, $categoria_pqr);
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

    public function get_categoria_pqr($IDClub, $IDTipoPqr)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM CategoriaPqr WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' AND IDTipoPqr='" . $IDTipoPqr . "' ORDER BY Nombre";

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $categoria_pqr["IDCategoriapqr"] = $r["IDCategoriaPqr"];
                $categoria_pqr["Nombre"] = $r["Nombre"];
                array_push($response, $categoria_pqr);
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

    public function get_servicio_pqr($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :
            $SQLServicioPqr = "SELECT * FROM ServiciosPqr WHERE IDClub = $IDClub AND Activo = 1 AND ParaSocio = 1";
            $QRYServicioPqr = $dbo->query($SQLServicioPqr);

            if ($dbo->rows($QRYServicioPqr)) :
                while ($Servicios = $dbo->fetchArray($QRYServicioPqr)) :
                    $InfoResponse[IDClub] = $Servicios[IDClub];
                    $InfoResponse[IDServicio] = $Servicios[IDServiciosPqr];
                    $InfoResponse[Nombre] = $Servicios[Nombre];

                    array_push($response, $InfoResponse);
                endwhile;

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    public function get_servicio_pqr_funcionario($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        if (!empty($IDClub)) :
            $SQLServicioPqr = "SELECT * FROM ServiciosPqr WHERE IDClub = $IDClub AND Activo = 1 AND ParaSocio = 0";
            $QRYServicioPqr = $dbo->query($SQLServicioPqr);

            if ($dbo->rows($QRYServicioPqr)) :
                while ($Servicios = $dbo->fetchArray($QRYServicioPqr)) :
                    $InfoResponse[IDClub] = $Servicios[IDClub];
                    $InfoResponse[IDServicio] = $Servicios[IDServiciosPqr];
                    $InfoResponse[Nombre] = $Servicios[Nombre];

                    array_push($response, $InfoResponse);
                endwhile;

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        else :
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        endif;

        return $respuesta;
    }

    //FIN PQR Funcionarios
}
