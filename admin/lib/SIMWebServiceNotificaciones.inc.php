<?php

    class SIMWebServiceNotificaciones
    {

        public function get_notificaciones($IDClub, $IDSocio, $TipoApp)
        {
            $dbo = &SIMDB::get();
    
            $response = array();
    
            if ($TipoApp == "Empleado") {
                $sql = "SELECT * FROM LogNotificacion WHERE App='Empleado' and IDUsuario = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' ORDER BY FechaReserva DESC, IDLogNotificacion Desc Limit 30";
            } else {
                $sql = "SELECT * FROM LogNotificacion WHERE App='Socio' and IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' and DATE(Fecha) > CURDATE() - INTERVAL 30 DAY ORDER BY IDLogNotificacion Desc Limit 50";
            }
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " Encontrados";
                while ($r = $dbo->fetchArray($qry)) {
                    $notificacion["IDLogNotificacion"] = $r["IDLogNotificacion"];
                    $notificacion["Tipo"] = $r["Tipo"];
                    $notificacion["Titulo"] = $r["Titulo"];
                    $notificacion["Mensaje"] = $r["Mensaje"];
                    $notificacion["Modulo"] = $r["Modulo"];
                    if((int)$r["IDSeccion"]>0)
                        $notificacion["IDSeccion"] = $r["IDSeccion"];
                    else    
                        $notificacion["IDSeccion"] = "";

                    if((int)$r["SubModulo"]>0)
                        $notificacion["SubModulo"] = $r["SubModulo"];
                    else    
                        $notificacion["SubModulo"] = "";                        
                        
                    if((int)$r["IDDetalle"]>0)
                        $notificacion["IDDetalle"] = $r["IDDetalle"];
                    else    
                        $notificacion["IDDetalle"] = "";    

                    if ($r["Leido"] == "") {
                        $Leido = "N";
                    } else {
                        $Leido = $r["Leido"];
                    }
    
                    $notificacion["Leido"] = $Leido;
                    $notificacion["Link"] = $r["Link"];
                    array_push($response, $notificacion);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = "No se encontraron registro";
                $respuesta["success"] = true;
                $respuesta["response"] = "";
            } //end else
    
            return $respuesta;
        } // fin function
    
        public function set_notificacion_leida($IDClub, $IDSocio, $IDLogNotificacion)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($IDLogNotificacion)) {
    
                if (!empty($IDLogNotificacion)) :
                    $array_not = explode(",", $IDLogNotificacion);
                    if (count($array_not) > 0) :
                        foreach ($array_not as $id_not) :
                            //$sql_leida = $dbo->query("Update LogNotificacion Set Leido = 'S' Where IDLogNotificacion = '".$id_not."' and IDSocio = '".$IDSocio."'");
                            $sql_leida = $dbo->query("Update LogNotificacion Set Leido = 'S' Where IDLogNotificacion = '" . $id_not . "'");
                        endforeach;
                    endif;
                endif;
    
                $respuesta["message"] = "Notificacion marcada como leida con exito";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "21. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
    
            return $respuesta;
        }

        
    }