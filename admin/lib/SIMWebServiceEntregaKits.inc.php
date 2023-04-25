<?php
class SIMWebServiceEntregaKits
{

    public function get_configuracion_kits_deportivos($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        //tipos de documentos
        $TiposDocumento = array('', 'Cédula', 'Pasaporte');
        $responseDocumentos = array();

        for ($i = 1; $i < count($TiposDocumento); $i++) {
            $Documentos["IDTipoDocumento"] = (string)$i;
            $Documentos["Nombre"] = $TiposDocumento[$i];

            array_push($responseDocumentos, $Documentos);
        }
        $configuracion["TiposDocumento"] = $responseDocumentos;

        $sql = "SELECT LabelConfirmarEntregarKit FROM ConfiguracionKits  WHERE IDClub = '" . $IDClub . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {

                $configuracion["LabelConfirmarEntregarKit"] = $r["LabelConfirmarEntregarKit"];
            }
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $configuracion;
        } else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nohaydatosdeconfiguración,porfavorverificar', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } //fin function

    public function buscar_kit_deportivo_documento($IDClub, $IDSocio, $IDUsuario, $Documento, $IDCarrera)
    {
        $dbo = &SIMDB::get();
        if (!empty($Documento)) {




            $documento = $Documento;

            //$sql = "SELECT * FROM RegistroCorredor  WHERE IDClub = '" . $IDClub . "' AND NumeroDocumento='" . $documento . "' AND IDCarrera='" . $IDCarrera . "' AND KitEntregado='' LIMIT 1";
            $sql = "SELECT * FROM RegistroCorredor  WHERE IDClub = '" . $IDClub . "' AND NumeroDocumento='" . $documento . "' AND IDCarrera='" . $IDCarrera . "' AND KitEntregado='' ";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $kits["IDKit"] = $dbo->getFields("Kit", "IDKit", "IDCarrera = '" . $r["IDCarrera"] . "'") . "-" . $documento . "-" . $r["IDCarrera"];
                    $kits["Nombre"] = $r["Nombre"] . " " . $r["Apellido"];

                    //tipos de documentos 
                    $Documentos["IDTipoDocumento"] = (string)"1";
                    $Documentos["Nombre"] = "Cédula";
                    $kits["TipoDocumento"] = $Documentos;
                    $kits["Estado"] = ($r["KitEntregado"] == 'S') ? 'entregado' : 'porentregar';
                    $kits["TextoEstado"] = ($r["KitEntregado"] == 'S') ? 'Entregado' : 'Por Entregar';
                }
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $kits;
            } else {
                $respuesta["message"] = "El usuario con documento no existe o no tiene un kit por registrar o entregar";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "Faltan parametros buscar_kit_deportivo_documento";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } //fin function

    public function buscar_kit_deportivo_documento_qr($IDClub, $IDSocio, $IDUsuario, $Qr)
    {
        $dbo = &SIMDB::get();
        if (!empty($Qr)) {

            //si se escanea con el qr se hace el explode 
            //EL QR TRAE NUMCAMISETA|NUMERODOCUMENTO|NOMBRE
            $documentoBuscar = explode('|', $Qr);

            $documento = $documentoBuscar[1];
            $numeroDeCamisa = $documentoBuscar[0];



            //SELECCIONO LAS CARRERAS QUE TIENEASIGNADO EL USUARIO
            $sql_id_carrera = "SELECT IDCarrera FROM UsuarioCarrera WHERE IDUsuario = '$IDUsuario'";
            //echo $sql_id_carrera;
            $query_idcarrera = $dbo->query($sql_id_carrera);

            while ($Datos = $dbo->fetchArray($query_idcarrera)) {
                $ArrayidCarrera[] = $Datos["IDCarrera"];
            }
            $DatosArrayidCarrera = implode(",", $ArrayidCarrera);

            //print_r($DatosArrayidCarrera);




            //SI TIENE ASIGNDO AL MENOS UNA CARRERA VALIDO QUE ESE USUARIO SI PUEDE ENTREGAR EL KIT DE ESA CARRERA
            if (!empty($DatosArrayidCarrera)) {

                //SELECCIONO LA CARRERA QUE VIENE EN EL QR
                $IDCarrera = $dbo->getFields("RegistroCorredor", "IDCarrera", "NumCamiseta = '" . $numeroDeCamisa . "' AND NumeroDocumento='" . $documento . "'");

                //echo "IDCarrera" . $IDCarrera;

                if (!in_array($IDCarrera, $ArrayidCarrera)) {


                    $respuesta["message"] = "No tiene asignada esta carrera para entregar este kit ";
                    $respuesta["success"] = false;
                    $respuesta["response"] = null;
                    return $respuesta;
                }
            }



            $IDCarrera = $dbo->getFields("RegistroCorredor", "IDCarrera", "NumCamiseta = '" . $numeroDeCamisa . "' AND NumeroDocumento='" . $documento . "'");

            //$sql = "SELECT * FROM RegistroCorredor  WHERE IDClub = '" . $IDClub . "' AND NumeroDocumento='" . $documento . "' AND NumCamiseta='" . $numeroDeCamisa . "' AND KitEntregado='' LIMIT 1";
            $sql = "SELECT * FROM RegistroCorredor  WHERE IDClub = '" . $IDClub . "' AND NumeroDocumento='" . $documento . "'  AND IDCarrera='" . $IDCarrera . "' AND  KitEntregado=''";
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
                while ($r = $dbo->fetchArray($qry)) {
                    $kits["IDKit"] = $dbo->getFields("Kit", "IDKit", "IDCarrera = '" . $r["IDCarrera"] . "'") . "-" . $documento . "-" . $r["IDCarrera"];
                    $kits["Nombre"] = $r["Nombre"] . " " . $r["Apellido"];

                    //tipos de documentos 
                    $Documentos["IDTipoDocumento"] = (string)"1";
                    $Documentos["Nombre"] = "Cédula";
                    $kits["TipoDocumento"] = $Documentos;
                    $kits["Estado"] = ($r["KitEntregado"] == 'S') ? 'entregado' : 'porentregar';
                    $kits["TextoEstado"] = ($r["KitEntregado"] == 'S') ? 'Entregado' : 'Por Entregar';
                }
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $kits;
            } else {
                $respuesta["message"] = "El usuario con documento no existe o no tiene un kit por registrar o entregar";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "Faltan parametros buscar_kit_deportivo_documento qr";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } //fin function

    public function get_carreras_kits_deportivos($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();

        //SELECCIONO LAS CARRERAS QUE TIENE ASIGNADO EL USUARIO
        $sql_id_carrera = "SELECT IDCarrera FROM UsuarioCarrera WHERE IDUsuario = '$IDUsuario'";
        $query_idcarrera = $dbo->query($sql_id_carrera);

        while ($Datos = $dbo->fetchArray($query_idcarrera)) {
            $ArrayidCarrera[] = $Datos["IDCarrera"];
        }
        $DatosArrayidCarrera = implode(",", $ArrayidCarrera);

        if (!empty($DatosArrayidCarrera)) {
            $condicionCarreras = " AND IDCarrera in(" . $DatosArrayidCarrera . ")";
        } else {
            $condicionCarreras = "";
        }

        $response = array();
        $sql = "SELECT * FROM Carrera  WHERE IDClub = '" . $IDClub . "' AND Activo='S' $condicionCarreras ORDER BY IDCarrera ASC";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $carrera["IDCarrera"] = $r["IDCarrera"];
                $carrera["Nombre"] = $r["Nombre"];
                $carrera["Descripcion"] = $r["Nombre"];
                array_push($response, $carrera);
            }
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "No hay carreras activas";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } //fin function

    public function get_kits_deportivos($IDClub, $IDSocio, $IDUsuario, $Documento, $IDCarrera)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDCarrera)) {

            // buscar por documento
            if (!empty($Documento)) :
                $condicionDocumento = " AND  NumeroDocumento  like '%" . $Documento . "%'";
            endif;


            $sql = "SELECT * FROM RegistroCorredor  WHERE IDClub = '" . $IDClub . "' AND IDCarrera='" . $IDCarrera . "' $condicionDocumento";
            //echo $sql;
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);

                //conteo de los kits entregados
                $sql_kits_total = "SELECT count(IDRegistroCorredor) as TotalEntregados FROM RegistroCorredor WHERE IDCarrera = '" . $IDCarrera . "' AND KitEntregado='S'";
                $r_kits_total = $dbo->query($sql_kits_total);
                $row_kits_total = $dbo->fetchArray($r_kits_total);
                $kits["NumeroKitsEntregados"] = $row_kits_total["TotalEntregados"];

                //conteo de los kits no entregados
                $sql_kits_total_porentregar = "SELECT count(IDRegistroCorredor) as TotalPorEntregar FROM RegistroCorredor WHERE IDCarrera = '" . $IDCarrera . "' AND (KitEntregado='' OR KitEntregado='N')";
                $r_kits_total_porentregar = $dbo->query($sql_kits_total_porentregar);
                $row_kits_total_porentregar = $dbo->fetchArray($r_kits_total_porentregar);
                $kits["NumeroKitsSinEntregar"] = $row_kits_total_porentregar["TotalPorEntregar"];


                $response = array();
                while ($r = $dbo->fetchArray($qry)) {
                    $kitsDisponibles["IDKit"] = $dbo->getFields("Kit", "IDKit", "IDCarrera = '" . $r["IDCarrera"] . "'") . "-" . $r["NumeroDocumento"] . "-" . $r["IDCarrera"];
                    $kitsDisponibles["Nombre"] = $r["Nombre"] . " " . $r["Apellido"];
                    $kitsDisponibles["Documento"] = $r["NumeroDocumento"];
                    //tipos de documentos 
                    $Documentos["IDTipoDocumento"] = (string)"1";
                    $Documentos["Nombre"] = "Cédula";
                    $kitsDisponibles["TipoDocumento"] = $Documentos;

                    $kitsDisponibles["Estado"] = ($r["KitEntregado"] == 'S') ? 'entregado' : 'porentregar';
                    $kitsDisponibles["TextoEstado"] = ($r["KitEntregado"] == 'S') ? 'Entregado' : 'Por Entregar';

                    array_push($response, $kitsDisponibles);
                }
                $kits["Kits"] = $response;
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $kits;
            } else {
                $respuesta["message"] = "No hay registro de usuarios en esta carrera";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "Faltan parametros get_kits_deportivos";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } //fin function

    public function get_kit_deportivo_detalle($IDClub, $IDSocio, $IDUsuario, $IDKit)
    {
        $dbo = &SIMDB::get();

        if (!empty($IDKit)) {

            // IDKIT tiene idkit[0] - documentoCorredor[1] - idcarrera[2] por eso se hace el explode
            $ID = explode('-', $IDKit);

            $sql = "SELECT * FROM RegistroCorredor  WHERE IDClub = '" . $IDClub . "' AND IDCarrera='" . $ID[2] . "'  AND  NumeroDocumento  = '" . $ID[1] . "'";
            //echo $sql;
            $qry = $dbo->query($sql);
            if ($dbo->rows($qry) > 0) {
                $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);




                $response = array();
                while ($r = $dbo->fetchArray($qry)) {
                    $IDKit = $dbo->getFields("Kit", "IDKit", "IDCarrera = '" . $r["IDCarrera"] . "'");
                    $kitsDisponibles["IDKit"] = $IDKit . "-" . $r["NumeroDocumento"] . "-" . $ID[2];
                    $kitsDisponibles["Nombre"] = $r["Nombre"] . " " . $r["Apellido"];
                    $kitsDisponibles["Documento"] = $r["NumeroDocumento"];

                    //tipos de documentos 
                    $Documentos["IDTipoDocumento"] = (string)"1";
                    $Documentos["Nombre"] = "Cédula";

                    $kitsDisponibles["TipoDocumento"] = $Documentos;
                    $kitsDisponibles["Estado"] = ($r["KitEntregado"] == 'S') ? 'entregado' : 'porentregar';
                    $kitsDisponibles["TextoEstado"] = ($r["KitEntregado"] == 'S') ? 'Entregado' : 'Por Entregar';

                    $kitsDisponibles["TextoHeaderFormulario"] = "Registra todos los datos";

                    //Pregunta
                    $pregunta = array();
                    $response_pregunta = array();
                    $sql_respuesta = "Select * From PreguntaKit Where IDKit = '" . $IDKit . "' and Publicar = 'S' Order by Orden";
                    //   echo $sql_respuesta;
                    $r_encuesta = $dbo->query($sql_respuesta);

                    //preguntas fijas en el app, las de titulo Carrera,Categoria,Numero Camisa

                    $sql_preguntas_fijas = "SELECT * FROM RegistroCorredor WHERE NumeroDocumento='" . $ID[1] . "' AND IDCarrera='" . $ID[2] . "'";
                    $r_preguntas_fijas = $dbo->query($sql_preguntas_fijas);
                    while ($DatosPreguntasFijas = $dbo->fetchArray($r_preguntas_fijas)) {
                        //CARRERA
                        $preguntaCarrera["IDPregunta"] = "0";
                        $preguntaCarrera["TipoCampo"] = "titulo";
                        $preguntaCarrera["EtiquetaCampo"] = "Carrera:" . $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $DatosPreguntasFijas["IDCarrera"] . "'");
                        $preguntaCarrera["Obligatorio"] = "N";
                        $preguntaCarrera["Valores"] = $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $DatosPreguntasFijas["IDCarrera"] . "'");
                        $preguntaCarrera["Orden"] = "0";
                        array_push($response_pregunta, $preguntaCarrera);
                        //CATEGORIA
                        $preguntaCategoria["IDPregunta"] = "0";
                        $preguntaCategoria["TipoCampo"] = "titulo";
                        $preguntaCategoria["EtiquetaCampo"] = "Categoria:" . $dbo->getFields("CategoriaTriatlon", "Nombre", "IDCategoriaTriatlon = '" . $DatosPreguntasFijas["IDCategoriaTriatlon"] . "'");
                        $preguntaCategoria["Obligatorio"] = "N";
                        $preguntaCategoria["Valores"] = $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $DatosPreguntasFijas["IDCarrera"] . "'");
                        $preguntaCategoria["Orden"] = "0";
                        array_push($response_pregunta, $preguntaCategoria);
                        $preguntaCamisa["IDPregunta"] = "0";
                        $preguntaCamisa["TipoCampo"] = "titulo";
                        $preguntaCamisa["EtiquetaCampo"] = "Camisa:" . $DatosPreguntasFijas["NumCamiseta"];
                        $preguntaCamisa["Obligatorio"] = "N";
                        $preguntaCamisa["Valores"] = $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $DatosPreguntasFijas["IDCarrera"] . "'");
                        $preguntaCamisa["Orden"] = "0";
                        //array_push($response_pregunta, $preguntaCategoria);

                        array_push($response_pregunta, $preguntaCamisa);
                    }
                    /*  //CARRERA
                    $preguntaCarrera["IDPregunta"] = "0";
                    $preguntaCarrera["TipoCampo"] = "titulo";
                    $preguntaCarrera["EtiquetaCampo"] = "Carrera:" . $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $r["IDCarrera"] . "'");
                    $preguntaCarrera["Obligatorio"] = "N";
                    $preguntaCarrera["Valores"] = $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $r["IDCarrera"] . "'");
                    $preguntaCarrera["Orden"] = "0";
                    array_push($response_pregunta, $preguntaCarrera);
                    //CATEGORIA
                    $preguntaCategoria["IDPregunta"] = "0";
                    $preguntaCategoria["TipoCampo"] = "titulo";
                    $preguntaCategoria["EtiquetaCampo"] = "Categoria:" . $dbo->getFields("CategoriaTriatlon", "Nombre", "IDCategoriaTriatlon = '" . $r["IDCategoriaTriatlon"] . "'");
                    $preguntaCategoria["Obligatorio"] = "N";
                    $preguntaCategoria["Valores"] = $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $r["IDCarrera"] . "'");
                    $preguntaCategoria["Orden"] = "0";
                    array_push($response_pregunta, $preguntaCategoria);

                    //CAMISA
                    $preguntaCamisa["IDPregunta"] = "0";
                    $preguntaCamisa["TipoCampo"] = "titulo";
                    $preguntaCamisa["EtiquetaCampo"] = "Camisa:" . $r["NumCamiseta"];
                    $preguntaCamisa["Obligatorio"] = "N";
                    $preguntaCamisa["Valores"] = $dbo->getFields("Carrera", "Nombre", "IDCarrera = '" . $r["IDCarrera"] . "'");
                    $preguntaCamisa["Orden"] = "0"; */



                    //array_push($response_pregunta, $preguntaCamisa);
                    while ($row_pregunta = $dbo->FetchArray($r_encuesta)) :



                        $pregunta["IDPregunta"] = $row_pregunta["IDPreguntaKit"];
                        $pregunta["TipoCampo"] = $row_pregunta["TipoCampo"];
                        $pregunta["EtiquetaCampo"] = $row_pregunta["EtiquetaCampo"];
                        $pregunta["Obligatorio"] = $row_pregunta["Obligatorio"];
                        $pregunta["Valores"] = $row_pregunta["Valores"];
                        $pregunta["Orden"] = $row_pregunta["Orden"];
                        if ($row_pregunta["TipoCampo"] == "imagen" || $row_pregunta["TipoCampo"] == "firmadigital" || $row_pregunta["TipoCampo"] == "imagenarchivo") {
                            $pregunta["ParametroEnvioPost"] = "ImagenPregunta|" . $row_pregunta["IDPreguntaKit"];
                        }

                        array_push($response_pregunta, $pregunta);
                    endwhile;
                }
                $kitsDisponibles["FormularioEntregaAdicionales"] = $response_pregunta;


                array_push($response, $kitsDisponibles);

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $kitsDisponibles;
            } else {
                $respuesta["message"] = "No hay registro de usuarios en esta carrera";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            } //end else
        } else {
            $respuesta["message"] = "Faltan parametros get_kit_deportivo_detalle";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } //fin function

    public function set_kit_usuario($IDClub, $IDSocio, $IDUsuario, $Documento, $Nombre, $CamposKits, $Archivo, $File = "", $IDKit)
    {

        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($Documento)) {

            // IDKIT tiene idkit[0] - documentoCorredor[1] - idcarrera[2] por eso se hace el explode
            $ID = explode('-', $IDKit);




            $CamposKits = trim(preg_replace('/\s+/', ' ', $CamposKits));
            $datos_respuesta = json_decode($CamposKits, true);
            $datos_correctos = "S";
            if (count($datos_respuesta) > 0) :





                foreach ($datos_respuesta as $detalle_respuesta) :

                    $sql_datos_form = $dbo->query("INSERT INTO KitRespuesta (IDKit, IDSocio,IDUsuario,IDPreguntaKit, Valor,Documento,Nombre, FechaTrCr)
                         Values ('" .  $ID[0] . "','" . $IDSocio . "','" . $IDUsuario . "','" . $detalle_respuesta["IDCampoEntregaKit"] . "','"  . $detalle_respuesta["Valor"] . "','" . $Documento . "','" . $Nombre . "',NOW())");

                /*  $sql_datos_form = "INSERT INTO KitRespuesta (IDKit, IDSocio, IDPregunta, Valor, FechaTrCr)
                                                                                                                        Values ('" . $IDKit . "','" . $IDSocio . "','" . $detalle_respuesta["IDCampoEntregaKit"] . "','"  . $detalle_respuesta["Valor"] . "',NOW())";*/

                // echo $sql_datos_form;

                endforeach;
            endif;

            //subir las imagenes
            if (isset($File)) {
                //$nombrefoto.=json_encode($File);

                foreach ($File as $nombre_archivo => $archivo) {
                    //  echo "Nombre Archivo:" . $nombre_archivo;
                    $ArrayPreguntaEncuesta = explode("|", $nombre_archivo);
                    $IDPreguntaActualiza = $ArrayPreguntaEncuesta[1];

                    //echo $IDPreguntaActualiza;
                    //$nombrefoto.=$archivo["name"];
                    //$nombrefoto.=json_encode($archivo);
                    $tamano_archivo = $archivo["size"];
                    if ($tamano_archivo >= 6000000) {
                        $respuesta["message"] = "El archivo supera el limite de peso permitido de 6 megas, por favor verifique";
                        $respuesta["success"] = false;
                        $respuesta["response"] = null;
                        return $respuesta;
                    } else {
                        $files = SIMFile::upload($File[$nombre_archivo], ENTREGAKITS_DIR, "IMAGE");
                        if (empty($files) && !empty($File[$nombre_archivo]["name"])) :
                            $respuesta["message"] = "Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.";
                            $respuesta["success"] = false;
                            $respuesta["response"] = null;
                            return $respuesta;
                        endif;

                        $Archivo = $files[0]["innername"];
                        $actualiza_pregunta = "UPDATE KitRespuesta SET Valor = '" . $Archivo . "' WHERE IDPreguntaKit ='" . $IDPreguntaActualiza . "' and IDKit  = '" . $ID[0] . "'  AND Documento='" . $Documento . "' ORDER BY FechaTrCr DESC LIMIT 1";
                        // echo $actualiza_pregunta;
                        $dbo->query($actualiza_pregunta);
                        //$nombrefoto.=    $actualiza_pregunta;
                    }
                }
            }

            //actualizo al corredor que ya recibio el kit
            $FechaAcutal = date('Y-m-d H:i:s');
            $actualiza_corredor = "UPDATE RegistroCorredor SET KitEntregado = 'S',FechaEntregaKit='$FechaAcutal',IDUsuarioEntregaKit='$IDUsuario' WHERE NumeroDocumento ='" . $Documento . "' and IDClub  = '" . $IDClub . "' ";

            $dbo->query($actualiza_corredor);

            $respuesta["message"] = "Datos enviado correctamente";
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "E1. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } //fin function
}
