<?php

class SIMWebServiceCorrespondencia
{
    public function get_configuracion_correspondencia($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM ConfiguracionCorrespondencia  WHERE IDClub = '" . $IDClub . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $configuracion["IDClub"] = $r["IDClub"];
                $configuracion["IconoPorEntregar"] = CLUB_ROOT . $r["IconoPorEntregar"];
                $configuracion["IconoEntregado"] = CLUB_ROOT . $r["IconoEntregado"];
                $configuracion["IconoEntregar"] = CLUB_ROOT . $r["IconoEntregar"];
                $configuracion["IconoRecibir"] = CLUB_ROOT . $r["IconoRecibir"];
                $configuracion["HabilitaRegistraTodos"] = $r["HabilitaRegistraTodos"];
                $configuracion["LabelBuscador"] = $r["LabelBuscador"];
                $configuracion["LabelBotonFirmaRecepcion"] = $r["LabelBotonFirmaRecepcion"];
                array_push($response, $configuracion);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "Correspondencia no está activo";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_correspondencia($IDClub, $IDSocio, $Tag)
    {
        $dbo = &SIMDB::get();

        $response = array();

        if (!empty($IDSocio)) {
            $condicion = " and IDSocio = '" . $IDSocio . "'";
        }

        if (!empty($Tag)) {
            $condicion = " and ( Nombre = '" . $Tag . "' or Apellido = '" . $Tag . "' or Accion = '" . $Tag . "' or Predio = '" . $Tag . "' or NumeroDocumento='" . $Tag . "')";
        }

        //configuracion del club
        $configuracion_club = $dbo->fetchAll('ConfiguracionCorrespondencia', 'IDClub ="' . $IDClub . '"', 'array');

        $sql = "SELECT * FROM Socio 	WHERE IDClub = '" . $IDClub . "' " . $condicion . " ORDER BY Nombre ASC ";
        // echo $sql;
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($row = $dbo->fetchArray($qry)) {

                $socio["IDClub"] = $IDClub;
                $socio["IDSocio"] = $row["IDSocio"];
                $socio["Predio"] = $row["Predio"];



                $response_cat_correspondencia = array();
                $sql_cat_correspondencia = $dbo->query("SELECT * FROM CategoriaCorrespondencia WHERE IDClub = '" . $IDClub . "' ORDER BY Nombre ASC");
                while ($r_cat_correspondencia = $dbo->fetchArray($sql_cat_correspondencia)) {
                    $dato_cat_correspondencia["IDCategoriaCorrespondencia"] = $r_cat_correspondencia["IDCategoriaCorrespondencia"];
                    $dato_cat_correspondencia["Nombre"] = $r_cat_correspondencia["Nombre"];
                    $dato_cat_correspondencia["ServicioPublico"] = $r_cat_correspondencia["ServicioPublico"];

                    $response_correspondencia = array();

                    $sql_correspondencia = $dbo->query("SELECT C.*, TC.Nombre as NombreCorrespondencia
                                                                                                                    FROM Correspondencia C, TipoCorrespondencia TC, CategoriaCorrespondencia CC
                                                                                                                        WHERE C.IDTipoCorrespondencia=TC.IDTipoCorrespondencia
                                                                                                                        AND CC.IDCategoriaCorrespondencia = TC.IDCategoriaCorrespondencia
                                                                                                                        AND TC.IDCategoriaCorrespondencia= '" . $dato_cat_correspondencia["IDCategoriaCorrespondencia"] . "'
                                                                                                                        AND C.IDSocio = '" . $row["IDSocio"] . "'
                                                                                                                        ORDER BY FIELD (IDCorrespondenciaEstado,'1','2'),IDCorrespondenciaEstado ASC");

                    while ($r_correspondencia = $dbo->fetchArray($sql_correspondencia)) {
                        $dato_correspondencia["IDCorrespondencia"] = $r_correspondencia["IDCorrespondencia"];
                        $dato_correspondencia["IDCorrespondenciaEstado"] = $r_correspondencia["IDCorrespondenciaEstado"];
                        $dato_correspondencia["Estado"] = utf8_decode($dbo->getFields("CorrespondenciaEstado", "Nombre", "IDCorrespondenciaEstado = '" . $r_correspondencia["IDCorrespondenciaEstado"] . "'"));
                        $dato_correspondencia["NombreCorrespondencia"] = $r_correspondencia["NombreCorrespondencia"];
                        $dato_correspondencia["Destinatario"] = $r_correspondencia["Destinatario"];
                        $dato_correspondencia["FechaRecepcion"] = $r_correspondencia["FechaRecepcion"];
                        $dato_correspondencia["FechaEntrega"] = $r_correspondencia["FechaEntrega"];
                        $dato_correspondencia["EntregadoA"] = $r_correspondencia["EntregadoA"] . " Observaciones:" . $r_correspondencia["Observaciones"];

                        if (!empty($r_correspondencia["Archivo"])) {
                            $foto = CORRESPONDENCIA_ROOT . $r_correspondencia["Archivo"];
                        } else {
                            $foto = "";
                        }

                        //firma digital
                        if ($configuracion_club["PermiteFirmarRecibido"] == "S") {
                            $dato_correspondencia["PermiteFirmarRecibido"] = $configuracion_club["PermiteFirmarRecibido"];
                            $dato_correspondencia["ObligatorioFirmarRecibido"] = $configuracion_club["ObligatorioFirmarRecibido"];
                        }

                        $dato_correspondencia["Archivo"] = $foto;
                        $dato_correspondencia["EntregadoPor"] = utf8_decode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $r_correspondencia["IDUsuarioEntrega"] . "'"));
                        array_push($response_correspondencia, $dato_correspondencia);
                    }

                    $dato_cat_correspondencia["Nombre"] = $r_cat_correspondencia["Nombre"];
                    $dato_cat_correspondencia["DatosCorrespondencia"] = $response_correspondencia;
                    array_push($response_cat_correspondencia, $dato_cat_correspondencia);
                }
                $socio["Correspondencia"] = $response_cat_correspondencia;
                array_push($response, $socio);
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            }
        } //End if
        else {
            $respuesta["message"] = "No se han encontraron resultados";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_tipo_correspondencia($IDClub)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $sql = "SELECT TC.*,CC.Nombre as Categoria,CC.ServicioPublico FROM TipoCorrespondencia TC, CategoriaCorrespondencia CC WHERE TC.IDCategoriaCorrespondencia = CC.IDCategoriaCorrespondencia and TC.Publicar = 'S' and TC.IDClub = '" . $IDClub . "' ORDER BY TC.Nombre";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                $tipo_corresp["IDTipoCorrespondencia"] = $r["IDTipoCorrespondencia"];
                $tipo_corresp["Nombre"] = $r["Nombre"] . " - " . $r["Categoria"];
                $tipo_corresp["EsServicioPublico"] = $r["ServicioPublico"];
                array_push($response, $tipo_corresp);
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

    public function set_correspondencia($IDClub, $IDSocio, $IDUsuario, $IDTipoCorrrespondencia, $Vivienda, $Destinatario, $FechaRecepcion, $HoraRecepcion, $EntregarATodos, $Archivo, $File = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($EntregarATodos) && !empty($IDTipoCorrrespondencia)) {

            if ($EntregarATodos == "S") {
                $condicion_socio = " ";
            } else {
                $condicion_socio = " and IDSocio = '" . $IDSocio . "'";
            }

            if (isset($File)) {

                $files = SIMFile::upload($File["Archivo"], CORRESPONDENCIA_DIR, "IMAGE");
                if (empty($files) && !empty($File["Archivo"]["name"])) :
                    $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
                $Archivo = $files[0]["innername"];
            } //end if

            $sql_socio = "Select * From Socio Where IDClub = '" . $IDClub . "' " . $condicion_socio;
            $r_socio = $dbo->query($sql_socio);
            while ($row_socio = $dbo->FetchArray($r_socio)) {
                $inserta_corresp = "INSERT INTO Correspondencia (IDClub, IDSocio, IDTipoCorrespondencia, IDUsuarioCrea, IDCorrespondenciaEstado, Vivienda, Destinatario, FechaRecepcion, Archivo, UsuarioTrCr, FechaTrCr) Values
                                                                                                                                            ('" . $IDClub . "','" . $IDSocio . "','" . $IDTipoCorrrespondencia . "','" . $IDUsuario . "','1','" . $Vivienda . "','" . $Destinatario . "','" . $FechaRecepcion . " " . $HoraRecepcion . "','" . $Archivo . "','" . $IDUsuario . "',NOW())";
                $dbo->query($inserta_corresp);
                //Envio la notificacion
                if ($EntregarATodos != "S") {
                    $Mensaje = "Nueva correspondencia: " . utf8_decode($dbo->getFields("TipoCorrespondencia", "Nombre", "IDTipoCorrespondencia = '" . $IDTipoCorrrespondencia . "'"));
                    SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocio, $Mensaje);
                } else {
                    //Inserto Cola de notificaciones
                    $sql_notif = "INSERT INTO ColaNotificacionPush (IDClub, IDSocio, Mensaje, UsuarioTrCr, FechaTrCr) VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $Mensaje . "','Aut',NOW())";
                    $dbo->query($sql_notif);
                }
            }

            $respuesta["message"] = "guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = "";
        } else {
            $respuesta["message"] = "E1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_entrega_correspondencia($IDClub, $IDSocio, $IDUsuario, $IDCorrespondencia, $FechaEntrega, $HoraEntrega, $EntregadoA, $Archivo, $File = "", $Dispositivo)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDUsuario) && !empty($IDCorrespondencia) && !empty($EntregadoA)) {

            if (isset($File)) {
                if ($Dispositivo == "Android") {
                    $nombreArchivo = "FotoFirma";
                } else {
                    $nombreArchivo = "Archivo";
                }


                //$files = SIMFile::upload($File["FotoFirma"], CORRESPONDENCIA_DIR, "IMAGE");
                $files = SIMFile::upload($File[$nombreArchivo], CORRESPONDENCIA_DIR, "IMAGE");
                //if (empty($files) && !empty($File["FotoFirma"]["name"])) :
                if (empty($files) && !empty($File[$nombreArchivo]["name"])) :
                    $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;
                $Archivo = $files[0]["innername"];
            } //end if

            $datos_corresp = $dbo->fetchAll("Correspondencia", " IDCorrespondencia = '" . $IDCorrespondencia . "' ", "array");
            $update_corresp = "UPDATE Correspondencia Set IDUsuarioEntrega = '" . $IDUsuario . "', IDCorrespondenciaEstado=2, FechaEntrega='" . $FechaEntrega . " " . $HoraEntrega . "', EntregadoA='" . $EntregadoA . "',FotoFirma='" . $Archivo . "',UsuarioTrEd='" . $IDUsuario . "',FechaTrEd =NOW() Where IDCorrespondencia = '" . $IDCorrespondencia . "'";
            $dbo->query($update_corresp);
            $Mensaje = "Correspondencia: " . utf8_decode($dbo->getFields("TipoCorrespondencia", "Nombre", "IDTipoCorrespondencia = '" . $datos_corresp["IDTipoCorrrespondencia"] . "'")) . " entregado a " . $EntregadoA;
            SIMUtil::enviar_notificacion_push_general($IDClub, $IDSocio, $Mensaje);

            $respuesta["message"] = "guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = "";
        } else {
            $respuesta["message"] = "E1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }
}
