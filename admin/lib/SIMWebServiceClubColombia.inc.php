<?php

    class SIMWebServiceClubColombia
    {
        //WEBSERVICE CLUB COLOMBIA
        public function get_datos_usuario_club_colombia($Token, $email = "", $clave = "")
        {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/user_information.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err) :
                $resultado_usuario = json_decode($response, true);
                $datos_usuario = json_decode($resultado_usuario["usuario"], true);
                SIMWebServiceClubColombia::actualiza_datos_colombia($datos_usuario, $email, $clave);
                return "ok";
            else :
                return "no";
            endif;
        }

        public function actualiza_datos_colombia($datos_usuario, $email = "", $clave = "")
        {
            $dbo = &SIMDB::get();
            $IDClub = 38;
            $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocioSistemaExterno = '" . $datos_usuario["id"] . "' and IDClub = '" . $IDClub . "'");
            if (empty($datos_usuario["email"])) :
                $cambiar_clave = "S";
            else :
                $cambiar_clave = "N";
            endif;

            if (empty($email)) {
                $email = $datos_usuario["documento"];
            }

            if (empty($clave)) {
                $clave = $datos_usuario["documento"];
            }

            if (empty($id_socio)) :
                $parametros_codigo_barras = $datos_usuario["documento"] . ";";
                $CodigoBarras = SIMUtil::generar_codigo_barras($parametros_codigo_barras, $id);
                $sql_crea_socio = "Insert into Socio (IDClub, IDSocioSistemaExterno, IDEstadoSocio, Accion, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, Telefono, Direccion,NumeroInvitados, NumeroAccesos, CodigoBarras,ClaveSistemaExterno,FotoActualizadaSocio)
                                                            Values ('38','" . $datos_usuario["id"] . "',1,'" . $datos_usuario["accion"] . "','" . $datos_usuario["sexo"] . "','" . $datos_usuario["nombre"] . "','','" . $datos_usuario["fecha_nacimiento"] . "','" . $datos_usuario["documento"] . "',
                                                            '" . $email . "',sha1('" . $clave . "'), '" . $datos_usuario["email"] . "',NOW(),'WebServiceClubColombia','Socio','S','" . $cambiar_clave . "','" . $datos_usuario["telefono_residencia"] . "','" . $datos_usuario["direccion_residencia"] . "','100','100','" . $CodigoBarras . "','" . base64_encode($clave) . "','S')";
                $dbo->query($sql_crea_socio);
            //echo "<br>" . $sql_crea_socio;
            else :
                $sql_actualiza_socio = "Update Socio Set
                                                        IDSocioSistemaExterno = '" . $datos_usuario["id"] . "',
                                                        IDEstadoSocio = 1,
                                                        Accion = '" . $datos_usuario["accion"] . "',
                                                        Genero = '" . $datos_usuario["sexo"] . "',
                                                        Nombre = '" . $datos_usuario["nombre"] . "',
                                                        Apellido = '',
                                                        FechaNacimiento = '" . $datos_usuario["fecha_nacimiento"] . "',
                                                        NumeroDocumento = '" . $datos_usuario["documento"] . "',
                                                        Email = '" . $email . "',
                                                        Clave = sha1('" . $clave . "'),
                                                        CorreoElectronico = '" . $datos_usuario["email"] . "',
                                                        FechaTrEd = 'NOW()',
                                                        UsuarioTrEd = 'Webservice Colombia',
                                                        TipoSocio = 'Socio',
                                                        PermiteReservar = 'S',
                                                        CambioClave = '" . $cambiar_clave . "',
                                                        Telefono = '" . $datos_usuario["telefono_residencia"] . "',
                                                        NumeroInvitados = '100',
                                                        NumeroAccesos = '100',
                                                        ClaveSistemaExterno = '" . base64_encode($clave) . "'
                                                        Where IDSocio = '" . $id_socio . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_actualiza_socio);
            //echo "<br>" . $sql_actualiza_socio;
            endif;
        }

        public function set_cambio_clave_colombia($Token, $NuevaClave)
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/update_password.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token . "&password=" . $NuevaClave,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err) :
                //print_r($response);
                return "ok";
            else :
                return "no";
            endif;
        }

        public function set_recordar_clave_colombia($Token, $Email, $NuevaClave)
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/reset_password.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token . "&email=" . $Email . "&password=" . $NuevaClave,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err) :
                //print_r($response);
                return "ok";
            else :
                return "no";
            endif;
        }

        public function set_email_colombia($Token, $Correo)
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/update_email.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token . "&email=" . $Correo,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err) :
                //print_r($response);
                return "ok";
            else :
                return "no";
            endif;
        }

        public function get_socios_colombia($Token)
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.clubcolombia.org/api/users_information.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=jwt_bearer&access_token=" . $Token,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded",
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if (!$err) :
                //print_r($response);
                return $response;
            else :
                return "no";
            endif;
        }

        public function obtener_token_colombia($Usuario, $Clave)
        {
            $post_data = array(
                'grant_type' => 'password',
                'client_id' => 'clubcolo_app',
                'client_secret' => '20ClUbC0l0mB1@18P@5SCl13nT',
                'username' => $Usuario,
                'password' => $Clave,
            );

            $crl = curl_init();
            curl_setopt($crl, CURLOPT_POST, true);
            curl_setopt($crl, CURLOPT_URL, 'https://www.clubcolombia.org/api/login.php');
            curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);

            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            $rest = curl_exec($crl);
            if ($rest === false) {
                return curl_error($crl);
            }
            curl_close($crl);
            //print_r($rest);
            $resultado_token = json_decode($rest, true);
            $token = $resultado_token["access_token"];
            return $token;
        }

    //FIN WEBSERVICE CLUB COLOMBIA
    }