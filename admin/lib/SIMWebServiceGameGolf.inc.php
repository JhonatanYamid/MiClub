<?php

class SIMWebServiceGameGolf
{
    public function get_juegos_golf_courses($IDClub, $IDSocio, $IDUsuario, $Texto, $IDCampo = "")
    {

        $dbo = &SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response = array();

        $datos_club = $dbo->fetchAll("ConfiguracionClub", " IDClub = '" . $IDClub . "' ", "array");


        if (!empty($Texto)) :
            $Condicion = " AND (Nombre like '%" . $Texto . "%' or Descripcion like '%" . $Texto . "%' ) ";
        endif;

        if (!empty($IDCampo)) :
            $Condicion = " AND IDGameGolfCourse = '" . $IDCampo . "' ";
        endif;

        //Consulto favoritos           
        $sql_fav = "SELECT IDSocio,IDGameGolfCourse FROM GameGolfCourseFavorito WHERE IDSocio = '" . $IDSocio . "' ";
        $r_fav = $dbo2->query($sql_fav);
        while ($row_fav = $dbo2->fetchArray($r_fav)) {
            $array_fav[] = $row_fav["IDGameGolfCourse"];
        }

        if (count($array_fav) > 0) {
            $id_course = implode(",", $array_fav);
            $CondicionOrden = " ORDER BY FIELD (`IDGameGolfCourse`," . $id_course . ") DESC, Nombre ASC; ";
        } else {
            $CondicionOrden = " ORDER BY Nombre; ";
        }

        $SQLDatos = "SELECT IDGameGolfCourse,Nombre, Descripcion, TextoHtml, Foto FROM GameGolfCourse  WHERE IDPais = '" . $datos_club["IDPais"] . "'  $Condicion AND Publicar = 'S' " . $CondicionOrden;
        $QRYDatos = $dbo2->query($SQLDatos);
        while ($Datos = $dbo2->fetchArray($QRYDatos)) :
            $response_marcas = array();
            $InfoResponse["IDGolfCourse"] = $Datos["IDGameGolfCourse"];
            $InfoResponse["Nombre"] = $Datos["Nombre"];
            if (in_array($Datos["IDGameGolfCourse"], $array_fav)) {
                $Favorito = "S";
            } else {
                $Favorito = "N";
            }
            $InfoResponse["Favorito"] = $Favorito;
            $InfoResponse["Descripcion"] = $Datos["Descripcion"];
            $InfoResponse["TextoHtml"] = $Datos["TextoHtml"];
            $InfoResponse["Foto"] = $Datos["Foto"];

            //Consulto marcas del campo
            $sql_marcas = "SELECT IDGameGolfMarca,Nombre FROM GameGolfMarca WHERE IDGameGolfCourse = '" . $Datos["IDGameGolfCourse"] . "' and Publicar = 'S' ";
            $r_marcas = $dbo2->query($sql_marcas);
            while ($row_marca = $dbo2->fetchArray($r_marcas)) {
                $array_hoyos = array();
                $array_marca["IDMarca"] = $row_marca["IDGameGolfMarca"];
                $array_marca["Nombre"] = $row_marca["Nombre"];

                $findme   = '[10-18]';
                $pos = strpos($row_marca["Nombre"], $findme);
                if ($pos === false) {
                    $findme   = '[1-9]';
                    $pos = strpos($row_marca["Nombre"], $findme);
                    if ($pos === false) {
                        $array_hoyos[] = "1-18";
                        $array_hoyos[] = "1-9";
                        $array_hoyos[] = "10-18";
                    } else {
                        $array_hoyos[] = "1-9";
                    }
                } else {
                    $array_hoyos[] = "10-18";
                }

                $array_marca["ConfiguracionHoyos"] = $array_hoyos;
                array_push($response_marcas, $array_marca);
            }
            $InfoResponse["Marcas"] = $response_marcas;



            array_push($response, $InfoResponse);
        endwhile;
        $respuesta[message] = "Datos encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;
        return $respuesta;
    }

    public function get_juegos_golf_jugadores($IDClub, $IDSocio, $IDUsuario, $Texto)
    {

        $dbo = SIMDB::get();
        $dbo2 = SIMDB2::get();
        $response = array();
        if (!empty($Texto)) :
            $array_busqueda = explode(" ", $Texto);
            if (count($array_busqueda) > 1) {
                foreach ($array_busqueda as $dato) {
                    $array_condicion[] = " (Nombre like '%" . $dato . "%' or Apellido like '%" . $dato . "%') ";
                }
                if (count($array_condicion) > 0) {
                    $Condicion = " and " . implode(" and ", $array_condicion);
                }
            } else {
                $Condicion = " AND (Nombre like '%" . $Texto . "%' or Apellido like '%" . $Texto . "%' or NumeroDocumento like '%" . $Texto . "%' or Accion like '%" . $Texto . "%' ) ";
            }
        endif;

        //Consulto favoritos
        $array_fav[] = $IDSocio;
        $sql_fav = "SELECT IDSocio,IDSocio2 FROM GameGolfSocioFavorito WHERE IDSocio = '" . $IDSocio . "' ";
        $r_fav = $dbo2->query($sql_fav);
        while ($row_fav = $dbo->fetchArray($r_fav)) {
            $array_fav[] = $row_fav["IDSocio2"];
        }


        if (empty($Condicion)) {
            if (count($array_fav) > 0) {
                $id_socio = implode(",", $array_fav);
            } else {
                $id_socio = $IDSocio;
            }
            $Condicion = " and IDSocio in (" . $id_socio . ") ";
        }



        $SQLDatos = "SELECT IDSocio,NumeroDocumento,Nombre,Apellido, Handicap, ClubPertenece,TipoSocio FROM Socio WHERE IDClub = $IDClub $Condicion AND IDEstadoSocio = 1 Order by Nombre";
        $QRYDatos = $dbo->query($SQLDatos);
        while ($Datos = $dbo->fetchArray($QRYDatos)) :
            $InfoResponse["IDJugador"] = $Datos["IDSocio"];
            $InfoResponse["Nombre"] = $Datos["Nombre"] . " " . $Datos["Apellido"];
            if ($Datos["TipoSocio"] == "Socio")
                $Externo = "N";
            else
                $Externo = "S";

            $InfoResponse["Externo"] = $Externo;
            $InfoResponse["Handicap"] = $Datos["Handicap"];
            $InfoResponse["NumeroDocumento"] = $Datos["NumeroDocumento"];

            if (in_array($Datos["IDSocio"], $array_fav)) {
                $Favorito = "S";
            } else {
                $Favorito = "N";
            }

            $InfoResponse["Favorito"] = $Favorito;
            $InfoResponse["NombreClub"] = $Datos["ClubPertenece"];
            array_push($response, $InfoResponse);
        endwhile;
        $respuesta[message] = "Datos encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;
        return $respuesta;
    }

    public function set_jugador_externo_juegos_golf($IDClub, $IDSocio, $IDUsuario, $NombreJugador, $NumeroDocumento, $Handicap, $Email, $Celular)
    {
        $dbo = &SIMDB::get();
        $response = array();
        if (!empty($NombreJugador) && !empty($Email) && (!empty($IDSocio) || !empty($IDUsuario))) {
            $IDSocioValida = $dbo->getFields("Socio", "IDSocio", "IDClub = $IDClub AND NumeroDocumento = '" . $NumeroDocumento . "' ");
            if ((int)$IDSocioValida <= 0) {
                $sql_inserta = "INSERT INTO Socio (IDClub,Accion,AccionPadre,Nombre,Apellido,NumeroDocumento,Email,Clave,CorreoElectronico,Celular,TipoSocio,Handicap,PermiteReservar,CambioClave,IDEstadoSocio,FechaNacimiento,NumeroInvitados,NumeroAccesos, Predio)
									Values ('" . $IDClub . "','" . $NumeroDocumento . "','" . $NumeroDocumento . "','" . $NombreJugador . "','" . $Apellido . "','" . $NumeroDocumento . "','" . $NumeroDocumento . "',sha1('" . $NumeroDocumento . "'),'" . $Email . "','" . $Celular . "','Externo','" . $Handicap . "','S','S','1','" . $FechaNacimiento . "','" . $NumeroInvitados . "','" . $NumeroInvitados . "','" . $Predio . "')";
                $dbo->query($sql_inserta);
                $IDJugador = $dbo->lastID();
                $array_datos["IDJugador"] = (string)$IDJugador;
                $array_datos["Nombre"] = $NombreJugador;
                $array_datos["Externo"] = "S";
                $array_datos["Handicap"] = $Handicap;
                $array_datos["NumeroDocumento"] = $NumeroDocumento;
                $array_datos["Favorito"] = "S";

                $respuesta[message] = "Externo agregado con exito";
                $respuesta[success] = true;
                $respuesta[response] = $array_datos;
            } else {
                $respuesta[message] = "GGC2. El jugador ya existe, por favor verifique";
                $respuesta[success] = false;
                $respuesta[response] = "";
            }
        } else {
            $respuesta[message] = "GGC1. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function set_favorito_juegos_golf_jugadores($IDClub, $IDSocio, $IDUsuario, $IDJugador, $Favorito)
    {
        $dbo2 = &SIMDB2::get();
        if (!empty($IDClub) && !empty($IDSocio) &&  !empty($IDJugador)) {

            //verifico que el socio exista y pertenezca al club


            if ($Favorito == "S" && (int) $IDJugador > 0) :
                $inserta_socio_favorito = $dbo2->query("Insert Into  GameGolfSocioFavorito  (IDSocio, IDSocio2) Values ('" . $IDSocio . "', '" . $IDJugador . "')");
            elseif ($Favorito == "N" && (int) $IDJugador > 0) :
                $delete_socio_favorito = $dbo2->query("Delete From GameGolfSocioFavorito Where IDSocio = '" . $IDSocio . "' and IDSocio2 = '" . $IDJugador . "'");
            endif;

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "8." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function set_favorito_juego_golf_course($IDClub, $IDSocio, $IDUsuario, $IDGolfCourse, $Favorito)
    {
        $dbo2 = &SIMDB2::get();
        if (!empty($IDClub) && !empty($IDSocio) &&  !empty($IDGolfCourse)) {

            //verifico que el socio exista y pertenezca al club


            if ($Favorito == "S" && (int) $IDGolfCourse > 0) :
                $dbo2->query("INSERT INTO  GameGolfCourseFavorito   (IDSocio, IDGameGolfCourse) Values ('" . $IDSocio . "', '" . $IDGolfCourse . "')");
            elseif ($Favorito == "N" && (int) $IDGolfCourse > 0) :
                $dbo2->query("DELETE FROM GameGolfCourseFavorito  Where IDSocio = '" . $IDSocio . "' and IDGameGolfCourse = '" . $IDGolfCourse . "'");
            endif;

            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Guardado', LANG);
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {
            $respuesta["message"] = "8." . SIMUtil::get_traduccion('', '', 'Atencionfaltanparametros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }

    public function get_jugador_socio_juegos_golf($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo = &SIMDB::get();
        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        if ($datos_socio["IDSocio"] > 0) :
            $InfoResponse[IDJugador] = $datos_socio[IDSocio];
            $InfoResponse[Nombre] = $datos_socio[Nombre] .  " " . $datos_socio[Apellido];
            if ($datos_socio["TipoSocio"] == "Socio")
                $Externo = "N";
            else
                $Externo = "S";

            $InfoResponse[Externo] = $Externo;
            $InfoResponse[Handicap] = $datos_socio[Handicap];
            $InfoResponse[NumeroDocumento] = $datos_socio[NumeroDocumento];
            $InfoResponse[Favorito] = "S";

            $respuesta[message] = "ENCONTRADOS";
            $respuesta[success] = true;
            $respuesta[response] = $InfoResponse;
        else :
            $respuesta[message] = "Jugador no existe";
            $respuesta[success] = false;
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function get_formatos_juegos_golf($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo2 = &SIMDB2::get();
        $response = array();
        $SQLDatos = "SELECT IDGameGolfCategoriaFormato, Nombre, NombreEng,  Descripcion,DescripcionEng, Publicar FROM  GameGolfCategoriaFormato   WHERE Publicar = 'S'";
        $QRYDatos = $dbo2->query($SQLDatos);
        while ($Datos = $dbo2->fetchArray($QRYDatos)) :
            $array_formato = array();
            $response_formato = array();
            $InfoResponse["IDCategoriaFormato"] = $Datos["IDGameGolfCategoriaFormato"];
            if (LANG == "en") {
                $Nombre = $Datos["NombreEng"];
                $Descripcion = $Datos["DescripcionEng"];
            } else {
                $Nombre = $Datos["Nombre"];
                $Descripcion = $Datos["Descripcion"];
            }
            $InfoResponse["Nombre"] = $Nombre;
            $InfoResponse["Descripcion"] = $Descripcion;

            //FormatosJuego
            $SQLFormato = "SELECT IDGameGolfFormato, IDClub,IDGameGolfCategoriaFormato,  Nombre, NombreEng, Descripcion, DescripcionEng, Tipo,MostrarUsarHandicap, MostrarUsarHandicapCampo, MostrarSeleccionarVentaja, MostrarPorcentajeHandicap, MostrarNumeroResultados, Publicar
                               FROM  GameGolfFormato  
                               WHERE IDGameGolfCategoriaFormato = '" . $Datos["IDGameGolfCategoriaFormato"] . "'  and Publicar = 'S'";
            $QRYFormato = $dbo2->query($SQLFormato);
            while ($Formato = $dbo2->fetchArray($QRYFormato)) :
                $array_formato["IDFormatoJuego"] = $Formato["IDGameGolfFormato"];
                if (LANG == "en") {
                    $Nombre = $Formato["NombreEng"];
                    $Descripcion = $Formato["DescripcionEng"];
                } else {
                    $Nombre = $Formato["Nombre"];
                    $Descripcion = $Formato["Descripcion"];
                }

                $array_formato["NombreFormatoJuego"] = $Nombre;
                $array_formato["Descripcion"] = $Descripcion;
                $array_formato["Tipo"] = $Formato["Tipo"];
                $array_formato["MostrarUsarHandicap"] = $Formato["MostrarUsarHandicap"];
                $array_formato["MostrarUsarHandicapCampo"] = $Formato["MostrarUsarHandicapCampo"];
                $array_formato["MostrarSeleccionarVentaja"] = $Formato["MostrarSeleccionarVentaja"];
                $array_formato["MostrarPorcentajeHandicap"] = $Formato["MostrarPorcentajeHandicap"];
                $array_formato["MostrarNumeroResultados"] = $Formato["MostrarNumeroResultados"];
                array_push($response_formato, $array_formato);
            endwhile;

            $InfoResponse["FormatosJuego"] = $response_formato;

            array_push($response, $InfoResponse);
        endwhile;
        $respuesta[message] = "Datos encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;
        return $respuesta;
    }

    public function agregar_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGolfCourse, $NumeroHoyos, $IDMarca, $Grupos, $FormatosJuegoCreados, $HoyoInicial)
    {
        $dbo = &SIMDB::get();
        $dbo2 = &SIMDB2::get();

        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $datos_juego = $dbo2->fetchAll("GameGolfCourse", " IDGameGolfCourse = '" . $IDGolfCourse . "' ", "array");
        $Mensaje = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] .  " te ha agregado a un juego de golf para el dia: " . date("Y-m-d") . " en: " . $datos_juego["Nombre"];

        if (!empty($IDSocio) && !empty($IDGolfCourse) && !empty($NumeroHoyos) && !empty($Grupos) && !empty($FormatosJuegoCreados)) {
            $FormatoHoyos = $NumeroHoyos;
            switch ($NumeroHoyos) {
                case "1-9":
                case "10-18":
                    $NumeroHoyos = 9;
                    break;
                case "1-18":
                    $NumeroHoyos = 18;
                    break;
            }

            $sql_inserta = "INSERT INTO GameGolfJuego  (IDClub, IDSocio, IDGolfCourse, IDGameGolfMarca, NumeroHoyos, FormatoHoyos, HoyoInicial, UsuarioTrCr, FechaTrCr ) 
                              VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDGolfCourse . "','" . $IDMarca . "','" . $NumeroHoyos . "','" . $FormatoHoyos . "','" . $HoyoInicial . "','APP',NOW())";
            $dbo2->query($sql_inserta);
            $IDJuego = $dbo2->lastID();

            //Creo los formatos
            $FormatosJuegoCreados = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($FormatosJuegoCreados));
            $datos_formato = json_decode($FormatosJuegoCreados, true);
            if (count($datos_formato) > 0) {
                foreach ($datos_formato as $detalle_datos) {
                    if (empty($detalle_datos["UsarHandicap"]))
                        $UsarHcp = "S";
                    else
                        $UsarHcp = $detalle_datos["UsarHandicap"];

                    $sql_inserta_grupo = "INSERT INTO GameGolfJuegoFormato   (IDClub, IDGameGolfJuego,IDGameGolfFormato, IDJugadorPrincipal, IDJugadorJuego, UsarHandicap, UsarHandicapCampo, PorcentajeHandicap, VentajaSeleccionada, NumeroDeResultados, JuegoPorHoyosMatch, JuegoPorGolpesMedal, MenorHandicapBajaCero, UsarMejorBolaDelGrupo, UsarSumaDelGrupo, UsuarioTrCr, FechaTrCr ) 
                                            VALUES ('" . $IDClub . "','" . $IDJuego . "','" . $detalle_datos["IDFormatoJuego"] . "','" . $detalle_datos["IDJugador2"] . "','" . $detalle_datos["IDJugador1"] . "','" . $UsarHcp . "', '" . $detalle_datos["UsarHandicapCampo"] . "', '" . $detalle_datos["PorcentajeHandicap"] . "', '" . $detalle_datos["VentajaSeleccionada"] . "','" . $detalle_datos["NumeroDeResultados"] . "' , '" . $detalle_datos["JuegoPorHoyosMatch"] . "' ,'" . $detalle_datos["JuegoPorGolpesMedal"] . "','" . $detalle_datos["MenorHandicapBajaCero"] . "','" . $detalle_datos["UsarMejorBolaDelGrupo"] . "','" . $detalle_datos["UsarSumaDelGrupo"] . "','APP',NOW())";
                    $dbo2->query($sql_inserta_grupo);
                    $IDFormato = $dbo2->lastID();


                    $datos_llaves = $detalle_datos["Llaves"];
                    if (count($datos_llaves) > 0) {
                        foreach ($datos_llaves as $detalle_llave) {
                            $IDJugador1Pareja1 = $detalle_llave["Pareja1"]["IDJugador1"];
                            $IDJugador2Pareja1 = $detalle_llave["Pareja1"]["IDJugador2"];
                            $IDJugador1Pareja2 = $detalle_llave["Pareja2"]["IDJugador1"];
                            $IDJugador2Pareja2 = $detalle_llave["Pareja2"]["IDJugador2"];
                            $sql_inserta_llave = "INSERT INTO GameGolfJuegoFormatoLlaves    (IDClub, IDGameGolfJuego,IDGameGolfFormato, IDGameGolfJuegoFormato, IDJugador1Pareja1, IDJugador2Pareja1, IDJugador1Pareja2, IDJugador2Pareja2, UsuarioTrCr, FechaTrCr ) 
                                                    VALUES ('" . $IDClub . "','" . $IDJuego . "','" . $detalle_datos["IDFormatoJuego"] . "','" . $IDFormato . "','" . $IDJugador1Pareja1 . "', '" . $IDJugador2Pareja1 . "', '" . $IDJugador1Pareja2 . "', '" . $IDJugador2Pareja2 . "',  'APP',NOW())";
                            $dbo2->query($sql_inserta_llave);
                        }
                    }

                    //Formato parejas match    
                    $datos_llaves = $detalle_datos["Pareja1"];
                    if (count($datos_llaves) > 0) {
                        $IDJugador1Pareja1 = $detalle_datos["Pareja1"]["IDJugador1"];
                        $IDJugador2Pareja1 = $detalle_datos["Pareja1"]["IDJugador2"];
                        $IDJugador1Pareja2 = $detalle_datos["Pareja2"]["IDJugador1"];
                        $IDJugador2Pareja2 = $detalle_datos["Pareja2"]["IDJugador2"];
                        $sql_inserta_llave = "INSERT INTO GameGolfJuegoFormatoLlaves    (IDClub, IDGameGolfJuego,IDGameGolfFormato, IDGameGolfJuegoFormato, IDJugador1Pareja1, IDJugador2Pareja1, IDJugador1Pareja2, IDJugador2Pareja2, UsuarioTrCr, FechaTrCr ) 
                                                    VALUES ('" . $IDClub . "','" . $IDJuego . "','" . $detalle_datos["IDFormatoJuego"] . "','" . $IDFormato . "','" . $IDJugador1Pareja1 . "', '" . $IDJugador2Pareja1 . "', '" . $IDJugador1Pareja2 . "', '" . $IDJugador2Pareja2 . "',  'APP',NOW())";
                        $dbo2->query($sql_inserta_llave);
                    }

                    //Datos subgrupos
                    $datos_subgrupo = $detalle_datos["SubGrupos"];
                    if (count($datos_subgrupo) > 0) {
                        foreach ($datos_subgrupo as $datos_llave) {
                            $sql_inserta_llave = "INSERT INTO GameGolfJuegoFormatoSubGrupo  (IDClub, IDGameGolfJuego,IDGameGolfFormato, IDGameGolfJuegoFormato, UsuarioTrCr, FechaTrCr ) 
                                                    VALUES ('" . $IDClub . "','" . $IDJuego . "','" . $detalle_datos["IDFormatoJuego"] . "','" . $IDFormato . "','APP',NOW())";
                            $dbo2->query($sql_inserta_llave);
                            $IDSubGrupo = $dbo2->lastID();
                            $datos_jugador = $datos_llave["Jugadores"];
                            if (count($datos_jugador) > 0) {
                                foreach ($datos_jugador as $datos_jugador) {
                                    $sql_inserta_llave = "INSERT INTO GameGolfJuegoFormatoSubGrupoDetalle   (IDGameGolfJuego,IDGameGolfJuegoFormatoSubGrupo, IDJugador, UsuarioTrCr, FechaTrCr ) 
                                                    VALUES ('" . $IDJuego . "','" . $IDSubGrupo . "','" . $datos_jugador["IDJugador"] . "','APP',NOW())";
                                    $dbo2->query($sql_inserta_llave);
                                }
                            }
                        }
                    }
                }
            }



            //Crear los  grupos
            $Grupos = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($Grupos));
            $datos_grupo = json_decode($Grupos, true);
            if (count($datos_grupo) > 0) {
                foreach ($datos_grupo as $detalle_datos) {

                    $sql_inserta_grupo = "INSERT INTO GameGolfJuegoGrupo  (IDClub, IDGameGolfJuego, Nombre, HoyoInicial, UsuarioTrCr, FechaTrCr ) 
                              VALUES ('" . $IDClub . "','" . $IDJuego . "','" . $detalle_datos["Nombre"] . "','" . $detalle_datos["HoyoInicial"] . "','APP',NOW())";
                    $dbo2->query($sql_inserta_grupo);
                    $IDGrupoCreado = $dbo2->lastID();
                    $datos_participante = $detalle_datos["Participantes"];
                    if (count($datos_participante) > 0) {
                        foreach ($datos_participante as $detalle_participante) {


                            $Handicap = $resp["response"];
                            $dbo2 = &SIMDB2::get();
                            $dblinkGame = $dbo2->connect(DBHOSTGame, DBNAMEGame, DBUSERGame, DBPASSGame);

                            $sql_participante = "INSERT INTO GameGolfJuegoGrupoJugadores (IDGameGolfJuegoGrupo,IDClub,IDGameGolfJuego,IDJugador,IDGameGolfMarca,Handicap,HandicapCampo,UsuarioTrCr,FechaTrCr) 
                                                    VALUES('" . $IDGrupoCreado . "','" . $IDClub . "','" . $IDJuego . "','" . $detalle_participante["IDJugador"] . "','" . $detalle_participante["IDMarca"] . "','" . $detalle_participante["Handicap"] . "','" . $HandicapCampo . "','WS',NOW())";

                            $dbo2->query($sql_participante);

                            //Actualizo el handicap del socio con el que se ponga
                            $actualiza_handicap = "UPDATE Socio SET Handicap = '" . $detalle_participante["Handicap"] . "' WHERE IDSocio = '" . $detalle_participante["IDJugador"] . "' ";
                            $dbo->query($actualiza_handicap);

                            self::calcular_resultado_tarjeta($IDClub, $detalle_participante["IDJugador"], $IDJuego);

                            SIMUtil::enviar_notificacion_push_general($IDClub, $detalle_participante["IDJugador"], $Mensaje, 182, "");
                        }
                    }
                }
            }


            if ((int)$IDJuego > 0) {

                $resp = self::get_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuego);
                $response_juego = $resp["response"];

                $respuesta[message] = "Juego creado correctamente";
                $respuesta[success] = true;
                $respuesta[response] = $response_juego;
            } else {
                $respuesta[message] = "GGJ2. Hubo un problema al tratar de crear el juego!";
                $respuesta[success] = false;
                $respuesta[response] = null;
            }
        } else {
            $respuesta[message] = "GGJ. Atencion faltan parametros!";
            $respuesta[success] = false;
            $respuesta[response] = null;
        }
        return $respuesta;
    }

    public function get_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf)
    {
        $dbo = SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response_hoyos = array();
        $response_grupo = array();
        $response_formatos = array();
        $response_llaves = array();
        $response_subgrupos = array();
        $response_jug_subgrupos = array();

        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
        if ($datos_juego["IDGameGolfJuego"] > 0) :
            $InfoResponse[IDJuegoGolf] = $datos_juego[IDGameGolfJuego];
            $InfoResponse[Fecha] = substr($datos_juego["FechaTrCr"], 0, 10);

            //info del campo
            $resp = self::get_juegos_golf_courses($IDClub, $IDSocio, $IDUsuario, $Texto, $datos_juego["IDGolfCourse"]);
            $GolfCourse = $resp["response"][0];
            $InfoResponse["GolfCourse"] = $GolfCourse;

            $InfoResponse["TextoEstado"] = "Juego activo";
            $InfoResponse["IDMarca"] = $datos_juego["IDGameGolfMarca"];

            $InfoResponse["NumeroHoyos"] = $datos_juego["FormatoHoyos"];
            $InfoResponse["HoyoInicial"] = $datos_juego["HoyoInicial"];

            //Hoyos
            $sql_hoyos = "SELECT IDGameGolfCourse, Hoyo, Par, Handicap FROM GameGolfHoyos  WHERE IDGameGolfCourse = '" . $datos_juego["IDGolfCourse"] . "' and IDGameGolfMarca = '" . $datos_juego["IDGameGolfMarca"] . "' and Hoyo <= '" . $datos_juego["NumeroHoyos"] . "' ";
            $r_hoyos = $dbo2->query($sql_hoyos);
            while ($row_hoyos = $dbo2->fetchArray($r_hoyos)) {
                $array_hoyo["Hoyo"] = $row_hoyos["Hoyo"];
                $array_hoyo["Par"] = $row_hoyos["Par"];
                $array_hoyo["Handicap"] = $row_hoyos["Handicap"];
                array_push($response_hoyos, $array_hoyo);
            }
            $InfoResponse[HoyosJuego] = $response_hoyos;


            //Formatos
            $sql_formato = "SELECT IDGameGolfJuegoFormato,IDGameGolfFormato, IDJugadorPrincipal, IDJugadorJuego, UsarHandicap, UsarHandicapCampo, PorcentajeHandicap, VentajaSeleccionada, NumeroDeResultados, JuegoPorHoyosMatch, JuegoPorGolpesMedal, MenorHandicapBajaCero, UsarMejorBolaDelGrupo, UsarSumaDelGrupo   
                              FROM GameGolfJuegoFormato   
                              WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $r_formato = $dbo2->query($sql_formato);
            while ($row_formato = $dbo2->fetchArray($r_formato)) {
                $response_subgrupos = array();
                $datos_formato = $dbo2->fetchAll("GameGolfFormato", " IDGameGolfFormato = '" . $row_formato["IDGameGolfFormato"] . "' ", "array");

                $array_formato["IDFormatoJuego"] = $row_formato["IDGameGolfFormato"];
                $array_formato["IDPartida"] = $row_formato["IDGameGolfJuegoFormato"];

                $array_formato["IDJugadorJuego"] = $row_formato["IDJugadorJuego"];
                $array_formato["IDJugador1"] = $row_formato["IDJugadorPrincipal"];
                $array_formato["IDJugador2"] = $row_formato["IDJugadorJuego"];
                /*
                    if($IDSocio==$row_formato["IDJugadorPrincipal"]){
                        $array_formato["IDJugadorJuego"]=$row_formato["IDJugadorJuego"];
                    }
                    else{
                        $array_formato["IDJugadorJuego"]=$row_formato["IDJugadorPrincipal"];
                    }
                    */

                if ($datos_formato["Tipo"] == "sub_grupal") {
                    if ($row_formato["UsarMejorBolaDelGrupo"] == "S") {
                        $DetalleFormato = " mejor bola";
                    } else {
                        $DetalleFormato = " suma";
                    }
                }

                $array_formato["NombreFormatoJuego"] = $datos_formato["Nombre"] . $DetalleFormato;
                $array_formato["Descripcion"] = $datos_formato["Descripcion"];
                $array_formato["UsarHandicap"] = $row_formato["UsarHandicap"];
                $array_formato["UsarHandicapCampo"] = $row_formato["UsarHandicapCampo"];
                $array_formato["PorcentajeHandicap"] = $row_formato["PorcentajeHandicap"];
                $array_formato["VentajaSeleccionada"] = (int)$row_formato["VentajaSeleccionada"];

                $array_formato["NumeroDeResultados"] = $row_formato["NumeroDeResultados"];
                $array_formato["TipoFormato"] = $datos_formato["Tipo"];
                $array_formato["MostrarUsarHandicap"] = $datos_formato["MostrarUsarHandicap"];
                $array_formato["MostrarUsarHandicapCampo"] = $datos_formato["MostrarUsarHandicapCampo"];
                $array_formato["MostrarPorcentajeHandicap"] = $datos_formato["MostrarPorcentajeHandicap"];
                $array_formato["MostrarNumeroResultados"] = $datos_formato["MostrarNumeroResultados"];

                $array_formato["JuegoPorGolpesMedal"] = $row_formato["JuegoPorGolpesMedal"];
                $array_formato["JuegoPorHoyosMatch"] = $row_formato["JuegoPorHoyosMatch"];
                $array_formato["MenorHandicapBajaCero"] = $row_formato["MenorHandicapBajaCero"];

                $array_formato["UsarMejorBolaDelGrupo"] = $row_formato["UsarMejorBolaDelGrupo"];
                $array_formato["UsarSumaDelGrupo"] = $row_formato["UsarSumaDelGrupo"];

                $array_formato["TipoCategoria"] = $dbo2->getFields(" GameGolfCategoriaFormato  ", "Nombre", "IDGameGolfCategoriaFormato  = '" . $datos_formato["IDGameGolfCategoriaFormato"] . "'  ");


                //laves Formato
                $sql_llaves = "SELECT IDJugador1Pareja1, IDJugador2Pareja1, IDJugador1Pareja2, IDJugador2Pareja2
                                  FROM GameGolfJuegoFormatoLlaves
                                  WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
                $r_llaves = $dbo2->query($sql_llaves);
                while ($row_llaves = $dbo2->fetchArray($r_llaves)) {
                    $array_formato["Pareja1"] = array("IDJugador1" => $row_llaves["IDJugador1Pareja1"], "IDJugador2" => $row_llaves["IDJugador2Pareja1"]);
                    $array_formato["Pareja2"] = array("IDJugador1" => $row_llaves["IDJugador1Pareja2"], "IDJugador2" => $row_llaves["IDJugador2Pareja2"]);
                    //array_push($response_llaves, $array_llaves);
                }

                //$array_formato["Llaves"]=$response_llaves;


                //Sub Grupos
                $sql_subgrupo = "SELECT IDGameGolfJuegoFormatoSubGrupo
                                  FROM GameGolfJuegoFormatoSubGrupo 
                                  WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDGameGolfFormato = '" . $row_formato["IDGameGolfFormato"] . "' and IDGameGolfJuegoFormato = '" . $row_formato["IDGameGolfJuegoFormato"] . "' ";
                $r_subgrupo = $dbo2->query($sql_subgrupo);
                while ($row_subgrupo = $dbo2->fetchArray($r_subgrupo)) {
                    $array_jug_subgrupo = array();
                    $response_jug_subgrupos = array();
                    $array_subgrupo["IDSubGrupo"] = $row_subgrupo["IDGameGolfJuegoFormatoSubGrupo"];
                    $sql_jug_subgrupo = "SELECT IDJugador
                                  FROM GameGolfJuegoFormatoSubGrupoDetalle 
                                  WHERE IDGameGolfJuegoFormatoSubGrupo = '" . $row_subgrupo["IDGameGolfJuegoFormatoSubGrupo"] . "'";
                    $r_jug_subgrupo = $dbo2->query($sql_jug_subgrupo);
                    while ($row_jug_subgrupo = $dbo2->fetchArray($r_jug_subgrupo)) {
                        $array_jug_subgrupo["IDJugador"] = $row_jug_subgrupo["IDJugador"];
                        array_push($response_jug_subgrupos, $array_jug_subgrupo);
                    }

                    $array_subgrupo["Jugadores"] = $response_jug_subgrupos;

                    array_push($response_subgrupos, $array_subgrupo);
                }

                $array_formato["SubGrupos"] = $response_subgrupos;


                array_push($response_formatos, $array_formato);
            }
            $InfoResponse[FormatosJuego] = $response_formatos;

            //Grupos
            $sql_grupo = "SELECT GGJP.IDGameGolfJuegoGrupo, GGJP.IDGameGolfJuego, GGJP.Nombre, GGJP.HoyoInicial
                            FROM GameGolfJuegoGrupo GGJP, GameGolfJuegoGrupoJugadores GGJGJ
                            WHERE GGJP.IDGameGolfJuegoGrupo = GGJGJ.IDGameGolfJuegoGrupo and  
                                  GGJP.IDGameGolfJuego = '" . $IDJuegoGolf . "' 
                            GROUP BY GGJGJ.IDGameGolfJuegoGrupo ";
            $r_grupo = $dbo2->query($sql_grupo);
            while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                $response_jugador = array();
                $array_grupo["IDGrupo"] = $row_grupo["IDGameGolfJuegoGrupo"];
                $array_grupo["Nombre"] = $row_grupo["Nombre"];
                $array_grupo["HoyoInicial"] = $row_grupo["HoyoInicial"];

                //Jugadores grupo
                $sql_grupo_part = "SELECT IDJugador, Handicap, IDGameGolfMarca
                                    FROM GameGolfJuegoGrupoJugadores GGJGJ
                                    WHERE GGJGJ.IDGameGolfJuegoGrupo =  '" . $row_grupo["IDGameGolfJuegoGrupo"] . "' ";
                $r_grupo_part = $dbo2->query($sql_grupo_part);
                while ($row_grupo_part = $dbo2->fetchArray($r_grupo_part)) {
                    if ((int)$row_grupo_part["IDJugador"] > 0) {
                        $Marca = $dbo2->getFields("GameGolfMarca", "Nombre", "IDGameGolfMarca = '" . $row_grupo_part["IDGameGolfMarca"] . "'  ");
                        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_grupo_part["IDJugador"] . "' ", "array");
                        $array_jugador["IDJugador"] = $row_grupo_part["IDJugador"];
                        $array_apellido = explode(" ", $datos_socio["Apellido"]);
                        $array_jugador["Nombre"] = $datos_socio["Nombre"] . " " . $array_apellido[0] . " " . substr($array_apellido[1], 0, 1);
                        $array_jugador["Handicap"] = $row_grupo_part["Handicap"];
                        $array_jugador["IDMarca"] = $row_grupo_part["IDGameGolfMarca"];
                        $array_jugador["NombreMarca"] = $Marca;
                        array_push($response_jugador, $array_jugador);
                    }
                }

                $array_grupo["Participantes"] = $response_jugador;

                array_push($response_grupo, $array_grupo);
            }

            $InfoResponse[Grupos] = $response_grupo;


            $respuesta[message] = "Juego encontrado";
            $respuesta[success] = true;
            $respuesta[response] = $InfoResponse;
        else :
            $respuesta[message] = "Juego no existe";
            $respuesta[success] = false;
            $respuesta[response] = "";
        endif;

        return $respuesta;
    }

    public function get_golpes_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf)
    {
        $dbo = SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response = array();
        $response_golpe = array();
        $response_neto = array();

        $InfoResponse["IDJuegoGolf"] = $IDJuegoGolf;

        //Score Neto
        $SQLNeto = "SELECT IDJugadorJuego,SobrePar FROM GameGolfJuegoTarjeta WHERE IDGameGolfJuego = $IDJuegoGolf GROUP BY IDJugadorJuego Order by IDGameGolfFormato ASC  ";
        $QRYNeto = $dbo2->query($SQLNeto);
        while ($Neto = $dbo2->fetchArray($QRYNeto)) :
            $array_neto["IDJugador"] = $Neto["IDJugadorJuego"];
            if ($Neto["SobrePar"] == -9999) {
                $MostrarValor = "NR";
                $ColorValor = "#F71212";
            } elseif ($Neto["SobrePar"] >= 0) {
                $MostrarValor = "+" . $Neto["SobrePar"];
                $ColorValor = "#000000";
            } else {
                $MostrarValor = $Neto["SobrePar"];
                $ColorValor = "#F71212";
            }
            $array_neto["Neto"] = $MostrarValor;
            $array_neto["ColorNeto"] = $ColorValor;
            array_push($response_neto, $array_neto);
        endwhile;

        $InfoResponse["ScoreNeto"] = $response_neto;


        //Golpes
        $SQLGolpe = "SELECT IDJugador,IDGameGolfJuego,Hoyo, NumeroGolpes
                                FROM GameGolfGolpe 
                                WHERE IDGameGolfJuego = $IDJuegoGolf ";
        $QRYGolpe = $dbo2->query($SQLGolpe);
        while ($Golpe = $dbo2->fetchArray($QRYGolpe)) :
            if ($Golpe["NumeroGolpes"] == -1) {
                $array_golpe["NumeroGolpes"] = "X";
            } else {
                $array_golpe["NumeroGolpes"] = $Golpe["NumeroGolpes"];
            }
            $array_golpe_jugador[$Golpe["Hoyo"]][$Golpe["IDJugador"]] = $array_golpe["NumeroGolpes"];
        endwhile;


        $sql_jugador = "SELECT IDJugador,IDGameGolfMarca,Handicap FROM GameGolfJuegoGrupoJugadores WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
        $r_jugador = $dbo2->query($sql_jugador);
        while ($row_jugador = $dbo2->fetchArray($r_jugador)) {
            $Signo = "";
            $array_hoyo_punto = array();
            $Handicap = $row_jugador["Handicap"];
            $PuntosTodosHoyos = 0;

            if (!empty($Handicap)) {
                if ($Handicap > 0) {
                    $Operacion = "Sumar";
                    $CondicionOrden = " ORDER BY Handicap ASC";
                    $Signo = "-";
                } elseif ($Handicap < 0) {
                    $CondicionOrden = " ORDER BY Handicap DESC";
                    $Operacion = "Restar";
                    $Signo = "+";
                } else {
                    $Signo = "";
                }

                $PuntosHcp = abs($Handicap);
                // Se reparten los puntos del handicap entre los hoyos en este caso a alos mas dificiles les doy ventaja
                if ($PuntosHcp > 18) {
                    $PuntosTodosHoyos = (int)($PuntosHcp / 18);
                    $OtrosPuntos = $PuntosHcp - (18 * $PuntosTodosHoyos);
                } else {
                    $OtrosPuntos = $PuntosHcp;
                }
            }




            if ($Handicap != 0) {
                //averiguo los hoyos de la marca seleccionada                        
                $PuntosAsignados = 0;
                $sql_hoyos = "SELECT Hoyo,Handicap FROM GameGolfHoyos WHERE IDGameGolfMarca = '" . $row_jugador["IDGameGolfMarca"] . "' " . $CondicionOrden;
                $r_hoyos = $dbo2->query($sql_hoyos);
                while ($row_hoyos = $dbo2->fetchArray($r_hoyos)) {
                    $array_hoyo_punto[$row_hoyos["Hoyo"]][$row_jugador["IDGameGolfMarca"]] = $PuntosTodosHoyos;
                    $array_hoyo_par[$row_hoyos["Hoyo"]][$row_jugador["IDGameGolfMarca"]] = $row_hoyos["Par"];
                    $array_hoyo_hcp[$row_hoyos["Hoyo"]][$row_jugador["IDGameGolfMarca"]] = $row_hoyos["Handicap"];
                    if ($OtrosPuntos > 0 && $PuntosAsignados < $OtrosPuntos) {
                        $array_hoyo_punto[$row_hoyos["Hoyo"]][$row_jugador["IDGameGolfMarca"]]++;
                    }
                    $PuntosAsignados++;
                }
            }




            for ($i = 1; $i <= 18; $i++) {
                $Golpes = (int)$array_golpe_jugador[$i][$row_jugador["IDJugador"]];
                $Ventaja = $array_hoyo_punto[$i][$row_jugador["IDGameGolfMarca"]];

                if ($Operacion == "Sumar") {
                    $Diff = $Golpes - $Ventaja;
                } else {
                    $Diff = $Golpes + $Ventaja;
                }

                $array_golpe["Hoyo"] = (string)$i;
                $array_golpe["IDJugador"] = $row_jugador["IDJugador"];
                if ($array_hoyo_punto[$i][$row_jugador["IDGameGolfMarca"]] > 0) {
                    $array_golpe["Ventaja"] = $Signo . $array_hoyo_punto[$i][$row_jugador["IDGameGolfMarca"]];
                } else {
                    $array_golpe["Ventaja"] = "";
                }

                $array_golpe["SubIndice"] = (string)$Diff;
                $array_golpe["NumeroGolpes"] = $array_golpe_jugador[$i][$row_jugador["IDJugador"]];
                $array_golpe["SubTituloEnScore"] = "Ventaja Hoyo: " . $array_hoyo_hcp[$i][$row_jugador["IDGameGolfMarca"]];;

                array_push($response_golpe, $array_golpe);
            }
        }

        $InfoResponse["Golpes"] = $response_golpe;
        array_push($response, $InfoResponse);

        $respuesta[message] = "Datos encontrados.";
        $respuesta[success] = true;
        $respuesta[response] = $InfoResponse;
        return $respuesta;
    }

    public function set_golpes_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $Golpes)
    {
        $dbo2 = &SIMDB2::get();
        $dbo = SIMDB::get();
        $response = array();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDJuegoGolf) && !empty($Golpes)) {




            $Golpes = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($Golpes));
            $datos_golpe = json_decode($Golpes, true);


            //Borrar solo las del grupo para no borrar el de los demas
            $IDGrupo = $dbo2->getFields("GameGolfJuegoGrupoJugadores  ", "IDGameGolfJuegoGrupo", "IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDJugador = '" . $IDSocio . "'  ");
            $sql_jugadores_grupo = "SELECT IDJugador FROM GameGolfJuegoGrupoJugadores WHERE IDGameGolfJuegoGrupo = '" . $IDGrupo . "'";
            $r_jugadores_grupo = $dbo2->query($sql_jugadores_grupo);
            while ($row_jugadores_grupo = $dbo2->fetchArray($r_jugadores_grupo)) {
                $array_iud_jugador[] = $row_jugadores_grupo["IDJugador"];
            }
            if (count($array_iud_jugador) > 0) {
                $id_jugador = implode(",", $array_iud_jugador);
            }

            $borra_golpes = "DELETE FROM GameGolfGolpe WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDJugador in (" . $id_jugador . ") ";
            $dbo2->query($borra_golpes);

            $borra_tarj = "DELETE FROM GameGolfJuegoTarjeta WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDJugadorJuego in (" . $id_jugador . ") ";
            $dbo2->query($borra_tarj);

            $borra_detalle_tarj = "DELETE FROM GameGolfJuegoTarjetaDetalle WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDJugadorJuego in (" . $id_jugador . ") ";
            $dbo2->query($borra_detalle_tarj);


            foreach ($datos_golpe as $detalle_golpe) :

                $Hoyo = $detalle_golpe["Hoyo"];
                $IDJugador = $detalle_golpe["IDJugador"];
                if ($detalle_golpe["NumeroGolpes"] == "X")
                    $NumeroGolpes = -1;
                else
                    $NumeroGolpes = $detalle_golpe["NumeroGolpes"];

                $array_jugador_calcular[] = $IDJugador;


                if (!empty($Hoyo) && !empty($IDJugador) && !empty($NumeroGolpes)) {

                    $inserta_golpe = "INSERT INTO GameGolfGolpe (IDClub, IDJugador, IDGameGolfJuego, Hoyo, NumeroGolpes, UsuarioTrCr, FechaTrCr)
                                            VALUES('" . $IDClub . "', '" . $IDJugador . "', '" . $IDJuegoGolf . "', '" . $Hoyo . "', '" . $NumeroGolpes . "', '" . $IDSocio . "', NOW() )";
                    $dbo2->query($inserta_golpe);

                    /*
                            $dboMongo =& SIMDBMongo::get();
                            $array_no_guardar = array("Token","Token2");                    
                            $FechaActual=(
                              ($FechaInsertar=new MongoDB\BSON\UTCDateTime())->toDateTime()->format('U.u')
                            );                    
                            $now = \DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new \DateTimeZone('America/Bogota'));
                            $FechaPeticionTexto=$now->setTimeZone(new DateTimeZone('America/Bogota'))->format("Y-m-d H:i:s.u");
                            $array_guardar["IDClub"]=$IDClub;
                            $array_guardar["IDJugador"]=$IDJugador;
                            $array_guardar["IDGameGolfJuego"]=$IDJuegoGolf;
                            $array_guardar["Hoyo"]=$Hoyo;
                            $array_guardar["NumeroGolpes"]=$NumeroGolpes;
                            $array_guardar["UsuarioTrCr"]=$IDSocio;
                            $array_guardar["FechaTrCr"]=$FechaInsertar;
                            $array_guardar["FechaPeticion"]=$FechaPeticionTexto;
                            $dboMongo->insert($array_guardar,'GameGolfJuegoTarjeta'); 

                            $dboMongo->deleteOne('{ "_id" : ObjectId("563237a41a4d68582c2509da") }  ');
                            */

                    //calculo de una vez los resultados de las tarjetas

                }
            endforeach;


            foreach ($array_jugador_calcular as $IDJugador) {
                self::calcular_resultado_tarjeta($IDClub, $IDJugador, $IDJuegoGolf);
            }
            self::calcular_resultado_tarjeta_grupo($IDClub, $IDJuegoGolf);

            $respuesta["message"] = "Valor guardado";
            $respuesta["success"] = true;
            $respuesta["response"] = null;
        } else {


            $respuesta[message] = "GGG1. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function datos_resultado($hoyo, $array_hoyo, $array_detalle, $Tipo)
    {

        //Si es dos golpes o mas  del par es cuadrado oscuro
        $DosMas = $array_hoyo[$hoyo]["Par"] + 2;
        $UnoMas = $array_hoyo[$hoyo]["Par"] + 1;
        $CuantoMenos = $array_hoyo[$hoyo]["Par"] - $array_detalle[$hoyo]["Resultado"];

        if ($Tipo == "Jugador") {
            $ResultadoMostrar = $array_detalle[$hoyo]["Resultado"];
            if ((int)$array_detalle[$hoyo]["Resultado"] == 0) {
                $TipoForma = "";
                $ColorFondo = "#E3F8E9";
                $ColorLetra = "#E3F8E9";
            } elseif ($array_detalle[$hoyo]["Resultado"] == -1) {
                $ResultadoMostrar = "X";
                $TipoForma = "";
                $ColorFondo = "#E3F8E9";
                $ColorLetra = "#000000";
            } elseif ($array_detalle[$hoyo]["Resultado"] >= $DosMas) {
                $TipoForma = "Cuadrada";
                $ColorFondo = "#064974";
                $ColorLetra = "#FFFEFF";
            } elseif ($array_detalle[$hoyo]["Resultado"] == $UnoMas) {
                $TipoForma = "Cuadrada";
                $ColorFondo = "#98C8E8";
                $ColorLetra = "#000000";
            } elseif ($CuantoMenos == 1) {
                $TipoForma = "Circular";
                $ColorFondo = "#EE271A";
                $ColorLetra = "#FFFEFF";
            } elseif ($CuantoMenos == 2) {
                $TipoForma = "Circular";
                $ColorFondo = "#FAF60B";
                $ColorLetra = "#000000";
            }
        } else {
            if ($array_detalle[$hoyo]["Resultado"] >= 1) {
                $SignoPoner = "+";
            }
            if (empty($array_detalle[$hoyo])) {
                $ResultadoMostrar = "";
                $TipoForma = "";
                $ColorFondo = "#E3F8E9";
                $ColorLetra = "#E3F8E9";
            } elseif ((int)$array_detalle[$hoyo]["Resultado"] == 0) {
                $ResultadoMostrar = "E";
                $TipoForma = "";
                $ColorFondo = "#FFFFFF";
                $ColorLetra = "#000000";
            } elseif ($array_detalle[$hoyo]["Resultado"] == "-1") {
                $ResultadoMostrar = $array_detalle[$hoyo]["Resultado"];
                $TipoForma = "Circular";
                $ColorFondo = "#EE271A";
                $ColorLetra = "#FFFEFF";
            } elseif ($array_detalle[$hoyo]["Resultado"] == "-2") {
                $ResultadoMostrar = $array_detalle[$hoyo]["Resultado"];
                $TipoForma = "Circular";
                $ColorFondo = "#FF9435";
                $ColorLetra = "#000000";
            } elseif ($array_detalle[$hoyo]["Resultado"] <= "-3") {
                $ResultadoMostrar = $array_detalle[$hoyo]["Resultado"];
                $TipoForma = "Circular";
                $ColorFondo = "#FFFF63";
                $ColorLetra = "#000000";
            } elseif ($array_detalle[$hoyo]["Resultado"] == 2) {
                $ResultadoMostrar = $array_detalle[$hoyo]["Resultado"];
                $TipoForma = "Cuadrada";
                $ColorFondo = "#064974";
                $ColorLetra = "#FFFEFF";
            } elseif ($array_detalle[$hoyo]["Resultado"] == 1) {
                $ResultadoMostrar = $array_detalle[$hoyo]["Resultado"];
                $TipoForma = "Cuadrada";
                $ColorFondo = "#98C8E8";
                $ColorLetra = "#FFFEFF";
            } elseif ($CuantoMenos == 2) {
                $ResultadoMostrar = $array_detalle[$hoyo]["Resultado"];
                $TipoForma = "Circular";
                $ColorFondo = "#FAF60B";
                $ColorLetra = "#000000";
            } else {
                $ResultadoMostrar = $array_detalle[$hoyo]["Resultado"];
            }
        }


        $array_respuesta["Numero"] = (string)$hoyo;
        $array_respuesta["Handicap"] = $array_hoyo[$hoyo]["Handicap"];
        $array_respuesta["Par"] = $array_hoyo[$hoyo]["Par"];
        $array_respuesta["Score"] = (string)$SignoPoner . $ResultadoMostrar;
        $array_respuesta["ScoreFondo"] = $ColorFondo;
        $array_respuesta["ScoreColor"] = $ColorLetra;
        $array_respuesta["ScoreForma"] = $TipoForma;
        $array_respuesta["Neto"] = $array_detalle[$hoyo]["Neto"];;
        $array_respuesta["ScoreNegrilla"] = "S";

        return $array_respuesta;
    }


    public function consulta_resultados($IDJuegoGolf, $IDFormatoJuego, $Identificador, $Tipo, $Posicion, $Vista)
    {
        $dbo = SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response_hoyo = array();
        $response_hoyo_vuelta = array();
        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
        $datos_formato = $dbo2->fetchAll("GameGolfFormato", "IDGameGolfFormato  = '" . $IDFormatoJuego . "' ", "array");

        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }





        if ($datos_formato["MostrarNeto"] == "S")
            $MostrarNeto = "S";
        else
            $MostrarNeto = "N";


        if ($Tipo == "Jugador") {
            $MostrarPar = "S";
            $MostrarHandicap = "S";
            $MostrarScore = "S";
            $SQLDatosTabla = "SELECT IDGameGolfJuegoTarjeta, IDJugadorJuego,Posicion,Titulo,SubTitulo,Puntaje, Resultado, SobrePar,ResultadoGeneralPar,ResultadoGeneralResultado,ResultadoGeneralPosicion, EN 
                                      FROM GameGolfJuegoTarjeta" . $NombreTabla . "
                                      WHERE IDGameGolfJuego = $IDJuegoGolf and IDGameGolfFormato = '" . $IDFormatoJuego . "' and IDJugadorJuego = '" . $Identificador . "'";
        } elseif ($Tipo == "Grupo") {
            $MostrarPar = "S";
            $MostrarHandicap = "S";
            $MostrarScore = "S";
            $SQLDatosTabla = "SELECT IDGameGolfJuegoTarjeta, IDGameGolfJuegoGrupo,Posicion,Titulo,SubTitulo,Puntaje, Resultado, SobrePar,ResultadoGeneralPar,ResultadoGeneralResultado,ResultadoGeneralPosicion, EN 
                                      FROM GameGolfJuegoTarjeta" . $NombreTabla . " 
                                      WHERE IDGameGolfJuego = $IDJuegoGolf and IDGameGolfFormato = '" . $IDFormatoJuego . "' and IDGameGolfJuegoGrupo = '" . $Identificador . "'";
        } elseif ($Tipo = "SubGrupo") {
            $MostrarPar = "S";
            $MostrarHandicap = "S";
            $MostrarScore = "S";
            $SQLDatosTabla = "SELECT IDGameGolfJuegoTarjeta, IDGameGolfJuegoGrupo,Posicion,Titulo,SubTitulo,Puntaje, Resultado, SobrePar,ResultadoGeneralPar,ResultadoGeneralResultado,ResultadoGeneralPosicion, EN 
                                      FROM GameGolfJuegoTarjeta" . $NombreTabla . " 
                                      WHERE IDGameGolfJuego = $IDJuegoGolf and IDGameGolfFormato = '" . $IDFormatoJuego . "' and IDGameGolfJuegoFormatoSubGrupo = '" . $Identificador . "'";
        }

        $QRYDatosTabla = $dbo2->query($SQLDatosTabla);
        while ($DatosTabla = $dbo2->fetchArray($QRYDatosTabla)) :

            if ($Tipo == "Jugador") {
                $datos_jugador_grupo = $dbo2->fetchAll("GameGolfJuegoGrupoJugadores ", " IDJugador = '" . $DatosTabla["IDJugadorJuego"]  . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ", "array");
                $IDMarcaConsulta = $datos_jugador_grupo["IDGameGolfMarca"];
            } elseif ($Tipo == "Grupo") {
                $IDMarcaConsulta = $datos_juego["IDGameGolfMarca"];
            } elseif ($Tipo = "SubGrupo") {
                $IDMarcaConsulta = $datos_juego["IDGameGolfMarca"];
            }

            //Hoyos

            $sql_hoyos = "SELECT IDGameGolfCourse, Hoyo, Par, Handicap FROM GameGolfHoyos  WHERE IDGameGolfCourse = '" . $datos_juego["IDGolfCourse"] . "' and IDGameGolfMarca = '" . $IDMarcaConsulta . "' and Hoyo <= '" . $datos_juego["NumeroHoyos"] . "' ";
            $r_hoyos = $dbo2->query($sql_hoyos);
            while ($row_hoyos = $dbo2->fetchArray($r_hoyos)) {
                $array_hoyo[$row_hoyos["Hoyo"]]["Par"] = $row_hoyos["Par"];
                $array_hoyo[$row_hoyos["Hoyo"]]["Handicap"] = $row_hoyos["Handicap"];
                $TotalParCampo += $row_hoyos["Par"];
            }


            unset($array_detalle);
            $SumaPar = 0;
            $Signo = "";
            //Consulto el detalle de la tarjeta
            $sql_detalle = "SELECT Hoyo, Puntos, Resultado, Neto
                                  FROM GameGolfJuegoTarjetaDetalle" . $NombreTabla . "
                                  WHERE IDGameGolfJuegoTarjeta = '" . $DatosTabla["IDGameGolfJuegoTarjeta"] . "'";
            $r_detalle = $dbo2->query($sql_detalle);
            while ($row_detalle = $dbo2->fetchArray($r_detalle)) {
                $array_detalle[$row_detalle["Hoyo"]]["Resultado"] = $row_detalle["Resultado"];
                $array_detalle[$row_detalle["Hoyo"]]["Neto"] = $row_detalle["Neto"];
            }



            if ($Tipo == "Jugador") {
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $DatosTabla["IDJugadorJuego"] . "' ", "array");
                $dbo2 = &SIMDB2::get();
                $dblinkGame = $dbo2->connect(DBHOSTGame, DBNAMEGame, DBUSERGame, DBPASSGame);
                $datos_hcp = $dbo2->fetchAll("GameGolfJuegoGrupoJugadores", " IDJugador = '" . $DatosTabla["IDJugadorJuego"] . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ", "array");
                $Marca = $dbo2->getFields("GameGolfMarca", "Nombre", "IDGameGolfMarca = '" . $datos_hcp["IDGameGolfMarca"] . "'  ");
                $array_apellido = explode(" ", $datos_socio["Apellido"]);
                $Titulo = $datos_socio["Nombre"] . " " . $array_apellido[0] . " " . substr($array_apellido[1], 0, 1);
                $Subtitulo = "HCP: " . $datos_hcp["Handicap"] . "/" . $Marca;
            } elseif ($Tipo == "Grupo") {
                $sql_grupo = "SELECT IDGameGolfJuegoGrupo, Nombre 
                                    FROM GameGolfJuegoGrupo
                                    WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDGameGolfJuegoGrupo = '" . $Identificador . "' ";
                $r_grupo = $dbo2->query($sql_grupo);
                while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                    $Titulo = $row_grupo["Nombre"];
                    //Consulto jugadores grupos
                    $sql_jugador_grupo = "SELECT IDJugador FROM GameGolfJuegoGrupoJugadores WHERE IDGameGolfJuegoGrupo = '" . $row_grupo["IDGameGolfJuegoGrupo"] . "'  ";
                    $r_jugador_grupo = $dbo2->query($sql_jugador_grupo);
                    while ($row_jugador_grupo = $dbo2->fetchArray($r_jugador_grupo)) {
                        if ((int)$row_jugador_grupo["IDJugador"] > 0) {
                            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_jugador_grupo["IDJugador"] . "' ", "array");
                            $datos_hcp = $dbo2->fetchAll("GameGolfJuegoGrupoJugadores", " IDJugador = '" . $row_jugador_grupo["IDJugador"] . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ", "array");
                            $array_Jugadores[] = substr($datos_socio["Nombre"], 0, 1) . " " . substr($datos_socio["Apellido"], 0, 8) . " (" . $datos_hcp["Handicap"] . ")";
                        }
                    }
                    if (count($array_Jugadores) > 0) {
                        $Subtitulo = implode(",", $array_Jugadores);
                    }
                }
            } elseif ($Tipo == "SubGrupo") {
                $sql_grupo = "SELECT IDGameGolfJuegoFormatoSubGrupo 
                        FROM GameGolfJuegoFormatoSubGrupo 
                        WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDGameGolfJuegoFormatoSubGrupo = '" . $Identificador . "' ";
                $r_grupo = $dbo2->query($sql_grupo);
                while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                    //Consulto jugadores grupos
                    $sql_jugador_grupo = "SELECT IDJugador FROM GameGolfJuegoFormatoSubGrupoDetalle  WHERE IDGameGolfJuegoFormatoSubGrupo = '" . $row_grupo["IDGameGolfJuegoFormatoSubGrupo"] . "'  ";
                    $r_jugador_grupo = $dbo2->query($sql_jugador_grupo);
                    while ($row_jugador_grupo = $dbo2->fetchArray($r_jugador_grupo)) {
                        if ((int)$row_jugador_grupo["IDJugador"] > 0) {
                            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_jugador_grupo["IDJugador"] . "' ", "array");
                            $datos_hcp = $dbo2->fetchAll("GameGolfJuegoGrupoJugadores", " IDJugador = '" . $row_jugador_grupo["IDJugador"] . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ", "array");
                            $array_Jugadores[] = substr($datos_socio["Nombre"], 0, 1) . " " . substr($datos_socio["Apellido"], 0, 8) . " (" . $datos_hcp["Handicap"] . ")";
                        }
                    }
                    if (count($array_Jugadores) > 0) {
                        $Titulo = implode(",", $array_Jugadores);
                        $Subtitulo = "";
                    }
                }
            }

            $dbo2 = &SIMDB2::get();
            $dblinkGame = $dbo2->connect(DBHOSTGame, DBNAMEGame, DBUSERGame, DBPASSGame);

            $InfoTabla["Posicion"] = (string)$Posicion;
            $InfoTabla["Titulo"] = $Titulo;
            $InfoTabla["SubTitulo"] = $Subtitulo;
            $InfoTabla["Puntaje"] = $DatosTabla["Resultado"];

            if ($DatosTabla["SobrePar"] == 0) {
                $DatosTabla["SobrePar"] = "E";
            } elseif ($DatosTabla["SobrePar"] == "-9999") {
                $Signo = "";
                $DatosTabla["SobrePar"] = "NR";
            } elseif ($DatosTabla["SobrePar"] >= 0) {
                $Signo = "+";
            }



            $InfoTabla["PuntajeAlPar"] = $Signo . $DatosTabla["SobrePar"];
            $InfoTabla["ColorPuntaje"] = "#000000";
            if ($DatosTabla["SobrePar"] <= 0 && $DatosTabla["SobrePar"] != "E")
                $InfoTabla["ColorPuntajeAlPar"] = "#FF0501";
            else
                $InfoTabla["ColorPuntajeAlPar"] = "#000000";

            $InfoTabla["HoyoActual"] = (string)$DatosTabla["EN"];
            $InfoTabla["Tipo"] = $datos_formato["Tipo"];

            //Averiguo los resultados de la tarjetas
            $array_tarjeta_general["Par"] = (string)$TotalParCampo;
            $array_tarjeta_general["Resultado"] = (string)$DatosTabla["Puntaje"] . "/" . $DatosTabla["Resultado"];
            $array_tarjeta_general["Posicion"] = (string)$Posicion;

            $InfoTarjeta["ResultadoGeneral"] = $array_tarjeta_general;

            //resultados de la tarjetas



            //Tarjeta Ida
            $array_tarjeta_ida["MostrarPar"] = $MostrarPar;
            $array_tarjeta_ida["MostrarHandicap"] = $MostrarHandicap;
            $array_tarjeta_ida["MostrarScore"] = $MostrarScore;
            $array_tarjeta_ida["MostrarNeto"] = $MostrarNeto;
            for ($hoyo = 1; $hoyo <= 9; $hoyo++) {
                $SumaPar += (int)$array_hoyo[$hoyo]["Par"];
                $SumaHandicap += (int)$array_hoyo[$hoyo]["Handicap"];
                $SumaScore += $array_detalle[$hoyo]["Resultado"];
                $SumaNeto += $array_detalle[$hoyo]["Neto"];
                $array_resultado = self::datos_resultado($hoyo, $array_hoyo, $array_detalle, $Tipo);
                array_push($response_hoyo, $array_resultado);
            }
            $array_tarjeta_ida["Hoyos"] = $response_hoyo;
            //Totales
            $array_totales["Par"] = (string)$SumaPar;
            $array_totales["Handicap"] = "";
            $array_totales["Score"] = (string)$SumaScore;
            $array_totales["Neto"] = (string)$SumaNeto;
            $array_tarjeta_ida["Total"] = $array_totales;
            $InfoTarjeta["TarjetaIda"] = $array_tarjeta_ida;
            //FIN Tarjeta Ida

            //Tarjeta Vuelta                        
            //Averiguo si se jugo a 18 hoyos para mostrar la vuelta
            if ((int)$datos_juego["NumeroHoyos"] > 9) {
                $SumaPar = 0;
                $SumaHandicap = 0;
                $SumaScore = 0;
                $SumaNeto = 0;
                $array_tarjeta_vuelta["MostrarPar"] = $MostrarPar;
                $array_tarjeta_vuelta["MostrarHandicap"] = $MostrarHandicap;
                $array_tarjeta_vuelta["MostrarScore"] = $MostrarScore;
                $array_tarjeta_vuelta["MostrarNeto"] = $MostrarNeto;

                for ($hoyo = 10; $hoyo <= 18; $hoyo++) {
                    $SumaPar += (int)$array_hoyo[$hoyo]["Par"];
                    $SumaHandicap += (int)$array_hoyo[$hoyo]["Handicap"];
                    $SumaScore += $array_detalle[$hoyo]["Resultado"];
                    $SumaNeto += $array_detalle[$hoyo]["Neto"];
                    $array_resultado = self::datos_resultado($hoyo, $array_hoyo, $array_detalle, $Tipo);
                    array_push($response_hoyo_vuelta, $array_resultado);
                }
                $array_tarjeta_vuelta["Hoyos"] = $response_hoyo_vuelta;
                //Totales
                $array_totales["Par"] = (string)$SumaPar;
                $array_totales["Handicap"] = "";
                $array_totales["Score"] = (string)$SumaScore;
                $array_totales["Neto"] = (string)$SumaNeto;
                $array_tarjeta_vuelta["Total"] = $array_totales;
                $InfoTarjeta["TarjetaVuelta"] = $array_tarjeta_vuelta;
            } else {
                $InfoTarjeta["TarjetaVuelta"] = $array_tarjeta_vuelta;
            }
            //FIN Tarjeta vuelta
            $InfoTabla["Tarjeta"] = $InfoTarjeta;
        endwhile;

        return $InfoTabla;
    }

    public function consulta_resultados_vs($IDClub, $IDJuegoGolf, $IDFormatoJuego, $IDPartida, $Vista, $NombreCortoJ1, $NombreCortoJ2, $IDJugador1, $IDJugador2)
    {
        $dbo = SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response_hoyo = array();
        $response_hoyo_vuelta = array();

        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
        $datos_formato = $dbo2->fetchAll("GameGolfFormato", "IDGameGolfFormato  = '" . $IDFormatoJuego . "' ", "array");


        $resp = self::get_resultado_formato_juego_individual_versus($IDClub, $IDJugador1, $IDUsuario, $IDJuegoGolf, $IDFormatoJuego, $Vista, "S");

        $DatosJug1 = $resp["response"][0]["DetalleNetoJug1"];
        //$DatosJug1=$resp["response"][0]["DetalleGrossJug1"];
        $DatosJug2 = $resp["response"][0]["DetalleNetoJug2"];
        //$DatosJug2=$resp["response"][0]["DetalleGrossJug2"];


        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }
        $MostrarPar = "S";



        $datos_jugador_grupo = $dbo2->fetchAll("GameGolfJuegoGrupoJugadores ", " IDJugador = '" . $IDJugador1  . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ", "array");
        $IDMarcaConsulta = $datos_jugador_grupo["IDGameGolfMarca"];

        //Hoyos					
        $sql_hoyos = "SELECT IDGameGolfCourse, Hoyo, Par, Handicap FROM GameGolfHoyos  WHERE IDGameGolfCourse = '" . $datos_juego["IDGolfCourse"] . "' and IDGameGolfMarca = '" . $IDMarcaConsulta . "' and Hoyo <= '" . $datos_juego["NumeroHoyos"] . "' ";
        $r_hoyos = $dbo2->query($sql_hoyos);
        while ($row_hoyos = $dbo2->fetchArray($r_hoyos)) {
            $array_hoyo[$row_hoyos["Hoyo"]]["Par"] = $row_hoyos["Par"];
            $array_hoyo[$row_hoyos["Hoyo"]]["Handicap"] = $row_hoyos["Handicap"];
            $TotalParCampo += $row_hoyos["Par"];
        }

        unset($array_detalle);
        $SumaPar = 0;
        $Signo = "";
        //Tarjeta Ida
        $array_tarjeta_ida["MostrarPar"] = $MostrarPar;
        $array_tarjeta_ida["MostrarClasificaciones"] = "S";
        $array_tarjeta_ida["NombreCortoJugador1"] = $NombreCortoJ1;
        $array_tarjeta_ida["NombreCortoJugador2"] = $NombreCortoJ2;
        $Puntos = 0;
        $PuntosJ1 = 0;
        $PuntosJ2 = 0;
        for ($hoyo = 1; $hoyo <= 9; $hoyo++) {
            $SumaPar += (int)$array_hoyo[$hoyo]["Par"];
            $SumaScore += $DatosJug2[$hoyo]["Resultado"];
            $SumaNeto += $DatosJug1[$hoyo]["Neto"];

            if ((int)$DatosJug1[$hoyo] <= 0 || (int)$DatosJug2[$hoyo] <= 0) {
                $DatosJug1[$hoyo] = "";
                $DatosJug2[$hoyo] = "";
                $Calificaciones = "";
            } else {
                if ($DatosJug1[$hoyo] == $DatosJug2[$hoyo]) {
                    $Puntos = 0;
                    $ColorLetraJ1 = "#000000";
                    $ColorLetraJ2 = "#000000";
                } elseif ($DatosJug1[$hoyo] < $DatosJug2[$hoyo]) {
                    $PuntosJ1++;
                    $PuntosJ2--;
                    $ColorLetraJ1 = "#000000";
                    $ColorLetraJ2 = "#0628F9";
                } else {
                    $PuntosJ1--;
                    $PuntosJ2++;
                    $ColorLetraJ2 = "#000000";
                    $ColorLetraJ1 = "#EE271A";
                }

                if ($PuntosJ1 == $PuntosJ2) {
                    $Calificaciones = "E";
                    $ColorLetraClasificaciones = "#000000";
                } elseif ($PuntosJ1 >= $PuntosJ2) {
                    $Calificaciones = "+" . $PuntosJ1;
                    $ColorLetraClasificaciones = $ColorLetraJ2;
                } else {
                    $Calificaciones = "+" . $PuntosJ2;
                    $ColorLetraClasificaciones = $ColorLetraJ1;
                }
            }




            $array_respuesta["Numero"] = (string)$hoyo;
            $array_respuesta["Handicap"] = $array_hoyo[$hoyo]["Handicap"];
            $array_respuesta["Par"] = $array_hoyo[$hoyo]["Par"];
            $array_respuesta["Jugador1"] = $DatosJug2[$hoyo];
            $array_respuesta["Jugador2"] = $DatosJug1[$hoyo];
            $array_respuesta["Jugador1Fondo"] = "";
            $array_respuesta["Jugador1Color"] = $ColorLetraJ1;
            $array_respuesta["Jugador1Forma"] = "";
            $array_respuesta["Jugador2Fondo"] = "";
            $array_respuesta["Jugador2Color"] = $ColorLetraJ2;
            $array_respuesta["Jugador2Forma"] = "";
            $array_respuesta["Clasificaciones"] = $Calificaciones;
            $array_respuesta["ClasificacionesNegrilla"] = "S";
            $array_respuesta["ClasificacionesColor"] = $ColorLetraClasificaciones;
            if (!empty($Calificaciones))
                $UltimaCalificacion = $Calificaciones;

            array_push($response_hoyo, $array_respuesta);
        }
        $array_tarjeta_ida["Hoyos"] = $response_hoyo;
        //Totales
        $array_totales["Par"] = (string)$SumaPar;
        $array_totales["Jugador1"] = (string)$SumaScore;
        $array_totales["Jugador2"] = (string)$SumaNeto;
        $array_totales["Clasificaciones"] = $UltimaCalificacion;

        $array_tarjeta_ida["Total"] = $array_totales;
        $InfoTarjeta["TarjetaIda"] = $array_tarjeta_ida;
        //FIN Tarjeta Ida

        //Tarjeta Vuelta                        
        //Averiguo si se jugo a 18 hoyos para mostrar la vuelta
        if ((int)$datos_juego["NumeroHoyos"] > 9) {
            $SumaPar = 0;
            $SumaHandicap = 0;
            $SumaScore = 0;
            $SumaNeto = 0;
            $array_tarjeta_vuelta["MostrarPar"] = $MostrarPar;
            $array_tarjeta_vuelta["MostrarClasificaciones"] = "S";
            $array_tarjeta_vuelta["NombreCortoJugador1"] = $NombreCortoJ1;
            $array_tarjeta_vuelta["NombreCortoJugador2"] = $NombreCortoJ2;

            $Puntos = 0;
            $PuntosJ1 = 0;
            $PuntosJ2 = 0;
            $UltimaCalificacion = "";
            for ($hoyo = 10; $hoyo <= 18; $hoyo++) {
                $SumaPar += (int)$array_hoyo[$hoyo]["Par"];
                $SumaScore += $DatosJug2[$hoyo]["Resultado"];
                $SumaNeto += $DatosJug1[$hoyo]["Neto"];

                if ((int)$DatosJug1[$hoyo] <= 0 || (int)$DatosJug2[$hoyo] <= 0) {
                    $DatosJug1[$hoyo] = "";
                    $DatosJug2[$hoyo] = "";
                    $Calificaciones = "";
                } else {
                    if ($DatosJug1[$hoyo] == $DatosJug2[$hoyo]) {
                        $Puntos = 0;
                        $ColorLetraJ1 = "#000000";
                        $ColorLetraJ2 = "#000000";
                    } elseif ($DatosJug1[$hoyo] < $DatosJug2[$hoyo]) {
                        $PuntosJ1++;
                        $PuntosJ2--;
                        $ColorLetraJ1 = "#000000";
                        $ColorLetraJ2 = "#0628F9";
                    } else {
                        $PuntosJ1--;
                        $PuntosJ2++;
                        $ColorLetraJ2 = "#000000";
                        $ColorLetraJ1 = "#EE271A";
                    }

                    if ($PuntosJ1 == $PuntosJ2) {
                        $Calificaciones = "E";
                        $ColorLetraClasificaciones = "#000000";
                    } elseif ($PuntosJ1 >= $PuntosJ2) {
                        $Calificaciones = "+" . $PuntosJ1;
                        $ColorLetraClasificaciones = $ColorLetraJ2;
                    } else {
                        $Calificaciones = "+" . $PuntosJ2;
                        $ColorLetraClasificaciones = $ColorLetraJ1;
                    }
                }


                $array_respuesta["Numero"] = (string)$hoyo;
                $array_respuesta["Handicap"] = $array_hoyo[$hoyo]["Handicap"];
                $array_respuesta["Par"] = $array_hoyo[$hoyo]["Par"];
                $array_respuesta["Jugador1"] = $DatosJug2[$hoyo];
                $array_respuesta["Jugador2"] = $DatosJug1[$hoyo];
                $array_respuesta["Jugador1Fondo"] = "";
                $array_respuesta["Jugador1Color"] = $ColorLetraJ1;
                $array_respuesta["Jugador1Forma"] = "";
                $array_respuesta["Jugador2Fondo"] = "";
                $array_respuesta["Jugador2Color"] = $ColorLetraJ2;
                $array_respuesta["Jugador2Forma"] = "";
                $array_respuesta["Clasificaciones"] = $Calificaciones;
                $array_respuesta["ClasificacionesNegrilla"] = "S";
                $array_respuesta["ClasificacionesColor"] = $ColorLetraClasificaciones;
                if (!empty($Calificaciones))
                    $UltimaCalificacion = $Calificaciones;

                array_push($response_hoyo_vuelta, $array_respuesta);
            }
            $array_tarjeta_vuelta["Hoyos"] = $response_hoyo_vuelta;
            //Totales
            $array_totales["Par"] = (string)$SumaPar;
            $array_totales["Jugador1"] = (string)$SumaScore;
            $array_totales["Jugador2"] = (string)$SumaNeto;
            $array_totales["Clasificaciones"] = $UltimaCalificacion;
            $array_tarjeta_vuelta["Total"] = $array_totales;
            $InfoTarjeta["TarjetaVuelta"] = $array_tarjeta_vuelta;
        } else {
            $InfoTarjeta["TarjetaVuelta"] = $array_tarjeta_vuelta;
        }
        //FIN Tarjeta vuelta                    


        return $InfoTarjeta;
    }

    public function consulta_resultados_pareja_vs($IDClub, $IDSocio, $IDJuegoGolf, $IDFormatoJuego, $IDPartida, $Vista, $NombreCortoJ1, $NombreCortoJ2, $IDllave)
    {
        $dbo = SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response_hoyo = array();
        $response_hoyo_vuelta = array();

        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
        $datos_formato = $dbo2->fetchAll("GameGolfFormato", "IDGameGolfFormato  = '" . $IDFormatoJuego . "' ", "array");

        $resp = self::get_resultado_formato_juego_parejas_versus($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatoJuego, $Vista, "S");

        //Neto
        $DatosJug1 = $resp["response"][0]["DetalleNetoJug1"];
        //$DatosJug1=$resp["response"][0]["DetalleGrossJug1"];
        $DatosJug2 = $resp["response"][0]["DetalleNetoJug2"];
        //$DatosJug2=$resp["response"][0]["DetalleGrossJug2"];

        //Suma
        $DatosJug1Suma = $resp["response"][0]["DetalleNetoJug1Suma"];
        //$DatosJug1Suma=$resp["response"][0]["DetalleGrossJug1Suma"];
        $DatosJug2Suma = $resp["response"][0]["DetalleNetoJug2Suma"];
        //$DatosJug2Suma=$resp["response"][0]["DetalleGrossJug2Suma"];



        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }
        $MostrarPar = "S";



        $datos_jugador_grupo = $dbo2->fetchAll("GameGolfJuegoGrupoJugadores ", " IDJugador = '" . $IDSocio  . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ", "array");
        $IDMarcaConsulta = $datos_jugador_grupo["IDGameGolfMarca"];

        //Hoyos					
        $sql_hoyos = "SELECT IDGameGolfCourse, Hoyo, Par, Handicap FROM GameGolfHoyos  WHERE IDGameGolfCourse = '" . $datos_juego["IDGolfCourse"] . "' and IDGameGolfMarca = '" . $IDMarcaConsulta . "' and Hoyo <= '" . $datos_juego["NumeroHoyos"] . "' ";
        $r_hoyos = $dbo2->query($sql_hoyos);
        while ($row_hoyos = $dbo2->fetchArray($r_hoyos)) {
            $array_hoyo[$row_hoyos["Hoyo"]]["Par"] = $row_hoyos["Par"];
            $array_hoyo[$row_hoyos["Hoyo"]]["Handicap"] = $row_hoyos["Handicap"];
            $TotalParCampo += $row_hoyos["Par"];
        }

        unset($array_detalle);
        $SumaPar = 0;
        $Signo = "";
        //Tarjeta Ida
        $array_tarjeta_ida["MostrarPar"] = $MostrarPar;
        $array_tarjeta_ida["MostrarClasificaciones"] = "S";
        $array_tarjeta_ida["NombreCortoPareja1"] = $NombreCortoJ1;
        $array_tarjeta_ida["NombreCortoPareja2"] = $NombreCortoJ2;
        $array_tarjeta_ida["TituloMejorBola"] = "Resultados Mejor Bola";
        $array_tarjeta_ida["TituloSuma"] = "Resultados Suma";
        $Puntos = 0;
        $PuntosJ1 = 0;
        $PuntosJ2 = 0;
        for ($hoyo = 1; $hoyo <= 9; $hoyo++) {

            $SumaPar += (int)$array_hoyo[$hoyo]["Par"];
            $SumaScoreP1 += $DatosJug1[$hoyo]["Resultado"];
            $SumaScoreP2 += $DatosJug2[$hoyo]["Resultado"];
            $SumaScoreSumaP1 += $DatosJug1Suma[$hoyo];
            $SumaScoreSumaP2 += $DatosJug2Suma[$hoyo];
            $SumaNeto += $DatosJug1[$hoyo]["Neto"];


            //Mejor bola
            if ((int)$DatosJug1[$hoyo] <= 0 || (int)$DatosJug2[$hoyo] <= 0) {
                $DatosJug1[$hoyo] = "";
                $DatosJug2[$hoyo] = "";
                $Calificaciones = "";
            } else {
                if ($DatosJug1[$hoyo] == $DatosJug2[$hoyo]) {
                    $Puntos = 0;
                    $ColorLetraJ1 = "#000000";
                    $ColorLetraJ2 = "#000000";
                } elseif ($DatosJug1[$hoyo] < $DatosJug2[$hoyo]) {
                    $PuntosJ1++;
                    $PuntosJ2--;
                    $ColorLetraJ1 = "#EE271A";
                    $ColorLetraJ2 = "#000000";
                } else {
                    $PuntosJ1--;
                    $PuntosJ2++;
                    $ColorLetraJ2 = "#0628F9";
                    $ColorLetraJ1 = "#000000";
                }

                if ($PuntosJ1 == $PuntosJ2) {
                    $Calificaciones = "E";
                    $ColorLetraClasificaciones = "#000000";
                } elseif ($PuntosJ1 >= $PuntosJ2) {
                    $Calificaciones = "+" . $PuntosJ1;
                    $ColorLetraClasificaciones = $ColorLetraJ1;
                } else {
                    $Calificaciones = "+" . $PuntosJ2;
                    $ColorLetraClasificaciones = $ColorLetraJ2;
                }
            }
            //Fin Mejor Bola  

            //SUMA
            if ((int)$DatosJug1Suma[$hoyo] <= 0 || (int)$DatosJug2Suma[$hoyo] <= 0) {
                $DatosJug1Suma[$hoyo] = "";
                $DatosJug2Suma[$hoyo] = "";
                $CalificacionesSuma = "";
            } else {
                if ($DatosJug1Suma[$hoyo] == $DatosJug2Suma[$hoyo]) {
                    $Puntos = 0;
                    $ColorLetraJ1Suma = "#000000";
                    $ColorLetraJ2Suma = "#000000";
                } elseif ($DatosJug1Suma[$hoyo] < $DatosJug2Suma[$hoyo]) {
                    $PuntosJ1Suma++;
                    $PuntosJ2Suma--;
                    $ColorLetraJ1Suma = "#EE271A";
                    $ColorLetraJ2Suma = "#000000";
                } else {
                    $PuntosJ1Suma--;
                    $PuntosJ2Suma++;
                    $ColorLetraJ2Suma = "#0628F9";
                    $ColorLetraJ1Suma = "#000000";
                }

                if ($PuntosJ1Suma == $PuntosJ2Suma) {
                    $CalificacionesSuma = "E";
                    $ColorLetraClasificacionesSuma = "#000000";
                } elseif ($PuntosJ1Suma >= $PuntosJ2Suma) {
                    $CalificacionesSuma = "+" . $PuntosJ1Suma;
                    $ColorLetraClasificacionesSuma = $ColorLetraJ1Suma;
                } else {
                    $CalificacionesSuma = "+" . $PuntosJ2Suma;
                    $ColorLetraClasificacionesSuma = $ColorLetraJ2Suma;
                }
            }
            //Fin Mejor Bola  





            $array_respuesta["Numero"] = (string)$hoyo;
            $array_respuesta["Par"] = $array_hoyo[$hoyo]["Par"];


            $array_mejorbola["Pareja1"] = $DatosJug1[$hoyo];
            $array_mejorbola["Pareja2"] = $DatosJug2[$hoyo];
            $array_mejorbola["Pareja1Fondo"] = "";
            $array_mejorbola["Pareja1Color"] = $ColorLetraJ1;
            $array_mejorbola["Pareja1Forma"] = "";
            $array_mejorbola["Pareja2Fondo"] = "";
            $array_mejorbola["Pareja2Color"] = $ColorLetraJ2;
            $array_mejorbola["Pareja2Forma"] = "";
            $array_mejorbola["Clasificaciones"] = $Calificaciones;
            $array_mejorbola["ClasificacionesNegrilla"] = "S";
            $array_mejorbola["ClasificacionesColor"] = $ColorLetraClasificaciones;
            if (!empty($Calificaciones))
                $UltimaCalificacion = $Calificaciones;

            $array_respuesta["MejorBola"] = $array_mejorbola;

            $array_suma["Pareja1"] = (string)$DatosJug1Suma[$hoyo];
            $array_suma["Pareja2"] = (string)$DatosJug2Suma[$hoyo];
            $array_suma["Pareja1Fondo"] = "";
            $array_suma["Pareja1Color"] = $ColorLetraJ1Suma;
            $array_suma["Pareja1Forma"] = "";
            $array_suma["Pareja2Fondo"] = "";
            $array_suma["Pareja2Color"] = $ColorLetraJ2Suma;
            $array_suma["Pareja2Forma"] = "";
            $array_suma["Clasificaciones"] = $CalificacionesSuma;
            $array_suma["ClasificacionesNegrilla"] = "S";
            $array_suma["ClasificacionesColor"] = $ColorLetraClasificacionesSuma;
            if (!empty($CalificacionesSuma))
                $UltimaCalificacionSuma = $CalificacionesSuma;

            $array_respuesta["Suma"] = $array_suma;


            array_push($response_hoyo, $array_respuesta);
        }
        $array_tarjeta_ida["Hoyos"] = $response_hoyo;
        //Totales
        $array_totales["Par"] = (string)$SumaPar;
        $array_total_mejor_bola["Pareja1"] = (string)$SumaScoreP1;
        $array_total_mejor_bola["Pareja2"] = (string)$SumaScoreP2;
        $array_total_mejor_bola["Clasificaciones"] = $UltimaCalificacion;
        $array_totales["MejorBola"] = $array_total_mejor_bola;
        $array_total_suma["Pareja1"] = (string)$SumaScoreSumaP1;
        $array_total_suma["Pareja2"] = (string)$SumaScoreSumaP2;
        $array_total_suma["Clasificaciones"] = $UltimaCalificacionSuma;
        $array_totales["Suma"] = $array_total_suma;


        $array_tarjeta_ida["Total"] = $array_totales;
        $InfoTarjeta["TarjetaIda"] = $array_tarjeta_ida;
        //FIN Tarjeta Ida

        //Tarjeta Vuelta                        
        //Averiguo si se jugo a 18 hoyos para mostrar la vuelta
        if ((int)$datos_juego["NumeroHoyos"] > 9) {
            $SumaPar = 0;
            $SumaHandicap = 0;
            $SumaScore = 0;
            $SumaNeto = 0;
            $UltimaCalificacionSuma = "";
            $array_tarjeta_vuelta["MostrarPar"] = $MostrarPar;
            $array_tarjeta_vuelta["MostrarClasificaciones"] = "S";
            $array_tarjeta_vuelta["NombreCortoPareja1"] = $NombreCortoJ1;
            $array_tarjeta_vuelta["NombreCortoPareja2"] = $NombreCortoJ1;
            $array_tarjeta_vuelta["TituloMejorBola"] = "Resultados Mejor Bola";
            $array_tarjeta_vuelta["TituloSuma"] = "Resultados Suma";

            $Puntos = 0;
            $PuntosJ1 = 0;
            $PuntosJ2 = 0;
            $PuntosJ1Suma = 0;
            $PuntosJ2Suma = 0;
            $SumaScoreP1 = 0;
            $SumaScoreP2 = 0;
            $SumaScoreSumaP1 = 0;
            $SumaScoreSumaP2 = 0;
            $UltimaCalificacion = "";
            for ($hoyo = 10; $hoyo <= 18; $hoyo++) {
                $SumaPar += (int)$array_hoyo[$hoyo]["Par"];
                $SumaScoreP1 += $DatosJug1[$hoyo]["Resultado"];
                $SumaScoreP2 += $DatosJug2[$hoyo]["Resultado"];
                $SumaScoreSumaP1 += $DatosJug1Suma[$hoyo];
                $SumaScoreSumaP2 += $DatosJug2Suma[$hoyo];
                $SumaNeto += $DatosJug1[$hoyo]["Neto"];

                //Mejor bola
                if ((int)$DatosJug1[$hoyo] <= 0 || (int)$DatosJug2[$hoyo] <= 0) {
                    $DatosJug1[$hoyo] = "";
                    $DatosJug2[$hoyo] = "";
                    $Calificaciones = "";
                } else {
                    if ($DatosJug1[$hoyo] == $DatosJug2[$hoyo]) {
                        $Puntos = 0;
                        $ColorLetraJ1 = "#000000";
                        $ColorLetraJ2 = "#000000";
                    } elseif ($DatosJug1[$hoyo] < $DatosJug2[$hoyo]) {
                        $PuntosJ1++;
                        $PuntosJ2--;
                        $ColorLetraJ1 = "#EE271A";
                        $ColorLetraJ2 = "#000000";
                    } else {
                        $PuntosJ1--;
                        $PuntosJ2++;
                        $ColorLetraJ2 = "#0628F9";
                        $ColorLetraJ1 = "#000000";
                    }

                    if ($PuntosJ1 == $PuntosJ2) {
                        $Calificaciones = "E";
                        $ColorLetraClasificaciones = "#000000";
                    } elseif ($PuntosJ1 >= $PuntosJ2) {
                        $Calificaciones = "+" . $PuntosJ1;
                        $ColorLetraClasificaciones = $ColorLetraJ1;
                    } else {
                        $Calificaciones = "+" . $PuntosJ2;
                        $ColorLetraClasificaciones = $ColorLetraJ2;
                    }
                }
                //Fin Mejor Bola  

                //SUMA
                if ((int)$DatosJug1Suma[$hoyo] <= 0 || (int)$DatosJug2Suma[$hoyo] <= 0) {
                    $DatosJug1Suma[$hoyo] = "";
                    $DatosJug2Suma[$hoyo] = "";
                    $CalificacionesSuma = "";
                } else {
                    if ($DatosJug1Suma[$hoyo] == $DatosJug2Suma[$hoyo]) {
                        $Puntos = 0;
                        $ColorLetraJ1Suma = "#000000";
                        $ColorLetraJ2Suma = "#000000";
                    } elseif ($DatosJug1Suma[$hoyo] < $DatosJug2Suma[$hoyo]) {
                        $PuntosJ1Suma++;
                        $PuntosJ2Suma--;
                        $ColorLetraJ1Suma = "#EE271A";
                        $ColorLetraJ2Suma = "#000000";
                    } else {
                        $PuntosJ1Suma--;
                        $PuntosJ2Suma++;
                        $ColorLetraJ2Suma = "#0628F9";
                        $ColorLetraJ1Suma = "#000000";
                    }

                    if ($PuntosJ1Suma == $PuntosJ2Suma) {
                        $CalificacionesSuma = "E";
                        $ColorLetraClasificacionesSuma = "#000000";
                    } elseif ($PuntosJ1Suma >= $PuntosJ2Suma) {
                        $CalificacionesSuma = "+" . $PuntosJ1Suma;
                        $ColorLetraClasificacionesSuma = $ColorLetraJ1Suma;
                    } else {
                        $CalificacionesSuma = "+" . $PuntosJ2Suma;
                        $ColorLetraClasificacionesSuma = $ColorLetraJ2Suma;
                    }
                }
                //Fin Mejor Bola  


                $array_respuesta["Numero"] = (string)$hoyo;
                $array_respuesta["Par"] = $array_hoyo[$hoyo]["Par"];

                $array_mejorbola["Pareja1"] = $DatosJug1[$hoyo];
                $array_mejorbola["Pareja2"] = $DatosJug2[$hoyo];
                $array_mejorbola["Pareja1Fondo"] = "";
                $array_mejorbola["Pareja1Color"] = $ColorLetraJ1;
                $array_mejorbola["Pareja1Forma"] = "";
                $array_mejorbola["Pareja2Fondo"] = "";
                $array_mejorbola["Pareja2Color"] = $ColorLetraJ2;
                $array_mejorbola["Pareja2Forma"] = "";
                $array_mejorbola["Clasificaciones"] = $Calificaciones;
                $array_mejorbola["ClasificacionesNegrilla"] = "S";
                $array_mejorbola["ClasificacionesColor"] = $ColorLetraClasificaciones;
                if (!empty($Calificaciones))
                    $UltimaCalificacion = $Calificaciones;

                $array_respuesta["MejorBola"] = $array_mejorbola;

                $array_suma["Pareja1"] = (string)$DatosJug1Suma[$hoyo];
                $array_suma["Pareja2"] = (string)$DatosJug2Suma[$hoyo];
                $array_suma["Pareja1Fondo"] = "";
                $array_suma["Pareja1Color"] = $ColorLetraJ1Suma;;
                $array_suma["Pareja1Forma"] = "";
                $array_suma["Pareja2Fondo"] = "";
                $array_suma["Pareja2Color"] = $ColorLetraJ2Suma;;
                $array_suma["Pareja2Forma"] = "";
                $array_suma["Clasificaciones"] = $CalificacionesSuma;
                $array_suma["ClasificacionesNegrilla"] = "S";
                $array_suma["ClasificacionesColor"] = $ColorLetraClasificacionesSuma;
                if (!empty($CalificacionesSuma))
                    $UltimaCalificacionSuma = $CalificacionesSuma;

                $array_respuesta["Suma"] = $array_suma;


                array_push($response_hoyo_vuelta, $array_respuesta);
            }
            $array_tarjeta_vuelta["Hoyos"] = $response_hoyo_vuelta;
            //Totales
            $array_totales["Par"] = (string)$SumaPar;
            $array_total_mejor_bola["Pareja1"] = (string)$SumaScoreP1;
            $array_total_mejor_bola["Pareja2"] = (string)$SumaScoreP2;
            $array_total_mejor_bola["Clasificaciones"] = $UltimaCalificacion;
            $array_totales["MejorBola"] = $array_total_mejor_bola;
            $array_total_suma["Pareja1"] = (string)$SumaScoreSumaP1;
            $array_total_suma["Pareja2"] = (string)$SumaScoreSumaP2;
            $array_total_suma["Clasificaciones"] = $UltimaCalificacionSuma;
            $array_totales["Suma"] = $array_total_suma;
            $array_tarjeta_vuelta["Total"] = $array_totales;

            $InfoTarjeta["TarjetaVuelta"] = $array_tarjeta_vuelta;
        } else {
            $InfoTarjeta["TarjetaVuelta"] = $array_tarjeta_vuelta;
        }
        //FIN Tarjeta vuelta                    


        return $InfoTarjeta;
    }

    public function get_juego_golf_resultado_formato_juego($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatoJuego, $Vista, $IDPartida)
    {
        $dbo = SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response_tabla_puntaje = array();
        $Posicion = 0;

        if (!empty($TipoConsulta))
            $IDFormatoJuego = 1;

        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }

        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
        if ((int)$datos_juego["IDGameGolfJuego"] > 0) {

            $InfoResponse["IDJuegoGolf"] = $IDJuegoGolf;
            $InfoResponse["IDFormatoJuego"] = $IDFormatoJuego;
            $datos_formato = $dbo2->fetchAll("GameGolfFormato", " IDGameGolfFormato = '" . $IDFormatoJuego . "' ", "array");
            $InfoResponse["Nombre"] = $datos_formato["Nombre"];

            if ($datos_formato["Tipo"] == "individual") {
                //Busco los grupos del juego para ver el resultado por jugador
                $sql_grupo = "SELECT GGJGJ.IDGameGolfJuegoGrupo, GGJGJ.IDJugador 
                                FROM GameGolfJuegoGrupoJugadores GGJGJ, GameGolfJuegoTarjeta" . $NombreTabla . " GGJT
                                WHERE GGJGJ.IDJugador = GGJT.IDJugadorJuego and GGJGJ.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJT.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJT.IDGameGolfFormato = '" . $IDFormatoJuego . "' ORDER BY GGJT.SobrePar ASC ";

                $r_grupo = $dbo2->query($sql_grupo);
                while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                    if ((int)$row_grupo["IDJugador"] > 0) {
                        $Posicion++;
                        $resultado = self::consulta_resultados($IDJuegoGolf, $IDFormatoJuego, $row_grupo["IDJugador"], "Jugador", $Posicion, $Vista);
                        if (is_array($resultado))
                            $response_tabla_puntaje[] = $resultado;
                    }
                }
            } elseif ($datos_formato["Tipo"] == "grupal") {
                $Posicion = 0;
                $sql_grupo = "SELECT GGJG.IDGameGolfJuegoGrupo
                                FROM GameGolfJuegoGrupo GGJG, GameGolfJuegoTarjeta" . $NombreTabla . " GGJT
                                WHERE GGJG.IDGameGolfJuegoGrupo = GGJT.IDGameGolfJuegoGrupo and GGJG.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJT.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJT.IDGameGolfFormato = '" . $IDFormatoJuego . "'
                                ORDER BY GGJT.SobrePar ASC";
                $r_grupo = $dbo2->query($sql_grupo);
                while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                    //Consulto tarjeta resultados por grupo                        
                    $Posicion++;
                    $resultado = self::consulta_resultados($IDJuegoGolf, $IDFormatoJuego, $row_grupo["IDGameGolfJuegoGrupo"], "Grupo", $Posicion, $Vista);
                    if (is_array($resultado))
                        $response_tabla_puntaje[] = $resultado;
                }
            } elseif ($datos_formato["Tipo"] == "sub_grupal") {
                $Posicion = 0;
                $sql_grupo = "SELECT DISTINCT(GGJFS.IDGameGolfJuegoFormatoSubGrupo)
                                    FROM GameGolfJuegoFormatoSubGrupo GGJFS, GameGolfJuegoTarjeta" . $NombreTabla . " GGJT
                                    WHERE GGJFS.IDGameGolfJuegoFormatoSubGrupo = GGJT.IDGameGolfJuegoFormatoSubGrupo and GGJFS.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJT.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJT.IDGameGolfFormato = '" . $IDFormatoJuego . "' and GGJFS.IDGameGolfJuegoFormato = '" . $IDPartida . "'
                                    ORDER BY GGJT.SobrePar ASC";
                $r_grupo = $dbo2->query($sql_grupo);
                while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                    //Consulto tarjeta resultados por grupo                        
                    $Posicion++;
                    $resultado = self::consulta_resultados($IDJuegoGolf, $IDFormatoJuego, $row_grupo["IDGameGolfJuegoFormatoSubGrupo"], "SubGrupo", $Posicion, $Vista);
                    if (is_array($resultado))
                        $response_tabla_puntaje[] = $resultado;
                }
            }



            $InfoResponse["TablaPuntaje"] = $response_tabla_puntaje;

            $respuesta[message] = "Datos encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $InfoResponse;
        } else {
            $respuesta[message] = "Juego no encontrado";
            $respuesta[success] = false;
            $respuesta[response] = $InfoResponse;
        }

        return $respuesta;
    }

    public function get_juego_golf_configurado($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf)
    {
        if ((int)$IDJuegoGolf > 0) {
            $resp = self::get_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf);
            $response_juego = $resp["response"];
            $respuesta[message] = "Encontrado";
            $respuesta[success] = true;
            $respuesta[response] = $response_juego;
        } else {
            $respuesta[message] = "GGJ2. Hubo un problema al tratar de consultar el juego!";
            $respuesta[success] = false;
            $respuesta[response] = null;
        }
        return $respuesta;
    }

    public function actualizar_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $FormatosJuegoCreados, $Grupos, $IDMarca)
    {
        $dbo = &SIMDB::get();
        $dbo2 = &SIMDB2::get();

        if (!empty($IDSocio) && !empty($IDJuegoGolf) && !empty($FormatosJuegoCreados)) {


            $sql_actualiza = "UPDATE GameGolfJuego SET IDGameGolfMarca = '" . $IDMarca . "' WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $dbo2->query($sql_actualiza);
            //Actualizar los  grupos
            $Grupos = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($Grupos));
            $datos_grupo = json_decode($Grupos, true);

            //primero consulto quienes estan en los grupos para ver si se elimina o se cambia de grupo
            $sql_grupos_actual = "SELECT IDJugador FROM GameGolfJuegoGrupoJugadores WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $r_grupos_actual = $dbo2->query($sql_grupos_actual);
            while ($row_grupos_actual = $dbo2->fetchArray($r_grupos_actual)) {
                $array_jugadores_actual[] = $row_grupos_actual["IDJugador"];
            }

            if (count($datos_grupo) > 0) {
                foreach ($datos_grupo as $detalle_datos) {
                    $datos_participante = $detalle_datos["Participantes"];
                    if (count($datos_participante) > 0) {
                        foreach ($datos_participante as $detalle_participante) {
                            $array_jugadores_nuevo[] = $detalle_participante["IDJugador"];
                        }
                    }
                }
            }
            foreach ($array_jugadores_actual as $id_jugador_actual) {
                if (!in_array($id_jugador_actual, $array_jugadores_nuevo)) {
                    //Elimino el jugador y sus resultados de la partida
                    $sql_borra = "DELETE FROM GameGolfJuegoGrupoJugadores WHERE IDJugador = '" . $id_jugador_actual . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
                    $dbo2->query($sql_borra);
                    $sql_borra = "DELETE FROM GameGolfJuegoTarjeta WHERE IDJugadorJuego = '" . $id_jugador_actual . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
                    $dbo2->query($sql_borra);
                    $sql_borra = "DELETE FROM GameGolfJuegoTarjetaDetalle  WHERE IDJugadorJuego = '" . $id_jugador_actual . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
                    $dbo2->query($sql_borra);
                    $sql_borra = "DELETE FROM GameGolfGolpe WHERE IDJugador = '" . $id_jugador_actual . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
                    $dbo2->query($sql_borra);
                }
            }





            $FormatosJuegoCreados = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($FormatosJuegoCreados));
            $datos_formato = json_decode($FormatosJuegoCreados, true);
            if (count($datos_formato) > 0) {

                //Actualizar los formatos
                $sql_borra_formato = "DELETE FROM GameGolfJuegoFormato WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "'";
                $dbo2->query($sql_borra_formato);
                $sql_borra_formato = "DELETE FROM GameGolfJuegoTarjeta WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "'";
                $dbo2->query($sql_borra_formato);
                $sql_borra_formato = "DELETE FROM GameGolfJuegoTarjetaDetalle WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "'";
                $dbo2->query($sql_borra_formato);
                $sql_borra_formato_llave = "DELETE FROM GameGolfJuegoFormatoLlaves  WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "'";
                $dbo2->query($sql_borra_formato_llave);
                $sql_borra_formato_llave = "DELETE FROM GameGolfJuegoFormatoSubGrupo WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "'";
                $dbo2->query($sql_borra_formato_llave);
                $sql_borra_formato_llave = "DELETE FROM GameGolfJuegoFormatoSubGrupoDetalle  WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "'";
                $dbo2->query($sql_borra_formato_llave);
                $sql_borra = "DELETE FROM  GameGolfJuegoFormatoLlaves  WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
                $dbo2->query($sql_borra);

                foreach ($datos_formato as $detalle_datos) {


                    if (empty($detalle_datos["UsarHandicap"]))
                        $UsarHcp = "S";
                    else
                        $UsarHcp = $detalle_datos["UsarHandicap"];

                    $sql_inserta_grupo = "INSERT INTO GameGolfJuegoFormato   (IDClub, IDGameGolfJuego,IDGameGolfFormato, IDJugadorPrincipal, IDJugadorJuego, UsarHandicap, UsarHandicapCampo, PorcentajeHandicap, VentajaSeleccionada, NumeroDeResultados, JuegoPorHoyosMatch, JuegoPorGolpesMedal, MenorHandicapBajaCero, UsarMejorBolaDelGrupo, UsarSumaDelGrupo, UsuarioTrCr, FechaTrCr ) 
                                            VALUES ('" . $IDClub . "','" . $IDJuegoGolf . "','" . $detalle_datos["IDFormatoJuego"] . "','" . $detalle_datos["IDJugador1"] . "','" . $detalle_datos["IDJugador2"] . "','" . $UsarHcp . "', '" . $detalle_datos["UsarHandicapCampo"] . "', '" . $detalle_datos["PorcentajeHandicap"] . "', '" . $detalle_datos["VentajaSeleccionada"] . "', '" . $detalle_datos["NumeroDeResultados"] . "','" . $detalle_datos["JuegoPorHoyosMatch"] . "','" . $detalle_datos["JuegoPorGolpesMedal"] . "','" . $detalle_datos["MenorHandicapBajaCero"] . "','" . $detalle_datos["UsarMejorBolaDelGrupo"] . "','" . $detalle_datos["UsarSumaDelGrupo"] . "', 'APP',NOW())";
                    $dbo2->query($sql_inserta_grupo);
                    $IDFormato = $dbo2->lastID();

                    $datos_llaves = $detalle_datos["Pareja1"];
                    if (count($datos_llaves) > 0 && is_array($detalle_datos["Pareja1"])) {
                        $IDJugador1Pareja1 = $detalle_datos["Pareja1"]["IDJugador1"];
                        $IDJugador2Pareja1 = $detalle_datos["Pareja1"]["IDJugador2"];
                        $IDJugador1Pareja2 = $detalle_datos["Pareja2"]["IDJugador1"];
                        $IDJugador2Pareja2 = $detalle_datos["Pareja2"]["IDJugador2"];
                        $sql_inserta_llave = "INSERT INTO GameGolfJuegoFormatoLlaves    (IDClub, IDGameGolfJuego,IDGameGolfFormato, IDGameGolfJuegoFormato, IDJugador1Pareja1, IDJugador2Pareja1, IDJugador1Pareja2, IDJugador2Pareja2, UsuarioTrCr, FechaTrCr ) 
                                                    VALUES ('" . $IDClub . "','" . $IDJuegoGolf . "','" . $detalle_datos["IDFormatoJuego"] . "','" . $IDFormato . "','" . $IDJugador1Pareja1 . "', '" . $IDJugador2Pareja1 . "', '" . $IDJugador1Pareja2 . "', '" . $IDJugador2Pareja2 . "',  'APP',NOW())";
                        $dbo2->query($sql_inserta_llave);
                    }

                    //Datos subgrupos
                    $datos_subgrupo = $detalle_datos["SubGrupos"];
                    if (count($datos_subgrupo) > 0) {
                        foreach ($datos_subgrupo as $datos_llave) {
                            $sql_inserta_llave = "INSERT INTO GameGolfJuegoFormatoSubGrupo  (IDClub, IDGameGolfJuego,IDGameGolfFormato, IDGameGolfJuegoFormato, UsuarioTrCr, FechaTrCr ) 
                                                    VALUES ('" . $IDClub . "','" . $IDJuegoGolf . "','" . $detalle_datos["IDFormatoJuego"] . "','" . $IDFormato . "','APP',NOW())";
                            $dbo2->query($sql_inserta_llave);
                            $IDSubGrupo = $dbo2->lastID();
                            $datos_jugador = $datos_llave["Jugadores"];
                            if (count($datos_jugador) > 0) {
                                foreach ($datos_jugador as $datos_jugador) {
                                    $sql_inserta_llave = "INSERT INTO GameGolfJuegoFormatoSubGrupoDetalle   (IDGameGolfJuego,IDGameGolfJuegoFormatoSubGrupo, IDJugador, UsuarioTrCr, FechaTrCr ) 
                                                    VALUES ('" . $IDJuegoGolf . "','" . $IDSubGrupo . "','" . $datos_jugador["IDJugador"] . "','APP',NOW())";
                                    $dbo2->query($sql_inserta_llave);
                                }
                            }
                        }
                    }
                }
            }

            //Actualizar los  grupos
            if (count($datos_grupo) > 0) {
                foreach ($datos_grupo as $detalle_datos) {
                    $IDGrupo = $detalle_datos["IDGrupo"];

                    if (!is_numeric($IDGrupo)) {
                        $sql_inserta_grupo = "INSERT INTO GameGolfJuegoGrupo  (IDClub, IDGameGolfJuego, Nombre, HoyoInicial, UsuarioTrCr, FechaTrCr ) 
                                                VALUES ('" . $IDClub . "','" . $IDJuegoGolf . "','" . $detalle_datos["Nombre"] . "','" . $detalle_datos["HoyoInicial"] . "','APP',NOW())";
                        $dbo2->query($sql_inserta_grupo);
                        $IDGrupo = $dbo2->lastID();
                    } else {
                        $sql_actualiza_grupo = "UPDATE GameGolfJuegoGrupo SET HoyoInicial = '" . $detalle_datos["HoyoInicial"] . "' WHERE IDGameGolfJuegoGrupo = '" . $IDGrupo . "' ";
                        $dbo2->query($sql_actualiza_grupo);
                    }

                    $datos_participante = $detalle_datos["Participantes"];
                    if (count($datos_participante) > 0) {
                        foreach ($datos_participante as $detalle_participante) {
                            if (in_array($detalle_participante["IDJugador"], $array_jugadores_actual)) {
                                $sql_participante = "UPDATE GameGolfJuegoGrupoJugadores 
                                                        SET IDGameGolfMarca='" . $detalle_participante["IDMarca"] . "',Handicap='" . $detalle_participante["Handicap"] . "',UsuarioTrEd='WS Modifica Grupo',FechaTrEd= NOW()
                                                        WHERE IDGameGolfJuegoGrupo = '" . $IDGrupo . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDJugador = '" . $detalle_participante["IDJugador"] . "'";
                                $dbo2->query($sql_participante);
                            } else {
                                $sql_participante = "INSERT INTO GameGolfJuegoGrupoJugadores (IDGameGolfJuegoGrupo,IDClub,IDGameGolfJuego,IDJugador,IDGameGolfMarca,Handicap,HandicapCampo,UsuarioTrCr,FechaTrCr) 
                                                    VALUES('" . $IDGrupo . "','" . $IDClub . "','" . $IDJuegoGolf . "','" . $detalle_participante["IDJugador"] . "','" . $detalle_participante["IDMarca"] . "','" . $detalle_participante["Handicap"] . "','" . $HandicapCampo . "','WS',NOW())";
                                $dbo2->query($sql_participante);
                            }

                            $actualiza_handicap = "UPDATE Socio SET Handicap = '" . $detalle_participante["Handicap"] . "' WHERE IDSocio = '" . $detalle_participante["IDJugador"] . "' ";
                            $dbo->query($actualiza_handicap);

                            self::calcular_resultado_tarjeta($IDClub, $detalle_participante["IDJugador"], $IDJuegoGolf);
                        }
                    }
                }
                self::calcular_resultado_tarjeta_grupo($IDClub, $IDJuegoGolf);
            }



            $respuesta[message] = "Juego Actualizado";
            $respuesta[success] = true;
            $respuesta[response] = $response_juego;
        } else {
            $respuesta[message] = "GGJ8. Atencion faltan parametros!";
            $respuesta[success] = false;
            $respuesta[response] = null;
        }
        return $respuesta;
    }

    public function get_formatos_defecto_juegos_golf($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo2 = &SIMDB2::get();
        $response = array();
        $response_formato = array();
        $SQLDatos = "SELECT  IDGameGolfFormato, Nombre, Descripcion,DefectoUsarHandicap, DefectoUsarHandicapCampo, DefectoPorcentajeHandicap, DefectoNumeroResultados, Tipo,  MostrarUsarHandicap, MostrarUsarHandicapCampo, MostrarSeleccionarVentaja,  MostrarPorcentajeHandicap, MostrarNumeroResultados 
                         FROM  GameGolfFormato 
                         WHERE Defecto = 'S' AND Publicar = 'S'";
        $QRYDatos = $dbo2->query($SQLDatos);
        while ($Datos = $dbo2->fetchArray($QRYDatos)) :
            $InfoResponse["IDFormatoJuego"] = $Datos["IDGameGolfFormato"];
            $InfoResponse["NombreFormatoJuego"] = $Datos["Nombre"];
            $InfoResponse["Descripcion"] = $Datos["Descripcion"];
            $InfoResponse["UsarHandicap"] = $Datos["DefectoUsarHandicap"];
            $InfoResponse["UsarHandicapCampo"] = $Datos["DefectoUsarHandicapCampo"];
            $InfoResponse["PorcentajeHandicap"] = $Datos["DefectoPorcentajeHandicap"];
            $InfoResponse["Tipo"] = $Datos["Tipo"];
            $InfoResponse["NumeroDeResultados"] = $Datos["DefectoNumeroResultados"];
            $InfoResponse["MostrarUsarHandicap"] = $Datos["MostrarUsarHandicap"];
            $InfoResponse["MostrarUsarHandicapCampo"] = $Datos["MostrarUsarHandicapCampo"];
            $InfoResponse["MostrarPorcentajeHandicap"] = $Datos["MostrarPorcentajeHandicap"];
            $InfoResponse["MostrarNumeroResultados"] = $Datos["MostrarNumeroResultados"];

            $InfoResponse["JuegoPorHoyosMatch"] = $Datos["JuegoPorHoyosMatch"];
            $InfoResponse["JuegoPorGolpesMedal"] = $Datos["JuegoPorGolpesMedal"];
            $InfoResponse["MenorHandicapBajaCero"] = $Datos["MenorHandicapBajaCero"];
            $InfoResponse["Tipo"] = $Datos["Tipo"];
            $InfoResponse["IDJugadorJuego"] = $Datos["IDJugadorJuego"];

            array_push($response, $InfoResponse);
        endwhile;
        $respuesta[message] = "Datos encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;
        return $respuesta;
    }

    public function get_juegos_golf_juego_activo($IDClub, $IDSocio, $IDUsuario)
    {
        $dbo2 = &SIMDB2::get();
        $response = array();
        $response_formato = array();
        $SQLDatos = "SELECT  GGJ.IDGameGolfJuego, GGJ.FechaTrCr, GGJ.IDGolfCourse , GGC.Nombre as NombreCampo, GGJ.FechaTrCr as FechaJuego
                        FROM  GameGolfJuego GGJ, GameGolfJuegoGrupo GGJG, GameGolfJuegoGrupoJugadores GGJGJ,  GameGolfCourse GGC
                        WHERE GGJ.IDGameGolfJuego = GGJG.IDGameGolfJuego AND 
                              GGJG.IDGameGolfJuegoGrupo =  GGJGJ.IDGameGolfJuegoGrupo AND 
                              GGC.IDGameGolfCourse = GGJ.IDGolfCourse AND
                              GGJ.Estado = 'A' and GGJGJ.IDJugador = '" . $IDSocio . "'
                        Order By FechaTrCr Limit 1";
        $QRYDatos = $dbo2->query($SQLDatos);
        while ($Datos = $dbo2->fetchArray($QRYDatos)) :
            $InfoResponse[IDJuegoGolf] = $Datos["IDGameGolfJuego"];
            $InfoResponse[Fecha] = substr($Datos["FechaTrCr"], 0, 10);
            $InfoResponse[IDGolfCourse] = $Datos["IDGolfCourse"];
            $InfoResponse[NombreCourse] = $dbo2->getFields("GameGolfCourse", "Nombre", "IDGameGolfCourse = '" . $Datos["IDGolfCourse"] . "'  ");
            $InfoResponse["TextoEstado"] = SIMUtil::get_traduccion('', '', 'TienesjuegoActivo', LANG) . $Datos["NombreCampo"] . " " . substr($Datos["FechaJuego"], 0, 10);

            array_push($response, $InfoResponse);
        endwhile;
        $respuesta[message] = "Se encontró un juego en el cual ud está activo, seleccione una opción: ";
        $respuesta[success] = true;
        $respuesta[response] = $response;
        return $respuesta;
    }

    public function set_finalizar_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf)
    {
        $dbo2 = &SIMDB2::get();
        $response = array();
        if (!empty($IDSocio) && !empty($IDJuegoGolf)) {
            $sql_termina = "UPDATE GameGolfJuego SET Estado = 'T' WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "'";
            $dbo2->query($sql_termina);

            $sql_historial = "INSERT INTO GameGolfGolpeHistorial (IDGameGolfGolpe,IDClub,IDJugador,IDGameGolfJuego,Hoyo,NumeroGolpes,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd) 
                                    SELECT IDGameGolfGolpe,IDClub,IDJugador,IDGameGolfJuego,Hoyo,NumeroGolpes,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd FROM GameGolfGolpe WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $dbo2->query($sql_historial);

            $sql_historial = "INSERT INTO GameGolfJuegoTarjetaHistorial (IDGameGolfJuegoTarjeta,IDClub, IDGameGolfJuego, IDGameGolfFormato, IDGameGolfJuegoFormato, IDJugadorJuego, IDGameGolfJuegoGrupo, IDGameGolfJuegoFormatoSubGrupo, Resultado, SobrePar, EN, Resultado1_9, SobrePar1_9, Resultado10_18, SobrePar10_18, Posicion, Titulo, SubTitulo, Puntaje, ResultadoGeneralPar, ResultadoGeneralResultado, ResultadoGeneralPosicion, UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd ) 
                                    SELECT IDGameGolfJuegoTarjeta,IDClub, IDGameGolfJuego, IDGameGolfFormato, IDGameGolfJuegoFormato, IDJugadorJuego, IDGameGolfJuegoGrupo, IDGameGolfJuegoFormatoSubGrupo, Resultado, SobrePar, EN, Resultado1_9, SobrePar1_9, Resultado10_18, SobrePar10_18, Posicion, Titulo, SubTitulo, Puntaje, ResultadoGeneralPar, ResultadoGeneralResultado, ResultadoGeneralPosicion, UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd FROM GameGolfJuegoTarjeta WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $dbo2->query($sql_historial);

            $sql_historial = "INSERT INTO GameGolfJuegoTarjetaDetalleHistorial  (IDGameGolfJuegoTarjetaDetalle,IDGameGolfJuegoTarjeta,IDClub,IDGameGolfJuego,IDJugadorJuego, IDGameGolfJuegoGrupo,IDGameGolfJuegoFormatoSubGrupo,Hoyo,Puntos,HandicapHoyo,Resultado,Neto,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd) 
                                    SELECT IDGameGolfJuegoTarjetaDetalle,IDGameGolfJuegoTarjeta,IDClub,IDGameGolfJuego,IDJugadorJuego, IDGameGolfJuegoGrupo,IDGameGolfJuegoFormatoSubGrupo,Hoyo,Puntos,HandicapHoyo,Resultado,Neto,UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd FROM GameGolfJuegoTarjetaDetalle WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $dbo2->query($sql_historial);

            //Borro datos maestros
            $sql_elimina = "DELETE FROM GameGolfGolpe WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $dbo2->query($sql_elimina);

            $sql_elimina = "DELETE  FROM GameGolfJuegoTarjeta WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $dbo2->query($sql_elimina);

            $sql_elimina = "DELETE FROM GameGolfJuegoTarjetaDetalle WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $dbo2->query($sql_elimina);

            if ($IDClub == 17) {
                self::enviar_score($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf);
                $Mensaje = "Se enviaron los resultados a fedegolf";
            }

            $respuesta[message] = "Juego golf Finalizado y guardado." . $Mensaje;
            $respuesta[success] = true;
            $respuesta[response] = "";
        } else {
            $respuesta[message] = "GGC10. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function validar_finalizar_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf)
    {
        $dbo2 = &SIMDB2::get();
        $dbo = &SIMDB::get();
        $response = array();
        $Mensaje = "";
        if (!empty($IDSocio) && !empty($IDJuegoGolf)) {
            //verifico si todos completaron los golpes
            $datos_juego = $dbo2->fetchAll("GameGolfJuego", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
            $sql_jugadores = "SELECT IDJugador FROM GameGolfJuegoGrupoJugadores WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' ";
            $r_jugadores = $dbo2->query($sql_jugadores);
            while ($row_jugador = $dbo2->fetchArray($r_jugadores)) {
                $sql_golpe = "SELECT COUNT(IDJugador) as Golpes FROM GameGolfGolpe WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDJugador = '" . $row_jugador["IDJugador"] . "' Group by IDJugador";
                $r_golpe = $dbo2->query($sql_golpe);
                $row_golpe = $dbo2->fetchArray($r_golpe);
                if ($row_golpe["Golpes"] != $datos_juego["NumeroHoyos"]) {
                    $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_jugador["IDJugador"] . "' ", "array");
                    $Mensaje .= $datos_socio["Nombre"] . " " . $datos_socio["Apellido"] . " no ha completado sus registros\r\n";
                }
            }

            if ($Mensaje == "") {
                $respuesta[message] = "¿Estás seguro de finalizar el juego?";
                $respuesta[success] = true;
                $respuesta[response] = null;
            } else {
                $respuesta[message] = $Mensaje . "¿Aun deseas finalizar el juego?";
                $respuesta[success] = true;
                $respuesta[response] = null;
            }
        } else {
            $respuesta[message] = "GGC19. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function get_juego_golf_resultado_general($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDJugador, $Vista, $IDPartida)
    {
        $dbo2 = &SIMDB2::get();
        $response_formato = array();
        $ActivarGrupo = "N";

        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }

        if ((!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDJuegoGolf) && !empty($IDJugador)) {
            $datos_juego = $dbo2->fetchAll("GameGolfJuego", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
            $datos_tarjeta = $dbo2->fetchAll("GameGolfJuegoTarjeta" . $NombreTabla, " IDGameGolfJuego  = '" . $IDJuegoGolf . "' and IDJugadorJuego = '" . $IDJugador . "' ", "array");
            $InfoResponse["IDJuegoGolf"] = $IDJuegoGolf;
            $InfoResponse["IDJugador"] = $IDJugador;
            $InfoResponse["Fecha"] = substr($datos_juego["FechaTrCr"], 0, 10);
            $InfoResponse["IDGolfCourse"] = $datos_juego["IDGolfCourse"];
            $InfoResponse["NombreCourse"] = $dbo2->getFields("GameGolfCourse ", "Nombre", "IDGameGolfCourse = '" . $datos_juego["IDGolfCourse"] . "'  ");
            $InfoResponse["GolpesGeneral"] = $datos_tarjeta["Resultado"];
            $InfoResponse["PuntajeParGeneral"] = $datos_tarjeta["SobrePar"];

            //FormatosJuego
            $SQLFormato = "SELECT GGJF.IDGameGolfFormato, GGJF.IDGameGolfJuegoFormato, GGF.Nombre, GGF.Tipo, GGJF.UsarMejorBolaDelGrupo, GGJF.UsarSumaDelGrupo
                                       FROM  GameGolfJuegoFormato  GGJF, GameGolfFormato GGF 
                                       WHERE GGJF.IDGameGolfFormato = GGF.IDGameGolfFormato AND IDGameGolfJuego = '" . $IDJuegoGolf . "'";
            $QRYFormato = $dbo2->query($SQLFormato);
            while ($Formato = $dbo2->fetchArray($QRYFormato)) :
                $array_formato["IDFormatoJuego"] = $Formato["IDGameGolfFormato"];
                $array_formato["IDPartida"] = $Formato["IDGameGolfJuegoFormato"];

                if ($Formato["Tipo"] == "sub_grupal") {
                    if ($Formato["UsarMejorBolaDelGrupo"] == "S") {
                        $DetalleFormato = " mejor bola";
                    } elseif ($Formato["UsarSumaDelGrupo"] == "S") {
                        $DetalleFormato = " suma";
                    }
                }


                $array_formato["Nombre"] = $Formato["Nombre"] . $DetalleFormato;
                $array_formato["Tipo"] = $Formato["Tipo"];
                //Temporal si es grupos solo muestro la pestaña especial
                if ($Formato["IDGameGolfFormato"] != 9 && $Formato["IDGameGolfFormato"] != 10)
                    array_push($response_formato, $array_formato);
                else
                    $ActivarGrupo = "S";
            endwhile;

            $InfoResponse["FormatosJuego"] = $response_formato;

            $InfoResponse["MostrarResultadosIndividual"] = "N";
            $InfoResponse["NombreResultadosIndividuales"] = "Resultados individuales";
            if ($ActivarGrupo == "S") {
                $InfoResponse["MostrarResultadosGrupales"] = "S";
                $InfoResponse["NombreResultadosGrupales"] = "Result. por grupo Gross";
            } else {
                $InfoResponse["MostrarResultadosGrupales"] = "N";
                $InfoResponse["NombreResultadosGrupales"] = "";
            }


            $respuesta[message] = "Datos encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $InfoResponse;
        } else {
            $respuesta[message] = "GGG2. Atencion Faltan Parametros!" . $IDJugador;
            $respuesta[success] = false;
            $respuesta[response] = "";
        }
        return $respuesta;
    }

    public function get_resultado_formato_juego_individual_versus($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatosJuegos, $Vista, $SoloMatch)
    {

        $dbo = &SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response = array();


        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }

        if ((!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDJuegoGolf) && !empty($IDFormatosJuegos)) {
            //$IDSocio=5533;
            //FormatosJuego
            $SQLFormato = "SELECT GGJF.IDGameGolfFormato, GGJF.IDGameGolfJuegoFormato, GGF.Nombre, GGF.Tipo, GGJF.IDJugadorJuego, GGJF.IDJugadorPrincipal, GGJF.JuegoPorHoyosMatch, GGJF.JuegoPorGolpesMedal, GGJF.MenorHandicapBajaCero, GGJF.UsarHandicap
                               FROM  GameGolfJuegoFormato  GGJF, GameGolfFormato GGF 
                               WHERE GGJF.IDGameGolfFormato = GGF.IDGameGolfFormato AND IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGF.Tipo='individual_versus' and (IDJugadorPrincipal='" . $IDSocio . "' or  IDJugadorJuego = '" . $IDSocio . "') ";
            $QRYFormato = $dbo2->query($SQLFormato);
            while ($Formato = $dbo2->fetchArray($QRYFormato)) :

                if ($Formato["UsarHandicap"] == "S") {
                    $TituloTarjetaMedal = "Resultado juego por golpes Neto";
                    $TituloTarjetaMatch = "Resultado juego por hoyos Neto";
                } else {
                    $TituloTarjetaMedal = "Resultado juego por golpes Gross";
                    $TituloTarjetaMatch = "Resultado juego por hoyos Gross";
                }

                if ($Formato["IDJugadorPrincipal"] == $IDSocio) {
                    $IDJugador1 = $Formato["IDJugadorJuego"];
                    $IDJugador2 = $IDSocio;
                } else {
                    $IDJugador1 = $Formato["IDJugadorPrincipal"];
                    $IDJugador2 = $IDSocio;
                }

                $InfoResponse = array();
                $InfoResponseNeto = array();
                $row_tarjeta1 = array();
                $row_tarjeta2 = array();
                $array_hoyos_neto = array();
                $Hoyos_jugado_1_9 = "N";
                $Hoyos_jugado_10_18 = "N";
                $NumeroHoyosJ1 = 0;
                $NumeroHoyosJ1_hoyo_1_9 = 0;
                $NumeroHoyosJ1_hoyo_10_18 = 0;
                $NumeroHoyosJ2 = 0;
                $NumeroHoyosJ2_hoyo_1_9 = 0;
                $NumeroHoyosJ2_hoyo_10_18 = 0;
                $PuntosJ1_hoyo_1_9 = 0;
                $PuntosJ1_hoyo_10_18 = 0;
                $PuntosJ2_hoyo_1_9 = 0;
                $PuntosJ2_hoyo_10_18 = 0;
                $PuntosTotalJ1 = 0;
                $PuntosTotalJ2 = 0;
                $ResultadoJ1 = "";
                $SobrePar1_9_J1 = 0;
                $ColorSobrePar1_9_J1 = "";
                $SobrePar10_18_Total_J1 = "";
                $ColorSobrePar10_18_Total_J1 = "";
                $SobrePar_Total_J1 = "";
                $ColorSobrePar_Total_J1 = "";
                $NumeroHoyosJ1 = "";
                $SobrePar1_9_J2 = "";
                $ColorSobrePar1_9_J2 = "";
                $SobrePar10_18_Total_J2 = "";
                $ColorSobrePar10_18_Total_J2 = "";
                $SobrePar_Total_J2 = "";
                $ColorSobrePar_Total_J2 = "";
                $NumeroHoyosJ2 = "";


                $datos_socio_contrincante = $dbo->fetchAll("Socio", " IDSocio = '" . $IDJugador1 . "' ", "array");
                $datos_socio_retador = $dbo->fetchAll("Socio", " IDSocio = '" . $IDJugador2 . "' ", "array");
                $InfoResponse["IDFormatoJuego"] = $Formato["IDGameGolfFormato"];
                $InfoResponse["IDPartida"] = $Formato["IDGameGolfJuegoFormato"];

                $InfoResponse["IDJugadorJuego"] = $IDJugador1;
                $InfoResponse["Titulo"] = $datos_socio_contrincante["Nombre"] . " " . $datos_socio_contrincante["Apellido"];

                $sql_datos_jugador = "SELECT IDGameGolfMarca, Handicap FROM GameGolfJuegoGrupoJugadores WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDJugador = '" . $IDJugador1 . "' Limit 1";
                $r_datos_jugador = $dbo2->query($sql_datos_jugador);
                $row_datos_jugador = $dbo2->fetchArray($r_datos_jugador);
                $Marca = $dbo2->getFields("GameGolfMarca", "Nombre", "IDGameGolfMarca = '" . $row_datos_jugador["IDGameGolfMarca"] . "'  ");
                $InfoResponse["Subtitulo"] = "Hcp: " . $row_datos_jugador["Handicap"] . " " . $Marca;


                //Consulto la tarjeta del contrincante
                $sql_tarjeta1 = "SELECT GGJTD.Hoyo,GGJTD.Resultado,GGJTD.Neto, GGJT.SobrePar,GGJT.SobrePar1_9,GGJT.SobrePar10_18,GGJT.Resultado,GGJT.Resultado1_9,GGJT.Resultado10_18
                                           FROM GameGolfJuegoTarjetaDetalle" . $NombreTabla . " GGJTD, GameGolfJuegoTarjeta" . $NombreTabla . " GGJT 
                                           WHERE GGJTD.IDGameGolfJuegoTarjeta = GGJT.IDGameGolfJuegoTarjeta and GGJTD.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJTD.IDJugadorJuego = '" . $IDJugador1 . "' and GGJT.IDGameGolfJuegoFormato = '" . $Formato["IDGameGolfJuegoFormato"] . "' ";
                $r_tarjeta1 = $dbo2->query($sql_tarjeta1);
                while ($row_tarjeta1 = $dbo->fetchArray($r_tarjeta1)) {
                    $array_resultado1_neto[$row_tarjeta1["Hoyo"]] = $row_tarjeta1["Neto"];
                    $array_resultado1_gross[$row_tarjeta1["Gross"]] = $row_tarjeta1["Resultado"];
                    $NumeroHoyosJ1++;
                    if ($row_tarjeta1["Hoyo"] <= 9)
                        $NumeroHoyosJ1_hoyo_1_9++;
                    else
                        $NumeroHoyosJ1_hoyo_10_18++;

                    if ($row_tarjeta1["SobrePar"] == 0) {
                        $SobrePar_Total_J1 = "E";
                        $ColorSobrePar_Total_J1 = "#000000";
                    } elseif ($row_tarjeta1["SobrePar"] > 0) {
                        $SobrePar_Total_J1 = "+" . $row_tarjeta1["SobrePar"];
                        $ColorSobrePar_Total_J1 = "#000000";
                    } else {
                        $SobrePar_Total_J1 = $row_tarjeta1["SobrePar"];
                        $ColorSobrePar_Total_J1 = "#FF0000";
                    }


                    if ($row_tarjeta1["Resultado1_9"] > 0) {
                        if ($row_tarjeta1["SobrePar1_9"] == 0) {
                            $SobrePar1_9_J1 = "E";
                            $ColorSobrePar1_9_J1 = "#000000";
                        } elseif ($row_tarjeta1["SobrePar1_9"] > 0) {
                            $SobrePar1_9_J1 = "+" . $row_tarjeta1["SobrePar1_9"];
                            $ColorSobrePar1_9_J1 = "#000000";
                        } else {
                            $SobrePar1_9_J1 = $row_tarjeta1["SobrePar1_9"];
                            $ColorSobrePar1_9_J1 = "#FF0000";
                        }
                    } else {
                        $SobrePar1_9_J1 = "";
                        $ColorSobrePar1_9_J1 = "";
                    }

                    if ($row_tarjeta1["Resultado10_18"] > 0) {
                        if ($row_tarjeta1["SobrePar10_18"] == 0) {
                            $SobrePar10_18_Total_J1 = "E";
                            $ColorSobrePar10_18_Total_J1 = "#000000";
                        } elseif ($row_tarjeta1["SobrePar10_18"] > 0) {
                            $SobrePar10_18_Total_J1 = "+" . $row_tarjeta1["SobrePar10_18"];
                            $ColorSobrePar10_18_Total_J1 = "#000000";
                        } else {
                            $SobrePar10_18_Total_J1 = $row_tarjeta1["SobrePar10_18"];
                            $ColorSobrePar10_18_Total_J1 = "#FF0000";
                        }
                    } else {
                        $SobrePar10_18_Total_J1 = "";
                        $ColorSobrePar10_18_Total_J1 = "";
                    }


                    $ResultadoJ1 = $row_tarjeta1["Resultado"];
                }

                //Consulto la tarjeta del retador
                $sql_tarjeta2 = "SELECT GGJTD.Hoyo,GGJTD.Resultado,GGJTD.Neto, GGJT.SobrePar,GGJT.SobrePar1_9,GGJT.SobrePar10_18,GGJT.Resultado,GGJT.Resultado1_9,GGJT.Resultado10_18
                                            FROM GameGolfJuegoTarjetaDetalle" . $NombreTabla . " GGJTD, GameGolfJuegoTarjeta" . $NombreTabla . " GGJT 
                                            WHERE GGJTD.IDGameGolfJuegoTarjeta = GGJT.IDGameGolfJuegoTarjeta and GGJTD.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJTD.IDJugadorJuego = '" . $IDJugador2 . "' and GGJT.IDGameGolfJuegoFormato = '" . $Formato["IDGameGolfJuegoFormato"] . "' ";
                $r_tarjeta2 = $dbo2->query($sql_tarjeta2);
                while ($row_tarjeta2 = $dbo->fetchArray($r_tarjeta2)) {


                    $array_resultado2_neto[$row_tarjeta2["Hoyo"]] = $row_tarjeta2["Neto"];
                    $array_resultado2_gross[$row_tarjeta2["Gross"]] = $row_tarjeta2["Resultado"];
                    $NumeroHoyosJ2++;
                    if ($row_tarjeta2["Hoyo"] <= 9)
                        $NumeroHoyosJ2_hoyo_1_9++;
                    else
                        $NumeroHoyosJ2_hoyo_10_18++;

                    if ($row_tarjeta2["SobrePar"] == 0) {
                        $SobrePar_Total_J2 = "E";
                        $ColorSobrePar_Total_J2 = "";
                    } elseif ($row_tarjeta2["SobrePar"] > 0) {
                        $SobrePar_Total_J2 = "+" . $row_tarjeta2["SobrePar"];
                        $ColorSobrePar_Total_J2 = "#000000";
                    } else {
                        $SobrePar_Total_J2 = $row_tarjeta2["SobrePar"];
                        $ColorSobrePar_Total_J2 = "#FF0000";
                    }

                    if ($row_tarjeta2["Resultado1_9"] > 0) {
                        if ($row_tarjeta2["SobrePar1_9"] == 0) {
                            $SobrePar1_9_J2 = "E";
                            $ColorSobrePar1_9_J2 = "#000000";
                        } elseif ($row_tarjeta2["SobrePar1_9"] > 0) {
                            $SobrePar1_9_J2 = "+" . $row_tarjeta2["SobrePar1_9"];
                            $ColorSobrePar1_9_J2 = "#000000";
                        } else {
                            $SobrePar1_9_J2 = $row_tarjeta2["SobrePar1_9"];
                            $ColorSobrePar1_9_J2 = "#FF0000";
                        }
                    } else {
                        $SobrePar1_9_J2 = "";
                        $ColorSobrePar1_9_J2 = "";
                    }

                    if ($row_tarjeta2["Resultado10_18"] > 0) {
                        if ($row_tarjeta2["SobrePar10_18"] == 0) {
                            $SobrePar10_18_Total_J2 = "E";
                            $ColorSobrePar10_18_Total_J2 = "#000000";
                        } elseif ($row_tarjeta2["SobrePar10_18"] > 0) {
                            $SobrePar10_18_Total_J2 = "+" . $row_tarjeta2["SobrePar10_18"];
                            $ColorSobrePar10_18_Total_J2 = "#000000";
                        } else {
                            $SobrePar10_18_Total_J2 = $row_tarjeta2["SobrePar10_18"];
                            $ColorSobrePar10_18_Total_J2 = "#FF0000";
                        }
                    } else {
                        $SobrePar10_18_Total_J2 = "";
                        $ColorSobrePar10_18_Total_J2 = "";
                    }

                    $ResultadoJ2 = $row_tarjeta2["Resultado"];
                }

                if ($Formato["JuegoPorGolpesMedal"] == "S" && $SoloMatch == "") {
                    $InfoResponseNeto["TituloTarjeta"] = $TituloTarjetaMedal;
                    //Jugador 1
                    $array_jugador1["Label"] = substr($datos_socio_contrincante["Nombre"], 0, 1) . substr($datos_socio_contrincante["Apellido"], 0, 1);
                    $array_jugador1["Hoyos1-9"] = $SobrePar1_9_J1;
                    $array_jugador1["ColorHoyos1-9"] = $ColorSobrePar1_9_J1;
                    $array_jugador1["Hoyos10-18"] = $SobrePar10_18_Total_J1;
                    $array_jugador1["ColorHoyos10-18"] = $ColorSobrePar10_18_Total_J1;
                    $array_jugador1["Total"] = $SobrePar_Total_J1;
                    $array_jugador1["ColorTotal"] = $ColorSobrePar_Total_J1;
                    $array_jugador1["Hoyo"] = (string)$NumeroHoyosJ1;
                    $array_jugador1["ColorHoyo"] = "";
                    $array_jugador1["Score"] = $ResultadoJ1;
                    $array_jugador1["ColorScore"] = "";
                    $InfoResponseNeto["Jugador1"] = $array_jugador1;

                    //Jugador 2
                    $array_jugador2["Label"] = substr($datos_socio_retador["Nombre"], 0, 1) . substr($datos_socio_retador["Apellido"], 0, 1);
                    $array_jugador2["Hoyos1-9"] = $SobrePar1_9_J2;
                    $array_jugador2["ColorHoyos1-9"] = $ColorSobrePar1_9_J2;
                    $array_jugador2["Hoyos10-18"] = $SobrePar10_18_Total_J2;
                    $array_jugador2["ColorHoyos10-18"] = $ColorSobrePar10_18_Total_J2;
                    $array_jugador2["Total"] = $SobrePar_Total_J2;
                    $array_jugador2["ColorTotal"] = $ColorSobrePar_Total_J2;
                    $array_jugador2["Hoyo"] = (string)$NumeroHoyosJ2;
                    $array_jugador2["ColorHoyo"] = "";
                    $array_jugador2["Score"] = $ResultadoJ2;
                    $array_jugador2["ColorScore"] = "";
                    $InfoResponseNeto["Jugador2"] = $array_jugador2;

                    $InfoResponse["TarjetaPorGolpesNeto"] = $InfoResponseNeto;
                }


                if ($Formato["JuegoPorHoyosMatch"] == "S") {
                    $PuntosJ1_hoyo_1_9 = 0;
                    $PuntosJ1_hoyo_10_18 = 0;
                    $PuntosJ2_hoyo_1_9 = 0;
                    $PuntosJ2_hoyo_10_18 = 0;

                    //Comparo resultados de los hoyos
                    for ($i = 1; $i <= 18; $i++) {

                        if (array_key_exists($i, $array_resultado1_neto) || array_key_exists($i, $array_resultado2_neto)) {
                            if ($i <= 9)
                                $Hoyos_jugado_1_9 = "S";
                            else
                                $Hoyos_jugado_10_18 = "S";
                        }

                        //Verifico que haya resultado en ambos jugadores en ese hoyo para tomarlo en cuenta en el calculo
                        if (!empty($array_resultado2_neto[$i]) && !empty($array_resultado1_neto[$i])) {
                            if ($array_resultado2_neto[$i] == $array_resultado1_neto[$i]) {
                                $Puntos = 0;
                            } elseif ($array_resultado1_neto[$i] < $array_resultado2_neto[$i]) {
                                if ($i <= 9) {
                                    $PuntosJ1_hoyo_1_9 += 1;
                                } else {
                                    $PuntosJ1_hoyo_10_18 += 1;
                                }
                            } else {
                                if ($i <= 9) {
                                    $PuntosJ2_hoyo_1_9 += 1;
                                } else {
                                    $PuntosJ2_hoyo_10_18 += 1;
                                }
                            }
                            $array_detalle_hoyo_jug1[$i] = $array_resultado1_neto[$i];
                            $array_detalle_hoyo_jug2[$i] = $array_resultado2_neto[$i];
                        }
                    }

                    $PuntosTotalJ1 = $PuntosJ1_hoyo_1_9 + $PuntosJ1_hoyo_10_18;
                    $PuntosTotalJ2 = $PuntosJ2_hoyo_1_9 + $PuntosJ2_hoyo_10_18;




                    //Hoyo 1-9
                    if ((int)$NumeroHoyosJ1_hoyo_1_9 > $NumeroHoyosJ2_hoyo_1_9) {
                        $NumeroHoyoshoyo_1_9Mostrar = $NumeroHoyosJ2_hoyo_1_9;
                    } else {
                        $NumeroHoyoshoyo_1_9Mostrar = $NumeroHoyosJ1_hoyo_1_9;
                    }
                    if ($Hoyos_jugado_1_9 == "S") {
                        if ($PuntosJ1_hoyo_1_9 == $PuntosJ2_hoyo_1_9) {
                            $MostrarTop_Hoyo_1_9 = "E";
                            $DeCuantosHoyos1_9 = "";
                            $Forma_hoyo_1_9 = "ninguna";
                        } elseif ($PuntosJ1_hoyo_1_9 > $PuntosJ2_hoyo_1_9) {
                            $PuntosJ1_hoyo_1_9 -= $PuntosJ2_hoyo_1_9;
                            $MostrarTop_Hoyo_1_9 = $PuntosJ1_hoyo_1_9 . " UP";
                            $DeCuantosHoyos1_9 = "de " . $NumeroHoyoshoyo_1_9Mostrar;
                            $Forma_hoyo_1_9 = "arriba";
                        } else {
                            $PuntosJ2_hoyo_1_9 -= $PuntosJ1_hoyo_1_9;
                            $MostrarTop_Hoyo_1_9 = $PuntosJ2_hoyo_1_9 . " UP";
                            $DeCuantosHoyos1_9 = "de " . $NumeroHoyoshoyo_1_9Mostrar;
                            $Forma_hoyo_1_9 = "abajo";
                        }
                    } else {
                        $MostrarTop_Hoyo_1_9 = "";
                        $DeCuantosHoyos1_9 = "";
                        $Forma_hoyo_1_9 = "ninguna";
                    }
                    //Fin  Hoyo 1-9 

                    //Hoyo 10-18
                    if ((int)$NumeroHoyosJ1_hoyo_10_18 > $NumeroHoyosJ2_hoyo_10_18) {
                        $NumeroHoyoshoyo_10_18Mostrar = $NumeroHoyosJ2_hoyo_10_18;
                    } else {
                        $NumeroHoyoshoyo_10_18Mostrar = $NumeroHoyosJ1_hoyo_10_18;
                    }
                    if ($Hoyos_jugado_10_18 == "S") {
                        if ($PuntosJ1_hoyo_10_18 == $PuntosJ2_hoyo_10_18) {
                            $MostrarTop_Hoyo_10_18 = "E";
                            $DeCuantosHoyos10_18 = "";
                            $Forma_hoyo_10_18 = "nunguna";
                        } elseif ($PuntosJ1_hoyo_10_18 > $PuntosJ2_hoyo_10_18) {
                            $PuntosJ1_hoyo_10_18 -= $PuntosJ2_hoyo_10_18;
                            $MostrarTop_Hoyo_10_18 = $PuntosJ1_hoyo_10_18 . " UP";
                            $DeCuantosHoyos10_18 = "de " . $NumeroHoyoshoyo_10_18Mostrar;
                            $Forma_hoyo_10_18 = "arriba";
                        } else {
                            $PuntosJ2_hoyo_10_18 -= $PuntosJ1_hoyo_10_18;
                            $MostrarTop_Hoyo_10_18 = $PuntosJ2_hoyo_10_18 . " UP";
                            $DeCuantosHoyos10_18 = "de " . $NumeroHoyoshoyo_10_18Mostrar;
                            $Forma_hoyo_10_18 = "abajo";
                        }
                    } else {
                        $MostrarTop_Hoyo_10_18 = "";
                        $DeCuantosHoyos10_18 = "";
                        $Forma_hoyo_10_18 = "ninguna";
                    }
                    //Fin  Hoyo 10-18 

                    //Gran Total
                    if ($Hoyos_jugado_1_9 == "S" || $Hoyos_jugado_10_18 == "S") {
                        if ($PuntosTotalJ1 == $PuntosTotalJ2) {
                            $MostrarPuntosTotal = "E";
                            $NumeroHoyosTotal = "";
                            $Forma_hoyo_total = "ninguna";
                        } elseif ($PuntosTotalJ1 > $PuntosTotalJ2) {
                            $PuntosTotalJ1 -= $PuntosTotalJ2;
                            $MostrarPuntosTotal = $PuntosTotalJ1 . " UP";
                            $NumeroHoyosTotal = "de " . $NumeroHoyosJ1;
                            $Forma_hoyo_total = "arriba";
                        } else {
                            $PuntosTotalJ2 -= $PuntosTotalJ1;
                            $MostrarPuntosTotal = $PuntosTotalJ2 . " UP";
                            $NumeroHoyosTotal = "de " . $NumeroHoyosJ2;
                            $Forma_hoyo_total = "abajo";
                        }
                    } else {
                        $MostrarPuntosTotal = "";
                        $NumeroHoyosTotal = "";
                        $Forma_hoyo_total = "ninguna";
                    }
                    //Fin Gran total

                    $array_hoyos_neto["TituloTarjeta"] = $TituloTarjetaMatch;
                    $array_hoyos_neto["LabelJugador1"] = substr($datos_socio_contrincante["Nombre"], 0, 1) . substr($datos_socio_contrincante["Apellido"], 0, 1);
                    $array_hoyos_neto["LabelJugador2"] = substr($datos_socio_retador["Nombre"], 0, 1) . substr($datos_socio_retador["Apellido"], 0, 1);
                    $array_hoyos_neto["Hoyos1-9Top"] = $MostrarTop_Hoyo_1_9;
                    $array_hoyos_neto["Hoyos1-9Bottom"] = $DeCuantosHoyos1_9;
                    $array_hoyos_neto["Hoyos1-9Forma"] = $Forma_hoyo_1_9; //"arriba/abajo/ninguno",
                    $array_hoyos_neto["Hoyos10-18Top"] = $MostrarTop_Hoyo_10_18;
                    $array_hoyos_neto["Hoyos10-18Bottom"] = $DeCuantosHoyos10_18;
                    $array_hoyos_neto["Hoyos10-18Forma"] = $Forma_hoyo_10_18;
                    $array_hoyos_neto["TotalTop"] = $MostrarPuntosTotal;
                    $array_hoyos_neto["TotalBottom"] = $NumeroHoyosTotal;
                    $array_hoyos_neto["TotalForma"] = $Forma_hoyo_total; //"arriba/abajo/ninguno"
                    $InfoResponse["TarjetaPorHoyosNeto"] = $array_hoyos_neto;
                }

                $InfoResponse["DetalleNetoJug1"] = $array_resultado1_neto;
                $InfoResponse["DetalleGrossJug1"] = $array_resultado1_gross;
                $InfoResponse["DetalleNetoJug2"] = $array_resultado2_neto;
                $InfoResponse["DetalleGrossJug2"] = $array_resultado2_gross;
                array_push($response, $InfoResponse);
            endwhile;




            $respuesta[message] = "Datos encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $response;
        } else {
            $respuesta[message] = "GGG2. Atencion Faltan Parametros!" . $IDJugador;
            $respuesta[success] = false;
            $respuesta[response] = "";
        }
        return $respuesta;
    }

    public function get_resultado_formato_juego_parejas_versus($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatosJuegos, $Vista, $SoloMatch)
    {

        $dbo = &SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response = array();



        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }

        if ((!empty($IDSocio) || !empty($IDUsuario)) && !empty($IDJuegoGolf) && !empty($IDFormatosJuegos)) {
            //$IDSocio=5533;
            //FormatosJuego
            $SQLFormato = "SELECT GGFLL.IDGameGolfJuegoFormatoLlaves, GGJF.IDGameGolfFormato, GGJF.IDGameGolfJuegoFormato, GGF.Nombre, GGF.Tipo, GGJF.IDJugadorJuego, GGJF.IDJugadorPrincipal, GGJF.JuegoPorHoyosMatch, GGJF.JuegoPorGolpesMedal, GGJF.MenorHandicapBajaCero, GGJF.UsarHandicap, GGJF.UsarSumaDelGrupo, GGJF.UsarMejorBolaDelGrupo
                               FROM  GameGolfJuegoFormato  GGJF, GameGolfFormato GGF, GameGolfJuegoFormatoLlaves GGFLL 
                               WHERE GGJF.IDGameGolfFormato = GGF.IDGameGolfFormato AND GGJF.IDGameGolfFormato = GGFLL.IDGameGolfFormato AND GGJF.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGF.Tipo='pair_versus' and (IDJugador1Pareja1='" . $IDSocio . "' or  IDJugador1Pareja2 = '" . $IDSocio . "' or IDJugador2Pareja1='" . $IDSocio . "' or  IDJugador2Pareja2 = '" . $IDSocio . "') ";
            $QRYFormato = $dbo2->query($SQLFormato);
            while ($Formato = $dbo2->fetchArray($QRYFormato)) :

                if ($Formato["UsarHandicap"] == "S") {
                    $TituloTarjetaMatch = "Resultado juego por hoyos Neto";
                } else {
                    $TituloTarjetaMatch = "Resultado juego por hoyos Gross";
                }



                $InfoResponse = array();
                $InfoResponseNeto = array();
                $row_tarjeta1 = array();
                $row_tarjeta2 = array();
                $array_hoyos_neto = array();
                $Hoyos_jugado_1_9 = "N";
                $Hoyos_jugado_10_18 = "N";
                $NumeroHoyosJ1 = 0;
                $NumeroHoyosJ1_hoyo_1_9 = 0;
                $NumeroHoyosJ1_hoyo_10_18 = 0;
                $NumeroHoyosJ2 = 0;
                $NumeroHoyosJ2_hoyo_1_9 = 0;
                $NumeroHoyosJ2_hoyo_10_18 = 0;
                $PuntosJ1_hoyo_1_9 = 0;
                $PuntosJ1_hoyo_10_18 = 0;
                $PuntosJ2_hoyo_1_9 = 0;
                $PuntosJ2_hoyo_10_18 = 0;
                $PuntosTotalJ1 = 0;
                $PuntosTotalJ2 = 0;
                $ResultadoJ1 = "";
                $SobrePar1_9_J1 = 0;
                $ColorSobrePar1_9_J1 = "";
                $SobrePar10_18_Total_J1 = "";
                $ColorSobrePar10_18_Total_J1 = "";
                $SobrePar_Total_J1 = "";
                $ColorSobrePar_Total_J1 = "";
                $NumeroHoyosJ1 = "";
                $SobrePar1_9_J2 = "";
                $ColorSobrePar1_9_J2 = "";
                $SobrePar10_18_Total_J2 = "";
                $ColorSobrePar10_18_Total_J2 = "";
                $SobrePar_Total_J2 = "";
                $ColorSobrePar_Total_J2 = "";
                $NumeroHoyosJ2 = "";


                $InfoResponse["IDFormatoJuego"] = $Formato["IDGameGolfFormato"];
                $InfoResponse["IDPartida"] = $Formato["IDGameGolfJuegoFormato"];


                //busco las parejas
                $sql_llave = "SELECT IDJugador1Pareja1, IDJugador2Pareja1, IDJugador1Pareja2, IDJugador2Pareja2
                                    FROM GameGolfJuegoFormatoLlaves 
                                    WHERE IDGameGolfJuegoFormatoLlaves='" . $Formato["IDGameGolfJuegoFormatoLlaves"] . "' ";
                $r_llave = $dbo2->query($sql_llave);
                while ($row_llave = $dbo2->fetchArray($r_llave)) {
                    $datos_socio_1 = $dbo->fetchAll("Socio", " IDSocio = '" . $row_llave["IDJugador1Pareja1"] . "' ", "array");
                    $datos_socio_2 = $dbo->fetchAll("Socio", " IDSocio = '" . $row_llave["IDJugador2Pareja1"] . "' ", "array");
                    $datos_socio_3 = $dbo->fetchAll("Socio", " IDSocio = '" . $row_llave["IDJugador1Pareja2"] . "' ", "array");
                    $datos_socio_4 = $dbo->fetchAll("Socio", " IDSocio = '" . $row_llave["IDJugador2Pareja2"] . "' ", "array");

                    $array_parejas1["IDJugador1"] = $row_llave["IDJugador1Pareja1"];
                    $array_parejas1["IDJugador2"] = $row_llave["IDJugador2Pareja1"];
                    $array_parejas2["IDJugador1"] = $row_llave["IDJugador1Pareja2"];
                    $array_parejas2["IDJugador2"] = $row_llave["IDJugador2Pareja2"];
                }

                $InfoResponse["Pareja1"] = $array_parejas1;
                $InfoResponse["Pareja2"] = $array_parejas1;

                $InfoResponse["Titulo"] = "Match pareja";
                $sql_datos_jugador = "SELECT IDGameGolfMarca, Handicap FROM GameGolfJuegoGrupoJugadores WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDJugador = '" . $array_parejas1["IDJugador1"] . "' Limit 1";
                $r_datos_jugador = $dbo2->query($sql_datos_jugador);
                $row_datos_jugador = $dbo2->fetchArray($r_datos_jugador);
                $Marca = $dbo2->getFields("GameGolfMarca", "Nombre", "IDGameGolfMarca = '" . $row_datos_jugador["IDGameGolfMarca"] . "'  ");
                $InfoResponse["Subtitulo"] = "Hcp: " . $row_datos_jugador["Handicap"] . " " . $Marca;

                $Pareja1 = $datos_socio_1["Nombre"] . " " . $datos_socio_1["Apellido"] . "\n" . $datos_socio_2["Nombre"] . " " . $datos_socio_2["Apellido"];
                $Pareja2 = $datos_socio_3["Nombre"] . " " . $datos_socio_3["Apellido"] . "\n" . $datos_socio_4["Nombre"] . " " . $datos_socio_4["Apellido"];



                //Consulto la tarjeta de la pareja 1     
                foreach ($array_parejas1 as $id_jugador) {
                    $sql_tarjeta1 = "SELECT GGJTD.Hoyo,GGJTD.Resultado,GGJTD.Neto, GGJT.SobrePar,GGJT.SobrePar1_9,GGJT.SobrePar10_18,GGJT.Resultado,GGJT.Resultado1_9,GGJT.Resultado10_18
                            FROM GameGolfJuegoTarjetaDetalle" . $NombreTabla . " GGJTD, GameGolfJuegoTarjeta" . $NombreTabla . " GGJT 
                            WHERE GGJTD.IDGameGolfJuegoTarjeta = GGJT.IDGameGolfJuegoTarjeta and GGJTD.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJTD.IDJugadorJuego = '" . $id_jugador . "' and GGJT.IDGameGolfJuegoFormato = '" . $Formato["IDGameGolfJuegoFormato"] . "' ";
                    $r_tarjeta1 = $dbo2->query($sql_tarjeta1);
                    while ($row_tarjeta1 = $dbo->fetchArray($r_tarjeta1)) {
                        $array_resultado1_neto_parcial[$id_jugador][$row_tarjeta1["Hoyo"]] = $row_tarjeta1["Neto"];
                        $array_resultado1_gross_parcial[$id_jugador][$row_tarjeta1["Gross"]] = $row_tarjeta1["Resultado"];
                    }
                }

                //Comparo las tarjetas de la pareja para armar la tarjeta final para comparar con la otra pareja
                for ($hoyo = 1; $hoyo <= 18; $hoyo++) {
                    if (!empty($array_resultado1_neto_parcial[$array_parejas1["IDJugador1"]][$hoyo]) || $array_resultado1_neto_parcial[$array_parejas1["IDJugador2"]][$hoyo]) {
                        $NumeroHoyosJ1++;
                        if ($hoyo <= 9)
                            $NumeroHoyosJ1_hoyo_1_9++;
                        else
                            $NumeroHoyosJ1_hoyo_10_18++;

                        if ($array_resultado1_neto_parcial[$array_parejas1["IDJugador1"]][$hoyo] <= $array_resultado1_neto_parcial[$array_parejas1["IDJugador2"]][$hoyo]) {
                            $array_resultado1_neto[$hoyo] = $array_resultado1_neto_parcial[$array_parejas1["IDJugador1"]][$hoyo];
                        } else {
                            $array_resultado1_neto[$hoyo] = $array_resultado1_neto_parcial[$array_parejas1["IDJugador2"]][$hoyo];
                        }

                        if ($array_resultado1_gross_parcial[$array_parejas1["IDJugador1"]][$hoyo] <= $array_resultado1_gross_parcial[$array_parejas1["IDJugador2"]][$hoyo]) {
                            $array_resultado1_gross[$hoyo] = $array_resultado1_gross_parcial[$array_parejas1["IDJugador1"]][$hoyo];
                        } else {
                            $array_resultado1_gross[$hoyo] = $array_resultado1_gross_parcial[$array_parejas1["IDJugador2"]][$hoyo];
                        }
                        //Para SUMA
                        $array_resultado1_neto_suma[$hoyo] = $array_resultado1_neto_parcial[$array_parejas1["IDJugador1"]][$hoyo] + $array_resultado1_neto_parcial[$array_parejas1["IDJugador2"]][$hoyo];
                        $array_resultado1_gross_suma[$hoyo] = $array_resultado1_gross_parcial[$array_parejas1["IDJugador1"]][$hoyo] + $array_resultado1_gross_parcial[$array_parejas1["IDJugador2"]][$hoyo];
                    }
                }





                //Consulto la tarjeta de la pareja retadora
                foreach ($array_parejas2 as $id_jugador) {
                    $sql_tarjeta2 = "SELECT GGJTD.Hoyo,GGJTD.Resultado,GGJTD.Neto, GGJT.SobrePar,GGJT.SobrePar1_9,GGJT.SobrePar10_18,GGJT.Resultado,GGJT.Resultado1_9,GGJT.Resultado10_18
                                            FROM GameGolfJuegoTarjetaDetalle" . $NombreTabla . " GGJTD, GameGolfJuegoTarjeta" . $NombreTabla . " GGJT 
                                            WHERE GGJTD.IDGameGolfJuegoTarjeta = GGJT.IDGameGolfJuegoTarjeta and GGJTD.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJTD.IDJugadorJuego = '" . $id_jugador . "' and GGJT.IDGameGolfJuegoFormato = '" . $Formato["IDGameGolfJuegoFormato"] . "' ";
                    $r_tarjeta2 = $dbo2->query($sql_tarjeta2);
                    while ($row_tarjeta2 = $dbo->fetchArray($r_tarjeta2)) {
                        $array_resultado2_neto_parcial[$id_jugador][$row_tarjeta2["Hoyo"]] = $row_tarjeta2["Neto"];
                        $array_resultado2_gross_parcial[$id_jugador][$row_tarjeta2["Gross"]] = $row_tarjeta2["Resultado"];
                    }
                }

                //Comparo las tarjetas de la pareja para armar la tarjeta final para comparar con la otra pareja
                for ($hoyo = 1; $hoyo <= 18; $hoyo++) {
                    if (!empty($array_resultado2_neto_parcial[$array_parejas2["IDJugador1"]][$hoyo]) || $array_resultado2_neto_parcial[$array_parejas2["IDJugador2"]][$hoyo]) {
                        $NumeroHoyosJ2++;
                        if ($hoyo <= 9)
                            $NumeroHoyosJ2_hoyo_1_9++;
                        else
                            $NumeroHoyosJ2_hoyo_10_18++;

                        if ($array_resultado2_neto_parcial[$array_parejas2["IDJugador1"]][$hoyo] <= $array_resultado2_neto_parcial[$array_parejas2["IDJugador2"]][$hoyo]) {
                            $array_resultado2_neto[$hoyo] = $array_resultado2_neto_parcial[$array_parejas2["IDJugador1"]][$hoyo];
                        } else {
                            $array_resultado2_neto[$hoyo] = $array_resultado2_neto_parcial[$array_parejas2["IDJugador2"]][$hoyo];
                        }

                        if ($array_resultado2_gross_parcial[$array_parejas2["IDJugador1"]][$hoyo] <= $array_resultado2_gross_parcial[$array_parejas2["IDJugador2"]][$hoyo]) {
                            $array_resultado2_gross[$hoyo] = $array_resultado2_gross_parcial[$array_parejas2["IDJugador1"]][$hoyo];
                        } else {
                            $array_resultado2_gross[$hoyo] = $array_resultado2_gross_parcial[$array_parejas2["IDJugador2"]][$hoyo];
                        }
                        //Para SUMA
                        $array_resultado2_neto_suma[$hoyo] = $array_resultado2_neto_parcial[$array_parejas2["IDJugador1"]][$hoyo] + $array_resultado2_neto_parcial[$array_parejas2["IDJugador2"]][$hoyo];
                        $array_resultado2_gross_suma[$hoyo] = $array_resultado2_gross_parcial[$array_parejas2["IDJugador1"]][$hoyo] + $array_resultado2_gross_parcial[$array_parejas2["IDJugador2"]][$hoyo];
                    }
                }

                $PuntosJ1_hoyo_1_9 = 0;
                $PuntosJ1_hoyo_10_18 = 0;
                $PuntosJ2_hoyo_1_9 = 0;
                $PuntosJ2_hoyo_10_18 = 0;

                //Comparo resultados de los hoyos
                for ($i = 1; $i <= 18; $i++) {

                    if (array_key_exists($i, $array_resultado1_neto) || array_key_exists($i, $array_resultado2_neto)) {
                        if ($i <= 9)
                            $Hoyos_jugado_1_9 = "S";
                        else
                            $Hoyos_jugado_10_18 = "S";
                    }

                    //Verifico que haya resultado en ambos jugadores en ese hoyo para tomarlo en cuenta en el calculo
                    if (!empty($array_resultado2_neto[$i]) && !empty($array_resultado1_neto[$i])) {
                        if ($array_resultado2_neto[$i] == $array_resultado1_neto[$i]) {
                            $Puntos = 0;
                        } elseif ($array_resultado1_neto[$i] < $array_resultado2_neto[$i]) {
                            if ($i <= 9) {
                                $PuntosJ1_hoyo_1_9 += 1;
                            } else {
                                $PuntosJ1_hoyo_10_18 += 1;
                            }
                        } else {
                            if ($i <= 9) {
                                $PuntosJ2_hoyo_1_9 += 1;
                            } else {
                                $PuntosJ2_hoyo_10_18 += 1;
                            }
                        }
                        $array_detalle_hoyo_jug1[$i] = $array_resultado1_neto[$i];
                        $array_detalle_hoyo_jug2[$i] = $array_resultado2_neto[$i];
                    }
                }

                $PuntosTotalJ1 = $PuntosJ1_hoyo_1_9 + $PuntosJ1_hoyo_10_18;
                $PuntosTotalJ2 = $PuntosJ2_hoyo_1_9 + $PuntosJ2_hoyo_10_18;

                //Hoyo 1-9
                if ((int)$NumeroHoyosJ1_hoyo_1_9 > $NumeroHoyosJ2_hoyo_1_9) {
                    $NumeroHoyoshoyo_1_9Mostrar = $NumeroHoyosJ2_hoyo_1_9;
                } else {
                    $NumeroHoyoshoyo_1_9Mostrar = $NumeroHoyosJ1_hoyo_1_9;
                }
                if ($Hoyos_jugado_1_9 == "S") {
                    if ($PuntosJ1_hoyo_1_9 == $PuntosJ2_hoyo_1_9) {
                        $MostrarTop_Hoyo_1_9 = "E";
                        $DeCuantosHoyos1_9 = "";
                        $Forma_hoyo_1_9 = "ninguna";
                    } elseif ($PuntosJ1_hoyo_1_9 > $PuntosJ2_hoyo_1_9) {
                        $PuntosJ1_hoyo_1_9 -= $PuntosJ2_hoyo_1_9;
                        $MostrarTop_Hoyo_1_9 = $PuntosJ1_hoyo_1_9 . " UP";
                        $DeCuantosHoyos1_9 = "de " . $NumeroHoyoshoyo_1_9Mostrar;
                        $Forma_hoyo_1_9 = "abajo";
                    } else {
                        $PuntosJ2_hoyo_1_9 -= $PuntosJ1_hoyo_1_9;
                        $MostrarTop_Hoyo_1_9 = $PuntosJ2_hoyo_1_9 . " UP";
                        $DeCuantosHoyos1_9 = "de " . $NumeroHoyoshoyo_1_9Mostrar;
                        $Forma_hoyo_1_9 = "abajo";
                    }
                } else {
                    $MostrarTop_Hoyo_1_9 = "";
                    $DeCuantosHoyos1_9 = "";
                    $Forma_hoyo_1_9 = "ninguna";
                }
                //Fin  Hoyo 1-9 



                //Hoyo 10-18
                if ((int)$NumeroHoyosJ1_hoyo_10_18 > $NumeroHoyosJ2_hoyo_10_18) {
                    $NumeroHoyoshoyo_10_18Mostrar = $NumeroHoyosJ2_hoyo_10_18;
                } else {
                    $NumeroHoyoshoyo_10_18Mostrar = $NumeroHoyosJ1_hoyo_10_18;
                }
                if ($Hoyos_jugado_10_18 == "S") {
                    if ($PuntosJ1_hoyo_10_18 == $PuntosJ2_hoyo_10_18) {
                        $MostrarTop_Hoyo_10_18 = "E";
                        $DeCuantosHoyos10_18 = "";
                        $Forma_hoyo_10_18 = "nunguna";
                    } elseif ($PuntosJ1_hoyo_10_18 > $PuntosJ2_hoyo_10_18) {
                        $PuntosJ1_hoyo_10_18 -= $PuntosJ2_hoyo_10_18;
                        $MostrarTop_Hoyo_10_18 = $PuntosJ1_hoyo_10_18 . " UP";
                        $DeCuantosHoyos10_18 = "de " . $NumeroHoyoshoyo_10_18Mostrar;
                        $Forma_hoyo_10_18 = "arriba";
                    } else {
                        $PuntosJ2_hoyo_10_18 -= $PuntosJ1_hoyo_10_18;
                        $MostrarTop_Hoyo_10_18 = $PuntosJ2_hoyo_10_18 . " UP";
                        $DeCuantosHoyos10_18 = "de " . $NumeroHoyoshoyo_10_18Mostrar;
                        $Forma_hoyo_10_18 = "abajo";
                    }
                } else {
                    $MostrarTop_Hoyo_10_18 = "";
                    $DeCuantosHoyos10_18 = "";
                    $Forma_hoyo_10_18 = "ninguna";
                }
                //Fin  Hoyo 10-18 




                //Gran Total
                if ($Hoyos_jugado_1_9 == "S" || $Hoyos_jugado_10_18 == "S") {
                    if ($PuntosTotalJ1 == $PuntosTotalJ2) {
                        $MostrarPuntosTotal = "E";
                        $NumeroHoyosTotal = "";
                        $Forma_hoyo_total = "ninguna";
                    } elseif ($PuntosTotalJ1 > $PuntosTotalJ2) {
                        $PuntosTotalJ1 -= $PuntosTotalJ2;
                        $MostrarPuntosTotal = $PuntosTotalJ1 . " UP";
                        $NumeroHoyosTotal = "des " . $NumeroHoyosJ1;
                        $Forma_hoyo_total = "arriba";
                    } else {
                        $PuntosTotalJ2 -= $PuntosTotalJ1;
                        $MostrarPuntosTotal = $PuntosTotalJ2 . " UP";
                        $NumeroHoyosTotal = "de " . $NumeroHoyosJ2;
                        $Forma_hoyo_total = "abajo";
                    }
                } else {
                    $MostrarPuntosTotal = "";
                    $NumeroHoyosTotal = "";
                    $Forma_hoyo_total = "ninguna";
                }
                //Fin Gran total


                $array_mejor_bola["TituloTarjeta"] = $TituloTarjetaMatch . " mejor bola";
                $array_mejor_bola["LabelPareja1"] = $Pareja1;
                $array_mejor_bola["LabelPareja2"] = $Pareja2;
                $array_mejor_bola["Hoyos1-9Top"] = $MostrarTop_Hoyo_1_9;
                $array_mejor_bola["Hoyos1-9Bottom"] = $DeCuantosHoyos1_9;
                $array_mejor_bola["Hoyos1-9Forma"] = $Forma_hoyo_1_9;
                $array_mejor_bola["Hoyos10-18Top"] = $MostrarTop_Hoyo_10_18;
                $array_mejor_bola["Hoyos10-18Bottom"] = $DeCuantosHoyos10_18;
                $array_mejor_bola["Hoyos10-18Forma"] = $Forma_hoyo_10_18;
                $array_mejor_bola["TotalTop"] = $MostrarPuntosTotal;
                $array_mejor_bola["TotalBottom"] = $NumeroHoyosTotal;
                $array_mejor_bola["TotalForma"] = $Forma_hoyo_total;

                if ($Formato["UsarMejorBolaDelGrupo"] == "S") {
                    $InfoResponse["TarjetaPorHoyosMejorBola"] = $array_mejor_bola;
                    $InfoResponse["DetalleNetoJug1"] = $array_resultado1_neto;
                    $InfoResponse["DetalleGrossJug1"] = $array_resultado1_gross;
                    $InfoResponse["DetalleNetoJug2"] = $array_resultado2_neto;
                    $InfoResponse["DetalleGrossJug2"] = $array_resultado2_gross;
                }

                //CALCULOS SUMA
                $PuntosJ1_hoyo_1_9 = 0;
                $PuntosJ1_hoyo_10_18 = 0;
                $PuntosJ2_hoyo_1_9 = 0;
                $PuntosJ2_hoyo_10_18 = 0;
                $array_detalle_hoyo_jug1 = array();
                $array_detalle_hoyo_jug2 = array();
                //Comparo resultados de los hoyos
                for ($i = 1; $i <= 18; $i++) {

                    if (array_key_exists($i, $array_resultado1_neto_suma) || array_key_exists($i, $array_resultado2_neto_suma)) {
                        if ($i <= 9)
                            $Hoyos_jugado_1_9 = "S";
                        else
                            $Hoyos_jugado_10_18 = "S";
                    }

                    //Verifico que haya resultado en ambos jugadores en ese hoyo para tomarlo en cuenta en el calculo
                    if (!empty($array_resultado2_neto_suma[$i]) && !empty($array_resultado1_neto_suma[$i])) {
                        if ($array_resultado2_neto_suma[$i] == $array_resultado1_neto_suma[$i]) {
                            $Puntos = 0;
                        } elseif ($array_resultado2_neto_suma[$i] < $array_resultado1_neto_suma[$i]) {
                            if ($i <= 9) {
                                $PuntosJ1_hoyo_1_9 += 1;
                            } else {
                                $PuntosJ1_hoyo_10_18 += 1;
                            }
                        } else {
                            if ($i <= 9) {
                                $PuntosJ2_hoyo_1_9 += 1;
                            } else {
                                $PuntosJ2_hoyo_10_18 += 1;
                            }
                        }
                        $array_detalle_hoyo_jug1[$i] = $array_resultado1_neto_suma[$i];
                        $array_detalle_hoyo_jug2[$i] = $array_resultado2_neto_suma[$i];


                        //echo " \nPuntos J2 Hoyo ".$i.": ".$PuntosJ2_hoyo_1_9 . " COMPARA ".$array_resultado2_neto_suma[$i]."==".$array_resultado1_neto_suma[$i];
                    }
                }


                $PuntosTotalJ1 = $PuntosJ1_hoyo_1_9 + $PuntosJ1_hoyo_10_18;
                $PuntosTotalJ2 = $PuntosJ2_hoyo_1_9 + $PuntosJ2_hoyo_10_18;


                //Hoyo 1-9
                if ((int)$NumeroHoyosJ1_hoyo_1_9 > $NumeroHoyosJ2_hoyo_1_9) {
                    $NumeroHoyoshoyo_1_9Mostrar = $NumeroHoyosJ2_hoyo_1_9;
                } else {
                    $NumeroHoyoshoyo_1_9Mostrar = $NumeroHoyosJ1_hoyo_1_9;
                }
                if ($Hoyos_jugado_1_9 == "S") {
                    if ($PuntosJ1_hoyo_1_9 == $PuntosJ2_hoyo_1_9) {
                        $MostrarTop_Hoyo_1_9 = "E";
                        $DeCuantosHoyos1_9 = "";
                        $Forma_hoyo_1_9 = "ninguna";
                    } elseif ($PuntosJ1_hoyo_1_9 > $PuntosJ2_hoyo_1_9) {
                        $PuntosJ1_hoyo_1_9 -= $PuntosJ2_hoyo_1_9;
                        $MostrarTop_Hoyo_1_9 = $PuntosJ1_hoyo_1_9 . " UP";
                        $DeCuantosHoyos1_9 = "de " . $NumeroHoyoshoyo_1_9Mostrar;
                        $Forma_hoyo_1_9 = "abajo";
                    } else {
                        $PuntosJ2_hoyo_1_9 -= $PuntosJ1_hoyo_1_9;
                        $MostrarTop_Hoyo_1_9 = $PuntosJ2_hoyo_1_9 . " UP";
                        $DeCuantosHoyos1_9 = "de " . $NumeroHoyoshoyo_1_9Mostrar;
                        $Forma_hoyo_1_9 = "arriba";
                    }
                } else {
                    $MostrarTop_Hoyo_1_9 = "";
                    $DeCuantosHoyos1_9 = "";
                    $Forma_hoyo_1_9 = "ninguna";
                }
                //Fin  Hoyo 1-9 


                //Hoyo 10-18
                if ((int)$NumeroHoyosJ1_hoyo_10_18 > $NumeroHoyosJ2_hoyo_10_18) {
                    $NumeroHoyoshoyo_10_18Mostrar = $NumeroHoyosJ2_hoyo_10_18;
                } else {
                    $NumeroHoyoshoyo_10_18Mostrar = $NumeroHoyosJ1_hoyo_10_18;
                }
                if ($Hoyos_jugado_10_18 == "S") {
                    if ($PuntosJ1_hoyo_10_18 == $PuntosJ2_hoyo_10_18) {
                        $MostrarTop_Hoyo_10_18 = "E";
                        $DeCuantosHoyos10_18 = "";
                        $Forma_hoyo_10_18 = "nunguna";
                    } elseif ($PuntosJ1_hoyo_10_18 > $PuntosJ2_hoyo_10_18) {
                        $PuntosJ1_hoyo_10_18 -= $PuntosJ2_hoyo_10_18;
                        $MostrarTop_Hoyo_10_18 = $PuntosJ1_hoyo_10_18 . " UP";
                        $DeCuantosHoyos10_18 = "de " . $NumeroHoyoshoyo_10_18Mostrar;
                        $Forma_hoyo_10_18 = "abajo";
                    } else {
                        $PuntosJ2_hoyo_10_18 -= $PuntosJ1_hoyo_10_18;
                        $MostrarTop_Hoyo_10_18 = $PuntosJ2_hoyo_10_18 . " UP";
                        $DeCuantosHoyos10_18 = "de " . $NumeroHoyoshoyo_10_18Mostrar;
                        $Forma_hoyo_10_18 = "arriba";
                    }
                } else {
                    $MostrarTop_Hoyo_10_18 = "";
                    $DeCuantosHoyos10_18 = "";
                    $Forma_hoyo_10_18 = "ninguna";
                }
                //Fin  Hoyo 10-18 




                //Gran Total
                if ($Hoyos_jugado_1_9 == "S" || $Hoyos_jugado_10_18 == "S") {
                    if ($PuntosTotalJ1 == $PuntosTotalJ2) {
                        $MostrarPuntosTotal = "E";
                        $NumeroHoyosTotal = "";
                        $Forma_hoyo_total = "ninguna";
                    } elseif ($PuntosTotalJ1 > $PuntosTotalJ2) {
                        $PuntosTotalJ1 -= $PuntosTotalJ2;
                        $MostrarPuntosTotal = $PuntosTotalJ1 . " UP";
                        $NumeroHoyosTotal = "de " . $NumeroHoyosJ1;
                        $Forma_hoyo_total = "abajo";
                    } else {
                        $PuntosTotalJ2 -= $PuntosTotalJ1;
                        $MostrarPuntosTotal = $PuntosTotalJ2 . " UP";
                        $NumeroHoyosTotal = "de " . $NumeroHoyosJ2;
                        $Forma_hoyo_total = "arriba";
                    }
                } else {
                    $MostrarPuntosTotal = "";
                    $NumeroHoyosTotal = "";
                    $Forma_hoyo_total = "ninguna";
                }

                //Fin Gran total



                $array_suma["TituloTarjeta"] = $TituloTarjetaMatch . " suma";
                $array_suma["LabelPareja1"] = $Pareja1;
                $array_suma["LabelPareja2"] = $Pareja2;
                $array_suma["Hoyos1-9Top"] = $MostrarTop_Hoyo_1_9;
                $array_suma["Hoyos1-9Bottom"] = $DeCuantosHoyos1_9;
                $array_suma["Hoyos1-9Forma"] = $Forma_hoyo_1_9;
                $array_suma["Hoyos10-18Top"] = $MostrarTop_Hoyo_10_18;
                $array_suma["Hoyos10-18Bottom"] = $DeCuantosHoyos10_18;
                $array_suma["Hoyos10-18Forma"] = $Forma_hoyo_10_18;
                $array_suma["TotalTop"] = $MostrarPuntosTotal;
                $array_suma["TotalBottom"] = $NumeroHoyosTotal;
                $array_suma["TotalForma"] = $Forma_hoyo_total;

                if ($Formato["UsarSumaDelGrupo"] == "S") {
                    $InfoResponse["TarjetaPorHoyosSuma"] = $array_suma;
                    $InfoResponse["DetalleNetoJug1Suma"] = $array_resultado1_neto_suma;
                    $InfoResponse["DetalleGrossJug1Suma"] = $array_resultado1_gross_suma;
                    $InfoResponse["DetalleNetoJug2Suma"] = $array_resultado2_neto_suma;
                    $InfoResponse["DetalleGrossJug2Suma"] = $array_resultado2_gross_suma;
                }


                array_push($response, $InfoResponse);
            endwhile;
            $respuesta[message] = "Datos encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $response;
        } else {
            $respuesta[message] = "GGG2. Atencion Faltan Parametros!" . $IDJugador;
            $respuesta[success] = false;
            $respuesta[response] = "";
        }
        return $respuesta;
    }

    public function calcular_resultado_tarjeta($IDClub, $IDJugador, $IDJuego)
    {
        $dbo2 = &SIMDB2::get();
        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuego . "' ", "array");
        $datos_jugador_grupo = $dbo2->fetchAll("GameGolfJuegoGrupoJugadores ", " IDJugador = '" . $IDJugador . "' and IDGameGolfJuego = '" . $IDJuego . "' ", "array");
        $PuntosAsignados = 0;
        $ConsultaHoyos = "S";
        $InfoGolpes = "N";
        $Abandono = "N";

        //Consulto los Golpes del jugador
        $SQLGolpe = "SELECT Hoyo, NumeroGolpes FROM GameGolfGolpe   WHERE IDGameGolfJuego = '" . $IDJuego . "' and  IDJugador = '" . $IDJugador . "' Order by Hoyo ASC ";
        $QRYGolpe = $dbo2->query($SQLGolpe);
        while ($Golpe = $dbo2->fetchArray($QRYGolpe)) :
            $array_golpe[$Golpe["Hoyo"]] = $Golpe["NumeroGolpes"];
            if ($Golpe["NumeroGolpes"] == "-1")
                $Abandono = "S";

            //$UltimoHoyo=$Golpe["Hoyo"];
            $InfoGolpes = "S";
            $TotalHoyos++;
        endwhile;

        //Segun los formatos de juego por cada uno guardo las tarjetas
        $sql_formato_juego = "SELECT IDGameGolfJuegoFormato, IDGameGolfFormato,IDJugadorPrincipal, IDJugadorJuego, UsarHandicap,UsarHandicapCampo,PorcentajeHandicap,VentajaSeleccionada,MenorHandicapBajaCero FROM GameGolfJuegoFormato WHERE IDGameGolfJuego = '" . $IDJuego . "'";
        $r_formato_juego = $dbo2->query($sql_formato_juego);
        while ($row_formato_juego = $dbo2->fetchArray($r_formato_juego)) {
            $PuntosAsignados = 0;
            $OtrosPuntos = 0;
            $InsertaFormato = "S";
            if ($InfoGolpes == "S") {
                $datos_formato = $dbo2->fetchAll("GameGolfFormato", " IDGameGolfFormato = '" . $row_formato_juego["IDGameGolfFormato"] . "' ", "array");
                //Individual versus debe realizar otros calculos                     
                if ($datos_formato["Tipo"] == "individual_versus") {
                    if ($IDJugador == $row_formato_juego["IDJugadorPrincipal"] || $IDJugador == $row_formato_juego["IDJugadorJuego"]) {
                        $InsertaFormato = "S";
                    } else {
                        $InsertaFormato = "N";
                    }
                } else {
                    $InsertaFormato = "S";
                }

                if ($InsertaFormato == "S") {
                    $Neto = 0;
                    $TotalNeto = 0;
                    $TotalNeto1_9 = 0;
                    $TotalNeto10_18 = 0;
                    $TotalGolpes = 0;
                    $DeberiaLlevar = 0;
                    $TotalGolpes1_9 = 0;
                    $DeberiaLlevar1_9 = 0;
                    $TotalGolpes10_18 = 0;
                    $DeberiaLlevar10_18 = 0;
                    $array_hoyo_hcp = array();

                    if ($row_formato_juego["UsarHandicap"] == "S") {
                        $Handicap = (int)$datos_jugador_grupo["Handicap"];
                        if ($row_formato_juego["PorcentajeHandicap"] != 100) {
                            $Handicap = (int)round($datos_jugador_grupo["Handicap"] * (int)$row_formato_juego["PorcentajeHandicap"] / 100);
                        }


                        if ($row_formato_juego["MenorHandicapBajaCero"] == "S") {
                            //averiguo el handicap del contrincante		
                            if ($row_formato_juego["IDJugadorPrincipal"] == $IDJugador)
                                $IDJugadorContrincante = $row_formato_juego["IDJugadorJuego"];
                            else
                                $IDJugadorContrincante = $row_formato_juego["IDJugadorPrincipal"];


                            $datos_jugador_grupo_contrincante = $dbo2->fetchAll("GameGolfJuegoGrupoJugadores ", " IDJugador = '" . $IDJugadorContrincante . "' and IDGameGolfJuego = '" . $IDJuego . "' ", "array");
                            $HandicapContrincante = (int)$datos_jugador_grupo_contrincante["Handicap"];
                            if ($row_formato_juego["PorcentajeHandicap"] != 100) {
                                $HandicapContrincante = (int)round($datos_jugador_grupo_contrincante["Handicap"] * (int)$row_formato_juego["PorcentajeHandicap"] / 100);
                            }

                            if ($Handicap == $HandicapContrincante) {
                                $Handicap = 0;
                                $HandicapContrincante = 0;
                            } else {

                                if ($Handicap > $HandicapContrincante) {
                                    if ($HandicapContrincante < 0)
                                        $Handicap += $HandicapContrincante;
                                    else
                                        $Handicap -= $HandicapContrincante;

                                    $HandicapContrincante = 0;
                                } else {
                                    if ($Handicap < 0)
                                        $HandicapContrincante += $Handicap;
                                    else
                                        $HandicapContrincante -= $Handicap;

                                    $Handicap = 0;
                                }
                            }
                        }
                    }







                    if (!empty($Handicap)) {
                        if ($Handicap > 0) {
                            $Operacion = "Sumar";
                            $CondicionOrden = " ORDER BY Handicap ASC";
                        } elseif ($Handicap < 0) {
                            $CondicionOrden = " ORDER BY Handicap DESC";
                            $Operacion = "Restar";
                        }

                        $PuntosHcp = abs($Handicap);
                        // Se reparten los puntos del handicap entre los hoyos en este caso a alos mas dificiles les doy ventaja
                        if ($PuntosHcp > 18) {
                            $PuntosTodosHoyos = (int)($PuntosHcp / 18);
                            $OtrosPuntos = $PuntosHcp - (18 * $PuntosTodosHoyos);
                        } else {
                            $OtrosPuntos = $PuntosHcp;
                        }
                    }




                    //Consulto los hoyos y los ordeno por el handicap solo 1 vez
                    //if($ConsultaHoyos=="S"){
                    $array_hoyo_punto = array();
                    $sql_hoyos = "SELECT IDGameGolfCourse, Hoyo, Par, Handicap FROM GameGolfHoyos  WHERE IDGameGolfCourse = '" . $datos_juego["IDGolfCourse"] . "' and IDGameGolfMarca = '" . $datos_jugador_grupo["IDGameGolfMarca"] . "' " . $CondicionOrden;
                    $r_hoyos = $dbo2->query($sql_hoyos);
                    while ($row_hoyos = $dbo2->fetchArray($r_hoyos)) {
                        $array_hoyo_punto[$row_hoyos["Hoyo"]] = $PuntosTodosHoyos;
                        $array_hoyo_hcp[$row_hoyos["Hoyo"]] = $row_hoyos["Handicap"];
                        $array_hoyo_par[$row_hoyos["Hoyo"]] = $row_hoyos["Par"];
                        if ($OtrosPuntos > 0 && $PuntosAsignados < $OtrosPuntos) {
                            $array_hoyo_punto[$row_hoyos["Hoyo"]]++;
                        }
                        $PuntosAsignados++;
                        $ConsultaHoyos = "N";
                    }
                    //}   




                    $IDTarjeta = $dbo2->getFields("GameGolfJuegoTarjeta", "IDGameGolfJuegoTarjeta", "IDClub = '" . $IDClub . "' and IDGameGolfJuego = '" . $IDJuego . "' and IDJugadorJuego = '" . $IDJugador . "' and IDGameGolfFormato='" . $row_formato_juego["IDGameGolfFormato"] . "' and IDGameGolfJuegoFormato = '" . $row_formato_juego["IDGameGolfJuegoFormato"] . "' ");
                    if ($IDTarjeta <= 0 && (int)$IDJugador > 0) {
                        $inserta_tarjeta = "INSERT INTO GameGolfJuegoTarjeta (IDClub,IDGameGolfJuego,IDGameGolfJuegoFormato,IDGameGolfFormato,IDJugadorJuego,Resultado,SobrePar,EN,UsuarioTrCr,FechaTrCr) 
                                                    VALUES('" . $IDClub . "','" . $IDJuego . "', '" . $row_formato_juego["IDGameGolfJuegoFormato"] . "' , '" . $row_formato_juego["IDGameGolfFormato"] . "' ,'" . $IDJugador . "','" . $ResultadoTotal . "','" . $SobrePar . "','" . $UltimoHoyo . "','WS',NOW()) ";
                        $dbo2->query($inserta_tarjeta);
                        $IDTarjeta = $dbo2->lastID();
                    } else {
                        $sql_borra = "DELETE FROM GameGolfJuegoTarjetaDetalle WHERE IDGameGolfJuegoTarjeta = '" . $IDTarjeta . "' ";
                        $dbo2->query($sql_borra);
                    }

                    foreach ($array_hoyo_punto as $hoyo => $puntos) {
                        if ($TotalHoyos == 18)
                            $UltimoHoyo = "F";

                        if (((int)$array_golpe[$hoyo] > 0 || $array_golpe[$hoyo] == -1) && (int)$IDJugador > 0) {
                            //Calculo el neto
                            if ($Handicap >= 0)
                                $Neto = $array_golpe[$hoyo] - $puntos;
                            else
                                $Neto = $array_golpe[$hoyo] + $puntos;

                            $TotalNeto += $Neto;
                            //echo "<br>" . $TotalNeto;
                            $TotalGolpes += $array_golpe[$hoyo];
                            $DeberiaLlevar += $array_hoyo_par[$hoyo];

                            if ($Abandono == "S") {
                                $TotalNeto = 0;
                                $TotalGolpes += 0;
                                $DeberiaLlevar += 0;
                                $Neto = 0;
                            }

                            $inserta_tarjeta_detalle = "INSERT INTO GameGolfJuegoTarjetaDetalle (IDGameGolfJuegoTarjeta,IDClub,IDGameGolfJuego,IDJugadorJuego,Hoyo,Puntos,HandicapHoyo,Resultado,Neto,UsuarioTrCr,FechaTrCr) 
                                                                    VALUES('" . $IDTarjeta . "','" . $IDClub . "','" . $IDJuego . "','" . $IDJugador . "','" . $hoyo . "','" . $puntos . "','" . $array_hoyo_hcp[$hoyo] . "','" . $array_golpe[$hoyo] . "','" . $Neto . "','WS',NOW()) ";
                            $dbo2->query($inserta_tarjeta_detalle);

                            if ($hoyo <= 9) {
                                if ($datos_formato["MostrarNeto"] == "N") {
                                    $TotalGolpes1_9 += $array_golpe[$hoyo];
                                    $DeberiaLlevar1_9 += $array_hoyo_par[$hoyo];
                                } else {
                                    $TotalNeto1_9 += $Neto;
                                    $DeberiaLlevar1_9 += $array_hoyo_par[$hoyo];
                                }
                            } else {
                                if ($datos_formato["MostrarNeto"] == "N") {
                                    $TotalGolpes10_18 += $array_golpe[$hoyo];
                                    $DeberiaLlevar10_18 += $array_hoyo_par[$hoyo];
                                } else {
                                    $TotalNeto10_18 += $Neto;
                                    $DeberiaLlevar10_18 += $array_hoyo_par[$hoyo];
                                }
                            }
                        }
                    }

                    if ($datos_formato["MostrarNeto"] == "N") {
                        $SobrePar = $TotalGolpes - $DeberiaLlevar;
                        $SobrePar1_9 = $TotalGolpes1_9 - $DeberiaLlevar1_9;
                        $SobrePar10_18 = $TotalGolpes10_18 - $DeberiaLlevar10_18;
                        $ResultadoGuardar = $TotalGolpes;
                        $ResultadoGuardar1_9 = $TotalGolpes1_9;
                        $ResultadoGuardar10_18 = $TotalGolpes1_9;
                    } else {
                        $SobrePar = $TotalNeto - $DeberiaLlevar;
                        $SobrePar1_9 = $TotalNeto1_9 - $DeberiaLlevar1_9;
                        $SobrePar10_18 = $TotalNeto10_18 - $DeberiaLlevar10_18;
                        $ResultadoGuardar = $TotalNeto;
                        $ResultadoGuardar1_9 = $TotalNeto1_9;
                        $ResultadoGuardar10_18 = $TotalNeto10_18;
                    }

                    if ($Abandono == "S") {
                        $ResultadoGuardar = "0"; //identificador de abandonado
                        $SobrePar = "-9999"; // identificador de abandonado
                        $TotalHoyos = "F";
                        $TotalGolpes = 0;
                    }

                    //Actualizo la tarjeta                
                    $actualiza_tarjeta = "UPDATE GameGolfJuegoTarjeta  
                                                    SET Resultado = '" . $ResultadoGuardar . "', SobrePar = '" . $SobrePar . "', En = '" . $TotalHoyos . "', Puntaje = '" . $TotalGolpes . "', Resultado1_9='" . $ResultadoGuardar1_9 . "', SobrePar1_9='" . $SobrePar1_9 . "',Resultado10_18='" . $ResultadoGuardar10_18 . "', SobrePar10_18='" . $SobrePar10_18 . "'
                                                    WHERE IDGameGolfJuegoTarjeta= '" . $IDTarjeta . "'";
                    $dbo2->query($actualiza_tarjeta);
                }
            } else {
                $IDTarjeta = $dbo2->getFields("GameGolfJuegoTarjeta", "IDGameGolfJuegoTarjeta", "IDClub = '" . $IDClub . "' and IDGameGolfJuego = '" . $IDJuego . "' and IDJugadorJuego = '" . $IDJugador . "' and IDGameGolfFormato='" . $row_formato_juego["IDGameGolfFormato"] . "' ");
                if ($IDTarjeta <= 0 && (int)$IDJugador > 0) {
                    $inserta_tarjeta = "INSERT INTO GameGolfJuegoTarjeta (IDClub,IDGameGolfJuego,IDGameGolfFormato,IDGameGolfJuegoFormato,IDJugadorJuego,Resultado,SobrePar,EN,UsuarioTrCr,FechaTrCr) 
                                                VALUES('" . $IDClub . "','" . $IDJuego . "', '" . $row_formato_juego["IDGameGolfFormato"] . "' ,'" . $row_formato_juego["IDGameGolfJuegoFormato"] . "','" . $IDJugador . "','0','0','0','WS',NOW()) ";
                    $dbo2->query($inserta_tarjeta);
                    $IDTarjeta = $dbo2->lastID();
                } else {
                    $sql_actualiza = "UPDATE GameGolfJuegoTarjeta SET Resultado=0,SobrePar=0,EN=0 WHERE IDGameGolfJuegoTarjeta = '" . $IDTarjeta . "' ";
                    $dbo2->query($sql_actualiza);
                }
            }
        }
    }

    public function calcular_resultado_tarjeta_grupo($IDClub, $IDJuego)
    {
        $dbo2 = &SIMDB2::get();

        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuego . "' ", "array");

        //Consulto los hoyos y los ordeno por el handicap
        $sql_hoyos = "SELECT IDGameGolfCourse, Hoyo, Par, Handicap FROM GameGolfHoyos  WHERE IDGameGolfCourse = '" . $datos_juego["IDGolfCourse"] . "' and IDGameGolfMarca = '" . $datos_juego["IDGameGolfMarca"] . "' ";
        $r_hoyos = $dbo2->query($sql_hoyos);
        while ($row_hoyos = $dbo2->fetchArray($r_hoyos)) {
            $array_hoyo_punto[$row_hoyos["Hoyo"]] = $PuntosTodosHoyos;
            $array_hoyo_hcp[$row_hoyos["Hoyo"]] = $row_hoyos["Handicap"];
            $array_hoyo_par[$row_hoyos["Hoyo"]] = $row_hoyos["Par"];
            if ($row_hoyos["Handicap"] <= $OtrosPuntos && $OtrosPuntos > 0) {
                if ($Operacion == "Sumar")
                    $array_hoyo_punto[$row_hoyos["Hoyo"]]++;
                else
                    $array_hoyo_punto[$row_hoyos["Hoyo"]]--;
            }
        }


        //Solo lo calculo si se agregó como formato de juego
        $sql_formato = "SELECT GGJF.IDGameGolfJuegoFormato, GGJF.IDGameGolfFormato, GGF.Tipo, GGJF.NumeroDeResultados, GGF.MostrarNeto, GGF.CalcularSobre, GGJF.UsarMejorBolaDelGrupo, GGJF.UsarSumaDelGrupo
                          FROM GameGolfJuegoFormato GGJF, GameGolfFormato GGF
                          WHERE GGJF.IDGameGolfFormato=GGF.IDGameGolfFormato and (GGF.Tipo = 'grupal' or GGF.Tipo = 'sub_grupal' ) and GGJF.IDGameGolfJuego = '" . $IDJuego . "' ";

        $r_formato = $dbo2->query($sql_formato);
        while ($row_formato = $dbo2->fetchArray($r_formato)) {

            if ($row_formato["Tipo"] == "grupal") {
                $sql_grupo = "SELECT IDGameGolfJuegoGrupo FROM GameGolfJuegoGrupo WHERE IDGameGolfJuego = '" . $IDJuego . "' ";
                $r_grupo = $dbo2->query($sql_grupo);
                $NumeroMejorBolas = $row_formato["NumeroDeResultados"];
            } elseif ($row_formato["Tipo"] == "sub_grupal") {
                $sql_grupo = "SELECT IDGameGolfJuegoFormatoSubGrupo FROM GameGolfJuegoFormatoSubGrupo WHERE IDGameGolfJuego = '" . $IDJuego . "' and IDGameGolfJuegoFormato = '" . $row_formato["IDGameGolfJuegoFormato"] . "'";
                $r_grupo = $dbo2->query($sql_grupo);
                if ($row_formato["UsarMejorBolaDelGrupo"] == "S") {
                    $NumeroMejorBolas = 1;
                } elseif ($row_formato["UsarSumaDelGrupo"] == "S") {
                    $NumeroMejorBolas = 2; //sumo el de tods de la llave pongo un valor alto por lo general la llave es 2
                } else {
                    $NumeroMejorBolas = 1;
                }
            }



            //Calculo los grupos

            while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                $TotalGolpes = 0;
                $TotalNeto = 0;
                $SobrePar = 0;
                $DeberiaLlevar = 0;
                $array_id_jugador = array();
                $id_jugador_grupo = "";
                //Consulto Jugadores del grupo
                if ($row_formato["Tipo"] == "grupal") {
                    $sql_jugador = "SELECT IDJugador FROM GameGolfJuegoGrupoJugadores WHERE IDGameGolfJuegoGrupo = '" . $row_grupo["IDGameGolfJuegoGrupo"] . "'";
                } elseif ($row_formato["Tipo"] == "sub_grupal") {
                    $sql_jugador = "SELECT IDJugador FROM GameGolfJuegoFormatoSubGrupoDetalle  WHERE IDGameGolfJuegoFormatoSubGrupo = '" . $row_grupo["IDGameGolfJuegoFormatoSubGrupo"] . "'";
                }


                $r_jugador = $dbo2->query($sql_jugador);
                while ($row_jugador = $dbo2->fetchArray($r_jugador)) {
                    if ((int)$row_jugador["IDJugador"] > 0)
                        $array_id_jugador[] = $row_jugador["IDJugador"];
                }
                if (count($array_id_jugador) > 0) {
                    $id_jugador_grupo = implode(",", $array_id_jugador);
                }

                if ($row_formato["Tipo"] == "grupal") {
                    $IDTarjeta = $dbo2->getFields("GameGolfJuegoTarjeta", "IDGameGolfJuegoTarjeta", "IDClub = '" . $IDClub . "' and IDGameGolfJuego = '" . $IDJuego . "' and IDGameGolfJuegoGrupo = '" . $row_grupo["IDGameGolfJuegoGrupo"] . "' and IDGameGolfFormato='" . $row_formato["IDGameGolfFormato"] . "' and IDGameGolfJuegoFormato = '" . $row_formato["IDGameGolfJuegoFormato"] . "' ");
                } elseif ($row_formato["Tipo"] == "sub_grupal") {
                    $IDTarjeta = $dbo2->getFields("GameGolfJuegoTarjeta", "IDGameGolfJuegoTarjeta", "IDClub = '" . $IDClub . "' and IDGameGolfJuego = '" . $IDJuego . "' and IDGameGolfJuegoFormatoSubGrupo = '" . $row_grupo["IDGameGolfJuegoFormatoSubGrupo"] . "' and IDGameGolfFormato='" . $row_formato["IDGameGolfFormato"] . "' and IDGameGolfJuegoFormato = '" . $row_formato["IDGameGolfJuegoFormato"] . "' ");
                }


                if ($IDTarjeta <= 0) {
                    $inserta_tarjeta = "INSERT INTO GameGolfJuegoTarjeta (IDClub,IDGameGolfJuego,IDGameGolfFormato,IDGameGolfJuegoFormato,IDGameGolfJuegoGrupo,IDGameGolfJuegoFormatoSubGrupo, Resultado,SobrePar,EN,UsuarioTrCr,FechaTrCr) 
                                            VALUES('" . $IDClub . "','" . $IDJuego . "', '" . $row_formato["IDGameGolfFormato"] . "','" . $row_formato["IDGameGolfJuegoFormato"] . "','" . $row_grupo["IDGameGolfJuegoGrupo"] . "','" . $row_grupo["IDGameGolfJuegoFormatoSubGrupo"] . "','" . $ResultadoTotal . "','" . $SobrePar . "','" . $UltimoHoyo . "','WS',NOW()) ";
                    $dbo2->query($inserta_tarjeta);
                    $IDTarjeta = $dbo2->lastID();
                } else {
                    $sql_borra = "DELETE FROM GameGolfJuegoTarjetaDetalle WHERE IDGameGolfJuegoTarjeta = '" . $IDTarjeta . "' ";
                    $dbo2->query($sql_borra);
                }


                $SQLhoyo = "SELECT Hoyo FROM GameGolfGolpe WHERE IDGameGolfJuego = '" . $IDJuego . "' and NumeroGolpes > 0 and IDJugador in (" . $id_jugador_grupo . ") Group by Hoyo Order by Hoyo ASC";
                $QRYHoyo = $dbo2->query($SQLhoyo);
                $TotalGolpes = 0;
                $SobrePar = 0;
                $TotalCalculo = 0;
                while ($row_hoyo = $dbo2->fetchArray($QRYHoyo)) :
                    $SumaNetos = 0;
                    $ParEsperado = $array_hoyo_par[$row_hoyo["Hoyo"]] * $NumeroMejorBolas;


                    //Calculo las mejores bolas	                        				
                    if ($row_formato["CalcularSobre"] == "Golpe") {
                        $sql_mejor_bolas = "SELECT DISTINCT(GGTD.IDJugadorJuego), GGTD.Resultado as ValorCalculo 
                                                FROM  GameGolfJuegoTarjetaDetalle GGTD, GameGolfJuegoTarjeta GGT 
                                                WHERE GGTD.IDGameGolfJuegoTarjeta = GGT.IDGameGolfJuegoTarjeta and GGTD.IDJugadorJuego in (" . $id_jugador_grupo . ") and Hoyo = '" . $row_hoyo["Hoyo"] . "' and GGTD.IDGameGolfJuego = '" . $IDJuego . "' and GGT.IDGameGolfFormato=2
                                                ORDER by Resultado ASC 
                                                LIMIT " . $NumeroMejorBolas;
                    } else {
                        $sql_mejor_bolas = "SELECT DISTINCT(GGTD.IDJugadorJuego), GGTD.Neto as ValorCalculo 
                                                FROM  GameGolfJuegoTarjetaDetalle GGTD, GameGolfJuegoTarjeta GGT  
                                                WHERE GGTD.IDGameGolfJuegoTarjeta = GGT.IDGameGolfJuegoTarjeta and  GGTD.IDJugadorJuego in (" . $id_jugador_grupo . ") and Hoyo = '" . $row_hoyo["Hoyo"] . "' and GGTD.IDGameGolfJuego = '" . $IDJuego . "' and GGT.IDGameGolfFormato=1
                                                ORDER by Neto ASC 
                                                LIMIT " . $NumeroMejorBolas;
                    }



                    $r_mejor_bolas = $dbo2->query($sql_mejor_bolas);
                    while ($row_mejor_bolas = $dbo2->fetchArray($r_mejor_bolas)) {
                        $SumaNetos += $row_mejor_bolas["ValorCalculo"];
                        $TotalCalculo += $row_mejor_bolas["ValorCalculo"];
                    }

                    $Resultado = $SumaNetos - $ParEsperado;
                    $SobrePar += $Resultado;
                    $UltimoHoyo = $row_hoyo["Hoyo"];
                    $inserta_tarjeta_detalle = "INSERT INTO GameGolfJuegoTarjetaDetalle (IDGameGolfJuegoTarjeta,IDClub,IDGameGolfJuego,IDGameGolfJuegoGrupo,IDGameGolfJuegoFormatoSubGrupo,Hoyo,Resultado,UsuarioTrCr,FechaTrCr) 
                                                VALUES('" . $IDTarjeta . "','" . $IDClub . "','" . $IDJuego . "','" . $row_grupo["IDGameGolfJuegoGrupo"] . "','" . $row_grupo["IDGameGolfJuegoFormatoSubGrupo"] . "','" . $row_hoyo["Hoyo"] . "','" . $Resultado . "','WS',NOW()) ";
                    $dbo2->query($inserta_tarjeta_detalle);

                endwhile;

                //Actualizo la tarjeta con el resultado
                $actualiza_tarjeta = "UPDATE GameGolfJuegoTarjeta  SET Resultado = '" . $TotalCalculo . "', SobrePar = '" . $SobrePar . "', En = '" . $UltimoHoyo . "', Puntaje = '" . $TotalGolpes . "' WHERE IDGameGolfJuegoTarjeta= '" . $IDTarjeta . "'";
                $dbo2->query($actualiza_tarjeta);
            }
        }
    }

    public function valida_edicion_jugador_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGolfCourse, $IDMarca, $Handicap, $HoyosAJugar, $HoyoInicial)
    {
        //info del campo
        $resp = self::get_juegos_golf_courses($IDClub, $IDSocio, $IDUsuario, $Texto, $IDGolfCourse);
        $GolfCourse = $resp["response"][0];
        $InfoResponse["GolfCourse"] = $GolfCourse;
        $MarcasCampo = $InfoResponse["GolfCourse"]["Marcas"];
        foreach ($MarcasCampo as $datos_marca) {
            if ($datos_marca["IDMarca"] == $IDMarca) {
                $array_marcas_jugara[] = $datos_marca["ConfiguracionHoyos"];
            }
        }
        if ($HoyosAJugar == "1-18") {
            $respuesta[message] = "Validacion exitosa";
            $respuesta[success] = true;
            $respuesta[response] = null;
        } elseif (in_array($HoyosAJugar, $array_marcas_jugara)) {
            $respuesta[message] = "Validacion exitosa";
            $respuesta[success] = true;
            $respuesta[response] = null;
        } else {
            $respuesta[message] = "Esta marca no es posible jugarla segun la configuracion del juego";
            $respuesta[success] = false;
            $respuesta[response] = null;
        }
        return $respuesta;
    }

    public function get_handicap_juegos_golf($IDClub, $IDGolfCourse, $IDMarca, $Jugadores, $IDSocio)
    {
        $dbo = &SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response_jugador = array();
        $datos_jugadores = json_decode($Jugadores, true);
        if (!empty($IDGolfCourse) && !empty($IDMarca) && count($datos_jugadores) > 0) {
            $datos_cancha = $dbo2->fetchAll("GameGolfCourse", " IDGameGolfCourse = '" . $IDGolfCourse . "' ", "array");
            foreach ($datos_jugadores as $array_jugador) {
                $datos_jugador = $dbo->fetchAll("Socio", " IDSocio = '" . $array_jugador["IDJugador"] . "' ", "array");
                $datos_marca = $dbo2->fetchAll("GameGolfMarca", " IDGameGolfMarca = '" . $array_jugador["IDMarca"] . "' ", "array");
                if ($IDClub == 17) {
                    $datos_cancha["IDCampoExterno"] . "-" . $datos_cancha["IDExterno"] . "-" . $datos_marca["IDExterno"] . "-" . $datos_jugador["Accion"] . "-" . $IDSocio;
                    $resp_hcp = SIMWebServiceFedegolf::get_handicap($datos_cancha["IDCampoExterno"], $datos_cancha["IDExterno"], $datos_marca["IDExterno"], $datos_jugador["Accion"], $IDSocio);
                    $array_handicap_jugador["IDJugador"] = $array_jugador["IDJugador"];
                    $array_handicap_jugador["Handicap"] = (string)$resp_hcp["response"];
                    array_push($response_jugador, $array_handicap_jugador);
                } else {
                    //Consulto el handcap de la ficha del socio
                    $array_handicap_jugador["IDJugador"] = $datos_jugador["IDJugador"];
                    $array_handicap_jugador["Handicap"] = (string)$datos_jugador["Handicap"];
                    array_push($response_jugador, $array_handicap_jugador);
                }
            }

            $respuesta[message] = "exitoso";
            $respuesta[success] = true;
            $respuesta[response] = $response_jugador;
        } else {
            $respuesta[message] = "faltan parametros";
            $respuesta[success] = true;
            $respuesta[response] = null;
        }
        return $respuesta;
    }

    public function get_mis_juegos_golf($IDClub, $IDSocio, $IDUsuario, $Fecha)
    {
        $dbo2 = &SIMDB2::get();
        $response = array();

        if (!empty($Fecha)) {
            $condicion_fecha = " and date(GGJ.FechaTrCr) = '" . $Fecha . "' ";
        }

        $SQLDatos = "SELECT GGJGJ.IDGameGolfJuego, GGJ.FechaTrCr, GGJ.IDGolfCourse, GGC.Nombre as NombreCourse
                         FROM  GameGolfJuegoGrupoJugadores GGJGJ,  GameGolfJuego GGJ, GameGolfCourse GGC 
                         WHERE GGJGJ.IDGameGolfJuego=GGJ.IDGameGolfJuego and 
                         GGC.IDGameGolfCourse =  GGJ.IDGolfCourse and IDJugador = '" . $IDSocio . "' " . $condicion_fecha . " 
                         GROUP BY GGJGJ.IDGameGolfJuego
                         ORDER BY GGJGJ.IDGameGolfJuego DESC";
        $QRYDatos = $dbo2->query($SQLDatos);
        while ($Datos = $dbo2->fetchArray($QRYDatos)) :
            //Consulto el puntaje en ese juego
            $sql_tarjeta = "SELECT SobrePar 
                              FROM GameGolfJuegoTarjetaHistorial
                              WHERE IDGameGolfJuego = '" . $Datos["IDGameGolfJuego"] . "' and IDJugadorJuego = '" . $IDSocio . "' LIMIT 1";
            $r_tarjeta = $dbo2->query($sql_tarjeta);
            $row_tarjeta = $dbo2->fetchArray($r_tarjeta);
            $InfoResponse["IDJuegoGolf"] = $Datos["IDGameGolfJuego"];
            $InfoResponse["Fecha"] = substr($Datos["FechaTrCr"], 0, 10);
            $InfoResponse["IDGolfCourse"] = $Datos["IDGolfCourse"];
            $InfoResponse["NombreCourse"] = $Datos["NombreCourse"];
            $InfoResponse["PuntajeGeneral"] = $row_tarjeta["SobrePar"];;
            array_push($response, $InfoResponse);
        endwhile;
        $respuesta[message] = "Datos encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;
        return $respuesta;
    }

    public function get_juegos_golf_de_mis_grupos($IDClub, $IDSocio, $IDUsuario, $Fecha)
    {
        $dbo = &SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response = array();

        if (!empty($Fecha)) {
            $condicion_fecha = " and date(GGJ.FechaTrCr) = '" . $Fecha . "' ";
        }

        ///Busco los jugadores de mis grupos
        $SQLDatos = "SELECT GS.IDGameGolfJuegoGrupoSocio as IDGrupo, GS.Nombre, GJ.IDJugador
                         FROM  GameGolfJuegoGrupoSocioJugadores GJ, GameGolfJuegoGrupoSocio GS
                         WHERE GJ.IDGameGolfJuegoGrupoSocio = GS.IDGameGolfJuegoGrupoSocio  and (GJ.IDJugador = '" . $IDSocio . "' or GS.IDSocio='" . $IDSocio . "')";
        $QRYDatos = $dbo2->query($SQLDatos);
        while ($Datos = $dbo2->fetchArray($QRYDatos)) :
            if ($IDSocio <> $Datos["IDJugador"])
                $array_id_jugador[] = $Datos["IDJugador"];
        endwhile;

        if (count($array_id_jugador) > 0) {
            $id_jugadores = implode(",", $array_id_jugador);
            $SQLDatos = "SELECT GGJGJ.IDGameGolfJuego, GGJ.FechaTrCr, GGJ.IDGolfCourse, GGC.Nombre as NombreCourse, GGJGJ.IDJugador
                         FROM  GameGolfJuegoGrupoJugadores GGJGJ,  GameGolfJuego GGJ, GameGolfCourse GGC 
                         WHERE GGJGJ.IDGameGolfJuego=GGJ.IDGameGolfJuego and 
                         GGC.IDGameGolfCourse =  GGJ.IDGolfCourse and IDJugador in (" . $id_jugadores . ") " . $condicion_fecha . " 
                         GROUP BY GGJGJ.IDGameGolfJuego
                         ORDER BY GGJGJ.IDGameGolfJuego DESC";
            $QRYDatos = $dbo2->query($SQLDatos);
            while ($Datos = $dbo2->fetchArray($QRYDatos)) :
                //Consulto el puntaje en ese juego
                $sql_tarjeta = "SELECT SobrePar 
                                FROM GameGolfJuegoTarjetaHistorial
                                WHERE IDGameGolfJuego = '" . $Datos["IDGameGolfJuego"] . "' and IDJugadorJuego = '" . $IDSocio . "' LIMIT 1";
                $r_tarjeta = $dbo2->query($sql_tarjeta);
                $row_tarjeta = $dbo2->fetchArray($r_tarjeta);
                $InfoResponse["IDJuegoGolf"] = $Datos["IDGameGolfJuego"];
                $InfoResponse["Fecha"] = substr($Datos["FechaTrCr"], 0, 10);
                $InfoResponse["IDGolfCourse"] = $Datos["IDGolfCourse"];
                $InfoResponse["NombreCourse"] = $Datos["NombreCourse"];
                $InfoResponse["PuntajeGeneral"] = $row_tarjeta["SobrePar"];
                //$datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $Datos["IDJugador"] . "' ", "array");
                //$InfoResponse["NombreJugadorGrupo"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                //Averiguo el nombre del grupo
                $SQLDatosG = "SELECT GS.IDGameGolfJuegoGrupoSocio as IDGrupo, GS.Nombre
                    FROM  GameGolfJuegoGrupoSocioJugadores GJ, GameGolfJuegoGrupoSocio GS
                    WHERE GJ.IDGameGolfJuegoGrupoSocio = GS.IDGameGolfJuegoGrupoSocio  and GJ.IDJugador = '" . $Datos["IDJugador"] . "'  
                    GROUP BY GS.IDGameGolfJuegoGrupoSocio LIMIT 1";
                $QRYDatosG = $dbo2->query($SQLDatosG);
                $DatosG = $dbo2->fetchArray($QRYDatosG);
                $NombreGrupo = $DatosG["Nombre"];
                $InfoResponse["NombreJugadorGrupo"] = $NombreGrupo;
                array_push($response, $InfoResponse);
            endwhile;
        }




        $respuesta[message] = "Datos encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;
        return $respuesta;
    }

    public function enviar_score($IDClub, $IDSocio, $IDUsuario, $IDJuego)
    {

        //DEV

        $EndpointReportarTarjeta = "https://fedegolf--fedegolfpc.sandbox.my.salesforce.com/services/apexrest/RetornaRegistroTarjeta";
        $EndPointToken = 'https://test.salesforce.com/services/oauth2/token';
        define("CLIENT_ID", '3MVG9W4cDaFe_AanHRlLhRgtgORz8yRE4u8RfHAqs4W9wgxhu.hnsEtTCjBQvnjNFnZxFBLSPCHI1mApS6mJt');
        define("CLIENT_SECRET", '583FD1947A7F6A3BAF7E67FABF6114ABAE3E0B5594128BD7F957055115351CAD');
        define("SF_INSTANCE", 'https://test.salesforce.com.my.salesforce.com');
        define("SF_USER", 'apiuser@federacioncolombianadegolf.com.fedegolfpc');
        define("SF_PWD", 'GolfGolf&2022');


        //PROD
        /*
            $EndpointReportarTarjeta="https://www.federacioncolombianadegolf.com/services/apexrest/RetornaRegistroTarjeta";            
            $EndPointToken='https://fedegolf.my.salesforce.com/services/oauth2/token';
            define("CLIENT_ID", '3MVG9LBJLApeX_PCOTjgWti2QybDOhEFdaFA4zICSxSaKX4M6FL1zUJoWp96WoMb4BL1q7bstCWeaQHPwSQ9Y');
            define("CLIENT_SECRET", '884FB3FFF9D738870F1E1726B38BD0BAAF2D392C201ADF55FFF45D4850406ECC');
            define("SF_INSTANCE", 'https://login.salesforce.com.my.salesforce.com');
            define("SF_USER", 'apiuser@federacioncolombianadegolf.com');
            define("SF_PWD", 'Fedegolf,2021.');
            */


        $dbo2 = &SIMDB2::get();
        $dbo = &SIMDB::get();
        $response = array();
        $array_datos_tarjeta = array();
        $Token = SIMWebServiceFedegolf::Token();
        //$Token = SIMWebServiceFedegolf::TokenPruebas();            
        if ($Token != "errortoken" && !empty($Token)) {
            $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuego . "' ", "array");
            $sql_grupo = "SELECT IDJugador,IDGameGolfMarca FROM GameGolfJuegoGrupoJugadores  WHERE IDGameGolfJuego = '" . $IDJuego . "' ";
            $r_grupo = $dbo2->query($sql_grupo);

            while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                $array_hoyo_golpe = array();
                $sql_golpe = "SELECT Hoyo,NumeroGolpes FROM GameGolfGolpeHistorial WHERE IDGameGolfJuego = '" . $IDJuego . "' and IDJugador = '" . $row_grupo["IDJugador"] . "' ";
                $r_golpe = $dbo2->query($sql_golpe);
                while ($row_golpe = $dbo2->fetchArray($r_golpe)) {
                    $array_hoyo_golpe[$row_golpe["Hoyo"]] = $row_golpe["NumeroGolpes"];
                }

                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $row_grupo["IDJugador"] . "' ", "array");
                $CodigoMarca = $dbo2->getFields("GameGolfMarca", "IDExterno", "IDGameGolfMarca = '" . $row_grupo["IDGameGolfMarca"] . "' ");
                $datos_cancha = $dbo2->fetchAll("GameGolfCourse", "IDGameGolfCourse = '" . $datos_juego["IDGolfCourse"] . "' ", "array");
                $CodigoCancha = $datos_cancha["IDExterno"];
                $CodigoClub = $datos_cancha["IDCampoExterno"];
                $CodigoJugador = $datos_socio["NumeroDocumento"];
                //$datos_juego["FechaTrCr"]="2023-03-01 15:00:00";
                $datos_juego["FechaTrCr"] = date("Y-m-d H:i:s");
                $FechaJuego = str_replace(" ", "T", $datos_juego["FechaTrCr"]) . ".000-0500";
                $NumeroHoyos = $datos_juego["NumeroHoyos"];

                $array_req["codigoTarjeta"] = "";
                $array_req["codJugador"] = (string)$CodigoJugador;
                $array_req["FechaDeJuego"] = $FechaJuego;
                $array_req["codmarca"] = $CodigoMarca;
                $array_req["codClub"] = (string)$CodigoClub;
                $array_req["codCancha"] = (string)$CodigoCancha;
                $array_req["numHoyos"] = (string)$NumeroHoyos;
                $array_req["Vuelta"] = "";


                for ($i = 1; $i <= 18; $i++) {
                    $array_req["Hoyo" . $i] = (int)$array_hoyo_golpe[$i];
                }
                for ($i = 1; $i <= 18; $i++) {
                    $array_req["FWHit" . $i] = (int)0;
                }
                for ($i = 1; $i <= 18; $i++) {
                    $array_req["Green" . $i] = (int)0;
                }
                for ($i = 1; $i <= 18; $i++) {
                    $array_req["Putts" . $i] = (int)0;
                }

                $array_req["usuRegistro"] = "";

                $array_datos_tarjeta["req"] = $array_req;
                $json_envia = json_encode($array_datos_tarjeta);




                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $EndPointToken,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_MAXREDIRS => 10, //default is 20
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => http_build_query(array(
                        "grant_type" => 'password',
                        "client_id" => CLIENT_ID,
                        "client_secret" => CLIENT_SECRET,
                        "username" => SF_USER,
                        "password" => SF_PWD,
                        "format" => "json"
                    )),
                    CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
                ));
                $response_j = curl_exec($curl);
                curl_close($curl);
                //var_dump($response_j);
                $datos_respuesta_token = json_decode($response_j);
                $TokenPeticion = $datos_respuesta_token->access_token;


                //echo "TOKEN " . $TokenPeticion;

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $EndpointReportarTarjeta,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $json_envia,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $TokenPeticion,
                        'Content-Type: application/json',
                        'Cookie: BrowserId=jhYdnbeJEe2vKX_9NBQXpg; CookieConsentPolicy=0:1; LSKey-c$CookieConsentPolicy=0:1'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                //echo $response;
                //exit;

                //echo "JSON:" . $json_envia;

                /*
                    EJEMPLO
                    $json_envia='{
                        "req":{
                           "codigoTarjeta":"",
                           "codJugador":"1136880140",
                           "FechaDeJuego":"2021-01-05T16:05:00.000-0500",
                           "codmarca":"647",
                           "codClub":"1",
                           "codCancha":"3",
                           "numHoyos":18,
                           "Vuelta":"",
                           "Hoyo1":4,
                           "Hoyo2":4,
                           "Hoyo3":5,
                           "Hoyo4":4,
                           "Hoyo5":5,
                           "Hoyo6":5,
                           "Hoyo7":3,
                           "Hoyo8":4,
                           "Hoyo9":4,
                           "Hoyo10":6,
                           "Hoyo11":4,
                           "Hoyo12":3,
                           "Hoyo13":4,
                           "Hoyo14":6,
                           "Hoyo15":2,
                           "Hoyo16":4,
                           "Hoyo17":4,
                           "Hoyo18":6,
                           "FWHit1":3,
                           "FWHit2":3,
                           "FWHit3":3,
                           "FWHit4":3,
                           "FWHit5":3,
                           "FWHit6":3,
                           "FWHit7":3,
                           "FWHit8":3,
                           "FWHit9":3,
                           "FWHit10":3,
                           "FWHit11":3,
                           "FWHit12":3,
                           "FWHit13":3,
                           "FWHit14":3,
                           "FWHit15":3,
                           "FWHit16":3,
                           "FWHit17":3,
                           "FWHit18":3,
                           "Green1":1,
                           "Green2":1,
                           "Green3":1,
                           "Green4":1,
                           "Green5":1,
                           "Green6":1,
                           "Green7":1,
                           "Green8":1,
                           "Green9":1,
                           "Green10":1,
                           "Green11":1,
                           "Green12":1,
                           "Green13":1,
                           "Green14":1,
                           "Green15":1,
                           "Green16":1,
                           "Green17":1,
                           "Green18":1,
                           "Putts1":3,
                           "Putts2":3,
                           "Putts3":3,
                           "Putts4":3,
                           "Putts5":3,
                           "Putts6":3,
                           "Putts7":3,
                           "Putts8":3,
                           "Putts9":3,
                           "Putts10":3,
                           "Putts11":3,
                           "Putts12":3,
                           "Putts13":3,
                           "Putts14":3,
                           "Putts15":3,
                           "Putts16":3,
                           "Putts17":3,
                           "Putts18":3,
                           "usuRegistro":""
                        }
                     }';
                     
                    
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://fedegolf--fedegolfsb.sandbox.my.salesforce.com/services/apexrest/RetornaRegistroTarjeta',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>$json_envia,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.$Token,
                        'Content-Type: application/json',
                        'Cookie: BrowserId=jhYdnbeJEe2vKX_9NBQXpg; CookieConsentPolicy=0:1; LSKey-c$CookieConsentPolicy=0:1'
                    ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    //echo $response;
                */

                $datos_respuesta = json_decode($response);

                if (is_array($datos_respuesta->RetornaRegistroTarjetaResult)) {
                    $ScoreTotal = $datos_respuesta->RetornaRegistroTarjetaResult[0]->scoreTotal;
                    $ScoreIngresado = $datos_respuesta->RetornaRegistroTarjetaResult[0]->scoreIngresado;
                    $Indice = $datos_respuesta->RetornaRegistroTarjetaResult[0]->indice;
                    $Diferencial = $datos_respuesta->RetornaRegistroTarjetaResult[0]->diferencial;
                    $Mensaje = $datos_respuesta->RetornaRegistroTarjetaResult[0]->Respuesta->Mensaje;
                } else {
                    $Error = $datos_respuesta[0]->errorCode;
                    $Mensaje = $datos_respuesta[0]->message;
                }

                //Guardo el Log de lo que pasó
                $sql_log = "INSERT INTO GameGolfLog (IDClub,IDSocioInserta,IDJugador,IDGameGolfJuego,Error,Mensaje,ScoreTotal,ScoreIngresado,Indice,Diferencial,CadenaXML,UsuarioTrCr,FechaTrCr) 
                              VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $row_grupo["IDJugador"] . "','" . $IDJuego . "','" . $Error . "','" . $Mensaje . "','" . $ScoreTotal . "','" . $ScoreIngresado . "','" . $Indice . "','" . $Diferencial . "','" . $json_envia . "','APP',NOW())";
                $dbo2->query($sql_log);
            }

            return true;
        } else {
            //Guardo el Log de lo que pasó
            $sql_log = "INSERT INTO GameGolfLog (IDClub,IDSocioInserta,IDGameGolfJuego,Error,Mensaje,UsuarioTrCr,FechaTrCr) 
                 VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $IDJuego . "','No se pudo generar el Token','Error en el token','APP',NOW())";
            $dbo2->query($sql_log);
            return false;
        }
    }

    public function eliminar_juego_golf_formato_configurado($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $IDFormatoJuego, $IDPartida)
    {
        $dbo2 = &SIMDB2::get();
        $response = array();
        if (!empty($IDSocio) && !empty($IDJuegoGolf) && !empty($IDFormatoJuego) && !empty($IDPartida)) {
            $sql_elimina = "DELETE FROM GameGolfJuegoFormato WHERE IDGameGolfJuegoFormato = '" . $IDPartida . "' ";
            $dbo2->query($sql_elimina);

            //Elimino las tarjetas y sus detalles
            $sql_tarjeta = "SELECT IDGameGolfJuegoTarjeta FROM GameGolfJuegoTarjeta WHERE IDGameGolfJuego = '" . $IDJuegoGolf . "' and IDGameGolfJuegoFormato = '" . $IDPartida . "'";
            $r_tarjeta = $dbo2->query($sql_tarjeta);
            while ($row_tarjeta = $dbo2->fetchArray($r_tarjeta)) {
                $sql_elimina = "DELETE FROM GameGolfJuegoTarjeta WHERE IDGameGolfJuegoTarjeta = '" . $row_tarjeta["IDGameGolfJuegoTarjeta"] . "'";
                $dbo2->query($sql_elimina);

                $sql_elimina = "DELETE FROM GameGolfJuegoTarjetaDetalle WHERE IDGameGolfJuegoTarjeta = '" . $row_tarjeta["IDGameGolfJuegoTarjeta"] . "'";
                $dbo2->query($sql_elimina);
            }

            $respuesta[message] = "Formato eliminado correctamente: " . $IDFormatoJuego;
            $respuesta[success] = true;
            $respuesta[response] = "";
        } else {
            $respuesta[message] = "GGCE11. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function editar_juego_golf_formato_configurado($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $FormatoJuego)
    {
        $dbo2 = &SIMDB2::get();
        if (!empty($IDSocio) && !empty($IDJuegoGolf) && !empty($FormatoJuego)) {
            //Creo los formatos
            $FormatosJuegoCreados = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($FormatoJuego));
            $datos_formato = json_decode($FormatosJuegoCreados, false);

            if (count($datos_formato) > 0) {
                if (!empty($datos_formato->IDFormatoJuego) && (!empty($datos_formato->IDJugador1) && !empty($datos_formato->IDJugador2) ||  !empty(($datos_formato->Pareja1->IDJugador1)))) {
                    if (empty($datos_formato->UsarHandicap))
                        $UsarHcp = "S";
                    else
                        $UsarHcp = $datos_formato->UsarHandicap;

                    $sql_inserta_grupo = "UPDATE GameGolfJuegoFormato 
                                            SET  UsarHandicap = '" . $UsarHcp . "', UsarHandicapCampo='" . $datos_formato->UsarHandicapCampo . "', PorcentajeHandicap='" . $datos_formato->PorcentajeHandicap . "', VentajaSeleccionada='" . $datos_formato->VentajaSeleccionada . "', NumeroDeResultados='" . $datos_formato->NumeroDeResultados . "', JuegoPorHoyosMatch='" . $datos_formato->JuegoPorHoyosMatch . "', JuegoPorGolpesMedal='" . $datos_formato->JuegoPorGolpesMedal . "', MenorHandicapBajaCero='" . $datos_formato->MenorHandicapBajaCero . "', UsarMejorBolaDelGrupo = '" . $datos_formato->UsarMejorBolaDelGrupo . "',UsarSumaDelGrupo = '" . $datos_formato->UsarSumaDelGrupo . "', UsuarioTrEd='" . $IDSocio . "', FechaTrEd=NOW() 
                                            WHERE IDGameGolfJuegoFormato = '" . $datos_formato->IDPartida . "'";
                    $dbo2->query($sql_inserta_grupo);
                }
            }

            self::calcular_resultado_tarjeta($IDClub, $datos_formato->IDJugador1, $IDJuegoGolf);
            self::calcular_resultado_tarjeta($IDClub, $datos_formato->IDJugador2, $IDJuegoGolf);

            $respuesta[message] = "Formato actualizado correctamente: ";
            $respuesta[success] = true;
            $respuesta[response] = "";
        } else {
            $respuesta[message] = "GGCA17. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function crear_juego_golf_formato_configurado($IDClub, $IDSocio, $IDUsuario, $IDJuegoGolf, $FormatoJuego)
    {

        $dbo2 = &SIMDB2::get();
        if (!empty($IDSocio) && !empty($IDJuegoGolf) && !empty($FormatoJuego)) {
            //Creo los formatos
            $FormatosJuegoCreados = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($FormatoJuego));
            $datos_formato = json_decode($FormatosJuegoCreados, false);

            if (count($datos_formato) > 0) {
                if (!empty($datos_formato->IDFormatoJuego) && !empty($datos_formato->IDJugador1) && !empty($datos_formato->IDJugador2)) {
                    if (empty($datos_formato->UsarHandicap))
                        $UsarHcp = "S";
                    else
                        $UsarHcp = $datos_formato->UsarHandicap;

                    $sql_inserta_grupo = "INSERT INTO GameGolfJuegoFormato   (IDClub, IDGameGolfJuego,IDGameGolfFormato, IDJugadorPrincipal, IDJugadorJuego, UsarHandicap, UsarHandicapCampo, PorcentajeHandicap, VentajaSeleccionada, NumeroDeResultados, JuegoPorHoyosMatch, JuegoPorGolpesMedal, MenorHandicapBajaCero, UsuarioTrCr, FechaTrCr ) 
                                            VALUES ('" . $IDClub . "','" . $IDJuegoGolf . "','" . $datos_formato->IDFormatoJuego . "','" . $datos_formato->IDJugador1 . "','" . $datos_formato->IDJugador2 . "','" . $UsarHcp . "', '" . $datos_formato->UsarHandicapCampo . "', '" . $datos_formato->PorcentajeHandicap . "', '" . $datos_formato->VentajaSeleccionada . "','" . $datos_formato->NumeroDeResultados . "' , '" . $datos_formato->JuegoPorHoyosMatch . "' ,'" . $datos_formato->JuegoPorGolpesMedal . "','" . $datos_formato->MenorHandicapBajaCero . "',   'APP',NOW())";
                    $dbo2->query($sql_inserta_grupo);
                    self::calcular_resultado_tarjeta($IDClub, $datos_formato->IDJugador1, $IDJuegoGolf);
                    self::calcular_resultado_tarjeta($IDClub, $datos_formato->IDJugador2, $IDJuegoGolf);
                }
            }

            $respuesta[message] = "Formato creado correctamente: ";
            $respuesta[success] = true;
            $respuesta[response] = "";
        } else {
            $respuesta[message] = "GGCE19. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function get_mis_grupos_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGrupo)
    {
        $dbo = &SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response = array();


        //Consulto favoritos
        $array_fav[] = $IDSocio;
        $sql_fav = "SELECT IDSocio,IDSocio2 FROM GameGolfSocioFavorito WHERE IDSocio = '" . $IDSocio . "' ";
        $r_fav = $dbo2->query($sql_fav);
        while ($row_fav = $dbo->fetchArray($r_fav)) {
            $array_fav[] = $row_fav["IDSocio2"];
        }

        if (!empty($IDGrupo)) {
            $condicion_fecha = " and IDGameGolfJuegoGrupoSocio =  '" . $IDGrupo . "' ";
        }

        $SQLDatos = "SELECT GS.IDGameGolfJuegoGrupoSocio as IDGrupo, GS.Nombre
                         FROM  GameGolfJuegoGrupoSocioJugadores GJ, GameGolfJuegoGrupoSocio GS
                         WHERE GJ.IDGameGolfJuegoGrupoSocio = GS.IDGameGolfJuegoGrupoSocio  and GJ.IDJugador = '" . $IDSocio . "'  
                         GROUP BY GS.IDGameGolfJuegoGrupoSocio";
        $QRYDatos = $dbo2->query($SQLDatos);
        while ($Datos = $dbo2->fetchArray($QRYDatos)) :
            $DatosParticipante = array();
            $response_partic = array();
            $InfoResponse["IDGrupo"] = $Datos["IDGrupo"];
            $InfoResponse["Nombre"] = $Datos["Nombre"];

            $sql_partic = "SELECT IDJugador FROM GameGolfJuegoGrupoSocioJugadores WHERE IDGameGolfJuegoGrupoSocio = '" . $Datos["IDGrupo"] . "'";
            $r_partic = $dbo2->query($sql_partic);
            while ($DatosPartic = $dbo2->fetchArray($r_partic)) :
                $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $DatosPartic["IDJugador"] . "' ", "array");
                $DatosParticipante["IDJugador"] = $DatosPartic["IDJugador"];
                $DatosParticipante["Nombre"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
                $DatosParticipante["Handicap"] = $datos_socio["Handicap"];

                if (in_array($datos_socio["IDSocio"], $array_fav)) {
                    $Favorito = "S";
                } else {
                    $Favorito = "N";
                }


                $DatosParticipante["Favorito"] = $Favorito;
                array_push($response_partic, $DatosParticipante);
            endwhile;

            $InfoResponse["Participantes"] = $response_partic;
            array_push($response, $InfoResponse);
        endwhile;

        $respuesta[message] = "Datos encontrados";
        $respuesta[success] = true;
        $respuesta[response] = $response;
        return $respuesta;
    }

    public function crear_mi_grupo_juegos_golf($IDClub, $IDSocio, $IDUsuario, $NombreGrupo, $Participantes)
    {
        $dbo2 = &SIMDB2::get();
        if (!empty($IDSocio) && !empty($NombreGrupo) &&  !empty($Participantes)) {

            $inserta_grupo = "INSERT INTO GameGolfJuegoGrupoSocio (IDClub,IDSocio,Nombre,UsuarioTrCr,FechaTrCr) VALUES ('" . $IDClub . "','" . $IDSocio . "','" . $NombreGrupo . "','" . $IDSocio . "',NOW())";
            $dbo2->query($inserta_grupo);
            $IDGrupo = $dbo2->lastID();

            if ((int)$IDGrupo > 0) {
                //Creo los participantes
                $Participantes = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($Participantes));
                $datos_participante = json_decode($Participantes, false);

                if (count($datos_participante) > 0) {
                    foreach ($datos_participante as $detalle_participante) {
                        if (!empty($detalle_participante->IDJugador)) {
                            $sql_inserta_partic = "INSERT INTO GameGolfJuegoGrupoSocioJugadores (IDClub, IDGameGolfJuegoGrupoSocio,IDJugador, UsuarioTrCr, FechaTrCr) 
                                                    VALUES ('" . $IDClub . "','" . $IDGrupo . "','" . $detalle_participante->IDJugador . "','APP',NOW())";
                            $dbo2->query($sql_inserta_partic);
                        }
                    }
                }
                $respuesta[message] = "Grupo creado correctamente: ";
                $respuesta[success] = true;
                $respuesta[response] = "";
            } else {
                $respuesta[message] = "Ocurrio un problema al crear el grupo, por favor intenete mas tarde";
                $respuesta[success] = false;
                $respuesta[response] = "";
            }
        } else {
            $respuesta[message] = "GGCE71. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function editar_mi_grupo_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGrupo, $NombreGrupo, $Participantes)
    {
        $dbo2 = &SIMDB2::get();
        if (!empty($IDSocio) && !empty($NombreGrupo) &&  !empty($Participantes) && !empty($IDGrupo)) {

            $edita_grupo = "UPDATE GameGolfJuegoGrupoSocio SET IDSocio = '" . $IDSocio . "',Nombre='" . $NombreGrupo . "',UsuarioTrEd = '" . $IDSocio . "',FechaTrEd=NOW() WHERE IDGameGolfJuegoGrupoSocio = '" . $IDGrupo . "'  ";
            $dbo2->query($edita_grupo);

            if ((int)$IDGrupo > 0) {
                $borra_partic = "DELETE FROM GameGolfJuegoGrupoSocioJugadores WHERE IDGameGolfJuegoGrupoSocio = '" . $IDGrupo . "' ";
                $dbo2->query($borra_partic);
                //Creo los participantes
                $Participantes = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($Participantes));
                $datos_participante = json_decode($Participantes, false);

                if (count($datos_participante) > 0) {
                    foreach ($datos_participante as $detalle_participante) {
                        if (!empty($detalle_participante->IDJugador)) {
                            $sql_inserta_partic = "INSERT INTO GameGolfJuegoGrupoSocioJugadores (IDClub, IDGameGolfJuegoGrupoSocio,IDJugador, UsuarioTrCr, FechaTrCr) 
                                                    VALUES ('" . $IDClub . "','" . $IDGrupo . "','" . $detalle_participante->IDJugador . "','APP',NOW())";
                            $dbo2->query($sql_inserta_partic);
                        }
                    }
                }
                $respuesta[message] = "Grupo editado correctamente: ";
                $respuesta[success] = true;
                $respuesta[response] = "";
            } else {
                $respuesta[message] = "Ocurrio un problema al crear el grupo, por favor intenete mas tarde";
                $respuesta[success] = true;
                $respuesta[response] = "";
            }
        } else {
            $respuesta[message] = "GGCE41. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function salir_de_mi_grupo_juego_golf($IDClub, $IDSocio, $IDUsuario, $IDGrupo, $IDJugador)
    {
        $dbo2 = &SIMDB2::get();
        if (!empty($IDSocio) && !empty($IDGrupo) &&  !empty($IDJugador)) {

            $edita_grupo = "DELETE FROM GameGolfJuegoGrupoSocioJugadores WHERE  IDJugador = '" . $IDJugador . "' and IDGameGolfJuegoGrupoSocio ='" . $IDGrupo . "' ";
            $dbo2->query($edita_grupo);

            $respuesta[message] = "Ha salido correctamente del grupo";
            $respuesta[success] = true;
            $respuesta[response] = "";
        } else {
            $respuesta[message] = "GGCE43. Atencion Faltan Parametros!";
            $respuesta[success] = false;
            $respuesta[response] = "";
        }

        return $respuesta;
    }

    public function get_detalle_juego_individual_vs($IDClub, $IDSocio, $IDUsuario, $IDFormatoJuego, $IDJugador, $IDJuegoGolf, $IDPartida, $Vista)
    {

        $dbo = SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response_tabla_puntaje = array();
        $Posicion = 0;
        if (!empty($TipoConsulta))
            $IDFormatoJuego = 1;

        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }

        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
        $datos_formato_juego = $dbo2->fetchAll("GameGolfJuegoFormato", " IDGameGolfJuegoFormato  = '" . $IDPartida . "' ", "array");
        if ((int)$datos_juego["IDGameGolfJuego"] > 0) {

            $InfoResponse["IDPartida"] = $IDPartida;
            $InfoResponse["IDFormatoJuego"] = $IDFormatoJuego;
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_formato_juego["IDJugadorPrincipal"] . "' ", "array");
            $InfoResponse["IDJugadorJuego1"] = $datos_formato_juego["IDJugadorPrincipal"];
            $InfoResponse["NombreJugadorJuego1"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
            $NombreCortoJ1 =  substr($datos_socio["Nombre"], 0, 1) . substr($datos_socio["Apellido"], 0, 1);
            $InfoResponse["ColorJugadorJuego1"] = "#EE271A";
            $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $datos_formato_juego["IDJugadorJuego"] . "' ", "array");
            $InfoResponse["IDJugadorJuego2"] = $datos_formato_juego["IDJugadorJuego"];
            $InfoResponse["NombreJugadorJuego2"] = $datos_socio["Nombre"] . " " . $datos_socio["Apellido"];
            $NombreCortoJ2 =  substr($datos_socio["Nombre"], 0, 1) . substr($datos_socio["Apellido"], 0, 1);
            $InfoResponse["ColorJugadorJuego2"] = "#0628F9";
            $InfoResponse["ColorHeaderTarjeta"] = "#48952A";

            //Busco los grupos del juego para ver el resultado por jugador
            $sql_grupo = "SELECT GGJGJ.IDGameGolfJuegoGrupo, GGJGJ.IDJugador 
                            FROM GameGolfJuegoGrupoJugadores GGJGJ, GameGolfJuegoTarjeta" . $NombreTabla . " GGJT
                            WHERE GGJGJ.IDJugador = GGJT.IDJugadorJuego and GGJGJ.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJT.IDGameGolfJuego = '" . $IDJuegoGolf . "' and GGJT.IDGameGolfFormato = '" . $IDFormatoJuego . "' ORDER BY GGJT.SobrePar ASC ";

            $r_grupo = $dbo2->query($sql_grupo);
            while ($row_grupo = $dbo2->fetchArray($r_grupo)) {
                if ((int)$row_grupo["IDJugador"] > 0) {
                    $Posicion++;
                    $resultado = self::consulta_resultados_vs($IDClub, $IDJuegoGolf, $IDFormatoJuego, $IDPartida, $Vista, $NombreCortoJ1, $NombreCortoJ2, $InfoResponse["IDJugadorJuego1"], $InfoResponse["IDJugadorJuego2"]);
                    if (is_array($resultado))
                        $response_tabla_puntaje = $resultado;
                }
            }


            $InfoResponse["Tarjeta"] = $response_tabla_puntaje;

            $respuesta[message] = "Datos encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $InfoResponse;
        } else {
            $respuesta[message] = "Juego no encontrado";
            $respuesta[success] = false;
            $respuesta[response] = $InfoResponse;
        }

        return $respuesta;
    }

    public function get_detalle_juego_pareja_vs($IDClub, $IDSocio, $IDUsuario, $IDFormatoJuego, $IDJugador, $IDJuegoGolf, $IDPartida, $Vista)
    {

        $dbo = SIMDB::get();
        $dbo2 = &SIMDB2::get();
        $response_tabla_puntaje = array();
        $Posicion = 0;
        if (!empty($TipoConsulta))
            $IDFormatoJuego = 1;

        if ($Vista == "misjuegos") {
            $NombreTabla = "Historial";
        }

        $datos_juego = $dbo2->fetchAll("GameGolfJuego ", " IDGameGolfJuego  = '" . $IDJuegoGolf . "' ", "array");
        $datos_formato_juego = $dbo2->fetchAll("GameGolfJuegoFormato", " IDGameGolfJuegoFormato  = '" . $IDPartida . "' ", "array");
        if ((int)$datos_juego["IDGameGolfJuego"] > 0) {

            $sql_llave = "SELECT IDGameGolfJuegoFormatoLlaves, IDJugador1Pareja1, IDJugador2Pareja1, IDJugador1Pareja2, IDJugador2Pareja2
                    FROM GameGolfJuegoFormatoLlaves 
                    WHERE IDGameGolfFormato='" . $datos_formato_juego["IDGameGolfFormato"] . "' and IDGameGolfJuego = '" . $IDJuegoGolf . "'  ";
            $r_llave = $dbo2->query($sql_llave);
            while ($row_llave = $dbo2->fetchArray($r_llave)) {
                $datos_socio_1 = $dbo->fetchAll("Socio", " IDSocio = '" . $row_llave["IDJugador1Pareja1"] . "' ", "array");
                $datos_socio_2 = $dbo->fetchAll("Socio", " IDSocio = '" . $row_llave["IDJugador2Pareja1"] . "' ", "array");
                $datos_socio_3 = $dbo->fetchAll("Socio", " IDSocio = '" . $row_llave["IDJugador1Pareja2"] . "' ", "array");
                $datos_socio_4 = $dbo->fetchAll("Socio", " IDSocio = '" . $row_llave["IDJugador2Pareja2"] . "' ", "array");
                $IDLlave = $row_llave["IDGameGolfJuegoFormatoLlaves"];
            }

            $Pareja1 = $datos_socio_1["Nombre"] . " " . $datos_socio_1["Apellido"] . "/" . $datos_socio_2["Nombre"] . " " . $datos_socio_2["Apellido"];
            $Pareja2 = $datos_socio_3["Nombre"] . " " . $datos_socio_3["Apellido"] . "/" . $datos_socio_4["Nombre"] . " " . $datos_socio_4["Apellido"];
            $Pareja1NombreCorto = substr($datos_socio_1["Nombre"], 0, 1) . substr($datos_socio_1["Apellido"], 0, 1) . "/" . substr($datos_socio_2["Nombre"], 0, 1) . substr($datos_socio_2["Apellido"], 0, 1);
            $Pareja2NombreCorto = substr($datos_socio_3["Nombre"], 0, 1) . substr($datos_socio_3["Apellido"], 0, 1)  . "/" . substr($datos_socio_4["Nombre"], 0, 1) . substr($datos_socio_4["Apellido"], 0, 1);


            $InfoResponse["IDPartida"] = $IDPartida;
            $InfoResponse["IDFormatoJuego"] = $IDFormatoJuego;
            $InfoResponse["NombrePareja1"] = $Pareja1;
            $InfoResponse["ColorPareja1"] = "#EE271A";
            $InfoResponse["NombrePareja2"] = $Pareja2;
            $InfoResponse["ColorPareja2"] = "#0628F9";
            $InfoResponse["ColorHeaderTarjeta"] = "#48952A";


            $resultado = self::consulta_resultados_pareja_vs($IDClub, $IDSocio, $IDJuegoGolf, $IDFormatoJuego, $IDPartida, $Vista, $Pareja1NombreCorto, $Pareja2NombreCorto, $IDLlave);
            if (is_array($resultado))
                $response_tabla_puntaje = $resultado;




            $InfoResponse["Tarjeta"] = $response_tabla_puntaje;

            $respuesta[message] = "Datos encontrados";
            $respuesta[success] = true;
            $respuesta[response] = $InfoResponse;
        } else {
            $respuesta[message] = "Juego no encontrado";
            $respuesta[success] = false;
            $respuesta[response] = $InfoResponse;
        }

        return $respuesta;
    }

    public function get_configuracion_juegos_de_golf($IDClub = "", $IDUsuario = "", $IDSocio = "")
    {
        $dbo = &SIMDB::get();
        if (!empty($IDClub) && (!empty($IDSocio) || !empty($IDUsuario))) {
            $param = 'IDSocio';
            $value = $IDSocio;
            if (!empty($IDUsuario)) {
                $param = 'IDUsuario';
                $value = $IDUsuario;
            }

            //verifico que el socio exista y pertenezca al club
            // $query = $dbo->query("SELECT * FROM ConfiguracionJuegosdeGolf WHERE $param = '$value' and IDClub = '$IDClub'");
            // $query = $dbo->fetch($query);
            $query = $dbo->fetchAll("ConfiguracionJuegosdeGolf", "IDClub = '$IDClub'");
            $respuesta = array();
            $response = array();
            if (!empty($query)) {
                foreach ($query as $clave => $valor) {
                    if (in_array($clave, array("OcultarTelefono", "OcultarEmail", "OcultarIdentificacion"))) {
                        $respuesta['DialogoNuevoJugador'][$clave] = $valor;
                    } else {
                        $respuesta[$clave] = $valor;
                    }
                }
                unset($respuesta['IDConfiguracionJuegosdeGolf']);
                unset($respuesta['IDUsuario']);
                unset($respuesta['IDSocio']);
                unset($respuesta['UsuarioTrCr']);
                unset($respuesta['FechaTrCr']);
                unset($respuesta['UsuarioTrEd']);
                unset($respuesta['FechaTrEd']);
                array_push($response, $respuesta);


                $respuesta["message"] = null;
                $respuesta["success"] = true;
                $respuesta["response"] = $response[0];
            } else {
                $respuesta["message"] = 'Error al obtejne';
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            }
        } else {
            $respuesta["message"] = "24." . 'Atencion faltan parametros';
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    }
}
