<?php

class SIMWebServiceValet
{
    public function set_solicitar_vehiculo($IDClub, $IDSocio, $Placa, $Tercero)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($Placa) && !empty($Tercero)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            //Verifico que la placa exista
            $id_valet = $dbo->getFields("ValetParking", "IDValetParking", "Placa = '" . $Placa . "' and IDClub = '" . $IDClub . "'");
            if (!empty($id_socio)) {
                if (!empty($id_valet)) {
                    //inserto movimiento
                    $sql_log = "INSERT INTO LogValetParking(IDClub,IDSocio,Estado,Placa,FechaSolicitado,UsuarioTrCr, FechaTrCr)
                                                VALUES ('" . $IDClub . "','" . $IDSocio . "','Solicitado','" . $Placa . "',NOW(),'SOCIO APP',NOW())";
                    $dbo->query($sql_log);
                    $sql_valet = $dbo->query("UPDATE ValetParking SET Estado='Solicitado', FechaSolicitado = NOW() WHERE Placa = '" . $Placa . "'");
                    $id_valet = $dbo->lastID();
                    $Mensaje = "Solicitud entrega vehiculo: " . $Placa;
                    SIMUtil::enviar_notificacion_push_entrega_vehiculo($IDClub, $Mensaje);

                    $respuesta["message"] = "Solicitado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "La placa no esta registrada!";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "O1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_recibir_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $Cedula, $Nombre, $NumeroParqueadero, $IDVehiculoValetParking)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDUsuario) && !empty($Placa) && !empty($NumeroParqueadero)) {

            //Verifico que la placa exista
            $id_valet = $dbo->getFields("ValetParking", "IDValetParking", "Placa = '" . $Placa . "' and IDClub = '" . $IDClub . "'");

            //inserto movimiento
            $sql_log = "INSERT INTO LogValetParking(IDClub,IDSocio,IDVehiculoValetParking,Estado,Placa,IDUsuarioRecibe,FechaRecibe,CedulaTercero,NombreTercero,NumeroParqueadero,UsuarioTrCr, FechaTrCr)
                                    VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDVehiculoValetParking . "','Recibido','" . $Placa . "','" . $IDUsuario . "',NOW(),'" . $Cedula . "','" . $Nombre . "','" . $NumeroParqueadero . "','SOCIO APP',NOW())";
            $dbo->query($sql_log);

            if (!empty($id_valet)) {
                $sql_valet = $dbo->query("UPDATE ValetParking
             SET Estado='Recibido',IDUsuarioRecibe='" . $IDUsuario . "',FechaRecibe=NOW(),CedulaTercero='" . $Cedula . "',NombreTercero='" . $Nombre . "',NumeroParqueadero='" . $NumeroParqueadero . "',UsuarioTrCr='" . $IDUsuario . "', FechaTrCr=NOW()
                                                                                    WHERE Placa = '" . $Placa . "'");
            } else {
                $sql_valet = "INSERT INTO ValetParking(IDClub,IDSocio,IDVehiculoValetParking,Estado,Placa,IDUsuarioRecibe,FechaRecibe,CedulaTercero,NombreTercero,NumeroParqueadero,UsuarioTrCr, FechaTrCr)
                                        VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDVehiculoValetParking . "','Recibido','" . $Placa . "','" . $IDUsuario . "',NOW(),'" . $Cedula . "','" . $Nombre . "','" . $NumeroParqueadero . "','SOCIO APP',NOW())";
                $dbo->query($sql_valet);
            }
            $respuesta["message"] = "Vehiculo recibido con exito";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "O1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_entregar_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $IDTiposDePagoValetParking, $NumeroPago)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($Placa)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            //Verifico que la placa exista
            $id_valet = $dbo->getFields("ValetParking", "IDValetParking", "Placa = '" . $Placa . "' and IDClub = '" . $IDClub . "'");
            if (!empty($id_socio)) {
                if (!empty($id_valet)) {
                    //inserto movimiento
                    $sql_log = "INSERT INTO LogValetParking(IDClub,IDSocio,IDTiposDePagoValetParking,NumeroPago,Estado,Placa,IDUsuarioEntrega,FechaEntrega,UsuarioTrCr, FechaTrCr)
                                                VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDTiposDePagoValetParking . "','" . $NumeroPago . "','Entregado','" . $Placa . "','" . $IDUsuario . "',NOW(),'" . $IDUsuario . "',NOW())";
                    $dbo->query($sql_log);
                    $sql_valet = $dbo->query("UPDATE ValetParking SET Estado='Entregado',IDUsuarioEntrega='" . $IDUsuario . "',IDTiposDePagoValetParking='" . $IDTiposDePagoValetParking . "',NumeroPago='" . $NumeroPago . "',FechaEntrega=NOW() WHERE Placa = '" . $Placa . "'");

                    $respuesta["message"] = "Entregado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "La placa no esta registrada!";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "O1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_cancelar_entregar_vehiculo($IDClub, $IDSocio, $IDUsuario, $Placa, $Tercero)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && !empty($IDSocio) && !empty($Placa) && !empty($Tercero)) {

            //verifico que el socio exista y pertenezca al club
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            //Verifico que la placa exista
            $id_valet = $dbo->getFields("ValetParking", "IDValetParking", "Placa = '" . $Placa . "' and IDClub = '" . $IDClub . "'");
            if (!empty($id_socio)) {
                if (!empty($id_valet)) {
                    //inserto movimiento
                    $sql_log = "INSERT INTO LogValetParking(IDClub,IDSocio,Estado,Placa,FechaSolicitado,UsuarioTrCr, FechaTrCr)
                                                VALUES ('" . $IDClub . "','" . $IDSocio . "','Cancelado','" . $Placa . "',NOW(),'SOCIO APP',NOW())";
                    $dbo->query($sql_log);
                    $sql_valet = $dbo->query("UPDATE ValetParking SET Estado='Recibido' WHERE Placa = '" . $Placa . "'");
                    $id_valet = $dbo->lastID();
                    $Mensaje = "Cancelacion de entrega vehiculo: " . $Placa;
                    SIMUtil::enviar_notificacion_push_entrega_vehiculo($IDClub, $Mensaje);

                    $respuesta["message"] = "Cancelado con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "La placa no esta registrada!";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "Atencion el socio no existe o no pertenece al club";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "O1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_configuracion_valet($IDClub)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT * FROM ConfiguracionValet  WHERE IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                $configuracion["MensajeCancelacion"] = $r["MensajeCancelacion"];
                $configuracion["MensajeSolicitar"] = $r["MensajeSolicitar"];
                $configuracion["MensajeSolicitarTercero"] = $r["MensajeSolicitarTercero"];
                $configuracion["BotonSolicitar"] = $r["BotonSolicitar"];
                $configuracion["BotonSolicitarTercero"] = $r["BotonSolicitarTercero"];
                $configuracion["BotonCancelacion"] = $r["BotonCancelacion"];
                $configuracion["LabelRecibirSocio"] = $r["LabelRecibirSocio"];
                $configuracion["BotonBuscarSocio"] = $r["BotonBuscarSocio"];
                $configuracion["BotonRecibirSocio"] = $r["BotonRecibirSocio"];
                $configuracion["LabelRecibirTercero"] = $r["LabelRecibirTercero"];
                $configuracion["BotonRecibirTercero"] = $r["BotonRecibirTercero"];
                $configuracion["PermiteSolicitarTipoVehiculo"] = $r["PermiteSolicitarTipoVehiculo"];
                $configuracion["ObligatorioRegistrarTipoVehiculo"] = $r["ObligatorioRegistrarTipoVehiculo"];

                //VEHICULOS PARKING
                $array_vehiculos = array();
                $sql_vehiculos = "SELECT Nombre,IDVehiculoValetParking FROM VehiculoValetParking  WHERE IDClub = '" . $IDClub . "' AND Publicar='S'";
                $qry_vehiculos = $dbo->query($sql_vehiculos);
                while ($rVehiculos = $dbo->fetchArray($qry_vehiculos)) {
                    $vehiculos["IDTipoVehiculo"] = $rVehiculos["IDVehiculoValetParking"];
                    $vehiculos["Nombre"] = $rVehiculos["Nombre"];
                    array_push($array_vehiculos, $vehiculos);
                }
                $configuracion["TiposVehiculo"] = $array_vehiculos;


                //TIPOS DE PAGO
                $array_tipos_de_pago = array();
                $sql_pagos = "SELECT IDTiposDePagoValetParking,Nombre,MostrarNumeroFactura,ObligatorioNumeroFactura FROM TiposDePagoValetParking  WHERE IDClub = '" . $IDClub . "' AND Publicar='S'";
                $qry_pagos = $dbo->query($sql_pagos);
                while ($rPagos = $dbo->fetchArray($qry_pagos)) {
                    $tiposdepago["IDTipoPago"] = $rPagos["IDTiposDePagoValetParking"];
                    $tiposdepago["Nombre"] = $rPagos["Nombre"];
                    $tiposdepago["MostrarNumeroFactura"] = $rPagos["MostrarNumeroFactura"];
                    $tiposdepago["ObligatorioNumeroFactura"] = $rPagos["ObligatorioNumeroFactura"];
                    array_push($array_tipos_de_pago, $tiposdepago);
                }
                $configuracion["TiposDePago"] = $array_tipos_de_pago;


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

    public function get_vehiculo_registrado_socio($IDClub, $IDSocio)
    {

        $dbo = &SIMDB::get();

        //configuracion valet parking
        $config_valet = $dbo->fetchAll("ConfiguracionValet", " IDClub = '" . $IDClub . "' ", "array");


        $response = array();
        $sql = "SELECT * FROM ValetParking  WHERE IDSocio = '" . $IDSocio . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {

                $valet["IDSocio"] = $r["IDSocio"];
                $valet["IDValetParking"] = $r["IDValetParking"];
                $valet["Placa"] = $r["Placa"];
                $valet["Estado"] = $r["Estado"];


                //Usuario que recibe o entrega
                if ($r["IDUsuarioEntrega"] > 0) {
                    $IDUsuario = $r["IDUsuarioEntrega"];
                } else {
                    $IDUsuario = $r["IDUsuarioRecibe"];
                }



                //MOSTRAR EL COLOR

                if ($r["Estado"] == "Recibido") {
                    $colorEstado = $config_valet["ColorRecibido"];
                    $mensaje = " por empleado";
                } elseif ($r["Estado"] == "Solicitado") {
                    $colorEstado = $config_valet["ColorSolicitado"];
                    $mensaje = " al empleado";
                } elseif ($r["Estado"] == "Entregado") {
                    $colorEstado = $config_valet["ColorEntregado"];
                    $mensaje = " por el empleado";
                } else {
                    $colorEstado = $config_valet["ColorCancelado"];
                    $mensaje = " por el socio";
                }
                $valet["TextoEstado"] = $r["Estado"] . $mensaje;
                $valet["ColorEstado"] = $colorEstado;
                $valet["NombreEmpleado"] = utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDUsuario . "'"));




                array_push($response, $valet);
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

    public function get_solicitud_entrega($IDClub, $Placa = "")
    {
        $dbo = &SIMDB::get();
        // Seccion Especifica
        if (!empty($Placa)) :
            $array_condiciones[] = " Placa  = '" . $Placa . "'";
        endif;

        if (count($array_condiciones) > 0) :
            $condiciones = implode(" and ", $array_condiciones);
            $condiciones = " and " . $condiciones;
        endif;

        //mes actual
        $mes = date("m");

        //configuracion valet parking
        $config_valet = $dbo->fetchAll("ConfiguracionValet", " IDClub = '" . $IDClub . "' ", "array");


        $response = array();
        //Verifico si se muestran los estados dependiendo de eso hago la consulta
        if ($config_valet["PermiteVerEstados"] == "S") {
            $sql = "SELECT * FROM ValetParking WHERE  MONTH(FechaTrCr)='" . $mes . "' AND IDClub = '" . $IDClub . "'" . $condiciones . " ORDER BY FechaTrCr ASC";
        } else {

            $sql = "SELECT * FROM ValetParking WHERE Estado = 'Solicitado' and IDClub = '" . $IDClub . "'" . $condiciones . " ORDER BY FechaSolicitado ASC";
        }

        //   echo $sql;

        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {
                //datos socio
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $r["IDSocio"] . "' ", "array");


                $msg = $config_valet["TextoAdicional"];

                $valet["IDClub"] = $IDClub;
                $valet["IDValetParking"] = $r["IDValetParking"];
                $valet["Placa"] = $r["Placa"];
                $valet["IDSocio"] = $r["IDSocio"];
                $valet["Socio"] = utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r["IDSocio"] . "'"));
                $valet["CedulaTercero"] = utf8_encode($r["CedulaTercero"]);
                $valet["NombreTercero"] = utf8_encode($r["NombreTercero"]);

                //VERIFICO SI SE MUESTRAN LOS ESTADOS

                if ($config_valet["PermiteVerEstados"] == "S") {

                    //texto adicional
                    if (!empty($msg)) {
                        $msg = str_replace("[AccionSocio]", $datos_socio["Accion"], $msg);
                    } else {
                        $msg = "";
                    }

                    $valet["TextoAdicional"] = $msg;

                    //Usuario que recibe o entrega
                    if ($r["IDUsuarioEntrega"] > 0) {
                        $IDUsuario = $r["IDUsuarioEntrega"];
                    } else {
                        $IDUsuario = $r["IDUsuarioRecibe"];
                    }



                    if ($r["Estado"] == "Recibido") {
                        $colorEstado = $config_valet["ColorRecibido"];
                        $mensaje = " por:" . utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDUsuario . "'"));
                    } elseif ($r["Estado"] == "Solicitado") {
                        $colorEstado = $config_valet["ColorSolicitado"];
                        $mensaje = " por:" . utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r["IDSocio"] . "'"));
                    } elseif ($r["Estado"] == "Entregado") {
                        $colorEstado = $config_valet["ColorEntregado"];
                        $mensaje = " por:" . utf8_encode($dbo->getFields("Usuario", "Nombre", "IDUsuario = '" . $IDUsuario . "'"));
                    } else {
                        $colorEstado = $config_valet["ColorCancelado"];
                        $mensaje = " por:" . utf8_encode($dbo->getFields("Socio", "Nombre", "IDSocio = '" . $r["IDSocio"] . "'") . " " . $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $r["IDSocio"] . "'"));
                    }

                    $valet["TextoEstado"] = $r["Estado"]  . $mensaje;

                    $valet["ColorEstado"] = $colorEstado;
                }

                //si permite pagos
                $valet["PermiteSolicitarTipoPago"] = $config_valet["PermiteSolicitarTipoPago"];
                $valet["ObligatorioSolicitarTipoPago"] = $config_valet["ObligatorioSolicitarTipoPago"];

                array_push($response, $valet);
            } //end while

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


}
