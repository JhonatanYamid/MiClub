<?php
class SIMWebServiceQrDinamico
{

    public function get_codigo_QR_carnet_dinamico($IDClub, $IDSocio, $IDUsuario, $IDSocioCarnet)
    {

        $dbo = &SIMDB::get();


        $sql = "SELECT DuracionQr,IDClub,TextoHeaderCodigoQr FROM ConfiguracionCarnet  WHERE IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($DatosConfiguracion = $dbo->fetchArray($qry)) {
                $NumeroDocumentoSocio = $dbo->getFields('Socio', 'NumeroDocumento', "IDSocio = $IDSocioCarnet");


                //PARA CASTILLO DE AMAGUAÑA GENERO EL QR CON EL VALOR QUE ME MANDAN
                if ($IDClub == 86) {
                    $TokenCastillo = $dbo->getFields('Socio', 'TokenCastillo', "IDSocio = $IDSocioCarnet");
                    if (!empty($TokenCastillo)) {
                        $urlPeticion = "http://181.39.25.76:44180/WebApiSIAccessCastillo/GetQr/" . $TokenCastillo;
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $urlPeticion);
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_HTTPGET, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($ch);
                        $response = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);

                        $ValorQr = $response["valorQr"];
                        if (!empty($ValorQr)) {

                            $configuracionQr["IDClub"] = $DatosConfiguracion["IDClub"];
                            $configuracionQr["IDSocio"] = $IDSocio;
                            $configuracionQr["DuracionSegundos"] = $DatosConfiguracion["DuracionQr"];
                            $configuracionQr["CodigoBarras"] = SIMUtil::generar_qr($IDInvitacionGenerada, $ValorQr, "MostrarSoloImagen");
                            $configuracionQr["TextoHeaderCodigoBarras"] = $DatosConfiguracion["TextoHeaderCodigoQr"];
                        } else {
                            $respuesta["message"] = "El servicio no esta devolviendo un valor para generar el qr.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    } else {
                        $respuesta["message"] = "Token esta vacio.";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                } else {
                    $configuracionQr["IDClub"] = $DatosConfiguracion["IDClub"];
                    $configuracionQr["IDSocio"] = $IDSocio;
                    $configuracionQr["DuracionSegundos"] = $DatosConfiguracion["DuracionQr"];
                    $configuracionQr["CodigoBarras"] = SIMUtil::generar_qr($IDInvitacionGenerada, $NumeroDocumentoSocio, "MostrarSoloImagen");
                    $configuracionQr["TextoHeaderCodigoBarras"] = $DatosConfiguracion["TextoHeaderCodigoQr"];
                }
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracionQr;
        } //End if
        else {
            $respuesta["message"] = "Configuracion no esta activa";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function activar_carnet_seguridad_socio($IDClub, $IDSocio, $IDUsuario, $UID, $Modelo, $CorreoElectronico)
    {
        $dbo = &SIMDB::get();

        if (!empty($CorreoElectronico)) {

            //CONSULTO CEDULA DEL SOCIO PARA HACER LA PETICION A CASTILLO DE AMAGUAÑA  DE CONSULTAR A LOS SOCIOS
            $NumeroDocumentoSocio = $dbo->getFields('Socio', 'NumeroDocumento', "IDSocio = $IDSocio");

            if (empty($NumeroDocumentoSocio)) {
                $respuesta["message"] = "Numero Documento activar canet seguridad socio no puede estar vacio.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            $urlPeticion = "http://181.39.25.76:44180/WebApiSIAccessCastillo/ConsultaDatosSocio/" . $NumeroDocumentoSocio;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlPeticion);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);

            if ($response["estado"] == 0) { //SOCIO ACTIVO


                //ACTUALIZAMOS EL CODIGO UID Y MODELO CELULAR  Y CORREO ELECTRONICO EN LA TABLA SOCIO
                $sql_actualiza_socio = "UPDATE Socio SET CodigoUID='" . $UID . "',ModeloCelular='" . $Modelo . "',CorreoElectronicoEnrolamiento='" . $CorreoElectronico . "' WHERE IDSocio='" . $IDSocio . "' AND IDClub='" . $IDClub . "'";

                $dbo->query($sql_actualiza_socio);
                $respuesta["message"] = "Usuario puede continuar proceso";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
                // }
            } else if ($response["estado"] == 2) { //SOCIO CON LA MEMBRESIA ANULADA
                $respuesta["message"] = "Membresía Anulada.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = $response["estadoDescripcion"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "Correo no puede estar vacio.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
        //print_r($response);
    }

    public function solicitar_otp_carnet_seguridad_socio($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();



        //CONSULTO CEDULA DEL SOCIO PARA HACER LA PETICION A CASTILLO DE AMAGUAÑA  DE CONSULTAR A LOS SOCIOS
        $NumeroDocumentoSocio = $dbo->getFields('Socio', 'NumeroDocumento', "IDSocio = $IDSocio");
        $Dispositivo = $dbo->getFields('Socio', 'Dispositivo', "IDSocio = $IDSocio");

        //CONSULTO EL CORREO QUE SE INGRESO ANTERIORMENTE EN EL ENROLAMIENTO Y YA ESTA EN LA TABLA SOCIO
        $CorreoElectronico = $dbo->getFields('Socio', 'CorreoElectronicoEnrolamiento', "IDSocio = $IDSocio");

        if (empty($CorreoElectronico)) {
            $respuesta["message"] = "Correo enrolamiento no puede estar vacio.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        } elseif (empty($Dispositivo)) {
            $respuesta["message"] = "Dispositivo no puede estar vacio.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        } elseif (empty($NumeroDocumentoSocio)) {
            $respuesta["message"] = "Numero Documento solicitar otp no puede estar vacio.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
            return $respuesta;
        }

        $urlPeticion = "http://181.39.25.76:44180/WebApiSIAccessCastillo/ConsultaDatosSocio/" . $NumeroDocumentoSocio;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlPeticion);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $response = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);

        if ($response["estado"] == 0) { //SOCIO ACTIVO

            //VERIFICAMOS EL SOCIO  Y ENVIAMOS CORREO
            $urlPeticion2 = "http://181.39.25.76:44180/WebApiSIAccessCastillo/VerificaSocio/" . $NumeroDocumentoSocio . "/" . $CorreoElectronico . "/" . $Dispositivo;
            /*   echo $urlPeticion2;
            exit; */
            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $urlPeticion2);
            curl_setopt($ch2, CURLOPT_HEADER, false);
            curl_setopt($ch2, CURLOPT_HTTPGET, true);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            $response2 = curl_exec($ch2);
            $response2 = json_decode($response2, true, 512, JSON_BIGINT_AS_STRING);

            if ($response2["valor"] == 0) { //SOCIO VERIFICADO CORRECTAMENTE

                $respuesta["message"] = "Te hemos enviado un código de confirmación de 6 dígitos a la dirección de correo electrónico validado, por favor ingresa el código de activación.";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = $response2["mensaje"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else if ($response["estado"] == 2) { //SOCIO CON LA MEMBRESIA ANULADA
            $respuesta["message"] = "Membresía Anulada.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = $response["estadoDescripcion"];
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
        //print_r($response);
    }

    public function verificar_otp_carnet_seguridad_socio($IDClub, $IDSocio, $IDUsuario, $Codigo)
    {
        $dbo = &SIMDB::get();

        if (!empty($Codigo)) {
            //CODIGO UID DEL SOCIO
            $CodigoUID = $dbo->getFields('Socio', 'CodigoUID', "IDSocio = $IDSocio");
            $Modelo = $dbo->getFields('Socio', 'ModeloCelular', "IDSocio = $IDSocio");
            $NumeroDocumento = $dbo->getFields('Socio', 'NumeroDocumento', "IDSocio = $IDSocio");

            if (empty($CodigoUID)) {
                $respuesta["message"] = "Codigo UID  no puede estar vacio.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            } elseif (empty($Modelo)) {
                $respuesta["message"] = "Modelo no puede estar vacio.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            } elseif (empty($NumeroDocumento)) {
                $respuesta["message"] = "Numero Documento verificar otp carnet seguridad socio no puede estar vacio.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }

            //CONSULTO EL CORREO QUE SE INGRESO ANTERIORMENTE EN EL ENROLAMIENTO Y YA ESTA EN LA TABLA SOCIO
            $CorreoElectronico = $dbo->getFields('Socio', 'CorreoElectronicoEnrolamiento', "IDSocio = $IDSocio");
            $urlPeticion = "http://181.39.25.76:44180/WebApiSIAccessCastillo/AutenticaDispositivo/" . $CodigoUID . "/" . $Modelo . "/" . $Modelo . "/" . $NumeroDocumento . "/" . $Codigo;
            //echo $urlPeticion;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlPeticion);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);

            if ($response["codError"] == 0) {
                $Token = $response["valorToken"];
                //GUARDO TOKEN DEL SOCIO Y ACTIVO CARNET 
                $sql_actualiza_token = "UPDATE Socio SET TokenCastillo='" . $Token  . "',ActivarCarnetSeguridad='N'  WHERE IDSocio='" . $IDSocio . "' AND IDClub='" . $IDClub . "'";
                $dbo->query($sql_actualiza_token);
                //$respuesta["message"] = $response["mensajeError"];
                $respuesta["message"] = "Su carné fue activado exitosamente.";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = $response["mensajeError"];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "Codigo no puede ser vacio.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
    public function activar_carnet_seguridad_miembro_familia($IDClub, $IDSocio, $IDUsuario, $IDSocioFamiliar)
    {

        require LIBDIR  . "SIMWebServiceCastillo.inc.php";
        $dbo = &SIMDB::get();

        if (!empty($IDClub) && !empty($IDSocio) && !empty($IDSocioFamiliar)) {
            //TOKEN SOCIO
            $TokenSocioTitular = $dbo->getFields('Socio', 'TokenCastillo', "IDSocio = $IDSocio");
            $CodigoUID = $dbo->getFields('Socio', 'CodigoUID', "IDSocio = $IDSocio");
            $ModeloCelular = $dbo->getFields('Socio', 'ModeloCelular', "IDSocio = $IDSocio");

            //NUMERO DOCUMENTO BENEFICIARIO
            $NumeroDocumento = (int) $dbo->getFields('Socio', 'NumeroDocumento', "IDSocio = $IDSocioFamiliar");

            if (empty($TokenSocioTitular)) {
                $respuesta["message"] = "Debe de activar primero al socio titular.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }



            $urlPeticion = "http://181.39.25.76:44180/WebApiSIAccessCastillo/GetDependientes/" . $TokenSocioTitular;
            //echo $urlPeticion;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlPeticion);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);



            if (!empty($response)) {

                foreach ($response as  $value) {

                    $Documento = $value["numeroDocumento"];
                    if ($Documento == $NumeroDocumento) {

                        //GENERO TOKEN DE LOS BENEFICIARIOS
                        $urlPeticion2 = "http://181.39.25.76:44180/WebApiSIAccessCastillo/AutenticaDispositivoDependiente/" . $CodigoUID . "/" . $ModeloCelular . "/" . $ModeloCelular . "/" . $Documento;


                        $ch2 = curl_init();
                        curl_setopt($ch2, CURLOPT_URL, $urlPeticion2);
                        curl_setopt($ch2, CURLOPT_HEADER, false);
                        curl_setopt($ch2, CURLOPT_HTTPGET, true);
                        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                        $response2 = curl_exec($ch2);
                        $response2 = json_decode($response2, true, 512, JSON_BIGINT_AS_STRING);

                        if ($response2["codError"] == 0) {
                            //ACTUALIZO EL TOKEN
                            $TokenBeneficiario = $response2["valorToken"];
                            $sql_actualiza_token_socio_beneficiario = "UPDATE Socio SET TokenCastillo='" . $TokenBeneficiario  . "',ActivarCarnetSeguridad='N'  WHERE IDSocio='" . $IDSocioFamiliar . "' AND IDClub='" . $IDClub . "'";

                            $dbo->query($sql_actualiza_token_socio_beneficiario);

                            //GENERO LA IMAGEN al beneficiario
                            SIMWebServiceCastillo::obtener_foto($IDClub, $IDSocioFamiliar);

                            $respuesta["message"] = "Activado Correctamente";
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                            break;
                        } else {
                            $respuesta["message"] = $response2["mensajeError"];
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            break;
                        }
                        // exit;
                    } else {
                        $respuesta["message"] = "No se encontraron coincidencias en los numeros de los documentos";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                }
            } else {
                $respuesta["message"] = "No se encontraron beneficiarios.";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "Faltan parametros.Acsmf";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
}
