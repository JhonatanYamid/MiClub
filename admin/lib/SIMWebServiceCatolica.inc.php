<?php

class SIMWebServiceCatolica
{

    public function valida_catolica($id_club, $email, $clave)
    {
        $dbo = &SIMDB::get();

        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $respuesta = array();
        $email = str_replace("@ucatolica.edu.co", "", $email);
        $datos_validar = trim($email) . "|" . trim($clave);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => HOST_CATOLICA_DA,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>
            "<Envelope xmlns=\"http://schemas.xmlsoap.org/soap/envelope/\">\n
				  		<Body>\n
				  			<fUsers xmlns=\"http://portalweb.ucatolica.edu.co/easyWeb2/admin/ws/login\">\n
						  	<usrOnline>" . $datos_validar . "</usrOnline>\n</fUsers>\n
						</Body>\n
					</Envelope>",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: text/xml",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);




        $p = xml_parser_create();
        xml_parse_into_struct($p, $response, $vals, $index);
        xml_parser_free($p);
        //echo "Index array\n";
        //print_r($index);
        //echo "\nVals array\n";
        //print_r($vals);

        $validacion = $vals[3]["value"];
        if ($validacion == 1) {
            //consulto los datos de la persona
            //$email="caorozco98";
            $params = array(
                "login" => $email,
            );

            $client  = new SoapClient(HOST_CATOLICA_DATOS . "Datos_acceso_app?wsdl", [
                'stream_context' => $context
            ]);

            $response = $client->__soapCall("Datos_accesos_app_OPR", array($params));

            $Identificacion = $response->identificacion;
            $DocumentoIdentificacion = trim($response->identificacion1);
            $Nombre = $response->nombre;
            $Correo = $response->email;
            $TipoUsuario = $response->Tipo_usuario;

            /*
            if($TipoUsuario=="Otro"){
            $TipoUsuario="Estudiante";
            $CampoFotoUtilizar=$DocumentoIdentificacion;
            }

            if($TipoUsuario!="Estudiante"){
            $TipoUsuario="Empleado";
            $CampoFotoUtilizar=trim($Identificacion);
            }
             */

            //if($TipoUsuario=="Administrativo")
            //                        $TipoUsuario="Administracion";

            if ($TipoUsuario != "Estudiante") {
                $TipoUsuario = "Empleado";
                $CampoFotoUtilizar = trim($Identificacion);
            }

            if (empty($CampoFotoUtilizar)) {
                $CampoFotoUtilizar = $DocumentoIdentificacion;
            }

            if (!empty($Identificacion)) {
                //actualizar la persona
                $Accion = trim($Identificacion);
                $AccionPadre = $Identificacion;
                $Parentesco = $TipoUsuario;
                $Genero = "";
                $Nombre = str_replace("'", " ", $Nombre);
                $Apellido = "";
                $FechaNacimiento = "";
                $NumeroDocumento = trim($Identificacion);
                $CorreoElectronico = trim($Correo);
                $Telefono = "";
                $Celular = "";
                $Direccion = "";
                $TipoSocio = $TipoUsuario;
                $InvitacionesPermitidasMes = "0";
                $UsuarioApp = $email;
                //Otros $dato_sumar
                $Predio = "";
                $Categoria = "";
                $EstadoSocio = "A";
                $CodigoCarne = $Identificacion;
                //if($TipoUsuario=="Estudiante"){


                $IDEstadoSocio = $dbo->getFields( "Socio", "IDEstadoSocio", "NumeroDocumento = '".$Identificacion."' and IDClub = '" . $id_club . "' " );
                if($IDEstadoSocio==2){
                  $EstadoSocio = "I";
                  $respuesta["estado"] = "inactivo";
                  $respuesta["mensaje"] = "Error al guardar los datos";
                }
                else{
                  $resp = SIMWebServiceApp::set_socio($id_club, $Accion, $AccionPadre, $Parentesco, $Genero, $Nombre, $Apellido, $FechaNacimiento, $NumeroDocumento, $CorreoElectronico, $Telefono, $Celular, $Direccion, $TipoSocio, $EstadoSocio, $InvitacionesPermitidasMes, $UsuarioApp, $Predio, $Categoria, "", $CodigoCarne);
                  if ($resp["success"] == 1) {
                      $NombreFoto = $CampoFotoUtilizar . ".JPG";

                      $url = URL_CATOLICA_WS_FOTOS."identificacion=".$CampoFotoUtilizar;
                      $ch = curl_init($url);
                      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                      curl_setopt($ch, CURLOPT_HEADER, false);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                      curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                      $resultadofoto = curl_exec($ch);
                      if ($resultadofoto === false) {
                        //echo 'Curl error: ' . curl_error($ch);
                      }

                    curl_close($ch);
                    $p = xml_parser_create();
                    xml_parse_into_struct($p, $resultadofoto, $vals, $index);
                    xml_parser_free($p);
                    $ruta_foto = $vals[2]["value"];
                    if(!empty($ruta_foto)){
                        
                        $RutaFinalFoto=$ruta_foto;
                        $sql_socio = "UPDATE Socio Set Foto='" . $NombreFoto . "',FotoActualizadaSocio='N' Where IDClub = '" . $id_club . "' and NumeroDocumento = '" . $NumeroDocumento . "'";
                        $dbo->query($sql_socio);

                        //copiar la foto
                        $url = $RutaFinalFoto;
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                        $result = curl_exec($ch);
                        if ($result === false) {
                            //echo 'Curl error: ' . curl_error($ch);
                        }
                        $fp = fopen(SOCIO_DIR . $NombreFoto, 'w+');
                        if (fwrite($fp, $result)) {
                            //echo "OK ".SOCIO_DIR.$NombreFoto;
                            fclose($fp);
                        }
                        curl_close($ch);
                        //Fin Copiar Foto
                    }


                      

                      $respuesta["estado"] = "ok";
                      $respuesta["mensaje"] = "correcto";
                      $respuesta["codigoempleado"] = $email;
                  } else {
                      $respuesta["estado"] = "errorguardar";
                      $respuesta["mensaje"] = "Error al guardar los datos";
                  }

                }






            } else {
                $respuesta["estado"] = "errordatos";
                $respuesta["mensaje"] = "no fue posible obtener los datos del usuario";
            }
        } else {
            $respuesta["estado"] = "errorguardar";
            $respuesta["mensaje"] = "datos incorrectos";
        }
        return $respuesta;
    }

  public function token_profesor_catolica($DocDocente)
    {  
    
        $curl = curl_init(); 
         $GET=$DocDocente;
         
         
             
             //insertamos el token y validamos que este correcto el token para su uso
         $url1 = "https://wso2dsvs.ucatolica.edu.co:8885/services/ins_con_app_from_paw_tk.SecureSOAP11Endpoint/insert_tk_OPR?DCNIDN=$GET";
         $ch1 = curl_init($url1);
         curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($ch1, CURLOPT_HEADER, false);
         curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch1, CURLOPT_BINARYTRANSFER, 1);
         $resultadofoto1 = curl_exec($ch1);
         if ($resultadofoto1 === false) {
             $estado= "ERROR!"; 
         }
 
             curl_close($ch1);
             $p1 = xml_parser_create();
             xml_parse_into_struct($p1, $resultadofoto1, $vals1, $index1);
             xml_parser_free($p1);
             
             $respuesta = $vals1[0]["value"];
             
             if($respuesta=="SUCCESSFUL"){
           $estado= "CORRECTO!"; 
             }else{
           $estado= "ERROR!"; 
             } 
             

         $url = "https://wso2dsvs.ucatolica.edu.co:8885/services/ins_con_app_from_paw_tk.SecureSOAP11Endpoint/Consulta_tk_OPR?DCNIDN=$GET";
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($ch, CURLOPT_HEADER, false);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
         $resultadofoto = curl_exec($ch);
         if ($resultadofoto === false) {
          echo   $token = "Lo sentimos, no se pudo generar el token";
         }
 
             curl_close($ch);
             $p = xml_parser_create();
             xml_parse_into_struct($p, $resultadofoto, $vals, $index);
             xml_parser_free($p);
             
             //datos generales de operacion
 
              $fecha_inicia = $vals[2]["value"];
              $fecha_fin= $vals[3]["value"];
              $token = $vals[4]["value"]; 
              $estado_token= $vals[5]["value"];  //G-> generado - V->Vencido
             
             
            
             //array 0->token,1->estado_token,2->horainicial->,3->horavencimiento,4->estado de operacion
$datos= "$token,$estado_token,$fecha_inicia,$fecha_fin,$estado";
 
$array = explode(",", $datos);

             return $array;
         

// 
    }
    
    
 

    public function consulta_identificacion($id_club, $NumeroDocumento)
    {
        $dbo = &SIMDB::get();

        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $respuesta = array();
        //consulto los datos de la persona
        $params = array(
            "Identificacion" => $NumeroDocumento,
        );
        $client  = new SoapClient(HOST_CATOLICA_DATOS . "Datos_acceso_NI_app?wsdl", [
            'stream_context' => $context
        ]);
        $response = $client->__soapCall("Datos_accesos_app_NI_OPR", array($params));

        $Identificacion = $response->identificacion;
        $Nombre = $response->nombre;
        $Correo = $response->email;
        $TipoUsuario = $response->Tipo_usuario;
        $Identificacion2 = trim($response->identificacion1);

        if ($TipoUsuario == "Otro") {
            $TipoUsuario = "Estudiante";
        }

        if ($TipoUsuario != "Estudiante") {
            $TipoUsuario = "Empleado";
        }

        if (!empty($Identificacion)) {
            //actualizar la persona
            $Accion = trim($Identificacion);
            $AccionPadre = $Identificacion;
            $Parentesco = $TipoUsuario;
            $Genero = "";
            $Nombre = $Nombre;
            $Apellido = "";
            $FechaNacimiento = "";
            $NumeroDocumento = trim($Identificacion);
            $CorreoElectronico = trim($Correo);
            $Telefono = "";
            $Celular = "";
            $Direccion = "";
            $TipoSocio = $TipoUsuario;
            $InvitacionesPermitidasMes = "0";
            $UsuarioApp = $email;
            //Otros $dato_sumar
            $Predio = "";
            $Categoria = "";
            $EstadoSocio = "A";
            $CodigoCarne = $Identificacion;

            //if($TipoUsuario=="Estudiante"){
            //$resp=SIMWebServiceApp::set_socio($id_club,$Accion,$AccionPadre,$Parentesco,$Genero,$Nombre,$Apellido,$FechaNacimiento,$NumeroDocumento,$CorreoElectronico,$Telefono,$Celular,$Direccion,$TipoSocio,$EstadoSocio,$InvitacionesPermitidasMes,$UsuarioApp,$Predio,$Categoria,"",$CodigoCarne);

            if ($resp["success"] == 1) {

                /*
                if($resp["success"]==1){

                if($TipoUsuario=="Estudiante")
                $NombreFoto=$Identificacion2.".JPG";
                else
                $NombreFoto=$NumeroDocumento.".JPG";

                //$NombreFoto=$NumeroDocumento.".JPG";
                $sql_socio="UPDATE Socio Set SolicitaEditarPerfil='S', Accion='".$Identificacion2."',Foto='".$NombreFoto."',FotoActualizadaSocio='N' Where IDClub = '".$id_club."' and NumeroDocumento = '".$NumeroDocumento."'";
                $dbo->query($sql_socio);

                //copiar la foto
                $url = URL_CATOLICA_FOTOS.$NombreFoto;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
                $result = curl_exec($ch);
                if ($result === false) {
                //echo 'Curl error: ' . curl_error($ch);
                }
                $fp = fopen(SOCIO_DIR.$NombreFoto,'w+');
                if(fwrite($fp, $result)){
                //echo "OK ".SOCIO_DIR.$NombreFoto;
                fclose($fp);
                }
                curl_close($ch);
                //Fin Copiar Foto

                $respuesta["estado"]="ok";
                $respuesta["mensaje"]="correcto";
                $respuesta["codigoempleado"]=$email;
                }
                 */

                $respuesta["estado"] = "ok";
                $respuesta["mensaje"] = "correcto";
                $respuesta["codigoempleado"] = $email;
            } else {
                $respuesta["estado"] = "erroguardar";
                $respuesta["mensaje"] = "Error al guardar los datos";
            }
        } else {
            $respuesta["estado"] = "errordatos";
            $respuesta["mensaje"] = "no fue posible obtener los datos del usuario";
        }

        return $respuesta;
    }

    public function get_horario($IDClub, $IDSocio, $Fecha)
    {

        $dbo = &SIMDB::get();

        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);


        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Identificacion = $datos_socio["NumeroDocumento"];

        if (!empty($Identificacion)) {
            $params = array(
                "Identificacion" => $Identificacion,
            );

            $client  = new SoapClient(HOST_CATOLICA_DATOS . "Datos_horarios_app?wsdl", [
                'stream_context' => $context
            ]);
            $response = $client->__soapCall("Datos_horarios_app_OPR", array($params));
            //print_r($response);
            foreach ($response as $key_datos => $datos) {
                foreach ($datos as $key => $datos_dia) {
                    if ($key_datos == "Codigo_dia") {
                        $array_dias[$datos_dia][] = $key;
                        $array_posicion[$key] = $datos_dia;
                    }
                }
            }

            foreach ($response as $key_datos => $datos) {
                foreach ($datos as $key => $datos_horario) {
                    $DiaSemana = $array_posicion[$key];
                    $array_horario_semana[$DiaSemana][$key_datos][] = $datos_horario;
                }
            }

            $respuesta["message"] = "Horario";
            $respuesta["success"] = true;
            $respuesta["response"] = $array_horario_semana;

            //print_r($array_posicion);
            //print_r($array_horario_semana);

        } else {
            $respuesta["message"] = "No se encontro la persona";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    function get_horario_materia($IDClub, $NumeroDocumento, $Fecha)
  	{

  			$dbo = &SIMDB::get();

  			$context = stream_context_create([
  					'ssl' => [
  							// set some SSL/TLS specific options
  							'verify_peer' => false,
  							'verify_peer_name' => false,
  							'allow_self_signed' => true
  					]
  			]);


  			$Identificacion = $NumeroDocumento;

  			if (!empty($Identificacion)) {
  					$params = array(
  							"Identificacion" => $Identificacion,
  					);

            $client  = new SoapClient(HOST_CATOLICA_DATOS . "Datos_horarios_app?wsdl", [
  							'stream_context' => $context
  					]);
  					$response = $client->__soapCall("Datos_horarios_app_OPR", array($params));

  					$total_datos = count($response->Codigo_asignatura);
  					if ($total_datos > 0) {
  							for ($i = 0; $i <= ($total_datos - 1); $i++) {
  									$CodigoAsignatura = $response->Codigo_asignatura[$i];
  									$NombreAsignatura = $response->Nombre_asignatura[$i];
  									$Dia = $response->Codigo_dia[$i];
  									$Grupo = $response->Grupo[$i];
  									$Codigo_dia = $response->Codigo_dia[$i];
  									$Sede = $response->Sede[$i];
  									$Edificio = $response->Edificio[$i];
  									$Salon = $response->Salon[$i];
  									$Jornada = $response->Jornada[$i];
  									$HoraInicial = $response->Hora_inicial[$i];
  									$HoraFinal = $response->Hora_final[$i];


  									$array_materia[$CodigoAsignatura]["Datos"] = $NombreAsignatura . "|" . $Jornada . "|" . $Grupo . "|" . $CodigoAsignatura . "|" . $Sede . "|" . $Edificio . "|" . $Salon;
  									$array_materia[$CodigoAsignatura][$Dia][] = $HoraInicial . "|" . $HoraFinal;
  									$array_materia[$CodigoAsignatura]["Dia"][$Dia]["Sede"][$HoraInicial] = $Sede . "|" . $Edificio . "|" . $Salon;
  							}
  					}

  					$respuesta["message"] = "Horario";
  					$respuesta["success"] = true;
  					$respuesta["response"] = $array_materia;

  					//print_r($array_posicion);
  					//print_r($array_horario_semana);

  			} else {
  					$respuesta["message"] = "No se encontro la persona";
  					$respuesta["success"] = false;
  					$respuesta["response"] = null;
  			}

  			return $respuesta;
  	}

    public function con_clase($datos_dias)
    {
        $dia_actual = date("N");
        $con_clase = "N";
        //print_r($datos_dias[$dia_actual]["Sede"]);
        foreach ($datos_dias[$dia_actual] as $key_dato => $valor_dato) {
            foreach ($valor_dato as $posicion => $valor) {
                $array_datos[$posicion][] = $valor;
            }
        }
        foreach ($array_datos as $key_dato => $valor_dato) {
            $valor_imprimir = "";
            $Sede = $valor_dato[6];
            if ($Sede != "VIR") {
                $con_clase = "S";
            }

        }

        return $con_clase;
    }

    public function set_acceso($IDClub, $IDSocio = "", $IDUsuario = "", $Tipo, $TipoApp)
    {
        $dbo = &SIMDB::get();

        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        if (!empty($IDSocio)) {
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
            $Identificacion = $datos_socio["NumeroDocumento"];
        } elseif (!empty($IDUsuario)) {
            $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");
            $Identificacion = $datos_usuario["NumeroDocumento"];
        }

        if (!empty($Identificacion)) {
            $params = array(
                "Identificacion" => $Identificacion,
                "Estado" => $Tipo,
            );

            $client  = new SoapClient(HOST_CATOLICA_DATOS . "Activacion_inactivacion_ps_app?wsdl", [
                'stream_context' => $context
            ]);

            if ($Tipo == "N") { //No activo
                $response = $client->__soapCall("Inactivacion_ps_app_OPR ", array($params));
                $respuesta["message"] = "Acceso Inactivado";
                $respuesta["success"] = true;
                $respuesta["response"] = $array_horario_semana;
            } elseif ($Tipo == "A") { //Activo
                $response = $client->__soapCall("Activacion_ps_app_OPR", array($params));
                $respuesta["message"] = "Acceso Activado";
                $respuesta["success"] = true;
                $respuesta["response"] = $array_horario_semana;
            } else {
                $respuesta["message"] = "Valor invalido";
                $respuesta["success"] = false;
                $respuesta["response"] = $array_horario_semana;

            }
        } else {
            $respuesta["message"] = "No se encontro la persona.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_fecha_alternancia($IDClub, $NumeroDocumento, $Fecha)
    {

        $dbo = &SIMDB::get();

        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $Identificacion = $NumeroDocumento;
        $DebeAsistir = "N";

        if (!empty($Identificacion)) {
            $params = array(
                "Identificacion" => $Identificacion,
            );

            $client  = new SoapClient(HOST_CATOLICA_DATOS . "Consulta_fecha_alternancia_app?wsdl", [
                'stream_context' => $context
            ]);
            $response = $client->__soapCall("Consulta_fecha_alternancia_app_OPR", array($params));

            $total_datos = count($response->Identificacion);
            if ($total_datos == 1) {
                $array_fechas[0]["Identificacion"] = $response->Identificacion;
                $array_fechas[0]["FechaAsistencia"] = $response->Fecha_asistencia;
                $array_fechas[0]["HoraInicial"] = $response->Hora_inicial;
                $array_fechas[0]["HoraFinal"] = $response->Hora_final;
                $array_fechas[0]["Sede"] = $response->Sede;
                $array_fechas[0]["Observaciones"] = $response->Observaciones;
                $fecha_actual = substr($response->Fecha_asistencia, 0, 4) . "-" . substr($response->Fecha_asistencia, 4, 2) . "-" . substr($response->Fecha_asistencia, 6, 2);
                if ($Fecha == $fecha_actual) {
                    $DebeAsistir = "S" . "|" . $response->Hora_inicial . "|" . $response->Hora_final;
                }
            } else {
                if ($total_datos > 0) {
                    for ($i = 0; $i <= ($total_datos - 1); $i++) {
                        $array_fechas[$i]["Identificacion"] = $response->Identificacion[$i];
                        $array_fechas[$i]["FechaAsistencia"] = $response->Fecha_asistencia[$i];
                        $array_fechas[$i]["HoraInicial"] = $response->Hora_inicial[$i];
                        $array_fechas[$i]["HoraFinal"] = $response->Hora_final[$i];
                        $array_fechas[$i]["Sede"] = $response->Sede[$i];
                        $array_fechas[$i]["Observaciones"] = $response->Observaciones[$i];
                        $fecha_actual = substr($response->Fecha_asistencia[$i], 0, 4) . "-" . substr($response->Fecha_asistencia[$i], 4, 2) . "-" . substr($response->Fecha_asistencia[$i], 6, 2);
                        if ($Fecha == $fecha_actual) {
                            $DebeAsistir = "S" . "|" . $response->Hora_inicial[$i] . "|" . $response->Hora_final[$i];
                        }
                    }
                }
            }

            if (!empty($Fecha)) {
                $respuesta["message"] = "Fecha Verificada";
                $respuesta["success"] = true;
                $respuesta["response"] = $DebeAsistir;
            } else {
                $respuesta["message"] = "Fechas.";
                $respuesta["success"] = true;
                $respuesta["response"] = $array_fechas;
            }

        } else {
            $respuesta["message"] = "No se encontro la persona";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_diagnostico($IDClub, $IDSocio, $Fecha, $Hora, $Estado)
    {

        $dbo = &SIMDB::get();

        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);


        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Identificacion = $datos_socio["NumeroDocumento"];

        /*
        $Identificacion=1073599456;
        $Fecha="20201121";
        $Hora="10:30";
        $Estado="N";
         */

        if (!empty($Identificacion)) {
            $params = array(
                "Fecha_diagnostico" => $Fecha,
                "Hora" => $Hora,
                "Identificacion" => $Identificacion,
                "Estado_diagnostico" => $Estado,
            );

            $client  = new SoapClient(HOST_CATOLICA_DATOS . "Registro_autodiag_app_paw?wsdl", [
                'stream_context' => $context
            ]);
            $response = $client->__soapCall("Registro_autodiag_app_OPR", array($params));
            if ($response == "SUCCESSFUL") {
                $respuesta["message"] = "registrado";
                $respuesta["success"] = true;
                $respuesta["response"] = $array_horario_semana;
            } else {
                $respuesta["message"] = "no se pudo insertar";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "No se encontro la persona";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function set_diagnosticov2($IDClub, $IDSocio, $Fecha, $Hora, $Estado, $Usuario)
    {

        $dbo = &SIMDB::get();

        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);


        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $Identificacion = $datos_socio["NumeroDocumento"];

        $Campos_enviados = $Fecha . "," . $Hora . "," . $Estado . "," . $Usuario;

        /*
        $Identificacion=1073599456;
        $Fecha="20201121";
        $Hora="10:30";
        $Estado="N";
         */

        if (!empty($Identificacion)) {
            $params = array(
                "Fecha_diagnostico" => $Fecha,
                "Hora" => $Hora,
                "Identificacion" => $Identificacion,
                "Estado_diagnostico" => $Estado,
                "Usuario_Registro" => $Usuario,
            );

            $client  = new SoapClient(HOST_CATOLICA_DATOS . "Registro_autodiag_app_paw_PRD?wsdl", [
                'stream_context' => $context
            ]);

            $response = $client->__soapCall("Registro_autodiag_app_OPR", array($params));
            if ($response == "SUCCESSFUL") {
                $respuesta["message"] = "registrado";
                $respuesta["success"] = true;
                $respuesta["response"] = $array_horario_semana;
                $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','WSCatolica','$Campos_enviados','correcto')");
            } else {
                $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','WSCatolica','$Campos_enviados','sin conexion')");
                $respuesta["message"] = "no se pudo insertar";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('" . $IDSocio . "','WSCatolica','$Campos_enviados','no se encontro persona')");
            $respuesta["message"] = "No se encontro la persona";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }

    public function enviacorreo($IDClub, $correo, $Mensaje)
    {
		$dbo = &SIMDB::get();

    $context = stream_context_create([
        'ssl' => [
            // set some SSL/TLS specific options
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);

        $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");

        $msg = "<br>Cordial Saludo,<br><br>
		<br><br>" . $Mensaje . "<br><br>

		Por favor no responda este correo, si desea dar una respuesta ingrese a nuestra app<br>
		Cordialmente<br><br>
		<b>Notificaciones " . $datos_club["Nombre"] . "</b>";

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
                $mail->AddAddress($correo_value);
            }
        }

        $mail->Subject = "Solicitud de Ayuda";
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
        $mail->FromName = $datos_club["RemitenteCorreo"];
        $mail->AddCustomHeader("List-Unsubscribe: <mailto:noreplay@miempresapp.com>,  <$url_baja>");
        $confirm = $mail->Send();
    }

    public function envia_notificaciones($IDClub, $IDSolicitudAyuda)
    {
        $dbo = &SIMDB::get();

        $Config = $dbo->fetchAll("ConfiguracionAyuda", "IDClub = $IDClub AND Publicar = 'S'", "array");
        $Solicitud = $dbo->fetchAll("SolicitudAyuda", "IDSolicitudAyuda = $IDSolicitudAyuda ", "array");
        $NombreSolicitante = $dbo->getFields("Socio", "Nombre","IDSocio = $Solicitud[IDSocio]") . " " . $dbo->getFields("Socio", "Apellido","IDSocio = $Solicitud[IDSocio]");

		$Mensaje = $Config[Mensaje] ."
					<br>
					Solicitante: ".$NombreSolicitante."
					<br>
					Horario: ".$Solicitud[Horario]."
					<br>
					Salon: ".$Solicitud[Salon]."
					<br>
					Clase: ".$Solicitud[Clase]."
					<br>
					Comentarios: ".$Solicitud[Comentario];

		$MensajeNotif = $Config[Mensaje] ."
                    Solicitante: ".$NombreSolicitante."
					Horario: ".$Solicitud[Horario]."
					Salon: ".$Solicitud[Salon]."
					Clase: ".$Solicitud[Clase]."
					Comentarios: ".$Solicitud[Comentario];

        // ENVIAR CORREOS

        if (!empty($Config[Correos])) {
			SIMWebServiceCatolica::enviacorreo($IDClub, $Config[Correos], $Mensaje);
            $respuesta = "Correos Enviados - ";
        } else {
            $respuesta = "No hay correos configurados - ";
        }

		$IDSocio = array();

        $array_usuarios = explode("|||", $Config[UsuariosPush]);
        foreach ($array_usuarios as $usuario => $datos):
            if (!empty($datos)) {

                $array_datos_invitados = explode("-", $datos);
                $IDSocio[] = $array_datos_invitados[1];

            }
        endforeach;

		$Socios = implode(",",$IDSocio);
		SIMUtil::enviar_notificacion_push_general($IDClub,$Socios,$MensajeNotif);

		$respuesta .= "Notificaciones enviadas";

		return $respuesta;

    }


    public function sincroniza_socio($NumeroDocumento,$IDSocio,$IDClub)
    {
        $dbo = &SIMDB::get();

        $context = stream_context_create([
            'ssl' => [
                // set some SSL/TLS specific options
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);


        $respuesta = array();
        //consulto los datos de la persona
        $params = array(
            "Identificacion" => $NumeroDocumento,
        );

        $client  = new SoapClient(HOST_CATOLICA_DATOS . "Activos_Inactivos_app?wsdl", [
            'stream_context' => $context
        ]);
        $response = $client->__soapCall("Activos_Inactivos_app_OPR", array($params));

        $Estado = $response->Estado;
        if($Estado=="Activo")
          $IDEstadoSocio=1;
        else
          $IDEstadoSocio=2;

          $sql_actualiza="UPDATE Socio SET IDEstadoSocio = '".$IDEstadoSocio."' WHERE IDSocio = '".$IDSocio."' ";
          //echo "<br>" . $sql_actualiza;
          //$dbo->query($sql_actualiza);
    }




public function sincroniza_socio_lote($IDClub){

  $dbo = &SIMDB::get();

  $context = stream_context_create([
      'ssl' => [
          // set some SSL/TLS specific options
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
      ]
  ]);

  $sql_socios="SELECT IDSocio,NumeroDocumento FROM Socio WHERE IDClub = '".$IDClub."'";
  $r_socios=$dbo->query($sql_socios);
  while($row_socios=$dbo->fetchArray($r_socios)){
    $array_socios[$row_socios["NumeroDocumento"]]=$row_socios["IDSocio"];
  }

  $client  = new SoapClient(HOST_CATOLICA_DATOS . "Activos_Bach_app?wsdl", [
      'stream_context' => $context
  ]);

  $response = $client->__soapCall("Activos_Bach_app_OPR", array());
  $datos = $response->Identificacion;

  
  if(count($datos)>0){
    $sql_inactivar = "UPDATE Socio SET IDEstadoSocio = 2 Where IDClub  = $IDClub ";
    $dbo->query($sql_inactivar);
    for ($i = 0; $i < count($datos); $i++) {
        $IDSocio=$array_socios[trim($datos[$i])];
        if((int)$IDSocio>0){
          $sql_activar = "UPDATE Socio SET IDEstadoSocio = 1 Where IDSocio = $IDSocio ";
          $dbo->query($sql_activar);
        }
    }
  }
  echo "Finalizado";
}

} //end class
