<?php

class SIMWebServiceClub
{
    public function verificar_contenido_modulo($IDModulo, $IDClub)
    {
        $dbo = &SIMDB::get();
        $flag_mostrar = 0;
        //Para Noticias, Eventos y galeria se verifica si hay contenido para mostrarlo en el menu los id son 3,4,5
        switch ($IDModulo):
            case "3": // Noticias
                // verifico que la seccion tenga por lo menos una noticia publicada
                $id_noticia = $dbo->getFields("Noticia", "IDNoticia", "IDClub = '" . $IDClub . "' and Publicar = 'S' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and (DirigidoA = 'S' or DirigidoA = 'T')");
                if (empty($id_noticia)) :
                    $flag_mostrar = 1;
                endif;
                break;
            case "4": // Eventos
                // verifico que la seccion tenga por lo menos una noticia publicada
                $id_evento = $dbo->getFields("Evento", "IDEvento", "IDClub = '" . $IDClub . "' and Publicar = 'S' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and (DirigidoA = 'S' or DirigidoA = 'T')");
                if (empty($id_evento)) :
                    $flag_mostrar = 1;
                endif;
                break;
            case "5": // Galerias
                // verifico que la seccion tenga por lo menos una galeria publicada
                $id_galeria = $dbo->getFields("Galeria", "IDGaleria", "IDClub = '" . $IDClub . "' and Publicar = 'S'");
                if (empty($id_galeria)) :
                    $flag_mostrar = 1;
                endif;
                break;
            default:
                $flag_mostrar = 0;
        endswitch;

        return $flag_mostrar;
    }

    public function get_club($IDClub, $IDSocio = "")
    {

        $dbo = &SIMDB::get();

        /*   if($IDClub==8):
        $fecha_actual = date("Y-m-d");

$datos = "SELECT * FROM SocioTalonera WHERE IDClub = ".$IDClub;
$datos_socio_talonera = $dbo->query($datos);
while ($info_socio_talonera = $dbo->fetchArray($datos_socio_talonera)) {

$info_socio_talonera["FechaVencimiento"];
$info_socio_talonera["CantidadPendiente"];
$Nueva_cantidad= $info_socio_talonera["CantidadTotal"]; 
$IDTalonera=$info_socio_talonera["IDSocioTalonera"]; 

if($info_socio_talonera["FechaVencimiento"] < $fecha_actual):
$vence= date("Y-m-d",strtotime($info_socio_talonera['FechaVencimiento']."+ 1 month"));

$datos = "UPDATE `SocioTalonera` SET FechaVencimiento=$vence , CantidadPendiente=$Nueva_cantidad WHERE IDSocioTalonera = ".$IDTalonera;
$datos_socio = $dbo->query($datos);

endif;

}
 
        endif; */

        //Validacion para hebraica sobre clientes morosos
        if ($IDClub == 225) {


            if ($IDSocio == 669635) {
                /*  $predio = "UPDATE Socio SET Predio='35337' where IDSocio='669635' ";
                      $datos_act = $dbo->query($predio); */
            }
            require LIBDIR  . "SIMWebServiceHebraica.inc.php";
            /* 742536|||742613|||                                                                  
$IDSocio="5533";
$IDClub="8"; */
            $datos_socios = "SELECT * FROM Socio WHERE IDSocio='$IDSocio' ";
            $datos = $dbo->query($datos_socios);
            while ($row = $dbo->fetchArray($datos)) {
                $ID = $row["Predio"];
                $accion_titular = $row["Accion"];
                $accion = $row["AccionPadre"];
            }

            if (!empty($ID)) {
                $credito = 0;
                $debito = 0;
                $resultado2 = SIMWebServiceHebraica::estado_cuenta_cliente($ID);
                foreach ($resultado2[items] as $socio => $row) :
                    $id = $row["id_persona"];
                    $credito += $row["nm_credito_reexpresado"];
                    $debito += $row["nm_debito_reexpresado"];
                endforeach;
                $credito_total = abs($credito);
                if ($credito_total < $debito) :
                    $id = "tiene deuda";
                else :
                    $id = "";
                endif;

                //SI EL ID NO ESTA VACIO, ENTONCES EL SOCIO TIENE DEUDA!
                if (!empty($id) and $accion_titular == $accion) {

                    //RESTRINGIMOS QUE PUEDA RESERVAR EN LA APP
                    //SE AGREAN LOS MIEMBROS FAMILIARES TAMBIEN.

                    $datos_nucleo = "SELECT * FROM Socio WHERE AccionPadre='$accion' ";
                    $datos = $dbo->query($datos_nucleo);
                    $mienbros = "";
                    while ($row = $dbo->fetchArray($datos)) {
                        $mienbros = $row["IDSocio"];

                        $restringir_reserva = "UPDATE Socio SET PermiteReservar='N' where IDSocio='$mienbros'";
                        $update_reserva = $dbo->query($restringir_reserva);
                    }
                    //VERIFICAMOS QUE EL GRUPO ESTE CREADO
                    $grupo = "SELECT count(*) as total FROM GrupoSocio WHERE Nombre='Clientes_Morosos_Hebraica' and IDClub='$IDClub' ";
                    $datos = $dbo->query($grupo);
                    $cantidad = $dbo->fetchArray($datos);
                    if ($cantidad["total"] > 0) {
                        $grupo_info = "SELECT IDSocio FROM GrupoSocio WHERE Nombre='Clientes_Morosos_Hebraica' and IDClub='$IDClub' ";
                        $datos_socios = $dbo->query($grupo_info);
                        $socios = $dbo->fetchArray($datos_socios);
                        $socios["IDSocio"];
                        //ANALIZAMOS LOS SOCIOS DEL GRUPO
                        $array_invitados = explode("|||", $socios["IDSocio"]);
                        if (count($array_invitados) > 0) {
                            foreach ($array_invitados as $id_invitado => $datos_invitado) {
                                $array_socios_tipoarchivo[] = $datos_invitado;
                            }
                        }

                        //SI EL SOCIO ESTA EN EL GRUPO NO SE AGREGA
                        if (in_array($IDSocio, $array_socios_tipoarchivo)) {
                        } else { //SI NO ESTA, SER AGREGA
                            $todos = $socios["IDSocio"];
                            //SE AGREAN LOS MIEMBROS FAMILIARES TAMBIEN.

                            $datos_nucleo = "SELECT * FROM Socio WHERE AccionPadre='$accion' ";
                            $datos = $dbo->query($datos_nucleo);
                            $mienbros = "";
                            while ($row = $dbo->fetchArray($datos)) {
                                $mienbros .= $row["IDSocio"] . "|||";
                            }
                            $gruposocios = $todos . $mienbros;



                            $grupo = "UPDATE GrupoSocio SET IDSocio='$gruposocios' where Nombre='Clientes_Morosos_Hebraica' and IDClub='$IDClub' ";
                            $datos = $dbo->query($grupo);
                        }
                    } else { //SI EL GRUPO NO ESTA CREADO, SE CREA Y SE AGREAN LOS MIEMBROS FAMILIARES TAMBIEN.

                        $datos_nucleo = "SELECT * FROM Socio WHERE AccionPadre='$accion' ";
                        $datos = $dbo->query($datos_nucleo);
                        $mienbros = "";
                        while ($row = $dbo->fetchArray($datos)) {
                            $mienbros .= $row["IDSocio"] . "|||";
                        }

                        $grupo = "INSERT INTO GrupoSocio(IDGrupoSocio, IDClub, IDSocio, Nombre, Descripcion, Publicar, UsuarioTrCr, FechaTrCr, UsuarioTrEd, FechaTrEd) VALUES (NULL,'$IDClub','$mienbros','Clientes_Morosos_Hebraica','Clientes_Morosos_Hebraica','S','Administrador',now(),'Administrador',now())";
                        $datos = $dbo->query($grupo);
                    }
                } else { //AQUI EL SERVICIO NO RETORNA EL ID, ENTONCES EL SOCIO NO TIENE DEUDA YA, PASAMOS ANALIZAR EL GRUPO Y ELIMINAR AL SOCIO.


                    //PERMITIMOS QUE PUEDA RESERVAR EN LA APP Y  LOS MIEMBROS FAMILIARES TAMBIEN.

                    $datos_nucleo = "SELECT * FROM Socio WHERE AccionPadre='$accion' ";
                    $datos = $dbo->query($datos_nucleo);
                    $mienbros = "";
                    while ($row = $dbo->fetchArray($datos)) {
                        $mienbros = $row["IDSocio"];


                        $permitir_reserva = "UPDATE Socio SET PermiteReservar='S' where IDSocio='$mienbros'";
                        $update_reserva = $dbo->query($permitir_reserva);
                    }

                    //BUSCAMOS SI ESTA EN EL GRUPO
                    $grupo_info = "SELECT IDSocio FROM GrupoSocio WHERE Nombre='Clientes_Morosos_Hebraica' and IDClub='$IDClub' ";
                    $datos_socios = $dbo->query($grupo_info);
                    $socios = $dbo->fetchArray($datos_socios);
                    $todos = $socios["IDSocio"];

                    // SI ESTA, ENTONCES SE ELIMINA Y LOS MIEMBROS FAMILIARES TAMBIEN.

                    $datos_nucleo = "SELECT * FROM Socio WHERE AccionPadre='$accion' ";
                    $datos = $dbo->query($datos_nucleo);
                    $mienbros = "";
                    while ($row = $dbo->fetchArray($datos)) {
                        $mienbros .= $row["IDSocio"] . "|||";
                    }
                    $array = str_replace("$mienbros", "|||", "$todos");

                    $grupo = "UPDATE GrupoSocio SET IDSocio='$array' where Nombre='Clientes_Morosos_Hebraica' and IDClub='$IDClub' ";
                    $datos = $dbo->query($grupo);
                }
            } else {
            }
        }


        $datos_socio = $dbo->fetchAll("Socio", " IDSocio = '" . $IDSocio . "' ", "array");
        $otra_config_club = $dbo->fetchAll("ConfiguracionClub", " IDClub  = '" . $IDClub . "' ", "array");

        //get splash para determinados usuarios 
        $sql = "SELECT * FROM ConfiguracionClub WHERE MostrarSplashHome = 'S' and IDClub = '" . $IDClub . "' ";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";

            while ($r = $dbo->fetchArray($qry)) {

                if (!empty($IDSocio)) {
                    $mostrar_popup = SIMWebServiceApp::verifica_ver_popup($r, $IDSocio);
                }

                if ($mostrar_popup == 1) {

                    $popup["IDClub"] = $r["IDClub"];
                    $popup["IDConfiguracionClub"] = $r["IDConfiguracionClub"];
                    $popup["IDModuloLinkSplashHome"] = $r["IDModuloLinkSplashHome"];
                    $popup["MostrarSplashHome"] = $r["MostrarSplashHome"];
                    $popup["DirigidoAGeneral"] = $r["DirigidoAGeneral"];
                    $popup["LabelBotonSplashHome"] = $r["LabelBotonSplashHome"];
                    $popup["ImagenSplashHome"] = $r["ImagenSplashHome"];
                    $popup["LinkSplashHome"] = $r["LinkSplashHome"];


                    if (!empty($r["ImagenSplashHome"])) :
                        $foto = $r["ImagenSplashHome"];


                    endif;


                    $popup["ImagenSplashHome"] = $foto;
                }
            }
        }
        $config_cumple = $dbo->fetchAll("CumpleAnnosApp", " IDClub  = '" . $IDClub . "' and (DirigidoA = 'S' or  DirigidoA = 'T') ORDER BY IDCumpleAnnosApp DESC LIMIT 1 ", "array");
        $PermiteReservar = $datos_socio["PermiteReservar"];

        // ACTULIZAMOS EL ULTIMO INGRESO EN EL APP
        $FechaIngreso = date("Y-m-d H:i:s");
        $UPDATE = $dbo->query("UPDATE Socio SET FechaUltimoIngresoApp = '$FechaIngreso' WHERE IDSocio = $IDSocio");

        $response = array();
        $sql = "SELECT * FROM Club WHERE IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " Encontrados";
            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($r["FotoLogoApp"])) {
                    $foto_logo = CLUB_ROOT . $r["FotoLogoApp"];
                }
                if (!empty($r["FotoDiseno1"])) {
                    $foto1 = CLUB_ROOT . $r["FotoDiseno1"];
                }
                if (!empty($r["FotoDiseno2"])) {
                    $foto2 = CLUB_ROOT . $r["FotoDiseno2"];
                }

                //Banners
                $response_banner = array();
                $sql_banner = "SELECT * FROM BannerApp WHERE (DirigidoA <> 'E' or DirigidoA='T')  and Publicar = 'S' and IDClub = '" . $IDClub . "' ORDER BY IDBannerApp";
                $qry_banner = $dbo->query($sql_banner);
                if ($dbo->rows($qry_banner) > 0) {
                    while ($r_banner = $dbo->fetchArray($qry_banner)) {
                        $banner["IDClub"] = $IDClub;
                        $banner["IDBannerApp"] = $r_banner["IDBannerApp"];
                        $banner["UrlLink"] = $r_banner["UrlLink"];
                        $MostrarBotonSaltarSplash = $r_banner["MostrarBotonSaltarSplash"];
                        $LabelBotonSaltarSplash = $r_banner["LabelBotonSaltarSplash"];
                        if (!empty($r_banner["UrlVideo"])) {
                            $banner["Tipo"] = "Video";
                            $banner["UrlVideo"] = $r_banner["UrlVideo"];
                        } else {
                            $banner["Tipo"] = "Imagen";
                            if (!empty($r_banner["Foto1"])) :
                                $foto = BANNERAPP_ROOT . $r_banner["Foto1"];
                            else :
                                $foto = "";
                            endif;
                            $banner["Foto"] = $foto;
                        }

                        array_push($response_banner, $banner);
                    } //ednw hile
                }

                // Consulto los tipos de servicio Iniciales
                $sql_servicio_inicial = "SELECT * FROM ServicioInicial Where Publicar='S'";
                $qry_servicio_inicial = $dbo->query($sql_servicio_inicial);
                while ($r_servicio_inicial = $dbo->fetchArray($qry_servicio_inicial)) {
                    $array_servicio_inicial[$r_servicio_inicial["IDServicioInicial"]] = $r_servicio_inicial["Nombre"];
                }

                //Servicios Reservas
                $response_servicio = array();
                $sql_servicio = "SELECT * FROM ServicioClub WHERE IDClub = '" . $IDClub . "' and Activo ='S' ORDER BY IDServicioMaestro";
                //$sql_servicio = "SELECT * FROM Servicio WHERE IDClub = '".$IDClub."' ORDER BY IDServicio";
                $qry_servicio = $dbo->query($sql_servicio);
                if ($dbo->rows($qry_servicio) > 0) {
                    while ($r_servicio = $dbo->fetchArray($qry_servicio)) {

                        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicioMaestro = '" . $r_servicio["IDServicioMaestro"] . "' and IDClub='" . $IDClub . "' ", "array");

                        $servicio["IDClub"] = $IDClub;
                        $servicio["IDServicio"] = $datos_servicio["IDServicio"];
                        $servicio["NombreServicio"] = $datos_servicio["Nombre"];
                        if (!empty($datos_servicio["Icono"])) :
                            $foto = SERVICIO_ROOT . $datos_servicio["Icono"];
                        else :
                            $foto = "";
                        endif;
                        $servicio["Icono"] = $foto;
                        $IDInicial = $dbo->getFields("ServicioMaestro", "IDServicioInicial", "IDServicioMaestro = '" . $r_servicio["IDServicioMaestro"] . "'");
                        //$servicio[ "ServicioInicial" ] = $dbo->getFields( "ServicioInicial", "Nombre", "IDServicioInicial = '" . $IDInicial . "'" );
                        $servicio["ServicioInicial"] = $array_servicio_inicial[$IDInicial];
                        $servicio["PermiteEditarReserva"] = $datos_servicio["PermiteEditarReserva"];

                        array_push($response_servicio, $servicio);
                    } //ednw while
                }

                //Reviso si el socio tiene en numero de invitaciones mayor a cero para hacer invitaciones para mostrar o no el modulo
                if (!empty($IDSocio)) :
                    $NumeroInvitados = $datos_socio["NumeroInvitados"];
                    $NumeroAccesos = $datos_socio["NumeroAccesos"];
                    if ((int) $NumeroInvitados <= 0 && (int) $NumeroAccesos <= 0) :
                        $condicion_modulo = " and IDModulo not in ( 1, 25, 28, 26 ) ";
                    endif;

                endif;

                //Modulos Sistema Menu Central

                $CerrarSesion = $datos_socio["SolicitarCierreSesion"];
                if ($CerrarSesion == "S" || $datos_socio["IDEstadoSocio"] == 2 || $datos_socio["IDEstadoSocio"] == 3) {
                    $condicion_modulo = " and IDModulo = 14 "; //cerrar sesion
                }

                //Consulto los modulos que se configuraron para un socio especifico
                $sql_tiposoc_mod = "SELECT IDModulo,InvitadoSeleccion From PermisoSocioModulo Where IDClub = '" . $IDClub . "' and InvitadoSeleccion <> '' ";
                $r_tiposoc_mod = $dbo->query($sql_tiposoc_mod);
                while ($row_tiposoc_mod = $dbo->fetchArray($r_tiposoc_mod)) {
                    $array_invitados = explode("|||", $row_tiposoc_mod["InvitadoSeleccion"]);
                    $array_soc_id = array();
                    foreach ($array_invitados as $id_invitado => $datos_invitado) {
                        if (!empty($datos_invitado)) {
                            $array_datos_invitados = explode("-", $datos_invitado);
                            $IDSocioInvitacion = $array_datos_invitados[1];
                            if ($IDSocioInvitacion > 0) :
                                $array_soc_id[] = $IDSocioInvitacion;
                            endif;
                        }
                    }

                    $array_mod_esp = explode("|", $row_tiposoc_mod["IDModulo"]);
                    foreach ($array_mod_esp as $IDModulo) {
                        if (!empty($IDModulo)) {
                            $array_id_mod_esp[$IDModulo] = $array_soc_id;
                        }
                    }
                }
                //FIN Consulto los modulos que se configuraron para un socio especifico

                //reviso si tienen modulo personalizado por perfil de socio
                $TipoSocio = $dbo->getFields("Socio", "TipoSocio", "IDSocio = '" . $IDSocio . "'");
                $sql_modulo_perfil = "Select * From TipoSocioModulo Where IDClub = '" . $IDClub . "' and TipoSocio = '" . $TipoSocio . "' Limit 1";
                $r_modulo_perfil = $dbo->query($sql_modulo_perfil);
                if ($dbo->rows($r_modulo_perfil) > 0) {
                    $row_modulo_perfil = $dbo->fetchArray($r_modulo_perfil);
                    if (!empty($row_modulo_perfil["IDModulo"])) {
                        $array_id_modulo = explode("|", $row_modulo_perfil["IDModulo"]);
                        if (count($array_id_modulo) > 0) {
                            $id_modulo_ver = implode(",", $array_id_modulo);
                            if ($row_modulo_perfil[Ocultar] == 1) :
                                $condicion_modulo .= " and IDModulo not in (" . $id_modulo_ver . ")";
                            else :
                                $condicion_modulo .= " and IDModulo in (" . $id_modulo_ver . ")";
                            endif;
                        }
                    }
                }

                $response_modulo = array();
                $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and Activo = 'S' and Ubicacion like '%Central%' " . $condicion_modulo . " ORDER BY Orden";
                $qry_modulo = $dbo->query($sql_modulo);
                if ($dbo->rows($qry_modulo) > 0) {

                    while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                        // Verificar si el modulo tiene contenido para mostrar
                        $flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $IDClub);

                        //Para mostrar el modulo solo a unos socios
                        if ($r_modulo["IDModulo"] == "2" && $PermiteReservar == "N" && ($IDClub != 20 && $IDClub != 48)) :
                            $flag_mostrar = 1;
                        endif;

                        // Verificar si es un modulo que se debe revisar permiso
                        if (array_key_exists($r_modulo["IDModulo"], $array_id_mod_esp)) {
                            if (in_array($IDSocio, $array_id_mod_esp[$r_modulo["IDModulo"]])) {
                                $flag_mostrar = 0;
                            } else {
                                $flag_mostrar = 1;
                            }
                        } else {
                            $flag_mostrar = 0;
                        }

                        $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $r_modulo["IDModulo"] . "' ", "array");

                        //$flag_mostrar=0;
                        if ($flag_mostrar == 0) :
                            $modulo["IDClub"] = $IDClub;
                            $modulo["IDModulo"] = $r_modulo["IDModulo"];
                            if (!empty($r_modulo["Titulo"])) {
                                $modulo["NombreModulo"] = trim($r_modulo["Titulo"]);
                            } else {
                                $modulo["NombreModulo"] = $datos_modulo["Nombre"];
                            }

                            $modulo["Tipo"] = $datos_modulo["Tipo"];

                            $modulo["Orden"] = $r_modulo["Orden"];
                            $icono_modulo = $r_modulo["Icono"];
                            if (!empty($r_modulo["Icono"])) :
                                $foto = MODULO_ROOT . $r_modulo["Icono"];
                            else :
                                $foto = "";
                            endif;
                            $modulo["Icono"] = $foto;
                            $modulo["MostrarBadgeNotificaciones"] = $datos_modulo["MostrarBadgeNotificaciones"];

                            //Consulto si tiene submodulos
                            $sql_sub = "Select * From SubModulo Where IDModulo = '" . $r_modulo["IDModulo"] . "' and IDClub = '" . $IDClub . "'";
                            $result_sub = $dbo->query($sql_sub);
                            if ($dbo->rows($result_sub) > 0) :
                                $modulo["SubModulos"] = "S";
                            else :
                                $modulo["SubModulos"] = "";
                            endif;
                            //Fin Submodulos

                            array_push($response_modulo, $modulo);
                        endif;
                    } //ednw while
                }

                //Modulos Sistema Menu Lateral

                unset($modulo);
                $response_modulo_lateral = array();
                $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and Activo = 'S' and Ubicacion like '%Lateral%' " . $condicion_modulo . " ORDER BY Orden";
                $qry_modulo = $dbo->query($sql_modulo);
                if ($dbo->rows($qry_modulo) > 0) {

                    while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                        // Verificar si el modulo tiene contenido para mostrar
                        $flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $IDClub);

                        if ($r_modulo["IDModulo"] == "2" && $PermiteReservar == "N" && ($IDClub != 20 && $IDClub != 48)) :
                            $flag_mostrar = 1;
                        endif;

                        // Verificar si es un modulo que se debe revisar permiso
                        if (array_key_exists($r_modulo["IDModulo"], $array_id_mod_esp)) {
                            if (in_array($IDSocio, $array_id_mod_esp[$r_modulo["IDModulo"]])) {
                                $flag_mostrar = 0;
                            } else {
                                $flag_mostrar = 1;
                            }
                        } else {
                            $flag_mostrar = 0;
                        }

                        $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $r_modulo["IDModulo"] . "' ", "array");
                        //$flag_mostrar=0;
                        if ($flag_mostrar == 0) :
                            $modulo["IDClub"] = $IDClub;
                            $modulo["IDModulo"] = $r_modulo["IDModulo"];
                            //$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));
                            if (!empty($r_modulo["Titulo"])) {
                                $modulo["NombreModulo"] = trim($r_modulo["TituloLateral"]);
                            } else {
                                $modulo["NombreModulo"] = $datos_modulo["Nombre"];
                            }

                            $modulo["Orden"] = $r_modulo["Orden"];
                            $modulo["Tipo"] = $datos_modulo["Tipo"];

                            $icono_modulo_lateral = $r_modulo["IconoLateral"];
                            if (!empty($r_modulo["IconoLateral"])) :
                                $foto = MODULO_ROOT . $r_modulo["IconoLateral"];
                            else :
                                if (!empty($r_modulo["Icono"])) :
                                    $foto = MODULO_ROOT . $r_modulo["Icono"];
                                else :
                                    $foto = "";
                                endif;
                            endif;
                            $modulo["Icono"] = $foto;
                            $modulo["MostrarBadgeNotificaciones"] = $datos_modulo["MostrarBadgeNotificaciones"];

                            //Consulto si tiene submodulos
                            $sql_sub = "Select * From SubModulo Where IDModulo = '" . $r_modulo["IDModulo"] . "' and IDClub = '" . $IDClub . "'";
                            $result_sub = $dbo->query($sql_sub);
                            if ($dbo->rows($result_sub) > 0) :
                                $modulo["SubModulos"] = "S";
                            else :
                                $modulo["SubModulos"] = "N";
                            endif;
                            //Fin Submodulos
                            array_push($response_modulo_lateral, $modulo);
                        endif;
                    } //ednw while
                }

                // Georeferenciacion
                //Tomo los valores de accesos que debe apolicar para invitados
                $IDParametroAcceso = $dbo->getFields("ParametroAcceso", "IDParametroAcceso", "IDClub = '" . $IDClub . "'");
                $datos_ParametroAcceso = $dbo->fetchAll("ParametroAcceso", " IDParametroAcceso = '" . $IDParametroAcceso . "' ", "array");
                $response_georef = array();
                $georef["Georeferenciacion"] = $datos_ParametroAcceso["Georeferenciacion"];
                $georef["Latitud"] = $datos_ParametroAcceso["Latitud"];
                $georef["Longitud"] = $datos_ParametroAcceso["Longitud"];
                $georef["Rango"] = $datos_ParametroAcceso["Rango"];
                $georef["MensajeFueraRango"] = $datos_ParametroAcceso["MensajeFueraRango"];
                $georef["Georeferenciacion"] = $datos_ParametroAcceso["Georeferenciacion"];
                //array_push($response_georef, $georef);

                // Parametros campos modulo invitados
                $georef["TextoMisInvitado"] = $datos_ParametroAcceso["TextoMisInvitados"];
                $georef["TextoInvitadoAnterior"] = $datos_ParametroAcceso["TextoInvitadoAnterior"];
                $georef["HintDocumentoInvitado"] = $datos_ParametroAcceso["LabelNumeroIdentificacion"];
                $georef["HintNombreInvitado"] = $datos_ParametroAcceso["LabelNombreInvitado"];
                $georef["HintFechaInvitado"] = $datos_ParametroAcceso["HintFechaInvitado"];
                $georef["TextoBotonAgregar"] = $datos_ParametroAcceso["TextoBotonAgregar"];
                // Fin Parametros campos modulo invitados

                $datos_club["IDClub"] = $r["IDClub"];
                $datos_club["Nombre"] = $r["Nombre"];
                $datos_club["Direccion"] = $r["Direccion"];
                $datos_club["Telefono"] = $r["Telefono"];
                $datos_club["Email"] = $r["Email"];
                $datos_club["IDDiseno"] = $r["IDDiseno"];
                $datos_club["Foto"] = $foto_logo;
                $datos_club["Foto1"] = $foto1;
                $datos_club["Foto2"] = $foto2;
                $datos_club["Color1"] = $r["Color1"];
                $datos_club["Color2"] = $r["Color2"];
                $datos_club["ColorFondoCarne"] = $r["ColorFondoCarne"];
                $datos_club["Banner"] = $response_banner;
                if ($IDClub == 227) {
                    require LIBDIR  . "SIMWebServiceCountryMedellin.inc.php";
                    $resultado = SIMWebServiceCountryMedellin::App_ConsultarTerminosCondiciones($IDClub);

                    $tyc =  str_replace("•", "<br> •", "$resultado");



                    $datos_club["Terminos"] = ($tyc);
                } else {
                    $datos_club["Terminos"] = $r["Terminos"];
                }

                $datos_club["SolicitaCambioCorreo"] = $r["SolicitaCambioCorreo"];
                $datos_club["IDSocio"] = $IDSocio;
                $datos_club["PermiteReservar"] = $PermiteReservar;
                $datos_club["MostrarBotonSaltarSplash"] = $MostrarBotonSaltarSplash;
                $datos_club["LabelBotonSaltarSplash"] = $LabelBotonSaltarSplash;

                //datos IOS Version
                $datos_club["iosVersion"] = $r["Version"];
                $datos_club["iosEsencial"] = $r["Esencial"];
                $datos_club["iosversionMessage"] = $r["VersionMessage"];
                $datos_club["versionURLiOS"] = $r["VersionURLIOS"];

                //datos Android Version
                $datos_club["androidVersion"] = $r["VersionAndroid"];
                $datos_club["androidEsencial"] = $r["EsencialAndroid"];
                $datos_club["androidversionMessage"] = $r["VersionMessageAndroid"];
                $datos_club["versionURLAndroid"] = $r["VersionURLAndroid"];

                /*
                        $datos_club["Version"] = $r["Version"];
                        $datos_club["Esencial"] = $r["Esencial"];
                        $datos_club["versionMessage"] = $r["VersionMessage"];
                        $datos_club["versionURLiOS"] = $r["VersionURLIOS"];
                        $datos_club["versionURLAndroid"] = $r["VersionURLAndroid"];
                            */

                $datos_club["ServiciosReserva"] = $response_servicio;
                $datos_club["Modulos"] = $response_modulo;
                $datos_club["ModulosLateral"] = $response_modulo_lateral;
                $datos_club["Georeferenciacion"] = $georef;

                //Si es club es numero derecho si es Residencial o colegio Numero Doc
                if (!empty($r["LabelIdentificadorUsuario"])) :
                    $IdentificadorUsuario = $r["LabelIdentificadorUsuario"];
                else :
                    $IdentificadorUsuario = "Numero de derecho";
                endif;
                $datos_club["IdentificadorUsuario"] = $IdentificadorUsuario . " ";

                $datos_club["LabelUsuario"] = $r["LabelUsuario"];
                $datos_club["LabelClave"] = $r["LabelClave"];
                $datos_club["LabelDigiteUsuario"] = $r["LabelDigiteUsuario"];
                $datos_club["LabelDigiteClave"] = $r["LabelDigiteClave"];
                $datos_club["LabelOlvidoUsuario"] = $r["LabelOlvidoUsuario"];
                $datos_club["LabelFotoError"] = $r["LabelFotoError"];
                $datos_club["LabelCodigoHandicap"] = $r["LabelCodigoHandicap"];
                $datos_club["LabelNombreHandicap"] = $r["LabelNombreHandicap"];
                $datos_club["AyudaHandicap"] = $r["AyudaHandicap"];
                $datos_club["LabelCodigoHandicapTexto"] = $r["LabelCodigoHandicapTexto"];
                $datos_club["InstruccionQr"] = $r["InstruccionQr"];
                $datos_club["PorcentajeQrAndroid"] = $r["PorcentajeQrAndroid"];
                $datos_club["PorcentajeQrIOS"] = $r["PorcentajeQrIOS"];
                $datos_club["MensajeListaEsperaReservas"] = $r["MensajeListaEsperaReservas"];
                $datos_club["MensajeListaEsperaHotel"] = $r["MensajeListaEsperaHotel"];
                $datos_club["MensajeAceptacionListaEsperaReservas"] = $r["MensajeAceptacionListaEsperaReservas"];
                $datos_club["MensajeAceptacionListaEsperaHotel"] = $r["MensajeAceptacionListaEsperaHotel"];
                $datos_club["PermiteListaEsperaHotel"] = $r["PermiteListaEsperaHotel"];
                $datos_club["PermiteSMS"] = $r["PermiteSMS"];
                $datos_club["PermiteOtroValorHotel"] = $r["PermiteOtroValorHotel"];
                $datos_club["PermiteMisEventos"] = $r["PermiteMisEventos"];
                $datos_club["CampoObservacionContratista"] = $r["CampoObservacionContratista"];
                $datos_club["ColorFondoEvento"] = $r["ColorFondoEvento"];
                $datos_club["EventoLista"] = $r["EventoLista"];
                $datos_club["BuscadorFechaEvento"] = $r["BuscadorFechaEvento"];
                $datos_club["LabelTerminos"] = $r["LabelTerminos"];
                $datos_club["TipoTerminos"] = $r["TipoTerminos"];
                $datos_club["ArchivoTerminos"] = CLUB_ROOT . $r["ArchivoTerminos"];
                $datos_club["LabelInvitacion"] = $otra_config_club["LabelInvitacion"];
                $datos_club["CheckSeguridadSocial"] = $r["CheckSeguridadSocial"];
                $datos_club["LabelSeguridadSocial"] = $r["LabelSeguridadSocial"];
                $datos_club["Presalida"] = $r["Presalida"];
                $datos_club["TiempoSplash"] = $r["TiempoSplash"];
                $datos_club["TipoCeldaEvento"] = $r["TipoCeldaEvento"];
                $datos_club["TipoFiltroEvento"] = $r["TipoFiltroEvento"];
                $datos_club["LabelCambioSegundaClave"] = $r["LabelCambioSegundaClave"];
                $datos_club["LabelPlaca"] = (empty($r["LabelPlaca"])) ? 'Placa' : $r["LabelPlaca"];
                $datos_club["LabelPresalida"] = (empty($r["LabelPresalida"])) ? 'Numero Documento' : $r["LabelPresalida"];
                $datos_club["LabelFelicitacion"] = utf8_encode($r["LabelFelicitacion"]);
                $datos_club["LabelCalificacion"] = $r["LabelCalificacion"];
                $datos_club["LabelComentarioFelicitacion"] = $r["LabelComentarioFelicitacion"];
                $datos_club["ObligatorioComentarioCalificar"] = $r["ObligatorioComentarioCalificar"];
                $datos_club["ObligatorioComentarioCalificar"] = $r["ObligatorioComentarioCalificar"];
                $datos_club["LabelBotonEventos"] = $r["LabelBotonEventos"];
                $datos_club["LabelVerMisReservas"] = $r["LabelVerMisReservas"];
                $datos_club["LabelAcercaDe"] = $r["LabelAcercaDe"];
                $datos_club["SolicitaEditarPerfil"] = $datos_socio["SolicitaEditarPerfil"];
                $datos_club["FechaInicioEditarPerfil"] = $r["FechaInicioEditarPerfil"];
                $datos_club["SolicitaEditarPefilLabel"] = $r["SolicitaEditarPefilLabel"];
                $datos_club["OcultarOlvideMiUsuario"] = $r["OcultarOlvideMiUsuario"];
                $datos_club["TipoImagenBanner"] = $r["TipoImagenBanner"];
                $datos_club["LabelNombreHandicapTexto"] = $r["LabelNombreHandicapTexto"];
                $datos_club["PermiteRegistroUsuario"] = $otra_config_club["PermiteRegistroUsuario"];
                $datos_club["PermiteEliminarCuenta"] = $otra_config_club["PermiteRegistroUsuario"];
                $datos_club["UrlRegistroUsuario"] = $otra_config_club["UrlRegistroUsuario"];
                $datos_club["LabelRegistroUsuario"] = $otra_config_club["LabelRegistroUsuario"];
                $datos_club["MostrarBotomMisReservasHome"] = $otra_config_club["MostrarBotomMisReservasHome"];
                $datos_club["TipoDisenoHome"] = $otra_config_club["TipoDisenoHome"];
                $datos_club["SolicitarCambioFotoPerfil"] = $datos_socio["SolicitarCambioFotoPerfil"];
                $datos_club["SolicitarCambioFotoPerfilLabel"] = $otra_config_club["SolicitarCambioFotoPerfilLabel"];

                $datos_club["MostrarInicioPublico"] = $otra_config_club["MostrarInicioPublico"];

                $datos_club["FormatoFechaHora"] = $otra_config_club["FormatoFechaHora"];
                $datos_club["FormatoFecha"] = $otra_config_club["FormatoFecha"];
                $datos_club["FormatoHora"] = $otra_config_club["FormatoHora"];

                $datos_club["MostrarBotonOjoContrasenaLogin"] = $otra_config_club["MostrarBotonOjoContrasenaLogin"];
                $datos_club["SolicitaCambioContrasenaDocumento"] = $otra_config_club["SolicitaCambioContrasenaDocumento"];

                $datos_club["BotonCargarPasaporte"] = $otra_config_club["BotonCargarPasaporte"];
                $datos_club["BotonCargarDiploma"] = $otra_config_club["BotonCargarDiploma"];
                $datos_club["PasaporteCarneLabel"] = $otra_config_club["PasaporteCarneLabel"];
                $datos_club["DiplomaCarneLabel"] = $otra_config_club["DiplomaCarneLabel"];



                //DATOS DEL POPUP
                $datos_club["MostrarSplashHome"] = $popup["MostrarSplashHome"];
                $datos_club["LabelBotonSplashHome"] = $popup["LabelBotonSplashHome"];
                $datos_club["ImagenSplashHome"] = CLUB_ROOT . $popup["ImagenSplashHome"];
                $datos_club["LinkSplashHome"] = $popup["LinkSplashHome"];
                $datos_club["IDModuloLinkSplashHome"] = $popup["IDModuloLinkSplashHome"] == 0 ? "" : $popup["IDModuloLinkSplashHome"];
                //FIN DATOS

                $datos_club["LabelBotonCumpleanos"] =  $config_cumple["LabelBotonCumpleAnos"];


                //configuracion especial para el campestre de mostrar las imagenes segun la edad
                if ($IDClub == 8 || $IDClub == 15) {
                    $FechaNacimientoSocio = $datos_socio["FechaNacimiento"];
                    $anno_fecha_socio = date("Y", strtotime($FechaNacimientoSocio));
                    $annoActual = date("Y");

                    $edad = (int) ($annoActual - $anno_fecha_socio);
                    if ($edad <= 12) {
                        $datos_club["ImagenCumpleanos"] = BANNERAPP_ROOT . $config_cumple["ImagenCumpleanosMenores"];
                    } else if ($edad > 12) {
                        $datos_club["ImagenCumpleanos"] = BANNERAPP_ROOT . $config_cumple["ImagenCumpleanosMayores"];
                    }
                } else {
                    $datos_club["ImagenCumpleanos"] = BANNERAPP_ROOT . $config_cumple["ImagenCumpleanos"];
                }


                $MostrarCumple = "N";
                if ($datos_socio["FechaNacimiento"] != "" && $datos_socio["FechaNacimiento"] != "0000-00-00" && $config_cumple["Publicar"] == "S") {
                    $FechaNac = $datos_socio["FechaNacimiento"];
                    $MesHoy = date("m");
                    $DiaHoy = date("d");
                    $mes_fecha = date("m", strtotime($FechaNac));
                    $dia_fecha = date("d", strtotime($FechaNac));
                    if ($mes_fecha == $MesHoy && $dia_fecha == $DiaHoy) {
                        $MostrarCumple = "S";
                    } else {
                        $MostrarCumple = "N";
                    }
                }

                //if ($IDClub == 44)
                // $MostrarCumple = "S";

                $datos_club["MostrarMensajeCumpleanos"] = $MostrarCumple;

                $SegundaClave = $dbo->getFields("Socio", "SegundaClave", "IDSocio = '" . $IDSocio . "'");
                if (empty($SegundaClave) && $r["ManejoSegundaClave"] == "S") {
                    $crearsegundaclave = "S";
                } else {
                    $crearsegundaclave = "N";
                }

                $datos_club["CrearSegundaClave"] = $crearsegundaclave;

                $datos_club["IDClubPadre"] = $r["IDClubPadre"];

                //Servicios Reservas
                $response_club_hijo = array();
                $sql_club_hijo = "SELECT * FROM Club WHERE IDClubPadre = '" . $IDClub . "' ORDER BY Nombre";
                $qry_club_hijo = $dbo->query($sql_club_hijo);
                if ($dbo->rows($qry_club_hijo) > 0) {
                    while ($r_club_hijo = $dbo->fetchArray($qry_club_hijo)) {
                        $club_hijo["IDClub"] = $r_club_hijo["IDClub"];
                        $club_hijo["Nombre"] = $r_club_hijo["Nombre"];

                        array_push($response_club_hijo, $club_hijo);
                    } //ednw while
                }
                $datos_club["IDClubHijos"] = $response_club_hijo;

                //Encuestas al abrir app
                $encuesta_activa = 0;
                $response_encuesta = array();
                $sql_encuesta = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'S' or DirigidoA = 'T')  ORDER BY Orden ";
                $qry_encuesta = $dbo->query($sql_encuesta);
                if ($dbo->rows($qry_encuesta) > 0) {
                    while ($r_encuesta = $dbo->fetchArray($qry_encuesta)) {
                        $mostrar_encuesta = 0;
                        //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                        if ($r_encuesta["UnaporSocio"] == "S") {
                            $sql_resp = "Select IDEncuesta From EncuestaRespuesta Where IDSocio = '" . $IDSocio . "' and IDEncuesta = '" . $r_encuesta["IDEncuesta"] . "' Limit 1";
                            $r_resp = $dbo->query($sql_resp);
                            if ($dbo->rows($r_resp) <= 0) {
                                $mostrar_encuesta = 1;
                            }
                        } else {
                            $mostrar_encuesta = 1;
                        }
                        //Verifico si la encuesta es solo para algunos socios para mostrar o no
                        $permiso_encuesta = SIMWebServiceApp::verifica_ver_encuesta($r_encuesta, $IDSocio);
                        //$permiso_encuesta=1;

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
                $datos_club["Encuesta"] = $response_encuesta;
                $datos_club["LabelEncuesta"] = $r["LabelEncuesta"];

                //Encuestas2 de calificacion al abrir app
                $encuesta_activa = 0;
                $response_encuesta = array();
                $sql_encuesta = "SELECT * FROM Encuesta2 WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'S' or DirigidoA = 'T')  ORDER BY Orden ";
                $qry_encuesta = $dbo->query($sql_encuesta);
                if ($dbo->rows($qry_encuesta) > 0) {
                    while ($r_encuesta = $dbo->fetchArray($qry_encuesta)) {
                        $mostrar_encuesta = 0;
                        //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                        if ($r_encuesta["UnaporSocio"] == "S") {
                            $sql_resp = "Select IDEncuesta2 From Encuesta2Respuesta Where IDSocio = '" . $IDSocio . "' and IDEncuesta2 = '" . $r_encuesta["IDEncuesta2"] . "' Limit 1";
                            $r_resp = $dbo->query($sql_resp);
                            if ($dbo->rows($r_resp) <= 0) {
                                $mostrar_encuesta = 1;
                            }
                        } else {
                            $mostrar_encuesta = 1;
                        }
                        //Verifico si la encuesta es solo para algunos socios para mostrar o no
                        $permiso_encuesta = SIMWebServiceApp::verifica_ver_encuesta2($r_encuesta, $IDSocio);
                        //$permiso_encuesta=1;

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
                $datos_club["EncuestaCalificada"] = $response_encuesta;
                $datos_club["LabelEncuestaCalificada"] = $r["LabelEncuesta2"];
                $datos_club["SolicitarRegistroAutodiagnostico"] = "N";

                //Autodisagnostico al abrir app
                $diagnostico_activa = 0;
                $response_diagnostico = array();
                $sql_diagnostico = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'S' or DirigidoA = 'T')  ORDER BY Orden ";
                $qry_diagnostico = $dbo->query($sql_diagnostico);
                if ($dbo->rows($qry_diagnostico) > 0) {
                    while ($r_diagnostico = $dbo->fetchArray($qry_diagnostico)) {
                        $mostrar_disgnostico = 0;
                        //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                        if ($r_diagnostico["UnaporSocio"] == "S") {
                            $sql_resp = "Select IDDiagnostico From DiagnosticoRespuesta Where IDSocio = '" . $IDSocio . "' and IDDiagnostico = '" . $r_diagnostico["IDDiagnostico"] . "' Limit 1";
                            $r_resp = $dbo->query($sql_resp);
                            if ($dbo->rows($r_resp) <= 0) {
                                $mostrar_disgnostico = 1;
                            }
                        } else {
                            $fecha_hoy = date("Y-m-d") . " 00:00:00";
                            $sql_unica = "SELECT IDDiagnosticoRespuesta FROM  DiagnosticoRespuesta WHERE IDDiagnostico = '" . $r_diagnostico["IDDiagnostico"] . "' and FechaTrCr >= '" . $fecha_hoy . "' and IDSocio = '" . $IDSocio . "' ";
                            $r_resp = $dbo->query($sql_unica);
                            if ($dbo->rows($r_resp) <= 0) {
                                $mostrar_disgnostico = 1;
                            }
                        }
                        //Verifico si la encuesta es solo para algunos socios para mostrar o no
                        $permiso_diagnostico = SIMWebServiceApp::verifica_ver_diagnostico($r_diagnostico, $IDSocio, $IDUsuario);
                        //$permiso_encuesta=1;

                        if ($mostrar_disgnostico == 1 && $permiso_diagnostico == 1) {
                            $diagnostico["IDClub"] = $r_diagnostico["IDClub"];
                            $diagnostico["IDDiagnostico"] = $r_diagnostico["IDDiagnostico"];
                            $diagnostico["Nombre"] = $r_diagnostico["Nombre"];
                            $diagnostico["Descripcion"] = $r_diagnostico["Descripcion"];
                            if ($r_diagnostico["DiagnosticoObligatorio"] == "S") {
                                $datos_club["SolicitarRegistroAutodiagnostico"] = "S";
                                $datos_club["SolicitarRegistroAutodiagnosticoLabel"] = $otra_config_club["SolicitarRegistroAutodiagnosticoLabel"];
                            }

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
                $datos_club["Diagnostico"] = $response_diagnostico;
                $datos_club["LabelDiagnostico"] = $r["LabelDiagnostico"];

                // Se valida que no haya una encuesta activa destacada para asi mostrar la votacion
                if ($encuesta_activa == 0) {
                    $response_votacion = array();
                    $sql_votacion = "SELECT * FROM Votacion WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S'  ORDER BY Orden ";
                    $qry_votacion = $dbo->query($sql_votacion);
                    if ($dbo->rows($qry_votacion) > 0) {
                        while ($r_votacion = $dbo->fetchArray($qry_votacion)) {
                            $mostrar_votacion = 0;
                            //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                            if ($r_votacion["UnaporSocio"] == "S") {
                                $sql_resp = "Select IDVotacion From VotacionRespuesta Where IDSocio = '" . $IDSocio . "' and IDVotacion = '" . $r_encuesta["IDVotacion"] . "' Limit 1";
                                $r_resp = $dbo->query($sql_resp);
                                if ($dbo->rows($r_resp) <= 0) {
                                    $mostrar_votacion = 1;
                                }
                            } else {
                                $mostrar_votacion = 1;
                            }
                            //Verifico si la encuesta es solo para algunos socios para mostrar o no
                            $permiso_votacion = SIMWebServiceApp::verifica_ver_votacion($r_votacion, $IDSocio, $IDUsuario);
                            //$permiso_votacion=1;

                            if ($mostrar_votacion == 1 && $permiso_votacion == 1) {
                                $votacion["IDClub"] = $r_votacion["IDClub"];
                                $votacion["IDVotacion"] = $r_votacion["IDVotacion"];
                                $votacion["Nombre"] = $r_votacion["Nombre"];
                                $votacion["Descripcion"] = $r_votacion["Descripcion"];
                                if (!empty($r_votacion["Imagen"])) :
                                    $foto = BANNERAPP_ROOT . $r_votacion["Imagen"];
                                else :
                                    $foto = "";
                                endif;
                                $votacion["ImagenVotacion"] = $foto;
                                array_push($response_votacion, $votacion);
                            }
                        } //ednw while
                    }
                    //FIN Encuestas al abrir app
                    $datos_club["Votacion"] = $response_votacion;
                    $datos_club["LabelVotacion"] = $r["LabelVotacion"];
                }

                //Movilidad al abrir app
                $diagnostico_activa = 0;
                $response_diagnostico = array();
                $sql_diagnostico = "SELECT * FROM EncuestaArbol WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'S' or DirigidoA = 'T')  ORDER BY Orden ";
                $qry_diagnostico = $dbo->query($sql_diagnostico);
                if ($dbo->rows($qry_diagnostico) > 0) {
                    while ($r_diagnostico = $dbo->fetchArray($qry_diagnostico)) {
                        $mostrar_disgnostico = 0;
                        //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                        if ($r_diagnostico["UnaporSocio"] == "S") {
                            $sql_resp = "Select IDEncuestaArbol From EncuestaArbolRespuesta Where IDSocio = '" . $IDSocio . "' and IDEncuestaArbol = '" . $r_diagnostico["IDEncuestaArbol"] . "' Limit 1";
                            $r_resp = $dbo->query($sql_resp);
                            if ($dbo->rows($r_resp) <= 0) {
                                $mostrar_disgnostico = 1;
                            }
                        } else {
                            $mostrar_disgnostico = 1;
                        }
                        //Verifico si la encuesta es solo para algunos socios para mostrar o no
                        //require( LIBDIR . "SIMWebServiceEncuestaArbol.inc.php" );
                        //$permiso_diagnostico=SIMWebServiceEncuestaArbol::verifica_ver_EncuestaArbol($r_diagnostico,$IDSocio,$IDUsuario);
                        //$permiso_encuesta=1;

                        if ($mostrar_disgnostico == 1 && $permiso_diagnostico == 1) {
                            $diagnostico["IDClub"] = $r_diagnostico["IDClub"];
                            $diagnostico["IDMovilidad"] = $r_diagnostico["IDEncuestaArbol"];
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
                $datos_club["Movilidad"] = $response_diagnostico;
                $datos_club["LabelMovilidad"] = $r["LabelMovilidad"];

                //CARNET CASTILLO DE AMAGUAÑA
                $datos_club["TextoHeaderFormularioActivacionCarnetSeguridad"] = $dbo->getFields("ConfiguracionCarnet", "TextoHeaderFormularioActivacionCarnetSeguridad", "IDClub = '" . $IDClub . "'");;

                //Tipo de header de app
                if (empty($r["TipoHeaderApp"])) :
                    $datos_club["TipoHeaderApp"] = "Clasico";
                    $datos_club["TiempoPublicidadHeader"] = "0";
                else :
                    $datos_club["TipoHeaderApp"] = $r["TipoHeaderApp"];
                    $datos_club["TiempoPublicidadHeader"] = $r["TiempoPublicidadHeader"];
                endif;
                $datos_club["LabelAbrirNotificaciones"] =  $otra_config_club["LabelAbrirNotificaciones"];
                $datos_club["SolicitaAbrirNotificaciones"] =  $otra_config_club["SolicitaAbrirNotificaciones"];
                $datos_club["MostrarBotonBusquedaGeneral"] =  $otra_config_club["MostrarBotonBusquedaGeneral"];
                array_push($response, $datos_club);
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehaencontradoclub', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function get_app_empleado($IDClub, $IDUsuario = "")
    {

        $dbo = &SIMDB::get();
        $response = array();

        //duracion splash empleado
        $TiempoSplashEmpleado = $dbo->getFields("ConfiguracionClub", "TiempoSplashFuncionarios", "IDClub = '" . $IDClub . "'");

        $otra_config_club = $dbo->fetchAll("ConfiguracionClub", " IDClub  = '" . $IDClub . "' ", "array");

        //get splash para determinados empleados
        $sql1 = "SELECT * FROM ConfiguracionClub WHERE MostrarSplashHome = 'S' and IDClub = '" . $IDClub . "' ";
        $qry1 = $dbo->query($sql1);
        if ($dbo->rows($qry1) > 0) {
            $message = $dbo->rows($qry1) . " Encontrados";

            while ($r = $dbo->fetchArray($qry1)) {
                if (!empty($IDUsuario)) {
                    $mostrar_popup = SIMWebServiceApp::verifica_ver_popup_empl($r, $IDUsuario);
                }

                if ($mostrar_popup == 1) {

                    $popup["IDClub"] = $r["IDClub"];
                    $popup["IDConfiguracionClub"] = $r["IDConfiguracionClub"];
                    $popup["IDModuloLinkSplashHome"] = $r["IDModuloLinkSplashHome"];
                    $popup["MostrarSplashHome"] = $r["MostrarSplashHome"];
                    $popup["DirigidoAGeneral"] = $r["DirigidoAGeneral"];
                    $popup["LabelBotonSplashHome"] = $r["LabelBotonSplashHome"];
                    $popup["ImagenSplashHome"] = $r["ImagenSplashHome"];
                    $popup["LinkSplashHome"] = $r["LinkSplashHome"];


                    if (!empty($r["ImagenSplashHome"])) :
                        $foto = $r["ImagenSplashHome"];

                    endif;

                    $popup["ImagenSplashHome"] = $foto;
                }
            }
        }



        $sql = "SELECT * FROM AppEmpleado WHERE IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            $datos_club_soc = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            while ($r = $dbo->fetchArray($qry)) {
                if (!empty($r["FotoLogoApp"])) {
                    $foto_logo = CLUB_ROOT . $r["FotoLogoApp"];
                }
                if (!empty($r["FotoDiseno1"])) {
                    $foto1 = CLUB_ROOT . $r["FotoDiseno1"];
                }
                if (!empty($r["FotoDiseno2"])) {
                    $foto2 = CLUB_ROOT . $r["FotoDiseno2"];
                }

                //Banners
                $response_banner = array();
                $sql_banner = "SELECT * FROM BannerApp WHERE (DirigidoA = 'E' or DirigidoA = 'T') and Publicar = 'S' and IDClub = '" . $IDClub . "' ORDER BY IDBannerApp";
                $qry_banner = $dbo->query($sql_banner);
                if ($dbo->rows($qry_banner) > 0) {

                    while ($r_banner = $dbo->fetchArray($qry_banner)) {
                        $banner["IDClub"] = $IDClub;
                        $banner["IDBannerApp"] = $r_banner["IDBannerApp"];
                        $MostrarBotonSaltarSplash = $r_banner["MostrarBotonSaltarSplash"];
                        $LabelBotonSaltarSplash = $r_banner["LabelBotonSaltarSplash"];
                        $banner["UrlLink"] = $r_banner["UrlLink"];
                        if (!empty($r_banner["Foto1"])) :
                            $foto = BANNERAPP_ROOT . $r_banner["Foto1"];
                        else :
                            $foto = "";
                        endif;
                        $banner["Foto"] = $foto;
                        array_push($response_banner, $banner);
                    } //ednw hile
                }

                //Otros datos Usuario
                $datos_usuario = $dbo->fetchAll("Usuario", " IDUsuario = '" . $IDUsuario . "' ", "array");


                // Georeferenciacion
                //Tomo los valores de accesos que debe apolicar para invitados
                $IDParametroAcceso = $dbo->getFields("ParametroAcceso", "IDParametroAcceso", "IDClub = '" . $IDClub . "'");
                $datos_ParametroAcceso = $dbo->fetchAll("ParametroAcceso", " IDParametroAcceso = '" . $IDParametroAcceso . "' ", "array");
                $response_georef = array();
                $georef["Georeferenciacion"] = $datos_ParametroAcceso["Georeferenciacion"];
                $georef["Latitud"] = $datos_ParametroAcceso["Latitud"];
                $georef["Longitud"] = $datos_ParametroAcceso["Longitud"];
                $georef["Rango"] = $datos_ParametroAcceso["Rango"];
                $georef["MensajeFueraRango"] = $datos_ParametroAcceso["MensajeFueraRango"];
                $georef["Georeferenciacion"] = $datos_ParametroAcceso["Georeferenciacion"];
                //array_push($response_georef, $georef);

                $datos_club["IDClub"] = $r["IDClub"];
                $datos_club["Telefono"] = $datos_club_soc["Telefono"];
                $datos_club["Foto"] = $foto_logo;
                $datos_club["Foto1"] = $foto1;
                $datos_club["Foto2"] = $foto2;
                $datos_club["Color1"] = $r["Color1"];
                $datos_club["Color2"] = $r["Color2"];
                $datos_club["Banner"] = $response_banner;
                $datos_club["TiempoSplash"] = $TiempoSplashEmpleado;
                $datos_club["Terminos"] = $r["TerminosEmpleados"];
                $datos_club["PermiteInvitacionPortero"] = $r["PermiteInvitacionPortero"];
                $datos_club["MostrarBotonSaltarSplash"] = $MostrarBotonSaltarSplash;
                $datos_club["LabelBotonSaltarSplash"] = $LabelBotonSaltarSplash;

                //datos IOS Version
                $datos_club["iosVersion"] = $r["Version"];
                $datos_club["iosEsencial"] = $r["Esencial"];
                $datos_club["iosversionMessage"] = $r["VersionMessage"];
                $datos_club["versionURLiOS"] = $r["VersionURLIOS"];

                //datos Android Version
                $datos_club["androidVersion"] = $r["VersionAndroid"];
                $datos_club["androidEsencial"] = $r["EsencialAndroid"];
                $datos_club["androidversionMessage"] = $r["VersionMessageAndroid"];
                $datos_club["versionURLAndroid"] = $r["VersionURLAndroid"];

                $datos_club["Georeferenciacion"] = $georef;

                $datos_club["LabelTerminos"] = $r["LabelTerminos"];
                $datos_club["TipoTerminos"] = $r["TipoTerminos"];
                $datos_club["ArchivoTerminos"] = CLUB_ROOT . $r["ArchivoTerminos"];
                $datos_club["LabelInvitacion"] = $otra_config_club["LabelInvitacion"];
                $datos_club["PorcentajeQrAndroid"] = $r["PorcentajeQrAndroid"];
                $datos_club["PorcentajeQrIOS"] = $r["PorcentajeQrIOS"];

                //DATOS DEL POPUP
                $datos_club["MostrarSplashHome"] = $popup["MostrarSplashHome"];
                $datos_club["LabelBotonSplashHome"] = $popup["LabelBotonSplashHome"];
                $datos_club["ImagenSplashHome"] = CLUB_ROOT . $popup["ImagenSplashHome"];
                $datos_club["LinkSplashHome"] = $popup["LinkSplashHome"];
                $datos_club["IDModuloLinkSplashHome"] = $popup["IDModuloLinkSplashHome"] == 0 ? "" : $popup["IDModuloLinkSplashHome"];
                //FIN DATOS

                $datos_club["LabelBotonCumpleanos"] =  $otra_config_club["LabelBotonCumpleanos"];
                $datos_club["ImagenCumpleanos"] = BANNERAPP_ROOT . $otra_config_club["ImagenCumpleanos"];
                $datos_club["MostrarMensajeCumpleanos"] = $datos_usuario["MostrarMensajeCumpleanos"];

                $datos_club["LabelFotoError"] = "Esta seguro de cambiar la foto?";

                if (!empty($r["OpcionesIngreso"])) {
                    $datos_club["OpcionesTipoIngreso"] = "S";
                } else {
                    $datos_club["OpcionesTipoIngreso"] = "N";
                }

                $datos_club["OpcionesIngreso"] = $r["OpcionesIngreso"];

                //Tipo de header de app
                if (empty($r["TipoHeaderApp"])) :
                    $datos_club["TipoHeaderApp"] = "Clasico";
                    $datos_club["TiempoPublicidadHeader"] = "0";
                else :
                    $datos_club["TipoHeaderApp"] = $r["TipoHeaderApp"];
                    $datos_club["TiempoPublicidadHeader"] = $r["TiempoPublicidadHeader"];
                endif;


                $CerrarSesion = $datos_usuario["SolicitarCierreSesion"];
                if ($CerrarSesion == "S") {
                    $condicion_modulo = " and IDModulo = 14 "; //cerrar sesion
                }

                //Modulos Sistema Menu Central
                $response_modulo = array();
                $sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '" . $IDClub . "' and Activo = 'S' and Ubicacion like '%Central%' " . $condicion_modulo . " ORDER BY Orden";
                $qry_modulo = $dbo->query($sql_modulo);
                if ($dbo->rows($qry_modulo) > 0) {
                    while ($r_modulo = $dbo->fetchArray($qry_modulo)) {

                        $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $r_modulo["IDModulo"] . "' ", "array");

                        $agregar_modulo = SIMWebServiceClub::verifica_permiso_modulo($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);

                        if ($agregar_modulo == "S") :
                            // Verificar si el modulo tiene contenido para mostrar
                            //$flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $IDClub);
                            //$flag_mostrar=0;
                            if ($flag_mostrar == 0) :
                                $modulo["IDClub"] = $IDClub;
                                $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                $modulo["Tipo"] = $datos_modulo["Tipo"];
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
                                array_push($response_modulo, $modulo);
                            endif;
                        endif;
                    } //ednw while
                }

                //Modulos Sistema Menu Lateral
                unset($modulo);
                $response_modulo_lateral = array();
                $sql_modulo = "SELECT * FROM AppEmpleadoModulo WHERE IDClub = '" . $IDClub . "' and Activo = 'S' and Ubicacion like '%Lateral%' " . $condicion_modulo . " ORDER BY Orden";
                $qry_modulo = $dbo->query($sql_modulo);
                if ($dbo->rows($qry_modulo) > 0) {

                    while ($r_modulo = $dbo->fetchArray($qry_modulo)) {
                        $agregar_modulo = SIMWebServiceClub::verifica_permiso_modulo($r_modulo["IDModulo"], $datos_usuario["IDPerfil"]);

                        $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $r_modulo["IDModulo"] . "' ", "array");
                        if ($agregar_modulo == "S") :
                            // Verificar si el modulo tiene contenido para mostrar
                            $flag_mostrar = SIMWebServiceClub::verificar_contenido_modulo($r_modulo["IDModulo"], $IDClub);
                            //$flag_mostrar=0;
                            if ($flag_mostrar == 0) :
                                $modulo["IDClub"] = $IDClub;
                                $modulo["IDModulo"] = $r_modulo["IDModulo"];
                                $modulo["Tipo"] = $datos_modulo["Tipo"];
                                //$modulo["NombreModulo"] = utf8_encode($dbo->getFields( "Modulo" , "Nombre" , "IDModulo = '".$r_modulo["IDModulo"]."'" ));
                                if (!empty($r_modulo["TituloLateral"])) {
                                    $modulo["NombreModulo"] = trim($r_modulo["TituloLateral"]);
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
                                array_push($response_modulo_lateral, $modulo);
                            endif;
                        endif;
                    } //ednw while
                }

                //traer servicios del usuario
                $response_servicio = array();
                $sql_servicios = "SELECT Servicio.* FROM UsuarioServicio, Servicio WHERE UsuarioServicio.IDUsuario = '" . $IDUsuario . "' AND UsuarioServicio.IDServicio = Servicio.IDServicio ";
                $qry_servicios = $dbo->query($sql_servicios);
                while ($r_servicio = $dbo->fetchArray($qry_servicios)) {
                    $servicio["IDClub"] = $IDClub;
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

                $tipo_codigo_carne = $dbo->getFields("AppEmpleado", "TipoCodigoCarne", "IDClub = '" . $IDClub . "'");
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

                $datos_club["IDUsuario"] = $datos_usuario["IDUsuario"];
                $datos_club["IDPerfil"] = $datos_usuario["IDPerfil"];
                $respdatos_clubonse["Nombre"] = $datos_usuario["Nombre"];
                $datos_club["Autorizado"] = $datos_usuario["Autorizado"];
                $datos_club["Nivel"] = $datos_usuario["Nivel"];
                $datos_club["Permiso"] = $datos_usuario["Permiso"];
                $datos_club["ServiciosReserva"] = $response_servicio;
                $datos_club["Modulos"] = $response_modulo;
                $datos_club["ModulosLateral"] = $response_modulo_lateral;
                $datos_club["CodigoBarras"] = $foto_cod_barras;
                $datos_club["Dispositivo"] = $datos_usuario["Dispositivo"];
                $resdatos_clubponse["Token"] = $datos_usuario["Token"];
                //$response["NumeroDerecho"] = $datos_usuario["CodigoUsuario"];
                $datos_club["NumeroDerecho"] = "";
                //Consulto si el app esta configurado para permitir se puede cambiar p[ara que sea por usuario
                $datos_club["PermiteInvitacionPortero"] = $dbo->getFields("AppEmpleado", "PermiteInvitacionPortero", "IDClub = '" . $IDClub . "'");
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
                $datos_club["Area"] = $nombre_areas;
                $datos_club["Cargo"] = utf8_encode($datos_usuario["Cargo"]);
                $datos_club["Codigo"] = $datos_usuario["CodigoUsuario"];
                $datos_club["PermiteReservar"] = $datos_usuario["PermiteReservar"];
                $datos_club["SolicitaEditarPerfil"] = $datos_usuario["SolicitaEditarPerfil"];
                $datos_club["Activo"] = $datos_usuario["Activo"];
                //$datos_club["Foto"] = $foto_empleado;
                $datos_club["TipoUsuario"] = SIMUtil::get_traduccion('', '', 'Empleado', LANG);

                //Fin otros datos usuario




                //Encuestas al abrir app
                $encuesta_activa = 0;
                $response_encuesta = array();
                $sql_encuesta = "SELECT * FROM Encuesta WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'E' or DirigidoA = 'T')  ORDER BY Orden ";
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
                $datos_club["Encuesta"] = $response_encuesta;
                $datos_club["LabelEncuesta"] = SIMUtil::get_traduccion('', '', 'Encuesta', LANG);

                //Autodisagnostico al abrir app
                $diagnostico_activa = 0;
                $response_diagnostico = array();
                $sql_diagnostico = "SELECT * FROM Diagnostico WHERE Publicar = 'S' and IDClub = '" . $IDClub . "' and FechaInicio <= CURDATE() AND FechaFin >= CURDATE() and SolicitarAbrirApp='S' and (DirigidoA = 'E' or DirigidoA = 'T')  ORDER BY Orden ";
                $qry_diagnostico = $dbo->query($sql_diagnostico);
                if ($dbo->rows($qry_diagnostico) > 0) {
                    while ($r_diagnostico = $dbo->fetchArray($qry_diagnostico)) {
                        $mostrar_disgnostico = 0;
                        //Verifico si la encuesta solo permite 1 por socio si es asi verifico si ya la contestó para mostrarla o no
                        if ($r_diagnostico["UnaporSocio"] == "S") {
                            $sql_resp = "Select IDDiagnostico From DiagnosticoRespuesta Where IDSocio = '" . $IDSocio . "' and IDDiagnostico = '" . $r_diagnostico["IDDiagnostico"] . "' Limit 1";
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
                $datos_club["Diagnostico"] = $response_diagnostico;
                $datos_club["LabelDiagnostico"] = $r["LabelDiagnostico"];
                $datos_club["LabelAbrirNotificaciones"] =  $otra_config_club["LabelAbrirNotificaciones"];
                $datos_club["SolicitaAbrirNotificaciones"] =  $otra_config_club["SolicitaAbrirNotificaciones"];
                $datos_club["MostrarBotonBusquedaGeneral"] =  $otra_config_club["MostrarBotonBusquedaGeneral"];
                array_push($response, $datos_club);
            } //ednw hile

            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Nosehaencontradoclub', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function        

    public function get_seccion_club($IDClub, $id_socio = "")
    {
        $dbo = &SIMDB::get();
        $response = array();
        $contador_resultado = 0;

        //Secciones Noticia
        $sql = "SELECT * FROM Seccion S  WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' ORDER BY S.Orden";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $contador_resultado++;
            $seccion["IDClub"] = $r["IDClub"];
            //Verifico si tiene nombre pesonalizado la sección
            //$nombre_modulo = utf8_encode($dbo->getFields( "ClubModulo" , "Titulo" , "IDClub = '".$IDClub."' and IDModulo = 3" ));
            if (empty($nombre_modulo)) {
                $nombre_modulo = "Noticia";
            }

            $seccion["Tipo"] = $nombre_modulo;
            $seccion["IDSeccion"] = $r["IDSeccion"];
            $seccion["Nombre"] = $r["Nombre"];
            $seccion["Descripcion"] = $r["Descripcion"];

            // verifico si es de preferencia del socio
            if (!empty($id_socio)) :
                $sql_preferencia = "Select * From SocioSeccion Where IDSocio = '" . $id_socio . "' and IDSeccion = '" . $seccion["IDSeccion"] . "'";
                $result_preferencia = $dbo->query($sql_preferencia);
                if ($dbo->rows($result_preferencia) > 0) :
                    $seccion["PreferenciaSocio"] = "S";
                else :
                    $seccion["PreferenciaSocio"] = "N";
                endif;
            else :
                $seccion["PreferenciaSocio"] = "N";
            endif;

            array_push($response, $seccion);
        } //end while

        //Secciones Noticia 3
        unset($seccion);
        $nombre_modulo = "";
        $sql = "SELECT * FROM Seccion2 S  WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' ORDER BY S.Orden";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $contador_resultado++;
            $seccion["IDClub"] = $r["IDClub"];
            //Verifico si tiene nombre pesonalizado la sección
            //$nombre_modulo = utf8_encode($dbo->getFields( "ClubModulo" , "Titulo" , "IDClub = '".$IDClub."' and IDModulo = 3" ));
            if (empty($nombre_modulo)) {
                $nombre_modulo = "Noticia2";
            }

            $seccion["Tipo"] = $nombre_modulo;
            $seccion["IDSeccion"] = $r["IDSeccion"];
            $seccion["Nombre"] = $r["Nombre"];
            $seccion["Descripcion"] = $r["Descripcion"];

            // verifico si es de preferencia del socio
            if (!empty($id_socio)) :
                $sql_preferencia = "Select * From SocioSeccion2 Where IDSocio = '" . $id_socio . "' and IDSeccion = '" . $seccion["IDSeccion"] . "'";
                $result_preferencia = $dbo->query($sql_preferencia);
                if ($dbo->rows($result_preferencia) > 0) :
                    $seccion["PreferenciaSocio"] = "S";
                else :
                    $seccion["PreferenciaSocio"] = "N";
                endif;
            else :
                $seccion["PreferenciaSocio"] = "N";
            endif;

            array_push($response, $seccion);
        } //end while

        //Secciones Noticia 3
        unset($seccion);
        $nombre_modulo = "";
        $sql = "SELECT * FROM Seccion3 S  WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' ORDER BY S.Orden";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $contador_resultado++;
            $seccion["IDClub"] = $r["IDClub"];
            //Verifico si tiene nombre pesonalizado la sección
            //$nombre_modulo = utf8_encode($dbo->getFields( "ClubModulo" , "Titulo" , "IDClub = '".$IDClub."' and IDModulo = 3" ));
            if (empty($nombre_modulo)) {
                $nombre_modulo = "Noticia3";
            }

            $seccion["Tipo"] = $nombre_modulo;
            $seccion["IDSeccion"] = $r["IDSeccion"];
            $seccion["Nombre"] = $r["Nombre"];
            $seccion["Descripcion"] = $r["Descripcion"];

            // verifico si es de preferencia del socio
            if (!empty($id_socio)) :
                $sql_preferencia = "Select * From SocioSeccion3 Where IDSocio = '" . $id_socio . "' and IDSeccion = '" . $seccion["IDSeccion"] . "'";
                $result_preferencia = $dbo->query($sql_preferencia);
                if ($dbo->rows($result_preferencia) > 0) :
                    $seccion["PreferenciaSocio"] = "S";
                else :
                    $seccion["PreferenciaSocio"] = "N";
                endif;
            else :
                $seccion["PreferenciaSocio"] = "N";
            endif;

            array_push($response, $seccion);
        } //end while

        unset($seccion);
        $nombre_modulo = "";
        //Secciones Evento
        $sql = "SELECT * FROM SeccionEvento S  WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' ORDER BY S.Orden";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $contador_resultado++;
            $seccion["IDClub"] = $r["IDClub"];
            //Verifico si tiene nombre pesonalizado la sección
            //$nombre_modulo = utf8_encode($dbo->getFields( "ClubModulo" , "Titulo" , "IDClub = '".$IDClub."' and IDModulo = 4" ));
            if (empty($nombre_modulo)) {
                $nombre_modulo = "Evento";
            }

            $seccion["Tipo"] = $nombre_modulo;

            $seccion["IDSeccion"] = $r["IDSeccionEvento"];
            $seccion["Nombre"] = $r["Nombre"];
            $seccion["Descripcion"] = $r["Descripcion"];

            // verifico si es de preferencia del socio
            if (!empty($id_socio)) :
                $sql_preferencia = "Select * From SocioSeccionEvento Where IDSocio = '" . $id_socio . "' and IDSeccionEvento = '" . $seccion["IDSeccion"] . "'";
                $result_preferencia = $dbo->query($sql_preferencia);
                if ($dbo->rows($result_preferencia) > 0) :
                    $seccion["PreferenciaSocio"] = "S";
                else :
                    $seccion["PreferenciaSocio"] = "N";
                endif;
            else :
                $seccion["PreferenciaSocio"] = "N";
            endif;

            array_push($response, $seccion);
        } //end while

        unset($seccion);
        $nombre_modulo = "";
        //Secciones Galeria
        $sql = "SELECT * FROM SeccionGaleria S  WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' ORDER BY S.Orden";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $contador_resultado++;
            $seccion["IDClub"] = $r["IDClub"];
            //Verifico si tiene nombre pesonalizado la sección
            //$nombre_modulo = utf8_encode($dbo->getFields( "ClubModulo" , "Titulo" , "IDClub = '".$IDClub."' and IDModulo = 5" ));
            if (empty($nombre_modulo)) {
                $nombre_modulo = "Galeria";
            }

            $seccion["Tipo"] = $nombre_modulo;
            $seccion["IDSeccion"] = $r["IDSeccionGaleria"];
            $seccion["Nombre"] = $r["Nombre"];
            $seccion["Descripcion"] = $r["Descripcion"];

            // verifico si es de preferencia del socio
            if (!empty($id_socio)) :
                $sql_preferencia = "Select * From SocioSeccionGaleria Where IDSocio = '" . $id_socio . "' and IDSeccionGaleria = '" . $seccion["IDSeccion"] . "'";
                $result_preferencia = $dbo->query($sql_preferencia);
                if ($dbo->rows($result_preferencia) > 0) :
                    $seccion["PreferenciaSocio"] = "S";
                else :
                    $seccion["PreferenciaSocio"] = "N";
                endif;
            else :
                $seccion["PreferenciaSocio"] = "N";
            endif;

            array_push($response, $seccion);
        } //end while

        unset($seccion);
        $nombre_modulo = "";
        //Secciones Clasificado
        $sql = "SELECT * FROM SeccionClasificados S  WHERE S.Publicar = 'S' and S.IDClub = '" . $IDClub . "' ORDER BY S.Orden";
        $qry = $dbo->query($sql);
        while ($r = $dbo->fetchArray($qry)) {
            $contador_resultado++;
            $seccion["IDClub"] = $r["IDClub"];
            //Verifico si tiene nombre pesonalizado la sección
            $nombre_modulo = utf8_encode($dbo->getFields("ClubModulo", "Titulo", "IDClub = '" . $IDClub . "' and IDModulo = 46"));
            $nombre_modulo = "Clasificado";

            $seccion["Tipo"] = $nombre_modulo;
            $seccion["IDSeccion"] = $r["IDSeccionClasificados"];
            $seccion["Nombre"] = $r["Nombre"];
            $seccion["Descripcion"] = $r["Descripcion"];

            // verifico si es de preferencia del socio
            if (!empty($id_socio)) :
                $sql_preferencia = "Select * From SocioSeccionClasificados Where IDSocio = '" . $id_socio . "' and IDSeccionClasificados = '" . $seccion["IDSeccion"] . "'";
                $result_preferencia = $dbo->query($sql_preferencia);
                if ($dbo->rows($result_preferencia) > 0) :
                    $seccion["PreferenciaSocio"] = "S";
                else :
                    $seccion["PreferenciaSocio"] = "N";
                endif;
            else :
                $seccion["PreferenciaSocio"] = "N";
            endif;

            array_push($response, $seccion);
        } //end while

        $message = $contador_resultado . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
        if ($contador_resultado > 0) {
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    } // fin function

    public function verifica_version_app($IDClub, $AppVersion, $Dispositivo, $TipoApp = "")
    {
        $dbo = &SIMDB::get();
        if ($Dispositivo == "Android") :
            $CampoVersion = "VersionAndroid";
            $CampoEsencial = "EsencialAndroid";
            $CampoMensaje = "VersionMessageAndroid";
            $CampoUrl = "VersionURLAndroid";
        else :
            $CampoVersion = "Version";
            $CampoEsencial = "Esencial";
            $CampoMensaje = "VersionMessage";
            $CampoUrl = "VersionURLIOS";
        endif;

        if ($TipoApp == "Empleado") :
            //Consulto cual debe ser la ultima la version de empleados segun Dispositivo
            $datos_appempleado = $dbo->fetchAll("AppEmpleado", " IDClub = '" . $IDClub . "' ", "array");
            $numero_version = $datos_appempleado[$CampoVersion];
            $esencial_version = $datos_appempleado[$CampoEsencial];
            if ($datos_club[$CampoVersion] != $AppVersion && $datos_club[$CampoEsencial] == "S") :
                $respuesta["message"] = $datos_club[$CampoMensaje] . " " . $datos_club[$CampoUrl];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                //inserta _log
                $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('verifica_version_app','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
                die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
                exit;
            endif;
        else :
            //Consulto cual debe ser la ultima la version funcionando segun Dispositivo
            $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            $numero_version = $dbo->getFields("Club", $CampoVersion, "IDClub = '" . $IDClub . "'");
            $esencial_version = $dbo->getFields("Club", $CampoEsencial, "IDClub = '" . $IDClub . "'");

            if ($datos_club[$CampoVersion] != $AppVersion && $datos_club[$CampoEsencial] == "S") :
                $respuesta["message"] = $datos_club[$CampoMensaje] . " " . $datos_club[$CampoUrl];
                $respuesta["success"] = false;
                $respuesta["response"] = null;
                //inserta _log
                $sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (Servicio, Parametros, Respuesta) Values ('verifica_version_app','" . json_encode($_GET) . "','" . json_encode($respuesta) . "')");
                die(json_encode(array('success' => $respuesta[success], 'message' => $respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver)));
                exit;
            endif;
        endif;
    }

    // NUEVAS

    public function verifica_permiso_modulo($IDModulo, $IDPerfil)
    {
        $dbo = &SIMDB::get();
        $agregar_modulo = "S";

        //Dependiendo el perfil muestro o no los modulos
        switch ($IDModulo) {
            case "1": //Invitados, Solo porteria y admin puede tener acceso a este modulo
                if ($IDPerfil == "4" || $IDPerfil == "25" || $IDPerfil == "16" || $IDPerfil == "63") {
                    $agregar_modulo = "S";
                } else {
                    $agregar_modulo = "N";
                }
                break;
            case "29": //Agenda, Solo porteria y admin puede tener acceso a este modulo
                $VerAgenda = $dbo->getFields("Perfil", "PuedeVerAgenda", "IDPerfil = $IDPerfil");

                if ($IDPerfil == "2" || $IDPerfil == "3" || $IDPerfil == "10" || $IDPerfil == "31" || $IDPerfil == "63" || $VerAgenda == 1) {
                    $agregar_modulo = "S";
                } else {
                    $agregar_modulo = "N";
                }
                break;
            case "2": //Reservas, Solo porteria y admin puede tener acceso a este modulo
                if ($IDPerfil == "2" || $IDPerfil == "3" || $IDPerfil == "10") {
                    $agregar_modulo = "S";
                } else {
                    $agregar_modulo = "N";
                }
                break;
        }

        //Perfil admin puede ver todo
        if ($IDPerfil == "1") :
            $agregar_modulo = "S";
        endif;

        return $agregar_modulo;
    }

    public function get_submodulo($IDClub, $IDModulo)
    {
        $dbo = &SIMDB::get();

        $response = array();
        $datos_club = $dbo->fetchAll("Club", "IDClub = $IDClub");
        $sql = "Select * From SubModulo Where IDModulo = '" . $IDModulo . "' and IDClub = '" . $IDClub . "'";
        $qry = $dbo->query($sql);
        if ($dbo->rows($qry) > 0) {
            $message = $dbo->rows($qry) . " " . SIMUtil::get_traduccion('', '', 'Encontrados', LANG);
            while ($r = $dbo->fetchArray($qry)) {
                $modulo["IDClub"] = $IDClub;
                $modulo["IDModulo"] = $IDModulo;
                $modulo["IDSubModulo"] = $r["IDSubModulo"];
                $modulo["MostrarMisReservas"] = $r["MostrarMisReservas"];
                $modulo["TextoBotonMisReservas"] = $r["TextoBotonMisReservas"];

                //Eventos
                $response_eve = array();
                if (!empty($r["IDSeccionEvento"])) :
                    $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '4' Limit 1";
                    $qry_modulo = $dbo->query($sql_modulo);
                    $r_modulo = $dbo->fetchArray($qry_modulo);
                    $modulo_eve["IDModulo"] = $r_modulo["IDModulo"];
                    if (!empty($r_modulo["Titulo"])) {
                        $modulo_eve["NombreModulo"] = str_replace(" ", "", $r_modulo["Titulo"]);
                    } else {
                        $modulo_eve["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                    }

                    $modulo_eve["Orden"] = $r_modulo["Orden"];
                    $icono_modulo = $r_modulo["Icono"];
                    if (!empty($r_modulo["Icono"])) :
                        $foto = MODULO_ROOT . $r_modulo["Icono"];
                    else :
                        $foto = "";
                    endif;
                    $modulo_eve["Icono"] = $foto;

                    if (($IDClub == "7") && ($IDModulo == "50" || $IDModulo == "54")) {
                        $modulo_eve["NombreModulo"] = SIMUtil::get_traduccion('', '', 'InscripcionTorneos', LANG);
                        $modulo_eve["Icono"] = MODULO_ROOT . "icon_rankf.png";
                    }




                    if (($IDClub == "9") && $IDModulo == "50") {
                        $modulo_eve["NombreModulo"] = SIMUtil::get_traduccion('', '', 'InscripcionRanking', LANG);
                        $modulo_eve["Icono"] = MODULO_ROOT . "icon_rankf.png";
                    }

                    $array_evento = explode("|", $r["IDSeccionEvento"]);
                    $response_id_eve = array();
                    foreach ($array_evento as $id_evento) :
                        $array_id_eve["IDSeccionEvento"] = $id_evento;
                        array_push($response_id_eve, $array_id_eve);
                    endforeach;

                    $modulo_eve["SeccionEvento"] = $response_id_eve;
                    array_push($response_eve, $modulo_eve);
                    $modulo["Eventos"] = $response_eve;
                endif;

                //Reservas
                $response_reserva = array();
                if (!empty($r["IDServicio"])) :
                    $array_reserva = explode("|", $r["IDServicio"]);
                    $response_id_reserva = array();
                    $id_servicio_sub = implode(",", $array_reserva);
                    $sql_sub = "SELECT S.IDServicio,SM.IDServicioMaestro FROM Servicio S,ServicioMaestro SM  WHERE S.IDServicioMaestro=SM.IDServicioMaestro and S.IDServicio in (" . $id_servicio_sub . ")";
                    $r_sub = $dbo->query($sql_sub);
                    unset($array_reserva);
                    while ($row_sub = $dbo->fetchArray($r_sub)) {
                        $OrdenServicio = $dbo->getFields("ServicioClub", "Orden", "IDServicioMaestro = '" . $row_sub["IDServicioMaestro"] . "' and IDClub = '" . $IDClub . "'");
                        $array_reserva[$OrdenServicio] = $row_sub["IDServicio"];
                    }

                    ksort($array_reserva);
                    foreach ($array_reserva as $id_servicio) :
                        $datos_servicio = $dbo->fetchAll("Servicio", " IDServicio = '" . $id_servicio . "' ", "array");
                        $NombrePersonalizado = $dbo->getFields("ServicioClub", "TituloServicio", "IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "' and IDClub = '" . $IDClub . "'");

                        $array_id_serv["IDServicio"] = $id_servicio;
                        //Si tiene un nombre personalizado se lo pongo de lo contrario le pongo el general
                        if (!empty($NombrePersonalizado)) :
                            //$array_id_serv["Nombre"] = str_replace(" ","",$NombrePersonalizado);
                            $array_id_serv["Nombre"] = $NombrePersonalizado;
                            if ($datos_club[SoloIcono] == 'S') :
                                $array_id_serv["Nombre"] = "";
                            endif;
                        else :
                            $array_id_serv["Nombre"] = $dbo->getFields("ServicioMaestro", "Nombre", "IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "'");
                        endif;

                        $foto = "";
                        if (!empty($datos_servicio["Icono"])) {
                            $foto = SERVICIO_ROOT . $datos_servicio["Icono"];
                        } else {
                            $icono_maestro = $dbo->getFields("ServicioMaestro", "Icono", "IDServicioMaestro = '" . $datos_servicio["IDServicioMaestro"] . "'");
                            if (!empty($icono_maestro)) {
                                $foto = SERVICIO_ROOT . $icono_maestro;
                            }
                        }
                        $array_id_serv["Icono"] = $foto;
                        array_push($response_id_reserva, $array_id_serv);
                    endforeach;
                    //Ordeno el array pr el or

                    $modulo["Reservas"] = $response_id_reserva;
                endif;

                //Noticias
                $response_not = array();
                if (!empty($r["IDSeccionNoticia"]) || !empty($r["IDSeccionNoticia2"]) || !empty($r["IDSeccionNoticia3"]) || !empty($r["IDSeccionNoticiaInfi"])) :
                    $sql_modulo = "SELECT CM.*, M.Tipo AS TipoModulo FROM ClubModulo CM, Modulo M WHERE CM.IDClub = '$IDClub' AND CM.IDModulo = M.IDModulo AND (M.IDModulo = 3 OR M.IDModulo = 66 OR M.IDModulo = 81 OR M.Tipo = 'Noticias') AND CM.Activo = 'S'";
                    $qry_modulo = $dbo->query($sql_modulo);
                    while ($r_modulo = $dbo->fetchArray($qry_modulo)) :

                        $modulo_not["IDModulo"] = $r_modulo["IDModulo"];

                        if (!empty($r_modulo["Titulo"]))
                            $modulo_not["NombreModulo"] = str_replace(" ", "", $r_modulo["Titulo"]);
                        else
                            $modulo_not["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));

                        $modulo_not["Orden"] = $r_modulo["Orden"];
                        $modulo_not["Tipo"] = $r_modulo["TipoModulo"];
                        $icono_modulo = $r_modulo["Icono"];
                        if (!empty($r_modulo["Icono"])) :
                            $foto = MODULO_ROOT . $r_modulo["Icono"];
                        else :
                            $foto = "";
                        endif;
                        $modulo_not["Icono"] = $foto;

                        if (($IDClub == "44") && ($IDModulo == "88" || $IDModulo == "89")) {
                            $modulo_not["NombreModulo"] = "Horarios";
                        }


                        $array_noticia = explode("|", $r["IDSeccionNoticia"]);
                        $array_noticia2 = explode("|", $r["IDSeccionNoticia2"]);
                        $array_noticia3 = explode("|", $r["IDSeccionNoticia3"]);
                        $array_noticiaInfi = explode("|", $r["IDSeccionNoticiaInfi"]);
                        $response_id_not = array();
                        $mostrar = 0;

                        if ($r_modulo["IDModulo"] == 3) :
                            foreach ($array_noticia as $id_noticia) :
                                if (!empty($id_noticia)) {
                                    $array_id_not["IDSeccionNoticia"] = $id_noticia;
                                    array_push($response_id_not, $array_id_not);
                                    $mostrar = 1;
                                }
                            endforeach;
                        elseif ($r_modulo["IDModulo"] == 66) :
                            foreach ($array_noticia2 as $id_noticia) :
                                if (!empty($id_noticia)) {
                                    $array_id_not["IDSeccionNoticia"] = $id_noticia;
                                    array_push($response_id_not, $array_id_not);
                                    $mostrar = 1;
                                }
                            endforeach;
                        elseif ($r_modulo["IDModulo"] == 81) :
                            foreach ($array_noticia3 as $id_noticia) :
                                if (!empty($id_noticia)) {
                                    $array_id_not["IDSeccionNoticia"] = $id_noticia;
                                    array_push($response_id_not, $array_id_not);
                                    $mostrar = 1;
                                }
                            endforeach;
                        else :
                            foreach ($array_noticiaInfi as $id_noticia) :
                                $array_id_not["IDSeccionNoticia"] = $id_noticia;
                                // BUSCAR NUEVO IDMODULO
                                $ModuloSeccion = $dbo->getFields("SeccionNoticiaInfinita", "IDModulo", "IDSeccionNoticiaInfinita = $id_noticia");

                                // NO HAY SECCIONES SI EL MODULO NO ESTA ACTIVO
                                if ($r_modulo["IDModulo"] == $ModuloSeccion) :
                                    array_push($response_id_not, $array_id_not);
                                    $mostrar = 1;
                                else :
                                    $mostrar = 0;
                                endif;

                            endforeach;
                        endif;

                        if ($mostrar == 1) :
                            $modulo_not["SeccionNoticia"] = $response_id_not;
                            array_push($response_not, $modulo_not);
                        endif;

                    endwhile;

                    $modulo["Noticias"] = $response_not;

                endif;

                //Galerias
                $response_gal = array();
                if (!empty($r["IDSeccionGaleria"])) :
                    $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '5' Limit 1";
                    $qry_modulo = $dbo->query($sql_modulo);
                    $r_modulo = $dbo->fetchArray($qry_modulo);
                    $modulo_gal["IDModulo"] = $r_modulo["IDModulo"];
                    if (!empty($r_modulo["Titulo"])) {
                        $modulo_gal["NombreModulo"] = str_replace(" ", "", $r_modulo["Titulo"]);
                    } else {
                        $modulo_gal["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                    }

                    $modulo_gal["Orden"] = $r_modulo["Orden"];
                    $icono_modulo = $r_modulo["Icono"];
                    if (!empty($r_modulo["Icono"])) :
                        $foto = MODULO_ROOT . $r_modulo["Icono"];
                    else :
                        $foto = "";
                    endif;
                    $modulo_gal["Icono"] = $foto;
                    $array_galeria = explode("|", $r["IDSeccionGaleria"]);
                    $response_id_gal = array();
                    foreach ($array_galeria as $id_galeria) :
                        $array_id_gal["IDSeccionGaleria"] = $id_galeria;
                        array_push($response_id_gal, $array_id_gal);
                    endforeach;

                    $modulo_gal["SeccionGaleria"] = $response_id_gal;
                    array_push($response_gal, $modulo_gal);
                    $modulo["Galerias"] = $response_gal;
                endif;

                //Archivos
                $response_arch = array();
                if (!empty($r["IDTipoArchivo"])) :
                    $sql_modulo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo = '6' Limit 1";
                    $qry_modulo = $dbo->query($sql_modulo);
                    $r_modulo = $dbo->fetchArray($qry_modulo);
                    $modulo_arch["IDModulo"] = $r_modulo["IDModulo"];
                    if (!empty($r_modulo["Titulo"])) {
                        $modulo_arch["NombreModulo"] = str_replace(" ", "", $r_modulo["Titulo"]);
                    } else {
                        $modulo_arch["NombreModulo"] = utf8_encode($dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $r_modulo["IDModulo"] . "'"));
                    }

                    if (($IDClub == "7") && ($IDModulo == "50" || $IDModulo == "54")) {
                        $modulo_arch["NombreModulo"] = SIMUtil::get_traduccion('', '', 'Cuadrosyprogramacióntorneos–Reglamento', LANG);
                    }


                    $modulo_arch["Orden"] = $r_modulo["Orden"];
                    $icono_modulo = $r_modulo["Icono"];
                    if (!empty($r_modulo["Icono"])) :
                        $foto = MODULO_ROOT . $r_modulo["Icono"];
                    else :
                        $foto = "";
                    endif;
                    $modulo_arch["Icono"] = $foto;
                    $array_archivo = explode("|", $r["IDTipoArchivo"]);
                    $response_id_arch = array();
                    foreach ($array_archivo as $id_tipoarchivo) :
                        $array_id_arch["IDTipoArchivo"] = $id_tipoarchivo;
                        array_push($response_id_arch, $array_id_arch);
                    endforeach;

                    $modulo_arch["TipoArchivo"] = $response_id_arch;
                    array_push($response_arch, $modulo_arch);
                    $modulo["Archivos"] = $response_arch;
                endif;

                //Modulos hijos
                $response_hijo = array();
                if (!empty($r["IDModuloHijo"])) :
                    $array_modhijo = explode("|", $r["IDModuloHijo"]);
                    if (count($array_modhijo) > 0) {
                        foreach ($array_modhijo as $id_modhijo) :
                            $array_id_modhijo[] = $id_modhijo;
                        endforeach;
                        $id_mod_hijo = implode(",", $array_id_modhijo);
                        $sql_modulo_hijo = "SELECT * FROM ClubModulo WHERE IDClub = '" . $IDClub . "' and IDModulo in ($id_mod_hijo)";
                        $r_modulo_hijo = $dbo->query($sql_modulo_hijo);
                        while ($row_modulo_hijo = $dbo->fetchArray($r_modulo_hijo)) {
                            $datos_hijo["IDClub"] = $IDClub;
                            $datos_hijo["IDModulo"] = $row_modulo_hijo["IDModulo"];
                            if (!empty($row_modulo_hijo["Titulo"])) {
                                $NombreModulo = str_replace(" ", "", $row_modulo_hijo["Titulo"]);
                            } elseif (!empty($row_modulo_hijo["TituloLateral"])) {
                                $NombreModulo = str_replace(" ", "", $row_modulo_hijo["TituloLateral"]);
                            } else {
                                $NombreModulo = $dbo->getFields("Modulo", "Nombre", "IDModulo = '" . $row_modulo_hijo["IDModulo"] . "'");
                            }

                            $datos_hijo["NombreModulo"] = $NombreModulo;
                            $datos_hijo["Orden"] = $row_modulo_hijo["Orden"];

                            $icono_modulo = $row_modulo_hijo["Icono"];
                            if (!empty($row_modulo_hijo["Icono"])) :
                                $foto = MODULO_ROOT . $row_modulo_hijo["Icono"];
                            else :
                                $foto = "";
                            endif;
                            $datos_hijo["Icono"] = $foto;
                            array_push($response_hijo, $datos_hijo);
                        }
                    }

                    $modulo["Modulos"] = $response_hijo;

                endif;

                array_push($response, $modulo);
            } //ednw hile
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } //End if
        else {
            $respuesta["message"] = SIMUtil::get_traduccion('', '', 'Noseencontraronregistros', LANG);
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        } //end else

        return $respuesta;
    }

    public function get_moduloweb_view($IDClub, $IDSocio, $IDModulo, $TipoApp)
    {


        $dbo = &SIMDB::get();

        $response = array();


        if (!empty($IDSocio)) {

            if ($TipoApp == "Socio") {
                $datos_club = $dbo->fetchAll("Club", " IDClub = '" . $IDClub . "' ", "array");
            } else {
                $datos_club = $dbo->fetchAll("AppEmpleado", " IDClub = '" . $IDClub . "' ", "array");
            }


            if ($IDModulo == 55) :
                $Mensaje = $datos_club["MensajeWebView1"];
                $UrlWebView = $datos_club["UrlWebView1"];
                $ControlNavegacion = "S";
                $MostrarHeader = "S";
                $LinkExterno = "N";
            elseif ($IDModulo == 56) :
                $Mensaje = $datos_club["MensajeWebView2"];
                $UrlWebView = $datos_club["UrlWebView2"];
                $ControlNavegacion = "S";
                $MostrarHeader = "S";
                $LinkExterno = "N";
            elseif ($IDModulo == 95) :
                $Mensaje = $datos_club["MensajeWebView3"];
                $UrlWebView = $datos_club["UrlWebView3"];
                $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                $MostrarHeader = $datos_modulo["MostrarHeader"];
                $LinkExterno = $datos_modulo["LinkExterno"];
            elseif ($IDModulo == 96) :
                $Mensaje = $datos_club["MensajeWebView4"];
                $UrlWebView = $datos_club["UrlWebView4"];
                $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                $MostrarHeader = $datos_modulo["MostrarHeader"];
                $LinkExterno = $datos_modulo["LinkExterno"];
            elseif ($IDModulo == 111) :
                $Mensaje = $datos_club["MensajeWebView5"];
                $UrlWebView = $datos_club["UrlWebView5"];
                $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                $MostrarHeader = $datos_modulo["MostrarHeader"];
                $LinkExterno = $datos_modulo["LinkExterno"];
            elseif ($IDModulo == 179) :
                $Mensaje = $datos_club["MensajeWebView6"];
                $UrlWebView = $datos_club["UrlWebView6"];
                $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                $MostrarHeader = $datos_modulo["MostrarHeader"];
                $LinkExterno = $datos_modulo["LinkExterno"];
            else :
                $datos_modulo = $dbo->fetchAll("Modulo", " IDModulo = '" . $IDModulo . "' ", "array");
                $Mensaje = $datos_modulo["MensajeWebView"];
                $UrlWebView = $datos_modulo["Url"];
                $ControlNavegacion = $datos_modulo["MostrarControlNavegacion"];
                $MostrarHeader = $datos_modulo["MostrarHeader"];
                $LinkExterno = $datos_modulo["LinkExterno"];

            endif;



            $webview["IDClub"] = $IDClub;
            $webview["Encabezado"] = $Mensaje;
            $webview["Url"] = $UrlWebView . "&IDSocio=" . $IDSocio . "&IDClub=" . $IDClub;

            if (($IDClub == 17 && $IDModulo == 111) || ($IDClub == 36 && $IDModulo == 179))
                $webview["Url"] = $UrlWebView;


            $webview["MostrarControlNavegacion"] = $ControlNavegacion;
            $webview["MostrarHeader"] = $MostrarHeader;
            $webview["LinkExterno"] = $LinkExterno;
            array_push($response, $webview);
            $respuesta["message"] = $message;
            $respuesta["success"] = true;
            $respuesta["response"] = $response;
        } else {
            $respuesta["message"] = "2. Atencion faltan parametros";
            $respuesta["success"] = false;
            $respuesta["response"] = null;
        }

        return $respuesta;
    } // fin function

    public function get_configuracion_noticias_publicas($IDClub)
    {
        require_once LIBDIR . "SIMWebServicePublicidad.inc.php";

        $dbo = SIMDB::get();
        $response = array();

        $datos_club = $dbo->fetchAll("Club", "IDClub = $IDClub");
        $datos_club_otros = $dbo->fetchAll("ConfiguracionClub", "IDClub = $IDClub");

        $InfoResponse[IDClub] = $IDClub;
        $InfoResponse[TipoImagenNoticias] = $datos_club_otros[TipoImagenPublicidadNoticias];

        $Publicidad[MostrarPublicidad] = $datos_club_otros[MostrarPublicidadPublica];
        $Publicidad[PublicidadTiempo] = $datos_club_otros[PublicidadTiempoPublica];

        $PublicidadesHeader = SIMWebServicePublicidad::get_banner_publicidad_header($IDClub, "", "", "Socio", "", 1);

        $Publicidad[PublicidadesHeader] = $PublicidadesHeader[response];

        $InfoResponse[Publicidad] = $Publicidad;

        array_push($response, $InfoResponse);

        $respuesta["message"] = "Encontrados";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public function get_noticias_publicas($IDClub)
    {
        $dbo = SIMDB::get();
        $response = array();

        require_once LIBDIR . "SIMWebServiceNoticias.inc.php";
        require_once LIBDIR . "SIMWebServiceNoticiaInfinitas.inc.php";

        $SQLValidaNoticias1 = "SELECT * FROM `ClubModulo` WHERE `IDClub` = $IDClub AND `IDModulo` = 3 ";
        $QRYValidaNoticias1 = $dbo->query($SQLValidaNoticias1);
        $DatoSValidaNoticias1 = $dbo->fetchArray($QRYValidaNoticias1);
        if ($DatoSValidaNoticias1[Activo] == 'S') :
            $Noticias1 = SIMWebServiceNoticias::get_noticias($IDClub, "", "", "", "", "");
            foreach ($Noticias1[response] as $ID => $Noticia) :
                if ($Noticia[ParaSeccionPublica] == 1) :
                    $InfoResponse[IDClub] = $IDClub;
                    $InfoResponse[IDSeccion] = $Noticia[IDSeccion];
                    $InfoResponse[NombreSeccion] = $dbo->getFields("Seccion", "Nombre", "IDSeccion = $Noticia[IDSeccion]");
                    $InfoResponse[IDModulo] = '3';
                    $InfoResponse[IDNoticia] = $Noticia[IDNoticia];
                    $InfoResponse[Titular] = $Noticia[Titular];
                    $InfoResponse[Introduccion] = $Noticia[Introduccion];
                    $InfoResponse[Fecha] = $Noticia[Fecha];
                    $InfoResponse[FotoPortada] = $Noticia[FotoPortada];
                    $InfoResponse[Foto] = $Noticia[Foto];
                    $InfoResponse[Cuerpo] = $Noticia[Cuerpo];
                    $InfoResponse[Foto2] = $Noticia[Foto2];

                    array_push($response, $InfoResponse);
                endif;
            endforeach;
        endif;

        $SQLValidaNoticias2 = "SELECT * FROM `ClubModulo` WHERE `IDClub` = $IDClub AND `IDModulo` = 66 ";
        $QRYValidaNoticias2 = $dbo->query($SQLValidaNoticias2);
        $DatoSValidaNoticias2 = $dbo->fetchArray($QRYValidaNoticias2);

        if ($DatoSValidaNoticias2[Activo] == 'S') :
            $Noticias2 = SIMWebServiceNoticias::get_noticias($IDClub, "", "", "", "2", "");
            foreach ($Noticias2[response] as $ID => $Noticia) :
                if ($Noticia[ParaSeccionPublica] == 1) :
                    $InfoResponse[IDClub] = $IDClub;
                    $InfoResponse[IDSeccion] = $Noticia[IDSeccion];
                    $InfoResponse[NombreSeccion] = $dbo->getFields("Seccion2", "Nombre", "IDSeccion = $Noticia[IDSeccion]");
                    $InfoResponse[IDModulo] = '66';
                    $InfoResponse[IDNoticia] = $Noticia[IDNoticia];
                    $InfoResponse[Titular] = $Noticia[Titular];
                    $InfoResponse[Introduccion] = $Noticia[Introduccion];
                    $InfoResponse[Fecha] = $Noticia[Fecha];
                    $InfoResponse[FotoPortada] = $Noticia[FotoPortada];
                    $InfoResponse[Foto] = $Noticia[Foto];
                    $InfoResponse[Cuerpo] = $Noticia[Cuerpo];
                    $InfoResponse[Foto2] = $Noticia[Foto2];

                    array_push($response, $InfoResponse);
                endif;
            endforeach;
        endif;

        $SQLValidaNoticias3 = "SELECT * FROM `ClubModulo` WHERE `IDClub` = $IDClub AND `IDModulo` = 81 ";
        $QRYValidaNoticias3 = $dbo->query($SQLValidaNoticias1);
        $DatoSValidaNoticias3 = $dbo->fetchArray($QRYValidaNoticias3);
        if ($DatoSValidaNoticias3[Activo] == 'S') :
            $Noticias3 = SIMWebServiceNoticias::get_noticias($IDClub, "", "", "", "3", "");
            foreach ($Noticias3[response] as $ID => $Noticia) :
                if ($Noticia[ParaSeccionPublica] == 1) :
                    $InfoResponse[IDClub] = $IDClub;
                    $InfoResponse[IDSeccion] = $Noticia[IDSeccion];
                    $InfoResponse[NombreSeccion] = $dbo->getFields("Seccion3", "Nombre", "IDSeccion = $Noticia[IDSeccion]");
                    $InfoResponse[IDModulo] = '81';
                    $InfoResponse[IDNoticia] = $Noticia[IDNoticia];
                    $InfoResponse[Titular] = $Noticia[Titular];
                    $InfoResponse[Introduccion] = $Noticia[Introduccion];
                    $InfoResponse[Fecha] = $Noticia[Fecha];
                    $InfoResponse[FotoPortada] = $Noticia[FotoPortada];
                    $InfoResponse[Foto] = $Noticia[Foto];
                    $InfoResponse[Cuerpo] = $Noticia[Cuerpo];
                    $InfoResponse[Foto2] = $Noticia[Foto2];

                    array_push($response, $InfoResponse);
                endif;
            endforeach;
        endif;
        // NOTICIAS INFINITAS

        $SQLNoticiasInfinitas = "SELECT * FROM NoticiaInfinita WHERE IDClub = $IDClub AND ParaSeccionPublica = 1";
        $QRYNoticiasInfinitas = $dbo->query($SQLNoticiasInfinitas);

        while ($Noticia = $dbo->fetchArray($QRYNoticiasInfinitas)) :

            $SQLValidaNoticiasInfi = "SELECT * FROM `ClubModulo` WHERE `IDClub` = $IDClub AND `IDModulo` = $Noticia[IDModulo] ";
            $QRYValidaNoticiasInfi = $dbo->query($SQLValidaNoticiasInfi);
            $DatoSValidaNoticiasInfi = $dbo->fetchArray($QRYValidaNoticiasInfi);
            if ($DatoSValidaNoticiasInfi[Activo] == 'S') :
                $InfoResponse[IDClub] = $IDClub;
                $InfoResponse[IDSeccion] = $Noticia[IDSeccionNoticiaInfinita];
                $InfoResponse[NombreSeccion] = $dbo->getFields("SeccionNoticiaInfinita", "Nombre", "IDSeccionNoticiaInfinita = $Noticia[IDSeccionNoticiaInfinita]");
                $InfoResponse[IDModulo] = $Noticia[IDModulo];
                $InfoResponse[IDNoticia] = $Noticia[IDNoticiaInfinita];
                $InfoResponse[Titular] = $Noticia[Titular];
                $InfoResponse[Introduccion] = $Noticia[Introduccion];
                $InfoResponse[Fecha] = $Noticia[FechaInicio];

                $cuerpo_noticia = str_replace("/file/noticia/editor/", IMGNOTICIA_ROOT . 'editor/', $r["Cuerpo"]);

                //Documentos adjuntos
                if (!empty($r["Adjunto1File"])) :
                    $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto1File"] . "' >" . $r["Adjunto1File"] . '</a>';
                endif;
                if (!empty($r["Adjunto2File"])) :
                    $cuerpo_noticia .= "<br><a href='" . IMGNOTICIA_ROOT . $r["Adjunto2File"] . "' >" . $r["Adjunto2File"] . '</a>';
                endif;

                $InfoResponse[Cuerpo] = $cuerpo_noticia;

                if (!empty($Noticia["FotoPortada"])) :
                    if (strstr(strtolower($Noticia["FotoPortada"]), "http://")) {
                        $FotoPortada = $Noticia["FotoPortada"];
                    }
                    if (strstr(strtolower($Noticia["FotoPortada"]), "https://")) {
                        $FotoPortada = $Noticia["FotoPortada"];
                    } else {
                        $FotoPortada = IMGNOTICIA_ROOT . $Noticia["FotoPortada"];
                    }

                //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                else :
                    $FotoPortada = "";
                endif;

                $InfoResponse[FotoPortada] = $FotoPortada;

                if (!empty($Noticia["NoticiaFile"])) :
                    if (strstr(strtolower($Noticia["NoticiaFile"]), "http://")) {
                        $foto1 = $Noticia["NoticiaFile"];
                    }
                    if (strstr(strtolower($Noticia["NoticiaFile"]), "https://")) {
                        $foto1 = $Noticia["NoticiaFile"];
                    } else {
                        $foto1 = IMGNOTICIA_ROOT . $Noticia["NoticiaFile"];
                    }

                //$foto1 = IMGNOTICIA_ROOT.$r["NoticiaFile"];
                else :
                    $foto1 = "";
                endif;

                $InfoResponse[Foto] = $foto1;

                if (!empty($r["FotoDestacada"])) :
                    if (strstr(strtolower($r["FotoDestacada"]), "http://")) {
                        $foto2 = $r["FotoDestacada"];
                    } else {
                        $foto2 = IMGNOTICIA_ROOT . $r["FotoDestacada"];
                    }

                //$foto2 = IMGNOTICIA_ROOT.$r["FotoDestacada"];
                else :
                    $foto2 = "";
                endif;

                $InfoResponse[Foto2] = $foto2;

                array_push($response, $InfoResponse);
            endif;
        endwhile;

        $respuesta["message"] = $message;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }
}
