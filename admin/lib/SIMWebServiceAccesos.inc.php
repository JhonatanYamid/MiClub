<?php

use SIMWebServiceAccesos as GlobalSIMWebServiceAccesos;

class SIMWebServiceAccesos
{

    public function get_configuracion_invitadosv1($IDClub)
    {
        require_once LIBDIR . "SIMWebServicePublicidad.inc.php";

        $dbo = &SIMDB::get();
        $response = array();

        //Campos Formulario
        $response_campo_formulario = array();
        $sql_campo_form = "SELECT * FROM CampoFormularioInvitado WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
        $qry_campo_form = $dbo->query($sql_campo_form);
        if ($dbo->rows($qry_campo_form) > 0) {
            while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                $campoformulario["IDCampoFormularioInvitado"] = $r_campo["IDCampoFormularioInvitado"];
                $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                $campoformulario["Valores"] = $r_campo["Valores"];
                if ($r_campo["TipoCampo"] == "imagen") {
                    $campoformulario["ParametroEnvioPost"] = $r_campo["IDCampoFormularioInvitado"];
                } elseif ($r_campo["TipoCampo"] == "imagenarchivo") {
                    $campoformulario["ParametroEnvioPost"] = $r_campo["IDCampoFormularioInvitado"];
                }
                array_push($response_campo_formulario, $campoformulario);
            } //end while
        }

        $sqlParametros = "SELECT PermiteCargarExcelInvitado, LabelBotonEjemploFormato,LabelNombreInvitado,LabelNumeroIdentificacion,TextoBotonAgregar,HintFechaInvitado,TextoInvitadoAnterior,TextoMisInvitados FROM ParametroAcceso WHERE IDClub = $IDClub";
        $qryParametros = $dbo->query($sqlParametros);
        $Datos = $dbo->fetchArray($qryParametros);

        // Parametros campos modulo invitados
        $configuracion[PermiteCargarExcel] = $Datos[PermiteCargarExcelInvitado];
        $configuracion[LabelBotonEjemploFormato] = $Datos[LabelBotonEjemploFormato];
        $configuracion[LabelNombreInvitado] = $Datos[LabelNombreInvitado];
        $configuracion[LabelNumeroIdentificacion] = $Datos[LabelNumeroIdentificacion];
        $configuracion[UrlEjemploFormato] = URLROOT . "formatoinvitadosV1.php";
        $configuracion["TextoMisInvitado"] = $Datos["TextoMisInvitados"];
        $configuracion["TextoInvitadoAnterior"] = $Datos["TextoInvitadoAnterior"];
        $configuracion["HintFechaInvitado"] = $Datos["HintFechaInvitado"];
        $configuracion["TextoBotonAgregar"] = $Datos["TextoBotonAgregar"];
        // Fin Parametros campos modulo invitados

        $resp_publicidad = SIMWebServicePublicidad::get_publicidad($IDClub, "1", "", "Socio", "");
        $configuracion["CamposFormulario"] = $response_campo_formulario;
        $configuracion["PublicidadInfo"] = $resp_publicidad["response"][0];


        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $configuracion;

        return $respuesta;
    }

    public function get_parametro_acceso($IDClub, $IDSocio = "")
    {
        require_once LIBDIR . "SIMWebServicePublicidad.inc.php";

        $dbo = &SIMDB::get();
        $response = array();
        $sql = "SELECT * FROM ParametroAcceso WHERE IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($r["IconoFamiliar"])) {
                    $foto_familiar = CLUB_ROOT . $r["IconoFamiliar"];
                }
                if (!empty($r["IconoIndividual"])) {
                    $foto_individual = CLUB_ROOT . $r["IconoIndividual"];
                }

                //Tipo Invitado
                $FiltrarTipoInvitado = $dbo->getFields('ConfiguracionClub', 'FiltrarTipoInvitado', "IDClub = $IDClub");
                $arr_omitir_tipo_invitado = array();
                if ($FiltrarTipoInvitado == 'S') {
                    $TipoSocio = $dbo->getFields('Socio', 'TipoSocio', "IDSocio = $IDSocio");
                    // Consultamos los TipoSocio asociados al TipoInvitado
                    $TipoSocioPorTipoInvitado = $dbo->fetchAll('TipoInvitado', "IDClub = $IDClub", "array");
                    foreach ($TipoSocioPorTipoInvitado as $TipoInvitado) {
                        $arr_TipoSocio = explode("|", $TipoInvitado['TipoSocio']);
                        if (!in_array($TipoSocio, $arr_TipoSocio)) {
                            array_push($arr_omitir_tipo_invitado, $TipoInvitado['Nombre']);
                        }
                    }
                }
                $response_tipo_invitado = array();
                $array_tipo_invitado = explode("|", $r["TipoInvitado"]);
                if (count($array_tipo_invitado) > 0) :
                    foreach ($array_tipo_invitado as $nombre_tipo) :
                        if (!in_array($nombre_tipo, $arr_omitir_tipo_invitado)) {
                            $dato_tipo_invitado[] = $nombre_tipo;
                            array_push($response_tipo_invitado, $nombre_tipo);
                        }
                    endforeach;
                endif;

                //Tipo Autorizacion
                $response_tipo_autorizacion = array();
                $array_tipo_autorizacion = explode("|", $r["TipoAutorizacion"]);
                if (count($array_tipo_autorizacion) > 0) :
                    foreach ($array_tipo_autorizacion as $nombre_tipo) :
                        $dato_tipo_autorizacion[] = $nombre_tipo;
                        array_push($response_tipo_autorizacion, $nombre_tipo);
                    endforeach;
                endif;

                //Tipo Documentos
                $response_tipodoc = array();
                $sql_tipodoc = "SELECT * FROM TipoDocumento WHERE Publicar = 'S' ORDER BY Nombre";
                $qry_tipodoc = $dbo->query($sql_tipodoc);
                if ($dbo->rows($qry_tipodoc) > 0) {
                    while ($r_tipodoc = $dbo->fetchArray($qry_tipodoc)) {
                        $tipodoc["IDTipoDocumento"] = (int) $r_tipodoc["IDTipoDocumento"];
                        $tipodoc["Nombre"] = $r_tipodoc["Nombre"];
                        array_push($response_tipodoc, $tipodoc);
                    } //ednw hile
                }

                // Georeferenciacion
                $response_georef = array();
                $georef["Georeferenciacion"] = $r["Georeferenciacion"];
                $georef["Latitud"] = $r["Latitud"];
                $georef["Longitud"] = $r["Longitud"];
                $georef["Rango"] = $r["Rango"];
                $georef["MensajeFueraRango"] = $r["MensajeFueraRango"];
                //array_push($response_georef, $georef);

                // Georeferenciacion
                $response_georef_contratista = array();
                $georef_contratista["Georeferenciacion"] = $r["GeoreferenciacionContratista"];
                $georef_contratista["Latitud"] = $r["LatitudContratista"];
                $georef_contratista["Longitud"] = $r["LongitudContratista"];
                $georef_contratista["Rango"] = $r["Rango"];
                $georef_contratista["MensajeFueraRango"] = $r["MensajeFueraRangoContratista"];
                //array_push($response_georef, $georef);

                $datos_acceso["IDClub"] = $r["IDClub"];
                $datos_acceso["GrupoFamiliar"] = $r["GrupoFamiliar"];
                $datos_acceso["IconoFamiliar"] = $foto_familiar;
                $datos_acceso["NombreFamiliar"] = $r["NombreFamiliar"];
                $datos_acceso["Invitado"] = $r["Invitado"];
                $datos_acceso["IconoIndividual"] = $foto_individual;
                $datos_acceso["NombreIndividual"] = $r["NombreIndividual"];
                $datos_acceso["TipoInvitado"] = $response_tipo_invitado;
                $datos_acceso["TipoAutorizacion"] = $response_tipo_autorizacion;
                $datos_acceso["TipoDocumento"] = $response_tipodoc;
                $datos_acceso["TextoMenorEdad"] = $r["TextoMenorEdad"];
                $datos_acceso["GeoreferenciacionInvitado"] = $georef;
                $datos_acceso["GeoreferenciacionContratista"] = $georef_contratista;
                $datos_acceso["MostrarTerminosContratista"] = $r["MostrarTerminosContratista"];
                $datos_acceso["TerminosHtmlContratista"] = $r["TerminosHtmlContratista"];
                $datos_acceso["LabelTerminosContratista"] = $r["LabelTerminosContratista"];
                $datos_acceso["MostrarHoraInicioFinContratista"] = $r["MostrarHoraInicioFinContratista"];

                $datos_acceso["PermiteCargarExcelInvitado"] = $r["PermiteCargarExcelInvitado"];
                $datos_acceso["TextoBotonCargarExcelInvitado"] = $r["TextoBotonCargarExcelInvitado"];
                $datos_acceso["PermiteCargarExcelContratista"] = $r["PermiteCargarExcelContratista"];
                $datos_acceso["TextoBotonCargarExcelContratista"] = $r["TextoBotonCargarExcelContratista"];

                $datos_acceso["PermiteInvitacionIndividualRangoFechas"] = $r["PermiteInvitacionIndividualRangoFechas"];
                $datos_acceso["PermiteInvitacionContratistaRangoFechas"] = $r["PermiteInvitacionContratistaRangoFechas"];
                $datos_acceso["LabelSeleccionDiasSemana"] = $r["LabelSeleccionDiasSemana"];
                $datos_acceso["LabelDiasSeleccionados"] = $r["LabelDiasSeleccionados"];

                $datos_acceso["UrlEjemploFormatoContratista"] = URLROOT . "formatocontratistas.php";
                $datos_acceso["UrlEjemploFormato"] = URLROOT . "formatoinvitados.php";

                $datos_acceso["LabelBotonEjemploFormato"] = $r["LabelBotonEjemploFormato"];

                $resp_publicidad = SIMWebServicePublicidad::get_publicidad($IDClub, "25", "", "Socio", "");
                $datos_acceso["PublicidadInfoIndividual"] = $resp_publicidad["response"][0];
                $resp_publicidad = SIMWebServicePublicidad::get_publicidad($IDClub, "26", "", "Socio", "");
                $datos_acceso["PublicidadInfoContratista"] = $resp_publicidad["response"][0];



                array_push($response, $datos_acceso);
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehaencontradoclub', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_parametros_empleados($IDClub)
    {
        require_once LIBDIR . "SIMWebServiceClub.inc.php";

        $dbo = &SIMDB::get();
        $response = array();
        $sql = "SELECT * FROM ParametroAcceso WHERE IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($r["IconoFamiliar"])) {
                    $foto_familiar = CLUB_ROOT . $r["IconoFamiliar"];
                }
                if (!empty($r["IconoIndividual"])) {
                    $foto_individual = CLUB_ROOT . $r["IconoIndividual"];
                }

                //Tipo Invitado
                $response_tipo_invitado = array();
                $array_tipo_invitado = explode("|", $r["TipoInvitado"]);
                if (count($array_tipo_invitado) > 0) :
                    foreach ($array_tipo_invitado as $nombre_tipo) :
                        $dato_tipo_invitado[] = $nombre_tipo;
                        array_push($response_tipo_invitado, $nombre_tipo);
                    endforeach;
                endif;

                //Tipo Autorizacion
                $response_tipo_autorizacion = array();
                $array_tipo_autorizacion = explode("|", $r["TipoAutorizacion"]);
                if (count($array_tipo_autorizacion) > 0) :
                    foreach ($array_tipo_autorizacion as $nombre_tipo) :
                        $dato_tipo_autorizacion[] = $nombre_tipo;
                        array_push($response_tipo_autorizacion, $nombre_tipo);
                    endforeach;
                endif;

                //Tipo Documentos
                $response_tipodoc = array();
                $sql_tipodoc = "SELECT * FROM TipoDocumento WHERE Publicar = 'S' ORDER BY Nombre";
                $qry_tipodoc = $dbo->query($sql_tipodoc);
                if ($dbo->rows($qry_tipodoc) > 0) {
                    while ($r_tipodoc = $dbo->fetchArray($qry_tipodoc)) {
                        $tipodoc["IDTipoDocumento"] = (int) $r_tipodoc["IDTipoDocumento"];
                        $tipodoc["Nombre"] = $r_tipodoc["Nombre"];
                        array_push($response_tipodoc, $tipodoc);
                    } //ednw hile
                }

                //Consulto el icono de contratistas
                //Modulos Sistema Menu Central
                $response_modulo = array();
                $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and Activo = 'S' and Ubicacion like '%Central%' and IDModulo = 26 ORDER BY Orden";
                $qry_modulo = $dbo->query($sql_modulo);
                if ($dbo->rows($qry_modulo) > 0) {
                    while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                        // Verificar si el modulo tiene contenido para mostrar
                        $flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $IDClub);
                        //$flag_mostrar=0;
                        if ($flag_mostrar == 0) :
                            if (!empty($r_modulo["Titulo"])) {
                                $modulo["NombreModulo"] = trim($r_modulo["Titulo"]);
                            } else {
                                $modulo["NombreModulo"] = trim($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                            }

                            $modulo["Orden"] = $r_modulo["Orden"];
                            $icono_modulo = $r_modulo["Icono"];
                            if (!empty($r_modulo["Icono"])) :
                                $foto_modulo = MODULO_ROOT . $r_modulo["Icono"];
                            else :
                                $foto_modulo = "";
                            endif;
                            $icono_contratista = $foto_modulo;
                        endif;
                    } //ednw while
                }

                $datos_acceso["IDClub"] = $r["IDClub"];
                $datos_acceso["GrupoFamiliar"] = $r["GrupoFamiliar"];
                $datos_acceso["IconoFamiliar"] = $foto_familiar;
                $datos_acceso["NombreFamiliar"] = $r["NombreFamiliar"];
                $datos_acceso["Invitado"] = $r["Invitado"];
                $datos_acceso["IconoIndividual"] = $foto_individual;
                $datos_acceso["NombreIndividual"] = $r["NombreIndividual"];
                $datos_acceso["TipoInvitado"] = $response_tipo_invitado;
                $datos_acceso["IconoContratista"] = $icono_contratista;
                $datos_acceso["NombreContratista"] = $modulo["NombreModulo"];
                $datos_acceso["TipoAutorizacion"] = $response_tipo_autorizacion;
                $datos_acceso["TipoDocumento"] = $response_tipodoc;
                $datos_acceso["TextoMenorEdad"] = $r["TextoMenorEdad"];

                $datos_acceso["PermiteCargarExcelInvitado"] = $r["PermiteCargarExcelInvitado"];
                $datos_acceso["TextoBotonCargarExcelInvitado"] = $r["TextoBotonCargarExcelInvitado"];
                $datos_acceso["PermiteCargarExcelContratista"] = $r["PermiteCargarExcelContratista"];
                $datos_acceso["TextoBotonCargarExcelContratista"] = $r["TextoBotonCargarExcelContratista"];

                array_push($response, $datos_acceso);
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se ha encontrado club";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_configuracion_autorizaciones($IDClub)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM ConfiguracionAccesos  WHERE IDClub = '" . $IDClub . "' and Activo = 'S' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $r["IDClub"];
                $configuracion["IntroduccionBuscador"] = $r["IntroduccionBuscador"];
                $configuracion["LabelBuscador"] = $r["LabelBuscador"];
                $configuracion["PintarEstadoSalidaEntrada"] = $r["PintarEstadoSalidaEntrada"];
                $configuracion["PermiteRegistrarSinEstado"] = $r["PermiteRegistrarSinEstado"];
                $configuracion["TipoBoton"] = $r["TipoBoton"];
                $configuracion["LabelRegistroObjeto"] = $r["LabelRegistroObjeto"];
                $configuracion["LabelCampo1RegistroObjeto"] = $r["LabelCampo1RegistroObjeto"];
                $configuracion["LabelCampo2RegistroObjeto"] = $r["LabelCampo2RegistroObjeto"];
                $configuracion["PermiteRegistroObjetos"] = $r["PermiteRegistroObjetos"];
                $configuracion["PermiteInvitacionPortero"] = $r["PermiteInvitacionPortero"];

                //Preguntas Generales Dinamicas tipo arbol
                $configuracion["PreguntasGenerales"] = SIMWebServiceAccesos::get_encuesta_acceso($IDClub);

                //Preguntas invitado x invitado
                $configuracion["PreguntasInvitado"] = SIMWebServiceAccesos::get_campo_acceso($IDClub);

                //TipoObjetos
                $configuracion["TipoObjetos"] = $r["PreguntasGenerales"];
                $response_tipo_obj = array();
                $sql_tipo_obj = "SELECT * From TipoObjeto Where Publicar = 'S'";
                $result_tipo_obj = $dbo->query($sql_tipo_obj);
                while ($row_tipo_obj = $dbo->fetchArray($result_tipo_obj)) :
                    $array_tipo_obj["IDTipo"] = $row_tipo_obj["IDTipoObjeto"];
                    $array_tipo_obj["Nombre"] = $row_tipo_obj["Nombre"];
                    array_push($response_tipo_obj, $array_tipo_obj);
                endwhile;
                $configuracion["TipoObjetos"] = $response_tipo_obj;
            }

            array_push($response, $configuracion);
        } //ednw hile
        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;
        return $respuesta;
    } //End if

    public function get_invitado_documento($IDClub, $Documento, $Dispositivo = "")
    {
        require_once LIBDIR . "SIMWebServiceReservas.inc.php";
        $dbo = &SIMDB::get();

        if (!empty($Documento)) {

            // Configuracion Club
            $q_ConfiguracionClub = $dbo->query("SELECT CampoObservacionGeneral,LimitarIngresosPorFechaFinalContrato FROM ConfiguracionClub WHERE IDClub = $IDClub");
            $ConfiguracionClub = $dbo->assoc($q_ConfiguracionClub);
            $CampoObservacionGeneral = $ConfiguracionClub['CampoObservacionGeneral'];
            // Fin Configuracion Club

            // Validación salto de linea para Android o IOS
            $SaltoLinea = ($Dispositivo == 'Android') ? "<br>" : "\r\n";
            //Fin Validación salto de linea para Android o IOS

            // Si es el club El Rincon se captura el numero de documento de la cadena que llega
            if ($IDClub == 10) {
                $arr_qryString = explode(" ", $Documento);
                $qryString = $arr_qryString[0];
            }
            //Fin Si es el club El Rincon se captura el numero de documento de la cadena que llega

            // Si es el club Exportiva se captura el numero de documento de la cadena que llega
            if ($IDClub == 136) {
                $arr_qryString = explode("|", $Documento);
                $qryString = $arr_qryString[1];
            }
            //Fin Si es el club El Xportiva se captura el numero de documento de la cadena que llega

            $autorizacion_recogida = 0;
            $autorizacion_invitacion = 0;
            $dia = date("w", strtotime(date("Y-m-d")));

            //BUSQUEDA INVITADOS ACCESOS
            $qryString = str_replace(".", "", $Documento);
            $qryString = str_replace(",", "", $qryString);
            $qryString = str_replace("-", "", $qryString);

            if (ctype_digit($qryString)) {
                // si es solo numeros en un numero de documento
                $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I WHERE (SIE.Dias LIKE '%$dia%' OR SIE.Dias = '') AND SIE.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='" . $IDClub . "'";
                $modo_busqueda = "DOCUMENTO";
            }

            $result_invitacion = $dbo->query($sql_invitacion);
            $total_resultados = $dbo->rows($result_invitacion);

            if ($total_resultados > 0) {
                $autorizacion_invitacion = 1;
            }

            $datos_invitacion = $dbo->fetchArray($result_invitacion);
            $datos_invitacion["TipoInvitacion"] = "InvitadoAcceso";
            $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitadoEspecial"];
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
            $tipo_socio = $datos_socio["TipoSocio"];
            $datos_socio["TipoSocio"] = "Invitado por";

            $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");

            $datos_invitacion["ValoresCampo"] = $response_campos;
            $Observaciones = "";
            // Consulto las preguntas dinamicas
            if ($total_resultados > 0 && isset($datos_invitacion['IDSocioInvitadoEspecial'])) {
                $sql_CampoFormularioInvitado = "SELECT IDCampoFormularioInvitado,EtiquetaCampo FROM CampoFormularioInvitado WHERE IDClub = $IDClub AND Publicar = 'S' ORDER BY Orden ASC";
                $q_CampoFormularioInvitado = $dbo->query($sql_CampoFormularioInvitado);
                $total_preguntas = $dbo->rows($q_CampoFormularioInvitado);
                if ($total_preguntas > 0) {
                    while ($row_preguntas_invitado = $dbo->fetchArray($q_CampoFormularioInvitado)) {
                        $sql_RespuestasInvitado = "SELECT Valor FROM SocioInvitadoEspecialOtrosDatos WHERE IDSocioInvitadoEspecial =" . $datos_invitacion['IDInvitacion'] . " AND IDCampoFormularioInvitado = " . $row_preguntas_invitado['IDCampoFormularioInvitado'];
                        $q_RespuestasInvitado = $dbo->query($sql_RespuestasInvitado);
                        $total_RespuestasInvitado = $dbo->rows($q_RespuestasInvitado);
                        if ($total_RespuestasInvitado > 0) {
                            $Respuesta_Inivitado = $dbo->assoc($q_RespuestasInvitado);
                            $Observaciones .= $SaltoLinea . $row_preguntas_invitado['EtiquetaCampo'] . ": " . $Respuesta_Inivitado['Valor'];
                        }
                    }
                }
            }
            // Fin Consulto las preguntas dinamicas

            if ($datos_invitacion["Ingreso"] == "N") {
                $accion_acceso = "ingreso";
                $label_accion_acceso = "Ingres&oacute;";
            } elseif ($datos_invitacion["Salida"] == "N") {
                $accion_acceso = "salio";
                $label_accion_acceso = "Sali&oacute;";
            }
            //Consulto grupo Familiar
            if ($datos_invitacion["CabezaInvitacion"] == "S") :
                $sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '" . $datos_invitacion["IDSocioInvitadoEspecial"] . "'";
                $result_grupo = $dbo->query($sql_grupo);
            endif;

            // PARA BUSCAR TODAS LAS INVITACIONES DE ESE MISMO INVITADO EN LAS FECHAS
            $IDInvitado = $datos_invitacion["IDInvitado"];

            // CON EL IDINVITADO BUSCAMOS TODAS LAS QUE ESTAN PARA ESE MISMO DIA SI HAY MAS DE UNA
            $LugaresInvitado = array();
            $SQLInvitaciones = "SELECT * FROM SocioInvitadoEspecial WHERE IDClub = $IDClub AND IDInvitado = $IDInvitado AND FechaInicio <= CURDATE() AND FechaFin >= CURDATE()";
            $QRYInvitaciones = $dbo->query($SQLInvitaciones);

            while ($Datos = $dbo->fetchArray($QRYInvitaciones)) :
                $InfoLugar[IDLugar] = $Datos[IDSocioInvitadoEspecial];

                $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $Datos[IDSocio]");

                if ($IDClub == 127) :
                    $Nombre = "Invitado por: $DatosSocio[Nombre] $DatosSocio[Apellido] \n Apartamento: $DatosSocio[Predio] \n Fechas Autorizado: $Datos[FechaInicio] hasta $Datos[FechaFin]";
                elseif ($IDClub == 18) :
                    $Nombre = "Invitado por: $DatosSocio[Nombre] $DatosSocio[Apellido] \n Se dirige a: $DatosSocio[Predio] \n Numero Documento: $datos_invitado[NumeroDocumento]";
                else :
                    $Nombre = "Invitado por: $DatosSocio[Nombre] $DatosSocio[Apellido]";
                endif;

                $InfoLugar[Nombre] = $Nombre;
                // BUSCAMOS LOS ULTIMO REGISTROS DE ACCESO
                $SQLAcceso = "SELECT * FROM LogAccesoDiario WHERE IDInvitacion = $Datos[IDSocioInvitadoEspecial] ORDER BY IDLogAcceso DESC LIMIT 1";
                $QRYAcceso = $dbo->query($SQLAcceso);
                $InfoAcceso = $dbo->fetchArray($QRYAcceso);

                if (empty($InfoAcceso[Tipo]))
                    $InfoAcceso[Tipo] = "";

                if (empty($InfoAcceso[Salida]))
                    $InfoAcceso[Salida] = "";

                if (empty($InfoAcceso[FechaSalida]))
                    $InfoAcceso[FechaSalida] = "";

                if (empty($InfoAcceso[Entrada]))
                    $InfoAcceso[Entrada] = "";

                if (empty($InfoAcceso[FechaIngreso]))
                    $InfoAcceso[FechaIngreso] = "";

                $UltimoRegistro[Tipo] = $InfoAcceso[Tipo];
                $UltimoRegistro[Salida] = $InfoAcceso[Salida];
                $UltimoRegistro[FechaSalida] = $InfoAcceso[FechaSalida];
                $UltimoRegistro[Entrada] = $InfoAcceso[Entrada];
                $UltimoRegistro[FechaIngreso] = $InfoAcceso[FechaIngreso];
                $UltimoRegistro[OtrosDatos] = "";
                $UltimoRegistro[LimiteSuperado] = "";

                $InfoLugar[UltimoRegistro] = $UltimoRegistro;

                array_push($LugaresInvitado, $InfoLugar);
            endwhile;


            //FIN BUSQUEDA INVITADOS ACCESOS

            //BUSQUEDA CONTRATISTA
            if ($total_resultados <= 0) :

                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I WHERE (SA.Dias LIKE '%$dia%' OR SA.Dias = '') AND SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion["Ingreso"];
                $datos_invitacion["Salida"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioAutorizacion"];
                $datos_invitacion["TipoInvitacion"] = "Contratista";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $datos_socio["TipoSocio"] = "Invitado por";
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");

                // PARA BUSCAR TODAS LAS INVITACIONES DE ESE MISMO INVITADO EN LAS FECHAS
                $IDInvitado = $datos_invitacion["IDInvitado"];

                // CON EL IDINVITADO BUSCAMOS TODAS LAS QUE ESTAN PARA ESE MISMO DIA SI HAY MAS DE UNA
                $LugaresInvitado = array();
                $SQLInvitaciones = "SELECT * FROM SocioAutorizacion WHERE IDClub = $IDClub AND IDInvitado = $IDInvitado AND FechaInicio <= CURDATE() AND FechaFin >= CURDATE()";
                $QRYInvitaciones = $dbo->query($SQLInvitaciones);

                while ($Datos = $dbo->fetchArray($QRYInvitaciones)) :
                    $InfoLugar[IDLugar] = $Datos[IDSocioAutorizacion];

                    $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $Datos[IDSocio]");
                    if ($IDClub == 127) :
                        $Nombre = "Invitado por: $DatosSocio[Nombre] $DatosSocio[Apellido] \n Apartamento: $DatosSocio[Predio] \n Fechas Autorizado: $Datos[FechaInicio] hasta $Datos[FechaFin]";
                    elseif ($IDClub == 18) :
                        $Nombre = "Invitado por: $DatosSocio[Nombre] $DatosSocio[Apellido] \n Se dirige a: $DatosSocio[Predio] \n Numero Documento: $datos_invitado[NumeroDocumento]";
                    else :
                        $Nombre = "Invitado por: $DatosSocio[Nombre] $DatosSocio[Apellido]";
                    endif;
                    $InfoLugar[Nombre] = $Nombre;
                    // BUSCAMOS LOS ULTIMO REGISTROS DE ACCESO
                    $SQLAcceso = "SELECT * FROM LogAccesoDiario WHERE IDInvitacion = $Datos[IDSocioAutorizacion] ORDER BY IDLogAcceso DESC LIMIT 1";
                    $QRYAcceso = $dbo->query($SQLAcceso);
                    $InfoAcceso = $dbo->fetchArray($QRYAcceso);

                    if (empty($InfoAcceso[Tipo]))
                        $InfoAcceso[Tipo] = "";

                    if (empty($InfoAcceso[Salida]))
                        $InfoAcceso[Salida] = "";

                    if (empty($InfoAcceso[FechaSalida]))
                        $InfoAcceso[FechaSalida] = "";

                    if (empty($InfoAcceso[Entrada]))
                        $InfoAcceso[Entrada] = "";

                    if (empty($InfoAcceso[FechaIngreso]))
                        $InfoAcceso[FechaIngreso] = "";

                    $UltimoRegistro[Tipo] = $InfoAcceso[Tipo];
                    $UltimoRegistro[Salida] = $InfoAcceso[Salida];
                    $UltimoRegistro[FechaSalida] = $InfoAcceso[FechaSalida];
                    $UltimoRegistro[Entrada] = $InfoAcceso[Entrada];
                    $UltimoRegistro[FechaIngreso] = $InfoAcceso[FechaIngreso];
                    $UltimoRegistro[OtrosDatos] = "";
                    $UltimoRegistro[LimiteSuperado] = "";

                    $InfoLugar[UltimoRegistro] = $UltimoRegistro;

                    array_push($LugaresInvitado, $InfoLugar);
                endwhile;

            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA INVITADOS GENERAL
            if ($total_resultados <= 0) :
                if (ctype_digit($qryString)) {

                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SI.* From SocioInvitado SI Where SI.NumeroDocumento = '" . (int) $qryString . "' and FechaIngreso = '" . date("Y-m-d") . "' and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";

                    $result_invitacion = $dbo->query($sql_invitacion);
                    $total_resultados = $dbo->rows($result_invitacion);
                    $datos_invitacion = $dbo->fetchArray($result_invitacion);

                    if ($total_resultados > 0) {
                        $autorizacion_invitacion = 1;
                    }

                    $datos_invitado_otro = $dbo->fetchAll("Invitado", " NumeroDocumento = '" . $qryString . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitacion["Ingreso"];
                    $datos_invitacion["Salida"];
                    $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitado"];
                    $datos_invitacion["TipoInvitacion"] = "Invitado";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                    $datos_invitado["NumeroDocumento"] = $qryString;
                    $datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
                    $datos_invitacion["FechaFin"] = $datos_invitacion["FechaIngreso"];
                    $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $datos_invitado["FotoFile"] = $datos_invitado_otro["FotoFile"];

                    // PARA BUSCAR TODAS LAS INVITACIONES DE ESE MISMO INVITADO EN LAS FECHAS
                    $IDInvitado = $datos_invitacion["IDInvitado"];

                    // CON EL IDINVITADO BUSCAMOS TODAS LAS QUE ESTAN PARA ESE MISMO DIA SI HAY MAS DE UNA
                    $LugaresInvitado = array();
                    $SQLInvitaciones = "SELECT * FROM SocioInvitado WHERE IDInvitado = $IDInvitado AND FechaIngreso = '" . date("Y-m-d") . "'";
                    $QRYInvitaciones = $dbo->query($SQLInvitaciones);

                    while ($Datos = $dbo->fetchArray($QRYInvitaciones)) :
                        $InfoLugar[IDLugar] = $Datos[IDSocioInvitadoEspecial];

                        $DatosSocio = $dbo->fetchAll("Socio", "IDSocio = $Datos[IDSocio]");
                        $Nombre = "Invitado por: $DatosSocio[Nombre] $DatosSocio[Apellido]";
                        $InfoLugar[Nombre] = $Nombre;
                        // BUSCAMOS LOS ULTIMO REGISTROS DE ACCESO
                        $SQLAcceso = "SELECT * FROM LogAccesoDiario WHERE IDClub = $IDClub AND IDInvitacion = $Datos[IDSocioInvitadoEspecial] ORDER BY IDLogAcceso DESC LIMIT 1";
                        $QRYAcceso = $dbo->query($SQLAcceso);
                        $InfoAcceso = $dbo->fetchArray($QRYAcceso);

                        if (empty($InfoAcceso[Tipo]))
                            $InfoAcceso[Tipo] = "";

                        if (empty($InfoAcceso[Salida]))
                            $InfoAcceso[Salida] = "";

                        if (empty($InfoAcceso[FechaSalida]))
                            $InfoAcceso[FechaSalida] = "";

                        if (empty($InfoAcceso[Entrada]))
                            $InfoAcceso[Entrada] = "";

                        if (empty($InfoAcceso[FechaIngreso]))
                            $InfoAcceso[FechaIngreso] = "";

                        $UltimoRegistro[Tipo] = $InfoAcceso[Tipo];
                        $UltimoRegistro[Salida] = $InfoAcceso[Salida];
                        $UltimoRegistro[FechaSalida] = $InfoAcceso[FechaSalida];
                        $UltimoRegistro[Entrada] = $InfoAcceso[Entrada];
                        $UltimoRegistro[FechaIngreso] = $InfoAcceso[FechaIngreso];
                        $UltimoRegistro[OtrosDatos] = "";
                        $UltimoRegistro[LimiteSuperado] = "";

                        $InfoLugar[UltimoRegistro] = $UltimoRegistro;

                        array_push($LugaresInvitado, $InfoLugar);
                    endwhile;
                }
            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA USUARIO FUNCIONARIO
            if ($total_resultados <= 0) :
                $whereAdd = "";



                // Validación para no permitir el ingreso de Usuarios con la fecha de contratación vencida.
                if ($ConfiguracionClub['LimitarIngresosPorFechaFinalContrato'] == 'S') {
                    $whereAdd = " AND IF(FechaFinContrato > '1999-01-01', FechaFinContrato >= '" . date('Y-m-d') . "',1) ";
                }
                //$sql_invitacion = "Select * From Usuario Where NumeroDocumento = '" . (int) $qryString . "' and IDClub = '" . $IDClub . "'";
                $sql_invitacion = "Select * From Usuario Where NumeroDocumento = '" .  $qryString . "' and IDClub = '" . $IDClub . "' $whereAdd";
                $modo_busqueda = "DOCUMENTO";

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                     //Validación lista negra para valle arriba athetic - Alertas de acceso
                    if ($IDClub == 239 || $IDClub == 8) {
                        $tarjetaNegra = $dbo->getFields("ListaNegraApp", "IDListaNegraApp", "NumeroDocumento = '" . $datos_invitacion["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
                   


                        if (!empty($tarjetaNegra)) {
                $datos_socio_bloqueo = $dbo->fetchAll("ListaNegraApp", " IDListaNegraApp = '" . $tarjetaNegra . "' ", "array");
 
                   //cuando es indefinido
                    if($datos_socio_bloqueo["FechaLimite"] == "0000-00-00"):
                    $motivo=$datos_socio_bloqueo["Motivo"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: Indefinidamente. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                            //cuando tiene una fecha limite
                    elseif($datos_socio_bloqueo["FechaLimite"] >= $fechaactual):
                    
                     $motivo=$datos_socio_bloqueo["Motivo"];
                     $fechalimite=$datos_socio_bloqueo["FechaLimite"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: $fechalimite. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                    else:
                             
                    endif;
                    

                        }
                    }
                }

                $id_registro = $datos_invitacion["IDUsuario"];
                // Deshabilitar alertas de diagnostico para vaac -> 239
                if ($IDClub != 239) {
                    $fecha_hoy = date("Y-m-d") . " 00:00:00";
                    $sql_unica = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDUsuario='" . $id_registro . "' Group by IDusuario ";
                    $r_unica = $dbo->query($sql_unica);
                    $total_unica = $dbo->rows($r_unica);
                    $row_resp_diag = $dbo->fetchArray($r_unica);
                    $peso_permitido = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' ");
                    if ($total_unica <= 0) {
                        $alerta_diagnostico = SIMUtil::get_traduccion('', '', 'Atenciónlapersonanohallenadoeldiagnostico', LANG);
                    } elseif ($row_resp_diag["Resultado"] > $peso_permitido) {
                        $alerta_diagnostico = SIMUtil::get_traduccion('', '', 'Atenciónlapersonasedebecomunicarconsaludocupacional', LANG);
                    } else {
                        $alerta_diagnostico = SIMUtil::get_traduccion('', '', 'Diagnosticocorrecto', LANG);
                    }
                }

                $datos_invitacion["Ingreso"];
                $datos_invitacion["Salida"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDUsuario"];
                $datos_invitacion["TipoInvitacion"] = "Usuario";
                $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                $datos_invitado["NumeroDocumento"] = $qryString;
                $datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
                $datos_invitacion["FechaFin"] = $datos_invitacion["FechaIngreso"];
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                $datos_invitado["FotoFile"] = $datos_invitado_otro["FotoFile"];
                $Observaciones = $alerta_diagnostico;

            endif;
            //FIN BUSQUEDA USUARIO FUNCIONARIO
            //BUSQUEDA SOCIO
            if ($total_resultados <= 0) :
                if (is_numeric($qryString)) {

                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select * From Socio Where (NumeroDocumento = '" .  $qryString . "' or Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "' or AccionPadre = '" . $qryString . "') and IDEstadoSocio = 1 and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa    o una accion
                    //Consulto las placas de vehiculos de socios
                    $sql_invitacion = "Select * From Socio Where (Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "' or NumeroDocumento = '" . $qryString . "') and IDClub = '" . $IDClub . "'
                        UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '" . $qryString . "' and IDClub = '" . $IDClub . "'
                        UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '" . $qryString . "' and IDClub = '" . $IDClub . "'  and AccionPadre = ''";
                }
                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                $FotoSocio = $datos_invitacion["Foto"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocio"];
                $IDSocio = $datos_invitacion["IDSocio"];
                $datos_invitacion["TipoInvitacion"] = "SocioClub";
                $datos_invitacion["PersonaAutoriza"] = "b";
                $datos_invitacion["FechaInicio"] = 'indefinida';
                $datos_invitacion["FechaFin"] = 'indedefinida';
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $datos_socio;
                $modulo = "Socio";
                $id_registro = $datos_invitacion["IDSocio"];

                $response_campos = array();
                $array_otros_datos["NumeroDocumento"] = $datos_invitacion["NumeroDocumento"];
                array_push($response_campos, $array_otros_datos);

                // Deshabilitar alertas de diagnostico para vaac -> 239
                if ($IDClub != 239) {
                    $fecha_hoy = date("Y-m-d") . " 00:00:00";
                    $sql_unica = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDSocio='" . $id_registro . "' Group by IDusuario ";
                    $r_unica = $dbo->query($sql_unica);
                    $total_unica = $dbo->rows($r_unica);
                    $row_resp_diag = $dbo->fetchArray($r_unica);
                    $peso_permitido = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' ");
                    if ($total_unica <= 0) {
                        $alerta_diagnostico = SIMUtil::get_traduccion('', '', 'Atenciónlapersonanohallenadoeldiagnostico', LANG);
                    } elseif ($row_resp_diag["Resultado"] > $peso_permitido) {
                        $alerta_diagnostico = SIMUtil::get_traduccion('', '', 'Atenciónlapersonasedebecomunicarconsaludocupacional', LANG);
                    } else {
                        $alerta_diagnostico = SIMUtil::get_traduccion('', '', 'Diagnosticocorrecto', LANG);
                    }
                    $Observaciones = $alerta_diagnostico;
                }
                $resp = SIMWebServiceReservas::get_reservas_socio($IDClub, $id_registro, $Limite, $IDReserva, $IDUsuario);
                $resp_como_invitado = SIMWebServiceReservas::get_reservas_socio_invitado($IDClub, $id_registro, 0, "", $IDUsuario);
                foreach ($resp["response"] as $key => $value) {
                    $FechaReserva = $value["Fecha"];
                    $HoraReserva = $value["Hora"];
                    $Servicio = $value["NombreServicio"];
                    if ($FechaReserva == date("Y-m-d")) {
                        $ObservacionsReservas .= "Fecha Reserva: " . $FechaReserva;
                        $ObservacionsReservas .= "Hora reserva: " . $HoraReserva;
                        $ObservacionsReservas .= "Servicio: " . $Servicio;
                    }
                }
                foreach ($resp_como_invitado["response"] as $key => $value) {
                    $FechaReserva = $value["Fecha"];
                    $HoraReserva = $value["Hora"];
                    $Servicio = $value["NombreServicio"];
                    if ($FechaReserva == date("Y-m-d")) {
                        $ObservacionsReservas .= "Fecha Reserva: " . $FechaReserva;
                        $ObservacionsReservas .= "Hora reserva: " . $HoraReserva;
                        $ObservacionsReservas .= "Servicio: " . $Servicio;
                    }
                }
                $Observaciones .= $ObservacionsReservas;

                $Observaciones .= "ESTADO: " . $dbo->getFields("EstadoSocio", "Nombre", "IDEstadoSocio = '" . $datos_invitacion["IDEstadoSocio"] . "'");
                $Vacunado = $dbo->fetchAll('Vacunado', "IDSocio=$IDSocio", "array");
                $EstaVacunado = ($Vacunado['DeseoVacuna'] == '' || empty($Vacunado['DeseoVacuna'])) ? 'no' : $Vacunado['DeseoVacuna'];

                $sql_Vacuna2 = "SELECT * FROM Vacuna2 WHERE IDSocio = $IDSocio ORDER BY IDDosis DESC LIMIT 1";
                $q_Vacuna2 = $dbo->query($sql_Vacuna2);
                $r_Vacuna2 = $dbo->assoc($q_Vacuna2);


                if (!empty($Vacunado)) {
                    if ($IDClub != 124 && $IDClub != 239) {
                        $Observaciones .= $SaltoLinea . " VACUNADO: " . $EstaVacunado;
                        $Observaciones .= $SaltoLinea . " Fecha vacunación: " . $r_Vacuna2['FechaVacuna'];
                        $Observaciones .= $SaltoLinea . " Entidad: " . $r_Vacuna2['EntidadDosis'];
                        $Observaciones .= $SaltoLinea . " Lugar: " . $r_Vacuna2['Lugar'];
                        $Observaciones .= $SaltoLinea . " Marca vacuna: " . $r_Vacuna2['Marca'];
                        $Observaciones .= $SaltoLinea . " Dosis: " . $dbo->getFields('Dosis', 'NumeroDosis', 'IDDosis = ' . $r_Vacuna2['IDDosis']);
                    }
                    // Imprimir Preguntas dinamicas Vacuna2
                    $sql_VacunaRespuesta = "SELECT vc.IDCampoVacunacion,vc.Valor, c.Nombre, c.Tipo FROM VacunaCampoVacunacion2 vc, CampoVacunacion c WHERE vc.IDCampoVacunacion=c.IDCampoVacunacion AND vc.IDVacuna = " . $r_Vacuna2['IDVacuna'] . " AND IDClub = " . $IDClub . " AND Valor <> '' AND Publicar = 'S' Order by Orden";

                    $q_VacunaRespuesta = $dbo->query($sql_VacunaRespuesta);
                    while ($r_VacunaRespuesta = $dbo->assoc($q_VacunaRespuesta)) {

                        if ($r_VacunaRespuesta['Tipo'] == 'imagen' || $r_VacunaRespuesta['Tipo'] == 'imagenarchivo') {
                            $Observaciones .= $SaltoLinea . " " . $r_VacunaRespuesta['Nombre'] . ": " . VACUNA_ROOT . $r_VacunaRespuesta['Valor'];
                        } else {
                            $Observaciones .= $SaltoLinea . " " . $r_VacunaRespuesta['Nombre'] . ": " . $r_VacunaRespuesta['Valor'];
                        }
                    }
                    //Fin Imprimir Preguntas dinamicas Vacuna2
                } else {
                    $Observaciones .= $SaltoLinea . " VACUNADO: No";
                }
                if ($CampoObservacionGeneral != '' && $datos_invitacion['ObservacionGeneral'] != '' && $IDClub == 88) {
                    $Observaciones .= $SaltoLinea . " Observacion general: " . $datos_invitacion['ObservacionGeneral'];
                }
                //Consulto grupo Familiar
                if (empty($datos_socio["AccionPadre"]) || !empty($datos_socio["Accion"])) : // Es Cabeza
                    $nucleo_socio = 1;
                    $condicion_nucleo = " and AccionPadre = '" . $datos_socio["Accion"] . "'";
                    $datos_invitacion["CabezaInvitacion"] = "S";
                    $response_nucleo = array();
                    $sql_grupo = "SELECT IDClub, IDSocio, Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras, NumeroDocumento FROM Socio WHERE IDClub = '" . $IDClub . "' and IDSocio <> '" . $datos_socio["IDSocio"] . "' " . $condicion_nucleo;
                    $result_grupo = $dbo->query($sql_grupo);
                    while ($row_nucleo = $dbo->fetchArray($result_grupo)) :
                        if (!empty($row_nucleo[Foto])) {
                            $foto_nucleo = SOCIO_ROOT . $row_nucleo[Foto];
                        } else {
                            $foto_nucleo = URLROOT . "plataform/assets/images/sinfoto.png";
                        }

                        $dato_nucleo["IDClub"] = $IDClub;
                        $dato_nucleo["IDInvitacion"] = $row_nucleo["IDSocio"];
                        $dato_nucleo["Nombre"] = $row_nucleo["Nombre"] . " " . $row_nucleo["Apellido"];
                        $dato_nucleo["TipoDocumento"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'");
                        $dato_nucleo["Documento"] = $row_nucleo["NumeroDocumento"];
                        $dato_nucleo["Foto"] = $foto_nucleo;
                        $dato_nucleo["TipoInvitacion"] = "SocioClub";

                        $response_campos_fam = array();
                        $array_otros_datos_fam["NumeroDocumento"] = $row_nucleo["NumeroDocumento"];
                        if ($CampoObservacionGeneral != '' && $row_nucleo['ObservacionGeneral'] != '' && $IDClub == 88) {
                            $array_otros_datos_fam["ObservacionGeneral"] = $row_nucleo['ObservacionGeneral'];
                        }
                        array_push($response_campos_fam, $array_otros_datos_fam);

                        $dato_nucleo["ValoresCampo"] = $response_campos_fam;
                        $dato_nucleo["UltimoRegistro"] = SIMWebServiceAccesos::ultimo_acceso_invitado($datos_socio["IDSocio"]);

                        if ($row_nucleo["IDSocio"] > 0) {
                            $response_obj_nuc = array();
                            $sql_obj = "SELECT * From AccesoObjeto Where IDSocio = '" . $row_nucleo["IDSocio"] . "' ";
                            $r_obj = $dbo->query($sql_obj);
                            if ($dbo->rows($r_obj) > 0) {
                                $encontrados = 1;
                                while ($row_obj = $dbo->fetchArray($r_obj)) {
                                    $dato_obj["IDObjeto"] = $row_obj["IDAccesoObjeto"];
                                    $dato_obj["Campo1"] = $row_obj["Campo1"];
                                    $dato_obj["Campo2"] =  $row_obj["Campo2"];
                                    $dato_obj["IDTipo"] = $row_obj["IDTipoObjeto"];
                                    $dato_obj["Tipo"] =  $dbo->getFields("TipoObjeto", "Nombre", "IDTipoObjeto = '" . $row_obj["IDTipoObjeto"] . "'");
                                    array_push($response_obj_nuc, $dato_obj);
                                }
                            }
                        }
                        $dato_nucleo["ObjetosIngresados"] = $response_obj_nuc;

                        //Tipos de ingresos
                        $response_tipo_n = array();
                        $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                        $array_tipo_n = explode(",", $TipoIngreso);
                        if (count($array_tipo_n) > 0) {
                            $encontrados = 1;
                            $message = count($array_tipo_n) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                            foreach ($array_tipo_n as $value) {
                                $tipo_ingreso_n["Nombre"] = utf8_encode($value);
                                array_push($response_tipo_n, $tipo_ingreso_n);
                            }
                        }

                        if ($datos_invitacion["IDSocio"] > 0) {
                            $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $datos_invitacion["IDSocio"] . "' ";
                            $r_vehiculo = $dbo->query($sql_vehiculo);
                            if ($dbo->rows($r_vehiculo) > 0) {
                                $encontrados = 1;
                                while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                                    $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                                    array_push($response_tipo_n, $tipo_ingreso_n);
                                }
                            }
                        }
                        $dato_nucleo["TipoIngreso"] = $response_tipo_n;
                        //Fin Tipo Ingreso

                        array_push($response_nucleo, $dato_nucleo);
                    endwhile;
                endif;

            endif;
            //FIN BUSQUEDA SOCIO          


            if ($total_resultados <= 0) {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noencontrado', LANG) . "!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end if
            else {

                //Para fontanar en observaciones agrego el predio del socio
                if ($IDClub == 35) {
                    $Observaciones = "Se dirige a:" . $datos_socio["Predio"];
                }

                $Observaciones .= " " . $datos_invitacion["ObservacionSocio"];
                if ($autorizacion_invitacion == 1) :
                    $datos_invitacion_individual = array();
                    $datos_invitacion_individual["IDInvitacion"] = $datos_invitacion["IDInvitacion"];
                    $datos_invitacion_individual["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                    $datos_invitacion_individual["FechaInicio"] = $datos_invitacion["FechaInicio"];
                    $datos_invitacion_individual["FechaFin"] = $datos_invitacion["FechaFin"];
                    $datos_invitacion_individual["Accion"] = $datos_socio["Accion"];
                    $datos_invitacion_individual["Socio"] = $datos_invitacion["PersonaAutoriza"];
                    $datos_invitacion_individual["TipoSocio"] = $datos_socio["TipoSocio"];
                    $datos_invitacion_individual["Observaciones"] = $Observaciones;
                    $datos_invitacion_individual["Ingreso"] = $datos_invitacion["Ingreso"];
                    $datos_invitacion_individual["FechaIngreso"] = $datos_invitacion["FechaInicio"];
                    $datos_invitacion_individual["Salida"] = $datos_invitacion["Salida"];
                    $datos_invitacion_individual["FechaSalida"] = $datos_invitacion["FechaFin"];

                    if (empty($response_campos))
                        $response_campos = [];

                    $datos_invitacion_individual["ValoresCampo"] = $response_campos;

                    if (!empty($datos_invitado[FotoFile])) {
                        $foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                    } elseif (!empty($FotoSocio)) {
                        $foto = SOCIO_ROOT . $FotoSocio;
                    } else {
                        $foto = URLROOT . "plataform/assets/images/sinfoto.png";
                    }

                    $ObsrvacionesEspeciales = "";

                    if ($IDClub == 18 && $datos_invitacion["IDSocio"] > 0) :
                        $ObsrvacionesEspeciales = "\n$datos_socio[Predio]\n$datos_socio[TipoSocio]";
                    endif;

                    $datos_invitacion_individual["Foto"] = $foto;
                    $datos_invitacion_individual["Nombre"] = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"] . $ObsrvacionesEspeciales;
                    $tipodoc = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                    if (empty($tipodoc)) {
                        $TipoDocumento = "Doc";
                    } else {
                        $TipoDocumento = $tipodoc;
                    }

                    $datos_invitacion_individual["TipoDocumentoInvitado"] = $TipoDocumento;
                    $datos_invitacion_individual["NumeroDocumentoInvitado"] = $datos_invitado["NumeroDocumento"];


                    //SI ES CABEZA CONUSLTO EL GRUPO FAMILIAR
                    $response_invitado_familia = array();
                    if ($datos_invitacion["CabezaInvitacion"] == "S") :
                        while ($datos_grupo_familiar = $dbo->fetchArray($result_grupo)) :
                            $datos_invitado_familiar = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_grupo_familiar["IDInvitado"] . "' ", "array");
                            if (!empty($datos_invitado_familiar[FotoFile])) {
                                $foto = INVITADO_ROOT . $datos_invitado_familiar[FotoFile];
                            } else {
                                $foto = URLROOT . "/images/sinfoto.png";
                            }

                            $dato_invitado_asociado["IDClub"] = $IDClub;
                            $dato_invitado_asociado["IDInvitacion"] = $datos_grupo_familiar["IDSocioInvitadoEspecial"];
                            $dato_invitado_asociado["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                            $dato_invitado_asociado["Nombre"] = $datos_invitado_familiar["Nombre"] . " " . $datos_invitado_familiar["Apellido"];
                            $dato_invitado_asociado["TipoDocumento"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'");
                            $dato_invitado_asociado["Documento"] = $datos_invitado_familiar["NumeroDocumento"];
                            $dato_invitado_asociado["Documento"] = $datos_invitado_familiar["NumeroDocumento"];

                            $response_campos_fam = array();
                            $array_otros_datos_fam["NumeroDocumento"] = $datos_invitado_familiar["NumeroDocumento"];
                            array_push($response_campos_fam, $array_otros_datos_fam);

                            $dato_invitado_asociado["ValoresCampo"] = $response_campos_fam;

                            //Tipos de ingresos
                            $response_tipo_n = array();
                            $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                            $array_tipo_n = explode(",", $TipoIngreso);
                            if (count($array_tipo_n) > 0) {
                                $encontrados = 1;
                                $message = count($array_tipo_n) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                                foreach ($array_tipo_n as $value) {
                                    $tipo_ingreso_n["Nombre"] = utf8_encode($value);
                                    array_push($response_tipo_n, $tipo_ingreso_n);
                                }
                            }

                            if ($IDSocio > 0) {
                                $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
                                $r_vehiculo = $dbo->query($sql_vehiculo);
                                if ($dbo->rows($r_vehiculo) > 0) {
                                    $encontrados = 1;
                                    while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                                        $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                                        array_push($response_tipo_n, $tipo_ingreso_n);
                                    }
                                }
                            }
                            $dato_invitado_asociado["TipoIngreso"] = $response_tipo_n;
                            //Fin Tipo Ingreso

                            array_push($response_invitado_familia, $dato_invitado_asociado);
                        endwhile;
                    endif;

                    $ultimoregistro = SIMWebServiceAccesos::ultimo_acceso_invitado($datos_invitacion["IDInvitacion"]);

                    if (!empty($ultimoregistro))
                        $datos_invitacion_individual["UltimoRegistro"] = $ultimoregistro;


                    $datos_invitacion_individual["GrupoInvitado"] = $response_invitado_familia;

                    //Objetos persona
                    if ($datos_invitacion["IDSocio"] > 0) {
                        $response_obj = array();
                        $sql_obj = "SELECT * From AccesoObjeto Where IDSocio = '" . $datos_invitacion["IDSocio"] . "' ";
                        $r_obj = $dbo->query($sql_obj);
                        if ($dbo->rows($r_obj) > 0) {
                            $encontrados = 1;
                            while ($row_obj = $dbo->fetchArray($r_obj)) {
                                $dato_obj["IDObjeto"] = $row_obj["IDAccesoObjeto"];
                                $dato_obj["Campo1"] = $row_obj["Campo1"];
                                $dato_obj["Campo2"] =  $row_obj["Campo2"];
                                $dato_obj["IDTipo"] = $row_obj["IDTipoObjeto"];
                                $dato_obj["Tipo"] =  $dbo->getFields("TipoObjeto", "Nombre", "IDTipoObjeto = '" . $row_obj["IDTipoObjeto"] . "'");
                                array_push($response_obj, $dato_obj);
                            }
                        }
                    }
                    $datos_invitacion_individual["ObjetosIngresados"] = $response_obj;
                else :
                    $datos_invitacion_individual = null;
                endif;

                if ($nucleo_socio == 1) :
                    $datos_invitacion_individual["GrupoInvitado"] = $response_nucleo;
                endif;

                $datos_invitacion_individual[LugaresInvitado] = $LugaresInvitado;

                //Tipos de ingresos
                $response_tipo = array();
                $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                $array_tipo = explode(",", $TipoIngreso);
                if (count($array_tipo) > 0) {
                    $encontrados = 1;
                    $message = count($array_tipo) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                    foreach ($array_tipo as $value) {
                        $tipo_ingreso["Nombre"] = utf8_encode($value);
                        array_push($response_tipo, $tipo_ingreso);
                    }
                }

                if ($IDSocio > 0) {
                    $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
                    $r_vehiculo = $dbo->query($sql_vehiculo);
                    if ($dbo->rows($r_vehiculo) > 0) {
                        $encontrados = 1;
                        while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                            $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                            array_push($response_tipo, $tipo_ingreso);
                        }
                    }

                    $datos_invitacion_individual["EstadoSocio"] = $dbo->getFields("EstadoSocio", "Nombre", "IDEstadoSocio = $datos_invitado[IDEstadoSocio]");
                    $datos_invitacion_individual["ColorEstadoSocio"] = $dbo->getFields("EstadoSocio", "Color", "IDEstadoSocio = $datos_invitado[IDEstadoSocio]");
                }
                $datos_invitacion_individual["TipoIngreso"] = $response_tipo;
                //Fin Tipo Ingreso

                $response = $datos_invitacion_individual;

                $respuesta["message"] = "ok";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } else {
            $respuesta["message"] = "Ver2.1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } //end function

    public function get_invitado_documento_v2($IDClub, $Documento, $Dispositivo = "")
    {
        $dbo = &SIMDB::get();
        require_once LIBDIR . "SIMWebService.inc.php";
        require_once LIBDIR . "SIMWebServiceReservas.inc.php";
        if (!empty($Documento)) {

            // Configuracion Club
            $q_ConfiguracionClub = $dbo->query("SELECT CampoObservacionGeneral,LimitarIngresosPorFechaFinalContrato FROM ConfiguracionClub WHERE IDClub = $IDClub");
            $ConfiguracionClub = $dbo->assoc($q_ConfiguracionClub);
            $CampoObservacionGeneral = $ConfiguracionClub['CampoObservacionGeneral'];
            // Fin Configuracion Club

            // Validación salto de linea para Android o IOS
            $SaltoLinea = ($Dispositivo == 'Android') ? "<br>" : "\r\n";
            //Fin Validación salto de linea para Android o IOS


            // Si es el club El Rincon se captura el numero de documento de la cadena que llega
            if ($IDClub == 10) {
                $arr_qryString = explode(" ", $Documento);
                $Documento = $arr_qryString[0];
            }
            //Fin Si es el club El Rincon se captura el numero de documento de la cadena que llega

            // Si es el club Exportiva se captura el numero de documento de la cadena que llega
            if ($IDClub == 136) {
                // if ($IDClub == 8) {
                $arr_qryString = explode("|", $Documento);
                $Documento = $arr_qryString[1];
            }
            //Fin Si es el club El Xportiva se captura el numero de documento de la cadena que llega

            $autorizacion_recogida = 0;
            $autorizacion_invitacion = 0;
            //BUSQUEDA INVITADOS ACCESOS
            $qryString = str_replace(".", "", $Documento);
            $qryString = str_replace(",", "", $qryString);
            $qryString = str_replace(" ", "", $qryString);
            if (ctype_digit($qryString)) {
                // si es solo numeros en un numero de documento
                $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I WHERE (SIE.Dias LIKE '%$dia%' OR SIE.Dias = '') AND SIE.IDInvitado = I.IDInvitado  and (I.NumeroDocumento = '" . (int) $qryString . "' OR I.NumeroDocumento = '" . $qryString . "') and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='" . $IDClub . "'";
                $modo_busqueda = "DOCUMENTO";
            } else {
                //seguramente es una placa
                //Consulto en invitaciones accesos
                $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V WHERE (SIE.Dias LIKE '%$dia%' OR SIE.Dias = '') AND SIE.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SIE.IDClub='" . $IDClub . "'";
                $modo_busqueda = "PLACA";
            }

            $result_invitacion = $dbo->query($sql_invitacion);
            $total_resultados = $dbo->rows($result_invitacion);
            $datos_invitacion = $dbo->fetchArray($result_invitacion);

            if ($total_resultados > 0) {
                $autorizacion_invitacion = 1;
              //Validación lista negra para valle arriba athetic - Alertas de acceso
                    if ($IDClub == 239 || $IDClub == 8) {
                        $tarjetaNegra = $dbo->getFields("ListaNegraApp", "IDListaNegraApp", "NumeroDocumento = '" . $datos_invitacion["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
                   


                        if (!empty($tarjetaNegra)) {
                $datos_socio_bloqueo = $dbo->fetchAll("ListaNegraApp", " IDListaNegraApp = '" . $tarjetaNegra . "' ", "array");
 
                   //cuando es indefinido
                    if($datos_socio_bloqueo["FechaLimite"] == "0000-00-00"):
                    $motivo=$datos_socio_bloqueo["Motivo"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: Indefinidamente. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                            //cuando tiene una fecha limite
                    elseif($datos_socio_bloqueo["FechaLimite"] >= $fechaactual):
                    
                     $motivo=$datos_socio_bloqueo["Motivo"];
                     $fechalimite=$datos_socio_bloqueo["FechaLimite"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: $fechalimite. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                    else:
                             
                    endif;
                    

                        }
                    }
            }

            $datos_invitacion["TipoInvitacion"] = "InvitadoAcceso";
            $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitadoEspecial"];
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
            $tipo_socio = $datos_socio["TipoSocio"];
            $datos_socio["TipoSocio"] = "Invitado por";
            $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");
            $Observaciones = "";
            // Consulto si tiene vehiculo asociado
            $Placa = $dbo->getFields('Vehiculo', 'Placa', "IDInvitado=" . $datos_invitado['IDInvitado']);
            if ($Placa && !empty($Placa)) {
                $Observaciones .= $SaltoLinea . "Placa: " . $Placa;
            }


            if ($datos_invitacion["Ingreso"] == "N") {
                $accion_acceso = "ingreso";
                $label_accion_acceso = "Ingres&oacute;";
            } elseif ($datos_invitacion["Salida"] == "N") {
                $accion_acceso = "salio";
                $label_accion_acceso = "Sali&oacute;";
            }
            //Consulto grupo Familiar
            if ($datos_invitacion["CabezaInvitacion"] == "S") :
                $sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '" . $datos_invitacion["IDSocioInvitadoEspecial"] . "'";
                $result_grupo = $dbo->query($sql_grupo);
            endif;

            // Consulto las preguntas dinamicas
            if ($total_resultados > 0 && isset($datos_invitacion['IDSocioInvitadoEspecial'])) {
                $sql_CampoFormularioInvitado = "SELECT IDCampoFormularioInvitado,EtiquetaCampo FROM CampoFormularioInvitado WHERE IDClub = $IDClub AND Publicar = 'S' ORDER BY Orden ASC";
                $q_CampoFormularioInvitado = $dbo->query($sql_CampoFormularioInvitado);
                $total_preguntas = $dbo->rows($q_CampoFormularioInvitado);
                if ($total_preguntas > 0) {
                    while ($row_preguntas_invitado = $dbo->fetchArray($q_CampoFormularioInvitado)) {
                        $sql_RespuestasInvitado = "SELECT Valor FROM SocioInvitadoEspecialOtrosDatos WHERE IDSocioInvitadoEspecial =" . $datos_invitacion['IDInvitacion'] . " AND IDCampoFormularioInvitado = " . $row_preguntas_invitado['IDCampoFormularioInvitado'];
                        $q_RespuestasInvitado = $dbo->query($sql_RespuestasInvitado);
                        $total_RespuestasInvitado = $dbo->rows($q_RespuestasInvitado);
                        if ($total_RespuestasInvitado > 0) {
                            $Respuesta_Inivitado = $dbo->assoc($q_RespuestasInvitado);
                            $Observaciones .= $SaltoLinea . $row_preguntas_invitado['EtiquetaCampo'] . ": " . $Respuesta_Inivitado['Valor'];
                        }
                    }
                }
            }
            // Fin Consulto las preguntas dinamicas
            // Club san jacinto, mostrar si es invitados
            if ($IDClub == 126 && isset($datos_invitacion['IDSocioInvitadoEspecial'])) {

                $Observaciones .= $SaltoLinea . " Tipo de tercero: Invitado";
                $Observaciones .= $SaltoLinea . " Nombre del titular: " . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                $Observaciones .= $SaltoLinea . " Número de Derecho: " . $datos_socio['Accion'];
            }

            //FIN BUSQUEDA INVITADOS ACCESOS

            //BUSQUEDA CONTRATISTA
            if ($total_resultados <= 0) :
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I WHERE (SA.Dias LIKE '%$dia%' OR SA.Dias = '') AND SA.IDInvitado = I.IDInvitado  and (I.NumeroDocumento = '" . (int) $qryString . "' OR I.NumeroDocumento = '" . $qryString . "') and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa
                    //Consulto en invitaciones accesos
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V WHERE (SA.Dias LIKE '%$dia%' OR SA.Dias = '') AND SA.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "PLACA";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                 //Validación lista negra para valle arriba athetic - Alertas de acceso
                    if ($IDClub == 239 || $IDClub == 8) {
                        $tarjetaNegra = $dbo->getFields("ListaNegraApp", "IDListaNegraApp", "NumeroDocumento = '" . $datos_invitacion["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
                   


                        if (!empty($tarjetaNegra)) {
                $datos_socio_bloqueo = $dbo->fetchAll("ListaNegraApp", " IDListaNegraApp = '" . $tarjetaNegra . "' ", "array");
 
                   //cuando es indefinido
                    if($datos_socio_bloqueo["FechaLimite"] == "0000-00-00"):
                    $motivo=$datos_socio_bloqueo["Motivo"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: Indefinidamente. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                            //cuando tiene una fecha limite
                    elseif($datos_socio_bloqueo["FechaLimite"] >= $fechaactual):
                    
                     $motivo=$datos_socio_bloqueo["Motivo"];
                     $fechalimite=$datos_socio_bloqueo["FechaLimite"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: $fechalimite. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                    else:
                             
                    endif;
                    

                        }
                    }
                }

                $datos_invitacion["Ingreso"];
                $datos_invitacion["Salida"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioAutorizacion"];
                $datos_invitacion["TipoInvitacion"] = "Contratista";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $datos_socio["TipoSocio"] = "Invitado por";
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");


                // Consulto las preguntas dinamicas
                if ($total_resultados > 0 && isset($datos_invitacion['IDSocioAutorizacion'])) {
                    $sql_CampoFormularioContratista = "SELECT IDCampoFormularioContratista,EtiquetaCampo FROM CampoFormularioContratista WHERE IDClub = $IDClub AND Publicar = 'S' ORDER BY Orden ASC";
                    $q_CampoFormularioContratista = $dbo->query($sql_CampoFormularioContratista);
                    $total_preguntas = $dbo->rows($q_CampoFormularioContratista);
                    if ($total_preguntas > 0) {
                        while ($row_preguntas = $dbo->fetchArray($q_CampoFormularioContratista)) {
                            $sql_Respuestas = "SELECT Valor FROM SocioAutorizacionOtrosDatos WHERE IDSocioAutorizacion =" . $datos_invitacion['IDInvitacion'] . " AND IDCampoFormularioContratista = " . $row_preguntas['IDCampoFormularioContratista'];
                            $q_Respuestas = $dbo->query($sql_Respuestas);
                            $total_Respuestas = $dbo->rows($q_Respuestas);
                            if ($total_Respuestas > 0) {
                                $Respuesta = $dbo->assoc($q_Respuestas);
                                $Observaciones .= $SaltoLinea . $row_preguntas['EtiquetaCampo'] . ": " . $Respuesta['Valor'];
                            }
                        }
                    }
                }
                // Fin Consulto las preguntas dinamicas

                // Club san jacinto, mostrar si es invitados
                if ($IDClub == 126 && isset($datos_invitacion['IDSocioAutorizacion'])) {
                    $Observaciones .= $SaltoLinea . " Tipo de tercero: Contratista";
                    $Observaciones .= $SaltoLinea . " Nombre del titular: " . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $Observaciones .= $SaltoLinea . " Número de Derecho: " . $datos_socio['Accion'];
                }

            endif;
            //FIN BUSQUEDA CONTRATISTA
            //BUSQUEDA INVITADOS GENERAL
            if ($total_resultados <= 0) :
                if (($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SI.* From SocioInvitado SI Where (SI.NumeroDocumento = '" . (int) $qryString . "' OR SI.NumeroDocumento = '" . $qryString . "') and FechaIngreso = '" . date("Y-m-d") . "' and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                    $result_invitacion = $dbo->query($sql_invitacion);
                    $total_resultados = $dbo->rows($result_invitacion);
                    $datos_invitacion = $dbo->fetchArray($result_invitacion);

                    if ($total_resultados > 0) {
                        $autorizacion_invitacion = 1;
                       //Validación lista negra para valle arriba athetic - Alertas de acceso
                    if ($IDClub == 239 || $IDClub == 8) {
                        $tarjetaNegra = $dbo->getFields("ListaNegraApp", "IDListaNegraApp", "NumeroDocumento = '" . $datos_invitacion["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
                   


                        if (!empty($tarjetaNegra)) {
                $datos_socio_bloqueo = $dbo->fetchAll("ListaNegraApp", " IDListaNegraApp = '" . $tarjetaNegra . "' ", "array");
 
                   //cuando es indefinido
                    if($datos_socio_bloqueo["FechaLimite"] == "0000-00-00"):
                    $motivo=$datos_socio_bloqueo["Motivo"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: Indefinidamente. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                            //cuando tiene una fecha limite
                    elseif($datos_socio_bloqueo["FechaLimite"] >= $fechaactual):
                    
                     $motivo=$datos_socio_bloqueo["Motivo"];
                     $fechalimite=$datos_socio_bloqueo["FechaLimite"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: $fechalimite. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                    else:
                             
                    endif;
                    

                        }
                    }
                    }

                    $datos_invitado_otro = $dbo->fetchAll("Invitado", " NumeroDocumento = '" . $qryString . "' ", "array");
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitacion["Ingreso"];
                    $datos_invitacion["Salida"];
                    $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitado"];
                    $datos_invitacion["TipoInvitacion"] = "Invitado";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                    $datos_invitado["NumeroDocumento"] = $qryString;
                    $datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
                    $datos_invitacion["FechaFin"] = $datos_invitacion["FechaIngreso"];
                    $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $datos_invitado["FotoFile"] = $datos_invitado_otro["FotoFile"];
                    $Observaciones .= "";
                    if (!empty($datos_invitacion['Placa'])) {
                        $Observaciones .= $SaltoLinea . "Placa: " . $datos_invitacion['Placa'];
                    }

                    // Consulto las preguntas dinamicas
                    if ($total_resultados > 0 && isset($datos_invitacion['IDSocioInvitado'])) {
                        $sql_CampoFormularioInvitado = "SELECT IDCampoFormularioInvitado,EtiquetaCampo,TipoCampo FROM CampoFormularioInvitado WHERE IDClub = $IDClub AND Publicar = 'S' ORDER BY Orden ASC";
                        $q_CampoFormularioInvitado = $dbo->query($sql_CampoFormularioInvitado);
                        $total_preguntas = $dbo->rows($q_CampoFormularioInvitado);
                        if ($total_preguntas > 0) {
                            while ($row_preguntas_invitado = $dbo->fetchArray($q_CampoFormularioInvitado)) {
                                if ($row_preguntas_invitado['TipoCampo'] != 'email') {
                                    $sql_RespuestasInvitado = "SELECT Valor FROM InvitadosOtrosDatos WHERE IDInvitacion =" . $datos_invitacion['IDInvitacion'] . " AND IDCampoFormularioInvitado = " . $row_preguntas_invitado['IDCampoFormularioInvitado'];
                                    $q_RespuestasInvitado = $dbo->query($sql_RespuestasInvitado);
                                    $total_RespuestasInvitado = $dbo->rows($q_RespuestasInvitado);
                                    if ($total_RespuestasInvitado > 0) {
                                        $Respuesta_Inivitado = $dbo->assoc($q_RespuestasInvitado);
                                        $Observaciones .= $SaltoLinea . $row_preguntas_invitado['EtiquetaCampo'] . ": " . $Respuesta_Inivitado['Valor'];
                                    }
                                }
                            }
                        }
                    }
                    // Fin Consulto las preguntas dinamicas
                    // Club san jacinto, mostrar si es invitados
                    if ($IDClub == 126 && isset($datos_invitacion['IDSocioInvitado'])) {
                        $Observaciones .= $SaltoLinea . " Tipo de tercero: Invitado";
                        $Observaciones .= $SaltoLinea . " Nombre del titular: " . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                        $Observaciones .= $SaltoLinea . " Número de Derecho: " . $datos_socio['Accion'];
                    }
                }
            endif;
            //FIN BUSQUEDA INVITADOS GENERAL

            //BUSQUEDA USUARIO FUNCIONARIO
            if ($total_resultados <= 0) :

                $whereAdd = "";
                // Validación para no permitir el ingreso de Usuarios con la fecha de contratación vencida.
                if ($ConfiguracionClub['LimitarIngresosPorFechaFinalContrato'] == 'S') {
                    $whereAdd = " AND IF(FechaFinContrato > '1999-01-01', FechaFinContrato >= '" . date('Y-m-d') . "',1) ";
                }
                //$sql_invitacion = "Select * From Usuario Where NumeroDocumento = '" . (int) $qryString . "' and IDClub = '" . $IDClub . "'";
                $sql_invitacion = "Select * From Usuario Where NumeroDocumento = '" .  $qryString . "' and IDClub = '" . $IDClub . "' $whereAdd";
                $modo_busqueda = "DOCUMENTO";

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                      //Validación lista negra para valle arriba athetic - Alertas de acceso
                    if ($IDClub == 239 || $IDClub == 8) {
                        $tarjetaNegra = $dbo->getFields("ListaNegraApp", "IDListaNegraApp", "NumeroDocumento = '" . $datos_invitacion["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
                   


                        if (!empty($tarjetaNegra)) {
                $datos_socio_bloqueo = $dbo->fetchAll("ListaNegraApp", " IDListaNegraApp = '" . $tarjetaNegra . "' ", "array");
 
                   //cuando es indefinido
                    if($datos_socio_bloqueo["FechaLimite"] == "0000-00-00"):
                    $motivo=$datos_socio_bloqueo["Motivo"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: Indefinidamente. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                            //cuando tiene una fecha limite
                    elseif($datos_socio_bloqueo["FechaLimite"] >= $fechaactual):
                    
                     $motivo=$datos_socio_bloqueo["Motivo"];
                     $fechalimite=$datos_socio_bloqueo["FechaLimite"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: $fechalimite. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                    else:
                             
                    endif;
                    

                        }
                    }
                }

                $id_registro = $datos_invitacion["IDUsuario"];
                if ($IDClub != 88 && $IDClub != 223 && $IDClub != 239) : // Omitir vacuncion para Serena del mar, Arboretto, Valle arriba vaac
                    $fecha_hoy = date("Y-m-d") . " 00:00:00";
                    $sql_unica = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDUsuario='" . $id_registro . "' Group by IDusuario ";
                    $r_unica = $dbo->query($sql_unica);
                    $total_unica = $dbo->rows($r_unica);
                    $row_resp_diag = $dbo->fetchArray($r_unica);
                    $peso_permitido = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' ");
                    if ($total_unica <= 0) {
                        $alerta_diagnostico = "Atención la persona no ha llenado el diagnostico";
                    } elseif ($row_resp_diag["Resultado"] > $peso_permitido) {
                        $alerta_diagnostico = "Atención la persona se debe comunicar con salud ocupacional";
                    } else {
                        $alerta_diagnostico = "Diagnostico correcto";
                    }
                endif;
                $datos_invitacion["Ingreso"];
                $datos_invitacion["Salida"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDUsuario"];
                $datos_invitacion["TipoInvitacion"] = "Usuario";
                $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                $datos_invitado["NumeroDocumento"] = $qryString;
                $datos_invitacion["FechaInicio"] = $datos_invitacion["FechaIngreso"];
                $datos_invitacion["FechaFin"] = $datos_invitacion["FechaIngreso"];
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                $datos_invitado["FotoFile"] = $datos_invitado_otro["FotoFile"];
                $FotoUsuario = $datos_invitacion["Foto"];
                $Observaciones .= $alerta_diagnostico;

            endif;
            //FIN BUSQUEDA USUARIO FUNCIONARIO

            //BUSQUEDA SOCIO o Empleado si esta como Socio
            if ($total_resultados <= 0) :
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select * From Socio Where (NumeroDocumento = '" . (int) $qryString . "' OR NumeroDocumento = '" . $qryString . "' OR Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "' or AccionPadre = '" . $qryString . "') and IDEstadoSocio = 1 and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa    o una accion
                    //Consulto las placas de vehiculos de socios
                    $sql_invitacion = "Select * From Socio Where (Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "' or NumeroDocumento = '" . $qryString . "') and IDClub = '" . $IDClub . "'
											  UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '" . $qryString . "' and IDClub = '" . $IDClub . "'
											  UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '" . $qryString . "' and IDClub = '" . $IDClub . "'  and AccionPadre = ''";
                }


                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                    $EsSocio = "S";
                     //Validación lista negra para valle arriba athetic - Alertas de acceso
                    if ($IDClub == 239 || $IDClub == 8) {
                        $tarjetaNegra = $dbo->getFields("ListaNegraApp", "IDListaNegraApp", "NumeroDocumento = '" . $datos_invitacion["NumeroDocumento"] . "' and IDClub = '" . $IDClub . "'");
                   


                        if (!empty($tarjetaNegra)) {
                $datos_socio_bloqueo = $dbo->fetchAll("ListaNegraApp", " IDListaNegraApp = '" . $tarjetaNegra . "' ", "array");
 
                   //cuando es indefinido
                    if($datos_socio_bloqueo["FechaLimite"] == "0000-00-00"):
                    $motivo=$datos_socio_bloqueo["Motivo"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: Indefinidamente. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                            //cuando tiene una fecha limite
                    elseif($datos_socio_bloqueo["FechaLimite"] >= $fechaactual):
                    
                     $motivo=$datos_socio_bloqueo["Motivo"];
                     $fechalimite=$datos_socio_bloqueo["FechaLimite"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: $fechalimite. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                    else:
                             
                    endif;
                    

                        }
                    }
                }

                $FotoSocio = $datos_invitacion["Foto"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocio"];
                $IDSocio = $datos_invitacion["IDSocio"];
                $datos_invitacion["TipoInvitacion"] = "SocioClub";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                if ($IDClub == 88) {
                    $PersonaAutoriza = $datos_socio['TipoSocio'];
                } else {
                    $PersonaAutoriza = "residente";
                }
                $datos_invitacion["PersonaAutoriza"] = " este documento pertenece a un $PersonaAutoriza ";
                $datos_invitacion["FechaInicio"] = 'indefinida';
                $datos_invitacion["FechaFin"] = 'indedefinida';
                $AccionSocio = $SaltoLinea . "ACCION: " . $datos_socio["Accion"];
                if ($IDClub == 239) : //VALLE ARRIBA ATHETIC 
                    if ($datos_socio["PermiteReservar"] == 1) :
                        $AccionSocio = $SaltoLinea . " ACCION: " . $datos_socio["Accion"] . $SaltoLinea . "Estado de cuenta: Su acción presenta un retraso a la fecha, por favor pase por caja lo antes posible" . $SaltoLinea;
                    else :
                        $AccionSocio = $SaltoLinea . " ACCION: " . $datos_socio["Accion"] . $SaltoLinea . "Estado de cuenta: Al día";
                    endif;
                endif;

                $datos_invitado = $datos_socio;
                $modulo = "Socio";


                $id_registro = $datos_invitacion["IDSocio"];
                if ($IDClub != 88 && $IDClub != 223 && $IDClub != 239) : // Omitir vacuncion para Serena del mar, arboretto y valle arriba vaac
                    $fecha_hoy = date("Y-m-d") . " 00:00:00";
                    $sql_unica = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDSocio='" . $id_registro . "' Group by IDusuario ";
                    $r_unica = $dbo->query($sql_unica);
                    $total_unica = $dbo->rows($r_unica);
                    $row_resp_diag = $dbo->fetchArray($r_unica);
                    $peso_permitido = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag["IDDiagnostico"] . "' ");
                    if ($total_unica <= 0) {
                        $alerta_diagnostico = "Atención la persona no ha llenado el diagnostico";
                    } elseif ($row_resp_diag["Resultado"] > $peso_permitido) {
                        $alerta_diagnostico = "Atención! la persona se debe comunicar con salud ocupacional.";
                    } else {
                        $alerta_diagnostico = "Diagnostico correcto";
                    }
                    $Observaciones .= $alerta_diagnostico;
                endif;

                if ($total_resultados > 0 && $EsSocio == "S") {
                    $resp = SIMWebServiceReservas::get_reservas_socio($IDClub, $id_registro, $Limite, $IDReserva, $IDUsuario);
                    $resp_como_invitado = SIMWebServiceReservas::get_reservas_socio_invitado($IDClub, $id_registro, 0, "", $IDUsuario);
                }

                foreach ($resp["response"] as $key => $value) {
                    $FechaReserva = $value["Fecha"];
                    $HoraReserva = $value["Hora"];
                    $Servicio = $value["NombreServicio"];
                    if ($FechaReserva == date("Y-m-d")) {
                        $ObservacionsReservas .= "Fecha Reserva: " . $FechaReserva;
                        $ObservacionsReservas .= "Hora reserva: " . $HoraReserva;
                        $ObservacionsReservas .= "Servicio: " . $Servicio;
                    }
                }

                foreach ($resp_como_invitado["response"] as $key => $value) {
                    $FechaReserva = $value["Fecha"];
                    $HoraReserva = $value["Hora"];
                    $Servicio = $value["NombreServicio"];
                    if ($FechaReserva == date("Y-m-d")) {
                        $ObservacionsReservas .= "Fecha Reserva: " . $FechaReserva . $SaltoLinea;
                        $ObservacionsReservas .= "Hora reserva: " . $HoraReserva . $SaltoLinea;
                        $ObservacionsReservas .= "Servicio: " . $Servicio . $SaltoLinea;
                    }
                }

                // Datos carrera para Xportiva
                if ($IDClub == 136) {
                    // if ($IDClub == 8) {
                    $sql_Carrera = "SELECT IDCarrera FROM Carrera where IDClub = $IDClub AND (FechaInicio >= '" . date('Y-m-d') . "') AND Activo = 'S' ORDER BY IDCarrera  DESC LIMIT 1";
                    $q_Carrera  = $dbo->query($sql_Carrera);
                    $n_Carrera = $dbo->rows($q_Carrera);
                    if ($n_Carrera > 0) {
                        $r_Carrera = $dbo->assoc($q_Carrera);

                        $Corredor = $dbo->getFields('RegistroCorredor', "NumCamiseta", "IDCarrera = " . $r_Carrera['IDCarrera'] . " AND IDSocio = " . $datos_invitacion['IDSocio']);
                        $Observaciones .= "$SaltoLinea Número de dorsal: " . $Corredor . $SaltoLinea;
                    }
                }
                $Observaciones .= $ObservacionsReservas;
                if ($datos_invitacion['FechaNacimiento'] != '') {
                    $Observaciones .= "$SaltoLinea Edad: " . SIMUtil::Calcular_edad($datos_invitacion['FechaNacimiento']);
                }
                $Observaciones .= "$SaltoLinea ESTADO: " . $dbo->getFields("EstadoSocio", "Nombre", "IDEstadoSocio = '" . $datos_invitacion["IDEstadoSocio"] . "'");

                $Vacunado = $dbo->fetchAll('Vacunado', "IDSocio=$IDSocio", "array");
                $EstaVacunado = ($Vacunado['DeseoVacuna'] == '' || empty($Vacunado['DeseoVacuna'])) ? 'no' : $Vacunado['DeseoVacuna'];

                $sql_Vacuna2 = "SELECT * FROM Vacuna2 WHERE IDSocio = $IDSocio ORDER BY IDDosis DESC LIMIT 1";
                $q_Vacuna2 = $dbo->query($sql_Vacuna2);
                $r_Vacuna2 = $dbo->assoc($q_Vacuna2);

                if ($IDClub != 88 && $IDClub != 239) : // Omitir vacuncion para Serena del mar
                    if (!empty($Vacunado)) {
                        if ($IDClub != 124) {
                            $Observaciones .= $SaltoLinea . " VACUNADO: " . $EstaVacunado;
                            $Observaciones .= $SaltoLinea . " Fecha vacunación: " . $r_Vacuna2['FechaVacuna'];
                            $Observaciones .= $SaltoLinea . " Entidad: " . $r_Vacuna2['EntidadDosis'];
                            $Observaciones .= $SaltoLinea . " Lugar: " . $r_Vacuna2['Lugar'];
                            $Observaciones .= $SaltoLinea . " Marca vacuna: " . $r_Vacuna2['Marca'];
                            $Observaciones .= $SaltoLinea . " Dosis: " . $dbo->getFields('Dosis', 'NumeroDosis', 'IDDosis = ' . $r_Vacuna2['IDDosis']);
                        }
                        // Imprimir Preguntas dinamicas Vacuna2
                        $sql_VacunaRespuesta = "SELECT vc.IDCampoVacunacion,vc.Valor, c.Nombre, c.Tipo FROM VacunaCampoVacunacion2 vc, CampoVacunacion c WHERE vc.IDCampoVacunacion=c.IDCampoVacunacion AND vc.IDVacuna = " . $r_Vacuna2['IDVacuna'] . " AND IDClub = " . $IDClub . " AND Valor <> '' AND Publicar = 'S' Order by Orden";

                        $q_VacunaRespuesta = $dbo->query($sql_VacunaRespuesta);
                        while ($r_VacunaRespuesta = $dbo->assoc($q_VacunaRespuesta)) {

                            if ($r_VacunaRespuesta['Tipo'] == 'imagen' || $r_VacunaRespuesta['Tipo'] == 'imagenarchivo') {
                                // $Observaciones .= "<br> \r\n " . $r_VacunaRespuesta['Nombre'] . ": Si, con imagen";
                                $Observaciones .= $SaltoLinea . " " . $r_VacunaRespuesta['Nombre'] . ": " . VACUNA_ROOT . $r_VacunaRespuesta['Valor'];
                            } else {
                                $Observaciones .= $SaltoLinea . " " . $r_VacunaRespuesta['Nombre'] . ": " . $r_VacunaRespuesta['Valor'];
                            }
                        }
                        //Fin Imprimir Preguntas dinamicas Vacuna2
                    } else {
                        $Observaciones .= $SaltoLinea . " VACUNADO: No";
                    }
                endif;
                if ($CampoObservacionGeneral != '' && $datos_invitacion['ObservacionGeneral'] != '' && $IDClub == 88) {
                    $Observaciones .= $SaltoLinea . " Observacion general: " . $datos_invitacion['ObservacionGeneral'];
                }
                if ($IDClub == 88) {
                    $Observaciones .= $SaltoLinea . " Predio: " . $datos_invitacion['Predio'];
                }

                //Consulto grupo Familiar
                if (empty($datos_socio["AccionPadre"]) || !empty($datos_socio["Accion"]) || $EsSocio == "S") : // Es Cabeza
                    $nucleo_socio = 1;
                    if (!empty($datos_socio["AccionPadre"])) {
                        $cond_otra_nucleo = "  or AccionPadre = '" . $datos_socio["AccionPadre"] . "' ";
                    }

                    $condicion_nucleo = " and (AccionPadre = '" . $datos_socio["Accion"] . "' " . $cond_otra_nucleo . ")";
                    $datos_invitacion["CabezaInvitacion"] = "S";
                    $response_nucleo = array();
                    $sql_grupo = "SELECT IDClub, IDSocio, TipoSocio,Foto, Nombre, Apellido, Accion, AccionPadre, CodigoBarras,IDEstadoSocio, NumeroDocumento,FechaNacimiento FROM Socio WHERE IDClub = '" . $IDClub . "' and IDSocio <> '" . $datos_socio["IDSocio"] . "' " . $condicion_nucleo;
                    $result_grupo = $dbo->query($sql_grupo);
                    $Observaciones_nucleo = "";
                    while ($row_nucleo = $dbo->fetchArray($result_grupo)) :
                        if (!empty($row_nucleo[Foto])) {
                            $foto_nucleo = SOCIO_ROOT . $row_nucleo[Foto];
                        } else {
                            $foto_nucleo = URLROOT . "plataform/assets/images/sinfoto.png";
                        }
                        if ($IDClub != 88 && $IDClub != 223 && $IDClub != 239) : // Omitir vacuncion para Serena del mar, Arboretto y valle arriba vaac
                            $sql_unica_n = "SELECT IDDiagnosticoRespuesta,SUM(Peso) as Resultado, IDDiagnostico FROM  DiagnosticoRespuesta WHERE FechaTrCr >= '" . $fecha_hoy . "' and IDSocio='" . $row_nucleo['IDSocio'] . "' Group by IDusuario ";
                            $r_unica_n = $dbo->query($sql_unica_n);
                            $total_unica_n = $dbo->rows($r_unica_n);
                            $row_resp_diag_n = $dbo->fetchArray($r_unica_n);
                            $peso_permitido_n = $dbo->getFields("Diagnostico", "PesoMaximo", "IDDiagnostico = '" . $row_resp_diag_n["IDDiagnostico"] . "' ");
                            if ($total_unica_n <= 0) {
                                $alerta_diagnostico_n = "Atención la persona no ha llenado el diagnostico";
                            } elseif ($row_resp_diag_n["Resultado"] > $peso_permitido_n) {
                                $alerta_diagnostico_n = "Atención! la persona se debe comunicar con salud ocupacional.";
                            } else {
                                $alerta_diagnostico_n = "Diagnostico correcto";
                            }
                            $Observaciones_nucleo = $alerta_diagnostico_n;
                        endif;
                        $Observaciones_nucleo .= $SaltoLinea . "TIPO SOCIO: " .  $row_nucleo["TipoSocio"];
                        if ($row_nucleo['FechaNacimiento'] != '') {
                            $Observaciones_nucleo .= $SaltoLinea . "Edad: " . SIMUtil::Calcular_edad($row_nucleo['FechaNacimiento']);
                        }

                        $Observaciones_nucleo .= $SaltoLinea . "ESTADO: " . $dbo->getFields("EstadoSocio", "Nombre", "IDEstadoSocio = '" .  $row_nucleo["IDEstadoSocio"] . "'");

                        $Vacunado = $dbo->fetchAll('Vacunado', "IDSocio=$IDSocio", "array");

                        $EstaVacunado = ($Vacunado['DeseoVacuna'] == '' || empty($Vacunado['DeseoVacuna'])) ? 'no' : $Vacunado['DeseoVacuna'];
                        $sql_Vacuna2 = "SELECT * FROM Vacuna2 WHERE IDSocio = $IDSocio ORDER BY IDDosis DESC LIMIT 1";
                        $q_Vacuna2 = $dbo->query($sql_Vacuna2);
                        $r_Vacuna2 = $dbo->assoc($q_Vacuna2);

                        if ($IDClub != 88) : // Omitir vacuncion para Serena del mar
                            if (!empty($Vacunado)) {
                                if ($IDClub != 124) {
                                    $Observaciones_nucleo .= $SaltoLinea . " VACUNADO: " . $EstaVacunado;
                                    $Observaciones_nucleo .= $SaltoLinea . " Fecha vacunación: " . $r_Vacuna2['FechaVacuna'];
                                    $Observaciones_nucleo .= $SaltoLinea . " Entidad: " . $r_Vacuna2['EntidadDosis'];
                                    $Observaciones_nucleo .= $SaltoLinea . " Lugar: " . $r_Vacuna2['Lugar'];
                                    $Observaciones_nucleo .= $SaltoLinea . " Marca vacuna: " . $r_Vacuna2['Marca'];
                                    $Observaciones_nucleo .= $SaltoLinea . " Dosis: " . $dbo->getFields('Dosis', 'NumeroDosis', 'IDDosis = ' . $r_Vacuna2['IDDosis']);
                                }
                                // Imprimir Preguntas dinamicas Vacuna2
                                $sql_VacunaRespuesta = "SELECT vc.IDCampoVacunacion,vc.Valor, c.Nombre, c.Tipo FROM VacunaCampoVacunacion2 vc, CampoVacunacion c WHERE vc.IDCampoVacunacion=c.IDCampoVacunacion AND vc.IDVacuna = " . $r_Vacuna2['IDVacuna'] . " AND IDClub = " . $IDClub . " AND Valor <> '' AND Publicar = 'S' Order by Orden";

                                $q_VacunaRespuesta = $dbo->query($sql_VacunaRespuesta);
                                while ($r_VacunaRespuesta = $dbo->assoc($q_VacunaRespuesta)) {

                                    if ($r_VacunaRespuesta['Tipo'] == 'imagen' || $r_VacunaRespuesta['Tipo'] == 'imagenarchivo') {
                                        // $Observaciones_nucleo .= "<br> \r\n " . $r_VacunaRespuesta['Nombre'] . ": Si, con imagen";
                                        $Observaciones_nucleo .= $SaltoLinea . " " . $r_VacunaRespuesta['Nombre'] . ": " . VACUNA_ROOT . $r_VacunaRespuesta['Valor'];
                                    } else {
                                        $Observaciones_nucleo .= $SaltoLinea . " " . $r_VacunaRespuesta['Nombre'] . ": " . $r_VacunaRespuesta['Valor'];
                                    }
                                }
                                //Fin Imprimir Preguntas dinamicas Vacuna2
                            } else {
                                $Observaciones_nucleo .= $SaltoLinea . " VACUNADO: No";
                            }
                        endif;

                        if ($CampoObservacionGeneral != '' && $row_nucleo['ObservacionGeneral'] != '' && $IDClub == 88) {
                            $Observaciones_nucleo = $SaltoLinea . "Observacion general: " . $row_nucleo['ObservacionGeneral'];
                        }

                        $dato_nucleo["Observaciones"] = $Observaciones_nucleo;
                        $dato_nucleo["IDClub"] = $IDClub;
                        $dato_nucleo["IDInvitacion"] = $row_nucleo["IDSocio"];
                        $dato_nucleo["Nombre"] = $row_nucleo["Nombre"] . " " . $row_nucleo["Apellido"];
                        $dato_nucleo["TipoDocumento"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'");
                        $dato_nucleo["TipoDocumentoInvitado"] = "Documento: ";
                        $dato_nucleo["Documento"] = $row_nucleo["NumeroDocumento"];
                        $dato_nucleo["Foto"] = $foto_nucleo;
                        $dato_nucleo["TipoInvitacion"] = "SocioClub";

                        //Tipos de ingresos
                        $response_tipo_n = array();
                        $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                        $array_tipo_n = explode(",", $TipoIngreso);
                        if (count($array_tipo_n) > 0) {
                            $encontrados = 1;
                            $message = count($array_tipo_n) . " Encontrados";
                            foreach ($array_tipo_n as $value) {
                                $tipo_ingreso_n["Nombre"] = utf8_encode($value);
                                array_push($response_tipo_n, $tipo_ingreso_n);
                            }
                        }

                        if ($IDSocio > 0) {
                            $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
                            $r_vehiculo = $dbo->query($sql_vehiculo);
                            if ($dbo->rows($r_vehiculo) > 0) {
                                $encontrados = 1;
                                while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                                    $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                                    array_push($response_tipo_n, $tipo_ingreso_n);
                                }
                            }
                        }
                        $dato_nucleo["TipoIngreso"] = $response_tipo_n;
                        //Fin Tipo Ingreso

                        array_push($response_nucleo, $dato_nucleo);
                        unset($Observaciones_nucleo);
                    endwhile;
                endif;

            endif;
            //FIN BUSQUEDA SOCIO


            // Valida lista negra por defecto
            //Validación lista negra para valle arriba athetic - Alertas de acceso
                    if ($IDClub == 239 || $IDClub == 8) {
                        $tarjetaNegra = $dbo->getFields("ListaNegraApp", "IDListaNegraApp", "NumeroDocumento = '" . $Documento . "' and IDClub = '" . $IDClub . "'");
                   


                        if (!empty($tarjetaNegra)) {
                $datos_socio_bloqueo = $dbo->fetchAll("ListaNegraApp", " IDListaNegraApp = '" . $tarjetaNegra . "' ", "array");
 
                   //cuando es indefinido
                    if($datos_socio_bloqueo["FechaLimite"] == "0000-00-00"):
                    $motivo=$datos_socio_bloqueo["Motivo"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: Indefinidamente. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                            //cuando tiene una fecha limite
                    elseif($datos_socio_bloqueo["FechaLimite"] >= $fechaactual):
                    
                     $motivo=$datos_socio_bloqueo["Motivo"];
                     $fechalimite=$datos_socio_bloqueo["FechaLimite"];
                    $mensaje="Lo sentimos, actualmente se encuentra en lista negra y no es posible su acceso. $SaltoLinea
                    Motivo: $SaltoLinea
                    $motivo $SaltoLinea
                    Fecha limite de restrincion: $fechalimite. 
                    ";
                            $respuesta['message'] = $mensaje;
                            $respuesta['success'] = false;
                            $respuesta['response'] = "";
                            return $respuesta;
                            
                    else:
                             
                    endif;
                    

                        }
                    } 
            //Busco en autorizaciones de recogida d alumnos

            $array_autorizacion_recogida = SIMWebServiceAccesos::buscar_autorizacion_recogida($IDClub, $Documento);

            if ($total_resultados <= 0 && count($array_autorizacion_recogida) <= 0) {
                $respuesta["message"] = "No encontrado!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end if
            else {

                //Para fontanar en observaciones agrego el predio del socio
                if ($IDClub == 35 || $IDClub == 18) {
                    $Observaciones .= "Se dirije a: " . $datos_socio["Predio"];
                }
                //Para fontanar en observaciones agrego el predio del socio
                if ($IDClub == 18) {

                    $otrosCampos = "SELECT Valor FROM SocioAutorizacionOtrosDatos WHERE IDSocioAutorizacion = $datos_invitacion[IDInvitacion] AND IDCampoFormularioContratista = 7";
                    $qry = $dbo->query($otrosCampos);
                    $data = $dbo->fetchArray($qry);

                    if (!empty($datos_invitado["Predio"])) :
                        $dirge = $datos_invitado["Predio"];
                    else :
                        $dirge = $data[Valor];
                    endif;

                    //$Observaciones = " Se dirije a ".$dirge;
                }

                $Observaciones .= $datos_invitacion["ObservacionSocio"];
                if ($autorizacion_invitacion == 1) :

                    if ($IDClub == 158) :
                        $socio = $datos_invitacion["PersonaAutoriza"] . " Torre: " . $datos_socio["Torre"] . " Apto: " . $datos_socio["Predio"];
                        $Observaciones .= "<br>Torre: " . $datos_socio["Torre"] . " Apto: " . $datos_socio["Predio"];
                    else :
                        $socio = $datos_invitacion["PersonaAutoriza"];
                    endif;

                    // Mostrar cartera socio para el club Izcaragua
                    if ($IDClub == 189) {
                        include('SIMWebServiceIzcaragua.inc.php');
                        $responseCartera = SIMWebServiceIzcaragua::DeudaSocio($datos_invitacion['IDSocio']);
                        $MensajeCartera = $responseCartera["message"];
                        $CodigoCliente = $datos_invitacion['Accion'];
                        $MontoCliente = 0;
                        if ($responseCartera["response"][0]["deuda"] == "TRUE") {
                            $MensajeCartera = $responseCartera["message"];
                            $CodigoCliente = $responseCartera["response"][0]["co_cli"];
                            // $MontoCliente = $responseCartera["response"][0]["monto"];
                        }

                        // if ($MontoCliente > 0) {
                        // $Observaciones .= "<br><br>Código cliente:<br>$CodigoCliente<br>Cartera pendiente:<br>$MontoCliente<br>Mensaje:<br>$MensajeCartera<br>";
                        $Observaciones .= "<br><br>Código cliente:<br>$CodigoCliente<br>Mensaje:<br>$MensajeCartera<br>";
                        // }
                    }

                    // Fin Mostrar cartera socio para el club Izcaragua

                    // Mostrar cartera socio para el club Playa Azul
                    if ($IDClub == 156) {
                        include('SIMWebServicePlayaAzul.inc.php');
                        $responseCartera = SIMWebServicePlayaAzul::Deuda($datos_invitacion['AccionPadre']);
                        $RespuestaCarteraPlayaAzul = $responseCartera['response'];
                        if (!empty($RespuestaCarteraPlayaAzul)) :
                            foreach ($RespuestaCarteraPlayaAzul as $indice => $cartera) {
                                $Observaciones .= "\r\n<br>" . '<p style="color:red">Acción: ' . $cartera[0]['Accion'] . " \r\n<br>Mensaje: Saldo pendiente<br><br> \r\n";
                            }
                        endif;
                    }
                    // Fin Mostrar cartera socio para el club Playa Azul

                    $datos_invitacion_individual = array();
                    $datos_invitacion_individual["IDInvitacion"] = $datos_invitacion["IDInvitacion"];
                    $datos_invitacion_individual["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                    $datos_invitacion_individual["FechaInicio"] = $datos_invitacion["FechaInicio"];
                    $datos_invitacion_individual["FechaFin"] = $datos_invitacion["FechaFin"];
                    $datos_invitacion_individual["Accion"] = $datos_socio["Accion"];
                    $datos_invitacion_individual["Socio"] = $socio;
                    $datos_invitacion_individual["TipoSocio"] = $datos_socio["TipoSocio"];
                    $datos_invitacion_individual["Observaciones"] = $Observaciones;
                    $datos_invitacion_individual["Ingreso"] = $datos_invitacion["Ingreso"];
                    $datos_invitacion_individual["FechaIngreso"] = $datos_invitacion["FechaInicio"];
                    $datos_invitacion_individual["Salida"] = $datos_invitacion["Salida"];
                    $datos_invitacion_individual["FechaSalida"] = $datos_invitacion["FechaFin"];

                    if (!empty($datos_invitado[FotoFile])) {
                        $foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                    } elseif (!empty($FotoSocio)) {
                        $foto = SOCIO_ROOT . $FotoSocio;
                    } elseif (!empty($FotoUsuario)) {
                        $foto = USUARIO_ROOT . $FotoUsuario;
                    } else {
                        $foto = URLROOT . "plataform/assets/images/sinfoto.png";
                    }

                    $datos_invitacion_individual["Foto"] = $foto;
                    $datos_invitacion_individual["NombreInvitado"] = $datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"];
                    $tipodoc = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                    if (empty($tipodoc)) {
                        $TipoDocumento = "Doc";
                    } else {
                        $TipoDocumento = $tipodoc;
                    }

                    $datos_invitacion_individual["TipoDocumentoInvitado"] = $TipoDocumento;
                    $datos_invitacion_individual["NumeroDocumentoInvitado"] = $datos_invitado["NumeroDocumento"] . $AccionSocio;

                    //SI ES CABEZA CONUSLTO EL GRUPO FAMILIAR
                    $response_invitado_familia = array();
                    if ($datos_invitacion["CabezaInvitacion"] == "S") :
                        while ($datos_grupo_familiar = $dbo->fetchArray($result_grupo)) :
                            $datos_invitado_familiar = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_grupo_familiar["IDInvitado"] . "' ", "array");
                            if (!empty($datos_invitado_familiar[FotoFile])) {
                                $foto = INVITADO_ROOT . $datos_invitado_familiar[FotoFile];
                            } else {
                                $foto = URLROOT . "/images/sinfoto.png";
                            }
                            // Consulto si tiene vehiculo asociado
                            $Placa = $dbo->getFields('Vehiculo', 'Placa', "IDInvitado=" . $datos_invitado_familiar['IDInvitado']);
                            if ($Placa && !empty($Placa)) {
                                $ObservacionesInvitado = $SaltoLinea . "Placa: " . $Placa;
                            }


                            $dato_invitado_asociado["IDClub"] = $IDClub;
                            $dato_invitado_asociado["IDInvitacion"] = $datos_grupo_familiar["IDSocioInvitadoEspecial"];
                            $dato_invitado_asociado["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                            $dato_invitado_asociado["Nombre"] = $datos_invitado_familiar["Nombre"] . " " . $datos_invitado_familiar["Apellido"];
                            $dato_invitado_asociado["TipoDocumento"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado_familiar["IDTipoDocumento"] . "'");
                            $dato_invitado_asociado["Documento"] = $datos_invitado_familiar["NumeroDocumento"];
                            $dato_invitado_asociado["Documento"] = $datos_invitado_familiar["NumeroDocumento"];
                            $dato_invitado_asociado["Observaciones"] = $ObservacionesInvitado;

                            //Tipos de ingresos
                            $response_tipo_n = array();
                            $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                            $array_tipo_n = explode(",", $TipoIngreso);
                            if (count($array_tipo_n) > 0) {
                                $encontrados = 1;
                                $message = count($array_tipo_n) . " Encontrados";
                                foreach ($array_tipo_n as $value) {
                                    $tipo_ingreso_n["Nombre"] = utf8_encode($value);
                                    array_push($response_tipo_n, $tipo_ingreso_n);
                                }
                            }

                            if ($IDSocio > 0) {
                                $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
                                $r_vehiculo = $dbo->query($sql_vehiculo);
                                if ($dbo->rows($r_vehiculo) > 0) {
                                    $encontrados = 1;
                                    while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                                        $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                                        array_push($response_tipo_n, $tipo_ingreso_n);
                                    }
                                }
                            }
                            $dato_invitado_asociado["TipoIngreso"] = $response_tipo_n;
                            //Fin Tipo Ingreso

                            array_push($response_invitado_familia, $dato_invitado_asociado);
                            unset($ObservacionesInvitado);
                        endwhile;
                    endif;

                    $datos_invitacion_individual["GrupoInvitado"] = $response_invitado_familia;

                    //Consulto el historial de ingresos y salidas del dia
                    $response_historial_acceso = array();
                    $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
                    $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
                    $sql_historial = $dbo->query("Select * From LogAcceso Where IDInvitacion = '" . $datos_invitacion["IDInvitacion"] . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "'");
                    while ($row_historial = $dbo->fetchArray($sql_historial)) :
                        $dato_historial["Tipo"] = $row_historial["Tipo"];
                        $dato_historial["Salida"] = $row_historial["Salida"];
                        $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
                        $dato_historial["Entrada"] = $row_historial["Entrada"];
                        $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];

                        $datos_campos = "";
                        //Consulto los otros datos registrados
                        $array_otros_datos = explode("|", $row_historial["CamposAcceso"]);
                        foreach ($array_otros_datos as $valor) {
                            if (!empty($valor)) {
                                if (!in_array($valor, $array_respuesta)) {
                                    $datos_campos .= $valor . "<br>";
                                }
                                $array_respuesta[] = $valor;
                            }
                        }
                        $dato_historial["OtrosDatos"] = $datos_campos;
                        $dato_historial["LimiteSuperado"] = $row_historial["LimiteSuperado"];

                        array_push($response_historial_acceso, $dato_historial);
                    endwhile;
                    $datos_invitacion_individual["Historial"] = $response_historial_acceso;
                else :
                    $datos_invitacion_individual = null;
                endif;

                if ($nucleo_socio == 1) :
                    $datos_invitacion_individual["GrupoInvitado"] = $response_nucleo;
                endif;

                //Tipos de ingresos
                $response_tipo = array();
                $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
                $array_tipo = explode(",", $TipoIngreso);
                if (count($array_tipo) > 0) {
                    $encontrados = 1;
                    $message = count($array_tipo) . " Encontrados";
                    foreach ($array_tipo as $value) {
                        $tipo_ingreso["Nombre"] = utf8_encode($value);
                        array_push($response_tipo, $tipo_ingreso);
                    }
                }

                if ($IDSocio > 0) {
                    $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
                    $r_vehiculo = $dbo->query($sql_vehiculo);
                    if ($dbo->rows($r_vehiculo) > 0) {
                        $encontrados = 1;
                        while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                            $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                            array_push($response_tipo, $tipo_ingreso);
                        }
                    }

                    $datos_invitacion_individual["EstadoSocio"] = $dbo->getFields("EstadoSocio", "Nombre", "IDEstadoSocio = $datos_invitado[IDEstadoSocio]");
                    $datos_invitacion_individual["ColorEstadoSocio"] = $dbo->getFields("EstadoSocio", "Color", "IDEstadoSocio = $datos_invitado[IDEstadoSocio]");
                }
                $datos_invitacion_individual["TipoIngreso"] = $response_tipo;
                //Fin Tipo Ingreso

                $response["Invitacion"] = $datos_invitacion_individual;

                $response["AutorizacionRecogida"] = $array_autorizacion_recogida;

                $respuesta["message"] = "ok";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } else {
            $respuesta["message"] = "Ver2.1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } //end function

    function get_encuesta_acceso($IDClub)
    {

        $dbo = &SIMDB::get();

        $response = array();

        $sql = "SELECT * FROM EncuestaArbolAcceso WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' Limit 1 ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $mostrar_encuesta = 1;
                if ($mostrar_encuesta == 1) {



                    //consulto si tiene preguntas asociadas
                    $sql_pregunta_hijos = "SELECT EAOR.IDEncuestaArbolAccesoPregunta,IDEncuestaArbolAccesoPreguntaSiguiente,IDEncuestaArbolAccesoOpcionesRespuesta
                                 FROM PreguntaEncuestaArbolAcceso PEA, EncuestaArbolAccesoOpcionesRespuesta EAOR
                                 WHERE EAOR.IDEncuestaArbolAccesoPregunta = PEA.IDPreguntaEncuestaArbolAcceso
                                 And IDEncuestaArbolAcceso = '" . $r["IDEncuestaArbolAcceso"] . "' and IDEncuestaArbolAccesoPreguntaSiguiente>0";
                    $r_pregunta_hijo = $dbo->query($sql_pregunta_hijos);
                    while ($row_pregunta_hijo = $dbo->fetchArray($r_pregunta_hijo)) {
                        $array_hijo[$row_pregunta_hijo["IDEncuestaArbolAccesoPreguntaSiguiente"]][] = $row_pregunta_hijo;
                    }

                    //print_r($array_hijo);
                    //print_r($array_hijo[1]);
                    //exit;


                    //Pregunta Dinamicas tipo arbol
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "SELECT * FROM PreguntaEncuestaArbolAcceso Where IDEncuestaArbolAcceso = '" . $r["IDEncuestaArbolAcceso"] . "' and Publicar = 'S' Order by Orden";
                    $r_encuesta = $dbo->query($sql_respuesta);
                    while ($row_pregunta = $dbo->fetchArray($r_encuesta)) :

                        $response_pregunta_padre = array();
                        if (count($array_hijo[$row_pregunta["IDPreguntaEncuestaArbolAcceso"]]) > 0) {
                            foreach ($array_hijo[$row_pregunta["IDPreguntaEncuestaArbolAcceso"]] as $id_hijo => $datos_hijo) {
                                $pregunta_padre["IDPreguntaMovilidad"] = $datos_hijo["IDEncuestaArbolAccesoPregunta"];
                                $pregunta_padre["IDMovilidadOpcionesRespuesta"] = $datos_hijo["IDEncuestaArbolAccesoOpcionesRespuesta"];
                                array_push($response_pregunta_padre, $pregunta_padre);
                            }
                        }



                        $pregunta["IDPadrePregunta"] = $response_pregunta_padre;
                        $pregunta["IDPreguntaAcceso"] = $row_pregunta["IDPreguntaEncuestaArbolAcceso"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        //Consulto los valores
                        $sql_opciones = "SELECT * FROM EncuestaArbolAccesoOpcionesRespuesta WHERE IDEncuestaArbolAccesoPregunta = '" . $row_pregunta["IDPreguntaEncuestaArbolAcceso"] . "' order by Orden";
                        $r_opciones = $dbo->query($sql_opciones);
                        $opciones_respuesta = array();
                        $response_valores = array();
                        while ($row_opciones = $dbo->fetchArray($r_opciones)) {
                            $opciones_respuesta["IDPreguntaAcceso"] = $row_opciones["IDEncuestaArbolAccesoPregunta"];
                            $opciones_respuesta["IDAccesoOpcionesRespuesta"] = $row_opciones["IDEncuestaArbolAccesoOpcionesRespuesta"];
                            //$opciones_respuesta[ "IDEncuestaArbolPreguntaSiguiente" ] = $row_opciones[ "IDEncuestaArbolPreguntaSiguiente" ];
                            $opciones_respuesta["Opcion"] = $row_opciones["Opcion"];
                            array_push($response_valores, $opciones_respuesta);
                        }
                        $pregunta["Valores"] = $response_valores;
                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        array_push($response_pregunta, $pregunta);
                    endwhile;
                }
            } //ednw hile

            $respuesta["response"] = $response_pregunta;
        } //End if


        return $response_pregunta;
    } // fin function

    function set_respuesta_movilidad($IDClub, $IDSocio, $IDEncuestaArbol, $Respuestas, $IDUsuario = "", $NumeroDocumento = "", $Nombre = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub)  && (!empty($IDSocio) || !empty($IDUsuario) || !empty($NumeroDocumento)) && !empty($IDEncuestaArbol)) {
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


            $guardar_encuesta = 1;
            if ($guardar_encuesta == 1) {
                $Respuestas = trim(preg_replace('/\s+/', ' ', $Respuestas));
                $datos_respuesta = json_decode($Respuestas, true);
                if (count($datos_respuesta) > 0) :
                    foreach ($datos_respuesta as $detalle_respuesta) :
                        if ($detalle_respuesta["Valor"] != "null") {
                            $sql_datos_form = $dbo->query("INSERT INTO EncuestaArbolAccesoRespuesta (IDEncuestaArbolAcceso, IDSocio, IDUsuario, IDPreguntaEncuestaArbolAcceso, IDEncuestaArbolAccesoOpcionesRespuesta, NumeroDocumento, Nombre, TipoUsuario, Valor, FechaTrCr) Values ('" . $IDEncuestaArbol . "','" . $IDSocio . "','" . $IDUsuario . "','" . $detalle_respuesta["IDPreguntaMovilidad"] . "','" . $detalle_respuesta["ValorID"] . "','" . $NumeroDocumento . "','" . $Nombre . "','" . $TipoUsuario . "','" . $detalle_respuesta["Valor"] . "','" . date("Y-m-d H:i:s") . "')");
                            $suma_peso += $detalle_respuesta["Peso"];
                            $datos_pregunta = $dbo->fetchAll("PreguntaEncuestaArbolAcceso", " IDPreguntaEncuestaArbolAcceso = '" . $detalle_respuesta["IDPreguntaEncuestaArbolAcceso"] . "' ", "array");
                            $respuestas_EncuestaArbol .= $datos_pregunta["EtiquetaCampo"] . "=" . $detalle_respuesta["Valor"] . "<br>";
                        }
                    endforeach;
                endif;

                $RespuestaEncuestaArbol = SIMUtil::get_traduccion('', '', 'Guardadoconéxito.', LANG);

                $respuesta["message"] = $RespuestaEncuestaArbol;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Yahabíacontestado,solosepermite1vezpordía', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = NULL;
            }
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = NULL;
        }

        return $respuesta;
    }

    public function get_campo_acceso($IDClub)
    {

        $dbo = &SIMDB::get();
        $response = array();
        //Pregunta
        $pregunta = array();
        $response_pregunta = array();
        $sql_respuesta = "Select * From PreguntaAcceso Where IDClub = '" . $IDClub . "' and Publicar = 'S' Order by Orden";
        $r_encuesta = $dbo->query($sql_respuesta);
        if ($dbo->rows($r_encuesta) > 0) {
            $message = $dbo->rows($r_encuesta) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :
                $pregunta["IDPreguntaAcceso"] = $row_pregunta["IDPreguntaAcceso"];
                $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                $pregunta["Valores"] = $row_pregunta["Valores"];
                $pregunta["Orden"] = $row_pregunta["Orden"];
                array_push($response, $pregunta);
            endwhile;
        } //End if


        return $response;
    } // fin function

    public function ultimo_acceso_invitado($IDInvitacion)
    {
        $dbo = &SIMDB::get();

        $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
        $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";

        //Consulto el historial de ingresos y salidas del dia
        $response_historial_acceso = array();
        $qry_acceso = "SELECT Tipo,Salida,FechaSalida,Entrada,FechaIngreso,Mecanismo,CamposAcceso,LimiteSuperado,Predio,Habitacion From LogAcceso Where IDInvitacion = '" . $IDInvitacion . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "' Order By IDLogAcceso DESC LIMIT 1";
        $sql_historial = $dbo->query($qry_acceso);
        while ($row_historial = $dbo->fetchArray($sql_historial)) :
            $dato_historial["Tipo"] = $row_historial["Tipo"];
            $dato_historial["Salida"] = $row_historial["Salida"];
            $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
            $dato_historial["Entrada"] = $row_historial["Entrada"];
            $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];

            $datos_campos = "";
            //Consulto los otros datos registrados
            $array_otros_datos = explode("|", $row_historial["CamposAcceso"]);
            foreach ($array_otros_datos as $valor) {
                if (!empty($valor)) {
                    if (!in_array($valor, $array_respuesta)) {
                        $datos_campos .= $valor . "<br>";
                    }
                    $array_respuesta[] = $valor;
                }
            }
            $dato_historial["OtrosDatos"] = $datos_campos;
            $dato_historial["LimiteSuperado"] = $row_historial["LimiteSuperado"];

            array_push($response_historial_acceso, $dato_historial);
        endwhile;
        $datos_invitacion_individual["Historial"] = $response_historial_acceso;

        return $dato_historial;
    }

    public function set_entrada_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo = "", $IDUsuario = "", $OtrosCampos = "", $RespuestasGeneral = "", $IDLugar = "")
    {
        $dbo = &SIMDB::get();
        require_once LIBDIR . "SIMWebServiceCheck.inc.php";
        if (!empty($IDLugar)) :
            $IDInvitacion = $IDLugar;
        endif;
        //Guardo el Log de la busqueda
        $parametros = "INVITACION:" . $IDInvitacion . " TIPO: " . $TipoInvitacion . " Mecanismo: " . $Mecanismo;

        $sql_log_peticion = $dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('" . $IDClub . "','" . $IDUsuario . "','" . $parametros . "','Entrada','" . date("Y-m-d H:i:s") . "')");
        if (!empty($IDClub) && !empty($IDInvitacion) && !empty($TipoInvitacion)) {


            $PermitirMultipleAcceso = $dbo->getFields("ConfiguracionClub", "PermitirMultipleAcceso", "IDClub=$IDClub");
            // Validamos los accesos en el día
            $sql_log_acceso_ultimo = "Select Entrada,Salida From LogAcceso Where IDInvitacion = '" . $IDInvitacion . "' AND FechaTrCr = '" . date('Y-m-d') . "'  Order by IDLogAcceso Desc Limit 1";
            $result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
            $row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);
            if ($PermitirMultipleAcceso == 'N') {
                if ($row_log_acceso_ultimo["Entrada"] == "S") :
                    $respuesta["message"] = "I5:" . SIMUtil::get_traduccion('', '', 'NoSePermiteEntradaSinSalida', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            }
            switch ($TipoInvitacion) {
                case "InvitadoAcceso":

                    $FechaFinAutorizacion = $dbo->getFields("SocioInvitadoEspecial", "FechaFin", "IDSocioInvitadoEspecial = $IDInvitacion");
                    $FechaHoy = date('Y-m-d');

                    // Valida si aun puede ingresar
                    if (strtotime($FechaHoy) > strtotime($FechaFinAutorizacion)) {
                        $respuesta["message"] = "Lo sentimos, la fecha de autorización ha expirado para el inivitado";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    // Fin Valida si aun puede ingresar
                    $SQLActualiza = "Update SocioInvitadoEspecial Set Ingreso = 'S', FechaIngreso = '" . date("Y-m-d H:i:s") . "', IDUsuarioIngreso = '$IDUsuario' Where IDSocioInvitadoEspecial = '$IDInvitacion'";
                    $sql_ingreso = $dbo->query($SQLActualiza);

                    //Envio Notificacion Push
                    if ($IDClub != "9") : // Solo para mesa yeguas temporalmente no se registra
                        SIMUtil::push_socio_entrada($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);

                    //Verifico si alguien mas innvito a esta persona para enviarle también la notificación
                    /* $sql_otra_inv = "Select * From SocioInvitadoEspecial Where IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' and FechaInicio<=CURDATE() and FechaFin >= CURDATE() and IDSocioInvitadoEspecial <> '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "'";
                        $result_otra_inv = $dbo->query($sql_otra_inv);
                        while ($row_otra_inv = $dbo->fetchArray($result_otra_inv)) :
                            SIMUtil::push_socio_entrada($IDClub, $row_otra_inv["IDSocioInvitadoEspecial"], $TipoInvitacion, $IDUsuario);
                        endwhile; */

                    endif;

                    break;

                case "Contratista":
                    $FechaFinAutorizacion = $dbo->getFields("SocioAutorizacion", "FechaFin", "IDSocioAutorizacion = $IDInvitacion Order by IDSocioAutorizacion desc limit 1");
                    $FechaHoy = date('Y-m-d');
                    // Valida si aun puede ingresar
                    if (strtotime($FechaHoy) > strtotime($FechaFinAutorizacion)) {
                        $respuesta["message"] = "Lo sentimos, la fecha de autorización ha expirado para el contratista";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    // Fin Valida si aun puede ingresar


                    /* if($IDClub == "127"):
                        $FechaHoy = date("Y-m-d");
                        $Dias = date("w");

                        $Festivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '$FechaHoy' and IDPais = 1");

                        if($Dia == 0 || !empty($Festivo)):
                            $respuesta["message"] = "No esta permitido el ingreso de los contratistas los Domingos y festivos";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif; */

                    if ($IDClub == "18") : // Para Fontanar en algunas horas no se debe permitir el acceso
                        $flag_restriccion = 1;
                        $fecha_hora_actual = date("Y-m-d H:i:s");

                        if (date("N", strtotime($Fecha)) == "7") : //Domingo
                            $flag_restriccion = 1;
                        endif;

                        if (date("N", strtotime($Fecha)) == "6") : //Sabado
                            $hora_inicio_permitida = date("Y-m-d 08:00:00");
                            $hora_fin_permitida = date("Y-m-d 12:00:00");
                            if (strtotime($fecha_hora_actual) >= strtotime($hora_inicio_permitida) && strtotime($fecha_hora_actual) <= strtotime($hora_fin_permitida)) :
                                $flag_restriccion = 0;
                            else :
                                $flag_restriccion = 1;
                            endif;

                        else : //Lunes - Viernes
                            $hora_inicio_permitida = date("Y-m-d 01:00:00");
                            //$hora_fin_permitida = date("Y-m-d 17:00:00");
                            $hora_fin_permitida = date("Y-m-d 23:00:00");
                            if (strtotime($fecha_hora_actual) >= strtotime($hora_inicio_permitida) && strtotime($fecha_hora_actual) <= strtotime($hora_fin_permitida)) :
                                $flag_restriccion = 0;
                            else :
                                $flag_restriccion = 1;
                            endif;
                        endif;

                        if ($flag_restriccion == 1) :
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimos,noestaenelhorarioestablecidoparaaccesodecontratistas', LANG);
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;

                    $sql_ingreso = $dbo->query("Update SocioAutorizacion Set Ingreso = 'S', FechaIngreso = '" . date("Y-m-d H:i:s") . "',IDUsuarioIngreso = '" . $IDUsuario . "' Where IDSocioAutorizacion = '" . $IDInvitacion . "'");


                    if ($IDClub != "9" && $IDClub != "37") // Solo para mesa yeguas y polo temporalmente no se registra
                    {
                        SIMUtil::push_socio_entrada($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;

                case "Socio":
                    ///Se deja la PreSalida en blanco
                    $sql_ingreso = $dbo->query("Update Socio Set IDSocioPresalida = '', Presalida = 'N',FehaPresalida = '' Where IDSocio = '" . $IDInvitacion . "'");

                    //para el club del country cambiamos la cantidad de ausencias de ser así
                    // if ($IDClub == 44) {
                    if ($IDClub == 44 || $IDClub == 8) {
                        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDInvitacion . "' ", "array");
                        if ($datos_socio["SocioAusente"] == "S") {
                            $cantidadAusencias = $datos_socio["CantidadAusencias"];
                            // Comentado por que solo debe sumar el ingreso al socio ausente al que se le marco el ingreso
                            // $selectNucleo = "SELECT IDSocio FROM Socio WHERE AccionPadre = '" . $datos_socio['AccionPadre'] . "' AND IDClub = 44";
                            // $queryNucleo = $dbo->query($selectNucleo);
                            // while ($nucleo = $dbo->fetchArray($queryNucleo)) {

                            //Consulto el historial de entradas y salidas del dia
                            $sql_log_acceso = "Select * From LogAccesoDiario Where IDInvitacion = '" . $datos_socio['IDSocio'] . "' and ( DATE_FORMAT(FechaIngreso,'%Y-%m-%d') = '" . date("Y-m-d") . "' or DATE_FORMAT(FechaSalida,'%Y-%m-%d') = '" . date("Y-m-d") . "') Order by IDLogAcceso Desc";
                            $result_log_acceso = $dbo->query($sql_log_acceso);
                            $rows_accesos = $dbo->rows($result_log_acceso);

                            if ($rows_accesos <= 0) {
                                $cantidadAusencias += 1;
                                $sqlAusencias = "UPDATE Socio SET CantidadAusencias = '" . $cantidadAusencias . "' WHERE IDSocio = '" . $datos_socio['IDSocio'] . "' AND IDClub = $IDClub";
                                $qryActualiza = $dbo->query($sqlAusencias);
                                if ($cantidadAusencias == 29) :
                                    $correo = "junta@countryclubdebogota.com,seguridad@countryclubdebogota.com,cartera@countryclubdebogota.com,rafael.gonzalez@countryclubdebogota.com," . $datos_socio['CorreoElectronico'];
                                    $Mensaje = "El socio ausente $datos_socio[Nombre] $datos_socio[Apellido] lleva $cantidadAusencias entradas";
                                    $Asunto = "ALERTA LÍMITE INGRESOS DE AUSENTE";
                                    SIMUtil::envia_correo_general($IDClub, $correo, $Mensaje, $Asunto);
                                endif;
                            }

                            // while ($row_log_acceso = $dbo->fetchArray($result_log_acceso)) {
                            //     if ($row_log_acceso["Entrada"] == 'S') :
                            //         $cantidadAusencias = 1;
                            //         $sqlAusencias = "UPDATE Socio SET CantidadAusencias = '" . $cantidadAusencias . "' WHERE IDSocio = '" . $datos_socio['IDSocio'] . "' AND IDClub = 44";
                            //         $qryActualiza = $dbo->query($sqlAusencias);
                            //         break;
                            //     endif;
                            // }
                            // }
                        }
                    }
                    break;

                case "SocioInvitado":
                case "Invitado":
                    $FechaFinAutorizacion = $dbo->getFields("SocioInvitado", "FechaIngreso", "IDSocioInvitado = $IDInvitacion");
                    $FechaHoy = date('Y-m-d');

                    // Valida si aun puede ingresar
                    if (strtotime($FechaHoy) != strtotime($FechaFinAutorizacion)) {
                        $respuesta["message"] = "Lo sentimos, el invitado no está autorizado para ingresar hoy";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    // Fin Valida si aun puede ingresar

                    //No tiene proceso especifico solo se registra el log de acceso
                    $sql_ingreso = $dbo->query("Update SocioInvitado Set Estado = 'I', FechaIngresoClub = '" . date("Y-m-d H:i:s") . "' Where IDSocioInvitado = '" . $IDInvitacion . "'");
                    //Envio Notificacion Push
                    if ($IDClub != "9") // Solo para mesa yeguas temporalmente no se registra
                    {
                        SIMUtil::push_socio_entrada($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;

                case "Usuario":

                    $accesoFuncionarios = $dbo->getFields("ConfiguracionClub", "AccesoFuncionarios", "IDClub = $IDClub");

                    if ($accesoFuncionarios == 'S') {

                        $rCheckinF = SIMWebServiceCheck::set_checkin_funcionarios($IDClub, $IDInvitacion, 'E');
                    }

                    break;
            }

            //Para Hacienda Fontanar muestro alerta si e=la persona ya se le había registrado acceso
            if ($IDClub == "18") :
                $sql_ultimo_registro = "Select * From LogAcceso Where IDInvitacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' and Tipo = '" . $TipoInvitacion . "' and FechaTrCr >= '" . date("Y-m-d 00:00:00") . "' and Entrada = 'S' Order by IDLogAcceso Desc Limit 1";
                $result_ultimo_registro = $dbo->query($sql_ultimo_registro);
                $row_ultimo_registro = $dbo->fetchArray($result_ultimo_registro);
                if ($row_ultimo_registro["Entrada"] == "S") :
                    $mensaje_alerta_ingreso = SIMUtil::get_traduccion('', '', 'ATENCION!!Estapersonayatieneuningresoeldíadehoy', LANG) . ":" . $row_ultimo_registro["FechaTrCr"];
                endif;
            endif;

            $OtrosCampos = str_replace("\\", "", $OtrosCampos);
            $datos_respuesta = json_decode($OtrosCampos, true);
            if (count($datos_respuesta) > 0) :
                $LimiteSuperado = "N";

                foreach ($datos_respuesta as $detalle_respuesta) :
                    if ($detalle_respuesta["IDPreguntaAcceso"] == 97) { //Guardo predio en campo aparte
                        $Predio = $detalle_respuesta["Valor"];
                    }
                    if ($detalle_respuesta["IDPreguntaAcceso"] == 98) { //Guardo habitacion en campo aparte
                        $Habitacion = $detalle_respuesta["Valor"];
                    }

                    $datos_pregunta = $dbo->fetchAll("PreguntaAcceso", " IDPreguntaAcceso = '" . $detalle_respuesta["IDPreguntaAcceso"] . "' ", "array");
                    if ($datos_pregunta["TipoCampo"] == "number" && $datos_pregunta["Limite"] > 0 && $detalle_respuesta["Valor"] > $datos_pregunta["Limite"]) {
                        //se envia notificacion de alerta al responsable
                        $datos_mensaje = $datos_pregunta["EtiquetaCampo"] . "=" . $detalle_respuesta["Valor"];
                        SIMUtil::notifica_alerta_acceso($IDClub, $IDInvitacion, $TipoInvitacion, $datos_mensaje);
                        $LimiteSuperado = "S";
                    }
                    $OtrosCamposAcceso .= "|" . $datos_pregunta["EtiquetaCampo"] . ":" . $detalle_respuesta["Valor"] . "|";
                //echo $detalle_respuesta["IDPreguntaAcceso"].":".$detalle_respuesta["Valor"] . " --- ";
                endforeach;
            endif;
            // Agregar registro de porteria para Mesa de Yeguas
            if ($IDClub == 9 && isset($_SESSION['Porteria'])) {
                $UsuarioPorteria = $dbo->getFields('Usuario', 'Nombre', "IDUsuario = $IDUsuario");
                $UsuarioTrCr = "$UsuarioPorteria|" . $_SESSION['Porteria'];
            }
            //Registro el historial de accesos
            $sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, IDClub, Tipo, Entrada, Mecanismo, FechaIngreso,UsuarioTrCr,FechaTrCr, IDUsuario, CamposAcceso,LimiteSuperado,Predio,Habitacion) Values ('" . $IDInvitacion . "','" . $IDClub . "', '" . $TipoInvitacion . "','S','" . $Mecanismo . "','" . date("Y-m-d H:i:s") . "','" . $UsuarioTrCr . "','" . date("Y-m-d H:i:s") . "','" . $IDUsuario . "','" . $OtrosCamposAcceso . "','" . $LimiteSuperado . "','" . $Predio . "','" . $Habitacion . "')");

            $IDLogA = $dbo->lastID();
            foreach ($datos_respuesta as $detalle_respuesta) {

                if ($detalle_respuesta["IDPreguntaAcceso"] == 97) { //Guardo predio en campo aparte
                    $Predio = $detalle_respuesta["Valor"];
                }
                if ($detalle_respuesta["IDPreguntaAcceso"] == 98) { //Guardo habitacion en campo aparte
                    $Habitacion = $detalle_respuesta["Valor"];
                }

                $datos_pregunta = $dbo->fetchAll("PreguntaAcceso", " IDPreguntaAcceso = '" . $detalle_respuesta["IDPreguntaAcceso"] . "' ", "array");
                $sql_otros = "INSERT INTO AccesosOtrosDatos(IDClub,IDLogAcceso,IDPreguntaAcceso,IDInvitacion,Tipo,Movimiento,Valor,FechaTrCr)
  												VALUES('" . $IDClub . "','" . $IDLogA . "','" . $detalle_respuesta["IDPreguntaAcceso"] . "','" . $IDInvitacion . "','" . $TipoInvitacion . "','Entrada','" . $detalle_respuesta["Valor"] . "','" . date("Y-m-d H:i:s") . "')";
                $dbo->query($sql_otros);
            }

            $RespuestasGeneral = str_replace("\\", "", $RespuestasGeneral);
            $datos_respuesta_gral = json_decode($RespuestasGeneral, true);
            foreach ($datos_respuesta_gral as $detalle_respuesta) {
                $sql_otros = "INSERT INTO AccesosEncuestaArbol(IDClub,IDEncuestaArbolAccesoOpcionesRespuesta,IDSocio,IDUsuario,IDPreguntaEncuestaArbol,FechaTrCr)
                      VALUES('" . $IDClub . "','" . $detalle_respuesta["ValorID"] . "','" . $detalle_respuesta["IDPreguntaAcceso"] . "','" . $IDInvitacion . "','" . $TipoInvitacion . "','Entrada','" . $detalle_respuesta["Valor"] . "','" . date("Y-m-d H:i:s") . "')";
                $dbo->query($sql_otros);
            }

            //echo $detalle_respuesta["IDPreguntaAcceso"].":".$detalle_respuesta["Valor"] . " --- ";

            $sql_inserta_historial = $dbo->query("Insert Into LogAccesoDiario (IDInvitacion, IDClub, Tipo, Entrada, Mecanismo, FechaIngreso,FechaTrCr, IDUsuario) Values ('" . $IDInvitacion . "','" . $IDClub . "', '" . $TipoInvitacion . "','S','" . $Mecanismo . "','" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "','" . $IDUsuario . "')");

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Ingresoregistradoconexito', LANG) . ":" . $mensaje_alerta_ingreso;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "7." .  SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function set_salida_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo = "", $IDUsuario = "", $OtrosCampos = "", $RespuestasGeneral = "", $IDLugar = "")
    {
        $dbo = &SIMDB::get();
        require_once LIBDIR . "SIMWebServiceCheck.inc.php";

        if (!empty($IDLugar)) :
            $IDInvitacion = $IDLugar;
        endif;

        //Guardo el Log de la busqueda
        $parametros = "INVITACION:" . $IDInvitacion . " TIPO: " . $TipoInvitacion . " Mecanismo: " . $Mecanismo;
        $sql_log_peticion = $dbo->query("Insert into LogAccesoPeticion (IDClub, IDUsuario, Parametro, Accion, FechaTrCr) Values ('" . $IDClub . "','" . $IDUsuario . "','" . $parametros . "','Salida','" . date("Y-m-d H:i:s") . "')");

        if (!empty($IDClub) && !empty($IDInvitacion) && !empty($TipoInvitacion)) {

            $PermitirMultipleAcceso = $dbo->getFields("ConfiguracionClub", "PermitirMultipleAcceso", "IDClub=$IDClub");
            // Validamos los accesos en el día

            $sql_log_acceso_ultimo = "Select Entrada,Salida From LogAcceso Where IDInvitacion = '" . $IDInvitacion . "' AND FechaTrCr = '" . date('Y-m-d') . "' Order by IDLogAcceso Desc Limit 1";
            $result_log_acceso_ultimo = $dbo->query($sql_log_acceso_ultimo);
            $row_log_acceso_ultimo = $dbo->fetchArray($result_log_acceso_ultimo);

            if ($PermitirMultipleAcceso == 'N') {
                if ($row_log_acceso_ultimo["Salida"] == "S") :
                    $respuesta["message"] = "I6:" . SIMUtil::get_traduccion('', '', 'NoSePermiteSalidaSinEntrada', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
            }

            switch ($TipoInvitacion) {
                case "InvitadoAcceso":
                    $sql_salida = $dbo->query("Update SocioInvitadoEspecial Set Salida = 'S', FechaSalida = '" . date("Y-m-d H:i:s") . "',IDUsuarioSalida = '" . $IDUsuario . "' Where IDSocioInvitadoEspecial = '" . $IDInvitacion . "'");
                    $IDInvitadoSal = $dbo->getFields("SocioInvitadoEspecial", "IDInvitado", "IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                    /*
                      $sql_registramov="SELECT IDSocioInvitadoEspecial FROM SocioInvitadoEspecial WHERE IDInvitado='".$IDInvitadoSal."' and IDClub = '".$IDClub."' ";
                      $r_resgitramov=$dbo->query($sql_registramov);
                      while($row_registramov=$dbo->fetchArray($r_resgitramov)){
                      $sql_inserta_historial_otro = "Insert Into LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('".$row_registramov["IDSocioInvitadoEspecial"]."','".$IDClub."', '".$TipoInvitacion."','S','".$Mecanismo."', NOW(),NOW(),'".$IDUsuario."')";
                      $dbo->query($sql_inserta_historial_otro);
                      }
                       */

                    //$sql_otras="Update SocioInvitadoEspecial Set Salida = 'S', FechaSalida = NOW(), IDUsuarioSalida = '".$IDUsuario."' Where IDInvitado='".$IDInvitadoSal."' and IDClub = '".$IDClub."' ";
                    //$dbo->query($sql_otras);
                    /*
                      $datos_invitacion_especial = $dbo->fetchAll( "SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array" );
                      if($datos_invitacion_especial["CabezaInvitacion"]=="S"):
                      $sql_ingreso_grupo = $dbo->query("Update SocioInvitadoEspecial Set Salida = 'S', FechaSalida = NOW() Where  IDPadre = '".$datos_invitacion_especial["IDInvitado"]."' and FechaInicio = '".$datos_invitacion_especial["FechaInicio"]."' and FechaFin = '".$datos_invitacion_especial["FechaFin"]."'");
                      endif;
                       */
                    if ($IDClub != "9") // Solo para mesa yeguas temporalmente no se registra
                    {
                        SIMUtil::push_socio_salida($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;
                case "Contratista":
                    $sql_salida = $dbo->query("Update SocioAutorizacion Set Salida = 'S', FechaSalida = '" . date("Y-m-d H:i:s") . "', IDUsuarioSalida = '" . $IDUsuario . "' Where IDSocioAutorizacion = '" . $IDInvitacion . "'");
                    $IDInvitadoSal = $dbo->getFields("SocioAutorizacion", "IDInvitado", "IDSocioAutorizacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                    /*
                      $sql_registramov="SELECT IDSocioAutorizacion FROM SocioAutorizacion WHERE IDInvitado='".$IDInvitadoSal."' and IDClub = '".$IDClub."' ";
                      $r_resgitramov=$dbo->query($sql_registramov);
                      while($row_registramov=$dbo->fetchArray($r_resgitramov)){
                      $sql_inserta_historial_otro = "Insert Into LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('".$row_registramov["IDSocioAutorizacion"]."','".$IDClub."', '".$TipoInvitacion."','S','".$Mecanismo."', NOW(),NOW(),'".$IDUsuario."')";
                      $dbo->query($sql_inserta_historial_otro);
                      }
                       */
                    //$sql_otras="Update SocioAutorizacion Set Salida = 'S', FechaSalida = NOW(), IDUsuarioSalida = '".$IDUsuario."' Where IDInvitado='".$IDInvitadoSal."' and IDClub = '".$IDClub."' ";
                    //$dbo->query($sql_otras);
                    //marcar las demas como salida

                    //Envio Notificacion Push
                    if ($IDClub != "9") // Solo para mesa yeguas temporalmente no se registra
                    {
                        SIMUtil::push_socio_salida($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;
                case "SocioInvitado":
                case "Invitado":
                    //Envio Notificacion Push
                    if ($IDClub != "9") // Solo para mesa yeguas temporalmente no se registra
                    {
                        SIMUtil::push_socio_salida($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario);
                    }

                    break;

                case "Usuario":

                    $accesoFuncionarios = $dbo->getFields("ConfiguracionClub", "AccesoFuncionarios", "IDClub = $IDClub");

                    if ($accesoFuncionarios == 'S') {

                        $rCheckinF = SIMWebServiceCheck::set_checkin_funcionarios($IDClub, $IDInvitacion, 'S');
                    }

                    break;
            }

            $OtrosCampos = str_replace("\\", "", $OtrosCampos);
            $datos_respuesta = json_decode($OtrosCampos, true);
            if (count($datos_respuesta) > 0) :
                $LimiteSuperado = "N";
                foreach ($datos_respuesta as $detalle_respuesta) :
                    if ($detalle_respuesta["IDPreguntaAcceso"] == 97) { //Guardo predio en campo aparte
                        $Predio = $detalle_respuesta["Valor"];
                    }
                    if ($detalle_respuesta["IDPreguntaAcceso"] == 98) { //Guardo habitacion en campo aparte
                        $Habitacion = $detalle_respuesta["Valor"];
                    }
                    $datos_pregunta = $dbo->fetchAll("PreguntaAcceso", " IDPreguntaAcceso = '" . $detalle_respuesta["IDPreguntaAcceso"] . "' ", "array");
                    if ($datos_pregunta["TipoCampo"] == "number" && $datos_pregunta["Limite"] > 0 && $detalle_respuesta["Valor"] > $datos_pregunta["Limite"]) {
                        //se envia notificacion de alerta al responsable
                        $datos_mensaje = $datos_pregunta["EtiquetaCampo"] . "=" . $detalle_respuesta["Valor"];
                        SIMUtil::notifica_alerta_acceso($IDClub, $IDInvitacion, $TipoInvitacion, $datos_mensaje);
                        $LimiteSuperado = "S";
                    }
                    $OtrosCamposAcceso .= "|" . $datos_pregunta["EtiquetaCampo"] . ":" . $detalle_respuesta["Valor"] . "|";
                //echo $detalle_respuesta["IDPreguntaAcceso"].":".$detalle_respuesta["Valor"] . " --- ";
                endforeach;
            endif;
            // Agregar registro de porteria para Mesa de Yeguas
            if ($IDClub == 9 && isset($_SESSION['Porteria'])) {
                $UsuarioPorteria = $dbo->getFields('Usuario', 'Nombre', "IDUsuario = $IDUsuario");
                $UsuarioTrCr = "$UsuarioPorteria|" . $_SESSION['Porteria'];
            }
            //Registro el historial de accesos
            $sql_inserta_historial = $dbo->query("Insert Into LogAcceso (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, UsuarioTrCr, FechaTrCr, IDUsuario,CamposAcceso,LimiteSuperado,Predio,Habitacion) Values ('" . $IDInvitacion . "','" . $IDClub . "', '" . $TipoInvitacion . "','S','" . $Mecanismo . "', '" . date("Y-m-d H:i:s") . "','" . $UsuarioTrCr . "','" . date("Y-m-d H:i:s") . "','" . $IDUsuario . "','" . $OtrosCamposAcceso . "','" . $LimiteSuperado . "','" . $Predio . "','" . $Habitacion . "')");
            $IDLogA = $dbo->lastID();

            foreach ($datos_respuesta as $detalle_respuesta) {
                $datos_pregunta = $dbo->fetchAll("PreguntaAcceso", " IDPreguntaAcceso = '" . $detalle_respuesta["IDPreguntaAcceso"] . "' ", "array");
                $sql_otros = "INSERT INTO AccesosOtrosDatos(IDClub,IDLogAcceso,IDPreguntaAcceso,IDInvitacion,Tipo,Movimiento,Valor,FechaTrCr)
  												VALUES('" . $IDClub . "','" . $IDLogA . "','" . $detalle_respuesta["IDPreguntaAcceso"] . "','" . $IDInvitacion . "','" . $TipoInvitacion . "','Salida','" . $detalle_respuesta["Valor"] . "','" . date("Y-m-d H:i:s") . "')";
                $dbo->query($sql_otros);
            }



            $RespuestasGeneral = str_replace("\\", "", $RespuestasGeneral);
            $datos_respuesta_gral = json_decode($RespuestasGeneral, true);
            foreach ($datos_respuesta_gral as $detalle_respuesta) {
                $sql_otros = "INSERT INTO AccesosEncuestaArbol(IDClub,IDEncuestaArbolAccesoOpcionesRespuesta,IDSocio,IDUsuario,IDPreguntaEncuestaArbol,FechaTrCr)
                      VALUES('" . $IDClub . "','" . $detalle_respuesta["ValorID"] . "','" . $detalle_respuesta["IDPreguntaAcceso"] . "','" . $IDInvitacion . "','" . $TipoInvitacion . "','Salida','" . $detalle_respuesta["Valor"] . "','" . date("Y-m-d H:i:s") . "')";
                $dbo->query($sql_otros);
            }



            $sql_inserta_historial = $dbo->query("Insert Into LogAccesoDiario (IDInvitacion, IDClub, Tipo, Salida, Mecanismo, FechaSalida, FechaTrCr, IDUsuario) Values ('" . $IDInvitacion . "','" . $IDClub . "', '" . $TipoInvitacion . "','S','" . $Mecanismo . "', '" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "','" . $IDUsuario . "')");

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Salidaregistradaconexito', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "7." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_objetos_sugeridos_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $response = array();


        switch ($TipoInvitacion) {
            case "InvitadoAcceso":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioInvitadoEspecial", "IDInvitado", "IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                break;
            case "Contratista":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioAutorizacion", "IDInvitado", "IDSocioAutorizacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                //Envio Notificacion Push
                break;
            case "SocioInvitado":
            case "Invitado":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioInvitado", "IDInvitado", "IDSocioInvitado = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                break;
            case "Socio":
                $Campo = "IDSocio";
                $IDInvitado = $IDInvitacion;
                break;
        }



        $sql = "SELECT * FROM AccesoObjeto  WHERE " . $Campo . " = '" . $IDInvitado . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($row_obj = $dbo->fetchArray($qry)) {
                $dato_obj["IDObjeto"] = $row_obj["IDAccesoObjeto"];
                $dato_obj["Campo1"] = $row_obj["Campo1"];
                $dato_obj["Campo2"] =  $row_obj["Campo2"];
                $dato_obj["IDTipo"] = $row_obj["IDTipoObjeto"];
                $dato_obj["Tipo"] =  $dbo->getFields("TipoObjeto", "Nombre", "IDTipoObjeto = '" . $row_obj["IDTipoObjeto"] . "'");
                array_push($response, $dato_obj);
            }
        } //ednw hile
        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;
        return $respuesta;
    }

    public function set_entrada_objetos_existentes_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario, $IDObjetos)
    {
        $dbo = &SIMDB::get();
        switch ($TipoInvitacion) {
            case "InvitadoAcceso":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioInvitadoEspecial", "IDInvitado", "IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                break;
            case "Contratista":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioAutorizacion", "IDInvitado", "IDSocioAutorizacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                //Envio Notificacion Push
                break;
            case "SocioInvitado":
            case "Invitado":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioInvitado", "IDInvitado", "IDSocioInvitado = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                break;
            case "Socio":
                $Campo = "IDSocio";
                $IDInvitado = $IDInvitacion;
                break;
        }


        $array_id_obj = explode(",", $IDObjetos);
        foreach ($array_id_obj as $id_obj) {
            $sql_ingreso_obj = "INSERT INTO LogAccesoObjeto (IDClub,IDAccesoObjeto,Entrada,FechaIngreso,IDUsuario)
                            VALUES('" . $IDClub . "','" . $id_obj . "','S','" . date("Y-m-d H:i:s") . "','" . $IDUsuario . "')";
            $dbo->query($sql_ingreso_obj);
        }

        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
        $respuesta["success"] = true;
        $respuesta["response"] = $response;
        return $respuesta;
    }

    public function set_entrada_objeto_nuevo_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario, $Campo1, $Campo2, $IDTipo)
    {
        $dbo = &SIMDB::get();

        switch ($TipoInvitacion) {
            case "InvitadoAcceso":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioInvitadoEspecial", "IDInvitado", "IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                break;
            case "Contratista":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioAutorizacion", "IDInvitado", "IDSocioAutorizacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                //Envio Notificacion Push
                break;
            case "SocioInvitado":
            case "Invitado":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioInvitado", "IDInvitado", "IDSocioInvitado = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                break;
            case "Socio":
                $Campo = "IDSocio";
                $IDInvitado = $IDInvitacion;
                break;
        }

        $sql_obj = "INSERT INTO AccesoObjeto (IDClub," . $Campo . ",IDTipoObjeto,Campo1,Campo2,UsuarioTrCr,FechaTrCr)
                  VALUES('" . $IDClub . "','" . $IDInvitado . "','" . $IDTipo . "','" . $Campo1 . "','" . $Campo2 . "','" . $IDUsuario . "','" . date("Y-m-d H:i:s") . "')";
        $dbo->query($sql_obj);
        $id_obj = $dbo->lastID();

        $sql_ingreso_obj = "INSERT INTO LogAccesoObjeto (IDClub,IDAccesoObjeto,Entrada,FechaIngreso,IDUsuario)
                          VALUES('" . $IDClub . "','" . $id_obj . "','S','" . date("Y-m-d H:i:s") . "','" . $IDUsuario . "')";
        $dbo->query($sql_ingreso_obj);

        $response_obj_nuc = array();
        $dato_obj["IDObjeto"] = $id_obj;
        $dato_obj["Campo1"] = $Campo1;
        $dato_obj["Campo2"] =  $Campo2;
        $dato_obj["IDTipo"] = $IDTipo;
        $dato_obj["Tipo"] =  $dbo->getFields("TipoObjeto", "Nombre", "IDTipoObjeto = '" . $IDTipo . "'");
        array_push($response_obj_nuc, $dato_obj);
        $respuesta["message"] = "ok";
        $respuesta["success"] = true;
        $respuesta["response"] = $response_obj_nuc;
        return $respuesta;
    }

    public function set_salida_objeto($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        switch ($TipoInvitacion) {
            case "InvitadoAcceso":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioInvitadoEspecial", "IDInvitado", "IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                break;
            case "Contratista":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioAutorizacion", "IDInvitado", "IDSocioAutorizacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                //Envio Notificacion Push
                break;
            case "SocioInvitado":
            case "Invitado":
                $Campo = "IDInvitado";
                $IDInvitado = $dbo->getFields("SocioInvitado", "IDInvitado", "IDSocioInvitado = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' Limit 1");
                break;
            case "Socio":
                $Campo = "IDSocio";
                $IDInvitado = $IDInvitacion;
                break;
        }


        $array_id_obj = explode(",", $IDObjetos);
        foreach ($array_id_obj as $id_obj) {
            $sql_ingreso_obj = "INSERT INTO LogAccesoObjeto (IDClub,IDAccesoObjeto,Salida,FechaSalida,IDUsuario)
                            VALUES('" . $IDClub . "','" . $id_obj . "','S','" . date("Y-m-d H:i:s") . "','" . $IDUsuario . "')";
            $dbo->query($sql_ingreso_obj);
        }

        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
        $respuesta["success"] = true;
        $respuesta["response"] = $response;
        return $respuesta;
    }

    public function get_historial_accesos_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $IDUsuario)
    {

        $dbo = &SIMDB::get();

        $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
        $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";

        //Consulto el historial de ingresos y salidas del dia
        $response_historial_acceso = array();
        $qry_acceso = "SELECT Tipo,Salida,FechaSalida,Entrada,FechaIngreso,Mecanismo,CamposAcceso,LimiteSuperado,Predio,Habitacion From LogAcceso Where IDInvitacion = '" . $IDInvitacion . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "' Order By IDLogAcceso DESC LIMIT 100";
        $sql_historial = $dbo->query($qry_acceso);
        while ($row_historial = $dbo->fetchArray($sql_historial)) :

            $dato_historial["Tipo"] = $row_historial["Tipo"];
            $dato_historial["Salida"] = $row_historial["Salida"];
            $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
            $dato_historial["Entrada"] = $row_historial["Entrada"];
            $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];

            $datos_campos = "";
            //Consulto los otros datos registrados
            $array_otros_datos = explode("|", $row_historial["CamposAcceso"]);
            foreach ($array_otros_datos as $valor) {
                if (!empty($valor)) {
                    if (!in_array($valor, $array_respuesta)) {
                        $datos_campos .= $valor . "<br>";
                    }
                    $array_respuesta[] = $valor;
                }
            }
            $dato_historial["OtrosDatos"] = $datos_campos;
            $dato_historial["LimiteSuperado"] = $row_historial["LimiteSuperado"];

            array_push($response_historial_acceso, $dato_historial);
        endwhile;
        $datos_invitacion_individual["Historial"] = $response_historial_acceso;

        $respuesta["message"] = "ok";
        $respuesta["success"] = true;
        $respuesta["response"] = $response_historial_acceso;
        return $respuesta;
    }

    public  function ingreso_salida_usuario($IDClub, $Documento, $Movimiento)
    {
        $dbo = &SIMDB::get();
        if (!empty($Documento) && !empty($Movimiento)) {

            //Verifico si tiene alguna invitación
            $sql_inv = "SELECT IDSocio FROM Socio WHERE IDClub = '" . $IDClub . "' and (NumeroDocumento = '" . $Documento . "' or Accion = '" . $Documento . "') and IDEstadoSocio = '1' LIMIT 1";
            $r_inv = $dbo->query($sql_inv);
            $row_inv = $dbo->fetchArray($r_inv);
            if ((int)$row_inv["IDSocio"] > 0) {
                $IDInvitacion = $row_inv["IDSocio"];
                $TipoInvitacion = "Socio";
            } else {
                $datos_invitado = $dbo->fetchAll("Invitado", " IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $Documento . "' LIMIT 1  ", "array");
                $sql_inv = "SELECT IDSocioInvitadoEspecial FROM SocioInvitadoEspecial WHERE IDClub = '" . $IDClub . "' and IDInvitado = '" . $datos_invitado["IDInvitado"] . "' ORDER IDSocioInvitadoEspecial BY  DESC LIMIT 1";
                $r_inv = $dbo->query($sql_inv);
                $row_inv = $dbo->fetchArray($r_inv);
                if ((int)$row_inv["IDSocioInvitadoEspecial"] > 0) {
                    $IDInvitacion = $row_inv["IDSocioInvitadoEspecial"];
                    $TipoInvitacion = "InvitadoAcceso";
                } else {
                    $datos_invitado = $dbo->fetchAll("Invitado", " IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $Documento . "' LIMIT 1  ", "array");
                    $sql_inv = "SELECT IDSocioAutorizacion FROM SocioAutorizacion WHERE IDClub = '" . $IDClub . "' and IDInvitado = '" . $datos_invitado["IDInvitado"] . "' and Mostrar != 'N' ORDER BY IDSocioAutorizacion DESC LIMIT 1";
                    $r_inv = $dbo->query($sql_inv);
                    $row_inv = $dbo->fetchArray($r_inv);
                    if ((int)$row_inv["IDSocioAutorizacion"] > 0) {
                        $IDInvitacion = $row_inv["IDSocioAutorizacion"];
                        $TipoInvitacion = "Contratista";
                    } else {
                        $sql_inv = "SELECT IDSocioInvitado FROM SocioInvitado WHERE IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $Documento . "' ORDER BY IDSocioInvitado DESC LIMIT 1";
                        $r_inv = $dbo->query($sql_inv);
                        $row_inv = $dbo->fetchArray($r_inv);
                        if ((int)$row_inv["IDSocioInvitado"] > 0) {
                            $IDInvitacion = $row_inv["IDSocioInvitado"];
                            $TipoInvitacion = "SocioInvitado";
                        } else {
                            //Verifico si es empleado
                            $sql_emp = "SELECT IDUsuario FROM Usuario WHERE IDClub = '" . $IDClub . "' and NumeroDocumento = '" . $Documento . "' ORDER BY IDUsuario DESC LIMIT 1";
                            $r_emp = $dbo->query($sql_emp);
                            $row_emp = $dbo->fetchArray($r_emp);
                            if ((int)$row_emp["IDUsuario"] > 0) {
                                $IDInvitacion = $row_emp["IDUsuario"];
                                $TipoInvitacion = "Empleado";
                            } else {
                                $respuesta["message"] = "I2." . SIMUtil::get_traduccion('', '', 'Eldocumentoenviadonotieneautorizaciondeingreso', LANG);
                                $respuesta["success"] = false;
                                $respuesta["response"] = "";
                            }
                        }
                    }
                }
            }

            $Mecanismo = "WS";
            $IDUsuario = "WS";
            if ($IDInvitacion > 0 && !empty($TipoInvitacion)) {
                if ($Movimiento == "Entrada") {
                    $respuesta = SIMWebserviceAccesos::set_entrada_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo, $IDUsuario, $OtrosCampos);
                } elseif ($Movimiento == "Entrada-Salida") {
                    //Cuando viene asi no se registra moivimiento de ingreso/salida
                    $respuesta["message"] = "I33 Lectura exitosa";
                    $respuesta["success"] = true;
                    $respuesta["response"] = "";
                } else {

                    $respuesta = SIMWebserviceAccesos::set_salida_invitado($IDClub, $IDInvitacion, $TipoInvitacion, $Mecanismo, $IDUsuario, $OtrosCampos);
                }
            }
        } else {
            $respuesta["message"] = "I1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        }
        return $respuesta;
    }

    public function set_autorizacion_invitado($IDClub, $IDSocio, $FechaIngreso, $FechaSalida, $DatosInvitado, $IDUsuario = "", $ValoresFormulario = "", $Masivo = "", $DiasCheckbox = "", $IDServicio = "")
    {

        $dbo = &SIMDB::get();
        $ConfiguracionClub = $dbo->fetchAll('ConfiguracionClub', "IDClub = $IDClub", "array");
        if ($IDClub == 1600 && (date('w', strtotime($FechaIngreso)) == 0 || date('w', strtotime($FechaIngreso)) == 6 || date('w', strtotime($FechaSalida)) == 0 || date('w', strtotime($FechaSalida)) == 6)) {
            $respuesta["message"] = "Lo sentimos, no es posible invitar los fines de semana";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if ($Masivo != 'S' && ($IDClub == 7 || $IDClub == 44) && (date('w', strtotime($FechaIngreso)) == 0 || date('w', strtotime($FechaIngreso)) == 6 || date('w', strtotime($FechaIngreso)) == 100)) {
            $respuesta["message"] = "Lo sentimos, no es posible invitar en el dia seleccionado";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }
        $arr_DatosInvitado = json_decode($DatosInvitado);

        if ($IDClub == 81) {
            // if ($IDClub == 8) {
            if ($arr_DatosInvitado[0]->TipoInvitado == "Contratista") {
                $datos_formulario = json_decode($ValoresFormulario, true);
                if (count($datos_formulario) > 0) :
                    foreach ($datos_formulario as $detalle_datos) :
                        if ($detalle_datos['IDCampoFormularioInvitado'] == 18) {
                            if (empty($detalle_datos['Valor']) || $detalle_datos['Valor'] == '') {
                                $respuesta["message"] = "El campo Observaciones es obligatorio";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            }
                        }
                    endforeach;
                endif;
            }
        }

        // Validación Tarjetas Rotativas Club Villa Peru -- Invitados v2
        // if ($IDClub == 81) {
        if ($IDClub == 8) {
            $datos_formulario = json_decode($ValoresFormulario, true);
            if (count($datos_formulario) > 0) :
                foreach ($datos_formulario as $detalle_datos) :
                    $IdentificadorPregunta = $dbo->getFields("CampoFormularioInvitado", "IdentificadorPregunta", "IDCampoFormularioInvitado = {$detalle_datos['IDCampoFormularioInvitado']}");
                    $IdentificadorPreguntaReserva = $dbo->getFields("CampoInvitadoExterno", "IdentificadorPregunta", "IDCampoInvitadoExterno = {$detalle_datos['IDCampoInvitadoExterno']}");

                    if ($IdentificadorPregunta == 'PTR' || $IdentificadorPreguntaReserva == 'PTR') {

                        if (empty($detalle_datos['Valor']) || $detalle_datos['Valor'] == '') {
                            $respuesta["message"] = "El campo Metodo Invitación es obligatorio";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        } else {
                            $date = date('Y-m-d');
                            $sql_TarjetasRotativas = "SELECT IDTarjetaRotativa FROM TarjetaRotativa t,TipoTarjetaRotativa tt WHERE t.IDTipoTarjetaRotativa=tt.IDTipoTarjetaRotativa AND t.IDSocio = $IDSocio AND t.FechaCaducidad > '{$date}' AND tt.Nombre = '{$detalle_datos['Valor']}' AND t.Cupos > 0 ORDER BY t.Cupos ASC LIMIT 1";

                            $q_TarjetasRotativas = $dbo->query($sql_TarjetasRotativas);
                            $rows_TarjetasRotativas = $dbo->rows($q_TarjetasRotativas);
                            if ($rows_TarjetasRotativas <= 0) {
                                $respuesta["message"] = "Lo sentimos, no posee cupos suficientes";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            } else {
                                $arr_TarjetaRotativa = $dbo->assoc($q_TarjetasRotativas);
                                $IDTarjetaRotativa = $arr_TarjetaRotativa['IDTarjetaRotativa'];
                            }
                        }
                    }
                endforeach;
            endif;
        }
        //Fin Validación Tarjetas Rotaticas Club Villa Peru -- Invitados V2

        // Validar invitaciones por Accion padre
        if ($ConfiguracionClub['LimitarInvitadosPorAccion'] == 'S') {
            $MaxInvitadosDiaPorAccion = $ConfiguracionClub['MaxInvitadosDiaPorAccion'];

            $q_datos_socio = $dbo->query("SELECT TipoSocio,Accion,AccionPadre FROM Socio WHERE IDSocio = $IDSocio");
            $datos_socio = $dbo->assoc($q_datos_socio);

            if (!empty($datos_socio['AccionPadre'])) {
                $whereAccion = " AND (s.Accion=" . $datos_socio['AccionPadre'] . " OR s.AccionPadre=" . $datos_socio['AccionPadre'] . ") ";
            } else {
                $whereAccion = " AND (s.AccionPadre=" . $datos_socio['Accion'] . " OR s.Accion=" . $datos_socio['Accion'] . ") ";
            }

            $q_InvitadosPorAccion = $dbo->query("SELECT COUNT(IDSocioInvitadoEspecial) as CantidadInvitados from SocioInvitadoEspecial si,Socio s WHERE si.IDSocio = s.IDSocio $whereAccion   and si.FechaInicio = '" . $FechaIngreso . "' ");
            $InvitadosPorAccion = $dbo->assoc($q_InvitadosPorAccion);
            if ($InvitadosPorAccion['CantidadInvitados'] >= $MaxInvitadosDiaPorAccion) {
                $respuesta["message"] = "Lo sentimos, ha superado el limite de $MaxInvitadosDiaPorAccion invitados por Acción por día";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        //FIN Validar invitaciones por Accion padre

        // Validación Datos invitados para Cantegril
        if ($IDClub == 185) {
            // if ($IDClub == 8) {
            if ($arr_DatosInvitado[0]->Nombre == '' || $arr_DatosInvitado[0]->Apellido == '') {
                $respuesta["message"] = "Lo sentimos, el Nombre y Apellido del Invitado son obligatorios";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            if ($arr_DatosInvitado[0]->NumeroDocumento == '') {
                $respuesta["message"] = "Lo sentimos, el Número de documento del Invitado es obligatorio";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            } elseif (strlen($arr_DatosInvitado[0]->NumeroDocumento) < 7) {
                $respuesta["message"] = "Lo sentimos, el Número de documento debe contener al menos 7 dígitos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            if ($arr_DatosInvitado[0]->Email == '') {
                $respuesta["message"] = "Lo sentimos, el Email del Invitado es obligatorio";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            } elseif (!filter_var($arr_DatosInvitado[0]->Email, FILTER_VALIDATE_EMAIL)) {
                $respuesta["message"] = "Lo sentimos, el Email no cumple con el formato requerido: 'ejemplo@email.com'";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        //Fin Validación Datos invitados para Cantegril

        //Limitar ingresos de invitados por club Barquisimeto
        if ($IDClub == 219) {

            // // Buscar cuantos invitados tiene el Socio en e
            // $sql_InvitadosPermanentes = "SELECT COUNT(IDSocioInvitadoEspecial) as Invitados FROM SocioInvitadoEspecial WHERE IDSocio = $IDSocio AND TipoInvitacion = 'Invitado Permanente' AND (FechaInicio>='" . date('Y-m-01') . "' AND FechaInicio <= '" . date('Y-m-t') . "') AND FechaFin >= '" . date('Y-m-d') . "'";
            // $q_sql_permanente = $dbo->query($sql_InvitadosPermanentes);
            // $rowsInvitadosPermanentes = $dbo->assoc($q_sql_permanente);
            // if ($rowsInvitadosPermanentes['Invitados'] >= $TipoInvitadoPermanente['MaxInvitadosMes']) {
            //     $respuesta["message"] = "Lo sentimos, solo puede invitar a " . $TipoInvitadoPermanente['MaxInvitadosMes'] . " Invitados Permanentes";
            //     $respuesta["success"] = false;
            //     $respuesta["response"] = null;
            //     return $respuesta;
            // }
            //         //Fin Buscar cuantos invitados permanentes tiene el Socio en el mes
        }
        //Fin Limitar ingresos de invitados por club Barquisimeto


        // Reglas invitaciones club golf de uruguay -> 125
        if ($IDClub == 125) {

            $ReglasGolfUruguay = GlobalSIMWebServiceAccesos::Reglas_invitados_ClubGolfUruguay($IDClub, $IDSocio, $FechaIngreso, $FechaSalida, $DatosInvitado, $ValoresFormulario, $IDServicio);
            if ($ReglasGolfUruguay["success"] == false) {

                $respuesta["message"] = $ReglasGolfUruguay["message"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        // Fin Reglas invitaciones club golf de uruguay

        // Filtrar por tipo de invitado si esta activa la opción
        $FiltrarTipoInvitado = $ConfiguracionClub['FiltrarTipoInvitado'];
        if ($FiltrarTipoInvitado == 'S') {
            $TipoSocio = $dbo->getFields('Socio', 'TipoSocio', "IDSocio = $IDSocio");
            // Validación para el club Karibao
            $arr_TipoSocioKaribao = array();
            // if ($IDClub == 8) {
            if ($IDClub == 206) {
                $arr_TipoSocioKaribao = array(
                    "001-2 Owner Ejecutivo 1",
                    "002-1 Owner Ejecutivo 2"
                );
                if (in_array($TipoSocio, $arr_TipoSocioKaribao)) {
                    $arr_DatosInvitado = json_decode($DatosInvitado);
                    if ($arr_DatosInvitado[0]->TipoInvitado == "Invitado Permanente") {
                        $respuesta["message"] = "Lo sentimos, su tipo de usuario no le permite registrar al invitado con la opción: 'Invitado Permanente";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }
                if ($arr_DatosInvitado[0]->TipoInvitado == "Invitado Permanente") {
                    $TipoInvitadoPermanente = $dbo->fetchAll("TipoInvitado", "Nombre='Invitado Permanente'", "array");

                    // Buscar cuantos invitados permanentes tiene el Socio en el mes
                    $sql_InvitadosPermanentes = "SELECT COUNT(IDSocioInvitadoEspecial) as Invitados FROM SocioInvitadoEspecial WHERE IDSocio = $IDSocio AND TipoInvitacion = 'Invitado Permanente' AND (FechaInicio>='" . date('Y-m-01') . "' AND FechaInicio <= '" . date('Y-m-t') . "') AND FechaFin >= '" . date('Y-m-d') . "'";
                    $q_sql_permanente = $dbo->query($sql_InvitadosPermanentes);
                    $rowsInvitadosPermanentes = $dbo->assoc($q_sql_permanente);
                    if ($rowsInvitadosPermanentes['Invitados'] >= $TipoInvitadoPermanente['MaxInvitadosMes']) {
                        $respuesta["message"] = "Lo sentimos, solo puede invitar a " . $TipoInvitadoPermanente['MaxInvitadosMes'] . " Invitados Permanentes";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    //Fin Buscar cuantos invitados permanentes tiene el Socio en el mes
                }

                if ($arr_DatosInvitado[0]->TipoInvitado == "Invitado Diario") {
                    $TipoInvitadoDiario = $dbo->fetchAll("TipoInvitado", "Nombre='Invitado Diario'", "array");

                    //Buscar cuantos invitados diarios tiene el Socio en el mes
                    $sql_InvitadosDiarios = "SELECT COUNT(IDSocioInvitadoEspecial) as Invitados FROM SocioInvitadoEspecial WHERE IDSocio = $IDSocio AND TipoInvitacion = 'Invitado Diario' AND (FechaInicio>='" . date('Y-m-01') . "' AND FechaInicio <= '" . date('Y-m-t') . "') AND FechaFin >= '" . date('Y-m-d') . "'";
                    $q_sql_diario = $dbo->query($sql_InvitadosDiarios);
                    $rowsInvitadosDiarios = $dbo->assoc($q_sql_diario);
                    if ($rowsInvitadosDiarios['Invitados'] >= $TipoInvitadoDiario['MaxInvitadosMes']) {
                        $respuesta["message"] = "Lo sentimos, solo puede invitar a " . $TipoInvitadoDiario['MaxInvitadosMes'] . " Invitados Diarios por mes";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    //Fin Buscar cuantos invitados diarios tiene el Socio en el mes

                    //Buscar cuantos invitados diarios tiene el Socio en el día
                    $sql_InvitadosDiarios = "SELECT COUNT(IDSocioInvitadoEspecial) as Invitados FROM SocioInvitadoEspecial si,Invitado i WHERE si.IDInvitado=i.IDInvitado AND IDSocio = $IDSocio AND TipoInvitacion = 'Invitado Diario' AND (FechaInicio>='" . date('Y-m-d') . "' AND FechaInicio <= '" . date('Y-m-d') . "') AND FechaFin >= '" . date('Y-m-d') . "' AND NumeroDocumento = " . $arr_DatosInvitado[0]->NumeroDocumento;
                    $q_sql_diario = $dbo->query($sql_InvitadosDiarios);
                    $rowsInvitadosDiarios = $dbo->assoc($q_sql_diario);
                    if ($rowsInvitadosDiarios['Invitados'] >= $TipoInvitadoDiario['InvitacionesMesPorInvitado']) {
                        $respuesta["message"] = "Lo sentimos, solo puede invitar a la misma persona " . $TipoInvitadoDiario['InvitacionesMesPorInvitado'] . " vez por mes";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    //Fin Buscar cuantos invitados diarios tiene el Socio en el día
                }
            }
            // Validación para el club Karibao fin
        }
        // Filtrar por tipo de invitado si esta activa la opción

        /* if($IDClub == 18 && empty(trim($DiasCheckbox))):
            $respuesta["message"] = "Los días son obligatorios";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif; */

        $datos_invitado = json_decode($DatosInvitado, true);
        $validaDocumento = str_replace(SIMResources::$abecedario, 1,  $datos_invitado[0]["NumeroDocumento"]);
        if ((int)$validaDocumento == 0) {
            $respuesta["message"] = "El numero de documento del invitado no es valido";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {
            if (!empty($FechaIngreso) && !empty($FechaSalida) && count($datos_invitado) > 0) {

                $bloqueado = $dbo->fetchAll("ListaNegraApp", "NumeroDocumento = '$NumeroDocumento' AND IDClub = '$IDClub' LIMIT 1", "array");
                if (empty($bloqueado[IDListaNegraApp])) {

                    $hoy = date("Y-m-d");

                    if (strtotime($FechaIngreso) >= strtotime($hoy) && strtotime($FechaSalida) >= strtotime($FechaIngreso)) {

                        //verifico que el socio exista y pertenezca al club
                        $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                        if (!empty($id_socio)) {

                            // Consulto las invitaciones que puede hacer el socio
                            $numero_invitados_dia_permitido = $dbo->getFields("Socio", "NumeroAccesos", "IDSocio = '" . $IDSocio . "'");

                            if ((int) $numero_invitados_dia_permitido > 0) {

                                //Consulto cuantas veces la persona ha sido invitada en el mes
                                $mes_invitacion = substr($FechaIngreso, 5, 2);
                                $year_invitacion = substr($FechaIngreso, 0, 4);
                                $dia_invitacion = substr($FechaIngreso, 8, 2);

                                $sql_invitados_dia_soc = $dbo->query("Select * From SocioInvitadoEspecial Where (IDSocio = '" . $IDSocio . "') and YEAR(FechaInicio) = '" . $year_invitacion . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and DAY(FechaInicio) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                                $numero_invitaciones_soc = $dbo->rows($sql_invitados_dia_soc);
                                if (($numero_invitaciones_soc) >= $numero_invitados_dia_permitido) :
                                    $respuesta["message"] = "Lo sentimos, supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia permitido";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;

                                //Recorro los datos de los invitados
                                if (count($datos_invitado) > 0) :

                                    //genero el codigo de autorizacion
                                    $CodigoAutorizacion = SIMUtil::genera_codigo_autorizacion("I");

                                    foreach ($datos_invitado as $detalle_datos) :
                                        $IDTipoDocumento = $detalle_datos["IDTipoDocumento"];
                                        $NumeroDocumento = $detalle_datos["NumeroDocumento"];
                                        $Nombre = $detalle_datos["Nombre"];
                                        $Apellido = $detalle_datos["Apellido"];
                                        $Email = $detalle_datos["Email"];
                                        $TipoInvitado = $detalle_datos["TipoInvitado"];
                                        $Placa = $detalle_datos["Placa"];
                                        $CabezaInvitacion = $detalle_datos["CabezaInvitacion"];
                                        $MenorEdad = $detalle_datos["MenorEdad"];
                                        $TipoSangre = $detalle_datos["TipoSangre"];
                                        $Predio = $detalle_datos["Predio"];

                                        $NumeroDocumento = str_replace(".", "", $NumeroDocumento);
                                        $NumeroDocumento = str_replace(",", "", $NumeroDocumento);

                                        /*
                                        if($IDClub==9 && (empty($Email) || !filter_var($Email, FILTER_VALIDATE_EMAIL))) {
                                        $respuesta[ "message" ] = "El Email del invitado es obligatorio, por favor verifique";
                                        $respuesta[ "success" ] = false;
                                        $respuesta[ "response" ] = NULL;
                                        return $respuesta;
                                        }
                                            */

                                        if (empty($Email)) {
                                            $Email = "sin mail";
                                        }

                                        if (empty($Placa)) {
                                            $Placa = "sin placa";
                                        }

                                        $bloque_administrativo = SIMWebServiceAccesos::verifica_bloqueo_invitado($NumeroDocumento, $IDClub);
                                        if ($bloque_administrativo == "S") :
                                            $respuesta["message"] = "Lo sentimos el invitado tiene un bloqueo por parte del club, no es posible realizar la invitacion";
                                            $respuesta["success"] = false;
                                            $respuesta["response"] = null;
                                            return $respuesta;
                                        endif;

                                        if ($MenorEdad == "S" || (empty($IDTipoDocumento) && empty($NumeroDocumento) && empty($Email))) :
                                            $NumeroDocumento = "MenorEdad" . $IDClub . rand(1, 1000000000);
                                            $IDTipoDocumento = 1;
                                        else :
                                            if ($IDTipoDocumento == "4") {
                                                $NumeroDocumento =  $NumeroDocumento;
                                            } elseif ($IDTipoDocumento = "3") {
                                                $NumeroDocumento = $NumeroDocumento;
                                            } else {
                                                // $NumeroDocumento = (int) $NumeroDocumento;
                                                $NumeroDocumento = $NumeroDocumento;
                                            }
                                        endif;

                                        //verifico si el invitado ya esta creado
                                        $id_invitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' ");

                                        if (!empty($IDUsuario)) {
                                            $UsuarioCrea = $IDUsuario . " " . $dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDUsuario . "'");
                                        } else {
                                            $UsuarioCrea = "Socio";
                                        }

                                        //Para MY el estado debe ser bloqueado para que puedan completar informacion
                                        /*
                                        if($IDClub==9)
                                        $IDEstadoInvitado = 3;
                                        else
                                        $IDEstadoInvitado = 1;
                                            */

                                        $IDEstadoInvitado = 1;

                                        //Si el invitado no existe en la tabla maestra lo creo
                                        if (empty($id_invitado) || (int) $id_invitado == 0) :
                                            $inserta_invitado = "INSERT INTO Invitado (Predio,TipoSangre,IDCLub, IDTipoDocumento, NumeroDocumento, Nombre, Apellido, Email, MenorEdad, IDEstadoInvitado, UsuarioTrCr, FechaTrCr)
                                                                        Values('$Predio','$TipoSangre','$IDClub', '$IDTipoDocumento','$NumeroDocumento','" . strtoupper($Nombre) . "','" . strtoupper($Apellido) . "','" . $Email . "','" . $MenorEdad . "','" . $IDEstadoInvitado . "','" . $UsuarioCrea . "','" . date("Y-m-d H:i:s") . "')";
                                            $dbo->query($inserta_invitado);
                                            $id_invitado = $dbo->lastID();
                                        else :

                                            // Si ya existe actualizo los datos basicos
                                            $sql_actualizao_datos_invitado = "UPDATE Invitado Set Predio = '$Predio', TipoSangre = '$TipoSangre', Nombre='" . strtoupper($Nombre) . "',Apellido='" . strtoupper($Apellido) . "', Email = '$Email' Where IDInvitado = '" . $id_invitado . "'";
                                            $dbo->query($sql_actualizao_datos_invitado);
                                        endif;

                                        //Si es cabeza de familia guardo el id del Socio
                                        if ($CabezaInvitacion == "S") :
                                            $IDPadre = $id_invitado;
                                        endif;

                                        // Validamos las reglas de invitados de los invitados especiales
                                        $valida_regla = SIMWebServiceAccesos::valida_regla_invitacion_acceso($IDClub, $IDSocio, $id_invitado, $FechaIngreso);

                                        if ($valida_regla['success'] != true) {
                                            $respuesta["message"] = $valida_regla['message'];
                                            $respuesta["success"] = false;
                                            $respuesta["response"] = null;
                                            return $respuesta;
                                        }

                                        $numero_invitaciones = 0;
                                        $numero_invitados_mes = 0;

                                        $numero_invitados_mes_permitido = 500;
                                        $numero_mismo_invitado_mes = "100";
                                        $cumplimiento_obligatorio_limite = "S";

                                        // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
                                        //$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );

                                        //if ((int)$numero_invitados_dia<(int)$numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite=="N"){
                                        //if ((int)$numero_invitaciones<(int)$numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite=="N"){
                                        //if ((int)$numero_invitados_mes_permitido>(int)$numero_invitados_mes || $cumplimiento_obligatorio_limite=="N"){
                                        //Verifico que el invitado no este invitado mas de una vez el mismo dia

                                        //$sql_invitacion_dia = $dbo->query("Select * From SocioInvitadoEspecial Where IDInvitado = '".$id_invitado."' and FechaInicio = '".$FechaIngreso."'");
                                        //$numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
                                        $numero_invitaciones_dia = 0;
                                        //if((int)$numero_invitaciones_dia<=100){

                                        //Inserto invitacion
                                        $sqlInivtacion = "INSERT INTO SocioInvitadoEspecial (IDClub, IDSocio, IDInvitado, IDPadre, IDPadreInvitacion, IDVehiculo, CodigoAutorizacion, CabezaInvitacion,  TipoInvitacion, FechaInicio, FechaFin, Dias, UsuarioTrCr, FechaTrCr)
                                        Values ('$IDClub','$IDSocio', '$id_invitado', '$IDPadre','$IDInvitacionGenerada', '$id_vehiculo', '$CodigoAutorizacion','$CabezaInvitacion',
                                                '$TipoInvitado', '$FechaIngreso', '$FechaSalida', '$DiasCheckbox','$UsuarioCrea','" . date("Y-m-d H:i:s") . "')";
                                        $sql_inserta_inv = $dbo->query($sqlInivtacion);
                                        $id_invitado_inserta = $dbo->lastID();
                                        $nombre_enviar = $Nombre . " " . $Apellido;
                                        // Restar cupos de tarjetas rotativas
                                        if ($sql_inserta_inv != false && isset($IDTarjetaRotativa)) {
                                            $CupoActual = $dbo->getFields("TarjetaRotativa", "Cupos", "IDTarjetaRotativa = $IDTarjetaRotativa");
                                            $CupoActual = $CupoActual - 1;
                                            $dbo->query("UPDATE TarjetaRotativa SET Cupos = $CupoActual, UsuarioTrEd = $IDSocio, FechaTrEd = NOW() WHERE IDTarjetaRotativa = $IDTarjetaRotativa");
                                        }
                                        //Fin Restar cupos de tarjetas rotativas

                                        SIMUtil::notificar_nuevo_invitado($IDClub, $IDSocio, $NumeroDocumento, $nombre_enviar, $FechaIngreso, $id_invitado_inserta);

                                        //Guardo los datos de los campos
                                        $datos_formulario = json_decode($ValoresFormulario, true);
                                        if (count($datos_formulario) > 0) :
                                            $OtrosDatosFormulario = "";
                                            foreach ($datos_formulario as $detalle_datos) :
                                                $sql_datos_form = $dbo->query("Insert Into SocioInvitadoEspecialOtrosDatos (IDSocioInvitadoEspecial, IDCampoFormularioInvitado, Valor) Values ('" . $id_invitado_inserta . "','" . $detalle_datos["IDCampoFormularioInvitado"] . "','" . $detalle_datos["Valor"] . "')");
                                                $OtrosDatosFormulario .= " " . $detalle_datos["Valor"];
                                            endforeach;
                                        endif;

                                        //verifico si el vehiculo ya esta creado
                                        if (!empty($Placa)) :
                                            $id_vehiculo = $dbo->getFields("Vehiculo", "IDVehiculo", "Placa = '" . $Placa . "'");
                                            //Si el vehiculo no existe en la tabla maestra lo creo
                                            if (empty($id_vehiculo) || (int) $id_vehiculo == 0) :
                                                $inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa) values('" . $id_invitado . "','" . $Placa . "')";
                                                $dbo->query($inserta_vehiculo);
                                                $id_vehiculo = $dbo->lastID();
                                            else :
                                                $inserta_vehiculo = "UPDATE Vehiculo SET IDInvitado = $id_invitado, Placa = '$Placa', IDSocio = $IDSocio WHERE IDVehiculo = $id_vehiculo";
                                                $dbo->query($inserta_vehiculo);
                                            endif;
                                        endif;

                                        //Inserto el vehiculo de la invitacion
                                        if (!empty($Placa)) :
                                            $inserta_vehiculo_inv = "Insert Into VehiculoInvitacion (IDSocioInvitadoEspecial, IDVehiculo, Placa)
                                            Values('" . $id_invitado_inserta . "','" . $id_vehiculo . "','" . $Placa . "')";
                                            $dbo->query($inserta_vehiculo_inv);
                                        endif;

                                        //Guardo el padre de la invitacion
                                        if (($CabezaInvitacion == "S" || count($datos_invitado) == 1) && empty($IDInvitacionGenerada)) :
                                            //Generar Codigo QR
                                            //$parametros_codigo_qr = URLROOT . "plataform/invitadosespeciales.php?IDInvitacion=".$id_invitado_inserta."&Placa=".$Placa;
                                            $parametros_codigo_qr = $NumeroDocumento . "\r\n";

                                            if ($Masivo != "S" && $Email != "sin mail") {

                                                SIMUtil::enviar_codigo_qr($id_invitado_inserta, $parametros_codigo_qr, "Invitado", $Email);
                                            }

                                            if ($CabezaInvitacion == "S" && empty($IDInvitacionGenerada)) :
                                                $IDInvitacionGenerada = $id_invitado_inserta;
                                            endif;
                                        endif;

                                    endforeach;
                                endif;

                                // if (count($array_errorres) > 0) :
                                //     $otros_mensajes = implode(",", $array_errorres);
                                // endif;

                                // $respuesta["message"] = "Invitado Guardado " . $otros_mensajes;


                                $respuesta["message"] = "Invitado Guardado";
                                $respuesta["success"] = true;
                                $respuesta["response"] = null;
                            } else {
                                $respuesta["message"] = "Lo sentimos, socio no tiene permisos suficientes para realizar invitaciones";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            }
                        } else {
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } else {
                        $respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Invitado bloqueado razón:\n" . $bloqueado[Razon];
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Inv acceso. Atencion faltan parametros.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        }

        return $respuesta;
    }

    public function set_autorizacion_contratista($IDClub, $IDSocio, $TipoAutorizacion, $FechaIngreso, $FechaSalida, $TipoDocumento, $NumeroDocumento, $Nombre, $Apellido, $Email, $Placa, $Admin = "", $HoraInicio = "", $HoraSalida = "", $Observaciones = "", $IDUsuario = "", $Telefono = "", $FechaNacimiento = "", $TipoSangre = "", $Predio = "", $Arl = "", $Eps = "", $VencimientoArl = "", $VencimientoEps = "", $ObservacionSocio = "", $ArlFile = "", $DiasCheckbox = "", $ValoresFormulario = "", $AceptaTerminos = "")
    {
        $dbo = &SIMDB::get();

        $bloque_administrativo = SIMWebServiceAccesos::verifica_bloqueo_invitado($NumeroDocumento, $IDClub);
        if ($bloque_administrativo == "S") :
            $respuesta["message"] = "Lo sentimos el contratista tiene un bloqueo por parte del club, no es posible realizar la autorizacion";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        if ($IDClub == 18 && empty(trim($DiasCheckbox))) :
            $respuesta["message"] = "Los días son obligatorios";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        $validaDocumento = str_replace(SIMResources::$abecedario, 1,  $NumeroDocumento);
        if ((int)$validaDocumento == 0) {
            $respuesta["message"] = "El numero de documento del invitado no es valido";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {

            if (!empty($FechaIngreso) && !empty($FechaSalida) /* && !empty($TipoAutorizacion) */ && !empty($TipoDocumento) && !empty($NumeroDocumento) && !empty($Nombre) && !empty($Apellido)) {

                $NumeroDocumento = str_replace(".", "", $NumeroDocumento);
                $NumeroDocumento = str_replace(",", "", $NumeroDocumento);

                $hoy = date("Y-m-d");
                $hoy_mas_año = date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 year"));

                $bloqueado = $dbo->fetchAll("ListaNegraApp", "NumeroDocumento = '$NumeroDocumento' AND IDClub = '$IDClub' LIMIT 1", "array");
                if (empty($bloqueado[IDListaNegraApp])) {
                    if (strtotime($FechaIngreso) >= strtotime($hoy) && strtotime($FechaSalida) >= strtotime($FechaIngreso)) {

                        if (!empty($IDSocio)) {

                            if ($IDClub == 78) {
                                if (strtotime($FechaSalida) >    strtotime($hoy_mas_año)) {
                                    $respuesta["message"] = "Recuerda que el máximo tiempo permito es un año a partir de hoy";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                }
                            }

                            // Consulto las invitaciones que puede hacer el socio
                            $numero_invitados_dia_permitido = $dbo->getFields("Socio", "NumeroAccesos", "IDSocio = '" . $IDSocio . "'");

                            if ((int) $numero_invitados_dia_permitido > 0) {

                                //Consulto cuantas veces la persona ha sido invitada en el mes
                                $mes_invitacion = substr($FechaIngreso, 5, 2);
                                $year_invitacion = substr($FechaIngreso, 0, 4);
                                $dia_invitacion = substr($FechaIngreso, 8, 2);

                                $sql_invitados_dia_soc = $dbo->query("Select * From SocioAutorizacion Where (IDSocio = '" . $IDSocio . "') and YEAR(FechaInicio) = '" . $year_invitacion . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and DAY(FechaInicio) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                                $numero_invitaciones_soc = $dbo->rows($sql_invitados_dia_soc);
                                if (($numero_invitaciones_soc) >= $numero_invitados_dia_permitido) :
                                    $respuesta["message"] = "Lo sentimos, supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia permitido";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;

                                //verifico si el invitado ya esta creado

                                $id_invitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . trim($NumeroDocumento) . "' AND IDClub = '" . $IDClub . "'");

                                //Si el invitado no existe en la tabla maestra lo creo
                                if (empty($id_invitado) || (int) $id_invitado == 0) :

                                    //Para MY el estado debe ser bloqueado para que puedan completar informacion
                                    /*
                                    if($IDClub==9)
                                    $IDEstadoInvitado = 3;
                                    else
                                    $IDEstadoInvitado = 1;
                                        */
                                    $IDEstadoInvitado = 1;

                                    $inserta_invitado = "INSERT INTO Invitado (ARLFILE,IDClub, IDTipoDocumento, NumeroDocumento, Nombre, Apellido, Email, ObservacionGeneral, Telefono, FechaNacimiento, TipoSangre, Predio, ARL, EPS, FechaVencimientoArl, FechaVencimientoEps, IDEstadoInvitado, UsuarioTrCr, FechaTrCr)
                                        VALUES('" . $ArlFile . "','" . $IDClub . "', '" . $TipoDocumento . "','" . trim($NumeroDocumento) . "','" . strtoupper($Nombre) . "','" . strtoupper($Apellido) . "','" . $Email . "','" . $Observaciones . "','" . $Telefono . "','" . $FechaNacimiento . "','" . $TipoSangre . "','" . $Predio . "','" . $Arl . "','" . $Eps . "','" . $VencimientoArl . "','" . $VencimientoEps . "','" . $IDEstadoInvitado . "','App','" . date("Y-m-d H:i:s") . "')";

                                    $dbo->query($inserta_invitado);
                                    $id_invitado = $dbo->lastID();
                                else :
                                    //Actualizo las observaciones y predio unicamente
                                    if (!empty($Observaciones)) {
                                        $CampoObservacion = " ,ObservacionGeneral = '" . $Observaciones . "'";
                                    }

                                    //if(!empty($ObservacionSocio))
                                    //$CampoObservacion = " ,ObservacionGeneral = '" . $ObservacionSocio . "'";

                                    if (!empty($FechaNacimiento) && $FechaNacimiento != "0000-00-00") {
                                        $CampoObservacion .= " ,FechaNacimiento = '" . $FechaNacimiento . "'";
                                    }

                                    if (!empty($TipoSangre)) {
                                        $CampoObservacion .= " , TipoSangre = '" . $TipoSangre . "'";
                                    }

                                    if (!empty($Telefono)) {
                                        $CampoObservacion .= " , Telefono = '" . $Telefono . "'";
                                    }

                                    if (!empty($Predio)) {
                                        $CampoObservacion .= " , Predio = '" . $Predio . "'";
                                    }

                                    if (!empty($Arl)) {
                                        $CampoObservacion .= " , ARL = '" . $Arl . "'";
                                    }

                                    if (!empty($Eps)) {
                                        $CampoObservacion .= " , EPS = '" . $Eps . "'";
                                    }

                                    if (!empty($Arl)) {
                                        $CampoObservacion .= " , FechaVencimientoArl = '" . $VencimientoArl . "'";
                                    }

                                    if (!empty($Eps)) {
                                        $CampoObservacion .= " , FechaVencimientoEps = '" . $VencimientoEps . "'";
                                    }

                                    if (!empty($Email)) {
                                        $CampoObservacion .= " , Email = '" . $Email . "'";
                                    }


                                    $sql_edit_invitado = "UPDATE Invitado Set  ARLFILE = '" . $ArlFile . "',Nombre = '" . strtoupper($Nombre) . "', Apellido  = '" . strtoupper($Apellido) . "', Email='" . $Email . "'
                                                                                                " . $CampoObservacion . "  Where IDInvitado = '" . $id_invitado . "'";

                                    $dbo->query($sql_edit_invitado);
                                endif;

                                //Si es cabeza de familia guardo el id del Socio
                                if ($CabezaInvitacion == "S") :
                                    $IDPadre = $id_invitado;
                                endif;

                                $sql_numero_invitacion = $dbo->query("Select IDSocioAutorizacion From SocioAutorizacion Where IDInvitado = '" . $id_invitado . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "'");
                                $numero_invitaciones = $dbo->rows($sql_numero_invitacion);
                                //Consulto cuantas personas ha invitado el socio en el mes
                                $sql_invitados_mes = $dbo->query("Select IDSocioAutorizacion From SocioAutorizacion Where IDSocio = '" . $IDSocio . "' and YEAR(FechaInicio) = '" . $year_invitacion . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "'");
                                $numero_invitados_mes = $dbo->rows($sql_invitados_mes);
                                //Consulto cuantas personas ha invitado el socio en el dia
                                $sql_invitados_dia = $dbo->query("Select IDSocioAutorizacion From SocioAutorizacion Where IDSocio = '" . $IDSocio . "' and YEAR(FechaInicio) = '" . $year_invitacion . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and DAY(FechaInicio) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                                $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                                $numero_invitados_mes_permitido = 50000;
                                $numero_mismo_invitado_mes = "30000";
                                $cumplimiento_obligatorio_limite = "S";

                                // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
                                //$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );

                                if ((int) $numero_invitados_dia < (int) $numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite == "N") {
                                    if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite == "N") {
                                        if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes || $cumplimiento_obligatorio_limite == "N") {
                                            //Verifico que el invitado no este invitado mas de una vez el mismo dia
                                            //echo "Select * From SocioAutorizacion Where IDInvitado = '".$id_invitado."' and FechaInicio = '".$FechaIngreso."'";
                                            //exit;

                                            //$sql_invitacion_dia = $dbo->query("Select * From SocioAutorizacion Where IDInvitado = '".$id_invitado."' and FechaInicio = '".$FechaIngreso."'");
                                            //$numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);

                                            // lo dejo en 0 para que pueda invitar mas de una vez la misma persona ya que un empleado puede entrara a varias predios en MY
                                            $numero_invitaciones_dia = 0;

                                            if ((int) $numero_invitaciones_dia <= 0) {

                                                //verifico si el vehiculo ya esta creado
                                                if (!empty($Placa)) :
                                                    $id_vehiculo = $dbo->getFields("Vehiculo", "IDVehiculo", "Placa = '" . $Placa . "'");
                                                    //Si el vehiculo no existe en la tabla maestra lo creo
                                                    if (empty($id_vehiculo) || (int) $id_vehiculo == 0) :
                                                        $inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa) Values('" . $id_invitado . "','" . $Placa . "')";
                                                        $dbo->query($inserta_vehiculo);
                                                        $id_vehiculo = $dbo->lastID();
                                                    endif;
                                                endif;

                                                //genero el codigo de autorizacion
                                                $CodigoAutorizacion = SIMUtil::genera_codigo_autorizacion("C");
                                                $CodigoAutorizacion = trim($TipoSangre);

                                                if (!empty($IDUsuario)) {
                                                    $creado_por = $IDUsuario;
                                                } else {
                                                    $creado_por = "Socio";
                                                }

                                                //Inserto invitacion
                                                $sql_inserta_inv = $dbo->query(
                                                    "INSERT INTO SocioAutorizacion (IDClub, IDSocio, IDInvitado, IDVehiculo, TipoAutorizacion, FechaInicio, HoraInicio, FechaFin, HoraFin, CodigoAutorizacion, Predio, ObservacionSocio, AceptaTerminos, UsuarioTrCr,Dias, FechaTrCr)
                                                        VALUES ('$IDClub','$IDSocio', '$id_invitado', '$id_vehiculo', '$TipoAutorizacion', '$FechaIngreso ','$HoraInicio','$FechaSalida', '$HoraSalida', '$CodigoAutorizacion','$Predio', '$ObservacionSocio','$AceptaTerminos',
                                                                '$creado_por','$DiasCheckbox','" . date("Y-m-d H:i:s") . "')"
                                                );
                                                $id_invitado_inserta = $dbo->lastID();

                                                //Guardo los datos de los campos
                                                $ValoresFormulario = trim(preg_replace('/\s+/', ' ', $ValoresFormulario));
                                                $datos_formulario = json_decode($ValoresFormulario, true);
                                                if (count($datos_formulario) > 0) :
                                                    foreach ($datos_formulario as $detalle_datos) :
                                                        $sql_datos_form = $dbo->query("Insert Into SocioAutorizacionOtrosDatos (IDSocioAutorizacion, IDCampoFormularioContratista, Valor) Values ('" . $id_invitado_inserta . "','" . $detalle_datos["IDCampoFormularioContratista"] . "','" . $detalle_datos["Valor"] . "')");
                                                        $OtrosDatosFormulario .= " " . $detalle_datos["Valor"];

                                                        if ($IDClub == 35) {
                                                            $ObservacionSocio = $detalle_datos["Valor"];
                                                            $actualizo = " UPDATE SocioAutorizacion SET ObservacionSocio = '" . $ObservacionSocio . "' WHERE IDSocioAutorizacion = '" . $id_invitado_inserta . "'";
                                                            // $ejecuta = $dbo->query($actualizo);
                                                        }
                                                    endforeach;
                                                endif;

                                                //Guardo el padre de la invitacion
                                                if (!empty($id_invitado_inserta)) :
                                                    //Generar Codigo QR
                                                    //$parametros_codigo_qr = URLROOT . "plataform/invitadosespeciales.php?IDInvitacion=".$id_invitado_inserta."&Placa=".$Placa;
                                                    $parametros_codigo_qr = $NumeroDocumento . "\r\n";
                                                    // if ((empty($Admin) && $IDClub != 34) || $IDClub == 16) :
                                                    if (($IDClub != 34) || $IDClub == 16) :
                                                        SIMUtil::enviar_codigo_qr($id_invitado_inserta, $parametros_codigo_qr, "Contratista");
                                                    endif;
                                                endif;

                                                if ($IDClub == 158) :
                                                    $mensaje = "Recuerde que los horarios autorizados para el ingreso de los contratistas es de lunes a viernes de 8:00 a.m. a 5:00 p.m. y los sábados de 8:00 a.m. a 12:00 m";
                                                endif;

                                                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG) . "" . $mensaje;
                                                $respuesta["success"] = true;
                                                $respuesta["response"] = null;
                                            } else {
                                                $respuesta["message"] = "Lo sentimos " . $Nombre . " " . $Apellido . " ya tiene una invitacion para el dia seleccionado.";
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = null;
                                            }
                                        } else {
                                            $respuesta["message"] = "Lo sentimos supera el numero maximo de " . $numero_invitados_mes_permitido . " invitaciones por mes";
                                            $respuesta["success"] = false;
                                            $respuesta["response"] = null;
                                        }
                                    } else {
                                        $respuesta["message"] = "Lo sentimos, " . $Nombre . " " . $Apellido . "  ha sido invitadas mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                    }
                                } else {
                                    $respuesta["message"] = "Lo sentimos, supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                }
                            } else {
                                $respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            }
                        } else {
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } else {
                        $respuesta["message"] = "Lo sentimos, no se permite fechas antiguas o la fecha inicio no puede ser menor a la fecha fin " . "Inicio " . $FechaIngreso . "FIN:" . $FechaSalida . "HOY " . date("Y-m-d");
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Invitado bloqueado razón:\n" . $bloqueado[Razon];
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Inv." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        }

        return $respuesta;
    }

    public function set_invitado($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario, $IDUsuario = "", $files = "")
    {
        $dbo = &SIMDB::get();

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $FechaIngreso . "' and IDPais = 1");
        $ConfiguracionClub = $dbo->fetchAll('ConfiguracionClub', "IDClub = $IDClub", "array");




        // Reglas invitados Carmel club, 108
        if ($IDClub == 108) {
            // if ($IDClub == 8) {
            $RepuestaReglasCarmelClub = SIMWebServiceAccesos::reglas_invitados_carmel_club($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario);
            if ($RepuestaReglasCarmelClub['success'] === false) {
                $respuesta["message"] = $RepuestaReglasCarmelClub['message'];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        // Fin Reglas invitados Carmel club, 108

        // Reglas invitados club san jacinto, 126
        // Inhabilitada por petición del club
        // if ($IDClub == 126) {
        //     $RepuestaReglasClubSanJacinto = SIMWebServiceAccesos::reglas_invitados_club_sanjacinto($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario);
        //     if ($RepuestaReglasClubSanJacinto['success'] === false) {
        //         $respuesta["message"] = $RepuestaReglasClubSanJacinto['message'];
        //         $respuesta["success"] = false;
        //         $respuesta["response"] = null;
        //         return $respuesta;
        //     }
        // }
        // Fin Reglas invitados club san jacinto, 126

        //PARA LAGUNITA NO PERMITO QUE HAGAN SOLICITUDES EL DIA LUNES
        if ($IDClub == 141) {
            $dia_semana = date('l', strtotime($FechaIngreso));

            if ($dia_semana == "Monday") {
                $respuesta["message"] = "Lo sentimos, no se pueden realizar invitaciones desde la aplicación los dias lunes";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        // Validación para impedir invitaciones el 19/11/2022 para el club Lagunita 141
        if ($IDClub == 141) {
            if ($FechaIngreso == '2022-11-19') {
                $respuesta["message"] = "Lo sentimos, no se pueden realizar invitaciones desde la aplicación para el 19 de noviembre";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        // Validación para impedir invitaciones el 19/11/2022 para el club Lagunita 141

        // Validación para impedir invitaciones el 19/12/2022 para el club Country club lagunita 141
        if ($IDClub == 141) {
            if ($FechaIngreso == '2022-12-19') {
                $respuesta["message"] = "Lo sentimos, no se pueden realizar invitaciones desde la aplicación para el 19 de diciembre";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        // Validación para impedir invitaciones el 19/12/2022 para el club Country club lagunita 141

        // Validación para impedir invitaciones el 18/12/2022 para el club Lagartos 7
        if ($IDClub == 7) {
            if ($FechaIngreso == '2022-12-18') {
                $respuesta["message"] = "Lo sentimos, no se pueden realizar invitaciones desde la aplicación para el 18 de diciembre";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        // Validación para impedir invitaciones el 18/12/2022 para el club Lagartos 7

        // Validar invitaciones por Accion padre
        if ($ConfiguracionClub['LimitarInvitadosPorAccion'] == 'S') {
            $MaxInvitadosDiaPorAccion = $ConfiguracionClub['MaxInvitadosDiaPorAccion'];

            // Validar dias festivos
            $EsDiaFestivo = SIMUtil::validaDiaFestivo($IDClub, $FechaIngreso);
            // Fin Validar dias festivos

            // Para el country limitamos a 4 invitados los fines de semana
            if ($IDClub == 44) {
                $DiaInvitacion = date("l", strtotime($FechaIngreso));
                if ($DiaInvitacion == "Saturday" || $DiaInvitacion == "Sunday" || $DiaInvitacion == "Friday" || $EsDiaFestivo == true) {
                    $MaxInvitadosDiaPorAccion = 4;
                }
            }
            // Fin Para el country limitamos a 4 invitados los fines de semana
            if (!empty($datos_socio['AccionPadre'])) {
                $whereAccion = " AND (s.Accion='" . $datos_socio['AccionPadre'] . "' OR s.AccionPadre='" . $datos_socio['AccionPadre'] . "') ";
            } else {
                $whereAccion = " AND (s.AccionPadre='" . $datos_socio['Accion'] . "' OR s.Accion='" . $datos_socio['Accion'] . "') ";
            }
            $q_InvitadosPorAccion = $dbo->query("SELECT COUNT(IDSocioInvitado) as CantidadInvitados from SocioInvitado si,Socio s WHERE si.IDSocio = s.IDSocio $whereAccion and si.IDClub = '" . $IDClub . "' and si.FechaIngreso = '" . $FechaIngreso . "'");

            $InvitadosPorAccion = $dbo->assoc($q_InvitadosPorAccion);
            if ($InvitadosPorAccion['CantidadInvitados'] >= $MaxInvitadosDiaPorAccion) {
                $respuesta["message"] = "Lo sentimos, ha superado el limite de $MaxInvitadosDiaPorAccion invitados por Acción por día";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        //FIN Validar invitaciones por Accion padre

        if ($datos_socio["IDEstadoSocio"] != "1" && $datos_socio["IDEstadoSocio"] != "5") {
            $respuesta["message"] = "Lo sentimos, tiene un estado diferente a activo, no puede crear invitaciones";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }
        // Validación numero documento mayor a 0
        if (empty($NumeroDocumento) || strlen($NumeroDocumento) <= 0) {
            $respuesta["message"] = "El numero de documento del invitado no es valido";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }
        //Fin Validación numero documento mayor a 0


        // Validación Datos invitados para Cantegril
        if ($IDClub == 185) {
            $FechaNacimiento = $datos_socio['FechaNacimiento'];
            $Edad = SIMUtil::Calcular_Edad($FechaNacimiento);
            if ($Edad < 18) {
                $respuesta["message"] = "Lo sentimos, los menores de edad deben registrar invitados por medio de recepción, este registro tiene un costo adicional";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            if ($Nombre == '') {
                $respuesta["message"] = "Lo sentimos, el Nombre del Invitado es obligatorio";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            if ($NumeroDocumento == '') {
                $respuesta["message"] = "Lo sentimos, el Número de documento del Invitado es obligatorio";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            if (!is_numeric($NumeroDocumento)) {
                $respuesta["message"] = "Lo sentimos, el Número de documento debe contener solo números";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            if (strlen($NumeroDocumento) < 7) {
                $respuesta["message"] = "Lo sentimos, el Número de documento debe contener al menos 7 dígitos";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            $datos_formulario = json_decode($ValoresFormulario, true);
            foreach ($datos_formulario as $Valores) {
                $CampoFormularioInvitado = $dbo->fetchAll('CampoFormularioInvitado', "IDCampoFormularioInvitado=" . $Valores['IDCampoFormularioInvitado'], "array");
                if ($CampoFormularioInvitado['TipoCampo'] == 'email') {
                    if (!empty($Valores['Valor'])) {
                        if (!filter_var($Valores['Valor'], FILTER_VALIDATE_EMAIL)) {
                            $respuesta["message"] = "Lo sentimos, el campo " . $CampoFormularioInvitado['EtiquetaCampo'] . " no cumple con el formato requerido: 'ejemplo@email.com'";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    } else {
                        $respuesta["message"] = "Lo sentimos, el Email del Invitado es obligatorio";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }
            }
        }
        //Fin Validación Datos invitados para Cantegril

        // Validación club Caracas Club Country
        if ($IDClub == 201) {
            $NumeroDocumento = strtoupper(str_replace(" ", "", str_replace(".", "", $NumeroDocumento)));
        }
        // Fin Validación club Caracas Club Country
        // Validación club Ecuador Arrayanes
        if ($IDClub == 23) {
            // Validar que el numero de documento sea igual a 10 caracteres
            $arr_numeroDocumento  = array_map('intval', str_split($NumeroDocumento));
            if (count($arr_numeroDocumento) > 10 || count($arr_numeroDocumento) < 10) {
                $respuesta["message"] = "El número de documento del invitado debe contener 10 números";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            // Validar que el numero de documento inicie por 1 o 0
            $PrimerNumero = substr($NumeroDocumento, 0, 1);
            if ($PrimerNumero != 0 && $PrimerNumero != 1) {
                $respuesta["message"] = "El numero de documento del invitado no es valido";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            // Validar que el numero de documento no sea un numero de un caracter repetido ejemplo: "111111111"
            $cont = 0;
            foreach ($arr_numeroDocumento as $key => $value) {
                if ($key > 0) {
                    if ($value != $arr_numeroDocumento[($key - 1)]) {
                        $cont = 1;
                    }
                }
            }
            if ($cont == 0) {
                $respuesta["message"] = "El numero de documento del invitado no es valido";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }
        // Fin Validación club Ecuador Arrayanes

        // PARA LAGARTOS NO PUEDEN INVITAR LOS MENORES DE EDAD
        if ($IDClub == 7) {

            if ($FechaIngreso == '2022-02-19' || $FechaIngreso == '2022-02-20') :

                $respuesta["message"] = "No estan permitidas las invitaciones para el 19 y 20 de Febrero";
                $respuesta["success"] = false;
                $respuesta["response"] = NULL;
                return $respuesta;

            endif;

            $fechaNacimiento = $datos_socio["FechaNacimiento"];
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
            $años = $edad_diff->format('%y');

            if ($años < 18) :
                $respuesta["message"] = "Lo sentimos, los menores de edad no pueden hacer invitaciones.";
                $respuesta["success"] = false;
                $respuesta["response"] = NULL;
                return $respuesta;
            endif;
        }

        // PARA LAGARTOS NO PUEDEN INVITAR LOS MENORES DE EDAD
        if ($IDClub == 7 && $FechaIngreso == '2021-12-19') {

            $respuesta["message"] = "Lo sentimos, no esta permitido invitar este día";
            $respuesta["success"] = false;
            $respuesta["response"] = NULL;
            return $respuesta;
        }

        if ($IDClub == 70) {
            $respuesta_reglas_sanAndres = self::reglas_invitados_club_sanAndres($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario);
            if ($respuesta_reglas_sanAndres['success'] == false) {
                $respuesta["message"] = $respuesta_reglas_sanAndres['message'];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        if (($IDClub == 700) && (date('w', strtotime($FechaIngreso)) == 0 || date('w', strtotime($FechaIngreso)) == 6 || date('w', strtotime($FechaIngreso)) == 100 || !empty($IDFestivo))) {
            $respuesta["message"] = "Lo sentimos, no es posible invitar en el dia seleccionado";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        /*  //if ($IDClub == 44 && (date('w', strtotime($FechaIngreso)) == 0 || date('w', strtotime($FechaIngreso)) == 6 || date('w', strtotime($FechaIngreso)) == 5 || !empty($IDFestivo)) ) {
        if ($IDClub == 44 && (date('w', strtotime($FechaIngreso)) == 0  || !empty($IDFestivo)) ) {
            $respuesta["message"] = "Lo sentimos, no es posible invitar en el dia seleccionado";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        } */

        //Especial bquilla se valida por cedula primero
        if ($IDClub == 110) {

            if ($datos_socio["TipoSocio"] != "PRINCIPAL" && $datos_socio["TipoSocio"] != "CONYUGUE") {
                $respuesta["message"] = "Lo sentimos solo los socios principales pueden realizar invitaciones";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            /* $resp_valida = SIMWebServiceAccesos::valida_quilla($NumeroDocumento, $ValoresFormulario, $IDSocio, $Nombre);
            if (!$resp_valida["success"]) {
                $respuesta["message"] = $resp_valida["message"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                //return $respuesta;
            } */
        }

        $SocioClub = $dbo->getFields("Socio", "IDSocio", "(IDEstadoSocio = 1 OR IDEstadoSocio = 5) AND TipoSocio <> 'Canje' AND TipoSocio <> 'Invitado' AND IDClub = $IDClub AND NumeroDocumento = '$NumeroDocumento'");
        if ($SocioClub > 0 && $IDClub != 196 && $IDClub != 185) {
            $respuesta["message"] = "No se puede invitar a socios del club.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;

            return $respuesta;
        }

        if ($IDClub == 44) {

            $datos_formulario = json_decode($ValoresFormulario, true);

            foreach ($datos_formulario as $detalle_datos) :

                if ($detalle_datos["IDCampoFormularioInvitado"] == '69') {
                    if ($detalle_datos["Valor"] == "Alguno") {
                        $respuesta["message"] = "No es posible el registro del invitado, debe reportarse con su médico";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                        return $respuesta;
                    }
                }

                if ($detalle_datos["IDCampoFormularioInvitado"] == '36') {
                    if ($detalle_datos["Valor"] == "No") {
                        $respuesta["message"] = "No es posible el registro del invitado, debe reportarse con su médico";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                        return $respuesta;
                    }
                }

                if ($detalle_datos["IDCampoFormularioInvitado"] == '70') {
                    if ($detalle_datos["Valor"] == "No") {
                        $respuesta["message"] = "No es posible el registro del invitado, debe reportarse con su médico";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                        return $respuesta;
                    }
                }
            endforeach;
        }

        if ($IDClub == 23) :
            $datos_formulario = json_decode($ValoresFormulario, true);

            $mes_invitacion = date("m", strtotime($FechaIngreso));
            $year_invitacion = date("Y", strtotime($FechaIngreso));

            foreach ($datos_formulario as $detalle_datos) :

                if ($detalle_datos["IDCampoFormularioInvitado"] == '3') {
                    $Valores = explode(",", $detalle_datos[Valor]);
                    foreach ($Valores as $id => $Respuesta) :
                        if (trim($Respuesta) != "Restaurantes") :
                            $sql_inv = "SELECT count(IDSocioInvitado) as TotalMesInvitado FROM SocioInvitado WHERE IDClub = '$IDClub' AND NumeroDocumento = '$NumeroDocumento' AND MONTH(FechaIngreso) = '$mes_invitacion' AND YEAR(FechaIngreso) = '$year_invitacion'";
                            $result_inv = $dbo->query($sql_inv);
                            $row_inv_dia = $dbo->fetchArray($result_inv);
                            if ($row_inv_dia["TotalMesInvitado"] >= 2) {

                                $respuesta["message"] = "Lo sentimos un invitado solo puede ingresar maximo 2 veces al mes a un area deportiva";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;

                                return $respuesta;
                            }
                        endif;
                    endforeach;
                }
            endforeach;
        endif;
        //Especial bquilla se valida por cedula primero

        if (empty($IDUsuario)) {
            $IDUsuario = "Socio";
        }


        $validaDocumento = str_replace(SIMResources::$abecedario, 1,  $NumeroDocumento);
        if ((int)$validaDocumento == 0) {
            $respuesta["message"] = "El numero de documento del invitado no es valido";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {

            if (!empty($NumeroDocumento) && !empty($Nombre) && !empty($FechaIngreso)) {
                $bloqueado = $dbo->fetchAll("ListaNegraApp", "NumeroDocumento = '$NumeroDocumento' AND IDClub = '$IDClub' LIMIT 1", "array");

                if (empty($bloqueado[IDListaNegraApp])) {

                    $NumeroDocumento = str_replace(".", "", $NumeroDocumento);
                    $NumeroDocumento = trim(str_replace(",", "", $NumeroDocumento));

                    $hoy = date("Y-m-d");

                    if (strtotime($FechaIngreso) >= strtotime($hoy)) {

                        //verifico que el socio exista y pertenezca al club
                        $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                        if (!empty($id_socio)) {

                            // CAMBIO Sep 12 2016: Se consulta las invitaciones que puede hacer el socio Titular
                            // Consulto las invitaciones que puede hacer el socio
                            $numero_invitados_dia_permitido_socio = $dbo->getFields("Socio", "NumeroInvitados", "IDSocio = '" . $IDSocio . "'");

                            if (!empty($datos_socio["AccionPadre"])) : // es un beneficiario
                                $id_socio_titular = $dbo->getFields("Socio", "IDSocio", "Accion = '" . $datos_socio["AccionPadre"] . "'");
                                $numero_invitados_dia_permitido = $dbo->getFields("Socio", "NumeroInvitados", "IDSocio = '" . $id_socio . "'");

                                if (empty($numero_invitados_dia_permitido) || $numero_invitados_dia_permitido == 0) {
                                    $numero_invitados_dia_permitido = $numero_invitados_dia_permitido_socio;
                                }

                                //Consulto los id socio de mi vinculo
                                $sql_socio_vinculo = "Select IDSocio From Socio Where AccionPadre = '" . $datos_socio["AccionPadre"] . "' or Accion = '" . $datos_socio["AccionPadre"] . "' and IDClub = '" . $IDClub . "'";
                                $qry_socio_vinculo = $dbo->query($sql_socio_vinculo);
                                while ($row_socio_vinculo = $dbo->fetchArray($qry_socio_vinculo)) :
                                    $array_socio_vinculo[] = $row_socio_vinculo["IDSocio"];
                                endwhile;
                                if (count($array_socio_vinculo) > 0) :
                                    $id_otro_socio = implode(",", $array_socio_vinculo);
                                    $condicion_otro_socio = " or IDSocio in (" . $id_otro_socio . ")";
                                endif;
                            else :
                                $id_socio_titular = $IDSocio;
                                $numero_invitados_dia_permitido = $dbo->getFields("Socio", "NumeroInvitados", "IDSocio = '" . $IDSocio . "'");
                                //Consulto los id socio de mi vinculo
                                $sql_socio_vinculo = "Select IDSocio From Socio Where AccionPadre = '" . $datos_socio["Accion"] . "' and IDClub = '" . $IDClub . "'";
                                $qry_socio_vinculo = $dbo->query($sql_socio_vinculo);
                                while ($row_socio_vinculo = $dbo->fetchArray($qry_socio_vinculo)) :
                                    $array_socio_vinculo[] = $row_socio_vinculo["IDSocio"];
                                endwhile;
                                if (count($array_socio_vinculo) > 0) :
                                    $id_otro_socio = implode(",", $array_socio_vinculo);
                                    $condicion_otro_socio = " or IDSocio in (" . $id_otro_socio . ")";
                                endif;
                            endif;

                            // Consulto si el dia de la reserva esta asignado como fecha especial para no tomar en cuenta invitaciones
                            $id_fecha_Especial = $dbo->getFields("FechaEspecialInvitado", "IDFechaEspecialInvitado", "Fecha = '" . $FechaIngreso . "' and IDClub = '" . $IDClub . "'");
                            if (!empty($id_fecha_Especial)) :
                                // Dejo los parametros ilimitados
                                $numero_invitados_dia_permitido = 100;
                                $numero_invitados_dia_permitido_socio = 100;
                            endif;

                            //Consulto cuantas veces la persona ha sido invitada en el mes
                            $mes_invitacion = substr($FechaIngreso, 5, 2);
                            $year_invitacion = substr($FechaIngreso, 0, 4);
                            $dia_invitacion = substr($FechaIngreso, 8, 2);

                            $fechats = strtotime($FechaIngreso);
                            if ($IDClub == '44') {
                                switch (date('w', $fechats)) {
                                    case 0:
                                        $dia_txt = "Domingo";
                                        break;
                                    case 1:
                                        $dia_txt = "Lunes";
                                        break;
                                    case 2:
                                        $dia_txt = "Martes";
                                        break;
                                    case 3:
                                        $dia_txt = "Miercoles";
                                        break;
                                    case 4:
                                        $dia_txt = "Jueves";
                                        break;
                                    case 5:
                                        $dia_txt = "Viernes";
                                        break;
                                    case 6:
                                        $dia_txt = "Sabado";
                                        break;
                                }

                                //Consulto cuantos invitadoa ha hecho para el dia
                                $sql_invitados_dia_soc = $dbo->query("Select * From SocioInvitado Where (IDSocio = '" . $IDSocio . "') and YEAR(FechaIngreso) = '" . $year_invitacion . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and DAY(FechaIngreso) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                                $numero_invitaciones_soc = $dbo->rows($sql_invitados_dia_soc);


                                if ($dia_txt == 'Viernes' || $dia_txt == 'Sabado' | $dia_txt == 'Domingo') {
                                    $MaxInvitacionesDia = ($FechaIngreso == '2023-02-10' || $FechaIngreso == '2023-02-11' || $FechaIngreso == '2023-02-12') ? 6 : 4;
                                    if (($numero_invitaciones_soc) >= $MaxInvitacionesDia) {
                                        $respuesta["message"] = "Lo sentimos, supera el numero maximo de invitaciones para el dia " . $dia_txt;
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    }
                                }
                            }




                            if (($numero_invitaciones_soc) >= $numero_invitados_dia_permitido_socio) :
                                $respuesta["message"] = "Lo sentimos supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia permitido";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            endif;

                            $valida_regla = SIMWebServiceAccesos::valida_regla_invitacion_v1($IDClub, $IDSocio, $NumeroDocumento, $FechaIngreso, $ValoresFormulario);

                            if (!$valida_regla["success"]) {

                                $respuesta["message"] = $valida_regla["message"];
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            } else {
                                if ($valida_regla["response"][MaximoInvitadoDiaValidaApp] == 0) :
                                    if ($valida_regla["response"]["MaximoInvitadoDia"] <= $valida_regla["response"]["TotalDia"]) :
                                        $mensajesadicionales = $valida_regla["response"][MensajeNoValidaApp];
                                    else :
                                        $mensajesadicionales = "";
                                    endif;
                                endif;
                            }
                            $Observaciones = "";
                            if ($IDClub == 191) :
                                $reglasCampestreBosque = SIMWebServiceAccesos::reglas_campestre_bosque($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario, $IDUsuario);
                                if ($reglasCampestreBosque["success"] == 1) {

                                    $respuesta["message"] = $valida_regla["message"];
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                } else {
                                    if ($reglasCampestreBosque["success"] == 2) :

                                        $mensajesadicionales = $reglasCampestreBosque["message"];
                                        // Capturamos lo comentarios de las reglas
                                        $Observaciones .= (!empty($reglasCampestreBosque['response'])) ? $reglasCampestreBosque['response'] : "";

                                    endif;
                                }
                            endif;
                            if ((int) $numero_invitados_dia_permitido > 0 && $numero_invitados_dia_permitido_socio > 0) {
                                // Regla invitados para el club Cantegril
                                if ($IDClub != 185) {
                                    $whereEstado = "AND Estado = 'I'";
                                }
                                // Fin Regla invitados para el club Cantegril

                                $sql = "SELECT * FROM SocioInvitado WHERE NumeroDocumento = '$NumeroDocumento' AND MONTH(FechaIngreso) = '$mes_invitacion' AND YEAR(FechaIngreso) = '$year_invitacion'  and IDClub = '$IDClub' $whereEstado";
                                $sql_numero_invitacion = $dbo->query($sql);
                                $numero_invitaciones = $dbo->rows($sql_numero_invitacion);

                                //Consulto cuantas personas ha invitado el socio en el mes
                                $sql_invitados_mes = $dbo->query("Select * From SocioInvitado Where IDSocio = '" . $IDSocio . "' and YEAR(FechaIngreso) = '" . $year_invitacion . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "' and Estado = 'I'");
                                $numero_invitados_mes = $dbo->rows($sql_invitados_mes);

                                //Cambio Sep 12: Se suma el total de invitados por accion padre
                                //Consulto cuantas personas ha invitado el socio en el dia
                                //$sql_invitados_dia = $dbo->query("Select * From SocioInvitado Where IDSocio = '".$IDSocio."' and YEAR(FechaIngreso) = '".$year_invitacion."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and DAY(FechaIngreso) = '".$dia_invitacion."' and IDClub = '".$IDClub."' and Estado = 'I'");
                                //$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
                                $sql_invitados_dia = $dbo->query("Select * From SocioInvitado Where (IDSocio = '" . $IDSocio . "' " . $condicion_otro_socio . ") and YEAR(FechaIngreso) = '" . $year_invitacion . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and DAY(FechaIngreso) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                                $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                                $numero_invitados_dia_permitido_accion = 100;
                                $numero_invitados_mes_permitido = 5000;
                                $numero_mismo_invitado_mes = "3000";
                                $cumplimiento_obligatorio_limite = "S";

                                if ($IDClub == 11) {
                                    $numero_mismo_invitado_mes = "2";
                                }
                                // Regla invitados para el club Cantegril
                                if ($IDClub == 185) {
                                    $numero_invitados_mes_permitido = 2;
                                    $numero_mismo_invitado_mes = 2;
                                }

                                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");


                                //prueba con mi club negar invitados a morosos
                                if ($IDClub == 239) :
                                    $moroso = $datos_socio["PermiteReservar"];
                                    if ($moroso == "N") :
                                        $respuesta["message"] = "Lo sentimos no puede crear una Invitación porque,  su acción presenta un retraso a la fecha";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    endif;

                                endif;

                                // Fin Regla invitados para el club Cantegril

                                //APLICAMOS CONDICONES ESPECIALES PARA ARRAYANES PRUEBA EN DEV, NO SUBIR AUN
                                if ($IDClub == 11) {

                                    $sql_total_mes = $dbo->query("SELECT COUNT(IDSocioInvitado) as invitadonormal from SocioInvitado WHERE  NumeroDocumento='$NumeroDocumento' and TipoInvitacion='Invitado normal' and YEAR(FechaTrCr) = YEAR(CURRENT_DATE()) and MONTH(FechaTrCr) = MONTH(CURRENT_DATE()) ");

                                    $sql_total_mes_socio = $dbo->query("SELECT COUNT(IDSocioInvitado) as invitadosocio from SocioInvitado WHERE  NumeroDocumento='$NumeroDocumento' and TipoInvitacion='Padre de socio' and YEAR(FechaTrCr) = YEAR(CURRENT_DATE()) and MONTH(FechaTrCr) = MONTH(CURRENT_DATE()) ");

                                    $sql = $dbo->assoc($sql_total_mes);
                                    $sql1 = $dbo->assoc($sql_total_mes_socio);
                                    //primer condicional
                                    $total_mes = $sql['invitadonormal'];
                                    $total_mes_socio = $sql['invitadosocio'];
                                    if ($total_mes >= 2) {
                                        $respuesta["message"] = "Lo sentimos, ha superado el limite de 2 invitaciones (Invitado normal) en el mes!";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    }

                                    if ($total_mes_socio >= 4) {
                                        $respuesta["message"] = "Lo sentimos, ha superado el limite de 4 invitaciones (Invitado Padre de socio) en el mes!";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    }


                                    $cumplimiento_obligatorio_limite = "S";


                                    $datos_formulario = json_decode($ValoresFormulario, true);

                                    foreach ($datos_formulario as $detalle_datos) :

                                        if ($detalle_datos["IDCampoFormularioInvitado"] == '126') {

                                            if ($detalle_datos["Valor"] == "Invitado normal") {

                                                $Tipo_Invitacion = "Invitado normal";
                                            } elseif ($detalle_datos["Valor"] == "Padre de socio") {

                                                $Tipo_Invitacion = "Padre de socio";
                                            } else {
                                                $Tipo_Invitacion = "";
                                            }
                                        }
                                    endforeach;
                                }

                                //APLICAMOS CONDICONES ESPECIALES PARA COUNTRY CLUB CARACAS
                                if ($IDClub == 239) {

                                    $sql_total_mes = $dbo->query("SELECT COUNT(IDSocioInvitado) as totalmensual from SocioInvitado WHERE  NumeroDocumento='$NumeroDocumento' and YEAR(FechaTrCr) = YEAR(CURRENT_DATE()) and MONTH(FechaTrCr) = MONTH(CURRENT_DATE()) ");
                                    $sql = $dbo->assoc($sql_total_mes);
                                    //primer condicional
                                    $total_mes = $sql['totalmensual'];

                                    if ($total_mes >= 4) {
                                        $respuesta["message"] = "Lo sentimos, este invitado ya ha sido invitado 4 veces en el mes!";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                        return $respuesta;
                                    }

                                    $cumplimiento_obligatorio_limite = "S";


                                    $datos_formulario = json_decode($ValoresFormulario, true);

                                    foreach ($datos_formulario as $detalle_datos) :

                                        if ($detalle_datos["IDCampoFormularioInvitado"] == '107') {

                                            //VALIDO QUE SOLO PUEDAN HACER INVITACION EN EL MES ACTUAL
                                            $mes = date("m", strtotime($FechaIngreso));
                                            $mes_actual = date("m");
                                            if ($mes_actual != $mes) {
                                                $respuesta["message"] = "Lo sentimos, solo se puede hacer invitaciones para el mes actual!";
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = null;
                                                return $respuesta;
                                            }

                                            if ($detalle_datos["Valor"] == "Disfrute") {
                                                //tipo de reservas normales
                                                $Tipo_Invitacion = "Disfrute";
                                                //contamos las invitaciones diferentes a las gastronomicas
                                                $q_InvitadosPorAccion = $dbo->query("SELECT COUNT(IDSocioInvitado) as CantidadInvitados from SocioInvitado si,Socio s WHERE si.IDSocio = s.IDSocio $whereAccion and TipoInvitacion='Disfrute' and YEAR(si.FechaTrCr) = YEAR(CURRENT_DATE()) and MONTH(si.FechaTrCr) = MONTH(CURRENT_DATE()) ");
                                                $InvitadosPorAccion = $dbo->assoc($q_InvitadosPorAccion);


                                                //primer condicional
                                                $numero_invitados_dia = $InvitadosPorAccion['CantidadInvitados'];
                                                $numero_invitados_dia_permitido_accion = 4;
                                                //segundo condicional
                                                $numero_invitaciones = 1;
                                                $numero_mismo_invitado_mes = 10;
                                                //tercer condicional
                                                $numero_invitados_mes_permitido = 4;
                                                $numero_invitados_mes = $InvitadosPorAccion['CantidadInvitados'];


                                                if ($InvitadosPorAccion['CantidadInvitados'] >= 4) {
                                                    $respuesta["message"] = "Lo sentimos, ha superado el limite de 4 invitaciones (Disfrute) por Acción en el mes!";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                }
                                            } else {
                                                //fecha actual y los limites horarios
                                                $fechaActual = date('Y-m-d');
                                                $fecha = date('Y-m-d 00:00:00', strtotime($fechaActual));
                                                $fecha2 = date('Y-m-d 23:59:59', strtotime($fechaActual));
                                                //tipo de invitacion gastronomica
                                                $Tipo_Invitacion = "Gastronomica";
                                                //contamos que el socio y/o familiares tengan o no las 6 invitaciones permitidas por dia 
                                                $q_InvitadosPorAccion = $dbo->query("SELECT count(si.IDSocioInvitado) as total_invitaciones from SocioInvitado si,Socio s WHERE si.IDSocio = s.IDSocio $whereAccion and si.FechaTrCr BETWEEN '$fecha' and '$fecha2' and si.TipoInvitacion='Gastronomica' ");

                                                $InvitadosPorAccion = $dbo->assoc($q_InvitadosPorAccion);


                                                //primer condicional
                                                $numero_invitados_dia = $InvitadosPorAccion['total_invitaciones'];
                                                $numero_invitados_dia_permitido_accion = 6;
                                                //segundo condicional
                                                $numero_invitaciones = 1;
                                                $numero_mismo_invitado_mes = 10;
                                                //tercer condicional
                                                $numero_invitados_mes_permitido = 180;
                                                $numero_invitados_mes = $InvitadosPorAccion['total_invitaciones'];



                                                // if ($InvitadosPorAccion['total_invitaciones'] >= 6) {
                                                if ($InvitadosPorAccion['total_invitaciones'] >= 6) {
                                                    $respuesta["message"] = "Lo sentimos, ha superado el limite de 6 invitaciones gastronomicas por Acción en el dia!";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                }
                                                /*
                                                
                                                $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $FechaIngreso . "' and IDPais = 2");
                                                $date = strtotime("$FechaIngreso");
                                                $dia = date("N", $date);
                                                if ($dia == 6 or $dia == 7 or !empty($IDFestivo)) {
                                                    $respuesta["message"] = "Lo sentimos, este tipo de invitaciones no esta disponible los fines de semana ni festivos!";
                                                    $respuesta["success"] = false;
                                                    $respuesta["response"] = null;
                                                    return $respuesta;
                                                } */
                                            }
                                        }
                                    endforeach;
                                }


                                // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
                                //$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );

                                if ((int) $numero_invitados_dia < (int) $numero_invitados_dia_permitido_accion || $cumplimiento_obligatorio_limite == "N") {

                                    if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite == "N") {
                                        if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes || $cumplimiento_obligatorio_limite == "N") {

                                            //Verifico que el invitado no este invitado mas de una vez el mismo dia
                                            $sql_invitacion_dia = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '" . $NumeroDocumento . "' and FechaIngreso = '" . $FechaIngreso . "' and IDClub = '" . $IDClub . "'");
                                            $numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);

                                            if ((int) $numero_invitaciones_dia <= 0) {

                                                //Lo creo como invitado
                                                $id_invitado_creado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'");
                                                if ((int) $id_invitado_creado <= 0) {
                                                    $sql_invitado = $dbo->query("INSERT INTO Invitado (IDClub,NumeroDocumento,Nombre) Values ('" . $IDClub . "', '" . $NumeroDocumento . "', '" . $Nombre . "','WebService','" . date("Y-m-d H:i:s") . "')");
                                                    $id_invitado_creado = $dbo->lastID();
                                                }
                                                $sql_datos_invitacion = "SELECT IDSocioInvitado FROM SocioInvitado WHERE NumeroDocumento = '" . $NumeroDocumento . "' Order by IDSocioInvitado DESC LIMIT 1";
                                                $r_datos_invitacion = $dbo->query($sql_datos_invitacion);
                                                $row_datos_invitacion_ant = $dbo->fetchArray($r_datos_invitacion);

                                                $sql_seccion_not = $dbo->query("INSERT INTO SocioInvitado (IDClub, IDInvitado,IDSocio, NumeroDocumento, Nombre,TipoInvitacion, FechaIngreso, Observaciones, UsuarioTrCr, FechaTrCr) Values ('" . $IDClub . "','" . $id_invitado_creado . "','" . $IDSocio . "', '" . $NumeroDocumento . "', '" . $Nombre . "','" . $Tipo_Invitacion . "','" . $FechaIngreso . "','" . $Observaciones . "', '" . $IDUsuario . "','" . date("Y-m-d H:i:s") . "')");

                                                $id_solicitud = $dbo->lastID();

                                                ///Guardo el invitado en la base general de invitados , para guardar la demas informacion
                                                $IDInvitadoGral = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub='" . $IDClub . "'");
                                                if ((int) $IDInvitadoGral <= 0) {
                                                    $inserta_invitado = "INSERT INTO Invitado (IDClub,NumeroDocumento, Nombre, IDEstadoInvitado, UsuarioTrCr, FechaTrCr)
                                                                                        VALUES('" . $IDClub . "', '" . trim($NumeroDocumento) . "','" . strtoupper($Nombre) . "','1','Socio . " . $IDSocio . "','" . date("Y-m-d H:i:s") . "')";
                                                    $dbo->query($inserta_invitado);
                                                    $id_nvo_invitado = $dbo->lastID();
                                                    //Actualizo el dato de la invitacion con el id generado
                                                    $actualiza_inv = "UPDATE SocioInvitado SET IDInvitado = '" . $id_nvo_invitado . "'WHERE IDSocioInvitado = '" . $id_solicitud . "'";
                                                    $dbo->query($actualiza_inv);
                                                }
                                                $IDInvitadoGral = (!empty($IDInvitadoGral)) ? $IDInvitadoGral : $id_nvo_invitado;
                                                //Guardo los datos de los campos
                                                $datos_formulario = json_decode($ValoresFormulario, true);
                                                if (count($datos_formulario) > 0) {
                                                    foreach ($datos_formulario as $detalle_datos) :

                                                        if (isset($files[$detalle_datos["IDCampoFormularioInvitado"]])) {
                                                            $File = $files[$detalle_datos["IDCampoFormularioInvitado"]];

                                                            $tamano_archivo = $File["size"];
                                                            if ($tamano_archivo >= 6000000) {
                                                                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Elarchivosuperaellimitedepesopermitidode6megas,porfavorverifique', LANG);
                                                                $respuesta["success"] = false;
                                                                $respuesta["response"] = null;
                                                                return $respuesta;
                                                            }

                                                            $Upfiles = SIMFile::upload($File, IMGINVITADO_DIR, "DOC");
                                                            if (empty($Upfiles) && !empty($File["name"])) :
                                                                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacarga.Verifiquequeelarchivonocontengaerroresyqueeltipodearchivoseapermitido', LANG);
                                                                $respuesta["success"] = false;
                                                                $respuesta["response"] = null;
                                                                return $respuesta;
                                                            endif;
                                                            $detalle_datos["Valor"] = $Upfiles[0]["innername"];
                                                        }

                                                        $sql_datos_form = $dbo->query("Insert Into InvitadosOtrosDatos (IDInvitacion, IDCampoFormularioInvitado, Valor) Values ('" . $id_solicitud . "','" . $detalle_datos["IDCampoFormularioInvitado"] . "','" . trim($detalle_datos["Valor"]) . "')");
                                                        $dbo->query($sql_datos_form);

                                                        $OtrosDatosFormulario .= " " . $detalle_datos["Valor"];

                                                        if (filter_var(trim($detalle_datos["Valor"]), FILTER_VALIDATE_EMAIL)) {
                                                            $parametros_codigo_qr = $NumeroDocumento;
                                                            SIMUtil::enviar_codigo_qr_invitado($id_solicitud, $parametros_codigo_qr, "Invitado", trim($detalle_datos["Valor"]));
                                                        }

                                                    endforeach;
                                                } else {

                                                    //Verifico si antes tenia otros datos para copiarlo
                                                    if ((int) $row_datos_invitacion_ant["IDSocioInvitado"] > 0) {
                                                        $sql_otros = "SELECT CFI.IDCampoFormularioInvitado, Valor FROM InvitadosOtrosDatos IOD, CampoFormularioInvitado CFI WHERE IOD.IDInvitacion = '$row_datos_invitacion_ant[IDSocioInvitado]' AND CFI.IDClub = '$IDClub' AND IOD.IDCampoFormularioInvitado = CFI.IDCampoFormularioInvitado";
                                                        $r_otros = $dbo->query($sql_otros);
                                                        while ($row_otros = $dbo->fetchArray($r_otros)) {
                                                            $sql_otros_datos = "INSERT INTO InvitadosOtrosDatos (IDInvitacion, IDCampoFormularioInvitado, Valor) VALUES ('" . $id_solicitud . "','" . $row_otros["IDCampoFormularioInvitado"] . "','" . trim($row_otros["Valor"]) . "')";
                                                            $dbo->query($sql_otros_datos);

                                                            if (filter_var(trim($row_otros["Valor"]), FILTER_VALIDATE_EMAIL)) {
                                                                $parametros_codigo_qr = $NumeroDocumento;
                                                                SIMUtil::enviar_codigo_qr_invitado($id_solicitud, $parametros_codigo_qr, "Invitado", trim($row_otros["Valor"]));
                                                            }
                                                        }
                                                    }
                                                }

                                                if (!empty($mensajesadicionales)) :
                                                    $mensaje = "Invitado Guardado: " . $mensajesadicionales;
                                                elseif ($IDClub == 155) :
                                                    $mensaje = "Jugador agregado con éxito";
                                                else :
                                                    $mensaje = "Invitado Guardado";
                                                endif;

                                                if ($IDClub == 151) {
                                                    $mensaje = "Saved";
                                                }

                                                // Regla invitados para el club Cantegril
                                                if ($IDClub == 185) {
                                                    $FechaNacimientoSocio = $datos_socio['FechaNacimiento'];
                                                    $Edad = SIMUtil::Calcular_Edad($FechaNacimientoSocio);

                                                    if ($Edad < 18) {
                                                        $mensaje = "Esta invitación tiene costo extra, debe acercarse a realizar el pago en recepción.";
                                                    }
                                                }
                                                // Fin Regla invitados para el club Cantegril

                                                // Envio invitacion a Arboretto
                                                if ($IDClub == 223) {
                                                    // if ($IDClub == 8) {
                                                    include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');

                                                    $Email = "";
                                                    $datos_formulario = json_decode($ValoresFormulario, true);
                                                    if (count($datos_formulario) > 0) {
                                                        foreach ($datos_formulario as $detalle_datos) :
                                                            if (filter_var($detalle_datos['valor'], FILTER_VALIDATE_EMAIL)) {
                                                                $Email = $detalle_datos['valor'];
                                                            }
                                                        endforeach;
                                                    }
                                                    // Enviar datos a Arboretto
                                                    $datos_socio['IDSocioSistemaExterno'] = ($datos_socio['IDSocioSistemaExterno'] > 0) ? $datos_socio['IDSocioSistemaExterno'] : 2840;
                                                    $JSon = '{
                                                            "receptionistId": "' . $datos_socio['IDSocioSistemaExterno'] . '",
                                                            "visitStartTime": "' . $FechaIngreso . 'T00:00:00+08:00",
                                                            "visitEndTime": "' . $FechaIngreso . 'T23:00:00+08:00",
                                                            "visitPurposeType": 2,
                                                            "visitPurpose": "Visitante",
                                                            "visitorInfoList": [
                                                                {
                                                                    "VisitorInfo": {
                                                                        "visitorFamilyName": "' . $Nombre . '",
                                                                        "visitorGivenName": "Visitante",
                                                                        "gender": 1,
                                                                        "email": "' . $Email . '",
                                                                        "phoneNo": "",
                                                                        "plateNo": "",
                                                                        "companyName": "",
                                                                        "certificateType": 111,
                                                                        "certificateNo": "' . $NumeroDocumento . '",
                                                                        "remark": "Creado por ' . $datos_socio['Nombre'] . ' ' . $datos_socio['Apellido'] . ' ' . $datos_socio['Accion'] . '"
                                                                    }
                                                                }
                                                            ]
                                                        }';
                                                    $respuesta_arboretto = SIMWebServiceArboretto::crear_visitante($IDClub, $row_socio["IDSocio"], "", $JSon);

                                                    $respuesta_arboretto = json_decode($respuesta_arboretto, true);

                                                    if ($respuesta_arboretto['msg'] == "Success") {
                                                        $IdVisitanteExterno = $respuesta_arboretto['data']['appointRecordId'];
                                                        $sql_update_invitado = "update Invitado set NumeroCarnet='$IdVisitanteExterno' where IDClub = $IDClub and IDInvitado = $IDInvitadoGral";
                                                        $dbo->query($sql_update_invitado);
                                                    }
                                                }
                                                $respuesta["message"] = $mensaje;
                                                $respuesta["success"] = true;
                                                $respuesta["response"] = null;

                                                //envio notificacion
                                                SIMUtil::notificar_nuevo_invitado($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $id_solicitud);
                                            } else {
                                                $respuesta["message"] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";
                                                $respuesta["success"] = false;
                                                $respuesta["response"] = null;
                                            }
                                        } else {
                                            $respuesta["message"] = "Lo sentimos supera el numero maximo de " . $numero_invitados_mes_permitido . " invitaciones por mes";
                                            $respuesta["success"] = false;
                                            $respuesta["response"] = null;
                                        }
                                    } else {
                                        if ($IDClub == 11) {
                                            $respuesta["message"] = "El invitado que esta seleccionando, supera el máximo de ingresos en el mes.";
                                        } else {
                                            $respuesta["message"] = "Lo sentimos, esta persona ha sido invitadas mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
                                        }

                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                    }
                                } else {
                                    $respuesta["message"] = "Lo sentimos, supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia.";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                }
                            } else {
                                $respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            }
                        } else {
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } else {
                        $respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Invitado bloqueado razón:\n" . $bloqueado[Razon];
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "14." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        }

        return $respuesta;
    }

    public function set_cargar_excel_invitadosv1($IDClub, $IDSocio, $IDUsuario, $FILES)
    {

        $dbo = SIMDB::get();
        $HTML = "";
        $CantidadIngresada = 0;
        $CantidadRegistros = 0;
        $filedir = SOCIOPLANO_DIR;
        $nuevo_nombre = rand(0, 1000000) . "_" . date("Y-m-d") . "_" . $FILES['Archivo']['name'];
        if (copy($FILES['Archivo']['tmp_name'], $filedir . $nuevo_nombre)) :

            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";

            $archivo = $filedir . $nuevo_nombre;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            for ($row = 2; $row <= $highestRow; $row++) :

                $CantidadRegistros++;

                $DocumentoInvitado = $sheet->getCell("A" . $row)->getValue();
                $Nombre = $sheet->getCell("B" . $row)->getValue();
                $FechaIngreso = $sheet->getCell("C" . $row)->getFormattedValue();

                if (!empty($DocumentoInvitado)) :
                    if (!empty($FechaIngreso)) :
                        if (!empty($Nombre)) :

                            $pos = strpos($FechaIngreso, "/");
                            if ($pos === false) {
                                //echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
                            } else {
                                $array_nueva_fecha = explode("/", $FechaIngreso);

                                if ((int)$array_nueva_fecha["0"] < 10)
                                    $mas = "0";

                                $FechaIngreso = $array_nueva_fecha[2] . "-" . $mas . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
                            }

                            if (strlen($FechaIngreso) == 10) :

                                $respuestainvi = SIMWebServiceAccesos::set_invitado($IDClub, $IDSocio, $DocumentoInvitado, $Nombre, $FechaIngreso, "");

                                if ($respuestainvi[success] == false) :
                                    $HTML .= "<br>" . $respuestainvi[message];
                                else :
                                    $CantidadIngresada++;
                                endif;

                            else :
                                $HTML .= "<br>" . "Las fechas tienen un formato invalido: FILA" . $row;
                            endif;
                        else :
                            $HTML .= "<br>" . "Nombre y Apellido son obligatorios, FILA: " . $row;
                        endif;
                    else :
                        $HTML .= "<br>" . "Las fechas de ingreso y salida son obligatorios, FILA: " . $row;
                    endif;
                else :
                    $HTML .= "<br>" . "El numero de documento debe ser numerico, FILA:" . $row;
                endif;
            endfor;

            $HTML .= "<br>Registros: $CantidadRegistros <br> Ingresados: $CantidadIngresada";

            $info[ResumenHtml] = $HTML;

            $respuesta["message"] = "Invitaciones cargadas con exito";
            $respuesta["success"] = true;
            $respuesta["response"] = $info;

        else :

            $errors = error_get_last();
            $error1 = "COPY ERROR: " . $errors['type'];
            $error2 = "<br/>\n" . $errors['message'];

            $respuesta["message"] = $error1 . $error2;
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        endif;

        return $respuesta;
    }

    public function set_cargar_excel_acceso($IDClub, $IDSocio, $IDUsuario, $FILES)
    {
        $dbo = SIMDB::get();
        $HTML = "";
        $CantidadIngresada = 0;
        $CantidadRegistros = 0;
        $filedir = SOCIOPLANO_DIR;
        $nuevo_nombre = rand(0, 1000000) . "_" . date("Y-m-d") . "_" . $FILES['Archivo']['name'];
        if (copy($FILES['Archivo']['tmp_name'], $filedir . $nuevo_nombre)) :

            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";

            $archivo = $filedir . $nuevo_nombre;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            for ($row = 2; $row <= $highestRow; $row++) :

                $CantidadRegistros++;

                $FechaIngreso = $sheet->getCell("A" . $row)->getFormattedValue();
                $FechaSalida = $sheet->getCell("B" . $row)->getFormattedValue();
                $DocumentoInvitado = utf8_encode($sheet->getCell("C" . $row)->getValue());
                $Nombre = utf8_encode($sheet->getCell("D" . $row)->getValue());
                $Apellido = $sheet->getCell("E" . $row)->getValue();
                $Email = $sheet->getCell("F" . $row)->getValue();
                $Telefono = $sheet->getCell("G" . $row)->getValue();
                $TipoSangre = $sheet->getCell("H" . $row)->getValue();
                $Placa = $sheet->getCell("I" . $row)->getValue();

                if (is_numeric($DocumentoInvitado)) :
                    if (!empty($FechaIngreso) && !empty($FechaSalida)) :
                        if (!empty($Nombre) && !empty($Apellido)) :

                            $pos = strpos($FechaIngreso, "/");
                            if ($pos === false) {
                                //echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
                            } else {
                                $array_nueva_fecha = explode("/", $FechaIngreso);

                                if ((int)$array_nueva_fecha["0"] < 10)
                                    $mas = "0";

                                $FechaIngreso = $array_nueva_fecha[2] . "-" . $mas . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
                            }

                            $pos = strpos($FechaSalida, "/");
                            if ($pos === false) {
                                //echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
                            } else {
                                $array_nueva_fecha = explode("/", $FechaSalida);

                                if ((int)$array_nueva_fecha["0"] < 10)
                                    $mas = "0";

                                $FechaSalida = $array_nueva_fecha[2] . "-" . $mas . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
                            }

                            if (strlen($FechaIngreso) == 10 && strlen($FechaSalida) == 10) {

                                $array_datos = array();

                                $array_datos_invitado["IDTipoDocumento"] = "2";
                                $array_datos_invitado["NumeroDocumento"] = $DocumentoInvitado;
                                $array_datos_invitado["Nombre"] = $Nombre;
                                $array_datos_invitado["Apellido"] = $Apellido;
                                $array_datos_invitado["Email"] = $Email;
                                $array_datos_invitado["TipoInvitado"] = $TipoSangre;
                                $array_datos_invitado["Placa"] = $Placa;
                                $array_datos_invitado["CabezaInvitacion"] = "N";
                                $array_datos_invitado["TipoSangre"] = $TipoSangre;
                                array_push($array_datos, $array_datos_invitado);
                                $DatosInvitado = json_encode($array_datos);

                                $respuestainvi = SIMWebServiceAccesos::set_autorizacion_invitado($IDClub, $IDSocio, $FechaIngreso, $FechaSalida, $DatosInvitado, "", "", "S");

                                if ($respuestainvi[success] == false) :
                                    $HTML .= "<br>" . $respuestainvi[message];
                                else :
                                    $CantidadIngresada++;
                                endif;
                            } else {
                                $HTML .= "<br>" . "Las fechas tienen un formato invalido: FILA" . $row;
                            }
                        else :
                            $HTML .= "<br>" . "Nombre y Apellido son obligatorios, FILA: " . $row;
                        endif;
                    else :
                        $HTML .= "<br>" . "Las fechas de ingreso y salida son obligatorios, FILA: " . $row;
                    endif;
                else :
                    $HTML .= "<br>" . "El numero de documento debe ser numerico, FILA:" . $row;
                endif;
            endfor;

            $HTML .= "<br>Registros: $CantidadRegistros <br> Ingresados: $CantidadIngresada";

            $info[ResumenHtml] = $HTML;

            $respuesta["message"] = "Invitaciones cargadas con exito";
            $respuesta["success"] = true;
            $respuesta["response"] = $info;

        else :
            $respuesta["message"] = "El archivo no se pudo cargar con exito";
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        endif;

        return $respuesta;
    }

    public function set_cargar_excel_contratista($IDClub, $IDSocio, $IDUsuario, $FILES)
    {
        $dbo = SIMDB::get();
        $HTML = "";
        $CantidadIngresada = 0;
        $CantidadRegistros = 0;
        $filedir = SOCIOPLANO_DIR;
        $nuevo_nombre = rand(0, 1000000) . "_" . date("Y-m-d") . "_" . $FILES['Archivo']['name'];

        if (copy($FILES['Archivo']['tmp_name'], $filedir . $nuevo_nombre)) :

            require_once LIBDIR . "excel/PHPExcel-1.8/Classes/PHPExcel.php";

            $archivo = $filedir . $nuevo_nombre;
            $inputFileType = PHPExcel_IOFactory::identify($archivo);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivo);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 2; $row <= $highestRow; $row++) :

                $CantidadRegistros++;

                $FechaIngreso = $sheet->getCell("A" . $row)->getFormattedValue();
                $FechaSalida = $sheet->getCell("B" . $row)->getFormattedValue();
                $DocumentoInvitado = $sheet->getCell("C" . $row)->getValue();
                $Nombre = $sheet->getCell("D" . $row)->getValue();
                $Apellido = $sheet->getCell("E" . $row)->getValue();
                $Email = $sheet->getCell("F" . $row)->getValue();
                $Telefono = $sheet->getCell("G" . $row)->getValue();
                $TipoSangre = $sheet->getCell("H" . $row)->getValue();
                $Placa = $sheet->getCell("I" . $row)->getValue();
                $Predio = $sheet->getCell("J" . $row)->getValue();
                $Arl = $sheet->getCell("K" . $row)->getValue();
                $Eps = $sheet->getCell("L" . $row)->getValue();
                $HoraInicio = $sheet->getCell("M" . $row)->getValue();
                $HoraSalida = $sheet->getCell("N" . $row)->getValue();

                $TipoAutorizacion = "Invitacion";

                if (!empty($DocumentoInvitado)) :
                    if (!empty($FechaIngreso) && !empty($FechaSalida)) :
                        if (!empty($Nombre) && !empty($Apellido)) :

                            $pos = strpos($FechaIngreso, "/");
                            if ($pos === false) {
                                //echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
                            } else {
                                $array_nueva_fecha = explode("/", $FechaIngreso);

                                if ((int)$array_nueva_fecha["0"] < 10)
                                    $mas = "0";

                                $FechaIngreso = $array_nueva_fecha[2] . "-" . $mas . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
                            }

                            $pos = strpos($FechaSalida, "/");
                            if ($pos === false) {
                                //echo "La cadena '$findme' no fue encontrada en la cadena '$mystring'";
                            } else {
                                $array_nueva_fecha = explode("/", $FechaSalida);

                                if ((int)$array_nueva_fecha["0"] < 10)
                                    $mas = "0";

                                $FechaSalida = $array_nueva_fecha[2] . "-" . $mas . $array_nueva_fecha["0"] . "-" . $array_nueva_fecha["1"];
                            }

                            if (strlen($FechaIngreso) == 10 && strlen($FechaSalida) == 10) :

                                $respuestainvi = SIMWebServiceAccesos::set_autorizacion_contratista($IDClub, $IDSocio, $TipoAutorizacion, $FechaIngreso, $FechaSalida, "1", $DocumentoInvitado, $Nombre, $Apellido, $Email, $Placa, "S", $HoraInicio, $HoraSalida, $Observaciones, $IDUsuario, $Telefono, $FechaNacimiento, $TipoSangre, $Predio, $Arl, $Eps, $ObservacionSocio);

                                if ($respuestainvi[success] == false) :
                                    $HTML .= "<br>" . $respuestainvi[message];
                                else :
                                    $CantidadIngresada++;
                                endif;

                            else :
                                $HTML .= "<br>" . "Las fechas tienen un formato invalido: FILA" . $row;
                            endif;
                        else :
                            $HTML .= "<br>" . "Nombre y Apellido son obligatorios, FILA: " . $row;
                        endif;
                    else :
                        $HTML .= "<br>" . "Las fechas de ingreso y salida son obligatorios, FILA: " . $row;
                    endif;
                else :
                    $HTML .= "<br>" . "El numero de documento debe ser numerico, FILA:" . $row;
                endif;
            endfor;

            $HTML .= "<br>Registros: $CantidadRegistros <br> Ingresados: $CantidadIngresada";

            $info[ResumenHtml] = $HTML;

            $respuesta["message"] = "Invitaciones cargadas con exito";
            $respuesta["success"] = true;
            $respuesta["response"] = $info;

        else :
            $respuesta["message"] = "El archivo no se pudo cargar con exito";
            $respuesta["success"] = false;
            $respuesta["response"] = "";
        endif;

        return $respuesta;
    }

    public function get_mis_autorizaciones_contratista($IDClub, $IDSocio, $Tag, $FechaIngreso, $Tiempo = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDSocio)) {

            $config_club = $dbo->fetchAll("ConfiguracionClub ", " IDClub = '" . $IDClub . "' ", "array");

            if (!empty($Tag)) :
                // Consulto lo invitados con este dato
                $sql_busca_invitado = "Select * From Invitado Where NumeroDocumento like '%" . $Tag . "%' or Nombre like '%" . $Tag . "%' or Apellido like '%" . $Tag . "%'  or Telefono like '%" . $Tag . "%' or Email like '%" . $Tag . "%' ";
                $result_busca_invitado = $dbo->query($sql_busca_invitado);
                while ($row_busca_invitado = $dbo->fetchArray($result_busca_invitado)) :
                    $array_condiciones_invitado[] = " IDInvitado  = '" . $row_busca_invitado["IDInvitado"] . "'";
                endwhile;
            endif;

            if (count($array_condiciones_invitado) > 0) :
                $condiciones_invitado = "and (" . implode(" or ", $array_condiciones_invitado) . ")";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = "and " . implode(" and ", $array_condiciones);
            endif;

            if ($Tiempo == "Futuro" || $Tiempo == "") :
                //$condicion_tiempo = " and FechaInicio >= CURDATE() ";
                $condicion_tiempo = " and FechaFin >= CURDATE() ";
            elseif ($Tiempo == "Pasado") :
                $condicion_tiempo = " and (FechaInicio <= CURDATE() or FechaInicio >= CURDATE() ) ";
            endif;

            $sql = "SELECT * FROM SocioAutorizacion WHERE IDClub = '$IDClub' AND IDSocio = '$IDSocio' " . $condiciones . " " . $condiciones_invitado . " " . $condicion_tiempo . "  and Mostrar <> 'N' ORDER BY FechaInicio Desc ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $elemento["IDClub"] = $IDClub;
                    $elemento["IDAutorizacion"] = $r["IDSocioAutorizacion"];
                    //Consulto datos invitado
                    $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $r["IDInvitado"] . "' ", "array");
                    $elemento["Nombre"] = $datos_invitado["Nombre"];
                    $elemento["Apellido"] = $datos_invitado["Apellido"];
                    $elemento["NumeroDocumento"] = "$datos_invitado[NumeroDocumento]";
                    $elemento["TipoAutorizacion"] = $r["TipoAutorizacion"];
                    $elemento["Email"] = $datos_invitado["Email"];
                    unset($response_tipodoc_asociado);
                    $response_tipodoc_asociado = array();
                    $tipodoc_asociado["IDTipoDocumento"] = (int) $datos_invitado["IDTipoDocumento"];
                    $tipodoc_asociado["Nombre"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                    array_push($response_tipodoc_asociado, $tipodoc_asociado);
                    $elemento["TipoDocumento"] = $response_tipodoc_asociado;

                    $elemento["FechaIngreso"] = $r["FechaInicio"];
                    $elemento["FechaSalida"] = $r["FechaFin"];

                    $elemento["HoraIngreso"] = $r["HoraInicio"];
                    $elemento["HoraSalida"] = $r["HoraFin"];
                    //Consulto datos vehiculo
                    $datos_vehiculo = $dbo->fetchAll("Vehiculo", " IDVehiculo = '" . $r["IDVehiculo"] . "' ", "array");
                    $elemento["Vehiculo"] = $datos_vehiculo["Placa"];
                    $elemento["Observaciones"] = $r["ObservacionSocio"];
                    $elemento["DiasCheckbox"] = $r["DiasCheckbox"];

                    //Reviso si ya se le dio entrada
                    $presalida = $dbo->getFields("Club", "Presalida", "IDClub = '" . $IDClub . "'");
                    if ($presalida == "S") {
                        $HabilitaPresalida = $dbo->getFields("LogAcceso", "Entrada", "IDClub = '" . $IDClub . "' and IDInvitacion = '" . $r["IDSocioAutorizacion"] . "' and Fechaingreso >= '" . date("Y-m-d") . " 00:00:00' ");
                    }

                    if ($HabilitaPresalida != "S") {
                        $HabilitaPresalida = "N";
                    }

                    if ($config_club["VerQrContratistas"] == "N") {
                        $Urlinvitacion = "";
                    } else {
                        $Urlinvitacion = URLROOT . "admin/lib/pdf_invitacion.php?club=$IDClub&seccion=socioautorizacion&id=" . $r["IDSocioAutorizacion"];
                    }

                    $elemento["HabilitaPresalida"] = $HabilitaPresalida;
                    $elemento["UrlInvitacion"] = $Urlinvitacion;
                    $elemento["TipoIngreso"] = $r['TipoAutorizacion'];

                    // Precarga Campos dinamicos

                    // $CampoFormularioContratista = $dbo->fetchAll("CampoFormularioContratista", "IDClub = $IDClub and Publicar = 'S'", "array");
                    $sqlCampoFormularioContratista = "select * from CampoFormularioContratista  where IDClub = $IDClub and Publicar = 'S'";
                    $qCampoFormularioContratista = $dbo->query($sqlCampoFormularioContratista);
                    $arr_CamposFormulario = array();
                    while ($CampoFormularioContratista = $dbo->assoc($qCampoFormularioContratista)) {
                        $Respuesta = $dbo->getFields("SocioAutorizacionOtrosDatos", "Valor", "IDSocioAutorizacion=" . $r["IDSocioAutorizacion"] . " and IDCampoFormularioContratista = " . $CampoFormularioContratista['IDCampoFormularioContratista']);
                        $CampoFormulario['IDCampoFormularioContratista'] = $CampoFormularioContratista['IDCampoFormularioContratista'];
                        $CampoFormulario['TipoCampo'] = $CampoFormularioContratista['TipoCampo'];
                        $CampoFormulario['EtiquetaCampo'] = $CampoFormularioContratista['EtiquetaCampo'];
                        $CampoFormulario['Obligatorio'] = $CampoFormularioContratista['Obligatorio'];
                        $CampoFormulario['Valores'] = $CampoFormularioContratista['Valores'];
                        $CampoFormulario['ValorActual'] = $Respuesta;
                        unset($Respuesta);
                        array_push($arr_CamposFormulario, $CampoFormulario);
                    }
                    $elemento["FormularioDinamico"] = $arr_CamposFormulario;


                    // Fin Precarga Campos dinamicos

                    array_push($response, $elemento);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "2." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_contratista_update_autorizacion($IDClub, $IDSocio, $IDInvitacion, $TipoAutorizacion, $FechaIngreso, $FechaSalida, $TipoDocumento, $NumeroDocumento, $Nombre, $Apellido, $Email, $Placa, $Admin = "", $HoraInicio = "", $HoraSalida = "", $Observaciones = "", $IDUsuario = "", $Telefono = "", $FechaNacimiento = "", $TipoSangre = "", $Predio = "", $Arl = "", $Eps = "", $VencimientoArl = "", $VencimientoEps = "", $ObservacionSocio = "", $ValoresFormulario = "", $CodigoAutorizacion = "", $ArlFile = "", $DiasCheckbox = "")
    {
        $dbo = &SIMDB::get();

        $bloque_administrativo = SIMWebServiceAccesos::verifica_bloqueo_invitado($NumeroDocumento, $IDClub);
        if ($bloque_administrativo == "S") :
            $respuesta["message"] = "Lo sentimos el contratista tiene un bloqueo por parte del club, no es posible realizar la autorizacion:" . $NumeroDocumento;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        endif;

        if (!empty($IDInvitacion) && !empty($FechaIngreso) && !empty($FechaSalida) && !empty($TipoAutorizacion) && !empty($TipoDocumento) && !empty($NumeroDocumento) && !empty($Nombre) && !empty($Apellido)) {

            $hoy = date("Y-m-d");
            if (strtotime($FechaIngreso) >= strtotime($hoy) && strtotime($FechaSalida) >= strtotime($FechaIngreso)) {

                $NumeroDocumento = str_replace(".", "", $NumeroDocumento);
                $NumeroDocumento = trim(str_replace(",", "", $NumeroDocumento));

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    // Consulto las invitaciones que puede hacer el socio
                    $numero_invitados_dia_permitido = $dbo->getFields("Socio", "NumeroAccesos", "IDSocio = '" . $IDSocio . "'");

                    if ((int) $numero_invitados_dia_permitido > 0) {

                        //Consulto cuantas veces la persona ha sido invitada en el mes
                        $mes_invitacion = substr($FechaIngreso, 5, 2);
                        $year_invitacion = substr($FechaIngreso, 0, 4);
                        $dia_invitacion = substr($FechaIngreso, 8, 2);

                        //verifico si el invitado ya esta creado
                        $id_invitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' ");
                        //Si el invitado no existe en la tabla maestra lo creo
                        if (empty($id_invitado) || (int) $id_invitado == 0) :
                            $inserta_invitado = "Insert Into Invitado (IDClub, IDTipoDocumento, NumeroDocumento, Nombre, Apellido, Email, Telefono, FechaNacimiento, TipoSangre, Predio, UsuarioTrCr, FechaTrCr)
                                                                                    Values('" . $IDClub . "','" . $IDTipoDocumento . "','" . (int) $NumeroDocumento . "','" . strtoupper($Nombre) . "','" . strtoupper($Apellido) . "','" . $Email . "','" . $Telefono . "','" . $FechaNacimiento . "'
                                                                                    ,'" . $TipoSangre . "','" . $Predio . "','App','" . date("Y-m-d H:i:s") . "')";
                            $dbo->query($inserta_invitado);
                            $id_invitado = $dbo->lastID();
                        else :

                            $CampoObservacion = "";

                            if (!empty($Observaciones)) {
                                $CampoObservacion = " ,ObservacionGeneral = '" . $Observaciones . "'";
                            }

                            if (!empty($FechaNacimiento) && $FechaNacimiento != "0000-00-00") {
                                $CampoObservacion .= " ,FechaNacimiento = '" . $FechaNacimiento . "'";
                            }

                            if (!empty($TipoSangre)) {
                                $CampoObservacion .= " , TipoSangre = '" . $TipoSangre . "'";
                            }

                            if (!empty($Telefono)) {
                                $CampoObservacion .= " , Telefono = '" . $Telefono . "'";
                            }

                            if (!empty($Predio)) {
                                $CampoObservacion .= " , Predio = '" . $Predio . "'";
                            }

                            if (!empty($Arl)) {
                                $CampoObservacion .= " , ARL = '" . $Arl . "'";
                            }

                            if (!empty($Eps)) {
                                $CampoObservacion .= " , EPS = '" . $Eps . "'";
                            }

                            if (!empty($VencimientoArl)) {
                                $CampoObservacion .= " , FechaVencimientoArl = '" . $VencimientoArl . "'";
                            }

                            if (!empty($VencimientoEps)) {
                                $CampoObservacion .= " , FechaVencimientoEps = '" . $VencimientoEps . "'";
                            }

                            if (!empty($Email)) {
                                $CampoObservacion .= " , Email = '" . $Email . "'";
                            }

                            $sql_invitado_update = $dbo->query("UPDATE Invitado
                                                                                SET ARLFILE = '" . $ArlFile . "',IDTipoDocumento = '" . $TipoDocumento . "', NumeroDocumento = '" . $NumeroDocumento . "', Nombre = '" . strtoupper($Nombre) . "',
                                                                                Apellido = '" . strtoupper($Apellido) . "', Email='" . $Email . "' " . $CampoObservacion . "
                                                                                Where IDInvitado = '" . $id_invitado . "'");
                        endif;

                        $sql_numero_invitacion = $dbo->query("Select * From SocioAutorizacion Where IDInvitado = '" . $id_invitado . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "'");
                        $numero_invitaciones = $dbo->rows($sql_numero_invitacion);
                        //Consulto cuantas personas ha invitado el socio en el mes
                        $sql_invitados_mes = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '" . $IDSocio . "' and YEAR(FechaInicio) = '" . $year_invitacion . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "'");
                        $numero_invitados_mes = $dbo->rows($sql_invitados_mes);
                        //Consulto cuantas personas ha invitado el socio en el dia
                        $sql_invitados_dia = $dbo->query("Select * From SocioAutorizacion Where IDSocio = '" . $IDSocio . "' and YEAR(FechaInicio) = '" . $year_invitacion . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and DAY(FechaInicio) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                        $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                        $numero_invitados_mes_permitido = 50000;
                        $numero_mismo_invitado_mes = "30000";
                        $cumplimiento_obligatorio_limite = "S";

                        // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
                        //$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );

                        if ((int) $numero_invitados_dia < (int) $numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite == "N") {
                            if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite == "N") {
                                if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes || $cumplimiento_obligatorio_limite == "N") {
                                    //Verifico que el invitado no este invitado mas de una vez el mismo dia

                                    $sql_invitacion_dia = $dbo->query("Select * From SocioAutorizacion Where IDInvitado = '" . $id_invitado . "' and FechaInicio = '" . $FechaIngreso . "'");
                                    $numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
                                    if ((int) $numero_invitaciones_dia <= 100) {

                                        //verifico si el vehiculo ya esta creado
                                        if (!empty($Placa)) :
                                            $id_vehiculo = $dbo->getFields("Vehiculo", "IDVehiculo", "Placa = '" . $Placa . "'");
                                            //Si el vehiculo no existe en la tabla maestra lo creo
                                            if (empty($id_vehiculo) || (int) $id_vehiculo == 0) :
                                                $inserta_vehiculo = "INSERT INTO Vehiculo (IDInvitado, Placa) Values('" . $id_invitado . "','" . $Placa . "')";
                                                $dbo->query($inserta_vehiculo);
                                                $id_vehiculo = $dbo->lastID();
                                            endif;
                                        endif;

                                        //Guardo los datos de los campos
                                        $ValoresFormulario = trim(preg_replace('/\s+/', ' ', $ValoresFormulario));
                                        $datos_formulario = json_decode($ValoresFormulario, true);
                                        if (count($datos_formulario) > 0) :
                                            foreach ($datos_formulario as $detalle_datos) :
                                                $sql_datos_form = $dbo->query("UPDATE SocioAutorizacionOtrosDatos SET IDCampoFormularioContratista = '$detalle_datos[IDCampoFormularioContratista]', Valor = '$detalle_datos[Valor]' WHERE IDSocioAutorizacion = $IDInvitacion");
                                                $OtrosDatosFormulario .= " " . $detalle_datos["Valor"];

                                                if ($IDClub == 35) {
                                                    $ObservacionSocio = $detalle_datos["Valor"];
                                                    $actualizo = " UPDATE SocioAutorizacion SET ObservacionSocio = '" . $ObservacionSocio . "' WHERE IDSocioAutorizacion = '" . $id_invitado_inserta . "'";
                                                    // $ejecuta = $dbo->query($actualizo);
                                                }
                                            endforeach;
                                        endif;

                                        if (!empty($IDUsuario)) {
                                            $creado_por = $IDUsuario;
                                        } else {
                                            $creado_por = "Socio";
                                        }

                                        //Actualizo invitacion
                                        $sqlActualiza = "UPDATE SocioAutorizacion SET 
                                                                IDVehiculo = '$id_vehiculo', TipoAutorizacion = '$TipoAutorizacion', 
                                                                FechaInicio = '$FechaIngreso', HoraInicio = '$HoraInicio', FechaFin = '$FechaSalida', HoraFin = '$HoraSalida', Predio = '$Predio', ObservacionSocio = '$ObservacionSocio', 
                                                                CodigoAutorizacion = '$CodigoAutorizacion', Dias = '$DiasCheckbox', 
                                                                UsuarioTrEd = '$creado_por', FechaTrEd = '" . date("Y-m-d H:i:s") . "' 
                                                                WHERE IDSocioAutorizacion = '$IDInvitacion'";

                                        $sql_inserta_inv = $dbo->query($sqlActualiza);

                                        $respuesta["message"] = "Actualizado";
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                    } else {
                                        $respuesta["message"] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                    }
                                } else {
                                    $respuesta["message"] = "Lo sentimos supera el numero maximo de " . $numero_invitados_mes_permitido . " invitaciones por mes";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                }
                            } else {
                                $respuesta["message"] = "Lo sentimos, esta persona ha sido invitadas mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            }
                        } else {
                            $respuesta["message"] = "Lo sentimos, supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } else {
                        $respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "SA." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_autorizacion_invitado_update($IDClub, $IDSocio, $IDInvitacion, $FechaIngreso, $FechaSalida, $DatosInvitado, $ValoresFormulario, $DiasCheckbox = "")
    {

        $dbo = &SIMDB::get();

        $datos_invitado = json_decode($DatosInvitado, true);

        if (($IDClub == 7 || $IDClub == 44) && (date('w', strtotime($FechaIngreso)) == 0 || date('w', strtotime($FechaIngreso)) == 6 || date('w', strtotime($FechaIngreso)) == 100)) {
            $respuesta["message"] = "Lo sentimos, no es posible invitar en el dia seleccionado";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if ($IDClub == 16000 && (date('w', strtotime($FechaIngreso)) == 0 || date('w', strtotime($FechaIngreso)) == 6 || date('w', strtotime($FechaSalida)) == 0 || date('w', strtotime($FechaSalida)) == 6)) {
            $respuesta["message"] = "Lo sentimos, no es posible invitar los fines de semana";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $datos_invitado = json_decode($DatosInvitado, true);
        $validaDocumento = str_replace(SIMResources::$abecedario, 1,  $datos_invitado[0]["NumeroDocumento"]);
        if ((int)$validaDocumento == 0) {
            $respuesta["message"] = "El numero de documento del invitado no es valido.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {

            if (!empty($FechaIngreso) && !empty($FechaSalida) && !empty($IDInvitacion) && count($datos_invitado) > 0) {

                $hoy = date("Y-m-d");
                if (strtotime($FechaIngreso) >= strtotime($hoy) && strtotime($FechaSalida) >= strtotime($FechaIngreso)) {

                    //verifico que el socio exista y pertenezca al club
                    $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                    if (!empty($id_socio)) {

                        // Consulto las invitaciones que puede hacer el socio
                        $numero_invitados_dia_permitido = $dbo->getFields("Socio", "NumeroAccesos", "IDSocio = '" . $IDSocio . "'");

                        if ((int) $numero_invitados_dia_permitido > 0) {

                            //Consulto cuantas veces la persona ha sido invitada en el mes
                            $mes_invitacion = substr($FechaIngreso, 5, 2);
                            $year_invitacion = substr($FechaIngreso, 0, 4);
                            $dia_invitacion = substr($FechaIngreso, 8, 2);

                            //Recorro los datos de los invitados
                            if (count($datos_invitado) > 0) :
                                //Borro las invitaciones para volverlas a crear
                                $datos_invitacion_especial = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array");
                                //$sql_borra_inv = $dbo->query("Delete From SocioInvitadoEspecial Where IDPadre = '".$datos_invitacion_especial["IDInvitado"]."'");
                                //$sql_borra_inv = $dbo->query("Delete From SocioInvitadoEspecial Where IDSocioInvitadoEspecial = '".$IDInvitacion."'");

                                foreach ($datos_invitado as $detalle_datos) :
                                    //$IDInvitacion = $detalle_datos["IDInvitacion"];
                                    $IDTipoDocumento = $detalle_datos["IDTipoDocumento"];
                                    $NumeroDocumento = $detalle_datos["NumeroDocumento"];
                                    $Nombre = $detalle_datos["Nombre"];
                                    $Apellido = $detalle_datos["Apellido"];
                                    $Email = $detalle_datos["Email"];
                                    $TipoInvitado = $detalle_datos["TipoInvitado"];
                                    $Placa = $detalle_datos["Placa"];
                                    $CabezaInvitacion = $detalle_datos["CabezaInvitacion"];
                                    $MenorEdad = $detalle_datos["MenorEdad"];

                                    $NumeroDocumento = str_replace(".", "", $NumeroDocumento);
                                    $NumeroDocumento = str_replace(",", "", $NumeroDocumento);

                                    if ($MenorEdad == "S" || (empty($IDTipoDocumento) && empty($NumeroDocumento) && empty($Email))) :
                                        $NumeroDocumento = "MenorEdad" . $IDClub . rand(1, 1000000000);
                                        $IDTipoDocumento = 1;
                                    else :
                                        if ($IDTipoDocumento == "4") {
                                            $NumeroDocumento =  $NumeroDocumento;
                                        } else {
                                            $NumeroDocumento = (int) $NumeroDocumento;
                                        }
                                    endif;

                                    //verifico si el invitado ya esta creado
                                    $id_invitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' ");
                                    //Si el invitado no existe en la tabla maestra lo creo
                                    if (empty($id_invitado) || (int) $id_invitado == 0) :
                                        $inserta_invitado = "Insert Into Invitado (IDCLub, IDTipoDocumento, NumeroDocumento, Nombre, Apellido, Email, MenorEdad, UsuarioTrCr, FechaTrCr)
                                                                                                            Values('" . $IDClub . "','" . $IDTipoDocumento . "','" . $NumeroDocumento . "','" . strtoupper($Nombre) . "','" . strtoupper($Apellido) . "','" . $Email . "','" . $MenorEdad . "','App','" . date("Y-m-d H:i:s") . "')";
                                        $dbo->query($inserta_invitado);
                                        $id_invitado = $dbo->lastID();
                                    else :

                                        if (!empty($Email)) {
                                            $CampoObservacion .= " , Email = '" . $Email . "'";
                                        }

                                        $actualiza_invitado = "Update Invitado set IDTipoDocumento = '" . $IDTipoDocumento . "', NumeroDocumento = '" . $NumeroDocumento . "', Nombre = '" . strtoupper($Nombre) . "', Apellido = '" . strtoupper($Apellido) . "', Email='" . $Email . "', UsuarioTrEd = 'App', FechaTrEd = '" . date("Y-m-d H:i:s") . "' " . $CampoObservacion . " Where IDInvitado = '" . $id_invitado . "'";
                                        $dbo->query($actualiza_invitado);
                                    endif;

                                    //Si es cabeza de familia guardo el id del Socio
                                    if ($CabezaInvitacion == "S") :
                                        $IDPadre = $id_invitado;
                                    endif;

                                    $sql_numero_invitacion = $dbo->query("Select * From SocioInvitadoEspecial Where IDInvitado = '" . $id_invitado . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "'");
                                    $numero_invitaciones = $dbo->rows($sql_numero_invitacion);
                                    //Consulto cuantas personas ha invitado el socio en el mes
                                    $sql_invitados_mes = $dbo->query("Select * From SocioInvitadoEspecial Where IDSocio = '" . $IDSocio . "' and YEAR(FechaInicio) = '" . $year_invitacion . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "'");
                                    $numero_invitados_mes = $dbo->rows($sql_invitados_mes);
                                    //Consulto cuantas personas ha invitado el socio en el dia
                                    $sql_invitados_dia = $dbo->query("Select * From SocioInvitadoEspecial Where IDSocio = '" . $IDSocio . "' and YEAR(FechaInicio) = '" . $year_invitacion . "' and MONTH(FechaInicio) = '" . $mes_invitacion . "' and DAY(FechaInicio) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                                    $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                                    $numero_invitados_mes_permitido = 500;
                                    $numero_mismo_invitado_mes = "300";
                                    $cumplimiento_obligatorio_limite = "S";

                                    // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
                                    //$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );

                                    if ((int) $numero_invitados_dia < (int) $numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite == "N") {
                                        if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite == "N") {
                                            if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes || $cumplimiento_obligatorio_limite == "N") {
                                                //Verifico que el invitado no este invitado mas de una vez el mismo dia

                                                $sql_invitacion_dia = $dbo->query("Select * From SocioInvitadoEspecial Where IDInvitado = '" . $id_invitado . "' and FechaInicio = '" . $FechaIngreso . "'");
                                                $numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
                                                if ((int) $numero_invitaciones_dia <= 1) {

                                                    //verifico si el vehiculo ya esta creado
                                                    if (!empty($Placa)) :
                                                        $id_vehiculo = $dbo->getFields("Vehiculo", "IDVehiculo", "Placa = '" . $Placa . "'");
                                                        //Si el vehiculo no existe en la tabla maestra lo creo
                                                        if (empty($id_vehiculo) || (int) $id_vehiculo == 0) :
                                                            $inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa)
                                                                                Values('" . $id_invitado . "','" . $Placa . "')";
                                                            $dbo->query($inserta_vehiculo);
                                                            $id_vehiculo = $dbo->lastID();
                                                        endif;
                                                    endif;

                                                    if (empty($id_invitado) || (int) $id_invitado == 0) :
                                                        //Inserto invitacion
                                                        $sql_inserta_inv = $dbo->query("INSERT INTO SocioInvitadoEspecial (IDClub, IDSocio, IDInvitado, IDPadre, IDVehiculo, CabezaInvitacion, TipoInvitacion, FechaInicio, FechaFin, Dias,UsuarioTrCr, FechaTrCr)
                                                                                                Values ('$IDClub','$IDSocio', '$id_invitado', '$IDPadre', '$id_vehiculo', '$CabezaInvitacion', '$TipoInvitado', '$FechaIngreso', '$FechaSalida', '$DiasCheckbox','WebService','" . date("Y-m-d H:i:s") . "')");
                                                        $id_invitacion_update = $dbo->lastID();

                                                        $id_invitado_inserta = $id_invitacion_update;
                                                        $nombre_enviar = $Nombre . " " . $Apellido;
                                                    else :

                                                        if (!empty($Email)) {
                                                            $CampoObservacion .= " , Email = '" . $Email . "'";
                                                        }

                                                        //Actualizo invitacion
                                                        $SQLActualizaInvi = "UPDATE SocioInvitadoEspecial SET IDClub = '$IDClub', IDSocio = '$IDSocio', IDInvitado = '$id_invitado', IDPadre = '$IDPadre', IDVehiculo = '$id_vehiculo', CabezaInvitacion = '$CabezaInvitacion', TipoInvitacion = '$TipoInvitado', FechaInicio = '$FechaIngreso', FechaFin = '$FechaSalida', Dias = '$DiasCheckbox',UsuarioTrEd = 'WebService', FechaTrEd = '" . date("Y-m-d H:i:s") . "' Where IDSocioInvitadoEspecial = '$IDInvitacion'";
                                                        $sql_actualiza_inv = $dbo->query($SQLActualizaInvi);
                                                        $id_invitado_inserta = $IDInvitacion;
                                                    endif;

                                                    //SIMUtil::notificar_nuevo_invitado( $IDClub, $IDSocio, $NumeroDocumento, $nombre_enviar , $FechaIngreso );

                                                    $datos_formulario = json_decode($ValoresFormulario, true);
                                                    if (count($datos_formulario) > 0) :
                                                        foreach ($datos_formulario as $detalle_datos) :
                                                            $sql_datos_form = $dbo->query("Insert Into SocioInvitadoEspecialOtrosDatos (IDSocioInvitadoEspecial, IDCampoFormularioInvitado, Valor) Values ('" . $id_invitado_inserta . "','" . $detalle_datos["IDCampoFormularioInvitado"] . "','" . $detalle_datos["Valor"] . "')");
                                                            $OtrosDatosFormulario .= " " . $detalle_datos["Valor"];
                                                        endforeach;
                                                    endif;

                                                    //Inserto el vehiculo de la invitacion
                                                    if (!empty($Placa)) :

                                                        $inserta_vehiculo_inv = "Insert Into VehiculoInvitacion (IDSocioInvitadoEspecial, IDVehiculo, Placa)
                                                                                    Values('" . $id_invitado_inserta . "','" . $id_vehiculo . "','" . $Placa . "')";
                                                        $dbo->query($inserta_vehiculo_inv);
                                                    endif;
                                                } else {
                                                    $array_errorres[] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";
                                                }
                                            } else {
                                                $array_errorres[] = "Lo sentimos supera el numero maximo de " . $numero_invitados_mes_permitido . " invitaciones por mes";
                                            }
                                        } else {
                                            $array_errorres[] = "Lo sentimos, esta persona ha sido invitadas mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
                                        }
                                    } else {
                                        $array_errorres[] = "Lo sentimos, supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia";
                                    }
                                endforeach;
                            endif;

                            if (count($array_errorres) > 0) :
                                $otros_mensajes = implode(",", $array_errorres);
                            endif;

                            $respuesta["message"] = "Invitado Actulizado " . $otros_mensajes;
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                        } else {
                            $respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } else {
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Invitado: Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        }

        return $respuesta;
    }

    public function get_mis_autorizaciones_invitados($IDClub, $IDSocio, $Tag, $FechaIngreso, $Tiempo = "")
    {
        $dbo = &SIMDB::get();

        $response = array();


        if (!empty($IDSocio)) {
            //Compartir Qr por whatsapp
            $config_club = $dbo->fetchAll("ConfiguracionClub ", " IDClub = '" . $IDClub . "' ", "array");
            $PermiteCompartirQrPorWhatsapp = $config_club["PermitePopupCompartir"];

            // Tag
            if (!empty($Tag)) :
                // Consulto lo invitados con este dato
                $sql_busca_invitado = "Select * From Invitado Where NumeroDocumento like '%" . $Tag . "%' or Nombre like '%" . $Tag . "%' or Apellido like '%" . $Tag . "%'  or Telefono like '%" . $Tag . "%' or Email like '%" . $Tag . "%' ";

                $result_busca_invitado = $dbo->query($sql_busca_invitado);
                while ($row_busca_invitado = $dbo->fetchArray($result_busca_invitado)) :
                    $array_condiciones_invitado[] = " IDInvitado  = '" . $row_busca_invitado["IDInvitado"] . "'";
                endwhile;
            endif;

            if (count($array_condiciones_invitado) > 0) :
                $condiciones_invitado = "and (" . implode(" or ", $array_condiciones_invitado) . ")";
            endif;

            if (!empty($FechaIngreso)) :
                $array_condiciones[] = " FechaInicio  = '" . $FechaIngreso . "'";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = "and " . implode(" and ", $array_condiciones);
            endif;

            if ($Tiempo == "Futuro" || $Tiempo == "") :
                $condicion_tiempo = " and FechaFin >= CURDATE() ";
            elseif ($Tiempo == "Pasado") :
                $condicion_tiempo = " and (FechaInicio <= CURDATE() or FechaInicio >= CURDATE() ) ";
            endif;

            //$sql = "SELECT * FROM SocioInvitadoEspecial WHERE IDClub = '" . $IDClub . "' and Mostrar <> 'N' and IDSocio = '" . $IDSocio . "' " . $condiciones . " " . " " . $condiciones_invitado . " " . $condicion_tiempo . "  ORDER BY IDSocioInvitadoEspecial,FechaInicio  Desc ";

            // Mostrar invitados por acción para Karibao
            $LimitarInvitadosPorAccion = $dbo->getFields('ConfiguracionClub', 'LimitarInvitadosPorAccion', "IDClub=$IDClub");
            if ($LimitarInvitadosPorAccion == 'S') {
                $sql_Accion = "SELECT Accion,AccionPadre FROM Socio WHERE IDSocio = $IDSocio";
                $q_Accion = $dbo->query($sql_Accion);
                $r_Accion  = $dbo->assoc($q_Accion);


                if (empty($r_Accion['AccionPadre'])) {
                    $condiciones .= " AND (AccionPadre = '" . $r_Accion['Accion'] . "' OR Accion = '" . $r_Accion['Accion'] . "')";
                } else if (!empty($r_Accion['AccionPadre'])) {
                    $condiciones .= " AND (Accion = '" . $r_Accion['AccionPadre'] . "' OR AccionPadre = '" . $r_Accion['AccionPadre'] . "')";
                } else {
                    $condiciones .= " AND IDSocio = '" . $IDSocio . "'";
                }


                $sql = "SELECT SocioInvitadoEspecial.* FROM SocioInvitadoEspecial,Socio WHERE SocioInvitadoEspecial.IDSocio=Socio.IDSocio and SocioInvitadoEspecial.IDClub = '" . $IDClub . "' and Mostrar <> 'N' " . $condiciones . " " . " " . $condiciones_invitado . " " . $condicion_tiempo . "  ORDER BY IDSocioInvitadoEspecial,FechaInicio  Desc ";
            } else {

                $sql = "SELECT * FROM SocioInvitadoEspecial WHERE IDClub = '" . $IDClub . "' and Mostrar <> 'N' and IDSocio = '" . $IDSocio . "' " . $condiciones . " " . " " . $condiciones_invitado . " " . $condicion_tiempo . "  ORDER BY IDSocioInvitadoEspecial,FechaInicio  Desc ";
            }


            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    if (!in_array($r["IDInvitado"], $array_socio_listado)) :

                        $elemento["IDClub"] = $IDClub;
                        $elemento["IDInvitacion"] = $r["IDSocioInvitadoEspecial"];
                        //Consulto datos invitado
                        $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $r["IDInvitado"] . "' ", "array");
                        $elemento["Nombre"] = $datos_invitado["Nombre"];
                        $elemento["Apellido"] = $datos_invitado["Apellido"];
                        $elemento["NumeroDocumento"] = "$datos_invitado[NumeroDocumento]";
                        $elemento["Email"] = $datos_invitado["Email"];
                        $elemento["TipoInvitado"] = $r["TipoInvitacion"];
                        $elemento["PermitePopupCompartir"] = $PermiteCompartirQrPorWhatsapp;

                        unset($response_tipodoc);
                        $response_tipodoc = array();
                        $tipodoc["IDTipoDocumento"] = (int) $datos_invitado["IDTipoDocumento"];
                        $tipodoc["Nombre"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                        array_push($response_tipodoc, $tipodoc);
                        $elemento["TipoDocumento"] = $response_tipodoc;
                        $elemento["FechaIngreso"] = $r["FechaInicio"];
                        $elemento["FechaSalida"] = $r["FechaFin"];
                        //Consulto datos vehiculo
                        $datos_vehiculo = $dbo->fetchAll("VehiculoInvitacion", " IDSocioInvitadoEspecial = '" . $r["IDSocioInvitadoEspecial"] . "' ", "array");


                        $elemento["Vehiculo"] = $datos_vehiculo["Placa"];
                        $elemento["CabezaInvitacion"] = $r["CabezaInvitacion"];
                        $elemento["DiasCheckbox"] = $r["Dias"];

                        //Reviso si ya se le dio entrada
                        $presalida = $dbo->getFields("Club", "Presalida", "IDClub = '" . $IDClub . "'");
                        if ($presalida == "S") {
                            $HabilitaPresalida = $dbo->getFields("LogAcceso", "Entrada", "IDClub = '" . $IDClub . "' and IDInvitacion = '" . $r["IDSocioInvitadoEspecial"] . "' and Fechaingreso >= '" . date("Y-m-d") . " 00:00:00' ");
                        }

                        if ($HabilitaPresalida != "S") {
                            $HabilitaPresalida = "N";
                        }

                        $elemento["HabilitaPresalida"] = $HabilitaPresalida;

                        //$array_socio_listado[] = $r[ "IDInvitado" ];

                        // Si tiene grupo familiar devuelvo el grupo y lo marco para no mostrarlo de nuevo
                        if ((int) $r["IDPadre"] > 0) :
                            $condicion_padre = " or IDPadre = '" . $r["IDPadre"] . "'";
                        endif;
                        /*
                        $response_invitado_familia = array();
                        $sql_grupo_familiar = "Select * From SocioInvitadoEspecial Where IDPadre = '".$r["IDInvitado"]."' or IDInvitado = '".$r["IDPadre"]."' " . $condicion_padre;
                        $result_grupo_familiar = $dbo->query($sql_grupo_familiar);
                        while($row_grupo_familiar = $dbo->fetchArray($result_grupo_familiar)):
                        if(!in_array($row_grupo_familiar["IDInvitado"],$array_socio_listado)):
                        $dato_invitado_asociado["IDClub"] = $IDClub;
                        $dato_invitado_asociado["IDInvitacion"] = $row_grupo_familiar["IDSocioInvitadoEspecial"];
                        //Consulto datos invitado
                        $datos_invitado = $dbo->fetchAll( "Invitado", " IDInvitado = '" . $row_grupo_familiar["IDInvitado"] . "' ", "array" );
                        $dato_invitado_asociado["Nombre"] = $datos_invitado["Nombre"];
                        $dato_invitado_asociado["Apellido"] = $datos_invitado["Apellido"];
                        $dato_invitado_asociado["NumeroDocumento"] = "$datos_invitado[NumeroDocumento]";
                        $dato_invitado_asociado["Email"] = $datos_invitado["Email"];
                        $dato_invitado_asociado["TipoInvitado"] = $row_grupo_familiar["TipoInvitacion"];

                        unset($response_tipodoc_asociado);
                        $response_tipodoc_asociado = array();
                        $tipodoc_asociado["IDTipoDocumento"] = (int)$datos_invitado["IDTipoDocumento"];
                        $tipodoc_asociado["Nombre"] = $dbo->getFields( "TipoDocumento" , "Nombre" , "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'" );
                        array_push($response_tipodoc_asociado, $tipodoc_asociado);
                        $dato_invitado_asociado["TipoDocumento"] = $response_tipodoc_asociado;

                        $dato_invitado_asociado["FechaIngreso"] = $row_grupo_familiar["FechaInicio"];
                        $dato_invitado_asociado["FechaSalida"] = $row_grupo_familiar["FechaFin"];
                        //Consulto datos vehiculo
                        $datos_vehiculo = $dbo->fetchAll( "VehiculoInvitacion", " IDSocioInvitadoEspecial = '" . $row_grupo_familiar["IDSocioInvitadoEspecial"] . "' ", "array" );
                        $dato_invitado_asociado["Vehiculo"] = $datos_vehiculo["Placa"];
                        $dato_invitado_asociado["CabezaInvitacion"] = $row_grupo_familiar["CabezaInvitacion"];
                        array_push($response_invitado_familia, $dato_invitado_asociado);
                        $array_socio_listado[]=$row_grupo_familiar["IDInvitado"];
                        endif;
                        endwhile;
                            */

                        if ($config_club["VerQrInvitados"] == "N") {
                            $Urlinvitacion = "";
                        } else {
                            $Urlinvitacion = URLROOT . "admin/lib/pdf_invitacion.php?club=$IDClub&seccion=socioinvitadoespecial&id=" . $r["IDSocioInvitadoEspecial"];
                        }

                        $elemento["GrupoFamiliar"] = $response_invitado_familia;
                        if ($PermiteCompartirQrPorWhatsapp == "S") {

                            $elemento["UrlInvitacion"] = SIMUtil::generar_qr($r["IDSocioInvitadoEspecial"], $datos_invitado["NumeroDocumento"], "MostrarSoloImagen");
                        } else {

                            $elemento["UrlInvitacion"] = $Urlinvitacion;
                        }

                        array_push($response, $elemento);
                    endif;
                } //end while
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "2." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function buscar_autorizacion_recogida($IDClub, $Documento)
    {

        $dbo = &SIMDB::get();

        //BUSQUEDA INVITADOS ACCESOS
        $qryString = str_replace(".", "", $Documento);
        $qryString = str_replace(",", "", $qryString);
        $qryString = str_replace("-", "", $qryString);

        if (ctype_digit($qryString)) {
            // si es solo numeros en un numero de documento
            $sql_invitacion = "Select SA.* From SocioAutorizacionSalida SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()";
            $modo_busqueda = "DOCUMENTO";
        } else {
            //seguramente es una placa
            //Consulto en invitaciones accesos
            $sql_invitacion = "Select SA.* From SocioAutorizacionSalida SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()";
            $modo_busqueda = "PLACA";
        }

        $result_invitacion = $dbo->query($sql_invitacion);
        $total_resultados = $dbo->rows($result_invitacion);

        if ($total_resultados > 0) :

            $datos_invitacion = $dbo->fetchArray($result_invitacion);
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");

            if (!empty($datos_invitado[FotoFile])) {
                $foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
            } else {
                $foto = URLROOT . "plataform/assets/images/sinfoto.png";
            }

            $datos_autorizado["IDSocioAutorizacionSalida"] = $datos_invitacion["IDSocioAutorizacionSalida"];
            $datos_autorizado["NombreAutorizado"] = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
            $datos_autorizado["TipoDocumentoAutorizado"] = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
            $datos_autorizado["DocumentoAutorizado"] = $datos_invitado["NumeroDocumento"];
            $datos_autorizado["FotoAutorizado"] = $foto;
            $datos_autorizado["FechaInicio"] = $datos_invitacion["FechaInicio"];
            $datos_autorizado["FechaFin"] = $datos_invitacion["FechaFin"];
            $datos_autorizado["AutorizadoPor"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $datos_socio["TipoSocio"] . ")");
            $datos_autorizado["TipoSocio"] = $datos_socio["TipoSocio"];
            $datos_autorizado["Observaciones"] = "";

            $array_doc_alumno = array();
            //Consulto los alumos que tiene autorizados para recogida
            $result_invitacion = $dbo->query($sql_invitacion);
            $contador_alumno = 0;
            while ($datos_autorizacion_estudiante = $dbo->fetchArray($result_invitacion)) :
                //valido que la cedula no tenga autorizaciones sobre el mismo alumno para no repetirlo
                if (!in_array($datos_autorizacion_estudiante["IDSocioSalida"], $array_doc_alumno)) :
                    //valido que el dia de hoy tenga permiso de recoger
                    $dia_hoy = date("w");
                    $array_dias_autorizados = explode("|", $datos_autorizacion_estudiante["Dias"]);
                    if (in_array($dia_hoy, $array_dias_autorizados)) :
                        $datos_alumno = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_autorizacion_estudiante["IDSocioSalida"] . "' ", "array");

                        if (!empty($datos_alumno[Foto])) {
                            $foto_alumno = SOCIO_ROOT . $datos_alumno["Foto"];
                        } else {
                            $foto_alumno = URLROOT . "plataform/assets/images/sinfoto.png";
                        }

                        $array_alumno_recogida[$contador_alumno]["IDSocioAutorizacionSalida"] = $datos_autorizacion_estudiante["IDSocioAutorizacionSalida"];
                        $array_alumno_recogida[$contador_alumno]["TipoInvitacion"] = "AutorizacionSalida";
                        $array_alumno_recogida[$contador_alumno]["NombreAlumno"] = $datos_alumno["Nombre"] . " " . $datos_alumno["Apellido"];
                        $array_alumno_recogida[$contador_alumno]["CursoAlumno"] = $datos_alumno["Curso"];
                        $array_alumno_recogida[$contador_alumno]["ObservacionesAlumno"] = "Solo los dias creados";
                        $array_alumno_recogida[$contador_alumno]["TipoDocumentoAlumno"] = "Codigo";
                        $array_alumno_recogida[$contador_alumno]["DocumentoAlumno"] = $datos_alumno["NumeroDocumento"];
                        $array_alumno_recogida[$contador_alumno]["FotoAlumno"] = $foto_alumno . " " . $datos_alumno[FotoFile];
                        $array_alumno_recogida[$contador_alumno]["FechaInicio"] = $datos_autorizacion_estudiante["FechaInicio"];
                        $array_alumno_recogida[$contador_alumno]["FechaFin"] = $datos_autorizacion_estudiante["FechaFin"];

                        //Consulto el historal de ingresos y salidas del alumno
                        $response_historial_acceso = array();
                        $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
                        $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
                        $sql_historial = $dbo->query("Select * From LogAcceso Where IDInvitacion = '" . $datos_autorizacion_estudiante["IDSocioAutorizacionSalida"] . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "'");
                        while ($row_historial = $dbo->fetchArray($sql_historial)) :
                            $dato_historial["Tipo"] = $row_historial["Tipo"];
                            $dato_historial["Salida"] = $row_historial["Salida"];
                            $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
                            $dato_historial["Entrada"] = $row_historial["Entrada"];
                            $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];
                            array_push($response_historial_acceso, $dato_historial);
                        endwhile;
                        $array_alumno_recogida[$contador_alumno]["Historial"] = $response_historial_acceso;

                    endif;
                endif;
                $array_doc_alumno[] = $datos_autorizacion_estudiante["IDSocioSalida"];
                $contador_alumno++;
            endwhile;

            $datos_autorizado["AlumnosAutorizados"] = $array_alumno_recogida;

        else :
            $datos_autorizado = null;
        endif;

        return $datos_autorizado;
    }

    public function get_presalida($IDClub, $IDSocio, $Documento)
    {
        $dbo = &SIMDB::get();
        if (!empty($Documento)) {
            $autorizacion_recogida = 0;
            $autorizacion_invitacion = 0;

            //BUSQUEDA INVITADOS ACCESOS
            $qryString = str_replace(".", "", $Documento);
            $qryString = str_replace(",", "", $qryString);
            $qryString = str_replace("-", "", $qryString);
            if (ctype_digit($qryString)) {
                // si es solo numeros en un numero de documento
                $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE, Invitado I Where SIE.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SIE.IDClub='" . $IDClub . "'";
                $modo_busqueda = "DOCUMENTO";
            } else {
                //seguramente es una placa
                //Consulto en invitaciones accesos
                $sql_invitacion = "Select SIE.* From SocioInvitadoEspecial SIE,Vehiculo V Where SIE.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SIE.IDClub='" . $IDClub . "'";
                $modo_busqueda = "PLACA";
            }

            $result_invitacion = $dbo->query($sql_invitacion);
            $total_resultados = $dbo->rows($result_invitacion);

            if ($total_resultados > 0) {
                $autorizacion_invitacion = 1;
            }

            $datos_invitacion = $dbo->fetchArray($result_invitacion);
            $datos_invitacion["TipoInvitacion"] = "InvitadoAcceso";
            $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitadoEspecial"];
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
            $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
            $tipo_socio = $datos_socio["TipoSocio"];
            $datos_socio["TipoSocio"] = "Invitado por";
            $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");

            if ($datos_invitacion["Ingreso"] == "N") {
                $accion_acceso = "ingreso";
                $label_accion_acceso = "Ingres&oacute;";
            } elseif ($datos_invitacion["Salida"] == "N") {
                $accion_acceso = "salio";
                $label_accion_acceso = "Sali&oacute;";
            }
            //Consulto grupo Familiar
            if ($datos_invitacion["CabezaInvitacion"] == "S") :
                $sql_grupo = "Select * From SocioInvitadoEspecial Where IDPadreInvitacion = '" . $datos_invitacion["IDSocioInvitadoEspecial"] . "'";
                $result_grupo = $dbo->query($sql_grupo);
            endif;
            //FIN BUSQUEDA INVITADOS ACCESOS

            //BUSQUEDA CONTRATISTA
            if ($total_resultados <= 0) :
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Invitado I Where SA.IDInvitado = I.IDInvitado  and I.NumeroDocumento = '" . (int) $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE()  and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa
                    //Consulto en invitaciones accesos
                    $sql_invitacion = "Select SA.* From SocioAutorizacion SA, Vehiculo V Where SA.IDVehiculo = V.IDVehiculo and V.Placa = '" . $qryString . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SA.IDClub='" . $IDClub . "'";
                    $modo_busqueda = "PLACA";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);
                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion["Ingreso"];
                $datos_invitacion["Salida"];
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioAutorizacion"];
                $datos_invitacion["TipoInvitacion"] = "Contratista";
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $dbo->fetchAll("Invitado", " IDInvitado = '" . $datos_invitacion["IDInvitado"] . "' ", "array");
                $datos_socio["TipoSocio"] = "Invitado por";
                $datos_invitacion["PersonaAutoriza"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . "(" . $tipo_socio . ")");

            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA INVITADOS GENERAL
            if ($total_resultados <= 0) :
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select SI.* From SocioInvitado SI Where SI.NumeroDocumento = '" . (int) $qryString . "' and FechaIngreso = '" . date("Y-m-d") . "' and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";

                    $result_invitacion = $dbo->query($sql_invitacion);
                    $total_resultados = $dbo->rows($result_invitacion);
                    $datos_invitacion = $dbo->fetchArray($result_invitacion);

                    if ($total_resultados > 0) {
                        $autorizacion_invitacion = 1;
                    }

                    $datos_invitacion["Ingreso"];
                    $datos_invitacion["Salida"];
                    $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocioInvitado"];
                    $datos_invitacion["TipoInvitacion"] = "Invitado";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                    $datos_invitado["Nombre"] = $datos_invitacion["Nombre"];
                }
            endif;
            //FIN BUSQUEDA CONTRATISTA

            //BUSQUEDA SOCIO o Empleado si esta como Socio
            if ($total_resultados <= 0) :
                if (ctype_digit($qryString)) {
                    // si es solo numeros en un numero de documento
                    $sql_invitacion = "Select * From Socio Where (NumeroDocumento = '" . (int) $qryString . "' or Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "') and IDClub = '" . $IDClub . "'";
                    $modo_busqueda = "DOCUMENTO";
                } else {
                    //seguramente es una placa    o una accion
                    //Consulto las placas de vehiculos de socios
                    $sql_invitacion = "Select * From Socio Where (Accion = '" . $qryString . "' or NumeroDerecho = '" . $qryString . "') and IDClub = '" . $IDClub . "'
											  UNION Select S.* From Socio S , Vehiculo V  Where S.IDSocio=V.IDSocio and  V.Placa = '" . $qryString . "' and IDClub = '" . $IDClub . "'
											  UNION Select S.* From Socio S , Predio P  Where S.IDSocio=P.IDSocio and  P.Predio = '" . $qryString . "' and IDClub = '" . $IDClub . "'  and AccionPadre = ''";
                }

                $result_invitacion = $dbo->query($sql_invitacion);
                $total_resultados = $dbo->rows($result_invitacion);

                if ($total_resultados > 0) {
                    $autorizacion_invitacion = 1;
                }

                $datos_invitacion = $dbo->fetchArray($result_invitacion);
                $datos_invitacion["IDInvitacion"] = $datos_invitacion["IDSocio"];
                $datos_invitacion["TipoInvitacion"] = "SocioClub";
                $datos_invitacion["PersonaAutoriza"] = "b";
                $datos_invitacion["FechaInicio"] = 'indefinida';
                $datos_invitacion["FechaFin"] = 'indedefinida';
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_invitacion["IDSocio"] . "' ", "array");
                $datos_invitado = $datos_socio;
                $modulo = "Socio";
                $id_registro = $datos_invitacion["IDSocio"];

            endif;
            //FIN BUSQUEDA SOCIO

            //Busco en autorizaciones de recogida d alumnos
            $array_autorizacion_recogida = SIMWebServiceAccesos::buscar_autorizacion_recogida($IDClub, $Documento);

            if ($total_resultados <= 0 && count($array_autorizacion_recogida) <= 0) {
                $respuesta["message"] = "No encontrado!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end if
            else {

                //Para fontanar en observaciones agrego el predio del socio
                if ($IDClub == 18 || $IDClub == 35) {
                    $Observaciones = "Se dirije a " . $datos_socio["Predio"];
                }

                $Observaciones .= " " . $datos_invitacion["ObservacionSocio"];
                if ($autorizacion_invitacion == 1) :
                    $datos_invitacion_individual = array();
                    $datos_invitacion_individual["IDInvitacion"] = $datos_invitacion["IDInvitacion"];
                    $datos_invitacion_individual["TipoInvitacion"] = $datos_invitacion["TipoInvitacion"];
                    $datos_invitacion_individual["FechaInicio"] = $datos_invitacion["FechaInicio"];
                    $datos_invitacion_individual["FechaFin"] = $datos_invitacion["FechaFin"];
                    $datos_invitacion_individual["Accion"] = $datos_socio["Accion"];
                    $datos_invitacion_individual["Socio"] = $datos_invitacion["PersonaAutoriza"];
                    $datos_invitacion_individual["TipoSocio"] = $datos_socio["TipoSocio"];
                    $datos_invitacion_individual["Observaciones"] = $Observaciones;
                    $datos_invitacion_individual["Ingreso"] = $datos_invitacion["Ingreso"];
                    $datos_invitacion_individual["FechaIngreso"] = $datos_invitacion["FechaInicio"];
                    $datos_invitacion_individual["Salida"] = $datos_invitacion["Salida"];
                    $datos_invitacion_individual["FechaSalida"] = $datos_invitacion["FechaFin"];

                    if (!empty($datos_invitado[FotoFile])) {
                        $foto = IMGINVITADO_ROOT . $datos_invitado["FotoFile"];
                    } else {
                        $foto = URLROOT . "plataform/assets/images/sinfoto.png";
                    }

                    $datos_invitacion_individual["Foto"] = $foto;
                    $datos_invitacion_individual["NombreInvitado"] = utf8_encode($datos_invitado["Nombre"] . " " . $datos_invitado["Apellido"]);
                    $tipodoc = $dbo->getFields("TipoDocumento", "Nombre", "IDTipoDocumento = '" . $datos_invitado["IDTipoDocumento"] . "'");
                    if (empty($tipodoc)) {
                        $TipoDocumento = "Doc";
                    } else {
                        $TipoDocumento = $tipodoc;
                    }

                    $datos_invitacion_individual["TipoDocumentoInvitado"] = $TipoDocumento;
                    $datos_invitacion_individual["NumeroDocumentoInvitado"] = $datos_invitado["NumeroDocumento"];

                    //Consulto el historial de ingresos y salidas del dia
                    $response_historial_acceso = array();
                    $fecha_hoy_desde = date("Y-m-d") . " 00:00:00";
                    $fecha_hoy_hasta = date("Y-m-d") . " 23:59:59";
                    $sql_historial = $dbo->query("Select * From LogAccesoDiario Where IDInvitacion = '" . $datos_invitacion["IDInvitacion"] . "' and FechaTrCr >= '" . $fecha_hoy_desde . "' and FechaTrCr <= '" . $fecha_hoy_hasta . "'");
                    while ($row_historial = $dbo->fetchArray($sql_historial)) :
                        $dato_historial["Tipo"] = $row_historial["Tipo"];
                        $dato_historial["Salida"] = $row_historial["Salida"];
                        $dato_historial["FechaSalida"] = $row_historial["FechaSalida"];
                        $dato_historial["Entrada"] = $row_historial["Entrada"];
                        $dato_historial["FechaIngreso"] = $row_historial["FechaIngreso"];
                        $UltimoMovimiento = $row_historial["Entrada"];
                        array_push($response_historial_acceso, $dato_historial);
                    endwhile;
                    $datos_invitacion_individual["Historial"] = $response_historial_acceso;
                else :
                    $datos_invitacion_individual = null;
                endif;

                $datos_invitacion_individual["Presalida"] = $datos_invitado["Presalida"];
                $datos_invitacion_individual["MostrarPresalida"] = ($UltimoMovimiento == "S" && $datos_invitado["Presalida"] != "S") ? "S" : "N";

                $response["Invitacion"] = $datos_invitacion_individual;

                $respuesta["message"] = "ok";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } else {
            $respuesta["message"] = "1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_eliminar_invitado_v1($IDClub, $IDSocio, $IDSocioInvitado)
    {
        $dbo = &SIMDB::get();
        $response = array();

        if (!empty($IDSocio) && !empty($IDSocioInvitado)) {
            $dato_invitado = $dbo->fetchAll("SocioInvitado", "IDSocioInvitado = $IDSocioInvitado", "array");
            $LogAcceso = $dbo->fetchAll("LogAcceso", "IDInvitacion={$IDSocioInvitado} AND FechaTrCr LIKE '" . date('Y-m-d') . "%' Order by IDLogAcceso Desc Limit 1", "array");
            if ($LogAcceso['Entrada'] != "S") :

                $sql = "Update SocioInvitado Set Mostrar = 'N' WHERE IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDSocioInvitado = '" . $IDSocioInvitado . "'";
                $qry = $dbo->query($sql);

                $respuesta["message"] = "Invitado eliminado correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :

                $respuesta["message"] = "El invitado no puede ser eliminado porque ya ingreso al club.";
                $respuesta["success"] = true;
                $respuesta["response"] = null;

            endif;
        } else {
            $respuesta["message"] = "I5." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function reglas_campestre_bosque($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario, $IDUsuario = "")
    {
        $dbo = SIMDB::get();

        $mes_invitacion = substr($FechaIngreso, 5, 2);
        $year_invitacion = substr($FechaIngreso, 0, 4);
        $dia_invitacion = substr($FechaIngreso, 8, 2);
        $hoy = date("Y-m-d");
        $Dia = date("w", strtotime($FechaIngreso));
        $validacion = 0;
        $Comentarios = "";

        // BUSCAMOS LA CATEGORIA DEL SOCIO
        $SQLDatosSocio = "SELECT TipoSocio, IDCategoria, AccionPadre FROM Socio WHERE IDSocio = $IDSocio";
        $QRYDatosSocio = $dbo->query($SQLDatosSocio);
        $DatosSocio = $dbo->fetchArray($QRYDatosSocio);
        // SABER LA CANTIDAD DEL GRUPO FAMILIAR
        $SQLGrupo = "SELECT COUNT(IDSocio) AS CantidadBeneficiario FROM `Socio` WHERE (AccionPadre = $DatosSocio[AccionPadre] OR Accion = $DatosSocio[AccionPadre]) AND IDClub = $IDClub";
        $QRYGrupo = $dbo->query($SQLGrupo);
        $DatosGrupo = $dbo->fetchArray($QRYGrupo);
        // Comentado por solucitud: solo los socios preferentes pueden invitar
        // if ($DatosSocio[TipoSocio] == "Asociado preferente" || $DatosSocio[TipoSocio] == "Adherente") :
        //     $CantidadInivitadoExSocioMes = 1;
        //     $CantidadInvitacionesValidas = 10 - $DatosGrupo[CantidadBeneficiario];
        // elseif ($DatosSocio[TipoSocio] == "Membresia" || $DatosSocio[TipoSocio] == "Usuario de Servicio") :
        //     $CantidadInivitadoExSocioMes = 1;
        //     $CantidadInvitacionesValidas = 8 - $DatosGrupo[CantidadBeneficiario];
        // endif;
        if ($DatosSocio['TipoSocio'] == "Asociados Preferentes") :
            $CantidadInivitadoExSocioMes = 1;
            $CantidadInvitacionesValidas = 10 - $DatosGrupo['CantidadBeneficiario'];
        endif;
        // var_dump($CantidadInivitadoExSocioMes);
        // var_dump($CantidadInvitacionesValidas);
        // die();
        // VALIDAMOS SI EL INVITADO ES UN EXASOCIADO
        $SQLExSocio = "SELECT IDSocio, IDEstadoSocio FROM Socio WHERE NumeroDocumento = $NumeroDocumento";
        $QRYExSocio = $dbo->query($SQLExSocio);
        $Datos = $dbo->fetchArray($QRYExSocio);

        if ($Datos['IDEstadoSocio'] == 2) :
            // BUSCAMOS NUMERO DE INIVTACIONES EN EL MES
            $sql_inv = "SELECT count(IDSocioInvitado) as TotalMesInvitado FROM SocioInvitado WHERE IDClub = '$IDClub' AND NumeroDocumento = '$NumeroDocumento' AND MONTH(FechaIngreso) = '$mes_invitacion' AND YEAR(FechaIngreso) = '$year_invitacion'  AND ((FechaIngreso > '$hoy'  AND Estado = 'P'))";
            $result_inv = $dbo->query($sql_inv);
            $row_inv_dia = $dbo->fetchArray($result_inv);
            if ($row_inv_dia["TotalMesInvitado"] >= $CantidadInivitadoExSocioMes) {

                $resultado = "Lo sentimos, el invitado es un exasociado y solo puede ser invitado una vez al mes y ya se cumplio";
                $validacion = 1;
            }
        endif;

        $ValoresFormulario = json_decode($ValoresFormulario);
        $Campo = $dbo->getFields("CampoFormularioInvitado", "EtiquetaCampo", "IDCampoFormularioInvitado = " . $ValoresFormulario[0]->IDCampoFormularioInvitado);
        if ($Campo == "Tipo Invitado" && $ValoresFormulario[0]->Valor == "Invitado por derecho") {


            // $sql_inv = "SELECT count(IDSocioInvitado) as TotalDia FROM SocioInvitado, WHERE IDSocio = '$IDSocio' and IDClub = '$IDClub' and FechaIngreso = '$FechaIngreso' ";
            $sql_inv = "SELECT count(IDSocioInvitado) as TotalDia FROM SocioInvitado si ,InvitadosOtrosDatos iod WHERE si.IDSocioInvitado=iod.IDInvitacion AND si.IDSocio = $IDSocio AND FechaIngreso = '{$FechaIngreso}' AND Valor = 'Invitado por derecho' AND Mostrar != 'N'";
            $result_inv = $dbo->query($sql_inv);
            $row_inv_dia = $dbo->fetchArray($result_inv);
            if ($row_inv_dia["TotalDia"] >= $CantidadInvitacionesValidas) {

                // $resultado = "Supera el maximo de cupos diarios entre su grupo familiar y sus invitados, a partir de este momento las invitaciones tendran un costo de $38.500.";
                $resultado = "Supera el maximo de cupos diarios entre su grupo familiar y sus invitados, a partir de este momento las invitaciones tendran un costo. Puede realizar el pago desde el módulo otros pagos o acercándose a la recepción principal, el costo depende de las tarifas vigentes.";
                $validacion = 2;
                $Comentarios = "Invitado con pago";
            }
        } else {

            // $resultado = "Esta invitación tiene un costo de $38.500, este pago lo puede realizar desde el módulo Otros pagos o acercándose a la recepción del club.";
            $resultado = "Esta invitación tiene un costo, este pago lo puede realizar desde el módulo Otros pagos o acercándose a la recepción del club. El costo depende de las tarifas vigentes.";
            $validacion = 2;
            $Comentarios = "Invitado con pago";
        }
        // Los siguientes tipos de socio no tienen limite pero deben pagar cada invitado
        if ($DatosSocio['TipoSocio'] == "Membresía" || $DatosSocio['TipoSocio'] == "Usuario de Servicios") :

            // $resultado = "Esta invitación tiene un costo de $38.500, este pago lo puede realizar desde el módulo Otros pagos o acercándose a la recepción del club.";
            $resultado = "Esta invitación tiene un costo, este pago lo puede realizar desde el módulo Otros pagos o acercándose a la recepción del club. El costo depende de las tarifas vigentes.";
            $validacion = 2;
            $Comentarios = "Invitado con pago";
        endif;
        $respuesta["message"] = $resultado;
        $respuesta["success"] = $validacion;
        $respuesta["response"] = $Comentarios;
        return $respuesta;
    }

    public function elimina_misautorizaciones_contratista_anterior($IDClub, $IDSocio, $IDAutorizacion)
    {

        $dbo = &SIMDB::get();

        $response = array();
        $Hoy = date("Y-m-d");

        if (!empty($IDSocio) && !empty($IDAutorizacion)) {
            $sql = "UPDATE SocioAutorizacion SET Mostrar = 'N', FechaFin = '$Hoy' WHERE IDClub = '$IDClub' AND IDSocio = '$IDSocio' AND IDSocioAutorizacion = '$IDAutorizacion'";
            $qry = $dbo->query($sql);
            $respuesta["message"] = "Invitado eliminado correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "I5." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    //NUEVAS

    public function get_invitado_general($IDClub, $NumeroDocumento = "", $Tag = "")
    {
        $dbo = &SIMDB::get();

        // Doc
        if (!empty($NumeroDocumento)) :
            $array_condiciones[] = " NumeroDocumento  = '" . $NumeroDocumento . "'";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (	Nombre  like '%" . $Tag . "%' or Apellido like '%" . $Tag . "%' or Email like '%" . $Tag . "%' or NumeroDocumento like '%" . $Tag . "%' )";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_noticia = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM Invitado WHERE 1 " . $condiciones_noticia . " ORDER BY Nombre ";

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $datos_invitado["IDInvitado"] = $r["IDInvitado"];
                $datos_invitado["IDTipoDocumento"] = $r["IDTipoDocumento"];
                $datos_invitado["NumeroDocumento"] = $r["NumeroDocumento"];
                $datos_invitado["Nombre"] = utf8_encode($r["Nombre"]);
                $datos_invitado["Apellido"] = utf8_encode($r["Apellido"]);
                $datos_invitado["Direccion"] = $r["Direccion"];
                $datos_invitado["Telefono"] = $r["Telefono"];
                $datos_invitado["Email"] = $r["Email"];
                $datos_invitado["FechaNacimiento"] = $r["FechaNacimiento"];

                array_push($response, $datos_invitado);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Porfavorutiliceelbuscador,noseencontraronregistros', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_invitado_dia_socio($IDClub, $NumeroDocumento = "", $Nombre = "", $Fecha, $IDSocio)
    {
        $dbo = &SIMDB::get();

        // Secciones Socio
        if (!empty($NumeroDocumento)) :
            $array_condiciones[] = " NumeroDocumento  = '" . $NumeroDocumento . "'";
        endif;

        // Seccion Especifica
        if (!empty($Nombre)) :
            $array_condiciones[] = " Nombre  = '%" . $Nombre . "%'";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
        endif;


        $response = array();
        $sql = "SELECT * FROM SocioInvitado WHERE IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and FechaIngreso = '" . $Fecha . "'" . $condiciones . " ORDER BY Nombre ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {




                $datos_invitado["IDClub"] = $r["IDClub"];
                $datos_invitado["IDSocio"] = $r["IDSocio"];
                $datos_invitado["NumeroDocumento"] = $r["NumeroDocumento"];
                $datos_invitado["Nombre"] = utf8_encode($r["Nombre"]);
                $datos_invitado["FechaIngreso"] = $r["FechaIngreso"];
                array_push($response, $datos_invitado);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Porfavorutiliceelbuscador,noseencontraronregistros', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_invitado($IDClub, $IDSocio, $NumeroDocumento, $FechaIngreso)
    {
        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio)) {
            //Compartir Qr por whatsapp
            $config_club = $dbo->fetchAll("ConfiguracionClub ", " IDClub = '" . $IDClub . "' ", "array");
            $PermiteCompartirQrPorWhatsapp = $config_club["PermitePopupCompartir"];
            // Numero Doc
            if (!empty($NumeroDocumento)) :
                $array_condiciones[] = " NumeroDocumento  like '%" . $NumeroDocumento . "%'";
            endif;

            if (!empty($FechaIngreso)) :
                $array_condiciones[] = " FechaIngreso  = '" . $FechaIngreso . "'";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = "and " . implode(" and ", $array_condiciones);
            endif;

            $sql = "SELECT * FROM SocioInvitado WHERE IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' " . $condiciones . " and Estado = 'P' and FechaIngreso >= CURDATE()  ORDER BY FechaIngreso Desc ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {

                    if ($IDClub == 239 and $r["TipoInvitacion"] == "Gastronomica") :
                        $tipo = "Gastronómica";
                    elseif ($IDClub == 239 and $r["TipoInvitacion"] == "Disfrute") :
                        $tipo = "Disfrute";
                    else :
                        $tipo = "";
                    endif;

                    $elemento["IDClub"] = $IDClub;
                    $elemento["IDInvitacion"] = $r["IDSocioInvitado"];
                    $elemento["Nombre"] = $r["Nombre"];
                    $elemento["NumeroDocumento"] = $r["NumeroDocumento"] . "\n" . $tipo;
                    $elemento["FechaIngreso"] = $r["FechaIngreso"];
                    $elemento["PermitePopupCompartir"] = $PermiteCompartirQrPorWhatsapp;

                    if ($config_club["VerQrInvitados"] == "N") {
                        $Urlinvitacion = "";
                    } else {
                        $Urlinvitacion = URLROOT . "admin/lib/pdf_invitacion.php?club=$IDClub&seccion=socioinvitado&id=" . $r["IDSocioInvitado"];
                    }

                    if ($PermiteCompartirQrPorWhatsapp == "S") {

                        $elemento["UrlInvitacion"] = SIMUtil::generar_qr($r["IDSocioInvitado"], $r["NumeroDocumento"], "MostrarSoloImagen");
                    } else {
                        $elemento["UrlInvitacion"] = $Urlinvitacion;
                    }
                    array_push($response, $elemento);
                    $r["TipoInvitacion"] = "";
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "2." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_mis_invitado($IDClub, $IDSocio, $NumeroDocumento)
    {
        $dbo = &SIMDB::get();

        $response = array();
        // Configuracion Club -- Mostrar invitados anteriores
        $VerInvitadosAnteriores = $dbo->getFields('ConfiguracionClub', 'VerInvitadosAnteriores', "IDClub=$IDClub");

        if ($VerInvitadosAnteriores == 'N') {
            return;
        }
        if (!empty($IDSocio)) {

            // Numero Doc
            if (!empty($NumeroDocumento)) :
                $array_condiciones[] = " NumeroDocumento  like '%" . $NumeroDocumento . "%'";
            endif;

            if (count($array_condiciones) > 0) :
                $condiciones = "and " . implode(" and ", $array_condiciones);
            endif;

            $sql = "SELECT * FROM SocioInvitado WHERE IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' " . $condiciones . " and Mostrar <> 'N' Group by IDSocio, NumeroDocumento ORDER BY FechaIngreso Desc";


            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    if ($IDClub == 239 and $r["TipoInvitacion"] == "Gastronomica") :
                        $tipo = "Gastronómica";
                    elseif ($IDClub == 239 and $r["TipoInvitacion"] == "Disfrute") :
                        $tipo = "Disfrute";
                    else :
                        $tipo = "";
                    endif;
                    $elemento["IDClub"] = $IDClub;
                    $elemento["IDSocioInvitado"] = $r["IDSocioInvitado"];
                    $elemento["Nombre"] = $r["Nombre"];
                    $elemento["NumeroDocumento"] = $r["NumeroDocumento"] . "\n" . $tipo;

                    array_push($response, $elemento);
                    $r["TipoInvitacion"] = "";
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "3." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_ingreso_salida_invitadov1($IDClub, $IDSocio, $IDSocioInvitado)
    {
        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio) && !empty($IDSocioInvitado)) {
            $sql = "Select * From LogAcceso Where IDInvitacion = '" . $IDSocioInvitado . "' and IDClub = '" . $IDClub . "' and Tipo = 'SocioInvitado' Order by IDLogAcceso Desc Limit 30";
            $qry = $dbo->query($sql);
            while ($r = $dbo->fetchArray($qry)) {
                $acceso["IDClub"] = $IDClub;
                $acceso["IDSocioInvitado"] = $r["IDInvitacion"];
                if ($r["Entrada"] == "S") :
                    $TipoMovimiento = "Entrada";
                    $FechaMovimiento = $r["FechaIngreso"];
                else :
                    $TipoMovimiento = "Salida";
                    $FechaMovimiento = $r["FechaSalida"];
                endif;
                $acceso["Movimiento"] = $TipoMovimiento;
                $acceso["FechaMovimiento"] = $FechaMovimiento;
                array_push($response, $acceso);
            } //ednw hile
            $respuesta["message"] = "Historial";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "I6." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_ingresosalida_autorizaciones_invitados($IDClub, $IDSocio, $IDInvitacion)
    {
        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio) && !empty($IDInvitacion)) {
            $sql = "Select * From LogAcceso Where IDInvitacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' and Tipo = 'InvitadoAcceso' Order by IDLogAcceso Desc Limit 30";
            $qry = $dbo->query($sql);
            while ($r = $dbo->fetchArray($qry)) {
                $acceso["IDClub"] = $IDClub;
                $acceso["IDSocioInvitado"] = $r["IDInvitacion"];
                if ($r["Entrada"] == "S") :
                    $TipoMovimiento = "Entrada";
                    $FechaMovimiento = $r["FechaIngreso"];
                else :
                    $TipoMovimiento = "Salida";
                    $FechaMovimiento = $r["FechaSalida"];
                endif;
                $acceso["Movimiento"] = $TipoMovimiento;
                $acceso["FechaMovimiento"] = $FechaMovimiento;
                array_push($response, $acceso);
            } //ednw hile
            $respuesta["message"] = "Historial";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "I6." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_ingresosalida_autorizaciones_contratista($IDClub, $IDSocio, $IDInvitacion)
    {
        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio) && !empty($IDInvitacion)) {
            $sql = "Select * From LogAcceso Where IDInvitacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "' and Tipo = 'Contratista' Order by IDLogAcceso Desc Limit 30";
            $qry = $dbo->query($sql);
            while ($r = $dbo->fetchArray($qry)) {
                $acceso["IDClub"] = $IDClub;
                $acceso["IDSocioInvitado"] = $r["IDInvitacion"];
                if ($r["Entrada"] == "S") :
                    $TipoMovimiento = "Entrada";
                    $FechaMovimiento = $r["FechaIngreso"];
                else :
                    $TipoMovimiento = "Salida";
                    $FechaMovimiento = $r["FechaSalida"];
                endif;
                $acceso["Movimiento"] = $TipoMovimiento;
                $acceso["FechaMovimiento"] = $FechaMovimiento;
                array_push($response, $acceso);
            } //ednw hile
            $respuesta["message"] = "Historial";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "I6." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function elimina_misautorizaciones_invitados_anterior($IDClub, $IDSocio, $IDInvitacion)
    {
        $dbo = &SIMDB::get();

        if ($IDClub == 206) {
            $TipoInvitacion = $dbo->getFields("SocioInvitadoEspecial", "TipoInvitacion", "IDClub = $IDClub and IDSocio = $IDSocio and IDSocioInvitadoEspecial = $IDInvitacion");

            if ($TipoInvitacion == "Invitado Permanente") {
                $respuesta["message"] = "I5." . SIMUtil::get_traduccion('', '', 'losentimosnosepuedeneliminarinvitadopermanentes', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
                return $respuesta;
            }
        }

        $response = array();

        if (!empty($IDSocio) && !empty($IDInvitacion)) {

            $sql = "Update SocioInvitadoEspecial Set Mostrar = 'N' WHERE IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDSocioInvitadoEspecial = '" . $IDInvitacion . "'";
            $qry = $dbo->query($sql);
            $respuesta["message"] = "Invitado eliminado correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "I5." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function verifica_bloqueo_invitado($NumeroDocumento, $IDClub = "")
    {
        $dbo = &SIMDB::get();
        $id_invitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub='" . $IDClub . "'");
        $sql_observacion_bloqueo = "Select * From ObservacionInvitado Where IDInvitado = '" . $id_invitado . "' and FechaInicioBloqueo <= CURDATE() AND FechaFinBloqueo >= CURDATE() Order by IDObservacionInvitado Desc";
        $result_observacion_bloqueo = $dbo->query($sql_observacion_bloqueo);
        $total_bloqueo = $dbo->rows($result_observacion_bloqueo);
        if ((int) $total_bloqueo > 0) {
            $bloqueo = "S";
        } else {
            $bloqueo = "N";
        }

        return $bloqueo;
    }

    public function set_invitadoV1($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso)
    {
        $dbo = &SIMDB::get();
        // Validación numero documento mayor a 0
        if (empty($NumeroDocumento) || $NumeroDocumento <= 0) {
            $respuesta["message"] = "El numero de documento del invitado no es valido";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        //Fin Validación numero documento mayor a 0

        if (!empty($NumeroDocumento) && !empty($Nombre) && !empty($FechaIngreso)) {

            $hoy = date("Y-m-d");
            // if (strtotime($FechaIngreso) >= strtotime($hoy) && strtotime($FechaSalida) >= strtotime($FechaIngreso)) {
            if (strtotime($FechaIngreso) >= strtotime($hoy)) {

                // Validación Datos invitados para Cantegril
                if ($IDClub == 185) {
                    if ($Nombre == '') {
                        $respuesta["message"] = "Lo sentimos, el Nombre del Invitado es obligatorio";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    if ($NumeroDocumento == '') {
                        $respuesta["message"] = "Lo sentimos, el Número de documento del Invitado es obligatorio";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } elseif (strlen($NumeroDocumento) < 7) {
                        $respuesta["message"] = "Lo sentimos, el Número de documento debe contener al menos 7 dígitos";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                    // if ($arr_DatosInvitado[0]->Email == '') {
                    //     $respuesta["message"] = "Lo sentimos, el Email del Invitado es obligatorio";
                    //     $respuesta["success"] = false;
                    //     $respuesta["response"] = null;
                    //     return $respuesta;
                    // } elseif (!filter_var($arr_DatosInvitado[0]->Email, FILTER_VALIDATE_EMAIL)) {
                    //     $respuesta["message"] = "Lo sentimos, el Email no cumple con el formato requerido: 'ejemplo@email.com'";
                    //     $respuesta["success"] = false;
                    //     $respuesta["response"] = null;
                    //     return $respuesta;
                    // }
                }
                //Fin Validación Datos invitados para Cantegril


                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    // Consulto las reglas que aplica al socio para invitaciones
                    $array_datos_regla = SIMUtil::consulta_regla_invitacion($IDSocio, $IDClub);

                    if ((int) $array_datos_regla["IDRegla"] > 0) {

                        //Consulto cuantas veces la persona ha sido invitada en el mes
                        $mes_invitacion = substr($FechaIngreso, 5, 2);
                        $year_invitacion = substr($FechaIngreso, 0, 4);
                        $dia_invitacion = substr($FechaIngreso, 8, 2);
                        $sql_numero_invitacion = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '" . $NumeroDocumento . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "' and Estado = 'I'");
                        $numero_invitaciones = $dbo->rows($sql_numero_invitacion);
                        //Consulto cuantas personas ha invitado el socio en el mes
                        $sql_invitados_mes = $dbo->query("Select * From SocioInvitado Where IDSocio = '" . $IDSocio . "' and YEAR(FechaIngreso) = '" . $year_invitacion . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "' and Estado = 'I'");
                        $numero_invitados_mes = $dbo->rows($sql_invitados_mes);
                        //Consulto cuantas personas ha invitado el socio en el dia
                        $sql_invitados_dia = $dbo->query("Select * From SocioInvitado Where IDSocio = '" . $IDSocio . "' and YEAR(FechaIngreso) = '" . $year_invitacion . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and DAY(FechaIngreso) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "' and Estado = 'I'");
                        $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                        //$numero_invitados_mes_permitido = $dbo->getFields( "Club" , "MaximoInvitadoSocio" , "IDClub = '".$IDClub."'" );
                        //$numero_mismo_invitado_mes = $dbo->getFields( "Club" , "MaximoRepeticionInvitado" , "IDClub = '".$IDClub."'" );

                        $numero_invitados_mes_permitido = $array_datos_regla["MaximoInvitadoSocio"];
                        $numero_mismo_invitado_mes = $array_datos_regla["MaximoRepeticionInvitado"];
                        $numero_invitados_dia_permitido = $array_datos_regla["MaximoInvitadoDia"];
                        $cumplimiento_obligatorio_limite = $array_datos_regla["CumplimientoInvitados"];

                        // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
                        //$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );

                        if ((int) $numero_invitados_dia < (int) $numero_invitados_dia_permitido || $cumplimiento_obligatorio_limite == "N") {

                            if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite == "N") {
                                if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes || $cumplimiento_obligatorio_limite == "N") {

                                    //Verifico que el invitado no este invitado mas de una vez el mismo dia
                                    $sql_invitacion_dia = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '" . $NumeroDocumento . "' and FechaIngreso = '" . $FechaIngreso . "'");
                                    $numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);
                                    // lo dejo en 0 para que pueda invitar mas de una vez la misma persona ya que un empleado puede entrara a varias predios en MY
                                    $numero_invitaciones_dia = 0;

                                    if ((int) $numero_invitaciones_dia <= 0) {
                                        $sql_seccion_not = $dbo->query("Insert Into SocioInvitado (IDClub, IDSocio, NumeroDocumento, Nombre, TipoInvitacion, FechaIngreso, UsuarioTrCr, FechaTrCr) Values ('" . $IDClub . "','" . $IDSocio . "', '" . $NumeroDocumento . "', '" . $Nombre . "', '" . $Tipo_Invitacion . "', '" . $FechaIngreso . "', 'WebService',NOW())");
                                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;
                                    } else {
                                        $respuesta["message"] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                    }
                                } else {
                                    $respuesta["message"] = "Lo sentimos supera el numero maximo de " . $numero_invitados_mes_permitido . " invitaciones por mes";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                }
                            } else {
                                $respuesta["message"] = "Lo sentimos, esta persona ha sido invitadas mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            }
                        } else {
                            $respuesta["message"] = "Lo sentimos, supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } else {
                        $respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "15." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_invitado_update($IDClub, $IDSocio, $IDInvitado, $NumeroDocumento, $Nombre, $FechaIngreso)
    {
        $dbo = &SIMDB::get();

        require_once LINDIR . "SIMWebServiceCountryBarranquilla.inc.php";
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

        if ($datos_socio["IDEstadoSocio"] != "1") {
            $respuesta["message"] = "Lo sentimos, tiene un estado diferente a activo, no puede crear invitaciones";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        // Validación numero documento mayor a 0
        if (empty($NumeroDocumento) || $NumeroDocumento <= 0) {
            $respuesta["message"] = "El numero de documento del invitado no es valido";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        //Fin Validación numero documento mayor a 0

        /*
        if( ($IDClub==72)  && ( date('w', strtotime($FechaIngreso))==0 || date('w', strtotime($FechaIngreso))==6 || date('w', strtotime($FechaIngreso))==1  )  ){
        $respuesta[ "message" ] = "Lo sentimos, no es posible invitar los fines de semana";
        $respuesta[ "success" ] = false;
        $respuesta[ "response" ] = NULL;
        return $respuesta;
        }
            */

        if (($IDClub == 44) && (date('w', strtotime($FechaIngreso)) == 0 || date('w', strtotime($FechaIngreso)) == 6 || date('w', strtotime($FechaIngreso)) == 100)) {
            $respuesta["message"] = "Lo sentimos, no es posible invitar los fines de semana";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        if (!empty($NumeroDocumento) && !empty($Nombre) && !empty($FechaIngreso)) {

            $NumeroDocumento = str_replace(".", "", $NumeroDocumento);
            $NumeroDocumento = trim(str_replace(",", "", $NumeroDocumento));

            //Especial bquilla se valida por cedula primero
            if ($IDClub == 110) {

                if ($datos_socio["TipoSocio"] != "PRINCIPAL" && $datos_socio["TipoSocio"] != "CONYUGUE") {
                    $respuesta["message"] = "Lo sentimos solo los socios principales pueden realizar invitaciones";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $resp_valida = SIMWebServiceCountryBarranquilla::valida_quilla($NumeroDocumento, $ValoresFormulario, $IDSocio, $Nombre);
                if (!$resp_valida["success"]) {
                    $respuesta["message"] = $resp_valida["message"];
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    //return $respuesta;
                }
            }
            //Especial bquilla se valida por cedula primero

            $hoy = date("Y-m-d");

            if (strtotime($FechaIngreso) >= strtotime($hoy)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    // CAMBIO Sep 12 2016: Se consulta las invitaciones que puede hacer el socio Titular
                    // Consulto las invitaciones que puede hacer el socio
                    $numero_invitados_dia_permitido_socio = $dbo->getFields("Socio", "NumeroInvitados", "IDSocio = '" . $IDSocio . "'");
                    //$datos_socio = $dbo->fetchAll( "Socio", " IDSocio = '" . $IDSocio . "' ", "array" );

                    if (!empty($datos_socio["AccionPadre"])) : // es un beneficiario
                        $id_socio_titular = $dbo->getFields("Socio", "IDSocio", "Accion = '" . $datos_socio["AccionPadre"] . "'");
                        $numero_invitados_dia_permitido = $dbo->getFields("Socio", "NumeroInvitados", "IDSocio = '" . $id_socio . "'");

                        if (empty($numero_invitados_dia_permitido) || $numero_invitados_dia_permitido == 0) {
                            $numero_invitados_dia_permitido = $numero_invitados_dia_permitido_socio;
                        }

                        //Consulto los id socio de mi vinculo
                        $sql_socio_vinculo = "Select IDSocio From Socio Where AccionPadre = '" . $datos_socio["AccionPadre"] . "' or Accion = '" . $datos_socio["AccionPadre"] . "' and IDClub = '" . $IDClub . "'";
                        $qry_socio_vinculo = $dbo->query($sql_socio_vinculo);
                        while ($row_socio_vinculo = $dbo->fetchArray($qry_socio_vinculo)) :
                            $array_socio_vinculo[] = $row_socio_vinculo["IDSocio"];
                        endwhile;
                        if (count($array_socio_vinculo) > 0) :
                            $id_otro_socio = implode(",", $array_socio_vinculo);
                            $condicion_otro_socio = " or IDSocio in (" . $id_otro_socio . ")";
                        endif;
                    else :
                        $id_socio_titular = $IDSocio;
                        $numero_invitados_dia_permitido = $dbo->getFields("Socio", "NumeroInvitados", "IDSocio = '" . $IDSocio . "'");
                        //Consulto los id socio de mi vinculo
                        $sql_socio_vinculo = "Select IDSocio From Socio Where AccionPadre = '" . $datos_socio["Accion"] . "' and IDClub = '" . $IDClub . "'";
                        $qry_socio_vinculo = $dbo->query($sql_socio_vinculo);
                        while ($row_socio_vinculo = $dbo->fetchArray($qry_socio_vinculo)) :
                            $array_socio_vinculo[] = $row_socio_vinculo["IDSocio"];
                        endwhile;
                        if (count($array_socio_vinculo) > 0) :
                            $id_otro_socio = implode(",", $array_socio_vinculo);
                            $condicion_otro_socio = " or IDSocio in (" . $id_otro_socio . ")";
                        endif;
                    endif;

                    // Consulto si el dia de la reserva esta asignado como fecha especial para no tomar en cuenta invitaciones
                    $id_fecha_Especial = $dbo->getFields("FechaEspecialInvitado", "IDFechaEspecialInvitado", "Fecha = '" . $FechaIngreso . "' and IDClub = '" . $IDClub . "'");
                    if (!empty($id_fecha_Especial)) :
                        // Dejo los parametros ilimitados
                        $numero_invitados_dia_permitido = 100;
                        $numero_invitados_dia_permitido_socio = 100;
                    endif;

                    //Consulto cuantas veces la persona ha sido invitada en el mes
                    $mes_invitacion = substr($FechaIngreso, 5, 2);
                    $year_invitacion = substr($FechaIngreso, 0, 4);
                    $dia_invitacion = substr($FechaIngreso, 8, 2);

                    if ($IDClub == '44') {
                        switch (date('w', $fechats)) {
                            case 0:
                                $dia_txt = "Domingo";
                                break;
                            case 1:
                                $dia_txt = "Lunes";
                                break;
                            case 2:
                                $dia_txt = "Martes";
                                break;
                            case 3:
                                $dia_txt = "Miercoles";
                                break;
                            case 4:
                                $dia_txt = "Jueves";
                                break;
                            case 5:
                                $dia_txt = "Viernes";
                                break;
                            case 6:
                                $dia_txt = "Sabado";
                                break;
                        }
                    }

                    //Consulto cuantos invitadoa ha hecho para el dia
                    $sql_invitados_dia_soc = $dbo->query("Select * From SocioInvitado Where (IDSocio = '" . $IDSocio . "') and YEAR(FechaIngreso) = '" . $year_invitacion . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and DAY(FechaIngreso) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                    $numero_invitaciones_soc = $dbo->rows($sql_invitados_dia_soc);

                    if ($dia_txt == 'Viernes' || $dia_txt == 'Sabado') {
                        if (($numero_invitaciones_soc) >= 3) {
                            $respuesta["message"] = "Lo sentimos, supera el numero maximo de invitaciones para el dia " . $dia_txt;
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }


                    if (($numero_invitaciones_soc) >= $numero_invitados_dia_permitido_socio) :
                        $respuesta["message"] = "Lo sentimos, supera el numero maximo de " . $numero_invitados_dia_permitido . " invitaciones por dia permitido";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    endif;

                    if ((int) $numero_invitados_dia_permitido > 0 && $numero_invitados_dia_permitido_socio > 0) {

                        $sql_numero_invitacion = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '" . $NumeroDocumento . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "' and Estado = 'I'");
                        $numero_invitaciones = $dbo->rows($sql_numero_invitacion);

                        //Consulto cuantas personas ha invitado el socio en el mes
                        $sql_invitados_mes = $dbo->query("Select * From SocioInvitado Where IDSocio = '" . $IDSocio . "' and YEAR(FechaIngreso) = '" . $year_invitacion . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and IDClub = '" . $IDClub . "' and Estado = 'I'");
                        $numero_invitados_mes = $dbo->rows($sql_invitados_mes);

                        //Cambio Sep 12: Se suma el total de invitados por accion padre
                        //Consulto cuantas personas ha invitado el socio en el dia
                        //$sql_invitados_dia = $dbo->query("Select * From SocioInvitado Where IDSocio = '".$IDSocio."' and YEAR(FechaIngreso) = '".$year_invitacion."' and MONTH(FechaIngreso) = '".$mes_invitacion."' and DAY(FechaIngreso) = '".$dia_invitacion."' and IDClub = '".$IDClub."' and Estado = 'I'");
                        //$numero_invitados_dia = $dbo->rows($sql_invitados_dia);
                        $sql_invitados_dia = $dbo->query("Select * From SocioInvitado Where (IDSocio = '" . $IDSocio . "' " . $condicion_otro_socio . ") and YEAR(FechaIngreso) = '" . $year_invitacion . "' and MONTH(FechaIngreso) = '" . $mes_invitacion . "' and DAY(FechaIngreso) = '" . $dia_invitacion . "' and IDClub = '" . $IDClub . "'");
                        $numero_invitados_dia = $dbo->rows($sql_invitados_dia);

                        $numero_invitados_dia_permitido_accion = 200;
                        $numero_invitados_mes_permitido = 5000;
                        $numero_mismo_invitado_mes = "3000";
                        $cumplimiento_obligatorio_limite = "S";

                        // Consulto si en la configuracion del club se marco como obligatorio el limite de invitados si es no permito agregar el invitado
                        //$cumplimiento_obligatorio_limite = $dbo->getFields( "Club" , "CumplimientoInvitados" , "IDClub = '".$IDClub."'" );

                        if ((int) $numero_invitados_dia < (int) $numero_invitados_dia_permitido_accion || $cumplimiento_obligatorio_limite == "N") {

                            if ((int) $numero_invitaciones < (int) $numero_mismo_invitado_mes || $cumplimiento_obligatorio_limite == "N") {
                                if ((int) $numero_invitados_mes_permitido > (int) $numero_invitados_mes || $cumplimiento_obligatorio_limite == "N") {

                                    //Verifico que el invitado no este invitado mas de una vez el mismo dia
                                    $sql_invitacion_dia = $dbo->query("Select * From SocioInvitado Where NumeroDocumento = '" . $NumeroDocumento . "' and FechaIngreso = '" . $FechaIngreso . "'");
                                    $numero_invitaciones_dia = $dbo->rows($sql_invitacion_dia);

                                    if ((int) $numero_invitaciones_dia <= 0) {
                                        $Tipo_Invitacion = $dbo->getFields("SocioInvitado", "TipoInvitacion", "IDSocioInvitado=$IDInvitado");
                                        $sql_SocioInitado = $dbo->query("Insert Into SocioInvitado (IDClub, IDSocio, NumeroDocumento, Nombre,TipoInvitacion, FechaIngreso, UsuarioTrCr, FechaTrCr) Values ('" . $IDClub . "','" . $IDSocio . "', '" . $NumeroDocumento . "', '" . $Nombre . "', '" . $Tipo_Invitacion . "', '" . $FechaIngreso . "', 'WebService',NOW())");
                                        $id_nueva_inv = $dbo->lastID();
                                        // Copiamos respuestas a preguntas dinamicas
                                        // if ($sql_SocioInitado) {
                                        //     $arr_InvitadosOtrosDatos = $dbo->fetchAll("InvitadosOtrosDatos", "IDInvitacion=$IDInvitacion", "array");
                                        //     foreach ($arr_InvitadosOtrosDatos as $id => $Valores) {
                                        //         $insert_InvitadosOtrosDatos = $dbo->query("insert into InvitadosOtrosDatos (IDInvitacion,IDCampoFormularioInvitado,Valor) values ($id_nueva_inv,{$Valores['IDCampoFormularioInvitado']},'{$Valores['Valor']}')");
                                        //     }
                                        // }
                                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                                        $respuesta["success"] = true;
                                        $respuesta["response"] = null;

                                        //envio notificacion
                                        SIMUtil::notificar_nuevo_invitado($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso);
                                    } else {
                                        $respuesta["message"] = "Lo sentimos esta persona ya tiene una invitacion para el dia seleccionado";
                                        $respuesta["success"] = false;
                                        $respuesta["response"] = null;
                                    }
                                } else {
                                    $respuesta["message"] = "Lo sentimos supera el numero maximo de " . $numero_invitados_mes_permitido . " invitaciones por mes";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                }
                            } else {
                                $respuesta["message"] = "Lo sentimos, esta persona ha sido invitadas mas de " . $numero_mismo_invitado_mes . " veces en este mes.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            }
                        } else {
                            $respuesta["message"] = "Lo sentimos, supera el numero maximo de: " . $numero_invitados_dia_permitido . " invitaciones por dia.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } else {
                        $respuesta["message"] = "Lo sentimos, no tiene permisos suficientes para realizar invitaciones";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Lo sentimos, no se permite fechas antiguas";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "14." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_contratista_update($IDClub, $IDSocio, $TipoAutorizacion, $FechaIngreso, $FechaSalida, $TipoDocumento, $NumeroDocumento, $Nombre, $Apellido, $Email, $Placa)
    {
        $dbo = &SIMDB::get();

        if (!empty($NumeroDocumento) && !empty($Nombre) && !empty($Apellido)) {

            $NumeroDocumento = str_replace(".", "", $NumeroDocumento);
            $NumeroDocumento = trim(str_replace(",", "", $NumeroDocumento));

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                $id_invitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "'");
                if (!empty($id_invitado)) {

                    if (!empty($Email)) {
                        $CampoObservacion .= " , Email = '" . $Email . "'";
                    }

                    $sql_invitado_update = $dbo->query("Update Invitado
                                                                    Set IDTipoDocumento = '" . $TipoDocumento . "', NumeroDocumento = '" . $NumeroDocumento . "', Nombre = '" . strtoupper($Nombre) . "',
                                                                    Apellido = '" . strtoupper($Apellido) . "', Email = '" . $Email . "' " . $CampoObservacion . "
                                                                    Where IDInvitado = '" . $id_invitado . "'");
                    //verifico si el vehiculo ya esta creado
                    if (!empty($Placa)) :
                        $id_vehiculo = $dbo->getFields("Vehiculo", "IDVehiculo", "Placa = '" . $Placa . "'");
                        //Si el vehiculo no existe en la tabla maestra lo creo
                        if (empty($id_vehiculo) || (int) $id_vehiculo == 0) :
                            $inserta_vehiculo = "Insert Into Vehiculo (IDInvitado, Placa)
                                                                                        Values('" . $id_invitado . "','" . $Placa . "')";
                            $dbo->query($inserta_vehiculo);
                            $id_vehiculo = $dbo->lastID();
                        endif;
                    endif;

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "El contratista no existe";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "16." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function cancela_invitacion($IDClub, $IDSocio, $IDInvitacion)
    {
        $dbo = &SIMDB::get();
        require_once LIBDIR . "SIMWebServiceCountryBarranquilla.inc.php";

        if (!empty($IDSocio) && !empty($IDInvitacion)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                $datos_tipo_reserva = $dbo->fetchAll("SocioInvitado", " IDSocioInvitado = '" . $IDInvitacion . "' ", "array");
                $LogAcceso = $dbo->fetchAll("LogAcceso", "IDInvitacion={$IDInvitacion} AND FechaTrCr LIKE '" . date('Y-m-d') . "%' Order by IDLogAcceso Desc Limit 1", "array");



                //Especial b/quilla
                if ($IDClub == 110) {
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                    $resp_b = SIMWebServiceCountryBarranquilla::cancela_invitacion_bquilla($IDClub, $datos_socio["IdentificadorExterno"], $datos_tipo_reserva["NumeroDocumento"]);
                    if (!$resp_b["success"]) {
                        $respuesta["message"] = $resp_b["message"];
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        //return $respuesta;
                    }
                }
                //fin Especial b/quilla

                if (!empty($datos_tipo_reserva["IDSocioInvitado"])) {
                    // if ($datos_tipo_reserva["Estado"] != "I") {
                    if ($LogAcceso['Entrada'] != "S") {
                        $sql_cancela_invitacion = $dbo->query("delete  From SocioInvitado Where IDSocioInvitado = '" . $IDInvitacion . "' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'  Limit 1");

                        // Actualizar inviados en arboretto
                        // Envio invitacion a Arboretto
                        if ($IDClub == 223) {
                            // if ($IDClub == 8) {
                            include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                            $datos_invitado = $dbo->fetchAll("Invitado", "IDInvitado={$datos_tipo_reserva['IDInvitado']}", "array");

                            if (!empty($datos_invitado['NumeroCarnet'])) {
                                $appointRecordId = $datos_invitado['NumeroCarnet'];
                                // Enviar datos a Arboretto
                                $JSon = '{
                                "appointRecordId": "' . $appointRecordId . '"
                            }';
                                $respuesta_arboretto = SIMWebServiceArboretto::salida_visitante($IDClub, $row_socio["IDSocio"], "", $JSon);
                            }
                        }

                        // Fin Actualizar inviados en arboretto


                        $respuesta["message"] = "invitacion cancelada";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } else {
                        $respuesta["message"] = "Lo sentimos, no se puede cancelar la invitacion, el invitado ya ingreso";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Lo sentimos, la invitacion no existe";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "17." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function cancela_autorizacion_invitado($IDClub, $IDSocio, $IDInvitacion)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocio) && !empty($IDInvitacion)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                if ($IDClub == 206) {
                    $TipoInvitacion = $dbo->getFields("SocioInvitadoEspecial", "TipoInvitacion", "IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' and IDSocioInvitadoEspecial = '" . $IDInvitacion . "'");

                    if ($TipoInvitacion == "Invitado Permanente") {
                        $respuesta["message"] = "I5." . SIMUtil::get_traduccion('', '', 'losentimosnosepuedeneliminarinvitadopermanentes', LANG);
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                $datos_tipo_reserva = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array");
                $LogAcceso = $dbo->fetchAll("LogAcceso", "IDInvitacion={$IDInvitacion} AND FechaTrCr LIKE '" . date('Y-m-d') . "%' Order by IDLogAcceso Desc Limit 1", "array");



                if (!empty($datos_tipo_reserva["IDSocioInvitadoEspecial"])) {
                    // if ($datos_tipo_reserva["Ingreso"] != "S") {
                    if ($LogAcceso['Entrada'] != "S") {
                        //$mensajecancelacion= "delete From SocioInvitadoEspecial Where IDSocioInvitadoEspecial = '".$IDInvitacion."' and IDSocio = '".$IDSocio."' and IDClub = '".$IDClub."'  Limit 1";
                        $sql_cancela_invitacion = $dbo->query("delete From SocioInvitadoEspecial Where IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'  Limit 1");
                        // Si es cabeza de grupo borro las invitaciones asociadas al cabeza de grupo
                        if ($datos_tipo_reserva["CabezaInvitacion"] == "S") :
                        //$sql_cancela_invitacion_hijos = $dbo->query("delete From SocioInvitadoEspecial Where IDPadre = '".$datos_tipo_reserva["IDInvitado"]."' and IDSocio = '".$IDSocio."' and IDClub = '".$IDClub."' and FechaInicio = '".$datos_tipo_reserva["FechaInicio"]."' and FechaFin = '".$datos_tipo_reserva["FechaFin"]."'");
                        endif;

                        $respuesta["message"] = "invitacion cancelada.";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } else {
                        $respuesta["message"] = "Lo sentimos, no se puede cancelar la invitacion, el invitado ya ingreso";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Lo sentimos, la invitacion no existe";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "17." .  SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_presalida($IDClub, $IDSocio, $IDInvitacion)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocio) && !empty($IDInvitacion)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                $datos_tipo_reserva = $dbo->fetchAll("SocioInvitadoEspecial", " IDSocioInvitadoEspecial = '" . $IDInvitacion . "' ", "array");
                if (!empty($datos_tipo_reserva["IDSocioInvitadoEspecial"])) {

                    $sql_presalida = $dbo->query("Update  SocioInvitadoEspecial Set IDSocioPresalida = '" . $IDSocio . "', Presalida = 'S', FehaPresalida = NOW() Where IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'  Limit 1");
                    $respuesta["message"] = "Presalida realizada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = "";
                } else {
                    $respuesta["message"] = "Lo sentimos, la invitacion no existe";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "17." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_presalida_contratista($IDClub, $IDSocio, $IDInvitacion)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocio) && !empty($IDInvitacion)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                $datos_tipo_reserva = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $IDInvitacion . "' ", "array");
                if (!empty($datos_tipo_reserva["IDSocioAutorizacion"])) {

                    $sql_presalida = $dbo->query("Update  SocioAutorizacion Set IDSocioPresalida = '" . $IDSocio . "', Presalida = 'S', FehaPresalida = NOW() Where IDSocioAutorizacion = '" . $IDInvitacion . "' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'  Limit 1");
                    $respuesta["message"] = "Presalida realizada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = "";
                } else {
                    $respuesta["message"] = "Lo sentimos, la invitacion no existe";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "17." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function cancela_autorizacion_contratista($IDClub, $IDSocio, $IDAutorizacion)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocio) && !empty($IDAutorizacion)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_socio)) {

                $datos_tipo_reserva = $dbo->fetchAll("SocioAutorizacion", " IDSocioAutorizacion = '" . $IDAutorizacion . "' ", "array");
                $LogAcceso = $dbo->fetchAll("LogAcceso", "IDInvitacion={$IDAutorizacion} AND FechaTrCr LIKE '" . date('Y-m-d') . "%' Order by IDLogAcceso Desc Limit 1", "array");


                if (!empty($datos_tipo_reserva["IDSocioAutorizacion"])) {
                    // if ($datos_tipo_reserva["Ingreso"] != "S") {
                    if ($LogAcceso['Entrada'] != "S") {
                        $sql_cancela_invitacion = $dbo->query("delete  From SocioAutorizacion Where IDSocioAutorizacion = '" . $IDAutorizacion . "' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'  Limit 1");
                        $respuesta["message"] = "autorizacion cancelada";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } else {
                        $respuesta["message"] = "Lo sentimos, no se puede cancelar la autorizacion, el invitado ya ingreso";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "Lo sentimos, la autorizacion no existe";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "17." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function valida_regla_invitacion_acceso($IDClub, $IDSocio, $IDInvitado, $FechaIngreso)
    {
        $dbo = &SIMDB::get();
        $mes_invitacion = substr($FechaIngreso, 5, 2);
        $year_invitacion = substr($FechaIngreso, 0, 4);
        $dia_invitacion = substr($FechaIngreso, 8, 2);
        $resultado = SIMUtil::get_traduccion('', '', 'ok', LANG);
        $datos_socio = $dbo->fetchAll("Socio", " IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' ", "array");
        $validacion = true;

        $datos_regla = SIMUtil::consulta_regla_invitacion($IDSocio, $IDClub);

        if ($datos_regla["IDRegla"] > 0) {

            if ($datos_regla["MaximoInvitadoDia"] >= 0) {
                //Consulto que el socio no supere el maximo de invitaciones que tiene en el dia
                $sql_inv = "SELECT count(IDSocioInvitadoEspecial) as TotalDia FROM SocioInvitadoEspecial WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' and FechaInicio = '" . $FechaIngreso . "'";
                $result_inv = $dbo->query($sql_inv);
                $row_inv_dia = $dbo->fetchArray($result_inv);
                if ($row_inv_dia["TotalDia"] > $datos_regla["MaximoInvitadoDia"]) {
                    $resultado = "Lo sentimos supera el maximo de " . $datos_regla["MaximoInvitadoDia"] . " invitaciones permitidas por dia";
                    $validacion = false;
                }
            }
            if ($datos_regla["MaximoRepeticionInvitado"] >= 0) {
                //Consulto cuantas veces el invitado ha sido invitado por el socio en el mes

                $porsocio = $datos_regla['MaximoRepeticionInvitadoSocio'];

                if ($porsocio == 1)
                    $condicionPorSocio = " AND IDSocio = $IDSocio";

                //Consulto cuantas veces el invitado ha sido invitado por el socio en el mes
                $sql_inv = "SELECT count(IDSocioInvitadoEspecial) as TotalMesInvitado FROM SocioInvitadoEspecial WHERE IDClub = '" . $IDClub . "' and YEAR(FechaInicio) = '" . date('Y') . "' and  MONTH(FechaInicio) = '" . $mes_invitacion . "' and IDInvitado = '" . $IDInvitado . "' $condicionPorSocio";
                $result_inv = $dbo->query($sql_inv);
                $row_inv_dia = $dbo->fetchArray($result_inv);
                if ($row_inv_dia["TotalMesInvitado"] >= $datos_regla["MaximoRepeticionInvitado"]) {
                    $resultado = "Lo sentimos supera el maximo de " . $datos_regla["MaximoRepeticionInvitado"] . " invitaciones permitidas por mes para el mismo invitado.";
                    $validacion = false;
                }
            }
            if ($datos_regla["MaximoInvitadoSocio"] >= 0) {
                //Consulto cuantas invitaciones el socio ha realizado al mes
                $sql_inv = "SELECT count(IDSocioInvitadoEspecial) as TotalInvitacionesMes FROM SocioInvitadoEspecial WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' AND YEAR(FechaInicio) = '" . date('Y') . "' and  MONTH(FechaInicio) = '" . $mes_invitacion . "'";
                $result_inv = $dbo->query($sql_inv);
                $row_inv_dia = $dbo->fetchArray($result_inv);
                if ($row_inv_dia["TotalInvitacionesMes"] > $datos_regla["MaximoInvitadoSocio"]) {
                    $resultado = "Lo sentimos supera el maximo de " . $datos_regla["MaximoInvitadoSocio"] . " invitaciones permitidas por mes";
                    $validacion = false;
                }
            }
        }
        $respuesta["message"] = $resultado;
        $respuesta["success"] = $validacion;
        $respuesta["response"] = $datos_regla;
        return $respuesta;
    }

    public function valida_regla_invitacion_v1($IDClub, $IDSocio, $NumeroDocumento, $FechaIngreso, $ValoresFormulario = "")
    {
        $dbo = &SIMDB::get();

        $mes_invitacion = substr($FechaIngreso, 5, 2);
        $year_invitacion = substr($FechaIngreso, 0, 4);
        $dia_invitacion = substr($FechaIngreso, 8, 2);
        $hoy = date("Y-m-d");
        $Dia = date("w", strtotime($FechaIngreso));

        $IDFestivo = $dbo->getFields("Festivos", "IDFestivo", "Fecha = '" . $FechaIngreso . "' and IDPais = 1");

        $resultado = "";
        $validacion = true;

        $datos_regla = SIMUtil::consulta_regla_invitacion($IDSocio, $IDClub);


        if ($datos_regla["IDRegla"] > 0) {

            // Valida Cantidad de Invitados por día, desde la configuración del Socio
            $NumeroInvitados = $dbo->getFields('Socio', 'NumeroInvitados', "IDSocio = $IDSocio");
            if ($NumeroInvitados > 0) {
                //Consulto que el socio no supere el maximo de invitaciones que tiene en el dia
                $sql_inv = "SELECT count(IDSocioInvitado) as TotalDia FROM SocioInvitado WHERE IDSocio = '$IDSocio' and IDClub = '$IDClub' and FechaIngreso = '$FechaIngreso'";
                $result_inv = $dbo->query($sql_inv);
                $row_inv_dia = $dbo->fetchArray($result_inv);
                $datos_regla["TotalDia"] = $row_inv_dia["TotalDia"];
                if ($row_inv_dia["TotalDia"] >= $NumeroInvitados) {

                    $resultado = "Lo sentimos supera el maximo de " . $NumeroInvitados . " invitaciones permitidas por dia";
                    $validacion = false;
                }
            } else {
                $resultado = "Lo sentimos, no tiene permisos para realizar invitaciones";
                $validacion = false;
            }
            // Fin Valida Cantidad de Invitados por día, desde la configuración del Socio


            if ($datos_regla["MaximoInvitadoDia"] >= 0) {
                $validaApp = $datos_regla['MaximoInvitadoDiaValidaApp'];

                //Consulto que el socio no supere el maximo de invitaciones que tiene en el dia
                $sql_inv = "SELECT count(IDSocioInvitado) as TotalDia FROM SocioInvitado WHERE IDSocio = '$IDSocio' and IDClub = '$IDClub' and FechaIngreso = '$FechaIngreso'";
                $result_inv = $dbo->query($sql_inv);
                $row_inv_dia = $dbo->fetchArray($result_inv);
                $datos_regla["TotalDia"] = $row_inv_dia["TotalDia"];
                if ($row_inv_dia["TotalDia"] >= $datos_regla["MaximoInvitadoDia"]) {
                    if ($validaApp == 1) {
                        $resultado = "Lo sentimos supera el maximo de " . $datos_regla["MaximoInvitadoDia"] . " invitaciones permitidas por dia";
                        $validacion = false;
                    }
                }
            }
            if ($datos_regla["MaximoRepeticionInvitado"] >= 0) {

                //Consulto cuantas veces el invitado ha sido invitado por el socio en el mes

                $porsocio = $datos_regla[MaximoRepeticionInvitadoSocio];
                $cumplimientoPasadas = $datos_regla[CumplimientoInvitados];
                $cumplimientoFuturas = $datos_regla[CumplimientoInvitadosFuturas];

                // SI ES PARA EL COUNTRY BARRANQUILLA NO SE VALIDA POR SOCIO
                if ($IDClub == 110) {
                    $datos_formulario = json_decode($ValoresFormulario, true);
                    foreach ($datos_formulario as $detalle_datos) :
                        if ($detalle_datos["IDCampoFormularioInvitado"] == '25') {
                            if ($detalle_datos["Valor"] != "AREA SOCIAL") {
                                $porsocio = 0;
                            }
                        }
                    endforeach;
                }

                if ($porsocio == 1)
                    $condicionPorSocio = " AND IDSocio = $IDSocio";

                if ($cumplimientoPasadas == 'S' && $cumplimientoFuturas == 'S') :
                    $condicionCumplidasFyP = " AND ((FechaIngreso >= '$hoy'  AND Estado = 'P') OR (FechaIngreso <= '$hoy' AND Estado = 'I'))";
                elseif ($cumplimientoPasadas == 'S' && $cumplimientoFuturas == 'N') :
                    $condicionCumplidasFyP = " AND ((FechaIngreso <= '$hoy' AND Estado = 'I'))";
                elseif ($cumplimientoPasadas == 'N' && $cumplimientoFuturas == 'S') :
                    $condicionCumplidasFyP = " AND ((FechaIngreso >= '$hoy'  AND Estado = 'P'))";
                else :
                    $condicionCumplidasFyP = "";
                endif;
                $condicionExtra = "";
                if ($IDClub == 44) {
                    $condicionExtra = " AND FechaIngreso != '2023-02-09' AND FechaIngreso != '2023-02-10' AND FechaIngreso != '2023-02-11' AND FechaIngreso != '2023-02-12'AND FechaIngreso != '2023-04-06'AND FechaIngreso != '2023-04-07' AND FechaIngreso != '2023-04-08' AND FechaIngreso != '2023-04-09'";
                }

                $sql_inv = "SELECT count(IDSocioInvitado) as TotalMesInvitado FROM SocioInvitado WHERE IDClub = '$IDClub' AND NumeroDocumento = '$NumeroDocumento' AND MONTH(FechaIngreso) = '$mes_invitacion' AND YEAR(FechaIngreso) = '$year_invitacion'  $condicionCumplidasFyP $condicionPorSocio $condicionExtra";
                $result_inv = $dbo->query($sql_inv);
                $row_inv_dia = $dbo->fetchArray($result_inv);
                if ($row_inv_dia["TotalMesInvitado"] >= $datos_regla["MaximoRepeticionInvitado"]) {

                    $resultado = "Lo sentimos supera el maximo de " . $datos_regla["MaximoRepeticionInvitado"] . " invitaciones permitidas por mes para el mismo invitado!";
                    $validacion = false;
                }

                // PARA BTC SE VALIDA 2 FINES DE SEMANA Y 2 ENTRE SEMANA

                if ($IDClub == 72) :
                    $CantidadFinSemana = 0;
                    $CantidadSemana = 0;
                    $SQLInvitaciones = "SELECT * FROM SocioInvitado WHERE IDClub = '$IDClub' AND NumeroDocumento = '$NumeroDocumento' AND MONTH(FechaIngreso) = '$mes_invitacion' AND YEAR(FechaIngreso) = '$year_invitacion'  $condicionCumplidasFyP $condicionPorSocio";
                    $QRYInvitaciones = $dbo->query($SQLInvitaciones);
                    while ($Data = $dbo->fetchArray($QRYInvitaciones)) :
                        $DiaFecha = date("w", strtotime($Data[FechaIngreso]));
                        if ($DiaFecha == '0' || $DiaFecha == '6') :
                            $CantidadFinSemana++;
                        else :
                            $CantidadSemana++;
                        endif;
                    endwhile;

                    if ($CantidadFinSemana > 2 && ($Dia == '0' || $Dia == '6')) {

                        $resultado = "Lo sentimos supera el maximo de " . $datos_regla["MaximoRepeticionInvitado"] . " invitaciones permitidas por mes para el mismo invitado los fines de semana!";
                        $validacion = false;
                    } elseif ($CantidadSemana > 2 && ($Dia != '0' && $Dia != '6')) {

                        $resultado = "Lo sentimos supera el maximo de " . $datos_regla["MaximoRepeticionInvitado"] . " invitaciones permitidas por mes para el mismo invitado entre semana!";
                        $validacion = false;
                    }

                endif;
            }

            if ($datos_regla["MaximoInvitadoSocio"] >= 0) {
                //Consulto cuantas invitaciones el socio ha realizado al mes
                $sql_inv = "SELECT count(IDSocioInvitado) as TotalInvitacionesMes FROM SocioInvitado WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'  and YEAR(FechaIngreso) = '" . $year_invitacion . "' and  MONTH(FechaIngreso) = '" . $mes_invitacion . "'";
                $result_inv = $dbo->query($sql_inv);
                $row_inv_dia = $dbo->fetchArray($result_inv);
                if ($row_inv_dia["TotalInvitacionesMes"] > $datos_regla["MaximoInvitadoSocio"]) {

                    $resultado = "Lo sentimos supera el maximo de " . $datos_regla["MaximoInvitadoSocio"] . " invitaciones permitidas por mes";
                    $validacion = false;
                }
            }
        }

        // COUNTRY CLUB BOGOTA REGLA ESPECIAL DE 4 LOS VIERNES, SABADO, DOMINGO, FESTIVOS
        /* if ($IDClub == 44 && ($Dia == 5 || $Dia == 6 || $Dia == 0 || !empty($IDFestivo)) && $IDSocio != 135019) :

            $accion_padre = $dbo->getFields("Socio", "AccionPadre", "IDSocio = '" . $IDSocio . "'");
            $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "'");
            if (empty($accion_padre)) : // Es titular
                $array_socio[] = $IDSocio;
                $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_socio . "' and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            else :
                $sql_nucleo = "Select IDSocio From Socio Where AccionPadre = '" . $accion_padre . "' or Accion = '" . $accion_padre . "' and IDClub = '" . $IDClub . "' ";
                $result_nucleo = $dbo->query($sql_nucleo);
                while ($row_nucleo = $dbo->fetchArray($result_nucleo)) :
                    $array_socio[] = $row_nucleo["IDSocio"];
                endwhile;
            endif;
            if (count($array_socio) > 0) :
                $id_socio_nucleo = implode(",", $array_socio);
            endif;

            $sql_inv = "SELECT count(IDSocioInvitado) as TotalDia FROM SocioInvitado WHERE IDSocio IN ($id_socio_nucleo) and IDClub = '$IDClub' and FechaIngreso = '$FechaIngreso'";
            $result_inv = $dbo->query($sql_inv);
            $row_inv_dia = $dbo->fetchArray($result_inv);
            $datos_regla["TotalDia"] = $row_inv_dia["TotalDia"];

            if ($row_inv_dia["TotalDia"] >= 4) {

                $resultado = "Lo sentimos solo se pueden 4 invitaciones por grupo familiar los viernes, sabados, domingos y festivos";
                $validacion = false;
            }
        endif; */


        $datos_regla["TotalMesInvitado"] = $row_inv_dia["TotalMesInvitado"];
        $datos_regla["TotalInvitacionesMes"] = $row_inv_dia["TotalInvitacionesMes"];

        $respuesta["message"] = $resultado;
        $respuesta["success"] = $validacion;
        $respuesta["response"] = $datos_regla;

        return $respuesta;
    }

    // NUEVAS

    public function getNumeroVistaAcceso($IDInvitacion)
    {
        $dbo = &SIMDB::get();

        $sql = "SELECT NumeroVisita FROM LogAcceso WHERE IDInvitacion=$IDInvitacion ORDER BY NumeroVisita DESC LIMIT 1";
        $queryAcceso = $dbo->query($sql);
        $acceso = $dbo->fetch($queryAcceso);

        $visita = 0;
        if (!empty($acceso)) {
            $visita = $acceso["NumeroVisita"];
        }

        return $visita;
    }

    public function get_campos_invitados($IDClub, $IDSocio = "")
    {
        $dbo = &SIMDB::get();
        $response = array();

        //Campos Formulario
        $response_campo_formulario = array();
        $sql_campo_form = "SELECT * FROM CampoFormularioInvitado WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
        $qry_campo_form = $dbo->query($sql_campo_form);
        if ($dbo->rows($qry_campo_form) > 0) {
            while ($r_campo = $dbo->fetchArray($qry_campo_form)) {

                // Validación reglas tarjetas rotativas Club Villa Peru
                // if ($IDClub == 220 && $r_campo['IdentificadorPregunta'] == 'PTR') {
                if (($IDClub == 8 || $IDClub == 220)  && $r_campo['IdentificadorPregunta'] == 'PTR') {
                    if (!empty($r_campo['Valores'])) {
                        $arr_Tarjetas = explode(',', $r_campo['Valores']);
                        $TarjetasDisponibles = array();
                        foreach ($arr_Tarjetas as $tarjeta) {
                            $IDTipoTarjetaRotativa = $dbo->getFields("TipoTarjetaRotativa", "IDTipoTarjetaRotativa", "IDClub = $IDClub AND Nombre ='$tarjeta' AND Publicar ='S'");
                            if ($IDTipoTarjetaRotativa != false) {

                                $ValidaDisponibilidadTarjeta = $dbo->getFields("TarjetaRotativa", "IDTarjetaRotativa", "IDSocio = $IDSocio AND  IDTipoTarjetaRotativa = $IDTipoTarjetaRotativa");

                                if ($ValidaDisponibilidadTarjeta != false) {
                                    array_push($TarjetasDisponibles, "{$tarjeta}");
                                }
                            }
                        }
                        $r_campo['Valores'] = implode(',', $TarjetasDisponibles);

                        $campoformulario["IDCampoFormularioInvitado"] = $r_campo["IDCampoFormularioInvitado"];
                        $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                        $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                        $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                        $campoformulario["Valores"] = $r_campo["Valores"];
                        array_push($response, $campoformulario);
                    }
                } else {
                    $campoformulario["IDCampoFormularioInvitado"] = $r_campo["IDCampoFormularioInvitado"];
                    $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                    $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                    $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                    $campoformulario["Valores"] = $r_campo["Valores"];
                    array_push($response, $campoformulario);
                }
            } //end while
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehanencontradocampos', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_campos_contratista($IDClub)
    {
        $dbo = &SIMDB::get();
        $response = array();

        //Campos Formulario
        $response_campo_formulario = array();
        $sql_campo_form = "SELECT * FROM CampoFormularioContratista WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
        $qry_campo_form = $dbo->query($sql_campo_form);
        if ($dbo->rows($qry_campo_form) > 0) {
            while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                $campoformulario["IDCampoFormularioContratista"] = $r_campo["IDCampoFormularioContratista"];
                $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                $campoformulario["Valores"] = $r_campo["Valores"];
                array_push($response, $campoformulario);
            } //end while
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehanencontradocampos', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function set_presalida_cualquiera($IDClub, $IDSocio, $IDInvitacion, $TipoInvitacion, $TipoSocio)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDInvitacion) && !empty($TipoInvitacion)) {

            switch ($TipoInvitacion) {
                case "InvitadoAcceso":
                    $sql_presalida = "Update  SocioInvitadoEspecial Set IDSocioPresalida = '" . $IDSocio . "', Presalida = 'S', FehaPresalida = NOW() Where IDSocioInvitadoEspecial = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "'  Limit 1";
                    $dbo->query($sql_presalida);
                    break;
                case "Contratista":
                    $sql_presalida = "Update  SocioAutorizacion Set IDSocioPresalida = '" . $IDSocio . "', Presalida = 'S', FehaPresalida = NOW() Where IDSocioAutorizacion = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "'  Limit 1";
                    $dbo->query($sql_presalida);
                    break;
                case "Socio":
                    $sql_presalida = $dbo->query("Update  Socio Set IDSocioPresalida = '" . $IDSocio . "', Presalida = 'S', FehaPresalida = NOW() Where IDSocio = '" . $IDInvitacion . "' and IDClub = '" . $IDClub . "'  Limit 1");
                    break;
                case "SocioInvitado":
                case "Invitado":
                    break;
            }
            $respuesta["message"] = "Presalida registrada con exito." . $mensaje_alerta_ingreso;
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "7. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function get_tipo_ingreso($IDClub, $IDSocio)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $TipoIngreso = utf8_decode($dbo->getFields("AppEmpleado", "OpcionesIngreso", "IDClub = '" . $IDClub . "'"));
        $array_tipo = explode(",", $TipoIngreso);
        if (count($array_tipo) > 0) {
            $encontrados = 1;
            $message = count($array_tipo) . " Encontrados";
            foreach ($array_tipo as $value) {
                $tipo_ingreso["Nombre"] = utf8_encode($value);
                array_push($response, $tipo_ingreso);
            }
        }

        $sql_vehiculo = "Select * From Vehiculo Where IDSocio = '" . $IDSocio . "' ";
        $r_vehiculo = $dbo->query($sql_vehiculo);
        if ($dbo->rows($r_vehiculo) > 0) {
            $encontrados = 1;
            while ($row_vehiculo = $dbo->fetchArray($r_vehiculo)) {
                $tipo_ingreso["Nombre"] = "Vehiculo: " . utf8_encode($row_vehiculo["Placa"]);
                array_push($response, $tipo_ingreso);
            }
        }

        if ($encontrados == 1) {
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    } // fin function

    public function reglas_invitados_golf_club_uruguay($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario, $IDUsuario = "")
    {
        $dbo = SIMDB::get();

        $mes_invitacion = substr($FechaIngreso, 5, 2);
        $year_invitacion = substr($FechaIngreso, 0, 4);
        $dia_invitacion = substr($FechaIngreso, 8, 2);
        $hoy = date("Y-m-d");
        $Dia = date("w", strtotime($FechaIngreso));
        $validacion = 0;
        $Comentarios = "";

        // BUSCAMOS LA CATEGORIA DEL SOCIO
        $SQLDatosSocio = "SELECT TipoSocio, IDCategoria, AccionPadre FROM Socio WHERE IDSocio = $IDSocio";
        $QRYDatosSocio = $dbo->query($SQLDatosSocio);
        $DatosSocio = $dbo->fetchArray($QRYDatosSocio);
        // SABER LA CANTIDAD DEL GRUPO FAMILIAR
        $SQLGrupo = "SELECT COUNT(IDSocio) AS CantidadBeneficiario FROM `Socio` WHERE (AccionPadre = $DatosSocio[AccionPadre] OR Accion = $DatosSocio[AccionPadre]) AND IDClub = $IDClub";
        $QRYGrupo = $dbo->query($SQLGrupo);
        $DatosGrupo = $dbo->fetchArray($QRYGrupo);
        // Comentado por solucitud: solo los socios preferentes pueden invitar
        // if ($DatosSocio[TipoSocio] == "Asociado preferente" || $DatosSocio[TipoSocio] == "Adherente") :
        //     $CantidadInivitadoExSocioMes = 1;
        //     $CantidadInvitacionesValidas = 10 - $DatosGrupo[CantidadBeneficiario];
        // elseif ($DatosSocio[TipoSocio] == "Membresia" || $DatosSocio[TipoSocio] == "Usuario de Servicio") :
        //     $CantidadInivitadoExSocioMes = 1;
        //     $CantidadInvitacionesValidas = 8 - $DatosGrupo[CantidadBeneficiario];
        // endif;
        if ($DatosSocio['TipoSocio'] == "Asociados Preferentes") :
            $CantidadInivitadoExSocioMes = 1;
            $CantidadInvitacionesValidas = 10 - $DatosGrupo['CantidadBeneficiario'];
        endif;
        // var_dump($CantidadInivitadoExSocioMes);
        // var_dump($CantidadInvitacionesValidas);
        // die();
        // VALIDAMOS SI EL INVITADO ES UN EXASOCIADO
        $SQLExSocio = "SELECT IDSocio, IDEstadoSocio FROM Socio WHERE NumeroDocumento = $NumeroDocumento";
        $QRYExSocio = $dbo->query($SQLExSocio);
        $Datos = $dbo->fetchArray($QRYExSocio);

        if ($Datos['IDEstadoSocio'] == 2) :
            // BUSCAMOS NUMERO DE INIVTACIONES EN EL MES
            $sql_inv = "SELECT count(IDSocioInvitado) as TotalMesInvitado FROM SocioInvitado WHERE IDClub = '$IDClub' AND NumeroDocumento = '$NumeroDocumento' AND MONTH(FechaIngreso) = '$mes_invitacion' AND YEAR(FechaIngreso) = '$year_invitacion'  AND ((FechaIngreso > '$hoy'  AND Estado = 'P'))";
            $result_inv = $dbo->query($sql_inv);
            $row_inv_dia = $dbo->fetchArray($result_inv);
            if ($row_inv_dia["TotalMesInvitado"] >= $CantidadInivitadoExSocioMes) {

                $resultado = "Lo sentimos, el invitado es un exasociado y solo puede ser invitado una vez al mes y ya se cumplio";
                $validacion = 1;
            }
        endif;

        $ValoresFormulario = json_decode($ValoresFormulario);
        $Campo = $dbo->getFields("CampoFormularioInvitado", "EtiquetaCampo", "IDCampoFormularioInvitado = " . $ValoresFormulario[0]->IDCampoFormularioInvitado);
        if ($Campo == "Tipo Invitado" && $ValoresFormulario[0]->Valor == "Invitado por derecho") {


            // $sql_inv = "SELECT count(IDSocioInvitado) as TotalDia FROM SocioInvitado, WHERE IDSocio = '$IDSocio' and IDClub = '$IDClub' and FechaIngreso = '$FechaIngreso' ";
            $sql_inv = "SELECT count(IDSocioInvitado) as TotalDia FROM SocioInvitado si ,InvitadosOtrosDatos iod WHERE si.IDSocioInvitado=iod.IDInvitacion AND si.IDSocio = $IDSocio AND FechaIngreso = '{$FechaIngreso}' AND Valor = 'Invitado por derecho' AND Mostrar != 'N'";
            $result_inv = $dbo->query($sql_inv);
            $row_inv_dia = $dbo->fetchArray($result_inv);
            if ($row_inv_dia["TotalDia"] >= $CantidadInvitacionesValidas) {

                $resultado = "Supera el maximo de cupos diarios entre su grupo familiar y sus invitados, a partir de este momento las invitaciones tendran un costo de $38.500.";
                $validacion = 2;
                $Comentarios = "Invitado con pago";
            }
        } else {

            $resultado = "Esta invitación tiene un costo de $38.500, este pago lo puede realizar desde el módulo Otros pagos o acercándose a la recepción del club.";
            $validacion = 2;
            $Comentarios = "Invitado con pago";
        }
        // Los siguientes tipos de socio no tienen limite pero deben pagar cada invitado
        if ($DatosSocio['TipoSocio'] == "Membresía" || $DatosSocio['TipoSocio'] == "Usuario de Servicios") :

            $resultado = "Esta invitación tiene un costo de $38.500, este pago lo puede realizar desde el módulo Otros pagos o acercándose a la recepción del club.";
            $validacion = 2;
            $Comentarios = "Invitado con pago";
        endif;
        $respuesta["message"] = $resultado;
        $respuesta["success"] = $validacion;
        $respuesta["response"] = $Comentarios;
        return $respuesta;
    }

    public function Reglas_invitados_ClubGolfUruguay($IDClub, $IDSocio, $FechaIngreso, $FechaSalida, $DatosInvitado, $ValoresFormulario = "", $IDServicio = "")
    {
        $dbo = SIMDB::get();

        $DatosInvitado = json_decode($DatosInvitado);
        $NumeroDocumento = $DatosInvitado[0]->NumeroDocumento;
        // 230313: Piscina exterior, 22972: Golf, 23070: Tenis
        if (!empty($IDServicio) && ($IDServicio == 23013 || $IDServicio == 22972 || $IDServicio == 23070)) {
            $anio = date('Y');
            $mes = date('m');
            $fechaActual = date('Y-m');
            $FechaIngreso = date('Y-m', strtotime($FechaIngreso));
            if ($fechaActual == $FechaIngreso) {

                // Validar invitaciones por Socio, una por mes, por cada servicio solicitado
                $sql_invitacion = "SELECT r.IDReservaGeneral FROM ReservaGeneral r,ReservaGeneralInvitado ri WHERE r.IDReservaGeneral = ri.IDReservaGeneral and r.IDClub = $IDClub and ri.IDSocioInvita = $IDSocio and r.IDServicio = $IDServicio and YEAR(r.Fecha) = '$anio' and MONTH(r.Fecha) = '$mes'";
                $q_invitacion = $dbo->query($sql_invitacion);
                $Invitaciones = $dbo->rows($q_invitacion);
                if ($Invitaciones >= 1) {
                    $msg = "Lo sentimos, solo puede invitar a una persona a este servicio por mes";
                    $respuesta["message"] = $msg;
                    $respuesta["success"] = false;
                    $respuesta["response"] = "";
                    return $respuesta;
                }

                // Validar invitaciones por invtado externo, una por mes, por cada servicio solicitado
                $sql_invitado = "SELECT r.IDReservaGeneral FROM ReservaGeneral r,ReservaGeneralInvitado ri WHERE r.IDReservaGeneral = ri.IDReservaGeneral and r.IDClub = $IDClub and r.IDServicio in (23013,22972,23070) and ri.Cedula = '$NumeroDocumento' and YEAR(r.Fecha) = '$anio' and MONTH(r.Fecha) = '$mes'";

                $q_invitado = $dbo->query($sql_invitado);
                $Invitado = $dbo->rows($q_invitado);
                if ($Invitado >= 1) {
                    // $msg = "Lo sentimos, el invitado solo puede acceder a los servicios de Piscina exterior, Golg o Tenis una vez en el mes";
                    $msg = SIMUtil::get_traduccion('', '', 'yafueinvitadoestemesporunsocioenesteservicioynopuedeserinvitadodenuevo', LANG) . ".";

                    $respuesta["message"] = $msg;
                    $respuesta["success"] = false;
                    $respuesta["response"] = "";
                    return $respuesta;
                }

                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = "";
                return $respuesta;
            } else {
                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = "";
                return $respuesta;
            }
        } else {
            $respuesta["message"] = "";
            $respuesta["success"] = true;
            $respuesta["response"] = "";
            return $respuesta;
        }
    }
    public function reglas_invitados_carmel_club($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario)
    {

        $dbo = SIMDB::get();
        $semana = date("W", strtotime($FechaIngreso));
        $anio = date("Y", strtotime($FechaIngreso));
        $mes = date("m", strtotime($FechaIngreso));

        // Limite reglas para el carmel
        // Maximo invitaciones para la misma persona en una semana
        $MaxMismoInvitadoEntreSemana = 1;
        // Maximo invitaciones para la misma persona para todos los fines de semana y festivos por mes
        $MaxMismoInvitadoMesFinesDeSemana = 2;

        // Valida invitaciones en dias festivos
        $sql = "SELECT * from SocioInvitado where IDClub = $IDClub and year(FechaIngreso) = '$anio' and month(FechaIngreso) = '$mes' and NumeroDocumento = '$NumeroDocumento'";
        $q_invitaciones = $dbo->query($sql);
        $arr_FechasFestivos = array();
        while ($rows = $dbo->assoc($q_invitaciones)) {
            $ValidaInvitacionesFestivo = SIMUtil::validaDiaFestivo($IDClub, $rows['FechaIngreso']);
            if ($ValidaInvitacionesFestivo === true) {
                array_push($arr_FechasFestivos, "'" . $rows['FechaIngreso'] . "'");
            }
        }

        if (count($arr_FechasFestivos) > 1) {
            $InvitacionesEnDiasFestivos = implode(",", $arr_FechasFestivos);
        } else {
            $InvitacionesEnDiasFestivos = $arr_FechasFestivos[0];
        }


        function getStartAndEndDate($semana, $anio)
        {
            $dto = new DateTime();
            $dto->setISODate($anio, $semana);
            $ret['lun'] = $dto->format('Y-m-d');
            $dto->modify('+1 days');
            $ret['mar'] = $dto->format('Y-m-d');
            $dto->modify('+1 days');
            $ret['mie'] = $dto->format('Y-m-d');
            $dto->modify('+1 days');
            $ret['jue'] = $dto->format('Y-m-d');
            $dto->modify('+1 days');
            $ret['vie'] = $dto->format('Y-m-d');
            $dto->modify('+1 days');
            $ret['sab'] = $dto->format('Y-m-d');
            $dto->modify('+1 days');
            $ret['dom'] = $dto->format('Y-m-d');
            return $ret;
        }

        $week_array = getStartAndEndDate($semana, $anio);

        $DiaIngreso = date("D", strtotime($FechaIngreso));
        $ValidaFestivo = SIMUtil::validaDiaFestivo($IDClub, $FechaIngreso);
        if ($DiaIngreso == 'Sat' || $DiaIngreso == 'Sun' || $ValidaFestivo === true) {
            // Valida fines de semana y festivos
            $FiltroFestivos = (!empty($InvitacionesEnDiasFestivos)) ? "|| FechaIngreso in ($InvitacionesEnDiasFestivos)" : "";
            $sql_invitacion = "SELECT * from SocioInvitado where IDClub = $IDClub and year(FechaIngreso) = '$anio' and month(FechaIngreso) = '$mes' and NumeroDocumento = '$NumeroDocumento' and (DATE_FORMAT(FechaIngreso,'%W') = 'Saturday' || DATE_FORMAT(FechaIngreso,'%W') = 'Sunday' $FiltroFestivos)";

            $q_invitaciones = $dbo->query($sql_invitacion);
            $rows = $dbo->rows($q_invitaciones);
            if ($rows >= $MaxMismoInvitadoMesFinesDeSemana) {
                $respuesta['message'] = "Lo sentimos, esta persona supera el limite de $MaxMismoInvitadoMesFinesDeSemana invitaciones para fines de semana en este mes";
                $respuesta['success'] = false;
                $respuesta['response'] = "";
                return $respuesta;
            }
        } else {
            // Valida dias entre semana
            $sql_invitacion = "SELECT * from SocioInvitado where IDClub = $IDClub and year(FechaIngreso) = '$anio' and month(FechaIngreso) = '$mes' and NumeroDocumento = '$NumeroDocumento'  and (FechaIngreso >= '{$week_array['lun']}' and FechaIngreso <= '{$week_array['vie']}')";

            $q_invitaciones = $dbo->query($sql_invitacion);
            $rows = $dbo->rows($q_invitaciones);
            if ($rows >= $MaxMismoInvitadoEntreSemana) {
                $respuesta['message'] = "Lo sentimos, esta persona supera el limite de $MaxMismoInvitadoEntreSemana invitaciones para esta semana";
                $respuesta['success'] = false;
                $respuesta['response'] = "";
                return $respuesta;
            }
        }
    }
    public function reglas_invitados_club_sanjacinto($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario)
    {
        $dbo = SIMDB::get();
        $ValoresFormulario = json_decode($ValoresFormulario);
        // Valida si fue invitado al club
        $Valida = 0;
        foreach ($ValoresFormulario as $id => $valor) {

            if ($valor->Valor == "Club" || $valor->Valor == "Casa,Club") {
                $Valida = 1;
            }
        }

        if ($Valida == 1) {
            // Limite solicitado
            $MaxInvitadosClubDia = 5;
            $sql = "select COUNT(*) as invitaciones from SocioInvitado s,InvitadosOtrosDatos o where s.IDSocioInvitado=o.IDInvitacion and s.IDClub = $IDClub and s.IDSocio=$IDSocio and FechaIngreso = '$FechaIngreso' and  o.Valor like '%Club%'";
            $q_invitaciones = $dbo->query($sql);
            $resultado = $dbo->fetchArray($q_invitaciones);
            $r_invitaciones = $resultado['invitaciones'];
            if ($r_invitaciones >= $MaxInvitadosClubDia) {
                $respuesta["message"] = "Lo sentimos, supera el limite de $MaxInvitadosClubDia invitaciones al Club por día";
                $respuesta["success"] = false;
                $respuesta["response"] = "";
            } else {
                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = "";
            }
        } else {
            $respuesta["message"] = "";
            $respuesta["success"] = true;
            $respuesta["response"] = "";
        }
        return $respuesta;
    }
    public function reglas_invitados_club_sanAndres($IDClub, $IDSocio, $NumeroDocumento, $Nombre, $FechaIngreso, $ValoresFormulario)
    {
        $dbo = SIMDB::get();
        $anio = date('Y');
        $mes = date('m');
        $fechaActual = date('Y-m');
        $FechaIngreso = date('Y-m', strtotime($FechaIngreso));
        if ($fechaActual == $FechaIngreso) {

            $MaxIngresosMesInvitados = 2;
            $sql_ingresos = "SELECT l.IDLogAcceso FROM LogAcceso l,SocioInvitado s WHERE l.IDInvitacion=s.IDSocioInvitado and l.IDClub = $IDClub and s.NumeroDocumento = '$NumeroDocumento' and l.Tipo = 'SocioInvitado' and YEAR(l.FechaIngreso) = '$anio' and MONTH(l.FechaIngreso) = '$mes'";
            $q_ingresos = $dbo->query($sql_ingresos);
            $Ingresos = $dbo->rows($q_ingresos);
            if ($Ingresos >= $MaxIngresosMesInvitados) {
                $msg = "Lo sentimos, el Invitado ya ha ingresado $Ingresos veces al club este mes";
                $respuesta["message"] = $msg;
                $respuesta["success"] = false;
                $respuesta["response"] = "";
            } else {
                $respuesta["message"] = "";
                $respuesta["success"] = true;
                $respuesta["response"] = "";
            }
        } else {
            $respuesta["message"] = "";
            $respuesta["success"] = true;
            $respuesta["response"] = "";
        }
        return $respuesta;
    }
} //end class
