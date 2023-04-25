<?php

class SIMWebServiceDotacion
{
    public function get_dotacion($IDClub, $IDSocio = "", $IDUsuario = "")
    {

        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio)) {
            $condicion = " and (DirigidoA = 'S' or DirigidoA = 'T') ";
        } elseif (!empty($IDUsuario)) {
            $condicion = " and (DirigidoA = 'E' or DirigidoA = 'T') ";
        }

        $sql = "SELECT * FROM Dotacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion . " ORDER BY Orden ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {

                $mostrar_encuesta = SIMWebServiceApp::verifica_ver_dotacion($r, $IDSocio);

                if ($mostrar_encuesta == 1) {
                    $encuesta["IDClub"] = $r["IDClub"];
                    $encuesta["IDDotacion"] = $r["IDDotacion"];
                    $encuesta["Nombre"] = $r["Nombre"];
                    $encuesta["Descripcion"] = $r["Descripcion"];
                    $encuesta["SolicitarAbrirApp"] = $r["SolicitarAbrirApp"];
                    $encuesta["FechaInicio"] = $r["FechaInicio"];
                    $encuesta["FechaFin"] = $r["FechaFin"];
                    $encuesta["SegundaClave"] = $r["SegundaClave"];

                    if (!empty($r["ImagenDotacion"])) :
                        $foto = BANNERAPP_ROOT . $r["ImagenDotacion"];
                    else :
                        $datos_modulo = $dbo->fetchAll("ClubModulo", " IDModulo = '102' and IDClub='" . $IDClub . "' ", "array");
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
                        $sql_contesta = "SELECT * FROM DotacionRespuesta WHERE IDSocio='" . $IDSocio . "' and IDEncuesta = '" . $r["IDDotacion"] . "'";
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
                    $sql_respuesta = "Select * From PreguntaDotacion Where IDDotacion = '" . $encuesta["IDDotacion"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :
                        $pregunta["IDPreguntaDotacion"] = $row_pregunta["IDPreguntaDotacion"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        $pregunta["Valores"] = $row_pregunta["Valores"];
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

    public function set_respuesta_dotacion($IDClub, $IDSocio, $IDDotacion, $Respuestas, $IDUsuario = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDDotacion)) {
            $guardar_encuesta = 0;
            $contesta_una = utf8_decode($dbo->getFields("Dotacion", "UnaporSocio", "IDDotacion = '" . $IDDotacion . "'"));
            if ($contesta_una == "S") {
                $sql_resp = "Select IDDotacion From DotacionRespuesta Where IDSocio = '" . $IDSocio . "' and IDDotacion = '" . $IDDotacion . "' Limit 1";
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
                $respuestas_dotacion = "";
                $datos_respuesta = json_decode($Respuestas, true);
                if (count($datos_respuesta) > 0) :
                    foreach ($datos_respuesta as $detalle_respuesta) :

                        $pregunta = $dbo->getFields("PreguntaDotacion", "EtiquetaCampo", "IDPreguntaDotacion = '" . $detalle_respuesta["IDPreguntaDotacion"] . "'");

                        $respuestas_dotacion .= $pregunta . " = " . $detalle_respuesta["Valor"] . "<br>";
                        $sql_datos_form = $dbo->query("Insert Into DotacionRespuesta (IDDotacion, IDSocio, IDPreguntaDotacion, TipoUsuario, Valor, FechaTrCr) Values ('" . $IDDotacion . "','" . $IDSocio . "','" . $detalle_respuesta["IDPreguntaDotacion"] . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "',NOW())");
                    endforeach;
                endif;

                $correos = $dbo->getFields("Dotacion", "EmailAlerta", "IDDotacion = '" . $IDDotacion . "'");
                if (!empty($correos)) {
                    SIMUtil::enviar_notificacion_dotacion($IDClub, $TipoUsuario, $IDSocio, $IDDotacion, $respuestas_dotacion, $correos);
                }

                $respuesta["message"] = "guardado";
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
}
