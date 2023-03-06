<?

SIMReg::setFromStructure(array(
    "title" => "EncuestaVial",
    "table" => "EncuestaVial",
    "key" => "IDEncuestaVial",
    "mod" => "EncuestaVial"
));

// var_dump($_POST)
$script = "encuestavial";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//Verificar permisos
SIMUtil::verificar_permiso($mod, SIMUser::get("IDPerfil"));

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);



switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "insert":
        /*
		 * Verificamos si el formulario valida.
		 * Si no valida devuelve un mensaje de error.
		 * SIMResources::capture  captura ese mensaje y si el mensaje existe devulve true
		*/

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas

            $frm = SIMUtil::varsLOG($_POST);

            $files =  SIMFile::upload($_FILES["Imagen"], BANNERAPP_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["Imagen"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Imagen"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["ImagenEncuesta"], BANNERAPP_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["ImagenEncuesta"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["ImagenEncuesta"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["Adjunto1Documento"], BANNERAPP_DIR, "DOC");
            if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Adjunto1File"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["Adjunto2Documento"], BANNERAPP_DIR, "DOC");
            if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Adjunto2File"] = $files[0]["innername"];

            //insertamos los datos del asistente
            $id = $dbo->insert($frm, $table, $key);

            // --------Inicio Notificación----------------------------

            $nombre_club = string;
            $nombre_club = $dbo->getFields("Club", "Nombre", "IDClub = '" . $frm["IDClub"] . "'");
            $TituloNotificacion = "Notificaciones " . $nombre_club;

            //verificar si se notifica
            if ($frm["NotificarPush"] == "S") {


                $frm["IDModulo"] = 3;
                $frm["TipoNotificacion"] = 'EncuestaVial'; //socio
                $frm["Mensaje"] = $frm["Introduccion"];
                if (empty($frm["Mensaje"])) $frm["Mensaje"] = ".";

                //notiifcar push
                if ($frm["DirigidoA"] == "S") :
                    $frm["TipoUsuario"] = 'S'; //socio

                    // if($frm["TipoSocio"]=="Socio"){
                    // 	$condicion_tipo=" and Socio.TipoSocio = '".$frm["TipoSocio"]."'";
                    // }
                    // elseif($frm["TipoSocio"]=="Estudiante"){
                    // 	$condicion_tipo=" and Socio.TipoSocio = '".$frm["TipoSocio"]."'";
                    // }

                    $arregloGrupos = explode("|||", $frm["SeleccionGrupo"]);

                    if (count($arregloGrupos) > 0) {
                        foreach ($arregloGrupos as $id_grupo => $datos_grupo) {
                            if (!empty($datos_grupo)) {
                                $array_datos_grupo = explode("-", $datos_grupo);
                                $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio = '" . $array_datos_grupo[1] . "'");
                                $array_invitados = explode("|||", $SociosGrupo);

                                if (count($array_invitados) > 0) {
                                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                                        $array_socios[] = $datos_invitado;
                                    }
                                }
                            }
                        }
                        if (count($array_socios) > 0) {
                            $id_socio_grupo = implode(",", $array_socios);
                            $condicion_tipo .= " and Socio.IDSocio in ( " . $id_socio_grupo . " )";
                        }
                    }

                    //traer socios a los que les interesa la EncuestaVial
                    $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccion WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccion.IDSocio AND SocioSeccion.IDSeccion = '" . $frm["IDSeccion"] . "' and Token <> '' and Token <> '2byte'  " . $condicion_tipo;
                    //  . $condicion_tipo;
                    //$sql_socios = "SELECT * FROM Socio WHERE IDSocio = '141131'";

                    $qry_socios = $dbo->query($sql_socios);

                    while ($r_socios = $dbo->fetchArray($qry_socios)) {

                        SIMUtil::envia_cola_notificacion($r_socios, $frm);
                        //Guardo el log
                        $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
						Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . $frm["Titular"] . "', '" . $frm["Mensaje"] . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $id . "')");
                    } //end while

                elseif ($frm["DirigidoA"] == "E") : //Empleados
                    $frm["TipoUsuario"] = 'E'; //socio
                    //traer empleados a los que les interesa la noticia
                    $sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
                    $qry_empleados = $dbo->query($sql_empleados);

                    while ($r_empleados = $dbo->fetchArray($qry_empleados)) {
                        SIMUtil::envia_cola_notificacion($r_socios, $frm);
                    } //end while
                endif;
            } //end if


            // --------Fin Notificación----------------------------


            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php");
        } else
            exit;
        break;
    case "edit":
        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        $sql_preguntas = " SELECT P.IDPreguntaVial,P.EtiquetaCampo,P.Orden
					FROM EncuestaVial E
					JOIN PreguntaVial P ON P.IDEncuestaVial = E.IDEncuestaVial
					WHERE E.IDClub = " . SIMUser::get("club") . "
					AND E.IDEncuestaVial = " . SIMNet::reqInt("id") . "
					AND P.Publicar = 'S'
					ORDER BY P.Orden";

        $result = $dbo->query($sql_preguntas);
        $numPreguntaVial = 1;
        $array_preguntas = array();
        $array_NoPreguntaVial = array();
        while ($rowPreguntaVial = $dbo->fetchArray($result)) {

            $array_preguntas[$rowPreguntaVial["IDPreguntaVial"]] = str_replace(",", "/", $rowPreguntaVial["EtiquetaCampo"]);
            $array_NoPreguntaVial[$rowPreguntaVial["IDPreguntaVial"]] = "_" . $rowPreguntaVial["IDPreguntaVial"];
        }


        break;

    case "update":


        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);


            $files =  SIMFile::upload($_FILES["Imagen"], BANNERAPP_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["Imagen"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Imagen"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["ImagenEncuesta"], BANNERAPP_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["ImagenEncuesta"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["ImagenEncuesta"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["Adjunto1Documento"], BANNERAPP_DIR, "DOC");
            if (empty($files) && !empty($_FILES["Adjunto1Documento"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Adjunto1File"] = $files[0]["innername"];

            $files =  SIMFile::upload($_FILES["Adjunto2Documento"], BANNERAPP_DIR, "DOC");
            if (empty($files) && !empty($_FILES["Adjunto2Documento"]["name"]))
                SIMNotify::capture("Ha ocurrido un error durante la carga del documento. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");

            $frm["Adjunto2File"] = $files[0]["innername"];

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"), "");

            // ------Inicio Notificación-----------------------------------

            $nombre_club = string;
            $nombre_club = $dbo->getFields("Club", "Nombre", "IDClub = '" . $frm["IDClub"] . "'");
            $TituloNotificacion = "Notificaciones " . $nombre_club;

            //verificar si se notifica
            if ($frm["NotificarPush"] == "S") {


                //notiifcar push
                $frm["IDModulo"] = 58;
                $frm["TipoNotificacion"] = 'EncuestaVial'; //socio
                $frm["Mensaje"] = $frm["Nombre"];
                if (empty($frm["Mensaje"])) $frm["Mensaje"] = ".";



                if ($frm["DirigidoA"] == "S" || $frm["DirigidoA"] == "T") :

                    //   . $condicion_tipo;

                    //$sql_socios = "SELECT * FROM Socio WHERE IDSocio in (159622,158750)";


                    switch ($frm["DirigidoAGeneral"]) {
                        case "S": // Todos
                            $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte' ";
                            break;
                        case "SE":
                            $array_invitados = explode("|||", $frm["InvitadoSeleccion"]);
                            foreach ($array_invitados as $id_invitado => $datos_invitado) :
                                if (!empty($datos_invitado)) {
                                    $array_datos_invitados = explode("-", $datos_invitado);
                                    $item--;
                                    $IDSocioInvitacion = $array_datos_invitados[1];
                                    if ($IDSocioInvitacion > 0) :
                                        $array_id_soc[] = $IDSocioInvitacion;
                                    endif;
                                }
                            endforeach;

                            if (count($array_id_soc) > 0) {
                                $id_soc_consulta = implode(",", $array_id_soc);
                                $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' and IDSocio in ($id_soc_consulta) and Token <> '' and Token <> '2byte' ";
                            }

                            break;
                        case "GS":
                            if (!empty($frm["IDGrupoSocio"])) {
                                $sql_grupo = "SELECT IDSocio FROM GrupoSocio WHERE IDGrupoSocio = '" . $frm["IDGrupoSocio"] . "'";
                                $resul_grupo = $dbo->query($sql_grupo);
                                $row_grupo = $dbo->fetchArray($resul_grupo);
                                $array_soc = explode("|||", $row_grupo["IDSocio"]);
                                foreach ($array_soc as $id_soc => $identificador_soc) {
                                    if (!empty($id_soc)) {
                                        $array_id_soc[] = $identificador_soc;
                                    }
                                }

                                if (count($array_id_soc) > 0) {
                                    $id_soc_consulta = implode(",", $array_id_soc);
                                    $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' and IDSocio in ($id_soc_consulta) and Token <> '' and Token <> '2byte' ";
                                }
                            }

                            $arregloGrupos = explode("|||", $frm["SeleccionGrupo"]);

                            if (count($arregloGrupos) > 0) {
                                foreach ($arregloGrupos as $id_grupo => $datos_grupo) {
                                    if (!empty($datos_grupo)) {
                                        $array_datos_grupo = explode("-", $datos_grupo);
                                        $SociosGrupo = $dbo->getFields("GrupoSocio", "IDSocio", "IDGrupoSocio = '" . $array_datos_grupo[1] . "'");
                                        $array_invitados = explode("|||", $SociosGrupo);

                                        if (count($array_invitados) > 0) {
                                            foreach ($array_invitados as $id_invitado => $datos_invitado) {
                                                $array_socios[] = $datos_invitado;
                                            }
                                        }
                                    }
                                }

                                if (count($array_socios) > 0) {
                                    $id_soc_consulta = implode(",", $array_socios);
                                    $sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio WHERE Socio.IDClub = '" . $frm["IDClub"] . "' and IDSocio in (" . $id_soc_consulta . ") and Token <> '' and Token <> '2byte' ";
                                }
                            }


                            break;
                    }


                    //echo $sql_socios;
                    //exit;

                    //$sql_socios = "SELECT Socio.IDSocio, Socio.IDClub, Socio.Token, Socio.Dispositivo FROM Socio, SocioSeccion WHERE Socio.IDClub = '" . $frm["IDClub"] . "' AND Socio.IDSocio = SocioSeccion.IDSocio AND SocioSeccion.IDSeccion = '" . $frm["IDSeccion"] . "' and Token <> '' and Token <> '2byte' and Socio.IDSocio = '118543'";
                    $frm["TipoUsuario"] = 'S'; //socio
                    $qry_socios = $dbo->query($sql_socios);
                    while ($r_socios = $dbo->fetchArray($qry_socios)) {
                        SIMUtil::envia_cola_notificacion($r_socios, $frm);
                        $sql_log = $dbo->query("INSERT INTO LogNotificacion (IDNotificacionesGenerales	, IDSocio, IDClub, Token, Dispositivo, Fecha, App, Tipo, Titulo, Mensaje, Modulo, IDSeccion, IDDetalle)
				  															Values ('" . $id . "', '" . $r_socios["IDSocio"] . "','" . $r_socios["IDClub"] . "','" . $r_socios["Token"] . "','" . $r_socios["Dispositivo"] . "',NOW(),'Socio','" . $frm["TipoNotificacion"] . "', '" . $frm["Titular"] . "', '" . $frm["Mensaje"] . "', '" . $frm["IDModulo"] . "','" . $frm["IDSeccion"] . "', '" . $frm["ID"] . "')");
                    } //end while
                elseif ($frm["DirigidoA"] == "E") : //Empleados
                    //traer empleados a los que les interesa la noticia
                    $frm["TipoUsuario"] = 'E'; //socio
                    $sql_empleados = "SELECT * FROM Usuario WHERE IDClub = '" . $frm["IDClub"] . "' and Token <> '' and Token <> '2byte'  ";
                    $qry_empleados = $dbo->query($sql_empleados);
                    while ($r_empleados = $dbo->fetchArray($qry_empleados)) {
                        SIMUtil::envia_cola_notificacion($r_empleados, $frm);
                    } //end while
                endif;
            } //end if

            // ------>Fin Notificación-----------------------------------


            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMNotify::capture("Los cambios han sido guardados satisfactoriamente", "info");
            SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroGuardadoCorrectamente', LANGSESSION));
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        } else
            exit;

        break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;

    case "delfoto":
        $campo = $_GET['campo'];
        $doceliminar = BANNERAPP_DIR . $dbo->getFields("EncuestaVial", "$campo", "IDEncuestaVial = '" . $_GET[id] . "'");
        unlink($doceliminar);
        $dbo->query("UPDATE EncuestaVial SET $campo = '' WHERE IDEncuestaVial = $_GET[id] LIMIT 1 ;");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ImagenEliminadaCorrectamente', LANGSESSION));

        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
        exit;
        break;

    case "InsertarPreguntaVial":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $id = $dbo->insert($frm, "PreguntaVial", "IDPreguntaVial");

        $borro_opciones = "DELETE FROM PreguntaVialOpcionesRespuesta WHERE IDPreguntaVial = '" . $frm["IDPreguntaVial"] . "'";
        $dbo->query($borro_opciones);
        for ($i = 1; $i <= $frm["CantidadOpciones"]; $i++) {
            if (!empty($frm["Respuesta" . $i])) {
                $frm["Respuesta" . $i] = trim(preg_replace('/\s+/', ' ', $frm["Respuesta" . $i]));
                $sql_insert = "INSERT INTO PreguntaVialOpcionesRespuesta (IDPreguntaVial,IDPreguntaVialSiguiente,Opcion,Terminar,Peso,Orden)
																 VALUES('" . $id . "','" . $frm["IDPreguntaVialSiguiente" . $i] . "','" . $frm["Respuesta" . $i] . "','" . $frm["Terminar" . $i] . "','" . $frm["Peso" . $i] . "','" . $frm["Orden" . $i] . "')";
                $dbo->query($sql_insert);
            }
        }

        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm['IDEncuestaVial']);
        exit;
        break;

    case "ModificaPreguntaVial":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $dbo->update($frm, "PreguntaVial", "IDPreguntaVial", $frm["IDPreguntaVial"]);

        $borro_opciones = "DELETE FROM PreguntaVialOpcionesRespuesta WHERE IDPreguntaVial = '" . $frm["IDPreguntaVial"] . "'";
        $dbo->query($borro_opciones);
        for ($i = 1; $i <= $frm["CantidadOpciones"]; $i++) {
            if (!empty($frm["Respuesta" . $i])) {
                $frm["Respuesta" . $i] = trim(preg_replace('/\s+/', ' ', $frm["Respuesta" . $i]));
                $sql_insert = "INSERT INTO PreguntaVialOpcionesRespuesta (IDPreguntaVial,IDPreguntaVialSiguiente,Opcion,Terminar,Peso,Orden)
																 VALUES('" . $frm["IDPreguntaVial"] . "','" . $frm["IDPreguntaVialSiguiente" . $i] . "','" . $frm["Respuesta" . $i] . "','" . $frm["Terminar" . $i] . "','" . $frm["Peso" . $i] . "','" . $frm["Orden" . $i] . "')";
                $dbo->query($sql_insert);
            }
        }

        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $frm['IDEncuestaVial']);
        exit;
        break;

    case "EliminaPreguntaVial":
        $id = $dbo->query("DELETE FROM PreguntaVial WHERE IDPreguntaVial   = '" . $_GET["IDPreguntaVial"] . "' LIMIT 1");
        $borro_opciones = "DELETE FROM PreguntaVialOpcionesRespuesta WHERE IDPreguntaVial = '" . $_GET["IDPreguntaVial"] . "'";
        $dbo->query($borro_opciones);
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=formulario&id=" . $_GET["id"]);
        exit;
        break;

    case "EliminarEncuestaRespuestaVial":

        for ($i = 1; $i <= $_GET["cantidad"]; $i++) {
            $sql = "DELETE FROM EncuestaRespuestaVial WHERE IDEncuestaVial  = '" . $_GET["IDEncuestaVial"] . "' AND IDSocio = '" . $_GET["IDSocio"] . "' ORDER BY FechaTrCr DESC LIMIT 1";
            $dbo->query($sql);
        }

        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . $_GET["IDEncuestaVial"]);
        exit;

        break;

    case "copiarEncuesta":

        $frm = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");

        $now = date("Y-m-j h:i:s");
        $frm['IDEncuestaVial'] = "";
        $frm['Nombre'] .= " - COPIA";
        $frm['FechaTrEd'] = $now;
        $frm['FechaTrCr'] = $now;
        $frm['copia'] = 'S';

        $id = $dbo->insert($frm, $table, $key);

        $consultaMax = "SELECT MAX(IDEncuestaVial) as Maximo FROM " . $table;
        $ejecutaMax = $dbo->query($consultaMax);
        $max = $dbo->fetchArray($ejecutaMax);
        $IDEncuestaVial = $max['Maximo'];


        $preguntas = $dbo->fetchAll("PreguntaVial", "IDEncuestaVial = " . SIMNet::reqInt("id"));

        for ($i = 0; $i < count($preguntas); $i++) {
            $frmPREGUNTA = $dbo->fetchById("PreguntaVial", "IDPreguntaVial", $preguntas[$i][IDPreguntaVial], "array");
            $frmPREGUNTA['IDPreguntaVial'] = "";
            $frmPREGUNTA['IDEncuestaVial'] = $IDEncuestaVial;
            $frmPREGUNTA['FechaTrEd'] = $now;
            $frmPREGUNTA['FechaTrCr'] = $now;

            $id = $dbo->insert($frmPREGUNTA, "PreguntaVial", "IDPreguntaVial");
        }

        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EncuestaCopiadaExitosamente', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php");
        exit;
        break;

    case "InsertarNotificacionLocal":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["IDModulo"] = 133;
        $frm["IDDetalle"] = $frm["IDDiagnostico"];
        foreach ($frm["IDDia"] as $Dia_seleccion) :
            $array_dia[] = $Dia_seleccion;
        endforeach;
        if (count($array_dia) > 0) :
            $id_dia = implode("|", $array_dia) . "|";
        endif;
        $frm["Dias"] = $id_dia;
        $id = $dbo->insert($frm, "NotificacionLocal", "IDNotificacionLocal");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'RegistroExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $frm["IDDiagnostico"]);
        exit;
        break;

    case "ModificaNotificacionLocal":
        $frm = SIMUtil::varsLOG($_POST);
        foreach ($frm["IDDia"] as $Dia_seleccion) :
            $array_dia[] = $Dia_seleccion;
        endforeach;
        if (count($array_dia) > 0) :
            $id_dia = implode("|", $array_dia) . "|";
        endif;
        $frm["Dias"] = $id_dia;
        $dbo->update($frm, "NotificacionLocal", "IDNotificacionLocal", $frm["IDNotificacionLocal"]);
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ModificacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $frm["IDDiagnostico"]);
        exit;
        break;

    case "EliminaNotificacionLocal":
        $id = $dbo->query("DELETE FROM NotificacionLocal WHERE IDNotificacionLocal   = '" . $_GET["IDNotificacionLocal"] . "' LIMIT 1");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'EliminacionExitoso', LANGSESSION));
        SIMHTML::jsRedirect($script . ".php?action=edit&tabencuesta=notificacionlocal&id=" . $_GET["id"]);
        exit;
        break;

    case "DelDocNot":
        $campo = $_GET['cam'];
        $doceliminar = BANNERAPP_DIR . $dbo->getFields("EncuestaVial", "$campo", "IDEncuestaVial = '" . $_GET[id] . "'");
        unlink($doceliminar);
        $dbo->query("UPDATE EncuestaVial SET $campo = '' WHERE IDEncuestaVial = $_GET[id] LIMIT 1 ;");
        SIMHTML::jsAlert(SIMUtil::get_traduccion('', '', 'ArchivoEliminadoCorrectamente', LANGSESSION));
        SIMHTML::jsRedirect("?mod=" . $mod . "&action=edit&id=" . $_GET[id]);
        exit;
        break;


    default:
        $view = "views/" . $script . "/list.php";
} // End switch



if (empty($view))
    $view = "views/" . $script . "/form.php";
