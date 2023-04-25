<?php
class SIMWebServicePerfilesInfinito
{
    public function get_configuracion_registros_dinamicos($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();


        $sql = "SELECT LabelBotonCrearRegistro,LabelTituloCategorias,LabelBotonConfirmarRegistro FROM ConfiguracionRegistrosDinamicos  WHERE IDClub = '" . $IDClub . "'  AND Activo='S'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($DatosConfiguracion = $dbo->fetchArray($qry)) {
                $configuracion["LabelBotonCrearRegistro"] = $DatosConfiguracion["LabelBotonCrearRegistro"];
                $configuracion["LabelTituloCategorias"] = $DatosConfiguracion["LabelTituloCategorias"];
                $configuracion["LabelBottonConfirmarRegistro"] = $DatosConfiguracion["LabelBotonConfirmarRegistro"];
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracion;
        } //End if
        else {
            $respuesta["message"] = "Configuracion no esta activa";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }


    public function get_tipos_formulario_registros_dinamicos($IDClub, $IDSocio, $IDUsuario)
    {

        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT IDTipoFormulario,Nombre,Icono FROM TipoFormulario  WHERE IDClub = '" . $IDClub . "'  AND Activo='S'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($DatosTipoFormulario = $dbo->fetchArray($qry)) {
                $TipoFormulario["IDTipoFormulario"] = $DatosTipoFormulario["IDTipoFormulario"];
                $TipoFormulario["Nombre"] = $DatosTipoFormulario["Nombre"];
                $TipoFormulario["Icono"] =  REGISTROSINFINITO_ROOT . $DatosTipoFormulario["Icono"];

                array_push($response, $TipoFormulario);
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No hay tipos de formularios.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_formulario_registros_dinamicos($IDClub, $IDSocio, $IDUsuario, $IDTipoFormulario)
    {
        $dbo = &SIMDB::get();


        $response["IDTipoFormulario"] = $IDTipoFormulario;
        $response["Nombre"] = $dbo->getFields("TipoFormulario", "Nombre", "IDTipoFormulario = '" . $IDTipoFormulario . "'");

        //Pregunta
        $pregunta = array();
        $response_pregunta = array();
        $sql_preguntas = "Select * From PreguntaRegistrosDinamicos Where IDTipoFormulario = '" . $IDTipoFormulario . "' and Publicar = 'S' Order by Orden";
        $qry_preguntas = $dbo->query($sql_preguntas);
        if ($dbo->rows($qry_preguntas) > 0) {
            $message = $dbo->rows($qry_preguntas) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($row_pregunta = $dbo->FetchArray($qry_preguntas)) :
                $pregunta["IDPregunta"] = $row_pregunta["IDPreguntaRegistrosDinamicos"];
                $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                $pregunta["Valores"] = $row_pregunta["Valores"];
                $pregunta["Orden"] = $row_pregunta["Orden"];

                $pregunta["ParametroEnvioPost"] = "";
                if ($row_pregunta["TipoCampo"] == "imagen" || $row_pregunta["TipoCampo"] == "firmadigital") {
                    $pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPreguntaRegistrosDinamicos"];
                }

                array_push($response_pregunta, $pregunta);
            endwhile;

            $response["CamposFormulario"] = $response_pregunta;


            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron preguntas.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_mis_registros_dinamicos($IDClub, $IDSocio, $IDUsuario, $IDTipoFormulario)
    {
        $dbo = &SIMDB::get();
        $response = array();

        $sql = "SELECT *,MIN(IDRegistrosDinamicos) FROM RegistrosDinamicos  WHERE IDSocio ='" . $IDSocio . "' AND IDTipoFormulario='" . $IDTipoFormulario . "' GROUP by FechaTrCr Order by FechaTrCr DESC ";
        //echo $sql;
        $qry = $dbo->query($sql);

        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($DatosRegistro = $dbo->fetchArray($qry)) {

                //ENCONTRAR EL SEGUNDO REGISTRO
                $MinimoIDRegistrosDinamicos = $DatosRegistro["IDRegistrosDinamicos"] + 1;
                $sql_encontrar_segundo_registro = "SELECT Valor FROM RegistrosDinamicos  WHERE IDSocio ='" . $IDSocio . "' AND IDTipoFormulario='" . $IDTipoFormulario . "' AND IDRegistrosDinamicos='" . $MinimoIDRegistrosDinamicos . "' GROUP by FechaTrCr Order by FechaTrCr DESC ";
                $qry_segundoRegistro = $dbo->query($sql_encontrar_segundo_registro);
                $DatosRegistroDos = $dbo->fetchArray($qry_segundoRegistro);

                // IDRegistrosDinamicos es la respuesta IDRegistrosDinamicos y IDTipoFormulario la categoria y la fechatrcr
                $Registro["IDRegistroDinamico"] = $DatosRegistro["IDRegistrosDinamicos"] . "|" . $DatosRegistro["IDTipoFormulario"] . "|" . $DatosRegistro["FechaTrCr"];
                $Registro["Nombre"] = $DatosRegistro["Valor"] . "-" . $DatosRegistroDos["Valor"];
                $Registro["TextoLista"] = $dbo->getFields("TipoFormulario", "Nombre", "IDTipoFormulario = '" . $DatosRegistro["IDTipoFormulario"] . "'");
                $Registro["IDTipoFormulario"] = $DatosRegistro["IDTipoFormulario"];

                array_push($response, $Registro);
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No hay Registros.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else
        return $respuesta;
    }

    public function get_detalle_registro_dinamico($IDClub, $IDSocio, $IDUsuario, $IDRegistroDinamico)
    {
        $dbo = &SIMDB::get();

        // IDRegistrosDinamico es  IDRegistrosDinamicos|IDTipoFormulario|FechaTrCr
        $ID = explode("|", $IDRegistroDinamico);
        $IDRegistrosDinamicos = $ID[0];
        $IDTipoFormulario = $ID[1];
        $FechaTrCr = $ID[2];


        $response["IDRegistroDinamico"] = $IDRegistroDinamico;
        $response["Nombre"] = $dbo->getFields("RegistrosDinamicos", "Valor", "IDRegistrosDinamicos = '" . $IDRegistrosDinamicos . "'");
        $response["TextoLista"] = $dbo->getFields("TipoFormulario", "Nombre", "IDTipoFormulario = '" . $IDTipoFormulario . "'");


        //Pregunta
        $pregunta = array();
        $response_pregunta = array();
        $sql_preguntas = "SELECT P.EtiquetaCampo,P.IDPreguntaRegistrosDinamicos,P.TipoCampo,P.Obligatorio,P.Valores,P.Orden,RD.IDTipoFormulario, RD.IDRegistrosDinamicos,RD.FechaTrCr, RD.Valor FROM RegistrosDinamicos RD, PreguntaRegistrosDinamicos P WHERE RD.IDPreguntaRegistrosDinamicos = P.IDPreguntaRegistrosDinamicos AND IDSocio = '$IDSocio' AND RD.IDTipoFormulario = '$IDTipoFormulario' AND RD.FechaTrCr='$FechaTrCr' ORDER BY P.Orden ASC";
        //echo $sql_preguntas;
        $qry_preguntas = $dbo->query($sql_preguntas);
        if ($dbo->rows($qry_preguntas) > 0) {
            $message = $dbo->rows($qry_preguntas) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($row_pregunta = $dbo->FetchArray($qry_preguntas)) :
                $preguntaRespuesta["IDPregunta"] = $row_pregunta["IDPreguntaRegistrosDinamicos"];
                $preguntaRespuesta["TipoCampo"] = $row_pregunta["TipoCampo"];
                $preguntaRespuesta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                $preguntaRespuesta["Obligatorio"] = $row_pregunta["Obligatorio"];
                $preguntaRespuesta["Valores"] = $row_pregunta["Valores"];
                $preguntaRespuesta["Orden"] = $row_pregunta["Orden"];

                if ($row_pregunta["TipoCampo"] == "imagen" || $row_pregunta["TipoCampo"] == "firmadigital") {
                    if (!empty($row_pregunta["Valor"])) {
                        $preguntaRespuesta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPreguntaRegistrosDinamicos"];
                        $preguntaRespuesta["RespuestaPrevia"] =  REGISTROSINFINITO_ROOT . $row_pregunta["Valor"];
                    } else {
                        $preguntaRespuesta["RespuestaPrevia"] = "";
                    }
                } else {
                    $preguntaRespuesta["RespuestaPrevia"] = $row_pregunta["Valor"];
                }




                array_push($response_pregunta, $preguntaRespuesta);
            endwhile;

            $response["CamposFormulario"] = $response_pregunta;


            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = "No se encontraron respuestas.";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function set_registro_dinamico($IDClub, $IDSocio, $IDUsuario, $IDTipoFormulario, $CamposFormulario, $Archivo, $File = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDTipoFormulario)) {


            $sql_pregunta = "SELECT IDPreguntaRegistrosDinamicos, Obligatorio,TipoCampo FROM PreguntaRegistrosDinamicos WHERE IDTipoFormulario = '" . $IDTipoFormulario . "' ";
            $r_pregunta = $dbo->query($sql_pregunta);
            while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
                $array_pregunta[$row_pregunta["IDCampoRegistroDinamico"]] = $row_pregunta["Obligatorio"];
                $array_preguntaImage[$row_pregunta["IDCampoRegistroDinamico"]] = $row_pregunta["TipoCampo"];
            }

            $datos_correctos = "S";

            $CamposFormulario = trim(preg_replace('/\s+/', ' ', $CamposFormulario));
            $datos_respuesta = json_decode($CamposFormulario, true);
            if (count($datos_respuesta) > 0) :
                foreach ($datos_respuesta as $detalle_respuesta) {
                    if (($detalle_respuesta["Valor"] == "null" || $detalle_respuesta["Valor"] == "") && $array_pregunta[$detalle_respuesta["IDCampoRegistroDinamico"]] == "S" && $array_preguntaImage[$detalle_respuesta["IDCampoRegistroDinamico"]] != "imagen") {
                        $datos_correctos = "N";
                        $PreguntaValida = $detalle_respuesta["IDCampoRegistroDinamico"];
                        break;
                    } else {
                        $datos_correctos = "S";
                    }
                }
                if ($datos_correctos == "N") {
                    $respuesta["message"] = "Datos No fueron enviados, alguna de las respuestas es incorrecta, por favor verifique." . $PreguntaValida;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }



                foreach ($datos_respuesta as $detalle_respuesta) :
                    $NumeroDocumento = ($detalle_respuesta['IDCampoRegistroDinamico'] == 32) ? $detalle_respuesta['Valor'] : "";

                    $sql_datos_form = $dbo->query("Insert Into RegistrosDinamicos (IDTipoFormulario, IDSocio, IDPreguntaRegistrosDinamicos, Valor, FechaTrCr) Values ('" . $IDTipoFormulario . "','" . $IDSocio . "','" . $detalle_respuesta["IDCampoRegistroDinamico"] .  "','" . $detalle_respuesta["Valor"] . "',NOW())");

                endforeach;

            endif;

            //subir las imagenes
            if (isset($File)) {
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
                        $files = SIMFile::upload($File[$nombre_archivo], REGISTROSINFINITO_DIR, "IMAGE");
                        if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
                            $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                        $Archivo = $files[0]["innername"];
                        $actualiza_pregunta = "UPDATE RegistrosDinamicos SET Valor = '" . $Archivo . "' WHERE IDPreguntaRegistrosDinamicos ='" . $IDPreguntaActualiza . "' and IDTipoFormulario  = '" . $IDTipoFormulario . "' and IDSocio = '" . $IDSocio . "' ORDER BY FechaTrCr DESC LIMIT 1";
                        $dbo->query($actualiza_pregunta);
                        //$nombrefoto.=    $actualiza_pregunta;


                    }
                }
            }
            $alto_barras = "";
            foreach ($datos_respuesta as $detalle_respuesta) {

                switch ($detalle_respuesta['IDCampoRegistroDinamico']) {
                        //arboretto    
                        // Residentes
                    case '32':
                        $NumeroDocumento = $detalle_respuesta['Valor'];
                        break;
                    case '33':
                        $NombreSocio = $detalle_respuesta['Valor'];
                        break;
                    case '34':
                        $ApellidosSocio = $detalle_respuesta['Valor'];
                        break;
                    case '35':
                        $Correo = $detalle_respuesta['Valor'];
                        break;
                    case '37':
                        $NumeroDeCasa = $detalle_respuesta['Valor'];
                        break;
                    case '99':
                        break;
                        $NombreDeCasa = $detalle_respuesta['Valor'];
                    case '103':
                        $Parentezco = $detalle_respuesta['Valor'];
                        break;
                        // Vehiculos
                    case '40':
                        $Marca = $detalle_respuesta['Valor'];
                        break;
                    case '41':
                        $NumeroMatricula = $detalle_respuesta['Valor'];
                        break;
                    case '42':
                        $Color = $detalle_respuesta['Valor'];
                        break;
                        // Empleado
                    case '43':
                        $NombreEmpleado = $detalle_respuesta['Valor'];
                        break;
                    case '44':
                        $EmailEmpleado = $detalle_respuesta['Valor'];
                        break;
                    case '45':
                        $NumeroDocumentoEmpleado = $detalle_respuesta['Valor'];
                        break;
                    case '46':
                        $ApellidoEmpleado = $detalle_respuesta['Valor'];
                        break;
                    case '48':
                        $FechaEmpleado = $detalle_respuesta['Valor'];
                        break;
                    case '49':
                        $HoraEntrada = $detalle_respuesta['Valor'];
                        break;
                    case '50':
                        $HoraSalida = $detalle_respuesta['Valor'];
                        break;
                    case '100':
                        $TelefonoEmpleado = $detalle_respuesta['Valor'];
                        break;
                    case '101':
                        $NombreDeCasaEmpleado = $detalle_respuesta['Valor'];
                        break;
                    case '102':
                        $NumeroDeCasaEmpleado = $detalle_respuesta['Valor'];
                        break;
                        //FIn arboretto
                }
            }
            $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            $Nombre = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            $Apellido = $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
            $IDSocioSistemaExterno = $dbo->getFields("Socio", "IDSocioSistemaExterno", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");


            // Envio de Datos a Arboretto
            //4: arboretto Residentes,5: arboretto Contacto emergencia,4: arboretto Empleados,4: arboretto Vehiculos
            // if ($IDTipoFormulario == 4 || $IDTipoFormulario == 5 || $IDTipoFormulario == 6 || $IDTipoFormulario == 7) {
            if ($IDTipoFormulario == 4) {
                //Verifico que el beneficiario no exista
                $sql_socio = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' LIMIT 1";

                $r_socio = $dbo->query($sql_socio);
                $row_socio = $dbo->fetchArray($r_socio);
                if ((int) $row_socio["IDSocio"] <= 0) {

                    $sql_crea_socio = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, PermiteReservar,TipoSocio,NumeroInvitados,NumeroAccesos)VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreSocio . "','" . $ApellidosSocio . "','" . $NumeroDocumento . "','" . $NumeroDocumento . "',sha1('" . $NumeroDocumento . "'), '" . $Correo . "', NOW(),'Perfiles infinitos','S','Beneficiario','3','3')";

                    $dbo->query($sql_crea_socio);
                    //genero codigo de barras
                    $IDSocio = $dbo->lastID("IDSocio");
                    $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($NumeroDocumento, $IDSocio, $alto_barras);
                    $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                    //genero codigo qr
                    $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $NumeroDocumento);
                    $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");

                    // Envio datos a Arboretto
                    if ($IDClub == 223) {
                        // if ($IDClub == 8) {
                        if ($IDTipoFormulario == 4) {
                            include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                            $respuesta_arboretto = SIMWebServiceArboretto::crear_persona($IDClub, $IDSocio, "", "Residente");
                            if ($respuesta_arboretto['msg'] == "Success") {
                                $dbo->query("update Socio set IDSocioSistemaExterno = '" . $respuesta_arboretto['data'] . "' where IDSocio = $IDSocio");
                            }
                        }
                    }
                } else {
                    $actualiza_socio = "UPDATE Socio SET Nombre='" . $NombreSocio . "', FechaNacimiento = '" . $FechaNacimiento . "', Apellido= '" . $ApellidosSocio . "', NumeroDocumento = '" . $NumeroDocumento . "', Email='" . $NumeroDocumento . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='Beneficiario',NumeroInvitados='3',NumeroAccesos='3',CorreoElectronico='$Correo', UsuarioTrEd = 'Perfiles Infinitos', FechaTrEd = NOW() WHERE IDSocio = '" . $row_socio["IDSocio"] . "'";
                    $dbo->query($actualiza_socio);
                    // Envio datos a Arboretto
                    if ($IDClub == 223) {
                        // if ($IDClub == 8) {
                        if ($IDTipoFormulario == 4) {
                            include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                            $respuesta_arboretto = SIMWebServiceArboretto::editar_persona($IDClub, $row_socio["IDSocio"], "", "Residente");
                            if ($respuesta_arboretto['msg'] == "Success") {
                                if ($respuesta_arboretto['data'] > 0) {
                                    $dbo->query("update Socio set IDSocioSistemaExterno = '" . $respuesta_arboretto['data'] . "' where IDSocio = {$row_socio['IDSocio']}");
                                }
                            }
                        }
                    }
                }
            }
            //4: arboretto Residentes,5: arboretto Contacto emergencia,6: arboretto Empleados,7: arboretto Vehiculos
            // Envio datos a Arboretto
            if ($IDClub == 223) {
                // if ($IDClub == 8) {
                switch ($Color) {
                    case 'otro color':
                        $IDColor = 0;
                        break;
                    case 'blanco':
                        $IDColor = 1;
                        break;
                    case 'astilla':
                        $IDColor = 2;
                        break;
                    case 'gris':
                        $IDColor = 3;
                        break;
                    case 'negro':
                        $IDColor = 4;
                        break;
                    case 'rojo':
                        $IDColor = 5;
                        break;
                    case 'azul oscuro':
                        $IDColor = 6;
                        break;
                    case 'azul':
                        $IDColor = 7;
                        break;
                    case 'amarillo':
                        $IDColor = 8;
                        break;
                    case 'verde':
                        $IDColor = 9;
                        break;
                    case 'marron':
                        $IDColor = 10;
                        break;
                    case 'rosa':
                        $IDColor = 11;
                        break;
                    case 'purpura':
                        $IDColor = 12;
                        break;
                    case 'cian':
                        $IDColor = 14;
                        break;

                    default:
                        $IDColor = 0;
                        break;
                }
                if ($IDTipoFormulario == 7) {
                    include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                    // Grupo de vehiculos -- vehicleGroupIndexCode
                    // 105:Arboretto
                    $JSon = '{
                        "plateNo": "' . $NumeroMatricula . '",
                        "personId": "' . $IDSocioSistemaExterno . '",
                        "phoneNo": "",
                        "vehicleColor": ' . $IDColor . ',
                        "vehicleGroupIndexCode": "105",
                        "personGivenName": "' . $accion_socio . '",
                        "personFamilyName": "' . $Nombre . ' ' . $Apellido . '",
                        "effectiveDate": "' . date('Y-m-d') . 'T' . date('H:i:s') . '+00:00",
                        "expiredDate": "2025-03-25T23:59:59+08:00"
                    }';

                    $respuesta_arboretto = SIMWebServiceArboretto::crear_vehiculo($IDClub, $row_socio["IDSocio"], "", $JSon);
                }
                if ($IDTipoFormulario == 6) {
                    include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                    // Grupo de vehiculos -- vehicleGroupIndexCode
                    // 105:Arboretto
                    $JSon = '{
                    "personCode": "' . $NumeroDocumentoEmpleado . '",
                    "personFamilyName": "' . $ApellidoEmpleado . '",
                    "personGivenName": "' . $NombreEmpleado . '",
                    "gender": 1,
                    "orgIndexCode": "33",
                    "remark": "",
                    "phoneNo": "' . $TelefonoEmpleado . '",
                    "email": "' . trim($EmailEmpleado) . '",
                    "beginTime": "' . $FechaEmpleado . 'T' . $HoraEntrada . '+00:00",
                    "endTime": "' . $FechaEmpleado . 'T' . $HoraSalida . '+00:00"
                }';

                    $respuesta_arboretto = SIMWebServiceArboretto::crear_persona_empleado($IDClub, $IDSocio, "", $JSon);
                }
            }

            $respuesta["message"] = "Registro guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }


    public function set_actualiza_registro_dinamico($IDClub, $IDSocio, $IDUsuario, $IDRegistroDinamico, $CamposFormulario, $Archivo, $File = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDRegistroDinamico)) {

            // IDRegistrosDinamico es  IDRegistrosDinamicos|IDTipoFormulario|FechaTrCr
            $ID = explode("|", $IDRegistroDinamico);
            $IDTipoFormulario = $ID[1];
            $FechaTrCr = $ID[2];


            $sql_pregunta = "SELECT IDPreguntaRegistrosDinamicos, Obligatorio,TipoCampo FROM PreguntaRegistrosDinamicos WHERE IDTipoFormulario = '" . $IDTipoFormulario . "' ";
            $r_pregunta = $dbo->query($sql_pregunta);
            while ($row_pregunta = $dbo->fetchArray($r_pregunta)) {
                $array_pregunta[$row_pregunta["IDCampoRegistroDinamico"]] = $row_pregunta["Obligatorio"];
                $array_preguntaImage[$row_pregunta["IDCampoRegistroDinamico"]] = $row_pregunta["TipoCampo"];
            }

            $datos_correctos = "S";

            $CamposFormulario = trim(preg_replace('/\s+/', ' ', $CamposFormulario));
            $datos_respuesta = json_decode($CamposFormulario, true);
            if (count($datos_respuesta) > 0) :
                foreach ($datos_respuesta as $detalle_respuesta) {
                    if (($detalle_respuesta["Valor"] == "null" || $detalle_respuesta["Valor"] == "") && $array_pregunta[$detalle_respuesta["IDCampoRegistroDinamico"]] == "S" && $array_preguntaImage[$detalle_respuesta["IDCampoRegistroDinamico"]] != "imagen") {
                        $datos_correctos = "N";
                        $PreguntaValida = $detalle_respuesta["IDCampoRegistroDinamico"];
                        break;
                    } else {
                        $datos_correctos = "S";
                    }
                }
                if ($datos_correctos == "N") {
                    $respuesta["message"] = "Datos No fueron enviados, alguna de las respuestas es incorrecta, por favor verifique." . $PreguntaValida;
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }



                foreach ($datos_respuesta as $detalle_respuesta) :
                    $IDPregunta = $detalle_respuesta["IDCampoRegistroDinamico"];
                    $valor = $detalle_respuesta["Valor"];
                    $sql_verifica = "SELECT P.IDPreguntaRegistrosDinamicos,P.TipoCampo,RD.IDTipoFormulario, RD.IDRegistrosDinamicos  FROM RegistrosDinamicos RD, PreguntaRegistrosDinamicos P WHERE RD.IDPreguntaRegistrosDinamicos = P.IDPreguntaRegistrosDinamicos AND IDSocio = '$IDSocio' AND RD.IDTipoFormulario = '$IDTipoFormulario' AND RD.FechaTrCr='$FechaTrCr' AND RD.IDPreguntaRegistrosDinamicos='$IDPregunta'";
                    // $sql_verifica = "SELECT SCES.IDSocioCampoEditarSocio,CES.Tipo FROM SocioCampoEditarSocio SCES,CampoEditarSocio CES   WHERE IDSocio = '" . $IDSocio . "' and SCES.IDCampoEditarSocio = '" . $IDCampo . "' AND SCES.IDCampoEditarSocio=CES.IDCampoEditarSocio";

                    $r_verifica = $dbo->query($sql_verifica);
                    $verifica = $dbo->fetchArray($r_verifica);
                    $verificarcampos = $verifica;

                    if ($verificarcampos["TipoCampo"] != "imagen" && $verificarcampos["TipoCampo"] != "imagenarchivo" && $verificarcampos["TipoCampo"] != "firmadigital") {

                        //echo $sql_verifica;
                        if ($dbo->rows($r_verifica) > 0) {



                            $sql_socio_datos = "UPDATE RegistrosDinamicos 
                            SET  Valor='" . $valor . "'
                            WHERE IDRegistrosDinamicos = '" . $verificarcampos["IDRegistrosDinamicos"] . "'";

                            // echo "UPDATE" . $sql_socio_datos;
                        } else {
                            $sql_socio_datos = "Insert Into RegistrosDinamicos (IDTipoFormulario, IDSocio, IDPreguntaRegistrosDinamicos, Valor, FechaTrCr) Values ('" . $ID[1] . "','" . $IDSocio . "','" . $detalle_respuesta["IDCampoRegistroDinamico"] .  "','" . $detalle_respuesta["Valor"] . "','$FechaTrCr')";
                        }
                    }

                    $dbo->query($sql_socio_datos);
                endforeach;




                $alto_barras = "";
                foreach ($datos_respuesta as $detalle_respuesta) {

                    switch ($detalle_respuesta['IDCampoRegistroDinamico']) {
                            //arboretto    
                            // Residentes
                        case '32':
                            $NumeroDocumento = $detalle_respuesta['Valor'];
                            break;
                        case '33':
                            $NombreSocio = $detalle_respuesta['Valor'];
                            break;
                        case '34':
                            $ApellidosSocio = $detalle_respuesta['Valor'];
                            break;
                        case '35':
                            $Correo = $detalle_respuesta['Valor'];
                            break;
                        case '37':
                            $NumeroDeCasa = $detalle_respuesta['Valor'];
                            break;
                        case '99':
                            break;
                            $NombreDeCasa = $detalle_respuesta['Valor'];
                        case '103':
                            $Parentezco = $detalle_respuesta['Valor'];
                            break;
                            // Vehiculos
                        case '40':
                            $Marca = $detalle_respuesta['Valor'];
                            break;
                        case '41':
                            $NumeroMatricula = $detalle_respuesta['Valor'];
                            break;
                        case '42':
                            $Color = $detalle_respuesta['Valor'];
                            break;
                            // Empleado
                        case '43':
                            $NombreEmpleado = $detalle_respuesta['Valor'];
                            break;
                        case '44':
                            $EmailEmpleado = $detalle_respuesta['Valor'];
                            break;
                        case '45':
                            $NumeroDocumentoEmpleado = $detalle_respuesta['Valor'];
                            break;
                        case '46':
                            $ApellidoEmpleado = $detalle_respuesta['Valor'];
                            break;
                        case '48':
                            $FechaEmpleado = $detalle_respuesta['Valor'];
                            break;
                        case '49':
                            $HoraEntrada = $detalle_respuesta['Valor'];
                            break;
                        case '50':
                            $HoraSalida = $detalle_respuesta['Valor'];
                            break;
                        case '100':
                            $TelefonoEmpleado = $detalle_respuesta['Valor'];
                            break;
                        case '101':
                            $NombreDeCasaEmpleado = $detalle_respuesta['Valor'];
                            break;
                        case '102':
                            $NumeroDeCasaEmpleado = $detalle_respuesta['Valor'];
                            break;
                            //FIn arboretto
                    }
                }
                $accion_socio = $dbo->getFields("Socio", "Accion", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                $Nombre = $dbo->getFields("Socio", "Nombre", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                $Apellido = $dbo->getFields("Socio", "Apellido", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");
                $IDSocioSistemaExterno = $dbo->getFields("Socio", "IDSocioSistemaExterno", "IDSocio = '" . $IDSocio . "' and IDClub = '" . $IDClub . "'");


                // Envio de Datos a Arboretto
                //4: arboretto Residentes,5: arboretto Contacto emergencia,4: arboretto Empleados,4: arboretto Vehiculos
                // if ($IDTipoFormulario == 4 || $IDTipoFormulario == 5 || $IDTipoFormulario == 6 || $IDTipoFormulario == 7) {
                if ($IDTipoFormulario == 4) {
                    //Verifico que el beneficiario no exista
                    $sql_socio = "SELECT IDSocio FROM Socio WHERE NumeroDocumento = '" . $NumeroDocumento . "' and IDClub = '" . $IDClub . "' LIMIT 1";

                    $r_socio = $dbo->query($sql_socio);
                    $row_socio = $dbo->fetchArray($r_socio);
                    if ((int) $row_socio["IDSocio"] <= 0) {

                        $sql_crea_socio = "INSERT INTO Socio (IDClub,IDEstadoSocio, Accion, AccionPadre, NumeroDerecho, Nombre, Apellido, NumeroDocumento, Email, Clave, CorreoElectronico, FechaTrCr, UsuarioTrCr, PermiteReservar,TipoSocio,NumeroInvitados,NumeroAccesos)VALUES ('" . $IDClub . "',1,'" . $accion_socio . "','" . $accion_socio . "','" . $accion_socio . "','" . $NombreSocio . "','" . $ApellidosSocio . "','" . $NumeroDocumento . "','" . $NumeroDocumento . "',sha1('" . $NumeroDocumento . "'), '" . $Correo . "', NOW(),'Perfiles infinitos','S','Beneficiario','3','3')";

                        $dbo->query($sql_crea_socio);
                        //genero codigo de barras
                        $IDSocio = $dbo->lastID("IDSocio");
                        $frm["CodigoBarras"] = SIMUtil::generar_codigo_barras($NumeroDocumento, $IDSocio, $alto_barras);
                        $update_codigo = $dbo->query("update Socio set CodigoBarras = '" . $frm["CodigoBarras"] . "' Where IDSocio = '" . $IDSocio . "'");

                        //genero codigo qr
                        $row_socio["CodigoQR"] = SIMUtil::generar_carne_qr($IDSocio, $NumeroDocumento);
                        $update_codigo = $dbo->query("update Socio set CodigoQR = '" . $row_socio["CodigoQR"] . "' Where IDSocio = '" . $IDSocio . "'");

                        // Envio datos a Arboretto
                        if ($IDClub == 223) {
                            // if ($IDClub == 8) {
                            if ($IDTipoFormulario == 4) {
                                include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                                $respuesta_arboretto = SIMWebServiceArboretto::crear_persona($IDClub, $IDSocio, "", "Residente");
                                if ($respuesta_arboretto['msg'] == "Success") {
                                    $dbo->query("update Socio set IDSocioSistemaExterno = '" . $respuesta_arboretto['data'] . "' where IDSocio = $IDSocio");
                                }
                            }
                        }
                    } else {
                        $actualiza_socio = "UPDATE Socio SET Nombre='" . $NombreSocio . "', FechaNacimiento = '" . $FechaNacimiento . "', Apellido= '" . $ApellidosSocio . "', NumeroDocumento = '" . $NumeroDocumento . "', Email='" . $NumeroDocumento . "', Accion='" . $accion_socio . "', AccionPadre='" . $accion_socio . "', NumeroDerecho='" . $accion_socio . "',TipoSocio='Beneficiario',NumeroInvitados='3',NumeroAccesos='3',CorreoElectronico='$Correo', UsuarioTrEd = 'Perfiles Infinitos', FechaTrEd = NOW() WHERE IDSocio = '" . $row_socio["IDSocio"] . "'";
                        $dbo->query($actualiza_socio);

                        // Envio datos a Arboretto
                        if ($IDClub == 223) {
                            // if ($IDClub == 8) {
                            if ($IDTipoFormulario == 4) {
                                include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                                $respuesta_arboretto = SIMWebServiceArboretto::editar_persona($IDClub, $row_socio["IDSocio"], "", "Residente");
                                if ($respuesta_arboretto['msg'] == "Success") {
                                    if ($respuesta_arboretto['data'] > 0) {
                                        $dbo->query("update Socio set IDSocioSistemaExterno = '" . $respuesta_arboretto['data'] . "' where IDSocio = {$row_socio['IDSocio']}");
                                    }
                                }
                            }
                        }
                    }
                }

                //4: arboretto Residentes,5: arboretto Contacto emergencia,6: arboretto Empleados,7: arboretto Vehiculos
                // Envio datos a Arboretto
                if ($IDClub == 223) {
                    // if ($IDClub == 8) {
                    switch ($Color) {
                        case 'otro color':
                            $IDColor = 0;
                            break;
                        case 'blanco':
                            $IDColor = 1;
                            break;
                        case 'astilla':
                            $IDColor = 2;
                            break;
                        case 'gris':
                            $IDColor = 3;
                            break;
                        case 'negro':
                            $IDColor = 4;
                            break;
                        case 'rojo':
                            $IDColor = 5;
                            break;
                        case 'azul oscuro':
                            $IDColor = 6;
                            break;
                        case 'azul':
                            $IDColor = 7;
                            break;
                        case 'amarillo':
                            $IDColor = 8;
                            break;
                        case 'verde':
                            $IDColor = 9;
                            break;
                        case 'marron':
                            $IDColor = 10;
                            break;
                        case 'rosa':
                            $IDColor = 11;
                            break;
                        case 'purpura':
                            $IDColor = 12;
                            break;
                        case 'cian':
                            $IDColor = 14;
                            break;

                        default:
                            $IDColor = 0;
                            break;
                    }
                    if ($IDTipoFormulario == 7) {

                        include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                        // Grupo de vehiculos -- vehicleGroupIndexCode
                        // 105:Arboretto

                        // Buscamos el vehiculo para eliminarlo
                        $JSon = '{
                            "pageNo": 1,
                            "pageSize": 10,
                            "personName": "",
                            "plateNo": "' . $NumeroMatricula . '",
                            "phoneNo": "",
                            "vehicleGroupIndexCode": "105"
                        }';
                        $respuesta_arboretto = SIMWebServiceArboretto::listar_vehiculo($IDClub, $row_socio["IDSocio"], "", $JSon);

                        $respuesta_arboretto = json_decode($respuesta_arboretto, true);

                        if ($respuesta_arboretto['msg'] == "Success") {
                            $vehiculoId = $respuesta_arboretto['data']['list'][0]['vehicleId'];

                            $JSon = '{
                                    "vehicleId": "' . $vehiculoId . '",
                                    "plateNo": "' . $NumeroMatricula . '",
                                    "personId": "' . $IDSocioSistemaExterno . '",
                                    "phoneNo": "",
                                    "vehicleColor": ' . $IDColor . ',
                                    "vehicleGroupIndexCode": "105",
                                    "personGivenName": "' . $accion_socio . '",
                                    "personFamilyName": "' . $Nombre . ' ' . $Apellido . '",
                                    "effectiveDate": "' . date('Y-m-d') . 'T' . date('H:i:s') . '+00:00",
                                    "expiredDate": "2025-03-25T23:59:59+08:00"
                                }';

                            $respuesta_arboretto = SIMWebServiceArboretto::editar_vehiculo($IDClub, $row_socio["IDSocio"], "", $JSon);
                        }
                    }
                    if ($IDTipoFormulario == 6) {
                        include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                        // Grupo de vehiculos -- vehicleGroupIndexCode
                        // 105:Arboretto
                        $JSon = '{
                            "personCode": "' . $NumeroDocumentoEmpleado . '",
                            "personFamilyName": "' . $ApellidoEmpleado . '",
                            "personGivenName": "' . $NombreEmpleado . '",
                            "gender": 1,
                            "orgIndexCode": "33",
                            "remark": "",
                            "phoneNo": "' . $TelefonoEmpleado . '",
                            "email": "' . trim($EmailEmpleado) . '",
                            "beginTime": "' . $FechaEmpleado . 'T' . $HoraEntrada . '+00:00",
                            "endTime": "' . $FechaEmpleado . 'T' . $HoraSalida . '+00:00"
                            }';
                    }
                }

            endif;

            //subir las imagenes
            if (isset($File)) {
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
                        $files = SIMFile::upload($File[$nombre_archivo], REGISTROSINFINITO_DIR, "IMAGE");
                        if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
                            $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                        $Archivo = $files[0]["innername"];
                        $actualiza_pregunta = "UPDATE RegistrosDinamicos SET Valor = '" . $Archivo . "' WHERE IDPreguntaRegistrosDinamicos ='" . $IDPreguntaActualiza . "' and IDTipoFormulario  = '" . $IDTipoFormulario . "' and IDSocio = '" . $IDSocio . "' AND FechaTrCr='" . $FechaTrCr . "'  ORDER BY FechaTrCr DESC LIMIT 1";
                        $dbo->query($actualiza_pregunta);
                        //$nombrefoto.=    $actualiza_pregunta;


                    }
                }
            }


            $respuesta["message"] = "Registro actualizado";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function eliminar_registro_dinamico($IDClub, $IDSocio, $IDUsuario, $IDRegistroDinamico)
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDRegistroDinamico)) {

            // IDRegistrosDinamico es  IDRegistrosDinamicos|IDTipoFormulario|FechaTrCr
            $ID = explode("|", $IDRegistroDinamico);
            $IDTipoFormulario = $ID[1];
            $FechaTrCr = $ID[2];

            if ($IDClub == 223) {
                // Envio de Datos a Arboretto
                //4: arboretto Residentes,5: arboretto Contacto emergencia,4: arboretto Empleados,4: arboretto Vehiculos
                if ($IDTipoFormulario == 4) {
                    // Pregunta NumeroDocumento
                    $IDCampoRegistroDinamico = 32;

                    $sql_datos_residente = "SELECT Valor as NumeroDocumento FROM RegistrosDinamicos WHERE IDTipoFormulario = $IDTipoFormulario AND IDSocio = $IDSocio AND IDPreguntaRegistrosDinamicos  = $IDCampoRegistroDinamico";

                    $q_NumeroDocumento =  $dbo->query($sql_datos_residente);
                    if ($dbo->rows($q_NumeroDocumento) > 0) {
                        $arr_NumeroDocumento = $dbo->assoc($q_NumeroDocumento);
                        $NumeroDocumento = $arr_NumeroDocumento['NumeroDocumento'];
                        //Verifico que el beneficiario no exista
                        $DatosSocioResidente = $dbo->fetchAll("Socio", "NumeroDocumento = '" . $NumeroDocumento . "' AND IDClub = '" . $IDClub . "' LIMIT 1", "array");
                        if ($DatosSocioResidente['IDSocio'] > 0) {
                            $dbo->query("update Socio set IDEstadoSocio='2' where IDClub = $IDClub and  IDSocio = " . $DatosSocioResidente['IDSocio']);
                        }

                        if ($DatosSocioResidente['IDSocioSistemaExterno'] > 0) {
                            $JSon = '{
                                "personId": "' . $DatosSocioResidente['IDSocioSistemaExterno'] . '",
                                "personCode": "' . $DatosSocioResidente['NumeroDocumento'] . '",
                                "personFamilyName": "' . $DatosSocioResidente['Apellido'] . '",
                                "personGivenName": "' . $DatosSocioResidente['Nombre'] . '",
                                "gender": 1,
                                "orgIndexCode": "31",
                                "remark": "",
                                "phoneNo": "' . $DatosSocioResidente['Telefono'] . '",
                                "email": "' . trim($DatosSocioResidente['Email']) . '",
                                "beginTime": "' . date('Y-m-d') . 'T00:00:00+00:00",
                                "endTime": "' . date('Y-m-d') . 'T00:00:00+00:00"
                                }';
                            $respuesta_arboretto = SIMWebServiceArboretto::borrar_persona($IDClub, $IDSocio, "", $JSon);
                        }
                    }
                }

                if ($IDTipoFormulario == 7) {

                    include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                    // Grupo de vehiculos -- vehicleGroupIndexCode
                    // 105:Arboretto
                    // Pregunta NumeroDocumento
                    $IDCampoRegistroDinamico = 41;

                    $sql_datos_vehiculo = "SELECT Valor as NumeroMatricula FROM RegistrosDinamicos WHERE IDTipoFormulario = $IDTipoFormulario AND IDSocio = $IDSocio AND IDPreguntaRegistrosDinamicos  = $IDCampoRegistroDinamico";

                    $q_Numero =  $dbo->query($sql_datos_vehiculo);
                    if ($dbo->rows($q_Numero) > 0) {
                        $arr_Numero = $dbo->assoc($q_Numero);

                        $NumeroMatricula = $arr_Numero['NumeroMatricula'];

                        // Buscamos el vehiculo para eliminarlo
                        $JSon = '{
                            "pageNo": 1,
                            "pageSize": 10,
                            "personName": "",
                            "plateNo": "' . $NumeroMatricula . '",
                            "phoneNo": "",
                            "vehicleGroupIndexCode": "105"
                        }';

                        $respuesta_arboretto = SIMWebServiceArboretto::listar_vehiculo($IDClub, $row_socio["IDSocio"], "", $JSon);
                        $respuesta_arboretto = json_decode($respuesta_arboretto, true);

                        if ($respuesta_arboretto['msg'] == "Success") {
                            $vehiculoId = $respuesta_arboretto['data']['list'][0]['vehicleId'];

                            $JSon = '{"vehicleId": "' . $vehiculoId . '"}';

                            $respuesta_arboretto = SIMWebServiceArboretto::borrar_vehiculo($IDClub, $row_socio["IDSocio"], "", $JSon);
                        }
                    }
                }
                if ($IDTipoFormulario == 6) {

                    include_once(LIBDIR . 'SIMWebServiceArboretto.inc.php');
                    // Grupo de vehiculos -- vehicleGroupIndexCode
                    // 105:Arboretto
                    // Pregunta NumeroDocumento
                    $IDCampoRegistroDinamico = 43;

                    $sql_datos_empleado = "SELECT Valor as Nombre FROM RegistrosDinamicos WHERE IDTipoFormulario = $IDTipoFormulario AND IDSocio = $IDSocio AND IDPreguntaRegistrosDinamicos  = $IDCampoRegistroDinamico";

                    $q_Nombre =  $dbo->query($sql_datos_empleado);
                    if ($dbo->rows($q_Nombre) > 0) {
                        $arr_Nombre = $dbo->assoc($q_Nombre);

                        $Nombre = $arr_Nombre['Nombre'];

                        // Buscamos el vehiculo para eliminarlo
                        $JSon = '{
                            "pageNo": 1,
                            "pageSize": 1,
                            "personName": "' . $Nombre . '"
                            }';

                        $respuesta_arboretto = SIMWebServiceArboretto::listar_persona($IDClub, $row_socio["IDSocio"], "", $JSon);
                        $respuesta_arboretto = json_decode($respuesta_arboretto, true);

                        if ($respuesta_arboretto['msg'] == "Success") {
                            $personId = $respuesta_arboretto['data']['list'][0]['personId'];
                            $NumeroDocumentoEmpleado = $respuesta_arboretto['data']['list'][0]['personCode'];
                            $ApellidoEmpleado = $respuesta_arboretto['data']['list'][0]['personFamilyName'];
                            $NombreEmpleado = $respuesta_arboretto['data']['list'][0]['personGivenName'];
                            $TelefonoEmpleado = $respuesta_arboretto['data']['list'][0]['phoneNo'];
                            $EmailEmpleado = $respuesta_arboretto['data']['list'][0]['email'];

                            $JSon = '{
                                "personId": "' . $personId . '",
                                "personCode": "' . $NumeroDocumentoEmpleado . '",
                                "personFamilyName": "' . $ApellidoEmpleado . '",
                                "personGivenName": "' . $NombreEmpleado . '",
                                "gender": 1,
                                "orgIndexCode": "33",
                                "remark": "",
                                "phoneNo": "' . $TelefonoEmpleado . '",
                                "email": "' . trim($EmailEmpleado) . '",
                                "beginTime": "' . date('Y-m-d') . 'T00:00:00+00:00",
                                "endTime": "' . date('Y-m-d') . 'T00:00:00+00:00"
                                }';

                            $respuesta_arboretto = SIMWebServiceArboretto::borrar_vehiculo($IDClub, $row_socio["IDSocio"], "", $JSon);
                        }
                    }
                }
            }

            $sql_borrar_datos = "DELETE FROM RegistrosDinamicos WHERE IDSocio='$IDSocio' AND IDTipoFormulario='$IDTipoFormulario' AND FechaTrCr='$FechaTrCr'";
            $qry_delete = $dbo->query($sql_borrar_datos);




            $respuesta["message"] = "Registro eliminado";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }
        return $respuesta;
    }
}
