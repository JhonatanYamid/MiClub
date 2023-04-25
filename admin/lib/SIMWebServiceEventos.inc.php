<?php

class SIMWebServiceEventos
{
    public function get_seccionevento($IDClub, $IDSocio = "", $Version = "")
    {
        $dbo = &SIMDB::get();

        if (!empty($IDSocio)) :
        //$condicion = " and SSE.IDSeccionEvento = S.IDSeccionEvento and IDSocio = '" . $IDSocio . "' ";
        //$tabla_join = ", SocioSeccionEvento SSE ";
        endif;

        if ($IDClub == 17) :
            $respuesta = SIMWebServiceFedegolf::get_seccionevento($IDClub, $IDSocio, $Version);
            return $respuesta;
        endif;

        $response = array();
        $sql = "SELECT S.* FROM SeccionEvento" . $Version . " S " . $tabla_join . " WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY S.Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                //$id_noticia = $dbo->getFields( "Evento", "IDEvento", "IDSeccionEvento = '" . $r[ "IDSeccionEvento" ] . "' and Publicar = 'S' and FechaFinEvento >= NOW()" );
                $id_noticia = $dbo->getFields("Evento" . $Version, "IDEvento" . $Version, "(DirigidoA = 'S' or DirigidoA = 'T') and IDSeccionEvento" . $Version . " = '" . $r["IDSeccionEvento" . $Version] . "' and Publicar = 'S' and  FechaInicio <= CURDATE() and FechaFin >= CURDATE()");
                if (!empty($id_noticia)) :
                    $seccion["IDClub"] = $r["IDClub"];
                    $seccion["IDSeccion"] = $r["IDSeccionEvento" . $Version];
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

    public function get_eventos($IDClub, $IDSeccion = "", $IDSocio = "", $Tag = "", $Nombre, $Fecha = "", $Version = "")
    {
        $dbo = &SIMDB::get();

        if ($IDClub == 17) :
            $respuesta = SIMWebServiceFedegolf::get_eventos($IDClub, $IDSeccion, $IDSocio, $Tag, $Fecha, $Version);
            return $respuesta;
        endif;

        // Secciones Socio
        if (!empty($IDSocio) && $IDSeccion == "") :

            $sql_seccion_socio = $dbo->query("Select * From SocioSeccionEvento" . $Version . " Where IDSocio = '" . $IDSocio . "'");

            while ($row_seccion = $dbo->fetchArray($sql_seccion_socio)) :
                $array_secciones_socio[] = $row_seccion["IDSeccionEvento"];
            endwhile;

            if (count($array_secciones_socio) > 0) :
                $IDSecciones = implode(",", $array_secciones_socio);
            //$array_condiciones[] = " IDSeccionEvento".$Version." in(".$IDSecciones.") ";
            endif;

            $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
            if ($IDClub == 36 && $TipoSocio == "Estudiante") {
                $array_condiciones[] = " TipoSocio = 'Estudiante'";
            } else {
                $array_condiciones[] = " (TipoSocio = '' or TipoSocio = 'Socio' ) ";
            }
        endif;

        // Seccion Especifica
        if (!empty($IDSeccion)) :
            $array_condiciones[] = " IDSeccionEvento" . $Version . "  = '" . $IDSeccion . "'";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Titular  like '%" . $Tag . "%' or Introduccion like '%" . $Tag . "%' or Cuerpo like '%" . $Tag . "%' or Lugar like '%" . $Tag . "%' )";
        endif;
        /* 
        if (!empty($Fecha)) :
            $array_condiciones[] = " FechaEvento  = '" . $Fecha . "'";
        endif; */

        //Buscador Por Nombre o por rangos de fecha
        if (!empty($Nombre)) {
            $array_condiciones[] = " (Titular  like '%" . $Nombre . "%' OR Introduccion LIKE '%" . $Nombre . "%')";
        } else if (empty($Nombre) && !empty($Fecha)) {
            $array_condiciones[] = " ((FechaEvento  = '" . $Fecha . "') OR (FechaEvento <='" . $Fecha . "' AND FechaFin>='" . $Fecha . "' AND '" . $Fecha . "' <= FechaFinEvento))";
            // $array_condiciones[] = " FechaEvento  = '" . $Fecha . "'";
            // $array_condiciones[] = " (Titular  like '%" . $Nombre . "%' OR Introduccion LIKE '%" . $Nombre . "%')";
        }

        /* if (!empty($Fecha)) {
            $array_condiciones[] = " FechaEvento  >= '" . $Fecha . "' AND FechaFinEvento<=" . $Fecha . "'";
        } */

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_noticia = " and " . $condiciones;
        endif;

        if ($IDClub == 34) :
            $orden = " IDEvento" . $Version . " DESC";
            $CondicionFechaEvento = " ";
        //elseif($IDClub == 44):
        //$orden = " Orden ASC";
        //$CondicionFechaEvento = " and FechaFinEvento >= CURDATE() ";
        elseif ($IDClub == 37) :
            $CondicionFechaEvento = " and FechaFinEvento >= CURDATE() ";
            $orden = " FechaEvento ASC";
        elseif ($IDClub == 88 || $IDClub == 86) :
            $orden = " FechaEvento DESC";
            $CondicionFechaEvento = " ";
        elseif ($IDClub == 17 || $IDClub == 49  || $IDClub == 133) :
            $orden = " FechaEvento ASC";
            $CondicionFechaEvento = " ";
        else :
            $orden = " Orden ASC";
        //$CondicionFechaEvento = " and FechaFinEvento >= CURDATE() ";
        endif;

        $response = array();
        $sql = "SELECT * FROM Evento" . $Version . " WHERE (DirigidoA = 'S' or DirigidoA = 'T') and Publicar = 'S' and FechaInicio <= CURDATE() and FechaFin >= CURDATE() " . $CondicionFechaEvento . " and IDClub = '" . $IDClub . "' " . $condiciones_noticia . " ORDER BY " . $orden . " ";

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $evento["IDClub"] = $r["IDClub"];
                $evento["IDSeccionEvento"] = $r["IDSeccionEvento" . $Version];

                //si es el tipofiltro es calendario se coloca la fecha segun el usuario seleccione
                $TipoFiltroEvento = $dbo->getFields("Club", "TipoFiltroEvento" . $Version, "IDClub = '" . $IDClub . "'");

                if ($TipoFiltroEvento == "Calendario") {

                    $evento["Titular"] = $Fecha . ": " . $r["Titular"];
                } else {
                    $evento["Titular"] = $r["FechaEvento"] . ": " . $r["Titular"];
                }
                $evento["IDEvento"] = $r["IDEvento" . $Version];
                // $evento["Titular"] = $r["FechaEvento"] . ": " . $r["Titular"];
                $evento["Introduccion"] = $r["Introduccion"];
                $evento["Introduccion"] = $r["Introduccion"];

                $cuerpo_evento = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Cuerpo"]);

                //Documentos adjuntos
                if (!empty($r["Adjunto1File"])) :
                    $cuerpo_evento .= "<br><a href='" . IMGEVENTO_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                endif;
                if (!empty($r["Adjunto2File"])) :
                    $cuerpo_evento .= "<br><a href='" . IMGEVENTO_ROOT . $r["Adjunto2File"] . "' >" . $r["Adjunto2File"] . '</a>';
                endif;
                if (!empty($r["Adjunto3File"])) :
                    $cuerpo_evento .= "<br><a href='" . IMGEVENTO_ROOT . $r["Adjunto3File"] . "' >" . $r["Adjunto3File"] . '</a>';
                endif;
                if (!empty($r["Adjunto4File"])) :
                    $cuerpo_evento .= "<br><a href='" . IMGEVENTO_ROOT . $r["Adjunto4File"] . "' >" . $r["Adjunto4File"] . '</a>';
                endif;

                $evento["Cuerpo"] = $cuerpo_evento;

                $evento["CuerpoEmail"] = $r["CuerpoEmail"];

                $fechaInicio = strtotime($r["FechaEvento"]);
                $fechaFin = strtotime($r["FechaEvento"]);
                for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400) {
                    $evento["Fecha"] = $r["FechaEvento"];
                }


                $evento["FechaFinEvento"] = $r["FechaFinEvento"];
                $evento["Lugar"] = $r["Lugar"];

                $HoraFinEvento = (string) $r["HoraFin"];
                if ($HoraFinEvento > "00:00:00") {
                    $HoraFin = " - " . $HoraFinEvento;
                }
                $HoraEvento = (string) $r["Hora"];
                if ($HoraEvento == "00:00:00") {
                    $evento["Hora"] = "";
                    $mostrtaHora = "";
                } else {
                    $evento["Hora"] = (string) $r["Hora"] . $HoraFin;
                    $mostrtaHora = " HORA:" . $evento["Hora"];
                }

                $evento["Fecha"] = $r["FechaEvento"];



                $evento["Valor"] = $r["Valor"];
                $evento["EmailContacto"] = $r["EmailContacto"];
                $evento["InscripcionApp"] = $r["InscripcionApp"];

                if ($r["InscripcionApp"] == "S") {
                    //Verifico si quedan cupos
                    $sql_registrados = "SELECT count(*) as Total FROM  EventoRegistro" . $Version . " Where IDEvento" . $Version . " = '" . $r["IDEvento"] . "'";


                    $r_registrados = $dbo->query($sql_registrados);
                    $row_registrados = $dbo->FetchArray($r_registrados);

                    if ((int) $row_registrados["Total"] >= (int) $r["MaximoParticipantes"]) {

                        $evento["InscripcionApp"] = "N";
                        $evento["Cuerpo"] .= "<br><strong>" . SIMUtil::get_traduccion('', '', 'Sellegoalmaximodecupos.', LANG) . "</strong>";
                    }

                    //verifico  la fecha/hora límite de inscripcion
                    $fechahora_actual = date("Y-m-d H:i:s");
                    $FechaHoraLimite = $r["FechaLimiteInscripcion"] . " " . $r["HoraLimiteInscripcion"];
                    $FechaLimiteInscripcion = $r["FechaLimiteInscripcion"];
                    $HoraLimiteInscripcion = $r["HoraLimiteInscripcion"];
                    if ($FechaLimiteInscripcion !== "0000-00-00" &&  $HoraLimiteInscripcion !== "00:00" && strtotime($fechahora_actual) > strtotime($FechaHoraLimite)) {

                        $evento["InscripcionApp"] = "N";
                        $evento["Cuerpo"] .= "<br><strong>" . SIMUtil::get_traduccion('', '', 'Inscripcionescerradas.', LANG) . "</strong>";
                    }
                }

                $evento["MensajePagoInscripcion"] = $r["MensajePagoInscripcion"];
                //Tipos de pagos recibidos
                $response_tipo_pago = array();
                $sql_tipo_pago = "SELECT * FROM EventoTipoPago" . $Version . " ETP, TipoPago TP  WHERE ETP.IDTipoPago = TP.IDTipoPago and IDEvento" . $Version . " = '" . $r["IDEvento" . $Version] . "' ";
                $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                if ($dbo->rows($qry_tipo_pago) > 0) {
                    $evento["PagoReserva"] = "S";
                    while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                        $tipopago["IDClub"] = $IDClub;
                        $tipopago["IDServicio"] = $r_tipo_pago["IDServicio"];
                        $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                        $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                        $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $IDClub);

                        if ($IDClub == 93 && $r_tipo_pago["IDTipoPago"] == 3)
                            $tipopago["Nombre"] = 'Pago en recepción.';
                        else
                            $tipopago["Nombre"] = $r_tipo_pago["Nombre"];

                        if ($IDClub == 15 && $r_tipo_pago["IDTipoPago"] == 15) :
                            $tipopago["Nombre"] = "Pago en efectivo";
                        endif;

                        $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                        $imagen = "";
                        //Para el condado y es pagos online muestro la imagen de placetopay
                        switch ($r_tipo_pago["IDTipoPago"]) {
                            case "1":
                                $imagen = "https://static.placetopay.com/placetopay-logo.svg";
                                break;
                            case "2":
                                $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                break;
                            case "3":
                                $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                break;
                            case "11":
                                $imagen = "https://www.miclubapp.com/file/noticia/260838_Sin_t__tulo.png";
                                break;
                            case "17":
                                $imagen = "https://www.miclubapp.com/file/tipopago/Tarjeta.png";
                                break;
                            case "15":
                                $imagen = "https://www.miclubapp.com/file/tipopago/Efectivo.png";
                                break;
                        }

                        $tipopago["Imagen"] = $imagen;
                        array_push($response_tipo_pago, $tipopago);

                    } //end while
                    $evento["TipoPago"] = $response_tipo_pago;
                } else {
                    $evento["PagoReserva"] = "N";
                }

                //Campos Formulario
                $response_campo_formulario = array();
                $sql_campo_form = "SELECT * FROM CampoFormularioEvento" . $Version . " WHERE IDEvento" . $Version . " = '" . $r["IDEvento" . $Version] . "' and Publicar = 'S' order by Orden ";
                $qry_campo_form = $dbo->query($sql_campo_form);
                if ($dbo->rows($qry_campo_form) > 0) {
                    while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                        $campoformulario["IDCampoFormularioEvento"] = $r_campo["IDCampoFormularioEvento" . $Version];
                        $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                        $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                        $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                        $campoformulario["Valores"] = $r_campo["Valores"];
                        array_push($response_campo_formulario, $campoformulario);
                    } //end while
                    $evento["CampoFormulario"] = $response_campo_formulario;
                } else {
                    $evento["CampoFormulario"] = $response_campo_formulario;
                }

                if (!empty($r["EventoFile"])) :
                    if (strstr(strtolower($r["EventoFile"]), "http://")) {
                        $foto1 = $r["EventoFile"];
                    } elseif (strstr(strtolower($r["EventoFile"]), "https://")) {
                        $foto1 = $r["EventoFile"];
                    } else {
                        $foto1 = IMGEVENTO_ROOT . $r["EventoFile"];
                    }

                //$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
                else :
                    $foto1 = "";
                endif;

                if (!empty($r["FotoDestacada"])) :
                    if (strstr(strtolower($r["FotoDestacada"]), "http://")) {
                        $foto2 = $r["FotoDestacada"];
                    } elseif (strstr(strtolower($r["FotoDestacada"]), "https://")) {
                        $foto2 = $r["FotoDestacada"];
                    } else {
                        $foto2 = IMGEVENTO_ROOT . $r["FotoDestacada"];
                    }

                //$foto2 = IMGEVENTO_ROOT.$r["FotoDestacada"];
                else :
                    $foto2 = "";
                endif;

                $evento["Foto"] = $foto1;
                $evento["Foto2"] = $foto2;

                //campos whatsapp
                $evento["Whatsapp"] = $r["Whatsapp"];
                $evento["PermiteBotonWhatsapp"] = $r["PermiteBotonWhatsapp"];
                $evento["LabelBotonWhatsapp"] = $r["LabelBotonWhatsapp"];

                array_push($response, $evento);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if

        else {

            if ($IDClub == 151) {
                $respuesta_serv = "No information found";
            } else {
                $respuesta_serv = SIMUtil::get_traduccion('', '', 'Nohayeventosenlafechaseleccionada', LANG);
            }

            $respuesta["message"] = $respuesta_serv;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;

    } // fin function

    public function get_eventos_empleados($IDClub, $IDSeccion = "", $IDUsuario = "", $Tag = "", $Version = "", $Nombre = "", $Fecha = "")
    {
        $dbo = &SIMDB::get();

        // Secciones Empleado
        if (!empty($IDUsuario)) :
            $sql_seccion_empleado = $dbo->query("Select * From UsuarioSeccionEvento Where IDSocio = '" . $id_socio . "'");
            while ($row_seccion = $dbo->fetchArray($sql_seccion_empleado)) :
                $array_secciones_empleado[] = $row_seccion["IDSeccionEvento"];
            endwhile;

            if (count($array_secciones_empleado) > 0) :
                $IDSecciones = implode(",", $array_secciones_empleado);
                $array_condiciones[] = " IDSeccionEvento in(" . $IDSecciones . ") ";
            endif;
        endif;

        // Seccion Especifica
        if (!empty($IDSeccion)) :
            $array_condiciones[] = " IDSeccionEvento  = '" . $IDSeccion . "'";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Titular  like '%" . $Tag . "%' or Introduccion like '%" . $Tag . "%' or Cuerpo like '%" . $Tag . "%' or Lugar like '%" . $Tag . "%' )";
        endif;


        //Buscador Por Nombre o por rangos de fecha
        if (!empty($Nombre)) {
            $array_condiciones[] = " (Titular  like '%" . $Nombre . "%' OR Introduccion LIKE '%" . $Nombre . "%')";
        } else if (empty($Nombre) && !empty($Fecha)) {
            $array_condiciones[] = " ((FechaEvento  = '" . $Fecha . "') OR (FechaEvento <='" . $Fecha . "' AND FechaFin>='" . $Fecha . "' AND '" . $Fecha . "' <= FechaFinEvento))";
            // $array_condiciones[] = " FechaEvento  = '" . $Fecha . "'";
            // $array_condiciones[] = " (Titular  like '%" . $Nombre . "%' OR Introduccion LIKE '%" . $Nombre . "%')";
        }

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_noticia = " and " . $condiciones;
        endif;

        $orden = " FechaEvento ASC";

        $response = array();
        $sql = "SELECT * FROM Evento" . $Version . " WHERE (DirigidoA = 'E' or DirigidoA = 'T') and Publicar = 'S' and FechaInicio <= NOW() and FechaFin >= NOW() and  IDClub = '" . $IDClub . "'" . $condiciones_noticia . " ORDER BY " . $orden . "";

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $evento["IDClub"] = $r["IDClub"];
                $evento["IDSeccionEvento"] = $r["IDSeccionEvento"];
                $evento["IDEvento"] = $r["IDEvento"];
                $evento["Titular"] = $r["Titular"];
                $evento["Introduccion"] = $r["Introduccion"];

                $cuerpo_evento = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Cuerpo"]);
                $evento["Cuerpo"] = $cuerpo_evento;

                $evento["CuerpoEmail"] = $r["CuerpoEmail"];
                $evento["Fecha"] = $r["FechaEvento"];
                $evento["FechaFinEvento"] = $r["FechaFinEvento"];
                $evento["Lugar"] = $r["Lugar"];
                $evento["Valor"] = $r["Valor"];
                $evento["EmailContacto"] = $r["EmailContacto"];
                $evento["InscripcionApp"] = $r["InscripcionApp"];

                if ($r["InscripcionApp"] == "S") {
                    //Verifico si quedan cupos
                    $sql_registrados = "SELECT count(*) as Total FROM  EventoRegistro" . $Version . " Where IDEvento" . $Version . " = '" . $r["IDEvento"] . "'";
                    $r_registrados = $dbo->query($sql_registrados);
                    $row_registrados = $dbo->FetchArray($r_registrados);
                    if ((int) $row_registrados["Total"] >= (int) $r["MaximoParticipantes"]) {
                        $evento["InscripcionApp"] = "N";
                        $evento["Cuerpo"] .= "<br><strong>" . SIMUtil::get_traduccion('', '', 'Sellegoalmaximodecupos.', LANG) . "</strong>";
                    }

                    //verifico  la fecha/hora límite de inscripcion
                    $fechahora_actual = date("Y-m-d H:i:s");
                    $FechaHoraLimite = $r["FechaLimiteInscripcion"] . " " . $r["HoraLimiteInscripcion"];
                    if ($FechaHoraLimite != "0000-00-00" && strtotime($fechahora_actual) > strtotime($FechaHoraLimite)) {
                        $evento["InscripcionApp"] = "N";
                        $evento["Cuerpo"] .= "<br><strong>" . SIMUtil::get_traduccion('', '', 'Inscripcionescerradas.', LANG) . "</strong>";

                    }
                }

                $evento["MensajePagoInscripcion"] = $r["MensajePagoInscripcion"];
                //Tipos de pagos recibidos
                $response_tipo_pago = array();
                $sql_tipo_pago = "SELECT * FROM EventoTipoPago" . $Version . " ETP, TipoPago TP  WHERE ETP.IDTipoPago = TP.IDTipoPago and IDEvento" . $Version . " = '" . $r["IDEvento" . $Version] . "' ";
                $qry_tipo_pago = $dbo->query($sql_tipo_pago);
                if ($dbo->rows($qry_tipo_pago) > 0) {
                    $evento["PagoReserva"] = "S";
                    while ($r_tipo_pago = $dbo->fetchArray($qry_tipo_pago)) {
                        $tipopago["IDClub"] = $IDClub;
                        $tipopago["IDServicio"] = $r_tipo_pago["IDServicio"];
                        $tipopago["IDTipoPago"] = $r_tipo_pago["IDTipoPago"];
                        $tipopago["PasarelaPago"] = $r_tipo_pago["PasarelaPago"];
                        $tipopago["Action"] = SIMUtil::obtener_accion_pasarela($r_tipo_pago["IDTipoPago"], $IDClub);

                        if ($IDClub == 93 && $r_tipo_pago["IDTipoPago"] == 3)
                            $tipopago["Nombre"] = 'Pago en recepción.';
                        else
                            $tipopago["Nombre"] = $r_tipo_pago["Nombre"];

                        if ($IDClub == 15 && $r_tipo_pago["IDTipoPago"] == 15) :
                            $tipopago["Nombre"] = "Pago en efectivo";
                        endif;

                        $tipopago["PaGoCredibanco"] = $r_tipo_pago["PaGoCredibanco"];

                        $imagen = "";
                        //Para el condado y es pagos online muestro la imagen de placetopay
                        switch ($r_tipo_pago["IDTipoPago"]) {
                            case "1":
                                $imagen = "https://static.placetopay.com/placetopay-logo.svg";
                                break;
                            case "2":
                                $imagen = "https://www.miclubapp.com/file/noticia/icono-bono.png";
                                break;
                            case "3":
                                $imagen = "https://www.miclubapp.com/file/noticia/abonoc.png";
                                break;
                            case "11":
                                $imagen = "https://www.miclubapp.com/file/noticia/260838_Sin_t__tulo.png";
                                break;
                            case "17":
                                $imagen = "https://www.miclubapp.com/file/tipopago/Tarjeta.png";
                                break;
                            case "15":
                                $imagen = "https://www.miclubapp.com/file/tipopago/Efectivo.png";
                                break;
                        }

                        $tipopago["Imagen"] = $imagen;
                        array_push($response_tipo_pago, $tipopago);
                    } //end while
                    $evento["TipoPago"] = $response_tipo_pago;
                } else {
                    $evento["PagoReserva"] = "N";
                }

                //Campos Formulario
                $response_campo_formulario = array();
                $sql_campo_form = "SELECT * FROM CampoFormularioEvento" . $Version . " WHERE IDEvento" . $Version . " = '" . $r["IDEvento" . $Version] . "' and Publicar = 'S' order by Orden ";
                $qry_campo_form = $dbo->query($sql_campo_form);
                if ($dbo->rows($qry_campo_form) > 0) {
                    while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                        $campoformulario["IDCampoFormularioEvento"] = $r_campo["IDCampoFormularioEvento" . $Version];
                        $campoformulario["TipoCampo"] = $r_campo["TipoCampo"];
                        $campoformulario["EtiquetaCampo"] = $r_campo["EtiquetaCampo"];
                        $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                        $campoformulario["Valores"] = $r_campo["Valores"];
                        array_push($response_campo_formulario, $campoformulario);
                    } //end while
                    $evento["CampoFormulario"] = $response_campo_formulario;
                } else {
                    $evento["CampoFormulario"] = $response_campo_formulario;
                }

                if (!empty($r["EventoFile"])) :
                    if (strstr(strtolower($r["EventoFile"]), "http://")) {
                        $foto1 = $r["EventoFile"];
                    }
                    if (strstr(strtolower($r["EventoFile"]), "https://")) {
                        $foto1 = $r["EventoFile"];
                    } else {
                        $foto1 = IMGEVENTO_ROOT . $r["EventoFile"];
                    }

                //$foto1 = IMGEVENTO_ROOT.$r["EventoFile"];
                else :
                    $foto1 = "";
                endif;

                if (!empty($r["FotoDestacada"])) :
                    if (strstr(strtolower($r["FotoDestacada"]), "http://")) {
                        $foto2 = $r["FotoDestacada"];
                    } else {
                        $foto2 = IMGEVENTO_ROOT . $r["FotoDestacada"];
                    }

                //$foto2 = IMGEVENTO_ROOT.$r["FotoDestacada"];
                else :
                    $foto2 = "";
                endif;

                $evento["Foto"] = $foto1;
                $evento["Foto2"] = $foto2;

                //campos whatsapp
                $evento["Whatsapp"] = $r["Whatsapp"];
                $evento["PermiteBotonWhatsapp"] = $r["PermiteBotonWhatsapp"];
                $evento["LabelBotonWhatsapp"] = $r["LabelBotonWhatsapp"];

                array_push($response, $evento);
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

    // NUEVAS

    public function get_configuracion_evento($IDClub, $IDSocio, $Version)
    {

        $dbo = &SIMDB::get();
        $response = array();
        if ($Version == "") {
            $VersionEvento = "1";
        } else
            $VersionEvento = "2";

        $sql = "SELECT PermiteMostrarCalendarioColores,PermiteBuscadorNombre,ColorCalendarioSeleccionado,TipoFechaHeader,PermiteMostrarLugarFormularioEvento,
         PermiteMostrarNombreFormularioEvento,PermiteBotonContactoEventos,LabelBotonEventos,LabelInscribirBotonEventos,PermiteRangoFechasEventos FROM ConfiguracionEvento WHERE VersionEvento='" . $VersionEvento . "' AND IDClub='" . $IDClub . "'";

        $qry = $dbo->query($sql);
        $DatosConfiguracionEvento = $dbo->fetchArray($qry);
        $configuracion["PermiteMostrarCalendarioColores"] = $DatosConfiguracionEvento["PermiteMostrarCalendarioColores"];
        $configuracion["PermiteBuscadorNombre"] = $DatosConfiguracionEvento["PermiteBuscadorNombre"];
        $configuracion["ColorCalendarioSeleccionado"] = $DatosConfiguracionEvento["ColorCalendarioSeleccionado"];
        $configuracion["TipoFechaHeader"] = $DatosConfiguracionEvento["TipoFechaHeader"];
        $configuracion["PermiteMostrarLugarFormularioEvento"] = $DatosConfiguracionEvento["PermiteMostrarLugarFormularioEvento"];
        $configuracion["PermiteMostrarNombreFormularioEvento"] = $DatosConfiguracionEvento["PermiteMostrarNombreFormularioEvento"];
        $configuracion["PermiteBotonContactoEventos"] = $DatosConfiguracionEvento["PermiteBotonContactoEventos"];
        $configuracion["LabelBotonEventos"] = $DatosConfiguracionEvento["LabelBotonEventos"];
        $configuracion["LabelInscribirBotonEventos"] = $DatosConfiguracionEvento["LabelInscribirBotonEventos"];
        $configuracion["PermiteRangoFechasEventos"] = $DatosConfiguracionEvento["PermiteRangoFechasEventos"];
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $configuracion["PermiteMisEventos"] = $datos_club["PermiteMisEventos" . $Version];
        $configuracion["PermiteMisEventos"] = $datos_club["PermiteMisEventos" . $Version];
        $configuracion["TipoFiltroEvento"] = $datos_club["TipoFiltroEvento" . $Version];
        $configuracion["EventoLista"] = $datos_club["EventoLista" . $Version];
        $configuracion["ColorFondoEvento"] = $datos_club["ColorFondoEvento" . $Version];
        $configuracion["BuscadorFechaEvento"] = $datos_club["BuscadorFechaEvento" . $Version];
        $configuracion["TipoCeldaEvento"] = $datos_club["TipoCeldaEvento" . $Version];


        //busco el titulo de la configuracion del app
        $sql_modulo = "Select IDModulo,Titulo,TituloLateral From ClubModulo Where IDClub = '" . $IDClub . "' and IDModulo in (4,76)";
        $r_modulo = $dbo->query($sql_modulo);

        while ($row_modulo = $dbo->fetchArray($r_modulo)) {
            $array_modulo[$row_modulo["IDModulo"]] = $row_modulo["TituloLateral"];
        }
        if ($VersionEvento == "1") {
            if (empty($array_modulo["4"])) {
                $configuracion["MisEventos"] = "Mis Eventos";
            } else {
                $configuracion["MisEventos"] = $array_modulo["4"];
            }
        }

        if ($VersionEvento == "2") {
            if (empty($array_modulo["76"])) {
                $configuracion["MisEventos"] = "Mis Eventos";
            } else {
                $configuracion["MisEventos"] = $array_modulo["76"];
            }
        }

        array_push($response, $configuracion);

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $configuracion;

        return $respuesta;
    } // fin function

    public function valida_pago_evento($IDClub, $IDSocio, $IDEventoRegistro, $Version = "")
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM EventoRegistro" . $Version . " WHERE IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                //para place to pay verifico que no se encuentre en estado pendiente
                $verifica_estado_ptp = SIMWebServiceApp::verifica_place_to_pay($IDClub, $IDSocio, $IDEventoRegistro);
                if ($verifica_estado_ptp == 1) {
                    $respuesta["message"] = "La transaccion se encuentra pendiente de aprobacion de la entidad financiera";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                if ($r["IDTipoPago"] == 1 || $r["IDTipoPago"] == 4) : // payU
                    if ($r["EstadoTransaccion"] == "") :
                        $respuesta["message"] = "No se ha obtenido respuesta de la transaccion de pagos online ";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                    elseif ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4") :
                        $respuesta["message"] = "Reserva pagada correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    else :
                        $respuesta["message"] = "El pago no fue realizado";
                        $respuesta["success"] = false;
                        $respuesta["response"] = $response;
                    endif;
                elseif ($r["IDTipoPago"] == 15) :
                    if ($r["EstadoTransaccion"] == "A" || $r["EstadoTransaccion"] == "4") :
                        $respuesta["message"] = "Evento pagado correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    endif;
                else :
                    $respuesta["message"] = "La reserva no fue pagada por pagos online ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                endif;
            }

            if ($IDClub == 15) :
                // BUSCAMOS EL REGISTRO EN LA TABLA DE PAGOS
                $SQL = "SELECT * FROM PagoEcollect WHERE IDClub = $IDClub AND Factura = '$IDEventoRegistro-$Version'";
                $qryPereira = $dbo->query($SQL);

                if ($dbo->rows($qryPereira) > 0) :
                    $respuesta["message"] = "Evento Campestre Pereira";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                endif;
            endif;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron registros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function set_formulario_evento($IDClub, $IDEvento, $IDSocio, $IDSocioBeneficiario, $ValoresFormulario = "", $OtrosDatosFormulario = "", $Version = "", $UsuarioCreacion = "", $IDNoSocios = 0, $TipoApp = "")
    {
        $dbo = &SIMDB::get();
        $FechaHoraSistemaActual = date("Y-m-d H:i:s");


        if (!empty($IDClub) && !empty($IDEvento) && (!empty($IDSocio) || $IDNoSocios > 0)) {



         //VALIDACION PARA COTOPAXI  
                if ($IDClub == 249) {
                //datos del evento actual
           $datos_evento = $dbo->fetchAll("Evento". $Version , " IDEvento$Version = '" . $IDEvento . "' ", "array");
            //dias y rango de fecha del evento actual
           $dias1= $datos_evento["Dias"];
           $fecha_inicio= $datos_evento["FechaInicio"];
           $fecha_fin= $datos_evento["FechaFin"];
           $horainicio1 = $datos_evento["Hora"];
           $horafin1 =$datos_evento["HoraFin"];
            
           //Buscamos los eventos a los que esta inscrito por ahora y que esten activos aun
           	$sql_eventos_isncripcion = "SELECT GROUP_CONCAT(IDEvento$Version SEPARATOR ',') AS IDEventos FROM EventoRegistro$Version WHERE IDSocio = $IDSocio AND IDEvento$Version in (SELECT IDEvento$Version FROM Evento$Version WHERE FechaFin >= NOW())"; 
            $qry_eventos_inscripcion = $dbo->query($sql_eventos_isncripcion); 
            $id_eventos= $dbo->fetchArray($qry_eventos_inscripcion);
            $id_eventos_inscriptos= $id_eventos["IDEventos"];
           
 
         $sql_selecion_eventos = $dbo->query("Select * From Evento$Version Where IDEvento$Version in ( $id_eventos_inscriptos )");
             
            while ($row_seleccion = $dbo->fetchArray($sql_selecion_eventos)) :
            
                 $dias2=$row_seleccion["Dias"];
                  
$horainicio2 = $row_seleccion["Hora"];
$horafin2 =$row_seleccion["HoraFin"];
                 
// Convertir las cadenas de texto en arreglos de números enteros
$dias1_array = array_map('intval', explode('|', trim($dias1, '|')));
$dias2_array = array_map('intval', explode('|', trim($dias2, '|')));

// Encontrar la intersección entre los dos arreglos
$interseccion = array_intersect($dias1_array, $dias2_array);
   
// Verificar si el arreglo resultante tiene al menos un elemento
if (!empty($interseccion)) {
   
                    
// Convertir las horas a timestamp
$inicio1 = strtotime($horainicio1);
$fin1 = strtotime($horafin1);
$inicio2 = strtotime($horainicio2);
$fin2 = strtotime($horafin2);

// Comprobar si hay cruce
if (($inicio1 >= $inicio2 && $inicio1 <= $fin2) || ($fin1 >= $inicio2 && $fin1 <= $fin2) || ($inicio1 < $inicio2 && $fin1 > $fin2)) { 

                    $respuesta["message"] = "Lo sentimos, ya tiene una inscripcion para el mismo Dia / Hora.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    }
}  
            endwhile;
            
             
 
                }
                



            if ($TipoApp == "Empleado") {

                $IDPerson = trim($IDSocio);
                $txtID = "IDUsuario";
                $txtTable = "Usuario";
            } else if ($TipoApp == "Socio") {
                $IDPerson = trim($IDSocio);
                $txtID = "IDSocio";
                $txtTable = "Socio";
            } else if ($TipoApp == "") {
                $IDPerson = trim($IDSocio);
                $txtID = "IDSocio";
                $txtTable = "Socio";
            } else if ($IDNoSocios > 0) {
                $IDPerson = $IDNoSocios;
                $txtID = "IDNoSocios";
                $txtTable = "NoSocios";
            }
            /* 
            echo "IDPerson:" . $IDPerson;
            echo "txtID:" .  $txtID;
            echo "txtTable:" . $txtTable; */



            $id_persona = $dbo->getFields($txtTable, $txtID, "$txtID = '" . $IDPerson . "' and IDClub = '" . $IDClub . "'");

            /*   echo "Persona:" . $id_persona; */
            // if ($id_persona !== false) {
            if (!empty($id_persona)) {




                //verifico que ya no este inscrito en este eventos
                $id_registro = (int) $dbo->getFields("EventoRegistro" . $Version, "IDEventoRegistro" . $Version, "$txtID = '" . $IDPerson . "' and IDSocioBeneficiario = '" . $IDSocioBeneficiario . "' and IDClub = '" . $IDClub . "' and IDEvento" . $Version . " = '" . $IDEvento . "' ");

                $permite_repetir = $dbo->getFields("Evento" . $Version, "PermiteRepetir", "IDEvento" . $Version . " = '" . $IDEvento . "' and IDClub = '" . $IDClub . "' ");
                $permiteEvento = $IDNoSocios > 0 ? 'S' : $dbo->getFields("Socio", "PermiteReservar", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if ($id_registro > 0 && $permite_repetir == "N") {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noesposibleregistrarseenesteeventoyaquetieneunainscripcionactiva', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                if ($IDClub == 135 && $permiteEvento == 'N') {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Notienespermisoparainscribirteenelevento', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_datos_evento = $dbo->query("Insert Into EventoRegistro$Version (IDEvento" . $Version . ", IDClub, $txtID, IDSocioBeneficiario, UsuarioTrCr, FechaTrCr) Values ('" . $IDEvento . "','" . $IDClub . "','" . $IDPerson . "', '" . $IDSocioBeneficiario . "','WebService $UsuarioCreacion','" . $FechaHoraSistemaActual . "')");
                /*    $sql_datos_evento = "Insert Into EventoRegistro$Version (IDEvento" . $Version . ", IDClub, $txtID, IDSocioBeneficiario, UsuarioTrCr, FechaTrCr) Values ('" . $IDEvento . "','" . $IDClub . "','" . $IDPerson . "', '" . $IDSocioBeneficiario . "','WebService $UsuarioCreacion','" . $FechaHoraSistemaActual . "')";
                echo $sql_datos_evento; */

                $id_evento_registro = $dbo->lastID();

                //Guardo los datos de los campos
                $ValoresFormulario = trim(preg_replace('/\s+/', ' ', $ValoresFormulario));
                $datos_formulario = json_decode($ValoresFormulario, true);
                if (!empty($IDSocioBeneficiario)) {
                    $OtrosDatosFormulario .= "<br><b>" . SIMUtil::get_traduccion('', '', 'Beneficiario', LANG) . ":</b> " . $dbo->getFields('Socio', 'Nombre', 'IDSocio = "' . $IDSocioBeneficiario . '"') . ' ' . $dbo->getFields('Socio', 'Apellido', 'IDSocio = "' . $IDSocioBeneficiario . '"');
                }
                if (count($datos_formulario) > 0) :
                    foreach ($datos_formulario as $detalle_datos) :
                        $IDSocioInvitado = $detalle_datos[$txtID];

                        $sql_datos_form = $dbo->query("Insert Into EventoRegistroDatos" . $Version . " (IDEventoRegistro" . $Version . ", IDCampoFormularioEvento" . $Version . ", Valor) Values ('" . $id_evento_registro . "','" . $detalle_datos["IDCampoFormularioEvento"] . "','" . $detalle_datos["Valor"] . "')");

                        $campo_form = $dbo->getFields("CampoFormularioEvento" . $Version, "EtiquetaCampo", "IDCampoFormularioEvento" . $Version . " = '" . $detalle_datos["IDCampoFormularioEvento"] . "'  ");
                        $OtrosDatosFormulario .= "<br><b>" . $campo_form . ":</b> " . $detalle_datos["Valor"];
                    endforeach;
                endif;
                SIMUtil::notificar_nueva_inscripcion_evento($IDEvento, $IDSocio, $OtrosDatosFormulario, $Version, $IDNoSocios);

                $parametros_codigo_qr = $IDEvento . "|" . $IDPerson;
                $ruta_qr_evento = SIMUtil::generar_qr_evento($IDPerson, $parametros_codigo_qr);
                $actualiza_qr = "UPDATE EventoRegistro" . $Version . " SET Qr = '" . $ruta_qr_evento . "' WHERE IDEventoRegistro" . $Version . " = '" . $id_evento_registro . "'";
                $dbo->query($actualiza_qr);

                if ($IDNoSocios > 0) {
                    $updateQrNs = "UPDATE NoSocios SET CodigoQr = '" . $ruta_qr_evento . "' WHERE IDNoSocios = $IDNoSocios";
                    $dbo->query($updateQrNs);
                }

                //Datos reserva
                $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
                $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");
                $response_reserva = array();
                $datos_reserva["IDEventoRegistro"] = (int) $id_evento_registro;
                //Calculo el valor de la reserva
                $valor_inicial_reserva = (float) $dbo->getFields("Evento" . $Version, "ValorInscripcion", "IDEvento" . $Version . " = '" . $IDEvento . "'");
                $datos_reserva["ValorReserva"] = $valor_inicial_reserva;
                $ValorReserva = $datos_reserva["ValorReserva"];
                $datos_reserva["ValorPagoTexto"] = $datos_club_otros["SignoPago"] . " " . (float)$ValorReserva . " " . $datos_club_otros["TextoPago"];
                $llave_encripcion = $datos_club["ApiKey"]; //llave de encripciÛn que se usa para generar la fima
                $ApiLogin = $datos_club["ApiLogin"]; //Api Login


                if ($datos_club["MerchantId"] != "placetopay") {
                    $usuarioId = $datos_club["MerchantId"];
                }
                //c0digo inicio del cliente
                else {
                    $usuarioId = $datos_club["ApiLogin"];
                }
                //c0digo inicio del cliente

                $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
                $iva = 0; //impuestos calculados de la transacciÛn
                $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
                $valor = $datos_reserva["ValorReserva"] + (($datos_reserva["ValorReserva"] * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total
                $moneda = "COP"; //la moneda con la que se realiza la compra
                $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba

                // ACTULIZAMOS EL VALOR
                $update = "UPDATE EventoRegistro$Version SET Valor = '$valor' WHERE IDEventoRegistro$Version = $id_evento_registro";
                $dbo->query($update);

                if ($IDClub == 28) {
                    $CampoCategoria = "SELECT IDCampoFormularioEvento FROM CampoFormularioEvento WHERE EtiquetaCampo = 'Categoría que participa el jugador' AND IDEvento = " . $IDEvento;
                    $qryCampo = $dbo->query($CampoCategoria);
                    $dato = $dbo->fetchArray($qryCampo);

                    $valorCampo = "SELECT Valor FROM EventoRegistroDatos WHERE IDCampoFormularioEvento = " . $dato["IDCampoFormularioEvento"] . " AND IDEventoRegistro = " . $id_evento_registro;
                    $qryValor = $dbo->query($valorCampo);
                    $Valor = $dbo->fetchArray($qryValor);

                    $socio = "SELECT Nombre, Apellido FROM Socio WHERE IDSocio = " . $IDSocio;
                    $qry = $dbo->query($socio);
                    $datoS = $dbo->fetchArray($qry);

                    $descripcion = "Pago Evento " . $datoS["Nombre"] . " " . $datoS["Apellido"] . " Categoria: " . $Valor["Valor"];
                } else {
                    $descripcion = "Pago Evento Mi Club."; //descripciÛn de la transacciÛn
                }

                $url_respuesta = URLROOT . "respuesta_transaccion_evento.php?Version=" . $Version; //Esta es la p·gina a la que se direccionar· al final del pago
                $url_confirmacion = URLROOT . "confirmacion_pagos_evento.php?Version=" . $Version;
                $emailSocio = $dbo->getFields($txtTable, "CorreoElectronico", "$txtID =" . $IDPerson); //email al que llega confirmaciÛn del estado final de la transacciÛn, forma de identificar al comprador
                if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                    $emailComprador = $emailSocio;
                } else {
                    $emailComprador = "";
                }

                $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
                $extra1 = $id_evento_registro;

                $datos_reserva["Action"] = $datos_club["URL_PAYU"];

                $response_parametros = array();
                $datos_post["llave"] = "moneda";
                $datos_post["valor"] = (string) $moneda;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "ref";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "llave";
                $datos_post["valor"] = $llave_encripcion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "Modulo";
                $datos_post["valor"] = "Evento";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "userid";
                $datos_post["valor"] = $usuarioId;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "usuarioId";
                $datos_post["valor"] = $usuarioId;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "accountId";
                $datos_post["valor"] = (string) $datos_club["AccountId"];
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "descripcion";
                $datos_post["valor"] = $descripcion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra1";
                $datos_post["valor"] = (string) $extra1;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "extra2";
                $datos_post["valor"] = $IDClub;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "refVenta";
                $datos_post["valor"] = $refVenta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "valor";
                $datos_post["valor"] = (string)$ValorReserva;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "iva";
                $datos_post["valor"] = "0";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "baseDevolucionIva";
                $datos_post["valor"] = "0";
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "firma";
                $datos_post["valor"] = $firma;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "emailComprador";
                $datos_post["valor"] = $emailComprador;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "prueba";
                $datos_post["valor"] = (string) $datos_club["IsTest"];
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_respuesta";
                $datos_post["valor"] = (string) $url_respuesta;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "url_confirmacion";
                $datos_post["valor"] = (string) $url_confirmacion;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = $txtID;
                $datos_post["valor"] = $IDPerson;
                array_push($response_parametros, $datos_post);

                $datos_post["llave"] = "version";
                $datos_post["valor"] = (string) $Version;
                array_push($response_parametros, $datos_post);

                $datos_reserva["ParametrosPost"] = $response_parametros;

                //PAGO
                $datos_post_pago = array();
                $datos_post_pago["iva"] = 0;
                $datos_post_pago["purchaseCode"] = $refVenta;
                $datos_post_pago["totalAmount"] = $ValorReserva * 100;
                $datos_post_pago["ipAddress"] = SIMUtil::get_IP();
                $datos_reserva["ParametrosPaGo"] = $datos_post_pago;
                //FIN PAGO

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = $datos_reserva;

                //envia corre al usuario externo indicando la inscripcion al evento
                if ($IDNoSocios) {

                    $persona = $dbo->getFields("NoSocios", array("Nombre", "CodigoQr", "CorreoElectronico"), "IDNoSocios = $IDNoSocios");
                    $titleEvento = $dbo->getFields("Evento", "Titular", "IDEvento = $IDEvento");

                    $msg = "<br>Señor(a): " . $persona['Nombre'] . " <br><br>
                                La inscripci&oacute;n al evento $titleEvento se realiz&oacute; exitosamente.<br>
                                A continuaci&oacute;n encontrar&aacute; su c&oacute;digo QR<br><br>
                                <img src='" . SOCIO_ROOT . "qr/" . $persona['CodigoQr'] . "'>
                                <br>";
                    // $msg = "<br>Señor(a): " . $persona['Nombre'] . " <br><br>
                    //             La inscripcion al evento: $titleEvento se realizo exitosamente.<br>
                    //             A continuacion encontrara su codigo Qr<br><br>
                    //             <img src='" . SOCIO_ROOT . "qr/" . $persona['CodigoQr'] . "'>
                    //             <br>
                    //             Por favor no responda este correo<br><br>
                    //             <b>Mi Club App</b>";

                    // $mensaje = "
                    //             <body>
                    //                 <table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
                    //                     <tr>
                    //                         <td>
                    //                             <img src='" . CLUB_ROOT . $datos_club['FotoLogoApp'] . "'>
                    //                         </td>
                    //                     </tr>
                    //                     <tr>
                    //                         <td> $msg </td>
                    //                     </tr>
                    //                 </table>
                    //             </body>
                    //                     ";

                    $asunto = "Inscripcion al evento $titleEvento";

                    $respuesta["message"] =  "";
                    $respuesta["success"] = false;
                    $respuesta["response"] = $datos_reserva;

                    SIMUtil::envia_correo_general($IDClub, $persona['CorreoElectronico'], $msg, $asunto);
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noidentificado,porfavorcierresesionyvuelvaaingresar', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "E1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_evento_socio($IDClub, $IDSocio, $Limite = 0, $IDEvento = "", $Version = "", $TipoApp = "")
    {
        $dbo = &SIMDB::get();

        if ($TipoApp == "Empleado") {

            $IDSocio = trim($IDSocio);
            $Campo = "IDUsuario";
            $Tabla = "Usuario";
        } else if ($TipoApp == "Socio") {
            $IDSocio = trim($IDSocio);
            $Campo = "IDSocio";
            $Tabla = "Socio";
        } else if ($TipoApp == "") {
            $IDSocio = trim($IDSocio);
            $Campo = "IDSocio";
            $Tabla = "Socio";
        }



        $response = array();

        if (!empty($IDEvento)) :
            $condicion_evento = " and ER.IDEvento" . $Version . " = '" . $IDEvento . "'";
        endif;

        /*    $sql = "SELECT *
    				   FROM EventoRegistro" . $Version . " ER, Evento" . $Version . " E
    				   WHERE ER.IDEvento" . $Version . " = E.IDEvento" . $Version . "
    				   and IDSocio = '" . $IDSocio . "'
    				   and FechaInicio <= '" . date("Y-m-d") . "'
    				   " . $condicion_evento . " ORDER BY ER.IDEvento" . $Version . " Desc  "; */

        $sql = "SELECT *
    				   FROM EventoRegistro" . $Version . " ER, Evento" . $Version . " E
    				   WHERE ER.IDEvento" . $Version . " = E.IDEvento" . $Version . "
    				   and " .  $Campo . "= '" . $IDSocio . "'
    				   and FechaInicio <= '" . date("Y-m-d") . "'
    				   " . $condicion_evento . " ORDER BY ER.IDEvento" . $Version . " Desc  ";




        $qry = $dbo->query($sql);

        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($row_reserva = $dbo->fetchArray($qry)) :
                $reserva["IDClub"] = $IDClub;
                $reserva["IDSocio"] = $IDSocio;
                $reserva["IDSocioBeneficiario"] = $IDSocioBeneficiario;
                $reserva["IDEvento"] = $row_reserva["IDEventoRegistro" . $Version];
                $reserva["IDEventoRegistro"] = $row_reserva["IDEventoRegistro" . $Version];
                $reserva["Evento"] = $dbo->getFields("Evento" . $Version, "Titular", "IDEvento" . $Version . " = '" . $row_reserva["IDEvento" . $Version] . "'");
                $reserva["Cuerpo"] = $dbo->getFields("Evento" . $Version, "Cuerpo", "IDEvento" . $Version . " = '" . $row_reserva["IDEvento" . $Version] . "'");

                if (empty($row_reserva["Qr"])) {
                    $reserva["Q"] = "";
                } else {
                    $reserva["QR"] = SOCIO_ROOT . "qr/" . $row_reserva["Qr"];
                }

                $rutafoto = $dbo->getFields("Evento" . $Version, "EventoFile", "IDEvento" . $Version . " = '" . $row_reserva["IDEvento" . $Version] . "'");

                if (!empty($rutafoto)) :
                    $foto1 = IMGEVENTO_ROOT . $rutafoto;
                else :
                    $foto1 = "";
                endif;
                $reserva["Foto"] = $foto1;



                $reserva["Fecha"] = $row_reserva["FechaEvento"];



                $reserva["FechaFinEvento"] = $row_reserva["FechaFinEvento"];

                $reserva["PagadaOnline"] = $row_reserva["Pagado"];
                if (!empty($row_reserva["FechaTransaccion"])) {
                    $reserva["FechaTransaccion"] = $row_reserva["FechaTransaccion"];
                } else {
                    $reserva["FechaTransaccion"] = "No aplica";
                }

                $Mensaje_transaccion = "";
                if (!empty($row_reserva["MensajeTransaccion"])) {
                    $reserva["MensajeTransaccion"] = "Mensaje transaccion: " . $Mensaje_transaccion;
                }

                //Datos Reserva
                $response_datos = array();
                $sql_datos_reserva = $dbo->query("Select * From EventoRegistroDatos" . $Version . " Where IDEventoRegistro" . $Version . " = '" . $row_reserva["IDEventoRegistro" . $Version] . "'");

                $dato_reserva["Campo"] = 'Titular';
                $dato_reserva["Valor"] = $dbo->getFields('Socio', 'Nombre', 'IDSocio = "' . $row_reserva['IDSocio'] . '"') . ' ' . $dbo->getFields('Socio', 'Apellido', 'IDSocio = "' . $row_reserva['IDSocio'] . '"');
                array_push($response_datos, $dato_reserva);

                if (!empty($row_reserva['IDSocioBeneficiario'])) {
                    $dato_reserva["Campo"] = 'Beneficiario';
                    $dato_reserva["Valor"] = $dbo->getFields('Socio', 'Nombre', 'IDSocio = "' . $row_reserva['IDSocioBeneficiario'] . '"') . ' ' . $dbo->getFields('Socio', 'Apellido', 'IDSocio = "' . $row_reserva['IDSocioBeneficiario'] . '"');
                    array_push($response_datos, $dato_reserva);
                }
                while ($r_datos_reserva = $dbo->fetchArray($sql_datos_reserva)) :
                    $dato_reserva["Campo"] = $dbo->getFields("CampoFormularioEvento" . $Version, "EtiquetaCampo", "IDCampoFormularioEvento" . $Version . " = '" . $r_datos_reserva["IDCampoFormularioEvento" . $Version] . "'");
                    $dato_reserva["Valor"] = $r_datos_reserva["Valor"];
                    array_push($response_datos, $dato_reserva);
                endwhile;

                $reserva["Datos"] = $response_datos;

                array_push($response, $reserva);

            endwhile;

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            /*
                    $reserva["IDClub"] = "";
                    $reserva["IDSocio"] = "";
                    $reserva["IDEvento"] = "";
                    array_push($response, $reserva);

                    $respuesta["message"] = "No tienes eventos programados.";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                     */

            $respuesta["message"] = "No tienes eventos programados.";
            $respuesta["success"] = false;
            $respuesta["response"] = $response;
        } //end else

        return $respuesta;
    }

    public function elimina_evento_socio($IDClub, $IDSocio, $IDEventoRegistro, $Version)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDEventoRegistro)) {

            //verifico que exista
            //$id_evento_socio = $dbo->getFields( "EventoRegistro".$Version , "IDEventoRegistro".$Version , "IDClub = '" . $IDClub . "' and IDEventoRegistro".$Version." = '".$IDEventoRegistro."' and IDSocio = '".$IDSocio."'" );
            $datos_registro = $dbo->fetchAll("EventoRegistro" . $Version, " IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' and IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "' ", "array");
            $id_evento_socio = $datos_registro["IDEventoRegistro" . $Version];
            $id_evento_consul = $datos_registro["IDEvento" . $Version];

            if (!empty($id_evento_socio)) {

                if ($datos_registro["Pagado"] != "S") {

                    $permiteelimnarsocio = $dbo->getFields("Evento" . $Version, "PermiteEliminarSocio", "IDEvento" . $Version . " = " . $id_evento_consul);
                    $mensaje = $dbo->getFields("Evento" . $Version, "MensajeNoEliminarSocio", "IDEvento" . $Version . " = " . $id_evento_consul);
                    if (!$permiteelimnarsocio) {
                        if (empty($mensaje))
                            $respuesta["message"] = "No puede elimnarse del evento.";
                        else
                            $respuesta["message"] = $mensaje;

                        $respuesta["success"] = false;
                        $respuesta["response"] = null;

                        return $respuesta;
                    }
                    //Hacer copia del registrado por temas de confirmacion de pago
                    $sql_bck = "INSERT IGNORE INTO EventoRegistroEliminado" . $Version . " (IDEventoRegistro" . $Version . ", IDEvento" . $Version . ", IDClub, IDSocio, IDSocioBeneficiario, IDTipoPago, Valor, CodigoPago, EstadoTransaccion, FechaTransaccion, CodigoRespuesta, MedioPago, TipoMedioPago, Pagado, PagoPayu, UsuarioTrCr, FechaTrCr,UsuarioTrEd, FechaTrEd)
                                        SELECT IDEventoRegistro" . $Version . ", IDEvento" . $Version . ", IDClub, IDSocio, IDSocioBeneficiario, IDTipoPago, Valor, CodigoPago, EstadoTransaccion, FechaTransaccion, CodigoRespuesta, MedioPago, TipoMedioPago, Pagado, PagoPayu, UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd FROM EventoRegistro" . $Version . "
                                        WHERE IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' and IDSocio = '" . $IDSocio . "'";
                    $dbo->query($sql_bck);
                    // Borrar evento socio

                    SIMUtil::notificar_elimina_inscripcion_evento($IDEventoRegistro, $IDSocio, $Version);

                    $sql_elimina_evento_socio = $dbo->query("DELETE FROM EventoRegistro" . $Version . " Where IDClub = '" . $IDClub . "' and IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' and IDSocio = '" . $IDSocio . "'");



                    $respuesta["message"] = "eliminado correctamente";
                    $respuesta["success"] = true;
                    $respuesta["response"] = "eliminado";
                } else {
                    $respuesta["message"] = "No se puede eliminar el registro ya fue pagado";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Atencion la reserva del evento no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_tipo_pago_evento($IDClub, $IDSocio, $IDEventoRegistro, $IDTipoPago, $CodigoPago = "", $Version = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDEventoRegistro) && !empty($IDTipoPago)) {

            //verifico que la reserva exista y pertenezca al club
            $id_reserva = $dbo->getFields("EventoRegistro" . $Version, "IDEventoRegistro" . $Version, "IDEventoRegistro" . $Version . " = '" . $IDEventoRegistro . "' and IDClub = '" . $IDClub . "'");

            if (!empty($id_reserva)) {

                //Si es codigo actualizo para que no se utilice mas y valido si existe el codigo
                if (!empty($CodigoPago)) :

                    $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    $codigo_disponible = $dbo->getFields("ClubCodigoPago", "Disponible", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'");
                    if (empty($id_codigo)) {
                        $respuesta["message"] = "Codigo invalido, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } elseif ($codigo_disponible != "S") {
                        $respuesta["message"] = "El codigo ya fue utilizado, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {

                        $sql_actualiza_codigo = "Update ClubCodigoPago Set Disponible= 'N', IDSocio = '" . $IDSocio . "'  Where   Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "'";
                        $dbo->query($sql_actualiza_codigo);
                    }

                endif;

                $datos_socio = $dbo->fetchAll("Socio", "IDSocio = '" . $IDSocio . "'");

                if ($datos_socio["IDEstadoSocio"] == 5 && $IDTipoPago == 3) {
                    $respuesta["message"] = "Lo sentimos no tiene permiso para realizar el pago de esta forma. Por favor contacte al Club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }

                $sql_tipo_pago = "Update EventoRegistro Set IDTipoPago =  '" . $IDTipoPago . "', CodigoPago = '" . $CodigoPago . "' Where IDEventoRegistro = '" . $IDEventoRegistro . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_tipo_pago);

                $respuesta["message"] = "Forma de pago registrada con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "Atencion la reserva no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "51. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }
}
