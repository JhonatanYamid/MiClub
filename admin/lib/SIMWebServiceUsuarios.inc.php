 <?php

    class SIMWebServiceUsuarios
    {
        public function valida_socio($email, $Clave, $IDClub, $AppVersion, $Data = "", $Identificador = "", $Modelo = "")
        {


            $dbo = &SIMDB::get();

            if ($AppVersion >= 31 && !empty($Data)) {
                $valornonce = substr($Data, 0, 48);
                $valorencrip = substr($Data, 48);
                $param['key'] = KEY_API;
                $param['chiper'] = $valorencrip;
                $param['nonce'] = $valornonce;
                $result_decrypt = SIMUtil::decryptSodium($param);
                if ($result_decrypt["decryptedText"] == "nodecrypt") {
                    $respuesta["message"] = "ENCRIPT. No";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                } else {
                    $result_decrypt["decryptedText"];
                    $array_datos = json_decode($result_decrypt["decryptedText"]);
                    $email = $array_datos->ax;
                    $Clave = $array_datos->az;
                }
            }

            if (!empty($email) && !empty($Clave)) {
                $foto = "";
                $foto_cod_barras = "";

                $sql_campos_carne = "SELECT IDCampoCarne,Nombre,CampoTabla From CampoCarne Where 1";
                $r_campo_carne = $dbo->query($sql_campos_carne);
                while ($row_campo_carne = $dbo->fetchArray($r_campo_carne)) {
                    $array_campo_carne[$row_campo_carne["IDCampoCarne"]] = $row_campo_carne;
                }

                //Caso especial hacienda fontanar que enviaron las claves en la codificación propia de su sistema
                if ($IDClub == "18") :
                    $llave_fija = "63380fcfe2bf3c3d2cb3ec089c3c521b";
                    $clave_especial = md5($email . md5($llave_fija . "-" . $Clave));
                    //$clave_especial =  md5($email."_".md5($Clave));
                    $otra_condicion_clave = " or Clave = '" . $clave_especial . "'";
                endif;

                //LOGIN PUERTO AZUL DESDE WEBSERVICES
                if ($IDClub == 130) :


                    require LIBDIR  . "SIMWebServicePuertoAzul.inc.php";

                    //Guardamos la clave encriptada para recuperarla y generar el token con uno nuevo en la webview estado de cuenta 
                    $password = "$Clave";
                    $contraseña = "jlospino";
                    $pass_encriptada = openssl_encrypt($password, 'aes-256-ecb', $contraseña);



                    $resultado = SIMWebServicePuertoAzul::App_AutenticarUser($email, $Clave);
                    $datos = json_decode($resultado, true);
                    $token = $datos[access_token];
                    $token_autogestion = $datos[access_token];
                    //No es socios verifico si es empleado
                    if (empty($token)) :
                        $result_funcionario = SIMWebServiceUsuarios::valida_usuario_web($email, $Clave, $IDClub, "", $AppVersion);
                        if ($result_funcionario["message"] == SIMUtil::get_traduccion('', '', 'ok', LANG)) :
                            return $result_funcionario;
                        endif;
                    endif;

                    //SI ES SOCIO ENTONCES PROCEDEMOS
                    if ($datos[access_token]) {
                        //PERFIL DEL SOCIO
                        $resultado1 = SIMWebServicePuertoAzul::App_ConsultarPerfil($token);
                        $datos1 = json_decode($resultado1, true);
                        $nombre = $datos1[NOMBRES];

                        $nombre = str_replace("'", " ", "$nombre");
                        $apellido = $datos1[APELLIDOS];
                        $apellido = str_replace("'", " ", "$apellido");

                        $tipo_doc = $datos1[PASAPORTE];
                        $identificacion = $datos1[CEDULA];

                        $accion = $datos1[ACCION];
                        $numeroderecho = $datos1[NUMERO_ACCION];
                        $codigo = $datos1[IDUNICO];
                        $fecha_nacimiento = $data->fecha_nacimiento;
                        $CorreoElectronico = $datos1[CORREO];
                        $telefono = $datos1[CELULAR];
                        $email_autogestion = $datos1[CORREO_AUTOGESTION];
                        $parentezco = $datos1[PARENTESCO];
                        $moroso = $datos1[MOROSIDAD]; //SIN DEUDAS = 0 - CON DEUDAS= 1
                        if ($moroso == "0") :
                            $moroso = "S";
                        else :
                            $moroso = "N";
                        endif;




                        if ($parentezco == "TITULAR" or $parentezco == "CONYUGE") :
                            $accionpadre = $accion;
                            $nacimiento = "1992-01-01";
                            $invitados = "10";

                        else :

                            $accionpadre = "";
                            $nacimiento = "1992-01-01";
                            $invitados = "4";

                        endif;

                        $pasaporte = $datos1[PASAPORTE];
                        $rif = $datos1[RIF];

                        if (isset($pasaporte)) :

                            if (empty($identificacion)) :
                                $identificacion = $pasaporte;
                            endif;
                            if (empty($pasaporte) and empty($identificacion)) :
                                $identificacion = $rif;
                            endif;

                        else :
                            if (!empty($rif)) :
                                $identificacion = $rif;
                            endif;


                        endif;

                        $validacion_parentezco = "SELECT * FROM TipoSocio WHERE  Nombre='$parentezco' LIMIT 1";
                        $validacion_final = $dbo->query($validacion_parentezco);

                        $parentezco = $dbo->fetchArray($validacion_final);
                        $idparentezco = $parentezco["IDTipoSocio"];
                        if (empty($parentezco["IDTipoSocio"])) :
                            $idparentezco =  "";
                        endif;


                        if (ctype_digit($identificacion)) {
                            $identificacion = ltrim($identificacion, "0");
                        }
                        if (ctype_digit($accion)) {
                            $accion = ltrim($accion, "0");
                        }
                        if (ctype_digit($accionpadre)) {
                            $accionpadre = ltrim($accionpadre, "0");
                        }
                        if (ctype_digit($codigo)) {
                            $codigo = ltrim($codigo, "0");
                        }


                        //VERIFICAMOS QUE NO ESTE GUARDADO YA
                        $datos_socio = "SELECT count(*) as total FROM Socio WHERE NumeroDocumento ='$identificacion' and IDClub=$IDClub and NumeroDerecho='$codigo' LIMIT 1";
                        $datos = $dbo->query($datos_socio);

                        $row = $dbo->fetchArray($datos);
                        $total = $row["total"];

                        if ($total == 0) {
                            //AHORA VALIDAMOS DE NUEVO SI ENTRE LOS MIEMBROS NO SE GUARDO EL SOCIO QUE INICIO SESION

                            /*
                            $datos_socio11 = "SELECT count(*) as total FROM Socio WHERE NumeroDocumento =$identificacion and IDClub=$IDClub and NumeroDerecho=$codigo LIMIT 1";
                            $datos1 = $dbo->query($datos_socio11);
                            $row1 = $dbo->fetchArray($datos1);
                            $total1 = $row1["total"];
                            */


                            //if ($total1 == 0) :

                            $parametros_codigo_qr = $identificacion;
                            $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);


                            $sql_crea_socio = "INSERT INTO Socio (IDClub, IDParentesco,IDEstadoSocio, Accion,AccionPadre, NumeroDerecho, Nombre, Apellido, Telefono, TokenValleArribaAthletic, NumeroDocumento, FechaNacimiento, Email, Clave, ClaveAES, CodigoQR, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil, FotoActualizadaSocio,NumeroInvitados, NumeroAccesos)      Values ('$IDClub','$idparentezco',1,'$accion','$accionpadre','$codigo','$nombre','$apellido','$telefono','$token_autogestion','$identificacion','$nacimiento','$email', sha1('" . $Clave . "'),'$pass_encriptada','" . $frm["CodigoQR"] . "' ,'$CorreoElectronico',NOW(),'SIMWebServicePuertoAzul','Socio','$moroso','N','N','N','$invitados','$invitados')";
                            $dbo->query($sql_crea_socio);
                            $IDSocio = $dbo->lastID();
                            //endif;




                            //SI NO ESTA, CONSULTAMOS LOS FAMILIARES

                            $resultado2 = SIMWebServicePuertoAzul::App_ConsultarPerfilFamiliares($token);
                            $datos_familiares = json_decode($resultado2, true);

                            foreach ($datos_familiares[FAMILIARES] as $socio => $datos2) :


                                $nombre1 = $datos2[NOMBRES];
                                $nombre1 = str_replace("'", " ", "$nombre1");
                                $apellido1 = $datos2[APELLIDOS];
                                $apellido1 = str_replace("'", " ", "$apellido1");

                                $tipo_doc1 = $datos2[PASAPORTE];
                                $identificacion1 = $datos2[CEDULA];

                                $accion1 = $datos2[ACCION];
                                $numeroderecho1 = $datos2[NUMERO_ACCION];
                                $codigo1 = $datos2[IDUNICO];

                                $email1 = $datos2[CORREO];
                                $telefono1 = $datos2[CELULAR];
                                $email_autogestion1 = $datos2[CORREO_AUTOGESTION];
                                $parentezco1 = $datos2[PARENTESCO];
                                $moroso1 = $datos2[MOROSIDAD]; //SIN DEUDAS = 0 - CON DEUDAS= 1
                                if ($moroso1 == "0") :
                                    $moroso1 = "S";
                                else :
                                    $moroso1 = "N";
                                endif;


                                if ($parentezco1 == "TITULAR" or $parentezco1 == "CONYUGE") :
                                    $accionpadre1 = $accion1;
                                    $nacimiento1 = "1992-01-01";
                                    $invitados1 = "10";
                                else :
                                    $accionpadre1 = "";
                                    $nacimiento1 = "1992-01-01";
                                    $invitados1 = "4";
                                endif;


                                $pasaporte1 = $datos2[PASAPORTE];
                                $rif1 = $datos2[RIF];

                                if (isset($pasaporte1)) :

                                    if (empty($identificacion1)) :
                                        $identificacion1 = $pasaporte1;
                                    endif;
                                    if (empty($pasaporte1) and empty($identificacion1)) :
                                        $identificacion1 = $rif1;
                                    endif;

                                else :
                                    if (!empty($rif1)) :
                                        $identificacion1 = $rif1;
                                    endif;

                                endif;


                                $validacion_parentezco1 = "SELECT * FROM TipoSocio WHERE  Nombre='" . $parentezco1 . "' LIMIT 1";
                                $validacion_final1 = $dbo->query($validacion_parentezco1);

                                $parentezco1 = $dbo->fetchArray($validacion_final1);
                                $idparentezco1 = $parentezco1["IDTipoSocio"];
                                if (empty($parentezco1["IDTipoSocio"])) :
                                    $idparentezco1 =  "";
                                endif;



                                //SI NO ESTAN GUARDADOS SE GUARDAN
                                $datos_socio11 = "SELECT count(*) as total FROM Socio WHERE NumeroDocumento='$identificacion1' and IDClub=$IDClub and NumeroDerecho='$codigo1' LIMIT 1";
                                $datos1 = $dbo->query($datos_socio11);

                                $row1 = $dbo->fetchArray($datos1);
                                $total1 = $row1["total"];

                                if ($total1 == 0) :
                                    $validacion = "SELECT count(*) as total FROM Socio WHERE  IDClub=$IDClub and NumeroDerecho='$codigo1' LIMIT 1";
                                    $validacion_final = $dbo->query($validacion);

                                    $cantidad = $dbo->fetchArray($validacion_final);
                                    $cantidad["total"];

                                    if ($cantidad["total"] == 0) {
                                        $parametros_codigo_qr = $identificacion;
                                        $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);

                                        $sql_crea_familiares = "INSERT INTO Socio (IDClub, IDParentesco, IDEstadoSocio,Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, Telefono, TokenValleArribaAthletic, NumeroDocumento,FechaNacimiento, Email, Clave,ClaveAES, CodigoQR, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil, FotoActualizadaSocio,NumeroInvitados, NumeroAccesos)
                                               Values ('$IDClub','$idparentezco1',1,'$accion1','$accionpadre1','$codigo1','$nombre1','$apellido1','$telefono1','$token_autogestion','$identificacion1','$nacimiento1','$identificacion1', sha1('" . $Clave . "'),'$pass_encriptada','" . $frm["CodigoQR"] . "','$email_autogestion1',NOW(),'SIMWebServicePuertoAzul','Socio','$moroso1','N','N','N','$invitados1','$invitados1')";
                                        $dbo->query($sql_crea_familiares);
                                    }
                                endif;

                            endforeach;



                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        } else {

                            $datos_socio = "SELECT  * FROM Socio WHERE  IDClub = '" . $IDClub . "' and NumeroDerecho='$codigo' LIMIT 1";
                            $datos = $dbo->query($datos_socio);

                            $row = $dbo->fetchArray($datos);
                            $IDSocioCreado = $row["IDSocio"];

                            $parametros_codigo_qr = $identificacion;
                            $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                            $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' , NumeroInvitados='" . $invitados . "',  NumeroAccesos='" . $invitados . "', IDParentesco='" . $idparentezco . "', TokenValleArribaAthletic = '$token_autogestion', Accion='" . $accion . "',AccionPadre='" . $accionpadre . "', NumeroDerecho='" . $codigo . "', Nombre='" . $nombre . "',Apellido='" . $apellido . "',FechaNacimiento='" . $nacimiento . "',NumeroDocumento='" . $identificacion . "', Email='" . $email . "', Clave = sha1('" . $Clave . "') , Telefono='" . $telefono . "',CorreoElectronico='" . $email_autogestion . "',PermiteReservar='" . $moroso . "', ClaveAES='" . $pass_encriptada . "'  Where IDSocio = '" . $IDSocioCreado . "'");


                            $IDSocio = $IDSocioCreado;

                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        }
                    } else {


                        $respuesta["message"] = "Lo sentimos, no se reconoce como miembro de la empresa";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }



                endif;

                //LOGIN VALLE ARRIBA ATHETIC DESDE WEBSERVICES
                if ($IDClub == 239) :

                    require LIBDIR  . "SIMWebServiceValleArribaAthetic.inc.php";

                    $resultado = SIMWebServiceValleArribaAthetic::App_AutenticarUser($email, $Clave);
                    $datos = json_decode($resultado, true);
                    $token = $datos[access_token];

                    //No es socios verifico si es empleado
                    if (empty($token)) :
                        $result_funcionario = SIMWebServiceUsuarios::valida_usuario_web($email, $Clave, $IDClub, "", $AppVersion);
                        if ($result_funcionario["message"] == SIMUtil::get_traduccion('', '', 'ok', LANG)) :
                            return $result_funcionario;
                        endif;
                    endif;

                    //SI ES SOCIO ENTONCES PROCEDEMOS
                    if ($datos[access_token]) {
                        //PERFIL DEL SOCIO
                        $resultado1 = SIMWebServiceValleArribaAthetic::App_ConsultarPerfil($token);
                        $datos1 = json_decode($resultado1, true);
                        $nombre = $datos1[NOMBRES];

                        $nombre = str_replace("'", " ", "$nombre");
                        $apellido = $datos1[APELLIDOS];
                        $apellido = str_replace("'", " ", "$apellido");

                        $tipo_doc = $datos1[PASAPORTE];
                        $identificacion = $datos1[CEDULA];
                        $token = $datos[access_token];
                        $accion = $datos1[ACCION];
                        $numeroderecho = $datos1[NUMERO_ACCION];
                        $codigo = $datos1[IDUNICO];
                        $fecha_nacimiento = $data->fecha_nacimiento;
                        $CorreoElectronico = $datos1[CORREO];
                        $telefono = $datos1[CELULAR];
                        $email_autogestion = $datos1[CORREO_AUTOGESTION];
                        $parentezco = $datos1[PARENTESCO];
                        $moroso = $datos1[MOROSIDAD]; //SIN DEUDAS = 0 - CON DEUDAS= 1
                        if ($moroso == "0") :
                            $moroso = "S";
                        else :
                            $moroso = "S"; //DEBE IR N PERO SE DEJO S TEMPORALMENTE A PETICION DE LOS DE VACC
                        endif;




                        if ($parentezco == "TITULAR" or $parentezco == "CONYUGE") :
                            $accionpadre = $accion;
                            $nacimiento = "1992-01-01";
                            $invitados = "10";

                        else :

                            $accionpadre = $accion;
                            $nacimiento = "1992-01-01";
                            $invitados = "4";

                        endif;

                        $pasaporte = $datos1[PASAPORTE];
                        $rif = $datos1[RIF];

                        if (isset($pasaporte)) :

                            if (empty($identificacion)) :
                                $identificacion = $pasaporte;
                            endif;
                            if (empty($pasaporte) and empty($identificacion)) :
                                $identificacion = $rif;
                            endif;

                        else :
                            if (!empty($rif)) :
                                $identificacion = $rif;
                            endif;


                        endif;

                        $validacion_parentezco = "SELECT * FROM TipoSocio WHERE  Nombre='$parentezco' LIMIT 1";
                        $validacion_final = $dbo->query($validacion_parentezco);

                        $parentezco = $dbo->fetchArray($validacion_final);
                        $idparentezco = $parentezco["IDTipoSocio"];
                        if (empty($parentezco["IDTipoSocio"])) :
                            $idparentezco =  "";
                        endif;


                        if (ctype_digit($identificacion)) {
                            $identificacion = ltrim($identificacion, "0");
                        }
                        if (ctype_digit($accion)) {
                            $accion = ltrim($accion, "0");
                        }
                        if (ctype_digit($accionpadre)) {
                            $accionpadre = ltrim($accionpadre, "0");
                        }
                        if (ctype_digit($codigo)) {
                            $codigo = ltrim($codigo, "0");
                        }

                        $token = $datos[access_token];
                        //VERIFICAMOS QUE NO ESTE GUARDADO YA
                        $datos_socio = "SELECT count(*) as total FROM Socio WHERE NumeroDocumento ='$identificacion' and IDClub=$IDClub and NumeroDerecho='$codigo' LIMIT 1";
                        $datos = $dbo->query($datos_socio);

                        $row = $dbo->fetchArray($datos);
                        $total = $row["total"];

                        if ($total == 0) {
                            //AHORA VALIDAMOS DE NUEVO SI ENTRE LOS MIEMBROS NO SE GUARDO EL SOCIO QUE INICIO SESION

                            /*
                            $datos_socio11 = "SELECT count(*) as total FROM Socio WHERE NumeroDocumento =$identificacion and IDClub=$IDClub and NumeroDerecho=$codigo LIMIT 1";
                            $datos1 = $dbo->query($datos_socio11);
                            $row1 = $dbo->fetchArray($datos1);
                            $total1 = $row1["total"];
                            */


                            //if ($total1 == 0) :

                            $parametros_codigo_qr = $identificacion;
                            $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);


                            $sql_crea_socio = "INSERT INTO Socio (IDClub, IDParentesco,IDEstadoSocio, Accion,AccionPadre, NumeroDerecho, Nombre, Apellido, Telefono, TokenValleArribaAthletic, NumeroDocumento, FechaNacimiento, Email, Clave, CodigoQR, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil, FotoActualizadaSocio,NumeroInvitados, NumeroAccesos)      Values ('$IDClub','$idparentezco',1,'$accion','$accionpadre','$codigo','$nombre','$apellido','$telefono','$token','$identificacion','$nacimiento','$email', sha1('" . $Clave . "'),'" . $frm["CodigoQR"] . "' ,'$CorreoElectronico',NOW(),'SIMWebServiceValleArribaAthletic','Socio','$moroso','N','N','N','$invitados','$invitados')";
                            $dbo->query($sql_crea_socio);
                            $IDSocio = $dbo->lastID();
                            //endif;




                            //SI NO ESTA, CONSULTAMOS LOS FAMILIARES

                            $resultado2 = SIMWebServiceValleArribaAthetic::App_ConsultarPerfilFamiliares($token);
                            $datos_familiares = json_decode($resultado2, true);

                            foreach ($datos_familiares[FAMILIARES] as $socio => $datos2) :


                                $nombre1 = $datos2[NOMBRES];
                                $nombre1 = str_replace("'", " ", "$nombre1");
                                $apellido1 = $datos2[APELLIDOS];
                                $apellido1 = str_replace("'", " ", "$apellido1");

                                $tipo_doc1 = $datos2[PASAPORTE];
                                $identificacion1 = $datos2[CEDULA];

                                $accion1 = $datos2[ACCION];
                                $numeroderecho1 = $datos2[NUMERO_ACCION];
                                $codigo1 = $datos2[IDUNICO];

                                $email1 = $datos2[CORREO];
                                $telefono1 = $datos2[CELULAR];
                                $email_autogestion1 = $datos2[CORREO_AUTOGESTION];
                                $parentezco1 = $datos2[PARENTESCO];
                                $moroso1 = $datos2[MOROSIDAD]; //SIN DEUDAS = 0 - CON DEUDAS= 1
                                if ($moroso1 == "0") :
                                    $moroso1 = "S";
                                else :
                                    $moroso1 = "S"; //DEBE IR EN N PERO VAAC LO PIDIO EN N TEMPORALMENTE
                                endif;


                                if ($parentezco1 == "TITULAR" or $parentezco1 == "CONYUGE") :
                                    $accionpadre1 = $accion1;
                                    $nacimiento1 = "1992-01-01";
                                    $invitados1 = "10";
                                else :
                                    $accionpadre1 = $accion1;
                                    $nacimiento1 = "1992-01-01";
                                    $invitados1 = "4";
                                endif;


                                $pasaporte1 = $datos2[PASAPORTE];
                                $rif1 = $datos2[RIF];

                                if (isset($pasaporte1)) :

                                    if (empty($identificacion1)) :
                                        $identificacion1 = $pasaporte1;
                                    endif;
                                    if (empty($pasaporte1) and empty($identificacion1)) :
                                        $identificacion1 = $rif1;
                                    endif;

                                else :
                                    if (!empty($rif1)) :
                                        $identificacion1 = $rif1;
                                    endif;

                                endif;
                                // Eliminamos los 0 a las izquierda
                                if (ctype_digit($identificacion1)) {
                                    $identificacion1 = ltrim($identificacion1, "0");
                                }
                                if (ctype_digit($accion1)) {
                                    $accion1 = ltrim($accion1, "0");
                                }
                                if (ctype_digit($accionpadre)) {
                                    $accionpadre1 = ltrim($accionpadre1, "0");
                                }
                                if (ctype_digit($codigo1)) {
                                    $codigo1 = ltrim($codigo1, "0");
                                }

                                $validacion_parentezco1 = "SELECT * FROM TipoSocio WHERE  Nombre='" . $parentezco1 . "' LIMIT 1";
                                $validacion_final1 = $dbo->query($validacion_parentezco1);

                                $parentezco1 = $dbo->fetchArray($validacion_final1);
                                $idparentezco1 = $parentezco1["IDTipoSocio"];
                                if (empty($parentezco1["IDTipoSocio"])) :
                                    $idparentezco1 =  "";
                                endif;



                                //SI NO ESTAN GUARDADOS SE GUARDAN
                                $datos_socio11 = "SELECT count(*) as total FROM Socio WHERE NumeroDocumento='$identificacion1' and IDClub=$IDClub and NumeroDerecho='$codigo1' LIMIT 1";
                                $datos1 = $dbo->query($datos_socio11);

                                $row1 = $dbo->fetchArray($datos1);
                                $total1 = $row1["total"];

                                if ($total1 == 0) :
                                    $validacion = "SELECT count(*) as total FROM Socio WHERE  IDClub=$IDClub and NumeroDerecho='$codigo1' LIMIT 1";
                                    $validacion_final = $dbo->query($validacion);

                                    $cantidad = $dbo->fetchArray($validacion_final);
                                    $cantidad["total"];

                                    if ($cantidad["total"] == 0) {
                                        $parametros_codigo_qr = $identificacion;
                                        $token1 = "";
                                        $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);



                                        $sql_crea_familiares = "INSERT INTO Socio (IDClub, IDParentesco, IDEstadoSocio,Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, Telefono, TokenValleArribaAthletic, NumeroDocumento,FechaNacimiento, Email, Clave,CodigoQR, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil, FotoActualizadaSocio,NumeroInvitados, NumeroAccesos)
                                               Values ('$IDClub','$idparentezco1',1,'$accion1','$accionpadre1','$codigo1','$nombre1','$apellido1','$telefono1','$token1','$identificacion1','$nacimiento1','$identificacion1', sha1('" . $Clave . "'),'" . $frm["CodigoQR"] . "','$email_autogestion1',NOW(),'SIMWebServiceValleArribaAthletic','Socio','$moroso1','N','N','N','$invitados1','$invitados1')";
                                        $dbo->query($sql_crea_familiares);
                                    }
                                endif;

                            endforeach;



                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        } else {

                            $datos_socio = "SELECT  * FROM Socio WHERE  IDClub = '" . $IDClub . "' and NumeroDerecho='$codigo' LIMIT 1";
                            $datos = $dbo->query($datos_socio);

                            $row = $dbo->fetchArray($datos);
                            $IDSocioCreado = $row["IDSocio"];

                            $parametros_codigo_qr = $identificacion;
                            $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                            $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' , NumeroInvitados='" . $invitados . "',  NumeroAccesos='" . $invitados . "', IDParentesco='" . $idparentezco . "', TokenValleArribaAthletic = '" . $token . "', Accion='" . $accion . "',AccionPadre='" . $accionpadre . "', NumeroDerecho='" . $codigo . "', Nombre='" . $nombre . "',Apellido='" . $apellido . "',FechaNacimiento='" . $nacimiento . "',NumeroDocumento='" . $identificacion . "', Email='" . $email . "', Clave = sha1('" . $Clave . "') , Telefono='" . $telefono . "',CorreoElectronico='" . $email_autogestion . "',PermiteReservar='" . $moroso . "'  Where IDSocio = '" . $IDSocioCreado . "'");


                            $IDSocio = $IDSocioCreado;

                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        }
                    } else {


                        $respuesta["message"] = "Lo sentimos, no se reconoce como miembro de la empresa";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }



                endif;


                //LOGIN COUNTRY CLUB MEDELLIN DESDE WEBSERVICES
                if ($IDClub == 227) :
                    require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

                    $resultado = SIMWebServiceCountryMedellin::App_AutenticarUser($email, $Clave);
                    $data = json_decode($resultado);
                    if ($data->token) {
                        $nombre = $data->nombre;
                        $identificacion = $data->NroIdentificacion;
                        $token = $data->token;
                        $accion = $data->codaccion;
                        $codigo = $data->codigo;
                        $fecha_nacimiento = $data->fecha_nacimiento;
                        $email = $data->correo;

                        $timestamp = strtotime($fecha_nacimiento);
                        $fecha_nacimiento = date("Y-m-d", $timestamp);



                        //ACTUALIZAMOS LA CONTRASEÑA POR SI NO ESTA ACTUALIZADA
                        $update_clave = $dbo->query("update Socio set Clave = sha1('" . $Clave . "') Where NumeroDocumento = '" . $identificacion . "' and IDClub= '" . $IDClub . "'");

                        $datos_socio = "SELECT count(*) as total, IDSocio FROM Socio WHERE NumeroDocumento ='$identificacion' and IDClub = '" . $IDClub . "' LIMIT 1";
                        $datos = $dbo->query($datos_socio);

                        while ($row = $dbo->fetchArray($datos)) {
                            $total = $row["total"];
                        }

                        if ($total == 0) {


                            require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";

                            $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarPerfil($token);
                            $data1 = json_decode($resultado1);
                            //datos perfil 

                            $dato =  $data1->perfil->estadoCivil;
                            $dato_fecha =   $data1->perfil->fechaAniversario;
                            $newDate = date("Y-m-d", strtotime($dato_fecha));
                            $dato1 =  $newDate;

                            $dato2 =  $data1->perfil->telefono;
                            $dato3 =  $data1->perfil->celular;
                            $dato4 =  $data1->perfil->direccion;
                            $dato5 =   $data1->perfil->profesion;
                            $dato6 =   $data1->perfil->nombreEmpresa;
                            $dato7 =  $data1->perfil->cargo;
                            $dato8 =  $data1->perfil->telefonoOficina;
                            $dato9 =  $data1->perfil->direccionOficina;
                            $dato10 =  $data1->perfil->direccionEnvio;
                            $dato11 =   $data1->perfil->correofacturacion;
                            $dato12 =   $data1->perfil->estudiante;
                            $imagenEnBase64 =   $data1->perfil->imagen;
                            if ($dato12 == "false") {
                                $dato12 = "No";
                            } else {
                                $dato12 = "Si";
                            }

                            /*                   
$contraseña="countrymedellin"; 
$pass=openssl_encrypt($Clave, 'aes-256-ecb', $contraseña);
     */

                            //Guardamos la foto de ellos en nuestro directorio 
                            $img = str_replace('data:image/png;base64,', '', $imagenEnBase64);
                            $img = str_replace(' ', '+', $img);
                            $data = base64_decode($img);
                            $file = SOCIO_DIR . uniqid() . '.png';
                            $success = file_put_contents($file, $data);


                            $foto = str_replace('/home/http/miclubapp/httpdocs/admin/../file/socio/', '', $file);


                            $sql_crea_socio = "INSERT INTO Socio (IDClub, IDEstadoSocio, Accion, NumeroDerecho, Nombre, FechaNacimiento, TokenCountryMedellin, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil, Foto, FotoActualizadaSocio)
                                               Values ('$IDClub',1,'$accion','$codigo','$nombre','$fecha_nacimiento','$token','$identificacion','$email', sha1('" . $Clave . "'),'$codigo',NOW(),'SIMWebServiceCountryMedellin','Socio','S','N','N','$foto','N')";
                            $dbo->query($sql_crea_socio);

                            $IDSocio = $dbo->lastID();

                            $parametros_codigo_qr = $identificacion;
                            $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                            $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");

                            $sqlinfosocio = "INSERT INTO SocioCampoEditarSocio ( IDCampoEditarSocio, IDSocio, Valor, FechaTrCr) VALUES  ('1235','$IDSocio', '$dato', NOW()), ('1236', '$IDSocio', '$dato1',NOW()),('1237','$IDSocio', '$dato2',NOW()),('1238', '$IDSocio', '$dato3',NOW()),('1239', '$IDSocio', '$dato4',NOW()),('1240', '$IDSocio', '$dato5',NOW()),('1241', '$IDSocio', '$dato6',NOW()),('1242', '$IDSocio', '$dato7',NOW()),('1243', '$IDSocio', '$dato8',NOW()),('1244', '$IDSocio', '$dato9',NOW()),('1245', '$IDSocio', '$dato10',NOW()),('1246', '$IDSocio', '$dato11',NOW()),('1247', '$IDSocio', '$dato12',NOW())";

                            $dbo->query($sqlinfosocio);

                            $tyc = "true";
                            $aceptar_tyc = SIMWebServiceCountryMedellin::App_ActualizarEstadoUsuario($token, $tyc);
                            $datos_tyc = json_decode($aceptar_tyc);
                            //datos perfil 

                            $dato_tyc =  $datos_tyc->estado;


                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        } else {


                            $datos_socioss = "SELECT  Foto, IDSocio FROM Socio WHERE NumeroDocumento ='$identificacion' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $datos1 = $dbo->query($datos_socioss);
                            while ($row = $dbo->fetchArray($datos1)) {
                                $foto = $row["Foto"];
                                $IDSocio = $row["IDSocio"];
                            }

                            $filedelete = SOCIO_DIR . $foto;
                            unlink($filedelete);

                            require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";

                            $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarPerfil($token);
                            $data1 = json_decode($resultado1);
                            //datos perfil 
                            $dato =  $data1->perfil->estadoCivil;
                            $$dato_fecha =   $data1->perfil->fechaAniversario;
                            $newDate = date("Y-m-d", strtotime($dato_fecha));
                            $dato1 =  $newDate;
                            $dato2 =  $data1->perfil->telefono;
                            $dato3 =  $data1->perfil->celular;
                            $dato4 =  $data1->perfil->direccion;
                            $dato5 =   $data1->perfil->profesion;
                            $dato6 =   $data1->perfil->nombreEmpresa;
                            $dato7 =  $data1->perfil->cargo;
                            $dato8 =  $data1->perfil->telefonoOficina;
                            $dato9 =  $data1->perfil->direccionOficina;
                            $dato10 =  $data1->perfil->direccionEnvio;
                            $dato11 =   $data1->perfil->correofacturacion;
                            $dato12 =   $data1->perfil->estudiante;
                            $imagenEnBase64 =   $data1->perfil->imagen;
                            if ($dato12 == "false") {
                                $dato12 = "No";
                            } else {
                                $dato12 = "Si";
                            }


                            //Guardamos la foto de ellos en nuestro directorio 
                            $img = str_replace('data:image/png;base64,', '', $imagenEnBase64);
                            $img = str_replace(' ', '+', $img);
                            $data = base64_decode($img);
                            $file = SOCIO_DIR . uniqid() . '.png';
                            $success = file_put_contents($file, $data);


                            $foto = str_replace('/home/http/miclubapp/httpdocs/admin/../file/socio/', '', $file);

                            $parametros_codigo_qr = $identificacion;
                            $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                            $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            //Actualizamos los datos por si se ha cambiado en la otra app
                            $sql_actualiza = "UPDATE Socio SET TokenCountryMedellin = '" . $token . "', Accion='" . $accion . "', NumeroDerecho='" . $codigo . "', Nombre='" . $nombre . "', FechaNacimiento='" . $fecha_nacimiento . "', NumeroDocumento='" . $identificacion . "', Email='" . $email . "' , Foto='" . $foto . "' , SolicitaEditarPerfil='N', FotoActualizadaSocio='N' WHERE IDSocio = '" . $IDSocio . "'";
                            $dbo->query($sql_actualiza);


                            $tyc = "true";
                            $aceptar_tyc = SIMWebServiceCountryMedellin::App_ActualizarEstadoUsuario($token, $tyc);
                            $datos_tyc = json_decode($aceptar_tyc);
                            //datos perfil 

                            $dato_tyc =  $datos_tyc->estado;


                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        }
                    } else {
                        $respuesta["message"] =  $data->mensaje;
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }



                endif;


                //Caso especial fedegolf si no existe en la base lo consulto y lo creo
                if ($IDClub == "17") :
                    require LIBDIR . "SIMWebServiceFedegolf.inc.php";
                    $resultado = SIMWebServiceFedegolf::login($email, $Clave);
                    if ($resultado["success"]) {

                        $DocFederado = $resultado["response"][0]["documento"];
                        //$socio_codigo = $dbo->getFields("Socio", "IDSocio", "IDClub = '" . $IDClub . "' and Email = '" . $email . "'");
                        $datos_socio = $dbo->fetchAll("Socio", " NumeroDocumento = '" . $email . "' and IDClub = '" . $IDClub . "' ", "array");
                        if (empty($datos_socio["IDSocio"])) :
                            //busco en el ws si existe este codigo para crearlo                    
                            $datos_jugador = SIMWebServiceFedegolf::get_usuario_codigo($email);

                            if (!empty($datos_jugador["response"][0]["codJugador"])) :
                                $sql_crea_socio = "INSERT INTO Socio (IDClub, IDCategoria, IDParentesco, IDEstadoSocio, Accion, NumeroDerecho, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave, SolicitaEditarPerfil)
                                               Values ('" . $IDClub . "','" . $datos_jugador["response"][0]["CategoriaSocio"] . "',18,1,'" . $datos_jugador["response"][0]["codJugador"] . "','" . $datos_jugador["response"][0]["codJugador"] . "','" . trim($datos_jugador["response"][0]["genero"]) . "','" . trim($datos_jugador["response"][0]["nombre"]) . "','" . trim($datos_jugador["response"][0]["apellido"]) . "',
                                                       '" . substr($datos_jugador["response"][0]["fecha_nacimiento"], 0, 10) . "','" . trim($datos_jugador["response"][0]["documento"]) . "',												'" . $email . "',sha1('" . $Clave . "'), '" . trim($datos_jugador["response"][0]["email"]) . "',NOW(),'WebServiceFedegolf','Socio','S','N','S')";
                                $dbo->query($sql_crea_socio);
                            endif;
                        endif;
                    } else {
                        $respuesta["message"] = $resultado["message"];
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                /*
                    $socio_codigo = $dbo->getFields("Socio", "IDSocio", "IDClub = '" . $IDClub . "' and Email = '" . $email . "'");
                    if (empty($socio_codigo)):
                        //busco en el ws si existe este codigo para crearlo
                        //$datos_jugador = SIMWebServiceFedegolf::get_usuario_codigo( $email );
                        if (!empty($datos_jugador["response"][0]["codJugador"])):
                            $sql_crea_socio = "Insert into Socio (IDClub, IDCategoria, IDParentesco, IDEstadoSocio, Accion, NumeroDerecho, Genero, Nombre, Apellido, FechaNacimiento, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, TipoSocio, PermiteReservar, CambioClave)												Values ('" . $IDClub . "',1,18,1,'" . $email . "','" . $email . "','" . trim($datos_jugador["response"][0]["genero"]) . "','" . trim($datos_jugador["response"][0]["nombre"]) . "','" . trim($datos_jugador["response"][0]["apellido"]) . "','" . substr($datos_jugador["response"][0]["fecha_nacimiento"], 0, 10) . "','" . trim($datos_jugador["response"][0]["documento"]) . "',												'" . $email . "',sha1('" . $email . "'), '" . trim($datos_jugador["response"][0]["email"]) . "',NOW(),'WebServiceFedegolf','Socio','S','N')";
                            $dbo->query($sql_crea_socio);
                        endif;
                    endif;
                    */

                endif;

                //Uruguay
                if ($IDClub == "125") {
                    $sql_verifica_uru = "SELECT IDSocio FROM Socio WHERE ( Email = '" . $email . "' and Token <> '' ) and IDClub in (" . $IDClub . ") Limit 1";
                    $r_uru = $dbo->query($sql_verifica_uru);
                    $row_uru = $dbo->fetchArray($r_uru);
                    if ((int) $row_uru["IDSocio"] <= 0) {

                        //No es socios verifico si es empleado
                        if ($AppVersion >= 25) :
                            $result_funcionario = SIMWebServiceUsuarios::valida_usuario_web($email, $Clave, $IDClub, "", $AppVersion);
                            if ($result_funcionario["message"] == SIMUtil::get_traduccion('', '', 'ok', LANG)) :
                                return $result_funcionario;
                            endif;
                        endif;

                        require LIBDIR . "SIMUruguay.inc.php";
                        $resp = SIMUruguay::valida_socio_uruguay($IDClub, $email, $Clave);
                        if ($resp["estado"] != "ok") {
                            $respuesta["message"] = $resp["mensaje"];
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    }
                }
                //Fin Uruguay

                //castillo amaguaña actualizo para que la pantalla del carnet se vuelva a activar
                if ($IDClub == "86") {
                    $sql_verifica_socio = "SELECT IDSocio FROM Socio WHERE ( Email = '" . $email . "' and Token <> '' ) and IDClub in (" . $IDClub . ") Limit 1";
                    $r_query_socio = $dbo->query($sql_verifica_socio);
                    $row_socio_castillo = $dbo->fetchArray($r_query_socio);
                    if ((int) $row_socio_castillo["IDSocio"] >= 0) {
                        $sql_actualiza_carnet = "UPDATE Socio SET ActivarCarnetSeguridad='S'  WHERE IDSocio='" . $row_socio_castillo["IDSocio"] . "' AND IDClub='" . $IDClub . "'";
                        $dbo->query($sql_actualiza_carnet);
                    }
                }
                //fin castillo

                //Mi Club ingreso con socio de ZEUS DEMO TEMPORAL
                if (($email == "860000164" || $email == "860058490")) {
                    $urlendpoint = "http://www.zeustecnologia.com/wszeus/ServiceWS.asmx?WSDL";
                    $usuariuozeus = "wsclubes";
                    $clavezeus = "zeus";
                    $TokenZeus = SIMWebServiceZeus::obtener_token_club($urlendpoint, $usuariuozeus, $clavezeus);
                    if (empty($TokenZeus)) {
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosepudoobtenertokendezeus', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        $datos_socio_zeus = $dbo->fetchAll("Socio", " Email = '" . $email . "' and IDClub = '" . $IDClub . "' ", "array");
                        $Identificacion = $email;
                        $Accion = $datos_socio_zeus["Accion"];
                        $Secuencia = "00";
                        $DatosSocioZeus = SIMWebServiceZeus::estado_socio($urlendpoint, $TokenZeus, $Identificacion, $Accion, $Secuencia);

                        if ($DatosSocioZeus->item->estado != "A") {
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'BloqeadoenZeusporlasiguienterazon', LANG) . ": " . $DatosSocioZeus->item->motivo;
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        } else {
                            //actualizo el valor de  la cartera
                            $sql_cartera = "UPDATE Socio Set ValorCartera='" . $DatosSocioZeus->item->saldocartera . "' WHERE IDClub = '" . $IDClub . "' and Email = '" . $email . "'";
                            $dbo->query($sql_cartera);
                        }
                    }
                }

                //Caso especial Club Colombia se consulta con lo WS de club colombia
                require_once LIBDIR . "SIMWebServiceClubColombia.inc.php";
                if ($IDClub == "380") :
                    $token_socio = SIMWebServiceClubColombia::obtener_token_colombia($email, $Clave);

                    if (empty($token_socio)) :

                        //No es socios verifico si es empleado
                        if ($AppVersion >= 25) :
                            $result_funcionario = SIMWebServiceUsuarios::valida_usuario_web($email, $Clave, $IDClub, "", $AppVersion);
                            if ($result_funcionario["message"] == "ok") :
                                return $result_funcionario;
                            endif;
                        endif;

                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noencontrado,porfavorverificatuusuarioy/oclave', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    else :

                        $datos_socio = SIMWebServiceClubColombia::get_datos_usuario_club_colombia($token_socio, $email, $Clave);
                        if ($datos_socio != "ok") :
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosepudoobtenerlosdatosdelusuario,porfavorintentemastarde', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                    endif;
                endif;

                //Caso especial Country Club se consulta con lo WS de ellos
                if ($IDClub == "44") {
                    /*
                $estado_autentica = SIMWebServiceApp::autentica_country_club( $email, $Clave );
                if ( $estado_autentica==0 ){
                $respuesta[ "message" ] = "No encontrado, por favor verifica tu usuario y/o clave";
                $respuesta[ "success" ] = false;
                $respuesta[ "response" ] = NULL;
                //return $respuesta;
                }
                 */
                }

                //Caso especial Puerto Azul se consulta con lo WS de ellos
                if ($IDClub == "00") {
                    require LIBDIR . "SIMWebServicePtoAzul.inc.php";
                    $datos_autentica = SIMWebServicePtoAzul::autentica($email, $Clave, $IDClub);
                    if ($datos_autentica["success"] == false) {
                        $respuesta["message"] = $datos_autentica["message"];
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }
                }

                // si el club tiene hijos valido usuario/clave dl primero que lo encuentre
                $sql_hijos = " Select IDClub From Club Where IDClubPadre = '" . $IDClub . "' ";
                $result_hijos = $dbo->query($sql_hijos);
                while ($r_hijos = $dbo->fetchArray($result_hijos)) :
                    $array_id_hijos[] = $r_hijos["IDClub"];
                endwhile;
                if (count($array_id_hijos) > 0) :
                    $id_club_consulta = implode(",", $array_id_hijos);
                else :
                    $id_club_consulta = $IDClub;
                endif;

                $sql_verifica = "SELECT * FROM Socio WHERE ( Email = '" . $email . "' ) and ( Clave = '" . sha1($Clave) . "' " . $otra_condicion_clave . ")  and IDClub in (" . $id_club_consulta . ") and (IDEstadoSocio <> 2  ) Limit 1";

                $qry_verifica = $dbo->query($sql_verifica);

                if ($dbo->rows($qry_verifica) == 0) {

                    //No es socios verifico si es empleado
                    if ($AppVersion >= 25) :
                        $id_club_consulta .= "," . $IDClub;
                        $result_funcionario = SIMWebServiceUsuarios::valida_usuario_web($email, $Clave, $IDClub, $id_club_consulta, $AppVersion);
                        if ($result_funcionario["message"] == "ok") :
                            return $result_funcionario;
                        endif;
                    endif;

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Porfavorverificatuusuarioy/oclave', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end if
                else {

                    $datos_socio = $dbo->fetchArray($qry_verifica);

                    if ($datos_socio["IDEstadoSocio"] == 3) {
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Sucuentaestáenmora,noesposiblesuaccesoalclub', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                    // estado del socio suspendido por junta
                    if ($datos_socio["IDEstadoSocio"] == 6) {
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Suaccesoalappnoestáautorizado', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    }

                    //Borro el id de socios que tienen que cerrar sesion obligatoriamente
                    $sql_borra_sesion = "delete from CierreSesionSocio Where IDSocio ='" . $datos_socio["IDSocio"] . "' Limit 1";
                    $dbo->query($sql_borra_sesion);

                    $flag_canje_cortesia = 0;

                    //Si el socio es por canje o cortesia valido este activo segun las fechas

                    switch ($datos_socio["TipoSocio"]):
                        case "Canje":
                            $fecha_hoy = strtotime(date("Y-m-d"));
                            $FechaInicioCanje = strtotime($datos_socio["FechaInicioCanje"]);
                            $FechaFinCanje = strtotime($datos_socio["FechaFinCanje"]);
                            //echo $FechaInicioCanje.">=".$fecha_hoy ."&&". $fecha_hoy."<=".$FechaFinCanje;
                            if ($fecha_hoy >= $FechaInicioCanje && $fecha_hoy <= $FechaFinCanje) :
                                $flag_canje_cortesia = 0;
                            else :
                                $flag_canje_cortesia = 1;

                            endif;
                            break;
                        case "Cortesia":
                            $fecha_hoy = strtotime(date("Y-m-d"));
                            $FechaInicioCortesia = strtotime($datos_socio["FechaInicioCortesia"]);
                            $FechaFinCortesia = strtotime($datos_socio["FechaFinCortesia"]);
                            if ($fecha_hoy >= $FechaInicioCortesia && $fecha_hoy <= $FechaFinCortesia) :
                                $flag_canje_cortesia = 0;
                            else :
                                $flag_canje_cortesia = 1;
                            endif;
                            break;
                        case "Invitado":
                            $fecha_hoy = strtotime(date("Y-m-d"));
                            $FechaInicioInvitado = strtotime($datos_socio["FechaInicioInvitado"]);
                            $FechaFinInvitado = strtotime($datos_socio["FechaFinInvitado"]);
                            if ($fecha_hoy >= $FechaInicioInvitado && $fecha_hoy <= $FechaFinInvitado) :
                                $flag_canje_cortesia = 0;
                            else :
                                $flag_canje_cortesia = 1;
                            endif;
                            break;
                        default:
                            $flag_canje_cortesia = 0;
                    endswitch;

                    if ($flag_canje_cortesia == 0) {

                        if (!empty($datos_socio["Foto"])) {
                            $foto = SOCIO_ROOT . $datos_socio["Foto"];
                        }

                        $tipo_codigo_carne = $dbo->getFields("Club", "TipoCodigoCarne", "IDClub = '" . $IDClub . "'");

                        switch ($tipo_codigo_carne) {
                            case "Barras":
                                if (!empty($datos_socio["CodigoBarras"])) {
                                    $foto_cod_barras = SOCIO_ROOT . $datos_socio["CodigoBarras"];
                                }
                                break;
                            case "QR":
                                if (!empty($datos_socio["CodigoQR"])) {
                                    $foto_cod_barras = SOCIO_ROOT . "qr/" . $datos_socio["CodigoQR"];
                                }
                                break;
                            default:
                                $foto_cod_barras = "";
                        }

                        //Consulto el nucleo familiar
                        if (!empty($datos_socio["AccionPadre"])) : // Es beneficiario
                            $condicion_nucleo = " and (AccionPadre = '" . $datos_socio["AccionPadre"] . "' or Accion = '" . $datos_socio["AccionPadre"] . "')";
                            //$tipo_socio = "Beneficiario";
                            $tipo_socio = $datos_socio["TipoSocio"];
                        else : // es Cabeza familia
                            $condicion_nucleo = " and AccionPadre = '" . $datos_socio["Accion"] . "'";
                            //$tipo_socio = "Socio";
                            $tipo_socio = $datos_socio["TipoSocio"];
                        endif;

                        //Especial uruguaty los hijos de socios separados deben salir en ambos asi sean acciones distintas
                        if ($IDClub == 125 && !empty($datos_socio["ClaveSistemaExterno"])) {
                            $AccionOtro = $dbo->getFields("Socio", "Accion", "NumeroDocumento = '" . $datos_socio["ClaveSistemaExterno"] . "'");
                            if (!empty($AccionOtro)) {
                                $condicion_nucleo .= " or AccionPadre = '" . $AccionOtro . "'";
                            }
                        }

                        if ($IDClub == 50) {
                            $tipo_socio = SIMUtil::get_traduccion('', '', 'Cargo', LANG) . ": ";
                        }

                        $tipo_socio .= " " . $datos_socio["Predio"] . " " . $datos_socio["Torre"];

                        $response_nucleo = array();
                        $sql_nucleo = "SELECT * FROM Socio WHERE IDClub = '" . $IDClub . "' and IDSocio <> '" . $datos_socio["IDSocio"] . "' and (IDEstadoSocio <> 2 and IDEstadoSocio <> 3 ) " . $condicion_nucleo;
                        $qry_nucleo = $dbo->query($sql_nucleo);
                        while ($datos_nucleo = $dbo->fetchArray($qry_nucleo)) :
                            $foto_nucleo = "";
                            $foto_cod_barras_nucleo = "";

                            if (!empty($datos_nucleo["Foto"])) {
                                $foto_nucleo = SOCIO_ROOT . $datos_nucleo["Foto"];
                            }

                            switch ($tipo_codigo_carne) {
                                case "Barras":
                                    if (!empty($datos_nucleo["CodigoBarras"])) {
                                        $foto_cod_barras_nucleo = SOCIO_ROOT . $datos_nucleo["CodigoBarras"];
                                    }
                                    break;
                                case "QR":
                                    if (!empty($datos_nucleo["CodigoQR"])) {
                                        $foto_cod_barras_nucleo = SOCIO_ROOT . "qr/" . $datos_nucleo["CodigoQR"];
                                    }
                                    break;
                                default:
                                    $foto_cod_barras_nucleo = "";
                            }

                            //if (!empty($datos_nucleo["CodigoBarras"])){
                            //$foto_cod_barras_nucleo =     SOCIO_ROOT.$datos_nucleo["CodigoBarras"];
                            //}

                            //Averiguo tipo: Socio o Beneficiario
                            if (!empty($datos_nucleo["AccionPadre"])) : // Es beneficiario
                                $tipo_socio_nucleo = "Beneficiario";
                                $tipo_socio_nucleo = $datos_nucleo["TipoSocio"];
                            else : // es Cabeza familia
                                //$tipo_socio_nucleo = "Socio";
                                $tipo_socio_nucleo = $datos_nucleo["TipoSocio"];
                            endif;

                            if ($IDClub == 29) {
                                $tipo_socio_nucleo .= " " . substr(trim($datos_nucleo["Predio"]), 0, 16);
                            } else {
                                $tipo_socio_nucleo .= " " . trim($datos_nucleo["Predio"]);
                            }

                            $nucleo["IDSocio"] = $datos_nucleo[IDSocio];
                            $nucleo["IDClub"] = $datos_nucleo[IDClub];
                            $nucleo["Foto"] = $foto_nucleo;
                            $nucleo["Socio"] = $datos_nucleo["Nombre"] . " " . $datos_nucleo["Apellido"];

                            $nucleo["NumeroDerecho"] = $datos_nucleo["Accion"];

                            if (trim($tipo_socio) == "Niñera" && $IDClub == 44) {
                                $nucleo["CodigoBarras"] = "";
                            } else {
                                $nucleo["CodigoBarras"] = $foto_cod_barras_nucleo;
                            }

                            $nucleo["TipoSocio"] = $tipo_socio_nucleo;
                            $nucleo["LabelEstadoUsuario"] = $datos_nucleo["LabelEstadoUsuario"];

                            //Campos carne
                            $array_carne_club_nucleo = array();
                            $reponse_datos_carne_nucleo = array();
                            $campo_mostrar_carne_nucleo = array();
                            $CamposCarne = $dbo->getFields("Club", "CampoCarne", "IDClub = '" . $IDClub . "'");
                            if (!empty($CamposCarne)) {
                                $array_carne_club_nucleo = explode("|||", $CamposCarne);
                                foreach ($array_carne_club_nucleo as $DetalleCampoCarne) {
                                    $EtiquetaCarne = $array_campo_carne[$DetalleCampoCarne]["Nombre"];
                                    $DatoCarne = $datos_nucleo[$array_campo_carne[$DetalleCampoCarne]["CampoTabla"]];
                                    $campo_mostrar_carne_nucleo[] = $EtiquetaCarne . " " . $DatoCarne;
                                }
                            }
                            if ($IDClub == 44 && $datos_nucleo["SocioAusente"] == "S") {
                                $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'EresSocioAusente', LANG);
                                $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'NumeroEntradas', LANG) . ": " . $datos_nucleo["CantidadAusencias"];
                                $LimiteIngresos = 30;
                                $restantes = $LimiteIngresos - $datos_nucleo["CantidadAusencias"];
                                $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'IngresosPendientes', LANG) . ": " . $restantes;
                                $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'Accion', LANG) . ": " . $datos_nucleo["Accion"];
                            } elseif ($IDClub == 133) {
                                $fechaNacimiento = $datos_nucleo["FechaNacimiento"];
                                $dia_actual = date("Y-m-d");
                                $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
                                $años = $edad_diff->format('%y');

                                $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'EdadSocio', LANG) . ": " . $años;
                                $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'FechaIngreso', LANG) . ": " . $datos_nucleo["FechaPago"];
                            } elseif ($IDClub == 194) {
                                $campo_mostrar_carne_nucleo[] = "Noches Disponibles";
                                $sqlHabitaciones = "SELECT * FROM SocioHabitacion WHERE IDSocio = $datos_nucleo[IDSocio]";
                                $qryHabitaciones = $dbo->query($sqlHabitaciones);
                                while ($Datos = $dbo->fetchArray($qryHabitaciones)) :
                                    $Habitacion = $dbo->fetchAll("Habitacion", "IDHabitacion = $Datos[IDHabitacion]");
                                    $Torre = $dbo->fetchAll("Torre", "IDTorre = $Habitacion[IDTorre]");
                                    if (!empty($Torre[Nombre]))
                                        $campo_mostrar_carne_nucleo[] = $Torre[Nombre]  . " Habitacion " . $Habitacion[NombreHabitacion] . ": " . $Datos[Noches] . " Noches";
                                endwhile;
                            }
                            $nucleo["ValoresCarnet"] = $campo_mostrar_carne_nucleo;

                            /*  $fechaNacimiento = $datos_nucleo["FechaNacimiento"];

                            $edad = SIMUtil::Calcular_Edad($fechaNacimiento);
                            /*  echo "EDAD:" . $edad;
                            echo "TIPO SOCIO:" . $datos_socio["TipoSocio"]; 
                            if (($IDClub == 8 || $IDClub == 86) && ($datos_socio["TipoSocio"] == "T" || $datos_socio["TipoSocio"] == "E") && ($edad > 12 && $edad < 65)) {
                                $nucleo["IDSocio"] = "";
                                $nucleo["IDClub"] = "";
                                $nucleo["Foto"] = "";
                                $nucleo["Socio"] = "";
                                $nucleo["NumeroDerecho"] = "";
                                $nucleo["CodigoBarras"] = "";
                                $nucleo["TipoSocio"] = "";
                                $nucleo["LabelEstadoUsuario"] = "";
                                $nucleo["ValoresCarnet"] = "";
                            } */

                            array_push($response_nucleo, $nucleo);

                            /*
                                $array_nucleo[$datos_nucleo[IDSocio]][IDSocio] = $datos_nucleo[IDSocio];
                                $array_nucleo[$datos_nucleo[IDSocio]][IDClub] = $datos_nucleo[IDClub];
                                $array_nucleo[$datos_nucleo[IDSocio]][Foto] = $foto_nucleo;
                                $array_nucleo[$datos_nucleo[IDSocio]][Socio] = $datos_nucleo[Socio];
                                $array_nucleo[$datos_nucleo[IDSocio]][NumeroDerecho] = $datos_nucleo[Accion];
                                $array_nucleo[$datos_nucleo[IDSocio]][CodigoBarras] = $foto_cod_barras_nucleo;
                                 */

                            if ($IDClub == 70 || $IDClub == 9) {
                                SIMWebServiceUsuarios::set_socio_favorito($IDClub, $datos_socio["IDSocio"], $datos_nucleo["IDSocio"], "S");
                            }

                        endwhile;

                        //Preferencias Contenido
                        $response_seccion_noticia = array();
                        $sql_seccionnot_socio = $dbo->query("Select * From SocioSeccion Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                        while ($r_seccionnot_socio = $dbo->fetchArray($sql_seccionnot_socio)) :
                            $seccion_noticia[IDSocio] = $datos_socio["IDSocio"];
                            $seccion_noticia[IDClub] = $datos_socio["IDClub"];
                            $seccion_noticia[IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                            array_push($response_seccion_noticia, $seccion_noticia);
                        /*
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSocio] = $datos_socio["IDSocio"];
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDClub] = $datos_socio["IDClub"];
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                             */
                        endwhile;

                        $response_seccion_noticia2 = array();
                        $sql_seccionnot_socio = $dbo->query("Select * From SocioSeccion2 Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                        while ($r_seccionnot_socio = $dbo->fetchArray($sql_seccionnot_socio)) :
                            $seccion_noticia[IDSocio] = $datos_socio["IDSocio"];
                            $seccion_noticia[IDClub] = $datos_socio["IDClub"];
                            $seccion_noticia[IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                            array_push($response_seccion_noticia2, $seccion_noticia);
                        /*
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSocio] = $datos_socio["IDSocio"];
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDClub] = $datos_socio["IDClub"];
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                             */
                        endwhile;

                        $response_seccion_noticia3 = array();
                        $sql_seccionnot_socio = $dbo->query("Select * From SocioSeccion3 Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                        while ($r_seccionnot_socio = $dbo->fetchArray($sql_seccionnot_socio)) :
                            $seccion_noticia[IDSocio] = $datos_socio["IDSocio"];
                            $seccion_noticia[IDClub] = $datos_socio["IDClub"];
                            $seccion_noticia[IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                            array_push($response_seccion_noticia3, $seccion_noticia);
                        /*
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSocio] = $datos_socio["IDSocio"];
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDClub] = $datos_socio["IDClub"];
                            $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                             */
                        endwhile;

                        //Preferencias Eventos
                        $response_seccion_evento = array();
                        $sql_seccioneve_socio = $dbo->query("Select * From SocioSeccionEvento Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                        while ($r_seccioneve_socio = $dbo->fetchArray($sql_seccioneve_socio)) :
                            $seccion_evento[IDSocio] = $datos_socio["IDSocio"];
                            $seccion_evento[IDClub] = $datos_socio["IDClub"];
                            $seccion_evento[IDSeccionEvento] = $r_seccioneve_socio["IDSeccionEvento"];
                            array_push($response_seccion_evento, $seccion_evento);
                        endwhile;

                        //Socios Favoritos
                        $response_favoritos = array();
                        $sql_favorito_socio = $dbo->query("Select * From SocioFavorito Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                        while ($r_favorito_socio = $dbo->fetchArray($sql_favorito_socio)) :
                            $favorito_socio[IDSocio] = $r_favorito_socio["IDSocio2"];
                            array_push($response_favoritos, $favorito_socio);
                        endwhile;

                        $response["IDClub"] = $datos_socio["IDClub"];
                        $response["IDSocio"] = $datos_socio["IDSocio"];
                        $response["Foto"] = $foto;
                        $response["Socio"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                        $response["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                        $response["Apellido"] = $datos_socio["Apellido"];
                        $response["Celular"] = $datos_socio["Celular"];
                        $response["CorreoElectronico"] = $datos_socio["CorreoElectronico"];
                        $response["Direccion"] = $datos_socio["Direccion"];
                        $response["EstadoCivil"] = $datos_socio["EstadoCivil"];
                        $response["NumeroDocumento"] = $datos_socio["NumeroDocumento"];


                        if ($IDClub == 46) {
                            $dato_carne = utf8_encode($datos_socio["NumeroDocumento"]);
                        } elseif ($IDClub == 44 && !empty($datos_socio["CodigoCarne"])) {
                            $dato_carne = $datos_socio["CodigoCarne"];
                        } elseif ($IDClub == 133 || $IDClub == 155) {
                            $dato_carne = $datos_socio["AccionPadre"];
                        } else {
                            $dato_carne = $datos_socio["Accion"];
                        }

                        $response["NumeroDerecho"] = $dato_carne;
                        $response["CodigoBarras"] = $foto_cod_barras;

                        $response["NucleoFamiliar"] = $response_nucleo;
                        $response["PreferenciasContenido"] = $response_seccion_noticia;
                        $response["PreferenciasContenido2"] = $response_seccion_noticia2;
                        $response["PreferenciasEvento"] = $response_seccion_evento;
                        $response["SocioFavorito"] = $response_favoritos;
                        $response["Dispositivo"] = $datos_socio["Dispositivo"];
                        $response["Token"] = $datos_socio["Token"];
                        $response["TipoSocio"] = $tipo_socio;
                        $response["TipoUsuario"] = "Socio";
                        $response["LabelEstadoUsuario"] = $datos_socio["LabelEstadoUsuario"];

                        //Token Socio
                        $TokenSocio = $datos_socio["IDSocio"] . "-" . date("Ymd-s") . "-" . bin2hex(openssl_random_pseudo_bytes((70 - ($longitud % 2)) / 2));
                        $sql_token = "UPDATE SocioTokenSesion  SET Activo  = 0 WHERE IDSocio = '" . $datos_socio["IDSocio"] . "'";
                        $dbo->query($sql_token);
                        $sql_asigna_token = "INSERT SocioTokenSesion (IDSocio, IDClub, Dispositivo, IDentificador, Modelo, Token, Fecha, Activo ) VALUES('" . $datos_socio["IDSocio"] . "','" . $datos_socio["IDClub"] . "', '" . $Dispositivo . "', '" . $Identificador . "', '" . $Modelo . "', '" . $TokenSocio . "', NOW(),1)";
                        $dbo->query($sql_asigna_token);
                        $response["TokenSesion"] = $TokenSocio;
                        // FIN TOKEN




                        //Campos carne
                        $array_carne_club = array();
                        $reponse_datos_carne = array();
                        $campo_mostrar_carne = array();
                        $CamposCarne = $dbo->getFields("Club", "CampoCarne", "IDClub = '" . $IDClub . "'");
                        if (!empty($CamposCarne)) {
                            $array_carne_club = explode("|||", $CamposCarne);
                            foreach ($array_carne_club as $DetalleCampoCarne) {
                                $EtiquetaCarne = $array_campo_carne[$DetalleCampoCarne]["Nombre"];
                                $DatoCarne = $datos_socio[$array_campo_carne[$DetalleCampoCarne]["CampoTabla"]];
                                $campo_mostrar_carne[] = $EtiquetaCarne . " " . $DatoCarne;
                            }
                        }

                        if ($IDClub == 44 && $datos_socio["SocioAusente"] == "S") {
                            $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'EresSocioAusente', LANG);
                            $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'NumeroEntradas', LANG) . ": " . $datos_socio["CantidadAusencias"];
                            $LimiteIngresos = 30;
                            $restantes = $LimiteIngresos - $datos_socio["CantidadAusencias"];
                            $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'IngresosPendientes', LANG) . ": " . $restantes;

                            $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'Accion', LANG) . ": " . $datos_socio["Accion"];
                        } elseif ($IDClub == 133) {
                            $fechaNacimiento = $datos_socio["FechaNacimiento"];
                            $dia_actual = date("Y-m-d");
                            $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
                            $años = $edad_diff->format('%y');

                            $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'EdadSocio', LANG) . ": " . $años;

                            $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'FechaIngreso', LANG) . ": " . $datos_socio["FechaPago"];
                        } elseif ($IDClub == 194) {
                            $campo_mostrar_carne[] = "Noches Disponibles";
                            $sqlHabitaciones = "SELECT * FROM SocioHabitacion WHERE IDSocio = $datos_socio[IDSocio]";
                            $qryHabitaciones = $dbo->query($sqlHabitaciones);
                            while ($Datos = $dbo->fetchArray($qryHabitaciones)) :
                                $Habitacion = $dbo->fetchAll("Habitacion", "IDHabitacion = $Datos[IDHabitacion]");
                                $Torre = $dbo->fetchAll("Torre", "IDTorre = $Habitacion[IDTorre]");
                                if (!empty($Torre[Nombre]))
                                    $campo_mostrar_carne[] = $Torre[Nombre]  . " Habitacion " . $Habitacion[NombreHabitacion] . ": " . $Datos[Noches] . " Noches";
                            endwhile;
                        }

                        $response["ValoresCarnet"] = $campo_mostrar_carne;

                        //Si el club tiene configurado para solciitar cambio de clave al primer ingreso o el usuario esta marcado para cambio de clave
                        $cambio_clave_club = $dbo->getFields("Club", "SolicitaCambioClave", "IDClub = '" . $datos_socio["IDClub"] . "'");
                        if ((empty($datos_socio["Token"]) && empty($datos_socio["Token"])) && $cambio_clave_club == "S" && (empty($datos_socio["CambioClave"]) || $datos_socio["CambioClave"] == "S")) :
                            $cambiar_clave = "S";
                        elseif ($cambio_clave_club == "S") :
                            $cambiar_clave = "S";
                        else :
                            $cambiar_clave = "N";
                        endif;
                        $response["CambioClave"] = $datos_socio["CambioClave"];
                        $response["CambioSegundaClave"] = $datos_socio["CambioSegundaClave"];

                        //Datos mostrar al editar perfil
                        $response_campo_editar = array();
                        /*
                            $sql_campo_editar = "SELECT CES.* FROM ClubCampoEditarSocio CCES,CampoEditarSocio CES
                            WHERE CCES.`IDCampoEditarSocio`=CES.IDCampoEditarSocio and CCES.IDClub = '" . $IDClub . "' ORDER BY CES.Orden";
                             */

                        $sql_campo_editar = "SELECT CES.* FROM CampoEditarSocio CES
                                                                    WHERE CES.IDClub = '" . $datos_socio["IDClub"] . "' ORDER BY CES.Orden";

                        $qry_campo_editar = $dbo->query($sql_campo_editar);
                        if ($dbo->rows($qry_campo_editar) > 0) {
                            while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                                $campo_editar["IDCampoEditarSocio"] = $r_campo_editar["IDCampoEditarSocio"];
                                $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                                $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                                $campo_editar["Valores"] = trim(preg_replace('/\s+/', ' ', $r_campo_editar["Valores"]));
                                $campo_editar["PermiteEditar"] = $r_campo_editar["PermiteEditar"];
                                //Consulto el valor de la actualización
                                $ValorDatoCampo = $dbo->getFields("SocioCampoEditarSocio", "Valor", "IDCampoEditarSocio = '" . $r_campo_editar["IDCampoEditarSocio"] . "' and IDSocio = '" . $datos_socio["IDSocio"] . "'");
                                if ($ValorDatoCampo != "" && $ValorDatoCampo != "false") {
                                    $ValorDato = $ValorDatoCampo;
                                } else {
                                    $ValorDato = $datos_socio[$r_campo_editar["Nombre"]];
                                }

                                //$campo_editar[ "ValorActual" ] = $datos_socio[  $r_campo_editar[ "Nombre" ] ];
                                $campo_editar["ValorActual"] = trim(preg_replace('/\s+/', ' ', $ValorDato));

                                $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                                array_push($response_campo_editar, $campo_editar);
                            } //ednw while
                        }
                        $response["CampoEditar"] = $response_campo_editar;

                        if ($AppVersion >= 31) {
                            $respuesta = json_encode($response);
                            $param['key'] = KEY_API;
                            $param['nonce'] = sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES));
                            $param['msg'] = $respuesta;
                            $result = SIMUtil::cryptSodium($param);

                            //$response_encrip=array();
                            //$response_encrip[ "data" ] = $param['nonce'].sodium_bin2hex($result["cryptedText"]);
                            $respuesta = array();
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                            $respuesta["success"] = true;
                            $respuesta["response"] = $param['nonce'] . sodium_bin2hex($result["cryptedText"]);
                        } else {
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        }
                    } else {
                        $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Losentimos,lasfechasdelacortesiaocanjeyavencieron', LANG);
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                    }
                }
            } else {
                $respuesta["message"] = "1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;

                $respuesta["response"] = null;
            }

            return $respuesta;
        } //end function

        public function get_perfil($IDClub, $IDSocio, $IDUsuario)
        {
            $dbo = &SIMDB::get();

            $response = array();

            if (!empty($IDSocio)) {
                $sql_campo_editar = "SELECT CES.* FROM CampoEditarSocio CES
                                                        WHERE CES.IDClub = '" . $IDClub . "' ORDER BY CES.Orden";

                $qry_campo_editar = $dbo->query($sql_campo_editar);
                if ($dbo->rows($qry_campo_editar) > 0) {
                    while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                        $array_datos_perfil["IDCampoEditarSocio"] = $r_campo_editar["IDCampoEditarSocio"];
                        $array_datos_perfil["Nombre"] = $r_campo_editar["Nombre"];
                        $array_datos_perfil["Tipo"] = $r_campo_editar["Tipo"];
                        $array_datos_perfil["Valores"] = $r_campo_editar["Valores"];
                        $array_datos_perfil["PermiteEditar"] = $r_campo_editar["PermiteEditar"];
                        //Consulto el valor de la actualización
                        $ValorDato = $dbo->getFields("SocioCampoEditarSocio", "Valor", "IDCampoEditarSocio = '" . $r_campo_editar["IDCampoEditarSocio"] . "' and IDSocio = '" . $IDSocio . "'");
                        //$campo_editar[ "ValorActual" ] = $datos_socio[  $r_campo_editar[ "Nombre" ] ];
                        $array_datos_perfil["ValorActual"] = $ValorDato;
                        $array_datos_perfil["Obligatorio"] = $r_campo_editar["Obligatorio"];
                        array_push($response, $array_datos_perfil);
                    } //ednw while
                }
            } elseif (!empty($IDUsuario)) {
                $sql_campo_editar = "SELECT CEU.* FROM CampoEditarUsuario CEU
                                                    WHERE CEU.IDClub = '" . $IDClub . "' ORDER BY CEU.Orden";

                $qry_campo_editar = $dbo->query($sql_campo_editar);
                if ($dbo->rows($qry_campo_editar) > 0) {
                    while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                        $array_datos_perfil["IDCampoEditarSocio"] = $r_campo_editar["IDCampoEditarUsuario"];
                        $array_datos_perfil["Nombre"] = $r_campo_editar["Nombre"];
                        $array_datos_perfil["Tipo"] = $r_campo_editar["Tipo"];
                        $array_datos_perfil["Valores"] = $r_campo_editar["Valores"];
                        $array_datos_perfil["PermiteEditar"] = $r_campo_editar["PermiteEditar"];
                        //Consulto el valor de la actualización
                        $ValorDato = $dbo->getFields("UsuarioCampoEditarUsuario", "Valor", "IDCampoEditarUsuario = '" . $r_campo_editar["IDCampoEditarUsuario"] . "' and IDUsuario = '" . $IDUsuario . "'");
                        //$campo_editar[ "ValorActual" ] = $datos_socio[  $r_campo_editar[ "Nombre" ] ];
                        $array_datos_perfil["ValorActual"] = $ValorDato;
                        $array_datos_perfil["Obligatorio"] = $r_campo_editar["Obligatorio"];
                        array_push($response, $array_datos_perfil);
                    } //ednw while
                }
            }

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = $response;

            return $respuesta;
        }

        public function valida_socio_accion($IDClub, $Accion)
        {
            $dbo = &SIMDB::get();
            $datos_socio = $dbo->fetchAll("Socio", " Accion = '" . $Accion . "' and IDCLub = '" . $IDClub . "' LIMIT 1", "array");
            $datos_socio = array_map("utf8_encode", $datos_socio);
            if (!empty($datos_socio["IDSocio"])) :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = $datos_socio;
            else :
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Socionoencontrado', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

            return $respuesta;
        }

        public function validar_canje_activo($IDSocio, $Fecha)
        {
            $dbo = &SIMDB::get();
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $flag_canje_cortesia = 0;
            //Si el socio es por canje o cortesia valido este activo segun las fechas

            switch ($datos_socio["TipoSocio"]):
                case "Canje":
                    $fecha_hoy = strtotime(date($Fecha));
                    $FechaInicioCanje = strtotime($datos_socio["FechaInicioCanje"]);
                    $FechaFinCanje = strtotime($datos_socio["FechaFinCanje"]);
                    //echo $FechaInicioCanje.">=".$fecha_hoy ."&&". $fecha_hoy."<=".$FechaFinCanje;
                    if ($fecha_hoy >= $FechaInicioCanje && $fecha_hoy <= $FechaFinCanje) :
                        $flag_canje_cortesia = 0;
                    else :
                        $flag_canje_cortesia = 1;
                    endif;
                    break;
                case "Cortesia":
                    $fecha_hoy = strtotime(date($Fecha));
                    $FechaInicioCortesia = strtotime($datos_socio["FechaInicioCortesia"]);
                    $FechaFinCortesia = strtotime($datos_socio["FechaFinCortesia"]);
                    if ($fecha_hoy >= $FechaInicioCortesia && $fecha_hoy <= $FechaFinCortesia) :
                        $flag_canje_cortesia = 0;
                    else :

                        $flag_canje_cortesia = 1;
                    endif;
                    break;
                case "Invitado":
                    $fecha_hoy = strtotime(date($Fecha));
                    $FechaInicioInvitado = strtotime($datos_socio["FechaInicioInvitado"]);
                    $FechaFinInvitado = strtotime($datos_socio["FechaFinInvitado"]);
                    if ($fecha_hoy >= $FechaInicioInvitado && $fecha_hoy <= $FechaFinInvitado) :
                        $flag_canje_cortesia = 0;
                    else :
                        $flag_canje_cortesia = 1;
                    endif;
                    break;
                default:
                    $flag_canje_cortesia = 0;
            endswitch;

            return $flag_canje_cortesia;
        }

        public function get_noticacion_local($IDClub, $IDSocio, $IDUsuario)
        {
            $dbo = &SIMDB::get();
            //Notificaciones Locales
            $condicion_notif_local = SIMWebServiceApp::verificar_notificacion_local($IDSocio, $IDUsuario, $IDClub);
            $response_notif_local = array();
            $sql_notif = "SELECT *
                                    FROM NotificacionLocal
                                    WHERE IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() " . $condicion_notif_local;
            $qry_notif = $dbo->query($sql_notif);
            if ($dbo->rows($qry_notif) > 0) {
                while ($r_notif = $dbo->fetchArray($qry_notif)) {
                    $datos_notif["IDNotificacionLocal"] = $r_notif["IDNotificacionLocal"];
                    $datos_notif["IDModulo"] = $r_notif["IDModulo"];
                    $datos_notif["IDDetalle"] = $r_notif["IDDetalle"];
                    $datos_notif["Titulo"] = $r_notif["Titulo"];
                    $datos_notif["Mensaje"] = $r_notif["Mensaje"];
                    //$datos_notif[ "FechaInicio" ] = $r_notif[ "FechaInicio" ];
                    $datos_notif["FechaInicio"] = date("Y-m-d");
                    $datos_notif["FechaFin"] = $r_notif["FechaFin"];
                    $datos_notif["HoraInicio"] = $r_notif["HoraInicio"];
                    $datos_notif["HoraFin"] = $r_notif["HoraFin"];
                    $datos_notif["Periodicidad"] = $r_notif["Periodicidad"];

                    $response_dias = array();
                    $datos_dia = array();
                    $array_dias = array();
                    $array_dias = explode("|", $r_notif["Dias"]);
                    foreach ($array_dias as $dia) {
                        if ((string) $dia == "0") {
                            $dia = 7;
                        }

                        if (!empty($dia)) {
                            switch ($dia) {
                                case '0':
                                case '7':
                                    $datos_dia["Dia"] = "D";
                                    break;
                                case '1':
                                    $datos_dia["Dia"] = "L";
                                    break;
                                case '2':
                                    $datos_dia["Dia"] = "M";
                                    break;
                                case '3':
                                    $datos_dia["Dia"] = "X";
                                    break;
                                case '4':
                                    $datos_dia["Dia"] = "J";
                                    break;
                                case '5':
                                    $datos_dia["Dia"] = "V";
                                    break;
                                case '6':
                                    $datos_dia["Dia"] = "S";
                                    break;
                                default:
                                    $datos_dia["Dia"] = "D";
                            }
                            array_push($response_dias, $datos_dia);
                        }
                    };

                    $datos_notif["Dias"] = $response_dias;
                    array_push($response_notif_local, $datos_notif);
                } //ednw while
            }

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = $response_notif_local;

            return $respuesta;
        }

        public function get_socios_club($id_club, $numero_documento = "", $numero_derecho = "", $tag = "", $IDSocio = "", $Titular = "", $IDClubAsociado = "", $IDFiltro = "")
        {
            $dbo = &SIMDB::get();

            $foto = "";

            // Secciones Socio
            if (!empty($numero_documento)) :
                $array_condiciones[] = " NumeroDocumento  = '" . $numero_documento . "'";
            endif;

            // Seccion Especifica
            if (!empty($numero_derecho)) :
                $array_condiciones[] = " Accion  = '" . $numero_derecho . "'";
            endif;

            // Tag
            if (!empty($tag)) :
                //$tag = utf8_decode($tag);
                $array_buscar = explode(" ", $tag);
                foreach ($array_buscar as $key => $value) {
                    $array_condiciones_nombre[] = " (	Nombre  like '%" . $value . "%' or Apellido like '%" . $value . "%' or Accion like '%" . $value . "%' or NumeroDocumento like '%" . $value . "%' or AccionPadre like '%" . $value . "%' or Predio like '%" . $value . "%' )";
                }
                if (count($array_condiciones_nombre) > 0) {
                    $condicion_nombre = implode(" and ", $array_condiciones_nombre);
                }

                $array_condiciones[] = $condicion_nombre;
            endif;

            //filtro para el modulo de reconocimientos
            if (!empty($IDFiltro)) {
                $array_condiciones[] = " IDAreaSocio='" . $IDFiltro . "'";
            } else if (!empty($IDSocio) && empty($tag)) {
                $sql_fav = "SELECT * FROM SocioFavorito WHERE IDSocio = '" . $IDSocio . "'";
                $qry_fav = $dbo->query($sql_fav);
                while ($r_fav = $dbo->fetchArray($qry_fav)) {
                    $array_favoritos[] = $r_fav["IDSocio2"];
                }
                if (count($array_favoritos) > 0) :
                    $array_condiciones[] = " IDSocio  in  (" . implode(",", $array_favoritos) . ")";
                else :
                    $array_condiciones[] = " IDSocio  in  (0)";
                endif;
            }


            if (count($array_condiciones) > 0) :
                $condiciones = implode(" and ", $array_condiciones);
                $condiciones_noticia = " and " . $condiciones;
            endif;

            $response = array();
            $sql = "SELECT * FROM Socio WHERE IDClub = '" . $id_club . "' and (IDEstadoSocio = 1 OR IDEstadoSocio = 5) and IDSocio <> '" . $IDSocio . "'" . $condiciones_noticia . " ORDER BY Nombre ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {

                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $evento["IDClub"] = $r["IDClub"];
                    $evento["IDSocio"] = $r["IDSocio"];

                    if (!empty($r["Foto"])) {
                        $foto = SOCIO_ROOT . $r["Foto"];
                    }

                    $favorito = "N";
                    if (!empty($IDSocio)) :
                        $socio_favorito = $dbo->getFields("SocioFavorito", "IDSocioFavorito", "IDSocio = '" . $IDSocio . "' and IDSocio2 = '" . $r["IDSocio"] . "'");
                        if (!empty($socio_favorito)) :
                            $favorito = "S";
                        else :
                            $favorito = "N";
                        endif;
                    endif;

                    $evento["Foto"] = $foto;
                    $evento["Socio"] = $r["Apellido"] . " " . $r["Nombre"];
                    $evento["Favorito"] = $favorito;
                    $evento["NumeroDerecho"] = $r["Accion"];
                    $evento["Predio"] = $r["Predio"];
                    array_push($response, $evento);
                } //ednw hile
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } //End if
            else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Porfavorutiliceelbuscador', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else

            return $respuesta;
        } // fin function

        public function get_info_socio($IDClub, $IDSocio, $AppVersion, $Data, $TipoApp)
        {
            require_once LIBDIR . "SIMWebServiceClub.inc.php";
            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
            require LIBDIR  . "SIMWebServiceCastillo.inc.php";
            $dbo = &SIMDB::get();
            $response = array();
            $id_club = $IDClub;



            $configuracion_carnet = $dbo->fetchAll("ConfiguracionCarnet", " IDClub = '" . $id_club . "' ", "array");

            if ($AppVersion >= 31 && !empty($Data)) {
                //if (!empty($Data)) {
                $valornonce = substr($Data, 0, 48);
                $valorencrip = substr($Data, 48);
                $param['key'] = KEY_API;
                $param['chiper'] = $valorencrip;
                $param['nonce'] = $valornonce;
                $result_decrypt = SIMUtil::decryptSodium($param);
                if ($result_decrypt["decryptedText"] == "nodecrypt") {
                    $respuesta["message"] = "ENCRIPT. No";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                } else {
                    $result_decrypt["decryptedText"];
                    $texto_desencrip = json_decode($result_decrypt["decryptedText"]);
                    $IDSocio = $texto_desencrip;

                    $sql_campos_carne = "SELECT IDCampoCarne,Nombre,CampoTabla From CampoCarne Where 1";
                    $r_campo_carne = $dbo->query($sql_campos_carne);
                    while ($row_campo_carne = $dbo->fetchArray($r_campo_carne)) {
                        $array_campo_carne[$row_campo_carne["IDCampoCarne"]] = $row_campo_carne;
                    }

                    if ($TipoApp == "Socio") {

                        $sql_verifica = "SELECT * FROM Socio WHERE IDSocio = '" . $IDSocio . "' Limit 1";

                        $qry_verifica = $dbo->query($sql_verifica);
                        if ($dbo->rows($qry_verifica) == 0) {
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Datosnoencontrados', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        } else {

                            $datos_socio = $dbo->fetchArray($qry_verifica);

                            //Borro el id de socios que tienen que cerrar sesion obligatoriamente
                            $sql_borra_sesion = "delete from CierreSesionSocio Where IDSocio ='" . $datos_socio["IDSocio"] . "' Limit 1";
                            $dbo->query($sql_borra_sesion);

                            $flag_canje_cortesia = 0;

                            //Si el socio es por canje o cortesia valido este activo segun las fechas

                            switch ($datos_socio["TipoSocio"]):
                                case "Canje":
                                    $fecha_hoy = strtotime(date("Y-m-d"));
                                    $FechaInicioCanje = strtotime($datos_socio["FechaInicioCanje"]);
                                    $FechaFinCanje = strtotime($datos_socio["FechaFinCanje"]);
                                    //echo $FechaInicioCanje.">=".$fecha_hoy ."&&". $fecha_hoy."<=".$FechaFinCanje;
                                    if ($fecha_hoy >= $FechaInicioCanje && $fecha_hoy <= $FechaFinCanje) :
                                        $flag_canje_cortesia = 0;
                                    else :
                                        $flag_canje_cortesia = 1;
                                    endif;
                                    break;
                                case "Cortesia":
                                    $fecha_hoy = strtotime(date("Y-m-d"));
                                    $FechaInicioCortesia = strtotime($datos_socio["FechaInicioCortesia"]);
                                    $FechaFinCortesia = strtotime($datos_socio["FechaFinCortesia"]);
                                    if ($fecha_hoy >= $FechaInicioCortesia && $fecha_hoy <= $FechaFinCortesia) :
                                        $flag_canje_cortesia = 0;
                                    else :
                                        $flag_canje_cortesia = 1;
                                    endif;
                                    break;
                                case "Invitado":
                                    $fecha_hoy = strtotime(date("Y-m-d"));
                                    $FechaInicioInvitado = strtotime($datos_socio["FechaInicioInvitado"]);
                                    $FechaFinInvitado = strtotime($datos_socio["FechaFinInvitado"]);
                                    if ($fecha_hoy >= $FechaInicioInvitado && $fecha_hoy <= $FechaFinInvitado) :
                                        $flag_canje_cortesia = 0;
                                    else :
                                        $flag_canje_cortesia = 1;
                                    endif;
                                    break;
                                default:
                                    $flag_canje_cortesia = 0;
                            endswitch;
                            if ($flag_canje_cortesia == 0) {

                                if (!empty($datos_socio["Foto"])) {
                                    $foto = SOCIO_ROOT . $datos_socio["Foto"];
                                }

                                $tipo_codigo_carne = $dbo->getFields("Club", "TipoCodigoCarne", "IDClub = '" . $id_club . "'");

                                switch ($tipo_codigo_carne) {
                                    case "Barras":
                                        if (!empty($datos_socio["CodigoBarras"])) {
                                            $foto_cod_barras = SOCIO_ROOT . $datos_socio["CodigoBarras"];
                                        }
                                        break;
                                    case "QR":
                                        if (!empty($datos_socio["CodigoQR"])) {
                                            $foto_cod_barras = SOCIO_ROOT . "qr/" . $datos_socio["CodigoQR"];
                                        }
                                        break;
                                    default:
                                        $foto_cod_barras = "";
                                }

                                //Consulto el nucleo familiar
                                if (!empty($datos_socio["AccionPadre"])) : // Es beneficiario
                                    $condicion_nucleo = " and (AccionPadre = '" . $datos_socio["AccionPadre"] . "' or Accion = '" . $datos_socio["AccionPadre"] . "')";
                                    //$tipo_socio = "Beneficiario";
                                    $tipo_socio = $datos_socio["TipoSocio"];
                                else : // es Cabeza familia
                                    $condicion_nucleo = " and AccionPadre = '" . $datos_socio["Accion"] . "'";
                                    //$tipo_socio = "Socio";
                                    $tipo_socio = $datos_socio["TipoSocio"];
                                endif;

                                if ($id_club == 50) {
                                    $tipo_socio = "Cargo: ";
                                }

                                $tipo_socio .= " " . $datos_socio["Predio"] . " " . $datos_socio["Torre"];

                                $response_nucleo = array();
                                $sql_nucleo = "SELECT * FROM Socio WHERE IDClub = '" . $id_club . "' and IDSocio <> '" . $datos_socio["IDSocio"] . "' and (IDEstadoSocio <> 2 and IDEstadoSocio <> 3 ) " . $condicion_nucleo;
                                $qry_nucleo = $dbo->query($sql_nucleo);
                                while ($datos_nucleo = $dbo->fetchArray($qry_nucleo)) :
                                    $foto_nucleo = "";
                                    $foto_cod_barras_nucleo = "";

                                    if (!empty($datos_nucleo["Foto"])) {
                                        $foto_nucleo = SOCIO_ROOT . $datos_nucleo["Foto"];
                                    }

                                    switch ($tipo_codigo_carne) {
                                        case "Barras":
                                            if (!empty($datos_nucleo["CodigoBarras"])) {
                                                $foto_cod_barras_nucleo = SOCIO_ROOT . $datos_nucleo["CodigoBarras"];
                                            }
                                            break;
                                        case "QR":
                                            if (!empty($datos_nucleo["CodigoQR"])) {
                                                $foto_cod_barras_nucleo = SOCIO_ROOT . "qr/" . $datos_nucleo["CodigoQR"];
                                            }
                                            break;
                                        default:
                                            $foto_cod_barras_nucleo = "";
                                    }

                                    //if (!empty($datos_nucleo["CodigoBarras"])){
                                    //$foto_cod_barras_nucleo =     SOCIO_ROOT.$datos_nucleo["CodigoBarras"];
                                    //}

                                    //Averiguo tipo: Socio o Beneficiario
                                    if (!empty($datos_nucleo["AccionPadre"])) : // Es beneficiario
                                        $tipo_socio_nucleo = "Beneficiario";
                                        $tipo_socio_nucleo = $datos_nucleo["TipoSocio"];
                                    else : // es Cabeza familia
                                        //$tipo_socio_nucleo = "Socio";
                                        $tipo_socio_nucleo = $datos_nucleo["TipoSocio"];
                                    endif;


                                    if ($id_club == 29) {
                                        $tipo_socio_nucleo .= " " . substr(trim($datos_nucleo["Predio"]), 0, 16);
                                    } else {
                                        $tipo_socio_nucleo .= " " . trim($datos_nucleo["Predio"]);
                                    }




                                    // INICIO QR DINAMICO
                                    $nucleo["ActivarCarnetSeguridad"] = $datos_nucleo["ActivarCarnetSeguridad"];
                                    $nucleo["LabelBotonCarnetSeguridad"] = $configuracion_carnet["LabelBotonCarnetSeguridad"];
                                    $nucleo["ActivarCodigoDinamico"] = $configuracion_carnet["ActivarCodigoDinamico"];
                                    $nucleo["LabelBotonCodigoDinamico"] = $configuracion_carnet["LabelBotonCodigoDinamico"];
                                    // FIN QR DINAMICO
                                    $nucleo["IDSocio"] = $datos_nucleo[IDSocio];
                                    $nucleo["IDClub"] = $datos_nucleo[IDClub];
                                    $nucleo["Foto"] = $foto_nucleo;
                                    $nucleo["Socio"] = $datos_nucleo["Nombre"] . " " . $datos_nucleo["Apellido"];
                                    $nucleo["NumeroDerecho"] = $datos_nucleo["Accion"];

                                    if (trim($tipo_socio) == "Niñera" && $id_club == 44) {
                                        $nucleo["CodigoBarras"] = "";
                                    } else {
                                        $nucleo["CodigoBarras"] = $foto_cod_barras_nucleo;
                                    }

                                    $nucleo["TipoSocio"] = $tipo_socio_nucleo;
                                    $nucleo["LabelEstadoUsuario"] = $datos_nucleo["LabelEstadoUsuario"];

                                    //Campos carne
                                    $array_carne_club_nucleo = array();
                                    $reponse_datos_carne_nucleo = array();
                                    $campo_mostrar_carne_nucleo = array();

                                    $CamposCarne = $dbo->getFields("Club", "CampoCarne", "IDClub = '" . $id_club . "'");
                                    if (!empty($CamposCarne)) {
                                        $array_carne_club_nucleo = explode("|||", $CamposCarne);
                                        foreach ($array_carne_club_nucleo as $DetalleCampoCarne) {
                                            $EtiquetaCarne = $array_campo_carne[$DetalleCampoCarne]["Nombre"];
                                            $DatoCarne = $datos_nucleo[$array_campo_carne[$DetalleCampoCarne]["CampoTabla"]];
                                            $campo_mostrar_carne_nucleo[] = $EtiquetaCarne . " " . $DatoCarne;
                                        }
                                    }

                                    if ($id_club == 44 && $datos_nucleo["SocioAusente"] == "S") {
                                        $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'EresSocioAusente', LANG);
                                        $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'NumeroEntradas', LANG) . ":" . $datos_nucleo["CantidadAusencias"];
                                        $LimiteIngresos = 30;
                                        $restantes = $LimiteIngresos - $datos_nucleo["CantidadAusencias"];
                                        $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'IngresosPendientes', LANG) . ": " . $restantes;
                                        $campo_mostrar_carne_nucleo[] =  SIMUtil::get_traduccion('', '', 'Accion', LANG) . ":" . $datos_nucleo["Accion"];
                                    } elseif ($id_club == 133) {
                                        $fechaNacimiento = $datos_nucleo["FechaNacimiento"];
                                        $dia_actual = date("Y-m-d");
                                        $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
                                        $años = $edad_diff->format('%y');

                                        $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'EdadSocio', LANG) . ":" . $años;
                                        $campo_mostrar_carne_nucleo[] = SIMUtil::get_traduccion('', '', 'FechaIngreso', LANG) . ":" . $datos_nucleo["FechaPago"];
                                    } elseif ($IDClub == 194) {
                                        $campo_mostrar_carne_nucleo[] = "Noches Disponibles";
                                        $sqlHabitaciones = "SELECT * FROM SocioHabitacion WHERE IDSocio = $datos_nucleo[IDSocio]";
                                        $qryHabitaciones = $dbo->query($sqlHabitaciones);
                                        while ($Datos = $dbo->fetchArray($qryHabitaciones)) :
                                            $Habitacion = $dbo->fetchAll("Habitacion", "IDHabitacion = $Datos[IDHabitacion]");
                                            $Torre = $dbo->fetchAll("Torre", "IDTorre = $Habitacion[IDTorre]");
                                            if (!empty($Torre[Nombre]))
                                                $campo_mostrar_carne_nucleo[] = $Torre[Nombre]  . " Habitacion " . $Habitacion[NombreHabitacion] . ": " . $Datos[Noches] . " Noches";
                                        endwhile;
                                    }

                                    $nucleo["ValoresCarnet"] = $campo_mostrar_carne_nucleo;








                                    array_push($response_nucleo, $nucleo);
                                    /*
                                        $array_nucleo[$datos_nucleo[IDSocio]][IDSocio] = $datos_nucleo[IDSocio];
                                        $array_nucleo[$datos_nucleo[IDSocio]][IDClub] = $datos_nucleo[IDClub];
                                        $array_nucleo[$datos_nucleo[IDSocio]][Foto] = $foto_nucleo;
                                        $array_nucleo[$datos_nucleo[IDSocio]][Socio] = $datos_nucleo[Socio];
                                        $array_nucleo[$datos_nucleo[IDSocio]][NumeroDerecho] = $datos_nucleo[Accion];
                                        $array_nucleo[$datos_nucleo[IDSocio]][CodigoBarras] = $foto_cod_barras_nucleo;
                                         */

                                    if ($id_club == 70 || $id_club == 9) {
                                        SIMWebServiceUsuarios::set_socio_favorito($id_club, $datos_socio["IDSocio"], $datos_nucleo["IDSocio"], "S");
                                    }

                                endwhile;




                                //PARA CASTILLO DE AMAGUAÑA Los socios titulares(T) y esposas(E) podran activar los carnets de los dependientes únicamente que cumplan las siguientes condiciones:
                                //Socios niños (hijos y nietos) de 4 a 12 años y tercera edad (padres) mayores a 65 años
                                // print_r($response_nucleo);

                                if ($id_club == 86 || $id_club == 8) {
                                    if (trim($datos_socio["TipoSocio"]) == "T" || trim($datos_socio["TipoSocio"]) == "E") {
                                        foreach ($response_nucleo as $key => $Datos) {

                                            // echo "entro en el 1 if";

                                            $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $Datos["IDSocio"] . "'");

                                            if (trim($TipoSocio) == "H" || trim($TipoSocio) == "P" || trim($TipoSocio) == "E" || trim($TipoSocio) == "T") {
                                                // echo "entro en el 2 if";

                                                $fechaNacimiento = $dbo->getFields("Socio", "FechaNacimiento", "IDSocio = '" . $Datos["IDSocio"] . "'");
                                                //echo "Fecha de nacimiento:" . $fechaNacimiento;
                                                $edad = SIMUtil::Calcular_Edad($fechaNacimiento);



                                                if (($edad > 12 && $edad < 65) || (trim($TipoSocio) == "E" || trim($TipoSocio) == "T")) {

                                                    //array_splice($response_nucleo, $key);
                                                    unset($response_nucleo[$key]);
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($response_nucleo as $key => $Datos) {


                                            //array_splice($response_nucleo, $key);
                                            unset($response_nucleo[$key]);
                                        }
                                    }
                                    $response_nucleo = array_values($response_nucleo);
                                }



                                //Preferencias Contenido
                                $response_seccion_noticia = array();
                                $sql_seccionnot_socio = $dbo->query("Select * From SocioSeccion Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                                while ($r_seccionnot_socio = $dbo->fetchArray($sql_seccionnot_socio)) :
                                    $seccion_noticia[IDSocio] = $datos_socio["IDSocio"];
                                    $seccion_noticia[IDClub] = $datos_socio["IDClub"];
                                    $seccion_noticia[IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                                    array_push($response_seccion_noticia, $seccion_noticia);
                                /*
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSocio] = $datos_socio["IDSocio"];
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDClub] = $datos_socio["IDClub"];
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                                     */
                                endwhile;

                                $response_seccion_noticia2 = array();
                                $sql_seccionnot_socio = $dbo->query("Select * From SocioSeccion2 Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                                while ($r_seccionnot_socio = $dbo->fetchArray($sql_seccionnot_socio)) :
                                    $seccion_noticia[IDSocio] = $datos_socio["IDSocio"];
                                    $seccion_noticia[IDClub] = $datos_socio["IDClub"];
                                    $seccion_noticia[IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                                    array_push($response_seccion_noticia2, $seccion_noticia);
                                /*
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSocio] = $datos_socio["IDSocio"];
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDClub] = $datos_socio["IDClub"];
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                                     */
                                endwhile;

                                $response_seccion_noticia3 = array();
                                $sql_seccionnot_socio = $dbo->query("Select * From SocioSeccion3 Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                                while ($r_seccionnot_socio = $dbo->fetchArray($sql_seccionnot_socio)) :
                                    $seccion_noticia[IDSocio] = $datos_socio["IDSocio"];
                                    $seccion_noticia[IDClub] = $datos_socio["IDClub"];
                                    $seccion_noticia[IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                                    array_push($response_seccion_noticia3, $seccion_noticia);
                                /*
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSocio] = $datos_socio["IDSocio"];
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDClub] = $datos_socio["IDClub"];
                                    $array_preferencia_contenido[$r_seccionnot_socio["IDSeccion"]][IDSeccion] = $r_seccionnot_socio["IDSeccion"];
                                     */
                                endwhile;

                                //Preferencias Eventos
                                $response_seccion_evento = array();
                                $sql_seccioneve_socio = $dbo->query("Select * From SocioSeccionEvento Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                                while ($r_seccioneve_socio = $dbo->fetchArray($sql_seccioneve_socio)) :
                                    $seccion_evento[IDSocio] = $datos_socio["IDSocio"];
                                    $seccion_evento[IDClub] = $datos_socio["IDClub"];
                                    $seccion_evento[IDSeccionEvento] = $r_seccioneve_socio["IDSeccionEvento"];
                                    array_push($response_seccion_evento, $seccion_evento);
                                endwhile;

                                //Socios Favoritos
                                $response_favoritos = array();
                                $sql_favorito_socio = $dbo->query("Select * From SocioFavorito Where IDSocio = '" . $datos_socio["IDSocio"] . "'");
                                while ($r_favorito_socio = $dbo->fetchArray($sql_favorito_socio)) :
                                    $favorito_socio[IDSocio] = $r_favorito_socio["IDSocio2"];
                                    array_push($response_favoritos, $favorito_socio);
                                endwhile;
                                // INICIO QR DINAMICO
                                $response["ActivarCarnetSeguridad"] = $datos_socio["ActivarCarnetSeguridad"];
                                $response["LabelBotonCarnetSeguridad"] = $configuracion_carnet["LabelBotonCarnetSeguridad"];
                                $response["ActivarCodigoDinamico"] = $configuracion_carnet["ActivarCodigoDinamico"];
                                $response["LabelBotonCodigoDinamico"] = $configuracion_carnet["LabelBotonCodigoDinamico"];
                                // FIN QR DINAMICO
                                $response["IDClub"] = $datos_socio["IDClub"];
                                $response["IDSocio"] = $datos_socio["IDSocio"];
                                $response["Foto"] = $foto;
                                $response["Socio"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                                $response["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                                $response["Apellido"] = $datos_socio["Apellido"];
                                $response["Celular"] = $datos_socio["Celular"];
                                $response["CorreoElectronico"] = $datos_socio["CorreoElectronico"];
                                $response["Direccion"] = $datos_socio["Direccion"];
                                $response["EstadoCivil"] = $datos_socio["EstadoCivil"];
                                $response["NumeroDocumento"] = $datos_socio["NumeroDocumento"];
                                $response["NumeroDerecho"] = $datos_socio["NumeroDerecho"];

                                //ACTUALIZO FOTO CASTILLO DE AMAGUAÑA
                                if ($id_club == 8 || $id_club == 86) {
                                    SIMWebServiceCastillo::obtener_foto($id_club, $datos_socio["IDSocio"]);
                                }


                                if ($id_club == 46) {
                                    $dato_carne = utf8_encode($datos_socio["NumeroDocumento"]);
                                } elseif ($id_club == 44 && !empty($datos_socio["CodigoCarne"])) {
                                    $dato_carne = $datos_socio["CodigoCarne"];
                                } elseif ($id_club == 227) {
                                    $dato_carne = utf8_encode($datos_socio["NumeroDerecho"]);
                                } elseif ($id_club == 133 || $id_club == 155 ||  $id_club == 1) {
                                    $dato_carne = $datos_socio["AccionPadre"];
                                } else {
                                    $dato_carne = $datos_socio["Accion"];
                                }

                                $response["NumeroDerecho"] = $dato_carne;
                                $response["CodigoBarras"] = $foto_cod_barras;
                                $response["NucleoFamiliar"] = $response_nucleo;
                                $response["PreferenciasContenido"] = $response_seccion_noticia;
                                $response["PreferenciasContenido2"] = $response_seccion_noticia2;
                                $response["PreferenciasEvento"] = $response_seccion_evento;
                                $response["SocioFavorito"] = $response_favoritos;
                                $response["Dispositivo"] = $datos_socio["Dispositivo"];
                                $response["Token"] = $datos_socio["Token"];
                                $response["TipoSocio"] = $tipo_socio;
                                $response["TipoUsuario"] = "Socio";
                                $response["LabelEstadoUsuario"] = $datos_socio["LabelEstadoUsuario"];

                                //Campos carne
                                $array_carne_club = array();
                                $reponse_datos_carne = array();
                                $campo_mostrar_carne = array();
                                $CamposCarne = $dbo->getFields("Club", "CampoCarne", "IDClub = '" . $id_club . "'");
                                if (!empty($CamposCarne)) {
                                    $array_carne_club = explode("|||", $CamposCarne);
                                    foreach ($array_carne_club as $DetalleCampoCarne) {
                                        $EtiquetaCarne = $array_campo_carne[$DetalleCampoCarne]["Nombre"];
                                        $DatoCarne = $datos_socio[$array_campo_carne[$DetalleCampoCarne]["CampoTabla"]];
                                        $campo_mostrar_carne[] = $EtiquetaCarne . " " . $DatoCarne;
                                    }
                                }
                                if ($id_club == 227) {
                                    //BUSCAMOS LOS ID DE CADA PUBLICIDAD PARA EL CLUB Y ELIMINAMOS CADA UNO EN LA TABLA PUBLICIDAD MODULO
                                    $eliminardatosmodulo = "SELECT IDPublicidad, Foto1 FROM Publicidad WHERE IDCLub='$id_club'";
                                    $eliminardatos = $dbo->query($eliminardatosmodulo);
                                    while ($row = $dbo->fetchArray($eliminardatos)) {
                                        $IDPublicidad = $row["IDPublicidad"];
                                        $foto = $row["Foto1"];
                                        $eliminardato = "DELETE FROM PublicidadModulo WHERE  IDPublicidad='$IDPublicidad'";
                                        $eliminar = $dbo->query($eliminardato);
                                        $filedelete = PUBLICIDAD_DIR . $foto;
                                        unlink($filedelete);
                                    }

                                    //SE ELIMINA CADA PUBLICIDAD DEL CLUB EN LA TABLA PUBLICIDAD
                                    $eliminardatos = " DELETE FROM Publicidad WHERE IDClub='$id_club'";
                                    $sql0 = $dbo->query($eliminardatos);

                                    $datos_socios = "SELECT * FROM Socio WHERE IDSocio ='$IDSocio'  and IDClub = '" . $id_club . "' LIMIT 1";
                                    $datos = $dbo->query($datos_socios);
                                    while ($row = $dbo->fetchArray($datos)) {
                                        $token = $row["TokenCountryMedellin"];
                                    }

                                    $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarDashboard($token);
                                    $data1 = json_decode($resultado1, true);
                                    //datos perfil 
                                    $cantidad = count($data1["imagenes"]["Dashboard"]);
                                    if ($cantidad == 1) :

                                        $dato =  $data1["hoy_en_el_club"];
                                        $imagen =  $data1["imagenes"]["Dashboard"]["imagen"];
                                        $url =  $data1["imagenes"]["Dashboard"]["url"];

                                        $fechaactual = date("Y-m-d");
                                        //sumo 1 día
                                        $fechafin = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));
                                        //resto 1 día
                                        $img = str_replace('data:image/png;base64,', '', $imagen);
                                        $img = str_replace(' ', '+', $img);
                                        $data = base64_decode($img);
                                        $file = PUBLICIDAD_DIR . uniqid() . '.png';
                                        $success = file_put_contents($file, $data);
                                        $foto = str_replace('/home/http/miclubapp/httpdocs/admin/../file/publicidad/', '', $file);

                                        //SE INSERTAN LOS NUEVOS DATOS PARA LA PUBLICIDAD DESDE EL SERVICIO DE ELLOS

                                        $sqlbanner = "INSERT INTO Publicidad (IDClub, DirigidoA, Nombre, Descripcion, AccionClick, Cuerpo, Url, VentanaExterna, Header, Footer, Foto1, FechaInicio, FechaFin, Orden, Publicar)  VALUES  ( '$id_club','S','$dato','$dato','$accion','$dato','$url','S','S','N','$foto','$fechaactual','$fechafin','$sum','S')";

                                        $sql = $dbo->query($sqlbanner);
                                        $idpublicidad = $dbo->lastID();
                                        $sqlmodulo = "INSERT INTO PublicidadModulo (IDPublicidad, IDModulo, Activo) VALUES ('$idpublicidad', '11', 'S');";
                                        $sql1 = $dbo->query($sqlmodulo);

                                    else :
                                        for ($k = 0; $k < $cantidad; $k++) {
                                            $dato =  $data1["hoy_en_el_club"];
                                            $imagen =  $data1["imagenes"]["Dashboard"][$k]["imagen"];
                                            $url =  $data1["imagenes"]["Dashboard"][$k]["url"];
                                            $accion = "SinAccion";
                                            if (isset($url)) {
                                                $accion = "Url";
                                            }
                                            $sum = $k + 1;

                                            $fechaactual = date("Y-m-d");
                                            //sumo 1 día
                                            $fechafin = date("Y-m-d", strtotime($fecha_actual . "+ 1 days"));
                                            //resto 1 día
                                            $img = str_replace('data:image/png;base64,', '', $imagen);
                                            $img = str_replace(' ', '+', $img);
                                            $data = base64_decode($img);
                                            $file = PUBLICIDAD_DIR . uniqid() . '.png';
                                            $success = file_put_contents($file, $data);
                                            $foto = str_replace('/home/http/miclubapp/httpdocs/admin/../file/publicidad/', '', $file);

                                            //SE INSERTAN LOS NUEVOS DATOS PARA LA PUBLICIDAD DESDE EL SERVICIO DE ELLOS

                                            $sqlbanner = "INSERT INTO Publicidad (IDClub, DirigidoA, Nombre, Descripcion, AccionClick, Cuerpo, Url, VentanaExterna, Header, Footer, Foto1, FechaInicio, FechaFin, Orden, Publicar)  VALUES  ( '$id_club','S','$dato','$dato','$accion','$dato','$url','S','S','N','$foto','$fechaactual','$fechafin','$sum','S')";

                                            $sql = $dbo->query($sqlbanner);
                                            $idpublicidad = $dbo->lastID();
                                            $sqlmodulo = "INSERT INTO PublicidadModulo (IDPublicidad, IDModulo, Activo) VALUES ('$idpublicidad', '11', 'S');";
                                            $sql1 = $dbo->query($sqlmodulo);
                                        }

                                    endif;
                                }
                                if ($id_club == 44 && $datos_socio["SocioAusente"] == "S") {
                                    $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'EresSocioAusente', LANG);
                                    $campo_mostrar_carne[] =  SIMUtil::get_traduccion('', '', 'NumeroEntradas', LANG) . ": " . $datos_socio["CantidadAusencias"];
                                    $LimiteIngresos = 30;
                                    $restantes = $LimiteIngresos - $datos_socio["CantidadAusencias"];
                                    $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'IngresosPendientes', LANG) . ": " . $restantes;
                                    $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'Accion', LANG) . ":" . $datos_socio["Accion"];
                                } elseif ($id_club == 133) {
                                    $fechaNacimiento = $datos_socio["FechaNacimiento"];
                                    $dia_actual = date("Y-m-d");
                                    $edad_diff = date_diff(date_create($fechaNacimiento), date_create($dia_actual));
                                    $años = $edad_diff->format('%y');

                                    $campo_mostrar_carne[] = SIMUtil::get_traduccion('', '', 'EdadSocio', LANG) . ": " . $años;
                                    $campo_mostrar_carne[] =  SIMUtil::get_traduccion('', '', 'FechaIngreso', LANG) . ":" . $datos_socio["FechaPago"];
                                } elseif ($IDClub == 194) {
                                    $campo_mostrar_carne[] = "Noches Disponibles";
                                    $sqlHabitaciones = "SELECT * FROM SocioHabitacion WHERE IDSocio = $datos_socio[IDSocio]";
                                    $qryHabitaciones = $dbo->query($sqlHabitaciones);
                                    while ($Datos = $dbo->fetchArray($qryHabitaciones)) :
                                        $Habitacion = $dbo->fetchAll("Habitacion", "IDHabitacion = $Datos[IDHabitacion]");
                                        $Torre = $dbo->fetchAll("Torre", "IDTorre = $Habitacion[IDTorre]");
                                        if (!empty($Torre[Nombre]))
                                            $campo_mostrar_carne[] = $Torre[Nombre]  . " Habitacion " . $Habitacion[NombreHabitacion] . ": " . $Datos[Noches] . " Noches";

                                    endwhile;
                                } elseif ($id_club == 227) {
                                    $datos_socios = "SELECT * FROM Socio WHERE IDSocio=$datos_socio[IDSocio] LIMIT 1";
                                    $datos = $dbo->query($datos_socios);
                                    while ($row = $dbo->fetchArray($datos)) {
                                        $token = $row["TokenCountryMedellin"];
                                        $codigo = $row["NumeroDerecho"];
                                    }

                                    //require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";                                     

                                    $resultado = SIMWebServiceCountryMedellin::App_ConsultarPerfil($token);
                                    $data1 = json_decode($resultado);
                                    $dato =  $data1->perfil->tiposocio;
                                    $campo_mostrar_carne[] = $dato;
                                    $dato_carne = $codigo;
                                } elseif ($IDClub == 239) { //VALLE ARRIBA ATHETIC

                                    $sqlHabitaciones = "SELECT * FROM Socio WHERE IDSocio = $datos_nucleo[IDSocio]";
                                    $qryHabitaciones = $dbo->query($sqlHabitaciones);
                                    $Datos = $dbo->fetchArray($qryHabitaciones);
                                    $Datos["PermiteReservar"];

                                    if ($Datos["PermiteReservar"] == 1) :
                                        $campo_mostrar_carne[] = "Su acción presenta un retraso a la fecha, por favor pase por caja lo antes posible";
                                    else :
                                        $campo_mostrar_carne[] = "Al día";
                                    endif;
                                }


                                $response["ValoresCarnet"] =  $campo_mostrar_carne;

                                //Si el club tiene configurado para solciitar cambio de clave al primer ingreso o el usuario esta marcado para cambio de clave
                                $cambio_clave_club = $dbo->getFields("Club", "SolicitaCambioClave", "IDClub = '" . $datos_socio["IDClub"] . "'");
                                if ((empty($datos_socio["Token"]) && empty($datos_socio["Token"])) && $cambio_clave_club == "S" && (empty($datos_socio["CambioClave"]) || $datos_socio["CambioClave"] == "S")) :
                                    $cambiar_clave = "S";
                                elseif ($cambio_clave_club == "S") :
                                    $cambiar_clave = "S";
                                else :
                                    $cambiar_clave = "N";
                                endif;
                                $response["CambioClave"] = $datos_socio["CambioClave"];
                                $response["CambioSegundaClave"] = $datos_socio["CambioSegundaClave"];

                                //Datos mostrar al editar perfil
                                $response_campo_editar = array();
                                /*
                                    $sql_campo_editar = "SELECT CES.* FROM ClubCampoEditarSocio CCES,CampoEditarSocio CES
                                    WHERE CCES.`IDCampoEditarSocio`=CES.IDCampoEditarSocio and CCES.IDClub = '" . $id_club . "' ORDER BY CES.Orden";
                                     */

                                $sql_campo_editar = "SELECT CES.* FROM CampoEditarSocio CES
                                                                                WHERE (Publicar='S' OR Publicar='') AND CES.IDClub = '" . $datos_socio["IDClub"] . "' ORDER BY CES.Orden";

                                $qry_campo_editar = $dbo->query($sql_campo_editar);
                                if ($dbo->rows($qry_campo_editar) > 0) {

                                    //aseguramos que los datos se actualicen cada vez que entren a mi perfil
                                    if ($IDClub == 227) {

                                        //Buscamos el id del socio
                                        $datos_socios = "SELECT * FROM Socio WHERE IDSocio ='$IDSocio'  and IDClub = '" . $IDClub . "' LIMIT 1";
                                        $datos = $dbo->query($datos_socios);
                                        while ($row = $dbo->fetchArray($datos)) {
                                            $token = $row["TokenCountryMedellin"];
                                        }                 //eliminamos los datos personales del socio
                                        $eliminar_datos = "DELETE FROM `SocioCampoEditarSocio` WHERE IDSocio='$IDSocio'";
                                        $dbo->query($eliminar_datos);

                                        //insertamos de nuevo los datos del services del socio por si ha cambiado en la otra app
                                        //require_once LIBDIR . "SIMWebServiceCountryMedellin.inc.php";


                                        $resultado1 = SIMWebServiceCountryMedellin::App_ConsultarPerfil($token);



                                        $data1 = json_decode($resultado1);





                                        //datos perfil 
                                        $dato =  $data1->perfil->estadoCivil;

                                        $dato_fecha =   $data1->perfil->fechaAniversario;
                                        $newDate = date("Y-m-d", strtotime($dato_fecha));

                                        $dato1 =  $newDate;
                                        $dato2 =  $data1->perfil->telefono;
                                        $dato3 =  $data1->perfil->celular;
                                        $dato4 =  $data1->perfil->direccion;
                                        $dato5 =   $data1->perfil->profesion;
                                        $dato6 =   $data1->perfil->nombreEmpresa;
                                        $dato7 =  $data1->perfil->cargo;
                                        $dato8 =  $data1->perfil->telefonoOficina;
                                        $dato9 =  $data1->perfil->direccionOficina;
                                        $dato10 =  $data1->perfil->direccionEnvio;
                                        $dato11 =   $data1->perfil->correofacturacion;
                                        $dato12 =   $data1->perfil->estudiante;
                                        if ($dato12 == "false") {
                                            $dato12 = "No";
                                        } else {
                                            $dato12 = "Si";
                                        }


                                        $sqlinfosocio = "INSERT INTO SocioCampoEditarSocio ( IDCampoEditarSocio, IDSocio, Valor, FechaTrCr) 
                                                        VALUES  ('1235','" . $IDSocio . "','" . $dato . "', NOW()), 
                                                                ('1236','" . $IDSocio . "','" . $dato1 . "',NOW()),
                                                                ('1237','" . $IDSocio . "','" . $dato2 . "',NOW()),
                                                                ('1238','" . $IDSocio . "','" . $dato3 . "',NOW()),
                                                                ('1239','" . $IDSocio . "','" . $dato4 . "',NOW()),
                                                                ('1240','" . $IDSocio . "','" . $dato5 . "',NOW()),
                                                                ('1241','" . $IDSocio . "','" . $dato6 . "',NOW()),
                                                                ('1242','" . $IDSocio . "','" . $dato7 . "',NOW()),
                                                                ('1243','" . $IDSocio . "','" . $dato8 . "',NOW()),
                                                                ('1244','" . $IDSocio . "','" . $dato9 . "',NOW()),
                                                                ('1245','" . $IDSocio . "','" . $dato10 . "',NOW()),
                                                                ('1246','" . $IDSocio . "','" . $dato11 . "',NOW()),
                                                                ('1247','" . $IDSocio . "','" . $dato12 . "',NOW())";

                                        $sql = $dbo->query($sqlinfosocio);
                                    }
                                    while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {

                                        $campo_editar["ParametroEnvioPost"] = "";
                                        $campo_editar["IDCampoEditarSocio"] = $r_campo_editar["IDCampoEditarSocio"];
                                        $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                                        $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                                        $campo_editar["Valores"] = trim(preg_replace('/\s+/', ' ', $r_campo_editar["Valores"]));
                                        $campo_editar["PermiteEditar"] = $r_campo_editar["PermiteEditar"];



                                        //campos imagenes
                                        if ($r_campo_editar["Tipo"] == "imagen" || $r_campo_editar["Tipo"] == "imagenarchivo") {
                                            $campo_editar["ParametroEnvioPost"] = "ImagenPregunta|" . $r_campo_editar["IDCampoEditarSocio"];
                                        }


                                        //Consulto el valor de la actualización
                                        $ValorDatoCampo = $dbo->getFields("SocioCampoEditarSocio", "Valor", "IDCampoEditarSocio = '" . $r_campo_editar["IDCampoEditarSocio"] . "' and IDSocio = '" . $datos_socio["IDSocio"] . "'");
                                        if ($ValorDatoCampo != "" && $ValorDatoCampo != "false") {
                                            $ValorDato = $ValorDatoCampo;
                                        } else {
                                            $ValorDato = $datos_socio[$r_campo_editar["Nombre"]];
                                        }

                                        // mostrar campos imagenes
                                        if ($r_campo_editar["Tipo"] == "imagen" || $r_campo_editar["Tipo"] == "imagenarchivo" && !empty($ValorDatoCampo)) {
                                            $ValorDato = EDITARPERFIL_ROOT . $ValorDatoCampo;
                                            //$ValorDato =  $ValorDatoCampo;
                                        }


                                        if (($r_campo_editar["Tipo"] == "imagen" || $r_campo_editar["Tipo"] == "imagenarchivo") && empty($ValorDatoCampo) && $r_campo_editar["PermiteEditar"] == "N") {
                                            $campo_editar["PermiteEditar"] = "S";
                                        }

                                        //mostrar los campos de la actualizacion
                                        //$campo_editar[ "ValorActual" ] = $datos_socio[  $r_campo_editar[ "Nombre" ] ];
                                        $campo_editar["ValorActual"] = trim(preg_replace('/\s+/', ' ', $ValorDato));

                                        $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                                        array_push($response_campo_editar, $campo_editar);
                                    } //ednw while
                                }
                                $response["CampoEditar"] = $response_campo_editar;

                                if ($AppVersion >= 31) {
                                    $respuesta = json_encode($response);
                                    $param['key'] = KEY_API;
                                    $param['nonce'] = sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES));
                                    $param['msg'] = $respuesta;
                                    $result = SIMUtil::cryptSodium($param);

                                    //$response_encrip=array();
                                    //$response_encrip[ "data" ] = $param['nonce'].sodium_bin2hex($result["cryptedText"]);
                                    $respuesta = array();
                                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                                    $respuesta["success"] = true;
                                    $respuesta["response"] = $param['nonce'] . sodium_bin2hex($result["cryptedText"]);
                                } else {
                                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                                    $respuesta["success"] = true;
                                    $respuesta["response"] = $response;
                                }
                            } else {
                                $respuesta["message"] =  SIMUtil::get_traduccion('', '', 'Losentimos,lasfechasdelacortesiaocanjeyavencieron', LANG);
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            }
                        }
                    } else {

                        //Es un empleado
                        $sql_verifica = "SELECT * FROM Usuario WHERE IDUsuario = '" . $IDSocio . "'  and IDClub ='" . $id_club . "'";
                        $qry_verifica = $dbo->query($sql_verifica);
                        $datos_usuario = $dbo->fetchArray($qry_verifica);

                        if ($dbo->rows($qry_verifica) == 0) {
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noencontrado', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        } //end if
                        else {

                            $CerrarSesion = $datos_usuario["SolicitarCierreSesion"];
                            if ($CerrarSesion == "S") {
                                $condicion_modulo = " and IDModulo = 14 "; //cerrar sesion
                            }

                            //Modulos Sistema Menu Central
                            $response_modulo = array();
                            $sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '" . $id_club . "' and Activo = 'S' and Ubicacion like '%Central%' " . $condicion_modulo . " ORDER BY Orden";
                            $qry_modulo = $dbo->query($sql_modulo);
                            if ($dbo->rows($qry_modulo) > 0) {
                                while ($r_modulo = $dbo->fetchArray($qry_modulo)) {

                                    $agregar_modulo = SIMWebServiceClub::verifica_permiso_modulo($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);

                                    if ($agregar_modulo == "S") :
                                        // Verificar si el modulo tiene contenido para mostrar
                                        $flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
                                        //$flag_mostrar=0;
                                        if ($flag_mostrar == 0) :
                                            $modulo["IDClub"] = $datos_usuario["IDClub"];
                                            $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                            if (!empty($r_modulo["Titulo"])) {
                                                $modulo["NombreModulo"] = $r_modulo["Titulo"];
                                            } else {
                                                $modulo["NombreModulo"] = $dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'");
                                            }

                                            $modulo["Orden"] = $r_modulo["Orden"];
                                            $icono_modulo = $r_modulo["Icono"];
                                            if (!empty($r_modulo["Icono"])) :
                                                $foto = MODULO_ROOT . $r_modulo["Icono"];
                                            else :
                                                $foto = "";
                                            endif;
                                            $modulo["Icono"] = $foto;
                                            array_push($response_modulo, $modulo);
                                        endif;
                                    endif;
                                } //ednw while
                            }

                            //Modulos Sistema Menu Lateral
                            unset($modulo);
                            $response_modulo_lateral = array();
                            $sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '" . $id_club . "' and Activo = 'S' and Ubicacion like '%Lateral%'  " . $condicion_modulo . " ORDER BY Orden";
                            $qry_modulo = $dbo->query($sql_modulo);
                            if ($dbo->rows($qry_modulo) > 0) {

                                while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                                    $agregar_modulo = SIMWebServiceClub::verifica_permiso_modulo($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);

                                    if ($agregar_modulo == "S") :
                                        // Verificar si el modulo tiene contenido para mostrar
                                        $flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
                                        //$flag_mostrar=0;
                                        if ($flag_mostrar == 0) :
                                            $modulo["IDClub"] = $datos_usuario["IDClub"];
                                            $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                            //$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));
                                            if (!empty($r_modulo["Titulo"])) {
                                                $modulo["NombreModulo"] = $r_modulo["Titulo"];
                                            } else {
                                                $modulo["NombreModulo"] = $dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'");
                                            }

                                            $modulo["Orden"] = $r_modulo["Orden"];
                                            $icono_modulo = $r_modulo["Icono"];
                                            if (!empty($r_modulo["Icono"])) :
                                                $foto = MODULO_ROOT . $r_modulo["Icono"];
                                            else :
                                                $foto = "";
                                            endif;
                                            $modulo["Icono"] = $foto;
                                            array_push($response_modulo_lateral, $modulo);
                                        endif;
                                    endif;
                                } //ednw while
                            }

                            //traer servicios del usuario
                            $response_servicio = array();
                            $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $datos_usuario["IDUsuario"] . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
                            $qry_servicios = $dbo->query($sql_servicios);
                            while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
                                $servicio["IDClub"] = $datos_usuario["IDClub"];
                                $servicio["IDServicio"] = $r_servicio["IDServicio"];
                                $servicio["NombreServicio"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r_servicio["IDServicioMaestro"] . "' ");
                                if (!empty($r_servicio["Icono"])) :
                                    $foto = SERVICIO_ROOT . $r_servicio["Icono"];
                                else :
                                    $foto = "";
                                endif;

                                $servicio["Icono"] = $foto;
                                //$servicio["ServicioInicial"] = $dbo->getFields( "ServicioInicial" , "Nombre" , "IDServicioInicial = '".$r_servicio["IDServicioInicial"]."'" );
                                array_push($response_servicio, $servicio);
                            } //end while

                            $tipo_codigo_carne = $dbo->getFields("AppEmpleado", "TipoCodigoCarne", "IDClub = '" . $id_club . "'");
                            switch ($tipo_codigo_carne) {
                                case "Barras":
                                    if (!empty($datos_usuario["CodigoBarras"])) {
                                        $foto_cod_barras = USUARIO_ROOT . $datos_usuario["CodigoBarras"];
                                    }
                                    break;
                                case "QR":
                                    if (!empty($datos_usuario["CodigoQR"])) {
                                        $foto_cod_barras = USUARIO_ROOT . "qr/" . $datos_usuario["CodigoQR"];
                                    }
                                    break;
                                default:
                                    $foto_cod_barras = "";
                            }

                            if (!empty($datos_usuario["Foto"])) {
                                $foto_empleado = USUARIO_ROOT . $datos_usuario["Foto"];
                            }

                            $response["IDClub"] = $datos_usuario["IDClub"];
                            $response["IDUsuario"] = $datos_usuario["IDUsuario"];
                            $response["IDPerfil"] = $datos_usuario["IDPerfil"];
                            $response["Nombre"] = $datos_usuario["Nombre"];
                            $response["Autorizado"] = $datos_usuario["Autorizado"];
                            $response["Nivel"] = $datos_usuario["Nivel"];
                            $response["Permiso"] = $datos_usuario["Permiso"];
                            $response["ServiciosReserva"] = $response_servicio;
                            $response["Modulos"] = $response_modulo;
                            $response["ModulosLateral"] = $response_modulo_lateral;
                            $response["CodigoBarras"] = $foto_cod_barras;
                            $response["Dispositivo"] = $datos_usuario["Dispositivo"];
                            $response["Token"] = $datos_usuario["Token"];

                            if (($datos_usuario["IDClub"] == 44 || $datos_usuario["IDClub"] == 8) && $datos_usuario["TipoUsuario"] != '') :
                                $campo_mostrar_carne[] = "Tipo Usuario";
                                $campo_mostrar_carne[] = $datos_usuario["TipoUsuario"];
                            endif;

                            $response["ValoresCarnet"] = $campo_mostrar_carne;

                            //$response["NumeroDerecho"] = $datos_usuario["CodigoUsuario"];
                            $response["NumeroDerecho"] = "";
                            //Consulto si el app esta configurado para permitir se puede cambiar p[ara que sea por usuario
                            $response["PermiteInvitacionPortero"] = $dbo->getFields("AppEmpleado", "PermiteInvitacionPortero", "IDClub = '" . $id_club . "'");
                            //Consulto las areas
                            $sql_area_usuario = "Select * From UsuarioArea Where IDUsuario = '" . $datos_usuario["IDUsuario"] . "'";
                            $result_area_usuario = $dbo->query($sql_area_usuario);
                            while ($row_area = $dbo->fetchArray($result_area_usuario)) :
                                $nombre_area = utf8_encode($dbo->getFields("Area", "Nombre", "IDArea = '" . $row_area["IDArea"] . "'"));
                                $array_areas[] = $nombre_area;
                            endwhile;
                            if (count($array_areas) > 0) :
                                $nombre_areas = implode(",", $array_areas);
                            endif;

                            $nombre_areas = "";
                            $response["Area"] = $nombre_areas;
                            $response["Cargo"] = utf8_encode($datos_usuario["Cargo"]);
                            $response["Codigo"] = $datos_usuario["CodigoUsuario"];
                            $response["PermiteReservar"] = $datos_usuario["PermiteReservar"];
                            $response["Activo"] = $datos_usuario["Activo"];
                            $response["Foto"] = $foto_empleado;
                            $response["TipoUsuario"] = 'Empleado';

                            //Encuestas al abrir app
                            $encuesta_activa = 0;
                            $response_encuesta = array();
                            $sql_encuesta = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $id_club . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'E' or DirigidoA = 'T')  ORDER BY Orden ";
                            $qry_encuesta = $dbo->query($sql_encuesta);
                            if ($dbo->rows($qry_encuesta) > 0) {
                                while ($r_encuesta = $dbo->fetchArray($qry_encuesta)) {
                                    $mostrar_encuesta = 0;
                                    //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                                    if ($r_encuesta["UnaporSocio"] == "S") {
                                        $sql_resp = "Select IDEncuesta From EncuestaRespuesta Where IDSocio = '" . $datos_usuario["IDUsuario"] . "' and IDEncuesta = '" . $r_encuesta["IDEncuesta"] . "' Limit 1";
                                        $r_resp = $dbo->query($sql_resp);
                                        if ($dbo->rows($r_resp) <= 0) {
                                            $mostrar_encuesta = 1;
                                        }
                                    } else {
                                        $mostrar_encuesta = 1;
                                    }
                                    //Verifico si la encuesta es solo para algunos socios para mostrar o no
                                    //$permiso_encuesta=SIMWebServiceApp::verifica_ver_encuesta($r_encuesta,$IDSocio);
                                    $permiso_encuesta = 1;

                                    if ($mostrar_encuesta == 1 && $permiso_encuesta == 1) {
                                        $encuesta["IDClub"] = $r_encuesta["IDClub"];
                                        $encuesta["IDEncuesta"] = $r_encuesta["IDEncuesta"];
                                        $encuesta["Nombre"] = $r_encuesta["Nombre"];
                                        $encuesta["Descripcion"] = $r_encuesta["Descripcion"];
                                        if (!empty($r_encuesta["Imagen"])) :
                                            $foto = BANNERAPP_ROOT . $r_encuesta["Imagen"];
                                        else :
                                            $foto = "";
                                        endif;
                                        $encuesta["ImagenEncuesta"] = $foto;
                                        $encuesta_activa = 1;

                                        array_push($response_encuesta, $encuesta);
                                    }
                                } //ednw while
                            }
                            //FIN Encuestas al abrir app
                            $response["Encuesta"] = $response_encuesta;
                            $response["LabelEncuesta"] = SIMUtil::get_traduccion('', '', 'Encuesta', LANG);

                            //Autodisagnostico al abrir app
                            $diagnostico_activa = 0;
                            $response_diagnostico = array();
                            $sql_diagnostico = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $id_club . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'E' or DirigidoA = 'T')  ORDER BY Orden ";
                            $qry_diagnostico = $dbo->query($sql_diagnostico);
                            if ($dbo->rows($qry_diagnostico) > 0) {
                                while ($r_diagnostico = $dbo->fetchArray($qry_diagnostico)) {
                                    $mostrar_disgnostico = 0;
                                    //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                                    if ($r_diagnostico["UnaporSocio"] == "S") {
                                        $sql_resp = "Select IDDiagnostico From DiagnosticoRespuesta Where IDUsuario = '" . $IDUsuario . "' and IDDiagnostico = '" . $r_diagnostico["IDDiagnostico"] . "' Limit 1";
                                        $r_resp = $dbo->query($sql_resp);
                                        if ($dbo->rows($r_resp) <= 0) {
                                            $mostrar_disgnostico = 1;
                                        }
                                    } else {
                                        $mostrar_disgnostico = 1;
                                    }
                                    //Verifico si la encuesta es solo para algunos socios para mostrar o no
                                    $permiso_diagnostico = SIMWebServiceApp::verifica_ver_diagnostico($r_diagnostico, $IDSocio, $IDUsuario);
                                    //$permiso_encuesta=1;

                                    if ($mostrar_disgnostico == 1 && $permiso_diagnostico == 1) {
                                        $diagnostico["IDClub"] = $r_diagnostico["IDClub"];
                                        $diagnostico["IDDiagnostico"] = $r_diagnostico["IDDiagnostico"];
                                        $diagnostico["Nombre"] = $r_diagnostico["Nombre"];
                                        $diagnostico["Descripcion"] = $r_diagnostico["Descripcion"];
                                        if (!empty($r_diagnostico["Imagen"])) :
                                            $foto = BANNERAPP_ROOT . $r_diagnostico["Imagen"];
                                        else :
                                            $foto = "";
                                        endif;
                                        $diagnostico["ImagenDiagnostico"] = $foto;
                                        $diagnostico_activa = 1;

                                        array_push($response_diagnostico, $diagnostico);
                                    }
                                } //ednw while
                            }
                            //FIN Encuestas al abrir app
                            $response["Diagnostico"] = $response_diagnostico;
                            $response["LabelDiagnostico"] = SIMUtil::get_traduccion('', '', 'Diligenciarauto', LANG);

                            $response_campo_editar = array();
                            $sql_campo_editar = "SELECT CEU.* FROM CampoEditarUsuario CEU
                                                                                                WHERE CEU.IDClub = '" . $datos_usuario["IDClub"] . "' ORDER BY CEU.Orden";

                            $qry_campo_editar = $dbo->query($sql_campo_editar);
                            if ($dbo->rows($qry_campo_editar) > 0) {
                                while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                                    $campo_editar["IDCampoEditarSocio"] = $r_campo_editar["IDCampoEditarUsuario"];
                                    $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                                    $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                                    $campo_editar["Valores"] = $r_campo_editar["Valores"];
                                    $campo_editar["PermiteEditar"] = $r_campo_editar["PermiteEditar"];
                                    //Consulto el valor de la actualización
                                    $ValorDatoCampo = $dbo->getFields("UsuarioCampoEditarUsuario", "Valor", "IDCampoEditarUsuario = '" . $r_campo_editar["IDCampoEditarUsuario"] . "' and IDUsuario = '" . $datos_usuario["IDUsuario"] . "'");
                                    if ($ValorDatoCampo != "" && $ValorDatoCampo != "false") {
                                        $ValorDato = $ValorDatoCampo;
                                    } else {
                                        $ValorDato = $datos_socio[$r_campo_editar["Nombre"]];
                                    }

                                    //$campo_editar[ "ValorActual" ] = $datos_socio[  $r_campo_editar[ "Nombre" ] ];
                                    $campo_editar["ValorActual"] = $ValorDato;

                                    $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                                    array_push($response_campo_editar, $campo_editar);
                                } //ednw while
                            }
                            $response["CampoEditar"] = $response_campo_editar;

                            if ($AppVersion >= 31) {
                                $respuesta = json_encode($response);
                                $param['key'] = KEY_API;
                                $param['nonce'] = sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES));
                                $param['msg'] = $respuesta;
                                $result = SIMUtil::cryptSodium($param);

                                //$response_encrip=array();
                                //$response_encrip[ "data" ] = $param['nonce'].sodium_bin2hex($result["cryptedText"]);
                                $respuesta = array();
                                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                                $respuesta["success"] = true;
                                $respuesta["response"] = $param['nonce'] . sodium_bin2hex($result["cryptedText"]);
                            } else {
                                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                                $respuesta["success"] = true;
                                $respuesta["response"] = $response;
                            }
                        }
                    }
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            }

            /*
                $sql = "SELECT * FROM Socio WHERE IDClub = '" . $IDClub . "' and IDSocio = '" . $IDSocio . "'";
                $qry = $dbo->query( $sql );
                if ( $dbo->rows( $qry ) > 0 ) {
    
                $message = $dbo->rows( $qry ) . " Encontrados";
                while ( $r = $dbo->fetchArray( $qry ) ) {
                $informacion[ "IDClub" ] = $r[ "IDClub" ];
                $informacion[ "IDSocio" ] = $r[ "IDSocio" ];
    
                if ( !empty( $r[ "Foto" ] ) ) {
                $foto = SOCIO_ROOT . $r[ "Foto" ];
                }
                $informacion[ "Foto" ] = $foto;
                $informacion[ "Accion" ] = $r[ "Accion" ];
                $informacion[ "DireccionDomicilio" ] = utf8_encode( $r[ "Direccion" ]);
                $informacion[ "Telefono" ] = utf8_encode( $r[ "Telefono" ]);
                $informacion[ "DireccionOficina" ] = utf8_encode( $r[ "DireccionOficina" ]);
                $informacion[ "TelefonoOficina" ] = utf8_encode( $r[ "TelefonoOficina" ]);
                $informacion[ "Celular" ] = utf8_encode( $r[ "Celular" ]);
                $informacion[ "CorreoElectronico" ] = utf8_encode( $r[ "CorreoElectronico" ]);
                array_push( $response, $informacion );
    
                } //ednw hile
                $respuesta[ "message" ] = $message;
                $respuesta[ "success" ] = true;
                $respuesta[ "response" ] = $response;
                } //End if
                else {
                $respuesta[ "message" ] = "No se encontraron registros";
                $respuesta[ "success" ] = true;
                $respuesta[ "response" ] = NULL;
                } //end else
                 */

            return $respuesta;
        } // fin function

        public function set_preferencias($IDClub, $IDSocio, $SeccionesContenido, $SeccionesEvento, $SeccionesGaleria, $SeccionesClasificado, $SeccionesContenido2, $SeccionesEvento2, $SeccionesContenido3, $IDUsuario)
        {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) {

                if (!empty($IDUsuario)) {
                    return SIMWebServiceUsuarios::set_preferencias_empleado($IDClub, $IDUsuario, $SeccionesContenido, $SeccionesEvento, $SeccionesGaleria);
                    exit;
                }

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {
                    //borro las secciones asociadas al socio
                    $sql_borra_seccion = $dbo->query("Delete From SocioSeccion Where IDSocio  = '" . $IDSocio . "'");

                    //borro las secciones asociadas al socio
                    $sql_borra_seccion = $dbo->query("Delete From SocioSeccion2 Where IDSocio  = '" . $IDSocio . "'");

                    //borro las secciones asociadas al socio
                    $sql_borra_seccion = $dbo->query("Delete From SocioSeccion3 Where IDSocio  = '" . $IDSocio . "'");

                    //borro las secciones asociadas al socio
                    $sql_borra_seccion_even = $dbo->query("Delete From SocioSeccionEvento Where IDSocio  = '" . $IDSocio . "'");

                    //borro las secciones asociadas al socio
                    $sql_borra_seccion_even = $dbo->query("Delete From SocioSeccionEvento2 Where IDSocio  = '" . $IDSocio . "'");

                    //borro las secciones asociadas al socio
                    $sql_borra_seccion_gal = $dbo->query("Delete From SocioSeccionGaleria Where IDSocio  = '" . $IDSocio . "'");

                    //borro las secciones asociadas al socio
                    $sql_borra_seccion_gal = $dbo->query("Delete From SocioSeccionClasificados Where IDSocio  = '" . $IDSocio . "'");

                    if (!empty($SeccionesContenido)) :
                        $array_secciones_not = explode(",", $SeccionesContenido);
                        if (count($array_secciones_not) > 0) :
                            foreach ($array_secciones_not as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("Seccion", "IDSeccion", "IDClub = '" . $IDClub . "' and IDSeccion = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_not = $dbo->query("Insert Into SocioSeccion (IDSocio, IDSeccion) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    if (!empty($SeccionesContenido2)) :
                        $array_secciones_not2 = explode(",", $SeccionesContenido2);
                        if (count($array_secciones_not2) > 0) :
                            foreach ($array_secciones_not2 as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("Seccion2", "IDSeccion", "IDClub = '" . $IDClub . "' and IDSeccion = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_not = $dbo->query("Insert Into SocioSeccion2 (IDSocio, IDSeccion) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    if (!empty($SeccionesContenido3)) :
                        $array_secciones_not3 = explode(",", $SeccionesContenido3);
                        if (count($array_secciones_not3) > 0) :
                            foreach ($array_secciones_not3 as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("Seccion3", "IDSeccion", "IDClub = '" . $IDClub . "' and IDSeccion = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_not = $dbo->query("Insert Into SocioSeccion3 (IDSocio, IDSeccion) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    if (!empty($SeccionesEvento)) :
                        $array_secciones_even = explode(",", $SeccionesEvento);
                        if (count($array_secciones_even) > 0) :
                            foreach ($array_secciones_even as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("SeccionEvento", "IDSeccionEvento", "IDClub = '" . $IDClub . "' and IDSeccionEvento = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_not = $dbo->query("Insert Into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    if (!empty($SeccionesEvento2)) :
                        $array_secciones_even = explode(",", $SeccionesEvento2);
                        if (count($array_secciones_even) > 0) :
                            foreach ($array_secciones_even as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("SeccionEvento2", "IDSeccionEvento2", "IDClub = '" . $IDClub . "' and IDSeccionEvento2 = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_not = $dbo->query("Insert Into SocioSeccionEvento2 (IDSocio, IDSeccionEvento2) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    if (!empty($SeccionesGaleria)) :
                        $array_secciones_gal = explode(",", $SeccionesGaleria);
                        if (count($array_secciones_gal) > 0) :
                            foreach ($array_secciones_gal as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("SeccionGaleria", "IDSeccionGaleria", "IDClub = '" . $IDClub . "' and IDSeccionGaleria = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_gal = $dbo->query("Insert Into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    if (!empty($SeccionesClasificado)) :
                        $array_secciones_cla = explode(",", $SeccionesClasificado);
                        if (count($array_secciones_cla) > 0) :
                            foreach ($array_secciones_cla as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("SeccionClasificados", "IDSeccionClasificados", "IDClub = '" . $IDClub . "' and IDSeccionClasificados = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_cla = $dbo->query("Insert Into SocioSeccionClasificados (IDSocio, IDSeccionClasificados) Values ('" . $IDSocio . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "7." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_preferencias_empleado($IDClub, $IDUsuario, $SeccionesContenido, $SeccionesEvento, $SeccionesGaleria)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDUsuario)) {

                //verifico que el Usuario exista y pertenezca al club
                $id_Usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_Usuario)) {
                    //borro las secciones asociadas al Usuario
                    $sql_borra_seccion = $dbo->query("Delete From UsuarioSeccion Where IDUsuario  = '" . $IDUsuario . "'");

                    //borro las secciones asociadas al Usuario
                    $sql_borra_seccion_even = $dbo->query("Delete From UsuarioSeccionEvento Where IDUsuario  = '" . $IDUsuario . "'");

                    //borro las secciones asociadas al Usuario
                    $sql_borra_seccion_gal = $dbo->query("Delete From UsuarioSeccionGaleria Where IDUsuario  = '" . $IDUsuario . "'");

                    if (!empty($SeccionesContenido)) :
                        $array_secciones_not = explode(",", $SeccionesContenido);
                        if (count($array_secciones_not) > 0) :
                            foreach ($array_secciones_not as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("Seccion", "IDSeccion", "IDClub = '" . $IDClub . "' and IDSeccion = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_not = $dbo->query("Insert Into UsuarioSeccion (IDUsuario, IDSeccion) Values ('" . $IDUsuario . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    if (!empty($SeccionesEvento)) :
                        $array_secciones_even = explode(",", $SeccionesEvento);
                        if (count($array_secciones_even) > 0) :
                            foreach ($array_secciones_even as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("SeccionEvento", "IDSeccionEvento", "IDClub = '" . $IDClub . "' and IDSeccionEvento = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_not = $dbo->query("Insert Into UsuarioSeccionEvento (IDUsuario, IDSeccionEvento) Values ('" . $IDUsuario . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    if (!empty($SeccionesGaleria)) :
                        $array_secciones_gal = explode(",", $SeccionesGaleria);
                        if (count($array_secciones_gal) > 0) :
                            foreach ($array_secciones_gal as $id_seccion) :
                                // verifico que la seccion sea del club
                                $id_seccion = $dbo->getFields("SeccionGaleria", "IDSeccionGaleria", "IDClub = '" . $IDClub . "' and IDSeccionGaleria = '" . $id_seccion . "'");
                                if (!empty($id_seccion)) :
                                    $sql_seccion_gal = $dbo->query("Insert Into UsuarioSeccionGaleria (IDUsuario, IDSeccionGaleria) Values ('" . $IDUsuario . "', '" . $id_seccion . "')");
                                endif;
                            endforeach;
                        endif;
                    endif;

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "Error el Usuario no existe o no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "7." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_socio_favorito($IDClub, $IDSocio, $SocioFavorito, $EstadoFavorito = "")
        {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {
                    //borro los favoritos del socio
                    //$sql_borra_favorito = $dbo->query("Delete From SocioFavorito Where IDSocio  = '".$IDSocio."'");

                    if (!empty($EstadoFavorito)) :
                        $array_socio_favorito_estado = explode(",", $EstadoFavorito);
                    endif;

                    if (!empty($SocioFavorito)) :
                        $array_socio_favorito = explode(",", $SocioFavorito);
                    endif;

                    $contador_socio = 0;
                    if (count($array_socio_favorito) > 0) :
                        foreach ($array_socio_favorito as $id_socio) :
                            if ($array_socio_favorito_estado[$contador_socio] == "S" && (int) $id_socio > 0) :
                                $inserta_socio_favorito = $dbo->query("Insert Into SocioFavorito (IDSocio, IDSocio2) Values ('" . $IDSocio . "', '" . $id_socio . "')");
                            elseif ($array_socio_favorito_estado[$contador_socio] == "N") :
                                $delete_socio_favorito = $dbo->query("Delete From SocioFavorito Where IDSocio = '" . $IDSocio . "' and IDSocio2 = '" . $id_socio . "'");
                            endif;
                            $contador_socio++;
                        endforeach;
                    endif;

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "8." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_foto_socio($IDClub, $IDSocio, $Archivo, $File = "")
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    //UPLOAD de imagenes

                    if (isset($File)) {

                        $files = SIMFile::upload($File["Archivo"], SOCIO_DIR, "IMAGE");
                        if (empty($files) && !empty($File["Archivo"]["name"])) :
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                        $Archivo = $files[0]["innername"];
                    } //end if

                    $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
                    if ($datos_club["PermiteCambioFotoPerfil"] == "S") {
                        $FotoActualizar = "S";
                    } else {
                        $FotoActualizar = "N";
                    }

                    if ($IDClub == 227) {

                        $url = SOCIO_ROOT . $Archivo;
                        $contenidoBinario = file_get_contents($url);
                        $foto = 1;
                        //base64_encode($contenidoBinario);

                        $sql_verifica = "SELECT * FROM Socio WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                        $qry_verifica = $dbo->query($sql_verifica);
                        $datos_socio = $dbo->fetchArray($qry_verifica);
                        $token = $datos_socio[TokenCountryMedellin];
                        //    $cambiarfoto= $datos_socio[FotoActualizadaSocio];  

                        if ($FotoActualizar == "N") {

                            $respuesta["message"] = "Lo sentimos, por el momento no es posible cambiar la foto de perfil";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }

                        require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
                        $resultado = SIMWebServiceCountryMedellin::App_ActualizarFotoPerfil($foto, $token);
                        $data = json_decode($resultado);
                        if ($data->estado == "true") {

                            $sql_actualiza_foto = $dbo->query("Update Socio Set Foto = '" . $Archivo . "', SolicitarCambioFotoPerfil = 'N', FotoActualizadaSocio = '" . $FotoActualizar . "', FechaActualizacionFoto = NOW() Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                            $respuesta["message"] = $data->mensaje;
                            $respuesta["success"] = true;
                            $respuesta["response"] = null;
                        } else {
                            $respuesta["message"] = $data->mensaje;
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } else {


                        $sql_actualiza_foto = $dbo->query("Update Socio Set Foto = '" . $Archivo . "', SolicitarCambioFotoPerfil = 'N', FotoActualizadaSocio = '" . $FotoActualizar . "', FechaActualizacionFoto = NOW() Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                        $respuesta["message"] = "Tu foto ha sido guardada con exito";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "13." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_foto_empleado($IDClub, $IDUsuario, $Archivo, $File = "")
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDUsuario)) {

                //verifico que el socio exista y pertenezca al club
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
                if ($datos_club["PermiteCambioFotoPerfil"] == "S") {
                    $FotoActualizar = "S";
                } else {
                    $FotoActualizar = "N";
                }

                if (!empty($id_usuario)) {

                    if (isset($File)) {

                        $files = SIMFile::upload($File["Archivo"], USUARIO_DIR, "IMAGE");
                        if (empty($files) && !empty($File["Archivo"]["name"])) :
                            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Haocurridounerrordurantelacargadelaimagen.Verifiquequelaimagennocontengaerroresyqueeltipodearchivoseapermitido.', LANG);
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;
                        $Archivo = $files[0]["innername"];
                    } //end if

                    $sql_actualiza_foto = $dbo->query("Update Usuario Set Foto = '" . $Archivo . "', FotoActualizadaEmpleado = '" . $FotoActualizar . "', FechaActualizacionFoto = NOW() Where IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");
                    $respuesta["message"] = "foto guardada";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "13." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function get_actualizar_foto_socio($IDClub, $IDSocio)
        {
            $dbo = &SIMDB::get();
            $response = array();
            $sql = "SELECT * FROM Socio WHERE IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' ORDER BY RAND()";
            $CambiarFoto = $dbo->getFields("Club", "PermiteCambioFotoPerfil", "IDClub = '" . $IDClub . "' ");
            if ($CambiarFoto == "S") {
                $respuesta["message"] = "ok, puede actualizar foto";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {

                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0 || $CambiarFoto == "S") {
                    $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                    while ($r = $dbo->fetchArray($qry)) {
                        if ($r["FotoActualizadaSocio"] == "N") :

                            if ($IDClub == 227) :
                                $respuesta["message"] = "Lo sentimos, por el momento no es posible actualizar la foto de perfil.";
                            else :
                                $respuesta["message"] = "Para actualizar la foto contáctese con Soporte";
                            endif;
                            // $respuesta["message"] = "Para actualizar la foto contáctese con Soporte";
                            $respuesta["success"] = false;
                            $respuesta["response"] = $response;
                        else :
                            $respuesta["message"] = "ok, puede actualizar foto";
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        endif;
                    } //ednw hile

                } //End if
                else {
                    $respuesta["message"] = "No se ha encontrado socio";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else
            }

            return $respuesta;
        }

        public function get_actualizar_foto_empleado($IDClub, $IDUsuario)
        {
            $dbo = &SIMDB::get();
            $response = array();
            $sql = "SELECT * FROM Usuario WHERE IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "' ORDER BY RAND()";
            $CambiarFoto = $dbo->getFields("Club", "PermiteCambioFotoPerfil", "IDClub = '" . $IDClub . "' ");
            if ($CambiarFoto == "S") {
                $respuesta["message"] = "ok, puede actualizar foto";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {

                $qry = $dbo->query($sql);
                if ($dbo->rows($qry) > 0) {
                    $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                    while ($r = $dbo->fetchArray($qry)) {
                        if ($r["FotoActualizadaEmpleado"] == "N") :
                            $respuesta["message"] = "Lo sentimos la foto ya fue actualizada";
                            $respuesta["success"] = false;
                            $respuesta["response"] = $response;
                        else :
                            $respuesta["message"] = "ok, puede actualizar foto";
                            $respuesta["success"] = true;
                            $respuesta["response"] = $response;
                        endif;
                    } //ednw hile

                } //End if
                else {
                    $respuesta["message"] = "No se ha encontrado usuario";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end else

            }



            return $respuesta;
        }

        public function valida_segunda_clave($IDClub, $IDSocio, $Clave)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDSocio)) {

                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "' and SegundaClave = '" . sha1($Clave) . "' and (IDEstadoSocio <> 2 and IDEstadoSocio <> 3 )", "array");
                if (!empty($datos_socio["IDSocio"]) && $datos_socio["TipoSocio"] != "Estudiante") {
                    $response["IDClub"] = $datos_socio["IDClub"];
                    $response["IDSocio"] = $datos_socio["IDSocio"];
                    $response["Socio"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                } else {
                    $respuesta["message"] = "Segunda Clave Incorrecta";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "25." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_cambiar_clave($IDClub, $IDSocio, $Clave = "", $SegundaClave = "", $Correo = "", $ClaveAnterior)
        {
            $dbo = &SIMDB::get();


            $longitudclave = strlen($Clave);
            if ($longitudclave <= 6 and $IDClub == 227) {
                /*  require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
             $familiares="true";
             $codigoMesa="-1";
             $sala="2";
             $token="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJDb2RTb2NpbyI6IjcwMjgwNiIsIk5vbWJyZSI6Im5vbWJyZSJ9.hgPGWoWVqwBuhkz8C96BKe-u0RS_bfuNGC4qdcF4QaE";
             
                $resultado = SIMWebServiceCountryMedellin::App_ConsultarMesasAbiertas($familiares,$codigoMesa,$sala,$token);

                $data11 = json_decode($resultado); 
           
               
                    $respuesta["message"] =$data11;
            $respuesta["success"] = false;
            $respuesta["response"] = null;
                    return $respuesta;*/

                $respuesta["message"] = "Lo sentimos, por politicas de seguridad su contraseña debe ser mayor a 6 caracteres";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                return $respuesta;
            }
            if (!empty($IDClub) && !empty($IDSocio) && !empty($Clave)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    if (!empty($Correo)) {

                        //actualizo el email por que correoelectronico es el campo que se usa para ingresar a la app contry medellin
                        if ($IDClub == 227) {

                            $sql_cambiar_correo = "Update Socio Set Email= '" . $Correo . "' Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                            $dbo->query($sql_cambiar_correo);
                        } else {
                            $sql_cambiar_correo = "Update Socio Set CorreoElectronico= '" . $Correo . "' Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                            $dbo->query($sql_cambiar_correo);
                        }
                    }

                    if (!empty($Clave)) :

                        $sql_cambiar = "Update Socio Set Clave =  sha1('" . $Clave . "'), CambioClave = 'N' Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                        $dbo->query($sql_cambiar);

                        if ($IDClub == "51") { // Si es Condado lo actualizo por el ws
                            $cambio = SIMWebServiceUsuarios::cambio_clave_condado($IDClub, $IDSocio, $Clave);
                        }


                        if ($IDClub == 227) {

                            require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";

                            $query = $dbo->query("SELECT * FROM Socio WHERE IDSocio='$IDSocio' AND IDClub='$IDClub'");



                            while ($row = mysqli_fetch_array($query)) {
                                //$Claveant= $row["Clave"];
                                $tokencountry = $row["TokenCountryMedellin"];
                            }
                            $token = urlencode($tokencountry); /*  
         $token1=urlencode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJDb2RTb2NpbyI6IjcwMjgwNiIsIk5vbWJyZSI6Im5vbWJyZSJ9.hgPGWoWVqwBuhkz8C96BKe-u0RS_bfuNGC4qdcF4QaE");
         
         //aseguramos el cambio de contraseña una vez a cada uno de los usuaios (2)
                    if($token1==$token){
                    $Claveanterior="702806802";
                      }
                    else{
                    $Claveanterior="702805305";
                    } 
      /*  $email=urlencode("702806");
        $Clave=urlencode("702806802");
        $Clavenueva=urlencode("702806802");
         $token=urlencode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJDb2RTb2NpbyI6IjcwMjgwNiIsIk5vbWJyZSI6Im5vbWJyZSJ9.hgPGWoWVqwBuhkz8C96BKe-u0RS_bfuNGC4qdcF4QaE");
                 */
                            $resultados = SIMWebServiceCountryMedellin::App_CambiarClave($ClaveAnterior, $Clave, $token);
                            $data = json_decode($resultados);

                            if ($data->estado == "true") {
                                $respuesta["message"] = $data->mensaje;
                                $respuesta["success"] = true;
                                $respuesta["response"] = null;
                                return $respuesta;
                            } else {
                                $respuesta["message"] = $data->mensaje;
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            }
                        }


                        if ($IDClub == "380") : // Si es Club Colombia tambien la actualizo por el ws
                            require_once LIBDIR . "SIMWebServiceClubColombia.inc.php";
                            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                            $token = SIMWebServiceClubColombia::obtener_token_colombia($datos_socio["Email"], base64_decode($datos_socio["ClaveSistemaExterno"]));
                            if (!empty($token)) :
                                $cambio_clave = SIMWebServiceClubColombia::set_cambio_clave_colombia($token, $Clave);
                                if (!empty($Correo)) {
                                    $cambio_correo = SIMWebServiceClubColombia::set_email_colombia($token, $Correo);
                                }

                            endif;
                        endif;

                    endif;

                    if (!empty($SegundaClave)) :
                        $sql_cambiar = "Update Socio Set SegundaClave =  sha1('" . $SegundaClave . "'), CambioSegundaClave = 'N',CorreoElectronico= '" . $Correo . "' Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                        $dbo->query($sql_cambiar);
                    endif;

                    $respuesta["message"] = "clave modificada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "25." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_cambiar_segunda_clave($IDClub, $IDSocio, $Clave, $Correo = "")
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($Clave)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    $sql_cambiar = "Update Socio Set SegundaClave =  sha1('" . $Clave . "'), CambioSegundaClave = 'N', CorreoElectronico= '" . $Correo . "' Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($sql_cambiar);

                    $respuesta["message"] = "Segunda clave modificada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "25." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_cambiar_clave_empleado($IDClub, $IDUsuario, $Clave)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDUsuario) && !empty($Clave)) {

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_socio)) {

                    $sql_cambiar = "Update Usuario Set Password =  sha1('" . $Clave . "') Where IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($sql_cambiar);

                    $respuesta["message"] = "clave modificada con exito";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelusuarionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "25." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_token($IDClub, $IDSocio, $Dispositivo, $Token, $TokenIBM = "", $UserIDIBM = "")
        {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && (!empty($Token) || !empty($TokenIBM))) {

                //verifico que el socio exista y pertenezca al club
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' and IDCLub = '" . $IDClub . "' ", "array");

                //quito carceter especial token
                $Token = str_replace(">", "", $Token);

                if (!empty($datos_socio["IDSocio"])) {

                    if ($datos_socio["FechaPrimerIngreso"] == "0000-00-00 00:00:00") :
                        $actualiza_primer_ingreso = " , FechaPrimerIngreso =  '" . date("Y-m-d H:i:s") . "' ";
                    // al primer ingreso actualizo todas las secciones activas de noticias
                    //SIMUtil::actualiza_secciones_socio($IDClub, $IDSocio);

                    endif;



                    $sql_seccion_not = $dbo->query("UPDATE Socio Set Dispositivo = '" . $Dispositivo . "', Token = '" . $Token . "', TokenIBM = '" . $TokenIBM . "' " . $actualiza_primer_ingreso . " Where IDSocio = '" . $IDSocio . "'");

                    //Guardo Token
                    $sql_token_socio = $dbo->query("INSERT IGNORE INTO SocioToken (IDSocio,IDClub,Dispositivo,Token,Fecha) VALUES ('" . $IDSocio . "','" . $IDClub . "','" . $Dispositivo . "','" . $Token . "',NOW()) ");

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelsocionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "26." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_token_empleado($IDClub, $IDUsuario, $Dispositivo, $Token)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDUsuario) && !empty($Token)) {

                //verifico que el socio exista y pertenezca al club
                $id_usuario = $dbo->getFields("Usuario", "IDUsuario", "IDUsuario = '" . $IDUsuario . "' and IDClub = '" . $IDClub . "'");

                if (!empty($id_usuario)) {

                    $sql_seccion_not = $dbo->query("Update Usuario Set Dispositivo = '" . $Dispositivo . "', Token = '" . $Token . "' Where IDUsuario = '" . $id_usuario . "'");

                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Errorelusuarionoexisteonopertenecealclub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "26." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function valida_cantidad_ingresos($IDClub, $IDSocio)
        {
            $dbo = &SIMDB::get();
            $Mes = date("m");
            $Year = date("Y");
            $sql = "SELECT IDLogAcceso FROM LogAcceso WHERE IDInvitacion = '" . $IDSocio . "' and Tipo = 'Socio' and MONTH(FechaIngreso) ='" . $Mes . "' and YEAR(FechaIngreso)='" . $Year . "'  ";
            $r = $dbo->query($sql);
            $total = $dbo->rows($r);
            return $total;
        }

        // NUEVAS


        public function valida_usuario_web($email, $clave, $id_club, $id_club_consulta = "", $AppVersion)
        {

            require_once LIBDIR . "SIMWebServiceClub.inc.php";

            $dbo = &SIMDB::get();
            $foto_cod_barras = "";

            if (empty($id_club_consulta)) {
                $id_club_consulta = $id_club;
            }

            if (!empty($email) && !empty($clave)) {

                //$sql_verifica = "SELECT * FROM Usuario WHERE User = '".$email ."' and Password = '".sha1($clave )."' and IDClub = '".$id_club."' and Activo <> 'N'";
                $sql_verifica = "SELECT * FROM Usuario WHERE User = '" . $email . "' and Password = '" . sha1($clave) . "' and IDClub in (" . $id_club_consulta . ") and Activo <> 'N'";

                $qry_verifica = $dbo->query($sql_verifica);
                if ($dbo->rows($qry_verifica) == 0) {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noencontrado', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end if
                else {
                    $datos_usuario = $dbo->fetchArray($qry_verifica);

                    $CerrarSesion = $datos_usuario["SolicitarCierreSesion"];
                    if ($CerrarSesion == "S") {
                        $condicion_modulo = " and IDModulo = 14 "; //cerrar sesion
                    }

                    //Modulos Sistema Menu Central
                    $response_modulo = array();
                    $sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '" . $id_club . "' and Activo = 'S' and Ubicacion like '%Central%' " . $condicion_modulo . " ORDER BY Orden";
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {
                        while ($r_modulo = $dbo->fetchArray($qry_modulo)) {

                            $agregar_modulo = SIMWebServiceClub::verifica_permiso_modulo($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);

                            if ($agregar_modulo == "S") :
                                // Verificar si el modulo tiene contenido para mostrar
                                $flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
                                //$flag_mostrar=0;
                                if ($flag_mostrar == 0) :
                                    $modulo["IDClub"] = $datos_usuario["IDClub"];
                                    $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                    if (!empty($r_modulo["Titulo"])) {
                                        $modulo["NombreModulo"] = trim($r_modulo["Titulo"]);
                                    } else {
                                        $modulo["NombreModulo"] = trim($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                                    }

                                    $modulo["Orden"] = $r_modulo["Orden"];
                                    $icono_modulo = $r_modulo["Icono"];
                                    if (!empty($r_modulo["Icono"])) :
                                        $foto = MODULO_ROOT . $r_modulo["Icono"];
                                    else :
                                        $foto = "";
                                    endif;
                                    $modulo["Icono"] = $foto;
                                    $modulo["MostrarBadgeNotificaciones"] = $r_modulo["MostrarBadgeNotificaciones"];
                                    array_push($response_modulo, $modulo);
                                endif;
                            endif;
                        } //ednw while
                    }

                    //Modulos Sistema Menu Lateral
                    unset($modulo);
                    $response_modulo_lateral = array();
                    $sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '" . $id_club . "' and Activo = 'S' and Ubicacion like '%Lateral%'  " . $condicion_modulo . " ORDER BY Orden";
                    $qry_modulo = $dbo->query($sql_modulo);
                    if ($dbo->rows($qry_modulo) > 0) {

                        while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                            $agregar_modulo = SIMWebServiceClub::verifica_permiso_modulo($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);
                            $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $r_modulo["IDModulo"] . "' ", "array");

                            if ($agregar_modulo == "S") :
                                // Verificar si el modulo tiene contenido para mostrar
                                $flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $id_club);
                                //$flag_mostrar=0;
                                if ($flag_mostrar == 0) :
                                    $modulo["IDClub"] = $datos_usuario["IDClub"];
                                    $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                    //$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));
                                    if (!empty($r_modulo["Titulo"])) {
                                        $modulo["NombreModulo"] = $r_modulo["Titulo"];
                                    } else {
                                        $modulo["NombreModulo"] = $dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'");
                                    }

                                    $modulo["Orden"] = $r_modulo["Orden"];
                                    $icono_modulo = $r_modulo["Icono"];
                                    if (!empty($r_modulo["Icono"])) :
                                        $foto = MODULO_ROOT . $r_modulo["Icono"];
                                    else :
                                        $foto = "";
                                    endif;
                                    $modulo["Icono"] = $foto;
                                    $modulo["Tipo"] = $datos_modulo["Tipo"];
                                    $modulo["MostrarBadgeNotificaciones"] = $r_modulo["MostrarBadgeNotificaciones"];
                                    array_push($response_modulo_lateral, $modulo);
                                endif;
                            endif;
                        } //ednw while
                    }

                    //traer servicios del usuario
                    $response_servicio = array();
                    $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $datos_usuario["IDUsuario"] . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
                    $qry_servicios = $dbo->query($sql_servicios);
                    while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
                        $servicio["IDClub"] = $datos_usuario["IDClub"];
                        $servicio["IDServicio"] = $r_servicio["IDServicio"];
                        $servicio["NombreServicio"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $r_servicio["IDServicioMaestro"] . "' ");
                        if (!empty($r_servicio["Icono"])) :
                            $foto = SERVICIO_ROOT . $r_servicio["Icono"];
                        else :
                            $foto = "";
                        endif;

                        $servicio["Icono"] = $foto;
                        //$servicio["ServicioInicial"] = $dbo->getFields( "ServicioInicial" , "Nombre" , "IDServicioInicial = '".$r_servicio["IDServicioInicial"]."'" );
                        array_push($response_servicio, $servicio);
                    } //end while

                    $tipo_codigo_carne = $dbo->getFields("AppEmpleado", "TipoCodigoCarne", "IDClub = '" . $id_club . "'");
                    switch ($tipo_codigo_carne) {
                        case "Barras":
                            if (!empty($datos_usuario["CodigoBarras"])) {
                                $foto_cod_barras = USUARIO_ROOT . $datos_usuario["CodigoBarras"];
                            }
                            break;
                        case "QR":
                            if (!empty($datos_usuario["CodigoQR"])) {
                                $foto_cod_barras = USUARIO_ROOT . "qr/" . $datos_usuario["CodigoQR"];
                            }
                            break;
                        default:
                            $foto_cod_barras = "";
                    }

                    if (!empty($datos_usuario["Foto"])) {
                        $foto_empleado = USUARIO_ROOT . $datos_usuario["Foto"];
                    }


                    //Preferencias Contenido
                    $response_seccion_contenido = array();
                    $sql_seccioncontenido = $dbo->query("Select * From UsuarioSeccion Where IDSocio = '" . $datos_usuario["IDUsuario"] . "'");
                    while ($r_seccioncontenido = $dbo->fetchArray($sql_seccioncontenido)) :
                        $seccion_contenido[IDUsuario] = $datos_usuario["IDUsuario"];
                        $seccion_contenido[IDClub] = $datos_usuario["IDClub"];
                        $seccion_contenido[IDSeccion] = $r_seccioncontenido["IDSeccion"];
                        array_push($response_seccion_contenido, $seccion_contenido);

                    endwhile;

                    //Preferencias Eventos
                    $response_seccion_evento = array();
                    $sql_seccionevento = $dbo->query("Select * From UsuarioSeccionEvento Where IDSocio = '" . $datos_usuario["IDUsuario"] . "'");
                    while ($r_seccionevento = $dbo->fetchArray($sql_seccionevento)) :
                        $seccion_evento[IDUsuario] = $datos_usuario["IDUsuario"];
                        $seccion_evento[IDClub] = $datos_usuario["IDClub"];
                        $seccion_evento[IDSeccionEvento] = $r_seccionevento["IDSeccionEvento"];
                        array_push($response_seccion_evento, $seccion_evento);
                    endwhile;
                    //Preferencias Galerias
                    $response_seccion_galeria = array();
                    $sql_secciongaleria = $dbo->query("Select * From UsuarioSeccionGaleria Where IDSocio = '" . $datos_usuario["IDUsuario"] . "'");
                    while ($r_secciongaleria = $dbo->fetchArray($sql_secciongaleria)) :
                        $seccion_galeria[IDUsuario] = $datos_usuario["IDUsuario"];
                        $seccion_galeria[IDClub] = $datos_usuario["IDClub"];
                        $seccion_galeria[IDSeccionEvento] = $r_secciongaleria["IDSeccionGaleria"];
                        array_push($response_seccion_galeria, $seccion_galeria);
                    endwhile;


                    $response["IDClub"] = $datos_usuario["IDClub"];
                    $response["IDUsuario"] = $datos_usuario["IDUsuario"];
                    $response["IDPerfil"] = $datos_usuario["IDPerfil"];
                    $response["Nombre"] = $datos_usuario["Nombre"];
                    $response["Autorizado"] = $datos_usuario["Autorizado"];
                    $response["Nivel"] = $datos_usuario["Nivel"];
                    $response["Permiso"] = $datos_usuario["Permiso"];
                    $response["ServiciosReserva"] = $response_servicio;
                    /*
                        $response["PreferenciasContenido"] = $response_seccion_contenido;
                        $response["PreferenciasGaleria"] = $response_seccion_galeria;
                        $response["PreferenciasEvento"] = $response_seccion_evento;
                        */
                    $response["Modulos"] = $response_modulo;
                    $response["ModulosLateral"] = $response_modulo_lateral;
                    $response["CodigoBarras"] = $foto_cod_barras;
                    $response["Dispositivo"] = $datos_usuario["Dispositivo"];
                    $response["Token"] = $datos_usuario["Token"];
                    $response["ColorFondoCarne"] = $dbo->getFields("AppEmpleado", "ColorFondoCarne", "IDClub = '" . $id_club . "'");
                    //$response["NumeroDerecho"] = $datos_usuario["CodigoUsuario"];
                    $response["NumeroDerecho"] = "";
                    //Consulto si el app esta configurado para permitir se puede cambiar p[ara que sea por usuario
                    $response["PermiteInvitacionPortero"] = $dbo->getFields("AppEmpleado", "PermiteInvitacionPortero", "IDClub = '" . $id_club . "'");
                    //Consulto las areas
                    $sql_area_usuario = "Select * From UsuarioArea Where IDUsuario = '" . $datos_usuario["IDUsuario"] . "'";
                    $result_area_usuario = $dbo->query($sql_area_usuario);
                    while ($row_area = $dbo->fetchArray($result_area_usuario)) :
                        $nombre_area = utf8_encode($dbo->getFields("Area", "Nombre", "IDArea = '" . $row_area["IDArea"] . "'"));
                        $array_areas[] = $nombre_area;
                    endwhile;
                    if (count($array_areas) > 0) :
                        $nombre_areas = implode(",", $array_areas);
                    endif;

                    $nombre_areas = "";
                    $response["Area"] = $nombre_areas;
                    $response["Cargo"] = utf8_encode($datos_usuario["Cargo"]);
                    $response["Codigo"] = $datos_usuario["CodigoUsuario"];
                    $response["PermiteReservar"] = $datos_usuario["PermiteReservar"];
                    $response["Activo"] = $datos_usuario["Activo"];
                    $response["Foto"] = $foto_empleado;
                    $response["TipoUsuario"] = 'Empleado';

                    //Token Usuario
                    $TokenUsuario = $datos_usuario["IDUsuario"] . "-" . date("Ymd-s") . "-" . bin2hex(openssl_random_pseudo_bytes((70 - ($longitud % 2)) / 2));
                    $sql_token = "UPDATE UsuarioTokenSesion  SET Activo  = 0 WHERE IDUsuario = '" . $datos_usuario["IDUsuario"] . "'";
                    $dbo->query($sql_token);
                    $sql_asigna_token = "INSERT UsuarioTokenSesion (IDUsuario, IDClub, Dispositivo, IDentificador, Modelo, Token, Fecha, Activo ) VALUES('" . $datos_usuario["IDUsuario"] . "','" . $datos_usuario["IDClub"] . "', '" . $Dispositivo . "', '" . $Identificador . "', '" . $Modelo . "', '" . $TokenUsuario . "', NOW(),1)";
                    $dbo->query($sql_asigna_token);
                    $response["TokenSesion"] = $TokenUsuario;
                    // FIN TOKEN

                    //Encuestas al abrir app
                    $encuesta_activa = 0;
                    $response_encuesta = array();
                    $sql_encuesta = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $id_club . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'E' or DirigidoA = 'T')  ORDER BY Orden ";
                    $qry_encuesta = $dbo->query($sql_encuesta);
                    if ($dbo->rows($qry_encuesta) > 0) {
                        while ($r_encuesta = $dbo->fetchArray($qry_encuesta)) {
                            $mostrar_encuesta = 0;
                            //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                            if ($r_encuesta["UnaporSocio"] == "S") {
                                $sql_resp = "Select IDEncuesta From EncuestaRespuesta Where IDSocio = '" . $datos_usuario["IDUsuario"] . "' and IDEncuesta = '" . $r_encuesta["IDEncuesta"] . "' Limit 1";
                                $r_resp = $dbo->query($sql_resp);
                                if ($dbo->rows($r_resp) <= 0) {
                                    $mostrar_encuesta = 1;
                                }
                            } else {
                                $mostrar_encuesta = 1;
                            }
                            //Verifico si la encuesta es solo para algunos socios para mostrar o no
                            //$permiso_encuesta=SIMWebServiceApp::verifica_ver_encuesta($r_encuesta,$IDSocio);
                            $permiso_encuesta = 1;

                            if ($mostrar_encuesta == 1 && $permiso_encuesta == 1) {
                                $encuesta["IDClub"] = $r_encuesta["IDClub"];
                                $encuesta["IDEncuesta"] = $r_encuesta["IDEncuesta"];
                                $encuesta["Nombre"] = $r_encuesta["Nombre"];
                                $encuesta["Descripcion"] = $r_encuesta["Descripcion"];
                                if (!empty($r_encuesta["Imagen"])) :
                                    $foto = BANNERAPP_ROOT . $r_encuesta["Imagen"];
                                else :
                                    $foto = "";
                                endif;
                                $encuesta["ImagenEncuesta"] = $foto;
                                $encuesta_activa = 1;

                                array_push($response_encuesta, $encuesta);
                            }
                        } //ednw while
                    }
                    //FIN Encuestas al abrir app
                    $response["Encuesta"] = $response_encuesta;
                    $response["LabelEncuesta"] = SIMUtil::get_traduccion('', '', 'Encuesta', LANG);

                    //Autodisagnostico al abrir app
                    $diagnostico_activa = 0;
                    $response_diagnostico = array();
                    $sql_diagnostico = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $id_club . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'E' or DirigidoA = 'T')  ORDER BY Orden ";
                    $qry_diagnostico = $dbo->query($sql_diagnostico);
                    if ($dbo->rows($qry_diagnostico) > 0) {
                        while ($r_diagnostico = $dbo->fetchArray($qry_diagnostico)) {
                            $mostrar_disgnostico = 0;
                            //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                            if ($r_diagnostico["UnaporSocio"] == "S") {
                                $sql_resp = "Select IDDiagnostico From DiagnosticoRespuesta Where IDUsuario = '" . $IDUsuario . "' and IDDiagnostico = '" . $r_diagnostico["IDDiagnostico"] . "' Limit 1";
                                $r_resp = $dbo->query($sql_resp);
                                if ($dbo->rows($r_resp) <= 0) {
                                    $mostrar_disgnostico = 1;
                                }
                            } else {
                                $mostrar_disgnostico = 1;
                            }
                            //Verifico si la encuesta es solo para algunos socios para mostrar o no
                            $permiso_diagnostico = SIMWebServiceApp::verifica_ver_diagnostico($r_diagnostico, $IDSocio, $IDUsuario);
                            //$permiso_encuesta=1;

                            if ($mostrar_disgnostico == 1 && $permiso_diagnostico == 1) {
                                $diagnostico["IDClub"] = $r_diagnostico["IDClub"];
                                $diagnostico["IDDiagnostico"] = $r_diagnostico["IDDiagnostico"];
                                $diagnostico["Nombre"] = $r_diagnostico["Nombre"];
                                $diagnostico["Descripcion"] = $r_diagnostico["Descripcion"];
                                if (!empty($r_diagnostico["Imagen"])) :
                                    $foto = BANNERAPP_ROOT . $r_diagnostico["Imagen"];
                                else :
                                    $foto = "";
                                endif;
                                $diagnostico["ImagenDiagnostico"] = $foto;
                                $diagnostico_activa = 1;

                                array_push($response_diagnostico, $diagnostico);
                            }
                        } //ednw while
                    }
                    //FIN Encuestas al abrir app
                    $response["Diagnostico"] = $response_diagnostico;
                    $response["LabelDiagnostico"] = SIMUtil::get_traduccion('', '', 'Diligenciarauto', LANG);

                    $response_campo_editar = array();
                    $sql_campo_editar = "SELECT CEU.* FROM CampoEditarUsuario CEU
                                                                            WHERE CEU.IDClub = '" . $datos_usuario["IDClub"] . "' ORDER BY CEU.Orden";

                    $qry_campo_editar = $dbo->query($sql_campo_editar);
                    if ($dbo->rows($qry_campo_editar) > 0) {
                        while ($r_campo_editar = $dbo->fetchArray($qry_campo_editar)) {
                            $campo_editar["IDCampoEditarSocio"] = $r_campo_editar["IDCampoEditarUsuario"];
                            $campo_editar["Nombre"] = $r_campo_editar["Nombre"];
                            $campo_editar["Tipo"] = $r_campo_editar["Tipo"];
                            $campo_editar["Valores"] = $r_campo_editar["Valores"];
                            $campo_editar["PermiteEditar"] = $r_campo_editar["PermiteEditar"];
                            //Consulto el valor de la actualización
                            $ValorDatoCampo = $dbo->getFields("UsuarioCampoEditarUsuario", "Valor", "IDCampoEditarUsuario = '" . $r_campo_editar["IDCampoEditarUsuario"] . "' and IDUsuario = '" . $datos_usuario["IDUsuario"] . "'");
                            if ($ValorDatoCampo != "" && $ValorDatoCampo != "false") {
                                $ValorDato = $ValorDatoCampo;
                            } else {
                                $ValorDato = $datos_socio[$r_campo_editar["Nombre"]];
                            }

                            //$campo_editar[ "ValorActual" ] = $datos_socio[  $r_campo_editar[ "Nombre" ] ];
                            $campo_editar["ValorActual"] = $ValorDato;

                            $campo_editar["Obligatorio"] = $r_campo_editar["Obligatorio"];
                            array_push($response_campo_editar, $campo_editar);
                        } //ednw while
                    }
                    $response["CampoEditar"] = $response_campo_editar;

                    if ($AppVersion >= 31) {
                        $respuesta = json_encode($response);
                        $param['key'] = KEY_API;
                        $param['nonce'] = sodium_bin2hex(random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES));
                        $param['msg'] = $respuesta;
                        $result = SIMUtil::cryptSodium($param);

                        //$response_encrip=array();
                        //$response_encrip[ "data" ] = $param['nonce'].sodium_bin2hex($result["cryptedText"]);
                        $respuesta = array();
                        $respuesta["message"] = 'ok';
                        $respuesta["success"] = true;
                        $respuesta["response"] = $param['nonce'] . sodium_bin2hex($result["cryptedText"]);
                    } else {
                        $respuesta["message"] = 'ok';
                        $respuesta["success"] = true;
                        $respuesta["response"] = $response;
                    }

                    //$respuesta["message"] = "ok";
                    //$respuesta["success"] = true;
                    //$respuesta["response"] = $response;
                }
            } else {
                $respuesta["message"] = "1." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;
        } //end function

        public function solicitar_cerrar_sesion($IDClub)
        {
            $dbo = &SIMDB::get();
            $nowserver = date("Y-m-d H:i:s");
            $respuesta["message"] = "Debe cerrar sesion e ingresar nuevamente";
            $respuesta["success"] = true;
            $respuesta["response"] = "Debe cerrar sesion e ingresar nuevamente";
            die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
            exit;
        }

        public function set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio = "", $Categoria = "", $Masivo = "", $CodigoCarne, $ClaveApp = "", $IDSocioSistemaExterno = "", $array_socios = "", $PermiteReservar = "", $SocioMora = "")
        {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($NumeroDocumento) && !empty($Nombre)) {

                // DESHABILITADO PARA LA HACIENDA FONTANAR
                if ($IDClub == 18) :
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'NoestapermitidalacreacióndeusuariosparaHaciendaFontanarporelServicioWebdeMiClub', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;

                    return $respuesta;
                endif;

                $Documento = trim($row["NumeroDocumento"]);
                $Nombre = str_replace("'", "", $Nombre);
                $Apellido = str_replace("'", "", $Apellido);
                //Consulto si el socio ya existe
                if (count($array_socios) > 0 && is_array($array_socios)) {
                    $con_array = "S";
                    if ((int) $array_socios[trim($NumeroDocumento)] > 0) {
                        $row_socio["IDSocio"] = $array_socios[trim($NumeroDocumento)];
                    }
                } else {
                    $con_array = "N";
                    $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'";
                    $result_socio = $dbo->query($sql_socio);
                    $row_socio = $dbo->fetchArray($result_socio);
                }

                //Estado Socio
                if ($EstadoSocio == "A") :
                    $estado_socio = 1;
                else :
                    $estado_socio = 2;
                endif;

                if ($IDClub == 70) {
                    $estado_socio = $EstadoSocio;

                    $con_array = "N";
                    $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' and Accion = '" . $Accion . "'";
                    $result_socio = $dbo->query($sql_socio);
                    $row_socio = $dbo->fetchArray($result_socio);
                }

                if ($IDClub == 9) :
                    $con_array = "N";
                    $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' and Accion = '" . $Accion . "'";
                    $result_socio = $dbo->query($sql_socio);
                    $row_socio = $dbo->fetchArray($result_socio);
                endif;

                if (empty($UsuarioApp)) :
                    $UsuarioApp = $NumeroDocumento;
                endif;

                if (empty($ClaveApp)) {
                    $clave_socio = sha1(trim($NumeroDocumento));
                } else {
                    $clave_socio = $ClaveApp;
                }

                if (empty($PermiteReservar)) {
                    $Permite_Reservar = 'S';
                } else {
                    $Permite_Reservar = $PermiteReservar;
                }

                if ($IDClub == 135) {

                    $Permite_Reservar = 'S';
                    $estado_socio = $EstadoSocio;

                    if ($PermiteReservar == 'S')
                        $PermiteServicio = 5;
                    else
                        $PermiteServicio = 6;

                    $actulizaPermiteServicio = ", IDPermisoServicio = '" . $PermiteServicio . "' ";

                    if ($SocioMora == 'M') :
                        $Permite_Reservar = 'N';
                    endif;
                }

                //$id_perentesco=$dbo->getFields( "Parentesco" , "IDParentesco" , "Nombre = '" . $Parentesco . "'" );

                if (!empty($Categoria)) {
                    $id_categoria = $dbo->getFields("Categoria", "IDCategoria", "IDSistemaExterno = '" . $Categoria . "' and IDClub = '" . $IDClub . "'");
                    if (!empty($id_categoria)) {
                        $id_categoria = $Categoria;
                    }
                }

                if ($IDClub == 135) {
                    $id_categoria = $Categoria;
                }

                if ($IDClub == 130) {
                    $CambiarClave = 'N';
                } else {
                    $CambiarClave = 'S';
                }

                if ($IDClub == 141) {
                    $estado_socio = $EstadoSocio;
                }

                if ($IDClub != 141)
                    $InvitacionesPermitidasMes = 100;

                if ((int) $row_socio["IDSocio"] <= 0) :
                    //Crear Socio
                    $inserta_socio = "INSERT INTO Socio (IDClub, IDSocioSistemaExterno, IdentificadorExterno, IDEstadoSocio, Accion, AccionPadre, IDParentesco, Parentesco, TipoSocio, IDCategoria, Genero, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, Telefono, Celular, FechaNacimiento, UsuarioTrCr, FechaTrCr, NumeroInvitados, NumeroAccesos, PermiteReservar,IDPermisoServicio, CambioClave,FotoActualizadaSocio,Predio,CodigoCarne)
                                                  Values ('" . $IDClub . "','" . $IDSocioSistemaExterno . "','" . $IDSocioSistemaExterno . "','" . $estado_socio . "','" . $Accion . "','" . $AccionPadre . "','" . $Parentesco . "','" . $Parentesco . "','" . $TipoSocio . "','" . $id_categoria . "','" . $Genero . "','" . trim($Nombre) . "','" . $Apellido . "','" . $NumeroDocumento . "','" . $UsuarioApp . "','" . $clave_socio . "','" . $CorreoElectronico . "',
                                                    '" . $Telefono . "','" . $Celular . "','" . $FechaNacimiento . "','Cron',NOW(),'$InvitacionesPermitidasMes','$InvitacionesPermitidasMes','" . $Permite_Reservar . "', '" . $PermiteServicio . "', '" . $CambiarClave . "','S','" . $Predio . "','" . $CodigoCarne . "')";

                    //echo "<br>".$inserta_socio;
                    $dbo->query($inserta_socio);
                    $id = $dbo->lastID();
                    $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setsocio','" . $inserta_socio . "','" . json_encode($parameters) . "')");

                    $parametros_codigo_barras = $NumeroDocumento;
                    //$frm["CodigoBarras"]=SIMUtil::generar_codigo_barras($parametros_codigo_barras,$id);
                    //actualizo codigo barras
                    //$update_codigo=$dbo->query("update Socio set CodigoBarras = '".$frm["CodigoBarras"]."' Where IDSocio = '".$id."'");

                    if ($frm[IDClub] == 34) :
                        $parametros_codigo_qr = $frm[NumeroDocumento];
                    else :
                        $parametros_codigo_qr = $frm[NumeroDocumento] . "\r\n";
                    endif;

                    if ($Masivo != "S") :
                        $frm["CodigoQR"] = SIMUtil::generar_carne_qr($frm[IDSocio], $parametros_codigo_qr);
                        $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $frm["CodigoQR"] . "' Where IDSocio = '" . $id . "'");
                    endif;

                else :
                    //Actualiza Socio

                    $actualiza_socio = "Update Socio
                                            set IDEstadoSocio = '" . $estado_socio . "',
                                            IDSocioSistemaExterno='" . $IDSocioSistemaExterno . "',
                                            IdentificadorExterno='" . $IDSocioSistemaExterno . "',
                                            Accion = '" . $Accion . "',
                                            AccionPadre='" . $AccionPadre . "',
                                            Parentesco = '" . $Parentesco . "',
                                            TipoSocio = '" . $TipoSocio . "',
                                            Telefono = '" . $Telefono . "',
                                            IDCategoria= '" . $id_categoria . "',
                                            Celular = '" . $Celular . "',
                                            Direccion = '" . $Direccion . "',
                                            Nombre = '" . trim($Nombre) . "',
                                            Apellido = '" . trim($Apellido) . "',
                                            NumeroDocumento = '" . $NumeroDocumento . "',
                                            CorreoElectronico = '" . $CorreoElectronico . "',
                                            FechaNacimiento = '" . $FechaNacimiento . "',
                                            NumeroInvitados = '$InvitacionesPermitidasMes',
                                            NumeroAccesos = '$InvitacionesPermitidasMes',
                                            Email='" . $UsuarioApp . "',
                                            Predio = '" . $Predio . "',
                                            CodigoCarne = '" . $CodigoCarne . "',
                                            UsuarioTrEd = 'Cron',
                                            FechaTrEd = NOW(),
                                            PermiteReservar = '" . $Permite_Reservar . "'
                                            " . $actulizaPermiteServicio . " Where IDSocio = '" . $row_socio["IDSocio"] . "'";
                    $dbo->query($actualiza_socio);
                //echo "SQ:".$actualiza_socio;
                //exit;
                endif;

                if ($IDClub != 70) {
                    echo $actualiza_socio;
                }

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

        public function set_propietario($IDClub, $Nombre, $Apellido, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Portal, $Casa, $Llave, $AccionRegistro)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($NumeroDocumento) && !empty($Nombre)) {

                $Documento = trim($row["NumeroDocumento"]);
                //Consulto si el socio ya existe
                $sql_socio = "Select IDSocio From Socio Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'";
                $result_socio = $dbo->query($sql_socio);
                //Estado Socio
                switch ($row["AccionRegistro"]):
                    case "insert":
                        $estado_socio = 1;
                        break;
                    case "delete":
                        $estado_socio = 2;
                        break;
                    case "update":
                        $estado_socio = 1;
                        break;
                    default:
                        $estado_socio = 1;
                endswitch;

                $clave_socio = $Llave;
                $Predio = $Portal . " " . $Casa;

                if ($dbo->rows($result_socio) <= 0) :
                    //Crear Socio
                    $inserta_socio = "Insert into Socio (IDClub, IDEstadoSocio, Accion, AccionPadre, Parentesco, Genero, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, Celular, FechaNacimiento, UsuarioTrCr, FechaTrCr, NumeroInvitados, NumeroAccesos, PermiteReservar, CambioClave, TipoSocio, Predio)
                                                  Values ('" . $IDClub . "','" . $estado_socio . "','" . $NumeroDocumento . "','" . $NumeroDocumento . "','','','" . trim($Nombre) . "','" . $Apellido . "','" . $NumeroDocumento . "','" . $NumeroDocumento . "','" . $clave_socio . "','" . $CorreoElectronico . "','" . $Telefono . "','" . $FechaNacimiento . "','Cron',NOW(),'100','100','S','N','Propietario','" . $Predio . "')";

                    $dbo->query($inserta_socio);
                else :
                    //Actualiza Socio
                    $actualiza_socio = "Update Socio
                                                    set IDEstadoSocio = '" . $estado_socio . "',
                                                    NumeroDocumento = '" . $NumeroDocumento . "',
                                                    NumeroDocumento='" . $NumeroDocumento . "',
                                                    TipoSocio = 'Propietario',
                                                    Celular = '" . $Celular . "',
                                                    Nombre = '" . trim($Nombre) . "',
                                                    Apellido = '" . trim($Apellido) . "',
                                                    NumeroDocumento = '" . $NumeroDocumento . "',
                                                    CorreoElectronico = '" . $CorreoElectronico . "',
                                                    FechaNacimiento = '" . $FechaNacimiento . "',
                                                    NumeroInvitados= '100',
                                                    NumeroAccesos= '100',
                                                    Clave = '" . $Llave . "',
                                                    Predio = '" . $Predio . "',
                                                    UsuarioTrEd = 'Cron',

                                                    FechaTrEd = NOW()
                                                    Where NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'";
                    $dbo->query($actualiza_socio);
                endif;

                $respuesta["message"] = "registro guardado Llave: " . $Llave;
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "11. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_crear_cuenta($IDClub, $Accion, $Identificacion, $CorreoElectronico)
        {

            $dbo = &SIMDB::get();

            if (!empty($IDClub) && !empty($Accion) && !empty($Identificacion) && !empty($CorreoElectronico)) {

                //Verifico que el socio no exista
                $id_socio = $dbo->getFields("Socio", "IDSocio", "Accion = '" . $Accion . "' and IDClub = '" . $IDClub . "'");
                if (empty($id_socio)) {
                    //Verifico si la membresia existe
                    $endpoint = ENDPOINT_CONDADO;
                    $wsdlFile = ENDPOINT_CONDADO;
                    //Creación del cliente SOAP
                    $clienteSOAP = new SoapClient($wsdlFile, array(
                        'location' => $endpoint,
                        'trace' => true,
                        'exceptions' => false
                    ));
                    //Incluye los parámetros que necesites en tu función
                    $parameters = array(
                        membresia => $Accion,
                    );
                    //Invocamos a una función del cliente, devolverá el resultado en formato array.
                    $membresia_encontrada = 0;
                    $valor = $clienteSOAP->Socio($parameters);

                    if (is_array($valor->SocioResult->Usuario)) {
                        $array_membresia = $valor->SocioResult->Usuario;
                    } elseif (!empty($valor->SocioResult->Usuario)) {
                        $array_membresia[] = $valor->SocioResult->Usuario;
                    }

                    foreach ($array_membresia as $datos_membresia) {

                        $membresia_encontrada = 1;

                        $Accion = $datos_membresia->Membresia;
                        $AccionPadre = $datos_membresia->Membresia;
                        $Parentesco = $datos_membresia->Aux3;
                        $Genero = "";
                        $Nombre = $datos_membresia->Socio;
                        $Apellido = "";
                        $FechaNacimiento = substr($datos_membresia->FechaNac, 0, 10);
                        $NumeroDocumento = $datos_membresia->CI;
                        $CorreoElectronico = $datos_membresia->email;
                        $Telefono = $datos_membresia->TelDom;
                        $Celular = $datos_membresia->Celular;
                        $Direccion = $datos_membresia->DirDom;
                        $TipoSocio = $datos_membresia->Relacion;
                        if ($datos_membresia->Estatus == "Presente") {
                            $EstadoSocio = "A";
                        } else {
                            $EstadoSocio = "I";
                        }

                        $InvitacionesPermitidasMes = 100;
                        $UsuarioApp = $Accion;
                        $Predio = $datos_membresia->CI;
                        $Categoria = "";

                        $respuesta = SIMWebServiceUsuarios::set_socio($IDClub, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio, $Categoria);
                        $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('setcrearcuenta','" . $NumeroDocumento . "|" . $IDClub . "|" . $Accion . "','" . json_encode($parameters) . "')");
                    }
                    if ($membresia_encontrada == 1) {
                        $respuesta["message"] = "Registro exitoso, Su usuario es el código de socio o membresía y clave es su numero de identificación";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } else {
                        $respuesta["message"] = "La membresia no existe, por favor verifique o comuniquese con el club";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    }
                } else {
                    $respuesta["message"] = "No es posible crear la cuenta la membresia ya existe, por favor verifique o comuniquese con el club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "11. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function set_actualiza_datos($IDClub, $IDSocio, $Direccion, $Telefono, $DireccionOficina, $TelefonoOficina, $Celular, $CorreoElectronico)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($CorreoElectronico)) {

                $sql_socio = "UPDATE Socio SET  Direccion='" . $Direccion . "',Telefono='" . $Telefono . "',DireccionOficina='" . $DireccionOficina . "',TelefonoOficina='" . $TelefonoOficina . "',Celular='" . $Celular . "',CorreoElectronico='" . $CorreoElectronico . "' WHERE IDSocio = '" . $IDSocio . "'";
                $dbo->query($sql_socio);
                $sql_socio_inserta = "INSERT INTO SocioActualizacion (IDClub, IDSocio,Direccion,Telefono,DireccionOficina,TelefonoOficina,Celular,CorreoElectronico,FechaTrCr) VALUES ('" . $IDClub . "', '" . $IDSocio . "','" . $Direccion . "','" . $Telefono . "','" . $DireccionOficina . "','" . $TelefonoOficina . "','" . $Celular . "','" . $CorreoElectronico . "',NOW())";
                $dbo->query($sql_socio_inserta);

                $respuesta["message"] = "Datos actualizados correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "11. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;
        }

        public function set_actualiza_datos_socio($IDClub, $IDSocio, $Campos, $IDUsuario, $TipoApp, $Archivo, $File = "")
        {




            $dbo = &SIMDB::get();

            /*   print_r($File);
            echo "Archvo:" . $Archivo; */
            // print_r($_FILES);
            // echo $File;
            if ($TipoApp == "Empleado" && $IDUsuario == "") {
                $IDUsuario = $IDSocio;
                $IDSocio = "";
            }
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) {

                $select_campos_editar = "SELECT * FROM CampoEditarSocio";
                // $select_campos_editar = "SELECT * FROM CampoEditarSocio WHERE Tipo<>'imagen' and Tipo<>'imagenarchivo'";
                $r_select_campos_editar = $dbo->query($select_campos_editar);
                while ($row_campo_editar = $dbo->fetchArray($r_select_campos_editar)) {
                    $datos_campos_editar[$row_campo_editar["Nombre"]] = $row_campo_editar["IDCampoEditarSocio"];
                }

                $Campos = trim(preg_replace('/\s+/', ' ', $Campos));
                $datos_campos = json_decode($Campos, true);
                if (count($datos_campos) > 0) :
                    foreach ($datos_campos as $detalle_campo) :
                        $IDCampo = $detalle_campo["IDCampoEditarSocio"];
                        $valor = $detalle_campo["Valor"];

                        if (!empty($IDSocio)) {
                            // $sql_verifica = "SELECT IDSocioCampoEditarSocio FROM SocioCampoEditarSocio  WHERE IDSocio = '" . $IDSocio . "' and IDCampoEditarSocio = '" . $IDCampo . "'";
                            $sql_verifica = "SELECT SCES.IDSocioCampoEditarSocio,CES.Tipo FROM SocioCampoEditarSocio SCES,CampoEditarSocio CES   WHERE IDSocio = '" . $IDSocio . "' and SCES.IDCampoEditarSocio = '" . $IDCampo . "' AND SCES.IDCampoEditarSocio=CES.IDCampoEditarSocio";
                            $r_verifica = $dbo->query($sql_verifica);
                            $verifica = $dbo->fetchArray($r_verifica);
                            $verificarcampos = $verifica;

                            if ($verificarcampos["Tipo"] != "imagen" && $verificarcampos["Tipo"] != "imagenarchivo") {
                                if ($dbo->rows($r_verifica) > 0) {
                                    // $row_verifica = $dbo->fetchArray($r_verifica);

                                    /* $sql_socio_datos = "UPDATE SocioCampoEditarSocio
                                    SET  Valor='" . $valor . "',FechaTrEd = NOW()
                                    WHERE IDSocioCampoEditarSocio = '" . $row_verifica["IDSocioCampoEditarSocio"] . "'"; */
                                    $sql_socio_datos = "UPDATE SocioCampoEditarSocio
                                    SET  Valor='" . $valor . "',FechaTrEd = NOW()
                                    WHERE IDSocioCampoEditarSocio = '" . $verificarcampos["IDSocioCampoEditarSocio"] . "'";

                                    // echo "UPDATE" . $sql_socio_datos;
                                } else {
                                    $sql_socio_datos = "INSERT INTO SocioCampoEditarSocio (IDCampoEditarSocio, IDSocio,Valor,FechaTrCr)
                                                                                            VALUES ('" . $IDCampo . "','" . $IDSocio . "','" . $valor . "',NOW())";
                                }
                            }
                        } else {
                            $sql_verifica = "SELECT IDUsuarioCampoEditarUsuario FROM UsuarioCampoEditarUsuario WHERE IDUsuario = '" . $IDUsuario . "' and IDCampoEditarUsuario = '" . $IDCampo . "'";
                            $r_verifica = $dbo->query($sql_verifica);
                            if ($dbo->rows($r_verifica) > 0) {
                                $row_verifica = $dbo->fetchArray($r_verifica);
                                $sql_socio_datos = "UPDATE UsuarioCampoEditarUsuario
                                                                                        SET  Valor='" . $valor . "',FechaTrEd = NOW()
                                                                                        WHERE IDUsuarioCampoEditarUsuario = '" . $row_verifica["IDUsuarioCampoEditarUsuario"] . "'";
                            } else {
                                $sql_socio_datos = "INSERT INTO UsuarioCampoEditarUsuario (IDCampoEditarUsuario, IDUsuario,Valor,FechaTrCr)
                                                                                            VALUES ('" . $IDCampo . "','" . $IDUsuario . "','" . $valor . "',NOW())";
                            }
                        }

                        $dbo->query($sql_socio_datos);

                    endforeach;

                    //subir las imagenes
                    if (isset($File)) {
                        //$nombrefoto.=json_encode($File);
                        foreach ($File as $nombre_archivo => $archivo) {
                            //echo "nombre Archivo:" .  $nombre_archivo;
                            $ArrayPreguntaEncuesta = explode("|", $nombre_archivo);
                            $IDPreguntaActualiza = $ArrayPreguntaEncuesta[1];
                            //$nombrefoto.=$archivo["name"];
                            //$nombrefoto.=json_encode($archivo);
                            $tamano_archivo = $archivo["size"];
                            if ($tamano_archivo >= 6000000) {
                                $respuesta["message"] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            } else {
                                $files = SIMFile::upload($File[$nombre_archivo], EDITARPERFIL_DIR, "IMAGE");
                                if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
                                    $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;

                                $Archivo = $files[0]["innername"];
                                $actualiza_pregunta = "UPDATE SocioCampoEditarSocio SET Valor = '" . $Archivo . "' WHERE IDCampoEditarSocio ='" . $IDPreguntaActualiza . "' AND IDSocio='" . $IDSocio . "'";
                                // echo "update:" . $actualiza_pregunta;
                                $dbo->query($actualiza_pregunta);
                                $nombrefoto .=    $actualiza_pregunta;
                            }
                        }
                    }


                    //subir las imagenes
                    /*    if (isset($File)) {

                        //$nombrefoto.=json_encode($File);
                        foreach ($File as $nombre_archivo => $archivo) {
                            $ArrayPreguntaEncuesta = explode("|", $nombre_archivo);
                            $IDPreguntaActualiza = $ArrayPreguntaEncuesta[1];
                            //$nombrefoto.=$archivo["name"];
                            //$nombrefoto.=json_encode($archivo);
                            $tamano_archivo = $archivo["size"];
                            if ($tamano_archivo >= 6000000) {
                                $respuesta["message"] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                                return $respuesta;
                            } else {
                                $files = SIMFile::upload($File[nombre_archivo], EDITARPERFIL_DIR, "IMAGE");
                                if (empty($files) && !empty($File[nombre_archivo]["name"])) :
                                    $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                                    $respuesta["success"] = false;
                                    $respuesta["response"] = null;
                                    return $respuesta;
                                endif;

                                $Archivo = $files[0]["innername"];
                                $actualiza_pregunta = "UPDATE SocioCampoEditarSocio SET Valor = '" . $Archivo . "' WHERE IDSocioCampoEditarSocio ='" . $IDPreguntaActualiza . "' AND IDSocio='" . $IDSocio . "'";
                                $dbo->query($actualiza_pregunta);
                                $nombrefoto .=    $actualiza_pregunta;
                            }
                        }
                    } */



                    //para club country medellin 

                    if ($IDClub == 227) {
                        require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
                        $datos_socios = "SELECT * FROM Socio WHERE IDSocio ='$IDSocio'  and IDClub = '" . $IDClub . "' LIMIT 1";
                        $datos = $dbo->query($datos_socios);
                        while ($row = $dbo->fetchArray($datos)) {
                            $token = $row["TokenCountryMedellin"];
                        }
                        $respuesta = SIMWebServiceCountryMedellin::App_ActualizarPerfil($datos_campos, $token);
                        return $respuesta;
                    }







                    //para club campestre de cartagena actualizo datos del socio
                    if ($IDClub == 218) {
                        if (count($datos_campos) > 0) {

                            foreach ($datos_campos as $detalle_campo) {
                                $IDCampo = $detalle_campo["IDCampoEditarSocio"];
                                $valor = trim($detalle_campo["Valor"]);
                                switch ($IDCampo) {
                                    case '1157':
                                        $NombreSocio = $valor;
                                        break;
                                    case '1158':
                                        $ApellidosSocio = $valor;
                                        break;
                                    case '1159':
                                        $TipoDocumentoSocio = $valor;
                                        if ($TipoDocumentoSocio == "C.C.") {
                                            $TipoDocumento = "Cédula de ciudadanía";
                                        }
                                        if ($TipoDocumentoSocio == "C.E.") {
                                            $TipoDocumento = "Cédula de extranjería";
                                        }
                                        if ($TipoDocumentoSocio == "T.I.") {
                                            $TipoDocumento = "Tarjeta de identidad";
                                        }
                                        if ($TipoDocumentoSocio == "Pasaporte") {
                                            $TipoDocumento = "Pasaporte";
                                        }
                                        break;
                                    case '1160':
                                        $NumeroDocumentoSocio = $valor;
                                        break;
                                    case '1161':
                                        $FechaNacimientoSocio = $valor;
                                        break;
                                    case '1162':
                                        $CelularSocio = $valor;
                                        break;
                                    case '1163':
                                        $CorreoElectronicoSocio = $valor;
                                        break;
                                    case '1164':
                                        $DireccionSocio = $valor;
                                        break;
                                }
                            }
                        }




                        if (!empty($NumeroDocumentoSocio)) {
                            //Verifico que el socio  exista
                            $sql_socio = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $NumeroDocumentoSocio . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_socio = $dbo->query($sql_socio);
                            $row_socio = $dbo->fetchArray($r_socio);
                            if ((int) $row_socio["IDSocio"] > 0) {
                                $actualiza_socio = "UPDATE Socio SET Nombre='" . $NombreSocio . "', FechaNacimiento = '" . $FechaNacimientoSocio . "', Apellido= '" . $ApellidosSocio . "', Celular= '" . $CelularSocio . "', CorreoElectronico='" . $CorreoElectronicoSocio . "', Direccion='" . $DireccionSocio . "', TipoDocumento='" . $TipoDocumento . "' WHERE IDSocio = '" . $row_socio["IDSocio"] . "'";
                                $dbo->query($actualiza_socio);

                                $respuesta["message"] = "Datos actualizados correctamente";
                                $respuesta["success"] = true;
                                $respuesta["response"] = null;
                            } else {
                                $respuesta["message"] = "Verifique que el numero de documento sea correcto.";
                                $respuesta["success"] = false;
                                $respuesta["response"] = null;
                            }
                        } else {
                            $respuesta["message"] = "Debes ingresar numero de documento";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                        }
                    } //end if



                    //para nuba childcare club inserto los datos hijo en la tabla Socio
                    if ($IDClub == 196) {
                        if (count($datos_campos) > 0) {

                            foreach ($datos_campos as $detalle_campo) {
                                $IDCampo = $detalle_campo["IDCampoEditarSocio"];
                                $valor = trim($detalle_campo["Valor"]);
                                switch ($IDCampo) {
                                    case '1020':
                                        $NombreHijo1 = $valor;
                                        break;
                                    case '1021':
                                        $ApellidosHijo1 = $valor;
                                        break;
                                    case '1023':
                                        $NumeroDocumentoHijo1 = $valor;
                                        break;
                                    case '1026':
                                        $TelefonoHijo1 = $valor;
                                        break;
                                    case '1031':
                                        $FechaNacimientoHijo1 = $valor;
                                        break;
                                    case '1050':
                                        $NombreHijo2 = $valor;
                                        break;
                                    case '1051':
                                        $ApellidosHijo2 = $valor;
                                        break;
                                    case '1053':
                                        $NumeroDocumentoHijo2 = $valor;
                                        break;
                                    case '1055':
                                        $TelefonoHijo2 = $valor;
                                        break;
                                    case '1056':
                                        $FechaNacimientoHijo2 = $valor;
                                        break;
                                    case '1251':
                                        $AutorizacionRedesSociales = $valor;
                                        break;
                                }
                            }
                        }

                        //busca la categoria en la que pueden estar los hijos aqui
                        $IDCategoria1 = SIMWebServiceUsuarios::actualizar_edad($FechaNacimientoHijo1, $IDClub);
                        $IDCategoria2 = SIMWebServiceUsuarios::actualizar_edad($FechaNacimientoHijo2, $IDClub);


                        $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                        $alto_barras = "";
                        //hijo1
                        if (!empty($NumeroDocumentoHijo1)) {
                            //Verifico que el hijo1 no exista
                            $sql_hijo1 = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $NumeroDocumentoHijo1 . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_hijo1 = $dbo->query($sql_hijo1);
                            $row_hijo1 = $dbo->fetchArray($r_hijo1);
                            if ((int) $row_hijo1["IDSocio"] <= 0) {
                                $sql_crea_hijo1 = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, FechaTrCr, UsuarioTrCr, PermiteReservar,FechaNacimiento,Celular,TipoSocio,IDCategoria,ModificaCategoria,AutorizacionRedesSociales)
        VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreHijo1 . "','" . $ApellidosHijo1 . "','" . $NumeroDocumentoHijo1 . "','" . $NumeroDocumentoHijo1 . "',sha1('" . $NumeroDocumentoHijo1 . "'), NOW(),
       'Modulo Editar Perfil','S','" . $FechaNacimientoHijo1 . "','" . $TelefonoHijo1 . "','HIJO',$IDCategoria1,'App','" . $AutorizacionRedesSociales . "')";
                                $dbo->query($sql_crea_hijo1);

                                //genero codigo de barras
                                $IDSocio = $dbo->lastID("IDSocio");
                                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($NumeroDocumentoHijo1, $IDSocio, $alto_barras);
                                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                                //genero codigo qr
                                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $NumeroDocumentoHijo1);
                                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            } else {
                                $actualiza_hijo1 = "UPDATE Socio SET Nombre='" . $NombreHijo1 . "', FechaNacimiento = '" . $FechaNacimientoHijo1 . "', Apellido= '" . $ApellidosHijo1 . "', NumeroDocumento = '" . $NumeroDocumentoHijo1 . "', Celular= '" . $TelefonoHijo1 . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='HIJO', IDCategoria = $IDCategoria1,ModificaCategoria = 'App',AutorizacionRedesSociales='" . $AutorizacionRedesSociales . "'  WHERE IDSocio = '" . $row_hijo1["IDSocio"] . "'";
                                $dbo->query($actualiza_hijo1);
                            }
                        }

                        //hijo2
                        if (!empty($NumeroDocumentoHijo2)) {
                            //Verifico que el hijo2 no exista
                            $sql_hijo2 = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $NumeroDocumentoHijo2 . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_hijo2 = $dbo->query($sql_hijo2);
                            $row_hijo2 = $dbo->fetchArray($r_hijo2);
                            if ((int) $row_hijo2["IDSocio"] <= 0) {
                                $sql_crea_hijo2 = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, FechaTrCr, UsuarioTrCr, PermiteReservar,FechaNacimiento,Celular,TipoSocio,IDCategoria,ModificaCategoria,AutorizacionRedesSociales)
                              VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreHijo2 . "','" . $ApellidosHijo2 . "','" . $NumeroDocumentoHijo2 . "','" . $NumeroDocumentoHijo2 . "',sha1('" . $NumeroDocumentoHijo1 . "'), NOW(),
                             'Modulo Editar Perfil','S','" . $FechaNacimientoHijo2 . "','" . $TelefonoHijo2 . "','HIJO',$IDCategoria2,'App','" . $AutorizacionRedesSociales . "')";
                                $dbo->query($sql_crea_hijo2);

                                //genero codigo de barras
                                $IDSocio = $dbo->lastID("IDSocio");
                                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($NumeroDocumentoHijo2, $IDSocio, $alto_barras);
                                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                                //genero codigo qr
                                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $NumeroDocumentoHijo2);
                                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            } else {
                                $actualiza_hijo2 = "UPDATE Socio SET Nombre='" . $NombreHijo2 . "', FechaNacimiento = '" . $FechaNacimientoHijo2 . "', Apellido= '" . $ApellidosHijo2 . "', NumeroDocumento = '" . $NumeroDocumentoHijo2 . "', Celular= '" . $TelefonoHijo2 . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='HIJO', IDCategoria = $IDCategoria2, ModificaCategoria = 'App',AutorizacionRedesSociales='" . $AutorizacionRedesSociales . "'  WHERE IDSocio = '" . $row_hijo2["IDSocio"] . "'";
                                $dbo->query($actualiza_hijo2);
                            }
                        }

                        $respuesta["message"] = "Datos actualizados correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    } //end if


                    //para asia golf club inserto los datos beneficiario en la tabla Socio
                    if ($IDClub == 187 || $IDClub == 8) {
                        if (count($datos_campos) > 0) {

                            foreach ($datos_campos as $detalle_campo) {
                                $IDCampo = $detalle_campo["IDCampoEditarSocio"];
                                $valor = trim($detalle_campo["Valor"]);
                                switch ($IDCampo) {
                                    case '927':
                                        $NombreConyugue = $valor;
                                        break;
                                    case '928':
                                        $ApellidosConyugue = $valor;
                                        break;
                                    case '940':
                                        $DniConyugue = $valor;
                                        break;
                                    case '958':
                                        $FechaNacimientoConyugue = $valor;
                                        break;
                                    case '931':
                                        $CelularConyugue = $valor;
                                        break;
                                    case '946':
                                        $CorreoConyugue = $valor;
                                        break;
                                    case '951':
                                        $NombresInvitado = $valor;
                                        break;

                                    case '939':
                                        $NombreBeneficiario1 = $valor;
                                        break;
                                    case '956':
                                        $ApellidosBeneficiario1 = $valor;
                                        break;
                                    case '942':
                                        $DniBeneficiario1 = $valor;
                                        break;
                                    case '960':
                                        $FechaNacimientoBeneficiario1 = $valor;
                                        break;
                                    case '968':
                                        $NombreBeneficiario2 = $valor;
                                        break;
                                    case '969':
                                        $ApellidosBeneficiario2 = $valor;
                                        break;
                                    case '970':
                                        $DniBeneficiario2 = $valor;
                                        break;
                                    case '971':
                                        $FechaNacimientoBeneficiario2 = $valor;
                                        break;
                                    case '933':
                                        $NombreBeneficiario3 = $valor;
                                        break;
                                    case '935':
                                        $ApellidosBeneficiario3 = $valor;
                                        break;
                                    case '950':
                                        $DniBeneficiario3 = $valor;
                                        break;
                                    case '974':
                                        $FechaNacimientoBeneficiario3 = $valor;
                                        break;
                                    case '977':
                                        $NombreBeneficiario4 = $valor;
                                        break;
                                    case '978':
                                        $ApellidosBeneficiario4 = $valor;
                                        break;
                                    case '979':
                                        $DniBeneficiario4 = $valor;
                                        break;
                                    case '980':
                                        $FechaNacimientoBeneficiario4 = $valor;
                                        break;
                                    case '981':
                                        $NombreBeneficiario5 = $valor;
                                        break;
                                    case '982':
                                        $ApellidosBeneficiario5 = $valor;
                                        break;
                                    case '983':
                                        $DniBeneficiario5 = $valor;
                                        break;
                                    case '984':
                                        $FechaNacimientoBeneficiario5 = $valor;
                                        break;
                                }
                            }
                        }


                        $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                        $alto_barras = "";
                        //conyugue
                        if (!empty($DniConyugue)) {
                            //Verifico que el conyugue no exista
                            $sql_conyugue = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $DniConyugue . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_conyugue = $dbo->query($sql_conyugue);
                            $row_conyugue = $dbo->fetchArray($r_conyugue);
                            if ((int) $row_conyugue["IDSocio"] <= 0) {
                                $sql_crea_conyugue = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, PermiteReservar,FechaNacimiento,Celular,TipoSocio)
                        VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreConyugue . "','" . $ApellidosConyugue . "','" . $DniConyugue . "','" . $DniConyugue . "',sha1('" . $DniConyugue . "'), '" . $CorreoConyugue . "', NOW(),
                       'Modulo Editar Perfil','S','" . $FechaNacimientoConyugue . "','" . $CelularConyugue . "','Beneficiario')";
                                $dbo->query($sql_crea_conyugue);

                                //genero codigo de barras
                                $IDSocio = $dbo->lastID("IDSocio");
                                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($DniConyugue, $IDSocio, $alto_barras);
                                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                                //genero codigo qr
                                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $DniConyugue);
                                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            } else {
                                $actualiza_conyugue = "UPDATE Socio SET Nombre='" . $NombreConyugue . "', FechaNacimiento = '" . $FechaNacimientoConyugue . "', Apellido= '" . $ApellidosConyugue . "', NumeroDocumento = '" . $DniConyugue . "', Celular= '" . $CelularConyugue . "', Email='" . $DniConyugue . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='Beneficiario' WHERE IDSocio = '" . $row_conyugue["IDSocio"] . "'";
                                $dbo->query($actualiza_conyugue);
                            }
                        }



                        //Beneficiario1
                        if (!empty($DniBeneficiario1)) {
                            //Verifico que el beneficiario no exista
                            $sql_beneficiario1 = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $DniBeneficiario1 . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_beneficiario1 = $dbo->query($sql_beneficiario1);
                            $row_beneficiario1 = $dbo->fetchArray($r_beneficiario1);
                            if ((int) $row_beneficiario1["IDSocio"] <= 0) {
                                $sql_crea_beneficiario1 = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, FechaTrCr, UsuarioTrCr, PermiteReservar,FechaNacimiento,TipoSocio)
                        VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreBeneficiario1 . "','" . $ApellidosBeneficiario1 . "','" . $DniBeneficiario1 . "','" . $DniBeneficiario1 . "',sha1('" . $DniBeneficiario1 . "'), NOW(),
                       'Modulo Editar Perfil','S','" . $FechaNacimientoBeneficiario1 . "','Beneficiario')";
                                $dbo->query($sql_crea_beneficiario1);

                                //genero codigo de barras
                                $IDSocio = $dbo->lastID("IDSocio");
                                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($DniBeneficiario1, $IDSocio, $alto_barras);
                                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                                //genero codigo qr
                                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $DniBeneficiario1);
                                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            } else {
                                $actualiza_beneficiario1 = "UPDATE Socio SET Nombre='" . $NombreBeneficiario1 . "', FechaNacimiento = '" . $FechaNacimientoBeneficiario1 . "', Apellido= '" . $ApellidosBeneficiario1 . "', NumeroDocumento = '" . $DniBeneficiario1 . "',Email='" . $DniBeneficiario1 . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='Beneficiario'   WHERE IDSocio = '" . $row_beneficiario1["IDSocio"] . "'";
                                $dbo->query($actualiza_beneficiario1);
                            }
                        }

                        //Beneficiario2
                        if (!empty($DniBeneficiario2)) {
                            //Verifico que el beneficiario no exista
                            $sql_beneficiario2 = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $DniBeneficiario2 . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_beneficiario2 = $dbo->query($sql_beneficiario2);
                            $row_beneficiario2 = $dbo->fetchArray($r_beneficiario2);
                            if ((int) $row_beneficiario2["IDSocio"] <= 0) {
                                $sql_crea_beneficiario2 = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, FechaTrCr, UsuarioTrCr, PermiteReservar,FechaNacimiento,TipoSocio)
                        VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreBeneficiario2 . "','" . $ApellidosBeneficiario2 . "','" . $DniBeneficiario2 . "','" . $DniBeneficiario2 . "',sha1('" . $DniBeneficiario2 . "'), NOW(),
                       'Modulo Editar Perfil','S','" . $FechaNacimientoBeneficiario2 . "','Beneficiario')";
                                $dbo->query($sql_crea_beneficiario2);

                                //genero codigo de barras
                                $IDSocio = $dbo->lastID("IDSocio");
                                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($DniBeneficiario2, $IDSocio, $alto_barras);
                                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                                //genero codigo qr
                                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $DniBeneficiario2);
                                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            } else {
                                $actualiza_beneficiario2 = "UPDATE Socio SET Nombre='" . $NombreBeneficiario2 . "', FechaNacimiento = '" . $FechaNacimientoBeneficiario2 . "', Apellido= '" . $ApellidosBeneficiario2 . "', NumeroDocumento = '" . $DniBeneficiario2 . "', Email='" . $DniBeneficiario2 . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='Beneficiario'   WHERE IDSocio = '" . $row_beneficiario2["IDSocio"] . "'";
                                $dbo->query($actualiza_beneficiario2);
                            }
                        }

                        //Beneficiario3
                        if (!empty($DniBeneficiario3)) {
                            //Verifico que el beneficiario no exista
                            $sql_beneficiario3 = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $DniBeneficiario3 . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_beneficiario3 = $dbo->query($sql_beneficiario3);
                            $row_beneficiario3 = $dbo->fetchArray($r_beneficiario3);
                            if ((int) $row_beneficiario3["IDSocio"] <= 0) {
                                $sql_crea_beneficiario3 = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, FechaTrCr, UsuarioTrCr, PermiteReservar,FechaNacimiento,TipoSocio)
                        VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreBeneficiario3 . "','" . $ApellidosBeneficiario3 . "','" . $DniBeneficiario3 . "','" . $DniBeneficiario3 . "',sha1('" . $DniBeneficiario3 . "'), NOW(),
                       'Modulo Editar Perfil','S','" . $FechaNacimientoBeneficiario3 . "','Beneficiario')";
                                $dbo->query($sql_crea_beneficiario3);

                                //genero codigo de barras
                                $IDSocio = $dbo->lastID("IDSocio");
                                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($DniBeneficiario3, $IDSocio, $alto_barras);
                                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                                //genero codigo qr
                                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $DniBeneficiario3);
                                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            } else {
                                $actualiza_beneficiario3 = "UPDATE Socio SET Nombre='" . $NombreBeneficiario3 . "', FechaNacimiento = '" . $FechaNacimientoBeneficiario3 . "', Apellido= '" . $ApellidosBeneficiario3 . "', NumeroDocumento = '" . $DniBeneficiario3 . "', Email='" . $DniBeneficiario3 . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='Beneficiario'  WHERE IDSocio = '" . $row_beneficiario3["IDSocio"] . "'";
                                $dbo->query($actualiza_beneficiario3);
                            }
                        }

                        //Beneficiario4
                        if (!empty($DniBeneficiario4)) {
                            //Verifico que el beneficiario no exista
                            $sql_beneficiario4 = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $DniBeneficiario4 . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_beneficiario4 = $dbo->query($sql_beneficiario4);
                            $row_beneficiario4 = $dbo->fetchArray($r_beneficiario4);
                            if ((int) $row_beneficiario4["IDSocio"] <= 0) {
                                $sql_crea_beneficiario4 = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, FechaTrCr, UsuarioTrCr, PermiteReservar,FechaNacimiento,TipoSocio)
                        VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreBeneficiario4 . "','" . $ApellidosBeneficiario4 . "','" . $DniBeneficiario4 . "','" . $DniBeneficiario4 . "',sha1('" . $DniBeneficiario4 . "'), NOW(),
                       'Modulo Editar Perfil','S','" . $FechaNacimientoBeneficiario4 . "','Beneficiario')";
                                $dbo->query($sql_crea_beneficiario4);

                                //genero codigo de barras
                                $IDSocio = $dbo->lastID("IDSocio");
                                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($DniBeneficiario4, $IDSocio, $alto_barras);
                                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                                //genero codigo qr
                                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $DniBeneficiario4);
                                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            } else {
                                $actualiza_beneficiario4 = "UPDATE Socio SET Nombre='" . $NombreBeneficiario4 . "', FechaNacimiento = '" . $FechaNacimientoBeneficiario4 . "', Apellido= '" . $ApellidosBeneficiario4 . "', NumeroDocumento = '" . $DniBeneficiario4 . "', Email='" . $DniBeneficiario4 . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='Beneficiario'  WHERE IDSocio = '" . $row_beneficiario4["IDSocio"] . "'";
                                $dbo->query($actualiza_beneficiario4);
                            }
                        }

                        //Beneficiario5
                        if (!empty($DniBeneficiario5)) {
                            //Verifico que el beneficiario no exista
                            $sql_beneficiario5 = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $DniBeneficiario5 . "' and IDClub = '" . $IDClub . "' LIMIT 1";
                            $r_beneficiario5 = $dbo->query($sql_beneficiario5);
                            $row_beneficiario5 = $dbo->fetchArray($r_beneficiario5);
                            if ((int) $row_beneficiario5["IDSocio"] <= 0) {
                                $sql_crea_beneficiario5 = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, FechaTrCr, UsuarioTrCr, PermiteReservar,FechaNacimiento,TipoSocio)
                        VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreBeneficiario5 . "','" . $ApellidosBeneficiario5 . "','" . $DniBeneficiario5 . "','" . $DniBeneficiario5 . "',sha1('" . $DniBeneficiario5 . "'), NOW(),

                       'Modulo Editar Perfil','S','" . $FechaNacimientoBeneficiario5 . "','Beneficiario')";
                                $dbo->query($sql_crea_beneficiario5);

                                //genero codigo de barras
                                $IDSocio = $dbo->lastID("IDSocio");
                                $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($DniBeneficiario5, $IDSocio, $alto_barras);
                                $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                                //genero codigo qr
                                $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $DniBeneficiario5);
                                $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");
                            } else {
                                $actualiza_beneficiario5 = "UPDATE Socio SET Nombre='" . $NombreBeneficiario5 . "', FechaNacimiento = '" . $FechaNacimientoBeneficiario5 . "', Apellido= '" . $ApellidosBeneficiario5 . "', NumeroDocumento = '" . $DniBeneficiario5 . "', Email='" . $ApellidosBeneficiario5 . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='Beneficiario'  WHERE IDSocio = '" . $row_beneficiario5["IDSocio"] . "'";
                                $dbo->query($actualiza_beneficiario5);
                            }
                        }
                        $respuesta["message"] = "Datos actualizados correctamente";
                        $respuesta["success"] = true;
                        $respuesta["response"] = null;
                    }

                    //Para uruguay envio datos al ws
                    if (!empty($IDSocio) && $IDClub == 125) :
                        require LIBDIR . "SIMUruguay.inc.php";
                        $resp = SIMUruguay::actualiza_socio_uruguay($IDClub, $IDSocio, $datos_campos);
                        if (!empty($resp)) {
                            $respuesta["message"] = $resp;
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        }
                    endif;

                else :
                    $respuesta["message"] = "Datos vacios por favor verifique";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                endif;

                if (count($datos_campos) > 0) {

                    $set_update = implode(",", $array_actualiza);
                    $set_campos = implode(",", $array_campos);
                    $set_datos = implode(",", $array_datos);
                    //$sql_socio="UPDATE Socio SET  ".$set_update.", SolicitaEditarPerfil='N' WHERE IDSocio = '".$IDSocio."' Limit 1";
                    if ($TipoApp == "Empleado") {
                        $sql_socio = "UPDATE Usuario SET SolicitaEditarPerfil='N' WHERE IDUsuario = '" . $IDUsuario . "' Limit 1";
                    } else {
                        $sql_socio = "UPDATE Socio SET SolicitaEditarPerfil='N' WHERE IDSocio = '" . $IDSocio . "' Limit 1";
                    }

                    $dbo->query($sql_socio);

                    $sql_socio_inserta = "INSERT INTO SocioActualizacion (IDClub, IDSocio," . $set_campos . ",FechaTrCr) VALUES ('" . $IDClub . "','" . $IDSocio . "',$set_datos,NOW())";
                    $dbo->query($sql_socio_inserta);
                }

                $respuesta["message"] = "Datos actualizados correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            } else {
                $respuesta["message"] = "11. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
            return $respuesta;
        }

        public function cambio_clave_condado($IDClub, $IDSocio, $Clave)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio) && !empty($Clave)) {
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");

                $endpoint = ENDPOINT_CONDADO;
                $wsdlFile = ENDPOINT_CONDADO;

                try {
                    $client = new SoapClient($wsdlFile, array('exceptions' => 0));
                    $parameters = array(
                        Membresia => $datos_socio["Accion"],
                        Cedula => $datos_socio["NumeroDocumento"],
                        Correo_electronico => $datos_socio["CorreoElectronico"],
                        Clave_desencriptada => $Clave,
                        Clave_encriptada => sha1($Clave),
                    );

                    $result = $client->ActualizacionPassword_Socio($parameters);
                } catch (SoapFault $fault) {
                    $error_ws = 1;
                    //trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
                }
                $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('cambioclavecondado','" . $result->ActualizacionPassword_SocioResult . "','" . json_encode($parameters) . "')");
            }

            return true;
        }

        public function get_deuda_socio($IDClub, $IDSocio)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($IDSocio)) {
                $response = array();
                //Verifico que el socio no exista
                $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                if (!empty($accion_socio)) {
                    //Verifico si la membresia existe
                    $endpoint = ENDPOINT_CONDADO;
                    $wsdlFile = ENDPOINT_CONDADO;
                    //Creación del cliente SOAP
                    $clienteSOAP = new SoapClient($wsdlFile, array(
                        'location' => $endpoint,
                        'trace' => true,
                        'exceptions' => false
                    ));
                    //Incluye los parámetros que necesites en tu función
                    $parameters = array(
                        membresia => $accion_socio,
                    );
                    //Invocamos a una función del cliente, devolverá el resultado en formato array.
                    $membresia_encontrada = 0;
                    $valor = $clienteSOAP->DeudaSocio($parameters);

                    if (is_array($valor->DeudaSocioResult->Deuda)) {
                        $array_membresia_deuda = $valor->DeudaSocioResult->Deuda;
                    } else {
                        $array_membresia_deuda[] = $valor->DeudaSocioResult->Deuda;
                    }

                    foreach ($array_membresia_deuda as $datos_deuda) {
                        $mostrar = "S";
                        //Verifico que el documento no se encuentre en estado de pago ya que pudo haber sido pago,  pero hasta el siguiente dia se envia al ws
                        $sql_place = "SELECT IDPeticionesPlacetoPay FROM PeticionesPlacetoPay WHERE Documento like '%" . $datos_deuda->DOCUMENTO . "%' and estado_transaccion = 'APPROVED' ";
                        $r_place = $dbo->query($sql_place);
                        if ($dbo->rows($r_place) > 0) {
                            $mostrar = "N";
                        }

                        if ($mostrar == "S") {
                            $encontrados++;
                            $deudasocio["IDClub"] = $IDClub;
                            $deudasocio["IDSocio"] = $IDSocio;
                            $deudasocio["Cedula"] = $datos_deuda->CEDULA;
                            $deudasocio["Contacto"] = $datos_deuda->CONTACTO;
                            $deudasocio["Descripcion"] = $datos_deuda->DESCRIPCION;
                            $deudasocio["Descuento"] = $datos_deuda->DESCUENTO;
                            $deudasocio["Documento"] = $datos_deuda->DOCUMENTO;
                            $deudasocio["FechaVencimiento"] = $datos_deuda->FECHA_VENCIMIENTO;
                            $deudasocio["ICE"] = $datos_deuda->ICE;
                            $deudasocio["Impuestos"] = $datos_deuda->IMPUESTOS;
                            $deudasocio["IVA"] = $datos_deuda->IVA;
                            $deudasocio["NombreSocio"] = $datos_deuda->NOMBRE_SOCIO;
                            $deudasocio["NombreSocioPrincipal"] = $datos_deuda->NOMBRE_SOCIO_PRINCIPAL;
                            $deudasocio["OtrosCargos"] = $datos_deuda->OTROS_CARGOS;
                            $deudasocio["SaldoPorCobrar"] = $datos_deuda->SALDO_POR_COBRAR;
                            $deudasocio["Servicio"] = $datos_deuda->SERVICIO;
                            $deudasocio["Subtotal"] = $datos_deuda->SUBTOTAL;
                            $deudasocio["TipoDocumento"] = $datos_deuda->TIPO_DOCUMENTO;
                            $deudasocio["TotalDocumento"] = $datos_deuda->SALDO_POR_COBRAR;

                            unset($array_detalle_deuda);
                            //Detalle de la deuda
                            if (is_array($datos_deuda->L_Detalle_Deuda)) {
                                $array_detalle_deuda = $datos_deuda->L_Detalle_Deuda;
                            } else {
                                $array_detalle_deuda[] = $datos_deuda->L_Detalle_Deuda;
                            }

                            $response_detalle = array();
                            unset($detalledeuda);
                            foreach ($array_detalle_deuda as $datos_detalle) {
                                $detalledeuda["Ambiente"] = $datos_detalle->Deuda_Detallle->AMBIENTE;
                                $detalledeuda["Cantidad"] = $datos_detalle->Deuda_Detallle->CANTIDAD_FACTURADA;
                                $detalledeuda["Descuento"] = $datos_detalle->Deuda_Detallle->DESCUENTO;
                                $detalledeuda["Documento"] = $datos_detalle->Deuda_Detallle->DOCUMENTO;
                                $detalledeuda["ItemFacturadoCodigo"] = $datos_detalle->Deuda_Detallle->ITEM_FACTURADO_CODIGO;
                                $detalledeuda["ItemFacturadoDescripcion"] = $datos_detalle->Deuda_Detallle->ITEM_FACTURADO_DESCRIPCION;
                                $detalledeuda["PrecioUnitario"] = $datos_detalle->Deuda_Detallle->PRECIO_UNITARIO;
                                $detalledeuda["SubTotal"] = $datos_detalle->Deuda_Detallle->SUB_TOTAL;
                                $detalledeuda["TipoDocumento"] = $datos_detalle->Deuda_Detallle->TIPO_DOCUMENTO;
                                array_push($response_detalle, $detalledeuda);
                            }
                            $deudasocio["DetalleDeuda"] = $response_detalle;
                            array_push($response, $deudasocio);
                        }
                    }

                    $respuesta["message"] = "Encontrados: " . $encontrados;
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                } else {
                    $respuesta["message"] = "No se encontro accion";
                    $respuesta["success"] = false;
                    $respuesta["response"] = $response;
                }
            } else {

                $respuesta["message"] = "Faltan parametros.";
                $respuesta["success"] = false;
                $respuesta["response"] = $response;
            }

            return $respuesta;
        }

        public function set_deuda_socio($IDClub, $IDSocio, $Documento, $ValorPagar)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($Documento) && !empty($IDSocio)) {

                SIMWebServiceUsuarios::get_deuda_socio($IDClub, $IDSocio);
                $array_documentos_pagar = json_decode($Documento, true);
                foreach ($array_documentos_pagar as $datos_doc) {
                    $array_seleccion_pago[] = $datos_doc["NumeroDocumento"];
                }

                $deuda_socio = SIMWebServiceUsuarios::get_deuda_socio($IDClub, $IDSocio);
                $contador = 1;
                foreach ($deuda_socio["response"] as $id_deuda => $datos_deuda) {
                    $array_pago_asc[$contador] = $datos_deuda["Documento"];
                    $contador++;
                }

                //$array_seleccion_pago=explode(",",$documentos_pagar);
                if (count($array_seleccion_pago) > 0) {
                    foreach ($array_seleccion_pago as $id_doc => $num_doc) {
                        $clave = array_search($num_doc, $array_pago_asc);
                        if ($clave > 0) {
                            $array_clave_encontradas[] = $clave;
                        }
                    }
                }
                //verifico si se selecciono en orden
                $en_orden = "S";
                for ($i = 1; $i <= count($array_seleccion_pago); $i++) {
                    if (!in_array($i, $array_clave_encontradas)) {
                        $en_orden = "N";
                    }
                }

                if ($en_orden == "N") {
                    $respuesta["message"] = "Debe primero pagar los mas antiguos, por favor verifique!";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                    exit;
                }

                //verifico que el socio exista y pertenezca al club
                $id_socio = $dbo->getFields("Socio", "IDSocio", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                if (!empty($id_socio)) {

                    //Datos reserva
                    $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
                    $response_reserva = array();
                    $datos_reserva["IDRegistro"] = time();
                    //Calculo el valor de la reserva
                    $valor_inicial_reserva = (float) $ValorPagar;
                    $datos_reserva["ValorReserva"] = $ValorPagar;
                    $ValorReserva = $datos_reserva["ValorReserva"];
                    $llave_encripcion = $datos_club["ApiKey"]; //llave de encripciÛn que se usa para generar la fima
                    $usuarioId = $datos_club["ApiLogin"]; //c0digo inicio del cliente
                    $refVenta = time(); //referencia que debe ser ?nica para cada transacciÛn
                    $iva = 0; //impuestos calculados de la transacciÛn
                    $baseDevolucionIva = 0; //el precio sin iva de los productos que tienen iva
                    $valor = $datos_reserva["ValorReserva"] + (($datos_reserva["ValorReserva"] * $ArrayParametro["Iva"]) / 100); //el valor ; //el valor total
                    $moneda = "COP"; //la moneda con la que se realiza la compra
                    $prueba = "0"; //variable para poder utilizar tarjetas de crÈdito de prueba
                    $descripcion = "Pago Cartera Condado."; //descripciÛn de la transacciÛn
                    $url_respuesta = URLROOT . "respuesta_transaccion_evento.php"; //Esta es la p·gina a la que se direccionar· al final del pago
                    $url_confirmacion = URLROOT . "confirmacion_pagos_evento.php";
                    $emailSocio = $dbo->getFields("Socio", "CorreoElectronico", "IDSocio =" . $IDSocio); //email al que llega confirmaciÛn del estado final de la transacciÛn, forma de identificar al comprador
                    if (filter_var(trim($emailSocio), FILTER_VALIDATE_EMAIL)) {
                        $emailComprador = $emailSocio;
                    } else {
                        $emailComprador = "";
                    }

                    $firma_cadena = "$llave_encripcion~$usuarioId~$refVenta~$valor~$moneda"; //concatenaciÛn para realizar la firma
                    $firma = md5($firma_cadena); //creaciÛn de la firma con la cadena previamente hecha
                    $extra1 = $IDSocio;

                    $datos_reserva["Action"] = "https://miclubapp.com/placetopaydeuda.php";

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
                    $datos_post["valor"] = $ValorReserva;
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

                    $datos_post["llave"] = "Documento";
                    $datos_post["valor"] = (string) $Documento;
                    array_push($response_parametros, $datos_post);

                    $datos_reserva["ParametrosPost"] = $response_parametros;

                    $respuesta["message"] = "guardado";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $datos_reserva;
                } else {
                    $respuesta["message"] = "No identificado, por favor cierre sesion y vuelva a ingresar!";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "D1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function get_verifica_accion($IDClub, $IDSocio, $IDUsuario, $IDNotificacion)
        {
            $dbo = &SIMDB::get();
            $FechaHoy = date("Y-m-d");
            //Modulos soportados
            $array_tabla_soportado = array("99" => "Diagnostico", "58" => "Encuesta", "101" => "Encuesta2", "70" => "Votacion", "102" => "Dotacion");
            $array_tabla_resp_soportado = array("99" => "DiagnosticoRespuesta", "58" => "EncuestaRespuesta", "101" => "Encuesta2Respuesta", "70" => "VotacionRespuesta", "102" => "DotacionRespuesta");
            if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && count($IDNotificacion) > 0) {

                $datos_notif = json_decode($IDNotificacion, true);

                $response_notif = array();
                foreach ($datos_notif as $datos_notif) {
                    $id_notif = $datos_notif["IDNotificacion"];
                    $datos_notif = $dbo->fetchAll("NotificacionLocal", " IDNotificacionLocal = '" . $id_notif . "' and IDClub = '" . $IDClub . "' ", "array");
                    if (!empty($datos_notif["IDModulo"]) && !empty($datos_notif["IDDetalle"])) {
                        $Tabla = $array_tabla_soportado[$datos_notif["IDModulo"]];
                        $IDTabla = "ID" . $Tabla;
                        $TablaResp = $array_tabla_resp_soportado[$datos_notif["IDModulo"]];
                        $IDTablaResp = "ID" . $TablaResp;
                        $parametro_busqueda = $IDTabla . "= '" . $datos_notif["IDDetalle"] . "' and IDClub  = '" . $IDClub . "'";
                        $datos_modulo = $dbo->fetchAll($Tabla, $parametro_busqueda, "array");
                        if ($datos_modulo["UnaporSocio"] == "N") {
                            $condicion = " and FechaTrCr >= '" . $FechaHoy . " 00:00:00'";
                        } else {
                            $condicion = "";
                        }

                        if (!empty($IDUsuario)) {
                            $IDSocio = $IDUsuario;
                        }

                        $sql_responde = "SELECT  " . $IDTablaResp . " FROM " . $TablaResp . " WHERE IDSocio = '" . $IDSocio . "' and  " . $IDTabla . " = '" . $datos_notif["IDDetalle"] . "' " . $condicion . " Limit 1";
                        $r_responde = $dbo->query($sql_responde);
                        if ($dbo->rows($r_responde) > 0) {
                            $array_modulo_resp["IDNotificacionLocal"] = $id_notif;
                            $array_modulo_resp["Respondido"] = "S";
                        } else {
                            $array_modulo_resp["IDNotificacionLocal"] = $id_notif;
                            $array_modulo_resp["Respondido"] = "N";
                        }
                    } else {
                        $array_modulo_resp["IDNotificacionLocal"] = $id_notif;
                        $array_modulo_resp["Respondido"] = "S";
                    }
                    array_push($response_notif, $array_modulo_resp);
                }

                $response["Notificacion"] = $response_notif;

                $respuesta["message"] = "Respuesta";
                $respuesta["success"] = true;
                $respuesta["response"] = $response;
            } else {
                $respuesta["message"] = "NL. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function get_verifica_documento($IDClub, $NumeroDocumento)
        {
            $dbo = &SIMDB::get();
            $FechaHoy = date("Y-m-d");
            $IDInvitado = $dbo->getFields("Invitado", "IDInvitado", "NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "'");
            if ((int) $IDInvitado > 0) {
                //Consulto si tuene una invitacion activa
                $IDInvitacion = $dbo->getFields("SocioInvitado", "IDSocioInvitado", "IDClub = '" . $IDClub . "' and FechaIngreso = '" . $FechaHoy . "' and NumeroDocumento = '" . $NumeroDocumento . "'");
                $IDInvitacionEspecial = $dbo->getFields("SocioInvitadoEspecial", "IDInvitado", "IDInvitado = '" . $IDInvitado . "' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ");
                $IDInvitacionAutorizacion = $dbo->getFields("SocioAutorizacion", "IDInvitado", "IDInvitado = '" . $IDInvitado . "' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() ");

                if (!empty($IDInvitacion) || !empty($IDInvitacionEspecial) || !empty($IDInvitacionAutorizacion)) {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'ok', LANG);
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                } else {
                    $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Eldocumentonotieneinvitacionparaeldiadehoy,recuerdediligenciaresteformularioeldiadesuingreso', LANG);
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Eldocumentonotieneinvitacionoautorizaciondeingreso', LANG);
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function valida_cierre_sesion($IDSocio)
        {
            $dbo = &SIMDB::get();
            $IDSocioCierre = $dbo->getFields("CierreSesionSocio", "IDSocio", "IDSocio = '" . $IDSocio . "'");
            if ((int) $IDSocioCierre > 0) : //El socio necesita cerrar sesion por alguna razon
                $respuesta = 1;
            else :
                $respuesta = 0;
            endif;
            return $respuesta;
        }

        public function set_cerrar_sesion($IDClub, $IDSocio)
        {
            $dbo = &SIMDB::get();
            if (!empty($IDSocio)) :
                $sql_actualiza_cierre = "Update Socio Set SolicitarCierreSesion = 'N', FechaCierreSesion = NOW() Where IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'";
                $dbo->query($sql_actualiza_cierre);
                $respuesta["message"] = "Sesion cerrada correctamente";
                $respuesta["success"] = true;
                $respuesta["response"] = null;
            else :
                $respuesta["message"] = "El socio no existe";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;

            return $respuesta;
        }

        public function get_datos_socio($IDClub, $Identificacion, $Todos)
        {

            $dbo = &SIMDB::get();
            if (!empty($Identificacion)) {
                $foto = "";
                $foto_cod_barras = "";
                $sql_verifica = "SELECT * FROM Socio WHERE NumeroDocumento = '" . $Identificacion . "'  and IDClub = '" . $IDClub . "'";
                $qry_verifica = $dbo->query($sql_verifica);
                if ($dbo->rows($qry_verifica) == 0) {
                    $respuesta["message"] = "El socio no pertenece al club";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                } //end if
                else {
                    $datos_socio = $dbo->fetchArray($qry_verifica);

                    if (!empty($datos_socio["Foto"])) {
                        $foto = SOCIO_ROOT . $datos_socio["Foto"];
                    }

                    $tipo_codigo_carne = $dbo->getFields("Club", "TipoCodigoCarne", "IDClub = '" . $IDClub . "'");

                    switch ($tipo_codigo_carne) {
                        case "Barras":
                            if (!empty($datos_socio["CodigoBarras"])) {
                                $foto_cod_barras = SOCIO_ROOT . $datos_socio["CodigoBarras"];
                            }
                            break;
                        case "QR":
                            if (!empty($datos_socio["CodigoQR"])) {
                                $foto_cod_barras = SOCIO_ROOT . "qr/" . $datos_socio["CodigoQR"];
                            }
                            break;
                        default:
                            $foto_cod_barras = "";
                    }

                    $tipo_socio = $datos_socio["TipoSocio"];
                    $response["IDClub"] = $datos_socio["IDClub"];
                    $response["IDSocio"] = $datos_socio["IDSocio"];
                    $response["Foto"] = $foto;
                    $response["Socio"] = utf8_encode($datos_socio["Nombre"] . " " . $datos_socio["Apellido"]);
                    $response["NumeroDerecho"] = $datos_socio["Accion"];
                    $response["CodigoBarras"] = $foto_cod_barras;
                    $response["Dispositivo"] = $datos_socio["Dispositivo"];
                    $response["TipoSocio"] = $tipo_socio;
                    $respuesta["message"] = "ok";
                    $respuesta["success"] = true;
                    $respuesta["response"] = $response;
                }
            } else {
                $respuesta["message"] = "WS1. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        } //end function

        public function get_codigo_pago_evento($IDClub, $CodigoPago)
        {

            $dbo = &SIMDB::get();
            if (!empty($IDClub) && !empty($CodigoPago)) {

                //verifico que el codigo exista y no haya sido utilizado
                $id_codigo = $dbo->getFields("ClubCodigoPago", "IDClubCodigoPago", "Codigo = '" . $CodigoPago . "' and IDClub = '" . $IDClub . "' and Disponible = 'S'");

                if (!empty($id_codigo)) {

                    $respuesta["message"] = "Codigo correcto";
                    $respuesta["success"] = true;
                    $respuesta["response"] = null;
                } else {
                    $respuesta["message"] = "El codigo no es valido";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                }
            } else {
                $respuesta["message"] = "52. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }

        public function insertar_secciones($IDSocio, $IDClub)
        {
            $dbo = &SIMDB::get();
            $sql_soc = "SELECT IDSocio FROM Socio WHERE IDClub = '" . $IDClub . "' AND IDSocio='" . $IDSocio . "'";
            $result_soc = $dbo->query($sql_soc);
            while ($row_soc = $dbo->fetchArray($result_soc)) :

                //Seccion Noticias 1
                $sql_secc_club = "SELECT IDSeccion From Seccion Where IDClub = '" . $IDClub . "'";
                $result_secc_club = $dbo->query($sql_secc_club);
                while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                    //Verifico si ya el socio la tiene si no se la creo
                    $sql_soci_secc = "SELECT IDSocio,IDSeccion From SocioSeccion Where IDSeccion = '" . $row_secc["IDSeccion"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                    $result_soci_secc = $dbo->query($sql_soci_secc);
                    if ($dbo->rows($result_soci_secc) <= 0) :
                        $insert_secc = "Insert into SocioSeccion (IDSocio, IDSeccion) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccion"] . "')";
                        $dbo->query($insert_secc);
                    //$count_noticia++;
                    endif;
                }
                //Fin Seccion Noticias

                //Seccion Noticias 2
                $sql_secc_club = "SELECT IDSeccion From Seccion2 Where IDClub = '" . $IDClub . "'";
                $result_secc_club = $dbo->query($sql_secc_club);
                while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                    //Verifico si ya el socio la tiene si no se la creo
                    $sql_soci_secc = "SELECT IDSocio,IDSeccion From SocioSeccion2 Where IDSeccion = '" . $row_secc["IDSeccion"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                    $result_soci_secc = $dbo->query($sql_soci_secc);
                    if ($dbo->rows($result_soci_secc) <= 0) :
                        $insert_secc = "Insert into SocioSeccion2 (IDSocio, IDSeccion) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccion"] . "')";
                        $dbo->query($insert_secc);
                    // $count_noticia++;
                    endif;
                }

                //Seccion Noticias 3
                $sql_secc_club = "SELECT IDSeccion From Seccion3 Where IDClub = '" . $IDClub . "'";
                $result_secc_club = $dbo->query($sql_secc_club);
                while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                    //Verifico si ya el socio la tiene si no se la creo
                    $sql_soci_secc = "SELECT IDSocio,IDSeccion From SocioSeccion3 Where IDSeccion = '" . $row_secc["IDSeccion"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                    $result_soci_secc = $dbo->query($sql_soci_secc);
                    if ($dbo->rows($result_soci_secc) <= 0) :
                        $insert_secc = "Insert into SocioSeccion3 (IDSocio, IDSeccion) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccion"] . "')";
                        $dbo->query($insert_secc);
                    // $count_noticia++;
                    endif;
                }
                //Fin Seccion Noticias

                //Seccion Galerias
                $sql_secc_club = "SELECT IDSeccionGaleria From SeccionGaleria Where IDClub = '" . $IDClub . "'";
                $result_secc_club = $dbo->query($sql_secc_club);
                while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                    //Verifico si ya el socio la tiene si no se la creo
                    $sql_soci_secc = "SELECT IDSocio,IDSeccion From SocioSeccionGaleria Where IDSeccionGaleria = '" . $row_secc["IDSeccionGaleria"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                    $result_soci_secc = $dbo->query($sql_soci_secc);
                    if ($dbo->rows($result_soci_secc) <= 0) :
                        $insert_secc = "Insert into SocioSeccionGaleria (IDSocio, IDSeccionGaleria) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccionGaleria"] . "')";
                        $dbo->query($insert_secc);

                    endif;
                }
                //FIN Seccion Galerias

                //Seccion Eventos 1
                $sql_secc_club = "SELECT IDSeccionEvento From SeccionEvento Where IDClub = '" . $IDClub . "'";
                $result_secc_club = $dbo->query($sql_secc_club);
                while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                    //Verifico si ya el socio la tiene si no se la creo
                    $sql_soci_secc = "SELECT IDSocio,IDSeccion From SocioSeccionEvento Where IDSeccionEvento = '" . $row_secc["IDSeccionEvento"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                    $result_soci_secc = $dbo->query($sql_soci_secc);
                    if ($dbo->rows($result_soci_secc) <= 0) :
                        $insert_secc = "Insert into SocioSeccionEvento (IDSocio, IDSeccionEvento) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccionEvento"] . "')";
                        $dbo->query($insert_secc);
                    //$count_evento++;
                    endif;
                }
                //FIN Seccion Galerias

                //Seccion Eventos 2
                $sql_secc_club = "SELECT IDSeccionEvento2 From SeccionEvento2 Where IDClub = '" . $IDClub . "'";
                $result_secc_club = $dbo->query($sql_secc_club);
                while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                    //Verifico si ya el socio la tiene si no se la creo
                    $sql_soci_secc = "SELECT IDSocio,IDSeccion From SocioSeccionEvento2 Where IDSeccionEvento2 = '" . $row_secc["IDSeccionEvento2"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                    $result_soci_secc = $dbo->query($sql_soci_secc);
                    if ($dbo->rows($result_soci_secc) <= 0) :
                        $insert_secc = "Insert into SocioSeccionEvento2 (IDSocio, IDSeccionEvento2) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccionEvento2"] . "')";
                        $dbo->query($insert_secc);
                    endif;
                }
                //FIN Seccion Galerias

                //Seccion Clasificado
                $sql_secc_club = "SELECT IDSeccionClasificados From SeccionClasificados Where IDClub = '" . $IDClub . "'";
                $result_secc_club = $dbo->query($sql_secc_club);
                while ($row_secc = $dbo->fetchArray($result_secc_club)) {
                    //Verifico si ya el socio la tiene si no se la creo
                    $sql_soci_secc = "SELECT IDSocio,IDSeccion From SocioSeccionClasificados Where IDSeccionClasificados = '" . $row_secc["IDSeccionClasificados"] . "' and IDSocio = '" . $row_soc["IDSocio"] . "'";
                    $result_soci_secc = $dbo->query($sql_soci_secc);
                    if ($dbo->rows($result_soci_secc) <= 0) :
                        $insert_secc = "Insert into SocioSeccionClasificados (IDSocio, IDSeccionClasificados) Values ('" . $row_soc["IDSocio"] . "','" . $row_secc["IDSeccionClasificados"] . "')";
                        $dbo->query($insert_secc);
                    endif;
                }
            //FIN Seccion Galerias


            endwhile;
        }

        public function actualizar_edad($fechaNacimiento, $IDClub)
        {
            $dbo = SIMDB::get();
            setlocale(LC_TIME, 'es_ES.UTF-8');

            $categorias = array();
            $hoy = new DateTime();
            $IDCategoria = 0;

            if ($fechaNacimiento != '' && $IDClub != '') :
                $sqlCategorias = "SELECT IDCategoria, Edad FROM Categoria WHERE (LOWER(Nombre) = 'caminadores' OR LOWER(Nombre) = 'corredores' OR LOWER(Nombre) = 'gateadores') AND IDClub = $IDClub";
                $qryCategorias = $dbo->query($sqlCategorias);

                while ($rowCategorias = $dbo->fetchArray($qryCategorias)) :
                    $categorias[$rowCategorias['IDCategoria']] = $rowCategorias['Edad'];
                endwhile;

                $fechaNacimiento = new DateTime($fechaNacimiento);
                $diff = $fechaNacimiento->diff($hoy);
                $edadDias = $diff->days;

                foreach ($categorias as $id => $edades) :
                    $arrEdades = explode('|', substr($edades, 1, -1));

                    $edadIn = round((int)$arrEdades[0] * 30.4167);
                    $edadFn = round((int)end($arrEdades) * 30.4167);

                    if ($edadDias >= $edadIn && $edadDias < $edadFn)
                        $IDCategoria = $id;

                endforeach;

            endif;

            return $IDCategoria;
        }

        public function validar_cambio_foto_perfil_usuario($IDClub, $IDSocio, $IDUsuario)
        {
            $dbo = &SIMDB::get();
            $response = array();
            if (!empty($IDClub)) {
                //verifico que el codigo exista y no haya sido utilizado
                $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");
                if ((int)$IDSocio > 0)
                    $datos_usuario = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
                else
                    $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");

                if (empty($datos_club_otros["SolicitarCambioFotoPerfilLabel"])) {
                    $LabelCambiarFoto = "Debe actualizar su foto de perfil";
                } else {
                    $LabelCambiarFoto = $datos_club_otros["SolicitarCambioFotoPerfilLabel"];
                }

                $configuracion["SolicitarCambioFotoPerfil"] = $datos_usuario["SolicitarCambioFotoPerfil"];
                $configuracion["SolicitarCambioFotoPerfilLabel"] = $LabelCambiarFoto;

                $respuesta["message"] = "1 encontrados";
                $respuesta["success"] = true;
                $respuesta["response"] = $configuracion;
            } else {
                $respuesta["message"] = "58. Atencion faltan parametros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }

            return $respuesta;
        }
    }
