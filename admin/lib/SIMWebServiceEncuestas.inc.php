<?php

class SIMWebServiceEncuestas
{
    public function get_encuesta($IDClub, $IDSocio = "", $IDUsuario = "", $IDModulo = "")
    {



        $dbo = &SIMDB::get();

        if ($IDClub == 227) {
            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
            $datos_socios = "SELECT * FROM Socio WHERE IDSocio ='$IDSocio'  and IDClub = '" . $IDClub . "' LIMIT 1";
            $datos = $dbo->query($datos_socios);
            while ($row = $dbo->fetchArray($datos)) {
                $token = $row["TokenCountryMedellin"];
            }
            if ($IDModulo == 136) :
                $respuesta = SIMWebServiceCountryMedellin::App_ConsultarFormulariostodos($token, $IDClub);
                return $respuesta;
            else :

                $respuesta = SIMWebServiceCountryMedellin::App_ConsultarFormularios($token, $IDClub);
                return $respuesta;

            endif;
        }

        $response = array();

        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        if ((int)$IDModulo > 0) {
            $condicion .= " and IDModulo = '" . $IDModulo . "' ";
        } else {
            $condicion .= " and IDModulo = 0 ";
        }

        $sql = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($IDSocio)) {
                    $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta($r, $IDSocio);
                } elseif (!empty($IDUsuario)) {
                    $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta($r, $IDUsuario);
                }

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDEncuesta"] = $r["IDEncuesta"];
                    $encuesta["IDModulo"] = $r["IDModulo"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];

                    if (!empty($r["ImagenEncuesta"])) :
                        $foto = BANNERAPP_ROOT . $r["ImagenEncuesta"];
                    else :
                        $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '58' and IDClub='" . $IDClub . "' ", "array");
                        $icono_modulo = $datos_modulo["Icono"];
                        if (!empty($datos_modulo["Icono"])) :
                            $foto = MODULO_ROOT . $datos_modulo["Icono"];
                        else :
                            $foto = "";
                        endif;
                    endif;

                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM EncuestaRespuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuesta = '" . $r["IDEncuesta"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    }

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $opciones_respuesta = array();
                    $response_valores = array();
                    $sql_respuesta = "Select * From Pregunta Where IDEncuesta = '" . $encuesta["IDEncuesta"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :
                        $pregunta["IDPregunta"] = $row_pregunta["IDPregunta"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        $pregunta["Valores"] = $row_pregunta["Valores"];
                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        if ($row_pregunta["TipoCampo"] == "imagen" || $row_pregunta["TipoCampo"] == "firmadigital") {
                            $pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPregunta"];
                        }

                        if ($row_pregunta["TipoCampo"] == "sociounico") {
                            $opciones_respuesta["IDPregunta"] = $row_pregunta["IDPregunta"];
                            $opciones_respuesta["IDPreguntaRespuesta"] = $row_pregunta["IDPregunta"];
                            $opciones_respuesta["Opcion"] = $row_pregunta["Valores"];
                            array_push($response_valores, $opciones_respuesta);
                            $pregunta["ValoresLista"] = $response_valores;
                        }



                        array_push($response_pregunta, $pregunta);
                    endwhile;

                    $encuesta["Preguntas"] = $response_pregunta;
                    array_push($response, $encuesta);
                }
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function    

    public function get_encuesta_calificacion($IDClub, $IDSocio = "", $IDUsuario = "")
    {

        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Encuesta2 WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {

                $mostrar_encuesta = SIMWebServiceApp::verifica_ver_encuesta2($r, $IDSocio);

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDEncuesta"] = $r["IDEncuesta2"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];

                    $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '101' and IDClub='" . $IDClub . "' ", "array");
                    $icono_modulo = $datos_modulo["Icono"];
                    if (!empty($datos_modulo["Icono"])) :
                        $foto = MODULO_ROOT . $datos_modulo["Icono"];
                    else :
                        $foto = "";
                    endif;
                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM  Encuesta2Respuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuesta2 = '" . $r["IDEncuesta2"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    }

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "SELECT * FROM PreguntaEncuesta2 Where IDEncuesta2 = '" . $encuesta["IDEncuesta"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->fetchArray($r_encuesta)) :
                        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "'", "array");
                        $TextoPregunta = $row_pregunta["EtiquetaCampo"];
                        $TextoPregunta = str_replace("[NombreSocio]", $datos_socio["Nombre"] . " " . $datos_socio["Apellido"], $TextoPregunta);
                        $TextoPregunta = str_replace("[AccionSocio]", $datos_socio["Accion"], $TextoPregunta);
                        $TextoPregunta = str_replace("[CasaSocio]", $datos_socio["Predio"], $TextoPregunta);
                        $TextoPregunta = str_replace("[DocumentoSocio]", $datos_socio["NumeroDocumento"], $TextoPregunta);

                        $pregunta["IDPreguntaEncuesta"] = $row_pregunta["IDPreguntaEncuesta2"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $TextoPregunta;
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        //Consulto los valores
                        $sql_opciones = "SELECT * FROM Encuesta2OpcionesRespuesta WHERE IDEncuesta2Pregunta = '" . $row_pregunta["IDPreguntaEncuesta2"] . "' Order by orden";
                        $r_opciones = $dbo->query($sql_opciones);
                        $opciones_respuesta = array();
                        $response_valores = array();
                        while ($row_opciones = $dbo->fetchArray($r_opciones)) {
                            $opciones_respuesta["IDEncuestaPregunta"] = $row_opciones["IDEncuesta2Pregunta"];
                            $opciones_respuesta["IDEncuesta2OpcionesRespuesta"] = $row_opciones["IDEncuesta2OpcionesRespuesta"];
                            //$opciones_respuesta[ "IDDiagnosticoPreguntaSiguiente" ] = $row_opciones[ "IDDiagnosticoPreguntaSiguiente" ];
                            $opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
                            $opciones_respuesta["RespuestaCorrecta"] = $row_opciones["RespuestaCorrecta"];
                            $opciones_respuesta["Puntos"] = $row_opciones["Puntos"];
                            array_push($response_valores, $opciones_respuesta);
                        }
                        $pregunta["Valores"] = $response_valores;
                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        array_push($response_pregunta, $pregunta);
                    endwhile;

                    $encuesta["Preguntas"] = $response_pregunta;
                    array_push($response, $encuesta);
                }
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function set_respuesta_encuesta_calificacion($IDClub, $IDSocio, $IDEncuesta, $Respuestas, $IDUsuario = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDEncuesta)) {
            $guardar_encuesta = 0;
            $datos_diagnostico = $dbo->fetchAll("Encuesta2", " IDEncuesta2 = '" . $IDEncuesta . "' and IDClub='" . $IDClub . "' ", "array");
            $contesta_una = $datos_diagnostico["UnaporSocio"];
            if ($contesta_una == "S") {
                $sql_resp = "SELECT IDEncuesta2 From Encuesta2Respuesta Where IDSocio = '" . $IDSocio . "' and IDDiagnostico = '" . $IDDiagnostico . "' Limit 1";
                $r_resp = $dbo->query($sql_resp);
                if ($dbo->rows($r_resp) <= 0) {
                    $guardar_encuesta = 1;
                }
            } else {
                $guardar_encuesta = 1;
            }

            if (!empty($IDUsuario)) {
                $IDSocio = $IDUsuario;
                $TipoUsuario = "Funcionario";
            } else {
                $TipoUsuario = "Socio";
            }

            if ($guardar_encuesta == 1) {
                $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                $datos_respuesta = json_decode($Respuestas, true);

                if (count($datos_respuesta) > 0) :
                    foreach ($datos_respuesta as $detalle_respuesta) :
                        $sql_datos_form = $dbo->query("INSERT INTO Encuesta2Respuesta (IDEncuesta2, IDSocio, IDPreguntaEncuesta2, IDEncuesta2OpcionesRespuesta, TipoUsuario, Valor, Puntos, RespuestaCorrecta, FechaTrCr)
                                                                                                                        Values ('" . $IDEncuesta . "','" . $IDSocio . "','" . $detalle_respuesta["IDEncuestaPregunta"] . "','" . $detalle_respuesta["ValorID"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "','" . $detalle_respuesta["Puntos"] . "','" . $detalle_respuesta["RespuestaCorrecta"] . "',NOW())");
                        $suma_puntos += $detalle_respuesta["Puntos"];
                    endforeach;
                endif;

                $respuesta["message"] = "Datos enviado correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "Este encuesta ya había sido contestada por ud, solo se permite 1 vez por día";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "E1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_respuesta_encuesta($IDClub, $IDSocio, $IDEncuesta, $Respuestas, $IDUsuario = "", $Archivo, $File = "", $RespuestaDesdeAdministrador = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDEncuesta)) {

            if ($IDClub == 190 && empty($RespuestaDesdeAdministrador)) {
                $respuesta["message"] = "No se puede realizar la encuesta en el app.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            $guardar_encuesta = 0;
            $contesta_una = utf8_decode($dbo->getFields("Encuesta", "UnaporSocio", "IDEncuesta = '" . $IDEncuesta . "'"));
            if ($contesta_una == "S") {
                $sql_resp = "Select IDEncuesta From EncuestaRespuesta Where IDSocio = '" . $IDSocio . "' and IDEncuesta = '" . $IDEncuesta . "' Limit 1";
                $r_resp = $dbo->query($sql_resp);
                if ($dbo->rows($r_resp) <= 0) {
                    $guardar_encuesta = 1;
                }
            } else {
                $guardar_encuesta = 1;
            }

            if (!empty($IDUsuario)) {
                $IDSocio = $IDUsuario;
                $TipoUsuario = "Funcionario";
            } else {
                $TipoUsuario = "Socio";
            }

            if ($guardar_encuesta == 1) {
                $sql_pregunta = "SELECT IDPregunta, Obligatorio,TipoCampo FROM Pregunta WHERE IDEncuesta = '" . $IDEncuesta . "' ";
                $r_pregunta = $dbo->query($sql_pregunta);
                while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
                    $array_pregunta[$row_pregunta["IDPregunta"]] = $row_pregunta["Obligatorio"];
                    $array_preguntaImage[$row_pregunta["IDPregunta"]] = $row_pregunta["TipoCampo"];
                }

                $datos_correctos = "S";
                if ($IDClub == 227) {

                    require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
                    $datos_socios = "SELECT * FROM Socio WHERE IDSocio ='$IDSocio'  and IDClub = '" . $IDClub . "' LIMIT 1";
                    $datos = $dbo->query($datos_socios);
                    while ($row = $dbo->fetchArray($datos)) {
                        $token = $row["TokenCountryMedellin"];
                    }
                    $respuesta = SIMWebServiceCountryMedellin::App_DiligenciarFormulario($IDEncuesta, $Respuestas, $token);
                    return $respuesta;
                }


                $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                $datos_respuesta = json_decode($Respuestas, true);
                if (count($datos_respuesta) > 0) :
                    foreach ($datos_respuesta as $detalle_respuesta) {
                        if (($detalle_respuesta["Valor"] == "null" || $detalle_respuesta["Valor"] == "") && $array_pregunta[$detalle_respuesta["IDPregunta"]] == "S" && $array_preguntaImage[$detalle_respuesta["IDPregunta"]] != "imagen" && $array_preguntaImage[$detalle_respuesta["IDPregunta"]] != "firmadigital") {
                            $datos_correctos = "N";
                            $PreguntaValida = $detalle_respuesta["IDPregunta"];
                            break;
                        } else {
                            $datos_correctos = "S";
                        }
                    }
                    if ($datos_correctos == "N") {
                        $respuesta["message"] = "Datos No fueron enviados, alguna de las respuestas es incorrecta, por favor verifique." . $PreguntaValida;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                    //verifico que el socio no este inactivo
                    $IDEstado = $dbo->getFields("Socio", "IDEstadoSocio", "IDSocio = '" . $IDSocio . "'");

                    if ($IDEstado == 2) {
                        $respuesta["message"] = "No puede realizar la encuesta, su estado es inactivo";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }



                    foreach ($datos_respuesta as $detalle_respuesta) :

                        //VALORES LISTA ESTA EL CAMPO PARA SELECCIONAR AL SOCIO
                        $valores_lista = $detalle_respuesta["ValoresLista"];

                        if (count($valores_lista) > 0) {
                            foreach ($valores_lista as $detalle_lista) {
                                $sql_datos_form = "INSERT INTO EncuestaRespuesta (IDEncuesta, IDSocio, IDPregunta, TipoUsuario, Valor, FechaTrCr)Values ('" . $IDEncuesta . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','"  . $detalle_lista["Valor"] . "',NOW())";
                                $dbo->query($sql_datos_form);
                                //  echo $sql_datos_form;
                            }
                        } else {

                            $sql_datos_form = $dbo->query("Insert Into EncuestaRespuesta (IDEncuesta, IDSocio, IDPregunta,  TipoUsuario, Valor, FechaTrCr) Values ('" . $IDEncuesta . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())");
                            // $sql_datos_form = "Insert Into EncuestaRespuesta (IDEncuesta, IDSocio, IDPregunta,  TipoUsuario, Valor, FechaTrCr) Values ('" . $IDEncuesta . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())";
                            // echo "INSERT2:" . $sql_datos_form;
                        }

                        //preguntas y respuestas para enviarlas al correo
                        if ($array_preguntaImage[$detalle_respuesta["IDPregunta"]] != "imagen") {
                            $NombrePreguntasRespuesta = $dbo->getFields("Pregunta", "EtiquetaCampo", "IDPregunta = '" . $detalle_respuesta["IDPregunta"] . "'") . ":" . $detalle_respuesta["Valor"];
                            $opciones .= "<li>" . $NombrePreguntasRespuesta . "</li>";
                        }
                    endforeach;

                endif;

                //subir las imagenes
                if (isset($File)) {
                    //$nombrefoto.=json_encode($File);
                    foreach ($File as $nombre_archivo => $archivo) {
                        $ArrayPreguntaEncuesta = explode("|", $nombre_archivo);
                        $IDPreguntaActualiza = $ArrayPreguntaEncuesta[1];
                        //$nombrefoto.=$archivo["name"];
                        //$nombrefoto.=json_encode($archivo);
                        $tamano_archivo = $archivo["size"];
                        if ($tamano_archivo >= 6000000) {
                            $respuesta["message"] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        } else {
                            $files = SIMFile::upload($File[$nombre_archivo], PQR_DIR, "IMAGE");
                            if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
                                $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                            $Archivo = $files[0]["innername"];
                            $actualiza_pregunta = "UPDATE EncuestaRespuesta SET Valor = '" . $Archivo . "' WHERE IDPregunta ='" . $IDPreguntaActualiza . "' and IDEncuesta  = '" . $IDEncuesta . "' and IDSocio = '" . $IDSocio . "' ORDER BY FechaTrCr DESC LIMIT 1";
                            $dbo->query($actualiza_pregunta);
                            //$nombrefoto.=    $actualiza_pregunta;

                            //si la pregunta es imagen la adjunto en el correo
                            $NombrePreguntasRespuesta = $dbo->getFields("Pregunta", "EtiquetaCampo", "IDPregunta = '" . $IDPreguntaActualiza . "'") . ":" .  "<img src='" . PQR_ROOT . $Archivo . "'  width='200px' height='200px'>";
                            $opciones .= "<li>" . $NombrePreguntasRespuesta . "</li>";
                        }
                    }
                }

                //verifico si hay un correo en la encuesta para enviar el correo de que hubo un registro 
                $CorreoRegistro = $dbo->getFields("Encuesta", "CorreoRegistro", "IDEncuesta = '" . $IDEncuesta . "'");
                $NombreEncuesta = $dbo->getFields("Encuesta", "Nombre", "IDEncuesta = '" . $IDEncuesta . "'");
                if (!empty($CorreoRegistro)) {

                    $Asunto = "Registro Nuevo Encuesta";
                    $Mensaje = "Se ha creado un nuevo registro de la encuesta:" . $NombreEncuesta .  "<br><br>" . $opciones . "<br>" . " para validar ingrese a la plataforma.";
                    SIMUtil::envia_correo_general($IDClub, $CorreoRegistro, $Mensaje, $Asunto);
                }

                $respuesta_personalizada = $dbo->getFields("Encuesta", "RespuestaGuardar", "IDEncuesta = '" . $IDEncuesta . "'");
                if (!empty($respuesta_personalizada)) {
                    $respuesta_enc = $respuesta_personalizada;
                } else {
                    $respuesta_enc = "Guardado";
                }

                // PARA LAGUNITA CLUB HAY QUE RERPORTAR EL PAGO EN EL SISTEMA DE ELLOS CON WS
                if ($IDClub == 141 && $IDEncuesta == 335) :
                    require LIBDIR . "SIMWebServiceLagunita.inc.php";
                    SIMWebServiceLagunita::reportePagos($IDSocio, "", "S", "");
                endif;

                // PARA URUGUAY HAY QUE REPORTAR LA ENCUESTA DE CONTROL DE SALUD
                if ($IDClub == 125 && $IDEncuesta == 463) :
                    require LIBDIR . "SIMUruguay.inc.php";
                    SIMUruguay::notificar_salud($IDSocio, $Respuestas);
                endif;

                $respuesta["message"] = $respuesta_enc;
                $respuesta["success"] = true;
                $respuesta["response"] = $datos_reserva;
            } else {
                $respuesta["message"] = "Esta encuesta ya había sido contestada por ud, solo se permite 1 vez";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "E1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_configuracion_encuesta_calificacion($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM ConfiguracionEncuesta2  WHERE IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                $configuracion["LabelResultado"] = $r["LabelResultado"];
                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "Sin configuracion";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_configuracion_encuesta($IDClub, $IDSocio, $IDUsuario, $IDModulo)
    {
        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT IDClub,PermiteMostrarHistorial,LabelMostrarHistorial FROM ConfiguracionEncuesta WHERE IDClub='" . $IDClub . "' AND IDModulo='" . $IDModulo . "'";


        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {


                $configuracionEncuesta["IDClub"] = $r["IDClub"];
                $configuracionEncuesta["PermiteMostrarHistorial"] = $r["PermiteMostrarHistorial"];
                $configuracionEncuesta["LabelMostrarHistorial"] = $r["LabelMostrarHistorial"];
                array_push($response, $configuracionEncuesta);
            } //end while
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //end if
        else {
            $respuesta["message"] = "No se encontro configuracion encuesta";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } //fin function

    public function get_mis_encuestas_historial($IDClub, $IDSocio, $IDModulo)
    {
        $dbo = SIMDB::get();

        if (!empty($IDSocio)) {
            $response = array();
            if (!empty($IDSocio)) {
                $condicion = " AND IDSocio = '" . $IDSocio . "' ";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                $info = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
            }



            $sql = "SELECT ER.FechaTrCr,ER.IDEncuesta,ER.IDSocio,ER.IDPregunta,ER.Valor FROM EncuestaRespuesta ER,Encuesta E WHERE ER.IDEncuesta = E.IDEncuesta AND E.IDmodulo='" . $IDModulo . "'"  . $condicion . "GROUP by FechaTrCr Order by FechaTrCr DESC Limit 15";
            $query = $dbo->query($sql);


            if ($dbo->rows($query) > 0) {
                $message = $dbo->rows($query) . " Encontrados";
                while ($r = $dbo->fetchArray($query)) :
                    $DetalleResp = "";
                    $Datos["IDEncuesta"] = $r["IDEncuesta"];
                    $Datos["Fecha"] = substr($r["FechaTrCr"], 0, 10);
                    $Datos["Hora"] = substr($r["FechaTrCr"], 10);
                    $Datos["Texto"] = $info;

                    //Consulta las respuestas de la encuesta
                    $sql_detalle = "SELECT P.EtiquetaCampo, ER.IDEncuesta, ER.IDEncuestaRespuesta,ER.FechaTrCr, ER.Valor
                                    FROM EncuestaRespuesta ER, Pregunta P
                                    WHERE  ER.IDPregunta = P.IDPregunta $condicion
                                        AND ER.IDEncuesta = '" . $r["IDEncuesta"] . "' AND (ER.FechaTrCr between '" . $r["FechaTrCr"] . "' AND '" . $r["FechaTrCr"] . "') 
                                    ORDER BY P.Orden ASC";

                    $qry_detalle = $dbo->query($sql_detalle);

                    while ($r_detalle = $dbo->fetchArray($qry_detalle)) {
                        $DetalleResp .= "<b>" . $r_detalle["EtiquetaCampo"] . ":</b><br>" . $r_detalle["Valor"] . "<br><br>";
                    }

                    $Datos["Descripcion"] = $DetalleResp;
                    array_push($response, $Datos);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } // end if
            else {
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "ER. Faltan Parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    } // fin function
}
