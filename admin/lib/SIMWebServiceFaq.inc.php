<?php
class SIMWebServiceFaq
{

    public function get_configuracion_faq($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT IDClub,MostrarCrearPregunta,ImagenThumbMeGusta,ImagenThumbNoMeGusta,PermiteMostrarFecha,OcultarBotonesLike FROM ConfiguracionFaqs  WHERE IDClub = '" . $IDClub . "'  And Activo='S'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $r["IDClub"];
                $configuracion["MostrarCrearPregunta"] = $r["MostrarCrearPregunta"];
                $configuracion["PermiteFechaPregunta"] = $r["PermiteMostrarFecha"];
                if (empty($r["ImagenThumbMeGusta"])) {
                    $configuracion["ImagenThumbMeGusta"] = "";
                } else {
                    $configuracion["ImagenThumbMeGusta"] = CLUB_ROOT . $r["ImagenThumbMeGusta"];
                }

                $configuracion["ImagenThumbNoMeGusta"] = CLUB_ROOT . $r["ImagenThumbNoMeGusta"];
                $configuracion["OcultarBotonesLike"] = $r["OcultarBotonesLike"];
                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Configuracionnoestáactivo', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_categorias_faq($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT * FROM CategoriaFaq  WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                $seccion["IDClub"] = $r["IDClub"];
                $seccion["IDCategoria"] = $r["IDCategoriaFaq"];
                $seccion["Nombre"] = $r["Nombre"];
                $seccion["Descripcion"] = $r["Descripcion"];

                if (!empty($r["Icono"])) :
                    $foto = CLUB_ROOT . $r["Icono"];
                else :
                    $foto = "";
                endif;

                $seccion["Icono"] = $foto;

                array_push($response, $seccion);
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

    public function get_preguntas_faq($IDClub, $IDSocio, $IDUsuario, $IDCategoria = "", $Tag = "")
    {
        $dbo = &SIMDB::get();

        // Seccion Especifica
        if (!empty($IDCategoria)) :
            $sql_cat = "SELECT IDFaq From FaqCategoria Where IDCategoriaFaq = '" . $IDCategoria . "'";
            $qry_cat = $dbo->query($sql_cat);
            while ($r_cat = $dbo->fetchArray($qry_cat)) {
                $array_cat[] = $r_cat["IDFaq"];
            }
            if (count($array_cat) > 0) {
                $id_faq = implode(",", $array_cat);
            }
            $array_condiciones[] = " IDFaq  in (" . $id_faq . ") ";
        endif;

        // Tag
        if (!empty($Tag)) :
            $array_condiciones[] = " (Pregunta  like '%" . $tag . "%' or Respuesta like '%" . $tag . "%') ";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones_buscar = " and " . $condiciones;
        endif;

        $response = array();
        $sql = "SELECT * FROM Faq  WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' " . $condiciones_buscar . " ORDER BY Orden";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                // verifico que la seccion tenga por lo menos una noticia publicada
                $seccion["IDClub"] = $r["IDClub"];
                $seccion["IDPregunta"] = $r["IDFaq"];
                $seccion["Pregunta"] = $r["Pregunta"];

                $cuerpo_respuesta = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Respuesta"]);
                $seccion["Respuesta"] = $cuerpo_respuesta;

                //$seccion["FechaPregunta"] = substr($r["FechaTrCr"], 0, 10);
                $seccion["FechaPregunta"] = "";
                $seccion["FechaRespuesta"] = substr($r["FechaTrCr"], 0, 10);
                array_push($response, $seccion);
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

    public function set_calificar_faq($IDClub, $IDSocio, $IDUsuario, $IDPregunta, $ResultoUtil)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDPregunta) && !empty($ResultoUtil)) {

            if (!empty($IDSocio)) {
                $condicion_usu = " and IDSocio = '" . $IDSocio . "' ";
            } elseif (!empty($IDUsuario)) {
                $condicion_usu = " and IDUsuario = '" . $IDUsuario . "' ";
            }

            $datos_reg = $dbo->fetchAll("FaqVoto", " IDFaq = '" . $IDPregunta . "' " . $condicion_usu, "array");

            if ($datos_reg["IDFaqVoto"] == 0) {

                if ($ResultoUtil == "S") {
                    $actualiza = " VotosUtil = VotosUtil+1 ";
                } else {
                    $actualiza = " VotosNoUtil = VotosNoUtil+1 ";
                }

                $sql_voto = $dbo->query("INSERT INTO FaqVoto (IDFaq,IDSocio,IDUsuario,Fecha,Util) VALUES('" . $IDPregunta . "','" . $IDUsuario . "','" . $IDUsuario . "',CURDATE(),'" . $ResultoUtil . "')");
                $sql_faq = $dbo->query("UPDATE Faq Set $actualiza Where IDFaq = '" . $IDPregunta . "'");

                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'enviadocorrectamente', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Yasehabíaregistradounacalificación', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "F1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_pregunta_faq($IDClub, $IDSocio, $IDUsuario, $Correo, $Pregunta)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($Correo) && !empty($Pregunta)) {

            $sql_faq = $dbo->query("INSERT INTO FaqSolicitud (IDClub, IDSocio, IDUsuario, Pregunta, CorreoElectronico, Fecha, UsuarioTrCr, FechaTrCr)
										Values ('" . $IDClub . "','" . $IDSocio . "','" . $IDUsuario . "','" . $Pregunta . "','" . $Correo . "',NOW(),'WebService',NOW())");
            $id_faq = $dbo->lastID();
            self::notificar_nuevo_faq($id_faq);

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "11." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function notificar_nuevo_faq($id_faq, $TipoNotif = "")
    {
        $dbo = &SIMDB::get();

        $datos_faq = $dbo->fetchAll("FaqSolicitud", " IDFaqSolicitud = '" . $id_faq . "' ", "array");
        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $datos_faq["IDClub"] . "' ", "array");
        $datos_config = $dbo->fetchAll("ConfiguracionFaqs", " IDClub = '" . $datos_faq["IDClub"] . "' ", "array");
        $correo = $datos_config["EmailNotificacion"];
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_faq["IDSocio"] . "' ", "array");

        if (!empty($correo)) :
            $msg = "<br>" . SIMUtil::get_traduccion('', '', 'CordialSaludo', LANG) . "<br><br>" .
                SIMUtil::get_traduccion('', '', 'Sehageneradounanuevapregunta', LANG) . " " .
                SIMUtil::get_traduccion('', '', 'Recuerdeingresaralsistemaparaconocermasdetalles', LANG) . " .<br><br>" .
                SIMUtil::get_traduccion('', '', 'Persona', LANG) . ":" . utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]) . "<br>" .
                SIMUtil::get_traduccion('', '', 'Correo', LANG) . ":" . utf8_encode($datos_faq["CorreoElectronico"]) . "<br>" .
                SIMUtil::get_traduccion('', '', 'Pregunta', LANG) . ":" . $datos_faq["Pregunta"] . "<br>" .
                SIMUtil::get_traduccion('', '', 'Fecha', LANG) . ":" . $datos_faq[Fecha] . "<br><br>" .

                SIMUtil::get_traduccion('', '', 'Porfavornorespondaestecorreo,sideseadarunarespuestaingresealadministrador', LANG) . "<br>" .
                SIMUtil::get_traduccion('', '', 'Cordialmente', LANG) . "<br><br>
				<b>" . SIMUtil::get_traduccion('', '', 'Notificaciones', LANG)  . utf8_encode($datos_club["Nombre"]) . "</b>";

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

            $url_baja = URLROOT . "contactenos.php";
            $mail = new phpmailer();
            $array_correo = explode(",", $correo);
            if (count($array_correo) > 0) {
                foreach ($array_correo as $correo_value) {
                    if (filter_var(trim($correo_value), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress($correo_value);
                        $enviar_a = $correo_value;
                    }
                }

                if ($TipoNotif == "") {
                    $TipoNotif = "Nuevo Pregunta";
                }

                $mail->Subject = $datos_club["Nombre"];
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
                $mail->FromName =  SIMUtil::get_traduccion('', '', 'CLUB', LANG);
                $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miclubapp.com>,  <$url_baja>");
                $confirm = $mail->Send();
            }

        endif;
    }
} //end class
