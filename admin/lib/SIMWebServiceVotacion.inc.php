<?php

class SIMWebServiceVotacion
{
    public function get_configuracion_votacion($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();
        $response = array();
        $configuracion["IDClub"] = $IDClub;
        $configuracion["LabelBotonResultados"] = "Ver Resultados";
        $configuracion["LabelBotonRefrescar"] = "Pulse para refrescar";
        $configuracion["BotonResultados"] = "S";
        //Busco el evento activo de la votacion
        $IDEvento = $dbo->getFields("VotacionEvento", "IDVotacionEvento", "Activo = 'S' and IDClub = '" . $IDClub . "'");
        $configuracion["URLResultados"] = URLROOT . "plataform/screen/pantallavotacion.php?IDVotacionEvento=" . $IDEvento . "&IDClub=" . $IDClub;
        array_push($response, $configuracion);
        $respuesta["message"] = "1 resultado encontrado";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    } // fin function

    public function get_votacion($IDClub, $IDSocio = "", $IDUsuario = "")
    {

        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T' or DirigidoA = 'L') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T' or DirigidoA = 'L') ";
        }

        $sql = "SELECT * FROM Votacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {
                if ($r["DirigidoAGeneral"] == "GS") :

                    //VALIDO EL USUARIO O SOCIO EN UNA MISMA VARIABLE
                    if (empty($IDSocio)) :
                        $IDSocio = $IDUsuario;
                    elseif (empty($IDUsuario)) :
                        $IDSocio = $IDSocio;
                    endif;

                    $idgrupo = $r["IDGrupoSocio"];
                    //evaluo si el socio esta en el grupo especifico
                    $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio ='$idgrupo'"); //evaluamos los grupos pero hay que recorrer los resultados
                    $array_socios = explode("|||", $SociosGrupo);
                    //cuento si hay socios en el grupo y los guardo en el array
                    if (count($array_socios) >= 0) :
                        foreach ($array_socios as $id_socios => $datos_socio) :
                            $array_socios_votacion[] = $datos_socio;
                        endforeach;
                    endif;
                    //verifico si el socio o usuario esta en el array
                    if (in_array($IDSocio, $array_socios_votacion)) :
                        unset($array_socios_votacion);
                        $mostrar_encuesta = 1;

                    else :
                        $mostrar_encuesta = 0;

                    endif;

                else :
                    $mostrar_encuesta = SIMWebServiceApp::verifica_ver_votacion($r, $IDSocio, $IDUsuario);
                endif;

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDVotacion"] = $r["IDVotacion"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];
                    $encuesta["Georeferenciacion"] = $r["Georeferenciacion"];
                    $encuesta["Latitud"] = $r["Latitud"];
                    $encuesta["Longitud"] = $r["Longitud"];
                    $encuesta["Rango"] = $r["Rango"];
                    $encuesta["MensajeFueraRango"] = $r["MensajeFueraRango"];

                    $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '70' and IDClub='" . $IDClub . "' ", "array");
                    $icono_modulo = $datos_modulo["Icono"];
                    if (!empty($datos_modulo["Icono"])) :
                        $foto = MODULO_ROOT . $datos_modulo["Icono"];
                    else :
                        $foto = "";
                    endif;
                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM VotacionRespuesta WHERE IDSocio='" . $IDSocio . "' and IDVotacion = '" . $r["IDVotacion"] . "'";
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
                    $sql_respuesta = "SELECT * From PreguntaVotacion Where IDVotacion = '" . $encuesta["IDVotacion"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :
                        $pregunta["IDPregunta"] = $row_pregunta["IDPregunta"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        $pregunta["Valores"] = $row_pregunta["Valores"];

                        //Consulto los valores
                        $sql_opciones = "SELECT * FROM VotacionOpcionesRespuesta WHERE IDPreguntaVotacion = '" . $row_pregunta["IDPregunta"] . "' order by Orden";
                        $r_opciones = $dbo->query($sql_opciones);
                        $opciones_respuesta = array();
                        $response_valores = array();
                        while ($row_opciones = $dbo->fetchArray($r_opciones)) {
                            $opciones_respuesta["IDPregunta"] = $row_opciones["IDPreguntaVotacion"];
                            $opciones_respuesta["IDPreguntaRespuesta"] = $row_opciones["IDVotacionOpcionesRespuesta"];
                            $opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
                            array_push($response_valores, $opciones_respuesta);
                        }
                        $pregunta["ValoresLista"] = $response_valores;

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

    public function set_respuesta_votacion($IDClub, $IDSocio, $IDVotacion, $Respuestas, $IDUsuario = "", $Dispositivo = "", $UsuarioCrea = "")
    {
        $dbo = &SIMDB::get();
        $IP = SIMUtil::get_IP();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDVotacion)) {
            $guardar_encuesta = 0;
            $contesta_una = utf8_decode($dbo->getFields("Votacion", "UnaporSocio", "IDVotacion = '" . $IDVotacion . "'"));
            $nombre_votacion = utf8_decode($dbo->getFields("Votacion", "Nombre", "IDVotacion = '" . $IDVotacion . "'"));
            if ($contesta_una == "S") {
                $sql_resp = "Select IDVotacion From VotacionRespuesta Where IDSocio = '" . $IDSocio . "' and IDVotacion = '" . $IDVotacion . "' Limit 1";
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

            $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
            if ($guardar_encuesta == 1) {

                $datos_respuesta = json_decode($Respuestas, true);
                if (count($datos_respuesta) > 0) :

                    foreach ($datos_respuesta as $detalle_respuesta) :

                        // print_r($detalle_respuesta);

                        //reviso si existeun evento activo
                        $sql_evento = "SELECT IDVotacionEvento FROM VotacionEvento WHERE IDClub = '" . $IDClub . "' and Activo = 'S' Order by IDVotacionEvento DESC Limit 1";
                        $r_evento = $dbo->query($sql_evento);
                        $row_evento = $dbo->fetchArray($r_evento);
                        if ($row_evento["IDVotacionEvento"] > 0) {
                            $IDEvento = $row_evento["IDVotacionEvento"];
                        } else {
                            $IDEvento = "1";
                        }
                        //Verifico el coeficiente del votante
                        $coeficiente = SIMUtil::verifica_coeficiente($IDSocio, $IDEvento);
                        if ((float) ($coeficiente) <= 0) {
                            $PesoVoto = 1;
                        } else {
                            $PesoVoto = $coeficiente;
                        }

                        $valores_lista = $detalle_respuesta["ValoresLista"];
                        // $valores_lista = $detalle_respuesta["Valor"];

                        //echo "valores_lista:" . $valores_lista;
                        if (count($valores_lista) > 0) {
                            foreach ($valores_lista as $detalle_lista) {
                                $sql_datos_form = "INSERT INTO VotacionRespuesta (IDVotacion, IDSocio, IDPregunta,  IDVotacionOpcionesRespuesta , TipoUsuario, Valor, ValorID, PesoVoto, IP, Dispositivo,IDUsuarioRemplazoVotante,FechaTrCr)
                                   
                                                                                                                        Values ('" . $IDVotacion . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $detalle_lista["IDPreguntaRespuesta"] . "','" . $TipoUsuario . "','" . $detalle_lista["Valor"] . "','" . $detalle_lista["ValorID"] . "','" . $PesoVoto . "','" . $IP . "','" . $Dispositivo . "','" . $UsuarioCrea . "',NOW())";
                                $dbo->query($sql_datos_form);
                                //echo "sql1" . $sql_datos_form;
                            }
                        } else {
                            $sql_datos_form = $dbo->query("INSERT INTO VotacionRespuesta (IDVotacion, IDSocio, IDPregunta, TipoUsuario, Valor, PesoVoto, IP, Dispositivo,IDUsuarioRemplazoVotante,FechaTrCr) Values ('" . $IDVotacion . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "','" . $PesoVoto . "','" . $IP . "','" . $Dispositivo . "','" . $UsuarioCrea . "',NOW())");
                            // $sql_datos_form = "INSERT INTO VotacionRespuesta (IDVotacion, IDSocio, IDPregunta, TipoUsuario, Valor, PesoVoto, IP, Dispositivo,FechaTrCr) Values ('" . $IDVotacion . "','" . $IDSocio . "','" . $detalle_respuesta["IDPregunta"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "','" . $PesoVoto . "','" . $IP . "','" . $Dispositivo . "',NOW())";

                            //echo "sql2" . $sql_datos_form;
                        }

                    endforeach;
                    SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocio, "Tu votacion a: " . $nombre_votacion . " fue contabilizada");
                endif;
                $respuesta["message"] = "guardado";
                $respuesta["success"] = true;
                $respuesta["response"] = $datos_reserva;
            } else {
                $respuesta["message"] = "Ya había votado, solo se permite 1 vez";
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
}
