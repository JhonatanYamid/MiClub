<?php

    class SIMWebServiceContactos
    {
        public function get_configuracion_registro_contacto($IDClub, $IDSocio, $IDUsuario)
        {
    
            $dbo = &SIMDB::get();
            $response = array();
    
            $sql = "SELECT * FROM ConfiguracionRegistroContacto  WHERE IDClub = '" . $IDClub . "' ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $configuracion["IDClub"] = $r["IDClub"];
                    $configuracion["LabelRegistro"] = $r["LabelRegistro"];
                    $configuracion["LabelBuscador"] = $r["LabelBuscador"];
    
                    //Campos Formulario
                    $response_campo_formulario = array();
                    $sql_campo_form = "SELECT * FROM CampoRegistroContacto WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
                    $qry_campo_form = $dbo->query($sql_campo_form);
                    if ($dbo->rows($qry_campo_form) > 0) {
                        while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                            $campoformulario["IDCampoFormulario"] = $r_campo["IDCampoRegistroContacto"];
                            $campoformulario["Tipo"] = $r_campo["Tipo"];
                            $campoformulario["Nombre"] = $r_campo["Nombre"];
                            $campoformulario["Obligatorio"] = $r_campo["Obligatorio"];
                            $campoformulario["Valores"] = $r_campo["Valores"];
                            array_push($response_campo_formulario, $campoformulario);
                        } //end while
                    }
                    $configuracion["CamposFormulario"] = $response_campo_formulario;
    
                    //Campos Contacto Externo
                    $response_campo_externo = array();
                    $sql_campo_form = "SELECT * FROM CampoContactoExterno WHERE IDClub= '" . $IDClub . "' and Publicar = 'S' order by Orden ";
                    $qry_campo_form = $dbo->query($sql_campo_form);
                    if ($dbo->rows($qry_campo_form) > 0) {
                        while ($r_campo = $dbo->fetchArray($qry_campo_form)) {
                            $campoexterno["IDCampoContacto"] = $r_campo["IDCampoContactoExterno"];
                            $campoexterno["Tipo"] = $r_campo["Tipo"];
                            $campoexterno["Nombre"] = $r_campo["Nombre"];
                            $campoexterno["Obligatorio"] = $r_campo["Obligatorio"];
                            $campoexterno["Valores"] = $r_campo["Valores"];
                            array_push($response_campo_externo, $campoexterno);
                        } //end while
                    }
    
                    $configuracion["CamposContactoExterno"] = $response_campo_externo;
    
                    array_push($response, $configuracion);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $configuracion["IDClub"] = $IDClub;
                $configuracion["LabelRegistro"] = "A continuación agregue a las personas con las que ha tenido un contacto (contacto se considera estar con una persona mas de 15 min)";
                $configuracion["LabelBuscador"] = "A continuación agregue a las personas con las que ha tenido un contacto (contacto se considera estar con una persona mas de 15 min)";
                array_push($response, $configuracion);
    
                $respuesta["message"] = "No hay configuracion en modulo registro contacto";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //end else
    
            return $respuesta;
        } // fin function

        public function set_registro_contacto($IDClub, $IDSocio, $IDUsuario, $FechaHora, $Lugar, $Latitud, $Longitud, $Contactos, $CamposFormulario = "")
        {
    
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($FechaHora) && !empty($Lugar) && !empty($Latitud) && !empty($Longitud)) {
    
                $sql_registro = $dbo->query("INSERT INTO RegistroContacto (IDClub, IDSocio, IDUsuario, Fecha, Lugar, Latitud, Longitud, UsuarioTrCr, FechaTrCr)
                                                                                     VALUES ('" . $IDClub . "','" . $IDSocio . "', '" . $IDUsuario . "','" . $FechaHora . "','" . $Lugar . "','" . $Latitud . "','" . $Longitud . "','App',NOW())");
                $id_registro = $dbo->lastID();
    
                //Inserto los contactos
                $datos_invitado_turno = json_decode($Contactos, true);
                $total_invitados_turno = count($datos_invitado_turno);
    
                $contador_invitado_agregado = 1;
    
                foreach ($datos_invitado_turno as $detalle_datos_turno) :
                    $IDSocioInvitadoTurno = $detalle_datos_turno["IDSocio"];
                    $NombreSocioInvitadoTurno = $detalle_datos_turno["Nombre"];
    
                    // Guardo los invitados socios o externos
                    $datos_invitado_actual = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;
                    if (!in_array($datos_invitado_actual, $array_invitado_agregado)) :
    
                        $sql_inserta_invitado_turno = $dbo->query("INSERT INTO RegistroContactoPersona (IDRegistroContacto, IDSocio, IDUsuario, NombreExterno, UsuarioTrCr, FechaTrCr)
                                                                                                                                          VALUES ('" . $id_registro . "','" . $IDSocioInvitadoTurno . "','" . $IDUsuario . "', '" . $NombreSocioInvitadoTurno . "','App',NOW())");
                        $id_registro_persona = $dbo->lastID();
    
                        //Inserto los otros datos
                        $datos_dinamico_ext = json_decode($detalle_datos_turno["CamposContactoExterno"], true);
                        $total_dinamico_ext = count($datos_dinamico_ext);
                        $contador_dinamico_ext = 1;
                        $datos_dinamico_ext = $detalle_datos_turno["CamposContactoExterno"];
    
                        foreach ($datos_dinamico_ext as $detalle_dinamico_ext) :
                            $IDCampoFormularioExt = $detalle_dinamico_ext["IDCampoContacto"];
                            $ValorExt = $detalle_dinamico_ext["Valor"];
                            $sql_inserta_otro_ext = $dbo->query("INSERT INTO RegistroContactoExternoOtrosDatos (IDRegistroContacto, IDCampoContactoExterno, IDRegistroContactoPersona, Valor)
                                                                                                                                VALUES ('" . $id_registro . "','" . $IDCampoFormularioExt . "','" . $id_registro_persona . "','" . $ValorExt . "')");
                        endforeach;
    
                        $array_invitado_agregado[] = $IDSocioInvitadoTurno . "-" . $NombreSocioInvitadoTurno;
    
                    else :
                        $contador_invitado_agregado = 0;
                    endif;
                    $contador_invitado_agregado++;
                endforeach;
    
                //Inserto los otros datos
                $datos_dinamico = json_decode($CamposFormulario, true);
                $total_dinamico = count($datos_dinamico);
                $contador_dinamico = 1;
                foreach ($datos_dinamico as $detalle_dinamico) :
                    $IDCampoFormulario = $detalle_dinamico["IDCampoFormulario"];
                    $Valor = $detalle_dinamico["Valor"];
                    $sql_inserta_otro = $dbo->query("INSERT INTO RegistroContactoOtrosDatos (IDRegistroContacto, IDCampoRegistroContacto, Valor)
                                                                                                            VALUES ('" . $id_registro . "','" . $IDCampoFormulario . "','" . $Valor . "')");
                endforeach;
    
                $respuesta["message"] = "¡Tu reporte ha sido generado exitosamente!";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "21. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
    
            return $respuesta;
        }
    
        public function get_mis_registros_contactos($IDClub, $IDSocio, $IDUsuario)
        {
    
            $dbo = &SIMDB::get();
    
            //Socio
            if (!empty($IDSocio) || !empty($IDUsuario)) {
    
                if (!empty($IDSocio)) {
                    $condicion = " IDSocio = '" . $IDSocio . "' ";
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                    $info = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                }
    
                if (!empty($IDUsuario)) {
                    $condicion = " IDUsuario = '" . $IDUsuario . "' ";
                    $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
                    $info = $datos_usuario["Nombre"];
                }
    
                $response = array();
                $sql = "SELECT IDRegistroContacto, FechaTrCr FROM RegistroContacto WHERE  " . $condicion . " GROUP by FechaTrCr Order by FechaTrCr DESC Limit 15";
                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0) {
                    $message = $dbo->rows($qry) . " Encontrados";
                    while ($r = $dbo->fetchArray($qry)) {
                        $DetalleResp = "";
                        $objeto["IDContacto"] = $r["IDRegistroContacto"];
                        $objeto["Fecha"] = substr($r["FechaTrCr"], 0, 10);
                        $objeto["Hora"] = substr($r["FechaTrCr"], 10);
                        $objeto["TextoContactos"] = $info;
                        $objeto["Texto"] = $info;
    
                        //Consulta otros datos
                        $sql_detalle = "SELECT CRC.Nombre,CRC.IDCampoRegistroContacto,Valor
                                                            FROM RegistroContactoOtrosDatos RCOD, CampoRegistroContacto CRC
                                                            WHERE RCOD.IDCampoRegistroContacto=CRC.IDCampoRegistroContacto and RCOD.IDRegistroContacto = '" . $r["IDRegistroContacto"] . "' ";
                        $qry_detalle = $dbo->query($sql_detalle);
                        while ($r_detalle = $dbo->fetchArray($qry_detalle)) {
                            $DetalleResp .= "<b>" . $r_detalle["Nombre"] . "</b>=" . $r_detalle["Valor"] . "<br>";
                        }
    
                        //Consulta los contactos
                        $sql_detalle = "SELECT IDSocio,NombreExterno
                                                            FROM RegistroContactoPersona RCP
                                                            WHERE IDRegistroContacto = '" . $r["IDRegistroContacto"] . "' ";
                        $qry_detalle = $dbo->query($sql_detalle);
                        while ($r_detalle = $dbo->fetchArray($qry_detalle)) {
                            $DetalleResp .= $r_detalle["NombreExterno"] . "<br>";
                        }
    
                        $objeto["Descripcion"] = $DetalleResp;
                        array_push($response, $objeto);
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
            } else {
                $respuesta["message"] = "DR. Faltan Parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;
        } // fin function

        public function set_contacto($IDClub, $IDSocio, $Telefono, $Ciudad, $Direccion, $Email, $Comentario, $Nombre)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($Email) && !empty($Comentario)) {
    
                $sql_seccion_not = $dbo->query("INSERT INTO Contacto (IDClub, IDSocio, Telefono, Ciudad, Direccion, Email, Comentario, Nombre, UsuarioTrCr, FechaTrCr) Values ('" . $IDClub . "','" . $IDSocio . "', '" . $Telefono . "','" . $Ciudad . "','" . $Direccion . "','" . $Email . "','" . $Comentario . "','" . $Nombre . "',WebService',NOW())");
                SIMUtil::notificar_contactenos($IDClub, $IDSocio, $Telefono, $Ciudad, $Direccion, $Email, $Comentario, $Nombre);
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado',LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
    
            } else {
                $respuesta["message"] = "10." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros',LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
    
            return $respuesta;
    
        }     
    }