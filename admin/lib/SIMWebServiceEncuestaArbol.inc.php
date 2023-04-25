<?php
class SIMWebServiceEncuestaArbol
{

    public function get_configuracion_movilidad($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();

        // $response = array();


        $sql = "SELECT * FROM ConfiguracionEncuestaArbol WHERE Activo = 'S' and IDClub = '" . $IDClub . "' ORDER BY IDConfiguracionEncuestaArbol LIMIT 1";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {

            $r = $dbo->assoc($qry);
            $encuesta["MostrarBotonHistorial"] = $r["MostrarBotonHistorial"];
            $encuesta["TextoBotonHistorial"] = $r["TextoBotonHistorial"];

            $respuesta["message"] = NULL;
            $respuesta["success"] = true;
            $respuesta["response"] = $encuesta;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function
    public function get_movilidad($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM EncuestaArbol WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {

                $mostrar_encuesta = self::verifica_ver_EncuestaArbol($r, $IDSocio, $IDUsuario);

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDMovilidad"] = $r["IDEncuestaArbol"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];

                    $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '104' and IDClub='" . $IDClub . "' ", "array");
                    $icono_modulo = $datos_modulo["Icono"];
                    if (!empty($datos_modulo["Icono"])) :
                        $foto = MODULO_ROOT . $datos_modulo["Icono"];
                    else :
                        $foto = "";
                    endif;
                    $encuesta["Icono"] = $foto;

                    //Verifico si el socio ya contesto la encuesta
                    if (!empty($IDSocio)) {
                        $sql_contesta = "SELECT * FROM EncuestaArbolRespuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuestaArbol = '" . $r["IDEncuestaArbol"] . "'";
                        $r_contesta = $dbo->query($sql_contesta);
                        if ($dbo->rows($r_contesta > 0)) {
                            $encuesta["Respondida"] = "S";
                        } else {
                            $encuesta["Respondida"] = "N";
                        }
                    }

                    //consulto si tiene preguntas asociadas
                    $sql_pregunta_hijos = "SELECT EAOR.IDEncuestaArbolPregunta,IDEncuestaArbolPreguntaSiguiente,IDEncuestaArbolOpcionesRespuesta
																	 FROM PreguntaEncuestaArbol PEA, EncuestaArbolOpcionesRespuesta EAOR
																	 WHERE EAOR.IDEncuestaArbolPregunta = PEA.IDPreguntaEncuestaArbol
																	 And IDEncuestaArbol = '" . $r["IDEncuestaArbol"] . "' and IDEncuestaArbolPreguntaSiguiente>0";
                    $r_pregunta_hijo = $dbo->query($sql_pregunta_hijos);
                    while ($row_pregunta_hijo = $dbo->fetchArray($r_pregunta_hijo)) {
                        $arr_PreguntaHijo = explode('|', $row_pregunta_hijo['IDEncuestaArbolPreguntaSiguiente']);
                        foreach ($arr_PreguntaHijo as $hijo) {
                            $row_pregunta_hijo['IDEncuestaArbolPreguntaSiguiente'] = $hijo;
                            $array_hijo[$hijo][] = $row_pregunta_hijo;
                        }
                    }

                    // print_r($array_hijo);
                    // print_r($array_hijo[1]);
                    // exit;

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "SELECT * FROM PreguntaEncuestaArbol Where IDEncuestaArbol = '" . $encuesta["IDMovilidad"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->fetchArray($r_encuesta)) :

                        $response_pregunta_padre = array();
                        if (count($array_hijo[$row_pregunta["IDPreguntaEncuestaArbol"]]) > 0) {
                            foreach ($array_hijo[$row_pregunta["IDPreguntaEncuestaArbol"]] as $id_hijo => $datos_hijo) {
                                $pregunta_padre["IDPreguntaMovilidad"] = $datos_hijo["IDEncuestaArbolPregunta"];
                                $pregunta_padre["IDMovilidadOpcionesRespuesta"] = $datos_hijo["IDEncuestaArbolOpcionesRespuesta"];
                                array_push($response_pregunta_padre, $pregunta_padre);
                            }
                        }

                        $pregunta["IDPadrePregunta"] = $response_pregunta_padre;
                        $pregunta["IDPreguntaMovilidad"] = $row_pregunta["IDPreguntaEncuestaArbol"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        //Consulto los valores
                        $sql_opciones = "SELECT * FROM EncuestaArbolOpcionesRespuesta WHERE IDEncuestaArbolPregunta = '" . $row_pregunta["IDPreguntaEncuestaArbol"] . "' order by Orden";
                        $r_opciones = $dbo->query($sql_opciones);
                        $opciones_respuesta = array();
                        $response_valores = array();
                        while ($row_opciones = $dbo->fetchArray($r_opciones)) {
                            $opciones_respuesta["IDPreguntaMovilidad"] = $row_opciones["IDEncuestaArbolPregunta"];
                            $opciones_respuesta["IDMovilidadOpcionesRespuesta"] = $row_opciones["IDEncuestaArbolOpcionesRespuesta"];
                            //$opciones_respuesta[ "IDEncuestaArbolPreguntaSiguiente" ] = $row_opciones[ "IDEncuestaArbolPreguntaSiguiente" ];
                            $opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
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
            // echo '<pre>';
            // print_r($response);
            // die();
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
    public function get_movilidad_historial($IDClub, $IDSocio, $IDUsuario, $IDEncuestaArbol = "", $Fecha = "")
    {

        $dbo = &SIMDB::get();

        $response = array();
        $condicion = "";
        if (!empty($IDSocio)) {
            $condicion .= " AND ER.IDSocio = $IDSocio and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion .= " AND ER.IDUsuario = $IDUsuario and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }
        if (!empty($IDEncuestaArbol)) {
            $condicion .= "AND E.IDEncuestaArbol = " . $IDEncuestaArbol;
        }
        if (!empty($Fecha)) {
            $condicion .= "AND ER.Fecha = '" . $Fecha . "'";
        }

        $sql = "SELECT E.IDEncuestaArbol,E.Nombre, S.IDSocio, U.IDUsuario, CONCAT( S.Nombre, ' ',S.Apellido) AS NombreSocio, U.Nombre AS NombreFuncionario,
					P.IDPreguntaEncuestaArbol,P.EtiquetaCampo,C.Nombre as Categoria, ER.Valor,CONCAT(DATE(ER.FechaTrCr),' ',HOUR(ER.FechaTrCr),':',MINUTE(ER.FechaTrCr)) AS FechaRespuesta ,ER.IDEncuestaArbolOpcionesRespuesta,O.Puntos
					FROM EncuestaArbol E
					JOIN PreguntaEncuestaArbol P ON P.IDEncuestaArbol = E.IDEncuestaArbol
                    LEFT JOIN CategoriaEncuestaArbol C ON P.IDCategoriaEncuestaArbol = C.IDCategoriaEncuestaArbol
					JOIN EncuestaArbolRespuesta ER ON ER.IDPreguntaEncuestaArbol = P.IDPreguntaEncuestaArbol
					LEFT JOIN EncuestaArbolOpcionesRespuesta O ON ER.IDEncuestaArbolOpcionesRespuesta = O.IDEncuestaArbolOpcionesRespuesta
					LEFT JOIN Socio S ON ER.IDSocio = S.IDSocio
					LEFT JOIN Usuario U ON ER.IDSocio = U.IDUsuario
                    WHERE E.IDClub = $IDClub 
                    $condicion
					AND P.Publicar = 'S'
                    -- GROUP BY E.IDEncuestaArbol,ER.IDSocio,FechaRespuesta
					ORDER BY P.NumeroPregunta ASC";

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $arr_encuesta = array();
            $arr_valores = array();
            $PuntosTotal = 0;
            $arr_respuetaTexto = "";

            while ($r = $dbo->assoc($qry)) {
                $mostrar_encuesta = self::verifica_ver_EncuestaArbol($r, $IDSocio, $IDUsuario);

                if ($mostrar_encuesta == 1) {

                    $Puntos = 0;
                    $arr_respuetaTexto = "";

                    $Valores = explode(',', $r['Valor']);
                    if (count($Valores) > 1) {
                        foreach ($Valores as $Respuesta) {
                            $Respuesta = trim($Respuesta);
                            $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "Opcion like '%" . $Respuesta . "%' AND IDEncuestaArbolPregunta = " . $r['IDPreguntaEncuestaArbol'] . " LIMIT 1");
                            $Puntos += $PuntosOpcionRespuesta;
                        }
                    } else {
                        if ($r['IDEncuestaArbolOpcionesRespuesta'] > 0) {
                            $PuntosOpcionRespuesta = $dbo->getFields("EncuestaArbolOpcionesRespuesta", "Puntos", "IDEncuestaArbolOpcionesRespuesta = " . $r['IDEncuestaArbolOpcionesRespuesta']);
                            $Puntos = $PuntosOpcionRespuesta;
                        }
                    }
                    $PuntosPregunta = $dbo->getFields("PreguntaEncuestaArbol", "Puntos", "IDPreguntaEncuestaArbol=" . $r['IDPreguntaEncuestaArbol']);
                    $Puntos += $PuntosPregunta;

                    // $arr_valores[$r['Categoria']]['Puntos'] += $Puntos;
                    // $arr_valores[$r['Categoria']][$r['EtiquetaCampo']] = $r['Valor'];

                    // $PuntosTotal += $arr_valores[$r['Categoria']]['Puntos'];
                    // $arr_respuestas = array(
                    //     'Nombre' => $r['Nombre'],
                    //     'Respuestas' => $arr_valores
                    // );
                    // Pregunta|Respuesta|Puntos
                    $arr_respuetaTexto = $r['EtiquetaCampo'] . "|" . $r['Valor'] . "|" . $Puntos . "&";
                    $arr_encuesta[$r['IDEncuestaArbol']][$r['FechaRespuesta']][$r['Categoria']] .= $arr_respuetaTexto;
                    $arr_valores = array();
                }
            } //end while

            $rows = 0;
            $arr_resultado = array();
            $cont = 0;
            foreach ($arr_encuesta as $IDEncuestaArbol => $encuestas) {
                $rows += count($encuestas);
                foreach ($encuestas as $fecha => $encuesta) {
                    $seccion = '';
                    $PuntajeTotal = 0;
                    foreach ($encuesta as $Categoria => $Respuestas) {
                        $seccion .= '<div class="categoria">';
                        $seccion .= '<span class="tituloCategoria">' . $Categoria . '</span>';
                        $seccion .= '
                            <span class="pregunta fw-600">Pregunta</span>
                            <span class="respuesta fw-600">Respuesta</span>
                            <span class="puntos fw-600">Puntos</span>
                            <br>
                            
                            ';
                        $Respuesta = explode("&", $Respuestas);
                        $PuntajeCategoria = 0;
                        foreach ($Respuesta as $OpcionRespuesta) {
                            $Valores = explode("|", $OpcionRespuesta);
                            // 0=>Pregunta|1=>Respuesta|2=>Puntos
                            $seccion .= '
                            <span class="pregunta">' . $Valores['0'] . '</span>
                            <span class="respuesta">' . $Valores['1'] . '</span>
                            <span class="puntos">' . $Valores['2'] . '</span>
                            <br>
                            
                            ';


                            $PuntajeCategoria += $Valores[2];
                            $PuntajeTotal += $Valores[2];
                        }
                        $seccion .= '<span class="TotalCategoria">' . $PuntajeCategoria . '</span>';

                        $seccion .= '</div>';
                    }
                    $seccion .= '<span class="TotalEncuesta">Puntaje encuesta: ' . $PuntajeTotal . '</span>';

                    $html = '<!DOCTYPE html>
                            <html lang="en">

                            <head>
                                <meta charset="UTF-8">
                                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Document</title>
                                    <style>
                                    * {
                                        padding: 0%;
                                        margin: 0%;
                                        box-sizing: border-box;
                                        font-size: 12px;
                                    }

                                    .container {
                                        width: 100%;
                                        height: auto;
                                        background: #fff;
                                        font-family: Arial, Helvetica, sans-serif;

                                    }

                                    .categoria {
                                        width: 95%;
                                        height: auto;
                                        margin: 8px auto;
                                        padding: 2px 0;
                                        display: flex;
                                        flex-wrap: wrap;
                                        flex-direction: row;
                                        justify-content: center;
                                        gap: 1px;
                                        align-items: center;
                                        background-color: #9ed0ff;
                                        border-radius: 1rem;
                                        color: #000;
                                    }

                                    .tituloCategoria {
                                        display: block;
                                        width: 100%;
                                        background: transparent;
                                        padding: 4px 0;
                                        margin: auto;
                                        text-align: center;
                                        font-weight: 600;
                                    }

                                    .pregunta,
                                    .respuesta {
                                        display: block;
                                        width: 20%;
                                        height: auto;
                                        padding: 2px 0;
                                        text-align: left;

                                    }

                                    .pregunta {
                                        width: 60%;
                                        font-weight: 600;
                                    }

                                    .puntos {
                                        display: block;
                                        width: 10%;
                                        height: auto;
                                        padding: 4px 0;
                                    }

                                    .puntos {
                                        text-align: center;

                                    }

                                    .TotalCategoria {
                                        display: block;
                                        width: 50%;
                                        height: auto;
                                        padding: 5px 0;
                                        margin: 2px;
                                        border-radius: 1rem;
                                        background-color: rgb(246, 246, 246);
                                        text-align: center;
                                        color: rgb(0, 0, 0);

                                    }

                                    .TotalEncuesta {
                                        display: block;
                                        width: 90%;
                                        height: auto;
                                        padding: 5px 0;
                                        margin: 2px auto;
                                        border-radius: 1rem;
                                        background-color: blue;
                                        text-align: center;
                                        color: #fff;

                                    }
                                    .fw-600{
                                        font-weight: 600;
                                    }
                                </style>
                            </head>

                            <body>
                                <div class="container">
                                    ' . $seccion . '
                                </div>
                            </body>

                            </html>';
                    $fechaRespuesta = date('Y-m-d', strtotime($fecha));
                    $HoraRespuesta = date('H:i', strtotime($fecha));
                    $arr_resultado = array(
                        "IDMovilidad" => $cont,
                        "Fecha" => $fechaRespuesta,
                        "Hora" => $HoraRespuesta . ":00",
                        "Texto" => $dbo->getFields('EncuestaArbol', 'Nombre', "IDEncuestaArbol=$IDEncuestaArbol"),
                        "Descripcion" => $html,
                    );
                    array_push($response, $arr_resultado);
                    $cont++;
                }
            }
            // $response = json_encode($arr_resultado);
            // echo '<pre>';
            // print_r($response);
            // die();
            $message = "$rows Encontrados";
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

    public function set_respuesta_movilidad($IDClub, $IDSocio, $IDEncuestaArbol, $Respuestas, $IDUsuario = "", $NumeroDocumento = "", $Nombre = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario) || !empty($NumeroDocumento)) && !empty($IDEncuestaArbol)) {
            $guardar_encuesta = 0;
            $datos_EncuestaArbol = $dbo->fetchAll("EncuestaArbol", " IDEncuestaArbol = '" . $IDEncuestaArbol . "' and IDClub='" . $IDClub . "' ", "array");
            $contesta_una = $datos_EncuestaArbol["UnaporSocio"];
            if ($contesta_una == "S") {
                $sql_resp = "SELECT IDEncuestaArbol From EncuestaArbolRespuesta Where IDSocio = '" . $IDSocio . "' and IDEncuestaArbol = '" . $IDEncuestaArbol . "' Limit 1";
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
                $condicion_unica = " and IDUsuario='" . $IDUsuario . "'";
            } elseif (!empty($IDSocio)) {
                $TipoUsuario = "Socio";
                $condicion_unica = " and IDSocio='" . $IDSocio . "'";
            } else {
                $TipoUsuario = "Externo";
                $condicion_unica = " and NumeroDocumento='" . $NumeroDocumento . "'";
            }
            // Validar si se pude responder mas de una vez al dia
            $contestaUnaPorDia = $datos_EncuestaArbol["UnaPorDia"];
            if ($contestaUnaPorDia == "S") {
                $fecha_hoy = date("Y-m-d") . " 00:00:00";
                $sql_unica = "SELECT IDEncuestaArbolRespuesta FROM  EncuestaArbolRespuesta WHERE IDEncuestaArbol = '" . $IDEncuestaArbol . "' and FechaTrCr >= '" . $fecha_hoy . "' " . $condicion_unica;
                $r_unica = $dbo->query($sql_unica);
                $total_unica = $dbo->rows($r_unica);
                if ($total_unica > 0) {
                    $respuesta["message"] = "Ya había registrado los datos el día de hoy";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }

            if ($guardar_encuesta == 1) {
                $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                $datos_respuesta = json_decode($Respuestas, true);
                if (count($datos_respuesta) > 0) :
                    foreach ($datos_respuesta as $detalle_respuesta) :
                        if ($detalle_respuesta["Valor"] != "null") {
                            $sql_datos_form = $dbo->query("INSERT INTO EncuestaArbolRespuesta (IDEncuestaArbol, IDSocio, IDUsuario, IDPreguntaEncuestaArbol, IDEncuestaArbolOpcionesRespuesta, NumeroDocumento, Nombre, TipoUsuario, Valor, FechaTrCr) Values ('" . $IDEncuestaArbol . "','" . $IDSocio . "','" . $IDUsuario . "','" . $detalle_respuesta["IDPreguntaMovilidad"] . "','" . $detalle_respuesta["ValorID"] . "','" . $NumeroDocumento . "','" . $Nombre . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())");
                            $suma_peso += $detalle_respuesta["Peso"];
                            $datos_pregunta = $dbo->fetchAll("PreguntaEncuestaArbol", " IDPreguntaEncuestaArbol = '" . $detalle_respuesta["IDPreguntaMovilidad"] . "' ", "array");
                            $respuestas_EncuestaArbol .= $datos_pregunta["EtiquetaCampo"] . " = " . $detalle_respuesta["Valor"] . "<br>";
                        }
                    endforeach;
                endif;

                $RespuestaEncuestaArbol = "Guardado con exito";

                if ($IDClub == 138)
                    SIMWebServiceEncuestaArbol::enviar_correo($respuestas_EncuestaArbol, $IDEncuestaArbol, $IDClub);

                $respuesta["message"] = $RespuestaEncuestaArbol;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "Ya había contestado, solo se permite 1 vez por día";
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

    public function verifica_ver_EncuestaArbol($r, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $mostrar_encuesta = 1;
        //Verifico si la encuesta esta configurada solo para un grupo de socios o socios especificos
        if (($r["DirigidoA"] == 'S' || $r["DirigidoA"] == 'T') && ($r["DirigidoAGeneral"] == "SE" || $r["DirigidoAGeneral"] == "GS")) {
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
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "GS") {
                $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio = '" . $r["IDGrupoSocio"] . "'");
                $array_invitados = explode("|||", $SociosGrupo);
                if (count($array_invitados) > 0) {
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        $array_socios_encuesta[] = $datos_invitado;
                    }
                }

                if (in_array($IDSocio, $array_socios_encuesta)) {
                    $mostrar_encuesta = 1;
                } else {
                    $mostrar_encuesta = 0;
                }
            } elseif ($r["DirigidoAGeneral"] == "E") {
                $mostrar_encuesta = 1;
                //$mostrar_encuesta=0;

            }
        }

        return $mostrar_encuesta;
    }

    public function enviar_correo($Respuestas, $IDEncuestaArbol, $IDClub)
    {

        $dbo = &SIMDB::get();

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
        $datos_EncuestaArbol = $dbo->fetchAll("EncuestaArbol", " IDEncuestaArbol = '" . $IDEncuestaArbol . "' and IDClub='" . $IDClub . "' ", "array");

        $correo = $datos_EncuestaArbol[EmailAlerta];

        if (!empty($correo)) :
            $Mensaje = "Hay una nueva respuesta para $datos_EncuestaArbol[Nombre].<br><br>Respuestas:<br> $Respuestas";

            $msg = "<br>Cordial Saludo,<br><br>
			<br><br>" . $Mensaje . "<br><br>

			Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
			Cordialmente<br><br>
			<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

            $mensaje = "
					<body>
						<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
							<tr>
								<td>
									<img src='" . CLUB_ROOT . $datos_club[FotoLogoApp] . "'>
								</td>
							</tr>
							<tr>
								<td>" .
                $msg
                . "</td>
							</tr>
						</table>
					</body>
			";
            /* //correos a donde se notifica
            $correo = $datos_dotacion['EmailAlerta']; */

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                    }
                }
            }
            $AsuntoMensaje = "Respuesta $datos_EncuestaArbol[Nombre]";
            $subject = "=?UTF-8?B?" . base64_encode($AsuntoMensaje) . "=?=";

            $mail->Subject = $subject;
            $mail->Body = $mensaje;
            $mail->IsHTML(true);
            $mail->Sender = $datos_club["CorreoRemitente"];
            $mail->Timeout = 120;
            //$mail->IsSMTP();
            $mail->Port = PUERTO_SMTP;
            $mail->SMTPAuth = true;
            $mail->Host = HOST_SMTP;
            //$mail->Mailer = 'smtp';
            $mail->Password = PASSWORD_SMPT;
            $mail->Username = USER_SMTP;
            $mail->From = $datos_club["CorreoRemitente"];
            $mail->FromName = "Club";
            $mail->CharSet = 'UTF-8';
            $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
            $confirm = $mail->Send();
        endif;
    }
} //end class
