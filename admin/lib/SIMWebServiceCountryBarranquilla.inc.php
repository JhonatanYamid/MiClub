<?php

    class SIMWebServiceCountryBarranquilla
    {
        public function valida_quilla($NumeroDocumento, $ValoresFormulario, $IDSocio, $Nombre)
        {
            $dbo = &SIMDB::get();
            $params = array(
                "Cedula" => $NumeroDocumento,
            );
            $client = new SoapClient(URLBQUILLA . "webserviceappcountry/Service1.asmx?WSDL");
            $response = $client->__soapCall("GetVisitanteValidador", array($params));
            $xml = $response->GetVisitanteValidadorResult;
            $xmlf = simplexml_load_string($xml);
            foreach ($xmlf as $idx => $datos) {
                $resultado = $datos->RESULTADO;
            }

            if ($resultado != "OK") {
                $respuesta["message"] = utf8_encode($resultado);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            } else {
                $ConsecutivoArea = "";
                //Consulto el id del area
                $params = array(
                    "GetAreaVisitante" => "GetAreaVisitante",
                );
                $client = new SoapClient(URLBQUILLA . "webserviceappcountry/Service1.asmx?WSDL");
                $response = $client->__soapCall("GetAreaVisitante", array($params));
                $xml = $response->GetAreaVisitanteResult;
                $xmlf = simplexml_load_string($xml);

                $datos_formulario = json_decode($ValoresFormulario, true);
                if (count($datos_formulario) > 0):
                    foreach ($datos_formulario as $detalle_datos):
                        if ($detalle_datos["IDCampoFormularioInvitado"] == 25) {
                            $Nombrerea = $detalle_datos["Valor"];
                        }

                    endforeach;
                endif;
                foreach ($xmlf as $idx => $datos) {
                    if ($Nombrerea == $datos->NomArea) {
                        $ConsecutivoArea = $datos->Consc;
                    }
                }

                if (empty($ConsecutivoArea)) {
                    $respuesta["message"] = "No se encuentra el area, por favor verifique";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                } else {
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                    //Envio la invitacion
                    $params = array(
                        "Idunico" => $datos_socio["IdentificadorExterno"],
                        "Invitado" => $Nombre,
                        "Cedula" => $NumeroDocumento,
                        "Area" => $ConsecutivoArea,
                    );
                    $client = new SoapClient(URLBQUILLA . "webserviceappcountry/Service1.asmx?WSDL");
                    $response = $client->__soapCall("InsertVisitanteIngreso", array($params));
                    $xml = $response->InsertVisitanteIngresoResult;
                    if ($xml != "exito") {
                        $respuesta["message"] = "No fue posible enviar la invitacion, por favor intente mas tarde";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        $respuesta["message"] = "";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                }
            }
            return $respuesta;
        }

        public function cancela_invitacion_bquilla($IDClub, $IdentificadorExterno, $NumeroDocumento)
        {
            $dbo = &SIMDB::get();

            $params = array(
                "idunico" => $IdentificadorExterno,
            );
            $client = new SoapClient(URLBQUILLA . "webserviceappcountry/Service1.asmx?WSDL");
            $response = $client->__soapCall("GetVisitantePendiente", array($params));
            $xml = $response->GetVisitantePendienteResult;
            $xmlf = simplexml_load_string($xml);
            foreach ($xmlf as $idx => $datos) {
                if ($datos->Cedula == $NumeroDocumento) {
                    $consec = $datos->Consc;
                }
            }
            if (empty($consec)) {
                $respuesta["message"] = "Invitacion no encontrada, intente mas tarde";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } else {
                ///Elimino la reserva
                $params = array(
                    "Consc" => $consec,
                );
                $client = new SoapClient(URLBQUILLA . "webserviceappcountry/Service1.asmx?WSDL");
                $response = $client->__soapCall("UpdateVisitanteCancelar", array($params));
                $xml = $response->UpdateVisitanteCancelarResult;
                if ($xml != "exito") {
                    $respuesta["message"] = "No se pudo cancelar la invitacion, por favor intente mas tarde";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = $consec;
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                }
            }
            return $respuesta;
        }  
    }