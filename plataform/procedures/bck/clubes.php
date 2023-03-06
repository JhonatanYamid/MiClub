 <?php

SIMReg::setFromStructure(array(
    "title" => "Club	",
    "table" => "Club",
    "key" => "IDClub",
    "mod" => "Club",
));

$script = "clubes";

//extraemos las variables
$table = SIMReg::get("table");
$key = SIMReg::get("key");
$mod = SIMReg::get("mod");

//creando las notificaciones que llegan en el parametro m de la URL
SIMNotify::capture(SIMResources::$mensajes[SIMNet::req("m")]["msg"], SIMResources::$mensajes[SIMNet::req("m")]["type"]);

function duplicar_club($IDClubNuevo)
{
    $dbo = &SIMDB::get();
    $IDClubPadre = 91;
    $IDClubDestino = $IDClubNuevo;
    //Preguntas Perfil
    $sql_secc = "INSERT INTO CampoEditarSocio (IDClub,Nombre,CampoTabla,Tipo,Valores,PermiteEditar,Obligatorio,Orden)
               SELECT " . $IDClubDestino . ",Nombre,CampoTabla,Tipo,Valores,PermiteEditar,Obligatorio,Orden
               FROM CampoEditarSocio
               WHERE IDClub = '" . $IDClubPadre . "' ";
    $dbo->query($sql_secc);

    //Duplicar archivos
    $sql_seccion = "SELECT * From TipoArchivo Where IDClub = '" . $IDClubPadre . "'";
    $r_seccion = $dbo->query($sql_seccion);
    while ($row = $dbo->fetchArray($r_seccion)) {
        $sql_secc = "INSERT INTO TipoArchivo (IDClub,DirigidoA,Nombre,Tipo,Icono,Mostrar,SoloIcono,Publicar)
                   SELECT " . $IDClubDestino . ",DirigidoA,Nombre,Tipo,Icono,Mostrar,SoloIcono,Publicar
                   FROM TipoArchivo
                   WHERE IDClub = '" . $IDClubPadre . "' and IDTipoArchivo = '" . $row["IDTipoArchivo"] . "'";
        $dbo->query($sql_secc);
        $id_nuevo = $dbo->lastID();
        //Selecciono las not
        $sql_hijo = "SELECT * From Documento Where IDClub = '" . $IDClubPadre . "' and IDTipoArchivo = '" . $row["IDTipoArchivo"] . "' ";
        $r_hijo = $dbo->query($sql_hijo);
        while ($row_hijo = $dbo->fetchArray($r_hijo)) {
            $sql_duplicar = "INSERT INTO Documento (IDClub,IDTipoArchivo,IDServicio,Nombre,Subtitular,Descripcion,Fecha,Archivo1,Icono,Orden,Publicar)
                     SELECT " . $IDClubDestino . "," . $id_nuevo . ",IDServicio,Nombre,Subtitular,Descripcion,Fecha,Archivo1,Icono,Orden,Publicar
                     FROM Documento
                     WHERE IDClub = '" . $IDClubPadre . "' and IDDocumento= '" . $row_hijo["IDDocumento"] . "'";
            $dbo->query($sql_duplicar);
        }
    }
    //Fin duplicar archivos

    //Diagnostico
    $sql_seccion = "SELECT * From Diagnostico Where IDClub = '" . $IDClubPadre . "'";
    $r_seccion = $dbo->query($sql_seccion);
    while ($row = $dbo->fetchArray($r_seccion)) {
        $sql_secc = "INSERT INTO Diagnostico (IDClub,IDGrupoSocio,DirigidoA,Nombre,Descripcion,Orden,SolicitarAbrirApp,UnaporSocio,FechaInicio,FechaFin,Imagen,DirigidoAGeneral,InvitadoSeleccion,PesoMaximo,EmailAlerta,MensajeBien,MensajeMal,Publicar)
                   SELECT " . $IDClubDestino . ",IDGrupoSocio,DirigidoA,Nombre,Descripcion,Orden,SolicitarAbrirApp,UnaporSocio,FechaInicio,FechaFin,Imagen,DirigidoAGeneral,InvitadoSeleccion,PesoMaximo,EmailAlerta,MensajeBien,MensajeMal,Publicar
                   FROM Diagnostico
                   WHERE IDClub = '" . $IDClubPadre . "' and IDDiagnostico = '" . $row["IDDiagnostico"] . "'";
        $dbo->query($sql_secc);
        $id_nuevo = $dbo->lastID();
        //Selecciono las preguntas
        $sql_hijo = "SELECT * From PreguntaDiagnostico Where IDDiagnostico = '" . $row["IDDiagnostico"] . "' ";
        $r_hijo = $dbo->query($sql_hijo);
        while ($row_hijo = $dbo->fetchArray($r_hijo)) {
            $sql_duplicar = "INSERT INTO PreguntaDiagnostico (IDDiagnostico,TipoCampo,EtiquetaCampo,Obligatorio,Valores,Orden,Publicar)
                     SELECT " . $id_nuevo . ",TipoCampo,EtiquetaCampo,Obligatorio,Valores,Orden,Publicar
                     FROM PreguntaDiagnostico
                     WHERE IDDiagnostico= '" . $row_hijo["IDDiagnostico"] . "' and IDPreguntaDiagnostico = '" . $row_hijo["IDPreguntaDiagnostico"] . "'";
            $dbo->query($sql_duplicar);
            $id_nueva_pregunta = $dbo->lastID();
            //selecciono las respuestas
            $sql_resp = "SELECT * From DiagnosticoOpcionesRespuesta Where IDDiagnosticoPregunta = '" . $row_hijo["IDPreguntaDiagnostico"] . "' ";
            $r_resp = $dbo->query($sql_resp);
            while ($row_resp = $dbo->fetchArray($r_resp)) {
                $sql_duplicar_resp = "INSERT INTO DiagnosticoOpcionesRespuesta (IDDiagnosticoPregunta,IDDiagnosticoPreguntaSiguiente,Opcion,Terminar,Peso,Orden)
                       SELECT " . $id_nueva_pregunta . ",IDDiagnosticoPreguntaSiguiente,Opcion,Terminar,Peso,Orden
                       FROM DiagnosticoOpcionesRespuesta
                       WHERE IDDiagnosticoOpcionesRespuesta= '" . $row_resp["IDDiagnosticoOpcionesRespuesta"] . "'";
                $dbo->query($sql_duplicar_resp);
            }
        }
    }
    //Fin duplicar diiagnostico

    //Duplicar noticias
    $sql_seccion = "SELECT * From Seccion Where IDClub = '" . $IDClubPadre . "'";
    $r_seccion = $dbo->query($sql_seccion);
    while ($row = $dbo->fetchArray($r_seccion)) {
        $sql_secc = "INSERT INTO Seccion (IDClub,IDPadre,DirigidoA,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase,PublicarZona,UbicacionZona)
                   SELECT " . $IDClubDestino . ",IDPadre,DirigidoA,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase,PublicarZona,UbicacionZona
                   FROM Seccion
                   WHERE IDClub = '" . $IDClubPadre . "' and IDSeccion = '" . $row["IDSeccion"] . "'";
        $dbo->query($sql_secc);
        $id_nuevo = $dbo->lastID();
        //Selecciono las not
        $sql_hijo = "SELECT * From Noticia Where IDClub = '" . $IDClubPadre . "' and IDSeccion = '" . $row["IDSeccion"] . "' ";
        $r_hijo = $dbo->query($sql_hijo);
        while ($row_hijo = $dbo->fetchArray($r_hijo)) {
            $sql_duplicar = "INSERT INTO Noticia (IDClub,IDSeccion,DirigidoA,TipoSocio,URL,Titular,SubTitular,Introduccion,Cuerpo,Plantilla,Publicar,Servicio,Home,Destacada,Orden,FechaInicio,FechaFin,NoticiaFile,Adjunto1File,Adjunto2File,Foto1,Foto2,Foto3,FotoDestacada,Tag,FechaNotificacion,Video)
                     SELECT " . $IDClubDestino . "," . $id_nuevo . ",DirigidoA,TipoSocio,URL,Titular,SubTitular,Introduccion,Cuerpo,Plantilla,Publicar,Servicio,Home,Destacada,Orden,FechaInicio,FechaFin,NoticiaFile,Adjunto1File,Adjunto2File,Foto1,Foto2,Foto3,FotoDestacada,Tag,FechaNotificacion,Video
                     FROM Noticia
                     WHERE IDClub = '" . $IDClubPadre . "' and IDNoticia= '" . $row_hijo["IDNoticia"] . "'";
            $dbo->query($sql_duplicar);
        }
    }
    //Fin duplicar noticias

    //Duplicar eventos
    $sql_seccion = "SELECT * From SeccionEvento Where IDClub = '" . $IDClubPadre . "'";
    $r_seccion = $dbo->query($sql_seccion);
    while ($row = $dbo->fetchArray($r_seccion)) {
        $sql_secc = "INSERT INTO SeccionEvento (IDClub,IDPadre,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase,PublicarZona,UbicacionZona)
                   SELECT " . $IDClubDestino . ",IDPadre,Nombre,Descripcion,Documentos,Publicar,Ubicacion,Orden,SeccionFile,URL,Clase,PublicarZona,UbicacionZona
                   FROM SeccionEvento
                   WHERE IDClub = '" . $IDClubPadre . "' and IDSeccionEvento = '" . $row["IDSeccionEvento"] . "'";
        $dbo->query($sql_secc);
        $id_nuevo = $dbo->lastID();
        //Selecciono las not
        $sql_hijo = "SELECT * From Evento Where IDClub = '" . $IDClubPadre . "' and IDSeccionEvento = '" . $row["IDSeccionEvento"] . "' ";
        $r_hijo = $dbo->query($sql_hijo);
        while ($row_hijo = $dbo->fetchArray($r_hijo)) {
            $sql_duplicar = "INSERT INTO Evento (IDClub,IDSeccionEvento,DirigidoA,TipoSocio,Titular,SubTitular,Introduccion,Cuerpo,CuerpoEmail,Publicar,Home,Destacada,Orden,FechaInicio,FechaFin,EventoFile,Adjunto1File,Adjunto2File,Adjunto3File,Adjunto4File,
                    Foto1,Foto2,Foto3,FotoDestacada,Visitas,Lugar,Hora,FechaEvento,FechaFinEvento,Valor,EmailContacto,Tag,InscripcionApp,MensajePagoInscripcion,ValorInscripcion,EmailNotificacionInscripcion,MaximoParticipantes,PermiteRepetir,FechaLimiteInscripcion,
                    HoraLimiteInscripcion,FechaNotificacion)
                     SELECT " . $IDClubDestino . "," . $id_nuevo . ",DirigidoA,TipoSocio,Titular,SubTitular,Introduccion,Cuerpo,CuerpoEmail,Publicar,Home,Destacada,Orden,FechaInicio,FechaFin,EventoFile,Adjunto1File,Adjunto2File,Adjunto3File,Adjunto4File,Foto1,Foto2,Foto3,FotoDestacada,
                     Visitas,Lugar,Hora,FechaEvento,FechaFinEvento,Valor,EmailContacto,Tag,InscripcionApp,MensajePagoInscripcion,ValorInscripcion,EmailNotificacionInscripcion,MaximoParticipantes,PermiteRepetir,FechaLimiteInscripcion,HoraLimiteInscripcion,FechaNotificacion
                     FROM Evento
                     WHERE IDClub = '" . $IDClubPadre . "' and IDEvento= '" . $row_hijo["IDEvento"] . "'";
            $dbo->query($sql_duplicar);
        }
    }
    //Fin duplicar eventos

    //Duplicar Publicidad
    $sql_reg = "INSERT INTO Publicidad (IDClub,DirigidoA,Nombre,Descripcion,AccionClick,Cuerpo,Url,Header,Footer,Foto1,FechaInicio,FechaFin,Orden,Publicar)
               SELECT " . $IDClubDestino . ",DirigidoA,Nombre,Descripcion,AccionClick,Cuerpo,Url,Header,Footer,Foto1,FechaInicio,FechaFin,Orden,Publicar
               FROM Publicidad
               WHERE IDClub = '" . $IDClubPadre . "'";
    $dbo->query($sql_reg);

    //Duplicar Pqr

    $sql_reg = "INSERT INTO TipoPqr (IDClub,Nombre,Descripcion,Publicar)
               SELECT " . $IDClubDestino . ",Nombre,Descripcion,Publicar
               FROM TipoPqr
               WHERE IDClub = '" . $IDClubPadre . "'";
    $dbo->query($sql_reg);

    $sql_reg = "INSERT INTO Area (IDClub,Nombre,Responsable,CorreoResponsable,MostrarApp,Orden,Icono,Activo)
               SELECT " . $IDClubDestino . ",Nombre,Responsable,CorreoResponsable,MostrarApp,Orden,Icono,Activo
               FROM Area
               WHERE IDClub = '" . $IDClubPadre . "'";
    $dbo->query($sql_reg);
}

switch (SIMNet::req("action")) {

    case "add":
        $view = "views/" . $script . "/form.php";
        $newmode = "insert";
        $titulo_accion = "Crear";
        break;

    case "insert":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            //UPLOAD de imagenes
            if (isset($_FILES)) {

                $files = SIMFile::upload($_FILES["Foto"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["Foto"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["FotoDiseno1"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoDiseno1"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["FotoDiseno1"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["FotoLogoApp"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoLogoApp"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["FotoLogoApp"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["ArchivoTerminos"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["ArchivoTerminos"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["ArchivoTerminos"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["ArchivoTerminosPago"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["ArchivoTerminosPago"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["ArchivoTerminosPago"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["IconoLikeNoticias"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["IconoLikeNoticias"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["IconoLikeNoticias"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["IconoUnLikeNoticias"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["IconoUnLikeNoticias"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["IconoUnLikeNoticias"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["Contrato"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["Contrato"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $files = SIMFile::upload($_FILES["ImagenComentarios"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["ImagenComentarios"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }
                $frm["ImagenComentarios"] = $files[0]["innername"];

                $frm["Contrato"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["Oferta"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["Oferta"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["Oferta"] = $files[0]["innername"];

            } //end if
            //$frm["LabelInvitacion"]=utf8_decode($frm["LabelInvitacion"]);

            //insertamos los datos
            $id = $dbo->insert($frm, $table, $key);
            $id_nuevo_club = $dbo->lastID();

            $frm2 = $frm;
            $frm2[IDClub] = $id_nuevo_club;
            $id2 = $dbo->insert($frm2, "ConfiguracionClub", "IDConfiguracionClub");            

            //Actualizo Servicios
            foreach ($frm[ServicioClub] as $id_servicio):
                $sql_interta_servicio = $dbo->query("Insert into ServicioClub (IDClub, IDServicio) Values ('" . $id . "', '" . $id_servicio . "')");
            endforeach;

            //Actualizo Servicios
            $query_servicios = $dbo->query("Select * from ServicioMaestro Where Publicar = 'S' Order by Nombre");
            while ($r = $dbo->object($query_servicios)) {
                if (in_array($r->IDServicioMaestro, $frm[ServicioClub])):
                    $activo = "S";
                else:
                    $activo = "N";
                endif;

                $id_servicio_maestro = $dbo->getFields("ServicioClub", "IDServicioMaestro", "IDServicioMaestro = '" . $r->IDServicioMaestro . "' and IDClub = '" . SIMNet::reqInt("id") . "'");
                if (empty($id_servicio_maestro)):
                    $sql_inserta_servicio = "Insert Into  ServicioClub (IDClub	, IDServicioMaestro, Activo) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDServicioMaestro . "','" . $activo . "')";
                    $dbo->query($sql_inserta_servicio);
                else:
                    $sql_actualiza_servicio = "Update ServicioClub Set Activo = '" . $activo . "' Where  IDClub = '" . SIMNet::reqInt("id") . "'	and IDServicioMaestro = '" . $r->IDServicioMaestro . "'";
                    $dbo->query($sql_actualiza_servicio);
                endif;

                //Crear la configuracion inicial
                $id_servicio_conf = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicioMaestro = '" . $r->IDServicioMaestro . "' and IDClub = '" . SIMNet::reqInt("id") . "'");
                if (empty($id_servicio_conf)):
                    $sql_inserta_servicio_conf = "Insert Into  Servicio (IDClub, IDServicioMaestro, Publicar) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDServicioMaestro . "','" . $activo . "')";
                    $dbo->query($sql_inserta_servicio_conf);
                endif;

            }

            $query_modulos = $dbo->query("Select * from Modulo Where Publicar = 'S' Order by Nombre");
            while ($r = $dbo->object($query_modulos)) {
                $sql_interta_modulo = $dbo->query("Insert into ClubModulo (IDClub, IDModulo) Values ('" . $id . "', '" . $r->IDModulo . "')");
            }

            $resp = duplicar_club($id_nuevo_club);

            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php");
        } else {
            exit;
        }

        break;

    case "edit":
        $frm1 = $dbo->fetchById($table, $key, SIMNet::reqInt("id"), "array");
        
        $config = "SELECT * FROM ConfiguracionClub WHERE IDClub = ". SIMNet::reqInt("id");
        $qry = $dbo->query($config);
        $frm2 = $dbo->fetchArray($qry);

        if(!empty($frm2)):
            $frm = array_merge($frm1, $frm2);
        else:
            $frm = $frm1;
        endif;        

        $view = "views/" . $script . "/form.php";
        $newmode = "update";
        $titulo_accion = "Actualizar";

        break;

    case "update":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            //UPLOAD de imagenes
            if (isset($_FILES)) {
                $files = SIMFile::upload($_FILES["Foto"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["Foto"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["FotoDiseno1"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoDiseno1"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["FotoDiseno1"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["FotoLogoApp"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoLogoApp"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["FotoLogoApp"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["IconoTelefono"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["IconoTelefono"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["IconoTelefono"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["IconoEmail"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["IconoEmail"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["IconoEmail"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["ArchivoTerminos"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["ArchivoTerminos"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["ArchivoTerminos"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["ArchivoTerminosPago"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["ArchivoTerminosPago"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["ArchivoTerminosPago"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["IconoLikeNoticias"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["IconoLikeNoticias"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["IconoLikeNoticias"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["IconoUnLikeNoticias"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["IconoUnLikeNoticias"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["IconoUnLikeNoticias"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["ImagenComentarios"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["ImagenComentarios"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }
                $frm["ImagenComentarios"] = $files[0]["innername"];


                $files = SIMFile::upload($_FILES["Contrato"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["Contrato"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["Contrato"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["Oferta"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["Oferta"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["Oferta"] = $files[0]["innername"];

            } //end if

            //$frm["LabelInvitacion"]=utf8_decode($frm["LabelInvitacion"]);

            $id = $dbo->update($frm, $table, $key, SIMNet::reqInt("id"));

            $sql = "SELECT IDConfiguracionClub FROM ConfiguracionClub WHERE IDClub = ".SIMNet::reqInt("id");
            $qry = $dbo->query($sql);
            $dat = $dbo->fetchArray($qry);

            if(!empty($dat[IDConfiguracionClub])):
                $frm[IDClub] = SIMNet::reqInt("id");
                $id2 = $dbo->update($frm, "ConfiguracionClub", "IDConfiguracionClub", $dat[IDConfiguracionClub]);                
            else:                
                $frm[IDClub] = SIMNet::reqInt("id");               
                $id2 = $dbo->insert($frm, "ConfiguracionClub", "IDConfiguracionClub");               
            endif;

            //Actualizo Servicios
            $query_servicios = $dbo->query("Select * from ServicioMaestro Where Publicar = 'S' Order by Nombre");
            while ($r = $dbo->object($query_servicios)) {
                $nombre_campo_id_servicio = "IDServicioMaestro" . $r->IDServicioMaestro;
                $nombre_campo_orden_servicio = "OrdenServicio" . $r->IDServicioMaestro;
                $nombre_campo_titulo_servicio = "TituloServicio" . $r->IDServicioMaestro;
                if (!empty($frm[$nombre_campo_id_servicio])):
                    $activo_servicio = "S";
                else:
                    $activo_servicio = "N";
                endif;

                $id_servicio_maestro = $dbo->getFields("ServicioClub", "IDServicioMaestro", "IDServicioMaestro = '" . $r->IDServicioMaestro . "' and IDClub = '" . SIMNet::reqInt("id") . "'");
                if (empty($id_servicio_maestro)):
                    $sql_inserta_servicio = "Insert Into  ServicioClub (IDClub	, IDServicioMaestro, TituloServicio, Activo) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDServicioMaestro . "','" . $frm[$nombre_campo_titulo_servicio] . "','" . $activo_servicio . "')";
                    $dbo->query($sql_inserta_servicio);
                else:
                    $sql_actualiza_servicio = "Update ServicioClub Set Activo = '" . $activo_servicio . "', Orden = '" . $frm[$nombre_campo_orden_servicio] . "', TituloServicio = '" . $frm[$nombre_campo_titulo_servicio] . "' Where  IDClub = '" . SIMNet::reqInt("id") . "'	and IDServicioMaestro = '" . $r->IDServicioMaestro . "'";
                    $dbo->query($sql_actualiza_servicio);
                endif;

                //Crear la configuracion inicial
                $id_servicio_conf = $dbo->getFields("Servicio", "IDServicioMaestro", "IDServicioMaestro = '" . $r->IDServicioMaestro . "' and IDClub = '" . SIMNet::reqInt("id") . "'");
                if (empty($id_servicio_conf)):
                    $sql_inserta_servicio_conf = "Insert Into  Servicio (IDClub, IDServicioMaestro, Publicar) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDServicioMaestro . "','" . $activo . "')";
                    $dbo->query($sql_inserta_servicio_conf);
                endif;

            }

            //Actualizo Modulos
            $query_modulos = $dbo->query("Select * from Modulo Where Publicar = 'S' Order by Nombre");
            while ($r = $dbo->object($query_modulos)) {
                $nombre_campo_id = "IDModulo" . $r->IDModulo;
                $nombre_campo_titulo = "Titulo" . $r->IDModulo;
                $nombre_campo_titulo_lat = "TituloLateral" . $r->IDModulo;
                $nombre_campo_orden = "Orden" . $r->IDModulo;
                $nombre_campo_icono = "Icono" . $r->IDModulo;
                $nombre_campo_icono_actual = "ImagenOriginal" . $r->IDModulo;
                $nombre_campo_icono_lateral = "IconoLateral" . $r->IDModulo;
                $nombre_campo_icono_actual_lateral = "ImagenOriginalLateral" . $r->IDModulo;
                $nombre_campo_ubicacion = "UbicacionModulo" . $r->IDModulo;
                $ubicacion_modulo = "";

                if (count($frm[$nombre_campo_ubicacion] > 0)):
                    $ubicacion_modulo = implode("|", $frm[$nombre_campo_ubicacion]);
                endif;

                if (!empty($frm[$nombre_campo_id])):
                    $activo = "S";
                else:
                    $activo = "N";
                endif;

                if (!empty($_FILES[$nombre_campo_icono]["name"])):
                    $files = SIMFile::upload($_FILES[$nombre_campo_icono], MODULO_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES[$nombre_campo_icono]["name"])) {
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    }

                    $frm[$nombre_campo_icono] = $files[0]["innername"];
                else:
                    $frm[$nombre_campo_icono] = $frm[$nombre_campo_icono_actual];
                endif;

                if (!empty($_FILES[$nombre_campo_icono_lateral]["name"])):
                    $files = SIMFile::upload($_FILES[$nombre_campo_icono_lateral], MODULO_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES[$nombre_campo_icono_lateral]["name"])) {
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    }

                    $frm[$nombre_campo_icono_lateral] = $files[0]["innername"];
                else:
                    $frm[$nombre_campo_icono_lateral] = $frm[$nombre_campo_icono_actual_lateral];
                endif;

                $id_modulo = $dbo->getFields("ClubModulo", "IDModulo", "IDModulo = '" . $r->IDModulo . "' and IDClub = '" . SIMNet::reqInt("id") . "'");
                if (empty($id_modulo)):
                    $sql_inserta_modulo = "Insert Into  ClubModulo (IDClub	, IDModulo, Titulo,TituloLateral, Orden, Icono, IconoLateral, Ubicacion, Activo) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDModulo . "','" . $frm[$nombre_campo_titulo] . "','" . $frm[$nombre_campo_titulo_lat] . "','" . $frm[$nombre_campo_orden] . "','" . $frm[$nombre_campo_icono] . "','" . $frm[$nombre_campo_icono_lateral] . "','" . $ubicacion_modulo . "','" . $activo . "')";
                    $dbo->query($sql_inserta_modulo);
                else:
                    $sql_actualiza_modulo = "Update ClubModulo Set Activo = '" . $activo . "', Titulo = '" . $frm[$nombre_campo_titulo] . "', TituloLateral = '" . $frm[$nombre_campo_titulo_lat] . "',  Orden = '" . $frm[$nombre_campo_orden] . "', Icono = '" . $frm[$nombre_campo_icono] . "', IconoLateral = '" . $frm[$nombre_campo_icono_lateral] . "', Ubicacion = '" . $ubicacion_modulo . "' Where  IDClub = '" . SIMNet::reqInt("id") . "'	and IDModulo = '" . $r->IDModulo . "'";
                    $dbo->query($sql_actualiza_modulo);
                endif;
            }

            //Actualizo Iconos de tipo de archivos
            $query_tipoarchivos = $dbo->query("Select * from TipoArchivo Where Publicar = 'S' Order by Nombre");
            while ($r = $dbo->object($query_tipoarchivos)) {
                $nombre_campo_id = "IDTipoArchivo" . $r->IDTipoArchivo;
                $nombre_campo_icono = "IconoTipoArchivo" . $r->IDTipoArchivo;
                $nombre_campo_icono_actual = "ImagenOriginalTipoArchivo" . $r->IDTipoArchivo;
                $nombre_campo_titulo = "NombreTipoArchivo" . $r->IDTipoArchivo;

                if (!empty($_FILES[$nombre_campo_icono]["name"])):
                    $files = SIMFile::upload($_FILES[$nombre_campo_icono], CLIENTE_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES[$nombre_campo_icono]["name"])) {
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    }

                    $frm[$nombre_campo_icono] = $files[0]["innername"];
                else:
                    $frm[$nombre_campo_icono] = $frm[$nombre_campo_icono_actual];
                endif;

                if (!empty($frm[$nombre_campo_id])):
                    $activo = "S";
                else:
                    $activo = "N";
                endif;

                $id_tipoarchivo = $dbo->getFields("ClubTipoArchivo", "IDTipoArchivo", "IDTipoArchivo = '" . $r->IDTipoArchivo . "' and IDClub = '" . SIMNet::reqInt("id") . "'");
                if (empty($id_tipoarchivo)):
                    $sql_inserta_tipoarchivo = "Insert Into  ClubTipoArchivo (IDClub	, IDTipoArchivo, Icono, NombreTipoArchivo, Activo) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDTipoArchivo . "','" . $frm[$nombre_campo_icono] . "','" . $frm[$nombre_campo_titulo] . "','" . $activo . "')";
                    //$dbo->query($sql_inserta_tipoarchivo);
                else:
                    $sql_actualiza_tipoarchivo = "Update ClubTipoArchivo Set  Icono = '" . $frm[$nombre_campo_icono] . "',NombreTipoArchivo='" . $frm[$nombre_campo_titulo] . "', Activo = '" . $activo . "' Where  IDClub = '" . SIMNet::reqInt("id") . "'	and IDTipoArchivo = '" . $r->IDTipoArchivo . "'";
                    //$dbo->query($sql_actualiza_tipoarchivo);
                endif;
            }

            $frm = $dbo->fetchById($table, $key, $id, "array");

            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));

        } else {
            exit;
        }

        break;

    case "delfoto":
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $id = $_GET['id'];
        $filedelete = SERVICIO_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE $table SET $campo = '' WHERE $key = $id   LIMIT 1 ;");
        SIMHTML::jsAlert("Imagen Eliminada Correctamente");
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        break;

    case "delfotomodulo":
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $id_club_modulo = $_GET['idclubmodulo'];
        $filedelete = MODULO_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE ClubModulo SET $campo = '' WHERE IDClubModulo = $id_club_modulo   LIMIT 1 ;");
        SIMHTML::jsAlert("Imagen Eliminada Correctamente");
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        break;

    case "delfotoarchivo":
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $id_tipoarchivo = $_GET['idtipoarchivo'];
        $filedelete = CLIENTE_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE ClubTipoArchivo SET $campo = '' WHERE IDClub = '" . SIMNet::reqInt("id") . "' and IDTipoArchivo = '" . $id_tipoarchivo . "'  LIMIT 1 ;");
        SIMHTML::jsAlert("Imagen Eliminada Correctamente");
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));
        break;

    case "InsertarTipoModalidadEsqui":
        $frm = SIMUtil::varsLOG($_POST);
        $id = $dbo->insert($frm, "TipoModalidadEsqui", "IDTipoModalidadEsqui");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=modalidad&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificaTipoModalidadEsqui":
        $frm = SIMUtil::varsLOG($_POST);
        $dbo->update($frm, "TipoModalidadEsqui", "IDTipoModalidadEsqui", $frm[IDTipoModalidadEsqui]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=modalidad&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaTipoModalidadEsqui":
        $id = $dbo->query("DELETE FROM TipoModalidadEsqui WHERE IDTipoModalidadEsqui   = '" . $_GET[IDTipoModalidadEsqui] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=modalidad&id=" . $_GET["id"]);
        exit;
        break;

    case "InsertarCampoDirectorioClub":
        $frm = SIMUtil::varsLOG($_POST);
        $id = $dbo->insert($frm, "CampoDirectorioClub", "IDCampoDirectorioClub");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposdirectorioclub&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificarCampoDirectorioClub":
        $frm = SIMUtil::varsLOG($_POST);
        $dbo->update($frm, "CampoDirectorioClub", "IDCampoDirectorioClub", $frm[IDCampoDirectorioClub]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposdirectorioclub&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaCampoDirectorioClub":
        $id = $dbo->query("DELETE FROM CampoDirectorioClub WHERE IDCampoDirectorioClub   = '" . $_GET[IDCampoDirectorioClub] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposdirectorioclub&id=" . $_GET["id"]);
        exit;
        break;

    case "InsertarCampoDirectorioSocio":
        $frm = SIMUtil::varsLOG($_POST);
        $id = $dbo->insert($frm, "CampoDirectorioSocio", "IDCampoDirectorioSocio");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposdirectoriosocio&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificarCampoDirectorioSocio":
        $frm = SIMUtil::varsLOG($_POST);
        $dbo->update($frm, "CampoDirectorioSocio", "IDCampoDirectorioSocio", $frm[IDCampoDirectorioSocio]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposdirectoriosocio&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaCampoDirectorioSocio":
        $id = $dbo->query("DELETE FROM CampoDirectorioSocio WHERE IDCampoDirectorioSocio   = '" . $_GET[IDCampoDirectorioSocio] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposdirectoriosocio&id=" . $_GET["id"]);
        exit;
        break;

    //Modulo Parametro accesos
    case "ModificaParametroAcceso":
        // Si existe lo inserto de lo contrario solo lo modifico
        $frm = SIMUtil::varsLOG($_POST);

        $files = SIMFile::upload($_FILES["IconoFamiliar"], CLUB_DIR, "IMAGE");
        if (empty($files) && !empty($_FILES["IconoFamiliar"]["name"])) {
            SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
        }

        $frm["IconoFamiliar"] = $files[0]["innername"];

        $files = SIMFile::upload($_FILES["IconoIndividual"], CLUB_DIR, "IMAGE");
        if (empty($files) && !empty($_FILES["IconoIndividual"]["name"])) {
            SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
        }

        $frm["IconoIndividual"] = $files[0]["innername"];

        if (count($frm["TipoInvitado"]) > 0):
            $frm["TipoInvitado"] = implode("|", $frm["TipoInvitado"]);
        else:
            $frm["TipoInvitado"] = "";
        endif;

        if (count($frm["TipoAutorizacion"]) > 0):
            $frm["TipoAutorizacion"] = implode("|", $frm["TipoAutorizacion"]);
        else:
            $frm["TipoAutorizacion"] = "";
        endif;

        if (empty($frm["GrupoFamiliar"])) {
            $frm["GrupoFamiliar"] = "";
        }

        if (empty($frm["Invitado"])) {
            $frm["Invitado"] = "";
        }

        $id_parametro_acceso = $dbo->getFields("ParametroAcceso", "IDParametroAcceso", "IDClub = '" . $frm[IDClub] . "'");
        if (empty($id_parametro_acceso)):
            $id = $dbo->insert($frm, "ParametroAcceso", "IDParametroAcceso");
        else:
            $dbo->update($frm, "ParametroAcceso", "IDParametroAcceso", $id_parametro_acceso);
        endif;
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=accesos&id=" . $frm[IDClub]);
        exit;
        break;

        //Modulo Parametro accesos
        case "ModificaConfiguracionNoticias":
            // Si existe lo inserto de lo contrario solo lo modifico
            $frm = SIMUtil::varsLOG($_POST);

            $files = SIMFile::upload($_FILES["IconoLikeNoticias"], CLUB_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["IconoLikeNoticias"]["name"])) {
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
            }

            $frm["IconoLikeNoticias"] = $files[0]["innername"];

            $files = SIMFile::upload($_FILES["IconoUnLikeNoticias"], CLUB_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["IconoUnLikeNoticias"]["name"])) {
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
            }

            $frm["IconoUnLikeNoticias"] = $files[0]["innername"];

            $files = SIMFile::upload($_FILES["ImagenComentarios"], CLUB_DIR, "IMAGE");
            if (empty($files) && !empty($_FILES["ImagenComentarios"]["name"])) {
                SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
            }

            $frm["ImagenComentarios"] = $files[0]["innername"];


            $id_parametro_not = $dbo->getFields("ConfiguracionNoticias", "IDConfiguracionNoticias", "IDClub = '" . $frm[IDClub] . "'");
            if (empty($id_parametro_not)):
                $id = $dbo->insert($frm, "ConfiguracionNoticias", "IDConfiguracionNoticias");
            else:
                $dbo->update($frm, "ConfiguracionNoticias", "IDConfiguracionNoticias", $id_parametro_not);
            endif;
            SIMHTML::jsAlert("Modificacion Exitoso");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=noticias&id=" . $frm[IDClub]);
            exit;
            break;

    case "delfotoacceso":
        $foto = $_GET['foto'];
        $campo = $_GET['campo'];
        $filedelete = CLUB_DIR . $foto;
        unlink($filedelete);
        $dbo->query("UPDATE ParametroAcceso SET $campo = '' WHERE IDClub = '" . SIMNet::reqInt("id") . "' LIMIT 1 ;");
        SIMHTML::jsAlert("Imagen Eliminada Correctamente");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=accesos&id=" . SIMNet::reqInt("id"));
        break;

    case "InsertarRegla":
        $frm = SIMUtil::varsLOG($_POST);

        if (count($frm["IDCategoria"]) > 0):
            $categorias = implode("|", $frm["IDCategoria"]);
            $frm["IDCategoria"] = $categorias;
        else:
            $frm["IDCategoria"] = "";
        endif;

        if (count($frm["IDParentesco"]) > 0):
            $parentesco = implode("|", $frm["IDParentesco"]);
            $frm["IDParentesco"] = $parentesco;
        else:
            $frm["IDParentesco"] = "";
        endif;

        $id = $dbo->insert($frm, "Regla", "IDRegla");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=invitaciones&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificaRegla":
        $frm = SIMUtil::varsLOG($_POST);

        if (count($frm["IDCategoria"]) > 0):
            $categorias = "|" . implode("|", $frm["IDCategoria"]) . "|";
            $frm["IDCategoria"] = $categorias;
        else:
            $frm["IDCategoria"] = "";
        endif;

        if (count($frm["IDParentesco"]) > 0):
            $parentesco = "|" . implode("|", $frm["IDParentesco"]) . "|";
            $frm["IDParentesco"] = $parentesco;
        else:
            $frm["IDParentesco"] = "";
        endif;

        $dbo->update($frm, "Regla", "IDRegla", $frm[IDRegla]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=invitaciones&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaRegla":
        $id = $dbo->query("DELETE FROM Regla WHERE IDRegla   = '" . $_GET[IDRegla] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=invitaciones&id=" . $_GET["id"]);
        exit;
        break;

    case "InsertarModuloTipoSocio":
        $frm = SIMUtil::varsLOG($_POST);

        if (count($frm["IDModulo"]) > 0):
            $clubmodulo = implode("|", $frm["IDModulo"]);
            $frm["IDModulo"] = $clubmodulo;
        else:
            $frm["IDModulo"] = "";
        endif;

        if (count($frm["IDServicioMaestro"]) > 0):
            $servicios = implode("|", $frm["IDServicioMaestro"]);
            $frm["IDServicioMaestro"] = $servicios;
        else:
            $frm["IDServicioMaestro"] = "";
        endif;

        $id = $dbo->insert($frm, "TipoSocioModulo", "IDTipoSocioModulo");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=tiposociomodulo&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificaModuloTipoSocio":
        $frm = SIMUtil::varsLOG($_POST);

        if (count($frm["IDModulo"]) > 0):
            $clubmodulo = implode("|", $frm["IDModulo"]);
            $frm["IDModulo"] = $clubmodulo;
        else:
            $frm["IDModulo"] = "";
        endif;

        if (count($frm["IDServicioMaestro"]) > 0):
            $servicios = implode("|", $frm["IDServicioMaestro"]);
            $frm["IDServicioMaestro"] = $servicios;
        else:
            $frm["IDServicioMaestro"] = "";
        endif;

        $dbo->update($frm, "TipoSocioModulo", "IDTipoSocioModulo", $frm["IDTipoSocioModulo"]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=tiposociomodulo&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaModuloTipoSocio":
        $id = $dbo->query("DELETE FROM TipoSocioModulo WHERE IDTipoSocioModulo   = '" . $_GET[IDTipoSocioModulo] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=tiposociomodulo&id=" . $_GET["id"]);
        exit;
        break;

    //Permisos Socios
    case "InsertarPermisoSocioModulo":
        $frm = SIMUtil::varsLOG($_POST);

        if (count($frm["IDModulo"]) > 0):
            $clubmodulo = implode("|", $frm["IDModulo"]);
            $frm["IDModulo"] = $clubmodulo;
        else:
            $frm["IDModulo"] = "";
        endif;

        if (count($frm["IDServicioMaestro"]) > 0):
            $servicios = implode("|", $frm["IDServicioMaestro"]);
            $frm["IDServicioMaestro"] = $servicios;
        else:
            $frm["IDServicioMaestro"] = "";
        endif;

        $id = $dbo->insert($frm, "PermisoSocioModulo", "IDPermisoSocioModulo");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=permisosociomodulo&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificaPermisoSocioModulo":
        $frm = SIMUtil::varsLOG($_POST);

        if (count($frm["IDModulo"]) > 0):
            $clubmodulo = implode("|", $frm["IDModulo"]);
            $frm["IDModulo"] = $clubmodulo;
        else:
            $frm["IDModulo"] = "";
        endif;

        if (count($frm["IDServicioMaestro"]) > 0):
            $servicios = implode("|", $frm["IDServicioMaestro"]);
            $frm["IDServicioMaestro"] = $servicios;
        else:
            $frm["IDServicioMaestro"] = "";
        endif;

        $dbo->update($frm, "PermisoSocioModulo", "IDPermisoSocioModulo", $frm["IDPermisoSocioModulo"]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=permisosociomodulo&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaPermisoSocioModulo":
        $id = $dbo->query("DELETE FROM PermisoSocioModulo WHERE IDPermisoSocioModulo   = '" . $_GET[IDPermisoSocioModulo] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=permisosociomodulo&id=" . $_GET["id"]);
        exit;
        break;
    //Fin permisos

    //Sanciones
    case "InsertarSancion":
        $frm = SIMUtil::varsLOG($_POST);

        $id = $dbo->insert($frm, "Sancion", "IDSancion");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=sanciones&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificaSancion":
        $frm = SIMUtil::varsLOG($_POST);

        $dbo->update($frm, "Sancion", "IDSancion", $frm[IDSancion]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=sanciones&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaSancion":
        $id = $dbo->query("DELETE FROM Sancion WHERE IDSancion   = '" . $_GET[IDSancion] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=sanciones&id=" . $_GET["id"]);
        exit;
        break;

    //Fin saciones

    case "ModificaAppEmpleado":

        if (!SIMNotify::capture(SIMUtil::valida($_POST, $array_valida), "error")) {
            //los campos al final de las tablas
            $frm = SIMUtil::varsLOG($_POST);

            //UPLOAD de imagenes
            if (isset($_FILES)) {
                $files = SIMFile::upload($_FILES["Foto"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["Foto"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["Foto"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["FotoDiseno1"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoDiseno1"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["FotoDiseno1"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["FotoLogoApp"], CLUB_DIR, "IMAGE");
                if (empty($files) && !empty($_FILES["FotoLogoApp"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["FotoLogoApp"] = $files[0]["innername"];

                $files = SIMFile::upload($_FILES["ArchivoTerminos"], CLUB_DIR, "DOC");
                if (empty($files) && !empty($_FILES["ArchivoTerminos"]["name"])) {
                    SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                }

                $frm["ArchivoTerminos"] = $files[0]["innername"];

            } //end if

            $id = $dbo->update($frm, "AppEmpleado", "IDAppEmpleado", $frm["IDAppEmpleado"]);

            //Actualizo Modulos
            $query_modulos = $dbo->query("Select * from Modulo Where Publicar = 'S' Order by Nombre");
            while ($r = $dbo->object($query_modulos)) {
                $nombre_campo_id = "IDModulo" . $r->IDModulo;
                $nombre_campo_titulo = "Titulo" . $r->IDModulo;
                $nombre_campo_titulo_lat = "TituloLateral" . $r->IDModulo;
                $nombre_campo_orden = "Orden" . $r->IDModulo;
                $nombre_campo_icono = "Icono" . $r->IDModulo;
                $nombre_campo_icono_actual = "ImagenOriginal" . $r->IDModulo;
                $nombre_campo_ubicacion = "UbicacionModulo" . $r->IDModulo;
                $ubicacion_modulo = "";

                if (count($frm[$nombre_campo_ubicacion] > 0)):
                    $ubicacion_modulo = implode("|", $frm[$nombre_campo_ubicacion]);
                endif;

                if (!empty($frm[$nombre_campo_id])):
                    $activo = "S";
                else:
                    $activo = "N";
                endif;

                if (!empty($_FILES[$nombre_campo_icono]["name"])):
                    $files = SIMFile::upload($_FILES[$nombre_campo_icono], MODULO_DIR, "IMAGE");
                    if (empty($files) && !empty($_FILES[$nombre_campo_icono]["name"])) {
                        SIMNotify::capture("Ha ocurrido un error durante la carga de la imagen. Verifique que la imagen no contenga errores y que el tipo de archivo sea permitido.", "error");
                    }

                    $frm[$nombre_campo_icono] = $files[0]["innername"];
                else:
                    $frm[$nombre_campo_icono] = $frm[$nombre_campo_icono_actual];
                endif;

                $id_modulo = $dbo->getFields("AppEmpleadoModulo", "IDModulo", "IDModulo = '" . $r->IDModulo . "' and IDClub = '" . SIMNet::reqInt("id") . "'");
                if (empty($id_modulo)):
                    $sql_inserta_modulo = "Insert Into  AppEmpleadoModulo (IDClub	, IDModulo, Titulo, TituloLateral, Orden, Icono, Ubicacion, Activo) Values ('" . SIMNet::reqInt("id") . "','" . $r->IDModulo . "','" . $frm[$nombre_campo_titulo] . "','" . $frm[$nombre_campo_titulo_lat] . "','" . $frm[$nombre_campo_orden] . "','" . $frm[$nombre_campo_icono] . "','" . $ubicacion_modulo . "','" . $activo . "')";
                    $dbo->query($sql_inserta_modulo);
                else:
                    $sql_actualiza_modulo = "Update AppEmpleadoModulo Set Activo = '" . $activo . "', Titulo = '" . $frm[$nombre_campo_titulo] . "', TituloLateral='" . $frm[$nombre_campo_titulo_lat] . "', Orden = '" . $frm[$nombre_campo_orden] . "', Icono = '" . $frm[$nombre_campo_icono] . "', Ubicacion = '" . $ubicacion_modulo . "' Where  IDClub = '" . SIMNet::reqInt("id") . "'	and IDModulo = '" . $r->IDModulo . "'";
                    $dbo->query($sql_actualiza_modulo);
                endif;
            }

            SIMHTML::jsAlert("Registro Guardado Correctamente");
            SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=appempleados&id=" . $frm[IDClub]);
            //print_form( $frm , "update" ,  "Realizar Cambios" );
        } else {
            exit;
        }

        break;

    case "updatesubmodulos":
        $frm = SIMUtil::varsLOG($_POST);
        $r_modulo = &$dbo->all("ClubModulo", "IDClub = '" . SIMNet::reqInt("id") . "' and Activo = 'S'");
        $delete_submodulo = "Delete From SubModulo Where IDClub = '" . SIMNet::reqInt("id") . "'";
        $dbo->query($delete_submodulo);
        while ($r = $dbo->object($r_modulo)) {
            $modulo_configurado = "N";
            $nombre_campo_valores = "Servicio" . $r->IDModulo;
            $nombre_campo_valores_Not = "SeccionNoticia" . $r->IDModulo;
            $nombre_campo_valores_Eve = "SeccionEvento" . $r->IDModulo;
            $nombre_campo_valores_Gal = "SeccionGaleria" . $r->IDModulo;
            $nombre_campo_valores_Arch = "TipoArchivo" . $r->IDModulo;
            $nombre_campo_valores_Mod = "ModuloHijo" . $r->IDModulo;
            $nombre_campo_verreservas = "MostrarMisReservas" . $r->IDModulo;

            unset($valores_servicio);
            unset($valores_secc_noticia);
            unset($valores_secc_evento);
            unset($valores_secc_galeria);
            unset($valores_tipo_archivo);
            unset($valores_modulo_hijo);

            //Servicios Reservas
            if (!empty($frm[$nombre_campo_valores]) && count($frm[$nombre_campo_valores]) > 0):
                $valores_servicio = implode("|", $frm[$nombre_campo_valores]);
                $modulo_configurado = "S";
            endif;
            //Seccion Noticias
            if (!empty($frm[$nombre_campo_valores_Not]) && count($frm[$nombre_campo_valores_Not]) > 0):
                $valores_secc_noticia = implode("|", $frm[$nombre_campo_valores_Not]);
                $modulo_configurado = "S";
            endif;
            //Seccion Evento
            if (!empty($frm[$nombre_campo_valores_Eve]) && count($frm[$nombre_campo_valores_Eve]) > 0):
                $valores_secc_evento = implode("|", $frm[$nombre_campo_valores_Eve]);
                $modulo_configurado = "S";
            endif;
            //Seccion Galeria
            if (!empty($frm[$nombre_campo_valores_Gal]) && count($frm[$nombre_campo_valores_Gal]) > 0):
                $valores_secc_galeria = implode("|", $frm[$nombre_campo_valores_Gal]);
                $modulo_configurado = "S";
            endif;
            //Tipo Archivo
            if (!empty($frm[$nombre_campo_valores_Arch]) && count($frm[$nombre_campo_valores_Arch]) > 0):
                $valores_tipo_archivo = implode("|", $frm[$nombre_campo_valores_Arch]);
                $modulo_configurado = "S";
            endif;
            //Modulos hijos
            if (!empty($frm[$nombre_campo_valores_Mod]) && count($frm[$nombre_campo_valores_Mod]) > 0):
                $valores_modulo_hijo = implode("|", $frm[$nombre_campo_valores_Mod]);
                $modulo_configurado = "S";
            endif;

            if ($modulo_configurado == "S"):
                $sql_submodulo = "Insert Into SubModulo (IDClub, IDModulo, IDServicio, IDSeccionNoticia, IDSeccionEvento, IDSeccionGaleria, IDTipoArchivo, IDModuloHijo,MostrarMisReservas)
											 Values ('" . SIMNet::reqInt("id") . "','" . $r->IDModulo . "','" . $valores_servicio . "','" . $valores_secc_noticia . "','" . $valores_secc_evento . "','" . $valores_secc_galeria . "','" . $valores_tipo_archivo . "','" . $valores_modulo_hijo . "','" . $frm[$nombre_campo_verreservas] . "')";
                $dbo->query($sql_submodulo);
            endif;
        }
        SIMHTML::jsAlert("Registro guardado");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=submodulos&id=" . SIMNet::reqInt("id"));
        break;

    case "UpdateTipoSocio";
        $frm = SIMUtil::varsLOG($_POST);

        $sql_borra = "DELETE FROM ClubTipoSocio WHERE IDClub = '" . $frm["IDClub"] . "'";
        $dbo->query($sql_borra);

        $sql_borra = "DELETE FROM ClubCategoria WHERE IDClub = '" . $frm["IDClub"] . "'";
        $dbo->query($sql_borra);

        $sql_borra = "DELETE FROM ClubParentesco WHERE IDClub = '" . $frm["IDClub"] . "'";
        $dbo->query($sql_borra);

        $sql_borra = "DELETE FROM ClubCampoEditarSocio WHERE IDClub = '" . $frm["IDClub"] . "'";
        $dbo->query($sql_borra);

        //Tipo Socio
        if (!empty($frm["TipoSocio"]) && count($frm["TipoSocio"]) > 0) {
            foreach ($frm["TipoSocio"] as $IDTipoSocio) {
                $sql_tipo = "INSERT INTO ClubTipoSocio (IDClub, IDTipoSocio) VALUES ('" . $frm["IDClub"] . "','" . $IDTipoSocio . "')";
                $dbo->query($sql_tipo);
            }
        }

        //Categoria
        if (!empty($frm["Categoria"]) && count($frm["Categoria"]) > 0) {
            foreach ($frm["Categoria"] as $IDCategoria) {
                $sql_tipo = "INSERT INTO ClubCategoria (IDClub, IDCategoria) VALUES ('" . $frm["IDClub"] . "','" . $IDCategoria . "')";
                $dbo->query($sql_tipo);
            }
        }

        //Parentesco
        if (!empty($frm["Parentesco"]) && count($frm["Parentesco"]) > 0) {
            foreach ($frm["Parentesco"] as $IDParentesco) {
                $sql_tipo = "INSERT INTO ClubParentesco (IDClub, IDParentesco) VALUES ('" . $frm["IDClub"] . "','" . $IDParentesco . "')";
                $dbo->query($sql_tipo);
            }
        }

        //Campos permitidos para editar
        if (!empty($frm["CampoEditarSocio"]) && count($frm["CampoEditarSocio"]) > 0) {
            foreach ($frm["CampoEditarSocio"] as $IDCampoEditarSocio) {
                $sql_tipo = "INSERT INTO ClubCampoEditarSocio (IDClub, IDCampoEditarSocio) VALUES ('" . $frm["IDClub"] . "','" . $IDCampoEditarSocio . "')";
                $dbo->query($sql_tipo);
            }
        }

        //Campos carne
        if (!empty($frm["CampoCarne"]) && count($frm["CampoCarne"]) > 0) {
            foreach ($frm["CampoCarne"] as $IDCampoCarne) {
                $array_campos[] = $IDCampoCarne;
            }
            $id_campo_carne = implode("|||", $array_campos);
            $sql_campo_carne = "UPDATE Club Set CampoCarne = '" . $id_campo_carne . "' WHERE IDClub = '" . $frm["IDClub"] . "'";
            $dbo->query($sql_campo_carne);
        }

        SIMHTML::jsAlert("Registro guardado");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=tiposocioclub&id=" . $frm[IDClub]);
        break;

    case "InsertarPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $id = $dbo->insert($frm, "PreguntaAcceso", "IDPreguntaAcceso");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposacceso&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificaPregunta":
        $frm = SIMUtil::varsLOG($_POST);
        $dbo->update($frm, "PreguntaAcceso", "IDPreguntaAcceso", $frm["IDPreguntaAcceso"]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposacceso&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaPregunta":
        $id = $dbo->query("DELETE FROM PreguntaAcceso WHERE IDPreguntaAcceso   = '" . $_GET["IDPreguntaAcceso"] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposacceso&id=" . $_GET["id"]);
        exit;
        break;

    case "InsertCorreoAlertaAcceso":
        $frm = SIMUtil::varsLOG($_POST);
        $sql_correo = "UPDATE Club SET CorreoAlertaCampoAcceso='" . $frm["CorreoAlertaCampoAcceso"] . "' WHERE IDClub   = '" . $frm["IDClub"] . "' LIMIT 1";
        $dbo->query($sql_correo);
        SIMHTML::jsAlert("Correo agregado con exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposacceso&id=" . $frm["IDClub"]);
        exit;
        break;

    //Sanciones
    case "InsertarCampoEditarSocio":
        $frm = SIMUtil::varsLOG($_POST);

        $id = $dbo->insert($frm, "CampoEditarSocio", "IDCampoEditarSocio");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposperfil&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificarCampoEditarSocio":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $dbo->update($frm, "CampoEditarSocio", "IDCampoEditarSocio", $frm[IDCampoEditarSocio]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposperfil&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaCampoEditarSocio":
        $id = $dbo->query("DELETE FROM CampoEditarSocio WHERE IDCampoEditarSocio   = '" . $_GET[IDCampoEditarSocio] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposperfil&id=" . $_GET["id"]);
        exit;
        break;

    case "InsertarCampoEditarUsuario":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $id = $dbo->insert($frm, "CampoEditarUsuario", "IDCampoEditarUsuario");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposperfilfuncionario&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificarCampoEditarUsuario":
        $frm = SIMUtil::varsLOG($_POST);
        $frm["Valores"] = trim(preg_replace('/\s+/', ' ', $frm["Valores"]));
        $dbo->update($frm, "CampoEditarUsuario", "IDCampoEditarUsuario", $frm[IDCampoEditarUsuario]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposperfilfuncionario&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaCampoEditarUsuario":
        $id = $dbo->query("DELETE FROM CampoEditarUsuario WHERE IDCampoEditarUsuario   = '" . $_GET[IDCampoEditarUsuario] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposperfilfuncionario&id=" . $_GET["id"]);
        exit;
        break;

    //Fin saciones

    case "InsertarCampoRegistroContacto":
        $frm = SIMUtil::varsLOG($_POST);

        $id = $dbo->insert($frm, "CampoRegistroContacto", "IDCampoRegistroContacto");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposregistrocontacto&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificarCampoRegistroContacto":
        $frm = SIMUtil::varsLOG($_POST);

        $dbo->update($frm, "CampoRegistroContacto", "IDCampoRegistroContacto", $frm[IDCampoRegistroContacto]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposregistrocontacto&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaCampoRegistroContacto":
        $id = $dbo->query("DELETE FROM CampoRegistroContacto WHERE IDCampoRegistroContacto   = '" . $_GET[IDCampoRegistroContacto] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposregistrocontacto&id=" . $_GET["id"]);
        exit;
        break;

    case "InsertarCampoContactoExterno":
        $frm = SIMUtil::varsLOG($_POST);

        $id = $dbo->insert($frm, "CampoContactoExterno", "IDCampoContactoExterno");
        SIMHTML::jsAlert("Registro Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposcontactoexterno&id=" . $frm[IDClub]);
        exit;
        break;

    case "ModificarCampoContactoExterno":
        $frm = SIMUtil::varsLOG($_POST);

        $dbo->update($frm, "CampoContactoExterno", "IDCampoContactoExterno", $frm[IDCampoContactoExterno]);
        SIMHTML::jsAlert("Modificacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposcontactoexterno&id=" . $frm[IDClub]);
        exit;
        break;

    case "EliminaCampoContactoExterno":
        $id = $dbo->query("DELETE FROM CampoContactoExterno WHERE IDCampoContactoExterno   = '" . $_GET[IDCampoContactoExterno] . "' LIMIT 1");
        SIMHTML::jsAlert("Eliminacion Exitoso");
        SIMHTML::jsRedirect($script . ".php?action=edit&tabclub=parametros&tabparametro=camposcontactoexterno&id=" . $_GET["id"]);
        exit;
        break;

    case "enviarcontratos":

        SIMUtil::envia_correo_contratos(SIMNet::reqInt("id"));
        SIMHTML::jsAlert("Correo Enviado");
        SIMHTML::jsRedirect($script . ".php?action=edit&id=" . SIMNet::reqInt("id"));

    break;

    case "copiar":
        $IDClub = $_GET[id];
        $IDClubPadre = $_GET[clubpadre];

        //COPIO LA CONFIGURACIÓN GENERAL DEL CLUB 
        $ConfigClub = $dbo->fetchAll("Club","IDClub = $IDClub","array");
        $ConfigClub[IDClub] = "";
        $id = $dbo->insert($ConfigClub, $table, $key);
        echo $NuevoClub = $dbo->lastID();
        
        // COPIO LA SEGUNDA CONFIGURACIÓN DEL CLUB
        $ConfigClub2 = $dbo->fetchAll("ConfiguracionClub","IDClub = $IDClub","array");       

        if(!empty($ConfigClub2[IDConfiguracionClub])):
            $ConfigClub2[IDConfiguracionClub] = "";
            $id = $dbo->insert($ConfigClub2, "ConfiguracionClub", "IDConfiguracionClub");                           
        else:   
            $ConfigClub2[IDClub] =  $NuevoClub;
            $id = $dbo->insert($ConfigClub2, "ConfiguracionClub", "IDConfiguracionClub"); 
        endif;

        //COPIO TODA LA INFORMACIÓN DE LOS MODULOS
        $sqlModulos = "SELECT * FROM ClubModulo WHERE IDClub = $IDClub";
        $qryModulos = $dbo->query($sqlModulos);

        while($info = $dbo->fetchArray($qryModulos)):
            $info[IDClubModulo] = "";
            $info[IDClub] = $NuevoClub;
            $insert = $dbo->insert($info,"ClubModulo","IDClubModulo");
        endwhile;

        //COPIO TODA LA INFORMACIÓN DE LOS SERVICIO
        $sqlServicios = "SELECT * FROM ServicioClub WHERE IDClub = $IDClub";
        $qryServicios = $dbo->query($sqlServicios);

        while($info = $dbo->fetchArray($qryServicios)):            
            $info[IDClub] = $NuevoClub;
            $insert = $dbo->insert($info,"ServicioClub","IDServicioClub");
        endwhile;

        SIMHTML::jsAlert("Club copiado con exito!");
        SIMHTML::jsRedirect($script . ".php?id=$IDClubPadre&Tipo=Padre");

    break;

    case "search":
        $view = "views/" . $script . "/list.php";
        break;

    default:
        $view = "";

} // End switch

?>
