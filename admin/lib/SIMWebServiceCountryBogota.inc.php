<?php
    class SIMWebServiceCountryBogota
    {
         //WebService Country CLUB
        public function autentica_country_club($usuario, $clave)
        {

            $estado_autentica = 0;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_PORT => "3000",
                CURLOPT_URL => "https://countryclubdebogota.com:3000/sessions/api_create",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\"session\":{\"username\":\"$usuario\",\"password\":\"$clave\"}}",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer 4beafec232f5610a2985bcc37f4693f2",
                    "Content-Type: application/json",
                    "Postman-Token: 8b8a043a-bd8d-4f92-ae7c-36740164bf5f",
                    "cache-control: no-cache",
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                //echo "cURL Error #:" . $err;
                $estado_autentica = 0;
            } else {
                $resultado_autentica = json_decode($response, true);
                if (!empty($resultado_autentica["message"])) {
                    $estado_autentica = 0;
                } else {
                    $CorreoElectronico = $resultado_autentica["data"]["attributes"]["email"];
                    $Documento = $resultado_autentica["data"]["attributes"]["document_number"];
                    $IDSocioExterno = $resultado_autentica["data"]["attributes"]["id_socio"];
                    $UsuarioApp = $resultado_autentica["data"]["attributes"]["username"];
                    $Nombre = $resultado_autentica["data"]["attributes"]["name"];
                    $Apellido = $resultado_autentica["data"]["attributes"]["last_name"];
                    $Accion = $Documento;
                    $cambiar_clave = "N";

                    $estado_autentica = SIMWebServiceApp::crea_socio_country($usuario, $clave, $CorreoElectronico, $Documento, $IDSocioExterno, $UsuarioApp, $Nombre, $Apellido, $Accion, $cambiar_clave);

                    $AccionPadre = $resultado_autentica["data"]["partner_data"]["id_derecho"];
                    $Consanguinidad = $resultado_autentica["data"]["partner_data"]["id_derecho"];
                    $Consecutivo = $resultado_autentica["data"]["partner_data"]["consecutivo"];
                    $Digito = $resultado_autentica["data"]["partner_data"]["digito"];
                    $Accion = $resultado_autentica["data"]["partner_data"]["numero_socio"];
                    $IDSocioPadre = $resultado_autentica["data"]["partner_data"]["id_socio_padre"];
                    $IDSocioTitular = $resultado_autentica["data"]["partner_data"]["id_socio_titular"];

                    $GrupoFamiliar = $resultado_autentica["data"]["partner_group"];
                    foreach ($GrupoFamiliar as $datos_grupo) {
                        $CorreoElectronico = "";
                        $Documento = $datos_grupo["numero_socio"];
                        $IDSocioExterno = $datos_grupo["id_socio"];
                        $UsuarioApp = $datos_grupo["numero_socio"];
                        $Consanguinidad = $datos_grupo["consanguinidad"];
                        $AccionPadre = $datos_grupo["id_derecho"];
                        $Accion = $datos_grupo["numero_socio"];
                        $Nombre = $datos_grupo["persona"]["primer_nombre"];
                        $Apellido = $datos_grupo["persona"]["primer_apellido"] . " " . $datos_grupo["data"]["persona"]["segundo_apellido"];

                        $Accion = $datos_grupo["numero_socio"];
                        $usuario = "sinusuario";
                        $clave = "sinclave";
                        $crear_beneficiario = SIMWebServiceApp::crea_socio_country($usuario, $clave, $CorreoElectronico, $Documento, $IDSocioExterno, $UsuarioApp, $Nombre, $Apellido, $Accion, $cambiar_clave, $Consanguinidad, $AccionPadre);
                    }
                }
            }
            return $estado_autentica;
        }    

        public function crea_socio_country($usuario, $clave, $CorreoElectronico, $Documento, $IDSocioExterno, $UsuarioApp, $Nombre, $Apellido, $Accion, $cambiar_clave, $Consanguinidad, $AccionPadre)
        {
            $dbo = &SIMDB::get();
            if ((int) $IDSocioExterno <= 0) {
                $IDSocioExterno = $resultado_autentica["data"]["id"];
            }

            //Si el Socio existe lo creo de los contrario lo actualizo
            $IDClub = 44;
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocioSistemaExterno = '" . $IDSocioExterno . "' and IDClub = '" . $IDClub . "'");

            if ($Consanguinidad == "TITULAR") {
                $Accion = $AccionPadre;
                $AccionPadre = "";
            }

            if (empty($id_socio)) :
                $parametros_codigo_barras = $Documento;
                $CodigoBarras = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $id);
                if ($usuario == "sinusuario" && $clave == "sinclave") {
                    $sql_crea_socio = "Insert into Socio (IDClub, IDSocioSistemaExterno, IDEstadoSocio, Accion, AccionPadre, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, Telefono, Direccion,NumeroInvitados, NumeroAccesos, CodigoBarras,ClaveSistemaExterno)
                        Values ('" . $IDClub . "','" . $IDSocioExterno . "',1,'" . $Accion . "','" . $AccionPadre . "','','" . $Nombre . "','" . $Apellido . "','','" . $Documento . "',
                        '" . $CorreoElectronico . "',NOW(),'WebServiceCountry','Socio','S','" . $cambiar_clave . "','','','100','100','" . $CodigoBarras . "','" . base64_encode($clave) . "')";
                } else {
                    $sql_crea_socio = "Insert into Socio (IDClub, IDSocioSistemaExterno, IDEstadoSocio, Accion, AccionPadre, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, Telefono, Direccion,NumeroInvitados, NumeroAccesos, CodigoBarras,ClaveSistemaExterno)
                        Values ('" . $IDClub . "','" . $IDSocioExterno . "',1,'" . $Accion . "','" . $AccionPadre . "','','" . $Nombre . "','" . $Apellido . "','','" . $Documento . "',
                        '" . $usuario . "',sha1('" . $clave . "'), '" . $CorreoElectronico . "',NOW(),'WebServiceCountry','Socio','S','" . $cambiar_clave . "','','','100','100','" . $CodigoBarras . "','" . base64_encode($clave) . "')";
                }
                $dbo->query($sql_crea_socio);
                //echo "<br>" . $sql_crea_socio;
                $estado_autentica = 1;
            else :

                //Validacion especial con este usuario que no quiere que aparezca el nombre del esposo sino el de ella
                if ($usuario == "homebrunner@gmail.com") {
                    $Nombre = "Ana Maria";
                    $Apellido = "de Brunner";
                }

                if ($usuario == "sinusuario" && $clave == "sinclave") {
                    $sql_actualiza_socio = "Update Socio Set
                                                IDSocioSistemaExterno = '" . $IDSocioExterno . "',
                                                IDEstadoSocio = 1,
                                                Accion = '" . $Accion . "',
                                                AccionPadre = '" . $AccionPadre . "',
                                                Genero = '',
                                                Nombre = '" . $Nombre . "',
                                                Apellido = '" . $Apellido . "',
                                                FechaNacimiento = '',
                                                NumeroDocumento = '" . $Documento . "',
                                                CorreoElectronico = '" . $CorreoElectronico . "',
                                                FechaTrEd = 'NOW()',
                                                UsuarioTrEd = 'Webservice Country',
                                                TipoSocio = 'Socio',
                                                PermiteReservar = 'S',
                                                CambioClave = '" . $cambiar_clave . "',
                                                Telefono = '',
                                                Direccion = '',
                                                NumeroInvitados = '100',
                                                NumeroAccesos = '100',
                                                ClaveSistemaExterno = '" . base64_encode($clave) . "'
                                                Where IDSocio = '" . $id_socio . "'";
                } else {
                    $sql_actualiza_socio = "Update Socio Set
                                                    IDSocioSistemaExterno = '" . $IDSocioExterno . "',
                                                    IDEstadoSocio = 1,
                                                    Accion = '" . $Accion . "',
                                                    AccionPadre = '" . $AccionPadre . "',
                                                    Genero = '',
                                                    Nombre = '" . $Nombre . "',
                                                    Apellido = '" . $Apellido . "',
                                                    FechaNacimiento = '',
                                                    NumeroDocumento = '" . $Documento . "',
                                                    Email = '" . $usuario . "',
                                                    Clave = sha1('" . $clave . "'),
                                                    CorreoElectronico = '" . $CorreoElectronico . "',
                                                    FechaTrEd = 'NOW()',
                                                    UsuarioTrEd = 'Webservice Country',
                                                    TipoSocio = 'Socio',
                                                    PermiteReservar = 'S',
                                                    CambioClave = '" . $cambiar_clave . "',
                                                    Telefono = '',
                                                    Direccion = '',
                                                    NumeroInvitados = '100',
                                                    NumeroAccesos = '100',
                                                    ClaveSistemaExterno = '" . base64_encode($clave) . "'
                                                    Where IDSocio = '" . $id_socio . "'";
                }
                $dbo->query($sql_actualiza_socio);
            //echo "<br>" . $sql_actualiza_socio;
            endif;
            return $estado_autentica = 1;
        }

    // WebService Country CLUB
    }